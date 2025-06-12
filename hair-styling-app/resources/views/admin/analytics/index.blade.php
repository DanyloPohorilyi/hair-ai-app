@extends('admin.layouts.app')

<head>
    <link rel="stylesheet" href="{{ asset('css/analytics.css') }}">
</head>
@section('content')
    <div class="admin-container">
        <header class="admin-header">
            <h1>📊 Аналітика</h1>
        </header>

        <section class="analytics-dashboard">
            <div class="stat-box users">
                <i class="fas fa-users"></i>
                <h3>Користувачі</h3>
                <p>{{ $totalUsers }}</p>
            </div>
            <div class="stat-box admins">
                <i class="fas fa-user-shield"></i>
                <h3>Адміністратори</h3>
                <p>{{ $totalAdmins }}</p>
            </div>
            <div class="stat-box hairstyles">
                <i class="fas fa-cut"></i>
                <h3>Зачіски</h3>
                <p>{{ $totalHairstyles }}</p>
            </div>
            <div class="stat-box images">
                <i class="fas fa-image"></i>
                <h3>Зображення</h3>
                <p>{{ $totalImages }}</p>
            </div>
            <div class="stat-box processed-images">
                <i class="fas fa-sync"></i>
                <h3>Оброблені зображення</h3>
                <p>{{ $totalProcessedImages }}</p>
            </div>
            <div class="stat-box recommendations">
                <i class="fas fa-lightbulb"></i>
                <h3>Рекомендації</h3>
                <p>{{ $totalRecommendations }}</p>
            </div>
        </section>

        <section class="charts">
            <h2>📈 Активність користувачів</h2>
            <canvas id="userChart"></canvas>

            <h2>🔥 ТОП-5 зачісок</h2>
            <ul>
                @foreach ($popularHairstyles as $hairstyle)
                    <li>{{ $hairstyle->hairstyle->name }} - {{ $hairstyle->count }} рекомендацій</li>
                @endforeach
            </ul>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var ctx = document.getElementById('userChart').getContext('2d');
        var userChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_keys($userRegistrations->toArray())) !!},
                datasets: [{
                    label: 'Реєстрації',
                    data: {!! json_encode(array_values($userRegistrations->toArray())) !!},
                    borderColor: '#8F90FF',
                    fill: false
                }]
            }
        });
    </script>
@endsection
