<?php 
 include ("headers/seguridad.php");
 include ("headers/conexion.php");

 header( 'Content-type: text/html; charset=utf-8' );
 
//iniciamos el buffer de salida 
//ob_start();

//cortamos los intentos de entrada sin autenticar
if (!(isset($_SESSION['checkout']))) {
 
    echo "<br>Acceso denegado. Debes autenticarte antes de entrar.";
	mysql_close($conexion);
	//ob_end_flush();		
    session_destroy();
   //el usuario debe autenticarse antes de entrar 
   header("Location: index.php?errorusuario=au");
   
}
$checkout = $_SESSION['checkout'];
if ($checkout != "Ok."){
    echo "<br>Acceso denegado. Debes autenticarte antes de entrar.";
	mysql_close($conexion);
	//ob_end_flush();		
    session_destroy();
   //el usuario debe autenticarse antes de entrar 
   header("Location: index.php?errorusuario=au");
}

//recopilacion de datos
$pod_activo = $_SESSION['pod_activo'];
$reserva_id = $_SESSION['reserva_id'];
$id_sesion = $_SESSION['id_sesion'];

$id_usuario = $_SESSION['id_usuario'];
$duracionTurno = $_SESSION['duracionTurno'];
$nombre_curso = $_SESSION['nombre_curso'];

$f_ahora = date('Y-m-d H:i:s');
$timeNow = strtotime($f_ahora );

$anio_ahora = date("Y", $timeNow);
$mes_ahora = date("m", $timeNow);
$dia_ahora = date("d", $timeNow);

$hoy = $dia_ahora."/".$mes_ahora."/".$anio_ahora;
//echo "<br>hoy = $hoy";

$str_fecha_resv = $_SESSION['str_fecha_resv'];
//echo "str_fecha_resv = $str_fecha_resv";
$fecha_inicio_reserva = $_SESSION['fecha_inicio_reserva'];
$fecha_fin_reserva = $_SESSION['fecha_fin_reserva'];
if (isset($fecha_fin_reserva)){
	$timeStamp = strtotime($fecha_fin_reserva);

	$anio_final = date("Y", $timeStamp);
	$mes_final = date("m", $timeStamp);
	$dia_final = date("d", $timeStamp);

	$hora_final = date("H", $timeStamp);
	$min_final = date("i", $timeStamp);
	$seg_final = date("s", $timeStamp);
}else{
	//Hora de referencia activa -> el inicio del dia de hoy, a medianoche
	$hora_ref_activa = date('Y-m-d'." 00:00:00");
	
	$anio_final = date("Y", $f_ahora);
	$mes_final = date("m", $f_ahora);
	$dia_final = date("d", $f_ahora);	

	$hora_ahora = date("H", $f_ahora);
	$hora_final = (floor($hora_ahora/2)*2)+2;
	$min_final = "00";
	$seg_final = "00";

	$timeStamp = date('Y-m-d H:i:s',strtotime('+'.$hora_final.' hours', strtotime($hora_ref_activa)));
}

$seg_restantes = round($timeStamp - $timeNow);
//echo "<br>seg_restantes = $seg_restantes";
//echo "<br>f_ahora  = $f_ahora ; fecha_inicio_reserva = $fecha_inicio_reserva ; fecha_fin_reserva = $fecha_fin_reserva";
//echo "<br>dia_final = $dia_final ; hora_final = $hora_final ; min_final = $min_final";

$countdown_seg = $seg_restantes % 60;
$countdown_min = floor($seg_restantes / 60) % 60;     
$countdown_hor = floor($seg_restantes / 3600) %24;     

$time_countdown = $countdown_hor.":".$countdown_min.":".$countdown_seg;
//echo "<br>time_countdown = $time_countdown";
$timeDown = strtotime($time_countdown);
//echo "<br>timeDown = $timeDown";
$hora_countdown = date("H", $timeDown);
//echo "<br>hora_countdown = $hora_countdown";
$min_countdown = date("i", $timeDown);
//echo "<br>min_countdown = $min_countdown";
$seg_countdown = date("s", $timeDown);
//echo "<br>seg_countdown = $seg_countdown";


///fija el final de la sesion ASP con el fin del Turno de la Reserva actual

// Get the current Session Timeout Value
$currentTimeoutInSecs = ini_get("session.gc_maxlifetime");
//echo "<br>Session Timeout Value by default = $currentTimeoutInSecs";

