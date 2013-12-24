<?php 
 include ("headers/seguridad.php");
 include ("headers/conexion.php");

//iniciamos el buffer de salida 
ob_start();

//Se comprueba si el usuario tiene privilegios de administrador
if ($_SESSION['admin'] == 0) { 
     session_destroy();
    //si no tiene privilegios de administrador, se devuelve a la pagina inicial 
    header("Location: index.php?errorusuario=ad"); 
} else { 

	$id_usuario = $_SESSION['id_usuario'];   
	$id_curso = $_SESSION['id_curso'];
	$id_sesion = $_SESSION['id_sesion'];

	$usuario = $_SESSION['usuario'];
	$nombre_curso = $_SESSION['nombre_curso'];
	
	$numPods = $_SESSION['numPods'];
	$offset_pod = $_SESSION['offset_pod'];

	$duracionTurno = $_SESSION['duracionTurno'];
	$HorasTurno = floor($duracionTurno);
	$MinutosTurno = ($duracionTurno - $HorasTurno)*60;
	$numTurnosDia = $_SESSION['numTurnosDia'];
	$f_ahora = date('Y-m-d H:i:s');
	$hoy = date('Y-m-d');

	function truemod($num, $mod) {
	   return ($mod + ($num % $mod)) % $mod;
	}
	
	for ($i=0; $i<=$numPods; $i++){
		$string = "pod".$i;
		$mi_pod[$i] = $_POST[$string];
		echo "<br><br>str1 = $str1; string = $string; mi_pod[$i] = $mi_pod[$i]<br><br>";
	}
	
	
	// ****** VERIFICAMOS LOS NUMEROS DE POD SELECCIONADOS PARA LOS TURNOS DE MANTENIMIENTO ******
	// el turno 0 implicara que todos los pods entraran en mantenimiento para el intervalo considerado
	if ($mi_pod[0] == "0") {
		for ($k = 0; $k < $numPods; $k++)
			$pods[$k] = $k+1;
	}else{
		$cont = 0;
		for ($i=1; $i<=$numPods; $i++){
		
			if ($mi_pod[$i] == "$i")
			{
				$pods[$cont] = $i;
				echo "<br>pods[$cont] = $pods[$cont]";
				$cont++;
			}
		}
    }


	if (isset($pods)) {
		$size_pods = count($pods);
		echo "<br>Se han marcado $size_pods pods.";
		for ($j=0; $j<$size_pods; $j++)
			echo " En particular, el pod $pods[$j].";
	}else{
		mysql_close($conexion);
		ob_end_flush();	
		
    	echo "<br>No se ha marcado ning&uacute;n pod.";
		header("Location: mantenimiento.php?errormant=so"); 	
	}


	//******** CODIGO PARA GESTIONAR LOS TURNOS DE MANTENIMIENTO ********** 

	if (isset($_POST['opcion'])) {

		$opciones_str = implode(" ", $_POST['opcion']);// converts $_POST opcion into a string
		$opciones_array = explode(" ", $opciones_str);// converts the string to an array which you can easily manipulate

		for ($i = 0; $i < count($opciones_array); $i++) {
			echo "<br>$opciones_array[$i]";// display the result as a string
		}

		//separamos las distintas opciones seleccionadas
		$num_turnos_seleccionados = count($opciones_array);	
		echo "<br>num_turnos_seleccionados = $num_turnos_seleccionados";
		
		//for ($i=0; $i<$num_turnos_seleccionados; $i++){
		   //$j = $i + 1;
		   //echo "<br> $j &#170; Opci&oacute;n --> $opciones_array[$i]";
		//}   

	}else{
		//si no hay ninguna opcion seleccionada, limpiamos y cerramos
		mysql_close($conexion);
		ob_end_flush();

		echo "<br>&iexcl;Selecciona al menos un turno!";
		header("Location: mantenimiento.php?errormant=si"); 
	}
	
	
	//******** CODIGO PARA PASAR DE VALOR A FECHA **********
	//Hora de referencia activa -> el inicio del dia de hoy, a medianoche
	$hora_ref_activa = date('Y-m-d'." 00:00:00");

	for ($i=0; $i<$num_turnos_seleccionados; $i++){
		
		$opcion = $opciones_array[$i];
		
		$diaMant = date('Y-m-d',strtotime('+'.floor($opcion/$numTurnosDia).' days', strtotime($hora_ref_activa)));
		
		$horaInicio = date('Y-m-d H:i:s',strtotime('+'.($opcion - floor($opcion/$numTurnosDia)*$numTurnosDia)*$duracionTurno.' hours', strtotime($diaMant)));
		if ($opcion*$duracionTurno > floor($opcion*$duracionTurno)){
			 $minutosOffset = ($duracionTurno - floor($duracionTurno))*60;
			 $horaTemp = date('Y-m-d H:i:s',strtotime('+'.$minutosOffset.' minutes', strtotime($horaInicio)));
			 $horaInicio = $horaTemp;
		}
		$horaMant = date('H:i:s',strtotime('+ 0 seconds', strtotime($horaInicio)));
		$fechaMant = $horaInicio;
		//echo "<br>Opcion = $opcion  -->  fecha Mant = $fechaMant";
		//echo "<br>diaMant = $diaMant -- horaMant = $horaMant  --> fechaMant = $fechaMant";
		
		
		//******** CODIGO PARA INSERTAR NUEVOS INTERVALOS DE MANTENIMIENTO EN EL SISTEMA ********** 
		
		for ($j = 0; $j < $size_pods; $j++){
		
			$pod_activo = $pods[$j];     //vamos a asignar los intervalos de mantenimiento en cada uno de los pods seleccionados
			
			$pod_activo = $pod_activo + $offset_pod;
			
			//se comprueba si este intervalo de mantenimiento ya ha sido contabilizado en la base de datos, por si se ha recargado la pagina de mantenimiento
			$sql1 = "SELECT * FROM mantenimiento WHERE fecha_outage = '$diaMant' AND horario_outage = '$horaMant' AND num_POD_outage = $pod_activo";
			$registros1 = mysql_query($sql1,$conexion) or die ("Problemas con el Select  (sql1) ".mysql_error());
			$count1 = mysql_num_rows($registros1);
			mysql_free_result($registros1);
			//echo "<br><br>admin_id = $id_usuario ; fecha_outage = $diaMant ; horario_outage =$horaMant ; estado_outage = 0 ; num_POD_outage = $pod_activo";  
			if ($count1 == 0) {
				 $estado_outage = 0;
				 $sql2 = "INSERT INTO mantenimiento (admin_id, curso_id, fecha_outage, horario_outage, estado_outage, num_POD_outage) VALUES ($id_usuario, $id_curso, '$diaMant', '$horaMant', $estado_outage, $pod_activo) ";
				 $registros2 = mysql_query($sql2,$conexion) or die("Problemas en el Insert (sql2) ".mysql_error());
				 
			}else{
				 //echo "<br>Ya existe un intervalo de mantenimiento reserva en este turno.";
			}
			
			//finalmente se deben borrar todas las reservas activas en el sistema en los intervalos de Mantenimiento registrados
			$sql3 = "SELECT * FROM reservas WHERE fecha_reserva = '$diaMant' AND horario_reserva = '$horaMant' AND num_POD = $pod_activo";
			$registros3 = mysql_query($sql3,$conexion) or die ("Problemas con el Select  (sql3) ".mysql_error());
			$count3 = mysql_num_rows($registros3);
			
			if ($count3 > 0){			
				//////primero comprobamos si todos los pods estan activos
				
				//extraemos el codigo flag_pods_ok de este curso almacenado en mysql
				$sql10 = "SELECT * FROM cursos WHERE curso_id = $id_curso";
				$registros10 = mysql_query($sql10,$conexion) or die ("Problemas con el Select  (sql10) ".mysql_error()); 
				$count10 = mysql_num_rows($registros10);
				//echo "<br>count10 = $count10";  
				if ($reg10 = mysql_fetch_array($registros10)){ 
					$flag_pods_ok = $reg10['flag_pods_ok'];
				}else{
					//echo "<br>error en la tabla Cursos de la BB.DD. mysql";
				}
				//liberamos recursos
				mysql_free_result($registros10); 

				//metemos los estados de los diversos pods en un array, y los numeros de pod en otro array
				$max_pods = $numPods; 
				$primer_pod = $offset_pod+1;
				$tmp = $flag_pods_ok;
				for ($i=0; $i<$max_pods; $i++){
					$cociente = floor($tmp/2);
					$resto = truemod(($tmp),2);
					$pods_estado[$i] = $resto;
					$pods_numero[$i] = $offset_pod +1 +$i;
					$tmp = $cociente;
					//echo "<br>estado del pod #$pods_numero[$i] = $pods_estado[$i] : ";
					//if ($pods_estado[$i] == 1) echo "ACTIVO"; else echo "INACTIVO";
				}

				///buscamos los pods desactivados (lista negra) en este turno
				$j=0;
				for ($i=0; $i<$max_pods; $i++){							      
					if ($pods_estado[$i] == 0){ 
						$lista_negra_pods[$j] = $pods_numero[$i];
						$j++;
					}
				}
				if (isset($lista_negra_pods))
					$numPods_lista_negra = count($lista_negra_pods);
				else
					$numPods_lista_negra = 0;		
					
				$numPods_activos = $numPods - $numPods_lista_negra;
				
	
				////ahora procesamos los turnos de mantenimiento seleccionados, uno a uno
				//echo "<br>Este turno en este pod estaba ocupado";
				while ($reg3 = mysql_fetch_array($registros3)){
					$codigo_usuario = $reg3['user_id'];
						
					$sql4 = "DELETE FROM reservas WHERE fecha_reserva = '$diaMant' AND horario_reserva = '$horaMant' AND num_POD = '$pod_activo'";
					$registros4 = mysql_query($sql4,$conexion) or die ("Problemas con el Delete  (sql4) ".mysql_error());
					//echo "<br>sql4 = $sql4";
					$flag = 0;
					
					////si se ponen en mantenimiento todos los pods, no importa buscar un pod libre
					if ($size_pods < $numPods_activos){    
						$cont_off=0;					
						$str_pods = '';
						for($k=0; $k<$size_pods; $k++){
							if ($k == 0)
								$str_pods = $pods[$k];
							else
								$str_pods = $str_pods.",".$pods[$k];
							$cont_off++;
						}
						echo "<br>str_pods = $str_pods";
						
						///se obtienen el resto de pods en este intervalo, en este curso, que ya estan en mantenimiento
						$sql9 = "SELECT * FROM mantenimiento WHERE fecha_outage = '$diaMant' AND horario_outage = '$horaMant' AND num_POD_outage != $pod_activo";
						$registros9 = mysql_query($sql9,$conexion) or die ("Problemas con el Select  (sql9) ".mysql_error());
						$count9 = mysql_num_rows($registros9);
						while ($reg9 = mysql_fetch_array($registros9)){
							$pod_mant = $reg9['num_POD_outage'];
							$str_pods = $str_pods.",".$pod_mant;
							$cont_off++;
						}					
						echo "<br>str_pods = $str_pods";
					
						//se añaden los pods que estan deshabilitados (lista negra) y por tanto no pueden albergar reservas
						if (isset($lista_negra_pods)){
							for ($k=0; $k<count($lista_negra_pods); $k++){
								$str_pods = $str_pods.",".$lista_negra_pods[$k];
								$cont_off++;
							}
						}		
					
						///se obtiene el resto de reservas para este curso
						$sql7 = "SELECT * FROM reservas WHERE fecha_reserva = '$diaMant' AND horario_reserva = '$horaMant' AND num_POD NOT IN ($str_pods)";
						$registros7 = mysql_query($sql7,$conexion) or die ("Problemas con el Select  (sql7) ".mysql_error());
						$count7 = mysql_num_rows($registros7);
						$num_pods_ocupados = $count7;
						//echo "<br>num_pods_ocupados = $num_pods_ocupados";	
						
						///se comprueba si se puede resituar la reserva borrada en otro pod libre en el mismo intervalo
						$num_pods_libres = $numPods - $cont_off - $num_pods_ocupados;
						//echo "<br>num_pods_libres = $numPods - $size_pods - $num_pods_ocupados = $num_pods_libres";
						if ($num_pods_libres > 0){
							//mysql_data_seek ( $registros7, 0);   //devolvemos el puntero a la posicion 0 de la lista
							//if ($reg7=mysql_fetch_array($registros7)){  
							$k=0;
							while ($reg7=mysql_fetch_array($registros7)){
								$vector_pods_ocupados[$k] = $reg7['num_POD'];
								$k++;
							}
							if (isset($vector_pods_ocupados)){
								for($j=0; $j<count($vector_pods_ocupados); $j++){
									$str_pods = $str_pods.",".$vector_pods_ocupados[$j];
								}
							}
							
							$array_pods_ocupados = explode(",", $str_pods);
							//echo "<br>ORIGINAL:"; 
							//for ($i=0; $i<count($array_pods_ocupados); $i++)  
								//echo "<br>array_pods_ocupados[$i] = $array_pods_ocupados[$i]";
							sort($array_pods_ocupados);
							//echo "<br>ORDENADO";
							//for ($i=0; $i<count($array_pods_ocupados); $i++)  
								//echo "<br>array_pods_ocupados[$i] = $array_pods_ocupados[$i]";
								
							for($i=0;$i<$numPods;$i++){
								if ($flag==0){
									if($array_pods_ocupados[$i]==($i+1)){
										//no hacer nada
									}else{
										$flag=($i+1);
									}
								}
							}			
							
							if ($flag > 0){
								$nuevo_pod = $flag;
								//echo "<br>nuevo_pod = $nuevo_pod";
																						
								////una vez que hemos conseguido un nuevo pod, le reasignamos el turno cuyo pod acabamos de poner en mantenimiento
								$estado_reserva = 0;
								
								$sql8= "INSERT INTO reservas (user_id, curso_id, fecha_reserva, horario_reserva, num_POD, estado_reserva) VALUES ($codigo_usuario, $id_curso, '$diaMant', '$horaMant', $nuevo_pod, $estado_reserva)";
								$registros8 = mysql_query($sql8,$conexion) or die("Problemas en el Insert (sql8) ".mysql_error());

							}else{
								//echo "<br>No quedan pods libres";
							}
							
						}
						mysql_free_result($registros7);	
						mysql_free_result($registros9);	
					}

					/////////enviamos un email a los usuarios a los cuales se les ha anulado una reserva al sobreponer un turno de mantenimiento sobre una reserva anterior////
					///////// y no ha sido posible resituarlos en otro pod dentro del mismo intervalo temporal de forma transparente al usuario
					if ($flag==0){
						//echo "<br><br>send email!!!";
							
						$sql33 = "SELECT * FROM datos_pers WHERE user_id = $codigo_usuario";
						$registros33 = mysql_query($sql33,$conexion) or die ("Problemas con el Select  (sql33) ".mysql_error());
						$count33 = mysql_num_rows($registros33);
						
						if ($count33 > 0){
							$diaRsv = substr($diaMant,8,2);
							$mesRsv = substr($diaMant,5,2);
							$anyoRsv = substr($diaMant,0,4);
							$horaRsv = substr($horaMant,0,2);
							
							while ($reg33 = mysql_fetch_array($registros33)){
								$destinatario = $reg33['email'];						
								$nombre = $reg33['nombre'];

							
								$asunto = "Anulación de Reserva";
								
								$cuerpo = '
								<html>
								<head>
								<title>Anulaci&oacute;n de Reserva</title>
								</head>
								<body>
								<h3>Hola '.$nombre.'!</h3>
								<p>
								<b>Se ha anulado tu Reserva para el curso '.$nombre_curso.'</b>
								</p>
								<p>
								<b>para la fecha '.$diaRsv.'/'.$mesRsv.'/'.$anyoRsv.' y con inicio a las '.$horaRsv.':00 horas.</b>
								</p>
								<p>
								<i>Rogamos nos disculpes por las inconveniencias causadas.</i>
								</p>
								Saludos cordiales,
								<p>________________<p>
								<b>RemoteLab.UMH</b>
								</body>
								</html>
								';
								
								//Envío en formato HTML
								$headers = "MIME-Version: 1.0\r\n";
								$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
					
								//Dirección del remitente
								$headers .= "From: Administrador < ".$nuestro_email.">\r\n";
					
								//Dirección de respuesta (Puede ser una diferente a la de pepito@mydomain.com)
								$headers .= "Reply-To: ".$nuestro_email."\r\n";
					
								$booleano = mail($destinatario,$asunto,$cuerpo,$headers);
					
								echo "<br>booleano = $booleano";
								if ($booleano)
									echo "<br> Email enviado";
								else
									echo "<br> Email fallido";
							}
						}
						mysql_free_result($registros33);
					}	
					///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				}	
			
			}	
			mysql_free_result($registros3);	

		}

		
		//se comprueba si este intervalo de mantenimiento ha sido completado con esta adicion de pods al turno de mantenimiento en cuestion
		$sql5 = "SELECT * FROM mantenimiento WHERE fecha_outage = '$diaMant' AND horario_outage = '$horaMant'";
		$registros5 = mysql_query($sql5,$conexion) or die ("Problemas con el Select  (sql5) ".mysql_error());
		$count5 = mysql_num_rows($registros5);
		$num_pods_mant_actualizado = $count5;
		mysql_free_result($registros5);
		//echo "<br><br>num_pods_mant_actualizado = $num_pods_mant_actualizado --";
		//echo "numPods = $numPods -- size_pods = $size_pods";
			
		////si se han deshabilitado todos los pods para cierto intervalo, se manda un mensaje temporal para avisar
		////ya sea porque se han deshabilitado todos de una vez, o porque la suma de estos y los anteriores dan el total de pods
		if (($size_pods == $numPods) || ($num_pods_mant_actualizado == $numPods)){         
				
			$texto_msg = "Turno de Mantenimiento: $diaMant a las $horaMant";
			$fecha_fin_turno = date('Y-m-d H:i:s',strtotime('+'.$duracionTurno.' hours', strtotime($fechaMant)));
			$fecha_fin_msg = $fecha_fin_turno;
			//$num_dias_aviso = 3;
			//$fecha_fin_msg = date('Y-m-d',strtotime('+'.$num_dias_aviso.' days', strtotime($hora_ref_activa)));
			echo "<br>fecha_fin_msg = $fecha_fin_msg";
			
			$sql6 = "INSERT INTO mensajes_temp (admin_id, curso_id, fecha_fin_mensaje, texto_mensaje) VALUES ($id_usuario, $id_curso, '$fecha_fin_msg', \"$texto_msg\") ";
			$registros6 = mysql_query($sql6,$conexion) or die("Problemas en el Insert (sql6) ".mysql_error());
		}
	}
	
	mysql_close($conexion);
	ob_end_flush();		

	header("Location: mantenimiento.php");
}
?>

<!--    <br><br><A href="mantenimiento.php">VOLVER</A>   -->