<?php
 include ("headers/seguridad.php");
 include ("headers/conexion.php");

 function truemod($num, $mod) {
   return ($mod + ($num % $mod)) % $mod;
 }

//iniciamos el buffer de salida 
//ob_start();


$id_curso = $_SESSION['id_curso'];	
$id_usuario = $_SESSION['id_usuario'];
$usuario = $_SESSION['usuario'];
$idle_timeout = $_SESSION['idle_timeout'];

////se comprueba si se han recibido datos del formulario
if(isset($_POST['flag']))
{
	$tmp0 = $_POST['contrasenia'];
	$tmp0 = trim(stripslashes($tmp0));
	$tmp1 = substr($tmp0,18);
	$tam=strlen($tmp1);
	$tmp2 = strrev($tmp1);
	$temp = '';
	for ($i=0; $i<$tam; $i++){
		$index = truemod(($i-$id_curso-1),$tam);
		$swap = $tmp2[$index];
		$temp[$i] = $swap;
		//echo "<br>tmp2[$i] = $tmp2[$i] ** index = $index ** swap = $swap ** $temp[$i] = $temp[$i]";
	}
	$str_temp = implode('',$temp);	
	$contrasenia_nueva = $str_temp;	
	//echo "<br><br>tmp1  = $tmp1";
	//echo "<br>tmp2 = $tmp2";
	//echo "<br>str_temp = $str_temp";
	
	////$contrasenia_nueva = $_POST['contrasenia'];
	////$contrasenia_nueva = trim(stripslashes($contrasenia_nueva));
	
	$contrasenia_anterior = $_POST['contrasenia0'];
	$contrasenia_anterior = trim(stripslashes($contrasenia_anterior));
	
	
	// seleccionamos el id para el usuario registrado, y de paso comprobamos si los datos introducidos son correctos
	$sql2="SELECT * FROM usuarios WHERE username = '$usuario' AND password = '$contrasenia_anterior'"; 
	$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el Select  (sql2) ".mysql_error()); 
	$count2 = mysql_num_rows($registros2);
	echo "<br>count2 = $count2";
			
	if ($count2 == 0){      //la contraseña actual introducida no es correcta
		echo "<script> alert(\"La contrase\u00f1a actual introducida no es la tuya. \"); </script>";		

		//liberamos recursos
		mysql_free_result($registros2);
		mysql_close($conexion);	
		
		//se devuelve a la pagina de reservas
		//header("Location: reservas.php?errorreserva=ui"); 

	}else{			      //los datos introducidos son correctos
		//echo "<script> alert(\"La contrase\u00f1a actual introducida si es la tuya. \"); </script>";
		
		while ($reg2=mysql_fetch_array($registros2)) 
			$usuario_id = $reg2['user_id'];
		
		$destinatario = $usuario;
			

		///////se procede a insertar los datos recogidos en las tablas de la Base de Datos correspondientes			
		$sql4 = "UPDATE usuarios SET password='$contrasenia_nueva' WHERE user_id = $usuario_id";
		$registros4 = mysql_query($sql4,$conexion) or die ("Problemas con el Update  (sql4) ".mysql_error()); 

		
		////////////////enviamos un email al usuario en el cual le confirmamos su usuario y nueva contraseña ////
		$sql3 = "SELECT * FROM datos_pers WHERE user_id = $usuario_id";
		$registros3 = mysql_query($sql3,$conexion) or die ("Problemas con el Select  (sql3) ".mysql_error());
		$count3 = mysql_num_rows($registros3);
				
		if ($count3 > 0){
					
				while ($reg3 = mysql_fetch_array($registros3)){
					//$destinatario = $reg3['email'];		//es el nombre de usuario  				
					$nombre = $reg3['nombre'];
				}			
				
				$asunto = "Modificación de Contraseña";
					
				$cuerpo = '
				<html>
				<head>
				<title>Modificaci&oacute;n de Contrase&ntilde;a</title>
				</head>
				<body>
				<h3>Hola '.$nombre.'!</h3>
				<p>
				<i>Se ha procedido al cambio de tu contrase&ntilde;a.</i>
				</p>
				<p>
				<i>Tus nuevos datos para acceder al sistema son los siguientes:</i>
				</p>
				<p>
				<b>  USUARIO: '.$usuario.'</b>
				</p>
				<p>
				<b>  CONTRASE&Ntilde;A: '.$contrasenia_nueva.'</b>
				</p>
				<p>
				<i>Esperamos que disfrutes de la plataforma.</i>
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
				
				echo "<script>alert('Contrase\u00f1a modificada'); location.href='index.php';</SCRIPT>";
		}
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	
		//liberamos recursos
		mysql_free_result($registros2);
		mysql_free_result($registros3);
		mysql_close($conexion);
			
		echo "<script> alert(\" La contrase\u00f1a ha sido modificada correctamente. \"); </script>";
			
		//se devuelve a la pagina de reservas
		header("Location: reservas.php?errorreserva=pm"); 
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
<meta http-equiv="Content-Type" con tent="text/html; charset=iso-8859-1" />
<title>MODIFICACI&Oacute;N DE CONTRASE&Ntilde;A</title>	
 <!-- Estas lineas evitan que la pagina deseada no se cargue en la cache y no pueda ser mostrada cuando se regresa atras. -->
  <meta http-equiv="Expires" CONTENT="0">
  <meta http-equiv="Cache-Control" CONTENT="no-cache">
  <meta http-equiv="Pragma" CONTENT="no-cache">
  
  
<script language='JavaScript' type='text/javascript'>
function soloLetras(e) {
    key = e.keyCode || e.which;
	//tecla = String.fromCharCode(key).toLowerCase();
    //letras = " áéíóúabcdefghijklmnñopqrstuvwxyz";
	tecla = String.fromCharCode(key);
	letras = " áéíóúabcdefghijklmnñopqrstuvwxyzÁÉÍÓÚABCDEFGHIJKLMNÑOPQRSTUVWXYZ-çÇ";
    especiales = [8, 37, 39, 46];

    tecla_especial = false
    for(var i in especiales) {
        if(key == especiales[i]) {
            tecla_especial = true;
            break;
        }
    }

    if(letras.indexOf(tecla) == -1 && !tecla_especial)
        return false;
}


function soloNumeros(e) {
	key = e.keyCode || e.which;
	tecla = String.fromCharCode(key).toLowerCase();
	letras = "0123456789";
	especiales = [8, 37, 39, 46];

	tecla_especial = false
	for(var i in especiales) {
		if(key == especiales[i]) {
			tecla_especial = true;
			break;
		}
	}

	if(letras.indexOf(tecla) == -1 && !tecla_especial)
		return false;
}



function validarEmail(email) {     
	//if (/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.([a-zA-Z]{2,4})+$/.test(email)){ 
	if (/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(email)){	
		return (true)    
	} else {         
		return (false);     
	} 
} 

function limpia_email() {
    var val = document.getElementById("email").value;
	var tam = val.length;
	//alert("val = " + val +"\ntam = " + tam);
	if (tam > 0){
		var status = validarEmail(val);
		//alert("status = " + status);
		if (!status && tam > 0){
			alert("El email no tiene el formato adecuado");
			document.getElementById("email").value = '';
			window.setTimeout(function() { document.form_modificacion.email.focus(); },0);
		}
	}
}

function comprueba_email() {
	var cadena1 = document.getElementById("email").value;
	var cadena2 = document.getElementById("email2").value;
	var tam = cadena2.length;
	
	if (cadena1 != cadena2 && tam > 0){
		alert("email 1 = " + cadena1 + "\nemail 2 = " + cadena2 + "\n\n\u00a1No coinciden!");
		document.getElementById("email2").value = '';
		window.setTimeout(function() { document.form_modificacion.email2.focus(); },0);
	}
}

//http://programacion.jias.es/2012/03/validar-direccion-de-correo-con-expresiones-regulares-en-javascript/	
//http://niednicifgenerador.appspot.com/ --> genera dni y nie de prueba     http://letradni.appspot.com/info.html



function soloCaracteresHabituales(e) {
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    letras = " áéíóúabcdefghijklmnñopqrstuvwxyzç0123456789";
    especiales = [8, 9, 37, 39, 46, 45, 95];

    tecla_especial = false
    for(var i in especiales) {
        if(key == especiales[i]) {
            tecla_especial = true;
            break;
        }
    }

    if(letras.indexOf(tecla) == -1 && !tecla_especial)
        return false;
}

function soloCaracteresConvencionales(e) {
    key = e.keyCode || e.which;
	//tecla = String.fromCharCode(key).toLowerCase();
    //letras = " abcdefghijklmnopqrstuvwxyz0123456789";
	tecla = String.fromCharCode(key);
	letras = " abcdefghijklmnñopqrstuvwxyzABCDEFGHIJKLMNÑOPQRSTUVWXYZçÇ0123456789";

    especiales = [8, 9, 37, 39, 46, 45, 95, 35, 36, 38, 42, 43, 47, 63, 64, 92, 124, 126, 164];  //, 35, 36, 38, 40, 41, 42, 43, 44, 47, 58, 59, 60, 61, 62, 63, 64, 91, 92, 93, 123, 124, 125, 126];

    tecla_especial = false
    for(var i in especiales) {
        if(key == especiales[i]) {
            tecla_especial = true;
            break;
        }
    }

    if(letras.indexOf(tecla) == -1 && !tecla_especial)
        return false;
}


function longitud_contrasenia0(){
	var longitud_minima_contrasenia = 10;
	
    var val = document.getElementById("contrasenia0").value;
	var tam = val.length;
	
	var flag_pass=1;
	if (tam < longitud_minima_contrasenia && tam > 0)
		flag_pass = 0;
	
	if (flag_pass == 0){
		alert("Contrase\u00f1a no v\u00e1lida.\n\u00a1Debe tener una longitud m\u00ednima de " + longitud_minima_contrasenia + "caracteres!");
		document.getElementById("contrasenia0").value = '';
		window.setTimeout(function() { document.form_modificacion.contrasenia0.focus(); },0);
	}else if ( tam > 0 ){
		/////se comprueba si la contraseña contiene los siguientes tipos de caracteres
		var flag_numeros=0;
		var flag_mayusculas=0;
		var flag_minusculas=0;
		var flag_especiales=0;
		var flag_contrasenia = 0;
		var especiales = [37, 39, 46, 45, 95, 35, 36, 38, 42, 43, 47, 63, 64, 92, 124, 126];
		
		for(i=0; i<tam; i++) {
			codigo_ascii = val.charCodeAt(i);   //convierte los caracteres a codigo ascii
			if( (codigo_ascii > 47) && (codigo_ascii < 58) ){
				flag_numeros = 1;
			}
			else if( ( (codigo_ascii > 64) && (codigo_ascii < 91) ) || codigo_ascii==165 ) {
				flag_mayusculas = 1;
			}
			else if( ( (codigo_ascii > 96 && codigo_ascii < 123) ) || codigo_ascii==164 )  {
				flag_minusculas = 1;
			}
			else{
				for(j=0; j<especiales.length; j++)
					if(codigo_ascii==especiales[j])
					   flag_especiales = 1;
				}
		}
		var flag_contrasenia = flag_numeros * flag_mayusculas * flag_minusculas;    ///se exige que la contraseña lleve numeros, mayusculas y minusculas
		//	var flag_contrasenia = flag_numeros * flag_mayusculas * flag_minusculas * flag_especiales;    ///se exige que la contraseña lleve numeros, mayusculas, minusculas y caracteres especiales autorizados
		
		//alert("flag_numeros = " + flag_numeros + "\nflag_mayusculas = " + flag_mayusculas + "\nflag_minusculas = " + flag_minusculas + "\n\nflag_contrasenia = " + flag_contrasenia);
		
		if (flag_contrasenia == 0) {
			document.getElementById("contrasenia0").value = '';
			alert("Contrase\u00f1a no v\u00e1lida.\nDebe contener n\u00fameros, may\u00fasculas y min\u00fasculas!");
			window.setTimeout(function() { document.form_modificacion.contrasenia0.focus(); },0);
		}
	}
}


function longitud_contrasenia(){
	var longitud_minima_contrasenia = 10;
	
    var val = document.getElementById("contrasenia").value;
	var tam = val.length;
	
	var flag_pass=1;
	if (tam < longitud_minima_contrasenia && tam > 0)
		flag_pass = 0;
	
	if (flag_pass == 0){
		alert("Contrase\u00f1a no v\u00e1lida.\n\u00a1Debe tener una longitud m\u00ednima de " + longitud_minima_contrasenia + "caracteres!");
		document.getElementById("contrasenia").value = '';
		window.setTimeout(function() { document.form_modificacion.contrasenia.focus(); },0);
	}else if ( tam > 0 ){
		/////se comprueba si la contraseña contiene los siguientes tipos de caracteres
		var flag_numeros=0;
		var flag_mayusculas=0;
		var flag_minusculas=0;
		var flag_especiales=0;
		var flag_contrasenia = 0;
		var especiales = [37, 39, 46, 45, 95, 35, 36, 38, 42, 43, 47, 63, 64, 92, 124, 126];
		
		for(i=0; i<tam; i++) {
			codigo_ascii = val.charCodeAt(i);   //convierte los caracteres a codigo ascii
			if( (codigo_ascii > 47) && (codigo_ascii < 58) ){
				flag_numeros = 1;
			}
			else if( ( (codigo_ascii > 64) && (codigo_ascii < 91) ) || codigo_ascii==165 ) {
				flag_mayusculas = 1;
			}
			else if( ( (codigo_ascii > 96 && codigo_ascii < 123) ) || codigo_ascii==164 )  {
				flag_minusculas = 1;
			}
			else{
				for(j=0; j<especiales.length; j++)
					if(codigo_ascii==especiales[j])
					   flag_especiales = 1;
				}
		}
		var flag_contrasenia = flag_numeros * flag_mayusculas * flag_minusculas;    ///se exige que la contraseña lleve numeros, mayusculas y minusculas
		//	var flag_contrasenia = flag_numeros * flag_mayusculas * flag_minusculas * flag_especiales;    ///se exige que la contraseña lleve numeros, mayusculas, minusculas y caracteres especiales autorizados
		
		//alert("flag_numeros = " + flag_numeros + "\nflag_mayusculas = " + flag_mayusculas + "\nflag_minusculas = " + flag_minusculas + "\n\nflag_contrasenia = " + flag_contrasenia);
		
		if (flag_contrasenia == 0) {
			document.getElementById("contrasenia").value = '';
			document.getElementById("contrasenia2").value = '';
			alert("Contrase\u00f1a no v\u00e1lida.\nDebe contener n\u00fameros, may\u00fasculas y min\u00fasculas!");
			window.setTimeout(function() { document.form_modificacion.contrasenia.focus(); },0);
		}
	}
}

function comprueba_contrasenia() {
	var cadena1 = document.getElementById("contrasenia").value;
	var cadena2 = document.getElementById("contrasenia2").value;
	var tam = cadena2.length;
	if (cadena1 != cadena2 && tam > 0) {
		//alert(" contrase\u00f1a 1 = " + cadena1 + "\n contrase\u00f1a 2 = " + cadena2 + "\n\n \u00a1No coinciden!");
		alert("\u00a1Las contrase\u00f1as NO coinciden!");
		document.getElementById("contrasenia2").value = '';
		window.setTimeout(function() { document.form_modificacion.contrasenia2.focus(); },0);
	}
}


function retornar(){
   	document.forms["form_retornar"].submit();
	alert("De regreso a la p\u00e1gina de reservas");
}

function BorrarTodo(){
	document.getElementById("contrasenia0").value = '';	
	document.getElementById("contrasenia").value = '';
	document.getElementById("contrasenia2").value = '';
}

function Validacion(){
	var control = 1;
	if (document.getElementById("contrasenia0").value == ''){
		BorrarTodo();
		alert("Falta la contrase\u00f1a anterior");
		control = 0;
	}
	if ( (document.getElementById("contrasenia").value == '') && (control==1) ){
		BorrarTodo();
		alert("Falta la contrase\u00f1a nueva");
		control = 0;
	}
	if ( (document.getElementById("contrasenia2").value == '') && (control==1) ){
		BorrarTodo();
		alert("Falta la confirmaci\u00f3n de la contrase\u00f1a nueva");
		control = 0;
	}

	if (control==1){
		var temp = document.getElementById("contrasenia").value;
		var code = "<?php echo $id_curso ?>";
		var tam = temp.length;
		var tmp = '';
		for (i=0; i<tam; i++){
		    index = (i+code)%tam;
			swap = temp.charAt(index); //swap = temp[index];
			tmp += swap;
			//alert("temp["+i+"] = " + temp[i] + "\nindex = " + index + "\nswap = " + swap + "\ntmp["+i+"] = " + tmp[i]);
		}
		//alert("temp = " + temp + "\ntmp = " + tmp);
		var prefix = '0xD51800BCF2A0FE4A';
		var mix = prefix + tmp;
		document.getElementById("contrasenia").value = mix;
		document.getElementById("flag").value = 1;
		return (true)
	}else{
		return (false);
	}
}



</script>

<!-- a helper script for validating the form -->
<script language="JavaScript" src="src/gen_validatorv31.js" type="text/javascript"></script>	

</head>

<body bgcolor="#<?php echo $bgcolor ?>">
	<center>
	<u><h1>MODIFICACI&Oacute;N DE CONTRASE&Ntilde;A</h1></u>
	
	<table style="background: #<?php echo $bgcolor2 ?>" RULES=NONE FRAME=BOX>
	   <center><U><font style="width:300; background: #<?php echo $bgcolor ?>" font-family: Verdana, Arial, Helvetica, sans-serif; font-size:14pt; font-weight:bold;  border: 1px solid #ccc;"> 
	   
	   <form name="form_modificacion" id="form_modificacion" action="" method="POST" onSubmit="return Validacion();">
  
		  <tr height="10px">
		    <td style="font-size:14pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif"></td>
			<td style="font-size:14pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif"></td>
		  </tr>
		  
		  <tr>
		    <td LABEL for="contrasenia0" ALIGN="right" style="font-size:14pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif"> CONTRASE&Ntilde;A ACTUAL: </LABEL> 
		    <td> <INPUT TYPE="password" onkeypress="return soloCaracteresConvencionales(event)" NAME="contrasenia0" id="contrasenia0" VALUE="" SIZE="40"> </td>	 
				    <script type="text/javascript">
				       var txtBox=document.getElementById("contrasenia0");
				       if (txtBox!=null ) txtBox.focus();
			        </script>			
		  </tr>			  

		  <tr height="15px">
		    <td style="font-size:14pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif"></td>
			<td style="font-size:14pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif"></td>
		  </tr>
		  
		  <tr>
		    <td LABEL for="contrasenia" ALIGN="right" style="font-size:14pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif">CONTRASE&Ntilde;A NUEVA: </LABEL> 
		    <td> <INPUT TYPE="password" onkeypress="return soloCaracteresConvencionales(event)" onblur="longitud_contrasenia()" NAME="contrasenia" id="contrasenia" VALUE="" SIZE="40"> </td>	 	
		  </tr>	
		  
		  <tr>
		    <td LABEL for="contrasenia2" ALIGN="right" style="font-size:14pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif">REPITE CONTRASE&Ntilde;A: </LABEL> 
		    <td> <INPUT TYPE="password" onblur="comprueba_contrasenia()" NAME="contrasenia2" id="contrasenia2" VALUE="" SIZE="40"> </td>	 	
		  </tr>			  

		  <tr height="16px">
		    <td style="font-size:14pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif"></td>
			<td style="font-size:14pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif"></td>
		  </tr>

		  
		  <tr>
			<td>
				<input type="hidden" name="flag" id="flag" value="">
			</td>
			<td>
				<INPUT TYPE="submit" NAME="alta" VALUE="ALTA" face="algerian" size="5" align="center" style="background-color : grey; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 10pt; text-align : center; font-weight: bold; width:80px;"align="right"> &nbsp;&nbsp;&nbsp;
				<INPUT TYPE="reset" NAME="borrar" VALUE="BORRAR" face="algerian" size="5" align="center" style="background-color : grey; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 9pt; text-align : center; font-weight: bold; width:75px;"> &nbsp;&nbsp;&nbsp;
				<INPUT type="button" name="volver" id="volver_in" value="VOLVER" face="algerian" size="5" align="center" style="background-color : grey; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 9pt; text-align : center; font-weight: bold; width:75px;" onClick="retornar();" /> 
			</td>
		  </tr>		  
	   </form>	 
	   
	   
	   </font></U></center>  
	 </table>
	 </center> 
	 

	 <form name="form_retornar" id="form_retornar" action="reservas.php" method="POST">
         <input type="hidden" name="retornar_id" id="retornar_id" value="1">
	 </form>

	 
	<form name="form_volver" action="reservas.php" method="POST">
      <div align="left">
	    <INPUT type="submit" name="volver" id="volver_izq" value="VOLVER" face="algerian" size="5" style="background-color : #555555; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:120px;" />
	  </div>	
	  
	  <div align="right">
	    <INPUT type="submit" name="volver" id="volver_dch" value="VOLVER" face="algerian" size="5" style="background-color : #555555; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:120px;" />
	  </div>	
	</form>
	

<center>
  <IMG SRC="fotos/password_change.jpg" name="logo" id="logo" alt="logo" width="200px" height="200px" onContextMenu="return(false)">
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <IMG SRC="fotos/password_send.jpg" name="logo" id="logo" alt="logo" width="260px" height="200px" onContextMenu="return(false)">
</center>

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
//ob_end_flush();		
?>
