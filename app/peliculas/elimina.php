<?php

session_start();

require '../config/database.php';

$id = $conn->real_escape_string($_POST['id']);

//borra de la tabla peliculas cuando el id ea igual al recibido
$sql = "DELETE FROM pelicula WHERE id=$id";
if ($conn->query($sql)) {

    $dir = "posters";
    $poster = $dir . '/' . $id . '.jpg';

    if (file_exists($poster)) { //en caso de que exista el archivo, lo eliminamos
        unlink($poster);
    }

    $_SESSION['color'] = "success";
    $_SESSION['msg'] = "Registro eliminado";
    } else {
    $_SESSION['color'] = "danger";
    $_SESSION['msg'] = "Error al eliminar registro";
}
//e caso de ser correcto, se regresa al index
header('Location: index.php');