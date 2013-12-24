<?php 
 include ("headers/seguridad.php");
 include ("headers/conexion.php");

//iniciamos el buffer de salida 
ob_start();

$admin = $_SESSION['admin'];
$idle_timeout = $_SESSION['idle_timeout'];
$hoy = date('Y-m-d');
$length_passw = 10;

function randomPassword($length_passw) {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = '';                           //password is a string
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < $length_passw; $i++) {
        $n = mt_rand(0, $alphaLength);    
        $pass = $pass.$alphabet[$n];      //append a random character
    }
    return ($pass); 
}
					

//Se comprueba si el usuario tiene privilegios de administrador
if ($_SESSION['admin'] == 0) { 
     session_destroy();
    //si no tiene privilegios de administrador, se devuelve a la pagina inicial 
    header("Location: index.php?errorusuario=ad"); 
} else { 

	//si los datos seleccionados son correctos, se procede a introduccion de los tokens
	if (isset($_GET['cursoID'])){
	
		$cursoID = $_GET['cursoID'];
		echo "<br>cursoID = $cursoID";
	
		//comprobamos la opcion de CURSO
		if ($cursoID == 0){
		   	//si no se ha seleccionado ningun curso 
			mysql_free_result($registros1);
			mysql_close($conexion);
			ob_end_flush();
			header("Location: introducir_tokens.php?errortokens=nc");
		}else{   
		    echo "<br>test";
			$area = $_GET['area_token'];
			$array_lineas = explode("\r\n", $area);
			$total_lineas = count($array_lineas);			
			echo "<br>total_lineas = ".count($array_lineas);
			$cont = 0;
								
			for ($k=0; $k<$total_lineas; $k++){
					$mystring = $array_lineas[$k];
					echo "<br><br>mystring = $mystring";
					$findme   = ' ';
					$pos = strpos($mystring, $findme);
					echo "<br>linea $k --> pos = $pos";
					if ($pos !== false) {
						$linea[$cont] = $array_lineas[$k];
						$cardinal = $cont + 1;
						echo "<br>LINEA $cardinal: $linea[$cont]";
						$cont++;
					}
			}
			
			if (isset($linea)){
				$num_lineas = count($linea);
			}else{
				$num_lineas = 0;
			}
			echo "<br><br>num_lineas = $num_lineas";
			
			
			if ($num_lineas == 0){

				echo "<br>El Texto Esta Vacio. \nNo se ha a&ntilde;adido ning&uacute;n token.";
				//si no se ha añadido ningun token 
				mysql_free_result($registros1);
				mysql_close($conexion);
				ob_end_flush();    echo "<br>*************";
				//header("Location: introducir_tokens.php?errortokens=nt");
			
			}else{			
			
				for ($k=0; $k<$num_lineas; $k++)
					echo "<br>linea[$k] = $linea[$k]";
			
				for ($i=0; $i<$num_lineas; $i++){    //$i=1 para evitar la primera linea
					$n = $i+1;
					echo "<br><br>LINEA $n";		
					$datos[$i] = explode(",", $linea[$i]);
					$long_datos[$i] = count($datos[$i])-1;
					//echo "<br>long_datos[$i] = $long_datos[$i]";	
					
					for ($j=0; $j<$long_datos[$i]; $j++){

						$temp[$j] = $datos[$i][$j];
						echo "<br>datos[$i][$j] = $temp[$j]";
					}

				}

				///////////////////////////////////////// MYSQL //////////////////////
			
				for ($k=1; $k<$num_lineas; $k++){
					//Primero se comprueba si ya existe otro usuario con el mismo nombre de usuario
					$sql2="SELECT * FROM usuarios"; 
					$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el Select (sql2) ".mysql_error());
					$count2 = mysql_num_rows($registros2);
					//echo "<br>Total usuarios = $count2<hr>";	
					echo "<br>k = $k";

					//$usuario = trim($datos[$k][2]);      //el email de cada alumno sera su login
					$usuario = trim(stripslashes($datos[$k][2]));
					echo "<br>usuario = $usuario";
					$flag_usuario_unico = 1;   //a priori no existe otro usuario igual				

					if ($count2 > 0){
						while ( ($reg2 = mysql_fetch_array($registros2)) && ($flag_usuario_unico==1) ) {
							$username =  $reg2['username'];   
							//echo "<br>username = $username";
							if (strcasecmp($username,$usuario)==0){
								$flag_usuario_unico = 0;
								$usuario_id = $reg2['user_id'];
							}
						}
					}else{
						echo "<br>En este momento no hay usuarios en el sistema.<br>";
						$flag_usuario_unico = -1;
					}	
										
					//echo "<br>flag_usuario_unico = $flag_usuario_unico";
					
					if($flag_usuario_unico == 0){
						echo "<br>Usuario ya existente.";
						
						//se debe comprobar si dicho usuario se quiere matricular en un nuevo curso o en uno en el que ya estaba matriculado
						$sql7="SELECT * FROM alumnos_en_cursos WHERE user_id = $usuario_id AND curso_id = $cursoID"; 
						$registros7=mysql_query($sql7,$conexion) or die ("Problemas con el Select (sql7) ".mysql_error());
						$count7 = mysql_num_rows($registros7);
						echo "<br>Usuario matriculado en este curso = ";
						if ($count7 == 0 ) echo "NO"; else echo "SI";
						
						if ($count7 > 0){  //ese alumno ya estaba matriculado en ese curso
							echo "<script> alert (\"Este usuario ya existe en el sistema.\"); </script>";
							
							mysql_free_result($registros2);
							mysql_free_result($registros7);
							
							mysql_close($conexion);
							ob_end_flush();
							//header("Location: introducir_tokens.php?errortokens=um");	
						}
					} 

					////////ya no se usan!!
					/////$contrasenia = trim($datos[$k][3]);   // --> contraseña: seria el token emitido por la plataforma para cada usuario nuevo
					/////$contrasenia = "xxx";             // -> provisional
					////////Finalemente se ha decidido implementar una funcion que genera passwords aleatorios para cada nuevo usuario
					//echo "<br>length_passw = $length_passw";
					$contrasenia = randomPassword($length_passw);
					echo "<br>contrasenia = $contrasenia";

					
					//$nombre = trim($datos[$k][0]);
					$nombre = trim(stripslashes($datos[$k][0]));
					//$apellidos = trim($datos[$k][1]);
					$apellidos = trim(stripslashes($datos[$k][1]));
					
					echo "<br>nombre = $nombre";
					echo "<br>apellidos = $apellidos";
					

					///////se procede a insertar los datos recogidos en las tablas de la Base de Datos correspondientes	
					
					if ($flag_usuario_unico != 0){
						
						$sql3="INSERT INTO usuarios(username, password, admin) VALUES ('$usuario', '$contrasenia', 0 )"; 
						$registros3=mysql_query($sql3,$conexion) or die("Problemas en el Insert (sql3) ".mysql_error());
					
						// seleccionamos el id para el nuevo usuario
						$sql4="SELECT * FROM usuarios WHERE username = '$usuario'"; 
						$registros4=mysql_query($sql4,$conexion) or die ("Problemas con el Select  (sql4) ".mysql_error()); 
						if ($reg4=mysql_fetch_array($registros4)) 
						  $usuario_id = $reg4['user_id'];
						echo "<br>usuario_id = $usuario_id";
						mysql_free_result($registros4);
										
						$sql6="INSERT INTO datos_pers(user_id, nombre, apellidos, email) VALUES ($usuario_id, '$nombre', '$apellidos', '$usuario')"; 
						$registros6=mysql_query($sql6,$conexion) or die("Problemas en el Insert (sql6) ".mysql_error());
					}					
					
					$sql5="INSERT INTO alumnos_en_cursos(curso_id, user_id, status) VALUES ($cursoID, $usuario_id, 0)"; 
					$registros5=mysql_query($sql5,$conexion) or die("Problemas en el Insert (sql5) ".mysql_error());
				
				}

				
				////////////////enviamos un email al usuario en el cual le confirmamos su usuario y nueva contraseña ////
				//$asunto = "Modificaci&oacute;n de Contrase&ntilde;a";
				//$destinatario = $usuario;
				//			
				//$cuerpo = '
				//<html>
				//<head>
				//<title>Modificaci&oacute;n de Contrase&ntilde;a</title>
				//</head>
				//<body>
				//<h1>Hola '.$nombre.'!</h1>
				//<p>
				//<i>Se ha procedido a tu incorporaci&oacute;n en el sistema.</i>
				//</p>
				//<p>
				//<i>Tus nuevos datos para acceder al sistema son los siguientes:</i>
				//</p>
				//<p>
				//<b>  USUARIO: '.$usuario.'</b>
				//</p>
				//<p>
				//<b>  CONTRASE&Ntilde;A: '.$contrasenia.'</b>
				//</p>
				//<p>
				//<i>Esperamos que disfrutes de tu estancia.</i>
				//</p>
				//Saludos cordiales,
				//</body>
				//</html>
				//';
				//			
				////Envío en formato HTML
				//$headers = "MIME-Version: 1.0\r\n";
				//$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
				//
				////Dirección del remitente
				//$headers .= "From: Administrador <".$nuestro_email.">\r\n";
				//
				////Dirección de respuesta (Puede ser una diferente a la de pepito@mydomain.com)
				//$headers .= "Reply-To: ".$nuestro_email."\r\n";
				//
				//$booleano = mail($destinatario,$asunto,$cuerpo,$headers);
				////echo "<br>booleano = $booleano";
				//if ($booleano)
				//  	echo "<br> Email enviado";
				//else
				//		echo "<br> Email fallido";
				//		
				//echo "<script>alert('Usuario registrado'); location.href='index.php';</SCRIPT>";				

				
				mysql_free_result($registros2);
			}
		}
		mysql_close($conexion);
		//al terminar el proceso le mando otra vez a la portada 
		//////////////////////////////////////////////////////////////////////header("Location: introducir_tokens.php?errortokens=no");		
		ob_end_flush();
	}	

	
	//consulta --> Listado de Cursos Activos o en ejecucion
	$sql1 = "SELECT * FROM cursos WHERE ( curso_activo = 1 OR fin_curso >= CURDATE() ) AND num_max_pods > 0";
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
	 <title>INTRODUCIR TOKENS</title>
	 <!-- Estas lineas evitan que la pagina deseada no se cargue en la cache y no pueda ser mostrada cuando se regresa atras. -->
	  <meta http-equiv="Expires" CONTENT="0">
	  <meta http-equiv="Cache-Control" CONTENT="no-cache">
	  <meta http-equiv="Pragma" CONTENT="no-cache">

	</head>


	<body bgcolor="#<?php echo $bgcolor ?>">
	
		<center><u><h1>INTRODUCIR TOKENS</h1></u>

		<table RULES=NONE FRAME=BOX style="background: #ccffff" border="0">
			<font style="width:100; background: #ffffff; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:14pt; font-weight:bold;  border: 1px solid #ccc;">
		
				<form name="cursoSelect" action="" method="GET">
				    <div align="center"> 
						<tr>
							<?php if (isset($_GET["errortokens"])) 
								  {
									 if ($_GET["errortokens"]=="nc") {
							?> 
				            <td colspan="2" align="center" bgcolor="lightblue"><span style="color:ffffff"><b>Curso no introducido</b></span> 

							<?php    }else if ($_GET["errortokens"]=="nt") { ?>
							
							<td colspan="2" align="center" bgcolor="blueslate"><span style="color:ffffff"><b>Tokens no introducidos</b></span>

							<?php    }else if ($_GET["errortokens"]=="um") { ?>
							
							<td colspan="2" align="center" bgcolor="thistle"><span style="color:ffffff"><b>Usuario ya matriculado</b></span>
							
							<?php    }else if ($_GET["errortokens"]=="no") { ?>
							
							<td colspan="2" align="center" bgcolor="thistle"><span style="color:ffffff"><b>Tokens Introducidos</b></span>

							<?php } } else {				?>			
							<td colspan="2" align="center" bgcolor=e0e0e0>Introduce curso y tokens </span>
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
								<font style="width:200; background: #ccffcc; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:14pt; font-weight:bold;  border: 1px solid #ccc;" readonly>No hay cursos activos en la actualidad </font>
								<script> alert("No hay cursos activos en la actualidad"); </script> </td>
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
							<td LABEL for="tokens" ALIGN="right" style="font-size:12pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif">TOKENS: &nbsp;&nbsp;</LABEL></td> 
							<td><TEXTAREA rows="12" cols="60" name="area_token" id="area_token" value="" style="align=left; font-size:11pt; background:#ffffff; font-family: Verdana, Arial, Helvetica, sans-serif" >
							
							    </TEXTAREA>
							</td>
						</tr> 	
						
						<tr>
							<td height="30"></td> 
							<td></td>
						</tr> 	
			
						<tr>
							 <td colspan="2" align="center">
								<INPUT type="submit" name="introducir" value="INTRODUCIR" face="algerian" size="5" align="center" style="background-color : olive; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:140px;" />
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
	//mysql_free_result($registros1);
	//mysql_free_result($registros2);
	mysql_close($conexion);
	ob_end_flush();	

}
?>