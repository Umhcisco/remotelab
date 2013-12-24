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
	 <title>LISTADO CURSOS ACTIVOS</title>
 	 <!-- Estas lineas evitan que la pagina deseada no se cargue en la cache y no pueda ser mostrada cuando se regresa atras. -->
	  <meta http-equiv="Expires" CONTENT="0">
	  <meta http-equiv="Cache-Control" CONTENT="no-cache">
	  <meta http-equiv="Pragma" CONTENT="no-cache">
	
	</head>


	<body bgcolor="#<?php echo $bgcolor ?>">
 
 
<?php
	//comprobamos la opcion de MODO: bien BASICO, que muestra solo los nombres de usuario, o bien DETALLES, que muestra el resto de datos de cada usuario (el nombre y apellidos del alumno, su email y su numero de id en el sistema)
	
   if (!isset($_GET['catID']))
      $catNum = 0;
   else
      $catNum = $_GET['catID'];
               
	   
	//si queremos incluir todos los detalles de los usuarios en el listado: modo DETALLES
	if ($catNum==2)
	{	
		//consulta --> Listado de Alumnos en Cursos Activos
		$hoy = date('Y-m-d');
		
		$sql1="SELECT * FROM cursos WHERE curso_activo = 1 AND fin_curso >= CURDATE() AND nombre_curso LIKE '%Experto%'";
		$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el Select (sql1) ".mysql_error());
		$count1=mysql_num_rows($registros1);       //numero de cursos activos expertos
		//echo "Num.cursos activos = $count1";
		
		$sql11="SELECT * FROM cursos WHERE curso_activo = 1 AND fin_curso >= CURDATE() AND nombre_curso NOT LIKE '%Experto%'";
		$registros11=mysql_query($sql11,$conexion) or die ("Problemas con el Select (sql11) ".mysql_error());
		$count11=mysql_num_rows($registros11);       //numero de cursos activos no expertos
		//echo "Num.cursos activos = $count11";
		
		$count0 = $count1 + $count11;
?>

        <input type="text" name="totalActivos" value="<?php echo " Num.cursos activos = $count0"?>" 
 		id="totalActivos" style="width: 180px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  


<?php	
		if ($count0 > 0)
		{		
			if ($count1 > 0)
			{
				while ($reg1 = mysql_fetch_array($registros1))
				{	
					$nombre_curso = $reg1['nombre_curso'];
					$curso_id = $reg1['curso_id'];
					$fecha_inicio = $reg1['inicio_curso'];
					$fecha_fin = $reg1['fin_curso'];
					echo "<center><U><font style=\"width:420; background: #99ff66; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:14pt; font-weight:bold;  border: 1px solid #ccc;\" readonly> $nombre_curso </font></U></center>";
					echo "<p>";
					//echo "<center><U><H3> $nombre_curso </H3></U></center>";
					
					
					$sql2 = "SELECT username FROM usuarios WHERE user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id = $curso_id  ORDER BY user_id ) ORDER BY user_id";
					$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el Select (sql2) ".mysql_error());
					$count2=mysql_num_rows($registros2);       //numero de usuarios en este curso  	
					
					echo "<center>";
					echo "    <table border=1 cellpadding=10 cellspacing=0>";
					echo "	    <tr>";
						
					
					// <!--  ******
					//si queremos incluir datos personales, usamos esta consulta
					$sql3 = "SELECT * FROM datos_pers WHERE user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id = $curso_id  ORDER BY user_id ) ORDER BY user_id";
					$registros3=mysql_query($sql3,$conexion) or die ("Problemas con el Select (sql3) ".mysql_error());
					$count3=mysql_num_rows($registros3);       //numero de usuarios en este curso
					
					if ($count3 > 0)
					{
						$campos=mysql_num_fields($registros3);    //numero de columnas de la tabla
						for ($i=1; $i<4; $i++)           //Titulos de las columnas
						{
							$titulo = mysql_field_name($registros3, $i);
							echo "<th bgcolor=\"khaki\"> $titulo </th>";
						}					
						echo "<th bgcolor=\"khaki\"> usuario </th>";
						echo "     </tr>";	
						
						while ($reg2 = mysql_fetch_array($registros2))   //filas de la tabla
						{			
							echo "     <tr>";
							
							$reg3 = mysql_fetch_array($registros3);
							for ($j=1; $j<4; $j++)       //columnas de cada fila
							{
								$dato = $reg3[$j];
								echo "<td bgcolor=\"#fcfcfc\"> $dato </td>";
							}
							
							$alumno = $reg2['username'];
							//echo "<br><center>$alumno</center>";	
							echo "<td bgcolor=\"#fcfcfc\"> $alumno </td>";
							
							echo "     </tr>";			
						}	
					}
					// --> *****
					
					//si solo queremos incluir el nombre de usuario en el listado
					else
					{		
						echo "<th bgcolor=\"khaki\">  usuario  </th>";
						echo "     </tr>";
						
						while ($reg2 = mysql_fetch_array($registros2))
						{			
							echo "     <tr>";
							$alumno = $reg2['username'];
							//echo "<br><center>$alumno</center>";	
							echo "<td bgcolor=\"#fcfcfc\"> $alumno </td>";
							echo "     </tr>";				
						}			
					}
					
					echo "</table></center>";
					//echo "<div style=\"text-align:left;\">Num.usuarios en el curso $nombre_curso = $count2</div>";
					

					///distinguimos entre cursos pendientes de iniciar o cursos ya en ejecucion
					$sql4 = "SELECT * FROM cursos WHERE curso_id=$curso_id AND inicio_curso <= '$hoy' AND fin_curso >= '$hoy' AND curso_activo=1";
					$registros4=mysql_query($sql4,$conexion) or die ("Problemas con el Select (sql4) ".mysql_error());
					$count4=mysql_num_rows($registros4);       //numero de cursos activos 	
					
					$array_inicio = explode("-", $fecha_inicio);
					$array_fin = explode("-", $fecha_fin);
					
					if ($count4 >0)
						echo "<U><font style=\"width:420; background: #ccffcc ; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold;  border: 1px solid #ccc;\" readonly> curso en ejecuci&oacute;n </font></U>";
					else
						echo "<U><font style=\"width:420; background: #ccffcc ; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold;  border: 1px solid #ccc;\" readonly> pendiente de inicio </font></U>";
					//echo "<br>curso_id = $curso_id --- count4 = $count4";
					
					echo "<p>";
					echo "<U><font style=\"width:420; background: #e0b2b2; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold;  border: 1px solid #ccc;\" readonly> Inicio: $array_inicio[2]/$array_inicio[1]/$array_inicio[0] </font></U>";
					echo "&nbsp;&nbsp;&nbsp;&nbsp;";
					echo "<U><font style=\"width:420; background: #e0b2b2; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold;  border: 1px solid #ccc;\" readonly> Inicio: $array_fin[2]/$array_fin[1]/$array_fin[0] </font></U>";		
					
?>
					<p>
					<input type="text" name="totalCurso" value="<?php echo " Num.usuarios en el curso $nombre_curso = $count2"?>" 
					id="totalCurso" style="width: 380px; background: lightblue; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  
					<br>			   
<?php
				}
				echo "<hr>";
				
				mysql_free_result($registros2);
				mysql_free_result($registros3);
				mysql_free_result($registros4);
			}

			if ($count11 > 0)
			{
				while ($reg11 = mysql_fetch_array($registros11))
				{	
					$nombre_curso = $reg11['nombre_curso'];
					$curso_id = $reg11['curso_id'];
					$fecha_inicio = $reg11['inicio_curso'];
					$fecha_fin = $reg11['fin_curso'];
					echo "<center><U><font style=\"width:420; background: #99ff66; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:14pt; font-weight:bold;  border: 1px solid #ccc;\" readonly> $nombre_curso </font></U></center>";
					echo "<p>";
					//echo "<center><U><H3> $nombre_curso </H3></U></center>";
					
					
					$sql2 = "SELECT username FROM usuarios WHERE user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id = $curso_id  ORDER BY user_id ) ORDER BY user_id";
					$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el Select (sql2) ".mysql_error());
					$count2=mysql_num_rows($registros2);       //numero de usuarios en este curso  	
					
					echo "<center>";
					echo "    <table border=1 cellpadding=10 cellspacing=0>";
					echo "	    <tr>";
						
					
					// <!--  ******
					//si queremos incluir datos personales, usamos esta consulta
					$sql3 = "SELECT * FROM datos_pers WHERE user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id = $curso_id  ORDER BY user_id ) ORDER BY user_id";
					$registros3=mysql_query($sql3,$conexion) or die ("Problemas con el Select (sql3) ".mysql_error());
					$count3=mysql_num_rows($registros3);       //numero de usuarios en este curso
					
					if ($count3 > 0)
					{
						$campos=mysql_num_fields($registros3);    //numero de columnas de la tabla
						for ($i=1; $i<4; $i++)           //Titulos de las columnas
						{
							$titulo = mysql_field_name($registros3, $i);
							echo "<th bgcolor=\"khaki\"> $titulo </th>";
						}
						
						echo "<th bgcolor=\"khaki\"> usuario </th>";
						echo "     </tr>";	
						
						while ($reg2 = mysql_fetch_array($registros2))   //filas de la tabla
						{			
							echo "     <tr>";
							
							$reg3 = mysql_fetch_array($registros3);
							for ($j=1; $j<4; $j++)       //columnas de cada fila
							{
								$dato = $reg3[$j];
								echo "<td bgcolor=\"#fcfcfc\"> $dato </td>";
							}

							$alumno = $reg2['username'];
							//echo "<br><center>$alumno</center>";	
							echo "<td bgcolor=\"#fcfcfc\"> $alumno </td>";
							
							echo "     </tr>";			
						}	
					}
					// --> *****
					
					//si solo queremos incluir el nombre de usuario en el listado
					else
					{		
						echo "<th bgcolor=\"khaki\">  usuario  </th>";
						echo "     </tr>";
						
						while ($reg2 = mysql_fetch_array($registros2))
						{			
							echo "     <tr>";
							$alumno = $reg2['username'];
							//echo "<br><center>$alumno</center>";	
							echo "<td bgcolor=\"#fcfcfc\"> $alumno </td>";
							echo "     </tr>";				
						}			
					}
					
					echo "</table></center>";
					//echo "<div style=\"text-align:left;\">Num.usuarios en el curso $nombre_curso = $count2</div>";
					

					///distinguimos entre cursos pendientes de iniciar o cursos ya en ejecucion
					$sql4 = "SELECT * FROM cursos WHERE curso_id=$curso_id AND inicio_curso <= '$hoy' AND fin_curso >= '$hoy' AND curso_activo=1";
					$registros4=mysql_query($sql4,$conexion) or die ("Problemas con el Select (sql4) ".mysql_error());
					$count4=mysql_num_rows($registros4);       //numero de cursos activos 	
					
					$array_inicio = explode("-", $fecha_inicio);
					$array_fin = explode("-", $fecha_fin);
					
					if ($count4 >0)
						echo "<U><font style=\"width:420; background: #ccffcc ; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold;  border: 1px solid #ccc;\" readonly> curso en ejecuci&oacute;n </font></U>";
					else
						echo "<U><font style=\"width:420; background: #ccffcc ; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold;  border: 1px solid #ccc;\" readonly> pendiente de inicio </font></U>";
					//echo "<br>curso_id = $curso_id --- count4 = $count4";
					
					echo "<p>";
					echo "<U><font style=\"width:420; background: #e0b2b2; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold;  border: 1px solid #ccc;\" readonly> Inicio: $array_inicio[2]/$array_inicio[1]/$array_inicio[0] </font></U>";
					echo "&nbsp;&nbsp;&nbsp;&nbsp;";
					echo "<U><font style=\"width:420; background: #e0b2b2; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold;  border: 1px solid #ccc;\" readonly> Fin: $array_fin[2]/$array_fin[1]/$array_fin[0] </font></U>";		
					
?>
					<p>
					<input type="text" name="totalCurso" value="<?php echo " Num.usuarios en el curso $nombre_curso = $count2"?>" 
					id="totalCurso" style="width: 390px; background: lightblue; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  
					<br>			   
<?php
				}
				echo "<hr>";
				
				mysql_free_result($registros2);
				mysql_free_result($registros3);
				mysql_free_result($registros4);
			}
				
		}else{
			//echo "<br>No hay cursos activos en este momento<br>";
?>
			<p> 
			<center> <input type="text" name="totalCurso" value="<?php echo " No hay cursos activos en este momento"?>" 
			id="totalCurso" style="width: 380px; background: lightblue; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly> </center>	  
			<br><br>
			   
<?php
		}	
		
		mysql_free_result($registros1);
		mysql_free_result($registros11);
		
	}
	
	
	//si solo queremos incluir el nombre de usuario en el listado: modo BASICO
	else
	{			
		//consulta --> Listado de Alumnos en Cursos Activos
		$hoy = date('Y-m-d');
		
		$sql1="SELECT * FROM cursos WHERE curso_activo = 1 AND fin_curso >= CURDATE() AND nombre_curso LIKE '%Experto%'";
		$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el Select (sql1) ".mysql_error());
		$count1=mysql_num_rows($registros1);       //numero de cursos activos expertos
		//echo "Num.cursos activos = $count1";
		
		$sql11="SELECT * FROM cursos WHERE curso_activo = 1 AND fin_curso >= CURDATE() AND nombre_curso NOT LIKE '%Experto%'";
		$registros11=mysql_query($sql11,$conexion) or die ("Problemas con el Select (sql11) ".mysql_error());
		$count11=mysql_num_rows($registros11);       //numero de cursos activos no expertos
		//echo "Num.cursos activos = $count11";
		
		$count0 = $count1 + $count11;
?>
        
        <input type="text" name="totalActivos" value="<?php echo " Num.cursos activos = $count0"?>" 
 		id="totalActivos" style="width: 180px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  


<?php	
		if ($count0 > 0)
		{
			if ($count1 > 0)
			{
				while ($reg1 = mysql_fetch_array($registros1))
				{	
					$nombre_curso = $reg1['nombre_curso'];
					$curso_id = $reg1['curso_id'];
					$fecha_inicio = $reg1['inicio_curso'];
					$fecha_fin = $reg1['fin_curso'];
					echo "<center><U><font style=\"width:420; background: #99ff66; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:14pt; font-weight:bold;  border: 1px solid #ccc;\" readonly> $nombre_curso </font></U></center>";
					echo "<p>";
					//echo "<center><U><H3> $nombre_curso </H3></U></center>";
										
					$sql2 = "SELECT username FROM usuarios WHERE user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id = $curso_id  ORDER BY user_id ) ORDER BY user_id";
					$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el Select (sql2) ".mysql_error());
					$count2=mysql_num_rows($registros2);       //numero de usuarios en este curso  	
					
					echo "<center>";
					echo "    <table border=1 cellpadding=10 cellspacing=0>";
					echo "	    <tr>";
									
					echo "<th bgcolor=\"khaki\">  usuario  </th>";
					echo "     </tr>";
					
					while ($reg2 = mysql_fetch_array($registros2))
					{			
						echo "     <tr>";
						$alumno = $reg2['username'];
						//echo "<br><center>$alumno</center>";	
						echo "<td bgcolor=\"#fcfcfc\"> $alumno </td>";
						echo "     </tr>";				
					}			
					
					echo "</table></center>";
					//echo "<div style=\"text-align:left;\">Num.usuarios en el curso $nombre_curso = $count2</div>";

					
					///distinguimos entre cursos pendientes de iniciar o cursos ya en ejecucion
					$sql4 = "SELECT * FROM cursos WHERE curso_id=$curso_id AND inicio_curso <= '$hoy' AND fin_curso >= '$hoy' AND curso_activo=1";
					$registros4=mysql_query($sql4,$conexion) or die ("Problemas con el Select (sql4) ".mysql_error());
					$count4=mysql_num_rows($registros4);       //numero de cursos activos 	
					
					$array_inicio = explode("-", $fecha_inicio);
					$array_fin = explode("-", $fecha_fin);
					
					if ($count4 >0)
						echo "<U><font style=\"width:420; background: #ccffcc ; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold;  border: 1px solid #ccc;\" readonly> curso en ejecuci&oacute;n </font></U>";
					else
						echo "<U><font style=\"width:420; background: #ccffcc ; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold;  border: 1px solid #ccc;\" readonly> pendiente de inicio </font></U>";
					//echo "<br>curso_id = $curso_id --- count4 = $count4";
					
					echo "<p>";
					echo "<U><font style=\"width:420; background: #e0b2b2; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold;  border: 1px solid #ccc;\" readonly> Inicio: $array_inicio[2]/$array_inicio[1]/$array_inicio[0] </font></U>";
					echo "&nbsp;&nbsp;&nbsp;&nbsp;";
					echo "<U><font style=\"width:420; background: #e0b2b2; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold;  border: 1px solid #ccc;\" readonly> Fin: $array_fin[2]/$array_fin[1]/$array_fin[0] </font></U>";		
					
?>
					<p>
					<input type="text" name="totalCurso" value="<?php echo " Num.usuarios en el curso $nombre_curso = $count2"?>" 
					id="totalCurso" style="width: 380px; background: lightblue; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  
					<br>			   
<?php
					echo "<hr>";
				}
				
				mysql_free_result($registros2);
				mysql_free_result($registros4);								
			}

			if ($count11 > 0)
			{
				while ($reg11 = mysql_fetch_array($registros11))
				{	
					$nombre_curso = $reg11['nombre_curso'];
					$curso_id = $reg11['curso_id'];
					$fecha_inicio = $reg11['inicio_curso'];
					$fecha_fin = $reg11['fin_curso'];
					echo "<center><U><font style=\"width:420; background: #99ff66; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:14pt; font-weight:bold;  border: 1px solid #ccc;\" readonly> $nombre_curso </font></U></center>";
					echo "<p>";
					//echo "<center><U><H3> $nombre_curso </H3></U></center>";
										
					$sql2 = "SELECT username FROM usuarios WHERE user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id = $curso_id  ORDER BY user_id ) ORDER BY user_id";
					$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el Select (sql2) ".mysql_error());
					$count2=mysql_num_rows($registros2);       //numero de usuarios en este curso  	
					
					echo "<center>";
					echo "    <table border=1 cellpadding=10 cellspacing=0>";
					echo "	    <tr>";
									
					echo "<th bgcolor=\"khaki\">  usuario  </th>";
					echo "     </tr>";
					
					while ($reg2 = mysql_fetch_array($registros2))
					{			
						echo "     <tr>";
						$alumno = $reg2['username'];
						//echo "<br><center>$alumno</center>";	
						echo "<td bgcolor=\"#fcfcfc\"> $alumno </td>";
						echo "     </tr>";				
					}			
					
					echo "</table></center>";
					//echo "<div style=\"text-align:left;\">Num.usuarios en el curso $nombre_curso = $count2</div>";

					
					///distinguimos entre cursos pendientes de iniciar o cursos ya en ejecucion
					$sql4 = "SELECT * FROM cursos WHERE curso_id=$curso_id AND inicio_curso <= '$hoy' AND fin_curso >= '$hoy' AND curso_activo=1";
					$registros4=mysql_query($sql4,$conexion) or die ("Problemas con el Select (sql4) ".mysql_error());
					$count4=mysql_num_rows($registros4);       //numero de cursos activos 	
					
					$array_inicio = explode("-", $fecha_inicio);
					$array_fin = explode("-", $fecha_fin);
					
					if ($count4 >0)
						echo "<U><font style=\"width:420; background: #ccffcc ; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold;  border: 1px solid #ccc;\" readonly> curso en ejecuci&oacute;n </font></U>";
					else
						echo "<U><font style=\"width:420; background: #ccffcc ; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold;  border: 1px solid #ccc;\" readonly> pendiente de inicio </font></U>";
					//echo "<br>curso_id = $curso_id --- count4 = $count4";
					
					echo "<p>";
					echo "<U><font style=\"width:420; background: #e0b2b2; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold;  border: 1px solid #ccc;\" readonly> Inicio: $array_inicio[2]/$array_inicio[1]/$array_inicio[0] </font></U>";
					echo "&nbsp;&nbsp;&nbsp;&nbsp;";
					echo "<U><font style=\"width:420; background: #e0b2b2; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold;  border: 1px solid #ccc;\" readonly> Fin: $array_fin[2]/$array_fin[1]/$array_fin[0] </font></U>";		
					
?>
					<p>
					<input type="text" name="totalCurso" value="<?php echo " Num.usuarios en el curso $nombre_curso = $count2"?>" 
					id="totalCurso" style="width: 390px; background: lightblue; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  
					<br>			   
<?php
					echo "<hr>";
				}
				
				mysql_free_result($registros2);
				mysql_free_result($registros4);								
			}			

		}else{
			//echo "<br>No hay cursos activos en este momento<br><br>";
?>
			<br>
			<input type="text" name="totalCurso" value="<?php echo " No hay cursos activos en este momento"?>" 
			id="totalCurso" style="width: 380px; background: lightblue; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  
			<br><br>
			   
<?php
		}

	mysql_free_result($registros1);
	mysql_free_result($registros11);
		
	}
?>
 
 
 
 	<form name="catSelect" action="" method="GET">
      <div align="center">
	    <LABEL for="modos" ALIGN="right" style="font-size:12pt" style="font-weight:bold" style="font-family: Verdana, Arial, Helvetica, sans-serif">MODO: &nbsp;&nbsp;</LABEL>
	    <SELECT name="catID" id="catID" style="width:200px; align=center; font-size:11pt; font-weight:bold; background:#ffffff;" onChange="catSelect.submit();"> 
		   <!-- <option id="encabezado" value="0">Elige opci&oacute;n</option> -->
	       <option id="basico" value="1" >nombre de Usuario</option>
		   <option id="detalles" value="2" <?php if ($catNum == 2) echo "selected"; ?> >todos los Detalles</option>
		</SELECT>
	  </div>
	</form>
	
	  
	  	  
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

	mysql_close($conexion);
	ob_end_flush();		
}
?>