<?php 
 include ("headers/seguridad.php");
 include ("headers/conexion.php");

//iniciamos el buffer de salida 
ob_start();


//codigos de salida -->  0:fin del lab, 1:boton de salir, -1:timeout por inactividad, 2:boton de salida ADMIN, -2:timeout ADMIN, nada:cierre abrupto de la sesion (se ha colgado)
if (isset($_POST['salida_volunt'])){
   $exit = $_POST['salida_volunt'];
   echo "<br>salida_volunt = $exit";
}
if (isset($_POST['salida_timeout'])){
   $timeout = $_POST['salida_timeout'];
   echo "<br>salida_timeout = $timeout"; 
} 
if (isset($_POST['salida_timeup'])){
   $timeup= $_POST['salida_timeup'];
   echo "<br>salida_timeup = $timeup";
}

 
if (!(isset($exit)) && !(isset($timeout))){
   if (isset($_SESSION['checkout'])){
        //la sesion ha acabado por finalizacion del turno en el lab
        $codigo_salida = (int) $timeup;    //$timeup;
		//$codigo_salida = 0;
   }else{
        //alguien intenta acceder directamente a esta pagina -> es expulsado
		header("Location: index.php?errorusuario=au");
   }	
}else if (!(isset($exit))){
    //la sesion ha acabado por timeout debido a inactividad durante un intervalo de idle_timeout
	$codigo_salida = (int) $timeout;
}else if (!(isset($timeout))){
    //la sesion ha acabado voluntariamente al pulsar el boton de SALIR
	$codigo_salida = (int) $exit;
}else{
    //si se produce un cierre repentino de la sesion, no deberia poner nada, asi que dejamos la opcion por defecto (NULL)
	$codigo_salida = NULL;
}
//echo "<br>codigo_salida = $codigo_salida";
   

$id_sesion = $_SESSION['id_sesion'];
$usuario = $_SESSION['usuario'];
$fecha_ahora = date('Y-m-d H:i:s');
$hoy = date('Y-m-d');

echo "<br>fecha_ahora = $fecha_ahora";
echo "<br>id_sesion = $id_sesion";


$sql1 = "UPDATE logs SET codigo_salida=$codigo_salida, salida='$fecha_ahora' WHERE log_id = $id_sesion";
echo "<br>sql1 = $sql1";
$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el Update  (sql1) ".mysql_error());



 //<SCRIPT TYPE="text/javascript">
   ////cerrar y abrir la sesion php e incluir esto en el BODY de un HTML para borrar completamente la cache
   //document.execCommand('ClearAuthenticationCache');
 //</SCRIPT>
 


echo "<br>";
//var_dump($codigo_salida); die;

//salida del sistema y vuelta a la pagina inicial
if ( $codigo_salida == 0 )
{
    echo "<br>Lab Timeup!!!";
	echo "<br>codigo_salida = $codigo_salida";
	echo "<br>Location: http://".$host.$uri."/index.php";
	echo "<br>";
    //var_dump($codigo_salida); die;
	
	mysql_close($conexion);
	ob_end_flush();	

	//la sesion ha acabado por finalizacion del turno en el lab
	session_destroy();
	session_unset();
	header("Location: http://".$host."/index.php?errorusuario=fi");
}
else if ( $codigo_salida > 0 )
{
    echo "<br>Voluntary Exit!!!";
	echo "<br>codigo_salida = $codigo_salida";
	echo "<br>Location: http://".$host."/index.php";
	echo "<br>";
	//var_dump($codigo_salida); die;
	
	mysql_close($conexion);
	ob_end_flush();	

    //la sesion ha acabado voluntariamente al pulsar el boton de SALIR
	session_destroy();
	session_unset();
    header("Location: http://".$host."/index.php?errorusuario=sa");
}
else if ( $codigo_salida < 0 )
{
    echo "<br>Idle Timeout!!!";
	echo "<br>codigo_salida = $codigo_salida";
	echo "<br>Location: http://".$host."/index.php";
	echo "<br>";
	//var_dump($codigo_salida); die;
	
	mysql_close($conexion);
	ob_end_flush();	

    //la sesion ha acabado por timeout debido a inactividad durante un intervalo de idle_timeout
	session_destroy();
	session_unset();
	
	header("Location: http://".$host."/index.php?errorusuario=to");
	//echo "<script> window.location.replace('index.php?errorusuario=to') </script>";
}
else         
{
    echo "<br>Else!!!";
	echo "<br>codigo_salida = $codigo_salida";
	echo "<br>Location: http://".$host."/index.php";
	echo "<br>";
	var_dump($codigo_salida); die;

	mysql_close($conexion);
	ob_end_flush();	
    //si se produce un cierre repentino de la sesion, no deberia poner nada, asi que dejamos la opcion por defecto (NULL) 
	//$codigo_salida = NULL;
	session_destroy();
	session_unset();
    header("Location: http://".$host."/index.php");
}

?>