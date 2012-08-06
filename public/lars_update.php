<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once("mokodesk_steam.php");

$task = ($_POST['task']) ? ($_POST['task']) : null;
#if ($task == "getUsersLastLogin"){exit;}
#if ($task == "getUpdate"){exit;}

//	include("../etc/config.php");
//    include("$phpsteamApiRoot/get_current_steam_user.class.php");
    include("lars_tools.php");
session_name("bidowl_session");
session_start();      
	$loginName = ($_SESSION['user']) ? ($_SESSION['user']) : null;
	$loginPwd = ($_SESSION['pass']) ? ($_SESSION['pass']) : null;
    $lastUpdate = $_SESSION['lastUpdate'] ? $_SESSION['lastUpdate'] : time();
	$current_folder_array_ids = $_SESSION['current_folder_array_ids'] ? $_SESSION['current_folder_array_ids'] : array();	
	$oldIds = $_SESSION['oldIds'] ? $_SESSION['oldIds'] : "";	
//UpdateUsersLogin
	$desktops_links_array_ids = $_SESSION['desktops_links_array_ids'] ? $_SESSION['desktops_links_array_ids'] : array();
	$owner_array_ids = $_SESSION['owner_array_ids'] ? $_SESSION['owner_array_ids'] : array();
	$usersOn = ($_SESSION['usersOn']) ? ($_SESSION['usersOn']) : array();
	$usersOff = ($_SESSION['usersOff']) ? ($_SESSION['usersOff']) : array();
	$LANG = ($_SESSION['language']) ? ($_SESSION['language']) : "de";
session_write_close();	
if ($task == "updateIds"){
    updateIds();
	exit;
}
	$ids = ($_POST['id']) ? ($_POST['id']) : null;
	$idUser = ($_POST['idUser']) ? ($_POST['idUser']) : null;
    try{
		$steam = mokodesk_steam::connect(	$configServerIp,
                                    $configServerPort,
                                    $loginName,
                                    $loginPwd);
	}catch(Exception $e){
        print (json_encode(array(success => false)));
		error_log("get_current_steam_user Exception");
		exit;
	}							  								
    if( !$steam || !$steam->get_login_status() )
    {
    	print (json_encode(array(success => false, name=>"Du bist nicht mehr auf dem Server eingeloggt!")));
//        ErrorException::getTrace();
    	exit();
    }

switch($task){
    case "getUpdates":
		$newIds = false;
		if (strcasecmp($oldIds, $ids) != 0) {$newIds = true;}
        getUpdates($steam, $ids, $lastUpdate, 0, $newIds, $current_folder_array_ids);
    	$steam->disconnect();
        break;
    case "getUsersLastLogin":
        getUsersLastLogin($steam, $usersOn, $usersOff, 0, $owner_array_ids, $desktops_links_array_ids);
    	$steam->disconnect();
        break;
}
function updateIds(){
	$ids = ($_POST['id']) ? ($_POST['id']) : null;
	if (!empty($ids)){
		session_name("bidowl_session");
		session_start();    
		$_SESSION['updateIds'] = $ids;
		session_write_close();
		$output[success] = true;
		$output[name] = "ids updated";
		$output = json_encode($output);
		echo $output;
		exit;
	}
	$output[success] = false;
	$output[name] = "no id added";
	$output = json_encode($output);	
	echo $output;
	return;
}

