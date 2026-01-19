<?php
// admin/ver_estudiante.php - VER DETALLES DE ESTUDIANTE
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

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "ID de estudiante no especificado";
    header('Location: estudiantes.php');
    exit;
}

$usuario_id = $_GET['id'];

// ========== OBTENER DATOS ==========
$database = new Database();
$db = $database->getConnection();
$adminModel = new AdminModel($db);

$datos = $adminModel->getEstudianteCompleto($usuario_id);

if (!$datos || empty($datos['usuario'])) {
    $_SESSION['error'] = "Estudiante no encontrado";
    header('Location: estudiantes.php');
    exit;
}

$usuario = $datos['usuario'];
$personal = $datos['personal'] ?? [];
$general = $datos['general'] ?? [];
$madre = $datos['madre'] ?? [];
$padre = $datos['padre'] ?? [];
$acudiente = $datos['acudiente'] ?? [];
$vivienda = $datos['vivienda'] ?? [];
$salud = $datos['salud'] ?? [];
$documentos = $datos['documentos'] ?? [];

// Calcular progreso
$pasos_completos = 0;
$total_pasos = 7;
if (!empty($personal))
    $pasos_completos++;
if (!empty($general))
    $pasos_completos++;
if (!empty($madre))
    $pasos_completos++;
if (!empty($padre))
    $pasos_completos++;
if (!empty($acudiente))
    $pasos_completos++;
if (!empty($vivienda))
    $pasos_completos++;
if (!empty($salud))
    $pasos_completos++;
