<?php
// admin/documentos.php - GESTIÓN DE DOCUMENTOS
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models_admin/AdminModel.php';

// ========== CONTROL DE ACCESO ==========
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

if ($_SESSION['user_role'] !== 'admin') {
    $_SESSION['error_message'] = "Acceso restringido a administradores";
    header('Location: ../home.php');
    exit;
}

// ========== OBTENER DOCUMENTOS ==========
$database = new Database();
$db = $database->getConnection();
$adminModel = new AdminModel($db);

// Procesar filtros
$filtros = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!empty($_GET['estudiante'])) $filtros['estudiante'] = $_GET['estudiante'];
    if (!empty($_GET['documento'])) $filtros['documento'] = $_GET['documento'];
    if (isset($_GET['estado']) && $_GET['estado'] !== '') $filtros['estado'] = $_GET['estado'];
}

// Obtener documentos
$query = "SELECT de.*, 
                 u.documento as estudiante_documento,
                 CONCAT(e.nombres, ' ', e.apellidos) as estudiante_nombre
          FROM documentos_estudiante de
          JOIN usuarios u ON de.usuario_id = u.id
          LEFT JOIN estudiantes e ON u.id = e.usuario_id
          WHERE 1=1";

if (!empty($filtros['estudiante'])) {
    $query .= " AND (e.nombres LIKE :estudiante OR e.apellidos LIKE :estudiante)";
}

if (!empty($filtros['documento'])) {
    $query .= " AND u.documento LIKE :documento";
}

if (isset($filtros['estado'])) {
    $query .= " AND de.estado = :estado";
}

$query .= " ORDER BY de.fecha_subida DESC";

$stmt = $db->prepare($query);

if (!empty($filtros['estudiante'])) {
    $stmt->bindValue(':estudiante', '%' . $filtros['estudiante'] . '%');
}

if (!empty($filtros['documento'])) {
    $stmt->bindValue(':documento', '%' . $filtros['documento'] . '%');
}

if (isset($filtros['estado'])) {
    $stmt->bindValue(':estado', $filtros['estado']);
}

