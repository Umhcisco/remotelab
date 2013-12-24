<?php
    include ("headers/conexion.php");
    include ("headers/crypto.php");
 
	//////si es necesario cambiar la config. del php.ini desde este script
	ini_set("session.use_only_cookies","1"); 
	ini_set("session.use_trans_sid","0"); 
	session_cache_limiter('nocache,private_no_expire');

	session_start();
	
	$msg = '';

	
    ////se comprueba si se han recibido datos del formulario
	if(isset($_POST['micaptcha']))
	{
		$micaptcha = $_POST['micaptcha'];
		$micaptcha = trim(stripslashes($micaptcha));
		echo "<script> alert('Mi captcha = ' + <?php echo \"$micaptcha\"; ?>'); </script>";
		
		if ($micaptcha !== ""){
			//echo "<br>micaptcha = $micaptcha";
			$captcha = $_SESSION['6_letters_code'];
			//echo "<br>&nbsp;&nbsp;captcha = $captcha";    
		}else{
			//liberamos recursos
			mysql_close($conexion);
				
			session_destroy();
			session_unset();	
				
			echo "<script> alert('El captcha est\u00e1 vac\u00edo. '); </script>";
				
			//se devuelve al inicio 
			header("Location: index.php?errorusuario=cv"); 		
		}
		
		if(strcmp($_SESSION['6_letters_code'], $_POST['6_letters_code']) != 0)
		{
			//Note: the captcha code is compared case sensitively.
			//if you want case sensitive match, update the check above to   
			//strcmp()
			//if you don't want case sensitive match, update the check above to 
			//strcasecmp()
			
			$msg= "\n El captcha NO coincide!";
			//echo "<br>msg = $msg";
			echo "<script> alert (\"El captcha NO coincide.\"); </script>";
			
			//liberamos recursos
			mysql_close($conexion);
				
			session_destroy();
			session_unset();	
				
			//se devuelve al inicio 
			header("Location: index.php?errorusuario=cp"); 	
		}
		else
		{
			$msg= "\n El captcha SI coincide!";
			//echo "<br>msg = $msg";
			
			//En caso de que el captcha sea correcto, se va a comprobar si el email esta registrado en el sistema			
			$email = $_POST['login_scrambled'];
			$email = trim(stripslashes($email));
			
			if ($email === ""){			
				//email vacio
				
				//liberamos recursos
				mysql_close($conexion);
					
				session_destroy();
				session_unset();	
					
				echo "<script> alert(\"El email est\u00e1 vac\u00edo. \"); </script>";
					
				//se devuelve al inicio 
				header("Location: index.php?errorusuario=ev"); 			
			
			}else{

				//////////se procede a insertar los datos recogidos en las tablas de la Base de Datos correspondientes	
				
				///reassembling login
				$len_micaptcha = strlen($micaptcha);
				$len_total = strlen($email);
				$mistring = substr($email,$len_micaptcha,$len_total-1);
				$tam = strlen($mistring);
				$findme = '*';
				$pos = strpos($mistring, $findme);
				if ($pos !== FALSE){
					$mistring[$pos] = '@';
				}	
				//echo "<br>mistring = $mistring  -- tam = $tam";
				$mycourseid=1;
				$array_tmp=$mistring;
				for ($i=0; $i<$tam; $i++){
					$index = truemod((-3*$i+$mycourseid),$tam);
					$swap = substr($mistring,$i,1);	
					$array_tmp[$index] = $swap;
					//substr($tmp,$index,1)
					//$tmp += $swap;
					//echo "<br>i = $i -- index = $index -- swap = $swap -- array_tmp = $array_tmp";
				}
				$check = substr($array_tmp,$tam-2,1);
				if ($check == '+'){
					$chunk = substr($array_tmp,0,$tam-2);
					$last = substr($array_tmp,$tam-1,1);
					$tmp = $chunk.$last;
					$tam = strlen($mistring);
					//echo "<br>chunk = $chunk -- last = $last";
				}else
					$tmp = $array_tmp;
				//echo "<br>tmp = $tmp";
				$email = '';
				$email = $tmp;
				//echo "<br>email = $email";				
				////////////////////////////////////////
				
				// seleccionamos el id para el usuario registrado
				$sql2="SELECT * FROM usuarios WHERE username = '$email'"; 
				$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el Select  (sql2) ".mysql_error()); 
				$count2 = mysql_num_rows($registros2);
				//echo "<br>count2 = $count2";
				
				if ($count2 == 0){
					echo "<script> alert(\"El email no existe en el sistema. \"); </script>";
					
					//liberamos recursos
					mysql_free_result($registros2);
					mysql_close($conexion);
					
					session_destroy();
					session_unset();	
					
					echo "<script> alert(\"El usuario requerido no pertenece al sistema \"); </script>";
					
					//se devuelve al inicio 
					header("Location: index.php?errorusuario=ui"); 	
					
				}else{			
					echo "<script> alert(\"El email existe en el sistema. \"); </script>";
					
					while ($reg2=mysql_fetch_array($registros2)){ 
						$usuario_id = $reg2['user_id'];
						$contrasenia = $reg2['password'];
					}

					$destinatario = $email;   
					
					////////////////enviamos un email al usuario en el cual le enviamos su usuario y contraseña ////
					$sql3 = "SELECT * FROM datos_pers WHERE user_id = $usuario_id";
					$registros3 = mysql_query($sql3,$conexion) or die ("Problemas con el Select  (sql3) ".mysql_error());
					$count3 = mysql_num_rows($registros3);
							
					if ($count3 > 0){
								
							while ($reg3 = mysql_fetch_array($registros3)){
								//$destinatario = $reg3['email'];		//es el nombre de usuario		 		
								$nombre = $reg3['nombre'];
							}
										
							$asunto = "Recuperación de Contraseña";
								
							$cuerpo = '
							<html>
							<head>
							<title>Recuperaci&oacute;n de Contrase&ntilde;a</title>
							</head>
							<body>
							<h3>Hola '.$nombre.'!</h3>
							<p>
							<i>Tus datos para acceder al sistema son los siguientes:</i>
							</p>
							<p>
							<b>  USUARIO: '.$destinatario.'</b>
							</p>
							<p>
							<b>  CONTRASE&Ntilde;A: '.$contrasenia.'</b>
							</p>
							<p>
							<i>Esperamos que disfrutes de la plataforma.</i>
							</p>
							Saludos cordiales,
							<p>________________<p>
							<b>RemoteLab.UMH</b>
							</p>
							</body>
							</html>
							';
								
							//Envío en formato HTML
							$headers = "MIME-Version: 1.0\r\n";
							$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
					
							//Dirección del remitente
							$headers .= "From: Administrador < ".$nuestro_email.">\r\n";
					
							//Dirección de respuesta (Puede ser una diferente al remitente)
							$headers .= "Reply-To: ".$nuestro_email."\r\n";
					
							$booleano = mail($destinatario,$asunto,$cuerpo,$headers);
					
							echo "<br>booleano = $booleano";
							if ($booleano)
								echo "<br> Email enviado";
							else
								echo "<br> Email fallido";
						
							echo "<script>alert('Contrase\u00f1a enviada por email'); location.href='index.php';</SCRIPT>";			
					}
					///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
					
					//liberamos recursos
					mysql_free_result($registros2);
					mysql_free_result($registros3);
					mysql_close($conexion);
					
					session_destroy();
					session_unset();	
					
					echo "<script> alert(\"La contrase\u00f1a ha sido enviada al email citado. \"); </script>";
					
					//se devuelve al inicio 
					header("Location: index.php?errorusuario=pr");
				}
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
<meta http-equiv="Content-Type" con tent="text/html; charset=iso-8859-1" />
<title>RECUPERACI&Oacute;N DE CONTRASE&Ntilde;AS</title>	
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
    especiales = [8, 9, 13, 46, 45, 95];

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
			window.setTimeout(function() { document.form_recuperacion.email.focus(); },0);
		}
	}
}

