<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once("mokodesk_steam.php");

$task = ($_POST['task']) ? ($_POST['task']) : null;

	include("../etc/config.php");
    include("$phpsteamApiRoot/steam_connector.class.php");
    include("lars_tools.php");
	include("lars_lang.php");

session_name("bidowl_session");
session_start();    
	$loginName = ($_SESSION['user']) ? ($_SESSION['user']) : null;
	$loginPwd = ($_SESSION['pass']) ? ($_SESSION['pass']) : null;
	$LANG = ($_SESSION['language']) ? ($_SESSION['language']) : "de";
session_write_close();	
	
	$id = ($_POST['id']) ? ($_POST['id']) : null;
    
	$steam = mokodesk_steam::connect(	$configServerIp,
                                    $configServerPort,
                                    $loginName,
                                    $loginPwd);
  								  								
    if( !$steam || !$steam->get_login_status() )
    {
//        ErrorException::getTrace();
    	print("No server connection!");
	    exit();
    }

switch($task){
    case "saveEditNewName":
        saveEditNewName($steam, $id);
    	$steam->disconnect();
        break;
	case "saveEdit":
        setContentHtml($steam, $id);
    	$steam->disconnect();
        break;
	case "saveTitleAndTextMessage":
        saveTitleAndTextMessage($steam, $id);
    	$steam->disconnect();
        break;
    case "edit":
        getHtml($steam, $id);
        break;
    default:
    	$steam->disconnect();
    	print (json_encode(array(success => false)));
        break;
}//end switch


function setContentHtml($steam, $id){
include("../etc/config.php");
$content = ($_POST['textField']) ? ($_POST['textField']) : null;
$origContent = ($_POST['origValue']) ? ($_POST['origValue']) : null;
  $document = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $id );
  $access_write = $document->check_access_write( $steam->get_current_steam_user() );
  $access_read = $document->check_access_read( $steam->get_current_steam_user() );

	$content = stripslashes($content);
    $content = preg_replace('#src="' . $config_webserver_ip . '/tools/get.php\?object=([a-z0-9.\- _\/]*)"#iU', 'src="$1"', $content);
    $current_path = substr( $document->get_path(), 0, strrpos($document->get_path(), "/")) . "/";
    $content = preg_replace('#'.$current_path.'#iU', '', $content);

  if(!$access_write){
      print (json_encode(array(success => false, message => "Keine Berechtigung zum Schreiben des Dokuments")));
      break;
      exit;
  }
  $serverContent = $document->get_content();
  $serverContent = $serverContent ? $serverContent :"";
  $serverContent = stripslashes($serverContent);
  $serverContent = preg_replace('#src="' . $config_webserver_ip . '/tools/get.php\?object=([a-z0-9.\- _\/]*)"#iU', 'src="$1"', $serverContent);
    $origContent = stripslashes($origContent);
	$origContent = preg_replace('#src="' . $config_webserver_ip . '/tools/get.php\?object=([a-z0-9.\- _\/]*)"#iU', 'src="$1"', $origContent);
    $origContent = preg_replace('#'.$current_path.'#iU', '', $origContent);
// echo $serverContent."\n";
// echo $origContent."\n";
  
  if($origContent == $serverContent)
  {
    $result = $document->set_content($content);
    print (json_encode(array(success => true, message => $result)));
  } else {
    print (json_encode(array(success => false,
    			message => "Originalinhalt hat sich geändert. Keine Änderungen vorgenommen!",
    			changed => true,
    			oldName => $document->get_attribute("OBJ_DESC"),
    			oldId => $document->get_id()
    			)));
  }
	
}

function saveEditNewName($steam, $id){
$content = ($_POST['textField']) ? ($_POST['textField']) : null;
$name = ($_POST['name']) ? $_POST['name'] : "Neu";

  $document = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $id );
  $current_folder = $document->get_environment();
  $newDocument = steam_factory::create_document($GLOBALS["STEAM"]->get_id(), tidyName($name).".html", stripslashes($content), "text/html", $current_folder, tidyDesc($name) );
  
  print (json_encode(array(success => true, message => $result)));
	
}

