<?php
// ACTIVAR ERRORES PARA DEBUG
error_reporting(E_ALL);
ini_set('display_errors', 1);

@session_start();
require_once __DIR__ . '/../models/StudentModel.php';
require_once __DIR__ . '/../config/database.php';

class StudentController
{
    private $studentModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->getConnection();
        $this->studentModel = new StudentModel($db);
    }

    // ======== MÉTODOS EXISTENTES ========

    public function savePersonalInfo()
    {
        // Verificar que el usuario está logueado
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../index.php');
            exit;
        }

        // Verificar que se envió el formulario
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error_message'] = "Método no permitido";
            header('Location: ../home.php#info-personal');
            exit;
        }

        // Recoger datos del formulario
        $data = [
            'usuario_id' => $_SESSION['user_id'],
            'nombres' => trim($_POST['nombres']),
            'apellidos' => trim($_POST['apellidos']),
            'grado_matricular' => 'Por asignar',
            'grado_actual' => 'Por completar',
            'sede' => 'ÚNICA',
            'tipo_estudiante' => $_POST['tipoEstudiante'],
            'fecha_nacimiento' => $_POST['fechaNacimiento'],
            'lugar_nacimiento' => trim($_POST['lugarNacimiento']),
            'edad' => intval($_POST['edad']),
            'tipo_sangre' => $_POST['tipoSangre'],
            'numero_documento' => trim($_POST['numeroDocumento']),
            'lugar_expedicion' => trim($_POST['lugarExpedicion']),
            'tipo_documento' => $_POST['tipoDocumento'],
            'genero' => $_POST['genero'],
            'celular' => trim($_POST['celular']),
            'telefono_residencia' => trim($_POST['telefonoResidencia']),
            'correo_institucional' => '',
            'fecha_matricula' => $_POST['fechaMatricula']
        ];

        // Guardar datos en sesión por si hay error
        $_SESSION['form_data'] = $_POST;

        // Validaciones básicas
        if (empty($data['nombres']) || empty($data['apellidos']) || empty($data['numero_documento'])) {
            $_SESSION['error_message'] = "Por favor complete todos los campos obligatorios";
            header('Location: ../home.php#info-personal');
            exit;
        }

        // Intentar guardar en la base de datos
        if ($this->studentModel->savePersonalInfo($data)) {
            $_SESSION['success_message'] = "✅ Información personal guardada correctamente";
            unset($_SESSION['form_data']);
        } else {
            $_SESSION['error_message'] = "❌ Error al guardar la información. Por favor intente nuevamente.";
        }

        header('Location: ../home.php#info-general');
        exit;
    }

    // ✅ FUNCIÓN PARA INFORMACIÓN GENERAL (ACTUALIZADA - SIN ACUDIENTE)
    public function saveGeneralInfo()
    {
        error_log("🔍 DEBUG: Iniciando saveGeneralInfo - CON HISTORIAL INTEGRADO");

        // Verificar que el usuario esté logueado
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../index.php');
            exit;
        }

        // Verificar que se envió el formulario
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error_message'] = "Método no permitido";
            header('Location: ../home.php#info-general');
            exit;
        }

        // Recoger los datos del formulario (INCLUYENDO HISTORIAL)
        $data = [
            'usuario_id' => $_SESSION['user_id'],
            'direccion' => trim($_POST['direccion']),
            'barrio' => trim($_POST['barrio']),
            'madre_cabeza_familia' => $_POST['madre_cabeza'],
            'estrato' => intval($_POST['estrato']),
            'municipio' => trim($_POST['municipio']),
            'sisben' => $_POST['sisben'],
            'eps' => trim($_POST['eps']),
            'etnia' => $_POST['etnia'] ?? '',
            'desplazado' => $_POST['desplazado'] ?? '',
            'discapacidad_diagnostico' => $_POST['discapacidad_diagnostico'] ?? '',
            'tipo_discapacidad' => $_POST['discapacidad'] ?? '',
            'certificado_discapacidad' => $_POST['certificado'] ?? '',
            'numero_hermanos' => $_POST['numero_hermanos'] ?? '0',
            'lugar_entre_hermanos' => $_POST['lugar_entre_hermanos'] ?? 'no aplica',
            'hermanos_en_colegio' => $_POST['hermanos_en_colegio'] ?? '',
            'lateralidad' => $_POST['lateralidad'] ?? '',

            // 🔥 HISTORIAL ACADÉMICO INTEGRADO
            'historial_2025_ano' => $_POST['historial_ano'][0] ?? '2025',
            'historial_2025_colegio' => $_POST['historial_colegio'][0] ?? '',
            'historial_2025_ciudad' => $_POST['historial_ciudad'][0] ?? '',
            'historial_2025_grado' => $_POST['historial_grado'][0] ?? '',

            'historial_2024_ano' => $_POST['historial_ano'][1] ?? '2024',
            'historial_2024_colegio' => $_POST['historial_colegio'][1] ?? '',
            'historial_2024_ciudad' => $_POST['historial_ciudad'][1] ?? '',
            'historial_2024_grado' => $_POST['historial_grado'][1] ?? '',

            'historial_2023_ano' => $_POST['historial_ano'][2] ?? '2023',
            'historial_2023_colegio' => $_POST['historial_colegio'][2] ?? '',
            'historial_2023_ciudad' => $_POST['historial_ciudad'][2] ?? '',
            'historial_2023_grado' => $_POST['historial_grado'][2] ?? '',

            'historial_2022_ano' => $_POST['historial_ano'][3] ?? '2022',
            'historial_2022_colegio' => $_POST['historial_colegio'][3] ?? '',
            'historial_2022_ciudad' => $_POST['historial_ciudad'][3] ?? '',
            'historial_2022_grado' => $_POST['historial_grado'][3] ?? '',

            'historial_2021_ano' => $_POST['historial_ano'][4] ?? '2021',
            'historial_2021_colegio' => $_POST['historial_colegio'][4] ?? '',
            'historial_2021_ciudad' => $_POST['historial_ciudad'][4] ?? '',
            'historial_2021_grado' => $_POST['historial_grado'][4] ?? '',

            'historial_2020_ano' => $_POST['historial_ano'][5] ?? '2020',
            'historial_2020_colegio' => $_POST['historial_colegio'][5] ?? '',
            'historial_2020_ciudad' => $_POST['historial_ciudad'][5] ?? '',
            'historial_2020_grado' => $_POST['historial_grado'][5] ?? '',

            'historial_2019_ano' => $_POST['historial_ano'][6] ?? '2019',
            'historial_2019_colegio' => $_POST['historial_colegio'][6] ?? '',
            'historial_2019_ciudad' => $_POST['historial_ciudad'][6] ?? '',
            'historial_2019_grado' => $_POST['historial_grado'][6] ?? ''
        ];

        // Guardar datos en sesión por si hay error
        $_SESSION['form_data_general'] = $_POST;

        // Validaciones básicas
        if (empty($data['direccion']) || empty($data['barrio']) || empty($data['municipio'])) {
            $_SESSION['error_message'] = "Por favor complete todos los campos obligatorios";
            header('Location: ../home.php#info-general');
            exit;
        }

        // 🔥 GUARDAR INFORMACIÓN GENERAL + HISTORIAL EN LA MISMA TABLA
        if ($this->studentModel->saveGeneralInfo($data)) {
            $_SESSION['success_message'] = "✅ Información general e historial académico guardados correctamente";
            unset($_SESSION['form_data_general']);

            // Recargar datos frescos
            $generalData = $this->studentModel->getGeneralInfo($_SESSION['user_id']);
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
                    'certificado_discapacidad' => $generalData['certificado'],
                    'numero_hermanos' => $generalData['numero_hermanos'],
                    'lugar_entre_hermanos' => $generalData['lugar_entre_hermanos'],
                    'hermanos_en_colegio' => $generalData['hermanos_en_colegio'],
                    'lateralidad' => $generalData['lateralidad']
                ];

                // 🔥 CARGAR DATOS DEL HISTORIAL EN LA SESIÓN PARA MOSTRARLOS
                $_SESSION['form_data_historial'] = [
                    [
                        'año' => $generalData['historial_2025_ano'],
                        'colegio' => $generalData['historial_2025_colegio'],
                        'ciudad' => $generalData['historial_2025_ciudad'],
                        'grado' => $generalData['historial_2025_grado']
                    ],
                    [
                        'año' => $generalData['historial_2024_ano'],
                        'colegio' => $generalData['historial_2024_colegio'],
                        'ciudad' => $generalData['historial_2024_ciudad'],
                        'grado' => $generalData['historial_2024_grado']
                    ],
                    [
                        'año' => $generalData['historial_2023_ano'],
                        'colegio' => $generalData['historial_2023_colegio'],
                        'ciudad' => $generalData['historial_2023_ciudad'],
                        'grado' => $generalData['historial_2023_grado']
                    ],
                    [
                        'año' => $generalData['historial_2022_ano'],
                        'colegio' => $generalData['historial_2022_colegio'],
                        'ciudad' => $generalData['historial_2022_ciudad'],
                        'grado' => $generalData['historial_2022_grado']
                    ],
                    [
                        'año' => $generalData['historial_2021_ano'],
                        'colegio' => $generalData['historial_2021_colegio'],
                        'ciudad' => $generalData['historial_2021_ciudad'],
                        'grado' => $generalData['historial_2021_grado']
                    ],
                    [
                        'año' => $generalData['historial_2020_ano'],
                        'colegio' => $generalData['historial_2020_colegio'],
                        'ciudad' => $generalData['historial_2020_ciudad'],
                        'grado' => $generalData['historial_2020_grado']
                    ],
                    [
                        'año' => $generalData['historial_2019_ano'],
                        'colegio' => $generalData['historial_2019_colegio'],
                        'ciudad' => $generalData['historial_2019_ciudad'],
                        'grado' => $generalData['historial_2019_grado']
                    ]
                ];
            }
        } else {
            $_SESSION['error_message'] = "❌ Error al guardar la información general";
        }

        header('Location: ../home.php#datos-madre');
        exit;
    }


    // ======== NUEVO MÉTODO PARA DATOS DE LA MADRE ========

    public function saveMotherData()
    {

        // Verificar que el usuario esté logueado
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../index.php');
            exit;
        }

        // Verificar que se envió el formulario
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error_madre'] = "Método no permitido";
            header('Location: ../home.php#datos-madre');
            exit;
        }

        // Recoger datos del formulario de la madre
        $data = [
            'usuario_id' => $_SESSION['user_id'],
            'nombres' => trim($_POST['madre_nombres']),
            'apellidos' => trim($_POST['madre_apellidos']),
            'celular' => trim($_POST['madre_celular']),
            'telefono' => trim($_POST['madre_telefono'] ?? ''),
            'direccion' => trim($_POST['madre_direccion']),
            'ciudad' => trim($_POST['madre_ciudad']),
            'tipo_documento' => $_POST['madre_tipo_documento'],
            'numero_documento' => trim($_POST['madre_numero_documento']),
            'lugar_expedicion' => trim($_POST['madre_lugar_expedicion']),
            'fecha_nacimiento' => $_POST['madre_fecha_nacimiento'],
            'lugar_nacimiento' => trim($_POST['madre_lugar_nacimiento']),
            'genero' => $_POST['madre_genero'],
            'correo' => trim($_POST['madre_correo']),
            'nivel_estudios' => $_POST['madre_nivel_estudios'],
            'ocupacion' => trim($_POST['madre_ocupacion']),
            'es_acudiente' => $_POST['madre_acudiente'],
            'asiste_reuniones' => $_POST['madre_reuniones'],
            'cabeza_familia' => $_POST['madre_cabeza_familia'],
            'convive_estudiante' => $_POST['madre_convive']
        ];

        // Guardar datos en sesión por si hay error
        $_SESSION['form_data_madre'] = $_POST;

        // Validaciones básicas
        $camposRequeridos = [
            'nombres',
            'apellidos',
            'celular',
            'direccion',
            'ciudad',
            'tipo_documento',
            'numero_documento',
            'lugar_expedicion',
            'fecha_nacimiento',
            'lugar_nacimiento',
            'genero',
            'correo',
            'nivel_estudios',
            'ocupacion',
            'es_acudiente',
            'asiste_reuniones',
            'cabeza_familia',
            'convive_estudiante'
        ];
        foreach ($camposRequeridos as $campo) {
            if (empty($data[$campo])) {
                $_SESSION['error_madre'] = "Por favor complete todos los campos obligatorios marcados con *";
                header('Location: ../home.php#datos-madre');
                exit;
            }
        }

        // Validar formato de correo
        if (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error_madre'] = "El correo electrónico no tiene un formato válido";
            header('Location: ../home.php#datos-madre');
            exit;
        }

        // Intentar guardar datos de la madre
        if ($this->studentModel->saveMotherData($data)) {
            $_SESSION['success_madre'] = "✅ Datos de la madre guardados correctamente";
            unset($_SESSION['form_data_madre']);

            // 🔥 RECARGAR DATOS FRESCOS DE LA BD
            $motherData = $this->studentModel->getMotherData($_SESSION['user_id']);
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
        } else {
            $_SESSION['error_madre'] = "❌ Error al guardar los datos de la madre. Por favor intente nuevamente.";
        }

        header('Location: ../home.php#datos-padre');
        exit;
    }
    // ======== NUEVO MÉTODO PARA DATOS DEL PADRE ========

    public function saveFatherData()
    {
        // Verificar que el usuario esté logueado
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../index.php');
            exit;
        }

        // Verificar que se envió el formulario
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error_message'] = "Método no permitido";
            header('Location: ../home.php#datos-padre');
            exit;
        }

        // Recoger datos del formulario del padre
        $data = [
            'usuario_id' => $_SESSION['user_id'],
            'nombres' => trim($_POST['padre_nombres']),
            'apellidos' => trim($_POST['padre_apellidos']),
            'celular' => trim($_POST['padre_celular']),
            'telefono' => trim($_POST['padre_telefono'] ?? ''),
            'direccion' => trim($_POST['padre_direccion']),
            'ciudad' => trim($_POST['padre_ciudad']),
            'tipo_documento' => $_POST['padre_tipo_documento'],
            'numero_documento' => trim($_POST['padre_numero_documento']),
            'lugar_expedicion' => trim($_POST['padre_lugar_expedicion']),
            'fecha_nacimiento' => $_POST['padre_fecha_nacimiento'],
            'lugar_nacimiento' => trim($_POST['padre_lugar_nacimiento']),
            'genero' => $_POST['padre_genero'],
            'correo' => trim($_POST['padre_correo']),
            'nivel_estudios' => $_POST['padre_nivel_estudios'],
            'ocupacion' => trim($_POST['padre_ocupacion']),
            'es_acudiente' => $_POST['padre_acudiente'],
            'asiste_reuniones' => $_POST['padre_reuniones'],
            'cabeza_familia' => $_POST['padre_cabeza_familia'],
            'convive_estudiante' => $_POST['padre_convive']
        ];

        // Guardar datos en sesión por si hay error
        $_SESSION['form_data_padre'] = $_POST;

        // Validaciones básicas
        $camposRequeridos = ['nombres', 'apellidos', 'celular', 'direccion', 'ciudad', 'numero_documento', 'lugar_expedicion', 'fecha_nacimiento', 'lugar_nacimiento', 'correo', 'ocupacion'];
        foreach ($camposRequeridos as $campo) {
            if (empty($data[$campo])) {
                $_SESSION['error_message'] = "Por favor complete todos los campos obligatorios marcados con *";
                header('Location: ../home.php#datos-padre');
                exit;
            }
        }

        // Validar formato de correo
        if (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error_message'] = "El correo electrónico no tiene un formato válido";
            header('Location: ../home.php#datos-padre');
            exit;
        }

        // Intentar guardar datos del padre
        if ($this->studentModel->saveFatherData($data)) {
            $_SESSION['success_message'] = "✅ Datos del padre guardados correctamente";
            unset($_SESSION['form_data_padre']);

            // 🔥 RECARGAR DATOS FRESCOS DE LA BD
            $fatherData = $this->studentModel->getFatherData($_SESSION['user_id']);
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
        } else {
            $_SESSION['error_message'] = "❌ Error al guardar los datos del padre. Por favor intente nuevamente.";
        }

        header('Location: ../home.php#otro-acudiente');
        exit;
    }
    // ======== NUEVO MÉTODO PARA DATOS DEL ACUDIENTE ========

    public function saveAcudienteData()
    {
        // Verificar que el usuario esté logueado
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../index.php');
            exit;
        }

        // Verificar que se envió el formulario
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error_message'] = "Método no permitido";
            header('Location: ../home.php#otro-acudiente');
            exit;
        }

        // Recoger datos del formulario del acudiente
        $data = [
            'usuario_id' => $_SESSION['user_id'],
            'nombres' => trim($_POST['acudiente_nombres']),
            'apellidos' => trim($_POST['acudiente_apellidos']),
            'celular' => trim($_POST['acudiente_celular']),
            'telefono' => trim($_POST['acudiente_telefono'] ?? ''),
            'direccion' => trim($_POST['acudiente_direccion']),
            'ciudad' => trim($_POST['acudiente_ciudad']),
            'tipo_documento' => $_POST['acudiente_tipo_documento'],
            'numero_documento' => trim($_POST['acudiente_numero_documento']),
            'lugar_expedicion' => trim($_POST['acudiente_lugar_expedicion']),
            'fecha_nacimiento' => $_POST['acudiente_fecha_nacimiento'],
            'lugar_nacimiento' => trim($_POST['acudiente_lugar_nacimiento']),
            'genero' => $_POST['acudiente_genero'],
            'correo' => trim($_POST['acudiente_correo']),
            'nivel_estudios' => $_POST['acudiente_nivel_estudios'],
            'ocupacion' => trim($_POST['acudiente_ocupacion']),
            'es_acudiente' => $_POST['acudiente_acudiente'],
            'asiste_reuniones' => $_POST['acudiente_reuniones'],
            'convive_estudiante' => $_POST['acudiente_convive']
        ];

        // Guardar datos en sesión por si hay error
        $_SESSION['form_data_acudiente'] = $_POST;

        // Validaciones básicas
        $camposRequeridos = ['nombres', 'apellidos', 'celular', 'direccion', 'ciudad', 'numero_documento', 'lugar_expedicion', 'fecha_nacimiento', 'lugar_nacimiento', 'correo', 'ocupacion'];
        foreach ($camposRequeridos as $campo) {
            if (empty($data[$campo])) {
                $_SESSION['error_message'] = "Por favor complete todos los campos obligatorios marcados con *";
                header('Location: ../home.php#otro-acudiente');
                exit;
            }
        }

        // Validar formato de correo
        if (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error_message'] = "El correo electrónico no tiene un formato válido";
            header('Location: ../home.php#otro-acudiente');
            exit;
        }

        // Intentar guardar datos del acudiente
        if ($this->studentModel->saveAcudienteData($data)) {
            $_SESSION['success_message'] = "✅ Datos del acudiente guardados correctamente";
            unset($_SESSION['form_data_acudiente']);

            // 🔥 RECARGAR DATOS FRESCOS DE LA BD
            $acudienteData = $this->studentModel->getAcudienteData($_SESSION['user_id']);
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
        } else {
            $_SESSION['error_message'] = "❌ Error al guardar los datos del acudiente. Por favor intente nuevamente.";
        }

        header('Location: ../home.php#datos-vivienda');
        exit;
    }
    // ======== NUEVO MÉTODO PARA DATOS DE VIVIENDA ========

    public function saveViviendaData()
    {
        // Verificar que el usuario esté logueado
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../index.php');
            exit;
        }

        // Verificar que se envió el formulario
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error_message'] = "Método no permitido";
            header('Location: ../home.php#datos-vivienda');
            exit;
        }

        // Recoger datos del formulario de vivienda
        $data = [
            'usuario_id' => $_SESSION['user_id'],
            'numero_personas' => intval($_POST['numero_personas']),
            'tipo_vivienda' => $_POST['tipo_vivienda'],
            'servicio_energia' => $_POST['servicio_energia'],
            'servicio_agua' => $_POST['servicio_agua'],
            'servicio_alcantarillado' => $_POST['servicio_alcantarillado'],
            'servicio_gas' => $_POST['servicio_gas'],
            'servicio_telefono' => $_POST['servicio_telefono'],
            'servicio_internet' => $_POST['servicio_internet']
        ];

        // Guardar datos en sesión por si hay error
        $_SESSION['form_data_vivienda'] = $_POST;

        // Validaciones básicas
        if (empty($data['numero_personas']) || $data['numero_personas'] < 1 || $data['numero_personas'] > 20) {
            $_SESSION['error_message'] = "El número de personas debe estar entre 1 y 20";
            header('Location: ../home.php#datos-vivienda');
            exit;
        }

        $camposRequeridos = ['tipo_vivienda', 'servicio_energia', 'servicio_agua', 'servicio_alcantarillado', 'servicio_gas', 'servicio_telefono', 'servicio_internet'];
        foreach ($camposRequeridos as $campo) {
            if (empty($data[$campo])) {
                $_SESSION['error_message'] = "Por favor complete todos los campos obligatorios marcados con *";
                header('Location: ../home.php#datos-vivienda');
                exit;
            }
        }

        // Intentar guardar datos de vivienda
        if ($this->studentModel->saveViviendaData($data)) {
            $_SESSION['success_message'] = "✅ Datos de la vivienda guardados correctamente";
            unset($_SESSION['form_data_vivienda']);

            // RECARGAR DATOS FRESCOS DE LA BD
            $viviendaData = $this->studentModel->getViviendaData($_SESSION['user_id']);
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
        } else {
            $_SESSION['error_message'] = "❌ Error al guardar los datos de la vivienda. Por favor intente nuevamente.";
        }

        header('Location: ../home.php#informacion-salud');
        exit;
    }
    // ======== NUEVO MÉTODO PARA DATOS DE SALUD ========

    public function saveSaludData()
    {
        // Verificar que el usuario esté logueado
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../index.php');
            exit;
        }

        // Verificar que se envió el formulario
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error_message'] = "Método no permitido";
            header('Location: ../home.php#informacion-salud');
            exit;
        }

        // Recoger datos del formulario de salud
        $data = [
            'usuario_id' => $_SESSION['user_id'],
            'tratamiento_medico' => $_POST['tratamiento_medico'],
            'alergia_medicamentos' => $_POST['alergia_medicamentos'],
            'alergia_especificar' => $_POST['alergia_especificar'] ?? '',
            'enfermedad_diagnosticada' => $_POST['enfermedad_diagnosticada'],
            'enfermedad_especificar' => $_POST['enfermedad_especificar'] ?? '',
            'peso' => floatval($_POST['peso']),
            'estatura' => floatval($_POST['estatura']),
            'observaciones_fisicas' => $_POST['observaciones_fisicas'] ?? '',
            'medicamentos_permanentes' => $_POST['medicamentos_permanentes'] ?? '',
            'informacion_salud_adicional' => $_POST['informacion_salud_adicional'] ?? ''
        ];

        // Guardar datos en sesión por si hay error
        $_SESSION['form_data_salud'] = $_POST;

        // Validaciones básicas
        $camposRequeridos = ['tratamiento_medico', 'alergia_medicamentos', 'enfermedad_diagnosticada', 'peso', 'estatura'];
        foreach ($camposRequeridos as $campo) {
            if (empty($data[$campo])) {
                $_SESSION['error_message'] = "Por favor complete todos los campos obligatorios marcados con *";
                header('Location: ../home.php#informacion-salud');
                exit;
            }
        }

        // Validar peso y estatura
        if ($data['peso'] < 10 || $data['peso'] > 200) {
            $_SESSION['error_message'] = "El peso debe estar entre 10 y 200 kg";
            header('Location: ../home.php#informacion-salud');
            exit;
        }

        if ($data['estatura'] < 50 || $data['estatura'] > 250) {
            $_SESSION['error_message'] = "La estatura debe estar entre 50 y 250 cm";
            header('Location: ../home.php#informacion-salud');
            exit;
        }

        // Si hay alergias pero no se especifican
        if ($data['alergia_medicamentos'] == 'si' && empty($data['alergia_especificar'])) {
            $_SESSION['error_message'] = "Por favor especifique a qué medicamentos es alérgico";
            header('Location: ../home.php#informacion-salud');
            exit;
        }

        // Si hay enfermedades pero no se especifican
        if ($data['enfermedad_diagnosticada'] == 'si' && empty($data['enfermedad_especificar'])) {
            $_SESSION['error_message'] = "Por favor especifique las enfermedades diagnosticadas";
            header('Location: ../home.php#informacion-salud');
            exit;
        }

        // Intentar guardar datos de salud
        if ($this->studentModel->saveSaludData($data)) {
            $_SESSION['success_message'] = "✅ Información de salud guardada correctamente";
            unset($_SESSION['form_data_salud']);

            // 🔥 RECARGAR DATOS FRESCOS DE LA BD
            $saludData = $this->studentModel->getSaludData($_SESSION['user_id']);
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
        } else {
            $_SESSION['error_message'] = "❌ Error al guardar la información de salud. Por favor intente nuevamente.";
        }
        header('Location: ../home.php#link-documentos');
        exit;
    }
    // ======== MÉTODO PARA GENERAR HOJA DE MATRÍCULA ========

    public function generarHojaMatricula()
{
    try {
        // ========== DETERMINAR QUIÉN SOLICITA EL PDF ==========
        $target = $_GET['target'] ?? 'user'; // user | admin
        $modo   = $_GET['modo']   ?? 'ver';  // ver | descargar

        if ($target === 'admin') {
            // Acceso solo admin
            if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
                header('Location: /admin/index.php');
                exit;
            }

            // ID del estudiante
            $usuario_id = $_GET['user_id'] ?? $_GET['usuario_id'] ?? null;

            if (!$usuario_id) {
                throw new Exception("No se especificó ID de estudiante");
            }

            // Verificar que el estudiante existe
            $usuario_existe = $this->studentModel->getPersonalInfo($usuario_id);
            if (!$usuario_existe) {
                throw new Exception("El estudiante no existe o no tiene datos");
            }

        } else {
            // Usuario normal
            if (!isset($_SESSION['user_id'])) {
                header('Location: ../index.php');
                exit;
            }

            $usuario_id = $_SESSION['user_id'];
        }

        // ========== OBTENER TODOS LOS DATOS ==========
        $personalData  = $this->studentModel->getPersonalInfo($usuario_id);
        $generalData   = $this->studentModel->getGeneralInfo($usuario_id);
        $motherData    = $this->studentModel->getMotherData($usuario_id);
        $fatherData    = $this->studentModel->getFatherData($usuario_id);
        $acudienteData = $this->studentModel->getAcudienteData($usuario_id);
        $viviendaData  = $this->studentModel->getViviendaData($usuario_id);
        $saludData     = $this->studentModel->getSaludData($usuario_id);

        if (!$personalData) {
            throw new Exception("No se encontraron datos personales del estudiante");
        }

        // ========== GENERAR PDF ==========
        $this->generarPDF(
            $personalData,
            $generalData,
            $motherData,
            $fatherData,
            $acudienteData,
            $viviendaData,
            $saludData,
            $modo // 👈 CLAVE: ver | descargar
        );

    } catch (Exception $e) {

        // ========== MANEJO DE ERRORES ==========
        if (isset($target) && $target === 'admin') {
            $_SESSION['error'] = "❌ Error al generar PDF: " . $e->getMessage();
            header('Location: /admin/estudiantes.php');
        } else {
            $_SESSION['error_message'] = "❌ Error al generar la hoja de matrícula: " . $e->getMessage();
            header('Location: ../home.php#link-documentos');
        }
        exit;
    }
}

    // ======== MÉTODO PRIVADO MEJORADO PARA GENERAR PDF ========


    // Asegúrate de tener tcpdf en ../tcpdf/tcpdf.php y logo en ../assets/img/escudomaria.jpg


    private function safeVal($v)
    {
        return htmlspecialchars((string) ($v ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
private function generarPDF($personal, $general, $madre, $padre, $acudiente, $vivienda, $salud, $modo ='ver')
{
    require_once __DIR__ . '/../tcpdf/tcpdf.php';

    // Instancia TCPDF
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator('Sistema de Matrículas');
    $pdf->SetAuthor('Colegio María Currea Manrique');
    $pdf->SetTitle('Hoja de Matrícula 2026');

    // Márgenes y configuración
    $pdf->SetMargins(10, 10, 10);
    $pdf->SetAutoPageBreak(false);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->AddPage();

    // Logo en la esquina superior izquierda
    $logoPath = __DIR__ . '/../assets/img/escudomaria.jpg';
    if (file_exists($logoPath)) {
        $pdf->Image($logoPath, 10, 10, 22, 24, '', '', '', false, 300);
    }

    // Encabezado del colegio
    $headerHtml = '
<table cellpadding="0" cellspacing="0" border="0" width="100%">
    <tr>
        <td width="12%">&nbsp;</td>
        <td width="63%" align="center" valign="middle" style="font-size:13px; font-weight:bold;">
            COLEGIO MARÍA CURREA MANRIQUE I.E.D<br/>
            <span style="font-size:8px; font-weight:normal;">NIT 899999061-9 &nbsp;&nbsp; Código DANE 111001801276</span><br/>
            <span style="font-size:7px; font-weight:normal; text-decoration:underline;">
                Resolución de Reconocimiento 032 de 10 de noviembre de 2023
            </span>
        </td>
        <td width="25%">&nbsp;</td>
    </tr>
</table>';

    $tituloHtml = '
<table cellpadding="1" cellspacing="0" border="1" width="78%" style="border-collapse:collapse;">
    <tr>
        <td align="center" style="font-size:10px; font-weight:bold; background-color:#e0e0e0; padding:2px;">
            HOJA DE MATRÍCULA 2026
        </td>
    </tr>
</table>';

    $fechaActual = date('d/m/Y'); // O el formato que prefieras
$bloqueGradoFecha = '
<table cellpadding="1" cellspacing="0" border="1" width="78%" style="font-size:8px; border-collapse:collapse;">
    <tr style="background-color:#f5f5f5;">
        <td width="50%" style="font-weight:bold; height:14px; padding:2px;">
            GRADO AL QUE SE MATRICULA:
        </td>
        <td width="50%" style="font-weight:bold; padding:2px;">
            FECHA DILIGENCIAMIENTO: ' . $fechaActual . '
        </td>
    </tr>
</table>';

  $tipoEstudiante = $personal['tipo_estudiante'] ?? '';
$gradoActual = $personal['grado_actual'] ?? '';
$gradoMatricular = $personal['grado_matricular'] ?? '';

// 1. Marcadores para Nuevo/Antiguo
$esAntiguo = ($tipoEstudiante == 'Antiguo') ? '&#9745;' : '&#9744;';
$esNuevo = ($tipoEstudiante == 'Nuevo') ? '&#9745;' : '&#9744;';

// 2. Lógica para repitente
$esRepitente = false;
if (!empty($gradoActual) && !empty($gradoMatricular)) {
    $gradoActualNormalizado = strtoupper(trim($gradoActual));
    $gradoMatricularNormalizado = strtoupper(trim($gradoMatricular));
    
    if ($gradoActualNormalizado === $gradoMatricularNormalizado) {
        $esRepitente = true;
    }
}

// Marcadores para repitente
$esRepitenteSi = $esRepitente ? '&#9745;' : '&#9744;';
$esRepitenteNo = !$esRepitente ? '&#9745;' : '&#9744;';

// 3. Lógica para "Renueva Matrícula" (Si es Antiguo → SI)
$renuevaMatriculaSi = ($tipoEstudiante == 'Antiguo') ? '&#9745;' : '&#9744;';
$renuevaMatriculaNo = ($tipoEstudiante == 'Nuevo') ? '&#9745;' : '&#9744;';

$estadoEstudiante = '
<table cellpadding="1" cellspacing="0" border="1" width="78%" style="font-size:8px; border-collapse:collapse;">
    <tr>
        <td width="50%" style="padding:2px;">
            <strong>ESTUDIANTE:</strong> &nbsp;
            ANTIGUO <span style="font-family:dejavusans;">' . $esAntiguo . '</span> &nbsp;&nbsp;
            NUEVO <span style="font-family:dejavusans;">' . $esNuevo . '</span>
        </td>

        <td width="50%" rowspan="2" style="vertical-align:top; padding:2px;">
            <strong>REPITENTE:</strong> &nbsp;
            SI <span style="font-family:dejavusans;">' . $esRepitenteSi . '</span> &nbsp;&nbsp;
            NO <span style="font-family:dejavusans;">' . $esRepitenteNo . '</span>
            <br/>
            <strong>RENUEVA MATRÍCULA:</strong> &nbsp;
            SI <span style="font-family:dejavusans;">' . $renuevaMatriculaSi . '</span> &nbsp;&nbsp;
            NO <span style="font-family:dejavusans;">' . $renuevaMatriculaNo . '</span>
        </td>
    </tr>

    <tr>
        <td style="padding:2px;">&nbsp;</td>
    </tr>
</table>
<br/>';
    // Escribir el header
    $pdf->writeHTML($headerHtml, true, false, true, false, '');
    
    // DIBUJAR EL RECTÁNGULO DE LA FOTO CON POSICIONAMIENTO ABSOLUTO
    // X=170mm (esquina derecha), Y=10mm (arriba), W=30mm, H=55mm
    $pdf->RoundedRect(165, 10, 35, 40, 3);

    
    // Añadir texto dentro del rectángulo
    $pdf->SetFont('helvetica', 'B', 7);
    $pdf->SetXY(168, 18);
    $pdf->Cell(30, 4, 'PEGUE AQUÍ LA', 0, 1, 'C');
    $pdf->SetXY(168, 22);
    $pdf->Cell(30, 4, 'FOTOGRAFÍA DEL', 0, 1, 'C');
    $pdf->SetXY(168, 26);
    $pdf->Cell(30, 4, 'ESTUDIANTE', 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 6);
    $pdf->SetXY(168, 30);
    $pdf->Cell(30, 4, '(TAMAÑO 3x4)', 0, 1, 'C');
    
    // Continuar con el resto del contenido
    $pdf->writeHTML($tituloHtml . $bloqueGradoFecha . $estadoEstudiante, true, false, true, false, '');

      // Preparar la condición de salud
$condicionSalud = '';
if (!empty($salud['informacion_salud_adicional'])) {
    $condicionSalud = $salud['informacion_salud_adicional'];
} elseif (!empty($salud['enfermedad_especificar'])) {
    $condicionSalud = $salud['enfermedad_especificar'];
} elseif (!empty($salud['alergia_especificar'])) {
    $condicionSalud = "Alergia: " . $salud['alergia_especificar'];
}

// Si no hay información de salud, poner "No aplica" junto al título
if (empty($condicionSalud)) {
    $condicionSalud = 'No aplica';
}

$datosBasicos = '
<table cellpadding="1" cellspacing="0" border="1" width="100%" style="font-size:8px; border-collapse:collapse;">
    <tr style="background-color:#d9d9d9;">
        <td colspan="4" style="font-weight:bold; padding:2px; text-align:center;">
            DATOS BÁSICOS DEL ESTUDIANTE
        </td>
    </tr>

    <tr>
        <td width="30%" style="font-weight:bold;">APELLIDOS Y NOMBRES COMPLETOS:</td>
        <td width="70%" colspan="3">' . $this->safeVal(($personal['apellidos'] ?? '') . ' ' . ($personal['nombres'] ?? '')) . '</td>
    </tr>

    <tr>
        <td width="20%" style="font-weight:bold;">TIPO DE DOCUMENTO:</td>
        <td width="20%">' . $this->safeVal($personal['tipo_documento'] ?? '') . '</td>
        <td width="25%" style="font-weight:bold;">NÚMERO DE DOCUMENTO:</td>
        <td width="35%">' . $this->safeVal($personal['numero_documento'] ?? '') . '</td>
    </tr>

    <tr>
        <td style="font-weight:bold;">FECHA DE NACIMIENTO:</td>
        <td>' . $this->safeVal($personal['fecha_nacimiento'] ?? '') . '</td>
        <td style="font-weight:bold;">LUGAR DE NACIMIENTO:</td>
        <td>' . $this->safeVal($personal['lugar_nacimiento'] ?? '') . '</td>
    </tr>

    <tr>
        <td style="font-weight:bold;">EDAD:</td>
        <td>' . $this->safeVal($personal['edad'] ?? '') . '</td>
        <td style="font-weight:bold;">RH:</td>
        <td>' . $this->safeVal($personal['tipo_sangre'] ?? '') . '</td>
    </tr>

    <tr>
        <td style="font-weight:bold;">DIRECCIÓN DE RESIDENCIA:</td>
        <td colspan="3">' . $this->safeVal($general['direccion'] ?? '') . '</td>
    </tr>

    <tr>
        <td style="font-weight:bold;">BARRIO:</td>
        <td>' . $this->safeVal($general['barrio'] ?? '') . '</td>
        <td style="font-weight:bold;">TELÉFONOS:</td>
        <td>' . $this->safeVal($personal['celular'] ?? '') . ($personal['telefono_residencia'] ? ' / ' . $personal['telefono_residencia'] : '') . '</td>
    </tr>

    <tr>
        <td style="font-weight:bold;">EPS:</td>
        <td>' . $this->safeVal($general['eps'] ?? '') . '</td>
        <td style="font-weight:bold;">SISBEN:</td>
        <td>' . $this->safeVal($general['sisben'] ?? '') . '</td>
    </tr>

    <tr>
        <td style="font-weight:bold;">ESTRATO:</td>
        <td colspan="3">' . $this->safeVal($general['estrato'] ?? '') . '</td>
    </tr>

    <tr>
        <td colspan="4" style="font-weight:bold;">CONDICIÓN ESPECIAL DE SALUD PARA TENER EN CUENTA: 
            <span style="font-weight:normal;">' . $this->safeVal($condicionSalud) . '</span>
        </td>
    </tr>

    <tr>
        <td colspan="2" style="font-weight:bold;">CORREO ELECTRÓNICO OBLIGATORIO DEL ACUDIENTE:</td>
        <td colspan="2">' . $this->safeVal($acudiente['correo'] ?? $madre['correo'] ?? $padre['correo'] ?? '') . '</td>
    </tr>
</table>';

       $infoAcademica = '
<table cellpadding="1" cellspacing="0" border="1" width="100%" style="font-size:8px; border-collapse:collapse;">
    <tr style="background-color:#d9d9d9;">
        <td colspan="3" style="font-weight:bold; padding:2px; text-align:center;">
            INFORMACIÓN ACADÉMICA DEL ESTUDIANTE
        </td>
    </tr>
    <tr style="background-color:#f0f0f0; text-align:center; font-weight:bold;">
        <td width="15%">AÑO</td>
        <td width="65%">ESTABLECIMIENTO EDUCATIVO</td>
        <td width="20%">GRADO</td>
    </tr>';

// SOLO USAR LOS DATOS QUE YA TIENES EN LA BD
$years = [2025, 2024, 2023, 2022, 2021, 2020, 2019];
foreach ($years as $year) {
    // Los datos YA están en $general
    $campo_colegio = 'historial_' . $year . '_colegio';
    $campo_grado = 'historial_' . $year . '_grado';
    $campo_ano = 'historial_' . $year . '_ano';
    
    // Tomar directamente de los datos que ya tienes
    $ano = $general[$campo_ano] ?? $year;
    $colegio = $general[$campo_colegio] ?? '';
    $grado = $general[$campo_grado] ?? '';
    
    $infoAcademica .= '<tr>
        <td style="text-align:center; font-weight:bold;">' . $ano . '</td>
        <td>' . $this->safeVal($colegio) . '</td>
        <td>' . $this->safeVal($grado) . '</td>
    </tr>';
}
$infoAcademica .= '</table>';

       $infoFamiliar = '
<table cellpadding="1" cellspacing="0" border="1" width="100%" style="font-size:8px; border-collapse:collapse;">
    <tr style="background-color:#d9d9d9;">
        <td colspan="4" style="font-weight:bold; padding:2px; text-align:center;">INFORMACIÓN FAMILIAR DEL ESTUDIANTE</td>
    </tr>

    <!-- DATOS DEL PADRE -->
    <tr style="background-color:#e8e8e8;">
        <td colspan="4" style="font-weight:bold; padding:2px; text-align:center;">DATOS DEL PADRE</td>
    </tr>
    <tr>
        <td width="30%" style="font-weight:bold;">NOMBRES Y APELLIDOS:</td>
        <td width="30%">' . $this->safeVal(($padre['nombres'] ?? '') . ' ' . ($padre['apellidos'] ?? '')) . '</td>
        <td width="20%" style="font-weight:bold;">CÉDULA:</td>
        <td width="20%">' . $this->safeVal($padre['numero_documento'] ?? '') . '</td>
    </tr>
    <tr>
        <td style="font-weight:bold;">TELÉFONO:</td>
        <td>' . $this->safeVal($padre['celular'] ?? '') . '</td>
        <td style="font-weight:bold;">EXPEDIDA EN:</td>
        <td>' . $this->safeVal($padre['lugar_expedicion'] ?? '') . '</td>
    </tr>
    <tr>
        <td style="font-weight:bold;">DIRECCIÓN:</td>
        <td>' . $this->safeVal($padre['direccion'] ?? '') . '</td>
        <td style="font-weight:bold;">OCUPACIÓN:</td>
        <td>' . $this->safeVal($padre['ocupacion'] ?? '') . '</td>
    </tr>
    <tr>
        <td style="font-weight:bold;">CORREO ELECTRÓNICO:</td>
        <td colspan="3">' . $this->safeVal($padre['correo'] ?? '') . '</td>
    </tr>

    <!-- DATOS DE LA MADRE -->
    <tr style="background-color:#e8e8e8;">
        <td colspan="4" style="font-weight:bold; padding:2px; text-align:center;">DATOS DE LA MADRE</td>
    </tr>
    <tr>
        <td style="font-weight:bold;">NOMBRES Y APELLIDOS:</td>
        <td>' . $this->safeVal(($madre['nombres'] ?? '') . ' ' . ($madre['apellidos'] ?? '')) . '</td>
        <td style="font-weight:bold;">CÉDULA:</td>
        <td>' . $this->safeVal($madre['numero_documento'] ?? '') . '</td>
    </tr>
    <tr>
        <td style="font-weight:bold;">TELÉFONO:</td>
        <td>' . $this->safeVal($madre['celular'] ?? '') . '</td>
        <td style="font-weight:bold;">EXPEDIDA EN:</td>
        <td>' . $this->safeVal($madre['lugar_expedicion'] ?? '') . '</td>
    </tr>
    <tr>
        <td style="font-weight:bold;">DIRECCIÓN:</td>
        <td>' . $this->safeVal($madre['direccion'] ?? '') . '</td>
        <td style="font-weight:bold;">OCUPACIÓN:</td>
        <td>' . $this->safeVal($madre['ocupacion'] ?? '') . '</td>
    </tr>
    <tr>
        <td style="font-weight:bold;">CORREO ELECTRÓNICO:</td>
        <td colspan="3">' . $this->safeVal($madre['correo'] ?? '') . '</td>
    </tr>

    <!-- DATOS DEL ACUDIENTE -->
    <tr style="background-color:#e8e8e8;">
        <td colspan="4" style="font-weight:bold; padding:2px; text-align:center;">DATOS DEL ACUDIENTE</td>
    </tr>
    <tr>
        <td style="font-weight:bold;">NOMBRES Y APELLIDOS:</td>
        <td>' . $this->safeVal(($acudiente['nombres'] ?? '') . ' ' . ($acudiente['apellidos'] ?? '')) . '</td>
        <td style="font-weight:bold;">CÉDULA:</td>
        <td>' . $this->safeVal($acudiente['numero_documento'] ?? '') . '</td>
    </tr>
    <tr>
        <td style="font-weight:bold;">TELÉFONO:</td>
        <td>' . $this->safeVal($acudiente['celular'] ?? '') . '</td>
        <td style="font-weight:bold;">EXPEDIDA EN:</td>
        <td>' . $this->safeVal($acudiente['lugar_expedicion'] ?? '') . '</td>
    </tr>
    <tr>
        <td style="font-weight:bold;">DIRECCIÓN:</td>
        <td>' . $this->safeVal($acudiente['direccion'] ?? '') . '</td>
        <td style="font-weight:bold;">OCUPACIÓN:</td>
        <td>' . $this->safeVal($acudiente['ocupacion'] ?? '') . '</td>
    </tr>
    <tr>
        <td style="font-weight:bold;">CORREO ELECTRÓNICO:</td>
        <td colspan="3">' . $this->safeVal($acudiente['correo'] ?? '') . '</td>
    </tr>
</table>';

    $textoLegal = '
<div style="font-size:7px; text-align:justify; font-style:italic; line-height:1.2; margin-top:3px;">
    <strong>Nosotros Padre/Madre/acudiente y estudiante, aceptamos el Proyecto Educativo Institucional (PEI) y nos comprometemos a cumplir con el manual de convivencia y demás planes, programas, normas y demás disposiciones de la institución educativa. El padre de familia y el estudiante se comprometen a obtener copia del manual de convivencia el cual estará disponible en el siguiente enlace:</strong> https://www.redacademica.edu.co/colegios <strong>escoger el Colegio María Currea Manrique opción manual de convivencia.</strong>
</div>
<div style="font-size:8px; font-style:italic; text-align:center; margin-top:3px;">
    <strong>EN CONSTANCIA FIRMAMOS:</strong>
</div> 
<br/>
<br/>
<br/>
<br/>';

    $firmas = '
<table cellpadding="3" cellspacing="0" border="0" width="100%" style="font-size:8px; margin-top:5px;">
    <tr>
        <td width="50%" align="center" valign="bottom">
            <div style="border-bottom:1px solid #000; width:75%; margin:0 auto; margin-top:10px;">&nbsp;</div>
            <strong style="font-style:italic;">FIRMA ESTUDIANTE</strong>
        </td>
        <td width="50%" align="center" valign="bottom">
            <div style="border-bottom:1px solid #000; width:75%; margin:0 auto; margin-top:10px;">&nbsp;</div>
            <strong style="font-style:italic;">PADRE/MADRE/ACUDIENTE</strong>
        </td>
    </tr>
</table>
<div style="font-size:7px; text-align:center; line-height:1.3; margin-top:5px;">
    Calle 58 D sur No. 51-10. Barrio Atlanta – Ciudad Bolívar<br/>
    Calendario A en jornada única &nbsp;|&nbsp; aaalvarezg@educacionbogota.edu.co
</div>';

    // Escribir el resto del contenido
    $pdf->writeHTML($datosBasicos . $infoAcademica . $infoFamiliar . $textoLegal . $firmas, true, false, true, false, '');

   // Salida
$filename = 'Hoja_Matricula_' . ($personal['nombres'] ?? 'Estudiante') . '_' . date('Y-m-d') . '.pdf';

    if ($modo === 'descargar') {
        $pdf->Output($filename, 'D'); // ⬇ DESCARGA
    } else {
        $pdf->Output($filename, 'I'); // 👁 VER
    }

    exit;
}

}

// ✅ PROCESADOR DE ACCIONES ACTUALIZADO
if (isset($_GET['action'])) {
    $studentController = new StudentController();

    switch ($_GET['action']) {
        case 'save_personal_info':
            $studentController->savePersonalInfo();
            break;
        case 'save_general_info':
            $studentController->saveGeneralInfo();
            break;
        case 'save_mother_data': // ← NUEVA ACCIÓN
            $studentController->saveMotherData();
            break;
        case 'save_father_data': // ← NUEVA ACCIÓN
            $studentController->saveFatherData();
            break;
        case 'save_acudiente_data': // ← NUEVA ACCIÓN
            $studentController->saveAcudienteData();
            break;
        case 'save_vivienda_data': // ← NUEVA ACCIÓN
            $studentController->saveViviendaData();
            break;
        case 'save_salud_data': // ← NUEVA ACCIÓN
            $studentController->saveSaludData();
            break;
        case 'generar_hoja_matricula':
            $studentController->generarHojaMatricula();
            break;
        default:
            $_SESSION['error_message'] = "Acción no válida";
            header('Location: ../home.php');
            exit;
    }
}
?>