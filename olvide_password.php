<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Recuperar Contraseña - Sistema de Matrículas 2025</title>
    <link rel="stylesheet" href="assets/css/style.css" />
  </head>
  <body>
    <div class="container">
      <!-- Escudo del colegio -->
      <img
        src="assets/img/escudomaria.png"
        alt="Escudo del colegio"
        class="logo"
      />

      <!-- Nombre del colegio -->
      <h1>Colegio Maria Currea Manrique</h1>

      <!-- Contenedor del formulario -->
      <div class="login-box">
        <h2>🔐 Recuperar Contraseña</h2>

        <!-- Mensajes de éxito/error -->
        <?php if (isset($_SESSION['error_message'])): ?>
          <div class="error-message">
            <?php echo $_SESSION['error_message']; ?>
            <?php unset($_SESSION['error_message']); ?>
          </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success_message'])): ?>
          <div class="success-message">
            <?php echo $_SESSION['success_message']; ?>
            <?php unset($_SESSION['success_message']); ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="controllers/AuthController.php">
          <input type="hidden" name="action" value="solicitar_recuperacion">
          
          <label for="correo_institucional">Correo Institucional</label>
          <input
            type="email"
            id="correo_institucional"
            name="correo_institucional"
            value="<?php echo isset($_SESSION['form_data']['correo_institucional']) ? htmlspecialchars($_SESSION['form_data']['correo_institucional']) : ''; ?>"
            placeholder="usuario@colegiomaria.edu.co"
            required
          />

          <button type="submit">✔ Enviar enlace de recuperación</button>
        </form>

        <p>
          ¿Recordó su contraseña?
          <a href="index.php">Ingresar al sistema</a>
        </p>
        
        <p>
          ¿No tiene cuenta aún?
          <a href="registro.php">Registrarse aquí</a>
        </p>
      </div>
    </div>

    <script src="assets/js/main.js"></script>
  </body>
</html>