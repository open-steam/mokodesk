<?php
////////////////////////////////////////////////////////////////////////
/*

Class for displaying variable info, taking time and dumping files

This class will display variable content, stop the time and display files
easier than the internal php functions. Output can easily be modified.


For the lastest version go to:
http://www.phpclasses.org/browse.html/package/891.html


////////////////////////////////////////////////////////////////////////
    CONSTRUCTOR:
        function debugHelper ($outputType = HTML)

    PUBLIC FUNCTIONS:
         function dump ($thing, $options = null)
         function trace($options = null) {
         function message($string = 'MESSAGE !!', $options = null)
         function switchForType ($thing, $functions, $paramArray = null)
         function getOptions($type, $additionalOptions = null)
         function highlightFile($file, $from = 1, $count = EOF)
         function startTimer ($id = 'default')
         function stopTimer ($id = 'default')
         function getTime ($id = 'default')
         function toTable ($array, $header = null, $horizontal = true, $tagAddon = 'border=1')

////////////////////////////////////////////////////////////////

    This library is free software; you can redistribute it and/or
    modify it under the terms of the GNU Lesser General Public
    License as published by the Free Software Foundation; either
    version 2.1 of the License, or (at your option) any later version.

    This library is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
    Lesser General Public License for more details.

    You should have received a copy of the GNU Lesser General Public
    License along with this library; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/
////////////////////////////////////////////////////////////////////////
/*
* @const  HTML            type for constructor
*/
define ('HTML', 1);
/*
* @const  PLAIN_TEXT      type for constructor
*/
define ('PLAIN_TEXT', 2);
/*
* @const  USER_DEFINED    type for constructor
*/
define ('USER_DEFINED', 4);
/*
* @const  EOF              end tag for line count
*/
define ('EOF', -1);
////////////////////////////////////////////////////////////////////////

