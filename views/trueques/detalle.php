<?php
/**
 * Vista: detalle de trueque
 * Variables esperadas: $trueque (array), $mensajes (array), $es_mi_turno (bool)
 */
$pageTitle = 'Trueque #' . ($trueque['id'] ?? '') . ' - Runa Maki';
$currentPage = 'trueques';
require_once ROOT_PATH . '/views/layout/header.php';
?>

<div class="container">
    <div style="display:flex; justify-content: space-between; align-items:center; margin-bottom:1rem;">
        <div>
            <h1>Trueque #<?= e($trueque['id'] ?? '') ?></h1>
            <p class="text-sm text-muted"><?= e($trueque['habilidad_ofrece_titulo'] ?? '') ?> ↔ <?= e($trueque['habilidad_recibe_titulo'] ?? '') ?></p>
        </div>
        <div>
            <span class="badge <?= $trueque['estado'] === 'pendiente' ? 'badge-primary' : ($trueque['estado'] === 'aceptado' ? 'badge' : ($trueque['estado']==='completado'?'badge-success':'badge')) ?>"><?= e(ucfirst($trueque['estado'] ?? '')) ?></span>
        </div>
    </div>

    <div class="grid" style="grid-template-columns: 2fr 1fr; gap: 1rem;">
        <div>
            <div class="card" style="margin-bottom:1rem;">
                <h3>Participantes</h3>
                <div style="display:flex; gap:1rem; align-items:center;">
                    <div style="flex:1;">
                        <strong><?= e($trueque['usuario_ofrece_nombre'] ?? 'Usuario') ?></strong>
                        <div class="text-sm text-muted">Ofrece: <?= e($trueque['habilidad_ofrece_titulo'] ?? '') ?></div>
                    </div>
                    <div style="flex:1; text-align:right;">
                        <strong><?= e($trueque['usuario_recibe_nombre'] ?? 'Usuario') ?></strong>
                        <div class="text-sm text-muted">Recibe: <?= e($trueque['habilidad_recibe_titulo'] ?? '') ?></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <h3>Mensajes</h3>
                <div style="max-height:360px; overflow:auto; padding-right:0.5rem;">
                    <?php if (empty($mensajes)): ?>
                        <p class="text-muted">Aún no hay mensajes. Usa el formulario para contactar al otro usuario.</p>
                    <?php else: ?>
                        <?php foreach ($mensajes as $m): ?>
                            <div style="margin-bottom:0.75rem; padding:0.75rem; background:var(--bg-muted); border-radius:6px;">
                                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.25rem;">
                                    <strong><?= e($m['remitente_nombre']) ?></strong>
                                    <span class="text-sm text-muted"><?= e($m['fecha_envio']) ?></span>
                                </div>
                                <div><?= nl2br(e($m['mensaje'])) ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div style="margin-top:1rem;">
                    <form action="index.php?page=mensaje-enviar" method="POST">
                        <input type="hidden" name="trueque_id" value="<?= e($trueque['id'] ?? '') ?>">
                        <div class="form-group">
                            <label for="mensaje">Tu mensaje</label>
                            <textarea id="mensaje" name="mensaje" class="form-textarea" rows="3" required></textarea>
                        </div>
                        <div style="display:flex; gap:0.5rem; margin-top:0.5rem;">
                            <?php if ($es_mi_turno): ?>
                                <button type="submit" class="btn btn-primary">Enviar</button>
                            <?php else: ?>
                                <button type="submit" class="btn btn-primary">Enviar</button>
                            <?php endif; ?>
                            <a href="index.php?page=trueques" class="btn btn-outline">Volver</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div>
            <div class="card" style="margin-bottom:1rem;">
                <h3>Resumen</h3>
                <div class="text-sm text-muted">Puntos de intercambio</div>
                <div style="font-weight:600; font-size:1.25rem; margin-top:0.5rem;">💰 <?= e($trueque['puntos_intercambio'] ?? 0) ?> pts</div>

                <div style="margin-top:1rem;"><a href="index.php?page=perfil&user_id=<?= e($trueque['usuario_ofrece_id'] ?? '') ?>">Ver perfil de <?= e($trueque['usuario_ofrece_nombre'] ?? '') ?></a></div>
                <div style="margin-top:0.5rem;"><a href="index.php?page=perfil&user_id=<?= e($trueque['usuario_recibe_id'] ?? '') ?>">Ver perfil de <?= e($trueque['usuario_recibe_nombre'] ?? '') ?></a></div>
            </div>

            <div class="card">
                <h3>Acciones</h3>
                <?php if (($trueque['estado'] ?? '') === 'pendiente'): ?>
                    <?php if ($trueque['usuario_recibe_id'] == ($_SESSION['usuario_id'] ?? null)): ?>
                        <form action="index.php?page=trueque-aceptar" method="POST" style="display:flex; gap:0.5rem;">
                            <input type="hidden" name="id" value="<?= e($trueque['id'] ?? '') ?>">
                            <button type="submit" class="btn btn-primary">Aceptar</button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if (($trueque['estado'] ?? '') === 'aceptado'): ?>
                    <form action="index.php?page=trueque-completar" method="POST" style="display:flex; gap:0.5rem; margin-top:0.5rem;">
                        <input type="hidden" name="id" value="<?= e($trueque['id'] ?? '') ?>">
                        <button type="submit" class="btn btn-primary">Marcar como completado</button>
                    </form>
                <?php endif; ?>

                <?php if (($trueque['estado'] ?? '') === 'completado'): ?>
                    <?php if (!($ya_valoro ?? false)): ?>
                        <div style="margin-top:1rem;">
                            <h4>Valorar a tu contraparte</h4>
                            <form action="index.php?page=trueque-valorar" method="POST">
                                <input type="hidden" name="trueque_id" value="<?= e($trueque['id'] ?? '') ?>">
                                <div class="form-group">
                                    <label>Puntuación (1-5)</label>
                                    <select name="puntuacion" class="form-select" required>
                                        <option value="">--Seleccione--</option>
                                        <?php for ($i=1;$i<=5;$i++): ?>
                                            <option value="<?= $i ?>"><?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Comentario (opcional)</label>
                                    <textarea name="comentario" class="form-textarea"></textarea>
                                </div>
                                <div style="margin-top:0.5rem;">
                                    <button type="submit" class="btn btn-primary">Enviar valoración</button>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <div style="margin-top:1rem;">Ya has valorado este trueque. Gracias.</div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/views/layout/footer.php'; ?>
