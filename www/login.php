<?php
session_start();

// Conectar a la base de datos
$conn = mysqli_connect('db', 'root', '', "dbname");
if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

$registroExitoso = false; // Variable para controlar el registro exitoso

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['login'])) {
        // Inicio de sesión
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        
        $query = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $query);
        
        if (!$result) {
            die("Error en la consulta SQL: " . mysqli_error($conn));
        }
        
        $row = mysqli_fetch_assoc($result);

        if ($row && password_verify($password, $row['password'])) {
            $_SESSION['user'] = $username;
            header("Location: index.php");
            exit();
        } else {
            echo "<p class='alert alert-danger'>Usuario o contraseña incorrectos</p>";
        }
    } elseif (isset($_POST['register'])) {
        // Registro de usuario
        $newUsername = mysqli_real_escape_string($conn, $_POST['new_username']);
        $newPassword = mysqli_real_escape_string($conn, $_POST['new_password']);
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        $checkUserQuery = "SELECT * FROM users WHERE username = '$newUsername'";
        $checkUserResult = mysqli_query($conn, $checkUserQuery);
        
        if (mysqli_num_rows($checkUserResult) > 0) {
            echo "<p class='alert alert-danger'>El nombre de usuario ya está en uso.</p>";
        } else {
            $insertQuery = "INSERT INTO users (username, password) VALUES ('$newUsername', '$hashedPassword')";
            
            if (mysqli_query($conn, $insertQuery)) {
                $registroExitoso = true; // Se usará para volver a mostrar el login
                echo "<p class='alert alert-success'>Usuario registrado exitosamente. Ahora puedes iniciar sesión.</p>";
            } else {
                echo "<p class='alert alert-danger'>Error al registrar el usuario.</p>";
            }
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script>
        function mostrarRegistro() {
            document.getElementById("loginForm").style.display = "none";
            document.getElementById("registerForm").style.display = "block";
        }

        function mostrarLogin() {
            document.getElementById("loginForm").style.display = "block";
            document.getElementById("registerForm").style.display = "none";
        }

        // Si el registro fue exitoso, mostrar el login automáticamente
        <?php if ($registroExitoso): ?>
            window.onload = function() {
                mostrarLogin();
            };
        <?php endif; ?>
    </script>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-body">
                    
                    <!-- Formulario de Inicio de Sesión -->
                    <div id="loginForm">
                        <h3 class="text-center">Iniciar Sesión</h3>
                        <form method="POST">
                            <div class="mb-3">
                                <label>Usuario:</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Contraseña:</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" name="login" class="btn btn-primary w-100">Ingresar</button>
                        </form>
                        <hr>
                        <button class="btn btn-secondary w-100 mt-2" onclick="mostrarRegistro()">Registrar Usuario</button>
                    </div>

                    <!-- Formulario de Registro (inicialmente oculto) -->
                    <div id="registerForm" style="display: none;">
                        <h3 class="text-center">Nuevo Usuario</h3>
                        <form method="POST">
                            <div class="mb-3">
                                <label>Usuario:</label>
                                <input type="text" name="new_username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Contraseña:</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>
                            <button type="submit" name="register" class="btn btn-success w-100">Registrar</button>
                        </form>
                        <hr>
                        <button class="btn btn-secondary w-100 mt-2" onclick="mostrarLogin()">Volver a Inicio de Sesión</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
