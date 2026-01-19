<?php
// admin/estudiantes.php - LISTADO DE ESTUDIANTES
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

// ========== PROCESAR FILTROS ==========
$database = new Database();
$db = $database->getConnection();
$adminModel = new AdminModel($db);

$filtros = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!empty($_GET['documento'])) $filtros['documento'] = $_GET['documento'];
    if (!empty($_GET['nombre'])) $filtros['nombre'] = $_GET['nombre'];
    if (!empty($_GET['grado'])) $filtros['grado'] = $_GET['grado'];
    if (isset($_GET['completo']) && $_GET['completo'] !== '') $filtros['completo'] = $_GET['completo'];
}

// Obtener estudiantes con filtros
$estudiantes = $adminModel->getEstudiantes($filtros);

// Obtener grados únicos para el filtro
$grados = [];
foreach ($estudiantes as $est) {
    if (!empty($est['grado_matricular']) && !in_array($est['grado_matricular'], $grados)) {
        $grados[] = $est['grado_matricular'];
    }
}
sort($grados);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Estudiantes - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <!-- HEADER -->
        <header class="admin-header">
            <div class="header-left">
                <h1><i class="fas fa-users"></i> Gestión de Estudiantes</h1>
                <p class="subtitle">Administración de matrículas</p>
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
            <a href="estudiantes.php" class="nav-item active">
                <i class="fas fa-users"></i> Estudiantes
            </a>
            <a href="documentos.php" class="nav-item">
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

            <!-- ENCABEZADO CON CONTADOR -->
            <div class="page-header">
                <div>
                    <h2><i class="fas fa-list"></i> Listado de Estudiantes</h2>
                    <p class="page-subtitle">Total: <?php echo count($estudiantes); ?> estudiante(s)</p>
                </div>
                <div class="header-actions">
                    <button onclick="seleccionarTodos()" class="btn-secondary">
                        <i class="fas fa-check-square"></i> Seleccionar todos
                    </button>
                    <button onclick="generarPDFsSeleccionados()" class="btn-primary">
                        <i class="fas fa-file-pdf"></i> Generar PDFs seleccionados
                    </button>
                </div>
            </div>

            <!-- FILTROS DE BÚSQUEDA -->
            <div class="filters-card">
                <h3><i class="fas fa-filter"></i> Filtros de Búsqueda</h3>
                <form method="GET" class="filters-form">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label for="documento"><i class="fas fa-id-card"></i> Documento</label>
                            <input type="text" id="documento" name="documento" 
                                   value="<?php echo htmlspecialchars($_GET['documento'] ?? ''); ?>"
                                   placeholder="Buscar por documento...">
                        </div>
                        
                        <div class="filter-group">
                            <label for="nombre"><i class="fas fa-user"></i> Nombre</label>
                            <input type="text" id="nombre" name="nombre" 
                                   value="<?php echo htmlspecialchars($_GET['nombre'] ?? ''); ?>"
                                   placeholder="Buscar por nombre...">
                        </div>
                        
                        <div class="filter-group">
                            <label for="grado"><i class="fas fa-graduation-cap"></i> Grado</label>
                            <select id="grado" name="grado">
                                <option value="">Todos los grados</option>
                                <?php foreach ($grados as $grado): ?>
                                    <option value="<?php echo htmlspecialchars($grado); ?>"
                                        <?php echo (isset($_GET['grado']) && $_GET['grado'] == $grado) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($grado); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="completo"><i class="fas fa-check-circle"></i> Estado</label>
                            <select id="completo" name="completo">
                                <option value="">Todos</option>
                                <option value="1" <?php echo (isset($_GET['completo']) && $_GET['completo'] == '1') ? 'selected' : ''; ?>>Completos</option>
                                <option value="0" <?php echo (isset($_GET['completo']) && $_GET['completo'] == '0') ? 'selected' : ''; ?>>Incompletos</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="filter-actions">
                        <button type="submit" class="btn-filter">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <a href="estudiantes.php" class="btn-clear">
                            <i class="fas fa-eraser"></i> Limpiar filtros
                        </a>
                    </div>
                </form>
            </div>

            <!-- TABLA DE ESTUDIANTES -->
            <form id="form-estudiantes" method="POST"
      action="../controllers_admin/AdminController.php?action=descargar_pdfs_firmados"
 onsubmit="return validarSeleccionPDF()">
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th width="50">
                                    <input type="checkbox" id="select-all" onclick="toggleSelectAll(this)">
                                </th>
                                <th>Documento</th>
                                <th>Nombre Completo</th>
                                <th>Tipo</th>
                                <th>Grado</th>
                                <th>Fecha Registro</th>
                                <th>Progreso</th>
                                <th>Documentos</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($estudiantes)): ?>
                                <?php foreach ($estudiantes as $est): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="estudiantes_ids[]" 
                                               value="<?php echo $est['usuario_id']; ?>" 
                                               class="estudiante-checkbox">
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($est['documento']); ?></strong>
                                    </td>
                                    <td>
                                        <?php 
                                        $nombre = trim(($est['nombres'] ?? '') . ' ' . ($est['apellidos'] ?? ''));
                                        if (!empty($nombre)) {
                                            echo '<div class="student-name">' . htmlspecialchars($nombre) . '</div>';
                                        } else {
                                            echo '<span class="text-muted"><i>Sin información personal</i></span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo ($est['tipo_estudiante'] == 'Nuevo') ? 'badge-new' : 'badge-old'; ?>">
                                            <?php echo htmlspecialchars($est['tipo_estudiante'] ?? 'No definido'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($est['grado_matricular'] ?? 'No asignado'); ?>
                                    </td>
                                    <td>
                                        <?php echo date('d/m/Y', strtotime($est['fecha_creacion'])); ?>
                                        <small class="text-muted"><?php echo date('H:i', strtotime($est['fecha_creacion'])); ?></small>
                                    </td>
                                    <td>
                                        <?php 
                                        $pasos_completados = 0;
                                        $total_pasos = 7;
                                        $pasos = ['tiene_personal', 'tiene_general', 'tiene_madre', 'tiene_padre', 'tiene_acudiente', 'tiene_vivienda', 'tiene_salud'];
                                        
                                        foreach ($pasos as $paso) {
                                            if (!empty($est[$paso])) $pasos_completados++;
                                        }
                                        
                                        $porcentaje = round(($pasos_completados / $total_pasos) * 100);
                                        $color = ($porcentaje == 100) ? '#2ecc71' : (($porcentaje >= 50) ? '#f39c12' : '#e74c3c');
                                        ?>
                                        <div class="progress-container">
                                            <div class="progress-info">
                                                <span><?php echo $pasos_completados; ?>/<?php echo $total_pasos; ?></span>
                                                <span><?php echo $porcentaje; ?>%</span>
                                            </div>
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width: <?php echo $porcentaje; ?>%; background: <?php echo $color; ?>;"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($est['documentos_subidos'] > 0): ?>
                                            <span class="badge badge-docs">
                                                <i class="fas fa-file"></i> <?php echo $est['documentos_subidos']; ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-none">Sin documentos</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="ver_estudiante.php?id=<?php echo $est['usuario_id']; ?>" 
                                               class="btn-action btn-view" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <?php
$tieneFirmada = !empty($est['tiene_hoja_firmada']);
$color = $tieneFirmada ? '#27ae60' : '#bdbdbd';
$hover = $tieneFirmada ? '#219653' : '#9e9e9e';
?>

<button type="button"
    onclick="descargarHojaMatricula(<?php echo $est['usuario_id']; ?>)"
    title="<?php echo $tieneFirmada 
        ? 'Descargar hoja de matrícula firmada' 
        : 'El estudiante no ha subido la hoja firmada'; ?>"
    onmouseover="
        this.style.background='<?php echo $hover; ?>';
        this.style.transform='translateY(-1px)';
    "
    onmouseout="
        this.style.background='<?php echo $color; ?>';
        this.style.transform='translateY(0)';
    "
    style="
        border: none;
        outline: none;
        cursor: pointer;
        background: <?php echo $color; ?>;
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 5px;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    ">
    <i class="fas fa-file-pdf"></i>
</button>

                                            
                                            <a href="javascript:void(0);" 
   onclick="eliminarUsuario(<?php echo (int)$est['usuario_id']; ?>)" 
   class="btn-action btn-delete" 
   title="Eliminar">
   <i class="fas fa-trash-alt"></i>
</a>

                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="empty-table">
                                        <div class="empty-state">
                                            <i class="fas fa-user-slash"></i>
                                            <h3>No se encontraron estudiantes</h3>
                                            <p><?php echo (!empty($filtros)) ? 'Intenta con otros filtros de búsqueda' : 'No hay estudiantes registrados aún'; ?></p>
                                            <?php if (!empty($filtros)): ?>
                                                <a href="estudiantes.php" class="btn-primary">
                                                    <i class="fas fa-undo"></i> Ver todos los estudiantes
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- BOTONES MASIVOS -->
                <?php if (!empty($estudiantes)): ?>
                <div class="bulk-actions">
                    <div class="bulk-info">
                        <span id="selected-count">0 estudiantes seleccionados</span>
                    </div>
                    <div class="bulk-buttons">
                        <button type="button" class="btn-bulk btn-excel" onclick="exportarSeleccionados()">
                            <i class="fas fa-file-excel"></i> Exportar seleccionados a Excel
                        </button>
                    </div>
                </div>
                <?php endif; ?>
            </form>

            <!-- ESTADÍSTICAS RÁPIDAS -->
            <?php if (!empty($estudiantes)): ?>
            <div class="quick-stats">
                <h3><i class="fas fa-chart-pie"></i> Resumen</h3>
                <div class="stats-row">
                    <div class="stat-item">
                        <span class="stat-label">Total estudiantes:</span>
                        <span class="stat-value"><?php echo count($estudiantes); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Nuevos:</span>
                        <span class="stat-value">
                            <?php 
                            $nuevos = array_filter($estudiantes, fn($e) => ($e['tipo_estudiante'] ?? '') == 'Nuevo');
                            echo count($nuevos);
                            ?>
                        </span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Antiguos:</span>
                        <span class="stat-value">
                            <?php 
                            $antiguos = array_filter($estudiantes, fn($e) => ($e['tipo_estudiante'] ?? '') == 'Antiguo');
                            echo count($antiguos);
                            ?>
                        </span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Completos:</span>
                        <span class="stat-value">
                            <?php 
                            $completos = array_filter($estudiantes, function($e) {
                                $pasos = ['tiene_personal', 'tiene_general', 'tiene_madre', 'tiene_padre', 'tiene_acudiente', 'tiene_vivienda', 'tiene_salud'];
                                foreach ($pasos as $paso) {
                                    if (empty($e[$paso])) return false;
                                }
                                return true;
                            });
                            echo count($completos);
                            ?>
                        </span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>
<script>
function descargarHojaMatricula(usuario_id) {
    if (!usuario_id) {
        alert('❌ ID inválido');
        return;
    }

    window.location.href =
        `../controllers_admin/AdminController.php?action=descargar_hoja_firmada&id=${usuario_id}`;
}
</script>

    <script>
    // FUNCIONES PARA SELECCIÓN MASIVA
    function toggleSelectAll(checkbox) {
        const checkboxes = document.querySelectorAll('.estudiante-checkbox');
        checkboxes.forEach(cb => cb.checked = checkbox.checked);
        actualizarContador();
    }

    function seleccionarTodos() {
        const checkboxes = document.querySelectorAll('.estudiante-checkbox');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        
        checkboxes.forEach(cb => cb.checked = !allChecked);
        document.getElementById('select-all').checked = !allChecked;
        actualizarContador();
    }

    function actualizarContador() {
        const selected = document.querySelectorAll('.estudiante-checkbox:checked').length;
        document.getElementById('selected-count').textContent = selected + ' estudiante(s) seleccionado(s)';
    }

    // Inicializar contador y listeners
    document.addEventListener('DOMContentLoaded', function() {
        actualizarContador();
        
        document.querySelectorAll('.estudiante-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', actualizarContador);
        });
    });

    // GENERAR PDFs SELECCIONADOS
   function generarPDFsSeleccionados() {
    const selected = document.querySelectorAll('.estudiante-checkbox:checked');

    if (selected.length === 0) {
        alert('⚠️ Por favor selecciona al menos un estudiante');
        return;
    }

    // Enviar formulario directamente
    document.getElementById('form-estudiantes').submit();
}

    

    // EXPORTAR A EXCEL
    // REEMPLAZA COMPLETAMENTE LA FUNCIÓN exportarSeleccionados:
