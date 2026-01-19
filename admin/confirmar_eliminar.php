<?php
// admin/confirmar_eliminar.php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models_admin/AdminModel.php';

// ================== CONTROL DE ACCESO ==================
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// ================== CAPTURAR ID ==================
if (isset($_GET['id'])) {
    $_SESSION['usuario_a_eliminar'] = (int) $_GET['id'];
}

$usuario_id = $_SESSION['usuario_a_eliminar'] ?? null;

// Si no hay ID válido, volver al listado
if (!$usuario_id) {
    header('Location: estudiantes.php');
    exit;
}

// ================== CONEXIÓN ==================
$database = new Database();
$db = $database->getConnection();
$adminModel = new AdminModel($db);

// ================== OBTENER USUARIO ==================
$usuario = $adminModel->getUsuarioParaEliminar($usuario_id);

if (!$usuario) {
    $_SESSION['error'] = "Usuario no encontrado";
    unset($_SESSION['usuario_a_eliminar']);
    header('Location: estudiantes.php');
    exit;
}

// ================== ELIMINAR USUARIO ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($adminModel->eliminarUsuario($usuario_id)) {
        unset($_SESSION['usuario_a_eliminar']);
        $_SESSION['success'] = 'Estudiante eliminado correctamente';
        header('Location: estudiantes.php');
        exit;
    } else {
        $error = 'Error al eliminar el estudiante';
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmar Eliminación</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <header class="admin-header">
            <h1><i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación</h1>
        </header>

        <main class="admin-main" style="max-width: 800px; margin: 2rem auto;">
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> 
                <strong>ADVERTENCIA: Esta acción no se puede deshacer</strong>
            </div>

            <div class="confirm-card">
                <h2>¿Está seguro de eliminar este usuario?</h2>
                
                <div class="user-info">
                    <div class="info-item">
                        <strong>Documento:</strong> <?= htmlspecialchars($usuario['documento']) ?>
                    </div>
                    <div class="info-item">
                        <strong>Nombre:</strong>
                        <?php
                        $nombre = trim(($usuario['nombres'] ?? '') . ' ' . ($usuario['apellidos'] ?? ''));
                        echo $nombre ? htmlspecialchars($nombre) : 'Sin información';
                        ?>
                    </div>
                    <div class="info-item">
                        <strong>Documentos subidos:</strong> <?= $usuario['total_documentos'] ?>
                    </div>
                </div>

                <div class="warning-box">
                    <h3><i class="fas fa-trash-alt"></i> Se eliminará:</h3>
                    <ul>
                        <li>Toda la información personal del estudiante</li>
                        <li>Datos familiares (padre, madre, acudiente)</li>
                        <li>Información de vivienda y salud</li>
                        <li>Todos los documentos subidos (<?= $usuario['total_documentos'] ?> archivos)</li>
                        <li>El registro de usuario completo</li>
                    </ul>
                </div>

                <div class="confirm-actions">
                    <!-- ✅ POST AL MISMO ARCHIVO -->
                    <form method="POST" action="../controllers_admin/AdminController.php">
    <input type="hidden" name="action" value="eliminar_usuario">
    <input type="hidden" name="usuario_id" value="<?php echo (int)$usuario_id; ?>">
    <input type="hidden" name="confirmar" value="1">

    <button type="submit" class="btn-danger">
        <i class="fas fa-trash-alt"></i> Sí, eliminar permanentemente
    </button>
</form>
                    
                    <a href="estudiantes.php" class="btn-cancel">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
