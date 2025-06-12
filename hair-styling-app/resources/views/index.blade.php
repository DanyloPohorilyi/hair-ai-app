@extends('layouts.app')

@section('content')
    <!DOCTYPE html>
    <html lang="uk">

    <head>
        <meta charset="UTF-8">
        <title>HairMorph — Головна</title>

        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

        <style>
            :root {
                --color-bg: #0F1235;
                --color-primary: #CFCFD3;
                --color-accent: #8F90FF;
                --font-family: 'Montserrat', sans-serif;
            }

            body {
                background-color: var(--color-bg);
                color: var(--color-primary);
                font-family: var(--font-family);
                margin: 0;
                padding: 0;
            }

            .banner {
                position: relative;
                width: 100%;
                height: 85vh;
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                overflow: hidden;
            }

            .banner video {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 100%;
                height: 100%;
                object-fit: cover;
                z-index: -1;
            }

            .banner-overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(15, 18, 53, 0.7);
                z-index: 0;
            }

            .banner-content {
                position: relative;
                z-index: 1;
                color: var(--color-primary);
            }

            .banner h1 {
                font-size: 4rem;
                font-weight: 600;
                margin-bottom: 1rem;
            }

            .banner p {
                font-size: 1.5rem;
                max-width: 700px;
                line-height: 1.6;
                margin-bottom: 2.5rem;
            }

            .btn-cta {
                background-color: var(--color-accent);
                color: #FFF;
                border: none;
                padding: 1rem 2.5rem;
                border-radius: 30px;
                font-weight: 600;
                font-size: 1.2rem;
                text-decoration: none;
                transition: background-color 0.3s ease;
            }

            .btn-cta:hover {
                background-color: #7f80e0;
            }

            .section {
                padding: 4rem 2rem;
                text-align: center;
            }

            .section-title {
                font-size: 2.5rem;
                font-weight: 600;
                margin-bottom: 1.5rem;
            }

            .how-it-works {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 2rem;
            }

            .step {
                background: rgba(255, 255, 255, 0.1);
                padding: 2rem;
                border-radius: 10px;
                width: 280px;
                text-align: center;
                transition: transform 0.3s ease;
            }

            .step:hover {
                transform: translateY(-10px);
            }

            .step i {
                font-size: 3rem;
                color: var(--color-accent);
                margin-bottom: 1rem;
            }

            .reviews {
                display: flex;
                justify-content: center;
                gap: 2rem;
                overflow: hidden;
            }

            .review {
                background: rgba(255, 255, 255, 0.1);
                padding: 1.5rem;
                border-radius: 10px;
                max-width: 300px;
                transition: transform 0.3s ease-in-out;
            }

            .review:hover {
                transform: scale(1.05);
            }

            .cta-section {
                background: var(--color-accent);
                padding: 3rem;
                text-align: center;
                border-radius: 15px;
                color: #fff;
                margin: 2rem auto;
                max-width: 800px;
            }

            .cta-section h2 {
                font-size: 2rem;
                margin-bottom: 1rem;
            }

            .cta-section .btn-cta {
                background: #FFF;
                color: var(--color-accent);
            }

            /* Галерея flip-карток */
            .gallery-grid {
                display: flex;
                gap: 1.5rem;
                justify-content: center;
                align-items: center;
            }

            .flip-card {
                background-color: transparent;
                width: 100%;
                height: 300px;
                perspective: 1000px;
            }

            .flip-card-inner {
                position: relative;
                width: 100%;
                height: 100%;
                transform-style: preserve-3d;
                transition: transform 0.6s;
            }

            .flip-card:hover .flip-card-inner {
                transform: rotateY(180deg);
            }

            .flip-card-front,
            .flip-card-back {
                position: absolute;
                width: 100%;
                height: 100%;
                backface-visibility: hidden;
                border-radius: 10px;
                overflow: hidden;
            }

            .flip-card-front {
                background-color: rgba(255, 255, 255, 0.1);
            }

            .flip-card-back {
                background-color: rgba(255, 255, 255, 0.2);
                transform: rotateY(180deg);
            }

            .flip-card img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
        </style>
    </head>

    <body>

        <section class="banner">
            <video autoplay loop muted playsinline>
                <source src="{{ asset('assets/banner-bg.mp4') }}" type="video/mp4">
                Ваш браузер не підтримує відео.
            </video>
            <div class="banner-overlay"></div>
            <div class="banner-content">
                <h1>HairMorph</h1>
                <p>Інноваційний сервіс генерації зачісок. Змініть свій образ за лічені секунди!</p>
                <a href="{{ route('upload.form') }}" class="btn-cta">Спробувати зараз</a>
            </div>
        </section>

        <section class="section">
            <h2 class="section-title">Як це працює?</h2>
            <div class="how-it-works">
                <div class="step">
                    <i class="fas fa-camera"></i>
                    <h3>Завантажте фото</h3>
                    <p>Додайте своє зображення для аналізу.</p>
                </div>
                <div class="step">
                    <i class="fas fa-brain"></i>
                    <h3>Аналіз AI</h3>
                    <p>Наша нейромережа аналізує ваше обличчя.</p>
                </div>
                <div class="step">
                    <i class="fas fa-cut"></i>
                    <h3>Обирайте зачіску</h3>
                    <p>Виберіть одну з запропонованих AI-стилів.</p>
                </div>
                <div class="step">
                    <i class="fas fa-download"></i>
                    <h3>Завантажте результат</h3>
                    <p>Збережіть свій новий стиль на пристрій.</p>
                </div>
            </div>
        </section>
        <section class="section">
            <h2 class="section-title">Приклади змін зачісок</h2>
            <div class="gallery-grid">
                <div class="flip-card">
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <img src="{{ asset('images/example1_before.png') }}" alt="До">
                        </div>
                        <div class="flip-card-back">
                            <img src="{{ asset('images/example1_after.png') }}" alt="Після">
                        </div>
                    </div>
                </div>
                <div class="flip-card">
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <img src="{{ asset('images/example2_before.jpg') }}" alt="До">
                        </div>
                        <div class="flip-card-back">
                            <img src="{{ asset('images/example2_after.png') }}" alt="Після">
                        </div>
                    </div>
                </div>
                <div class="flip-card">
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <img src="{{ asset('images/example3_before.jpg') }}" alt="До">
                        </div>
                        <div class="flip-card-back">
                            <img src="{{ asset('images/example3_after.png') }}" alt="Після">
                        </div>
                    </div>
                </div>
                <div class="flip-card">
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <img src="{{ asset('images/example4_before.jpg') }}" alt="До">
                        </div>
                        <div class="flip-card-back">
                            <img src="{{ asset('images/example4_after.png') }}" alt="Після">
                        </div>
                    </div>
                </div>
            </div>

        </section>

        <section class="section">
            <h2 class="section-title">Що кажуть наші користувачі?</h2>
            <div class="reviews">
                <div class="review">
                    <p>"HairMorph змінив моє уявлення про зачіски! Дуже швидко і просто."</p>
                    <h4>- Олена</h4>
                </div>
                <div class="review">
                    <p>"Додаток реально працює! Дякую за чудовий сервіс."</p>
                    <h4>- Максим</h4>
                </div>
                <div class="review">
                    <p>"Це просто магія! Завантажив фото і за хвилину отримав ідеальний стиль."</p>
                    <h4>- Ірина</h4>
                </div>
            </div>
        </section>

        <section class="cta-section">
            <h2>Спробуйте прямо зараз!</h2>
            <a href="{{ route('upload.form') }}" class="btn-cta">Завантажити фото</a>
        </section>

    </body>

    </html>
@endsection
