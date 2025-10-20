<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Runa Maki</title>
    <link rel="stylesheet" href="public/css/styles.css">
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: linear-gradient(135deg, var(--bg-main) 0%, var(--bg-muted) 100%);
        }
        
        .auth-card {
            width: 100%;
            max-width: 450px;
            background-color: var(--bg-card);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            padding: 2.5rem;
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .auth-logo {
            width: 80px;
            height: 80px;
            background-color: var(--color-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 3rem;
        }
        
        .divider {
            text-align: center;
            margin: 1.5rem 0;
            color: var(--text-secondary);
            position: relative;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 40%;
            height: 1px;
            background-color: var(--border-color);
        }
        
        .divider::before {
            left: 0;
        }
        
        .divider::after {
            right: 0;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">🤝</div>
                <h1>Bienvenido de vuelta</h1>
                <p class="text-muted">Inicia sesión en Runa Maki</p>
            </div>

            <?php 
            $flash = getFlashMessage();
            if ($flash): 
            ?>
                <div class="alert alert-<?= $flash['type'] === 'error' ? 'error' : 'success' ?>">
                    <?= e($flash['message']) ?>
                </div>
            <?php endif; ?>

            <form action="index.php?page=login-submit" method="POST">
                <div class="form-group">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input" 
                        placeholder="tu@email.com"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Contraseña</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input" 
                        placeholder="••••••••"
                        required
                    >
                </div>

                <div class="form-group" style="margin-bottom: 0.5rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="remember" style="cursor: pointer;">
                        <span class="text-sm">Recordar sesión</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                    Iniciar Sesión
                </button>
            </form>

            <div class="divider">o</div>

            <a href="index.php?page=guest" class="btn btn-outline" style="width: 100%; justify-content: center;">
                Ingresar como invitado
            </a>

            <p class="text-center text-sm mt-4" style="color: var(--text-secondary);">
                ¿No tienes cuenta? 
                <a href="index.php?page=register" style="color: var(--color-primary); font-weight: 600;">
                    Regístrate aquí
                </a>
            </p>

            <p class="text-center text-sm mt-4">
                <a href="index.php" style="color: var(--text-secondary);">
                    ← Volver al inicio
                </a>
            </p>
        </div>
    </div>

    <script>
        // Dark mode toggle
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