function exportarSeleccionados() {
    const checkboxes = document.querySelectorAll('.estudiante-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('⚠️ Por favor selecciona al menos un estudiante');
        return false;
    }
    
    // Obtener IDs de forma segura
    const ids = [];
    checkboxes.forEach(checkbox => {
        if (checkbox.value && checkbox.value !== '') {
            ids.push(checkbox.value);
        }
    });
    
    if (ids.length === 0) {
        alert('❌ No se pudieron obtener los IDs de los estudiantes seleccionados');
        return false;
    }
    
    console.log('Exportando IDs:', ids); // Para depuración
    
    // Redirigir con los IDs
    window.location.href = '../controllers_admin/AdminController.php?action=export_excel&ids=' + ids.join(',');
    return true;
}
// ========== VALIDAR SELECCIÓN PARA PDFs ==========
function validarSeleccionPDF() {
    const checkboxes = document.querySelectorAll('.estudiante-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('⚠️ Por favor selecciona al menos un estudiante');
        return false;
    }
    
    // Verificar que los checkboxes tienen valores válidos
    let todosValidos = true;
    const ids = [];
    
    checkboxes.forEach(checkbox => {
        if (!checkbox.value || checkbox.value === '') {
            console.error('Checkbox sin valor:', checkbox);
            todosValidos = false;
        } else {
            ids.push(checkbox.value);
        }
    });
    
    if (!todosValidos || ids.length === 0) {
        alert('❌ Error: Algunos estudiantes seleccionados no tienen ID válido');
        return false;
    }
    
    console.log('Generando PDFs para IDs:', ids);
    return true;
}

// ========== GENERAR PDF INDIVIDUAL ==========
function generarPDFIndividual(usuario_id) {
    if (!usuario_id || usuario_id === '') {
        alert('❌ Error: ID de estudiante no válido');
        return;
    }
    
    console.log('Generando PDF para usuario:', usuario_id);
    window.open('../controllers/StudentController.php?action=generar_hoja_matricula&usuario_id=' + usuario_id, '_blank');
}
    </script>
   <script>
    // ========== FUNCIÓN PARA ELIMINAR USUARIO ==========
    function eliminarUsuario(usuario_id) {
        console.log('Intentando eliminar usuario ID:', usuario_id);

        if (!usuario_id) {
            alert('❌ Error: ID no válido');
            return false;
        }

        // Redirige a la pantalla de confirmación
        window.location.href = 'confirmar_eliminar.php?id=' + usuario_id;
        return false;
    }
</script>

</body>
</html>