<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'HairMorph')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('assets/logo.png') }}">

    <!-- Підключення Bootstrap (або інші CSS) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <!-- Підключення іконок (наприклад, FontAwesome) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

    <!-- Google Fonts (можна змінити шрифт на інший) -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <style>
        :root {
            /* Базова кольорова палітра */
            --color-bg: #0F1235;
            /* Темно-синій / фіолетовий */
            --color-primary: #CFCFD3;
            /* Сріблясто-білий / світлий відтінок */
            --color-accent: #8F90FF;
            /* Можна підкреслити для кнопок */
            --font-family: 'Montserrat', sans-serif;
        }

        body {
            background-color: var(--color-bg);
            color: var(--color-primary);
            font-family: var(--font-family);
            margin: 0;
            padding: 0;
        }

        .header,
        .footer {
            background-color: transparent;
        }

        .logo-img {
            width: 40px;
            height: 40px;
        }

        .logo-text {
            font-size: 1.2rem;
        }

        .user-icon-link {
            color: var(--color-primary);
            font-size: 1.5rem;
        }

        .user-icon-link:hover {
            color: var(--color-accent);
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #0F1235;
            /* Загальний темний фон з прикладу */
            color: #CFCFD3;
            /* Світлий текст */
            margin: 0;
        }

        .content {
            min-height: 70vh;
            /* щоб мати простір між header та footer */
            padding: 0;
        }

        .header,
        .footer {
            background-color: transparent;
        }
    </style>

    @yield('head')
    <!-- секція, якщо потрібно вставити власний <style> чи інший head-контент з дочірніх шаблонів -->
</head>

<body>

    <!-- Включаємо хедер -->
    @include('layouts.header')

    <!-- Основний вміст сторінки -->
    <div class="content">
        @yield('content')
    </div>

    <!-- Включаємо футер -->
    @include('layouts.footer')

    <!-- Підключаємо Bootstrap JS (якщо потрібно),
         і можливі додаткові скрипти -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
    <!-- секція для підключення додаткових скриптів у дочірніх шаблонах -->

</body>

</html>
