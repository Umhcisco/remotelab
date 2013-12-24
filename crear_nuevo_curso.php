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

	//si los datos seleccionados son correctos, se procede a la creacion del nuevo curso
	if (isset($_GET['flag'])){
		$flag_devuelto = $_GET['flag'];
		//echo "<br>flag_devuelto = $flag_devuelto";
	
		$codigo_curso = $_GET['nombre_curso'];
		$num_max_pods = $_GET['num_max_pods'];
		
		$anio_inicio_curso = $_GET['inicio_anio'];
		$mes_inicio_curso = $_GET['inicio_mes'];
		$dia_inicio_curso = $_GET['inicio_dia'];
		
		$inicio_curso = "$anio_inicio_curso-$mes_inicio_curso-$dia_inicio_curso";
		
		$anio_fin_curso = $_GET['fin_anio'];
		$mes_fin_curso = $_GET['fin_mes'];
		$dia_fin_curso = $_GET['fin_dia'];
		
		$fin_curso = "$anio_fin_curso-$mes_fin_curso-$dia_fin_curso";
		
		//echo "<br>nombre_curso = $nombre_curso";
		//echo "<br>num_max_pods = $num_max_pods";
		//echo "<br>inicio_curso = $inicio_curso";
		//echo "<br>fin_curso = $fin_curso";
	
		//////////insertamos el curso seleccionado en la tabla cursos
		
		//buscamos el nombre del curso seleccionado
		if ($codigo_curso == 1)
			$nombre_curso = 'CCNA';
		else if ($codigo_curso == 2)
			$nombre_curso = "CCNA_SECURITY";
		else if ($codigo_curso == 3)
			$nombre_curso = "CCNP";

		/////buscamos la edicion anterior de dicho tipo de curso
		//$str_temp = substr($nombre_curso, -3);
		//$suffix = "eba";
		//$comp = strcmp($str_temp, $suffix);
		////echo "<br>comp = $comp";
		//if (strcmp($str_temp, $suffix) == 0)
		//	$nombre_curso = substr($nombre_curso, 0, strlen($nombre_curso)-7);
		//echo "<br>nombre_curso = $nombre_curso<br>";

		$sql1="SELECT * FROM cursos WHERE nombre_curso = '$nombre_curso' ORDER BY curso_id DESC LIMIT 1"; 
		$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el Select  (sql1) ".mysql_error()); 
		if ($reg1=mysql_fetch_array($registros1)) 
            $edicion = $reg1['edicion'] + 1;
		else
			$edicion = 1;
		//echo "<br>edicion = $edicion";		
		mysql_free_result($registros1);
		
		//calculamos el flag_pods_ok, suponiendo que todos los pods asignados estan disponibles
		$flag_pods_ok = pow(2,$num_max_pods)-1;
		//echo "<br>flag_pods_ok = $flag_pods_ok";
		
		//finalmente insertamos el nuevo curso en la tabla cursos
		$curso_activo = 1;  //por defecto, los cursos se crean como activos
        $sql2="INSERT INTO cursos(nombre_curso, num_max_pods, inicio_curso, fin_curso, edicion, curso_activo, dia_mant_semanal, hora_inicio_mant_semanal, duracion_mant_semanal, flag_pods_ok) VALUES ('$nombre_curso', $num_max_pods, '$inicio_curso', '$fin_curso', $edicion, $curso_activo, -1, -1, -1, $flag_pods_ok)"; 
        $registros2=mysql_query($sql2,$conexion) or die("Problemas en el Insert (sql2) ".mysql_error());		
		
		echo "<script> alert(\"Nuevo Curso creado\") </script>";
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
	 <title>CREACION NUEVO CURSO</title>
	 <!-- Estas lineas evitan que la pagina deseada no se cargue en la cache y no pueda ser mostrada cuando se regresa atras. -->
	  <meta http-equiv="Expires" CONTENT="0">
	  <meta http-equiv="Cache-Control" CONTENT="no-cache">
	  <meta http-equiv="Pragma" CONTENT="no-cache">
	 
	  <!-- Estas lineas son necesarias para sacar el calendario emergente -> da error, pero funciona -->	 
	  <link rel="stylesheet" type="text/css" href="src/css_calendario/jscal2.css" />
	  <link rel="stylesheet" type="text/css" href="src/css_calendario/border-radius.css" />
	  <link rel="stylesheet" type="text/css" href="src/css_calendario/steel/steel.css" />
	  <script type="text/javascript" src="src/js_calendario/jscal2.js"></script>
	  <script type="text/javascript" src="src/js_calendario/lang/es.js"></script>

	  <script type="text/javascript" language="javascript">
		  function Validacion(){
			  var hoy = "<?php echo $hoy ?>";
			  //alert("hoy = " + hoy);			  
			  var anio_hoy = hoy.substring(0,4);
			  var mes_hoy = hoy.substring(5,7);
			  var dia_hoy = hoy.substring(8,10);
			  //alert("a\u00f1o_hoy = " + anio_hoy + "\nmes_hoy = " + mes_hoy + "\ndia_hoy = " + dia_hoy);	
			  var fecha_hoy_sql = anio_hoy+"-"+mes_hoy+"-"+dia_hoy;
			  
			  var inicio_curso = document.getElementById('inicio_curso').value;
			  //alert("inicio_curso = " + inicio_curso);
			  var anio_inicio = inicio_curso.substring(0,4);
			  var mes_inicio = inicio_curso.substring(5,7);
			  var dia_inicio = inicio_curso.substring(8,10);
			  //alert("a\u00f1o_inicio = " + anio_inicio + "\nmes_inicio = " + mes_inicio + "\ndia_inicio = " + dia_inicio);
			  var fecha_inicio_sql = anio_inicio+"-"+mes_inicio+"-"+dia_inicio;
	  
			  var fecha_inicio = anio_inicio+"/"+mes_inicio+"/"+dia_inicio;
			  //alert("fecha inicio = " + fecha_inicio);
			  if (!isNaN(fecha_inicio)){
				 alert("\u00a1El campo Fecha Inicio Curso no puede estar vac\u00edo!");
				 return (false);
			  }
			  
			  var regexp = /^[0-9]{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])/;
			  var M1 = fecha_inicio_sql.match(regexp);
			  //var mesaj = M1?  alert("Formato de Fecha Inicio Correcto" ) : alert("Formato de Fecha Inicio err\u00f3neo!");
			  if (!M1){
			    alert("\u00a1La Fecha Inicio Curso no tiene el formato correcto.\n YYYY-MM-DD");
				return (false);
			  }
			  
			  var fin_curso = document.getElementById('fin_curso').value;
			  //alert("fin_curso = " + fin_curso);
			  var anio_fin = fin_curso.substring(0,4);
			  var mes_fin = fin_curso.substring(5,7);
			  var dia_fin = fin_curso.substring(8,10);
			  //alert("a\u00f1o_fin = " + anio_fin + "\nmes_fin = " + mes_fin + "\ndia_fin = " + dia_fin);
			  var fecha_fin_sql = anio_fin+"-"+mes_fin+"-"+dia_fin;
			  
			  fecha_fin = anio_fin+"/"+mes_fin+"/"+dia_fin;
			  //alert("fecha_fin = " + fecha_fin);
			  if (!isNaN(fecha_fin)){
				 alert("\u00a1El campo Fecha Fin Curso no puede estar vac\u00edo!");
				 return (false);
			  }

			  var regexp = /^[0-9]{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])/;
			  var M2 = fecha_fin_sql.match(regexp);
			  //var mesaj = M2?  alert("Formato de Fecha Fin Correcto" ) : alert("Formato de Fecha Fin err\u00f3neo!");
			  if (!M2){
			    alert("\u00a1La Fecha Fin Curso no tiene el formato correcto.\n YYYY-MM-DD");
				return (false);
			  }
				
			  document.getElementById('inicio_anio').value = anio_inicio;
			  document.getElementById('inicio_mes').value = mes_inicio;
			  document.getElementById('inicio_dia').value = dia_inicio;

			  document.getElementById('fin_anio').value = anio_fin;
			  document.getElementById('fin_mes').value = mes_fin;
			  document.getElementById('fin_dia').value = dia_fin;	

			  if (fecha_inicio_sql > fecha_fin_sql){
				 alert("\u00a1La fecha Inicio Curso no puede ser mayor que la fecha Fin Curso!");
				 document.getElementById('inicio_curso').value = "";
				 document.getElementById('fin_curso').value = "";
				 return (false);			  
			  }
			  
			  if (fecha_inicio_sql < fecha_hoy_sql){
				 alert("\u00a1La fecha Inicio Curso no puede ser anterior a la fecha de hoy!");
				 document.getElementById('inicio_curso').value = "";
				 document.getElementById('fin_curso').value = "";
				 return (false);  
			  }
			  
			  document.getElementById('flag').value = 1;
			  return (true);
		  }
	  </script>
	  
	</head>


	<body bgcolor="#<?php echo $bgcolor ?>"> 
	
	<center>
	  <u><h1>CREAR NUEVO CURSO</h1></u>   
	
		<table style="background: #ccffff" RULES=NONE FRAME=BOX>
		 <U><font style="width:300; background: #ccffff; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:14pt; font-weight:bold;  border: 1px solid #ccc;"> </U>
	   
		<form name="form_nuevo_curso" id="form_nuevo_curso" action="crear_nuevo_curso.php" method="GET" onSubmit="return Validacion();">
	   
			<tr>
				<!--    <td LABEL for="nombre_curso" ALIGN="right" style="font-size:14pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif">NOMBRE DEL CURSO: </LABEL> 
						<td> <INPUT TYPE="text" NAME="nombre_curso" id="nombre_curso" VALUE="" SIZE="20"> </td>	 	
				-->	   
					<td><LABEL for="nombre_curso" ALIGN="right" style="font-size:12pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif">NOMBRE DEL CURSO: &nbsp;&nbsp;</LABEL>
					<td> 
						<SELECT name="nombre_curso" id="nombre_curso" style="width:150px; align=center; font-size:11pt; font-weight:bold; background:#ffffff;"> 
						   <!-- <option id="encabezado" value="0">Elige opci&oacute;n</option> -->
						   <option id="ccna" value="1" >CCNA</option>
						   <option id="ccnp" value="2" >CCNA_SECURITY</option>
						   <option id="ccnasec" value="3" >CCNP</option>
						</SELECT>
					</td>
			</tr>
			
			<tr>
				<!--	<td LABEL for="num_max_pods" ALIGN="right" style="font-size:12pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif">NUMERO MAXIMO DE PODS: </LABEL> 
						<td> <INPUT TYPE="text" NAME="num_max_pods" id="num_max_pods" VALUE="" SIZE="20"> </td>
				-->	
					<td LABEL for="num_max_pods" ALIGN="right" style="font-size:12pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif">NUMERO MAXIMO DE PODS: &nbsp;</LABEL>
					<td> 
						<SELECT name="num_max_pods" id="num_max_pods" style="width:150px; align=center; font-size:11pt; font-weight:bold; background:#ffffff;"> 
						   <!-- <option id="encabezado" value="0">Elige opci&oacute;n</option> -->
						   <option id="pods_1" value="1" >1</option>
						   <option id="pods_2" value="2" >2</option>
						   <option id="pods_3" value="3" >3</option>
						   <!-- <option id="pods_4" value="4" >4</option> -->
						</SELECT>
					</td>
			</tr>
			
			<tr>
					<td LABEL for="inicio_curso" ALIGN="right" style="font-size:12pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif">FECHA INICIO CURSO: &nbsp;</LABEL>
					<td> 
						<input type="text" value=" " id="inicio_curso" size="13"/>
						<input type="button" value="..." id="boton_inicio_curso"/> 

						  <script type="text/javascript">
						  RANGE_CAL_1 = new Calendar({
								  inputField: "inicio_curso",
								  dateFormat: "%Y-%m-%d",
								  trigger: "boton_inicio_curso",
								  bottomBar: false,
								  onSelect: function() {
										  var date = Calendar.intToDate(this.selection.get());
										  RANGE_CAL_1.args.min = date;
										  RANGE_CAL_1.redraw();
										  this.hide();
								  }
						  });
						  </script>
						  
					</td>
			</tr>

			<tr>
					<td LABEL for="fin_curso" ALIGN="right" style="font-size:12pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif">FECHA FIN CURSO: &nbsp;</LABEL>
					<td> 
						<input type="text" value=" " id="fin_curso" size="13"/>
						<input type="button" value="..." id="boton_fin_curso"/> 
						
						  <script type="text/javascript">
						  RANGE_CAL_2 = new Calendar({
								  inputField: "fin_curso",
								  dateFormat: "%Y-%m-%d",
								  trigger: "boton_fin_curso",
								  bottomBar: false,
								  onSelect: function() {
										  var date = Calendar.intToDate(this.selection.get());
										  RANGE_CAL_2.args.min = date;
										  RANGE_CAL_2.redraw();
										  this.hide();
								  }
						  });
						  </script>
						  
					</td>
			</tr>
			
			<tr height="16px">
				<td style="font-size:14pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif">
				
					<input type="hidden" name="flag" id="flag" value="">
				</td>
				<td style="font-size:14pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif">
				
					<input type="hidden" name="inicio_anio" id="inicio_anio" value="">
					<input type="hidden" name="inicio_mes" id="inicio_mes" value="">
					<input type="hidden" name="inicio_dia" id="inicio_dia" value="">
					
					<input type="hidden" name="fin_anio" id="fin_anio" value="">
					<input type="hidden" name="fin_mes" id="fin_mes" value="">
					<input type="hidden" name="fin_dia" id="fin_dia" value="">
				</td>
			</tr>
			
			<tr>
				<td colspan="2" align="center">
					 <div align="center">
					   <INPUT type="submit" name="crear" value="CREAR" face="algerian" size="5" align="center" style="background-color : olive; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:120px;"/>
					 </div>
				</td>
			</tr>
		
		  </form>
		  
		  </font>
		</table>
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
	//mysql_free_result($registros1);
	//mysql_free_result($registros2);
	mysql_close($conexion);
	ob_end_flush();	

}
?>