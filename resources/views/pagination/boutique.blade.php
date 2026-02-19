@if ($paginator->hasPages())
    <nav aria-label="Paginación" class="boutique-pagination-wrapper">
        <div class="pagination-info">
            Mostrando <strong>{{ $paginator->firstItem() }}</strong> - <strong>{{ $paginator->lastItem() }}</strong>
            de <strong>{{ $paginator->total() }}</strong> registros
        </div>
        <ul class="boutique-pagination">
            {{-- Botón Anterior --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">
                        <i class="fas fa-chevron-left"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- Números de página --}}
            @foreach ($elements as $element)
                {{-- Separador "..." --}}
                @if (is_string($element))
                    <li class="page-item disabled">
                        <span class="page-link dots">{{ $element }}</span>
                    </li>
                @endif

                {{-- Links de página --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Botón Siguiente --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">
                        <i class="fas fa-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>

    <style>
        .boutique-pagination-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 8px 8px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .pagination-info {
            font-size: 0.85rem;
            color: #6c757d;
            font-weight: 500;
        }

        .pagination-info strong {
            color: #2C2C2C;
        }

        .boutique-pagination {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
            gap: 4px;
        }

        .boutique-pagination .page-item .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 38px;
            height: 38px;
            padding: 0 10px;
            border: 2px solid #e8e8e8;
            border-radius: 10px;
            background: #ffffff;
            color: #555;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .boutique-pagination .page-item .page-link:hover {
            background: #f8f4e8;
            border-color: #D4AF37;
            color: #D4AF37;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.2);
        }

        .boutique-pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #D4AF37, #c4a030);
            border-color: #D4AF37;
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.35);
            transform: translateY(-1px);
        }

        .boutique-pagination .page-item.disabled .page-link {
            background: #f5f5f5;
            border-color: #eee;
            color: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .boutique-pagination .page-item .page-link.dots {
            border: none;
            background: transparent;
            color: #999;
            cursor: default;
            min-width: 30px;
        }

        .boutique-pagination .page-item .page-link.dots:hover {
            transform: none;
            box-shadow: none;
            background: transparent;
        }

        @media (max-width: 576px) {
            .boutique-pagination-wrapper {
                justify-content: center;
                flex-direction: column;
                align-items: center;
            }

            .boutique-pagination .page-item .page-link {
                min-width: 34px;
                height: 34px;
                font-size: 0.8rem;
            }
        }
    </style>
@endif
