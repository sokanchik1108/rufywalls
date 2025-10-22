<section class="about-section" id="about-us" style="padding-bottom:6em; background-color:#f9f6f2;">

    <div class="container-fluid px-3" style="max-width: 1650px;">
        <div class="row align-items-stretch g-5">
            <!-- Левая часть -->
            <div class="col-12 col-lg-7 d-flex flex-column justify-content-center">
                <h2 class="section-title mb-4 text-start" style="font-weight:700; font-size:1.8rem;">
                    О компании <span class="company-name">RAFY WALLS</span>
                </h2>

                <div class="section-text" style="font-size:1.1rem; line-height:1.65;">
                    <p>
                        <strong>RAFY WALLS — это компания с самым широким выбором премиальных обоев для интерьера в Казахстане.</strong>
                        Мы создаём пространство, в котором красиво, уютно и удобно, помогая каждому клиенту найти идеальный вариант для своего дома или офиса.
                    </p>

                    <p>
                        Наша команда объединяет профессионалов, которые ценят качество, стиль и внимание к деталям.
                        Мы гордимся тем, что можем предложить <strong>широкий ассортимент, вдохновение и индивидуальный подход</strong> каждому клиенту.
                    </p>

                    <p>
                        RAFY WALLS предоставляет <strong>круглосуточную поддержку</strong> и возможность сделать заказ дистанционно, чтобы нашим клиентам было удобно и комфортно сотрудничать с нами вне зависимости от времени и места.
                    </p>

                    <p>
                        Мы делаем всё, чтобы каждый проект, каждая идея и каждая деталь были реализованы на высшем уровне.
                        RAFY WALLS — это надёжность, профессионализм и огромный выбор, который позволяет воплотить любые интерьерные мечты.
                    </p>
                </div>
            </div>

            <!-- Правая часть -->
            <div class="col-12 col-lg-5 d-flex flex-column justify-content-center">
                <div class="advantages-grid">
                    <div class="advantage-item">
                        <i class="bi bi-grid-1x2"></i>
                        <div>
                            <h5>Трендовые коллекции</h5>
                            <p>Всегда актуальные дизайны обоев, вдохновлённые мировыми интерьерными тенденциями.</p>
                        </div>
                    </div>

                    <div class="advantage-item">
                        <i class="bi bi-chat-left-text"></i>
                        <div>
                            <h5>Консультация специалистов</h5>
                            <p>Помогаем подобрать обои и даём рекомендации под ваш стиль и интерьер.</p>
                        </div>
                    </div>

                    <div class="advantage-item">
                        <i class="bi bi-bag-check"></i>
                        <div>
                            <h5>Онлайн-заказы</h5>
                            <p>Вы можете удобно выбирать и оформлять заказ через интернет.</p>
                        </div>
                    </div>

                    <div class="advantage-item">
                        <i class="bi bi-award"></i>
                        <div>
                            <h5>Надёжность и качество</h5>
                            <p>Мы выбираем только проверенные бренды и высококачественные материалы.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .company-name {
        font-family: 'Playfair Display', serif;
        font-weight: 600;
        font-size: 1.9rem;
        letter-spacing: 2px;
        color: #01142f;
    }

    /* --- Минималистичные преимущества --- */
    .advantages-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.8rem;
    }

    .advantage-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        border-bottom: 1px solid #e6e7ea;
        padding-bottom: 1.2rem;
    }

    .advantage-item:last-child {
        border-bottom: none;
    }

    .advantage-item i {
        font-size: 1.5rem;
        color: #01142f;
        margin-top: 0.1rem;
    }

    .advantage-item h5 {
        font-size: 1.05rem;
        font-weight: 600;
        color: #01142f;
        font-family: 'Playfair Display', serif;
        margin-bottom: 0.3rem;
    }

    .advantage-item p {
        font-size: 0.95rem;
        color: #555;
        margin: 0;
        line-height: 1.45;
    }

    /* --- Адаптив --- */
    @media (max-width: 991.98px) {
        .section-title {
            font-size: 1.6rem !important;
        }

        .company-name {
            font-size: 1.6rem !important;
        }

        .advantage-item i {
            font-size: 1.35rem;
        }

        .advantage-item h5 {
            font-size: 1rem;
        }

        .advantages-grid {
            gap: 1.5rem;
        }
    }
</style>