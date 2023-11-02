<?php

session_start();
//inicar una sesion para indicar errores temporales
require '../config/database.php';

$nombre = $conn->real_escape_string($_POST['nombre']);
$descripcion = $conn->real_escape_string($_POST['descripcion']);
$genero = $conn->real_escape_string($_POST['genero']);

$sql = "INSERT INTO pelicula (nombre, descripcion, id_genero, fecha_alta)
VALUES ('$nombre', '$descripcion', $genero, NOW())";
if ($conn->query($sql)) {
    $id = $conn->insert_id;
    // cuando el id se guarda correctamente, insertamos una imagen

    $_SESSION['color'] = "success";
    $_SESSION['msg'] = "Registro guardado";

    if ($_FILES['poster']['error'] == UPLOAD_ERR_OK) {
        $permitidos = array("image/jpg", "image/jpeg");
        if (in_array($_FILES['poster']['type'], $permitidos)) {

            //despues de dos validaciones, se puede guardar la imagen en la carpeta posters
            $dir = "posters";

            $info_img = pathinfo($_FILES['poster']['name']);
            $info_img['extension'];

            $poster = $dir . '/' . $id . '.jpg';

            if (!file_exists($dir)) {
                mkdir($dir, 0777); //mkdir crea, el 077 es pasarle todos los permisos
            }
            //lo guarda en una carpeta temporal para identificarlo y luego le pasa los parametros de ubicacion
            if (!move_uploaded_file($_FILES['poster']['tmp_name'], $poster)) {
                $_SESSION['color'] = "danger";
                $_SESSION['msg'] .= "<br>Error al guardar imagen";
            }
        } else {
            $_SESSION['color'] = "danger";
            $_SESSION['msg'] .= "<br>Formato de imágen no permitido";
        }
    }
} else {
    $_SESSION['color'] = "danger";
    $_SESSION['msg'] = "Error al guarda imágen";
}

header('Location: index.php');