// Change the session timeout value to 30 minutes  // 8*60*60 = 8 hours   
//ini_set(’session.gc_maxlifetime’, 30*60);  ///$seg_restantes -> los segundos que faltan para llegar al fin de la reserva
//// php.ini setting required for session timeout.
////ini_set(”session.cookie_lifetime”,120);  ini_set(”session.gc_maxlifetime”, 120);  --> antes del sesion start();
$offset = 3;
$tiempo_restante = $seg_restantes + $offset;

///aumentar el tiempo de sesion
ini_set("session.cookie_lifetime", $tiempo_restante);       //Para modificar el tiempo de vida de una sesión. El tiempo viene dado en segundos
ini_set("session.gc_maxlifetime", $tiempo_restante);        //Para que el recolector de basura de php no elimine la cookie antes de su expiración
ini_set("session.gc_probability",1);
ini_set("session.gc_divisor",1);

$newTimeoutInSecs = ini_get("session.gc_maxlifetime");
//echo "<br>New Session Timeout Value = $newTimeoutInSecs";


///se actualiza la tabla reservas, para reflejar este turno como aprovechado
$estado_reserva = 2;
$sql1 = "UPDATE reservas SET estado_reserva=$estado_reserva WHERE reserva_id = $reserva_id";
$registros1 = mysql_query($sql1,$conexion) or die ("Problemas con el Update  (sql1) ".mysql_error());

$sql2 = "UPDATE log_detalles SET in_pod=$pod_activo WHERE user_id = $id_usuario AND confir_resv = '$str_fecha_resv'";
$registros2 = mysql_query($sql2,$conexion) or die ("Problemas con el Update  (sql2) ".mysql_error());

         ////////////////
         //$fp = fopen("/var/www/loggs.txt","a");
         //fwrite($fp, "**En uso.- inicio_reserva: $fecha_inicio_reserva \t fin_reserva: $fecha_fin_reserva \t id_sesion: $id_sesion \t status= $estado_reserva \t user_id= $id_usuario" . PHP_EOL); 
         //fclose($fp);
         ///////////////

         ////////////////
         //$fp = fopen("/var/www/loggs.txt","a");
         //fwrite($fp, "-------------------------------------------------------------------------------------------------------------------------------" . PHP_EOL); 
         //fclose($fp);
         ///////////////

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
 <title>LAB</title>
  <!-- Estas lineas evitan que la pagina deseada no se cargue en la cache y no pueda ser mostrada cuando se regresa atras. -->
   <meta http-equiv="Expires" CONTENT="0">
   <meta http-equiv="Cache-Control" CONTENT="no-cache">
   <meta http-equiv="Pragma" CONTENT="no-cache"> 

  <!-- Estas lineas evitan el zoom -->
   <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
   <meta name="viewport" content="width=device-width" />
   <!-- <meta name="viewport" content="target-densitydpi=device-dpi, initial-scale=1.0, user-scalable=no" />   -->
  
  
<script LANGUAGE="JavaScript">
  function FinTurno()
  {
    //alert("\u00a1Turno de Reserva agotado!") 
    document.forms["form_salida_timeup"].submit();
  }
