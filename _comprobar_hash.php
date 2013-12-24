<?php
 include ("headers/conexion.php");
 include ("headers/crypto.php");
 
//si es necesario cambiar la config. del php.ini desde este script
ini_set("session.use_only_cookies","1"); 
ini_set("session.use_trans_sid","0"); 
session_cache_limiter('nocache,private_no_expire');
//cambiamos la duración a la cookie de la sesión a cero
session_set_cookie_params(0, "/", $_SERVER["HTTP_HOST"], 0);
	
session_start();
$msg = '';

//echo "<br>";   //comprobamos el valor de la varible de sesion 6_letters_code
//var_dump($_SESSION['6_letters_code']); die;

////se comprueba si se han recibido datos del formulario
if(isset($_POST['micaptcha']))
{
	//echo "<br>SUBMIT!!!";
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
		
		$msg= "\n El captcha NO coincide!<br>";
		//echo "<br>msg = $msg";
		echo "<script> alert (\"El captcha NO coincide.\"); </script>";
		echo "<script> document.forms[\"miform\"].reset(); </script>";
		
		//liberamos recursos
		mysql_close($conexion);
			
		session_destroy();
		session_unset();	
			
		//se devuelve al inicio 
		header("Location: index.php?errorusuario=cp"); 	
	}else
	{
		$msg= "\n El captcha SI coincide!<br>";
		//echo "<br>msg = $msg";
		
		session_destroy();
		session_unset();
	}
} 

////funcion para redirigir hacia una url cuando hay @ en el usuario
function redirect($url)
{
    if (!headers_sent())
    {    
        header('Location: '.$url);
        exit;
        }
    else
        {  
        echo '<script type="text/javascript">';
        echo 'window.location.href="'.$url.'";';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
        echo '</noscript>'; exit;
    }
}
	
//iniciamos el buffer de salida 
ob_start();
 
//// Define $myusername and $mypassword and $mycourseid
//$myusername=decrypt($_POST['login_encrypted'],$clave); 
//$mypassword=$_POST['passw'];
//$mycourseid=decrypt($_POST['curso_encrypted'],$clave);
//$mypass_md5=decrypt($_POST['passw_encrypted'],$clave);

////////$myusername=$_POST['login']; 
$myusername=$_POST['login_scrambled']; 
$mypassword=$_POST['passw'];
$mycourseid=$_POST['curso'];
$mypass_md5=$_POST['passw_hashed'];


// To protect MySQL injection 
$myusername = trim(stripslashes($myusername));
$mypassword = trim(stripslashes($mypassword));
$myusername = mysql_real_escape_string($myusername);
$mypassword = mysql_real_escape_string($mypassword);

$mypass_md5 = trim(stripslashes($mypass_md5));
$mypass_md5 = mysql_real_escape_string($mypass_md5);


///reassembling login
$len_micaptcha = strlen($micaptcha);
$len_total = strlen($myusername);
$mistring = substr($myusername,$len_micaptcha,$len_total-1);
$tam = strlen($mistring);
$findme = '*';
$pos = strpos($mistring, $findme);
if ($pos !== FALSE){
	$mistring[$pos] = '@';
}	
echo "<br>mistring = $mistring  -- tam = $tam";
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
$myusername = '';
$myusername = $tmp;
echo "<br>myusername = $myusername";



$sql1="SELECT * FROM usuarios WHERE username='$myusername'";
$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el Select  (sql1) ".mysql_error());

// Mysql_num_row is counting table row
$count=mysql_num_rows($registros1);


