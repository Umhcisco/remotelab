<?php 
 include ("headers/seguridad.php");
 include ("headers/conexion.php");

//iniciamos el buffer de salida          
//ob_start();

//Se comprueba si el usuario tiene privilegios de administrador
if ($_SESSION['admin'] == 0) { 
     session_destroy();
    //si no tiene privilegios de administrador, se devuelve a la pagina inicial 
    header("Location: index.php?errorusuario=ad"); 
} else {

	$id_usuario = $_SESSION['id_usuario'];
	$id_curso = $_SESSION['id_curso'];
	$id_sesion = $_SESSION['id_sesion'];
	$numPods = $_SESSION['numPods'];
	$offset_pod = $_SESSION['offset_pod'];

	$duracionTurno = $_SESSION['duracionTurno'];
	$HorasTurno = floor($duracionTurno);
	$MinutosTurno = ($duracionTurno - $HorasTurno)*60;
	$numTurnosDia = $_SESSION['numTurnosDia'];
	$f_ahora = date('Y-m-d H:i:s');
	$hoy = date('Y-m-d');


	//******** CODIGO PARA ANULAR TURNOS de MANTENIMIENTO **********


	if (isset($_POST['mostrar'])) {

		$mostrar_str = implode(" ", $_POST['mostrar']);// converts $_POST mostrar into a string
		$mostrar_array = explode(" ", $mostrar_str);// converts the string to an array which you can easily manipulate

		for ($i = 0; $i < count($mostrar_array); $i++) {
			echo "<br>$mostrar_array[$i]";// display the result as a string
		}

		//separamos las distintas opciones seleccionadas
		$num_turnos_mant_anulados = count($mostrar_array);	
		echo "<br>num_turnos_mant_anulados = $num_turnos_mant_anulados";
		
		for ($i=0; $i<$num_turnos_mant_anulados; $i++){
		   $j = $i + 1;
		   //echo "<br> $j &#170; Turno Mantenimiento Anulado --> $mostrar_array[$i]";
		}  
		
		if ($mostrar_array[0] == -1){
			//limpiamos y cerramos
			mysql_close($conexion);
			ob_end_flush();
			
			echo "<br>&iexcl;No tienes ning&uacute;n turno de mantenimiento activo, as&iacute; que no puedes anular nada!";
			header("Location: mantenimiento.php?errormant=ni");		
		}
		
		//deserializamos el vector $vector_outage_pod, que antes hemos serializado para pasarlo por el POST
		$vector_outage_pod = unserialize(stripslashes($_POST['pods_mant']));
		echo "<br>count(vector_outage_pod) = ".count($vector_outage_pod);
		
	}else{	
		//si no hay ninguna opcion seleccionada, limpiamos y cerramos
		mysql_close($conexion);
		ob_end_flush();
		
		echo "<br>&iexcl;No has seleccionado ningu&uacute;n turno de mantenimiento activo!";
		header("Location: mantenimiento.php?errormant=no");
	}


	/// se localizan los turnos de mantenimiento que se van a anular, para este curso
	$pod_inicial = $offset_pod + 1;
	$pod_final = $offset_pod + $numPods;
	$sql2 = "SELECT * FROM mantenimiento WHERE estado_outage = 0 AND num_POD_outage BETWEEN $pod_inicial AND $pod_final ORDER BY fecha_outage, horario_outage ASC";
	$registros2 = mysql_query($sql2,$conexion) or die ("Problemas con el Select  (sql2) ".mysql_error()); 
	$count2 = mysql_num_rows($registros2);

	if ($count2 > 0){
		echo "<br><br>Hay turnos de mantenimiento activos en el sistema";
		$orden_mant = 0;
		$contador = 0;
		
		while ($reg2 = mysql_fetch_array($registros2)){  
					
			for ($i=0; $i<$num_turnos_mant_anulados; $i++){
			
				//$j = $mostrar_array[$i];
				//$numPodMant = $vector_outage_pod[$j];
				//echo "<br>numPodMant = $numPodMant";
			  
				//echo "<br>i = $i ---> mostrar_array[$i] = $mostrar_array[$i] --> orden_mant = $orden_mant";
				
				if ($mostrar_array[$i] == $orden_mant){
								
					//echo " *** FOUND!!! ***";
					////$outage_id = $reg2['outage_id'];
					////echo "<br>outage_id = $outage_id";
					////$array_mant_id[$contador] = $outage_id;
					
					$diaMant[$contador] = $reg2['fecha_outage'];
					$horaMant[$contador] = $reg2['horario_outage'];
					$fecha_completa = $diaMant[$contador]." ".$horaMant[$contador];
					$fechaMant = date('Y-m-d H:i:s',strtotime($fecha_completa));
					//echo "<br>fechaMant = $fechaMant";
				                                                                 
					$k = $mostrar_array[$contador];
					$numPodMant[$contador] = $vector_outage_pod[$k];
					//echo "<br> ----> numPodMant[$contador] = $numPodMant[$contador]";
					
					$array_fechas_anuladas[$contador] = $fechaMant;				
					$array_fechas_fin_turno[$contador] = date('Y-m-d H:i:s',strtotime('+'.$duracionTurno.' hours', strtotime($fechaMant)));
					
					$contador++;
				}
			}
			$orden_mant++;
		}
		echo "<br>contador = $contador";
		
	}else{
		echo "<br>No hay turnos de mantenimiento activo en el sistema";
	}
													  

	/// se procede a anular estos turnos de mantenimiento

	if ($num_turnos_mant_anulados > 0){
													   
		for ($j=0; $j<$num_turnos_mant_anulados; $j++){

			//$sql3 = "DELETE FROM mantenimiento WHERE outage_id = $array_mant_id[$j] AND num_POD_outage = $numPodMant[$j]"; 
			$sql3 = "DELETE FROM mantenimiento WHERE fecha_outage = '$diaMant[$j]' AND horario_outage = '$horaMant[$j]'  AND num_POD_outage = $numPodMant[$j]"; 
			$registros3 = mysql_query($sql3,$conexion) or die ("Problemas con el Delete (sql3) ".mysql_error());
			//echo "<br>sql3 = $sql3";
			
			// contamos el numero historico de conexiones de este usuario
			$sql4="SELECT count(*) AS total FROM mantenimiento WHERE fecha_outage = '$diaMant[$j]' AND horario_outage = '$horaMant[$j]' ";
			$registros4=mysql_query($sql4,$conexion) or die ("Problemas con el select  (sql4) ".mysql_error());
			$cuenta=mysql_fetch_assoc($registros4);
			//echo "<br>Total turnos mant = ".$cuenta['total'];
			$num_pods_mant = $cuenta['total'];
			//echo "<br>num_pods_mant_restantes = $num_pods_mant";
			mysql_free_result($registros4);
				
			if ($numPods == $num_pods_mant+1){
			
				$sql5 = "DELETE FROM mensajes_temp WHERE fecha_fin_mensaje = '$array_fechas_fin_turno[$j]'"; 
				$registros5 = mysql_query($sql5,$conexion) or die ("Problemas con el Delete (sql5) ".mysql_error());
				//echo "<br>sql5 = $sql5";
			}
		}
	}

	mysql_free_result($registros2);
	mysql_close($conexion);
	//ob_end_flush();		

	header("Location: mantenimiento.php");
}
?>

<!--  <br><br><A href="mantenimiento.php">VOLVER</A>  -->