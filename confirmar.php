<?php 
 include ("headers/seguridad.php");
 include ("headers/conexion.php");

//iniciamos el buffer de salida 
ob_start();

$id_usuario = $_SESSION['id_usuario'];
$id_curso = $_SESSION['id_curso'];
$id_sesion = $_SESSION['id_sesion'];

$numMaxReservas = $_SESSION['numMaxReservas'];
$numMaxResvSemana = $_SESSION['numMaxResvSemana'];
$round_robin = $_SESSION['round_robin'];
$numPods = $_SESSION['numPods'];
$offset_pod = $_SESSION['offset_pod'];

$duracionTurno = $_SESSION['duracionTurno'];
$HorasTurno = floor($duracionTurno);
$MinutosTurno = ($duracionTurno - $HorasTurno)*60;
$numTurnosDia = $_SESSION['numTurnosDia'];
$f_ahora = date('Y-m-d H:i:s');
$hoy = date('Y-m-d');
$hora = date('H:i:s');


//******** CODIGO PARA GESTIONAR LAS RESERVAS ********** 

if (isset($_POST['opcion'])) {

    $opciones_str = implode(" ", $_POST['opcion']);// converts $_POST opcion into a string
    $opciones_array = explode(" ", $opciones_str);// converts the string to an array which you can easily manipulate

	for ($i = 0; $i < count($opciones_array); $i++) {
		echo "<br>$opciones_array[$i]";// display the result as a string
	}

    //separamos las distintas opciones seleccionadas
    $num_turnos_seleccionados = count($opciones_array);	
	echo "<br>num_turnos_seleccionados = $num_turnos_seleccionados";
	
	for ($i=0; $i<$num_turnos_seleccionados; $i++){
	   $j = $i + 1;
       echo "<br> $j &#170; Opci&oacute;n --> $opciones_array[$i]";
	}   

	////SE EXTRAEN las RESERVAS ACTIVAS para en este momento, de este usuario, para este curso
	$sql9="SELECT * FROM reservas WHERE (estado_reserva = 0  OR estado_reserva = 2) AND curso_id = $id_curso and user_id = $id_usuario ORDER BY fecha_reserva, horario_reserva "; 
	$registros9=mysql_query($sql9,$conexion) or die ("Problemas con el Select (sql9) ".mysql_error());
	$num_mis_reservas_activas = mysql_num_rows($registros9);
	echo "<br>num_mis_reservas_activas = $num_mis_reservas_activas";
	//liberamos recursos
	mysql_free_result($registros9);

	// se comprueba si se ha sobrepasado el limite de reservas
	$reservas_disponibles = $numMaxReservas - $num_mis_reservas_activas;   
	if ($num_turnos_seleccionados > $reservas_disponibles) {
	    //limpiamos y cerramos
        mysql_close($conexion);
		ob_end_flush();
		
		echo "<br>&iexcl;Has sobrepasado el l&iacute;mite total de reservas!";
		echo "<br>Te quedan $reservas_disponibles reservas disponibles.";
	    header("Location: reservas.php?errorreserva=so");	
	}
}else{
    //si no hay ninguna opcion seleccionada, limpiamos y cerramos
	mysql_close($conexion);
	ob_end_flush();

	echo "<br>&iexcl;Selecciona al menos un turno!";
    header("Location: reservas.php?errorreserva=si"); 
}


//******** CODIGO PARA CONTROLAR LAS RESERVAS POR SEMANAS NATURALES ********
$dia_sem = date('w', strtotime($hoy));
if ($dia_sem == 0)
	$dia_sem=7;
echo "<br>dia_sem = $dia_sem";

$dias_distancia = $dia_sem - 1;
$lunes_ref = date('Y-m-d',strtotime('-'.$dias_distancia.' days', strtotime($f_ahora)));
$domingo_ref = date('Y-m-d',strtotime('+6 days', strtotime($lunes_ref)));
$lunes_sgte = date('Y-m-d',strtotime('+7 days', strtotime($lunes_ref)));
$domingo_sgte = date('Y-m-d',strtotime('+13 days', strtotime($lunes_ref)));
echo "<br>$lunes_ref - $domingo_ref ; $lunes_sgte - $domingo_sgte";

//buscamos cuantas reservas ha realizado este usuario para la semana actual 
$sql1 = "SELECT count(*) AS total FROM reservas WHERE user_id = $id_usuario AND fecha_reserva BETWEEN '$lunes_ref' AND '$domingo_ref' ";
$registros1 = mysql_query($sql1,$conexion) or die ("Problemas con el Select  (sql1) ".mysql_error()); 
$cuenta1 = mysql_fetch_assoc($registros1);
$count1 = $cuenta1['total'];

