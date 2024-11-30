<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include necessary files
require_once 'database.php';

// Get the selected course ID
$curso_id = isset($_GET['curso']) ? intval($_GET['curso']) : 0;

if ($curso_id == 0) {
    die("Curso no válido.");
}

// Function to get the users enrolled in the selected course
function obtenerUsuariosInscritos($curso_id) {
    global $conexion;
    
    try {
        $stmt = $conexion->prepare("SELECT u.nombre_completo, u.email, u.telefono, i.fecha_inscripcion
                                    FROM inscripciones i
                                    JOIN usuarios u ON i.id_usuario = u.id_usuario
                                    WHERE i.id_curso = :curso_id");
        $stmt->bindParam(':curso_id', $curso_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        die("Error al obtener usuarios inscritos: " . $e->getMessage());
    }
}

// Obtain enrolled users for the selected course
$usuarios_inscritos = obtenerUsuariosInscritos($curso_id);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios Inscritos</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* General body styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
            color: #333;
        }

        /* Container for the content */
        .container {
            width: 80%;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Page title */
        h1 {
            font-size: 2rem;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
        }

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            font-size: 16px;
        }

        th {
            background-color: #4CAF50;
            color: white;
            font-weight: 600;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        /* Message for no users */
        p {
            text-align: center;
            font-size: 1.2rem;
            color: #666;
        }

        /* Button for back or any other actions */
        .back-btn {
            display: block;
            width: 150px;
            margin: 30px auto;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            text-align: center;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            table {
                width: 100%;
            }

            th, td {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Usuarios Inscritos</h1>

        <?php if (count($usuarios_inscritos) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nombre Completo</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Fecha de Inscripción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios_inscritos as $usuario): ?>
                        <tr>
                            <td><?= htmlspecialchars($usuario['nombre_completo']) ?></td>
                            <td><?= htmlspecialchars($usuario['email']) ?></td>
                            <td><?= htmlspecialchars($usuario['telefono']) ?></td>
                            <td><?= htmlspecialchars($usuario['fecha_inscripcion']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay usuarios inscritos en este curso.</p>
        <?php endif; ?>

        <!-- Optional back button -->
        <a href="index.php" class="back-btn">Volver</a>
    </div>
</body>
</html>
