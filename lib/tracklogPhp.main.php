<?php
function tracklog_autoloader($class_name){
	if (file_exists($class_name . '.class.php')) {
		require_once($class_name . '.class.php');
	}elseif (file_exists('formats/' . $class_name . '.class.php')) {
		require_once('formats/' . $class_name . '.class.php');
	}elseif(file_exists('exceptions/' . $class_name . '.class.php')){
		require_once('exceptions/' . $class_name . '.class.php');
	}
}

spl_autoload_register("tracklog_autoloader");

?>