$stmt->execute();
$documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contadores
$total = count($documentos);
$pendientes = array_filter($documentos, fn($doc) => $doc['estado'] === 'pendiente');
$aprobados = array_filter($documentos, fn($doc) => $doc['estado'] === 'aprobado');
$rechazados = array_filter($documentos, fn($doc) => $doc['estado'] === 'rechazado');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Documentos - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <!-- HEADER -->
        <header class="admin-header">
            <div class="header-left">
                <h1><i class="fas fa-file-alt"></i> Gestión de Documentos</h1>
                <p class="subtitle">Revisión y aprobación</p>
            </div>
            <div class="header-right">
                <div class="admin-info">
                    <span class="admin-name">Administrador</span>
                    <span class="admin-doc"><?php echo htmlspecialchars($_SESSION['user_document'] ?? ''); ?></span>
                </div>
                <a href="../controllers/AuthController.php?action=logout" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Salir
                </a>
            </div>
        </header>

        <!-- NAVEGACIÓN -->
        <nav class="admin-nav">
            <a href="index.php" class="nav-item">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="estudiantes.php" class="nav-item">
                <i class="fas fa-users"></i> Estudiantes
            </a>
            <a href="documentos.php" class="nav-item active">
                <i class="fas fa-file-alt"></i> Documentos
            </a>
        </nav>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="admin-main">
            <!-- MENSAJES -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; ?>
                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['info_message'])): ?>
                <div class="alert info-message">
                    <i class="fas fa-info-circle"></i> <?php echo $_SESSION['info_message']; ?>
                    <?php unset($_SESSION['info_message']); ?>
                </div>
            <?php endif; ?>

            <!-- ENCABEZADO -->
            <div class="page-header">
                <div>
                    <h2><i class="fas fa-folder-open"></i> Documentos Subidos</h2>
                    <p class="page-subtitle">
                        Total: <?php echo $total; ?> | 
                        Pendientes: <span class="badge badge-warning"><?php echo count($pendientes); ?></span> | 
                        Aprobados: <span class="badge badge-success"><?php echo count($aprobados); ?></span> | 
                        Rechazados: <span class="badge badge-danger"><?php echo count($rechazados); ?></span>
                    </p>
                </div>
            </div>

            <!-- FILTROS -->
            <div class="filters-card">
                <h3><i class="fas fa-filter"></i> Filtros</h3>
                <form method="GET" class="filters-form">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label for="estudiante"><i class="fas fa-user"></i> Estudiante</label>
                            <input type="text" id="estudiante" name="estudiante" 
                                   value="<?php echo htmlspecialchars($_GET['estudiante'] ?? ''); ?>"
                                   placeholder="Buscar por nombre...">
                        </div>
                        
                        <div class="filter-group">
                            <label for="documento"><i class="fas fa-id-card"></i> Documento</label>
                            <input type="text" id="documento" name="documento" 
                                   value="<?php echo htmlspecialchars($_GET['documento'] ?? ''); ?>"
                                   placeholder="Buscar por documento...">
                        </div>
                        
                        <div class="filter-group">
                            <label for="estado"><i class="fas fa-check-circle"></i> Estado</label>
                            <select id="estado" name="estado">
                                <option value="">Todos</option>
                                <option value="pendiente" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'pendiente') ? 'selected' : ''; ?>>Pendientes</option>
                                <option value="aprobado" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'aprobado') ? 'selected' : ''; ?>>Aprobados</option>
                                <option value="rechazado" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'rechazado') ? 'selected' : ''; ?>>Rechazados</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="filter-actions">
                        <button type="submit" class="btn-filter">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <a href="documentos.php" class="btn-clear">
                            <i class="fas fa-eraser"></i> Limpiar
                        </a>
                    </div>
                </form>
            </div>

            <!-- TABLA DE DOCUMENTOS -->
            <div class="table-container">
                <table class="data-table documentos-table">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Documento</th>
                            <th>Tipo Documento</th>
                            <th>Archivo</th>
                            <th>Fecha Subida</th>
                            <th>Estado</th>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($documentos)): ?>
                            <?php foreach ($documentos as $doc): 
                                $tipos = [
                                    'registro_civil' => 'Registro Civil',
                                    'tarjeta_identidad' => 'Tarjeta Identidad',
                                    'documento_extranjero' => 'Documento Extranjero',
                                    'documento_madre' => 'Doc. Madre',
                                    'documento_padre' => 'Doc. Padre',
                                    'cedula_acudiente' => 'Cédula Acudiente',
                                    'recibo_publico' => 'Recibo Público',
                                    'certificado_eps' => 'Certificado EPS',
                                    'foto_estudiante' => 'Foto Estudiante',
                                    'certificados_escolaridad' => 'Certificados',
                                    'formulario_simpade' => 'Formulario SIMPADE',
                                    'formato_imagen' => 'Uso de Imagen',
                                    'acta_acuerdos' => 'Acta Acuerdos',
                                    'hoja_matricula_firmada' => 'Matrícula Firmada',
                                    'certificado_discapacidad' => 'Cert. Discapacidad'
                                ];
                            ?>
                            <tr>
                                <td>
                                    <div class="student-name"><?php echo htmlspecialchars($doc['estudiante_nombre'] ?? 'Sin nombre'); ?></div>
                                    <small class="text-muted">Doc: <?php echo htmlspecialchars($doc['estudiante_documento']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($doc['estudiante_documento']); ?></td>
                                <td><?php echo $tipos[$doc['tipo_documento']] ?? $doc['tipo_documento']; ?></td>
                                <td>
                                    <?php if (!empty($doc['ruta_archivo'])): ?>
                                        <a href="../<?php echo htmlspecialchars($doc['ruta_archivo']); ?>" 
                                           target="_blank" class="btn-action btn-view" title="Ver documento">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Sin archivo</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($doc['fecha_subida'])); ?></td>
                                <td>
                                    <span class="estado-documento estado-<?php echo $doc['estado']; ?>">
                                        <?php 
                                        $estados = [
                                            'pendiente' => '⏳ Pendiente', 
                                            'aprobado' => '✅ Aprobado', 
                                            'rechazado' => '❌ Rechazado'
                                        ];
                                        echo $estados[$doc['estado']] ?? $doc['estado'];
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($doc['observaciones'])): ?>
                                        <small><?php echo htmlspecialchars($doc['observaciones']); ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">Sin observaciones</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="POST" action="../controllers_admin/AdminController.php?action=actualizar_estado_documento" class="inline-form">
                                        <input type="hidden" name="documento_id" value="<?php echo $doc['id']; ?>">
                                        <select name="estado" class="status-select" onchange="this.form.submit()">
                                            <option value="pendiente" <?php echo ($doc['estado'] == 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                            <option value="aprobado" <?php echo ($doc['estado'] == 'aprobado') ? 'selected' : ''; ?>>Aprobar</option>
                                            <option value="rechazado" <?php echo ($doc['estado'] == 'rechazado') ? 'selected' : ''; ?>>Rechazar</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="empty-table">
                                    <div class="empty-state">
                                        <i class="fas fa-file-slash"></i>
                                        <h3>No se encontraron documentos</h3>
                                        <p><?php echo (!empty($filtros)) ? 'Intenta con otros filtros de búsqueda' : 'No hay documentos subidos aún'; ?></p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        document.querySelectorAll('select[name="estado"]').forEach(select => {
            select.addEventListener('change', function() {
                if (this.value === 'rechazado') {
                    const form = this.closest('form');
                    const observaciones = prompt('Ingrese motivo del rechazo:');
                    if (observaciones !== null) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'observaciones';
                        input.value = observaciones;
                        form.appendChild(input);
                    } else {
                        this.value = 'pendiente';
                        return false;
                    }
                }
                return true;
            });
        });
    </script>
</body>
</html>