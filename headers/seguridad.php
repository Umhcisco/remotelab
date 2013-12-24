<?php
//El servidor considerará que cerró el navegador cuando hasta la última ventana sea abandonada, ya sea porque el usuario la cerró o porque fue hacia otro sitio que no es el nuestro.
//Entonces, para que la sesión caduque al cerrar el navegador, habrá que, por un lado, forzar al php.ini a que propague la sesión solamente en cookies y por otro lado, asignarle a ésta, una duración cero.
//En este caso vamos a cambiar la configuracion del php.ini desde este script 
ini_set("session.use_only_cookies","1");
ini_set("session.use_trans_sid","0");


//iniciamos la sesión 
session_name("loginUsuario"); 
session_cache_limiter('nocache,private_no_expire');
//Ahora solo restará, cambiar el parámetro de duración a la cookie de la sesión. Con lo cual estaremos indicando una duración de 0 (cero) segundos. Esto significará que durará hasta que termine el script.
session_set_cookie_params(0, "/", $_SERVER["HTTP_HOST"], 0);
session_start(); 

//antes de hacer los cálculos, compruebo que el usuario está logueado 
//utilizamos el mismo script que antes 
if ($_SESSION["autentificado"] != "SI.") { 
    //si no está logueado lo envío a la página de autentificación 
    header("Location: index.php?errorusuario=au"); 
} else { 
    //sino, calculamos el tiempo transcurrido 
    $fechaGuardada = $_SESSION["ultimoAcceso"]; 
    $ahora = date("Y-n-j H:i:s"); 
    $tiempo_transcurrido = (strtotime($ahora)-strtotime($fechaGuardada)); 

    //comparamos el tiempo transcurrido 
    if($tiempo_transcurrido >= 7200) {       // 600 -> 10 minutos ;; 7200 -> 2 horas
      //si pasaron 10 minutos o más  ;; (2 horas como maximo)
      session_destroy(); // destruyo la sesión 
      header("Location: index.php?errorusuario=to"); //envío al usuario a la pag. de autenticación 
      //sino, actualizo la fecha de la sesión 
    }else { 
      $_SESSION["ultimoAcceso"] = $ahora; 
    } 
} 

?> 