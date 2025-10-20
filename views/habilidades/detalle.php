<?php
/**
 * Vista: detalle de habilidad
 * Espera variables: $habilidad (array), $usuario (array), $categoria (array)
 */
$pageTitle = e($habilidad['titulo'] ?? 'Detalle de habilidad') . ' - Runa Maki';
$currentPage = 'buscar';
require_once ROOT_PATH . '/views/layout/header.php';
?>

<div class="container">
    <div class="card" style="padding: 1.5rem;">
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; align-items: start;">
            <div>
                <?php
                    $imagen = $habilidad['imagen'] ?? null;
                    $gallery = [];
                    if ($imagen && !empty($imagen)) {
                        if (strpos($imagen, ',') !== false) {
                            $parts = array_map('trim', explode(',', $imagen));
                            foreach ($parts as $p) {
                                if (strpos($p, 'http') === 0) {
                                    $gallery[] = $p;
                                } else {
                                    $gallery[] = APP_URL . '/public/uploads/' . $p;
                                }
                            }
                        } else {
                            if (strpos($imagen, 'http') === 0) {
                                $gallery[] = $imagen;
                            } else {
                                $gallery[] = APP_URL . '/public/uploads/' . $imagen;
                            }
                        }
                    }

                    $mainImg = $gallery[0] ?? 'https://via.placeholder.com/800x500';
                ?>
                <img id="mainImage" src="<?= e($mainImg) ?>" alt="<?= e($habilidad['titulo'] ?? 'Habilidad') ?>" style="width: 100%; border-radius: var(--radius); object-fit: cover;">

                <?php if (count($gallery) > 1): ?>
                    <div style="display:flex; gap:0.5rem; margin-top:0.75rem;">
                        <?php foreach ($gallery as $g): ?>
                            <img src="<?= e($g) ?>" alt="thumb" style="width:72px; height:48px; object-fit:cover; border-radius:4px; cursor:pointer;" onclick="document.getElementById('mainImage').src='<?= e($g) ?>'">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div>
                <div style="display:flex; justify-content: space-between; align-items: start; gap: 1rem;">
                    <div>
                        <h1 style="margin:0 0 0.25rem 0;"><?= e($habilidad['titulo'] ?? 'Título de la habilidad') ?></h1>
                        <p class="text-sm text-muted" style="margin:0;">por <strong><a href="index.php?page=perfil&user_id=<?= $habilidad['usuario_id'] ?? '' ?>" style="color:inherit; text-decoration: none;"><?= e($usuario['nombre'] ?? 'Usuario') ?></a></strong> • <?= e($categoria['nombre'] ?? 'Categoría') ?></p>
                        <p class="text-sm text-muted" style="margin:0;">⭐ <?= number_format((float)($usuario['reputacion'] ?? 0.0), 1) ?></p>
                    </div>
                    <div style="text-align: right;">
                        <div class="badge badge-primary">💰 <?= isset($habilidad['puntos_sugeridos']) ? (int)$habilidad['puntos_sugeridos'] : 0 ?> pts</div>
                        <div style="margin-top:0.5rem;" class="badge">⏳ <?= isset($habilidad['horas_ofrecidas']) ? (int)$habilidad['horas_ofrecidas'] : 1 ?> hrs</div>
                    </div>
                </div>

                <hr style="margin: 1rem 0; border-color: var(--border-color);">

                <div>
                    <h3 style="margin-bottom:0.5rem;">Descripción</h3>
                    <p class="text-muted" style="line-height:1.6;">
                        <?= nl2br(e($habilidad['descripcion'] ?? 'Sin descripción disponible')) ?>
                    </p>
                </div>

                <div style="display:flex; gap:0.75rem; margin-top:1.25rem;">
                    <?php
                    $currentUserId = getCurrentUserId();
                    $isGuest = !isAuthenticated() || $currentUserId === 'guest';
                    $isAuthor = isset($habilidad['usuario_id']) && $currentUserId == $habilidad['usuario_id'];
                    ?>

                    <?php if (!$isGuest && !$isAuthor): ?>
                        <button class="btn btn-primary" onclick="document.getElementById('modalTrueque').style.display='flex'">Solicitar Trueque</button>
                    <?php elseif ($isAuthor): ?>
                        <span class="text-sm text-muted">Esta es tu publicación</span>
                    <?php else: ?>
                        <a href="index.php?page=login" class="btn btn-primary">Inicia sesión para solicitar</a>
                    <?php endif; ?>

                    <a href="index.php?page=habilidades" class="btn btn-outline">← Volver</a>
                </div>

                <!-- Modal crear trueque -->
                <div id="modalTrueque" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1200; align-items:center; justify-content:center;">
                    <div class="card" style="width:100%; max-width:520px; margin:2rem;">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <h3>Solicitar Trueque</h3>
                            <button onclick="document.getElementById('modalTrueque').style.display='none'" style="background:none; border:none; font-size:1.25rem;">×</button>
                        </div>

                        <form action="index.php?page=trueque-crear" method="POST">
                            <input type="hidden" name="usuario_ofrece_id" value="<?= e($currentUserId) ?>">
                            <input type="hidden" name="usuario_recibe_id" value="<?= e($habilidad['usuario_id'] ?? '') ?>">
                            <input type="hidden" name="habilidad_recibe_id" value="<?= e($habilidad['id'] ?? '') ?>">

                            <?php $mis_habilidades = $mis_habilidades ?? []; ?>
                            <?php if (empty($mis_habilidades)): ?>
                                <div class="form-group">
                                    <p class="text-sm text-muted">No tienes habilidades publicadas para ofrecer. Publica una primera habilidad antes de solicitar un trueque.</p>
                                </div>
                            <?php else: ?>
                                <div class="form-group">
                                    <label class="form-label">Selecciona la habilidad que ofreces</label>
                                    <select name="habilidad_ofrece_id" class="form-select" required>
                                        <option value="">-- Selecciona --</option>
                                        <?php foreach ($mis_habilidades as $m): ?>
                                            <option value="<?= e($m['id']) ?>"><?= e($m['titulo']) ?> (<?= e($m['categoria_nombre']) ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Puntos a intercambiar</label>
                                    <input type="number" name="puntos_intercambio" class="form-input" value="<?= isset($habilidad['puntos_sugeridos']) ? (int)$habilidad['puntos_sugeridos'] : 20 ?>" min="1" required>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Mensaje para el autor (opcional)</label>
                                    <textarea name="mensaje_inicial" class="form-textarea" placeholder="Escribe un mensaje corto para explicar tu propuesta..."></textarea>
                                </div>
                            <?php endif; ?>

                            <div style="display:flex; gap:0.5rem; margin-top:1rem;">
                                <button type="submit" class="btn btn-primary" <?= empty($mis_habilidades) ? 'disabled' : '' ?>>Enviar propuesta</button>
                                <button type="button" class="btn btn-outline" onclick="document.getElementById('modalTrueque').style.display='none'">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if (!empty($habilidad['fecha_creacion'])): ?>
                <div class="text-sm text-muted" style="margin-top:1rem;">Publicado el <?= date('d M Y', strtotime($habilidad['fecha_creacion'])) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/views/layout/footer.php'; ?>
