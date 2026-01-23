@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="pagination-text">
        <div class="pagination-text__links">
            @if ($paginator->onFirstPage())
                <span class="pagination-text__disabled" aria-disabled="true">Previous</span>
            @else
                <a class="pagination-text__link" href="{{ $paginator->previousPageUrl() }}" rel="prev">Previous</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="pagination-text__ellipsis">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="pagination-text__current" aria-current="page">{{ $page }}</span>
                        @else
                            <a class="pagination-text__link" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a class="pagination-text__link" href="{{ $paginator->nextPageUrl() }}" rel="next">Next</a>
            @else
                <span class="pagination-text__disabled" aria-disabled="true">Next</span>
            @endif
        </div>
    </nav>
@endif
