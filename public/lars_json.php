<?php
require_once("mokodesk_steam.php");
$task = ($_POST['task'])?($_POST['task']):
    null;
    $LANG = 'de';
    //include ("../etc/config.php");
    //include ("$phpsteamApiRoot/get_current_steam_user.class.php");
    include ("lars_tools.php");
    include ("lars_lang.php");

    session_name("bidowl_session");
    session_start();
	$loginName = ($_SESSION['user']) ? ($_SESSION['user']) : null;
	$loginPwd = ($_SESSION['pass']) ? ($_SESSION['pass']) : null;
	$userGroup = ($_SESSION['group']) ? ($_SESSION['group']) : null;
	$LANG = ($_SESSION['language']) ? ($_SESSION['language']) : "de";
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
    case "saveEdit":
        setContentHtml($steam, $id);
    	$steam->disconnect();
        break;
    case "edit":
        getHtml($steam, $id);
        break;
    case "view":
        getView($steam, $id);
		$steam->disconnect();
        break;
    case "getLernstand":
    	getLernstand($steam, $id);        
        break;
    case "newUploadFile":
        uploadFile($steam, $id);
		$steam->disconnect();
        break;
    case "copyIntoPackage":
        copyIntoPackage($steam, $id);
		$steam->disconnect();
        break;
    case "copyPackage":
        copyPackage($steam, $id);
		$steam->disconnect();
        break;
    case "copyFolder":
        copyFolder($steam, $id);
		$steam->disconnect();
        break;
    case "copyFile":
        copyFile($steam, $id);
		$steam->disconnect();
        break;
    case "copyFilePackage":
        copyFilePackage($steam, $id);
		$steam->disconnect();
        break;
    case "archivePackage":
        archivePackage($steam, $id);
		$steam->disconnect();
        break;
    case "archiveGroupPackage":
        archiveGroupPackage($steam, $id);
		$steam->disconnect();
        break;
    case "newAssignment":
        newAssignmentPackage($steam, $id);
//[Mon Feb 16 16:02:59 2009] [error] [client 89.245.83.167] PHP Fatal error:  Uncaught exception 'steam_exception' with message 'Error during data transfer. COAL_ERROR : args[0]=1 args[1]=No name set for object !' in /var/www/lars/PHPsTeam/get_current_steam_user.class.php:690\nStack trace:\n#0 /var/www/lars/PHPsTeam/get_current_steam_user.class.php(599): get_current_steam_user->send_command(Array)\n#1 /var/www/lars/PHPsTeam/get_current_steam_user.class.php(970): get_current_steam_user->command(Object(steam_request))\n#2 /var/www/lars/PHPsTeam/steam_factory.class.php(338): get_current_steam_user->predefined_command(Object(steam_object), 'execute', Array, 0)\n#3 /var/www/lars/PHPsTeam/steam_factory.class.php(385): steam_factory::create_object(Object(get_current_steam_user), '', 2, Object(steam_container), Array)\n#4 /var/www/lars/lars_json.php(718): steam_factory::create_container(Object(get_current_steam_user), '', Object(steam_container), '')\n#5 /var/www/lars/lars_json.php(78): newAssignmentPackage(Object(get_current_steam_user), '481879')\n#6 {main}\n  thrown in /var/www/lars/PHPsTeam/get_current_steam_user.class.php on line 690, referer: http://www.bid-owl.de/lars/lars2/index.html
        break;
    case "getAssignment":
        getAssignmentPackage($steam, $id);
        break;
    case "getUserIcon":
        getUserIcon($steam, $name);
        break;
    case "getNewItems":
        getNewItems($steam);
        break;
    case "newFolder":
        newTopicsFolder($steam, $id);
        break;
    case "newFolderLinks":
        newFolderLinks($steam, $id);
        break;
    case "newSchueler":
        newSchueler($steam);
        break;
    case "newLink":
    	newLink($steam, $id);
    	break;
    case "changeTitle":
        changeTitle($steam);
        break;
	case "customImage":
        getCustomImage($steam);
        break;
    case "saveAppointments":
        saveAppointments($steam);
        break;
	case "getAppointments":
        getAppointments($steam);
        break;
    case "changeState":
        setLarsPackageState($steam, $id);
        break;
    case "newCustomImage":
        setCustomImage($steam);
        break;
    case "deleteItem":
        deleteItem($steam, $id);
        break;
    case "emptyTrash":
        emptyTrash($steam);
        break;
    case "update":
		saveData($steam, $id);
        break;
	case "getDesktopAbo":
        getDesktopAbo($steam, $id);
        break;
	case "updateSubscription":
        updateSubscription($steam, $id);
        break;
	case "getSchuelerTopics":
        getSchuelerTopics($steam, $id);
        break;
	case "getOwnStudents":
        getOwnStudents($steam);
        break;
	case "getDiscussion":
        getDiscussion($steam, $id);
        break;
	case "newMessage":
        newMessage($steam, $id);
        break;
	case "newText":
        newText($steam, $id);
        break;
	case "getAboGroups":
		getAboGroups($steam, $id);
		break;
	case "getRightsGroups":
		getRightsGroups($steam, $id);
		break;
	case "updateGroup":
		updateGroup($steam, $id);
		break;
	case "getGroupsTree":
		getGroupsTree($steam, $id);
		break;
	case "addBuddy":
		addBuddy($steam, $id);
		break;
	case "deleteBuddy":
		deleteBuddy($steam, $id);
		break;
	case "errorReport":
		errorReport($steam, $id);
		break;
	case "changeHeight":
		changeHeight($steam);
		break;
		
//    default:
//    	$steam->disconnect();
//    	print (json_encode(array(success => false)));
//        break;
}//end switch

function errorReport($steam){
	$message = $_POST['message'];
	$discussion_folder = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), 430552);
	$newMessageObject = steam_factory::create_document($GLOBALS["STEAM"]->get_id(), "Fehlermeldung", $message, "text/html", $discussion_folder, "Fehlermeldung" );
	$newID = $newMessageObject->get_id();
	print json_encode(array(success => true, newID => $newID));
}
function newLink($steam, $id){
	require_once("includes/derive_url.php");
	$name = trim($_POST['name']);
	$url = trim($_POST['url']);
	if ($name == "" || $url == "" ) {
	    print (json_encode(array(success => false)));
	    exit;
	}
	$url = derive_url($url);
	$steam_user = $steam->get_current_steam_user();
	if ($id){
		$link_folder = 	steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $id);
	} else {
		$link_folder = $steam_user->get_attribute("USER_BOOKMARKROOM");
	}
    $newLinkObject = steam_factory::create_docextern( $GLOBALS["STEAM"]->get_id(), tidyName($name), $url, $link_folder, tidyDesc($name) );
    print (json_encode(array(success => true)));
	$steam->disconnect();
}
function changeTitle($steam){
	$title = $_POST['title'];
	$current_user = $steam->get_current_steam_user();
	$current_user->set_attribute("LARS_TITLE", tidyDesc($title));
    print (json_encode(array(success => true, title => tidyDesc($title))));
}
function getLernstand($steam, $id){
	$current_folder = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $id);
	$attribute_names = $current_folder->get_attribute_names();
	if (in_array("LARS_LERNSTAND", $attribute_names) && ($current_folder->get_attribute("LARS_LERNSTAND") instanceof steam_document)){
		$folder_lernstand = $current_folder->get_attribute("LARS_LERNSTAND");
	} else {
    	$name = $current_folder->get_attribute('OBJ_DESC');
		$folder_lernstand = steam_factory::create_document($GLOBALS["STEAM"]->get_id(), "Lernstand_".tidyName($name).".html", " ", "text/html", $current_folder, "Lernstand ".tidyDesc($name) );
		$folder_lernstand->set_attribute("LARS_HIDDEN", true);
		$folder_lernstand->set_attribute("OBJ_TYPE", "MOKO_LEARN_STATUS");
		$current_folder->set_attribute("LARS_LERNSTAND", $folder_lernstand);
	}
	$id = $folder_lernstand->get_id();
	$text = $folder_lernstand->get_attribute("OBJ_DESC");
//	getView($steam, $id);
    $steam->disconnect();                                    
	print (json_encode(array(success => true, id => $id, text => $text)));
}

/*
 * Kopie von getResources auf json_folder.php
 */
