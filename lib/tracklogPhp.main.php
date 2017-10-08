<?php
// function tracklog_autoloader($class_name){
	// if (file_exists($_SERVER["DOCUMENT_ROOT"].'/lib/'.$class_name . '.class.php')) {
	// 	require_once($_SERVER["DOCUMENT_ROOT"].'/lib/'.$class_name . '.class.php');
	// }elseif (file_exists($_SERVER["DOCUMENT_ROOT"].'/lib/formats/' . $class_name . '.class.php')) {
	// 	require_once($_SERVER["DOCUMENT_ROOT"].'/lib/formats/' . $class_name . '.class.php');
	// }elseif(file_exists($_SERVER["DOCUMENT_ROOT"].'/lib/exceptions/' . $class_name . '.class.php')){
	// 	require_once($_SERVER["DOCUMENT_ROOT"].'/lib/exceptions/' . $class_name . '.class.php');
	// }
// }

// spl_autoload_register("tracklog_autoloader");
require_once("lib/tracklog.class.php");
require_once("lib/trackPoint.class.php");
require_once("lib/formats/CSV.class.php");
require_once("lib/formats/GeoJson.class.php");
require_once("lib/formats/GPX.class.php");
require_once("lib/formats/KML.class.php");
require_once("lib/formats/TCX.class.php");
require_once("lib/exceptions/tracklogPhpException.class.php");

?>