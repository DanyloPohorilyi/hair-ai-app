@extends('admin.layouts.app')

@section('content')
    <div class="admin-container">
        <h1>➕ Додати зачіску</h1>

        <form action="{{ route('admin.hairstyles.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="text" name="name" placeholder="Назва" required>
            <textarea name="description" placeholder="Опис" required></textarea>

            <!-- Custom File Input -->
            <div class="file-upload">
                <input type="file" name="image" id="fileInput" required>
                <label for="fileInput">📂 Оберіть файл</label>
            </div>

            <input type="text" name="recommended_for" placeholder="Рекомендовано для" required>
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

    /* 🔹 Base Styles */
    body {
        font-family: 'Poppins', sans-serif;
        background-color: var(--color-bg);
        color: var(--color-primary);
    }

    /* 🔹 Form Container */
    .admin-container {
        max-width: 600px;
        /* Increased width */
        margin: 60px auto;
        padding: 40px;
        background: var(--color-card);
        border-radius: 12px;
        box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.25);
        text-align: center;
    }

    /* 🔹 Heading */
    h1 {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 25px;
    }

    /* 🔹 Form Styling */
    form {
        display: flex;
        flex-direction: column;
        gap: 20px;
        align-items: center;
    }

    input,
    textarea {
        background: var(--color-input);
        border: 2px solid transparent;
        padding: 14px;
        border-radius: 8px;
        color: var(--color-primary);
        font-size: 1rem;
        width: 90%;
        /* Wider input fields */
        transition: border 0.3s, background 0.3s;
    }

    input:focus,
    textarea:focus {
        border: 2px solid var(--color-hover);
        background: #3A3A50;
        outline: none;
    }

    /* 🔹 Custom File Input */
    .file-upload {
        width: 90%;
        text-align: center;
        position: relative;
    }

    .file-upload input {
        display: none;
    }

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

    .file-upload label:hover {
        background: var(--color-hover);
    }

    /* 🔹 Submit Button */
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

    /* 🔹 Responsive Fix */
    @media (max-width: 768px) {
        .admin-container {
            max-width: 95%;
            padding: 25px;
        }

        input,
        textarea,
        .file-upload label,
        button {
            width: 100%;
        }
    }
</style>
