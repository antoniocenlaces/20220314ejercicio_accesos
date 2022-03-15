<?php
$nombre= $_GET['user'] ?? '';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Bienvenido</title>
</head>
<body>
<h1>Bienvenido <?=$nombre?> a la página del ejercicio</h1>
<form>
    <div class="form-block">
        <button type="" ><a href="index.php">Volver a identificación</a></button>
    </div>
</form>
</body>
</html>
