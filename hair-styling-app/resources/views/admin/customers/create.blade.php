@extends('admin.layouts.app')

<head>
    <link rel="stylesheet" href="{{ asset('css/form.css') }}">
</head>
@section('content')
    <div class="admin-container">
        <div class="form-wrapper">
            <h1 class="form-title">➕ Додати користувача</h1>

            <form action="{{ route('admin.customers.store') }}" method="POST" class="user-form">
                @csrf
                <div class="input-group">
                    <label for="name">Ім'я</label>
                    <input type="text" name="name" id="name" placeholder="Введіть ім'я" required>
                </div>

                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" placeholder="Введіть email" required>
                </div>

                <div class="input-group">
                    <label for="password">Пароль</label>
                    <input type="password" name="password" id="password" placeholder="Введіть пароль" required>
                </div>

                <div class="input-group">
                    <label for="password_confirmation">Підтвердьте пароль</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        placeholder="Повторіть пароль" required>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" name="is_admin" id="is_admin">
                    <label for="is_admin">Зробити адміном</label>
                </div>

                <button type="submit" class="btn-submit">Зберегти</button>
            </form>
        </div>
    </div>
@endsection
