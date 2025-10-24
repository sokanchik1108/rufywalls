@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<div class="container py-5" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="fw-semibold mb-1" style="font-size:1.3rem;">Поиск по истории продаж</h1>
        <a href="javascript:history.back()" class="btn btn-outline-dark btn-sm">
            ← Назад
        </a>

    </div>

    <div class="card shadow-sm border-0 p-4 mb-4">
        <label for="returnSkuInput" class="form-label fw-semibold text-secondary mb-2">
            Выберите артикул, чтобы увидеть историю продаж:
        </label>
        <div class="position-relative">
            <input type="text" id="returnSkuInput" class="form-control form-control-lg pe-5"
                placeholder="Введите артикул товара..." autocomplete="off" style="border-radius:10px;">
            <button type="button" id="clearReturnInput"
                class="clear-btn position-absolute top-50 end-0 translate-middle-y"
                style="display:none;">×</button>
        </div>

        <p class="text-muted small mt-3 mb-0">
            Показаны продажи только за <strong>последние 30 дней</strong>.
        </p>
    </div>

    <div class="card shadow-sm border-0 p-0" id="returnHistoryTableContainer" style="display:none;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Дата</th>
                        <th>Партия</th>
                        <th class="text-end">Кол-во</th>
                        <th>Склад</th>
                    </tr>
                </thead>
                <tbody id="returnHistoryTableBody">
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">Загрузка...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    body {
        background: #f8f9fa;
        color: #222;
        font-size: 0.95rem;
    }

    h1 {
        color: #111;
        font-weight: 600;
        letter-spacing: -0.3px;
    }

    .card {
        border-radius: 12px;
        background: #fff;
    }

    .form-control {
        font-size: 1rem;
        padding: 0.7rem 1rem;
        border: 1px solid #ddd;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        border-color: #000;
        box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.1);
    }

    .clear-btn {
        border: none;
        background: transparent;
        font-size: 1.6rem;
        line-height: 1;
        color: #aaa;
        cursor: pointer;
        padding: 0 1rem;
        transition: color 0.2s ease;
    }

    .clear-btn:hover {
        color: #000;
    }

    table th {
        font-weight: 600;
        color: #444;
        font-size: 0.9rem;
    }

    table td {
        font-size: 0.9rem;
        color: #333;
    }

    @media (max-width: 576px) {
        h1 {
            font-size: 1.1rem;
        }

        .form-control {
            font-size: 1rem;
        }

        table {
            font-size: 0.85rem;
        }

        .clear-btn {
            font-size: 1.8rem;
            padding: 0 0.8rem;
        }
    }
</style>

<script>
    $(function() {
        const $input = $('#returnSkuInput');
        const $clearBtn = $('#clearReturnInput');
        const $tableContainer = $('#returnHistoryTableContainer');
        const $tbody = $('#returnHistoryTableBody');

        $input.autocomplete({
            source: '{{ route("admin.variants.autocomplete") }}',
            minLength: 1,
            select: function(event, ui) {
                const sku = ui.item.value;
                $tbody.html('<tr><td colspan="4" class="text-center py-4 text-muted">Загрузка...</td></tr>');
                $tableContainer.show();

                $.ajax({
                    url: '/admin/sales/history/' + encodeURIComponent(sku),
                    success: function(data) {
                        $tbody.empty();

                        if (data.length === 0) {
                            $tbody.append('<tr><td colspan="4" class="text-center text-muted py-3">Нет продаж за последние 30 дней</td></tr>');
                        } else {
                            data.forEach(sale => {
                                $tbody.append(`
                                    <tr>
                                        <td>${sale.sale_date}</td>
                                        <td>${sale.batch_code ?? '—'}</td>
                                        <td class="text-end">${sale.quantity}</td>
                                        <td>${sale.warehouse_name}</td>
                                    </tr>
                                `);
                            });
                        }
                    },
                    error: function() {
                        $tbody.html('<tr><td colspan="4" class="text-center text-danger py-3">Ошибка загрузки данных</td></tr>');
                    }
                });
            }
        });

        $input.on('input', function() {
            const hasText = $(this).val().trim() !== '';
            $clearBtn.toggle(hasText);
            if (!hasText) {
                $tableContainer.hide();
                $tbody.empty();
            }
        });

        $clearBtn.on('click', function() {
            $input.val('').focus();
            $clearBtn.hide();
            $tableContainer.hide();
            $tbody.empty();
        });
    });
</script>
@endsection