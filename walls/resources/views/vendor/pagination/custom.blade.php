@if ($paginator->hasPages())
<nav class="custom-pagination pagination">
    {{-- Назад --}}
    @if ($paginator->onFirstPage())
    <span class="page-btn disabled">&laquo;</span>
    @else
    <a href="{{ $paginator->previousPageUrl() }}" class="page-btn">&laquo;</a>
    @endif

    {{-- Номера страниц --}}
    @foreach ($elements as $element)
    @if (is_string($element))
    <span class="page-ellipsis">{{ $element }}</span>
    @endif

    @if (is_array($element))
    @foreach ($element as $page => $url)
    @if ($page == $paginator->currentPage())
    <span class="page-btn active">{{ $page }}</span>
    @else
    <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
    @endif
    @endforeach
    @endif
    @endforeach

    {{-- Вперёд --}}
    @if ($paginator->hasMorePages())
    <a href="{{ $paginator->nextPageUrl() }}" class="page-btn">&raquo;</a>
    @else
    <span class="page-btn disabled">&raquo;</span>
    @endif
</nav>
@endif


<style>
    .pagination-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 30px;
    }

    .custom-pagination {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .page-btn {
        padding: 8px 14px;
        border: 1px solid #ddd;
        background: #fff;
        color: #333;
        border-radius: 6px;
        font-size: 14px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .page-btn:hover {
        background: #000;
        color: #fff;
    }

    .page-btn.active {
        background: #000;
        color: #fff;
        font-weight: bold;
    }

    .page-btn.disabled {
        opacity: 0.5;
        pointer-events: none;
    }

    .page-ellipsis {
        padding: 8px 12px;
        color: #999;
        font-size: 14px;
    }
</style>