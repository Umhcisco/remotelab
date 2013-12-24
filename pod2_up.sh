#!/bin/sh

#paromarcha.sh (numoutlet) (estado)
#estado 1:: ON
#estado 2:: OFF
##/var/www/paro_marcha.sh 6 1
/var/www/paro_marcha.sh POD2 ON


#levantamos POD2 segun dir MAC de eth0
#wakeonlan 60:a4:4c:3f:bd:f4

