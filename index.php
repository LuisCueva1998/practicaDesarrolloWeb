<?php
// Habilitar el informe de errores para la depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir archivos necesarios
require_once 'database.php';

// Función para obtener cursos
function obtenerCursos() {
    global $conexion;
    
    try {
        $stmt = $conexion->query("SELECT * FROM cursos WHERE cupos_disponibles > 0");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        // Show database error
        die("Error al obtener cursos: " . $e->getMessage());
    }
}

// Comprobar si se selecciona un curso específico
$curso_seleccionado = isset($_GET['curso']) ? intval($_GET['curso']) : null;

// Obtener Cursos
$cursos = obtenerCursos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Inscripción de Cursos</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .course-card {
            position: relative;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            background-color: #fff;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .course-card img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            object-fit: cover;
        }
        .course-card .course-content {
            padding: 10px 0;
        }
        .course-card .course-description {
            font-size: 14px;
            color: #555;
        }
        .course-card .course-details {
            margin-top: 10px;
        }
        .enroll-btn {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 16px;
            text-decoration: none;
            cursor: pointer;
            border-radius: 4px;
        }
        
        .enroll-form {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            z-index: 1000;
            width: 400px;
        }
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 999;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bienvenido a los Mejores Cursos 2024-2025</h1>
        
        <div class="course-presentation">
            <div class="course-card">
                <img src="https://www.argentina.gob.ar/sites/default/files/2023/07/servicios-de-programacion-web-a-medida.jpg" alt="Curso 1">
                <div class="course-content">
                    <h2>Curso de Programación Web</h2>
                    <p class="course-description">Aprende a crear sitios web interactivos usando HTML, CSS y JavaScript.</p>
                    <div class="course-details">
                        <span class="course-duration"><strong>Duración:</strong> 3 meses</span><br>
                        <span class="course-price"><strong>Precio:</strong> $200</span><br>
                        <span class="course-start"><strong>Fecha de Inicio:</strong> 15/01/2024</span><br>
                        <span class="course-slots"><strong>Cupos Disponibles:</strong> 10</span>
                    </div>
                </div>
                <button class="enroll-btn" onclick="openEnrollmentForm(1, 'Curso de Programación Web')">Inscribirme</button>
                <button class="view-btn" onclick="window.location.href='ver_inscritos.php?curso=1'">Ver Inscritos</button>
            </div>
            <script>
    }
</script>
            <div class="course-card">
                <img src="https://impulsapopular.com/wp-content/uploads/2023/02/5120-Cinco-tendencias-de-marketing-digital-para-el-2023.jpg" alt="Curso 2">
                <div class="course-content">
                    <h2>Curso de Marketing Digital</h2>
                    <p class="course-description">Domina el marketing online y aprende a posicionar tu marca en Internet.</p>
                    <div class="course-details">
                        <span class="course-duration"><strong>Duración:</strong> 2 meses</span><br>
                        <span class="course-price"><strong>Precio:</strong> $150</span><br>
                        <span class="course-start"><strong>Fecha de Inicio:</strong> 10/02/2024</span><br>
                        <span class="course-slots"><strong>Cupos Disponibles:</strong> 15</span>
                    </div>
                </div>
                <button class="enroll-btn" onclick="openEnrollmentForm(2, 'Curso de Marketing Digital')">Inscribirme</button>
                <button class="view-btn" onclick="window.location.href='ver_inscritos.php?curso=2'">Ver Inscritos</button>
            </div>

            <div class="course-card">
                <img src="https://escuela.teducas.com/wp-content/uploads/2022/12/Portadas-2-768x432-1.jpg" alt="Curso 3">
                <div class="course-content">
                    <h2>Curso de Diseño Gráfico</h2>
                    <p class="course-description">Aprende a diseñar gráficos impactantes utilizando herramientas profesionales.</p>
                    <div class="course-details">
                        <span class="course-duration"><strong>Duración:</strong> 4 meses</span><br>
                        <span class="course-price"><strong>Precio:</strong> $250</span><br>
                        <span class="course-start"><strong>Fecha de Inicio:</strong> 01/03/2024</span><br>
                        <span class="course-slots"><strong>Cupos Disponibles:</strong> 5</span>
                    </div>
                </div>
                <button class="enroll-btn" onclick="openEnrollmentForm(3, 'Curso de Diseño Gráfico')">Inscribirme</button>
                <button class="view-btn" onclick="window.location.href='ver_inscritos.php?curso=3'">Ver Inscritos</button>
            </div>
        </div>
    </div>

    <!-- Formulario de inscripción y superposición -->
    <div id="overlay" class="overlay"></div>
    <div id="enrollmentForm" class="enroll-form">
        <h2>Inscripción al Curso</h2>
        <form action="inscribir-curso.php" method="POST">
            <input type="hidden" id="selected-course-id" name="curso" value="">
            <div class="form-group">
                <label for="nombre">Nombre Completo:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="tel" id="telefono" name="telefono">
            </div>

            <div class="form-group">
                <label for="selected-course-name">Curso Seleccionado:</label>
                <input type="text" id="selected-course-name" readonly>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn">Inscribirme</button>
                <button type="button" class="btn btn-cancel" onclick="closeEnrollmentForm()">Cancelar</button>
            </div>
        </form>
    </div>

    <script>
        function openEnrollmentForm(courseId, courseName) {
            document.getElementById('selected-course-id').value = courseId;
            document.getElementById('selected-course-name').value = courseName;
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('enrollmentForm').style.display = 'block';
        }

        function closeEnrollmentForm() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('enrollmentForm').style.display = 'none';
        }
    </script>
</body>
</html>