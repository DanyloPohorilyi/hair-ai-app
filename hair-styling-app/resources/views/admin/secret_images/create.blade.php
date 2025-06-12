@extends('admin.layouts.app')

@section('content')
    <div class="admin-container">
        <h1>📷 Додати секретне зображення</h1>

        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('admin.secret-images.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Завантаження оригінального фото -->
            <div class="file-upload">
                <label for="original_image">📂 Оригінальне фото</label>
                <input type="file" name="original_image" id="original_image" required>
            </div>

            <!-- Вибір зачіски -->
            <div class="select-box">
                <label for="haircut">💇‍♀️ Оберіть зачіску</label>
                <select name="haircut" id="haircut" required>
                    <option value="">-- Виберіть зачіску --</option>
                    @foreach ($hairstyles as $hairstyle)
                        <option value="{{ $hairstyle->name }}">{{ $hairstyle->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Завантаження згенерованого фото -->
            <div class="file-upload">
                <label for="generated_image">🖼 Завантажити змінене фото</label>
                <input type="file" name="generated_image" id="generated_image" required>
            </div>

            <button type="submit">Зберегти</button>
        </form>
    </div>
@endsection
<style>
    :root {
        --color-bg: #1E1E2E;
        --color-primary: #E0E0E6;
        --color-accent: #6A5ACD;
        --color-card: #2A2A3C;
        --color-hover: #A78BFA;
        --color-input: #2E2E40;
    }

    /* 🔹 Базові стилі */
    .admin-container {
        max-width: 600px;
        margin: 60px auto;
        padding: 40px;
        background: var(--color-card);
        border-radius: 12px;
        box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.25);
        text-align: center;
    }

    /* 🔹 Заголовок */
    h1 {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 25px;
    }

    /* 🔹 Форма */
    form {
        display: flex;
        flex-direction: column;
        gap: 20px;
        align-items: center;
    }

    /* 🔹 Текстові поля */
    input,
    select {
        background: var(--color-input);
        border: 2px solid transparent;
        padding: 14px;
        border-radius: 8px;
        color: var(--color-primary);
        font-size: 1rem;
        width: 90%;
        transition: border 0.3s, background 0.3s;
    }

    input:focus,
    select:focus {
        border: 2px solid var(--color-hover);
        background: #3A3A50;
        outline: none;
    }

    /* 🔹 Файл */
    .file-upload label {
        background: var(--color-accent);
        padding: 12px 15px;
        border-radius: 8px;
        color: white;
        cursor: pointer;
        font-size: 1rem;
        width: 100%;
        display: block;
        text-align: center;
        font-weight: bold;
        transition: background 0.3s;
    }

    .file-upload input {
        display: none;
    }

    .file-upload label:hover {
        background: var(--color-hover);
    }

    /* 🔹 Кнопка */
    button {
        background: var(--color-accent);
        border: none;
        padding: 14px;
        border-radius: 8px;
        color: white;
        font-size: 1.1rem;
        font-weight: bold;
        width: 90%;
        cursor: pointer;
        transition: 0.3s;
    }

    button:hover {
        background: var(--color-hover);
    }
</style>
