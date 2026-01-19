<?php
session_start();

// Verificar si hay un token en la URL
if (!isset($_GET['token']) || empty($_GET['token'])) {
    $_SESSION['error'] = 'Enlace de recuperación inválido';
    header('Location: olvide_password.php');
    exit;
}

$token = $_GET['token'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Restablecer Contraseña - Sistema de Matrículas 2025</title>
    <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
    <div class="container">
        <img src="assets/img/escudomaria.png" alt="Escudo del colegio" class="logo" />
        <h1>Colegio Maria Currea Manrique</h1>

        <div class="login-box">
            <h2>🔑 Restablecer Contraseña</h2>

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
                <input type="hidden" name="action" value="restablecer_password">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                
                <label for="documento">Usuario (Documento de identificación)</label>
                <input
                    type="text"
                    id="documento"
                    name="documento"
                    value="<?php echo isset($_SESSION['form_data']['documento']) ? htmlspecialchars($_SESSION['form_data']['documento']) : ''; ?>"
                    pattern="[0-9]{6,12}"
                    title="Ingrese solo números, entre 6 y 12 dígitos"
                    required
                />

                <label for="nueva_password">Nueva Contraseña</label>
                <input type="password" id="nueva_password" name="nueva_password" required />

                <label for="confirmar_password">Confirmar Nueva Contraseña</label>
                <input type="password" id="confirmar_password" name="confirmar_password" required />

                <button type="submit">✔ Restablecer Contraseña</button>
            </form>

            <p>
                <a href="index.php">Volver al inicio de sesión</a>
            </p>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script>
        // Validación de contraseñas coincidentes
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('nueva_password').value;
            const confirmPassword = document.getElementById('confirmar_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Las contraseñas no coinciden. Por favor verifique.');
                document.getElementById('nueva_password').focus();
            }
        });
    </script>
</body>
</html>