<?php
//-----------------CODIGO PHP PARA MOSTRAR EN LA PAGINA WEB LA BASE DE DATOS-----------------

session_start();

//indicamos donde se encuentra nuestra configuracion de la base de datos
require '../config/database.php';

//traemos campo por campo. el nombre de genero se le asigna el alias genero.
//SELECT es para recuperar. AS asigna alias. 
$sqlPeliculas = "SELECT p.id, p.nombre, p.descripcion, g.nombre AS genero FROM pelicula AS p
--INNER JOIN es para juntar las dos tablas y traer solo coincidencias en ambas
INNER JOIN genero AS g ON p.id_genero=g.id";
//ejecuta el sql y lo almacena en la variable $peliculas
$peliculas = $conn->query($sqlPeliculas);

//donde los posters se van a guardar.
$dir = "posters/";

?>

<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Modal</title>

    <!-- referecia a las hojas de estilo, bajamos de carpetas con ../../ -->
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/all.min.css" rel="stylesheet">
</head>

<body class="d-flex flex-column h-100">
    <!-- pu es para generar pading -->
    <div class="container py-3">

        <h2 class="text-center">Peliculas</h2>

        <hr>
        <!-- alerta donde mostrara errores o mensajes temporal 
        isset sirve para determinar si un input esta validado y no esta vacio-->
        <?php if (isset($_SESSION['msg']) && isset($_SESSION['color'])) { ?>
            <div class="alert alert-<?= $_SESSION['color']; ?> alert-dismissible fade show" role="alert">
                <?= $_SESSION['msg']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php
            //y con unset lo borramos
            unset($_SESSION['color']);
            unset($_SESSION['msg']);
        } ?>

        <div class="row justify-content-end">
            <div class="col-auto">
                <!-- databstoggle habre un modal e indicar que ventana con el id del modal-->
                <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoModal"><i class="fa-solid fa-circle-plus"></i> Nuevo registro</a>
            </div>
        </div>

        <table class="table table-sm table-striped table-hover mt-4">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th width="45%">Descripción</th>
                    <th>Género</th>
                    <th>Poster</th>
                    <th>Acción</th>
                </tr>
            </thead>

            <tbody>
                <!-- extraemos cada una de las filas con un while -->
                <?php while ($row = $peliculas->fetch_assoc()) { ?>
                    <tr>
                        <!-- imprimimos en la columna los datos-->
                        <td><?= $row['id']; ?></td>
                        <td><?= $row['nombre']; ?></td>
                        <td><?= $row['descripcion']; ?></td>
                        <td><?= $row['genero']; ?></td>
                        <td><img src="<?= $dir . $row['id'] . '.jpg?n=' . time(); ?>" width="100"></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editaModal" data-bs-id="<?= $row['id']; ?>"><i class="fa-solid fa-pen-to-square"></i> Editar</a>

                            <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#eliminaModal" data-bs-id="<?= $row['id']; ?>"><i class="fa-solid fa-trash"></i></i> Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <?php
    //hacemos consulta a la tabla de generos para traerla en modal,
    //"selecciona el id y nombre de la tabla genero"
    $sqlGenero = "SELECT id, nombre FROM genero";
    //conexion. query es la funcion de msqly. asigna a la variable generos la consulta de sqlGeneros 
    $generos = $conn->query($sqlGenero);
    include 'nuevoModal.php'; 
    //aca se reinicia para que vuleva a retornar y vovler a hacer la consulta, sino no trae el genero cuando se edita
    $generos->data_seek(0); 
    // include es una instruccion para incluir contenido de archivo
    include 'editaModal.php'; 
    include 'eliminaModal.php';  ?>

    <script>
        // pasamos que boton presionamos
        let nuevoModal = document.getElementById('nuevoModal')
        let editaModal = document.getElementById('editaModal')
        let eliminaModal = document.getElementById('eliminaModal')
        //el nombre del evento se llama shown (la n cuando termina de cargar)
        nuevoModal.addEventListener('shown.bs.modal', event => {
            nuevoModal.querySelector('.modal-body #nombre').focus()
        })

        //para que el modal no se quede con informacion anterior, se deja vacio
        nuevoModal.addEventListener('hide.bs.modal', event => {
            nuevoModal.querySelector('.modal-body #nombre').value = ""
            nuevoModal.querySelector('.modal-body #descripcion').value = ""
            nuevoModal.querySelector('.modal-body #genero').value = ""
            nuevoModal.querySelector('.modal-body #poster').value = ""
        })

        editaModal.addEventListener('hide.bs.modal', event => {
            editaModal.querySelector('.modal-body #nombre').value = ""
            editaModal.querySelector('.modal-body #descripcion').value = ""
            editaModal.querySelector('.modal-body #genero').value = ""
            editaModal.querySelector('.modal-body #img_poster').value = ""
            editaModal.querySelector('.modal-body #poster').value = ""
        })

        editaModal.addEventListener('shown.bs.modal', event => {
            let button = event.relatedTarget //para saber cual boton presionaste
            let id = button.getAttribute('data-bs-id') //atributo para pasarle el id que quiero modificar

            //definir los elementos del formulario que se quiere editar
            let inputId = editaModal.querySelector('.modal-body #id')
            let inputNombre = editaModal.querySelector('.modal-body #nombre')
            let inputDescripcion = editaModal.querySelector('.modal-body #descripcion')
            let inputGenero = editaModal.querySelector('.modal-body #genero')
            let poster = editaModal.querySelector('.modal-body #img_poster')

            //peticion para pedir la pelicula 
            let url = "getPelicula.php"
            let formData = new FormData()
            //con el elemento formdata se pasamos el iid
            formData.append('id', id)

            //peteicion ajax desde javascrip de forma nativa
            fetch(url, {
                    method: "POST",
                    body: formData
                //then arroja como respondera una promesa al terminar
                }).then(response => response.json()) //que el response sea en json
                .then(data => { //lo que arroja json se guarda en una variable llamada data

                    //data tiene los datos del registro
                    //si la peticion es correcta se asigna con data.
                    inputId.value = data.id
                    inputNombre.value = data.nombre
                    inputDescripcion.value = data.descripcion
                    inputGenero.value = data.id_genero
                    poster.src = '<?= $dir ?>' + data.id + '.jpg' //comillas simples para rutas

                //catch captura excepciones, en este caso un error que imprimira
                }).catch(err => console.log(err))

        })

        eliminaModal.addEventListener('shown.bs.modal', event => {
            // detecta que boton se presiona
            let button = event.relatedTarget
            let id = button.getAttribute('data-bs-id')
            // en el modal, busca el id y le pasa un valor la variale id para saber q registro eliminar
            eliminaModal.querySelector('.modal-footer #id').value = id
        })
    </script>

    <script src="../../assets/js/bootstrap.bundle.min.js"></script>

</body>

</html>