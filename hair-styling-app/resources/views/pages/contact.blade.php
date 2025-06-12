@extends('layouts.app')

@section('title', 'Контакти')

@section('content')
    <style>
        .fade-in {
            animation: fadeInUp 0.8s ease-out both;
        }

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .section-block {
            background: linear-gradient(to right, #4c1d95, #312e81);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 20px 30px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .section-block:hover {
            transform: translateY(-4px);
            box-shadow: 0 30px 40px rgba(0, 0, 0, 0.4);
        }
    </style>

    <div class="container mx-auto py-12 px-4 md:px-8 fade-in">
        <h1 class="text-4xl font-bold text-white text-center mb-10 drop-shadow-lg">Контакти</h1>

        {{-- Блок зв’язку --}}
        <div class="section-block text-white mb-10">
            <h2 class="text-2xl font-semibold mb-4">📩 Зв'яжіться з нами</h2>
            <p class="text-indigo-200 leading-relaxed mb-6">
                Маєте запитання, пропозиції чи потребуєте допомоги? Ми завжди поруч!
                Оберіть потрібний відділ і звертайтесь зручно для вас.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-xl font-semibold mb-2">🔎 Загальні запитання</h3>
                    <p class="text-indigo-100">Електронна пошта:
                        <a href="mailto:info@hairmorph.com" class="text-blue-400 hover:underline">info@hairmorph.com</a>
                    </p>
                    <p class="text-indigo-100">Телефон:
                        <a href="tel:+380123456789" class="text-blue-400 hover:underline">+38 (012) 345-67-89</a>
                    </p>
                </div>

                <div>
                    <h3 class="text-xl font-semibold mb-2">🛠️ Підтримка</h3>
                    <p class="text-indigo-100">Електронна пошта:
                        <a href="mailto:support@hairmorph.com"
                            class="text-blue-400 hover:underline">support@hairmorph.com</a>
                    </p>
                    <p class="text-indigo-100">Графік роботи: <br> <span class="text-sm">Пн–Пт, 9:00–18:00 (UTC+3)</span>
                    </p>
                </div>
            </div>
        </div>

        {{-- Місцезнаходження --}}
        <div class="section-block text-white">
            <h2 class="text-2xl font-semibold mb-4">📍 Наше місцезнаходження</h2>
            <p class="text-indigo-200 leading-relaxed mb-4">
                Хоча ми працюємо переважно онлайн, ви можете знайти нас за цією адресою:
            </p>
            <address class="text-indigo-100 not-italic leading-relaxed">
                Вулиця Хрещатик, 10<br>
                Київ, 01001<br>
                Україна
            </address>

            {{-- Опціональна карта --}}
            {{--
            <div class="mt-6 h-64 bg-gray-700 rounded-lg flex items-center justify-center text-gray-300">
                [Тут може бути вбудована карта Google Maps]
            </div>
            --}}
        </div>
    </div>
@endsection
