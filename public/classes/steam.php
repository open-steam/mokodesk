<?php
  /****************************************************************************
  steam.php - This class provides a bunch of functionality which is neccessary to connect and interact with a sTeam server.
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

  Author: Henrik Beige
  EMail: hebeige@gmx.de

  Author: Alexander Roth
  EMail: roth@dsor.de

  ****************************************************************************/

//Set the $steam_path below or before you include this file to the directory where PHPsTeam has been installed to
$steam_path = dirname( __FILE__);


//Do not edit below this point
include("$steam_path/steam_attributes.php");
include("$steam_path/steam_types.php");
include("$steam_path/steam_object.php");
include("$steam_path/steam_request.php");

class steam
{

  //***************************************************************************
  //class variables
  //***************************************************************************

  //socket data
  var $socket;
  var $socket_status;

  //server data
  var $steam_server_ip;
  var $steam_server_port;

  //command buffer
  var $command_buffer;
  var $command_counter;
  var $request_buffer;

  //login information
  var $login_data;
  var $login_arguments;
  var $login_user;
  var $login_user_name;
  var $login_status; //1=logged in, 0=not logged in

  //internal transactionid counter
  var $transactionid;

  //version description
  var $version = "PHPsTeam V1.2";


  //***************************************************************************
  //construtor: steam_api()
  //***************************************************************************
  function steam($server_ip = "", $server_port = "", $login_name = "", $login_password = "")
  {

    //variable init
    $this->_init();

    //if ip and port are set connect automatically
    if($server_ip != "" && $server_port != "")
      $this->connect($server_ip, $server_port, $login_name, $login_password);

  } //function steam_api()


  //***************************************************************************
  //method: init()
  //***************************************************************************
  function _init()
  {

    //variable init
    $this->transaction_id  = 1;

    $this->socket_status = false;
    $this->login_status = false;

    $this->command_buffer = "";
    $this->command_counter = "";
  } //function init()


  //***************************************************************************
  //method: _send_command()
  //***************************************************************************
  function _send_command($command_buffer, $command_counter)
  {
    //send command to steam server
    $bytes = fwrite($this->socket, $command_buffer, strlen($command_buffer));

    //get command
    $data = "";
    for($i = 0; $i < $command_counter; $i++)
    {
      //read "-1" and size of packet
      $buffer = fread($this->socket, 1);
      $size = fread($this->socket, 4);

      $count = hexdec(bin2hex($size)) - 5;

      //read whole ONE packet
      $readData = "";


      while ( $count > 0 ) {
        $databuf = fread($this->socket, $count);
        $datalen = strlen($databuf);

        $count -= $datalen;
        if ( feof($this->socket) )
          $count = 0;
        $readData .= $databuf;
      }
      $data .= $buffer . $size . $readData;

    } //for($i = 0; $i < $command_counter; $i++)

    //return result
    return $data;
  } //function send_command()


  //***************************************************************************
  //method: connect($server_ip, $server_port)
  //***************************************************************************
  function connect($server_ip, $server_port, $login_name = "", $login_password = "")
  {

    //store server ip an port in object
    $this->steam_server_ip = gethostbyname($server_ip);
    $this->steam_server_port = $server_port;


    //open connection to steam server
    $this->socket = fsockopen($server_ip, $server_port, &$errno, &$errstr);
    if(!$this->socket)
    {
      $this->socket_status = false;
      return false;
    }


    //store socket status in class
    $this->socket_status = true;


    //store Server IP and Port in class
    $this->steam_server_ip = $server_ip;
    $this->steam_server_counter = $server_port;

    //if login name and password are set login automatically
    if(trim($login_name) != "" && trim($login_password) != "")
    {
      $this->login($login_name, $login_password);

      //if it is an automatic login => disconnect on error
      if(!$this->login_status)
      {
        $this->disconnect();
        return false;
      }
     }

    return $this->socket;

  } //function connect($server_ip, $server_port, $login_name = "", $login_password = "")


  //***************************************************************************
  //method: disconnect()
  //***************************************************************************
  function disconnect()
  {

    //close socket to steam server
    fclose($this->socket);


    //store socket status in class
    $this->socket_status = false;

  } //function disconnect()


  //***************************************************************************
  //method: login()
  //***************************************************************************
  function login($name, $password)
  {

    //login
    $request = $this->command(new steam_request($this->gettransactionid(), new steam_object(), array($name, $password, $this->version, CLIENT_STATUS_CONNECTED), COAL_LOGIN));


    if($request)
    {
      //save login status 1=login succesfull, 0=login failed
      $this->login_status = !($request->is_error());

      //save login data
      $this->login_data = $request;
      $this->login_arguments = $request->arguments;
      $this->login_user = $request->object;
      $this->login_user_name = $request->arguments[0];

      return $request;
    }
    else
    {
      //save login status 0=login failed
      $this->login_status = 0;
      return false;
    }

  } //function login()


  //***************************************************************************
  //method: logout()
  //***************************************************************************
  function logout()
  {

    //logout
    $request = $this->command(new steam_request($this->gettransactionid(), new steam_object(), "", COAL_LOGOUT));

    //save login status
    $this->login_status = false;

    return $request;

  } //function logout()


  //***************************************************************************
  //method: command()
  //***************************************************************************
  function command($request)
  {

    //if no socket is open return FALSE
    if(!$this->socket)
      return FALSE;

    //encode command
    $command = $request->encode();

    //send single command
    $tmp = $this->_send_command($command, 1);

    //if noone answers return FALSE
    if(trim($tmp) == "")
      return FALSE;

    //decodeOB the results
    $request->decode($tmp);

    return $request;

  } //function command($request)


  //***************************************************************************
  //method: buffer_command()
  //***************************************************************************

  function buffer_command($request)
  {

    //Add command to buffer
    $this->command_buffer .= $request->encode();
    $this->command_counter += 1;

  } //function buffer_command($request)


  //***************************************************************************
  //method: buffer_empty()
  //***************************************************************************

  function buffer_empty()
  {

    //reset buffer
    $this->command_buffer = "";
    $this->command_counter = 0;

  } //function buffer_empty()


  //***************************************************************************
  //method: buffer_flush()
  //***************************************************************************

  function buffer_flush()
  {

    //Only flush buffer if more then one command is buffered
    if($this->command_counter > 0)
    {
      //send command buffer
      $data = $this->_send_command($this->command_buffer, $this->command_counter);

      //get results
      for($i = 0; $i < $this->command_counter; $i++)
      {
        $length = hexdec(bin2hex((substr($data, 1, 4))));
        $request = new steam_request();
        $request->decode(substr($data, 0, $length));

        $result[$request->transactionid] = $request;

        $data = substr($data, $length);
      }; //for($i = 0; $i < $command_counter; $i++)


      //reset buffer data
      $this->command_buffer = "";
      $this->command_counter = 0;
    }
    else
      $result = array();

    //return full/emtpy result
    return $result;

  } //function buffer_flush()


