@extends('admin.layouts.app')

@section('content')
    <div class="admin-container">
        <header class="admin-header">
            <h1>📷 Секретні зображення</h1>
            <a href="{{ route('admin.secret-images.create') }}" class="btn-add">+ Додати</a>
        </header>

        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif

        <table class="custom-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Оригінал</th>
                    <th>Згенероване</th>
                    <th>Зачіска</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($secretImages as $image)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><img src="{{ asset('storage/' . $image->original_path) }}" class="preview-img"></td>
                        <td><img src="{{ asset('storage/' . $image->generated_path) }}" class="preview-img"></td>
                        <td>{{ $image->haircut }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $secretImages->links() }}
    </div>
@endsection
<style>
    .admin-container {
        max-width: 1100px;
        margin: 40px auto;
        padding: 30px;
        background: var(--color-card);
        border-radius: 12px;
        box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
        text-align: center;
    }

    h1 {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 20px;
    }

    /* Таблиця */
    .custom-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .custom-table th,
    .custom-table td {
        padding: 12px;
        border-bottom: 1px solid var(--color-primary);
        text-align: center;
    }

    .custom-table th {
        font-weight: bold;
        color: var(--color-primary);
        background: var(--color-input);
    }

    /* Зображення */
    .preview-img {
        width: 80px;
        height: 80px;
        border-radius: 8px;
        object-fit: cover;
    }

    /* Кнопка додати */
    .btn-add {
        background: var(--color-accent);
        padding: 12px 16px;
        border-radius: 8px;
        color: white;
        font-size: 1rem;
        font-weight: bold;
        text-decoration: none;
        transition: 0.3s;
    }

    .btn-add:hover {
        background: var(--color-hover);
    }
</style>
