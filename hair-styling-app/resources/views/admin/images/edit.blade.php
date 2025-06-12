@extends('admin.layouts.app')

@section('content')
    <div class="admin-container">
        <h1>✏️ Редагування зображення #{{ $image->image_id }}</h1>

        <div class="image-preview">
            <img src="{{ asset('storage/' . $image->original_path) }}" class="preview-img">
            <img src="{{ asset('storage/' . $image->generated_path) }}" class="preview-img">
        </div>

        <form action="{{ route('admin.images.update', $image->image_id) }}" method="POST">
            @csrf
            @method('PUT')
            <label>Зачіска</label>
            <input type="text" name="haircut" value="{{ $image->haircut }}" required>
            <button type="submit">Оновити</button>
        </form>
    </div>
@endsection
