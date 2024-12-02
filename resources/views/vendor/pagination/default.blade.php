@if ($paginator->hasPages())
    <ul class="dTables_paginate">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="paginate_button disabled"><span>Prec.</span></li>
        @else
            <li class="paginate_button"><a href="{{ $paginator->previousPageUrl() }}" rel="prev">Prec.</a></li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="paginate_button disabled"><span>{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="paginate_button current"><span>{{ $page }}</span></li>
                    @else
                        <li class="paginate_button"><a href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="paginate_button"><a href="{{ $paginator->nextPageUrl() }}" rel="next">Succ.</a></li>
        @else
            <li class="paginate_button disabled"><span>Succ.</span></li>
        @endif
    </ul>
@endif
