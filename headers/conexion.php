<?php
 $host= '193.147.145.173';  // Host name (127.0.0.1 si se trabaja en local)
 $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
 
 $hostdb="localhost";  // Host name
 $username="root";   // Mysql username 
 $password="vXq@z*Ab33";       // Mysql password 
 $db_name="radius"; // Database name 
 
 
 // Connect to server and select databse.
 $conexion = mysql_connect("$hostdb", "$username", "$password")or die("cannot connect"); 
 mysql_select_db("$db_name",$conexion)or die("cannot select DB");

 //fecha en este mismo instante
 $ahora = date('Y-m-d H:i:s');
 
 //color de fondo de la web
 $bgcolor = "fbb504";
 
 //color secundario para fondo de tablas
 $bgcolor2 = "ffc737";    //ffcc66

?>