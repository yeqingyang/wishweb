<?php
require_once("../def/AddressDef.php");
$file = fopen("../../public/js/address.js","w");
$json = array('city'=>AddressDef::$CITY,'district'=>AddressDef::$DISTRICT);
$jsonstring = json_encode($json,JSON_UNESCAPED_UNICODE);
$address=$jsonstring;
echo $address;
fwrite($file, $address);
fclose($file);