  //***************************************************************************
  //method: gettransactionid()
  //***************************************************************************
  function gettransactionid()
  {

    //increase transactionid
    $this->transactionid += 1;

    return $this->transactionid;

  } //function gettransactionid()


  //***************************************************************************
  //method: download()
  //***************************************************************************
  function download($object)
  {
    //create command
    $command = $this->command(new steam_request($this->gettransactionid(), $object, 0, COAL_FILE_DOWNLOAD));

    if ( $command->is_error() ) 
      return "";

    //download
    $buffer = "";
    $size = 0;
    while($size < $command->arguments[0])
    {
      $buffer .= fread($this->socket, $command->arguments[0]);
      $size = strlen($buffer);
    }

    //return data
    return $buffer;

  } //function download()


  //***************************************************************************
  //method: upload()
  //***************************************************************************
  function upload($path, $data, $name = "")
  {
    //if $path is an object (steam_object) resolve path to object and append name
    if(is_object($path))
      $path = $this->object_to_path($path) . "/" . $name;
    else if($name != "")
      $path = $path . "/" . $name;

    //create command
    $command = $this->command(new steam_request($this->gettransactionid(), 0, array($path, strlen($data)), COAL_FILE_UPLOAD));

    //upload data
    if($command->arguments[0])
      $tmp = fwrite($this->socket, $data, strlen($data));

    //return object id
    return $command->arguments[0];

  } //function upload($command)


  //***************************************************************************
  //method: _predefined_command($object, $method, $arguments, $update, $buffer)
  //$object = steam_object
  //$method = the name of the method
  //$arguments = array("argument", "argument", ...)
  //$update = cache object or arguments or both "obj"/"arg"/"objarg"
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //***************************************************************************

  function _predefined_command($object, $method, $arguments, $update, $buffer)
  {

    //if similar request is has been sent before, modify it
    if(isset($this->request_buffer[$method]))
    {
      //update only needed parts of the request
      $request = $this->request_buffer[$method];
      $request->set_transactionid($this->gettransactionid());
      if($update == "objarg" || $update == "arg")
        $request->set_arguments(array($method, $arguments));
      if($update == "objarg" || $update == "obj")
        $request->set_object($object);
    }
    //create new request
    else
      $request = new steam_request($this->gettransactionid(), $object, array($method, $arguments));

    //store request in request buffer
    $this->request_buffer[$method] = $request;



    //if command should be executed at once => do it
    if($buffer == 0)
    {
      //send request
      $request = $this->command($request);

      //return object arguments
      return $request->arguments;
    }

    //if command should be buffered => do it
    else
    {
      //buffer command
      $this->buffer_command($request);

      //return transactionid
      return $request->transactionid;
    }
  } //function _predefined_command($object, $method, $arguments, $update, $buffer)


  //***************************************************************************
  //** C A L E N D A R
  //***************************************************************************

  //***************************************************************************
  //method: calendar_add_attachment($calendar, $date, $name, $attachment)
  //$calendar = the calendar object to which the date belongs
  //$date = the date object for which the attachment shall be uploaded
  //$name = name of the Attachment
  //$attachment = attachment Data
  //return = true on success / false otherwise
  //***************************************************************************
  function calendar_add_attachment($calendar, $date, $name, $attachment)
  {
    if(!is_object($calendar) || !is_object($date) || !is_string($name) || !is_string($attachment)) return false;

    //get Name and DATE_ATTACHMENT
    $attach_container = $this->get_attributes($date, array(OBJ_NAME, "DATE_ATTACHMENT"));

    //if DATE_ATTACHMENT is 0 (Then its not an steam_object) create container
    if($attach_container["DATE_ATTACHMENT"] == 0)
      $attach_container["DATE_ATTACHMENT"] = $this->create_container("_" . $attach_container[OBJ_NAME], $calendar);

    //resolve upload path for attachment
    $owner = $this->get_creator($calendar);
    $path = "/calendars/" . $this->get_attribute($owner, OBJ_NAME) . "/" . $attach_container[OBJ_NAME] . "/" . $name;

    //upload attachment in container
    $result = $this->upload($path, $attachment);

    //set container id in DATE_ATTACHMENT in case the container has been created recently
    $result = $this->set_attribute($date, "DATE_ATTACHMENT", $attach_container["DATE_ATTACHMENT"]) && $result;

    return true;
  }

  //***************************************************************************
  //method: calendar_delete_attachment($date, $attachment)
  //$date = date object from which the attachment shall be deleted
  //$attachment = Attachment Object
  //return = true on success / false otherwise
  //***************************************************************************
  function calendar_delete_attachment($date, $attachment)
  {
    if(!is_object($date) || !is_object($attachment)) return false;

    //get attachments container
    $attach_container = $this->get_environment($attachment);

    //delete date
    $result = $this->delete_object($attachment);

    //get inventory of attachments container to check wether it was the last or not
    $inventory = $this->get_inventory($attach_container);
    if(sizeof($inventory) < 1)
    {
      //delete attachments container and set DATE_ATTACHMENT to 0
      $result = $this->delete_object($attach_container) && $result;
      $result = $this->set_attribute($date, "DATE_ATTACHMENT", 0) && $result;
    }

    return $result;
  }

