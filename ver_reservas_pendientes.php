<?php 
 include ("headers/seguridad.php");
 include ("headers/conexion.php");

//iniciamos el buffer de salida 
ob_start();

$admin = $_SESSION['admin'];
$idle_timeout = $_SESSION['idle_timeout'];

$duracionTurno = $_SESSION['duracionTurno'];
$HorasTurno = floor($duracionTurno);
$MinutosTurno = ($duracionTurno - $HorasTurno)*60;
$numTurnosDia = $_SESSION['numTurnosDia'];
$f_ahora = date('Y-m-d H:i:s');

////fecha y hora del turno actual
$hoy = date('Y-m-d');
$hora_actual = date('H');
$hora_par = (floor($hora_actual/2)*2);
//echo "<br>hora_par = $hora_par";
if ($hora_par < 10)
	$inicio_turno = "0".$hora_par.":00:00";
else
	$inicio_turno = $hora_par.":00:00";
//echo "<br>inicio_turno = $inicio_turno";
$$datetime_inicio_turno_actual = date('Y-m-d H:i:s',strtotime('+ '.$hora_par.' hours', strtotime($hoy)));



//Se comprueba si el usuario tiene privilegios de administrador
if ($_SESSION['admin'] == 0) { 
     session_destroy();
    //si no tiene privilegios de administrador, se devuelve a la pagina inicial 
    header("Location: index.php?errorusuario=ad"); 
} else { 

	////ACTUALIZAMOS RESERVAS que ya han pasado y que han sido usadas --> RESERVAS USADA (estado_reserva = 1)
	$sql01="SELECT * FROM reservas WHERE estado_reserva = 2"; 
	$registros01=mysql_query($sql01,$conexion) or die ("Problemas con el Select (sql01) ".mysql_error());
	$count01 = mysql_num_rows($registros01);
	//echo "<br>Total de reservas acabadas de usar = $count01<hr>";

	if ($count01 != 0){
	  while ($reg01 = mysql_fetch_array($registros01)){

		  $id_reserva = $reg01['reserva_id'];
		  
		  $dia_almacenado = $reg01['fecha_reserva'];
		  $hora_almacenada = $reg01['horario_reserva'];
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
				//echo "<br>status_reserva = $status_reserva<hr>";
			  
				$sql02="UPDATE reservas SET estado_reserva = 1 WHERE reserva_id = $id_reserva"; 
				$registros02=mysql_query($sql02,$conexion) or die ("Problemas con el Update (sql02) ".mysql_error());	
		  }
	  }
	}else{
	  //echo "<br>En este momento no hay ninguna reserva desactualizada.<br>";
	}
	//liberamos recursos
	mysql_free_result($registros01);



	////ACTUALIZAMOS RESERVAS que ya han pasado sin haber sido usadas --> RESERVAS CADUCADAS (estado_reserva = -1)
	$sql03="SELECT * FROM reservas WHERE estado_reserva = 0"; 
	$registros03=mysql_query($sql03,$conexion) or die ("Problemas con el Select (sql03) ".mysql_error());
	$count03 = mysql_num_rows($registros03);
	//echo "<br>Total de reservas activas = $count1<hr>";

	if ($count03 != 0){
	  while ($reg03 = mysql_fetch_array($registros03)){

		  $status_reserva =  $reg03['estado_reserva'];
		
		  $id_reserva = $reg03['reserva_id'];
		  
		  $dia_almacenado = $reg03['fecha_reserva'];
		  $hora_almacenada = $reg03['horario_reserva'];
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
		  
		  $sql04="UPDATE reservas SET estado_reserva = $status_reserva WHERE reserva_id = $id_reserva"; 
		  $registros04=mysql_query($sql04,$conexion) or die ("Problemas con el Update (sql04) ".mysql_error());	

	  }
	}else{
	  //echo "<br>En este momento no hay ninguna reserva en el sistema.<br>";
	}
	//liberamos recursos
	mysql_free_result($registros03);

	
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
	 <title>VER RESERVAS PENDIENTES</title>
 	 <!-- Estas lineas evitan que la pagina deseada no se cargue en la cache y no pueda ser mostrada cuando se regresa atras. -->
	  <meta http-equiv="Expires" CONTENT="0">
	  <meta http-equiv="Cache-Control" CONTENT="no-cache">
	  <meta http-equiv="Pragma" CONTENT="no-cache">
	
	</head>


	<body bgcolor="#<?php echo $bgcolor ?>"> 
 
 
