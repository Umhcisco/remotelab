###:::::::::::::::::::::::::::::::::::::::::::::::::::::::
##
##    Chequear si los PODS viven  
###
### 2013
###:::::::::::::::::::::::::::::::::::::::::::::::::::::::  
#! /bin/bash

DIR="/var/www"
DAT="$DIR/check_pods.dat"
OUT="$DIR/check_pods.out"
TMP="$DIR/check_pods.tmp"
intentos=5

salida=`date +"%D %R"`
while read ip
  do 
  r=`ping -c1 $ip | grep ttl`
  longitud=${#r}
  host=0
  
  if [ $longitud -gt 0 ]; then
      host=1
  fi
  salida="$salida $host"
done < $DAT

echo "$salida" >> $OUT

### borramos la linea 5000 para evitar que el fichero crezca infinitamente
sed '5000d' $DAT > $TMP ; mv $TMP $DAT

exit 1



ip=$1       #ip="10.1.52.54"
i=$2        #i=10
file_dat="tension.dat"

test ! -f $file_dat && echo "0" > $file_dat # si no existe, se crea

intentos=`cat $file_dat`
echo "intentos = $intentos"

if [ $intentos -ge $i ]; then 
    echo ".... parando el sistema"
    echo "0" > $file_dat 
    halt  
    exit
fi

echo "continuamos"

r=`ping -c1 $ip | grep ttl`

longitud=${#r}
echo "longitud = $longitud"

let intentos=$intentos+1
test $longitud -gt 0 && let intentos=0   ## exito

#if [ $longitud = 0 ] ; then
#   echo "NO responde el ping"
#   let intentos=$intentos+1
#else 
#    echo "SI responde"
#    let intentos=0
#fi

echo "$intentos" > $file_dat
