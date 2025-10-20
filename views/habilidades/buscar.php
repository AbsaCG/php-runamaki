<?php 
$pageTitle = 'Buscar Servicios - Runa Maki';
$currentPage = 'buscar';
require_once ROOT_PATH . '/views/layout/header.php';
?>

<div class="container">
    <!-- Header -->
    <div style="margin-bottom: 2rem;">
        <h1>🔍 Buscar Servicios</h1>
        <p class="text-muted">Encuentra habilidades disponibles en la comunidad</p>
    </div>

    <!-- Buscador y Filtros -->
    <div class="card" style="margin-bottom: 2rem;">
        <form action="index.php?page=buscar" method="GET" style="display: grid; grid-template-columns: 1fr auto auto; gap: 1rem; align-items: end;">
            <input type="hidden" name="page" value="buscar">
            
            <div class="form-group" style="margin-bottom: 0;">
                <label for="busqueda" class="form-label">Buscar por palabra clave</label>
                <input 
                    type="text" 
                    id="busqueda" 
                    name="q" 
                    class="form-input" 
                    placeholder="Ej: guitarra, reparación, costura..."
                    value="<?= e($filtro_busqueda ?? '') ?>"
                >
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="categoria" class="form-label">Categoría</label>
                <select id="categoria" name="categoria" class="form-select">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($filtro_categoria == $cat['id']) ? 'selected' : '' ?>>
                        <?= e($cat['nombre']) ?> (<?= $cat['total_habilidades'] ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary" style="white-space: nowrap;">
                🔍 Buscar
            </button>
        </form>
    </div>

    <!-- Categorías Rápidas -->
    <?php if (empty($filtro_categoria) && empty($filtro_busqueda)): ?>
    <div style="margin-bottom: 2rem;">
        <h3 style="margin-bottom: 1rem;">Explorar por categoría</h3>
        <div class="grid grid-cols-4">
            <?php foreach ($categorias as $cat): ?>
            <a href="index.php?page=buscar&categoria=<?= $cat['id'] ?>" class="card" style="text-align: center; cursor: pointer; text-decoration: none; transition: all 0.3s;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">
                    <?php
                    $iconos = [
                        'Educación' => '📚',
                        'Tecnología' => '💻',
                        'Manualidades' => '✂️',
                        'Idiomas' => '🗣️',
                        'Cocina' => '🍳',
                        'Reparaciones' => '🔧',
                        'Arte' => '🎨',
                        'Música' => '🎸'
                    ];
                    echo $iconos[$cat['nombre']] ?? '⚡';
                    ?>
                </div>
                <h4><?= e($cat['nombre']) ?></h4>
                <p class="text-sm text-muted"><?= $cat['total_habilidades'] ?> servicios</p>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Resultados -->
    <div style="margin-bottom: 1rem;">
        <h2>
            <?php if ($filtro_busqueda || $filtro_categoria): ?>
                Resultados de búsqueda
            <?php else: ?>
                Todos los servicios disponibles
            <?php endif ?>
        </h2>
        <?php if ($filtro_busqueda): ?>
        <p class="text-muted">
            Búsqueda: "<strong><?= e($filtro_busqueda) ?></strong>"
            <a href="index.php?page=buscar" style="margin-left: 1rem;">✕ Limpiar</a>
        </p>
        <?php endif; ?>
    </div>

    <?php if (empty($habilidades)): ?>
        <div class="card text-center" style="padding: 4rem;">
            <div style="font-size: 4rem; margin-bottom: 1rem;">🔍</div>
            <h2>No se encontraron servicios</h2>
            <p class="text-muted">Intenta con otros términos de búsqueda o explora otras categorías</p>
            <a href="index.php?page=buscar" class="btn btn-outline mt-4">
                Ver todos los servicios
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-3">
            <?php foreach ($habilidades as $habilidad): ?>
            <div class="card" style="cursor: pointer;" onclick="window.location='index.php?page=habilidad-detalle&id=<?= $habilidad['id'] ?>'">
                <!-- Header con Usuario -->
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
                    <img 
                        src="https://ui-avatars.com/api/?name=<?= urlencode($habilidad['usuario_nombre']) ?>&background=C86F3C&color=fff" 
                        alt="Avatar" 
                        class="avatar"
                    >
                    <div style="flex: 1;">
                        <div style="font-weight: 600;"><?= e($habilidad['usuario_nombre']) ?></div>
                        <div class="text-sm text-muted">⭐ <?= number_format((float)($habilidad['usuario_reputacion'] ?? 5.0), 1) ?></div>
                    </div>
                    <div class="badge badge-primary">
                        <?= e($habilidad['categoria_nombre']) ?>
                    </div>
                </div>

                <!-- Contenido -->
                <h3 style="margin-bottom: 0.75rem;"><?= e($habilidad['titulo']) ?></h3>
                <p class="text-muted text-sm" style="margin-bottom: 1rem; line-height: 1.4;">
                    <?= e(substr($habilidad['descripcion'], 0, 120)) ?><?= strlen($habilidad['descripcion']) > 120 ? '...' : '' ?>
                </p>

                <!-- Footer con Info -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                    <div style="display: flex; gap: 1rem;">
                        <span class="text-sm">
                            <span style="opacity: 0.6;">⏱️</span> <?= $habilidad['horas_ofrecidas'] ?>h
                        </span>
                        <span class="text-sm">
                            <span style="opacity: 0.6;">👁️</span> <?= $habilidad['visitas'] ?>
                        </span>
                    </div>
                    <div class="badge badge-success" style="padding: 0.5rem 1rem;">
                        💰 <strong><?= $habilidad['puntos_sugeridos'] ?></strong> pts
                    </div>
                </div>

                <!-- Botón de Acción -->
                <button 
                    onclick="event.stopPropagation(); window.location='index.php?page=habilidad-detalle&id=<?= $habilidad['id'] ?>'" 
                    class="btn btn-primary"
                    style="width: 100%; margin-top: 1rem;">
                    Ver detalles y solicitar →
                </button>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once ROOT_PATH . '/views/layout/footer.php'; ?>
