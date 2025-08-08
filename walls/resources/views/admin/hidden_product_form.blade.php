@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 700px">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h4 class="mb-4 fw-semibold">Добавить скрытый товар</h4>

            @if (session('success'))
                <div class="alert alert-success small">{{ session('success') }}</div>
            @endif

            @error('error')
                <div class="alert alert-danger small">{{ $message }}</div>
            @enderror

            <form method="POST" action="{{ route('admin.hidden.store') }}">
                @csrf

                <div id="variants-wrapper">
                    <div class="variant-item border rounded p-3 mb-3 bg-light-subtle position-relative">
                        <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="removeVariant(this)" title="Удалить оттенок"></button>
                        <div class="mb-2">
                            <label class="form-label small">Артикул (SKU)</label>
                            <input type="text" name="variants[0][sku]" class="form-control form-control-sm" required>
                        </div>

                        <div>
                            <label class="form-label small">Оттенок</label>
                            <input type="text" name="variants[0][color]" class="form-control form-control-sm" required>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addVariant()">+ Добавить оттенок</button>
                    <small class="text-muted">Можно добавить несколько вариантов</small>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-dark px-4">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let variantIndex = 1;

    function addVariant() {
        const wrapper = document.getElementById('variants-wrapper');
        const html = `
            <div class="variant-item border rounded p-3 mb-3 bg-light-subtle position-relative">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="removeVariant(this)" title="Удалить оттенок"></button>
                <div class="mb-2">
                    <label class="form-label small">Артикул (SKU)</label>
                    <input type="text" name="variants[${variantIndex}][sku]" class="form-control form-control-sm" required>
                </div>
                <div>
                    <label class="form-label small">Оттенок</label>
                    <input type="text" name="variants[${variantIndex}][color]" class="form-control form-control-sm" required>
                </div>
            </div>
        `;
        wrapper.insertAdjacentHTML('beforeend', html);
        variantIndex++;
    }

    function removeVariant(button) {
        const variantItem = button.closest('.variant-item');
        variantItem.remove();
    }
</script>
@endsection
