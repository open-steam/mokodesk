<?php

  /****************************************************************************
  get.php - script to download object content from steam server
  Copyright (C)

  This program is free software; you can redistribute it and/or modify it
  under the terms of the GNU General Public License as published by the
  Free Software Foundation; either version 2 of the License,
  or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
  See the GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software Foundation,
  Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

  Author: Henrik Beige <hebeige@gmx.de>
  		  Bastian Schröder <bastian@upb.de>

  ****************************************************************************/
require_once("../mokodesk_steam.php");
  require_once("$configDocRoot/public/lars_tools.php");
  require_once("$configDocRoot/public/includes/mimetype_map.php");
  require_once("$configDocRoot/public/includes/derive_mimetype.php");

  $object = (int) (isset($_GET["object"]))?trim($_GET["object"]):0;
session_name("bidowl_session");
session_start();    
	$login_name = ($_SESSION['user']) ? ($_SESSION['user']) : null;
	$login_pwd = ($_SESSION['pass']) ? ($_SESSION['pass']) : null;
session_write_close();	  
	$steam = mokodesk_steam::connect(	$configServerIp,
                  $configServerPort,
                  $login_name,
                  $login_pwd);
                  
  if( !$steam || !$steam->get_login_status() )
  {
//    header("Location: $config_webserver_ip/index.html");
    exit();
  }

  //if ID has been properly specified => download and output
  if( $object !== 0 && $object !== "")
  {
    if ($object - 0 == 0) {
	  $path = $object;
      $object = steam_factory::path_to_object($GLOBALS["STEAM"]->get_id(), $object);
	  if ($object instanceof steam_object){
	  	$object = ($object->get_id());
	  } else {
	  	error_log("no object for path ".$path);
		exit;
	  }
    }

    $object = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $object );

    // store object data in array below
    // (makes future usage of disk cache mechanisms easier too)
    $data = array();
    // changed condition to either height or width is set
    if (isset($_GET["mode"]) && $_GET["mode"] == "thumbnail" && (isset($_GET["width"]) || isset($_GET["height"]))) {
      $width     = (int)$_GET["width"];
      $height    = (int)$_GET["height"];
      $object->get_attributes( array(
          "OBJ_NAME",
          "DOC_MIME_TYPE",
          "DOC_LAST_MODIFIED"
        ), TRUE );
      $tnr_imagecontent = $object->get_thumbnail_data($width, $height, 0, TRUE);
      $result = $steam->buffer_flush();
      $data["mimetype"]    = $result[$tnr_imagecontent]["mimetype"];
      $data["lastmodified"]= $result[$tnr_imagecontent]["timestamp"];
      $data["name"]        = $result[$tnr_imagecontent]["name"];
      $data["content"]     = $result[$tnr_imagecontent]["content"];
      $data["contentsize"] = $result[$tnr_imagecontent]["contentsize"];
      // For debug issues:
      //error_log("get.php: thumbnail name=" . $data["name"] . " width=". $width . " height=" . $height);
    }
    else {
      //get object attribs
      $data["name"] = $object->get_attribute(OBJ_NAME);
    
      //derive mimetype
//      $data["mimetype"] = derive_mimetype($data["name"]);
      $data["mimetype"] = $object->get_attribute(DOC_MIME_TYPE);
      
      //get content
      $filecontent = $object->get_content();
      if ( empty($filecontent) ) {
        echo "Das Dokument " . $data["name"] . " hat keinen Inhalt";
//        header("WWW-Authenticate: Basic realm=\"Test Authentication System\"");
//        header("HTTP/1.0 401 Unauthorized");
//        exit();
      }
      $data["content"] = $filecontent;
      $data["contentsize"] = strlen($filecontent);
    }
    header('Cache-Control: private');
    header('Cache-Control: must-revalidate');
    //header("Accept-Ranges: bytes");
    header("Content-Type: " . $data["mimetype"]);
    header("Content-Length: " . $data["contentsize"]);
    header("Pragma: public");
    header('Connection: close');
    
    header("Content-Disposition: inline; filename=" . $data["name"]);
//    echo $data["content"];
    if ($data["mimetype"] == "text/html"){
    	echo(_get_texthtmlnew($config_webserver_ip, $data["content"], $object));
    } else {
		echo($data["content"]);
    }

  }
  else
  {
    echo("Download nicht m&ouml;glich. ID wurde nicht korrekt &uumlbergeben.<br>");
    exit();
  }

  //Logout & Disconnect
  $steam->disconnect();

?>