$porcentaje = round(($pasos_completos / $total_pasos) * 100);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Estudiante - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .student-profile {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            background: #4CAF50;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            color: white;
            font-size: 36px;
        }

        .profile-info h2 {
            margin: 0 0 5px 0;
            color: #333;
        }

        .profile-info p {
            margin: 5px 0;
            color: #666;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .info-card {
            background: #f9f9f9;
            border-radius: 8px;
            padding: 15px;
            border-left: 4px solid #4CAF50;
        }

        .info-card h3 {
            margin-top: 0;
            color: #333;
            font-size: 16px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .info-item {
            margin-bottom: 10px;
        }

        .info-label {
            font-weight: bold;
            color: #555;
            display: block;
            margin-bottom: 3px;
        }

        .info-value {
            color: #333;
        }

        .documentos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .documento-item {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }

        .documento-icon {
            font-size: 24px;
            display: block;
            margin-bottom: 10px;
            color: #4CAF50;
        }

        .empty-section {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .section-tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }

        .section-tab {
            padding: 10px 20px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }

        .section-tab.active {
            border-bottom-color: #4CAF50;
            font-weight: bold;
            color: #4CAF50;
        }

        .section-content {
            display: none;
        }

        .section-content.active {
            display: block;
        }
    </style>
</head>

<body>
    <div class="admin-container">
        <!-- HEADER -->
        <header class="admin-header">
            <div class="header-left">
                <h1><i class="fas fa-user-graduate"></i> Detalles del Estudiante</h1>
                <p class="subtitle">Información completa</p>
            </div>
            <div class="header-right">
                <div class="admin-info">
                    <span class="admin-name">Administrador</span>
                    <span class="admin-doc"><?php echo htmlspecialchars($_SESSION['user_document'] ?? ''); ?></span>
                </div>
                <a href="estudiantes.php" class="logout-btn">
                    <i class="fas fa-arrow-left"></i> Volver
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
            <a href="documentos.php" class="nav-item">
                <i class="fas fa-file-alt"></i> Documentos
            </a>
            <a href="#" class="nav-item" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimir
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

            <!-- PERFIL DEL ESTUDIANTE -->
            <div class="student-profile">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="profile-info">
                        <h2>
                            <?php
                            echo htmlspecialchars(($personal['nombres'] ?? '') . ' ' . ($personal['apellidos'] ?? ''));
                            if (empty($personal['nombres']))
                                echo '<span class="text-muted">Sin información personal</span>';
                            ?>
                        </h2>
                        <p><i class="fas fa-id-card"></i> Documento:
                            <?php echo htmlspecialchars($usuario['documento']); ?></p>
                        <p><i class="fas fa-calendar"></i> Registrado:
                            <?php echo date('d/m/Y H:i', strtotime($usuario['fecha_creacion'])); ?></p>

                        <!-- PROGRESO -->
                        <div style="width: 300px; margin-top: 10px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                <span>Progreso del formulario:</span>
                                <span><?php echo $pasos_completos; ?>/<?php echo $total_pasos; ?>
                                    (<?php echo $porcentaje; ?>%)</span>
                            </div>
                            <div style="background: #eee; height: 10px; border-radius: 5px; overflow: hidden;">
                                <div style="background: #4CAF50; width: <?php echo $porcentaje; ?>%; height: 100%;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BOTONES DE ACCIÓN -->
                <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                    <button type="button" class="btn-primary"
                        onclick="generarHojaMatriculaAdmin(<?php echo $usuario_id; ?>, event)">
                        <i class="fas fa-file-pdf"></i> Generar Hoja de Matrícula
                    </button>

                    <a href="../controllers/StudentController.php?action=generar_hoja_matricula&usuario_id=<?php echo $usuario_id; ?>&target=admin"
                        target="_blank" class="btn-secondary">
                        <i class="fas fa-eye"></i> Ver como estudiante
                    </a>

                    <a href="estudiantes.php" class="btn-cancel">
                        <i class="fas fa-arrow-left"></i> Volver a lista
                    </a>
                </div>

                <!-- PESTAÑAS DE INFORMACIÓN -->
                <div class="section-tabs">
                    <div class="section-tab active" onclick="mostrarSeccion('personal')">
                        <i class="fas fa-user"></i> Información Personal
                    </div>
                    <div class="section-tab" onclick="mostrarSeccion('general')">
                        <i class="fas fa-info-circle"></i> Información General
                    </div>
                    <div class="section-tab" onclick="mostrarSeccion('familia')">
                        <i class="fas fa-users"></i> Datos Familiares
                    </div>
                    <div class="section-tab" onclick="mostrarSeccion('documentos')">
                        <i class="fas fa-file-alt"></i> Documentos
                    </div>
                </div>

                <!-- SECCIÓN INFORMACIÓN PERSONAL -->
                <div id="seccion-personal" class="section-content active">
                    <div class="info-grid">
                        <div class="info-card">
                            <h3><i class="fas fa-id-card"></i> Datos Básicos</h3>
                            <?php if (!empty($personal)): ?>
                                <div class="info-item">
                                    <span class="info-label">Nombres:</span>
                                    <span
                                        class="info-value"><?php echo htmlspecialchars($personal['nombres'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Apellidos:</span>
                                    <span
                                        class="info-value"><?php echo htmlspecialchars($personal['apellidos'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Tipo Estudiante:</span>
                                    <span
                                        class="info-value"><?php echo htmlspecialchars($personal['tipo_estudiante'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Fecha Nacimiento:</span>
                                    <span
                                        class="info-value"><?php echo !empty($personal['fecha_nacimiento']) ? date('d/m/Y', strtotime($personal['fecha_nacimiento'])) : ''; ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Lugar Nacimiento:</span>
                                    <span
                                        class="info-value"><?php echo htmlspecialchars($personal['lugar_nacimiento'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Edad:</span>
                                    <span class="info-value"><?php echo htmlspecialchars($personal['edad'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Tipo Sangre:</span>
                                    <span
                                        class="info-value"><?php echo htmlspecialchars($personal['tipo_sangre'] ?? ''); ?></span>
                                </div>
                            <?php else: ?>
                                <div class="empty-section">
                                    <i class="fas fa-user-slash"></i>
                                    <p>No hay información personal registrada</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="info-card">
                            <h3><i class="fas fa-address-card"></i> Documentación</h3>
                            <?php if (!empty($personal)): ?>
                                <div class="info-item">
                                    <span class="info-label">Tipo Documento:</span>
                                    <span
                                        class="info-value"><?php echo htmlspecialchars($personal['tipo_documento'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Número Documento:</span>
                                    <span
                                        class="info-value"><?php echo htmlspecialchars($personal['numero_documento'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Lugar Expedición:</span>
                                    <span
                                        class="info-value"><?php echo htmlspecialchars($personal['lugar_expedicion'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Género:</span>
                                    <span
                                        class="info-value"><?php echo htmlspecialchars($personal['genero'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Celular:</span>
                                    <span
                                        class="info-value"><?php echo htmlspecialchars($personal['celular'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Teléfono Residencia:</span>
                                    <span
                                        class="info-value"><?php echo htmlspecialchars($personal['telefono_residencia'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Fecha Matrícula:</span>
                                    <span
                                        class="info-value"><?php echo !empty($personal['fecha_matricula']) ? date('d/m/Y', strtotime($personal['fecha_matricula'])) : ''; ?></span>
                                </div>
                            <?php else: ?>
                                <div class="empty-section">
                                    <i class="fas fa-file-slash"></i>
                                    <p>No hay información de documentación</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN INFORMACIÓN GENERAL -->
                <div id="seccion-general" class="section-content">
                    <div class="info-grid">
                        <?php if (!empty($general)): ?>
                            <div class="info-card">
                                <h3><i class="fas fa-home"></i> Domicilio</h3>
                                <div class="info-item">
                                    <span class="info-label">Dirección:</span>
                                    <span
                                        class="info-value"><?php echo htmlspecialchars($general['direccion'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Barrio:</span>
                                    <span
                                        class="info-value"><?php echo htmlspecialchars($general['barrio'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Municipio:</span>
                                    <span
                                        class="info-value"><?php echo htmlspecialchars($general['municipio'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Estrato:</span>
                                    <span
                                        class="info-value"><?php echo htmlspecialchars($general['estrato'] ?? ''); ?></span>
                                </div>
                            </div>

                            <div class="info-card">
                                <h3><i class="fas fa-heartbeat"></i> Salud y Bienestar</h3>
                                <div class="info-item">
                                    <span class="info-label">EPS:</span>
                                    <span class="info-value"><?php echo htmlspecialchars($general['eps'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">SISBEN:</span>
                                    <span
                                        class="info-value"><?php echo htmlspecialchars($general['sisben'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Etnia:</span>
                                    <span class="info-value"><?php echo htmlspecialchars($general['etnia'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Desplazado:</span>
                                    <span
                                        class="info-value"><?php echo ($general['desplazado'] ?? '') == 'si' ? 'Sí' : 'No'; ?></span>
                                </div>
                            </div>

                            <div class="info-card">
                                <h3><i class="fas fa-user-friends"></i> Familia</h3>
                                <div class="info-item">
                                    <span class="info-label">Madre Cabeza Familia:</span>
                                    <span
                                        class="info-value"><?php echo ($general['madre_cabeza_familia'] ?? '') == 'si' ? 'Sí' : 'No'; ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Número Hermanos:</span>
                                    <span
                                        class="info-value"><?php echo htmlspecialchars($general['numero_hermanos'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Lugar entre Hermanos:</span>
                                    <span
                                        class="info-value"><?php echo htmlspecialchars($general['lugar_entre_hermanos'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Hermanos en Colegio:</span>
                                    <span
                                        class="info-value"><?php echo ($general['hermanos_en_colegio'] ?? '') == 'si' ? 'Sí' : 'No'; ?></span>
                                </div>
                            </div>

                            <div class="info-card">
                                <h3><i class="fas fa-user-md"></i> Discapacidad</h3>
                                <div class="info-item">
                                    <span class="info-label">Discapacidad Diagnosticada:</span>
                                    <span
                                        class="info-value"><?php echo ($general['discapacidad_diagnostico'] ?? '') == 'si' ? 'Sí' : 'No'; ?></span>
                                </div>
                                <?php if (($general['discapacidad_diagnostico'] ?? '') == 'si'): ?>
                                    <div class="info-item">
                                        <span class="info-label">Tipo Discapacidad:</span>
                                        <span
                                            class="info-value"><?php echo htmlspecialchars($general['tipo_discapacidad'] ?? ''); ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="info-item">
                                    <span class="info-label">Lateralidad:</span>
                                    <span
                                        class="info-value"><?php echo htmlspecialchars($general['lateralidad'] ?? ''); ?></span>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="empty-section">
                                <i class="fas fa-info-circle"></i>
                                <p>No hay información general registrada</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- SECCIÓN DATOS FAMILIARES -->
                <div id="seccion-familia" class="section-content">
                    <div class="info-grid">
                        <!-- DATOS MADRE -->
                        <div class="info-card">
                            <h3><i class="fas fa-female"></i> Datos de la Madre</h3>
                            <?php if (!empty($madre)): ?>
                                <div class="info-item">
                                    <span class="info-label">Nombres:</span>
                                    <span class="info-value"><?php echo htmlspecialchars($madre['nombres'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Apellidos:</span>
                                    <span
                                        class="info-value"><?php echo htmlspecialchars($madre['apellidos'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Celular:</span>
                                    <span class="info-value"><?php echo htmlspecialchars($madre['celular'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Correo:</span>
                                    <span class="info-value"><?php echo htmlspecialchars($madre['correo'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Ocupación:</span>
                                    <span
                                        class="info-value"><?php echo htmlspecialchars($madre['ocupacion'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Es Acudiente:</span>
                                    <span
                                        class="info-value"><?php echo ($madre['es_acudiente'] ?? '') == 'si' ? 'Sí' : 'No'; ?></span>
                                </div>
                            <?php else: ?>
                                <div class="empty-section">
                                    <i class="fas fa-user-slash"></i>
                                    <p>No hay datos de la madre</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- DATOS PADRE -->
                        <div class="info-card">
                            <h3><i class="fas fa-male"></i> Datos del Padre</h3>
                            <?php if (!empty($padre)): ?>
                                <div class="info-item">
                                    <span class="info-label">Nombres:</span>
                                    <span class="info-value"><?php echo htmlspecialchars($padre['nombres'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Apellidos:</span>
                                    <span
                                        class="info-value"><?php echo htmlspecialchars($padre['apellidos'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Celular:</span>
                                    <span class="info-value"><?php echo htmlspecialchars($padre['celular'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Correo:</span>
                                    <span class="info-value"><?php echo htmlspecialchars($padre['correo'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Ocupación:</span>
                                    <span
                                        class="info-value"><?php echo htmlspecialchars($padre['ocupacion'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Es Acudiente:</span>
                                    <span
                                        class="info-value"><?php echo ($padre['es_acudiente'] ?? '') == 'si' ? 'Sí' : 'No'; ?></span>
                                </div>
                            <?php else: ?>
                                <div class="empty-section">
                                    <i class="fas fa-user-slash"></i>
                                    <p>No hay datos del padre</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- DATOS ACUDIENTE -->
                        <div class="info-card">
                            <h3><i class="fas fa-user-tie"></i> Datos del Acudiente</h3>
                            <?php if (!empty($acudiente)): ?>
                                <div class="info-item">
                                    <span class="info-label">Nombres:</span>
                                    <span
                                        class="info-value"><?php echo htmlspecialchars($acudiente['nombres'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Apellidos:</span>
                                    <span
                                        class="info-value"><?php echo htmlspecialchars($acudiente['apellidos'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Celular:</span>
                                    <span
                                        class="info-value"><?php echo htmlspecialchars($acudiente['celular'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Correo:</span>
                                    <span
                                        class="info-value"><?php echo htmlspecialchars($acudiente['correo'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Ocupación:</span>
                                    <span
                                        class="info-value"><?php echo htmlspecialchars($acudiente['ocupacion'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Convive con Estudiante:</span>
                                    <span
                                        class="info-value"><?php echo ($acudiente['convive_estudiante'] ?? '') == 'si' ? 'Sí' : 'No'; ?></span>
                                </div>
                            <?php else: ?>
                                <div class="empty-section">
                                    <i class="fas fa-user-slash"></i>
                                    <p>No hay datos del acudiente</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- DATOS VIVIENDA -->
                        <div class="info-card">
                            <h3><i class="fas fa-building"></i> Datos de Vivienda</h3>
                            <?php if (!empty($vivienda)): ?>
                                <div class="info-item">
                                    <span class="info-label">Número Personas en Hogar:</span>
                                    <span
                                        class="info-value"><?php echo htmlspecialchars($vivienda['numero_personas'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Tipo Vivienda:</span>
                                    <span
                                        class="info-value"><?php echo htmlspecialchars($vivienda['tipo_vivienda'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Servicios:</span>
                                    <span class="info-value">
                                        <?php
                                        $servicios = [];
                                        if (($vivienda['servicio_energia'] ?? '') == 'si')
                                            $servicios[] = 'Energía';
                                        if (($vivienda['servicio_agua'] ?? '') == 'si')
                                            $servicios[] = 'Agua';
                                        if (($vivienda['servicio_alcantarillado'] ?? '') == 'si')
                                            $servicios[] = 'Alcantarillado';
                                        if (($vivienda['servicio_gas'] ?? '') == 'si')
                                            $servicios[] = 'Gas';
                                        echo implode(', ', $servicios);
                                        ?>
                                    </span>
                                </div>
                            <?php else: ?>
                                <div class="empty-section">
                                    <i class="fas fa-home"></i>
                                    <p>No hay datos de vivienda</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN DOCUMENTOS -->
<div id="seccion-documentos" class="section-content">
    <?php if (!empty($documentos)): ?>
        <div class="documentos-grid">
            <?php foreach ($documentos as $doc):

                $tipos = [
                    'registro_civil' => 'Registro Civil',
                    'tarjeta_identidad' => 'Tarjeta Identidad',
                    'documento_extranjero' => 'Documento Extranjero',
                    'documento_madre' => 'Documento Madre',
                    'documento_padre' => 'Documento Padre',
                    'cedula_acudiente' => 'Cédula Acudiente',
                    'recibo_publico' => 'Recibo Público',
                    'certificado_eps' => 'Certificado EPS',
                    'foto_estudiante' => 'Foto Estudiante',
                    'certificados_escolaridad' => 'Certificados',
                    'formulario_simpade' => 'Formulario SIMPADE',
                    'formato_imagen' => 'Uso de Imagen',
                    'acta_acuerdos' => 'Acta de Acuerdos',
                    'hoja_matricula_firmada' => 'Matrícula Firmada',
                    'certificado_discapacidad' => 'Certificado Discapacidad'
                ];

                // ÍCONOS por tipo de documento
                $iconos = [
                    'registro_civil' => '📄',
                    'tarjeta_identidad' => '🪪',
                    'documento_extranjero' => '🌎',
                    'documento_madre' => '👩',
                    'documento_padre' => '👨',
                    'cedula_acudiente' => '🪪',
                    'recibo_publico' => '💡',
                    'certificado_eps' => '🏥',
                    'foto_estudiante' => '📸',
                    'certificados_escolaridad' => '🎓',
                    'formulario_simpade' => '📝',
                    'formato_imagen' => '🖼️',
                    'acta_acuerdos' => '📑',
                    'hoja_matricula_firmada' => '📝',
                    'certificado_discapacidad' => '♿'
                ];

                $nombre_doc = $tipos[$doc['tipo_documento']] ?? $doc['tipo_documento'];
                $icono_doc  = $iconos[$doc['tipo_documento']] ?? '📄';
                $estado_class = 'estado-' . ($doc['estado'] ?? 'pendiente');
            ?>
                <div class="documento-item">

                    <div class="documento-icon">
                        <?php echo $icono_doc; ?>
                    </div>

                    <h4><?php echo htmlspecialchars($nombre_doc); ?></h4>

                    <small>
                        Subido: <?php echo date('d/m/Y', strtotime($doc['fecha_subida'])); ?>
                    </small>
                    <br>

                    <span class="estado-documento <?php echo $estado_class; ?>">
                        <?php
                        $estados = [
                            'pendiente' => '⏳ Pendiente',
                            'aprobado'  => '✅ Aprobado',
                            'rechazado' => '❌ Rechazado'
                        ];
                        echo $estados[$doc['estado']] ?? $doc['estado'];
                        ?>
                    </span>

                    <?php if (!empty($doc['ruta_archivo'])): ?>
                        <br>
                        <a href="../<?php echo htmlspecialchars($doc['ruta_archivo']); ?>"
                           target="_blank"
                           class="btn-action btn-view"
                           style="margin-top: 10px;">
                            <i class="fas fa-eye"></i> Ver
                        </a>
                    <?php endif; ?>

                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-section">
            <i class="fas fa-file-slash"></i>
            <p>No hay documentos subidos</p>
        </div>
    <?php endif; ?>
</div>

            </div>
        </main>
    </div>
    <script>
        function generarHojaMatriculaAdmin(usuario_id) {
            console.log('🔄 Generando hoja de matrícula para usuario:', usuario_id);

            if (!usuario_id || usuario_id === '') {
                alert('❌ Error: ID de estudiante no válido');
                return false;
            }

            // IMPORTANTE: Usar "user_id" como parámetro (no "usuario_id")
            const url = `../controllers/StudentController.php?action=generar_hoja_matricula&user_id=${usuario_id}&target=admin`;
            window.open(url, '_blank');
            return true;
        }
    </script>
    <script>
        // Funciones para cambiar pestañas
        function mostrarSeccion(seccion) {
            // Ocultar todas las secciones
            document.querySelectorAll('.section-content').forEach(el => {
                el.classList.remove('active');
            });

            // Desactivar todas las pestañas
            document.querySelectorAll('.section-tab').forEach(el => {
                el.classList.remove('active');
            });

            // Mostrar sección seleccionada
            document.getElementById('seccion-' + seccion).classList.add('active');

            // Activar pestaña seleccionada
            event.target.classList.add('active');
        }

        // Mostrar primera sección por defecto
        document.addEventListener('DOMContentLoaded', function () {
            mostrarSeccion('personal');
        });
    </script>
</body>

</html>