function comprueba_email() {
	var cadena1 = document.getElementById("email").value;
	var cadena2 = document.getElementById("email2").value;
	var tam = cadena2.length;
	
	if (cadena1 != cadena2 && tam > 0){
		//alert("email 1 = " + cadena1 + "\nemail 2 = " + cadena2 + "\n\n\u00a1No coinciden!");
		alert("\u00a1Los emails no coinciden!");
		document.getElementById("email2").value = '';
		window.setTimeout(function() { document.form_recuperacion.email2.focus(); },0);
	}
}

function soloCaracteresHabituales(e) {
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    letras = " áéíóúabcdefghijklmnñopqrstuvwxyz0123456789";
    especiales = [8, 9, 13, 37, 39, 46, 45, 95];

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

function retornar(){
   	document.forms["form_retornar"].submit();
	alert("De regreso a la p\u00e1gina inicial");
}

function BorrarTodo(){
	document.getElementById("email").value = '';
	document.getElementById("email2").value = '';
	document.getElementById("6_letters_code").value = '';
}

function Validacion(){
	var flag = 1;
	if (document.getElementById("6_letters_code").value == ''){
		BorrarTodo();
		alert("Falta el captcha");
		flag = 0;
	} 
	else{
		micaptcha = document.getElementById("6_letters_code").value;
		//alert("micaptcha = " + micaptcha);
		document.getElementById("micaptcha").value = micaptcha;
	}
	if ( (document.getElementById("email").value == '') && (flag==1) ){
		BorrarTodo();
		alert("Falta el email");
		flag = 0;
	}
	if ( (document.getElementById("email2").value == '') && (flag==1) ){
		BorrarTodo();
		alert("Falta la confirmaci\u00f3n del email");
		flag = 0;
	}
	
	if (flag==1){	
		var temp = document.getElementById("email").value;
		var user = reshuffle(temp,micaptcha);	
		document.getElementById("login_scrambled").value = user;
		//alert("user = " + user);
		 
		var stars = "";
		for(i=0;i<temp.length;i++)
			stars = stars+"*";
		document.getElementById("email").value = stars;	
		document.getElementById("email2").value = stars;			
	}
	
	if (flag==1){
		return true;
	}else{
		return false;
	}
}


</script>

<!-- a helper script for validating the form -->
<script language="JavaScript" src="src/gen_validatorv31.js" type="text/javascript"></script>	

</head>

<body bgcolor="#<?php echo $bgcolor ?>">
	<center>
	<h1><u>RECUPERACI&Oacute;N DE CONTRASE&Ntilde;AS</u></h1>

	<table style="background: #<?php echo $bgcolor2 ?>" RULES=NONE FRAME=BOX>
	   <center><U><font style="width:320; background: #<?php echo $bgcolor ?>; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:14pt; font-weight:bold;  border: 1px solid #ccc;"> 
	   
	   <form name="form_recuperacion" id="form_recuperacion" action="" method="POST" onSubmit="return Validacion()">
  
		  <tr height="10px">
		    <td style="font-size:14pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif"></td>
			<td style="font-size:14pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif"></td>
		  </tr>
		  
		  <tr>
		    <td LABEL for="email" ALIGN="right" style="font-size:14pt">EMAIL: </LABEL> 
		    <td> <INPUT TYPE="text" onblur="limpia_email()" NAME="email" id="email" VALUE="" SIZE="40"> &nbsp; </td>	 	
					<script type="text/javascript">
				       var txtBox=document.getElementById("email");
				       if (txtBox!=null ) txtBox.focus();
			        </script>
		  </tr>	

		  <tr>
		    <td LABEL for="email2" ALIGN="right" style="font-size:14pt"> &nbsp;&nbsp; REPITE EMAIL: </LABEL> 
		    <td> <INPUT TYPE="text" onblur="comprueba_email()" NAME="email2" id="email2" VALUE="" SIZE="40"> &nbsp; </td>	 	
		  </tr>	
		  
		  <tr height="10px">
		    <td> <input type="hidden" name="flag" id="flag" value=""> </td>
			<td style="font-size:14pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif"></td>
		  </tr>

		  
          <tr>  
		    <td colspan="2" align="center">
			 
				<p>
				<img src="src/captcha_code_file.php?rand=<?php echo rand(); ?>" id='captchaimg' ><br>
				<label for='message'>Introduce el c&oacute;digo :</label><br>
				<input type="text" onkeypress="return soloCaracteresHabituales(event)" name="6_letters_code" id="6_letters_code" value=""><br>
				<small>&iquest;No puedes leer la imagen? Click <a href='javascript: refreshCaptcha();'>aqu&iacute;</a> para refrescar</small>
				</p>

				 <script language='JavaScript' type='text/javascript'>
				function refreshCaptcha()
				{
					var img = document.images['captchaimg'];
					img.src = img.src.substring(0,img.src.lastIndexOf("?"))+"?rand="+Math.random()*1000;
				}
				</script>

			</td>
		  </tr>
		  
		  <tr>
			<td>
				<input type="hidden" name="micaptcha" id="micaptcha" value="">
				<input type="hidden" name="login_scrambled" id="login_scrambled" value="">
			</td>
			<td>
				<INPUT TYPE="submit" NAME="alta" VALUE="ALTA" face="algerian" size="5" align="center" style="background-color : grey; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 10pt; text-align : center; font-weight: bold; width:80px;"align="right"> &nbsp;&nbsp;&nbsp;
				<INPUT TYPE="reset" NAME="borrar" VALUE="BORRAR" face="algerian" size="5" align="center" style="background-color : grey; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 9pt; text-align : center; font-weight: bold; width:75px;"> &nbsp;&nbsp;&nbsp;
				<INPUT type="button" name="volver" id="volver_in" value="VOLVER" face="algerian" size="5" align="center" style="background-color : grey; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 9pt; text-align : center; font-weight: bold; width:75px;" onClick="retornar();" /> 
			</td>
		  </tr>		

		  <tr height="10px">
		    <td style="font-size:14pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif"></td>
			<td style="font-size:14pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif"></td>
		  </tr>
		  
	   </form>	 
	   
	   
	   </font></U></center>  
	 </table>
	 </center> 
	 

	 <form name="form_retornar" id="form_retornar" action="index.php" method="POST">
         <input type="hidden" name="retornar_id" id="retornar_id" value="1">
	 </form>

	 
	<form name="form_volver" action="index.php" method="POST">
      <div align="left">
	    <INPUT type="submit" name="volver" id="volver_izq" value="VOLVER" face="algerian" size="5" style="background-color : #555555; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:120px;" />
	  </div>	
	  
	  <div align="right">
	    <INPUT type="submit" name="volver" id="volver_dch" value="VOLVER" face="algerian" size="5" style="background-color : #555555; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:120px;" />
	  </div>	
	</form>
	
<?php
  //$codigo = $_SESSION['6_letters_code'];
  //echo "<br>CAPTCHA: $codigo";
  //var_dump($_SESSION);
  //echo "<br>errors = $errors"; 
?>	 

<!-- 
<script>
	//var errors = "<?php echo $errors ?>";
	//document.write("<br>errors = " + errors);
</script>
-->

<?php

	//liberamos recursos
	mysql_close($conexion);
	
	session_destroy();
	session_unset();
?>

<center>
  <IMG SRC="fotos/password_send.jpg" name="logo" id="logo" alt="logo" width="420px" height="190px" onContextMenu="return(false)">
</center>

</body>
</html>