function getOwnStudents($steam){ //TODO: Caching
	if ($_POST['node'] != "source"){
		print "Fehlerhafter Aufruf";
		exit;
	}

	$schueler_link_room = $steam->get_current_steam_user()->get_attribute("LARS_SCHUELER");
	$other_desktops_room = $steam->get_current_steam_user()->get_attribute("LARS_ABO");
//	print $schueler_link_room->get_id();
//			$inventory = $discussion_folder->get_inventory_filtered(
//				array(
////					array( '+', 'attribute', 'OBJ_TYPE', 'prefix', 'steam_document' ),
//					array( '+', 'attribute', 'DOC_MIME_TYPE', 'prefix', "text/html" )
//					), 
//				array( 
//	 				array( '>', 'attribute', 'DOC_LAST_MODIFIED' )
//	 			),
//				$start+0,
//				$limit+0
//				);

	$inventory1 = $schueler_link_room->get_inventory_filtered(
		array(
			array( '-', 'attribute', 'OBJ_TYPE', '==', "LARS_MESSAGES" ),
			array( '+', 'class', CLASS_LINK),
			)
		);
	$inventory2 = $other_desktops_room->get_inventory_filtered(
		array(
			array( '-', 'attribute', 'OBJ_TYPE', '==', "LARS_MESSAGES" ),
			array( '+', 'class', CLASS_LINK),
			)
		);
	$inventory = array_merge($inventory1, $inventory2);
//		print $schueler_link_room->get_id();

	$colors = array(
					"green",
					"red",
					"blue",
					"darkcyan",
					"A52A2A", //braun
					"magenta",
					"FF4500", //OrangeRed
					"darkmagenta",
					"darkkhaki",
					"darkgreen",
					"8B4513", //SaddleBrown
					"chocolate",
					"brown"
			);
	$i = 0;
	$all_folders = array();
	foreach ($inventory as $key => $schueler_desktop)
		if ($schueler_desktop instanceof steam_link){
			$name = $schueler_desktop->get_name();
			$schueler_item = $schueler_desktop->get_link_object();
			$iconCls = in_array($schueler_desktop, $inventory2, true) ? "group":"user";
//TODO: Falsche Links aussortieren
			if (!($schueler_item instanceof steam_container)){
				$all_folders[] = array(
					qtip=>"Dieser MokoDesk existiert nicht: ".$schueler_desktop->get_attribute("OBJ_DESC"),//TODO: Sprachen
					text=>"<i>Kein MokoDesk: ".$schueler_desktop->get_attribute("OBJ_DESC")."</i>",
					leaf=>false,
					id=>$schueler_desktop->get_id(),
					origName=>$schueler_desktop->get_attribute(OBJ_NAME),
					allowDrop=>false,
					isTarget=>false,
					iconCls=>$iconCls);
				continue;
			}
			if (!($schueler_item->check_access_read( $steam->get_current_steam_user() ))){
				$all_folders[] = array(
					qtip=>"Kein Leserecht für den eingetragenen Link beim Nutzer ".$schueler_desktop->get_attribute("OBJ_DESC"),//TODO:Sprachen
					text=>"<i>Fehler: ".$schueler_desktop->get_attribute("OBJ_DESC")."</i>",
					leaf=>false,
					id=>$schueler_desktop->get_id(),
					origName=>$schueler_desktop->get_attribute(OBJ_NAME),
					allowDrop=>false,
					isTarget=>false,
					iconCls=>$iconCls);
				continue;
			}
			$color = $colors[$i%12];
			$children = getSchuelerTopics($steam, $id, $schueler_item, $color);
			$all_folders[] = array(
				qtip=>$qtipParent, 
//				qtip=>print_r($schueler_desktop->get_attribute_names()), 
				text=>($iconCls == "user" ? "<i>" : "").'<b><font color='.$colors[$i%12].'>'.tidyDesc($schueler_desktop->get_attribute("OBJ_DESC")).'</font></b>'.($iconCls == "user" ? "</i>" : ""), 
				origName=>$schueler_desktop->get_attribute(OBJ_NAME),
//				state=>$stateParent, 
//				uiProvider=>"col", 
				iconCls=>$iconCls, 
//				id=>$schueler_item->get_id(),
				allowDrop=>false,
				allowDrag=>false,
				isTarget=>false,
				id=>$schueler_desktop->get_id(),
				children=>$children,
				expanded=>count($children) ? false : true,
//				archivable=>($iconCls == "user") ? false : true //funktioniert nicht, da leaf nicht betroffen
				);
			$i++;
		}
session_name("bidowl_session");
session_start();    
	$_SESSION['reset_online_status'] = true;
session_write_close();	
	echo json_encode($all_folders);	
    $steam->disconnect();                                    
}
function newSchueler($steam){
//	include("lars_lang.php");
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
	$object_to_link = $steam_user->get_attribute("LARS_DESKTOP");
	if (!($object_to_link instanceof steam_container)){
	    print (json_encode(array(success => false, name=>msg('USER_HAS_NO_DESKTOP'))));
	    exit;
	}
	$student_container = $steam->get_current_steam_user()->get_attribute("LARS_SCHUELER");
	$inventory = $student_container->get_inventory();
	for( $i=0; $i < count($inventory); $i++ ){
		if (($inventory[$i] instanceof steam_link)){
			if($inventory[$i]->get_link_object() instanceof steam_object){
				if($inventory[$i]->get_link_object()->get_id() == $object_to_link->get_id()){
				    print (json_encode(array(success => false, name=>msg('USER_EXISTS'))));
				    exit;
				}
			}
		}
	}
	$access_write = $object_to_link->check_access_write( $steam->get_current_steam_user() );
	$access_read = $object_to_link->check_access_read( $steam->get_current_steam_user() );
  	if (!$access_read && !$access_write){
	    print (json_encode(array(success => false, name=>msg('NO_WRITE_OR_READ_ACCESS'))));
	    exit;
	}
	$newlink = steam_factory::create_link($GLOBALS["STEAM"]->get_id(), $object_to_link);
	$linkName = $steam_user->get_attribute("USER_FIRSTNAME")." ".$steam_user->get_attribute("USER_FULLNAME");
	$newlink->set_attribute("OBJ_NAME", tidyName($linkName));
	$newlink->set_attribute("OBJ_DESC", tidyDesc($linkName));
	$newlink->move($student_container);
    print (json_encode(array(success => true)));
}
function getView($steam, $id){
	global $config_webserver_ip;
	$object = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $id);
	if ($object instanceof steam_document){
		$content = $object->get_content();
//		$content = _get_texthtmlnew($config_webserver_ip, utf8_encode($content), $object);
		$content = _get_texthtmlnew($config_webserver_ip, $content, $object);
		echo json_encode(array(success=>true, html=>$content));
	} else {
		print (json_encode(array(success => false)));
	}
}  
function getAppointments($steam){
	global $config_webserver_ip;
	$current_user = $steam->get_current_steam_user();
	$attribute_names = $current_user->get_attribute_names();
	if (in_array("LARS_APPOINTMENTS", $attribute_names)){
		$lars_appointments = $current_user->get_attribute("LARS_APPOINTMENTS");
	} else {
		$lars_appointments = steam_factory::create_textdoc($GLOBALS["STEAM"]->get_id(), 'LARS_APPOINTMENTS', '', false, "Lars Termine");
		$current_user->set_attribute("LARS_APPOINTMENTS", $lars_appointments);
	}
	$data = $lars_appointments->get_content();
	$steam->disconnect();
    $newData["content"] = $data ? $data : "";
    $newData["success"] = true;
    echo json_encode($newData);
}
function saveAppointments($steam){
	$content = ($_POST['content']) ? $_POST['content'] : exit;
	$current_user = $steam->get_current_steam_user();
	$lars_appointments = $current_user->get_attribute("LARS_APPOINTMENTS");
	$lars_appointments->set_content($content);
	echo json_encode(array(success => true));
}
function getDiscussion($steam, $id){
	global $config_webserver_ip;
	$start = $_POST['start'];
	$limit = $_POST['limit'];
	$current_folder = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $id);
	if ($current_folder instanceof steam_link){
		$current_folder = $current_folder->get_link_object();
	}
	$attribute_names = $current_folder->get_attribute_names();
	$data = array();
	if (in_array("FOLDER_DISCUSSION", $attribute_names)){
		$discussion_folder = $current_folder->get_attribute("FOLDER_DISCUSSION");
		if ($limit+0){
			$inventory = $discussion_folder->get_inventory_filtered(
				array(
//					array( '+', 'attribute', 'OBJ_TYPE', 'prefix', 'steam_document' ),
					array( '+', 'attribute', 'DOC_MIME_TYPE', 'prefix', "text/html" )
					), 
				array( 
	 				array( '>', 'attribute', 'DOC_LAST_MODIFIED' )
	 			),
				$start+0,
				$limit+0
				);
// [Mon Feb 16 13:46:38 2009] [error] [client 131.234.252.2] PHP Fatal error:  Uncaught exception 'steam_exception' with message 'Error during data transfer. COAL_ERROR : args[0]=16 args[1]=Access denied for user /kernel/proxy.pike(363033/PSTAT_SAVE_OK)(/kernel/proxy.pike(363033/PSTAT_SAVE_OK)) accessing /kernel/proxy.pike(494819/PSTAT_SAVE_OK) using 1 called by /kernel/securesocket()\nNo READ access on "Meine_Mitteilungen" for /kernel/proxy.pike(363033/PSTAT_SAVE_OK) (0)' in /var/www/lars/PHPsTeam/get_current_steam_user.class.php:690\nStack trace:\n#0 /var/www/lars/PHPsTeam/get_current_steam_user.class.php(599): get_current_steam_user->send_command(Array)\n#1 /var/www/lars/PHPsTeam/get_current_steam_user.class.php(970): get_current_steam_user->command(Object(steam_request))\n#2 /var/www/lars/PHPsTeam/steam_object.class.php(584): get_current_steam_user->predefined_command(Object(steam_room), 'get_inventory_f...', Array, false)\n#3 /var/www/lars/PHPsTeam/steam_container.class.php(313): steam_object->steam_command(Object(steam_room), 'get_inventory_f...', Array, false)\n#4 /var/www/lars/lars_json.php(431): steam_container->get_inventory in /var/www/lars/PHPsTeam/get_current_steam_user.class.php on line 690, referer: http://www.bid-owl.de/lars/lars2/index.html
// [Wed Feb 18 13:44:46 2009] [error] [client 80.66.15.17] PHP Fatal error:  Uncaught exception 'steam_exception' with message 'Error during data transfer. COAL_ERROR : args[0]=16 args[1]=Access denied for user /kernel/proxy.pike(5282/PSTAT_SAVE_OK)(/kernel/proxy.pike(5282/PSTAT_SAVE_OK)) accessing /kernel/proxy.pike(494819/PSTAT_SAVE_OK) using 1 called by /kernel/securesocket()\nNo READ access on "Meine_Mitteilungen" for /kernel/proxy.pike(5282/PSTAT_SAVE_OK) (0)' in /var/www/lars/PHPsTeam/get_current_steam_user.class.php:690\nStack trace:\n#0 /var/www/lars/PHPsTeam/get_current_steam_user.class.php(599): get_current_steam_user->send_command(Array)\n#1 /var/www/lars/PHPsTeam/get_current_steam_user.class.php(970): get_current_steam_user->command(Object(steam_request))\n#2 /var/www/lars/PHPsTeam/steam_object.class.php(584): get_current_steam_user->predefined_command(Object(steam_room), 'get_inventory_f...', Array, false)\n#3 /var/www/lars/PHPsTeam/steam_container.class.php(313): steam_object->steam_command(Object(steam_room), 'get_inventory_f...', Array, false)\n#4 /var/www/lars/lars_json.php(431): steam_container->get_inventory_filte in /var/www/lars/PHPsTeam/get_current_steam_user.class.php on line 690, referer: http://www.bid-owl.de/lars/lars2/index.html
// Leserechte sind das Problem!
			$inventory_to_count = $discussion_folder->get_inventory_filtered(
				array(
//					array( '+', 'attribute', 'OBJ_TYPE', 'prefix', 'steam_document' ),
					array( '+', 'attribute', 'DOC_MIME_TYPE', 'prefix', "text/html" )
				));
				//			$inventory = $inventory_paged['objects'];
//	public function get_inventory_paginated ( $pFilters = array(), $pSort = array(), $pOffset = 0, $pLength = 0, $pBuffer = FALSE )
//	public function get_inventory_filtered ( $pFilters = array(), $pSort = array(), $pOffset = 0, $pLength = 0, $pBuffer = FALSE )
		} else {
			$inventory = $discussion_folder->get_inventory();
		}
		foreach ($inventory as $item){
			$attributes = $item->get_attributes(array(DOC_MIME_TYPE, OBJ_NAME, OBJ_DESC, OBJ_CREATION_TIME, OBJ_LAST_CHANGED, DOC_LAST_MODIFIED, CONT_LAST_MODIFIED));
			$attributes["OBJ_ID"] = $item->get_id();
			$attributes["OBJ_AUTHOR"] = $item->get_creator()->get_name();
			$attributes["LARS_CONTENT"] = '<div class="dflt">'._get_texthtmlnew($config_webserver_ip, stripslashes($item->get_content()), $item).'</div>';
//			if ($item instanceof steam_document && $attributes[DOC_MIME_TYPE] === "text/html"){
			$data[] = array(
				id=>$attributes["OBJ_ID"],
				OBJ_NAME=>$attributes["OBJ_NAME"],
				OBJ_DESC=>$attributes["OBJ_DESC"], 
				OBJ_AUTHOR=>$attributes["OBJ_AUTHOR"], 
				DOC_LAST_MODIFIED=>$attributes["DOC_LAST_MODIFIED"], 
				LARS_CONTENT=>$attributes["LARS_CONTENT"], 
				);
//			} else {
//			}
		}
	}
	$steam->disconnect();
	$newData["messages"] = $data;
	$newData["totalCount"] = count($inventory_to_count);
	echo json_encode($newData);
}
function newMessage($steam, $id){
//	include("lars_lang.php");
//	$name = ($_POST['name']) ? utf8_decode($_POST['name']) : "Neue Nachricht";
	$name = ($_POST['name']) ? $_POST['name'] : "Neue Nachricht";
	$current_folder = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $id);
	if ($current_folder instanceof steam_link){
		$current_folder = $current_folder->get_link_object();
	}
	if (!($current_folder->check_access_read($steam->get_current_steam_user()))){
		echo json_encode(array(success => false, name=>msg('NO_READ_ACCESS')));
		exit;
	}
	$attribute_names = $current_folder->get_attribute_names();
	if (in_array("FOLDER_DISCUSSION", $attribute_names)){
		$discussion_folder = $current_folder->get_attribute("FOLDER_DISCUSSION");
		if (!($discussion_folder->check_access_write($steam->get_current_steam_user()))){
			echo json_encode(array(success => false, name=>msg('NO_WRITE_ACCESS')));
			exit;
		}
		//! Übergangsweise wird das Attribut erstellt.
		$discussion_folder->set_attribute("OBJ_TYPE", "LARS_MESSAGES");
	} else {
		$discussion_folder = steam_factory::create_container($GLOBALS["STEAM"]->get_id(), tidyName("Nachrichten_zu_".$current_folder->get_name()), $current_folder, tidyDesc("Nachrichten zu ".$current_folder->get_attribute("OBJ_DESC")));
		$discussion_folder->set_attribute("LARS_HIDDEN", true);
		$discussion_folder->set_attribute("OBJ_TYPE", "LARS_MESSAGES");
		$current_folder->set_attribute("FOLDER_DISCUSSION", $discussion_folder);
	}
	$discussion_folder = $current_folder->get_attribute("FOLDER_DISCUSSION");
	if (!($discussion_folder->check_access_write($steam->get_current_steam_user()))){
		echo json_encode(array(success => false, name=>msg('NO_WRITE_ACCESS')));
		exit;
	}
	$newMessageObject = steam_factory::create_document($GLOBALS["STEAM"]->get_id(), tidyName($name).".html", " ", "text/html", $discussion_folder, tidyDesc($name) );
	$newID = $newMessageObject->get_id();
	print json_encode(array(success => true, newID => $newID));
}
function newText($steam, $id){
//	include("lars_lang.php");
//	$name = ($_POST['name']) ? utf8_encode($_POST['name']) : "Neue Nachricht";
	$name = ($_POST['name']) ? $_POST['name'] : "Neue Nachricht";
	$current_folder = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $id);
	if (!($current_folder->check_access_write($steam->get_current_steam_user()))){
		echo json_encode(array(success => false, name=>msg('NO_WRITE_ACCESS')));
		exit;
	}
	$newMessageObject = steam_factory::create_document($GLOBALS["STEAM"]->get_id(), tidyName($name).".html", " ", "text/html", $current_folder, tidyDesc($name) );
	$newID = $newMessageObject->get_id();
	print json_encode(array(success => true, newID => $newID));
}

