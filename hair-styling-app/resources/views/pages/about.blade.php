@extends('layouts.app')

@section('title', 'Про нас')

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

        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        .section-divider {
            height: 2px;
            background: linear-gradient(to right, #7c3aed, #4f46e5);
            margin: 3rem 0;
        }
    </style>

    <div class="container mx-auto py-12 px-4 md:px-8">
        <h1 class="text-4xl font-bold text-white text-center mb-4 drop-shadow-lg fade-in">Про нас</h1>
        <p class="text-indigo-200 text-center text-lg italic mb-12 fade-in">
            «Майбутнє краси — це поєднання штучного інтелекту та людської індивідуальності.»
        </p>

        <div class="section-divider fade-in"></div>

        {{-- Наша місія --}}
        <section class="bg-gradient-to-br from-purple-800 to-indigo-900 p-8 rounded-2xl shadow-xl mb-10 fade-in">
            <h2 class="text-2xl font-bold text-white mb-4">Наша місія</h2>
            <p class="text-gray-200 leading-relaxed text-lg">
                Ми віримо, що кожен заслуговує на зачіску, яка ідеально підкреслює його індивідуальність.
                Наше завдання — за допомогою штучного інтелекту зробити процес підбору зачіски легким,
                естетично приємним та надихаючим. Користувачі можуть експериментувати з образами в реальному
                часі прямо у себе вдома.
            </p>
        </section>

        {{-- Наша історія --}}
        <section class="bg-gradient-to-br from-indigo-900 to-purple-800 p-8 rounded-2xl shadow-xl mb-10 fade-in">
            <h2 class="text-2xl font-bold text-white mb-4">Наша історія</h2>
            <p class="text-gray-200 leading-relaxed text-lg">
                У 2023 році троє ентузіастів — розробник, дизайнер та аналітик — об’єднали зусилля, щоб
                створити HairMorph. Почавши з ідеї онлайн-примірки зачісок, ми побудували інтелектуальну
                платформу, яка зараз допомагає тисячам людей з усього світу обирати стиль, що справді їм пасує.
            </p>
        </section>

        <div class="section-divider fade-in"></div>

        {{-- Наша команда --}}
        <section class="mb-10 fade-in">
            <h2 class="text-3xl font-bold text-white mb-10 text-center tracking-tight">Наша команда</h2>
            <div class="grid md:grid-cols-3 gap-8">
                {{-- Card Template --}}
                @php
                    $team = [
                        [
                            'name' => 'Данило Погорілий',
                            'role' => 'CEO',
                            'desc' =>
                                'Відповідає за візію, бізнес-розвиток та партнерства. Надихає команду рухатись уперед.',
                        ],
                        [
                            'name' => 'Даніель Погор',
                            'role' => 'Lead AI Developer',
                            'desc' =>
                                'Створює та оптимізує моделі глибокого навчання, що оживляють зачіски на вашому фото.',
                        ],
                        [
                            'name' => 'Данік Погорільський',
                            'role' => 'UI/UX Designer',
                            'desc' =>
                                'Відповідальний за зручний інтерфейс, логіку взаємодії та візуальне оформлення сайту.',
                        ],
                    ];
                @endphp

                @foreach ($team as $member)
                    <div
                        class="group relative overflow-hidden bg-gradient-to-br from-[#6a4bc8] to-[#4b44d4] border border-indigo-400/30 rounded-xl p-6 shadow-2xl transition-transform duration-300 hover:-translate-y-2 hover:shadow-indigo-500/50">
                        <div
                            class="absolute inset-0 z-0 bg-gradient-to-tr from-purple-700 via-indigo-500 to-transparent opacity-20 group-hover:opacity-40 transition-opacity duration-500 blur-xl">
                        </div>

                        <div class="relative z-10">
                            <h3 class="text-white text-xl font-semibold mb-1">{{ $member['name'] }}</h3>
                            <p class="text-indigo-200 text-sm font-medium mb-3 tracking-wide uppercase">
                                {{ $member['role'] }}</p>
                            <p class="text-gray-200 text-sm leading-relaxed">
                                {{ $member['desc'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

    </div>
@endsection
