<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Адмін Панель | HairMorph</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body>

    <div class="admin-container">
        <!-- 📜 Бічне меню -->
        <aside class="sidebar">
            <div class="logo">
                <a href="{{ route('admin.dashboard') }}">
                    <img src="{{ asset('assets/ico.png') }}" alt="HairMorph Logo">
                    <span>HairMorph Admin</span>
                </a>
            </div>
            <ul class="menu">
                <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->is('admin') ? 'active' : '' }}">
                        <i class="fas fa-home"></i> Головна</a></li>
                <li><a href="{{ route('admin.customers.index') }}"
                        class="{{ request()->is('admin/customers*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> Користувачі</a></li>
                <li><a href="{{ route('admin.hairstyles.index') }}"
                        class="{{ request()->is('admin') ? 'active' : '' }}"><i class="fas fa-cut"></i> Зачіски</a></li>
                <li><a href="{{ route('admin.images.index') }}"><i class="fas fa-images"
                            class="{{ request()->is('admin/customers*') ? 'active' : '' }}"></i> Зображення</a></li>
                <li><a href="{{ route('admin.analytics') }}"
                        class="{{ request()->is('admin/analytics*') ? 'active' : '' }}"><i
                            class="fas fa-chart-bar"></i>
                        Аналітика</a></li>
                <li><a href="/" class="logout"><i class="fas fa-sign-out-alt"></i> Вийти</a></li>
            </ul>
        </aside>

        <!-- Основний контент -->
        <main class="content">
            @yield('content')
        </main>
    </div>

</body>

</html>
