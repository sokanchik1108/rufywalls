@extends('layouts.main')

@section('title', 'Калькулятор обоев')

@section('content')
<div class="container pt-5 mt-5 d-flex justify-content-center align-items-start min-vh-100">
    <div class="card shadow-lg border-0 rounded-4 w-100" style="max-width: 900px; background-color: #fff;">
        <div class="card-body p-5">

            {{-- Результат всегда сверху --}}
            <div id="result" class="mb-5">
                <h5 class="fw-bold text-dark">Результат:</h5>
                <p class="fs-5 text-dark mb-1">
                    Необходимое количество рулонов: <span id="rollsNeeded" class="fw-semibold">–</span>
                </p>

                <div class="mt-2 p-3 border border-warning rounded bg-light text-dark">
                    <strong>⚠️ Примечание:</strong> расчёт без учёта подгонки рисунка.<br>
                    При наличии раппорта может потребоваться больше рулонов.
                </div>

                <div id="detailedExplanation" class="mt-4" style="display: none;">
                    <h6 class="fw-bold text-dark">Как рассчитано:</h6>
                    <ul class="text-secondary small list-unstyled" id="explanationList"></ul>
                </div>
            </div>

            {{-- Заголовок --}}
            <div class="text-center mb-4">
                <h1 class="h3 fw-bold text-dark">Калькулятор обоев</h1>
                <p class="text-muted small">Введите размеры в метрах, например: <strong>2.75</strong></p>
            </div>

            {{-- Форма расчёта --}}
            <form id="wallpaperCalc">
                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label text-dark">Длина комнаты</label>
                        <input type="number" step="0.01" min="0" class="form-control border-secondary-subtle" id="length" required>
                    </div>
                    <div class="col">
                        <label class="form-label text-dark">Ширина комнаты</label>
                        <input type="number" step="0.01" min="0" class="form-control border-secondary-subtle" id="width" required>
                    </div>
                    <div class="col">
                        <label class="form-label text-dark">Высота стен</label>
                        <input type="number" step="0.01" min="0" class="form-control border-secondary-subtle" id="height" required>
                    </div>
                </div>

                <h6 class="fw-semibold text-dark mt-4 mb-2">Размер окна:</h6>
                <div class="row mb-3">
                    <div class="col">
                        <input type="number" step="0.01" min="0" class="form-control border-secondary-subtle" id="windowWidth" value="1.2" placeholder="Ширина окна">
                    </div>
                    <div class="col">
                        <input type="number" step="0.01" min="0" class="form-control border-secondary-subtle" id="windowHeight" value="1.4" placeholder="Высота окна">
                    </div>
                </div>

                <h6 class="fw-semibold text-dark mb-2">Размер двери:</h6>
                <div class="row mb-4">
                    <div class="col">
                        <input type="number" step="0.01" min="0" class="form-control border-secondary-subtle" id="doorWidth" value="0.9" placeholder="Ширина двери">
                    </div>
                    <div class="col">
                        <input type="number" step="0.01" min="0" class="form-control border-secondary-subtle" id="doorHeight" value="2.0" placeholder="Высота двери">
                    </div>
                </div>

                <div class="mb-3 text-muted small">
                    Рулон: <strong>1.06 м</strong> шириной и <strong>10 м</strong> длиной
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary custom-btn">Рассчитать</button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- Кастомные стили для кнопки --}}
<style>
    .custom-btn {
        background-color: #01142f;
        color: white;
        border: none;
        transition: background-color 0.3s ease;
    }

    .custom-btn:hover {
        background-color: #02214b;
    }
</style>

<script>
    document.getElementById('wallpaperCalc').addEventListener('submit', function(e) {
        e.preventDefault();

        const length = parseFloat(document.getElementById('length').value);
        const width = parseFloat(document.getElementById('width').value);
        const height = parseFloat(document.getElementById('height').value);
        const windowWidth = parseFloat(document.getElementById('windowWidth').value);
        const windowHeight = parseFloat(document.getElementById('windowHeight').value);
        const doorWidth = parseFloat(document.getElementById('doorWidth').value);
        const doorHeight = parseFloat(document.getElementById('doorHeight').value);

        const rollWidth = 1.06;
        const rollLength = 10;

        if (length <= 0 || width <= 0 || height <= 0) {
            alert("Введите корректные размеры.");
            return;
        }

        const perimeter = 2 * (length + width);
        const wallArea = perimeter * height;
        const windowArea = windowWidth * windowHeight;
        const doorArea = doorWidth * doorHeight;
        const adjustedArea = wallArea - windowArea - doorArea;

        const stripsPerRoll = Math.floor(rollLength / height);
        const rollCoverageArea = stripsPerRoll * rollWidth * height;
        const rollsNeeded = Math.ceil(adjustedArea / rollCoverageArea);

        document.getElementById('rollsNeeded').textContent = rollsNeeded;

        const explanationList = document.getElementById('explanationList');
        explanationList.innerHTML = `
            <li>Периметр: <code>2 × (${length} + ${width}) = ${perimeter.toFixed(2)} м</code></li>
            <li>Площадь стен: <code>${perimeter.toFixed(2)} × ${height} = ${wallArea.toFixed(2)} м²</code></li>
            <li>Окно: <code>${windowWidth} × ${windowHeight} = ${windowArea.toFixed(2)} м²</code></li>
            <li>Дверь: <code>${doorWidth} × ${doorHeight} = ${doorArea.toFixed(2)} м²</code></li>
            <li>Чистая площадь: <code>${adjustedArea.toFixed(2)} м²</code></li>
            <li>Полос из рулона: <code>10 ÷ ${height} = ${stripsPerRoll}</code></li>
            <li>Покрытие рулона: <code>${rollCoverageArea.toFixed(2)} м²</code></li>
            <li>Нужно рулонов: <code>${rollsNeeded}</code></li>
        `;
        document.getElementById('detailedExplanation').style.display = 'block';
    });
</script>
@include('partials.footer')
@endsection
