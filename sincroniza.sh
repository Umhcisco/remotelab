#!/bin/bash

###:::::::::::::::::::::::::::::::::::::::::::::::::::::::
### Sincronizacion Sesiones y RADIUS
###
### 2013
###:::::::::::::::::::::::::::::::::::::::::::::::::::::::  
dia=`date +%Y-%m-%d`
hora=$(echo `date +%H` | bc)  ## el 'bc' para quitar los ceros 01, 02... -> 1,2..

let hora=($hora/2)*2

#dia="2013-04-26"
#hora="12:00"

echo "Dia y hora: $dia  $hora"

###:::::::::::::::::::::::::::::::::::::::::::::::::::::::
#reservas="reservas"
#usuarios="usuarios"

pass="vXq@z*Ab33"


##  $reservas.num_POD -> cisco-avpair 
#buscoAvPair="(SELECT num_POD FROM $reservas WHERE $filtro)"

echo "DELETE FROM radcheck WHERE 1" | mysql radius -uroot -p$pass
echo "DELETE FROM radreply WHERE 1" | mysql radius -uroot -p$pass

for pod in `seq 1 3`; do
    echo "insertamos para pod=$pod"
    
    buscoUsuarioId="(SELECT username FROM usuarios WHERE (select user_id from reservas where (num_POD=$pod && fecha_reserva='$dia' && HOUR(horario_reserva)='$hora')) = user_id)"
    buscoPass="     (SELECT password FROM usuarios WHERE (select user_id from reservas where (num_POD=$pod && fecha_reserva='$dia' && HOUR(horario_reserva)='$hora')) = user_id)"
    
  ###:::::::::::::::::::::::::::::::::::::::::::::::::::::::  
  ###    radcheck
  ###:::::::::::::::::::::::::::::::::::::::::::::::::::::::  
    t_radius="radcheck"
    
    echo "INSERT INTO $t_radius (username,attribute,op,value) VALUES ($buscoUsuarioId,'password','==',$buscoPass)"  | mysql radius -uroot -p$pass
    
  ###:::::::::::::::::::::::::::::::::::::::::::::::::::::::  
  ###    radreply
  ###:::::::::::::::::::::::::::::::::::::::::::::::::::::::   
    t_radius="radreply"
    
    echo "INSERT INTO $t_radius (username,attribute,op,value) VALUES ($buscoUsuarioId,'Service-Type','==','Framed-User')" | mysql radius -uroot -p$pass
    echo "INSERT INTO $t_radius (username,attribute,op,value) VALUES ($buscoUsuarioId,'Framed-Protocol','==','PPP')" | mysql radius -uroot -p$pass

    pod_radius="ipsec:addr-pool=$pod"
    echo "INSERT INTO $t_radius (username,attribute,op,value) VALUES ($buscoUsuarioId,'cisco-avpair','==','$pod_radius')" | mysql radius -uroot -p$pass

echo "INSERT INTO $t_radius (username,attribute,op,value) VALUES ($buscoUsuarioId,'cisco-avpair','==','ipsec:inacl=StudentsSplit')" | mysql radius -uroot -p$pass
done

## Ponemos los usuarios fijos
echo "INSERT INTO radcheck (username,attribute,op,value) VALUES ('Students','password','==','cisco')" | mysql radius -uroot -p$pass
echo "INSERT INTO radreply (username,attribute,op,value) VALUES ('Students','cisco-avpair','==','ipsec:tunnel-password=YourAccess')" | mysql radius -uroot -p$pass

###:::::::::::::::::::::::::::::::::::::::::::::::::::::::
###    nas
###:::::::::::::::::::::::::::::::::::::::::::::::::::::::
t_radius="nas"
#echo "INSERT INTO $t_radius (nasname,shortname,Type,secret) VALUES ('192.168.100.254','vpnServer','cisco','ufdy65tg')" | mysql radius -uroot -p$pass

###:::::::::::::::::::::::::::::::::::::::::::::::::::::::
###    Arrancamos el pod correspondiente
###:::::::::::::::::::::::::::::::::::::::::::::::::::::::

if [ $# -gt 0 ]
 then
  pod=$1
  #/var/www/paro_marcha.sh $pod 1 
  /var/www/paro_marcha.sh RTSW$pod ON
fi

##pod=$1
##/var/www/paro_marcha.sh $pod 1

###::::::::::::::::::::::::::::::::::::::::::::::::::::::: 
### ...this is the end
###:::::::::::::::::::::::::::::::::::::::::::::::::::::::
