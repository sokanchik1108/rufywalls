<footer class="footer text-dark py-5">
  <div class="container">
    <div class="row">

      <!-- Бренд -->
      <div class="col-md-4 mb-4">
        <h5 class="footer-logo">RAFY WALLS</h5>
        <div class="footer-description">
          <p class="small mb-2">Премиум-обои для тех, кто ценит стиль и качество. Мы предлагаем широкий ассортимент высококачественных обоев, которые подойдут для любого интерьера.</p>
          <p class="small ">Наши коллекции созданы с вниманием к деталям, чтобы добавить вашему дому элегантности и уюта.</p>
        </div>
      </div>

      <!-- Навигация -->
      <div class="col-md-4 mb-4">
        <h6 class="footer-heading">Навигация</h6>
        <ul class="list-unstyled">
          <li><a href="/" class="footer-link">Главная</a></li>
          <li><a href="/delivery" class="footer-link">Доставка</a></li>
          <li><a href="/catalog" class="footer-link">Каталог</a></li>
          <li><a href="/about-products" class="footer-link">О продукции</a></li>
          <li><a href="/address" class="footer-link">Контакты</a></li>
          <li><a href="{{ route('how-to-order') }}" class="footer-link">Как оформить заказ?</a></li>
          <li><a href="{{ route('calculator') }}" class="footer-link">Калькулятор</a></li>
        </ul>
      </div>

      <!-- Контакты -->
      <div class="col-md-4 mb-4">
        <h6 class="footer-heading">Контакты</h6>
        <p class="mb-2 small">+7 707 712 1255</p>
        <p class="mb-2 small">Рыскулова / Розыбакиева, рынок Сауран, здание №109, Алматы</p>
        <p class="mb-2 small">Рыскулова / Розыбакиева, рынок Сауран, здание №105, Алматы</p>

        <h6 class="footer-heading">Мы в соц. сетях</h6>
        <!-- Соцсети -->
        <div class="social-icons d-flex gap-3 mt-1">
          <a href="https://wa.me/77077121255" target="_blank" aria-label="WhatsApp">
            <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/whatsapp.svg" width="24" height="24" alt="WhatsApp">
          </a>
          <a href="https://instagram.com/rafy_walls" target="_blank" aria-label="Instagram">
            <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/instagram.svg" width="24" height="24" alt="Instagram">
          </a>
        </div>
      </div>
    </div>

    <div class="text-center mt-4 small text-muted">
      © {{ date('Y') }} Rafy Walls — Все права защищены
    </div>
  </div>

  <!-- Кнопка наверх -->
  <button id="scrollToTop" class="scroll-top-btn" aria-label="Наверх">↑</button>
</footer>

<style>
  .footer {
    background-color: #e8e8e8;
    /* слегка темнее светло-серого */
    color: #333;
    font-size: 0.95rem;
    position: relative;
  }

  .container {
    margin: 0 auto;
    padding-left: 15px;
    /* Уменьшение отступов слева */
    padding-right: 15px;
    /* Уменьшение отступов справа */
  }

  .footer-logo {
    font-family: 'Playfair Display', serif;
    font-weight: 600;
    font-size: 1.9rem;
    color: #01142f;
    letter-spacing: 2px;
  }

  .footer-description {
    max-width: 80%;
    /* Убираем ограничение ширины */
    margin-top: 10px;
  }

  .footer-heading {
    font-weight: 600;
    margin-bottom: 12px;
    font-size: 1rem;
    color: #222;
  }

  .footer-link {
    color: black;
    text-decoration: none;
    display: block;
    margin-bottom: 6px;
    transition: color 0.3s ease;
  }

  .footer-link:hover {
    color: #000;
    text-decoration: underline;
  }

  .social-icons img {
    filter: grayscale(100%) brightness(0.6);
    transition: 0.3s ease;
    cursor: pointer;
  }

  .social-icons img:hover {
    filter: none;
    transform: scale(1.1);
  }

  .scroll-top-btn {
    display: none;
    /* изначально скрыта */
    position: fixed;
    bottom: 30px;
    right: 30px;
    background-color: #01142f;
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 42px;
    height: 42px;
    font-size: 20px;
    cursor: pointer;
    z-index: 1000;

    display: flex;
    /* оставь flex только при показе через JS */
    align-items: center;
    justify-content: center;

    transition: background 0.3s ease, transform 0.3s ease;

    margin: 0;
    padding: 0;
  }


  @media (max-width: 576px) {
    .scroll-top-btn {
      right: 15px;
      /* ближе к краю экрана на мобилках */
      bottom: 20px;
    }
  }


  .scroll-top-btn:hover {
    background-color: #02214b;
    transform: scale(1.1);
  }
</style>

<script>
  const btn = document.getElementById('scrollToTop');

  function toggleScrollBtn() {
    if (window.scrollY > 300) {
      btn.style.display = 'flex';
    } else {
      btn.style.display = 'none';
    }
  }

  // проверка сразу при загрузке страницы
  toggleScrollBtn();

  // и на скролле
  window.addEventListener('scroll', toggleScrollBtn);

  btn.addEventListener('click', function() {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });
</script>