  //***************************************************************************
  //method: calendar_add_entry($object, $kind_of, $is_serial, $priority, $title, $description, $timefrom, $timeto, $buffer = 0)
  //$object = the calendar steam_object where the entry shall be added
  //$data = associative array with all attribute names as key and its values
  //        array keys    = value description
  //
  //        DATE_ATTACHMENT    = Link to a Container object (steam_object), default -1
  //        DATE_DESCRIPTION   = calendar entry description
  //        DATE_END_DATE      = unix timestamp when the entry ends
  //        DATE_END_TIME      = string: time when date starts on that day, eg. 15:00
  //        DATE_INTERVAL      = string ...
  //        DATE_IS_SERIAL     = true/false
  //        DATE_KIND_OF_ENTRY = kind of entry (float) (???)
  //        DATE_LOCATION      = string of the location where the date takes place
  //        DATE_NOTICE        = personal comment on the date
  //        DATE_PRIORITY      = priority of entry (float) (???)
  //        DATE_START_DATE    = unix timestamp when the entry starts
  //        DATE_START_TIME    = string: time when date ends on that day, eg. 17:00
  //        DATE_STATUS        = integer: status of the date must be one of 1, 2, 3 (1=>confirmed, 2=>cancelled, 3=>delayed)
  //        DATE_TITLE         = title of the calendar entry
  //        DATE_TYPE          = Array of userdefined descriptions of the date eg. 1 => "Private", 2 => "Business" ...
  //        DATE_WEBSITE       = URL to any website describing the date
  //
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function calendar_add_entry($object, $data, $buffer = 0)
  {
    if(!is_object($object) || !is_array($data)) return false;

    if(!isset($data["DATE_ACCEPTED"]     )) $data["DATE_ACCEPTED"]     = array();
    if(!isset($data["DATE_ATTACHMENT"]   )) $data["DATE_ATTACHMENT"]   = "";
    if(!isset($data["DATE_CANCELLED"]    )) $data["DATE_CANCELLED"]    = array();
    if(!isset($data["DATE_DESCRIPTION"]  )) $data["DATE_DESCRIPTION"]  = "";
    if(!isset($data["DATE_END_DATE"]     )) $data["DATE_END_DATE"]     = "0";
    if(!isset($data["DATE_END_TIME"]     )) $data["DATE_END_TIME"]     = "0";
    if(!isset($data["DATE_INTERVAL"]     )) $data["DATE_INTERVAL"]     = "";
    if(!isset($data["DATE_IS_SERIAL"]    )) $data["DATE_IS_SERIAL"]    = "0";
    if(!isset($data["DATE_KIND_OF_ENTRY"])) $data["DATE_KIND_OF_ENTRY"]= "0";
    if(!isset($data["DATE_LOCATION"]     )) $data["DATE_LOCATION"]     = "";
    if(!isset($data["DATE_NOTICE"]       )) $data["DATE_NOTICE"]       = "";
    if(!isset($data["DATE_ORGANIZERS"]   )) $data["DATE_ORGANIZERS"]   = array();
    if(!isset($data["DATE_PARTICIPANTS"] )) $data["DATE_PARTICIPANTS"] = array();
    if(!isset($data["DATE_PRIORITY"]     )) $data["DATE_PRIORITY"]     = "0";
    if(!isset($data["DATE_START_DATE"]   )) $data["DATE_START_DATE"]   = "0";
    if(!isset($data["DATE_START_TIME"]   )) $data["DATE_START_TIME"]   = "0";
    if(!isset($data["DATE_STATUS"]       )) $data["DATE_STATUS"]       = 1;
    if(!isset($data["DATE_TITLE"]        )) $data["DATE_TITLE"]        = "";
    if(!isset($data["DATE_TYPE"]         )) $data["DATE_TYPE"]         = 0;
    if(!isset($data["DATE_WEBSITE"]      )) $data["DATE_WEBSITE"]      = "";
    return $this->_predefined_command($object, "add_entry", array("name" => "date" . time(), "attributes" => $data), "objarg", $buffer);
  }

  //***************************************************************************
  //method: calendar_get($object, $buffer = 0)
  //$object = steam_object from which the calendar shall be delivered
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function calendar_get($object, $buffer = 0)
  {
    if(!is_object($object)) return false;

    return $this->get_attributes($object, array("user_calendar"), $buffer);
  }

  //***************************************************************************
  //method: calendar_check_conflicts($object, $startdate, $enddate, $starttime, $endtime, $buffer = 0)
  //$object = steam_object from which the calendar shall be delivered
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function calendar_check_conflicts($object, $startdate, $enddate, $starttime, $endtime, $buffer = 0)
  {
    if(!is_object($object) ||
       !is_int($startdate) || !is_int($enddate) ||
       !is_int($starttime) || !is_int($endtime))
       return false;

    return $this->_predefined_command($object, "check_conflicts", array($startdate, $enddate, $starttime, $endtime), "objarg", $buffer);
  }

  //***************************************************************************
  //method: calendar_get_all_entries($calendar, $timefrom = 0, $timeto = 0, $type = 0, $buffer = 0)
  //$calendar = calendar as (steam_object) from which the entries shall be delivered
  //$timefrom = unix timestamp from what time the entries should be delivered
  //$timeto = unix timestamp until what time the entries should be delivered
  //$type = type of date, integer from 0 -> 12, 0 = all entries, 1 -> 12 single entry type
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function calendar_get_all_entries($calendar, $timefrom=0, $timeto=0, $type=0, $buffer = 0)
  {
    if(!is_object($calendar)) return false;

    if($timefrom == 0 && $timeto == 0)
      $timeto = mktime (0,0,0,1,1,2050);

    return $this->_predefined_command($calendar, "get_all_entries", array($timefrom, $timeto, $type), "obj", $buffer);
  }

  //***************************************************************************
  //method: calendar_get_entry_data($object, $buffer = 0)
  //$object = steam_object/calendar entry from which the date data shall be delivered (for description of the attributes see calendar_add_entry(...))
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function calendar_get_entry_data($object, $buffer = 0)
  {
    if(!is_object($object)) return false;

    return $this->get_attributes($object, array(
      "DATE_ACCEPTED",
      "DATE_ATTACHMENT",
      "DATE_CANCELLED",
      "DATE_DESCRIPTION",
      "DATE_END_DATE",
      "DATE_END_TIME",
      "DATE_INTERVAL",
      "DATE_IS_SERIAL",
      "DATE_KIND_OF_ENTRY",
      "DATE_LOCATION",
      "DATE_NOTICE",
      "DATE_ORGANIZERS",
      "DATE_PARTICIPANTS",
      "DATE_PRIORITY",
      "DATE_START_DATE",
      "DATE_START_TIME",
      "DATE_STATUS",
      "DATE_TITLE",
      "DATE_TYPE",
      "DATE_WEBSITE",
      "OBJ_NAME",
      "OBJ_LAST_CHANGED",
      "OBJ_CREATION_TIME"), $buffer);
  }

  //***************************************************************************
  //method: calendar_get_from_group($groupname, $buffer = 0)
  //$groupname = the name or the steam_object of the group from which calendar shall be delivered
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function calendar_get_from_group($groupname, $buffer = 0)
  {
    if(is_object($groupname))
      $group = $groupname;
    else if(is_string($groupname))
      $group = $this->groupname_to_object($groupname);
    else
      return false;

    return $this->get_attribute($group, "GROUP_CALENDAR", $buffer);
  }

  //***************************************************************************
  //method: calendar_get_from_user($username)
  //$username = the name or the steam_object of the user whos calendar shall be delivered, Default => logged in user
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function calendar_get_from_user($username = "", $buffer = 0)
  {
    if($username === "") $username = $this->login_arguments[0];
    if(is_object($username))
      $user = $username;
    else if(is_string($username))
      $user = $this->username_to_object($username);
    else
      return false;

    return $this->get_attribute($user, "USER_CALENDAR", $buffer);
  }

  //***************************************************************************
  //method: calendar_get_calendar_data($object, $buffer = 0)
  //$object = the calendar object
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function calendar_get_calendar_data($object, $buffer = 0)
  {
    if(!is_object($object)) return false;

    return $this->get_attributes($object, array(
      "CALENDAR_TIMETABLE_START",
      "CALENDAR_TIMETABLE_END",
      "CALENDAR_TIMETABLE_ROTATION",
      "CALENDAR_DATE_TYPE",
      "CALENDAR_TRASH",
      "CALENDAR_OWNER",
      "OBJ_NAME",
      "OBJ_LAST_CHANGED",
      "OBJ_CREATION_TIME"), $buffer);
  }

  //***************************************************************************
  //method: calendar_set_calendar_data($object, $attributes, $buffer = 0)
  //$object = the calendar object
  //$attributes = array of the attributes to be changed
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function calendar_set_calendar_data($object, $attributes, $buffer = 0)
  {
    if(!is_object($object) || !is_array($attributes)) return false;

    return $this->set_attributes($object, $attributes, $buffer);
  }

