<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
var_dump($_COOKIE);
// Establezco el número de intentos permitidos en $limite
$limite=3;
// Establezco el tiempo máximo de bloqueo en segundos
$tiempo=60;
// Inicializo variables con los valores de usuario
$nombre='';
$passwd='';
// Almaceno una cookie con un array ($usuarios) donde el índice es el nombre de usuario
// el valor asociado a cada usuario será otro array, con el índice 0 el valor de intentos realizados
// y el índice 1 el momento que se bloqueó el acceso a este usuario
if (isset($_POST['f1'])) { // El botón de Acceder ha sido pulsado
    echo "<p>Han pulsado el botón</p>";
    $nombre=$_POST['nombre'];
    $passwd=$_POST['passwd'];
    echo "<p>Los valores escritos en nombre: $nombre</p>";
    echo "<p>Los valores escritos en passwd: $passwd</p>";
// Cargamos los valores introducidos en formulario
    $nombre = filter_input(INPUT_POST, "nombre", FILTER_SANITIZE_STRING);
    $passwd = filter_input(INPUT_POST, 'passwd', FILTER_SANITIZE_STRING);
// Cargamos el array almacenado en cookie
    $usuarios = unserialize($_COOKIE['usuarios']) ?? [];
    echo "<p>Contenido de \$usuarios</p>";
    var_dump($usuarios);
    if ($nombre!="") { //Un usuario ha pedido acceso, es decir el campo nombre tiene contenido
        
        $accesos= $usuarios[$nombre][0] ?? 0; //leo el número de accesos que ya lleva este usuario si es la primera vez le asigna 0
        echo "<script>alert('Han escrito $nombre en el campo input. Accesos hasta ahora $accesos');</script>";
        if ($nombre == $passwd) { //usuario identificado correctamente
            if ($accesos<$limite) { //usuario que aún tiene accesos disponibles
                $usuarios[$nombre][0]=0; //reinicializo la cookies para este usuario. contador de entradas a cero. tiempo a pasado
                $usuarios[$nombre][1]=time()-$tiempo;
                $txt_usuarios = serialize($usuarios);
                setcookie('usuarios', $txt_usuarios, time() + 3600);
                header("Location:bienvenido.php?user=$nombre"); //re-enviado a la página de bienvenida
                exit();
            } else { //usuario identificado correctamente pero bloqueado al no quedarle intentos
                $blocked_time= $usuarios[$nombre][1] ?? time();
                $time=$blocked_time+$tiempo-time();
                header("Location:bloqueo.php?user=$nombre&time=$time&espera=$tiempo"); //re-enviado a página de bloqueo
                // Indicando por GET el usuario y cuantos segundos de bloqueo le quedan
                exit();
            }
        } else { //$nombre tiene contenido pero $passwd incorrecta
            // lo primero sería revisar si aún le quedan intentos
            if ($accesos<$limite) {
                $accesos++; //aumento el número de accesos en 1
                $restantes=$limite-$accesos;
                $usuarios[$nombre][0]=$accesos;
                $txt_usuarios = serialize($usuarios); //guardo el nuevo valor de $accesos de este usuario
                setcookie('usuarios', $txt_usuarios, time() + 3600);
                //Aviso al usuario del número de intentos que le quedan
                echo "<script>alert('Por favor $nombre introduce una clave correcta.'+String.fromCharCode(13)+'Te quedan $restantes intentos.');</script>";
                echo "<p>El usuario $nombre ha introducido una clave incorrecta</p>";
                echo "<p>El contenido de \$usuarios ahora</p>";
                var_dump($usuarios);
                $time=time();
                echo "<p>Tiempo actual $time</p>";
                if ($accesos>=$limite) { // ya no quedan intentos para este usuario
                    $usuarios[$nombre][1]=time(); // Escribo el momento en que sobrepasa intentos
                    $txt_usuarios = serialize($usuarios);
                    setcookie('usuarios', $txt_usuarios, time() + 3600);
                    $blocked_time= $usuarios[$nombre][1];
                    $time=$blocked_time+$tiempo-time();
                    echo "<p>Se ha superado el número de intentos para $nombre</p>";
                    echo "<p>El contenido de \$usuarios ahora</p>";
                var_dump($usuarios);
                echo "<p>Los segundos calculados $time</p>";
                   // header("Location:bloqueo.php?user=$nombre&time=$time&espera=$tiempo");
                   // exit();
                }
            } else {
                $blocked_time= $usuarios[$nombre][1];
                $time=$blocked_time+$tiempo-time();
                header("Location:bloqueo.php?user=$nombre&time=$time&espera=$tiempo");
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
            <label class="form-label" for="passwd" >Passwd</label>
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
