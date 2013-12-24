<?php
 include ("headers/conexion.php");
 include ("headers/crypto.php");

 
  	//////si es necesario cambiar la config. del php.ini desde este script
	ini_set("session.use_only_cookies","1"); 
	ini_set("session.use_trans_sid","0"); 
	session_cache_limiter('nocache,private_no_expire');

	session_start();
	
	$msg = '';
	
	
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
<title>LAB REMOTO</title>	
 <!-- Estas lineas evitan que la pagina deseada no se cargue en la cache y no pueda ser mostrada cuando se regresa atras. -->
  <meta http-equiv="Expires" CONTENT="0">
  <meta http-equiv="Cache-Control" CONTENT="no-cache">
  <meta http-equiv="Pragma" CONTENT="no-cache">
  
<script src="src/md5.pack.js" type="text/javascript"></script>
<script src="src/sha1.js" type="text/javascript"></script>

<script language="JavaScript" src="src/gen_validatorv31.js" type="text/javascript"></script>

<script language="Javascript"> 
function check() {  
   if (self!=top) 
      top.location.href=self.location.href; 
   } 
setTimeout("check()", 1); 

function validarEmail(email) {     
	//if (/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.([a-zA-Z]{2,4})+$/.test(email)){ 
	if (/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(email)){	
		return (true)    
	} else {         
		return (false);     
	} 
} 

function limpia_email() {
    var val = document.getElementById("login").value;
	var tam = val.length;
	//alert("val = " + val +"\ntam = " + tam);
	if (tam > 0){
		var status = validarEmail(val);
		//alert("status = " + status);
		if (!status && tam > 0){
			alert("El login debe ser un email\ny \u00e9ste no tiene el formato adecuado");
			document.getElementById("login").value = '';
			window.setTimeout(function() { document.miform.login.focus(); },0);
		}
	}
}

