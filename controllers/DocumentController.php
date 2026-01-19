<?php
// controllers/DocumentController.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

@session_start();
require_once __DIR__ . '/../models/DocumentModel.php';
require_once __DIR__ . '/../models/StudentModel.php';
require_once __DIR__ . '/../config/database.php';

class DocumentController {
    private $documentModel;
    private $studentModel;
    private $upload_dir;

    // Tipos de documentos (hardcodeados como array)
    private $tiposDocumentos = [
        'registro_civil' => ['nombre' => 'Registro Civil', 'obligatorio' => true, 'tipo_archivo' => 'pdf'],
        'tarjeta_identidad' => ['nombre' => 'Tarjeta de Identidad', 'obligatorio' => false, 'tipo_archivo' => 'pdf'],
        'documento_extranjero' => ['nombre' => 'Documento Extranjero', 'obligatorio' => false, 'tipo_archivo' => 'pdf'],
        'documento_madre' => ['nombre' => 'Documento Madre', 'obligatorio' => false, 'tipo_archivo' => 'pdf'],
        'documento_padre' => ['nombre' => 'Documento Padre', 'obligatorio' => false, 'tipo_archivo' => 'pdf'],
        'cedula_acudiente' => ['nombre' => 'Cédula Acudiente', 'obligatorio' => false, 'tipo_archivo' => 'pdf'],
        'recibo_publico' => ['nombre' => 'Recibo Público', 'obligatorio' => true, 'tipo_archivo' => 'pdf'],
        'certificado_eps' => ['nombre' => 'Certificado EPS', 'obligatorio' => true, 'tipo_archivo' => 'pdf'],
        'foto_estudiante' => ['nombre' => 'Foto Estudiante', 'obligatorio' => true, 'tipo_archivo' => 'img'],
        'certificados_escolaridad' => ['nombre' => 'Certificados Escolaridad', 'obligatorio' => true, 'tipo_archivo' => 'pdf'],
        'formulario_simpade' => ['nombre' => 'Formulario SIMPADE', 'obligatorio' => true, 'tipo_archivo' => 'pdf'],
        'formato_imagen' => ['nombre' => 'Formato de Imagen', 'obligatorio' => true, 'tipo_archivo' => 'pdf'],
        'acta_acuerdos' => ['nombre' => 'Acta de Acuerdos', 'obligatorio' => true, 'tipo_archivo' => 'pdf'],
        'hoja_matricula_firmada' => ['nombre' => 'Hoja Matrícula Firmada', 'obligatorio' => true, 'tipo_archivo' => 'pdf'],
        'certificado_discapacidad' => ['nombre' => 'Certificado Discapacidad', 'obligatorio' => false, 'tipo_archivo' => 'pdf']
    ];

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->documentModel = new DocumentModel($db);
        $this->studentModel = new StudentModel($db);
        
        // Directorio de subidas
        $this->upload_dir = __DIR__ . '/../assets/uploads/estudiantes/';
        
