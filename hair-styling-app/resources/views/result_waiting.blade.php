@extends('layouts.app')

@section('content')
    <div class="container mt-5 text-center">
        <h2 class="mb-4">Зачекайте, ми створюємо вашу зачіску...</h2>

        <!-- Лоадер -->
        <div class="loading-container">
            <div class="loader"></div>
            <p id="loading-text">Оцінюємо ваш Face-Card...</p>
        </div>

        <script>
            const messages = [
                "Оцінюємо ваш Face-Card...",
                "Закликаємо найкращих стилістів...",
                "Аналізуємо форму вашого обличчя...",
                "Обираємо найкращі трендові зачіски...",
                "Створюємо унікальні варіанти...",
                "Перевіряємо гармонійність стилю...",
                "Фінальні штрихи...",
                "Готово! Завантажуємо результат..."
            ];

            let messageIndex = 0;

            function updateLoadingMessage() {
                if (messageIndex < messages.length - 1) {
                    document.getElementById("loading-text").textContent = messages[messageIndex];
                    messageIndex++;
                }
            }

            setInterval(updateLoadingMessage, 5000);

            function checkStatus() {
                fetch("{{ route('result.status', ['id' => $id]) }}")
                    .then(response => response.json())
                    .then(data => {
                        if (data.done) {
                            window.location.href = "{{ route('result.show', ['id' => $id]) }}";
                        } else {
                            setTimeout(checkStatus, 3000);
                        }
                    });
            }

            checkStatus();
        </script>

        <style>
            .loading-container {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                margin-top: 30px;
            }

            .loader {
                border: 6px solid rgba(255, 255, 255, 0.1);
                border-left-color: var(--color-accent);
                border-radius: 50%;
                width: 80px;
                height: 80px;
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                0% {
                    transform: rotate(0deg);
                }

                100% {
                    transform: rotate(360deg);
                }
            }

            #loading-text {
                font-size: 1.2rem;
                margin-top: 15px;
                font-weight: bold;
            }
        </style>
    </div>
@endsection