//buscamos cuantas reservas ha realizado este usuario para la semana proxima
$sql2 = "SELECT count(*) AS total FROM reservas WHERE user_id = $id_usuario AND fecha_reserva BETWEEN '$lunes_sgte' AND '$domingo_sgte' ";
$registros2 = mysql_query($sql2,$conexion) or die ("Problemas con el Select  (sql2) ".mysql_error()); 
$cuenta2 = mysql_fetch_assoc($registros2);
$count2 = $cuenta2['total'];

echo "<br>Num. de mis Reservas para la semana actual= $count1";
echo "<br>Num. de mis Reservas para la semana que viene= $count2";
$cont_sem_actual=$count1;
$cont_sem_proxima=$count2;

//liberamos recursos
mysql_free_result($registros1);
mysql_free_result($registros2);



//******** codigo para localizar los turnos consecutivos ******//
//primero ordenamos de menor a mayor los turnos (de mas proximo a mas lejano en el tiempo futuro)
sort($opciones_array);       //rsort($opciones_ordenadas);
$array_length=count($opciones_array);
echo "<br>";
for ($j=0; $j<$array_length; $j++)
	echo "<br>opciones_array:  Elemento $j ==> $opciones_array[$j]";
$array_suc[0]=0;
$flag = 1;
if ($array_length > 1){
	for ($j=0; $j<($array_length-1); $j++){
		if ( ($opciones_array[$j] + 1) == $opciones_array[$j+1] ){
			$array_suc[$j] = $flag;
			$array_suc[$j+1]= $flag;
		}else{
			$array_suc[$j+1]=0;
			if ($array_suc[$j]>0)
				$flag++;
		}
	}
}
for ($j=0; $j<$array_length; $j++)
	echo "<br>array_suc[$j] = $array_suc[$j]";
	
if ($round_robin == 2){	
	$primero_meseta1_flag = 0;   //se prepara este flag para avisar del inicio de la meseta 1, si la hubiera 
	$primero_meseta2_flag = 0;	 //se prepara este flag para avisar del inicio de la meseta 2, si la hubiera
}

				

//********* CODIGO PARA LOCALIZAR LOS PODS DESHABILITADOS ********//
//extraemos el codigo flag_pods_ok de este curso almacenado en mysql
$sql10 = "SELECT * FROM cursos WHERE curso_id = $id_curso";
$registros10 = mysql_query($sql10,$conexion) or die ("Problemas con el Select  (sql10) ".mysql_error()); 
$count10 = mysql_num_rows($registros10);
//echo "<br>count10 = $count10";  
if ($reg10 = mysql_fetch_array($registros10)){ 
	$flag_pods_ok = $reg10['flag_pods_ok'];  
	echo "<br><br>flag_pods_ok=$flag_pods_ok";
}else{
	//echo "<br>error en la tabla Cursos de la BB.DD. mysql";
}
//liberamos recursos
mysql_free_result($registros10); 

function truemod($num, $mod) {
   return ($mod + ($num % $mod)) % $mod;
}

//metemos los estados de los diversos pods en un array, y los numeros de pod en otro array
$max_pods = $numPods; 
$primer_pod = $offset_pod+1;
$tmp = $flag_pods_ok;
for ($i=0; $i<$max_pods; $i++){
	$cociente = floor($tmp/2);
	$resto = truemod(($tmp),2);
	$pods_estado[$i] = $resto;
	$pods_numero[$i] = $offset_pod +1 +$i;
	$tmp = $cociente;
	echo "<br>estado del pod #$pods_numero[$i] = $pods_estado[$i] : ";
	if ($pods_estado[$i] == 1) echo "ACTIVO"; else echo "INACTIVO";
}

///buscamos los pods desactivados (lista negra) en este turno
$j=0;
for ($i=0; $i<$max_pods; $i++){							      
	if ($pods_estado[$i] == 0){ 
		$lista_negra_pods[$j] = $pods_numero[$i];
		$j++;
	}
}

if ($j>0){
	for ($i=0; $i<$j; $i++)
	echo "<br>lista_negra_pods[$i] = $lista_negra_pods[$i]";
}



//******** CODIGO PARA PASAR DE VALOR A FECHA **********
//Hora de referencia activa -> el inicio del dia de hoy, a medianoche
$hora_ref_activa = date('Y-m-d'." 00:00:00");