<?php
	//comprobamos la opcion de MODO: bien TODAS, que muestra las reservas de todos los cursos a la vez, ordenadas cronologicamente, 
	//  o bien POR CURSOS, que muestra las reservas de cada curso por separado, ordenadas cronologicamente,
	
   if (!isset($_GET['catID']))
      $catNum = 0;
   else
      $catNum = $_GET['catID'];
               
	
	///nombres de los cursos implicados
	$sql="SELECT * FROM cursos";
	$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
	$count = mysql_num_rows($registros);
	$j = 0;
	if ($count > 0)
	{		
		while ($reg = mysql_fetch_array($registros))
		{
		   $elenco_id_cursos[$j] = $reg['curso_id'];
		   $elenco_cursos[$j] = $reg['nombre_curso'];
		   $j++;
		}		  
	}
	mysql_free_result($registros);

	///nombre de los usuarios de esos cursos implicados
	$sql="SELECT * FROM usuarios";
	$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
	$count = mysql_num_rows($registros);
	$j = 0;
	if ($count > 0)
	{		
		while ($reg = mysql_fetch_array($registros))
		{
		   $elenco_id_usuarios[$j] = $reg['user_id'];
		   $elenco_usuarios[$j] = $reg['username'];
		   $j++;
		}		  
	}
	mysql_free_result($registros);
		
		
	
	/////si queremos mostrar las reservas pendientes SEPARADAS por CURSOS, ordenadas cronologicamente
	if ($catNum == 2)
	{	
		
		//consulta --> Listado de Alumnos en Cursos Activos
		$hoy = date('Y-m-d');
		
		$sql1 = "SELECT curso_id, nombre_curso FROM cursos WHERE fin_curso >= '$hoy'";
		$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el Select (sql1) ".mysql_error());
		$count1=mysql_num_rows($registros1);       //numero de cursos activos 	
		//echo "Num.cursos activos = $count1";
		
		// calculamos el total de reservas pendientes
		$sql3="SELECT * FROM reservas WHERE estado_reserva = 0 OR estado_reserva = 2 ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC";
		$registros3=mysql_query($sql3,$conexion) or die ("Problemas con el select  (sql3) ".mysql_error());
		$count3 = mysql_num_rows($registros3);
		mysql_free_result($registros3);
?>

        <input type="text" name="totalActivos" value="<?php echo " Num.cursos activos = $count1"?>" 
 		id="totalActivos" style="width: 190px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  
		
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		
		<input type="text" name="totalReservasPendientes" value="<?php echo " Num.reservas pendientes totales = $count3"?>" 
		id="totalCurso" style="width: 300px; background: khaki; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  


<?php			
		if ($count1 > 0)
		{
			while ($reg1 = mysql_fetch_array($registros1))
			{	
				$nombre_curso = $reg1['nombre_curso'];
				$curso_id = $reg1['curso_id'];
				echo "<center><U><H3> $nombre_curso </H3></U></center>";
									
				// seleccionamos las fechas de las proximas reservas en un curso en particular
				$sql2="SELECT * FROM reservas WHERE (estado_reserva = 0 OR estado_reserva = 2) AND curso_id = $curso_id ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC";
				$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
				$count2 = mysql_num_rows($registros2);
				$j = 0;
				if ($count2 > 0){
				  while ($reg2 = mysql_fetch_array($registros2)){
					   $fecha[$j] = $reg2['fecha_reserva'];
					   $hora[$j] = $reg2['horario_reserva'];
					   $user_id[$j] = $reg2['user_id'];
					   $curso_id[$j] = $reg2['curso_id'];
					   $numero_pod[$j] = $reg2['num_POD'];
					   $estado_reserva[$j] = $reg2['estado_reserva'];
					   $j++;
				  }
				}  

				if ($count2 > 0)
					$altura2 = $count2;
				else
					$altura2 = 1;	
?>				

				<center>
					<table border="1" cellpadding="1" cellspacing="0" width="83%">
						<tr>
							<th width="18%" rowspan="<?php echo "$altura2"; ?>" bgcolor="lightcyan"> reservas PENDIENTES</th>
<?php					
							if ($count2 > 0)
							{
								for ($i=0; $i<$count2; $i++)
								{
									$j = $i+1;

									if ($fecha[$i]==$hoy && $hora[$i]==$inicio_turno)					
										$turno_actual=1;						
									else		
										$turno_actual=0;
		
									for ($k=0; $k<count($elenco_id_usuarios); $k++)
										if ($user_id[$i] == $elenco_id_usuarios[$k])	
											 $user = $k;

									for ($k=0; $k<count($elenco_id_cursos); $k++)
									   if ($curso_id[$i] == $elenco_id_cursos[$k])
											$course = $k;
									
									echo "<td width=\"65%\" "; echo ($turno_actual) ? "bgcolor=\"#ffccc\""  : "bgcolor=\"#ffffff\""; echo " align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $j.- $fecha[$i] a las $hora[$i] en el <i>POD #$numero_pod[$i]</i> para el usuario <b><i>$elenco_usuarios[$user]</i></b> con el curso <i>$elenco_cursos[$course]</i> "; if ($turno_actual){ echo ($estado_reserva[$i]) ? "  --> TURNO ACTUAL: <u>en USO</u>" : "  --> TURNO ACTUAL: <u>en ESPERA</u>"; }; echo "</td></tr>";
									
									//echo "<td width=\"65%\" bgcolor=\"#fcfcfc\" align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $j.- $fecha[$i] a las $hora[$i] en el <i>POD #$numero_pod[$i]</i> para el usuario <b><i>$elenco_usuarios[$user]</i></b> con el curso <i>$elenco_cursos[$course]</i> </td></tr>";

									//echo "<td width=\"55%\"> &nbsp; $j.- $fecha[$i] a las $hora[$i] en el POD #$numero_pod[$i] para el usuario $user_id[$i] con el curso $curso_id[$i].</td></tr>";
								}
							}
							else
							{
								echo "<td width=\"65%\" bgcolor=\"#fcfcfc\" align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; No hay reservas pendientes en este curso</td></tr>";
							}				
?>		
						</tr>
					</table>
				</center>
				<br>
				<input type="text" name="totalCurso" value="<?php echo " Num.usuarios en el curso $nombre_curso = $count2"?>" 
				id="totalCurso" style="width: 380px; background: lightblue; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  
<?php			
			}
			echo "<p>";
				
		}else{
			//echo "<br>No hay cursos activos en este momento<br>";
?>
            <br> 			
			<input type="text" name="totalCurso" value="<?php echo " No hay cursos activos en este momento"?>" 
			id="totalCurso" style="width: 300px; background: lightblue; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  	       
<?php
		}	
	}
	
	/////si queremos mostrar TODAS las reservas pendientes ordenadas cronologicamente, sin importar el curso al cual estan adscritas
	else
	{
		//consulta --> Listado de Alumnos en Cursos Activos
		$hoy = date('Y-m-d');
		
		$sql1 = "SELECT curso_id, nombre_curso FROM cursos WHERE fin_curso >= '$hoy'";
		$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el Select (sql1) ".mysql_error());
		$count1=mysql_num_rows($registros1);       //numero de cursos activos 	
		//echo "Num.cursos activos = $count1";

		if ($count1 > 0)
		{	
			while ($reg1 = mysql_fetch_array($registros1))
			{	
				// seleccionamos las fechas de las proximas reservas
				$sql2="SELECT * FROM reservas WHERE estado_reserva = 0 OR estado_reserva = 2 ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC";
				$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
				$count2 = mysql_num_rows($registros2);
				$j = 0;
				if ($count2 > 0){
				  while ($reg2 = mysql_fetch_array($registros2)){
					   $fecha[$j] = $reg2['fecha_reserva'];
					   $hora[$j] = $reg2['horario_reserva'];
					   $user_id[$j] = $reg2['user_id'];
					   $curso_id[$j] = $reg2['curso_id'];
					   $numero_pod[$j] = $reg2['num_POD'];
					   $estado_reserva[$j] = $reg2['estado_reserva'];
					   $j++;
				  }
				}  
									
			}
			
			if ($count2 > 0)
				$altura2 = $count2;
			else
				$altura2 = 1;
?>	

        <input type="text" name="totalActivos" value="<?php echo " Num.cursos activos = $count1"?>" 
 		id="totalActivos" style="width: 190px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  

		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		
		<input type="text" name="totalReservasPendientes" value="<?php echo " Num.reservas pendientes totales = $count2"?>" 
		id="totalCurso" style="width: 300px; background: khaki; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  

		<br><br><br>
		
			<center>
				<table border="1" cellpadding="1" cellspacing="0" width="83%">
					<tr>
						<th width="18%" rowspan="<?php echo "$altura2"; ?>" bgcolor="lightcyan"> reservas PENDIENTES</th>
<?php					
						if ($count2 > 0)
						{
							for ($i=0; $i<$count2; $i++)
							{
								$j = $i+1;

								if ($fecha[$i]==$hoy && $hora[$i]==$inicio_turno)					
									$turno_actual=1;						
								else		
									$turno_actual=0;
										
								for ($k=0; $k<count($elenco_id_usuarios); $k++)
									if ($user_id[$i] == $elenco_id_usuarios[$k])	
										 $user = $k;

								for ($k=0; $k<count($elenco_id_cursos); $k++)
								   if ($curso_id[$i] == $elenco_id_cursos[$k])
										$course = $k;
								
								echo "<td width=\"65%\" "; echo ($turno_actual) ? "bgcolor=\"#ffcccc\""  : "bgcolor=\"#ffffff\""; echo " align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $j.- $fecha[$i] a las $hora[$i] en el <i>POD #$numero_pod[$i]</i> para el usuario <b><i>$elenco_usuarios[$user]</i></b> con el curso <i>$elenco_cursos[$course]</i> "; if ($turno_actual){ echo ($estado_reserva[$i]) ? "  --> TURNO ACTUAL: <u>en USO</u>" : "  --> TURNO ACTUAL: <u>en ESPERA</u>"; }; echo "</td></tr>";
								
								//echo "<td width=\"65%\" bgcolor=\"#fcfcfc\" align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $j.- $fecha[$i] a las $hora[$i] en el <i>POD #$numero_pod[$i]</i> para el usuario <b><i>$elenco_usuarios[$user]</i></b> con el curso <i>$elenco_cursos[$course]</i> </td></tr>";

								//echo "<td width=\"55%\"> &nbsp; $j.- $fecha[$i] a las $hora[$i] en el POD #$numero_pod[$i] para el usuario $user_id[$i] con el curso $curso_id[$i].</td></tr>";
							}
						}
						else
						{
							echo "<td width=\"65%\" bgcolor=\"#fcfcfc\" align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; No hay reservas pendientes en el sistema</td></tr>";
						}				
?>		
					</tr>
				</table>
			</center>
			<br>


<?php
			
			echo "<p>";
		}else{
			//echo "<br>No hay cursos activos en este momento<br><br>";
?>
                <br>
				<input type="text" name="totalCurso" value="<?php echo " No hay cursos activos en este momento"?>" 
				id="totalCurso" style="width: 300px; background: lightblue; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  
			    
			   
<?php
		}
	}
?>
 
 
 
 	<form name="catSelect" action="" method="GET">
      <div align="center">
	    <LABEL for="modos" ALIGN="right" style="font-size:12pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif">MODO: &nbsp;&nbsp;</LABEL>
	    <SELECT name="catID" id="catID" style="width:200px; align=center; font-size:11pt; font-weight:bold; background:#ffffff;" onChange="catSelect.submit();"> 
		   <!-- <option id="encabezado" value="0">Elige opci&oacute;n</option> -->
	       <option id="resumen" value="1" >Todas</option>
		   <option id="detalles" value="2" <?php if ($catNum == 2) echo "selected"; ?> >por Cursos</option>
		</SELECT>
	  </div>
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
	mysql_free_result($registros1);
	mysql_free_result($registros2);
	mysql_close($conexion);
	ob_end_flush();		
}
?>