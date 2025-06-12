@extends('admin.layouts.app')

@section('content')
    <header class="admin-header">
        <h1>🧑‍💻 Панель адміністратора</h1>
        <a href="{{ route('logout') }}" class="btn-logout">Вийти</a>
    </header>

    <section class="dashboard">
        <div class="stat-box users">
            <i class="fas fa-users"></i>
            <h3>Користувачі</h3>
            <p>{{ $customersCount }}</p>
        </div>
        <div class="stat-box hairstyles">
            <i class="fas fa-cut"></i>
            <h3>Зачіски</h3>
            <p>{{ $hairstylesCount }}</p>
        </div>
        <div class="stat-box images">
            <i class="fas fa-image"></i>
            <h3>Зображення</h3>
            <p>{{ $imagesCount }}</p>
        </div>
        <div class="stat-box recommendations">
            <i class="fas fa-lightbulb"></i>
            <h3>Рекомендації</h3>
            <p>{{ $recommendationsCount }}</p>
        </div>
    </section>
@endsection
