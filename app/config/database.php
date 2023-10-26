<?php

$conn = new mysqli("127.0.0.1", "root", "", "cinema");

if($conn->connect_error){
    die("error de conexion" . $conn->connect_error);
}