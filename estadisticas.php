<?php 
 include ("headers/seguridad.php");
 include ("headers/conexion.php");

//iniciamos el buffer de salida 
ob_start();

$admin = $_SESSION['admin'];
$idle_timeout = $_SESSION['idle_timeout'];
$total_pods = $_SESSION['total_pods'];
$offset_pod = $_SESSION['offset_pod'];

//Se comprueba si el usuario tiene privilegios de administrador
if ($_SESSION['admin'] == 0) { 
     session_destroy();
    //si no tiene privilegios de administrador, se devuelve a la pagina inicial 
    header("Location: index.php?errorusuario=ad"); 
} else { 

	//consulta --> Listado de Alumnos en Cursos Activos
	$hoy = date('Y-m-d');
	
	//////////////TODOS LOS CURSOS/////////////////
	
	////cursos de experto
	$sql0 = "SELECT curso_id, nombre_curso FROM cursos WHERE nombre_curso LIKE '%Experto%' AND num_max_pods > 0";
	$registros0 = mysql_query($sql0,$conexion) or die ("Problemas con el Select (sql0) ".mysql_error());
	$count0 = mysql_num_rows($registros0);       //numero de cursos total
	$i=0;
	if ($count0 > 0)
	{
		while ($reg0 = mysql_fetch_array($registros0))
		{
			$cursos_0[$i] =  $reg0['curso_id'];
			$nombre_cursos_0[$i] = $reg0['nombre_curso'];
			//echo "<br>cursos_0[$i] = $cursos_0[$i]";
			$i++;
		}	  
	}
	mysql_free_result($registros0);
	
	////cursos de NO experto
	$sql0 = "SELECT curso_id, nombre_curso FROM cursos WHERE nombre_curso NOT LIKE '%Experto%' AND num_max_pods > 0";
	$registros0 = mysql_query($sql0,$conexion) or die ("Problemas con el Select (sql0) ".mysql_error());
	$count0 = mysql_num_rows($registros0);       //numero de cursos total
	if ($count0 > 0)
	{
		while ($reg0 = mysql_fetch_array($registros0))
		{
			$cursos_0[$i] =  $reg0['curso_id'];
			$nombre_cursos_0[$i] = $reg0['nombre_curso'];
			//echo "<br>cursos_0[$i] = $cursos_0[$i]";
			$i++;
		}	  
	}
	mysql_free_result($registros0);
	
	////cursos de control
	$sql0 = "SELECT curso_id, nombre_curso FROM cursos WHERE num_max_pods = 0";
	$registros0 = mysql_query($sql0,$conexion) or die ("Problemas con el Select (sql0) ".mysql_error());
	$count0 = mysql_num_rows($registros0);       //numero de cursos total	
	if ($count0 > 0)
	{
		while ($reg0 = mysql_fetch_array($registros0))
		{
			$cursos_0[$i] =  $reg0['curso_id'];
			$nombre_cursos_0[$i] = $reg0['nombre_curso'];
			//echo "<br>cursos_0[$i] = $cursos_0[$i]";
			$i++;
		}	  
	}	
	mysql_free_result($registros0);
	$num_cursos_totales = $i;
	//echo "Num.cursos totales = $num_cursos_totales";
	
	
	//////////////TODOS LOS CURSOS ACTIVOS	
	
	/////cursos de experto
	$sql1 = "SELECT curso_id, nombre_curso FROM cursos WHERE fin_curso >= '$hoy' AND nombre_curso LIKE '%Experto%' AND num_max_pods > 0";
	$registros1 = mysql_query($sql1,$conexion) or die ("Problemas con el Select (sql1) ".mysql_error());
	$count1 = mysql_num_rows($registros1);       //numero de cursos activos 	
	$i=0;
	if ($count1 > 0)
	{
		while ($reg1 = mysql_fetch_array($registros1))
		{
			$cursos_1[$i] =  $reg1['curso_id'];
			$nombre_cursos_1[$i] = $reg1['nombre_curso'];
			//echo "<br>cursos_1[$i] = $cursos_1[$i]";
			$i++;
		}	  
	}
	mysql_free_result($registros1);
	
	/////crusos de NO experto
	$sql1 = "SELECT curso_id, nombre_curso FROM cursos WHERE fin_curso >= '$hoy' AND nombre_curso NOT LIKE '%Experto%' AND num_max_pods > 0";
	$registros1 = mysql_query($sql1,$conexion) or die ("Problemas con el Select (sql1) ".mysql_error());
	$count1 = mysql_num_rows($registros1);       //numero de cursos activos 	
	if ($count1 > 0)
	{
		while ($reg1 = mysql_fetch_array($registros1))
		{
			$cursos_1[$i] =  $reg1['curso_id'];
			$nombre_cursos_1[$i] = $reg1['nombre_curso'];
			//echo "<br>cursos_1[$i] = $cursos_1[$i]";
			$i++;
		}	  
	}
	mysql_free_result($registros1);
	
	/////cursos de control
	
	$sql1 = "SELECT curso_id, nombre_curso FROM cursos WHERE fin_curso >= '$hoy' AND num_max_pods = 0";
	$registros1 = mysql_query($sql1,$conexion) or die ("Problemas con el Select (sql1) ".mysql_error());
	$count1 = mysql_num_rows($registros1);       //numero de cursos activos 	
	if ($count1 > 0)
	{
		while ($reg1 = mysql_fetch_array($registros1))
		{
			$cursos_1[$i] =  $reg1['curso_id'];
			$nombre_cursos_1[$i] = $reg1['nombre_curso'];
			//echo "<br>cursos_1[$i] = $cursos_1[$i]";
			$i++;
		}	  
	}
	mysql_free_result($registros1);
	$num_cursos_activos = $i;
	//echo "Num.cursos activos = $num_cursos_activos";


	//////////////TODOS LOS ACABADOS
	
	/////cursos de experto
	$sql2 = "SELECT curso_id, nombre_curso FROM cursos WHERE fin_curso < '$hoy' AND nombre_curso LIKE '%Experto%' AND num_max_pods > 0";
	$registros2 = mysql_query($sql2,$conexion) or die ("Problemas con el Select (sql2) ".mysql_error());
	$count2 = mysql_num_rows($registros2);       //numero de cursos pasados 	
	$i=0;
	if ($count2 > 0)
	{
		while ($reg2 = mysql_fetch_array($registros2))
		{
			$cursos_2[$i] =  $reg2['curso_id'];
			$nombre_cursos_2[$i] = $reg2['nombre_curso'];
			//echo "<br>cursos_2[$i] = $cursos_2[$i]";
			$i++;
		}	  
	}
	mysql_free_result($registros2);
	
	/////cursos de NO experto
	$sql2 = "SELECT curso_id, nombre_curso FROM cursos WHERE fin_curso < '$hoy' AND nombre_curso NOT LIKE '%Experto%' AND num_max_pods > 0";
	$registros2 = mysql_query($sql2,$conexion) or die ("Problemas con el Select (sql2) ".mysql_error());
	$count2 = mysql_num_rows($registros2);       //numero de cursos pasados 	
	if ($count2 > 0)
	{
		while ($reg2 = mysql_fetch_array($registros2))
		{
			$cursos_2[$i] =  $reg2['curso_id'];
			$nombre_cursos_2[$i] = $reg2['nombre_curso'];
			//echo "<br>cursos_2[$i] = $cursos_2[$i]";
			$i++;
		}	  
	}
	mysql_free_result($registros2);
	
	/////cursos de control
	$sql2 = "SELECT curso_id, nombre_curso FROM cursos WHERE fin_curso < '$hoy' AND num_max_pods = 0";
	$registros2 = mysql_query($sql2,$conexion) or die ("Problemas con el Select (sql2) ".mysql_error());
	$count2 = mysql_num_rows($registros2);       //numero de cursos pasados 
	if ($count2 > 0)
	{
		while ($reg2 = mysql_fetch_array($registros2))
		{
			$cursos_2[$i] =  $reg2['curso_id'];
			$nombre_cursos_2[$i] = $reg2['nombre_curso'];
			//echo "<br>cursos_2[$i] = $cursos_2[$i]";
			$i++;
		}	  
	}
	mysql_free_result($registros2);	
	$num_cursos_pasados = $i;
	//echo "Num.cursos activos = $num_cursos_pasados";
	
	///////////////////////////////////////////////
	
		
	//comprobamos la opcion de ESTADO: todos, activos, pasados
	if (!isset($_GET['estadoID'])){
	   $statusNum = 0;
	   $flag_cursos = 0;  //para marcar que NO se ha recibido estadoID, asi que tampoco stat_cursos
	}else{
	   $statusNum = $_GET['estadoID'];
	   $flag_cursos = 1;  //para marcar que se ha recibido estadoID, asi que tambien stat_cursos
	}
    //echo "<br>flag_cursos = $flag_cursos";
	//echo "<br>statusNum = $statusNum";

	//comprobamos la opcion de CURSO: todos, o uno en particular
	if (!isset($_GET['cursoID'])){
	   $cursoNum = 0;
	}else{
	   $cursoNum = $_GET['cursoID'];
	}//echo "<br>cursoNum = $cursoNum";

	//comprobamos la opcion de ALUMNO: todos, o uno en particular del curso elegido
	if (!isset($_GET['usuarioID'])){
	   $alumnoNum = 0;
	   $flag_usuarios = 0;  //para marcar que NO se ha recibido usuarioID, asi que tampoco stat_usuarios
	}else{
	   $alumnoNum = $_GET['usuarioID'];
	   $flag_usuarios = 1;  //para marcar que se ha recibido usuarioID, asi que tambien stat_usuarios
	}//echo "<br>alumnoNum = $alumnoNum";
	
	//comprobamos la opcion de ESCALA Temporal: por semanas, por dias, por horas
	if (!isset($_GET['tiempoID'])){
	   $escalaTempNum = 0;
	   $flag_tiempos = 0;  //para marcar que NO se ha recibido tiempoID, asi que tampoco stat_tiempos
	}else{
	   $escalaTempNum = $_GET['tiempoID'];
	   $flag_tiempos = 1;  //para marcar que se ha recibido tiempoID, asi que tambien stat_tiempos
	}//echo "<br>escalaTempNum = $escalaTempNum";

	//comprobamos la opcion de PERIODO: reservas recientes (anteriores) o pendientes (posteriores) al momento actual
	if (!isset($_GET['ultimasReservasID'])){
	   $lastResv = 0;
	   $flag_ultimasReservas = 0;  //para marcar que NO se ha recibido ultimasReservasID, asi que tampoco stat_reservas
	}else{
	   $lastResv = $_GET['ultimasReservasID'];
	   $flag_ultimasReservas = 1;  //para marcar que se ha recibido ultimasReservasID, asi que tambien stat_reservas
	}//echo "<br>lastResv = $lastResv";
	
	if (!isset($_GET['stat_cursos'])){
	   //echo "<br>stat_cursos NO se ha recibido";
	}else{
	   //echo "<br>stat_cursos se ha recibido";
	}

	//comprobamos la opcion de ADMINS: todos, o uno en particular
	if (!isset($_GET['adminsID'])){
	   $adminsNum = 0;
	   $flag_admins = 0;  //para marcar que NO se ha recibido adminsID, asi que tampoco stat_admins
	}else{
	   $adminsNum = $_GET['adminsID'];
	   $flag_admins = 1;  //para marcar que se ha recibido adminsID, asi que tambien stat_admins
	}//echo "<br>adminsNum = $adminsNum";	
	
	
    ////buscamos los alumnos matriculados en los cursos seleccionados
    if (!isset($_GET['stat_cursos']))        	//si no se han seleccionado cursos, entonces se muestran todos los alumnos
	{
		$sql3 = "SELECT user_id, username FROM usuarios";
		$registros3=mysql_query($sql3,$conexion) or die ("Problemas con el Select (sql3) ".mysql_error());
		$count3=mysql_num_rows($registros3);       //numero de cursos totales 	
		//echo "<br>Num.usuarios total = $count3";
		$pool_alumnos = 0;
	}
	else
	{
	    // si se ha seleccionado la opcion Todos los cursos
	    if ($cursoNum == 0)
		{
			if ($statusNum == 0)     //cursos activos y pasados
			{
				$sql3 = "SELECT user_id, username FROM usuarios";
				$registros3=mysql_query($sql3,$conexion) or die ("Problemas con el Select (sql3) ".mysql_error());
				$count3=mysql_num_rows($registros3);       //numero de cursos totales 	
				//echo "<br>Num.usuarios total = $count3";
				$pool_alumnos = 0;
			}
			else if ($statusNum == 1)    // cursos activos
			{
				$sql3 = "SELECT user_id, username FROM usuarios WHERE user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy'  ) ) ";
				$registros3=mysql_query($sql3,$conexion) or die ("Problemas con el Select (sql3) ".mysql_error());
				$count3=mysql_num_rows($registros3);       //numero de cursos totales 	
				//echo "<br>Num.usuarios total = $count3";
				$pool_alumnos = 1;
			}
			else   //($statusNum == 2)    //cursos pasados
			{
				$sql3 = "SELECT user_id, username FROM usuarios WHERE user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy'  ) ) ";
				$registros3=mysql_query($sql3,$conexion) or die ("Problemas con el Select (sql3) ".mysql_error());
				$count3=mysql_num_rows($registros3);       //numero de cursos totales 	
				//echo "<br>Num.usuarios total = $count3";
				$pool_alumnos = 2;	
			}
		}
		else     //un curso en particular
		{
				$sql3 = "SELECT user_id, username FROM usuarios WHERE user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE curso_id = $cursoNum ) ";
				$registros3=mysql_query($sql3,$conexion) or die ("Problemas con el Select (sql3) ".mysql_error());
				$count3=mysql_num_rows($registros3);       //numero de cursos totales 	
				//echo "<br>Num.usuarios total = $count3";
				$pool_alumnos = 3;	
		}
	}
	//echo "<br>pool_alumnos = $pool_alumnos";



    ////buscamos los administradores matriculados en los cursos seleccionados
    if (!isset($_GET['stat_cursos']))        	//si no se han seleccionado cursos, entonces se muestran todos los administradores
	{
		$sql4 = "SELECT user_id, username FROM usuarios WHERE admin=1";
		$registros4=mysql_query($sql4,$conexion) or die ("Problemas con el Select (sql4) ".mysql_error());
		$count4=mysql_num_rows($registros4);       //numero de administradores 	
		//echo "<br>Num.administradores = $count4";
		$pool_admins = 0;
	}
	else
	{
	    // si se ha seleccionado la opcion Todos los cursos
	    if ($cursoNum == 0)
		{
			if ($statusNum == 0)     //cursos activos y pasados
			{
				$sql4 = "SELECT user_id, username FROM usuarios WHERE admin=1";
				$registros4=mysql_query($sql4,$conexion) or die ("Problemas con el Select (sql4) ".mysql_error());
				$count4=mysql_num_rows($registros4);       //numero de administradores 	
				//echo "<br>Num.administradores = $count4";
				$pool_admins = 0;
			}
			else if ($statusNum == 1)    // cursos activos
			{
				$sql4 = "SELECT user_id, username FROM usuarios WHERE admin=1 AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy'  ) ) ";
				$registros4=mysql_query($sql4,$conexion) or die ("Problemas con el Select (sql4) ".mysql_error());
				$count4=mysql_num_rows($registros4);       //numero de administradores 	
				//echo "<br>Num.administradores = $count4";
				$pool_admins = 1;
			}
			else   //($statusNum == 2)    //cursos pasados
			{
				$sql4 = "SELECT user_id, username FROM usuarios WHERE admin=1 AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy'  ) ) ";
				$registros4=mysql_query($sql4,$conexion) or die ("Problemas con el Select (sql4) ".mysql_error());
				$count4=mysql_num_rows($registros4);       //numero de administradores 	
				//echo "<br>Num.administradores = $count4";
				$pool_admins = 2;
			}
		}
		else     //un curso en particular
		{
				$sql4 = "SELECT user_id, username FROM usuarios WHERE admin=1 AND user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE curso_id = $cursoNum ) ";
				$registros4=mysql_query($sql4,$conexion) or die ("Problemas con el Select (sql4) ".mysql_error());
				$count4=mysql_num_rows($registros4);       //numero de administradores 	
				//echo "<br>Num.administradores = $count4";
				$pool_admins = 0;
		}
	}
	//echo "<br>pool_admins = $pool_admins";	

	
	

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/////////ACTUALIZAMOS LA TABLA RESERVAS, PARA QUE LAS ESTADISTICAS SEAN FIELES A LA REALIDAD ACTUAL//////////////
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//actualizamos las reservas que ya han pasado sin haber sido usadas  --> RESERVAS CADUCADAS (estado final -1)
	$duracionTurno = $_SESSION['duracionTurno'];
	if (!isset($duracionTurno))
		$duracionTurno = 2;
	$HorasTurno = floor($duracionTurno);
	$MinutosTurno = ($duracionTurno - $HorasTurno)*60;
	$numTurnosDia = $_SESSION['numTurnosDia'];
	$f_ahora = date('Y-m-d H:i:s');
	 
	$consulta11 = "SELECT * FROM reservas WHERE estado_reserva = 0";
	$registros11=mysql_query($consulta11,$conexion) or die ("Problemas con el Select (consulta11) ".mysql_error());
	$cuenta11 = mysql_num_rows($registros11);

	$consulta22 = "SELECT * FROM reservas WHERE estado_reserva = 2";
	$registros22=mysql_query($consulta22,$conexion) or die ("Problemas con el Select (consulta22) ".mysql_error());
	$cuenta22 = mysql_num_rows($registros22);
	
	if ($cuenta11 != 0){
	while ($reg11 = mysql_fetch_array($registros11)){
	
		  $status_reserva =  $reg11['estado_reserva'];					
		  $id_reserva = $reg11['reserva_id'];
		  
		  $dia_almacenado = $reg11['fecha_reserva'];
		  $hora_almacenada = $reg11['horario_reserva'];
		  $f_almacenada = date($dia_almacenado." ".$hora_almacenada);
		  //echo "<br>f_almacenada = $f_almacenada";
		  
		  $f_fin_reserva = date('Y-m-d H:i:s',strtotime('+'.$HorasTurno.' hours', strtotime($f_almacenada)));
		  if ($duracionTurno > $HorasTurno){
			$f_fin_temp = date('Y-m-d H:i:s',strtotime('+'.$MinutosTurno.' minutes', strtotime($f_fin_reserva)));
			$f_fin_reserva = $f_fin_temp;
		  }
		  //echo "<br>f_fin_reserva = $f_fin_reserva";	
		  
		  if ($f_ahora > $f_fin_reserva){
			$status_reserva = -1;							
			//echo "<br>status_reserva = $status_reserva<hr>";

			$actualizacion1="UPDATE reservas SET estado_reserva = $status_reserva WHERE reserva_id = $id_reserva"; 
			$resultado1=mysql_query($actualizacion1,$conexion) or die ("Problemas con el Update (actualizacion1) ".mysql_error());			   	
		  }

	  }
	}else{
	  //echo "<br>En este momento no hay ninguna reserva anterior no usada en el sistema.<br>";
	}
	//liberamos recursos
	mysql_free_result($registros11);
	

	if ($cuenta22 != 0){
	while ($reg22 = mysql_fetch_array($registros22)){
	
		  $status_reserva =  $reg22['estado_reserva'];					
		  $id_reserva = $reg22['reserva_id'];
		  
		  $dia_almacenado = $reg22['fecha_reserva'];
		  $hora_almacenada = $reg22['horario_reserva'];
		  $f_almacenada = date($dia_almacenado." ".$hora_almacenada);
		  //echo "<br>f_almacenada = $f_almacenada";
		  
		  $f_fin_reserva = date('Y-m-d H:i:s',strtotime('+'.$HorasTurno.' hours', strtotime($f_almacenada)));
		  if ($duracionTurno > $HorasTurno){
			$f_fin_temp = date('Y-m-d H:i:s',strtotime('+'.$MinutosTurno.' minutes', strtotime($f_fin_reserva)));
			$f_fin_reserva = $f_fin_temp;
		  }
		  //echo "<br>f_fin_reserva = $f_fin_reserva";	
		  
		  if ($f_ahora > $f_fin_reserva){
			$status_reserva = 1;
			
			   //echo "<br>status_reserva = $status_reserva<hr>";
		  
			   $actualizacion2="UPDATE reservas SET estado_reserva = $status_reserva WHERE reserva_id = $id_reserva"; 
			   $resultado2=mysql_query($actualizacion2,$conexion) or die ("Problemas con el Update (actualizacion2) ".mysql_error());	
		  }

	  }
	}else{
	  //echo "<br>En este momento no hay ninguna reserva anterior usada en el sistema.<br>";
	}
	//liberamos recursos
	mysql_free_result($registros22);
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////////////////////					
	
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
	 <title>ESTADISTICAS1</title>
	 <!-- Estas lineas evitan que la pagina deseada no se cargue en la cache y no pueda ser mostrada cuando se regresa atras. -->
	  <meta http-equiv="Expires" CONTENT="0">
	  <meta http-equiv="Cache-Control" CONTENT="no-cache">
	  <meta http-equiv="Pragma" CONTENT="no-cache">

	  
	 <script type="text/javascript">
		 function statistics()
		 {
		   document.forms["form_enviar"].submit();
		 }
	 </script>
	 

	      <script type="text/javascript">
     function asignar() {

		document.getElementById("Xstat_pods").value = document.getElementById("stat_pods").value;	 
		 
		if (document.getElementById("stat_pods").value == "1")
		{
		   <?php
				for ($i=0; $i<=$total_pods; $i++){
		   ?>
					if (document.getElementById("pod<?php echo $i ?>").checked) 
						document.getElementById("Xpod<?php echo $i ?>").value = "<?php echo $i ?>";					
		   <?php
				}
			?>
		}
		   
		   	
		flag_cursos = "<?php echo "$flag_cursos" ?>";
		if (flag_cursos == 1)
			document.getElementById("stat_cursos").value = 1;		
		
	    document.getElementById("Xstat_cursos").value = document.getElementById("stat_cursos").value;
				
		if (document.getElementById("stat_cursos").value == "1")
		{		
			if (document.getElementById("estadoID").value) 
			   document.getElementById("XestadoID").value = document.getElementById("estadoID").value;	

			if (document.getElementById("cursoID").value) 
			   document.getElementById("XcursoID").value = document.getElementById("cursoID").value;		
		}
		
		
		flag_usuarios = "<?php echo "$flag_usuarios" ?>";
		if (flag_usuarios == 1)
			document.getElementById("stat_usuarios").value = 1;
			
		document.getElementById("Xstat_usuarios").value = document.getElementById("stat_usuarios").value;

		if (document.getElementById("stat_usuarios").value == "1")
		{		
		   document.getElementById("XusuarioID").value = document.getElementById("usuarioID").value;			
		}	
		
		
		flag_tiempos = "<?php echo "$flag_tiempos" ?>";
		if (flag_tiempos == 1)
			document.getElementById("stat_tiempos").value = 1;
			
		document.getElementById("Xstat_tiempos").value = document.getElementById("stat_tiempos").value;
				   
		if (document.getElementById("stat_tiempos").value == "1")
		{		
		   document.getElementById("XtiempoID").value = document.getElementById("tiempoID").value;			
		}	
		

		flag_ultimasReservas = "<?php echo "$flag_ultimasReservas" ?>";
		if (flag_ultimasReservas == 1)
			document.getElementById("stat_reservas").value = 1;

		document.getElementById("Xstat_reservas").value = document.getElementById("stat_reservas").value;
		
		if (document.getElementById("stat_reservas").value == "1")
		{		
		   document.getElementById("XultimasReservasID").value = document.getElementById("ultimasReservasID").value;			
		}	

		
		flag_admins = "<?php echo "$flag_admins" ?>";
		if (flag_admins == 1)
			document.getElementById("stat_admins").value = 1;

		document.getElementById("Xstat_admins").value = document.getElementById("stat_admins").value;
		
		if (document.getElementById("stat_admins").value == "1")
		{		
		   document.getElementById("XadminsID").value = document.getElementById("adminsID").value;			
		}
		
	
		if (document.getElementById("stat_totales").checked) 
		   document.getElementById("Xstat_totales").value = "1";	
	   
 	 }
	 </script>
	 

     <script type="text/javascript">
     function habilitar_pods() {
        if (document.getElementById("stat_pods").checked)
		{
		   document.getElementById("stat_pods").value="1";
		   
		   <?php
				for ($i=0; $i<=$total_pods; $i++){
		   ?>
					document.getElementById("pod<?php echo $i ?>").disabled = "";			   
		   <?php
				}
		   ?>
		}
		else
		{
		   document.getElementById("stat_pods").value="";

		   <?php
				for ($i=0; $i<=$total_pods; $i++){
		   ?>
					document.getElementById("pod<?php echo $i ?>").disabled = "disabled";	
					document.getElementById("Xpod<?php echo $i ?>").value = "";
					document.getElementById("pod<?php echo $i ?>").value = "";
					document.getElementById("pod<?php echo $i ?>").checked = false;
		   <?php
				}
		   ?>
		}
 	 }
	 </script>


     <script type="text/javascript">
     function habilitar_cursos() {
        if (document.getElementById("stat_cursos").checked)
		{
		   document.getElementById("stat_cursos").value="1";
		   
		   document.getElementById("estadoID").disabled = "";	
		   document.getElementById("cursoID").disabled = "";
		}
		else
		{
		   document.getElementById("stat_cursos").value="";

		   document.getElementById("estadoID").disabled = "disabled";	
		   document.getElementById("cursoID").disabled = "disabled";
		   
		   document.getElementById("estadoID").value = "";	
		   document.getElementById("cursoID").value = "";
		   
		   document.getElementById("XestadoID").value = "";	
		   document.getElementById("XcursoID").value = "";	  
		}
 	 }
	 </script>


	 <script type="text/javascript">
     function habilitar_usuarios() {
        if (document.getElementById("stat_usuarios").checked)
		{
		   document.getElementById("stat_usuarios").value="1";
		   
		   document.getElementById("usuarioID").disabled = "";	
		}
		else
		{
		   document.getElementById("stat_usuarios").value="";
		   
		   document.getElementById("usuarioID").disabled = "disabled";	
		   
		   document.getElementById("usuarioID").value = "";
				   
		   document.getElementById("XusuarioID").value = "";	
		}
 	 }
	 </script>
	 
	 
     <script type="text/javascript">
     function habilitar_tiempos() {
        if (document.getElementById("stat_tiempos").checked)
		{
		   document.getElementById("stat_tiempos").value="1";
		   
		   document.getElementById("tiempoID").disabled = "";	
		}
		else
		{
		   document.getElementById("stat_tiempos").value="";

		   document.getElementById("tiempoID").disabled = "disabled";

		   document.getElementById("tiempoID").value = "";
		   
		   document.getElementById("XtiempoID").value = "";
		}
 	 }
	 </script>
	 

	 <script type="text/javascript">
     function habilitar_ultimas_reservas() {
        if (document.getElementById("stat_reservas").checked)
		{
		   document.getElementById("stat_reservas").value="1";
		   
		   document.getElementById("ultimasReservasID").disabled = "";	
		}
		else
		{
		   document.getElementById("stat_reservas").value="";

		   document.getElementById("ultimasReservasID").disabled = "disabled";

		   document.getElementById("ultimasReservasID").value = "";
		   
		   document.getElementById("XultimasReservasID").value = "";
		}
 	 }
	 </script>


	 <script type="text/javascript">
     function habilitar_admins() {
        if (document.getElementById("stat_admins").checked)
		{
		   document.getElementById("stat_admins").value="1";
		   
		   document.getElementById("adminsID").disabled = "";	
		}
		else
		{
		   document.getElementById("stat_admins").value="";

		   document.getElementById("adminsID").disabled = "disabled";

		   document.getElementById("adminsID").value = "";
		   
		   document.getElementById("XadminsID").value = "";
		}
 	 }
	 </script>	 

	 
	</head>


	<body bgcolor="#<?php echo $bgcolor ?>"> 
	
	<center><u><h1>ESTADISTICAS</h1></u></center>
 

 
	 <table RULES=NONE FRAME=BOX style="background: #ccffff">
	   <center><U><font style="width:100; background: #ccffff; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:14pt; font-weight:bold;  border: 1px solid #ccc;">
		 <tr>
			<form name="form_estadisticas" id="form_pods" action="" method="GET">
				<td><input type="checkbox" name="stat_pods" id="stat_pods" value="" onChange="habilitar_pods();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>	
				
				<td LABEL for="podX" ALIGN="right" style="font-size:14pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif">ESTADISTICAS POR NUM.POD: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</LABEL></td>
				<td width="200px"> <input type="checkbox" name="pod0" id="pod0" value="0" > Todos los PODS</td>
				<td>
				<?php
					for ($i=0; $i<$total_pods; $i++){
							$j=$i+1;
				?>
						<input type="checkbox" name="pod<?php echo $j ?>" id="pod<?php echo $j ?>" value="<?php echo $j ?>" > POD # <?php echo $j ?> &nbsp;&nbsp;&nbsp;&nbsp;
				<?php 
					} 
				?>
				</td>
		 </tr>
		 
		 <tr height="30px">
		 </tr> 
		 
		 <tr>

				<td><input type="checkbox" name="stat_cursos" id="stat_cursos" value="" onChange="habilitar_cursos();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				
				<td LABEL for="cursoX" ALIGN="right" style="font-size:14pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif">ESTADISTICAS POR CURSO: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</LABEL></td>
				<td  ALIGN="right" style="font-size:12pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif">ESTADO: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				
				<td colspan="3">
				   <SELECT name="estadoID" id="estadoID" style="width:200px; align=center; font-size:11pt; font-weight:bold; background:#ffffff;" onChange="form_estadisticas.submit();">
					   <option id="todos" value="0" style="font-size:11pt; font-weight:bold;  width:360px; border: 1px solid #ccc;" >Todos</option>				   
					   <option id="activo" value="1" style="font-size:11pt; font-weight:bold;  width:360px; border: 1px solid #ccc;" <?php if ($statusNum == 1) echo "selected"; ?> >Activos</option>
					   <option id="pasado" value="2" style="font-size:11pt; font-weight:bold;  width:360px; border: 1px solid #ccc;" <?php if ($statusNum == 2) echo "selected"; ?> >Pasados</option>
				   </SELECT>
				</td>
		 </tr>
		 
		 <tr height="20px">
		 </tr>
			 
		 <tr>
				
				<td colspan="2"></td>
				<td	ALIGN="right" style="font-size:12pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif">CURSO: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>			

				<td>
				   <SELECT name="cursoID" id="cursoID" style="width:200px; align=center; font-size:11pt; font-weight:bold; background:#ffffff;" onChange="form_estadisticas.submit();"> 			  

					  <option id="todos" value="0" style="font-size:11pt; font-weight:bold;  width:360px; border: 1px solid #ccc;" >Todos</option>
					  
					  <?php
						if ($statusNum == 1)    //cursos activos
						{
							for ($i=0; $i<$num_cursos_activos; $i++)
							{   
								echo "<option value='".$cursos_1[$i]."' "; if ($cursoNum == $cursos_1[$i]) echo "selected"; echo ">".$nombre_cursos_1[$i]."</option>";
							}	
						}
						else if ($statusNum == 2)                  //cursos pasados
						{
							for ($i=0; $i<$num_cursos_pasados; $i++)
							{   
								echo "<option value='".$cursos_2[$i]."' "; if ($cursoNum == $cursos_2[$i]) echo "selected"; echo ">".$nombre_cursos_2[$i]."</option>";
							}						
						}
						else             //todos los cursos
						{
							for ($i=0; $i<$num_cursos_totales; $i++)
							{   
								echo "<option value='".$cursos_0[$i]."' "; if ($cursoNum == $cursos_0[$i]) echo "selected"; echo ">".$nombre_cursos_0[$i]."</option>";
							}					
						}
					  ?>
					</SELECT>
				</td>			

		 </tr>
		 
		 <tr height="20px">
		 </tr> 
		 
		 <tr>

				<td><input type="checkbox" name="stat_usuarios" id="stat_usuarios" value="" onChange="habilitar_usuarios();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>	
				
				<td LABEL for="usuarioX" ALIGN="right" style="font-size:14pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif">ESTADISTICAS POR USUARIO: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</LABEL></td>
				<td  ALIGN="right" style="font-size:12pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif">ALUMNO: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				
				<td colspan="3">
				   <SELECT name="usuarioID" id="usuarioID" style="width:200px; align=center; font-size:11pt; font-weight:bold; background:#ffffff;" onChange="form_estadisticas.submit();">
					   <option id="todos" value="0" style="font-size:11pt; font-weight:bold;  width:360px; border: 1px solid #ccc;" >Todos</option>
					   
					  <?php
						if ($pool_alumnos == 0)    //el checkbox "curso" no esta habilitado --> se muestran todos los alumnos de todos los cursos
						{
							mysql_data_seek ( $registros3, 0);   //devolvemos el puntero a la posicion 0 de la lista
							while ($reg3=mysql_fetch_array($registros3))
							{        
								echo "<option value='".$reg3['user_id']."' "; if ($alumnoNum == $reg3['user_id']) echo "selected"; echo ">".$reg3['username']."</option>";
							}
						}	
					   	else if ($pool_alumnos == 1)    //se muestran todos los alumnos de los cursos activos
						{
							mysql_data_seek ( $registros3, 0);   //devolvemos el puntero a la posicion 0 de la lista
							while ($reg3=mysql_fetch_array($registros3))
							{         
								echo "<option value='".$reg3['user_id']."' "; if ($alumnoNum == $reg3['user_id']) echo "selected"; echo ">".$reg3['username']."</option>";
							}
						}	
						else if ($pool_alumnos == 2)     //se muestran todos los alumnos de los cursos pasados
						{
							mysql_data_seek ( $registros3, 0);   //devolvemos el puntero a la posicion 0 de la lista
							while ($reg3=mysql_fetch_array($registros3))
							{         
								echo "<option value='".$reg3['user_id']."' "; if ($alumnoNum == $reg3['user_id']) echo "selected"; echo ">".$reg3['username']."</option>";
							}
						}	
						else   //  ($pool_alumnos == 3)           //se muestran todos los alumnos de un curso en concreto, el seleccionado en cursos
						{
							mysql_data_seek ( $registros3, 0);   //devolvemos el puntero a la posicion 0 de la lista
							while ($reg3=mysql_fetch_array($registros3))
							{         
								echo "<option value='".$reg3['user_id']."' "; if ($alumnoNum == $reg3['user_id']) echo "selected"; echo ">".$reg3['username']."</option>";
							}
						}	
					  ?>
					</SELECT>
	 
		 </tr>
		 
		 <tr height="30px">
		 </tr> 
		 
		 <tr>

				<td><input type="checkbox" name="stat_tiempos" id="stat_tiempos" value="" onChange="habilitar_tiempos();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>	
				
				<td LABEL for="tiempoX" ALIGN="right" style="font-size:14pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif">ESTADISTICAS POR TIEMPOS: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</LABEL></td>
				<td  ALIGN="right" style="font-size:12pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif">ESCALA: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				
				<td colspan="3">
				   <SELECT name="tiempoID" id="tiempoID" style="width:200px; align=center; font-size:11pt; font-weight:bold; background:#ffffff;" onChange="form_estadisticas.submit();">
					   <option id="todos" value="0" style="font-size:11pt; font-weight:bold;  width:360px; border: 1px solid #ccc;" >Todos</option>				   
					   <option id="semanas" value="1" style="font-size:11pt; font-weight:bold;  width:360px; border: 1px solid #ccc;" <?php if ($escalaTempNum == 1) echo "selected"; ?> >por Semanas</option>
					   <option id="dias" value="2" style="font-size:11pt; font-weight:bold;  width:360px; border: 1px solid #ccc;" <?php if ($escalaTempNum == 2) echo "selected"; ?> >por D&iacute;as</option>
					   <option id="horas" value="3" style="font-size:11pt; font-weight:bold;  width:360px; border: 1px solid #ccc;" <?php if ($escalaTempNum == 3) echo "selected"; ?> >por Horas</option>
				   </SELECT>
				</td>
		 
		 </tr>

		 <tr height="30px">
		 </tr> 

		 <tr>

				<td><input type="checkbox" name="stat_admins" id="stat_admins" value="" onChange="habilitar_admins();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>	
				
				<td LABEL for="adminsX" ALIGN="right" style="font-size:14pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif">Estad&iacute;sticas por Administradores: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</LABEL></td>
				<td  ALIGN="right" style="font-size:12pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif">ADMIN: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				
				<td colspan="3">
				   <SELECT name="adminsID" id="adminsID" style="width:200px; align=center; font-size:11pt; font-weight:bold; background:#ffffff;" onChange="form_estadisticas.submit();">
					   <option id="todos" value="0" style="font-size:11pt; font-weight:bold;  width:360px; border: 1px solid #ccc;" >Todos</option>	
					
							<?php
								if ($pool_admins == 0)    //el checkbox "curso" no esta habilitado --> se muestran todos los administradores de todos los cursos
								{
									mysql_data_seek ( $registros4, 0);   //devolvemos el puntero a la posicion 0 de la lista
									while ($reg4=mysql_fetch_array($registros4))
									{        
										echo "<option value='".$reg4['user_id']."' "; if ($adminsNum == $reg4['user_id']) echo "selected"; echo ">".$reg4['username']."</option>";
									}
								}	
								else if ($pool_admins == 1)    //se muestran todos los administradores de los cursos activos
								{
									mysql_data_seek ( $registros4, 0);   //devolvemos el puntero a la posicion 0 de la lista
									while ($reg4=mysql_fetch_array($registros4))
									{         
										echo "<option value='".$reg4['user_id']."' "; if ($adminsNum == $reg4['user_id']) echo "selected"; echo ">".$reg4['username']."</option>";
									}
								}	
								else if ($pool_admins == 2)     //se muestran todos los administradores de los cursos pasados
								{
									mysql_data_seek ( $registros4, 0);   //devolvemos el puntero a la posicion 0 de la lista
									while ($reg4=mysql_fetch_array($registros4))
									{         
										echo "<option value='".$reg4['user_id']."' "; if ($adminsNum == $reg4['user_id']) echo "selected"; echo ">".$reg4['username']."</option>";
									}
								}	
								else   //  ($pool_admins == 3)           //se muestran todos los administradores de un curso en concreto, el seleccionado en cursos
								{
									mysql_data_seek ( $registros4, 0);   //devolvemos el puntero a la posicion 0 de la lista
									while ($reg4=mysql_fetch_array($registros4))
									{         
										echo "<option value='".$reg4['user_id']."' "; if ($adminsNum == $reg4['user_id']) echo "selected"; echo ">".$reg4['username']."</option>";
									}
								}	
							  ?>
				   </SELECT>
				</td>
				
		 </tr>
		 
		 <tr height="30px">
		 </tr> 
		 
		 <tr>

				<td><input type="checkbox" name="stat_reservas" id="stat_reservas" value="" onChange="habilitar_ultimas_reservas();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>	
				
				<td LABEL for="reservasX" ALIGN="right" style="font-size:14pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif">ULTIMAS RESERVAS: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</LABEL></td>
				<td  ALIGN="right" style="font-size:12pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif">PERIODO: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				
				<td colspan="3">
				   <SELECT name="ultimasReservasID" id="ultimasReservasID" style="width:200px; align=center; font-size:11pt; font-weight:bold; background:#ffffff;" onChange="form_estadisticas.submit();">
					   <option id="todos" value="0" style="font-size:11pt; font-weight:bold;  width:360px; border: 1px solid #ccc;" >Todos</option>				   
					   <option id="recientes" value="1" style="font-size:11pt; font-weight:bold;  width:360px; border: 1px solid #ccc;" <?php if ($lastResv == 1) echo "selected"; ?> >Recientes</option>
					   <option id="pendientes" value="2" style="font-size:11pt; font-weight:bold;  width:360px; border: 1px solid #ccc;" <?php if ($lastResv == 2) echo "selected"; ?> >Pendientes</option>
					   <option id="ejecucion" value="3" style="font-size:11pt; font-weight:bold;  width:360px; border: 1px solid #ccc;" <?php if ($lastResv == 3) echo "selected"; ?> >En uso ahora</option>
				   </SELECT>
				</td>
				
		 </tr>



		 <tr height="30px">
		 </tr> 
		 
		 <tr>
				<td><input type="checkbox" name="stat_totales" id="stat_totales" value="" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>	
				
				<td LABEL for="totalX" ALIGN="right" style="font-size:14pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif"><u>ESTADISTICAS GENERALES:</u> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</LABEL></td>
 		    </form>		 
		 </tr>
		  
	   </font></U></center>
	 </table>
	 
	 <br>
	 
	 <div align="center">
	   <INPUT type="button" name="enviar" value="ENVIAR" face="algerian" size="5" align="center" style="background-color : red; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:120px;" onClick="asignar(); statistics();" />
     </div>
	   
 
	 <form name="form_enviar" ID="form_enviar" action="estadisticas2.php" method="post">
	 		<input type="hidden" name="Xstat_pods" id="Xstat_pods" value=""> 
			  <?php
				 for ($i=0; $i<=$total_pods; $i++){
			  ?>
					 <input type="hidden" name="Xpod<?php echo $i ?>" id="Xpod<?php echo $i ?>" value="">
			  <?php
				  }
			  ?>

			<input type="hidden" name="Xstat_cursos" id="Xstat_cursos" value="">
			 <input type="hidden" name="XestadoID" id="XestadoID" value=""> 
			 <input type="hidden" name="XcursoID" id="XcursoID" value=""> 
		
			<input type="hidden" name="Xstat_usuarios" id="Xstat_usuarios" value=""> 
			 <input type="hidden" name="XusuarioID" id="XusuarioID" value=""> 
			
			<input type="hidden" name="Xstat_tiempos" id="Xstat_tiempos" value=""> 	
			 <input type="hidden" name="XtiempoID" id="XtiempoID" value="">			
			 
			<input type="hidden" name="Xstat_reservas" id="Xstat_reservas" value=""> 
			 <input type="hidden" name="XultimasReservasID" id="XultimasReservasID" value="">

			<input type="hidden" name="Xstat_admins" id="Xstat_admins" value=""> 
			 <input type="hidden" name="XadminsID" id="XadminsID" value="">
			 
			<input type="hidden" name="Xstat_totales" id="Xstat_totales" value=""> 

	  </form>
 
 
      <script type="text/javascript">
	   //si el checkbox de Estadisticas por Pod esta habilitado
       if (document.getElementById("stat_pods").value == 1){  
	       document.getElementById('stat_pods').checked = true;
			<?php
			    for ($i=0; $i<=$total_pods; $i++){
			?>
					document.getElementById('pod<?php echo $i ?>').disabled = "";
			<?php
				}
			?>		   
	   }else{    //si no lo esta
	       document.getElementById('stat_pods').checked = false;
			<?php
			    for ($i=0; $i<=$total_pods; $i++){
			?>
					document.getElementById('pod<?php echo $i ?>').disabled = "disabled";
			<?php
				}
			?>	
	   }
	 </script> 


	  <script type="text/javascript">
	   //si el checkbox de Estadisticas por Curso esta habilitado
       if (document.getElementById("stat_cursos").value == 1){  
	       document.getElementById('stat_cursos').checked = true;
		   document.getElementById('estadoID').disabled = "";	
		   document.getElementById('cursoID').disabled = "";
	   }else{    //si no lo esta
	       document.getElementById('stat_cursos').checked = false;
		   document.getElementById('estadoID').disabled = "disabled";	
		   document.getElementById('cursoID').disabled = "disabled";
	   }
	 </script> 


	  <script type="text/javascript">
	   //si el checkbox de Estadisticas por Usuario esta habilitado
       if (document.getElementById("stat_usuarios").value == 1){  
	       document.getElementById('stat_usuarios').checked = true;
		   document.getElementById('usuarioID').disabled = "";	
	   }else{    //si no lo esta
	       document.getElementById('stat_usuarios').checked = false;
		   document.getElementById('usuarioID').disabled = "disabled";	
	   }
	 </script> 


	  <script type="text/javascript">
	   //si el checkbox de Estadisticas por Tiempos esta habilitado
       if (document.getElementById("stat_tiempos").value == 1){  
	       document.getElementById('stat_tiempos').checked = true;
		   document.getElementById('tiempoID').disabled = "";	
	   }else{    //si no lo esta
	       document.getElementById('stat_tiempos').checked = false;
		   document.getElementById('tiempoID').disabled = "disabled";	
	   }
	 </script> 

	 
	 <script type="text/javascript">
	   //si el checkbox de Ultimas Reservas esta habilitado
       if (document.getElementById("stat_reservas").value == 1){  
	       document.getElementById('stat_reservas').checked = true;
		   document.getElementById('ultimasReservasID').disabled = "";	
	   }else{    //si no lo esta
	       document.getElementById('stat_reservas').checked = false;
		   document.getElementById('ultimasReservasID').disabled = "disabled";	
	   }
	 </script> 


     <script type="text/javascript">
	   //si el checkbox de Estadisticas por Administrador esta habilitado
       if (document.getElementById("stat_admins").value == 1){  
	       document.getElementById('stat_admins').checked = true;
		   document.getElementById('adminsID').disabled = "";	
	   }else{    //si no lo esta
	       document.getElementById('stat_admins').checked = false;
		   document.getElementById('adminsID').disabled = "disabled";	
	   }
	 </script> 
	 
	 
	 <?php
	 if (isset($_GET['stat_cursos'])){
	 ?>
	   	  <script type="text/javascript">
		     document.getElementById('stat_cursos').checked = true;
		     document.getElementById('estadoID').disabled = "";	
		     document.getElementById('cursoID').disabled = "";
		  </script> 
	 <?php
	 }
	 ?>

	 
	 <?php
	 if (isset($_GET['stat_usuarios'])){
	 ?>
	   	  <script type="text/javascript">
		     document.getElementById('stat_usuarios').checked = true;
		     document.getElementById('usuarioID').disabled = "";	
		  </script> 
	 <?php
	 }
	 ?>


	 <?php
	 if (isset($_GET['stat_tiempos'])){
	 ?>
	   	  <script type="text/javascript">
		     document.getElementById('stat_tiempos').checked = true;
		     document.getElementById('tiempoID').disabled = "";	
		  </script> 
	 <?php
	 }
	 ?>
 
	 
	 <?php
	 if (isset($_GET['stat_reservas'])){
	 ?>
	   	  <script type="text/javascript">
		     document.getElementById('stat_reservas').checked = true;
		     document.getElementById('ultimasReservasID').disabled = "";	
		  </script> 
	 <?php
	 }
	 ?>


	 <?php
	 if (isset($_GET['stat_admins'])){
	 ?>
	   	  <script type="text/javascript">
		     document.getElementById('stat_admins').checked = true;
		     document.getElementById('adminsID').disabled = "";	
		  </script> 
	 <?php
	 }
	 ?>
	 
	 
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

	
	<center>

			<!--[if lt IE 9]>
				<IMG SRC="fotos/R&S4.png" name="logo" id="logo" alt="logo" width="200px" height="100px" onContextMenu="return(false)">			
			<![endif]-->

			<!--[if gte IE 9]>			   
			   <?php // convert image to dataURL
				 $img_source = "fotos/R&S4.png"; // image path/name
				 $img_binary = fread(fopen($img_source, "r"), filesize($img_source));
				 $img_string = base64_encode($img_binary);
			   ?>
			   <IMG SRC="data:image/gif;base64,<?php echo $img_string; ?>" name="logo" id="logo" alt="logo" width="200px" height="100px">
			<![endif]-->
			
			<!--[if !IE]>-->
			   <?php // convert image to dataURL
				 $img_source = "fotos/R&S4.png"; // image path/name
				 $img_binary = fread(fopen($img_source, "r"), filesize($img_source));
				 $img_string = base64_encode($img_binary);
			   ?>
			   <IMG SRC="data:image/gif;base64,<?php echo $img_string; ?>" name="logo" id="logo" alt="logo" width="200px" height="100px">			
			<!--<![endif]-->
			
	</center>
	
	
	</body>
	</html>

	
<?php
	mysql_free_result($registros0);
	mysql_free_result($registros1);
	mysql_free_result($registros2);
	mysql_free_result($registros3);
	mysql_free_result($registros4);
	mysql_close($conexion);
	ob_end_flush();		
}
?>