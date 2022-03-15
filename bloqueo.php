<?php
$nombre= $_GET['user'] ?? '';
$time = $_GET['time'] ?? $_GET['espera'] ?? '';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Bloqueado</title>
</head>
<body>
<h1>Usuario <?=$nombre?> está bloqueado para acceso.</h1>
<h2>Aún te faltan <?=$time?> segundos para vover a intentarlo.</h2>
</body>
</html>
