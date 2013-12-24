#!/usr/bin/perl
################################################
### Mensajes de aviso
###
### Script controlado por 'cron'
### (c) 2013
################################################

use DBI;
use DateTime::Format::ISO8601; 
use MIME::Lite;

##################################################
sub SendMailObsoleto {
    my ($from, $to, $cc, $subject, $body)=@_;
    
    printf ("from=$from  to=$to   cc=$cc  subject=$subject  body=$body\n");
    
    $mailer = Mail::Mailer->new("sendmail");
    $mailer -> open({From => $from,
		     To   => $to,
		     Cc   => $cc,
		     Subject => $subject,
		    })
	or Msg("Can't open $!");
    print $mailer $body;
    $mailer -> close()
}

##################################################
sub SendMail {
    my ($from, $to, $cc, $subject,$type,$body)=@_;
    
    my $msg = MIME::Lite->new(
	From    => $from,
	To      => $to,
	Cc      => $cc,
	Subject => $subject,
	Type    => $type,
	Data    => $body
	);
    
    $msg->send();
}
    
##################################################
## Programa Principal
##################################################

#######################################################################
### Configuracion de la BD
$DBServerName='localhost';
$DBName='radius';
$DBUserName='root';
$DBPassword='vXq@z*Ab33';
$db=DBI->connect("dbi:mysql:host=$DBServerName;database=$DBName;user=$DBUserName;password=$DBPassword");

##################################################
### Obtenemos la fecha/hora del sistema
my ($hour,$mday,$month,$year) = (localtime(time))[2, 3, 4, 5];

$HoraPar = ($hour % 2 == 0 ) ? 1 : 0;

$hour= int($hour/2)*2;
$month += 1;
$year += 1900;

# TurnoVigente contiene el turno en vigor segun la hora
my $TurnoVigente = DateTime-> new(
    year => $year,
    month => $month,
    day => $mday,
    hour => $hour,
    minute => 00,
    second => 00,
    time_zone => 'UTC',
    ); 

print "Inicio del turno=$hour:00:00 \n hora par?=$HoraPar";

