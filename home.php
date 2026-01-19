<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
// PROTECCIÓN DE SESIÓN
if (!isset($_SESSION['user_id'])) {
  header('Location: index.php');
  exit;
}

// CARGAR DATOS EXISTENTES DEL ESTUDIANTE
require_once __DIR__ . '/controllers/StudentController.php';
require_once __DIR__ . '/config/database.php';

// Si no existe form_data en sesión, intentar cargar datos de la BD
if (!isset($_SESSION['form_data'])) {
  $database = new Database();
  $db = $database->getConnection();
  $studentModel = new StudentModel($db);

  $existingData = $studentModel->getPersonalInfo($_SESSION['user_id']);
  if ($existingData) {
    $_SESSION['form_data'] = [
      'nombres' => $existingData['nombres'],
      'apellidos' => $existingData['apellidos'],
      'tipoEstudiante' => $existingData['tipo_estudiante'],
      'fechaNacimiento' => $existingData['fecha_nacimiento'],
      'lugarNacimiento' => $existingData['lugar_nacimiento'],
      'edad' => $existingData['edad'],
      'tipoSangre' => $existingData['tipo_sangre'],
      'numeroDocumento' => $existingData['numero_documento'],
      'lugarExpedicion' => $existingData['lugar_expedicion'],
      'tipoDocumento' => $existingData['tipo_documento'],
      'genero' => $existingData['genero'],
      'celular' => $existingData['celular'],
      'telefonoResidencia' => $existingData['telefono_residencia'],
      'fechaMatricula' => $existingData['fecha_matricula']
    ];
  }
  // CARGAR DATOS DE INFORMACIÓN GENERAL
  // CARGAR DATOS DE INFORMACIÓN GENERAL (INCLUYENDO HISTORIAL)
  if (!isset($_SESSION['form_data_general'])) {
    $database = new Database();
    $db = $database->getConnection();
    $studentModel = new StudentModel($db);

    $generalData = $studentModel->getGeneralInfo($_SESSION['user_id']);
    if ($generalData) {
      $_SESSION['form_data_general'] = [
        'direccion' => $generalData['direccion'],
        'barrio' => $generalData['barrio'],
        'madre_cabeza_familia' => $generalData['madre_cabeza_familia'],
        'estrato' => $generalData['estrato'],
        'municipio' => $generalData['municipio'],
        'sisben' => $generalData['sisben'],
        'eps' => $generalData['eps'],
        'etnia' => $generalData['etnia'],
        'desplazado' => $generalData['desplazado'],
        'discapacidad_diagnostico' => $generalData['discapacidad_diagnostico'],
        'tipo_discapacidad' => $generalData['tipo_discapacidad'],
        'certificado_discapacidad' => $generalData['certificado_discapacidad'] ?? '', // ✅ MANTENIDO
        'numero_hermanos' => $generalData['numero_hermanos'],
        'lugar_entre_hermanos' => $generalData['lugar_entre_hermanos'],
        'hermanos_en_colegio' => $generalData['hermanos_en_colegio'],
        'lateralidad' => $generalData['lateralidad']
      ];

      // 🔥 CARGAR HISTORIAL ACADÉMICO DESDE LA MISMA TABLA
      $_SESSION['form_data_historial'] = [
        [
          'año' => $generalData['historial_2025_ano'] ?? '2025',
          'colegio' => $generalData['historial_2025_colegio'] ?? '',
          'ciudad' => $generalData['historial_2025_ciudad'] ?? '',
          'grado' => $generalData['historial_2025_grado'] ?? ''
        ],
        [
          'año' => $generalData['historial_2024_ano'] ?? '2024',
          'colegio' => $generalData['historial_2024_colegio'] ?? '',
          'ciudad' => $generalData['historial_2024_ciudad'] ?? '',
          'grado' => $generalData['historial_2024_grado'] ?? ''
        ],
        [
          'año' => $generalData['historial_2023_ano'] ?? '2023',
          'colegio' => $generalData['historial_2023_colegio'] ?? '',
          'ciudad' => $generalData['historial_2023_ciudad'] ?? '',
          'grado' => $generalData['historial_2023_grado'] ?? ''
        ],
        [
          'año' => $generalData['historial_2022_ano'] ?? '2022',
          'colegio' => $generalData['historial_2022_colegio'] ?? '',
          'ciudad' => $generalData['historial_2022_ciudad'] ?? '',
          'grado' => $generalData['historial_2022_grado'] ?? ''
        ],
        [
          'año' => $generalData['historial_2021_ano'] ?? '2021',
          'colegio' => $generalData['historial_2021_colegio'] ?? '',
          'ciudad' => $generalData['historial_2021_ciudad'] ?? '',
          'grado' => $generalData['historial_2021_grado'] ?? ''
        ],
        [
          'año' => $generalData['historial_2020_ano'] ?? '2020',
          'colegio' => $generalData['historial_2020_colegio'] ?? '',
          'ciudad' => $generalData['historial_2020_ciudad'] ?? '',
          'grado' => $generalData['historial_2020_grado'] ?? ''
        ],
        [
          'año' => $generalData['historial_2019_ano'] ?? '2019',
          'colegio' => $generalData['historial_2019_colegio'] ?? '',
          'ciudad' => $generalData['historial_2019_ciudad'] ?? '',
          'grado' => $generalData['historial_2019_grado'] ?? ''
        ]
      ];
    }
  }



  if (!isset($_SESSION['form_data_madre'])) {
    $database = new Database();
    $db = $database->getConnection();
    $studentModel = new StudentModel($db);

    $motherData = $studentModel->getMotherData($_SESSION['user_id']);
    if ($motherData) {
      $_SESSION['form_data_madre'] = [
        'madre_nombres' => $motherData['nombres'],
        'madre_apellidos' => $motherData['apellidos'],
        'madre_celular' => $motherData['celular'],
        'madre_telefono' => $motherData['telefono'],
        'madre_direccion' => $motherData['direccion'],
        'madre_ciudad' => $motherData['ciudad'],
        'madre_tipo_documento' => $motherData['tipo_documento'],
        'madre_numero_documento' => $motherData['numero_documento'],
        'madre_lugar_expedicion' => $motherData['lugar_expedicion'],
        'madre_fecha_nacimiento' => $motherData['fecha_nacimiento'],
        'madre_lugar_nacimiento' => $motherData['lugar_nacimiento'],
        'madre_genero' => $motherData['genero'],
        'madre_correo' => $motherData['correo'],
        'madre_nivel_estudios' => $motherData['nivel_estudios'],
        'madre_ocupacion' => $motherData['ocupacion'],
        'madre_acudiente' => $motherData['es_acudiente'],
        'madre_reuniones' => $motherData['asiste_reuniones'],
        'madre_cabeza_familia' => $motherData['cabeza_familia'],
        'madre_convive' => $motherData['convive_estudiante']
      ];
    }
  }
  // CARGAR DATOS DEL PADRE
  if (!isset($_SESSION['form_data_padre'])) {
    $database = new Database();
    $db = $database->getConnection();
    $studentModel = new StudentModel($db);

    $fatherData = $studentModel->getFatherData($_SESSION['user_id']);
    if ($fatherData) {
      $_SESSION['form_data_padre'] = [
        'padre_nombres' => $fatherData['nombres'],
        'padre_apellidos' => $fatherData['apellidos'],
        'padre_celular' => $fatherData['celular'],
        'padre_telefono' => $fatherData['telefono'],
        'padre_direccion' => $fatherData['direccion'],
        'padre_ciudad' => $fatherData['ciudad'],
        'padre_tipo_documento' => $fatherData['tipo_documento'],
        'padre_numero_documento' => $fatherData['numero_documento'],
        'padre_lugar_expedicion' => $fatherData['lugar_expedicion'],
        'padre_fecha_nacimiento' => $fatherData['fecha_nacimiento'],
        'padre_lugar_nacimiento' => $fatherData['lugar_nacimiento'],
        'padre_genero' => $fatherData['genero'],
        'padre_correo' => $fatherData['correo'],
        'padre_nivel_estudios' => $fatherData['nivel_estudios'],
        'padre_ocupacion' => $fatherData['ocupacion'],
        'padre_acudiente' => $fatherData['es_acudiente'],
        'padre_reuniones' => $fatherData['asiste_reuniones'],
        'padre_cabeza_familia' => $fatherData['cabeza_familia'],
        'padre_convive' => $fatherData['convive_estudiante']
      ];
    }
  }
  // CARGAR DATOS DEL ACUDIENTE
  if (!isset($_SESSION['form_data_acudiente'])) {
    $database = new Database();
    $db = $database->getConnection();
    $studentModel = new StudentModel($db);

    $acudienteData = $studentModel->getAcudienteData($_SESSION['user_id']);
    if ($acudienteData) {
      $_SESSION['form_data_acudiente'] = [
        'acudiente_nombres' => $acudienteData['nombres'],
        'acudiente_apellidos' => $acudienteData['apellidos'],
        'acudiente_celular' => $acudienteData['celular'],
        'acudiente_telefono' => $acudienteData['telefono'],
        'acudiente_direccion' => $acudienteData['direccion'],
        'acudiente_ciudad' => $acudienteData['ciudad'],
        'acudiente_tipo_documento' => $acudienteData['tipo_documento'],
        'acudiente_numero_documento' => $acudienteData['numero_documento'],
        'acudiente_lugar_expedicion' => $acudienteData['lugar_expedicion'],
        'acudiente_fecha_nacimiento' => $acudienteData['fecha_nacimiento'],
        'acudiente_lugar_nacimiento' => $acudienteData['lugar_nacimiento'],
        'acudiente_genero' => $acudienteData['genero'],
        'acudiente_correo' => $acudienteData['correo'],
        'acudiente_nivel_estudios' => $acudienteData['nivel_estudios'],
        'acudiente_ocupacion' => $acudienteData['ocupacion'],
        'acudiente_acudiente' => $acudienteData['es_acudiente'],
        'acudiente_reuniones' => $acudienteData['asiste_reuniones'],
        'acudiente_convive' => $acudienteData['convive_estudiante']
      ];
    }
  }
  // CARGAR DATOS DE VIVIENDA
  // CARGAR DATOS DE VIVIENDA
  // CARGAR DATOS DE VIVIENDA
  if (!isset($_SESSION['form_data_vivienda'])) {
    $database = new Database();
    $db = $database->getConnection();
    $studentModel = new StudentModel($db);

    $viviendaData = $studentModel->getViviendaData($_SESSION['user_id']);
    if ($viviendaData) {
      $_SESSION['form_data_vivienda'] = [
        'numero_personas' => $viviendaData['numero_personas'],
        'tipo_vivienda' => $viviendaData['tipo_vivienda'],
        'servicio_energia' => $viviendaData['servicio_energia'],
        'servicio_agua' => $viviendaData['servicio_agua'],
        'servicio_alcantarillado' => $viviendaData['servicio_alcantarillado'],
        'servicio_gas' => $viviendaData['servicio_gas'],
        'servicio_telefono' => $viviendaData['servicio_telefono'],
        'servicio_internet' => $viviendaData['servicio_internet']
      ];
    }
  }
  // CARGAR DATOS DE SALUD
  if (!isset($_SESSION['form_data_salud'])) {
    $database = new Database();
    $db = $database->getConnection();
    $studentModel = new StudentModel($db);

    $saludData = $studentModel->getSaludData($_SESSION['user_id']);
    if ($saludData) {
      $_SESSION['form_data_salud'] = [
        'tratamiento_medico' => $saludData['tratamiento_medico'],
        'alergia_medicamentos' => $saludData['alergia_medicamentos'],
        'alergia_especificar' => $saludData['alergia_especificar'],
        'enfermedad_diagnosticada' => $saludData['enfermedad_diagnosticada'],
        'enfermedad_especificar' => $saludData['enfermedad_especificar'],
        'peso' => $saludData['peso'],
        'estatura' => $saludData['estatura'],
        'observaciones_fisicas' => $saludData['observaciones_fisicas'],
        'medicamentos_permanentes' => $saludData['medicamentos_permanentes'],
        'informacion_salud_adicional' => $saludData['informacion_salud_adicional']
      ];
    }
  }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sistema de Matrículas - Información Importante</title>
  <link rel="stylesheet" href="assets/css/home.css" />
    
</head>

<body>
    <!-- Botón semicircular en esquina superior derecha -->
   <!-- Botón con el mismo gradiente del fondo -->
  <a href="logout.php" 
     onclick="return confirm('¿Estás seguro de cerrar sesión?')"
     style="
       position: fixed;
       top: 20px;
       right: 20px;
       background-color: red;
       color: white;
       padding: 8px 18px;
       border-radius: 30px 5px 30px 30px;
       text-decoration: none;
       font-weight: bold;
       font-size: 14px;
       z-index: 1000;
       border: 2px solid rgba(255,255,255,0.3);
       box-shadow: 0 4px 8px rgba(0,0,0,0.3);
       transition: all 0.3s ease;
       display: inline-block;
       text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
     "
     onmouseover="
       this.style.backgroundColor='red';
       this.style.transform='translateY(-3px) scale(1.02)';
       this.style.boxShadow='0 8px 16px rgba(0,0,0,0.4)';
       this.style.color='white';
     "
     onmouseout="
       this.style.backgroundColor='red';
       this.style.transform='translateY(0) scale(1)';
       this.style.boxShadow='0 4px 8px rgba(0,0,0,0.3)';
       this.style.color='white';
     ">
    Cerrar Sesión
  </a>
  <div class="home-container">
    <nav class="tabs">
      <button class="tab active">Información Importante</button>
      <button class="tab">Información Personal</button>
      <button class="tab">Información General</button>
      <button class="tab">Datos de la Madre</button>
      <button class="tab">Datos del Padre</button>
      <button class="tab">Acudiente</button>
      <button class="tab">Datos de la Vivienda</button>
      <button class="tab">Información de Salud</button>
      <button class="tab">Link y Documentos</button>
      <button class="tab">Adjuntar Documentos</button>
    </nav>

    <section class="content active">
      <h2>Información Importante</h2>
      <ul>
        <li>
          Los campos señalados con un
          <strong style="color: red">asterisco rojo (*)</strong> son de
          carácter obligatorio.
        </li>
        <li>
          En caso de que el <strong>padre o la madre</strong> haya fallecido,
          por favor escriba <strong>"NO APLICA"</strong> en los campos de
          texto y relacione <strong>99</strong> en los campos numéricos
          correspondientes.
        </li>
        <li>
          Recuerde registrar un
          <strong>correo electrónico válido</strong> tanto del padre como de
          la madre. Estos deben ser <strong>diferentes</strong>.
        </li>
        <li>
          El sistema <strong>no permitirá avanzar</strong> si no se
          diligencian todos los campos obligatorios.
        </li>
      </ul>
    </section>
    <!-- ======== INFORMACIÓN PERSONAL ======== -->
    <section id="info-personal" class="content" style="display: none">
      <h2>Información Personal del Estudiante</h2>



      <form action="controllers/StudentController.php?action=save_personal_info" method="POST" class="info-form">
        <!-- Fila 1: Nombres y Apellidos -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="nombres">Nombres: <span style="color: red">*</span></label>
            <input type="text" id="nombres" name="nombres"
              value="<?php echo isset($_SESSION['form_data']['nombres']) ? htmlspecialchars($_SESSION['form_data']['nombres']) : ''; ?>"
              required />
          </div>
          <div class="form-group">
            <label for="apellidos">Apellidos: <span style="color: red">*</span></label>
            <input type="text" id="apellidos" name="apellidos"
              value="<?php echo isset($_SESSION['form_data']['apellidos']) ? htmlspecialchars($_SESSION['form_data']['apellidos']) : ''; ?>"
              required />
          </div>
        </div>

        <!-- Fila 2: Grado a Matricular y Grado Actual -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="gradoMatricular">Grado a Matricular: <span style="color: red">*</span></label>
            <input type="text" id="gradoMatricular" name="gradoMatricular" placeholder="Se asignará automáticamente"
              readonly />
          </div>
          <div class="form-group">
            <label for="gradoActual">Grado que Cursa Actualmente: <span style="color: red">*</span></label>
            <input type="text" id="gradoActual" name="gradoActual" placeholder="Se completará automáticamente"
              readonly />
          </div>
        </div>

        <!-- Fila 3: Sede y Tipo Estudiante -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="sede">Sede:</label>
            <input type="text" id="sede" name="sede" value="ÚNICA" readonly />
          </div>
          <div class="form-group">
            <label for="tipoEstudiante">Estudiante Nuevo o Antiguo: <span style="color: red">*</span></label>
            <select id="tipoEstudiante" name="tipoEstudiante" required>
              <option value="">Seleccione</option>
              <option value="Antiguo" <?php echo (isset($_SESSION['form_data']['tipoEstudiante']) && $_SESSION['form_data']['tipoEstudiante'] == 'Antiguo') ? 'selected' : ''; ?>>Antiguo</option>
              <option value="Nuevo" <?php echo (isset($_SESSION['form_data']['tipoEstudiante']) && $_SESSION['form_data']['tipoEstudiante'] == 'Nuevo') ? 'selected' : ''; ?>>Nuevo</option>
            </select>
          </div>
        </div>

        <!-- Fila 4: Fecha Nacimiento y Lugar Nacimiento -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="fechaNacimiento">Fecha de Nacimiento: <span style="color: red">*</span></label>
            <input type="date" id="fechaNacimiento" name="fechaNacimiento"
              value="<?php echo isset($_SESSION['form_data']['fechaNacimiento']) ? $_SESSION['form_data']['fechaNacimiento'] : ''; ?>"
              required />
          </div>
          <div class="form-group">
            <label for="lugarNacimiento">Lugar de Nacimiento: <span style="color: red">*</span></label>
            <input type="text" id="lugarNacimiento" name="lugarNacimiento"
              value="<?php echo isset($_SESSION['form_data']['lugarNacimiento']) ? htmlspecialchars($_SESSION['form_data']['lugarNacimiento']) : ''; ?>"
              required />
          </div>
        </div>

        <!-- Fila 5: Edad y Tipo Sangre -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="edad">Edad: <span style="color: red">*</span></label>
            <input type="number" id="edad" name="edad" min="1" max="25"
              value="<?php echo isset($_SESSION['form_data']['edad']) ? htmlspecialchars($_SESSION['form_data']['edad']) : ''; ?>"
              required />
          </div>
          <div class="form-group">
            <label for="tipoSangre">Tipo de Sangre: <span style="color: red">*</span></label>
            <select id="tipoSangre" name="tipoSangre" required>
              <option value="">Seleccione</option>
              <option value="O+" <?php echo (isset($_SESSION['form_data']['tipoSangre']) && $_SESSION['form_data']['tipoSangre'] == 'O+') ? 'selected' : ''; ?>>O+</option>
              <option value="O-" <?php echo (isset($_SESSION['form_data']['tipoSangre']) && $_SESSION['form_data']['tipoSangre'] == 'O-') ? 'selected' : ''; ?>>O-</option>
              <option value="A+" <?php echo (isset($_SESSION['form_data']['tipoSangre']) && $_SESSION['form_data']['tipoSangre'] == 'A+') ? 'selected' : ''; ?>>A+</option>
              <option value="A-" <?php echo (isset($_SESSION['form_data']['tipoSangre']) && $_SESSION['form_data']['tipoSangre'] == 'A-') ? 'selected' : ''; ?>>A-</option>
              <option value="B+" <?php echo (isset($_SESSION['form_data']['tipoSangre']) && $_SESSION['form_data']['tipoSangre'] == 'B+') ? 'selected' : ''; ?>>B+</option>
              <option value="B-" <?php echo (isset($_SESSION['form_data']['tipoSangre']) && $_SESSION['form_data']['tipoSangre'] == 'B-') ? 'selected' : ''; ?>>B-</option>
              <option value="AB+" <?php echo (isset($_SESSION['form_data']['tipoSangre']) && $_SESSION['form_data']['tipoSangre'] == 'AB+') ? 'selected' : ''; ?>>AB+</option>
              <option value="AB-" <?php echo (isset($_SESSION['form_data']['tipoSangre']) && $_SESSION['form_data']['tipoSangre'] == 'AB-') ? 'selected' : ''; ?>>AB-</option>
            </select>
          </div>
        </div>

        <!-- Fila 6: Número Documento y Lugar Expedición -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="tipoDocumento">Tipo de Documento: <span style="color: red">*</span></label>
            <select id="tipoDocumento" name="tipoDocumento" required>
              <option value="">Seleccione</option>
              <option value="TI" <?php echo (isset($_SESSION['form_data']['tipoDocumento']) && $_SESSION['form_data']['tipoDocumento'] == 'TI') ? 'selected' : ''; ?>>Tarjeta de Identidad</option>
              <option value="RC" <?php echo (isset($_SESSION['form_data']['tipoDocumento']) && $_SESSION['form_data']['tipoDocumento'] == 'RC') ? 'selected' : ''; ?>>Registro Civil</option>
              <option value="CC" <?php echo (isset($_SESSION['form_data']['tipoDocumento']) && $_SESSION['form_data']['tipoDocumento'] == 'CC') ? 'selected' : ''; ?>>Cédula de Ciudadanía</option>
              <!-- NUEVAS OPCIONES -->
              <option value="PPT" <?php echo (isset($_SESSION['form_data']['tipoDocumento']) && $_SESSION['form_data']['tipoDocumento'] == 'PPT') ? 'selected' : ''; ?>>Permiso de Protección Temporal
              </option>
              <option value="PASAPORTE" <?php echo (isset($_SESSION['form_data']['tipoDocumento']) && $_SESSION['form_data']['tipoDocumento'] == 'PASAPORTE') ? 'selected' : ''; ?>>Pasaporte</option>
              <option value="CE" <?php echo (isset($_SESSION['form_data']['tipoDocumento']) && $_SESSION['form_data']['tipoDocumento'] == 'CE') ? 'selected' : ''; ?>>Cédula de Extranjería</option>
            </select>
          </div>
          <div class="form-group">
            <label for="numeroDocumento">Número de Documento: <span style="color: red">*</span></label>
            <input type="text" id="numeroDocumento" name="numeroDocumento"
              value="<?php echo isset($_SESSION['form_data']['numeroDocumento']) ? htmlspecialchars($_SESSION['form_data']['numeroDocumento']) : ''; ?>"
              required />
          </div>
        </div>

        <!-- Fila 7: Lugar Expedición y Género -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="lugarExpedicion">Lugar de Expedición: <span style="color: red">*</span></label>
            <input type="text" id="lugarExpedicion" name="lugarExpedicion"
              value="<?php echo isset($_SESSION['form_data']['lugarExpedicion']) ? htmlspecialchars($_SESSION['form_data']['lugarExpedicion']) : ''; ?>"
              required />
          </div>
          <div class="form-group">
            <label for="genero">Género: <span style="color: red">*</span></label>
            <select id="genero" name="genero" required>
              <option value="">Seleccione</option>
              <option value="Masculino" <?php echo (isset($_SESSION['form_data']['genero']) && $_SESSION['form_data']['genero'] == 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
              <option value="Femenino" <?php echo (isset($_SESSION['form_data']['genero']) && $_SESSION['form_data']['genero'] == 'Femenino') ? 'selected' : ''; ?>>Femenino</option>
              <option value="Otro" <?php echo (isset($_SESSION['form_data']['genero']) && $_SESSION['form_data']['genero'] == 'Otro') ? 'selected' : ''; ?>>Otro</option>
            </select>
          </div>
        </div>

        <!-- Fila 8: Celular y Teléfono Residencia -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="celular">Celular del Estudiante: <span style="color: red">*</span></label>
            <input type="tel" id="celular" name="celular" maxlength="10" pattern="[0-9]{10}"
              title="Ingrese un número de 10 dígitos"
              value="<?php echo isset($_SESSION['form_data']['celular']) ? htmlspecialchars($_SESSION['form_data']['celular']) : ''; ?>"
              required />
          </div>
          <div class="form-group">
            <label for="telefonoResidencia">Teléfono Residencia:</label> <!-- QUITAMOS * rojo -->
            <input type="tel" id="telefonoResidencia" name="telefonoResidencia" maxlength="10" pattern="[0-9]{7,10}"
              value="<?php echo isset($_SESSION['form_data']['telefonoResidencia']) ? htmlspecialchars($_SESSION['form_data']['telefonoResidencia']) : ''; ?>" />
            <!-- QUITAMOS el "required" -->
          </div>
        </div>

        <!-- Fila 9: Correo Institucional y Fecha Matrícula -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="correoInstitucional">Correo Institucional:</label>
            <input type="email" id="correoInstitucional" name="correoInstitucional"
              placeholder="Asignado automáticamente" readonly />
          </div>
          <div class="form-group">
            <label for="fechaMatricula">Fecha de Matrícula: <span style="color: red">*</span></label>
            <input type="date" id="fechaMatricula" name="fechaMatricula"
              value="<?php echo isset($_SESSION['form_data']['fechaMatricula']) ? $_SESSION['form_data']['fechaMatricula'] : ''; ?>"
              required />
          </div>
        </div>

        <button type="submit" class="save-btn">Guardar Información</button>
      </form>
    </section>
    <!-- ======== INFORMACIÓN GENERAL ======== -->
    <!-- ======== INFORMACIÓN GENERAL ======== -->
    <section id="info-general" class="content" style="display: none">
      <h2>Información General</h2>

      <!-- FORMULARIO UNIFICADO CON HISTORIAL INTEGRADO -->
      <form id="form-informacion-general" class="info-general-form" method="POST"
        action="controllers/StudentController.php?action=save_general_info">

        <!-- Dirección y Barrio -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="direccion">Dirección: <span style="color: red">*</span></label>
            <input type="text" id="direccion" name="direccion"
              value="<?php echo isset($_SESSION['form_data_general']['direccion']) ? htmlspecialchars($_SESSION['form_data_general']['direccion']) : ''; ?>"
              required />
          </div>
          <div class="form-group">
            <label for="barrio">Barrio: <span style="color: red">*</span></label>
            <input type="text" id="barrio" name="barrio"
              value="<?php echo isset($_SESSION['form_data_general']['barrio']) ? htmlspecialchars($_SESSION['form_data_general']['barrio']) : ''; ?>"
              required />
          </div>
        </div>

        <!-- Madre cabeza de familia y Estrato -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="madre-cabeza">Alumno Madre Cabeza de Familia: <span style="color: red">*</span></label>
            <select id="madre-cabeza" name="madre_cabeza" required>
              <option value="">Seleccione</option>
              <option value="si" <?php echo (isset($_SESSION['form_data_general']['madre_cabeza_familia']) && $_SESSION['form_data_general']['madre_cabeza_familia'] == 'si') ? 'selected' : ''; ?>>Sí</option>
              <option value="no" <?php echo (isset($_SESSION['form_data_general']['madre_cabeza_familia']) && $_SESSION['form_data_general']['madre_cabeza_familia'] == 'no') ? 'selected' : ''; ?>>No</option>
            </select>
          </div>
          <div class="form-group">
            <label for="estrato">Estrato: <span style="color: red">*</span></label>
            <select id="estrato" name="estrato" required>
              <option value="">Seleccione</option>
              <?php
              for ($i = 1; $i <= 6; $i++) {
                $selected = (isset($_SESSION['form_data_general']['estrato']) && $_SESSION['form_data_general']['estrato'] == $i) ? 'selected' : '';
                echo "<option value='$i' $selected>$i</option>";
              }
              ?>
            </select>
          </div>
        </div>

        <!-- Municipio -->
        <div class="form-row">
          <label for="municipio">Municipio: <span style="color: red">*</span></label>
          <input type="text" id="municipio" name="municipio"
            value="<?php echo isset($_SESSION['form_data_general']['municipio']) ? htmlspecialchars($_SESSION['form_data_general']['municipio']) : ''; ?>"
            required />
        </div>

        <!-- Historial Académico INTEGRADO -->
        <div class="form-row">
          <p>
            <strong>Historial Académico Año 2025: <span style="color: red">*</span></strong><br />
            (Por favor diligenciar todos los espacios: Para Primaria desde grado Jardín y para Bachillerato desde grado
            5°)
          </p>

          <table border="1" cellpadding="8" cellspacing="0">
            <thead style="background: #f1f1f1">
              <tr>
                <th>Año</th>
                <th>Colegio</th>
                <th>Ciudad</th>
                <th>Grado</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $years = [2025, 2024, 2023, 2022, 2021, 2020, 2019];

              foreach ($years as $index => $year) {
                $anoValue = $year;
                $colegioValue = '';
                $ciudadValue = '';
                $gradoValue = '';

                if (isset($_SESSION['form_data_historial'][$index])) {
                  $anoValue = $_SESSION['form_data_historial'][$index]['año'] ?? $year;
                  $colegioValue = htmlspecialchars($_SESSION['form_data_historial'][$index]['colegio'] ?? '');
                  $ciudadValue = htmlspecialchars($_SESSION['form_data_historial'][$index]['ciudad'] ?? '');
                  $gradoValue = htmlspecialchars($_SESSION['form_data_historial'][$index]['grado'] ?? '');
                }

                echo "<tr>";
                echo "<td><input type='text' name='historial_ano[]' value='$anoValue' placeholder='$year' required /></td>";
                echo "<td><input type='text' name='historial_colegio[]' value='$colegioValue' /></td>";
                echo "<td><input type='text' name='historial_ciudad[]' value='$ciudadValue' /></td>";
                echo "<td><input type='text' name='historial_grado[]' value='$gradoValue' /></td>";
                echo "</tr>";
              }
              ?>
            </tbody>
          </table>
        </div>

        <!-- Sisben y EPS -->
        <div class="form-row-inline">
          <!-- Sisben por Categorías -->
          <div class="form-group">
            <label for="sisben">Categoría Sisben: <span style="color: red">*</span></label>
            <select id="sisben" name="sisben" required>
              <option value="">Seleccione</option>
              <!-- OPCIÓN "NO APLICA" AGREGADA -->
              <option value="no aplica" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'no aplica') ? 'selected' : ''; ?>>No aplica</option>
              <!-- Grupo A -->
              <option value="A1" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'A1') ? 'selected' : ''; ?>>A1</option>
              <option value="A2" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'A2') ? 'selected' : ''; ?>>A2</option>
              <option value="A3" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'A3') ? 'selected' : ''; ?>>A3</option>
              <option value="A4" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'A4') ? 'selected' : ''; ?>>A4</option>
              <option value="A5" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'A5') ? 'selected' : ''; ?>>A5</option>

              <!-- Grupo B -->
              <option value="B1" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'B1') ? 'selected' : ''; ?>>B1</option>
              <option value="B2" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'B2') ? 'selected' : ''; ?>>B2</option>
              <option value="B3" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'B3') ? 'selected' : ''; ?>>B3</option>
              <option value="B4" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'B4') ? 'selected' : ''; ?>>B4</option>
              <option value="B5" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'B5') ? 'selected' : ''; ?>>B5</option>
              <option value="B6" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'B6') ? 'selected' : ''; ?>>B6</option>
              <option value="B7" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'B7') ? 'selected' : ''; ?>>B7</option>

              <!-- Grupo C -->
              <option value="C1" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'C1') ? 'selected' : ''; ?>>C1</option>
              <option value="C2" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'C2') ? 'selected' : ''; ?>>C2</option>
              <option value="C3" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'C3') ? 'selected' : ''; ?>>C3</option>
              <option value="C4" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'C4') ? 'selected' : ''; ?>>C4</option>
              <option value="C5" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'C5') ? 'selected' : ''; ?>>C5</option>
              <option value="C6" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'C6') ? 'selected' : ''; ?>>C6</option>
              <option value="C7" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'C7') ? 'selected' : ''; ?>>C7</option>
              <option value="C8" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'C8') ? 'selected' : ''; ?>>C8</option>
              <option value="C9" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'C9') ? 'selected' : ''; ?>>C9</option>
              <option value="C10" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'C10') ? 'selected' : ''; ?>>C10</option>
              <option value="C11" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'C11') ? 'selected' : ''; ?>>C11</option>
              <option value="C12" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'C12') ? 'selected' : ''; ?>>C12</option>
              <option value="C13" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'C13') ? 'selected' : ''; ?>>C13</option>
              <option value="C14" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'C14') ? 'selected' : ''; ?>>C14</option>
              <option value="C15" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'C15') ? 'selected' : ''; ?>>C15</option>
              <option value="C16" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'C16') ? 'selected' : ''; ?>>C16</option>
              <option value="C17" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'C17') ? 'selected' : ''; ?>>C17</option>
              <option value="C18" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'C18') ? 'selected' : ''; ?>>C18</option>

              <!-- Grupo D -->
              <option value="D1" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'D1') ? 'selected' : ''; ?>>D1</option>
              <option value="D2" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'D2') ? 'selected' : ''; ?>>D2</option>
              <option value="D3" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'D3') ? 'selected' : ''; ?>>D3</option>
              <option value="D4" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'D4') ? 'selected' : ''; ?>>D4</option>
              <option value="D5" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'D5') ? 'selected' : ''; ?>>D5</option>
              <option value="D6" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'D6') ? 'selected' : ''; ?>>D6</option>
              <option value="D7" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'D7') ? 'selected' : ''; ?>>D7</option>
              <option value="D8" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'D8') ? 'selected' : ''; ?>>D8</option>
              <option value="D9" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'D9') ? 'selected' : ''; ?>>D9</option>
              <option value="D10" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'D10') ? 'selected' : ''; ?>>D10</option>
              <option value="D11" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'D11') ? 'selected' : ''; ?>>D11</option>
              <option value="D12" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'D12') ? 'selected' : ''; ?>>D12</option>
              <option value="D13" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'D13') ? 'selected' : ''; ?>>D13</option>
              <option value="D14" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'D14') ? 'selected' : ''; ?>>D14</option>
              <option value="D15" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'D15') ? 'selected' : ''; ?>>D15</option>
              <option value="D16" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'D16') ? 'selected' : ''; ?>>D16</option>
              <option value="D17" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'D17') ? 'selected' : ''; ?>>D17</option>
              <option value="D18" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'D18') ? 'selected' : ''; ?>>D18</option>
              <option value="D19" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'D19') ? 'selected' : ''; ?>>D19</option>
              <option value="D20" <?php echo (isset($_SESSION['form_data_general']['sisben']) && $_SESSION['form_data_general']['sisben'] == 'D20') ? 'selected' : ''; ?>>D20</option>
            </select>
          </div>

          <div class="form-group">
            <label for="eps">IPS/EPS: <span style="color: red">*</span></label>
            <input type="text" id="eps" name="eps"
              value="<?php echo isset($_SESSION['form_data_general']['eps']) ? htmlspecialchars($_SESSION['form_data_general']['eps']) : ''; ?>"
              required />
          </div>
        </div>

        <!-- Etnia y Desplazado -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="etnia">Etnia: <span style="color: red">*</span></label>
            <select id="etnia" name="etnia">
              <option value="">Seleccione</option>
              <?php
              $etnias = [
                "NINGUNA",
                "AFRODESCENDIENTE",
                "ACHAGUA",
                "AMBALO",
                "AMORUA",
                "ARHUACO",
                "AWA",
                "BANIVA",
                "BARA",
                "BARASANO",
                "BARI",
                "BETOYE",
                "BORA",
                "CAMENTSA",
                "CHIMILAS",
                "CHIRICOA",
                "COREGUAJE",
                "COCAMA",
                "CUNA",
                "DESANO",
                "EMBERA CHAMI",
                "EMBERA KATIO",
                "GUAMBIANO",
                "INGA",
                "KOGUI",
                "MUISCA",
                "NEGRITUDES",
                "NUKAK",
                "PAEZ",
                "PIJAO",
                "PUINAVE",
                "RAIZAL",
                "WAYUU",
                "YANACONA",
                "ZENU"
              ];

              foreach ($etnias as $etnia) {
                $selected = (isset($_SESSION['form_data_general']['etnia']) && $_SESSION['form_data_general']['etnia'] == $etnia) ? 'selected' : '';
                echo "<option value='$etnia' $selected>$etnia</option>";
              }
              ?>
            </select>
          </div>
          <div class="form-group">
            <label for="desplazado" required>Desplazado: <span style="color: red">*</span></label>
            <select id="desplazado" name="desplazado">
              <option value="">Seleccione</option>
              <option value="si" <?php echo (isset($_SESSION['form_data_general']['desplazado']) && $_SESSION['form_data_general']['desplazado'] == 'si') ? 'selected' : ''; ?>>Sí</option>
              <option value="no" <?php echo (isset($_SESSION['form_data_general']['desplazado']) && $_SESSION['form_data_general']['desplazado'] == 'no') ? 'selected' : ''; ?>>No</option>
            </select>
          </div>
        </div>

        <!-- Discapacidad -->
        <div class="form-row">
          <label for="discapacidad-diagnostico" required>Discapacidad Diagnosticada: <span
              style="color: red">*</span></label>
          <select id="discapacidad-diagnostico" name="discapacidad_diagnostico">
            <option value="">Seleccione</option>
            <option value="si" <?php echo (isset($_SESSION['form_data_general']['discapacidad_diagnostico']) && $_SESSION['form_data_general']['discapacidad_diagnostico'] == 'si') ? 'selected' : ''; ?>>Sí</option>
            <option value="no" <?php echo (isset($_SESSION['form_data_general']['discapacidad_diagnostico']) && $_SESSION['form_data_general']['discapacidad_diagnostico'] == 'no') ? 'selected' : ''; ?>>No</option>
          </select>
        </div>

        <div id="discapacidad-div" style="display: none">
          <div class="form-row-inline">
            <div class="form-group">
              <label for="discapacidad">Tipo de Discapacidad: <span style="color: red">*</span></label>
              <select id="discapacidad" name="discapacidad">
                <option value="">Seleccione</option>
                <?php
                $discapacidades = [
                  "fisica" => "DISCAPACIDAD FÍSICA",
                  "auditiva" => "DISCAPACIDAD AUDITIVA",
                  "visual" => "DISCAPACIDAD VISUAL",
                  "sordoceguera" => "SORDOCEGUERA",
                  "intelectual" => "DISCAPACIDAD INTELECTUAL",
                  "psicosocial" => "DISCAPACIDAD PSICOSOCIAL (MENTAL)",
                  "multiple" => "DISCAPACIDAD MÚLTIPLE"
                ];

                foreach ($discapacidades as $valor => $texto) {
                  $selected = (isset($_SESSION['form_data_general']['tipo_discapacidad']) && $_SESSION['form_data_general']['tipo_discapacidad'] == $valor) ? 'selected' : '';
                  echo "<option value='$valor' $selected>$texto</option>";
                }
                ?>
              </select>
            </div>
            <div class="form-group">
              <label for="certificado">Adjuntar Certificado: <span style="color: red">*</span></label>
              <input type="file" id="certificado" name="certificado" />
            </div>
          </div>
        </div>

        <!-- Hermanos -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="numero_hermanos">Número de Hermanos: <span style="color: red">*</span></label>
            <select id="numero_hermanos" name="numero_hermanos" required>
              <option value="">Seleccione</option>
              <?php for ($i = 0; $i <= 7; $i++): ?>
                <option value="<?php echo $i; ?>" <?php echo (isset($_SESSION['form_data_general']['numero_hermanos']) && $_SESSION['form_data_general']['numero_hermanos'] == $i) ? 'selected' : ''; ?>>
                  <?php echo $i; ?>
                </option>
              <?php endfor; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="lugar_entre_hermanos">Lugar que ocupa entre ellos: <span style="color: red">*</span></label>
            <select id="lugar_entre_hermanos" name="lugar_entre_hermanos" required>
              <option value="">Seleccione</option>
              <option value="no aplica" <?php echo (isset($_SESSION['form_data_general']['lugar_entre_hermanos']) && $_SESSION['form_data_general']['lugar_entre_hermanos'] == 'no aplica') ? 'selected' : ''; ?>>No aplica
              </option>
              <?php for ($i = 1; $i <= 7; $i++): ?>
                <option value="<?php echo $i; ?>" <?php echo (isset($_SESSION['form_data_general']['lugar_entre_hermanos']) && $_SESSION['form_data_general']['lugar_entre_hermanos'] == $i) ? 'selected' : ''; ?>>
                  <?php echo $i; ?>
                </option>
              <?php endfor; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="hermanos_en_colegio">¿Tiene hermanos en el colegio?: <span style="color: red">*</span></label>
            <select id="hermanos_en_colegio" name="hermanos_en_colegio" required>
              <option value="">Seleccione</option>
              <option value="si" <?php echo (isset($_SESSION['form_data_general']['hermanos_en_colegio']) && $_SESSION['form_data_general']['hermanos_en_colegio'] == 'si') ? 'selected' : ''; ?>>Sí</option>
              <option value="no" <?php echo (isset($_SESSION['form_data_general']['hermanos_en_colegio']) && $_SESSION['form_data_general']['hermanos_en_colegio'] == 'no') ? 'selected' : ''; ?>>No</option>
            </select>
          </div>
        </div>

        <!-- Lateralidad -->
        <div class="form-row">
          <label for="lateralidad">Lateralidad del estudiante:<span style="color: red">*</span></label>
          <select id="lateralidad" name="lateralidad">
            <option value="">Seleccione</option>
            <option value="zurdo" <?php echo (isset($_SESSION['form_data_general']['lateralidad']) && $_SESSION['form_data_general']['lateralidad'] == 'zurdo') ? 'selected' : ''; ?>>Zurdo</option>
            <option value="diestro" <?php echo (isset($_SESSION['form_data_general']['lateralidad']) && $_SESSION['form_data_general']['lateralidad'] == 'diestro') ? 'selected' : ''; ?>>Diestro</option>
            <option value="ambidiestro" <?php echo (isset($_SESSION['form_data_general']['lateralidad']) && $_SESSION['form_data_general']['lateralidad'] == 'ambidiestro') ? 'selected' : ''; ?>>Ambidiestro</option>
          </select>
        </div>

        <hr />

        <button type="submit" class="save-btn">Guardar Información</button>
      </form>
    </section>
    <!-- ======== DATOS DE LA MADRE ======== -->
    <section id="datos-madre" class="content" style="display: none">
      <h2>Datos de la Madre</h2>

      <form id="form-datos-madre" class="info-general-form" method="POST"
        action="controllers/StudentController.php?action=save_mother_data">
        <input type="hidden" name="action" value="guardar_datos_madre">

        <!-- Nombres y Apellidos -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="madre-nombres">Nombres: <span style="color: red">*</span></label>
            <input type="text" id="madre-nombres" name="madre_nombres"
              value="<?php echo isset($_SESSION['form_data_madre']['madre_nombres']) ? htmlspecialchars($_SESSION['form_data_madre']['madre_nombres']) : ''; ?>"
              required />
          </div>
          <div class="form-group">
            <label for="madre-apellidos">Apellidos: <span style="color: red">*</span></label>
            <input type="text" id="madre-apellidos" name="madre_apellidos"
              value="<?php echo isset($_SESSION['form_data_madre']['madre_apellidos']) ? htmlspecialchars($_SESSION['form_data_madre']['madre_apellidos']) : ''; ?>"
              required />
          </div>
        </div>

        <!-- Celular y Teléfono Residencia -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="madre-celular">Número Celular: <span style="color: red">*</span></label>
            <input type="tel" id="madre-celular" name="madre_celular"
              value="<?php echo isset($_SESSION['form_data_madre']['madre_celular']) ? htmlspecialchars($_SESSION['form_data_madre']['madre_celular']) : ''; ?>"
              required />
          </div>
          <div class="form-group">
            <label for="madre-telefono">Teléfono Residencia (si tiene):</label>
            <input type="tel" id="madre-telefono" name="madre_telefono"
              value="<?php echo isset($_SESSION['form_data_madre']['madre_telefono']) ? htmlspecialchars($_SESSION['form_data_madre']['madre_telefono']) : ''; ?>" />
          </div>
        </div>

        <!-- Dirección y Ciudad -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="madre-direccion">Dirección: <span style="color: red">*</span></label>
            <input type="text" id="madre-direccion" name="madre_direccion"
              value="<?php echo isset($_SESSION['form_data_madre']['madre_direccion']) ? htmlspecialchars($_SESSION['form_data_madre']['madre_direccion']) : ''; ?>"
              required />
          </div>
          <div class="form-group">
            <label for="madre-ciudad">Ciudad: <span style="color: red">*</span></label>
            <input type="text" id="madre-ciudad" name="madre_ciudad"
              value="<?php echo isset($_SESSION['form_data_madre']['madre_ciudad']) ? htmlspecialchars($_SESSION['form_data_madre']['madre_ciudad']) : ''; ?>"
              required />
          </div>
        </div>

        <!-- Tipo Documento y Número Documento -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="madre-tipo-documento">Tipo de Documento: <span style="color: red">*</span></label>
            <select id="madre-tipo-documento" name="madre_tipo_documento" required>
              <option value="">Seleccione</option>
              <option value="CC" <?php echo (isset($_SESSION['form_data_madre']['madre_tipo_documento']) && $_SESSION['form_data_madre']['madre_tipo_documento'] == 'CC') ? 'selected' : ''; ?>>Cédula de Ciudadanía
              </option>
              <option value="CE" <?php echo (isset($_SESSION['form_data_madre']['madre_tipo_documento']) && $_SESSION['form_data_madre']['madre_tipo_documento'] == 'CE') ? 'selected' : ''; ?>>Cédula de Extranjería
              </option>
              <option value="TI" <?php echo (isset($_SESSION['form_data_madre']['madre_tipo_documento']) && $_SESSION['form_data_madre']['madre_tipo_documento'] == 'TI') ? 'selected' : ''; ?>>Tarjeta de Identidad
              </option>
              <option value="RC" <?php echo (isset($_SESSION['form_data_madre']['madre_tipo_documento']) && $_SESSION['form_data_madre']['madre_tipo_documento'] == 'RC') ? 'selected' : ''; ?>>Registro Civil
              </option>
              <option value="PAS" <?php echo (isset($_SESSION['form_data_madre']['madre_tipo_documento']) && $_SESSION['form_data_madre']['madre_tipo_documento'] == 'PAS') ? 'selected' : ''; ?>>Pasaporte</option>
            </select>
          </div>
          <div class="form-group">
            <label for="madre-numero-documento">Número de Documento: <span style="color: red">*</span></label>
            <input type="text" id="madre-numero-documento" name="madre_numero_documento"
              value="<?php echo isset($_SESSION['form_data_madre']['madre_numero_documento']) ? htmlspecialchars($_SESSION['form_data_madre']['madre_numero_documento']) : ''; ?>"
              required />
          </div>
        </div>

        <!-- Lugar Expedición y Fecha Nacimiento -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="madre-lugar-expedicion">Lugar de Expedición: <span style="color: red">*</span></label>
            <input type="text" id="madre-lugar-expedicion" name="madre_lugar_expedicion"
              value="<?php echo isset($_SESSION['form_data_madre']['madre_lugar_expedicion']) ? htmlspecialchars($_SESSION['form_data_madre']['madre_lugar_expedicion']) : ''; ?>"
              required />
          </div>
          <div class="form-group">
            <label for="madre-fecha-nacimiento">Fecha de Nacimiento: <span style="color: red">*</span></label>
            <input type="date" id="madre-fecha-nacimiento" name="madre_fecha_nacimiento"
              value="<?php echo isset($_SESSION['form_data_madre']['madre_fecha_nacimiento']) ? htmlspecialchars($_SESSION['form_data_madre']['madre_fecha_nacimiento']) : ''; ?>"
              required />
          </div>
        </div>

        <!-- Lugar Nacimiento y Género -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="madre-lugar-nacimiento">Lugar de Nacimiento: <span style="color: red">*</span></label>
            <input type="text" id="madre-lugar-nacimiento" name="madre_lugar_nacimiento"
              value="<?php echo isset($_SESSION['form_data_madre']['madre_lugar_nacimiento']) ? htmlspecialchars($_SESSION['form_data_madre']['madre_lugar_nacimiento']) : ''; ?>"
              required />
          </div>
          <div class="form-group">
            <label for="madre-genero">Género: <span style="color: red">*</span></label>
            <select id="madre-genero" name="madre_genero" required>
              <option value="">Seleccione</option>
              <option value="Femenino" <?php echo (isset($_SESSION['form_data_madre']['madre_genero']) && $_SESSION['form_data_madre']['madre_genero'] == 'Femenino') ? 'selected' : ''; ?>>Femenino</option>
              <option value="Masculino" <?php echo (isset($_SESSION['form_data_madre']['madre_genero']) && $_SESSION['form_data_madre']['madre_genero'] == 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
              <option value="Otro" <?php echo (isset($_SESSION['form_data_madre']['madre_genero']) && $_SESSION['form_data_madre']['madre_genero'] == 'Otro') ? 'selected' : ''; ?>>Otro</option>
              <option value="Prefiero no decir" <?php echo (isset($_SESSION['form_data_madre']['madre_genero']) && $_SESSION['form_data_madre']['madre_genero'] == 'Prefiero no decir') ? 'selected' : ''; ?>>Prefiero no
                decir
              </option>
            </select>
          </div>
        </div>

        <!-- Correo electrónico -->
        <div class="form-row">
          <label for="madre-correo">Correo electrónico: <span style="color: red">*</span></label>
          <input type="email" id="madre-correo" name="madre_correo"
            value="<?php echo isset($_SESSION['form_data_madre']['madre_correo']) ? htmlspecialchars($_SESSION['form_data_madre']['madre_correo']) : ''; ?>"
            required placeholder="Escribir en minúscula" />
        </div>

        <!-- Nivel Estudios y Ocupación -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="madre-nivel-estudios">Nivel de Estudios: <span style="color: red">*</span></label>
            <select id="madre-nivel-estudios" name="madre_nivel_estudios" required>
              <option value="">Seleccione</option>
              <option value="Primaria" <?php echo (isset($_SESSION['form_data_madre']['madre_nivel_estudios']) && $_SESSION['form_data_madre']['madre_nivel_estudios'] == 'Primaria') ? 'selected' : ''; ?>>Primaria
              </option>
              <option value="Bachillerato" <?php echo (isset($_SESSION['form_data_madre']['madre_nivel_estudios']) && $_SESSION['form_data_madre']['madre_nivel_estudios'] == 'Bachillerato') ? 'selected' : ''; ?>>Bachillerato
              </option>
              <option value="Técnico" <?php echo (isset($_SESSION['form_data_madre']['madre_nivel_estudios']) && $_SESSION['form_data_madre']['madre_nivel_estudios'] == 'Técnico') ? 'selected' : ''; ?>>Técnico</option>
              <option value="Tecnólogo" <?php echo (isset($_SESSION['form_data_madre']['madre_nivel_estudios']) && $_SESSION['form_data_madre']['madre_nivel_estudios'] == 'Tecnólogo') ? 'selected' : ''; ?>>Tecnólogo
              </option>
              <option value="Pregrado" <?php echo (isset($_SESSION['form_data_madre']['madre_nivel_estudios']) && $_SESSION['form_data_madre']['madre_nivel_estudios'] == 'Pregrado') ? 'selected' : ''; ?>>Pregrado
              </option>
              <option value="Especialización" <?php echo (isset($_SESSION['form_data_madre']['madre_nivel_estudios']) && $_SESSION['form_data_madre']['madre_nivel_estudios'] == 'Especialización') ? 'selected' : ''; ?>>
                Especialización
              </option>
              <option value="Maestría" <?php echo (isset($_SESSION['form_data_madre']['madre_nivel_estudios']) && $_SESSION['form_data_madre']['madre_nivel_estudios'] == 'Maestría') ? 'selected' : ''; ?>>Maestría
              </option>
              <option value="Doctorado" <?php echo (isset($_SESSION['form_data_madre']['madre_nivel_estudios']) && $_SESSION['form_data_madre']['madre_nivel_estudios'] == 'Doctorado') ? 'selected' : ''; ?>>Doctorado
              </option>
              <option value="Ninguno" <?php echo (isset($_SESSION['form_data_madre']['madre_nivel_estudios']) && $_SESSION['form_data_madre']['madre_nivel_estudios'] == 'Ninguno') ? 'selected' : ''; ?>>Ninguno</option>
            </select>
          </div>
          <div class="form-group">
            <label for="madre-ocupacion">Ocupación / Profesión: <span style="color: red">*</span></label>
            <input type="text" id="madre-ocupacion" name="madre_ocupacion"
              value="<?php echo isset($_SESSION['form_data_madre']['madre_ocupacion']) ? htmlspecialchars($_SESSION['form_data_madre']['madre_ocupacion']) : ''; ?>"
              required />
          </div>
        </div>

        <!-- Preguntas Sí/No - Primera fila -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="madre-acudiente">Será el acudiente en el colegio?: <span style="color: red">*</span></label>
            <select id="madre-acudiente" name="madre_acudiente" required>
              <option value="">Seleccione</option>
              <option value="si" <?php echo (isset($_SESSION['form_data_madre']['madre_acudiente']) && $_SESSION['form_data_madre']['madre_acudiente'] == 'si') ? 'selected' : ''; ?>>Sí</option>
              <option value="no" <?php echo (isset($_SESSION['form_data_madre']['madre_acudiente']) && $_SESSION['form_data_madre']['madre_acudiente'] == 'no') ? 'selected' : ''; ?>>No</option>
            </select>
          </div>
          <div class="form-group">
            <label for="madre-reuniones">Asiste a Reuniones: <span style="color: red">*</span></label>
            <select id="madre-reuniones" name="madre_reuniones" required>
              <option value="">Seleccione</option>
              <option value="si" <?php echo (isset($_SESSION['form_data_madre']['madre_reuniones']) && $_SESSION['form_data_madre']['madre_reuniones'] == 'si') ? 'selected' : ''; ?>>Sí</option>
              <option value="no" <?php echo (isset($_SESSION['form_data_madre']['madre_reuniones']) && $_SESSION['form_data_madre']['madre_reuniones'] == 'no') ? 'selected' : ''; ?>>No</option>
            </select>
          </div>
        </div>

        <!-- Preguntas Sí/No - Segunda fila -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="madre-cabeza-familia">Madre cabeza de familia: <span style="color: red">*</span></label>
            <select id="madre-cabeza-familia" name="madre_cabeza_familia" required>
              <option value="">Seleccione</option>
              <option value="si" <?php echo (isset($_SESSION['form_data_madre']['madre_cabeza_familia']) && $_SESSION['form_data_madre']['madre_cabeza_familia'] == 'si') ? 'selected' : ''; ?>>Sí</option>
              <option value="no" <?php echo (isset($_SESSION['form_data_madre']['madre_cabeza_familia']) && $_SESSION['form_data_madre']['madre_cabeza_familia'] == 'no') ? 'selected' : ''; ?>>No</option>
            </select>
          </div>
          <div class="form-group">
            <label for="madre-convive">Convive con el estudiante: <span style="color: red">*</span></label>
            <select id="madre-convive" name="madre_convive" required>
              <option value="">Seleccione</option>
              <option value="si" <?php echo (isset($_SESSION['form_data_madre']['madre_convive']) && $_SESSION['form_data_madre']['madre_convive'] == 'si') ? 'selected' : ''; ?>>Sí</option>
              <option value="no" <?php echo (isset($_SESSION['form_data_madre']['madre_convive']) && $_SESSION['form_data_madre']['madre_convive'] == 'no') ? 'selected' : ''; ?>>No</option>
            </select>
          </div>
        </div>

        <button type="submit" class="save-btn">Guardar Información</button>
      </form>
    </section>
    <!-- ======== DATOS DEL PADRE ======== -->
    <section id="datos-padre" class="content" style="display: none">
      <h2>Datos del Padre</h2>

      <form id="form-datos-padre" class="info-general-form" method="POST"
        action="controllers/StudentController.php?action=save_father_data">
        <input type="hidden" name="action" value="save_father_data">

        <!-- Nombres y Apellidos -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="padre-nombres">Nombres: <span style="color: red">*</span></label>
            <input type="text" id="padre-nombres" name="padre_nombres"
              value="<?php echo isset($_SESSION['form_data_padre']['padre_nombres']) ? htmlspecialchars($_SESSION['form_data_padre']['padre_nombres']) : ''; ?>"
              required />
          </div>
          <div class="form-group">
            <label for="padre-apellidos">Apellidos: <span style="color: red">*</span></label>
            <input type="text" id="padre-apellidos" name="padre_apellidos"
              value="<?php echo isset($_SESSION['form_data_padre']['padre_apellidos']) ? htmlspecialchars($_SESSION['form_data_padre']['padre_apellidos']) : ''; ?>"
              required />
          </div>
        </div>

        <!-- Celular y Teléfono Residencia -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="padre-celular">Número Celular: <span style="color: red">*</span></label>
            <input type="tel" id="padre-celular" name="padre_celular"
              value="<?php echo isset($_SESSION['form_data_padre']['padre_celular']) ? htmlspecialchars($_SESSION['form_data_padre']['padre_celular']) : ''; ?>"
              required />
          </div>
          <div class="form-group">
            <label for="padre-telefono">Teléfono Residencia (si tiene):</label>
            <input type="tel" id="padre-telefono" name="padre_telefono"
              value="<?php echo isset($_SESSION['form_data_padre']['padre_telefono']) ? htmlspecialchars($_SESSION['form_data_padre']['padre_telefono']) : ''; ?>" />
          </div>
        </div>

        <!-- Dirección y Ciudad -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="padre-direccion">Dirección: <span style="color: red">*</span></label>
            <input type="text" id="padre-direccion" name="padre_direccion"
              value="<?php echo isset($_SESSION['form_data_padre']['padre_direccion']) ? htmlspecialchars($_SESSION['form_data_padre']['padre_direccion']) : ''; ?>"
              required />
          </div>
          <div class="form-group">
            <label for="padre-ciudad">Ciudad: <span style="color: red">*</span></label>
            <input type="text" id="padre-ciudad" name="padre_ciudad"
              value="<?php echo isset($_SESSION['form_data_padre']['padre_ciudad']) ? htmlspecialchars($_SESSION['form_data_padre']['padre_ciudad']) : ''; ?>"
              required />
          </div>
        </div>

        <!-- Tipo Documento y Número Documento -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="padre-tipo-documento">Tipo de Documento: <span style="color: red">*</span></label>
            <select id="padre-tipo-documento" name="padre_tipo_documento" required>
              <option value="">Seleccione</option>
              <option value="CC" <?php echo (isset($_SESSION['form_data_padre']['padre_tipo_documento']) && $_SESSION['form_data_padre']['padre_tipo_documento'] == 'CC') ? 'selected' : ''; ?>>Cédula de Ciudadanía
              </option>
              <option value="CE" <?php echo (isset($_SESSION['form_data_padre']['padre_tipo_documento']) && $_SESSION['form_data_padre']['padre_tipo_documento'] == 'CE') ? 'selected' : ''; ?>>Cédula de Extranjería
              </option>
              <option value="TI" <?php echo (isset($_SESSION['form_data_padre']['padre_tipo_documento']) && $_SESSION['form_data_padre']['padre_tipo_documento'] == 'TI') ? 'selected' : ''; ?>>Tarjeta de Identidad
              </option>
              <option value="RC" <?php echo (isset($_SESSION['form_data_padre']['padre_tipo_documento']) && $_SESSION['form_data_padre']['padre_tipo_documento'] == 'RC') ? 'selected' : ''; ?>>Registro Civil
              </option>
              <option value="PAS" <?php echo (isset($_SESSION['form_data_padre']['padre_tipo_documento']) && $_SESSION['form_data_padre']['padre_tipo_documento'] == 'PAS') ? 'selected' : ''; ?>>Pasaporte</option>
            </select>
          </div>
          <div class="form-group">
            <label for="padre-numero-documento">Número de Documento: <span style="color: red">*</span></label>
            <input type="text" id="padre-numero-documento" name="padre_numero_documento"
              value="<?php echo isset($_SESSION['form_data_padre']['padre_numero_documento']) ? htmlspecialchars($_SESSION['form_data_padre']['padre_numero_documento']) : ''; ?>"
              required />
          </div>
        </div>

        <!-- Lugar Expedición y Fecha Nacimiento -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="padre-lugar-expedicion">Lugar de Expedición: <span style="color: red">*</span></label>
            <input type="text" id="padre-lugar-expedicion" name="padre_lugar_expedicion"
              value="<?php echo isset($_SESSION['form_data_padre']['padre_lugar_expedicion']) ? htmlspecialchars($_SESSION['form_data_padre']['padre_lugar_expedicion']) : ''; ?>"
              required />
          </div>
          <div class="form-group">
            <label for="padre-fecha-nacimiento">Fecha de Nacimiento: <span style="color: red">*</span></label>
            <input type="date" id="padre-fecha-nacimiento" name="padre_fecha_nacimiento"
              value="<?php echo isset($_SESSION['form_data_padre']['padre_fecha_nacimiento']) ? htmlspecialchars($_SESSION['form_data_padre']['padre_fecha_nacimiento']) : ''; ?>"
              required />
          </div>
        </div>

        <!-- Lugar Nacimiento y Género -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="padre-lugar-nacimiento">Lugar de Nacimiento: <span style="color: red">*</span></label>
            <input type="text" id="padre-lugar-nacimiento" name="padre_lugar_nacimiento"
              value="<?php echo isset($_SESSION['form_data_padre']['padre_lugar_nacimiento']) ? htmlspecialchars($_SESSION['form_data_padre']['padre_lugar_nacimiento']) : ''; ?>"
              required />
          </div>
          <div class="form-group">
            <label for="padre-genero">Género: <span style="color: red">*</span></label>
            <select id="padre-genero" name="padre_genero" required>
              <option value="">Seleccione</option>
              <option value="Masculino" <?php echo (isset($_SESSION['form_data_padre']['padre_genero']) && $_SESSION['form_data_padre']['padre_genero'] == 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
              <option value="Femenino" <?php echo (isset($_SESSION['form_data_padre']['padre_genero']) && $_SESSION['form_data_padre']['padre_genero'] == 'Femenino') ? 'selected' : ''; ?>>Femenino</option>
              <option value="Otro" <?php echo (isset($_SESSION['form_data_padre']['padre_genero']) && $_SESSION['form_data_padre']['padre_genero'] == 'Otro') ? 'selected' : ''; ?>>Otro</option>
              <option value="Prefiero no decir" <?php echo (isset($_SESSION['form_data_padre']['padre_genero']) && $_SESSION['form_data_padre']['padre_genero'] == 'Prefiero no decir') ? 'selected' : ''; ?>>Prefiero no
                decir</option>
            </select>
          </div>
        </div>

        <!-- Correo electrónico -->
        <div class="form-row">
          <label for="padre-correo">Correo electrónico: <span style="color: red">*</span></label>
          <input type="email" id="padre-correo" name="padre_correo"
            value="<?php echo isset($_SESSION['form_data_padre']['padre_correo']) ? htmlspecialchars($_SESSION['form_data_padre']['padre_correo']) : ''; ?>"
            required placeholder="Escribir en minúscula" />
        </div>

        <!-- Nivel Estudios y Ocupación -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="padre-nivel-estudios">Nivel de Estudios: <span style="color: red">*</span></label>
            <select id="padre-nivel-estudios" name="padre_nivel_estudios" required>
              <option value="">Seleccione</option>
              <option value="Primaria" <?php echo (isset($_SESSION['form_data_padre']['padre_nivel_estudios']) && $_SESSION['form_data_padre']['padre_nivel_estudios'] == 'Primaria') ? 'selected' : ''; ?>>Primaria
              </option>
              <option value="Bachillerato" <?php echo (isset($_SESSION['form_data_padre']['padre_nivel_estudios']) && $_SESSION['form_data_padre']['padre_nivel_estudios'] == 'Bachillerato') ? 'selected' : ''; ?>>Bachillerato
              </option>
              <option value="Técnico" <?php echo (isset($_SESSION['form_data_padre']['padre_nivel_estudios']) && $_SESSION['form_data_padre']['padre_nivel_estudios'] == 'Técnico') ? 'selected' : ''; ?>>Técnico</option>
              <option value="Tecnólogo" <?php echo (isset($_SESSION['form_data_padre']['padre_nivel_estudios']) && $_SESSION['form_data_padre']['padre_nivel_estudios'] == 'Tecnólogo') ? 'selected' : ''; ?>>Tecnólogo
              </option>
              <option value="Pregrado" <?php echo (isset($_SESSION['form_data_padre']['padre_nivel_estudios']) && $_SESSION['form_data_padre']['padre_nivel_estudios'] == 'Pregrado') ? 'selected' : ''; ?>>Pregrado
              </option>
              <option value="Especialización" <?php echo (isset($_SESSION['form_data_padre']['padre_nivel_estudios']) && $_SESSION['form_data_padre']['padre_nivel_estudios'] == 'Especialización') ? 'selected' : ''; ?>>
                Especialización</option>
              <option value="Maestría" <?php echo (isset($_SESSION['form_data_padre']['padre_nivel_estudios']) && $_SESSION['form_data_padre']['padre_nivel_estudios'] == 'Maestría') ? 'selected' : ''; ?>>Maestría
              </option>
              <option value="Doctorado" <?php echo (isset($_SESSION['form_data_padre']['padre_nivel_estudios']) && $_SESSION['form_data_padre']['padre_nivel_estudios'] == 'Doctorado') ? 'selected' : ''; ?>>Doctorado
              </option>
              <option value="Ninguno" <?php echo (isset($_SESSION['form_data_padre']['padre_nivel_estudios']) && $_SESSION['form_data_padre']['padre_nivel_estudios'] == 'Ninguno') ? 'selected' : ''; ?>>Ninguno</option>
            </select>
          </div>
          <div class="form-group">
            <label for="padre-ocupacion">Ocupación / Profesión: <span style="color: red">*</span></label>
            <input type="text" id="padre-ocupacion" name="padre_ocupacion"
              value="<?php echo isset($_SESSION['form_data_padre']['padre_ocupacion']) ? htmlspecialchars($_SESSION['form_data_padre']['padre_ocupacion']) : ''; ?>"
              required />
          </div>
        </div>

        <!-- Preguntas Sí/No - Primera fila -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="padre-acudiente">Será el acudiente en el colegio?: <span style="color: red">*</span></label>
            <select id="padre-acudiente" name="padre_acudiente" required>
              <option value="">Seleccione</option>
              <option value="si" <?php echo (isset($_SESSION['form_data_padre']['padre_acudiente']) && $_SESSION['form_data_padre']['padre_acudiente'] == 'si') ? 'selected' : ''; ?>>Sí</option>
              <option value="no" <?php echo (isset($_SESSION['form_data_padre']['padre_acudiente']) && $_SESSION['form_data_padre']['padre_acudiente'] == 'no') ? 'selected' : ''; ?>>No</option>
            </select>
          </div>
          <div class="form-group">
            <label for="padre-reuniones">Asiste a Reuniones: <span style="color: red">*</span></label>
            <select id="padre-reuniones" name="padre_reuniones" required>
              <option value="">Seleccione</option>
              <option value="si" <?php echo (isset($_SESSION['form_data_padre']['padre_reuniones']) && $_SESSION['form_data_padre']['padre_reuniones'] == 'si') ? 'selected' : ''; ?>>Sí</option>
              <option value="no" <?php echo (isset($_SESSION['form_data_padre']['padre_reuniones']) && $_SESSION['form_data_padre']['padre_reuniones'] == 'no') ? 'selected' : ''; ?>>No</option>
            </select>
          </div>
        </div>

        <!-- Preguntas Sí/No - Segunda fila -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="padre-cabeza-familia">Padre cabeza de familia: <span style="color: red">*</span></label>
            <select id="padre-cabeza-familia" name="padre_cabeza_familia" required>
              <option value="">Seleccione</option>
              <option value="si" <?php echo (isset($_SESSION['form_data_padre']['padre_cabeza_familia']) && $_SESSION['form_data_padre']['padre_cabeza_familia'] == 'si') ? 'selected' : ''; ?>>Sí</option>
              <option value="no" <?php echo (isset($_SESSION['form_data_padre']['padre_cabeza_familia']) && $_SESSION['form_data_padre']['padre_cabeza_familia'] == 'no') ? 'selected' : ''; ?>>No</option>
            </select>
          </div>
          <div class="form-group">
            <label for="padre-convive">Convive con el estudiante: <span style="color: red">*</span></label>
            <select id="padre-convive" name="padre_convive" required>
              <option value="">Seleccione</option>
              <option value="si" <?php echo (isset($_SESSION['form_data_padre']['padre_convive']) && $_SESSION['form_data_padre']['padre_convive'] == 'si') ? 'selected' : ''; ?>>Sí</option>
              <option value="no" <?php echo (isset($_SESSION['form_data_padre']['padre_convive']) && $_SESSION['form_data_padre']['padre_convive'] == 'no') ? 'selected' : ''; ?>>No</option>
            </select>
          </div>
        </div>

        <button type="submit" class="save-btn">Guardar Información</button>
      </form>
    </section>

    <!-- ======== ACUDIENTE ======== -->
    <section id="otro-acudiente" class="content" style="display: none">
      <h2>Acudiente (puede ser diferente al padre o la madre)</h2>

      <form id="form-datos-acudiente" class="info-general-form" method="POST"
        action="controllers/StudentController.php?action=save_acudiente_data">
        <input type="hidden" name="action" value="save_acudiente_data">

        <!-- Nombres y Apellidos -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="acudiente-nombres">Nombres: <span style="color: red">*</span></label>
            <input type="text" id="acudiente-nombres" name="acudiente_nombres"
              value="<?php echo isset($_SESSION['form_data_acudiente']['acudiente_nombres']) ? htmlspecialchars($_SESSION['form_data_acudiente']['acudiente_nombres']) : ''; ?>"
              required />
          </div>
          <div class="form-group">
            <label for="acudiente-apellidos">Apellidos: <span style="color: red">*</span></label>
            <input type="text" id="acudiente-apellidos" name="acudiente_apellidos"
              value="<?php echo isset($_SESSION['form_data_acudiente']['acudiente_apellidos']) ? htmlspecialchars($_SESSION['form_data_acudiente']['acudiente_apellidos']) : ''; ?>"
              required />
          </div>
        </div>

        <!-- Celular y Teléfono Residencia -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="acudiente-celular">Número Celular: <span style="color: red">*</span></label>
            <input type="tel" id="acudiente-celular" name="acudiente_celular"
              value="<?php echo isset($_SESSION['form_data_acudiente']['acudiente_celular']) ? htmlspecialchars($_SESSION['form_data_acudiente']['acudiente_celular']) : ''; ?>"
              required />
          </div>
          <div class="form-group">
            <label for="acudiente-telefono">Teléfono Residencia (si tiene):</label>
            <input type="tel" id="acudiente-telefono" name="acudiente_telefono"
              value="<?php echo isset($_SESSION['form_data_acudiente']['acudiente_telefono']) ? htmlspecialchars($_SESSION['form_data_acudiente']['acudiente_telefono']) : ''; ?>" />
          </div>
        </div>

        <!-- Dirección y Ciudad -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="acudiente-direccion">Dirección: <span style="color: red">*</span></label>
            <input type="text" id="acudiente-direccion" name="acudiente_direccion"
              value="<?php echo isset($_SESSION['form_data_acudiente']['acudiente_direccion']) ? htmlspecialchars($_SESSION['form_data_acudiente']['acudiente_direccion']) : ''; ?>"
              required />
          </div>
          <div class="form-group">
            <label for="acudiente-ciudad">Ciudad: <span style="color: red">*</span></label>
            <input type="text" id="acudiente-ciudad" name="acudiente_ciudad"
              value="<?php echo isset($_SESSION['form_data_acudiente']['acudiente_ciudad']) ? htmlspecialchars($_SESSION['form_data_acudiente']['acudiente_ciudad']) : ''; ?>"
              required />
          </div>
        </div>

        <!-- Tipo Documento y Número Documento -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="acudiente-tipo-documento">Tipo de Documento: <span style="color: red">*</span></label>
            <select id="acudiente-tipo-documento" name="acudiente_tipo_documento" required>
              <option value="">Seleccione</option>
              <option value="CC" <?php echo (isset($_SESSION['form_data_acudiente']['acudiente_tipo_documento']) && $_SESSION['form_data_acudiente']['acudiente_tipo_documento'] == 'CC') ? 'selected' : ''; ?>>Cédula de
                Ciudadanía</option>
              <option value="CE" <?php echo (isset($_SESSION['form_data_acudiente']['acudiente_tipo_documento']) && $_SESSION['form_data_acudiente']['acudiente_tipo_documento'] == 'CE') ? 'selected' : ''; ?>>Cédula de
                Extranjería</option>
              <option value="TI" <?php echo (isset($_SESSION['form_data_acudiente']['acudiente_tipo_documento']) && $_SESSION['form_data_acudiente']['acudiente_tipo_documento'] == 'TI') ? 'selected' : ''; ?>>Tarjeta de
                Identidad</option>
              <option value="RC" <?php echo (isset($_SESSION['form_data_acudiente']['acudiente_tipo_documento']) && $_SESSION['form_data_acudiente']['acudiente_tipo_documento'] == 'RC') ? 'selected' : ''; ?>>Registro Civil
              </option>
              <option value="PAS" <?php echo (isset($_SESSION['form_data_acudiente']['acudiente_tipo_documento']) && $_SESSION['form_data_acudiente']['acudiente_tipo_documento'] == 'PAS') ? 'selected' : ''; ?>>Pasaporte
              </option>
            </select>
          </div>
          <div class="form-group">
            <label for="acudiente-numero-documento">Número de Documento: <span style="color: red">*</span></label>
            <input type="text" id="acudiente-numero-documento" name="acudiente_numero_documento"
              value="<?php echo isset($_SESSION['form_data_acudiente']['acudiente_numero_documento']) ? htmlspecialchars($_SESSION['form_data_acudiente']['acudiente_numero_documento']) : ''; ?>"
              required />
          </div>
        </div>

        <!-- Lugar Expedición y Fecha Nacimiento -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="acudiente-lugar-expedicion">Lugar de Expedición: <span style="color: red">*</span></label>
            <input type="text" id="acudiente-lugar-expedicion" name="acudiente_lugar_expedicion"
              value="<?php echo isset($_SESSION['form_data_acudiente']['acudiente_lugar_expedicion']) ? htmlspecialchars($_SESSION['form_data_acudiente']['acudiente_lugar_expedicion']) : ''; ?>"
              required />
          </div>
          <div class="form-group">
            <label for="acudiente-fecha-nacimiento">Fecha de Nacimiento: <span style="color: red">*</span></label>
            <input type="date" id="acudiente-fecha-nacimiento" name="acudiente_fecha_nacimiento"
              value="<?php echo isset($_SESSION['form_data_acudiente']['acudiente_fecha_nacimiento']) ? htmlspecialchars($_SESSION['form_data_acudiente']['acudiente_fecha_nacimiento']) : ''; ?>"
              required />
          </div>
        </div>

        <!-- Lugar Nacimiento y Género -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="acudiente-lugar-nacimiento">Lugar de Nacimiento: <span style="color: red">*</span></label>
            <input type="text" id="acudiente-lugar-nacimiento" name="acudiente_lugar_nacimiento"
              value="<?php echo isset($_SESSION['form_data_acudiente']['acudiente_lugar_nacimiento']) ? htmlspecialchars($_SESSION['form_data_acudiente']['acudiente_lugar_nacimiento']) : ''; ?>"
              required />
          </div>
          <div class="form-group">
            <label for="acudiente-genero">Género: <span style="color: red">*</span></label>
            <select id="acudiente-genero" name="acudiente_genero" required>
              <option value="">Seleccione</option>
              <option value="Masculino" <?php echo (isset($_SESSION['form_data_acudiente']['acudiente_genero']) && $_SESSION['form_data_acudiente']['acudiente_genero'] == 'Masculino') ? 'selected' : ''; ?>>Masculino
              </option>
              <option value="Femenino" <?php echo (isset($_SESSION['form_data_acudiente']['acudiente_genero']) && $_SESSION['form_data_acudiente']['acudiente_genero'] == 'Femenino') ? 'selected' : ''; ?>>Femenino
              </option>
              <option value="Otro" <?php echo (isset($_SESSION['form_data_acudiente']['acudiente_genero']) && $_SESSION['form_data_acudiente']['acudiente_genero'] == 'Otro') ? 'selected' : ''; ?>>Otro</option>
              <option value="Prefiero no decir" <?php echo (isset($_SESSION['form_data_acudiente']['acudiente_genero']) && $_SESSION['form_data_acudiente']['acudiente_genero'] == 'Prefiero no decir') ? 'selected' : ''; ?>>
                Prefiero no decir</option>
            </select>
          </div>
        </div>

        <!-- Correo electrónico -->
        <div class="form-row">
          <label for="acudiente-correo">Correo electrónico: <span style="color: red">*</span></label>
          <input type="email" id="acudiente-correo" name="acudiente_correo"
            value="<?php echo isset($_SESSION['form_data_acudiente']['acudiente_correo']) ? htmlspecialchars($_SESSION['form_data_acudiente']['acudiente_correo']) : ''; ?>"
            required placeholder="Escribir en minúscula" />
        </div>

        <!-- Nivel Estudios y Ocupación -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="acudiente-nivel-estudios">Nivel de Estudios: <span style="color: red">*</span></label>
            <select id="acudiente-nivel-estudios" name="acudiente_nivel_estudios" required>
              <option value="">Seleccione</option>
              <option value="Primaria" <?php echo (isset($_SESSION['form_data_acudiente']['acudiente_nivel_estudios']) && $_SESSION['form_data_acudiente']['acudiente_nivel_estudios'] == 'Primaria') ? 'selected' : ''; ?>>
                Primaria</option>
              <option value="Bachillerato" <?php echo (isset($_SESSION['form_data_acudiente']['acudiente_nivel_estudios']) && $_SESSION['form_data_acudiente']['acudiente_nivel_estudios'] == 'Bachillerato') ? 'selected' : ''; ?>>
                Bachillerato</option>
              <option value="Técnico" <?php echo (isset($_SESSION['form_data_acudiente']['acudiente_nivel_estudios']) && $_SESSION['form_data_acudiente']['acudiente_nivel_estudios'] == 'Técnico') ? 'selected' : ''; ?>>Técnico
              </option>
              <option value="Tecnólogo" <?php echo (isset($_SESSION['form_data_acudiente']['acudiente_nivel_estudios']) && $_SESSION['form_data_acudiente']['acudiente_nivel_estudios'] == 'Tecnólogo') ? 'selected' : ''; ?>>
                Tecnólogo</option>
              <option value="Pregrado" <?php echo (isset($_SESSION['form_data_acudiente']['acudiente_nivel_estudios']) && $_SESSION['form_data_acudiente']['acudiente_nivel_estudios'] == 'Pregrado') ? 'selected' : ''; ?>>
                Pregrado</option>
              <option value="Especialización" <?php echo (isset($_SESSION['form_data_acudiente']['acudiente_nivel_estudios']) && $_SESSION['form_data_acudiente']['acudiente_nivel_estudios'] == 'Especialización') ? 'selected' : ''; ?>>
                Especialización</option>
              <option value="Maestría" <?php echo (isset($_SESSION['form_data_acudiente']['acudiente_nivel_estudios']) && $_SESSION['form_data_acudiente']['acudiente_nivel_estudios'] == 'Maestría') ? 'selected' : ''; ?>>
                Maestría</option>
              <option value="Doctorado" <?php echo (isset($_SESSION['form_data_acudiente']['acudiente_nivel_estudios']) && $_SESSION['form_data_acudiente']['acudiente_nivel_estudios'] == 'Doctorado') ? 'selected' : ''; ?>>
                Doctorado</option>
              <option value="Ninguno" <?php echo (isset($_SESSION['form_data_acudiente']['acudiente_nivel_estudios']) && $_SESSION['form_data_acudiente']['acudiente_nivel_estudios'] == 'Ninguno') ? 'selected' : ''; ?>>Ninguno
              </option>
            </select>
          </div>
          <div class="form-group">
            <label for="acudiente-ocupacion">Ocupación / Profesión: <span style="color: red">*</span></label>
            <input type="text" id="acudiente-ocupacion" name="acudiente_ocupacion"
              value="<?php echo isset($_SESSION['form_data_acudiente']['acudiente_ocupacion']) ? htmlspecialchars($_SESSION['form_data_acudiente']['acudiente_ocupacion']) : ''; ?>"
              required />
          </div>
        </div>

        <!-- Preguntas Sí/No -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="acudiente-acudiente">Será el acudiente en el colegio?: <span style="color: red">*</span></label>
            <select id="acudiente-acudiente" name="acudiente_acudiente" required>
              <option value="">Seleccione</option>
              <option value="si" <?php echo (isset($_SESSION['form_data_acudiente']['acudiente_acudiente']) && $_SESSION['form_data_acudiente']['acudiente_acudiente'] == 'si') ? 'selected' : ''; ?>>Sí</option>
              <option value="no" <?php echo (isset($_SESSION['form_data_acudiente']['acudiente_acudiente']) && $_SESSION['form_data_acudiente']['acudiente_acudiente'] == 'no') ? 'selected' : ''; ?>>No</option>
            </select>
          </div>
          <div class="form-group">
            <label for="acudiente-reuniones">Asiste a Reuniones: <span style="color: red">*</span></label>
            <select id="acudiente-reuniones" name="acudiente_reuniones" required>
              <option value="">Seleccione</option>
              <option value="si" <?php echo (isset($_SESSION['form_data_acudiente']['acudiente_reuniones']) && $_SESSION['form_data_acudiente']['acudiente_reuniones'] == 'si') ? 'selected' : ''; ?>>Sí</option>
              <option value="no" <?php echo (isset($_SESSION['form_data_acudiente']['acudiente_reuniones']) && $_SESSION['form_data_acudiente']['acudiente_reuniones'] == 'no') ? 'selected' : ''; ?>>No</option>
            </select>
          </div>
        </div>

        <!-- Convive con el estudiante -->
        <div class="form-row">
          <label for="acudiente-convive">Convive con el estudiante: <span style="color: red">*</span></label>
          <select id="acudiente-convive" name="acudiente_convive" required>
            <option value="">Seleccione</option>
            <option value="si" <?php echo (isset($_SESSION['form_data_acudiente']['acudiente_convive']) && $_SESSION['form_data_acudiente']['acudiente_convive'] == 'si') ? 'selected' : ''; ?>>Sí</option>
            <option value="no" <?php echo (isset($_SESSION['form_data_acudiente']['acudiente_convive']) && $_SESSION['form_data_acudiente']['acudiente_convive'] == 'no') ? 'selected' : ''; ?>>No</option>
          </select>
        </div>

        <button type="submit" class="save-btn">Guardar Información</button>
      </form>
    </section>
    <!-- ======== DATOS DE LA VIVIENDA ======== -->
    <section id="datos-vivienda" class="content" style="display: none">
      <h2>Datos de la Vivienda</h2>

      <form id="form-datos-vivienda" class="info-general-form" method="POST"
        action="controllers/StudentController.php?action=save_vivienda_data">
        <input type="hidden" name="action" value="save_vivienda_data">

        <!-- Número de personas en el hogar -->
        <div class="form-row">
          <label for="numero-personas">Número de personas que viven en su hogar: <span
              style="color: red">*</span></label>
          <input type="number" id="numero-personas" name="numero_personas"
            value="<?php echo isset($_SESSION['form_data_vivienda']['numero_personas']) ? htmlspecialchars($_SESSION['form_data_vivienda']['numero_personas']) : ''; ?>"
            min="1" max="20" required />
        </div>

        <!-- Tipo de vivienda -->
        <div class="form-row">
          <label for="tipo-vivienda">¿La vivienda donde habita la familia es? <span style="color: red">*</span></label>
          <select id="tipo-vivienda" name="tipo_vivienda" required>
            <option value="">Seleccione</option>
            <option value="propia" <?php echo (isset($_SESSION['form_data_vivienda']['tipo_vivienda']) && $_SESSION['form_data_vivienda']['tipo_vivienda'] == 'propia') ? 'selected' : ''; ?>>Propia</option>
            <option value="familiar" <?php echo (isset($_SESSION['form_data_vivienda']['tipo_vivienda']) && $_SESSION['form_data_vivienda']['tipo_vivienda'] == 'familiar') ? 'selected' : ''; ?>>Familiar</option>
            <option value="arriendo" <?php echo (isset($_SESSION['form_data_vivienda']['tipo_vivienda']) && $_SESSION['form_data_vivienda']['tipo_vivienda'] == 'arriendo') ? 'selected' : ''; ?>>En arriendo</option>
            <option value="comodato" <?php echo (isset($_SESSION['form_data_vivienda']['tipo_vivienda']) && $_SESSION['form_data_vivienda']['tipo_vivienda'] == 'comodato') ? 'selected' : ''; ?>>En comodato</option>
          </select>
        </div>

        <!-- Servicios básicos - Primera fila -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="servicio-energia">¿Su vivienda cuenta con servicios básicos de Energía? <span
                style="color: red">*</span></label>
            <select id="servicio-energia" name="servicio_energia" required>
              <option value="">Seleccione</option>
              <option value="si" <?php echo (isset($_SESSION['form_data_vivienda']['servicio_energia']) && $_SESSION['form_data_vivienda']['servicio_energia'] == 'si') ? 'selected' : ''; ?>>Sí</option>
              <option value="no" <?php echo (isset($_SESSION['form_data_vivienda']['servicio_energia']) && $_SESSION['form_data_vivienda']['servicio_energia'] == 'no') ? 'selected' : ''; ?>>No</option>
            </select>
          </div>
          <div class="form-group">
            <label for="servicio-agua">¿Su vivienda cuenta con servicios básicos de Agua? <span
                style="color: red">*</span></label>
            <select id="servicio-agua" name="servicio_agua" required>
              <option value="">Seleccione</option>
              <option value="si" <?php echo (isset($_SESSION['form_data_vivienda']['servicio_agua']) && $_SESSION['form_data_vivienda']['servicio_agua'] == 'si') ? 'selected' : ''; ?>>Sí</option>
              <option value="no" <?php echo (isset($_SESSION['form_data_vivienda']['servicio_agua']) && $_SESSION['form_data_vivienda']['servicio_agua'] == 'no') ? 'selected' : ''; ?>>No</option>
            </select>
          </div>
        </div>

        <!-- Servicios básicos - Segunda fila -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="servicio-alcantarillado">¿Su vivienda cuenta con servicios básicos de Alcantarillado? <span
                style="color: red">*</span></label>
            <select id="servicio-alcantarillado" name="servicio_alcantarillado" required>
              <option value="">Seleccione</option>
              <option value="si" <?php echo (isset($_SESSION['form_data_vivienda']['servicio_alcantarillado']) && $_SESSION['form_data_vivienda']['servicio_alcantarillado'] == 'si') ? 'selected' : ''; ?>>Sí</option>
              <option value="no" <?php echo (isset($_SESSION['form_data_vivienda']['servicio_alcantarillado']) && $_SESSION['form_data_vivienda']['servicio_alcantarillado'] == 'no') ? 'selected' : ''; ?>>No</option>
            </select>
          </div>
          <div class="form-group">
            <label for="servicio-gas">¿Su vivienda cuenta con servicios básicos de Gas Domiciliario? <span
                style="color: red">*</span></label>
            <select id="servicio-gas" name="servicio_gas" required>
              <option value="">Seleccione</option>
              <option value="si" <?php echo (isset($_SESSION['form_data_vivienda']['servicio_gas']) && $_SESSION['form_data_vivienda']['servicio_gas'] == 'si') ? 'selected' : ''; ?>>Sí</option>
              <option value="no" <?php echo (isset($_SESSION['form_data_vivienda']['servicio_gas']) && $_SESSION['form_data_vivienda']['servicio_gas'] == 'no') ? 'selected' : ''; ?>>No</option>
            </select>
          </div>
        </div>

        <!-- Servicios básicos - Tercera fila -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="servicio-telefono">¿Su vivienda cuenta con servicios básicos de Teléfono? <span
                style="color: red">*</span></label>
            <select id="servicio-telefono" name="servicio_telefono" required>
              <option value="">Seleccione</option>
              <option value="si" <?php echo (isset($_SESSION['form_data_vivienda']['servicio_telefono']) && $_SESSION['form_data_vivienda']['servicio_telefono'] == 'si') ? 'selected' : ''; ?>>Sí</option>
              <option value="no" <?php echo (isset($_SESSION['form_data_vivienda']['servicio_telefono']) && $_SESSION['form_data_vivienda']['servicio_telefono'] == 'no') ? 'selected' : ''; ?>>No</option>
            </select>
          </div>
          <div class="form-group">
            <label for="servicio-internet">¿Su vivienda cuenta con servicios básicos de Internet? <span
                style="color: red">*</span></label>
            <select id="servicio-internet" name="servicio_internet" required>
              <option value="">Seleccione</option>
              <option value="si" <?php echo (isset($_SESSION['form_data_vivienda']['servicio_internet']) && $_SESSION['form_data_vivienda']['servicio_internet'] == 'si') ? 'selected' : ''; ?>>Sí</option>
              <option value="no" <?php echo (isset($_SESSION['form_data_vivienda']['servicio_internet']) && $_SESSION['form_data_vivienda']['servicio_internet'] == 'no') ? 'selected' : ''; ?>>No</option>
            </select>
          </div>
        </div>

        <button type="submit" class="save-btn">Guardar Información</button>
      </form>
    </section>

    <!-- ======== INFORMACIÓN DE SALUD ======== -->
    <section id="informacion-salud" class="content" style="display: none">
      <h2>Información de Salud del Estudiante</h2>

      <form id="form-informacion-salud" class="info-general-form" method="POST"
        action="controllers/StudentController.php?action=save_salud_data">
        <input type="hidden" name="action" value="save_salud_data">

        <!-- Tratamiento médico actual -->
        <div class="form-row">
          <label for="tratamiento-medico">¿Se encuentra actualmente en tratamiento médico? <span
              style="color: red">*</span></label>
          <select id="tratamiento-medico" name="tratamiento_medico" required>
            <option value="">Seleccione</option>
            <option value="si" <?php echo (isset($_SESSION['form_data_salud']['tratamiento_medico']) && $_SESSION['form_data_salud']['tratamiento_medico'] == 'si') ? 'selected' : ''; ?>>Sí</option>
            <option value="no" <?php echo (isset($_SESSION['form_data_salud']['tratamiento_medico']) && $_SESSION['form_data_salud']['tratamiento_medico'] == 'no') ? 'selected' : ''; ?>>No</option>
          </select>
        </div>

        <!-- Alergias a medicamentos -->
        <div class="form-row">
          <label for="alergia-medicamentos">¿Es alérgico(a) a algún medicamento? <span
              style="color: red">*</span></label>
          <select id="alergia-medicamentos" name="alergia_medicamentos" required>
            <option value="">Seleccione</option>
            <option value="si" <?php echo (isset($_SESSION['form_data_salud']['alergia_medicamentos']) && $_SESSION['form_data_salud']['alergia_medicamentos'] == 'si') ? 'selected' : ''; ?>>Sí</option>
            <option value="no" <?php echo (isset($_SESSION['form_data_salud']['alergia_medicamentos']) && $_SESSION['form_data_salud']['alergia_medicamentos'] == 'no') ? 'selected' : ''; ?>>No</option>
          </select>
        </div>

        <!-- Especificar alergias (se muestra solo si respuesta es Sí) -->
        <div id="alergia-especificar-div"
          style="display: <?php echo (isset($_SESSION['form_data_salud']['alergia_medicamentos']) && $_SESSION['form_data_salud']['alergia_medicamentos'] == 'si') ? 'block' : 'none'; ?>">
          <div class="form-row">
            <label for="alergia-especificar">Si su respuesta es "Sí", por favor especifique:</label>
            <textarea id="alergia-especificar" name="alergia_especificar" rows="3"
              placeholder="Especifique a qué medicamentos es alérgico..."><?php echo isset($_SESSION['form_data_salud']['alergia_especificar']) ? htmlspecialchars($_SESSION['form_data_salud']['alergia_especificar']) : ''; ?></textarea>
          </div>
        </div>

        <!-- Enfermedades diagnosticadas -->
        <div class="form-row">
          <label for="enfermedad-diagnosticada">¿Padece alguna enfermedad diagnosticada? <span
              style="color: red">*</span></label>
          <select id="enfermedad-diagnosticada" name="enfermedad_diagnosticada" required>
            <option value="">Seleccione</option>
            <option value="si" <?php echo (isset($_SESSION['form_data_salud']['enfermedad_diagnosticada']) && $_SESSION['form_data_salud']['enfermedad_diagnosticada'] == 'si') ? 'selected' : ''; ?>>Sí</option>
            <option value="no" <?php echo (isset($_SESSION['form_data_salud']['enfermedad_diagnosticada']) && $_SESSION['form_data_salud']['enfermedad_diagnosticada'] == 'no') ? 'selected' : ''; ?>>No</option>
          </select>
        </div>

        <!-- Especificar enfermedades (se muestra solo si respuesta es Sí) -->
        <div id="enfermedad-especificar-div"
          style="display: <?php echo (isset($_SESSION['form_data_salud']['enfermedad_diagnosticada']) && $_SESSION['form_data_salud']['enfermedad_diagnosticada'] == 'si') ? 'block' : 'none'; ?>">
          <div class="form-row">
            <label for="enfermedad-especificar">Si su respuesta es "Sí", indique cuál o cuáles:</label>
            <textarea id="enfermedad-especificar" name="enfermedad_especificar" rows="3"
              placeholder="Especifique las enfermedades diagnosticadas..."><?php echo isset($_SESSION['form_data_salud']['enfermedad_especificar']) ? htmlspecialchars($_SESSION['form_data_salud']['enfermedad_especificar']) : ''; ?></textarea>
          </div>
        </div>

        <!-- Peso y Estatura -->
        <div class="form-row-inline">
          <div class="form-group">
            <label for="peso">Peso: <span style="color: red">*</span></label>
            <input type="number" id="peso" name="peso"
              value="<?php echo isset($_SESSION['form_data_salud']['peso']) ? htmlspecialchars($_SESSION['form_data_salud']['peso']) : ''; ?>"
              min="10" max="200" step="0.1" required placeholder="kg" />
          </div>
          <div class="form-group">
            <label for="estatura">Estatura: <span style="color: red">*</span></label>
            <input type="number" id="estatura" name="estatura"
              value="<?php echo isset($_SESSION['form_data_salud']['estatura']) ? htmlspecialchars($_SESSION['form_data_salud']['estatura']) : ''; ?>"
              min="50" max="250" step="0.1" required placeholder="cm" />
          </div>
        </div>

        <!-- Observaciones adicionales -->
        <div class="form-row">
          <label for="observaciones-fisicas">Observaciones adicionales sobre el estado físico:</label>
          <textarea id="observaciones-fisicas" name="observaciones_fisicas" rows="3"
            placeholder="Observaciones sobre condición física..."><?php echo isset($_SESSION['form_data_salud']['observaciones_fisicas']) ? htmlspecialchars($_SESSION['form_data_salud']['observaciones_fisicas']) : ''; ?></textarea>
        </div>

        <!-- Medicamentos de uso permanente -->
        <div class="form-row">
          <label for="medicamentos-permanentes">Medicamentos de uso permanente:</label>
          <textarea id="medicamentos-permanentes" name="medicamentos_permanentes" rows="3"
            placeholder="Lista de medicamentos de uso regular..."><?php echo isset($_SESSION['form_data_salud']['medicamentos_permanentes']) ? htmlspecialchars($_SESSION['form_data_salud']['medicamentos_permanentes']) : ''; ?></textarea>
        </div>

        <!-- Información adicional de salud -->
        <div class="form-row">
          <label for="informacion-salud-adicional">Enfermedades, alergias u otros aspectos de salud que deban tenerse en
            cuenta:</label>
          <textarea id="informacion-salud-adicional" name="informacion_salud_adicional" rows="4"
            placeholder="Información adicional importante sobre la salud del estudiante..."><?php echo isset($_SESSION['form_data_salud']['informacion_salud_adicional']) ? htmlspecialchars($_SESSION['form_data_salud']['informacion_salud_adicional']) : ''; ?></textarea>
        </div>

        <button type="submit" class="save-btn">Guardar Información</button>
      </form>
    </section>
    <!-- ======== LINK Y DOCUMENTOS ======== -->
    <section id="link-documentos" class="content" style="display: none">
      <h2>Link y Documentos a Diligenciar</h2>

      <div class="info-general-form">
        <!-- Información principal -->
        <div class="form-row">
          <p><strong>Por favor:</strong></p>
        </div>

        <!-- Paso 1: Formulario en línea -->
        <div class="form-row">
          <div class="paso-item">
            <h3>1. Diligencie el formulario en línea</h3>
            <p>
              Ingrese al siguiente enlace usando el número de identificación
              del estudiante:
            </p>
            <div class="link-container">
              <a href="https://formularios.educacionbogota.edu.co/index.php/121449?lang=es" target="_blank"
                class="document-link">
                🔗
                https://formularios.educacionbogota.edu.co/index.php/121449?lang=es
              </a>
            </div>
            <div class="aviso-informativo"
              style="margin-top: 10px; background: #e8f4fd; padding: 10px; border-radius: 5px;">
              <strong>💡 Nota:</strong> Si ya diligenció el formulario, puede omitir este paso.
            </div>
          </div>
        </div>

        <!-- Paso 2: Descargar documentos -->
        <div class="form-row">
          <div class="paso-item">
            <h3>2. Descargue los siguientes documentos:</h3>

            <div class="documentos-lista">
              <div class="documento-item">
                <span class="documento-icon">📄</span>
                <span class="documento-nombre">FORMULARIO SIMPADE 2026</span>
                <button type="button" class="descargar-btn" onclick="descargarDocumento('formulario_simpade_2026.pdf')">
                  Descargar
                </button>
              </div>

              <div class="documento-item">
                <span class="documento-icon">📄</span>
                <span class="documento-nombre">FORMATO DE USO DE IMAGEN 2026 MCM</span>
                <button type="button" class="descargar-btn"
                  onclick="descargarDocumento('formato_uso_imagen_2026_mcm.pdf')">
                  Descargar
                </button>
              </div>

              <div class="documento-item">
                <span class="documento-icon">📄</span>
                <span class="documento-nombre">ACTA DE ACUERDOS Y COMPROMISOS</span>
                <button type="button" class="descargar-btn"
                  onclick="descargarDocumento('acta_acuerdos_compromisos.pdf')">
                  Descargar
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Paso 3: Generar hoja de matrícula -->
        <div class="form-row">
          <div class="paso-item">
            <h3>3. Genere y descargue su hoja de matrícula</h3>
            <div class="documentos-lista">
              <div class="documento-item">
                <span class="documento-icon">📋</span>
                <span class="documento-nombre">HOJA DE MATRÍCULA AUTOMÁTICA</span>
                <button type="button" class="descargar-btn" onclick="generarHojaMatricula(event)">Generar y
                  Descargar</button>
              </div>
            </div>
            <div class="aviso-informativo"
              style="margin-top: 10px; background: #f0f8f0; padding: 10px; border-radius: 5px;">
              <strong>💡 Nota:</strong> Esta hoja se genera automáticamente con los datos que ya ingresó en el sistema.
            </div>
          </div>
        </div>

        <!-- Paso 4: Diligenciar y firmar -->
        <div class="form-row">
          <div class="paso-item">
            <h3>4. Diligencie y firme cada uno de los formularios</h3>
            <p>
              Una vez descargados, complete toda la información requerida y
              firme los documentos, incluyendo la hoja de matrícula generada.
            </p>
          </div>
        </div>

        <!-- Paso 5: Adjuntar documentos -->
        <div class="form-row">
          <div class="paso-item">
            <h3>5. Adjunte los documentos firmados</h3>
            <p>
              En la siguiente pestaña de la plataforma, cargue los documentos
              firmados en formato PDF, incluyendo la hoja de matrícula firmada.
            </p>
            <div class="aviso-importante">
              <strong>⚠️ Importante:</strong> Todos los documentos deben estar
              en formato PDF y debidamente firmados.
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ======== ADJUNTAR DOCUMENTOS ======== -->
    <!-- ======== ADJUNTAR DOCUMENTOS ======== -->
<section id="adjuntar-documentos" class="content" style="display: none">
    <h2>Adjuntar Documentos</h2>

    <div class="info-general-form">
        <?php
        // Cargar controlador de documentos
        require_once __DIR__ . '/controllers/DocumentController.php';
        $documentController = new DocumentController();
        $documentosSubidos = $documentController->listarDocumentos();
        $necesitaDiscapacidad = $documentController->necesitaCertificadoDiscapacidad();
        
        // Mostrar mensajes
        if (isset($_SESSION['success_message'])): ?>
            <div class="success-message">
                <?php echo $_SESSION['success_message']; ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="error-message">
                <?php echo $_SESSION['error_message']; ?>
                <?php unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['info_message'])): ?>
            <div class="info-message">
                <?php echo $_SESSION['info_message']; ?>
                <?php unset($_SESSION['info_message']); ?>
            </div>
        <?php endif; ?>
        
        <!-- Mostrar documentos ya subidos -->
        <?php if (!empty($documentosSubidos)): ?>
        <div class="documentos-subidos">
            <h3>📁 Documentos Ya Subidos</h3>
            <table class="documentos-table">
                <thead>
                    <tr>
                        <th>Documento</th>
                        <th>Archivo</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documentosSubidos as $doc): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($doc['nombre_humano'] ?? $doc['tipo_documento']); ?></td>
                        <td><?php echo htmlspecialchars($doc['nombre_archivo']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($doc['fecha_subida'])); ?></td>
                        <td>
                            <span class="estado-documento estado-<?php echo $doc['estado']; ?>">
                                <?php 
                                $estados = ['pendiente' => '⏳ Pendiente', 'aprobado' => '✅ Aprobado', 'rechazado' => '❌ Rechazado'];
                                echo $estados[$doc['estado']] ?? $doc['estado'];
                                ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <hr>
        <?php endif; ?>

        <!-- Información importante -->
        <div class="form-row">
            <div class="aviso-importante">
                <strong>📋 INFORMACIÓN IMPORTANTE:</strong>
                Los campos marcados con <span style="color: orange">●</span> son
                <strong>OBLIGATORIOS</strong>. Los campos marcados con
                <span style="color: #0078d7">●</span> son
                <strong>OPCIONALES</strong>.
            </div>
        </div>

        <form id="form-adjuntar-documentos" method="POST" action="controllers/DocumentController.php" enctype="multipart/form-data">
            <input type="hidden" name="action" value="save_documentos">

            <!-- Documentos obligatorios -->
            <div class="documentos-seccion">
                <h3>📄 Documentos Obligatorios</h3>

                <!-- 1. Registro civil -->
                <div class="documento-adjunto obligatorio">
                    <label for="registro-civil">1. Fotocopia del registro civil del estudiante</label>
                    <input type="file" id="registro-civil" name="registro_civil" accept=".pdf" />
                    <span class="requerido">● Obligatorio</span>
                </div>

                <!-- 2. Tarjeta de identidad -->
                <div class="documento-adjunto opcional">
                    <label for="tarjeta-identidad">2. Fotocopia de la tarjeta de identidad (para estudiantes mayores de 7 años)</label>
                    <input type="file" id="tarjeta-identidad" name="tarjeta_identidad" accept=".pdf" />
                    <span class="opcional-text">● Opcional</span>
                </div>

                <!-- 3. Estudiantes extranjeros -->
                <div class="documento-adjunto opcional">
                    <label for="documento-extranjero">3. Para estudiantes extranjeros: fotocopia de documento de identificación
                        <small>(puede ser: Permiso de Protección Temporal - PPT, Visa, Cédula de Extranjería)</small>
                    </label>
                    <input type="file" id="documento-extranjero" name="documento_extranjero" accept=".pdf" />
                    <span class="opcional-text">● Opcional</span>
                </div>

                <!-- 4. Documento madre -->
                <div class="documento-adjunto opcional">
                    <label for="documento-madre">4. Documento de identificación de la madre (opcional)</label>
                    <input type="file" id="documento-madre" name="documento_madre" accept=".pdf" />
                    <span class="opcional-text">● Opcional</span>
                </div>

                <!-- 5. Documento padre -->
                <div class="documento-adjunto opcional">
                    <label for="documento-padre">5. Documento de identificación del padre (opcional)</label>
                    <input type="file" id="documento-padre" name="documento_padre" accept=".pdf" />
                    <span class="opcional-text">● Opcional</span>
                </div>

                <!-- 6. Cédula acudiente -->
                <div class="documento-adjunto opcional">
                    <label for="cedula-acudiente">6. Fotocopia de la cédula del acudiente, solo en caso de que sea una persona diferente al padre o a la madre</label>
                    <input type="file" id="cedula-acudiente" name="cedula_acudiente" accept=".pdf" />
                    <span class="opcional-text">● Opcional</span>
                </div>

                <!-- 7. Recibo público -->
                <div class="documento-adjunto obligatorio">
                    <label for="recibo-publico">7. Fotocopia del recibo público</label>
                    <input type="file" id="recibo-publico" name="recibo_publico" accept=".pdf" />
                    <span class="requerido">● Obligatorio</span>
                </div>

                <!-- 8. EPS -->
                <div class="documento-adjunto obligatorio">
                    <label for="certificado-eps">8. Fotocopia del certificado de afiliación a la EPS
                        <small>(puede descargarlo en:
                            <a href="https://www.adres.gov.co/consulte-su-eps" target="_blank">https://www.adres.gov.co/consulte-su-eps</a>)
                        </small>
                    </label>
                    <input type="file" id="certificado-eps" name="certificado_eps" accept=".pdf" />
                    <span class="requerido">● Obligatorio</span>
                </div>

                <!-- 9. Foto -->
                <div class="documento-adjunto obligatorio">
                    <label for="foto-estudiante">9. Una foto reciente, tamaño 3x4</label>
                    <input type="file" id="foto-estudiante" name="foto_estudiante" accept=".jpg,.jpeg,.png" />
                    <span class="requerido">● Obligatorio</span>
                </div>

                <!-- 10. Certificados escolaridad -->
                <div class="documento-adjunto obligatorio">
                    <label for="certificados-escolaridad">10. Certificados de escolaridad de los grados anteriores al solicitado
                        <small>Los certificados deben ser adjuntados en un solo archivo PDF</small>
                    </label>
                    <input type="file" id="certificados-escolaridad" name="certificados_escolaridad" accept=".pdf" />
                    <span class="requerido">● Obligatorio</span>
                </div>
            </div>

            <!-- Documentos del sistema -->
            <div class="documentos-seccion">
                <h3>📋 Documentos del Sistema</h3>

                <!-- 11. SIMPADE -->
                <div class="documento-adjunto obligatorio">
                    <label for="formulario-simpade">11. FORMULARIO SIMPADE 2026</label>
                    <input type="file" id="formulario-simpade" name="formulario_simpade" accept=".pdf" />
                    <span class="requerido">● Obligatorio</span>
                </div>

                <!-- 12. Uso de imagen -->
                <div class="documento-adjunto obligatorio">
                    <label for="formato-imagen">12. FORMATO DE USO DE IMAGEN 2026</label>
                    <input type="file" id="formato-imagen" name="formato_imagen" accept=".pdf" />
                    <span class="requerido">● Obligatorio</span>
                </div>

                <!-- 13. Acuerdos -->
                <div class="documento-adjunto obligatorio">
                    <label for="acta-acuerdos">13. ACTA DE ACUERDOS Y COMPROMISOS</label>
                    <input type="file" id="acta-acuerdos" name="acta_acuerdos" accept=".pdf" />
                    <span class="requerido">● Obligatorio</span>
                </div>
            </div>

            <!-- Hoja de matrícula firmada -->
            <div class="documentos-seccion">
                <h3>📝 Hoja de Matrícula Firmada</h3>

                <!-- 14. Hoja de matrícula firmada -->
                <div class="documento-adjunto obligatorio">
                    <label for="hoja-matricula-firmada">14. HOJA DE MATRÍCULA FIRMADA
                        <small>La hoja generada automáticamente por el sistema, impresa y firmada</small>
                    </label>
                    <input type="file" id="hoja-matricula-firmada" name="hoja_matricula_firmada" accept=".pdf" />
                    <span class="requerido">● Obligatorio</span>
                </div>
            </div>

            <!-- Certificado de discapacidad (condicional) -->
            <?php if ($necesitaDiscapacidad): ?>
            <div class="documentos-seccion">
                <h3>♿ Certificado de Discapacidad</h3>
                <div class="documento-adjunto obligatorio">
                    <label for="certificado-discapacidad">15. CERTIFICADO DE DISCAPACIDAD
                        <small>Documento que certifique la condición de discapacidad</small>
                    </label>
                    <input type="file" id="certificado-discapacidad" name="certificado_discapacidad" accept=".pdf" />
                    <span class="requerido">● Obligatorio</span>
                </div>
            </div>
            <?php endif; ?>

            <!-- Información adicional -->
            <div class="form-row">
                <div class="aviso-informativo">
                    <strong>💡 Nota:</strong> Los documentos 11, 12 y 13 fueron descargados previamente de esta plataforma.
                    <br><strong>📏 Límites:</strong> PDF máximo 5MB | Imágenes máximo 3MB
                </div>
            </div>

            <button type="submit" class="save-btn">📤 Subir Documentos</button>
        </form>
    </div>
</section>
  </div>
  <script>
    function descargarDocumento(nombreArchivo) {
      const ruta = 'assets/uploads/documentos/' + nombreArchivo;

      const link = document.createElement('a');
      link.href = ruta;
      link.download = nombreArchivo;
      link.target = '_blank';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }
  </script>



  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Mapeo de hashes a textos de pestañas
      const hashToTabText = {
        '#info-personal': 'Información Personal',
        '#info-general': 'Información General',
        '#datos-madre': 'Datos de la Madre',
        '#datos-padre': 'Datos del Padre',
        '#otro-acudiente': 'Otro Acudiente',
        '#datos-vivienda': 'Datos de la Vivienda',
        '#informacion-salud': 'Información de Salud',
        '#link-documentos': 'Link y Documentos',
        '#adjuntar-documentos': 'Adjuntar Documentos'
      };

      // Si hay un hash en la URL, activar esa pestaña
      if (window.location.hash && hashToTabText[window.location.hash]) {
        const targetTabText = hashToTabText[window.location.hash];

        // Ocultar todos los contenidos
        document.querySelectorAll('.content').forEach(content => {
          content.style.display = 'none';
        });

        // Desactivar todas las pestañas
        document.querySelectorAll('.tab').forEach(tab => {
          tab.classList.remove('active');
        });

        // Mostrar el contenido objetivo (por ID)
        const targetContent = document.querySelector(window.location.hash);
        if (targetContent) {
          targetContent.style.display = 'block';
        }

        // Activar la pestaña correspondiente (buscar por el texto)
        document.querySelectorAll('.tab').forEach(tab => {
          if (tab.textContent.trim() === targetTabText) {
            tab.classList.add('active');
          }
        });
      }
    });
  </script>
  <script>