</script>


 <SCRIPT LANGUAGE="JavaScript">
 <!--
  function mueveReloj(){ 
    momentoActual = new Date() 
    hora = momentoActual.getHours() 
    minuto = momentoActual.getMinutes() 
    segundo = momentoActual.getSeconds() 

    str_segundo = new String (segundo) 
    if (str_segundo.length == 1) 
       segundo = "0" + segundo 

    str_minuto = new String (minuto) 
    if (str_minuto.length == 1) 
       minuto = "0" + minuto 

    str_hora = new String (hora) 
    if (str_hora.length == 1) 
       hora = "0" + hora 

    horaImprimible = hora + " : " + minuto + " : " + segundo 

    document.form_reloj.reloj.value = horaImprimible 


    setTimeout(function() { mueveReloj(); }, 1000);  
  }
  //-->
 </script>
 
 
  <SCRIPT LANGUAGE="JavaScript">
 <!--
  function CountDown(){ 
    momentoActual = new Date() 
    //document.write("<br>momentoActual = " + momentoActual)

    anio_final = "<?php echo $anio_final ?>";
    mes_final = "<?php echo $mes_final ?>";
    dia_final = "<?php echo $dia_final ?>";

    hora_final = "<?php echo $hora_final ?>";
    min_final = "<?php echo $min_final ?>";
    seg_final = "<?php echo $seg_final ?>";
 
    final_turno = mes_final + "/" + dia_final + "/" + anio_final + " " + hora_final + ":" + min_final + ":" + seg_final    

    momentoFinal = new Date(final_turno)
    //document.write("<br>momentoFinal = " + momentoFinal)
    
    //document.write("<br>momentoFinal = " + momentoFinal.getTime() )
    //document.write("<br>momentoActual = " + momentoActual.getTime() )
 
    dif_miliseg = momentoFinal.getTime() - momentoActual.getTime()
    //document.write("<br>dif_miliseg = " + dif_miliseg)

    dif_seg_raw = dif_miliseg / 1000
    //document.write("<br>dif_seg_raw = " + dif_seg_raw)
    dif_seg = Math.round(dif_seg_raw)
    //document.write("<br>dif_seg = " + dif_seg)

    countdown_seg = dif_seg % 60
    //document.write("<br>countdown_seg = " + countdown_seg)

    countdown_min = Math.floor(dif_seg / 60) % 60
    //document.write("<br>countdown_min = " + countdown_min)

    countdown_hor = Math.floor(dif_seg / 3600) % 24
    //document.write("<br>countdown_hor = " + countdown_hor)

    
    str_segundo = new String (countdown_seg) 
    if (str_segundo.length == 1) 
       countdown_seg = "0" + countdown_seg

    str_minuto = new String (countdown_min) 
    if (str_minuto.length == 1) 
       countdown_min = "0" + countdown_min 

    str_hora = new String (countdown_hor) 
    if (str_hora.length == 1) 
       countdown_hor = "0" + countdown_hor 

    hora_Imprimible = countdown_hor + " : " + countdown_min + " : " + countdown_seg

    document.form_reloj.cuenta_atras.value = hora_Imprimible 
	
	var timeoutID;

	if (dif_seg > 0)
	    timeoutID = setTimeout(function() { CountDown(); }, 1000);     
	else
	{
	clearTimeout(timeoutID);
	cerrarVentanaLab();
	FinTurno();  //document.forms["form_salida_timeup"].submit();
	}  
  }
  //-->
 </script>
 
 
  <SCRIPT LANGUAGE="JavaScript">
 <!--
  function Semaforo(){ 
     //dif_seg = '<%=remaining_time%>';

	momentoActual = new Date() 
    //document.write("<br>momentoActual = " + momentoActual)

    anio_final = "<?php echo $anio_final ?>";
    mes_final = "<?php echo $mes_final ?>";
    dia_final = "<?php echo $dia_final ?>";

    hora_final = "<?php echo $hora_final ?>";
    min_final = "<?php echo $min_final ?>";
    seg_final = "<?php echo $seg_final ?>";
 
    final_turno = mes_final + "/" + dia_final + "/" + anio_final + " " + hora_final + ":" + min_final + ":" + seg_final    

    momentoFinal = new Date(final_turno)
    //document.write("<br>momentoFinal = " + momentoFinal)
    
    //document.write("<br>momentoFinal = " + momentoFinal.getTime() )
    //document.write("<br>momentoActual = " + momentoActual.getTime() )
 
    dif_miliseg = momentoFinal.getTime() - momentoActual.getTime()
    //document.write("<br>dif_miliseg = " + dif_miliseg)

    dif_seg_raw = dif_miliseg / 1000
    //document.write("<br>dif_seg_raw = " + dif_seg_raw)
    dif_seg = Math.round(dif_seg_raw)
    //document.write("<br>dif_seg = " + dif_seg)
	
	//document.form_reloj.seg_restantes.value = dif_seg;
	
	var imgs = {
        1 : "fotos/round-green.jpeg",
        2 : "fotos/round-light-green.jpeg",
        3 : "fotos/round-yellow.jpeg",
        4 : "fotos/round-orange.jpg",
        5 : "fotos/round-red.jpeg"
    };
	  
  
    if (dif_seg > 900)
    {
	   //document.form_reloj.semaforo.value = 1;
       //document.form_reloj.codigo_color.value = 1;
	   document.getElementById('imagen').src = imgs[1];
	}
    else
    {
       if (dif_seg > 600)
       {
          //document.form_reloj.semaforo.value = 2; 
	      //document.form_reloj.codigo_color.value = 2;
		  document.getElementById('imagen').src = imgs[2];
       }  
       else
       {
          if (dif_seg > 300)
          {
		     //document.form_reloj.semaforo.value = 3; 
	         //document.form_reloj.codigo_color.value = 3;
             document.getElementById('imagen').src = imgs[3];
          }
          else
          {
		     if (dif_seg > 0)
             {
                //document.form_reloj.semaforo.value = 4;
                //document.form_reloj.codigo_color.value = 4;
				//document.getElementById('imagen').src = imgs[4];
				
				if ((dif_seg % 2) == 0)   //numero par
					document.getElementById('imagen').src = imgs[3];
				else                      //numero impar
					document.getElementById('imagen').src = imgs[4];
             }
             else
             {
			    //document.form_reloj.semaforo.value = 5;
				//document.form_reloj.codigo_color.value = 5;
				document.getElementById('imagen').src = imgs[5];
				clearTimeout(timeoutID);
				////FinTurno();
	         }
	      }
	   }
	}
	var timeoutID;
    timeoutID = setTimeout(function() { Semaforo(); }, 1000); 
  }
  //-->
 </script>
 

 <script>
   //Creamos la variable ventana_secundaria (ventana_lab) que contendrá una referencia al popup que vamos a abrir
   //La creamos como variable global para poder acceder a ella desde las distintas funciones 
   var ventana_lab;
   
   function abrirVentanaLab(destino)
   {
    //guardo la referencia de la ventana para poder utilizarla luego
    ventana_lab = window.open(destino,"LAB_REMOTO","top=200,left=120,width=240,height=180,menubar=no,resizable=no, scrollbars=yes,toolbar=no,location=no,directories=no");
	
   } 
   
   function cerrarVentanaLab()
   {
	//la referencia de la ventana es el objeto window del popup. Lo utilizo para acceder al método close
	ventana_lab.close();
   } 
