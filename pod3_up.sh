#!/bin/sh

#paromarcha.sh (numoutlet) (estado)
#estado 1:: ON
#estado 2:: OFF
#/var/www/paro_marcha.sh 7 1
/var/www/paro_marcha.sh POD3 ON


#levantamos POD3 segun dir MAC de eth0
#wakeonlan 60:a4:4c:3e:19:98
