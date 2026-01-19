<?php
// models/DocumentModel.php
class DocumentModel {
    private $conn;
    private $table_name = "documentos_estudiante";

    public function __construct($db) {
        $this->conn = $db;
    }

    // ========== VALIDACIONES DE ARCHIVOS ==========
    public function validarArchivo($file, $tipo = 'pdf') {
        $errors = [];
        
        if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return $errors; // No es error, simplemente no subió archivo
        }
        
        // Verificar si hay error de subida
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = $this->getUploadError($file['error']);
            return $errors;
        }
        
        // Configuración según tipo
        $config = [
            'pdf' => ['max_size' => 5 * 1024 * 1024, 'mimes' => ['application/pdf'], 'exts' => ['pdf']],
            'img' => ['max_size' => 3 * 1024 * 1024, 'mimes' => ['image/jpeg', 'image/jpg', 'image/png'], 'exts' => ['jpg', 'jpeg', 'png']]
        ];
        
        if (!isset($config[$tipo])) {
            $errors[] = "Tipo de archivo no configurado";
            return $errors;
        }
        
        $max_size = $config[$tipo]['max_size'];
        $allowed_mimes = $config[$tipo]['mimes'];
        $allowed_exts = $config[$tipo]['exts'];
        
        // Validar tamaño
        if ($file['size'] > $max_size) {
            $max_mb = $max_size / (1024 * 1024);
            $errors[] = "Máximo {$max_mb}MB permitido";
        }
        
        // Validar tipo MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime_type, $allowed_mimes)) {
            $errors[] = "Tipo de archivo no permitido";
        }
        
        // Validar extensión
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_exts)) {
            $errors[] = "Extensión .$ext no permitida";
        }
        
        return $errors;
    }
    
    private function getUploadError($error_code) {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'Archivo excede tamaño máximo del servidor',
            UPLOAD_ERR_FORM_SIZE => 'Archivo excede tamaño máximo del formulario',
            UPLOAD_ERR_PARTIAL => 'Archivo subido parcialmente',
            UPLOAD_ERR_NO_FILE => 'No se seleccionó archivo',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta carpeta temporal',
            UPLOAD_ERR_CANT_WRITE => 'Error al escribir en disco',
            UPLOAD_ERR_EXTENSION => 'Extensión de PHP detuvo la subida'
        ];
        return $errors[$error_code] ?? 'Error desconocido en subida';
    }
    
    // ========== MÉTODOS DE BASE DE DATOS ==========
    public function guardarDocumento($data) {
        // Verificar si ya existe
        $existente = $this->getDocumentoPorTipo($data['usuario_id'], $data['tipo_documento']);
        
        if ($existente) {
            // Actualizar documento existente
            return $this->actualizarDocumento($existente['id'], $data);
        } else {
            // Insertar nuevo documento
            $query = "INSERT INTO " . $this->table_name . " 
                      (usuario_id, tipo_documento, nombre_archivo, ruta_archivo, estado) 
                      VALUES (:usuario_id, :tipo_documento, :nombre_archivo, :ruta_archivo, :estado)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':usuario_id', $data['usuario_id']);
            $stmt->bindParam(':tipo_documento', $data['tipo_documento']);
            $stmt->bindParam(':nombre_archivo', $data['nombre_archivo']);
            $stmt->bindParam(':ruta_archivo', $data['ruta_archivo']);
            $stmt->bindParam(':estado', $data['estado']);
            
            return $stmt->execute();
        }
    }
    
    private function getDocumentoPorTipo($usuario_id, $tipo_documento) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE usuario_id = :usuario_id 
                  AND tipo_documento = :tipo_documento 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':tipo_documento', $tipo_documento);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    private function actualizarDocumento($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET nombre_archivo = :nombre_archivo, 
                      ruta_archivo = :ruta_archivo,
                      fecha_subida = CURRENT_TIMESTAMP,
                      estado = 'pendiente'
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre_archivo', $data['nombre_archivo']);
        $stmt->bindParam(':ruta_archivo', $data['ruta_archivo']);
        
        return $stmt->execute();
    }
    
    public function getDocumentosByUsuario($usuario_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE usuario_id = :usuario_id 
                  ORDER BY fecha_subida DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getEstadosDocumentos($usuario_id) {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado = 'aprobado' THEN 1 ELSE 0 END) as aprobados,
                    SUM(CASE WHEN estado = 'rechazado' THEN 1 ELSE 0 END) as rechazados
                  FROM " . $this->table_name . " 
                  WHERE usuario_id = :usuario_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function eliminarDocumento($id) {
        // Primero obtener la ruta para eliminar el archivo físico
        $doc = $this->getDocumentoById($id);
        if ($doc && file_exists(__DIR__ . '/../' . $doc['ruta_archivo'])) {
            unlink(__DIR__ . '/../' . $doc['ruta_archivo']);
        }
        
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    private function getDocumentoById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>