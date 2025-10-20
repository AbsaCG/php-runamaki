<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Runa Maki' ?></title>
    <link rel="stylesheet" href="public/css/styles.css">
</head>
<body>
    <script>
        // Aplicar dark mode desde localStorage antes de renderizar
        if (localStorage.getItem('darkMode') === 'true') {
            document.body.classList.add('dark-mode');
        }
    </script>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <a href="index.php?page=dashboard" class="logo">
                <div class="logo-icon">🤝</div>
                <div>
                    <div>Runa Maki</div>
                    <small style="font-size: 0.75rem; color: var(--text-secondary);">Comunidad de trueque</small>
                </div>
            </a>

            <div style="display: flex; align-items: center; gap: 1rem;">
                <!-- Puntos Runa -->
                <div class="badge badge-primary" style="padding: 0.5rem 1rem;">
                    <span style="font-size: 1.25rem;">💰</span>
                    <strong><?= $_SESSION['puntos_runa'] ?? 0 ?></strong> Puntos Runa
                </div>

                <!-- Usuario -->
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="text-align: right;">
                        <div style="font-weight: 600;"><?= e($_SESSION['nombre'] ?? 'Usuario') ?></div>
                        <div class="text-sm text-muted">⭐ <?= number_format((float)($_SESSION['reputacion'] ?? 5.0), 1) ?></div>
                    </div>
                    <img 
                        src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['nombre'] ?? 'U') ?>&background=C86F3C&color=fff" 
                        alt="Avatar" 
                        class="avatar"
                    >
                </div>

                <!-- Dark Mode Toggle -->
                <button id="darkModeToggle" class="btn btn-sm btn-outline" style="border-radius: 50%; width: 40px; height: 40px; padding: 0;">
                    <span id="darkModeIcon">🌙</span>
                </button>

                <!-- Menú móvil -->
                <button id="menuToggle" class="btn btn-sm btn-outline" style="display: none; border-radius: 50%; width: 40px; height: 40px; padding: 0;">
                    ☰
                </button>
            </div>
        </div>
    </nav>

    <div style="display: flex;">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <ul class="sidebar-menu">
                <li>
                    <a href="index.php?page=dashboard" class="<?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
                        <span style="font-size: 1.25rem;">🏠</span>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="index.php?page=habilidades" class="<?= ($currentPage ?? '') === 'habilidades' ? 'active' : '' ?>">
                        <span style="font-size: 1.25rem;">⚡</span>
                        Mis Habilidades
                    </a>
                </li>
                <li>
                    <a href="index.php?page=buscar" class="<?= ($currentPage ?? '') === 'buscar' ? 'active' : '' ?>">
                        <span style="font-size: 1.25rem;">🔍</span>
                        Buscar Servicios
                    </a>
                </li>
                <li>
                    <a href="index.php?page=trueques" class="<?= ($currentPage ?? '') === 'trueques' ? 'active' : '' ?>">
                        <span style="font-size: 1.25rem;">🤝</span>
                        Mis Trueques
                    </a>
                </li>
                <li>
                    <a href="index.php?page=perfil" class="<?= ($currentPage ?? '') === 'perfil' ? 'active' : '' ?>">
                        <span style="font-size: 1.25rem;">👤</span>
                        Mi Perfil
                    </a>
                </li>

                <?php if (isAdmin()): ?>
                <li style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                    <a href="index.php?page=admin" class="<?= ($currentPage ?? '') === 'admin' ? 'active' : '' ?>">
                        <span style="font-size: 1.25rem;">⚙️</span>
                        Administración
                    </a>
                </li>
                <?php endif; ?>

                <li style="margin-top: auto;">
                    <a href="index.php?page=logout" style="color: #d4183d;">
                        <span style="font-size: 1.25rem;">🚪</span>
                        Cerrar Sesión
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <?php 
            $flash = getFlashMessage();
            if ($flash): 
            ?>
                <div class="alert alert-<?= $flash['type'] === 'error' ? 'error' : ($flash['type'] === 'info' ? 'info' : 'success') ?>" style="margin-bottom: 2rem;">
                    <?= e($flash['message']) ?>
                </div>
            <?php endif; ?>
