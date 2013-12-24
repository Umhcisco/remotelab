#!/bin/bash
########################################################################
########################################################################
###:::::::::::::::::::::::::::::::::::::::::::::::::::::::
### ### cambios en el paro_marcha.sh
###
## parametro $1 sera el numero de pod, 
## o el grupo de Routers & Switches asociados a un pod
## --------------------------------------------------------------
##  POD1: PC1 se deberia poner en el outlet 5, 
##        y asi tambien se controlaria con el snmp del SAI
##  POD2: PC2 -> outlet 6
##  POD3: PC3 -> outlet 7
##
##  RTSW1: Routers & Switches asociados al POD1
##  RTSW2: Routers & Switches asociados al POD2
##  RTSW3: Routers & Switches asociados al POD3
####
####
## parametro $2 sera ON u OFF
## o sea, encender o apagar el outlet correspondiente en el APC
## ---------------------------------------------------------------
##  ON:  operacion 1
##  OFF: operacion 2
##
### 2013
###::::::::::::::::::::::::::::::::::::::::::::::::::::::: 
equipo=$1
operacion=$2

dispositivo=${equipo:0:(-1)}
numero=${equipo:(-1)}
#echo $dispositivo
#echo $numero

###### obtenemos el outlet correspondiente
if [ "$dispositivo" == "POD" ]; then     ##PC fisicos
  let outlet=$numero+4
  #echo $outlet
else                             ##Routers & Switches
  outlet=$numero
  #echo $outlet
fi

#########################
##echo $operacion

if [ "$operacion" == "ON" ]; then
  ##echo "encendido!!!"
  status=1
  #echo $status
elif [ "$operacion" == "OFF" ]; then
  ##echo "apagado!!!"
  status=2
  #echo $status
else
  echo "error en el estado!!!"
fi

snmpset -c remotelab -v 1 192.168.98.253 1.3.6.1.4.1.318.1.1.12.3.3.1.1.4.$outlet i $status


###::::::::::::::::::::::::::::::::::::::::::::::::::::::: 
### ...this is the end
###:::::::::::::::::::::::::::::::::::::::::::::::::::::::
