<?php

  /****************************************************************************
  session_data.php - do the login, get all data, store them in session
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

session_name("bidowl_session");
session_start();

// logout
if( isset($_POST["logout"]) && $_POST["logout"] ){
	session_destroy();
	header("Location: $config_webserver_ip/index.html");
    exit();
}
// login
else if( isset($_POST["login"]) && $_POST["login"] ){
	if( isset($_SESSION["performe_login"]) && $_SESSION["performe_login"] == "do" && strlen($_SERVER["PHP_AUTH_USER"]) > 0 ){
		$_SESSION["performe_login"] = "done";
		$_SESSION["login_name"] = $_SERVER["PHP_AUTH_USER"];
 		$_SESSION["login_pwd"] = $_SERVER["PHP_AUTH_PW"];
	}
	else{
		$_SESSION["performe_login"] = "do";
		Header( "WWW-Authenticate: Basic realm=\"bid-owl\"" );
		Header( "HTTP/1.0 401 Unauthorized" );

		die("<html>\n" .
       		"<head>\n" .
       		"  <meta http-equiv=\"refresh\" content=\"1\"; URL=\"$config_webserver_ip/index.html\">\n" .
       		"</head>\n" .
       		"<body>\n" .
       		"<script type=\"text/javascript\">\n" .
       		"  location.href = \"$config_webserver_ip/index.html\"\n" .
       		"</script>\n" .
       		"</body>\n" .
       		"</html>");
	}
}

$login_name = isset($_SESSION["login_name"])?$_SESSION["login_name"]:"guest";
$login_pwd = isset($_SESSION["login_pwd"])?$_SESSION["login_pwd"]:"guest";

###
# user is loged in or not

//Current Object
if(isset($_GET["object"]) && $_GET["object"] != "0")
  $object = $_GET["object"];
else if(isset($_SESSION["object"]) && $_SESSION["object"] != "0")
  $object = $_SESSION["object"];
else
  $object = "0";

$_SESSION["object"] = $object;


// Language Settings
if(isset($_GET["language"]) && is_dir("./templates/" . $_GET["language"]))
  $language = $_GET["language"];
else if(isset($_SESSION["language"]) && is_dir("./templates/" . $_SESSION["language"]))
  $language = $_SESSION["language"];
else
  $language = "ge";

$_SESSION["language"] = $language;

//Treeview Status
if(isset($_GET["treeview"]))
  $treeview = ($_GET["treeview"] == "on")?true:false;
else if(isset($_SESSION["treeview"]))
  $treeview = $_SESSION["treeview"];

if(isset($treeview))
  $_SESSION["treeview"] = $treeview;


//Treeview Mininmal Display
if(isset($_GET["treeview_mini"]))
  $treeview_mini = ($_GET["treeview_mini"] == "on")?true:false;
else if(isset($_SESSION["treeview_mini"]))
  $treeview_mini = $_SESSION["treeview_mini"];

if(isset($treeview_mini))
  $_SESSION["treeview_mini"] = $treeview_mini;


//Show Hidden Status
if(isset($_GET["show_hidden"]))
  $show_hidden = ($_GET["show_hidden"] == "on")?true:false;
else if(isset($_SESSION["show_hidden"]))
  $show_hidden = $_SESSION["show_hidden"];

if(isset($show_hidden))
  $_SESSION["show_hidden"] = $show_hidden;

?>