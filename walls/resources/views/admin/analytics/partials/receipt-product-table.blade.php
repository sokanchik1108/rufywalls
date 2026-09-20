<div class="table-wrapper">

    <table class="products-table">

        <thead>

            <tr>

                <th>
                    #
                </th>


                <th>

                    <div class="th-sort">

                        <span>
                            Артикул
                        </span>

                        <div class="sort-arrows">

                            <button
                                type="button"
                                class="sort-arrow
                                    {{ $sortBy === 'sku' && $sortDirection === 'asc'
                                        ? 'active'
                                        : '' }}"
                                data-sort="sku"
                                data-direction="asc"
                                title="От меньшего к большему"
                            >
                                ▲
                            </button>

                            <button
                                type="button"
                                class="sort-arrow
                                    {{ $sortBy === 'sku' && $sortDirection === 'desc'
                                        ? 'active'
                                        : '' }}"
                                data-sort="sku"
                                data-direction="desc"
                                title="От большего к меньшему"
                            >
                                ▼
                            </button>

                        </div>

                    </div>

                </th>


                <th>
                    Товар
                </th>


                <th>

                    <div class="th-sort">

                        <span>
                            Приёмок
                        </span>

                        <div class="sort-arrows">

                            <button
                                type="button"
                                class="sort-arrow
                                    {{ $sortBy === 'receipts' && $sortDirection === 'asc'
                                        ? 'active'
                                        : '' }}"
                                data-sort="receipts"
                                data-direction="asc"
                                title="Меньше → больше"
                            >
                                ▲
                            </button>

                            <button
                                type="button"
                                class="sort-arrow
                                    {{ $sortBy === 'receipts' && $sortDirection === 'desc'
                                        ? 'active'
                                        : '' }}"
                                data-sort="receipts"
                                data-direction="desc"
                                title="Больше → меньше"
                            >
                                ▼
                            </button>

                        </div>

                    </div>

                </th>


                <th>

                    <div class="th-sort">

                        <span>
                            Завезено
                        </span>

                        <div class="sort-arrows">

                            <button
                                type="button"
                                class="sort-arrow
                                    {{ $sortBy === 'quantity' && $sortDirection === 'asc'
                                        ? 'active'
                                        : '' }}"
                                data-sort="quantity"
                                data-direction="asc"
                                title="Меньше → больше"
                            >
                                ▲
                            </button>

                            <button
                                type="button"
                                class="sort-arrow
                                    {{ $sortBy === 'quantity' && $sortDirection === 'desc'
                                        ? 'active'
                                        : '' }}"
                                data-sort="quantity"
                                data-direction="desc"
                                title="Больше → меньше"
                            >
                                ▼
                            </button>

                        </div>

                    </div>

                </th>


                <th>

                    <div class="th-sort">

                        <span>
                            Средний завоз
                        </span>

                        <div class="sort-arrows">

                            <button
                                type="button"
                                class="sort-arrow
                                    {{ $sortBy === 'average_quantity' && $sortDirection === 'asc'
                                        ? 'active'
                                        : '' }}"
                                data-sort="average_quantity"
                                data-direction="asc"
                                title="Меньше → больше"
                            >
                                ▲
                            </button>

                            <button
                                type="button"
                                class="sort-arrow
                                    {{ $sortBy === 'average_quantity' && $sortDirection === 'desc'
                                        ? 'active'
                                        : '' }}"
                                data-sort="average_quantity"
                                data-direction="desc"
                                title="Больше → меньше"
                            >
                                ▼
                            </button>

                        </div>

                    </div>

                </th>

            </tr>

        </thead>


        <tbody>

            @forelse(
                $allProductStats
                as $index => $product
            )

                <tr>

                    <td>
                        {{ $index + 1 }}
                    </td>


                    <td>

                        <a
                            href="{{ route(
                                'admin.analytics.receipts.product',
                                array_merge(
                                    request()->query(),
                                    [
                                        'variant_id' =>
                                            $product['variant_id']
                                    ]
                                )
                            ) }}"
                            class="sku-link"
                        >
                            {{ $product['sku'] }}
                        </a>

                    </td>


                    <td>
                        {{ $product['product'] }}
                    </td>


                    <td>
                        {{ $product['receipts'] }}
                    </td>


                    <td>
                        {{ number_format(
                            $product['quantity'],
                            0,
                            ',',
                            ' '
                        ) }}
                    </td>


                    <td>
                        {{ number_format(
                            $product['average_quantity'],
                            2,
                            ',',
                            ' '
                        ) }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="6"
                        class="empty-row"
                    >
                        Ничего не найдено
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

</div>