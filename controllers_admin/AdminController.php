<?php
// controllers_admin/AdminController.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

@session_start();
require_once __DIR__ . '/../models_admin/AdminModel.php';
require_once __DIR__ . '/../models/StudentModel.php';
require_once __DIR__ . '/../config/database.php';

class AdminController
{
    private $adminModel;
    private $studentModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->getConnection();
        $this->adminModel = new AdminModel($db);
        $this->studentModel = new StudentModel($db);
    }

    // ========== EXPORTAR A EXCEL (CORREGIDO) ==========
    public function exportarExcel()
    {
        if (!$this->verificarAdmin()) {
            return;
        }

        // Obtener IDs si vienen por GET
        $ids = [];
        if (isset($_GET['ids']) && !empty($_GET['ids'])) {
            $ids = explode(',', $_GET['ids']);
        }

        // Obtener datos CON filtros
        $datos = $this->adminModel->getDatosParaExportarConFiltros($ids);

        if (empty($datos)) {
            $_SESSION['error'] = "No hay datos para exportar";
            header('Location: ../admin/estudiantes.php');
            exit;
        }

        // CABECERAS PARA EXCEL
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="estudiantes_' . date('Y-m-d_H-i') . '.xls"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo '<!DOCTYPE html>';
        echo '<html>';
        echo '<head>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
        echo '<style>';
        echo 'td, th { border: 1px solid #ddd; padding: 8px; font-family: Calibri, Arial; font-size: 11pt; }';
        echo 'th { background-color: #4CAF50; color: white; font-weight: bold; text-align: center; }';
        echo 'tr:nth-child(even) { background-color: #f2f2f2; }';
        echo '</style>';
        echo '</head>';
        echo '<body>';

        echo '<table>';

        // ENCABEZADOS
        $encabezados = [
            'DOCUMENTO',
            'NOMBRE COMPLETO',
            'TIPO ESTUDIANTE',
            'GRADO',
            'FECHA NACIMIENTO',
            'GÉNERO',
            'CELULAR',
            'DIRECCIÓN',
            'BARRIO',
            'EPS',
            'SISBEN',
            'MADRE',
            'CELULAR MADRE',
            'CORREO MADRE',
            'PADRE',
            'CELULAR PADRE',
            'CORREO PADRE',
            'DOCUMENTOS APROBADOS',
            'FECHA REGISTRO'
        ];

        echo '<tr>';
        foreach ($encabezados as $encabezado) {
            echo '<th>' . htmlspecialchars($encabezado) . '</th>';
        }
        echo '</tr>';

        // DATOS
        foreach ($datos as $fila) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($fila['documento'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($fila['nombre_completo'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($fila['tipo_estudiante'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($fila['grado'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($fila['fecha_nacimiento'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($fila['genero'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($fila['celular'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($fila['direccion'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($fila['barrio'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($fila['eps'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($fila['sisben'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($fila['madre'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($fila['celular_madre'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($fila['correo_madre'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($fila['padre'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($fila['celular_padre'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($fila['correo_padre'] ?? '') . '</td>';
            echo '<td style="text-align: center;">' . htmlspecialchars($fila['documentos_aprobados'] ?? '0') . '</td>';
            echo '<td>' . htmlspecialchars($fila['fecha_registro'] ?? '') . '</td>';
            echo '</tr>';
        }

        echo '</table>';
        echo '</body>';
        echo '</html>';
        exit;
    }
public function descargarPDFsFirmadosMasivos()
{
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        $_SESSION['error'] = 'Acceso no autorizado';
        header('Location: ../admin/estudiantes.php');
        exit;
    }

    if (empty($_POST['estudiantes_ids'])) {
        $_SESSION['error'] = 'No se seleccionaron estudiantes';
        header('Location: ../admin/estudiantes.php');
        exit;
    }

    $ids = $_POST['estudiantes_ids'];

    $zip = new ZipArchive();
    $zipName = 'Hojas_Matricula_Firmadas_' . date('Y-m-d_H-i-s') . '.zip';
    $zipPath = sys_get_temp_dir() . '/' . $zipName;

    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        $_SESSION['error'] = 'No se pudo crear el archivo ZIP';
        header('Location: ../admin/estudiantes.php');
        exit;
    }

    $agregados = 0;

    foreach ($ids as $usuario_id) {
        // Buscar hoja firmada en BD
        $doc = $this->adminModel->getDocumentoFirmado($usuario_id);

        if (!$doc || empty($doc['ruta_archivo'])) {
            continue; // ⛔ no existe
        }

        $rutaFisica = __DIR__ . '/../' . $doc['ruta_archivo'];

        if (file_exists($rutaFisica)) {
            $nombreZip = $usuario_id . '_Hoja_Matricula_Firmada.pdf';
            $zip->addFile($rutaFisica, $nombreZip);
            $agregados++;
        }
    }

    $zip->close();

    if ($agregados === 0) {
        unlink($zipPath);
        $_SESSION['error'] = 'Ningún estudiante tiene hoja de matrícula firmada';
        header('Location: ../admin/estudiantes.php');
        exit;
    }

    // Descargar ZIP
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $zipName . '"');
    header('Content-Length: ' . filesize($zipPath));

    readfile($zipPath);
    unlink($zipPath);
    exit;
}

    // ========== GENERAR PDFs MASIVOS ==========
    // public function generarPDFsMasivos()
    // {
    //     if (!$this->verificarAdmin()) {
    //         return;
    //     }

    //     $ids = $_POST['estudiantes_ids'] ?? [];

    //     if (empty($ids)) {
    //         $_SESSION['error'] = "No se seleccionaron estudiantes";
    //         header('Location: ../admin/estudiantes.php');
    //         exit;
    //     }

    //     $estudiantes = $this->adminModel->getUsuariosParaPDFMasivo($ids);

    //     if (empty($estudiantes)) {
    //         $_SESSION['error'] = "No se encontraron estudiantes";
    //         header('Location: ../admin/estudiantes.php');
    //         exit;
    //     }

    //     if (count($estudiantes) === 1) {
    //         header('Location: ../controllers/StudentController.php?action=generar_hoja_matricula&usuario_id=' . $estudiantes[0]['usuario_id']);
    //         exit;
    //     } else {
    //         $_SESSION['info'] = "Generando " . count($estudiantes) . " PDFs...";
    //         echo '<script>';
    //         foreach ($estudiantes as $est) {
    //             echo 'window.open("../controllers/StudentController.php?action=generar_hoja_matricula&usuario_id=' . $est['usuario_id'] . '", "_blank");';
    //         }
    //         echo 'setTimeout(function() { window.location.href = "../admin/estudiantes.php"; }, 1000);';
    //         echo '</script>';
    //         exit;
    //     }
    // }

    // ========== ACTUALIZAR ESTADO DOCUMENTO ==========
    public function actualizarEstadoDocumento()
    {
        if (!$this->verificarAdmin()) {
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Método no permitido";
            header('Location: ../admin/documentos.php');
            exit;
        }

        $documento_id = $_POST['documento_id'] ?? null;
        $estado = $_POST['estado'] ?? null;
        $observaciones = $_POST['observaciones'] ?? '';

        if (!$documento_id || !$estado) {
            $_SESSION['error'] = "Datos incompletos";
            header('Location: ../admin/documentos.php');
            exit;
        }

        if ($this->adminModel->actualizarEstadoDocumento($documento_id, $estado, $observaciones)) {
            $_SESSION['success'] = "Estado del documento actualizado correctamente";
        } else {
            $_SESSION['error'] = "Error al actualizar el estado";
        }

        header('Location: ../admin/documentos.php');
        exit;
    }

    // ========== VERIFICAR SI ES ADMIN ==========
    private function verificarAdmin()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['error'] = "Acceso restringido a administradores";
            header('Location: ../index.php');
            exit;
        }
        return true;
    }

    // ========== OBTENER DATOS PARA DASHBOARD ==========
    public function getDatosDashboard()
    {
        if (!$this->verificarAdmin()) {
            return null;
        }

        return $this->adminModel->getEstadisticas();
    }

    // ========== BUSCAR ESTUDIANTES ==========
    public function buscarEstudiantes($filtros = [])
    {
        if (!$this->verificarAdmin()) {
            return [];
        }

        return $this->adminModel->getEstudiantes($filtros);
    }

    // ========== OBTENER ESTUDIANTE COMPLETO ==========
    public function getEstudianteCompleto($usuario_id)
    {
        if (!$this->verificarAdmin()) {
            return null;
        }

        return $this->adminModel->getEstudianteCompleto($usuario_id);
    }

    // ========== ELIMINAR USUARIO ==========
    // ========== ELIMINAR USUARIO ==========
    public function eliminarUsuario()
    {
        if (!$this->verificarAdmin())
            return;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Método no permitido";
            header('Location: ../admin/estudiantes.php');
            exit;
        }

        $usuario_id = $_POST['usuario_id'] ?? null;
        $confirmar = $_POST['confirmar'] ?? null;

        if (!$usuario_id || $confirmar !== '1') {
            $_SESSION['error'] = "Confirmación requerida";
            header('Location: ../admin/estudiantes.php');
            exit;
        }

        if ($this->adminModel->eliminarUsuario($usuario_id)) {
            $_SESSION['success'] = "Usuario eliminado correctamente";
        } else {
            $_SESSION['error'] = "Error al eliminar el usuario. Puede tener registros relacionados.";
        }

        header('Location: ../admin/estudiantes.php');
        exit;
    }
    public function descargarHojaFirmada()
    {
        if (empty($_GET['id'])) {
            die('ID no válido');
        }

        $id = (int) $_GET['id'];

        $rutaBase = __DIR__ . '/../assets/uploads/estudiantes/' . $id;

        if (!is_dir($rutaBase)) {
            die('No hay documentos para este estudiante');
        }

        // Buscar hoja firmada
        $archivos = glob($rutaBase . '/hoja_matricula_firmada*.pdf');

        if (empty($archivos)) {
            die('El estudiante no ha subido la hoja firmada');
        }

        $archivo = $archivos[0];

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($archivo) . '"');
        header('Content-Length: ' . filesize($archivo));

        readfile($archivo);
        exit;
    }

}

// ========== PROCESAR ACCIONES ==========
$action = $_POST['action'] ?? $_GET['action'] ?? null;

if ($action) {
    $adminController = new AdminController();

    switch ($action) {
        case 'export_excel':
            $adminController->exportarExcel();
            break;

        // case 'generar_pdfs_masivos':
        //     $adminController->generarPDFsMasivos();
        //     break;
        case 'descargar_pdfs_firmados':
    $adminController->descargarPDFsFirmadosMasivos();
    break;

        case 'descargar_hoja_firmada':
            $adminController->descargarHojaFirmada();
            break;

        case 'actualizar_estado_documento':
            $adminController->actualizarEstadoDocumento();
            break;

        case 'eliminar_usuario':
            $adminController->eliminarUsuario();
            break;

        default:
            $_SESSION['error'] = "Acción no válida";
            header('Location: ../admin/index.php');
            exit;
    }
}

?>