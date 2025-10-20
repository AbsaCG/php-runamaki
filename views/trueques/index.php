<?php 
$pageTitle = 'Mis Trueques - Runa Maki';
$currentPage = 'trueques';
require_once ROOT_PATH . '/views/layout/header.php';
?>

<div class="container">
    <!-- Header -->
    <div style="margin-bottom: 2rem;">
        <h1>🤝 Mis Trueques</h1>
        <p class="text-muted">Gestiona tus intercambios de servicios</p>
    </div>

    <!-- Filtros -->
    <div style="display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
        <a href="index.php?page=trueques" 
           class="btn <?= empty($filtro_estado) ? 'btn-primary' : 'btn-outline' ?>">
            📋 Todos
        </a>
        <a href="index.php?page=trueques&estado=pendiente" 
           class="btn <?= $filtro_estado === 'pendiente' ? 'btn-primary' : 'btn-outline' ?>">
            ⏳ Pendientes
        </a>
        <a href="index.php?page=trueques&estado=aceptado" 
           class="btn <?= $filtro_estado === 'aceptado' ? 'btn-primary' : 'btn-outline' ?>">
            🔄 En Progreso
        </a>
        <a href="index.php?page=trueques&estado=completado" 
           class="btn <?= $filtro_estado === 'completado' ? 'btn-primary' : 'btn-outline' ?>">
            ✅ Completados
        </a>
        <a href="index.php?page=trueques&estado=rechazado" 
           class="btn <?= $filtro_estado === 'rechazado' ? 'btn-primary' : 'btn-outline' ?>">
            ❌ Rechazados
        </a>
    </div>

    <!-- Lista de Trueques -->
    <?php if (empty($trueques)): ?>
        <div class="card text-center" style="padding: 4rem;">
            <div style="font-size: 4rem; margin-bottom: 1rem;">🤝</div>
            <h2>No tienes trueques <?= $filtro_estado ? 'con estado "' . $filtro_estado . '"' : '' ?></h2>
            <p class="text-muted" style="max-width: 500px; margin: 1rem auto;">
                Comienza a intercambiar servicios con otros miembros de la comunidad
            </p>
            <a href="index.php?page=buscar" class="btn btn-primary btn-lg mt-4">
                Buscar servicios disponibles
            </a>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <?php foreach ($trueques as $trueque): ?>
            <div class="card" style="cursor: pointer;" onclick="window.location='index.php?page=trueque-detalle&id=<?= $trueque['id'] ?>'">
                <div style="display: grid; grid-template-columns: auto 1fr auto; gap: 1.5rem; align-items: center;">
                    <!-- Avatar y Nombre -->
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <img 
                            src="https://ui-avatars.com/api/?name=<?= urlencode($trueque['partner_nombre']) ?>&background=C86F3C&color=fff" 
                            alt="Avatar" 
                            class="avatar-lg"
                        >
                        <div>
                            <h3 style="margin-bottom: 0.25rem;"><?= e($trueque['partner_nombre']) ?></h3>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span class="text-sm text-muted">⭐ <?= number_format($trueque['partner_reputacion'] ?? 5.0, 1) ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Detalles del Intercambio -->
                    <div>
                        <div style="display: grid; grid-template-columns: 1fr auto 1fr; gap: 1rem; align-items: center;">
                            <div style="padding: 1rem; background-color: var(--bg-muted); border-radius: var(--radius);">
                                <div class="text-sm text-muted" style="margin-bottom: 0.25rem;">Yo ofrezco:</div>
                                <div style="font-weight: 600;"><?= e($trueque['mi_servicio']) ?></div>
                            </div>
                            
                            <div style="text-align: center; font-size: 1.5rem;">
                                ⇄
                            </div>
                            
                            <div style="padding: 1rem; background-color: rgba(90, 139, 74, 0.1); border-radius: var(--radius);">
                                <div class="text-sm text-muted" style="margin-bottom: 0.25rem;">Recibo:</div>
                                <div style="font-weight: 600;"><?= e($trueque['su_servicio']) ?></div>
                            </div>
                        </div>
                        
                        <div style="display: flex; gap: 1rem; margin-top: 1rem; align-items: center;">
                            <span class="badge">
                                💰 <strong><?= $trueque['puntos_intercambio'] ?></strong> Puntos Runa
                            </span>
                            <span class="text-sm text-muted">
                                📅 <?= timeAgo($trueque['fecha_creacion']) ?>
                            </span>
                        </div>
                    </div>

                    <!-- Estado y Acciones -->
                    <div style="text-align: right;">
                        <?php
                        $estadoBadges = [
                            'pendiente' => ['⏳', 'badge-primary', 'Pendiente'],
                            'aceptado' => ['🔄', 'badge', 'En Progreso'],
                            'completado' => ['✅', 'badge-success', 'Completado'],
                            'rechazado' => ['❌', 'badge', 'Rechazado'],
                            'cancelado' => ['⛔', 'badge', 'Cancelado']
                        ];
                        $badge = $estadoBadges[$trueque['estado']] ?? ['📋', 'badge', 'Desconocido'];
                        ?>
                        <div class="badge <?= $badge[1] ?>" style="margin-bottom: 1rem; padding: 0.5rem 1rem;">
                            <?= $badge[0] ?> <?= $badge[2] ?>
                        </div>
                        <div>
                            <button 
                                onclick="event.stopPropagation(); window.location='index.php?page=trueque-detalle&id=<?= $trueque['id'] ?>'" 
                                class="btn btn-sm btn-primary">
                                Ver detalles →
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once ROOT_PATH . '/views/layout/footer.php'; ?>