function getUsersLastLogin($steam, $usersOn, $usersOff, $count, $owner_array_ids = array(), $desktops_links_array_ids = array()){
	$login_user = $steam->get_current_steam_user();
	$now = time();
	$login_user->set_attribute('USER_LAST_LOGIN_LARS', time(), 1);
//	$login_user->set_attribute('USER_LAST_LOGIN_LARS', time());
//	error_log($login_user->get_id().":".time());
//error_log($login_user->get_attribute(USER_LAST_LOGIN_LARS).':::'.time());
//error_log("owner:".print_r($owner_array_ids,1));
if (empty($owner_array_ids)){	
	$schueler_link_room = $steam->get_current_steam_user()->get_attribute("LARS_SCHUELER");
	$inventory = $schueler_link_room->get_inventory_filtered(
		array(
//			array( '-', 'attribute', 'bid:hidden', '==', true ),
//			array( '-', 'attribute', 'LARS_HIDDEN', '==', true ),
//			array( '-', '!access', SANCTION_READ ),
//			array( '+', 'class', 0x0000001f), //documents, container, ...
			array( '+', 'class', 0x00000026), //rooms, container
			),
			array(),
			0,
			0,
			0
		);
	

// Desktop Ordner holen	
	$desktops_links_array = array();
	$desktops_array = array();
	foreach ($inventory as $key => $item) { 
		if ($item instanceof steam_link){
			$desktops_array[$key] = $item->get_link_object(1);
			$desktops_links_array[$key] = $item;
			$desktops_links_array_ids[$key] = $desktops_links_array[$key]->get_id();
		}
	}
	$result = $steam->buffer_flush();
	foreach ($inventory as $key => $item) { 
		if ($item instanceof steam_link){
			$desktops_array[$key] = $result[$desktops_array[$key]];
		}
	}

// Lesbarkeit prüfen
	$is_readable = array();
	foreach ($desktops_array as $key => $item) { 
		if ($desktops_array[$key]){
			$is_readable[$key] = $desktops_array[$key]->check_access_read( $login_user, 1 );
		}
	}
	$result = $steam->buffer_flush();
	foreach ($desktops_array as $key => $item) { 
		$is_readable[$key] = $result[$is_readable[$key]];
	}

// Owner herausfinden
	$owner_array = array();
	foreach ($desktops_array as $key => $item) { 
		if ($is_readable[$key]){
			$owner_array[$key] = $item->get_creator(1);
		}
	}
	$result = $steam->buffer_flush();
	foreach ($desktops_array as $key => $item) { 
		if ($is_readable[$key]){
			$owner_array[$key] = $result[$owner_array[$key]];
			$owner_array_ids[$key] = $owner_array[$key]->get_id();
		}
	}

// Attribute herausfinden
	$attribute_exists_array = array();
	foreach ($desktops_array as $key => $item) { 
		if ($is_readable[$key]){
			$attribute_exists_array[$key] = $owner_array[$key]->get_attribute_names(1);
		}
	}
	$result = $steam->buffer_flush();
	foreach ($desktops_array as $key => $item) { 
		if ($is_readable[$key]){
			$attribute_exists_array[$key] = $result[$attribute_exists_array[$key]];
		}
	}
	
} else {
		foreach ($desktops_links_array_ids as $key => $id){
			if ($owner_array_ids[$key]){
				$owner_array[$key] = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $owner_array_ids[$key], CLASS_USER);
				$desktops_links_array[$key] = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $id, CLASS_LINK);
			}
		}
}
//error_log($count.":".print_r($desktops_links_array_ids,1));
	
// Attribut prüfen
	$onlineNew = array();
	$offlineNew = array();
	$is_online_array = array();
	$login_times_array = array();

	foreach ($desktops_links_array_ids as $key => $item) { 
		if ($is_readable[$key] || $owner_array_ids[$key]){
			$login_times_array[$key] = $owner_array[$key]->get_attribute(USER_LAST_LOGIN_LARS, 1);
		}
	}
	$result = $steam->buffer_flush();
	foreach ($desktops_links_array_ids as $key => $item) { 
		if ($is_readable[$key] || $owner_array_ids[$key]){
			$login_times_array[$key] = $result[$login_times_array[$key]];
			$is_online_array[$key] = ($now > $login_times_array[$key] + 60) ? false : true;
//			error_log($owner_array_ids[$key]."-".$now.":::".($login_times_array[$key] + 120).":".$count);
			if ($is_online_array[$key]){
				$onlineNew[] = $desktops_links_array[$key]->get_id();
			} else {
				$offlineNew[] = $desktops_links_array[$key]->get_id();
			}
		}
	}
