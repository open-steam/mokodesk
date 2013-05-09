<?php
require_once("mokodesk_steam.php");

include("lars_tools.php");

session_name("bidowl_session");
session_start();
$loginName = ($_SESSION['user']) ? ($_SESSION['user']) : null;
$loginPwd = ($_SESSION['pass']) ? ($_SESSION['pass']) : null;
session_write_close();
$action = ($_POST['aktion']) ? ($_POST['aktion']) : null;
$id = ($_POST['id']) ? ($_POST['id']) : null;
$hidden = ($_POST['bidHidden']) ? ($_POST['bidHidden']) : false;
$type = ($_POST['larsType']) ? ($_POST['larsType']) : 0;


$steam = mokodesk_steam::connect(	$configServerIp,
                                    $configServerPort,
                                    $loginName,
                                    $loginPwd);

if( !$steam || !$steam->get_login_status() )
{
		echo json_encode(array(
					"success"=>false,
					"name"=>"Keine Verbindung"
				));
	exit();
}

switch ($action){
	case "image":
		$current_room = false;
		break;
	default:
		if (!$id){
			echo json_encode(array(
						"success"=>false,
						"name"=>"Unbekannter Fehler"
					));
			exit;
		}
		$current_room = steam_factory::get_object($GLOBALS["STEAM"]->get_id(),$id);
		if (!($current_room instanceof steam_container)){
			$current_room = $current_room->get_environment();
		}
		break;
}
require_once("../libary/php/derive_mimetype.php");

try{
    if( isset($_FILES["file"]) && $_FILES["file"]["name"] != "" ){
    	if ($_FILES['file']['size'] > 16000000
    		){
			echo json_encode(array(
			     		"success"=>false,
			        	"name"=>"Datei ist zu groß oder kann nicht gelesen werden"
			    	));
	    	$steam->disconnect();
			exit;
		}
		$baseFileName = $_FILES["file"]["name"];
		$description = trim($_POST['description']) ? trim($_POST['description']) : $baseFileName;
		$fileContent = file_get_contents( $_FILES["file"]["tmp_name"] );
		$mimetype = derive_mimetype( $_FILES["file"]["name"] );
		if ($fileContent === false){
			echo json_encode(array(
			     		"success"=>false,
			        	"name"=>"Kann Datei nicht von der Festplatte laden" //TODO: Sprachen
			    	));
	    	exit;
		}
		$newDocument = steam_factory::create_document($GLOBALS["STEAM"]->get_id(), tidyName($baseFileName), $fileContent, $mimetype, $current_room, tidyDesc($description) );
		$newDocument->set_attribute("bid:hidden", $hidden);
		$newDocument->set_attribute("LARS_TYPE", $type);

		if ($action === "image"){
			$steam->get_current_steam_user()->set_attribute("OBJ_ICON", $newDocument);
		}
		echo (json_encode(array(
			success => true
			,fileName=>$config_webserver_ip."/tools/get.php?object=".$newDocument->get_path()
			)));
	}
    else if( !isset($_FILES["file"]) || $_FILES["file"]["name"] != "" ){
    	echo json_encode(array(
     		"success"=>false,
        	"name"=>"Files nicht gesetzt"));
    }


} catch (Exception $e){
	error_log("exception: ".ErrorException::getTrace());
	echo json_encode(array(
     		"success"=>false,
        	"name"=>"LoginException"
    ));
}
    $steam->disconnect();
?>