/*
 * Abonnierte Desktops für neue Dokumente anzeigen.
 * ACHTUNG: Löschen von Desktops unter "Andere MokoDesks" löscht nicht diese Abonnement
 */
function getDesktopAbo($steam, $id) {
	$login_user = $steam->get_current_steam_user();
	$schueler_link_room = $login_user->get_attribute("LARS_SCHUELER");
	$other_desktops_room = $login_user->get_attribute("LARS_ABO");
	$subscribed_desktops_room = $login_user->get_attribute("MOKO_SUBSCRIPTION_CHECK");
	$inventory1 = $schueler_link_room->get_inventory_filtered(
		array(
			array( '-', 'attribute', 'OBJ_TYPE', '==', "LARS_MESSAGES" ),
			array( '+', 'class', CLASS_LINK),
			)
		);
	$inventory2 = $other_desktops_room->get_inventory_filtered(
		array(
			array( '-', 'attribute', 'OBJ_TYPE', '==', "LARS_MESSAGES" ),
			array( '+', 'class', CLASS_LINK),
			)
		);
	$inventory3 = $subscribed_desktops_room->get_inventory_filtered(
		array(
			array( '-', 'attribute', 'OBJ_TYPE', '==', "LARS_MESSAGES" ),
			array( '+', 'class', CLASS_LINK),
			)
		);
	$subscribed_ids = array();
	#### get subscribed desktop ids cached
	foreach ($inventory3 as $key => $subscribed_desktop) { 
		if ($subscribed_desktop instanceof steam_link){
			$subscribed_desktop_objects[$key] = $subscribed_desktop->get_link_object(1);
		}
	}
	$result = $steam->buffer_flush();
	foreach ($inventory3 as $key => $subscribed_desktop) { 
		if ($subscribed_desktop instanceof steam_link){
			$subscribed_desktop_objects[$key] = $result[$subscribed_desktop_objects[$key]];
			$subscribed_ids[] = $subscribed_desktop_objects[$key]->get_id();
		}
	}
	####	
		
	$inventory = array_merge($inventory1, $inventory2);
	$data = array();
	foreach ($inventory as $key => $desktop){
		if ($desktop instanceof steam_link){
			$name = $desktop->get_name();
			$desktop_object = $desktop->get_link_object();
			$iconCls = in_array($desktop, $inventory2, true) ? "group":"user";
			if (!($desktop_object instanceof steam_container)){
				continue;
			}
			if (!($desktop_object->check_access_read( $login_user ))){
				continue;
			}
			$desktop_object_id = $desktop_object->get_id();
			$data[] = array(
					name=>$desktop->get_attribute("OBJ_DESC"),
					checked=>in_array($desktop_object_id, $subscribed_ids),
					type=>$iconCls, // not used yet
					id=>$desktop->get_id()
					);
		}
	}
	$data = json_encode($data);
    echo $data;
	$steam->disconnect();
}

/*
 * Subscription für neue Dokumente aktualisieren (User Attribut MOKO_SUBSCRIPTION_CHECK)
 */
function updateSubscription($steam, $id)
{
try{

	    $id 		= $_POST['keyValue'];
	    $key    	= $_POST['key'];
	    $field 		= $_POST['field'];
    	$fieldValue = $_POST['fieldValue'];
	    $message 	= " ";

	    $desktop_link = steam_factory::get_object($GLOBALS["STEAM"]->get_id(),$id);
		$login_user = $steam->get_current_steam_user();
		$success = false;
	    switch($fieldValue){
    		case "true":
				$copy = steam_factory::create_copy( $GLOBALS["STEAM"]->get_id(), $desktop_link );
				$copy->move( $login_user->get_attribute("MOKO_SUBSCRIPTION_CHECK") );	
				$success = true;
				break;
			case "false":
				$desktop_link_object_id = $desktop_link->get_link_object()->get_id();
				$subscribed_desktops_room = $login_user->get_attribute("MOKO_SUBSCRIPTION_CHECK");
				$inventory = $subscribed_desktops_room->get_inventory_filtered(
					array(
						array( '-', 'attribute', 'OBJ_TYPE', '==', "LARS_MESSAGES" ),
						array( '+', 'class', CLASS_LINK),
						)
					);
					foreach ($inventory as $key => $subscribed_desktop){
						if ($subscribed_desktop instanceof steam_link){
							$subscribed_desktop_object_id = $subscribed_desktop->get_link_object()->get_id();
							if ($subscribed_desktop_object_id == $desktop_link_object_id){
								$subscribed_desktop->move($login_user->get_attribute("USER_TRASHBIN"));
								$success = true;
								break;
							}
						}
					}
		 	    }
		print (json_encode(array(success => $success)));

	}catch(Exception $e){
        print (json_encode(array(success => false, name=>$e->getMessage())));
	}
    $steam->disconnect();
	
}


function getSchuelerTopics($steam, $id, $folder = false, $color = false){
	if (trim($_POST['folder'])){
		$larsFolder = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $_POST['folder']);
	} else if ($folder){
		$larsFolder = $folder;
	} else {
		$larsFolder = $steam->get_current_steam_user()->get_attribute("LARS_DESKTOP");
	}
	$inventory = $larsFolder->get_inventory_filtered(
		array(
			array( '-', 'attribute', 'OBJ_TYPE', '==', "LARS_MESSAGES" ),
			array( '-', 'attribute', 'LARS_HIDDEN', '==', true ),
			array( '+', 'class', 0x00000006), //container + room
			),
			array(),
			0,
			0,
			1
		);
	$archivInventory = $larsFolder->get_inventory_filtered(
		array(
			array( '-', 'attribute', 'OBJ_TYPE', '!=', "LARS_ARCHIV" ),
			array( '+', 'class', 0x00000006),
			),
			array(),
			0,
			0,
			1
		);
	$result = $steam->buffer_flush();
	$inventory = $result[$inventory];
	$archivInventory = $result[$archivInventory];
	
	$data = array();

	$subInventoryArray = array();

	foreach ($inventory as $key => $item) { // Hole alle Container in allen Ordnern
		$subInventoryArray[$key] = $item->get_inventory_filtered(
			array(
				array( '-', 'attribute', 'OBJ_TYPE', '==', "LARS_MESSAGES" ),
				array( '-', 'attribute', 'LARS_HIDDEN', '==', true ),
				array( '+', 'class', 0x00000006),
				),
				array(),
				0,
				0,
				1
			);
	}
	
	$result_inventory = $steam->buffer_flush();
	
	$subInventoryAttributes = array();
	$subInventoryNames = array();
	$subInventoryDesc = array();
	$inventoryAttributes = array();
	$inventoryNames = array();
	$inventoryDesc = array();
	$inventoryObjTypes = array();

	foreach ($inventory as $key => $value) { // Hole alle Attribute der Ordner
		$inventoryNames[$key] = $value->get_name(1);
		$inventoryDesc[$key] = $value->get_attribute("OBJ_DESC", 1);
		$inventoryObjTypes[$key] = $value->get_attribute("OBJ_TYPE", 1);
			foreach ($result_inventory[$subInventoryArray[$key]] as $key2 => $value2) {
				$subInventoryAttributes[$key][$key2] = $result_inventory[$subInventoryArray[$key]][$key2]->get_attribute_names(1);
				$subInventoryNames[$key][$key2] = $result_inventory[$subInventoryArray[$key]][$key2]->get_name(1);
				$subInventoryDesc[$key][$key2] = $result_inventory[$subInventoryArray[$key]][$key2]->get_attribute("OBJ_DESC", 1);
			}
	}
	$result_attributes = $steam->buffer_flush();

	if (!empty($archivInventory) && $folder){
		foreach ($archivInventory as $key => $item){ //TODO: Objekt sollte direkt ausgewählt werden
			$archiv_folder = $item;
		}
		$data[] = array(
			qtip=>"Archiv",//TODO: Sprachen
			text=>"Archiv",  
			state=>'<div class="whiteIcon">&nbsp;</div>', 
			allowDrag=>false,
			iconCls=>"folder-archiv", 
			leaf=>true,
			id=>$archiv_folder->get_id()
			);
	}

	
	foreach ($inventory as $key => $item){
		$itemName = $result_attributes[$inventoryNames[$key]];
		$itemDesc = $result_attributes[$inventoryDesc[$key]];
//		if ($result_attributes[$inventoryObjTypes[$key]] !== "LARS_ARCHIV"){
			$subInventory = $result_inventory[$subInventoryArray[$key]];
//			$subInventory = $subInventory[$key];
//			$item->get_inventory_filtered(
//				array(
//					array( '-', 'attribute', 'OBJ_TYPE', '==', "LARS_MESSAGES" ),
//					array( '-', 'attribute', 'LARS_HIDDEN', '==', true ),
//					array( '+', 'class', CLASS_CONTAINER),
//					)
//				);
			$children = array();
			
			foreach ($subInventory as $key2 => $subItem){
//				if (!($subItem->get_attribute("LARS_HIDDEN")) && $subItem instanceof steam_container){
//					$attributes = $subItem->get_attribute_names();
					$attributes = $result_attributes[$subInventoryAttributes[$key][$key2]];
					$qtipParent = "";
					$subItemName = $result_attributes[$subInventoryNames[$key][$key2]];
					$subItemDesc = $result_attributes[$subInventoryDesc[$key][$key2]];
					if (in_array("LARS_STATE", $attributes)){
						switch ($state = $subItem->get_attribute("LARS_STATE")){
							case 0:
								$icon = 'reportRed';
								$qtip = "Diese Aufgabe ist neu und muss noch erledigt werden";
								$qtipParent = msg('QTIP_SUBJECT_HAS_ASSIGNMENT');
								$stateParent = '<div class="redIcon">&nbsp;</div>';
								break;
							case 1:
								$icon = 'reportOrange';
								$qtip = msg('QTIP_ASSIGNMENT_NOT_COMPLETED');;
								break;
							case 2:
								$icon = 'reportGreen';
								$qtip =  msg('QTIP_ASSIGNMENT_FINISHED_UNCORRECTED');
								break;
							case 3:
								$icon = 'reportBlue';
								$qtip = msg('QTIP_ASSIGNMENT_CORRECTED');
								break;
							case 4:
								$icon = 'reportYellow';
								$qtip = msg('QTIP_ASSIGNMENT_FINISHED');
								break;
							default:
								$icon = 'package';
								$state = "";
								break;
						}
					} else {
						$state = "";
						$icon = 'package';
//						$stateCls = '<div class="whiteIcon">&nbsp;</div>';
						$qtip = $subItemName;
						$qtipParent = $qtipParent ? $qtipParent : $item->get_name();
						$stateParent = $stateParent ? $stateParent : ""; 				
					}
					$children[] = array(
						qtip=>$qtip,
						text=>tidyDesc($subItemDesc), 
						origName=>$subItemName,
						state=>$state, 
						iconCls=>$icon, 
						leaf=>true,
						allowDrag=>false,
						id=>$subItem->get_id(),
						groupColor=>$color ? $color : "",
						archiv=>$archiv_folder ? $archiv_folder->get_id():0
						);
				}
//			}
			$data[] = array(
				qtip=>$qtipParent, 
				text=>tidyDesc($itemDesc), 
				origName=>$itemName,
				stateCls=>$stateParent, 
//				uiProvider=>"col", 
//				cls=>"master-task", 
//				iconCls=>"comments", 
				iconCls=>(count($children)>0) ? "comments" : "comments", 
				id=>$item->get_id(),
				allowDrag=>false,
				children=>$children,
				groupColor=>$color ? $color : "",
				expanded=>count($children) ? false : true
				);
			$stateParent = "";
//		} elseif ($result_attributes[$inventoryObjTypes[$key]] === "LARS_ARCHIV" && $folder){

//		}
	}
    if ($folder){
    	return $data;
    } else {
		$data = json_encode($data);  //encode the data in json format
	    echo $data;
		$steam->disconnect();
    }
	
    
    
}
function newAssignmentPackage($steam, $id)
{
	// name and description for the new package
//	$name = utf8_decode($_POST['name']);
	$name = $_POST['name'];
	//	$description = $_POST['description'];
	
	// get folder for the new assignment package
	$current_folder = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $id );
	// create new Assignment container
	$newPackage = steam_factory::create_container($GLOBALS["STEAM"]->get_id(), tidyName($name), $current_folder, tidyDesc($name));
	$newPackage->set_attribute("LARS_STATE", 0);
	$newPackage->set_attribute("OBJ_TYPE", "ASSIGNMENT_PACKAGE");
	print (json_encode(array(success => true, newId => $newPackage->get_id(), origName => tidyName($name))));
	$steam->disconnect();
	
}
function copyIntoPackage($steam, $id)
{
	global $config_webserver_ip;

	// 	name and description for the new package
	$packageId = $_POST['folderId'];
	
	$current_folder = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $packageId );
	$object_to_copy = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $id );
	// copy document
	$copy = steam_factory::create_copy( $GLOBALS["STEAM"]->get_id(), $object_to_copy );
	$copy->move( $current_folder );	
	
	// copy embedded pictures
	if (!($copy instanceof steam_document)){
		print (json_encode(array(success => true, newId=>$copy->get_id())));
		exit;
	}
	$content = $object_to_copy->get_content();
	$current_path = substr( $object_to_copy->get_path(), 0, strrpos($object_to_copy->get_path(), "/")) . "/";
    preg_match_all('/src="([a-z0-9.\-_\/]*)"/iU', $content, $treffer);
    $newContent = $content;
