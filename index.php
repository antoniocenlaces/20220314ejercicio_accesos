<?php
var_dump($_COOKIE);
$limite=3;
if (isset($_POST['f1'])) {
    echo "<p>Han pulsado el botón</p>";
    $nombre=$_POST['nombre'];
    $passwd=$_POST['passwd'];
    echo "<p>Los valores escritos en nombre: $nombre</p>";
    echo "<p>Los valores escritos en passwd: $passwd</p>";
// Cargamos los valores introducidos en formulario
    $nombre = filter_input(INPUT_POST, "nombre", FILTER_SANITIZE_STRING);
    $passwd = filter_input(INPUT_POST, 'passwd', FILTER_SANITIZE_STRING);
    if ($nombre!="") { //Un usuario ha pedido acceso, es decir el campo nombre tiene contenido
        echo "<script>alert('Han escrito $nombre en el campo input.');</script>";
        $accesos= $_COOKIE[$nombre] ?? 0; //leo el número de accesos que ya lleva este usuario si es la primera vez le asigna 0
        if ($nombre == $passwd) { //usuario identificado correctamente
            if ($accesos<$limite) { //usuario que aún tiene accesos disponibles
                setcookie($nombre,0,time()+90); //reinicializo la cookies para este usuario. contador de entradas a cero.
                header("Location:bienvenido.php?user=$nombre"); //re-enviado a la página de bienvenida
                exit();
            } else { //usuario identificado correctamente pero bloqueado al no quedarle intentos
                header("Location:bloqueo.php?user=$nombre"); //re-enviado a página de bloqueo
                exit();
            }
        } else { //$nombre tiene contenido pero $passwd incorrecta
                $accesos++; //aumento el número de accesos en 1
                $restantes=$limite-$accesos;
                setcookie($nombre,$accesos,time()+90); //guardo el nuevo valor de $accesos de este usuario
                //Aviso al usuario del número de intentos que le quedas
                echo "<script>alert('Por favor $nombre introduce una clave correcta.'+String.fromCharCode(13)+'Te quedan $restantes intentos.');</script>";
                if ($accesos>=$limite) {
                    header("Location:bloqueo.php?user=$nombre");
                    exit();
                }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="css/style.css" type="text/css" rel="stylesheet">
    <title>Access control</title>
</head>
<body>
<form action="index.php" method="POST">
    <fieldset>
        <legend>Control de acceso de usuarios</legend>
        <div class="form-block">
            <label class="form-label" for="nombre" >Nombre</label>
            <input class="form-control" type="text" name="nombre" id="nombre" value="<?=$nombre?>">
        </div>
        <div class="form-block">
            <label class="form-label" for="passwd" >Password</label>
            <input class="form-control" type="text" name="passwd" id="passwd" value="<?=$passwd?>">
        </div>
    </fieldset>
    <div class="form-block">
        <button type="submit" name="f1" class="envia">Acceder</button>
        <button type="reset"  class="borra" >Borrar</button>
    </div>

</form>
</body>
</html>
