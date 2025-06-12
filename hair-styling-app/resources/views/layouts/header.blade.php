<header class="header d-flex justify-content-between align-items-center px-4 py-3">
    <div class="logo-container d-flex align-items-center">
        <a href="/">
            <img src="{{ asset('assets/ico.png') }}" alt="HairMorph Logo" class="logo-img me-2">
        </a>
        <span class="logo-text fw-bold">HairMorph</span>
    </div>

    <!-- Навігація для десктопу -->
    <nav class="nav-links d-none d-md-flex gap-4">
        <a href="{{ route('upload.form') }}" class="nav-link">Підбір зачіски</a>
        <a href="{{ route('hairstyles.index') }}" class="nav-link">Зачіски</a>
        <a href="{{ route('about') }}" class="nav-link">Про нас</a>
        <a href="{{ route('contact') }}" class="nav-link">Контакти</a>
    </nav>

    <!-- Іконка користувача / Вхід / Вихід -->
    <nav class="nav d-flex align-items-center">
        @auth
            <div class="user-menu">
                <div class="user-avatar-container">
                    <img src="{{ asset('storage/' . auth()->user()->photo) }}" alt="User Avatar" class="user-avatar"
                        id="avatar-click">
                    <div class="dropdown-menu" id="profile-dropdown">
                        <a href="{{ route('profile') }}">👤 Мій профіль</a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="logout-btn">🚪 Вийти</button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <a href="{{ route('login') }}" class="nav-link login-btn">Увійти</a>
        @endauth
    </nav>
</header>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const avatar = document.getElementById("avatar-click");
        const dropdown = document.getElementById("profile-dropdown");

        if (avatar) {
            avatar.addEventListener("click", function(event) {
                event.stopPropagation();
                dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
            });

            document.addEventListener("click", function(event) {
                if (!dropdown.contains(event.target) && event.target !== avatar) {
                    dropdown.style.display = "none";
                }
            });
        }
    });
</script>
