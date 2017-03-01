<?php
/*
Polls Upstream Signal to noise ratio for the cable modem
on Casa C2200/Casa C3200 CMTS
*/

// cable modem IP Address
$cmip = "";

//CMTS IP Address
$cmts = "";

//SNMP Community String
$community = "";

//OID to pull modem ID
$modemidoid = "1.3.6.1.2.1.10.127.1.3.7.1.2.";
//USSNR OID missing Modem ID
$ussnroid = "1.3.6.1.2.1.10.127.1.3.3.1.13.";
function get_mac_decimal($mac) {
    $clear_mac = preg_replace('/[^0-9A-F]/i','',$mac);
    $mac_decimal = array();
    for ($i = 0; $i < strlen($clear_mac); $i += 2 ):
        $mac_decimal[] = hexdec(substr($clear_mac, $i, 2));
    endfor;
    return implode('.',$mac_decimal);
}
function putinplace($string=NULL, $put=NULL, $position=false)
{
    $d1=$d2=$i=false;
    $d=array(strlen($string), strlen($put));
    if($position > $d[0]) $position=$d[0];
    for($i=$d[0]; $i >= $position; $i--) $string[$i+$d[1]]=$string[$i];
    for($i=0; $i<$d[1]; $i++) $string[$position+$i]=$put[$i];
    return $string;
}
if(isset($cmip)){

//SNMP GET to retrieve mac address via IP address
	$macaddy = @snmpget("$cmip", "$community", "1.3.6.1.2.1.4.22.1.2.2");

//Mac Addy converted to decimal and stripped first 12 chars
	$decimalmac = get_mac_decimal(substr($macaddy, 12));

//Combine the 2
	$modemidoid = $modemidoid . $decimalmac;

//SNMP GET for modem ID
	$modemid = @snmpget("$cmts", "$community", "$modemidoid");

//Clean USSNR request
	$cleansnr = $ussnroid . substr($modemid, 9);

//SNMP GET for USSNR
	$ussnr = @snmpget("$cmts", "$community", "$cleansnr");
//echo substr($modemid, 9);

$put = ".";
$position = 2;
print_r(putinplace(substr($ussnr, 9), $put, $position));
//echo substr($ussnr, 9);
}
?>


