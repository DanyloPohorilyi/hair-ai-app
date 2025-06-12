@extends('admin.layouts.app')

<head>

    <link rel="stylesheet" href="{{ asset('css/admin-hairstyles.css') }}">
</head>
@section('content')
    <div class="admin-container">
        <header class="admin-header">
            <h1>💇‍♀️ Зачіски</h1>
            <a href="{{ route('admin.hairstyles.create') }}" class="btn-add">+ Додати</a>
        </header>

        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif

        <table class="custom-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Зображення</th>
                    <th>Назва</th>
                    <th>Опис</th>
                    <th>Рекомендовано для</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($hairstyles as $hairstyle)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><img src="{{ asset('storage/' . $hairstyle->image_path) }}" class="preview-img"></td>
                        <td>{{ $hairstyle->name }}</td>
                        <td>{{ $hairstyle->description }}</td>
                        <td>{{ $hairstyle->recommended_for }}</td>
                        <td>
                            <a href="{{ route('admin.hairstyles.edit', $hairstyle->hairstyle_id) }}" class="btn-edit">✏️</a>
                            <form action="{{ route('admin.hairstyles.destroy', $hairstyle->hairstyle_id) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">🗑️</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $hairstyles->links() }}
    </div>
@endsection
