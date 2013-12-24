<?php 
 include ("headers/seguridad.php");
 include ("headers/conexion.php");

 function truemod($num, $mod) {
   return ($mod + ($num % $mod)) % $mod;
 }
 
//iniciamos el buffer de salida 
ob_start();

$admin = $_SESSION['admin'];
$idle_timeout = $_SESSION['idle_timeout'];
$hoy = date('Y-m-d');
	
//si el usuario no es administrador
if ($admin == 0) {
    //Limpiamos y cerramos
	mysql_close($conexion);
	ob_end_flush();		
	session_destroy();
	
    header("Location: index.php?errorusuario=ad");
	
//si es administrador	
}else{

	/////datos por cursos activos con pods
	///cursos de experto	 
	$sql="SELECT * FROM cursos WHERE curso_activo = 1 AND inicio_curso <= CURDATE() AND fin_curso >= CURDATE() AND num_max_pods > 0 AND nombre_curso LIKE '%Experto%'";
	$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
	$count = mysql_num_rows($registros);
	$num_cursos_en_ejecucion = $count;
	$j = 0;
	if ($count > 0)
	{	
		$campos=mysql_num_fields($registros);   //numero de columnas de la tabla
		for ($i=0; $i<$campos; $i++)            //Titulos de las columnas
		{
			$vector_titulos[$i] = mysql_field_name($registros, $i);
		}
		$vector_titulos[$campos] = "curso_iniciado";
		
		mysql_data_seek ( $registros, 0);   //devolvemos el puntero a la posicion 0 de la lista
		
		while ($reg = mysql_fetch_array($registros))
		{
		    $array_curso_id[$j] = $reg['curso_id'];
		    $array_nombre_curso[$j] = $reg['nombre_curso'];
		    $array_num_max_pods[$j] = $reg['num_max_pods'];
		    $array_inicio_curso[$j] = $reg['inicio_curso'];
		    $array_fin_curso[$j] = $reg['fin_curso'];
		    $array_edicion[$j] = $reg['edicion'];
		    $array_curso_activo[$j] = $reg['curso_activo'];
			
			$array_dia_mant_semanal[$j] = $reg['dia_mant_semanal'];
			$array_hora_inicio_mant_semanal[$j] = $reg['hora_inicio_mant_semanal'];
			$array_duracion_mant_semanal[$j] = $reg['duracion_mant_semanal'];
			$array_flag_pods_ok[$j] = $reg['flag_pods_ok'];
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
		$campos=mysql_num_fields($registros);   //numero de columnas de la tabla
		for ($i=0; $i<$campos; $i++)            //Titulos de las columnas
		{
			$vector_titulos[$i] = mysql_field_name($registros, $i);
		}
		$vector_titulos[$campos] = "curso_iniciado";
		
		mysql_data_seek ( $registros, 0);   //devolvemos el puntero a la posicion 0 de la lista
		
		while ($reg = mysql_fetch_array($registros))
		{
		    $array_curso_id[$j] = $reg['curso_id'];
		    $array_nombre_curso[$j] = $reg['nombre_curso'];
		    $array_num_max_pods[$j] = $reg['num_max_pods'];
		    $array_inicio_curso[$j] = $reg['inicio_curso'];
		    $array_fin_curso[$j] = $reg['fin_curso'];
		    $array_edicion[$j] = $reg['edicion'];
		    $array_curso_activo[$j] = $reg['curso_activo'];
			
			$array_dia_mant_semanal[$j] = $reg['dia_mant_semanal'];
			$array_hora_inicio_mant_semanal[$j] = $reg['hora_inicio_mant_semanal'];
			$array_duracion_mant_semanal[$j] = $reg['duracion_mant_semanal'];
			$array_flag_pods_ok[$j] = $reg['flag_pods_ok'];
		    $j++;
		}		  
	}
	mysql_free_result($registros);
	
	
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

	/////////////
	///datos por cursos activos sin pods
	$sql="SELECT * FROM cursos WHERE fin_curso >= CURDATE() AND num_max_pods = 0";
	$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
	$count = mysql_num_rows($registros);
	$num_cursos_en_ejecucion_sin_pods = $count;  
	
	if ($count > 0)
	{	
		$campos=mysql_num_fields($registros);   //numero de columnas de la tabla
		for ($i=0; $i<$campos; $i++)            //Titulos de las columnas
		{
			$vector_titulos[$i] = mysql_field_name($registros, $i);
		}
		$vector_titulos[$campos] = "curso_iniciado";
		
		mysql_data_seek ( $registros, 0);   //devolvemos el puntero a la posicion 0 de la lista
		
		while ($reg = mysql_fetch_array($registros))
		{
		    $array_curso_id[$j] = $reg['curso_id'];
		    $array_nombre_curso[$j] = $reg['nombre_curso'];
		    $array_num_max_pods[$j] = $reg['num_max_pods'];
		    $array_inicio_curso[$j] = $reg['inicio_curso'];
		    $array_fin_curso[$j] = $reg['fin_curso'];
		    $array_edicion[$j] = $reg['edicion'];
		    $array_curso_activo[$j] = $reg['curso_activo'];
			
			$array_dia_mant_semanal[$j] = $reg['dia_mant_semanal'];
			$array_hora_inicio_mant_semanal[$j] = $reg['hora_inicio_mant_semanal'];
			$array_duracion_mant_semanal[$j] = $reg['duracion_mant_semanal'];
			$array_flag_pods_ok[$j] = $reg['flag_pods_ok'];
			
			$array_num_pods[$j] = "-";
		    $j++;
		}		  
	}
	mysql_free_result($registros);
	

	////buscamos los numeros de pod en funcionamiento (ok)
	if ($j>0){
		$count_array=count($array_flag_pods_ok);  
		$offset_pod = 0;
		//echo "count_array=$count_array";
		for ($k=0; $k<$count_array; $k++){
			if ($array_flag_pods_ok[$k] > 0){
				$tmp = $array_flag_pods_ok[$k];
				//echo "<br>tmp = $tmp";
				$num_max_pods = $array_num_max_pods[$k];
				//$logaritmo = log($tmp+1,2);
				//echo "<br>logaritmo = $logaritmo";
				$str="";
				//echo "<br>";
				for ($m=0; $m<$num_max_pods; $m++){
					$cociente = floor(($tmp/2));
					$resto = truemod(($tmp),2);
					$pods_temp[$m] = $resto;
					$tmp = $cociente;
					//echo "cociente = $cociente; resto = $resto ** ";
					if ($resto==1){
						$num = ($m + 1) + $offset_pod;
						$str = $str . "&nbsp; $num &nbsp;";
					}
				}
				$offset_pod += $num_max_pods;
				$array_pods_ok[$k] = $str;
				//echo "<br>array_pods_ok[$k] = $array_pods_ok[$k]";
			}else{
				$array_pods_ok[$k]='-';
				//echo "<br>Curso sin pods activos";
				$num_max_pods = $array_num_max_pods[$k];
				$offset_pod += $num_max_pods;
			}
		}
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
	 <title>PODS_ASIGNADOS POR CURSO</title>
 	 <!-- Estas lineas evitan que la pagina deseada no se cargue en la cache y no pueda ser mostrada cuando se regresa atras. -->
	  <meta http-equiv="Expires" CONTENT="0">
	  <meta http-equiv="Cache-Control" CONTENT="no-cache">
	  <meta http-equiv="Pragma" CONTENT="no-cache">
	
	</head>

	
	<body bgcolor="#<?php echo $bgcolor ?>"> 
	
		<center><u><h1>PODS ASIGNADOS por CURSO</h1></u>

		<table border="1" cellpadding="1" cellspacing="0" width="75%" style="background: #ccffff">
			<tr>
					<th bgcolor="ffe1ae" width="20%" style="border-width: 1px;border: solid; border-color: #000000;"> Nombre del Curso </th>
					<th bgcolor="ffe1ae" width="10%" style="border-width: 1px;border: solid; border-color: #000000;"> Edici&oacute;n </th>					
					<th bgcolor="ffe1ae" width="10%" style="border-width: 1px;border: solid; border-color: #000000;"> N&uacute;m.m&aacute;ximo de PODs </th>
					<th bgcolor="ffe1ae" width="15%" style="border-width: 1px;border: solid; border-color: #000000;"> PODS asignados </th>
					<th bgcolor="ffe1ae" width="15%" style="border-width: 1px;border: solid; border-color: #000000;"> PODS OK </th>
			</tr>

					<?php 	for ($i=0; $i<($num_cursos_en_ejecucion+$num_cursos_en_ejecucion_sin_pods); $i++){    ?>			
			<tr>
					<td width="15%" align="center" style="border-width: 1px; border: solid; border-color: #000000;"><I> <?php echo "$array_nombre_curso[$i]"; ?>  </I></td>
			        <td width="15%" align="center" style="border-width: 1px; border: solid; border-color: #000000;"> <?php echo "$array_edicion[$i]"; ?>  </td>
					<td width="15%" align="center" style="border-width: 1px; border: solid; border-color: #000000;"> <?php echo "$array_num_max_pods[$i]"; ?>  </td>
					<td width="15%" align="center" style="border-width: 1px; border: solid; border-color: #000000;"> <?php echo "$array_num_pods[$i]"; ?>  </td>
					<td width="15%" align="center" style="border-width: 1px; border: solid; border-color: #000000; background: #ccccff"> <?php echo "$array_pods_ok[$i]"; ?>  </td>
			</tr>
					<?php   }  ?>
				
		</table>	
		
		<br><br><br>
		
		<hr>
		
		<br>
		
		<center><u><h1>DESCRIPCI&Oacute;N de los CURSOS PROGRAMADOS</h1></u>
				
		<table RULES=NONE FRAME=BOX style="background: #ccffff" border="1" width="80%">
			<font style="width:100; background: #ffffff; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:14pt; font-weight:bold;  border: 1px solid #ccc;">
			
			    <tr height="40px" border="0" cellpadding="0" cellspacing="0">
					<?php   for ($i=0; $i<$campos-5; $i++){ ?> 		
		
							     <th bgcolor="khaki" width="15%" style="border-width: 1px;border: solid; border-color: #000000;"> <?php echo $vector_titulos[$i] ?>  </th>	 
					 
					<?php   }  ?>
								
								<th bgcolor="khaki" width="15%" style="border-width: 1px;border: solid; border-color: #000000;"> <?php echo $vector_titulos[$campos-1] ?>  </th>
								<th bgcolor="khaki" width="15%" style="border-width: 1px;border: solid; border-color: #000000;"> <?php echo "Pods_Asignados";?>  </th>
								<th bgcolor="khaki" width="15%" style="border-width: 1px;border: solid; border-color: #000000;"> <?php echo "_PODS_OK_";?>  </th>							
								<th bgcolor="khaki" width="15%" style="border-width: 1px;border: solid; border-color: #000000;"> <?php echo $vector_titulos[$campos-5] ?>  </th>								
								<th bgcolor="khaki" width="15%" style="border-width: 1px;border: solid; border-color: #000000;"> <?php echo $vector_titulos[$campos] ?>  </th>
								

				</tr>

					<?php 	for ($i=0; $i<($num_cursos_en_ejecucion+$num_cursos_en_ejecucion_sin_pods); $i++){    ?>				
				<tr border="1">
								 <td width="15%" align="center" style="border-width: 1px;border: solid; border-color: #000000;"> <?php echo "$array_curso_id[$i]"; ?>  </td>
								 <td width="15%" align="center" style="border-width: 1px;border: solid; border-color: #000000;"> <?php echo "$array_nombre_curso[$i]"; ?>  </td>
								 <td width="15%" align="center" style="border-width: 1px;border: solid; border-color: #000000;"> <?php echo "$array_num_max_pods[$i]"; ?>  </td>
								 <td width="15%" align="center" style="border-width: 1px;border: solid; border-color: #000000;"> <?php echo "$array_inicio_curso[$i]"; ?>  </td>
								 <td width="15%" align="center" style="border-width: 1px;border: solid; border-color: #000000;"> <?php echo "$array_fin_curso[$i]"; ?>  </td>
								 <td width="15%" align="center" style="border-width: 1px;border: solid; border-color: #000000;"> <?php echo "$array_edicion[$i]"; ?>  </td>
								 
								 <td width="15%" align="center" style="border-width: 1px;border: solid; border-color: #000000;"> <?php echo "$array_flag_pods_ok[$i]"; ?>  </td>
								 <td width="15%" align="center" style="border-width: 1px;border: solid; border-color: #000000;"> <?php echo "$array_num_pods[$i]"; ?>  </td>									 
								 <td width="15%" align="center" style="border-width: 1px;border: solid; border-color: #000000;"> <?php echo "$array_pods_ok[$i]"; ?>  </td>								 
								 <td width="15%" align="center" style="border-width: 1px;border: solid; border-color: #000000;"> <?php if ($array_curso_activo[$i]==1) echo "SI"; else echo "NO"; ?>  </td>  
								 <td width="15%" align="center" style="border-width: 1px;border: solid; border-color: #000000;"> <?php if (($array_inicio_curso[$i]<=$hoy) && ($array_fin_curso[$i]>=$hoy)) echo "SI"; else echo "NO"; ?>  </td> 
				</tr>
					<?php   }  ?>
					
	
			</font></center>
		</table>
	 
		<br>
		
		
		<br>
		
		</center>
		
	
	<form name="form_gestionar" action="gestionar_cursos.php" method="POST">
      <div align="left">
	    <INPUT type="submit" name="gestionar" value="GESTIONAR" face="algerian" size="5" style="background-color : #555555; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:120px;" />
	  </div>	
	</form>

    <form name="form_volver" action="admin.php" method="POST">	  
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
	mysql_close($conexion);
	ob_end_flush();		
}
?>