//	print_r($treffer);
	for ($index=0; $index<count($treffer[0]); $index++){
//		print $treffer[$index]."-".$index."<<<<<";
		$original_image_1 = steam_factory::path_to_object($GLOBALS["STEAM"]->get_id(), $current_path."".$treffer[1][$index]);
		$original_image_2 = steam_factory::path_to_object($GLOBALS["STEAM"]->get_id(), $treffer[1][$index]);
		$original_image = $original_image_1 ? $original_image_1 : $original_image_2;
		//		$original_image->get_id()
		if ($original_image instanceof steam_document){
			$baseFileName = tidyName(basename($treffer[1][$index]));
			$original_image_id = $original_image->get_id();
			$original_image_object = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $original_image_id );
			$copy_image = steam_factory::create_copy( $GLOBALS["STEAM"]->get_id(), $original_image_object );
			$copy_image->move( $current_folder );
			$copy_image->set_attribute("OBJ_NAME", $baseFileName);
			$newContent = str_replace($treffer[0][$index], 'src="'.$baseFileName.'"', $newContent);
		}
	}
	
//	print $object_to_copy->get_content()."++++++++++++++";
//	print $copy->get_content()."old content \n";
//	print $newContent."<- new content \n";
	$copy->set_content($newContent);
	if (!$copy->get_attribute("OBJ_DESC")){
		$copy->set_attribute("OBJ_DESC", $copy->get_attribute("OBJ_NAME"));
	}
//	$newContent = _get_texthtmlnew($config_webserver_ip, utf8_encode($newContent), $copy);
	$newContent = _get_texthtmlnew($config_webserver_ip, $newContent, $copy);
	print (json_encode(array(success => true, newId=>$copy->get_id(), html => $newContent, ersetzungen => $treffer)));
}
function copyPackage($steam, $id)
{
//	include("lars_lang.php");
	global $config_webserver_ip;

	// 	name and description for the new package
	$sourceId = $_POST['sourceId'];
	$targetId = $_POST['targetId'];
	
	$object_to_copy = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $sourceId );
	$target_folder = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $targetId );
	$archiv_folder = $steam->get_current_steam_user()->get_attribute("LARS_ARCHIV");
	if (!($target_folder instanceof steam_container) || !($object_to_copy instanceof steam_container)){
		print (json_encode(array(success => false)));
		exit;
	}
	if (!$target_folder->check_access_write($steam->get_current_steam_user())){
		print (json_encode(array(success => false, name=>msg('NO_WRITE_ACCESS'))));
		exit;
	}
	// copy document
	$copy1 = steam_factory::create_copy( $GLOBALS["STEAM"]->get_id(), $object_to_copy );
	$copy2 = steam_factory::create_copy( $GLOBALS["STEAM"]->get_id(), $object_to_copy );
	$steam_user_target = $target_folder->get_environment()->get_environment()->get_creator();
	if ($steam_user_target->get_attribute("USER_FIRSTNAME")){
		$folderName = $steam_user_target->get_attribute("USER_FIRSTNAME")." ".$steam_user_target->get_attribute("USER_FULLNAME");	
	} else {
//		$folderName = $target_folder->get_environment()->get_attribute("OBJ_OWNER");	
		$other_desktops_inventory = $steam->get_current_steam_user()->get_attribute("LARS_ABO")->get_inventory();
		foreach ($other_desktops_inventory as $desktop_link){
			$desktop_path = $desktop_link->get_link_object()->get_path();
			$target_path = $target_folder->get_path();
			if (stripos($target_path, $desktop_path) === 0){
				$folderName = $desktop_link->get_name();
				break;
			}
		}
	}
	$inventory = $archiv_folder->get_inventory();
	  for( $i=0; $i < count($inventory); $i++ )
		if( tidyName($folderName) == $inventory[$i]->get_name() && $inventory[$i] instanceof steam_container){
			$result = $inventory[$i];
			break;
		}
	if( !$result ){
		$archiv_folder = steam_factory::create_container($GLOBALS["STEAM"]->get_id(), tidyName($folderName), $archiv_folder, tidyDesc($folderName));
	} else {
		$archiv_folder = $result;
	}

	$copy_inventory_1 = $copy1->get_inventory();
	$copy_inventory_2 = $copy2->get_inventory();
//	$current_path = substr( $object_to_move->get_path(), 0, strrpos($object_to_move->get_path(), "/")) . "/";
	$current_path = $object_to_copy->get_path()."/";
	foreach ($copy_inventory_1 as $item){
		if ($item->get_attribute("DOC_MIME_TYPE") === "text/html"
			|| $item->get_attribute("DOC_MIME_TYPE") === "text/plain")
			{
				$content = $item->get_content();
				$content = str_replace($current_path, '', $content);
				$item->set_content($content);
			}
	}
	foreach ($copy_inventory_2 as $item){
		if ($item->get_attribute("DOC_MIME_TYPE") === "text/html"
			|| $item->get_attribute("DOC_MIME_TYPE") === "text/plain")
			{
				$content = $item->get_content();
				$content = str_replace($current_path, '', $content);
				$item->set_content($content);
			}
	}

	$copy1->move( $target_folder );	
	$copy2->set_attribute(OBJ_DESC, date("Y.m.d")." ".$copy2->get_attribute(OBJ_DESC));
	$copy2->move( $archiv_folder );	

	print (json_encode(array(success => true, newId => $copy1->get_id(), origName => $copy1->get_name())));
}
function copyFolder($steam, $id)
{
	global $config_webserver_ip;

	// 	name and description for the new package
	$sourceId = $_POST['sourceId'];
	$targetId = $_POST['targetId'];
	
	$object_to_copy = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $sourceId );
	$target_folder = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $targetId );
	if ($target_folder instanceof steam_link){
		$target_folder = $target_folder->get_link_object();
		}
	if (!($target_folder instanceof steam_container) || !($object_to_copy instanceof steam_container)){
		print (json_encode(array(success => false, message => msg('NO_CONTAINER'))));
		exit;
	}
	// copy document
	$copy1 = steam_factory::create_copy( $GLOBALS["STEAM"]->get_id(), $object_to_copy );

	$copy_inventory_1 = $copy1->get_inventory();
	$current_path = $object_to_copy->get_path()."/";
	foreach ($copy_inventory_1 as $item){
		if ($item->get_attribute("DOC_MIME_TYPE") === "text/html"
			|| $item->get_attribute("DOC_MIME_TYPE") === "text/plain")
			{
				$content = $item->get_content();
				$content = str_replace($current_path, '', $content);
				$item->set_content($content);
			}
	}
	$copy1->move( $target_folder );	

	print (json_encode(array(success => true, newId => $copy1->get_id(), origName => $copy1->get_name())));
}
function copyFile($steam, $id)
{
	global $config_webserver_ip;

	// 	name and description for the new package
	$sourceId = $_POST['sourceId'];
	$targetId = $_POST['targetId'];
	
	$object_to_copy = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $sourceId );
	$target_folder = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $targetId );
	if (!($target_folder instanceof steam_container)){
		$target_folder = $target_folder->get_environment();
		}
	if ($target_folder instanceof steam_link){
		$target_folder = $target_folder->get_link_object();
		}
	
	if (!($target_folder instanceof steam_container) || ($object_to_copy instanceof steam_container)){
		print (json_encode(array(success => false)));
		exit;
		}
	// copy document
	$copy1 = steam_factory::create_copy( $GLOBALS["STEAM"]->get_id(), $object_to_copy );
	$copy1->move( $target_folder );	

	print (json_encode(array(success => true, newId => $copy1->get_id(), origName => $copy1->get_name())));
}
function copyFilePackage($steam, $id)
{
	global $config_webserver_ip;

	// 	name and description for the new package
	$sourceId = $_POST['sourceId'];
	$targetId = $_POST['targetId'];
	
	$file_to_copy = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $sourceId );
	$file_desc = $file_to_copy->get_attribute(OBJ_DESC) ? $file_to_copy->get_attribute(OBJ_DESC) : $file_to_copy->get_attribute(OBJ_NAME);
	$file_name = pathinfo($file_desc);
	$file_copy = steam_factory::create_copy( $GLOBALS["STEAM"]->get_id(), $file_to_copy );
	$file_copy->set_attribute("OBJ_DESC", $file_desc);
	$target_folder = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $targetId );
	$archiv_folder = $steam->get_current_steam_user()->get_attribute("LARS_ARCHIV");
	if (!($target_folder instanceof steam_container)){
		print (json_encode(array(success => false)));
		exit;
	}
	// copy document
	$copy1 = steam_factory::create_container($GLOBALS["STEAM"]->get_id(), $file_name['filename'], $archiv_folder, $file_name['filename']);
	$file_copy->move($copy1);
	$copy2 = steam_factory::create_copy( $GLOBALS["STEAM"]->get_id(), $copy1 );

	$steam_user_target = $target_folder->get_environment()->get_environment()->get_creator();
	$folderName = $steam_user_target->get_attribute("USER_FIRSTNAME")." ".$steam_user_target->get_attribute("USER_FULLNAME");
	$inventory = $archiv_folder->get_inventory();
	  for( $i=0; $i < count($inventory); $i++ )
		if( tidyName($folderName) == $inventory[$i]->get_name() && $inventory[$i] instanceof steam_container){
			$result = $inventory[$i];
			break;
		}
		if( !$result ){
		$archiv_folder = steam_factory::create_container($GLOBALS["STEAM"]->get_id(), tidyName($folderName), $archiv_folder, tidyDesc($folderName));
	} else {
		$archiv_folder = $result;
	}
	
	$copy_inventory_1 = $copy1->get_inventory();
	$copy_inventory_2 = $copy2->get_inventory();
