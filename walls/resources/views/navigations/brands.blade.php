@extends('layouts.main')

@section('content')

<style>
    /* Заголовок */
    .brands-header {
        text-align: center;
        font-size: 45px;
        font-weight: 300;
        margin: 40px 0;
    }

    /* Блок бренда */
    .brand-item {
        width: 100%;
        display: flex;
        justify-content: center;
        padding: 60px 0;
    }

    .brand-item:nth-child(odd) {
        background: #ffffff;
    }

    .brand-item:nth-child(even) {
        background: #f7f7f7;
    }

    .brand-content-wrapper {
        max-width: 1000px;
        width: 100%;
        padding: 0 15px;
        margin: 0 auto;
    }

    .brand-top {
        display: flex;
        gap: 100px;
        align-items: flex-start;
    }

    .brand-logo {
        width: 230px;
        height: auto;
        object-fit: contain;
        flex-shrink: 0;
    }

    .brand-content {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .brand-content p {
        margin: 0 0 15px;
        line-height: 1.5;
        font-size: 14px;
    }

    .brand-content .all-products {
        font-weight: 600;
        margin-top: 20px;
        text-align: left;
    }

    .brand-content .all-products a {
        text-decoration: none;
        color: #01142f;
        font-size: 14px;
    }

    .brand-content .all-products a:hover {
        text-decoration: underline;
    }

    .brand-products {
        display: flex;
        flex-wrap: nowrap;
        justify-content: center;
        gap: 20px;
        padding-top: 40px;
    }

    .brand-products img {
        width: 400px;
        height: 250px;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .brand-products img:hover {
        transform: scale(1.01);
    }

    /* Планшеты (769px - 1200px) */
    @media (max-width: 1200px) and (min-width: 769px) {
        .brand-content-wrapper {
            width: 90%;
            padding: 0 15px;
            margin: 0 auto;
        }

        .brand-top {
            gap: 50px;
        }

        .brand-logo {
            width: 200px;
        }

        .brand-products {
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 15px;
            padding-top: 30px;
        }

        .brand-products img {
            width: calc(50% - 7.5px);
            height: 220px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }
    }

    /* Мобильные устройства (до 768px) */
    @media (max-width: 768px) {
        .brand-content-wrapper {
            width: 95%;
            padding: 0 15px;
        }

        .brand-top {
            flex-direction: column;
            align-items: flex-start;
            text-align: left;
            gap: 15px;
        }

        .brand-logo {
            width: 140px;
        }

        .brand-products {
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 10px;
            padding-top: 20px;
        }

        .brand-products img {
            width: calc(50% - 5px);
            height: 140px;
            /* меньше на телефонах */
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        .brand-content .all-products {
            margin-top: 10px;
            margin-bottom: 10px;
        }
    }

    /* Очень маленькие экраны (до 480px) */
    @media (max-width: 480px) {
        .brand-logo {
            width: 100px;
        }

        .brand-products {
            justify-content: space-between;
            /* чтобы картинки не растягивались на всю ширину */
        }

        .brand-products img {
            width: calc(50% - 5px);
            /* две картинки в ряд с небольшим промежутком */
            height: 130px;
            /* можно регулировать */
            margin-bottom: 10px;
            box-sizing: border-box;
        }
    }
</style>


<div class="brands-header">Бренды</div>

{{-- Далее идут блоки брендов, как у тебя в коде, без изменений --}}
<div class="brand-item">
    <div class="brand-content-wrapper">
        <div class="brand-top">
            <img src="{{ asset('images/arteks.png') }}" alt="Бренд 1" class="brand-logo">
            <div class="brand-content">
                <p>Обои Артекс призваны удовлетворить эстетический запрос любой, даже самой взыскательной аудитории, сохраняя баланс между неувядающей классикой и актуальными трендами. Специалисты фабрики активно изучают новейшие течения в дизайне, что позволяет создавать неповторимые образы в самых разных интерьерах.</p>
                <p>В линейку бренда входят обои в технике трафаретной и глубокой печати с тиснением в регистр. Оптимальное соотношение цены и качества нашей продукции отражено в форматах обоев 0,53×10, 1,06×10, 25×1,06 м. Бренд Артекс имеет широкую географию распространения и известен как на территории России, так и в большинстве стран СНГ.</p>
                <div class="all-products">
                    <a href="/catalog?brand=Артекс">Весь ассортимент</a>
                </div>
            </div>
        </div>
        <div class="brand-products">
            <img src="{{ asset('images/arteksimg.jpg') }}" alt="Товар 1">
            <img src="{{ asset('images/arteksimg2.jpg') }}" alt="Товар 2">
            <img src="{{ asset('images/arteksimg3.jpg') }}" alt="Товар 3">
            <img src="{{ asset('images/arteksimg4.jpg') }}" alt="Товар 4">
        </div>
    </div>
</div>

<div class="brand-item">
    <div class="brand-content-wrapper">
        <div class="brand-top">
            <img src="{{ asset('images/ovkdesign.png') }}" alt="Бренд 1" class="brand-logo">
            <div class="brand-content">
                <p>Коллекции ОВК дизайн — это уважение к качеству и тонкое чувство стиля, выраженное в полной свободе выбора для каждого клиента. Изысканная благородная классика, экспрессивная смелая геометрия, трогательные детские сюжеты и многое другое ежедневно создается, чтобы сделать каждый интерьер уникальным и неповторимым.</p>
                <p>Бренд ОВК Дизайн имеет широкую географию распространения и известен как на территории России, так и в большинстве стран СНГ. В линейку бренда входят обои формата 1,06×10м выполненные в технике глубокой печати с тиснением в регистр.</p>
                <div class="all-products">
                    <a href="/catalog?brand=OVK Design">Весь ассортимент</a>
                </div>
            </div>
        </div>
        <div class="brand-products">
            <img src="{{ asset('images/ovkdesignimg.jpg') }}" alt="Товар 1">
            <img src="{{ asset('images/ovkdesignimg2.jpg') }}" alt="Товар 2">
            <img src="{{ asset('images/ovkdesignimg3.jpg') }}" alt="Товар 3">
            <img src="{{ asset('images/ovkdesignimg4.jpg') }}" alt="Товар 4">
        </div>
    </div>
</div>

<div class="brand-item">
    <div class="brand-content-wrapper">
        <div class="brand-top">
            <img src="{{ asset('images/ecoline.png') }}" alt="Бренд 1" class="brand-logo">
            <div class="brand-content">
                <p>
                    Идею бренда Эколайн можно выразить в трех словах: простота, доступность и качество. В производстве коллекций под брендом Эколайн мы используем технологии, позволяющие добиться удешевления продукции, при этом сохраняя оптимальный внешний вид обоев. Эколайн — бренд для тех, кому близка прагматичность и интеллигентная скромность в дизайне.</p>
                <p>В линейку бренда входят обои в технике трафаретной и глубокой печати с тиснением в регистр. Оптимальное соотношение цены и качества нашей продукции отражено в форматах обоев 0,53×10, 1,06×10, 25×1,06 м.</p>
                <div class="all-products">
                    <a href="/catalog?brand=Ecoline">Весь ассортимент</a>
                </div>
            </div>
        </div>
        <div class="brand-products">
            <img src="{{ asset('images/ecolineimg.jpg') }}" alt="Товар 1">
            <img src="{{ asset('images/ecolineimg2.jpg') }}" alt="Товар 2">
            <img src="{{ asset('images/ecolineimg3.jpg') }}" alt="Товар 3">
            <img src="{{ asset('images/ecolineimg4.jpg') }}" alt="Товар 4">
        </div>
    </div>
</div>

<div class="brand-item">
    <div class="brand-content-wrapper">
        <div class="brand-top">
            <img src="{{ asset('images/freedom.png') }}" alt="Бренд 1" class="brand-logo">
            <div class="brand-content">
                <p>Приятно создавать коллекции совместно с нашими любимыми партнерами, настоящими профессионалами обойного рынка. Представляя на рынке коллекции под брендом Фридом, мы заручились поддержкой опытных специалистов в области продаж, которые тонко чувствуют и понимают рынок, следят за новыми тенденциями и прислушиваются к мнению своих покупателей.</p>
                <p>Коллекции Фридом необычайно красивы и технически сложны. Разнообразие стилей, в котором преобладают классические мотивы, делает продукт неизменно востребованным на рынке.</p>
                <p>Бренд Фридом имеет широкую географию распространения и известен как на территории России, так и в большинстве стран СНГ. В линейку бренда входят обои формата 1,06×10 м, выполненные в технике глубокой печати с тиснением в регистр.</p>
                <div class="all-products">
                    <a href="/catalog?brand=FREEDOM">Весь ассортимент</a>
                </div>
            </div>
        </div>
        <div class="brand-products">
            <img src="{{ asset('images/freedomimg.jpg') }}" alt="Товар 1">
            <img src="{{ asset('images/freedomimg2.jpg') }}" alt="Товар 2">
            <img src="{{ asset('images/freedomimg3.jpg') }}" alt="Товар 3">
            <img src="{{ asset('images/freedomimg4.jpg') }}" alt="Товар 4">
        </div>
    </div>
</div>

<div class="brand-item">
    <div class="brand-content-wrapper">
        <div class="brand-top">
            <img src="{{ asset('images/domingo.png') }}" alt="Бренд 1" class="brand-logo">
            <div class="brand-content">
                <div class="all-products">
                    <a href="/catalog?brand=Domingo">Весь ассортимент</a>
                </div>
            </div>
        </div>
        <div class="brand-products">
            <img src="{{ asset('images/domingoimg.jpg') }}" alt="Товар 1">
            <img src="{{ asset('images/domingoimg2.jpg') }}" alt="Товар 2">
            <img src="{{ asset('images/domingoimg3.jpg') }}" alt="Товар 3">
            <img src="{{ asset('images/domingoimg4.jpg') }}" alt="Товар 4">
        </div>
    </div>
</div>

@include('partials.footer')
@endsection