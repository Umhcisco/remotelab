<?php 
 include ("headers/seguridad.php");
 include ("headers/conexion.php");

//iniciamos el buffer de salida 
ob_start();

$id_usuario = $_SESSION['id_usuario'];
$id_curso = $_SESSION['id_curso'];
$id_sesion = $_SESSION['id_sesion'];

$usuario = $_SESSION['usuario'];
$numPods = $_SESSION['numPods'];

$duracionTurno = $_SESSION['duracionTurno'];
$HorasTurno = floor($duracionTurno);
$MinutosTurno = ($duracionTurno - $HorasTurno)*60;
$numTurnosDia = $_SESSION['numTurnosDia'];

$fecha_ahora = date('Y-m-d H:i:s');
$hoy = date('Y-m-d');


///se comprueba si el usuario tiene una reserva en este turno
$sql1 = "SELECT * FROM reservas WHERE (estado_reserva = 0 OR estado_reserva = 2) AND curso_id = $id_curso AND user_id = $id_usuario ORDER BY fecha_reserva, horario_reserva ASC LIMIT 1";
$registros1 = mysql_query($sql1,$conexion) or die ("Problemas con el Select  (sql1) ".mysql_error()); 

if ($reg1 = mysql_fetch_array($registros1)){ 

	$diaReserva = $reg1['fecha_reserva'];
	$horaReserva = $reg1['horario_reserva'];
	$fecha_completa = $diaReserva." ".$horaReserva;
	$fecha_inicio_reserva = date('Y-m-d H:i:s',strtotime($fecha_completa));
	//echo "<br>fecha_inicio_reserva = $fecha_inicio_reserva";
	  
	if ($duracionTurno == floor($duracionTurno)){
	   $fecha_fin_reserva = date('Y-m-d H:i:s',strtotime('+'.$HorasTurno.' hours', strtotime($fecha_inicio_reserva)));	   
	}else{ 
	   $fecha_fin_temp = date('Y-m-d H:i:s',strtotime('+'.$HorasTurno.' hours', strtotime($fecha_inicio_reserva)));
	   $fecha_fin_reserva = date('Y-m-d H:i:s',strtotime('+'.$MinutosTurno.' minutes', strtotime($fecha_fin_temp)));	
	}
	//echo "<br>fecha_fin_reserva = $fecha_fin_reserva";

	
    if ($fecha_ahora > $fecha_inicio_reserva){
	   
	    if ($fecha_ahora < $fecha_fin_reserva){

			///se comprueba si el usuario tiene una reserva en este turno (por si entra justo en el momento en que termina el turno)
			////Por si acaso ha accedido a la VPN antes del fin de turno, y esta intentando entrar una vez iniciado el siguiente turno (de modo que la VPN ya se ha cerrado automaticamente)
			exec("/var/www/sincroniza.sh");			
			$sql00 = "SELECT * FROM radcheck WHERE username = '$usuario'";
			$registros00 = mysql_query($sql00,$conexion) or die ("Problemas con el Select  (sql00) ".mysql_error()); 
			$count00 = mysql_num_rows($registros00);
			
			if ($count00 == 0){
				mysql_free_result($registros1);
				mysql_free_result($registros00);
				mysql_close($conexion);
				ob_end_flush();

				//si se le ha acabado el turno, se le devuelve a la pagina de reservas 
				header("Location: reservas.php?errorreserva=rd"); 					
			}

		    	//echo "<br> OK!!!";
			$_SESSION['checkout']="Ok.";
			$reserva_id = $reg1['reserva_id'];
			$pod_activo = $reg1['num_POD'];
			
			$_SESSION['pod_activo'] = $pod_activo;
			$_SESSION['reserva_id'] = $reserva_id;
			$_SESSION['str_fecha_resv'] = $fecha_completa;
			$_SESSION['fecha_inicio_reserva'] = $fecha_inicio_reserva;
			$_SESSION['fecha_fin_reserva'] = $fecha_fin_reserva;
			
			$sql2 = "UPDATE logs SET reserva_id=$reserva_id, num_pod_lab=$pod_activo, acceso_lab='$fecha_ahora' WHERE log_id = $id_sesion";
			$registros2 = mysql_query($sql2,$conexion) or die ("Problemas con el Update  (sql2) ".mysql_error()); 
			
			///////SCRIPT para actualizar la base de datos RADIUS, de modo ////que me permita entrar en cualquier momento////////////////// 
			//////exec("/var/www/sincroniza.sh $pod_activo");     ///mejor llamar directamente al paro_marcha, y no de nuevo al sincroniza
			///////SCRIPT para dar corriente a los dispositivos (router & switches) asociados al pod activo
			///exec("/var/www/paro_marcha.sh $pod_activo 1");
			exec("/var/www/paro_marcha.sh RTSW$pod_activo ON");
			///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	

			///////SCRIPT para dar corriente a los PCs fisicos 2 y 3, en caso de que el pod activo sea el 2 o el 3 ////////////////////////// 
			if ($pod_activo == 2){
				//$outlet = 6;
				//exec("/var/www/paro_marcha.sh $outlet 1");
				exec("/var/www/paro_marcha.sh POD$pod_activo ON");
			}else if ($pod_activo == 3){
				//$outlet = 7;
				//exec("/var/www/paro_marcha.sh $outlet 1");
				exec("/var/www/paro_marcha.sh POD$pod_activo ON");			
			}
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
		    //Limpiamos y cerramos
			mysql_free_result($registros1);
			mysql_free_result($registros00);
	        mysql_close($conexion);
	        ob_end_flush();
	
	        //echo "<br>Acceso concedido al sistema!<br>Bienvenido, $usuario!";
			
			/////header("Location: lab_frames.php");   /// esto era para la version anterior de lab con un frame arriba para el Count-Down
            header("Location: lab.php");
			
		}else{
			//Limpiamos y cerramos
			mysql_free_result($registros1);
	        mysql_close($conexion);
	        ob_end_flush();
			
		    echo "<br>Ya se ha pasado la hora de tu reserva.";
            echo "<br>Programa otra reserva para m&aacute;s adelante.";
			
		    header("Location: reservas.php?errorreserva=te");
		}
		
	}else{
		//Limpiamos y cerramos
		mysql_free_result($registros1);
		mysql_close($conexion);
		ob_end_flush();
		
	    echo "<br>Todav&iacute;a es pronto para que accedas al sistema.";
		echo "<br>Tu pr&oacute;xima reserva est&aacute; fijada a las $horaReserva del $diaReserva";
	    
		header("Location: reservas.php?errorreserva=ta");
    }

}else{
	//Limpiamos y cerramos
	mysql_free_result($registros1);
	mysql_close($conexion);
	ob_end_flush();
		
    echo "<br>No tienes ninguna reserva activa en estos momentos, as&iacute; que no puedes acceder al LAB.";
	
	header("Location: reservas.php?errorreserva=nr");
}

?>

 <!-- <br><br><A href="reservas.php">VOLVER</A> -->
