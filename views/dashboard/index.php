<?php 
$pageTitle = 'Dashboard - Runa Maki';
$currentPage = 'dashboard';
require_once ROOT_PATH . '/views/layout/header.php';
?>

<div class="container">
    <!-- Header del Dashboard -->
    <div style="margin-bottom: 2rem;">
        <h1>¡Bienvenido, <?= e($usuario['nombre'] ?? 'Invitado') ?>! 👋</h1>
        <p class="text-muted">Aquí está tu resumen de actividad en la comunidad</p>
    </div>

    <!-- Estadísticas Principales -->
    <div class="grid grid-cols-4" style="margin-bottom: 2rem;">
        <div class="card">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="font-size: 2.5rem;">💰</div>
                <div>
                    <div class="text-sm text-muted">Puntos Runa</div>
                    <div style="font-size: 1.5rem; font-weight: 600;"><?= isset($usuario['puntos_runa']) ? (int)$usuario['puntos_runa'] : 0 ?></div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="font-size: 2.5rem;">🤝</div>
                <div>
                    <div class="text-sm text-muted">Trueques Completados</div>
                    <div style="font-size: 1.5rem; font-weight: 600;"><?= $estadisticas['trueques_completados'] ?? 0 ?></div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="font-size: 2.5rem;">⚡</div>
                <div>
                    <div class="text-sm text-muted">Habilidades Activas</div>
                    <div style="font-size: 1.5rem; font-weight: 600;"><?= $estadisticas['habilidades_activas'] ?? 0 ?></div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="font-size: 2.5rem;">⭐</div>
                <div>
                    <div class="text-sm text-muted">Reputación</div>
                    <div style="font-size: 1.5rem; font-weight: 600;"><?= number_format((float)($usuario['reputacion'] ?? 0.0), 1) ?>/5.0</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas de Trueques Pendientes -->
    <?php if ($trueques_pendientes > 0): ?>
    <div class="alert alert-info" style="margin-bottom: 2rem;">
        <strong>📬 Tienes <?= $trueques_pendientes ?> trueque(s) pendiente(s) por revisar.</strong>
        <a href="index.php?page=trueques&estado=pendiente" style="margin-left: 1rem;">Ver ahora →</a>
    </div>
    <?php endif; ?>

    <div class="grid" style="grid-template-columns: 2fr 1fr; gap: 2rem;">
        <!-- Ofertas Destacadas -->
        <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h2>🔥 Ofertas Destacadas</h2>
                <a href="index.php?page=buscar" class="btn btn-sm btn-outline">Ver todas</a>
            </div>

            <?php if (empty($ofertas_destacadas)): ?>
                <div class="card text-center" style="padding: 3rem;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">🔍</div>
                    <p class="text-muted">No hay ofertas disponibles por el momento</p>
                    <a href="index.php?page=habilidades" class="btn btn-primary mt-4">Publica tu primera habilidad</a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-2" style="gap: 1rem;">
                    <?php foreach ($ofertas_destacadas as $oferta): ?>
                    <div class="card" style="cursor: pointer;" onclick="window.location='index.php?page=habilidad-detalle&id=<?= $oferta['id'] ?>'">
                        <div style="display: flex; gap: 1rem;">
                            <img 
                                src="https://ui-avatars.com/api/?name=<?= urlencode($oferta['usuario_nombre']) ?>&background=C86F3C&color=fff" 
                                alt="Avatar" 
                                class="avatar"
                            >
                            <div style="flex: 1;">
                                <h4 style="margin-bottom: 0.25rem;"><?= e($oferta['titulo']) ?></h4>
                                <p class="text-sm text-muted" style="margin-bottom: 0.5rem;">
                                    por <?= e($oferta['usuario_nombre']) ?>
                                </p>
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                    <span class="badge badge-primary">
                                        <?= $oferta['categoria_nombre'] ?>
                                    </span>
                                    <span class="badge">
                                        💰 <?= $oferta['puntos_sugeridos'] ?> puntos
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Panel Lateral -->
        <div>
            <!-- Acciones Rápidas -->
            <div class="card" style="margin-bottom: 1.5rem;">
                <h3 style="margin-bottom: 1rem;">⚡ Acciones Rápidas</h3>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <a href="index.php?page=habilidades" class="btn btn-primary" style="width: 100%; justify-content: center;">
                        ➕ Publicar Habilidad
                    </a>
                    <a href="index.php?page=buscar" class="btn btn-outline" style="width: 100%; justify-content: center;">
                        🔍 Buscar Servicios
                    </a>
                    <a href="index.php?page=trueques" class="btn btn-outline" style="width: 100%; justify-content: center;">
                        🤝 Mis Trueques
                    </a>
                </div>
            </div>

            <!-- Estado de Trueques -->
            <div class="card">
                <h3 style="margin-bottom: 1rem;">📊 Estado de Trueques</h3>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span class="text-muted">⏳ Pendientes</span>
                        <span class="badge badge-primary"><?= $trueques_pendientes ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span class="text-muted">🔄 En progreso</span>
                        <span class="badge"><?= $trueques_activos ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span class="text-muted">✅ Completados</span>
                        <span class="badge badge-success"><?= $trueques_completados ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Trueques Recientes -->
    <?php if (!empty($trueques_recientes)): ?>
    <div style="margin-top: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h2>📜 Historial Reciente</h2>
            <a href="index.php?page=trueques" class="btn btn-sm btn-outline">Ver todos</a>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Partner</th>
                            <th>Mi Servicio</th>
                            <th>Su Servicio</th>
                            <th>Puntos</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($trueques_recientes as $trueque): ?>
                        <tr onclick="window.location='index.php?page=trueque-detalle&id=<?= $trueque['id'] ?>'" style="cursor: pointer;">
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <img 
                                        src="https://ui-avatars.com/api/?name=<?= urlencode($trueque['partner_nombre']) ?>&background=C86F3C&color=fff" 
                                        alt="Avatar" 
                                        style="width: 32px; height: 32px; border-radius: 50%;"
                                    >
                                    <?= e($trueque['partner_nombre']) ?>
                                </div>
                            </td>
                            <td><?= e($trueque['mi_servicio']) ?></td>
                            <td><?= e($trueque['su_servicio']) ?></td>
                            <td><strong><?= $trueque['puntos_intercambio'] ?></strong> pts</td>
                            <td>
                                <?php
                                $estadoBadges = [
                                    'pendiente' => ['⏳', 'badge-primary'],
                                    'aceptado' => ['🔄', 'badge'],
                                    'completado' => ['✅', 'badge-success'],
                                    'rechazado' => ['❌', 'badge'],
                                    'cancelado' => ['⛔', 'badge']
                                ];
                                $badge = $estadoBadges[$trueque['estado']] ?? ['📋', 'badge'];
                                ?>
                                <span class="badge <?= $badge[1] ?>">
                                    <?= $badge[0] ?> <?= ucfirst($trueque['estado']) ?>
                                </span>
                            </td>
                            <td class="text-muted text-sm"><?= timeAgo($trueque['fecha_creacion']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once ROOT_PATH . '/views/layout/footer.php'; ?>
