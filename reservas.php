<?php 
 include ("headers/seguridad.php");
 include ("headers/conexion.php");

//iniciamos el buffer de salida 
//ob_start();

//Con este pequeño script lo que hemos hecho es un contador de visitas por usuario. De esta forma sabremos cuantas visitas ha hecho el usuario a esta página.
if(isset($_COOKIE['num_visitas'])) {
  $_COOKIE['num_visitas']++;
  setcookie("num_visitas",$_COOKIE['num_visitas'],time()+30*24*60*60);   // la cookie permanecera durante 30 dias mas activa en el cliente
} else {
  setcookie("num_visitas",1,time()+30*24*60*60);
}


//var_dump($_SESSION); die;

$duracionTurno = $_SESSION['duracionTurno'];
if (!isset($duracionTurno))
	$duracionTurno = 2;
$HorasTurno = floor($duracionTurno);
$MinutosTurno = ($duracionTurno - $HorasTurno)*60;
$numTurnosDia = $_SESSION['numTurnosDia'];
$f_ahora = date('Y-m-d H:i:s');
//echo "<br>f_ahora = $f_ahora";
$hoy = date('Y-m-d');
//echo "<br>hoy = $hoy";

$id_usuario = $_SESSION['id_usuario'];
$admin = $_SESSION['admin'];
$usuario = $_SESSION['usuario'];
$id_sesion = $_SESSION['id_sesion'];

if (!isset($id_sesion))
    echo "id_sesion VACIO";
else
    //echo "id_sesion = $id_sesion";

if (!isset($id_sesion)){
	 //var_dump($_SESSION); die;

	$HorasTurno=2;
	$MinutosTurno=0;
	$numTurnosDia=12;
}


$id_curso = $_SESSION['id_curso'];
$nombre_curso = $_SESSION['nombre_curso'];
$edicion = $_SESSION['edicion'];
$offset_pod = $_SESSION['offset_pod'];

$numMaxReservas = $_SESSION['numMaxReservas'];
$numMaxResvSemana = $_SESSION['numMaxResvSemana'];
$numPods = $_SESSION['numPods'];
$idle_timeout = $_SESSION['idle_timeout'];

////parametros para el outbreak semanal (mantenimiento semanal)
//$dia_mant_semanal = $_SESSION['dia_mant_semanal'];	
//$hora_inicio_mant_semanal = $_SESSION['hora_inicio_mant_semanal'];	
//$duracion_mant_semanal = $_SESSION['duracion_mant_semanal'];

//echo "<br>duracionTurno = $duracionTurno";
//$ultimoAcceso = $_SESSION['ultimoAcceso'];
//echo "<br>ultimo acceso = $ultimoAcceso";
//echo "numPods = $numPods";


////ACTUALIZAMOS LOS CURSOS QUE ACABAN DE FINALIZAR --> (cursos) curso_activo=0 --> curso inactivo
///ASI COMO ACTUALIZAMOS LOS ALUMNOS DE ESOS CURSOS  --> (alumnos_en_cursos) status=1 --> finalizado
$sql01="SELECT * FROM cursos WHERE curso_activo = 1 AND fin_curso < CURDATE()"; 
$registros01=mysql_query($sql01,$conexion) or die ("Problemas con el Select (sql01) ".mysql_error());
$count01 = mysql_num_rows($registros01);
//echo "<br>Total de cursos desactualizados = $count01<hr>";

