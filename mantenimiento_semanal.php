<?php 
 include ("headers/seguridad.php");
 include ("headers/conexion.php");

//iniciamos el buffer de salida 
ob_start();

$admin = $_SESSION['admin'];
$id_curso = $_SESSION['id_curso'];
$idle_timeout = $_SESSION['idle_timeout'];
$hoy = date('Y-m-d');
$ahora = date('Y-m-d H:i:s');


//Se comprueba si el usuario tiene privilegios de administrador
if ($_SESSION['admin'] == 0) { 
     session_destroy();
    //si no tiene privilegios de administrador, se devuelve a la pagina inicial 
    header("Location: index.php?errorusuario=ad"); 
} else { 

	//si los datos seleccionados son correctos, se procede al cambio de los parametros para el mantenimiento semanal
	if (isset($_POST['flag'])){
		//echo "<br>CAMBIADO!!!!!!!!!!";
		
		$flag = $_POST['flag'];
		
		$hora_inicio_mant_semanal_nueva = $_POST['hora_inicio'];
		$duracion_mant_semanal_nueva = $_POST['duracion'];

		$curso_id_mant = $_POST['curso_id_mant'];
		
		$radio_dia = $_POST['radio_dia'];
		if ($radio_dia == 1){
			$sql1 = "SELECT * FROM cursos WHERE curso_id = $curso_id_mant";
			$registros1 = mysql_query($sql1,$conexion) or die ("Problemas con el Select  (sql1) ".mysql_error()); 
			$count1 = mysql_num_rows($registros1);
			if ($count1 > 0){
				while ($reg1=mysql_fetch_array($registros1)){
					$dia_mant_semanal_anterior = $reg1['dia_mant_semanal'];
					//echo "<br>dia_mant_semanal_anterior = $dia_mant_semanal_anterior";
					$dia_mant_semanal_nuevo = $dia_mant_semanal_anterior;
				}
			}else{
				echo "<br>Error en la tabla cursos!";
			}
			mysql_free_result($registros1);
			
			$dia_mant_semanal_nuevo = $dia_mant_semanal_anterior;
		}else  if ($radio_dia == 2)
			$dia_mant_semanal_nuevo = $_POST['otro_dia'];
		else
			echo "Error en la asignaci&oacute;n del d&iacute;a";
		
		//echo "<br>dia_mant_semanal_nuevo = $dia_mant_semanal_nuevo;<br> hora_inicio_mant_semanal_nueva=$hora_inicio_mant_semanal_nueva;<br> duracion_mant_semanal_nueva =$duracion_mant_semanal_nueva;<br> curso_id_mant =$curso_id_mant <br> radio_dia = $radio_dia";
		//echo "<br>curso_id_mant = $curso_id_mant";
		
		if ($curso_id_mant > 0){
			///se actualiza la tabla de cursos, para reflejar el cambio fijado para el intervalo de mantenimiento semanal
			$sql2 = "UPDATE cursos SET dia_mant_semanal=$dia_mant_semanal_nuevo, hora_inicio_mant_semanal=$hora_inicio_mant_semanal_nueva, duracion_mant_semanal=$duracion_mant_semanal_nueva WHERE curso_id = $curso_id_mant";
			$registros2 = mysql_query($sql2,$conexion) or die ("Problemas con el Update  (sql2) ".mysql_error());
	
			//if ($curso_id_mant == $id_curso){
				//$_SESSION['dia_mant_semanal'] = $dia_mant_semanal_nuevo;	
				//$_SESSION['hora_inicio_mant_semanal'] = $hora_inicio_mant_semanal_nueva;	
				//$_SESSION['duracion_mant_semanal'] = $duracion_mant_semanal_nueva;	
				//$a=$_SESSION['dia_mant_semanal']; $b=$_SESSION['hora_inicio_mant_semanal']; $c=$_SESSION['duracion_mant_semanal'];
				//echo "<br>dia_mant_semanal=$a <br>hora_inicio_mant_semanal=$b <br>duracion_mant_semanal=$c";
				//echo "<br>S_SESSION['duracion_mant_semanal'] = ".$_SESSION['duracion_mant_semanal'];
			//}
			
			///////////////////// borrado de las reservas que habia en los nuevos turnos de mantenimiento semanal ////////////////////////
			////codigo para fijar los lunes, el inicio de las semanas naturales
			$dia_sem = date('w', strtotime($hoy));
			//echo "<br>dia_sem = $dia_sem";
			////semana actual
			$dias_distancia = $dia_sem - 1;
			$lunes_ref = date('Y-m-d',strtotime('-'.$dias_distancia.' days', strtotime($hoy)));
			$domingo_ref = date('Y-m-d',strtotime('+6 days', strtotime($lunes_ref)));
			
			$dia_mant = date('Y-m-d H:i:s',strtotime('+'.($dia_mant_semanal_nuevo -1).' days', strtotime($lunes_ref)));
			//echo "<br>dia_mant = $dia_mant";
			$hora_mant_inicial = date('Y-m-d H:i:s',strtotime('+'.$hora_inicio_mant_semanal_nueva.' hours', strtotime($dia_mant)));	
			//echo "<br><br>hora_mant_inicial = $hora_mant_inicial";

			$dia_mant_start = date('Y-m-d', strtotime($hora_mant_inicial));
			$hora_mant_start = date('H:i:s', strtotime($hora_mant_inicial));

			$num_turnos_mant = ($duracion_mant_semanal_nueva/2);
			//echo "<br>num_turnos_mant = $num_turnos_mant";
			
			for ($n=0; $n<$num_turnos_mant; $n++)
			{
				//se calcula el dia y la hora de cada uno de los turnos de mantenimiento implicados
				$var = 2*$n;
				$fechaMant = date('Y-m-d H:i:s',strtotime('+'.$var.' hours', strtotime($hora_mant_inicial)));
				$diaMant = date('Y-m-d', strtotime($fechaMant));
				$horaMant = date('H:i:s', strtotime($fechaMant));
				
				//finalmente se deben borrar todas las reservas activas en el sistema en los intervalos de Mantenimiento registrados
				$sql3 = "SELECT * FROM reservas WHERE fecha_reserva = '$diaMant' AND horario_reserva = '$horaMant' AND curso_id = $curso_id_mant";
				$registros3 = mysql_query($sql3,$conexion) or die ("Problemas con el Select  (sql3) ".mysql_error());
				$count3 = mysql_num_rows($registros3);
				//echo "<br>sql3 = $sql3";
				
				if ($count3 > 0){
					//echo "<br>Este turno en este pod estaba ocupado";
					while ($reg3 = mysql_fetch_array($registros3)){
						$codigo_usuario = $reg3['user_id'];
							
						$sql4 = "DELETE FROM reservas WHERE fecha_reserva = '$diaMant' AND horario_reserva = '$horaMant' AND curso_id = $curso_id_mant";
						$registros4 = mysql_query($sql4,$conexion) or die ("Problemas con el Delete  (sql4) ".mysql_error());
						//echo "<br>sql4 = $sql4";
						$flag = 0;

						/////////enviamos un email a los usuarios a los cuales se les ha anulado una reserva al sobreponer un turno de mantenimiento sobre una reserva anterior////
						///////// y no ha sido posible resituarlos en otro pod dentro del mismo intervalo temporal de forma transparente al usuario
						if ($flag==0){
							echo "<br><br>send email!!!";
							//mysql_data_seek ( $registros3, 0);   //devolvemos el puntero a la posicion 0 de la lista
							//while ($reg3 = mysql_fetch_array($registros3)){
							//		$codigo_usuario = $reg3['user_id'];
							//		
							//		$sql33 = "SELECT * FROM datos_pers WHERE user_id = $codigo_usuario";
							//		$registros33 = mysql_query($sql33,$conexion) or die ("Problemas con el Select  (sql33) ".mysql_error());
							//		$count33 = mysql_num_rows($registros33);
							//		
							//		if ($count33 > 0){
							//			
							//			while ($reg33 = mysql_fetch_array($registros33)){
							//					$destinatario = $reg33['email'];						
							//					$nombre = $reg33['nombre'];
							//			}		
							//			mysql_free_result($registros33);	
							//
							//			$asunto = "Anulaci&oacute;n de Reserva";
							//			
							//			$cuerpo = '
							//			<html>
							//			<head>
							//			<title>Anulaci&oacute;n de Reserva</title>
							//			</head>
							//			<body>
							//			<h1>Hola '.$nombre.'!</h1>
							//			<p>
							//			<b>Se ha anulado tu Reserva para el curso '.$nombre_curso.'</b>
							//			</p>
							//			<p>
							//			<b>para con fecha $diaMant con inicio a las $horaMant</b>
							//			</p>
							//			<p>
							//			<i>Rogamos nos disculpes por las inconveniencias causadas.</i>
							//			</p>
							//			Saludos cordiales,
							//			</body>
							//			</html>
							//			';
							//			
							//			//Envío en formato HTML
							//			$headers = "MIME-Version: 1.0\r\n";
							//			$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
							//
							//			//Dirección del remitente
							//			$headers .= "From: Administrador < ".$nuestro_email.">\r\n";
							//
							//			//Dirección de respuesta (Puede ser una diferente a la de pepito@mydomain.com)
							//			$headers .= "Reply-To: ".$nuestro_email."\r\n";
							//
							//			$booleano = mail($destinatario,$asunto,$cuerpo,$headers);
							//
							//			echo "<br>booleano = $booleano";
							//			if ($booleano)
							//				echo "<br> Email enviado";
							//			else
							//				echo "<br> Email fallido";
							//		}
							//}
							///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
						}

					}	
				
				}	
				mysql_free_result($registros3);	
				
			}
		
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		}else{    //if ($curso_id_mant==0)
		
			//num.cursos en ejecucion
			$sql0 = "SELECT * FROM cursos WHERE curso_activo = 1 AND inicio_curso <= CURDATE() AND fin_curso >= CURDATE() AND num_max_pods > 0";
			$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el Select (sql0) ".mysql_error());
			$num_cursos_en_ejecucion=mysql_num_rows($registros0);       //numero de cursos total
			//echo "<br>Num.cursos en ejecucion = $num_cursos_en_ejecucion";			

			if ($num_cursos_en_ejecucion > 0){
				$cont = 0;
				while ($reg0 = mysql_fetch_array($registros0)){
					$curso_id_outage[$cont] = $reg0['curso_id'];
					$cont++;
				}
				for ($k=0; $k<$num_cursos_en_ejecucion; $k++){
					///se actualiza la tabla de cursos, para reflejar el cambio fijado para el intervalo de mantenimiento semanal
					$sql2 = "UPDATE cursos SET dia_mant_semanal=$dia_mant_semanal_nuevo, hora_inicio_mant_semanal=$hora_inicio_mant_semanal_nueva, duracion_mant_semanal=$duracion_mant_semanal_nueva WHERE curso_id = $curso_id_outage[$k]";
					$registros2 = mysql_query($sql2,$conexion) or die ("Problemas con el Update  (sql2) ".mysql_error());	

					//if ($curso_id_outage[$k] == $id_curso){
					//	$_SESSION['dia_mant_semanal'] = $dia_mant_semanal_nuevo;	
					//	$_SESSION['hora_inicio_mant_semanal'] = $hora_inicio_mant_semanal_nueva;	
					//	$_SESSION['duracion_mant_semanal'] = $duracion_mant_semanal_nueva;	
					//}					
				}
				
				///////////////////// borrado de las reservas que habia en los nuevos turnos de mantenimiento semanal ////////////////////////

				////codigo para fijar los lunes, el inicio de las semanas naturales
				$dia_sem = date('w', strtotime($hoy));
				//echo "<br>dia_sem = $dia_sem";
				////semana actual
				$dias_distancia = $dia_sem - 1;
				$lunes_ref = date('Y-m-d',strtotime('-'.$dias_distancia.' days', strtotime($hoy)));
				$domingo_ref = date('Y-m-d',strtotime('+6 days', strtotime($lunes_ref)));
				
				$dia_mant = date('Y-m-d H:i:s',strtotime('+'.($dia_mant_semanal_nuevo -1).' days', strtotime($lunes_ref)));
				//echo "<br>dia_mant = $dia_mant";
				$hora_mant_inicial = date('Y-m-d H:i:s',strtotime('+'.$hora_inicio_mant_semanal_nueva.' hours', strtotime($dia_mant)));	
				//echo "<br><br>hora_mant_inicial = $hora_mant_inicial";

				$dia_mant_start = date('Y-m-d', strtotime($hora_mant_inicial));
				$hora_mant_start = date('H:i:s', strtotime($hora_mant_inicial));

				$num_turnos_mant = ($duracion_mant_semanal_nueva/2);
				//echo "<br>num_turnos_mant = $num_turnos_mant";
				
				
				for ($k=0; $k<$num_cursos_en_ejecucion; $k++){
					for ($n=0; $n<$num_turnos_mant; $n++)
					{
						//se calcula el dia la hora de cada uno de los turnos de mantenimiento implicados
						$var = 2*$n;
						$fechaMant = date('Y-m-d H:i:s',strtotime('+'.$var.' hours', strtotime($hora_mant_inicial)));
						$diaMant = date('Y-m-d', strtotime($fechaMant));
						$horaMant = date('H:i:s', strtotime($fechaMant));
						
						//finalmente se deben borrar todas las reservas activas en el sistema en los intervalos de Mantenimiento registrados
						$sql3 = "SELECT * FROM reservas WHERE fecha_reserva = '$diaMant' AND horario_reserva = '$horaMant' AND curso_id = $curso_id_outage[$k]";
						$registros3 = mysql_query($sql3,$conexion) or die ("Problemas con el Select  (sql3) ".mysql_error());
						$count3 = mysql_num_rows($registros3);
						
						if ($count3 > 0){
							//echo "<br>Este turno en este pod estaba ocupado";
							while ($reg3 = mysql_fetch_array($registros3)){
								$codigo_usuario = $reg3['user_id'];
									
								$sql4 = "DELETE FROM reservas WHERE fecha_reserva = '$diaMant' AND horario_reserva = '$horaMant' AND curso_id = $curso_id_outage[$k]";
								$registros4 = mysql_query($sql4,$conexion) or die ("Problemas con el Delete  (sql4) ".mysql_error());
								//echo "<br>sql4 = $sql4";
								$flag = 0;

								/////////enviamos un email a los usuarios a los cuales se les ha anulado una reserva al sobreponer un turno de mantenimiento sobre una reserva anterior////
								///////// y no ha sido posible resituarlos en otro pod dentro del mismo intervalo temporal de forma transparente al usuario
								if ($flag==0){
									echo "<br><br>send email!!!";
									//mysql_data_seek ( $registros3, 0);   //devolvemos el puntero a la posicion 0 de la lista
									//while ($reg3 = mysql_fetch_array($registros3)){
									//		$codigo_usuario = $reg3['user_id'];
									//		
									//		$sql33 = "SELECT * FROM datos_pers WHERE user_id = $codigo_usuario";
									//		$registros33 = mysql_query($sql33,$conexion) or die ("Problemas con el Select  (sql33) ".mysql_error());
									//		$count33 = mysql_num_rows($registros33);
									//		
									//		if ($count33 > 0){
									//			
									//			while ($reg33 = mysql_fetch_array($registros33)){
									//					$destinatario = $reg33['email'];						
									//					$nombre = $reg33['nombre'];
									//			}		
									//			mysql_free_result($registros33);	
									//
									//			$asunto = "Anulaci&oacute;n de Reserva";
									//			
									//			$cuerpo = '
									//			<html>
									//			<head>
									//			<title>Anulaci&oacute;n de Reserva</title>
									//			</head>
									//			<body>
									//			<h1>Hola '.$nombre.'!</h1>
									//			<p>
									//			<b>Se ha anulado tu Reserva para el curso '.$nombre_curso.'</b>
									//			</p>
									//			<p>
									//			<b>para con fecha $diaMant con inicio a las $horaMant</b>
									//			</p>
									//			<p>
									//			<i>Rogamos nos disculpes por las inconveniencias causadas.</i>
									//			</p>
									//			Saludos cordiales,
									//			</body>
									//			</html>
									//			';
									//			
									//			//Envío en formato HTML
									//			$headers = "MIME-Version: 1.0\r\n";
									//			$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
									//
									//			//Dirección del remitente
									//			$headers .= "From: Administrador < ".$nuestro_email.">\r\n";
									//
									//			//Dirección de respuesta (Puede ser una diferente a la de pepito@mydomain.com)
									//			$headers .= "Reply-To: ".$nuestro_email."\r\n";
									//
									//			$booleano = mail($destinatario,$asunto,$cuerpo,$headers);
									//
									//			echo "<br>booleano = $booleano";
									//			if ($booleano)
									//				echo "<br> Email enviado";
									//			else
									//				echo "<br> Email fallido";
									//		}
									//}
									///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
								}

							}	
						}
					}	
				}
				mysql_free_result($registros0);
				mysql_free_result($registros3);	
				
			}
		
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		}
	}
	
	//se procede a la anulacion del intervalo de mantenimiento semanal programado
	if (isset($_POST['anular_mant'])){
		//echo "<br>ANULADO!!!!!!!!!!";
		$curso_anular_mant = $_POST['curso_anular_mant'];
		//echo "<br>curso_anular_mant = $curso_anular_mant";
		
		if ($curso_anular_mant > 0){
			///se actualiza la tabla de cursos, para reflejar el cambio fijado para el intervalo de mantenimiento semanal, en el curso elegido
			$sql1 = "UPDATE cursos SET dia_mant_semanal=-1, hora_inicio_mant_semanal=-1, duracion_mant_semanal=-1 WHERE curso_id = $curso_anular_mant";
			$registros1 = mysql_query($sql1,$conexion) or die ("Problemas con el Update  (sql1) ".mysql_error());
		
			if ($id_curso == $curso_anular_mant){
				//$_SESSION['dia_mant_semanal'] = -1;	
				//$_SESSION['hora_inicio_mant_semanal'] = -1;	
				//$_SESSION['duracion_mant_semanal'] = -1;	
			}
		}else{      //if ($curso_id_mant==0)
		
			//num.cursos en ejecucion
			$sql0 = "SELECT * FROM cursos WHERE curso_activo = 1 AND inicio_curso <= CURDATE() AND fin_curso >= CURDATE() AND num_max_pods > 0";
			$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el Select (sql0) ".mysql_error());
			$num_cursos_en_ejecucion=mysql_num_rows($registros0);       //numero de cursos total
			//echo "<br>Num.cursos en ejecucion = $num_cursos_en_ejecucion";			

			if ($num_cursos_en_ejecucion > 0){
				$cont = 0;
				while ($reg0 = mysql_fetch_array($registros0)){
					$curso_id_outage[$cont] = $reg0['curso_id'];
					$cont++;
				}
				for ($k=0; $k<$num_cursos_en_ejecucion; $k++){
					///se actualiza la tabla de cursos, para reflejar el cambio fijado para el intervalo de mantenimiento semanal
					$sql2 = "UPDATE cursos SET dia_mant_semanal=-1, hora_inicio_mant_semanal=-1, duracion_mant_semanal=-1 WHERE curso_id = $curso_id_outage[$k]";
					$registros2 = mysql_query($sql2,$conexion) or die ("Problemas con el Update  (sql2) ".mysql_error());	

					//if ($curso_id_outage[$k] == $id_curso){
						//$_SESSION['dia_mant_semanal'] = -1;	
						//$_SESSION['hora_inicio_mant_semanal'] = -1;	
						//$_SESSION['duracion_mant_semanal'] = -1;	
					//}					
				}
			}else{
				echo "<br>No hay cursos en ejecuci&oacute;n en estos momentos.";
			}
			
			mysql_free_result($registros0);
		}
	}	
	


	////////////////////////////////////////////////////
	////////pagina inicial///sacamos todos los cursos///
	///datos por cursos de experto
	$sql0 = "SELECT * FROM cursos WHERE curso_activo = 1 AND inicio_curso <= CURDATE() AND fin_curso >= CURDATE() AND num_max_pods > 0 AND nombre_curso LIKE '%Experto%'";
	$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el Select (sql0) ".mysql_error());
	$num_cursos_en_ejecucion=mysql_num_rows($registros0);       //numero de cursos total
	//echo "<br><br>Num.cursos en ejecuci&oacute;n = $num_cursos_en_ejecucion";
	
	$j = 0;
	while ($reg0 = mysql_fetch_array($registros0))
	{
		$curso_id[$j] =  $reg0['curso_id'];
		$nombre_curso[$j] = $reg0['nombre_curso'];
		$edicion[$j] = $reg0['edicion'];

		////parametros para el outbreak semanal (mantenimiento semanal)
		$dia_mant_semanal[$j] = $reg0['dia_mant_semanal'];
		$hora_inicio_mant_semanal[$j] = $reg0['hora_inicio_mant_semanal'];
		$duracion_mant_semanal[$j] = $reg0['duracion_mant_semanal'];
		
		$curso_cont[$j]=$j;
		$j++;
	}

	///datos por cursos no experto
	$sql00 = "SELECT * FROM cursos WHERE curso_activo = 1 AND inicio_curso <= CURDATE() AND fin_curso >= CURDATE() AND num_max_pods > 0 AND nombre_curso NOT LIKE '%Experto%'";
	$registros00=mysql_query($sql00,$conexion) or die ("Problemas con el Select (sql00) ".mysql_error());
	$num_cursos_en_ejecucion+=mysql_num_rows($registros00);       //numero de cursos total
	//echo "<br><br>Num.cursos en ejecuci&oacute;n = $num_cursos_en_ejecucion";
	
	while ($reg00 = mysql_fetch_array($registros00))
	{
		$curso_id[$j] =  $reg00['curso_id'];
		$nombre_curso[$j] = $reg00['nombre_curso'];
		$edicion[$j] = $reg00['edicion'];

		////parametros para el outbreak semanal (mantenimiento semanal)
		$dia_mant_semanal[$j] = $reg00['dia_mant_semanal'];
		$hora_inicio_mant_semanal[$j] = $reg00['hora_inicio_mant_semanal'];
		$duracion_mant_semanal[$j] = $reg00['duracion_mant_semanal'];
		
		$curso_cont[$j]=$j;
		$j++;
	}

	//for ($i=0; $i<$num_cursos_en_ejecucion; $i++)
		//echo "<br>curso_id[$i] = $curso_id[$i] -- nombre_curso[$i] = $nombre_curso[$i] -- edicion[$i] = $edicion[$i] ** dia_mant_semanal[$i] = $dia_mant_semanal[$i] -- hora_inicio_mant_semanal[$i] = $hora_inicio_mant_semanal[$i] -- duracion_mant_semanal[$i] = $duracion_mant_semanal[$i] ** curso_cont[$i] = $curso_cont[$i]";
	
	////implode --> convierte array en cadena
	$str_curso_id = implode(' ',$curso_id);
	//echo "<br>str_curso_id = $str_curso_id";
	$str_dia_mant_semanal = implode(' ',$dia_mant_semanal);
	//echo "<br>str_dia_mant_semanal = $str_dia_mant_semanal";
	
	////explode --> convierte cadena en array
	$array_curso_id = explode(' ',$str_curso_id);
	//for ($i=0; $i<count($array_curso_id); $i++)
	//	echo "<br>array_curso_id[$i] = $array_curso_id[$i]";	
		
	/////////////////////////
	$dia_sem_hoy = date('w', strtotime($hoy));
	if ($dia_sem_hoy == 0)
		$dia_sem_hoy = 7;
	//echo "<br>dia_sem_hoy = $dia_sem_hoy";
	//echo"<br>";
	////Hora de referencia activa -> el inicio del dia de hoy, a medianoche
	$hora_ref_activa = date('Y-m-d'." 00:00:00");
	$dias_distancia = $dia_sem_hoy - 1;
	$lunes_ref = date('Y-m-d',strtotime('-'.$dias_distancia.' days', strtotime($hora_ref_activa)));
	$domingo_ref = date('Y-m-d',strtotime('+6 days', strtotime($lunes_ref)));
	//echo "<br>$lunes_ref *** $domingo_ref";
	//echo"<br>";	
	$dia_sem_lunes_ref = date('w', strtotime($lunes_ref));
	$dia_sem_domingo_ref = date('w', strtotime($domingo_ref));
	if ($dia_sem_domingo_ref == 0)
		$dia_sem_domingo_ref = 7;
	//echo "<br>dia_sem_lunes_ref = $dia_sem_lunes_ref -- dia_sem_domingo_ref = $dia_sem_domingo_ref";
	//echo"<br>";	
	$hora_ahora = date('H', strtotime($ahora));
	$inicio_turno_ahora = floor($hora_ahora/2)*2;
	//echo "<br>inicio_turno_ahora = $inicio_turno_ahora";
	////////////////////////
		
	if ($num_cursos_en_ejecucion > 0)
	{
		for ($i=0; $i<$num_cursos_en_ejecucion; $i++)
		{			
			if ($dia_mant_semanal[$i] > 0){
				//echo "<br><br>i=$i ** dia_mant_semanal[$i] = $dia_mant_semanal[$i] -- hora_inicio_mant_semanal[$i] = $hora_inicio_mant_semanal[$i] -- duracion_mant_semanal[$i] = $duracion_mant_semanal[$i]";
				
				$dias = $dia_mant_semanal[$i]-1;
				//echo "<br>dias = $dias";
				$dia_mantenimiento[$i] = date('Y-m-d H:i:s',strtotime('+'.$dias.' days', strtotime($lunes_ref)));
				//echo "<br>dia_mantenimiento[$i] = $dia_mantenimiento[$i]";
				
				$horas = $hora_inicio_mant_semanal[$i];
				//echo "<br>horas = $horas";
				$hora_mantenimiento_inicial[$i] = date('Y-m-d H:i:s',strtotime('+'.$horas.' hours', strtotime($dia_mantenimiento[$i])));	
				//echo "<br>hora_mantenimiento_inicial[$i] = $hora_mantenimiento_inicial[$i]";
				
				$duracion = $duracion_mant_semanal[$i];
				//echo "<br>duracion = $duracion";
				$hora_mantenimiento_final[$i] = date('Y-m-d H:i:s',strtotime('+'.$duracion.' hours', strtotime($hora_mantenimiento_inicial[$i])));	
				//echo "<br>hora_mantenimiento_final[$i] = $hora_mantenimiento_final[$i]";

				if ($dia_mant_semanal[$i] == 1)
					$dia_semana[$i] = "LUNES";
				else if ($dia_mant_semanal[$i] == 2)
					$dia_semana[$i] = "MARTES";
				else if ($dia_mant_semanal[$i] == 3)
					$dia_semana[$i] = "MI&Eacute;RCOLES";
				else if ($dia_mant_semanal[$i] == 4)
					$dia_semana[$i] = "JUEVES"; 
				else if ($dia_mant_semanal[$i] == 5)
					$dia_semana[$i] = "VIERNES";
				else if ($dia_mant_semanal[$i] == 6)
					$dia_semana[$i] = "S&Aacute;BADOS";
				else if ($dia_mant_semanal[$i] == 7)
					$dia_semana[$i] = "DOMINGOS"; 
				else
					echo "<br>Error en el d&iacute;a de la semana almacenado en mysql";

				//echo "<br>dia_mant_semanal[$i] = $dia_mant_semanal[$i]";
				//echo "<br>dia_semana[$i] = $dia_semana[$i]";
				
				$hora_inicio[$i] = date('H:i:s', strtotime($hora_mantenimiento_inicial[$i]));
				$hora_fin[$i] = date('H:i:s', strtotime($hora_mantenimiento_final[$i]));
			}else{
				$hora_inicio[$i] = -1;
				$hora_fin[$i] = -1;
			}
		}
	}		  


	/////////////////////////
	
	
?>

	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
	<html version="-//W3C//DTD XHTML 1.1//EN"
		  xmlns="http://www.w3.org/1999/xhtml" xml:lang="en"
		  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
		  xsi:schemaLocation="http://www.w3.org/1999/xhtml
							  http://www.w3.org/MarkUp/SCHEMA/xhtml11.xsd"
	>

	<head>
	 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	 <title>MANTENIMIENTO SEMANAL</title>
 	 <!-- Estas lineas evitan que la pagina deseada no se cargue en la cache y no pueda ser mostrada cuando se regresa atras. -->
	  <meta http-equiv="Expires" CONTENT="0">
	  <meta http-equiv="Cache-Control" CONTENT="no-cache">
	  <meta http-equiv="Pragma" CONTENT="no-cache">
	
	  <script type="text/javascript" language="javascript">
		function deshabilitar_dia() {  
		   document.getElementById("otro_dia").disabled = true;
		   document.getElementById("otro_dia").value = '';
		}

		function habilitar_dia() {  
		   document.getElementById("otro_dia").disabled = false;
		}	 

		function validar(){
			var flag = 1;
			if (document.getElementById("curso_id_mant").value == '-1'){
				alert("Falta el curso");
				flag = 0;
			} 
			
			if ((document.getElementById("otro_dia").disabled == false ) && (flag == 1)){
				if (document.getElementById("otro_dia").value == '-1' ){
					alert("Falta el d\u00eda de la semana");
					flag = 0;
				}
			}	

			if ((document.getElementById("radio_mismo").checked) && (flag == 1)){
				if (document.getElementById("curso_id_mant").value == '0'){
						alert("Selecciona un d\u00eda de la semana para todos los cursos");
						flag = 0;				
				}else{     //if(document.getElementById("curso_id_mant").value > '0')
					var str_curso_id = "<?php echo $str_curso_id ?>";
					var str_dia_mant_semanal = "<?php echo $str_dia_mant_semanal ?>";
					//alert("str_curso_id = " + str_curso_id + "\nstr_dia_mant_semanal = " + str_dia_mant_semanal);
					var array_curso_id = str_curso_id.split(" ");
					var array_dia_mant_semanal = str_dia_mant_semanal.split(" ");	
					//for (x=0; x<array_curso_id.length; x++)
					//	alert("array_curso_id["+x+"] = " + array_curso_id[x] + "\narray_dia_mant_semanal["+x+"] = " + array_dia_mant_semanal[x]);
					var curso_id = document.getElementById("curso_id_mant").value;
					//alert("curso_id = " + curso_id + "\narray_curso_id.length = " + array_curso_id.length);
					for (x=0; x<array_curso_id.length; x++)
						if (curso_id.toString() == array_curso_id[x]) 
							indice = x;
					//alert("indice = " + indice);
					//alert("array_dia_mant_semanal["+indice+"] = " + array_dia_mant_semanal[indice]);
					if (array_dia_mant_semanal[indice] == '-1'){
						alert("No hay un turno de mantenimiento establecido para este curso\n\nSelecciona un d\u00eda de la semana");
						flag = 0;
					}
				}
			}
			
			if ((document.getElementById("hora_inicio").value == '-1') && (flag == 1)){
				alert("Falta la hora de inicio");
				flag = 0;
			} 			
			
			if ((document.getElementById("duracion").value == '-1') && (flag == 1)){
				alert("Falta la duraci\u00f3n");
				flag = 0;
			} 		

			if (flag == 1){
				document.getElementById('flag').value = 1;
				return (true)
			}else{
				return (false);
			}		
		}
		
		function anular()
		{
		    if (document.getElementById("curso_id_mant").value == '-1'){
			    alert("Falta el curso");
			    return (false);
		    }
			else if (document.getElementById("curso_id_mant").value == '0'){
				alert("\u00a1Se anulan los turnos de mantenimiento semanal \nde todos los cursos en ejecuci\u00f3n!");
			}
			else
				alert("\u00a1Se anulan los turnos de mantenimiento semanal \ndel curso seleccionado!");
		    var temp = document.getElementById("curso_id_mant").value;
		    document.getElementById("curso_anular_mant").value = temp;
		    document.getElementById("anular_mant").value = '-1';	
		    var tmp = document.getElementById("anular_mant").value;
		    //alert("anular_mant = " + tmp + "\ncurso_anular_mant = " + temp);
		    document.forms["form_anular_mant"].submit();
			//return (true);
		}
		
	  </script>
  
	  <style type="text/css">
        .bb
        {
            border-bottom: solid 1px black;
        }
		
		.bl
        {
            border-left: solid 1px black;
        }
	  </style>

	</head>


	<body bgcolor="#<?php echo $bgcolor ?>"> 
	
	<center>
	  <u><h1>MANTENIMIENTO SEMANAL ACTUAL</h1></u>  
	
		<table style="background: #ccffff" border="1" cellpadding="10">
		    <font style="width:300; background: #ccffff; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:14pt; font-weight:bold;  border: 1px solid #ccc;"> 
					<tr>
						<th bgcolor="#edc951"> Curso en ejecuci&oacute;n</th>    <!-- $nombre_curso - $edicion &ordm; edici&oacute;n -->
						<th bgcolor="khaki"> D&iacute;a de Mantenimiento Semanal</th>
						<th bgcolor="khaki"> Hora de Inicio del Mant.Semanal</th>
						<th bgcolor="khaki"> Hora de Fin del Mant.Semanal</th>
					</tr>

			    <?php for ($i=0; $i<$num_cursos_en_ejecucion; $i++){?>				
					<tr>
						<td bgcolor="#fcfcfc" align="center"> &nbsp;&nbsp;&nbsp; <?php if ($edicion[$i] == 0) echo "$nombre_curso[$i]"; else echo "$nombre_curso[$i] - $edicion[$i]&ordm; edici&oacute;n";  ?> &nbsp;&nbsp;&nbsp;  </td>
						<td bgcolor="#fcfcfc" align="center"> <?php if ($dia_mant_semanal[$i] > 0) echo "Todos los $dia_semana[$i]"; else echo '-'; ?> </td>
						<td bgcolor="#fcfcfc" align="center"> <?php if ($hora_inicio[$i] >= 0) echo $hora_inicio[$i]; else echo '-'; ?> </td>
						<td bgcolor="#fcfcfc" align="center"> <?php if ($hora_fin[$i] > 0) echo $hora_fin[$i]; else echo '-'; ?> </td>
					</tr>
				<?php } ?>
			</font>
		</table>
	</center>
	
	<br>
	<br>
	
	<center>
	  <u><h1>CAMBIAR EL MANTENIMIENTO SEMANAL</h1></u>   
	
		<table border="0" width="100%">
		  <form name="form_cambiar" ID="form_cambiar" action="mantenimiento_semanal.php" method="POST" onSubmit="return validar();">
		    <tr>
			    <td width="23%" align="center" valign="top">				
			    </td>
		        <td align="center">
  
					<table style="border:1px solid black" cellpadding="10">
						<font style="width:300; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:14pt; font-weight:bold;  border: 1px solid #ccc;"> 
						
						  <tr>
						  	<td colspan="3" align="center" style="background: #dcdcdc	" class="bb">
								<LABEL for="curso_id_mant" ALIGN="left" style="font-size:14pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif">&nbsp; CURSO: &nbsp;</LABEL>
								 <SELECT name="curso_id_mant" id="curso_id_mant" style="width:250px; align=center; font-size:11pt; font-weight:bold; background:#ffffff;"> 
									   <option id="encabezado0" value="-1"> </option> 
								 <?php
									if ($num_cursos_en_ejecucion > 0){
										mysql_data_seek ( $registros0, 0);   //devolvemos el puntero a la posicion 0 de la lista
										while ($reg0=mysql_fetch_array($registros0))
										{         
											$edicion = $reg0['edicion'];
											if ($edicion > 0){
												echo "<option value='".$reg0['curso_id']."'>".$reg0['nombre_curso']." &nbsp;&nbsp; (".$reg0['edicion']." &ordf; edici&oacute;n)</option>";
												//echo "<option value='".$row['curso_id']."'>".$row['nombre_curso']."</option>";
											}else{
												echo "<option value='".$reg0['curso_id']."'>".$reg0['nombre_curso']."</option>";
											}
										}	
										
										mysql_data_seek ( $registros00, 0);   //devolvemos el puntero a la posicion 0 de la lista
										while ($reg00=mysql_fetch_array($registros00))
										{         
											$edicion = $reg00['edicion'];
											if ($edicion > 0){
												echo "<option value='".$reg00['curso_id']."'>".$reg00['nombre_curso']." &nbsp;&nbsp; (".$reg00['edicion']." &ordf; edici&oacute;n)</option>";
												//echo "<option value='".$row['curso_id']."'>".$row['nombre_curso']."</option>";
											}else{
												echo "<option value='".$reg00['curso_id']."'>".$reg00['nombre_curso']."</option>";
											}
										}
									}
								  ?>
										<option id="todos" value="0"> TODOS </option> -->
								 </SELECT>
								 
							</td>
						  </tr>
				
						  <tr style="background: #ccffff">
							<td LABEL for="radio_dia" ALIGN="left" style="font-size:14pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif" class="bb">&nbsp; D&Iacute;A DE LA SEMANA: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</LABEL> 
							</td>				
							<td align="left" class="bb"> &nbsp;
								 <input type="radio" name="radio_dia" id="radio_mismo" value="1" onClick="deshabilitar_dia()" checked> &nbsp;&nbsp; MISMO D&Iacute;A  
							</td>
							<td class="bb"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								 <input type="radio" name="radio_dia" id="radio_otro" value="2" onClick="habilitar_dia()" > OTRO  
									
								 <SELECT name="otro_dia" id="otro_dia" style="width:150px; align=center; font-size:11pt; font-weight:bold; background:#ffffff;" disabled="true"> 
									<option id="encabezado1" value="-1"> </option> -->
									<option id="lunes" value="1" >LUNES</option>
									<option id="martes" value="2" >MARTES</option>
									<option id="miercoles" value="3" >MI&Eacute;RCOLES</option>
									<option id="jueves" value="4" >JUEVES</option>
									<option id="viernes" value="5" >VIERNES</option>
									<option id="sabado" value="6" >S&Aacute;BADO</option>
									<option id="domingo" value="7" >DOMINGO</option>
								 </SELECT>
							</td>	
						  </tr>	
						  
						  <tr style="background: #ffddff">
							<td LABEL for="hora_inicio" ALIGN="left" style="font-size:14pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif" >&nbsp; HORA DE INICIO: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</LABEL>
							</td>	
							<td align="left"> &nbsp;
											
								 <SELECT name="hora_inicio" id="hora_inicio" style="width:150px; align=center; font-size:11pt; font-weight:bold; background:#ffffff;"> 
									<option id="encabezado2" value="-1"> </option>
									<option id="hora0" value="0" >00:00:00</option>
									<option id="hora2" value="2" >02:00:00</option>
									<option id="hora4" value="4" >04:00:00</option>
									<option id="hora6" value="6" >06:00:00</option>
									<option id="hora8" value="8" >08:00:00</option>
									<option id="hora10" value="10" >10:00:00</option>
									<option id="hora12" value="12" >12:00:00</option>
									<option id="hora14" value="14" >14:00:00</option>
									<option id="hora16" value="16" >16:00:00</option>
									<option id="hora18" value="18" >18:00:00</option>
									<option id="hora20" value="20" >20:00:00</option>
									<option id="hora22" value="22" >22:00:00</option>
								 </SELECT>
							</td>
							<td style="background: #aaffaa" LABEL for="duracion" ALIGN="left" style="font-size:14pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif" class="bl">&nbsp; DURACI&Oacute;N: &nbsp;&nbsp;&nbsp;&nbsp;</LABEL>
								 <SELECT name="duracion" id="duracion" style="width:50px; align=center; font-size:11pt; font-weight:bold; background:#ffffff;"> 
									<option id="encabezado3" value="-1"> </option> -->
									<option id="duracion2" value="2" >2</option>
									<option id="duracion4" value="4" >4</option>
									<option id="duracion6" value="6" >6</option>
									<option id="duracion8" value="8" >8</option>
								 </SELECT>		
								 
								 <font style="width:50; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold;" readonly> horas </font>
								 
								 <input type="hidden" name="flag" id="flag" value="">
							</td>
						  </tr>

						</font>
					</table>
				
					<br>
					
					<div align="center">
						<INPUT type="submit" name="cambiar" value="CAMBIAR" face="algerian" size="5" align="center" style="background-color : red; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:120px;"/>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<INPUT type="button" name="quitar" value="QUITAR" face="algerian" size="5" align="center" style="background-color : #b6b6b6; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 9pt; text-align : center; font-weight: bold; width:80px;" onClick="anular();" />
		<!--			    <INPUT type="button" name="salir" value="SALIR" face="algerian" size="5" style="background-color : #b6b6b6; color : Black; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:120px; heigth:80px" onClick="salida_controlada();" /> -->
					</div>	
					
				</td>
				<td width="23%">
				</td>
		    </tr>
		  </form>
		</table>
	</center>


   <form name="form_anular_mant" ID="form_anular_mant" action="mantenimiento_semanal.php" method="POST" onSubmit="return anular();">	   
		<input type="hidden" name="anular_mant" id="anular_mant" value="">   
		<input type="hidden" name="curso_anular_mant" id="curso_anular_mant" value="">  		
   </form>

   
		   
	<form name="form_volver" action="admin.php" method="POST">
      <div align="left">
	    <INPUT type="submit" name="volver" value="VOLVER" face="algerian" size="5" style="background-color : #555555; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:120px;" />
	  </div>	
	  
	  <div align="right">
	    <INPUT type="submit" name="volver" value="VOLVER" face="algerian" size="5" style="background-color : #555555; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:120px;" />
	  </div>	
	</form>


	
	<hr>


	<script type="text/javascript">

	///funcion que contabiliza el tiempo de inactividad en esta pagina, y si se supera el limite, se abandona la sesion

	  var IDLE_TIMEOUT = "<?php echo $idle_timeout ?>"; //seconds     ///lo suyo es ponerle un timeout entre 5 y 10 minutos (300 y 600 segundos)
	  var _idleSecondsCounter = 0;
	  document.onclick = function() {
		_idleSecondsCounter = 0;
	  };
	  document.onmousemove = function() { 
		_idleSecondsCounter = 0;
	  };
	  document.onkeypress = function() {
		_idleSecondsCounter = 0;
	  };
	  //////estas 3 opciones funcionan con todos los navegadores, excepto con FireFox
	  //window.setInterval(CheckIdleTime, 1000);    
	  //setInterval(CheckIdleTime, 1000);
	  //setInterval(function() { CheckIdleTime(); }, 1000);

	  //function CheckIdleTime() {
		//_idleSecondsCounter++;
		//var oPanel = document.getElementById("SecondsUntilExpire");
		//if (oPanel) {
			//oPanel.innerHTML = "Timeout en " + (IDLE_TIMEOUT - _idleSecondsCounter) + " segundos";
		//}
		//if (_idleSecondsCounter >= IDLE_TIMEOUT) {
			//document.forms["form_salida_timeout"].submit();
		//}
	  //}

	  ////////si el navegador es FireFox
	  //var isGecko = navigator.product == 'Gecko' && !/webkit/i.test(navigator.userAgent);
	  
	  /////////////////////////////////////////
	  //var timeoutID;
	  //
	  //var timeout = function() {
		//_idleSecondsCounter++;
		//var oPanel = document.getElementById("SecondsUntilExpire");
		//if (oPanel) {
			//oPanel.innerHTML = "Timeout en " + (IDLE_TIMEOUT - _idleSecondsCounter) + " segundos";
		//}
		//if (_idleSecondsCounter >= IDLE_TIMEOUT) {
			//window.clearTimeout(timeoutID);
			//document.forms["form_salida_timeout"].submit();
		//}
		//
		//timeoutID = window.setTimeout(timeout, 1000);   
	  //};
	  // 
	  //timeout();
	 
	  //////////////////////////////////////////
	  var interval = setInterval(function(){
		_idleSecondsCounter++;
		var oPanel = document.getElementById("SecondsUntilExpire");
		if (oPanel) {
			oPanel.innerHTML = "Timeout en " + (IDLE_TIMEOUT - _idleSecondsCounter) + " segundos";
		}
		if (_idleSecondsCounter >= IDLE_TIMEOUT) {
			clearInterval( interval );
			document.forms["form_salida_timeout"].submit();
		}  
	  
	  }, 1000);

	</script>


	<form name="form_salida_timeout" id="form_salida_timeout" action="finalizar.php" method="post">
	  <div style="overflow:hidden; width:80px; background: transparent no-repeat>
		<input type="text" name="timeout" id="SecondsUntilExpire" style="width:100px;" readonly>
	  </div>
			  
		<input type="hidden" name="salida_timeout" id="salida_timeout" value="<?php if ($admin==0) echo "-1"; else echo "-2"; ?>">    	
	</form>

	
	</body>
	</html>

	
<?php
	mysql_free_result($registros0);
	mysql_free_result($registros00);
	mysql_close($conexion);
	ob_end_flush();		
}
?>