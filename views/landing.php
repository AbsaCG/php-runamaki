<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Runa Maki - Trueque Digital, Comunidad Real</title>
    <link rel="stylesheet" href="public/css/styles.css">
    <style>
        .hero {
            padding: 4rem 0;
            text-align: center;
            background: linear-gradient(135deg, var(--bg-main) 0%, var(--bg-muted) 100%);
        }
        
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .hero .highlight {
            color: var(--color-primary);
        }
        
        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
            flex-wrap: wrap;
        }
        
        .features {
            padding: 4rem 0;
        }
        
        .feature-card {
            text-align: center;
            padding: 2rem;
        }
        
        .feature-icon {
            width: 60px;
            height: 60px;
            background-color: rgba(200, 111, 60, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
        }
        
        .carousel {
            padding: 4rem 0;
            background-color: var(--bg-card);
        }
        
        .carousel-items {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .carousel-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: var(--radius) var(--radius) 0 0;
        }
        
        .cta {
            padding: 4rem 0;
            text-align: center;
            background: linear-gradient(135deg, rgba(200, 111, 60, 0.1) 0%, rgba(90, 139, 74, 0.1) 100%);
            border-radius: var(--radius);
            margin: 2rem 0;
        }
        
        .footer {
            background-color: var(--bg-card);
            border-top: 1px solid var(--border-color);
            padding: 2rem 0;
            text-align: center;
            color: var(--text-secondary);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <div class="logo-icon">🤝</div>
                <div>
                    <div>Runa Maki</div>
                    <small style="font-size: 0.75rem; color: var(--text-secondary);">Trueque digital, comunidad real</small>
                </div>
            </div>
            <div style="display: flex; gap: 1rem;">
                <a href="index.php?page=login" class="btn btn-outline">Iniciar Sesión</a>
                <a href="index.php?page=register" class="btn btn-primary">Registrarme</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="badge badge-primary" style="margin-bottom: 1rem;">
                ✨ Valoramos tus conocimientos locales
            </div>
            <h1>
                Intercambia servicios y habilidades
                <br>
                <span class="highlight">sin usar dinero</span>
            </h1>
            <p class="text-lg text-muted" style="max-width: 600px; margin: 1rem auto;">
                Runa Maki es una plataforma de economía solidaria para la comunidad cusqueña. 
                Comparte lo que sabes, aprende de otros y fortalece nuestra comunidad.
            </p>
            <div class="hero-buttons">
                <a href="index.php?page=register" class="btn btn-primary btn-lg">Empezar ahora</a>
                <a href="index.php?page=guest" class="btn btn-outline btn-lg">Ingresar como invitado</a>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="features">
        <div class="container">
            <div class="grid grid-cols-3">
                <div class="feature-card card">
                    <div class="feature-icon">👥</div>
                    <h3>Comunidad Local</h3>
                    <p class="text-muted">
                        Conecta con vecinos y construye relaciones de confianza en Cusco
                    </p>
                </div>
                <div class="feature-card card">
                    <div class="feature-icon">📚</div>
                    <h3>Aprende y Enseña</h3>
                    <p class="text-muted">
                        Comparte tus habilidades y aprende nuevas sin costo monetario
                    </p>
                </div>
                <div class="feature-card card">
                    <div class="feature-icon">💰</div>
                    <h3>Puntos Runa</h3>
                    <p class="text-muted">
                        Sistema justo de intercambio basado en tiempo y esfuerzo
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Carousel de Habilidades -->
    <section class="carousel">
        <div class="container">
            <h2 class="text-center mb-4">Ejemplos de habilidades en nuestra comunidad</h2>
            <div class="carousel-items">
                <div class="carousel-card card">
                    <img src="https://images.unsplash.com/photo-1758524944402-1903b38f848f" alt="Clases de guitarra">
                    <div class="p-4">
                        <h4>Clases de guitarra</h4>
                        <p class="text-muted text-sm">Aprende a tocar desde cero</p>
                        <div class="badge badge-primary mt-2">🎸 Música</div>
                    </div>
                </div>
                <div class="carousel-card card">
                    <img src="https://images.unsplash.com/photo-1560165143-fa7e2d9e594c" alt="Reparación de laptops">
                    <div class="p-4">
                        <h4>Reparación de laptops</h4>
                        <p class="text-muted text-sm">Soluciones tecnológicas</p>
                        <div class="badge badge-primary mt-2">💻 Tecnología</div>
                    </div>
                </div>
                <div class="carousel-card card">
                    <img src="https://images.unsplash.com/photo-1759738096144-b43206226765" alt="Costura tradicional">
                    <div class="p-4">
                        <h4>Costura tradicional</h4>
                        <p class="text-muted text-sm">Técnicas ancestrales</p>
                        <div class="badge badge-primary mt-2">✂️ Manualidades</div>
                    </div>
                </div>
                <div class="carousel-card card">
                    <img src="https://images.unsplash.com/photo-1563807893528-313039d9761f" alt="Tutorías de Quechua">
                    <div class="p-4">
                        <h4>Tutorías de Quechua</h4>
                        <p class="text-muted text-sm">Idioma ancestral</p>
                        <div class="badge badge-primary mt-2">🗣️ Idiomas</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2>¿Listo para empezar?</h2>
            <p class="text-muted" style="max-width: 600px; margin: 1rem auto;">
                Únete a cientos de cusqueños que ya están compartiendo sus conocimientos 
                y fortaleciendo la comunidad.
            </p>
            <a href="index.php?page=register" class="btn btn-primary btn-lg mt-4">
                Crear mi cuenta gratis
            </a>
            <p class="text-sm text-muted mt-4" style="font-style: italic;">
                "Tu habilidad también vale. Ayuda a otro, gana puntos Runa."
            </p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>© 2025 Runa Maki - Economía Solidaria Cusco</p>
            <p class="text-sm">Plataforma de intercambio de habilidades locales</p>
        </div>
    </footer>

    <script>
        // Toggle dark mode (guardado en localStorage)
        const darkModeToggle = document.createElement('button');
        darkModeToggle.className = 'btn btn-sm';
        darkModeToggle.style.cssText = 'position: fixed; bottom: 20px; right: 20px; border-radius: 50%; width: 50px; height: 50px;';
        darkModeToggle.innerHTML = '🌙';
        document.body.appendChild(darkModeToggle);

        const isDark = localStorage.getItem('darkMode') === 'true';
        if (isDark) {
            document.body.classList.add('dark-mode');
            darkModeToggle.innerHTML = '☀️';
        }

        darkModeToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            const isDarkNow = document.body.classList.contains('dark-mode');
            localStorage.setItem('darkMode', isDarkNow);
            darkModeToggle.innerHTML = isDarkNow ? '☀️' : '🌙';
        });
    </script>
</body>
</html>
