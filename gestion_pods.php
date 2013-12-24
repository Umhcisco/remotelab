<?php 
 include ("headers/seguridad.php");
 include ("headers/conexion.php");

//iniciamos el buffer de salida 
ob_start();

$admin = $_SESSION['admin'];
$idle_timeout = $_SESSION['idle_timeout'];
$control = $_SESSION['control'];
$total_pods = $_SESSION['total_pods'];
$numPods = $_SESSION['numPods'];

$num_Outlets_APC = 8;

if ($control == 0)		
	$max_pods = $numPods;    //solo los pods del curso activo en el cual se ha ingresado 
	//$max_pods = $total_pods;   //pods de todos los cursos activos
else
	$max_pods = $total_pods;
	
					
//si el usuario no es administrador
if ($admin == 0) {
    //Limpiamos y cerramos
	mysql_close($conexion);
	ob_end_flush();		
	session_destroy();
	
    header("Location: index.php?errorusuario=ad");
	
//si es administrador	
}else{

	///se comprueba si se ha pulsado algun boton para la gestion de pods
    if (isset($_POST['flag'])){
		$pod = $_POST['pod'];
		$estado = $_POST['estado'];
		$flag = $_POST['flag'];
		echo "pod=$pod; estado=$estado; flag=$flag";
		if ($estado==1)
			$accion="ON";
		else	
			$accion="OFF";
		
		///si se trata de encender o apagar un outlet (1-8)
		if ($flag==1){
			//si hay que encender (1) o apagar (2) los dispositivos fisicos asociados a un pod (Routers & Switches)
			if (($pod==6)&&($estado==2)){
				exec("/var/www/pod2_down.sh");
				echo "<br>POD 2 tumbado!!!";
				echo "<br>Outlet $pod $accion";
			}else if (($pod==7)&&($estado==2)){
				exec("/var/www/pod3_down.sh");
				echo "<br>POD 3 tumbado!!!";
				echo "<br>Outlet $pod $accion";
			}else if (($pod==8)&&($estado==2)){
				exec("/var/www/pod3_down.sh");
				echo "<br>Dispositivos auxiliares tumbados!!!";
				echo "<br>Outlet $pod $accion";
			}else{
				exec("/var/www/paro_marcha.sh RTSW$pod $accion");    ///http://stackoverflow.com/questions/13903506/pass-php-variables-to-a-bash-script-and-then-launch-it
				echo "<br>Outlet $pod $accion";
			}echo "<br>Operaci&oacute;n realizada con &eacute;xito!!!";
		}
		///si se trata de encender o apagar una máquina física o virtual
		else if ($flag==2){
			//si hay que encender (1) o apagar (2) el PC fisico que alberga a un pod (PC fisico con Ubuntu Server)
			if ($estado==1){
				if ($pod == 2){
					exec('/var/www/pod2_up.sh &');
					echo "<br>POD 2 levantado!!!";			
				}
				else if ($pod == 3){
					exec('/var/www/pod3_up.sh &');
					echo "<br>POD 3 levantado!!!";			
				}
			}else if ($estado==2){
				if ($pod == 2){
					exec('/var/www/pod2_down.sh &');
					echo "<br>POD 2 tumbado!!!";			
				}
				else if ($pod == 3){
					exec('/var/www/pod3_down.sh &');
					echo "<br>POD 3 tumbado!!!";			
				}		
			//si hay que levantar (3) o tumbar (4) las maquinas virtales asociadas a un pod (VirtualBox)
			}else{
				/////exec('/var/www/gestionVMs_desde_PC1.sh $pod $estado &');
				echo "<br>Falta poner la ruta donde esta el script que levanta y tumba las Virtual Machines desde PC1 (que todav&iacute;a no est&aacute; hecho)!!!<br><br>";
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
	<title>CONTROL APC</title>
	 <!-- Estas lineas evitan que la pagina deseada no se cargue en la cache y no pueda ser mostrada cuando se regresa atras. -->
	  <meta http-equiv="Expires" CONTENT="0">
	  <meta http-equiv="Cache-Control" CONTENT="no-cache">
	  <meta http-equiv="Pragma" CONTENT="no-cache">	

	  	<script type="text/javascript">
		  function comprueba_paro(outlet)
		  {
			 if ((outlet == 6) || (outlet == 7)){
				var numpod = outlet - 4;
				var popup = confirm("\u00a1Esta acci\u00f3n provocar\u00e1 el apagado del POD " + numpod + " !!! \n\u00bfEst\u00e1s seguro?","ACEPTAR","CANCELAR");
				if (!popup)
				  return 0;
			 }
			 if (outlet == 8){
				var popup = confirm("\u00a1Esta acci\u00f3n provocar\u00e1 el apagado brusco de los dispositivos auxiliares!!! \ncomo Router de Borde, Switch de Entrada, KVM o el OpenGear\n\u00bfEst\u00e1s seguro?","ACEPTAR","CANCELAR");
				if (!popup)
				  return 0;
			 }
			 if (outlet == 5){
				var popup = confirm("\u00a1En un futuro, esta acci\u00f3n provocar\u00e1 el apagado brusco del POD1 !!!\n\u00bfEst\u00e1s seguro?","ACEPTAR","CANCELAR");
				if (!popup)
				  return 0;
			 }	
			 if (outlet == 4){
				var popup = confirm("\u00a1Esta acci\u00f3n provocar\u00e1 el apagado de los dispositivos asociados al outlet " + outlet + "!!!\n\u00bfEst\u00e1s seguro?","ACEPTAR","CANCELAR");
				if (!popup)
				  return 0;			 
			 }
			 if (outlet == 3){
				var popup = confirm("\u00a1Esta acci\u00f3n provocar\u00e1 el apagado de los Routers & Switches asociados al POD " + outlet + "!!!\n\u00bfEst\u00e1s seguro?","ACEPTAR","CANCELAR");
				if (!popup)
				  return 0;			 
			 }
			 if (outlet == 2){
				var popup = confirm("\u00a1Esta acci\u00f3n provocar\u00e1 el apagado de los Routers & Switches asociados al POD " + outlet + "!!!\n\u00bfEst\u00e1s seguro?","ACEPTAR","CANCELAR");
				if (!popup)
				  return 0;			 
			 }
			 if (outlet == 1){
				var popup = confirm("\u00a1Esta acci\u00f3n provocar\u00e1 el apagado de los Routers & Switches asociados al POD " + outlet + "!!!\n\u00bfEst\u00e1s seguro?","ACEPTAR","CANCELAR");
				if (!popup)
				  return 0;			 
			 }
 			 return 1;
		  }
		</script>

	  	<script type="text/javascript">
		  function comprueba_marcha(outlet)
		  {
			 if ((outlet == 6) || (outlet == 7)){
				var numpod = outlet - 4;
				var popup = confirm("\u00a1Esta acci\u00f3n provocar\u00e1 el encendido del POD " + numpod + " !!! \n\u00bfEst\u00e1s seguro?","ACEPTAR","CANCELAR");
				if (!popup)
				  return 0;
			 }
			 if (outlet == 8){
				var popup = confirm("\u00a1Esta acci\u00f3n provocar\u00e1 el encendido de los dispositivos auxiliares!!! \ncomo Router de Borde, Switch de Entrada, KVM o el OpenGear\n\u00bfEst\u00e1s seguro?","ACEPTAR","CANCELAR");
				if (!popup)
				  return 0;
			 }
			 if (outlet == 5){
				var popup = confirm("\u00a1En un futuro, esta acci\u00f3n provocar\u00e1 el encendido del POD1 !!!\n\u00bfEst\u00e1s seguro?","ACEPTAR","CANCELAR");
				if (!popup)
				  return 0;
			 }	
			 if (outlet == 4){
				var popup = confirm("\u00a1Esta acci\u00f3n provocar\u00e1 el encendido de los dispositivos asociados al outlet " + outlet + "!!!\n\u00bfEst\u00e1s seguro?","ACEPTAR","CANCELAR");
				if (!popup)
				  return 0;			 
			 }
			 if (outlet == 3){
				var popup = confirm("\u00a1Esta acci\u00f3n provocar\u00e1 el encendido de los Routers & Switches asociados al POD " + outlet + "!!!\n\u00bfEst\u00e1s seguro?","ACEPTAR","CANCELAR");
				if (!popup)
				  return 0;			 
			 }
			 if (outlet == 2){
				var popup = confirm("\u00a1Esta acci\u00f3n provocar\u00e1 el encendido de los Routers & Switches asociados al POD " + outlet + "!!!\n\u00bfEst\u00e1s seguro?","ACEPTAR","CANCELAR");
				if (!popup)
				  return 0;			 
			 }
			 if (outlet == 1){
				var popup = confirm("\u00a1Esta acci\u00f3n provocar\u00e1 el encendido de los Routers & Switches asociados al POD " + outlet + "!!!\n\u00bfEst\u00e1s seguro?","ACEPTAR","CANCELAR");
				if (!popup)
				  return 0;			 
			 }
 			 return 1;
		  }
		</script>
		
	  	<script type="text/javascript">
		  function paro_marcha_Outlets(pod,estado)
		  {
			 document.getElementById("pod").value = pod;
			 document.getElementById("estado").value = estado;
			 document.getElementById("flag").value = 1;
		  }
		</script>

		
		<script type="text/javascript">
		  function paro_marcha_PCs(pod,estado)
		  {

			 ///alert("pod = " + pod + "\nestado = " + estado); 
			 switch (estado) {
				case 1:
				    var popup = confirm("\u00bfEst\u00e1 seguro que quieres encender el PC " + pod + "?","ACEPTAR","CANCELAR");
				    if (!popup)
					   return 0;
					break;
				case 2:
				    var popup = confirm("\u00bfEst\u00e1 seguro que quieres apagar el PC " + pod + "?","ACEPTAR","CANCELAR");
				    if (!popup)
					   return 0;
					break;
				case 3:
					 alert("Las m\u00e1quinas virtuales se inician con el reboot de las m\u00e1quinas f\u00edsicas \ny de momento no se pueden apagar desde el servidor web \n\n As\u00ed que no es necesaria esta opci\u00f3n.");
					 return 0;
					 break;
				case 4:
					 alert("En este momento no se ha implementado la funcionalidad de gestionar \nlas m\u00e1quinas virtuales de todos los PODS desde el servidor web. \n\n Esto debe hacerse desde la l\u00ednea de comandos de cada POD.");
					 return 0;
					 break;
				default:
					alert('Error en el estado');
					break;
			 }
			 document.getElementById("pod").value = pod;
			 document.getElementById("estado").value = estado;
			 document.getElementById("flag").value = 2;
			 return 1;
		  }
		</script>
		
		<script type="text/javascript">
		  function accion()
		  {
		     document.forms["form_enviar"].submit();
		  }
		</script>

	</head>

	<body bgcolor="#<?php echo $bgcolor ?>" SCROLLING=NO>

	<h1><b>GESTION de Outlets del APC
	</b></h1>
	<hr>
	<hr>	
	<hr>
	

	<!-- BOTONES DE PARO Y MARCHA PARA LOS DISPOSITIVOS FISICOS asociados a los PODS de los cursos con acceso web VIA RESERVAS -->
	
	<table border="0" align="center">
		<tr>
			<td>
				<H1><B><U><center>Gesti&oacute;n de los Outlets de los Dispositivos de los PODS</center></U></B></H1>		
			</td>
		</tr>
		<tr>
			<td align="center">
			  <?php
				for ($i=0; $i<$max_pods; $i++){
					$j=$i+1;
			  ?>
					<!-- paro DISPOSITIVOS FISICOS (ROUTERS & SWITCHES) ASOCIADOS A CADA POD -->
					&nbsp;&nbsp;&nbsp;&nbsp;
					<button id="paro_o<?php echo $j;?>" class="wrapText" type="button" face="algerian" size="5" style="background-color : #ff0000; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:145px; height:50px" onClick="bool=comprueba_paro(<?php echo $j;?>); if(bool){ paro_marcha_Outlets('<?php echo $j;?>','2'); accion();}">PARO<br>outlet <?php echo $j ?></button>
			  <?php
				}
			  ?>
			</td>

		</tr>
		<tr>
		   <td><br></td>
		</tr>
		<tr>
			<td align="center">
			  <?php
				for ($i=0; $i<$max_pods; $i++){
					$j=$i+1;
			  ?>		
					<!-- marcha DISPOSITIVOS FISICOS (ROUTERS & SWITCHES) ASOCIADOS A CADA POD-->
					&nbsp;&nbsp;&nbsp;&nbsp;
					<button id="marcha_o<?php echo $j;?>" class="wrapText" type="button" face="algerian" size="5" style="background-color : #009900; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:145px; height:50px" onClick="bool=comprueba_marcha(<?php echo $j;?>); if(bool){ paro_marcha_Outlets('<?php echo $j;?>','1'); accion();}">MARCHA<br>outlet <?php echo $j;?></button>
			  <?php
				}
			  ?>
			</td>		
		</tr>
	</table>
	
	<br>
	
	<!-- ****************************************************************** -->
	
	<hr>
	<hr>
	<hr>
	

	<!-- BOTONES DE PARO Y MARCHA PARA LOS DISPOSITIVOS FISICOS asociados a los PODS de los cursos con acceso web VIA SSH (el resto de pods) -->
	
	<table border="0" align="center">
		<tr>
			<td>
				<H1><B><U><center>Gesti&oacute;n del Resto de los Outlets</center></U></B></H1>		
			</td>
		</tr>
		<tr>
			<td align="center">
			  <?php
				for ($i=$max_pods; $i<$num_Outlets_APC; $i++){
					$j=$i+1;
			  ?>
					<!-- paro Resto de los Oulets -->
					&nbsp;&nbsp;&nbsp;&nbsp;
					<button id="paro_o<?php echo $j;?>" class="wrapText" type="button" face="algerian" size="5" style=" <?php if (($j==6)||($j==7)){ ?>   background-color : #AA1C47  <?php } else { ?>  background-color : #ff0000  <?php } ?>; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:145px; height:50px" onClick="bool=comprueba_paro(<?php echo $j;?>); if(bool){ paro_marcha_Outlets('<?php echo $j;?>','2'); accion();}">PARO<br>outlet <?php echo $j ?></button>
			  <?php
				}
			  ?>
			</td>

		</tr>
		<tr>
		   <td><br></td>
		</tr>
		<tr>
			<td align="center">
			  <?php
				for ($i=$max_pods; $i<$num_Outlets_APC; $i++){
					$j=$i+1;
			  ?>		
					<!-- marcha Resto de los Outlets -->
					&nbsp;&nbsp;&nbsp;&nbsp;
					<button id="marcha_o<?php echo $j;?>" class="wrapText" type="button" face="algerian" size="5" style="background-color : #009900; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:145px; height:50px" onClick="bool=comprueba_marcha(<?php echo $j;?>); if(bool){ paro_marcha_Outlets('<?php echo $j;?>','1'); accion();}">MARCHA<br>outlet <?php echo $j;?></button>
			  <?php
				}
			  ?>
			</td>		
		</tr>
	</table>	
	
	<br>

	<!-- ****************************************************************** -->
	
	<hr>
	<hr>
	<hr>

	<!--	<h1><b>GESTION MANUAL de MAQUINAS FISICAS</b></h1>
	<hr>	
	-->

	
	<!-- BOTONES DE PARO Y MARCHA PARA LAS MAQUINAS FISICAS que albergan a los PODS de los cursos con acceso web VIA RESERVAS -->
	
	<table border="0" align="center">
		<tr>
			<td>
				<H1><B><U><center>Gesti&oacute;n PCs FISICOS <br>que albergan los PODS</center></U></B></H1>		
			</td>
		</tr>
		<tr>
			<td align="center">
			  <?php
				for ($i=0; $i<$max_pods; $i++){
					$j=$i+1;
			  ?>
					<!-- paro MAQUINAS FISICAS que ALBERGAN A CADA POD -->	
					&nbsp;&nbsp;&nbsp;&nbsp;
					<button id="paro_f<?php echo $j;?>" class="wrapText" type="button" face="algerian" size="5" style="background-color : #ff0000; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:145px; height:50px" onClick="bool=paro_marcha_PCs(<?php echo $j;?>,2); if(bool) accion();">PARO PC<br>POD <?php echo $j;?></button>														
			  <?php
				}
			  ?>
			</td>

		</tr>
		<tr>
		   <td><br></td>
		</tr>
		<tr>
			<td align="center">
			  <?php
				for ($i=0; $i<$max_pods; $i++){
					$j=$i+1;
			  ?>		
					<!-- marcha MAQUINAS FISICAS que ALBERGAN A CADA POD -->
					&nbsp;&nbsp;&nbsp;&nbsp;
					<button id="marcha_f<?php echo $j;?>" class="wrapText" type="button" face="algerian" size="5" style="background-color : #009900; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:145px; height:50px" onClick="bool=paro_marcha_PCs(<?php echo $j;?>,1); if(bool) accion();">MARCHA PC<br>POD <?php echo $j;?></button>
			  <?php
				}
			  ?>
			</td>		
		</tr>
	</table>
	
	<br>
	
	<!-- ****************************************************************** -->
	
	<hr>
	<hr>
	<hr>
	

	<!-- BOTONES DE PARO Y MARCHA PARA LAS MAQUINAS VIRTUALES asociadas a los PODS de los cursos con acceso web VIA RESERVAS -->
	
	<table border="0" align="center">
		<tr>
			<td>
				<H1><B><U><center>Gesti&oacute;n PCs Virtuales <br>que forman los PODS</center></U></B></H1>		
			</td>
		</tr>
		<tr>
			<td align="center">
			  <?php
				for ($i=0; $i<$max_pods; $i++){
					$j=$i+1;
			  ?>
					<!-- paro MAQUINAS VIRTUALES (SUELO POD + 4 PCs BORDE) ASOCIADAS A CADA POD -->	
					&nbsp;&nbsp;&nbsp;&nbsp;
					<button id="paro_v<?php echo $j;?>" class="wrapText" type="button" face="algerian" size="5" style="background-color : #ff0000; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:145px; height:50px" onClick="bool=paro_marcha_PCs(<?php echo $j;?>,4); if(bool) accion();">PARO VMs<br>POD <?php echo $j;?></button>														
			  <?php
				}
			  ?>
			</td>

		</tr>
		<tr>
		   <td><br></td>
		</tr>
		<tr>
			<td align="center">
			  <?php
				for ($i=0; $i<$max_pods; $i++){
					$j=$i+1;
			  ?>		
					<!-- marcha MAQUINAS VIRTUALES (SUELO POD + 4 PCs BORDE) ASOCIADAS A CADA POD -->
					&nbsp;&nbsp;&nbsp;&nbsp;
					<button id="marcha_v<?php echo $j;?>" class="wrapText" type="button" face="algerian" size="5" style="background-color : #009900; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:145px; height:50px" onClick="bool=paro_marcha_PCs(<?php echo $j;?>,3); if(bool) accion();">MARCHA VMs<br>POD <?php echo $j;?></button>
			  <?php
				}
			  ?>
			</td>		
		</tr>
	</table>
	
	
	<!-- ****************************************************************** -->
	
	<hr>
	<hr>
	<hr>
	

	
	<br>
	

	
	 <form name="form_enviar" ID="form_enviar" action="" method="post">
			<input type="hidden" name="pod" id="pod" value="">
			<input type="hidden" name="estado" id="estado" value="">
	    	<input type="hidden" name="flag" id="flag" value="">
	 </form>
	
	
	<hr>

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
		document.getElementById("paro_f1").disabled = true;
		document.getElementById("marcha_f1").disabled = true;
		
		document.getElementById("paro_f1").style.visibility = "hidden";
		document.getElementById("marcha_f1").style.visibility = "hidden";		
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
	mysql_close($conexion);
	ob_end_flush();		
}
?>

<!-- <br><br><A href="admin.php">VOLVER</A> -->