function saveTitleAndTextMessage($steam, $id){
$content = ($_POST['textField']) ? ($_POST['textField']) : null;
$name = ($_POST['name']) ? $_POST['name'] : "Neu";
  $document = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $id );
  if ($document instanceof steam_link){$document = $document->get_link_object();}
  if ($document instanceof steam_container){
  	$current_folder = $document;
########
	$attribute_names = $current_folder->get_attribute_names();
	if (in_array("FOLDER_DISCUSSION", $attribute_names)){
		$discussion_folder = $current_folder->get_attribute("FOLDER_DISCUSSION");
		if (!($discussion_folder->check_access_write($steam->get_current_steam_user()))){
			echo json_encode(array(success => false, name=>msg('NO_WRITE_ACCESS')));
			exit;
		}
		//TODO: Übergangsweise wird das Attribut erstellt.
		$discussion_folder->set_attribute("OBJ_TYPE", "LARS_MESSAGES");
	} else {
		$discussion_folder = steam_factory::create_container($GLOBALS["STEAM"]->get_id(), tidyName("Mitteilungen_zu_".$current_folder->get_name()), $current_folder, tidyDesc(msg('MSG_FOR').$current_folder->get_attribute("OBJ_DESC")));
		$discussion_folder->set_attribute("LARS_HIDDEN", true);
		$discussion_folder->set_attribute("OBJ_TYPE", "LARS_MESSAGES");
		$current_folder->set_attribute("FOLDER_DISCUSSION", $discussion_folder);
	}
	$discussion_folder = $current_folder->get_attribute("FOLDER_DISCUSSION");
	if (!($discussion_folder->check_access_write($steam->get_current_steam_user()))){
		echo json_encode(array(success => false, name=>msg('NO_WRITE_ACCESS')));
		exit;
	}
#####  
  	$newDocument = steam_factory::create_document($GLOBALS["STEAM"]->get_id(), tidyName($name).".html", stripslashes($content), "text/html", $discussion_folder, tidyDesc($name) );
  } else {
  	$document->set_attribute("OBJ_DESC", tidyDesc($name)); //Name wird nicht geändert bei erneutem editieren
//  	$document->set_name(tidyName($name).".html"); //TODO: error_log bid_owl
//[Sun Feb 15 22:37:36 2009] [error] [client 131.234.252.13] PHP Fatal error:  Uncaught exception 'steam_exception' with message 'Error during data transfer. COAL_ERROR : args[0]=1 args[1]=Cannot update identifier - object with name exists (/kernel/proxy.pike(492940/PSTAT_SAVE_OK))!' in /var/www/lars/PHPsTeam/steam_connector.class.php:690\nStack trace:\n#0 /var/www/lars/PHPsTeam/steam_connector.class.php(599): steam_connector->send_command(Array)\n#1 /var/www/lars/PHPsTeam/steam_connector.class.php(970): steam_connector->command(Object(steam_request))\n#2 /var/www/lars/PHPsTeam/steam_object.class.php(584): steam_connector->predefined_command(Object(steam_document), 'set_attributes', Array, 0)\n#3 /var/www/lars/PHPsTeam/steam_object.class.php(490): steam_object->steam_command(Object(steam_document), 'set_attributes', Array, 0)\n#4 /var/www/lars/PHPsTeam/steam_object.class.php(453): steam_object->set_attributes(Array, 0)\n#5 /var/www/lars/PHPsTeam/steam_object.class.php(360): steam_object->set_attribute('OBJ_NAME', 'Fehlermeldung.h...', 0)\n#6 /var/www/lars/lars_edit.php(138): steam_object->set_n in /var/www/lars/PHPsTeam/steam_connector.class.php on line 690, referer: http://www.bid-owl.de/lars/lars2/index.html
  	$document->set_content(stripslashes($content));
  }
  
  
  print (json_encode(array(success => true, name => msg('MSG_SAVED'))));
	
}

function getHtml($steam, $id){
	include("../etc/config.php");
	$object = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $id);
	if ($object instanceof steam_document){
//		$current_path = $object->get_path();
	    $current_path = substr( $object->get_path(), 0, strrpos($object->get_path(), "/")) . "/";
		$content = $object->get_content();
		$content = $content ? $content : ""; 
		$content = preg_replace('/\n\n/', '</p>', $content);
		$content = stripslashes($content);
//		echo $content;
//	    $content = preg_replace('/src="\/([a-z0-9.\-\%_\/]*)"/iU', 'src="' . $config_webserver_ip . '/tools/get.php?object=$1"', $content);
//    	$content = preg_replace('/src="+(?!http)([a-z0-9.\-_\/]*)"/iU', 'src="' . $config_webserver_ip . '/tools/get.php?object=' . $current_path . '$1"', $content);
    	$content = preg_replace('/src="([a-z0-9.\- _\/]*)"/iU', 'src="' . $config_webserver_ip . '/tools/get.php?object=' . $current_path . '$1"', $content);
//    	$content = preg_replace('#src="([a-z0-9.\-_\/]*)'.$current_path.'([a-z0-9.\-_\/]*)"#iU', 'src="' . $config_webserver_ip . '/tools/get.php?object=$1' . $current_path . '$1"', $content);
		echo json_encode(array(success=>true, html=>$content));
	} else {
		print (json_encode(array(success => false)));
	}
	$steam->disconnect();
	
}  
?>
