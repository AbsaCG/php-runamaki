<?php 
$pageTitle = 'Mi Perfil - Runa Maki';
$currentPage = 'perfil';
require_once ROOT_PATH . '/views/layout/header.php';

// Obtener datos del usuario
$usuarioModel = new Usuario();
$usuario = $usuarioModel->obtenerPorId(getCurrentUserId());
$estadisticas = $usuarioModel->obtenerEstadisticas(getCurrentUserId());

// Normalizar en caso de que el modelo devuelva false/null (por ejemplo usuario invitado)
if ($usuario === false || $usuario === null) {
    $usuario = [
        'id' => getCurrentUserId(),
        'nombre' => 'Invitado',
        'email' => 'invitado@runamaki.com',
        'nivel' => 'Usuario',
        'rol' => 'usuario',
        'reputacion' => 0.0,
        'ubicacion' => '',
        'fecha_registro' => null,
        'puntos_runa' => 0
    ];
}

if ($estadisticas === false || $estadisticas === null) {
    $estadisticas = [
        'trueques_completados' => 0,
        'habilidades_activas' => 0,
        'puntos_ganados_total' => 0,
        'puntos_gastados_total' => 0
    ];
}
?>

<div class="container">
    <!-- Header -->
    <div style="margin-bottom: 2rem;">
        <h1>👤 Mi Perfil</h1>
        <p class="text-muted">Gestiona tu información personal y estadísticas</p>
    </div>

    <div class="profile-grid">
        <!-- Sidebar - Info del Usuario -->
        <aside class="profile-sidebar">
            <div class="card text-center">
                <img 
                    src="https://ui-avatars.com/api/?name=<?= urlencode($usuario['nombre'] ?? 'Invitado') ?>&background=C86F3C&color=fff&size=150" 
                    alt="Avatar" 
                    class="profile-avatar"
                >
                <h2><?= e($usuario['nombre'] ?? 'Invitado') ?></h2>
                <p class="text-muted"><?= e($usuario['email'] ?? '') ?></p>
                
                <div style="display: flex; gap: 0.5rem; justify-content: center; margin: 1rem 0;">
                    <span class="badge badge-primary"><?= e($usuario['nivel'] ?? 'Usuario') ?></span>
                    <?php if (($usuario['rol'] ?? '') === 'admin'): ?>
                    <span class="badge badge-success">⚡ Admin</span>
                    <?php endif; ?>
                </div>

                <div style="margin: 1.5rem 0; padding: 1rem; background-color: var(--bg-muted); border-radius: var(--radius);">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">⭐</div>
                    <div style="font-size: 1.5rem; font-weight: 600;"><?= number_format((float)($usuario['reputacion'] ?? 0.0), 2) ?></div>
                    <div class="text-sm text-muted">Reputación</div>
                </div>

                <?php
                    // Logros del usuario
                    require_once ROOT_PATH . '/models/Logro.php';
                    $logroModel = new Logro();
                    $misLogros = $logroModel->obtenerPorUsuario($usuario['id']);
                ?>
                <div style="margin-bottom:1rem; padding:1rem; background-color:var(--bg-muted); border-radius:var(--radius);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
                        <div style="font-weight:600;">Logros</div>
                        <div class="text-sm text-muted"><?= count($misLogros) ?> obtenidos</div>
                    </div>

                    <?php if (empty($misLogros)): ?>
                        <div class="text-sm text-muted">Aún no has obtenido logros.</div>
                    <?php else: ?>
                        <div style="display:flex; flex-direction:column; gap:0.5rem;">
                            <?php foreach ($misLogros as $l): ?>
                                <div style="display:flex; align-items:center; gap:0.5rem;">
                                    <div style="font-size:1.25rem; width:36px; text-align:center;"><?= e($l['icono'] ?? '🏅') ?></div>
                                    <div style="flex:1;">
                                        <div style="font-weight:600;"><?= e($l['nombre']) ?></div>
                                        <div class="text-sm text-muted">Obtenido: <?= date('d M Y', strtotime($l['fecha_obtencion'])) ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php
                    // Resumen de valoraciones
                    $resumen = $usuarioModel->resumenValoraciones($usuario['id']);
                    $comentarios = $usuarioModel->comentariosRecientes($usuario['id'], 5);
                ?>
                <div style="margin-bottom:1rem; padding:1rem; background-color:var(--bg-muted); border-radius:var(--radius);">
                    <div style="display:flex; justify-content: space-between; align-items:center;">
                        <div style="font-weight:600;">Valoraciones</div>
                        <div style="text-align:right;">
                            <div style="font-size:1.25rem; font-weight:700;"><?= number_format((float)($resumen['promedio'] ?? 0.0), 1) ?>/5</div>
                            <div class="text-sm text-muted"><?= (int)($resumen['total'] ?? 0) ?> valoraciones</div>
                        </div>
                    </div>

                    <div style="margin-top:0.75rem; display:flex; gap:0.5rem; align-items:center;">
                        <div style="font-size:1.25rem; color: #F5A623;">
                            <?php $avg = round((float)($resumen['promedio'] ?? 0.0)); ?>
                            <?php for ($i=1;$i<=5;$i++): ?>
                                <?php if ($i <= $avg): ?>★<?php else: ?>☆<?php endif; ?>
                            <?php endfor; ?>
                        </div>
                        <div class="text-sm text-muted">Promedio visual</div>
                    </div>

                    <?php $breakdown = $usuarioModel->desgloseValoraciones($usuario['id']); ?>
                    <div style="margin-top:0.75rem;">
                        <?php for ($s=5;$s>=1;$s--):
                            $count = $breakdown[$s] ?? 0;
                            $total = max(1, (int)($resumen['total'] ?? 0));
                            $pct = $total ? round($count / $total * 100) : 0;
                        ?>
                        <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.25rem;">
                            <div style="width:32px; text-align:right;"><?= $s ?>★</div>
                            <div style="flex:1; background:#e9ecef; height:10px; border-radius:6px; overflow:hidden;">
                                <div style="width:<?= $pct ?>%; height:100%; background:linear-gradient(90deg,#C86F3C,#5A8B4A);"></div>
                            </div>
                            <div style="width:36px; text-align:right;"><?= $count ?></div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <div style="margin-bottom:1rem; padding:1rem; background-color:var(--bg-muted); border-radius:var(--radius);">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <div style="font-weight:600;">Comentarios recientes</div>
                        <?php if (!empty($comentarios)): ?>
                            <a href="#" id="verMasComentariosToggle" style="font-size:0.85rem;">Ver más</a>
                        <?php endif; ?>
                    </div>
                    <div id="comentariosPreview" style="margin-top:0.75rem;">
                    <?php if (empty($comentarios)): ?>
                        <div class="text-sm text-muted">Aún no hay comentarios.</div>
                    <?php else: ?>
                        <?php foreach ($comentarios as $c): ?>
                            <div style="margin-bottom:0.5rem;">
                                <div style="font-weight:600;"><?= e($c['evaluador_nombre']) ?> <span class="text-sm text-muted">· <?= date('d M Y', strtotime($c['fecha_valoracion'])) ?></span></div>
                                <div class="text-sm text-muted"><?= e($c['comentario'] ?? '') ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </div>

                    <div id="comentariosFull" style="display:none; margin-top:0.75rem;">
                        <?php
                            $allComments = $usuarioModel->comentariosRecientes($usuario['id'], 50);
                            if (empty($allComments)) {
                                echo '<div class="text-sm text-muted">Aún no hay comentarios.</div>';
                            } else {
                                foreach ($allComments as $c) {
                                    echo '<div style="margin-bottom:0.5rem;"><div style="font-weight:600;">' . e($c['evaluador_nombre']) . ' <span class="text-sm text-muted">· ' . date('d M Y', strtotime($c['fecha_valoracion'])) . '</span></div><div class="text-sm text-muted">' . e($c['comentario'] ?? '') . '</div></div>';
                                }
                            }
                        ?>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function(){
                            const toggle = document.getElementById('verMasComentariosToggle');
                            if (!toggle) return;
                            toggle.addEventListener('click', function(e){
                                e.preventDefault();
                                const full = document.getElementById('comentariosFull');
                                const prev = document.getElementById('comentariosPreview');
                                if (full.style.display === 'none' || full.style.display === '') {
                                    full.style.display = 'block';
                                    prev.style.display = 'none';
                                    toggle.textContent = 'Ver menos';
                                } else {
                                    full.style.display = 'none';
                                    prev.style.display = 'block';
                                    toggle.textContent = 'Ver más';
                                }
                            });
                        });
                    </script>
                </div>

                <div style="text-align: left; padding: 1rem; background-color: var(--bg-muted); border-radius: var(--radius);">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <span>📍</span>
                        <span class="text-sm"><?= e($usuario['ubicacion'] ?? '') ?></span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span>📅</span>
                        <?php
                            $fechaRegistro = $usuario['fecha_registro'] ?? null;
                            $miembroDesde = '—';
                            if (!empty($fechaRegistro)) {
                                $ts = strtotime($fechaRegistro);
                                if ($ts !== false && $ts !== null) {
                                    $miembroDesde = date('M Y', $ts);
                                }
                            }
                        ?>
                        <span class="text-sm">Miembro desde <?= $miembroDesde ?></span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <main class="profile-main">
            <!-- Estadísticas -->
            <div class="card" style="margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1.5rem;">📊 Estadísticas</h3>
                <div class="grid grid-cols-3">
                    <div style="text-align: center; padding: 1rem;">
                        <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">💰</div>
                        <div style="font-size: 1.75rem; font-weight: 600;"><?= isset($usuario['puntos_runa']) ? (int)$usuario['puntos_runa'] : 0 ?></div>
                        <div class="text-sm text-muted">Puntos Runa</div>
                    </div>
                    <div style="text-align: center; padding: 1rem;">
                        <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🤝</div>
                        <div style="font-size: 1.75rem; font-weight: 600;"><?= $estadisticas['trueques_completados'] ?? 0 ?></div>
                        <div class="text-sm text-muted">Trueques Completados</div>
                    </div>
                    <div style="text-align: center; padding: 1rem;">
                        <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">⚡</div>
                        <div style="font-size: 1.75rem; font-weight: 600;"><?= $estadisticas['habilidades_activas'] ?? 0 ?></div>
                        <div class="text-sm text-muted">Habilidades Activas</div>
                    </div>
                </div>

                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
                    <div class="grid grid-cols-2">
                        <div>
                            <div class="text-sm text-muted">Puntos Ganados Total</div>
                            <div style="font-size: 1.25rem; font-weight: 600; color: var(--color-accent);">
                                +<?= $estadisticas['puntos_ganados_total'] ?? 0 ?>
                            </div>
                        </div>
                        <div>
                            <div class="text-sm text-muted">Puntos Gastados Total</div>
                            <div style="font-size: 1.25rem; font-weight: 600; color: var(--color-primary);">
                                -<?= $estadisticas['puntos_gastados_total'] ?? 0 ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Editar Perfil -->
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3>✏️ Editar Información</h3>
                </div>

                <form action="index.php?page=perfil-actualizar" method="POST">
                    <div class="form-group">
                        <label for="nombre" class="form-label">Nombre Completo</label>
                        <input 
                            type="text" 
                            id="nombre" 
                            name="nombre" 
                            class="form-input" 
                            value="<?= e($usuario['nombre']) ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-input" 
                            value="<?= e($usuario['email']) ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="ubicacion" class="form-label">Ubicación</label>
                        <input 
                            type="text" 
                            id="ubicacion" 
                            name="ubicacion" 
                            class="form-input" 
                            value="<?= e($usuario['ubicacion']) ?>"
                        >
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Guardar cambios
                    </button>
                </form>
            </div>

            <!-- Cambiar Contraseña -->
            <div class="card" style="margin-top: 2rem;">
                <h3 style="margin-bottom: 1.5rem;">🔒 Cambiar Contraseña</h3>
                <form action="index.php?page=perfil-cambiar-password" method="POST">
                    <div class="form-group">
                        <label for="password_actual" class="form-label">Contraseña Actual</label>
                        <input 
                            type="password" 
                            id="password_actual" 
                            name="password_actual" 
                            class="form-input" 
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="password_nueva" class="form-label">Nueva Contraseña</label>
                        <input 
                            type="password" 
                            id="password_nueva" 
                            name="password_nueva" 
                            class="form-input" 
                            minlength="6"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="password_confirmar" class="form-label">Confirmar Nueva Contraseña</label>
                        <input 
                            type="password" 
                            id="password_confirmar" 
                            name="password_confirmar" 
                            class="form-input" 
                            required
                        >
                    </div>

                    <button type="submit" class="btn btn-secondary">
                        Cambiar contraseña
                    </button>
                </form>
            </div>

            <!-- Editar Perfil -->
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3>✏️ Editar Información</h3>
                </div>

                <form action="index.php?page=perfil-actualizar" method="POST">
                    <div class="form-group">
                        <label for="nombre" class="form-label">Nombre Completo</label>
                        <input 
                            type="text" 
                            id="nombre" 
                            name="nombre" 
                            class="form-input" 
                            value="<?= e($usuario['nombre']) ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-input" 
                            value="<?= e($usuario['email']) ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="ubicacion" class="form-label">Ubicación</label>
                        <input 
                            type="text" 
                            id="ubicacion" 
                            name="ubicacion" 
                            class="form-input" 
                            value="<?= e($usuario['ubicacion']) ?>"
                        >
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Guardar cambios
                    </button>
                </form>
            </div>

            <!-- Cambiar Contraseña -->
            <div class="card" style="margin-top: 2rem;">
                <h3 style="margin-bottom: 1.5rem;">🔒 Cambiar Contraseña</h3>
                <form action="index.php?page=perfil-cambiar-password" method="POST">
                    <div class="form-group">
                        <label for="password_actual" class="form-label">Contraseña Actual</label>
                        <input 
                            type="password" 
                            id="password_actual" 
                            name="password_actual" 
                            class="form-input" 
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="password_nueva" class="form-label">Nueva Contraseña</label>
                        <input 
                            type="password" 
                            id="password_nueva" 
                            name="password_nueva" 
                            class="form-input" 
                            minlength="6"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="password_confirmar" class="form-label">Confirmar Nueva Contraseña</label>
                        <input 
                            type="password" 
                            id="password_confirmar" 
                            name="password_confirmar" 
                            class="form-input" 
                            required
                        >
                    </div>

                    <button type="submit" class="btn btn-secondary">
                        Cambiar contraseña
                    </button>
                </form>
            </div>
        </main>
    </div>
</div>

<?php require_once ROOT_PATH . '/views/layout/footer.php'; ?>
