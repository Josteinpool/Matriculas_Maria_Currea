<?php
session_start();
require_once 'controllers/AuthController.php';

$authController = new AuthController();

// Verificar si ya está logueado
if (isset($_SESSION['user_id'])) {
    // REDIRIGIR SEGÚN ROL SI YA ESTÁ LOGUEADO
    if ($_SESSION['user_role'] === 'admin') {
        header('Location: admin/index.php');
    } else {
        header('Location: home.php');
    }
    exit;
}

// Procesar login si se envió el formulario
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $documento = trim($_POST['usuario']);
    $password = $_POST['password'];

    if ($authController->login($documento, $password)) {
        // REDIRIGIR SEGÚN ROL DESPUÉS DE LOGIN EXITOSO
        if ($_SESSION['user_role'] === 'admin') {
            header('Location: admin/index.php');
        } else {
            header('Location: home.php');
        }
        exit;
    } else {
        $error = 'Documento o contraseña incorrectos';
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sistema de Matrículas 2025</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>
  <div class="container">
    <!-- Escudo del colegio -->
    <img src="assets/img/escudomaria.png" alt="Escudo del colegio" class="logo" />

    <!-- Nombre del colegio -->
    <h1>Colegio Maria Currea Manrique</h1>

    <!-- Contenedor del formulario -->
    <div class="login-box">
      <h2>📘 Matrículas 2025</h2>

      <!-- Mostrar mensajes de éxito -->
      <?php if (isset($_SESSION['success'])): ?>
        <div
          style="background: #d4edda; color: #155724; padding: 12px 15px; border-radius: 10px; margin-bottom: 20px; border-left: 4px solid #c3e6cb; font-weight: 500;">
          ✅ <?php echo $_SESSION['success']; ?>
          <?php unset($_SESSION['success']); ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($error)): ?>
        <div
          style="background: #f8d7da; color: #721c24; padding: 12px 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #f5c6cb; font-weight: 500;">
          ❌ <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="">
        <label for="usuario">Usuario (Documento de identificación)</label>
        <input type="text" id="usuario" name="usuario"
          value="<?php echo isset($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : ''; ?>" required />

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required />

        <button type="submit">✔ Ingresar</button>
      </form>

      <p>¿No tiene acceso aún? <a href="registro.php">Regístrese aquí</a></p>
      <p>¿Olvido su contraseña? <a href="olvide_password.php">Recuerdela aquí</a></p>
    </div>
  </div>

  <script src="assets/js/main.js"></script>
</body>

</html>