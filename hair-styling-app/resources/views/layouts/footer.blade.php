<footer class="footer text-center text-md-left py-5 mt-5">
    <div class="container">
        <div class="row">
            <!-- Колонка 1: Великий логотип -->
            <div class="col-md-3 mb-4 mb-md-0 text-center">
                <img src="{{ asset('assets/logo.png') }}" alt="HairMorph Logo" class="footer-logo-img">
            </div>

            <!-- Колонка 2: Навігація -->
            <div class="col-md-3 mb-4 mb-md-0">
                <h5 class="footer-title">Навігація</h5>
                <ul class="footer-nav list-unstyled">
                    <li><a href="{{ route('upload.form') }}">Підбір зачіски</a></li>
                    <li><a href="{{ route('hairstyles.index') }}" class="nav-link">Зачіски</a></li>
                    <li><a href="{{ route('about') }}">Про нас</a></li>
                    <li><a href="#">Контакти</a></li>
                </ul>
            </div>

            <!-- Колонка 3: Контактна інформація -->
            <div class="col-md-3 mb-4 mb-md-0">
                <h5 class="footer-title">Контакти</h5>
                <p class="footer-contact"><i class="fas fa-envelope"></i> support@hairmorph.com</p>
                <p class="footer-contact"><i class="fas fa-phone"></i> +380 50 123 4567</p>
                <p class="footer-contact"><i class="fas fa-map-marker-alt"></i> Київ, Україна</p>
            </div>

            <!-- Колонка 4: Соцмережі -->
            <div class="col-md-3">
                <h5 class="footer-title">Ми в соцмережах</h5>
                <div class="footer-social">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-telegram-plane"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-github"></i></a>
                </div>
            </div>
        </div>

        <!-- Нижній рядок з копірайтом -->
        <div class="footer-bottom text-center mt-4">
            <p class="mb-0">© {{ date('Y') }} HairMorph. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Стилі для футера -->
<style>
    .footer {
        background-color: #02012e;
        /* Новий темний фон */
        color: #E0E0E0;
    }

    .footer-logo-img {
        max-width: 150px;
        height: auto;
    }

    .footer-title {
        font-size: 1.2rem;
        margin-bottom: 10px;
        font-weight: bold;
        color: #FFFFFF;
    }

    .footer-nav {
        padding-left: 0;
    }

    .footer-nav li {
        margin-bottom: 8px;
    }

    .footer-nav a {
        color: #E0E0E0;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .footer-nav a:hover {
        color: var(--color-accent);
    }

    .footer-contact {
        margin: 5px 0;
        font-size: 0.95rem;
    }

    .footer-contact i {
        margin-right: 8px;
        color: var(--color-accent);
    }

    .footer-social {
        margin-top: 10px;
    }

    .social-link {
        color: #E0E0E0;
        font-size: 1.4rem;
        margin: 0 10px;
        transition: color 0.3s ease;
    }

    .social-link:hover {
        color: var(--color-accent);
    }

    .footer-bottom {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding-top: 10px;
        font-size: 0.9rem;
        color: #B0B0B0;
    }
</style>
