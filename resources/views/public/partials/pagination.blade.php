{{-- Pagination for the public pages: $paginator->links('public.partials.pagination') --}}
@if ($paginator->hasPages())
    @php
        $base = 'grid size-11 place-items-center rounded-xl text-sm font-semibold transition';
    @endphp
    <nav aria-label="{{ __('public.pagination.label') }}" class="flex flex-col items-center justify-between gap-4 sm:flex-row">
        <p class="text-sm text-ink/70">{{ __('public.pagination.page_of', ['current' => $paginator->currentPage(), 'last' => $paginator->lastPage()]) }}</p>

        <ul class="flex flex-wrap items-center gap-1.5">
            <li>
                @if ($paginator->onFirstPage())
                    <span class="{{ $base }} text-ink/30" aria-disabled="true">
                        <i aria-hidden="true" class="bi bi-chevron-left"></i><span class="sr-only">{{ __('public.pagination.previous') }}</span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $base }} text-ink hover:bg-forest-50">
                        <i aria-hidden="true" class="bi bi-chevron-left"></i><span class="sr-only">{{ __('public.pagination.previous') }}</span>
                    </a>
                @endif
            </li>

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span class="{{ $base }} text-ink/50" aria-hidden="true">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="{{ $base }} bg-forest-900 text-white">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="{{ $base }} text-ink ring-1 ring-forest-900/10 hover:bg-forest-50">
                                    <span class="sr-only">{{ __('public.pagination.page') }}</span> {{ $page }}
                                </a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach

            <li>
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $base }} text-ink hover:bg-forest-50">
                        <i aria-hidden="true" class="bi bi-chevron-right"></i><span class="sr-only">{{ __('public.pagination.next') }}</span>
                    </a>
                @else
                    <span class="{{ $base }} text-ink/30" aria-disabled="true">
                        <i aria-hidden="true" class="bi bi-chevron-right"></i><span class="sr-only">{{ __('public.pagination.next') }}</span>
                    </span>
                @endif
            </li>
        </ul>
    </nav>
@endif