</script>


</head>




<body bgcolor="#<?php echo $bgcolor ?>" onLoad="mueveReloj(); CountDown(); Semaforo();">


  <form name="form_reloj"> 

    <table width="100%">
	
		<col style="width:15%">
		<col style="width:20%">
		<col style="width:20%">
		<col style="width:15%">
		<col style="width:25%">
		<col style="width:5%">
		
		<tr>
		
			<td align="left" style="overflow:hidden; white-space:nowrap;">	<font size="5" face="Tahoma" color="red"><B><u>RemoteLAB</u></B></font>   </td>

			
			<td align="center" style="overflow:hidden; white-space:nowrap;">   <span id="fecha" STYLE="color: grey; font-size: 12pt">  Fecha:&nbsp; </span> <input type="text" name="calendario" value="<?php echo $hoy ?>" size="10" style="background-color : Black; color : White; font-family : Verdana, Arial, Helvetica; font-size : 16pt; text-align : center;" onfocus="window.document.form_reloj.calendario.blur()">   </td>
  

            <td align="center" style="overflow:hidden; white-space:nowrap;">  <span id="hora" STYLE="color: grey; font-size: 12pt"> Hora:&nbsp; </span> <input type="text" name="reloj" size="10" style="background-color : Black; color : White; font-family : Verdana, Arial, Helvetica; font-size : 16pt; text-align : center;" onfocus="window.document.form_reloj.reloj.blur()">   </td>

 
            <td align="center" style="overflow:hidden; white-space:nowrap;">   <font size="4" face="Tahoma" color="blue"><B><u><?php echo $nombre_curso ?></u></B></font>   </td>
 

            <td align="center" style="overflow:hidden; white-space:nowrap;">   <span id="tiempo" STYLE="color: grey; font-size: 12pt"> Tiempo Restante:&nbsp; </span> <input type="text" name="cuenta_atras" size="10" style="background-color : Black; color : White; font-family : Verdana, Arial, Helvetica; font-size : 16pt; text-align : center;" onfocus="window.document.form_reloj.cuenta_atras.blur()">   </td>
        

            <td align="center">    
  
				<?php // convert image to dataURL
				  $img_source = "fotos/round-transparent.png"; // image path/name
				  $img_binary = fread(fopen($img_source, "r"), filesize($img_source));
				  $img_string = base64_encode($img_binary);
				?>

			    <img src="data:image/gif;base64,<?php echo $img_string; ?>" width="36px" height="36px" alt="Time Image" name='imagen' id="imagen" onContextMenu="return(false)">
            
			</td>
        </tr>
	</table>
	
  
  <!-- Codigo para alargar la sesion artificialmente, para evitar el timeout por defecto, de modo que la sesion php dure mientras continue la sesion del lab remoto -->
  <!-- Extend session timeout programmatically: you can make periodical requests from client side, which will keep session alive only if browser is still showing your page.  -->
  <img id="imgSessionAlive" width="1" height="1" onContextMenu="return(false)"/>
  
  <script type="text/javascript" >
   // Helper variable used to prevent caching on some browsers
   var counter;
   counter = 0;

   function KeepSessionAlive() {
    // Increase counter value, so we'll always get unique URL
    counter++;

    // Gets reference of image
    var img = document.getElementById("imgSessionAlive");

    // Set new src value, which will cause request to server, so 
    // session will stay alive
    ////img.src = "http://YourWebSiteUrl.com/RefreshSessionState.aspx?c=" + counter;
	////img.src = "fotos/1x1-pixel.png?c=" + counter;
	img.src = "http://localhost/ztest/lab_titulo.php?c=" + counter;

    // Schedule new call of KeepSessionAlive function after 60 seconds
    setTimeout(KeepSessionAlive, 60000);
   }

   // Run function for a first time
   KeepSessionAlive();
  </script>
  
 </form>   
 

 
 <form name="form_salida_timeup" id="form_salida_timeup" action="finalizar.php" method="post">

   <input type="hidden" name="salida_timeup" id="salida_timeup" value="0">
 </form>
 
 
 <table border="0">
  <tr>
    <td width="20%" valign="top"><h1>POD <?php echo $pod_activo ?></h1>
    </td>

    <td width="60%">
      <center>
	  <!-- <IMG SRC="fotos/fotolab.jpg" ALT="cargando" name="cargando" id="cargando" width="720px" height="540px" onContextMenu="return(false)"> -->
	  <!-- <IMG SRC="fotos/fotolab2.jpg" ALT="cargando" name="cargando" id="cargando" width="720px" height="540px" onContextMenu="return(false)"> -->
	  <IMG SRC="fotos/fotolab3.jpg" ALT="cargando" name="cargando" id="cargando" width="648px" height="486px" onContextMenu="return(false)">
      </center>
    </td>

    <td width="20%" valign="top">  
      <center>
		<br>
        <INPUT type="button" name="reload" value="RELOAD" face="algerian" size="5" style="background-color : #555555; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:120px;" onClick="location.reload(true);" />
		<br><br><br>
	    <INPUT type="button" name="volver" value="VOLVER" face="algerian" size="5" style="background-color : #555555; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:120px;" onClick="location.href='reservas.php';" />
		<br><br><br>
		
