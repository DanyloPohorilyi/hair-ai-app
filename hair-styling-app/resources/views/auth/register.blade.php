@extends('layouts.app')

<head>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
@section('content')
    <div class="auth-container">
        <h1>📌 Реєстрація</h1>

        <form action="{{ route('register.submit') }}" method="POST">
            @csrf
            <div class="input-group">
                <label for="name">Ім'я</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required>
                @error('name')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required>
                @error('email')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="input-group">
                <label for="password">Пароль</label>
                <input type="password" name="password" id="password" required>
                @error('password')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="input-group">
                <label for="password_confirmation">Підтвердьте пароль</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required>
            </div>

            <button type="submit" class="btn-submit">Зареєструватися</button>
        </form>

        <p>Вже маєте акаунт? <a href="{{ route('login') }}">Увійдіть</a></p>
    </div>
@endsection
