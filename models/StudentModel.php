<?php
class StudentModel
{
    private $conn;
    private $table_name = "estudiantes";
    private $table_general = "estudiantes_info_general";
    private $table_historial = "estudiantes_historial_academico";
    private $table_madre = "datos_madre"; // ← NUEVA TABLA
    private $table_padre = "datos_padre"; // ← NUEVA TABLA
    private $table_acudiente = "datos_acudiente"; // ← NUEVA TABLA
    private $table_vivienda = "datos_vivienda"; // ← NUEVA TABLA
    private $table_salud = "datos_salud"; // ← NUEVA TABLA
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // ======== MÉTODOS EXISTENTES (SE MANTIENEN IGUAL) ========

    // Guardar información personal del estudiante
    public function savePersonalInfo($data)
    {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET usuario_id = :usuario_id, 
                      nombres = :nombres, 
                      apellidos = :apellidos, 
                      grado_matricular = :grado_matricular, 
                      grado_actual = :grado_actual, 
                      sede = :sede, 
                      tipo_estudiante = :tipo_estudiante, 
                      fecha_nacimiento = :fecha_nacimiento, 
                      lugar_nacimiento = :lugar_nacimiento, 
                      edad = :edad, 
                      tipo_sangre = :tipo_sangre, 
                      numero_documento = :numero_documento, 
                      lugar_expedicion = :lugar_expedicion, 
                      tipo_documento = :tipo_documento, 
                      genero = :genero, 
                      celular = :celular, 
                      telefono_residencia = :telefono_residencia, 
                      correo_institucional = :correo_institucional, 
                      fecha_matricula = :fecha_matricula
                  ON DUPLICATE KEY UPDATE
                      nombres = :nombres, 
                      apellidos = :apellidos, 
                      grado_matricular = :grado_matricular, 
                      grado_actual = :grado_actual, 
                      sede = :sede, 
                      tipo_estudiante = :tipo_estudiante, 
                      fecha_nacimiento = :fecha_nacimiento, 
                      lugar_nacimiento = :lugar_nacimiento, 
                      edad = :edad, 
                      tipo_sangre = :tipo_sangre, 
                      numero_documento = :numero_documento, 
                      lugar_expedicion = :lugar_expedicion, 
                      tipo_documento = :tipo_documento, 
                      genero = :genero, 
                      celular = :celular, 
                      telefono_residencia = :telefono_residencia, 
                      correo_institucional = :correo_institucional, 
                      fecha_matricula = :fecha_matricula,
                      updated_at = CURRENT_TIMESTAMP";

        $stmt = $this->conn->prepare($query);

        // Limpiar y bindear los datos
        $stmt->bindParam(":usuario_id", $data['usuario_id']);
        $stmt->bindParam(":nombres", $data['nombres']);
        $stmt->bindParam(":apellidos", $data['apellidos']);
        $stmt->bindParam(":grado_matricular", $data['grado_matricular']);
        $stmt->bindParam(":grado_actual", $data['grado_actual']);
        $stmt->bindParam(":sede", $data['sede']);
        $stmt->bindParam(":tipo_estudiante", $data['tipo_estudiante']);
        $stmt->bindParam(":fecha_nacimiento", $data['fecha_nacimiento']);
        $stmt->bindParam(":lugar_nacimiento", $data['lugar_nacimiento']);
        $stmt->bindParam(":edad", $data['edad']);
        $stmt->bindParam(":tipo_sangre", $data['tipo_sangre']);
        $stmt->bindParam(":numero_documento", $data['numero_documento']);
        $stmt->bindParam(":lugar_expedicion", $data['lugar_expedicion']);
        $stmt->bindParam(":tipo_documento", $data['tipo_documento']);
        $stmt->bindParam(":genero", $data['genero']);
        $stmt->bindParam(":celular", $data['celular']);
        $stmt->bindParam(":telefono_residencia", $data['telefono_residencia']);
        $stmt->bindParam(":correo_institucional", $data['correo_institucional']);
        $stmt->bindParam(":fecha_matricula", $data['fecha_matricula']);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Obtener información personal del estudiante
    public function getPersonalInfo($usuario_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE usuario_id = :usuario_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    // ✅ FUNCIÓN CORREGIDA - SIN CAMPOS DE ACUDIENTE
    public function saveGeneralInfo($data)
{
    $query = "INSERT INTO " . $this->table_general . " 
          SET usuario_id = :usuario_id, 
              direccion = :direccion, 
              barrio = :barrio, 
              madre_cabeza_familia = :madre_cabeza_familia, 
              estrato = :estrato, 
              municipio = :municipio, 
              sisben = :sisben, 
              eps = :eps, 
              etnia = :etnia, 
              desplazado = :desplazado, 
              discapacidad_diagnostico = :discapacidad_diagnostico,
              tipo_discapacidad = :tipo_discapacidad,
              certificado_discapacidad = :certificado_discapacidad,
              numero_hermanos = :numero_hermanos,
              lugar_entre_hermanos = :lugar_entre_hermanos, 
              hermanos_en_colegio = :hermanos_en_colegio,
              lateralidad = :lateralidad,
              historial_2025_ano = :historial_2025_ano,
              historial_2025_colegio = :historial_2025_colegio,
              historial_2025_ciudad = :historial_2025_ciudad,
              historial_2025_grado = :historial_2025_grado,
              historial_2024_ano = :historial_2024_ano,
              historial_2024_colegio = :historial_2024_colegio,
              historial_2024_ciudad = :historial_2024_ciudad,
              historial_2024_grado = :historial_2024_grado,
              historial_2023_ano = :historial_2023_ano,
              historial_2023_colegio = :historial_2023_colegio,
              historial_2023_ciudad = :historial_2023_ciudad,
              historial_2023_grado = :historial_2023_grado,
              historial_2022_ano = :historial_2022_ano,
              historial_2022_colegio = :historial_2022_colegio,
              historial_2022_ciudad = :historial_2022_ciudad,
              historial_2022_grado = :historial_2022_grado,
              historial_2021_ano = :historial_2021_ano,
              historial_2021_colegio = :historial_2021_colegio,
              historial_2021_ciudad = :historial_2021_ciudad,
              historial_2021_grado = :historial_2021_grado,
              historial_2020_ano = :historial_2020_ano,
              historial_2020_colegio = :historial_2020_colegio,
              historial_2020_ciudad = :historial_2020_ciudad,
              historial_2020_grado = :historial_2020_grado,
              historial_2019_ano = :historial_2019_ano,
              historial_2019_colegio = :historial_2019_colegio,
              historial_2019_ciudad = :historial_2019_ciudad,
              historial_2019_grado = :historial_2019_grado
          ON DUPLICATE KEY UPDATE
              direccion = :direccion, 
              barrio = :barrio, 
              madre_cabeza_familia = :madre_cabeza_familia, 
              estrato = :estrato, 
              municipio = :municipio, 
              sisben = :sisben, 
              eps = :eps, 
              etnia = :etnia, 
              desplazado = :desplazado, 
              discapacidad_diagnostico = :discapacidad_diagnostico,
              tipo_discapacidad = :tipo_discapacidad, 
              certificado_discapacidad = :certificado_discapacidad,
              numero_hermanos = :numero_hermanos,
              lugar_entre_hermanos = :lugar_entre_hermanos, 
              hermanos_en_colegio = :hermanos_en_colegio,
              lateralidad = :lateralidad,
              historial_2025_ano = :historial_2025_ano,
              historial_2025_colegio = :historial_2025_colegio,
              historial_2025_ciudad = :historial_2025_ciudad,
              historial_2025_grado = :historial_2025_grado,
              historial_2024_ano = :historial_2024_ano,
              historial_2024_colegio = :historial_2024_colegio,
              historial_2024_ciudad = :historial_2024_ciudad,
              historial_2024_grado = :historial_2024_grado,
              historial_2023_ano = :historial_2023_ano,
              historial_2023_colegio = :historial_2023_colegio,
              historial_2023_ciudad = :historial_2023_ciudad,
              historial_2023_grado = :historial_2023_grado,
              historial_2022_ano = :historial_2022_ano,
              historial_2022_colegio = :historial_2022_colegio,
              historial_2022_ciudad = :historial_2022_ciudad,
              historial_2022_grado = :historial_2022_grado,
              historial_2021_ano = :historial_2021_ano,
              historial_2021_colegio = :historial_2021_colegio,
              historial_2021_ciudad = :historial_2021_ciudad,
              historial_2021_grado = :historial_2021_grado,
              historial_2020_ano = :historial_2020_ano,
              historial_2020_colegio = :historial_2020_colegio,
              historial_2020_ciudad = :historial_2020_ciudad,
              historial_2020_grado = :historial_2020_grado,
              historial_2019_ano = :historial_2019_ano,
              historial_2019_colegio = :historial_2019_colegio,
              historial_2019_ciudad = :historial_2019_ciudad,
              historial_2019_grado = :historial_2019_grado";

    $stmt = $this->conn->prepare($query);

    // Bind parameters existentes
    $stmt->bindParam(':usuario_id', $data['usuario_id']);
    $stmt->bindParam(':direccion', $data['direccion']);
    $stmt->bindParam(':barrio', $data['barrio']);
    $stmt->bindParam(':madre_cabeza_familia', $data['madre_cabeza_familia']);
    $stmt->bindParam(':estrato', $data['estrato']);
    $stmt->bindParam(':municipio', $data['municipio']);
    $stmt->bindParam(':sisben', $data['sisben']);
    $stmt->bindParam(':eps', $data['eps']);
    $stmt->bindParam(':etnia', $data['etnia']);
    $stmt->bindParam(':desplazado', $data['desplazado']);
    $stmt->bindParam(':discapacidad_diagnostico', $data['discapacidad_diagnostico']);
    $stmt->bindParam(':tipo_discapacidad', $data['tipo_discapacidad']);
    $stmt->bindParam(':certificado_discapacidad', $data['certificado_discapacidad']);
    $stmt->bindParam(':numero_hermanos', $data['numero_hermanos']);
    $stmt->bindParam(':lugar_entre_hermanos', $data['lugar_entre_hermanos']);
    $stmt->bindParam(':hermanos_en_colegio', $data['hermanos_en_colegio']);
    $stmt->bindParam(':lateralidad', $data['lateralidad']);

    // Bind parameters del historial (AÑO + COLEGIO + CIUDAD + GRADO)
    $stmt->bindParam(':historial_2025_ano', $data['historial_2025_ano']);
    $stmt->bindParam(':historial_2025_colegio', $data['historial_2025_colegio']);
    $stmt->bindParam(':historial_2025_ciudad', $data['historial_2025_ciudad']);
    $stmt->bindParam(':historial_2025_grado', $data['historial_2025_grado']);
    $stmt->bindParam(':historial_2024_ano', $data['historial_2024_ano']);
    $stmt->bindParam(':historial_2024_colegio', $data['historial_2024_colegio']);
    $stmt->bindParam(':historial_2024_ciudad', $data['historial_2024_ciudad']);
    $stmt->bindParam(':historial_2024_grado', $data['historial_2024_grado']);
    $stmt->bindParam(':historial_2023_ano', $data['historial_2023_ano']);
    $stmt->bindParam(':historial_2023_colegio', $data['historial_2023_colegio']);
    $stmt->bindParam(':historial_2023_ciudad', $data['historial_2023_ciudad']);
    $stmt->bindParam(':historial_2023_grado', $data['historial_2023_grado']);
    $stmt->bindParam(':historial_2022_ano', $data['historial_2022_ano']);
    $stmt->bindParam(':historial_2022_colegio', $data['historial_2022_colegio']);
    $stmt->bindParam(':historial_2022_ciudad', $data['historial_2022_ciudad']);
    $stmt->bindParam(':historial_2022_grado', $data['historial_2022_grado']);
    $stmt->bindParam(':historial_2021_ano', $data['historial_2021_ano']);
    $stmt->bindParam(':historial_2021_colegio', $data['historial_2021_colegio']);
    $stmt->bindParam(':historial_2021_ciudad', $data['historial_2021_ciudad']);
    $stmt->bindParam(':historial_2021_grado', $data['historial_2021_grado']);
    $stmt->bindParam(':historial_2020_ano', $data['historial_2020_ano']);
    $stmt->bindParam(':historial_2020_colegio', $data['historial_2020_colegio']);
    $stmt->bindParam(':historial_2020_ciudad', $data['historial_2020_ciudad']);
    $stmt->bindParam(':historial_2020_grado', $data['historial_2020_grado']);
    $stmt->bindParam(':historial_2019_ano', $data['historial_2019_ano']);
    $stmt->bindParam(':historial_2019_colegio', $data['historial_2019_colegio']);
    $stmt->bindParam(':historial_2019_ciudad', $data['historial_2019_ciudad']);
    $stmt->bindParam(':historial_2019_grado', $data['historial_2019_grado']);

    if ($stmt->execute()) {
        return true;
    }
    return false;
}
    // Obtener información general del estudiante
public function getGeneralInfo($usuario_id)
{
    $query = "SELECT * FROM " . $this->table_general . " WHERE usuario_id = :usuario_id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":usuario_id", $usuario_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return false;
}
    // ======== NUEVOS MÉTODOS PARA DATOS DE LA MADRE ========

    // Guardar datos de la madre
    public function saveMotherData($data)
{
    $query = "INSERT INTO " . $this->table_madre . " 
          SET usuario_id = :usuario_id, 
              nombres = :nombres, 
              apellidos = :apellidos, 
              celular = :celular, 
              telefono = :telefono, 
              direccion = :direccion, 
              ciudad = :ciudad, 
              tipo_documento = :tipo_documento, 
              numero_documento = :numero_documento, 
              lugar_expedicion = :lugar_expedicion, 
              fecha_nacimiento = :fecha_nacimiento, 
              lugar_nacimiento = :lugar_nacimiento, 
              genero = :genero, 
              correo = :correo, 
              nivel_estudios = :nivel_estudios, 
              ocupacion = :ocupacion, 
              es_acudiente = :es_acudiente, 
              asiste_reuniones = :asiste_reuniones, 
              cabeza_familia = :cabeza_familia, 
              convive_estudiante = :convive_estudiante
          ON DUPLICATE KEY UPDATE
              nombres = :nombres, 
              apellidos = :apellidos, 
              celular = :celular, 
              telefono = :telefono, 
              direccion = :direccion, 
              ciudad = :ciudad, 
              tipo_documento = :tipo_documento, 
              numero_documento = :numero_documento, 
              lugar_expedicion = :lugar_expedicion, 
              fecha_nacimiento = :fecha_nacimiento, 
              lugar_nacimiento = :lugar_nacimiento, 
              genero = :genero, 
              correo = :correo, 
              nivel_estudios = :nivel_estudios, 
              ocupacion = :ocupacion, 
              es_acudiente = :es_acudiente, 
              asiste_reuniones = :asiste_reuniones, 
              cabeza_familia = :cabeza_familia, 
              convive_estudiante = :convive_estudiante";

    $stmt = $this->conn->prepare($query);

    // Bind parameters
    $stmt->bindParam(':usuario_id', $data['usuario_id']);
    $stmt->bindParam(':nombres', $data['nombres']);
    $stmt->bindParam(':apellidos', $data['apellidos']);
    $stmt->bindParam(':celular', $data['celular']);
    $stmt->bindParam(':telefono', $data['telefono']);
    $stmt->bindParam(':direccion', $data['direccion']);
    $stmt->bindParam(':ciudad', $data['ciudad']);
    $stmt->bindParam(':tipo_documento', $data['tipo_documento']);
    $stmt->bindParam(':numero_documento', $data['numero_documento']);
    $stmt->bindParam(':lugar_expedicion', $data['lugar_expedicion']);
    $stmt->bindParam(':fecha_nacimiento', $data['fecha_nacimiento']);
    $stmt->bindParam(':lugar_nacimiento', $data['lugar_nacimiento']);
    $stmt->bindParam(':genero', $data['genero']);
    $stmt->bindParam(':correo', $data['correo']);
    $stmt->bindParam(':nivel_estudios', $data['nivel_estudios']);
    $stmt->bindParam(':ocupacion', $data['ocupacion']);
    $stmt->bindParam(':es_acudiente', $data['es_acudiente']);
    $stmt->bindParam(':asiste_reuniones', $data['asiste_reuniones']);
    $stmt->bindParam(':cabeza_familia', $data['cabeza_familia']);
    $stmt->bindParam(':convive_estudiante', $data['convive_estudiante']);

    if ($stmt->execute()) {
        return true;
    }
    return false;
}
    // Obtener datos de la madre
    public function getMotherData($usuario_id)
    {
        $query = "SELECT * FROM " . $this->table_madre . " WHERE usuario_id = :usuario_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }
    // ======== NUEVOS MÉTODOS PARA DATOS DEL PADRE ========

    // Guardar datos del padre
    public function saveFatherData($data)
{
    $query = "INSERT INTO " . $this->table_padre . " 
          SET usuario_id = :usuario_id, 
              nombres = :nombres, 
              apellidos = :apellidos, 
              celular = :celular, 
              telefono = :telefono, 
              direccion = :direccion, 
              ciudad = :ciudad, 
              tipo_documento = :tipo_documento, 
              numero_documento = :numero_documento, 
              lugar_expedicion = :lugar_expedicion, 
              fecha_nacimiento = :fecha_nacimiento, 
              lugar_nacimiento = :lugar_nacimiento, 
              genero = :genero, 
              correo = :correo, 
              nivel_estudios = :nivel_estudios, 
              ocupacion = :ocupacion, 
              es_acudiente = :es_acudiente, 
              asiste_reuniones = :asiste_reuniones, 
              cabeza_familia = :cabeza_familia, 
              convive_estudiante = :convive_estudiante
          ON DUPLICATE KEY UPDATE
              nombres = :nombres, 
              apellidos = :apellidos, 
              celular = :celular, 
              telefono = :telefono, 
              direccion = :direccion, 
              ciudad = :ciudad, 
              tipo_documento = :tipo_documento, 
              numero_documento = :numero_documento, 
              lugar_expedicion = :lugar_expedicion, 
              fecha_nacimiento = :fecha_nacimiento, 
              lugar_nacimiento = :lugar_nacimiento, 
              genero = :genero, 
              correo = :correo, 
              nivel_estudios = :nivel_estudios, 
              ocupacion = :ocupacion, 
              es_acudiente = :es_acudiente, 
              asiste_reuniones = :asiste_reuniones, 
              cabeza_familia = :cabeza_familia, 
              convive_estudiante = :convive_estudiante";

    $stmt = $this->conn->prepare($query);

    // Bind parameters
    $stmt->bindParam(':usuario_id', $data['usuario_id']);
    $stmt->bindParam(':nombres', $data['nombres']);
    $stmt->bindParam(':apellidos', $data['apellidos']);
    $stmt->bindParam(':celular', $data['celular']);
    $stmt->bindParam(':telefono', $data['telefono']);
    $stmt->bindParam(':direccion', $data['direccion']);
    $stmt->bindParam(':ciudad', $data['ciudad']);
    $stmt->bindParam(':tipo_documento', $data['tipo_documento']);
    $stmt->bindParam(':numero_documento', $data['numero_documento']);
    $stmt->bindParam(':lugar_expedicion', $data['lugar_expedicion']);
    $stmt->bindParam(':fecha_nacimiento', $data['fecha_nacimiento']);
    $stmt->bindParam(':lugar_nacimiento', $data['lugar_nacimiento']);
    $stmt->bindParam(':genero', $data['genero']);
    $stmt->bindParam(':correo', $data['correo']);
    $stmt->bindParam(':nivel_estudios', $data['nivel_estudios']);
    $stmt->bindParam(':ocupacion', $data['ocupacion']);
    $stmt->bindParam(':es_acudiente', $data['es_acudiente']);
    $stmt->bindParam(':asiste_reuniones', $data['asiste_reuniones']);
    $stmt->bindParam(':cabeza_familia', $data['cabeza_familia']);
    $stmt->bindParam(':convive_estudiante', $data['convive_estudiante']);

    if ($stmt->execute()) {
        return true;
    }
    return false;
}

    // Obtener datos del padre
    public function getFatherData($usuario_id)
    {
        $query = "SELECT * FROM " . $this->table_padre . " WHERE usuario_id = :usuario_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    // ======== NUEVOS MÉTODOS PARA DATOS DEL ACUDIENTE ========

    // Guardar datos del acudiente
    public function saveAcudienteData($data)
{
    $query = "INSERT INTO " . $this->table_acudiente . " 
          SET usuario_id = :usuario_id, 
              nombres = :nombres, 
              apellidos = :apellidos, 
              celular = :celular, 
              telefono = :telefono, 
              direccion = :direccion, 
              ciudad = :ciudad, 
              tipo_documento = :tipo_documento, 
              numero_documento = :numero_documento, 
              lugar_expedicion = :lugar_expedicion, 
              fecha_nacimiento = :fecha_nacimiento, 
              lugar_nacimiento = :lugar_nacimiento, 
              genero = :genero, 
              correo = :correo, 
              nivel_estudios = :nivel_estudios, 
              ocupacion = :ocupacion, 
              es_acudiente = :es_acudiente, 
              asiste_reuniones = :asiste_reuniones, 
              convive_estudiante = :convive_estudiante
          ON DUPLICATE KEY UPDATE
              nombres = :nombres, 
              apellidos = :apellidos, 
              celular = :celular, 
              telefono = :telefono, 
              direccion = :direccion, 
              ciudad = :ciudad, 
              tipo_documento = :tipo_documento, 
              numero_documento = :numero_documento, 
              lugar_expedicion = :lugar_expedicion, 
              fecha_nacimiento = :fecha_nacimiento, 
              lugar_nacimiento = :lugar_nacimiento, 
              genero = :genero, 
              correo = :correo, 
              nivel_estudios = :nivel_estudios, 
              ocupacion = :ocupacion, 
              es_acudiente = :es_acudiente, 
              asiste_reuniones = :asiste_reuniones, 
              convive_estudiante = :convive_estudiante";

    $stmt = $this->conn->prepare($query);

    // Bind parameters
    $stmt->bindParam(':usuario_id', $data['usuario_id']);
    $stmt->bindParam(':nombres', $data['nombres']);
    $stmt->bindParam(':apellidos', $data['apellidos']);
    $stmt->bindParam(':celular', $data['celular']);
    $stmt->bindParam(':telefono', $data['telefono']);
    $stmt->bindParam(':direccion', $data['direccion']);
    $stmt->bindParam(':ciudad', $data['ciudad']);
    $stmt->bindParam(':tipo_documento', $data['tipo_documento']);
    $stmt->bindParam(':numero_documento', $data['numero_documento']);
    $stmt->bindParam(':lugar_expedicion', $data['lugar_expedicion']);
    $stmt->bindParam(':fecha_nacimiento', $data['fecha_nacimiento']);
    $stmt->bindParam(':lugar_nacimiento', $data['lugar_nacimiento']);
    $stmt->bindParam(':genero', $data['genero']);
    $stmt->bindParam(':correo', $data['correo']);
    $stmt->bindParam(':nivel_estudios', $data['nivel_estudios']);
    $stmt->bindParam(':ocupacion', $data['ocupacion']);
    $stmt->bindParam(':es_acudiente', $data['es_acudiente']);
    $stmt->bindParam(':asiste_reuniones', $data['asiste_reuniones']);
    $stmt->bindParam(':convive_estudiante', $data['convive_estudiante']);

    if ($stmt->execute()) {
        return true;
    }
    return false;
}

    // Obtener datos del acudiente
    public function getAcudienteData($usuario_id)
    {
        $query = "SELECT * FROM " . $this->table_acudiente . " WHERE usuario_id = :usuario_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }
    // ======== NUEVOS MÉTODOS PARA DATOS DE VIVIENDA ========

    // Guardar datos de vivienda
    // Guardar datos de vivienda
    public function saveViviendaData($data)
    {
        $query = "INSERT INTO " . $this->table_vivienda . " 
              (usuario_id, numero_personas, tipo_vivienda, servicio_energia, 
               servicio_agua, servicio_alcantarillado, servicio_gas, 
               servicio_telefono, servicio_internet) 
              VALUES (:usuario_id, :numero_personas, :tipo_vivienda, :servicio_energia, 
                      :servicio_agua, :servicio_alcantarillado, :servicio_gas, 
                      :servicio_telefono, :servicio_internet)
              ON DUPLICATE KEY UPDATE
              numero_personas = :numero_personas,
              tipo_vivienda = :tipo_vivienda,
              servicio_energia = :servicio_energia,
              servicio_agua = :servicio_agua,
              servicio_alcantarillado = :servicio_alcantarillado,
              servicio_gas = :servicio_gas,
              servicio_telefono = :servicio_telefono,
              servicio_internet = :servicio_internet,
              fecha_actualizacion = CURRENT_TIMESTAMP";

        $stmt = $this->conn->prepare($query);

        // Bind parameters CORREGIDOS - Nombres SIN "vivienda_"
        $stmt->bindParam(':usuario_id', $data['usuario_id']);
        $stmt->bindParam(':numero_personas', $data['numero_personas']);
        $stmt->bindParam(':tipo_vivienda', $data['tipo_vivienda']);
        $stmt->bindParam(':servicio_energia', $data['servicio_energia']);
        $stmt->bindParam(':servicio_agua', $data['servicio_agua']);
        $stmt->bindParam(':servicio_alcantarillado', $data['servicio_alcantarillado']);
        $stmt->bindParam(':servicio_gas', $data['servicio_gas']);
        $stmt->bindParam(':servicio_telefono', $data['servicio_telefono']);
        $stmt->bindParam(':servicio_internet', $data['servicio_internet']);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Obtener datos de vivienda
    public function getViviendaData($usuario_id)
    {
        $query = "SELECT * FROM " . $this->table_vivienda . " WHERE usuario_id = :usuario_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    // ======== NUEVOS MÉTODOS PARA DATOS DE SALUD ========

    // Guardar datos de salud
    public function saveSaludData($data)
    {
        $query = "INSERT INTO " . $this->table_salud . " 
              (usuario_id, tratamiento_medico, alergia_medicamentos, alergia_especificar,
               enfermedad_diagnosticada, enfermedad_especificar, peso, estatura,
               observaciones_fisicas, medicamentos_permanentes, informacion_salud_adicional) 
              VALUES (:usuario_id, :tratamiento_medico, :alergia_medicamentos, :alergia_especificar,
                      :enfermedad_diagnosticada, :enfermedad_especificar, :peso, :estatura,
                      :observaciones_fisicas, :medicamentos_permanentes, :informacion_salud_adicional)
              ON DUPLICATE KEY UPDATE
              tratamiento_medico = :tratamiento_medico,
              alergia_medicamentos = :alergia_medicamentos,
              alergia_especificar = :alergia_especificar,
              enfermedad_diagnosticada = :enfermedad_diagnosticada,
              enfermedad_especificar = :enfermedad_especificar,
              peso = :peso,
              estatura = :estatura,
              observaciones_fisicas = :observaciones_fisicas,
              medicamentos_permanentes = :medicamentos_permanentes,
              informacion_salud_adicional = :informacion_salud_adicional,
              fecha_actualizacion = CURRENT_TIMESTAMP";

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':usuario_id', $data['usuario_id']);
        $stmt->bindParam(':tratamiento_medico', $data['tratamiento_medico']);
        $stmt->bindParam(':alergia_medicamentos', $data['alergia_medicamentos']);
        $stmt->bindParam(':alergia_especificar', $data['alergia_especificar']);
        $stmt->bindParam(':enfermedad_diagnosticada', $data['enfermedad_diagnosticada']);
        $stmt->bindParam(':enfermedad_especificar', $data['enfermedad_especificar']);
        $stmt->bindParam(':peso', $data['peso']);
        $stmt->bindParam(':estatura', $data['estatura']);
        $stmt->bindParam(':observaciones_fisicas', $data['observaciones_fisicas']);
        $stmt->bindParam(':medicamentos_permanentes', $data['medicamentos_permanentes']);
        $stmt->bindParam(':informacion_salud_adicional', $data['informacion_salud_adicional']);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Obtener datos de salud
    public function getSaludData($usuario_id)
    {
        $query = "SELECT * FROM " . $this->table_salud . " WHERE usuario_id = :usuario_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }
}
?>