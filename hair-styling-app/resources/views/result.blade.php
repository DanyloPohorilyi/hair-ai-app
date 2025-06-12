@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h2 class="text-center mb-4">Результати обробки</h2>

        <h3 class="text-center mt-5">📷 Оригінальне зображення</h3>
        <div class="text-center mb-4">
            <img src="{{ asset($data['original_image']) }}" class="img-fluid original-img" alt="Оригінальне зображення">
        </div>

        <!-- Загальна інформація -->
        <div class="result-info text-center">
            <p><strong>Гендер:</strong> {{ $data['gender'] }}</p>
            <p><strong>Форма обличчя:</strong> {{ $data['face_shape'] }}</p>
        </div>

        <!-- Рекомендовані зачіски -->
        <h3 class="text-center mt-5">💇‍♀️ Рекомендовані зачіски</h3>
        <div class="row">
            @foreach ($data['recommended_hairstyles'] as $hairstyle)
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="hairstyle-card">
                        <h5>{{ $hairstyle['hairstyle'] }}</h5>
                        <img src="{{ asset($hairstyle['generated_image']) }}" class="img-fluid adaptive-img"
                            alt="Зачіска: {{ $hairstyle['hairstyle'] }}">
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Кнопка повернення -->
        <div class="text-center mt-4">
            <a href="{{ route('upload.form') }}" class="btn btn-secondary">🔄 Завантажити інше фото</a>
        </div>

        <!-- Логи -->
        <div class="mt-5">
            <details>
                <summary class="text-center h5 mb-3">📝 Переглянути логи обробки</summary>
                <pre class="log-box">
{{ file_get_contents(storage_path('logs/python_log.txt')) }}
                </pre>
            </details>
        </div>
    </div>

    <style>
        .result-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .hairstyle-card {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .hairstyle-card:hover {
            transform: translateY(-5px);
        }

        .adaptive-img {
            max-width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 5px;
        }

        summary {
            cursor: pointer;
            user-select: none;
        }

        .log-box {
            background: #222;
            color: #ccc;
            padding: 20px;
            border-radius: 10px;
            max-height: 400px;
            overflow: auto;
            font-size: 14px;
        }

        .original-img {
            max-width: 20vw;

            height: auto;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }
    </style>
@endsection
