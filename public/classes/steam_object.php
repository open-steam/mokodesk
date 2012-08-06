<?php
  /****************************************************************************
  steam_object.php - a simple steam object
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

class steam_object
{
  //***************************************************************************
  //class variables
  //***************************************************************************
  var $id;
  var $class;


  //***************************************************************************
  //construtor: steam_object($id, $class)
  //***************************************************************************
  function steam_object($id = "0", $class = "0")
  {
    $this->id = $id;
    $this->class = $class;
  } //function steam_object(id, $class)

  //***************************************************************************
  //methods to get variable status
  //***************************************************************************
  function is_object() { return ($this->dec2bin($this->class) & CLASS_OBJECT) == CLASS_OBJECT; }
  function is_container() { return ($this->dec2bin($this->class) & CLASS_CONTAINER) == CLASS_CONTAINER; }
  function is_room() { return ($this->dec2bin($this->class) & CLASS_ROOM) == CLASS_ROOM; }
  function is_user() { return ($this->dec2bin($this->class) & CLASS_USER) == CLASS_USER; }
  function is_document() { return ($this->dec2bin($this->class) & CLASS_DOCUMENT) == CLASS_DOCUMENT; }
  function is_link() { return ($this->dec2bin($this->class) & CLASS_LINK) == CLASS_LINK; }
  function is_group() { return ($this->dec2bin($this->class) & CLASS_GROUP) == CLASS_GROUP; }
  function is_exit() { return ($this->dec2bin($this->class) & CLASS_EXIT) == CLASS_EXIT; }
  function is_docextern() { return ($this->dec2bin($this->class) & CLASS_DOCEXTERN) == CLASS_DOCEXTERN; }
  function is_doclpc() { return ($this->dec2bin($this->class) & CLASS_DOCLPC) == CLASS_DOCLPC; }
  function is_script() { return ($this->dec2bin($this->class) & CLASS_SCRIPT) == CLASS_SCRIPT; }
  function is_dochtml() { return ($this->dec2bin($this->class) & CLASS_DOCHTML) == CLASS_DOCHTML; }
  function is_date() { return ($this->dec2bin($this->class) & CLASS_DATE) == CLASS_DATE; }
  function is_factory() { return ($this->dec2bin($this->class) & CLASS_FACTORY) == CLASS_FACTORY; }
  function is_module() { return ($this->dec2bin($this->class) & CLASS_MODULE) == CLASS_MODULE; }
  function is_database() { return ($this->dec2bin($this->class) & CLASS_DATABASE) == CLASS_DATABASE; }
  function is_package() { return ($this->dec2bin($this->class) & CLASS_PACKAGE) == CLASS_PACKAGE; }
  function is_image() { return ($this->dec2bin($this->class) & CLASS_IMAGE) == CLASS_IMAGE; }
  function is_messageboard() { return ($this->dec2bin($this->class) & CLASS_MESSAGEBOARD) == CLASS_MESSAGEBOARD; }
  function is_ghost() { return ($this->dec2bin($this->class) & CLASS_GHOST) == CLASS_GHOST; }
  function is_servergate() { return ($this->dec2bin($this->class) & CLASS_SERVERGATE) == CLASS_SERVERGATE; }
  function is_trashbin() { return ($this->dec2bin($this->class) & CLASS_TRASHBIN) == CLASS_TRASHBIN; }
  function is_docxml() { return ($this->dec2bin($this->class) & CLASS_DOCXML) == CLASS_DOCXML; }
  function is_docxsl() { return ($this->dec2bin($this->class) & CLASS_DOCXSL) == CLASS_DOCXSL; }
  function is_lab() { return ($this->dec2bin($this->class) & CLASS_LAB) == CLASS_LAB; }
  function is_docwiki() { return ($this->dec2bin($this->class) & CLASS_DOCWIKI) == CLASS_DOCWIKI; }
  function is_bug() { return ($this->dec2bin($this->class) & CLASS_BUG) == CLASS_BUG; }
  function is_calendar() { return ($this->dec2bin($this->class) & CLASS_CALENDAR) == CLASS_CALENDAR; }
  function is_scorm() { return ($this->dec2bin($this->class) & CLASS_SCORM) == CLASS_SCORM; }
  function is_drawing() { return ($this->dec2bin($this->class) & CLASS_DRAWING) == CLASS_DRAWING; }

  //***************************************************************************
  //method: dec2bin()
  //***************************************************************************
  function dec2bin($data)
  {
    return pack("C*", $data >> 24, $data >> 16, $data >> 8 , $data);
  } //function dec2bin($data)

}; //class steam_object

?>
