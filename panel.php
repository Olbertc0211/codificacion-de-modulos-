<?php

session_start();

if (!isset($_SESSION["usuario_id"])) {

    header("Location: index.html");
    exit();

}

$nombre = $_SESSION["nombre"];
$correo = $_SESSION["correo"];
$rol = $_SESSION["rol"];

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Panel - Suplementor</title>

    <link rel="stylesheet" href="estilo.css">

    <style>

        body{

            background:#0d0d0d;

            color:white;

            font-family:Arial;

            display:flex;

            justify-content:center;

            align-items:center;

            height:100vh;

        }

        .panel{

            background:#151515;

            padding:40px;

            width:500px;

            text-align:center;

            border-radius:20px;

            border:1px solid #00ff99;

            box-shadow:0 0 30px rgba(0,255,153,.2);

        }

        .panel h1{

            color:#00ff99;

            margin-bottom:20px;

        }

        .panel p{

            margin:10px;

        }

        .panel a{

            display:block;

            margin-top:25px;

            padding:14px;

            background:#00ff99;

            color:black;

            text-decoration:none;

            border-radius:10px;

            font-weight:bold;

        }

    </style>

</head>

<body>

    <div class="panel">

        <h1>Bienvenido a Suplementor</h1>

        <p>Hola, <strong><?php echo htmlspecialchars($nombre); ?></strong></p>

        <p>Correo: <?php echo htmlspecialchars($correo); ?></p>

        <p>Rol: <?php echo htmlspecialchars($rol); ?></p>

        <a href="cerrar_sesion.php">
            Cerrar sesión
        </a>

    </div>

</body>

</html>