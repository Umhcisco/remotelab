#!/bin/sh


#echo 'shutdown -P now' | ssh root@192.168.100.250
echo 'halt' | ssh root@192.168.100.250 &


sleep 20

##Apagamos el outlet que da corriente al PC3
# paro_marcha.sh   (num outlet )   (on / Off)
#1 - ON
#2 - OFF

#/var/www/paro_marcha.sh 7 2
/var/www/paro_marcha.sh POD3 OFF


