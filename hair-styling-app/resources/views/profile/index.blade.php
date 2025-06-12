@extends('layouts.app')


<head>
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
</head>
@section('content')
    <div class="profile-container">
        <div class="profile-header">
            <div class="avatar-section">
                <img src="{{ asset('storage/' . auth()->user()->photo) }}?{{ time() }}" alt="User Avatar"
                    class="avatar">

                <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="avatar" id="avatar-upload" class="hidden-input"
                        onchange="this.form.submit()">
                    <label for="avatar-upload" class="btn-change-avatar">Змінити аватар</label>
                </form>
            </div>
            <h1>{{ $user->name }}</h1>
            <p>Email: {{ $user->email }}</p>
        </div>

        <div class="edit-section">
            <h2>Редагування профілю</h2>
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                <label>Ім'я</label>
                <input type="text" name="name" value="{{ $user->name }}" required>

                <label>Новий пароль (якщо змінюєте)</label>
                <input type="password" name="password">

                <label>Підтвердження пароля</label>
                <input type="password" name="password_confirmation">

                <button type="submit">Зберегти</button>
            </form>
        </div>

        <div class="images-section">
            <h2>Результати обробки</h2>
            <div class="image-grid">
                @foreach ($processedImages as $image)
                    <a href="{{ route('result.show', $image->image_id) }}" class="image-card">
                        <img src="{{ asset('storage/' . $image->processed_image_path) }}" alt="Processed Image">
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endsection
