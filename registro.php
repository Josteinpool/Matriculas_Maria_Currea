<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registro - Sistema de Matrículas 2025</title>
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
        <h2>📘 Registro para Matrículas 2025</h2>

        <!-- Mensajes de éxito/error -->
        <?php if (isset($_SESSION['error'])): ?>
          <div class="error-message">
            <?php echo $_SESSION['error']; ?>
            <?php unset($_SESSION['error']); ?>
          </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
          <div class="success-message">
            <?php echo $_SESSION['success']; ?>
            <?php unset($_SESSION['success']); ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="controllers/AuthController.php">
          <input type="hidden" name="action" value="register">
          
          <label for="documento">Usuario (Documento de identificación del Estudiante)</label>
          <input
            type="text"
            id="documento"
            name="documento"
            value="<?php echo isset($_SESSION['form_data']['documento']) ? htmlspecialchars($_SESSION['form_data']['documento']) : ''; ?>"
            pattern="[0-9]{6,12}"
            title="Ingrese solo números, entre 6 y 12 dígitos"
            required
          />

          <label for="password">Cree una Contraseña</label>
          <input type="password" id="password" name="password" required />

          <label for="confirm-password">Confirme la Contraseña</label>
          <input
            type="password"
            id="confirm-password"
            name="confirm-password"
            required
          />

          <button type="submit">✔ Crear acceso</button>
        </form>

        <p>
          ¿Ya tiene acceso al sistema?
          <a href="index.php">Ingresar</a>
        </p>

        <p>
          ¿Olvidó su contraseña?
          <a href="olvide_password.php">Recuperar contraseña</a>
        </p>
      </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script>
      // Validación de contraseñas coincidentes
      document.querySelector('form').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        
        if (password !== confirmPassword) {
          e.preventDefault();
          alert('Las contraseñas no coinciden. Por favor verifique.');
          document.getElementById('password').focus();
        }
      });
    </script>
  </body>
</html>