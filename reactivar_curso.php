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

	//si los datos seleccionados son correctos, se procede a la desactivacion del curso elegido
	if (isset($_GET['cursoID'])){
	
		$cursoID = $_GET['cursoID'];
		echo "<br>cursoID = $cursoID";
		
		//comprobamos la opcion de CURSO
		if ($cursoID == 0){
		   	//si no se ha seleccionado ningun curso 
			mysql_close($conexion);
			ob_end_flush();
			header("Location: reactivar_curso.php?erroractivar=nc");
		}else{   
			//si se ha seleccionado algun curso		
			//se procede a activar el curso, de modo que sea accesible, siempre y cuando se encuentre entre las fechas inicial y final del desarrollo de dicho curso
			//SE SUPONE QUE ESTO SE VA A REALIZAR cuando se hayan paliado CASOS EXCEPCIONALES: avería, reparación, catástrofe, ..., que obligaron a desactivar algun curso
			$sql2 = "UPDATE cursos SET curso_activo = 1 WHERE curso_id = $cursoID";
			$registros2 = mysql_query($sql2,$conexion) or die ("Problemas con el Update  (sql2) ".mysql_error()); 

			//se habilitan todos los pods asignados a este curso
			$sql3="SELECT * FROM cursos WHERE curso_id = $cursoID";
			$registros3=mysql_query($sql3,$conexion) or die ("Problemas con el select  (sql3) ".mysql_error());
			$count3 = mysql_num_rows($registros3);
			if ($count3 == 1){
				while ($reg3 = mysql_fetch_array($registros3)){
					$num_max_pods = $reg3['num_max_pods'];
					$nombre_curso = $reg3['nombre_curso'];
				}
			}else
				echo "<br>Error en los identificadores de los cursos!<br>"; 
			
			$flag_pods_ok = pow(2,$num_max_pods)-1;
			$sql4 = "UPDATE cursos SET flag_pods_ok = $flag_pods_ok WHERE curso_id = $cursoID";
			$registros4 = mysql_query($sql4,$conexion) or die ("Problemas con el Update  (sql4) ".mysql_error()); 			
			
			mysql_free_result($registros3);
			mysql_close($conexion);
			ob_end_flush();		


			/////////enviamos un email a todos los usuarios matriculados en ese curso, avisandoles de que se va a reactivar el curso ////
			//echo "<br><br>send email!!!";
			$sql9="SELECT * FROM alumnos_en_cursos WHERE curso_id = $cursoID AND status = 0";
			$registros9=mysql_query($sql9,$conexion) or die ("Problemas con el select  (sql9) ".mysql_error());
			$count9 = mysql_num_rows($registros9);
			if ($count9 > 0){
				while ($reg9 = mysql_fetch_array($registros9)){
					$codigo_usuario = $reg9['user_id'];	

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
							<title>Reactivaci&oacute;n del Curso '.$nombre_curso.'</title>
							</head>
							<body>
							<h3>Hola '.$nombre.'!</h3>
							<p>
							<b>Te informamos que se ha reactivado el curso '.$nombre_curso.'</b>
							</p>
							<p>
							<b>El acceso a dicho curso vuelve a estar habilitado.</b>
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
								echo "<br> Email enviado";
							else
							echo "<br> Email fallido";
						}	
					}		
					mysql_free_result($registros33);							
				}
				
			}			
			mysql_free_result($registros9);			
			
			
			//al terminar el proceso le mando otra vez a la portada 
			header("Location: reactivar_curso.php?erroractivar=no"); 
		}
	}	

	
	//consulta --> Listado de Cursos Inactivos
	$sql1 = "SELECT * FROM cursos WHERE curso_activo = 0 AND fin_curso >= CURDATE()";
	$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el Select (sql1) ".mysql_error());
	$count1=mysql_num_rows($registros1);       //numero de cursos activos 
	

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
	 <title>REACTIVAR CURSOS</title>
 	 <!-- Estas lineas evitan que la pagina deseada no se cargue en la cache y no pueda ser mostrada cuando se regresa atras. -->
	  <meta http-equiv="Expires" CONTENT="0">
	  <meta http-equiv="Cache-Control" CONTENT="no-cache">
	  <meta http-equiv="Pragma" CONTENT="no-cache">
	
	</head>


	<body bgcolor="#<?php echo $bgcolor ?>"> 
	
		<center><u><h1>REACTIVAR CURSO</h1></u>

		<table RULES=NONE FRAME=BOX style="background: #ccffff" border="0">
			<font style="width:100; background: #ffffff; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:14pt; font-weight:bold;  border: 1px solid #ccc;">
		
				<form name="cursoSelect" action="" method="GET">
				    <div align="center"> 
						<tr>
							<?php if (isset($_GET["erroractivar"])) 
								  {
									 if ($_GET["erroractivar"]=="nc") {
							?> 
				            <td colspan="2" align="center" bgcolor="lightblue"><span style="color:ffffff"><b>Curso no introducido</b></span> 

							<?php    }else if ($_GET["erroractivar"]=="no") { ?>
							
							<td colspan="2" align="center" bgcolor="blueslate"><span style="color:ffffff"><b>Curso Desactivado</b></span>

							<?php } } else {				?>			
							<td colspan="2" align="center" bgcolor=e0e0e0>Introduce curso y alumno </span>
							<?php } ?></td> 
						 </tr> 
						</tr>
						<tr>
							<td height="10"></td> 
							<td></td>
						</tr> 	
						<tr>
							<?php  
							  if ($count1 > 0) {
							?>
								<td LABEL for="curso" ALIGN="right" style="font-size:12pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif">CURSO: &nbsp;&nbsp;</LABEL></td>
								<td><SELECT name="cursoID" id="cursoID" style="width:200px; align=center; font-size:11pt; font-weight:bold; background:#ffffff;" > 
								   <option id="encabezado" value="0">---Elige el curso---</option> 
								  <?php
									mysql_data_seek ( $registros1, 0);   //devolvemos el puntero a la posicion 0 de la lista
									while ($reg1=mysql_fetch_array($registros1))
									{         
										echo "<option value='".$reg1['curso_id']."'>".$reg1['nombre_curso']."</option>";
									}	
								  ?>
								</SELECT>
								</td>
								<div id="result">&nbsp;</div>
							<?php 
							   } else {	
							?>
								<td>  
								<font style="width:200; background: #ccffcc; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:14pt; font-weight:bold;  border: 1px solid #ccc;" readonly>No hay cursos inactivos en la actualidad </font>
								<script> alert("No hay cursos inactivos en la actualidad"); </script> </td>
								<td></td>
							<?php 
							   } 
							?>							
						</tr>
						<tr>
							<td height="10"></td> 
							<td></td>
						</tr> 			
				
						<tr>
							<td height="30"></td> 
							<td></td>
						</tr> 	
			
						<tr>
							 <td colspan="2" align="center">
								<INPUT type="submit" name="activar" value="ACTIVAR" face="algerian" size="5" align="center" style="background-color : olive; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:140px;" />
							 </td>
						</tr>
						
						<tr>
							<td height="10"></td> 
							<td></td>
						</tr> 			
				    </div> 
				</form>						
			</font></center>
		</table>
	 
		<br>				
				
	
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
	mysql_free_result($registros1);
	mysql_close($conexion);
	ob_end_flush();		
}
?>