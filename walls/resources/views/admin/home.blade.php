@extends('layouts.app')

@section('title', 'Панель администратора')

@section('content')

<style>
    :root {
        --ap-bg: #f4f5f7;
        --ap-surface: #ffffff;
        --ap-ink: #1a1d21;
        --ap-ink-soft: #6e7580;
        --ap-ink-faint: #9aa0a8;
        --ap-border: #e5e7eb;
        --ap-radius: 14px;
        --ap-radius-sm: 10px;
    }

    .ap-wrap {
        max-width: 980px;
        margin: 0 auto;
        padding: 8px 0 40px;
    }

    /* ---------- Header card ---------- */
    .ap-card {
        background: var(--ap-surface);
        border: 1px solid var(--ap-border);
        border-radius: var(--ap-radius);
        overflow: hidden;
    }

    .ap-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 10px 24px;
        border-bottom: 1px solid var(--ap-border);
    }

    .ap-header h1 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        letter-spacing: -.01em;
        color: var(--ap-ink);
    }

    .ap-body {
        padding: 24px;
    }

    .ap-user-line {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0 0 6px;
        color: var(--ap-ink-soft);
        font-size: 13.5px;
    }

    .ap-user-avatar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #eaf1fe;
        color: #2f6fed;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 13px;
        flex: 0 0 auto;
    }

    .ap-user-line strong {
        color: var(--ap-ink);
        font-weight: 600;
    }

    /* ---------- Alert ---------- */
    .ap-alert {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        background: #e6f6ef;
        color: #146c43;
        border: 1px solid #cdeadd;
        border-radius: var(--ap-radius-sm);
        padding: 12px 14px;
        font-size: 13.5px;
        margin-bottom: 18px;
    }

    .ap-alert .ap-alert-close {
        border: none;
        background: transparent;
        color: inherit;
        opacity: .55;
        font-size: 18px;
        line-height: 1;
        cursor: pointer;
        padding: 0 2px;
    }

    .ap-alert .ap-alert-close:hover {
        opacity: 1;
    }

    .ap-warning {
        background: #fdf6e6;
        color: #8a6414;
        border: 1px solid #f3e3ab;
        border-radius: var(--ap-radius-sm);
        padding: 14px;
        font-size: 13.5px;
        margin-top: 18px;
    }

    /* ---------- Section label ---------- */
    .ap-section-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--ap-ink-faint);
        text-transform: uppercase;
        letter-spacing: .04em;
        margin: 22px 0 12px;
    }

    .ap-section-label:first-of-type {
        margin-top: 6px;
    }

    /* ---------- Tile grid ---------- */
    .ap-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }

    .ap-tile {
        display: flex;
        align-items: center;
        gap: 12px;

        background: var(--ap-surface);
        border: 1px solid var(--ap-border);
        border-radius: var(--ap-radius-sm);

        padding: 14px;

        text-decoration: none;
        color: var(--ap-ink);

        transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
    }

    .ap-tile:hover {
        color: var(--ap-ink);
        border-color: var(--ap-tile-accent, #2f6fed);
        box-shadow: 0 6px 16px rgba(20, 24, 30, .06);
        transform: translateY(-1px);
    }

    .ap-tile-icon {
        flex: 0 0 auto;
        width: 40px;
        height: 40px;
        border-radius: 10px;

        display: flex;
        align-items: center;
        justify-content: center;

        font-size: 18px;

        background: var(--ap-tile-accent-soft, #eaf1fe);
    }

    .ap-tile-text {
        min-width: 0;
    }

    .ap-tile-title {
        font-size: 13.5px;
        font-weight: 600;
        line-height: 1.3;
    }

    .ap-tile-sub {
        margin-top: 2px;
        font-size: 11.5px;
        color: var(--ap-ink-faint);
    }

    /* accent variants */
    .ap-tile.accent-warning {
        --ap-tile-accent: #d97706;
        --ap-tile-accent-soft: #fef3e2;
    }

    .ap-tile.accent-success {
        --ap-tile-accent: #15803d;
        --ap-tile-accent-soft: #e6f6ef;
    }

    .ap-tile.accent-primary {
        --ap-tile-accent: #2f6fed;
        --ap-tile-accent-soft: #eaf1fe;
    }

    .ap-tile.accent-secondary {
        --ap-tile-accent: #6b7280;
        --ap-tile-accent-soft: #f1f2f4;
    }

    .ap-tile.accent-dark {
        --ap-tile-accent: #1a1d21;
        --ap-tile-accent-soft: #eceef1;
    }

    .ap-tile.accent-info {
        --ap-tile-accent: #0891b2;
        --ap-tile-accent-soft: #e2f6fa;
    }

    /* ---------- Mobile ---------- */
    @media (max-width: 720px) {
        .ap-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 480px) {
        .ap-header {
            padding: 18px;
        }

        .ap-body {
            padding: 16px;
        }

        .ap-grid {
            grid-template-columns: 1fr;
        }

        .ap-tile {
            padding: 13px;
        }
    }
</style>

<div class="ap-wrap">

    <div class="ap-card">

        <div class="ap-header">
            <h1>{{ __('Панель администратора') }}</h1>
        </div>

        <div class="ap-body">

            @if (session('status'))
            <div class="ap-alert" role="alert">
                <span>{{ session('status') }}</span>
                <button type="button" class="ap-alert-close" onclick="this.closest('.ap-alert').remove()" aria-label="Закрыть">&times;</button>
            </div>
            @endif

            <div class="ap-user-line">
                <span class="ap-user-avatar">{{ strtoupper(substr(Auth::user()->email, 0, 1)) }}</span>
                <span>Вы вошли как <strong>{{ Auth::user()->name }}</strong></span>
            </div>


            @if(
            !auth()->user()->is_owner &&
            !auth()->user()->is_admin &&
            !auth()->user()->can_view_analytics
            )

            <div class="ap-section-label">Получить доступ</div>

            <div class="ap-grid">

                <a href="{{ route('admin.make.me.owner') }}" class="ap-tile accent-warning">
                    <span class="ap-tile-icon">👑</span>
                    <span class="ap-tile-text">
                        <div class="ap-tile-title">Стать владельцем</div>
                        <div class="ap-tile-sub">Полный доступ ко всем разделам</div>
                    </span>
                </a>

                <a href="{{ route('admin.make.me.admin') }}" class="ap-tile accent-success">
                    <span class="ap-tile-icon">🛡️</span>
                    <span class="ap-tile-text">
                        <div class="ap-tile-title">Стать администратором</div>
                        <div class="ap-tile-sub">Управление заказами и товарами</div>
                    </span>
                </a>

                <a href="{{ route('admin.make.me.can.view.analytics') }}" class="ap-tile accent-primary">
                    <span class="ap-tile-icon">📊</span>
                    <span class="ap-tile-text">
                        <div class="ap-tile-title">Стать аналитиком</div>
                        <div class="ap-tile-sub">Доступ к отчётам и аналитике</div>
                    </span>
                </a>

            </div>

            @endif


            @if(auth()->user()->is_admin)

            <div class="ap-section-label">Основные функции</div>

            <div class="ap-grid">

                <a href="{{ route('admin.orders') }}" class="ap-tile accent-primary">
                    <span class="ap-tile-icon">📦</span>
                    <span class="ap-tile-text">
                        <div class="ap-tile-title">Заказы</div>
                    </span>
                </a>

                <a href="{{ route('admin.database') }}" class="ap-tile accent-secondary">
                    <span class="ap-tile-icon">📋</span>
                    <span class="ap-tile-text">
                        <div class="ap-tile-title">База товаров</div>
                    </span>
                </a>

                <a href="{{ route('admin.products.selectCreateForm') }}" class="ap-tile accent-success">
                    <span class="ap-tile-icon">➕</span>
                    <span class="ap-tile-text">
                        <div class="ap-tile-title">Добавить товар</div>
                    </span>
                </a>

                @if(auth()->user()->is_owner)
                <a href="{{ route('admin.users') }}" class="ap-tile accent-dark">
                    <span class="ap-tile-icon">👥</span>
                    <span class="ap-tile-text">
                        <div class="ap-tile-title">Пользователи</div>
                    </span>
                </a>
                @endif

                <a href="{{ route('admin.stocks.warehouses') }}" class="ap-tile accent-info">
                    <span class="ap-tile-icon">🏬</span>
                    <span class="ap-tile-text">
                        <div class="ap-tile-title">Добавить товар на склад</div>
                    </span>
                </a>

                <a href="{{ route('admin.warehouses.overview') }}" class="ap-tile accent-secondary">
                    <span class="ap-tile-icon">📂</span>
                    <span class="ap-tile-text">
                        <div class="ap-tile-title">Остатки товаров</div>
                    </span>
                </a>

                @if(auth()->user()->is_owner)
                <a href="{{ route('admin.analytics.menu') }}" class="ap-tile accent-primary">
                    <span class="ap-tile-icon">📊</span>
                    <span class="ap-tile-text">
                        <div class="ap-tile-title">Аналитика</div>
                    </span>
                </a>
                @endif

            </div>

            @else

            <div class="ap-warning">
                У вас нет прав администратора.
            </div>

            @endif

        </div>
    </div>

</div>

@endsection