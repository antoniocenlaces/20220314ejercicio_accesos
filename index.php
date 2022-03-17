<?php
// error_reporting(E_ALL);
// ini_set('display_errors',1);
// Establezco el número de intentos permitidos en $limite
$limite=3;
// Establezco el tiempo máximo de bloqueo en segundos
$tiempo=60;
// Inicializo variables con los valores de usuario
$nombre='';
$passwd='';
// Almaceno una cookie con un array ($usuarios) donde el índice es el nombre de usuario
// el valor asociado a cada usuario será otro array, con el índice 0 el valor de intentos realizados
// y el índice 1 el momento que se bloqueó el acceso a este usuario, o cero si no bloqueado
if (isset($_POST['f1'])) { // El botón de Acceder ha sido pulsado
// Cargamos los valores introducidos en formulario
    $nombre = filter_input(INPUT_POST, "nombre", FILTER_SANITIZE_STRING);
    $passwd = filter_input(INPUT_POST, 'passwd', FILTER_SANITIZE_STRING);
// Cargamos el array almacenado en cookie
    $usuarios = unserialize($_COOKIE['usuarios']) ?? [];
    if ($nombre!="") { //Un usuario ha pedido acceso, es decir el campo nombre tiene contenido        
        $accesos= $usuarios[$nombre][0] ?? 0; //leo el número de accesos que ya lleva este usuario si es la primera vez le asigna 0
        $tiempo_bloqueo= $usuarios[$nombre][1] ?? 0; //leo si hay un tiempo de bloqueo para este usuario
        $accesos++; // aumento los accesos, pues han enviado un nombre y accesos inicia en 0
        if ($tiempo_bloqueo==0) { // el usuario no está bloqueado
            if ($accesos<$limite) { // el usuario tiene accesos disponibles
                if ($nombre == $passwd) { // usuario habilitado para entrar
                    $usuarios[$nombre][0]=0; //reinicializo la cookies para este usuario. contador de entradas a cero. tiempo a cero
                    $usuarios[$nombre][1]=0;
                    $txt_usuarios = serialize($usuarios);
                    setcookie('usuarios', $txt_usuarios, time() + 3600);
                    header("Location:bienvenido.php?user=$nombre"); //re-enviado a la página de bienvenida
                    exit();
                } else { // passwd incorrecta. mensaje de intentos restantes y vuelve a inicio
                    $restantes=$limite-$accesos;
                    $usuarios[$nombre][0]=$accesos;
                    $txt_usuarios = serialize($usuarios); //guardo el nuevo valor de $accesos de este usuario
                    setcookie('usuarios', $txt_usuarios, time() + 3600);
                    //Aviso al usuario del número de intentos que le quedan
                    echo "<script>alert('Por favor $nombre introduce una clave correcta.'+String.fromCharCode(13)+'Te quedan $restantes intentos.');</script>";
                }
            } else { // se ha llegado al último intento sin éxito. bloqueo al usuario
                    $usuarios[$nombre][0]=0;
                    $usuarios[$nombre][1]=time(); // Escribo el momento en que sobrepasa intentos
                    $txt_usuarios = serialize($usuarios);
                    setcookie('usuarios', $txt_usuarios, time() + 3600);
                    $blocked_time= $usuarios[$nombre][1];
                    $time=$blocked_time+$tiempo-time();                    
                    header("Location:bloqueo.php?user=$nombre&time=$time&espera=$tiempo");
                    exit();
            }
        } else { // el usuario está bloqueado. vamos a comprobar si ya ha expirado el tiempo de bloqueo
            if (time()>($tiempo_bloqueo+$tiempo)) { // se ha superado el tiempo de bloqueo
                $usuarios[$nombre][0]=0;
                $usuarios[$nombre][1]=0;
                $tiempo_bloqueo=0;
                $txt_usuarios = serialize($usuarios);
                setcookie('usuarios', $txt_usuarios, time() + 3600);
                if ($nombre == $passwd) { //usuario identificado correctamente. este if se ejecuta siempre que haya un nombre escrito en el input
                    // se utiliza para que si después de un bloqueo se introduce nombre y passwd correctos entre directamente
                        // En este caso las cookies ya se han establecido en alguno de los if de arriba
                        header("Location:bienvenido.php?user=$nombre"); //re-enviado a la página de bienvenida
                        exit();
                    } else {
                        $accesos=1; // ha terminado bloqueo y este intento cuenta
                        $restantes=$limite-$accesos;
                        $usuarios[$nombre][0]=$accesos;
                        $txt_usuarios = serialize($usuarios); //guardo el nuevo valor de $accesos de este usuario
                        setcookie('usuarios', $txt_usuarios, time() + 3600);
                        //Aviso al usuario del número de intentos que le quedan
                        echo "<script>alert('Por favor $nombre introduce una clave correcta.'+String.fromCharCode(13)+'Te quedan $restantes intentos.');</script>";
                    }
            } else { // aún sigue bloqueado
                $time=$tiempo_bloqueo+$tiempo-time();
                header("Location:bloqueo.php?user=$nombre&time=$time&espera=$tiempo"); //re-enviado a página de bloqueo
                // Indicando por GET el usuario y cuantos segundos de bloqueo le quedan
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
            <input class="form-control" type="text" name="nombre" id="nombre">
        </div>
        <div class="form-block">
            <label class="form-label" for="passwd" >Passwd</label>
            <input class="form-control" type="text" name="passwd" id="passwd">
        </div>
    </fieldset>
    <div class="form-block">
        <button type="submit" name="f1" class="envia">Acceder</button>
        <button type="reset"  class="borra" >Borrar</button>
    </div>

</form>
</body>
</html>