/**
* Class for displaying var info and taking time
*
* @author	    Lennart Groetzbach <lennartg@web.de>
* @copyright	Lennart Groetzbach <lennartg@web.de> - distributed under the LGPL
* @version 	    0.8 - 2004/04/11
* @package      debughelper
* @access   public
*/class debugHelper {

////////////////////////////////////////////////////////////////////////
/**
* options for html
*
* @access   private
* @var      Array
*/
var $_optionsHtml = array(
        "type"                  => HTML,
        "spacer"                => "&nbsp;&nbsp;&nbsp;&nbsp;",
        "lf"                    => "<br />\n",
        "structOpen"            => "<strong>",
        "structClose"           => "</strong>",
        "specialOpen"           => "<em>",
        "specialClose"          => "</em>",
        "dumpOpen"             => "<div class=\"dbgDump\">",
        "dumpClose"             => "</div>",
        "errorOpen"             => "<div class=\"dbgError\">",
        "errorClose"             => "</div>",
        "addAnchors"            => true,
        "useErrorHandler"       => true,
        "echoResult"            => true,
        "returnResult"          => false,
        "convertHtml"           => true,
        "convertSpecial"        => true,
        "dumpObjectFunctions"   => true,
        "dumpObjectVars"        => true,
        "dumpObjectInheritance" => true,
        "showVarOnError"        => true,
        "showSourceOnError"     => true
   );
/**
*  options for plain text
*
* @access   private
* @var      Array
*/

var $_optionsPlain = array(
        "type"                  => PLAIN_TEXT,
        "spacer"                => "    ",
        "lf"                    => "\n",
        "structOpen"            => "",
        "structClose"           => "",
        "specialOpen"           => "",
        "specialClose"          => "",
        "dumpOpen"             =>  "",
        "dumpClose"             => "",
        "errorOpen"             =>  "",
        "errorClose"             => "",
        "addAnchors"            => false,
        "useErrorHandler"       => true,
        "echoResult"            => true,
        "returnResult"          => true,
        "convertHtml"           => true,
        "convertSpecial"        => true,
        "dumpObjectFunctions"   => true,
        "dumpObjectVars"        => true,
        "dumpObjectInheritance" => true,
        "showSourceOnError"     => false,
        "showVarOnError"        => true
    );

/**
* the option set, the debug class was created with
*
* @access   public
* @var      Array
*/
var $options = null;

/**
* start time array
*
* @access   private
* @var      String
*/
var $_start;

/**
* stop time array
*
* @access   private
* @var      String
*/var $_stop;

/**
* dump depth
*
* @access   private
* @var      Integer
*/
var $_depth = 0;

var $_count = 0;

////////////////////////////////////////////////////////////////////////
/**
* Constructor
*
* Defines which type of output is wanted. You can add your own options in an array
*
* @access   public

*
* @param    Integer     $outputType
* @param    Array       $options
*
* array(
*        "type"                  => one of the constants
*        "spacer"                => spacing for one depth level
*        "lf"                    => linefeed character
*        "structOpen"            => opening tags for arrays or objects
*        "structClose"           => closing tags for arrays or objects
*        "specialOpen"           => opening tags for lf, tab, newline
*        "specialClose"          => closing tags for lf, tab, newline
*        "useErrorHandler"       => true, if the internal error handler be called
*        "echoResult"            => true, if the result of dump() should be echoed
*        "returnResult"          => true, if the result of dump() should be returned
*        "convertHtml"           => true, if the special html chars should be converted
*        "convertSpecial"        => true, if the /n, /t should be converted
*        "dumpObjectFunctions"   => true, if the obj. functions should be displayed
*        "dumpObjectVars"        => true, if the obj. vars should be displayed
*        "dumpObjectInheritance" => true, if the obj. inheritance should be displayed
*    );
**/

function debugHelper ($outputType = HTML, $options = null) {
    // set layout for dump()
    switch ($outputType) {
        case HTML:
            $this->options = $this->_optionsHtml;
            break;
        case PLAIN_TEXT :
            $this->options = $this->_optionsPlain;
            break;
        default:
            $this->options = $this->_optionsHtml;
            break;
    }
    // add user options
    if (is_array($options)) {
        $this->options = array_merge($this->options, $options);
    }
    // sets the error handler
    if ($this->options['useErrorHandler'] == true) {
        set_error_handler("_errorHandler");
    }
}


////////////////////////////////////////////////////////////////////////
/**
* Traces the function call back
*
* Will display the filename and line number
*
* @access   public
*
* @param    Integer     $options	current options
*/
function trace($options = null) {
   if ($options == null) {
     $options = $this->options;
   }
   $tmp = debug_backtrace();
   $size = sizeof($tmp);
   $res = '';
   for($i = 0 ; $i < $size; $i++) {
      if ($tmp[$i]['file']) {
         $res .= $options['specialOpen'] . ' [' . basename($tmp[$i]['file']) . ' : ' . $tmp[$i]['line'] . ']' . $options['specialClose'] ;
      }
   }
      return $res;
}

////////////////////////////////////////////////////////////////////////
/**
* Echos a message
*
* Good for checking if a certain part of your source code is accessed
*
* @access   public
*
* @param    mixed       $string		text to de echoed
* @param    Integer     $options	current options
*/


function message($string = 'MESSAGE!', $options = null) {
   if ($options == null) {
     $options = $this->options;
   }
   $res = $string . $this->trace($options);
    if ($options['echoResult'] == true) {
        echo $res;
    }
    if ($options['returnResult'] == true) {
        return $res;
    }
}

////////////////////////////////////////////////////////////////////////
/**
* Dumps content of a variable
*
* Is able to dump all numeric values, Strings, Arrays, Objects and Resource Types
*
* @access   public
*
* @param    mixed       $thing      the variable
* @param    Integer     $column     current depth
*/
function dump($thing, $options = null, $varName = '') {
    $res = '';
    if ($options == null) {
        $options = $this->options;
    }
    if (is_object($thing)) {
        $res = $this->_dumpObject($thing, $options);
    }
    elseif (is_array($thing)) {
        $res = $this->_dumpArray($thing, $options, $varName);
    }
    elseif (is_int($thing)) {
        $res = $this->_dumpInteger($thing, $options);
    }
    elseif (is_float($thing)) {
        $res = $this->_dumpFloat($thing, $options);
    }
    elseif (is_double($thing)) {
        $res = $this->_dumpDouble($thing, $options);
    }
    elseif (is_long($thing)) {
        $res = $this->_dumpLong($thing, $options);
    }
    elseif (is_string($thing)) {
        $res = $this->_dumpString($thing, $options);
    }
    elseif (is_bool($thing)) {
        $res = $this->_dumpBoolean($thing, $options);
    }
    elseif (is_resource($thing)) {
        $res = $this->_dumpResource($thing, $options);
    }
    elseif (is_null($thing)) {
        $res = $this->_dumpNull($thing, $options);
    }
    else {
        $res = $this->_dumpUnknown($thing, $options);
    }
    return $this->_dumpComplete($res, $options, $varName);
}


////////////////////////////////////////////////////////////////////////
/**
* Calls function according to the variable type
*
* Basically works like dump() but can call user defined functions
*
* @access   public
*
* @param    mixed       $thing      the variable
* @param    Array       $functions  function array

* array(
*      "object"    =>  "_dumpObject",
*      "array"     =>  "_dumpArray",
*      "integer"   =>  "_dumpInteger",
*      "float"     =>  "_dumpFloat",
*      "double"    =>  "_dumpDouble",
*      "long"      =>  "_dumpLong",
*      "string"    =>  "_dumpString",
*      "bool"      =>  "_dumpBoolean",
*      "resource"   =>  "_dumpResource",
*      "null"      =>  "_dumpNull",
*      "unknown"   =>  "_dumpUnknown",
*      "i_am_done" =>  "_dumpComplete"
* );
*
* @param    Array       $options  array with params
*/
function switchForType ($thing, $functions, $options = null) {
    $res = '';
    $paramArray = array($thing, $options);
    if (is_object($thing)) {
        $res = $this->_callFunction(@$functions['object'], false, $paramArray);
    }
    elseif (is_array($thing)) {
        $res = $this->_callFunction(@$functions['array'], false, $paramArray);
    }
    elseif (is_int($thing)) {
        $res = $this->_callFunction(@$functions['integer'], false, $paramArray);
    }
    elseif (is_float($thing)) {
        $res = $this->_callFunction(@$functions['float'], false, $paramArray);
    }
    elseif (is_double($thing)) {
        $res = $this->_callFunction(@$functions['double'], false, $paramArray);
    }
    elseif (is_long($thing)) {
        $res = $this->_callFunction(@$functions['long'], false, $paramArray);
    }
    elseif (is_string($thing)) {
        $res = $this->_callFunction(@$functions['string'], false, $paramArray);
    }
    elseif (is_bool($thing)) {
        $res = $this->_callFunction(@$functions['bool'], false, $paramArray);
    }
    elseif (is_resource($thing)) {
        $res = $this->_callFunction(@$functions['resource'], false, $paramArray);
    }
    elseif (is_null($thing)) {
        $res = $this->_callFunction(@$functions['null'], false, $paramArray);
    }
    else {
        $res = $this->_callFunction(@$functions['unknown'], false, $paramArray);
    }
    return $this->_callFunction(@$functions['i_am_done'], false, array ($res, $options));
}


////////////////////////////////////////////////////////////////////////
/**
* Starts timing
*
* A unique id an be given for several timings.
*
* @access   public
*
* @param    String  $id     timer id
*/

function startTimer ($id = 'default') {
    $this->_start[$id] = explode(' ', microtime());
    unset($this->_stop[$id]);
}

////////////////////////////////////////////////////////////////////////
/**
* Stops timing
*
* Stops timing and returns current value.
*
* @access   public
*
* @param    String  $id     timer id
* @return   Integer current time
*/

function stopTimer ($id = 'default') {
    $this->_stop[$id] = explode(' ', microtime());
    return $this->_calculateTime($this->_stop[$id], $id);
}

////////////////////////////////////////////////////////////////////////
/**
* Returns the current time
*
* Returns the current time or 0, if id does not exist.
*
* @access   public
*
* @param    String  $id     timer id
* @return   Integer current time
*/

function getTime ($id = 'default') {
    $time = explode(' ', microtime());
    if (!isset($this->_start[$id])) {
        return 0;
    }
    if (isset($this->_stop[$id])) {
        $time = $this->_stop[$id];
    }
    return $this->_calculateTime($time, $id);

}

////////////////////////////////////////////////////////////////////////
/**
* Highlights a file
*
* Highlights the file source and adds line numbers
*
* @access   public
*
* @param    String      $file
*/
function highlightFile($file, $from = 1, $count = EOF)
{
    $ret = '';
    if ((trim($file) != '') && file_exists($file)) {
        // cache the highlighting info
        ob_start();
        highlight_file($file);
        $data = ob_get_contents();
        ob_end_clean();
        // seperate by lines
        $data_lines = explode('<br />',$data);
        // dump all lines?
        if ($count == -1) {
            $n = count($data_lines);
        } else {
            // calculate the amout of lines to be dumped
            $n = $from + $count - 1;
            if ($n > count($data_lines)) {
                $n = count($data_lines);
            }
        }
            if ($from < 1) {
                $from = 1;
            }
        // show the lines
         $ret .= "<div class=\"dbgSource\">";
        for ($i=$from - 1; $i < $n; $i++) {
            $k = $i + 1;
            $ret .= '<div style="white-space: nowrap;"><span style="width:45px; color:#0000BB;">'.$k .'</span>'. $data_lines[$i] . '</div>';
            if (strlen($data_lines[$i]) > 450) {
                $ret .= '<br />';
            }
        }
        $ret .= "</div>";
        if ($count != EOF) {
            $ret .= '</font></span>';
        }
    }
    return $ret;
}

////////////////////////////////////////////////////////////////////////
/**
* Returns the options for a certain type
*
* Returns the type options plus additonal ones, you provided
*
* @access   public
*
* @param    Integer     $type       the option type
* @param    Integer     $additionalOptions  to override options
*/
function getOptions($type, $additionalOptions = null) {
    $opts = null;
    switch ($type) {
        case PLAIN_TEXT:
            $opts =  $this->_optionsPlain;
            break;
        case HTML:
            $opts =  $this->_optionsHtml;
            break;
        default:
            $opts =  $this->_optionsHtml;
            break;
    }
    if ($additionalOptions != null) {
        return array_merge($opts, $additionalOptions);
    } else {
        return $opts;
    }
}

////////////////////////////////////////////////////////////////////////
/**
* Converts an array to html table data
*
* Returns html data for a given 1 or 2 dimensional array
*
* @access   public
*
* @param    Array   $array      array
* @param    header  $header     optional header
* @param    Boolean $horizontal horizontal or vertical ordering
* @param    String  $tagAddon   additional data for the table tag
*/

function toTable ($array, $header = null, $horizontal = true, $tagAddon = 'border=1') {
    $res = '';
    if (@is_array($array) && @sizeof($array)) {
        $res .= "<table $tagAddon>\n";
		// check if array is 2dim
		if (@is_array($array[0])  && @sizeof($array[0])) {
            if ($horizontal) {
                $res .= $this->_twodimhor($array, $header);
            } else {
                $res .= $this->_twodimver($array, $header);
            }
        } else {
            if ($horizontal) {
                $res .= $this->_onedimhor($array, $header);
            } else {
                $res .= $this->_onedimver($array, $header);
            }
        }
        $res .= "</table>\n";
    }
    return $res;
}

////////////////////////////////////////////////////////////////////////
/**
* Calculates the time from an timer id
*
* Called by stopTimer() and getTime()
*
* @access   private
*
* @param    Integer $time   end time
* @param    String  $id     timer id for the start time
* @return   Integer amount of time
*/

function _calculateTime($time, $id) {
    $current = $time[1] - $this->_start[$id][1];
    $current += $time[0] - $this->_start[$id][0];
    return $current;
}


////////////////////////////////////////////////////////////////////////
/**
* Dumps a string
*
* Is called by the dump() method.
*
* @access   private
*
* @param    String  $str
* @param    Array       $options    display options
*/
function _dumpString ($str, $options) {
    $res = "string[".strlen($str)."]('";
    if ($options['convertHtml'] == true) {
        // converts to html
        $str = htmlentities($str);
    }
    if ($options['convertSpecial'] != false) {
        // replaces the special chars
        $search = array ("/\n/", "/\t/", "/\f/");
        $replace = array ($options['specialOpen'] . "/n" . $options['specialClose'],  $options['specialOpen'] . "/t" . $options['specialClose'],  $options['specialOpen'] . "/f" . $options['specialClose']);
        $str = preg_replace($search, $replace, $str);
    }
    $res .= $str . "')";
    return $res;
}

////////////////////////////////////////////////////////////////////////
/**
* Dumps an array
*
* Is called by the dump() method.
*
* @access   private
*
* @param    Array       $array      the array
* @param    Array       $options    display options
*/
function _dumpArray ($array, $options, $varName) {
    $this->_depth++;
    $this->_count += 1;
    $openId = $this->_count;
    $str = $options['structOpen']. 'array[' . sizeof($array) . '] ' . ($options['addAnchors']? "<a name=\"open$openId\">" : '') . ' {' . ($options['addAnchors']? "</a>" : '') . $options['structClose'] . ($options['addAnchors']? "<a href=\"#close$openId\">&darr;</a>" : '') . $options['lf'];
    while(list($var, $val) = each($array)) {
        $str .= str_repeat($options['spacer'], $this->_depth);
        $str .= $var . ' => ';

        $str .= $this->dump($val, $options)  . $options['lf'];
    }
    $str .= str_repeat($options['spacer'], $this->_depth - 1);
    $this->_depth--;
    return $str . $options['structOpen'] . ($options['addAnchors']? "<a name=\"close$openId\">" : '') . '} ' . ($options['addAnchors']? "</a>" : '') . $options['structClose'] . ($options['addAnchors']? "<a href=\"#open$openId\">&uarr;</a>" : '');
}

////////////////////////////////////////////////////////////////////////
/**
* Dumps an object
*
* Is called by the dump() method.
*
* @access   private
*
* @param    Object       $object    the object
* @param    Integer     $options    display options
*/


function _dumpObject ($object, $options) {
    $this->_depth++;
    $str = $options['structOpen'] . 'object(' . get_class($object) . ')';
    if ($options['dumpObjectInheritance'] == true) {
        $separator = ' extends ';
        $parent = $this;
        do {
            $temp = get_parent_class($parent);
            $str .= ($temp == '' ? '' : $separator) . $temp;
            $parent = $temp;
        } while ($parent != '');
    }
    $this->_count += 1;
    $openId = $this->_count;
    $str .= ($options['addAnchors']? "<a name=\"open$openId\">" : '') . ' {' . ($options['addAnchors']? "</a>" : '') . $options['structClose'] . ($options['addAnchors']? "<a href=\"#close$openId\">&darr;</a>" : '') . $options['lf'];
    if ($options['dumpObjectVars'] == true) {
        $vars = get_class_vars(get_class($object));
        // sort the varnames alphabetically
        ksort($vars);
        reset($vars);
        // dump all vars
        foreach ($vars as $name => $value) {
            $str .= str_repeat($options['spacer'], $this->_depth);
            $str .= $name.' => ';
            $str .= $this->dump($object->$name, $options, $name) . $options['lf'];
        }
        $str .= $options['lf'];
    }

    /* hab ich rausgenommen
    // dump all methods
    if ($options['dumpObjectFunctions'] == true) {
        $methods = get_class_methods(get_class($object));
        sort($methods);
        reset($methods);
        foreach ($methods as $name => $value) {
            $str .= str_repeat($options['spacer'], $this->_depth);
            $str .= 'function ' . $value. '()'. $options['lf'];
        }
        $str .= str_repeat($options['spacer'], $this->_depth - 1);
    }
    */
    $this->_depth--;
    return $str . $options['structOpen'] . ($options['addAnchors']? "<a name=\"close$openId\">" : '') . '} ' . ($options['addAnchors']? "</a>" : '') . $options['structClose'] . ($options['addAnchors']? "<a href=\"#open$openId\">&uarr;</a>" : '') ;
}

////////////////////////////////////////////////////////////////////////
/**
* Dumps an integer
*
* Is called by the dump() method.
*
* @access   private
*
* @param    Integer     $thing
*/

function _dumpInteger ($thing) {
    return 'int(' . $thing . ')';
}

////////////////////////////////////////////////////////////////////////
/**
* Dumps a float
*
* Is called by the dump() method.
*
* @access   private
*
* @param    Float     $thing
*/

function _dumpFloat ($thing) {
    return 'float(' . $thing . ')';
}

////////////////////////////////////////////////////////////////////////
/**
* Dumps a double
*
* Is called by the dump() method.
*
* @access   private
*
* @param    Double     $thing
*/

function _dumpDouble ($thing) {
    return 'double(' . $thing . ')';
}

////////////////////////////////////////////////////////////////////////
/**
* Dumps a long
*
* Is called by the dump() method.
*
* @access   private
*
* @param    Long     $thing
*/

function _dumpLong ($thing) {
    return 'long(' . $thing . ')';
}

////////////////////////////////////////////////////////////////////////
/**
* Dumps a boolean
*
* Is called by the dump() method.
*
* @access   private
*
* @param    Boolean     $thing
*/

function _dumpBoolean ($thing) {
    return 'bool('. ($thing == false ? 'false' : 'true') .')';
}

////////////////////////////////////////////////////////////////////////
/**
* Dumps an Resource
*
* Is called by the dump() method.
*
* @access   private
*
* @param    Resource     $thing
*/
function _dumpResource($thing) {
    return get_resource_type($thing) . "(" . $thing . ")";
}

////////////////////////////////////////////////////////////////////////
/**
* Dumps null
*
* Is called by the dump() method.
*
* @access   private
*
* @param    mixed     $thing
*/

function _dumpNull ($thing) {
    return '(null)';
}

////////////////////////////////////////////////////////////////////////
/**
* Dumps unknown
*
* Is called by the dump() method.
*
* @access   private
*
* @param    mixed     $thing
*/

function _dumpUnknown ($thing) {
    return 'Unknown('.$thing.')';
}

////////////////////////////////////////////////////////////////////////
/**
* Is called when the dump is complete
*
* Is called by the dump() method.
*
* @access   private
*
* @param    String  $res
* @param    Array   $options
*/

function _dumpComplete($res, $options, $varName) {
    if ($this->_depth == 0) {
        if (!$varName) {
            $this->_internal = true;
            $g = debug_backtrace();
            $index = sizeof($g) - 1;
            $file = file($g[$index]['file']);
            $line = $file[$g[$index]['line'] - 1];
            unset($file);
            $pos = strpos($line, 'dump') + 5;
            $pos1 = strpos($line, '$', $pos);
            if($pos1) {
                $pos2 = strpos($line, ')');
                $varName = trim(substr($line, $pos1, $pos2 - $pos1));
            }
        } else {
         $varName = '$' . $varName;
        }
        $res = $options['dumpOpen'] . $varName . ' = ' . $res;
        $res .= $options['dumpClose'];
        if ($options['echoResult'] == true) {
            echo $res;
        }
        if ($options['returnResult'] == true) {
            return $res;
        }
    } else {
        return $res;
    }
}

////////////////////////////////////////////////////////////////////////
/**
* Calls the function with given parameters
*
* Calls functions inside or outside the class
*
* @access   private
*
* @param    String  $functionName
* @param    Boolean $abortAmbiguous
*/

function _callFunction($functionName, $abortAmbiguous = false) {
        if ($functionName == '') {
            return;
        }
        // # of params
        $params = func_get_arg(2);
        // flags if funcrion exists in a class or outside
        $isInside = method_exists(@$this, $functionName);
        $isOutside = function_exists($functionName);
        // do we need to abort if function name is ambigous?
        if ($abortAmbiguous) {
            if ($isInside && $isOutside) {
                return -1;
            }
        }
        // call the inner method first
        if ($isInside) {
            return call_user_func_array(array($this, $functionName), $params);
        // or the "outer" one
        } else if ($isOutside) {
            return call_user_func_array($functionName, $params);
        // function does not exist at all
        } else if ($functionName) {
            return -1;
        }
}

////////////////////////////////////////////////////////////////

function _twodimhor(&$array, &$headerArray) {
    $res = '';
    if ($headerArray != null) {
        foreach(@$headerArray as $th) {
            $res .= "<th>" . ($th != '' ? $th : "&nbsp;") . "</th>";
        }
    }
    foreach(@$array as $values) {
    $res .= "<tr>\n";
        foreach($values as $key => $value) {
            $res .= "<td>" . ($value != '' ? $value : "&nbsp;") . "</td>";
        }
    $res .= "</tr>\n";
    }
    return $res;
}

////////////////////////////////////////////////////////////////

function _twodimver(&$array, &$headerArray) {
    $res = '';
    $size = sizeof($array);
    $size2 = sizeof($array[0]);
    for ($i = 0; $i < $size2; $i++) {
        $res .= "<tr>\n";
        if ($headerArray != null) {
            $res .= "<th>" . ($headerArray[$i] != '' ? $headerArray[$i] : "&nbsp;") . "</th>";
        }
        for ($j = 0; $j < $size; $j++) {
            $res .= "<td>" . ($array[$j][$i] != '' ? $array[$j][$i] : "&nbsp;") . "</td>";
        }
        $res .= "</tr>\n";
    }
    return $res;
}

////////////////////////////////////////////////////////////////

function _onedimhor(&$array, &$header) {
    $res = '';
    $res .= "<tr>\n";
    if ($header != null) {
        if (@is_array($header)) {
            $header = $header[0];
        }
        $res .= "<th>" . ($header != null ? $header : "&nbsp;") . "</th>";
    }
    foreach($array as $val) {
            $res .= "<td>" . ($val != '' ? $val : "&nbsp;") . "</td>";
    }
    $res .= "\n</tr>\n";
    return $res;
}

////////////////////////////////////////////////////////////////

function _onedimver(&$array, &$header) {
    $res = '';
    if ($header != null) {
        $res .= "<tr>\n";
        $res .= "<th>" . ($header != null ? $header : "&nbsp;") . "</th>";
        if (@is_array($header)) {
            $header = $header[0];
        }
        $res .= "\n</tr>\n";
    }
    foreach($array as $val) {
        $res .= "<tr>\n";
        $res .= "<td>" . ($val != '' ? $val : "&nbsp;") . "</td>";
        $res .= "\n</tr>\n";
    }
    return $res;
}

////////////////////////////////////////////////////////////////////////

}

