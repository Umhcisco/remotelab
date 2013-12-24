<?php 
 include ("headers/seguridad.php");
 include ("headers/conexion.php");

//iniciamos el buffer de salida 
ob_start();

$admin = $_SESSION['admin'];
$idle_timeout = $_SESSION['idle_timeout'];
$total_pods = $_SESSION['total_pods'];
$f_ahora = date('Y-m-d H:i:s');
$hoy = date('Y-m-d');

//numero de elementos mostrados en el apartado "Ultimas reservas"
$sql0="SELECT * FROM parametros_basicos";
$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el Select  (sql0) ".mysql_error());

while ($reg0=mysql_fetch_array($registros0)){
	$num_resv_activas = $reg0['estadist_num_resv_activas'];
	$num_resv_ejecutadas = $reg0['estadist_num_resv_ejecutadas'];
	$num_resv_canceladas = $reg0['estadist_num_resv_canceladas'];
}
mysql_free_result($registros0);


//Se comprueba si el usuario tiene privilegios de administrador
if ($_SESSION['admin'] == 0) { 
     session_destroy();
    //si no tiene privilegios de administrador, se devuelve a la pagina inicial 
    header("Location: index.php?errorusuario=ad"); 
} else { 

	// ****** VERIFICAMOS LOS NUMEROS DE POD SELECCIONADOS PARA LOS TURNOS DE mantenimiento ******
	// el turno 0 implicara que todos los pods entraran en mantenimiento para el intervalo considerado
	
	$stat_pods = $_POST['Xstat_pods']; 
	
	for ($i=0; $i<=$total_pods; $i++){
		$str1 = "pod".$i;
		$string = "Xpod".$i;
		$mi_pod[$i] = $_POST[$string];
		//echo "<br><br>str1 = $str1; string = $string; mi_pod[$i] = $mi_pod[$i]<br><br>";
	}


	$stat_cursos = $_POST['Xstat_cursos']; 	
	$estadoID = $_POST['XestadoID'];
	$cursoID = $_POST['XcursoID'];

	$stat_usuarios = $_POST['Xstat_usuarios']; 
	$usuarioID = $_POST['XusuarioID'];
	
	$stat_tiempos = $_POST['Xstat_tiempos']; 
	$tiempoID = $_POST['XtiempoID'];
	
	$stat_reservas = $_POST['Xstat_reservas']; 
	$ultimasreservasID = $_POST['XultimasReservasID'];

	$stat_admins = $_POST['Xstat_admins']; 
	$adminsID = $_POST['XadminsID'];
	
	$stat_totales = $_POST['Xstat_totales']; 
			

	$flag_parametros = 0;
	//echo "pod0 = $pod0";
	
	if ($mi_pod[0]=="0" || $mi_pod[1]==1 || $mi_pod[2]==2 || $mi_pod[3]==3 || $mi_pod[4]==4) 
	    $flag_parametros = 1;
	if  ($stat_cursos==1 || $stat_usuarios==1 || $stat_tiempos==1 || $stat_reservas==1 || $stat_admins==1 || $stat_totales == 1)
	    $flag_parametros = 1;
	//echo "<br>flag_parametros = $flag_parametros";
	
	
	//echo "<br>stat_pods = $stat_pods *** mi_pod[0] = $mi_pod[0] -- mi_pod[1] = $mi_pod[1] -- mi_pod[2] = $mi_pod[2] -- mi_pod[3] = $mi_pod[3] -- mi_pod[4] = $mi_pod[4]";
    //echo "<br>stat_cursos = $stat_cursos *** estadoID = $estadoID -- cursoID = $cursoID";
	//echo "<br>stat_usuarios = $stat_usuarios *** usuarioID = $usuarioID";
	//echo "<br>stat_tiempos = $stat_tiempos *** tiempoID = $tiempoID";
	//echo "<br>stat_reservas = $stat_reservas *** ultimasreservasID = $ultimasreservasID";
	//echo "<br>stat_admins = $stat_admins *** adminsID = $adminsID";
	//echo "<br>stat_totales = $stat_totales";
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
	 <title>ESTADISTICAS2</title>
	 <!-- Estas lineas evitan que la pagina deseada no se cargue en la cache y no pueda ser mostrada cuando se regresa atras. -->
	  <meta http-equiv="Expires" CONTENT="0">
	  <meta http-equiv="Cache-Control" CONTENT="no-cache">
	  <meta http-equiv="Pragma" CONTENT="no-cache">
	  
	  <script src="src/dragtable.js"></script>
      <script src="src/sorttable.js"></script>
	</head>
	
	<body bgcolor="#<?php echo $bgcolor ?>">
		<center><u><h1>ESTADISTICAS</h1></u></center>      


	
<?php	  
	//almacenamos los pods elegidos en el array $pods
	if ($stat_pods == 1)
	{
	    //echo "<br>MARCADO PODS";
	    $cont = 0;
	   
	    if ($mi_pod[0] == "0")
		{
		    //echo "<br>mi_pod[0] = $mi_pod[0]";
			for ($i=1; $i<=$total_pods; $i++)
			{
			    $pods[$cont] = $i;
				//echo "<br>pods[$cont] = $pods[$cont]";
			    $cont++;  
		    }
	   }else{
			for ($i=1; $i<=$total_pods; $i++){
			
				if ($mi_pod[$i] == "$i")
				{
					$pods[$cont] = $i;
					//echo "<br>pods[$cont] = $pods[$cont]";
					$cont++;
				}
			}
	   }
	   
	   //if (isset($pods))
	   //   echo "<br>existe el array pods";
	   //else
	   //   echo "<br>NO existe el array pods";

	}else{ ;
	   //echo "<br>DESMARCADO PODS";
	}
	
	
	//almacenamos los cursos elegidos en el array $cursos
	if ($stat_cursos == 1)
	{
		//echo "<br>estadoID = $estadoID";
		//echo "<br>cursoID = $cursoID";
		
		if ($cursoID == 0)
		{
			if ($estadoID == 0)
			{
				///primero van los cursos de experto
				$sql0 = "SELECT curso_id, nombre_curso FROM cursos WHERE nombre_curso LIKE '%Experto%' AND num_max_pods > 0";
				$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el Select (sql0) ".mysql_error());
				$count0=mysql_num_rows($registros0);       //numero de cursos total
				//echo "<br>Num.cursos total = $count0";
				
				$i = 0;
				if ($count0 > 0)
				{
					while ($reg0 = mysql_fetch_array($registros0))
					{
						$cursos[$i] =  $reg0['curso_id'];
						$nombre_cursos[$i] = $reg0['nombre_curso'];
						//echo "<br>cursos[$i] = $cursos[$i]";
						$i++;
					}	  
				}		  
				$num_experto=$i;
				mysql_free_result($registros0);

				
				///despues van los cursos NO experto
				$sql0 = "SELECT curso_id, nombre_curso FROM cursos WHERE nombre_curso NOT LIKE '%Experto%' AND num_max_pods > 0";
				$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el Select (sql0) ".mysql_error());
				$count0=mysql_num_rows($registros0);       //numero de cursos total
				//echo "<br>Num.cursos total = $count0";

				if ($count0 > 0)
				{
					while ($reg0 = mysql_fetch_array($registros0))
					{
						$cursos[$i] =  $reg0['curso_id'];
						$nombre_cursos[$i] = $reg0['nombre_curso'];
						//echo "<br>cursos[$i] = $cursos[$i]";
						$i++;
					}	  
				}		
				$num_no_experto=$i-$num_experto;
				mysql_free_result($registros0);	

				
				///finalmente van los cursos de control
				$sql0 = "SELECT curso_id, nombre_curso FROM cursos WHERE num_max_pods = 0";
				$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el Select (sql0) ".mysql_error());
				$count0=mysql_num_rows($registros0);       //numero de cursos total
				//echo "<br>Num.cursos total = $count0";

				if ($count0 > 0)
				{
					while ($reg0 = mysql_fetch_array($registros0))
					{
						$cursos[$i] =  $reg0['curso_id'];
						$nombre_cursos[$i] = $reg0['nombre_curso'];
						//echo "<br>cursos[$i] = $cursos[$i]";
						$i++;
					}	  
				}		  
				$num_control=$i-$num_experto-$num_no_experto;
				mysql_free_result($registros0);				
			}
			else if ($estadoID == 1)
			{
				///primero van los cursos de experto
				$sql0 = "SELECT curso_id, nombre_curso FROM cursos WHERE nombre_curso LIKE '%Experto%' AND num_max_pods > 0 AND fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1";
				$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el Select (sql0) ".mysql_error());
				$count0=mysql_num_rows($registros0);       //numero de cursos total
				//echo "<br>Num.cursos total = $count0";
				
				$i = 0;
				if ($count0 > 0)
				{
					while ($reg0 = mysql_fetch_array($registros0))
					{
						$cursos[$i] =  $reg0['curso_id'];
						$nombre_cursos[$i] = $reg0['nombre_curso'];
						//echo "<br>cursos[$i] = $cursos[$i]";
						$i++;
					}	  
				}		  
				mysql_free_result($registros0);

				
				///despues van los cursos NO experto
				$sql0 = "SELECT curso_id, nombre_curso FROM cursos WHERE nombre_curso NOT LIKE '%Experto%' AND num_max_pods > 0 AND fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1";
				$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el Select (sql0) ".mysql_error());
				$count0=mysql_num_rows($registros0);       //numero de cursos total
				//echo "<br>Num.cursos total = $count0";

				if ($count0 > 0)
				{
					while ($reg0 = mysql_fetch_array($registros0))
					{
						$cursos[$i] =  $reg0['curso_id'];
						$nombre_cursos[$i] = $reg0['nombre_curso'];
						//echo "<br>cursos[$i] = $cursos[$i]";
						$i++;
					}	  
				}		  
				mysql_free_result($registros0);	

				
				///finalmente van los cursos de control
				$sql0 = "SELECT curso_id, nombre_curso FROM cursos WHERE num_max_pods = 0 AND fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1";
				$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el Select (sql0) ".mysql_error());
				$count0=mysql_num_rows($registros0);       //numero de cursos total
				//echo "<br>Num.cursos total = $count0";

				if ($count0 > 0)
				{
					while ($reg0 = mysql_fetch_array($registros0))
					{
						$cursos[$i] =  $reg0['curso_id'];
						$nombre_cursos[$i] = $reg0['nombre_curso'];
						//echo "<br>cursos[$i] = $cursos[$i]";
						$i++;
					}	  
				}		  
				mysql_free_result($registros0);	
			}
			else   //($estadoID == 2)
			{
				///primero van los cursos de experto
				$sql0 = "SELECT curso_id, nombre_curso FROM cursos WHERE nombre_curso LIKE '%Experto%' AND num_max_pods > 0  AND (fin_curso < '$hoy' OR curso_activo = 0)";
				$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el Select (sql0) ".mysql_error());
				$count0=mysql_num_rows($registros0);       //numero de cursos total
				//echo "<br>Num.cursos total = $count0";
				
				$i = 0;
				if ($count0 > 0)
				{
					while ($reg0 = mysql_fetch_array($registros0))
					{
						$cursos[$i] =  $reg0['curso_id'];
						$nombre_cursos[$i] = $reg0['nombre_curso'];
						//echo "<br>cursos[$i] = $cursos[$i]";
						$i++;
					}	  
				}		  
				mysql_free_result($registros0);

				
				///despues van los cursos NO experto
				$sql0 = "SELECT curso_id, nombre_curso FROM cursos WHERE nombre_curso NOT LIKE '%Experto%' AND num_max_pods > 0 AND (fin_curso < '$hoy' OR curso_activo = 0)";
				$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el Select (sql0) ".mysql_error());
				$count0=mysql_num_rows($registros0);       //numero de cursos total
				//echo "<br>Num.cursos total = $count0";

				if ($count0 > 0)
				{
					while ($reg0 = mysql_fetch_array($registros0))
					{
						$cursos[$i] =  $reg0['curso_id'];
						$nombre_cursos[$i] = $reg0['nombre_curso'];
						//echo "<br>cursos[$i] = $cursos[$i]";
						$i++;
					}	  
				}		  
				mysql_free_result($registros0);	

				
				///finalmente van los cursos de control
				$sql0 = "SELECT curso_id, nombre_curso FROM cursos WHERE num_max_pods = 0 AND (fin_curso < '$hoy' OR curso_activo = 0)";
				$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el Select (sql0) ".mysql_error());
				$count0=mysql_num_rows($registros0);       //numero de cursos total
				//echo "<br>Num.cursos total = $count0";

				if ($count0 > 0)
				{
					while ($reg0 = mysql_fetch_array($registros0))
					{
						$cursos[$i] =  $reg0['curso_id'];
						$nombre_cursos[$i] = $reg0['nombre_curso'];
						//echo "<br>cursos[$i] = $cursos[$i]";
						$i++;
					}	  
				}		  
				mysql_free_result($registros0);	
			}
		}
		else
		{
			$cursos[0] = $cursoID;
			
			$sql3 = "SELECT nombre_curso FROM cursos WHERE curso_id = $cursoID";
			$registros3=mysql_query($sql3,$conexion) or die ("Problemas con el Select (sql3) ".mysql_error());
			$count3=mysql_num_rows($registros3);       //numero de cursos seleccionados: debe ser solo 1

            if ($reg3 = mysql_fetch_array($registros3) )
			{
				$nombre_cursos[0] = $reg3['nombre_curso'];
				//echo "<br>cursos[0]= $cursos[0]";			
			}
			mysql_free_result($registros3);
		}
	}


	//almacenamos los cursos elegidos en el array $alumnos
	if ($stat_usuarios == 1)
	{	
		if ($usuarioID == 0)  //// si NO se ha seleccionado ningun alumno en concreto
		{
			if ($cursoID == "")
			{		
				//**-*-*-*- vector que guarda los nombres de todos los usuarios -*-*-*-**
				$sql0="SELECT * FROM usuarios";
				$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
				$cuenta0 = mysql_num_rows($registros0);
				$j = 0;
				if ($cuenta0 > 0){
				  while ($reg0 = mysql_fetch_array($registros0)){
					   $lista_user_id[$j] = $reg0['user_id'];
					   $lista_nombre_usuarios[$j] = $reg0['username'];
					   $lista_admins[$j] = $reg0['admin'];
					   $j++;
				  }
				}  
				mysql_free_result($registros0);
				//echo "<br>";
			}
			else if ($cursoID == 0)
			{
				if ($estadoID == 0)
				{
					$sql0="SELECT * FROM usuarios";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0 = mysql_num_rows($registros0);
					$j = 0;
					if ($cuenta0 > 0){
					  while ($reg0 = mysql_fetch_array($registros0)){
						   $lista_user_id[$j] = $reg0['user_id'];
						   $lista_nombre_usuarios[$j] = $reg0['username'];
						   $lista_admins[$j] = $reg0['admin'];
						   $j++;
					  }
					}  
					mysql_free_result($registros0);
					//echo "<br>";
				}
				else if ($estadoID == 1)
				{
					$sql0 = "SELECT * FROM usuarios WHERE user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 ) ) ";			
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0 = mysql_num_rows($registros0);
					$j = 0;
					if ($cuenta0 > 0){
					  while ($reg0 = mysql_fetch_array($registros0)){
						   $lista_user_id[$j] = $reg0['user_id'];
						   $lista_nombre_usuarios[$j] = $reg0['username'];
						   $lista_admins[$j] = $reg0['admin'];
						   $j++;
					  }
					}  
					mysql_free_result($registros0);
					//echo "<br>";			
				}
				else  //if($estadoID==2)
				{
					$sql0 = "SELECT * FROM usuarios WHERE user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ) ";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0 = mysql_num_rows($registros0);
					$j = 0;
					if ($cuenta0 > 0){
					  while ($reg0 = mysql_fetch_array($registros0)){
						   $lista_user_id[$j] = $reg0['user_id'];
						   $lista_nombre_usuarios[$j] = $reg0['username'];
						   $lista_admins[$j] = $reg0['admin'];
						   $j++;
					  }
					}  
					mysql_free_result($registros0);
					//echo "<br>";			
				}
			}
			else   //if($cursoID>0)
			{
				$sql0 = "SELECT * FROM usuarios WHERE user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE curso_id = $cursoID ) ";
				$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
				$cuenta0 = mysql_num_rows($registros0);
				$j = 0;
				if ($cuenta0 > 0){
				  while ($reg0 = mysql_fetch_array($registros0)){
					   $lista_user_id[$j] = $reg0['user_id'];
					   $lista_nombre_usuarios[$j] = $reg0['username'];
					   $lista_admins[$j] = $reg0['admin'];
					   $j++;
				  }
				}  
				mysql_free_result($registros0);
				//echo "<br>";	
			}
		}else  //if($usuarioID>0)
		{
			$sql0="SELECT * FROM usuarios WHERE user_id = $usuarioID";
			$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
			$cuenta0 = mysql_num_rows($registros0);
			$j = 0;
			if ($cuenta0 > 0){
			  while ($reg0 = mysql_fetch_array($registros0)){
				   $lista_user_id[$j] = $reg0['user_id'];
				   $lista_nombre_usuarios[$j] = $reg0['username'];
				   $lista_admins[$j] = $reg0['admin'];
				   $j++;
			  }
			}  
			mysql_free_result($registros0);
			//echo "<br>";			
		}
	}
	
	
	//datos sobre los administradores
	$sql0="SELECT user_id, username FROM usuarios WHERE admin = 1";
	$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
	$cuenta0=mysql_num_rows($registros0);
	$numero_total_admins = $cuenta0;
	$j = 0;
	if ($cuenta0 > 0)
	{
		while ($reg0 = mysql_fetch_array($registros0))
		{	
		   $lista_admins[$j] = $reg0['user_id'];
		   $lista_nombre_admins[$j] = $reg0['username'];
		   $j++;
		}
	}
	mysql_free_result($registros0);
	//echo"<br>numero de administradores = $numero_total_admins";
	//for ($j=0; $j<count($lista_admins); $j++)
		//echo"<br>lista_nombre_admins[$j]=$lista_nombre_admins[$j]";

	
	
	
	//*********ESTADISTICAS GLOBALES*********//

	    //***ACCESOS al SISTEMA
		// contamos el numero historico de accesos en todos los cursos
		$sql0="SELECT count(*) AS total FROM logs";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
		$cuenta0=mysql_fetch_assoc($registros0);
		//echo "<br>Total accesos = ".$cuenta0['total'];	
		$total_accesos = $cuenta0['total'];
		mysql_free_result($registros0);

	    //***reservas		
		// contamos el numero historico de reservas en todos los pods
		$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_pod IS NOT NULL";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
		$cuenta0=mysql_fetch_assoc($registros0);
		//echo "<br>Total reservas = ".$cuenta0['total'];	
		$total_reservas = $cuenta0['total'];
		mysql_free_result($registros0);
		
	    //***CONEXIONES al LAB
		// contamos el numero historico de conexiones en todos los pods
		$sql0="SELECT count(*) AS total FROM log_detalles WHERE in_pod IS NOT NULL";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
		$cuenta0=mysql_fetch_assoc($registros0);
		//echo "<br>Total conexiones = ".$cuenta0['total'];	
		$total_conexiones = $cuenta0['total'];
		mysql_free_result($registros0);

//***-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-***
		
		//***NUMERO DE cursos TOTALES
		$sql0 = "SELECT count(*) AS total FROM cursos";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el Select (sql0) ".mysql_error());
		$cuenta0=mysql_fetch_assoc($registros0);       //numero de cursos totales
	    //echo "<br>Numero de cursos total = ".$cuenta0['total'];	
		$num_cursos_totales = $cuenta0['total'];
		mysql_free_result($registros0);

		//***NUMERO DE cursos ACTIVOS
		$sql0 = "SELECT count(*) AS total FROM cursos WHERE inicio_curso <= '$hoy' AND fin_curso >= '$hoy' AND curso_activo = 1";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el Select (sql0) ".mysql_error());
		$cuenta0=mysql_fetch_assoc($registros0);       //numero de cursos activos
	    //echo "<br>Numero de cursos activos = ".$cuenta0['total'];	
		$num_cursos_activos = $cuenta0['total'];
		mysql_free_result($registros0);
		
		//***NUMERO DE cursos PASADOS 
		$sql0 = "SELECT count(*) AS total FROM cursos WHERE fin_curso < '$hoy'";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el Select (sql0) ".mysql_error());
		$cuenta0=mysql_fetch_assoc($registros0);       //numero de cursos pasados
	    //echo "<br>Numero de cursos pasados = ".$cuenta0['total'];	
		$num_cursos_pasados = $cuenta0['total'];
		mysql_free_result($registros0);

//***-*-*-*-*-*-*--*-*-*--*-*-***

		//vector que guarda el estado de cada curso, ya sea activo o pasado  
		$sql0 = "SELECT * FROM cursos WHERE nombre_curso LIKE '%Experto%' AND num_max_pods > 0";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el Select (sql0) ".mysql_error());
		$count0=mysql_num_rows($registros0); 
		$j=0;
		while ($reg0 = mysql_fetch_array($registros0)){
			$fin_curso = $reg0['fin_curso'];
			$inicio_curso = $reg0['inicio_curso'];
			$curso_activo = $reg0['curso_activo'];
			if (($fin_curso >= $hoy) && ($inicio_curso <= $hoy) && ($curso_activo == 1)){
			   $abierto0[$j] = 1;
			}else{
			   $abierto0[$j] = 0;
			}
			$j++;
		}
		mysql_free_result($registros0);
		
		$sql0 = "SELECT * FROM cursos WHERE nombre_curso NOT LIKE '%Experto%' AND num_max_pods > 0";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el Select (sql0) ".mysql_error());
		$count0=mysql_num_rows($registros0); 
		while ($reg0 = mysql_fetch_array($registros0)){
			$fin_curso = $reg0['fin_curso'];
			$inicio_curso = $reg0['inicio_curso'];
			$curso_activo = $reg0['curso_activo'];
			if (($fin_curso >= $hoy) && ($inicio_curso <= $hoy) && ($curso_activo == 1)){
			   $abierto0[$j] = 1;
			}else{
			   $abierto0[$j] = 0;
			}
			$j++;
		}
		mysql_free_result($registros0);
		
		$sql0 = "SELECT * FROM cursos WHERE num_max_pods = 0";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el Select (sql0) ".mysql_error());
		$count0=mysql_num_rows($registros0); 
		while ($reg0 = mysql_fetch_array($registros0)){
			$fin_curso = $reg0['fin_curso'];
			$inicio_curso = $reg0['inicio_curso'];
			$curso_activo = $reg0['curso_activo'];
			if (($fin_curso >= $hoy) && ($inicio_curso <= $hoy) && ($curso_activo == 1)){
			   $abierto0[$j] = 1;
			}else{
			   $abierto0[$j] = 0;
			}
			$j++;
		}
		mysql_free_result($registros0);
//*-*-*-*-*-*-*-*-*-
		//////se crea un array llamado abierto1, donde se almacenan los cursos activos.
		////////se sigue el orden de cursos preestablecido: 1.-todos los cursos de experto; 2.-todos los cursos NO experto; 3.-cursos de control
		$sql0 = "SELECT * FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 AND nombre_curso LIKE '%Experto%' AND num_max_pods > 0";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el Select (sql0) ".mysql_error());
		$count0=mysql_num_rows($registros0); 
		$j=0;
		while ($reg0 = mysql_fetch_array($registros0)){
			$fin_curso = $reg0['fin_curso'];
			$inicio_curso = $reg0['inicio_curso'];
			$curso_activo = $reg0['curso_activo'];
			if (($fin_curso >= $hoy) && ($inicio_curso <= $hoy) && ($curso_activo == 1)){
			   $abierto1[$j] = 1;
			}else{
			   $abierto1[$j] = 0;
			}
			$j++;
		}
		mysql_free_result($registros0);
		
		$sql0 = "SELECT * FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 AND nombre_curso NOT LIKE '%Experto%' AND num_max_pods > 0";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el Select (sql0) ".mysql_error());
		$count0=mysql_num_rows($registros0); 
		while ($reg0 = mysql_fetch_array($registros0)){
			$fin_curso = $reg0['fin_curso'];
			$inicio_curso = $reg0['inicio_curso'];
			$curso_activo = $reg0['curso_activo'];
			if (($fin_curso >= $hoy) && ($inicio_curso <= $hoy) && ($curso_activo == 1)){
			   $abierto1[$j] = 1;
			}else{
			   $abierto1[$j] = 0;
			}
			$j++;
		}
		mysql_free_result($registros0);
		
		$sql0 = "SELECT * FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 AND num_max_pods = 0";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el Select (sql0) ".mysql_error());
		$count0=mysql_num_rows($registros0); 
		while ($reg0 = mysql_fetch_array($registros0)){
			$fin_curso = $reg0['fin_curso'];
			$inicio_curso = $reg0['inicio_curso'];
			$curso_activo = $reg0['curso_activo'];
			if (($fin_curso >= $hoy) && ($inicio_curso <= $hoy) && ($curso_activo == 1)){
			   $abierto1[$j] = 1;
			}else{
			   $abierto1[$j] = 0;
			}
			$j++;
		}
		mysql_free_result($registros0);
//*-*-*-*-*-*-*-*-*-*-
		////lo mismo que antes, pero ahora en la variable abierto2 se asocia 1 a los inactivos, y 0 a los activos
		$sql0 = "SELECT * FROM cursos WHERE (fin_curso < '$hoy' OR curso_activo = 0) AND nombre_curso LIKE '%Experto%' AND num_max_pods > 0";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el Select (sql0) ".mysql_error());
		$count0=mysql_num_rows($registros0); 
		$j=0;
		while ($reg0 = mysql_fetch_array($registros0)){
			$fin_curso = $reg0['fin_curso'];
			$inicio_curso = $reg0['inicio_curso'];
			$curso_activo = $reg0['curso_activo'];
			if (($fin_curso >= $hoy) && ($inicio_curso <= $hoy) && ($curso_activo == 1)){
			   $abierto2[$j] = 1;
			}else{
			   $abierto2[$j] = 0;
			}
			$j++;
		}
		mysql_free_result($registros0);
		
		$sql0 = "SELECT * FROM cursos WHERE (fin_curso < '$hoy' OR curso_activo = 0) AND nombre_curso NOT LIKE '%Experto%' AND num_max_pods > 0";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el Select (sql0) ".mysql_error());
		$count0=mysql_num_rows($registros0); 
		while ($reg0 = mysql_fetch_array($registros0)){
			$fin_curso = $reg0['fin_curso'];
			$inicio_curso = $reg0['inicio_curso'];
			$curso_activo = $reg0['curso_activo'];
			if (($fin_curso >= $hoy) && ($inicio_curso <= $hoy) && ($curso_activo == 1)){
			   $abierto2[$j] = 1;
			}else{
			   $abierto2[$j] = 0;
			}
			$j++;
		}
		mysql_free_result($registros0);
		
		$sql0 = "SELECT * FROM cursos WHERE (fin_curso < '$hoy' OR curso_activo = 0) AND num_max_pods = 0";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el Select (sql0) ".mysql_error());
		$count0=mysql_num_rows($registros0); 
		while ($reg0 = mysql_fetch_array($registros0)){
			$fin_curso = $reg0['fin_curso'];
			$inicio_curso = $reg0['inicio_curso'];
			$curso_activo = $reg0['curso_activo'];
			if (($fin_curso >= $hoy) && ($inicio_curso <= $hoy) && ($curso_activo == 1)){
			   $abierto2[$j] = 1;
			}else{
			   $abierto2[$j] = 0;
			}
			$j++;
		}
		mysql_free_result($registros0);
//*-*-*-*-*-*-*-*-*-*-

		//***NUMERO DE usuarios TOTALES
		$sql0 = "SELECT count(*) AS total FROM usuarios";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el Select (sql0) ".mysql_error());
		$cuenta0=mysql_fetch_assoc($registros0);       //numero de cursos totales
	    //echo "<br>Numero de usuarios total = ".$cuenta0['total'];	
		$num_usuarios_totales = $cuenta0['total'];
		mysql_free_result($registros0);

		//***NUMERO DE usuarios en ACTIVO
		$sql0 = "SELECT count(*) AS total FROM usuarios WHERE user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 ) ) ";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el Select (sql0) ".mysql_error());
		$cuenta0=mysql_fetch_assoc($registros0);       //numero de cursos activos
	    //echo "<br>Numero de usuarios activos = ".$cuenta0['total'];	
		$num_usuarios_activos = $cuenta0['total'];
		mysql_free_result($registros0);
//+++******+++++++++**********++++++++++++*++++++++++*+++++//

		///datos de cursos y sus pods
		
		///cursos de experto
		$sql0="SELECT * FROM cursos WHERE curso_activo = 1 AND inicio_curso <= CURDATE() AND fin_curso >= CURDATE() AND nombre_curso LIKE '%Experto%' AND num_max_pods > 0";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
		$count0 = mysql_num_rows($registros0);
		$num_cursos_en_ejecucion = $count0;
		$j = 0;
		if ($count0 > 0)
		{
			while ($reg0 = mysql_fetch_array($registros0))
			{		
				$array_curso_id[$j] = $reg0['curso_id'];
				$array_num_max_pods[$j] = $reg0['num_max_pods'];
				$array_nombre_curso[$j] = $reg0['nombre_curso'];
				$array_inicio_curso[$j] = $reg0['inicio_curso'];
				$array_fin_curso[$j] = $reg0['fin_curso'];
				$array_edicion[$j] = $reg0['edicion'];
				$array_curso_activo[$j] = $reg0['curso_activo'];

				$array_dia_mant_semanal[$j] = $reg0['dia_mant_semanal'];
				$array_hora_inicio_mant_semanal[$j] = $reg0['hora_inicio_mant_semanal'];
				$array_duracion_mant_semanal[$j] = $reg0['duracion_mant_semanal'];
				$array_flag_pods_ok[$j] = $reg0['flag_pods_ok'];				
				$j++;
			}
		}
		mysql_free_result($registros0);

		///cursos de NO experto
		$sql0="SELECT * FROM cursos WHERE curso_activo = 1 AND inicio_curso <= CURDATE() AND fin_curso >= CURDATE() AND nombre_curso NOT LIKE '%Experto%' AND num_max_pods > 0";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
		$count0 = mysql_num_rows($registros0);
		$num_cursos_en_ejecucion += $count0;
		if ($count0 > 0)
		{
			while ($reg0 = mysql_fetch_array($registros0))
			{		
				$array_curso_id[$j] = $reg0['curso_id'];
				$array_num_max_pods[$j] = $reg0['num_max_pods'];
				$array_nombre_curso[$j] = $reg0['nombre_curso'];
				$array_inicio_curso[$j] = $reg0['inicio_curso'];
				$array_fin_curso[$j] = $reg0['fin_curso'];
				$array_edicion[$j] = $reg0['edicion'];
				$array_curso_activo[$j] = $reg0['curso_activo'];

				$array_dia_mant_semanal[$j] = $reg0['dia_mant_semanal'];
				$array_hora_inicio_mant_semanal[$j] = $reg0['hora_inicio_mant_semanal'];
				$array_duracion_mant_semanal[$j] = $reg0['duracion_mant_semanal'];
				$array_flag_pods_ok[$j] = $reg0['flag_pods_ok'];				
				$j++;
			}
		}
		mysql_free_result($registros0);
		
		$offset_pod = 0;
		
		for ($j=0; $j<$num_cursos_en_ejecucion; $j++) {
			$num = $j + 1;
			//echo "<br>CURSO $array_nombre_curso[$j]:  PODS ASIGNADOS: ";
			$str = '';
			
			for ($k=0; $k<$array_num_max_pods[$j]; $k++){
				$num = ($k + 1) + $offset_pod;
				//echo " $num ";
				$str = $str . "&nbsp; $num &nbsp;";
			}
			
			$offset_pod = $num;
			$array_num_pods[$j] = $str;   
			//echo "array_num_pods[$j] = $array_num_pods[$j]";		
			
			$pod_inicial[$j]=$offset_pod-$array_num_max_pods[$j]+1;
			$pod_final[$j]=$offset_pod;			
		}
//*********************************************//		
		
?>

        <input type="text" name="estadisticasGlobales" value=" Estad&iacute;sticas Globales" 
 		id="estadisticasGlobales" style="width: 210px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:12pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  

        <center>
		   <table border="1" width="91%" cellpadding="10" cellspacing="0">
		        <tr>
				    <th bgcolor="khaki" width="10%"> Total accesos:  </th>
					<th bgcolor="khaki" width="10%"> Total reservas:  </th>
				    <th bgcolor="khaki" width="10%"> Total conexiones:  </th>
					<th width="1%"> </th>
					<th bgcolor="khaki" width="10%"> N&ordm; cursos Total  </th>
					<th bgcolor="khaki" width="10%"> N&ordm; cursos Activos  </th>
					<th width="1%"> </th>
					<th bgcolor="khaki" width="10%"> N&ordm; usuarios Total  </th>
					<th bgcolor="khaki" width="12%"> N&ordm; usuarios Activos  </th>
					<th width="1%"> </th>
					<th bgcolor="khaki" width="6%"> N&ordm; admin </th>
				</tr>
				
		        <tr>
					<td width="10%" bgcolor="#eeeeee" align="right"> <?php echo "  $total_accesos"; ?>
					<td width="10%" bgcolor="#eeeeee" align="right"> <?php echo "  $total_reservas"; ?>
					<td width="10%" bgcolor="#eeeeee" align="right"> <?php echo "  $total_conexiones"; ?>
					<th width="1%"> </th>
					<td width="10%" bgcolor="#eeeeee" align="right"> <?php echo "  $num_cursos_totales"; ?>
					<td width="10%" bgcolor="#eeeeee" align="right"> <?php echo "  $num_cursos_activos"; ?>
					<th width="1%"> </th>
					<td width="10%" bgcolor="#eeeeee" align="right"> <?php echo "  $num_usuarios_totales"; ?>
					<td width="12%" bgcolor="#eeeeee" align="right"> <?php echo "  $num_usuarios_activos"; ?>
					<th width="1%"> </th>
					<td width="6%" bgcolor="#eeeeee" align="right"> <?php echo "  $numero_total_admins"; ?>
				</tr>
			</table>
		</center>
		<br>
		<br>
		
        <center>
		   <table border="1" width="60%" cellpadding="10" cellspacing="0">
		        <tr>
				    <th bgcolor="khaki" width="23%"> Nombre del Curso:  </th>
					<th bgcolor="khaki" width="20%"> N&uacute;m.m&aacute;ximo de PODs:  </th>		
					<th bgcolor="khaki" width="17%"> PODS involucrados:  </th>
				</tr>			
				
	<?php 	for ($i=0; $i<$num_cursos_en_ejecucion; $i++){    ?>
	
		        <tr>					
					<td width="23%" bgcolor="#eeeeee" align="right"> <?php echo "  $array_nombre_curso[$i]"; if ($array_edicion[$i]>0) echo "&nbsp;&nbsp;($array_edicion[$i]&ordf; edici&oacute;n)"; ?>
					<td width="20%" bgcolor="#eeeeee" align="right"> <?php echo "  $array_num_max_pods[$i]"; ?>
					<td width="17%" bgcolor="#eeeeee" align="right"> <?php echo "  $array_num_pods[$i]"; ?>
				</tr>
				
	<?php   }   ?>
	
			</table>
		</center>
		<br>
		<br>		
		
		<?php  
		if ($flag_parametros==1){  
		?>
			<input type="text" name="ParametrosEstadisticos" value=" Par&aacute;metros Estad&iacute;sticos" 
			id="ParametrosEstadisticos" style="width: 240px; background: #ffc9c9; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:12pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  

			<center>
			   <table border="1" width="91%" cellpadding="10" cellspacing="0">
					<tr>
						<?php if ($stat_pods == 1) ?>
							<td width="5%" bgcolor="#ffffcc" align="center"> PODS </td>  <?php ; ?>
						<?php if ($stat_cursos == 1) ?>
							<td width="5%" bgcolor="#ffffcc" align="center"> cursos </td>  <?php ; ?>
						<?php if ($stat_usuarios == 1) ?>
							<td width="5%" bgcolor="#ffffcc" align="center"> usuarios </td>  <?php ; ?>
						<?php if ($stat_tiempos == 1) ?>
							<td width="5%" bgcolor="#ffffcc" align="center"> TEMPORAL </td>  <?php ; ?>
						<?php if ($stat_reservas == 1) ?>
							<td width="5%" bgcolor="#ffffcc" align="center"> ADMINS </td>  <?php ; ?>
						<?php if ($stat_admins == 1) ?>
							<td width="5%" bgcolor="#ffffcc" align="center"> ULTIMAS RESV </td>  <?php ; ?>
						<?php if ($stat_totales== 1) ?>
							<td width="5%" bgcolor="#ffffcc" align="center"> TOTAL </td>  <?php ; ?>
					</tr>
																													
					<tr>
						<td width="5%" bgcolor="#fcfcfc" align="center"> <?php if (isset($pods)){ if (count($pods)==$total_pods) echo "Todos"; else  for ($i=0;$i<count($pods);$i++) echo "$pods[$i] &nbsp;&nbsp;&nbsp;"; } else echo "-"; ?> </td>  
						<td width="5%" bgcolor="#fcfcfc" align="center"> <?php if (isset($cursos)){ if($cursoID==0){ if($estadoID==0) echo "Todos"; else if($estadoID==1) echo "Activos"; else echo "Pasados"; } else{ echo "$nombre_cursos[0]";} } else echo "-"; ?> </td>  
						<td width="5%" bgcolor="#fcfcfc" align="center"> <?php if (isset($lista_nombre_usuarios)){ if($usuarioID==0){ if($cursoID=="") echo "Todos"; else if($cursoID==0){ if ($estadoID==0) echo "Todos"; else if ($estadoID==1) echo "Enrolados "; else echo "Enrolados"; } else echo "Enrolados"; } else echo "$lista_nombre_usuarios[0]"; } else echo "-"; ?> </td>  
						<td width="5%" bgcolor="#fcfcfc" align="center"> <?php if ($stat_tiempos==1){ if($tiempoID==0) echo "Todos"; else if($tiempoID==1) echo "por semanas"; else if($tiempoID==2) echo "por d&iacute;as"; else echo "por horas"; } else echo "-"; ?> </td> 
						<td width="5%" bgcolor="#fcfcfc" align="center"> <?php if ($stat_admins==1){ if($adminsID==0) echo "Todos"; else{ for($i=0;$i<count($lista_admins);$i++) if ($adminsID==$lista_admins[$i]) echo "$lista_nombre_admins[$i]"; } } else echo "-"; ?> </td>
						<td width="5%" bgcolor="#fcfcfc" align="center"> <?php if ($stat_reservas==1){ if($ultimasreservasID==0) echo "Todas"; else if ($ultimasreservasID==1) echo "las m&aacute;s recientes"; else if ($ultimasreservasID==2) echo "pendientes"; else echo "turno actual"; } else echo "-"; ?> </td>   
						<td width="5%" bgcolor="#fcfcfc" align="center"> <?php if ($stat_totales==1){ echo "Resumen General"; } else echo "-"; ?> </td>  
					</tr>
				</table>
			</center>
			<br>
		<?php 
		}  
		?>
		
<?php



	echo "<hr>";
	
	
	
	//****ESTADISTICAS POR PODS****//
	
	if (isset($pods))
	{
	    //***reservas		
		// contamos el numero historico de reservas en todos los pods
		$total_reservas_pods = $total_reservas;
		//echo "<br>total_reservas_pods = $total_reservas_pods";

		
	    // contamos el numero historico de reservas por cada pod
	    for ($i=0; $i<count($pods); $i++)
	    {
			//echo "<br>$pods[$i]";
			$sql1="SELECT count(*) AS total FROM log_detalles WHERE confir_pod = $pods[$i]";
			$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
			$cuenta1=mysql_fetch_assoc($registros1);
			//echo "<br>Total reservas POD #$pods[$i] = ".$cuenta1['total'];
			$contador_reservas_pods[$i] = $cuenta1['total'];
			//echo "contador_reservas_pods[$i] = $contador_reservas_pods[$i]";
	        mysql_free_result($registros1);
		}
		//echo "<br>";

		
	    //***CONEXIONES al LAB
		// contamos el numero historico de conexiones en todos los pods
		$total_conexiones_pods = $total_conexiones;
		//echo "<br>total_conexiones_pods = $total_conexiones_pods";

		
	   // contamos el numero historico de conexiones por cada pod	
	   for ($i=0; $i<count($pods); $i++)
	   {
			//echo "<br>$pods[$i]";
			$sql1="SELECT count(*) AS total FROM log_detalles WHERE in_pod = $pods[$i]";
			$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
			$cuenta1=mysql_fetch_assoc($registros1);
			//echo "<br>Total conexiones POD #$pods[$i] = ".$cuenta1['total'];
			$contador_conexiones_pods[$i] = $cuenta1['total'];
			//echo "contador_conexiones_pods[$i] = $contador_conexiones_pods[$i]";
	        mysql_free_result($registros1);
		}
		//echo "<br>";	


	    //***mantenimiento de PODS
		// contamos el numero historico de intervalos de mantenimiento en todos los pods
		$sql0="SELECT count(*) AS total FROM mantenimiento";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
		$cuenta0=mysql_fetch_assoc($registros0);
		//echo "<br>Total turnos de mantenimiento en todos los Pods = ".$cuenta0['total'];	
		$total_mantenimiento_pods = $cuenta0['total'];
		//echo "<br>total_mantenimiento_pods = $total_mantenimiento_pods";
		mysql_free_result($registros0);
		
		
		// contamos el numero historico de intervalos de mantenimiento por cada POD
	    for ($i=0; $i<count($pods); $i++)
	    {
			//echo "<br>$pods[$i]";
			$sql1="SELECT count(*) AS total FROM mantenimiento WHERE num_POD_outage = $pods[$i]";
			$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
			$cuenta1=mysql_fetch_assoc($registros1);
			//echo "<br>Total turnos de mantenimiento en POD #$pods[$i] = ".$cuenta1['total'];
			$contador_mantenimiento_pods[$i] = $cuenta1['total'];
			//echo "contador_mantenimiento_pods[$i] = $contador_mantenimiento_pods[$i]";
	        mysql_free_result($registros1);
		}
		//echo "<br>";			
 
?>

        <input type="text" name="estadisticasPods" value=" Estad&iacute;sticas por PODS" 
 		id="estadisticasPods" style="width: 210px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:12pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  

        <center>
		   <table border="1" cellpadding="10" cellspacing="0" width="48%" class="thin sortable draggable">
				<tr>
					<th width="12%"></th>
					<th bgcolor="#cccc99" width="12%"> <?php echo "N&ordm; mantenimientos en el POD"; ?>  </th>
					<th bgcolor="khaki" width="12%"> <?php echo "N&ordm; reservas en el POD"; ?>  </th>					
					<th bgcolor="khaki" width="12%"> <?php echo "N&ordm; conexiones en el POD"; ?>  </th>					
				</tr>
	
<?php        	for ($i=0; $i<count($pods); $i++){    ?>

					<tr>
						<th bgcolor="aquamarine" width="12%" align="center"> <?php echo "POD #$pods[$i]"; ?>  </th>
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php echo "$contador_mantenimiento_pods[$i]"; ?>  </td>	
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php echo "$contador_reservas_pods[$i]"; ?>  </td>
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php echo "$contador_conexiones_pods[$i]"; ?>  </td>	
					</tr>
					
					
<?php			}  ?>		

				</tr>
					<th width="12%" bgcolor="#eeeeee">  TOTAL </th>			
					<td width="12%" bgcolor="#eeeeee" align="right">  <?php $suma = array_sum($contador_mantenimiento_pods); echo " $suma"; ?>  </td>						
					<td width="12%" bgcolor="#eeeeee" align="right"> <?php $suma = array_sum($contador_reservas_pods); echo " $suma"; ?>  </td>
					<td width="12%" bgcolor="#eeeeee" align="right">  <?php $suma = array_sum($contador_conexiones_pods); echo " $suma"; ?>  </td>											
				</tr>	

			</table>
		</center>
		<br>

<?php 
	
	}
		
		
	echo "<hr>";

	
	//****ESTADISTICAS POR cursos****//
	if ($stat_cursos == 1)
	{
	    //***ACCESOS al SISTEMA
		// contamos el numero historico de accesos en todos los cursos
		$total_accesos_cursos = $total_accesos;
		//echo "<br>total_accesos_cursos = $total_accesos_cursos";
		
		
		// contamos el numero historico de accesos por cada curso
	    for ($i=0; $i<count($cursos); $i++)
	    {
			//echo "<br>$cursos[$i]";
			$sql1="SELECT count(*) AS total FROM logs WHERE curso_id = $cursos[$i]";
			$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
			$cuenta1=mysql_fetch_assoc($registros1);
			//echo "<br>Total accesos en curso #$cursos[$i] = ".$cuenta1['total'];
			$contador_accesos_cursos[$i] = $cuenta1['total'];
			//echo "contador_accesos_cursos[$i] = $contador_accesos_cursos[$i]";
	        mysql_free_result($registros1);
		}		
		//echo "<br>";
		
		
		//***RESERVAS EFECTUADAS
		// contamos el numero historico de reservas en todos los cursos
		$total_reservas_cursos = $total_reservas;
		//echo "<br>total_reservas_cursos = $total_reservas_cursos";

		
		// contamos el numero historico de reservas por cada curso
	    for ($i=0; $i<count($cursos); $i++)
	    {
			//echo "<br>$cursos[$i]";			
			///$sql1="SELECT count(*) AS total FROM reservas WHERE curso_id = $cursos[$i]";
			$sql1="SELECT count(*) AS total FROM log_detalles WHERE curso_id = $cursos[$i] AND confir_resv IS NOT NULL";
			$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
			$cuenta1=mysql_fetch_assoc($registros1);
			//echo "<br>Total reservas en curso #$cursos[$i] = ".$cuenta1['total'];
			$contador_reservas_cursos[$i] = $cuenta1['total'];
			//echo "contador_reservas_cursos[$i] = $contador_reservas_cursos[$i]";
	        mysql_free_result($registros1);
		}
		//echo "<br>";


	    //***CONEXIONES al LAB
		// contamos el numero historico de conexiones en todos los pods
		$total_conexiones_cursos = $total_conexiones;
		//echo "<br>total_conexiones_cursos = $total_conexiones_cursos";

		
		// contamos el numero historico de conexiones por cada curso
	    for ($i=0; $i<count($cursos); $i++)
	    {
			//echo "<br>$cursos[$i]";
			$sql1="SELECT count(*) AS total FROM log_detalles WHERE curso_id = $cursos[$i] AND in_pod IS NOT NULL";
			$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
			$cuenta1=mysql_fetch_assoc($registros1);
			//echo "<br>Total conexiones en curso #[$i] = ".$cuenta1['total'];
			$contador_conexiones_cursos[$i] = $cuenta1['total'];
			//echo "contador_conexiones_cursos[$i] = $contador_conexiones_cursos[$i]";
	        mysql_free_result($registros1);
		}
		//echo "<br>";	

		?>

        <input type="text" name="estadisticascursos" value=" Estad&iacute;sticas por cursos" 
 		id="estadisticascursos" style="width: 210px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:12pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  
		
        <center>
		   <table border="1" cellpadding="10" cellspacing="0" width="48%" class="thin sortable draggable">
				<tr>
					<th width="12%"></th>
					<th bgcolor="khaki" width="12%"> <?php echo "N&ordm; accesos al Curso"; ?>  </th>
					<th bgcolor="khaki" width="12%"> <?php echo "N&ordm; reservas al Curso"; ?>  </th>					
					<th bgcolor="khaki" width="12%"> <?php echo "N&ordm; conexiones al Curso"; ?>  </th>	
					<th bgcolor="khaki" width="15%"> <?php echo "Curso Activo ahora"; ?>  </th>
				</tr>
	
<?php        	for ($i=0; $i<count($cursos); $i++){    ?>

					<tr>
						<th bgcolor="aquamarine" width="12%" align="center"> <?php echo "$nombre_cursos[$i]"; ?>  </th>
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php echo "$contador_accesos_cursos[$i]"; ?>  </td>	
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php echo "$contador_reservas_cursos[$i]"; ?>  </td>
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php echo "$contador_conexiones_cursos[$i]"; ?>  </td>
						<td width="15%" bgcolor="#fcfcfc" align="right"> <?php if ($cursoID==0){ if ($estadoID==1) echo " SI"; else if ($estadoID==2) echo " NO"; else  if ($abierto0[$i]==1) echo " SI"; else echo " NO";} else{ $j=$cursoID-1; if($abierto0[$j]==1) echo " SI"; else echo " NO"; } ?>  </td>
					</tr>
					
					
<?php			}  ?>		

				</tr>
					<th width="12%" bgcolor="#eeeeee">  TOTAL </th>			
					<td width="12%" bgcolor="#eeeeee" align="right">  <?php $suma = array_sum($contador_accesos_cursos); echo " $suma"; ?>  </td>						
					<td width="12%" bgcolor="#eeeeee" align="right"> <?php $suma = array_sum($contador_reservas_cursos); echo " $suma"; ?>  </td>
					<td width="12%" bgcolor="#eeeeee" align="right">  <?php $suma = array_sum($contador_conexiones_cursos); echo " $suma"; ?>  </td>
					<td width="15%" bgcolor="#eeeeee" align="right">  <?php $j=0; for ($i=0;$i<count($abierto0);$i++) if($abierto0[$i]==1) $j++; echo " $j"; ?></td>
				</tr>	

			</table>
		</center>
		<br>

<?php 

		
    }


	echo "<hr>";
		
		
	//****ESTADISTICAS POR usuarios****//
	if ($stat_usuarios == 1)
	{
	    ///**FASE 1: seleccionamos los usuarios correspondientes **//
		
		if ($usuarioID == 0)  //// si NO se ha seleccionado ningun alumno en concreto
		{
			if (!isset ($cursoID))    /////si NO se ha seleccionado algun curso
			{
				// seleccionamos todos los usuarios
				$sql="SELECT * FROM usuarios";
				$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
				$count = mysql_num_rows($registros);
				$j = 0;
				if ($count > 0){
				  while ($reg = mysql_fetch_array($registros)){
					   $usuarios[$j] = $reg['user_id'];
					   $nombre_usuarios[$j] = $reg['username'];
					   $admins[$j] = $reg['admin'];
					   $j++;
				  }
				$total_usuarios = $j;
				//echo "<br>total_usuarios = $total_usuarios";
				}  
				mysql_free_result($registros);
				//echo "<br>";		 			
			}
			else   /////si se ha seleccionado algun curso
			{
				////se comprueba si se trabaja con todos los usuarios, o por el contrario, si se ha elegido algun curso, se trabajara solo con los usuarios de ese mismo curso
				if ($cursoID == 0)
				{
					if ($estadoID == 0)  //todos los cursos (Activos y Pasados)
					{
						// seleccionamos todos los usuarios
						$sql="SELECT * FROM usuarios";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count = mysql_num_rows($registros);
						$j = 0;
						if ($count > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $usuarios[$j] = $reg['user_id'];
							   $nombre_usuarios[$j] = $reg['username'];
							   $admins[$j] = $reg['admin'];
							   $j++;
						  }
						$total_usuarios = $j;
						//echo "<br>total_usuarios = $total_usuarios";
						}  
						mysql_free_result($registros);
						//echo "<br>";
					}
					else if ($estadoID == 1)   //todos los cursos Activos
					{
						$sql = "SELECT * FROM usuarios WHERE user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 ) ) ";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count = mysql_num_rows($registros);
						$j = 0;
						if ($count > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $usuarios[$j] = $reg['user_id'];
							   $nombre_usuarios[$j] = $reg['username'];
							   $admins[$j] = $reg['admin'];
							   $j++;
						  }
						$total_usuarios = $j;
						//echo "<br>total_usuarios = $total_usuarios";
						}  
						mysql_free_result($registros);
						//echo "<br>";
					}
					else  //if ($estadoID == 2)    //todos los cursos Pasados
					{
						$sql = "SELECT * FROM usuarios WHERE user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ) ";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count = mysql_num_rows($registros);
						$j = 0;
						if ($count > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $usuarios[$j] = $reg['user_id'];
							   $nombre_usuarios[$j] = $reg['username'];
							   $admins[$j] = $reg['admin'];
							   $j++;
						  }
						$total_usuarios = $j;
						//echo "<br>total_usuarios = $total_usuarios";
						}  
						mysql_free_result($registros);
						//echo "<br>";
					}
				}
				else     //el curso seleccionado en particular
				{
						$sql = "SELECT * FROM usuarios WHERE user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE curso_id = $cursoID ) ";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count = mysql_num_rows($registros);
						$j = 0;
						if ($count > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $usuarios[$j] = $reg['user_id'];
							   $nombre_usuarios[$j] = $reg['username'];
							   $admins[$j] = $reg['admin'];
							   $j++;
						  }
						$total_usuarios = $j;
						//echo "<br>total_usuarios = $total_usuarios";
						}  
						mysql_free_result($registros);
						//echo "<br>";
				}
			}
		}
		else   /////si se ha seleccionado algun alumno en particular, se seleccionan sus datos
		{
			// seleccionamos los datos de ese usuario
			$sql="SELECT * FROM usuarios WHERE user_id = $usuarioID";
			$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
			$count = mysql_num_rows($registros);
			$j = 0;
			if ($count > 0){
			  while ($reg = mysql_fetch_array($registros)){
				   $usuarios[$j] = $reg['user_id'];
				   $nombre_usuarios[$j] = $reg['username'];
				   $admins[$j] = $reg['admin'];
				   $j++;
			  }
			$total_usuarios = $j;
			//echo "<br>total_usuarios = $total_usuarios";
			}  
			mysql_free_result($registros);
			//echo "<br>";		
		}
		
		
		//echo "<br><br>";
		
		
		///**FASE 2: calculamos las estadisticas segun los usuarios y los cursos seleccionados **//
				
		if ($cursoID == "")    //todos los usuarios seleccionados en la variable $usuarios[], sin tener en cuenta a que Curso pertenecen
		{
			//***ACCESOS al SISTEMA
			// contamos el numero historico de accesos de todos los usuarios
			$total_accesos_usuarios = $total_accesos;   
			//echo "<br>total_accesos_usuarios = $total_accesos_usuarios";
			//echo"<br>NO cursos";
			
			// seleccionamos el numero historico de accesos por cada usuario
			for ($i=0; $i<count($usuarios); $i++)
			{
				//echo "<br>$usuarios[$i]";
				$sql1="SELECT count(*) AS total FROM logs WHERE user_id = $usuarios[$i]";
				$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
				$cuenta1=mysql_fetch_assoc($registros1);
				//echo "<br>Total accesos del usuario #$usuarios[$i] = ".$cuenta1['total'];
				$contador_accesos_usuarios[$i] = $cuenta1['total'];
				//echo "contador_accesos_usuarios[$i] = $contador_accesos_usuarios[$i]";
				mysql_free_result($registros1);
			}	
			//echo "<br>";
			
			
			//***reservas		
			// contamos el numero historico de reservas de todos los usuarios
			$total_reservas_usuarios = $total_reservas;
			//echo "<br>total_reservas_usuarios = $total_reservas_usuarios";

			
			// contamos el numero historico de reservas por usuario
			for ($i=0; $i<count($usuarios); $i++)
			{
				//echo "<br>$usuarios[$i]";
				$sql1="SELECT count(*) AS total FROM log_detalles WHERE user_id = $usuarios[$i] AND confir_resv IS NOT NULL";
				$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
				$cuenta1=mysql_fetch_assoc($registros1);
				//echo "<br>Total reservas del usuario #$usuarios[$i] = ".$cuenta1['total'];
				$contador_reservas_usuarios[$i] = $cuenta1['total'];
				//echo "contador_reservas_usuarios[$i] = $contador_reservas_usuarios[$i]";
				mysql_free_result($registros1);
			}
			//echo "<br>";
			
			
			//***CONEXIONES al LAB
			// contamos el numero historico de conexiones de todos los usuarios
			$total_conexiones_usuarios = $total_conexiones;
			//echo "<br>total_conexiones_usuarios = $total_conexiones_usuarios";

			
			// contamos el numero historico de conexiones por usuario
			for ($i=0; $i<count($usuarios); $i++)
			{
				//echo "<br>$usuarios[$i]";
				$sql1="SELECT count(*) AS total FROM log_detalles WHERE user_id = $usuarios[$i] AND in_pod IS NOT NULL";
				$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
				$cuenta1=mysql_fetch_assoc($registros1);
				//echo "<br>Total conexiones del usuario #$usuarios[$i] = ".$cuenta1['total'];
				$contador_conexiones_usuarios[$i] = $cuenta1['total'];
				//echo "contador_conexiones_usuarios[$i] = $contador_conexiones_usuarios[$i]";
				mysql_free_result($registros1);
			}
			//echo "<br>";
			
			
			//***ACCESOS de ADMINISTRADOR
			// contamos el numero historico de accesos de administrador
			$sql0="SELECT count(*) AS total FROM logs WHERE user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 )";
			$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
			$cuenta0=mysql_fetch_assoc($registros0);
			//echo "<br>Total accesos admin = ".$cuenta0['total'];	
			$total_accesos_admin = $cuenta0['total'];
			//echo "<br>total_accesos_admin = $total_accesos_admin";
			mysql_free_result($registros0);
			
			$sql1="SELECT user_id, username FROM usuarios WHERE admin = 1";
			$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
			$cuenta1=mysql_num_rows($registros1);
			$total_admin = $cuenta1;
			//echo "<br>Num.administradores = $total_admin";

			if ($cuenta1 > 0)
			{
			    $j = 0;
				while ($reg1 = mysql_fetch_array($registros1))
				{	
				   $administradores[$j] = $reg1['user_id'];
				   $nombre_administradores[$j] = $reg1['username'];
				   //echo "<br>administradores[$j] = $administradores[$j]";
				   $j++;
				}
				mysql_free_result($registros1);
								
				// contamos el numero historico de accesos para cada administrador
				for ($i=0; $i<count($administradores); $i++)
				{		
					$sql2="SELECT count(*) AS total FROM logs WHERE user_id = $administradores[$i]";
					$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
					$cuenta2=mysql_fetch_assoc($registros2);
					$contador_accesos_admin[$i] = $cuenta2['total'];
					//echo "<br>total_accesos_admin $nombre_administradores[$i] = $contador_accesos_admin[$i]";
					mysql_free_result($registros2);
				}
			}
			//echo "<br>";
			
			
			//***CODIGOS DE SALIDA
			// contamos el codigo de salida de cada log, para ver como se sale del sistema
			// NULL: final abrupto, 0: tiempo en POD cumplido, 1: salida voluntaria, -1: timeout cumplido, 2: salida voluntaria admin, -2: timeout admin
		   $exit_code[0] = 0;
		   $exit_code[1] = 1;
		   $exit_code[2] = -1;
		   $exit_code[3] = 2;
		   $exit_code[4] = -2;
		   $exit_code[5] = NULL;
		   
			for ($i=0; $i<5; $i++)
			{
				//echo "<br>exit_code = $exit_code[$i]";
				$sql1="SELECT count(*) AS total FROM logs WHERE codigo_salida = $exit_code[$i]";
				$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
				$cuenta1=mysql_fetch_assoc($registros1);
				//echo "<br>Total salidas con codigo de salida #$exit_code[$i] = ".$cuenta1['total'];
				$total_codigo_salida[$i] = $cuenta1['total'];
				//echo "total_codigo_salida[$i] = $total_codigo_salida[$i]";
				mysql_free_result($registros1);
				
				for ($j=0; $j<count($usuarios); $j++)
				{
					$sql2="SELECT count(*) AS total FROM logs WHERE codigo_salida = $exit_code[$i] AND user_id = $usuarios[$j]";
					$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
					$cuenta2=mysql_fetch_assoc($registros2);
					//echo "<br> salidas del usuario $usuarios[$j] con codigo de salida #$exit_code[$i] = ".$cuenta2['total'];
					$contador_codigo_salida[$i][$j] = $cuenta2['total'];
					//echo "contador_codigo_salida[$i][$j] = $contador_codigo_salida[$i][$j]";
					mysql_free_result($registros2);		
				}
			}
			//echo "<br>exit_code = NULL";
			$sql1="SELECT count(*) AS total FROM logs WHERE codigo_salida IS NULL";
			$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
			$cuenta1=mysql_fetch_assoc($registros1);
			//echo "<br>Total salidas con codigo de salida #NULL = ".$cuenta1['total'];
			$total_codigo_salida[5] = $cuenta1['total'];
			//echo "total_codigo_salida[5] = $total_codigo_salida[5]";
			mysql_free_result($registros1);
			
			for ($j=0; $j<count($usuarios); $j++)
			{
				$sql2="SELECT count(*) AS total FROM logs WHERE codigo_salida IS NULL AND user_id = $usuarios[$j]";
				$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
				$cuenta2=mysql_fetch_assoc($registros2);
				//echo "<br> salidas del usuario $usuarios[$j] con codigo de salida #NULL = ".$cuenta2['total'];
				$contador_codigo_salida[5][$j] = $cuenta2['total'];
				//echo "contador_codigo_salida[5][$j] = $contador_codigo_salida[5][$j]";
				mysql_free_result($registros2);
			}
			//echo "<br>";	
		}
		
		
		else  if($cursoID == 0)    //todos los usuarios seleccionados en la variable $usuarios[], teniendo en cuenta el grupo de cursos al que pertenecen  
		{
		
			//pasamos los codigos de curso almacenados en el array $cursos a la cadena $cursos_str
			$cursos_str = implode(",", $cursos); 
			
            //echo "<br>cursos_str = $cursos_str";		
		
			//***ACCESOS al SISTEMA
			// contamos el numero historico de accesos de todos los usuarios
			$total_accesos_usuarios = $total_accesos;
			//echo "<br>total_accesos_usuarios = $total_accesos_usuarios";

			
			// seleccionamos el numero historico de accesos por cada usuario
			for ($i=0; $i<count($usuarios); $i++)
			{
				//echo "<br>$usuarios[$i]";
				$sql1="SELECT count(*) AS total FROM logs WHERE user_id = $usuarios[$i] AND curso_id IN ($cursos_str)";
				$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
				$cuenta1=mysql_fetch_assoc($registros1);
				//echo "<br>Total accesos del usuario #$usuarios[$i] = ".$cuenta1['total'];
				$contador_accesos_usuarios[$i] = $cuenta1['total'];
				//echo "contador_accesos_usuarios[$i] = $contador_accesos_usuarios[$i]";
				mysql_free_result($registros1);
			}	
			//echo "<br>";
			
			
			//***reservas		
			// contamos el numero historico de reservas de todos los usuarios
			$total_reservas_usuarios = $total_reservas;
			//echo "<br>total_reservas_usuarios = $total_reservas_usuarios";

			
			// contamos el numero historico de reservas por usuario
			for ($i=0; $i<count($usuarios); $i++)
			{
				//echo "<br>$usuarios[$i]";
				$sql1="SELECT count(*) AS total FROM log_detalles WHERE user_id = $usuarios[$i] AND curso_id IN ($cursos_str) AND confir_resv IS NOT NULL";
				$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
				$cuenta1=mysql_fetch_assoc($registros1);
				//echo "<br>Total reservas del usuario #$usuarios[$i] = ".$cuenta1['total'];
				$contador_reservas_usuarios[$i] = $cuenta1['total'];
				//echo "contador_reservas_usuarios[$i] = $contador_reservas_usuarios[$i]";
				mysql_free_result($registros1);
			}
			//echo "<br>";
			
			
			//***CONEXIONES al LAB
			// contamos el numero historico de conexiones de todos los usuarios
			$total_conexiones_usuarios = $total_conexiones;
			//echo "<br>total_conexiones_usuarios = $total_conexiones_usuarios";

			
			// contamos el numero historico de conexiones por usuario
			for ($i=0; $i<count($usuarios); $i++)
			{
				//echo "<br>$usuarios[$i]";
				$sql1="SELECT count(*) AS total FROM log_detalles WHERE user_id = $usuarios[$i] AND curso_id IN ($cursos_str) AND in_pod IS NOT NULL";
				$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
				$cuenta1=mysql_fetch_assoc($registros1);
				//echo "<br>Total conexiones del usuario #$usuarios[$i] = ".$cuenta1['total'];
				$contador_conexiones_usuarios[$i] = $cuenta1['total'];
				//echo "contador_conexiones_usuarios[$i] = $contador_conexiones_usuarios[$i]";
				mysql_free_result($registros1);
			}
			//echo "<br>";
			
			
			//***ACCESOS de ADMINISTRADOR
			// contamos el numero historico de accesos de administrador
			$sql0="SELECT count(*) AS total FROM logs WHERE user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 )";
			$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
			$cuenta0=mysql_fetch_assoc($registros0);
			//echo "<br>Total accesos admin = ".$cuenta0['total'];	
			$total_accesos_admin = $cuenta0['total'];
			//echo "<br>total_accesos_admin = $total_accesos_admin";
			mysql_free_result($registros0);
			//echo "<br>";

			$sql1="SELECT user_id, username FROM usuarios WHERE admin = 1";
			$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
			$cuenta1=mysql_num_rows($registros1);
			$total_admin = $cuenta1;
			//echo "<br>Num.administradores = $total_admin";

			if ($cuenta1 > 0)
			{
			    $j = 0;
				while ($reg1 = mysql_fetch_array($registros1))
				{	
				   $administradores[$j] = $reg1['user_id'];
				   $nombre_administradores[$j] = $reg1['username'];
				   //echo "<br>administradores[$j] = $administradores[$j]";
				   $j++;
				}
				mysql_free_result($registros1);
								
				// contamos el numero historico de accesos para cada administrador
				for ($i=0; $i<count($administradores); $i++)
				{		
					$sql2="SELECT count(*) AS total FROM logs WHERE user_id = $administradores[$i]";
					$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
					$cuenta2=mysql_fetch_assoc($registros2);
					$contador_accesos_admin[$i] = $cuenta2['total'];
					//echo "<br>total_accesos_admin $nombre_administradores[$i] = $contador_accesos_admin[$i]";
					mysql_free_result($registros2);
				}
			}
			//echo "<br>";
			
			
			//***CODIGOS DE SALIDA
			// contamos el codigo de salida de cada log, para ver como se sale del sistema
			// NULL: final abrupto, 0: tiempo en POD cumplido, 1: salida voluntaria, -1: timeout cumplido, 2: salida voluntaria admin, -2: timeout admin
		   $exit_code[0] = 0;
		   $exit_code[1] = 1;
		   $exit_code[2] = 2;
		   $exit_code[3] = -1;
		   $exit_code[4] = -2;
		   $exit_code[5] = NULL;
		   
			for ($i=0; $i<5; $i++)
			{
				//echo "<br>exit_code = $exit_code[$i]";
				$sql1="SELECT count(*) AS total FROM logs WHERE codigo_salida = $exit_code[$i] AND curso_id IN ($cursos_str)";
				$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
				$cuenta1=mysql_fetch_assoc($registros1);
				//echo "<br>Total salidas con codigo de salida #$exit_code[$i] = ".$cuenta1['total'];
				$total_codigo_salida[$i] = $cuenta1['total'];
				//echo "total_codigo_salida[$i] = $total_codigo_salida[$i]";
				mysql_free_result($registros1);
				
				for ($j=0; $j<count($usuarios); $j++)
				{
					$sql2="SELECT count(*) AS total FROM logs WHERE codigo_salida = $exit_code[$i] AND user_id = $usuarios[$j] AND curso_id IN ($cursos_str)";
					$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
					$cuenta2=mysql_fetch_assoc($registros2);
					//echo "<br> salidas del usuario $usuarios[$j] con codigo de salida #$exit_code[$i] = ".$cuenta2['total'];
					$contador_codigo_salida[$i][$j] = $cuenta2['total'];
					//echo "contador_codigo_salida[$i][$j] = $contador_codigo_salida[$i][$j]";
					mysql_free_result($registros2);		
				}
			}
			//echo "<br>exit_code = NULL";
			$sql1="SELECT count(*) AS total FROM logs WHERE codigo_salida IS NULL AND curso_id IN ($cursos_str)";
			$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
			$cuenta1=mysql_fetch_assoc($registros1);
			//echo "<br>Total salidas con codigo de salida #NULL = ".$cuenta1['total'];
			$total_codigo_salida[5] = $cuenta1['total'];
			//echo "total_codigo_salida[5] = $total_codigo_salida[5]";
			mysql_free_result($registros1);
			
			for ($j=0; $j<count($usuarios); $j++)
			{
				$sql2="SELECT count(*) AS total FROM logs WHERE codigo_salida IS NULL AND user_id = $usuarios[$j] AND curso_id IN ($cursos_str)";
				$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
				$cuenta2=mysql_fetch_assoc($registros2);
				//echo "<br> salidas del usuario $usuarios[$j] con codigo de salida #NULL = ".$cuenta2['total'];
				$contador_codigo_salida[5][$j] = $cuenta2['total'];
				//echo "contador_codigo_salida[5][$j] = $contador_codigo_salida[5][$j]";
				mysql_free_result($registros2);
			}
			//echo "<br>";		
		}
		
		else   //if ($cursoID > 0)    		//el usuario particular seleccionado en la variable $usuarios[], que dependera del Curso o cursos seleccionados
		{
			//***ACCESOS al SISTEMA
			// contamos el numero historico de accesos de todos los usuarios
			$total_accesos_usuarios = $total_accesos;
			//echo "<br>total_accesos_usuarios = $total_accesos_usuarios";

			
			// seleccionamos el numero historico de accesos por cada usuario
			for ($i=0; $i<count($usuarios); $i++)
			{
				//echo "<br>$usuarios[$i]";
				$sql1="SELECT count(*) AS total FROM logs WHERE user_id = $usuarios[$i] AND curso_id = $cursoID";
				$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
				$cuenta1=mysql_fetch_assoc($registros1);
				//echo "<br>Total accesos del usuario #$usuarios[$i] = ".$cuenta1['total'];
				$contador_accesos_usuarios[$i] = $cuenta1['total'];
				//echo "contador_accesos_usuarios[$i] = $contador_accesos_usuarios[$i]";
				mysql_free_result($registros1);
			}	
			//echo "<br>";
			
			
			//***reservas		
			// contamos el numero historico de reservas de todos los usuarios
			$total_reservas_usuarios = $total_reservas;
			//echo "<br>total_reservas_usuarios = $total_reservas_usuarios";

			
			// contamos el numero historico de reservas por usuario
			for ($i=0; $i<count($usuarios); $i++)
			{
				//echo "<br>$usuarios[$i]";
				$sql1="SELECT count(*) AS total FROM log_detalles WHERE user_id = $usuarios[$i] AND curso_id = $cursoID AND confir_resv IS NOT NULL";
				$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
				$cuenta1=mysql_fetch_assoc($registros1);
				//echo "<br>Total reservas del usuario #$usuarios[$i] = ".$cuenta1['total'];
				$contador_reservas_usuarios[$i] = $cuenta1['total'];
				//echo "contador_reservas_usuarios[$i] = $contador_reservas_usuarios[$i]";
				mysql_free_result($registros1);
			}
			//echo "<br>";
			
			
			//***CONEXIONES al LAB
			// contamos el numero historico de conexiones de todos los usuarios
			$total_conexiones_usuarios = $total_conexiones;
			//echo "<br>total_conexiones_usuarios = $total_conexiones_usuarios";

			
			// contamos el numero historico de conexiones por usuario
			for ($i=0; $i<count($usuarios); $i++)
			{
				//echo "<br>$usuarios[$i]";
				$sql1="SELECT count(*) AS total FROM log_detalles WHERE user_id = $usuarios[$i] AND curso_id = $cursoID AND in_pod IS NOT NULL";
				$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
				$cuenta1=mysql_fetch_assoc($registros1);
				//echo "<br>Total conexiones del usuario #$usuarios[$i] = ".$cuenta1['total'];
				$contador_conexiones_usuarios[$i] = $cuenta1['total'];
				//echo "contador_conexiones_usuarios[$i] = $contador_conexiones_usuarios[$i]";
				mysql_free_result($registros1);
			}
			//echo "<br>";
			
			
			//***ACCESOS de ADMINISTRADOR
			// contamos el numero historico de accesos de administrador
			$sql0="SELECT count(*) AS total FROM logs WHERE user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 )";
			$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
			$cuenta0=mysql_fetch_assoc($registros0);
			//echo "<br>Total accesos admin = ".$cuenta0['total'];	
			$total_accesos_admin = $cuenta0['total'];
			//echo "<br>total_accesos_admin = $total_accesos_admin";
			mysql_free_result($registros0);
			//echo "<br>";

			$sql1="SELECT user_id, username FROM usuarios WHERE admin = 1";
			$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
			$cuenta1=mysql_num_rows($registros1);
			$total_admin = $cuenta1;
			//echo "<br>Num.administradores = $total_admin";

			if ($cuenta1 > 0)
			{
			    $j = 0;
				while ($reg1 = mysql_fetch_array($registros1))
				{	
				   $administradores[$j] = $reg1['user_id'];
				   $nombre_administradores[$j] = $reg1['username'];
				   //echo "<br>administradores[$j] = $administradores[$j]";
				   $j++;
				}
				mysql_free_result($registros1);
				
				// contamos el numero historico de accesos para cada administrador
				for ($i=0; $i<count($administradores); $i++)
				{		
					$sql2="SELECT count(*) AS total FROM logs WHERE user_id = $administradores[$i]";
					$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
					$cuenta2=mysql_fetch_assoc($registros2);
					$contador_accesos_admin[$i] = $cuenta2['total'];
					//echo "<br>total_accesos_admin $nombre_administradores[$i] = $contador_accesos_admin[$i]";
					mysql_free_result($registros2);
				}
			}
			//echo "<br>";
						
			
			//***CODIGOS DE SALIDA
			// contamos el codigo de salida de cada log, para ver como se sale del sistema
			// 0: tiempo en POD cumplido, 1: salida voluntaria, 2: salida voluntaria admin, -1: timeout cumplido, -2: timeout admin, NULL: final abrupto
		   $exit_code[0] = 0;
		   $exit_code[1] = 1;
		   $exit_code[2] = 2;
		   $exit_code[3] = -1;
		   $exit_code[4] = -2;
		   $exit_code[5] = NULL;
		   
			for ($i=0; $i<5; $i++)
			{
				//echo "<br>exit_code = $exit_code[$i]";
				$sql1="SELECT count(*) AS total FROM logs WHERE codigo_salida = $exit_code[$i] AND curso_id = $cursoID";
				$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
				$cuenta1=mysql_fetch_assoc($registros1);
				//echo "<br>Total salidas con codigo de salida #$exit_code[$i] = ".$cuenta1['total'];
				$total_codigo_salida[$i] = $cuenta1['total'];
				//echo "total_codigo_salida[$i] = $total_codigo_salida[$i]";
				mysql_free_result($registros1);
				
				for ($j=0; $j<count($usuarios); $j++)
				{
					$sql2="SELECT count(*) AS total FROM logs WHERE codigo_salida = $exit_code[$i] AND user_id = $usuarios[$j] AND curso_id = $cursoID";
					$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
					$cuenta2=mysql_fetch_assoc($registros2);
					//echo "<br> salidas del usuario $usuarios[$j] con codigo de salida #$exit_code[$i] = ".$cuenta2['total'];
					$contador_codigo_salida[$i][$j] = $cuenta2['total'];
					//echo "contador_codigo_salida[$i][$j] = $contador_codigo_salida[$i][$j]";
					mysql_free_result($registros2);		
				}
			}
			//echo "<br>exit_code = NULL";
			$sql1="SELECT count(*) AS total FROM logs WHERE codigo_salida IS NULL AND curso_id = $cursoID";
			$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
			$cuenta1=mysql_fetch_assoc($registros1);
			//echo "<br>Total salidas con codigo de salida #NULL = ".$cuenta1['total'];
			$total_codigo_salida[5] = $cuenta1['total'];
			//echo "total_codigo_salida[5] = $total_codigo_salida[5]";
			mysql_free_result($registros1);
			
			for ($j=0; $j<count($usuarios); $j++)
			{
				$sql2="SELECT count(*) AS total FROM logs WHERE codigo_salida IS NULL AND user_id = $usuarios[$j] AND curso_id = $cursoID";
				$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
				$cuenta2=mysql_fetch_assoc($registros2);
				//echo "<br> salidas del usuario $usuarios[$j] con codigo de salida #NULL = ".$cuenta2['total'];
				$contador_codigo_salida[5][$j] = $cuenta2['total'];
				//echo "contador_codigo_salida[5][$j] = $contador_codigo_salida[5][$j]";
				mysql_free_result($registros2);
			}
			//echo "<br>";		
		}
		
?>

        <input type="text" name="estadisticasusuarios" value=" Estad&iacute;sticas por usuarios" 
 		id="estadisticasusuarios" style="width: 230px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:12pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  

        <center>
		   <table border="1" cellpadding="10" cellspacing="0" width="60%" class="thin sortable draggable">
				<tr>
					<th width="12%"></th>
					<th bgcolor="khaki" width="14%"> <?php echo "N&ordm; accesos del Usuario"; ?>  </th>
					<th bgcolor="khaki" width="12%"> <?php echo "N&ordm; reservas del Usuario"; ?>  </th>					
					<th bgcolor="khaki" width="12%"> <?php echo "N&ordm; conexiones del Usuario"; ?>  </th>	
					<th bgcolor="khaki" width="12%"> <?php echo "Usuario Administrador"; ?>  </th>						
				</tr>
	
<?php        	for ($i=0; $i<count($usuarios); $i++){    ?>

					<tr>
						<th bgcolor="aquamarine" width="14%" align="center"> <?php echo "$nombre_usuarios[$i]"; ?>  </th>
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php echo "$contador_accesos_usuarios[$i]"; ?>  </td>	
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php echo "$contador_reservas_usuarios[$i]"; ?>  </td>
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php echo "$contador_conexiones_usuarios[$i]"; ?>  </td>
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php if ($admins[$i]==1) echo " SI"; else echo " NO"; ?>  </td>				
					</tr>
					
					
<?php			}  ?>		

				</tr>
					<th width="14%" bgcolor="#eeeeee">  TOTAL </th>			
					<td width="12%" bgcolor="#eeeeee" align="right">  <?php $suma = array_sum($contador_accesos_usuarios); echo " $suma"; ?>  </td>						
					<td width="12%" bgcolor="#eeeeee" align="right"> <?php $suma = array_sum($contador_reservas_usuarios); echo " $suma"; ?>  </td>
					<td width="12%" bgcolor="#eeeeee" align="right">  <?php $suma = array_sum($contador_conexiones_usuarios); echo " $suma"; ?>  </td>
					<td width="12%" bgcolor="#eeeeee" align="right">  <?php $j=0; for ($i=0;$i<count($lista_admins);$i++) if($lista_admins[$i]==1) $j++; echo " $j"; ?></td>
				</tr>	

			</table>
		</center>
		<br>



		<input type="text" name="estadisticasCodigoSalida" value=" Estad&iacute;sticas por C&oacute;digo de Salida" 
 		id="estadisticasCodigoSalida" style="width: 300px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:12pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  

        <center>
		   <table border="1" cellpadding="10" cellspacing="0" width="80%" class="thin sortable draggable">
				<tr>
					<th width="15%"></th>
					<th bgcolor="khaki" width="12%"> <?php echo "Tiempo en POD <br><u>cumplido</u>"; echo "<br>($exit_code[0])"; ?>  </th>
					<th bgcolor="khaki" width="12%"> <?php echo "Salida voluntaria <br><u>usuario</u>"; echo "<br>($exit_code[1])"; ?>  </th>					
					<th bgcolor="khaki" width="12%"> <?php echo "Timeout <br><u>usuario</u>"; echo "<br>($exit_code[2])"; ?>  </th>	
					<th bgcolor="khaki" width="12%"> <?php echo "Salida voluntaria <br><u>administrador</u>"; echo "<br>($exit_code[3])"; ?>  </th>
					<th bgcolor="khaki" width="12%"> <?php echo "Timeout <br><u>administrador</u>"; echo "<br>($exit_code[4])"; ?>  </th>	
					<th bgcolor="khaki" width="12%"> <?php echo "Salida <br><u>abrupta</u>"; echo "<br>(NULL)"; ?>  </th>
				</tr>
	
<?php        	for ($j=0; $j<count($usuarios); $j++){    ?>
					
					<tr>
						<th bgcolor="aquamarine" width="15%" align="center"> <?php echo "$nombre_usuarios[$j]"; ?>  </th>
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php $cuenta = $contador_codigo_salida[0][$j]; echo "$cuenta"; ?>  </td>	
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php $cuenta = $contador_codigo_salida[1][$j]; echo "$cuenta"; ?>  </td>
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php $cuenta = $contador_codigo_salida[2][$j]; echo "$cuenta"; ?>  </td>
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php $cuenta = $contador_codigo_salida[3][$j]; echo "$cuenta"; ?>  </td>
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php $cuenta = $contador_codigo_salida[4][$j]; echo "$cuenta"; ?>  </td>
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php $cuenta = $contador_codigo_salida[5][$j]; echo "$cuenta"; ?>  </td>						
					</tr>		
					
<?php			}  ?>	

				<tr>
					<th width="15%" bgcolor="#eeeeee">  TOTAL </th>			
					<td width="15%" bgcolor="#eeeeee" align="right">  <?php $suma = array_sum($contador_codigo_salida[0]); echo " $suma"; ?>  </td>						
					<td width="15%" bgcolor="#eeeeee" align="right">  <?php $suma = array_sum($contador_codigo_salida[1]); echo " $suma"; ?>  </td>
					<td width="15%" bgcolor="#eeeeee" align="right">  <?php $suma = array_sum($contador_codigo_salida[2]); echo " $suma"; ?>  </td>
					<td width="15%" bgcolor="#eeeeee" align="right">  <?php $suma = array_sum($contador_codigo_salida[3]); echo " $suma"; ?>  </td>
					<td width="15%" bgcolor="#eeeeee" align="right">  <?php $suma = array_sum($contador_codigo_salida[4]); echo " $suma"; ?>  </td>
					<td width="15%" bgcolor="#eeeeee" align="right">  <?php $suma = array_sum($contador_codigo_salida[5]); echo " $suma"; ?>  </td>
				</tr>	

			</table>
		</center>
		<br>
		
<?php 


	}
	
	
	echo "<hr>";
		
	
	//****ESTADISTICAS POR TIEMPOS****//
	if ($stat_tiempos == 1)
	{
		//inicializamos variable de almacenamiento para los dias de la semana
		$TotalDiasSemana = array(0,0,0,0,0,0,0);
		$ArraySemana = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado');
		
		//inicializamos variable de almacenamiento para las horas de conexion
		$TotalIntervalos = array(0,0,0,0,0,0,0,0,0,0,0,0);
		$ArrayIntervalos = array('0 - 2 h','2 - 4 h','4 - 6 h','6 - 8 h','8 - 10 h','10 - 12 h','12 - 14 h','14 - 16 h','16 - 18 h','18 - 20 h','20 - 22 h','22 - 24 h');	

						
	    if ($usuarioID=="" && $cursoID=="")      //o sea, NO tenemos seleccionado ningun curso ni ningun alumno, de modo que las estadisticas son generales del sistema
		{
		    if ($tiempoID==0)        //estadisticas por semanas, dias y horas
			{
				//buscamos el Lunes de la semana actual y el de la semana anterior
				$dia_sem = date('w', strtotime($hoy));
				//echo "<br>dia_sem = $dia_sem";

				$dia_semana = $dia_sem - 1;
				$Lunes_ref = date('Y-m-d',strtotime('-'.$dia_semana.' days', strtotime($f_ahora)));
				$Lunes_ant = date('Y-m-d',strtotime('-7 days', strtotime($Lunes_ref)));
				
				//***CONEXIONES AL LAB - SEMANA ACTUAL
				// contamos el numero historico de conexiones la semana actual
				//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ref'";
				$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ref' AND in_pod IS NOT NULL";
				$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
				$cuenta0=mysql_fetch_assoc($registros0);
				//echo "<br>Total conexiones en la semana actual = ".$cuenta0['total'];	
				$total_conexiones_semana_actual = $cuenta0['total'];
				//echo "<br>total_conexiones_semana_actual = $total_conexiones_semana_actual";
				mysql_free_result($registros0);

				
				//***CONEXIONES AL LAB - SEMANA ANTERIOR
				// contamos el numero historico de conexiones la semana anterior
				//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant' AND entrada < '$Lunes_ref'";
				$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant' AND confir_resv < '$Lunes_ref' AND in_pod IS NOT NULL";
				$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
				$cuenta0=mysql_fetch_assoc($registros0);
				//echo "<br>Total conexiones en la semana anterior = ".$cuenta0['total'];	
				$total_conexiones_semana_anterior = $cuenta0['total'];
				//echo "<br>total_conexiones_semana_anterior = $total_conexiones_semana_anterior";
				mysql_free_result($registros0);		
				
				
				//***CONEXIONES AL LAB - HACE 2 SEMANAS
				// contamos el numero historico de conexiones la semana anterior
				$Lunes_ant_2 = date('Y-m-d',strtotime('-14 days', strtotime($Lunes_ref)));
				//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_2' AND entrada < '$Lunes_ant'";
				$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_2' AND confir_resv < '$Lunes_ant' AND in_pod IS NOT NULL";
				$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
				$cuenta0=mysql_fetch_assoc($registros0);
				//echo "<br>Total conexiones hace 2 semanas = ".$cuenta0['total'];	
				$total_conexiones_semana_anterior_2 = $cuenta0['total'];
				//echo "<br>total_conexiones_semana_anterior_2 = $total_conexiones_semana_anterior_2";
				mysql_free_result($registros0);
				

				//***CONEXIONES AL LAB - HACE 3 SEMANAS
				// contamos el numero historico de conexiones la semana anterior
				$Lunes_ant_3 = date('Y-m-d',strtotime('-21 days', strtotime($Lunes_ref)));
				//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_3' AND entrada < '$Lunes_ant_2'";
				$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_3' AND confir_resv < '$Lunes_ant_2' AND in_pod IS NOT NULL";
				$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
				$cuenta0=mysql_fetch_assoc($registros0);
				//echo "<br>Total conexiones hace 3 semanas = ".$cuenta0['total'];	
				$total_conexiones_semana_anterior_3 = $cuenta0['total'];
				//echo "<br>total_conexiones_semana_anterior_3 = $total_conexiones_semana_anterior_3";
				mysql_free_result($registros0);


				//***CONEXIONES AL LAB - HACE 4 SEMANAS
				// contamos el numero historico de conexiones la semana anterior
				$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
				//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_4' AND entrada < '$Lunes_ant_3'";
				$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_4' AND confir_resv < '$Lunes_ant_3' AND in_pod IS NOT NULL";
				$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
				$cuenta0=mysql_fetch_assoc($registros0);
				//echo "<br>Total conexiones hace 4 semanas = ".$cuenta0['total'];	
				$total_conexiones_semana_anterior_4 = $cuenta0['total'];
				//echo "<br>total_conexiones_semana_anterior_4 = $total_conexiones_semana_anterior_4";
				mysql_free_result($registros0);


				//*CONEXIONES AL LAB - todas las ANTERIORES A HACE 4 SEMANAS 
				// contamos el numero historico de conexiones la semana anterior
				////$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
				//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada < '$Lunes_ant_4'";
				$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv < '$Lunes_ant_4' AND in_pod IS NOT NULL";
				$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
				$cuenta0=mysql_fetch_assoc($registros0);
				//echo "<br>Total conexiones antiguas = ".$cuenta0['total'];	
				$total_conexiones_antiguas = $cuenta0['total'];
				//echo "<br>total_conexiones_antiguas = $total_conexiones_antiguas";
				mysql_free_result($registros0);
		
				//echo "<br><br>";

				
				//******************************************************//
				//***CONEXIONES AL LAB POR TIEMPOS DE ENTRADA AL LAB	
				
				// seleccionamos las fechas de entrada de todas las conexiones
				//$sql="SELECT entrada FROM logs WHERE num_pod_lab IS NOT NULL";
				$sql="SELECT confir_resv FROM log_detalles WHERE in_pod IS NOT NULL";
				$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
				$count = mysql_num_rows($registros);
				$j = 0;
					if ($count > 0)
					{
						while ($reg = mysql_fetch_array($registros))
						{
						   $conexiones[$j] = $reg['confir_resv'];
						   $j++;
						}
					
						//inicializamos variable de almacenamiento para los dias de la semana
						$TotalDiasSemana = array(0,0,0,0,0,0,0);
						$ArraySemana = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado');
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$DayOfWeek = date( "w", strtotime($conexiones[$i]));
							switch ($DayOfWeek) {
								case 0:     //Domingo
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 1:     //Lunes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 2:     //Martes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 3:     //Mi&eacute;rcoles
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 4:     //Jueves
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 5:     //Viernes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 6:     //S&aacute;bado
									$TotalDiasSemana[$DayOfWeek]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por dias de la semana
						//for ($i=0; $i<7; $i++)
						//{
							//echo "$ArraySemana[$i] = $TotalDiasSemana[$i]  ";
						//}
						
						//echo "<br><br>";
						
						
						//inicializamos variable de almacenamiento para las horas de conexion
						$TotalIntervalos = array(0,0,0,0,0,0,0,0,0,0,0,0);
						$ArrayIntervalos = array('0 - 2 h','2 - 4 h','4 - 6 h','6 - 8 h','8 - 10 h','10 - 12 h','12 - 14 h','14 - 16 h','16 - 18 h','18 - 20 h','20 - 22 h','22 - 24 h');	
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$Intervalo = date( "H", strtotime($conexiones[$i]));
							$indice = floor($Intervalo / 2);
							switch ($indice) 
							{
								case 0:    
									$TotalIntervalos[$indice]++;
									break;
								case 1:    
									$TotalIntervalos[$indice]++;
									break;
								case 2:    
									$TotalIntervalos[$indice]++;
									break;
								case 3:    
									$TotalIntervalos[$indice]++;
									break;
								case 4:    
									$TotalIntervalos[$indice]++;
									break;
								case 5:    
									$TotalIntervalos[$indice]++;
									break;
								case 6:    
									$TotalIntervalos[$indice]++;
									break;
								case 7:    
									$TotalIntervalos[$indice]++;
									break;
								case 8:    
									$TotalIntervalos[$indice]++;
									break;
								case 9:    
									$TotalIntervalos[$indice]++;
									break;
								case 10:    
									$TotalIntervalos[$indice]++;
									break;
								case 11:    
									$TotalIntervalos[$indice]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por intervalos horarios diarios
						//for ($i=0; $i<12; $i++)
						//{
							//echo "$ArrayIntervalos[$i] = $TotalIntervalos[$i]; &nbsp;&nbsp;";
						//}

					}  
					mysql_free_result($registros);
					
					//echo "<br><br>";
			
			}
			else if ($tiempoID==1)   //estadisticas por semanas
			{
				//***HISTORICO DE CONEXIONES AL LAB
				// contamos el numero historico de conexiones en todos los usuarios
				$total_conexiones_historico = $total_conexiones;
				//echo "<br>total_conexiones_historico = $total_conexiones_historico";

				//buscamos el Lunes de la semana actual y el de la semana anterior
				$dia_sem = date('w', strtotime($hoy));
				//echo "<br>dia_sem = $dia_sem";

				$dia_semana = $dia_sem - 1;
				$Lunes_ref = date('Y-m-d',strtotime('-'.$dia_semana.' days', strtotime($f_ahora)));
				$Lunes_ant = date('Y-m-d',strtotime('-7 days', strtotime($Lunes_ref)));
				
				//***CONEXIONES AL LAB - SEMANA ACTUAL
				// contamos el numero historico de conexiones la semana actual
				//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ref'";
				$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ref' AND in_pod IS NOT NULL";
				$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
				$cuenta0=mysql_fetch_assoc($registros0);
				//echo "<br>Total conexiones en la semana actual = ".$cuenta0['total'];	
				$total_conexiones_semana_actual = $cuenta0['total'];
				//echo "<br>total_conexiones_semana_actual = $total_conexiones_semana_actual";
				mysql_free_result($registros0);

				
				//***CONEXIONES AL LAB - SEMANA ANTERIOR
				// contamos el numero historico de conexiones la semana anterior
				//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant' AND entrada < '$Lunes_ref'";
				$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant' AND confir_resv < '$Lunes_ref' AND in_pod IS NOT NULL";
				$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
				$cuenta0=mysql_fetch_assoc($registros0);
				//echo "<br>Total conexiones en la semana anterior = ".$cuenta0['total'];	
				$total_conexiones_semana_anterior = $cuenta0['total'];
				//echo "<br>total_conexiones_semana_anterior = $total_conexiones_semana_anterior";
				mysql_free_result($registros0);		
				
				
				//***CONEXIONES AL LAB - HACE 2 SEMANAS
				// contamos el numero historico de conexiones la semana anterior
				$Lunes_ant_2 = date('Y-m-d',strtotime('-14 days', strtotime($Lunes_ref)));
				//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_2' AND entrada < '$Lunes_ant'";
				$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_2' AND confir_resv < '$Lunes_ant' AND in_pod IS NOT NULL";
				$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
				$cuenta0=mysql_fetch_assoc($registros0);
				//echo "<br>Total conexiones hace 2 semanas = ".$cuenta0['total'];	
				$total_conexiones_semana_anterior_2 = $cuenta0['total'];
				//echo "<br>total_conexiones_semana_anterior_2 = $total_conexiones_semana_anterior_2";
				mysql_free_result($registros0);
				

				//***CONEXIONES AL LAB - HACE 3 SEMANAS
				// contamos el numero historico de conexiones la semana anterior
				$Lunes_ant_3 = date('Y-m-d',strtotime('-21 days', strtotime($Lunes_ref)));
				//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_3' AND entrada < '$Lunes_ant_2'";
				$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_3' AND confir_resv < '$Lunes_ant_2' AND in_pod IS NOT NULL";
				$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
				$cuenta0=mysql_fetch_assoc($registros0);
				//echo "<br>Total conexiones hace 3 semanas = ".$cuenta0['total'];	
				$total_conexiones_semana_anterior_3 = $cuenta0['total'];
				//echo "<br>total_conexiones_semana_anterior_3 = $total_conexiones_semana_anterior_3";
				mysql_free_result($registros0);


				//***CONEXIONES AL LAB - HACE 4 SEMANAS
				// contamos el numero historico de conexiones la semana anterior
				$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
				//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_4' AND entrada < '$Lunes_ant_3'";
				$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_4' AND confir_resv < '$Lunes_ant_3' AND in_pod IS NOT NULL";
				$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
				$cuenta0=mysql_fetch_assoc($registros0);
				//echo "<br>Total conexiones hace 4 semanas = ".$cuenta0['total'];	
				$total_conexiones_semana_anterior_4 = $cuenta0['total'];
				//echo "<br>total_conexiones_semana_anterior_4 = $total_conexiones_semana_anterior_4";
				mysql_free_result($registros0);


				//*CONEXIONES AL LAB - todas las ANTERIORES A HACE 4 SEMANAS 
				// contamos el numero historico de conexiones la semana anterior
				////$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
				//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada < '$Lunes_ant_4'";
				$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv < '$Lunes_ant_4' AND in_pod IS NOT NULL";
				$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
				$cuenta0=mysql_fetch_assoc($registros0);
				//echo "<br>Total conexiones antiguas = ".$cuenta0['total'];	
				$total_conexiones_antiguas = $cuenta0['total'];
				//echo "<br>total_conexiones_antiguas = $total_conexiones_antiguas";
				mysql_free_result($registros0);
		
				//echo "<br><br>";
			}
			
			else if ($tiempoID==2)      //estadisticas por dias
			{
				//******************************************************//
				//***CONEXIONES AL LAB POR TIEMPOS DE ENTRADA AL LAB	
				
				// seleccionamos las fechas de entrada de todas las conexiones
				//$sql="SELECT entrada FROM logs WHERE num_pod_lab IS NOT NULL";
				$sql="SELECT confir_resv FROM log_detalles WHERE in_pod IS NOT NULL";
				$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
				$count = mysql_num_rows($registros);
				$j = 0;
					if ($count > 0)
					{
						while ($reg = mysql_fetch_array($registros))
						{
						   $conexiones[$j] = $reg['confir_resv'];
						   $j++;
						}
					
						//inicializamos variable de almacenamiento para los dias de la semana
						$TotalDiasSemana = array(0,0,0,0,0,0,0);
						$ArraySemana = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado');
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$DayOfWeek = date( "w", strtotime($conexiones[$i]));
							switch ($DayOfWeek) {
								case 0:     //Domingo
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 1:     //Lunes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 2:     //Martes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 3:     //Mi&eacute;rcoles
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 4:     //Jueves
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 5:     //Viernes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 6:     //S&aacute;bado
									$TotalDiasSemana[$DayOfWeek]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por dias de la semana
						//for ($i=0; $i<7; $i++)
						//{
							//echo "$ArraySemana[$i] = $TotalDiasSemana[$i]  ";
						//}
						
					}  
					mysql_free_result($registros);
					
					//echo "<br><br>";
			
			}
			
			else // if ($tiempoID==3)      //estadisticas por horas
			{
				//***CONEXIONES AL LAB POR TIEMPOS DE ENTRADA AL LAB - como en el punto anterior
				// seleccionamos las fechas de entrada de todas las conexiones
				//$sql="SELECT entrada FROM logs WHERE num_pod_lab IS NOT NULL";
				$sql="SELECT confir_resv FROM log_detalles WHERE in_pod IS NOT NULL";
				$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
				$count = mysql_num_rows($registros);
				$j = 0;
					if ($count > 0)
					{
						while ($reg = mysql_fetch_array($registros))
						{
						   $conexiones[$j] = $reg['confir_resv'];
						   $j++;
						}
										
						//inicializamos variable de almacenamiento para las horas de conexion
						$TotalIntervalos = array(0,0,0,0,0,0,0,0,0,0,0,0);
						$ArrayIntervalos = array('0 - 2 h','2 - 4 h','4 - 6 h','6 - 8 h','8 - 10 h','10 - 12 h','12 - 14 h','14 - 16 h','16 - 18 h','18 - 20 h','20 - 22 h','22 - 24 h');	
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$Intervalo = date( "H", strtotime($conexiones[$i]));
							$indice = floor($Intervalo / 2);
							switch ($indice) 
							{
								case 0:    
									$TotalIntervalos[$indice]++;
									break;
								case 1:    
									$TotalIntervalos[$indice]++;
									break;
								case 2:    
									$TotalIntervalos[$indice]++;
									break;
								case 3:    
									$TotalIntervalos[$indice]++;
									break;
								case 4:    
									$TotalIntervalos[$indice]++;
									break;
								case 5:    
									$TotalIntervalos[$indice]++;
									break;
								case 6:    
									$TotalIntervalos[$indice]++;
									break;
								case 7:    
									$TotalIntervalos[$indice]++;
									break;
								case 8:    
									$TotalIntervalos[$indice]++;
									break;
								case 9:    
									$TotalIntervalos[$indice]++;
									break;
								case 10:    
									$TotalIntervalos[$indice]++;
									break;
								case 11:    
									$TotalIntervalos[$indice]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por intervalos horarios diarios
						//for ($i=0; $i<12; $i++)
						//{
							//echo "$ArrayIntervalos[$i] = $TotalIntervalos[$i]; &nbsp;&nbsp;";
						//}
						
					}  
					mysql_free_result($registros);
					
					//echo "<br><br>";
			}		
		}
		
		
		else if ($usuarioID=="")   //implicitamente $cursoID!="", o sea, tenemos seleccionado un curso
		{
		
			if($cursoID == 0)    //un grupo de cursos, almacenados en la variable $cursos
			{
				//pasamos los codigos de curso almacenados en el array $cursos a la cadena $cursos_str
				$cursos_str = implode(",", $cursos); 
				
				//echo "<br>cursos_str = $cursos_str";
			
		
		        if ($tiempoID==0)       //estadisticas por semanas, dias y horas
				{
					//buscamos el Lunes de la semana actual y el de la semana anterior
					$dia_sem = date('w', strtotime($hoy));
					//echo "<br>dia_sem = $dia_sem";

					$dia_semana = $dia_sem - 1;
					$Lunes_ref = date('Y-m-d',strtotime('-'.$dia_semana.' days', strtotime($f_ahora)));
					$Lunes_ant = date('Y-m-d',strtotime('-7 days', strtotime($Lunes_ref)));
					
					//***CONEXIONES AL LAB - SEMANA ACTUAL
					// contamos el numero historico de conexiones la semana actual
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ref' AND curso_id IN ($cursos_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ref' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana actual = ".$cuenta0['total'];	
					$total_conexiones_semana_actual = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_actual = $total_conexiones_semana_actual";
					mysql_free_result($registros0);

					
					//***CONEXIONES AL LAB - SEMANA ANTERIOR
					// contamos el numero historico de conexiones la semana anterior
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant' AND entrada < '$Lunes_ref' AND curso_id IN ($cursos_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant' AND confir_resv < '$Lunes_ref' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana anterior = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior = $total_conexiones_semana_anterior";
					mysql_free_result($registros0);		
					
					
					//***CONEXIONES AL LAB - HACE 2 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_2 = date('Y-m-d',strtotime('-14 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_2' AND entrada < '$Lunes_ant' AND curso_id IN ($cursos_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_2' AND confir_resv < '$Lunes_ant' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 2 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_2 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_2 = $total_conexiones_semana_anterior_2";
					mysql_free_result($registros0);
					

					//***CONEXIONES AL LAB - HACE 3 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_3 = date('Y-m-d',strtotime('-21 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_3' AND entrada < '$Lunes_ant_2' AND curso_id IN ($cursos_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_3' AND confir_resv < '$Lunes_ant_2' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 3 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_3 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_3 = $total_conexiones_semana_anterior_3";
					mysql_free_result($registros0);


					//***CONEXIONES AL LAB - HACE 4 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_4' AND entrada < '$Lunes_ant_3' AND curso_id IN ($cursos_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_4' AND confir_resv < '$Lunes_ant_3' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 4 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_4 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_4 = $total_conexiones_semana_anterior_4";
					mysql_free_result($registros0);

					
					//*CONEXIONES AL LAB - todas las ANTERIORES A HACE 4 SEMANAS 
					// contamos el numero historico de conexiones la semana anterior
					////$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada < '$Lunes_ant_4' AND curso_id IN ($cursos_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv < '$Lunes_ant_4' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones antiguas = ".$cuenta0['total'];	
					$total_conexiones_antiguas = $cuenta0['total'];
					//echo "<br>total_conexiones_antiguas = $total_conexiones_antiguas";
					mysql_free_result($registros0);
		
					//echo "<br><br>";
					
					
					
					//******************************************************//
					//***CONEXIONES AL LAB POR TIEMPOS DE ENTRADA AL LAB	
					
					// seleccionamos las fechas de entrada de todas las conexiones
					//$sql="SELECT entrada FROM logs WHERE num_pod_lab IS NOT NULL AND curso_id IN ($cursos_str)";
					$sql="SELECT confir_resv FROM log_detalles WHERE in_pod IS NOT NULL AND curso_id IN ($cursos_str)";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count = mysql_num_rows($registros);
					$j = 0;
					if ($count > 0)
					{
						while ($reg = mysql_fetch_array($registros))
						{
						   $conexiones[$j] = $reg['confir_resv'];
						   $j++;
						}
					
						//inicializamos variable de almacenamiento para los dias de la semana
						$TotalDiasSemana = array(0,0,0,0,0,0,0);
						$ArraySemana = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado');
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$DayOfWeek = date( "w", strtotime($conexiones[$i]));
							switch ($DayOfWeek) {
								case 0:     //Domingo
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 1:     //Lunes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 2:     //Martes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 3:     //Mi&eacute;rcoles
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 4:     //Jueves
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 5:     //Viernes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 6:     //S&aacute;bado
									$TotalDiasSemana[$DayOfWeek]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por dias de la semana
						//for ($i=0; $i<7; $i++)
						//{
							//echo "$ArraySemana[$i] = $TotalDiasSemana[$i]  ";
						//}
						
						//echo "<br><br>";
						
						
						//inicializamos variable de almacenamiento para las horas de conexion
						$TotalIntervalos = array(0,0,0,0,0,0,0,0,0,0,0,0);
						$ArrayIntervalos = array('0 - 2 h','2 - 4 h','4 - 6 h','6 - 8 h','8 - 10 h','10 - 12 h','12 - 14 h','14 - 16 h','16 - 18 h','18 - 20 h','20 - 22 h','22 - 24 h');	
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$Intervalo = date( "H", strtotime($conexiones[$i]));
							$indice = floor($Intervalo / 2);
							switch ($indice) 
							{
								case 0:    
									$TotalIntervalos[$indice]++;
									break;
								case 1:    
									$TotalIntervalos[$indice]++;
									break;
								case 2:    
									$TotalIntervalos[$indice]++;
									break;
								case 3:    
									$TotalIntervalos[$indice]++;
									break;
								case 4:    
									$TotalIntervalos[$indice]++;
									break;
								case 5:    
									$TotalIntervalos[$indice]++;
									break;
								case 6:    
									$TotalIntervalos[$indice]++;
									break;
								case 7:    
									$TotalIntervalos[$indice]++;
									break;
								case 8:    
									$TotalIntervalos[$indice]++;
									break;
								case 9:    
									$TotalIntervalos[$indice]++;
									break;
								case 10:    
									$TotalIntervalos[$indice]++;
									break;
								case 11:    
									$TotalIntervalos[$indice]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por intervalos horarios diarios
						//for ($i=0; $i<12; $i++)
						//{
							//echo "$ArrayIntervalos[$i] = $TotalIntervalos[$i]; &nbsp;&nbsp;";
						//}
						
					}  
					mysql_free_result($registros);
					
					//echo "<br><br>";				
				
				}
				else if ($tiempoID==1)   //estadisticas por semanas
				{
					//***HISTORICO DE CONEXIONES AL LAB
					// contamos el numero historico de conexiones en todos los usuarios
					$total_conexiones_historico = $total_conexiones;
					//echo "<br>total_conexiones_historico = $total_conexiones_historico";

					//buscamos el Lunes de la semana actual y el de la semana anterior
					$dia_sem = date('w', strtotime($hoy));
					//echo "<br>dia_sem = $dia_sem";

					$dia_semana = $dia_sem - 1;
					$Lunes_ref = date('Y-m-d',strtotime('-'.$dia_semana.' days', strtotime($f_ahora)));
					$Lunes_ant = date('Y-m-d',strtotime('-7 days', strtotime($Lunes_ref)));
					
					//***CONEXIONES AL LAB - SEMANA ACTUAL
					// contamos el numero historico de conexiones la semana actual
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ref' AND curso_id IN ($cursos_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ref' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana actual = ".$cuenta0['total'];	
					$total_conexiones_semana_actual = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_actual = $total_conexiones_semana_actual";
					mysql_free_result($registros0);

					
					//***CONEXIONES AL LAB - SEMANA ANTERIOR
					// contamos el numero historico de conexiones la semana anterior
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant' AND entrada < '$Lunes_ref' AND curso_id IN ($cursos_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant' AND confir_resv < '$Lunes_ref' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana anterior = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior = $total_conexiones_semana_anterior";
					mysql_free_result($registros0);		
					
					
					//***CONEXIONES AL LAB - HACE 2 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_2 = date('Y-m-d',strtotime('-14 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_2' AND entrada < '$Lunes_ant' AND curso_id IN ($cursos_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_2' AND confir_resv < '$Lunes_ant' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 2 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_2 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_2 = $total_conexiones_semana_anterior_2";
					mysql_free_result($registros0);
					

					//***CONEXIONES AL LAB - HACE 3 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_3 = date('Y-m-d',strtotime('-21 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_3' AND entrada < '$Lunes_ant_2' AND curso_id IN ($cursos_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_3' AND confir_resv < '$Lunes_ant_2' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 3 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_3 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_3 = $total_conexiones_semana_anterior_3";
					mysql_free_result($registros0);


					//***CONEXIONES AL LAB - HACE 4 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_4' AND entrada < '$Lunes_ant_3' AND curso_id IN ($cursos_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_4' AND confir_resv < '$Lunes_ant_3' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 4 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_4 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_4 = $total_conexiones_semana_anterior_4";
					mysql_free_result($registros0);


					//*CONEXIONES AL LAB - todas las ANTERIORES A HACE 4 SEMANAS 
					// contamos el numero historico de conexiones la semana anterior
					////$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada < '$Lunes_ant_4' AND curso_id IN ($cursos_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv < '$Lunes_ant_4' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones antiguas = ".$cuenta0['total'];	
					$total_conexiones_antiguas = $cuenta0['total'];
					//echo "<br>total_conexiones_antiguas = $total_conexiones_antiguas";
					mysql_free_result($registros0);
		
					//echo "<br><br>";
				}
				
				else if ($tiempoID==2)      //estadisticas por dias
				{
					//******************************************************//
					//***CONEXIONES AL LAB POR TIEMPOS DE ENTRADA AL LAB	
					
					// seleccionamos las fechas de entrada de todas las conexiones
					//$sql="SELECT entrada FROM logs WHERE num_pod_lab IS NOT NULL AND curso_id IN ($cursos_str)";			
					$sql="SELECT confir_resv FROM log_detalles WHERE in_pod IS NOT NULL AND curso_id IN ($cursos_str)";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count = mysql_num_rows($registros);
					$j = 0;
					if ($count > 0)
					{
						while ($reg = mysql_fetch_array($registros))
						{
						   $conexiones[$j] = $reg['confir_resv'];
						   $j++;
						}
					
						//inicializamos variable de almacenamiento para los dias de la semana
						$TotalDiasSemana = array(0,0,0,0,0,0,0);
						$ArraySemana = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado');
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$DayOfWeek = date( "w", strtotime($conexiones[$i]));
							switch ($DayOfWeek) {
								case 0:     //Domingo
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 1:     //Lunes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 2:     //Martes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 3:     //Mi&eacute;rcoles
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 4:     //Jueves
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 5:     //Viernes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 6:     //S&aacute;bado
									$TotalDiasSemana[$DayOfWeek]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por dias de la semana
						//for ($i=0; $i<7; $i++)
						//{
							//echo "$ArraySemana[$i] = $TotalDiasSemana[$i]  ";
						//}

					}  
					mysql_free_result($registros);
					
					//echo "<br><br>";
				
				}
				
				else   //if ($tiempoID==3)      //estadisticas por horas
				{
					//***CONEXIONES AL LAB POR TIEMPOS DE ENTRADA AL LAB - como en el punto anterior
					// seleccionamos las fechas de entrada de todas las conexiones
					//$sql="SELECT entrada FROM logs WHERE num_pod_lab IS NOT NULL AND curso_id IN ($cursos_str)";				
					$sql="SELECT confir_resv FROM log_detalles WHERE in_pod IS NOT NULL AND curso_id IN ($cursos_str)";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count = mysql_num_rows($registros);
					$j = 0;
					if ($count > 0)
					{
						while ($reg = mysql_fetch_array($registros))
						{
						   $conexiones[$j] = $reg['confir_resv'];
						   $j++;
						}
						
						//inicializamos variable de almacenamiento para las horas de conexion
						$TotalIntervalos = array(0,0,0,0,0,0,0,0,0,0,0,0);
						$ArrayIntervalos = array('0 - 2 h','2 - 4 h','4 - 6 h','6 - 8 h','8 - 10 h','10 - 12 h','12 - 14 h','14 - 16 h','16 - 18 h','18 - 20 h','20 - 22 h','22 - 24 h');	
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$Intervalo = date( "H", strtotime($conexiones[$i]));
							$indice = floor($Intervalo / 2);
							switch ($indice) 
							{
								case 0:    
									$TotalIntervalos[$indice]++;
									break;
								case 1:    
									$TotalIntervalos[$indice]++;
									break;
								case 2:    
									$TotalIntervalos[$indice]++;
									break;
								case 3:    
									$TotalIntervalos[$indice]++;
									break;
								case 4:    
									$TotalIntervalos[$indice]++;
									break;
								case 5:    
									$TotalIntervalos[$indice]++;
									break;
								case 6:    
									$TotalIntervalos[$indice]++;
									break;
								case 7:    
									$TotalIntervalos[$indice]++;
									break;
								case 8:    
									$TotalIntervalos[$indice]++;
									break;
								case 9:    
									$TotalIntervalos[$indice]++;
									break;
								case 10:    
									$TotalIntervalos[$indice]++;
									break;
								case 11:    
									$TotalIntervalos[$indice]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por intervalos horarios diarios
						//for ($i=0; $i<12; $i++)
						//{
							//echo "$ArrayIntervalos[$i] = $TotalIntervalos[$i]; &nbsp;&nbsp;";
						//}
	
					}  
					mysql_free_result($registros);
					
					//echo "<br><br>";
				}				
			}
			else   //($cursoID > 0)    //un solo curso, almacenado tambien en la variable $cursos
			{
			    if ($tiempoID==0)        //estadisticas por semanas, dias y horas
				{
					//buscamos el Lunes de la semana actual y el de la semana anterior
					$dia_sem = date('w', strtotime($hoy));
					//echo "<br>dia_sem = $dia_sem";

					$dia_semana = $dia_sem - 1;
					$Lunes_ref = date('Y-m-d',strtotime('-'.$dia_semana.' days', strtotime($f_ahora)));
					$Lunes_ant = date('Y-m-d',strtotime('-7 days', strtotime($Lunes_ref)));
					
					//***CONEXIONES AL LAB - SEMANA ACTUAL
					// contamos el numero historico de conexiones la semana actual
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ref' AND curso_id = $cursoID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ref' AND in_pod IS NOT NULL AND curso_id = $cursoID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana actual = ".$cuenta0['total'];	
					$total_conexiones_semana_actual = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_actual = $total_conexiones_semana_actual";
					mysql_free_result($registros0);

					
					//***CONEXIONES AL LAB - SEMANA ANTERIOR
					// contamos el numero historico de conexiones la semana anterior
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant' AND entrada < '$Lunes_ref' AND curso_id = $cursoID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant' AND confir_resv < '$Lunes_ref' AND in_pod IS NOT NULL AND curso_id = $cursoID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana anterior = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior = $total_conexiones_semana_anterior";
					mysql_free_result($registros0);		
					
					
					//***CONEXIONES AL LAB - HACE 2 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_2 = date('Y-m-d',strtotime('-14 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_2' AND entrada < '$Lunes_ant' AND curso_id = $cursoID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_2' AND confir_resv < '$Lunes_ant' AND in_pod IS NOT NULL AND curso_id = $cursoID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 2 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_2 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_2 = $total_conexiones_semana_anterior_2";
					mysql_free_result($registros0);
					

					//***CONEXIONES AL LAB - HACE 3 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_3 = date('Y-m-d',strtotime('-21 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_3' AND entrada < '$Lunes_ant_2' AND curso_id = $cursoID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_3' AND confir_resv < '$Lunes_ant_2' AND in_pod IS NOT NULL AND curso_id = $cursoID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 3 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_3 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_3 = $total_conexiones_semana_anterior_3";
					mysql_free_result($registros0);


					//***CONEXIONES AL LAB - HACE 4 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_4' AND entrada < '$Lunes_ant_3' AND curso_id = $cursoID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_4' AND confir_resv < '$Lunes_ant_3' AND in_pod IS NOT NULL AND curso_id = $cursoID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 4 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_4 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_4 = $total_conexiones_semana_anterior_4";
					mysql_free_result($registros0);

					
					//*CONEXIONES AL LAB - todas las ANTERIORES A HACE 4 SEMANAS 
					// contamos el numero historico de conexiones la semana anterior
					////$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada < '$Lunes_ant_4' AND curso_id = $cursoID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv < '$Lunes_ant_4' AND in_pod IS NOT NULL AND curso_id = $cursoID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones antiguas = ".$cuenta0['total'];	
					$total_conexiones_antiguas = $cuenta0['total'];
					//echo "<br>total_conexiones_antiguas = $total_conexiones_antiguas";
					mysql_free_result($registros0);
		
					//echo "<br><br>";
					
					
					
					//******************************************************//
					//***CONEXIONES AL LAB POR TIEMPOS DE ENTRADA AL LAB	
					
					// seleccionamos las fechas de entrada de todas las conexiones
					//$sql="SELECT entrada FROM logs WHERE num_pod_lab IS NOT NULL AND curso_id = $cursoID";
					$sql="SELECT confir_resv FROM log_detalles WHERE in_pod IS NOT NULL AND curso_id = $cursoID";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count = mysql_num_rows($registros);
					$j = 0;
					if ($count > 0)
					{
						while ($reg = mysql_fetch_array($registros))
						{
						   $conexiones[$j] = $reg['confir_resv'];
						   $j++;
						}
					
						//inicializamos variable de almacenamiento para los dias de la semana
						$TotalDiasSemana = array(0,0,0,0,0,0,0);
						$ArraySemana = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado');
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$DayOfWeek = date( "w", strtotime($conexiones[$i]));
							switch ($DayOfWeek) {
								case 0:     //Domingo
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 1:     //Lunes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 2:     //Martes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 3:     //Mi&eacute;rcoles
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 4:     //Jueves
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 5:     //Viernes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 6:     //S&aacute;bado
									$TotalDiasSemana[$DayOfWeek]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por dias de la semana
						//for ($i=0; $i<7; $i++)
						//{
							//echo "$ArraySemana[$i] = $TotalDiasSemana[$i]  ";
						//}
						
						//echo "<br><br>";
						
						
						//inicializamos variable de almacenamiento para las horas de conexion
						$TotalIntervalos = array(0,0,0,0,0,0,0,0,0,0,0,0);
						$ArrayIntervalos = array('0 - 2 h','2 - 4 h','4 - 6 h','6 - 8 h','8 - 10 h','10 - 12 h','12 - 14 h','14 - 16 h','16 - 18 h','18 - 20 h','20 - 22 h','22 - 24 h');
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$Intervalo = date( "H", strtotime($conexiones[$i]));
							$indice = floor($Intervalo / 2);
							switch ($indice) 
							{
								case 0:    
									$TotalIntervalos[$indice]++;
									break;
								case 1:    
									$TotalIntervalos[$indice]++;
									break;
								case 2:    
									$TotalIntervalos[$indice]++;
									break;
								case 3:    
									$TotalIntervalos[$indice]++;
									break;
								case 4:    
									$TotalIntervalos[$indice]++;
									break;
								case 5:    
									$TotalIntervalos[$indice]++;
									break;
								case 6:    
									$TotalIntervalos[$indice]++;
									break;
								case 7:    
									$TotalIntervalos[$indice]++;
									break;
								case 8:    
									$TotalIntervalos[$indice]++;
									break;
								case 9:    
									$TotalIntervalos[$indice]++;
									break;
								case 10:    
									$TotalIntervalos[$indice]++;
									break;
								case 11:    
									$TotalIntervalos[$indice]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por intervalos horarios diarios
						//for ($i=0; $i<12; $i++)
						//{
							//echo "$ArrayIntervalos[$i] = $TotalIntervalos[$i]; &nbsp;&nbsp;";
						//}
	
					}  
					mysql_free_result($registros);
					
					//echo "<br><br>";						
				
				}
				else if ($tiempoID==1)   //estadisticas por semanas
				{
					//***HISTORICO DE CONEXIONES AL LAB
					// contamos el numero historico de conexiones en todos los usuarios
					$total_conexiones_historico = $total_conexiones;
					//echo "<br>total_conexiones_historico = $total_conexiones_historico";

					//buscamos el Lunes de la semana actual y el de la semana anterior
					$dia_sem = date('w', strtotime($hoy));
					//echo "<br>dia_sem = $dia_sem";

					$dia_semana = $dia_sem - 1;
					$Lunes_ref = date('Y-m-d',strtotime('-'.$dia_semana.' days', strtotime($f_ahora)));
					$Lunes_ant = date('Y-m-d',strtotime('-7 days', strtotime($Lunes_ref)));
					
					//***CONEXIONES AL LAB - SEMANA ACTUAL
					// contamos el numero historico de conexiones la semana actual
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ref' AND curso_id = $cursoID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ref' AND in_pod IS NOT NULL AND curso_id = $cursoID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana actual = ".$cuenta0['total'];	
					$total_conexiones_semana_actual = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_actual = $total_conexiones_semana_actual";
					mysql_free_result($registros0);

					
					//***CONEXIONES AL LAB - SEMANA ANTERIOR
					// contamos el numero historico de conexiones la semana anterior
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant' AND entrada < '$Lunes_ref' AND curso_id = $cursoID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant' AND confir_resv < '$Lunes_ref' AND in_pod IS NOT NULL AND curso_id = $cursoID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana anterior = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior = $total_conexiones_semana_anterior";
					mysql_free_result($registros0);		
					
					
					//***CONEXIONES AL LAB - HACE 2 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_2 = date('Y-m-d',strtotime('-14 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_2' AND entrada < '$Lunes_ant' AND curso_id = $cursoID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_2' AND confir_resv < '$Lunes_ant' AND in_pod IS NOT NULL AND curso_id = $cursoID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 2 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_2 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_2 = $total_conexiones_semana_anterior_2";
					mysql_free_result($registros0);
					

					//***CONEXIONES AL LAB - HACE 3 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_3 = date('Y-m-d',strtotime('-21 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_3' AND entrada < '$Lunes_ant_2' AND curso_id = $cursoID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_3' AND confir_resv < '$Lunes_ant_2' AND in_pod IS NOT NULL AND curso_id = $cursoID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 3 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_3 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_3 = $total_conexiones_semana_anterior_3";
					mysql_free_result($registros0);


					//***CONEXIONES AL LAB - HACE 4 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_4' AND entrada < '$Lunes_ant_3' AND curso_id = $cursoID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_4' AND confir_resv < '$Lunes_ant_3' AND in_pod IS NOT NULL AND curso_id = $cursoID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 4 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_4 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_4 = $total_conexiones_semana_anterior_4";
					mysql_free_result($registros0);


					//*CONEXIONES AL LAB - todas las ANTERIORES A HACE 4 SEMANAS 
					// contamos el numero historico de conexiones la semana anterior
					////$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada < '$Lunes_ant_4' AND curso_id = $cursoID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv < '$Lunes_ant_4' AND in_pod IS NOT NULL AND curso_id = $cursoID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones antiguas = ".$cuenta0['total'];	
					$total_conexiones_antiguas = $cuenta0['total'];
					//echo "<br>total_conexiones_antiguas = $total_conexiones_antiguas";
					mysql_free_result($registros0);
		
					//echo "<br><br>";
				}
				
				else if ($tiempoID==2)      //estadisticas por dias
				{
					//******************************************************//
					//***CONEXIONES AL LAB POR TIEMPOS DE ENTRADA AL LAB	
					
					// seleccionamos las fechas de entrada de todas las conexiones
					//$sql="SELECT entrada FROM logs WHERE num_pod_lab IS NOT NULL AND curso_id = $cursoID";
					$sql="SELECT confir_resv FROM log_detalles WHERE in_pod IS NOT NULL AND curso_id = $cursoID";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count = mysql_num_rows($registros);
					$j = 0;
					if ($count > 0)
					{
						while ($reg = mysql_fetch_array($registros))
						{
						   $conexiones[$j] = $reg['confir_resv'];
						   $j++;
						}
					
						//inicializamos variable de almacenamiento para los dias de la semana
						$TotalDiasSemana = array(0,0,0,0,0,0,0);
						$ArraySemana = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado');
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$DayOfWeek = date( "w", strtotime($conexiones[$i]));
							switch ($DayOfWeek) {
								case 0:     //Domingo
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 1:     //Lunes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 2:     //Martes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 3:     //Mi&eacute;rcoles
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 4:     //Jueves
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 5:     //Viernes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 6:     //S&aacute;bado
									$TotalDiasSemana[$DayOfWeek]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por dias de la semana
						//for ($i=0; $i<7; $i++)
						//{
							//echo "$ArraySemana[$i] = $TotalDiasSemana[$i]  ";
						//}
							
					}  
					mysql_free_result($registros);
					
					//echo "<br><br>";
				
				}
				
				else   //if ($tiempoID==3)      //estadisticas por horas
				{
					//***CONEXIONES AL LAB POR TIEMPOS DE ENTRADA AL LAB - como en el punto anterior
					// seleccionamos las fechas de entrada de todas las conexiones
					//$sql="SELECT entrada FROM logs WHERE num_pod_lab IS NOT NULL AND curso_id = $cursoID";
					$sql="SELECT confir_resv FROM log_detalles WHERE in_pod IS NOT NULL AND curso_id = $cursoID";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count = mysql_num_rows($registros);
					$j = 0;
					if ($count > 0)
					{
						while ($reg = mysql_fetch_array($registros))
						{
						   $conexiones[$j] = $reg['confir_resv'];
						   $j++;
						}

						//inicializamos variable de almacenamiento para las horas de conexion
						$TotalIntervalos = array(0,0,0,0,0,0,0,0,0,0,0,0);
						$ArrayIntervalos = array('0 - 2 h','2 - 4 h','4 - 6 h','6 - 8 h','8 - 10 h','10 - 12 h','12 - 14 h','14 - 16 h','16 - 18 h','18 - 20 h','20 - 22 h','22 - 24 h');
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$Intervalo = date( "H", strtotime($conexiones[$i]));
							$indice = floor($Intervalo / 2);
							switch ($indice) 
							{
								case 0:    
									$TotalIntervalos[$indice]++;
									break;
								case 1:    
									$TotalIntervalos[$indice]++;
									break;
								case 2:    
									$TotalIntervalos[$indice]++;
									break;
								case 3:    
									$TotalIntervalos[$indice]++;
									break;
								case 4:    
									$TotalIntervalos[$indice]++;
									break;
								case 5:    
									$TotalIntervalos[$indice]++;
									break;
								case 6:    
									$TotalIntervalos[$indice]++;
									break;
								case 7:    
									$TotalIntervalos[$indice]++;
									break;
								case 8:    
									$TotalIntervalos[$indice]++;
									break;
								case 9:    
									$TotalIntervalos[$indice]++;
									break;
								case 10:    
									$TotalIntervalos[$indice]++;
									break;
								case 11:    
									$TotalIntervalos[$indice]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por intervalos horarios diarios
						//for ($i=0; $i<12; $i++)
						//{
							//echo "$ArrayIntervalos[$i] = $TotalIntervalos[$i]; &nbsp;&nbsp;";
						//}

					}  
					mysql_free_result($registros);
					
					//echo "<br><br>";
				}		
	
			}
		}
		else if ($cursoID=="")     //implicitamente $usuarioID!="", o sea, tenemos seleccionado un alumno
		{
		
			if($usuarioID == 0)    //un grupo de usuarios, almacenados en la variable $usuarios
			{
				//pasamos los codigos de usuario almacenados en el array $usuarios a la cadena $usuarios_str
				$usuarios_str = implode(",", $usuarios); 
				
				//echo "<br>usuarios_str = $usuarios_str";
					
				if ($tiempoID==0)     //estadisticas por semanas, dias y horas
				{
					//buscamos el Lunes de la semana actual y el de la semana anterior
					$dia_sem = date('w', strtotime($hoy));
					//echo "<br>dia_sem = $dia_sem";

					$dia_semana = $dia_sem - 1;
					$Lunes_ref = date('Y-m-d',strtotime('-'.$dia_semana.' days', strtotime($f_ahora)));
					$Lunes_ant = date('Y-m-d',strtotime('-7 days', strtotime($Lunes_ref)));
					
					//***CONEXIONES AL LAB - SEMANA ACTUAL
					// contamos el numero historico de conexiones la semana actual
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ref' AND user_id IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ref' AND in_pod IS NOT NULL AND user_id IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana actual = ".$cuenta0['total'];	
					$total_conexiones_semana_actual = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_actual = $total_conexiones_semana_actual";
					mysql_free_result($registros0);

					
					//***CONEXIONES AL LAB - SEMANA ANTERIOR
					// contamos el numero historico de conexiones la semana anterior
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant' AND entrada < '$Lunes_ref' AND user_id IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant' AND confir_resv < '$Lunes_ref' AND in_pod IS NOT NULL AND user_id IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana anterior = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior = $total_conexiones_semana_anterior";
					mysql_free_result($registros0);		
					
					
					//***CONEXIONES AL LAB - HACE 2 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_2 = date('Y-m-d',strtotime('-14 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_2' AND entrada < '$Lunes_ant' AND user_id IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_2' AND confir_resv < '$Lunes_ant' AND in_pod IS NOT NULL AND user_id IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 2 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_2 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_2 = $total_conexiones_semana_anterior_2";
					mysql_free_result($registros0);
					

					//***CONEXIONES AL LAB - HACE 3 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_3 = date('Y-m-d',strtotime('-21 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_3' AND entrada < '$Lunes_ant_2' AND user_id IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_3' AND confir_resv < '$Lunes_ant_2' AND in_pod IS NOT NULL AND user_id IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 3 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_3 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_3 = $total_conexiones_semana_anterior_3";
					mysql_free_result($registros0);


					//***CONEXIONES AL LAB - HACE 4 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_4' AND entrada < '$Lunes_ant_3' AND user_id IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_4' AND confir_resv < '$Lunes_ant_3' AND in_pod IS NOT NULL AND user_id IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 4 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_4 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_4 = $total_conexiones_semana_anterior_4";
					mysql_free_result($registros0);

					
					//*CONEXIONES AL LAB - todas las ANTERIORES A HACE 4 SEMANAS 
					// contamos el numero historico de conexiones la semana anterior
					////$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada < '$Lunes_ant_4' AND user_id IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv < '$Lunes_ant_4' AND in_pod IS NOT NULL AND user_id IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones antiguas = ".$cuenta0['total'];	
					$total_conexiones_antiguas = $cuenta0['total'];
					//echo "<br>total_conexiones_antiguas = $total_conexiones_antiguas";
					mysql_free_result($registros0);
		
					//echo "<br><br>";
					
					
					
					//******************************************************//
					//***CONEXIONES AL LAB POR TIEMPOS DE ENTRADA AL LAB	
					
					// seleccionamos las fechas de entrada de todas las conexiones
					//$sql="SELECT entrada FROM logs WHERE num_pod_lab IS NOT NULL AND user_id IN ($usuarios_str)";
					$sql="SELECT confir_resv FROM log_detalles WHERE in_pod IS NOT NULL AND user_id IN ($usuarios_str)";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count = mysql_num_rows($registros);
					$j = 0;
					if ($count > 0)
					{
						while ($reg = mysql_fetch_array($registros))
						{
						   $conexiones[$j] = $reg['confir_resv'];
						   $j++;
						}
					
						//inicializamos variable de almacenamiento para los dias de la semana
						$TotalDiasSemana = array(0,0,0,0,0,0,0);
						$ArraySemana = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado');
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$DayOfWeek = date( "w", strtotime($conexiones[$i]));
							switch ($DayOfWeek) {
								case 0:     //Domingo
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 1:     //Lunes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 2:     //Martes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 3:     //Mi&eacute;rcoles
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 4:     //Jueves
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 5:     //Viernes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 6:     //S&aacute;bado
									$TotalDiasSemana[$DayOfWeek]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por dias de la semana
						//for ($i=0; $i<7; $i++)
						//{
							//echo "$ArraySemana[$i] = $TotalDiasSemana[$i]  ";
						//}
						
						//echo "<br><br>";
						
						
						//inicializamos variable de almacenamiento para las horas de conexion
						$TotalIntervalos = array(0,0,0,0,0,0,0,0,0,0,0,0);
						$ArrayIntervalos = array('0 - 2 h','2 - 4 h','4 - 6 h','6 - 8 h','8 - 10 h','10 - 12 h','12 - 14 h','14 - 16 h','16 - 18 h','18 - 20 h','20 - 22 h','22 - 24 h');	
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$Intervalo = date( "H", strtotime($conexiones[$i]));
							$indice = floor($Intervalo / 2);
							switch ($indice) 
							{
								case 0:    
									$TotalIntervalos[$indice]++;
									break;
								case 1:    
									$TotalIntervalos[$indice]++;
									break;
								case 2:    
									$TotalIntervalos[$indice]++;
									break;
								case 3:    
									$TotalIntervalos[$indice]++;
									break;
								case 4:    
									$TotalIntervalos[$indice]++;
									break;
								case 5:    
									$TotalIntervalos[$indice]++;
									break;
								case 6:    
									$TotalIntervalos[$indice]++;
									break;
								case 7:    
									$TotalIntervalos[$indice]++;
									break;
								case 8:    
									$TotalIntervalos[$indice]++;
									break;
								case 9:    
									$TotalIntervalos[$indice]++;
									break;
								case 10:    
									$TotalIntervalos[$indice]++;
									break;
								case 11:    
									$TotalIntervalos[$indice]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por intervalos horarios diarios
						//for ($i=0; $i<12; $i++)
						//{
							//echo "$ArrayIntervalos[$i] = $TotalIntervalos[$i]; &nbsp;&nbsp;";
						//}

					}  
					mysql_free_result($registros);
					
					//echo "<br><br>";				
				
				}
			 	else if ($tiempoID== 1)   //estadisticas por semanas
				{
					//***HISTORICO DE CONEXIONES AL LAB
					// contamos el numero historico de conexiones en todos los usuarios
					$total_conexiones_historico = $total_conexiones;
					//echo "<br>total_conexiones_historico = $total_conexiones_historico";

					//buscamos el Lunes de la semana actual y el de la semana anterior
					$dia_sem = date('w', strtotime($hoy));
					//echo "<br>dia_sem = $dia_sem";

					$dia_semana = $dia_sem - 1;
					$Lunes_ref = date('Y-m-d',strtotime('-'.$dia_semana.' days', strtotime($f_ahora)));
					$Lunes_ant = date('Y-m-d',strtotime('-7 days', strtotime($Lunes_ref)));
					
					//***CONEXIONES AL LAB - SEMANA ACTUAL
					// contamos el numero historico de conexiones la semana actual
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ref' AND user_id IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ref' AND in_pod IS NOT NULL AND user_id IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana actual = ".$cuenta0['total'];	
					$total_conexiones_semana_actual = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_actual = $total_conexiones_semana_actual";
					mysql_free_result($registros0);

					
					//***CONEXIONES AL LAB - SEMANA ANTERIOR
					// contamos el numero historico de conexiones la semana anterior
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant' AND entrada < '$Lunes_ref' AND user_id IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant' AND confir_resv < '$Lunes_ref' AND in_pod IS NOT NULL AND user_id IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana anterior = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior = $total_conexiones_semana_anterior";
					mysql_free_result($registros0);		
					
					
					//***CONEXIONES AL LAB - HACE 2 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_2 = date('Y-m-d',strtotime('-14 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_2' AND entrada < '$Lunes_ant' AND user_id IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_2' AND confir_resv < '$Lunes_ant' AND in_pod IS NOT NULL AND user_id IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 2 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_2 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_2 = $total_conexiones_semana_anterior_2";
					mysql_free_result($registros0);
					

					//***CONEXIONES AL LAB - HACE 3 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_3 = date('Y-m-d',strtotime('-21 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_3' AND entrada < '$Lunes_ant_2' AND user_id IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_3' AND confir_resv < '$Lunes_ant_2' AND in_pod IS NOT NULL AND user_id IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 3 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_3 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_3 = $total_conexiones_semana_anterior_3";
					mysql_free_result($registros0);


					//***CONEXIONES AL LAB - HACE 4 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_4' AND entrada < '$Lunes_ant_3' AND user_id IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_4' AND confir_resv < '$Lunes_ant_3' AND in_pod IS NOT NULL AND user_id IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 4 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_4 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_4 = $total_conexiones_semana_anterior_4";
					mysql_free_result($registros0);


					//*CONEXIONES AL LAB - todas las ANTERIORES A HACE 4 SEMANAS 
					// contamos el numero historico de conexiones la semana anterior
					////$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada < '$Lunes_ant_4' AND user_id IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv < '$Lunes_ant_4' AND in_pod IS NOT NULL AND user_id IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones antiguas = ".$cuenta0['total'];	
					$total_conexiones_antiguas = $cuenta0['total'];
					//echo "<br>total_conexiones_antiguas = $total_conexiones_antiguas";
					mysql_free_result($registros0);
		
					//echo "<br><br>";
				}
				
				else if ($tiempoID==2)      //estadisticas por dias
				{
					//******************************************************//
					//***CONEXIONES AL LAB POR TIEMPOS DE ENTRADA AL LAB	
					
					// seleccionamos las fechas de entrada de todas las conexiones
					//$sql="SELECT entrada FROM logs WHERE num_pod_lab IS NOT NULL AND user_id IN ($usuarios_str)";
					$sql="SELECT confir_resv FROM log_detalles WHERE in_pod IS NOT NULL AND user_id IN ($usuarios_str)";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count = mysql_num_rows($registros);
					$j = 0;
					if ($count > 0)
					{
						while ($reg = mysql_fetch_array($registros))
						{
						   $conexiones[$j] = $reg['confir_resv'];
						   $j++;
						}
					
						//inicializamos variable de almacenamiento para los dias de la semana
						$TotalDiasSemana = array(0,0,0,0,0,0,0);
						$ArraySemana = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado');
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$DayOfWeek = date( "w", strtotime($conexiones[$i]));
							switch ($DayOfWeek) {
								case 0:     //Domingo
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 1:     //Lunes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 2:     //Martes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 3:     //Mi&eacute;rcoles
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 4:     //Jueves
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 5:     //Viernes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 6:     //S&aacute;bado
									$TotalDiasSemana[$DayOfWeek]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por dias de la semana
						//for ($i=0; $i<7; $i++)
						//{
							//echo "$ArraySemana[$i] = $TotalDiasSemana[$i]  ";
						//}
						
					}  
					mysql_free_result($registros);
					
					//echo "<br><br>";
				
				}
				
				else   //if ($tiempoID==3)      //estadisticas por horas
				{
					//***CONEXIONES AL LAB POR TIEMPOS DE ENTRADA AL LAB - como en el punto anterior
					// seleccionamos las fechas de entrada de todas las conexiones
					//$sql="SELECT entrada FROM logs WHERE num_pod_lab IS NOT NULL AND user_id IN ($usuarios_str)";
					$sql="SELECT confir_resv FROM log_detalles WHERE in_pod IS NOT NULL AND user_id IN ($usuarios_str)";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count = mysql_num_rows($registros);
					$j = 0;
					if ($count > 0)
					{
						while ($reg = mysql_fetch_array($registros))
						{
						   $conexiones[$j] = $reg['confir_resv'];
						   $j++;
						}
							
						//inicializamos variable de almacenamiento para las horas de conexion
						$TotalIntervalos = array(0,0,0,0,0,0,0,0,0,0,0,0);
						$ArrayIntervalos = array('0 - 2 h','2 - 4 h','4 - 6 h','6 - 8 h','8 - 10 h','10 - 12 h','12 - 14 h','14 - 16 h','16 - 18 h','18 - 20 h','20 - 22 h','22 - 24 h');
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$Intervalo = date( "H", strtotime($conexiones[$i]));
							$indice = floor($Intervalo / 2);
							switch ($indice) 
							{
								case 0:    
									$TotalIntervalos[$indice]++;
									break;
								case 1:    
									$TotalIntervalos[$indice]++;
									break;
								case 2:    
									$TotalIntervalos[$indice]++;
									break;
								case 3:    
									$TotalIntervalos[$indice]++;
									break;
								case 4:    
									$TotalIntervalos[$indice]++;
									break;
								case 5:    
									$TotalIntervalos[$indice]++;
									break;
								case 6:    
									$TotalIntervalos[$indice]++;
									break;
								case 7:    
									$TotalIntervalos[$indice]++;
									break;
								case 8:    
									$TotalIntervalos[$indice]++;
									break;
								case 9:    
									$TotalIntervalos[$indice]++;
									break;
								case 10:    
									$TotalIntervalos[$indice]++;
									break;
								case 11:    
									$TotalIntervalos[$indice]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por intervalos horarios diarios
						//for ($i=0; $i<12; $i++)
						//{
							//echo "$ArrayIntervalos[$i] = $TotalIntervalos[$i]; &nbsp;&nbsp;";
						//}
					
					}  
					mysql_free_result($registros);
					
					//echo "<br><br>";
				}				
				
			}
			else   //($usuarioID > 0)    //un solo usuario, almacenado tambien en la variable $usuarios
			{ 
			    if ($tiempoID==0)     //estadisticas por semanas, dias y horas
				{
					//buscamos el Lunes de la semana actual y el de la semana anterior
					$dia_sem = date('w', strtotime($hoy));
					//echo "<br>dia_sem = $dia_sem";

					$dia_semana = $dia_sem - 1;
					$Lunes_ref = date('Y-m-d',strtotime('-'.$dia_semana.' days', strtotime($f_ahora)));
					$Lunes_ant = date('Y-m-d',strtotime('-7 days', strtotime($Lunes_ref)));
					
					//***CONEXIONES AL LAB - SEMANA ACTUAL
					// contamos el numero historico de conexiones la semana actual
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ref' AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ref' AND in_pod IS NOT NULL AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana actual = ".$cuenta0['total'];	
					$total_conexiones_semana_actual = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_actual = $total_conexiones_semana_actual";
					mysql_free_result($registros0);

					
					//***CONEXIONES AL LAB - SEMANA ANTERIOR
					// contamos el numero historico de conexiones la semana anterior
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant' AND entrada < '$Lunes_ref' AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant' AND confir_resv < '$Lunes_ref' AND in_pod IS NOT NULL AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana anterior = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior = $total_conexiones_semana_anterior";
					mysql_free_result($registros0);		
					
					
					//***CONEXIONES AL LAB - HACE 2 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_2 = date('Y-m-d',strtotime('-14 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_2' AND entrada < '$Lunes_ant' AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_2' AND confir_resv < '$Lunes_ant' AND in_pod IS NOT NULL AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 2 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_2 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_2 = $total_conexiones_semana_anterior_2";
					mysql_free_result($registros0);
					

					//***CONEXIONES AL LAB - HACE 3 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_3 = date('Y-m-d',strtotime('-21 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_3' AND entrada < '$Lunes_ant_2' AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_3' AND confir_resv < '$Lunes_ant_2' AND in_pod IS NOT NULL AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 3 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_3 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_3 = $total_conexiones_semana_anterior_3";
					mysql_free_result($registros0);


					//***CONEXIONES AL LAB - HACE 4 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_4' AND entrada < '$Lunes_ant_3' AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_4' AND confir_resv < '$Lunes_ant_3' AND in_pod IS NOT NULL AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 4 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_4 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_4 = $total_conexiones_semana_anterior_4";
					mysql_free_result($registros0);

					
					//*CONEXIONES AL LAB - todas las ANTERIORES A HACE 4 SEMANAS 
					// contamos el numero historico de conexiones la semana anterior
					////$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada < '$Lunes_ant_4' AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv < '$Lunes_ant_4' AND in_pod IS NOT NULL AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones antiguas = ".$cuenta0['total'];	
					$total_conexiones_antiguas = $cuenta0['total'];
					//echo "<br>total_conexiones_antiguas = $total_conexiones_antiguas";
					mysql_free_result($registros0);
		
					//echo "<br><br>";
					
					
					
					//******************************************************//
					//***CONEXIONES AL LAB POR TIEMPOS DE ENTRADA AL LAB	
					
					// seleccionamos las fechas de entrada de todas las conexiones
					//$sql="SELECT entrada FROM logs WHERE num_pod_lab IS NOT NULL AND user_id = $usuarioID";
					$sql="SELECT confir_resv FROM log_detalles WHERE in_pod IS NOT NULL AND user_id = $usuarioID";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count = mysql_num_rows($registros);
					$j = 0;
					if ($count > 0)
					{
						while ($reg = mysql_fetch_array($registros))
						{
						   $conexiones[$j] = $reg['confir_resv'];
						   $j++;
						}
					
						//inicializamos variable de almacenamiento para los dias de la semana
						$TotalDiasSemana = array(0,0,0,0,0,0,0);
						$ArraySemana = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado');
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$DayOfWeek = date( "w", strtotime($conexiones[$i]));
							switch ($DayOfWeek) {
								case 0:     //Domingo
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 1:     //Lunes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 2:     //Martes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 3:     //Mi&eacute;rcoles
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 4:     //Jueves
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 5:     //Viernes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 6:     //S&aacute;bado
									$TotalDiasSemana[$DayOfWeek]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por dias de la semana
						//for ($i=0; $i<7; $i++)
						//{
							//echo "$ArraySemana[$i] = $TotalDiasSemana[$i]  ";
						//}
						
						//echo "<br><br>";
						
						
						//inicializamos variable de almacenamiento para las horas de conexion
						$TotalIntervalos = array(0,0,0,0,0,0,0,0,0,0,0,0);
						$ArrayIntervalos = array('0 - 2 h','2 - 4 h','4 - 6 h','6 - 8 h','8 - 10 h','10 - 12 h','12 - 14 h','14 - 16 h','16 - 18 h','18 - 20 h','20 - 22 h','22 - 24 h');	
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$Intervalo = date( "H", strtotime($conexiones[$i]));
							$indice = floor($Intervalo / 2);
							switch ($indice) 
							{
								case 0:    
									$TotalIntervalos[$indice]++;
									break;
								case 1:    
									$TotalIntervalos[$indice]++;
									break;
								case 2:    
									$TotalIntervalos[$indice]++;
									break;
								case 3:    
									$TotalIntervalos[$indice]++;
									break;
								case 4:    
									$TotalIntervalos[$indice]++;
									break;
								case 5:    
									$TotalIntervalos[$indice]++;
									break;
								case 6:    
									$TotalIntervalos[$indice]++;
									break;
								case 7:    
									$TotalIntervalos[$indice]++;
									break;
								case 8:    
									$TotalIntervalos[$indice]++;
									break;
								case 9:    
									$TotalIntervalos[$indice]++;
									break;
								case 10:    
									$TotalIntervalos[$indice]++;
									break;
								case 11:    
									$TotalIntervalos[$indice]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por intervalos horarios diarios
						//for ($i=0; $i<12; $i++)
						//{
							//echo "$ArrayIntervalos[$i] = $TotalIntervalos[$i]; &nbsp;&nbsp;";
						//}
	
					}  
					mysql_free_result($registros);
					
					//echo "<br><br>";				
				
				}
				else if ($tiempoID==1)   //estadisticas por semanas
				{
					//***HISTORICO DE CONEXIONES AL LAB
					// contamos el numero historico de conexiones en todos los usuarios
					$total_conexiones_historico = $total_conexiones;
					//echo "<br>total_conexiones_historico = $total_conexiones_historico";

					//buscamos el Lunes de la semana actual y el de la semana anterior
					$dia_sem = date('w', strtotime($hoy));
					//echo "<br>dia_sem = $dia_sem";

					$dia_semana = $dia_sem - 1;
					$Lunes_ref = date('Y-m-d',strtotime('-'.$dia_semana.' days', strtotime($f_ahora)));
					$Lunes_ant = date('Y-m-d',strtotime('-7 days', strtotime($Lunes_ref)));
					
					//***CONEXIONES AL LAB - SEMANA ACTUAL
					// contamos el numero historico de conexiones la semana actual
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ref' AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ref' AND in_pod IS NOT NULL AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana actual = ".$cuenta0['total'];	
					$total_conexiones_semana_actual = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_actual = $total_conexiones_semana_actual";
					mysql_free_result($registros0);

					
					//***CONEXIONES AL LAB - SEMANA ANTERIOR
					// contamos el numero historico de conexiones la semana anterior
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant' AND entrada < '$Lunes_ref' AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant' AND confir_resv < '$Lunes_ref' AND in_pod IS NOT NULL AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana anterior = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior = $total_conexiones_semana_anterior";
					mysql_free_result($registros0);		
					
					
					//***CONEXIONES AL LAB - HACE 2 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_2 = date('Y-m-d',strtotime('-14 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_2' AND entrada < '$Lunes_ant' AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_2' AND confir_resv < '$Lunes_ant' AND in_pod IS NOT NULL AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 2 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_2 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_2 = $total_conexiones_semana_anterior_2";
					mysql_free_result($registros0);
					

					//***CONEXIONES AL LAB - HACE 3 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_3 = date('Y-m-d',strtotime('-21 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_3' AND entrada < '$Lunes_ant_2' AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_3' AND confir_resv < '$Lunes_ant_2' AND in_pod IS NOT NULL AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 3 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_3 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_3 = $total_conexiones_semana_anterior_3";
					mysql_free_result($registros0);


					//***CONEXIONES AL LAB - HACE 4 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_4' AND entrada < '$Lunes_ant_3' AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_4' AND confir_resv < '$Lunes_ant_3' AND in_pod IS NOT NULL AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 4 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_4 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_4 = $total_conexiones_semana_anterior_4";
					mysql_free_result($registros0);


					//*CONEXIONES AL LAB - todas las ANTERIORES A HACE 4 SEMANAS 
					// contamos el numero historico de conexiones la semana anterior
					////$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada < '$Lunes_ant_4' AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv < '$Lunes_ant_4' AND in_pod IS NOT NULL AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones antiguas = ".$cuenta0['total'];	
					$total_conexiones_antiguas = $cuenta0['total'];
					//echo "<br>total_conexiones_antiguas = $total_conexiones_antiguas";
					mysql_free_result($registros0);
		
					//echo "<br><br>";
				}
				
				else if ($tiempoID==2)      //estadisticas por dias
				{
					//******************************************************//
					//***CONEXIONES AL LAB POR TIEMPOS DE ENTRADA AL LAB	
					
					// seleccionamos las fechas de entrada de todas las conexiones
					//$sql="SELECT entrada FROM logs WHERE num_pod_lab IS NOT NULL AND user_id = $usuarioID";				
					$sql="SELECT confir_resv FROM log_detalles WHERE in_pod IS NOT NULL AND user_id = $usuarioID";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count = mysql_num_rows($registros);
					$j = 0;
					if ($count > 0)
					{
						while ($reg = mysql_fetch_array($registros))
						{
						   $conexiones[$j] = $reg['confir_resv'];
						   $j++;
						}
					
						//inicializamos variable de almacenamiento para los dias de la semana
						$TotalDiasSemana = array(0,0,0,0,0,0,0);
						$ArraySemana = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado');
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$DayOfWeek = date( "w", strtotime($conexiones[$i]));
							switch ($DayOfWeek) {
								case 0:     //Domingo
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 1:     //Lunes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 2:     //Martes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 3:     //Mi&eacute;rcoles
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 4:     //Jueves
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 5:     //Viernes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 6:     //S&aacute;bado
									$TotalDiasSemana[$DayOfWeek]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por dias de la semana
						//for ($i=0; $i<7; $i++)
						//{
							//echo "$ArraySemana[$i] = $TotalDiasSemana[$i]  ";
						//}

					}  
					mysql_free_result($registros);
					
					//echo "<br><br>";
				
				}
		
				else   //if ($tiempoID==3)      //estadisticas por horas
				{
					//***CONEXIONES AL LAB POR TIEMPOS DE ENTRADA AL LAB - como en el punto anterior
					// seleccionamos las fechas de entrada de todas las conexiones
					//$sql="SELECT entrada FROM logs WHERE num_pod_lab IS NOT NULL AND user_id = $usuarioID";				
					$sql="SELECT confir_resv FROM log_detalles WHERE in_pod IS NOT NULL AND user_id = $usuarioID";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count = mysql_num_rows($registros);
					$j = 0;
					if ($count > 0)
					{
						while ($reg = mysql_fetch_array($registros))
						{
						   $conexiones[$j] = $reg['confir_resv'];
						   $j++;
						}
					
						//inicializamos variable de almacenamiento para las horas de conexion
						$TotalIntervalos = array(0,0,0,0,0,0,0,0,0,0,0,0);
						$ArrayIntervalos = array('0 - 2 h','2 - 4 h','4 - 6 h','6 - 8 h','8 - 10 h','10 - 12 h','12 - 14 h','14 - 16 h','16 - 18 h','18 - 20 h','20 - 22 h','22 - 24 h');	
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$Intervalo = date( "H", strtotime($conexiones[$i]));
							$indice = floor($Intervalo / 2);
							switch ($indice) 
							{
								case 0:    
									$TotalIntervalos[$indice]++;
									break;
								case 1:    
									$TotalIntervalos[$indice]++;
									break;
								case 2:    
									$TotalIntervalos[$indice]++;
									break;
								case 3:    
									$TotalIntervalos[$indice]++;
									break;
								case 4:    
									$TotalIntervalos[$indice]++;
									break;
								case 5:    
									$TotalIntervalos[$indice]++;
									break;
								case 6:    
									$TotalIntervalos[$indice]++;
									break;
								case 7:    
									$TotalIntervalos[$indice]++;
									break;
								case 8:    
									$TotalIntervalos[$indice]++;
									break;
								case 9:    
									$TotalIntervalos[$indice]++;
									break;
								case 10:    
									$TotalIntervalos[$indice]++;
									break;
								case 11:    
									$TotalIntervalos[$indice]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por intervalos horarios diarios
						//for ($i=0; $i<12; $i++)
						//{
							//echo "$ArrayIntervalos[$i] = $TotalIntervalos[$i]; &nbsp;&nbsp;";
						//}
			
					}  
					mysql_free_result($registros);
					
					//echo "<br><br>";
				}	

	
			}		
		}
		else  //      //implicitamente $cursoID!="" y $usuarioID!="", o sea, tenemos seleccionado un curso y un alumno
		{
			if ($cursoID == 0 && $usuarioID == 0)    //un grupo de cursos, almacenados en la variable $cursos, y dentro de ellos un grupo de alumnos, almacenados en la variable $usuarios
			{
				//pasamos los codigos de usuario almacenados en el array $usuarios a la cadena $usuarios_str
				$cursos_str = implode(",", $cursos); 
				
				//echo "<br>usuarios_str = $usuarios_str";
				
				//pasamos los codigos de usuario almacenados en el array $usuarios a la cadena $usuarios_str
				$usuarios_str = implode(",", $usuarios); 
				
				//echo "<br>usuarios_str = $usuarios_str";	


				if ($tiempoID==0)     //estadisticas por semanas, dias y horas
				{
					//buscamos el Lunes de la semana actual y el de la semana anterior
					$dia_sem = date('w', strtotime($hoy));
					//echo "<br>dia_sem = $dia_sem";

					$dia_semana = $dia_sem - 1;
					$Lunes_ref = date('Y-m-d',strtotime('-'.$dia_semana.' days', strtotime($f_ahora)));
					$Lunes_ant = date('Y-m-d',strtotime('-7 days', strtotime($Lunes_ref)));
					
					//***CONEXIONES AL LAB - SEMANA ACTUAL
					// contamos el numero historico de conexiones la semana actual
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ref' AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ref' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana actual = ".$cuenta0['total'];	
					$total_conexiones_semana_actual = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_actual = $total_conexiones_semana_actual";
					mysql_free_result($registros0);

					
					//***CONEXIONES AL LAB - SEMANA ANTERIOR
					// contamos el numero historico de conexiones la semana anterior
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant' AND entrada < '$Lunes_ref' AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant' AND confir_resv < '$Lunes_ref' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana anterior = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior = $total_conexiones_semana_anterior";
					mysql_free_result($registros0);		
					
					
					//***CONEXIONES AL LAB - HACE 2 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_2 = date('Y-m-d',strtotime('-14 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_2' AND entrada < '$Lunes_ant' AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_2' AND confir_resv < '$Lunes_ant' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 2 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_2 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_2 = $total_conexiones_semana_anterior_2";
					mysql_free_result($registros0);
					

					//***CONEXIONES AL LAB - HACE 3 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_3 = date('Y-m-d',strtotime('-21 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_3' AND entrada < '$Lunes_ant_2' AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_3' AND confir_resv < '$Lunes_ant_2' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 3 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_3 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_3 = $total_conexiones_semana_anterior_3";
					mysql_free_result($registros0);


					//***CONEXIONES AL LAB - HACE 4 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_4' AND entrada < '$Lunes_ant_3' AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_4' AND confir_resv < '$Lunes_ant_3' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 4 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_4 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_4 = $total_conexiones_semana_anterior_4";
					mysql_free_result($registros0);

					
					//*CONEXIONES AL LAB - todas las ANTERIORES A HACE 4 SEMANAS 
					// contamos el numero historico de conexiones la semana anterior
					////$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada < '$Lunes_ant_4'";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv < '$Lunes_ant_4' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones antiguas = ".$cuenta0['total'];	
					$total_conexiones_antiguas = $cuenta0['total'];
					//echo "<br>total_conexiones_antiguas = $total_conexiones_antiguas";
					mysql_free_result($registros0);
		
					//echo "<br><br>";
					
					
					
					//******************************************************//
					//***CONEXIONES AL LAB POR TIEMPOS DE ENTRADA AL LAB	
					
					// seleccionamos las fechas de entrada de todas las conexiones
					//$sql="SELECT entrada FROM logs WHERE num_pod_lab IS NOT NULL AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str)";
					$sql="SELECT confir_resv FROM log_detalles WHERE in_pod IS NOT NULL AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str)";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count = mysql_num_rows($registros);
					$j = 0;
					if ($count > 0)
					{
						while ($reg = mysql_fetch_array($registros))
						{
						   $conexiones[$j] = $reg['confir_resv'];
						   $j++;
						}
					
						//inicializamos variable de almacenamiento para los dias de la semana
						$TotalDiasSemana = array(0,0,0,0,0,0,0);
						$ArraySemana = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado');
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$DayOfWeek = date( "w", strtotime($conexiones[$i]));
							switch ($DayOfWeek) {
								case 0:     //Domingo
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 1:     //Lunes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 2:     //Martes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 3:     //Mi&eacute;rcoles
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 4:     //Jueves
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 5:     //Viernes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 6:     //S&aacute;bado
									$TotalDiasSemana[$DayOfWeek]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por dias de la semana
						///for ($i=0; $i<7; $i++)
						//{
							//echo "$ArraySemana[$i] = $TotalDiasSemana[$i]  ";
						//}
						
						//echo "<br><br>";
						
						
						//inicializamos variable de almacenamiento para las horas de conexion
						$TotalIntervalos = array(0,0,0,0,0,0,0,0,0,0,0,0);
						$ArrayIntervalos = array('0 - 2 h','2 - 4 h','4 - 6 h','6 - 8 h','8 - 10 h','10 - 12 h','12 - 14 h','14 - 16 h','16 - 18 h','18 - 20 h','20 - 22 h','22 - 24 h');	
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$Intervalo = date( "H", strtotime($conexiones[$i]));
							$indice = floor($Intervalo / 2);
							switch ($indice) 
							{
								case 0:    
									$TotalIntervalos[$indice]++;
									break;
								case 1:    
									$TotalIntervalos[$indice]++;
									break;
								case 2:    
									$TotalIntervalos[$indice]++;
									break;
								case 3:    
									$TotalIntervalos[$indice]++;
									break;
								case 4:    
									$TotalIntervalos[$indice]++;
									break;
								case 5:    
									$TotalIntervalos[$indice]++;
									break;
								case 6:    
									$TotalIntervalos[$indice]++;
									break;
								case 7:    
									$TotalIntervalos[$indice]++;
									break;
								case 8:    
									$TotalIntervalos[$indice]++;
									break;
								case 9:    
									$TotalIntervalos[$indice]++;
									break;
								case 10:    
									$TotalIntervalos[$indice]++;
									break;
								case 11:    
									$TotalIntervalos[$indice]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por intervalos horarios diarios
						//for ($i=0; $i<12; $i++)
						//{
							//echo "$ArrayIntervalos[$i] = $TotalIntervalos[$i]; &nbsp;&nbsp;";
						//}

					}  
					mysql_free_result($registros);
					
					//echo "<br><br>";				
				
				}
			 	else if ($tiempoID== 1)   //estadisticas por semanas
				{
					//***HISTORICO DE CONEXIONES AL LAB
					// contamos el numero historico de conexiones en todos los usuarios
					$total_conexiones_historico = $total_conexiones;
					//echo "<br>total_conexiones_historico = $total_conexiones_historico";

					//buscamos el Lunes de la semana actual y el de la semana anterior
					$dia_sem = date('w', strtotime($hoy));
					//echo "<br>dia_sem = $dia_sem";

					$dia_semana = $dia_sem - 1;
					$Lunes_ref = date('Y-m-d',strtotime('-'.$dia_semana.' days', strtotime($f_ahora)));
					$Lunes_ant = date('Y-m-d',strtotime('-7 days', strtotime($Lunes_ref)));
					
					//***CONEXIONES AL LAB - SEMANA ACTUAL
					// contamos el numero historico de conexiones la semana actual
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ref' AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ref' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana actual = ".$cuenta0['total'];	
					$total_conexiones_semana_actual = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_actual = $total_conexiones_semana_actual";
					mysql_free_result($registros0);

					
					//***CONEXIONES AL LAB - SEMANA ANTERIOR
					// contamos el numero historico de conexiones la semana anterior
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant' AND entrada < '$Lunes_ref' AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant' AND confir_resv < '$Lunes_ref' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana anterior = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior = $total_conexiones_semana_anterior";
					mysql_free_result($registros0);		
					
					
					//***CONEXIONES AL LAB - HACE 2 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_2 = date('Y-m-d',strtotime('-14 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_2' AND entrada < '$Lunes_ant' AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_2' AND confir_resv < '$Lunes_ant' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 2 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_2 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_2 = $total_conexiones_semana_anterior_2";
					mysql_free_result($registros0);
					

					//***CONEXIONES AL LAB - HACE 3 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_3 = date('Y-m-d',strtotime('-21 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_3' AND entrada < '$Lunes_ant_2' AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_3' AND confir_resv < '$Lunes_ant_2' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 3 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_3 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_3 = $total_conexiones_semana_anterior_3";
					mysql_free_result($registros0);


					//***CONEXIONES AL LAB - HACE 4 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_4' AND entrada < '$Lunes_ant_3' AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_4' AND confir_resv < '$Lunes_ant_3' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 4 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_4 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_4 = $total_conexiones_semana_anterior_4";
					mysql_free_result($registros0);


					//*CONEXIONES AL LAB - todas las ANTERIORES A HACE 4 SEMANAS 
					// contamos el numero historico de conexiones la semana anterior
					////$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada < '$Lunes_ant_4'";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv < '$Lunes_ant_4' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones antiguas = ".$cuenta0['total'];	
					$total_conexiones_antiguas = $cuenta0['total'];
					//echo "<br>total_conexiones_antiguas = $total_conexiones_antiguas";
					mysql_free_result($registros0);
		
					//echo "<br><br>";
				}
				
				else if ($tiempoID==2)      //estadisticas por dias
				{
					//******************************************************//
					//***CONEXIONES AL LAB POR TIEMPOS DE ENTRADA AL LAB	
					
					// seleccionamos las fechas de entrada de todas las conexiones
					//$sql="SELECT entrada FROM logs WHERE num_pod_lab IS NOT NULL AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str)";
					$sql="SELECT confir_resv FROM log_detalles WHERE in_pod IS NOT NULL AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str)";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count = mysql_num_rows($registros);
					$j = 0;
					if ($count > 0)
					{
						while ($reg = mysql_fetch_array($registros))
						{
						   $conexiones[$j] = $reg['confir_resv'];
						   $j++;
						}
					
						//inicializamos variable de almacenamiento para los dias de la semana
						$TotalDiasSemana = array(0,0,0,0,0,0,0);
						$ArraySemana = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado');
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$DayOfWeek = date( "w", strtotime($conexiones[$i]));
							switch ($DayOfWeek) {
								case 0:     //Domingo
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 1:     //Lunes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 2:     //Martes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 3:     //Mi&eacute;rcoles
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 4:     //Jueves
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 5:     //Viernes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 6:     //S&aacute;bado
									$TotalDiasSemana[$DayOfWeek]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por dias de la semana
						//for ($i=0; $i<7; $i++)
						//{
							//echo "$ArraySemana[$i] = $TotalDiasSemana[$i]  ";
						//}
						
					}  
					mysql_free_result($registros);
					
					//echo "<br><br>";
				
				}
				
				else   //if ($tiempoID==3)      //estadisticas por horas
				{
					//***CONEXIONES AL LAB POR TIEMPOS DE ENTRADA AL LAB - como en el punto anterior
					// seleccionamos las fechas de entrada de todas las conexiones
					//$sql="SELECT entrada FROM logs WHERE num_pod_lab IS NOT NULL AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str)";
					$sql="SELECT confir_resv FROM log_detalles WHERE in_pod IS NOT NULL AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str)";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count = mysql_num_rows($registros);
					$j = 0;
					if ($count > 0)
					{
						while ($reg = mysql_fetch_array($registros))
						{
						   $conexiones[$j] = $reg['confir_resv'];
						   $j++;
						}
							
						//inicializamos variable de almacenamiento para las horas de conexion
						$TotalIntervalos = array(0,0,0,0,0,0,0,0,0,0,0,0);
						$ArrayIntervalos = array('0 - 2 h','2 - 4 h','4 - 6 h','6 - 8 h','8 - 10 h','10 - 12 h','12 - 14 h','14 - 16 h','16 - 18 h','18 - 20 h','20 - 22 h','22 - 24 h');	
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$Intervalo = date( "H", strtotime($conexiones[$i]));
							$indice = floor($Intervalo / 2);
							switch ($indice) 
							{
								case 0:    
									$TotalIntervalos[$indice]++;
									break;
								case 1:    
									$TotalIntervalos[$indice]++;
									break;
								case 2:    
									$TotalIntervalos[$indice]++;
									break;
								case 3:    
									$TotalIntervalos[$indice]++;
									break;
								case 4:    
									$TotalIntervalos[$indice]++;
									break;
								case 5:    
									$TotalIntervalos[$indice]++;
									break;
								case 6:    
									$TotalIntervalos[$indice]++;
									break;
								case 7:    
									$TotalIntervalos[$indice]++;
									break;
								case 8:    
									$TotalIntervalos[$indice]++;
									break;
								case 9:    
									$TotalIntervalos[$indice]++;
									break;
								case 10:    
									$TotalIntervalos[$indice]++;
									break;
								case 11:    
									$TotalIntervalos[$indice]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por intervalos horarios diarios
						//for ($i=0; $i<12; $i++)
						//{
							//echo "$ArrayIntervalos[$i] = $TotalIntervalos[$i]; &nbsp;&nbsp;";
						//}
					
					}  
					mysql_free_result($registros);
					
					//echo "<br><br>";
				}
				
			}
			else if ($cursoID == 0)     //un grupo de cursos, con el mismo alumno   ($usuarioID > 0)
			{
				//pasamos los codigos de usuario almacenados en el array $usuarios a la cadena $usuarios_str
				$cursos_str = implode(",", $cursos); 
				
				//echo "<br>usuarios_str = $usuarios_str";
					
				if ($tiempoID==0)     //estadisticas por semanas, dias y horas
				{
					//buscamos el Lunes de la semana actual y el de la semana anterior
					$dia_sem = date('w', strtotime($hoy));
					//echo "<br>dia_sem = $dia_sem";

					$dia_semana = $dia_sem - 1;
					$Lunes_ref = date('Y-m-d',strtotime('-'.$dia_semana.' days', strtotime($f_ahora)));
					$Lunes_ant = date('Y-m-d',strtotime('-7 days', strtotime($Lunes_ref)));
					
					//***CONEXIONES AL LAB - SEMANA ACTUAL
					// contamos el numero historico de conexiones la semana actual
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ref' AND curso_id IN ($cursos_str) AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ref' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str) AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana actual = ".$cuenta0['total'];	
					$total_conexiones_semana_actual = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_actual = $total_conexiones_semana_actual";
					mysql_free_result($registros0);

					
					//***CONEXIONES AL LAB - SEMANA ANTERIOR
					// contamos el numero historico de conexiones la semana anterior
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant' AND entrada < '$Lunes_ref' AND curso_id IN ($cursos_str) AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant' AND confir_resv < '$Lunes_ref' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str) AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana anterior = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior = $total_conexiones_semana_anterior";
					mysql_free_result($registros0);		
					
					
					//***CONEXIONES AL LAB - HACE 2 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_2 = date('Y-m-d',strtotime('-14 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_2' AND entrada < '$Lunes_ant' AND curso_id IN ($cursos_str) AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_2' AND confir_resv < '$Lunes_ant' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str) AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 2 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_2 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_2 = $total_conexiones_semana_anterior_2";
					mysql_free_result($registros0);
					

					//***CONEXIONES AL LAB - HACE 3 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_3 = date('Y-m-d',strtotime('-21 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_3' AND entrada < '$Lunes_ant_2' AND curso_id IN ($cursos_str) AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_3' AND confir_resv < '$Lunes_ant_2' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str) AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 3 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_3 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_3 = $total_conexiones_semana_anterior_3";
					mysql_free_result($registros0);


					//***CONEXIONES AL LAB - HACE 4 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_4' AND entrada < '$Lunes_ant_3' AND curso_id IN ($cursos_str) AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_4' AND confir_resv < '$Lunes_ant_3' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str) AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 4 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_4 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_4 = $total_conexiones_semana_anterior_4";
					mysql_free_result($registros0);

					
					//*CONEXIONES AL LAB - todas las ANTERIORES A HACE 4 SEMANAS 
					// contamos el numero historico de conexiones la semana anterior
					////$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada < '$Lunes_ant_4' AND curso_id IN ($cursos_str) AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv < '$Lunes_ant_4' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str) AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones antiguas = ".$cuenta0['total'];	
					$total_conexiones_antiguas = $cuenta0['total'];
					//echo "<br>total_conexiones_antiguas = $total_conexiones_antiguas";
					mysql_free_result($registros0);
		
					//echo "<br><br>";
					
					
					
					//******************************************************//
					//***CONEXIONES AL LAB POR TIEMPOS DE ENTRADA AL LAB	
					
					// seleccionamos las fechas de entrada de todas las conexiones
					//$sql="SELECT entrada FROM logs WHERE num_pod_lab IS NOT NULL AND curso_id IN ($cursos_str) AND user_id = $usuarioID";
					$sql="SELECT confir_resv FROM log_detalles WHERE in_pod IS NOT NULL AND curso_id IN ($cursos_str) AND user_id = $usuarioID";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count = mysql_num_rows($registros);
					$j = 0;
					if ($count > 0)
					{
						while ($reg = mysql_fetch_array($registros))
						{
						   $conexiones[$j] = $reg['confir_resv'];
						   $j++;
						}
					
						//inicializamos variable de almacenamiento para los dias de la semana
						$TotalDiasSemana = array(0,0,0,0,0,0,0);
						$ArraySemana = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado');
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$DayOfWeek = date( "w", strtotime($conexiones[$i]));
							switch ($DayOfWeek) {
								case 0:     //Domingo
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 1:     //Lunes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 2:     //Martes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 3:     //Mi&eacute;rcoles
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 4:     //Jueves
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 5:     //Viernes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 6:     //S&aacute;bado
									$TotalDiasSemana[$DayOfWeek]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por dias de la semana
						//for ($i=0; $i<7; $i++)
						//{
							//echo "$ArraySemana[$i] = $TotalDiasSemana[$i]  ";
						//}
						
						//echo "<br><br>";
						
						
						//inicializamos variable de almacenamiento para las horas de conexion
						$TotalIntervalos = array(0,0,0,0,0,0,0,0,0,0,0,0);
						$ArrayIntervalos = array('0 - 2 h','2 - 4 h','4 - 6 h','6 - 8 h','8 - 10 h','10 - 12 h','12 - 14 h','14 - 16 h','16 - 18 h','18 - 20 h','20 - 22 h','22 - 24 h');	
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$Intervalo = date( "H", strtotime($conexiones[$i]));
							$indice = floor($Intervalo / 2);
							switch ($indice) 
							{
								case 0:    
									$TotalIntervalos[$indice]++;
									break;
								case 1:    
									$TotalIntervalos[$indice]++;
									break;
								case 2:    
									$TotalIntervalos[$indice]++;
									break;
								case 3:    
									$TotalIntervalos[$indice]++;
									break;
								case 4:    
									$TotalIntervalos[$indice]++;
									break;
								case 5:    
									$TotalIntervalos[$indice]++;
									break;
								case 6:    
									$TotalIntervalos[$indice]++;
									break;
								case 7:    
									$TotalIntervalos[$indice]++;
									break;
								case 8:    
									$TotalIntervalos[$indice]++;
									break;
								case 9:    
									$TotalIntervalos[$indice]++;
									break;
								case 10:    
									$TotalIntervalos[$indice]++;
									break;
								case 11:    
									$TotalIntervalos[$indice]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por intervalos horarios diarios
						//for ($i=0; $i<12; $i++)
						//{
							//echo "$ArrayIntervalos[$i] = $TotalIntervalos[$i]; &nbsp;&nbsp;";
						//}

					}  
					mysql_free_result($registros);
					
					//echo "<br><br>";				
				
				}
			 	else if ($tiempoID== 1)   //estadisticas por semanas
				{
					//***HISTORICO DE CONEXIONES AL LAB
					// contamos el numero historico de conexiones en todos los usuarios
					$total_conexiones_historico = $total_conexiones;
					//echo "<br>total_conexiones_historico = $total_conexiones_historico";

					//buscamos el Lunes de la semana actual y el de la semana anterior
					$dia_sem = date('w', strtotime($hoy));
					//echo "<br>dia_sem = $dia_sem";

					$dia_semana = $dia_sem - 1;
					$Lunes_ref = date('Y-m-d',strtotime('-'.$dia_semana.' days', strtotime($f_ahora)));
					$Lunes_ant = date('Y-m-d',strtotime('-7 days', strtotime($Lunes_ref)));
					
					//***CONEXIONES AL LAB - SEMANA ACTUAL
					// contamos el numero historico de conexiones la semana actual
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ref' AND curso_id IN ($cursos_str) AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ref' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str) AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana actual = ".$cuenta0['total'];	
					$total_conexiones_semana_actual = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_actual = $total_conexiones_semana_actual";
					mysql_free_result($registros0);

					
					//***CONEXIONES AL LAB - SEMANA ANTERIOR
					// contamos el numero historico de conexiones la semana anterior
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant' AND entrada < '$Lunes_ref' AND curso_id IN ($cursos_str) AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant' AND confir_resv < '$Lunes_ref' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str) AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana anterior = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior = $total_conexiones_semana_anterior";
					mysql_free_result($registros0);		
					
					
					//***CONEXIONES AL LAB - HACE 2 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_2 = date('Y-m-d',strtotime('-14 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_2' AND entrada < '$Lunes_ant' AND curso_id IN ($cursos_str) AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_2' AND confir_resv < '$Lunes_ant' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str) AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 2 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_2 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_2 = $total_conexiones_semana_anterior_2";
					mysql_free_result($registros0);
					

					//***CONEXIONES AL LAB - HACE 3 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_3 = date('Y-m-d',strtotime('-21 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_3' AND entrada < '$Lunes_ant_2' AND curso_id IN ($cursos_str) AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_3' AND confir_resv < '$Lunes_ant_2' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str) AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 3 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_3 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_3 = $total_conexiones_semana_anterior_3";
					mysql_free_result($registros0);


					//***CONEXIONES AL LAB - HACE 4 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_4' AND entrada < '$Lunes_ant_3' AND curso_id IN ($cursos_str) AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_4' AND confir_resv < '$Lunes_ant_3' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str) AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 4 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_4 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_4 = $total_conexiones_semana_anterior_4";
					mysql_free_result($registros0);


					//*CONEXIONES AL LAB - todas las ANTERIORES A HACE 4 SEMANAS 
					// contamos el numero historico de conexiones la semana anterior
					////$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada < '$Lunes_ant_4' AND curso_id IN ($cursos_str) AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv < '$Lunes_ant_4' AND in_pod IS NOT NULL AND curso_id IN ($cursos_str) AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones antiguas = ".$cuenta0['total'];	
					$total_conexiones_antiguas = $cuenta0['total'];
					//echo "<br>total_conexiones_antiguas = $total_conexiones_antiguas";
					mysql_free_result($registros0);
		
					//echo "<br><br>";
				}
				
				else if ($tiempoID==2)      //estadisticas por dias
				{
					//******************************************************//
					//***CONEXIONES AL LAB POR TIEMPOS DE ENTRADA AL LAB	
					
					// seleccionamos las fechas de entrada de todas las conexiones
					//$sql="SELECT entrada FROM logs WHERE num_pod_lab IS NOT NULL AND curso_id IN ($cursos_str) AND user_id = $usuarioID";
					$sql="SELECT confir_resv FROM log_detalles WHERE in_pod IS NOT NULL AND curso_id IN ($cursos_str) AND user_id = $usuarioID";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count = mysql_num_rows($registros);
					$j = 0;
					if ($count > 0)
					{
						while ($reg = mysql_fetch_array($registros))
						{
						   $conexiones[$j] = $reg['confir_resv'];
						   $j++;
						}
					
						//inicializamos variable de almacenamiento para los dias de la semana
						$TotalDiasSemana = array(0,0,0,0,0,0,0);
						$ArraySemana = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado');
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$DayOfWeek = date( "w", strtotime($conexiones[$i]));
							switch ($DayOfWeek) {
								case 0:     //Domingo
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 1:     //Lunes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 2:     //Martes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 3:     //Mi&eacute;rcoles
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 4:     //Jueves
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 5:     //Viernes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 6:     //S&aacute;bado
									$TotalDiasSemana[$DayOfWeek]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por dias de la semana
						//for ($i=0; $i<7; $i++)
						//{
							//echo "$ArraySemana[$i] = $TotalDiasSemana[$i]  ";
						//}
						
					}  
					mysql_free_result($registros);
					
					//echo "<br><br>";
				
				}
				
				else   //if ($tiempoID==3)      //estadisticas por horas
				{
					//***CONEXIONES AL LAB POR TIEMPOS DE ENTRADA AL LAB - como en el punto anterior
					// seleccionamos las fechas de entrada de todas las conexiones
					//$sql="SELECT entrada FROM logs WHERE num_pod_lab IS NOT NULL AND curso_id IN ($cursos_str) AND user_id = $usuarioID";
					$sql="SELECT confir_resv FROM log_detalles WHERE in_pod IS NOT NULL AND curso_id IN ($cursos_str) AND user_id = $usuarioID";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count = mysql_num_rows($registros);
					$j = 0;
					if ($count > 0)
					{
						while ($reg = mysql_fetch_array($registros))
						{
						   $conexiones[$j] = $reg['confir_resv'];
						   $j++;
						}
							
						//inicializamos variable de almacenamiento para las horas de conexion
						$TotalIntervalos = array(0,0,0,0,0,0,0,0,0,0,0,0);
						$ArrayIntervalos = array('0 - 2 h','2 - 4 h','4 - 6 h','6 - 8 h','8 - 10 h','10 - 12 h','12 - 14 h','14 - 16 h','16 - 18 h','18 - 20 h','20 - 22 h','22 - 24 h');	
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$Intervalo = date( "H", strtotime($conexiones[$i]));
							$indice = floor($Intervalo / 2);
							switch ($indice) 
							{
								case 0:    
									$TotalIntervalos[$indice]++;
									break;
								case 1:    
									$TotalIntervalos[$indice]++;
									break;
								case 2:    
									$TotalIntervalos[$indice]++;
									break;
								case 3:    
									$TotalIntervalos[$indice]++;
									break;
								case 4:    
									$TotalIntervalos[$indice]++;
									break;
								case 5:    
									$TotalIntervalos[$indice]++;
									break;
								case 6:    
									$TotalIntervalos[$indice]++;
									break;
								case 7:    
									$TotalIntervalos[$indice]++;
									break;
								case 8:    
									$TotalIntervalos[$indice]++;
									break;
								case 9:    
									$TotalIntervalos[$indice]++;
									break;
								case 10:    
									$TotalIntervalos[$indice]++;
									break;
								case 11:    
									$TotalIntervalos[$indice]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por intervalos horarios diarios
						//for ($i=0; $i<12; $i++)
						//{
							//echo "$ArrayIntervalos[$i] = $TotalIntervalos[$i]; &nbsp;&nbsp;";
						//}
					
					}  
					mysql_free_result($registros);
					
					//echo "<br><br>";
				}				
			
			
			}
			else if ($usuarioID == 0)    //un grupo de alumnos con el mismo curso    ($cursoID > 0)
			{
				//pasamos los codigos de usuario almacenados en el array $usuarios a la cadena $usuarios_str
				$usuarios_str = implode(",", $usuarios); 
				
				//echo "<br>usuarios_str = $usuarios_str";
					
				if ($tiempoID==0)     //estadisticas por semanas, dias y horas
				{
					//buscamos el Lunes de la semana actual y el de la semana anterior
					$dia_sem = date('w', strtotime($hoy));
					//echo "<br>dia_sem = $dia_sem";

					$dia_semana = $dia_sem - 1;
					$Lunes_ref = date('Y-m-d',strtotime('-'.$dia_semana.' days', strtotime($f_ahora)));
					$Lunes_ant = date('Y-m-d',strtotime('-7 days', strtotime($Lunes_ref)));
					
					//***CONEXIONES AL LAB - SEMANA ACTUAL
					// contamos el numero historico de conexiones la semana actual
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ref' AND curso_id = $cursoID AND user_id = IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ref' AND in_pod IS NOT NULL AND curso_id = $cursoID AND user_id = IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana actual = ".$cuenta0['total'];	
					$total_conexiones_semana_actual = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_actual = $total_conexiones_semana_actual";
					mysql_free_result($registros0);

					
					//***CONEXIONES AL LAB - SEMANA ANTERIOR
					// contamos el numero historico de conexiones la semana anterior
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant' AND entrada < '$Lunes_ref' AND curso_id = $cursoID AND user_id = IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant' AND confir_resv < '$Lunes_ref' AND in_pod IS NOT NULL AND curso_id = $cursoID AND user_id = IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana anterior = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior = $total_conexiones_semana_anterior";
					mysql_free_result($registros0);		
					
					
					//***CONEXIONES AL LAB - HACE 2 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_2 = date('Y-m-d',strtotime('-14 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_2' AND entrada < '$Lunes_ant' AND curso_id = $cursoID AND user_id = IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_2' AND confir_resv < '$Lunes_ant' AND in_pod IS NOT NULL AND curso_id = $cursoID AND user_id = IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 2 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_2 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_2 = $total_conexiones_semana_anterior_2";
					mysql_free_result($registros0);
					

					//***CONEXIONES AL LAB - HACE 3 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_3 = date('Y-m-d',strtotime('-21 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_3' AND entrada < '$Lunes_ant_2' AND curso_id = $cursoID AND user_id = IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_3' AND confir_resv < '$Lunes_ant_2' AND in_pod IS NOT NULL AND curso_id = $cursoID AND user_id = IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 3 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_3 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_3 = $total_conexiones_semana_anterior_3";
					mysql_free_result($registros0);


					//***CONEXIONES AL LAB - HACE 4 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_4' AND entrada < '$Lunes_ant_3' AND curso_id = $cursoID AND user_id = IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_4' AND confir_resv < '$Lunes_ant_3' AND in_pod IS NOT NULL AND curso_id = $cursoID AND user_id = IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 4 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_4 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_4 = $total_conexiones_semana_anterior_4";
					mysql_free_result($registros0);

					
					//*CONEXIONES AL LAB - todas las ANTERIORES A HACE 4 SEMANAS 
					// contamos el numero historico de conexiones la semana anterior
					////$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada < '$Lunes_ant_4' AND curso_id = $cursoID AND user_id = IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv < '$Lunes_ant_4' AND in_pod IS NOT NULL AND curso_id = $cursoID AND user_id = IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones antiguas = ".$cuenta0['total'];	
					$total_conexiones_antiguas = $cuenta0['total'];
					//echo "<br>total_conexiones_antiguas = $total_conexiones_antiguas";
					mysql_free_result($registros0);
		
					//echo "<br><br>";
					
					
					
					//******************************************************//
					//***CONEXIONES AL LAB POR TIEMPOS DE ENTRADA AL LAB	
					
					// seleccionamos las fechas de entrada de todas las conexiones
					//$sql="SELECT entrada FROM logs WHERE num_pod_lab IS NOT NULL AND curso_id = $cursoID AND user_id = IN ($usuarios_str)";
					$sql="SELECT confir_resv FROM log_detalles WHERE in_pod IS NOT NULL AND curso_id = $cursoID AND user_id = IN ($usuarios_str)";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count = mysql_num_rows($registros);
					$j = 0;
					if ($count > 0)
					{
						while ($reg = mysql_fetch_array($registros))
						{
						   $conexiones[$j] = $reg['confir_resv'];
						   $j++;
						}
					
						//inicializamos variable de almacenamiento para los dias de la semana
						$TotalDiasSemana = array(0,0,0,0,0,0,0);
						$ArraySemana = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado');
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$DayOfWeek = date( "w", strtotime($conexiones[$i]));
							switch ($DayOfWeek) {
								case 0:     //Domingo
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 1:     //Lunes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 2:     //Martes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 3:     //Mi&eacute;rcoles
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 4:     //Jueves
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 5:     //Viernes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 6:     //S&aacute;bado
									$TotalDiasSemana[$DayOfWeek]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por dias de la semana
						//for ($i=0; $i<7; $i++)
						//{
							//echo "$ArraySemana[$i] = $TotalDiasSemana[$i]  ";
						//}
						
						//echo "<br><br>";
						
						
						//inicializamos variable de almacenamiento para las horas de conexion
						$TotalIntervalos = array(0,0,0,0,0,0,0,0,0,0,0,0);
						$ArrayIntervalos = array('0 - 2 h','2 - 4 h','4 - 6 h','6 - 8 h','8 - 10 h','10 - 12 h','12 - 14 h','14 - 16 h','16 - 18 h','18 - 20 h','20 - 22 h','22 - 24 h');	
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$Intervalo = date( "H", strtotime($conexiones[$i]));
							$indice = floor($Intervalo / 2);
							switch ($indice) 
							{
								case 0:    
									$TotalIntervalos[$indice]++;
									break;
								case 1:    
									$TotalIntervalos[$indice]++;
									break;
								case 2:    
									$TotalIntervalos[$indice]++;
									break;
								case 3:    
									$TotalIntervalos[$indice]++;
									break;
								case 4:    
									$TotalIntervalos[$indice]++;
									break;
								case 5:    
									$TotalIntervalos[$indice]++;
									break;
								case 6:    
									$TotalIntervalos[$indice]++;
									break;
								case 7:    
									$TotalIntervalos[$indice]++;
									break;
								case 8:    
									$TotalIntervalos[$indice]++;
									break;
								case 9:    
									$TotalIntervalos[$indice]++;
									break;
								case 10:    
									$TotalIntervalos[$indice]++;
									break;
								case 11:    
									$TotalIntervalos[$indice]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por intervalos horarios diarios
						//for ($i=0; $i<12; $i++)
						//{
							//echo "$ArrayIntervalos[$i] = $TotalIntervalos[$i]; &nbsp;&nbsp;";
						//}

					}  
					mysql_free_result($registros);
					
					//echo "<br><br>";				
				
				}
			 	else if ($tiempoID== 1)   //estadisticas por semanas
				{
					//***HISTORICO DE CONEXIONES AL LAB
					// contamos el numero historico de conexiones en todos los usuarios
					$total_conexiones_historico = $total_conexiones;
					//echo "<br>total_conexiones_historico = $total_conexiones_historico";

					//buscamos el Lunes de la semana actual y el de la semana anterior
					$dia_sem = date('w', strtotime($hoy));
					//echo "<br>dia_sem = $dia_sem";

					$dia_semana = $dia_sem - 1;
					$Lunes_ref = date('Y-m-d',strtotime('-'.$dia_semana.' days', strtotime($f_ahora)));
					$Lunes_ant = date('Y-m-d',strtotime('-7 days', strtotime($Lunes_ref)));
					
					//***CONEXIONES AL LAB - SEMANA ACTUAL
					// contamos el numero historico de conexiones la semana actual
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ref' AND curso_id = $cursoID AND user_id = IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ref' AND in_pod IS NOT NULL AND curso_id = $cursoID AND user_id = IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana actual = ".$cuenta0['total'];	
					$total_conexiones_semana_actual = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_actual = $total_conexiones_semana_actual";
					mysql_free_result($registros0);

					
					//***CONEXIONES AL LAB - SEMANA ANTERIOR
					// contamos el numero historico de conexiones la semana anterior
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant' AND entrada < '$Lunes_ref' AND curso_id = $cursoID AND user_id = IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant' AND confir_resv < '$Lunes_ref' AND in_pod IS NOT NULL AND curso_id = $cursoID AND user_id = IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana anterior = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior = $total_conexiones_semana_anterior";
					mysql_free_result($registros0);		
					
					
					//***CONEXIONES AL LAB - HACE 2 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_2 = date('Y-m-d',strtotime('-14 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_2' AND entrada < '$Lunes_ant' AND curso_id = $cursoID AND user_id = IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_2' AND confir_resv < '$Lunes_ant' AND in_pod IS NOT NULL AND curso_id = $cursoID AND user_id = IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 2 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_2 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_2 = $total_conexiones_semana_anterior_2";
					mysql_free_result($registros0);
					

					//***CONEXIONES AL LAB - HACE 3 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_3 = date('Y-m-d',strtotime('-21 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_3' AND entrada < '$Lunes_ant_2' AND curso_id = $cursoID AND user_id = IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_3' AND confir_resv < '$Lunes_ant_2' AND in_pod IS NOT NULL AND curso_id = $cursoID AND user_id = IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 3 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_3 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_3 = $total_conexiones_semana_anterior_3";
					mysql_free_result($registros0);


					//***CONEXIONES AL LAB - HACE 4 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_4' AND entrada < '$Lunes_ant_3' AND curso_id = $cursoID AND user_id = IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_4' AND confir_resv < '$Lunes_ant_3' AND in_pod IS NOT NULL AND curso_id = $cursoID AND user_id = IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 4 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_4 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_4 = $total_conexiones_semana_anterior_4";
					mysql_free_result($registros0);


					//*CONEXIONES AL LAB - todas las ANTERIORES A HACE 4 SEMANAS 
					// contamos el numero historico de conexiones la semana anterior
					////$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada < '$Lunes_ant_4' AND curso_id = $cursoID AND user_id = IN ($usuarios_str)";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv < '$Lunes_ant_4' AND in_pod IS NOT NULL AND curso_id = $cursoID AND user_id = IN ($usuarios_str)";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones antiguas = ".$cuenta0['total'];	
					$total_conexiones_antiguas = $cuenta0['total'];
					//echo "<br>total_conexiones_antiguas = $total_conexiones_antiguas";
					mysql_free_result($registros0);
		
					//echo "<br><br>";
				}
				
				else if ($tiempoID==2)      //estadisticas por dias
				{
					//******************************************************//
					//***CONEXIONES AL LAB POR TIEMPOS DE ENTRADA AL LAB	
					
					// seleccionamos las fechas de entrada de todas las conexiones
					//$sql="SELECT entrada FROM logs WHERE num_pod_lab IS NOT NULL AND curso_id = $cursoID AND user_id = IN ($usuarios_str)";				
					$sql="SELECT confir_resv FROM log_detalles WHERE in_pod IS NOT NULL AND curso_id = $cursoID AND user_id = IN ($usuarios_str)";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count = mysql_num_rows($registros);
					$j = 0;
					if ($count > 0)
					{
						while ($reg = mysql_fetch_array($registros))
						{
						   $conexiones[$j] = $reg['confir_resv'];
						   $j++;
						}
					
						//inicializamos variable de almacenamiento para los dias de la semana
						$TotalDiasSemana = array(0,0,0,0,0,0,0);
						$ArraySemana = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado');
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$DayOfWeek = date( "w", strtotime($conexiones[$i]));
							switch ($DayOfWeek) {
								case 0:     //Domingo
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 1:     //Lunes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 2:     //Martes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 3:     //Mi&eacute;rcoles
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 4:     //Jueves
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 5:     //Viernes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 6:     //S&aacute;bado
									$TotalDiasSemana[$DayOfWeek]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por dias de la semana
						//for ($i=0; $i<7; $i++)
						//{
							//echo "$ArraySemana[$i] = $TotalDiasSemana[$i]  ";
						//}
						
					}  
					mysql_free_result($registros);
					
					//echo "<br><br>";
				
				}
				
				else   //if ($tiempoID==3)      //estadisticas por horas
				{
					//***CONEXIONES AL LAB POR TIEMPOS DE ENTRADA AL LAB - como en el punto anterior
					// seleccionamos las fechas de entrada de todas las conexiones
					//$sql="SELECT entrada FROM logs WHERE num_pod_lab IS NOT NULL AND curso_id = $cursoID AND user_id = IN ($usuarios_str)";					
					$sql="SELECT confir_resv FROM log_detalles WHERE in_pod IS NOT NULL AND curso_id = $cursoID AND user_id = IN ($usuarios_str)";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count = mysql_num_rows($registros);
					$j = 0;
					if ($count > 0)
					{
						while ($reg = mysql_fetch_array($registros))
						{
						   $conexiones[$j] = $reg['confir_resv'];
						   $j++;
						}
							
						//inicializamos variable de almacenamiento para las horas de conexion
						$TotalIntervalos = array(0,0,0,0,0,0,0,0,0,0,0,0);
						$ArrayIntervalos = array('0 - 2 h','2 - 4 h','4 - 6 h','6 - 8 h','8 - 10 h','10 - 12 h','12 - 14 h','14 - 16 h','16 - 18 h','18 - 20 h','20 - 22 h','22 - 24 h');
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$Intervalo = date( "H", strtotime($conexiones[$i]));
							$indice = floor($Intervalo / 2);
							switch ($indice) 
							{
								case 0:    
									$TotalIntervalos[$indice]++;
									break;
								case 1:    
									$TotalIntervalos[$indice]++;
									break;
								case 2:    
									$TotalIntervalos[$indice]++;
									break;
								case 3:    
									$TotalIntervalos[$indice]++;
									break;
								case 4:    
									$TotalIntervalos[$indice]++;
									break;
								case 5:    
									$TotalIntervalos[$indice]++;
									break;
								case 6:    
									$TotalIntervalos[$indice]++;
									break;
								case 7:    
									$TotalIntervalos[$indice]++;
									break;
								case 8:    
									$TotalIntervalos[$indice]++;
									break;
								case 9:    
									$TotalIntervalos[$indice]++;
									break;
								case 10:    
									$TotalIntervalos[$indice]++;
									break;
								case 11:    
									$TotalIntervalos[$indice]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por intervalos horarios diarios
						//for ($i=0; $i<12; $i++)
						//{
							//echo "$ArrayIntervalos[$i] = $TotalIntervalos[$i]; &nbsp;&nbsp;";
						//}
					
					}  
					mysql_free_result($registros);
					
					//echo "<br><br>";
				}				
						
			
			} 
			else        //un alumno particular dentro de un curso particular        ($usuarioID > 0 && $cursoID > 0)
			{
					
				if ($tiempoID==0)     //estadisticas por semanas, dias y horas
				{
					//buscamos el Lunes de la semana actual y el de la semana anterior
					$dia_sem = date('w', strtotime($hoy));
					//echo "<br>dia_sem = $dia_sem";

					$dia_semana = $dia_sem - 1;
					$Lunes_ref = date('Y-m-d',strtotime('-'.$dia_semana.' days', strtotime($f_ahora)));
					$Lunes_ant = date('Y-m-d',strtotime('-7 days', strtotime($Lunes_ref)));
					
					//***CONEXIONES AL LAB - SEMANA ACTUAL
					// contamos el numero historico de conexiones la semana actual
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ref' AND curso_id = $cursoID AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ref' AND in_pod IS NOT NULL";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana actual = ".$cuenta0['total'];	
					$total_conexiones_semana_actual = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_actual = $total_conexiones_semana_actual";
					mysql_free_result($registros0);

					
					//***CONEXIONES AL LAB - SEMANA ANTERIOR
					// contamos el numero historico de conexiones la semana anterior
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant' AND entrada < '$Lunes_ref' AND curso_id = $cursoID AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant' AND confir_resv < '$Lunes_ref' AND in_pod IS NOT NULL AND curso_id = $cursoID AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana anterior = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior = $total_conexiones_semana_anterior";
					mysql_free_result($registros0);		
					
					
					//***CONEXIONES AL LAB - HACE 2 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_2 = date('Y-m-d',strtotime('-14 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_2' AND entrada < '$Lunes_ant' AND curso_id = $cursoID AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_2' AND confir_resv < '$Lunes_ant' AND in_pod IS NOT NULL AND curso_id = $cursoID AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 2 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_2 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_2 = $total_conexiones_semana_anterior_2";
					mysql_free_result($registros0);
					

					//***CONEXIONES AL LAB - HACE 3 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_3 = date('Y-m-d',strtotime('-21 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_3' AND entrada < '$Lunes_ant_2' AND curso_id = $cursoID AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_3' AND confir_resv < '$Lunes_ant_2' AND in_pod IS NOT NULL AND curso_id = $cursoID AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 3 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_3 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_3 = $total_conexiones_semana_anterior_3";
					mysql_free_result($registros0);


					//***CONEXIONES AL LAB - HACE 4 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_4' AND entrada < '$Lunes_ant_3' AND curso_id = $cursoID AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_4' AND confir_resv < '$Lunes_ant_3' AND in_pod IS NOT NULL AND curso_id = $cursoID AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 4 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_4 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_4 = $total_conexiones_semana_anterior_4";
					mysql_free_result($registros0);

					
					//*CONEXIONES AL LAB - todas las ANTERIORES A HACE 4 SEMANAS 
					// contamos el numero historico de conexiones la semana anterior
					////$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada < '$Lunes_ant_4' AND curso_id = $cursoID AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv < '$Lunes_ant_4' AND in_pod IS NOT NULL AND curso_id = $cursoID AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones antiguas = ".$cuenta0['total'];	
					$total_conexiones_antiguas = $cuenta0['total'];
					//echo "<br>total_conexiones_antiguas = $total_conexiones_antiguas";
					mysql_free_result($registros0);
		
					//echo "<br><br>";
					
					
					
					//******************************************************//
					//***CONEXIONES AL LAB POR TIEMPOS DE ENTRADA AL LAB	
					
					// seleccionamos las fechas de entrada de todas las conexiones
					//$sql="SELECT entrada FROM logs WHERE num_pod_lab IS NOT NULL AND curso_id = $cursoID AND user_id = $usuarioID";
					$sql="SELECT confir_resv FROM log_detalles WHERE in_pod IS NOT NULL AND curso_id = $cursoID AND user_id = $usuarioID";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count = mysql_num_rows($registros);
					$j = 0;
					if ($count > 0)
					{
						while ($reg = mysql_fetch_array($registros))
						{
						   $conexiones[$j] = $reg['confir_resv'];
						   $j++;
						}
					
						//inicializamos variable de almacenamiento para los dias de la semana
						$TotalDiasSemana = array(0,0,0,0,0,0,0);
						$ArraySemana = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado');
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$DayOfWeek = date( "w", strtotime($conexiones[$i]));
							switch ($DayOfWeek) {
								case 0:     //Domingo
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 1:     //Lunes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 2:     //Martes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 3:     //Mi&eacute;rcoles
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 4:     //Jueves
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 5:     //Viernes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 6:     //S&aacute;bado
									$TotalDiasSemana[$DayOfWeek]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por dias de la semana
						//for ($i=0; $i<7; $i++)
						//{
							//echo "$ArraySemana[$i] = $TotalDiasSemana[$i]  ";
						//}
						
						//echo "<br><br>";
						
						
						//inicializamos variable de almacenamiento para las horas de conexion
						$TotalIntervalos = array(0,0,0,0,0,0,0,0,0,0,0,0);
						$ArrayIntervalos = array('0 - 2 h','2 - 4 h','4 - 6 h','6 - 8 h','8 - 10 h','10 - 12 h','12 - 14 h','14 - 16 h','16 - 18 h','18 - 20 h','20 - 22 h','22 - 24 h');	
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$Intervalo = date( "H", strtotime($conexiones[$i]));
							$indice = floor($Intervalo / 2);
							switch ($indice) 
							{
								case 0:    
									$TotalIntervalos[$indice]++;
									break;
								case 1:    
									$TotalIntervalos[$indice]++;
									break;
								case 2:    
									$TotalIntervalos[$indice]++;
									break;
								case 3:    
									$TotalIntervalos[$indice]++;
									break;
								case 4:    
									$TotalIntervalos[$indice]++;
									break;
								case 5:    
									$TotalIntervalos[$indice]++;
									break;
								case 6:    
									$TotalIntervalos[$indice]++;
									break;
								case 7:    
									$TotalIntervalos[$indice]++;
									break;
								case 8:    
									$TotalIntervalos[$indice]++;
									break;
								case 9:    
									$TotalIntervalos[$indice]++;
									break;
								case 10:    
									$TotalIntervalos[$indice]++;
									break;
								case 11:    
									$TotalIntervalos[$indice]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por intervalos horarios diarios
						//for ($i=0; $i<12; $i++)
						//{
							//echo "$ArrayIntervalos[$i] = $TotalIntervalos[$i]; &nbsp;&nbsp;";
						//}

					}  
					mysql_free_result($registros);
					
					echo "<br><br>";				
				
				}
				else if ($tiempoID== 1)   //estadisticas por semanas
				{
					//***HISTORICO DE CONEXIONES AL LAB
					// contamos el numero historico de conexiones en todos los usuarios
					$total_conexiones_historico = $total_conexiones;
					//echo "<br>total_conexiones_historico = $total_conexiones_historico";

					//buscamos el Lunes de la semana actual y el de la semana anterior
					$dia_sem = date('w', strtotime($hoy));
					//echo "<br>dia_sem = $dia_sem";

					$dia_semana = $dia_sem - 1;
					$Lunes_ref = date('Y-m-d',strtotime('-'.$dia_semana.' days', strtotime($f_ahora)));
					$Lunes_ant = date('Y-m-d',strtotime('-7 days', strtotime($Lunes_ref)));
					
					//***CONEXIONES AL LAB - SEMANA ACTUAL
					// contamos el numero historico de conexiones la semana actual
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ref' AND curso_id = $cursoID AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ref' AND in_pod IS NOT NULL AND curso_id = $cursoID AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana actual = ".$cuenta0['total'];	
					$total_conexiones_semana_actual = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_actual = $total_conexiones_semana_actual";
					mysql_free_result($registros0);

					
					//***CONEXIONES AL LAB - SEMANA ANTERIOR
					// contamos el numero historico de conexiones la semana anterior
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant' AND entrada < '$Lunes_ref' AND curso_id = $cursoID AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant' AND confir_resv < '$Lunes_ref' AND in_pod IS NOT NULL AND curso_id = $cursoID AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones en la semana anterior = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior = $total_conexiones_semana_anterior";
					mysql_free_result($registros0);		
					
					
					//***CONEXIONES AL LAB - HACE 2 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_2 = date('Y-m-d',strtotime('-14 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_2' AND entrada < '$Lunes_ant' AND curso_id = $cursoID AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_2' AND confir_resv < '$Lunes_ant' AND in_pod IS NOT NULL AND curso_id = $cursoID AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 2 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_2 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_2 = $total_conexiones_semana_anterior_2";
					mysql_free_result($registros0);
					

					//***CONEXIONES AL LAB - HACE 3 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_3 = date('Y-m-d',strtotime('-21 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_3' AND entrada < '$Lunes_ant_2' AND curso_id = $cursoID AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_3' AND confir_resv < '$Lunes_ant_2' AND in_pod IS NOT NULL AND curso_id = $cursoID AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 3 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_3 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_3 = $total_conexiones_semana_anterior_3";
					mysql_free_result($registros0);


					//***CONEXIONES AL LAB - HACE 4 SEMANAS
					// contamos el numero historico de conexiones la semana anterior
					$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_4' AND entrada < '$Lunes_ant_3' AND curso_id = $cursoID AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv >= '$Lunes_ant_4' AND confir_resv < '$Lunes_ant_3' AND in_pod IS NOT NULL AND curso_id = $cursoID AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones hace 4 semanas = ".$cuenta0['total'];	
					$total_conexiones_semana_anterior_4 = $cuenta0['total'];
					//echo "<br>total_conexiones_semana_anterior_4 = $total_conexiones_semana_anterior_4";
					mysql_free_result($registros0);


					//*CONEXIONES AL LAB - todas las ANTERIORES A HACE 4 SEMANAS 
					// contamos el numero historico de conexiones la semana anterior
					////$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada < '$Lunes_ant_4' AND curso_id = $cursoID AND user_id = $usuarioID";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv < '$Lunes_ant_4' AND in_pod IS NOT NULL AND curso_id = $cursoID AND user_id = $usuarioID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones antiguas = ".$cuenta0['total'];	
					$total_conexiones_antiguas = $cuenta0['total'];
					//echo "<br>total_conexiones_antiguas = $total_conexiones_antiguas";
					mysql_free_result($registros0);
		
					//echo "<br><br>";
				}
				
				else if ($tiempoID==2)      //estadisticas por dias
				{
					//******************************************************//
					//***CONEXIONES AL LAB POR TIEMPOS DE ENTRADA AL LAB	
					
					// seleccionamos las fechas de entrada de todas las conexiones
					//$sql="SELECT entrada FROM logs WHERE num_pod_lab IS NOT NULL AND curso_id = $cursoID AND user_id = $usuarioID";
					$sql="SELECT confir_resv FROM log_detalles WHERE in_pod IS NOT NULL AND curso_id = $cursoID AND user_id = $usuarioID";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count = mysql_num_rows($registros);
					$j = 0;
					if ($count > 0)
					{
						while ($reg = mysql_fetch_array($registros))
						{
						   $conexiones[$j] = $reg['confir_resv'];
						   $j++;
						}
					
						//inicializamos variable de almacenamiento para los dias de la semana
						$TotalDiasSemana = array(0,0,0,0,0,0,0);
						$ArraySemana = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado');
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$DayOfWeek = date( "w", strtotime($conexiones[$i]));
							switch ($DayOfWeek) {
								case 0:     //Domingo
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 1:     //Lunes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 2:     //Martes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 3:     //Mi&eacute;rcoles
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 4:     //Jueves
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 5:     //Viernes
									$TotalDiasSemana[$DayOfWeek]++;
									break;
								case 6:     //S&aacute;bado
									$TotalDiasSemana[$DayOfWeek]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por dias de la semana
						//for ($i=0; $i<7; $i++)
						//{
							//echo "$ArraySemana[$i] = $TotalDiasSemana[$i]  ";
						//}
						
					}  
					mysql_free_result($registros);
					
					//echo "<br><br>";
				
				}
				
				else   //if ($tiempoID==3)      //estadisticas por horas
				{
					//***CONEXIONES AL LAB POR TIEMPOS DE ENTRADA AL LAB - como en el punto anterior
					// seleccionamos las fechas de entrada de todas las conexiones
					//$sql="SELECT entrada FROM logs WHERE num_pod_lab IS NOT NULL AND curso_id = $cursoID AND user_id = $usuarioID";
					$sql="SELECT confir_resv FROM log_detalles WHERE in_pod IS NOT NULL AND curso_id = $cursoID AND user_id = $usuarioID";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count = mysql_num_rows($registros);
					$j = 0;
					if ($count > 0)
					{
						while ($reg = mysql_fetch_array($registros))
						{
						   $conexiones[$j] = $reg['confir_resv'];
						   $j++;
						}
							
						//inicializamos variable de almacenamiento para las horas de conexion
						$TotalIntervalos = array(0,0,0,0,0,0,0,0,0,0,0,0);
						$ArrayIntervalos = array('0 - 2 h','2 - 4 h','4 - 6 h','6 - 8 h','8 - 10 h','10 - 12 h','12 - 14 h','14 - 16 h','16 - 18 h','18 - 20 h','20 - 22 h','22 - 24 h');	
						
						// separamos las conexiones por dia de la semana
						for ($i=0; $i<count($conexiones); $i++)
						{
							$Intervalo = date( "H", strtotime($conexiones[$i]));
							$indice = floor($Intervalo / 2);
							switch ($indice) 
							{
								case 0:    
									$TotalIntervalos[$indice]++;
									break;
								case 1:    
									$TotalIntervalos[$indice]++;
									break;
								case 2:    
									$TotalIntervalos[$indice]++;
									break;
								case 3:    
									$TotalIntervalos[$indice]++;
									break;
								case 4:    
									$TotalIntervalos[$indice]++;
									break;
								case 5:    
									$TotalIntervalos[$indice]++;
									break;
								case 6:    
									$TotalIntervalos[$indice]++;
									break;
								case 7:    
									$TotalIntervalos[$indice]++;
									break;
								case 8:    
									$TotalIntervalos[$indice]++;
									break;
								case 9:    
									$TotalIntervalos[$indice]++;
									break;
								case 10:    
									$TotalIntervalos[$indice]++;
									break;
								case 11:    
									$TotalIntervalos[$indice]++;
									break;					
							}  
						}
						
						////imprimimos el resultado de la clasificacion por intervalos horarios diarios
						//for ($i=0; $i<12; $i++)
						//{
							//echo "$ArrayIntervalos[$i] = $TotalIntervalos[$i]; &nbsp;&nbsp;";
						//}
					
					}  
					mysql_free_result($registros);
					
					//echo "<br><br>";
				}				
						
			
			}

		
		}
		

?>

        <input type="text" name="estadisticasTemporales" value=" Estad&iacute;sticas Temporales" 
 		id="estadisticasTemporales" style="width: 230px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:12pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  

<?php  if ($tiempoID==0 || $tiempoID==1)
		{  ?>

			<center>
			   <table border="1" cellpadding="10" cellspacing="0" width="85%" class="thin sortable draggable">
					<tr>
						<th width="11%"> </th>
						<th bgcolor="khaki" width="15%"> <?php echo "N&ordm; conexiones en la Semana Actual"; ?>  </th>
						<th bgcolor="khaki" width="15%"> <?php echo "N&ordm; conexiones en la Semana Anterior"; ?>  </th>					
						<th bgcolor="khaki" width="15%"> <?php echo "N&ordm; conexiones  <br>  hace 2 Semanas"; ?>  </th>	
						<th bgcolor="khaki" width="15%"> <?php echo "N&ordm; conexiones  <br>  hace 3 Semanas"; ?>  </th>		
						<th bgcolor="khaki" width="15%"> <?php echo "N&ordm; conexiones  <br>  hace 4 Semanas"; ?>  </th>
						<th bgcolor="khaki" width="15%"> <?php echo "N&ordm; conexiones  <br>  hace m&aacute;s de 1 mes"; ?>  </th>						
					</tr>

					<tr>
						<th bgcolor="aquamarine" width="11%" align="center"> por SEMANAS  </th>
						<td width="15%" bgcolor="#fcfcfc" align="right"> <?php echo "$total_conexiones_semana_actual"; ?>  </td>	
						<td width="15%" bgcolor="#fcfcfc" align="right"> <?php echo "$total_conexiones_semana_anterior"; ?>  </td>
						<td width="15%" bgcolor="#fcfcfc" align="right"> <?php echo "$total_conexiones_semana_anterior_2"; ?>  </td>
						<td width="15%" bgcolor="#fcfcfc" align="right"> <?php echo "$total_conexiones_semana_anterior_3"; ?>  </td>		
						<td width="15%" bgcolor="#fcfcfc" align="right"> <?php echo "$total_conexiones_semana_anterior_4"; ?>  </td>	
						<td width="15%" bgcolor="#fcfcfc" align="right"> <?php echo "$total_conexiones_antiguas"; ?>  </td>						
					</tr>
								
				</table>
			</center>
			<br>

<?php
        }
		
		if ($tiempoID==0 || $tiempoID==2)
		{  ?>
		
			<center>
			   <table border="1" cellpadding="10" cellspacing="0" width="85%" class="thin sortable draggable">
					<tr>
						<th width="11%"> </th>
						
<?php                   for ($i=1; $i<count($ArraySemana); $i++){  ?>
							<th bgcolor="khaki" width="11%"> <?php echo "$ArraySemana[$i]"; ?>  </th>
<?php					}  ?>	
					    <th bgcolor="khaki" width="11%"> <?php echo "$ArraySemana[0]"; ?>  </th>
					</tr>

					<tr>
						<th bgcolor="aquamarine" width="11%" align="center"> por DIAS  </th>
						
<?php                   for ($i=1; $i<count($ArraySemana); $i++){  ?>
							<td width="11%" bgcolor="#fcfcfc"align="right"> <?php echo "$TotalDiasSemana[$i]"; ?>  </td>	
<?php					}  ?>			
						<td width="11%" bgcolor="#fcfcfc" align="right"> <?php echo "$TotalDiasSemana[0]"; ?>  </td>
					</tr>
								
				</table>
			</center>
			<br>

<?php
        }
		
		if ($tiempoID==0 || $tiempoID==3)
		{  ?>
	
			<center>
			   <table border="1" cellpadding="10" cellspacing="0" width="85%" class="thin sortable draggable">
					<tr>
						<th width="12%"> </th>
						
<?php                   for ($i=0; $i<count($ArrayIntervalos); $i++){  ?>
							<th bgcolor="khaki" width="7%"> <?php echo "$ArrayIntervalos[$i]"; ?>  </th>
<?php					}  ?>	
					</tr>

					<tr>
						<th bgcolor="aquamarine" width="12%" align="center"> por HORAS  </th>
						
<?php                   for ($i=0; $i<count($ArrayIntervalos); $i++){  ?>
							<td width="7%" bgcolor="#fcfcfc" align="right"> <?php echo "$TotalIntervalos[$i]"; ?>  </td>	
<?php					}  ?>			
					</tr>
								
				</table>
			</center>
			<br>

<?php
        }
	}	
	
	

	echo "<hr>";
		
		


	//****ESTADISTICAS POR ADMINISTRADORES****//
	if ($stat_admins == 1)
	{	
		if ($adminsID == 0)    /////se seleccionan todos los administradores
		{	
			if (!isset ($cursoID))    /////si NO se ha seleccionado algun curso
			{
				// contamos el numero historico de accesos de administrador
				$sql0="SELECT count(*) AS total FROM logs WHERE user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 )";
				$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
				$cuenta0=mysql_fetch_assoc($registros0);
				//echo "<br>Total accesos admin = ".$cuenta0['total'];	
				$total_accesos_admin = $cuenta0['total'];
				mysql_free_result($registros0);	

				// contamos el numero historico de reservas de administrador
				//$sql0="SELECT count(*) AS total FROM reservas WHERE user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 )";
				$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv IS NOT NULL AND user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 )";
				$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
				$cuenta0=mysql_fetch_assoc($registros0);
				//echo "<br>Total reservas admin = ".$cuenta0['total'];	
				$total_reservas_admin = $cuenta0['total'];
				mysql_free_result($registros0);	
				
				// contamos el numero historico de conexiones de administrador  
				//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 )";
				$sql0="SELECT count(*) AS total FROM log_detalles WHERE in_pod IS NOT NULL AND user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 )";
				$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
				$cuenta0=mysql_fetch_assoc($registros0);
				//echo "<br>Total conexiones admin = ".$cuenta0['total'];	
				$total_conexiones_admin = $cuenta0['total'];
				mysql_free_result($registros0);	
		
		
				// contamos el numero historico de intervalos de mantenimiento 
				$sql0="SELECT count(*) AS total FROM mantenimiento";
				$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
				$cuenta0=mysql_fetch_assoc($registros0);
				//echo "<br>Total intervalos de mantenimiento = ".$cuenta0['total'];	
				$total_outage = $cuenta0['total'];
				mysql_free_result($registros0);	
			
			
				// seleccionamos los user_id y username de todos los administradores
				$sql1="SELECT user_id, username FROM usuarios WHERE admin = 1";
				$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
				$cuenta1=mysql_num_rows($registros1);
				$total_admins = $cuenta1;
				//echo "<br>Num.administradores = $total_admins";
				
				if ($cuenta1 > 0)
				{
					$j = 0;
					while ($reg1 = mysql_fetch_array($registros1))
					{	
					   $administradores[$j] = $reg1['user_id'];
					   $nombre_administradores[$j] = $reg1['username'];
					   $j++;
					}
					mysql_free_result($registros1);
				
					// contamos el numero historico de accesos para cada administrador
					for ($i=0; $i<count($administradores); $i++)
					{		
						$sql2="SELECT count(*) AS total FROM logs WHERE user_id = $administradores[$i]";
						$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
						$cuenta2=mysql_fetch_assoc($registros2);
						$contador_accesos_admin[$i] = $cuenta2['total'];
						mysql_free_result($registros2);
					}

					// contamos el numero historico de reservas para cada administrador
					for ($i=0; $i<count($administradores); $i++)
					{		
						//$sql2="SELECT count(*) AS total FROM reservas WHERE user_id = $administradores[$i]";
						$sql2="SELECT count(*) AS total FROM log_detalles WHERE confir_resv IS NOT NULL AND user_id = $administradores[$i]";
						$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
						$cuenta2=mysql_fetch_assoc($registros2);
						$contador_reservas_admin[$i] = $cuenta2['total'];
						mysql_free_result($registros2);
					}

					// contamos el numero historico de conexiones para cada administrador
					for ($i=0; $i<count($administradores); $i++)
					{		
						//$sql2="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND user_id = $administradores[$i]";
						$sql2="SELECT count(*) AS total FROM log_detalles WHERE in_pod IS NOT NULL AND user_id = $administradores[$i]";
						$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
						$cuenta2=mysql_fetch_assoc($registros2);
						$contador_conexiones_admin[$i] = $cuenta2['total'];
						mysql_free_result($registros2);
					}
			
					// contamos el numero historico de intervalos de mantenimiento de cada administrador
					for ($i=0; $i<count($administradores); $i++)
					{		
						$sql2="SELECT count(*) AS total FROM mantenimiento WHERE admin_id = $administradores[$i]";
						$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
						$cuenta2=mysql_fetch_assoc($registros2);
						$contador_outage[$i] = $cuenta2['total'];
						mysql_free_result($registros2);
					}	
				}
				else
					echo "<br>No hay administradores en el sistema.";
			}
			else   /////si se ha seleccionado algun curso
			{
				////se comprueba si se trabaja con todos los usuarios, o por el contrario, si se ha elegido algun curso, se trabajara solo con los usuarios de ese mismo curso
				if ($cursoID == 0)
				{
					if ($estadoID == 0)  //todos los cursos (Activos y Pasados)
					{				
						// contamos el numero historico de accesos de administrador
						$sql0="SELECT count(*) AS total FROM logs WHERE user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 )";
						$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
						$cuenta0=mysql_fetch_assoc($registros0);
						//echo "<br>Total accesos admin = ".$cuenta0['total'];	
						$total_accesos_admin = $cuenta0['total'];
						mysql_free_result($registros0);	

						// contamos el numero historico de reservas de administrador
						//$sql0="SELECT count(*) AS total FROM reservas WHERE user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 )";
						$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv IS NOT NULL AND user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 )";
						$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
						$cuenta0=mysql_fetch_assoc($registros0);
						//echo "<br>Total reservas admin = ".$cuenta0['total'];	
						$total_reservas_admin = $cuenta0['total'];
						mysql_free_result($registros0);	
						
						// contamos el numero historico de conexiones de administrador  
						//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 )";
						$sql0="SELECT count(*) AS total FROM log_detalles WHERE in_pod IS NOT NULL AND user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 )";
						$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
						$cuenta0=mysql_fetch_assoc($registros0);
						//echo "<br>Total conexiones admin = ".$cuenta0['total'];	
						$total_conexiones_admin = $cuenta0['total'];
						mysql_free_result($registros0);	
						
		
						// contamos el numero historico de intervalos de mantenimiento 
						$sql0="SELECT count(*) AS total FROM mantenimiento";
						$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
						$cuenta0=mysql_fetch_assoc($registros0);
						//echo "<br>Total intervalos de mantenimiento = ".$cuenta0['total'];	
						$total_outage = $cuenta0['total'];
						mysql_free_result($registros0);	
						
					
						// seleccionamos los user_id y username de todos los administradores
						$sql1="SELECT user_id, username FROM usuarios WHERE admin = 1";
						$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
						$cuenta1=mysql_num_rows($registros1);
						$total_admins = $cuenta1;
						//echo "<br>Num.administradores = $total_admins";
						
						if ($cuenta1 > 0)
						{
							$j = 0;
							while ($reg1 = mysql_fetch_array($registros1))
							{	
							   $administradores[$j] = $reg1['user_id'];
							   $nombre_administradores[$j] = $reg1['username'];
							   $j++;
							}
							mysql_free_result($registros1);
						
							// contamos el numero historico de accesos para cada administrador
							for ($i=0; $i<count($administradores); $i++)
							{		
								$sql2="SELECT count(*) AS total FROM logs WHERE user_id = $administradores[$i]";
								$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
								$cuenta2=mysql_fetch_assoc($registros2);
								$contador_accesos_admin[$i] = $cuenta2['total'];
								mysql_free_result($registros2);
							}
							
							// contamos el numero historico de reservas para cada administrador
							for ($i=0; $i<count($administradores); $i++)
							{		
								//$sql2="SELECT count(*) AS total FROM reservas WHERE user_id = $administradores[$i]";
								$sql2="SELECT count(*) AS total FROM log_detalles WHERE confir_resv IS NOT NULL AND user_id = $administradores[$i]";
								$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
								$cuenta2=mysql_fetch_assoc($registros2);
								$contador_reservas_admin[$i] = $cuenta2['total'];
								mysql_free_result($registros2);
							}

							// contamos el numero historico de conexiones para cada administrador
							for ($i=0; $i<count($administradores); $i++)
							{		
								//$sql2="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND user_id = $administradores[$i]";
								$sql2="SELECT count(*) AS total FROM log_detalles WHERE in_pod IS NOT NULL AND user_id = $administradores[$i]";
								$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
								$cuenta2=mysql_fetch_assoc($registros2);
								$contador_conexiones_admin[$i] = $cuenta2['total'];
								mysql_free_result($registros2);
							}
							
							// contamos el numero historico de intervalos de mantenimiento de cada administrador
							for ($i=0; $i<count($administradores); $i++)
							{		
								$sql2="SELECT count(*) AS total FROM mantenimiento WHERE admin_id = $administradores[$i]";
								$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
								$cuenta2=mysql_fetch_assoc($registros2);
								$contador_outage[$i] = $cuenta2['total'];
								mysql_free_result($registros2);
							}	
						}
						else
							echo "<br>No hay administradores en el sistema.";						
					}
					else if ($estadoID == 1)   //todos los cursos Activos
					{
						// contamos el numero historico de accesos de administrador
						$sql0="SELECT count(*) AS total FROM logs WHERE user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 ) AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 ) ) AND curso_id in (SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1) ";
						$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
						$cuenta0=mysql_fetch_assoc($registros0);
						//echo "<br>Total accesos admin = ".$cuenta0['total'];	
						$total_accesos_admin = $cuenta0['total'];
						mysql_free_result($registros0);	

						// contamos el numero historico de reservas de administrador
						//$sql0="SELECT count(*) AS total FROM reservas WHERE user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 ) AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 ) ) ";
						$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv IS NOT NULL AND user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 ) AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 ) ) AND curso_id in (SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1) ";
						$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
						$cuenta0=mysql_fetch_assoc($registros0);
						//echo "<br>Total reservas admin = ".$cuenta0['total'];	
						$total_reservas_admin = $cuenta0['total'];
						mysql_free_result($registros0);	
						
						// contamos el numero historico de conexiones de administrador  
						//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 ) AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 ) ) ";
						$sql0="SELECT count(*) AS total FROM log_detalles WHERE in_pod IS NOT NULL AND user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 ) AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 ) ) AND curso_id in (SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1) ";
						$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
						$cuenta0=mysql_fetch_assoc($registros0);
						//echo "<br>Total conexiones admin = ".$cuenta0['total'];	
						$total_conexiones_admin = $cuenta0['total'];
						mysql_free_result($registros0);	

						
						// contamos el numero historico de intervalos de mantenimiento 
						$sql0="SELECT count(*) AS total FROM mantenimiento WHERE admin_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 ) ) AND curso_id in (SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1) ";
						$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
						$cuenta0=mysql_fetch_assoc($registros0);
						//echo "<br>Total intervalos de mantenimiento = ".$cuenta0['total'];	
						$total_outage = $cuenta0['total'];
						mysql_free_result($registros0);	
						
					
						// seleccionamos los user_id y username de todos los administradores
						$sql1="SELECT user_id, username FROM usuarios WHERE admin = 1 AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 ) ) ";
						$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
						$cuenta1=mysql_num_rows($registros1);
						$total_admins = $cuenta1;
						//echo "<br>Num.administradores = $total_admins";
						
						if ($cuenta1 > 0)
						{
							$j = 0;
							while ($reg1 = mysql_fetch_array($registros1))
							{	
							   $administradores[$j] = $reg1['user_id'];
							   $nombre_administradores[$j] = $reg1['username'];
							   $j++;
							}
							mysql_free_result($registros1);
						
							// contamos el numero historico de accesos para cada administrador
							for ($i=0; $i<count($administradores); $i++)
							{		
								$sql2="SELECT count(*) AS total FROM logs WHERE user_id = $administradores[$i] AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 ) ) AND curso_id in (SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1) ";
								$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
								$cuenta2=mysql_fetch_assoc($registros2);
								$contador_accesos_admin[$i] = $cuenta2['total'];
								mysql_free_result($registros2);
							}
							
							// contamos el numero historico de reservas para cada administrador
							for ($i=0; $i<count($administradores); $i++)
							{		
								//$sql2="SELECT count(*) AS total FROM reservas WHERE user_id = $administradores[$i] AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 ) ) ";
								$sql2="SELECT count(*) AS total FROM log_detalles WHERE confir_resv IS NOT NULL AND user_id = $administradores[$i] AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 ) ) AND curso_id in (SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1) ";
								$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
								$cuenta2=mysql_fetch_assoc($registros2);
								$contador_reservas_admin[$i] = $cuenta2['total'];
								mysql_free_result($registros2);
							}

							// contamos el numero historico de conexiones para cada administrador
							for ($i=0; $i<count($administradores); $i++)
							{		
								//$sql2="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND user_id = $administradores[$i] AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 ) ) ";
								$sql2="SELECT count(*) AS total FROM log_detalles WHERE in_pod IS NOT NULL AND user_id = $administradores[$i] AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 ) ) AND curso_id in (SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1) ";
								$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
								$cuenta2=mysql_fetch_assoc($registros2);
								$contador_conexiones_admin[$i] = $cuenta2['total'];
								mysql_free_result($registros2);
							}
							
							// contamos el numero historico de intervalos de mantenimiento de cada administrador
							for ($i=0; $i<count($administradores); $i++)
							{		
								$sql2="SELECT count(*) AS total FROM mantenimiento WHERE admin_id = $administradores[$i] AND admin_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 ) ) AND curso_id in (SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1) ";
								$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
								$cuenta2=mysql_fetch_assoc($registros2);
								$contador_outage[$i] = $cuenta2['total'];
								mysql_free_result($registros2);
							}	
						}
						else
							echo "<br>No hay administradores en los cursos activos.";
					}
					else  //if ($estadoID == 2)    //todos los cursos Pasados
					{					
						// contamos el numero historico de accesos de administrador
						$sql0="SELECT count(*) AS total FROM logs WHERE user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 ) AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ) AND curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ";
						$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
						$cuenta0=mysql_fetch_assoc($registros0);
						//echo "<br>Total accesos admin = ".$cuenta0['total'];	
						$total_accesos_admin = $cuenta0['total'];
						mysql_free_result($registros0);	
						
						// contamos el numero historico de reservas de administrador
						//$sql0="SELECT count(*) AS total FROM reservas WHERE user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 ) AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ) ";
						$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv IS NOT NULL AND user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 ) AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ) AND curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ";
						$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
						$cuenta0=mysql_fetch_assoc($registros0);
						//echo "<br>Total reservas admin = ".$cuenta0['total'];	
						$total_reservas_admin = $cuenta0['total'];
						mysql_free_result($registros0);	
						
						// contamos el numero historico de conexiones de administrador  
						//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 ) AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ) ";
						$sql0="SELECT count(*) AS total FROM log_detalles WHERE in_pod IS NOT NULL AND user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 ) AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ) AND curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ";
						$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
						$cuenta0=mysql_fetch_assoc($registros0);
						//echo "<br>Total conexiones admin = ".$cuenta0['total'];	
						$total_conexiones_admin = $cuenta0['total'];
						mysql_free_result($registros0);	

						
						// contamos el numero historico de intervalos de mantenimiento 
						$sql0="SELECT count(*) AS total FROM mantenimiento WHERE admin_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ) AND curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ";
						$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
						$cuenta0=mysql_fetch_assoc($registros0);
						//echo "<br>Total intervalos de mantenimiento = ".$cuenta0['total'];	
						$total_outage = $cuenta0['total'];
						mysql_free_result($registros0);	
						
					
						// seleccionamos los user_id y username de todos los administradores
						$sql1="SELECT user_id, username FROM usuarios WHERE admin = 1 AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ) ";
						$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
						$cuenta1=mysql_num_rows($registros1);
						$total_admins = $cuenta1;
						//echo "<br>Num.administradores = $total_admins";
						
						if ($cuenta1 > 0)
						{
							$j = 0;
							while ($reg1 = mysql_fetch_array($registros1))
							{	
							   $administradores[$j] = $reg1['user_id'];
							   $nombre_administradores[$j] = $reg1['username'];
							   $j++;
							}
							mysql_free_result($registros1);
						
							// contamos el numero historico de accesos para cada administrador
							for ($i=0; $i<count($administradores); $i++)
							{		
								$sql2="SELECT count(*) AS total FROM logs WHERE user_id = $administradores[$i] AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ) AND curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ";
								$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
								$cuenta2=mysql_fetch_assoc($registros2);
								$contador_accesos_admin[$i] = $cuenta2['total'];
								mysql_free_result($registros2);
							}
							
							// contamos el numero historico de reservas para cada administrador
							for ($i=0; $i<count($administradores); $i++)
							{		
								//$sql2="SELECT count(*) AS total FROM reservas WHERE user_id = $administradores[$i] AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ) ";
								$sql2="SELECT count(*) AS total FROM log_detalles WHERE confir_resv IS NOT NULL AND user_id = $administradores[$i] AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ) AND curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ";
								$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
								$cuenta2=mysql_fetch_assoc($registros2);
								$contador_reservas_admin[$i] = $cuenta2['total'];
								mysql_free_result($registros2);
							}

							// contamos el numero historico de conexiones para cada administrador
							for ($i=0; $i<count($administradores); $i++)
							{		
								//$sql2="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND user_id = $administradores[$i] AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ) ";
								$sql2="SELECT count(*) AS total FROM log_detalles WHERE in_pod IS NOT NULL AND user_id = $administradores[$i] AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ) AND curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ";
								$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
								$cuenta2=mysql_fetch_assoc($registros2);
								$contador_conexiones_admin[$i] = $cuenta2['total'];
								mysql_free_result($registros2);
							}
							
							// contamos el numero historico de intervalos de mantenimiento de cada administrador
							for ($i=0; $i<count($administradores); $i++)
							{		
								$sql2="SELECT count(*) AS total FROM mantenimiento WHERE admin_id = $administradores[$i] AND admin_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ) AND curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ";
								$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
								$cuenta2=mysql_fetch_assoc($registros2);
								$contador_outage[$i] = $cuenta2['total'];
								mysql_free_result($registros2);
							}	
						}
						else
							echo "<br>No hab&iacute;a administradores en los cursos pasados.";
					}
				}
				else     //el curso seleccionado en particular
				{
					// contamos el numero historico de accesos de administrador
					$sql0="SELECT count(*) AS total FROM logs WHERE user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 ) AND user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE curso_id = $cursoID ) AND curso_id = $cursoID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total accesos admin = ".$cuenta0['total'];	
					$total_accesos_admin = $cuenta0['total'];
					mysql_free_result($registros0);	

					// contamos el numero historico de reservas de administrador
					//$sql0="SELECT count(*) AS total FROM reservas WHERE user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 ) AND user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE curso_id = $cursoID )";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv IS NOT NULL AND user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 ) AND user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE curso_id = $cursoID ) AND curso_id = $cursoID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total reservas admin = ".$cuenta0['total'];	
					$total_reservas_admin = $cuenta0['total'];
					mysql_free_result($registros0);	
					
					// contamos el numero historico de conexiones de administrador  
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 ) AND user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE curso_id = $cursoID )";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE in_pod IS NOT NULL AND user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 ) AND user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE curso_id = $cursoID ) AND curso_id = $cursoID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones admin = ".$cuenta0['total'];	
					$total_conexiones_admin = $cuenta0['total'];
					mysql_free_result($registros0);	
					

					///pods del cursoID en particular
					for ($j=0; $j<count($array_curso_id); $j++){
						if ($cursoID == $array_curso_id[$j]){
							//echo "<br>array_curso_id[$j] = $array_curso_id[$j]";
							$indice = $j;
							//echo "<br>indice = $indice";
						}
					}
                    
					if (isset($indice)){
						// contamos el numero historico de intervalos de mantenimiento 
						$sql0="SELECT count(*) AS total FROM mantenimiento WHERE admin_id in ( SELECT user_id FROM alumnos_en_cursos WHERE curso_id = $cursoID ) AND num_POD_outage BETWEEN $pod_inicial[$indice] AND $pod_final[$indice] AND curso_id = $cursoID";
						$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
						$cuenta0=mysql_fetch_assoc($registros0);
						//echo "<br>Total intervalos de mantenimiento = ".$cuenta0['total'];	
						$total_outage = $cuenta0['total'];
						mysql_free_result($registros0);	
					}else
						$total_outage = 0;
					
				
					// seleccionamos los user_id y username de todos los administradores
					$sql1="SELECT user_id, username FROM usuarios WHERE admin = 1 AND user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE curso_id = $cursoID )";
					$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
					$cuenta1=mysql_num_rows($registros1);
					$total_admins = $cuenta1;
					//echo "<br>Num.administradores = $total_admins";
					
					if ($cuenta1 > 0)
					{
						$j = 0;
						while ($reg1 = mysql_fetch_array($registros1))
						{	
						   $administradores[$j] = $reg1['user_id'];
						   $nombre_administradores[$j] = $reg1['username'];
						   $j++;
						}
						mysql_free_result($registros1);
					
						// contamos el numero historico de accesos para cada administrador
						for ($i=0; $i<count($administradores); $i++)
						{		
							$sql2="SELECT count(*) AS total FROM logs WHERE user_id = $administradores[$i] AND user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE curso_id = $cursoID ) AND curso_id = $cursoID";
							$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
							$cuenta2=mysql_fetch_assoc($registros2);
							$contador_accesos_admin[$i] = $cuenta2['total'];
							mysql_free_result($registros2);
						}
						
						// contamos el numero historico de reservas para cada administrador
						for ($i=0; $i<count($administradores); $i++)
						{		
							//$sql2="SELECT count(*) AS total FROM reservas WHERE user_id = $administradores[$i] AND user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE curso_id = $cursoID )";
							$sql2="SELECT count(*) AS total FROM log_detalles WHERE confir_resv IS NOT NULL AND user_id = $administradores[$i] AND user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE curso_id = $cursoID ) AND curso_id = $cursoID";
							$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
							$cuenta2=mysql_fetch_assoc($registros2);
							$contador_reservas_admin[$i] = $cuenta2['total'];
							mysql_free_result($registros2);
						}

						// contamos el numero historico de conexiones para cada administrador
						for ($i=0; $i<count($administradores); $i++)
						{		
							//$sql2="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND user_id = $administradores[$i] AND user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE curso_id = $cursoID )";
							$sql2="SELECT count(*) AS total FROM log_detalles WHERE in_pod IS NOT NULL AND user_id = $administradores[$i] AND user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE curso_id = $cursoID ) AND curso_id = $cursoID";
							$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
							$cuenta2=mysql_fetch_assoc($registros2);
							$contador_conexiones_admin[$i] = $cuenta2['total'];
							mysql_free_result($registros2);
						}
			
						// contamos el numero historico de intervalos de mantenimiento de cada administrador
						if (isset($indice)){
							for ($i=0; $i<count($administradores); $i++)
							{		
								$sql2="SELECT count(*) AS total FROM mantenimiento WHERE admin_id = $administradores[$i] AND admin_id in ( SELECT user_id FROM alumnos_en_cursos WHERE curso_id = $cursoID ) AND curso_id = $cursoID";
								$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
								$cuenta2=mysql_fetch_assoc($registros2);
								$contador_outage[$i] = $cuenta2['total'];
								mysql_free_result($registros2);
							}	
						}else
							$contador_outage[0]=0;
					}
					else
						echo "<br>No hay administradores en este curso.";					
				}
			}
		}
		else   //if ($adminsID>0) /////si se ha seleccionado algun administrador en particular, se seleccionan sus datos
		{	
		
			////comprobaciones
			//echo "<br>adminsID = $adminsID";
			//if ($cursoID == "")
			//	echo "<br>cursoID NOT SET";
			//else
			//	echo "<br>cursoID = $cursoID";			
			//echo "<br>";
			//////////////////////////
			
			if ($cursoID == "")    /////si NO se ha seleccionado algun curso
			{   
				// contamos el numero historico de accesos de administrador
				$sql0="SELECT count(*) AS total FROM logs WHERE user_id = $adminsID";
				$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
				$cuenta0=mysql_fetch_assoc($registros0);
				//echo "<br>Total accesos admin = ".$cuenta0['total'];	
				$total_accesos_admin = $cuenta0['total'];
				mysql_free_result($registros0);	
				
				// contamos el numero historico de reservas de administrador
				//$sql0="SELECT count(*) AS total FROM reservas WHERE user_id = $adminsID";
				$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv IS NOT NULL AND user_id = $adminsID";
				$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
				$cuenta0=mysql_fetch_assoc($registros0);
				//echo "<br>Total reservas admin = ".$cuenta0['total'];	
				$total_reservas_admin = $cuenta0['total'];
				mysql_free_result($registros0);	
				
				// contamos el numero historico de conexiones de administrador  
				//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND user_id = $adminsID";
				$sql0="SELECT count(*) AS total FROM log_detalles WHERE in_pod IS NOT NULL AND user_id = $adminsID";
				$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
				$cuenta0=mysql_fetch_assoc($registros0);
				//echo "<br>Total conexiones admin = ".$cuenta0['total'];	
				$total_conexiones_admin = $cuenta0['total'];
				mysql_free_result($registros0);	
				

				// contamos el numero historico de intervalos de mantenimiento 
				$sql0="SELECT count(*) AS total FROM mantenimiento WHERE admin_id = $adminsID";
				$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
				$cuenta0=mysql_fetch_assoc($registros0);
				//echo "<br>Total intervalos de mantenimiento = ".$cuenta0['total'];	
				$total_outage = $cuenta0['total'];
				mysql_free_result($registros0);	
				

				$administradores[0] = $adminsID;
				//echo "<br>administradores[0] = $administradores[0]";

				// seleccionamos los user_id y username de este administrador
				$sql1="SELECT username FROM usuarios WHERE user_id = $adminsID";
				$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
				$cuenta1=mysql_num_rows($registros1);
				
				if ($cuenta1 > 0)
					while ($reg1 = mysql_fetch_array($registros1))
						$nombre_administradores[0] = $reg1['username'];
				mysql_free_result($registros1);

				// contamos el numero historico de accesos de este administrador
				$sql2="SELECT count(*) AS total FROM logs WHERE user_id = $adminsID";
				$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
				$cuenta2=mysql_fetch_assoc($registros2);
				$contador_accesos_admin[0] = $cuenta2['total'];
				mysql_free_result($registros2);
				
				// contamos el numero historico de reservas de este administrador
				//$sql2="SELECT count(*) AS total FROM reservas WHERE user_id = $adminsID";
				$sql2="SELECT count(*) AS total FROM log_detalles WHERE confir_resv IS NOT NULL AND user_id = $adminsID";
				$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
				$cuenta2=mysql_fetch_assoc($registros2);
				$contador_reservas_admin[0] = $cuenta2['total'];
				mysql_free_result($registros2);
				
				// contamos el numero historico de conexiones de este administrador
				//$sql2="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND user_id = $adminsID";
				$sql2="SELECT count(*) AS total FROM log_detalles WHERE in_pod IS NOT NULL AND user_id = $adminsID";
				$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
				$cuenta2=mysql_fetch_assoc($registros2);
				$contador_conexiones_admin[0] = $cuenta2['total'];
				mysql_free_result($registros2);

				
				// contamos el numero historico de intervalos de mantenimiento de este administrador
				$sql2="SELECT count(*) AS total FROM mantenimiento WHERE admin_id = $adminsID";
				$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
				$cuenta2=mysql_fetch_assoc($registros2);
				$contador_outage[0] = $cuenta2['total'];
				mysql_free_result($registros2);			
			}
		/***/		
			else   /////si se ha seleccionado algun curso
			{ 
			/***/
				////se comprueba si se trabaja con todos los usuarios, o por el contrario, si se ha elegido algun curso, se trabajara solo con los usuarios de ese mismo curso
				if ($cursoID == 0)
				{   
					/***/
					if ($estadoID == 0)  //todos los cursos (Activos y Pasados)
					{   
						// contamos el numero historico de accesos de administrador
						$sql0="SELECT count(*) AS total FROM logs WHERE user_id = $adminsID";
						$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
						$cuenta0=mysql_fetch_assoc($registros0);
						//echo "<br>Total accesos admin = ".$cuenta0['total'];	
						$total_accesos_admin = $cuenta0['total'];
						mysql_free_result($registros0);	
						
						// contamos el numero historico de reservas de administrador
						//$sql0="SELECT count(*) AS total FROM reservas WHERE user_id = $adminsID";
						$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv IS NOT NULL AND user_id = $adminsID";
						$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
						$cuenta0=mysql_fetch_assoc($registros0);
						//echo "<br>Total reservas admin = ".$cuenta0['total'];	
						$total_reservas_admin = $cuenta0['total'];
						mysql_free_result($registros0);	
						
						// contamos el numero historico de conexiones de administrador  
						//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND user_id = $adminsID";
						$sql0="SELECT count(*) AS total FROM log_detalles WHERE in_pod IS NOT NULL AND user_id = $adminsID";
						$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
						$cuenta0=mysql_fetch_assoc($registros0);
						//echo "<br>Total conexiones admin = ".$cuenta0['total'];	
						$total_conexiones_admin = $cuenta0['total'];
						mysql_free_result($registros0);	
						

						// contamos el numero historico de intervalos de mantenimiento 
						$sql0="SELECT count(*) AS total FROM mantenimiento WHERE admin_id = $adminsID";
						$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
						$cuenta0=mysql_fetch_assoc($registros0);
						//echo "<br>Total intervalos de mantenimiento = ".$cuenta0['total'];	
						$total_outage = $cuenta0['total'];
						mysql_free_result($registros0);	
						

						$administradores[0] = $adminsID;
						//echo "<br>administradores[0] = $administradores[0]";

						// seleccionamos los user_id y username de este administrador
						$sql1="SELECT username FROM usuarios WHERE user_id = $adminsID";
						$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
						$cuenta1=mysql_num_rows($registros1);
						
						if ($cuenta1 > 0)
							while ($reg1 = mysql_fetch_array($registros1))
								$nombre_administradores[0] = $reg1['username'];
						mysql_free_result($registros1);

						// contamos el numero historico de accesos de este administrador
						$sql2="SELECT count(*) AS total FROM logs WHERE user_id = $adminsID";
						$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
						$cuenta2=mysql_fetch_assoc($registros2);
						$contador_accesos_admin[0] = $cuenta2['total'];
						mysql_free_result($registros2);
						
						// contamos el numero historico de reservas de este administrador
						//$sql2="SELECT count(*) AS total FROM reservas WHERE user_id = $adminsID";
						$sql2="SELECT count(*) AS total FROM log_detalles WHERE confir_resv IS NOT NULL AND user_id = $adminsID";
						$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
						$cuenta2=mysql_fetch_assoc($registros2);
						$contador_reservas_admin[0] = $cuenta2['total'];
						mysql_free_result($registros2);
						
						// contamos el numero historico de conexiones de este administrador
						//$sql2="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND user_id = $adminsID";
						$sql2="SELECT count(*) AS total FROM log_detalles WHERE in_pod IS NOT NULL AND user_id = $adminsID";
						$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
						$cuenta2=mysql_fetch_assoc($registros2);
						$contador_conexiones_admin[0] = $cuenta2['total'];
						mysql_free_result($registros2);

						
						// contamos el numero historico de intervalos de mantenimiento de este administrador
						$sql2="SELECT count(*) AS total FROM mantenimiento WHERE admin_id = $adminsID";
						$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
						$cuenta2=mysql_fetch_assoc($registros2);
						$contador_outage[0] = $cuenta2['total'];
						mysql_free_result($registros2);			
					} /***/
					else if ($estadoID == 1)   //todos los cursos Activos
					{
						// contamos el numero historico de accesos de administrador
						$sql0="SELECT count(*) AS total FROM logs WHERE user_id = $adminsID AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 ) ) AND curso_id in (SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1) ";
						$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
						$cuenta0=mysql_fetch_assoc($registros0);
						//echo "<br>Total accesos admin = ".$cuenta0['total'];	
						$total_accesos_admin = $cuenta0['total'];
						mysql_free_result($registros0);	
						
						// contamos el numero historico de reservas de administrador
						//$sql0="SELECT count(*) AS total FROM reservas WHERE user_id = $adminsID AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 ) ) ";
						$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv IS NOT NULL AND user_id = $adminsID AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 ) ) AND curso_id in (SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1) ";
						$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
						$cuenta0=mysql_fetch_assoc($registros0);
						//echo "<br>Total reservas admin = ".$cuenta0['total'];	
						$total_reservas_admin = $cuenta0['total'];
						mysql_free_result($registros0);	
						
						// contamos el numero historico de conexiones de administrador  
						//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND user_id = $adminsID AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 ) ) ";
						$sql0="SELECT count(*) AS total FROM log_detalles WHERE in_pod IS NOT NULL AND user_id = $adminsID AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 ) ) AND curso_id in (SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1) ";
						$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
						$cuenta0=mysql_fetch_assoc($registros0);
						//echo "<br>Total conexiones admin = ".$cuenta0['total'];	
						$total_conexiones_admin = $cuenta0['total'];
						mysql_free_result($registros0);	
		

						// contamos el numero historico de intervalos de mantenimiento 
						$sql0="SELECT count(*) AS total FROM mantenimiento WHERE admin_id = $adminsID AND admin_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 ) ) AND curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 ) ";
						$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
						$cuenta0=mysql_fetch_assoc($registros0);
						//echo "<br>Total intervalos de mantenimiento = ".$cuenta0['total'];	
						$total_outage = $cuenta0['total'];
						mysql_free_result($registros0);	
		
		
						$administradores[0] = $adminsID;
						//echo "<br>administradores[0] = $administradores[0]";

						// seleccionamos los user_id y username de este administrador
						$sql1="SELECT username FROM usuarios WHERE user_id = $adminsID AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 ) ) ";
						$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
						$cuenta1=mysql_num_rows($registros1);
						
						if ($cuenta1 > 0)
							while ($reg1 = mysql_fetch_array($registros1))
								$nombre_administradores[0] = $reg1['username'];
						else{
						    $sql4="SELECT username FROM usuarios WHERE user_id = $adminsID";
							$registros4=mysql_query($sql4,$conexion) or die ("Problemas con el select  (sql4) ".mysql_error());
							if ($reg4=mysql_fetch_array($registros4))
								$nombre_administradores[0] = $reg4['username'];
							mysql_free_result($registros4);
						}
						mysql_free_result($registros1);
						
						// contamos el numero historico de accesos para este administrador	
						$sql2="SELECT count(*) AS total FROM logs WHERE user_id = $adminsID AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 ) ) AND curso_id in (SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1) ";
						$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
						$cuenta2=mysql_fetch_assoc($registros2);
						$contador_accesos_admin[0] = $cuenta2['total'];
						mysql_free_result($registros2);
					
						// contamos el numero historico de reservas para este administrador
						//$sql2="SELECT count(*) AS total FROM reservas WHERE user_id = $adminsID AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 ) ) ";
						$sql2="SELECT count(*) AS total FROM log_detalles WHERE confir_resv IS NOT NULL AND user_id = $adminsID AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 ) ) AND curso_id in (SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1) ";
						$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
						$cuenta2=mysql_fetch_assoc($registros2);
						$contador_reservas_admin[0] = $cuenta2['total'];
						mysql_free_result($registros2);

						// contamos el numero historico de conexiones para cada administrador
						//$sql2="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND user_id = $adminsID AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 ) ) ";
						$sql2="SELECT count(*) AS total FROM log_detalles WHERE in_pod IS NOT NULL AND user_id = $adminsID AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 ) ) AND curso_id in (SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1) ";
						$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
						$cuenta2=mysql_fetch_assoc($registros2);
						$contador_conexiones_admin[0] = $cuenta2['total'];
						mysql_free_result($registros2);

			
						// contamos el numero historico de intervalos de mantenimiento de cada administrador
						$sql2="SELECT count(*) AS total FROM mantenimiento WHERE admin_id = $adminsID AND admin_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 ) ) AND curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso >= '$hoy' AND inicio_curso <= '$hoy' AND curso_activo = 1 ) ";
						$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
						$cuenta2=mysql_fetch_assoc($registros2);
						$contador_outage[0] = $cuenta2['total'];
						mysql_free_result($registros2);

					} /***/
					else  //if ($estadoID == 2)    //todos los cursos Pasados
					{					
						// contamos el numero historico de accesos de administrador
						$sql0="SELECT count(*) AS total FROM logs WHERE user_id = $adminsID AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ) AND curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ";
						$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
						$cuenta0=mysql_fetch_assoc($registros0);
						//echo "<br>Total accesos admin = ".$cuenta0['total'];	
						$total_accesos_admin = $cuenta0['total'];
						mysql_free_result($registros0);	
						
						// contamos el numero historico de reservas de administrador
						//$sql0="SELECT count(*) AS total FROM reservas WHERE user_id = $adminsID AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ) ";
						$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv IS NOT NULL AND user_id = $adminsID AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ) AND curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ";
						$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
						$cuenta0=mysql_fetch_assoc($registros0);
						//echo "<br>Total reservas admin = ".$cuenta0['total'];	
						$total_reservas_admin = $cuenta0['total'];
						mysql_free_result($registros0);	
						
						// contamos el numero historico de conexiones de administrador  
						//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND user_id = $adminsID AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ) ";
						$sql0="SELECT count(*) AS total FROM log_detalles WHERE in_pod IS NOT NULL AND user_id = $adminsID AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ) AND curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ";
						$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
						$cuenta0=mysql_fetch_assoc($registros0);
						//echo "<br>Total conexiones admin = ".$cuenta0['total'];	
						$total_conexiones_admin = $cuenta0['total'];
						mysql_free_result($registros0);	
		

						// contamos el numero historico de intervalos de mantenimiento 
						$sql0="SELECT count(*) AS total FROM mantenimiento WHERE admin_id = $adminsID AND admin_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ) AND curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ";
						$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
						$cuenta0=mysql_fetch_assoc($registros0);
						//echo "<br>Total intervalos de mantenimiento = ".$cuenta0['total'];	
						$total_outage = $cuenta0['total'];
						mysql_free_result($registros0);	

						
						$administradores[0] = $adminsID;
						//echo "<br>administradores[0] = $administradores[0]";
						
						// seleccionamos los user_id y username de este administrador
						$sql1="SELECT username FROM usuarios WHERE user_id = $adminsID AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ) ";
						$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
						$cuenta1=mysql_num_rows($registros1);
						
						if ($cuenta1 > 0)
							while ($reg1 = mysql_fetch_array($registros1))
								$nombre_administradores[0] = $reg1['username'];
						else{
						    $sql4="SELECT username FROM usuarios WHERE user_id = $adminsID";
							$registros4=mysql_query($sql4,$conexion) or die ("Problemas con el select  (sql4) ".mysql_error());
							if ($reg4=mysql_fetch_array($registros4))
								$nombre_administradores[0] = $reg4['username'];
							mysql_free_result($registros4);
						}
						mysql_free_result($registros1);
						
						// contamos el numero historico de accesos para este administrador	
						$sql2="SELECT count(*) AS total FROM logs WHERE user_id = $adminsID AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ) AND curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ";
						$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
						$cuenta2=mysql_fetch_assoc($registros2);
						$contador_accesos_admin[0] = $cuenta2['total'];
						mysql_free_result($registros2);						
		
						// contamos el numero historico de reservas para cada administrador
						$sql2="SELECT count(*) AS total FROM reservas WHERE user_id = $adminsID AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ) AND curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ";
						$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
						$cuenta2=mysql_fetch_assoc($registros2);
						$contador_reservas_admin[0] = $cuenta2['total'];
						mysql_free_result($registros2);

						// contamos el numero historico de conexiones para cada administrador
						$sql2="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND user_id = $adminsID AND user_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ) AND curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ";
						$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
						$cuenta2=mysql_fetch_assoc($registros2);
						$contador_conexiones_admin[0] = $cuenta2['total'];
						mysql_free_result($registros2);
			
						// contamos el numero historico de intervalos de mantenimiento de cada administrador
						$sql2="SELECT count(*) AS total FROM mantenimiento WHERE admin_id = $adminsID AND admin_id in (SELECT DISTINCT user_id FROM alumnos_en_cursos WHERE alumnos_en_cursos.curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ) AND curso_id in ( SELECT curso_id FROM cursos WHERE fin_curso < '$hoy' OR curso_activo = 0 ) ";
						$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
						$cuenta2=mysql_fetch_assoc($registros2);
						$contador_outage[0] = $cuenta2['total'];
						mysql_free_result($registros2);
					}
					/***/
				}
				
				////////////////////////////////////////////////////
				///////////////////////////////////////////////////
				else     //el curso seleccionado en particular
				{
					// contamos el numero historico de accesos de administrador
					$sql0="SELECT count(*) AS total FROM logs WHERE user_id = $adminsID AND user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE curso_id = $cursoID ) AND curso_id = $cursoID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total accesos admin = ".$cuenta0['total'];	
					$total_accesos_admin = $cuenta0['total'];
					mysql_free_result($registros0);  	//echo "<br>a1";
					
					// contamos el numero historico de reservas de administrador
					//$sql0="SELECT count(*) AS total FROM reservas WHERE user_id = $adminsID AND user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE curso_id = $cursoID )";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv IS NOT NULL AND user_id = $adminsID AND user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE curso_id = $cursoID ) AND curso_id = $cursoID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total reservas admin = ".$cuenta0['total'];	
					$total_reservas_admin = $cuenta0['total'];
					mysql_free_result($registros0);	     //echo "<br>a2";
					
					// contamos el numero historico de conexiones de administrador  
					//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND user_id = $adminsID AND user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE curso_id = $cursoID )";
					$sql0="SELECT count(*) AS total FROM log_detalles WHERE in_pod IS NOT NULL AND user_id = $adminsID AND user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE curso_id = $cursoID ) AND curso_id = $cursoID";
					$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
					$cuenta0=mysql_fetch_assoc($registros0);
					//echo "<br>Total conexiones admin = ".$cuenta0['total'];	
					$total_conexiones_admin = $cuenta0['total'];
					mysql_free_result($registros0);	       //echo "<br>a3";
		
					///pods del cursoID en particular
					for ($j=0; $j<count($array_curso_id); $j++){
						if ($cursoID == $array_curso_id[$j]){
							//echo "<br>array_curso_id[$j] = $array_curso_id[$j]";
							$indice = $j;
							//echo "<br>indice = $indice";
						}
					}
                    
					if (isset($indice)){
						// contamos el numero historico de intervalos de mantenimiento 
						$sql0="SELECT count(*) AS total FROM mantenimiento WHERE admin_id = $adminsID AND admin_id in ( SELECT user_id FROM alumnos_en_cursos WHERE curso_id = $cursoID ) AND num_POD_outage BETWEEN $pod_inicial[$indice] AND $pod_final[$indice] AND curso_id = $cursoID ";
						$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
						$cuenta0=mysql_fetch_assoc($registros0);
						//echo "<br>Total intervalos de mantenimiento = ".$cuenta0['total'];	
						$total_outage = $cuenta0['total'];
						mysql_free_result($registros0);	      //echo "<br>a4";
					}else
						$total_outage = 0;
					
					
					$administradores[0] = $adminsID;
					//echo "<br>administradores[0] = $administradores[0]";

					// seleccionamos los user_id y username de este administrador
					$sql1="SELECT username FROM usuarios WHERE user_id = $adminsID AND user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE curso_id = $cursoID )";
					$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
					$cuenta1=mysql_num_rows($registros1);
					
					if ($cuenta1 > 0){
						while ($reg1 = mysql_fetch_array($registros1)){
							$nombre_administradores[0] = $reg1['username'];
						}					
					}else{
						$sql4="SELECT username FROM usuarios WHERE user_id = $adminsID";
						$registros4=mysql_query($sql4,$conexion) or die ("Problemas con el select  (sql4) ".mysql_error());
						if ($reg4=mysql_fetch_array($registros4))
							$nombre_administradores[0] = $reg4['username'];
						mysql_free_result($registros4);
					}
					mysql_free_result($registros1);
						
					// contamos el numero historico de accesos de este administrador					
					$sql2="SELECT count(*) AS total FROM logs WHERE user_id = $adminsID AND user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE curso_id = $cursoID ) AND curso_id = $cursoID";
					$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
					$cuenta2=mysql_fetch_assoc($registros2);
					$contador_accesos_admin[0] = $cuenta2['total'];
					mysql_free_result($registros2);

					// contamos el numero historico de reservas para cada administrador
					//$sql2="SELECT count(*) AS total FROM reservas WHERE user_id = $adminsID AND user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE curso_id = $cursoID )";
					$sql2="SELECT count(*) AS total FROM log_detalles WHERE confir_resv IS NOT NULL AND user_id = $adminsID AND user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE curso_id = $cursoID ) AND curso_id = $cursoID";
					$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
					$cuenta2=mysql_fetch_assoc($registros2);
					$contador_reservas_admin[0] = $cuenta2['total'];
					mysql_free_result($registros2);

					// contamos el numero historico de conexiones para cada administrador
					//$sql2="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND user_id = $adminsID AND user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE curso_id = $cursoID )";
					$sql2="SELECT count(*) AS total FROM log_detalles WHERE in_pod IS NOT NULL AND user_id = $adminsID AND user_id in ( SELECT user_id FROM alumnos_en_cursos WHERE curso_id = $cursoID ) AND curso_id = $cursoID";
					$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
					$cuenta2=mysql_fetch_assoc($registros2);
					$contador_conexiones_admin[0] = $cuenta2['total'];
					mysql_free_result($registros2);
			
					if (isset($indice)){
						// contamos el numero historico de intervalos de mantenimiento de cada administrador
						$sql2="SELECT count(*) AS total FROM mantenimiento WHERE admin_id = $adminsID AND admin_id in ( SELECT user_id FROM alumnos_en_cursos WHERE curso_id = $cursoID ) AND curso_id = $cursoID AND (num_POD_outage BETWEEN $pod_inicial[$indice] AND $pod_final[$indice]) ";
						$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
						$cuenta2=mysql_fetch_assoc($registros2);
						$contador_outage[0] = $cuenta2['total'];
						mysql_free_result($registros2);
					}else
						$contador_outage[0] = 0;
				}
			/***/
			}
			
		/***/	
		}
		//////////////////////////////////////////////////////
		//////////////////////////////////////////////////////
?>

		<input type="text" name="estadisticasAdministradoress" value=" Estad&iacute;sticas por Administradores" 
 		id="estadisticasAdministradoress" style="width: 300px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:12pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  

        <center>
		   <table border="1" cellpadding="10" cellspacing="0" width="55%" class="thin sortable draggable">
				<tr>
					<th width="15%"></th>
					<th bgcolor="khaki" width="20%"> <?php echo "N&ordm; de accesos de Administrador"; ?>  </th>
					<th bgcolor="khaki" width="20%"> <?php echo "N&ordm; de reservas de Administrador"; ?>  </th>
					<th bgcolor="khaki" width="20%"> <?php echo "N&ordm; de conexiones de Administrador"; ?>  </th>
					<th bgcolor="khaki" width="20%"> <?php echo "N&ordm; intervalos de mantenimiento"; ?>  </th>
				</tr>
	
<?php        	for ($i=0; $i<count($administradores); $i++){    ?>

					<tr>					
						<th bgcolor="aquamarine" width="15%" align="center"> <?php echo "$nombre_administradores[$i]"; ?>  </th>
						<td width="20%" bgcolor="#fcfcfc" align="right"> <?php echo "$contador_accesos_admin[$i]"; ?>  </td>
						<td width="20%" bgcolor="#fcfcfc" align="right"> <?php echo "$contador_reservas_admin[$i]"; ?>  </td>
						<td width="20%" bgcolor="#fcfcfc" align="right"> <?php echo "$contador_conexiones_admin[$i]"; ?>  </td>						
						<td width="20%" bgcolor="#fcfcfc" align="right"> <?php echo "$contador_outage[$i]"; ?>  </td>						
					</tr>		
					
<?php			}  ?>	

				</tr>
					<th width="15%" bgcolor="#eeeeee">  TOTAL: <?php $suma=count($administradores); echo"&nbsp;$suma admins." ?> </th>		
					<td width="20%" bgcolor="#eeeeee" align="right">  <?php echo " $total_accesos_admin"; ?>  </td>
					<td width="20%" bgcolor="#eeeeee" align="right">  <?php echo " $total_reservas_admin"; ?>  </td>
					<td width="20%" bgcolor="#eeeeee" align="right">  <?php echo " $total_conexiones_admin"; ?>  </td>
					<td width="20%" bgcolor="#eeeeee" align="right">  <?php echo " $total_outage"; ?>  </td>
				</tr>	

			</table>
		</center>
		<br>
		
<?php		
	}
	
	
	
	echo "<hr>";
	
	
	
	
	//****reservas PENDIENTES****//
	if ($stat_reservas == 1)
	{
		///nombres de los cursos implicados
		$sql="SELECT * FROM cursos";
		$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
		$count = mysql_num_rows($registros);
		$j = 0;
		if ($count > 0)
		{		
			while ($reg = mysql_fetch_array($registros))
			{
			   $elenco_id_cursos[$j] = $reg['curso_id'];
			   $elenco_cursos[$j] = $reg['nombre_curso'];
			   $j++;
			}		  
		}
		mysql_free_result($registros);

		///nombre de los usuarios de esos cursos implicados
		$sql="SELECT * FROM usuarios";
		$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
		$count = mysql_num_rows($registros);
		$j = 0;
		if ($count > 0)
		{		
			while ($reg = mysql_fetch_array($registros))
			{
			   $elenco_id_usuarios[$j] = $reg['user_id'];
			   $elenco_usuarios[$j] = $reg['username'];
			   $j++;
			}		  
		}
		mysql_free_result($registros);

		////fecha y hora del turno actual
		$hora_actual = date('H');
		$hora_par = (floor($hora_actual/2)*2);
		//echo "<br>hora_par = $hora_par";
		if ($hora_par < 10)
			$inicio_turno = "0".$hora_par.":00:00";
		else
			$inicio_turno = $hora_par.":00:00";
		//echo "<br>inicio_turno = $inicio_turno";
		$inicio_turno_actual = date('Y-m-d H:i:s',strtotime('+ '.$hora_par.' hours', strtotime($hoy)));

		
	    if ($usuarioID=="" && $estadoID=="")      //o sea, NO tenemos seleccionado ningun curso ni ningun alumno, de modo que las estadisticas son generales del sistema
		{
		    if ($ultimasreservasID == 0)     //reservas recientes y pendientes y en uso
			{
				// seleccionamos las fechas de las proximas reservas
				//$sql="SELECT * FROM reservas WHERE (estado_reserva = 0 OR estado_reserva = 2) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
				$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND NOT (fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno_actual') ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
				$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
				$count1 = mysql_num_rows($registros);
				$j = 0;  
				if ($count1 > 0){
				  while ($reg = mysql_fetch_array($registros)){			  
					   $fecha1[$j] = $reg['fecha_reserva'];
					   $hora1[$j] = $reg['horario_reserva'];
					   $user_id1[$j] = $reg['user_id'];
					   $curso_id1[$j] = $reg['curso_id'];
					   $numero_pod1[$j] = $reg['num_POD'];
					   $j++;
				  }
				}  
				mysql_free_result($registros);
				
				//echo "<br>reservas PENDIENTES";
				//if ($count1 > 0)
				//{
				//	for ($i=0; $i<count($fecha1); $i++)
				//	{
				//		$j = $i+1;
				//		echo "<br>$j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].";
				//	}
				//}else
				//	echo "<br>No hay reservas pendientes en el sistema.";
				//echo "<br>";	

				
				// seleccionamos las fechas de las ultimas reservas ejecutadas
				$sql="SELECT * FROM reservas WHERE estado_reserva = 1 ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_ejecutadas";
				$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
				$count2 = mysql_num_rows($registros);
				$j = 0;
				if ($count2 > 0){
				  while ($reg = mysql_fetch_array($registros)){
					   $fecha2[$j] = $reg['fecha_reserva'];
					   $hora2[$j] = $reg['horario_reserva'];
					   $user_id2[$j] = $reg['user_id'];
					   $curso_id2[$j] = $reg['curso_id'];
					   $numero_pod2[$j] = $reg['num_POD'];
					   $j++;
				  }
				}  
				mysql_free_result($registros);
				
				//echo "<br>reservas APROVECHADAS";
				//if ($count2 > 0)
				//{
				//	for ($i=0; $i<count($fecha2); $i++)
				//	{
				//		$j = $i+1;
				//		echo "<br>$j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].";
				//	}
				//}else
				//	echo "<br>No hay reservas ejecutadas en el sistema.";
				//echo "<br>";
				
				
				// seleccionamos las fechas de las ultimas reservas canceladas
				$sql="SELECT * FROM reservas WHERE estado_reserva = -1 ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_canceladas";
				$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
				$count3 = mysql_num_rows($registros);
				$j = 0;
				if ($count3 > 0){
				  while ($reg = mysql_fetch_array($registros)){
					   $fecha3[$j] = $reg['fecha_reserva'];
					   $hora3[$j] = $reg['horario_reserva'];
					   $user_id3[$j] = $reg['user_id'];
					   $curso_id3[$j] = $reg['curso_id'];
					   $numero_pod3[$j] = $reg['num_POD'];
					   $j++;
				  }
				}  
				mysql_free_result($registros);
				
				//echo "<br>reservas CANCELADAS";
				//if ($count3 > 0)
				//{
				//	for ($i=0; $i<count($fecha3); $i++)
				//	{
				//		$j = $i+1;
				//		echo "<br>$j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].";
				//	}
				//}else
				//	echo "<br>No hay reservas canceladas en el sistema.";
				//echo "<br>";
				
				
				// seleccionamos las reservas que se estan ejecutando ahora mismo
				$sql="SELECT * FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno' ORDER BY num_POD ASC";
				$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
				$count4 = mysql_num_rows($registros);
				$j = 0;  
				if ($count4 > 0){
				  while ($reg = mysql_fetch_array($registros)){
					   $fecha4[$j] = $reg['fecha_reserva'];
					   $hora4[$j] = $reg['horario_reserva'];
					   $user_id4[$j] = $reg['user_id'];
					   $curso_id4[$j] = $reg['curso_id'];
					   $numero_pod4[$j] = $reg['num_POD'];
					   $estadoreserva4[$j] = $reg['estado_reserva'];
					   $j++;
				  }
				}  
				mysql_free_result($registros);
				
				//echo "<br>reservas EN EJECUCION ahora";
				//if ($count4 > 0)
				//{
				//	for ($i=0; $i<count($fecha4); $i++)
				//	{
				//		$j = $i+1;
				//		echo "<br>$j.- POD #$numero_pod4[$i] para el usuario $user_id4[$i] con el curso $curso_id4[$i].";
				//		echo "  ($fecha4[$i] a las $hora4[$i]) ";
				//		if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; 
				//	}
				//}else
				//	echo "<br>No hay reservas en ejecucion en el sistema.";
				//echo "<br>";	
				
			}
			else if ($ultimasreservasID == 1)     //solo las recientes
			{
				// seleccionamos las fechas de las ultimas reservas ejecutadas
				$sql="SELECT * FROM reservas WHERE estado_reserva = 1 ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_ejecutadas";
				$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
				$count2 = mysql_num_rows($registros);
				$j = 0;
				if ($count2 > 0){
				  while ($reg = mysql_fetch_array($registros)){
					   $fecha2[$j] = $reg['fecha_reserva'];
					   $hora2[$j] = $reg['horario_reserva'];
					   $user_id2[$j] = $reg['user_id'];
					   $curso_id2[$j] = $reg['curso_id'];
					   $numero_pod2[$j] = $reg['num_POD'];
					   $estadoreserva2[$j] = $reg['estado_reserva'];
					   $j++;
				  }
				}  
				mysql_free_result($registros);
				
				//echo "<br>reservas APROVECHADAS";
				//if ($count2 > 0)
				//{
				//	for ($i=0; $i<count($fecha2); $i++)
				//	{
				//		$j = $i+1;
				//		echo "<br>$j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].";
				//	}
				//}else
				//	echo "<br>No hay reservas ejecutadas en el sistema.";
				//echo "<br>";
				
				
				// seleccionamos las fechas de las ultimas reservas canceladas
				$sql="SELECT * FROM reservas WHERE estado_reserva = -1 ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_canceladas";
				$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
				$count3 = mysql_num_rows($registros);
				$j = 0;
				if ($count3 > 0){
				  while ($reg = mysql_fetch_array($registros)){
					   $fecha3[$j] = $reg['fecha_reserva'];
					   $hora3[$j] = $reg['horario_reserva'];
					   $user_id3[$j] = $reg['user_id'];
					   $curso_id3[$j] = $reg['curso_id'];
					   $numero_pod3[$j] = $reg['num_POD'];
					   $estadoreserva3[$j] = $reg['estado_reserva'];
					   $j++;
				  }
				}  
				mysql_free_result($registros);
				
				//echo "<br>reservas CANCELADAS";
				//if ($count3 > 0)
				//{
				//	for ($i=0; $i<count($fecha3); $i++)
				//	{
				//		$j = $i+1;
				//		echo "<br>$j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].";
				//	}
				//}else
				//	echo "<br>No hay reservas canceladas en el sistema.";
				//echo "<br>";			

			}
			else  if ($ultimasreservasID == 2)   // if ($ultimasreservasID == 2)    //solo las pendientes
			{
				// seleccionamos las fechas de las proximas reservas
				//$sql="SELECT * FROM reservas WHERE (estado_reserva = 0 OR estado_reserva = 2) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
				$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND NOT (fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno_actual') ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
				$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
				$count1 = mysql_num_rows($registros);
				$j = 0;
				if ($count1 > 0){
				  while ($reg = mysql_fetch_array($registros)){
					   $fecha1[$j] = $reg['fecha_reserva'];
					   $hora1[$j] = $reg['horario_reserva'];
					   $user_id1[$j] = $reg['user_id'];
					   $curso_id1[$j] = $reg['curso_id'];
					   $numero_pod1[$j] = $reg['num_POD'];
					   $j++;
				  }
				}  
				mysql_free_result($registros);
				
				//echo "<br>reservas PENDIENTES";
				//if ($count1 > 0)
				//{
				//	for ($i=0; $i<count($fecha1); $i++)
				//	{
				//		$j = $i+1;
				//		echo "<br>$j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].";
				//	}
				//}else
				//	echo "<br>No hay reservas pendientes en el sistema.";
				//echo "<br>";		

			}
			else   // if ($ultimasreservasID == 3)    //solo las del turno actual
			{
				// seleccionamos las reservas que se estan ejecutando ahora mismo
				$sql="SELECT * FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno' ORDER BY num_POD ASC";
				$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
				$count4 = mysql_num_rows($registros);
				$j = 0;  
				if ($count4 > 0){
				  while ($reg = mysql_fetch_array($registros)){
					   $fecha4[$j] = $reg['fecha_reserva'];
					   $hora4[$j] = $reg['horario_reserva'];
					   $user_id4[$j] = $reg['user_id'];
					   $curso_id4[$j] = $reg['curso_id'];
					   $numero_pod4[$j] = $reg['num_POD'];
					   $estadoreserva4[$j] = $reg['estado_reserva'];
					   $j++;
				  }
				}  
				mysql_free_result($registros);
				
				//echo "<br>reservas EN EJECUCION ahora";
				//if ($count4 > 0)
				//{
				//	for ($i=0; $i<count($fecha4); $i++)
				//	{
				//		$j = $i+1;
				//		echo "<br>$j.- POD #$numero_pod4[$i] para el usuario $user_id4[$i] con el curso $curso_id4[$i].";
				//		echo "  ($fecha4[$i] a las $hora4[$i]) ";
				//		if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; 
				//	}
				//}else
				//	echo "<br>No hay reservas en ejecucion en el sistema.";
				//echo "<br>";					
			}
		}

		else if ($usuarioID=="")   //implicitamente $cursoID!="", o sea, tenemos seleccionado un curso
		{

			if ($cursoID == 0)   //todos los cursos
			{
				if ($estadoID == 0)    //todos los cursos: activos y pasados
				{
					//pasamos los codigos de curso almacenados en el array $cursos a la cadena $cursos_str
					$cursos_str = implode(",", $cursos); 
					
					//echo "<br>cursos_str = $cursos_str";
					
					
					if ($ultimasreservasID == 0)     //reservas recientes y pendientes
					{
						// seleccionamos las fechas de las proximas reservas
						//$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND curso_id IN ($cursos_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas ";
						$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND NOT (fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno_actual') AND curso_id IN ($cursos_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count1 = mysql_num_rows($registros);
						$j = 0;
						if ($count1 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha1[$j] = $reg['fecha_reserva'];
							   $hora1[$j] = $reg['horario_reserva'];
							   $user_id1[$j] = $reg['user_id'];
							   $curso_id1[$j] = $reg['curso_id'];
							   $numero_pod1[$j] = $reg['num_POD'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas PENDIENTES";
						//if ($count1 > 0)
						//{
						//	for ($i=0; $i<count($fecha1); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].";
						//	}
						//}else
						//{
						//	if ($estadoID == 2)   //en un curso pasado no puede haber reservas futuras
						//	{
						//		if ($cursoID == 0)
						//			echo "<br>cursos Pasados";
						//		else  //($cursoID > 0)
						//			echo "<br>Curso Pasado";
						//		
						//		echo " - No puede haber reservas activas en el sistema";
						//	}
						//	else
						//		echo "<br>No hay reservas pendientes en el sistema.";
						//}
						//echo "<br>";	

						
						// seleccionamos las fechas de las ultimas reservas ejecutadas
						$sql="SELECT * FROM reservas WHERE estado_reserva = 1 AND curso_id IN ($cursos_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_ejecutadas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count2 = mysql_num_rows($registros);
						$j = 0;
						if ($count2 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha2[$j] = $reg['fecha_reserva'];
							   $hora2[$j] = $reg['horario_reserva'];
							   $user_id2[$j] = $reg['user_id'];
							   $curso_id2[$j] = $reg['curso_id'];
							   $numero_pod2[$j] = $reg['num_POD'];
							   $estadoreserva2[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas APROVECHADAS";
						//if ($count2 > 0)
						//{
						//	for ($i=0; $i<count($fecha2); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas ejecutadas en el sistema.";
						//echo "<br>";
						
						
						// seleccionamos las fechas de las ultimas reservas canceladas
						$sql="SELECT * FROM reservas WHERE estado_reserva = -1 AND curso_id IN ($cursos_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_canceladas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count3 = mysql_num_rows($registros);
						$j = 0;
						if ($count3 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha3[$j] = $reg['fecha_reserva'];
							   $hora3[$j] = $reg['horario_reserva'];
							   $user_id3[$j] = $reg['user_id'];
							   $curso_id3[$j] = $reg['curso_id'];
							   $numero_pod3[$j] = $reg['num_POD'];
							   $estadoreserva3[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas CANCELADAS";
						//if ($count3 > 0)
						//{
						//	for ($i=0; $i<count($fecha3); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas canceladas en el sistema.";
						//echo "<br>";
						

						
						// seleccionamos las reservas que se estan ejecutando ahora mismo
						$sql="SELECT * FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno' AND curso_id IN ($cursos_str) ORDER BY num_POD ASC";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count4 = mysql_num_rows($registros);
						$j = 0;  
						if ($count4 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha4[$j] = $reg['fecha_reserva'];
							   $hora4[$j] = $reg['horario_reserva'];
							   $user_id4[$j] = $reg['user_id'];
							   $curso_id4[$j] = $reg['curso_id'];
							   $numero_pod4[$j] = $reg['num_POD'];
							   $estadoreserva4[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas EN EJECUCION ahora";
						//if ($count4 > 0)
						//{
						//	for ($i=0; $i<count($fecha4); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- POD #$numero_pod4[$i] para el usuario $user_id4[$i] con el curso $curso_id4[$i].";
						//		echo "  ($fecha4[$i] a las $hora4[$i]) ";
						//		if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; 
						//	}
						//}else
						//	echo "<br>No hay reservas en ejecucion en el sistema.";
						//echo "<br>";	
					}
					else if ($ultimasreservasID == 1)     //solo las recientes
					{
						// seleccionamos las fechas de las ultimas reservas ejecutadas
						$sql="SELECT * FROM reservas WHERE estado_reserva = 1 AND curso_id IN ($cursos_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_ejecutadas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count2 = mysql_num_rows($registros);
						$j = 0;
						if ($count2 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha2[$j] = $reg['fecha_reserva'];
							   $hora2[$j] = $reg['horario_reserva'];
							   $user_id2[$j] = $reg['user_id'];
							   $curso_id2[$j] = $reg['curso_id'];
							   $numero_pod2[$j] = $reg['num_POD'];
							   $estadoreserva2[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas APROVECHADAS";
						//if ($count2 > 0)
						//{
						//	for ($i=0; $i<count($fecha2); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas ejecutadas en el sistema.";
						//echo "<br>";
						
						
						// seleccionamos las fechas de las ultimas reservas canceladas
						$sql="SELECT * FROM reservas WHERE estado_reserva = -1 AND curso_id IN ($cursos_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_canceladas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count3 = mysql_num_rows($registros);
						$j = 0;
						if ($count3 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha3[$j] = $reg['fecha_reserva'];
							   $hora3[$j] = $reg['horario_reserva'];
							   $user_id3[$j] = $reg['user_id'];
							   $curso_id3[$j] = $reg['curso_id'];
							   $numero_pod3[$j] = $reg['num_POD'];
							   $estadoreserva3[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas CANCELADAS";
						//if ($count3 > 0)
						//{
						//	for ($i=0; $i<count($fecha3); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas canceladas en el sistema.";
						//echo "<br>";			

					}
					else  if ($ultimasreservasID == 2)   // if ($ultimasreservasID == 2)    //solo las pendientes
					{
						// seleccionamos las fechas de las proximas reservas
						//$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND curso_id IN ($cursos_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND NOT (fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno_actual') AND curso_id IN ($cursos_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count1 = mysql_num_rows($registros);
						$j = 0;
						if ($count1 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha1[$j] = $reg['fecha_reserva'];
							   $hora1[$j] = $reg['horario_reserva'];
							   $user_id1[$j] = $reg['user_id'];
							   $curso_id1[$j] = $reg['curso_id'];
							   $numero_pod1[$j] = $reg['num_POD'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas PENDIENTES";
						//if ($count1 > 0)
						//{
						//	for ($i=0; $i<count($fecha1); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].";
						//	}
						//}
						//else
						//{
						//	if ($estadoID == 2)   //en un curso pasado no puede haber reservas futuras
						//	{
						//		if ($cursoID == 0)
						//			echo "<br>cursos Pasados";
						//		else  //($cursoID > 0)
						//			echo "<br>Curso Pasado";
						//		
						//		echo " - No puede haber reservas activas en el sistema";
						//	}
						//	else
						//		echo "<br>No hay reservas pendientes en el sistema.";
						//}
						//echo "<br>";	

					}
					else   // if ($ultimasreservasID == 3)    //solo las del turno actual
					{					
						// seleccionamos las reservas que se estan ejecutando ahora mismo
						$sql="SELECT * FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno' AND curso_id IN ($cursos_str) ORDER BY num_POD ASC";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count4 = mysql_num_rows($registros);
						$j = 0;  
						if ($count4 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha4[$j] = $reg['fecha_reserva'];
							   $hora4[$j] = $reg['horario_reserva'];
							   $user_id4[$j] = $reg['user_id'];
							   $curso_id4[$j] = $reg['curso_id'];
							   $numero_pod4[$j] = $reg['num_POD'];
							   $estadoreserva4[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas EN EJECUCION ahora";
						//if ($count4 > 0)
						//{
						//	for ($i=0; $i<count($fecha4); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- POD #$numero_pod4[$i] para el usuario $user_id4[$i] con el curso $curso_id4[$i].";
						//		echo "  ($fecha4[$i] a las $hora4[$i]) ";
						//		if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; 
						//	}
						//}else
						//	echo "<br>No hay reservas en ejecucion en el sistema.";
						//echo "<br>";	

					}
				}
				else if ($estadoID == 1)    ///solo los cursos activos
				{
					//pasamos los codigos de curso almacenados en el array $cursos a la cadena $cursos_str
					$cursos_str = implode(",", $cursos); 
					
					//echo "<br>cursos_str = $cursos_str";
					
					
					if ($ultimasreservasID == 0)     //reservas recientes y pendientes
					{
						// seleccionamos las fechas de las proximas reservas
						//$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND curso_id IN ($cursos_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas ";
						$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND NOT (fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno_actual') AND curso_id IN ($cursos_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count1 = mysql_num_rows($registros);
						$j = 0;
						if ($count1 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha1[$j] = $reg['fecha_reserva'];
							   $hora1[$j] = $reg['horario_reserva'];
							   $user_id1[$j] = $reg['user_id'];
							   $curso_id1[$j] = $reg['curso_id'];
							   $numero_pod1[$j] = $reg['num_POD'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas PENDIENTES";
						//if ($count1 > 0)
						//{
						//	for ($i=0; $i<count($fecha1); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].";
						//	}
						//}else
						//{
						//	if ($estadoID == 2)   //en un curso pasado no puede haber reservas futuras
						//	{
						//		if ($cursoID == 0)
						//			echo "<br>cursos Pasados";
						//		else  //($cursoID > 0)
						//			echo "<br>Curso Pasado";
						//		
						//		echo " - No puede haber reservas activas en el sistema";
						//	}
						//	else
						//		echo "<br>No hay reservas pendientes en el sistema.";
						//}
						//echo "<br>";	

						
						// seleccionamos las fechas de las ultimas reservas ejecutadas
						$sql="SELECT * FROM reservas WHERE estado_reserva = 1 AND curso_id IN ($cursos_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_ejecutadas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count2 = mysql_num_rows($registros);
						$j = 0;
						if ($count2 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha2[$j] = $reg['fecha_reserva'];
							   $hora2[$j] = $reg['horario_reserva'];
							   $user_id2[$j] = $reg['user_id'];
							   $curso_id2[$j] = $reg['curso_id'];
							   $numero_pod2[$j] = $reg['num_POD'];
							   $estadoreserva2[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas APROVECHADAS";
						//if ($count2 > 0)
						//{
						//	for ($i=0; $i<count($fecha2); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas ejecutadas en el sistema.";
						//echo "<br>";
						
						
						// seleccionamos las fechas de las ultimas reservas canceladas
						$sql="SELECT * FROM reservas WHERE estado_reserva = -1 AND curso_id IN ($cursos_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_canceladas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count3 = mysql_num_rows($registros);
						$j = 0;
						if ($count3 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha3[$j] = $reg['fecha_reserva'];
							   $hora3[$j] = $reg['horario_reserva'];
							   $user_id3[$j] = $reg['user_id'];
							   $curso_id3[$j] = $reg['curso_id'];
							   $numero_pod3[$j] = $reg['num_POD'];
							   $estadoreserva3[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas CANCELADAS";
						//if ($count3 > 0)
						//{
						//	for ($i=0; $i<count($fecha3); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas canceladas en el sistema.";
						//echo "<br>";
						

						
						// seleccionamos las reservas que se estan ejecutando ahora mismo
						$sql="SELECT * FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno' AND curso_id IN ($cursos_str) ORDER BY num_POD ASC";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count4 = mysql_num_rows($registros);
						$j = 0;  
						if ($count4 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha4[$j] = $reg['fecha_reserva'];
							   $hora4[$j] = $reg['horario_reserva'];
							   $user_id4[$j] = $reg['user_id'];
							   $curso_id4[$j] = $reg['curso_id'];
							   $numero_pod4[$j] = $reg['num_POD'];
							   $estadoreserva4[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas EN EJECUCION ahora";
						//if ($count4 > 0)
						//{
						//	for ($i=0; $i<count($fecha4); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- POD #$numero_pod4[$i] para el usuario $user_id4[$i] con el curso $curso_id4[$i].";
						//		echo "  ($fecha4[$i] a las $hora4[$i]) ";
						//		if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; 
						//	}
						//}else
						//	echo "<br>No hay reservas en ejecucion en el sistema.";
						//echo "<br>";	
					}
					else if ($ultimasreservasID == 1)     //solo las recientes
					{
						// seleccionamos las fechas de las ultimas reservas ejecutadas
						$sql="SELECT * FROM reservas WHERE estado_reserva = 1 AND curso_id IN ($cursos_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_ejecutadas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count2 = mysql_num_rows($registros);
						$j = 0;
						if ($count2 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha2[$j] = $reg['fecha_reserva'];
							   $hora2[$j] = $reg['horario_reserva'];
							   $user_id2[$j] = $reg['user_id'];
							   $curso_id2[$j] = $reg['curso_id'];
							   $numero_pod2[$j] = $reg['num_POD'];
							   $estadoreserva2[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas APROVECHADAS";
						//if ($count2 > 0)
						//{
						//	for ($i=0; $i<count($fecha2); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas ejecutadas en el sistema.";
						//echo "<br>";
						
						
						// seleccionamos las fechas de las ultimas reservas canceladas
						$sql="SELECT * FROM reservas WHERE estado_reserva = -1 AND curso_id IN ($cursos_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_canceladas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count3 = mysql_num_rows($registros);
						$j = 0;
						if ($count3 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha3[$j] = $reg['fecha_reserva'];
							   $hora3[$j] = $reg['horario_reserva'];
							   $user_id3[$j] = $reg['user_id'];
							   $curso_id3[$j] = $reg['curso_id'];
							   $numero_pod3[$j] = $reg['num_POD'];
							   $estadoreserva3[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas CANCELADAS";
						//if ($count3 > 0)
						//{
						//	for ($i=0; $i<count($fecha3); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas canceladas en el sistema.";
						//echo "<br>";			

					}
					else  if ($ultimasreservasID == 2)   // if ($ultimasreservasID == 2)    //solo las pendientes
					{
						// seleccionamos las fechas de las proximas reservas
						//$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND curso_id IN ($cursos_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND NOT (fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno_actual') AND curso_id IN ($cursos_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count1 = mysql_num_rows($registros);
						$j = 0;
						if ($count1 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha1[$j] = $reg['fecha_reserva'];
							   $hora1[$j] = $reg['horario_reserva'];
							   $user_id1[$j] = $reg['user_id'];
							   $curso_id1[$j] = $reg['curso_id'];
							   $numero_pod1[$j] = $reg['num_POD'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas PENDIENTES";
						//if ($count1 > 0)
						//{
						//	for ($i=0; $i<count($fecha1); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].";
						//	}
						//}
						//else
						//{
						//	if ($estadoID == 2)   //en un curso pasado no puede haber reservas futuras
						//	{
						//		if ($cursoID == 0)
						//			echo "<br>cursos Pasados";
						//		else  //($cursoID > 0)
						//			echo "<br>Curso Pasado";
						//		
						//		echo " - No puede haber reservas activas en el sistema";
						//	}
						//	else
						//		echo "<br>No hay reservas pendientes en el sistema.";
						//}
						//echo "<br>";	

					}
					else   // if ($ultimasreservasID == 3)    //solo las del turno actual
					{					
						// seleccionamos las reservas que se estan ejecutando ahora mismo
						$sql="SELECT * FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno' AND curso_id IN ($cursos_str) ORDER BY num_POD ASC";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count4 = mysql_num_rows($registros);
						$j = 0;  
						if ($count4 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha4[$j] = $reg['fecha_reserva'];
							   $hora4[$j] = $reg['horario_reserva'];
							   $user_id4[$j] = $reg['user_id'];
							   $curso_id4[$j] = $reg['curso_id'];
							   $numero_pod4[$j] = $reg['num_POD'];
							   $estadoreserva4[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas EN EJECUCION ahora";
						//if ($count4 > 0)
						//{
						//	for ($i=0; $i<count($fecha4); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- POD #$numero_pod4[$i] para el usuario $user_id4[$i] con el curso $curso_id4[$i].";
						//		echo "  ($fecha4[$i] a las $hora4[$i]) ";
						//		if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; 
						//	}
						//}else
						//	echo "<br>No hay reservas en ejecucion en el sistema.";
						//echo "<br>";	

					}
				}
				else
				{		///if ($estadoID == 2){    //solo los cursos pasados
				
					//pasamos los codigos de curso almacenados en el array $cursos a la cadena $cursos_str
					$cursos_str = implode(",", $cursos); 
					
					//echo "<br>cursos_str = $cursos_str";
					
					
					if ($ultimasreservasID == 0)     //reservas recientes y pendientes
					{
						// seleccionamos las fechas de las proximas reservas
						//$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND curso_id IN ($cursos_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas ";
						$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND NOT (fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno_actual') AND curso_id IN ($cursos_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count1 = mysql_num_rows($registros);
						$j = 0;
						if ($count1 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha1[$j] = $reg['fecha_reserva'];
							   $hora1[$j] = $reg['horario_reserva'];
							   $user_id1[$j] = $reg['user_id'];
							   $curso_id1[$j] = $reg['curso_id'];
							   $numero_pod1[$j] = $reg['num_POD'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas PENDIENTES";
						//if ($count1 > 0)
						//{
						//	for ($i=0; $i<count($fecha1); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].";
						//	}
						//}else
						//{
						//	if ($estadoID == 2)   //en un curso pasado no puede haber reservas futuras
						//	{
						//		if ($cursoID == 0)
						//			echo "<br>cursos Pasados";
						//		else  //($cursoID > 0)
						//			echo "<br>Curso Pasado";
						//		
						//		echo " - No puede haber reservas activas en el sistema";
						//	}
						//	else
						//		echo "<br>No hay reservas pendientes en el sistema.";
						//}
						//echo "<br>";	

						
						// seleccionamos las fechas de las ultimas reservas ejecutadas
						$sql="SELECT * FROM reservas WHERE estado_reserva = 1 AND curso_id IN ($cursos_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_ejecutadas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count2 = mysql_num_rows($registros);
						$j = 0;
						if ($count2 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha2[$j] = $reg['fecha_reserva'];
							   $hora2[$j] = $reg['horario_reserva'];
							   $user_id2[$j] = $reg['user_id'];
							   $curso_id2[$j] = $reg['curso_id'];
							   $numero_pod2[$j] = $reg['num_POD'];
							   $estadoreserva2[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas APROVECHADAS";
						//if ($count2 > 0)
						//{
						//	for ($i=0; $i<count($fecha2); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas ejecutadas en el sistema.";
						//echo "<br>";
						
						
						// seleccionamos las fechas de las ultimas reservas canceladas
						$sql="SELECT * FROM reservas WHERE estado_reserva = -1 AND curso_id IN ($cursos_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_canceladas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count3 = mysql_num_rows($registros);
						$j = 0;
						if ($count3 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha3[$j] = $reg['fecha_reserva'];
							   $hora3[$j] = $reg['horario_reserva'];
							   $user_id3[$j] = $reg['user_id'];
							   $curso_id3[$j] = $reg['curso_id'];
							   $numero_pod3[$j] = $reg['num_POD'];
							   $estadoreserva3[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas CANCELADAS";
						//if ($count3 > 0)
						//{
						//	for ($i=0; $i<count($fecha3); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas canceladas en el sistema.";
						//echo "<br>";
						

						
						// seleccionamos las reservas que se estan ejecutando ahora mismo
						///// UN CURSO PASADO NO PUEDE TENER RESERVAS AHORA///
						$sql="SELECT * FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno' AND curso_id IN ($cursos_str) ORDER BY num_POD ASC";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count4 = mysql_num_rows($registros);
						$j = 0;  
						if ($count4 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha4[$j] = $reg['fecha_reserva'];
							   $hora4[$j] = $reg['horario_reserva'];
							   $user_id4[$j] = $reg['user_id'];
							   $curso_id4[$j] = $reg['curso_id'];
							   $numero_pod4[$j] = $reg['num_POD'];
							   $estadoreserva4[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas EN EJECUCION ahora";
						//if ($count4 > 0)
						//{
						//	for ($i=0; $i<count($fecha4); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- POD #$numero_pod4[$i] para el usuario $user_id4[$i] con el curso $curso_id4[$i].";
						//		echo "  ($fecha4[$i] a las $hora4[$i]) ";
						//		if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; 
						//	}
						//}else
						//	echo "<br>No hay reservas en ejecucion en el sistema.";
						//echo "<br>";	
					}
					else if ($ultimasreservasID == 1)     //solo las recientes
					{
						// seleccionamos las fechas de las ultimas reservas ejecutadas
						$sql="SELECT * FROM reservas WHERE estado_reserva = 1 AND curso_id IN ($cursos_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_ejecutadas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count2 = mysql_num_rows($registros);
						$j = 0;
						if ($count2 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha2[$j] = $reg['fecha_reserva'];
							   $hora2[$j] = $reg['horario_reserva'];
							   $user_id2[$j] = $reg['user_id'];
							   $curso_id2[$j] = $reg['curso_id'];
							   $numero_pod2[$j] = $reg['num_POD'];
							   $estadoreserva2[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas APROVECHADAS";
						//if ($count2 > 0)
						//{
						//	for ($i=0; $i<count($fecha2); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas ejecutadas en el sistema.";
						//echo "<br>";
						
						
						// seleccionamos las fechas de las ultimas reservas canceladas
						$sql="SELECT * FROM reservas WHERE estado_reserva = -1 AND curso_id IN ($cursos_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_canceladas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count3 = mysql_num_rows($registros);
						$j = 0;
						if ($count3 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha3[$j] = $reg['fecha_reserva'];
							   $hora3[$j] = $reg['horario_reserva'];
							   $user_id3[$j] = $reg['user_id'];
							   $curso_id3[$j] = $reg['curso_id'];
							   $numero_pod3[$j] = $reg['num_POD'];
							   $estadoreserva3[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas CANCELADAS";
						//if ($count3 > 0)
						//{
						//	for ($i=0; $i<count($fecha3); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas canceladas en el sistema.";
						//echo "<br>";			

					}
					else  if ($ultimasreservasID == 2)   // if ($ultimasreservasID == 2)    //solo las pendientes
					{
						// seleccionamos las fechas de las proximas reservas
						//$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND curso_id IN ($cursos_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND NOT (fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno_actual') AND curso_id IN ($cursos_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count1 = mysql_num_rows($registros);
						$j = 0;
						if ($count1 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha1[$j] = $reg['fecha_reserva'];
							   $hora1[$j] = $reg['horario_reserva'];
							   $user_id1[$j] = $reg['user_id'];
							   $curso_id1[$j] = $reg['curso_id'];
							   $numero_pod1[$j] = $reg['num_POD'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas PENDIENTES";
						//if ($count1 > 0)
						//{
						//	for ($i=0; $i<count($fecha1); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].";
						//	}
						//}
						//else
						//{
						//	if ($estadoID == 2)   //en un curso pasado no puede haber reservas futuras
						//	{
						//		if ($cursoID == 0)
						//			echo "<br>cursos Pasados";
						//		else  //($cursoID > 0)
						//			echo "<br>Curso Pasado";
						//		
						//		echo " - No puede haber reservas activas en el sistema";
						//	}
						//	else
						//		echo "<br>No hay reservas pendientes en el sistema.";
						//}
						//echo "<br>";	

					}
					else   // if ($ultimasreservasID == 3)    //solo las del turno actual
					{					
						// seleccionamos las reservas que se estan ejecutando ahora mismo
						///// UN CURSO PASADO NO PUEDE TENER RESERVAS AHORA///
						$sql="SELECT * FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno' AND curso_id IN ($cursos_str) ORDER BY num_POD ASC";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count4 = mysql_num_rows($registros);
						$j = 0;  
						if ($count4 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha4[$j] = $reg['fecha_reserva'];
							   $hora4[$j] = $reg['horario_reserva'];
							   $user_id4[$j] = $reg['user_id'];
							   $curso_id4[$j] = $reg['curso_id'];
							   $numero_pod4[$j] = $reg['num_POD'];
							   $estadoreserva4[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas EN EJECUCION ahora";
						//if ($count4 > 0)
						//{
						//	for ($i=0; $i<count($fecha4); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- POD #$numero_pod4[$i] para el usuario $user_id4[$i] con el curso $curso_id4[$i].";
						//		echo "  ($fecha4[$i] a las $hora4[$i]) ";
						//		if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; 
						//	}
						//}else
						//	echo "<br>No hay reservas en ejecucion en el sistema.";
						//echo "<br>";	

					}
				}
			}
			else    // ($cursoID > 0)     //un curso en particular  
			{
				if ($ultimasreservasID == 0)     //reservas recientes y pendientes
				{
					// seleccionamos las fechas de las proximas reservas
					//$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND curso_id = $cursoID ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
					$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND NOT (fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno_actual') AND curso_id = $cursoID ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count1 = mysql_num_rows($registros);
					$j = 0;
					if ($count1 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha1[$j] = $reg['fecha_reserva'];
						   $hora1[$j] = $reg['horario_reserva'];
						   $user_id1[$j] = $reg['user_id'];
						   $curso_id1[$j] = $reg['curso_id'];
						   $numero_pod1[$j] = $reg['num_POD'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas PENDIENTES";
					//if ($count1 > 0)
					//{
					//	for ($i=0; $i<count($fecha1); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].";
					//	}
					//}
					//else
					//{
					//	if ($estadoID == 2)   //en un curso pasado no puede haber reservas futuras
					//	{
					//		if ($cursoID == 0)
					//			echo "<br>cursos Pasados";
					//		else  //($cursoID > 0)
					//			echo "<br>Curso Pasado";
					//		
					//		echo " - No puede haber reservas activas en el sistema";
					//	}
					//	else
					//		echo "<br>No hay reservas pendientes en el sistema.";
					//}
					//echo "<br>";	

					
					// seleccionamos las fechas de las ultimas reservas ejecutadas
					$sql="SELECT * FROM reservas WHERE estado_reserva = 1 AND curso_id = $cursoID ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_ejecutadas";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count2 = mysql_num_rows($registros);
					$j = 0;
					if ($count2 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha2[$j] = $reg['fecha_reserva'];
						   $hora2[$j] = $reg['horario_reserva'];
						   $user_id2[$j] = $reg['user_id'];
						   $curso_id2[$j] = $reg['curso_id'];
						   $numero_pod2[$j] = $reg['num_POD'];
						   $estadoreserva2[$j] = $reg['estado_reserva'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas APROVECHADAS";
					//if ($count2 > 0)
					//{
					//	for ($i=0; $i<count($fecha2); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].";
					//	}
					//}else
					//	echo "<br>No hay reservas ejecutadas en el sistema.";
					//echo "<br>";
					
					
					// seleccionamos las fechas de las ultimas reservas canceladas
					$sql="SELECT * FROM reservas WHERE estado_reserva = -1 AND curso_id = $cursoID ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_canceladas";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count3 = mysql_num_rows($registros);
					$j = 0;
					if ($count3 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha3[$j] = $reg['fecha_reserva'];
						   $hora3[$j] = $reg['horario_reserva'];
						   $user_id3[$j] = $reg['user_id'];
						   $curso_id3[$j] = $reg['curso_id'];
						   $numero_pod3[$j] = $reg['num_POD'];
						   $estadoreserva3[$j] = $reg['estado_reserva'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas CANCELADAS";
					//if ($count3 > 0)
					//{
					//	for ($i=0; $i<count($fecha3); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].";
					//	}
					//}else
					//	echo "<br>No hay reservas canceladas en el sistema.";
					//echo "<br>";
					
					
					// seleccionamos las reservas que se estan ejecutando ahora mismo
					$sql="SELECT * FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno' AND curso_id = $cursoID ORDER BY num_POD ASC";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count4 = mysql_num_rows($registros);
					$j = 0;  
					if ($count4 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha4[$j] = $reg['fecha_reserva'];
						   $hora4[$j] = $reg['horario_reserva'];
						   $user_id4[$j] = $reg['user_id'];
						   $curso_id4[$j] = $reg['curso_id'];
						   $numero_pod4[$j] = $reg['num_POD'];
						   $estadoreserva4[$j] = $reg['estado_reserva'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas EN EJECUCION ahora";
					//if ($count4 > 0)
					//{
					//	for ($i=0; $i<count($fecha4); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- POD #$numero_pod4[$i] para el usuario $user_id4[$i] con el curso $curso_id4[$i].";
					//		echo "  ($fecha4[$i] a las $hora4[$i]) ";
					//		if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; 
					//	}
					//}else
					//	echo "<br>No hay reservas en ejecucion en el sistema.";
					//echo "<br>";	
					
				}
				else if ($ultimasreservasID == 1)     //solo las recientes
				{
					// seleccionamos las fechas de las ultimas reservas ejecutadas
					$sql="SELECT * FROM reservas WHERE estado_reserva = 1 AND curso_id = $cursoID ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_ejecutadas";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count2 = mysql_num_rows($registros);
					$j = 0;
					if ($count2 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha2[$j] = $reg['fecha_reserva'];
						   $hora2[$j] = $reg['horario_reserva'];
						   $user_id2[$j] = $reg['user_id'];
						   $curso_id2[$j] = $reg['curso_id'];
						   $numero_pod2[$j] = $reg['num_POD'];
						   $estadoreserva2[$j] = $reg['estado_reserva'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas APROVECHADAS";
					//if ($count2 > 0)
					//{
					//	for ($i=0; $i<count($fecha2); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].";
					//	}
					//}else
					//	echo "<br>No hay reservas ejecutadas en el sistema.";
					//echo "<br>";
					
					
					// seleccionamos las fechas de las ultimas reservas canceladas
					$sql="SELECT * FROM reservas WHERE estado_reserva = -1 AND curso_id = $cursoID ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_canceladas";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count3 = mysql_num_rows($registros);
					$j = 0;
					if ($count3 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha3[$j] = $reg['fecha_reserva'];
						   $hora3[$j] = $reg['horario_reserva'];
						   $user_id3[$j] = $reg['user_id'];
						   $curso_id3[$j] = $reg['curso_id'];
						   $numero_pod3[$j] = $reg['num_POD'];
						   $estadoreserva3[$j] = $reg['estado_reserva'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas CANCELADAS";
					//if ($count3 > 0)
					//{
					//	for ($i=0; $i<count($fecha3); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].";
					//	}
					//}else
					//	echo "<br>No hay reservas canceladas en el sistema.";
					//echo "<br>";			

				}
				else  if ($ultimasreservasID == 2)   // if ($ultimasreservasID == 2)    //solo las pendientes
				{
					// seleccionamos las fechas de las proximas reservas
					//$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND curso_id = $cursoID ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
					$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND NOT (fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno_actual') AND curso_id = $cursoID ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count1 = mysql_num_rows($registros);
					$j = 0;
					if ($count1 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha1[$j] = $reg['fecha_reserva'];
						   $hora1[$j] = $reg['horario_reserva'];
						   $user_id1[$j] = $reg['user_id'];
						   $curso_id1[$j] = $reg['curso_id'];
						   $numero_pod1[$j] = $reg['num_POD'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas PENDIENTES";
					//if ($count1 > 0)
					//{
					//	for ($i=0; $i<count($fecha1); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].";
					//	}
					//}
					//else
					//{
					//	if ($estadoID == 2)   //en un curso pasado no puede haber reservas futuras
					//	{
					//		if ($cursoID == 0)
					//			echo "<br>cursos Pasados";
					//		else  //($cursoID > 0)
					//			echo "<br>Curso Pasado";
					//		
					//		echo " - No puede haber reservas activas en el sistema";
					//	}
					//	else
					//		echo "<br>No hay reservas pendientes en el sistema.";
					//}
					//echo "<br>";

				}
				else   // if ($ultimasreservasID == 3)    //solo las del turno actual
				{					
					// seleccionamos las reservas que se estan ejecutando ahora mismo
					$sql="SELECT * FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno' AND curso_id = $cursoID ORDER BY num_POD ASC";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count4 = mysql_num_rows($registros);
					$j = 0;  
					if ($count4 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha4[$j] = $reg['fecha_reserva'];
						   $hora4[$j] = $reg['horario_reserva'];
						   $user_id4[$j] = $reg['user_id'];
						   $curso_id4[$j] = $reg['curso_id'];
						   $numero_pod4[$j] = $reg['num_POD'];
						   $estadoreserva4[$j] = $reg['estado_reserva'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas EN EJECUCION ahora";
					//if ($count4 > 0)
					//{
					//	for ($i=0; $i<count($fecha4); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- POD #$numero_pod4[$i] para el usuario $user_id4[$i] con el curso $curso_id4[$i].";
					//		echo "  ($fecha4[$i] a las $hora4[$i]) ";
					//		if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; 
					//	}
					//}else
					//	echo "<br>No hay reservas en ejecucion en el sistema.";
					//echo "<br>";	
					
				}
			
			}
			
		}
		else if ($estadoID=="")   //implicitamente $usuarioID!="", o sea, tenemos seleccionado un alumno		
		{

			if ($usuarioID == 0)   //todos los usuarios (alumnos)
			{
				//pasamos los codigos de usuario almacenados en el array $usuarios a la cadena $usuarios_str
				$usuarios_str = implode(",", $usuarios); 
				
				//echo "<br>usuarios_str = $usuarios_str";
				
				
				if ($ultimasreservasID == 0)     //reservas recientes y pendientes
				{
					// seleccionamos las fechas de las proximas reservas
					//$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND user_id IN ($usuarios_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas ";
					$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND NOT (fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno_actual') AND user_id IN ($usuarios_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count1 = mysql_num_rows($registros);
					$j = 0;
					if ($count1 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha1[$j] = $reg['fecha_reserva'];
						   $hora1[$j] = $reg['horario_reserva'];
						   $user_id1[$j] = $reg['user_id'];
						   $curso_id1[$j] = $reg['curso_id'];
						   $numero_pod1[$j] = $reg['num_POD'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas PENDIENTES";
					//if ($count1 > 0)
					//{
					//	for ($i=0; $i<count($fecha1); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].";
					//	}
					//}else
					//{
					//	echo "<br>No hay reservas pendientes en el sistema.";
					//}
					//echo "<br>";	

					
					// seleccionamos las fechas de las ultimas reservas ejecutadas
					$sql="SELECT * FROM reservas WHERE estado_reserva = 1 AND user_id IN ($usuarios_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_ejecutadas";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count2 = mysql_num_rows($registros);
					$j = 0;
					if ($count2 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha2[$j] = $reg['fecha_reserva'];
						   $hora2[$j] = $reg['horario_reserva'];
						   $user_id2[$j] = $reg['user_id'];
						   $curso_id2[$j] = $reg['curso_id'];
						   $numero_pod2[$j] = $reg['num_POD'];
						   $estadoreserva2[$j] = $reg['estado_reserva'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas APROVECHADAS";
					//if ($count2 > 0)
					//{
					//	for ($i=0; $i<count($fecha2); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].";
					//	}
					//}else
					//	echo "<br>No hay reservas ejecutadas en el sistema.";
					//echo "<br>";
					
					
					// seleccionamos las fechas de las ultimas reservas canceladas
					$sql="SELECT * FROM reservas WHERE estado_reserva = -1 AND user_id IN ($usuarios_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_canceladas";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count3 = mysql_num_rows($registros);
					$j = 0;
					if ($count3 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha3[$j] = $reg['fecha_reserva'];
						   $hora3[$j] = $reg['horario_reserva'];
						   $user_id3[$j] = $reg['user_id'];
						   $curso_id3[$j] = $reg['curso_id'];
						   $numero_pod3[$j] = $reg['num_POD'];
						   $estadoreserva3[$j] = $reg['estado_reserva'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas CANCELADAS";
					//if ($count3 > 0)
					//{
					//	for ($i=0; $i<count($fecha3); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].";
					//	}
					//}else
					//	echo "<br>No hay reservas canceladas en el sistema.";
					//echo "<br>";
					
					
					// seleccionamos las reservas que se estan ejecutando ahora mismo
					$sql="SELECT * FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno' AND user_id IN ($usuarios_str) ORDER BY num_POD ASC";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count4 = mysql_num_rows($registros);
					$j = 0;  
					if ($count4 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha4[$j] = $reg['fecha_reserva'];
						   $hora4[$j] = $reg['horario_reserva'];
						   $user_id4[$j] = $reg['user_id'];
						   $curso_id4[$j] = $reg['curso_id'];
						   $numero_pod4[$j] = $reg['num_POD'];
						   $estadoreserva4[$j] = $reg['estado_reserva'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas EN EJECUCION ahora";
					//if ($count4 > 0)
					//{
					//	for ($i=0; $i<count($fecha4); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- POD #$numero_pod4[$i] para el usuario $user_id4[$i] con el curso $curso_id4[$i].";
					//		echo "  ($fecha4[$i] a las $hora4[$i]) ";
					//		if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; 
					//	}
					//}else
					//	echo "<br>No hay reservas en ejecucion en el sistema.";
					//echo "<br>";	
					
				}
				else if ($ultimasreservasID == 1)     //solo las recientes
				{
					// seleccionamos las fechas de las ultimas reservas ejecutadas
					$sql="SELECT * FROM reservas WHERE estado_reserva = 1 AND user_id IN ($usuarios_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_ejecutadas";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count2 = mysql_num_rows($registros);
					$j = 0;
					if ($count2 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha2[$j] = $reg['fecha_reserva'];
						   $hora2[$j] = $reg['horario_reserva'];
						   $user_id2[$j] = $reg['user_id'];
						   $curso_id2[$j] = $reg['curso_id'];
						   $numero_pod2[$j] = $reg['num_POD'];
						   $estadoreserva2[$j] = $reg['estado_reserva'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas APROVECHADAS";
					//if ($count2 > 0)
					//{
					//	for ($i=0; $i<count($fecha2); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].";
					//	}
					//}else
					//	echo "<br>No hay reservas ejecutadas en el sistema.";
					//echo "<br>";
					
					
					// seleccionamos las fechas de las ultimas reservas canceladas
					$sql="SELECT * FROM reservas WHERE estado_reserva = -1 AND user_id IN ($usuarios_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_canceladas";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count3 = mysql_num_rows($registros);
					$j = 0;
					if ($count3 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha3[$j] = $reg['fecha_reserva'];
						   $hora3[$j] = $reg['horario_reserva'];
						   $user_id3[$j] = $reg['user_id'];
						   $curso_id3[$j] = $reg['curso_id'];
						   $numero_pod3[$j] = $reg['num_POD'];
						   $estadoreserva3[$j] = $reg['estado_reserva'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas CANCELADAS";
					//if ($count3 > 0)
					//{
					//	for ($i=0; $i<count($fecha3); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].";
					//	}
					//}else
					//	echo "<br>No hay reservas canceladas en el sistema.";
					//echo "<br>";			

				}
				else  if ($ultimasreservasID == 2)   // if ($ultimasreservasID == 2)    //solo las pendientes
				{
					// seleccionamos las fechas de las proximas reservas
					//$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND user_id IN ($usuarios_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
					$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND NOT (fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno_actual') AND user_id IN ($usuarios_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count1 = mysql_num_rows($registros);
					$j = 0;
					if ($count1 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha1[$j] = $reg['fecha_reserva'];
						   $hora1[$j] = $reg['horario_reserva'];
						   $user_id1[$j] = $reg['user_id'];
						   $curso_id1[$j] = $reg['curso_id'];
						   $numero_pod1[$j] = $reg['num_POD'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas PENDIENTES";
					//if ($count1 > 0)
					//{
					//	for ($i=0; $i<count($fecha1); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].";
					//	}
					//}
					//else
					//{
					//	echo "<br>No hay reservas pendientes en el sistema.";
					//}
					//echo "<br>";	

				}
				else   // if ($ultimasreservasID == 3)    //solo las del turno actual
				{										
					// seleccionamos las reservas que se estan ejecutando ahora mismo
					$sql="SELECT * FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno' AND user_id IN ($usuarios_str) ORDER BY num_POD ASC";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count4 = mysql_num_rows($registros);
					$j = 0;  
					if ($count4 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha4[$j] = $reg['fecha_reserva'];
						   $hora4[$j] = $reg['horario_reserva'];
						   $user_id4[$j] = $reg['user_id'];
						   $curso_id4[$j] = $reg['curso_id'];
						   $numero_pod4[$j] = $reg['num_POD'];
						   $estadoreserva4[$j] = $reg['estado_reserva'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas EN EJECUCION ahora";
					//if ($count4 > 0)
					//{
					//	for ($i=0; $i<count($fecha4); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- POD #$numero_pod4[$i] para el usuario $user_id4[$i] con el curso $curso_id4[$i].";
					//		echo "  ($fecha4[$i] a las $hora4[$i]) ";
					//		if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; 
					//	}
					//}else
					//	echo "<br>No hay reservas en ejecucion en el sistema.";
					//echo "<br>";	
					
				}
		
			}
			else    // ($usuarioID > 0)     //un usuario en particular  
			{
				if ($ultimasreservasID == 0)     //reservas recientes y pendientes
				{
					// seleccionamos las fechas de las proximas reservas
					//$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND user_id = $usuarioID ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
					$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND NOT (fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno_actual') AND user_id = $usuarioID ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count1 = mysql_num_rows($registros);
					$j = 0;
					if ($count1 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha1[$j] = $reg['fecha_reserva'];
						   $hora1[$j] = $reg['horario_reserva'];
						   $user_id1[$j] = $reg['user_id'];
						   $curso_id1[$j] = $reg['curso_id'];
						   $numero_pod1[$j] = $reg['num_POD'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas PENDIENTES";
					//if ($count1 > 0)
					//{
					//	for ($i=0; $i<count($fecha1); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].";
					//	}
					//}
					//else
					//{
					//	echo "<br>No hay reservas pendientes en el sistema.";
					//}
					//echo "<br>";	

					
					// seleccionamos las fechas de las ultimas reservas ejecutadas
					$sql="SELECT * FROM reservas WHERE estado_reserva = 1 AND user_id = $usuarioID ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_ejecutadas";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count2 = mysql_num_rows($registros);
					$j = 0;
					if ($count2 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha2[$j] = $reg['fecha_reserva'];
						   $hora2[$j] = $reg['horario_reserva'];
						   $user_id2[$j] = $reg['user_id'];
						   $curso_id2[$j] = $reg['curso_id'];
						   $numero_pod2[$j] = $reg['num_POD'];
						   $estadoreserva2[$j] = $reg['estado_reserva'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas APROVECHADAS";
					//if ($count2 > 0)
					//{
					//	for ($i=0; $i<count($fecha2); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].";
					//	}
					//}else
					//	echo "<br>No hay reservas ejecutadas en el sistema.";
					//echo "<br>";
					
					
					// seleccionamos las fechas de las ultimas reservas canceladas
					$sql="SELECT * FROM reservas WHERE estado_reserva = -1 AND user_id = $usuarioID ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_canceladas";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count3 = mysql_num_rows($registros);
					$j = 0;
					if ($count3 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha3[$j] = $reg['fecha_reserva'];
						   $hora3[$j] = $reg['horario_reserva'];
						   $user_id3[$j] = $reg['user_id'];
						   $curso_id3[$j] = $reg['curso_id'];
						   $numero_pod3[$j] = $reg['num_POD'];
						   $estadoreserva3[$j] = $reg['estado_reserva'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas CANCELADAS";
					//if ($count3 > 0)
					//{
					//	for ($i=0; $i<count($fecha3); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].";
					//	}
					//}else
					//	echo "<br>No hay reservas canceladas en el sistema.";
					//echo "<br>";
					

					// seleccionamos las reservas que se estan ejecutando ahora mismo
					$sql="SELECT * FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno' AND user_id = $usuarioID ORDER BY num_POD ASC";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count4 = mysql_num_rows($registros);
					$j = 0;  
					if ($count4 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha4[$j] = $reg['fecha_reserva'];
						   $hora4[$j] = $reg['horario_reserva'];
						   $user_id4[$j] = $reg['user_id'];
						   $curso_id4[$j] = $reg['curso_id'];
						   $numero_pod4[$j] = $reg['num_POD'];
						   $estadoreserva4[$j] = $reg['estado_reserva'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas EN EJECUCION ahora";
					//if ($count4 > 0)
					//{
					//	for ($i=0; $i<count($fecha4); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- POD #$numero_pod4[$i] para el usuario $user_id4[$i] con el curso $curso_id4[$i].";
					//		echo "  ($fecha4[$i] a las $hora4[$i]) ";
					//		if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; 
					//	}
					//}else
					//	echo "<br>No hay reservas en ejecucion en el sistema.";
					//echo "<br>";	
					
				}
				else if ($ultimasreservasID == 1)     //solo las recientes
				{
					// seleccionamos las fechas de las ultimas reservas ejecutadas
					$sql="SELECT * FROM reservas WHERE estado_reserva = 1 AND user_id = $usuarioID ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_ejecutadas";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count2 = mysql_num_rows($registros);
					$j = 0;
					if ($count2 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha2[$j] = $reg['fecha_reserva'];
						   $hora2[$j] = $reg['horario_reserva'];
						   $user_id2[$j] = $reg['user_id'];
						   $curso_id2[$j] = $reg['curso_id'];
						   $numero_pod2[$j] = $reg['num_POD'];
						   $estadoreserva2[$j] = $reg['estado_reserva'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas APROVECHADAS";
					//if ($count2 > 0)
					///{
					//	for ($i=0; $i<count($fecha2); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].";
					//	}
					//}else
					//	echo "<br>No hay reservas ejecutadas en el sistema.";
					//echo "<br>";
					
					
					// seleccionamos las fechas de las ultimas reservas canceladas
					$sql="SELECT * FROM reservas WHERE estado_reserva = -1 AND user_id = $usuarioID ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_canceladas";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count3 = mysql_num_rows($registros);
					$j = 0;
					if ($count3 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha3[$j] = $reg['fecha_reserva'];
						   $hora3[$j] = $reg['horario_reserva'];
						   $user_id3[$j] = $reg['user_id'];
						   $curso_id3[$j] = $reg['curso_id'];
						   $numero_pod3[$j] = $reg['num_POD'];
						   $estadoreserva3[$j] = $reg['estado_reserva'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas CANCELADAS";
					//if ($count3 > 0)
					//{
					//	for ($i=0; $i<count($fecha3); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].";
					//	}
					//}else
					//	echo "<br>No hay reservas canceladas en el sistema.";
					//echo "<br>";			

				}
				else  if ($ultimasreservasID == 2)   // if ($ultimasreservasID == 2)    //solo las pendientes
				{
					// seleccionamos las fechas de las proximas reservas
					//$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND user_id = $usuarioID ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
					$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND NOT (fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno_actual') AND user_id = $usuarioID ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count1 = mysql_num_rows($registros);
					$j = 0;
					if ($count1 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha1[$j] = $reg['fecha_reserva'];
						   $hora1[$j] = $reg['horario_reserva'];
						   $user_id1[$j] = $reg['user_id'];
						   $curso_id1[$j] = $reg['curso_id'];
						   $numero_pod1[$j] = $reg['num_POD'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas PENDIENTES";
					//if ($count1 > 0)
					//{
					//	for ($i=0; $i<count($fecha1); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].";
					//	}
					//}
					//else
					//{
					//	echo "<br>No hay reservas pendientes en el sistema.";
					//}
					//echo "<br>";

				}
				else   // if ($ultimasreservasID == 3)    //solo las del turno actual
				{										
					// seleccionamos las reservas que se estan ejecutando ahora mismo
					$sql="SELECT * FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno' AND user_id = $usuarioID ORDER BY num_POD ASC";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count4 = mysql_num_rows($registros);
					$j = 0;  
					if ($count4 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha4[$j] = $reg['fecha_reserva'];
						   $hora4[$j] = $reg['horario_reserva'];
						   $user_id4[$j] = $reg['user_id'];
						   $curso_id4[$j] = $reg['curso_id'];
						   $numero_pod4[$j] = $reg['num_POD'];
						   $estadoreserva4[$j] = $reg['estado_reserva'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas EN EJECUCION ahora";
					//if ($count4 > 0)
					//{
					//	for ($i=0; $i<count($fecha4); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- POD #$numero_pod4[$i] para el usuario $user_id4[$i] con el curso $curso_id4[$i].";
					//		echo "  ($fecha4[$i] a las $hora4[$i]) ";
					//		if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; 
					//	}
					//}else
					//	echo "<br>No hay reservas en ejecucion en el sistema.";
					//echo "<br>";	
					
				}
			
			}		
		}
		else  //      //implicitamente $cursoID!="" y $usuarioID!="", o sea, tenemos seleccionado un curso y un alumno
		{
			if ($cursoID == 0 && $usuarioID == 0)    //un grupo de cursos, almacenados en la variable $cursos, y dentro de ellos un grupo de alumnos, almacenados en la variable $usuarios
			{
			
				if ($estadoID == 0)    //todos los cursos: activos y pasados
				{
					//pasamos los codigos de usuario almacenados en el array $usuarios a la cadena $usuarios_str
					$cursos_str = implode(",", $cursos); 
					
					//echo "<br>cursos_str = $cursos_str";
					
					//pasamos los codigos de usuario almacenados en el array $usuarios a la cadena $usuarios_str
					$usuarios_str = implode(",", $usuarios); 
					
					//echo "<br>usuarios_str = $usuarios_str";


					if ($ultimasreservasID == 0)     //reservas recientes y pendientes
					{
						// seleccionamos las fechas de las proximas reservas
						//$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND NOT (fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno_actual') AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count1 = mysql_num_rows($registros);
						$j = 0;
						if ($count1 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha1[$j] = $reg['fecha_reserva'];
							   $hora1[$j] = $reg['horario_reserva'];
							   $user_id1[$j] = $reg['user_id'];
							   $curso_id1[$j] = $reg['curso_id'];
							   $numero_pod1[$j] = $reg['num_POD'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas PENDIENTES";
						//if ($count1 > 0)
						//{
						//	for ($i=0; $i<count($fecha1); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].";
						//	}
						//}
						//else
						//{
						//	if ($estadoID == 2)   //en un curso pasado no puede haber reservas futuras
						//	{
						//		if ($cursoID == 0)
						//			echo "<br>cursos Pasados";
						//		else  //($cursoID > 0)
						//			echo "<br>Curso Pasado";
						//		
						//		echo " - No puede haber reservas activas en el sistema";
						//	}
						//	else
						//	{
						//		echo "<br>No hay reservas pendientes en el sistema.";
						//	}
						//}
						//echo "<br>";	

						
						// seleccionamos las fechas de las ultimas reservas ejecutadas
						$sql="SELECT * FROM reservas WHERE estado_reserva = 1 AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_ejecutadas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count2 = mysql_num_rows($registros);
						$j = 0;
						if ($count2 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha2[$j] = $reg['fecha_reserva'];
							   $hora2[$j] = $reg['horario_reserva'];
							   $user_id2[$j] = $reg['user_id'];
							   $curso_id2[$j] = $reg['curso_id'];
							   $numero_pod2[$j] = $reg['num_POD'];
							   $estadoreserva2[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas APROVECHADAS";
						//if ($count2 > 0)
						//{
						//	for ($i=0; $i<count($fecha2); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas ejecutadas en el sistema.";
						//echo "<br>";
						
						
						// seleccionamos las fechas de las ultimas reservas canceladas
						$sql="SELECT * FROM reservas WHERE estado_reserva = -1 AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_canceladas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count3 = mysql_num_rows($registros);
						$j = 0;
						if ($count3 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha3[$j] = $reg['fecha_reserva'];
							   $hora3[$j] = $reg['horario_reserva'];
							   $user_id3[$j] = $reg['user_id'];
							   $curso_id3[$j] = $reg['curso_id'];
							   $numero_pod3[$j] = $reg['num_POD'];
							   $estadoreserva3[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas CANCELADAS";
						//if ($count3 > 0)
						//{
						//	for ($i=0; $i<count($fecha3); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas canceladas en el sistema.";
						//echo "<br>";
						
						
						// seleccionamos las reservas que se estan ejecutando ahora mismo
						$sql="SELECT * FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno' AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str) ORDER BY num_POD ASC";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count4 = mysql_num_rows($registros);
						$j = 0;  
						if ($count4 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha4[$j] = $reg['fecha_reserva'];
							   $hora4[$j] = $reg['horario_reserva'];
							   $user_id4[$j] = $reg['user_id'];
							   $curso_id4[$j] = $reg['curso_id'];
							   $numero_pod4[$j] = $reg['num_POD'];
							   $estadoreserva4[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas EN EJECUCION ahora";
						//if ($count4 > 0)
						//{
						//	for ($i=0; $i<count($fecha4); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- POD #$numero_pod4[$i] para el usuario $user_id4[$i] con el curso $curso_id4[$i].";
						//		echo "  ($fecha4[$i] a las $hora4[$i]) ";
						//		if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; 
						//	}
						//}else
						//	echo "<br>No hay reservas en ejecucion en el sistema.";
						//echo "<br>";	
						
					}
					else if ($ultimasreservasID == 1)     //solo las recientes
					{
						// seleccionamos las fechas de las ultimas reservas ejecutadas
						$sql="SELECT * FROM reservas WHERE estado_reserva = 1 AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_ejecutadas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count2 = mysql_num_rows($registros);
						$j = 0;
						if ($count2 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha2[$j] = $reg['fecha_reserva'];
							   $hora2[$j] = $reg['horario_reserva'];
							   $user_id2[$j] = $reg['user_id'];
							   $curso_id2[$j] = $reg['curso_id'];
							   $numero_pod2[$j] = $reg['num_POD'];
							   $estadoreserva2[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas APROVECHADAS";
						//if ($count2 > 0)
						//{
						//	for ($i=0; $i<count($fecha2); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas ejecutadas en el sistema.";
						//echo "<br>";
						
						
						// seleccionamos las fechas de las ultimas reservas canceladas
						$sql="SELECT * FROM reservas WHERE estado_reserva = -1 AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_canceladas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count3 = mysql_num_rows($registros);
						$j = 0;
						if ($count3 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha3[$j] = $reg['fecha_reserva'];
							   $hora3[$j] = $reg['horario_reserva'];
							   $user_id3[$j] = $reg['user_id'];
							   $curso_id3[$j] = $reg['curso_id'];
							   $numero_pod3[$j] = $reg['num_POD'];
							   $estadoreserva3[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas CANCELADAS";
						//if ($count3 > 0)
						//{
						//	for ($i=0; $i<count($fecha3); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas canceladas en el sistema.";
						//echo "<br>";			

					}
					else  if ($ultimasreservasID == 2)   // if ($ultimasreservasID == 2)    //solo las pendientes
					{
						// seleccionamos las fechas de las proximas reservas
						//$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND NOT (fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno_actual') AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count1 = mysql_num_rows($registros);
						$j = 0;
						if ($count1 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha1[$j] = $reg['fecha_reserva'];
							   $hora1[$j] = $reg['horario_reserva'];
							   $user_id1[$j] = $reg['user_id'];
							   $curso_id1[$j] = $reg['curso_id'];
							   $numero_pod1[$j] = $reg['num_POD'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas PENDIENTES";
						//if ($count1 > 0)
						//{
						//	for ($i=0; $i<count($fecha1); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].";
						//	}
						//}
						//else
						//{
						//	if ($estadoID == 2)   //en un curso pasado no puede haber reservas futuras
						//	{
						//		if ($cursoID == 0)
						//			echo "<br>cursos Pasados";
						//		else  //($cursoID > 0)
						//			echo "<br>Curso Pasado";
						//		
						//		echo " - No puede haber reservas activas en el sistema";
						//	}
						//	else
						//	{
						//		echo "<br>No hay reservas pendientes en el sistema.";
						//	}
						//}
						//echo "<br>";

					}
					else   // if ($ultimasreservasID == 3)    //solo las del turno actual
					{													
						// seleccionamos las reservas que se estan ejecutando ahora mismo
						$sql="SELECT * FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno' AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str) ORDER BY num_POD ASC";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count4 = mysql_num_rows($registros);
						$j = 0;  
						if ($count4 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha4[$j] = $reg['fecha_reserva'];
							   $hora4[$j] = $reg['horario_reserva'];
							   $user_id4[$j] = $reg['user_id'];
							   $curso_id4[$j] = $reg['curso_id'];
							   $numero_pod4[$j] = $reg['num_POD'];
							   $estadoreserva4[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas EN EJECUCION ahora";
						//if ($count4 > 0)
						//{
						//	for ($i=0; $i<count($fecha4); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- POD #$numero_pod4[$i] para el usuario $user_id4[$i] con el curso $curso_id4[$i].";
						//		echo "  ($fecha4[$i] a las $hora4[$i]) ";
						//		if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; 
						//	}
						//}else
						//	echo "<br>No hay reservas en ejecucion en el sistema.";
						//echo "<br>";	
						
					}
				}
				else if ($estadoID == 1)    //solo los cursos activos
				{
					//pasamos los codigos de usuario almacenados en el array $usuarios a la cadena $usuarios_str
					$cursos_str = implode(",", $cursos); 
					
					//echo "<br>cursos_str = $cursos_str";
					
					//pasamos los codigos de usuario almacenados en el array $usuarios a la cadena $usuarios_str
					$usuarios_str = implode(",", $usuarios); 
					
					//echo "<br>usuarios_str = $usuarios_str";


					if ($ultimasreservasID == 0)     //reservas recientes y pendientes
					{
						// seleccionamos las fechas de las proximas reservas
						//$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND NOT (fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno_actual') AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count1 = mysql_num_rows($registros);
						$j = 0;
						if ($count1 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha1[$j] = $reg['fecha_reserva'];
							   $hora1[$j] = $reg['horario_reserva'];
							   $user_id1[$j] = $reg['user_id'];
							   $curso_id1[$j] = $reg['curso_id'];
							   $numero_pod1[$j] = $reg['num_POD'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas PENDIENTES";
						//if ($count1 > 0)
						//{
						//	for ($i=0; $i<count($fecha1); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].";
						//	}
						//}
						//else
						//{
						//	if ($estadoID == 2)   //en un curso pasado no puede haber reservas futuras
						//	{
						//		if ($cursoID == 0)
						//			echo "<br>cursos Pasados";
						//		else  //($cursoID > 0)
						//			echo "<br>Curso Pasado";
						//		
						//		echo " - No puede haber reservas activas en el sistema";
						//	}
						//	else
						//	{
						//		echo "<br>No hay reservas pendientes en el sistema.";
						//	}
						//}
						//echo "<br>";	

						
						// seleccionamos las fechas de las ultimas reservas ejecutadas
						$sql="SELECT * FROM reservas WHERE estado_reserva = 1 AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_ejecutadas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count2 = mysql_num_rows($registros);
						$j = 0;
						if ($count2 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha2[$j] = $reg['fecha_reserva'];
							   $hora2[$j] = $reg['horario_reserva'];
							   $user_id2[$j] = $reg['user_id'];
							   $curso_id2[$j] = $reg['curso_id'];
							   $numero_pod2[$j] = $reg['num_POD'];
							   $estadoreserva2[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas APROVECHADAS";
						//if ($count2 > 0)
						//{
						//	for ($i=0; $i<count($fecha2); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas ejecutadas en el sistema.";
						//echo "<br>";
						
						
						// seleccionamos las fechas de las ultimas reservas canceladas
						$sql="SELECT * FROM reservas WHERE estado_reserva = -1 AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_canceladas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count3 = mysql_num_rows($registros);
						$j = 0;
						if ($count3 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha3[$j] = $reg['fecha_reserva'];
							   $hora3[$j] = $reg['horario_reserva'];
							   $user_id3[$j] = $reg['user_id'];
							   $curso_id3[$j] = $reg['curso_id'];
							   $numero_pod3[$j] = $reg['num_POD'];
							   $estadoreserva3[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas CANCELADAS";
						//if ($count3 > 0)
						//{
						//	for ($i=0; $i<count($fecha3); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas canceladas en el sistema.";
						//echo "<br>";
						
						
						// seleccionamos las reservas que se estan ejecutando ahora mismo
						$sql="SELECT * FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno' AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str) ORDER BY num_POD ASC";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count4 = mysql_num_rows($registros);
						$j = 0;  
						if ($count4 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha4[$j] = $reg['fecha_reserva'];
							   $hora4[$j] = $reg['horario_reserva'];
							   $user_id4[$j] = $reg['user_id'];
							   $curso_id4[$j] = $reg['curso_id'];
							   $numero_pod4[$j] = $reg['num_POD'];
							   $estadoreserva4[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas EN EJECUCION ahora";
						//if ($count4 > 0)
						//{
						//	for ($i=0; $i<count($fecha4); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- POD #$numero_pod4[$i] para el usuario $user_id4[$i] con el curso $curso_id4[$i].";
						//		echo "  ($fecha4[$i] a las $hora4[$i]) ";
						//		if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; 
						//	}
						//}else
						//	echo "<br>No hay reservas en ejecucion en el sistema.";
						//echo "<br>";	
						
					}
					else if ($ultimasreservasID == 1)     //solo las recientes
					{
						// seleccionamos las fechas de las ultimas reservas ejecutadas
						$sql="SELECT * FROM reservas WHERE estado_reserva = 1 AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_ejecutadas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count2 = mysql_num_rows($registros);
						$j = 0;
						if ($count2 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha2[$j] = $reg['fecha_reserva'];
							   $hora2[$j] = $reg['horario_reserva'];
							   $user_id2[$j] = $reg['user_id'];
							   $curso_id2[$j] = $reg['curso_id'];
							   $numero_pod2[$j] = $reg['num_POD'];
							   $estadoreserva2[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas APROVECHADAS";
						//if ($count2 > 0)
						//{
						//	for ($i=0; $i<count($fecha2); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas ejecutadas en el sistema.";
						//echo "<br>";
						
						
						// seleccionamos las fechas de las ultimas reservas canceladas
						$sql="SELECT * FROM reservas WHERE estado_reserva = -1 AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_canceladas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count3 = mysql_num_rows($registros);
						$j = 0;
						if ($count3 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha3[$j] = $reg['fecha_reserva'];
							   $hora3[$j] = $reg['horario_reserva'];
							   $user_id3[$j] = $reg['user_id'];
							   $curso_id3[$j] = $reg['curso_id'];
							   $numero_pod3[$j] = $reg['num_POD'];
							   $estadoreserva3[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas CANCELADAS";
						//if ($count3 > 0)
						//{
						//	for ($i=0; $i<count($fecha3); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas canceladas en el sistema.";
						//echo "<br>";			

					}
					else  if ($ultimasreservasID == 2)   // if ($ultimasreservasID == 2)    //solo las pendientes
					{
						// seleccionamos las fechas de las proximas reservas
						//$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND NOT (fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno_actual') AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count1 = mysql_num_rows($registros);
						$j = 0;
						if ($count1 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha1[$j] = $reg['fecha_reserva'];
							   $hora1[$j] = $reg['horario_reserva'];
							   $user_id1[$j] = $reg['user_id'];
							   $curso_id1[$j] = $reg['curso_id'];
							   $numero_pod1[$j] = $reg['num_POD'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas PENDIENTES";
						//if ($count1 > 0)
						//{
						//	for ($i=0; $i<count($fecha1); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].";
						//	}
						//}
						//else
						//{
						//	if ($estadoID == 2)   //en un curso pasado no puede haber reservas futuras
						//	{
						//		if ($cursoID == 0)
						//			echo "<br>cursos Pasados";
						//		else  //($cursoID > 0)
						//			echo "<br>Curso Pasado";
						//		
						//		echo " - No puede haber reservas activas en el sistema";
						//	}
						//	else
						//	{
						//		echo "<br>No hay reservas pendientes en el sistema.";
						//	}
						//}
						//echo "<br>";

					}
					else   // if ($ultimasreservasID == 3)    //solo las del turno actual
					{													
						// seleccionamos las reservas que se estan ejecutando ahora mismo
						$sql="SELECT * FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno' AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str) ORDER BY num_POD ASC";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count4 = mysql_num_rows($registros);
						$j = 0;  
						if ($count4 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha4[$j] = $reg['fecha_reserva'];
							   $hora4[$j] = $reg['horario_reserva'];
							   $user_id4[$j] = $reg['user_id'];
							   $curso_id4[$j] = $reg['curso_id'];
							   $numero_pod4[$j] = $reg['num_POD'];
							   $estadoreserva4[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas EN EJECUCION ahora";
						//if ($count4 > 0)
						//{
						//	for ($i=0; $i<count($fecha4); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- POD #$numero_pod4[$i] para el usuario $user_id4[$i] con el curso $curso_id4[$i].";
						//		echo "  ($fecha4[$i] a las $hora4[$i]) ";
						//		if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; 
						//	}
						//}else
						//	echo "<br>No hay reservas en ejecucion en el sistema.";
						//echo "<br>";	
						
					}
				}
				else  	  ///if ($estadoID == 2)    //solo los cursos pasados
				{
					//pasamos los codigos de usuario almacenados en el array $usuarios a la cadena $usuarios_str
					$cursos_str = implode(",", $cursos); 
					
					//echo "<br>cursos_str = $cursos_str";
					
					//pasamos los codigos de usuario almacenados en el array $usuarios a la cadena $usuarios_str
					$usuarios_str = implode(",", $usuarios); 
					
					//echo "<br>usuarios_str = $usuarios_str";


					if ($ultimasreservasID == 0)     //reservas recientes y pendientes
					{
						// seleccionamos las fechas de las proximas reservas
						//$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND NOT (fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno_actual') AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count1 = mysql_num_rows($registros);
						$j = 0;
						if ($count1 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha1[$j] = $reg['fecha_reserva'];
							   $hora1[$j] = $reg['horario_reserva'];
							   $user_id1[$j] = $reg['user_id'];
							   $curso_id1[$j] = $reg['curso_id'];
							   $numero_pod1[$j] = $reg['num_POD'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas PENDIENTES";
						//if ($count1 > 0)
						//{
						//	for ($i=0; $i<count($fecha1); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].";
						//	}
						//}
						//else
						//{
						//	if ($estadoID == 2)   //en un curso pasado no puede haber reservas futuras
						//	{
						//		if ($cursoID == 0)
						//			echo "<br>cursos Pasados";
						//		else  //($cursoID > 0)
						//			echo "<br>Curso Pasado";
						//		
						//		echo " - No puede haber reservas activas en el sistema";
						//	}
						//	else
						//	{
						//		echo "<br>No hay reservas pendientes en el sistema.";
						//	}
						//}
						//echo "<br>";	

						
						// seleccionamos las fechas de las ultimas reservas ejecutadas
						$sql="SELECT * FROM reservas WHERE estado_reserva = 1 AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_ejecutadas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count2 = mysql_num_rows($registros);
						$j = 0;
						if ($count2 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha2[$j] = $reg['fecha_reserva'];
							   $hora2[$j] = $reg['horario_reserva'];
							   $user_id2[$j] = $reg['user_id'];
							   $curso_id2[$j] = $reg['curso_id'];
							   $numero_pod2[$j] = $reg['num_POD'];
							   $estadoreserva2[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas APROVECHADAS";
						//if ($count2 > 0)
						//{
						//	for ($i=0; $i<count($fecha2); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas ejecutadas en el sistema.";
						//echo "<br>";
						
						
						// seleccionamos las fechas de las ultimas reservas canceladas
						$sql="SELECT * FROM reservas WHERE estado_reserva = -1 AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_canceladas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count3 = mysql_num_rows($registros);
						$j = 0;
						if ($count3 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha3[$j] = $reg['fecha_reserva'];
							   $hora3[$j] = $reg['horario_reserva'];
							   $user_id3[$j] = $reg['user_id'];
							   $curso_id3[$j] = $reg['curso_id'];
							   $numero_pod3[$j] = $reg['num_POD'];
							   $estadoreserva3[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas CANCELADAS";
						//if ($count3 > 0)
						//{
						//	for ($i=0; $i<count($fecha3); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas canceladas en el sistema.";
						//echo "<br>";
						
						
						// seleccionamos las reservas que se estan ejecutando ahora mismo
						$sql="SELECT * FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno' AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str) ORDER BY num_POD ASC";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count4 = mysql_num_rows($registros);
						$j = 0;  
						if ($count4 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha4[$j] = $reg['fecha_reserva'];
							   $hora4[$j] = $reg['horario_reserva'];
							   $user_id4[$j] = $reg['user_id'];
							   $curso_id4[$j] = $reg['curso_id'];
							   $numero_pod4[$j] = $reg['num_POD'];
							   $estadoreserva4[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas EN EJECUCION ahora";
						//if ($count4 > 0)
						//{
						//	for ($i=0; $i<count($fecha4); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- POD #$numero_pod4[$i] para el usuario $user_id4[$i] con el curso $curso_id4[$i].";
						//		echo "  ($fecha4[$i] a las $hora4[$i]) ";
						//		if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; 
						//	}
						//}else
						//	echo "<br>No hay reservas en ejecucion en el sistema.";
						//echo "<br>";	
						
					}
					else if ($ultimasreservasID == 1)     //solo las recientes
					{
						// seleccionamos las fechas de las ultimas reservas ejecutadas
						$sql="SELECT * FROM reservas WHERE estado_reserva = 1 AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_ejecutadas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count2 = mysql_num_rows($registros);
						$j = 0;
						if ($count2 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha2[$j] = $reg['fecha_reserva'];
							   $hora2[$j] = $reg['horario_reserva'];
							   $user_id2[$j] = $reg['user_id'];
							   $curso_id2[$j] = $reg['curso_id'];
							   $numero_pod2[$j] = $reg['num_POD'];
							   $estadoreserva2[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas APROVECHADAS";
						//if ($count2 > 0)
						//{
						//	for ($i=0; $i<count($fecha2); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas ejecutadas en el sistema.";
						//echo "<br>";
						
						
						// seleccionamos las fechas de las ultimas reservas canceladas
						$sql="SELECT * FROM reservas WHERE estado_reserva = -1 AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_canceladas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count3 = mysql_num_rows($registros);
						$j = 0;
						if ($count3 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha3[$j] = $reg['fecha_reserva'];
							   $hora3[$j] = $reg['horario_reserva'];
							   $user_id3[$j] = $reg['user_id'];
							   $curso_id3[$j] = $reg['curso_id'];
							   $numero_pod3[$j] = $reg['num_POD'];
							   $estadoreserva3[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas CANCELADAS";
						//if ($count3 > 0)
						//{
						//	for ($i=0; $i<count($fecha3); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas canceladas en el sistema.";
						//echo "<br>";			

					}
					else  if ($ultimasreservasID == 2)   // if ($ultimasreservasID == 2)    //solo las pendientes
					{
						// seleccionamos las fechas de las proximas reservas
						//$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND NOT (fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno_actual') AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count1 = mysql_num_rows($registros);
						$j = 0;
						if ($count1 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha1[$j] = $reg['fecha_reserva'];
							   $hora1[$j] = $reg['horario_reserva'];
							   $user_id1[$j] = $reg['user_id'];
							   $curso_id1[$j] = $reg['curso_id'];
							   $numero_pod1[$j] = $reg['num_POD'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas PENDIENTES";
						//if ($count1 > 0)
						//{
						//	for ($i=0; $i<count($fecha1); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].";
						//	}
						//}
						//else
						//{
						//	if ($estadoID == 2)   //en un curso pasado no puede haber reservas futuras
						//	{
						//		if ($cursoID == 0)
						//			echo "<br>cursos Pasados";
						//		else  //($cursoID > 0)
						//			echo "<br>Curso Pasado";
						//		
						//		echo " - No puede haber reservas activas en el sistema";
						//	}
						//	else
						//	{
						//		echo "<br>No hay reservas pendientes en el sistema.";
						//	}
						//}
						//echo "<br>";

					}
					else   // if ($ultimasreservasID == 3)    //solo las del turno actual
					{													
						// seleccionamos las reservas que se estan ejecutando ahora mismo
						$sql="SELECT * FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno' AND curso_id IN ($cursos_str) AND user_id IN ($usuarios_str) ORDER BY num_POD ASC";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count4 = mysql_num_rows($registros);
						$j = 0;  
						if ($count4 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha4[$j] = $reg['fecha_reserva'];
							   $hora4[$j] = $reg['horario_reserva'];
							   $user_id4[$j] = $reg['user_id'];
							   $curso_id4[$j] = $reg['curso_id'];
							   $numero_pod4[$j] = $reg['num_POD'];
							   $estadoreserva4[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas EN EJECUCION ahora";
						//if ($count4 > 0)
						//{
						//	for ($i=0; $i<count($fecha4); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- POD #$numero_pod4[$i] para el usuario $user_id4[$i] con el curso $curso_id4[$i].";
						//		echo "  ($fecha4[$i] a las $hora4[$i]) ";
						//		if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; 
						//	}
						//}else
						//	echo "<br>No hay reservas en ejecucion en el sistema.";
						//echo "<br>";	
						
					}
				}
				
			}
			else if ($cursoID == 0)     //un grupo de cursos, con el mismo alumno   ($usuarioID > 0)
			{
			
				if ($estadoID == 0)    //todos los cursos: activos y pasados
				{
					//pasamos los codigos de usuario almacenados en el array $usuarios a la cadena $usuarios_str
					$cursos_str = implode(",", $cursos); 
					
					//echo "<br>usuarios_str = $usuarios_str";

					
					if ($ultimasreservasID == 0)     //reservas recientes y pendientes
					{
						// seleccionamos las fechas de las proximas reservas
						//$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND curso_id IN ($cursos_str) AND user_id = $usuarioID ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND NOT (fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno_actual') AND curso_id IN ($cursos_str) AND user_id = $usuarioID ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count1 = mysql_num_rows($registros);
						$j = 0;
						if ($count1 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha1[$j] = $reg['fecha_reserva'];
							   $hora1[$j] = $reg['horario_reserva'];
							   $user_id1[$j] = $reg['user_id'];
							   $curso_id1[$j] = $reg['curso_id'];
							   $numero_pod1[$j] = $reg['num_POD'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas PENDIENTES";
						//if ($count1 > 0)
						//{
						//	for ($i=0; $i<count($fecha1); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].";
						//	}
						//}
						//else
						//{
						//	if ($estadoID == 2)   //en un curso pasado no puede haber reservas futuras
						//	{
						//		if ($cursoID == 0)
						//			echo "<br>cursos Pasados";
						//		else  //($cursoID > 0)
						//			echo "<br>Curso Pasado";
						//		
						//		echo " - No puede haber reservas activas en el sistema";
						//	}
						//	else
						//	{
						//		echo "<br>No hay reservas pendientes en el sistema.";
						//	}
						//}
						//echo "<br>";	

						
						// seleccionamos las fechas de las ultimas reservas ejecutadas
						$sql="SELECT * FROM reservas WHERE estado_reserva = 1 AND curso_id IN ($cursos_str) AND user_id = $usuarioID ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_ejecutadas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count2 = mysql_num_rows($registros);
						$j = 0;
						if ($count2 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha2[$j] = $reg['fecha_reserva'];
							   $hora2[$j] = $reg['horario_reserva'];
							   $user_id2[$j] = $reg['user_id'];
							   $curso_id2[$j] = $reg['curso_id'];
							   $numero_pod2[$j] = $reg['num_POD'];
							   $estadoreserva2[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas APROVECHADAS";
						//if ($count2 > 0)
						//{
						//	for ($i=0; $i<count($fecha2); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas ejecutadas en el sistema.";
						//echo "<br>";
						
						
						// seleccionamos las fechas de las ultimas reservas canceladas
						$sql="SELECT * FROM reservas WHERE estado_reserva = -1 AND curso_id IN ($cursos_str) AND user_id = $usuarioID ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_canceladas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count3 = mysql_num_rows($registros);
						$j = 0;
						if ($count3 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha3[$j] = $reg['fecha_reserva'];
							   $hora3[$j] = $reg['horario_reserva'];
							   $user_id3[$j] = $reg['user_id'];
							   $curso_id3[$j] = $reg['curso_id'];
							   $numero_pod3[$j] = $reg['num_POD'];
							   $estadoreserva3[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas CANCELADAS";
						//if ($count3 > 0)
						//{
						//	for ($i=0; $i<count($fecha3); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas canceladas en el sistema.";
						//echo "<br>";
						
						
						// seleccionamos las reservas que se estan ejecutando ahora mismo
						$sql="SELECT * FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno' AND curso_id IN ($cursos_str) AND user_id = $usuarioID ORDER BY num_POD ASC";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count4 = mysql_num_rows($registros);
						$j = 0;  
						if ($count4 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha4[$j] = $reg['fecha_reserva'];
							   $hora4[$j] = $reg['horario_reserva'];
							   $user_id4[$j] = $reg['user_id'];
							   $curso_id4[$j] = $reg['curso_id'];
							   $numero_pod4[$j] = $reg['num_POD'];
							   $estadoreserva4[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas EN EJECUCION ahora";
						//if ($count4 > 0)
						//{
						//	for ($i=0; $i<count($fecha4); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- POD #$numero_pod4[$i] para el usuario $user_id4[$i] con el curso $curso_id4[$i].";
						//		echo "  ($fecha4[$i] a las $hora4[$i]) ";
						//		if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; 
						//	}
						//}else
						//	echo "<br>No hay reservas en ejecucion en el sistema.";
						//echo "<br>";	
						
					}
					else if ($ultimasreservasID == 1)     //solo las recientes
					{
						// seleccionamos las fechas de las ultimas reservas ejecutadas
						$sql="SELECT * FROM reservas WHERE estado_reserva = 1 AND curso_id IN ($cursos_str) AND user_id = $usuarioID ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_ejecutadas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count2 = mysql_num_rows($registros);
						$j = 0;
						if ($count2 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha2[$j] = $reg['fecha_reserva'];
							   $hora2[$j] = $reg['horario_reserva'];
							   $user_id2[$j] = $reg['user_id'];
							   $curso_id2[$j] = $reg['curso_id'];
							   $numero_pod2[$j] = $reg['num_POD'];
							   $estadoreserva2[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas APROVECHADAS";
						//if ($count2 > 0)
						//{
						//	for ($i=0; $i<count($fecha2); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas ejecutadas en el sistema.";
						//echo "<br>";
						
						
						// seleccionamos las fechas de las ultimas reservas canceladas
						$sql="SELECT * FROM reservas WHERE estado_reserva = -1 AND curso_id IN ($cursos_str) AND user_id = $usuarioID ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_canceladas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count3 = mysql_num_rows($registros);
						$j = 0;
						if ($count3 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha3[$j] = $reg['fecha_reserva'];
							   $hora3[$j] = $reg['horario_reserva'];
							   $user_id3[$j] = $reg['user_id'];
							   $curso_id3[$j] = $reg['curso_id'];
							   $numero_pod3[$j] = $reg['num_POD'];
							   $estadoreserva3[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas CANCELADAS";
						//if ($count3 > 0)
						//{
						//	for ($i=0; $i<count($fecha3); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas canceladas en el sistema.";
						//echo "<br>";			

					}
					else  if ($ultimasreservasID == 2)   // if ($ultimasreservasID == 2)    //solo las pendientes
					{
						// seleccionamos las fechas de las proximas reservas
						//$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND curso_id IN ($cursos_str) AND user_id = $usuarioID ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND NOT (fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno_actual') AND curso_id IN ($cursos_str) AND user_id = $usuarioID ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count1 = mysql_num_rows($registros);
						$j = 0;
						if ($count1 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha1[$j] = $reg['fecha_reserva'];
							   $hora1[$j] = $reg['horario_reserva'];
							   $user_id1[$j] = $reg['user_id'];
							   $curso_id1[$j] = $reg['curso_id'];
							   $numero_pod1[$j] = $reg['num_POD'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas PENDIENTES";
						//if ($count1 > 0)
						//{
						//	for ($i=0; $i<count($fecha1); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].";
						//	}
						//}
						//else
						//{
						//	if ($estadoID == 2)   //en un curso pasado no puede haber reservas futuras
						//	{
						//		if ($cursoID == 0)
						//			echo "<br>cursos Pasados";
						//		else  //($cursoID > 0)
						//			echo "<br>Curso Pasado";
						//		
						//		echo " - No puede haber reservas activas en el sistema";
						//	}
						//	else
						//	{
						//		echo "<br>No hay reservas pendientes en el sistema.";
						//	}
						//}
						//echo "<br>";

					}
					else   // if ($ultimasreservasID == 3)    //solo las del turno actual
					{									
						// seleccionamos las reservas que se estan ejecutando ahora mismo
						$sql="SELECT * FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno' AND curso_id IN ($cursos_str) AND user_id = $usuarioID ORDER BY num_POD ASC";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count4 = mysql_num_rows($registros);
						$j = 0;  
						if ($count4 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha4[$j] = $reg['fecha_reserva'];
							   $hora4[$j] = $reg['horario_reserva'];
							   $user_id4[$j] = $reg['user_id'];
							   $curso_id4[$j] = $reg['curso_id'];
							   $numero_pod4[$j] = $reg['num_POD'];
							   $estadoreserva4[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas EN EJECUCION ahora";
						//if ($count4 > 0)
						//{
						//	for ($i=0; $i<count($fecha4); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- POD #$numero_pod4[$i] para el usuario $user_id4[$i] con el curso $curso_id4[$i].";
						//		echo "  ($fecha4[$i] a las $hora4[$i]) ";
						//		if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; 
						//	}
						//}else
						//	echo "<br>No hay reservas en ejecucion en el sistema.";
						//echo "<br>";	
						
					}
				}
				else if ($estadoID == 1)    //solo los cursos activos
				{
					//pasamos los codigos de usuario almacenados en el array $usuarios a la cadena $usuarios_str
					$cursos_str = implode(",", $cursos); 
					
					//echo "<br>cursos_str = $cursos_str";
				
					if ($ultimasreservasID == 0)     //reservas recientes y pendientes
					{
						// seleccionamos las fechas de las proximas reservas
						//$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND curso_id IN ($cursos_str) AND user_id = $usuarioID ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND NOT (fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno_actual') AND curso_id IN ($cursos_str) AND user_id = $usuarioID ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count1 = mysql_num_rows($registros);
						$j = 0;
						if ($count1 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha1[$j] = $reg['fecha_reserva'];
							   $hora1[$j] = $reg['horario_reserva'];
							   $user_id1[$j] = $reg['user_id'];
							   $curso_id1[$j] = $reg['curso_id'];
							   $numero_pod1[$j] = $reg['num_POD'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas PENDIENTES";
						//if ($count1 > 0)
						//{
						//	for ($i=0; $i<count($fecha1); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].";
						//	}
						//}
						//else
						//{
						//	if ($estadoID == 2)   //en un curso pasado no puede haber reservas futuras
						//	{
						//		if ($cursoID == 0)
						//			echo "<br>cursos Pasados";
						//		else  //($cursoID > 0)
						//			echo "<br>Curso Pasado";
						//		
						//		echo " - No puede haber reservas activas en el sistema";
						//	}
						//	else
						//	{
						//		echo "<br>No hay reservas pendientes en el sistema.";
						//	}
						//}
						//echo "<br>";	

						
						// seleccionamos las fechas de las ultimas reservas ejecutadas
						$sql="SELECT * FROM reservas WHERE estado_reserva = 1 AND curso_id IN ($cursos_str) AND user_id = $usuarioID ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_ejecutadas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count2 = mysql_num_rows($registros);
						$j = 0;
						if ($count2 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha2[$j] = $reg['fecha_reserva'];
							   $hora2[$j] = $reg['horario_reserva'];
							   $user_id2[$j] = $reg['user_id'];
							   $curso_id2[$j] = $reg['curso_id'];
							   $numero_pod2[$j] = $reg['num_POD'];
							   $estadoreserva2[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas APROVECHADAS";
						//if ($count2 > 0)
						//{
						//	for ($i=0; $i<count($fecha2); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas ejecutadas en el sistema.";
						//echo "<br>";
						
						
						// seleccionamos las fechas de las ultimas reservas canceladas
						$sql="SELECT * FROM reservas WHERE estado_reserva = -1 AND curso_id IN ($cursos_str) AND user_id = $usuarioID ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_canceladas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count3 = mysql_num_rows($registros);
						$j = 0;
						if ($count3 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha3[$j] = $reg['fecha_reserva'];
							   $hora3[$j] = $reg['horario_reserva'];
							   $user_id3[$j] = $reg['user_id'];
							   $curso_id3[$j] = $reg['curso_id'];
							   $numero_pod3[$j] = $reg['num_POD'];
							   $estadoreserva3[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas CANCELADAS";
						//if ($count3 > 0)
						//{
						//	for ($i=0; $i<count($fecha3); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas canceladas en el sistema.";
						//echo "<br>";
						
						
						// seleccionamos las reservas que se estan ejecutando ahora mismo
						$sql="SELECT * FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno' AND curso_id IN ($cursos_str) AND user_id = $usuarioID ORDER BY num_POD ASC";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count4 = mysql_num_rows($registros);
						$j = 0;  
						if ($count4 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha4[$j] = $reg['fecha_reserva'];
							   $hora4[$j] = $reg['horario_reserva'];
							   $user_id4[$j] = $reg['user_id'];
							   $curso_id4[$j] = $reg['curso_id'];
							   $numero_pod4[$j] = $reg['num_POD'];
							   $estadoreserva4[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas EN EJECUCION ahora";
						//if ($count4 > 0)
						//{
						//	for ($i=0; $i<count($fecha4); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- POD #$numero_pod4[$i] para el usuario $user_id4[$i] con el curso $curso_id4[$i].";
						//		echo "  ($fecha4[$i] a las $hora4[$i]) ";
						//		if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; 
						//	}
						//}else
						//	echo "<br>No hay reservas en ejecucion en el sistema.";
						//echo "<br>";	
						
					}
					else if ($ultimasreservasID == 1)     //solo las recientes
					{
						// seleccionamos las fechas de las ultimas reservas ejecutadas
						$sql="SELECT * FROM reservas WHERE estado_reserva = 1 AND curso_id IN ($cursos_str) AND user_id = $usuarioID ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_ejecutadas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count2 = mysql_num_rows($registros);
						$j = 0;
						if ($count2 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha2[$j] = $reg['fecha_reserva'];
							   $hora2[$j] = $reg['horario_reserva'];
							   $user_id2[$j] = $reg['user_id'];
							   $curso_id2[$j] = $reg['curso_id'];
							   $numero_pod2[$j] = $reg['num_POD'];
							   $estadoreserva2[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas APROVECHADAS";
						//if ($count2 > 0)
						//{
						//	for ($i=0; $i<count($fecha2); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas ejecutadas en el sistema.";
						//echo "<br>";
						
						
						// seleccionamos las fechas de las ultimas reservas canceladas
						$sql="SELECT * FROM reservas WHERE estado_reserva = -1 AND curso_id IN ($cursos_str) AND user_id = $usuarioID ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_canceladas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count3 = mysql_num_rows($registros);
						$j = 0;
						if ($count3 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha3[$j] = $reg['fecha_reserva'];
							   $hora3[$j] = $reg['horario_reserva'];
							   $user_id3[$j] = $reg['user_id'];
							   $curso_id3[$j] = $reg['curso_id'];
							   $numero_pod3[$j] = $reg['num_POD'];
							   $estadoreserva3[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas CANCELADAS";
						//if ($count3 > 0)
						//{
						//	for ($i=0; $i<count($fecha3); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas canceladas en el sistema.";
						//echo "<br>";			

					}
					else  if ($ultimasreservasID == 2)   // if ($ultimasreservasID == 2)    //solo las pendientes
					{
						// seleccionamos las fechas de las proximas reservas
						//$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND curso_id IN ($cursos_str) AND user_id = $usuarioID ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND NOT (fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno_actual') AND curso_id IN ($cursos_str) AND user_id = $usuarioID ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count1 = mysql_num_rows($registros);
						$j = 0;
						if ($count1 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha1[$j] = $reg['fecha_reserva'];
							   $hora1[$j] = $reg['horario_reserva'];
							   $user_id1[$j] = $reg['user_id'];
							   $curso_id1[$j] = $reg['curso_id'];
							   $numero_pod1[$j] = $reg['num_POD'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas PENDIENTES";
						//if ($count1 > 0)
						//{
						//	for ($i=0; $i<count($fecha1); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].";
						//	}
						//}
						//else
						//{
						//	if ($estadoID == 2)   //en un curso pasado no puede haber reservas futuras
						//	{
						//		if ($cursoID == 0)
						//			echo "<br>cursos Pasados";
						//		else  //($cursoID > 0)
						//			echo "<br>Curso Pasado";
						//		
						//		echo " - No puede haber reservas activas en el sistema";
						//	}
						//	else
						//	{
						//		echo "<br>No hay reservas pendientes en el sistema.";
						//	}
						//}
						//echo "<br>";

					}
					else   // if ($ultimasreservasID == 3)    //solo las del turno actual
					{									
						// seleccionamos las reservas que se estan ejecutando ahora mismo
						$sql="SELECT * FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno' AND curso_id IN ($cursos_str) AND user_id = $usuarioID ORDER BY num_POD ASC";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count4 = mysql_num_rows($registros);
						$j = 0;  
						if ($count4 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha4[$j] = $reg['fecha_reserva'];
							   $hora4[$j] = $reg['horario_reserva'];
							   $user_id4[$j] = $reg['user_id'];
							   $curso_id4[$j] = $reg['curso_id'];
							   $numero_pod4[$j] = $reg['num_POD'];
							   $estadoreserva4[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas EN EJECUCION ahora";
						//if ($count4 > 0)
						//{
						//	for ($i=0; $i<count($fecha4); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- POD #$numero_pod4[$i] para el usuario $user_id4[$i] con el curso $curso_id4[$i].";
						//		echo "  ($fecha4[$i] a las $hora4[$i]) ";
						//		if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; 
						//	}
						//}else
						//	echo "<br>No hay reservas en ejecucion en el sistema.";
						//echo "<br>";	
						
					}
				}
				else  	  ///if ($estadoID == 2)    //solo los cursos pasados
				{
					if ($ultimasreservasID == 0)     //reservas recientes y pendientes
					{
						// seleccionamos las fechas de las proximas reservas
						//$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND curso_id IN ($cursos_str) AND user_id = $usuarioID ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND NOT (fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno_actual') AND curso_id IN ($cursos_str) AND user_id = $usuarioID ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count1 = mysql_num_rows($registros);
						$j = 0;
						if ($count1 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha1[$j] = $reg['fecha_reserva'];
							   $hora1[$j] = $reg['horario_reserva'];
							   $user_id1[$j] = $reg['user_id'];
							   $curso_id1[$j] = $reg['curso_id'];
							   $numero_pod1[$j] = $reg['num_POD'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas PENDIENTES";
						//if ($count1 > 0)
						//{
						//	for ($i=0; $i<count($fecha1); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].";
						//	}
						//}
						//else
						//{
						//	if ($estadoID == 2)   //en un curso pasado no puede haber reservas futuras
						//	{
						//		if ($cursoID == 0)
						//			echo "<br>cursos Pasados";
						//		else  //($cursoID > 0)
						//			echo "<br>Curso Pasado";
						//		
						//		echo " - No puede haber reservas activas en el sistema";
						//	}
						//	else
						//	{
						//		echo "<br>No hay reservas pendientes en el sistema.";
						//	}
						//}
						//echo "<br>";	

						
						// seleccionamos las fechas de las ultimas reservas ejecutadas
						$sql="SELECT * FROM reservas WHERE estado_reserva = 1 AND curso_id IN ($cursos_str) AND user_id = $usuarioID ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_ejecutadas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count2 = mysql_num_rows($registros);
						$j = 0;
						if ($count2 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha2[$j] = $reg['fecha_reserva'];
							   $hora2[$j] = $reg['horario_reserva'];
							   $user_id2[$j] = $reg['user_id'];
							   $curso_id2[$j] = $reg['curso_id'];
							   $numero_pod2[$j] = $reg['num_POD'];
							   $estadoreserva2[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas APROVECHADAS";
						//if ($count2 > 0)
						//{
						//	for ($i=0; $i<count($fecha2); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas ejecutadas en el sistema.";
						//echo "<br>";
						
						
						// seleccionamos las fechas de las ultimas reservas canceladas
						$sql="SELECT * FROM reservas WHERE estado_reserva = -1 AND curso_id IN ($cursos_str) AND user_id = $usuarioID ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_canceladas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count3 = mysql_num_rows($registros);
						$j = 0;
						if ($count3 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha3[$j] = $reg['fecha_reserva'];
							   $hora3[$j] = $reg['horario_reserva'];
							   $user_id3[$j] = $reg['user_id'];
							   $curso_id3[$j] = $reg['curso_id'];
							   $numero_pod3[$j] = $reg['num_POD'];
							   $estadoreserva3[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas CANCELADAS";
						//if ($count3 > 0)
						//{
						//	for ($i=0; $i<count($fecha3); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas canceladas en el sistema.";
						//echo "<br>";
						
						
						// seleccionamos las reservas que se estan ejecutando ahora mismo
						$sql="SELECT * FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno' AND curso_id IN ($cursos_str) AND user_id = $usuarioID ORDER BY num_POD ASC";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count4 = mysql_num_rows($registros);
						$j = 0;  
						if ($count4 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha4[$j] = $reg['fecha_reserva'];
							   $hora4[$j] = $reg['horario_reserva'];
							   $user_id4[$j] = $reg['user_id'];
							   $curso_id4[$j] = $reg['curso_id'];
							   $numero_pod4[$j] = $reg['num_POD'];
							   $estadoreserva4[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas EN EJECUCION ahora";
						//if ($count4 > 0)
						//{
						//	for ($i=0; $i<count($fecha4); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- POD #$numero_pod4[$i] para el usuario $user_id4[$i] con el curso $curso_id4[$i].";
						//		echo "  ($fecha4[$i] a las $hora4[$i]) ";
						//		if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; 
						//	}
						//}else
						//	echo "<br>No hay reservas en ejecucion en el sistema.";
						//echo "<br>";	
						
					}
					else if ($ultimasreservasID == 1)     //solo las recientes
					{
						// seleccionamos las fechas de las ultimas reservas ejecutadas
						$sql="SELECT * FROM reservas WHERE estado_reserva = 1 AND curso_id IN ($cursos_str) AND user_id = $usuarioID ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_ejecutadas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count2 = mysql_num_rows($registros);
						$j = 0;
						if ($count2 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha2[$j] = $reg['fecha_reserva'];
							   $hora2[$j] = $reg['horario_reserva'];
							   $user_id2[$j] = $reg['user_id'];
							   $curso_id2[$j] = $reg['curso_id'];
							   $numero_pod2[$j] = $reg['num_POD'];
							   $estadoreserva2[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas APROVECHADAS";
						//if ($count2 > 0)
						//{
						//	for ($i=0; $i<count($fecha2); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas ejecutadas en el sistema.";
						//echo "<br>";
						
						
						// seleccionamos las fechas de las ultimas reservas canceladas
						$sql="SELECT * FROM reservas WHERE estado_reserva = -1 AND curso_id IN ($cursos_str) AND user_id = $usuarioID ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_canceladas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count3 = mysql_num_rows($registros);
						$j = 0;
						if ($count3 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha3[$j] = $reg['fecha_reserva'];
							   $hora3[$j] = $reg['horario_reserva'];
							   $user_id3[$j] = $reg['user_id'];
							   $curso_id3[$j] = $reg['curso_id'];
							   $numero_pod3[$j] = $reg['num_POD'];
							   $estadoreserva3[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas CANCELADAS";
						//if ($count3 > 0)
						//{
						//	for ($i=0; $i<count($fecha3); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].";
						//	}
						//}else
						//	echo "<br>No hay reservas canceladas en el sistema.";
						//echo "<br>";			

					}
					else  if ($ultimasreservasID == 2)   // if ($ultimasreservasID == 2)    //solo las pendientes
					{
						// seleccionamos las fechas de las proximas reservas
						//$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND curso_id IN ($cursos_str) AND user_id = $usuarioID ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND NOT (fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno_actual') AND curso_id IN ($cursos_str) AND user_id = $usuarioID ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count1 = mysql_num_rows($registros);
						$j = 0;
						if ($count1 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha1[$j] = $reg['fecha_reserva'];
							   $hora1[$j] = $reg['horario_reserva'];
							   $user_id1[$j] = $reg['user_id'];
							   $curso_id1[$j] = $reg['curso_id'];
							   $numero_pod1[$j] = $reg['num_POD'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas PENDIENTES";
						//if ($count1 > 0)
						//{
						//	for ($i=0; $i<count($fecha1); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].";
						//	}
						//}
						//else
						//{
						//	if ($estadoID == 2)   //en un curso pasado no puede haber reservas futuras
						//	{
						//		if ($cursoID == 0)
						//			echo "<br>cursos Pasados";
						//		else  //($cursoID > 0)
						//			echo "<br>Curso Pasado";
						//		
						//		echo " - No puede haber reservas activas en el sistema";
						//	}
						//	else
						//	{
						//		echo "<br>No hay reservas pendientes en el sistema.";
						//	}
						//}
						//echo "<br>";

					}
					else   // if ($ultimasreservasID == 3)    //solo las del turno actual
					{									
						// seleccionamos las reservas que se estan ejecutando ahora mismo
						$sql="SELECT * FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno' AND curso_id IN ($cursos_str) AND user_id = $usuarioID ORDER BY num_POD ASC";
						$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
						$count4 = mysql_num_rows($registros);
						$j = 0;  
						if ($count4 > 0){
						  while ($reg = mysql_fetch_array($registros)){
							   $fecha4[$j] = $reg['fecha_reserva'];
							   $hora4[$j] = $reg['horario_reserva'];
							   $user_id4[$j] = $reg['user_id'];
							   $curso_id4[$j] = $reg['curso_id'];
							   $numero_pod4[$j] = $reg['num_POD'];
							   $estadoreserva4[$j] = $reg['estado_reserva'];
							   $j++;
						  }
						}  
						mysql_free_result($registros);
						
						//echo "<br>reservas EN EJECUCION ahora";
						//if ($count4 > 0)
						//{
						//	for ($i=0; $i<count($fecha4); $i++)
						//	{
						//		$j = $i+1;
						//		echo "<br>$j.- POD #$numero_pod4[$i] para el usuario $user_id4[$i] con el curso $curso_id4[$i].";
						//		echo "  ($fecha4[$i] a las $hora4[$i]) ";
						//		if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; 
						//	}
						//}else
						//	echo "<br>No hay reservas en ejecucion en el sistema.";
						//echo "<br>";	
						
					}
				}
			}
			else if ($usuarioID == 0)    //un grupo de alumnos con el mismo curso    ($cursoID > 0)
			{
				//pasamos los codigos de usuario almacenados en el array $usuarios a la cadena $usuarios_str
				$usuarios_str = implode(",", $usuarios); 
				
				//echo "<br>usuarios_str = $usuarios_str";

				
				if ($ultimasreservasID == 0)     //reservas recientes y pendientes
				{
					// seleccionamos las fechas de las proximas reservas
					//$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND curso_id = $cursoID AND user_id IN ($usuarios_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
					$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND NOT (fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno_actual') AND curso_id = $cursoID AND user_id IN ($usuarios_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count1 = mysql_num_rows($registros);
					$j = 0;
					if ($count1 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha1[$j] = $reg['fecha_reserva'];
						   $hora1[$j] = $reg['horario_reserva'];
						   $user_id1[$j] = $reg['user_id'];
						   $curso_id1[$j] = $reg['curso_id'];
						   $numero_pod1[$j] = $reg['num_POD'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas PENDIENTES";
					//if ($count1 > 0)
					//{
					//	for ($i=0; $i<count($fecha1); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].";
					//	}
					//}
					//else
					//{
					//	if ($estadoID == 2)   //en un curso pasado no puede haber reservas futuras
					//	{
					//		if ($cursoID == 0)
					//			echo "<br>cursos Pasados";
					//		else  //($cursoID > 0)
					//			echo "<br>Curso Pasado";
					//		
					//		echo " - No puede haber reservas activas en el sistema";
					//	}
					//	else
					//	{
					//		echo "<br>No hay reservas pendientes en el sistema.";
					//	}
					//}
					//echo "<br>";	

					
					// seleccionamos las fechas de las ultimas reservas ejecutadas
					$sql="SELECT * FROM reservas WHERE estado_reserva = 1 AND curso_id = $cursoID AND user_id IN ($usuarios_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_ejecutadas";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count2 = mysql_num_rows($registros);
					$j = 0;
					if ($count2 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha2[$j] = $reg['fecha_reserva'];
						   $hora2[$j] = $reg['horario_reserva'];
						   $user_id2[$j] = $reg['user_id'];
						   $curso_id2[$j] = $reg['curso_id'];
						   $numero_pod2[$j] = $reg['num_POD'];
						   $estadoreserva2[$j] = $reg['estado_reserva'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas APROVECHADAS";
					//if ($count2 > 0)
					//{
					//	for ($i=0; $i<count($fecha2); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].";
					//	}
					//}else
					//	echo "<br>No hay reservas ejecutadas en el sistema.";
					//echo "<br>";
					
					
					// seleccionamos las fechas de las ultimas reservas canceladas
					$sql="SELECT * FROM reservas WHERE estado_reserva = -1 AND curso_id = $cursoID AND user_id IN ($usuarios_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_canceladas";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count3 = mysql_num_rows($registros);
					$j = 0;
					if ($count3 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha3[$j] = $reg['fecha_reserva'];
						   $hora3[$j] = $reg['horario_reserva'];
						   $user_id3[$j] = $reg['user_id'];
						   $curso_id3[$j] = $reg['curso_id'];
						   $numero_pod3[$j] = $reg['num_POD'];
						   $estadoreserva3[$j] = $reg['estado_reserva'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas CANCELADAS";
					//if ($count3 > 0)
					//{
					//	for ($i=0; $i<count($fecha3); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].";
					//	}
					//}else
					//	echo "<br>No hay reservas canceladas en el sistema.";
					//echo "<br>";
					
					
					// seleccionamos las reservas que se estan ejecutando ahora mismo
					$sql="SELECT * FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno' AND curso_id = $cursoID AND user_id IN ($usuarios_str) ORDER BY num_POD ASC";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count4 = mysql_num_rows($registros);
					$j = 0;  
					if ($count4 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha4[$j] = $reg['fecha_reserva'];
						   $hora4[$j] = $reg['horario_reserva'];
						   $user_id4[$j] = $reg['user_id'];
						   $curso_id4[$j] = $reg['curso_id'];
						   $numero_pod4[$j] = $reg['num_POD'];
						   $estadoreserva4[$j] = $reg['estado_reserva'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas EN EJECUCION ahora";
					//if ($count4 > 0)
					//{
					//	for ($i=0; $i<count($fecha4); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- POD #$numero_pod4[$i] para el usuario $user_id4[$i] con el curso $curso_id4[$i].";
					//		echo "  ($fecha4[$i] a las $hora4[$i]) ";
					//		if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; 
					//	}
					//}else
					//	echo "<br>No hay reservas en ejecucion en el sistema.";
					//echo "<br>";	
					
				}
				else if ($ultimasreservasID == 1)     //solo las recientes
				{
					// seleccionamos las fechas de las ultimas reservas ejecutadas
					$sql="SELECT * FROM reservas WHERE estado_reserva = 1 AND curso_id = $cursoID AND user_id IN ($usuarios_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_ejecutadas";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count2 = mysql_num_rows($registros);
					$j = 0;
					if ($count2 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha2[$j] = $reg['fecha_reserva'];
						   $hora2[$j] = $reg['horario_reserva'];
						   $user_id2[$j] = $reg['user_id'];
						   $curso_id2[$j] = $reg['curso_id'];
						   $numero_pod2[$j] = $reg['num_POD'];
						   $estadoreserva2[$j] = $reg['estado_reserva'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas APROVECHADAS";
					//if ($count2 > 0)
					//{
					//	for ($i=0; $i<count($fecha2); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].";
					//	}
					//}else
					//	echo "<br>No hay reservas ejecutadas en el sistema.";
					//echo "<br>";
					
					
					// seleccionamos las fechas de las ultimas reservas canceladas
					$sql="SELECT * FROM reservas WHERE estado_reserva = -1 AND curso_id = $cursoID AND user_id IN ($usuarios_str) ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_canceladas";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count3 = mysql_num_rows($registros);
					$j = 0;
					if ($count3 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha3[$j] = $reg['fecha_reserva'];
						   $hora3[$j] = $reg['horario_reserva'];
						   $user_id3[$j] = $reg['user_id'];
						   $curso_id3[$j] = $reg['curso_id'];
						   $numero_pod3[$j] = $reg['num_POD'];
						   $estadoreserva3[$j] = $reg['estado_reserva'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas CANCELADAS";
					//if ($count3 > 0)
					//{
					//	for ($i=0; $i<count($fecha3); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].";
					//	}
					//}else
					//	echo "<br>No hay reservas canceladas en el sistema.";
					//echo "<br>";			

				}
				else  if ($ultimasreservasID == 2)   // if ($ultimasreservasID == 2)    //solo las pendientes
				{
					// seleccionamos las fechas de las proximas reservas
					//$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND curso_id = $cursoID AND user_id IN ($usuarios_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
					$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND NOT (fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno_actual') AND curso_id = $cursoID AND user_id IN ($usuarios_str) ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count1 = mysql_num_rows($registros);
					$j = 0;
					if ($count1 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha1[$j] = $reg['fecha_reserva'];
						   $hora1[$j] = $reg['horario_reserva'];
						   $user_id1[$j] = $reg['user_id'];
						   $curso_id1[$j] = $reg['curso_id'];
						   $numero_pod1[$j] = $reg['num_POD'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas PENDIENTES";
					//if ($count1 > 0)
					//{
					//	for ($i=0; $i<count($fecha1); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].";
					//	}
					//}
					//else
					//{
					//	if ($estadoID == 2)   //en un curso pasado no puede haber reservas futuras
					//	{
					//		if ($cursoID == 0)
					//			echo "<br>cursos Pasados";
					//		else  //($cursoID > 0)
					//			echo "<br>Curso Pasado";
					//		
					//		echo " - No puede haber reservas activas en el sistema";
					//	}
					//	else
					//	{
					//		echo "<br>No hay reservas pendientes en el sistema.";
					//	}
					//}
					//echo "<br>";
					
				}
				else   // if ($ultimasreservasID == 3)    //solo las del turno actual
				{										
					// seleccionamos las reservas que se estan ejecutando ahora mismo
					$sql="SELECT * FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno' AND curso_id = $cursoID AND user_id IN ($usuarios_str) ORDER BY num_POD ASC";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count4 = mysql_num_rows($registros);
					$j = 0;  
					if ($count4 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha4[$j] = $reg['fecha_reserva'];
						   $hora4[$j] = $reg['horario_reserva'];
						   $user_id4[$j] = $reg['user_id'];
						   $curso_id4[$j] = $reg['curso_id'];
						   $numero_pod4[$j] = $reg['num_POD'];
						   $estadoreserva4[$j] = $reg['estado_reserva'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas EN EJECUCION ahora";
					//if ($count4 > 0)
					//{
					//	for ($i=0; $i<count($fecha4); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- POD #$numero_pod4[$i] para el usuario $user_id4[$i] con el curso $curso_id4[$i].";
					//		echo "  ($fecha4[$i] a las $hora4[$i]) ";
					//		if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; 
					//	}
					//}else
					//	echo "<br>No hay reservas en ejecucion en el sistema.";
					//echo "<br>";	
					
				}				
			} 
			else        //un alumno particular dentro de un curso particular        ($usuarioID > 0 && $cursoID > 0)
			{
			
				if ($ultimasreservasID == 0)     //reservas recientes y pendientes
				{
					// seleccionamos las fechas de las proximas reservas
					//$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND curso_id = $cursoID AND user_id = $usuarioID ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
					$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND NOT (fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno_actual') AND curso_id = $cursoID AND user_id = $usuarioID ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count1 = mysql_num_rows($registros);
					$j = 0;
					if ($count1 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha1[$j] = $reg['fecha_reserva'];
						   $hora1[$j] = $reg['horario_reserva'];
						   $user_id1[$j] = $reg['user_id'];
						   $curso_id1[$j] = $reg['curso_id'];
						   $numero_pod1[$j] = $reg['num_POD'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas PENDIENTES";
					//if ($count1 > 0)
					//{
					//	for ($i=0; $i<count($fecha1); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].";
					//	}
					//}
					//else
					//{
					//	if ($estadoID == 2)   //en un curso pasado no puede haber reservas futuras
					//	{
					//		if ($cursoID == 0)
					//			echo "<br>cursos Pasados";
					//		else  //($cursoID > 0)
					//			echo "<br>Curso Pasado";
					//		
					//		echo " - No puede haber reservas activas en el sistema";
					//	}
					//	else
					//	{
					//		echo "<br>No hay reservas pendientes en el sistema.";
					//	}
					//}
					//echo "<br>";	

					
					// seleccionamos las fechas de las ultimas reservas ejecutadas
					$sql="SELECT * FROM reservas WHERE estado_reserva = 1 AND curso_id = $cursoID AND user_id = $usuarioID ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_ejecutadas";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count2 = mysql_num_rows($registros);
					$j = 0;
					if ($count2 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha2[$j] = $reg['fecha_reserva'];
						   $hora2[$j] = $reg['horario_reserva'];
						   $user_id2[$j] = $reg['user_id'];
						   $curso_id2[$j] = $reg['curso_id'];
						   $numero_pod2[$j] = $reg['num_POD'];
						   $estadoreserva2[$j] = $reg['estado_reserva'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas APROVECHADAS";
					//if ($count2 > 0)
					//{
					//	for ($i=0; $i<count($fecha2); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].";
					//	}
					//}else
					//	echo "<br>No hay reservas ejecutadas en el sistema.";
					//echo "<br>";
					
					
					// seleccionamos las fechas de las ultimas reservas canceladas
					$sql="SELECT * FROM reservas WHERE estado_reserva = -1 AND curso_id = $cursoID AND user_id = $usuarioID ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_canceladas";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count3 = mysql_num_rows($registros);
					$j = 0;
					if ($count3 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha3[$j] = $reg['fecha_reserva'];
						   $hora3[$j] = $reg['horario_reserva'];
						   $user_id3[$j] = $reg['user_id'];
						   $curso_id3[$j] = $reg['curso_id'];
						   $numero_pod3[$j] = $reg['num_POD'];
						   $estadoreserva3[$j] = $reg['estado_reserva'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas CANCELADAS";
					//if ($count3 > 0)
					//{
					//	for ($i=0; $i<count($fecha3); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].";
					//	}
					//}else
					//	echo "<br>No hay reservas canceladas en el sistema.";
					//echo "<br>";
					
					
					// seleccionamos las reservas que se estan ejecutando ahora mismo
					$sql="SELECT * FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno' AND curso_id = $cursoID AND user_id = $usuarioID ORDER BY num_POD ASC";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count4 = mysql_num_rows($registros);
					$j = 0;  
					if ($count4 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha4[$j] = $reg['fecha_reserva'];
						   $hora4[$j] = $reg['horario_reserva'];
						   $user_id4[$j] = $reg['user_id'];
						   $curso_id4[$j] = $reg['curso_id'];
						   $numero_pod4[$j] = $reg['num_POD'];
						   $estadoreserva4[$j] = $reg['estado_reserva'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas EN EJECUCION ahora";
					//if ($count4 > 0)
					//{
					//	for ($i=0; $i<count($fecha4); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- POD #$numero_pod4[$i] para el usuario $user_id4[$i] con el curso $curso_id4[$i].";
					//		echo "  ($fecha4[$i] a las $hora4[$i]) ";
					//		if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; 
					//	}
					//}else
					//	echo "<br>No hay reservas en ejecucion en el sistema.";
					//echo "<br>";	
					
				}
				else if ($ultimasreservasID == 1)     //solo las recientes
				{
					// seleccionamos las fechas de las ultimas reservas ejecutadas
					$sql="SELECT * FROM reservas WHERE estado_reserva = 1 AND curso_id = $cursoID AND user_id = $usuarioID ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_ejecutadas";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count2 = mysql_num_rows($registros);
					$j = 0;
					if ($count2 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha2[$j] = $reg['fecha_reserva'];
						   $hora2[$j] = $reg['horario_reserva'];
						   $user_id2[$j] = $reg['user_id'];
						   $curso_id2[$j] = $reg['curso_id'];
						   $numero_pod2[$j] = $reg['num_POD'];
						   $estadoreserva2[$j] = $reg['estado_reserva'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas APROVECHADAS";
					//if ($count2 > 0)
					//{
					//	for ($i=0; $i<count($fecha2); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].";
					//	}
					//}else
					//	echo "<br>No hay reservas ejecutadas en el sistema.";
					//echo "<br>";
					
					
					// seleccionamos las fechas de las ultimas reservas canceladas
					$sql="SELECT * FROM reservas WHERE estado_reserva = -1 AND curso_id = $cursoID AND user_id = $usuarioID ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_canceladas";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count3 = mysql_num_rows($registros);
					$j = 0;
					if ($count3 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha3[$j] = $reg['fecha_reserva'];
						   $hora3[$j] = $reg['horario_reserva'];
						   $user_id3[$j] = $reg['user_id'];
						   $curso_id3[$j] = $reg['curso_id'];
						   $numero_pod3[$j] = $reg['num_POD'];
						   $estadoreserva3[$j] = $reg['estado_reserva'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas CANCELADAS";
					//if ($count3 > 0)
					//{
					//	for ($i=0; $i<count($fecha3); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].";
					//	}
					//}else
					//	echo "<br>No hay reservas canceladas en el sistema.";
					//echo "<br>";			

				}
				else  if ($ultimasreservasID == 2)   // if ($ultimasreservasID == 2)    //solo las pendientes
				{
					// seleccionamos las fechas de las proximas reservas
					//$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND curso_id = $cursoID AND user_id = $usuarioID ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
					$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND NOT (fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno_actual') AND curso_id = $cursoID AND user_id = $usuarioID ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count1 = mysql_num_rows($registros);
					$j = 0;
					if ($count1 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha1[$j] = $reg['fecha_reserva'];
						   $hora1[$j] = $reg['horario_reserva'];
						   $user_id1[$j] = $reg['user_id'];
						   $curso_id1[$j] = $reg['curso_id'];
						   $numero_pod1[$j] = $reg['num_POD'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas PENDIENTES";
					//if ($count1 > 0)
					//{
					//	for ($i=0; $i<count($fecha1); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].";
					//	}
					//}
					//else
					//{
					//	if ($estadoID == 2)   //en un curso pasado no puede haber reservas futuras
					//	{
					//		if ($cursoID == 0)
					//			echo "<br>cursos Pasados";
					//		else  //($cursoID > 0)
					//			echo "<br>Curso Pasado";
					//		
					//		echo " - No puede haber reservas activas en el sistema";
					//	}
					//	else
					//	{
					//		echo "<br>No hay reservas pendientes en el sistema.";
					//	}
					//}
					//echo "<br>";

				}
				else   // if ($ultimasreservasID == 3)    //solo las del turno actual
				{										
					// seleccionamos las reservas que se estan ejecutando ahora mismo
					$sql="SELECT * FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno' AND curso_id = $cursoID AND user_id = $usuarioID ORDER BY num_POD ASC";
					$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
					$count4 = mysql_num_rows($registros);
					$j = 0;  
					if ($count4 > 0){
					  while ($reg = mysql_fetch_array($registros)){
						   $fecha4[$j] = $reg['fecha_reserva'];
						   $hora4[$j] = $reg['horario_reserva'];
						   $user_id4[$j] = $reg['user_id'];
						   $curso_id4[$j] = $reg['curso_id'];
						   $numero_pod4[$j] = $reg['num_POD'];
						   $estadoreserva4[$j] = $reg['estado_reserva'];
						   $j++;
					  }
					}  
					mysql_free_result($registros);
					
					//echo "<br>reservas EN EJECUCION ahora";
					//if ($count4 > 0)
					//{
					//	for ($i=0; $i<count($fecha4); $i++)
					//	{
					//		$j = $i+1;
					//		echo "<br>$j.- POD #$numero_pod4[$i] para el usuario $user_id4[$i] con el curso $curso_id4[$i].";
					//		echo "  ($fecha4[$i] a las $hora4[$i]) ";
					//		if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; 
					//	}
					//}else
					//	echo "<br>No hay reservas en ejecucion en el sistema.";
					//echo "<br>";	
					
				}
			
			}

		}

?>

        <input type="text" name="ultimasreservas" value=" &Uacute;ltimas reservas" 
 		id="ultimasreservas" style="width: 160px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:12pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  

		<center>
		    <table border="1" cellpadding="1" cellspacing="0" width="88%">
		   
<?php  		if ($ultimasreservasID==0 || $ultimasreservasID==3)
			{ 
			    if ($count4 > 0)
				    $altura4 = $count4;
			    else
				    $altura4 = 1;
?>
				<tr>
					<th width="18%" rowspan="<?php echo "$altura4"; ?>" bgcolor="lightcyan"> reservas EN EJECUCION</th>
<?php					
				
					if ($count4 > 0)
					{
						for ($i=0; $i<count($fecha4); $i++)
						{
							$j = $i+1;
							
							for ($k=0; $k<count($elenco_id_usuarios); $k++)
							   if ($user_id4[$i] == $elenco_id_usuarios[$k])	
							        $user4 = $k;

							for ($k=0; $k<count($elenco_id_cursos); $k++)
							   if ($curso_id4[$i] == $elenco_id_cursos[$k])
							        $course4 = $k;
									
							echo "<td width=\"70%\" bgcolor=\"#fcfcfc\" align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $j.- <u>AHORA</u> en el <i>POD #$numero_pod4[$i]</i> para el usuario <b><i>$elenco_usuarios[$user4]</i></b> con el curso <i>$elenco_cursos[$course4]</i>  ($fecha4[$i] a las $hora4[$i])";  if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; echo "</td></tr>";

							//echo "<td width=\"70%\"> &nbsp; $j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].</td></tr>";
						}
					}
					else
					{
						if ($estadoID == 2)   //en un curso pasado las reservas ejecutadas son de un tiempo atras
						{
							if ($cursoID == 0)
								echo "<td width=\"70%\" bgcolor=\"#fcfcfc\" align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; cursos Pasados";
							else  //($cursoID > 0)
								echo "<td width=\"70%\" bgcolor=\"#fcfcfc\" align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Curso Pasado";
							
							echo " - No puede haber reservas en ejecuci&oacute;n en el sistema</td></tr>";
						}
						else
						{
							echo "<td width=\"70%\" bgcolor=\"#fcfcfc\" align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Ahora mismo No hay reservas en ejecuci&oacute;n en el sistema</td></tr>";
						}
					}				
			?>		
				</tr>
<?php       }  ?>	

<?php       if ($ultimasreservasID==0) 
			{  ?>
			     <tr>
					<td colspan="2" height="10px"> </td>
				 </tr>
<?php		}  ?>		

<?php  		if ($ultimasreservasID==0 || $ultimasreservasID==2)
			{ 
			    if ($count1 > 0)
				    $altura1 = $count1;
			    else
				    $altura1 = 1;
?>
				<tr>
					<th width="18%" rowspan="<?php echo "$altura1"; ?>" bgcolor="lightcyan"> reservas PENDIENTES</th>
<?php					
					if ($count1 > 0)
					{
						for ($i=0; $i<count($fecha1); $i++)
						{
							$j = $i+1;  
							for ($k=0; $k<count($elenco_id_usuarios); $k++)
							    if ($user_id1[$i] == $elenco_id_usuarios[$k])	
							         $user1 = $k; 
									 
							for ($k=0; $k<count($elenco_id_cursos); $k++)
							   if ($curso_id1[$i] == $elenco_id_cursos[$k])
							        $course1 = $k;
							
							echo "<td width=\"70%\" bgcolor=\"#fcfcfc\" align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $j.- $fecha1[$i] a las $hora1[$i] en el <i>POD #$numero_pod1[$i]</i> para el usuario <b><i>$elenco_usuarios[$user1]</i></b> con el curso <i>$elenco_cursos[$course1]</i> </td></tr>";

							//echo "<td width=\"70%\"> &nbsp; $j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].</td></tr>";
						}
					}
					else
					{
						if ($estadoID == 2)   //en un curso pasado no puede haber reservas futuras
						{
							if ($cursoID == 0)
								echo "<td width=\"70%\" bgcolor=\"#fcfcfc\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; cursos Pasados";
							else  //($cursoID > 0)
								echo "<td width=\"70%\" bgcolor=\"#fcfcfc\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Curso Pasado";
							
							echo " - No puede haber reservas activas en el sistema</td></<tr>";
						}
						else
						{
							echo "<td width=\"70%\" bgcolor=\"#fcfcfc\" align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; No hay reservas pendientes en el sistema</td></tr>";
						}
					}				
			?>		
				</tr>
<?php       }  ?>	

<?php       if ($ultimasreservasID==0) 
			{  ?>
			     <tr>
					<td colspan="2" height="10px"> </td>
				 </tr>
<?php		}  ?>		

<?php  		if ($ultimasreservasID==0 || $ultimasreservasID==1)
			{ 
			    if ($count2 > 0)
				    $altura2 = $count2;
			    else
				    $altura2 = 1;
?>
				<tr>
					<th width="18%" rowspan="<?php echo "$altura2"; ?>" bgcolor="lightcyan"> reservas APROVECHADAS</th>
<?php					
				
					if ($count2 > 0)
					{
						for ($i=0; $i<count($fecha2); $i++)
						{
							$j = $i+1;
							
							for ($k=0; $k<count($elenco_id_usuarios); $k++)
							   if ($user_id2[$i] == $elenco_id_usuarios[$k])	
							        $user2 = $k;

							for ($k=0; $k<count($elenco_id_cursos); $k++)
							   if ($curso_id2[$i] == $elenco_id_cursos[$k])
							        $course2 = $k;
									
							echo "<td width=\"70%\" bgcolor=\"#fcfcfc\" align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $j.- $fecha2[$i] a las $hora2[$i] en el <i>POD #$numero_pod2[$i]</i> para el usuario <b><i>$elenco_usuarios[$user2]</i></b> con el curso <i>$elenco_cursos[$course2]</i> </td></tr>";

							//echo "<td width=\"70%\"> &nbsp; $j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].</td></tr>";
						}
					}
					else
					{
						if ($estadoID == 2)   //en un curso pasado las reservas ejecutadas son de un tiempo atras
						{
							if ($cursoID == 0)
								echo "<td width=\"70%\" bgcolor=\"#fcfcfc\" align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; cursos Pasados";
							else  //($cursoID > 0)
								echo "<td width=\"70%\" bgcolor=\"#fcfcfc\" align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Curso Pasado";
							
							echo " - Las &uacute;ltimas reservas ejecutadas corresponden a tiempo atr&aacute;s</td></tr>";
						}
						else
						{
							echo "<td width=\"70%\" bgcolor=\"#fcfcfc\" align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; No hay reservas ejecutadas en el sistema</td></tr>";
						}
					}				
			?>		
				</tr>
				
				<tr>
					<td colspan="2" height="10px"> </td>
				</tr>
				
<?php				
			    if ($count3 > 0)
				    $altura3 = $count3;
			    else
				    $altura3 = 1;
?>
				<tr>
					<th width="18%" rowspan="<?php echo "$altura3"; ?>" bgcolor="lightcyan"> reservas CANCELADAS</th>
<?php					
				
					if ($count3 > 0)
					{
						for ($i=0; $i<count($fecha3); $i++)
						{
							$j = $i+1;
							
							for ($k=0; $k<count($elenco_id_usuarios); $k++)
							   if ($user_id3[$i] == $elenco_id_usuarios[$k])	
							        $user3 = $k;

							for ($k=0; $k<count($elenco_id_cursos); $k++)
							   if ($curso_id3[$i] == $elenco_id_cursos[$k])
							        $course3 = $k;
									
							echo "<td width=\"75%\" bgcolor=\"#fcfcfc\" align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $j.- $fecha3[$i] a las $hora3[$i] en el <i>POD #$numero_pod3[$i]</i> para el usuario <b><i>$elenco_usuarios[$user3]</i></b> con el curso <i>$elenco_cursos[$course3]</i> </td></tr>";

							//echo "<td width=\"75%\"> &nbsp; $j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].</td></tr>";
						}
					}
					else
					{
						if ($estadoID == 2)   //en un curso pasado las reservas ejecutadas son de un tiempo atras
						{
							if ($cursoID == 0)
								echo "<td width=\"75%\" bgcolor=\"#fcfcfc\" align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; cursos Pasados";
							else  //($cursoID > 0)
								echo "<td width=\"75%\" bgcolor=\"#fcfcfc\" align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Curso Pasado";
							
							echo " - Las &uacute;ltimas reservas canceladas corresponden a tiempo atr&aacute;s</td></tr>";
						}
						else
						{
							echo "<td width=\"75%\" bgcolor=\"#fcfcfc\" align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; No hay reservas canceladas en el sistema</td>";
						}
					}				
			?>		
				</tr>				
				
				
<?php       }  ?>


		
			</table>
		</center>
		<br>

<?php
        
	
	}
	
	
		
	echo "<hr>";
	
	
	
	//****ESTADISTICAS GENERALES****//
	if ($stat_totales == 1)
	{		
	    ////numeros totales del sistema:
		////accesos, reservas, conexiones..., confirmaciones, anulaciones,....
		//echo "<br>Total accesos = $total_accesos";
		//echo "<br>Total reservas = $total_reservas";
		//echo "<br>Total conexiones = $total_conexiones";
		//echo "<br><br>";
	

		////// ESTADISTICAS POR POD

		//creamos la variable array_pods, para almacenar los diferentes numeros de pod
		for ($i=0; $i<$total_pods; $i++)
				$array_pods[$i] = $i + 1;
		
		// contamos el numero historico de reservas por cada pod
	    for ($i=0; $i<$total_pods; $i++)
	    {
		    $j = $i + 1;
			//$sql1="SELECT count(*) AS total FROM reservas WHERE num_POD = $j";
			$sql1="SELECT count(*) AS total FROM log_detalles WHERE confir_pod = $j";
			$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
			$cuenta1=mysql_fetch_assoc($registros1);
			//echo "<br>Total reservas POD #$j = ".$cuenta1['total'];
			$contador_reservas_pods[$i] = $cuenta1['total'];
	        mysql_free_result($registros1);
		}
		echo "<br>";
		
		
		// contamos el numero historico de conexiones por cada pod	
	    for ($i=0; $i<$total_pods; $i++)
	    {
			$j = $i + 1;
			//$sql1="SELECT count(*) AS total FROM logs WHERE num_pod_lab = $j";
			$sql1="SELECT count(*) AS total FROM log_detalles WHERE in_pod = $j";
			$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
			$cuenta1=mysql_fetch_assoc($registros1);
			//echo "<br>Total conexiones POD #$j = ".$cuenta1['total'];
			$contador_conexiones_pods[$i] = $cuenta1['total'];
	        mysql_free_result($registros1);
		}
		//echo "<br>";	
        //echo "<br>";
		

	    //*mantenimiento de Pods
		// contamos el numero historico de intervalos de mantenimiento en todos los pods
		$sql0="SELECT count(*) AS total FROM mantenimiento";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
		$cuenta0=mysql_fetch_assoc($registros0);
		//echo "<br>Total turnos de mantenimiento en todos los Pods = ".$cuenta0['total'];	
		$total_mantenimiento_pods = $cuenta0['total'];
		mysql_free_result($registros0);
		
		
		// contamos el numero historico de intervalos de mantenimiento por cada POD
	    for ($i=0; $i<$total_pods; $i++)
	    {
			$j = $i + 1;
			$sql1="SELECT count(*) AS total FROM mantenimiento WHERE num_POD_outage = $j";
			$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
			$cuenta1=mysql_fetch_assoc($registros1);
			//echo "<br>Total turnos de mantenimiento en POD #$j = ".$cuenta1['total'];
			$contador_mantenimiento_pods[$i] = $cuenta1['total'];
	        mysql_free_result($registros1);
		}
		//echo "<br>";
		//echo "<br>";
		

		////// ESTADISTICAS POR CURSO

		///datos por cursos
		$sql="SELECT * FROM cursos";
		$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
		$count = mysql_num_rows($registros);
		$j = 0;
		if ($count > 0)
		{		
			while ($reg = mysql_fetch_array($registros))
			{
			   $array_cursos[$j] = $reg['curso_id'];
			   $array_nombre_cursos[$j] = $reg['nombre_curso'];
			   $j++;
			}		  
		}
		mysql_free_result($registros);

		
		// contamos el numero historico de accesos por cada curso
		for ($i=0; $i<count($array_cursos); $i++)
		{
			$sql1="SELECT count(*) AS total FROM logs WHERE curso_id = $array_cursos[$i]";
			$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
			$cuenta1=mysql_fetch_assoc($registros1);
			//echo "<br>Total accesos en el curso #$array_nombre_cursos[$i] = ".$cuenta1['total'];
			$contador_accesos_cursos[$i] = $cuenta1['total'];
			mysql_free_result($registros1);
		}		
		//echo "<br>";

		// contamos el numero historico de reservas por cada curso
		for ($i=0; $i<count($array_cursos); $i++)
		{
			//$sql1="SELECT count(*) AS total FROM reservas WHERE curso_id = $array_cursos[$i]";
			$sql1="SELECT count(*) AS total FROM log_detalles WHERE confir_resv IS NOT NULL AND curso_id = $array_cursos[$i]";
			$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
			$cuenta1=mysql_fetch_assoc($registros1);
			//echo "<br>Total reservas en el curso $array_nombre_cursos[$i] = ".$cuenta1['total'];
			$contador_reservas_cursos[$i] = $cuenta1['total'];
			mysql_free_result($registros1);
		}
		//echo "<br>";		

		// contamos el numero historico de conexiones por cada curso
		for ($i=0; $i<count($array_cursos); $i++)
		{
			//$sql1="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND curso_id = $array_cursos[$i]";
			$sql1="SELECT count(*) AS total FROM log_detalles WHERE in_pod IS NOT NULL AND curso_id = $array_cursos[$i]";
			$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
			$cuenta1=mysql_fetch_assoc($registros1);
			//echo "<br>Total conexiones en el curso $array_nombre_cursos[$i] = ".$cuenta1['total'];
			$contador_conexiones_cursos[$i] = $cuenta1['total'];
			mysql_free_result($registros1);
		}
		//echo "<br>";
		//echo "<br>";		
		
		
		////// ESTADISTICAS POR USUARIO

		///datos por usuarios
		$sql="SELECT * FROM usuarios";
		$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
		$count = mysql_num_rows($registros);
		$j = 0;
		if ($count > 0)
		{		
			while ($reg = mysql_fetch_array($registros))
			{
			   $array_usuarios[$j] = $reg['user_id'];
			   $array_nombre_usuarios[$j] = $reg['username'];
			   $array_admins[$j] = $reg['admin'];
			   $j++;
			}		  
		}
		mysql_free_result($registros);

		
		// seleccionamos el numero historico de accesos por cada usuario
		for ($i=0; $i<count($array_usuarios); $i++)
		{
			$sql1="SELECT count(*) AS total FROM logs WHERE user_id = $array_usuarios[$i]";
			$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
			$cuenta1=mysql_fetch_assoc($registros1);
			//echo "<br>Total accesos del usuario $array_nombre_usuarios[$i] = ".$cuenta1['total'];
			$contador_accesos_usuarios[$i] = $cuenta1['total'];
			mysql_free_result($registros1);
		}	
		//echo "<br>";

		// contamos el numero historico de reservas por usuario
		for ($i=0; $i<count($array_usuarios); $i++)
		{
			//$sql1="SELECT count(*) AS total FROM reservas WHERE user_id = $array_usuarios[$i]";
			$sql1="SELECT count(*) AS total FROM log_detalles WHERE confir_resv IS NOT NULL AND user_id = $array_usuarios[$i]";
			$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
			$cuenta1=mysql_fetch_assoc($registros1);
			//echo "<br>Total reservas del usuario $array_nombre_usuarios[$i] = ".$cuenta1['total'];
			$contador_reservas_usuarios[$i] = $cuenta1['total'];
			mysql_free_result($registros1);
		}
		//echo "<br>";

		// contamos el numero historico de conexiones por usuario
		for ($i=0; $i<count($array_usuarios); $i++)
		{
			//$sql1="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND user_id = $array_usuarios[$i]";
			$sql1="SELECT count(*) AS total FROM log_detalles WHERE in_pod IS NOT NULL AND user_id = $array_usuarios[$i]";
			$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
			$cuenta1=mysql_fetch_assoc($registros1);
			//echo "<br>Total conexiones del usuario $array_nombre_usuarios[$i] = ".$cuenta1['total'];
			$contador_conexiones_usuarios[$i] = $cuenta1['total'];
			mysql_free_result($registros1);
		}
		//echo "<br>";			
		//echo "<br>";	


		//*accesos de Administrador
		// contamos el numero historico de accesos de administrador
		$sql0="SELECT count(*) AS total FROM logs WHERE user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 )";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
		$cuenta0=mysql_fetch_assoc($registros0);
		//echo "<br>Total accesos admin = ".$cuenta0['total'];	
		$total_accesos_admin = $cuenta0['total'];
		mysql_free_result($registros0);

		
		$sql1="SELECT user_id, username FROM usuarios WHERE admin = 1";
		$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
		$cuenta1=mysql_num_rows($registros1);
		//echo "<br>Num.administradores = $cuenta1";
		//si hay mas de un administrador
		if ($cuenta1 > 1)
		{
			$j = 0;
			while ($reg1 = mysql_fetch_array($registros1))
			{	
			   $administradores[$j] = $reg1['user_id'];
			   $nombre_administradores[$j] = $reg1['username'];
			   $j++;
			}
			mysql_free_result($registros1);
			
			// contamos el numero historico de accesos para cada administrador
			for ($i=0; $i<count($administradores); $i++)
			{		
				$sql2="SELECT count(*) AS total FROM logs WHERE user_id = $administradores[$i]";
				$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
				$cuenta2=mysql_fetch_assoc($registros2);
				$contador_accesos_admin[$i] = $cuenta2['total'];
				//echo "<br>total_accesos_admin $nombre_administradores[$i] = $contador_accesos_admin[$i]";
				mysql_free_result($registros2);
			}
		}
		//echo "<br>";
        //echo "<br>";

		
		//*Codigos de Salida
		// contamos el codigo de salida de cada log, para ver como se sale del sistema
		// NULL: final abrupto, 0: tiempo en POD cumplido, 1: salida voluntaria, -1: timeout cumplido, 2: salida voluntaria admin, -2: timeout admin
	    $exit_code[0] = 0;
	    $exit_code[1] = 1;
	    $exit_code[2] = 2;
	    $exit_code[3] = -1;
	    $exit_code[4] = -2;
	    $exit_code[5] = NULL;
	   
		for ($i=0; $i<5; $i++)
		{
			//echo "<br>exit_code = $exit_code[$i]";
			$sql1="SELECT count(*) AS total FROM logs WHERE codigo_salida = $exit_code[$i]";
			$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
			$cuenta1=mysql_fetch_assoc($registros1);
			//echo "<br>Total salidas con codigo de salida #$exit_code[$i] = ".$cuenta1['total'];
			$total_codigo_salida[$i] = $cuenta1['total'];
			mysql_free_result($registros1);
			
			for ($j=0; $j<count($array_usuarios); $j++)
			{
				$sql2="SELECT count(*) AS total FROM logs WHERE codigo_salida = $exit_code[$i] AND user_id = $array_usuarios[$j]";
				$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
				$cuenta2=mysql_fetch_assoc($registros2);
				//echo "<br> salidas del usuario $array_nombre_usuarios[$j] con codigo de salida #$exit_code[$i] = ".$cuenta2['total'];
				$contador_codigo_salida[$i][$j] = $cuenta2['total'];
				mysql_free_result($registros2);		
			}
		}
		//echo "<br>exit_code = NULL";
		$sql1="SELECT count(*) AS total FROM logs WHERE codigo_salida IS NULL";
		$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
		$cuenta1=mysql_fetch_assoc($registros1);
		//echo "<br>Total salidas con codigo de salida #NULL = ".$cuenta1['total'];
		$total_codigo_salida[5] = $cuenta1['total'];
		mysql_free_result($registros1);
		
		for ($j=0; $j<count($array_usuarios); $j++)
		{
			$sql2="SELECT count(*) AS total FROM logs WHERE codigo_salida IS NULL AND user_id = $array_usuarios[$j]";
			$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
			$cuenta2=mysql_fetch_assoc($registros2);
			//echo "<br> salidas del usuario $array_nombre_usuarios[$j] con codigo de salida #NULL = ".$cuenta2['total'];
			$contador_codigo_salida[5][$j] = $cuenta2['total'];
			mysql_free_result($registros2);
		}
		//echo "<br>";		
		//echo "<br>";

		
		////// ESTADISTICAS POR TIEMPO

		//buscamos el Lunes de la semana actual y el de la semana anterior
		$dia_sem = date('w', strtotime($hoy));
		//echo "<br>dia_sem = $dia_sem";

		$dia_semana = $dia_sem - 1;
		$Lunes_ref = date('Y-m-d',strtotime('-'.$dia_semana.' days', strtotime($f_ahora)));
		$Lunes_ant = date('Y-m-d',strtotime('-7 days', strtotime($Lunes_ref)));
		
		//*CONEXIONES AL LAB - SEMANA ACTUAL
		// contamos el numero historico de conexiones la semana actual
		//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ref'";
		$sql0="SELECT count(*) AS total FROM log_detalles WHERE in_pod IS NOT NULL AND confir_resv >= '$Lunes_ref'";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
		$cuenta0=mysql_fetch_assoc($registros0);
		//echo "<br>Total conexiones en la semana actual = ".$cuenta0['total'];	
		$total_conexiones_semana_actual = $cuenta0['total'];
		//echo "<br>total_conexiones_semana_actual = $total_conexiones_semana_actual";
		mysql_free_result($registros0);

		
		//*CONEXIONES AL LAB - SEMANA ANTERIOR
		// contamos el numero historico de conexiones la semana anterior
		//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant' AND entrada < '$Lunes_ref'";
		$sql0="SELECT count(*) AS total FROM log_detalles WHERE in_pod IS NOT NULL AND confir_resv >= '$Lunes_ant' AND confir_resv < '$Lunes_ref'";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
		$cuenta0=mysql_fetch_assoc($registros0);
		//echo "<br>Total conexiones en la semana anterior = ".$cuenta0['total'];	
		$total_conexiones_semana_anterior = $cuenta0['total'];
		//echo "<br>total_conexiones_semana_anterior = $total_conexiones_semana_anterior";
		mysql_free_result($registros0);		
		
		
		//*CONEXIONES AL LAB - HACE 2 SEMANAS
		// contamos el numero historico de conexiones la semana anterior
		$Lunes_ant_2 = date('Y-m-d',strtotime('-14 days', strtotime($Lunes_ref)));
		//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_2' AND entrada < '$Lunes_ant'";
		$sql0="SELECT count(*) AS total FROM log_detalles WHERE in_pod IS NOT NULL AND confir_resv >= '$Lunes_ant_2' AND confir_resv < '$Lunes_ant'";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
		$cuenta0=mysql_fetch_assoc($registros0);
		//echo "<br>Total conexiones hace 2 semanas = ".$cuenta0['total'];	
		$total_conexiones_semana_anterior_2 = $cuenta0['total'];
		//echo "<br>total_conexiones_semana_anterior_2 = $total_conexiones_semana_anterior_2";
		mysql_free_result($registros0);
		

		//*CONEXIONES AL LAB - HACE 3 SEMANAS
		// contamos el numero historico de conexiones la semana anterior
		$Lunes_ant_3 = date('Y-m-d',strtotime('-21 days', strtotime($Lunes_ref)));
		//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_3' AND entrada < '$Lunes_ant_2'";
		$sql0="SELECT count(*) AS total FROM log_detalles WHERE in_pod IS NOT NULL AND confir_resv >= '$Lunes_ant_3' AND confir_resv < '$Lunes_ant_2'";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
		$cuenta0=mysql_fetch_assoc($registros0);
		//echo "<br>Total conexiones hace 3 semanas = ".$cuenta0['total'];	
		$total_conexiones_semana_anterior_3 = $cuenta0['total'];
		//echo "<br>total_conexiones_semana_anterior_3 = $total_conexiones_semana_anterior_3";
		mysql_free_result($registros0);


		//*CONEXIONES AL LAB - HACE 4 SEMANAS
		// contamos el numero historico de conexiones la semana anterior
		$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
		//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada >= '$Lunes_ant_4' AND entrada < '$Lunes_ant_3'";
		$sql0="SELECT count(*) AS total FROM log_detalles WHERE in_pod IS NOT NULL AND confir_resv >= '$Lunes_ant_4' AND confir_resv < '$Lunes_ant_3'";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
		$cuenta0=mysql_fetch_assoc($registros0);
		//echo "<br>Total conexiones hace 4 semanas = ".$cuenta0['total'];	
		$total_conexiones_semana_anterior_4 = $cuenta0['total'];
		//echo "<br>total_conexiones_semana_anterior_4 = $total_conexiones_semana_anterior_4";
		mysql_free_result($registros0);

		
		//*CONEXIONES AL LAB - todas las ANTERIORES A HACE 4 SEMANAS 
		// contamos el numero historico de conexiones la semana anterior
		////$Lunes_ant_4 = date('Y-m-d',strtotime('-28 days', strtotime($Lunes_ref)));
		//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND entrada < '$Lunes_ant_4'";
		$sql0="SELECT count(*) AS total FROM log_detalles WHERE in_pod IS NOT NULL AND confir_resv < '$Lunes_ant_4'";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
		$cuenta0=mysql_fetch_assoc($registros0);
		//echo "<br>Total conexiones antiguas = ".$cuenta0['total'];	
		$total_conexiones_antiguas = $cuenta0['total'];
		//echo "<br>total_conexiones_antiguas = $total_conexiones_antiguas";
		mysql_free_result($registros0);

		
		//******************************************************//
		//*CONEXIONES AL LAB POR TIEMPOS DE ENTRADA AL LAB	
		
		// seleccionamos las fechas de entrada de todas las conexiones
		//$sql="SELECT entrada FROM logs WHERE num_pod_lab IS NOT NULL";
		$sql="SELECT confir_resv FROM log_detalles WHERE in_pod IS NOT NULL";
		$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
		$count = mysql_num_rows($registros);
		$j = 0;
		if ($count > 0){
		  while ($reg = mysql_fetch_array($registros)){
			   $conexiones[$j] = $reg['confir_resv'];
			   $j++;
		  }
		}  
		mysql_free_result($registros);
		
		//echo "<br><br>";
	
		//inicializamos variable de almacenamiento para los dias de la semana
		$TotalDiasSemana = array(0,0,0,0,0,0,0);
		$ArraySemana = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado');
		
		if (isset($conexiones))
		{
			// separamos las conexiones por dia de la semana
			for ($i=0; $i<count($conexiones); $i++)
			{
				$DayOfWeek = date( "w", strtotime($conexiones[$i]));
				switch ($DayOfWeek) {
					case 0:     //Domingo
						$TotalDiasSemana[$DayOfWeek]++;
						break;
					case 1:     //Lunes
						$TotalDiasSemana[$DayOfWeek]++;
						break;
					case 2:     //Martes
						$TotalDiasSemana[$DayOfWeek]++;
						break;
					case 3:     //Mi&eacute;rcoles
						$TotalDiasSemana[$DayOfWeek]++;
						break;
					case 4:     //Jueves
						$TotalDiasSemana[$DayOfWeek]++;
						break;
					case 5:     //Viernes
						$TotalDiasSemana[$DayOfWeek]++;
						break;
					case 6:     //S&aacute;bado
						$TotalDiasSemana[$DayOfWeek]++;
						break;					
				}  
			}
			
			////imprimimos el resultado de la clasificacion por dias de la semana
			//for ($i=0; $i<7; $i++)
			//{
			//	echo "$ArraySemana[$i] = $TotalDiasSemana[$i]  ";
			//}
		}
		//echo "<br><br>";
		
		
		//inicializamos variable de almacenamiento para las horas de conexion
		$TotalIntervalos = array(0,0,0,0,0,0,0,0,0,0,0,0);
		$ArrayIntervalos = array('0 - 2 h','2 - 4 h','4 - 6 h','6 - 8 h','8 - 10 h','10 - 12 h','12 - 14 h','14 - 16 h','16 - 18 h','18 - 20 h','20 - 22 h','22 - 24 h');
		
		if (isset($conexiones))
		{
		// separamos las conexiones por dia de la semana
			for ($i=0; $i<count($conexiones); $i++)
			{
				$Intervalo = date( "H", strtotime($conexiones[$i]));
				$indice = floor($Intervalo / 2);
				switch ($indice) {
					case 0:    
						$TotalIntervalos[$indice]++;
						break;
					case 1:    
						$TotalIntervalos[$indice]++;
						break;
					case 2:    
						$TotalIntervalos[$indice]++;
						break;
					case 3:    
						$TotalIntervalos[$indice]++;
						break;
					case 4:    
						$TotalIntervalos[$indice]++;
						break;
					case 5:    
						$TotalIntervalos[$indice]++;
						break;
					case 6:    
						$TotalIntervalos[$indice]++;
						break;
					case 7:    
						$TotalIntervalos[$indice]++;
						break;
					case 8:    
						$TotalIntervalos[$indice]++;
						break;
					case 9:    
						$TotalIntervalos[$indice]++;
						break;
					case 10:    
						$TotalIntervalos[$indice]++;
						break;
					case 11:    
						$TotalIntervalos[$indice]++;
						break;					
				}  
			}
			
			////imprimimos el resultado de la clasificacion por intervalos horarios diarios
			//for ($i=0; $i<12; $i++)
			//{
			//	echo "$ArrayIntervalos[$i] = $TotalIntervalos[$i]; &nbsp;&nbsp;";
			//}
		}
		//echo "<br>";	
		//echo "<br>";
		
		
		
		////// ESTADISTICAS POR ADMINISTRADORES

		// contamos el numero historico de accesos de administrador
		$sql0="SELECT count(*) AS total FROM logs WHERE user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 )";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
		$cuenta0=mysql_fetch_assoc($registros0);
		//echo "<br>Total accesos admin = ".$cuenta0['total'];	
		$total_accesos_admin = $cuenta0['total'];
		mysql_free_result($registros0);	

		// contamos el numero historico de reservas de administrador
		//$sql0="SELECT count(*) AS total FROM reservas WHERE user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 )";
		$sql0="SELECT count(*) AS total FROM log_detalles WHERE confir_resv IS NOT NULL AND user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 )";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
		$cuenta0=mysql_fetch_assoc($registros0);
		//echo "<br>Total reservas admin = ".$cuenta0['total'];	
		$total_reservas_admin = $cuenta0['total'];
		mysql_free_result($registros0);	
		
		// contamos el numero historico de conexiones de administrador  
		//$sql0="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 )";
		$sql0="SELECT count(*) AS total FROM log_detalles WHERE in_pod IS NOT NULL AND user_id IN ( SELECT user_id FROM usuarios WHERE admin = 1 )";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
		$cuenta0=mysql_fetch_assoc($registros0);
		//echo "<br>Total conexiones admin = ".$cuenta0['total'];	
		$total_conexiones_admin = $cuenta0['total'];
		mysql_free_result($registros0);	
		
		
		// contamos el numero historico de intervalos de mantenimiento 
		$sql0="SELECT count(*) AS total FROM mantenimiento";
		$registros0=mysql_query($sql0,$conexion) or die ("Problemas con el select  (sql0) ".mysql_error());
		$cuenta0=mysql_fetch_assoc($registros0);
		//echo "<br>Total intervalos de mantenimiento = ".$cuenta0['total'];	
		$total_outage = $cuenta0['total'];
		mysql_free_result($registros0);	
			
				
		// seleccionamos los user_id y username de todos los administradores
		$sql1="SELECT user_id, username FROM usuarios WHERE admin = 1";
		$registros1=mysql_query($sql1,$conexion) or die ("Problemas con el select  (sql1) ".mysql_error());
		$cuenta1=mysql_num_rows($registros1);
		$total_admins = $cuenta1;
		//echo "<br>Num.administradores = $total_admins";
		if ($cuenta1 > 0)
		{
			$j = 0;
			while ($reg1 = mysql_fetch_array($registros1))
			{	
			   $administradores[$j] = $reg1['user_id'];
			   $nombre_administradores[$j] = $reg1['username'];
			   $j++;
			}
			mysql_free_result($registros1);
		
			// contamos el numero historico de accesos para cada administrador
			for ($i=0; $i<count($administradores); $i++)
			{		
				$sql2="SELECT count(*) AS total FROM logs WHERE user_id = $administradores[$i]";
				$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
				$cuenta2=mysql_fetch_assoc($registros2);
				$contador_accesos_admin[$i] = $cuenta2['total'];
				mysql_free_result($registros2);
			}

			// contamos el numero historico de reservas para cada administrador
			for ($i=0; $i<count($administradores); $i++)
			{		
				//$sql2="SELECT count(*) AS total FROM reservas WHERE user_id = $administradores[$i]";
				$sql2="SELECT count(*) AS total FROM log_detalles WHERE confir_resv IS NOT NULL AND user_id = $administradores[$i]";
				$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
				$cuenta2=mysql_fetch_assoc($registros2);
				$contador_reservas_admin[$i] = $cuenta2['total'];
				mysql_free_result($registros2);
			}

			// contamos el numero historico de conexiones para cada administrador
			for ($i=0; $i<count($administradores); $i++)
			{		
				//$sql2="SELECT count(*) AS total FROM logs WHERE num_pod_lab IS NOT NULL AND user_id = $administradores[$i]";
				$sql2="SELECT count(*) AS total FROM log_detalles WHERE in_pod IS NOT NULL AND user_id = $administradores[$i]";
				$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
				$cuenta2=mysql_fetch_assoc($registros2);
				$contador_conexiones_admin[$i] = $cuenta2['total'];
				mysql_free_result($registros2);
			}
			
			// contamos el numero historico de intervalos de mantenimiento de cada administrador
			for ($i=0; $i<count($administradores); $i++)
			{		
				$sql2="SELECT count(*) AS total FROM mantenimiento WHERE admin_id = $administradores[$i]";
				$registros2=mysql_query($sql2,$conexion) or die ("Problemas con el select  (sql2) ".mysql_error());
				$cuenta2=mysql_fetch_assoc($registros2);
				$contador_outage[$i] = $cuenta2['total'];
				mysql_free_result($registros2);
			}	
		}
		else
			echo "<br>No hay administradores en el sistema.";

		
		
		//////ULTIMAS RESERVAS
		
		///nombres de los cursos implicados
		$sql="SELECT * FROM cursos";
		$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
		$count = mysql_num_rows($registros);
		$j = 0;
		if ($count > 0)
		{		
			while ($reg = mysql_fetch_array($registros))
			{
			   $elenco_id_cursos[$j] = $reg['curso_id'];
			   $elenco_cursos[$j] = $reg['nombre_curso'];
			   $j++;
			}		  
		}
		mysql_free_result($registros);

		///nombre de los usuarios de esos cursos implicados
		$sql="SELECT * FROM usuarios";
		$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
		$count = mysql_num_rows($registros);
		$j = 0;
		if ($count > 0)
		{		
			while ($reg = mysql_fetch_array($registros))
			{
			   $elenco_id_usuarios[$j] = $reg['user_id'];
			   $elenco_usuarios[$j] = $reg['username'];
			   $j++;
			}		  
		}
		mysql_free_result($registros);

		////fecha y hora del turno actual
		$hora_actual = date('H');
		$hora_par = (floor($hora_actual/2)*2);
		//echo "<br>hora_par = $hora_par";
		if ($hora_par < 10)
			$inicio_turno = "0".$hora_par.":00:00";
		else
			$inicio_turno = $hora_par.":00:00";
		//echo "<br>inicio_turno = $inicio_turno";
		$inicio_turno_actual = date('Y-m-d H:i:s',strtotime('+ '.$hora_par.' hours', strtotime($hoy)));
		

		// seleccionamos las fechas de las proximas reservas
		//$sql="SELECT * FROM reservas WHERE estado_reserva = 0 ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
		$sql="SELECT * FROM reservas WHERE estado_reserva = 0 AND NOT (fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno_actual') ORDER BY fecha_reserva ASC, horario_reserva ASC, num_POD ASC LIMIT $num_resv_activas";
		$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
		$count1 = mysql_num_rows($registros);
		$j = 0;
		if ($count1 > 0){
		  while ($reg = mysql_fetch_array($registros)){
			   $fecha1[$j] = $reg['fecha_reserva'];
			   $hora1[$j] = $reg['horario_reserva'];
			   $user_id1[$j] = $reg['user_id'];
			   $curso_id1[$j] = $reg['curso_id'];
			   $numero_pod1[$j] = $reg['num_POD'];
			   $j++;
		  }
		}  
		mysql_free_result($registros);
		
		//echo "<br>reservas PENDIENTES";
		//if ($count1 > 0)
		//{
		//	for ($i=0; $i<count($fecha1); $i++)
		//	{
		//		$j = $i+1;
		//		echo "<br>$j.- $fecha1[$i] a las $hora1[$i] en el POD #$numero_pod1[$i] para el usuario $user_id1[$i] con el curso $curso_id1[$i].";
		//	}
		//}
		//else
		//{
		//	echo "<br>No hay reservas pendientes en el sistema.";
		//}
		//echo "<br>";	

		
		// seleccionamos las fechas de las ultimas reservas ejecutadas
		$sql="SELECT * FROM reservas WHERE estado_reserva = 1 ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_ejecutadas";
		$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
		$count2 = mysql_num_rows($registros);
		$j = 0;
		if ($count2 > 0){
		  while ($reg = mysql_fetch_array($registros)){
			   $fecha2[$j] = $reg['fecha_reserva'];
			   $hora2[$j] = $reg['horario_reserva'];
			   $user_id2[$j] = $reg['user_id'];
			   $curso_id2[$j] = $reg['curso_id'];
			   $numero_pod2[$j] = $reg['num_POD'];
			   $estadoreserva2[$j] = $reg['estado_reserva'];
			   $j++;
		  }
		}  
		mysql_free_result($registros);
		
		//echo "<br>reservas EJECUTADAS";
		//if ($count2 > 0)
		//{
		//	for ($i=0; $i<count($fecha2); $i++)
		//	{
		//		$j = $i+1;
		//		echo "<br>$j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].";
		//	}
		//}
		//else
		//{
		//	echo "<br>No hay reservas ejecutadas en el sistema.";
		//}
		//echo "<br>";
		
		
		// seleccionamos las fechas de las ultimas reservas canceladas
		$sql="SELECT * FROM reservas WHERE estado_reserva = -1 ORDER BY fecha_reserva DESC, horario_reserva DESC, num_POD ASC LIMIT $num_resv_canceladas";
		$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
		$count3 = mysql_num_rows($registros);
		$j = 0;
		if ($count3 > 0){
		  while ($reg = mysql_fetch_array($registros)){
			   $fecha3[$j] = $reg['fecha_reserva'];
			   $hora3[$j] = $reg['horario_reserva'];
			   $user_id3[$j] = $reg['user_id'];
			   $curso_id3[$j] = $reg['curso_id'];
			   $numero_pod3[$j] = $reg['num_POD'];
			   $estadoreserva3[$j] = $reg['estado_reserva'];
			   $j++;
		  }
		}  
		mysql_free_result($registros);
		
		//echo "<br>reservas CANCELADAS";
		//if ($count3 > 0)
		//{
		//	for ($i=0; $i<count($fecha3); $i++)
		//	{
		//		$j = $i+1;
		//		echo "<br>$j.- $fecha3[$i] a las $hora3[$i] en el POD #$numero_pod3[$i] para el usuario $user_id3[$i] con el curso $curso_id3[$i].";
		//	}
		//}
		//else
		//{
		//	echo "<br>No hay reservas canceladas en el sistema.";
		//}
		//echo "<br>";
	

		//echo "<br>";		
		//echo "<br>";


		// seleccionamos las reservas que se estan ejecutando ahora mismo
		$sql="SELECT * FROM reservas WHERE fecha_reserva = '$hoy' AND horario_reserva = '$inicio_turno' ORDER BY num_POD ASC";
		$registros=mysql_query($sql,$conexion) or die ("Problemas con el select  (sql) ".mysql_error());
		$count4 = mysql_num_rows($registros);
		$j = 0;  
		if ($count4 > 0){
		  while ($reg = mysql_fetch_array($registros)){
			   $fecha4[$j] = $reg['fecha_reserva'];
			   $hora4[$j] = $reg['horario_reserva'];
			   $user_id4[$j] = $reg['user_id'];
			   $curso_id4[$j] = $reg['curso_id'];
			   $numero_pod4[$j] = $reg['num_POD'];
			   $estadoreserva4[$j] = $reg['estado_reserva'];
			   $j++;
		  }
		}  
		mysql_free_result($registros);
		
		//echo "<br>reservas EN EJECUCION ahora";
		//if ($count4 > 0)
		//{
		//	for ($i=0; $i<count($fecha4); $i++)
		//	{
		//		$j = $i+1;
		//		echo "<br>$j.- POD #$numero_pod4[$i] para el usuario $user_id4[$i] con el curso $curso_id4[$i].";
		//		echo "  ($fecha4[$i] a las $hora4[$i]) ";
		//		if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; 
		//	}
		//}else
		//	echo "<br>No hay reservas en ejecucion en el sistema.";
		//echo "<br>";		
		
?>
        <input type="text" name="estadisticasGenerales" value=" Estad&iacute;sticas Generales" 
 		id="estadisticasGenerales" style="width: 210px; background: coral; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:12pt; font-weight:bold; border: 1px solid #ccc; align:center;" readonly>	  

		<p>
		<center>
		    <table border="1" cellpadding="1" cellspacing="0" width="60%" class="thin sortable draggable">
				<tr>
					<th width="12%" bgcolor="#ccffff" align="left"> PODS: <?php echo "$total_pods"; ?> </th>
					<th bgcolor="ffe1ae" width="12%"> <?php echo "N&ordm; mantenimientos en el POD"; ?>  </th>
					<th bgcolor="ffe1ae" width="12%"> <?php echo "N&ordm; reservas en el POD"; ?>  </th>					
					<th bgcolor="ffe1ae" width="12%"> <?php echo "N&ordm; conexiones en el POD"; ?>  </th>					
				</tr>
	
<?php        	for ($i=0; $i<count($array_pods); $i++){    ?>

					<tr>
						<th bgcolor="d0f0c0" width="12%" align="center"> <?php echo "POD #$array_pods[$i]"; ?>  </th>
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php echo "$contador_mantenimiento_pods[$i]"; ?>  </td>	
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php echo "$contador_reservas_pods[$i]"; ?>  </td>
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php echo "$contador_conexiones_pods[$i]"; ?>  </td>	
					</tr>
					
					
<?php			}  ?>		

				</tr>
					<th width="12%" bgcolor="e8e8e8">  TOTAL </th>			
					<td width="12%" bgcolor="e8e8e8" align="right">  <?php $suma = array_sum($contador_mantenimiento_pods); echo " $suma"; ?>  </td>						
					<td width="12%" bgcolor="e8e8e8" align="right"> <?php $suma = array_sum($contador_reservas_pods); echo " $suma"; ?>  </td>
					<td width="12%" bgcolor="e8e8e8" align="right">  <?php $suma = array_sum($contador_conexiones_pods); echo " $suma"; ?>  </td>											
				</tr>

			</table>
		</center>
		<br>
		
		
        <center>
		   <table border="1" cellpadding="1" cellspacing="0" width="70%" class="thin sortable draggable">
				<tr>
					<th width="12%" bgcolor="#ccffff" align="left"> cursos: <?php echo "$num_cursos_totales"; ?> </th>
					<th bgcolor="ffe1ae" width="12%"> <?php echo "N&ordm; accesos al Curso"; ?>  </th>
					<th bgcolor="ffe1ae" width="12%"> <?php echo "N&ordm; reservas al Curso"; ?>  </th>					
					<th bgcolor="ffe1ae" width="12%"> <?php echo "N&ordm; conexiones al Curso"; ?>  </th>	
					<th bgcolor="ffe1ae" width="15%"> <?php echo "Curso Activo ahora"; ?>  </th>
				</tr>
	
<?php        	for ($i=0; $i<count($array_cursos); $i++){    ?>

					<tr>
						<th bgcolor="d0f0c0" width="12%" align="center"> <?php echo "$array_nombre_cursos[$i]"; ?>  </th>
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php echo "$contador_accesos_cursos[$i]"; ?>  </td>	
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php echo "$contador_reservas_cursos[$i]"; ?>  </td>
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php echo "$contador_conexiones_cursos[$i]"; ?>  </td>
						<td width="15%" bgcolor="#fcfcfc" align="right"> <?php if ($cursoID==0){ if ($estadoID==1) echo " SI"; else if ($estadoID==2) echo " NO"; else  if ($abierto0[$i]==1) echo " SI"; else echo " NO";} else{ $j=$cursoID-1; if($abierto0[$j]==1) echo " SI"; else echo " NO"; } ?>  </td>
					</tr>
					
					
<?php			}  ?>		

				</tr>
					<th width="12%" bgcolor="e8e8e8">  TOTAL </th>			
					<td width="12%" bgcolor="e8e8e8" align="right">  <?php $suma = array_sum($contador_accesos_cursos); echo " $suma"; ?>  </td>						
					<td width="12%" bgcolor="e8e8e8" align="right"> <?php $suma = array_sum($contador_reservas_cursos); echo " $suma"; ?>  </td>
					<td width="12%" bgcolor="e8e8e8" align="right">  <?php $suma = array_sum($contador_conexiones_cursos); echo " $suma"; ?>  </td>
					<td width="15%" bgcolor="e8e8e8" align="right">  <?php $j=0; for ($i=0;$i<count($abierto0);$i++) if($abierto0[$i]==1) $j++; echo " $j"; ?></td>
				</tr>	

			</table>
		</center>		
        <br>
		

        <center>
		   <table border="1" cellpadding="1" cellspacing="0" width="80%" class="thin sortable draggable">
				<tr>
					<th width="12%" bgcolor="#ccffff" align="left"> usuarios: <?php echo "$num_usuarios_totales"; ?> </th>
					<th bgcolor="ffe1ae" width="14%"> <?php echo "N&ordm; accesos del Usuario"; ?>  </th>
					<th bgcolor="ffe1ae" width="12%"> <?php echo "N&ordm; reservas del Usuario"; ?>  </th>					
					<th bgcolor="ffe1ae" width="12%"> <?php echo "N&ordm; conexiones del Usuario"; ?>  </th>	
					<th bgcolor="ffe1ae" width="12%"> <?php echo "Usuario Administrador"; ?>  </th>						
				</tr>
	
<?php        	for ($i=0; $i<count($array_usuarios); $i++){    ?>

					<tr>
						<th bgcolor="d0f0c0" width="14%" align="center"> <?php echo "$array_nombre_usuarios[$i]"; ?>  </th>
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php echo "$contador_accesos_usuarios[$i]"; ?>  </td>	
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php echo "$contador_reservas_usuarios[$i]"; ?>  </td>
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php echo "$contador_conexiones_usuarios[$i]"; ?>  </td>
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php if ($array_admins[$i]==1) echo " SI"; else echo " NO"; ?>  </td>				
					</tr>
					
					
<?php			}  ?>		

				</tr>
					<th width="14%" bgcolor="e8e8e8">  TOTAL </th>			
					<td width="12%" bgcolor="e8e8e8" align="right">  <?php $suma = array_sum($contador_accesos_usuarios); echo " $suma"; ?>  </td>						
					<td width="12%" bgcolor="e8e8e8" align="right"> <?php $suma = array_sum($contador_reservas_usuarios); echo " $suma"; ?>  </td>
					<td width="12%" bgcolor="e8e8e8" align="right">  <?php $suma = array_sum($contador_conexiones_usuarios); echo " $suma"; ?>  </td>
					<td width="12%" bgcolor="e8e8e8" align="right">  <?php $j=0; for ($i=0;$i<count($array_admins);$i++) if($array_admins[$i]==1) $j++; echo " $j"; ?></td>
				</tr>	

			</table>
		</center>
		<br>		
		

        <center>
		   <table border="1" cellpadding="1" cellspacing="0" width="85%" class="thin sortable draggable">
				<tr>
					<th width="15%" bgcolor="#ccffff"> C&Oacute;DIGOS DE SALIDA</th>
					<th bgcolor="ffe1ae" width="12%"> <?php echo "Tiempo POD <br><u>cumplido</u> <br>(0)"; ?>  </th>
					<th bgcolor="ffe1ae" width="12%"> <?php echo "Salida voluntaria <br><u>usuario</u> <br>(1)"; ?>  </th>					
					<th bgcolor="ffe1ae" width="12%"> <?php echo "Salida voluntaria <br><u>administrador</u> <br>(2)"; ?>  </th>	
					<th bgcolor="ffe1ae" width="12%"> <?php echo "Timeout <br><u>usuario</u> <br>(-1)"; ?>  </th>
					<th bgcolor="ffe1ae" width="12%"> <?php echo "Timeout <br><u>administrador</u> <br>(-2)"; ?>  </th>	
					<th bgcolor="ffe1ae" width="12%"> <?php echo "Salida <br><u>abrupta</u> <br>(NULL)"; ?>  </th>
				</tr>
	
<?php        	for ($j=0; $j<count($array_usuarios); $j++){    ?>
					
					<tr>
						<th bgcolor="d0f0c0" width="15%" align="center"> <?php echo "$array_nombre_usuarios[$j]"; ?>  </th>
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php $cuenta = $contador_codigo_salida[0][$j]; echo "$cuenta"; ?>  </td>	
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php $cuenta = $contador_codigo_salida[1][$j]; echo "$cuenta"; ?>  </td>
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php $cuenta = $contador_codigo_salida[2][$j]; echo "$cuenta"; ?>  </td>
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php $cuenta = $contador_codigo_salida[3][$j]; echo "$cuenta"; ?>  </td>
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php $cuenta = $contador_codigo_salida[4][$j]; echo "$cuenta"; ?>  </td>
						<td width="12%" bgcolor="#fcfcfc" align="right"> <?php $cuenta = $contador_codigo_salida[5][$j]; echo "$cuenta"; ?>  </td>						
					</tr>		
					
<?php			}  ?>	

				<tr>
					<th width="15%" bgcolor="e8e8e8">  TOTAL </th>			
					<td width="15%" bgcolor="e8e8e8" align="right">  <?php $suma = array_sum($contador_codigo_salida[0]); echo " $suma"; ?>  </td>						
					<td width="15%" bgcolor="e8e8e8" align="right">  <?php $suma = array_sum($contador_codigo_salida[1]); echo " $suma"; ?>  </td>
					<td width="15%" bgcolor="e8e8e8" align="right">  <?php $suma = array_sum($contador_codigo_salida[2]); echo " $suma"; ?>  </td>
					<td width="15%" bgcolor="e8e8e8" align="right">  <?php $suma = array_sum($contador_codigo_salida[3]); echo " $suma"; ?>  </td>
					<td width="15%" bgcolor="e8e8e8" align="right">  <?php $suma = array_sum($contador_codigo_salida[4]); echo " $suma"; ?>  </td>
					<td width="15%" bgcolor="e8e8e8" align="right">  <?php $suma = array_sum($contador_codigo_salida[5]); echo " $suma"; ?>  </td>
				</tr>	

			</table>
		</center>
		<br>


		<center>
		   <table border="1" cellpadding="1" cellspacing="0" width="95%" class="thin sortable draggable">
				<tr>
					<th width="11%" bgcolor="#ccffff"> CONEXIONES </th>
					<th bgcolor="ffe1ae" width="14%"> <?php echo "Semana Actual"; ?>  </th>
					<th bgcolor="ffe1ae" width="14%"> <?php echo "Semana Anterior"; ?>  </th>					
					<th bgcolor="ffe1ae" width="14%"> <?php echo "hace 2 Semanas"; ?>  </th>	
					<th bgcolor="ffe1ae" width="14%"> <?php echo "hace 3 Semanas"; ?>  </th>		
					<th bgcolor="ffe1ae" width="14%"> <?php echo "hace 4 Semanas"; ?>  </th>	
					<th bgcolor="ffe1ae" width="14%"> <?php echo "m&aacute;s de 1 mes"; ?>  </th>					
				</tr>

				<tr>
					<th bgcolor="d0f0c0" width="11%" align="center"> por SEMANAS  </th>
					<td width="14%" bgcolor="#fcfcfc" align="right"> <?php echo "$total_conexiones_semana_actual"; ?>  </td>	
					<td width="14%" bgcolor="#fcfcfc" align="right"> <?php echo "$total_conexiones_semana_anterior"; ?>  </td>
					<td width="14%" bgcolor="#fcfcfc" align="right"> <?php echo "$total_conexiones_semana_anterior_2"; ?>  </td>
					<td width="14%" bgcolor="#fcfcfc" align="right"> <?php echo "$total_conexiones_semana_anterior_3"; ?>  </td>		
					<td width="14%" bgcolor="#fcfcfc" align="right"> <?php echo "$total_conexiones_semana_anterior_4"; ?>  </td>	
					<td width="14%" bgcolor="#fcfcfc" align="right"> <?php echo "$total_conexiones_antiguas"; ?>  </td>					
				</tr>
							
			</table>
		</center>
		<br>		


		<center>
		   <table border="1" cellpadding="1" cellspacing="0" width="95%" class="thin sortable draggable">
				<tr>
					<th width="11%" bgcolor="#ccffff"> CONEXIONES </th>
					
<?php                   for ($i=1; $i<count($ArraySemana); $i++){  ?>
						<th bgcolor="ffe1ae" width="11%"> <?php echo "$ArraySemana[$i]"; ?>  </th>
<?php					}  ?>	
					<th bgcolor="ffe1ae" width="11%"> <?php echo "$ArraySemana[0]"; ?>  </th>
				</tr>

				<tr>
					<th bgcolor="d0f0c0" width="11%" align="center"> por DIAS  </th>
					
<?php                   for ($i=1; $i<count($ArraySemana); $i++){  ?>
						<td width="11%" bgcolor="#fcfcfc" align="right"> <?php echo "$TotalDiasSemana[$i]"; ?>  </td>	
<?php					}  ?>			
					<td width="11%" bgcolor="#fcfcfc" align="right"> <?php echo "$TotalDiasSemana[0]"; ?>  </td>
				</tr>
							
			</table>
		</center>
		<br>


		<center>
		   <table border="1" cellpadding="1" cellspacing="0" width="95%" class="thin sortable draggable">
				<tr>
					<th width="12%" bgcolor="#ccffff"> CONEXIONES </th>
					
	<?php                   for ($i=0; $i<count($ArrayIntervalos); $i++){  ?>
						<th bgcolor="ffe1ae" width="7%"> <?php echo "$ArrayIntervalos[$i]"; ?>  </th>
	<?php					}  ?>	
				</tr>

				<tr>
					<th bgcolor="d0f0c0" width="12%" align="center"> por HORAS  </th>
					
	<?php                   for ($i=0; $i<count($ArrayIntervalos); $i++){  ?>
						<td width="7%" bgcolor="#fcfcfc" align="right"> <?php echo "$TotalIntervalos[$i]"; ?>  </td>	
	<?php					}  ?>			
				</tr>
							
			</table>
		</center>
		<br>

        <center>
		   <table border="1" cellpadding="1" cellspacing="0" width="90%" class="thin sortable draggable">
				<tr>
					<th width="17%" bgcolor="#ccffff"> ADMINISTRADORES: <?php echo "$numero_total_admins"; ?> </th>
					<th bgcolor="ffe1ae" width="17%"> <?php echo "N&ordm; de accesos de Administrador"; ?>  </th>
					<th bgcolor="ffe1ae" width="17%"> <?php echo "N&ordm; de reservas de Administrador"; ?>  </th>
					<th bgcolor="ffe1ae" width="18%"> <?php echo "N&ordm; de conexiones de Administrador"; ?>  </th>
					<th bgcolor="ffe1ae" width="20%"> <?php echo "N&ordm; intervalos de mantenimiento"; ?>  </th>
				</tr>
	
<?php        	for ($i=0; $i<count($administradores); $i++){    ?>

					<tr>					
						<th bgcolor="d0f0c0" width="17%" align="center"> <?php echo "$nombre_administradores[$i]"; ?>  </th>
						<td width="17%" bgcolor="#fcfcfc" align="right"> <?php echo "$contador_accesos_admin[$i]"; ?>  </td>
						<td width="17%" bgcolor="#fcfcfc" align="right"> <?php echo "$contador_reservas_admin[$i]"; ?>  </td>
						<td width="18%" bgcolor="#fcfcfc" align="right"> <?php echo "$contador_conexiones_admin[$i]"; ?>  </td>						
						<td width="20%" bgcolor="#fcfcfc" align="right"> <b> <?php echo "$contador_outage[$i]"; ?> </b> </td>						
					</tr>		
					
<?php			}  ?>	

				</tr>
					<th width="17%" bgcolor="e8e8e8">  TOTAL </th>		
					<td width="17%" bgcolor="e8e8e8" align="right">  <?php echo " $total_accesos_admin"; ?>  </td>
					<td width="17%" bgcolor="e8e8e8" align="right">  <?php echo " $total_reservas_admin"; ?>  </td>
					<td width="18%" bgcolor="e8e8e8" align="right">  <?php echo " $total_conexiones_admin"; ?>  </td>
					<td width="20%" bgcolor="e8e8e8" align="right"> <b> <?php echo " $total_outage"; ?> </b> </td>
				</tr>	

			</table>
		</center>
		<br>		
		

		<center>
		    <table border="1" cellpadding="1" cellspacing="0" width="68%"> 
<?php
			    if ($count4 > 0)
				    $altura4 = $count4;
			    else
				    $altura4 = 1;
?>
				<tr>
					<th width="18%" rowspan="<?php echo "$altura4"; ?>" bgcolor="lightcyan"> reservas EN EJECUCION</th>
<?php					
				
					if ($count4 > 0)
					{
						for ($i=0; $i<count($fecha4); $i++)
						{
							$j = $i+1;
							
							for ($k=0; $k<count($elenco_id_usuarios); $k++)
							   if ($user_id4[$i] == $elenco_id_usuarios[$k])	
							        $user4 = $k;

							for ($k=0; $k<count($elenco_id_cursos); $k++)
							   if ($curso_id4[$i] == $elenco_id_cursos[$k])
							        $course4 = $k;
									
							echo "<td width=\"50%\" bgcolor=\"#fcfcfc\" align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $j.- <u>AHORA</u> en el <i>POD #$numero_pod4[$i]</i> para el usuario <b><i>$elenco_usuarios[$user4]</i></b> con el curso <i>$elenco_cursos[$course4]</i>  ($fecha4[$i] a las $hora4[$i])";  if ($estadoreserva4[$i]==0) echo " --> EN ESPERA"; else echo " --> EN USO"; echo "</td></tr>";

							//echo "<td width=\"50%\"> &nbsp; $j.- $fecha2[$i] a las $hora2[$i] en el POD #$numero_pod2[$i] para el usuario $user_id2[$i] con el curso $curso_id2[$i].</td></tr>";
						}
					}
					else
					{
						if ($estadoID == 2)   //en un curso pasado las reservas ejecutadas son de un tiempo atras
						{
							if ($cursoID == 0)
								echo "<td width=\"55%\" bgcolor=\"#fcfcfc\" align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; cursos Pasados";
							else  //($cursoID > 0)
								echo "<td width=\"55%\" bgcolor=\"#fcfcfc\" align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Curso Pasado";
							
							echo " - No puede haber reservas en ejecuci&oacute;n en el sistema</td></tr>";
						}
						else
						{
							echo "<td width=\"55%\" bgcolor=\"#fcfcfc\" align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Ahora mismo No hay reservas en ejecuci&oacute;n en el sistema</td></tr>";
						}
					}				
			?>		
				</tr>
				
				<tr>
					<td colspan="2" height="10px"> </td>
				</tr>		
				
<?php  		  
				if ($count1 > 0)
					 $altura1 = $count1;
				else
					 $altura1 = 1;
?>
				<tr>
					<th width="18%" rowspan="<?php echo "$altura1"; ?>" bgcolor="FFE4C4"> reservas PENDIENTES</th>
<?php					
					if ($count1 > 0)
					{
						for ($i=0; $i<count($fecha1); $i++)
						{
							$j = $i+1;

							for ($k=0; $k<count($elenco_id_usuarios); $k++)
							    if ($user_id1[$i] == $elenco_id_usuarios[$k])	
							         $user1 = $k;

							for ($k=0; $k<count($elenco_id_cursos); $k++)
							   if ($curso_id1[$i] == $elenco_id_cursos[$k])
							        $course1 = $k;
									
							echo "<td width=\"50%\" bgcolor=\"#fcfcfc\" align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $j.- $fecha1[$i] a las $hora1[$i] en el <i>POD #$numero_pod1[$i]</i> para el usuario <b><i>$array_nombre_usuarios[$user1]</i></b> con el curso <i>$array_nombre_cursos[$course1]</i> </td></tr>";
						}
					}
					else
					{
						echo "<td width=\"50%\" bgcolor=\"#fcfcfc\" align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; No hay reservas pendientes en el sistema</td></tr>";
					}				
?>		
				</tr>
	
			    <tr>
					<td colspan="2" height="10px"> </td>
				</tr>
<?php  		
				if ($count2 > 0)
					 $altura2 = $count2;
				else
					 $altura2 = 1;
?>
				<tr>
					<th width="18%" rowspan="<?php echo "$altura2"; ?>" bgcolor="FFEFD5"> reservas EJECUTADAS</th>
<?php								
					if ($count2 > 0)
					{
						for ($i=0; $i<count($fecha2); $i++)
						{
							$j = $i+1;

							for ($k=0; $k<count($elenco_id_usuarios); $k++)
							    if ($user_id2[$i] == $elenco_id_usuarios[$k])	
							         $user2 = $k;

							for ($k=0; $k<count($elenco_id_cursos); $k++)
							   if ($curso_id2[$i] == $elenco_id_cursos[$k])
							        $course2 = $k;
									
							echo "<td width=\"50%\" bgcolor=\"#fcfcfc\" align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $j.- $fecha2[$i] a las $hora2[$i] en el <i>POD #$numero_pod2[$i]</i> para el usuario <b><i>$array_nombre_usuarios[$user2]</i></b> con el curso <i>$array_nombre_cursos[$course2]</i> </td></tr>";
						}
					}
					else
					{
						echo "<td width=\"50%\" bgcolor=\"#fcfcfc\" align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; No hay reservas ejecutadas en el sistema</td></tr>";
					}				
?>		
				</tr>
				
				<tr>
					<td colspan="2" height="10px"> </td>
				</tr>	
				
<?php				
			    if ($count3 > 0)
				     $altura3 = $count3;
			    else
				     $altura3 = 1;
?>
				<tr>
					<th width="18%" rowspan="<?php echo "$altura3"; ?>" bgcolor="EED9C4"> reservas CANCELADAS</th>
<?php					
					if ($count3 > 0)
					{
						for ($i=0; $i<count($fecha3); $i++)
						{
							$j = $i+1;

							for ($k=0; $k<count($elenco_id_usuarios); $k++)
							    if ($user_id3[$i] == $elenco_id_usuarios[$k])	
							         $user3 = $k;

							for ($k=0; $k<count($elenco_id_cursos); $k++)
							   if ($curso_id3[$i] == $elenco_id_cursos[$k])
							        $course3 = $k;
									
							echo "<td width=\"50%\" bgcolor=\"#fcfcfc\" align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $j.- $fecha3[$i] a las $hora3[$i] en el <i>POD #$numero_pod3[$i]</i> para el usuario <b><i>$array_nombre_usuarios[$user3]</i></b> con el curso <i>$array_nombre_cursos[$course3]</i> </td></tr>";
						}
					}
					else
					{
						echo "<td width=\"50%\" bgcolor=\"#fcfcfc\" align=\"left\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; No hay reservas canceladas en el sistema</td>";
 					}				
?>		
				</tr>				
			</table>
		</center>

		
<?php
			
	}

	
	echo "<hr>";

	
?>
  
	  
	  	  
	<form name="form_stats" action="estadisticas.php" method="POST">
      <div align="left">
	    <INPUT type="submit" name="estadisticas" value="STATS" face="algerian" size="5" style="background-color : #555555; color : White; font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 11pt; text-align : center; font-weight: bold; width:120px;" />
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
	mysql_close($conexion);
	ob_end_flush();		
}
?>