  //***************************************************************************
  //method: calendar_set_entry_data($object, $attributes, $buffer = 0)
  //$object = steam_object/calendar entry from which the date data shall be changed
  //$attributes = hash/mapping/associative array with the attribute names as key and its value (for description of the attributes see calendar_add_entry(...) )
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function calendar_set_entry_data($object, $attributes, $buffer = 0)
  {
    if(!is_object($object) || !is_array($attributes)) return false;

    return $this->set_attributes($object, $attributes, $buffer);
  }



  //***************************************************************************
  //** G E N E R A L
  //***************************************************************************

  //***************************************************************************
  //method: add_annotation($object, $subject, $content, $buffer = 0)
  //$object = steam_object, which shall be annotated
  //$subject = the subject of the annotation as string
  //$content = the content/text of the annotation as string
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function add_annotation($object, $subject, $content, $buffer = 0)
  {
    if(!is_object($object) || !is_string($subject) || !is_string($content)) return false;

    //create document
    $annotation = $this->create_document($subject, "text/plain");

    //set annotation content / text
    $result = $this->set_content($annotation, $content, $buffer);

    //add annotation to $object
    $result = $this->_predefined_command($object, "add_annotation", $annotation, "objarg", $buffer);
    //$this->_predefined_command($object, "set_acquire", $object, "objarg", $buffer);

    //return annotation object
    return $annotation;
  }

  //***************************************************************************
  //method: backtrack_path($object, $root = 0)
  //$object = steam_object from which the backtrack start
  //$root = steam object where the backtrack should end. If root is not within the path it ends at the steam root object
  //return = see _predefined_command
  //***************************************************************************
  function backtrack_path($object, $root = 0)
  {
    if(!is_object($object)) return false;

    $backtrack = array();
    $current_environment = $object;
    while($current_environment->id != $root->id && $current_environment != 0)
    {
      array_unshift($backtrack, $current_environment);
      $current_environment = $this->get_environment($current_environment);
    }

    return $backtrack;
  }

  //***************************************************************************
  //method: call_function($object, $command, $arguments, $buffer = 0)
  //$object = steam_object
  //$command = method name as string
  //$arguments = array with arguments as strings (if the called method has no arguments, leave array emtpy)
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function call_function($object, $command, $arguments = array(), $buffer = 0)
  {
    if(!is_object($object) || !is_string($command)) return false;

    if(is_array($arguments))
      $arg_array = $arguments;
    else
      $arg_array = array($arguments);

    return $this->_predefined_command($object, $command, $arg_array, "objarg", $buffer);
  }

  //***************************************************************************
  //method: create_container($name, $environment = false)
  //$name = name of the new container
  //$environment = steam object in which the new object shall be moved to, if false no move is being made
  //return = created object
  //***************************************************************************
  function create_container($name, $environment = false)
  {
    if(!is_string($name)) return false;

    return $this->create_object(CLASS_CONTAINER, $name, $environment);
  }

  //***************************************************************************
  //method: create_docextern($name, $url, $environment = false)
  //$name = name of the new object
  //$url = the URL DocExtern object shall link to
  //$environment = steam object in which the new object shall be moved to, if false no move is being made
  //return = created object
  //***************************************************************************
  function create_docextern($name, $url, $environment = false)
  {
    if(!is_string($name) || !is_string($url)) return false;
    return $this->create_object(CLASS_DOCEXTERN, $name, $environment, array("url" => $url));
  }

  //***************************************************************************
  //method: create_document($name, $mimetype, $environment = false)
  //$name = name of the new object
  //$mimetype = mimetype of the new object
  //$environment = steam object in which the new object shall be moved to, if false no move is being made
  //return = created object
  //***************************************************************************
  function create_document($name, $mimetype, $environment = false)
  {
    if(!is_string($name) || !is_string($mimetype)) return false;

    return $this->create_object(CLASS_DOCUMENT, $name, $environment, array("mimetype" => $mimetype));
  }

  //***************************************************************************
  //method: create_link($name, $link_to, $environment = false)
  //$name = name of the new object
  //$link_to = steam_object to which the link should point
  //$environment = steam object in which the new object shall be moved to, if false no move is being made
  //return = created object
  //***************************************************************************
  function create_link($name, $link_to, $environment = false)
  {
    if(!is_string($name) || !is_object($link_to)) return false;

    return $this->create_object(CLASS_LINK, $name, $environment, array("link_to" => $link_to));
  }

  //***************************************************************************
  //method: create_object($class_id, $name, $environment = false, $additional_parameter = array())
  //$class_id = object class id as defined in steam_types
  //$name = name of the new object
  //$environment = steam object in which the new object shall be moved to, if false no move is being made
  //$additional_parameter = an array of additional parameters of the factory e.g. array("mimetype" => "text/plain")
  //return = created object
  //***************************************************************************
  function create_object($class_id, $name, $environment = false, $additional_parameter = array())
  {
    //get factory object
    $factory = $this->login_data->arguments[9][hexdec(bin2hex($class_id))];

    //create object
    $parameter = array_merge(array("name" => $name), $additional_parameter);
    $object = $this->_predefined_command($factory, "execute", $parameter, "objarg", 0);

    //move object to destination environment
    if($environment !== false)
      $request = $this->move_object($object, $environment);

    return $object;
  }

  //***************************************************************************
  //method: delete_object($object, $buffer = 0)
  //$object = object to be deleted
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = true on success
  //***************************************************************************
  function delete_object($object, $buffer = 0)
  {
    if(!is_object($object)) return false;

    //delete object
    return $this->_predefined_command($object, "delete", array(), "obj", $buffer);
  }

  //***************************************************************************
  //method: duplicate_object($object, $recursive, $buffer = 0)
  //$object = object to be duplicated
  //$recursive = if object is a container all content may be duplicated too, true => do it, false => dont do it
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = duplicated object
  //***************************************************************************
  function duplicate_object($object, $recursive = false, $buffer = 0)
  {
    if(!is_object($object)) return false;

    //produce duplicate from object
    if($recursive)
      return $this->_predefined_command($object, "duplicate", array(true), "objarg", $buffer);
    else
      return $this->_predefined_command($object, "duplicate", array(), "objarg", $buffer);
  }

  //***************************************************************************
  //method: get_annotating($object, $buffer = 0)
  //$object = steam_object - annotation from which the annotating object shall be derived
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function get_annotating($object, $buffer = 0)
  {
    if(!is_object($object)) return false;

    return $this->_predefined_command($object, "get_annotating", array(), "obj", $buffer);
  }

  //***************************************************************************
  //method: get_annotations($object, $buffer = 0)
  //$object = steam_object
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function get_annotations($object, $buffer = 0)
  {
    if(!is_object($object)) return false;

    return $this->_predefined_command($object, "get_annotations", array(), "obj", $buffer);
  }

