<?php
error_reporting(E_ERROR);

require_once dirname(dirname(__FILE__)) . "/etc/config.php";
require_once "mokodesk_version.php";

$current_version = MOKODESK_VERSION;

spl_autoload_register(function ($class) {
	global $phpsteamApiRoot;
	include_once "$phpsteamApiRoot/" . $class . '.class.php';
});

class mokodesk_steam
{
	public static function connect( $server = STEAM_SERVER, $port = STEAM_PORT, $login = STEAM_GUEST_LOGIN, $password = STEAM_GUEST_PW )
	{
		try {
			$GLOBALS["STEAM"] = steam_connector::connect( $server, $port, $login, $password );
			return $GLOBALS["STEAM"];
		} catch (ParameterException $p) {
			throw new Exception("Missing or wrong params for server connection");
		} catch( Exception $e ) {
			throw new Exception("No connection to sTeam server ($server:$port).");
		}
	}
}

?>
