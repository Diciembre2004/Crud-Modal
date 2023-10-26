<?php

require '../config/database.php';

$nombre = $conn->real_escape_string($_POST['nombre']); 
$descripcion = $conn->real_escape_string($_POST['descripcion']); 
$genero = $conn->real_escape_string($_POST['genero']); 

$sql = "INSERT INTO pelicula (nombre, descripcion, id_genero, fecha_alta)
VALUES ('$nombre', '$descripcion', $genero, NOW())";
if($conn->query($sql)){
    $id = $conn->insert_id;
}

header('Location: index.php');