<?php 
//Receptora eventos panico celular wialon
error_reporting(E_ALL);
ini_set('display_errors', '1');

//variables
$host = 'udp://192.168.1.210';
$puerto = 9002;
$cod_evnt = 02;//panico

$unidad = $_GET['unidad'];
$lat = $_GET['lat'];//-34.547099
$long = $_GET['long'];//-58.496030
$time = $_GET['time'];//18.09.2019 14:01:35
$event = $_GET['event'];//02
$imei = $_GET['imei'];//352593083678514
$vel = $_GET['vel'];//0 km/h
$senti = $_GET['senti'];//Direccion: 150.00
$bat_lvl = $_GET['bint'];//Bateria: 61.00 %
$sat_count = $_GET['sat'];//Satelites: 10.0

/////////////////////////////////////////////////////////////////////
//RECEPCION
/////////////////////////////////////////////////////////////////////

echo "<br/> &nbsp; unidad: ". $unidad;
echo "<br/> &nbsp; latitud: ". $lat ;
echo "<br/> &nbsp; longitud: ". $long;
echo "<br/> &nbsp; time: ". $time;
echo "<br/> &nbsp; event: ". $event;
echo "<br/> &nbsp; imei: ". $imei;
echo "<br/> &nbsp; velocidad: ". $vel;
echo "<br/> &nbsp; sentido: ". $senti;
echo "<br/> &nbsp; batery level: ". $bat_lvl;
echo "<br/> &nbsp; satellite count: ". $sat_count;

$trama = $unidad.",".$lat.",".$long.",".$time.",".$event.",".$imei.",".$vel.",".$senti.",".$bat_lvl.",".$sat_count."\r\n";

$archivo = "receptoraLog.txt";

$gestor = fopen("receptoraLog.txt", "a+");
fwrite($gestor,$trama);
fclose($gestor);

//adecuacion
//quitar puntos de lat y long y 7 caracteres
$lat = substr(strtr($lat,".",""), 0, 8);
$long = substr(strtr($long,".",""), 0, 8);

//adecuar fecha y hora
$fecha_hora = strtr($time,".","");
$fecha_hora = strtr($fecha_hora," ","");
$fecha_hora = strtr($fecha_hora,":","");

//adecuar rumbo
$senti = explode(" ", $senti);
$senti = substr($senti[1], 0, 3);

//adecuar velocidad
$vel = strtr($vel," km/h","");

//adecuar satellites
$sat_count = strtr($sat_count,"Satelites: ","");
$sat_count = explode(".", $sat_count);
$sat_count = $sat_count[0];



/////////////////////////////////////////////////////////////////////
//Armado de trama para bykom
/////////////////////////////////////////////////////////////////////

$mensaje = '>$@BYKM,A1,'.$fecha_hora.''.$lat.''.$long.''.$senti.''.$vel.'0'.$sat_count.'00011000110000000000007F,0000,00,000,00,00,00.0,00.0,00000000,'.$cod_evnt.',L,000.00,00.0%,0,00.0,00.0;#2405;ID='.$imei.';*00<';

/////////////////////////////////////////////////////////////////////
//Enviamos a receptora de bykom el mensaje
/////////////////////////////////////////////////////////////////////

//Abrir coneccion y enviar mensaje
if(!$fp = fsockopen($host, 9002, $errno, $errstr, 1)) {
    echo "ERROR: $errno - $errstr<br />\n";
} else {
    fwrite($fp, $mensaje);
	echo "</br>Comunicacion ok:\n";
    echo fread($fp, 26);
    fclose($fp);
}
?>
