@extends('admin.layouts.app')

@section('content')
    <div class="admin-container">
        <header class="admin-header">
            <h1>📷 Зображення</h1>
        </header>

        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif

        <table class="custom-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Оригінал</th>
                    <th>Генероване</th>
                    <th>Зачіска</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($images as $image)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><img src="{{ asset('storage/' . $image->original_path) }}" class="preview-img"></td>
                        <td><img src="{{ asset('storage/' . $image->generated_path) }}" class="preview-img"></td>
                        <td>{{ $image->haircut }}</td>
                        <td>
                            <a href="{{ route('admin.images.edit', $image->image_id) }}" class="btn-edit">✏️</a>
                            <form action="{{ route('admin.images.destroy', $image->image_id) }}" method="POST"
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

        {{ $images->links() }}
    </div>
@endsection
