<?php 
$pageTitle = 'Mis Habilidades - Runa Maki';
$currentPage = 'habilidades';
require_once ROOT_PATH . '/views/layout/header.php';
?>

<div class="container">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1>⚡ Mis Habilidades</h1>
            <p class="text-muted">Gestiona los servicios que ofreces a la comunidad</p>
        </div>
        <button onclick="mostrarModal()" class="btn btn-primary">
            ➕ Nueva Habilidad
        </button>
    </div>

    <!-- Lista de Habilidades -->
    <?php if (empty($habilidades)): ?>
        <div class="card text-center" style="padding: 4rem;">
            <div style="font-size: 4rem; margin-bottom: 1rem;">📝</div>
            <h2>Aún no tienes habilidades publicadas</h2>
            <p class="text-muted" style="max-width: 500px; margin: 1rem auto;">
                Comparte tus conocimientos con la comunidad. Publica tu primera habilidad y comienza a intercambiar servicios.
            </p>
            <button onclick="mostrarModal()" class="btn btn-primary btn-lg mt-4">
                Publicar mi primera habilidad
            </button>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-3">
            <?php foreach ($habilidades as $habilidad): ?>
            <div class="card">
                <div style="display: flex; justify-content: between; align-items: start; margin-bottom: 1rem;">
                    <div class="badge badge-primary">
                        <?= e($habilidad['categoria_nombre']) ?>
                    </div>
                    <div style="margin-left: auto; display: flex; gap: 0.5rem;">
                        <button onclick="editarHabilidad(<?= htmlspecialchars(json_encode($habilidad), ENT_QUOTES) ?>)" 
                                class="btn btn-sm btn-outline" 
                                title="Editar">
                            ✏️
                        </button>
                        <button onclick="eliminarHabilidad(<?= $habilidad['id'] ?>, '<?= e($habilidad['titulo']) ?>')" 
                                class="btn btn-sm" 
                                style="color: #d4183d;"
                                title="Eliminar">
                            🗑️
                        </button>
                    </div>
                </div>

                <h3 style="margin-bottom: 0.5rem;"><?= e($habilidad['titulo']) ?></h3>
                <p class="text-muted text-sm" style="margin-bottom: 1rem; line-height: 1.4;">
                    <?= e(substr($habilidad['descripcion'], 0, 100)) ?><?= strlen($habilidad['descripcion']) > 100 ? '...' : '' ?>
                </p>

                <div style="display: flex; gap: 1rem; margin-bottom: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                    <div>
                        <div class="text-sm text-muted">Horas</div>
                        <div style="font-weight: 600;">⏱️ <?= $habilidad['horas_ofrecidas'] ?>h</div>
                    </div>
                    <div>
                        <div class="text-sm text-muted">Puntos</div>
                        <div style="font-weight: 600;">💰 <?= $habilidad['puntos_sugeridos'] ?></div>
                    </div>
                    <div>
                        <div class="text-sm text-muted">Visitas</div>
                        <div style="font-weight: 600;">👁️ <?= $habilidad['visitas'] ?></div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <?php
                    $estadoBadges = [
                        'pendiente' => ['⏳', 'badge-primary', 'Pendiente revisión'],
                        'aprobado' => ['✅', 'badge-success', 'Publicado'],
                        'rechazado' => ['❌', 'badge', 'Rechazado']
                    ];
                    $badge = $estadoBadges[$habilidad['estado']] ?? ['📋', 'badge', 'Desconocido'];
                    ?>
                    <span class="badge <?= $badge[1] ?>"><?= $badge[0] ?> <?= $badge[2] ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal para Crear/Editar Habilidad -->
