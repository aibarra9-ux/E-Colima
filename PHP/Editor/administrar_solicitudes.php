<?php
session_start();

// Control de acceso para Editores
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'editor') {
    header("Location: ../Home/home.php");
    exit();
}

include("../Perfil/conexion.php");

// Consulta limpia sin columnas inexistentes (como p.imagen)
$sql = "SELECT p.id, p.titulo, p.contenido, p.fecha_creacion, c.slug AS categoria, u.username AS autor 
        FROM publicaciones p
        JOIN categorias c ON p.categoria_id = c.id
        JOIN usuarios u ON p.autor_id = u.id 
        WHERE p.estado = 'pendiente'
        ORDER BY p.fecha_creacion DESC";

$result = $conn->query($sql);
$solicitudes = [];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $solicitudes[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Solicitudes - ECOLIMA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../CSS/Editor/solicitudes.css">
</head>
<body>

    <div class="panel-container">
        <header class="panel-header">
            <div class="header-left">
                <a href="../Home/home.php" class="btn-regresar"><i class="fas fa-arrow-left"></i> Volver al Home</a>
                <h1>Panel de Control de Solicitudes</h1>
                <p>Revisa, previsualiza y autoriza las contribuciones de la comunidad escritora.</p>
            </div>
            <div class="counter-badge">
                <span class="number"><?php echo count($solicitudes); ?></span>
                <span class="label">Pendientes</span>
            </div>
        </header>

        <?php if (isset($_GET['actualizado'])): ?>
            <div style="background: rgba(46, 160, 67, 0.15); border: 1px solid rgba(46, 160, 67, 0.3); color: #57ab5a; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-family: Arial, sans-serif; font-size: 14px;">
                <i class="fas fa-check-circle"></i> La solicitud ha sido procesada correctamente.
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="solicitudes-table">
                <thead>
                    <tr>
                        <th>Escritor</th>
                        <th>Categoría</th>
                        <th>Título de la Publicación</th>
                        <th>Fecha de Envío</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($solicitudes)): ?>
                        <tr>
                            <td colspan="5" class="empty-state">
                                <i class="fas fa-check-circle" style="font-size: 40px; color: #3e464f; display:block; margin-bottom:10px;"></i>
                                <p style="color: #8b949e; text-align:center;">No hay publicaciones pendientes de revisión por ahora.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($solicitudes as $solicitud): ?>
                            <tr>
                                <td class="user-cell">
                                    <i class="fas fa-user-circle"></i> 
                                    <span><?php echo htmlspecialchars($solicitud['autor']); ?></span>
                                </td>
                                <td>
                                    <span class="badge-categoria <?php echo strtolower($solicitud['categoria']); ?>">
                                        <?php echo htmlspecialchars($solicitud['categoria']); ?>
                                    </span>
                                </td>
                                <td class="title-cell" style="font-weight: 500; color: #ffffff;">
                                    <?php echo htmlspecialchars($solicitud['titulo']); ?>
                                </td>
                                <td class="date-cell" style="color: #8b949e; font-size: 14px;">
                                    <?php echo date('d/m/Y H:i', strtotime($solicitud['fecha_creacion'])); ?>
                                </td>
                                <td class="actions-cell">
                                    <div class="acciones-container">
                                        
                                        <div class="tooltip-wrapper">
                                            <button class="btn-accion btn-ver" onclick="abrirModalVer('<?php echo addslashes(htmlspecialchars($solicitud['titulo'])); ?>', '<?php echo addslashes(htmlspecialchars($solicitud['contenido'])); ?>', '<?php echo addslashes(htmlspecialchars($solicitud['autor'])); ?>')">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                            <span class="tooltip-text">Previsualizar</span>
                                        </div>
                                        
                                        <form action="procesar_revision.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="id_post" value="<?php echo $solicitud['id']; ?>">
                                            <input type="hidden" name="accion" value="aprobar">
                                            <div class="tooltip-wrapper">
                                                <button type="submit" class="btn-accion btn-aprobar">
                                                    <i class="fa-solid fa-check"></i>
                                                </button>
                                                <span class="tooltip-text">Aprobar y Publicar</span>
                                            </div>
                                        </form>

                                        <div class="tooltip-wrapper">
                                            <button class="btn-accion btn-rechazar" onclick="abrirModalRechazar(<?php echo $solicitud['id']; ?>)">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                            <span class="tooltip-text">Rechazar Solicitud</span>
                                        </div>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="modalVer" class="modal-overlay">
        <div class="modal-content asimetrico" style="max-width: 700px;">
            <div class="modal-header-asimetrico">
                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <h2 id="modalVerTitulo"></h2>
                    <span id="modalVerAutor" style="color: #8b949e; font-size: 13px; font-weight: 500;"></span>
                </div>
                <button type="button" class="modal-close-btn" onclick="cerrarModalVer()">[Cerrar: X]</button>
            </div>

            <div id="modalVerContenido" style="max-height: 400px; overflow-y: auto; color: #e2e8f0; line-height: 1.7; font-size: 15px; padding-right: 10px;"></div>
        </div>
    </div>

    <div id="modalRechazar" class="modal-overlay">
        <div class="modal-content" style="max-width: 450px;">
            <h3 style="color: #f87171; margin-top: 0; margin-bottom: 10px;"><i class="fas fa-exclamation-triangle"></i> Rechazar Solicitud</h3>
            <p style="color: #94a3b8; font-size: 13px; margin-bottom: 15px;">Especifica el motivo del rechazo. La publicación cambiará a estado rechazado.</p>
            
            <form action="procesar_revision.php" method="POST">
                <input type="hidden" name="id_post" id="rechazarPostId" value="">
                <input type="hidden" name="accion" value="rechazar">
                
                <textarea name="motivo" placeholder="Escribe aquí el motivo detallado del rechazo..." required style="width: 100%; height: 100px; background: rgba(0,0,0,0.3); border: 1px solid rgba(126, 189, 145, 0.2); border-radius: 8px; color: #fff; padding: 10px; font-family: sans-serif; resize: none; box-sizing: border-box; margin-bottom: 20px;"></textarea>
                
                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn-modal btn-cancelar" onclick="cerrarModalRechazar()">Cancelar</button>
                    <button type="submit" class="btn-modal btn-enviar">Enviar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function abrirModalVer(titulo, contenido, autor) {
        document.getElementById('modalVerTitulo').innerText = titulo;
        document.getElementById('modalVerContenido').innerHTML = contenido.replace(/\n/g, '<br>');
        document.getElementById('modalVerAutor').innerText = "Escrito por: @" + autor;
        document.getElementById('modalVer').classList.add('active');
    }

    function cerrarModalVer() {
        document.getElementById('modalVer').classList.remove('active');
    }

    function abrirModalRechazar(id) {
        document.getElementById('rechazarPostId').value = id;
        document.getElementById('modalRechazar').classList.add('active');
    }

    function cerrarModalRechazar() {
        document.getElementById('modalRechazar').classList.remove('active');
    }

    window.onclick = function(event) {
        const modalVer = document.getElementById('modalVer');
        const modalRechazar = document.getElementById('modalRechazar');
        if (event.target == modalVer) cerrarModalVer();
        if (event.target == modalRechazar) cerrarModalRechazar();
    }
    // Detectar si el usuario regresó usando la flecha de atrás del navegador
    window.addEventListener('pageshow', function(event) {
        // Si la página fue cargada desde la memoria caché del historial (persisted)
        if (event.persisted || (typeof window.performance != 'undefined' && window.performance.navigation.type === 2)) {
            // Forzar una recarga limpia desde el servidor para actualizar la base de datos
            window.location.reload();
        }
    });
    </script>
</body>
</html>