if ($count01 != 0){
  while ($reg01 = mysql_fetch_array($registros01)){
	
	  $curso_id_finalizado = $reg01['curso_id'];	
	  
	  $sql02="UPDATE cursos SET curso_activo = 0, dia_mant_semanal = -1, hora_inicio_mant_semanal = -1, duracion_mant_semanal = -1, flag_pods_ok = 0 WHERE curso_id = $curso_id_finalizado"; 
      $registros02=mysql_query($sql02,$conexion) or die ("Problemas con el Update (sql02) ".mysql_error());	
	  
	  $sql03="SELECT * FROM alumnos_en_cursos WHERE curso_id = $curso_id_finalizado";   
	  $registros03=mysql_query($sql03,$conexion) or die ("Problemas con el Select (sql03) ".mysql_error());
	  $count03 = mysql_num_rows($registros03);
	  //echo "<br>Total de alumnos en este curso desactualizado ($curso_id_finalizado) = $count03<hr>";
	  
	  if ($count03 != 0){
	      while ($reg03 = mysql_fetch_array($registros03)){

		      $status_id = $reg03['status'];
			  if ($status_id != 1) {
				 
				 $user_id_finalizado = $reg03['user_id'];
				 
				 $sql04="UPDATE alumnos_en_cursos SET status = 1 WHERE curso_id = $curso_id_finalizado AND user_id = $user_id_finalizado"; 
				 $registros04=mysql_query($sql04,$conexion) or die ("Problemas con el Update (sql04) ".mysql_error());	


				///////enviamos un email a los usuarios cuyo curso ha finalizado ////////////
				
				$sql33 = "SELECT * FROM datos_pers WHERE user_id = $user_id_finalizado";
				$registros33 = mysql_query($sql33,$conexion) or die ("Problemas con el Select  (sql33) ".mysql_error());
				$count33 = mysql_num_rows($registros33);
				
				if ($count33 > 0){				
					while ($reg33 = mysql_fetch_array($registros33)){
						$destinatario = $reg33['email'];						
						$nombre = $reg33['nombre'];

					
						$asunto = "Curso Finalizado";
						
						$cuerpo = '
						<html>
						<head>
						<title>Curso Finalizado</title>
						</head>
						<body>
						<h3>Hola '.$nombre.'!</h3>
						<p>
						<b>Ha finalizado el curso '.$nombre_curso.'</b>
						</p>
						<p>
						<b>en el cual estabas matriculado, con lo cual queda bloqueado tu acceso al mismo.</b>
						</p>
						<p>
						<i>Esperamos que hayas disfrutado del mismo y que te animes a realizar otros cursos.</i>
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
				
				///////////////////////////////////////////////////////////////////////////////////////////////		
				
			  }
		  }
	  }
	  mysql_free_result($registros03);  
  }
}else{
  //echo "<br>En este momento no hay ningun curso desactualizado.<br>";
}
//liberamos recursos
mysql_free_result($registros01);



////ACTUALIZAMOS RESERVAS que ya han pasado y que han sido usadas --> RESERVAS USADA (estado_reserva = 1)
$sql05="SELECT * FROM reservas WHERE estado_reserva = 2"; 
$registros05=mysql_query($sql05,$conexion) or die ("Problemas con el Select (sql05) ".mysql_error());
$count05 = mysql_num_rows($registros05);
//echo "<br>Total de reservas acabadas de usar = $count05<hr>";

if ($count05 != 0){
  while ($reg05 = mysql_fetch_array($registros05)){

	  $id_reserva = $reg05['reserva_id'];
	  $id_user = $reg05['user_id'];
	  $status_reserva = $reg05['estado_reserva'];	  

	  $dia_almacenado = $reg05['fecha_reserva'];
	  $hora_almacenada = $reg05['horario_reserva'];
	  $f_almacenada = date($dia_almacenado." ".$hora_almacenada);
	  //echo "<br>f_almacenada = $f_almacenada";
	  
	  $f_fin_reserva = date('Y-m-d H:i:s',strtotime('+'.$HorasTurno.' hours', strtotime($f_almacenada)));
	  if ($duracionTurno > $HorasTurno){
	    $f_fin_temp = date('Y-m-d H:i:s',strtotime('+'.$MinutosTurno.' minutes', strtotime($f_fin_reserva)));
		$f_fin_reserva = $f_fin_temp;
	  }
	  //echo "<br>f_fin_reserva = $f_fin_reserva";	
	  
	  if ($f_ahora > $f_fin_reserva) {
			$status_reserva = 1;
			
			//echo "<br>status_reserva = $status_reserva --> f_fin_reserva = $f_fin_reserva --> ";
		       //if ($status_reserva == 0){ if ($f_ahora > $f_almacenada)  echo "status = PENDIENTE en el TURNO ACTUAL<hr>"; else echo "status = PENDIENTE en un TURNO FUTURO<hr>";} else if ($status_reserva == 2) echo "status = YA USADA en el TURNO ACTUAL<hr>"; else if ($status_reserva == 1) echo "status = FINALIZADA en un TURNO ANTERIOR<hr>"; else if ($status_reserva == -1) echo "status = PERDIDA en un TURNO ANTERIOR<hr>"; else echo "status = INDETERMINADO<hr>";  
		  
		    $sql06="UPDATE reservas SET estado_reserva = 1 WHERE reserva_id = $id_reserva"; 
		    $registros06=mysql_query($sql06,$conexion) or die ("Problemas con el Update (sql06) ".mysql_error());	

	  		$log = "LAB TERMINADO: 1";	
	  }
	  else
		$log="LAB EN EJECUCION: 2";

         ////////////////
         //$fp = fopen("/var/www/loggs.txt","a");
         //fwrite($fp, "**En uso.- inicio_reserva: $f_almacenada \t fin_reserva: $f_fin_reserva \t id_sesion: $id_sesion \t status= $status_reserva \t log: $log \t user_id=$id_user \t usuario: $usuario" . PHP_EOL); 
         //fclose($fp);
         ///////////////

  }

         ////////////////
         //$fp = fopen("/var/www/loggs.txt","a");
         //fwrite($fp, "-------------------------------------------------------------------------------------------------------------------------------" . PHP_EOL); 
         //fclose($fp);
         ///////////////

}else{
  //echo "<br>En este momento no hay ninguna reserva desactualizada.<br>";
}
//liberamos recursos
mysql_free_result($registros05);




////ACTUALIZAMOS RESERVAS que ya han pasado sin haber sido usadas --> RESERVAS CADUCADAS (estado_reserva = -1)
$sql1="SELECT * FROM reservas WHERE estado_reserva = 0"; 
$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el Select (sql1) ".mysql_error());
$count1 = mysql_num_rows($registros1);
//echo "<br>Total de reservas activas = $count1<hr>";

if ($count1 != 0){
  while ($reg1 = mysql_fetch_array($registros1)){
	
	  $id_reserva = $reg1['reserva_id'];
	  $id_user = $reg1['user_id'];
	  $status_reserva =  $reg1['estado_reserva'];
		
      $dia_almacenado = $reg1['fecha_reserva'];
	  $hora_almacenada = $reg1['horario_reserva'];
	  $f_almacenada = date($dia_almacenado." ".$hora_almacenada);
	  //echo "<br>f_almacenada = $f_almacenada";
	  
	  $f_fin_reserva = date('Y-m-d H:i:s',strtotime('+'.$HorasTurno.' hours', strtotime($f_almacenada)));
	  if ($duracionTurno > $HorasTurno){
	    $f_fin_temp = date('Y-m-d H:i:s',strtotime('+'.$MinutosTurno.' minutes', strtotime($f_fin_reserva)));
		$f_fin_reserva = $f_fin_temp;
	  }
	  //echo "<br>f_ahora = $f_ahora -- f_fin_reserva = $f_fin_reserva";	
	  
	  if ($f_ahora > $f_fin_reserva){
	        $status_reserva = -1;

			$sql2="UPDATE reservas SET estado_reserva = $status_reserva WHERE reserva_id = $id_reserva"; 
			$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el Update (sql2) ".mysql_error());
			
			//echo "<br>status_reserva = $status_reserva --> f_fin_reserva = $f_fin_reserva --> ";
		    //if ($status_reserva == 0){ if ($f_ahora > $f_almacenada)  echo "status = PENDIENTE en el TURNO ACTUAL<hr>"; else echo "status = PENDIENTE en un TURNO FUTURO<hr>";} else if ($status_reserva == 2) echo "status = YA USADA en el TURNO ACTUAL<hr>"; else if ($status_reserva == 1) echo "status = FINALIZADA en un TURNO ANTERIOR<hr>"; else if ($status_reserva == -1) echo "status = PERDIDA en un TURNO ANTERIOR<hr>"; else echo "status = INDETERMINADO<hr>";  
 
 
			/**********************************************************************************
			////este email ya se ha enviado por el crontab justo al final de turno caducado////
			
			///////enviamos un email a los usuarios que han dejado pasar un turno sin usarlo ////////////
			
			$sql33 = "SELECT * FROM datos_pers WHERE user_id = $id_user";
			$registros33 = mysql_query($sql33,$conexion) or die ("Problemas con el Select  (sql33) ".mysql_error());
			$count33 = mysql_num_rows($registros33);
			
			if ($count33 > 0){
				$diaRsv = substr($dia_almacenado,8,2);
				$mesRsv = substr($dia_almacenado,5,2);
				$anyoRsv = substr($dia_almacenado,0,4);
				$horaRsv = substr($hora_almacenada,0,2);
				
				while ($reg33 = mysql_fetch_array($registros33)){
					$destinatario = $reg33['email'];						
					$nombre = $reg33['nombre'];

				
					$asunto = "Reserva No Utilizada";
					
					$cuerpo = '
					<html>
					<head>
					<title>Reserva Caducada</title>
					</head>
					<body>
					<h3>Hola '.$nombre.'!</h3>
					<p>
					<b>Ha expirado tu Reserva para el curso '.$nombre_curso.'</b>
					</p>
					<p>
					<b>para la fecha '.$diaRsv.'/'.$mesRsv.'/'.$anyoRsv.' y con inicio a las '.$horaRsv.':00 horas.</b>
					</p>
					<p>
					<i>Deseamos que puedas utilizar tus pr&oacute;ximas reservas.</i>
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
			
			///////////////////////////////////////////////////////////////////////////////////////////////
			
			////este email ya se habia enviado por el crontab al final del turno caducado////
			********************************************************************************/	
	
	  		//$log = "LAB CADUCADO: -1";	
	  }
	  else{
		//$log="LAB PENDIENTE: 0";

         ////////////////
         //$fp = fopen("/var/www/loggs.txt","a");
         //fwrite($fp, "* Futuro.- inicio_reserva: $f_almacenada \t fin_reserva: $f_fin_reserva \t id_sesion: $id_sesion \t status= $status_reserva \t log: $log \t user_id=$id_user \t usuario: $usuario" . PHP_EOL); 
         //fclose($fp);
         ///////////////
	  }

  }

         ////////////////
         //$fp = fopen("/var/www/loggs.txt","a");
         //fwrite($fp, "-------------------------------------------------------------------------------------------------------------------------------" . PHP_EOL); 
         //fclose($fp);
         ///////////////

}else{
  //echo "<br>En este momento no hay ninguna reserva en el sistema.<br>";
}
//liberamos recursos
mysql_free_result($registros1);



////SE EXTRAEN las RESERVAS ACTIVAS para en este momento, de cualquier usuario, para este curso
$sql3="SELECT * FROM reservas WHERE (estado_reserva = 0  OR estado_reserva = 2) AND curso_id = $id_curso ORDER BY fecha_reserva, horario_reserva "; 
$registros3=mysql_query($sql3,$conexion) or die ("Problemas con el Select (sql3) ".mysql_error());
$count3 = mysql_num_rows($registros3);

$num_reservas_activas = 0;

if (isset($vector_turno))
  unset($vector_turno);
if (isset($vector_pod))
  unset($vector_pod);
if (isset($vector_userid))
  unset($vector_userid);
  
//Hora de referencia activa -> el inicio del dia de hoy, a medianoche
$hora_ref_activa = date('Y-m-d'." 00:00:00");
//echo "<br><br>hora_ref_activa = $hora_ref_activa <hr>";
//echo "duracionTurno = $duracionTurno";


///al mismo tiempo, se extraen las RESERVAS ACTIVAS para ESTE USUARIO
$num_mis_reservas_activas = 0;

if (isset($vector_mis_horas))
  unset($vector_mis_horas);
if (isset($vector_mis_pods))
  unset($vector_mis_pods);
  

//para cada reserva activa de este usuario
if ($count3 > 0){
  while ($reg3 = mysql_fetch_array($registros3)){  
  
    $dia_Reserva_activa = $reg3['fecha_reserva'];
    $hora_Reserva_activa = $reg3['horario_reserva'];
    $fecha_guardada_activa = date($dia_Reserva_activa." ".$hora_Reserva_activa); 
  
    //cogemos las fechas de las reservas activas y las traducimos a NUMERO DE BLOQUE
    $dif_segundos = strtotime($fecha_guardada_activa) - strtotime($hora_ref_activa);
	$bloque_dias_activo = floor($dif_segundos/3600/24);
    //echo "<br>Numero de Dias = $bloque_dias_activo";

	if ($duracionTurno == floor($duracionTurno)){
	  $bloque_horas_total_activo = floor($dif_segundos/3600);
      //echo "<br>Horas_total =  $bloque_horas_total_activo";
	  
	  if ($bloque_dias_activo > 0)
	    $horas_temp_activas = $bloque_horas_total_activo % 24;
	  else
	    $horas_temp_activas = $bloque_horas_total_activo;
		
	  $bloque_horas_temp_activo = floor($horas_temp_activas/$duracionTurno);
	  //echo "<br>bloque_horas_temp_activo = $bloque_horas_temp_activo";
    }
	else{
	  $bloque_minutos_total_activo = floor($dif_segundos/60);
	  //echo "<br>Minutos_total = $bloque_minutos_total_activo";
	  
      $duracionTurnoMinutos = 60 * $duracionTurno;
	  $bloque_minutos_temp_activo = floor($bloque_minutos_total_activo / $duracionTurnoMinutos);

	  if ($bloque_dias_activo > 0)
	    $bloque_horas_temp_activo = $bloque_minutos_temp_activo % $numTurnosDia;
      else
	    $bloque_horas_temp_activo = $bloque_minutos_temp_activo;
      //echo "<br>bloque_horas_temp_activo = $bloque_horas_temp_activo";
    }
    $bloque_horas_activo = $bloque_horas_temp_activo;

    //Valor del bloque opcion
	$opcion_activa = $bloque_dias_activo * $numTurnosDia + $bloque_horas_activo;
	//echo "<br>Valor de la opcion asociada: $opcion_activa<hr>";

	//se almacena el valor del bloque correspondiente a la reserva activa 
    $vector_turno[$num_reservas_activas] = $opcion_activa;
	//se almacena el valor del pod correspondiente a la reserva activa
	$vector_pod[$num_reservas_activas] = $reg3['num_POD'];
    //se almacena el valor del userid correspondiente a la reserva activa
	$vector_userid[$num_reservas_activas] = $reg3['user_id'];
	
	
	//// si la reserva pertenece a este usuario, se almacena
	if ($vector_userid[$num_reservas_activas] == $id_usuario)
	{
	   $vector_mis_horas[$num_mis_reservas_activas] = $fecha_guardada_activa;
	   $vector_mis_pods[$num_mis_reservas_activas] = $vector_pod[$num_reservas_activas];
	   
	   $num_mis_reservas_activas = $num_mis_reservas_activas + 1;
	}
	
	//se incrementa el contador de reservas activas
    $num_reservas_activas = $num_reservas_activas + 1;
  }	
}else{  
  //echo"<br>En este momento no hay reservas activas en el sistema.";
}
//liberamos recursos
mysql_free_result($registros3);


/** **/
//echo"<hr><hr>";
//se muestran por pantalla los turnos reservados, el numero de Pod correspondiente y a que usuario pertenece cada reserva
if (isset($vector_turno)){
  do{
    $i = key($vector_turno);
    //echo $vector_turno[$i].' ';
  }while(next($vector_turno));
  reset ($vector_turno);
  //echo"<hr>";
  do{
    $i = key($vector_pod);
    //echo $vector_pod[$i].' ';
  }while(next($vector_pod));
  reset ($vector_pod);
  //echo"<hr>";
  do{
    $i = key($vector_userid);
    //echo $vector_userid[$i].' ';
  }while(next($vector_userid));
  reset ($vector_userid);
  //echo"<hr><hr>";
}
//se muestran por pantalla las horas reservadas y el numero de Pod correspondiente a las reservas de este usuario
if (isset($vector_mis_horas)){
  do{
    $i = key($vector_mis_horas);
    //echo $vector_mis_horas[$i].' ';
  }while(next($vector_mis_horas));
  reset ($vector_mis_horas);
  //echo"<hr>";
  do{
    $i = key($vector_mis_pods);
    //echo $vector_mis_pods[$i].' ';
  }while(next($vector_mis_pods));
  reset ($vector_mis_pods);
  //echo"<hr><hr>";
}
/** **/


////ACTUALIZAMOS INTERVALOS DE MANTENIMIENTO que ya han pasado --> (estado_outage = -1)
$sql4="SELECT * FROM mantenimiento WHERE estado_outage = 0"; 
$registros4=mysql_query($sql4,$conexion) or die ("Problemas con el Select (sql4) ".mysql_error());
$count4 = mysql_num_rows($registros4);
//echo "<br>Total intervalos de mantenimiento activos = $count4<hr>";

if ($count4 != 0){
  while ($reg4 = mysql_fetch_array($registros4)){

      $status_outage = $reg4['estado_outage'];
	  $id_outage = $reg4['outage_id'];
	  $num_POD_outage = $reg4['num_POD_outage'];
	  //echo "<br>num_POD_outage = $num_POD_outage<hr>";
	  
	  $dia_outage = $reg4['fecha_outage'];
	  $hora_outage = $reg4['horario_outage'];
	  $f_outage = date($dia_outage." ".$hora_outage);
	  //echo "<br>f_outage = $f_outage";
	  
	  $f_fin_outage = date('Y-m-d H:i:s',strtotime('+'.$HorasTurno.' hours', strtotime($f_outage)));
	  if ($duracionTurno > $HorasTurno){
		$f_fin_outage_temp = date('Y-m-d H:i:s',strtotime('+'.$MinutosTurno.' minutes', strtotime($f_fin_outage)));
		$f_fin_outage = $f_fin_temp_outage;
	  }
	  //echo "<br>f_fin_outage = $f_fin_outage";	
	  
	  if ($f_ahora > $f_fin_outage){
		 $status_outage = -1;
		 //echo "<br>status_outage = $status_outage";
	  
		 $sql5="UPDATE mantenimiento SET estado_outage = $status_outage WHERE outage_id = $id_outage"; 
		 $registros5=mysql_query($sql5,$conexion) or die ("Problemas con el Update (sql5) ".mysql_error());	
	  }
  }
}else{
  //echo "<br>En este momento no hay ning&uacute;n intervalo de mantenimiento activo en el sistema.<br>";
}
//liberamos recursos
mysql_free_result($registros4);




////SE EXTRAEN los INTERVALOS DE MANTENIMIENTO ACTIVOS en este momento, para este curso
$pod_inicial = $offset_pod + 1;
$pod_final = $offset_pod + $numPods;

$sql6="SELECT * FROM mantenimiento  WHERE estado_outage = 0 AND num_POD_outage BETWEEN $pod_inicial AND $pod_final  ORDER BY fecha_outage, horario_outage, num_POD_outage "; 
$registros6=mysql_query($sql6,$conexion) or die ("Problemas con el Select (sql6) ".mysql_error());
$count6 = mysql_num_rows($registros6);

$num_turnos_outage_activos = 0;

if (isset($vector_outage_hora))
  unset($vector_outage_hora);
if (isset($vector_outage_turno))
  unset($vector_outage_turno);
if (isset($vector_outage_pod))
  unset($vector_outage_pod);

//para cada intervalo de mantenimiento activo
if ($count6 > 0){
  while ($reg6 = mysql_fetch_array($registros6)){  
  
    $dia_Reserva_outage = $reg6['fecha_outage'];
    $hora_Reserva_outage = $reg6['horario_outage'];
    $fecha_guardada_outage = date($dia_Reserva_outage." ".$hora_Reserva_outage); 

	//cogemos las fechas de las reservas activas y las traducimos a NUMERO DE BLOQUE
    //$hora_ref_activa = date('Y-m-d'." 00:00:00");
    //echo "<br><br>hora_ref_activa = $hora_ref_activa";
  
    $dif_segundos = strtotime($fecha_guardada_outage) - strtotime($hora_ref_activa);
	$bloque_dias_outage = floor($dif_segundos/3600/24);
    //echo "<br>Numero de Dias = $bloque_dias_outage";

	if ($duracionTurno == floor($duracionTurno)){
	  $bloque_horas_total_outage = floor($dif_segundos/3600);
      //echo "<br>Horas_total =  $bloque_horas_total_outage";
	  
	  if ($bloque_dias_outage > 0)
	    $horas_temp_outage = $bloque_horas_total_outage % 24;
	  else
	    $horas_temp_outage = $bloque_horas_total_outage;
		
	  $bloque_horas_temp_outage = floor($horas_temp_outage/$duracionTurno);
	  //echo "<br>bloque_horas_temp_outage = $bloque_horas_temp_outage";
    }
	else{
	  $bloque_minutos_total_outage = floor($dif_segundos/60);
	  //echo "<br>Minutos_total = $bloque_minutos_total_outage";
	  
      $duracionTurnoMinutos = 60 * $duracionTurno;
	  $bloque_minutos_temp_outage = floor($bloque_minutos_total_outage / $duracionTurnoMinutos);

	  if ($bloque_dias_outage > 0)
	    $bloque_horas_temp_outage = $bloque_minutos_temp_outage % $numTurnosDia;
      else
	    $bloque_horas_temp_outage = $bloque_minutos_temp_outage;
      //echo "<br>bloque_horas_temp_outage = $bloque_horas_temp_outage";
    }
    $bloque_horas_outage = $bloque_horas_temp_outage;

    //Valor del bloque opcion
	$opcion_outage = $bloque_dias_outage * $numTurnosDia + $bloque_horas_outage;
	//echo "<br>Valor de la opcion outage asociada: $opcion_outage<hr>";

	//se almacena la fecha y hora correspondiente al inicio del intervalo de mantenimiento activo
	$vector_outage_hora[$num_turnos_outage_activos] = $fecha_guardada_outage;
	//se almacena el valor del bloque correspondiente al intervalo de mantenimiento activo
    $vector_outage_turno[$num_turnos_outage_activos] = $opcion_outage;
    //se almacena el valor del pod correspondiente al intervalo de mantenimiento activo  (1,2,3,...,X -> todos)
	$vector_outage_pod[$num_turnos_outage_activos] = $reg6['num_POD_outage'];
	
	//se almacena tambien solo la fecha correspondiente al inicio del intervalo activo
	$vector_outage_solo_dia[$num_turnos_outage_activos] = $dia_Reserva_outage;
	//se almacena tambien solo la hora correspondiente al inicio del intervalo activo
	$vector_outage_solo_hora[$num_turnos_outage_activos] =	$hora_Reserva_outage;
		
	//se incrementa el contador de reservas activas
    $num_turnos_outage_activos = $num_turnos_outage_activos + 1;  
  }	
}else{  
  //echo"<br>En este momento no hay intervalos de mantenimiento activos en el sistema.";
}
//liberamos recursos
mysql_free_result($registros6);


/** **/
//echo"<hr><hr>";
//se muestran por pantalla los turnos reservados, el numero de Pod correspondiente y a que usuario pertenece cada reserva
if (isset($vector_outage_turno)){
  do{
	$i = key($vector_outage_hora);
	//echo $vector_outage_hora[$i].' ';
  }while(next($vector_outage_hora));
  reset ($vector_outage_hora);
  //echo"<hr>";
  do{
    $i = key($vector_outage_turno);
    //echo $vector_outage_turno[$i].' ';
  }while(next($vector_outage_turno));
  reset ($vector_outage_turno);
  //echo"<hr>";
  do{
    $i = key($vector_outage_pod);
    //echo $vector_outage_pod[$i].' ';
  }while(next($vector_outage_pod));
  reset ($vector_outage_pod);
  //echo"<hr>";
}
/** **/  



//******** CODIGO PARA CONTROLAR LAS RESERVAS POR SEMANAS NATURALES ********
$dia_sem = date('w', strtotime($hoy));
if ($dia_sem == 0)
	$dia_sem=7;
//echo "<br>dia_sem = $dia_sem";

////semana actual
$dias_distancia = $dia_sem - 1;
$lunes_ref = date('Y-m-d',strtotime('-'.$dias_distancia.' days', strtotime($hoy)));
$domingo_ref = date('Y-m-d',strtotime('+6 days', strtotime($lunes_ref)));

//buscamos cuantas reservas ha realizado este usuario para la semana actual 
$sql07 = "SELECT count(*) AS total FROM reservas WHERE user_id = $id_usuario AND fecha_reserva BETWEEN '$lunes_ref' AND '$domingo_ref' ";
$registros07 = mysql_query($sql07,$conexion) or die ("Problemas con el Select  (sql07) ".mysql_error()); 
$cuenta07 = mysql_fetch_assoc($registros07);
$count07 = $cuenta07['total'];

$num_resv_semana_actual = $count07;
//echo "<br>Num. de mis Reservas para la semana actual= $num_resv_semana_actual";

////semana siguiente
$lunes_sgte = date('Y-m-d',strtotime('+7 days', strtotime($lunes_ref)));
$domingo_sgte = date('Y-m-d',strtotime('+13 days', strtotime($lunes_ref)));

//buscamos cuantas reservas ha realizado este usuario para la semana proxima
$sql08 = "SELECT count(*) AS total FROM reservas WHERE user_id = $id_usuario AND fecha_reserva BETWEEN '$lunes_sgte' AND '$domingo_sgte' ";
$registros08 = mysql_query($sql08,$conexion) or die ("Problemas con el Select  (sql08) ".mysql_error()); 
$cuenta08 = mysql_fetch_assoc($registros08);
$count08 = $cuenta08['total'];

$num_resv_semana_proxima = $count08;
//echo "<br>Num. de mis Reservas para la semana proxima= $num_resv_semana_proxima";

//liberamos recursos
mysql_free_result($registros07);
mysql_free_result($registros08);
//***************************************************************************



///////////////////////////////////////////
/////******se almacenan los intervalos correspondientes al mantenimiento semanal, para poderlos deshabilitar
$sql00="SELECT * FROM cursos WHERE curso_id = $id_curso"; 
$registros00=mysql_query($sql00,$conexion) or die ("Problemas con el Select  (sql00) ".mysql_error());
$count00 = mysql_num_rows($registros00);
if ($count00 > 0){
  while ($reg00 = mysql_fetch_array($registros00)){  
      $dia_mant_semanal = $reg00['dia_mant_semanal'];
      $hora_inicio_mant_semanal = $reg00['hora_inicio_mant_semanal'];
      $duracion_mant_semanal = $reg00['duracion_mant_semanal'];
  }
}
mysql_free_result($registros00);

$dia_mant = date('Y-m-d H:i:s',strtotime('+'.($dia_mant_semanal -1).' days', strtotime($lunes_ref)));
if ($dia_mant < $hoy)
	$dia_mant = date('Y-m-d H:i:s',strtotime('+7 days', strtotime($dia_mant)));	
//echo "<br>dia_mant = $dia_mant";
$hora_mant_inicial = date('Y-m-d H:i:s',strtotime('+'.$hora_inicio_mant_semanal.' hours', strtotime($dia_mant)));	
//echo "<br>hora_mant_inicial = $hora_mant_inicial";
$hora_mant_final = date('Y-m-d H:i:s',strtotime('+'.$duracion_mant_semanal.' hours', strtotime($hora_mant_inicial)));	
//echo "<br>hora_mant_final = $hora_mant_final";
//echo "<br>lunes_ref = $lunes_ref<br>";
$dia_mant_start = date('Y-m-d', strtotime($hora_mant_inicial));
$hora_mant_start = date('H:i:s', strtotime($hora_mant_inicial));
$dia_mant_stop = date('Y-m-d', strtotime($hora_mant_final));
$hora_mant_stop = date('H:i:s', strtotime($hora_mant_final));
//echo "<br>hora_mant_start = $hora_mant_start -- hora_mant_stop = $hora_mant_stop";

if ($duracion_mant_semanal > 0){
	$dif_segundos_mant = strtotime($hora_mant_inicial) - strtotime($hora_ref_activa);
	$bloque_dias_mant = floor($dif_segundos_mant/3600/24);
	//echo "<br>Numero de Dias = $bloque_dias_mant";

	if ($duracionTurno == floor($duracionTurno)){
	  $bloque_horas_total_mant = floor($dif_segundos_mant/3600);
	  //echo "<br>Horas_total =  $bloque_horas_total_mant";
	  
	  if ($bloque_dias_mant > 0)
		$horas_temp_mant = $bloque_horas_total_mant % 24;
	  else
		$horas_temp_mant = $bloque_horas_total_mant;
		
	  $bloque_horas_temp_mant = floor($horas_temp_mant/$duracionTurno);
	  //echo "<br>bloque_horas_temp_mant = $bloque_horas_temp_mant";
	}
	else{
	  $bloque_minutos_total_mant = floor($dif_segundos_mant/60);
	  //echo "<br>Minutos_total = $bloque_minutos_total_mant";
	  
	  $duracionTurnoMinutos = 60 * $duracionTurno;
	  $bloque_minutos_temp_mant = floor($bloque_minutos_total_mant / $duracionTurnoMinutos);

	  if ($bloque_dias_mant > 0)
		$bloque_horas_temp_mant = $bloque_minutos_temp_mant % $numTurnosDia;
	  else
		$bloque_horas_temp_mant = $bloque_minutos_temp_mant;
	  //echo "<br>bloque_horas_temp_mant = $bloque_horas_temp_mant";
	}
	$bloque_horas_mant = $bloque_horas_temp_mant;

	//Valor del bloque opcion
	$opcion_mant = $bloque_dias_mant * $numTurnosDia + $bloque_horas_mant;
	//echo "<br>Valor de la opcion mant asociada: $opcion_mant<hr>";

	for ($i=0; $i<($duracion_mant_semanal/2); $i++){
		//se almacena la fecha y hora correspondiente al inicio del intervalo de mantenimiento activo
		$vector_mant_sem_hora[$i] = date('Y-m-d H:i:s',strtotime('+'.($i*2).' hours', strtotime($hora_mant_inicial)));
		//se almacena el valor del bloque correspondiente al intervalo de mantenimiento activo
		$vector_mant_sem_turno[$i] = $opcion_mant + $i;	
	}
	
	//for ($i=0; $i<($duracion_mant_semanal/2); $i++){
	//	echo "<br>vector_mant_sem_hora[$i] = $vector_mant_sem_hora[$i]";
	//	echo "<br>vector_mant_sem_turno[$i] = $vector_mant_sem_turno[$i]";		
	//}
}
////////////////////////////////////



////SE EXTRAEN las RESERVAS ACTIVAS para este turno, de ESTE USUARIO, para OTROS CURSOS
$sql09="SELECT * FROM reservas WHERE (estado_reserva = 0  OR estado_reserva = 2) AND user_id = $id_usuario AND curso_id != $id_curso ORDER BY fecha_reserva, horario_reserva, num_POD"; 
$registros09=mysql_query($sql09,$conexion) or die ("Problemas con el Select (sql09) ".mysql_error());
$count09 = mysql_num_rows($registros09);
//echo "<br>count09 = $count09";

$num_mis_otras_reservas_activas = 0;

if (isset($vector_mis_otras_horas))
  unset($vector_mis_otras_horas);
if (isset($vector_mis_otros_turnos))
  unset($vector_mis_otros_turnos);
if (isset($vector_mis_otros_pods))
  unset($vector_mis_otros_pods);
  

//para cada reserva activa de este usuario en otros cursos (que no sean este mismo)
if ($count09 > 0){
  while ($reg09 = mysql_fetch_array($registros09)){  
                         
    $dia_Reserva_activa = $reg09['fecha_reserva'];
    $hora_Reserva_activa = $reg09['horario_reserva'];
    $fecha_guardada_activa = date($dia_Reserva_activa." ".$hora_Reserva_activa); 
   
    //cogemos las fechas de las reservas activas y las traducimos a NUMERO DE BLOQUE
    $dif_segundos = strtotime($fecha_guardada_activa) - strtotime($hora_ref_activa);
	$bloque_dias_activo = floor($dif_segundos/3600/24);
    //echo "<br>Numero de Dias = $bloque_dias_activo";

	if ($duracionTurno == floor($duracionTurno)){
	  $bloque_horas_total_activo = floor($dif_segundos/3600);
      //echo "<br>Horas_total =  $bloque_horas_total_activo";
	  
	  if ($bloque_dias_activo > 0)
	    $horas_temp_activas = $bloque_horas_total_activo % 24;
	  else
	    $horas_temp_activas = $bloque_horas_total_activo;
		
	  $bloque_horas_temp_activo = floor($horas_temp_activas/$duracionTurno);
	  //echo "<br>bloque_horas_temp_activo = $bloque_horas_temp_activo";
    }
	else{
	  $bloque_minutos_total_activo = floor($dif_segundos/60);
	  //echo "<br>Minutos_total = $bloque_minutos_total_activo";
	  
      $duracionTurnoMinutos = 60 * $duracionTurno;
	  $bloque_minutos_temp_activo = floor($bloque_minutos_total_activo / $duracionTurnoMinutos);

	  if ($bloque_dias_activo > 0)
	    $bloque_horas_temp_activo = $bloque_minutos_temp_activo % $numTurnosDia;
      else
	    $bloque_horas_temp_activo = $bloque_minutos_temp_activo;
      //echo "<br>bloque_horas_temp_activo = $bloque_horas_temp_activo";
    }
    $bloque_horas_activo = $bloque_horas_temp_activo;  
  
    //Valor del bloque opcion
	$opcion_activa = $bloque_dias_activo * $numTurnosDia + $bloque_horas_activo;
	//echo "<br>Valor de la opcion asociada: $opcion_activa<hr>";


	//se almacena la fecha y hora correspondiente al inicio del intervalo de reserva activo en otro curso
	$vector_mis_otras_horas[$num_mis_otras_reservas_activas] = $fecha_guardada_activa;
	//se almacena el valor del bloque correspondiente al intervalo de reserva activo en otro curso
    $vector_mis_otros_turnos[$num_mis_otras_reservas_activas] = $opcion_activa;
    //se almacena el valor del pod correspondiente al intervalo de reserva activo en otro curso 
	$vector_mis_otros_pods[$num_mis_otras_reservas_activas] = $reg09['num_POD'];		

	////echo "<br>vector_mis_otros_turnos[$num_mis_otras_reservas_activas] = $vector_mis_otros_turnos[$num_mis_otras_reservas_activas]";
	//se incrementa el contador de mis otras reservas activas
    $num_mis_otras_reservas_activas = $num_mis_otras_reservas_activas + 1;
  }
}else{  
  //echo"<br>En este momento no hay reservas activas en otro curso para este usuario.";
}
//liberamos recursos
mysql_free_result($registros09);  

////////////////***********************************************////////////////


/**     //ya no se usa
///leemos la ultima linea del fichero check_pods.out, para ver si los pods estan activos o desactivados
////FORMATO de las lineas: fecha  hora  pod1  pod2 pod3
////http://stackoverflow.com/questions/15025875/what-is-the-best-way-in-php-to-read-last-lines-from-a-file
function tailFile($filepath, $lines = 1) {
  return trim(implode("", array_slice(file($filepath), -$lines)));
}

$filepath = "/var/www/check_pods.out";
$lastline = tailFile($filepath);
//echo $lastline;
$chunk[0]=substr($lastline,0,8);
$chunk[1]=substr($lastline,9,5);
$chunk[2]=substr($lastline,15,1);  //estado pod 1
$chunk[3]=substr($lastline,17,1);  //estado pod 2
$chunk[4]=substr($lastline,19,1);  //estado pod 3
//$chunk[5]=substr($lastline,21,1);  //estado pod 4
//$chunk[6]=substr($lastline,23,1);  //estado pod 5 ...
//echo "<br>$chunk[0]<br>$chunk[1]<br>$chunk[2]<br>$chunk[3]<br>$chunk[4]";
$estado_pod1 = $chunk[2];
$estado_pod2 = $chunk[3];
$estado_pod3 = $chunk[4];

//metemos los estados de los diversos pods en un array, y los numeros de pod en otro array
$max_pods = $numPods; 
$primer_pod = $offset_pod+1;
for ($i=0; $i<$max_pods; $i++){
	$j= $i + 2;
	$pod_number[$i] = $primer_pod + $i;
	$pod_status[$i] = $chunk[$j];
}
**/

//extraemos el codigo flag_pods_ok de este curso almacenado en mysql
$sql10 = "SELECT * FROM cursos WHERE curso_id = $id_curso";
$registros10 = mysql_query($sql10,$conexion) or die ("Problemas con el Select  (sql10) ".mysql_error()); 
$count10 = mysql_num_rows($registros10);
//echo "<br>count10 = $count10";  
if ($reg10 = mysql_fetch_array($registros10)){ 
	$flag_pods_ok = $reg10['flag_pods_ok'];
	//$present_flag = $reg10['flag_pods_ok'];  
	//echo "<br>present_flag=$flag_pods_ok";
}else{
	//echo "<br>error en la tabla Cursos de la BB.DD. mysql";
}
//liberamos recursos
mysql_free_result($registros10); 


function truemod($num, $mod) {
   return ($mod + ($num % $mod)) % $mod;
}

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
			
//if ($j>0){
//	for ($i=0; $i<$j; $i++){
//	  echo "<br>*lista_negra_pods[$i] = $lista_negra_pods[$i]";
//	}
//}else
//	  echo "<br>*lista_negra vac&iacute;a";

/**     
//se comprueba si el codigo de flag_pods_ok para este curso (1:pod activo, 0:pod inactivo) es el correcto, comparandolo con los ultimos valores actualizados de check_pods.out
/// flag_pods_ok = 1*estadopod1+2*estadopod2+4*estadopod3
$new_flag = 0;
for($i=0; $i<$max_pods; $i++){
	if ($pod_status[$i] == 1)
		$new_flag += pow(2,$i);
	//echo "<br>new_flag = $new_flag";
}

//se compara el valor del flag_pods_ok de la BBDD mysql con el valor del flag calculado, y si no son iguales, se actualiza la BB.DD. mysql
echo "<br>valores de flag_pods_ok: present_flag = $present_flag ; new_flag = $new_flag";
if ($present_flag != $new_flag){
		$sql22 = "UPDATE cursos SET flag_pods_ok=$new_flag WHERE curso_id = $id_curso";
		$registros22 = mysql_query($sql22,$conexion) or die ("Problemas con el Update  (sql22) ".mysql_error());
		$present_flag = $new_flag;
}
**/


////////////**********//////////////////**************////////////////////////////

//// Se comprueba si el pod asignado ha sido deshabilitado tras la asignacion de la reserva. 
//// Si se puede, se le asigna otro pod, y si no, se le envia un email avisando de la cancelacion.

////Devuelve el número ingresado con ceros a la izquierda dependiendo del largo deseado de la cadena de salida
function zerofill($entero, $largo){
	// Limpiamos por si se encontraran errores de tipo en las variables
	$entero = (int)$entero;
	$largo = (int)$largo;
	 
	$relleno = '';
	 
	/**
	 * Determinamos la cantidad de caracteres utilizados por $entero
	 * Si este valor es mayor o igual que $largo, devolvemos el $entero
	 * De lo contrario, rellenamos con ceros a la izquierda del número
	 **/
	if (strlen($entero) < $largo)
		$relleno = str_repeat('0', $largo - strlen($entero));
	return $relleno . $entero;
}

//ahora comprobamos si el usuario tiene reserva en este turno (y en este curso)
$hora_now = date("H");
$hora_start = floor($hora_now/2)*2;
$hora_inicial = zerofill($hora_start,2).":00:00";

$sql11 = "SELECT * FROM reservas WHERE user_id = $id_usuario AND curso_id = $id_curso AND fecha_reserva = '$hoy' AND horario_reserva = '$hora_inicial'";
$registros11 = mysql_query($sql11,$conexion) or die ("Problemas con el Select  (sql11) ".mysql_error()); 
$count11 = mysql_num_rows($registros11);
//echo "<br>count11 = $count11";  
//echo "<br>hora_now=$hora_now ; hora_start=$hora_start ; hora_inicial = $hora_inicial";
if ($reg11 = mysql_fetch_array($registros11)){ 
	$codigo_reserva = $reg11['reserva_id'];
	$num_pod = $reg11['num_POD'];  
	//echo "<br>num_pod=$num_pod";
	$flag_rsv = 1;
}else{
	$flag_rsv = 0;
}
//liberamos recursos
mysql_free_result($registros11);


///En caso de que este usuario tenga reserva ahora en este curso, comprobamos el estado del pod asignado, para ver si sigue activo
if ($flag_rsv == 1){
	//echo "<br>flag_rsv = $flag_rsv";
	//echo "<br>numPods = $numPods";
	$i = $num_pod - $offset_pod - 1;
	//echo "<br>pods_estado[$i] = $pods_estado[$i]";
	if ($pods_estado[$i] == 0){ 	
		/////primero, se busca otro pod libre en este turno para este curso
		$j = 0;
				
		//Seleccionamos de la tabla Reservas los pods ocupados en el intervalo de tiempo seleccionado, para encontrar un POD libre
		$sql12 = "SELECT * FROM reservas WHERE user_id != $id_usuario AND curso_id = $id_curso AND fecha_reserva = '$hoy' AND horario_reserva = '$hora_inicial' ORDER BY num_POD ASC";
		$registros12 = mysql_query($sql12,$conexion) or die ("Problemas con el Select  (sql12) ".mysql_error()); 
		$numPods_ocupados = mysql_num_rows($registros12);
		//echo "<br>numPods_ocupados = $numPods_ocupados"; 

		if ($numPods_ocupados > 0){
			mysql_data_seek ( $registros12, 0);
			while ($reg12 = mysql_fetch_array($registros12)){
				 $reserva_id  = $reg12['reserva_id'];
				 $pods_ocupados[$j] = $reg12['num_POD'];
				 //echo "<br> $j &deg; turno ocupado = POD $pods_ocupados[$j] ; con la reserva_id = $reserva_id.";
				 $j++;	
			}
		}		
		//liberamos recursos
		mysql_free_result($registros12);

		//Seleccionamos de la tabla Mantenimiento los turnos de mantenimiento fijados en el intervalo de tiempo seleccionado, para encontrar un POD libre
		$pod_inicial = $offset_pod + 1;
		$pod_final = $offset_pod + $numPods;  
		$sql13 = "SELECT * FROM mantenimiento WHERE fecha_outage = '$hoy' AND horario_outage = '$hora_inicial' AND num_POD_outage BETWEEN $pod_inicial AND $pod_final ORDER BY num_POD_outage ASC";
		$registros13 = mysql_query($sql13,$conexion) or die ("Problemas con el Select  (sql13) ".mysql_error()); 
		$numPods_mant = mysql_num_rows($registros13);
		//echo "; numPods_mant = $numPods_mant";	   //echo "<br>sql13 = $sql13";
		
		if ($numPods_mant > 0){
			mysql_data_seek ( $registros13, 0);
			while ($reg13 = mysql_fetch_array($registros13)){
				 $outage_id  = $reg13['outage_id'];
				 $pods_ocupados[$j] = $reg13['num_POD_outage'];
				 //echo "<br> $j &deg; turno en mantenimiento = POD $pods_ocupados[$j] ; con el outage_id = $outage_id.";
				 $j++;	
			}	
		}		
		//liberamos recursos
		mysql_free_result($registros13);

		//Seleccionamos los pods de la lista negra (deshabilitados)  --> el pod deshabilitado que estaba seleccionado estara en esta lista
		if (isset($lista_negra_pods)){
			for ($k=0; $k<count($lista_negra_pods); $k++){
				$pod_temp = $lista_negra_pods[$k];
				$pods_ocupados[$j] = $pod_temp;
				//echo "<br> $j &deg; pod en lista negra = POD $pods_ocupados[$j]";
				$j++;
			}
		}

		//se reordena el vector $pods_ocupados, de menor a mayor
		if (isset($pods_ocupados)){
			sort($pods_ocupados);

			//for($h=0; $h<count($pods_ocupados); $h++)
				//echo "<br>pods_ocupados[$h] = $pods_ocupados[$h]";
		}else
			//echo "<br>No hay pods ocupados en este turno";

		//este es el numero de pods disponibles
		$num_pods_disponibles = $numPods - $numPods_ocupados - $numPods_mant - $numPods_lista_negra;
		//echo "<br>num_pods_disponibles = $num_pods_disponibles";
					
		$flag_mail = 0;
		
		///Si hay pods libres, se reasigna la reserva hacia otro pod libre
		if ($num_pods_disponibles > 0){
			$str_pods = '';
			for($k=0; $k<count($pods_ocupados); $k++){
				if ($k == 0)
					$str_pods = $pods_ocupados[$k];
				else
					$str_pods = $str_pods.",".$pods_ocupados[$k];
			}
			//echo "<br>str_pods = $str_pods";			
		
			//elegimos el primer pod disponible, que sera el mas bajo
			$nuevo_pod = $primer_pod;
			for ($j=0; $j<count($pods_ocupados); $j++){
				if ( $pods_ocupados[$j] == ($primer_pod + $j) ){
					$nuevo_pod++;  	
				}
			}			
			//echo "<br>nuevo_pod = $nuevo_pod";
			$flag_mail = $nuevo_pod;
			
			////una vez que hemos conseguido un nuevo pod, le reasignamos el turno cuyo pod acabamos de poner en mantenimiento
			$sql17= "UPDATE reservas set num_POD= $nuevo_pod where reserva_id=$codigo_reserva";
			$registros17 = mysql_query($sql17,$conexion) or die("Problemas en el Insert (sql17) ".mysql_error());

		}else
			//echo "<br>Ya no quedan pods libres en el turno actual";
			
		//echo "<br>flag_mail = $flag_mail";
		
		//////////email de anulacion de reserva
		/////////enviamos un email a los usuarios a los cuales se les ha anulado una reserva al estar deshabilitado el pod que se les habia asignado para este turno, ////
		///////// y no ha sido posible resituarlos en otro pod dentro del mismo intervalo temporal de forma transparente al usuario
		if ($flag_mail==0){
			//echo "<br><br>send email!!!";
			//$sql14 = "SELECT * FROM reservas WHERE user_id = $id_usuario AND curso_id = $id_curso AND fecha_reserva = '$hoy' AND horario_reserva = '$hora_inicial'";
			//$registros14 = mysql_query($sql14,$conexion) or die ("Problemas con el Select  (sql14) ".mysql_error()); 
			//$count14 = mysql_num_rows($registros14);
			//echo "<br>count14 = $count14";  
			//while ($reg14 = mysql_fetch_array($registros14)){
			//		$codigo_usuario = $reg14['user_id'];
			//		
			//		$sql15 = "SELECT * FROM datos_pers WHERE user_id = $codigo_usuario";
			//		$registros15 = mysql_query($sql15$conexion) or die ("Problemas con el Select  (sql15 ".mysql_error());
			//		$count15 = mysql_num_rows($registros15);
			//		
			//		if ($count15 > 0){
			//			
			//			while ($reg15 = mysql_fetch_array($registros15)){
			//					$destinatario = $reg15['email'];						
			//					$nombre = $reg15['nombre'];
			//					$apellidos = $reg15['apellidos'];
			//			}		
			//			mysql_free_result($registros15);	
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
			//			<b>para con fecha $hoy con inicio a las $hora_inicio</b>
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
			//liberamos recursos
			//mysql_free_result($registros14);
			///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//
			///finalmente borramos esa reserva del sistema
			$sql16 = "DELETE FROM reservas WHERE user_id = $id_usuario AND curso_id = $id_curso AND fecha_reserva = '$hoy' AND horario_reserva = '$hora_inicial'";
			$registros16 = mysql_query($sql16,$conexion) or die ("Problemas con el Delete  (sql16) ".mysql_error());
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}
		
	}else{
		//echo "<br>El pod asignado para este turno est&aacute; operativo!";
	}
}


	
////////////////***********************************************////////////////



//Se distinguen las opciones correspondientes a los intervalos temporales, segun el estado de las reservas en cada uno de ellos
function fondo_temp($marca){
  global $duracionTurno, $hora_ref_activa, $f_ahora, $vector_outage_turno, $vector_turno, $vector_userid, $numPods, $id_usuario, $vector_mis_otros_turnos, $vector_mant_sem_turno, $lista_negra_pods;
  $tiempoFinal = $marca + 1;
  $horasTotal = $tiempoFinal * $duracionTurno;
  $f_fin_turno = date('Y-m-d H:i:s',strtotime('+'.$horasTotal.' hours', strtotime($hora_ref_activa)));
  //echo "<br><br>f_fin_turno = $f_fin_turno"; 
  if (isset($lista_negra_pods)){
	$num_pods_down = count($lista_negra_pods);
  }else
	$num_pods_down = 0;
  
  //estado por defecto, donde el turno esta disponible
  $flag = 0;
  
  //si el turno ya ha caducado
  if ($f_ahora > $f_fin_turno)
    $flag = 1;
  
  else {
    //si se ha programado una ventana de mantenimiento y aun esta activa
    if (isset($vector_outage_turno)){
	
	     $size1 = count($vector_outage_turno);
		 $cuenta = 0;
		 $cuenta = $num_pods_down;
	     for ($i=0; $i<$size1; $i++){
		 
             //echo $vector_outage_turno[$i].' ';
			 if ($marca == $vector_outage_turno[$i]){
			 
			    $cuenta = $cuenta + 1;
				if ($cuenta == $numPods){
					$flag = 4;
				}	
			 }
         }		 
	}
  }	
  
  //se busca si este turno ya ha sido ocupado (en el curso actual)
  if (isset($vector_turno) && ($flag == 0)){
      
	   $size2 = count($vector_turno);
	   $cont = 0;   
	   $cont = $num_pods_down;
       for ($i=0; $i<$size2; $i++){
	      
	       //echo $vector_outage_turno[$i].' '; 
	       if ($marca == $vector_turno[$i]){
		     
			  //si este turno pertenece al usuario actual 
			  if ($vector_userid[$i] == $id_usuario){
			  
			     $flag = 2;
				 
			   //si este turno esta ocupado en todos los pods 	 
		      }else{
                 $cont = $cont + 1;		
                 if (($cont == $numPods) && ($flag == 0)){

                    $flag = 3;
				 }
			  }
			  
		   }		   
	   }
  }
  
  //se busca si este turno ya ha sido ocupado a medias entre reservas y mantenimiento
  if (isset($vector_turno) && isset($vector_outage_turno) && ($flag == 0)){
	$count = 0;
	$count = $num_pods_down;
	for ($i=0; $i<$size1; $i++){
		if ($marca == $vector_outage_turno[$i]){	//se cuentan los turnos de mantenimiento en ese mismo intervalo		 
			$count = $count + 1;	
		}	
	}
	for ($j=0; $j<$size2; $j++){			        //se le añaden los turnos reservados en ese mismo intervalo
		if ($marca == $vector_turno[$j]){			 
			$count = $count + 1;
			if ($count == $numPods){                //se comprueba si entre ambos se cubren todos los pods disponibles para este curso
				$flag = 5;
			}	
		}
	}
  }

  //se busca si este turno ya ha sido ocupado por este usuario en otro curso
  if (isset($vector_mis_otros_turnos) && ($flag == 0)){
      
	   $size3 = count($vector_mis_otros_turnos); 
       for ($i=0; $i<$size3; $i++){
	   	   if ($marca == $vector_mis_otros_turnos[$i]){
				$flag = 6;
		   }
	   }
  }

  //se busca si este turno ha sido deshabilitado por mantenimiento semanal
  if (isset($vector_mant_sem_turno) && ($flag == 0)){
      
	   $size4 = count($vector_mant_sem_turno); 
       for ($i=0; $i<$size4; $i++){
	   	   if ($marca == $vector_mant_sem_turno[$i]){
				$flag = 7;
		   }
	   }
  }  

  
  return $flag;
}



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html version="-//W3C//DTD XHTML 1.1//EN"
      xmlns="http://www.w3.org/1999/xhtml" xml:lang="en"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.w3.org/1999/xhtml
                          http://www.w3.org/MarkUp/SCHEMA/xhtml11.xsd"
>

<head>
 <meta http-equiv="Content-Type" content="text/html; charset=iso-8559-1" />
 <title>RESERVAS</title>
 <!-- Estas lineas evitan que la pagina deseada no se cargue en la cache y no pueda ser mostrada cuando se regresa atras. -->
  <meta http-equiv="Expires" CONTENT="0">
  <meta http-equiv="Cache-Control" CONTENT="no-cache">
  <meta http-equiv="Pragma" CONTENT="no-cache">
  
 <SCRIPT LANGUAGE="JavaScript">
 <!--
  function mueveReloj(){ 
    momentoActual = new Date() 
    hora = momentoActual.getHours() 
    minuto = momentoActual.getMinutes() 
    segundo = momentoActual.getSeconds() 

    str_segundo = new String (segundo) 
    if (str_segundo.length == 1) 
       segundo = "0" + segundo 

    str_minuto = new String (minuto) 
    if (str_minuto.length == 1) 
       minuto = "0" + minuto 

    str_hora = new String (hora) 
    if (str_hora.length == 1) 
       hora = "0" + hora 

    horaImprimible = hora + " : " + minuto + " : " + segundo 

    document.form_reloj.reloj.value = horaImprimible 


    setTimeout("mueveReloj()",1000) 
  }
  //-->
 </script>
 
 
 <script LANGUAGE="JavaScript">
 function Reload(){
   var num_cuadros = document.forms.form_horario.elements["opcion[]"].length;
   var numTurnosAlDia = num_cuadros/7;
   var duracionDeCadaTurno = 24 / numTurnosAlDia;

   //document.write("<br>FUNCION RELOAD --> cada " + 1000*60*60*duracionDeCadaTurno + " miliseg, o sea, cada " + duracionDeCadaTurno + " horas.<br>");

   var numMilisegPorTurno = 1000*60*60*duracionDeCadaTurno;
   //document.write("<br>numMilisegPorTurno = " + numMilisegPorTurno);

   location.reload(true);
   
   window.setInterval("Reload()",numMilisegPorTurno);
 }
 </script>
 
 
 <script type="text/javascript">
 /// elimina elementos duplicados en un array
 function eliminateDuplicates(arr) 
 {
  var i,
     len=arr.length,
     out=[],
     obj={};

  for (i=0;i<len;i++) {
    obj[arr[i]]=0;
  }
  for (i in obj) {
    out.push(i);
  }
  return out;
 }
</script>


 <script type="text/javascript">
 function salida_controlada()
 {
   //alert("Voluntary exit!");
   document.forms["form_salida_volunt"].submit();
 }
</script>


 <script type="text/javascript">
 function alertas()
 {
   //var lastline = "<?php echo $lastline ?>";
   //var estado_pod1 = "<?php echo $estado_pod1 ?>";
   //var estado_pod2 = "<?php echo $estado_pod2 ?>";
   //var estado_pod3 = "<?php echo $estado_pod3 ?>";
   ///alert ("estado_pod1 = " + estado_pod1 + "\nestado_pod2 = " + estado_pod2 + "\nestado_pod3 = " + estado_pod3);
   //var num_pod = "<?php echo $num_pod ?>";
   //var flag_rsv = "<?php echo $flag_rsv ?>";
   //alert ("lastline = "+lastline + "\t num_pod = " + num_pod + "\t flag_rsv = " + flag_rsv);
   //if (flag_rsv == 1){
   //		var index = parseInt(num_pod,10);
   //		var estados = [estado_pod1, estado_pod2, estado_pod3];
   //		//alert ("index = " + index + "estados["+(index-1)+"] = " + estados[index-1]);
   //		if (estados[index-1] == '0'){
   //			alert("El pod reservado no est\u00e1 operativo. \nConsulta con el administrador");
   //			return 0;
   //		}
   //}
   //
   //var usuario = "<?php echo $usuario ?>";
   var popup = confirm("\u00a1Aseg\u00farate de que el bloqueador de ventanas emergentes \nest\u00e1 desactivado!","ACEPTAR","CANCELAR");
   if (!popup)
      return 0;
   return confirm("\u00a1Aseg\u00farate de conectar la VPN \ncon el perfil Student, en este preciso instante,\ncon tus credenciales de usuario y contrase\u00f1a \nantes de acceder al RemoteLAB!");
   //return confirm("\u00a1Conecta ahora la VPN con el perfil Student \ncon tu usuario: " + usuario + "\ny tu contrase\u00f1a \nantes de acceder al LAB Remoto!");
 }
</script>


 <script type="text/javascript">
 function confirmacion()
 {
   //alert("Enviando form_horario");
   document.forms["form_horario"].submit();
 }
</script>


 <script type="text/javascript">
 function anulacion()
 {
   //alert("Enviando form_mostrar_reservas");
   document.forms["form_mostrar_reservas"].submit();
 }
</script>


<script type="text/javascript">
    var diasSemana = new Array("Domingo","Lunes","Martes","Mi\u00e9rcoles","Jueves","Viernes","S\u00e1bado"); 
    var meses = new Array ("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
    var f1=new Date();
	//fecha=diasSemana[f1.getDay()] + ", " + f1.getDate() + " de " + meses[f1.getMonth()] + " de " + f1.getFullYear();
    //document.write(fecha.bold());
</script>
	
	
</head>



<body bgcolor="#<?php echo $bgcolor ?>"> 

<?php
//si el usuario es administrador
if ($admin > 0){
?>

  <table width="100%" height="80px" align="center" border="0px">
	<tr>
		<td width="20%" align="center">

		</td>
		
		<th width="60%" align="center" style="width:200; background: #ffffcc; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:24pt; font-weight:bold;  border: 1px solid #ccc;">  RemoteLAB: Sistema de Reservas </th>		
		
		<td width="20%" align="center">
			<INPUT type="button" name="web_admin" value="WEB_ADMIN" face="algerian" size="5" style="background-color : #00ff00; color : Black; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:150px;" onClick="location.href='admin.php'" />
		</td>
	</tr>
	
<?php
}else{
//si el usuario NO es administrador
?>	

  <table width="60%" height="80px" align="center" border="0px">
	<tr>
		<th align="center" style="width:200; background: #ffffcc; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:24pt; font-weight:bold;  border: 1px solid #ccc;">  RemoteLAB: Sistema de Reservas </th>
	</tr>
<?php
}
?>
	<tr height="10">
	</tr>
	
	<tr>
		<td align="center" colspan="3">
		
			<form name="form_errores">	

				  <?php if (isset($_GET["errorreserva"])) 
						{
							if ($_GET["errorreserva"]=="si") {   
				  ?>			
								 <input type="text" name="panelReservas" value="<?php echo "  No has seleccionado ning&uacute;n turno"?>" 
								 id="panelResv" style="width: 270px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  

				  <?php
							}else if ($_GET["errorreserva"]=="so") {   
				  ?>	
								 <input type="text" name="panelReservas" value="<?php echo " Has sobrepasado el l&iacute;mite de Reservas"?>"
								 id="panelResv" style="width: 300px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  

				  <?php
							}else if ($_GET["errorreserva"]=="ni") {   
				  ?>	
								 <input type="text" name="panelReservas" value="<?php echo "   No tienes ninguna Reserva activa"?>"
								 id="panelResv" style="width: 280px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  

				  <?php 
							}else if ($_GET["errorreserva"]=="no") {   
				  ?>		
								 <input type="text" name="panelReservas" value="<?php echo "      No has anulado ning&uacute;n turno "?>"
								 id="panelResv" style="width: 262px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  

				  <?php
							}else if ($_GET["errorreserva"]=="na") {   
				  ?>		
								 <input type="text" name="panelReservas" value="<?php echo " Ya has superado el l&iacute;mite de cancelaciones "?>"
								 id="panelResv" style="width: 330px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  

				  <?php
							}else if ($_GET["errorreserva"]=="sa") {   
				  ?>		
								 <input type="text" name="panelReservas" value="<?php echo " Reservas para esta semana completas"?>"
								 id="panelResv" style="width: 292px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  

				  <?php
							}else if ($_GET["errorreserva"]=="sp") {   
				  ?>		
								 <input type="text" name="panelReservas" value="<?php echo " Reservas para la pr&oacute;xima semana completas"?>"
								 id="panelResv" style="width: 350px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  

				  <?php
							}else if ($_GET["errorreserva"]=="np") {   
				  ?>		
								 <input type="text" name="panelReservas" value="<?php echo "  Ya no quedan Pods libres en este turno"?>"
								 id="panelResv" style="width: 330px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  

				  <?php
							}else if ($_GET["errorreserva"]=="sr") {   
				  ?>		
								 <input type="text" name="panelReservas" value="<?php echo "  Ya tienes una reserva en este turno"?>"
								 id="panelResv" style="width: 320px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  
								 
				  <?php
							}else if ($_GET["errorreserva"]=="nr") {   
				  ?>		
								 <input type="text" name="panelReservas" value="<?php echo "  Acceso denegado al LAB. No tienes reserva"?>"
								 id="panelResv" style="width: 340px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  

				  <?php
							}else if ($_GET["errorreserva"]=="te") {   
				  ?>		
								 <input type="text" name="panelReservas" value="<?php echo "  Acceso denegado al LAB. Tiempo excedido"?>"
								 id="panelResv" style="width: 330px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  

				  <?php
							}else if ($_GET["errorreserva"]=="ta") {   
				  ?>		
								 <input type="text" name="panelReservas" value="<?php echo "   Acceso denegado al LAB. Espera tu turno"?>"
								 id="panelResv" style="width: 330px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  

				  <?php
							}else if ($_GET["errorreserva"]=="ne") {   
				  ?>		
								 <input type="text" name="panelReservas" value="<?php echo "  No puedes anular una reserva ya utilizada"?>"
								 id="panelResv" style="width: 330px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  

				  <?php
							}else if ($_GET["errorreserva"]=="cp") {   
				  ?>		
								 <input type="text" name="panelReservas" value="<?php echo "  Captcha err&oacute;neo durante el cambio de contrase&ntilde;a"?>"
								 id="panelResv" style="width: 380px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  

				  <?php
							}else if ($_GET["errorreserva"]=="ui") {   
				  ?>		
								 <input type="text" name="panelReservas" value="<?php echo "  La contrase&ntilde;a actual introducida no es la tuya"?>"
								 id="panelResv" style="width: 360px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  

				  <?php
							}else if ($_GET["errorreserva"]=="pm") {   
				  ?>		
								 <input type="text" name="panelReservas" value="<?php echo "  Contrase&ntilde;a modificada correctamente"?>"
								 id="panelResv" style="width: 300px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  

				  <?php
							}else if ($_GET["errorreserva"]=="rd") {   
				  ?>		
								 <input type="text" name="panelReservas" value="<?php echo "  Aseg&uacute;rate de tener turno reservado"?>"
								 id="panelResv" style="width: 290px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  

				  <?php
				  }  } else {	
				  ?>			 
								 <input type="text" name="panelReservas" value="<?php echo "  Selecciona tus turnos reservados"?>" 
								 id="panelResv" style="width: 264px; background: #e0e0e0; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  
								 
				   <?php 
						} 
				   ?> 
			 </form>
		
		
		</td>
	</tr>
</table>
  
 <p>
 


		 
<table width="100%" border="1px">
	<tr>
	  <td width="83%" colspan="3">
	  
		<form name="form_horario" id="form_horario" action="confirmar.php" method="POST">
		 <TABLE BORDER>
		  <colgroup span=8 align="center" width="130">

		  <script>
			//funcion similar a DateAdd de ASP para el incremento de tiempos
			Date.prototype.add = function (sInterval, iNum)
			{ 
			 var dTemp = this; 
			 if (!sInterval || iNum == 0) 
			  dTemp; 
			 switch (sInterval.toLowerCase())
			 {
			  case "ms": 
				dTemp.setMilliseconds(dTemp.getMilliseconds() + iNum); 
				break; 
			  case "s": 
				dTemp.setSeconds(dTemp.getSeconds() + iNum); 
				break; 
			  case "mi": 
				dTemp.setMinutes(dTemp.getMinutes() + iNum); 
				break; 
			  case "h": 
				dTemp.setHours(dTemp.getHours() + iNum); 
				break; 
			  case "d": 
				dTemp.setDate(dTemp.getDate() + iNum); 
				break; 
			  case "mo": 
				dTemp.setMonth(dTemp.getMonth() + iNum); 
				break; 
			  case "y": 
				dTemp.setFullYear(dTemp.getFullYear() + iNum); 
				break; 
			 } 
			 return dTemp; 
			} 
		  </script>

		  <TR height="20">
		   <TH bgcolor="CCCCCC">
		   </TH>

		   <TH bgcolor="FFE4B5">
			<script>
			  //var f1=new Date();
			  //var mes = f1.getMonth() + 1;
			  //document.write(diasSemana[f1.getDay()] + ", " + f1.getDate() + "/" + mes);
			  var mes = f1.getMonth();
			  var mes_romano = new Array("I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
			  document.write(diasSemana[f1.getDay()] + ", " + f1.getDate() + "/" + mes_romano[mes]);
			</script>
		   </TH>

		   <TH bgcolor="FFE4B5">
			<script>
			 var f1=new Date();
			 f2=f1.add("d", 1);
			 var mes = f2.getMonth();
			 document.write(diasSemana[f2.getDay()] + ", " + f2.getDate() + "/" + mes_romano[mes]);
			</script>
		   </TH>

		   <TH bgcolor="FFE4B5">
			<script>
			 var f1=new Date();
			 f3=f1.add("d", 2);
			 var mes = f3.getMonth();
			 document.write(diasSemana[f3.getDay()] + ", " + f3.getDate() + "/" + mes_romano[mes]);
			</script>
		   </TH>

		   <TH bgcolor="FFE4B5">
			<script>
			 var f1=new Date();
			 f4=f1.add("d", 3);
			 var mes = f4.getMonth();
			 document.write(diasSemana[f4.getDay()] + ", " + f4.getDate() + "/" + mes_romano[mes]);
			</script>
		   </TH>

		   <TH bgcolor="FFE4B5">
			<script>
			 var f1=new Date();
			 f5=f1.add("d", 4);
			 var mes = f5.getMonth();
			 document.write(diasSemana[f5.getDay()] + ", " + f5.getDate() + "/" + mes_romano[mes]);
			</script>
		   </TH>

		   <TH bgcolor="FFE4B5">
			<script>
			 var f1=new Date();
			 f6=f1.add("d", 5);
			 var mes = f6.getMonth();
			 document.write(diasSemana[f6.getDay()] + ", " + f6.getDate() + "/" + mes_romano[mes]);
			</script>
		   </TH>

		   <TH bgcolor="FFE4B5">
			<script>
			 var f1=new Date();
			 f7=f1.add("d", 6);

			 var mes = f7.getMonth();
			 document.write(diasSemana[f7.getDay()] + ", " + f7.getDate() + "/" + mes_romano[mes]);
			</script>
		   </TH>
			
		  </TR>


		<?php 
		//fondos para los intervalos de la tabla: Blanco=disponible, Azul=pasado, Verde=Ocupado por este usuario en cualquier POD, Rojo=Todos los pods ocupados para ese turno, Violeta=ventana de mantenimiento: turno inactivo
		 $color_temp[0]="white";
		 $color_temp[1]="#3333ff";   //"blue";
		 $color_temp[2]="#009900";   //"green";
		 $color_temp[3]="#ff3333";   //"red";         //"#aeff00" 
		 $color_temp[4]="#993333";   //"maroon";      //vino-tinto
		 $color_temp[5]="#e52d2d";   //"red-maroon";  //"#cc2828"
		 $color_temp[6]="#005500";   //"dark green"; 
		 $color_temp[7]="#743466";   //"violeta";       
		?>

		<?php
		 $horaFin = $hora_ref_activa;  //$hora_ref_activa = date('Y-m-d'." 00:00:00");
		 $valor = 0;
		 
		 for ($contTurnos = 1; $contTurnos<=$numTurnosDia; $contTurnos++){
		?>
		  <TR> 
		   <TD bgcolor="FFE4B5">
			 <?php
				 $horaInicio = $horaFin;
				 $horaFin = date('Y-m-d H:i:s',strtotime('+'.$duracionTurno.' hours', strtotime($horaInicio)));
				 $timeInicio = date('H:i',strtotime('+ 0 hours', strtotime($horaInicio)));
				 $timeFin = date('H:i',strtotime('+ 0 hours', strtotime($horaFin)));
				 echo "$timeInicio - $timeFin";
			 ?>
		   </TD>
		   <?php
			 for ($contDias = 1; $contDias<=7; $contDias++){
				$valor = ($contDias - 1) * $numTurnosDia + ($contTurnos - 1);
				$codigo_color = fondo_temp($valor);
			?>	
				<TD bgcolor="<?php echo $color_temp[$codigo_color]; ?>" align="center"> <!-- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->

				   <input type="checkbox" name="opcion[]" id="opcion<?php echo"$valor";?>" value="<?php echo "$valor";?>" align="center" > <!-- <?php echo "$valor -- $codigo_color"; ?> -->

					 <?php if($codigo_color > 0){ ?>
						  <script type="text/javascript" LANGUAGE="JavaScript">
							 document.getElementById("opcion<?php echo"$valor";?>").style.visibility = "hidden"; 
						  </script>
					 <?php } ?>	
					  
				</TD> 
		   <?php		
			 }
		   ?>

		   </TR>
		<?php   
		  }
		?>
		 </TABLE>
		 
		 <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		 
		 
		</form>
		
	  </td>	
	  
	  
	  
	  
	  <td width="17%" align="left" valign="top">

		<table  border>	  
			<form name="form_datos">
				<tr>
				  <td>
					<input type="text" name="nombreCurso" value="<?php echo "curso:  $nombre_curso"; ?>"
					id="nombreCurso" style="width: 220px; background: lightblue; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:11pt; font-weight:bold; border: 1px solid #ccc; text-align: center; color: black" readonly>
				  </td>
				</tr>
				
				<tr height="5">
				</tr>
				
				<tr>
				  <td>
					<input type="text" name="nombreAlumno" value="<?php echo "usuario:  $usuario"; ?>" 
					id="nombreCurso" style="width: 220px; background: lightblue; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:8pt; font-weight:bold; border: 1px solid #ccc; text-align: center; color: black" readonly>				
				  </td>
				</tr>

				<tr height="5">
				</tr>
				
				<tr>
				  <td>
					<?php
							$reservas_disponibles = $numMaxReservas - $num_mis_reservas_activas;
							
																  /////**************************************
							if ($reservas_disponibles > 0) {      //if ($num_resv_semana_actual > 0){     ////asi obtendriamos las reservas disponibles para ESTA SEMANA  --> cambiar AQUI ABAJO $reservas_disponibles por $num_resv_semana_actual
					?>
								 <input type="text" name="ReservasLibres" value="<?php  echo " Reservas Simult&aacute;neas: $reservas_disponibles "; ?>"
								 id="ResvLibres" style="width: 220px; background: #e0e0e0; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold;  border: 1px solid #ccc; align:center;" readonly>    
					<?php
							}else{
					?>	
								 <input type="text" name="ReservasLibres" value="<?php  echo " Reservas Simult&aacute;neas: $reservas_disponibles"?>" 
								 id="ResvLibres" style="width: 220px; background: khaki; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold;  border: 1px solid #ccc; align:center;" readonly>    		
					<?php
							}
					?>				
				  </td>
				</tr>
				
				<tr height="5">
				</tr>
				
				<tr>
				  <td>
					<?php
							$reservas_disponibles_esta_semana = $numMaxResvSemana - $num_resv_semana_actual;
							/////**************************************
							if ($num_resv_semana_actual > 0){     ////asi obtendriamos las reservas disponibles para ESTA SEMANA  --> cambiar AQUI ABAJO $reservas_disponibles por $num_resv_semana_actual
					?>
								 <input type="text" name="ReservasLibresEstaSemana" value="<?php  echo " Disponibles esta semana: $reservas_disponibles_esta_semana "; ?>"
								 id="ResvLibresSem" style="width: 220px; background: #e0e0e0; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:9pt; font-weight:bold;  border: 1px solid #ccc; align:center;" readonly>    
					<?php
							}else{
					?>	
								 <input type="text" name="ReservasLibresEstaSemana" value="<?php  echo " Disponibles esta semana: $reservas_disponibles_esta_semana"?>" 
								 id="ResvLibresSem" style="width: 220px; background: khaki; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:9pt; font-weight:bold;  border: 1px solid #ccc; align:center;" readonly>    		
					<?php
							}
					?>				
				  </td>
				</tr>

				<tr>
					<tr height="5">
				</tr>
				
				<tr>
				  <td>
					<?php
							$reservas_disponibles_prox_semana = $numMaxResvSemana - $num_resv_semana_proxima;
							/////**************************************
							if ($num_resv_semana_actual > 0){     ////asi obtendriamos las reservas disponibles para ESTA SEMANA  --> cambiar AQUI ABAJO $reservas_disponibles por $num_resv_semana_actual
					?>
								 <input type="text" name="ReservasLibresProxSemana" value="<?php  echo " Disponibles prox.semana: $reservas_disponibles_prox_semana "; ?>"
								 id="ResvLibresProxSem" style="width: 220px; background: #e0e0e0; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:9pt; font-weight:bold;  border: 1px solid #ccc; align:center;" readonly>    
					<?php
							}else{
					?>	
								 <input type="text" name="ReservasLibresProxSemana" value="<?php  echo " Disponibles prox.semana: $reservas_disponibles_prox_semana"?>" 
								 id="ResvLibresProxSem" style="width: 220px; background: khaki; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:9pt; font-weight:bold;  border: 1px solid #ccc; align:center;" readonly>    		
					<?php
							}
					?>				
				  </td>
				</tr>
				
			</form>
			  

			<form name="form_mostrar_reservas" action="anular.php" method="POST">			  
				<tr>
					<td width="340px">	  
						<p>
						<font size="3" face="Tahoma" color="blue"><B><u>MIS RESERVAS ACTIVAS</u></B></font>
<?php
						 if (isset($vector_mis_horas))
						 {
							$size = count($vector_mis_horas);
							//echo "<br>size = $size";
?> 
							<div style="overflow:hidden; width:200px; background: transparent no-repeat right #ffffaa;">
							  <SELECT name="mostrar[]" id="mostrar" size="<?php echo "$size" ?>" style="font-size:11pt; font-weight:bold;  width:220px; height:<?php $altura=$size*19; echo "$altura"; ?>px; border: 1px solid #ccc;" multiple="multiple">
<?php
								for ($i=0; $i<$size; $i++)
								{
									 $fecha_partida = explode(" ",$vector_mis_horas[$i]);
									 
									 $fecha_troceada = explode("-",$fecha_partida[0]);
									 $fecha_ordenada = $fecha_troceada[2]."/".$fecha_troceada[1]."/".$fecha_troceada[0];
									 //echo "<br><br>fecha_ordenada = $fecha_ordenada";
									 
									 $hora_ordenada = $fecha_partida[1];
									 //echo "<br>hora_ordenada = $hora_ordenada";
									 
									 $mis_reservas[$i]= $fecha_ordenada." a las ".$hora_ordenada;
									 //echo "<br>mis_reservas[$i] = $mis_reservas[$i]";		 

?>        
									  <option value="<?php echo "$i"; ?>" id="mis_pods<?php echo "$i"; ?>" style="background: <?php if ($i % 2 == 0) echo "#ffffaa;"; else echo "#ffe6a2;"; ?>"  >      
									   <?php $j=$i+1; echo "$j"; ?>.- <?php echo "$mis_reservas[$i]" ?>  
									  </option>
<?php
								}
								reset ($vector_mis_horas);
								reset ($mis_reservas);
?>
							  </SELECT> 
							</div> 
<?php
						 } 
						 else
						 {
							$size = 1;
							//echo "<br>size = $size";
?>
						 <div style="overflow:hidden; width:220px; background: transparent no-repeat right #ffffaa;">
						   <SELECT name="mostrar[]" size="1" style="font-size:11pt; font-weight:bold; background:#ffffaa; width:240px; height:<?php $altura=$size*20; echo "$altura"; ?>px; border: 1px solid #ccc;">
							  <option value="-1" id="mis_pods-1" >         
								 No hay turnos reservados
							  </option>

							  </SELECT> 
						 </div>

<?php
						 }
?>

						 <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

						 <button id="anular" class="wrapText" type="button" face="algerian" size="5" style="background-color : #969696; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:125px; height:50px" onClick="anulacion();">ANULAR<br>RESERVAS</button>
					</td>
				</tr>
				

			</form>
		</table>

		 
      </td>
	</tr>
	
	
	<tr valign="top">
		<td align="left" width="25%">
		
			<table border="0">
				<tr>
					<td align="left" width="44%">
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<a href="modificar_contrasenia.php"><img src="fotos/password_modify.png" alt="modificar_contrasenia" WIDTH="60" HEIGHT="80" border="0"></a>
					</td>
					<td align="left" width="1%"> <hr width="1" size="70">
					</td>
					<td align="center" width="55%" valign="top">
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<button id="confirmar" class="wrapText" type="button" face="algerian" size="5" style="background-color : #969696; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:125px; height:50px" onClick="confirmacion();">CONFIRMAR<br>RESERVAS</button>
					</td>
			
				</tr>
				<tr>
					<td width="44%">
						<font size="2" align="right"><a href="modificar_contrasenia.php">Modificar  Contrase&ntilde;a</a></font>
					</td>
					<td align="left" width="1%">    <hr width="1" size="12"> 
					</td>				
					<td width="55%">
					</td>
				</tr>
			</table>

			 
		</td>

		<td valign="top">
			<table border="0">
				<tr>
					<td>
						 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;	   <!-- onClick="location.href='acceder.php'" -->
						 <button id="accederr" type="button" onClick="bool=alertas(); if(bool) location.href='acceder.php'">  <img src="fotos/boton.png" height="80" width="200"> </button>
					</td>
					<td valign="top">
						 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;    <!-- onClick="location.href='acceder.php'" -->
						 <button id="acceder" class="wrapText" type="button" face="algerian" size="5" style="background-color : #969696; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:125px; height:50px" onClick="bool=alertas(); if(bool) location.href='acceder.php'">ACCEDER<br>al LAB</button>
					</td>
				</tr>
				<tr>
					<td colspan="2">
					  <center>
						<font size="2" align="right"><a href="http://www.me-soluziones.net/index.php/2011/03/como-desbloquear-elementos-emergentes-diferentes-navegadores/" target="_blank">Desbloquear ventanas emergentes</a></font>
					  </center>
					</td>
				</tr>
			</table>							
		</td>	
		
		<td width="25%">
			<?php
				///mensajes temporales activos
				$sql7 = "SELECT * FROM mensajes_temp WHERE (curso_id = $id_curso OR curso_id = 0) AND fecha_fin_mensaje >= '$f_ahora' ORDER BY fecha_fin_mensaje ASC, mensaje_id ASC";
				$registros7=mysql_query($sql7,$conexion) or die ("Problemas con el Select (sql7) ".mysql_error());
				$count7 = mysql_num_rows($registros7);  

				if ($count7 > 0){
				  $texto = "";
				  $k = 0;
				  while ($reg7 = mysql_fetch_array($registros7)){
					   $mensaje = $reg7['texto_mensaje'];
					   $texto[$k] = $mensaje;
					   $k++;
					}
			?>	
				
			    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;			
				<font size="3" face="Tahoma" color="maroon"><B><u>MENSAJES TEMPORALES</u></B></font>  
				<p>
			<?php
				}
				else  
				   //echo "<br>No hay mensajes temporales activos.";

				///liberamos recursos
				mysql_free_result($registros7);
			?>	

			<?php
				 if ($count7 > 0)
				 {
			?> 
					<div style="overflow:hidden; width:380px; background: transparent no-repeat right #ccff66; align:right">
						<SELECT name="msg[]" id="msg" size="<?php $lineas=$count7; echo "$lineas"; ?>" style="text-align:right; font-size:10pt; font-weight:bold;  width:400px; height:<?php $altura=$lineas*18; echo "$altura"; ?>px; border: 1px solid #ccc;" multiple="multiple">
			<?php		
							for ($i=0; $i<$count7; $i++)
							{			
			?>        
								<option value="<?php echo "$i"; ?>" id="turno_mant<?php echo "$i"; ?>" style="background: <?php if ($i % 2 == 0) echo "#ccff66;"; else echo "#cc9966;"; ?>"  >      
									<?php $j=$i+1; echo "$j"; ?>.- <?php echo "$texto[$i]" ?>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								</option>
			<?php
							}
							reset ($texto);
			?>
						</SELECT> 
					</div> 
			<?php
				 } 
			?>			
		</td>

	   <td align="center">
			<INPUT type="button" name="salir" value="SALIR" face="algerian" size="5" style="background-color : #b6b6b6; color : Black; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:120px; heigth:80px" onClick="salida_controlada();" />
			<p>
			<?php
				$salida_timeout_value = ($admin==0) ? -1 : -2 ;
			?>

		   <form name="form_salida_timeout" id="form_salida_timeout" action="finalizar.php" method="post">
			   <div style="overflow:hidden; width:80px; background: transparent no-repeat>
				   <input type="text" name="timeout" id="SecondsUntilExpire" style="width:100px;" readonly>
			   </div>			  
				   <input type="hidden" name="salida_timeout" id="salida_timeout" value="<?=$salida_timeout_value ?>">    	
		   </form>

		   <form name="form_salida_volunt" ID="form_salida_volunt" action="finalizar.php" method="POST">			
				<input type="hidden" name="salida_volunt" id="salida_volunt" value="<?php if ($admin==0) echo "1"; else echo "2"; ?>">   	
		   </form>
  
	   </td>
				   
	</tr>
</table>






<!-- Paso de arrays PHP a arrays Javascript -->
<?php
///paso de $vector_turno (PHP) a array_turno (Javascript)   
/// array_php --> $vector_turno;      array_js --> array_turno;
if (isset($vector_turno)) { 
?>
   <script type="text/javascript">
	  var array_turno = new Array();
	<?php

              for($i = 0; $i<count($vector_turno); $i++)
                     echo "array_turno[$i]='".$vector_turno[$i]."';\n";	
    ?>	
	  //for(var i=0; i<array_turno.length; i++)
	     //document.write("<br>array_turno["+i+"] = " + array_turno[i]);
	</script>	  
<?php
    //echo "<br>";
	//for ($j=0; $j<count($vector_turno); $j++)
      //echo "<br>vector_turno[$j] = $vector_turno[$j]";
}
?>


<?php
///paso de $vector_pod (PHP) a array_pod (Javascript)   
if (isset($vector_pod)) { 
?>
   <script type="text/javascript">
	  var array_pod = new Array();
	<?php

              for($i = 0; $i<count($vector_pod); $i++)
                     echo "array_pod[$i]='".$vector_pod[$i]."';\n";	
    ?>
	  //for(var i=0; i<array_pod.length; i++)
	     //document.write("<br>array_pod["+i+"] = " + array_pod[i]);
	</script>	  
<?php
}
?>


<?php
///paso de $vector_userid (PHP) a array_userid (Javascript)   
if (isset($vector_userid)) { 
?>
   <script type="text/javascript">
	  var array_userid = new Array();
	<?php

              for($i = 0; $i<count($vector_userid); $i++)
                     echo "array_userid[$i]='".$vector_userid[$i]."';\n";	
    ?>	
	  //for(var i=0; i<array_userid.length; i++)
	     //document.write("<br>array_userid["+i+"] = " + array_userid[i]);
	</script>	  
<?php
}
?>



<?php
///paso de $vector_outage_hora (PHP) a array_outage_turno (Javascript)   
if (isset($vector_outage_hora)) { 
?>
   <script type="text/javascript">
	  var array_outage_hora = new Array();
	<?php

			  for($i = 0; $i<count($vector_outage_hora); $i++)
					 echo "array_outage_hora[$i]='".$vector_outage_hora[$i]."';\n";	
	?>	
	  //for(var i=0; i<array_outage_hora.length; i++)
	  //   document.write("<br>array_outage_hora["+i+"] = " + array_outage_hora[i]);
	</script>	  
<?php
}
?>
	
	
<?php
///paso de $vector_outage_turno (PHP) a array_outage_turno (Javascript)   
if (isset($vector_outage_turno)) { 
?>
   <script type="text/javascript">
	  var array_outage_turno = new Array();
	<?php

              for($i = 0; $i<count($vector_outage_turno); $i++)
                     echo "array_outage_turno[$i]='".$vector_outage_turno[$i]."';\n";	
    ?>	
	  //for(var i=0; i<array_outage_turno.length; i++)
	  //   document.write("<br>array_outage_turno["+i+"] = " + array_outage_turno[i]);
	</script>	  
<?php
}
?>


<?php
///paso de $vector_outage_pod (PHP) a array_outage_pod (Javascript)   
if (isset($vector_outage_pod)) { 
?>
   <script type="text/javascript">
	  var array_outage_pod = new Array();
	<?php

              for($i = 0; $i<count($vector_outage_pod); $i++)
                     echo "array_outage_pod[$i]='".$vector_outage_pod[$i]."';\n";	
    ?>	
	  //for(var i=0; i<array_outage_pod.length; i++)
	  //   document.write("<br>array_outage_pod["+i+"] = " + array_outage_pod[i]);
	</script>	  
<?php
}
?>



<?php
  // 1.- TURNOS OCUPADOS
if (isset($vector_turno)) { 
?>
  <script type="text/javascript">
    ////SCRIPT para DESHABILITAR los turnos ocupados AL inicio de la sesion//
    var num_turnos_reservados = array_turno.length;
    var total_pods = "<?php echo $numPods ?>";
    var usuario_actual = '<?php echo $id_usuario ?>';
    //document.write("<br>Total turnos = " + num_turnos_reservados + " ; Total pods = " + total_pods + " ; usuario_actual = " + usuario_actual);
	var item1 = array_turno[0];
    var cont1 = 0;
	var flag1 = 0;
    
	for (j=0; j<num_turnos_reservados; j++)
    {  
		/// se deshabilitan los turnos pertenecientes a este usuario
		if (array_userid[j] == usuario_actual)
		{
			var busy = "";
			busy = busy + "opcion" + array_turno[j];
			
			/// deshabilitar el checkbox si el turno correspondiente esta ocupado
			document.getElementById( busy ).disabled = "disabled"; 	 
			
			flag1 = array_turno[j];
			cont1 = 1;
	    }
	    /// se deshabilitan los turnos de otros usuarios que ocupan Todos los pods para ese unico turno
	    else
	    {
			if (flag1 != array_turno[j])
			{
				if (item1 == array_turno[j])
				{
					cont1++;
					if (cont1 == total_pods)
					{
						var busy = "";
						busy = busy + "opcion" + array_turno[j];

						/// deshabilitar el checkbox si el turno correspondiente esta destinado a mantenimiento
						document.getElementById( busy ).disabled = "disabled";    // esto deshabilita
						// document.getElementById( busy ).disabled = "";    // esto habilita
					}
				}
				else
				{
					item1 = array_turno[j];
					cont1 = 1;
				}  
			}
		}
    }  
  </script>
<?php
}
?>


<?php
  // 2.- TURNOS de MANTENIMIENTO en Todos los Pods
if (isset($vector_outage_turno)) { 
?>
<script type="text/javascript">
  ///SCRIPT para DESHABILITAR los turnos de mantenimiento en todos los pods AL inicio de la sesion//
  var num_turnos_outage = array_outage_turno.length;
  var item2 = array_outage_turno[0];
  var cont2 = 0;
  
  for (j=0; j<num_turnos_outage; j++)
  {
    if (item2 == array_outage_turno[j])
	{
	    cont2++;
		if (cont2 == total_pods)
		{
			 var maint = "";
			 maint = maint + "opcion" + array_outage_turno[j];

			 /// deshabilitar el checkbox si el turno correspondiente esta destinado a mantenimiento
			 document.getElementById( maint ).disabled = "disabled";    // esto deshabilita
			 // document.getElementById( maint ).disabled = "";    // esto habilita
		}
	}
	else
	{
	    item2 = array_outage_turno[j];
		cont2 = 1;
	}
  }
</script>
<?php
}
?>


<script type="text/javascript">
  // 3.- TURNOS PASADOS
  ////SCRIPT para DESHABILITAR los turnos pasados AL inicio de la sesion//
  var numTurnosDia = "<?php echo $numTurnosDia ?>";
  var duracionTurno = "<?php echo $duracionTurno ?>";
  var ahora = new Date();
  //document.write("<br>numTurnosDia = " + numTurnosDia + " ; duracionTurno = " + duracionTurno + " ; ahora = " + ahora);
  var array_horas_fin = new Array();
  var indice = -1;
	 
  for (j=0; j<numTurnosDia; j++)
  {
     var hora_ref = new Date(ahora.getFullYear(),ahora.getMonth(),ahora.getDate());
     array_horas_fin[j]=hora_ref.add("h", ((j+1)*duracionTurno));
	 //document.write("<br>array_horas_fin["+j+"] = " + array_horas_fin[j]);
	 //document.write("ahora - array_horas_fin["+j+"] = " + (ahora - array_horas_fin[j]));
	 
	 /// deshabilitar el checkbox si el tiempo actual es mayor que el fin de turno correspondiente
     if ((ahora - array_horas_fin[j]) > 0)
	 {		 
		 var slot = "";
         slot = slot + "opcion" + j;
		 // deshabilitar el checkbox si el tiempo actual es mayor que el fin de turno correspondiente
	     document.getElementById( slot ).disabled = "disabled";    // esto deshabilita
		 
		 //se almacena el ultimo numero de intervalo que se deshabilita
		 indice = j;
		 //var HoraFinResv = "";
		 //HoraFinResv = array_horas_fin[j];
	 }
  }  
</script>


 
<script type="text/javascript">
  //// CALCULO del TIEMPO del FIN de la SESION corriente
  //document.write("<br>HoraFinResv = " + HoraFinResv);
  HoraFinResv = hora_ref.add("h", ((indice+2)*duracionTurno));
  //document.write("<br>HoraFinResv = " + HoraFinResv);

  ///formato para la hora fin de la sesion actual
  var AnioFinSesion = HoraFinResv.getFullYear();

  var MesFinSesion = HoraFinResv.getMonth() + 1;
  if (MesFinSesion <= 9)
	MesFinSesion = "0" + MesFinSesion;

  var DiaFinSesion = HoraFinResv.getDate();
  if (DiaFinSesion <= 9)
	DiaFinSesion = "0" + DiaFinSesion;

	
  var HoraFinSesion = HoraFinResv.getHours();
  if (HoraFinSesion <= 9)
	HoraFinSesion = "0" + HoraFinSesion;

  var MinutoFinSesion = HoraFinResv.getMinutes();
  if (MinutoFinSesion <= 9)
	MinutoFinSesion = "0" + MinutoFinSesion;

  var SegundoFinSesion = HoraFinResv.getSeconds();
  if (SegundoFinSesion <= 9)
	SegundoFinSesion = "0" + SegundoFinSesion;  
  
  //document.write("<br>HORA FIN SESION ---> " + DiaFinSesion + "/" + MesFinSesion + "/" + AnioFinSesion + " " + HoraFinSesion + ":" + MinutoFinSesion + ":" + SegundoFinSesion);
   
   
  ///formato para la hora actual
  var Hoy = new Date();
  var Dia = new Array("Domingo", "Lunes", "Martes", "Mi&eacute;rcoles", "Jueves", "Viernes", "S&aacute;bado", "Domingo");
  var Mes = new Array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
  var Anio = Hoy.getFullYear();
  var Hora = Hoy.getHours(); 
  var Minutos = Hoy.getMinutes(); 
  var Segundos = Hoy.getSeconds(); 
  /* Si la Hora, los Minutos o los Segundos Son Menores o igual a 9, le añadimos un 0 */
  if (Hora<=9)
    Hora = "0" + Hora;
  if (Minutos<=9)
    Minutos = "0" + Minutos;
  if (Segundos<=9)
    Segundos = "0" + Segundos;
  var Fecha = "<br>Hoy es " + Dia[Hoy.getDay()] + ", " + Hoy.getDate() + " de " + Mes[Hoy.getMonth()] + " de " + Anio + ", a las " + Hora + ":" + Minutos + ":" + Segundos;
  //document.write(Fecha);
</script>
   
   
   
<script type="text/javascript">  
// CALCULO del TIEMPO RESTANTE de la SESION corriente
  // las fechas se almacenan como numeros enteros, y las horas como la parte decimal de cada fecha
    
  var diferencia_seg = (HoraFinResv - ahora)/1000;        // tiempo en milisegundos
    
  var seg_temp = (diferencia_seg % 60);
  var countdown_seg = Math.floor(seg_temp);

  var min_temp = Math.floor(diferencia_seg / 60);
  var countdown_min = (min_temp % 60);

  var hor_temp = Math.floor(diferencia_seg / 3600);   
  var countdown_hor = (hor_temp % 24);     
    

  if (countdown_hor<=9)
    countdown_hor = "0" + countdown_hor;
 
  if (countdown_min<=9)
    countdown_min = "0" + countdown_min;
 
  if (countdown_seg<=9)
    countdown_seg = "0" + countdown_seg;

  
  horaImprimir = countdown_hor + " : " + countdown_min + " : " + countdown_seg;

  //document.write("<br>** Tiempo restante para fin de turno: " + horaImprimir)
  //document.write("  -->>  diferencia_seg: " + diferencia_seg + "<br>") 

 
  var numMilisegHastaFinTurno = 1000 * Math.floor(diferencia_seg);
  //document.write("numMilisegHastaFinTurno = " + numMilisegHastaFinTurno);

  offset = 1000 * 5;  // 5 segundos despues del cambio de turno se actualiza la pagina web
  
  setTimeout("Reload();",numMilisegHastaFinTurno + offset);       // se actualiza cuando finaliza el turno corriente, y a partir de ahi se autoactualizara automaticamente cada fin de turno
</script>

 
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

  //////////////////////
  ////////si el navegador es FireFox
  ////var isGecko = navigator.product == 'Gecko' && !/webkit/i.test(navigator.userAgent);
  
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



  <script>
   ///desmarcamos todos los cuadros
   var chks = document.getElementsByName('opcion[]');
   for (i=0; i<chks.length; i++)
   {
      chks[i].checked = false; 
   }
  </script>




</body>
</html>

<?php
exec("/var/www/sincroniza.sh");

//mysql_free_result($registros1);
//mysql_free_result($registros2);
//mysql_free_result($registros3);
//mysql_free_result($registros4);
//mysql_free_result($registros5);
//mysql_free_result($registros6);
mysql_close($conexion);
ob_end_flush();		
?>