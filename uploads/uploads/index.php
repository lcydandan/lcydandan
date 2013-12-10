<?php
header("Content-type: text/html; charset=utf-8");
if (get_magic_quotes_gpc()) {
	function stripslashes_deep($value){
		$value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value); 
		return $value; 
	}
	$_POST = array_map('stripslashes_deep', $_POST);
	$_GET = array_map('stripslashes_deep', $_GET);
	$_COOKIE = array_map('stripslashes_deep', $_COOKIE); 
}
define('APP_DEBUG',true);
define('APP_NAME', 'AI9ME');
define('CONF_PATH','./AI9MEdata/conf/');
define('RUNTIME_PATH','./AI9MEdata/logs/');
define('TMPL_PATH','./themes/');
define('HTML_PATH','./AI9MEdata/html/');
define('APP_PATH','./AI9ME/');
define('CORE','./AI9ME/_Core');
require(CORE.'/AI9ME.php');
?>