  //***************************************************************************
  //method: get_attribute($object, $key, $buffer = 0)
  //$object = steam_object
  //$key = name of the attribute
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command, if $object is an array the inventories of all steam_object will be he result
  //***************************************************************************
  function get_attribute($object, $key, $buffer = 0)
  {
    if(!(is_object($object) || is_array($object))) return false;

    if(is_array($object))
    {
      $result = array();
      foreach($object as $item)
        array_push($result, $this->_predefined_command($item, "query_attribute", $key, "objarg", $buffer));
      return $result;
    }
    else
      return $this->_predefined_command($object, "query_attribute", $key, "objarg", $buffer);
  }

  //***************************************************************************
  //method: get_attribute_names($object, $buffer = 0)
  //$object = steam_object of which all attribute names should be gathered
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command, if $object is an array the inventories of all steam_object will be he result
  //***************************************************************************
  function get_attribute_names($object, $buffer = 0)
  {
    if(!is_object($object)) return false;

    return $this->_predefined_command($object, "get_attribute_names", array(), "objarg", $buffer);
  }

  //***************************************************************************
  //method: get_attributes($object, $attributes, $buffer = 0)
  //$object = steam_object
  //$attributes = array with attribute names ( array("attribute", "attribute", ... ) )
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command, if $object is an array the inventories of all steam_object will be he result
  //***************************************************************************
  function get_attributes($object, $attributes, $buffer = 0)
  {
    if(!(is_object($object) || is_array($object)) || !is_array($attributes)) return false;

    foreach ($attributes as $key)
      $list[$key] = "";

    if(is_array($object))
    {
      $result = array();
      foreach($object as $item)
        array_push($result, $this->_predefined_command($item, "query_attributes", $list, "objarg", $buffer));
      return $result;
    }
    else
      return $this->_predefined_command($object, "query_attributes", $list, "objarg", $buffer);
  }

  //***************************************************************************
  //method: get_content($object, $buffer = 0)
  //$object = steam_object
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function get_rss($object, $version = "0.9")
  {
    if(!is_object($object)) return false;
    $rss_script = $this->path_to_object("/scripts/rss.pike");

    $result = $this->_predefined_command($rss_script, "execute", array( array( "feed" => $object->id, "v" => $version ) ), "objarg", 0);
    $ret = array( "content" => $result[0], "mime-type" => $result[1]);
    return $ret;
  }


  //***************************************************************************
  //method: get_content($object, $buffer = 0)
  //$object = steam_object
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function get_content($object, $buffer = 0)
  {
    if(!is_object($object)) return false;

    return $this->_predefined_command($object, "get_content", array(), "obj", $buffer);
  }

  //***************************************************************************
  //method: get_content_size($object, $buffer = 0)
  //$object = steam_object
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function get_content_size($object, $buffer = 0)
  {
    if(!is_object($object)) return false;

    return $this->_predefined_command($object, "get_content_size", array(), "obj", $buffer);
  }

  //***************************************************************************
  //method: get_creator($object, $buffer = 0)
  //$object = steam_object
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function get_creator($object, $buffer = 0)
  {
    if(!is_object($object) && !is_array($object)) return false;

    if(is_array($object))
    {
      $result = array();
      foreach($object as $item)
        array_push($result, $this->_predefined_command($item, "get_creator", array(), "obj", $buffer));
      return $result;
    }
    else
      return $this->_predefined_command($object, "get_creator", array(), "obj", $buffer);
  }

  //***************************************************************************
  //method: get_exit($object, $buffer = 0)
  //$object = steam_object (does only make sense if object is an exit)
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function get_exit($object, $buffer = 0)
  {
    if(!is_object($object)) return false;

    return $this->_predefined_command($object, "get_exit", array(), "obj", $buffer);
  }

  //***************************************************************************
  //method: get_environment($object, $buffer = 0)
  //$object = steam_object
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function get_environment($object, $buffer = 0)
  {
    if(!is_object($object)) return false;

    return $this->_predefined_command($object, "get_environment", array(), "obj", $buffer);
  }

  //***************************************************************************
  //method: get_favourites_from_user($user, $buffer = 0)
  //$user = steam_object of user from who the favourites shall be derived, or 0 if current user should be looked up
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function get_favourites_from_user($user = 0, $buffer = 0)
  {
    if($user === 0) $user = $this->login_user;
      return $this->get_attribute($user, USER_FAVOURITES, $buffer);
  }

  //***************************************************************************
  //method: get_groups_all($buffer = 0)
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function get_groups_all($buffer = 0)
  {
    return $this->_predefined_command($this->login_data->arguments[8]["groups"], "get_groups", array(), "", $buffer);
  }

  //***************************************************************************
  //method: get_groups_from_user($user, $buffer = 0)
  //$user = steam_object of user from who the group shall be derived, or 0 if current user should be looked up
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function get_groups_from_user($user = 0, $buffer = 0)
  {
    if($user === 0) $user = $this->login_user;
    return $this->_predefined_command($user, "get_groups", array(), "obj", $buffer);
  }

  //***************************************************************************
  //method: get_parent_of_group($group, $buffer = 0)
  //$user = steam_object of group from who the parent shall be derived
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function get_parent_of_group($group = 0, $buffer = 0)
  {
    return $this->_predefined_command($group, "get_parent", array(), "obj", $buffer);
  }

  //***************************************************************************
  //method: get_inventory($object, $buffer = 0)
  //$object = steam_object
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command, if $object is an array the inventories of all steam_object will be he result
  //***************************************************************************
  function get_inventory($object, $buffer = 0)
  {
    if(is_array($object))
    {
      $result = array();
      foreach($object as $item)
        array_push($result, $this->_predefined_command($item, "get_inventory", array(), "obj", $buffer));
      return $result;
    }
    else
      return $this->_predefined_command($object, "get_inventory", array(), "obj", $buffer);
  }

  //***************************************************************************
  //method: get_inventory_by_class($object, $class_definition, $buffer = 0)
  //$object = steam_object
  //$class_definition = any combination of object classes, e.g. CLASS_CONTAINER or CLASS_CONTAINER|CLASS_EXIT
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command, if $object is an array the inventories of all steam_object will be he result
  //***************************************************************************
  function get_inventory_by_class($object, $class_definition, $buffer = 0)
  {
    //change bitwise format to integer format
    $class_definition = hexdec(bin2hex(substr($class_definition, 1, 4)));
    settype($class_definition,"integer");

    if(is_array($object))
    {
      $result = array();
      foreach($object as $item)
        array_push($result, $this->_predefined_command($item, "get_inventory_by_class", array($class_definition), "obj", $buffer));
      return $result;
    }
    else
      return $this->_predefined_command($object, "get_inventory_by_class", array($class_definition), "obj", $buffer);
  }

  //***************************************************************************
  //method: get_link_object($object, $buffer = 0)
  //$object = steam_object from which the link shall be derived (has to be a steamobject of CLASS_LINK)
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function get_link_object($object, $buffer = 0)
  {
    if(!is_object($object)) return false;

    return $this->_predefined_command($object, "get_link_object", array(), "obj", $buffer);
  }

