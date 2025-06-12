@extends('layouts.app')

@section('title', 'Зачіски')

@section('content')
    <style>
        .fade-in {
            animation: fadeInUp 0.6s ease-out both;
        }

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hairstyle-card {
            background: #1e1b4b;
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 1rem;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hairstyle-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4);
        }

        .hairstyle-img {
            width: 100%;
            object-fit: cover;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }


        .hairstyle-content {
            padding: 1rem;
            color: #f3f4f6;
        }

        .hairstyle-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: #e0e7ff;
            margin-bottom: 0.25rem;
        }

        .hairstyle-tag {
            font-size: 0.85rem;
            color: #c4b5fd;
            margin-bottom: 0.5rem;
        }

        .hairstyle-description {
            font-size: 0.9rem;
            color: #d1d5db;
        }

        .masonry {
            column-count: 1;
            column-gap: 1.5rem;
        }

        @media (min-width: 640px) {
            .masonry {
                column-count: 2;
            }
        }

        @media (min-width: 1024px) {
            .masonry {
                column-count: 3;
            }
        }

        @media (min-width: 1280px) {
            .masonry {
                column-count: 4;
            }
        }

        .masonry-item {
            break-inside: avoid;
            margin-bottom: 1.5rem;
        }
    </style>
    <style>
        .search-wrapper {
            max-width: 640px;
            margin: 0 auto 2.5rem auto;
            padding: 0 1rem;
        }

        .search-form {
            display: flex;
            border-radius: 0.75rem;
            overflow: hidden;
            border: 1px solid #6366f1;
            /* індиго */
            background-color: #1f1b3a;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            transition: box-shadow 0.3s ease;
        }

        .search-form:focus-within {
            box-shadow: 0 0 0 3px #6366f1;
        }

        .search-input {
            flex: 1;
            padding: 0.75rem 1rem;
            background: transparent;
            color: #ffffff;
            border: none;
            font-size: 1rem;
        }

        .search-input::placeholder {
            color: #a5b4fc;
        }

        .search-input:focus {
            outline: none;
        }

        .search-button {
            background-color: #6366f1;
            color: white;
            padding: 0 1.5rem;
            font-size: 1rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .search-button:hover {
            background-color: #4f46e5;
        }
    </style>
    <div class="search-wrapper">
        <form action="{{ route('hairstyles.index') }}" method="GET" class="search-form">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Введіть назву зачіски"
                class="search-input" autocomplete="off">
            <button type="submit" class="search-button">Знайти</button>
        </form>
    </div>



    <div class="masonry">
        @foreach ($hairstyles as $hairstyle)
            <div class="hairstyle-card masonry-item">
                @if ($hairstyle->image_path)
                    <img src="{{ asset('storage/' . $hairstyle->image_path) }}" alt="{{ $hairstyle->name }}"
                        class="hairstyle-img">
                @endif

                <div class="hairstyle-content">
                    <h2 class="hairstyle-name">{{ $hairstyle->name }}</h2>
                    <p class="hairstyle-tag">{{ $hairstyle->recommended_for }}</p>
                    <p class="hairstyle-description">{{ Str::limit($hairstyle->description, 90) }}</p>
                </div>
            </div>
        @endforeach
    </div>

@endsection
