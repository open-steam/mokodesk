<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once("mokodesk_steam.php");

//print ("lskdhfklsdf");
//$o = array(
//     "success"=>true
//    ,"post"=>preg_replace("/\\n+/", "<br>", print_r($_POST, 1))
//    ,"files"=>preg_replace("/\\n+/", "<br>", print_r($_FILES, 1))
//);
//echo json_encode($o);
//error_log("upload aufruf");
//error_reporting(6143);
//if(isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH']>2097152)
//	error_log('Upload FAILED, file is too large !'.$_SERVER['CONTENT_LENGTH']);
// error_log(preg_replace("/\\n+/", "<br>", print_r($_POST, 1)));
// error_log(preg_replace("/\\n+/", "<br>", print_r($_FILES, 1)));


// error_log(print_r($_POST, 1));
// error_log($_FILES['file']['size']);
// error_log(print_r($_FILES, 1));
// include("../etc/config.php");
//    include("$phpsteamApiRoot/get_current_steam_user.class.php");
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
//        ErrorException::getTrace();
			echo json_encode(array(
			     		"success"=>false,
			        	"name"=>"Keine Verbindung"
			    	));
//        print("No server connection!");
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
    require_once("includes/derive_mimetype.php");

try{
    if( isset($_FILES["file"]) && $_FILES["file"]["name"] != "" ){
// 		error_log(print_r($_FILES, 1));
    	if ($_FILES['file']['size'] > 16000000 
//    		|| $_FILES['file']['size'] == 0 
    		){
			echo json_encode(array(
			     		"success"=>false,
			        	"name"=>"Datei ist zu groß oder kann nicht gelesen werden"
			    	));
	    	$steam->disconnect();
			exit;
		}
//		error_log("File Size is: ".$_FILES['file']['size']);
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
//		error_log($_FILES["file"]["name"]); 
		$newDocument = steam_factory::create_document($GLOBALS["STEAM"]->get_id(), tidyName($baseFileName), $fileContent, $mimetype, $current_room, tidyDesc($description) );
		$newDocument->set_attribute("bid:hidden", $hidden);
		$newDocument->set_attribute("LARS_TYPE", $type);
		
		if ($action === "image"){
			$steam->get_login_user()->set_attribute("OBJ_ICON", $newDocument);
		}
		echo (json_encode(array(//TODO: Dateiname mit URL
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
//    		"exception"=>ErrorException::getTrace()
    ));
}
    $steam->disconnect();
	
	
	
//	$fileLocation = $fileLocation ? $fileLocation : $_POST['location'];
////	print $fileLocation."---";
////	print urlencode($fileLocation);
//	$baseFileName =  basename($fileLocation);
//	$description = trim($_POST['description']) ? utf8_decode($_POST['description']) : tidyDesc($baseFileName);
//	$current_room = steam_factory::get_object($GLOBALS["STEAM"]->get_id(),$id);
//	$fileLocation = utf8_decode($fileLocation);
//	
//	
////	print $fileLocation;
////	$fileLocation = stripslashes($fileLocation);
////	$fileContent = file_get_contents( $fileLocation );
//	$fileContent = file_get_contents( urlencode($fileLocation) );
//	print $fileContent;
//	$mimetype = derive_mimetype( $baseFileName );
//	$inventory = $current_room->get_inventory();
//	
//	$newDocument = steam_factory::create_document($GLOBALS["STEAM"]->get_id(), tidyName($baseFileName), $fileContent, $mimetype, $current_room, tidyDesc($description) );
//
//	$newId = $newDocument->get_id();
//
//	if ($print){
//		print (json_encode(array(success => true, newId => $newId, message => "Neues Dokument erstellt")));
//	}
////}

?>
