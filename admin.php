<?php 
 include ("headers/seguridad.php");
 include ("headers/conexion.php");

//iniciamos el buffer de salida 
ob_start();

$admin = $_SESSION['admin'];
$idle_timeout = $_SESSION['idle_timeout'];
$control = $_SESSION['control'];
$id_curso = $_SESSION['id_curso'];
$nombre_curso = $_SESSION['nombre_curso'];

$total_pods = $_SESSION['total_pods'];
$offset_pod = $_SESSION['offset_pod'];
$numPods = $_SESSION['numPods'];
$num_max_pods = $_SESSION['num_max_pods'];

if ($control == 0){		
	$max_pods = $numPods; 
	$primer_pod = $offset_pod+1;
}else{
	$max_pods = $total_pods;
	$primer_pod = 1;
}
	
//si el usuario no es administrador
if ($admin == 0){
    //Limpiamos y cerramos
	mysql_close($conexion);
	ob_end_flush();		
	session_destroy();
	
    header("Location: index.php?errorusuario=ad");
	
//si es administrador	
} else{
							
	///se comprueba si se ha pulsado algun boton para la gestion de pods
    if (isset($_POST['flag'])){
		$pod = $_POST['pod'];
		$estado = $_POST['estado'];
		$flag = $_POST['flag'];
		echo "pod=$pod; estado=$estado; flag=$flag";
		$outlet = $pod;
		
		//// el PC 1 no se puede apagar, ya que alberga el Servidor Web y el resto de servicios, por eso se ha escondido
		switch ($estado) {
			case 1:	$accion = "ON";
					exec("/var/www/paro_marcha.sh RTSW$outlet $accion");   
					echo "<br>Operaci&oacute;n realizada con &eacute;xito en los <i>Routers & Switches</i>!!!";
					break;
		    case 2: $accion = "OFF";
					exec("/var/www/paro_marcha.sh RTSW$outlet $accion");   
					echo "<br>Operaci&oacute;n realizada con &eacute;xito en los <i>Routers & Switches</i>!!!";
					break;
			case 3: $accion = "ON";	
					switch ($pod) {
						 case 2:  exec("/var/www/pod2_up.sh");
								  echo "<br><i>PCs f&iacute;sico $pod</i> levantado!!!";
								  break;
					     case 3:  exec("/var/www/pod3_up.sh");
								  echo "<br><i>PCs f&iacute;sico $pod</i> levantado!!!";
								  break;
					     default: echo "<br>Error en case 3";
								  break;
					};
					break;
			case 4: $accion = "OFF";		
					switch ($pod) {
						 case 2:  exec("/var/www/pod2_down.sh");
								  echo "<br><i>PCs f&iacute;sico $pod</i> tumbado!!!";
								  break;
					     case 3:  exec("/var/www/pod3_down.sh");
								  echo "<br><i>PCs f&iacute;sico $pod</i> tumbado!!!";
								  break;
					     default: echo "<br>Error en case 4";
								  break;
					};	
					break;
			default: echo "<br>Error en case (estado)";
					break;
		};
	}


	///se comprueba si se ha cambiado el estado habilitado o deshabilitado de algun pod
    if (isset($_POST['check_pods'])){
		$old_pod_status = explode(',', $_POST['pods_estado']);
		$pod_number = explode(',', $_POST['pods_numero']);
		//echo "<br>ESTADO ANTERIOR";
		for ($i=0; $i<$max_pods; $i++){
			$index = $pod_number[$i];
			//echo "<br>POD $pod_number[$i] = $old_pod_status[$i]";
		}
		
		////extraemos los resultados de los checkbox
		//echo "<br>ESTADO ACTUAL";
		for ($i=0; $i<$max_pods; $i++){
			$index = $primer_pod + $i;
			$str_name = "pod".$index;
			//echo "<br>str_name = $str_name";
			if (isset($_POST[$str_name]))
				$str_status[$i] = $_POST[$str_name];  //1
			else
				$str_status[$i] = 0;
			$new_pod_status[$i] = $str_status[$i];
			//echo "<br>POD $pod_number[$i] = $new_pod_status[$i]";
		}		
		//comparamos los resultados con los valores almacenados en mysql
		$k = 0;
		$token = 0;
		while (($token == 0) && ($k<$max_pods)){
			if ($new_pod_status[$k] != $old_pod_status[$k])
				$token = 1;
			else
				$k++;
		}
		//echo "<br>k=$k";
		echo "<br>Cambio de estado del pod $pod_number[$k] a ";
		if ($new_pod_status[$k] == 1) echo "ACTIVO"; else echo "INACTIVO";
		
		//se calcula el nuevo codigo de flag_pods_ok para este curso (1:pod activo, 0:pod inactivo)
		// flag_pods_ok = 1*estadopod1+2*estadopod2+4*estadopod3
		$new_flag = 0;
		for($i=0; $i<$max_pods; $i++){
			if ($new_pod_status[$i] == 1)
				$new_flag += pow(2,$i);
			//echo "<br>new_flag = $new_flag ** estado $i = $new_pod_status[$i]";
		}
		//echo "<br>TOTAL new_flag = $new_flag";
		
		///se actualiza la tabla reservas, para reflejar este turno como aprovechado
		$sql2 = "UPDATE cursos SET flag_pods_ok=$new_flag WHERE curso_id = $id_curso";
		$registros2 = mysql_query($sql2,$conexion) or die ("Problemas con el Update  (sql2) ".mysql_error());

		
		/////////////////////////////////////////
		/////////////////////////////////////////
		
		if ($new_pod_status[$k] == 0){
			//se borran los turnos de mantenimiento fijados para ese pod en cualquier turno, para evitar informacion redundante
			$sql9 = "DELETE FROM mantenimiento WHERE num_POD_outage = $pod_number[$k] AND estado_outage = 0";
			$registros9 = mysql_query($sql9,$conexion) or die ("Problemas con el Delete  (sql9) ".mysql_error());	
			echo "<br>Los turnos de mantenimiento pendientes en este pod han sido todos borrados.";				
		
			//en un curso de control, si se ha desactivado un pod, se borran directamente todas las reservas activas del sistema en el pod desactivado
			if ($control == 0){	
				$sql4 = "DELETE FROM reservas WHERE num_POD = $pod_number[$k]";
				$registros4 = mysql_query($sql4,$conexion) or die ("Problemas con el Delete  (sql4) ".mysql_error());	
				echo "<br>Las reservas de este turno para este curso han sido todas borradas.";
			//en el resto de cursos, si se ha desactivado un pod, se intentan mover las reservas de ese pod a otro slot temporal, y si no se puede, se borran
			}else{		
				$sql3 = "SELECT * FROM reservas WHERE num_POD = $pod_number[$k]";
				$registros3 = mysql_query($sql3,$conexion) or die ("Problemas con el Select  (sql3) ".mysql_error());
				$count3 = mysql_num_rows($registros3);
				
				if ($count3 > 0){
					$n = 0;
					//echo "<br>Hay turnos reservados en este pod";
					while ($reg3 = mysql_fetch_array($registros3)){
						$reserva_id = $reg3['reserva_id'];
						$codigo_usuario = $reg3['user_id'];
						$fecha_reserva = $reg3['fecha_reserva'];
						$horario_reserva = $reg3['horario_reserva'];

						///buscamos los pods reservados en este turno
						$sql0 = "SELECT * FROM reservas WHERE fecha_reserva = '$fecha_reserva' AND horario_reserva = '$horario_reserva' AND curso_id = $id_curso ORDER BY num_POD ASC";
						$registros0 = mysql_query($sql0,$conexion) or die ("Problemas con el Select  (sql0) ".mysql_error());
						$count0 = mysql_num_rows($registros0);
						while ($reg0 = mysql_fetch_array($registros0)){
							$pod_temp = $reg0['num_POD'];
							$vector_pods_temp[$n] = $pod_temp;
							$n++;
						}
						
						///buscamos los pods en mantenimiento en este turno
						$pod_inicial=$primer_pod;
						$pod_final=$_pod_inicial+$max_pods-1;
						$sql00 = "SELECT * FROM mantenimiento WHERE fecha_outage = '$fecha_reserva' AND horario_outage = '$horario_reserva' AND num_POD_outage BETWEEN $pod_inicial AND $pod_final ORDER BY num_POD_outage ASC";
						$registros00 = mysql_query($sql00,$conexion) or die ("Problemas con el Select  (sql00) ".mysql_error());
						$count00 = mysql_num_rows($registros00);
						while ($reg00 = mysql_fetch_array($registros00)){
							$pod_temp = $reg0['num_POD'];
							$vector_pods_temp[$n] = $pod_temp;
							$n++;
						}

						///buscamos los pods desactivados (lista negra) en este turno
						$j=0;
						for ($i=0; $i<$max_pods; $i++){							      
							if ($pods_estado[$i] == 0){ 
								$lista_negra_pods[$j] = $pods_numero[$i];
								$j++;
							}
						}
						if ($j>0){
							for ($i=0; $i<$j; $i++){
								$vector_pods_temp[$n] = $lista_negra_pods[$i];
								$n++;								
							}
						}
							
						///ordenamos todos los pods anteriores de menor a mayor
						if (isset($vector_pods_temp)){
							//for ($m=0; $m<count($vector_pods_temp); $m++)
								//echo "<br>vector_pods_temp[$m] = $vector_pods_temp[$m]";
							//echo "<br>n = $n";
							$vector_pods = array_values(array_unique($vector_pods_temp));
							sort($vector_pods);
							$str_pods=implode(',',$vector_pods);
							//echo "<br>str_pods = $str_pods";
						}else{
							//echo "<br>Todos los pods están libres en este turno";
						}	
						mysql_free_result($registros0);
						mysql_free_result($registros00);
						
						$flag_mail = 0;
					
						////si no hay pods libres, no importa buscarlos
						if (count($vector_pods) == $numPods){    
							//echo "<br>No quedan pods libres para cambiar la reserva a otro pod";
							
							///borramos esa reserva del sistema, ante la imposibilidad de reasignarla a otro pod
							$sql4 = "DELETE FROM reservas WHERE user_id = $id_usuario AND curso_id = $id_curso AND fecha_reserva = '$hoy' AND horario_reserva = '$hora_inicial'";
							$registros4 = mysql_query($sql4,$conexion) or die ("Problemas con el Delete  (sql4) ".mysql_error());
							//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
						
						}else{
							///hay algun pod libre, asi que lo buscamos
							$nuevo_pod = $primer_pod;
							for ($i=0; $i<=count($vector_pods); $i++){
								if ($vector_pods[$i] == ($primer_pod + $i) ){
									$nuevo_pod++;
								}
							}
							//echo "<br>nuevo_pod = $nuevo_pod";
							$flag_mail = $nuevo_pod;

							$sql7="UPDATE reservas SET num_POD = $nuevo_pod where reserva_id = $reserva_id"; 
							$registros7=mysql_query($sql7,$conexion) or die("Problemas en el Insert (sql7) ".mysql_error());
						}			
	
						//echo "<br>flag_mail = $flag_mail";
						
						/////////enviamos un email a los usuarios a los cuales se les ha anulado una reserva al sobreponer un turno de mantenimiento sobre una reserva anterior////
						///////// y no ha sido posible resituarlos en otro pod dentro del mismo intervalo temporal de forma transparente al usuario
						if ($flag_mail > 0){
							//echo "<br><br>send email!!!";		
							
							$sql33 = "SELECT * FROM datos_pers WHERE user_id = $codigo_usuario";
							$registros33 = mysql_query($sql33,$conexion) or die ("Problemas con el Select  (sql33) ".mysql_error());
							$count33 = mysql_num_rows($registros33);
							
							if ($count33 > 0){
								$diaRsv = substr($fecha_reserva,8,2);
								$mesRsv = substr($fecha_reserva,5,2);
								$anyoRsv = substr($fecha_reserva,0,4);
								$horaRsv = substr($horario_reserva,0,2);
									
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
									<i>Se ha <b>anulado</b> tu Reserva para el curso '.$nombre_curso.'</i>
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
										echo "<br> Email alumno enviado";
									else
										echo "<br> Email alumno fallido";
								}
							}
							mysql_free_result($registros33);
						}
						///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					}	
				
				}	
				mysql_free_result($registros3);
			}
			/////////////////////////////////////////
			/////////////////////////////////////////

		
			//////////////////////////////////////////////////////////////////
			echo "<br>Send email administradores cuando se desactiva un pod!";

			$destino_admin[0] = "remotelab.umh@gmail.com";
			$destino_admin[1] = "pedrojroig@hotmail.com";
			
			$asunto = "Desactivación del POD $new_pod_status[$k]";
					
			$cuerpo = '
			<html>
			<head>
			<title>Desactivaci&oacute;n del POD '.$new_pod_status[$k].'</title>
			</head>
			<body>
			<h3>Hola administrador!</h3>
			<p>
			<b>Se ha desactivado temporalmente el POD '.$new_pod_status[$k].'</b>
			</p>
			<p>
			<b>para el curso '.$nombre_curso.'</b>
			</p>
			<p>
			<i>Recuerda que cuando el pod esté de nuevo activo, debes habilitarlo manualmente</i>
			</p>
			<p>
			<i>marcando su casilla correspondiente, ya sea en la p&aacute;gina de admin o en deshabilitar_pods.</i>
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
			
			for ($i=0; $i<count($destino_admin); $i++){
				$booleano = mail($i,$asunto,$cuerpo,$headers);
				
				echo "<br>booleano = $booleano";
				if ($booleano)
					echo "<br> Email admin para $destino_admin[$i] enviado";
				else
					echo "<br> Email admin para $destino_admin[$i] fallido";
			}
		}
		
		////////////////////////////////////////
		////////////////////////////////////////

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
	<title>ADMIN</title>
	 <!-- Estas lineas evitan que la pagina deseada no se cargue en la cache y no pueda ser mostrada cuando se regresa atras. -->
	  <meta http-equiv="Expires" CONTENT="0">
	  <meta http-equiv="Cache-Control" CONTENT="no-cache">
	  <meta http-equiv="Pragma" CONTENT="no-cache">	

		<style type="text/css">
			#formulario { font:11px arial; width:300px; float:left;}
			#formulario form { margin:0px; padding:0px; }
			#formulario fieldset { border:1px solid #ccc; padding-top:10px;}
			#formulario legend { font-weight:bold; color:#666; }
			#formulario label { font-weight:bold; display:block; width:130px; float:left; padding-left:10px;}
			#formulario input { font:11px arial; background-color:#F7F4FF; border:1px solid #A5ACB2; height:18px; width:130px; padding-left:5px; }
			#formulario br { display:block; margin-bottom:10px; clear:both; }

			#formulario2 { font:11px arial; width:300px; float:right;}
			#formulario2 form { margin:0px; padding:0px; }
			#formulario2 fieldset { border:1px solid #ccc; padding-top:10px;}
			#formulario2 legend { font-weight:bold; color:#666; }
			#formulario2 label { font-weight:bold; display:block; width:130px; float:left; padding-left:10px;}
			#formulario2 input { font:11px arial; background-color:#F7F4FF; border:1px solid #A5ACB2; height:18px; width:130px; padding-left:5px; }
			#formulario2 br { display:block; margin-bottom:10px; clear:both; }
		</style>


		<script type="text/javascript">
		  function paro_marcha(pod,estado)
		  {
			 //if (estado == 3){
			 //	 alert("Las m\u00e1quinas virtuales se inician con el reboot de las m\u00e1quinas f\u00edsicas \ny de momento no se pueden apagar desde el servidor web \n\n As\u00ed que no es necesaria esta opci\u00f3n.");
			 //	 return 0;
			 // }
			 // else if (estado == 4){
			 //	 alert("En este momento no se ha implementado la funcionalidad de gestionar \nlas m\u00e1quinas virtuales de todos los PODS desde el servidor web. \n\n Esto debe hacerse desde la l\u00ednea de comandos de cada POD.");
			 //	 return 0;
			 // }
			 if (estado == 1){
				 alert("\u00a1Se van a encender los Routers & Switches asociados al POD " + pod + " !!!");
				 //return 0;
			 }
			 else if (estado == 2){
				 alert("\u00a1Se van a apagar los Routers & Switches asociados al POD " + pod + " !!!");				 
				 //return 0;
			 }
			 else if (estado == 3){
			   var popup = confirm("\u00a1Aseg\u00farate de que quieres encender la m\u00e1quina f\u00edsica " + pod + " !","ACEPTAR","CANCELAR");
			   if (!popup)
				  return 0;
			 }
			 else if (estado == 4){
			   var popup = confirm("\u00a1Aseg\u00farate de que quieres apagar la m\u00e1quina f\u00edsica " + pod + " !","ACEPTAR","CANCELAR");
			   if (!popup)
				  return 0;			 
			 }
			 document.getElementById("pod").value = pod;
			 document.getElementById("estado").value = estado;
			 document.getElementById("flag").value = 1;
			 return 1;
		  }
		</script>
		
		<script type="text/javascript">
		  function accion()
		  {
		     document.forms["form_enviar"].submit();
		  }
		</script>

	
		<script type="text/javascript">
		  function cambio_estado(){	
		  	alert ("CAMBIO DE ESTADO");
			var max_pods = "<?php echo $max_pods ?>";	
			for (i=0; i<max_pods; i++){
				var j=i+1;
				var str = "pod"+j;
				if (document.getElementById(str).checked)
					document.getElementById(str).value = 1;
				else
					document.getElementById(str).value = 0;
			}
		  
			for (i=0; i<max_pods; i++){	
				var j=i+1;
				var str = "pod"+j;			
		  		var name = document.getElementById(str).name;
				var value = document.getElementById(str).value;
				//alert(name + " = " + value);
			}
			document.getElementById("check_pods").value=1;
			document.forms["form_pods"].submit();			
		  }
		</script>
	 
	</head>


	<body bgcolor="#<?php echo $bgcolor ?>" SCROLLING=NO>

	<h1><b>P&Aacute;GINA DE ADMINISTRACI&Oacute;N
		<?php if ($control==1) echo "&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;
									<i>WEB DE CONTROL</i>"; ?>
	</b></h1>
	<hr>

	<table>
	  <tbody>
		<tr>
		   <td style="padding-right: 15px; margin-top: 0px; padding-left: 15px; padding-bottom: 15px; width: 20%; padding-top: 15px">
			  <p style="text-align: justify">
	 
				 <!-- Escribir aquí el texto de la 1º columna -->

				
				 <h2><A href="mantenimiento.php">MANTENIMIENTO</A></h2>   
					 
				 <font size="1"><br></font>
				
   				 <?php if ($control == 0){	?>			 
					 <h2><A href="reservas.php">RESERVAS</A></h2>
				 <?php }else{ ?>
					 <h2></h2>	
				 <?php } ?>
				 
				 <font size="1"><br></font>	

				 <h2><A href="ver_tablas.php">VER_TABLAS</A></h2>

				 <font size="1"><br></font>
				 
				 <h2><A href="mantenimiento_semanal.php">OUTAGE_SEMANAL</A></h2>	
				 
				 <font size="1"><br></font>
				 
				 <h2><A href="estadisticas.php">ESTADISTICAS</A></h2>

				 <font size="1"><br></font>
				 
				 <h2><A href="gestion_pods.php">GESTION OUTLETS APC</A></h2>			 		 

				 <font size="1"><br></font>
				 
				 <h2><A href="deshabilitar_pods.php">DESHABILITAR PODS</A></h2>

				 
			  </p>

		   <td style="padding-right: 15px; margin-top: 0px; padding-left: 15px; padding-bottom: 15px; width: 20%; padding-top: 15x">
			  <p style="text-align: justify">

				 <!-- Escribir aquí el texto de la 2º columna -->
	 

				 <h2><A href="listar_cursos_activos.php">LISTAR_CURSOS_ACTIVOS</A></h2>

				 <font size="1"><br></font>

				 <h2><A href="gestionar_cursos.php">GESTIONAR_CURSOS</A></h2>

				 <font size="1"><br></font>
				 
				 <h2><A href="http://192.168.100.230/nagios3" target="_blank">VER NAGIOS</A></h2>

				 <font size="1"><br></font>
				 
				 <h2><A href="mapa_reservas.php">MAPA de RESERVAS</A></h2>
				 
				 <font size="1"><br></font>
				 
				 <h2><A href="ver_reservas_pendientes.php">VER_RESERVAS_PENDIENTES </A></h2>

				 <font size="1"><br></font>
				 
				 <h2><A href="ver_mantenimientos_pendientes.php">VER_MANTENIMIENTOS_PENDIENTES</A></h2>
				 
				 <font size="1"><br></font>
				 
				 <h2><A href="listar_cursos_pasados.php">LISTAR_CURSOS_NO_ACTIVOS</A></h2>

				 
			  </p>
		  
		   </td>
		</tr>
	  </tbody>
	</table>

	<hr>
	<hr>

	<H1><B>
	  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	  <U>GESTION DISPOSITIVOS y PCs</U>
	</B></H1>

	<!-- BOTONES DE PARO Y MARCHA PARA LOS DISPOSITIVOS DE RED ASOCIADOS A CADA POD  Y  LAS MAQUINAS FISICAS QUE ALBERGAN LOS PODS -->

	<table border="0" >
		<tr>
			<td>
			  <?php
				if ($control == 0)		
					$max_pods = $numPods; 
				else
					$max_pods = $total_pods;
				for ($i=0; $i<$max_pods; $i++){
					$j=$i+1;
			  ?>
					<!-- paro DISPOSITIVOS FISICOS (ROUTERS & SWITCHES) ASOCIADOS A CADA POD -->
					&nbsp;&nbsp;&nbsp;&nbsp;
					<button id="paro_rs<?php echo $j;?>" class="wrapText" type="button" face="algerian" size="5" style="background-color : #ff0000; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:145px; height:50px" onClick="bool=paro_marcha(<?php echo $j;?>,2); if(bool) accion();">PARO R&S<br>dispositivos <?php echo $j ?></button>
			  <?php
				}
			  ?>
			</td>
			<td width="8%">
			</td>
			<td>
			  <?php
				for ($i=0; $i<$max_pods; $i++){
					$j=$i+1;
			  ?>
					<!-- paro MAQUINAS FISICAS QUE ALBERGAN CADA POD -->	
					&nbsp;&nbsp;&nbsp;&nbsp;
					<button id="paro_pc<?php echo $j;?>" class="wrapText" type="button" face="algerian" size="5" style="background-color : #ff0000; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:145px; height:50px" onClick="bool=paro_marcha(<?php echo $j;?>,4); if(bool) accion();">PARO PC<br>POD <?php echo $j;?></button>														
			  <?php
				}
			  ?>
			</td>
		</tr>
		<tr>
		   <td span="3"><br></td>
		</tr>
		<tr>
			<td>
			  <?php
				for ($i=0; $i<$max_pods; $i++){
					$j=$i+1;
			  ?>		
					<!-- marcha DISPOSITIVOS FISICOS (ROUTERS & SWITCHES) ASOCIADOS A CADA POD-->
					&nbsp;&nbsp;&nbsp;&nbsp;
					<button id="marcha_rs<?php echo $j;?>" class="wrapText" type="button" face="algerian" size="5" style="background-color : #009900; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:145px; height:50px" onClick="bool=paro_marcha(<?php echo $j;?>,1); if(bool) accion();">MARCHA R&S<br>dispositivos <?php echo $j;?></button>
			  <?php
				}
			  ?>
			</td>
			<td width="8%">
			</td>
			<td>
			  <?php
				for ($i=0; $i<$max_pods; $i++){
					$j=$i+1;
			  ?>						
					<!-- marcha MAQUINAS FISICAS QUE ALBERGAN CADA POD -->	
					&nbsp;&nbsp;&nbsp;&nbsp;
					<button id="marcha_pc<?php echo $j;?>" class="wrapText" type="button" face="algerian" size="5" style="background-color : #009900; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:145px; height:50px" onClick="bool=paro_marcha(<?php echo $j;?>,3); if(bool) accion();">MARCHA PC<br>POD <?php echo $j;?></button>
			  <?php
				}
			  ?>
			</td>		
		</tr>
	</table>	  
	<br>
	
	<hr>
	<hr>

	
	<script type="text/javascript">
		document.getElementById("paro_pc1").disabled = true;
		document.getElementById("marcha_pc1").disabled = true;
		
		document.getElementById("paro_pc1").style.visibility = "hidden";
		document.getElementById("marcha_pc1").style.visibility = "hidden";		
	</script>
	
	

<!-- ///////////////////////////////////////////////////////////////////////////////////// -->	

	<table border="1" width="100%">
	  <tr>
	     <td width="25%"> 
		 </td>
		 
		 <td width="50%">
			<table border="1">
			  <tr>
				<td width="60%" colspan="2" align="center">
					<H1><B>
					  <!-- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
					  <U>PODS HABILITADOS</U>
					</B></H1>
				</td>
			  </tr>
			  <tr>
				<td width="30%">	
					
					<form name="form_pods" id="form_pods" action="" method="POST">
						<?php
						
							///////////////////////////////////////////////////

/*						
							if ($id_curso==6){
								///leemos la ultima linea del fichero pods_ok.txt, para ver si los pods estan activos o desactivados
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
								//echo "<br>$chunk[0]<br>$chunk[1]<br>$chunk[2]<br>$chunk[3]<br>$chunk[4]";
								$estado_pod1 = $chunk[2];
								$estado_pod2 = $chunk[3];
								$estado_pod3 = $chunk[4];
							}
*/

							/////////se cambiara la lectura del fichero y se quitara esto////////////////
							////////////////////////////////////////////////////////////////////////////
							// seleccionamos el estado de los pods del curso elegido
							$sql1="SELECT * FROM cursos WHERE curso_id = $id_curso";
							$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el Select  (sql1) ".mysql_error());
							if ($reg1=mysql_fetch_array($registros1)){
							   $flag_pods_ok = $reg1['flag_pods_ok'];
							}else{
								echo "<br>Error en la variable de session id_curso";
								mysql_free_result($registros1);
								mysql_close($conexion);
								ob_end_flush();

								//si s pierde la variable de sesion le mando otra vez a la portada 
								header("Location: index.php?errorusuario=vs"); 								
							}

							function truemod($num, $mod) {
							   return ($mod + ($num % $mod)) % $mod;
							}

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
						

							for ($i=0; $i<$max_pods; $i++){
								$j = $primer_pod + $i;
						?>
								<h2>&nbsp;&nbsp;POD <?php echo "$j";?>: &nbsp; <input type="checkbox" name="pod<?php echo"$j";?>" id="pod<?php echo"$j";?>" value="" onChange="cambio_estado();"> &nbsp;&nbsp:&nbsp; <?php if ($pods_estado[$i] == 1) echo "ACTIVO"; else echo "INACTIVO"; ?></h2>
								<p>
						
						<?php
							}
						?>
								<input type="hidden" name="pods_estado" value="<?php echo implode(',', $pods_estado); ?>">
								<input type="hidden" name="pods_numero" value="<?php echo implode(',', $pods_numero); ?>">	
								<input type="hidden" name="check_pods" id="check_pods" value="">	
							
					</form>
				</td>
				<td width="30%">
					<table border="0">
					  <tr>
						<td>
						  <h2><b>&nbsp;&nbsp;<u>LISTA NEGRA PODS</u></b></h2>
						</td>
					  </tr>
					  <tr>
						 <td>
							&nbsp;&nbsp;
							<font style="width:200; background: #ccffcc; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:14pt; font-weight:bold;  border: 1px solid #ccc;" readonly>
							   <?php
									$j=0;
									for ($i=0; $i<$max_pods; $i++){							      
										if ($pods_estado[$i] == 0){ 
											if ($j == 0)
												echo " &nbsp;<u>POD ".$pods_numero[$i]."</u>&nbsp;";
											else
												echo " &nbsp;&nbsp;<u>POD ".$pods_numero[$i]."</u>&nbsp;";
											$lista_negra_pods[$j] = $pods_numero[$i];
											$j++;
										}
									}
							   ?>
							</font>
						 </td>
					  </tr>
					</table>
				</td>
			  </tr>
			</table>
		 </td>
		 
	     <td width="25%"> 
		 </td>
	  <tr>
	</table>
	
	<hr>
	<hr>
	
	 
	 
	 <form name="form_enviar" ID="form_enviar" action="" method="post">
			<input type="hidden" name="pod" id="pod" value="">
			<input type="hidden" name="estado" id="estado" value="">
	    	<input type="hidden" name="flag" id="flag" value="">
	 </form>
	
	
	
	<form name="form_salida_volunt" ID="form_salida_volunt" action="finalizar.php" method="post">
	   <input type="hidden" name="salida_volunt" ID="salida_volunt" value="2">


	<table>
	  <tbody>
		<tr>
		   <td style="padding-right: 15px; margin-top: 0px; padding-left: 15px; padding-bottom: 3px; width: 20%; padding-top: 3px">
			  <p style="text-align: justify">

				   <h2><a href="#" onclick="document.forms['form_salida_volunt'].submit(); return false;">SALIR</A></h2>

			  </p>

		   <td style="padding-right: 15px; margin-top: 0px; padding-left: 15px; padding-bottom: 3px; width: 20%; padding-top: 3px">
			  <p style="text-align: justify">

					 <div align="center">
						<INPUT type="submit" name="salir" value="SALIR" face="algerian" size="5" style="background-color : #555555; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:120px;" />
					 </div>

			  </p>
		  
		   </td>
		</tr>
	  </tbody>
	</table>

	</form>


	<hr>


	
	<script type="text/javascript">
	    ///paso del array $pods_estado de php a javascript
		var arreglo = new Array();

		<?php
		  for ($i = 0; $i < count($pods_estado); $i++){
		?>
			arreglo[<?php echo $i ?>] = "<?php echo $pods_estado[$i]; ?>";
		<?php
		  }
		?>

		//*******************************************************
		for(var i=0; i<arreglo.length; i++){
		  //document.write(arreglo[i]+"<br />");
		}
	</script> 


	<script type="text/javascript">
		////se marcan los checkbox correspondientes a los pods activos, dejando desmarcados los inactivos
		var max_pods = "<?php echo $max_pods ?>";	
		var primer_pod = "<?php echo $primer_pod ?>";
		//document.write("max_pods = " + max_pods +"; primer_pod = " + primer_pod);
		for (i=0; i<max_pods; i++){
			var numero_pod = parseInt(primer_pod,10) + i;
			var str = "pod"+numero_pod;  
			//document.write("<br>str = "+str);
			if (arreglo[i] == 0){
				document.getElementById(str).checked = false;
			}else{
				document.getElementById(str).checked = true;
			}
		}
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

	  /////////////////////////////////////
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

    <br>
    <table>
	   <tr>
	      <td width = "34%">
			 <form name="form_salida_timeout" id="form_salida_timeout" action="finalizar.php" method="post">
			    <div style="overflow:hidden; width:80px; background: transparent no-repeat>
				  <input type="text" name="timeout" id="SecondsUntilExpire" style="width:100px;" readonly>
			    </div>

			    <input type="hidden" name="salida_timeout" id="salida_timeout" value="-2">
		  	 </form>
		  </td>
		  <td size="300px"></td>
		  <td width="66%">
		  
			<!--[if lt IE 9]>
				<IMG SRC="fotos/R&S3.png" name="logo" id="logo" alt="logo" width="200px" height="134px" onContextMenu="return(false)">			
			<![endif]-->

			<!--[if gte IE 9]>			   
			   <?php // convert image to dataURL
				 $img_source = "fotos/R&S3.png"; // image path/name
				 $img_binary = fread(fopen($img_source, "r"), filesize($img_source));
				 $img_string = base64_encode($img_binary);
			   ?>
			   <IMG SRC="data:image/gif;base64,<?php echo $img_string; ?>" name="logo" id="logo" alt="logo" width="200px" height="134px">
			<![endif]-->
			
			<!--[if !IE]>-->
			   <?php // convert image to dataURL
				 $img_source = "fotos/R&S3.png"; // image path/name
				 $img_binary = fread(fopen($img_source, "r"), filesize($img_source));
				 $img_string = base64_encode($img_binary);
			   ?>
			   <IMG SRC="data:image/gif;base64,<?php echo $img_string; ?>" name="logo" id="logo" alt="logo" width="200px" height="134px">			
			<!--<![endif]-->
			
		  </td>
	   </tr>
    </table>
   

	</body>
	</html>


<?php
    //Limpiamos y cerramos
	mysql_free_result($registros1);
	mysql_close($conexion);
	ob_end_flush();		
}
?>

<!-- <br><br><A href="reservas.php">VOLVER</A> -->