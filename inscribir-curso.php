<?php
require_once 'database.php';

// Función para registrar usuario e inscribirlo en un curso
function inscribirUsuario($nombre, $email, $telefono, $id_curso) {
    global $conexion;

    try {
        // Comenzar transacción
        $conexion->beginTransaction();

        // Verificar si el curso tiene cupos disponibles
        $stmt = $conexion->prepare("SELECT cupos_disponibles FROM cursos WHERE id_curso = :id_curso");
        $stmt->execute(['id_curso' => $id_curso]);
        $curso = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($curso['cupos_disponibles'] <= 0) {
            throw new Exception("Lo siento, no hay cupos disponibles en este curso.");
        }

        // Verificar si el email ya existe
        $stmt = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $usuario_existente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario_existente) {
            $id_usuario = $usuario_existente['id_usuario'];
        } else {
            // Registrar nuevo usuario
            $stmt = $conexion->prepare("
                INSERT INTO usuarios (nombre_completo, email, telefono) 
                VALUES (:nombre, :email, :telefono)
            ");
            $stmt->execute([
                'nombre' => $nombre,
                'email' => $email,
                'telefono' => $telefono
            ]);
            $id_usuario = $conexion->lastInsertId();
        }

        // Verificar si ya está inscrito en este curso
        $stmt = $conexion->prepare("
            SELECT id_inscripcion FROM inscripciones 
            WHERE id_usuario = :id_usuario AND id_curso = :id_curso
        ");
        $stmt->execute([
            'id_usuario' => $id_usuario,
            'id_curso' => $id_curso
        ]);
        $inscripcion_existente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($inscripcion_existente) {
            throw new Exception("El usuario ya está inscrito en este curso.");
        }

        // Registrar inscripción
        $stmt = $conexion->prepare("
            INSERT INTO inscripciones (id_usuario, id_curso) 
            VALUES (:id_usuario, :id_curso)
        ");
        $stmt->execute([
            'id_usuario' => $id_usuario,
            'id_curso' => $id_curso
        ]);

        // Reducir cupos disponibles
        $stmt = $conexion->prepare("
            UPDATE cursos 
            SET cupos_disponibles = cupos_disponibles - 1 
            WHERE id_curso = :id_curso
        ");
        $stmt->execute(['id_curso' => $id_curso]);

        // Confirmar transacción
        $conexion->commit();

        return ['exito' => true, 'nombre' => $nombre];
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conexion->rollBack();
        return ['exito' => false, 'mensaje' => $e->getMessage()];
    }
}

// Procesar formulario de inscripción
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $id_curso = $_POST['curso'];

    $resultado = inscribirUsuario($nombre, $email, $telefono, $id_curso);
    if ($resultado['exito']) {
        $mensaje = "¡Inscripción exitosa! Bienvenido, " . $resultado['nombre'] . ".";
    } else {
        $error = $resultado['mensaje'];
    }
}

// Si no se envió por POST, redirigir al inicio
if (empty($_POST)) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado de Inscripción</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            width: 50%;
            margin: 0 auto;
            padding-top: 50px;
        }

        .mensaje {
            text-align: center;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .success {
            color: green;
            font-size: 18px;
            font-weight: bold;
        }

        .error {
            color: red;
            font-size: 18px;
            font-weight: bold;
        }

        .botones button {
            display: block;
            margin: 0 auto;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .botones button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Mensaje de resultado -->
        <div class="mensaje" id="mensaje" style="display:none;">
            <?php
            // Mostrar mensaje según el resultado
            if (isset($mensaje)) {
                echo "<p class='success'>" . $mensaje . "</p>";
            } elseif (isset($error)) {
                echo "<p class='error'>" . $error . "</p>";
            }
            ?>
        </div>

        <!-- Botón para regresar al índice (cursos) -->
        <div class="botones">
            <button onclick="location.href='index.php'">Regresar al índice</button>
        </div>
    </div>

    <script>
        // JavaScript para manejar la visibilidad de los mensajes
        const mensaje = "<?php echo isset($mensaje) ? $mensaje : ''; ?>";
        const error = "<?php echo isset($error) ? $error : ''; ?>";

        const mensajeDiv = document.getElementById('mensaje');

        // Si hay un mensaje de éxito, mostrarlo
        if (mensaje) {
            mensajeDiv.innerHTML = "<p class='success'>" + mensaje + "</p>";
            mensajeDiv.style.display = 'block';
        }

        // Si hay un mensaje de error, mostrarlo
        else if (error) {
            mensajeDiv.innerHTML = "<p class='error'>" + error + "</p>";
            mensajeDiv.style.display = 'block';
        }
    </script>
</body>
</html>