//	$current_path = substr( $object_to_move->get_path(), 0, strrpos($object_to_move->get_path(), "/")) . "/";
	$current_path = $file_to_copy->get_path()."/";
	foreach ($copy_inventory_1 as $item){
		if ($item->get_attribute("DOC_MIME_TYPE") === "text/html"
			|| $item->get_attribute("DOC_MIME_TYPE") === "text/plain")
			{
				$content = $item->get_content();
				$content = str_replace($current_path, '', $content);
				$item->set_content($content);
			}
	}
	foreach ($copy_inventory_2 as $item){
		if ($item->get_attribute("DOC_MIME_TYPE") === "text/html"
			|| $item->get_attribute("DOC_MIME_TYPE") === "text/plain")
			{
				$content = $item->get_content();
				$content = str_replace($current_path, '', $content);
				$item->set_content($content);
			}
	}
	
	$copy1->move( $target_folder );	
	$copy2->set_attribute(OBJ_DESC, date("Y.m.d")." ".$copy2->get_attribute(OBJ_DESC));
	$copy2->move( $archiv_folder );	

	print (json_encode(array(success => true, newId => $copy1->get_id())));
}
function archivePackage($steam, $id)
{
	$larsArchiv = $steam->get_current_steam_user()->get_attribute("LARS_ARCHIV");
	$object_to_move = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $id );
	$folderName = $object_to_move->get_environment()->get_attribute("OBJ_DESC");
	
	$inventory = $larsArchiv->get_inventory();
	  for( $i=0; $i < count($inventory); $i++ )
		if( $folderName == $inventory[$i]->get_attribute("OBJ_DESC") && $inventory[$i] instanceof steam_container){
			$result = $inventory[$i];
			break;
		}
	if( !$result ){
		$container_to_move_in = steam_factory::create_container($GLOBALS["STEAM"]->get_id(), $object_to_move->get_environment()->get_name(), $larsArchiv, $folderName);
	} else {
		$container_to_move_in = $result;
	}

	$objectInventory = $object_to_move->get_inventory();
//	$current_path = substr( $object_to_move->get_path(), 0, strrpos($object_to_move->get_path(), "/")) . "/";
	$current_path = $object_to_move->get_path()."/";
	foreach ($objectInventory as $item){
		if ($item->get_attribute("DOC_MIME_TYPE") === "text/html"
			|| $item->get_attribute("DOC_MIME_TYPE") === "text/plain")
			{
				$content = $item->get_content();
				$content = str_replace($current_path, '', $content);
				$item->set_content($content);
			}
	}
	$object_to_move->move($container_to_move_in);
	$object_to_move->set_attribute(OBJ_DESC, date("Y.m.d")." ".$object_to_move->get_attribute(OBJ_DESC));
	print (json_encode(array(success => true)));
}
function archiveGroupPackage($steam, $id)
{
//	$larsArchiv = $steam->get_current_steam_user()->get_attribute("LARS_ARCHIV");
	$archiveId = intval($_POST['archiveId']);
	$larsArchiv = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $archiveId );
	//From here copy of archivePackage
	$object_to_move = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $id );
	$folderName = $object_to_move->get_environment()->get_attribute("OBJ_DESC");
	
	$inventory = $larsArchiv->get_inventory();
	  for( $i=0; $i < count($inventory); $i++ )
		if( $folderName == $inventory[$i]->get_attribute("OBJ_DESC") && $inventory[$i] instanceof steam_container){
			$result = $inventory[$i];
			break;
		}
	if( !$result ){
		$container_to_move_in = steam_factory::create_container($GLOBALS["STEAM"]->get_id(), $object_to_move->get_environment()->get_name(), $larsArchiv, $folderName);
	} else {
		$container_to_move_in = $result;
	}

	$objectInventory = $object_to_move->get_inventory();
//	$current_path = substr( $object_to_move->get_path(), 0, strrpos($object_to_move->get_path(), "/")) . "/";
	$current_path = $object_to_move->get_path()."/";
	foreach ($objectInventory as $item){
		if ($item->get_attribute("DOC_MIME_TYPE") === "text/html"
			|| $item->get_attribute("DOC_MIME_TYPE") === "text/plain")
			{
				$content = $item->get_content();
				$content = str_replace($current_path, '', $content);
				$item->set_content($content);
			}
	}
	$object_to_move->move($container_to_move_in);
	$object_to_move->set_attribute(OBJ_DESC, date("Y.m.d")." ".$object_to_move->get_attribute(OBJ_DESC));
	print (json_encode(array(success => true)));
}
function changeHeight($steam){
	$height = intval($_POST['height']);
	$steam->get_current_steam_user()->set_attribute("LARS_IMAGE_HEIGHT", $height);
	print (json_encode(array(success => true)));
}
function newTopicsFolder($steam, $id)
{
	// name and description for the new package
	$name = utf8_decode($_POST['name']);
//	$description = $_POST['description'];
	
	
	if ($id){
		$current_folder = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $id )->get_link_object();
	} else {
		// get folder from user attribute
		$current_folder = $steam->get_current_steam_user()->get_attribute("LARS_DESKTOP");
	}
	// create new folder
	$newPackage = steam_factory::create_container($GLOBALS["STEAM"]->get_id(), tidyName($name), $current_folder, tidyDesc($name));
	$newPackage->set_attribute("OBJ_TYPE", "LARS_FOLDER");
	
	print (json_encode(array(success => true, newId => $newPackage->get_id())));
	$steam->disconnect();
	
}
function newFolderLinks($steam, $id)
{
	if ($id == "root"){ //hier wird bestimmt welcher Ordner als Wurzelknoten verwendet werden soll
		$current_folder = $steam->get_current_steam_user()->get_attribute("USER_BOOKMARKROOM");
	} else {
		$current_folder = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $id );
	}
	if ($current_folder instanceof steam_link){
		$current_folder = $current_folder->get_link_object();
		}

	// name and description for the new package
	$name = trim($_POST['name']);
	//	$description = $_POST['description'];
	
	// get folder from user attribute
	// create new folder
	$newFolder = steam_factory::create_container($GLOBALS["STEAM"]->get_id(), tidyName($name), $current_folder, tidyDesc($name));
	
	print (json_encode(array(success => true)));
	$steam->disconnect();
	
}
function getAssignmentPackage($steam, $id)
{
	global $config_webserver_ip;
	//TODO:  Button in Grid erstellen um Parameter anzupassen
	$showHidden = ($_POST['showHidden']) ? $_POST['showHidden'] : false;
	
//	$last_login_time = $steam->get_current_steam_user()->get_attribute(USER_LAST_LOGIN_LARS);
	// get folder for the new assignment package
	$current_folder = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $id );
	// create new Assignment container
	$inventory = $current_folder->get_inventory_filtered(		
		array(
			array( '-', 'attribute', 'bid:hidden', '==', 'hide_always' ),
			array( '-', 'attribute', 'bid:hidden', '==', 'true' ),
			array( '-', 'attribute', 'LARS_HIDDEN', '==', true ),
			array( '+', 'class', 0x00000930),//documents, images, links, ...
			)
	);	
	$items_array = array();
	foreach ($inventory as $key => $item){
		$items_array[$key]["attributes"] = $item->get_attributes(array("bid:hidden", LARS_HIDDEN, DOC_MIME_TYPE, OBJ_NAME, DOC_EXTERN_URL, OBJ_DESC, OBJ_PATH, OBJ_CREATION_TIME, OBJ_LAST_CHANGED, LARS_STATE, LARS_COMMENT, OBJ_TYPE, LARS_TYPE), 1);
//		if ($item instanceof steam_document){
//			$items_array[$key]["content"] = $item->get_content(1);
//		}
	}
	$result = $steam->buffer_flush();
	foreach ($inventory as $key => $item){
		$items_array[$key]["attributes"] = $result[$items_array[$key]["attributes"]];
//		if ($item instanceof steam_document){
//			$items_array[$key]["content"] = $result[$items_array[$key]["content"]];
//		}
	}	
	foreach ($inventory as $key => $item){
		$action0 = false;
		$action1 = false;
		$action2 = false;
		$action3 = false;
		$action4 = false;
		$action5 = false;
		$qtip0 = false;
		$qtip1 = false;
		$qtip2 = false;
		$qtip3 = false;
		$qtip4 = false;
		$qtip5 = false;
		$hide2 = false;
		$attributes = $items_array[$key]["attributes"];
//		if ($attributes["LARS_HIDDEN"]){continue;}
//		if ($attributes["bid:hidden"] && !$showHidden){continue;}
//				print $item->get_path();
////				$object = steam_factory::path_to_object($steam, $item->get_path());
//				$object = steam_factory::path_to_object($steam, '/home/brix1/Lars Desktop/123/Aufgabenpaket%201/database_refresh.png');
//				print $object->get_id();
		$attributes["OBJ_ID"] = $item->get_id();
		if (strlen($attributes["LARS_COMMENT"]) > 5){
			$action1 = 'comments';
			$qtip1 = $attributes["LARS_COMMENT"];
		} else {
			$action1 = 'comment-edit';
			$qtip1 = msg('WRITE_COMMENT_QT');
		}
//				if ($attributes[OBJ_LAST_CHANGED] > $last_login_time){
//						$action3 = 'changed';
//						$qtip3 = 'Diese Datei wurde neu erstellt oder verändert seit dem letzten mal';
//				}
			
		if (!($item instanceof steam_docextern)){
		switch($attributes["DOC_MIME_TYPE"]){
		    case "text/html":
		    case "text/plain":
		    	$content = $item->get_content();
//				$content = $items_array[$key]["content"];
//						print $content;
				$content = "HTML-Text";
//						$content = strip_tags(_get_texthtmlnew($config_webserver_ip, stripslashes($content), $item));
				$mimeType = "Text";
				$qtip0 = msg('SHOW_HERE');
		    	$action2 = 'editPage';
				$qtip2 = msg('DOC_EDIT');
				$action4 = 'delete';
				$qtip4 = msg('DOC_DEL');
				$action5 = 'tab-go';
				$qtip5 = msg('OPEN_TAB');
				break;
			case "image/x-ms-bmp":
			case "image/gif":
			case "image/jpg":
			case "image/jpeg":
			case "image/png":
				$content = '<p style="text-align: center;"><img src="'.$config_webserver_ip.'/tools/get.php?mode=thumbnail&height=100&object='.$attributes["OBJ_ID"].'" border="0" /></p>'; 
				$qtip0 = msg('SHOW_HERE');
//						$content = _get_texthtmlnew($config_webserver_ip,'<p><img src="'.$attributes[OBJ_PATH].'" border="0" /></p>', $item);
//						$mimeType = $attributes["DOC_MIME_TYPE"];
				$mimeType = 'Bild';
				$action2 = 'page-save';
				$qtip2 = msg('PIC_FULL_SIZE');
				$action3 = 'cut';
				$qtip3 = msg('COPY_ADDR');
				$action4 = 'delete';
				$qtip4 = msg('DOC_DEL');
				$action5 = 'tab-go';
				$qtip5 = msg('PIC_TAB');
				break;
			default:
				$content = '<a href="'.$config_webserver_ip.'/tools/get.php?object='.$attributes["OBJ_ID"].'" title="'.$attributes["OBJ_NAME"].'">'.$attributes["OBJ_NAME"].'</a>';
//						$content = "<i>".$attributes["OBJ_NAME"]."</i> : ".$attributes["OBJ_DESC"];
				$mimeType = "Download";
//				$action2 = ($attributes["DOC_MIME_TYPE"] != 'application/pdf') ? 'page-save' : "pdf";
				$action2 = 'page-save';
				$qtip2 = msg('DOC_DOWNLOAD');				
				$hide2 = 1;						
				$action3 = 'add-page';
				$qtip3 = msg('ADD_SOLUTION');
				$action4 = 'delete';
				$qtip4 = msg('DOC_DEL');
				break;
		    }//end switch
		} else {
			$content = $attributes["DOC_EXTERN_URL"]; 
//					$content = '<p style="text-align: center;"><a href="'.$attributes["DOC_EXTERN_URL"].'">'.$attributes["DOC_EXTERN_URL"].'</a></p>'; 
			$qtip0 = msg('LINK_TAB');
			$mimeType = "Link";
			$hide2 = 1;
			$action4 = 'delete';
			$qtip4 = msg('LINK_DEL');
		}//end if !docextern
	
	$name_parts = pathinfo($attributes["OBJ_PATH"]);
	$name_parts["extension"] = ($mimeType == "Link") ? "link" : $name_parts["extension"];
	$name_parts["extension"] = ($attributes["DOC_MIME_TYPE"] == "text/html") ? "html" : $name_parts["extension"];
	$name_parts["extension"] = ($attributes["DOC_MIME_TYPE"] == "text/plain") ? "txt" : $name_parts["extension"];
	$data[] = array(
			text=>$attributes["OBJ_NAME"], 
			type=>$mimeType, 
//					content=>$content, 
			LARS_CONTENT=>$content, 
			id=>$attributes["OBJ_ID"], 
			OBJ_NAME=>$attributes["OBJ_NAME"], 
			OBJ_CREATION_TIME=>$attributes["OBJ_CREATION_TIME"]+1,
			OBJ_LAST_CHANGED=>$attributes["OBJ_LAST_CHANGED"]+1,
//					DOC_LAST_MODIFIED=>$attributes["DOC_LAST_MODIFIED"]+1,
//					CONT_LAST_MODIFIED=>$attributes["CONT_LAST_MODIFIED"]+1,
			OBJ_DESC=>$attributes["OBJ_DESC"],
			LARS_STATE=>$attributes["LARS_STATE"],
//					CONT_USER_MODIFIED=>"!!!!!Container",
//					DOC_USER_MODIFIED=>"!!!!!Dokument",
			OBJ_PATH=>$attributes["OBJ_PATH"],
			LARS_FOLDER=>$folder_name,
			LARS_COMMENT=>$attributes["LARS_COMMENT"],
			OBJ_TYPE=>$attributes["OBJ_TYPE"],
			LARS_TYPE=>$attributes["LARS_TYPE"],
			action0=>"file-".$name_parts["extension"],
			action1=>$action1,
			action2=>$action2,
			action3=>$action3,
			action4=>$action4,
			action5=>$action5,
			qtip0=>$qtip0 ? $qtip0 : "",
			qtip1=>$qtip1 ? $qtip1 : "",
			qtip2=>$qtip2 ? $qtip2 : "",
			qtip3=>$qtip3 ? $qtip3 : "",
			qtip4=>$qtip4 ? $qtip4 : "",
			qtip5=>$qtip5 ? $qtip5 : "",
			hide1=>$action1 ? 0 : 1,
			hide2=>$hide2 ? 1 : 0,
			hide3=>$action3 ? 0 : 1,
			hide4=>$action4 ? 0 : 1,
			hide5=>$qtip5 ? 0 : 1
			);
		
		
	}
	$output[success] = true;
	$output[ass] = $data;
	$output = json_encode($output);  //encode the data in json format
    echo $output;
	$steam->disconnect();
	
}
		

