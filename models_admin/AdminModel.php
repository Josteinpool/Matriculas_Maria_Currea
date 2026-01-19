<?php
// models/AdminModel.php
class AdminModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // ========== ESTADÍSTICAS ==========
    public function getEstadisticas() {
        $query = "SELECT 
                    (SELECT COUNT(*) FROM usuarios WHERE rol = 'usuario') as total_estudiantes,
                    (SELECT COUNT(*) FROM estudiantes WHERE usuario_id IS NOT NULL) as completaron_personal,
                    (SELECT COUNT(*) FROM estudiantes_info_general WHERE usuario_id IS NOT NULL) as completaron_general,
                    (SELECT COUNT(*) FROM datos_madre WHERE usuario_id IS NOT NULL) as completaron_madre,
                    (SELECT COUNT(*) FROM datos_padre WHERE usuario_id IS NOT NULL) as completaron_padre,
                    (SELECT COUNT(*) FROM datos_acudiente WHERE usuario_id IS NOT NULL) as completaron_acudiente,
                    (SELECT COUNT(*) FROM datos_vivienda WHERE usuario_id IS NOT NULL) as completaron_vivienda,
                    (SELECT COUNT(*) FROM datos_salud WHERE usuario_id IS NOT NULL) as completaron_salud,
                    (SELECT COUNT(DISTINCT usuario_id) FROM documentos_estudiante) as subieron_documentos";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ========== LISTADO DE ESTUDIANTES ==========
    public function getEstudiantes($filtros = []) {
        $where = [];
        $params = [];

        if (!empty($filtros['documento'])) {
            $where[] = "u.documento LIKE :documento";
            $params[':documento'] = '%' . $filtros['documento'] . '%';
        }

        if (!empty($filtros['nombre'])) {
            $where[] = "(e.nombres LIKE :nombre OR e.apellidos LIKE :nombre)";
            $params[':nombre'] = '%' . $filtros['nombre'] . '%';
        }

        if (!empty($filtros['grado'])) {
            $where[] = "e.grado_matricular = :grado";
            $params[':grado'] = $filtros['grado'];
        }

        if (isset($filtros['completo']) && $filtros['completo'] !== '') {
            if ($filtros['completo'] == '1') {
                $where[] = "e.usuario_id IS NOT NULL AND eg.usuario_id IS NOT NULL AND 
                           dm.usuario_id IS NOT NULL AND dp.usuario_id IS NOT NULL AND 
                           da.usuario_id IS NOT NULL AND dv.usuario_id IS NOT NULL AND 
                           ds.usuario_id IS NOT NULL";
            } else {
                $where[] = "(e.usuario_id IS NULL OR eg.usuario_id IS NULL OR 
                           dm.usuario_id IS NULL OR dp.usuario_id IS NULL OR 
                           da.usuario_id IS NULL OR dv.usuario_id IS NULL OR 
                           ds.usuario_id IS NULL)";
            }
        }

        // FILTRO POR IDs (para exportación selectiva)
        if (!empty($filtros['ids'])) {
            $placeholders = implode(',', array_fill(0, count($filtros['ids']), '?'));
            $where[] = "u.id IN ($placeholders)";
            foreach ($filtros['ids'] as $id) {
                $params[] = $id;
            }
        }

        $whereClause = '';
        if (!empty($where)) {
            $whereClause = 'WHERE ' . implode(' AND ', $where);
        }

        $query = "SELECT 
    u.id as usuario_id,
    u.documento,
    u.fecha_creacion,
    e.nombres,
    e.apellidos,
    e.tipo_estudiante,
    e.grado_matricular,
    e.fecha_nacimiento,
    e.genero,
    e.celular,
    e.fecha_matricula,
    eg.direccion,
    eg.barrio,
    eg.eps,
    eg.sisben,
    dm.nombres as madre_nombres,
    dm.apellidos as madre_apellidos,
    dm.celular as madre_celular,
    dm.correo as madre_correo,
    dp.nombres as padre_nombres,
    dp.apellidos as padre_apellidos,
    dp.celular as padre_celular,
    dp.correo as padre_correo,

    -- Verificar qué formularios están completos
    CASE WHEN e.usuario_id IS NOT NULL THEN 1 ELSE 0 END as tiene_personal,
    CASE WHEN eg.usuario_id IS NOT NULL THEN 1 ELSE 0 END as tiene_general,
    CASE WHEN dm.usuario_id IS NOT NULL THEN 1 ELSE 0 END as tiene_madre,
    CASE WHEN dp.usuario_id IS NOT NULL THEN 1 ELSE 0 END as tiene_padre,
    CASE WHEN da.usuario_id IS NOT NULL THEN 1 ELSE 0 END as tiene_acudiente,
    CASE WHEN dv.usuario_id IS NOT NULL THEN 1 ELSE 0 END as tiene_vivienda,
    CASE WHEN ds.usuario_id IS NOT NULL THEN 1 ELSE 0 END as tiene_salud,

    -- Contar documentos subidos
    (SELECT COUNT(*) 
     FROM documentos_estudiante de 
     WHERE de.usuario_id = u.id
    ) as documentos_subidos,

    -- 👉 VERIFICAR SI TIENE HOJA DE MATRÍCULA FIRMADA
    (SELECT COUNT(*) 
     FROM documentos_estudiante de2 
     WHERE de2.usuario_id = u.id
       AND de2.tipo_documento = 'hoja_matricula_firmada'
    ) as tiene_hoja_firmada

FROM usuarios u
LEFT JOIN estudiantes e ON u.id = e.usuario_id
LEFT JOIN estudiantes_info_general eg ON u.id = eg.usuario_id
LEFT JOIN datos_madre dm ON u.id = dm.usuario_id
LEFT JOIN datos_padre dp ON u.id = dp.usuario_id
LEFT JOIN datos_acudiente da ON u.id = da.usuario_id
LEFT JOIN datos_vivienda dv ON u.id = dv.usuario_id
LEFT JOIN datos_salud ds ON u.id = ds.usuario_id
$whereClause
ORDER BY u.fecha_creacion DESC";


        $stmt = $this->conn->prepare($query);
        
        // Manejar parámetros de forma dinámica
        $paramIndex = 1;
        foreach ($params as $key => $value) {
            if (is_int($key)) {
                // Para IDs numéricos
                $stmt->bindValue($paramIndex, $value, PDO::PARAM_INT);
                $paramIndex++;
            } else {
                // Para parámetros con nombre
                $stmt->bindValue($key, $value);
            }
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ========== OBTENER TODOS LOS DATOS DE UN ESTUDIANTE ==========
    public function getEstudianteCompleto($usuario_id) {
        $datos = [];

        $query = "SELECT * FROM usuarios WHERE id = :usuario_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();
        $datos['usuario'] = $stmt->fetch(PDO::FETCH_ASSOC);

        $tablas = [
            'estudiantes' => 'personal',
            'estudiantes_info_general' => 'general',
            'datos_madre' => 'madre',
            'datos_padre' => 'padre',
            'datos_acudiente' => 'acudiente',
            'datos_vivienda' => 'vivienda',
            'datos_salud' => 'salud'
        ];

        foreach ($tablas as $tabla => $key) {
            $query = "SELECT * FROM $tabla WHERE usuario_id = :usuario_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':usuario_id', $usuario_id);
            $stmt->execute();
            $datos[$key] = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        $query = "SELECT * FROM documentos_estudiante WHERE usuario_id = :usuario_id ORDER BY tipo_documento";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();
        $datos['documentos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $datos;
    }

    // ========== ACTUALIZAR ESTADO DE DOCUMENTO ==========
    public function actualizarEstadoDocumento($documento_id, $estado, $observaciones = null) {
        $query = "UPDATE documentos_estudiante 
                  SET estado = :estado, 
                      observaciones = :observaciones 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $documento_id);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':observaciones', $observaciones);
        
        return $stmt->execute();
    }

    // ========== EXPORTAR CON FILTROS (NUEVO MÉTODO) ==========
   public function getDatosParaExportarConFiltros($ids = [])
{
    $where = '';
    $params = [];

    if (!empty($ids)) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $where = "WHERE u.id IN ($placeholders)";
        $params = $ids;
    }

    $sql = "
        SELECT 
            u.documento,
            CONCAT(e.nombres, ' ', e.apellidos) AS nombre_completo,
            e.tipo_estudiante,
            e.grado_matricular AS grado,
            e.fecha_nacimiento,
            e.genero,
            e.celular,
            eg.direccion,
            eg.barrio,
            eg.eps,
            eg.sisben,
            CONCAT(dm.nombres, ' ', dm.apellidos) AS madre,
            dm.celular AS celular_madre,
            dm.correo AS correo_madre,
            CONCAT(dp.nombres, ' ', dp.apellidos) AS padre,
            dp.celular AS celular_padre,
            dp.correo AS correo_padre,
            (
                SELECT COUNT(*) 
                FROM documentos_estudiante de 
                WHERE de.usuario_id = u.id 
                  AND de.estado = 'aprobado'
            ) AS documentos_aprobados,
            u.fecha_creacion AS fecha_registro
        FROM usuarios u
        LEFT JOIN estudiantes e ON u.id = e.usuario_id
        LEFT JOIN estudiantes_info_general eg ON u.id = eg.usuario_id
        LEFT JOIN datos_madre dm ON u.id = dm.usuario_id
        LEFT JOIN datos_padre dp ON u.id = dp.usuario_id
        $where
        ORDER BY u.fecha_creacion DESC
    ";

    $stmt = $this->conn->prepare($sql);

    foreach ($params as $i => $id) {
        $stmt->bindValue($i + 1, $id, PDO::PARAM_INT);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    // ========== GENERAR ZIP DE PDFs ==========
    public function getUsuariosParaPDFMasivo($ids = []) {
        $query = "SELECT u.id as usuario_id, u.documento, e.nombres, e.apellidos 
                  FROM usuarios u
                  LEFT JOIN estudiantes e ON u.id = e.usuario_id
                  WHERE u.rol = 'usuario'";
        
        if (!empty($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $query .= " AND u.id IN ($placeholders)";
        }
        
        $query .= " ORDER BY e.apellidos, e.nombres";
        
        $stmt = $this->conn->prepare($query);
        
        if (!empty($ids)) {
            foreach ($ids as $index => $id) {
                $stmt->bindValue($index + 1, $id, PDO::PARAM_INT);
            }
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ========== ELIMINAR USUARIO ==========
    public function eliminarUsuario($usuario_id)
{
    if (!$usuario_id) {
        return false;
    }

    try {
        $this->conn->beginTransaction();

        // 1️⃣ Eliminar archivos físicos de documentos
        $stmt = $this->conn->prepare(
            "SELECT ruta_archivo FROM documentos_estudiante WHERE usuario_id = ?"
        );
        $stmt->execute([$usuario_id]);

        $documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($documentos as $doc) {
            if (!empty($doc['ruta_archivo'])) {
                $ruta = __DIR__ . '/../' . $doc['ruta_archivo'];
                if (file_exists($ruta)) {
                    unlink($ruta);
                }
            }
        }

        // 2️⃣ Eliminar registros de tablas hijas
        $tablas_hijas = [
            'documentos_estudiante',
            'datos_salud',
            'datos_vivienda',
            'datos_acudiente',
            'datos_padre',
            'datos_madre',
            'estudiantes_info_general',
            'estudiantes'
        ];

        foreach ($tablas_hijas as $tabla) {
            $stmt = $this->conn->prepare(
                "DELETE FROM {$tabla} WHERE usuario_id = ?"
            );
            $stmt->execute([$usuario_id]);
        }

        // 3️⃣ Eliminar usuario (tabla padre)
        $stmt = $this->conn->prepare(
            "DELETE FROM usuarios WHERE id = ?"
        );
        $stmt->execute([$usuario_id]);

        // 4️⃣ Eliminar carpeta física del estudiante
        $dir = __DIR__ . '/../assets/uploads/estudiantes/' . $usuario_id;
        if (is_dir($dir)) {
            $this->eliminarDirectorio($dir);
        }

        $this->conn->commit();
        return true;

    } catch (Exception $e) {
        $this->conn->rollBack();
        error_log("❌ ERROR ELIMINANDO USUARIO {$usuario_id}: " . $e->getMessage());
        return false;
    }
}

    private function eliminarDirectorio($dir)
{
    if (!is_dir($dir)) {
        return;
    }

    $archivos = scandir($dir);
    foreach ($archivos as $archivo) {
        if ($archivo !== '.' && $archivo !== '..') {
            $ruta = $dir . '/' . $archivo;
            if (is_dir($ruta)) {
                $this->eliminarDirectorio($ruta);
            } else {
                unlink($ruta);
            }
        }
    }
    rmdir($dir);
}


    // ========== OBTENER USUARIO PARA CONFIRMAR ==========
    public function getUsuarioParaEliminar($usuario_id) {
        $query = "SELECT u.id, u.documento, e.nombres, e.apellidos, 
                         (SELECT COUNT(*) FROM documentos_estudiante WHERE usuario_id = u.id) as total_documentos
                  FROM usuarios u
                  LEFT JOIN estudiantes e ON u.id = e.usuario_id
                  WHERE u.id = :usuario_id AND u.rol = 'usuario'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
 public function getDocumentoFirmado($usuario_id)
{
    $sql = "SELECT ruta_archivo
            FROM documentos_estudiante
            WHERE usuario_id = :usuario_id
              AND tipo_documento = 'hoja_matricula_firmada'
            LIMIT 1";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}


}
?>