<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Runa Maki</title>
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
            max-width: 500px;
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
        
        .nivel-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
            margin-top: 0.5rem;
        }
        
        .nivel-card {
            padding: 1rem;
            border: 2px solid var(--border-color);
            border-radius: var(--radius);
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .nivel-card input[type="radio"] {
            display: none;
        }
        
        .nivel-card:hover {
            border-color: var(--color-primary);
        }
        
        .nivel-card input[type="radio"]:checked + label {
            color: var(--color-primary);
        }
        
        .nivel-card.selected {
            border-color: var(--color-primary);
            background-color: rgba(200, 111, 60, 0.05);
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">🤝</div>
                <h1>Únete a Runa Maki</h1>
                <p class="text-muted">Comienza a intercambiar habilidades</p>
            </div>

            <?php 
            $flash = getFlashMessage();
            if ($flash): 
            ?>
                <div class="alert alert-<?= $flash['type'] === 'error' ? 'error' : 'success' ?>">
                    <?= e($flash['message']) ?>
                </div>
            <?php endif; ?>

            <form action="index.php?page=register-submit" method="POST" id="registerForm">
                <div class="form-group">
                    <label for="nombre" class="form-label">Nombre Completo</label>
                    <input 
                        type="text" 
                        id="nombre" 
                        name="nombre" 
                        class="form-input" 
                        placeholder="Tu nombre completo"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input" 
                        placeholder="tu@email.com"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Contraseña</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input" 
                        placeholder="Mínimo 6 caracteres"
                        minlength="6"
                        required
                    >
                    <small class="text-muted text-sm">Debe tener al menos 6 caracteres</small>
                </div>

                <div class="form-group">
                    <label for="password_confirm" class="form-label">Confirmar Contraseña</label>
                    <input 
                        type="password" 
                        id="password_confirm" 
                        name="password_confirm" 
                        class="form-input" 
                        placeholder="Repite tu contraseña"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">¿Cuál es tu nivel de experiencia?</label>
                    <div class="nivel-cards">
                        <div class="nivel-card" data-nivel="Principiante">
                            <input type="radio" name="nivel" value="Principiante" id="principiante" checked>
                            <label for="principiante" style="cursor: pointer; display: block;">
                                <div style="font-size: 1.5rem; margin-bottom: 0.25rem;">🌱</div>
                                <div class="text-sm" style="font-weight: 600;">Principiante</div>
                            </label>
                        </div>
                        <div class="nivel-card" data-nivel="Intermedio">
                            <input type="radio" name="nivel" value="Intermedio" id="intermedio">
                            <label for="intermedio" style="cursor: pointer; display: block;">
                                <div style="font-size: 1.5rem; margin-bottom: 0.25rem;">🌿</div>
                                <div class="text-sm" style="font-weight: 600;">Intermedio</div>
                            </label>
                        </div>
                        <div class="nivel-card" data-nivel="Experto">
                            <input type="radio" name="nivel" value="Experto" id="experto">
                            <label for="experto" style="cursor: pointer; display: block;">
                                <div style="font-size: 1.5rem; margin-bottom: 0.25rem;">🌳</div>
                                <div class="text-sm" style="font-weight: 600;">Experto</div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: start; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="terminos" required style="cursor: pointer; margin-top: 0.25rem;">
                        <span class="text-sm">
                            Acepto los <a href="#" style="color: var(--color-primary);">términos y condiciones</a> 
                            y la <a href="#" style="color: var(--color-primary);">política de privacidad</a>
                        </span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                    Crear mi cuenta
                </button>
            </form>

            <p class="text-center text-sm mt-4" style="color: var(--text-secondary);">
                ¿Ya tienes cuenta? 
                <a href="index.php?page=login" style="color: var(--color-primary); font-weight: 600;">
                    Inicia sesión
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

        // Nivel selection visual
        const nivelCards = document.querySelectorAll('.nivel-card');
        nivelCards.forEach(card => {
            const input = card.querySelector('input[type="radio"]');
            
            if (input.checked) {
                card.classList.add('selected');
            }
            
            card.addEventListener('click', () => {
                nivelCards.forEach(c => c.classList.remove('selected'));
                card.classList.add('selected');
                input.checked = true;
            });
        });

        // Validar contraseñas
        const form = document.getElementById('registerForm');
        form.addEventListener('submit', (e) => {
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirm').value;
            
            if (password !== passwordConfirm) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
                return false;
            }
        });
    </script>
</body>
</html>
