<?php 
 include ("headers/seguridad.php");
 include ("headers/conexion.php");

//iniciamos el buffer de salida 
ob_start();

$admin = $_SESSION['admin'];
$idle_timeout = $_SESSION['idle_timeout'];

//Se comprueba si el usuario tiene privilegios de administrador
if ($_SESSION['admin'] == 0) { 
     session_destroy();
    //si no tiene privilegios de administrador, se devuelve a la pagina inicial 
    header("Location: index.php?errorusuario=ad"); 
} else { 

	/****
	 * zerofill()
	 *
	 * Devuelve el número ingresado con ceros a la izquierda dependiendo del
	 * largo deseado de la cadena de salida.
	 *
	 * @param   int $entero
	 * @param   int $largo
	 * @return  string numero_formateado_ceros_izquierda
	 */
	 
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
	 <title>MOSTRAR TABLAS</title>
 	 <!-- Estas lineas evitan que la pagina deseada no se cargue en la cache y no pueda ser mostrada cuando se regresa atras. -->
	  <meta http-equiv="Expires" CONTENT="0">
	  <meta http-equiv="Cache-Control" CONTENT="no-cache">
	  <meta http-equiv="Pragma" CONTENT="no-cache">
	  
      <script src="src/dragtable.js"></script>
      <script src="src/sorttable.js"></script>

	</head>


	<body bgcolor="#<?php echo $bgcolor ?>">
	
 <?php
    if (isset($_POST['tablas'])) {     

			$tablas_str = implode(" ", $_POST['tablas']);// converts $_POST tablas into a string
			$tablas_array = explode(" ", $tablas_str);// converts the string to an array which you can easily manipulate

		for ($i = 0; $i < count($tablas_array); $i++) {
			//echo "<br>$tablas_array[$i]";// display the result as a string
		}

		//separamos las distintas opciones seleccionadas
		$num_tablas = count($tablas_array);	
		//echo "<br>num_tablas = $num_tablas";
		
		for ($i=0; $i<$num_tablas; $i++){
		   $j = $i + 1;
		   //echo "<br> $j &#170; Tabla --> $tablas_array[$i]";
		}  
		
	}else{	
		//si no hay ninguna opcion seleccionada, limpiamos y cerramos
		mysql_close($conexion);
		ob_end_flush();
		
		echo "<br>&iexcl;No has seleccionado ninguna tabla!";
		header("Location: ver_tablas.php");
	}
	
	if ($tablas_array[0] == 0){
	    for ($i=0; $i<13; $i++){
		    $tablas_array[$i] = $i+1;
		}
		$num_tablas = count($tablas_array);
	}	
		
		
	for ($i=0; $i<$num_tablas; $i++)
	{
	    switch ($tablas_array[$i])
		{
		    case 1:
					//tabla ALUMNOS_EN_CURSOS
					//consulta para mostrar Todos los campos de esta tabla
					$sql = "SELECT * FROM alumnos_en_cursos ORDER BY curso_id, user_id";
					$result=mysql_query($sql,$conexion) or die ("Problemas con el Select (sql) ".mysql_error());
					
					$count=mysql_num_rows($result);       //filas de la tabla 
					$fields=mysql_num_fields($result);    //columnas de la tabla
					
					//mostramos los campos por pantalla
					if(!$result)
					{
					    echo "<br>La tabla ALUMNOS_EN_CURSOS esta vac&iacute;a.<br>";
						$message = 'ERROR:' . mysql_error();
						return $message;
					}
					else
					{
                        echo "<center>";
						echo "  <H3>Tabla ALUMNOS_EN_CURSOS</H3>";
						echo "    <table border=1 class=\"thin sortable draggable\">";
						echo "	    <tr>";
						
						for ($j=0; $j<$fields; $j++)  //Table Header
						{
							$titulo = mysql_field_name($result, $j);
							echo "<th bgcolor=\"khaki\">  $titulo  </th>";
						}
						echo "     </tr>";
						
						while ($row = mysql_fetch_row($result))   //Table body
						{
							echo "<tr>";
							for ($j=0; $j<$fields; $j++)
							{
							    $campo = $row[$j];
								echo "<td>  $campo  </td>"; 
							}
							echo "</tr>\n";
						}
						echo "</table><p>";
						
						echo "</table></center>";
						mysql_free_result($result);
						echo "<p>";
					}			
					break;
					
		    case 2:
					//tabla CURSOS
					//consulta para mostrar Todos los campos de esta tabla
					$sql = "SELECT * FROM cursos ORDER BY curso_id";
					$result=mysql_query($sql,$conexion) or die ("Problemas con el Select (sql) ".mysql_error());
					
					$count=mysql_num_rows($result);       //filas de la tabla 
					$fields=mysql_num_fields($result);    //columnas de la tabla
					
					//mostramos los campos por pantalla
					if(!$result)
					{
					    echo "<br>La tabla CURSOS esta vac&iacute;a.<br>";
						$message = 'ERROR:' . mysql_error();
						return $message;
					}
					else
					{
                        echo "<center>";
						echo "  <H3>Tabla CURSOS</H3>";
						echo "    <table border=1 class=\"thin sortable draggable\">";
						echo "	    <tr>";
						
						for ($j=0; $j<$fields; $j++)  //Table Header
						{
							$titulo = mysql_field_name($result, $j);
							echo "<th bgcolor=\"khaki\">  $titulo  </th>";
						}
						echo "     </tr>";
						
						while ($row = mysql_fetch_row($result))   //Table body
						{
							echo "<tr>";
							for ($j=0; $j<$fields; $j++)
							{
							    $campo = $row[$j];
								echo "<td>  $campo  </td>"; 
							}
							echo "</tr>\n";
						}
						echo "</table><p>";
						
						echo "</table></center>";
						mysql_free_result($result);
						echo "<p>";
					}	
					break;
					
		    case 3:
					//tabla DATOS_PERS
					//consulta para mostrar Todos los campos de esta tabla
					$sql = "SELECT * FROM datos_pers ORDER BY datos_pers_id";
					$result=mysql_query($sql,$conexion) or die ("Problemas con el Select (sql) ".mysql_error());
					
					$count=mysql_num_rows($result);       //filas de la tabla 
					$fields=mysql_num_fields($result);    //columnas de la tabla
					
					//mostramos los campos por pantalla
					if(!$result)
					{
					    echo "<br>La tabla DATOS_PERS esta vac&iacute;a.<br>";
						$message = 'ERROR:' . mysql_error();
						return $message;
					}
					else
					{
                        echo "<center>";
						echo "  <H3>Tabla DATOS_PERS</H3>";
						echo "    <table border=1 class=\"thin sortable draggable\">";
						echo "	    <tr>";
						
						for ($j=0; $j<$fields; $j++)  //Table Header
						{
							$titulo = mysql_field_name($result, $j);
							echo "<th bgcolor=\"khaki\">  $titulo  </th>";
						}
						echo "     </tr>";
						
						while ($row = mysql_fetch_row($result))   //Table body
						{
							echo "<tr>";
							for ($j=0; $j<$fields; $j++)
							{
							    $campo = $row[$j];
								echo "<td>  $campo  </td>"; 
							}
							echo "</tr>\n";
						}
						echo "</table><p>";
						
						echo "</table></center>";
						mysql_free_result($result);
						echo "<p>";
					}		
					break;
					
		    case 4:
					//tabla LOGS
					//consulta para mostrar Todos los campos de esta tabla
					$sql = "SELECT * FROM logs ORDER BY log_id";
					$result=mysql_query($sql,$conexion) or die ("Problemas con el Select (sql) ".mysql_error());
					
					$count=mysql_num_rows($result);       //filas de la tabla 
					$fields=mysql_num_fields($result);    //columnas de la tabla
					
					//mostramos los campos por pantalla
					if(!$result)
					{
					    echo "<br>La tabla LOGS esta vac&iacute;a.<br>";
						$message = 'ERROR:' . mysql_error();
						return $message;
					}
					else
					{
                        echo "<center>";
						echo "  <H3>Tabla LOGS</H3>";
						echo "    <table border=1 class=\"thin sortable draggable\">";
						echo "	    <tr>";
						
						for ($j=0; $j<$fields; $j++)  //Table Header
						{
							$titulo = mysql_field_name($result, $j);
							echo "<th bgcolor=\"khaki\">  $titulo  </th>";
						}
						echo "     </tr>";
						
						while ($row = mysql_fetch_row($result))   //Table body
						{
							echo "<tr>";
							for ($j=0; $j<$fields; $j++)
							{
							    $campo = $row[$j];
								echo "<td>  $campo  </td>"; 
							}
							echo "</tr>\n";
						}
						echo "</table><p>";
						
						echo "</table></center>";
						mysql_free_result($result);
						echo "<p>";
					}			
					break;
					
		    case 5:
					//tabla LOG_DETALLES
					//consulta para mostrar Todos los campos de esta tabla
					$sql = "SELECT * FROM log_detalles ORDER BY log_det_id";
					$result=mysql_query($sql,$conexion) or die ("Problemas con el Select (sql) ".mysql_error());
					
					$count=mysql_num_rows($result);       //filas de la tabla 
					$fields=mysql_num_fields($result);    //columnas de la tabla
					
					//mostramos los campos por pantalla
					if(!$result)
					{
					    echo "<br>La tabla LOG_DETALLES esta vac&iacute;a.<br>";
						$message = 'ERROR:' . mysql_error();
						return $message;
					}
					else
					{
                        echo "<center>";
						echo "  <H3>Tabla LOG_DETALLES</H3>";
						echo "    <table border=1 class=\"thin sortable draggable\">";
						echo "	    <tr>";
						
						for ($j=0; $j<$fields; $j++)  //Table Header
						{
							$titulo = mysql_field_name($result, $j);
							echo "<th bgcolor=\"khaki\">  $titulo  </th>";
						}
						echo "     </tr>";
						
						while ($row = mysql_fetch_row($result))   //Table body
						{
							echo "<tr>";
							for ($j=0; $j<$fields; $j++)
							{
							    $campo = $row[$j];
								echo "<td>  $campo  </td>"; 
							}
							echo "</tr>\n";
						}
						echo "</table><p>";
						
						echo "</table></center>";
						mysql_free_result($result);
						echo "<p>";
					}			
					break;	
					
		    case 6:
					//tabla MANTENIMIENTO
					//consulta para mostrar Todos los campos de esta tabla
					$sql = "SELECT * FROM mantenimiento ORDER BY fecha_outage, horario_outage, num_POD_outage";
					$result=mysql_query($sql,$conexion) or die ("Problemas con el Select (sql) ".mysql_error());
					
					$count=mysql_num_rows($result);       //filas de la tabla 
					$fields=mysql_num_fields($result);    //columnas de la tabla
					
					//mostramos los campos por pantalla
					if(!$result)
					{
					    echo "<br>La tabla MANTENIMIENTO esta vac&iacute;a.<br>";
						$message = 'ERROR:' . mysql_error();
						return $message;
					}
					else
					{
                        echo "<center>";
						echo "  <H3>Tabla MANTENIMIENTO</H3>";
						echo "    <table border=1 class=\"thin sortable draggable\">";
						echo "	    <tr>";
						
						for ($j=0; $j<$fields; $j++)  //Table Header
						{
							$titulo = mysql_field_name($result, $j);
							echo "<th bgcolor=\"khaki\">  $titulo  </th>";
						}
						echo "     </tr>";
						
						while ($row = mysql_fetch_row($result))   //Table body
						{
							echo "<tr>";
							for ($j=0; $j<$fields; $j++)
							{
							    $campo = $row[$j];
								echo "<td>  $campo  </td>"; 
							}
							echo "</tr>\n";
						}
						echo "</table><p>";
						
						echo "</table></center>";
						mysql_free_result($result);
						echo "<p>";
					}			
					break;
					
		    case 7:
					//tabla MENSAJES_TEMP
					//consulta para mostrar Todos los campos de esta tabla
					$sql = "SELECT * FROM mensajes_temp ORDER BY mensaje_id";
					$result=mysql_query($sql,$conexion) or die ("Problemas con el Select (sql) ".mysql_error());
					
					$count=mysql_num_rows($result);       //filas de la tabla 
					$fields=mysql_num_fields($result);    //columnas de la tabla
					
					//mostramos los campos por pantalla
					if(!$result)
					{
					    echo "<br>La tabla MENSAJES_TEMP esta vac&iacute;a.<br>";
						$message = 'ERROR:' . mysql_error();
						return $message;
					}
					else
					{
                        echo "<center>";
						echo "  <H3>Tabla MENSAJES_TEMP</H3>";
						echo "    <table border=1 class=\"thin sortable draggable\">";
						echo "	    <tr>";
						
						for ($j=0; $j<$fields; $j++)  //Table Header
						{
							$titulo = mysql_field_name($result, $j);
							echo "<th bgcolor=\"khaki\">  $titulo  </th>";
						}
						echo "     </tr>";
						
						while ($row = mysql_fetch_row($result))   //Table body
						{
							echo "<tr>";
							for ($j=0; $j<$fields; $j++)
							{
							    $campo = $row[$j];
								echo "<td>  $campo  </td>"; 
							}
							echo "</tr>\n";
						}
						echo "</table><p>";
						
						echo "</table></center>";
						mysql_free_result($result);
						echo "<p>";
					}
					break;
					
		    case 8:
					//tabla PARAMETROS_BASICOS
					//consulta para mostrar Todos los campos de esta tabla
					$sql = "SELECT * FROM parametros_basicos ORDER BY param_id";
					$result=mysql_query($sql,$conexion) or die ("Problemas con el Select (sql) ".mysql_error());
					
					$count=mysql_num_rows($result);       //filas de la tabla 
					$fields=mysql_num_fields($result);    //columnas de la tabla
					
					//mostramos los campos por pantalla
					if(!$result)
					{
					    echo "<br>La tabla PARAMETROS_BASICOS esta vac&iacute;a.<br>";
						$message = 'ERROR:' . mysql_error();
						return $message;
					}
					else
					{
                        echo "<center>";
						echo "  <H3>Tabla PARAMETROS_BASICOS</H3>";
						echo "    <table border=1 class=\"thin sortable draggable\">";
						echo "	    <tr>";
						
						for ($j=0; $j<$fields; $j++)  //Table Header
						{
							$titulo = mysql_field_name($result, $j);
							echo "<th bgcolor=\"khaki\">  $titulo  </th>";
						}
						echo "     </tr>";
						
						while ($row = mysql_fetch_row($result))   //Table body
						{
							echo "<tr>";
							for ($j=0; $j<$fields; $j++)
							{
							    $campo = $row[$j];
								echo "<td>  $campo  </td>"; 
							}
							echo "</tr>\n";
						}
						echo "</table><p>";
						
						echo "</table></center>";
						mysql_free_result($result);
						echo "<p>";
					}
					break;
					
		    case 9:
					//tabla RESERVAS
					
					//primero actualizamos las reservas que ya han pasado sin haber sido usadas  --> RESERVAS CADUCADAS (estado final -1)
					$duracionTurno = $_SESSION['duracionTurno'];
					if (!isset($duracionTurno))
						$duracionTurno = 2;
					$HorasTurno = floor($duracionTurno);
					$MinutosTurno = ($duracionTurno - $HorasTurno)*60;
					$numTurnosDia = $_SESSION['numTurnosDia'];
					$f_ahora = date('Y-m-d H:i:s');
					 
					$consulta1 = "SELECT * FROM reservas WHERE estado_reserva = 0";
					$registros1=mysql_query($consulta1,$conexion) or die ("Problemas con el Select (consulta1) ".mysql_error());
					$cuenta1 = mysql_num_rows($registros1);

					$consulta2 = "SELECT * FROM reservas WHERE estado_reserva = 2";
					$registros2=mysql_query($consulta2,$conexion) or die ("Problemas con el Select (consulta2) ".mysql_error());
					$cuenta2 = mysql_num_rows($registros2);
					
					if ($cuenta1 != 0){
					while ($reg1 = mysql_fetch_array($registros1)){
					
						  $status_reserva =  $reg1['estado_reserva'];					
						  $id_reserva = $reg1['reserva_id'];
						  $id_user = $reg1['user_id'];
						  
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
						  
						  if ($f_ahora > $f_fin_reserva){
							$status_reserva = -1;							
						    //echo "<br>status_reserva = $status_reserva<hr>";

							$actualizacion1="UPDATE reservas SET estado_reserva = $status_reserva WHERE reserva_id = $id_reserva"; 
						    $resultado1=mysql_query($actualizacion1,$conexion) or die ("Problemas con el Update (actualizacion1) ".mysql_error());

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
									<i>Esperamos que puedas utilizar tus pr&oacute;ximas reservas.</i>
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
						  }

					  }
					}else{
					  //echo "<br>En este momento no hay ninguna reserva anterior no usada en el sistema.<br>";
					}
					//liberamos recursos
					mysql_free_result($registros1);
					

					if ($cuenta2 != 0){
					while ($reg2 = mysql_fetch_array($registros2)){
					
						  $status_reserva =  $reg2['estado_reserva'];					
						  $id_reserva = $reg2['reserva_id'];
						  
						  $dia_almacenado = $reg2['fecha_reserva'];
						  $hora_almacenada = $reg2['horario_reserva'];
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
					mysql_free_result($registros2);

					
					//*******************//
					////ahora realizamos la consulta para mostrar Todoos los campos de la tabla RESERVA con los tiempos ACTUALIZADOS
					//$sql = "SELECT * FROM reservas ORDER BY fecha_reserva, horario_reserva, num_POD";
					
					////sustituimos la tabla reservas por una vista, que nos permitirá ver toda la info del usuario de cada reserva
					$sql= "SELECT * FROM reservas";
					$result=mysql_query($sql,$conexion) or die ("Problemas con el Select (sql) ".mysql_error());
					
					$count=mysql_num_rows($result);       //filas de la tabla 
					$fields=mysql_num_fields($result);    //columnas de la tabla
					
					//mostramos los campos por pantalla
					if(!$result)
					{
					    echo "<br>La tabla RESERVAS esta vac&iacute;a.<br>";
						$message = 'ERROR:' . mysql_error();
						return $message;
					}
					else
					{
                        echo "<center>";
						echo "  <H3>Tabla RESERVAS</H3>";
						echo "    <table border=1 class=\"thin sortable draggable\">";
						echo "	    <tr>";
						                                                                     
						for ($j=0; $j<$fields; $j++)  //Table Header
						{
							$titulo = mysql_field_name($result, $j);
							echo "<th bgcolor=\"khaki\">  $titulo  </th>";
						}
						echo "     </tr>";
						
						while ($row = mysql_fetch_row($result))   //Table body
						{
							echo "<tr>";
							for ($j=0; $j<$fields; $j++)
							{
							    $campo = $row[$j];
								echo "<td>  $campo  </td>"; 
							}
							echo "</tr>\n";
						}
						echo "</table><p>";
						
						echo "</table></center>";
						mysql_free_result($result);
						echo "<p>";
					}			
					break;
					
		    case 10:
					//tabla USUARIOS
					//consulta para mostrar Todos los campos de esta tabla
					$sql = "SELECT * FROM usuarios ORDER BY user_id";
					$result=mysql_query($sql,$conexion) or die ("Problemas con el Select (sql) ".mysql_error());
					
					$count=mysql_num_rows($result);       //filas de la tabla 
					$fields=mysql_num_fields($result);    //columnas de la tabla
					
					//mostramos los campos por pantalla
					if(!$result)
					{
					    echo "<br>La tabla USUARIOS esta vac&iacute;a.<br>";
						$message = 'ERROR:' . mysql_error();
						return $message;
					}
					else
					{
                        echo "<center>";
						echo "  <H3>Tabla USUARIOS</H3>";
						echo "    <table border=1 class=\"thin sortable draggable\">";
						echo "	    <tr>";
						
						for ($j=0; $j<$fields; $j++)  //Table Header
						{
							$titulo = mysql_field_name($result, $j);
							echo "<th bgcolor=\"khaki\">  $titulo  </th>";
						}
						echo "     </tr>";
						
						while ($row = mysql_fetch_row($result))   //Table body
						{
							echo "<tr>";
							for ($j=0; $j<$fields; $j++)
							{
							    $campo = $row[$j];
								echo "<td>  $campo  </td>"; 
							}
							echo "</tr>\n";
						}
						
						echo "</table></center>";
						mysql_free_result($result);
						echo "<p>";
					}		 
					break;		

			///////////////////////// TABLAS RADIUS /////////////
							
		    case 11:
					//tabla RADCHECK
					
					//primero actualizamos las tablas del radius al turno actual
					exec("/var/www/sincroniza.sh");
					
					//consulta para mostrar Todos los campos de esta tabla
					$sql = "SELECT * FROM radcheck ORDER BY id";
					$result=mysql_query($sql,$conexion) or die ("Problemas con el Select (sql) ".mysql_error());
					
					$count=mysql_num_rows($result);       //filas de la tabla 
					$fields=mysql_num_fields($result);    //columnas de la tabla
					
					//mostramos los campos por pantalla
					if(!$result)
					{
					    echo "<br>La tabla RADCHECK esta vac&iacute;a.<br>";
						$message = 'ERROR:' . mysql_error();
						return $message;
					}
					else
					{
                        echo "<center>";
						echo "  <H3>Tabla RADCHECK</H3>";
						echo "    <table border=1 class=\"thin sortable draggable\">";
						echo "	    <tr>";

						$hora = date('H');
						$turno_inicio = floor($hora/2)*2;
						$turno_fin = $turno_inicio+2;
						$hora_start = zerofill($turno_inicio,2);
						$hora_end = zerofill($turno_fin,2);
						echo "-------------------------------------";
						echo "<br>TURNO: $hora_start:00 - $hora_end:00";
						echo "<br>-------------------------------------";
						
						for ($j=0; $j<$fields; $j++)  //Table Header
						{
							$titulo = mysql_field_name($result, $j);
							echo "<th bgcolor=\"khaki\">  $titulo  </th>";
						}
						echo "     </tr>";
						
						while ($row = mysql_fetch_row($result))   //Table body
						{
							echo "<tr>";
							for ($j=0; $j<$fields; $j++)
							{
							    $campo = $row[$j];
								echo "<td>  $campo  </td>"; 
							}
							echo "</tr>\n";
						}
						
						echo "</table></center>";
						mysql_free_result($result);
						echo "<p>";
					}		 
					break;	

		    case 12:				
					//tabla RADREPLY

					//primero actualizamos las tablas del radius al turno actual
					exec("/var/www/sincroniza.sh");
					
					//consulta para mostrar Todos los campos de esta tabla
					$sql = "SELECT * FROM radreply ORDER BY id";
					$result=mysql_query($sql,$conexion) or die ("Problemas con el Select (sql) ".mysql_error());
					
					$count=mysql_num_rows($result);       //filas de la tabla 
					$fields=mysql_num_fields($result);    //columnas de la tabla
					
					//mostramos los campos por pantalla
					if(!$result)
					{
					    echo "<br>La tabla RADREPLY esta vac&iacute;a.<br>";
						$message = 'ERROR:' . mysql_error();
						return $message;
					}
					else
					{
                        echo "<center>";
						echo "  <H3>Tabla RADREPLY</H3>";
						echo "    <table border=1 class=\"thin sortable draggable\">";
						echo "	    <tr>";

						$hora = date('H');
						$turno_inicio = floor($hora/2)*2;
						$turno_fin = $turno_inicio+2;
						$hora_start = zerofill($turno_inicio,2);
						$hora_end = zerofill($turno_fin,2);
						echo "-------------------------------------";
						echo "<br>TURNO: $hora_start:00 - $hora_end:00";
						echo "<br>-------------------------------------";
						
						for ($j=0; $j<$fields; $j++)  //Table Header
						{
							$titulo = mysql_field_name($result, $j);
							echo "<th bgcolor=\"khaki\">  $titulo  </th>";
						}
						echo "     </tr>";
						
						while ($row = mysql_fetch_row($result))   //Table body
						{
							echo "<tr>";
							for ($j=0; $j<$fields; $j++)
							{
							    $campo = $row[$j];
								echo "<td>  $campo  </td>"; 
							}
							echo "</tr>\n";
						}
						
						echo "</table></center>";
						mysql_free_result($result);
						echo "<p>";
					}		 
					break;

		    case 13:
					//tabla NAS
					
					//primero actualizamos las tablas del radius al turno actual
					exec("/var/www/sincroniza.sh");
					
					//consulta para mostrar Todos los campos de esta tabla
					$sql = "SELECT * FROM nas ORDER BY id";
					$result=mysql_query($sql,$conexion) or die ("Problemas con el Select (sql) ".mysql_error());
					
					$count=mysql_num_rows($result);       //filas de la tabla 
					$fields=mysql_num_fields($result);    //columnas de la tabla
					
					//mostramos los campos por pantalla
					if(!$result)
					{
					    echo "<br>La tabla NAS esta vac&iacute;a.<br>";
						$message = 'ERROR:' . mysql_error();
						return $message;
					}
					else
					{
                        echo "<center>";
						echo "  <H3>Tabla NAS</H3>";
						echo "    <table border=1 class=\"thin sortable draggable\">";
						echo "	    <tr>";
						
						for ($j=0; $j<$fields; $j++)  //Table Header
						{
							$titulo = mysql_field_name($result, $j);
							echo "<th bgcolor=\"khaki\">  $titulo  </th>";
						}
						echo "     </tr>";
						
						while ($row = mysql_fetch_row($result))   //Table body
						{
							echo "<tr>";
							for ($j=0; $j<$fields; $j++)
							{
							    $campo = $row[$j];
								echo "<td>  $campo  </td>"; 
							}
							echo "</tr>\n";
						}
						
						echo "</table></center>";
						mysql_free_result($result);
						echo "<p>";
					}		 
					break;

		    case 14:
					//VISTA de las ultimas RESERVAS (ultimo mes)   --> vista v
					
					//parametros previos
					$duracionTurno = $_SESSION['duracionTurno'];
					if (!isset($duracionTurno))
						$duracionTurno = 2;
					$HorasTurno = floor($duracionTurno);
					$MinutosTurno = ($duracionTurno - $HorasTurno)*60;
					$numTurnosDia = $_SESSION['numTurnosDia'];
					$f_ahora = date('Y-m-d H:i:s');

					//primero actualizamos las reservas que ya han pasado sin haber sido usadas  --> RESERVAS CADUCADAS (estado final -1)					
					$consulta1 = "SELECT * FROM reservas WHERE estado_reserva = 0";
					$registros1=mysql_query($consulta1,$conexion) or die ("Problemas con el Select (consulta1) ".mysql_error());
					$cuenta1 = mysql_num_rows($registros1);
					
					if ($cuenta1 != 0){
						while ($reg1 = mysql_fetch_array($registros1)){
					
							$status_reserva =  $reg1['estado_reserva'];					
							$id_reserva = $reg1['reserva_id'];
							$id_user = $reg1['user_id'];
						  
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
					mysql_free_result($registros1);
					
					
					//despues actualizamos las reservas que ya han pasado habiendo sido usadas  --> RESERVAS APROVECHADAS (estado final 1)
					$consulta2 = "SELECT * FROM reservas WHERE estado_reserva = 2";
					$registros2=mysql_query($consulta2,$conexion) or die ("Problemas con el Select (consulta2) ".mysql_error());
					$cuenta2 = mysql_num_rows($registros2);
					
					if ($cuenta2 != 0){
						while ($reg2 = mysql_fetch_array($registros2)){
					
							$status_reserva =  $reg2['estado_reserva'];					
							$id_reserva = $reg2['reserva_id'];
						  
							$dia_almacenado = $reg2['fecha_reserva'];
							$hora_almacenada = $reg2['horario_reserva'];
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
					mysql_free_result($registros2);

					
					////fecha y hora del turno actual, para resaltarlo en la tabla
					$hoy = date('Y-m-d');
					$hora_actual = date('H');
					$hora_par = (floor($hora_actual/2)*2);
					//echo "<br>hora_par = $hora_par";
					if ($hora_par < 10)
						$inicio_turno = "0".$hora_par.":00:00";
					else
						$inicio_turno = $hora_par.":00:00";
					//echo "<br>inicio_turno = $inicio_turno";
					$datetime_inicio_turno_actual = date('Y-m-d H:i:s',strtotime('+ '.$hora_par.' hours', strtotime($hoy)));
					
					
					//*******************//
					
					/// Actualizamos la vista v, de modo que solo contenga las reservas de las ultimas 4 semanas
					//$hoy = date('Y-m-d');
					$hace_28_dias = date('Y-m-d',strtotime('-28 days', strtotime($hoy)));
					//echo "<br>hace_28_dias = $hace_28_dias"; 
								
					
					//*******************//
					////ahora realizamos la consulta para mostrar Todoos los campos de la vista v (Reservas del ultimo mes) con los tiempos ACTUALIZADOS
					$sql= "SELECT * FROM v WHERE fecha_reserva >= '$hace_28_dias' ORDER BY fecha_reserva desc, horario_reserva desc, num_POD ASC";
					$result=mysql_query($sql,$conexion) or die ("Problemas con el Select (sql) ".mysql_error());
					
					$count=mysql_num_rows($result);       //filas de la tabla 
					$fields=mysql_num_fields($result);    //columnas de la tabla
					
					//mostramos los campos por pantalla
					if(!$result)
					{
					    echo "<br>La vista v est&aacute; vac&iacute;a.<br>";
						$message = 'ERROR:' . mysql_error();
						return $message;
					}
					else
					{
                        echo "<center>";
						echo "  <H3><u>VISTA &Uacute;ltimas RESERVAS &nbsp;&nbsp;<i>(&uacute;ltimas 4 semanas)</i></u></H3>";
						echo "    <table border=1 class=\"thin sortable draggable\">";
						echo "	    <tr>";
						                                                                     
						for ($j=0; $j<$fields; $j++)  //Table Header
						{
							$titulo = mysql_field_name($result, $j);
							echo "<th bgcolor=\"khaki\">  $titulo  </th>";
						}
						echo "     </tr>";
						
						while ($row = mysql_fetch_row($result))   //Table body
						{
							if ($row[1]==$hoy && $row[2]==$inicio_turno)
								$turno_actual=1;
							else
								$turno_actual=0;
							echo "<tr>";
							for ($j=0; $j<$fields; $j++)
							{
							    $campo = $row[$j];
								//echo "<td>  $campo  </td>"; 
								echo "<td "; echo ($turno_actual) ? "bgcolor=\"#ffffff\""  : "bgcolor=$bgcolor";   echo ">  $campo  </td>";
							}
							echo "</tr>\n";
						}
						echo "</table><p>";
						
						echo "</table></center>";
						mysql_free_result($result);
						echo "<p>";
					}			
					break;
					
			default:
			        echo "<br>Error: El n&uacute;umero de la tabla es incorrecto!";

		}
	}  
?>	
	  	  
	<form name="form_tablas" action="ver_tablas.php" method="POST">
      <div align="left">
	    <INPUT type="submit" name="vertablas" value="VER TABLAS" face="algerian" size="5" style="background-color : #555555; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:120px;" />
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