        // Crear directorio principal si no existe
        if (!file_exists($this->upload_dir)) {
            mkdir($this->upload_dir, 0777, true);
        }
    }
    
    // Método principal para subir documentos
    public function saveDocumentos() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = "Debe iniciar sesión para subir documentos";
            header('Location: ../home.php#adjuntar-documentos');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error_message'] = "Método no permitido";
            header('Location: ../home.php#adjuntar-documentos');
            exit;
        }
        
        $usuario_id = $_SESSION['user_id'];
        $resultados = [];
        $errors = [];
        $success_count = 0;
        
        // Crear carpeta personalizada para el usuario
        $user_dir = $this->upload_dir . $usuario_id . '/';
        if (!file_exists($user_dir)) {
            mkdir($user_dir, 0777, true);
        }
        
        // Verificar si necesita certificado de discapacidad
        $necesitaDiscapacidad = false;
        $generalData = $this->studentModel->getGeneralInfo($usuario_id);
        if ($generalData && isset($generalData['discapacidad_diagnostico'])) {
            $necesitaDiscapacidad = ($generalData['discapacidad_diagnostico'] === 'si');
        }
        
        // Procesar cada tipo de documento
        foreach ($this->tiposDocumentos as $tipo_key => $doc_info) {
            // Saltar certificado discapacidad si no lo necesita
            if ($tipo_key === 'certificado_discapacidad' && !$necesitaDiscapacidad) {
                continue;
            }
            
            $input_name = $tipo_key;
            $tipo_archivo = $doc_info['tipo_archivo'];
            $obligatorio = $doc_info['obligatorio'];
            $nombre_humano = $doc_info['nombre'];
            
            // Verificar si se subió archivo
            if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] !== UPLOAD_ERR_NO_FILE) {
                $file = $_FILES[$input_name];
                
                // Validar archivo
                $validation_errors = $this->documentModel->validarArchivo($file, $tipo_archivo);
                
                if (empty($validation_errors)) {
                    // Generar nombre único
                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $new_filename = $input_name . '_' . time() . '_' . uniqid() . '.' . $ext;
                    $destino = $user_dir . $new_filename;
                    
                    // Mover archivo
                    if (move_uploaded_file($file['tmp_name'], $destino)) {
                        // Ruta relativa para la BD
                        $ruta_relativa = 'assets/uploads/estudiantes/' . $usuario_id . '/' . $new_filename;
                        
                        // Guardar en base de datos
                        $data = [
                            'usuario_id' => $usuario_id,
                            'tipo_documento' => $input_name,
                            'nombre_archivo' => $file['name'], // Nombre original
                            'ruta_archivo' => $ruta_relativa,
                            'estado' => 'pendiente'
                        ];
                        
                        if ($this->documentModel->guardarDocumento($data)) {
                            $success_count++;
                            $resultados[$input_name] = [
                                'success' => true, 
                                'message' => "$nombre_humano subido correctamente",
                                'nombre_archivo' => $file['name']
                            ];
                        } else {
                            $errors[] = "Error al guardar $nombre_humano en la base de datos";
                        }
                    } else {
                        $errors[] = "Error al mover el archivo $nombre_humano";
                    }
                } else {
                    $errors[] = "$nombre_humano: " . implode(', ', $validation_errors);
                }
            } elseif ($obligatorio) {
                $errors[] = "El documento $nombre_humano es obligatorio";
            }
        }
        
        // Guardar resultados en sesión
        if (empty($errors)) {
            if ($success_count > 0) {
                $_SESSION['success_message'] = "✅ $success_count documento(s) subido(s) correctamente";
            } else {
                $_SESSION['info_message'] = "ℹ️ No se subieron nuevos documentos";
            }
            $_SESSION['documentos_subidos'] = $resultados;
        } else {
            $_SESSION['error_message'] = "❌ " . implode('<br>❌ ', $errors);
            // Si hubo algunos éxitos, mostrarlos también
            if ($success_count > 0) {
                $_SESSION['info_message'] = "✅ $success_count documento(s) se subieron correctamente, pero hubo errores en otros";
            }
        }
        
        header('Location: ../home.php#adjuntar-documentos');
        exit;
    }
    
    // Listar documentos del usuario
    public function listarDocumentos() {
        if (!isset($_SESSION['user_id'])) {
            return [];
        }
        
        $documentos = $this->documentModel->getDocumentosByUsuario($_SESSION['user_id']);
        
        // Añadir información humana a cada documento
        foreach ($documentos as &$doc) {
            $tipo_key = $doc['tipo_documento'];
            if (isset($this->tiposDocumentos[$tipo_key])) {
                $doc['nombre_humano'] = $this->tiposDocumentos[$tipo_key]['nombre'];
                $doc['obligatorio'] = $this->tiposDocumentos[$tipo_key]['obligatorio'];
            } else {
                $doc['nombre_humano'] = ucfirst(str_replace('_', ' ', $tipo_key));
                $doc['obligatorio'] = false;
            }
        }
        
        return $documentos;
    }
    
    // Obtener estados de documentos
    public function getEstadosDocumentos() {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        
        return $this->documentModel->getEstadosDocumentos($_SESSION['user_id']);
    }
    
    // Verificar si necesita certificado de discapacidad
    public function necesitaCertificadoDiscapacidad() {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        $generalData = $this->studentModel->getGeneralInfo($_SESSION['user_id']);
        if ($generalData && isset($generalData['discapacidad_diagnostico'])) {
            return ($generalData['discapacidad_diagnostico'] === 'si');
        }
        
        return false;
    }
}

// ========== PROCESAR ACCIONES ==========
if (isset($_POST['action'])) {
    $documentController = new DocumentController();
    
    switch ($_POST['action']) {
        case 'save_documentos':
            $documentController->saveDocumentos();
            break;
        default:
            $_SESSION['error_message'] = "Acción no válida";
            header('Location: ../home.php#adjuntar-documentos');
            exit;
    }
}
?>