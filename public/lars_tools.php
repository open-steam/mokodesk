<?php
require_once("mokodesk_steam.php");

/**
  * Get html text content
  * @access   private
  **/
  function _get_texthtmlnew($config_webserver_ip, $content, $object)
  {
    // get current path
    $current_path = substr( $object->get_path(), 0, strrpos($object->get_path(), "/")) . "/";
//echo $object->get_path();
    $content = preg_replace('/link href="(?!http)([a-z0-9.-_\/]*)"/iU', 'link href="' . $config_webserver_ip . '/tools/get.php?object=' . $current_path . '$1"', $content);
    $content = preg_replace('/src="\/(?!mokodesk\/tiny_mce)([a-z0-9. \-\%_\/]+)"/iU', 'src="' . $config_webserver_ip . '/tools/get.php?object=$1"', $content);
    $content = preg_replace('/src="(?!\/|http)([a-z0-9.\- _\/]+)"/iU', 'src="' . $config_webserver_ip . '/tools/get.php?object=' . $current_path . '$1"', $content);
//http://de3.php.net/manual/en/regexp.reference.assertions.php
    $content = preg_replace('/code="([a-z0-9.\-_\/]*)"/iU', 'src="' . $config_webserver_ip . '/tools/get.php?object=' . $current_path . '$1"', $content);
    $addition_math = "";
    $addition_anno = "";
	if (strpos($content, '<span class="AM') || strpos($content, 'image/svg+xml')){
		$addition_math = '<script type="text/javascript" src="' . $config_webserver_ip . '/moko/tiny_mce/plugins/asciimath/js/ASCIIMathMLwFallbackMin.js"></script>
		                  <script type="text/javascript" src="' . $config_webserver_ip . '/moko/tiny_mce/plugins/asciisvg/js/ASCIIsvgPIMin.js"></script>
		                  <script type="text/javascript">
		                  var AScgiloc = "tools/asciisvg/svgimg.php";
		                  var AMTcgiloc = "cgi-bin/mimetex.cgi";
		                  </script>';
	}
    if (strpos($content, '<acronym')) {
        $addition_anno = '<link href="/moko/tiny_mce/plugins/bid_tooltip/css/content.css" type="text/css" rel="stylesheet">';
    }

    $content =  '<head>' . $addition_math . $addition_anno . '</head> ' . $content;

	//TODO Server URI
	// TODO d.svg dynamisch Adresse erzeugen!
//		var AScgiloc = "http://www.imathas.com/imathas/filter/graph/svgimg.php";
//		var AMTcgiloc = "http://www.imathas.com/cgi-bin/mimetex.cgi";
    // recode to UTF-8 if necessary
//    if (mb_detect_encoding($content, 'UTF-8, ISO-8859-1') !== 'UTF-8')
//      $content = utf8_encode($content);
    if (mb_detect_encoding($content, 'UTF-8, ISO-8859-1') !== 'UTF-8') $content = utf8_encode($content);
//    return '<div class="dflt">'.$content.'</div>';
    return $content;
//	$steam->disconnect();
  }

function tidyDesc($desc){
	$allowed = "/[^a-zÖÄÜöäüß0-9\\040\\.\\-\\_\\(\\)\\!\\,\\;\\?\\:]/i";
    if (mb_detect_encoding($desc, 'UTF-8, ISO-8859-1') !== 'UTF-8') $desc = utf8_encode($desc);
	$desc = preg_replace($allowed,"",$desc);
	return $desc;
}
function tidyName($name){
	$search =  Array(' ', 'ß', 'ö', 'ä', 'ü', 'Ö', 'Ä', 'Ü', '&');
	$replace = Array('_', 'ss','oe','ae','ue','Oe','Ae','Ue', 'und');
	$name = str_replace($search, $replace, $name);
	$allowed = "/[^a-z0-9\\040\\.\\-\\_]/i";
	$name = preg_replace($allowed,"",$name);
	return $name;
}
?>
