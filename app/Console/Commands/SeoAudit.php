<?php

namespace App\Console\Commands;

use DOMDocument;
use DOMXPath;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SeoAudit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seo:audit
                            {url? : Site to audit, e.g. https://example.go.id (default: APP_URL)}
                            {--limit=0 : Audit at most this many sitemap URLs (0 = all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl robots.txt and the sitemap and check each page\'s search metadata';

    /** Fields Google needs before it shows an article as a rich result. */
    private const ARTICLE_FIELDS = ['headline', 'image', 'datePublished', 'author'];

    /** @var array<int, array{0: string, 1: string, 2: string}> [level, url, message] */
    private array $issues = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $site = rtrim((string) ($this->argument('url') ?: config('app.url')), '/');

        $sitemapUrl = $this->checkRobots($site);
        $urls = $this->sitemapUrls($sitemapUrl);
        if ($urls === null) {
            return $this->report(0);
        }

        $limit = (int) $this->option('limit');
        if ($limit > 0) {
            $urls = array_slice($urls, 0, $limit);
        }

        $titles = [];
        $descriptions = [];
        $bar = $this->output->createProgressBar(count($urls));
        foreach ($urls as $url) {
            $page = $this->checkPage($url);
            if ($page !== null) {
                $titles[$page['title']][] = $url;
                $descriptions[$page['description']][] = $url;
            }
            $bar->advance();
        }
        $bar->finish();
        $this->newLine(2);

        foreach (['title' => $titles, 'description' => $descriptions] as $field => $groups) {
            foreach ($groups as $text => $pages) {
                if ($text !== '' && count($pages) > 1) {
                    $this->issue('warning', $pages[0], "Same {$field} on ".count($pages).' pages: '.implode(', ', array_slice($pages, 1, 3)));
                }
            }
        }

        return $this->report(count($urls));
    }

    /**
     * Check robots.txt and return the sitemap URL it lists (or the default location).
     */
    private function checkRobots(string $site): string
    {
        $url = $site.'/robots.txt';
        $response = $this->fetch($url);
        if ($response === null || ! $response->successful()) {
            $this->issue('error', $url, 'robots.txt is missing ('.($response?->status() ?? 'no response').')');

            return $site.'/sitemap.xml';
        }

        $body = $response->body();
        if (preg_match('/^\s*Disallow:\s*\/\s*$/mi', $body)) {
            $this->issue('error', $url, 'robots.txt blocks the whole site (Disallow: /)');
        }
        if (! preg_match('/^\s*Sitemap:\s*(\S+)/mi', $body, $match)) {
            $this->issue('warning', $url, 'robots.txt does not list the sitemap');

            return $site.'/sitemap.xml';
        }

        return $match[1];
    }

    /**
     * @return array<int, string>|null
     */
    private function sitemapUrls(string $url): ?array
    {
        $response = $this->fetch($url);
        if ($response === null || ! $response->successful()) {
            $this->issue('error', $url, 'Sitemap is missing ('.($response?->status() ?? 'no response').')');

            return null;
        }

        $previous = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($response->body());
        libxml_use_internal_errors($previous);
        if ($xml === false) {
            $this->issue('error', $url, 'Sitemap is not valid XML');

            return null;
        }

        $xml->registerXPathNamespace('s', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $urls = array_map(fn ($loc) => trim((string) $loc), $xml->xpath('//s:url/s:loc') ?: []);
        if ($urls === []) {
            $this->issue('error', $url, 'Sitemap lists no URLs');
        }

        return $urls;
    }

    /**
     * @return array{title: string, description: string}|null
     */
    private function checkPage(string $url): ?array
    {
        $response = $this->fetch($url);
        if ($response === null || ! $response->successful()) {
            $this->issue('error', $url, 'Page in the sitemap does not load ('.($response?->status() ?? 'no response').')');

            return null;
        }

        $dom = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">'.$response->body());
        libxml_use_internal_errors($previous);
        $xpath = new DOMXPath($dom);

        $first = fn (string $query, string $attribute = '') => ($node = $xpath->query($query)->item(0))
            ? trim($attribute ? $node->getAttribute($attribute) : $node->textContent)
            : null;

        $title = $first('//title') ?? '';
        $description = $first('//meta[@name="description"]', 'content') ?? '';
        $canonical = $first('//link[@rel="canonical"]', 'href');
        $robots = $first('//meta[@name="robots"]', 'content') ?? '';

        $this->checkLength($url, 'Title', $title, 15, 65);
        $this->checkLength($url, 'Meta description', $description, 50, 160);

        if ($canonical === null) {
            $this->issue('error', $url, 'No canonical link');
        } elseif (rtrim($canonical, '/') !== rtrim($url, '/')) {
            $this->issue('error', $url, "Canonical points elsewhere: {$canonical}");
        }

        if (Str::contains(strtolower($robots), 'noindex')) {
            $this->issue('error', $url, 'Page is in the sitemap but marked noindex');
        }

        $h1 = $xpath->query('//h1')->length;
        if ($h1 !== 1) {
            $this->issue('warning', $url, "Page has {$h1} h1 headings (expected 1)");
        }

        $withoutAlt = $xpath->query('//img[not(@alt)]')->length;
        if ($withoutAlt > 0) {
            $this->issue('warning', $url, "{$withoutAlt} image(s) without an alt attribute");
        }

        foreach (['og:title', 'og:description', 'og:image'] as $property) {
            $value = $first("//meta[@property=\"{$property}\"]", 'content');
            if (! $value) {
                $this->issue('warning', $url, "No {$property}");
            } elseif ($property === 'og:image' && ! Str::startsWith($value, ['http://', 'https://'])) {
                $this->issue('error', $url, "og:image is not an absolute URL: {$value}");
            }
        }

        $this->checkStructuredData($url, $xpath);

        return ['title' => $title, 'description' => $description];
    }

    private function checkStructuredData(string $url, DOMXPath $xpath): void
    {
        $nodes = [];
        foreach ($xpath->query('//script[@type="application/ld+json"]') as $script) {
            $data = json_decode($script->textContent, true);
            if (! is_array($data)) {
                $this->issue('error', $url, 'Structured data (JSON-LD) is not valid JSON');

                continue;
            }
            array_push($nodes, ...($data['@graph'] ?? [$data]));
        }

        if ($nodes === []) {
            $this->issue('warning', $url, 'No structured data (JSON-LD)');
        }

        foreach ($nodes as $node) {
            if (in_array($node['@type'] ?? null, ['NewsArticle', 'Article', 'BlogPosting'], true)) {
                $missing = array_filter(self::ARTICLE_FIELDS, fn ($field) => empty($node[$field]));
                if ($missing) {
                    $this->issue('error', $url, $node['@type'].' is missing: '.implode(', ', $missing));
                }
            }
        }
    }

    private function checkLength(string $url, string $label, string $text, int $min, int $max): void
    {
        $length = mb_strlen($text);

        if ($length === 0) {
            $this->issue('error', $url, "{$label} is empty");
        } elseif ($length < $min) {
            $this->issue('warning', $url, "{$label} is short ({$length} characters, aim for {$min}–{$max})");
        } elseif ($length > $max) {
            $this->issue('warning', $url, "{$label} may be cut off in results ({$length} characters, aim for at most {$max})");
        }
    }

    private function fetch(string $url): ?\Illuminate\Http\Client\Response
    {
        try {
            return Http::timeout(20)
                ->withUserAgent('CDK-SEO-Audit/1.0')
                ->get($url);
        } catch (ConnectionException) {
            return null;
        }
    }

    private function issue(string $level, string $url, string $message): void
    {
        $this->issues[] = [$level, $url, $message];
    }

    private function report(int $pages): int
    {
        $errors = count(array_filter($this->issues, fn ($issue) => $issue[0] === 'error'));
        $warnings = count($this->issues) - $errors;

        if ($this->issues !== []) {
            usort($this->issues, fn ($a, $b) => [$a[0], $a[1]] <=> [$b[0], $b[1]]);
            $this->table(['Level', 'URL', 'Issue'], array_map(
                fn ($issue) => [$issue[0], Str::limit($issue[1], 70), $issue[2]],
                $this->issues,
            ));
        }

        $summary = "Audited {$pages} page(s): {$errors} error(s), {$warnings} warning(s).";
        $errors > 0 ? $this->error($summary) : $this->info($summary);

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }
}