  //***************************************************************************
  //method: get_members_from_group($group, $buffer = 0)
  //$group = steam_object of the group whos members shall be derived
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function get_members_from_group($group, $buffer = 0)
  {
    if(!is_object($group)) return false;

    return $this->_predefined_command($group, "get_members", array(), "obj", $buffer);
  }

  //***************************************************************************
  //method: get_object_class($object, $buffer = 0)
  //$object = steam_object of which the class shall be delivered
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
        function get_object_class($object, $buffer = 0)
        {
    if(!is_object($object)) return false;

    return $this->_predefined_command($object, "get_object_class", array(), "obj", $buffer);
        }

  //***************************************************************************
  //method: get_referencing_objects($object, $buffer = 0)
  //$object = steam_object from which all references (link objects that point on $object) shall be derived
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function get_referencing_objects($object, $buffer = 0)
  {
    if(!is_object($object)) return false;

    return $this->_predefined_command($object, "get_referencing", array(), "obj", $buffer);
  }

  //***************************************************************************
  //method: get_workroom($object, $type, $buffer = 0)
  //$object = steam_object - user from which the workroom shall be determined
  //$type = GROUP_WORKROOM or USER_WORKROOM
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function get_workroom($object, $type, $buffer = 0)
  {
    if(!is_object($object)) return false;

    return $this->_predefined_command($object, "query_attribute", array($type), "objarg", $buffer);
  }

  //***************************************************************************
  //method: get_workroom_group($object, $buffer = 0)
  //$object = steam_object - group from which the workroom shall be determined
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function get_workroom_group($object, $buffer = 0)
  {
    if(!is_object($object)) return false;

    return $this->get_workroom($object, GROUP_WORKROOM, $buffer);
  }

  //***************************************************************************
  //method: get_workroom_user($object, $buffer = 0)
  //$object = steam_object - user from which the workroom shall be determined
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function get_workroom_user($object, $buffer = 0)
  {
    if(!is_object($object)) return false;

    return $this->get_workroom($object, USER_WORKROOM, $buffer);
  }

  //***************************************************************************
  //method: groupname_to_object($name, $buffer = 0)
  //$name = name of the group
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function groupname_to_object($name, $buffer = 0)
  {
    if(!is_string($name)) return false;

    return $this->_predefined_command($this->login_data->arguments[8]["groups"], "lookup", array($name), "arg", $buffer);
  }

  //***************************************************************************
  //method: group_is_member($group, $user, $buffer = 0)
  //$group = group object
  //$user = user object that shall be tested whether it is a member of $group or not
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function group_is_member($group, $user, $buffer = 0)
  {
    if(!is_object($group) || !is_object($user)) return false;

    return $this->_predefined_command($group, "is_member", array($user), "objarg", $buffer);
  }

  //***************************************************************************
  //method: move_object($object, $environment, $buffer = 0)
  //$object = object to be moved
  //$environment = environment object in which object should be moved to
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = true on success
  //***************************************************************************
  function move_object($object, $environment, $buffer = 0)
  {
    if(!is_object($object) || !is_object($environment)) return false;

    //move exercise container to course room
    return $this->_predefined_command($object, "move", array($environment), "objarg", 0);
  }

  //***************************************************************************
  //method: object_name($object, $buffer = 0)
  //$object = steam_object or array of steam_objects
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function object_name($object, $buffer = 0)
  {
    if(!is_object($object) && !is_array($object)) return false;

    //single object
    if(is_object($object))
      return $this->_predefined_command($object, "query_attribute", array(OBJ_NAME), "objarg", $buffer);

    //get object_name for every object in array
    else if(is_array($object))
    {
      $results = array();
      foreach($object as $tmp)
      {
        if(!is_object($tmp)) continue;
        $results[] = $this->_predefined_command($tmp, "query_attribute", array(OBJ_NAME), "objarg", $buffer);
      }
      return $results;
    }
  }

  //***************************************************************************
  //method: object_identifier($object, $buffer = 0)
  //$object = steam_object or array of steam_objects
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function object_identifier($object, $buffer = 0)
  {
    if(!is_object($object) && !is_array($object)) return false;

    //single object
    if(is_object($object))
      return $this->_predefined_command($object, "get_identifier", array(), "objarg", $buffer);

    //get object_name for every object in array
    else if(is_array($object))
    {
      $results = array();
      foreach($object as $tmp)
      {
        if(!is_object($tmp)) continue;
        $results[] = $this->_predefined_command($tmp, "get_identifier", array(), "objarg", $buffer);
      }
      return $results;
    }
  }

  //***************************************************************************
  //method: object_to_path($object, $buffer = 0)
  //$object = steam_object
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function object_to_path($object, $buffer = 0)
  {
    if(!is_object($object)) return false;

    return $this->_predefined_command($this->login_data->arguments[8]["filepath:tree"], "object_to_filename", array($object), "arg", $buffer);
  }

  //***************************************************************************
  //method: path_to_object($path, $buffer = 0)
  //$path = absolut path to steam object
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function path_to_object($path, $buffer = 0)
  {
    if(!is_string($path)) return false;

    return $this->_predefined_command($this->login_data->arguments[8]["filepath:tree"], "path_to_object", array($path), "arg", $buffer);
  }

  //***************************************************************************
  //method: send_message_simple($target, $subject, $message, $buffer = 0)
  //$target = array of string which are the targets of the message (string) object->id, username, mailadress
  //$subject = subject of the message
  //$message = message body/content
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function send_message_simple($target, $subject, $message, $buffer = 0)
  {
    if(!(is_array($target) || is_string($target)) || !is_string($subject) || !is_string($message)) return false;

    if(is_string($target))
      $target = array($target);

    return $this->_predefined_command($this->login_data->arguments[8]["forward"] , "send_message_simple", array($target, $subject, $message), "objarg", $buffer);
  }

  //***************************************************************************
  //method: set_attribute($object, $key, $value, $buffer = 0)
  //$object = steam_object
  //$key = name of the attribute
  //$value = value of the attribute
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function set_attribute($object, $key, $value, $buffer = 0)
  {
    if(!is_object($object) || !is_string($key)) return false;

    return $this->_predefined_command($object, "set_attributes", array($key => $value), "objarg", $buffer);
  }

  //***************************************************************************
  //method: set_attributes($object, $attributes, $buffer = 0)
  //$object = steam_object
  //$attributes = associative array with attribute name as key and attribute value as value ( array("name" => "Henrik") )
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function set_attributes($object, $attributes, $buffer = 0)
  {
    if(!is_object($object) || !is_array($attributes)) return false;

    return $this->_predefined_command($object, "set_attributes", $attributes, "objarg", $buffer);
  }

  //***************************************************************************
  //method: set_content($object, $content, $buffer = 0)
  //$object = steam_object
  //$content = content of the object
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function set_content($object, $content, $buffer = 0)
  {
    if(!is_object($object)) return false;

    return $this->_predefined_command($object, "set_content", array($content), "objarg", $buffer);
  }