<div id="modalHabilidad" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 600px; margin: 2rem; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 id="modalTitulo">Nueva Habilidad</h2>
            <button onclick="cerrarModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">×</button>
        </div>

        <form id="formHabilidad" action="index.php?page=habilidad-crear" method="POST">
            <input type="hidden" id="habilidad_id" name="id">
            
            <div class="form-group">
                <label for="categoria_id" class="form-label">Categoría</label>
                <select id="categoria_id" name="categoria_id" class="form-select" required>
                    <option value="">Selecciona una categoría</option>
                    <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= e($cat['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="titulo" class="form-label">Título</label>
                <input 
                    type="text" 
                    id="titulo" 
                    name="titulo" 
                    class="form-input" 
                    placeholder="Ej: Clases de guitarra para principiantes"
                    required
                >
            </div>

            <div class="form-group">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea 
                    id="descripcion" 
                    name="descripcion" 
                    class="form-textarea" 
                    placeholder="Describe lo que ofreces, tu experiencia y lo que aprenderán..."
                    required
                ></textarea>
            </div>

            <div class="grid grid-cols-2">
                <div class="form-group">
                    <label for="horas_ofrecidas" class="form-label">Horas por sesión</label>
                    <input 
                        type="number" 
                        id="horas_ofrecidas" 
                        name="horas_ofrecidas" 
                        class="form-input" 
                        min="1" 
                        max="10" 
                        value="2"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="puntos_sugeridos" class="form-label">Puntos sugeridos</label>
                    <input 
                        type="number" 
                        id="puntos_sugeridos" 
                        name="puntos_sugeridos" 
                        class="form-input" 
                        min="10" 
                        max="500" 
                        value="50"
                        required
                    >
                    <small class="text-muted text-sm">Sugerencia: 25 puntos por hora</small>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    Guardar
                </button>
                <button type="button" onclick="cerrarModal()" class="btn btn-outline" style="flex: 1;">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Form invisible para eliminar -->
<form id="formEliminar" action="index.php?page=habilidad-eliminar" method="POST" style="display: none;">
    <input type="hidden" id="eliminar_id" name="id">
</form>

<script>
    function mostrarModal() {
        document.getElementById('modalHabilidad').style.display = 'flex';
        document.getElementById('modalTitulo').textContent = 'Nueva Habilidad';
        document.getElementById('formHabilidad').reset();
        document.getElementById('habilidad_id').value = '';
        document.getElementById('formHabilidad').action = 'index.php?page=habilidad-crear';
    }

    function cerrarModal() {
        document.getElementById('modalHabilidad').style.display = 'none';
    }

    function editarHabilidad(habilidad) {
        document.getElementById('modalHabilidad').style.display = 'flex';
        document.getElementById('modalTitulo').textContent = 'Editar Habilidad';
        document.getElementById('formHabilidad').action = 'index.php?page=habilidad-actualizar';
        
        document.getElementById('habilidad_id').value = habilidad.id;
        document.getElementById('categoria_id').value = habilidad.categoria_id;
        document.getElementById('titulo').value = habilidad.titulo;
        document.getElementById('descripcion').value = habilidad.descripcion;
        document.getElementById('horas_ofrecidas').value = habilidad.horas_ofrecidas;
        document.getElementById('puntos_sugeridos').value = habilidad.puntos_sugeridos;
    }

    function eliminarHabilidad(id, titulo) {
        if (confirm(`¿Estás seguro de eliminar "${titulo}"?`)) {
            document.getElementById('eliminar_id').value = id;
            document.getElementById('formEliminar').submit();
        }
    }

    // Cerrar modal al hacer click fuera
    document.getElementById('modalHabilidad').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModal();
        }
    });

    // Auto-calcular puntos sugeridos basado en horas
    document.getElementById('horas_ofrecidas').addEventListener('change', function() {
        const horas = parseInt(this.value) || 1;
        const puntosInput = document.getElementById('puntos_sugeridos');
        if (!puntosInput.value || puntosInput.value == puntosInput.defaultValue) {
            puntosInput.value = horas * 25;
        }
    });
</script>

<?php require_once ROOT_PATH . '/views/layout/footer.php'; ?>
