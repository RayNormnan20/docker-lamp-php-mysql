<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect('db', 'root', '', "dbname");

// Cerrar sesión
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Operaciones CRUD
$message = "";

if (isset($_POST['add'])) {
    $name = $_POST['name'];
    mysqli_query($conn, "INSERT INTO Person (name) VALUES ('$name')");
    $message = "<div class='alert alert-success fade-out'>Registro agregado correctamente</div>";
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM Person WHERE id=$id");
    $message = "<div class='alert alert-success fade-out'>Registro eliminado correctamente</div>";
}

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    mysqli_query($conn, "UPDATE Person SET name='$name' WHERE id=$id");
    $message = "<div class='alert alert-success fade-out'>Registro actualizado correctamente</div>";
}

$result = mysqli_query($conn, "SELECT * FROM Person");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>
    <div class="container-fluid">
        <h1>¡Hola, <?= $_SESSION['user'] ?> te da la bienvenida!</h1>
        <a href="?logout=true" class="btn btn-danger">Cerrar Sesión</a>
        
        <?= $message ?>

        <form method="POST">
            <div class="form-group">
                <label>Nombre:</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <button type="submit" name="add" class="btn btn-primary">Agregar</button>
        </form>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td>
                            <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Eliminar</a>
                            <button class="btn btn-warning btn-sm" onclick="editar(<?= $row['id'] ?>, '<?= htmlspecialchars($row['name']) ?>')">Editar</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <form method="POST" id="updateForm" style="display:none;">
            <input type="hidden" name="id" id="updateId">
            <div class="form-group">
                <label>Nuevo Nombre:</label>
                <input type="text" name="name" id="updateName" class="form-control" required>
            </div>
            <button type="submit" name="update" class="btn btn-success">Actualizar</button>
            <button type="button" class="btn btn-secondary" onclick="cancelarEdicion()">Cancelar</button>
        </form>
    </div>

    <script>
        function editar(id, name) {
            document.getElementById('updateForm').style.display = 'block';
            document.getElementById('updateId').value = id;
            document.getElementById('updateName').value = name;
        }

        function cancelarEdicion() {
            document.getElementById('updateForm').style.display = 'none';
        }

        setTimeout(() => {
            document.querySelectorAll('.fade-out').forEach(msg => {
                msg.style.opacity = '0';
                setTimeout(() => msg.remove(), 1000);
            });
        }, 3000);
    </script>
</body>
</html>
