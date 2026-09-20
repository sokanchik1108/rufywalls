<table>

    <thead>

    <tr>

        {{-- АРТИКУЛ --}}
        <th>
            Артикул
        </th>


        {{-- ПРОДАНО --}}
        <th
            class="sortable"
            data-sort="quantity"
            title="Сортировать"
        >
            <span class="sortable-content">
                Продано

                <span class="sort-arrow {{ $sortBy === 'quantity' ? '' : 'inactive' }}">
                    @if($sortBy === 'quantity')
                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                    @else
                        ↕
                    @endif
                </span>
            </span>
        </th>


        {{-- СРЕДНЯЯ ЦЕНА ПРОДАЖИ --}}
        <th
            class="sortable"
            data-sort="average_sale_price"
            title="Сортировать"
        >
            <span class="sortable-content">
                Средняя цена продажи

                <span class="sort-arrow {{ $sortBy === 'average_sale_price' ? '' : 'inactive' }}">
                    @if($sortBy === 'average_sale_price')
                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                    @else
                        ↕
                    @endif
                </span>
            </span>
        </th>


        {{-- СРЕДНЯЯ СЕБЕСТОИМОСТЬ --}}
        <th
            class="sortable"
            data-sort="average_cost"
            title="Сортировать"
        >
            <span class="sortable-content">
                Средняя себестоимость

                <span class="sort-arrow {{ $sortBy === 'average_cost' ? '' : 'inactive' }}">
                    @if($sortBy === 'average_cost')
                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                    @else
                        ↕
                    @endif
                </span>
            </span>
        </th>


        {{-- СУММА ПРОДАЖ --}}
        <th
            class="sortable"
            data-sort="sales"
            title="Сортировать"
        >
            <span class="sortable-content">
                Сумма продаж

                <span class="sort-arrow {{ $sortBy === 'sales' ? '' : 'inactive' }}">
                    @if($sortBy === 'sales')
                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                    @else
                        ↕
                    @endif
                </span>
            </span>
        </th>


        {{-- СЕБЕСТОИМОСТЬ --}}
        <th
            class="sortable"
            data-sort="cost"
            title="Сортировать"
        >
            <span class="sortable-content">
                Себестоимость

                <span class="sort-arrow {{ $sortBy === 'cost' ? '' : 'inactive' }}">
                    @if($sortBy === 'cost')
                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                    @else
                        ↕
                    @endif
                </span>
            </span>
        </th>


        {{-- ПРИБЫЛЬ --}}
        <th
            class="sortable"
            data-sort="profit"
            title="Сортировать"
        >
            <span class="sortable-content">
                Прибыль

                <span class="sort-arrow {{ $sortBy === 'profit' ? '' : 'inactive' }}">
                    @if($sortBy === 'profit')
                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                    @else
                        ↕
                    @endif
                </span>
            </span>
        </th>

    </tr>

    </thead>


    <tbody>

    @forelse($products as $product)

        <tr>

            {{-- АРТИКУЛ --}}
            <td>

                <a
                    href="{{ route(
                        'admin.analytics.sales.product',
                        [
                            'sku' => $product['sku'],
                            'from' => request('from'),
                            'to' => request('to'),
                            'point_of_sale_id' => request('point_of_sale_id'),
                        ]
                    ) }}"
                    class="sku-link"
                >
                    {{ $product['sku'] }}
                </a>

            </td>


            {{-- ПРОДАНО --}}
            <td class="number">

                {{ number_format(
                    $product['quantity'],
                    0,
                    ',',
                    ' '
                ) }}

            </td>


            {{-- СРЕДНЯЯ ЦЕНА --}}
            <td class="number">

                {{ number_format(
                    $product['average_sale_price'],
                    2,
                    ',',
                    ' '
                ) }}

                ₸

            </td>


            {{-- СРЕДНЯЯ СЕБЕСТОИМОСТЬ --}}
            <td class="number">

                {{ number_format(
                    $product['average_cost'],
                    2,
                    ',',
                    ' '
                ) }}

                ₸

            </td>


            {{-- СУММА ПРОДАЖ --}}
            <td class="number">

                {{ number_format(
                    $product['sales'],
                    2,
                    ',',
                    ' '
                ) }}

                ₸

            </td>


            {{-- СЕБЕСТОИМОСТЬ --}}
            <td class="number">

                {{ number_format(
                    $product['cost'],
                    2,
                    ',',
                    ' '
                ) }}

                ₸

            </td>


            {{-- ПРИБЫЛЬ --}}
            <td
                class="number {{ $product['profit'] >= 0
                    ? 'positive'
                    : 'negative' }}"
            >

                {{ number_format(
                    $product['profit'],
                    2,
                    ',',
                    ' '
                ) }}

                ₸

            </td>

        </tr>

    @empty

        <tr>

            <td
                colspan="7"
                class="empty"
            >
                Продаж за выбранный период нет
            </td>

        </tr>

    @endforelse

    </tbody>

</table>

