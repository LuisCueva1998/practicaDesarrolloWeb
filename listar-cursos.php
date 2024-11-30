<?php
require_once 'database.php';

function obtenerCursos() {
    global $conexion;
    
    $stmt = $conexion->query("SELECT * FROM cursos WHERE cupos_disponibles > 0");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$cursos = obtenerCursos();
?>