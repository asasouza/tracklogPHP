<?php
function tracklog_autoloader($class_name){
	if (file_exists('lib/'.$class_name . '.class.php')) {
		require_once('lib/'.$class_name . '.class.php');
	}elseif (file_exists('lib/formats/' . $class_name . '.class.php')) {
		require_once('lib/formats/' . $class_name . '.class.php');
	}elseif(file_exists('lib/exceptions/' . $class_name . '.class.php')){
		require_once('lib/exceptions/' . $class_name . '.class.php');
	}
}

spl_autoload_register("tracklog_autoloader");

?>