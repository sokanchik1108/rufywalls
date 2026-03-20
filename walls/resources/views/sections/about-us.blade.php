<section class="about-section" id="about-us" style="padding:3em 0; background-color:#fff8f0; overflow-x:hidden;">

  <div class="content-wrapper">
    <!-- ВИДИМЫЙ H1 для Google, но по стилю меньше H2 -->
    <h1 class="about-h1 mb-4">
      О компании <span class="company-name">RAFY WALLS</span>
    </h1>

    <div class="section-text">
      <p>
        <strong><span class="company-name inline">RAFY WALLS</span> — магазин премиальных обоев в Алматы с широким ассортиментом решений для современного интерьера.</strong>
        Мы помогаем создать стильное, уютное и гармоничное пространство для дома, офиса и коммерческих помещений.
      </p>

      <p>
        В каталоге <span class="company-name inline">RAFY WALLS</span> представлены флизелиновые и моющиеся обои
        от проверенных производителей, сочетающие высокое качество, актуальный дизайн и долговечность.
        Мы внимательно подходим к подбору коллекций и работаем только с надёжными брендами.
      </p>

      <p>
        Наша команда состоит из специалистов, которые помогают подобрать обои с учётом стиля интерьера,
        освещения и индивидуальных предпочтений клиента.
        Мы сопровождаем заказ на всех этапах — от выбора до оформления покупки.
      </p>

      <p>
        <strong><span class="company-name inline">RAFY WALLS</span></strong> обеспечивает удобный онлайн-заказ
        и доставку обоев по всему Казахстану, делая процесс покупки комфортным и доступным вне зависимости
        от города и времени.
      </p>
    </div>

    <div class="advantages-grid mt-5">
      <div class="advantage-item">
        <i class="bi bi-grid-1x2"></i>
        <div>
          <h5>Трендовые коллекции</h5>
          <p>Современные дизайны и актуальные интерьерные решения.</p>
        </div>
      </div>

      <div class="advantage-item">
        <i class="bi bi-chat-left-text"></i>
        <div>
          <h5>Профессиональная консультация</h5>
          <p>Помогаем подобрать обои под стиль и задачи интерьера.</p>
        </div>
      </div>

      <div class="advantage-item">
        <i class="bi bi-bag-check"></i>
        <div>
          <h5>Онлайн-оформление заказов</h5>
          <p>Удобное и быстрое оформление заказа через сайт.</p>
        </div>
      </div>

      <div class="advantage-item">
        <i class="bi bi-award"></i>
        <div>
          <h5>Качество и надёжность</h5>
          <p>Работаем только с проверенными брендами и материалами.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<style>
  .about-section {
    font-family: 'Poppins', sans-serif;
  }

  .content-wrapper {
    max-width: 1500px;
    margin: 0 auto;
    padding: 0 2rem;
    text-align: left;
    margin-bottom: 70px;
  }

  /* H1 теперь меньше по размеру, визуально аккуратнее */
  .about-h1 {
    font-size: 1.6rem;
    /* меньше, чем H2 раньше */
    font-weight: 700;
    color: #01142f;
    margin-bottom: 1.5rem;
    line-height: 1.3;
  }

  .section-title {
    display: none;
    /* Скрываем прежний H2, чтобы не дублировался */
  }

  /* Заголовок RAFY WALLS */
  .company-name {
    font-family: 'Playfair Display', serif;
    font-weight: 600;
    font-size: 1.9rem;
    letter-spacing: 2px;
  }

  /* Встроенные RAFY WALLS в тексте */
  .company-name.inline {
    font-family: 'Playfair Display', serif;
    font-weight: 600;
    font-size: 17px;
    letter-spacing: 0.5px;
  }

  .section-text {
    font-size: 1.1rem;
    line-height: 1.7;
    color: #333;
    max-width: 1500px;
  }

  /* Сетка преимуществ */
  .advantages-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-top: 3rem;
    justify-items: center;
    text-align: center;
  }

  .advantage-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    max-width: 280px;
  }

  .advantage-item i {
    font-size: 1.9rem;
    color: #01142f;
    margin-bottom: 0.5rem;
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
    line-height: 1.5;
    margin: 0;
  }

  /* 📱 Адаптив */
  @media (max-width: 991.98px) {
    .content-wrapper {
      padding: 0 1.2rem;
    }

    .section-text {
      font-size: 1rem;
    }
  }

  @media (max-width: 767.98px) {
    .content-wrapper {
      padding: 0 1rem;
    }

    .about-h1 {
      font-size: 1.4rem;
    }

    .company-name {
      font-size: 1.5rem;
    }
  }

  @media (max-width: 480px) {
    .content-wrapper {
      padding: 0 0.8rem;
    }

    .about-h1 {
      font-size: 1.2rem;
    }

    .section-text {
      font-size: 0.95rem;
    }
  }
</style>