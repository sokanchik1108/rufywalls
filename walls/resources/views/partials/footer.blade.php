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

        <!-- Соцсети -->
        <div class="social-icons d-flex gap-3 mt-3">
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
    background-color: #d1cfcf; /* Более темный и серый фон */
    color: #333;
    font-size: 1rem; /* Увеличили с 0.95rem до 1rem */
    position: relative;
  }

  .container {
    margin: 0 auto;
    padding-left: 15px;
    padding-right: 15px;
  }

  .footer-logo {
    font-size: 1.85rem; /* Слегка увеличили с 1.7rem */
    font-weight: 700;
    color: #111;
  }

  .footer-description {
    max-width: 80%;
    margin-top: 12px; /* чуть больше отступ сверху */
  }

  .footer-description p {
    font-size: 1rem; /* Увеличили с 0.95rem */
    line-height: 1.6;
  }

  .footer-heading {
    font-weight: 600;
    margin-bottom: 14px; /* чуть больше отступа */
    font-size: 1.1rem; /* Увеличили с 1rem */
    color: #222;
  }

  .footer-link {
    color: black;
    text-decoration: none;
    display: block;
    margin-bottom: 8px; /* чуть больше */
    font-size: 1rem; /* увеличили размер ссылок */
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
    width: 28px; /* чуть больше иконки */
    height: 28px;
  }

  .social-icons img:hover {
    filter: none;
    transform: scale(1.15);
  }

  /* Кнопка наверх */
  .scroll-top-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background-color: #01142f;
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 44px; /* немного увеличили */
    height: 44px;
    font-size: 22px; /* немного увеличили */
    line-height: 44px;
    text-align: center;
    cursor: pointer;
    z-index: 1000;
    display: none;
    transition: background 0.3s ease, transform 0.3s ease;
  }

  .scroll-top-btn:hover {
    background-color: #02214b;
    transform: scale(1.15);
  }

  .text-center.small.text-muted {
    font-size: 0.95rem; /* немного больше для копирайта */
  }
</style>

<script>
  // Показывать кнопку при прокрутке
  window.addEventListener('scroll', function () {
    const btn = document.getElementById('scrollToTop');
    if (window.scrollY > 300) {
      btn.style.display = 'block';
    } else {
      btn.style.display = 'none';
    }
  });

  // Прокрутка наверх
  document.getElementById('scrollToTop').addEventListener('click', function () {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
</script>
