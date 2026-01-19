<?php
// admin/index.php - DASHBOARD DEL ADMINISTRADOR
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

// ========== OBTENER ESTADÍSTICAS ==========
$database = new Database();
$db = $database->getConnection();
$adminModel = new AdminModel($db);

$estadisticas = $adminModel->getEstadisticas();

// OBTENER también el listado completo (sin límite)
$todos_estudiantes = $adminModel->getEstudiantes();

// Calcular porcentajes
$total_estudiantes = count($todos_estudiantes); // ← Usar este conteo
$porcentaje_personal = $total_estudiantes > 0 ? round(($estadisticas['completaron_personal'] / $total_estudiantes) * 100) : 0;
$porcentaje_documentos = $total_estudiantes > 0 ? round(($estadisticas['subieron_documentos'] / $total_estudiantes) * 100) : 0;

// Estudiantes recientes (últimos 5)
$estudiantes_recientes = $adminModel->getEstudiantes(['limite' => 5]);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Colegio María Currea Manrique</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="admin-container">
        <!-- HEADER -->
        <header class="admin-header">
            <div class="header-left">
                <h1><i class="fas fa-tachometer-alt"></i> Panel de Administración</h1>
                <p class="subtitle">Colegio María Currea Manrique I.E.D</p>
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
            <a href="index.php" class="nav-item active">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="estudiantes.php" class="nav-item">
                <i class="fas fa-users"></i> Estudiantes
            </a>
            <a href="documentos.php" class="nav-item">
                <i class="fas fa-file-alt"></i> Documentos
            </a>
    
            <a href="../home.php" class="nav-item" target="_blank">
                <i class="fas fa-eye"></i> Ver como usuario
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

            <!-- TÍTULO -->
            <div class="page-header">
                <h2><i class="fas fa-chart-line"></i> Estadísticas del Sistema</h2>
                <p class="page-date"><?php echo date('d/m/Y'); ?></p>
            </div>

            <!-- TARJETAS DE ESTADÍSTICAS -->
            <div class="stats-grid">
                <!-- Total Estudiantes -->
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #4CAF50;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Total Estudiantes</h3>
                        <p class="stat-number"><?php echo $total_estudiantes; ?></p>
                        <p class="stat-desc">Registrados en el sistema</p>
                    </div>
                </div>

                <!-- Información Personal -->
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #2196F3;">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Información Personal</h3>
                        <p class="stat-number"><?php echo $estadisticas['completaron_personal'] ?? 0; ?>
                            <small>(<?php echo $porcentaje_personal; ?>%)</small></p>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo $porcentaje_personal; ?>%"></div>
                        </div>
                    </div>
                </div>

                <!-- Documentos Subidos -->
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #FF9800;">
                        <i class="fas fa-file-upload"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Documentos Subidos</h3>
                        <p class="stat-number"><?php echo $estadisticas['subieron_documentos'] ?? 0; ?>
                            <small>(<?php echo $porcentaje_documentos; ?>%)</small></p>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo $porcentaje_documentos; ?>%"></div>
                        </div>
                    </div>
                </div>

                <!-- Completitud General -->
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #9C27B0;">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Completitud General</h3>
                        <p class="stat-number"><?php echo $estadisticas['completaron_salud'] ?? 0; ?></p>
                        <p class="stat-desc">Completaron todos los formularios</p>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN DE ESTUDIANTES RECIENTES -->
            <div class="recent-section">
                <div class="section-header">
                    <h3><i class="fas fa-history"></i> Estudiantes Recientes</h3>
                    <a href="estudiantes.php" class="view-all">Ver todos →</a>
                </div>

                <?php if (!empty($estudiantes_recientes)): ?>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Documento</th>
                                    <th>Nombre</th>
                                    <th>Grado</th>
                                    <th>Fecha Registro</th>
                                    <th>Progreso</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($estudiantes_recientes as $estudiante): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($estudiante['documento'] ?? ''); ?></td>
                                        <td>
                                            <?php
                                            $nombre = trim(($estudiante['nombres'] ?? '') . ' ' . ($estudiante['apellidos'] ?? ''));
                                            echo !empty($nombre) ? htmlspecialchars($nombre) : '<span class="text-muted">Sin información</span>';
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($estudiante['grado_matricular'] ?? 'No asignado'); ?>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($estudiante['fecha_creacion'])); ?></td>
                                        <td>
                                            <?php
                                            $pasos = 0;
                                            $total_pasos = 7;
                                            if ($estudiante['tiene_personal'])
                                                $pasos++;
                                            if ($estudiante['tiene_general'])
                                                $pasos++;
                                            if ($estudiante['tiene_madre'])
                                                $pasos++;
                                            if ($estudiante['tiene_padre'])
                                                $pasos++;
                                            if ($estudiante['tiene_acudiente'])
                                                $pasos++;
                                            if ($estudiante['tiene_vivienda'])
                                                $pasos++;
                                            if ($estudiante['tiene_salud'])
                                                $pasos++;
                                            $porcentaje = round(($pasos / $total_pasos) * 100);
                                            ?>
                                            <div class="mini-progress">
                                                <div class="mini-progress-bar" style="width: <?php echo $porcentaje; ?>%"></div>
                                                <span><?php echo $porcentaje; ?>%</span>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="ver_estudiante.php?id=<?php echo $estudiante['usuario_id']; ?>"
                                                class="btn-action btn-view" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button"
                                                onclick="generarHojaMatriculaAdmin(<?php echo $estudiante['usuario_id']; ?>, event)"
                                                title="Generar Hoja de Matrícula" onmouseover="
            this.style.background='#219653';
            this.style.transform='translateY(-1px)';
            this.style.boxShadow='0 4px 8px rgba(39,174,96,0.3)';
        " onmouseout="
            this.style.background='#27ae60';
            this.style.transform='translateY(0)';
            this.style.boxShadow='none';
        " style="
            border: none;
            outline: none;
            cursor: pointer;
            background: #27ae60;
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 5px;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        ">
                                                <i class="fas fa-file-pdf"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-user-slash"></i>
                        <p>No hay estudiantes registrados aún</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ACCIONES RÁPIDAS -->
            <div class="quick-actions">
                <h3><i class="fas fa-bolt"></i> Acciones Rápidas</h3>
                <div class="actions-grid">
                    <a href="estudiantes.php" class="action-card">
                        <i class="fas fa-search"></i>
                        <span>Buscar Estudiante</span>
                    </a>
                    <a href="documentos.php?estado=pendiente" class="action-card">
                        <i class="fas fa-clipboard-check"></i>
                        <span>Revisar Documentos</span>
                    </a>
                </div>
            </div>
        </main>

        <!-- FOOTER -->
        <footer class="admin-footer">
            <p>Sistema de Matrículas 2025 - Colegio María Currea Manrique I.E.D</p>
            <p class="footer-info">Usuarios activos: <?php echo $total_estudiantes; ?> | Última actualización:
                <?php echo date('H:i:s'); ?></p>
        </footer>
    </div>
    <script>
        function generarHojaMatriculaAdmin(usuario_id) {
            console.log('🔄 Generando hoja de matrícula para usuario:', usuario_id);

            if (!usuario_id || usuario_id === '') {
                alert('❌ Error: ID de estudiante no válido');
                return false;
            }

            // IMPORTANTE: Usar "user_id" como parámetro (no "usuario_id")
            const url = `../controllers/StudentController.php?action=generar_hoja_matricula&user_id=${usuario_id}&target=admin&modo=ver`;
            window.open(url, '_blank');
            return true;
        }
    </script>
    <script>
        // Actualizar hora cada minuto
        function actualizarHora() {
            const ahora = new Date();
            const hora = ahora.getHours().toString().padStart(2, '0');
            const minutos = ahora.getMinutes().toString().padStart(2, '0');
            const segundos = ahora.getSeconds().toString().padStart(2, '0');

            document.querySelectorAll('.footer-info').forEach(el => {
                el.textContent = el.textContent.replace(/\d{2}:\d{2}:\d{2}/, `${hora}:${minutos}:${segundos}`);
            });
        }

        setInterval(actualizarHora, 1000);

        // Mostrar/ocultar detalles
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('click', function () {
                this.classList.toggle('expanded');
            });
        });
    </script>
</body>

</html>