<?php
		///////CODIGO para esperar unos segundos antes de llamar a los pods 2 y 3///////////////////////////////////////// 
		///////// y asi dar tiempo a que POD2 y POD3 arranquen y carguen las maquinas virtuales que forman el pod ////////
			 
		if($pod_activo > 1){
			$tiempo=75;
			//echo("Estableciendo conexi&oacute;n con RemoteLab...<br><br>");
?>
			  <p><span id="banner" STYLE="background-color: #ffffcc; color: green; font-size: 14pt; font-weight: bold">Estableciendo conexi&oacute;n <br>con RemoteLab ...</span></p>
			  
			  <form name="form_waiting">
				 <input type="text" name="waiting" size="3" style="background-color : Black; color : White; font-family : Verdana, Arial, Helvetica; font-size : 16pt; text-align : center;" onfocus="window.document.form_waiting.waiting.blur()">
<?php  
				for ($i=$tiempo; $i>=0; $i--){
					if ($i<=9)
						$j = '0'.$i;
					else
						$j = $i;
						
					//echo $j;
					flush();
					ob_flush();
?>
					<script LANGUAGE="JavaScript">
						document.form_waiting.waiting.value = <?php echo $j ?>
					</script>
	
<?php
					sleep(1);
				}
?>
			</form>
<?php
		}
?>

	  </center>
    </td>
  </tr>
</table>

	<script>
	   //se muestra el escritorio del XPmanager del pod activo --> Muestra una nueva ventana con VNC (escritorio remoto) para controlar todos los dispositivos del POD  
	   var pod_activo = "<?php echo $pod_activo ?>";
	   var X = Math.floor((pod_activo/10)+1);
	   var Y = pod_activo - (X-1)*10;
	   var Z = 0;
	   var destino = "http://192.168." + X + Y + Z + ".252:58" + (X-1) + Y;           ////asi soporta hasta 15 pods -> 110-250
	   //var destino = "http://192.168.1" + pod_activo + "0.252:580" + pod_activo;      ////asi soporta hasta 9 pods  -> 110-190
	   //document.write("destino = " + destino);   <<<<<<<-----<<<<<<--------<<<<<<-------<<<<<<--------- este enlace nos abre la conexion con el pod remoto !!!!!
	  
	   //asi se abre una ventana PopUp con el lab remoto, que se cerrara en la funcion CountDown cuando llegue a cero.
	   abrirVentanaLab(destino);    
	</script>
		
</body>
</html>


<?php
	//Limpiamos y cerramos
	mysql_close($conexion);
    //ob_end_flush();
?>