  //***************************************************************************
  //method: set_favourites_for_user($favourites, $user = 0, $buffer = 0)
  //$favourites = array of steam objects which represent the favourites of the given user
  //$user = steam_object of user from who the favourites shall be derived, or 0 if current user should be looked up
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function set_favourites_for_user($favourites, $user = 0, $buffer = 0)
  {
    if($user === 0) $user = $this->login_user;
      return $this->set_attribute($user, USER_FAVOURITES, $favourites, $buffer);
  }

  //***************************************************************************
  //method: set_user_password($password, $buffer = 0)
  //$password = new password of the currently logged in user
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function set_user_password($password, $buffer = 0)
  {
    if(!is_string($password)) return false;

    return $this->_predefined_command($this->login_user, "set_user_password", array($password), "objarg", $buffer);
  }

  //***************************************************************************
  //method: swap_inventory($container, $object_from, $object_to, $buffer = 0)
  //$container = steam_object container in which object order shall be changed
  //$object_from = object that shall be exchange with $object_to (can be object or the number of the object in directory order)
  //$object_to = object that shall be exchange with $object_from (can be object or the number of the object in directory order)
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function swap_inventory($container, $object_from, $object_to, $buffer = 0)
  {
    if(!is_object($container) ||
       !(is_object($object_from) || is_int($object_from)) ||
       !(is_object($object_to) || is_int($object_to))) return false;

    return $this->_predefined_command($container, "swap_inventory", array($object_from, $object_to), "objarg", $buffer);
  }

  //***************************************************************************
  //method: username_to_object($name, $buffer = 0)
  //$name = name of the user
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function username_to_object($name, $buffer = 0)
  {
    if(!is_string($name)) return false;

    return $this->_predefined_command($this->login_data->arguments[8]["users"], "lookup", array($name), "arg", $buffer);
  }



  //***************************************************************************
  //** S E C U R I T Y
  //***************************************************************************

  //***************************************************************************
  //method: check_user_access($object, $user, $bit, $buffer = 0)
  //$object = object from which the user access shall be derived
  //$user = user object or name who wants to access the $object
  //$bit = bit mask of rights
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function check_user_access($object, $user, $bit, $buffer = 0)
  {
    if(!(is_object($object) && (is_object($user) || is_string($user)))) return false;

    if(is_string($user))
      $user = $this->username_to_object($user);

    return $this->_predefined_command($this->login_data->arguments[8]["security"], "check_user_access", array($object, $user, (int) hexdec($bit), 0, 0), "arg", $buffer);
  }

  //***************************************************************************
  //method: check_user_access_read($object, $user, $buffer = 0)
  //$object = object from which the user access shall be derived
  //$user = user object or name who wants to access the $object
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function check_user_access_read($object, $user, $buffer = 0)
  {
                if (!is_object($object) || !is_object($user))
                        return FALSE;

    return $this->check_user_access($object, $user, SANCTION_READ, $buffer);
  }

  //***************************************************************************
  //method: check_user_access_sanction($object, $user, $buffer = 0)
  //$object = object from which the user access shall be derived
  //$user = user object or name who wants to access the $object
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function check_user_access_sanction($object, $user, $buffer = 0)
  {
                if (!is_object($object) || !is_object($user))
                        return FALSE;

    return $this->check_user_access($object, $user, SANCTION_SANCTION, $buffer);
  }

  //***************************************************************************
  //method: check_user_access_write($object, $user, $buffer = 0)
  //$object = object from which the user access shall be derived
  //$user = user object or name who wants to access the $object
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function check_user_access_write($object, $user, $buffer = 0)
  {
                if (!is_object($object) || !is_object($user))
                        return FALSE;

    return $this->check_user_access($object, $user, SANCTION_WRITE, $buffer);
  }

//***************************************************************************
  //method: check_user_access_annotate($object, $user, $buffer = 0)
  //$object = object from which the user access shall be derived
  //$user = user object or name who wants to access the $object
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function check_user_access_annotate($object, $user, $buffer = 0)
  {
                if (!is_object($object) || !is_object($user))
                        return FALSE;

    return $this->check_user_access($object, $user, SANCTION_ANNOTATE, $buffer);
  }

  //***************************************************************************
  //method: get_acquire($object, $buffer = 0)
  //$object = object from which the acquire status shall be derived
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function get_acquire($object, $buffer = 0)
  {
                if(!is_object($object))
                        return FALSE;

    $tmp = $this->_predefined_command($object, "resolve_acquire", array(), "objarg", 0);
    if(is_array($tmp) && is_string($tmp[0]) && is_object($tmp[1]))
      return $this->_predefined_command($tmp[1], $tmp[0], array(), "obj", $buffer);
    else if(is_object($tmp))
      return $tmp;
    else
      return false;

//    return $this->_predefined_command($object, "get_acquire", array(), "objarg", $buffer);
  }

  //***************************************************************************
  //method: get_sanction($object, $person, $buffer = 0)
  //$object = steam_object from which the sanction bit code shall be derived
  //$person = group or user from which the sanction string of $object shall be derived
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = sanction bit code
  //***************************************************************************
        function get_sanction($object, $person, $buffer = 0)
        {
                if(!is_object($object) || !is_object($person))
                        return FALSE;

                return $this->_predefined_command($object, "query_sanction", array($person), "objarg", $buffer);
        }

  //***************************************************************************
  //method: sanction_object($object, $group, $sanction, $buffer = 0)
  //$object = steam_object from which the sanction bit code shall be derived
  //$group = group or user for which the sanction string of $object shall be set
  //$sanction = the sanction string
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
        function sanction_object($object, $group, $sanction, $buffer = 0)
        {
                if (!is_object($object) || !is_object($group))
                        return FALSE;

                return $this->_predefined_command($object, "sanction_object", array($group, $sanction), "objarg", $buffer);
        }

  //***************************************************************************
  //method: set_acquire($object, $acquire_object, $buffer = 0)
  //
  //(in case of relative environment acquire you MUST use method set_aquire_environment)
  //
  //$object = object from which the acquire status shall be derived
  //$acquire_object = object from which the rights shall be acquired or 0 to remove acquiring
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function set_acquire($object, $acquire_object, $buffer = 0)
  {
                if (!is_object($object) || !(is_object($acquire_object) || $acquire_object === 0))
                        return FALSE;

    return $this->_predefined_command($object, "set_acquire", array($acquire_object), "objarg", $buffer);
  }

  //***************************************************************************
  //method: set_acquire_annotating($object, $environment, $buffer = 0)
  //$object = object from which the acquire status shall be derived
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function set_acquire_annotating($object, $buffer = 0)
  {
                if (!is_object($object))
                        return FALSE;

    return $this->_predefined_command($object, "set_acquire", array(array("function", "get_annotating", $object)), "objarg", $buffer);
  }

  //***************************************************************************
  //method: set_acquire_environment($object, $environment, $buffer = 0)
  //$object = object from which the acquire status shall be derived
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function set_acquire_environment($object, $buffer = 0)
  {
                if (!is_object($object))
                        return FALSE;

    return $this->_predefined_command($object, "set_acquire", array(array("function", "get_environment", $object)), "objarg", $buffer);
  }