/*
 * Neue Dokumente rekursiv mit getNewItemsRec holen
 * Dokumente auf dem eigenen Schreibtisch und der Dokumente im Folder des User-Attributs MOKO_SUBSCRIPTION_CHECK
 */
function getNewItems($steam)
{
	global $config_webserver_ip;
	$lag = $_POST['time'] ? ($_POST['time']*3600) : 0;
//	if ($_POST['time']){
//		error_log("time:".time()."Lag:".$lag);
//	}
	$login_user = $steam->get_current_steam_user();
	// get folder for the new assignment package

	$subscriptions = $login_user->get_attribute("MOKO_SUBSCRIPTION_CHECK", 1);
	$home = $login_user->get_attribute("LARS_DESKTOP", 1);
//	$current_folder2 = $login_user->get_attribute("LARS_SCHUELER", 1);
//	$current_folder3 = $login_user->get_attribute("LARS_ABO", 1);
	$result = $steam->buffer_flush();
	$subscriptions = $result[$subscriptions];
	$home = $result[$home];
//	$current_folder2 = $result[$current_folder2];
//	$current_folder3 = $result[$current_folder3];

	$all_folder = array($home, $subscriptions);
	$inventory_cache = array();
	$inventory = array();
//	print_r($all_folder);
	foreach ($all_folder as $key => $value) {
		$inventory_cache[$key] = $value->get_inventory_filtered(
		array(
			array( '-', 'attribute', 'bid:hidden', '==', true ),
//			array( '-', 'attribute', 'LARS_HIDDEN', '==', true ),
			array( '-', '!access', SANCTION_READ ),
//			array( '+', 'class', 0x0000001f), //documents, container, ...
			array( '+', 'class', 0x00000026), //rooms, container
			),
			array(),
			0,
			0,
			1
		);
	}
	
	$last_login_time = $login_user->get_attribute(USER_LAST_LOGIN_LARS, 1);
	$result = $steam->buffer_flush();
	foreach ($all_folder as $key => $value) {
		$inventory = array_merge($result[$inventory_cache[$key]], $inventory);
	}
	$last_login_time = $result[$last_login_time];

	$current_lars_room = array();
	foreach ($inventory as $key => $studentsLarsFolder) { 
		if ($studentsLarsFolder instanceof steam_link){
			$current_lars_room[$key] = $studentsLarsFolder->get_link_object(1);
		} else {
			$current_lars_room[$key] = $studentsLarsFolder;
		}
		$description_array[$key] = $studentsLarsFolder->get_attribute("OBJ_DESC", 1);
	}
	$result = $steam->buffer_flush();
	foreach ($inventory as $key => $studentsLarsFolder) { 
		if ($studentsLarsFolder instanceof steam_link){
			$current_lars_room[$key] = $result[$current_lars_room[$key]];
			$is_link[$key] = true;
		} else {
			$current_lars_room[$key] = $studentsLarsFolder;
			$is_link[$key] = false;
		}
		$description_array[$key] = $result[$description_array[$key]];
	}

	$is_readable = array();
	foreach ($inventory as $key => $studentsLarsFolder) { 
		if ($is_link[$key] && $current_lars_room[$key]){ //Zweites Argument nötig, wenn ein Schriebtisch gelöscht wurde
			$is_readable[$key] = $current_lars_room[$key]->check_access_read( $login_user, 1 );
		}
	}
	$result = $steam->buffer_flush();
	foreach ($inventory as $key => $studentsLarsFolder) { 
		if ($is_link[$key]){
			if (!($current_lars_room[$key] instanceof steam_container)){
				$is_readable[$key] = false;
			} else {
				$is_readable[$key] = $result[$is_readable[$key]];
			}
		} else {
			$is_readable[$key] = true;
		}
	}
		
	$data = array();
	foreach ($inventory as $key => $studentsLarsFolder){
		$dataTmp = array();
		if ($is_readable[$key]){
			$is_home = !$is_link[$key];
			$dataTmp = (getNewItemsRec($steam, $current_lars_room[$key], $last_login_time - $lag, $dataTmp, $description_array[$key]."/", $is_home));
			$data = array_merge($data, $dataTmp);
		}	
	}
	$output[success] = true;
	$output[newItems] = $data;
	$output = json_encode($output);
    echo $output;
	$steam->disconnect();
	exit;
}

