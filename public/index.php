<?php
require_once("mokodesk_steam.php");
$form = <<<E
		<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
		<html>
		<head>
		 <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
		  <title>MokoDesk</title>
		</head>
		<body scroll="no">
        <style type="text/css">
         form {
			 position:absolute;
			 top:5em;
			 left:50%;
			 margin-left: -13em;
		 }
         body {
			 background: #F89801;
			 color: #000;
			 font: 0.9em "Helvetica";
         }
         fieldset {
			background: #D2E0F2;
			box-shadow: 0 0 10px #041E29;
			border-radius: 20px;
			border: 0px solid #98C0F4;
			background:  -moz-linear-gradient(top, #e0e6ef, #c5d3e6);
			background:  -webkit-gradient(linear, left top, left bottom, from(#e0e6ef), to(#c5d3e6));
			padding: 1em;
			width: 23em;
			margin: 1em;
         }
         legend {
			padding: 0;
			background: #D2E0F2;
			background:  -moz-linear-gradient(top, #e0e6ef, #c5d3e6);
			background:  -webkit-gradient(linear, left top, left bottom, from(#e0e6ef), to(#c5d3e6));
			border: 0px solid #000;
			box-shadow: 0 0 2px #041E29;
			width: 15em;
			margin: 1em;
			position: absolute;
			border-radius: 10px;
			margin: -30px 0px 0px 110px;
         }
         label {
			 margin-right: 1.5em;
			 float: left;
			 width: 5em;
			 padding-top: 0.3em;
			 text-align: left;
         }
         input, select {
			 display: block;
			 font-size: 1em;
			 margin-bottom: 0em;
			 border: 1px solid #000;
			 padding: 0.1em;
			 width: 15em;
			 border-radius: 7px;
			 box-shadow: 0 0 5px #999;
         }
         #mokodesk {
			 position:absolute;
			 top:100%;
			 margin-left: -100px;		 
			 margin-top: -80px;		 
		 }
         #julia {
			 position:absolute;
			 top:19em;
			 margin-left: -300px;		 
		 }
         #lars {
			 position:absolute;
			 top:21em;
			 margin-left: 0px;		 
		 }
         #juletext {
			 position:absolute;
			 top:36em;
			 right: 57%;
		 }
         #larstext {
			 position:absolute;
			 top:36em;
			 left: 60%;
		 }
         #version {
			 position:absolute;
			 bottom:0%;
		 }
		 .input submit {
			background:  -moz-linear-gradient(top, #e0e6ef, #c5d3e6);
			background:  -webkit-gradient(linear, left top, left bottom, from(#e0e6ef), to(#c5d3e6));
		 }
        </style>
        <div align="center">
		<form action="" method="post">
		<fieldset>
		<legend align="right">Login zum MokoDesk</legend>
		<label>Nutzername:</label>
		<input type="text" name="user"/><br />
		<label>Passwort:</label>
		<input type="password" name="pass"/><br/>
		<label>Sprache:</label>
		<select name="lang">
		
E;
$options = array(
			"Wie beim letzten Mal" => "last",
			"LARS" => "de_lars",
			"MokoDesk (Deutsch)" => "de",
			"MokoDesk (Englisch)" => "en",
			"MokoDesk (Französisch)" => "fr",
//			"debug" => "debug",
			);
foreach($options as $key => $value)
{
	$form .= '<option value="'. $value .'">'. $key . '</option>'."\n";
}
$title = "Bildungsnetz Förderung : Individuell";
$form .= '	</select>
			<br />
			<input type="submit" value=" Login "/>
			</fieldset>
			</form>
			<p id="version"><small>'.$current_version.'</small></p>
			<h1 id="header">'.$title.'</h1>
			<a href="http://www.bfiev.de/unsere-projekte/jule-internetschule/">
				<img title="Weitere Informationen zur JuLe Internetschule" id="julia" src="moko/img/julia1.png">
				<p id="juletext">Weitere Informationen...</p>
			</a>
			<a href="http://www.bfiev.de/unsere-projekte/lars-lernen-auf-reisen-schule/">
				<img title="Weitere Informationen zu LARS - Lernen auf Reisen-Schule" id="lars" src="moko/img/lars.png">
				<p id="larstext">Weitere Informationen...</p>
			</a>
			<img id="mokodesk" src="moko/img/MokoDesk_shadow.png">
			</div></body></html>';

if($_SERVER["REQUEST_METHOD"] == "POST"){
  	$loginName = isset($_POST['user']) ? ($_POST['user']) : null;
    $loginPwd = isset($_POST['pass']) ? ($_POST['pass']) : null;
}
session_name("bidowl_session");
session_start();
if(isset($_SESSION["login_name"])){
    $loginName = isset($_SESSION["login_name"]) ?  $_SESSION['login_name'] : $loginName;
    $loginPwd = isset($_SESSION["login_pwd"]) ? $_SESSION['login_pwd'] : $loginPwd;
}
session_write_close();	

if($_SERVER["REQUEST_METHOD"] == "POST" or isset($_SESSION["login_name"])){
//    $loginName = isset($_SESSION["login_name"]) ?  $_SESSION['login_name'] : $loginName;
//    $loginPwd = isset($_SESSION["login_pwd"]) ? $_SESSION['login_pwd'] : $loginPwd;
	$host  = $_SERVER['HTTP_HOST'];
	$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
/*
	if (preg_match("/mokodesken/", $uri)){
		$included_js = "LarsSchreibtischMinEn.js";
	} elseif (preg_match("/mokodesk/i", $uri)){
		$included_js = "LarsSchreibtischMin_formal.js";
	} elseif (preg_match("/larsen\//", $uri)){
		$included_js = "LarsSchreibtischMinEn_colloquial.js";
	} elseif (preg_match("/lars\//", $uri)){
		$included_js = "LarsSchreibtischMin_colloquial.js";
	}
*/
	$steam = mokodesk_steam::connect(	$configServerIp,
								$configServerPort,
								$loginName,
								$loginPwd);
	if (!$steam || !$steam->get_login_status() ){
    	print $form;
		exit;
	}
	session_name("bidowl_session");
	session_start();
	$_SESSION['user'] = $loginName;
	$_SESSION['pass'] = $loginPwd;
	$_SESSION["login_name"] = $loginName;
	$_SESSION["login_pwd"] = $loginPwd;
	$language_selected = $_POST['lang'];
	$steam_user = $steam->get_current_steam_user();
	if ( $language_selected == "last" or !isset($_POST['lang'])){
		$attributes = $steam_user->get_attribute_names();
		if ( in_array( "LARS_LANGUAGE", $attributes ) )
			$language_selected = $steam_user->get_attribute("LARS_LANGUAGE");
		else {
			$language_selected = "de";
		}
	}
	$_SESSION['language'] = substr($language_selected, 0, 2);
	session_write_close();	
	$included_js = array(
				"last" => "",
				"de" => "LarsSchreibtischMin_formal.js",
				"de_lars" => "LarsSchreibtischMin_colloquial.js",
				"fr" => "LarsSchreibtischMinFr.js",
				"en" => "LarsSchreibtischMinEn_formal.js",
				"en_lars" => "LarsSchreibtischMinEn.js",
				"debug" => "LarsSchreibtisch.js",
				);
	$steam_user->set_attribute( "LARS_LANGUAGE", $language_selected );
	print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
		<html>
		<head>
		 <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
		  <title>MokoDesk</title>
			<link rel="stylesheet" type="text/css" href="ext2/resources/css/ext-allMin.css" />
			<link rel="stylesheet" type="text/css" href="moko/css/LarsSchreibtischCssMin.css" />
		</head>
		<body scroll="no" id="docs">
		<div id="loading-mask" style=""></div>
		  <div id="loading">
			<div class="loading-indicator"><style="margin-right:8px;" align="absmiddle">Anwendung wird geladen...</style></div>
		  </div>
			<script type="text/javascript" src="moko/tiny_mce/tiny_mce.js"></script>  
			<script type="text/javascript" src="moko/'.$included_js[$language_selected].'"></script>
		</body>
		</html>';
	exit;
} else {
	print $form;
}
?>
