<?php
  /****************************************************************************
  steam_request.php - Data structure for a request to/from the steam server
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

  ****************************************************************************/

class steam_request
{
  //***************************************************************************
  //class variables
  //***************************************************************************

  var $length;
  var $transactionid;
  var $coalcommand;
  var $object;
  var $arguments;

  var $length_encoded;
  var $transactionid_encoded;
  var $object_encoded;
  var $arguments_encoded;

  var $command_encoded;

  //***************************************************************************
  //construtor: steam_request()
  //***************************************************************************
  function steam_request($transactionid = 0, $object = 0, $arguments = 0, $coalcommand = COAL_COMMAND)
  {

    //set class variables
    $this->set_transactionid($transactionid);
    $this->set_coalcommand($coalcommand);
    $this->set_object($object);
    $this->set_arguments($arguments);

  } //function steam_request($transactionid, $coalcommand, $object, $arguments)


  //***************************************************************************
  //method: encode()
  //***************************************************************************
  function encode()
  {
    //build COAL command
    $command = "\xff" . $this->length_encoded . $this->transactionid_encoded . $this->coalcommand . $this->object_encoded . $this->arguments_encoded;

    return $command;

  } //function encode()


  //***************************************************************************
  //method: encode_data()
  //NOTE: float fehlt
  //***************************************************************************
  function encode_data($data)
  {
    //encode array/mapping
    if(is_array($data) &&
       //this is the definition of the CMD_TYPE_FUNCTION
       !(sizeof($data) == 3 &&
         isset($data[0]) && $data[0] == "function" &&
         isset($data[1]) && gettype($data[1]) == "string" &&
         isset($data[2]) && gettype($data[2]) == "object"
        )
      )
    {
      //check if its an  array or mapping
      $array = true;
      $j = 0;
      foreach($data as $key => $value)
      {
        if(gettype($key) != "integer" || !($key === $j))
        {
          $array = false;
          break;
        }
        $j++;
      }


      //build array
      if($array)
      {
        $count = sizeof($data);
        $newdata = CMD_TYPE_ARRAY . pack("C*", $count >> 8 , $count);

        foreach($data as $tmpdata)
        {
          $newdata .= $this->encode_data($tmpdata);
        } //foreach($data as $tmpdata)

      }

      //build mapping
      else
      {
        $count = sizeof($data);
        $newdata = CMD_TYPE_MAPPING . pack("C*", $count >> 8, $count);

        foreach($data as $key => $tmpdata)
        {
          $newdata .= $this->encode_data($key);
          $newdata .= $this->encode_data($tmpdata);
        }
      }

    }

    //encode basic types
    else
    {
      $type = gettype($data);
      switch ($type)
      {
        case "boolean":
          $data = ($data)?1:0;
        case "integer":
          $newdata = CMD_TYPE_INT . pack("C*", $data >> 24, $data >> 16, $data >> 8, $data);
          break;
        case "double":
          $newdata .= CMD_TYPE_FLOAT . "\x00\x00\x00\x00";
          break;
        case "string":
          $len = strlen($data);
          $newdata = CMD_TYPE_STRING . pack("C*", $len >> 24, $len >> 16, $len >> 8, $len) . $data;
          break;
        case "object":
          $tmpid = $data->id;
          $tmpclass = $data->class;
          $newdata = CMD_TYPE_OBJECT .
                     pack("C*", $tmpid >> 24, $tmpid >> 16, $tmpid >> 8, $tmpid) .
                     pack("C*", $tmpclass >> 24, $tmpclass >> 16, $tmpclass >> 8, $tmpclass);
          break;
        case "array":
          //definition of the CMD_TYPE_FUNCTION
          if(sizeof($data) == 3 && $data[0] == "function" && gettype($data[1]) == "string" && gettype($data[2]) == "object")
          {
            $tmp = "(" . $data[1] . "():" . $data[2]->id . ")";
            $len = strlen($tmp);
            $newdata = CMD_TYPE_FUNCTION . pack("C*", $len >> 24, $len >> 16, $len >> 8, $len) . $tmp;
          }
          break;
        default:
          $newdata = "Error: Type '$type' is not supported by the COAL protocoll!";
          echo("$newdata<br>\n");
      } //switch
    }

    return $newdata;

  } //function encode_data($data)


  //***************************************************************************
  //method: decode()
  //***************************************************************************
  function decode($command)
  {
    $this->command_encoded = $command;


    //strip answer of header
    $this->length = hexdec(bin2hex(substr($command, 1, 4)));
    $this->transactionid = hexdec(bin2hex(substr($command, 5, 4)));
    $this->coalcommand = $command[9];
    $this->object = new steam_object(hexdec(bin2hex(substr($command, 10, 4))), hexdec(bin2hex(substr($command, 14, 4))));

    //get data
    $command = substr($command, 18);
    $this->arguments = $this->decode_data(&$command);

    return $this->arguments;

  } //function decode($command)