// If result matched $myusername, table row must be 1 row
if($count==1){
    while ($reg1=mysql_fetch_array($registros1)){
		// procesamos la contraseña del usuario
		$password = $reg1['password'];   
		$prefix = '0xD51800BCF2A0FE4A';
		$cadena1 = "$".$mycourseid.".".$password;
		$salt = md5($cadena1);
		$cadena2 = $prefix.$password.$salt;
		$mi_pass_md5 = sha1($cadena2);
		$mipassword_md5 = $mi_pass_md5.$captcha;
		echo "<br>mypass_md5(js) = $mypass_md5 -- mipassword_md5(php) = $mipassword_md5"; 
		
		if ($mipassword_md5 != $mypass_md5){
			mysql_free_result($registros1);
			mysql_close($conexion);
			ob_end_flush();

			//si no existe le mando otra vez a la portada 
			header("Location: index.php?errorusuario=md");	
		}else{
			// comprobamos si este usuario es administrador o no
			$admin = $reg1['admin'];
		}
	}
	echo "<br>mycourseid = $mycourseid";
	//////////////////////////////////////////////////////////////////// comprobacion de acceso para cursos de control /////
	///un curso de control tiene el campo $num_max_pods = 0 en la tabla 'cursos'///
	$sql0="SELECT * FROM cursos WHERE curso_id = $mycourseid";
    $registros0=mysql_query($sql0,$conexion) or die ("Problemas con el Select (sql0) ".mysql_error());
    $count0 = mysql_num_rows($registros0);	
	if ($count0 > 0){
		while ($reg0=mysql_fetch_array($registros0)){
			$num_max_pods = $reg0['num_max_pods'];
			echo "<br>num_max_pods = $num_max_pods";
		}
	}else{
		echo "<br>Error en la tabla cursos!";
	}
	mysql_free_result($registros0);
	
	//se comprueba si se esta accediendo a un curso de control  ($num_max_pods = 0)
	if ($num_max_pods == 0){
			//solo se puede acceder a curso de control si se es administrador
			if ($admin == 0){
				mysql_free_result($registros1);
				mysql_close($conexion);
				ob_end_flush();

				//si no existe le mando otra vez a la portada 
				header("Location: index.php?errorusuario=ad");				
			}
	}
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
    //se comprueba si hay cursos dispo{nibles en este momentos
    if ($mycourseid == -1){
		mysql_free_result($registros1);
		mysql_close($conexion);
		ob_end_flush();

        //si no existe le mando otra vez a la portada 
        header("Location: index.php?errorusuario=ni"); 
	}
 
    //asigno un nombre a la sesión para poder guardar diferentes datos
    session_name("loginUsuario"); 
	session_cache_limiter('nocache,private_no_expire');
	//cambiamos la duración a la cookie de la sesión a cero
	session_set_cookie_params(0, "/", $_SERVER["HTTP_HOST"], 0);
    // inicio la sesión  
    session_start(); 
 
    //defino la variable de sesión que demuestra que el usuario está autorizado 
    $_SESSION["autentificado"]= "SI."; 
    //defino la fecha y hora de inicio de sesión en formato aaaa-mm-dd hh:mm:ss 
    $_SESSION["ultimoAcceso"]= date("Y-n-j H:i:s"); 
	
    // Registro el nombre del usuario
    $_SESSION['usuario'] = $myusername;
	// Registro el id del usuario
	mysql_data_seek ( $registros1, 0);
	if ($reg1=mysql_fetch_array($registros1)) 
       $id_usuario = $reg1['user_id'];
	$_SESSION['id_usuario'] = $id_usuario;
	// Registro el id del curso
	$id_curso = $mycourseid;
	$_SESSION['id_curso'] = $id_curso;
   
    echo "<br>id_curso = $id_curso";
	echo "<br>id_usuario = $id_usuario";
	
    // comprobamos si el usuario esta matriculado en el curso seleccionado
    $sql2="SELECT * FROM alumnos_en_cursos WHERE curso_id = $id_curso AND user_id = $id_usuario"; 
    $registros2=mysql_query($sql2,$conexion) or die ("Problemas con el Select (sql2) ".mysql_error());
    $count2 = mysql_num_rows($registros2);
	echo "<br>count2 =  $count2";

	if ($count2 == 0){
		mysql_free_result($registros1);
		mysql_free_result($registros2);
		mysql_close($conexion);
		ob_end_flush();

		//si no esta matriculado le mando otra vez a la portada 
		header("Location: index.php?errorusuario=no"); 
		//echo "<br><br>Pasamos a index.php";
	} else {
	    // insertamos los datos de usuario, curso y tiempo de entrada en la tabla LOGS
		echo "<br>id_usuario = $id_usuario";
		echo "<br>id_curso = $id_curso";
		echo "<br>ahora = $ahora";
        $sql3="INSERT INTO logs(user_id, curso_id, entrada) VALUES ($id_usuario, $id_curso, NOW() )"; 
        $registros3=mysql_query($sql3,$conexion) or die("Problemas en el Insert (sql3) ".mysql_error());

		// contamos el numero historico de conexiones de este usuario
		$sql4="SELECT count(*) AS total FROM logs WHERE user_id = $id_usuario";
		$registros4=mysql_query($sql4,$conexion) or die ("Problemas con el select  (sql4) ".mysql_error());
        $cuenta=mysql_fetch_assoc($registros4);
		echo "<br>Total conexiones = ".$cuenta['total'];
	 
	    // seleccionamos el id de esta sesion
		$sql5="SELECT log_id FROM logs ORDER BY log_id DESC LIMIT 1"; 
		$registros5=mysql_query($sql5,$conexion) or die ("Problemas con el Select  (sql5) ".mysql_error()); 
		if ($reg5=mysql_fetch_array($registros5)) 
          $id_sesion = $reg5['log_id'];
		echo "<br>id_sesion = $id_sesion";
	    $_SESSION['id_sesion'] = $id_sesion;
			
		// seleccionamos los parametros basicos del sistema
		$sql6="SELECT * FROM parametros_basicos ORDER BY param_id DESC LIMIT 1";
		$registros6=mysql_query($sql6,$conexion) or die ("Problemas con el Select  (sql6) ".mysql_error());
		
        while ($reg6=mysql_fetch_array($registros6)){
		    $duracionTurno = $reg6['duracionTurno'];
			$_SESSION['duracionTurno']=$duracionTurno;
		   
  		    $numPods = $reg6['numPods'];
			////$_SESSION['numPods']=$numPods;    //modificado mas abajo para adecuar cada curso a un numero maximo de pods distinto
		 
		   	$numMaxReservas = $reg6['numMaxReservas'];
			$_SESSION['numMaxReservas']=$numMaxReservas;

		   	$numMaxResvSemana = $reg6['numMaxResvSemana'];
			$_SESSION['numMaxResvSemana']=$numMaxResvSemana;
			
		   	$numMaxCanc = $reg6['numMaxCanc'];
			$_SESSION['numMaxCanc']=$numMaxCanc;

		   	$round_robin = $reg6['round_robin'];
			$_SESSION['round_robin']=$round_robin;	

		   	$idle_timeout = $reg6['idle_timeout'];
			$_SESSION['idle_timeout']=$idle_timeout;		
        }
		
		$_SESSION['numTurnosDia'] = floor(24/$duracionTurno);  
		$numTurnosDia = $_SESSION['numTurnosDia'];
		echo "<br><br>duracionTurno = $duracionTurno";
        echo "<br>numTurnosDia = $numTurnosDia";
		
		// seleccionamos el nombre del curso elegido
		$sql7="SELECT * FROM cursos WHERE curso_id = $id_curso";
		$registros7=mysql_query($sql7,$conexion) or die ("Problemas con el Select  (sql7) ".mysql_error());
		
		if ($reg7=mysql_fetch_array($registros7)){
           $nombre_curso = $reg7['nombre_curso'];
		   $edicion = $reg7['edicion'];
		   $num_max_pods = $reg7['num_max_pods'];
		  
		   $dia_mant_semanal = $reg7['dia_mant_semanal'];
		   $hora_inicio_mant_semanal = $reg7['hora_inicio_mant_semanal'];
		   $duracion_mant_semanal = $reg7['duracion_mant_semanal'];
		}  
		echo "<br>nombre_curso = $nombre_curso";
	    $_SESSION['nombre_curso'] = $nombre_curso;
		$_SESSION['edicion'] = $edicion;
		$_SESSION['num_max_pods'] =  $num_max_pods;		
		
		$_SESSION['dia_mant_semanal']=$dia_mant_semanal;
		$_SESSION['hora_inicio_mant_semanal']=$hora_inicio_mant_semanal;
		$_SESSION['duracion_mant_semanal']=$duracion_mant_semanal;	
			
			
		// seleccionamos el numero maximo de pods que corresponden al curso elegido
		// cursos de experto universitario
		$sql8="SELECT * FROM cursos WHERE curso_activo = 1 AND inicio_curso <= CURDATE() AND fin_curso >= CURDATE() AND num_max_pods > 0 AND nombre_curso LIKE '%Experto%'";
		$registros8=mysql_query($sql8,$conexion) or die ("Problemas con el Select  (sql8) ".mysql_error());
		$num_cursos_activos = mysql_num_rows($registros8);
		//echo "<br><br>num_cursos_activos = $num_cursos_activos";
		$i = 0;
		$total_pods = 0;
		//echo "<br><br>i = $i";
		if ($reg8=mysql_fetch_array($registros8)){
			mysql_data_seek ( $registros8, 0);   //devolvemos el puntero a la posicion 0 de la lista
		    while ($reg8 = mysql_fetch_array($registros8)){
				$codigo_curso = $reg8['curso_id'];
				//echo "<br><br>codigo_curso = $codigo_curso";
				$array_curso_id[$i] = $codigo_curso;
				//echo "<br>array_curso_id[$i] = $array_curso_id[$i]";
				
				$pods_curso = $reg8['num_max_pods'];
				//echo "<br><br>pods_curso = $pods_curso";
				$array_num_max_pods[$i] = $pods_curso;
				//echo "<br>array_num_max_pods[$i] = $array_num_max_pods[$i]";
				$i++;
				//echo "<br><br>i = $i";
				$total_pods = $total_pods + $pods_curso;
			}
		}  
		
		// resto de cursos
		$sql9="SELECT * FROM cursos WHERE curso_activo = 1 AND inicio_curso <= CURDATE() AND fin_curso >= CURDATE() AND num_max_pods > 0 AND nombre_curso NOT LIKE '%Experto%'";
		$registros9=mysql_query($sql9,$conexion) or die ("Problemas con el Select  (sql9) ".mysql_error());
		$num_cursos_activos += mysql_num_rows($registros9);
		//echo "<br><br>num_cursos_activos = $num_cursos_activos";
		//echo "<br><br>i = $i";
		if ($reg9=mysql_fetch_array($registros9)){
			mysql_data_seek ( $registros9, 0);   //devolvemos el puntero a la posicion 0 de la lista
		    while ($reg9 = mysql_fetch_array($registros9)){
				$codigo_curso = $reg9['curso_id'];
				//echo "<br><br>codigo_curso = $codigo_curso";
				$array_curso_id[$i] = $codigo_curso;
				//echo "<br>array_curso_id[$i] = $array_curso_id[$i]";
				
				$pods_curso = $reg9['num_max_pods'];
				//echo "<br><br>pods_curso = $pods_curso";
				$array_num_max_pods[$i] = $pods_curso;
				//echo "<br>array_num_max_pods[$i] = $array_num_max_pods[$i]";
				$i++;
				//echo "<br><br>i = $i";
				$total_pods = $total_pods + $pods_curso;
			}
		} 
		
		//echo "<br><br>id_curso = $id_curso";
		for ($j=0; $j<$num_cursos_activos; $j++) {
			echo "<br><br>array_curso_id[$j] = $array_curso_id[$j]";
			echo"<br>array_num_max_pods[$j] = $array_num_max_pods[$j]";
		}
		$offset_pod = 0;
		for ($j=0; $j<$num_cursos_activos; $j++) {
			if ($array_curso_id[$j] == $id_curso){
				$num_max_pods =$array_num_max_pods[$j];
				$flag = $j;
			}
		}
		for ($k=0; $k<$flag; $k++) {
				$offset_pod = $offset_pod + $array_num_max_pods[$k];
		}
		echo "<br><br>num_max_pods = $num_max_pods ; numPods = $numPods ; offset_pod = $offset_pod";
		if (($offset_pod + $num_max_pods) > $numPods){
		    echo "<br><br>N&uacute;mero de Pods m&aacute;ximo para este curso sobrepasa el n&uacute;mero de Pods existentes.";
			echo "<br>Se debe reconfigurar el n&uacute;mero de Pods asignados a este curso en particular.";
			mysql_free_result($registros8);
			mysql_close($conexion);
			ob_end_flush();
		   //se devuelve al inicio, con un mensaje de advertencia 
		   header("Location: index.php?errorusuario=de"); 			
		}
		$_SESSION['total_pods'] = $total_pods;   echo "<br>total_pods = $total_pods";
		$_SESSION['offset_pod'] = $offset_pod;
		$_SESSION['numPods'] = $num_max_pods;        ////$_SESSION['numPods']=$numPods;
		echo "<br><br>num_max_pods = $num_max_pods";	
		
		if ($num_max_pods == 0)
				$_SESSION['control'] = 1;
		else
			$_SESSION['control'] = 0;
			
		// comprobamos si este usuario es administrador o no
		echo "<br><br>admin = $admin";
	    $_SESSION['admin'] = $admin;
	
	    if ($admin == 1){
			mysql_free_result($registros1);
			mysql_free_result($registros2);
			//mysql_free_result($registros3);  //En sentencias INSERT, UPDATE, etc. mysql_query() no devuelve un recurso sino TRUE o FALSE. No tiene sentido usar mysql_free_result() en esos casos ya que no hay nada que liberar.
			mysql_free_result($registros4);
			mysql_free_result($registros5);
			mysql_free_result($registros6);
			mysql_free_result($registros7);
			mysql_free_result($registros8);
			mysql_free_result($registros9);
			mysql_close($conexion);
			ob_end_flush();
			
		    header ("Location: admin.php");
	        echo"<br><br>Pasamos a admin.php";
			
	    }else{
		    mysql_free_result($registros1);
			mysql_free_result($registros2);
			//mysql_free_result($registros3);   //En sentencias INSERT, UPDATE, etc. mysql_query() no devuelve un recurso sino TRUE o FALSE. No tiene sentido usar mysql_free_result() en esos casos ya que no hay nada que liberar.
			mysql_free_result($registros4); 
			mysql_free_result($registros5);
			mysql_free_result($registros6);
			mysql_free_result($registros7);
			mysql_free_result($registros8);
			mysql_free_result($registros9);
			mysql_close($conexion);
			ob_end_flush();
			
			$_SESSION['checkin'] = "Bien.";
            //header ("Location: reservas.php");
			redirect("reservas.php");
	        echo"<br><br>Pasamos a reservas.php";
	    }
   }
}
else {
   //echo "<br><br>Wrong Username or Password";
    mysql_free_result($registros1);
    mysql_close($conexion);
    ob_end_flush();
 
   //si no existe le mando otra vez a la portada 
   header("Location: index.php?errorusuario=si"); 
}
 ?>
 
<!-- <br><br><A href="index.php">VOLVER</A> -->