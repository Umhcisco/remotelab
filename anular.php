<?php 
 include ("headers/seguridad.php");
 include ("headers/conexion.php");

//iniciamos el buffer de salida 
ob_start();

$id_usuario = $_SESSION['id_usuario'];
$id_curso = $_SESSION['id_curso'];
$id_sesion = $_SESSION['id_sesion'];

$duracionTurno = $_SESSION['duracionTurno'];
$HorasTurno = floor($duracionTurno);
$MinutosTurno = ($duracionTurno - $HorasTurno)*60;
$numTurnosDia = $_SESSION['numTurnosDia'];
$f_ahora = date('Y-m-d H:i:s');
$hoy = date('Y-m-d');
$hora = date('H:i:s');

$numMaxCanc = $_SESSION['numMaxCanc'];
echo "numMaxCanc = $numMaxCanc";


//******** CODIGO PARA ANULAR RESERVAS **********


if (isset($_POST['mostrar'])) {

    $mostrar_str = implode(" ", $_POST['mostrar']);// converts $_POST mostrar into a string
    $mostrar_array = explode(" ", $mostrar_str);// converts the string to an array which you can easily manipulate

	for ($i = 0; $i < count($mostrar_array); $i++) {
		echo "<br>$mostrar_array[$i]";// display the result as a string
	}

    //separamos las distintas opciones seleccionadas
    $num_reservas_anuladas = count($mostrar_array);	
	echo "<br>num_reservas_anuladas = $num_reservas_anuladas";
	
	for ($i=0; $i<$num_reservas_anuladas; $i++){
	   $j = $i + 1;
       echo "<br> $j &#170; Reserva Anulada --> $mostrar_array[$i]";
	}  
	
	if ($mostrar_array[0] == -1){
	    //limpiamos y cerramos
        mysql_close($conexion);
		ob_end_flush();
		
		echo "<br>&iexcl;No tienes ninguna reserva activa, as&iacute; que no puedes anular nada!";
	    header("Location: reservas.php?errorreserva=ni");		
	}
}else{	
    //si no hay ninguna opcion seleccionada, limpiamos y cerramos
	mysql_close($conexion);
	ob_end_flush();
	
	echo "<br>&iexcl;No has seleccionado ninguna reserva activa!";
	header("Location: reservas.php?errorreserva=no");
}


//se comprueba si se ha superado el limite de cancelaciones permitido
$sql1 = "SELECT count(cancel_resv) AS total FROM log_detalles WHERE user_id = $id_usuario";
$registros1 = mysql_query($sql1,$conexion) or die ("Problemas con el Select  (sql1) ".mysql_error()); 
$cuenta1 = mysql_fetch_assoc($registros1);
$num_cancelaciones = $cuenta1['total'];
echo "<br>num_cancelaciones = $num_cancelaciones";

if ($num_cancelaciones > $numMaxCanc){
    //limpiamos y cerramos
	mysql_free_result($registros1);
	mysql_close($conexion);
	ob_end_flush();
	
	echo "<br>Ya has superado el l&iacute;mite de cancelaciones permitido";
    header("Location: reservas.php?errorreserva=na");	
}	


/// se localizan las reservas que se van a anular

$sql2 = "SELECT * FROM reservas WHERE (estado_reserva = 0 OR estado_reserva = 2) AND user_id = $id_usuario ORDER BY fecha_reserva, horario_reserva ASC";
$registros2 = mysql_query($sql2,$conexion) or die ("Problemas con el Select  (sql2) ".mysql_error()); 
$count2 = mysql_num_rows($registros2);

if ($count2 > 0){
	echo "<br><br>Hay reservas en el sistema";
	$orden_reserva = 0;
	$contador = 0;
	
	while ($reg2 = mysql_fetch_array($registros2)){  
		
		for ($i=0; $i<$num_reservas_anuladas; $i++){
		  
		    echo "<br>i = $i ---> mostrar_array[$i] = $mostrar_array[$i] --> orden_reserva = $orden_reserva";
			
			if ($mostrar_array[$i] == $orden_reserva){
				echo " *** FOUND!!! ***";
				$reserva_id = $reg2['reserva_id'];
				echo "<br>reserva_id = $reserva_id";
				$array_reservas_id[$contador] = $reserva_id;
				
				$diaReserva = $reg2['fecha_reserva'];
				$horaReserva = $reg2['horario_reserva'];
				$fecha_completa = $diaReserva." ".$horaReserva;
				$fechaReserva = date('Y-m-d H:i:s',strtotime($fecha_completa));
				echo "<br>fechaReserva = $fechaReserva";

				
				//// se localizan las reservas que han sido usadas durante este turno, para que no se puedan anular
				$estado_reserva = $reg2['estado_reserva'];
				if (($contador == 0) && ($estado_reserva == 2)){
					if ( ($hoy == $diaReserva) && ($hora > $horaReserva) ){
						
						echo "<br><br> hoy = $hoy \n diaReserva = $diaReserva";
						echo "<br><br>hora = $hora \n horaReserva = $horaReserva";
						
						//limpiamos y cerramos
						mysql_free_result($registros1);
						mysql_free_result($registros2);
						mysql_close($conexion);
						ob_end_flush();
						
						echo "<br>No est&aacute; permitido cancelar una reserva que ya se ha ejecutado.";
						header("Location: reservas.php?errorreserva=ne");
					}
				}

				
				$array_fechas_anuladas[$contador] = $fechaReserva;
				$contador++;
			}
	    }
		$orden_reserva++;
    }
    echo "<br>contador = $contador";
	
}else{
    echo "<br>No hay reservas en el sistema";
}
                                                  

/// se procede a anular esas reservas 

if ($num_reservas_anuladas > 0){

    /// se procede a anular las reservas seleccionadas
	
    for ($j=0; $j<$num_reservas_anuladas; $j++){

	    $sql3 = "DELETE FROM reservas WHERE reserva_id = $array_reservas_id[$j]"; 
		$registros3 = mysql_query($sql3,$conexion) or die ("Problemas con el Delete (sql3) ".mysql_error());

		$fecha_anulada = $array_fechas_anuladas[$j];
		$sql4 = "INSERT INTO log_detalles (log_id, curso_id, user_id, cancel_resv) VALUES ($id_sesion, $id_curso, $id_usuario, '$fecha_anulada')";
		$registros4 = mysql_query($sql4,$conexion) or die ("Problemas con el Insert (sql4) ".mysql_error());
	}
}

//liberamos recursos
mysql_free_result($registros1);
mysql_free_result($registros2);
mysql_close($conexion);
ob_end_flush();		

header("Location: reservas.php");
?>

<!--  <br><br><A href="reservas.php">VOLVER</A>  -->