//	$is_online_array = array();
	if (array_diff($onlineNew, $usersOn) || array_diff($offlineNew, $usersOff)){
		session_name("bidowl_session");
		session_start();
		$_SESSION['ownerArray'] =  $owner_array;
		$_SESSION['desktopsLinksArray'] =  $desktops_links_array;
		$_SESSION['desktops_links_array_ids'] =  $desktops_links_array_ids;
		$_SESSION['owner_array_ids'] = $owner_array_ids;
		$_SESSION['usersOn'] =  $onlineNew;
		$_SESSION['usersOff'] =  $offlineNew;
		session_write_close();
		print json_encode(array(success => true, online => $onlineNew, offline => $offlineNew));
		exit;
	} else {
		$count++;
//		sleep(30);
		session_name("bidowl_session");
		session_start();
		if ($_SESSION['reset_online_status']){
//			error_log("reset online status");
			$_SESSION['reset_online_status'] = false;
			session_write_close();
			getUsersLastLogin($steam, $usersOn = array(), $usersOff = array(), $count, $owner_array_ids = array(), $desktops_links_array_ids);
			exit;
			unset($onlineNew,
				$offlineNew,
				$desktops_links_array,
				$owner_array,
				$owner_array_ids
			);
			$usersOn = array();
			$usersOff = array();
//			$count--;
			$pause = true;
		}
//		session_write_close();		
		if ($count > 4){
//			session_name("bidowl_session");
//			session_start();    
			$_SESSION['ownerArray'] =  $owner_array;
			$_SESSION['desktopsLinksArray'] =  $desktops_links_array;
			$_SESSION['desktops_links_array_ids'] =  $desktops_links_array_ids;
			$_SESSION['owner_array_ids'] = $owner_array_ids;
			$_SESSION['usersOn'] =  $onlineNew;
			$_SESSION['usersOff'] =  $offlineNew;
//			session_write_close();		
		}
		session_write_close();		
		if ($count > 4){exit;}
//		if ($pause){sleep(2);}//because loading the tree may take some time at client	
//		getUsersLastLogin($steam, $usersOn, $usersOff, $count, $owner_array_ids, $desktops_links_array_ids);
	}
}

