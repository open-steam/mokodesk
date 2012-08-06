<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

include "etc/config.php";
//require_once("$phpsteamApiRoot/steam_connector.class.php");
//require_once("$phpsteamApiRoot/steam_request.class.php");
//require_once("$phpsteamApiRoot/steam_user.class.php");
//require_once("$phpsteamApiRoot/steam_object.class.php");
//require_once("$phpsteamApiRoot/steam_container.class.php");

//function __autoload($class_name) {
    //include "$phpsteamApiRoot/" . $class_name . '.class.php';
//}

// Or, using an anonymous function as of PHP 5.3.0
spl_autoload_register(function ($class) {
	global $phpsteamApiRoot;
	include_once "$phpsteamApiRoot/" . $class . '.class.php';
});

//function my_autoloader($class) {
	//require_once("../etc/config.php");
	//error_log("$phpsteamApiRoot" . $class . '.class.php');
	//include "$phpsteamApiRoot" . $class . '.class.php';
//}

//spl_autoload_register('my_autoloader');
	
class mokodesk_steam
{
	public static function connect( $server = STEAM_SERVER, $port = STEAM_PORT, $login = STEAM_GUEST_LOGIN, $password = STEAM_GUEST_PW )
	{
		try {
			$GLOBALS[ "STEAM" ] = steam_connector::connect( $server, $port, $login, $password );
			return $GLOBALS[ "STEAM" ];
		} catch (ParameterException $p) {
			throw new Exception("Missing or wrong params for server connection", E_CONNECTION);
		} catch( Exception $e ) {
				throw new Exception(
						"No connection to sTeam server ($server:$port).",
						E_CONNECTION
						);
		}
	}
}

?>
