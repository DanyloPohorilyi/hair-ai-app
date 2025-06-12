@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h2 class="text-center mb-4 text-light">📸 Завантажте ваше фото</h2>

        <div id="error-message" class="alert alert-danger d-none"></div>

        <div class="upload-container text-center">
            <form id="upload-form" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="upload-box">
                    <input type="file" id="imageUpload" name="image" class="form-control d-none" required>
                    <label for="imageUpload" class="upload-label">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p id="file-name" class="upload-text">Натисніть або перетягніть фото сюди</p>
                    </label>
                </div>
                <button type="submit" id="submit-button" class="btn btn-custom mt-3">
                    <i class="fas fa-magic"></i> Обробити
                </button>
            </form>
        </div>

        <!-- Лоадер + цікаві факти -->
        <div id="loading-container" class="text-center d-none mt-4">
            <div class="loader"></div>
            <p id="loading-text">Зачекайте, ми створюємо вашу зачіску...</p>

            <!-- Інтерактивний блок з фактами -->
            <div id="fact-container" class="fact-box">
                <p id="fun-fact">💡 Чи знали ви, що волосся складається на 95% з кератину?</p>
            </div>
        </div>
    </div>

    <!-- 🎨 Стилі -->
    <style>
        /* Базові кольори */
        :root {
            --color-bg: #0F1235;
            --color-primary: #CFCFD3;
            --color-accent: #8F90FF;
        }

        body {
            background-color: var(--color-bg);
            color: var(--color-primary);
            font-family: 'Arial', sans-serif;
        }

        .upload-container {
            max-width: 500px;
            margin: auto;
        }

        /* 📂 Коробка завантаження */
        .upload-box {
            width: 100%;
            height: 200px;
            border: 2px dashed var(--color-accent);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .upload-box:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .upload-label {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--color-primary);
            font-size: 1.2rem;
            font-weight: bold;
        }

        .upload-label i {
            font-size: 3rem;
            margin-bottom: 10px;
            color: var(--color-accent);
        }

        #file-name {
            font-size: 1.1rem;
            font-weight: bold;
            color: var(--color-accent);
        }

        /* 🎡 Лоадер */
        .loader {
            border: 6px solid rgba(255, 255, 255, 0.1);
            border-left-color: var(--color-accent);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            display: inline-block;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* 📜 Блок фактів */
        .fact-box {
            margin-top: 20px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid var(--color-accent);
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 500;
            color: var(--color-primary);
            text-align: center;
            transition: opacity 0.8s ease-in-out, transform 0.5s ease-in-out;
        }

        .fact-box.fade-out {
            opacity: 0;
            transform: translateY(-10px);
        }

        .fact-box.fade-in {
            opacity: 1;
            transform: translateY(0);
        }

        /* 🎨 Кастомна кнопка */
        .btn-custom {
            background-color: var(--color-accent);
            border: none;
            color: var(--color-bg);
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 8px;
            transition: 0.3s;
        }

        .btn-custom:hover {
            background-color: var(--color-primary);
            color: var(--color-bg);
        }
    </style>

    <!-- ⚡ JavaScript -->
    <script>
        const funFacts = [
            "💡 Чи знали ви, що волосся складається на 95% з кератину?",
            "🎨 У давнину люди фарбували волосся за допомогою природних барвників, таких як хна та індиго.",
            "🧬 Колір волосся визначається рівнем меланіну, що міститься у волоссі.",
            "💇 Найшвидше волосся росте у віці 15-30 років.",
            "🌍 Волосся може витримати вагу до 100 грамів на пасмо!",
            "🔬 Волосся — одна з найшвидше зростаючих тканин у людському тілі.",
            "🦁 Людина в середньому має від 100 000 до 150 000 волосин на голові.",
            "🌡️ Влітку волосся росте швидше, ніж взимку.",
            "🐻 Волосся на голові зберігає тепло і захищає шкіру від ультрафіолету.",
            "🛁 Мокре волосся може стати на 30% довшим, ніж сухе.",
            "🧴 Фарбування та гаряча укладка можуть пошкодити волосся і зробити його ламким.",
            "⚡ Волосся може накопичувати статичний заряд, що призводить до електризації.",
            "🖤 Чорний — найпоширеніший колір волосся у світі.",
            "👩‍🦳 Лише 2% населення світу народжується блондинами.",
            "🦊 Рудий колір волосся зустрічається у менш ніж 2% людей у світі.",
            "💪 Волосся міцніше, ніж мідний дріт того ж діаметру!",
            "📏 В середньому волосся виростає на 1-1.5 см на місяць.",
            "⏳ Волосся живе від 2 до 7 років, перш ніж випаде.",
            "🍏 Збалансоване харчування сприяє здоров'ю волосся.",
            "❄️ Стрес може викликати передчасне посивіння волосся.",
            "💦 Волосяний фолікул на 25% складається з води.",
            "🛌 Волосся може ламатися через тертя об подушку під час сну.",
            "🚿 Миття волосся дуже гарячою водою може висушити шкіру голови.",
            "🔁 Людина втрачає від 50 до 100 волосин щодня.",
            "💨 Волосся може вбирати запахи з навколишнього середовища.",
            "🎭 Волосся може змінювати свою текстуру з віком.",
            "☀️ Сонячні промені можуть знебарвити волосся, освітлюючи його.",
            "💆 Масаж шкіри голови стимулює ріст волосся.",
            "🥑 Продукти, багаті на біотин, сприяють зміцненню волосся.",
            "🧴 Лупа не завжди пов'язана з сухістю шкіри голови.",
            "🔬 ДНК волосся може розповісти про спосіб життя та харчування людини.",
            "📜 У Стародавньому Єгипті чоловіки і жінки голили голову та носили перуки.",
            "👶 Деякі немовлята народжуються з волоссям, але воно може випасти через кілька тижнів.",
            "🍯 Мед використовували для догляду за волоссям ще в античні часи.",
            "💨 Швидкість росту волосся може зменшитися через нестачу вітамінів.",
            "👨‍🦲 Лисина у чоловіків часто обумовлена генетично.",
            "👩‍🦱 Кучеряве волосся росте більш хаотично, ніж пряме.",
            "🌊 Солона вода може висушувати волосся, роблячи його ламким.",
            "🔥 Гарячі інструменти для укладання можуть спричинити пошкодження волосся.",
            "🚀 Волосся астронавтів у космосі росте так само, як на Землі!",
            "🦠 Волосся не містить живих клітин, тому воно не болить при стрижці.",
            "📏 Найдовше волосся у світі було понад 5,6 метра завдовжки.",
            "🥑 Авокадо є природним зволожувачем для волосся.",
            "🎭 Вікторіанці зберігали локони волосся як пам’ятні сувеніри.",
            "🌡️ Волосся може стати сухим через кондиціонери повітря.",
            "🍵 Зелений чай може допомогти зміцнити волосся.",
            "🧖‍♀️ Маски для волосся з кокосової олії глибоко живлять волосся.",
            "🌱 Алое вера може заспокоїти шкіру голови і зменшити свербіж.",
            "💤 Недосипання може сповільнити ріст волосся.",
            "💊 Вітамін D важливий для здоров'я волосяних фолікулів.",
            "🔬 Під мікроскопом волосся схоже на лускату структуру.",
            "🍎 Яблучний оцет допомагає зменшити жирність волосся.",
            "🚿 Часте миття волосся може висушувати шкіру голови.",
            "🧴 Використання кондиціонера допомагає запобігти ламкості волосся.",
            "🛌 Шовкова наволочка допомагає зменшити ламкість волосся.",
            "📏 Волосся росте швидше у жінок, ніж у чоловіків.",
            "🧑‍🦳 Сивина з'являється через зменшення вироблення меланіну.",
            "🛑 Високий рівень стресу може спричинити випадіння волосся.",
            "🧴 Сульфати в шампунях можуть сушити волосся.",
            "🌙 Волосся може випадати більше восени, ніж навесні.",
            "☕ Надмірне споживання кофеїну може впливати на ріст волосся.",
            "🚶 Фізична активність сприяє здоровому росту волосся.",
            "🍳 Білок — важливий компонент для зміцнення волосся.",
            "🌰 Горіхи та насіння містять корисні жири, які живлять волосся.",
            "💇 Часті стрижки не роблять волосся густішим, але запобігають ламкості."
        ];

        function getRandomFact() {
            return funFacts[Math.floor(Math.random() * funFacts.length)];
        }

        function updateFact() {
            const factBox = document.getElementById("fact-container");
            const factText = document.getElementById("fun-fact");

            factBox.classList.add("fade-out");
            setTimeout(() => {
                factText.textContent = getRandomFact();
                factBox.classList.remove("fade-out");
                factBox.classList.add("fade-in");
            }, 800);
        }

        document.getElementById('upload-form').addEventListener('submit', function(event) {
            event.preventDefault();

            let formData = new FormData(this);
            let loadingContainer = document.getElementById("loading-container");

            loadingContainer.classList.remove("d-none");
            setInterval(updateFact, 5000);

            fetch("{{ route('process.image') }}", {
                    method: "POST",
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = "/result/show/" + data.trackingId;
                    }
                });
        });

        document.getElementById('imageUpload').addEventListener('change', function() {
            document.getElementById("file-name").textContent = this.files[0]?.name ||
                "Натисніть або перетягніть фото сюди";
        });
    </script>
@endsection
