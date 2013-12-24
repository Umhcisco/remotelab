#!/bin/sh

#echo 'shutdown -P now' | ssh root@192.168.100.251
echo 'halt' | ssh root@192.168.100.251 &


sleep 20

##Apagamos el outlet que da corriente al PC2
# paro_marcha.sh   (num outlet )   (on / Off)
#1 - ON
#2 - OFF

#/var/www/paro_marcha.sh 6 2
/var/www/paro_marcha.sh POD2 OFF