# El script se ejecuta todas las horas
# hay que distinguir horas pares e impares
if ($HoraPar) { #### ejecutamos en horas pares
    $TurnoAnterior = $TurnoVigente;
    $TurnoAnterior-> add( hours => -2 );  ## turno anterior (2 h)    
    
    $fechaOK=$TurnoAnterior->ymd('-');
    $horaOK=$TurnoAnterior->hms;
    
    print "\n Turno anterior=$horaOK";

    ################################################
    ### seleccionar los que NOOOOOOOOOOOO han aprovechado el turno estado_reserva=0    
    ################################################
    $DBTable='reservas';
    $sth=$db->prepare("SELECT * FROM $DBTable WHERE fecha_reserva='$fechaOK' AND horario_reserva='$horaOK' AND estado_reserva=0");
    $sth->execute;
    
    while ( @registro=$sth->fetchrow_array()) {
        $IdUser=$registro[1];
	
        $DBTable='datos_pers';
        $sth2=$db->prepare("SELECT * FROM $DBTable WHERE user_id=$IdUser");
        $sth2->execute;
	
        @registro2=$sth2->fetchrow_array();
	
        $nombre=$registro2[2];
        $from="RemoteLab";
        $to=$registro2[4];
        $cc='remotelab.umh@gmail.com';
        $type= 'text/html',
        $subject='RemoteLab: aviso de turno perdido';
	
        my $fechaRsv = $TurnoAnterior->dmy('-');
	$body=
'
<html>
<head>
<title>RemoteLab</title>
</head>
<body>
<h3>Estimad@ '.$nombre.'!</h3>
<p>
<b>Te recordamos que no has aprovechado el turno de laboratorio que ten&iacute;as reservado a las '.$horaOK.' del '.$fechaRsv.'.</b>
</p>
<p> Cada vez que no utilizas un turno reservado una hada del bosque pierde sus alas 
<b></b>
</p>
<p>
<i>Esperamos verte por aqu&iacute; muy pronto.</i>
</p>
Saludos cordiales,
<p>________________<p>
<b>(c) RemoteLab</b>
</body>
</html>
'
;
        SendMail($from,$to,$cc,$subject,$type,$body);
    }
    ################################################
    ### seleccionar los que SIIIIIIIIII han aproechado el turno estado_reserva=2
    ################################################
    $DBTable='reservas';
    $sth=$db->prepare("SELECT * FROM $DBTable WHERE fecha_reserva='$fechaOK' AND horario_reserva='$horaOK' AND estado_reserva=2");
    $sth->execute;

    while ( @registro=$sth->fetchrow_array()) {
        $IdUser=$registro[1];

        $DBTable='datos_pers';
        $sth2=$db->prepare("SELECT * FROM $DBTable WHERE user_id=$IdUser");
        $sth2->execute;

        @registro2=$sth2->fetchrow_array();

        $nombre=$registro2[2];
        $from="RemoteLab";
        $to=$registro2[4];
        $cc='remotelab.umh@gmail.com';
        $type= 'text/html',
        $subject='RemoteLab: agradecemos su visita';

        my $fechaRsv = $TurnoAnterior->dmy('-');
	$body=
'
<html>
<head>
<title>RemoteLab</title>
</head>
<body>
<h3>Estimad@ '.$nombre.'!</h3>
<p>
<b>Te agradecemos que hayas aprovechado satisfactoriamente tu turno de laboratorio de las '.$horaOK.' del '.$fechaRsv.'.</b>
</p>
<p> A partir de este momento, una hada del bosque habr&aacute; recuperado sus alitas.
<b></b>
</p>
<p>
<i>Esperamos verte por aqu&iacute; muy pronto.</i>
</p>
Saludos cordiales,
<p>________________<p>
<b>(c) RemoteLab</b>
</body>
</html>
'
;
        SendMail($from,$to,$cc,$subject,$type,$body);
    }
} else {    #### ejecutamos en horas impares
    ### Vamos a anunciar las reservas del proximo turno
    $TurnoProximo = $TurnoVigente;
    $TurnoProximo-> add( hours => 2 );  ## sigueinte turno (2 h)
    
    $fechaOK=$TurnoProximo->ymd('-');
    $horaOK=$TurnoProximo->hms;
    
    $DBTable='reservas';
    $sth=$db->prepare("SELECT * FROM $DBTable WHERE fecha_reserva='$fechaOK' AND horario_reserva='$horaOK'");
    $sth->execute;
    
    while ( @registro=$sth->fetchrow_array()) {
	$IdUser=$registro[1];
	
	$DBTable='datos_pers';
	$sth2=$db->prepare("SELECT * FROM $DBTable WHERE user_id=$IdUser");
	$sth2->execute;
	
	@registro2=$sth2->fetchrow_array();
	
	$nombre=$registro2[2];
	$from="RemoteLab";
	$to=$registro2[4];
	$cc='remotelab.umh@gmail.com';
	$type= 'text/html',
	$subject='RemoteLab: aviso de turno';
	
	my $fechaRsv = $TurnoProximo->dmy('-');
	
	$body='
<html>
<head>
<title>RemoteLab: Aviso de reserva en siguiente turno</title>
</head>
<body>
<h3>Estimad@ '.$nombre.'!</h3>
<p>
<b>Te recordamos que tienes una reserva que comenzar&aacute; en breve,</b>
</p>
<p>
<b>con fecha '.$fechaRsv.' e inicio a las '.$horaOK.' horas.</b>
</p>
<p>
<i>Esperamos verte por aqu&iacute;.</i>
</p>
Saludos cordiales,
<p>________________<p>
<b>(c) RemoteLab</b>
</body>
</html>
'
;
	SendMail($from,$to,$cc,$subject,$type,$body);
    }
}

$sth=finish;
$sth2=finish;
$db->disconnect;   ### por si las flies  

################################################
### This is the end
################################################