  //***************************************************************************
  //method: decode_data()
  //NOTE: float fehlt
  //***************************************************************************
  function decode_data($command)
  {
    $typ = $command[0];
    switch ($typ)
    {
      case CMD_TYPE_INT:
        $newdata = (int) hexdec(bin2hex(substr($command, 1, 4)));
        $command = substr($command, 5);
        break;
      case CMD_TYPE_FLOAT:
        $newdata = "type \"float\" not yet implemented";
        $command = substr($command, 5);
        break;
      case CMD_TYPE_STRING:
        $length = (string) hexdec(bin2hex(substr($command, 1, 4)));
        $newdata = substr($command, 5, $length);
        $command = substr($command, 5 + $length);
        break;
      case CMD_TYPE_OBJECT:
        $newdata = new steam_object(hexdec(bin2hex(substr($command, 1, 4))), hexdec(bin2hex(substr($command, 5, 4))));
        $command = substr($command, 9);
        break;
      case CMD_TYPE_ARRAY:
        $count = hexdec(bin2hex(substr($command, 1, 2)));
        $command = substr($command, 3);

        if($count <= 0)
          $newdata = array();
        else
          for($i = 0; $i < $count; $i++)
          {
            $value = $this->decode_data(&$command);
            $newdata[$i] = $value;
          }; //for($i = 0; $i < $count; $i++)

        break;
      case CMD_TYPE_MAPPING:
        $count = hexdec(bin2hex(substr($command, 1, 2)));
        $command = substr($command, 3);

        if($count <= 0)
          $newdata = array();
        else
          for($i = 0; $i < $count; $i++)
          {
            $key = $this->decode_data(&$command);
            $value = $this->decode_data(&$command);

            if(is_object($key) || is_array($key))
              $newdata[] = $value;
            else
              $newdata[$key] = $value;
          }; //for($i = 0; $i < $count; $i++)

        break;
      case CMD_TYPE_PROGRAM:
        $newdata = "type program not yet implemented";
        break;
      case CMD_TYPE_TIME:
        $newdata = (int) hexdec(bin2hex(substr($command, 1, 4)));
        $command = substr($command, 5);
        break;
      case CMD_TYPE_FUNCTION:
        $length = (string) hexdec(bin2hex(substr($command, 1, 4)));
        preg_match("/^\((.*)\(\)\:([0-9]*)\)/", substr($command, 5, $length), $newdata);
        if(sizeof($newdata) == 3)
        {
          array_shift($newdata);
          $newdata[1] = new steam_object($newdata[1]);
        }
        else
          $newdata = substr($command, 5, $length);
        $command = substr($command, 5 + $length);
        break;
      default:
        $newdata = "type \"default\" not yet implemented [Typ " . $typ . "] $command";
        echo("$newdata<br>\n");
        break;
    } //switch ($typ)

    return $newdata;

  } //function decode_data($command)


  //***************************************************************************
  //methods to set variable and encode (same time)
  //***************************************************************************

  function set_transactionid($transactionid)
  {
    $this->transactionid = $transactionid;
    $this->transactionid_encoded = pack("C*", $transactionid >> 24, $transactionid >> 16, $transactionid >> 8 , $transactionid);
  } //function set_transactionid($transactionid)

  function set_coalcommand($coalcommand)
  {
    $this->coalcommand = $coalcommand;
  } //function set_coalcommand($coalcommand)

  function set_object($object)
  {
    if($object == 0)
    {
      $this->object = new steam_object();
      $this->object_encoded = "\x00\x00\x00\x00\x00\x00\x00\x00";
    }
    else
    {
      $this->object = $object;
      $this->object_encoded =  pack("C*", $object->id >> 24, $object->id >> 16, $object->id >> 8, $object->id) .
                               pack("C*", $object->class >> 24, $object->class >> 16, $object->class >> 8, $object->class);
    }
  } //function set_object($object)

  function set_arguments($arguments)
  {
    $this->arguments = $arguments;
    $this->arguments_encoded = $this->encode_data($arguments);

    $this->length = strlen($this->arguments_encoded) + 18;
    $this->length_encoded = pack("C*", $this->length >> 24, $this->length >> 16, $this->length >> 8, $this->length);
  } //function set_arguments($arguments)


  //***************************************************************************
  //methods to get variable status
  //***************************************************************************
  function is_error() { return ($this->coalcommand == COAL_ERROR); }
  function access_granted() { return ($this->arguments[1] == "Access denied !");}

  function get_transactionid()
  {
    return $this->transactionid;
  } //function get_transactionid()

  function get_coalcommand()
  {
    return $this->coalcommand;
  } //function get_coalcommand()

  function get_object()
  {
    return $this->object;
  } //function get_object()

  function get_arguments()
  {
    return $this->arguments;
  } //function get_arguments()

}; //class steam_request

?>
