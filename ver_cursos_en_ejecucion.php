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
	
//si el usuario no es administrador
if ($admin == 0) {
    //Limpiamos y cerramos
	mysql_close($conexion);
	ob_end_flush();		
	session_destroy();
	
    header("Location: index.php?errorusuario=ad");
	
//si es administrador	
}else{

	///datos por cursos de experto
	$sql="SELECT * FROM cursos WHERE inicio_curso <= CURDATE() AND fin_curso >= CURDATE() AND curso_activo = 1 AND num_max_pods > 0 AND nombre_curso LIKE '%Experto%'";
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
		$vector_titulos[$campos-1]="hora_fin_mant_semanal";
		$vector_titulos[$campos]="pods_asignados";
		$vector_titulos[$campos+1]="_pods_ok_"; 
		//echo "campos=$campos";  for ($h=0;$h<count($vector_titulos);$h++) echo "<br>vector_titulos[$h]=$vector_titulos[$h]";
		mysql_data_seek ( $registros, 0);   //devolvemos el puntero a la posicion 0 de la lista
		
		while ($reg = mysql_fetch_array($registros))
		{
		    $array_curso_id[$j] = $reg['curso_id'];
		    $array_num_max_pods[$j] = $reg['num_max_pods'];
		    $array_nombre_curso[$j] = $reg['nombre_curso'];
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
	

	///datos por cursos no experto
	$sql="SELECT * FROM cursos WHERE inicio_curso <= CURDATE() AND fin_curso >= CURDATE() AND curso_activo = 1 AND num_max_pods > 0 AND nombre_curso NOT LIKE '%Experto%'";
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
		$vector_titulos[$campos-1]="hora_fin_mant_semanal";
		$vector_titulos[$campos]="_pods_ok_";
		
		mysql_data_seek ( $registros, 0);   //devolvemos el puntero a la posicion 0 de la lista
		
		while ($reg = mysql_fetch_array($registros))
		{
		    $array_curso_id[$j] = $reg['curso_id'];
		    $array_num_max_pods[$j] = $reg['num_max_pods'];
		    $array_nombre_curso[$j] = $reg['nombre_curso'];
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
	
	
	///datos por cursos de control
	$sql="SELECT * FROM cursos WHERE inicio_curso <= CURDATE() AND fin_curso >= CURDATE() AND curso_activo = 1 AND num_max_pods = 0";
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
		$vector_titulos[$campos-1]="hora_fin_mant_semanal";
		$vector_titulos[$campos]="_pods_ok_";
		
		mysql_data_seek ( $registros, 0);   //devolvemos el puntero a la posicion 0 de la lista
		
		while ($reg = mysql_fetch_array($registros))
		{
		    $array_curso_id[$j] = $reg['curso_id'];
		    $array_num_max_pods[$j] = $reg['num_max_pods'];
		    $array_nombre_curso[$j] = $reg['nombre_curso'];
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

	
	////pods asignados por curso
	$offset_pod = 0;	
	for ($j=0; $j<$num_cursos_en_ejecucion; $j++) {
		$num = $j + 1;
		//echo "<br>CURSO $array_nombre_curso[$j];  PODS ASIGNADOS: ";
		$str='';
		for ($k=0; $k<$array_num_max_pods[$j]; $k++){
			$num = $k + 1 + $offset_pod;
			//echo " $num ";
			$str = $str . "&nbsp; $num &nbsp;";
		}
		if (strcmp($str,''))
			$array_pods_asignados[$j] = $str;
		else
			$array_pods_asignados[$j] = "-";
		//echo "<br>array_pods_ok[$j] = $array_pods_ok[$j]";
		$offset_pod = $num;
	}


	////pods en funcionamiento (ok) por curso
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
	 <title>VER CURSOS EN EJECUCION</title>
 	 <!-- Estas lineas evitan que la pagina deseada no se cargue en la cache y no pueda ser mostrada cuando se regresa atras. -->
	  <meta http-equiv="Expires" CONTENT="0">
	  <meta http-equiv="Cache-Control" CONTENT="no-cache">
	  <meta http-equiv="Pragma" CONTENT="no-cache">
	
	</head>

	
	<body bgcolor="#<?php echo $bgcolor ?>">
	
		
		<center><u><h1>DESCRIPCI&Oacute;N de TODOS los CURSOS en EJECUCI&Oacute;N</h1></u>
				
		<table RULES=NONE FRAME=BOX style="background: #ccffff" border="1" width="60%">
			<font style="width:100; background: #ffffff; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:14pt; font-weight:bold;  border: 1px solid #ccc;">
			
			    <tr height="40px" border="0" cellpadding="0" cellspacing="0">
					<?php   for ($i=0; $i<$campos-2; $i++){ ?> 		
		
							     <th bgcolor="khaki" width="15%" style="border-width: 1px;border: solid; border-color: #000000;"> <?php echo $vector_titulos[$i] ?>  </th>	 
					 
					<?php   }  ?>
		
							     <th bgcolor="khaki" width="15%" style="border-width: 1px;border: solid; border-color: #000000;"> <?php echo $vector_titulos[$campos-1] ?>  </th>	 
							     <th bgcolor="khaki" width="15%" style="border-width: 1px;border: solid; border-color: #000000;"> <?php echo "pods_asignados" ?>  </th>	 
							     <th bgcolor="khaki" width="15%" style="border-width: 1px;border: solid; border-color: #000000;"> <?php echo $vector_titulos[$campos+1] ?>  </th>	 
								 
				</tr>

					<?php 	for ($i=0; $i<$num_cursos_en_ejecucion; $i++){    ?>				
				<tr border="1">
								 <td width="15%" align="center" style="border-width: 1px;border: solid; border-color: #000000;"> <?php echo "$array_curso_id[$i]"; ?>  </td>
								 <td width="15%" align="center" style="border-width: 1px;border: solid; border-color: #000000;"> <?php echo "$array_nombre_curso[$i]"; ?>  </td>
								 <td width="15%" align="center" style="border-width: 1px;border: solid; border-color: #000000;"> <?php echo "$array_num_max_pods[$i]"; ?>  </td>
								 <td width="15%" align="center" style="border-width: 1px;border: solid; border-color: #000000;"> <?php echo "$array_inicio_curso[$i]"; ?>  </td>
								 <td width="15%" align="center" style="border-width: 1px;border: solid; border-color: #000000;"> <?php echo "$array_fin_curso[$i]"; ?>  </td>
								 <td width="15%" align="center" style="border-width: 1px;border: solid; border-color: #000000;"> <?php echo "$array_edicion[$i]"; ?>  </td>
								 <td width="15%" align="center" style="border-width: 1px;border: solid; border-color: #000000;"> <?php if ($array_curso_activo[$i]==1) echo "SI"; else echo "NO"; ?>  </td>
	
								 <td width="15%" align="center" style="border-width: 1px;border: solid; border-color: #000000;"> 
									<?php 
										if($array_dia_mant_semanal[$i]=='1')  echo "lunes"; 
										else if($array_dia_mant_semanal[$i]=='2')  echo "martes"; 
										else if($array_dia_mant_semanal[$i]=='3')  echo "mi&eacute;rcoles"; 
										else if($array_dia_mant_semanal[$i]=='4')  echo "jueves"; 
										else if($array_dia_mant_semanal[$i]=='5')  echo "viernes"; 
										else if($array_dia_mant_semanal[$i]=='6')  echo "s&aacute;bado"; 
										else if($array_dia_mant_semanal[$i]=='7')  echo "domingo"; 
										else echo "-"; 																	
									?>  
								 </td>
								 <td width="15%" align="center" style="border-width: 1px;border: solid; border-color: #000000;"> 
									<?php 
										if ($array_hora_inicio_mant_semanal[$i] >= 0)
											echo "$array_hora_inicio_mant_semanal[$i]:00:00"; 
										else
											echo "-";			
									?>  
								 </td>
								 <td width="15%" align="center" style="border-width: 1px;border: solid; border-color: #000000;"> 
									<?php  
										if ($array_duracion_mant_semanal[$i] > 0){
											//echo "$array_duracion_mant_semanal[$i] horas"; 
											$array_hora_fin_mant_semanal[$i] = ($array_hora_inicio_mant_semanal[$i] + $array_duracion_mant_semanal[$i]) % 24;
											echo "$array_hora_fin_mant_semanal[$i]:00:00";
										}else
											echo "-";									
									?>  
								 </td>	
	
								 <td width="15%" align="center" style="border-width: 1px;border: solid; border-color: #000000;"> <?php echo "$array_pods_asignados[$i]"; ?>  </td>	
								 <td width="15%" align="center" style="border-width: 1px;border: solid; border-color: #000000; background: #ccccff"> <?php echo "$array_pods_ok[$i]"; ?>  </td>
				</tr>
					<?php   }  ?>
					
	
			</font>
		</table>
	 
		</center>
	 
	 
		<br>
		
		
		<br>
		
		</center>
		
		<hr>
		
	
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