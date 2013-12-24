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

	$duracionTurno = $_SESSION['duracionTurno'];
	$HorasTurno = floor($duracionTurno);
	$MinutosTurno = ($duracionTurno - $HorasTurno)*60;
	$numTurnosDia = $_SESSION['numTurnosDia'];
	$f_ahora = date('Y-m-d H:i:s');
	$hoy = date('Y-m-d');

	$id_usuario = $_SESSION['id_usuario'];
	$usuario = $_SESSION['usuario'];
	$admin = $_SESSION['admin'];

	$id_curso = $_SESSION['id_curso'];
	$nombre_curso = $_SESSION['nombre_curso'];
	$edicion = $_SESSION['edicion'];

	$numMaxReservas = $_SESSION['numMaxReservas'];
	$idle_timeout = $_SESSION['idle_timeout'];
	$control = $_SESSION['control'];
	
	$numPods = $_SESSION['numPods'];
	$total_pods = $_SESSION['total_pods'];
	$offset_pod = $_SESSION['offset_pod'];	

	////parametros para el outbreak semanal (mantenimiento semanal)
	$dia_mant_semanal = $_SESSION['dia_mant_semanal'];	
	$hora_inicio_mant_semanal = $_SESSION['hora_inicio_mant_semanal'];	
	$duracion_mant_semanal = $_SESSION['duracion_mant_semanal'];

	
	////ACTUALIZAMOS RESERVAS que ya han pasado sin haber sido usadas --> RESERVAS CADUCADAS (estado_reserva = -1)
	$sql1="SELECT * FROM reservas WHERE estado_reserva = 0"; 
	$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el Select (sql1) ".mysql_error());
	$count1 = mysql_num_rows($registros1);
	//echo "<br>Total de reservas activas = $count1<hr>";

	if ($count1 != 0){
	  while ($reg1 = mysql_fetch_array($registros1)){

		  $status_reserva =  $reg1['estado_reserva'];
		
		  $id_reserva = $reg1['reserva_id'];
		  
		  $dia_almacenado = $reg1['fecha_reserva'];
		  $hora_almacenada = $reg1['horario_reserva'];
		  $f_almacenada = date($dia_almacenado." ".$hora_almacenada);
		  //echo "<br>f_almacenada = $f_almacenada";
		  
		  $f_fin_reserva = date('Y-m-d H:i:s',strtotime('+'.$HorasTurno.' hours', strtotime($f_almacenada)));
		  if ($duracionTurno > $HorasTurno){
			$f_fin_temp = date('Y-m-d H:i:s',strtotime('+'.$MinutosTurno.' minutes', strtotime($f_fin_reserva)));
			$f_fin_reserva = $f_fin_temp;
		  }
		  //echo "<br>f_fin_reserva = $f_fin_reserva";	
		  
		  if ($f_ahora > $f_fin_reserva)
			$status_reserva = -1;
			
		  //echo "<br>status_reserva = $status_reserva<hr>";
		  
		  $sql2="UPDATE reservas SET estado_reserva = $status_reserva WHERE reserva_id = $id_reserva"; 
		  $registros2=mysql_query($sql2,$conexion) or die ("Problemas con el Update (sql2) ".mysql_error());	

	  }
	}else{
	  //echo "<br>En este momento no hay ninguna reserva en el sistema.<br>";
	}
	//liberamos recursos
	mysql_free_result($registros1);

	

	////ACTUALIZAMOS INTERVALOS DE MANTENIMIENTO que ya han pasado --> (estado_outage = -1)
	$sql4="SELECT * FROM mantenimiento WHERE estado_outage = 0"; 
	$registros4=mysql_query($sql4,$conexion) or die ("Problemas con el Select (sql4) ".mysql_error());
	$count4 = mysql_num_rows($registros4);
	//echo "<br>Total intervalos de mantenimiento activos = $count4<hr>";

	if ($count4 != 0){
	  while ($reg4 = mysql_fetch_array($registros4)){

		  $status_outage = $reg4['estado_outage'];
		  $id_outage = $reg4['outage_id'];
		  
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
		  
		  if ($f_ahora > $f_fin_outage)
			$status_outage = -1;
			
		  //echo "<br>status_outage = $status_outage";
		  
		  $num_POD_outage = $reg4['num_POD_outage'];
		  //echo "<br>num_POD_outage = $num_POD_outage<hr>";
		  
		  $sql5="UPDATE mantenimiento SET estado_outage = $status_outage WHERE outage_id = $id_outage"; 
		  $registros5=mysql_query($sql5,$conexion) or die ("Problemas con el Update (sql5) ".mysql_error());	
	  }
	}else{
	  //echo "<br>En este momento no hay ning&uacute;n intervalo de mantenimiento activo en el sistema.<br>";
	}
	//liberamos recursos
	mysql_free_result($registros4);

	

	//Hora de referencia activa -> el inicio del dia de hoy, a medianoche
	$hora_ref_activa = date('Y-m-d'." 00:00:00");
	//echo "<br><br>hora_ref_activa = $hora_ref_activa <hr>";
	
	
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

		//se almacena la fecha y horae correspondiente al inicio del intervalo de mantenimiento activo
		$vector_outage_hora[$num_turnos_outage_activos] = $fecha_guardada_outage;
		//se almacena el valor del bloque correspondiente al intervalo de mantenimiento activo
		$vector_outage_turno[$num_turnos_outage_activos] = $opcion_outage;
		//se almacena el valor del pod correspondiente al intervalo de mantenimiento activo  (1,2,3,...,X -> todos)
		$vector_outage_pod[$num_turnos_outage_activos] = $reg6['num_POD_outage'];

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
	if (isset($vector_outage_hora)){
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
	  

	  
	/////******se almacenan los intervalos correspondientes al mantenimiento semanal, para poderlos deshabilitar
	////codigo para fijar los lunes, el inicio de las semanas naturales
	$dia_sem = date('w', strtotime($hoy));
	//echo "<br>dia_sem = $dia_sem";
	////semana actual
	$dias_distancia = $dia_sem - 1;
	$lunes_ref = date('Y-m-d',strtotime('-'.$dias_distancia.' days', strtotime($hoy)));
	$domingo_ref = date('Y-m-d',strtotime('+6 days', strtotime($lunes_ref)));
	
	$dia_mant = date('Y-m-d H:i:s',strtotime('+'.($dia_mant_semanal -1).' days', strtotime($lunes_ref)));
	//echo "<br>dia_mant = $dia_mant";
	$hora_mant_inicial = date('Y-m-d H:i:s',strtotime('+'.$hora_inicio_mant_semanal.' hours', strtotime($dia_mant)));	
	//echo "<br>hora_mant_inicial = $hora_mant_inicial";
	$hora_mant_final = date('Y-m-d H:i:s',strtotime('+'.$duracion_mant_semanal.' hours', strtotime($hora_mant_inicial)));	
	//echo "<br>hora_mant_final = $hora_mant_final";
	//echo "<br>lunes_ref = $lunes_ref";
	$dia_mant_start = date('Y-m-d', strtotime($hora_mant_inicial));
	$hora_mant_start = date('H:i:s', strtotime($hora_mant_inicial));
	$dia_mant_stop = date('Y-m-d', strtotime($hora_mant_final));
	$hora_mant_stop = date('H:i:s', strtotime($hora_mant_final));

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


	//Se distinguen las opciones correspondientes a los intervalos temporales, segun el estado de las reservas en cada uno de ellos
	function fondo_temp($marca){
	  global $duracionTurno, $hora_ref_activa, $f_ahora, $vector_outage_turno, $vector_turno, $vector_userid, $numPods, $id_usuario, $vector_mant_sem_turno;
	  $tiempoFinal = $marca + 1;
	  $horasTotal = $tiempoFinal * $duracionTurno;
	  $f_fin_turno = date('Y-m-d H:i:s',strtotime('+'.$horasTotal.' hours', strtotime($hora_ref_activa)));
	  //echo "<br><br>f_fin_turno = $f_fin_turno"; 
	  
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
	 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	 <title>MANTENIMIENTO</title>
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

	</head>



	<body bgcolor="#<?php echo $bgcolor ?>" onload="mueveReloj(); Javascript:history.go(1);" onunload="Javascript:history.go(1);">  

	<?php
	//si el usuario es administrador
	if ($admin > 0){
	?>
	   <div style="color:black;font-size:20px;">
		<script>
		  var meses = new Array ("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
		  var diasSemana = new Array("Domingo","Lunes","Martes","Mi\u00e9rcoles","Jueves","Viernes","S\u00e1bado");
		  var f1=new Date();
		  //document.write(diasSemana[f1.getDay()] + ", " + f1.getDate() + " de " + meses[f1.getMonth()] + " de " + f1.getFullYear());
		  fecha=diasSemana[f1.getDay()] + ", " + f1.getDate() + " de " + meses[f1.getMonth()] + " de " + f1.getFullYear();
		  document.write(fecha.bold());
		
		  document.write("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;")
		  document.write("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;")
		</script>
		
	   <INPUT type="button" name="web_admin" value="WEB_ADMIN" face="algerian" size="5" style="background-color : #00ff00; color : Black; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:150px;" onClick="location.href='admin.php'" />
	 
	   <script>	
		 username = '<?php echo $usuario ?>'	
		
		 document.write("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;")	
		 document.write("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;")

		 document.write("usuario: " + username.bold() )
		 //document.write("<hr>")
	   </script> 
	  </div>
	<?php
	}else{
	//si el usuario NO es administrador
	?>
	   <div style="color:black;font-size:20px;">
		<script>
		  var meses = new Array ("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
		  var diasSemana = new Array("Domingo","Lunes","Martes","Mi\u00e9rcoles","Jueves","Viernes","S\u00e1bado");
		  var f1=new Date();
		  //document.write(diasSemana[f1.getDay()] + ", " + f1.getDate() + " de " + meses[f1.getMonth()] + " de " + f1.getFullYear());
		  fecha=diasSemana[f1.getDay()] + ", " + f1.getDate() + " de " + meses[f1.getMonth()] + " de " + f1.getFullYear();
		  document.write(fecha.bold());

		  username = '<?php echo $usuario ?>'
		
		  document.write("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;")
		  document.write("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;")
		  document.write("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;")
		  document.write("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;")
		  document.write("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;")
		  document.write("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;")

		  document.write("usuario: " + username.bold() )
		  //document.write("<hr>")
		</script> 
	   </div>
	<?php
	}
	?>

	<form name="form_reloj"> 
	   <input type="text" name="reloj" size="10" style="background-color : Black; color : White; font-family : Verdana, Arial, Helvetica; font-size : 11pt; text-align : center;" onfocus="window.document.form_reloj.reloj.blur()"> 
	 </form> 
	 
	 
	 <form name="form_param_basicos">
		<font size="5" face="Tahoma" color="red"><B><u>MANTENIMIENTO</u></B></font>  
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

		<?php  if (isset($_GET["errormant"])) 
			{
                if ($_GET["errormant"]=="si") {   
	  ?>			
	                 <input type="text" name="panelMant" value="<?php echo "     No has seleccionado ning&uacute;n turno de Mantenimiento"?>" 
	                 id="panelResv" style="width: 430px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  

	  <?php
	            }else if ($_GET["errormant"]=="so") {   
	  ?>	
	                 <input type="text" name="panelMant" value="<?php echo "  No has seleccionado ning&uacute;n POD para el Mantenimiento"?>"
	                 id="panelResv" style="width: 430px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  

 	  <?php
	  	        }else if ($_GET["errormant"]=="ni") {   
	  ?>	
	                 <input type="text" name="panelMant" value="<?php echo "     No hay ning&uacute;n turno de Mantenimiento activo ahora"?>"
	                 id="panelResv" style="width: 430px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  
 
 	  <?php 
	            }else if ($_GET["errormant"]=="no") {   
	  ?>		
	                 <input type="text" name="panelMant" value="<?php echo "    No has anulado ning&uacute;n turno de Mantenimiento activo"?>"
	                 id="panelResv" style="width: 430px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  	

	  <?php  }  }else{ ?>
		    <!-- 
			  <input type="text" name="MaxResv" value="<?php echo "$numMaxReservas Reservas  por Usuario"?>" id="MaxResv" 
			  style="width: <?php if ($numMaxReservas < 10): echo"175px"; elseif($numMaxReservas < 100): echo"183px"; else: echo"191px"; endif; ?>;
			  background: aquamarine; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold;  border: 1px solid #ccc;" readonly>
			  
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 

			 <input type="text" name="DuracionTurno" value="<?php echo "$duracionTurno"; if($duracionTurno == 1) echo " Hora "; else echo " Horas "; echo "por Reserva"; ?>" id="DuracionTurno" 
			 style="width: <?php if ($duracionTurno == floor($duracionTurno)): echo"155px";  elseif ((10*$duracionTurno) == floor(10*$duracionTurno)): echo"170px";  elseif ((100*$duracionTurno) == floor(100*$duracionTurno)): echo"180px"; else: echo"190px"; endif; ?>;
			 background: aquamarine; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc;" readonly>
            -->
			
             <input type="text" name="panelMant" value="<?php echo "  Selecciona turnos de Mantenimiento y PODs asociados"?>"
             id="panelResv" style="width: 430px; background: aquamarine; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  	

		<?php } ?>
			 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		
        	 <input type="text" name="nombreCurso" value="<?php if ($edicion == 0) echo "$nombre_curso"; else echo "$nombre_curso - $edicion&ordf; edici&oacute;n"; ?>" id="nombreCurso" 
			 style="width: 200px; background: khaki; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:9pt; font-weight:bold; border: 1px solid #ccc; text-align: center; color: black" readonly>

	 </form>
	

	
	<form name="form_horario" id="form_horario" action="mant_confirmar.php" method="POST">
	
	 <table RULES=NONE FRAME=BOX style="background: lightblue" width="1000px">
	   <center><U><font style="background: #ccffcc; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:16pt; font-weight:bold;  border: 1px solid #ccc;">
         <tr height="50px">
		    <th LABEL for="podX" ALIGN="center" style="font-size:16pt" style="font-weight:bold" style="width= 250px; font-family: Verdana, Arial, Helvetica, sans-serif">SELECCIONA NUMERO POD: </LABEL></th>
			<th style="width=220px; font-size:15pt;"> <input type="checkbox" name="pod0" id="pod0" value="0"> Todos los PODS</th>
			
		    <?php
				if ($control == 0){
					for ($i=1; $i<=$numPods; $i++){    ///para hacer mantenimiento de los Pods solo del curso seleccionado
						$pod_mant = $offset_pod + $i; 
	  	    ?>
						<th style="width=160px; font-size:15pt;"> <input type="checkbox" name="pod<?php echo $i ?>" id="pod<?php echo $i ?>" value="<?php echo $i ?>"> POD #<?php echo $pod_mant ?></th>	
			<?php
					}
				}else{
					for ($i=1; $i<=$total_pods; $i++){   ///para hacer mantenimiento de TODOS los pods
			?>
						<th style="width=160px; font-size:15pt;"> <input type="checkbox" name="pod<?php echo $i ?>" id="pod<?php echo $i ?>" value="<?php echo $i ?>"> POD #<?php echo $i ?></th>	
			<?php
					}
				}
			?>
	     </tr>
	   </font></U></center>
     </table>
	
	
	 <TABLE BORDER width="1000px">
	  <colgroup span="8" align="center" width="120px">

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
	 $color_temp[1]="blue";
	 //$color_temp[2]="green";
	 //$color_temp[3]="red";         //"#aeff00" 
	 $color_temp[4]="maroon";
	 //$color_temp[5]="#e52d2d";   //"red-maroon";  //"#cc2828"
	 //$color_temp[6]="#005500";   //"dark green"; 
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
			<TD bgcolor="<?php echo $color_temp[$codigo_color]; ?>" align="center">
			  <input type="checkbox" name="opcion[]" id="opcion<?php echo"$valor";?>" align="center" value="<?php echo "$valor";?>" >   <!--  <?php echo "$valor -- $codigo_color"; ?> -->
			  
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

	 <INPUT type="submit" name="confirmar" value="CONFIRMAR" face="algerian" size="5" style="background-color : Purple; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:125px;" onClick="location.href='mant_confirmar.php';"/>
	 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
     <!--
	 <INPUT type="button" name="salir" value="SALIR" face="algerian" size="5" style="background-color : #555555; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:120px;" onClick="salida_controlada();" />

	 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	 <b>ACCEDER AL SISTEMA: &nbsp;&nbsp;</b><INPUT TYPE="button" name="acceder" value="LABS REMOTO" face="algerian" size="5" style="background-color : Black; color : Gold; font-family : Verdana, Arial, Helvetica, sans-serif ; font-size : 12pt; text-align : center; font-weight: bold; border: 4px solid gold;" onClick="location.href='acceder.php'" />
     -->
	</form>



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
		     //document.write("<br>array_outage_hora["+i+"] = " + array_outage_hora[i]);
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
		     //document.write("<br>array_outage_turno["+i+"] = " + array_outage_turno[i]);
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
		     //document.write("<br>array_outage_pod["+i+"] = " + array_outage_pod[i]);
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
	  var total_pods = "<?php echo $numPods ?>";
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



	<p>
	<font size="3" face="Tahoma" color="blue"><B><u>TURNOS DE MANTENIMIENTO ACTIVOS</u></B></font>

	<?php
	///mensajes temporales activos
	if ($control==0)   // --> solo para el curso actual
		$sql7 = "SELECT * FROM mensajes_temp WHERE (curso_id = $id_curso OR curso_id = 0) AND fecha_fin_mensaje >= '$f_ahora' ORDER BY fecha_fin_mensaje ASC, mensaje_id ASC";
	else   //control==1  --> para todos los cursos
		$sql7 = "SELECT * FROM mensajes_temp WHERE fecha_fin_mensaje >= '$f_ahora' ORDER BY fecha_fin_mensaje ASC, mensaje_id ASC";
		
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
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

	<font size="3" face="Tahoma" color="maroon"><B><u>MENSAJES TEMPORALES</u></B></font>

	<?php

	}
	else  
	   //echo "<br>No hay mensajes temporales activos.";

	///liberamos recursos
	mysql_free_result($registros7);
	?>


	<form name="form_mostrar_mantenimiento" action="mant_anular.php" method="POST">
	 <table width="1000px" border="0px">
	  <tr>
	   <td width="340px">
	<?php
		 if (isset($vector_outage_hora))
		 {
			$altura = count($vector_outage_hora);
	?> 
			<div style="overflow:hidden; width:340px; background: transparent no-repeat right #ffff66;">
			  <SELECT name="mostrar[]" id="mostrar" size="<?php echo "$altura" ?>" style="font-size:11pt; font-weight:bold;  width:360px; border: 1px solid #ccc;" multiple="multiple">
	<?php
					$size = count($vector_outage_hora);
					
					for ($i=0; $i<$size; $i++)
					{
						 $fecha_partida = explode(" ",$vector_outage_hora[$i]);
						 
						 $fecha_troceada = explode("-",$fecha_partida[0]);
						 $fecha_ordenada = $fecha_troceada[2]."/".$fecha_troceada[1]."/".$fecha_troceada[0];
						 //echo "<br><br>fecha_ordenada = $fecha_ordenada";
						 
						 $hora_ordenada = $fecha_partida[1];
						 //echo "<br>hora_ordenada = $hora_ordenada";
						 
						 $turnos_mantenimiento[$i]= $fecha_ordenada." a las ".$hora_ordenada.", en el POD #".$vector_outage_pod[$i];
						 //echo "<br>turnos_mantenimiento[$i] = $turnos_mantenimiento[$i]";		 

	?>        
				  <option value="<?php echo "$i"; ?>" id="turno_mant<?php echo "$i"; ?>" style="background: <?php if ($i % 2 == 0) echo "#ffff66;"; else echo "#ffc31e;"; ?>"  >      
				   <?php $j=$i+1; echo "$j"; ?>.- <?php echo "$turnos_mantenimiento[$i]" ?>  
				  </option>
	<?php
					}
	?>
			  </SELECT> 
			</div> 
			
			<!--serializamos el vector para pasarlo por POST, y en la otra pagina lo deserializamos -->
			<input type="hidden" name="pods_mant" id="pods_mant" value='<?php echo serialize($vector_outage_pod) ?>'></input>  
	<?php
				reset ($turnos_mantenimiento);
				reset ($vector_outage_hora);
				reset ($vector_outage_pod);
		 } 
		 else
		 {
	?>
			 <div style="overflow:hidden; width:312px; background: transparent no-repeat right #ffff66;">
			   <SELECT name="mostrar[]" size="1" style="font-size:11pt; font-weight:bold; background:#ffff66; width:330px; border: 1px solid #ccc;">
				  <option value="-1" id="turno_mant-1" >         
					 No hay turnos de mantenimiento activos
				  </option>
			   </SELECT> 
			 </div>

			 <input type="hidden" name="pods_mant" id="pods_mant" value="-1"></input>
	<?php
	}
	?>
	   </td>
	 
	   <td width="200px">
	   </td>
	   
	   <td align="left">
	   
	<?php
		 if ($count7 > 0)
		 {
	?> 
			<div style="overflow:hidden; width:440px; background: transparent no-repeat right #ccff66;">
			    <SELECT name="msg[]" id="msg" size="<?php $lineas=$count7; echo "$lineas"; ?>" style="text-align:right; font-size:10pt; font-weight:bold;  width:460px; height:<?php $altura=$lineas*18; echo "$altura"; ?>px; border: 1px solid #ccc;" multiple="multiple">
	<?php		
					for ($i=0; $i<$size; $i++)
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
	  </tr>
	 </table>
	 <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

	 <INPUT type="submit" name="anular" value="ANULAR" face="algerian" size="5" style="background-color : chocolate; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:120px;" onClick="location.href='mant_anular.php';"/>

	</form>
	 

	 
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




	  <form name="form_salida_volunt" ID="form_salida_volunt" action="finalizar.php" method="post">
			
			<input type="hidden" name="salida_volunt" id="salida_volunt" value="<?php if ($admin==0) echo "1"; else echo "2"; ?>">   	
	  </form>




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
	//mysql_free_result($registros1);
	//mysql_free_result($registros2);
	//mysql_free_result($registros3);
	//mysql_free_result($registros4);
	//mysql_free_result($registros5);
	//mysql_free_result($registros6);
	mysql_close($conexion);
	ob_end_flush();		
}
?>