function generarHojaMatricula(event) {
    console.log('🔄 Generando hoja de matrícula...');
    
    // Mostrar loading en el botón
    const boton = event.target;
    const textoOriginal = boton.innerHTML;
    boton.innerHTML = '⏳ Generando PDF...';
    boton.disabled = true;
    
    // Abrir en nueva pestaña
    const ventana = window.open('controllers/StudentController.php?action=generar_hoja_matricula', '_blank');
    
    // Verificar si se abrió la ventana
    if (!ventana || ventana.closed || typeof ventana.closed == 'undefined') {
        // Si el navegador bloqueó el popup, mostrar alerta
        alert('⚠️ Tu navegador bloqueó la ventana emergente.\nPor favor permite popups para este sitio.');
        boton.innerHTML = textoOriginal;
        boton.disabled = false;
        return;
    }
    
    // Restaurar botón después de 3 segundos
    setTimeout(() => {
        boton.innerHTML = textoOriginal;
        boton.disabled = false;
        
        // Opcional: Verificar si se generó el PDF
        try {
            if (!ventana.closed) {
                console.log('✅ PDF generado correctamente');
                // Puedes cerrar la ventana automáticamente después de un tiempo
                setTimeout(() => {
                    if (!ventana.closed) {
                        ventana.close();
                    }
                }, 5000);
            }
        } catch (e) {
            // Ignorar errores de cross-origin
            console.log('ℹ️ No se pudo verificar el estado de la ventana');
        }
    }, 3000);
}
</script>
  <script src="assets/js/main.js"></script>
</body>

</html>