for ($i=0; $i<$num_turnos_seleccionados; $i++){
    
	$opcion = $opciones_array[$i];
	
	$diaReserva = date('Y-m-d',strtotime('+'.floor($opcion/$numTurnosDia).' days', strtotime($hora_ref_activa)));
	
    $horaInicio = date('Y-m-d H:i:s',strtotime('+'.($opcion - floor($opcion/$numTurnosDia)*$numTurnosDia)*$duracionTurno.' hours', strtotime($diaReserva)));
    if ($opcion*$duracionTurno > floor($opcion*$duracionTurno)){
	     $minutosOffset = ($duracionTurno - floor($duracionTurno))*60;
		 $horaTemp = date('Y-m-d H:i:s',strtotime('+'.$minutosOffset.' minutes', strtotime($horaInicio)));
         $horaInicio = $horaTemp;
	}
	$horaReserva = date('H:i:s',strtotime('+ 0 seconds', strtotime($horaInicio)));
	$fechaReserva = $horaInicio;
    echo "<br><br>Opcion = $opcion  -->  Fecha Reserva = $fechaReserva";
	echo "<br>diaReserva = $diaReserva -- horaReserva = $horaReserva  --> fechaReserva = $fechaReserva";
   
    //se comprueba si se ha sobrepasado el limite establecido de reservas semanal
	$dif_lunes_ref = floor(abs((strtotime($fechaReserva) - strtotime($lunes_ref)) / 60 / 60 / 24));
    $dif_semanas = floor($dif_lunes_ref / 7);
 
    if ($dif_semanas == 0)
	   $count1++;
	else
	   $count2++;
	   
    if ($count1 > $numMaxResvSemana){
	    //limpiamos y cerramos
        mysql_close($conexion);
		ob_end_flush();
		
		echo "<br>&iexcl;Has sobrepasado el l&iacute;mite de reservas para esta semana!";
		echo "<br>Hab&iacute;as realizado $cont_sem_actual reservas hasta ahora, y el l&iacute;mite son $numMaxResvSemana.";
	    header("Location: reservas.php?errorreserva=sa");	
    
	}else if ($count2 > $numMaxResvSemana){
			  //limpiamos y cerramos
			  mysql_close($conexion);
			  ob_end_flush();	
			  
			  echo "<br>&iexcl;Has sobrepasado el l&iacute;mite de reservas para la semana pr&oacute;xima!";
			  echo "<br>Hab&iacute;as realizado $cont_sem_proxima reservas hasta ahora, y el l&iacute;mite son $numMaxResvSemana.";
			  header("Location: reservas.php?errorreserva=sp");	
	}  
	
	//Seleccionamos de la tabla Reservas los turnos ocupados en el intervalo de tiempo seleccionado, para encontrar un POD libre
	$sql3 = "SELECT * FROM reservas WHERE fecha_reserva = '$diaReserva' AND horario_reserva = '$horaReserva' AND curso_id = $id_curso ORDER BY num_POD ASC";
	$registros3 = mysql_query($sql3,$conexion) or die ("Problemas con el Select  (sql3) ".mysql_error()); 
	$numPods_ocupados = mysql_num_rows($registros3);
	echo "<br>numPods = $numPods; numPods_ocupados = $numPods_ocupados";
	
	//Seleccionamos de la tabla Mantenimiento los turnos de mantenimiento fijados en el intervalo de tiempo seleccionado, para encontrar un POD libre
	$pod_inicial = $offset_pod + 1;
	$pod_final = $offset_pod + $numPods;
	$sql33 = "SELECT * FROM mantenimiento WHERE fecha_outage = '$diaReserva' AND horario_outage = '$horaReserva' AND num_POD_outage BETWEEN $pod_inicial AND $pod_final ORDER BY num_POD_outage ASC";
	$registros33 = mysql_query($sql33,$conexion) or die ("Problemas con el Select  (sql33) ".mysql_error()); 
	$numPods_mant = mysql_num_rows($registros33);
	echo "; numPods_mant = $numPods_mant";	   //echo "<br>sql33 = $sql33";

	//contamos el numero de pods en la lista negra (deshabilitados)
	if (isset($numPods_lista_negra)){
		$numPods_lista_negra = count($lista_negra_pods);
		echo "; numPods_lista_negra = $numPods_lista_negra";
	}else{
		$numPods_lista_negra = 0;
		echo "; numPods_lista_negra = $numPods_lista_negra";	
	}
	
	$num_pods_disponibles = $numPods - $numPods_ocupados - $numPods_mant - $numPods_lista_negra;
	echo "<br>num_pods_disponibles = $num_pods_disponibles";
	
	//se comprueba si quedan pods libres para asignar a la reserva solicitada
	if ($num_pods_disponibles <= 0)
	{
		//limpiamos y cerramos
		mysql_free_result($registros3);
		mysql_close($conexion);
		ob_end_flush();	
		  
		echo "<br>&iexcl;Ya no quedan Pods libres en este turno!";
		echo "<br>Realiza tu reserva en otro intervalo temporal.";
		header("Location: reservas.php?errorreserva=np");		
	}
	else   //si que hay pods libres
	{	
		/////////////se procede a combinar los turnos reservados y los turnos de mantenimiento sobre este intervalo 
		$cont = 0;
		$j = 0;
		//Seleccionamos de la tabla Reservas los pods ocupados en el intervalo de tiempo seleccionado, para encontrar un POD libre
		if ($numPods_ocupados > 0){
			mysql_data_seek ( $registros3, 0);
			while ($reg3 = mysql_fetch_array($registros3)){
				 $reserva_id  = $reg3['reserva_id'];
				 $pods_ocupados[$cont] = $reg3['num_POD'];
				 $j=$cont+1;
				 echo "<br> $j &deg; turno ocupado = POD $pods_ocupados[$cont] ; con la reserva_id = $reserva_id.";
				 $cont++;	
			}
		}
		//Seleccionamos de la tabla Mantenimiento los pods en mantenimiento en el intervalo de tiempo seleccionado, para encontrar un POD libre
		if ($numPods_mant > 0){
			mysql_data_seek ( $registros33, 0);
			while ($reg33 = mysql_fetch_array($registros33)){
				 $outage_id  = $reg33['outage_id'];
				 $pods_ocupados[$cont] = $reg33['num_POD_outage'];
				 $j=$cont+1;
				 echo "<br> $j &deg; turno en mantenimiento = POD $pods_ocupados[$cont] ; con el outage_id = $outage_id.";
				 $cont++;	
			}	
		}
		////////Se seleccionan los pods de la lista negra (deshabilitados)
		if (isset($lista_negra_pods)){
			for ($h=0; $h<count($lista_negra_pods); $h++){
				$pod_temp = $lista_negra_pods[$h];
				$pods_ocupados[$cont] = $pod_temp;
				$j=$cont+1;
				echo "<br> $j &deg; pod en lista negra = POD $pods_ocupados[$cont]";
				$cont++;
			}
		}
		//se reordena el vector $pods_ocupados, de menor a mayor
		if (isset($pods_ocupados)){
			sort($pods_ocupados);

			for($h=0; $h<count($pods_ocupados); $h++)
				echo "<br>pods_ocupados[$h] = $pods_ocupados[$h]";
		}else
			echo "<br>No hay pods ocupados en este turno";
		
		
		echo "<br>round_robin = $round_robin";
				
		//si el metodo de asignacion de nuevos pods es round_robin
		if ($round_robin == 1)     //round_robin = 1
		{
			$sql4 = "SELECT * FROM log_detalles WHERE confir_resv IS NOT NULL AND curso_id = $id_curso ORDER BY log_det_id DESC LIMIT 1";
			$registros4 = mysql_query($sql4,$conexion) or die ("Problemas con el Select  (sql4) ".mysql_error()); 
			$count4 = mysql_num_rows($registros4);
			
			//si hay reservas en el sistema
			mysql_data_seek ( $registros4, 0);
			if ($reg4=mysql_fetch_array($registros4))
			{
				$ultimo_pod = $reg4['confir_pod'];
				$ultimo_pod_corregido = $ultimo_pod - $offset_pod;
				echo "<br>ultimo_pod_corregido = $ultimo_pod_corregido";
				//numero de pod que sera asignado a esta reserva
				$pod_activo = ($ultimo_pod_corregido % $numPods) + 1;
				echo "<br>pod_activo = $pod_activo";
				$pod_activo_corregido = $pod_activo + $offset_pod;
				echo " --> pod_activo_corregido = $pod_activo_corregido";
				
				if ($cont > 0){
					 for ($j=0; $j<count($pods_ocupados); $j++){
						 
						 if ($pods_ocupados[$j] == $pod_activo_corregido){
							 echo "<br>pods_ocupados[$j] = $pods_ocupados[$j] ; pod_activo_corregido = $pod_activo_corregido";
							 $pod_activo = ($pod_activo % $numPods) + 1;
							 echo "<br>Rectificaci&oacute;n del pod_activo = $pod_activo";
							 $pod_activo_corregido = $pod_activo + $offset_pod;
							 echo " --> pod_activo_corregido = $pod_activo_corregido";
						 }
					 }
				}
				else
					 echo "<br>No hay turnos ocupados en este intervalo horario";
							 
			//si no hay reservas en el sistema						
			}else{
				$pod_activo = 1;	
				$pod_activo_corregido = $pod_activo + $offset_pod;
			}
				
			echo "<br>**El pod adjudicado es el pod_activo_corregido = $pod_activo_corregido";	
			
			//liberamos recursos
			mysql_free_result($registros3);
			mysql_free_result($registros33);
			mysql_free_result($registros4);		
		}
		
		
		//si el metodo de asignacion de nuevos pods es prioridad al pod mas bajo (esto es, el pod 1) 
		// pero intentando asignar el mismo pod a turnos consecutivos
		else if ($round_robin == 2)  //round_robin = 2
		{
			$succ_flag = -1;
			$top = max($array_suc);  
			echo "<br>top = $top";
			if ($top == 0)
				$succ_flag = 0;
			else{
				if ($array_suc[$i] == 0)
					$succ_flag = 0;
				else if ($array_suc[$i] == 1)
					$succ_flag = 1;
				else if ($array_suc[$i] == 2)	
					$succ_flag = 2;
				else
					echo "<br>Error: array_suc ha superado el valor 2";
			}
			echo "<br>succ_flag = $succ_flag";
			
			if ($succ_flag == 1)				 //primer grupo de turnos consecutivos 
			{
				if ($primero_meseta1_flag == 0){
					$m=0;
					$n=0;
					$primero_meseta1_flag = 1;
					if (isset($vector_pods_temp)){
						unset($vector_pods_temp);
						unset($pods_temp);
					}
					echo "<br>array_length = $array_length";
					if ($top > 0){
						for($k=0; $k<$array_length; $k++){
							if ($array_suc[$k] == 1){
								$meseta1[$m]=$opciones_array[$k];
								$m++;
							}						
							
							if ($array_suc[$k] == 2){
								$meseta2[$n]=$opciones_array[$k];
								$n++;
							}	
						}
						if (isset($meseta1)){
							$update = 0;
							////echo "<br>meseta1[$update] = $meseta1[$update] *** opcion = $opcion";
							while ($meseta1[$update] < $opcion){										
								$meseta1 = array_slice($meseta1, 1);
								$m--;
							}
							echo "<br>m = $m";

							for($j=0; $j<count($meseta1); $j++)  echo "<br>meseta1[$j] = $meseta1[$j]";
						}
						if (isset($meseta2)){
							$update2 = 0;
							while ($meseta2[$update2] < $opcion){										
								$meseta2 = array_slice($meseta2, 1);
								$n--;
							}
							echo "<br>n = $n";	
							
							for($j=0; $j<count($meseta2); $j++)  echo "<br>meseta2[$j] = $meseta2[$j]";							
						}

					}
					
					$k=0;
					for ($j=0; $j<count($meseta1); $j++){
						//Seleccionamos de la tabla Reservas los turnos ocupados en el intervalo de tiempo seleccionado, para encontrar un POD libre. En los turnos consecutivos y el anterior y el posterior
						///$variable = ($j-1)*$duracionTurno;
						$variable =$j*$duracionTurno;
						$fecha_temp = date('Y-m-d H:i:s',strtotime('+'.$variable.' hours', strtotime($horaInicio)));
						$zero = 0;
						$dia_temp[$j] = date('Y-m-d',strtotime('+'.$zero.' hours', strtotime($fecha_temp)));
						$hora_temp[$j] = date('H:i:s',strtotime('+'.$zero.' hours', strtotime($fecha_temp)));
						echo "<br>j=$j -- variable=$variable -- dia_temp = $dia_temp[$j] -- hora_temp = $hora_temp[$j]";
		
						///intentar que todos los intervalos consecutivos tengan el mismo pod --> pods ocupados
						$sql0 = "SELECT * FROM reservas WHERE fecha_reserva = '$dia_temp[$j]' AND horario_reserva = '$hora_temp[$j]' AND curso_id = $id_curso ORDER BY num_POD ASC";
						$registros0 = mysql_query($sql0,$conexion) or die ("Problemas con el Select  (sql0) ".mysql_error()); 
						$numPods_ocupados = mysql_num_rows($registros0);
						
						///intentar que todos los intervalos consecutivos tengan el mismo pod --> pods mantenimiento
						$sql00 = "SELECT * FROM mantenimiento WHERE fecha_outage = '$dia_temp[$j]' AND horario_outage = '$hora_temp[$j]' AND num_POD_outage BETWEEN $pod_inicial AND $pod_final ORDER BY num_POD_outage ASC";
						$registros00 = mysql_query($sql00,$conexion) or die ("Problemas con el Select  (sql00) ".mysql_error()); 
						$numPods_mant = mysql_num_rows($registros00);
					
						while ($reg0 = mysql_fetch_array($registros0)){
							$pod_temp = $reg0['num_POD'];
							$vector_pods_temp[$k] = $pod_temp;
							$k++;
						}
						while ($reg00 = mysql_fetch_array($registros00)){
							$pod_temp = $reg00['num_POD_outage'];
							$vector_pods_temp[$k] = $pod_temp;
							$k++;
						}

						///contabilizar los pods de la lista negra --> pods deshabilitados
						if (isset($lista_negra_pods)){
							for ($h=0; $h<count($lista_negra_pods); $h++){
								$pod_temp = $lista_negra_pods[$h];
								$vector_pods_temp[$k] = $pod_temp;
								$k++;
							}
						}
						
						//ordenamos los pods ocupados en order ascendente
						if (isset($vector_pods_temp)){
							for ($h=0; $h<count($vector_pods_temp); $h++)
								echo "<br>vector_pods_temp[$h] = $vector_pods_temp[$h]";
							echo "<br>k = $k";
							$vector_pods = array_values(array_unique($vector_pods_temp));
							sort($vector_pods);
							$str_pods=implode(',',$vector_pods);
							echo "<br>str_pods = $str_pods";
						}else
							echo "<br>No hay reservas en este turno";
						mysql_free_result($registros0);
						mysql_free_result($registros00);
					}				
					
					//////////////////
					//eliminar duplicados en un array --> $lista=array(1,2,1,3,1,1,2,3) --> $lista_simple = array_unique($lista) --> lista_final = array_values($lista_simple)
					
					
					$pod_activo = 1;         //por defecto vamos al pod 1
					$pod_activo_corregido = $pod_activo + $offset_pod;
					
					//si hay reservas en esta franja horaria, o bien turnos de mantenimiento
					if (isset($vector_pods)){
						if (count($vector_pods) < $numPods){   //si queda algun pod libre
							for ($j=0; $j<count($vector_pods); $j++){
								if ($vector_pods[$j] == $pod_activo_corregido) {
									$pod_activo++;                        
									$pod_activo_corregido++;
								}
							}
						}else{  //si ya no quedan pods libres
							if (isset($pods_ocupados)){
								//este primer elemento se hace como round_robin=0
								for ($j=0; $j<count($pods_ocupados); $j++){
									if ($pods_ocupados[$j] == $pod_activo_corregido){
										$pod_activo++;  
										$pod_activo_corregido++;	
									}
								}
								//se quita este primer elemento de la lista de turnos consecutivos para intentar colocar los siguientes en el mismo pod
								$primero_meseta1_flag = 0;
								//$meseta1 = array_slice($meseta1, 1);
								//for($j=0; $j<count($meseta1); $j++)  echo "<br>meseta1[$j] = $meseta1[$j]";
							//si no hay reservas en esta franja horaria						
							}else    
								echo "<br>No hay ninguna reserva en esta franja horaria";					
						}
					//si no hay reservas en esta franja horaria, ni turnos de mantenimiento					
					}else    
						echo "<br>No hay ninguna otra reserva en esta franja horaria";
					
					echo "<br>**El pod adjudicado es el pod_activo_corregido = $pod_activo_corregido";
					$pod_activo_succ = $pod_activo_corregido;

				}
				else
				{
					////si no es el primer elemento de la meseta, se asigna el pod calculado para toda la meseta
					$pod_activo_corregido = $pod_activo_succ;
					echo "<br>**El pod adjudicado es el pod_activo_corregido = $pod_activo_corregido";
				}
			}
			
			else if ($succ_flag == 2)     //segundo grupo de turnos consecutivos
			{
				if ($primero_meseta2_flag == 0){
					$primero_meseta2_flag = 1;
					if (isset($vector_pods_temp2)){
						unset($vector_pods_temp2);
						unset($pods_temp2);
					}
					$k=0;
					for ($j=0; $j<count($meseta2); $j++){
						//Seleccionamos de la tabla Reservas los turnos ocupados en el intervalo de tiempo seleccionado, para encontrar un POD libre. En los turnos consecutivos y el anterior y el posterior
						///$variable = ($j-1)*$duracionTurno;
						$variable =$j*$duracionTurno;
						$fecha_temp = date('Y-m-d H:i:s',strtotime('+'.$variable.' hours', strtotime($horaInicio)));
						$zero = 0;
						$dia_temp[$j] = date('Y-m-d',strtotime('+'.$zero.' hours', strtotime($fecha_temp)));
						$hora_temp[$j] = date('H:i:s',strtotime('+'.$zero.' hours', strtotime($fecha_temp)));
						echo "<br>j=$j -- variable=$variable -- dia_temp = $dia_temp[$j] -- hora_temp = $hora_temp[$j]";
		
						///intentar que todos los intervalos consecutivos tengan el mismo pod --> pods ocupados
						$sql0 = "SELECT * FROM reservas WHERE fecha_reserva = '$dia_temp[$j]' AND horario_reserva = '$hora_temp[$j]' AND curso_id = $id_curso ORDER BY num_POD ASC";
						$registros0 = mysql_query($sql0,$conexion) or die ("Problemas con el Select  (sql0) ".mysql_error()); 
						$numPods_ocupados = mysql_num_rows($registros0);
						
						///intentar que todos los intervalos consecutivos tengan el mismo pod --> pods mantenimiento
						$sql00 = "SELECT * FROM mantenimiento WHERE fecha_outage = '$dia_temp[$j]' AND horario_outage = '$hora_temp[$j]' AND num_POD_outage BETWEEN $pod_inicial AND $pod_final ORDER BY num_POD_outage ASC";
						$registros00 = mysql_query($sql00,$conexion) or die ("Problemas con el Select  (sql00) ".mysql_error()); 
						$numPods_mant = mysql_num_rows($registros00);
						
						while ($reg0 = mysql_fetch_array($registros0)){
							$pod_temp = $reg0['num_POD'];
							$vector_pods_temp2[$k] = $pod_temp;
							$k++;
						}
						while ($reg00 = mysql_fetch_array($registros00)){
							$pod_temp = $reg00['num_POD_outage'];
							$vector_pods_temp2[$k] = $pod_temp;
							$k++;
						}

						if (isset($vector_pods_temp2)){
							for ($h=0; $h<count($vector_pods_temp2); $h++)
								echo "<br>vector_pods_temp2[$h] = $vector_pods_temp2[$h]";
							echo "<br>k = $k";
							$vector_pods2 = array_values(array_unique($vector_pods_temp2));
							sort($vector_pods2);
							$str_pods2=implode(',',$vector_pods2);
							echo "<br>str_pods2 = $str_pods2";
						}else
							echo "<br>No hay reservas en este turno";
						mysql_free_result($registros0);
						mysql_free_result($registros00);
					}				
					
					$pod_activo = 1;         //por defecto vamos al pod 1
					$pod_activo_corregido = $pod_activo + $offset_pod;
					
					//si hay reservas en esta franja horaria, o bien turnos de mantenimiento
					if (isset($vector_pods2)){
						if (count($vector_pods2) < $numPods){   //si queda algun pod libre
							for ($j=0; $j<count($vector_pods2); $j++){
								if ($vector_pods2[$j] == $pod_activo_corregido) {
									$pod_activo++;                        
									$pod_activo_corregido++;
								}
							}
						}else{  //si ya no quedan pods libres, se hace como round_robin=0
							if (isset($pods_ocupados)){
								//este primer elemento se hace como round_robin=0
								for ($j=0; $j<count($pods_ocupados); $j++){
									if ($pods_ocupados[$j] == $pod_activo_corregido){
										$pod_activo++;  
										$pod_activo_corregido++;	
									}
								}
								//se quita este primer elemento de la lista de turnos consecutivos para intentar colocar los siguientes en el mismo pod
								$primero_meseta2_flag = 0;
								//$meseta2 = array_slice($meseta2, 1);
							//si no hay reservas en esta franja horaria						
							}else    
								echo "<br>No hay ninguna otra reserva en esta franja horaria";					
						}
					//si no hay reservas en esta franja horaria, ni turnos de mantenimiento					
					}else    
						echo "<br>No hay ninguna otra reserva en esta franja horaria";							
					
					echo "<br>**El pod adjudicado es el pod_activo_corregido = $pod_activo_corregido";
					$pod_activo_succ = $pod_activo_corregido;
				}
				else
				{
					////si no es el primer elemento de la meseta, se asigna el pod calculado para toda la meseta
					$pod_activo_corregido = $pod_activo_succ;
					echo "<br>**El pod adjudicado es el pod_activo_corregido = $pod_activo_corregido";
				}
			}
			else  ////como si fuera round-robin=0
			{
				////como si fuera round-robin=0
				$pod_activo = 1;     //por defecto vamos al pod 1
				$pod_activo_corregido = $pod_activo + $offset_pod;
				
				//si hay reservas en esta franja horaria
				if (isset($pods_ocupados)){
					
					for ($j=0; $j<count($pods_ocupados); $j++){
						if ($pods_ocupados[$j] == $pod_activo_corregido){
							$pod_activo++;  
							$pod_activo_corregido++;	
						}
					}
				//si no hay reservas en esta franja horaria						
				}else    
					echo "<br>No hay ninguna otra reserva en esta franja horaria";
				
				echo "<br>**El pod adjudicado es el pod_activo_corregido = $pod_activo_corregido";
							
			}
			
			//liberamos recursos
			mysql_free_result($registros3);
			mysql_free_result($registros33);
			
		}
		
		//si el metodo de asignacion de nuevos pods es prioridad al pod mas bajo (esto es, el pod 1)  
		else   //round_robin = 0
		{
			$pod_activo = 1;     //por defecto vamos al pod 1
			$pod_activo_corregido = $pod_activo + $offset_pod;
			
			//si hay reservas en esta franja horaria
			if (isset($pods_ocupados)){
				
				for ($j=0; $j<count($pods_ocupados); $j++){
					if ($pods_ocupados[$j] == $pod_activo_corregido){
					    $pod_activo++;  
						$pod_activo_corregido++;	
					}
				}
			//si no hay reservas en esta franja horaria						
			}else    
				echo "<br>No hay ninguna otra reserva en esta franja horaria";
			
			echo "<br>**El pod adjudicado es el pod_activo_corregido = $pod_activo_corregido";
			
			//liberamos recursos
			mysql_free_result($registros3);
			mysql_free_result($registros33);
		}

	}
	
	//se comprueba si esta reserva ya ha sido contabilizada en la base de datos, por si se ha recargado la pagina de reservas
	$sql5 = "SELECT * FROM reservas WHERE user_id = $id_usuario AND fecha_reserva = '$diaReserva' AND horario_reserva = '$horaReserva'";
	$registros5 = mysql_query($sql5,$conexion) or die ("Problemas con el Select  (sql5) ".mysql_error());
	$count5 = mysql_num_rows($registros5);
	mysql_free_result($registros5);
											
	if ($count5 == 0) {
	     $estado_reserva = 0;
											
		 $sql6 = "INSERT INTO reservas (user_id, curso_id, fecha_reserva, horario_reserva, num_POD, estado_reserva) VALUES ($id_usuario, $id_curso, '$diaReserva', '$horaReserva', $pod_activo_corregido, $estado_reserva) ";
	     $registros6 = mysql_query($sql6,$conexion) or die("Problemas en el Insert (sql6) ".mysql_error());
		 
		 $sql7 = "INSERT INTO log_detalles (log_id, curso_id, user_id, confir_resv, confir_pod) VALUES ($id_sesion, $id_curso, $id_usuario, '$fechaReserva', $pod_activo_corregido) ";
		 $registros7 = mysql_query($sql7,$conexion) or die("Problemas en el Insert (sql7) ".mysql_error());
		 
		 ////////// SCRIPT para que la Base de Datos RADIUS me permita entrar si hago una reserva habiendo empezando ya el intervalo.
		 //if ( ($hoy == $fecha_reserva) && ($hora > $horario_reserva) ) {
		 //		exec('/home/labremoto/sincroniza.sh &');       //// echo exec('whoami');
		 //}
	 	 
	}else{                                
		//limpiamos y cerramos
		mysql_free_result($registros5);
		mysql_close($conexion);
		ob_end_flush();	
		  
		echo "<br>&iexcl;Ya existe una reserva en este turno para este usuario!";
		echo "<br>Realiza tu reserva en otro intervalo temporal.";
		header("Location: reservas.php?errorreserva=sr");
	}
	
	//reseteamos el vector pods_ocupados, para la siguiente iteracion
	if (isset($pods_ocupados))
		unset($pods_ocupados);
	//echo "<br>num_turnos_seleccionados = $num_turnos_seleccionados -- opcion = $opcion";
	echo "<br>i = $i";
}

mysql_close($conexion);
ob_end_flush();		

header("Location: reservas.php");
?>

<!--  <br><br><A href="reservas.php">VOLVER</A>   -->