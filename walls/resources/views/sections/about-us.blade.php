<!-- СЕКЦИЯ "О КОМПАНИИ" -->
<section class="about-section" id="about-us" style="padding:3em 0; background-color:#fff8f0; overflow-x:hidden;">

  <div class="content-wrapper">
    <h2 class="section-title mb-4">
      О компании <span class="company-name">RAFY WALLS</span>
    </h2>

    <div class="section-text">
      <p>
        <strong><span class="company-name inline">RAFY WALLS</span> — это компания с самым широким выбором премиальных обоев для интерьера в Казахстане.</strong>
        Мы создаём пространство, в котором красиво, уютно и удобно, помогая каждому клиенту найти идеальный вариант для своего дома или офиса.
      </p>

      <p>
        Наша команда объединяет профессионалов, которые ценят качество, стиль и внимание к деталям.
        Мы гордимся тем, что можем предложить широкий ассортимент, вдохновение и индивидуальный подход каждому клиенту.
      </p>

      <p>
        <span class="company-name inline">RAFY WALLS</span> предоставляет круглосуточную поддержку и возможность сделать заказ дистанционно,
        чтобы нашим клиентам было удобно и комфортно сотрудничать с нами вне зависимости от времени и места.
      </p>

      <p>
        Мы делаем всё, чтобы каждый проект, каждая идея и каждая деталь были реализованы на высшем уровне.
        <strong><span class="company-name inline">RAFY WALLS</span> — это надёжность, профессионализм и огромный выбор, который позволяет воплотить любые интерьерные мечты.</strong>
      </p>
    </div>

    <div class="advantages-grid mt-5">
      <div class="advantage-item">
        <i class="bi bi-grid-1x2"></i>
        <div>
          <h5>Трендовые коллекции</h5>
          <p>Актуальные дизайны, вдохновлённые мировыми тенденциями.</p>
        </div>
      </div>

      <div class="advantage-item">
        <i class="bi bi-chat-left-text"></i>
        <div>
          <h5>Консультация специалистов</h5>
          <p>Помогаем подобрать обои и даём советы по интерьеру.</p>
        </div>
      </div>

      <div class="advantage-item">
        <i class="bi bi-bag-check"></i>
        <div>
          <h5>Онлайн-заказы</h5>
          <p>Удобное оформление заказа через интернет.</p>
        </div>
      </div>

      <div class="advantage-item">
        <i class="bi bi-award"></i>
        <div>
          <h5>Надёжность и качество</h5>
          <p>Мы работаем только с проверенными брендами и материалами.</p>
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

  .section-title {
    font-weight: 700;
    font-size: 2rem;
    color: #01142f;
    margin-bottom: 1.5rem;
  }

  /* ✅ Заголовок RAFY WALLS */
  .company-name {
    font-family: 'Playfair Display', serif;
    font-weight: 600;
    font-size: 1.9rem;
    letter-spacing: 2px;
  }

  /* ✅ Встроенные RAFY WALLS в тексте (тот же шрифт, но старый размер текста) */
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
    .section-title {
      font-size: 1.6rem;
    }
    .company-name {
      font-size: 1.5rem;
    }
  }

  @media (max-width: 480px) {
    .content-wrapper {
      padding: 0 0.8rem;
    }
    .section-title {
      font-size: 1.4rem;
    }
    .section-text {
      font-size: 0.95rem;
    }
  }
</style>
