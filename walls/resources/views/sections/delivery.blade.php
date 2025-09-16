<section class="pickup-section py-5 bg-light" id="delivery">
    <div class="container px-3 px-md-4" style="max-width: 900px;">
        <h2 class="pickup-title mb-4">Получите заказ удобным способом</h2>

        <div class="pickup-intro mb-4">
            <p>Мы предлагаем два простых и быстрых варианта получения — выберите тот, что подходит именно вам.</p>
        </div>

        <div class="pickup-grid">
            <!-- Самовывоз -->
            <div class="pickup-card">
                <h3>Самовывоз</h3>

                <p class="pickup-subtext">Вы можете забрать заказ лично в одном из наших пунктов — сразу и без ожидания.</p>

                <div class="pickup-info">
                    <p><strong>Адрес:</strong> Рыскулова Розыбакиева, рынок Сауран, новое здание №109, Казахстан, Алматы</p>
                </div>

                <div class="pickup-info">
                    <p><strong>Адрес:</strong> Рыскулова Розыбакиева, рынок Сауран, новое здание №105, Казахстан, Алматы</p>
                </div>

                <p class="mt-3"><strong>График работы:</strong><br>Вт–вс, с 8:00 до 17:00</p>
            </div>

            <!-- Доставка -->
            <div class="pickup-card">
                <h3>Доставка</h3>
                <div class="pickup-info">
                    <p>Мы доставляем заказы в любой город Казахстана удобным для вас способом — через транспортную компанию, курьерскую службу или такси по вашему выбору. Вы сами решаете, какой способ доставки использовать, так как мы не сотрудничаем с конкретными службами. Все расходы на доставку оплачиваются клиентом, включая доставку внутри города и междугородние отправки. Подробности уточняйте у нашего менеджера перед оформлением заказа.</p>
                </div>
            </div>
        </div>

        <p class="pickup-help mt-5 text-muted text-center">Остались вопросы? Мы с радостью ответим 💬</p>
    </div>
</section>

<style>
    .pickup-section {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #1e1e1e;
    }

    .pickup-title {
        font-size: clamp(1.8rem, 2.8vw, 2.4rem);
        font-weight: 700;
        text-align: center;
        letter-spacing: -0.5px;
        margin-bottom: 2.5rem;
    }

    .pickup-intro p {
        font-size: clamp(1rem, 1.5vw, 1.1rem);
        text-align: center;
        color: #555;
        max-width: 600px;
        margin: 0 auto;
    }

    .pickup-grid {
        display: grid;
        gap: 2rem;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        margin-top: 3.5rem;
    }

    .pickup-card {
        position: relative;
        background: #fff;
        border-radius: 20px;
        padding: 2.5rem 2rem;
        text-align: left;
        box-shadow: 0 4px 18px rgba(1, 20, 47, 0.06);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }

    .pickup-card h3 {
        color: #01142f;
        font-size: 1.2rem;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 1.2rem;
    }

    .pickup-card .pickup-subtext {
        color: #444;
        font-size: 1rem;
        margin-bottom: 1.2rem;
    }

    .pickup-card .pickup-info {
        font-size: 0.95rem;
        color: #333;
        margin-bottom: 0.8rem;
    }

    /* Анимированный градиент при наведении */
    .pickup-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 5px;
        background: linear-gradient(90deg, #01142f, #27406e, #01142f);
        background-size: 200% 100%;
        transition: left 0.4s ease, background-position 0.6s ease;
    }

    .pickup-card:hover::before {
        left: 0;
        background-position: 100% 0;
    }

    .pickup-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 30px rgba(1, 20, 47, 0.15);
    }

    .pickup-help {
        font-size: 0.95rem;
    }
</style>