function getUpdates($steam, $ids, $lastUpdate, $count, $newIds, $current_folder_array_ids = array()){
//	$lastUpdate = $lastUpdate-250;
	$data = array();
	$allIds = json_decode(stripslashes($ids));
	$now = time()-1;
	$lastUpdateTmp = time()-1;

if ($newIds || empty($current_folder_array_ids)){
//	error_log("new ids update: ".$ids);
	$current_folder_array_ids = array();
	foreach ($allIds as $key => $id){
		$current_folder_array[$key] = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), substr($id, 1));
		if ($current_folder_array[$key] instanceof steam_link){
			$current_folder_array[$key] = $current_folder_array[$key]->get_link_object();
		}
		$current_folder_array_ids[] = $current_folder_array[$key]->get_id();
	}
} else { // no server calls
	foreach ($allIds as $key => $id){
		$current_folder_array[$key] = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $current_folder_array_ids[$key], CLASS_CONTAINER);
	}
}
	//Attribute & mehr cachen
	$current_folder_discussion_array = array();
	$inventory_packages_array = array();
	foreach ($allIds as $key => $id){
		if (substr($id, 0, 1) == "d"){
			$current_folder_discussion_array[$key] = $current_folder_array[$key]->get_attribute("FOLDER_DISCUSSION", 1);
		} elseif (substr($id, 0, 1) == "p"){
			$inventory_packages_array[$key] = $current_folder_array[$key]->get_inventory_filtered(
				array(
//					array( '+', 'attribute', 'OBJ_TYPE', 'prefix', 'steam_document' ),
//					array( '+', 'attribute', 'DOC_MIME_TYPE', 'prefix', "text/html" ),
//					array( '-', 'attribute', 'OBJ_LAST_CHANGED', '>', $lastUpdate)
					array( '-', '!access', SANCTION_READ ),
					array( '+', 'attribute', 'DOC_LAST_MODIFIED', '>', $lastUpdate),
					array( '-', 'attribute', 'DOC_LAST_MODIFIED', '>', $now)
				),
				array(),
				0,
				0,
				1
			);
		}
	}
	$result = $steam->buffer_flush();
	$inventory_discussion_array = array();
	foreach ($allIds as $key => $id){
		if (substr($id, 0, 1) == "d" && $result[$current_folder_discussion_array[$key]] instanceof steam_container){
			$current_folder_discussion_array[$key] = $result[$current_folder_discussion_array[$key]];
			$inventory_discussion_array[$key] = $current_folder_discussion_array[$key]->get_inventory_filtered(
				array(
//					array( '+', 'attribute', 'OBJ_TYPE', 'prefix', 'steam_document' ),
					array( '-', '!access', SANCTION_READ ),
					array( '-', 'attribute', 'DOC_MIME_TYPE', '!=', "text/html" ),
					array( '+', 'attribute', 'DOC_LAST_MODIFIED', '>', $lastUpdate),
					array( '-', 'attribute', 'DOC_LAST_MODIFIED', '>', $now)
				),
				array(),
				0,
				0,
				1
			);
		} elseif (substr($id, 0, 1) == "p"){
			$inventory_packages_array[$key] = $result[$inventory_packages_array[$key]];
		}
	}
	$result = $steam->buffer_flush();
	foreach ($allIds as $key => $id){
		if (substr($id, 0, 1) == "d" && $current_folder_discussion_array[$key] instanceof steam_container){
			$inventory_discussion_array[$key] = $result[$inventory_discussion_array[$key]];
		}
	}
	$inventory_items_attributes = array();
	$inventory_items_content = array();
	$inventory_items_creator = array();
	foreach ($allIds as $key => $id){
		if (substr($id, 0, 1) == "d" && $current_folder_discussion_array[$key] instanceof steam_container){
			foreach ($inventory_discussion_array[$key] as $key2 => $item){
				if (!($item instanceof steam_object)){error_log("00026: ".$id);continue;}
//TODO: Error
//[Tue Mar 17 13:39:23 2009] [error] [client 131.234.198.58] lars_login call:adettke, referer: http://www.bid-owl.de/lars/lars2/index.html
//[Tue Mar 17 13:39:43 2009] [error] [client 131.234.198.58] 00025: d497268, referer: http://www.bid-owl.de/lars/lars2/index.html
//[Tue Mar 17 13:39:43 2009] [error] [client 131.234.198.58] 00025: d497268, referer: http://www.bid-owl.de/lars/lars2/index.html
//[Tue Mar 17 13:39:43 2009] [error] [client 131.234.198.58] 00025: d497268, referer: http://www.bid-owl.de/lars/lars2/index.html
//[Tue Mar 17 13:39:43 2009] [error] [client 131.234.198.58] 00025: d497268, referer: http://www.bid-owl.de/lars/lars2/index.html
//[Tue Mar 17 13:39:43 2009] [error] [client 131.234.198.58] 00025: d497268, referer: http://www.bid-owl.de/lars/lars2/index.html

//[Sun Mar 15 14:54:07 2009] [error] [client 80.66.22.86] lars_login call:sknoth, referer: http://www.bid-owl.de/lars/lars2/index.html
//[Tue Mar 17 14:31:39 2009] [error] [client 80.66.22.86] lars_login call:sknoth, referer: http://www.bid-owl.de/lars/lars2/index.html
//[Tue Mar 17 15:38:13 2009] [error] [client 80.66.22.86] lars_login call:uwassmann, referer: http://www.bid-owl.de/lars/lars2/index.html
//[Tue Mar 17 16:04:12 2009] [error] [client 80.66.22.86] lars_login call:aberghaus, referer: http://www.bid-owl.de/lars/lars2/index.html
//[Tue Mar 17 16:16:06 2009] [error] [client 80.66.22.86] lars_login call:lberghaus, referer: http://www.bid-owl.de/lars/lars2/index.html
//[Tue Mar 17 16:51:58 2009] [error] [client 80.66.22.86] lars_login call:aberghaus, referer: http://www.bid-owl.de/lars/lars2/index.html
//[Tue Mar 17 16:52:05 2009] [error] [client 80.66.22.86] PHP Fatal error:  Call to a member function get_attributes() on a non-object in /var/www/lars/lars_update.php on line 338, referer: http://www.bid-owl.de/lars/lars2/index.html
//...
//[Tue Mar 17 17:12:08 2009] [error] [client 80.66.22.86] PHP Fatal error:  Call to a member function get_attributes() on a non-object in /var/www/lars/lars_update.php on line 338, referer: http://www.bid-owl.de/lars/lars2/index.html
//[Tue Mar 17 17:12:19 2009] [error] [client 80.66.22.86] 00025: d481738, referer: http://www.bid-owl.de/lars/lars2/index.html
//[Tue Mar 17 17:12:19 2009] [error] [client 80.66.22.86] 00025: d481738, referer: http://www.bid-owl.de/lars/lars2/index.html
//[Tue Mar 17 17:12:19 2009] [error] [client 80.66.22.86] 00025: d481738, referer: http://www.bid-owl.de/lars/lars2/index.html
//[Tue Mar 17 17:12:19 2009] [error] [client 80.66.22.86] 00025: d481738, referer: http://www.bid-owl.de/lars/lars2/index.html
//[Tue Mar 17 17:12:19 2009] [error] [client 80.66.22.86] 00025: d481738, referer: http://www.bid-owl.de/lars/lars2/index.html
//[Tue Mar 17 17:12:19 2009] [error] [client 80.66.22.86] 00025: d510037, referer: http://www.bid-owl.de/lars/lars2/index.html
//[Tue Mar 17 17:12:19 2009] [error] [client 80.66.22.86] 00025: d510037, referer: http://www.bid-owl.de/lars/lars2/index.html
//[Tue Mar 17 17:12:19 2009] [error] [client 80.66.22.86] 00025: d510037, referer: http://www.bid-owl.de/lars/lars2/index.html
//[Tue Mar 17 17:12:19 2009] [error] [client 80.66.22.86] 00025: d510037, referer: http://www.bid-owl.de/lars/lars2/index.html
//[Tue Mar 17 17:12:19 2009] [error] [client 80.66.22.86] 00025: d510037, referer: http://www.bid-owl.de/lars/lars2/index.html
//[Tue Mar 17 17:12:19 2009] [error] [client 80.66.22.86] 00026: d481738, referer: http://www.bid-owl.de/lars/lars2/index.html
//[Tue Mar 17 17:12:19 2009] [error] [client 80.66.22.86] 00026: d481738, referer: http://www.bid-owl.de/lars/lars2/index.html
//[Tue Mar 17 17:12:19 2009] [error] [client 80.66.22.86] 00026: d481738, referer: http://www.bid-owl.de/lars/lars2/index.html
//[Tue Mar 17 17:12:19 2009] [error] [client 80.66.22.86] 00026: d481738, referer: http://www.bid-owl.de/lars/lars2/index.html
//[Tue Mar 17 17:12:19 2009] [error] [client 80.66.22.86] 00026: d481738, referer: http://www.bid-owl.de/lars/lars2/index.html
//[Tue Mar 17 17:12:19 2009] [error] [client 80.66.22.86] PHP Fatal error:  Call to a member function get_attributes() on a non-object in /var/www/lars/lars_update.php on line 338, referer: http://www.bid-owl.de/lars/lars2/index.html

				$inventory_items_attributes[$key][$key2] = $item->get_attributes(array(DOC_MIME_TYPE, OBJ_NAME, OBJ_DESC, OBJ_CREATION_TIME, OBJ_LAST_CHANGED, DOC_LAST_MODIFIED, CONT_LAST_MODIFIED), 1);
// [Mon Feb 16 11:21:08 2009] [error] [client 80.66.15.17] PHP Fatal error:  Call to a member function get_attributes() on a non-object in /var/www/lars/lars_update.php on line 249
// [Tue Feb 17 22:49:10 2009] [error] [client 78.94.196.129] PHP Fatal error:  Call to a member function get_attributes() on a non-object in /var/www/lars/lars_update.php on line 255, referer: http://www.bid-owl.de/lars/lars2/index2.html

				$inventory_items_content[$key][$key2] = $item->get_content(1);
				$inventory_items_creator[$key][$key2] = $item->get_creator(1);
			}
		}
	}
	$result = $steam->buffer_flush();
	$inventory_items_creator_name = array();
	foreach ($allIds as $key => $id){
		if (substr($id, 0, 1) == "p"){
			foreach ($inventory_packages_array[$key] as $key3 => $item){
				$inventory_items_attributes[$key][$key3] = $item->get_attributes(array("bid:hidden", LARS_HIDDEN, DOC_MIME_TYPE, OBJ_NAME, DOC_EXTERN_URL, OBJ_DESC, OBJ_PATH, OBJ_CREATION_TIME, OBJ_LAST_CHANGED, LARS_STATE, LARS_COMMENT, LARS_TYPE), 1);
// massiv viele Fehler [Tue Mar 17 17:12:29 2009] [error] [client 80.66.22.86] PHP Fatal error:  Call to a member function get_attributes() on a non-object in /var/www/lars/lars_update.php on line 338, referer: http://www.bid-owl.de/lars/lars2/index.html		if (substr($id, 0, 1) == "d" && $current_folder_discussion_array[$key] instanceof steam_container){
				$inventory_items_content[$key][$key3] = $item->get_content(1);
				$inventory_items_creator[$key][$key3] = $item->get_creator(1);
			}
		}
		if (substr($id, 0, 1) == "d" && $current_folder_discussion_array[$key] instanceof steam_container){
			foreach ($inventory_discussion_array[$key] as $key2 => $item){
				if (!($item instanceof steam_object)){error_log("00027: ".$id);continue;}
//[Tue Mar 17 13:39:43 2009] [error] [client 131.234.198.58] 00026: d497268, referer: http://www.bid-owl.de/lars/lars2/index.html
//[Tue Mar 17 13:39:43 2009] [error] [client 131.234.198.58] 00026: d497268, referer: http://www.bid-owl.de/lars/lars2/index.html
//[Tue Mar 17 13:39:43 2009] [error] [client 131.234.198.58] 00026: d497268, referer: http://www.bid-owl.de/lars/lars2/index.html

				$inventory_items_attributes[$key][$key2] = $result[$inventory_items_attributes[$key][$key2]];
				$inventory_items_content[$key][$key2] = $result[$inventory_items_content[$key][$key2]];
				$inventory_items_creator[$key][$key2] = $result[$inventory_items_creator[$key][$key2]];;
				$inventory_items_creator_name[$key][$key2] = $inventory_items_creator[$key][$key2]->get_name(1);
//[Wed Feb 18 13:47:39 2009] [error] [client 80.66.15.17] PHP Fatal error:  Call to a member function get_name() on a non-object in /var/www/lars/lars_update.php on line 338, referer: http://www.bid-owl.de/lars/lars2/index.html
			}
		}
	}
	$result = $steam->buffer_flush();
	foreach ($allIds as $key => $id){
		if (substr($id, 0, 1) == "p"){
			foreach ($inventory_packages_array[$key] as $key3 => $item){
				$inventory_items_attributes[$key][$key3] = $result[$inventory_items_attributes[$key][$key3]];
				$inventory_items_content[$key][$key3] = $result[$inventory_items_content[$key][$key3]];
				$inventory_items_creator[$key][$key3] = $result[$inventory_items_creator[$key][$key3]];
			}
		}
		if (substr($id, 0, 1) == "d" && $current_folder_discussion_array[$key] instanceof steam_container){
			foreach ($inventory_discussion_array[$key] as $key2 => $item){
				$inventory_items_creator_name[$key][$key2] = $result[$inventory_items_creator_name[$key][$key2]];
			}
		}
	}
	
	foreach ($allIds as $key => $id){
		
		/*
		 * Discussion
		 */
		if (substr($id, 0, 1) == "d"){

			if (!($current_folder_discussion_array[$key] instanceof steam_container)) continue;
			$items = array();
			foreach ($inventory_discussion_array[$key] as $key2 => $item){
				$attributes = $inventory_items_attributes[$key][$key2];
				$attributes["OBJ_ID"] = $item->get_id();
//[Tue Mar 17 13:39:43 2009] [error] [client 131.234.198.58] PHP Fatal error:  Call to a member function get_id() on a non-object in /var/www/lars/lars_update.php on line 381, referer: http://www.bid-owl.de/lars/lars2/index.html
				$attributes["OBJ_AUTHOR"] = $inventory_items_creator_name[$key][$key2];
				$attributes["LARS_CONTENT"] = '<div class="dflt">'._get_texthtmlnew($config_webserver_ip, stripslashes($inventory_items_content[$key][$key2]), $item).'</div>';
				$items[] = array(
					id=>$attributes["OBJ_ID"],
					OBJ_NAME=>$attributes["OBJ_NAME"],
					OBJ_DESC=>$attributes["OBJ_DESC"], 
					OBJ_AUTHOR=>$attributes["OBJ_AUTHOR"], 
					DOC_LAST_MODIFIED=>$attributes["DOC_LAST_MODIFIED"], 
					LARS_CONTENT=>$attributes["LARS_CONTENT"], 
					container=>$id
					);
		} 
		}
		/*
		 * Package
		 */	
		elseif (substr($id, 0, 1) == "p"){
		 $items = array();
		 foreach ($inventory_packages_array[$key] as $key3 => $item){
			if ($item instanceof steam_document || $item instanceof steam_docextern){
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
				$attributes = $inventory_items_attributes[$key][$key3];
				if ($attributes["LARS_HIDDEN"]){continue;}
				if ($attributes["bid:hidden"] && !$showHidden){continue;}
				$attributes["OBJ_ID"] = $item->get_id();
				if ($attributes["LARS_COMMENT"]){
					$action1 = 'comments';
					$qtip1 = $attributes["LARS_COMMENT"];
				} else {
					$action1 = 'comment-edit';
					$qtip1 = 'Schreibe eine Anmerkung zu diesem Dokument';
				}
					
				if (!($item instanceof steam_docextern)){
				switch($attributes["DOC_MIME_TYPE"]){
				    case "text/html":
				    case "text/plain":
				    $content = $inventory_items_content[$key][$key3];
						$content = strip_tags(_get_texthtmlnew($config_webserver_ip, stripslashes($content), $item));
						$mimeType = "Text";
						$qtip0 = 'Dokument hier anzeigen';
				    	$action2 = 'editPage';
						$qtip2 = 'Dieses Dokument bearbeiten';
						$action4 = 'delete';
						$qtip4 = 'Dieses Dokument löschen';
						$action5 = 'tab-go';
						$qtip5 = 'In einem neuen Tab öffnen';
						break;
					case "image/x-ms-bmp":
					case "image/gif":
					case "image/jpg":
					case "image/jpeg":
					case "image/png":
						$content = '<p style="text-align: center;"><img src="'.$config_webserver_ip.'/tools/get.php?mode=thumbnail&height=100&object='.$attributes["OBJ_ID"].'" border="0" /></p>'; 
						$qtip0 = 'Dokument hier anzeigen';
						$mimeType = 'Bild';
						$action2 = 'page-save';
						$qtip2 = 'Dieses Bild in voller Größe laden';
						$action3 = 'cut';
						$qtip3 = "Den Pfad des Bildes in die Zwischenablage kopieren um in ein Dokument einzufügen";
						$action4 = 'delete';
						$qtip4 = 'Dieses Dokument löschen';
						$action5 = 'tab-go';
						$qtip5 = 'In voller Größe in neuem Tab öffnen';
						break;
					default:
						$content = '<a href="'.$config_webserver_ip.'/tools/get.php?object='.$attributes["OBJ_ID"].'" title="'.$attributes["OBJ_NAME"].'">'.$attributes["OBJ_NAME"].'</a>';
//						$content = "<i>".$attributes["OBJ_NAME"]."</i> : ".$attributes["OBJ_DESC"];
						$mimeType = "Download";
						$action2 = 'page-save';
						$hide2 = 1;						
						$action3 = 'add-page';
						$qtip3 = 'Eine Lösung zu diesem Dokument hochladen';
						$action4 = 'delete';
						$qtip4 = 'Dieses Dokument löschen';
						break;
				    }//end switch
				} else {
					$content = $attributes["DOC_EXTERN_URL"]; 
//					$content = '<p style="text-align: center;"><a href="'.$attributes["DOC_EXTERN_URL"].'">'.$attributes["DOC_EXTERN_URL"].'</a></p>'; 
					$qtip0 = "Diesen Link in einem neuen Fenster öffnen";
					$mimeType = "Link";
					$hide2 = 1;
					$action4 = 'delete';
					$qtip4 = 'Diesen Link löschen';
				}//end if !docextern
			
			$name_parts = pathinfo($attributes["OBJ_PATH"]);
			$name_parts["extension"] = ($mimeType == "Link") ? "link" : $name_parts["extension"];
			
			$items[] = array(
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
					hide5=>$qtip5 ? 0 : 1,
					container=>$id
					);
		}
		
		}
				
		}
		if (count($items)>0){
			$data[] = array(
				id => $id,
				items => $items
				);
		}
	}

	
	if (empty($data) && $count <10){
		$count++;
		sleep(10);
		$idsOld = $ids;
		session_name("bidowl_session");
		session_start();
		$ids = ($_SESSION['updateIds']) ? ($_SESSION['updateIds']) : $ids;
		$_SESSION['current_folder_array_ids'] = $current_folder_array_ids;
		session_write_close();
		$newIds = false;
		if (strcasecmp($idsOld, $ids) != 0) {$newIds = true;}
		getUpdates($steam, $ids, $lastUpdateTmp, $count, $newIds, $current_folder_array_ids);
	}else {
		session_name("bidowl_session");
		session_start();
		$_SESSION['lastUpdate'] = $lastUpdateTmp;
		$_SESSION['oldIds'] = $ids;
		$_SESSION['current_folder_array_ids'] = $current_folder_array_ids;
		session_write_close();	
		$output = array();
//			"success" => true,
//			"data" => $data
//		); 
//		$data_all = array();
//		$data_all[] = $data;
		$output[success] = true;
		$output[data] = $data;
		$output = json_encode($output);
		echo $output;
		return;
		}
}
 
?>
