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

	///se comprueba si se ha pulsado algun boton para la gestion de pods
    if (isset($_POST['flag'])){
	
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
	
		$hora = date('H');
		$turno_inicio = floor($hora/2)*2;
		$hora_start = zerofill($turno_inicio,2).":00:00";
		//echo "<br>TURNO ACTUAL: $hora_start";
		$hoy = date('Y-m-d');
		
		$sql0 = "DELETE FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$hora_start'"; 
		$registros0 = mysql_query($sql0,$conexion) or die ("Problemas con el Delete (sql0) ".mysql_error());				

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
	 <title>VER TABLAS</title>
	 <!-- Estas lineas evitan que la pagina deseada no se cargue en la cache y no pueda ser mostrada cuando se regresa atras. -->
	  <meta http-equiv="Expires" CONTENT="0">
	  <meta http-equiv="Cache-Control" CONTENT="no-cache">
	  <meta http-equiv="Pragma" CONTENT="no-cache">
	  
	  
	  <script type="text/javascript">
  
		function current_form(current_form) {
			var error_message =""
			// añadimos propiedades a select multiples
			current_form["tablas"].field_name = "TABLAS";
			// maximo de elementos seleccionados permitidos
				current_form["tablas"].max_selected = 10;
			// minimo  permitidos
			current_form["tablas"].min_selected = 1;

			// recorremos todo los campos
			for (var ctr1 = 0; field_m_select = current_form[ctr1]; ctr1++)
				{
				// si es un select multiple y hemos añadido las propiedades el campo es obligatorio
				if (field_m_select.type == "select-multiple" && field_m_select.max_selected)
						{
					(function()
								 {
						var cuantos = 0;
						for (var ctr = 0; opt = field_m_select.options[ctr]; ctr++) 
										{
						   if (opt.selected) cuantos ++
						}
					
							if (cuantos > field_m_select.max_selected || cuantos < field_m_select.min_selected )
										{
							  if (field_m_select.max_selected == field_m_select.min_selected)
										  {
								error_message += "En el campo " + field_m_select.field_name + " debe seleccionar " + field_m_select.min_selected + 
									 (field_m_select.min_selected > 1 ?" opciones ":" opción")+".";					
							  }
							  else
										  {
								 error_message += "En el campo " + field_m_select.field_name + " debe seleccionar un minimo de " + field_m_select.min_selected + 
									" y un m\u00e1ximo de " + field_m_select.max_selected + (field_m_select.max_selected > 1 ? " opciones":" opción")+ ".\n";
							  }
							}
						 })(field_m_select);
				}
			}

			// Si el mensaje no está vacío mostramos el error
			if(error_message != "")
				{
				   alert("ERROR\n\n" + error_message)
				}
			else 
				{   
				   //alert("Enviamos el formulario.");
				   document.forms["form_tablas"].submit();
				}

		}

		//-->

		</script>

		
		<script type="text/javascript">
		  function limpia_turno_actual()
		  {
			 document.getElementById("flag").value = 1;
			 document.forms["form_limpiar_turno_actual"].submit();
		  }
		</script>
		
		</head>


	<body bgcolor="#<?php echo $bgcolor ?>">

		  <font size="3" face="Tahoma" color="blue"><center><B><u>SELECCIONA TABLAS</u></B></center></font>
		  <font size="1"><br></font>

		  <form name="form_tablas" action="ver_tablas2.php" method="POST">
			<center>

			 <div style="overflow:hidden; width:250px; background: transparent no-repeat right #ffff66;">
			   <SELECT name="tablas[]" ID="tablas" size="15" style="font-size:11pt; font-weight:bold;  width:270px; border: 1px solid #ccc;" multiple="multiple">

				   <option value="0" id="mis_tablas0" style="background: <?php if (0 % 2 == 0) echo "#ffff66;"; else echo "#ffc31e;"; ?> " >0.- Todas las tablas</option>  
		 
				   <option value="1" id="mis_tablas1" style="background: <?php if (1 % 2 == 0) echo "#ffff66;"; else echo "#ffc31e;"; ?> " >1.- ALUMNOS_EN_CURSOS</option>
				  
				   <option value="2" id="mis_tablas2" style="background: <?php if (2 % 2 == 0) echo "#ffff66;"; else echo "#ffc31e;"; ?> " >2.- CURSOS</option>

				   <option value="3" id="mis_tablas3" style="background: <?php if (3 % 2 == 0) echo "#ffff66;"; else echo "#ffc31e;"; ?> " >3.- DATOS_PERS</option>
					
				   <option value="4" id="mis_tablas4" style="background: <?php if (4 % 2 == 0) echo "#ffff66;"; else echo "#ffc31e;"; ?> " >4.- LOGS</option> 
				   
				   <option value="5" id="mis_tablas5" style="background: <?php if (5 % 2 == 0) echo "#ffff66;"; else echo "#ffc31e;"; ?> " >5.- LOG_DETALLES</option>
				   
				   <option value="6" id="mis_tablas6" style="background: <?php if (6 % 2 == 0) echo "#ffff66;"; else echo "#ffc31e;"; ?> " >6.- MANTENIMIENTO</option>
					
				   <option value="7" id="mis_tablas7" style="background: <?php if (7 % 2 == 0) echo "#ffff66;"; else echo "#ffc31e;"; ?> " >7.- MENSAJES_TEMP</option>
		 
				   <option value="8" id="mis_tablas8" style="background: <?php if (8 % 2 == 0) echo "#ffff66;"; else echo "#ffc31e;"; ?> " >8.- PARAMETROS_BASICOS</option>

				   <option value="9" id="mis_tablas9" style="background: <?php if (9 % 2 == 0) echo "#ffff66;"; else echo "#ffc31e;"; ?> " >9.- RESERVAS</option>

				   <option value="10" id="mis_tablasX" style="background: <?php if (10 % 2 == 0) echo "#ffff66;"; else echo "#ffc31e;"; ?> " >X.- USUARIOS</option>               

				   <option value="11" id="mis_tablas11" style="background: <?php if (11 % 2 == 0) echo "#99ffff	;"; else echo "#ff99ff;"; ?> " >11.- RADCHECK</option>

				   <option value="12" id="mis_tablas12" style="background: <?php if (12 % 2 == 0) echo "#99ffff;"; else echo "#ff99ff;"; ?> " >12.- RADREPLY</option>

				   <option value="13" id="mis_tablas13" style="background: <?php if (13 % 2 == 0) echo "#ff9999;"; else echo "#ffffcc;"; ?> " >13.- NAS</option>

				   <option value="14" id="mis_tablas14" style="background: <?php if (14 % 2 == 0) echo "#00ff00;"; else echo "#ffffff;"; ?> " >14.- VISTA &Uacute;ltimas RESERVAS</option>
				   
			   </SELECT> 
			 </div> 

			 <font size="1"><br></font>
			 <INPUT type="button" name="MOSTRAR" value="MOSTRAR" face="algerian" size="5" 
			   style="background-color : red; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:120px;" 
				onClick="current_form(this.form);" />

		   </center>
		  </form>


		  <form name="form_volver" action="admin.php" method="POST">
		  	<div align="left">
			  <INPUT type="submit" name="volver" value="VOLVER" face="algerian" size="5" style="background-color : #555555; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:120px;" />
			</div>

			<div align="right">
			  <INPUT type="submit" name="volver" value="VOLVER" face="algerian" size="5" style="background-color : #555555; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:120px;" />
			</div>
			
			
			<!--
			<div align="center">
			  <INPUT type="button" name="limpiar_turno_actual" value="LIMPIAR TURNO ACTUAL" face="algerian" size="5" style="background-color : #00ff00; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:240px;" onClick="limpia_turno_actual();"/>
			</div>
			-->
		  </form>					
		  
		<hr>
		

		<form name="form_limpiar_turno_actual" ID="form_limpiar_turno_actual" action="" method="post">
	    	<input type="hidden" name="flag" id="flag" value="">
		</form>
	 
		
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
				<IMG SRC="fotos/R&S2.png" name="logo" id="logo" alt="logo" width="400px" height="200px" onContextMenu="return(false)">			
            <![endif]-->
			
			<!--[if gte IE 9]>
			   <?php // convert image to dataURL
				 $img_source = "fotos/R&S2.png"; // image path/name
				 $img_binary = fread(fopen($img_source, "r"), filesize($img_source));
				 $img_string = base64_encode($img_binary);
			   ?>
			   <IMG SRC="data:image/gif;base64,<?php echo $img_string; ?>" name="logo" id="logo" alt="logo" width="400px" height="200px">
		   <![endif]-->
		    
		   <!--[if !IE]>-->
			   <?php // convert image to dataURL
				 $img_source = "fotos/R&S2.png"; // image path/name
				 $img_binary = fread(fopen($img_source, "r"), filesize($img_source));
				 $img_string = base64_encode($img_binary);
			   ?>
			   <IMG SRC="data:image/gif;base64,<?php echo $img_string; ?>" name="logo" id="logo" alt="logo" width="400px" height="200px">		   
		   <!--<![endif]-->	   
		   
		</center>
	
		</body>
		</html>

		
<?php
	mysql_close($conexion);
	ob_end_flush();		
}
?>