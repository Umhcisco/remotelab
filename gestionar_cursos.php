<?php 
 include ("headers/seguridad.php");
 include ("headers/conexion.php");

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

	</head>


	<body bgcolor="#<?php echo $bgcolor ?>" SCROLLING=NO>

	<center><u><h1>GESTIONAR CURSOS</h1></u></center>
	<hr>


	<table>
	  <tbody>
		<tr>
		   <td style="padding-right: 15px; margin-top: 0px; padding-left: 15px; padding-bottom: 15px; width: 20%; padding-top: 15px">
			  <p style="text-align: justify">
	 
				 <!-- Escribir aquí el texto de la 1º columna -->


 				 <h2><A href="crear_nuevo_curso.php">CREAR_NUEVO_CURSO</A></h2>   
				 
				 <font size="1"><br></font>
				 
				 <h2><A href="ver_cursos_y_sus_pods.php">VER_CURSOS_Y_SUS_PODS</A></h2>
				 
				 <font size="1"><br></font>	
				 
				 <h2><A href="ver_cursos_en_ejecucion.php">VER_CURSOS_EN_EJECUCION</A></h2>
				 
				 <font size="1"><br></font>	


<!--				 <h2><A href="ver_mantenimientos_pendientes.php">VER_MANTENIMIENTOS_PENDIENTES</A></h2>				 -->

				 
			  </p>

		   <td style="padding-right: 15px; margin-top: 0px; padding-left: 15px; padding-bottom: 15px; width: 20%; padding-top: 15x">
			  <p style="text-align: justify">

				 <!-- Escribir aquí el texto de la 2º columna -->
	 
	 
				 <h2><A href="introducir_tokens.php">INTRODUCIR_TOKENS</A></h2>

				 <font size="1"><br></font>
				 
				 
				 <h2><A href="desactivar_curso.php">DESACTIVAR_CURSO</A></h2>

				 <font size="1"><br></font>

				 <h2><A href="reactivar_curso.php">REACTIVAR_CURSO</A></h2>

				 <font size="1"><br></font>	

				 
<!--			     <h2><A href="mantenimiento_semanal.php">MANTENIMIENTO_SEMANAL</A></h2>					-->
				 
				 
			  </p>
		  
		   </td>
		</tr>
	  </tbody>
	</table>

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
	//mysql_free_result($registros1);
	//mysql_free_result($registros2);
	mysql_close($conexion);
	ob_end_flush();	

}
?>