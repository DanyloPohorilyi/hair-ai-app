@extends('admin.layouts.app')

<head>
    <link rel="stylesheet" href="{{ asset('css/form.css') }}">
</head>
@section('content')
    <div class="admin-container">
        <div class="form-wrapper">
            <h1 class="form-title">✏️ Редагування користувача</h1>

            <form action="{{ route('admin.customers.update', $customer->user_id) }}" method="POST" class="user-form">
                @csrf
                @method('PUT')

                <!-- Ім'я -->
                <div class="input-group">
                    <label for="name">Ім'я</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $customer->name) }}" required>
                    @error('name')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $customer->email) }}"
                        required>
                    @error('email')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Пароль -->
                <div class="input-group">
                    <label for="password">Новий пароль (якщо змінюєте)</label>
                    <input type="password" name="password" id="password" placeholder="Введіть новий пароль">
                    @error('password')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Підтвердження паролю -->
                <div class="input-group">
                    <label for="password_confirmation">Підтвердьте пароль</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        placeholder="Повторіть пароль">
                    @error('password_confirmation')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Чекбокс Адміністратора -->
                <div class="checkbox-group">
                    <input type="checkbox" name="is_admin" id="is_admin"
                        {{ old('is_admin', $customer->is_admin) ? 'checked' : '' }}>
                    <label for="is_admin">Адміністратор</label>
                </div>

                <button type="submit" class="btn-submit">Оновити</button>
            </form>
        </div>
    </div>
@endsection
