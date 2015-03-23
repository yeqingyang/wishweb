<?php
class AddressDef{
	public static $CITY=array(
		1=>'北京',
		2=>'广州',
	);

	public static $DISTRICT=array(
		1=>array(
			1=>'上地东里',
			2=>'上地西里',
			3=>'南湖西里'
			),
		2=>array(
			1=>'亚运城'
			),
	);

	public static function toJson(){
		$json = array('city'=>self::$CITY,'district'=>self::$DISTRICT);
		$jsonstring = json_encode($json);
		return $jsonstring;
	}
}
$file = fopen("../../public/js/address.js","w");
$address = AddressDef::toJson();
fwrite($file, $address);
fclose($file);
