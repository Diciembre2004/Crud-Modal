<?php
//la variable conn, primer parametro es el host, el usuario, contraseña y nombre de base de datos. 
$conn = new mysqli("127.0.0.1", "root", "", "cinema");

//verificacion de error con connect_error
if($conn->connect_error){
    //si hay error lo terminamos y lo imprimimos con conatecion
    die("error de conexion" . $conn->connect_error);
}