function getNewItemsRec($steam, $current_folder, $last_login_time, $data, $folder_name, $is_home = false){
	global $config_webserver_ip;
	$login_user = $steam->get_current_steam_user();
//	print($current_folder->get_name()."\n");
//print $folder_name.$current_folder->get_name()."/\n";
	$inventory_documents = $current_folder->get_inventory_filtered(
		array(
			array( '-', 'attribute', 'bid:hidden', '==', 'hide_always' ),
			array( '-', 'attribute', 'bid:hidden', '==', 'true' ),//Das wirkt
			array( '-', 'attribute', 'OBJ_LAST_CHANGED', '<', $last_login_time ),
			array( '-', 'attribute', 'DOC_USER_MODIFIED', '==', $login_user ),
			// TODO: wird zwischen llusti03 und llustig04 nicht korrekt gefiltert!!!
//			array( '+', 'attribute', 'OBJ_TYPE', '==', "LARS_MESSAGES" ),
			array( '+', 'class', 0x00000930),//documents, images, links, ...
//			array( '+', 'class', 0x0000ffff),//documents, images, links, ...
			),
			array(),0,0,1
		);
	$inventory_container = $current_folder->get_inventory_filtered(
		array(
			array( '-', 'attribute', 'bid:hidden', '==', 'hide_always' ),
			array( '-', 'attribute', 'bid:hidden', '==', true ),
			array( '-', 'attribute', 'OBJ_TYPE', '==', "LARS_ARCHIV" ),
//			array( '-', 'attribute', 'LARS_HIDDEN', '==', true ),//maybe needed
			array( '+', 'attribute', 'OBJ_TYPE', '==', "LARS_MESSAGES" ),
			array( '+', 'class', 0x00000006),
			),
			array(),0,0,1
		);
	$container_type = $current_folder->get_attribute("OBJ_TYPE", 1);
	$result = $steam->buffer_flush();
	$inventory_documents = $result[$inventory_documents];
	$inventory_container = $result[$inventory_container];
	$container_type = $result[$container_type];
	
	foreach ($inventory_documents as $key => $item) { 
		$items_attributes_array[$key] = $item->get_attributes(array(DOC_MIME_TYPE, OBJ_NAME, OBJ_DESC, OBJ_PATH, OBJ_CREATION_TIME, OBJ_LAST_CHANGED, LARS_STATE, LARS_COMMENT, OBJ_TYPE), 1);
//		if ($item instanceof steam_document){
//			$items_content_array[$key] = $item->get_content(1);
//		}
	}
	$result = $steam->buffer_flush();
	foreach ($inventory_documents as $key => $item) { 
		$items_attributes_array[$key] = $result[$items_attributes_array[$key]];
		if ($item instanceof steam_document){
//			$items_content_array[$key] = $result[$items_content_array[$key]];
			$items_content_array[$key] = "---";
		}
	}


	foreach ($inventory_container as $container){
		$data = getNewItemsRec($steam, $container, $last_login_time, $data, $folder_name."".$container->get_attribute("OBJ_DESC")."/", $is_home);
		}

	foreach ($inventory_documents as $key => $item){
		$action0 = false;
		$action1 = false;
		$action2 = false;
		$action3 = false;
		$action4 = false;
		$action5 = false;
		$qtip0 = false;
		$qtip1 = false;
		$qtip2 = false;
		$qtip3 = false;
		$qtip4 = false;
		$qtip5 = false;
		$attributes = $items_attributes_array[$key];
//		$attributes["DOC_USER_MODIFIED"];
//		print_r($items_attributes_array[$key]);
//		if ($attributes["LARS_HIDDEN"]){continue;}
//		if ($attributes["bid:hidden"] && !$showHidden){continue;}
		$attributes["OBJ_ID"] = $item->get_id();
		if (strlen($attributes["LARS_COMMENT"]) > 5){
			$action1 = 'comments';
			$qtip1 = $attributes["LARS_COMMENT"];
		} else {
			$action1 = 'comment-edit';
			$qtip1 = 'Schreibe eine Anmerkung zu diesem Dokument';
		}
		
	if (!($item instanceof steam_docextern)){		
		switch($attributes[DOC_MIME_TYPE]){
		    case "text/html":
		    case "text/plain":
				$content = "HTML-Text";
//						$content = $item->get_content();
//						$content = _get_texthtmlnew($config_webserver_ip, stripslashes($content), $item);
//				    	$content = strip_tags($content);
		    	$mimeType = "Text";
				$qtip0 = msg('SHOW_HERE');
		    	$action2 = 'editPage';
				$qtip2 = msg('DOC_EDIT');
				$action4 = 'delete';
				$qtip4 = msg('DOC_DEL');
				$action5 = 'tab-go';
				$qtip5 = msg('OPEN_TAB');
				break;
			case "image/x-ms-bmp":
			case "image/gif":
			case "image/jpg":
			case "image/jpeg":
			case "image/png":
			case "image/tiff":
				$content = '<p style="text-align: center;"><img src="'.$config_webserver_ip.'/tools/get.php?mode=thumbnail&height=100&object='.$attributes["OBJ_ID"].'" border="0" /></p>'; 
				$qtip0 = msg('SHOW_HERE');
//						$content = _get_texthtmlnew($config_webserver_ip,'<p><img src="'.$attributes[OBJ_PATH].'" border="0" /></p>', $item);
//						$mimeType = $attributes["DOC_MIME_TYPE"];
				$mimeType = 'Bild';
				$action2 = 'page-save';
				$qtip2 = msg('PIC_FULL_SIZE');
				$action3 = 'cut';
				$qtip3 = msg('COPY_ADDR');
				$action4 = 'delete';
				$qtip4 = msg('DOC_DEL');
				$action5 = 'tab-go';
				$qtip5 = msg('PIC_TAB');
				break;
			default:
				$content = '<a href="'.$config_webserver_ip.'/tools/get.php?object='.$attributes["OBJ_ID"].'" title="'.$attributes["OBJ_NAME"].'">'.$attributes["OBJ_NAME"].'</a>';
//						$content = "<i>".$attributes["OBJ_NAME"]."</i> : ".$attributes["OBJ_DESC"];
				$mimeType = "Download";
//				$action2 = ($attributes["DOC_MIME_TYPE"] != 'application/pdf') ? 'page-save' : "pdf";
				$action2 = 'page-save';
				$qtip2 = msg('DOC_DOWNLOAD');
				$hide2 = 1;						
				$action4 = 'delete';
				$qtip4 = msg('DOC_DEL');
				break;
		    }//end switch
		}else{
			$content = $attributes["DOC_EXTERN_URL"]; 
//					$content = '<p style="text-align: center;"><a href="'.$attributes["DOC_EXTERN_URL"].'">'.$attributes["DOC_EXTERN_URL"].'</a></p>'; 
			$qtip0 = msg('LINK_TAB');
			$mimeType = "Link";
			$hide2 = 1;
			$action4 = 'delete';
			$qtip4 = msg('LINK_DEL');
		}//end if !docextern
			
	$name_parts = pathinfo($attributes["OBJ_PATH"]);
	$name_parts["extension"] = ($mimeType == "Link") ? "link" : $name_parts["extension"];
	$name_parts["extension"] = ($attributes["DOC_MIME_TYPE"] == "text/html") ? "html" : $name_parts["extension"];
	$name_parts["extension"] = ($attributes["DOC_MIME_TYPE"] == "text/plain") ? "txt" : $name_parts["extension"];
    $data[] = array(
			text=>$attributes["OBJ_NAME"], 
			type=>$mimeType, 
//					content=>$content, 
			LARS_CONTENT=>$content, 
			id=>$attributes["OBJ_ID"], 
			OBJ_NAME=>$attributes["OBJ_NAME"], 
			OBJ_CREATION_TIME=>$attributes["OBJ_CREATION_TIME"]+1,
			OBJ_LAST_CHANGED=>$attributes["OBJ_LAST_CHANGED"]+1,
//					DOC_LAST_MODIFIED=>$attributes["DOC_LAST_MODIFIED"]+1,
//					CONT_LAST_MODIFIED=>$attributes["CONT_LAST_MODIFIED"]+1,
			OBJ_DESC=>$attributes["OBJ_DESC"],
			LARS_STATE=>$attributes["LARS_STATE"],
//					CONT_USER_MODIFIED=>"!!!!!Container",
//					DOC_USER_MODIFIED=>"!!!!!Dokument",
			OBJ_PATH=>$attributes["OBJ_PATH"],
			LARS_FOLDER=>$folder_name,
			LARS_COMMENT=>$attributes["LARS_COMMENT"],
			OBJ_TYPE=>$container_type, // because it depends on environment
//			OBJ_TYPE=>$attributes["OBJ_TYPE"],
			action0=>"file-".$name_parts["extension"],
			is_home=>$is_home,
			action1=>$action1,
			action2=>$action2,
			action3=>$action3,
			action4=>$action4,
			action5=>$action5,
			qtip0=>$qtip0 ? $qtip0 : "",
			qtip1=>$qtip1 ? $qtip1 : "",
			qtip2=>$qtip2 ? $qtip2 : "",
			qtip3=>$qtip3 ? $qtip3 : "",
			qtip4=>$qtip4 ? $qtip4 : "",
			qtip5=>$qtip5 ? $qtip5 : "",
			hide1=>$action1 ? 0 : 1,
			hide2=>$hide2 ? 1 : 0,
			hide3=>$action3 ? 0 : 1,
			hide4=>$action4 ? 0 : 1,
			hide5=>$qtip5 ? 0 : 1
			);
	}
	return $data;
}
function deleteItem($steam, $id){
//	include("lars_lang.php");
	$name = stripslashes($_POST['name']);
	$irrevocable = $_POST['irrevocable'];
	try{
	    $toDelete = steam_factory::get_object($GLOBALS["STEAM"]->get_id(),$id);
	    if (strcmp($toDelete->get_attribute("OBJ_NAME"), $name) == 0){
	    	if ($irrevocable){
		    	if ($toDelete->delete())
		    		print (json_encode(array(success => true)));
	    	} else{
		    	if ($toDelete->move($steam->get_current_steam_user()->get_attribute("USER_TRASHBIN")))
		    		print (json_encode(array(success => true)));
	    	}
	    }else{
	    	print (json_encode(array(success => false, message => utf8_encode(msg('FAILURE_DELETE')))));
	    }
    }catch(Exception $e){
		print (json_encode(array(success => false, message => "steam exception")));
		error_log("00025:".$e->getMessage());
	}
	$steam->disconnect();
}
function emptyTrash($steam){
	$trashbin = $steam->get_current_steam_user()->get_attribute("USER_TRASHBIN");
	$objectInventory = $trashbin->get_inventory();
	foreach ($objectInventory as $item){
		$item->delete(1);
	}
	$steam->buffer_flush();
    $steam->disconnect();
    print (json_encode(array(success => true)));
}

function getUserIcon($steam){
 global $config_webserver_ip;
 $name = $_POST['name'];
// $user = steam_factory::get_user($steam, $name);
// $attributes = $user->get_attribute_names();
// if (in_array("OBJ_ICON", $attributes)){
//	 $imageId = $user->get_attribute("OBJ_ICON")->get_id();
//	 $imageUri = '<img src="'.$config_webserver_ip.'/tools/get.php?mode=thumbnail&height=50&object='.$imageId.'" border="0" height="100%" align="left" vspace="5" hspace="5" />';
	 $imageUri = '<img src="'.$config_webserver_ip.'/moko/images/chat_bubble.png" border="0" align="left" vspace="5" hspace="5" />';
// }else{
// 	 error_log("no image icon");
// 	 $imageUri="";
// }
 print (json_encode(array(success => true, imageUri => $imageUri)));
}
	
function getCustomImage($steam){
 global $config_webserver_ip;
 $login_user = $steam->get_current_steam_user();
 $attributes = $login_user->get_attribute_names();
 if (in_array("OBJ_ICON", $attributes)){
	 $imageId = $login_user->get_attribute("OBJ_ICON")->get_id();
	 print ('<img src="'.$config_webserver_ip.'/tools/get.php?mode=thumbnail&height=100&object='.$imageId.'" border="0" height="100%" />'); //TODO: Not working locally 
//	 print ('"tools/get.php?mode=thumbnail&height=100&object='.$imageId.'" border="0" height="100%"'.'<img src="tools/get.php?mode=thumbnail&height=100&object='.$imageId.'" border="0" height="100%" />'); 
 } else {
 	print ("Doppelklick um hier ein eigenes Bild anzeigen zu lassen.");// TODO: Sprachen
 }
// print (json_encode(array(success => true, imageId => $imageId)));
}
function setCustomImage($steam){
	require_once("includes/derive_mimetype.php");
	try{
	$login_user = $steam->get_current_steam_user();
	$description = $_POST['description'];
//	$fileLocation = utf8_decode($_POST['location']);
	$fileLocation = $_POST['location'];
	$baseFileName =  basename($fileLocation);
	$fileContent = file_get_contents( urlencode($fileLocation) );
	$mimetype = derive_mimetype( $baseFileName );
	//create image
	$result = steam_factory::create_document($GLOBALS["STEAM"]->get_id(), tidyName($baseFileName), $fileContent, $mimetype, false, tidyDesc($baseFileName) );
	
	$login_user->set_attribute("OBJ_ICON", $result);
	
	if( tidyDesc($_POST["description"]) != "" )
		$result->set_attribute( "OBJ_DESC", tidyDesc($description) );
		
	print (json_encode(array(success => true)));
	}catch (Exception $e){
	print "catch";
	}
}
function setLarsPackageState($steam, $id){
	$login_user = $steam->get_current_steam_user();
	$state = $_POST['state'];
    $packageContainer = steam_factory::get_object($GLOBALS["STEAM"]->get_id(),$id);
	
	$packageContainer->set_attribute("LARS_STATE", $state);

	print (json_encode(array(success => true)));

}