function encrypt_pass(){
	var mipassword = document.getElementById("passw").value;
	var micurso = document.getElementById("curso").value;
	document.getElementById("passw").value = "";
	var cadena1 = "$"+micurso+"."+mipassword;
	//var salt = md5(cadena1);
	//var cadena2 = mipassword+salt;
	//var mipassword_encriptado = sha1(cadena2);
	//document.getElementById("passw_hashed").value = mipassword_encriptado;
	document.getElementById("passw_hashed").value = cadena1;
	
    var stars = "";
    for(i=0;i<mipassword.length;i++)
        stars = stars+"*";
    document.getElementById("passw").value = stars;
		
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

function Validacion(){
	var flag = 1;
	if (document.getElementById("6_letters_code").value == ''){
		alert("Falta el captcha");
		document.forms["miform"].reset();
		document.getElementById("login").focus();
		flag = 0;
	} 
	else{
		micaptcha = document.getElementById("6_letters_code").value;
		//alert("micaptcha = " + micaptcha);
		document.getElementById("micaptcha").value = micaptcha;
		
		document.getElementById("passw_hashed").value += micaptcha;
		var pass = document.getElementById("passw_hashed").value;
		//alert("pass = " + pass);
		
		var temp = document.getElementById("login").value;
		var code = document.getElementById("curso").value;
		var user = reshuffle(temp,micaptcha,code);	
		document.getElementById("login_scrambled").value = user;
		//alert("user = " + user);
		
		var stars = "";
		for(i=0;i<temp.length;i++)
			stars = stars+"*";
		document.getElementById("login").value = stars;
	}
	
	if (flag==1){
		return true;
	}else{
		return false;
	}
}

</script> 

</head>

<body bgcolor="#<?php echo $bgcolor ?>"> 

  
<?php  
if (!(isset($_POST) && array_key_exists('check', $_POST))) { // Si NO existe es que todavia no le hemos dado a submit
	
	 $registros0=mysql_query("SELECT * FROM cursos WHERE curso_activo = 1 AND inicio_curso <= CURDATE() AND fin_curso >= CURDATE() AND num_max_pods > 0 AND nombre_curso LIKE '%Experto%'",$conexion) or die ("Problemas con el select ".mysql_error());	
	 $registros1=mysql_query("SELECT * FROM cursos WHERE curso_activo = 1 AND inicio_curso <= CURDATE() AND fin_curso >= CURDATE() AND num_max_pods > 0 AND nombre_curso NOT LIKE '%Experto%'",$conexion) or die ("Problemas con el select ".mysql_error());
	 $registros2=mysql_query("SELECT * FROM cursos WHERE curso_activo = 1 AND inicio_curso <= CURDATE() AND fin_curso >= CURDATE() AND num_max_pods = 0",$conexion) or die ("Problemas con el select ".mysql_error());

?>	 

	<table border="0" width="100%">
	 <tr>
	  <td width="5%">
	  </td>
	  
      <td width="60%">	  
	   <table border="0">
	    <tr>
		 <td colspan="3">
		   <center>
		     <IMG SRC="fotos/remotelab.jpg" name="remotelab" id="remotelab" alt="remotelab" width="400px" height="100px" onContextMenu="return(false)" border="8">
             <br><br>
		   </center>	
		 </td>
		</tr>
		<tr>	
	     <td>
		     <br>
			 <a href="http://www.umh.es" target="_blank"><IMG SRC="fotos/umh.jpg" name="umh" id="umh" alt="umh" width="232px" height="95px" onContextMenu="return(false)" style="padding:1px;border:thin solid black;"></a>
			 <br><br>
			 
			 <table RULES=NONE FRAME=BOX>
			  <tr>
			   <td>
				  <IMG SRC="fotos/foro_label.png" name="foro_label" id="foro_label" alt="foro_label" width="50px" height="20px" onContextMenu="return(false)">
				   &nbsp;
				  <a href="http://formacioncisco.umh.es/" target="_blank"><IMG SRC="fotos/foro.jpg" name="foro" id="foro" alt="foro" width="170px" height="40px" onContextMenu="return(false)" style="padding:1px;border:thin solid black;"></a>
				</td>
			   </tr>
			  </table>
         </td>
		 <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		 </td>
		 <td>
			
		  <center>			
			<!--[if lt IE 9]>
				<IMG SRC="fotos/R&S.png" name="equipos_reales" id="equipos_reales" alt="equipos_reales" width="400px" height="200px" onContextMenu="return(false)" style="padding:1px;border:thin solid black;">			
			<![endif]-->
			
			<!--[if gte IE 9]>
		   <?php // convert image to dataURL
			 $img_source = "fotos/R&S.png"; // image path/name
			 $img_binary = fread(fopen($img_source, "r"), filesize($img_source));
			 $img_string = base64_encode($img_binary);
		   ?>
		   <IMG SRC="data:image/gif;base64,<?php echo $img_string; ?>" name="equipos_reales" id="equipos_reales" alt="equipos_reales" width="400px" height="200px" onContextMenu="return(false)" style="padding:1px;border:thin solid black;">
		   <![endif]-->
		   
		   <!--[if !IE]>-->
		   <?php // convert image to dataURL
			 $img_source = "fotos/R&S.png"; // image path/name
			 $img_binary = fread(fopen($img_source, "r"), filesize($img_source));
			 $img_string = base64_encode($img_binary);
		   ?>
		   <IMG SRC="data:image/gif;base64,<?php echo $img_string; ?>" name="equipos_reales" id="equipos_reales" alt="equipos_reales" width="400px" height="200px" onContextMenu="return(false)" style="padding:1px;border:thin solid black;">
		   <!--<![endif]-->
		  </center>
	     </td>
		</tr>
	   </table>
	  </td>
	
	  <td width="5%">
	  </td>
	
	  <td>
	   <center>	 
		 <P><P>
		 <FORM ACTION="comprobar.php" NAME="miform" METHOD="POST" onSubmit="encrypt_pass(); return Validacion();">
			   <!-- <center><U><font style="width:200; background: #ccffcc; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:14pt; font-weight:bold;  border: 1px solid #ccc;" readonly>INICIAR SESION</font></U></center>  -->
				<table RULES=NONE FRAME=BOX style="background: #<?php echo $bgcolor2 ?>">
				  <tr>       
					<?php  
					  $count0 = mysql_num_rows($registros0);
					  $count1 = mysql_num_rows($registros1);
					  $count2 = mysql_num_rows($registros2);
					  if (($count0 > 0) ||($count1 > 0) || ($count2 > 0)) {
					?>	 
					
						<td LABEL for="curso" ALIGN="right" style="font-size:14pt">curso: </LABEL>
					
						<td><SELECT name="curso" id="curso" style="width:234px; align=center; font-size:11pt; font-weight:bold; background:#ffffff;"> 			  
	  
						  <?php
							if ($count0 > 0){
								mysql_data_seek ( $registros0, 0);   //devolvemos el puntero a la posicion 0 de la lista
								while ($row0=mysql_fetch_array($registros0))
								{         
									$edicion = $row0['edicion'];
									if ($edicion > 0){
										echo "<option value='".$row0['curso_id']."'>".$row0['nombre_curso']."</option>";
										//echo "<option value='".$row0['curso_id']."'>".$row0['nombre_curso']."</option>";
									}else{
										echo "<option value='".$row0['curso_id']."'>".$row0['nombre_curso']."</option>";
									}
								}	
							}
							
							if ($count1 > 0){
								mysql_data_seek ( $registros1, 0);   //devolvemos el puntero a la posicion 0 de la lista
								while ($row=mysql_fetch_array($registros1))
								{         
									$edicion = $row['edicion'];
									if ($edicion > 0){
										echo "<option value='".$row['curso_id']."'>".$row['nombre_curso']." &nbsp;&nbsp; (".$row['edicion']." &ordf; edici&oacute;n)</option>";
										//echo "<option value='".$row['curso_id']."'>".$row['nombre_curso']."</option>";
									}else{
										echo "<option value='".$row['curso_id']."'>".$row['nombre_curso']."</option>";
									}
								}	
							}
							
							if ($count2 > 0){
								mysql_data_seek ( $registros2, 0);   //devolvemos el puntero a la posicion 0 de la lista
								while ($reg=mysql_fetch_array($registros2))
								{         
									echo "<option value='".$reg['curso_id']."'>".$reg['nombre_curso']."</option>";
								}
							}
						  ?>
						  
						</SELECT>
						</td>
						<div id="result">&nbsp;</div>
					<?php } else {	?>
						<td> <input type="hidden" name="curso" id="curso" value="-1"> </td>
					<?php } ?>
				  </tr>
				  <tr>
				   <td height="10"></td> <td></td>
				  </tr>
				  <tr>  
					<?php if (isset($_GET["errorusuario"])) 
						  {
							 if ($_GET["errorusuario"]=="si") {
					?> 
								<td colspan="2" align="center" bgcolor="coral"><span style="color:ffffff"><b>Datos incorrectos</b></span> 
					<?php    }else if($_GET["errorusuario"]=="md") { ?> 
					
								<td colspan="2" align="center" bgcolor="slate grey"><span style="color:ffffff"><b>Contrase&ntilde;a err&oacute;nea</b></span>
					<?php    }else if($_GET["errorusuario"]=="pr") { ?> 
					
								<td colspan="2" align="center" bgcolor="lightblue"><span style="color:ffffff"><b>Contrase&ntilde;a enviada por email</b></span>
					<?php    }else if($_GET["errorusuario"]=="ui") { ?> 
					
								<td colspan="2" align="center" bgcolor="lightblue"><span style="color:ffffff"><b>Email no registrado</b></span>
					<?php    }else if($_GET["errorusuario"]=="cp") { ?> 
					
								<td colspan="2" align="center" bgcolor="coral"><span style="color:ffffff"><b>Captcha err&oacute;neo</b></span>
					<?php    }else if($_GET["errorusuario"]=="cv") { ?> 
					
								<td colspan="2" align="center" bgcolor="lightblue"><span style="color:ffffff"><b>Captcha vac&iacute;o</b></span>
					<?php    }else if($_GET["errorusuario"]=="ev") { ?> 
					
								<td colspan="2" align="center" bgcolor="lightblue"><span style="color:ffffff"><b>Email vac&iacute;o</b></span>
					<?php    }else if($_GET["errorusuario"]=="ni") { ?> 
					
								<td colspan="2" align="center" bgcolor="mediumturquoise"><span style="color:ffffff"><b>No hay cursos disponibles ahora</b></span>
					<?php    }else if($_GET["errorusuario"]=="vs") { ?> 
					
								<td colspan="2" align="center" bgcolor="mediumturquoise"><span style="color:ffffff"><b>Error en la variable de session</b></span>

					<?php    }else if($_GET["errorusuario"]=="de") { ?> 
					
								<td colspan="2" align="center" bgcolor="mediumturquoise"><span style="color:ffffff"><b>N&uacute;mero de Pods excesivo</b></span>
					<?php    }else if ($_GET["errorusuario"]=="no") { ?>
					
								<td colspan="2" align="center" bgcolor="lightblue"><span style="color:ffffff"><b>No matriculado en el curso seleccionado</b></span>
					<?php    }else if ($_GET["errorusuario"]=="au") { ?>
					
								<td colspan="2" align="center" bgcolor="mediumturquoise"><span style="color:ffffff"><b>Usuario no autenticado</b></span>
					<?php    }else if ($_GET["errorusuario"]=="ad") { ?>
					
								<td colspan="2" align="center" bgcolor="mediumturquoise"><span style="color:ffffff"><b>No tienes privilegios de administrador</b></span>
					<?php    }else if ($_GET["errorusuario"]=="to") { ?>
					
								<td colspan="2" align="center" bgcolor="slate grey3"><span style="color:ffffff"><b>Timeout cumplido</b></span>
					<?php    }else if ($_GET["errorusuario"]=="sa") { ?>
					
								<td colspan="2" align="center" bgcolor="aquamarine"><span style="color:ffffff"><b>Salida del sistema</b></span>
					<?php    }else if ($_GET["errorusuario"]=="fi") { ?>
					
								<td colspan="2" align="center" bgcolor="aquamarine"><span style="color:ffffff"><b>Fin de la sesi&oacute;n</b></span>
					<?php
						}  } else {				?>			
								<td colspan="2" align="center" bgcolor=#e0e0e0>Introduce tu clave de acceso 
					<?php } ?></td> 
				  </tr> 
				  <tr>
				   <td height="10"></td> <td></td>
				  </tr>
					<?php  
					  mysql_free_result($registros1);
					  mysql_free_result($registros2);
					?>
				  <tr>
					<td LABEL for="login" ALIGN="right" style="font-size:14pt">usuario: </LABEL>
					<td><INPUT TYPE="text" NAME="login" id="login" VALUE="" SIZE="35" >  <!-- onblur="limpia_email()" --> 
					  <script type="text/javascript">
						var txtBox=document.getElementById("login" );
						if (txtBox!=null ) txtBox.focus();
					  </script>
				   </td>
				  </tr>
				 
				  <tr>
					<td LABEL for="passw" ALIGN="right" style="font-size:14pt"> &nbsp; &nbsp; contrase&ntilde;a:</LABEL>            
					<td><INPUT TYPE="password" NAME="passw" id="passw" VALUE="" SIZE="35"></td>
				  </tr>
	 

				  <tr height="14px">
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
						<input type="hidden" name="login_scrambled" id="login_scrambled" value="">
						<input type="hidden" name="passw_hashed" id="passw_hashed" value="">
						<input type="hidden" name="micaptcha" id="micaptcha" value="">
						<input type="hidden" name="check" value="1">
					</td>
					<td height="50"> <INPUT TYPE="submit" NAME="entrar" VALUE="ENTRAR">
									 <INPUT TYPE="reset" NAME="borrar" VALUE="BORRAR">
					</td>
				  </tr>
				  
				  <tr>
					<td colspan="2" align="center">
						<table style="border: thin solid black">
							<tr>    
								<td>
									<a href="recuperacion_contrasenia.php"><IMG SRC="fotos/password_recovery.jpg" name="passwrec" id="passwrec" alt="passwrec" width="80px" height="40px" onContextMenu="return(false)"></a> 
									&nbsp;&nbsp;							
								</td>  
								<td>
									<font style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size:8pt;"><A href="recuperacion_contrasenia.php">Recuperaci&oacute;n de Contrase&ntilde;as</A></font> 
								</td>
							</tr>
						</table> 
					</td>
				  </tr>
				</table>
			   </center>
			 </FORM>

			 
			 <!-- <br><br> -->
			 <!-- <center><U><font style="width:420; background: #ccffcc; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:14pt; font-weight:bold;  border: 1px solid #ccc;" readonly>TU LABORATORIO DE REDES ON-LINE </font></U></center> -->

	  </td>
	  <td width="5%">
	  </td>
	 </tr>
	</table>
	

<!-- footer START -->
<div id="footer">
  <center>
    <IMG SRC="fotos/pie.jpg" name="pie" id="pie" alt="pie" width="90%" height="100px" onContextMenu="return(false)">
  </center>
</div>
<!-- footer END -->

			 
			 
		<script language="JavaScript" type="text/javascript">
			document.forms["miform"].reset();
			document.getElementById("miform").reset();
			document.getElementById("login").value = '';
			document.getElementById("passw").value = '';
		</script>			 
		 
<?php
		 


}else{

    ////se comprueba si se han recibido datos del formulario
	if(isset($_POST['micaptcha']))
	{
		//echo "<br>SUBMIT!!!";
		$micaptcha = $_POST['micaptcha'];
		$micaptcha = trim(stripslashes($micaptcha));
		/* echo "<script> alert('Mi captcha = ' + <?php echo \"$micaptcha\"; ?>'); </script>"; */
		
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
			echo "<script> document.forms[\"miform\"].reset(); </script>";
			
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
			echo "<script> document.forms[\"miform\"].reset(); </script>";
			
			//liberamos recursos
			mysql_close($conexion);
				
			session_destroy();
			session_unset();	
				
			//se devuelve al inicio 
			header("Location: index.php?errorusuario=cp"); 	
		}
	
		//echo "<br>Pasando...";

		$usuario = trim(stripslashes($_POST['login']));
		$oculto = trim(stripslashes($_POST['passw']));
		$contrasenia = trim(stripslashes($_POST['passw_hashed']));
		$curriculum = trim(stripslashes($_POST['curso']));
		
		$usuario_encr = encrypt($usuario, $clave);
		$contrasenia_encr = encrypt($contrasenia, $clave);
		$curso_encr = encrypt($curriculum, $clave);

?>


		<script language="Javascript" type="text/javascript">
			var isOpera = !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;  		// Opera 8.0+ (UA detection to detect Blink/v8-powered Opera)

			var isFirefox = typeof InstallTrigger !== 'undefined';   // Firefox 1.0+
			var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;  		// At least Safari 3+: "[object HTMLElementConstructor]"

			var isChrome = !!window.chrome && !isOpera;              // Chrome 1+
			var isIE = /*@cc_on!@*/false || document.documentMode;   // At least IE6
			
			///////////////////////////
			//document.write('isFirefox: ' + isFirefox + '<br>');
			//document.write('isChrome: ' + isChrome + '<br>');
			//document.write('isSafari: ' + isSafari + '<br>');
			//document.write('isOpera: ' + isOpera + '<br>');
			//document.write('isIE: ' + isIE + '<br>');
			
		</script>
	
<!--    

		<table align="center" width="100%" border="0">
			<tr>
				<td width="30%">
				</td>
				
				<td align="center">				
					<IMG SRC="fotos/earth_computer_animated_gif.gif" ALT="conectando" name="conectando" id="conectando" width="400px" height="400px" onContextMenu="return(false)"> 
				</td>

				<td width="30%">
				</td>
			</tr>
			<tr>
				<td width="30%">
				</td>

				<td align="center" valign="middle"><h1 style="text-align: center; color: black;">CONECTANDO ... </h1>
				</td>
				
				<td width="30%">
				</td>				
			</tr>
			<tr>
				<td width="30%">
				</td>

				<td align="center">
					<IMG SRC="fotos/cargando.gif" ALT="cargando" name="cargando" id="cargando" width="150px" height="30px" onContextMenu="return(false)">
		

						<IMG SRC="fotos/fotolab.jpg" ALT="cargando" name="cargando" id="cargando" width="600px" height="450px" onContextMenu="return(false)">	

			<script language="Javascript" type="text/javascript">
			////con esto no aparecen errores en la foto con Chrome y Safari///
			</script>					
				</td>
				
				<td width="30%">
				</td>				
			</tr>
			<tr>
				<td width="30%">
				</td>
				<td align="center" valign="middle"> <font style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size:44pt; font-weight:bold; color:red"> RemoteLab </font>
				</td>
				<td width="30%">
				</td>
			</tr>
		</table>
-->

	  <center>	 
		 <P><P>
		 <FORM ACTION="" NAME="miform" METHOD="POST" onSubmit="encrypt_pass(); return Validacion();">
				<table RULES=NONE FRAME=BOX style="background: #<?php echo $bgcolor2 ?>" border="0">
				  <tr>       					
						<td height="100px"></td>			
						<td></td>
				  </tr>
				  <tr>  		
						<td height="100px"></td> 
						<td></td>
				  </tr> 
				</table>
			   </center>
			 </FORM>

			 <p>
			 <center><h1 style="text-align: center; color: black;">CONECTANDO ... </h1></center>
			 
	
	<form name="miencform" method="POST" action="comprobar.php">
				<input type="hidden" name="curso_encrypted" id="curso_encrypted" value="">			 
				<input type="hidden" name="login_encrypted" id="login_encrypted" value="">
				<input type="hidden" name="passw_encrypted" id="passw_encrypted" value="">
				<input type="hidden" name="pass" id="pass" value="">					
	</form>
	 
	 
	<script type="text/javascript" language="javascript">

		var curso_encr = "<?php echo $curso_encr ?>";
		var usuario_encr = "<?php echo $usuario_encr ?>";
		var contrasenia_encr = "<?php echo $contrasenia_encr ?>";
		var oculto = "<?php echo $oculto ?>";		

		//alert("curso_encr = " + curso_encr +"\nusuario_encr = " + usuario_encr + "\ncontrasenia_encr = " + contrasenia_encr + "\noculto = " + oculto);
		//document.write("curso_encr: " + curso_encr + "<br>usuario_encr: " + usuario_encr + "<br>contrasenia_encr: " + contrasenia_encr + "<br>oculto = " + oculto);
		
		document.getElementById("curso_encrypted").value = curso_encr;
		document.getElementById("login_encrypted").value = usuario_encr;
		document.getElementById("passw_encrypted").value = contrasenia_encr;
		document.getElementById("pass").value = oculto;

		///document.getElementById('miencform').submit();
		document.forms["miencform"].submit();
	</script>


<?php
	}
}

//liberamos recursos
mysql_close($conexion);
	
//session_destroy();
//session_unset();
?>

</body>
</html>  