  //***************************************************************************
  //method: set_insert_access($object, $personOrGroup, $buffer = 0)
  //$object = steam_object from which the sanction bit code shall be derived
  //$personOrGroup = group or user for which the write access for $object shall be set
  //$status = true - set write, false - unset write
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
        function set_insert_access($object, $personOrGroup, $status, $buffer = 0)
        {
                if(!is_object($object) || !is_object($personOrGroup))
                        return FALSE;

                $sanction = $this->get_sanction($object, $personOrGroup);
                if($status)
      $sanction |= hexdec(SANCTION_INSERT);
    else
      $sanction &= ~hexdec(SANCTION_INSERT);

                return $this->sanction_object($object, $personOrGroup, $sanction, $buffer);
        }
  //***************************************************************************
  //method: set_read_access($object, $personOrGroup, $buffer = 0)
  //$object = steam_object from which the sanction bit code shall be derived
  //$personOrGroup = group or user for which the the read access for $object shall be set
  //$status = true - set read, false - unset read
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
        function set_read_access($object, $personOrGroup, $status, $buffer = 0)
        {
                if(!is_object($object) || !is_object($personOrGroup))
                        return FALSE;

                $sanction = $this->get_sanction($object, $personOrGroup);
                if($status)
      $sanction |= hexdec(SANCTION_READ);
    else
      $sanction &= ~hexdec(SANCTION_READ);

                return $this->sanction_object($object, $personOrGroup, $sanction, $buffer);
        }

  //***************************************************************************
  //method: set_sanction_access($object, $personOrGroup, $buffer = 0)
  //$object = steam_object from which the sanction bit code shall be derived
  //$personOrGroup = group or user for which the the read access for $object shall be set
  //$status = true - set read, false - unset read
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
        function set_sanction_access($object, $personOrGroup, $status)
        {
                if(!is_object($object) || !is_object($personOrGroup))
                        return FALSE;

                $sanction = $this->get_sanction($object, $personOrGroup);
                if($status) {
			      	$sanction |= hexdec(SANCTION_SANCTION);
			      	$this->sanction_object($object, $personOrGroup, $sanction);
			      	// set meta sanction too
			      	$this->set_sanction_meta($object, $personOrGroup, hexdec(SANCTION_SANCTION));
			      }
			    else {
			      $sanction &= ~hexdec(SANCTION_SANCTION);
			      $this->sanction_object($object, $personOrGroup, $sanction);
			      // remove meta sanction too
                  $this->set_sanction_meta($object, $personOrGroup, 0);			      
				}
                return 1;
        }

/**
 * function sanction_meta:
 * 
 * @param $pSanction access rights (as Decimal!)
 * @param $pPersonOrGroup the group to grant the specified rights to
 * @param $pBuffer 
 * 
 * @return 
 */
public function set_sanction_meta( $pObject, $pSanction, $pPersonOrGroup, $pBuffer = 0 )
{
	return $this->_predefined_command(
		$pObject,
		"sanction_object_meta",
		array( $pPersonOrGroup, $pSanction ),
		$pBuffer
		);
}


  //***************************************************************************
  //method: set_sanction_all($object, $personOrGroup, $buffer = 0)
  //$object = steam_object from which the sanction bit code shall be derived
  //$personOrGroup = group or user for which the ALL_SANCTION for $object shall be set
  //$status = true - set all sanction, false - unset all sanction
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
        function set_sanction_all($object, $personOrGroup, $status, $buffer = 0)
        {
                if(!is_object($object) || !is_object($personOrGroup))
                        return FALSE;

				$sanction = $this->get_sanction($object, $personOrGroup);
                if($status) {
					$sanction |= hexdec(SANCTION_ALL);
					// set meta sanction too
					$this->set_sanction_meta($object, $personOrGroup, hexdec(SANCTION_SANCTION));
				}
				else {
					$sanction &= ~hexdec(SANCTION_ALL);
					// remove meta sanction too
					$this->set_sanction_meta($object, $personOrGroup, 0);
				}
                return $this->sanction_object($object, $personOrGroup, $sanction, $buffer);
        }

  //***************************************************************************
  //method: set_write_access($object, $personOrGroup, $buffer = 0)
  //$object = steam_object from which the sanction bit code shall be derived
  //$personOrGroup = group or user for which the write access for $object shall be set
  //$status = true - set write, false - unset write
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
        function set_write_access($object, $personOrGroup, $status, $buffer = 0)
        {
                if(!is_object($object) || !is_object($personOrGroup))
                        return FALSE;

                $sanction = $this->get_sanction($object, $personOrGroup);
                if($status)
					$sanction |= hexdec(SANCTION_WRITE);
				else
					$sanction &= ~hexdec(SANCTION_WRITE);

                return $this->sanction_object($object, $personOrGroup, $sanction, $buffer);
        }

  //***************************************************************************
  //method: set_move_access($object, $personOrGroup, $buffer = 0)
  //$object = steam_object from which the sanction bit code shall be derived
  //$personOrGroup = group or user for which the move access for $object shall be set
  //$status = true - set move, false - unset move
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
        function set_move_access($object, $personOrGroup, $status, $buffer = 0)
        {
                if(!is_object($object) || !is_object($personOrGroup))
                        return FALSE;

                $sanction = $this->get_sanction($object, $personOrGroup);
                if($status)
      $sanction |= hexdec(SANCTION_MOVE);
    else
      $sanction &= ~hexdec(SANCTION_MOVE);

                return $this->sanction_object($object, $personOrGroup, $sanction, $buffer);
        }

//***************************************************************************
  //method: set_annotate_access($object, $personOrGroup, $buffer = 0)
  //$object = steam_object from which the sanction bit code shall be derived
  //$personOrGroup = group or user for which the write access for $object shall be set
  //$status = true - set write, false - unset write
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
        function set_annotate_access($object, $personOrGroup, $status, $buffer = 0)
        {
                if(!is_object($object) || !is_object($personOrGroup))
                        return FALSE;

                $sanction = $this->get_sanction($object, $personOrGroup);
                if($status)
      $sanction |= hexdec(SANCTION_ANNOTATE);
    else
      $sanction &= ~hexdec(SANCTION_ANNOTATE);

                return $this->sanction_object($object, $personOrGroup, $sanction, $buffer);
        }



  //***************************************************************************
  //** S E A R C H I N G
  //***************************************************************************

  //***************************************************************************
  //method: search_attribute($attribute_key, $attribute_value, $buffer = 0)
  //$attribute_key = the attribute key that should have the $attribute_value
  //$attribute_value = the attribute value that should be set under the $attribute_key
  //$buffer = optional if command should be buffered (=1) or sent immidiately (=0)
  //return = see _predefined_command
  //***************************************************************************
  function search_attribute($attribute_key, $attribute_value, $buffer = 0)
  {
    return $this->_predefined_command($this->login_data->arguments[8]["searching"], "searchAsyncAttribute", array($attribute_key, $attribute_value), "arg", $buffer);
  }

} //class steam

?>