////////////////////////////////////////////////////////////////////////
// END OF CLASS
////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////
// implementation of is_a
if (!function_exists('is_a'))
{
  function is_a($object, $class_name)
  {
     $class_name = strtolower($class_name);
    if (get_class($object) == $class_name) return TRUE;
     else return is_subclass_of($object, $class_name);
  }
}

////////////////////////////////////////////////////////////////////////
// instanciates a default $debug var

if (!isset($debug)) {
    $debug = new debugHelper(HTML);
}


////////////////////////////////////////////////////////////////////////
// error handler

function _errorHandler ($errno, $errstr, $errfile, $errline) {
    global $debug;
    if (is_a($debug, "debugHelper")) {
        $d =& $debug;
    } else {
        $d = new debugHelper();
    }
    // is a @ before error line?
    if (error_reporting() > 0) {
        $str = $d->options['lf'] . $d->options['errorOpen'];
        $str2 = '';
        switch ($errno) {
            case E_NOTICE :
            case E_USER_NOTICE :
                $str .= $d->options['structOpen'] . "Notice: " . $d->options['structClose'];
                if ($d->options['showVarOnError']) {
                    if (substr($errstr, 0, 9) == 'Undefined') {
                        $pos = strpos($errstr, ':', 10);
                        $type = substr($errstr, 10, $pos - 10); // index - offset - (?)variable - property
                        $name = substr($errstr, $pos + 3, strpos($errstr, ' ', $pos));
                        $file = file($errfile);
                        $line = $file[$errline - 1];
                         unset($file);
                         $fpos = strpos($line, $name);
                         switch ($type) {
                            case 'property':
                               $rpos = strrpos(substr($line, 0, $fpos), '->');
                               $lpos = strrpos(substr($line, 0, $rpos), '$') + 1;
                               $var = substr($line, $lpos, $rpos - $lpos);
                               if ($var) {
                                  $str2 = $d->dump($GLOBALS[$var], $d->getOptions($d->options, array('echoResult' => false, 'returnResult' => true, 'dumpObjectFunctions' => false)), $var);
                               }
                               break;

                               break;
                            case 'index':
                            case 'offset':
                               $rpos = strrpos(substr($line, 0, $fpos), '[');
                               $lpos = strrpos(substr($line, 0, $rpos), '$') + 1;
                               $var = substr($line, $lpos, $rpos - $lpos);
                               if ($var) {
                                  $str2 = $d->dump($GLOBALS[$var], $d->getOptions($d->options, array('echoResult' => false, 'returnResult' => true)), $var);
                               }
                               break;
                         }
                    }
                }
                break;
            case E_USER_WARNING :
            case E_WARNING :
                $str .= $d->options['structOpen'] . "Warning: " . $d->options['structClose'];
                break;
            case E_USER_ERROR :
            case E_ERROR :
                $str .= $d->options['structOpen'] . "Error: " . $d->options['structClose'];
                break;
            default :
                $str .= $d->options['structOpen'] . "Unknown Error: " . $d->options['structClose'];
                break;
        }
        $str .= "$errstr in ". $d->options['structOpen'] . $errfile . $d->options['structClose'] . " on line " . $d->options['structOpen'] . $errline . $d->options['structClose'] . $d->options['lf'] . $d->trace() ."\n";
        if ($d->options['showSourceOnError']) {
            $str .= $d->highlightFile($errfile, $errline - 1, 3);
         }
        $str .= $str2;
        $str .= $d->options['errorClose'];
        if ($errno == E_ERROR) {
            die($str);
        } else {
            echo $str . $d->option['lf'];
        }
    }
}
////////////////////////////////////////////////////////////////////////
?>
