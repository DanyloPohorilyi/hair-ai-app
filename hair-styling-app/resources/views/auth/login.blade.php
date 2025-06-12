@extends('layouts.app')

<head>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
@section('content')
    <div class="auth-container">
        <h1>🔑 Вхід</h1>

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
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

            <button type="submit" class="btn-submit">Увійти</button>
        </form>

        <p>Ще не зареєстровані? <a href="{{ route('register') }}">Створити акаунт</a></p>
    </div>
@endsection
