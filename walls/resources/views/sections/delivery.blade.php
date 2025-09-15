<section class="pickup-section py-5" id="delivery">
    <div class="container px-3 px-md-4" style="max-width: 900px;">
        <h2 class="pickup-title mb-5">Самовывоз или доставка</h2>

        <div class="pickup-intro mb-5">
            <p>Вы можете выбрать один из двух удобных способов получения заказа:</p>
        </div>

        <div class="pickup-grid">
            <!-- Самовывоз -->
            <div class="pickup-card">
                <h3 style="color: #01142f;">Самовывоз</h3>

                <p class="pickup-subtext">Вы можете забрать заказ лично в одном из наших пунктов — сразу и без ожидания.</p>

                <!-- Первый адрес -->
                <div class="pickup-info">
                    <p><strong>Адрес:</strong> Рыскулова Розыбакиева, рынок Сауран, новое здание №109, Казахстан, Алматы</p>
                </div>

                <!-- Второй адрес -->
                <div class="pickup-info">
                    <p><strong>Адрес:</strong> Рыскулова Розыбакиева, рынок Сауран, новое здание №105, Казахстан, Алматы</p>
                </div>
                <p class="mt-3"><strong>График работы:</strong><br>Вт–вс, с 8:00 до 17:00</p>
            </div>


            <!-- Доставка -->
            <div class="pickup-card">
                <h3 style="color: #01142f;">Доставка</h3>
                <p class="pickup-subtext">Мы организуем доставку заказа до вашей двери.</p>
                <div class="pickup-info">
                    <p>Оплата доставки производится напрямую курьеру.
                        Это позволяет сохранять честные цены и гибкость в выборе способа получения.</p>
                </div>
            </div>
        </div>

        <p class="pickup-help mt-5 text-muted text-center">Если остались вопросы — мы с радостью ответим.</p>
    </div>
</section>

<style>
    .pickup-section {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #222;
        background-color: #fff;
    }

    .pickup-title {
        font-size: 2.2rem;
        font-weight: 600;
        text-align: center;
        letter-spacing: -0.5px;
    }

    .pickup-intro p {
        font-size: 1.1rem;
        text-align: center;
        color: #444;
    }

    .pickup-grid {
        display: grid;
        gap: 2rem;
        grid-template-columns: 1fr;
        margin-top: 3rem;
    }

    .pickup-card {
        background: #fafafa;
        border: 1px solid #eee;
        border-radius: 14px;
        padding: 2rem;
        text-align: center;
        transition: box-shadow 0.3s ease, transform 0.2s ease;
    }

    .pickup-card:hover {
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
        transform: translateY(-2px);
    }

    .pickup-card h3 {
        font-size: 1.1rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #555;
        margin-bottom: 0.8rem;
    }

    .pickup-subtext {
        font-size: 1rem;
        color: #666;
        margin-bottom: 1.2rem;
    }

    .pickup-info p {
        font-size: 1rem;
        line-height: 1.6;
        color: #333;
        margin-bottom: 0.8rem;
    }

    .pickup-help {
        font-size: 0.95rem;
        color: #777;
    }

    /* Планшеты и выше */
    @media (min-width: 768px) {
        .pickup-title {
            font-size: 2.4rem;
        }

        .pickup-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>