############------------getgroups begin
function getRightsGroups($steam, $id)
{
	global $config_webserver_ip;
	$login_user = $steam->get_current_steam_user();
	$groups = $login_user->get_groups();
	$buddies = $login_user->get_buddies();
	$buddies_ids = array();
	foreach ($buddies as $buddy){
		$buddies_ids[] = $buddy->get_id();
	}
	$groups = is_array($login_user->get_buddies()) ? array_merge($groups, $buddies) : $groups;
	$lars_abo = $login_user->get_attribute("LARS_ABO")->get_inventory();
	$lars_desktop = $login_user->get_attribute("LARS_DESKTOP");
	
	// check if already in abo
	foreach ($groups as $group){
		$abo = 0;
		if (!($group instanceof steam_object))
			continue;
		$name = ($group instanceof steam_group) ? $group->get_groupname() : $group->get_attribute("OBJ_NAME");
		$data[] = array(
//				text=>$group->get_attribute("OBJ_NAME"),
//				text=>$group->get_groupname(), 
				text=>$name, 
				id=>$group->get_id(),
				group=>($group instanceof steam_group) ? "Gruppe" : "Nutzer",
				fav=>(in_array($group->get_id(), $buddies_ids)) ? "Ja" : "Nein",
				ACCESS_READ=>$lars_desktop->check_access_read($group)?1:0,
				ACCESS_WRITE=>$lars_desktop->check_access_write($group)?1:0,
				);
	}
	$output[success] = true;
	$output[groups] = $data;
	$output = json_encode($output);  //encode the data in json format
    echo $output;
	$steam->disconnect();
}

/*
 * Anzeige der Gruppen dessen Schreibtisch abonniert werden kann
 */
function getAboGroups($steam, $id)
{
	global $config_webserver_ip;
	$login_user = $steam->get_current_steam_user();
	$groups = $login_user->get_groups();
	$lars_abo = $login_user->get_attribute("LARS_ABO")->get_inventory();
	$lars_desktop = $login_user->get_attribute("LARS_DESKTOP");
	
	// check if already in abo
	foreach ($groups as $group){
		$abo = 0;
		if (!($group->get_workroom() instanceof steam_object)){continue;}
		$access_read = $group->get_workroom()->check_access_read( $steam->get_current_steam_user() );
		if ($access_read){
			$group_workroom = $group->get_workroom();
			$group_inventory = $group_workroom->get_inventory();
			for( $i=0; $i < count($group_inventory); $i++ ){
				if( $group_inventory[$i]->get_attribute(OBJ_TYPE) == "LARS_DESKTOP"){
					$groupLarsDesktop = $group_inventory[$i];
					for( $j=0; $j < count($lars_abo); $j++ ){
						if( $group_inventory[$i]->get_id() == $lars_abo[$j]->get_link_object()->get_id()){
							$abo = 1;
							break;
						}
					}
				}
			}
		} elseif ($id == "desktops") { // Gruppen die nicht gelesen werden für das Abonnieren übersprungen 
			continue;
		}

		$data[] = array(
//				text=>$group->get_parent_and_group_name(), 
				text=>$group->get_groupname(), 
				id=>$group->get_id(), 
				ABO=>$abo,
				);
	}
	$output[success] = true;
	$output[groups] = $data;
	$output = json_encode($output);  //encode the data in json format
    echo $output;
	$steam->disconnect();
}

function updateGroup($steam, $id)
{
try{
//	include("lars_lang.php");

	    $id 		= $_POST['keyValue'];
	    $key    	= $_POST['key'];
	    $field 		= $_POST['field'];
    	$fieldValue = $_POST['fieldValue'];
	    $message 	= " ";

	    $group_object = steam_factory::get_object($GLOBALS["STEAM"]->get_id(),$id);
	    
	    switch($field){
    		case "ABO":
				if ($fieldValue && $fieldValue != "false"){
					$abo_container = $steam->get_current_steam_user()->get_attribute("LARS_ABO");
					// Überprüfen, ob ein Desktop vorhanden ist und ggf. erstellen
					$group_workroom = $group_object->get_workroom();
					
					$group_inventory = $group_workroom->get_inventory();
					for( $i=0; $i < count($group_inventory); $i++ )
						if( $group_inventory[$i]->get_attribute(OBJ_TYPE) == "LARS_DESKTOP"){
							$object_to_link = $group_inventory[$i];
							break;
						}
					if (!$object_to_link){
						$new_lars_room = steam_factory::create_room($GLOBALS["STEAM"]->get_id(), $group_object->get_groupname()."", $group_workroom, $group_object->get_groupname()."");
						$new_lars_room->set_attribute("OBJ_TYPE", "LARS_DESKTOP");
						$object_to_link = $new_lars_room;
						$new_lars_archiv = steam_factory::create_room($GLOBALS["STEAM"]->get_id(), "Archiv", $new_lars_room, "Archiv");
						$new_lars_archiv->set_attribute("OBJ_TYPE", "LARS_ARCHIV");
						$new_lars_archiv->set_attribute("LARS_HIDDEN", true);
					}
					$access_write = $object_to_link->check_access_write( $steam->get_current_steam_user() );
					$access_read = $object_to_link->check_access_read( $steam->get_current_steam_user() );
					if (!$access_read && !$access_write){
					    print (json_encode(array(success => false, name=>msg('NO_WRITE_OR_READ_ACCESS'))));
					    exit;
					}
					$newlink = steam_factory::create_link($GLOBALS["STEAM"]->get_id(), $object_to_link);
					$linkName = $group_object->get_groupname()."";
//					$linkName = $group_object->get_attribute("OBJ_NAME");
					$newlink->set_attribute("OBJ_NAME", $linkName);
					$newlink->set_attribute("OBJ_DESC", $linkName);
					$newlink->move($abo_container);
				} else {
					$lars_abo_inventory = $steam->get_current_steam_user()->get_attribute("LARS_ABO")->get_inventory();
					$group_workroom = $group_object->get_workroom();
					$group_inventory = $group_workroom->get_inventory();
					for( $i=0; $i < count($group_inventory); $i++ )
						if( $group_inventory[$i]->get_attribute(OBJ_TYPE) == "LARS_DESKTOP"){
							$object_to_unlink = $group_inventory[$i];
							break;
						}
					for( $i=0; $i < count($lars_abo_inventory); $i++ ){
						if( $object_to_unlink->get_id() == $lars_abo_inventory[$i]->get_link_object()->get_id()){
							$lars_abo_inventory[$i]->delete();
							break;
						}
					}
				}
        		print (json_encode(array(success => true)));
				break;
    		case "ACCESS_READ":
    			$user_desktop = $steam->get_current_steam_user()->get_attribute("LARS_DESKTOP");
    			if ($fieldValue && $fieldValue != "false"){
					$user_desktop->set_read_access($group_object,1);
				} else {
					$user_desktop->set_read_access($group_object,0);
				}
            	print (json_encode(array(success => true)));
				break;
        	case "ACCESS_WRITE":
    			$user_desktop = $steam->get_current_steam_user()->get_attribute("LARS_DESKTOP");
    			if ($fieldValue && $fieldValue != "false"){
    				$user_desktop->set_sanction_all($group_object);
    			} else {
    				$user_desktop->sanction(ACCESS_DENIED, $group_object);
    			}
        		print (json_encode(array(success => true)));
    			break;
	    }
	}catch(Exception $e){
        print (json_encode(array(success => false, name=>$e->getMessage())));
	}
    $steam->disconnect();
	
}

function getGroupsTree($steam, $id){
	global $phpsteamApiRoot;
//	require_once("$phpsteamApiRoot/modules/groups.class.php");
	$arr = array();
	$id = ($_POST['node']) ? ($_POST['node']) : null;
	if ($id == "source"){
		$subGroups = $steam->get_server_module("groups")->get_top_groups();
	} else {
	    $master_group = steam_factory::get_object($GLOBALS["STEAM"]->get_id(),$id);
		$subGroups = $master_group->get_subgroups();
	}
//	echo $master_group->get_name();
//	echo $master_group->get_parent_group()->get_name();
//	print_r($subGroups);
	foreach ($subGroups as $group) {
		$arr[] = array(
//			"text"=>$group->get_groupname(),
			"text"=>$group->get_attribute("OBJ_NAME"),
			"id"=>$group->get_id(), 
			"iconCls"=>"group",
	    ); 
	}
  	echo json_encode($arr);      
    $steam->disconnect();                                    
}
function addBuddy($steam, $id){
//	include("lars_lang.php");
	$login_user = $steam->get_current_steam_user();
	$buddies = $login_user->get_buddies();
  	if(!is_array($buddies)) $buddies = array();
  	if ($id){
  		$new_group = steam_factory::get_object($GLOBALS["STEAM"]->get_id(),$id);
  	} else {
		$name = ($_POST['name']) ? ($_POST['name']) : null;
  		$new_group = steam_factory::get_user($GLOBALS["STEAM"]->get_id(),$name);
  	}
	
//  	$access_read = $new_group->check_access_read( $steam->get_current_steam_user() );
//  	if ($access_read){
	if ($new_group instanceof steam_user || $new_group instanceof steam_group){
  		$buddies[] = $new_group;
  	} else {
		print (json_encode(array(success => false, message => msg('FAILURE'))));
		$steam->disconnect();                           
		exit;
  	}
	$steam->get_current_steam_user()->set_buddies($buddies);
	$steam->disconnect();                           
	print (json_encode(array(success => true)));
}
function deleteBuddy($steam, $id){
//	include("lars_lang.php");
	$login_user = $steam->get_current_steam_user();
	$buddies = $login_user->get_buddies();
  	if(!is_array($buddies)) $buddies = array();
  	if ($id){
  		$delete_group = steam_factory::get_object($GLOBALS["STEAM"]->get_id(),$id);
  	} else {
		$name = ($_POST['name']) ? ($_POST['name']) : null;
  		$delete_group = steam_factory::get_user($GLOBALS["STEAM"]->get_id(),$name);
  	}

  	for ($i = 0; $i < count($buddies); $i++){
//	print $buddies[$i]->get_id()."###".$delete_group->get_id()."\n";
  		if ($buddies[$i]->get_id() == $delete_group->get_id()){
			array_splice($buddies, $i, $i);
			$success = true;
		}
  	}
	if ($success){
		$steam->get_current_steam_user()->set_buddies($buddies);
		print (json_encode(array(success => true)));                         
	} else {
		print (json_encode(array(success => false, message => msg('FAILURE'))));
	}
	
}
############------------getgroups end

//############################################################
function saveData($steam, $id)
{
try{

	    $id = $_POST['keyValue'];
	    $key    = $_POST['key'];
	    $field = $_POST['field'];
	    $fieldValue = $_POST['fieldValue'];
	    $message = " ";
	    
	    switch($field){
    		case "OBJ_DESC":
    	    	if ($fieldValue == "")
    			$fieldValue = " ";
    	    	break;
    		case "LARS_EAST_COLLAPSED":
    	    	$id = $steam->get_current_steam_user()->get_id();
    	    	break;
	    }
		$current_obj = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $id );
		$current_obj->set_attribute($field, $fieldValue, 1);
		if ($field == "OBJ_DESC" && !($current_obj instanceOf steam_document))
			$current_obj->set_attribute("OBJ_NAME", tidyName($fieldValue), 1);
		
		print (json_encode(array(success => true, name=>$message)));

	}catch(Exception $e){
        print (json_encode(array(success => false, name=>$e->getMessage())));
	}
	$steam->buffer_flush();
    $steam->disconnect();
	
}//end saveData
?>
