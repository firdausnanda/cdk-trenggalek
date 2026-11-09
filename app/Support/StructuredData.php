<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Post;
use Illuminate\Support\Str;

/**
 * schema.org data (JSON-LD) for the public pages, read by search engines for rich results.
 * Profile and office details come from config/cdk.php.
 */
final class StructuredData
{
    public static function organizationId(): string
    {
        return url('/') . '#organisasi';
    }

    public static function organization(): array
    {
        $kantorUtama = config('cdk.kantor.0');

        return [
            '@type' => 'GovernmentOrganization',
            '@id' => self::organizationId(),
            'name' => config('cdk.nama'),
            'alternateName' => config('cdk.nama_singkat'),
            'url' => url('/'),
            'logo' => asset('images/logo/cdk.png'),
            'email' => config('cdk.email'),
            'address' => self::address($kantorUtama),
            'parentOrganization' => [
                '@type' => 'GovernmentOrganization',
                'name' => config('cdk.instansi_induk'),
            ],
            'areaServed' => array_map(
                fn (string $wilayah) => ['@type' => 'AdministrativeArea', 'name' => $wilayah],
                config('cdk.wilayah'),
            ),
            'sameAs' => config('cdk.sosial'),
        ];
    }

    public static function website(): array
    {
        return [
            '@type' => 'WebSite',
            '@id' => url('/') . '#situs',
            'name' => config('cdk.nama'),
            'url' => url('/'),
            'inLanguage' => 'id-ID',
            'publisher' => ['@id' => self::organizationId()],
        ];
    }

    /**
     * One GovernmentOffice per office in config/cdk.php, with the service hours.
     */
    public static function offices(): array
    {
        $jam = config('cdk.jam_layanan');

        return array_map(fn (array $kantor) => [
            '@type' => 'GovernmentOffice',
            'name' => config('cdk.nama') . ' — ' . $kantor['nama'],
            'address' => self::address($kantor),
            'hasMap' => $kantor['maps'],
            'email' => config('cdk.email'),
            'parentOrganization' => ['@id' => self::organizationId()],
            'openingHoursSpecification' => [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'opens' => $jam['buka'],
                'closes' => $jam['tutup'],
            ],
        ], config('cdk.kantor'));
    }

    /**
     * @param  array<int, array{0: string, 1: string}>  $items  [name, url] pairs, from the homepage down.
     */
    public static function breadcrumb(array $items): array
    {
        return [
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_map(fn (array $item, int $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $item[0],
                'item' => $item[1],
            ], $items, array_keys($items)),
        ];
    }

    public static function newsArticle(Post $post): array
    {
        $url = route('berita.show', $post->slug);
        $deskripsi = $post->seo('description') ?: deskripsi_meta($post->excerpt ?: $post->content);

        return array_filter([
            '@type' => 'NewsArticle',
            '@id' => $url . '#artikel',
            'mainEntityOfPage' => $url,
            'headline' => Str::limit($post->title, 110),
            'description' => $deskripsi ?: null,
            'image' => [$post->featured_image ? url($post->featured_image) : asset('images/og-default.jpg')],
            'datePublished' => ($post->published_at ?? $post->created_at)?->toIso8601String(),
            'dateModified' => $post->updated_at?->toIso8601String(),
            'inLanguage' => 'id-ID',
            'author' => [
                '@type' => 'GovernmentOrganization',
                'name' => config('cdk.nama'),
                'url' => url('/'),
            ],
            'publisher' => ['@id' => self::organizationId()],
            'articleSection' => $post->relationLoaded('categories') ? $post->categories->pluck('name')->all() : null,
            'keywords' => $post->relationLoaded('tags') ? $post->tags->pluck('name')->implode(', ') : null,
        ], fn ($value) => $value !== null && $value !== [] && $value !== '');
    }

    /**
     * Encode nodes as one JSON-LD graph, safe to print inside a <script> element.
     */
    public static function toJson(array ...$nodes): string
    {
        return json_encode(
            ['@context' => 'https://schema.org', '@graph' => array_values($nodes)],
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_THROW_ON_ERROR,
        );
    }

    private static function address(array $kantor): array
    {
        return [
            '@type' => 'PostalAddress',
            'streetAddress' => $kantor['alamat'],
            'addressLocality' => $kantor['kota'],
            'addressRegion' => 'Jawa Timur',
            'postalCode' => $kantor['kode_pos'],
            'addressCountry' => 'ID',
        ];
    }
}
