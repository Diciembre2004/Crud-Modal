<?php
//inicar una sesion para indicar errores temporales
session_start();
//llamamos a la conexion a la base de datos
require '../config/database.php';

//obtenemos con POST el campo de la tabla con forma real_string (para evitar inyecciones de codigo lo limpiamos) 
//y lo guardamos en la variable nombre. 
$nombre = $conn->real_escape_string($_POST['nombre']);
$descripcion = $conn->real_escape_string($_POST['descripcion']);
$genero = $conn->real_escape_string($_POST['genero']);

//le insertamos las variables que tuvimos con POST. Comillas simples son cadenas de texto.
$sql = "INSERT INTO pelicula (nombre, descripcion, id_genero, fecha_alta)
VALUES ('$nombre', '$descripcion', $genero, NOW())"; //NOW toma fecha y hora del servidor mysql

//VALIDACION
if ($conn->query($sql)) { 
    $id = $conn->insert_id;
    // cuando el id se guarda correctamente, insertamos una imagen

    $_SESSION['color'] = "success";
    $_SESSION['msg'] = "Registro guardado";

    //verificamos que el campo no este vacio con la variable  global FILES
    if ($_FILES['poster']['error'] == UPLOAD_ERR_OK) { 
        $permitidos = array("image/jpg", "image/jpeg"); //validar formatos de imagen

        //calidar dentro del arreglo
        if (in_array($_FILES['poster']['type'], $permitidos)) {

            //despues de dos validaciones, se puede guardar la imagen en la carpeta posters
            $dir = "posters";

            //pathinfo trae la informacion como el nombre, ruta etc y lo guarda en la variable info_iimg
            $info_img = pathinfo($_FILES['poster']['name']);
            $info_img['extension']; //para acceder a la extension de la img

            //en la variable se guarda la ruta y el nombre de como se guardara, guardada con id del registro
            $poster = $dir . '/' . $id . '.jpg';

            //despues de la verificacion, creamos carpeta y guardamos
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

//header redirecciona hacia la locacion: index.
header('Location: index.php');