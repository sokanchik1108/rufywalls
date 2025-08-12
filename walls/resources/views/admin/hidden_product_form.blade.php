@extends('layouts.app')

@section('content')
<div class="container py-3" style="max-width: 600px">
    <div class="card shadow-sm border-0">
        <div class="card-body p-3">
            <h5 class="mb-3 fw-semibold text-center">Добавить скрытый товар</h5>

            @if (session('success'))
            <div class="alert alert-success small py-2 px-3">{{ session('success') }}</div>
            @endif

            @error('error')
            <div class="alert alert-danger small py-2 px-3">{{ $message }}</div>
            @enderror


            @if ($errors->any())
            <div class="alert alert-danger small">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif


            <form method="POST" action="{{ route('admin.hidden.store') }}">
                @csrf

                <div id="variants-wrapper">
                    <div class="variant-item border rounded-3 p-3 mb-3 bg-light-subtle position-relative shadow-sm">
                        <button type="button" class="btn-close position-absolute top-0 end-0 m-2 p-2 rounded-circle"
                            onclick="removeVariant(this)" title="Удалить оттенок" style="background-color: rgba(0,0,0,0.05)"></button>
                        <div class="mb-2">
                            <label class="form-label small mb-1">Артикул (SKU)</label>
                            <input type="text" name="variants[0][sku]"
                                class="form-control form-control-sm rounded-3" required>
                        </div>
                        <div>
                            <label class="form-label small mb-1">Оттенок</label>
                            <input type="text" name="variants[0][color]"
                                class="form-control form-control-sm rounded-3" required>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center mb-3 gap-2">
                    <button type="button" class="btn btn-outline-primary w-100 w-sm-auto btn-sm py-2" onclick="addVariant()">+ Добавить оттенок</button>
                    <small class="text-muted text-center">Можно добавить несколько вариантов</small>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-dark w-100 py-2">Сохранить</button>
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
            <div class="variant-item border rounded-3 p-3 mb-3 bg-light-subtle position-relative shadow-sm">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2 p-2 rounded-circle" 
                        onclick="removeVariant(this)" title="Удалить оттенок" style="background-color: rgba(0,0,0,0.05)"></button>
                <div class="mb-2">
                    <label class="form-label small mb-1">Артикул (SKU)</label>
                    <input type="text" name="variants[${variantIndex}][sku]" 
                           class="form-control form-control-sm rounded-3" required>
                </div>
                <div>
                    <label class="form-label small mb-1">Оттенок</label>
                    <input type="text" name="variants[${variantIndex}][color]" 
                           class="form-control form-control-sm rounded-3" required>
                </div>
            </div>
        `;
        wrapper.insertAdjacentHTML('beforeend', html);
        variantIndex++;
    }

    function removeVariant(button) {
        button.closest('.variant-item').remove();
    }
</script>

<style>
    input,
    select,
    textarea {
        font-size: 16px !important;
    }
</style>
@endsection