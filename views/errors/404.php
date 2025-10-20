<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada | Runa Maki</title>
    <link rel="stylesheet" href="public/css/styles.css">
    <style>
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: linear-gradient(135deg, var(--bg-main) 0%, var(--bg-muted) 100%);
        }
        
        .error-card {
            text-align: center;
            max-width: 600px;
            background-color: var(--bg-card);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            padding: 3rem;
        }
        
        .error-code {
            font-size: 6rem;
            font-weight: 700;
            color: var(--color-primary);
            line-height: 1;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-card">
            <div class="error-code">404</div>
            <h1 style="margin-bottom: 1rem;">Página no encontrada</h1>
            <p class="text-muted" style="margin-bottom: 2rem;">
                Lo sentimos, la página que buscas no existe o ha sido movida.
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <a href="index.php" class="btn btn-primary">
                    Ir al inicio
                </a>
                <?php if (isAuthenticated()): ?>
                <a href="index.php?page=dashboard" class="btn btn-outline">
                    Ir al dashboard
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        const isDark = localStorage.getItem('darkMode') === 'true';
        if (isDark) {
            document.body.classList.add('dark-mode');
        }
    </script>
</body>
</html>
