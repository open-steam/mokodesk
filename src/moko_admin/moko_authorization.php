<?php
require_once("mokodesk_steam.php");

$task = ($_POST['task'])?($_POST['task']):
    $LANG = 'de';
    include ("../etc/config.php");
    include ("$phpsteamApiRoot/get_current_steam_user.class.php");
    include ("lars_tools.php");
    include ("lars_lang.php");

    session_name("bidowl_session");
    session_start();
	$loginName = ($_SESSION['user']) ? ($_SESSION['user']) : null;
	$loginPwd = ($_SESSION['pass']) ? ($_SESSION['pass']) : null;
	$userGroup = ($_SESSION['group']) ? ($_SESSION['group']) : null;
	session_write_close();	
	
	$id = ($_POST['id']) ? ($_POST['id']) : null;
    
	$steam = mokodesk_steam::connect(	$configServerIp,
                                    $configServerPort,
                                    $loginName,
                                    $loginPwd);
  								  								
    if( !$steam || !$steam->get_login_status() )
    {
    	print (json_encode(array(success => false, name=>msg('NO_CONNECTION'))));
//        ErrorException::getTrace();
    	exit();
    }

switch($task){
    case "addAuthorization":
        addAuthorization($steam, $id);
    	$steam->disconnect();
        break;
    case "newUser":
        newUser($steam, $id);
    	$steam->disconnect();
        break;
}

function addAuthorization($steam, $id){
	global $MOKODESK_ALLOWED_GROUP_NAME;
	$group = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $id);
	$groupLink = steam_factory::create_link($GLOBALS["STEAM"]->get_id(), $group);
	$moko_authorization_group = steam_factory::groupname_to_object($GLOBALS["STEAM"]->get_id(), $MOKODESK_ALLOWED_GROUP_NAME);
	if (!($moko_authorization_group instanceof steam_group)){
    	print (json_encode(array(success => false, name=>"Gruppenhome nicht gefunden")));
    	exit;
	}
	$moko_authorization_home_folder = $moko_authorization_group->get_workroom();

//	Just for setting the rights manually
	$groupEveryone = steam_factory::get_object($GLOBALS["STEAM"]->get_id(),59);
	$moko_authorization_home_folder->set_sanction($groupEveryone, SANCTION_READ);
	
	if (!$moko_authorization_home_folder->check_access_write( $steam->get_login_user() )){
    	print (json_encode(array(success => false, name=>msg('NO_WRITE_ACCESS'))));
    	exit;
	}
	$groupLink->move($moko_authorization_home_folder);
    print (json_encode(array(success => true)));
}

function newUser($steam, $id){
	global $MOKODESK_ALLOWED_GROUP_NAME;
	$name = $_POST['name'];
	if (!$name){
	    print (json_encode(array(success => false)));
	    exit;
	}
	$steam_user = steam_factory::get_user($GLOBALS["STEAM"]->get_id(),$name);
	if (!$steam_user){
	    print (json_encode(array(success => false, name=>msg('NO_MATCH_USERNAME'))));
	    exit;
	}
	
	$userLink = steam_factory::create_link($GLOBALS["STEAM"]->get_id(), $steam_user);
	$moko_authorization_group = steam_factory::groupname_to_object($GLOBALS["STEAM"]->get_id(), $MOKODESK_ALLOWED_GROUP_NAME);
	if (!($moko_authorization_group instanceof steam_group)){
    	print (json_encode(array(success => false, name=>"Gruppenhome nicht gefunden")));
    	exit;
	}
	$moko_authorization_home_folder = $moko_authorization_group->get_workroom();
	if (!$moko_authorization_home_folder->check_access_write( $steam->get_login_user() )){
    	print (json_encode(array(success => false, name=>msg('NO_WRITE_ACCESS'))));
    	exit;
	}
	$userLink->move($moko_authorization_home_folder);
    print (json_encode(array(success => true)));
}
?>
