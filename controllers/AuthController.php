<?php
// controllers/AuthController.php

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../config/database.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->userModel = new UserModel($db);
    }

    public function register($documento, $password) {
        // Verificar si el usuario ya existe
        if ($this->userModel->getUserByDocument($documento)) {
            return "El documento ya está registrado";
        }

        // Hash de la contraseña
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Crear el usuario
        $data = [
            'documento' => $documento,
            'password' => $hashedPassword,
            'rol' => 'usuario'
        ];

        if ($this->userModel->createUser($data)) {
            return true;
        }
        return "Error al crear el usuario";
    }

    public function login($documento, $password) {
        $user = $this->userModel->getUserByDocument($documento);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_document'] = $user['documento'];
            $_SESSION['user_role'] = $user['rol'];
            return true;
        }
        return false;
    }

    // NUEVO MÉTODO: Solicitar recuperación de contraseña
    public function solicitarRecuperacion($correo) {
        // Generar token único (no necesitamos buscar usuario por correo)
        $token = bin2hex(random_bytes(50));
        $expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Guardar token en la base de datos (sin asociar a usuario todavía)
        if ($this->userModel->guardarTokenGenerico($token, $expiracion, $correo)) {
            return [
                'success' => true,
                'token' => $token,
                'message' => 'Se ha enviado un enlace de recuperación a tu correo'
            ];
        }
        
        return "Error al procesar la solicitud";
    }

    // NUEVO MÉTODO: Restablecer contraseña con token + documento
    public function restablecerPassword($token, $documento, $nuevaPassword) {
        // Primero validar el token
        $tokenValido = $this->userModel->validarTokenGenerico($token);
        
        if (!$tokenValido) {
            return "El enlace de recuperación es inválido o ha expirado";
        }

        // Buscar usuario por documento
        $user = $this->userModel->getUserByDocument($documento);
        if (!$user) {
            return "No existe un usuario con ese documento";
        }

        // Hash de la nueva contraseña
        $hashedPassword = password_hash($nuevaPassword, PASSWORD_DEFAULT);

        // Actualizar contraseña y limpiar token
        if ($this->userModel->updatePassword($user['id'], $hashedPassword)) {
            // Limpiar el token después de usarlo
            $this->userModel->limpiarToken($token);
            return true;
        }
        
        return "Error al actualizar la contraseña";
    }

    public function logout() {
        session_destroy();
        header('Location: ../index.php');
        exit;
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'documento' => $_SESSION['user_document'],
                'rol' => $_SESSION['user_role']
            ];
        }
        return null;
    }
}

// Procesar registro si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    session_start();
    $authController = new AuthController();
    
    $documento = trim($_POST['documento']);
    $password = $_POST['password'];

    // Validaciones básicas
    if (empty($documento) || empty($password)) {
        $_SESSION['error'] = 'Todos los campos son obligatorios';
        $_SESSION['form_data'] = ['documento' => $documento];
        header('Location: ../registro.php');
        exit;
    }

    $result = $authController->register($documento, $password);
    
    if ($result === true) {
        $_SESSION['success'] = 'Registro exitoso. Ahora puede iniciar sesión.';
        header('Location: ../index.php');
    } else {
        $_SESSION['error'] = $result;
        $_SESSION['form_data'] = ['documento' => $documento];
        header('Location: ../registro.php');
    }
    exit;
}

// Procesar login si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    session_start();
    $authController = new AuthController();
    
    $documento = trim($_POST['usuario']);
    $password = $_POST['password'];

    if ($authController->login($documento, $password)) {
        header('Location: ../home.php');
    } else {
        $_SESSION['error'] = 'Documento o contraseña incorrectos';
        header('Location: ../index.php');
    }
    exit;
}

// NUEVO: Procesar solicitud de recuperación de contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'solicitar_recuperacion') {
    session_start();
    $authController = new AuthController();
    
    $correo = trim($_POST['correo_institucional']);

    if (empty($correo)) {
        $_SESSION['error_message'] = 'El correo institucional es obligatorio';
        header('Location: ../olvide_password.php');
        exit;
    }

    $result = $authController->solicitarRecuperacion($correo);
    
    if (is_array($result) && $result['success']) {
        $_SESSION['success'] = 'Se ha enviado un enlace de recuperación a tu correo.';
        header('Location: ../index.php');
        exit;
    } else {
        $_SESSION['error_message'] = $result;
        $_SESSION['form_data'] = ['correo_institucional' => $correo];
        header('Location: ../olvide_password.php');
        exit;
    }
}

// NUEVO: Procesar restablecimiento de contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'restablecer_password') {
    session_start();
    $authController = new AuthController();
    
    $token = $_POST['token'];
    $documento = trim($_POST['documento']);
    $nueva_password = $_POST['nueva_password'];
    $confirmar_password = $_POST['confirmar_password'];

    // Guardar datos del formulario en sesión por si hay error
    $_SESSION['form_data'] = ['documento' => $documento];

    if (empty($documento)) {
        $_SESSION['error'] = 'El documento es obligatorio';
        header('Location: ../restablecer_password.php?token=' . $token);
        exit;
    }

    if ($nueva_password !== $confirmar_password) {
        $_SESSION['error'] = 'Las contraseñas no coinciden';
        header('Location: ../restablecer_password.php?token=' . $token);
        exit;
    }

    if (empty($nueva_password)) {
        $_SESSION['error'] = 'La nueva contraseña es obligatoria';
        header('Location: ../restablecer_password.php?token=' . $token);
        exit;
    }

    $result = $authController->restablecerPassword($token, $documento, $nueva_password);
    
    if ($result === true) {
        // Limpiar datos del formulario en caso de éxito
        unset($_SESSION['form_data']);
        $_SESSION['success'] = 'Contraseña actualizada exitosamente. Ahora puede iniciar sesión.';
        header('Location: ../index.php');
    } else {
        $_SESSION['error'] = $result;
        header('Location: ../restablecer_password.php?token=' . $token);
    }
    exit;
}

// Procesar logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_start();
    $authController = new AuthController();
    $authController->logout();
}
?>