<?php 
 include ("headers/seguridad.php");
 include ("headers/conexion.php");

//iniciamos el buffer de salida 
ob_start();

$admin = $_SESSION['admin'];
$idle_timeout = $_SESSION['idle_timeout'];
$hoy = date('Y-m-d');


//Se comprueba si el usuario tiene privilegios de administrador
if ($_SESSION['admin'] == 0) { 
     session_destroy();
    //si no tiene privilegios de administrador, se devuelve a la pagina inicial 
    header("Location: index.php?errorusuario=ad"); 
} else { 

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
	 <title>VER MANTENIMIENTOS PENDIENTES</title>
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
		/////datos por cursos activos con pods
		
		///cursos de experto	 
		$sql="SELECT * FROM cursos WHERE curso_activo = 1 AND inicio_curso <= CURDATE() AND fin_curso >= CURDATE() AND num_max_pods > 0 AND nombre_curso LIKE '%Experto%'";
		$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
		$count = mysql_num_rows($registros);
		$num_cursos_en_ejecucion = $count;
		$j = 0;
		if ($count > 0)
		{			
			while ($reg = mysql_fetch_array($registros))
			{
			    $elenco_id_cursos[$j] = $reg['curso_id'];
			    $elenco_cursos[$j] = $reg['nombre_curso'];
				$array_num_max_pods[$j] = $reg['num_max_pods'];
			    $j++;
			}		  
		}
		mysql_free_result($registros);
				
		///cursos aparte de los de experto
		$sql="SELECT * FROM cursos WHERE curso_activo = 1 AND inicio_curso <= CURDATE() AND fin_curso >= CURDATE() AND num_max_pods > 0 AND nombre_curso NOT LIKE '%Experto%'";
		$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
		$count = mysql_num_rows($registros);
		$num_cursos_en_ejecucion += $count;
		if ($count > 0)
		{	
			while ($reg = mysql_fetch_array($registros))
			{
			    $elenco_id_cursos[$j] = $reg['curso_id'];
			    $elenco_cursos[$j] = $reg['nombre_curso'];
				$array_num_max_pods[$j] = $reg['num_max_pods'];
			    $j++;
			}		  
		}
		mysql_free_result($registros);
		
		
		///asignacion de pods a sus cursos correspondientes
		$offset_pod = 0;  

		for ($j=0; $j<$num_cursos_en_ejecucion; $j++) {
			////si es el curso ya esta en ejecucion
			if (($array_inicio_curso[$j]<=$hoy) && ($array_fin_curso[$j]>=$hoy)){  
			
				$num = $j + 1;
				//echo "<br>CURSO $array_nombre_curso[$j]:  PODS ASIGNADOS: ";
				$str = '';
				
				for ($k=0; $k<$array_num_max_pods[$j]; $k++){
					$num = ($k + 1) + $offset_pod;
					//echo " $num ";
					$str = $str . "&nbsp; $num &nbsp;";
				}
				
				$offset_pod = $num;
				$array_num_pods[$j] = $str;   
				//echo "array_num_pods[$j] = $array_num_pods[$j]";
			}else{
				$array_num_pods[$j] = "-";
			}
		}
			

	
	
	/////////////////////////////////////////////////////////////////////
	//for ($i=0; $i<count($elenco_cursos); $i++){
	//			echo "<br>$i CURSO: $elenco_cursos[$i]";
	//}
	//echo "<br>";		
	/////////////////////////////////////////////////////////////

	
	/////si queremos mostrar las reservas pendientes SEPARADAS por CURSOS, ordenadas cronologicamente
	if ($catNum == 2)
	{	
		
		// calculamos el total de turnos de mantenimiento pendientes
		$sql3="SELECT * FROM mantenimiento WHERE estado_outage = 0 ORDER BY fecha_outage ASC, horario_outage ASC, num_POD_outage ASC";
		$registros3=mysql_query($sql3,$conexion) or die ("Problemas con el select  (sql3) ".mysql_error());
		$count3 = mysql_num_rows($registros3);
		mysql_free_result($registros3);
?>

        <input type="text" name="totalActivos" value="<?php echo " Num.cursos activos = $num_cursos_en_ejecucion"?>" 
 		id="totalActivos" style="width: 190px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  
		
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		
		<input type="text" name="totalReservasPendientes" value="<?php echo " Num.turnos mant. pendientes totales = $count3"?>" 
		id="totalCurso" style="width: 330px; background: khaki; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  


<?php			
		if ($num_cursos_en_ejecucion > 0)
		{
			$index=0;
			$offset_pod = 0; 
			for($n=0;$n<$num_cursos_en_ejecucion;$n++)
			{	
				$nombre_curso = $elenco_cursos[$n];  
				$curso_id = $elenco_id_cursos[$n];
				echo "<center><U><H3> $nombre_curso </H3></U></center>";
				
				$num_max_pods = $array_num_max_pods[$n];
				$pod_inicial[$index] = $offset_pod + 1;
				$pod_final[$index] = $offset_pod + $num_max_pods;

				//echo "<br>nombre_curso=$nombre_curso -- curso_id=$curso_id -- num_max_pods=$num_max_pods -- pod_inicial[$index] = $pod_inicial[$index] -- pod_final[$index]=$pod_final[$index]<br>";
					
					
				// seleccionamos las fechas de las proximas reservas en un curso en particular
				$sql2="SELECT * FROM mantenimiento WHERE estado_outage = 0 AND num_POD_outage BETWEEN $pod_inicial[$index] AND $pod_final[$index] ORDER BY fecha_outage ASC, horario_outage ASC, num_POD_outage ASC";
				$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
				$count2 = mysql_num_rows($registros2);
				$j = 0;
				if ($count2 > 0){
				  while ($reg2 = mysql_fetch_array($registros2)){
					   $fecha[$j] = $reg2['fecha_outage'];
					   $hora[$j] = $reg2['horario_outage'];
					   $numero_pod[$j] = $reg2['num_POD_outage'];
					   $j++;
				  }
				}  
				
				if ($count2 > 0)
					$altura2 = $count2;
				else
					$altura2 = 1;	
					
				$offset_pod = $pod_final[$index];
				$index++;
?>				

				<center>
					<table border="1" cellpadding="1" cellspacing="0" width="65%">
						<tr>
							<th width="18%" rowspan="<?php echo "$altura2"; ?>" bgcolor="lightcyan"> turnos MANT. PENDIENTES</th>
<?php					
							if ($count2 > 0)
							{
								for ($i=0; $i<$count2; $i++)
								{
									$j = $i+1;

																	//echo "numero_pod[$i] = $numero_pod[$i] -->  ";		
									for ($k=0; $k<$index; $k++){
																	//echo " pod_inicial[$k]=$pod_inicial[$k] *** pod_final[$k]=$pod_final[$k] // ";
									   if ( ($pod_inicial[$k] <= $numero_pod[$i]) && ($numero_pod[$i] <= $pod_final[$k]) ){
											$course = $k;
											//echo "course = $course ///";
										}
									}
									
									echo "<td width=\"65%\" bgcolor=\"#fcfcfc\" align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp; $j.- $fecha[$i] a las $hora[$i] en el <i>POD #$numero_pod[$i]</i> con el curso <b><i>$elenco_cursos[$course]</i></b> </td></tr>";

									//echo "<td width=\"55%\"> &nbsp; $j.- $fecha[$i] a las $hora[$i] en el POD #$numero_pod[$i] para el usuario $user_id[$i] con el curso $curso_id[$i].</td></tr>";
								}
							}
							else
							{
								echo "<td width=\"55%\" bgcolor=\"#fcfcfc\"> &nbsp; No hay turnos de mant. pendientes en este curso</td></tr>";
							}				
?>		
						</tr>
					</table>
				</center>
				<br>
				<input type="text" name="totalCurso" value="<?php echo " Num.turnos de mant. en el curso $nombre_curso = $count2"?>" 
				id="totalCurso" style="width: 440px; background: lightblue; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  
<?php			
			}
			echo "<p>";
				
		}else{
			//echo "<br>No hay cursos activos en este momento<br>";
?>
            <br> 			
			<input type="text" name="totalCurso" value="<?php echo " No hay cursos activos en este momento"?>" 
			id="totalCurso" style="width: 310px; background: lightblue; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  	       
<?php
		}	
	}
	
	/////si queremos mostrar TODAS las reservas pendientes ordenadas cronologicamente, sin importar el curso al cual estan adscritas
	else
	{

		if ($num_cursos_en_ejecucion > 0)
		{
			$index=0;
			$offset_pod = 0;
			for($i=0;$i<$num_cursos_en_ejecucion;$i++)
			{	
				$nombre_curso = $elenco_cursos[$i];
				$curso_id = $elenco_id_cursos[$i];
				//echo "<center><U><H3> $nombre_curso </H3></U></center>";
				
				$num_max_pods = $array_num_max_pods[$i];
				$pod_inicial[$index] = $offset_pod + 1;
				$pod_final[$index] = $offset_pod + $num_max_pods;

				//echo "<br>nombre_curso=$nombre_curso -- curso_id=$curso_id -- num_max_pods=$num_max_pods -- pod_inicial[$index] = $pod_inicial[$index] -- pod_final[$index]=$pod_final[$index]<br>";
				$offset_pod = $pod_final[$index];
				$index++;
				
				// seleccionamos las fechas de los proximos turnos de mantenimiento
				$sql2="SELECT * FROM mantenimiento WHERE estado_outage = 0 ORDER BY fecha_outage ASC, horario_outage ASC, num_POD_outage ASC";
				$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
				$count2 = mysql_num_rows($registros2);
				$j = 0;
				if ($count2 > 0){
				  while ($reg2 = mysql_fetch_array($registros2)){
					   $fecha[$j] = $reg2['fecha_outage'];
					   $hora[$j] = $reg2['horario_outage'];
					   $numero_pod[$j] = $reg2['num_POD_outage'];
					   $j++;
				  }
				}  
									
			}
			
			if ($count2 > 0)
				$altura2 = $count2;
			else
				$altura2 = 1;
?>	

        <input type="text" name="totalActivos" value="<?php echo " Num.cursos activos = $num_cursos_en_ejecucion"?>" 
 		id="totalActivos" style="width: 190px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  

		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		
		<input type="text" name="totalReservasPendientes" value="<?php echo " Num.turnos mant. pendientes totales = $count2"?>" 
		id="totalCurso" style="width: 330px; background: khaki; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  

		<br><br><br>
		
			<center>
				<table border="1" cellpadding="1" cellspacing="0" width="65%">
					<tr>
						<th width="18%" rowspan="<?php echo "$altura2"; ?>" bgcolor="lightcyan"> turnos MANT. PENDIENTES</th>
<?php					
						if ($count2 > 0)
						{
							for ($i=0; $i<$count2; $i++)
							{
								$j = $i+1;

	
																//echo "numero_pod[$i] = $numero_pod[$i] -->  ";		
								for ($k=0; $k<$index; $k++){
																//echo " pod_inicial[$k]=$pod_inicial[$k] *** pod_final[$k]=$pod_final[$k] // ";
								   if ( ($pod_inicial[$k] <= $numero_pod[$i]) && ($numero_pod[$i] <= $pod_final[$k]) ){
										$course = $k;
										//echo "course = $course ///";
									}
								}
								
								echo "<td width=\"65%\" bgcolor=\"#fcfcfc\" align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp; $j.- $fecha[$i] a las $hora[$i] en el <i>POD #$numero_pod[$i]</i> para el curso <i>$elenco_cursos[$course]</i> </td></tr>";

								//echo "<td width=\"55%\"> &nbsp; $j.- $fecha[$i] a las $hora[$i] en el POD #$numero_pod[$i] para el usuario $user_id[$i] con el curso $curso_id[$i].</td></tr>";
							}
						}
						else
						{
							echo "<td width=\"55%\" bgcolor=\"#fcfcfc\"> &nbsp; No hay turnos de mant. pendientes en el sistema</td></tr>";
						}				
?>		
					</tr>
				</table>
			</center>
			<br>


<?php
			$offset_pod = $pod_final;
			
			echo "<p>";
		}else{
			//echo "<br>No hay cursos activos en este momento<br><br>";
?>
                <br>
				<input type="text" name="totalCurso" value="<?php echo " No hay cursos activos en este momento"?>" 
				id="totalCurso" style="width: 310px; background: lightblue; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  
			    
			   
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