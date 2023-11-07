<?php

require '../config/database.php';

//limpieza del id
$id = $conn->real_escape_string($_POST['id']);

//cuando el id sea igual que el id sea igual que el recibo. LIMIT 1 para que no siga  buscando en la tabla
$sql = "SELECT id, nombre, descripcion, id_genero FROM pelicula WHERE id=$id LIMIT 1";
//el resultado es igual a la ejecuccion de este query
$resultado = $conn->query($sql);
//verfiicamos que resutados trajo, si trae filas
$rows = $resultado->num_rows;

//arreglo, trae un json para retornarlo se necesita un arreglo
$pelicula = [];

//validar si trae informacion
if ($rows > 0) {
    $pelicula = $resultado->fetch_array();
}

//retornamos la pelicula, que traiga acentos
echo json_encode($pelicula, JSON_UNESCAPED_UNICODE);