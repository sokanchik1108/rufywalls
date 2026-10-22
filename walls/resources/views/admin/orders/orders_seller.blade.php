```blade
@extends('layouts.app')

@section('title', 'Заказы продавцов')

@section('content')
@php
$selectedDate = request()->get('date')
    ? \Carbon\Carbon::parse(request()->get('date'), 'Asia/Almaty')->format('Y-m-d')
    : now('Asia/Almaty')->format('Y-m-d');

$prevDate = \Carbon\Carbon::parse($selectedDate)->subDay()->format('Y-m-d');
$nextDate = \Carbon\Carbon::parse($selectedDate)->addDay()->format('Y-m-d');

$totalDaySum = 0;
$totalDayReturns = 0;
$dailyPaymentTotals = [];

$methodNames = [
    'cash' => 'Наличные',
    'qr' => 'QR',
    'transfer' => 'Перевод',
    'card' => 'Карта',
];

$normalizePaymentMethod = static function ($method) {
    $value = mb_strtolower(trim((string) $method));

    if ($value === '') return 'unknown';
    if ($value === 'cash' || str_contains($value, 'налич') || str_contains($value, 'cash')) return 'cash';
    if ($value === 'qr' || str_contains($value, 'qr')) return 'qr';
    if ($value === 'transfer' || str_contains($value, 'перевод') || str_contains($value, 'transfer')) return 'transfer';
    if ($value === 'card' || str_contains($value, 'карт') || str_contains($value, 'card')) return 'card';

    return $value;
};

$getPaymentMethodName = static function ($method) use ($normalizePaymentMethod, $methodNames) {
    $key = $normalizePaymentMethod($method);

    return $methodNames[$key] ?? (trim((string) $method) ?: 'Способ не указан');
};

$formatQuantity = static function ($quantity) {
    return rtrim(rtrim(number_format((float) $quantity, 2, '.', ''), '0'), '.');
};

$getOrderReturns = static function ($order) use ($returnDetails) {
    return ($returnDetails ?? collect())->get($order->id, collect());
};

$getReturnPrice = static function ($returnItem) {
    return (float) ($returnItem->orderItem->price ?? 0);
};

$getReturnSku = static function ($returnItem) {
    return $returnItem->variant->sku
        ?? $returnItem->orderItem->variant->sku
        ?? 'Товар';
};

/*
|--------------------------------------------------------------------------
| Стоимость товаров в документе возврата до скидки
|--------------------------------------------------------------------------
*/
$getReturnGrossTotal = static function ($return) use ($getReturnPrice) {
    return round(
        collect($return->items ?? [])->sum(function ($item) use ($getReturnPrice) {
            return (float) $item->quantity * $getReturnPrice($item);
        }),
        2
    );
};

/*
|--------------------------------------------------------------------------
| Фактически возвращённые деньги по конкретному документу
|--------------------------------------------------------------------------
| Если у документа есть платежи, используем именно их.
| Это важно, когда скидка заказа была вычтена из последнего полного возврата.
|--------------------------------------------------------------------------
*/
$getReturnPaymentTotal = static function ($return) use ($getReturnGrossTotal) {
    $payments = collect($return->payments ?? []);

    if ($payments->isNotEmpty()) {
        return round(
            (float) $payments->sum(fn ($payment) => (float) $payment->amount),
            2
        );
    }

    // Для старых документов без записанных платежей оставляем расчёт по товарам.
    return $getReturnGrossTotal($return);
};

/*
|--------------------------------------------------------------------------
| Общая сумма возвратов по заказу — фактические выплаты
|--------------------------------------------------------------------------
*/
$getOrderReturnTotal = static function ($order) use ($getOrderReturns, $getReturnPaymentTotal) {
    return round(
        collect($getOrderReturns($order))->sum(
            fn ($return) => $getReturnPaymentTotal($return)
        ),
        2
    );
};

/*
|--------------------------------------------------------------------------
| Чистые платежи по способам оплаты с учётом возвратов
|--------------------------------------------------------------------------
*/
$getNetPayments = static function ($order) use ($getOrderReturns, $normalizePaymentMethod) {
    $payments = [];

    foreach ($order->payments ?? [] as $payment) {
        $method = $normalizePaymentMethod($payment->payment_method ?? '');

        $payments[$method] = ($payments[$method] ?? 0)
            + (float) $payment->amount;
    }

    foreach ($getOrderReturns($order) as $return) {
        foreach ($return->payments ?? [] as $payment) {
            $methodName = $payment->paymentMethod->name
                ?? $payment->payment_method
                ?? null;

            $method = $normalizePaymentMethod($methodName);

            $payments[$method] = ($payments[$method] ?? 0)
                - (float) $payment->amount;
        }
    }

    return array_filter(
        $payments,
        static fn ($amount) => abs($amount) > 0.001
    );
};
@endphp

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
:root{--page-bg:#f7f8fa;--surface:#fff;--text:#25292f;--text-secondary:#727b87;--text-muted:#a1a8b1;--border:#e8ebef;--border-light:#eef0f3;--blue:#2f6fed;--blue-hover:#1f56d1;--blue-light:#f1f6fd;--red:#bd3038;--red-light:#fff0f0;--red-border:#f2c4c4;--green:#16803d;--green-light:#e7f7ed;--radius:9px;--radius-small:7px}
*{box-sizing:border-box}
body{background:var(--page-bg);color:var(--text);font-family:Inter,'Segoe UI',Arial,sans-serif;font-size:14px;-webkit-font-smoothing:antialiased}
.container{max-width:680px}
.orders-page{padding-top:10px;padding-bottom:45px}
.orders-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:17px}
.orders-title{margin:0;font-size:20px;line-height:1.2;font-weight:700;letter-spacing:-.025em}
.btn-create{height:36px;display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:0 15px;background:var(--blue);color:#fff;border:1px solid var(--blue);border-radius:var(--radius-small);font-size:13px;font-weight:600;text-decoration:none}
.btn-create:hover{background:var(--blue-hover);color:#fff}
.orders-toolbar{display:flex;gap:6px;margin-bottom:10px}
.date-picker{width:100%;display:flex;align-items:center;gap:6px}
.date-arrow{width:35px;height:35px;flex:0 0 35px;display:flex;align-items:center;justify-content:center;background:#fff;border:1px solid var(--border);border-radius:var(--radius-small);color:#89919b;text-decoration:none}
.date-arrow:hover{background:var(--blue-light);color:var(--blue)}
.date-input{width:100%;height:35px;background:#fff;border:1px solid var(--border)!important;border-radius:var(--radius-small)!important;text-align:center;font-family:inherit;font-size:13px;box-shadow:none!important}
.date-input:focus,#searchInput:focus{border-color:#c8dbf1!important;box-shadow:0 0 0 3px var(--blue-light)!important}
.search-wrapper{position:relative;margin-bottom:13px}
.search-icon{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:#adb4bc;pointer-events:none}
#searchInput{width:100%;height:39px;padding-left:36px;padding-right:42px;background:#fff;border:1px solid var(--border)!important;border-radius:var(--radius)!important;font-family:inherit;font-size:13px;box-shadow:none!important}
#searchInput::placeholder{color:#adb4bc}
.search-clear{position:absolute;right:10px;top:50%;transform:translateY(-50%);width:26px;height:26px;border:0;border-radius:50%;background:#f0f2f5;color:#68717d;display:none;align-items:center;justify-content:center;cursor:pointer;z-index:2;font-size:18px;line-height:1}
.search-clear.show{display:flex}
.autocomplete-list{position:absolute;top:calc(100% + 4px);left:0;right:0;display:none;max-height:260px;overflow-y:auto;background:#fff;border:1px solid var(--border);border-radius:9px;box-shadow:0 8px 24px rgba(30,40,55,.12);z-index:1000}
.autocomplete-list.show{display:block}
.autocomplete-item{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:11px 13px;cursor:pointer;border-bottom:1px solid var(--border-light);font-size:13px}
.autocomplete-item:last-child{border-bottom:none}
.autocomplete-item:hover,.autocomplete-item.active{background:var(--blue-light)}
.autocomplete-sku{font-weight:700;color:var(--text)}
.autocomplete-label{font-size:11px;color:var(--text-muted);white-space:nowrap}
.day-summary{background:#fff;border:1px solid var(--border);border-radius:var(--radius);margin-bottom:13px;overflow:hidden}
.day-summary-main{display:flex;align-items:center;justify-content:space-between;padding:13px 15px}
.summary-label{font-size:12px;color:var(--text-secondary);margin-bottom:3px}
.summary-date{font-size:11px;color:var(--text-muted)}
.summary-total{font-size:18px;font-weight:700;letter-spacing:-.02em}
.summary-return{margin-top:4px;font-size:11px;color:var(--red);text-align:right}
.payment-summary{display:flex;flex-wrap:wrap;gap:6px;padding:9px 15px;border-top:1px solid var(--border-light);background:#fcfcfd}
.payment-chip{display:inline-flex;align-items:center;gap:5px;padding:5px 8px;border:1px solid #e9edf1;border-radius:6px;background:#fff;color:var(--text-secondary);font-size:11px}
.payment-chip strong{color:#4e5660;font-weight:600}
.payment-chip.negative strong{color:var(--red)}
.orders-list{display:flex;flex-direction:column;gap:8px}
.order-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:13px 14px;transition:border-color .15s,box-shadow .15s}
.order-card:hover{border-color:#dde2e8;box-shadow:0 2px 10px rgba(40,50,65,.025)}
.order-top{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:9px}
.order-number{display:flex;align-items:center;flex-wrap:wrap;gap:5px;color:#424951;font-size:13px;font-weight:700;min-width:0}
.order-date{flex:0 0 auto;color:var(--text-muted);font-size:11px;text-align:right;white-space:nowrap}
.return-badge{display:inline-flex;align-items:center;gap:4px;padding:4px 7px;border-radius:5px;background:#fff1f1;border:1px solid #f0d1d1;color:var(--red);font-size:10px;font-weight:600;white-space:nowrap}
.payment-badge{display:inline-flex;align-items:center;gap:4px;padding:4px 7px;border-radius:6px;background:var(--green-light);border:1px solid #b9e6c7;color:var(--green);font-size:10.5px;font-weight:700;white-space:nowrap}
.payment-badge.negative{background:var(--red-light);border-color:var(--red-border);color:var(--red)}
.customer-row{display:flex;align-items:flex-start;justify-content:space-between;gap:12px}
.customer-block{flex:1 1 auto;min-width:0}
.customer-name{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#343940;font-size:13.5px;font-weight:600}
.customer-phone{margin-top:3px;color:var(--text-secondary);font-size:11.5px}
.customer-payments{display:flex;flex-wrap:wrap;gap:5px;margin-top:7px}
.customer-payments .payment-badge{white-space:normal;line-height:1.4;font-size:10.5px}
.order-point{flex:0 1 auto;max-width:48%;color:#8a929c;font-size:10.5px;text-align:right;overflow-wrap:anywhere}
.order-bottom{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-top:12px;padding-top:10px;border-top:1px solid var(--border-light)}
.order-finance{min-width:0}
.discount-line{margin-bottom:2px;color:#a4aab1;font-size:10.5px}
.return-amount{margin-bottom:3px;color:var(--red);font-size:11px;font-weight:600}
.order-total{color:#30353b;font-size:16px;font-weight:700;letter-spacing:-.015em}
.order-actions{display:flex;align-items:center;gap:5px}
.action-btn{width:27px;height:27px;display:flex;align-items:center;justify-content:center;border-radius:7px;border:1px solid var(--blue);background:#fff;color:var(--blue);text-decoration:none;font-size:11px;cursor:pointer}
.action-btn:not(.action-delete):hover{background:var(--blue-hover);color:#fff}
.action-delete{background:var(--red-light);border-color:var(--red-border);color:var(--red)}
.action-delete:hover{background:#ffeded;color:#d6535c}
.delete-form{display:inline-flex;margin:0}
.details-btn{width:100%;height:33px;margin-top:9px;border:1px solid var(--blue);border-radius:7px;background:var(--blue);color:#fff;font-family:inherit;font-size:12px;font-weight:500;cursor:pointer}
.details-btn:hover{background:var(--blue-hover)}
.modal-content{border:none;border-radius:0;background:var(--page-bg);font-family:Inter,'Segoe UI',Arial,sans-serif}
.modal-header{padding:12px 16px;background:#fff;border-bottom:1px solid var(--border)}
.modal-title{color:#30353b;font-size:14px;font-weight:700}
.btn-close{opacity:.4;transform:scale(.85)}
.modal-body{padding:12px 16px 25px}
.modal-section{background:#fff;border:1px solid var(--border);border-radius:8px;padding:10px 12px;margin-bottom:7px}
.modal-section-title{margin-bottom:6px;color:#9299a2;font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.02em}
.info-row{display:flex;align-items:center;justify-content:space-between;gap:15px;min-height:27px;padding:4px 0;border-bottom:1px solid #f2f3f5}
.info-row:last-child{border-bottom:none}
.info-label{flex:0 0 auto;color:#9ba2aa;font-size:10.5px}
.info-value{min-width:0;color:#3e444b;font-size:11.5px;font-weight:600;text-align:right;overflow-wrap:anywhere}
.customer-info-inline{display:flex;align-items:center;justify-content:space-between;gap:15px;min-width:0;width:100%}
.customer-info-name{min-width:0;color:#3e444b;font-size:11.5px;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.customer-info-phone{flex:0 0 auto;color:#737b85;font-size:11px;white-space:nowrap}
.order-info-inline{display:flex;align-items:center;justify-content:flex-end;gap:14px;min-width:0}
.order-info-value{color:#3e444b;font-size:11.5px;font-weight:600}
.order-info-date{color:#737b85;font-size:11px;white-space:nowrap}
.finance-inline{display:flex;align-items:center;justify-content:flex-end;gap:10px;flex-wrap:wrap}
.finance-discount{color:var(--red);font-size:11px;white-space:nowrap}
.finance-total{color:#30353b;font-size:12px;font-weight:700;white-space:nowrap}
.comment-value{color:#555d66;font-size:11.5px;font-weight:500;text-align:right;white-space:pre-wrap}
.modal-payments{display:flex;flex-wrap:wrap;justify-content:flex-end;gap:5px}
.modal-payment{display:inline-flex;align-items:center;gap:4px;padding:4px 7px;border-radius:5px;background:var(--green-light);color:var(--green);font-size:10.5px;white-space:nowrap}
.modal-payment.negative{background:var(--red-light);color:var(--red)}
.modal-item{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:7px 0;border-bottom:1px solid #f1f2f4}
.modal-item:last-child{border-bottom:none}
.item-left{min-width:0;flex:1 1 auto}
.item-main-line{display:flex;align-items:center;gap:7px;min-width:0}
.item-sku{min-width:0;color:#41474e;font-size:11.5px;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.item-batch-inline{flex:0 0 auto;color:#a1a8b0;font-size:10px;white-space:nowrap}
.item-qty{margin-top:3px;color:#7c848d;font-size:10.5px}
.item-sum{flex:0 0 auto;color:#3e444b;font-size:11.5px;font-weight:700;white-space:nowrap}
.return-section{border-color:var(--red-border);background:#fffdfd}
.return-section .modal-section-title{color:var(--red)}
.return-heading{margin:10px 0 5px;color:var(--red);font-size:11px;font-weight:700}
.return-total-row{display:flex;justify-content:space-between;gap:12px;padding-top:8px;margin-top:5px;border-top:1px solid var(--red-border);font-size:11px;font-weight:600}
.return-total-row strong{color:var(--red)}
.return-note{margin-top:5px;color:#8a929c;font-size:10.5px;line-height:1.5}
.orders-empty{padding:36px 20px;text-align:center;background:#fff;border:1px solid var(--border);border-radius:var(--radius);color:var(--text-muted);font-size:12.5px}
@media(max-width:575px){
.container{max-width:100%;padding-left:12px;padding-right:12px}
.orders-page{padding-top:14px}
.orders-title{font-size:18px}
.btn-create{height:34px;padding:0 11px;font-size:12px}
.date-arrow{width:34px;height:34px;flex-basis:34px}
.date-input,#searchInput{font-size:16px!important;-webkit-text-size-adjust:100%}
.order-card{padding:12px}
.order-total{font-size:15px}
.modal-header{padding:11px 13px}
.modal-body{padding:9px 10px 20px}
.modal-section{padding:9px 10px;margin-bottom:6px}
.customer-info-inline{gap:8px}
.customer-info-name{font-size:11px}
.customer-info-phone{font-size:10px}
.order-info-inline{gap:9px}
.order-info-value,.order-info-date{font-size:10.5px}
.info-label{font-size:10px}
.info-value{font-size:11px}
.payment-summary{padding:8px 12px}
.modal-payments{gap:4px}
.modal-payment{padding:4px 6px;font-size:10px}
.item-main-line{gap:5px}
.item-sku{font-size:11px}
.item-batch-inline{font-size:9px}
.customer-payments{gap:4px}
.customer-payments .payment-badge{font-size:10px;padding:4px 6px}
}
</style>

<div class="container orders-page">
    <div class="orders-header">
        <h1 class="orders-title">Заказы</h1>
        <a href="{{ route('admin.orders.create') }}" class="btn-create">
            <i class="bi bi-plus-lg"></i> Создать
        </a>
    </div>

    <div class="orders-toolbar">
        <div class="date-picker">
            <a href="{{ route('admin.orders.seller', ['date' => $prevDate]) }}"
               class="date-arrow" title="Предыдущий день">
                <i class="bi bi-chevron-left"></i>
            </a>

            <form method="GET" style="flex:1">
                <input type="date" name="date" value="{{ $selectedDate }}"
                       onchange="this.form.submit()" class="date-input">
            </form>

            <a href="{{ route('admin.orders.seller', ['date' => $nextDate]) }}"
               class="date-arrow" title="Следующий день">
                <i class="bi bi-chevron-right"></i>
            </a>
        </div>
    </div>

    <form method="GET" id="ordersSearchForm" class="search-wrapper">
        <i class="bi bi-search search-icon"></i>

        <input type="hidden" name="date" value="{{ $selectedDate }}">

        <input type="text" id="searchInput" name="q" class="form-control"
               value="{{ request('q', request('sku', '')) }}"
               placeholder="Поиск по имени, телефону, номеру заказа или артикулу"
               autocomplete="off" aria-autocomplete="list"
               aria-controls="autocompleteList">

        <button type="button" id="clearSearch" class="search-clear"
                aria-label="Очистить поиск" title="Очистить">×</button>

        <div id="autocompleteList" class="autocomplete-list"
             role="listbox" aria-label="Артикулы за последние 30 дней"></div>
    </form>

    @if(!request()->filled('q') && !request()->filled('sku'))
        @foreach($orders as $order)
            @php
                $orderSum = $order->items->sum(
                    fn ($item) => (float) ($item->price ?? 0) * (float) $item->quantity
                );

                $orderDiscount = (float) ($order->discount ?? 0);
                $finalSum = $orderSum - $orderDiscount;

                $orderReturnTotal = $getOrderReturnTotal($order);
                $netPayments = $getNetPayments($order);

                $totalDayReturns += $orderReturnTotal;
                $totalDaySum += $finalSum - $orderReturnTotal;

                foreach ($netPayments as $method => $amount) {
                    $dailyPaymentTotals[$method] =
                        ($dailyPaymentTotals[$method] ?? 0) + $amount;
                }
            @endphp
        @endforeach

        @php
            $dailyPaymentTotals = array_filter(
                $dailyPaymentTotals,
                fn ($amount) => abs($amount) > 0.001
            );
        @endphp

        <div class="day-summary">
            <div class="day-summary-main">
                <div>
                    <div class="summary-label">Продажи за день</div>
                    <div class="summary-date">
                        {{ \Carbon\Carbon::parse($selectedDate)->format('d.m.Y') }}
                    </div>
                </div>

                <div style="text-align:right">
                    <div class="summary-total">
                        {{ number_format($totalDaySum + $totalDayReturns, 0, '.', ' ') }} ₸
                    </div>

                    @if($totalDayReturns > 0)
                        <div class="summary-return">
                            Возвраты: −{{ number_format($totalDayReturns, 0, '.', ' ') }} ₸
                        </div>
                    @endif

                    <div style="margin-top:4px;font-size:12px;font-weight:700;color:#30353b">
                        Итог: {{ number_format($totalDaySum, 0, '.', ' ') }} ₸
                    </div>
                </div>
            </div>

            @if(count($dailyPaymentTotals) > 0)
                <div class="payment-summary">
                    @foreach($dailyPaymentTotals as $method => $amount)
                        <div class="payment-chip {{ $amount < 0 ? 'negative' : '' }}">
                            <span>{{ $methodNames[$method] ?? ucfirst($method) }}:</span>
                            <strong>
                                {{ $amount < 0 ? '−' : '' }}{{ number_format(abs($amount), 0, '.', ' ') }} ₸
                            </strong>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    <div id="mobileOrders" class="orders-list">
        @forelse($orders as $order)
            @php
                $orderSum = $order->items->sum(
                    fn ($item) => (float) ($item->price ?? 0) * (float) $item->quantity
                );

                $finalSum = $orderSum - (float) ($order->discount ?? 0);
                $orderReturnTotal = $getOrderReturnTotal($order);
                $orderReturns = $getOrderReturns($order);

                $hasReturn = in_array(
                    (int) $order->id,
                    $ordersWithReturns ?? [],
                    true
                ) || $orderReturns->isNotEmpty();
            @endphp

            <div class="order-card">
                <div class="order-top">
                    <div class="order-number">#{{ $order->id }}</div>
                    <div class="order-date">
                        {{ $order->order_date?->format('d.m.Y H:i') ?? '—' }}
                    </div>
                </div>

                <div class="customer-row">
                    <div class="customer-block">
                        <div class="customer-name">{{ $order->name ?: 'Без имени' }}</div>
                        <div class="customer-phone">{{ $order->phone ?: 'Телефон не указан' }}</div>

                        <div class="customer-payments">
                            @foreach($order->payments as $payment)
                                <span class="payment-badge">
                                    {{ $getPaymentMethodName($payment->payment_method) }}:
                                    +{{ number_format((float) $payment->amount, 0, '.', ' ') }} ₸
                                </span>
                            @endforeach

                            @foreach($orderReturns as $return)
                                @foreach($return->payments as $payment)
                                    <span class="payment-badge negative">
                                        {{ $getPaymentMethodName($payment->paymentMethod->name ?? $payment->payment_method ?? null) }}:
                                        −{{ number_format((float) $payment->amount, 0, '.', ' ') }} ₸
                                    </span>
                                @endforeach
                            @endforeach

                            @if($order->payments->isEmpty() && $orderReturns->every(fn ($return) => $return->payments->isEmpty()))
                                <span class="payment-badge negative">Оплата не указана</span>
                            @endif
                        </div>
                    </div>

                    <div class="order-point">
                        {{ $order->pointOfSale->name ?? 'Точка не указана' }}
                    </div>
                </div>

                <div class="order-bottom">
                    <div class="order-finance">
                        @if(($order->discount ?? 0) > 0)
                            <div class="discount-line">
                                Скидка: {{ number_format($order->discount, 0, '.', ' ') }} ₸
                            </div>
                        @endif

                        @if($orderReturnTotal > 0)
                            <div class="return-amount">
                                Возврат: −{{ number_format($orderReturnTotal, 0, '.', ' ') }} ₸
                            </div>
                        @endif

                        <div class="order-total">
                            {{ number_format($finalSum, 0, '.', ' ') }} ₸
                        </div>
                    </div>

                    <div class="order-actions">
                        <a href="{{ route('admin.orders.edit', $order->id) }}"
                           class="action-btn" title="Редактировать">
                            <i class="bi bi-pencil"></i>
                        </a>

                        <form action="{{ route('admin.orders.destroy', $order->id) }}"
                              method="POST" class="delete-form"
                              onsubmit="return confirm('Удалить заказ?')">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="action-btn action-delete" title="Удалить">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <button type="button" class="details-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#orderModal{{ $order->id }}">
                    Подробнее
                </button>
            </div>
        @empty
            <div class="orders-empty">
                <i class="bi bi-receipt" style="font-size:24px;display:block;margin-bottom:8px"></i>
                Заказов не найдено
            </div>
        @endforelse
    </div>
</div>

@foreach($orders as $order)
    @php
        $orderSum = $order->items->sum(
            fn ($item) => (float) ($item->price ?? 0) * (float) $item->quantity
        );

        $orderDiscount = (float) ($order->discount ?? 0);
        $finalSum = $orderSum - $orderDiscount;

        $orderReturnTotal = $getOrderReturnTotal($order);
        $netPayments = $getNetPayments($order);
        $orderReturns = $getOrderReturns($order);

        $hasReturn = in_array(
            (int) $order->id,
            $ordersWithReturns ?? [],
            true
        ) || $orderReturns->isNotEmpty();
    @endphp

    <div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">
                        Заказ #{{ $order->id }}

                        @if($hasReturn)
                            <span class="return-badge">
                                <i class="bi bi-arrow-counterclockwise"></i>
                                Был возврат
                            </span>
                        @endif
                    </h6>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="modal-section">
                        <div class="modal-section-title">Покупатель</div>

                        <div class="info-row">
                            <div class="customer-info-inline">
                                <div class="customer-info-name">
                                    {{ $order->name ?: 'Без имени' }}
                                </div>
                                <div class="customer-info-phone">
                                    {{ $order->phone ?: 'Телефон не указан' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-section">
                        <div class="modal-section-title">Информация о заказе</div>

                        <div class="info-row">
                            <div class="info-label">Точка продаж</div>

                            <div class="order-info-inline">
                                <div class="order-info-value">
                                    <i class="bi bi-shop" style="color:#aeb6bf;margin-right:4px"></i>
                                    {{ $order->pointOfSale->name ?? '—' }}
                                </div>

                                <div class="order-info-date">
                                    {{ $order->order_date?->format('d.m.Y H:i') ?? '—' }}
                                </div>
                            </div>
                        </div>

                        @if($order->comment)
                            <div class="info-row">
                                <div class="info-label">Комментарий</div>
                                <div class="comment-value">{{ $order->comment }}</div>
                            </div>
                        @endif
                    </div>

                    <div class="modal-section">
                        <div class="modal-section-title">Оплата</div>

                        <div class="info-row">
                            <div class="info-label">Сумма товаров</div>
                            <div class="finance-total">
                                {{ number_format($orderSum, 0, '.', ' ') }} ₸
                            </div>
                        </div>

                        @if($orderDiscount > 0)
                            <div class="info-row">
                                <div class="info-label">Скидка заказа</div>
                                <div class="finance-discount">
                                    −{{ number_format($orderDiscount, 0, '.', ' ') }} ₸
                                </div>
                            </div>
                        @endif

                        <div class="info-row">
                            <div class="info-label">Итого по заказу</div>
                            <div class="finance-total">
                                {{ number_format($finalSum, 0, '.', ' ') }} ₸
                            </div>
                        </div>

                        @if($orderReturnTotal > 0)
                            <div class="info-row">
                                <div class="info-label">Фактически возвращено</div>
                                <div class="finance-total" style="color:var(--red)">
                                    −{{ number_format($orderReturnTotal, 0, '.', ' ') }} ₸
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">Итого после возвратов</div>
                                <div class="finance-total">
                                    {{ number_format($finalSum - $orderReturnTotal, 0, '.', ' ') }} ₸
                                </div>
                            </div>
                        @endif

                        <div class="info-row">
                            <div class="info-label">Платежи после возвратов</div>

                            <div class="modal-payments">
                                @forelse($netPayments as $method => $amount)
                                    <div class="modal-payment {{ $amount < 0 ? 'negative' : '' }}">
                                        {{ $methodNames[$method] ?? $method }}
                                        {{ $amount < 0 ? '−' : '' }}{{ number_format(abs($amount), 0, '.', ' ') }} ₸
                                    </div>
                                @empty
                                    <span style="color:var(--text-muted);font-size:11px">Нет платежей</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="modal-section">
                        <div class="modal-section-title">Товары</div>

                        @forelse($order->items as $item)
                            <div class="modal-item">
                                <div class="item-left">
                                    <div class="item-main-line">
                                        <div class="item-sku">
                                            {{ $item->variant->sku ?? '—' }}
                                        </div>
                                        <div class="item-batch-inline">
                                            Партия: {{ $item->batch_code ?? '—' }}
                                        </div>
                                    </div>

                                    <div class="item-qty">
                                        {{ $formatQuantity($item->quantity) }}
                                        × {{ number_format($item->price ?? 0, 0, '.', ' ') }} ₸
                                    </div>
                                </div>

                                <div class="item-sum">
                                    {{ number_format(($item->price ?? 0) * $item->quantity, 0, '.', ' ') }} ₸
                                </div>
                            </div>
                        @empty
                            <div style="color:var(--text-muted);font-size:11px;padding:7px 0">
                                Товаров нет
                            </div>
                        @endforelse
                    </div>

                    @if($orderReturns->isNotEmpty())
                        <div class="modal-section return-section">
                            <div class="modal-section-title">
                                <i class="bi bi-arrow-counterclockwise"></i>
                                Возвраты товара
                            </div>

                            @foreach($orderReturns as $return)
                                @php
                                    $returnQuantity = 0;
                                    $returnGrossTotal = $getReturnGrossTotal($return);
                                    $returnPaidTotal = $getReturnPaymentTotal($return);

                                    // Разница между стоимостью товаров и выплатой.
                                    // Если скидка была вычтена из этого возврата,
                                    // она будет показана отдельно.
                                    $returnDiscount = max(
                                        0,
                                        round($returnGrossTotal - $returnPaidTotal, 2)
                                    );

                                    $returnPayments = collect($return->payments ?? []);
                                @endphp

                                <div class="return-heading">
                                    Возврат #{{ $return->id }}
                                    @if($return->return_date)
                                        · {{ $return->return_date->format('d.m.Y H:i') }}
                                    @endif
                                </div>

                                @foreach($return->items as $returnItem)
                                    @php
                                        $quantity = (float) $returnItem->quantity;
                                        $price = $getReturnPrice($returnItem);
                                        $itemTotal = $quantity * $price;

                                        $returnQuantity += $quantity;
                                    @endphp

                                    <div class="modal-item">
                                        <div class="item-left">
                                            <div class="item-main-line">
                                                <div class="item-sku">
                                                    {{ $getReturnSku($returnItem) }}
                                                </div>

                                                <div class="item-batch-inline">
                                                    Партия:
                                                    {{ $returnItem->batch->code
                                                        ?? $returnItem->batch->batch_code
                                                        ?? $returnItem->batch_id
                                                        ?? '—' }}
                                                </div>
                                            </div>

                                            <div class="item-qty">
                                                Возвращено: {{ $formatQuantity($quantity) }} шт.
                                                × {{ number_format($price, 0, '.', ' ') }} ₸
                                            </div>
                                        </div>

                                        <div class="item-sum" style="color:var(--red)">
                                            −{{ number_format($itemTotal, 0, '.', ' ') }} ₸
                                        </div>
                                    </div>
                                @endforeach

                                <div class="return-total-row">
                                    <span>Количество товаров</span>
                                    <strong>{{ $formatQuantity($returnQuantity) }} шт.</strong>
                                </div>

                                <div class="return-total-row" style="border-top:none;margin-top:0">
                                    <span>Стоимость товаров до скидки</span>
                                    <strong>
                                        {{ number_format($returnGrossTotal, 0, '.', ' ') }} ₸
                                    </strong>
                                </div>

                                @if($returnDiscount > 0.009)
                                    <div class="return-total-row" style="border-top:none;margin-top:0;padding-top:4px">
                                        <span>Вычтенная скидка</span>
                                        <strong>
                                            −{{ number_format($returnDiscount, 0, '.', ' ') }} ₸
                                        </strong>
                                    </div>
                                @endif

                                <div class="return-total-row">
                                    <span>Фактически возвращено</span>
                                    <strong>
                                        −{{ number_format($returnPaidTotal, 0, '.', ' ') }} ₸
                                    </strong>
                                </div>

                                @if($returnPayments->isNotEmpty())
                                    <div class="info-row">
                                        <div class="info-label">Способы возврата</div>

                                        <div class="modal-payments">
                                            @foreach($returnPayments as $returnPayment)
                                                <div class="modal-payment negative">
                                                    {{ $returnPayment->paymentMethod->name
                                                        ?? $returnPayment->payment_method
                                                        ?? 'Способ не указан' }}:
                                                    −{{ number_format((float) $returnPayment->amount, 0, '.', ' ') }} ₸
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($return->comment)
                                    <div class="info-row">
                                        <div class="info-label">Комментарий</div>
                                        <div class="comment-value">{{ $return->comment }}</div>
                                    </div>
                                @endif
                            @endforeach

                            <div class="return-total-row" style="margin-top:12px">
                                <span>Всего фактически возвращено по заказу</span>
                                <strong>
                                    −{{ number_format($orderReturnTotal, 0, '.', ' ') }} ₸
                                </strong>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach

<script>
document.addEventListener('DOMContentLoaded', function () {
    const search = document.getElementById('searchInput');
    const form = document.getElementById('ordersSearchForm');
    const clearButton = document.getElementById('clearSearch');
    const autocomplete = document.getElementById('autocompleteList');

    if (!search || !form || !clearButton || !autocomplete) return;

    const suggestionsUrl = @json(route('admin.orders.seller.sku-suggestions'));
    const selectedDate = @json($selectedDate);

    let suggestions = [];
    let activeIndex = -1;
    let debounceTimer = null;
    let requestController = null;

    function hideAutocomplete() {
        autocomplete.classList.remove('show');
        autocomplete.innerHTML = '';
        suggestions = [];
        activeIndex = -1;
    }

    function updateClearButton() {
        clearButton.classList.toggle('show', search.value.length > 0);
    }

    function goToSku(sku) {
        const url = new URL(window.location.href);

        url.searchParams.set('date', selectedDate);
        url.searchParams.delete('q');
        url.searchParams.set('sku', sku);

        window.location.href = url.toString();
    }

    function renderSuggestions(items) {
        suggestions = items;
        activeIndex = -1;
        autocomplete.innerHTML = '';

        if (!items.length) {
            hideAutocomplete();
            return;
        }

        items.forEach(function (sku) {
            const option = document.createElement('div');

            option.className = 'autocomplete-item';
            option.setAttribute('role', 'option');
            option.setAttribute('aria-selected', 'false');

            const skuText = document.createElement('span');
            skuText.className = 'autocomplete-sku';
            skuText.textContent = sku;

            const hint = document.createElement('span');
            hint.className = 'autocomplete-label';
            hint.textContent = 'Артикул за 30 дней';

            option.append(skuText, hint);

            option.addEventListener('mousedown', function (event) {
                event.preventDefault();
                goToSku(sku);
            });

            autocomplete.appendChild(option);
        });

        autocomplete.classList.add('show');
    }

    async function loadSuggestions(term) {
        if (requestController) requestController.abort();

        requestController = new AbortController();

        const url = new URL(suggestionsUrl, window.location.origin);
        url.searchParams.set('q', term);

        try {
            const response = await fetch(url.toString(), {
                headers: { 'Accept': 'application/json' },
                signal: requestController.signal
            });

            if (!response.ok) {
                console.error(
                    'Ошибка автокомплита:',
                    response.status,
                    await response.text()
                );

                hideAutocomplete();
                return;
            }

            const data = await response.json();

            if (search.value.trim() !== term) return;

            const normalized = term.toLocaleLowerCase();

            const items = Array.isArray(data)
                ? data.filter(value => typeof value === 'string')
                : [];

            items.sort((a, b) => {
                const aa = a.toLocaleLowerCase();
                const bb = b.toLocaleLowerCase();

                if ((aa === normalized) !== (bb === normalized)) {
                    return aa === normalized ? -1 : 1;
                }

                if (aa.startsWith(normalized) !== bb.startsWith(normalized)) {
                    return aa.startsWith(normalized) ? -1 : 1;
                }

                return a.localeCompare(b);
            });

            renderSuggestions(items.slice(0, 10));
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Ошибка автокомплита:', error);
                hideAutocomplete();
            }
        }
    }

    search.addEventListener('input', function () {
        const term = this.value.trim();

        updateClearButton();
        clearTimeout(debounceTimer);

        if (requestController) requestController.abort();

        hideAutocomplete();

        if (!/^\d{2,}[-/]?\w*$/i.test(term)) return;

        debounceTimer = setTimeout(() => loadSuggestions(term), 200);
    });

    search.addEventListener('keydown', function (event) {
        const options = Array.from(
            autocomplete.querySelectorAll('.autocomplete-item')
        );

        if (event.key === 'Enter') {
            event.preventDefault();

            if (
                autocomplete.classList.contains('show') &&
                activeIndex >= 0 &&
                suggestions[activeIndex]
            ) {
                goToSku(suggestions[activeIndex]);
                return;
            }

            hideAutocomplete();
            form.requestSubmit();
            return;
        }

        if (!options.length || !autocomplete.classList.contains('show')) {
            if (event.key === 'Escape') hideAutocomplete();
            return;
        }

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            activeIndex = (activeIndex + 1) % options.length;
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            activeIndex = (activeIndex - 1 + options.length) % options.length;
        } else if (event.key === 'Escape') {
            hideAutocomplete();
            return;
        } else {
            return;
        }

        options.forEach((option, index) => {
            const active = index === activeIndex;

            option.classList.toggle('active', active);
            option.setAttribute('aria-selected', active ? 'true' : 'false');
        });
    });

    clearButton.addEventListener('click', function () {
        const url = new URL(window.location.href);

        url.searchParams.delete('q');
        url.searchParams.delete('sku');
        url.searchParams.set('date', selectedDate);

        window.location.href = url.toString();
    });

    document.addEventListener('click', function (event) {
        if (!event.target.closest('.search-wrapper')) {
            hideAutocomplete();
        }
    });

    updateClearButton();
});
</script>
@endsection
```