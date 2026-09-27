<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

    <div class="container-fluid">

        <div class="mb-4">
            <h1 class="mb-1">Редактировать поставщика</h1>
            <a href="{{ route('admin.suppliers.index') }}" class="text-muted">
                ← Назад к поставщикам
            </a>
        </div>

        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form
                    action="{{ route('admin.suppliers.update', $supplier) }}"
                    method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">
                            Название поставщика *
                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="{{ old('name', $supplier->name) }}"
                            required
                            autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Телефон
                        </label>

                        <input
                            type="text"
                            name="phone"
                            class="form-control"
                            value="{{ old('phone', $supplier->phone) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Комментарий
                        </label>

                        <textarea
                            name="comment"
                            class="form-control"
                            rows="4">{{ old('comment', $supplier->comment) }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            Сохранить изменения
                        </button>

                        <a
                            href="{{ route('admin.suppliers.index') }}"
                            class="btn btn-secondary">
                            Отмена
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>


</body>

</html>