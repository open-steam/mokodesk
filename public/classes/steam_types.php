<?php
  /****************************************************************************
  steam_types.php - all types for COAL protocol / all types of CLASS definition / all types of Serialization
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

  //define COAL command
  define("COAL_QUERY_COMMANDS", "\x00");
  define("COAL_COMMAND",        "\x01");
  define("COAL_EVENT",          "\x02");
  define("COAL_LOGIN",          "\x03");
  define("COAL_LOGOUT",         "\x04");
  define("COAL_FILE_DOWNLOAD",  "\x05");
  define("COAL_FILE_UPLOAD",    "\x06");
  define("COAL_QUERY_PROGRAMS", "\x07");
  define("COAL_ERROR",          "\x08");
  define("COAL_SET_CLIENT",     "\x09");
  define("COAL_UPLOAD_START",   "\x0a");
  define("COAL_UPLOAD_PACKAGE", "\x0b");
  define("COAL_CRYPT",          "\x0c");


  //define CLASS ids
  define("CLASS_OBJECT",       "\x00\x00\x00\x01");
  define("CLASS_CONTAINER",    "\x00\x00\x00\x02");
  define("CLASS_ROOM",         "\x00\x00\x00\x04");
  define("CLASS_USER",         "\x00\x00\x00\x08");
  define("CLASS_DOCUMENT",     "\x00\x00\x00\x10");
  define("CLASS_LINK",         "\x00\x00\x00\x20");
  define("CLASS_GROUP",        "\x00\x00\x00\x40");
  define("CLASS_EXIT",         "\x00\x00\x00\x80");
  define("CLASS_DOCEXTERN",    "\x00\x00\x01\x00");
  define("CLASS_DOCLPC",       "\x00\x00\x02\x00");
  define("CLASS_SCRIPT",       "\x00\x00\x04\x00");
  define("CLASS_DOCHTML",      "\x00\x00\x08\x00");
  define("CLASS_DATE",         "\x00\x00\x10\x00");
  define("CLASS_FACTORY",      "\x00\x00\x20\x00");
  define("CLASS_MODULE",       "\x00\x00\x40\x00");
  define("CLASS_DATABASE",     "\x00\x00\x80\x00");
  define("CLASS_PACKAGE",      "\x00\x01\x00\x00");
  define("CLASS_IMAGE",        "\x00\x02\x00\x00");
  define("CLASS_MESSAGEBOARD", "\x00\x04\x00\x00");
  define("CLASS_GHOST",        "\x00\x08\x00\x00");
  define("CLASS_SERVERGATE",   "\x00\x10\x00\x00");
  define("CLASS_TRASHBIN",     "\x00\x20\x00\x00");
  define("CLASS_DOCXML",       "\x00\x40\x00\x00");
  define("CLASS_DOCXSL",       "\x00\x80\x00\x00");
  define("CLASS_LAB",          "\x01\x00\x00\x00");
  define("CLASS_DOCWIKI",      "\x02\x00\x00\x00");
  define("CLASS_BUG",          "\x04\x00\x00\x00");
  define("CLASS_CALENDAR",     "\x08\x00\x00\x00");
  define("CLASS_SCORM",        "\x10\x00\x00\x00");
  define("CLASS_DRAWING",      "\x20\x00\x00\x00");
  define("CLASS_AGENT",        "\x20\x00\x00\x00");


  //define serialization types
  define("CMD_TYPE_UNKNOWN",   "\x00");
  define("CMD_TYPE_INT",       "\x01");
  define("CMD_TYPE_FLOAT",     "\x02");
  define("CMD_TYPE_STRING",    "\x03");
  define("CMD_TYPE_OBJECT",    "\x04");
  define("CMD_TYPE_ARRAY",     "\x05");
  define("CMD_TYPE_MAPPING",   "\x06");
  define("CMD_TYPE_MAP_ENTRY", "\x07");
  define("CMD_TYPE_PROGRAM",   "\x08");
  define("CMD_TYPE_TIME",      "\x09");
  define("CMD_TYPE_FUNCTION",  "\x0a");


  //define access types
  define("FAILURE",             "0xffffffff");
  define("ACCESS_DENIED",       "0x00000000");
  define("ACCESS_GRANTED",      "0x00000001");
  define("ACCESS_BLOCKED",      "0x00000002");
  define("SANCTION_READ",       "0x00000001");
  define("SANCTION_EXECUTE",    "0x00000002");
  define("SANCTION_MOVE",       "0x00000004");
  define("SANCTION_WRITE",      "0x00000008");
  define("SANCTION_INSERT",     "0x00000010");
  define("SANCTION_ANNOTATE",   "0x00000020");
  define("SANCTION_SANCTION",   "0x00000100");
  define("SANCTION_LOCAL",      "0x00000200");
  define("SANCTION_ALL",        "0x0000ffff");
  define("SANCTION_SHIFT_DENY", "0x00000010");
  define("SANCTION_COMPLETE",   "0xffffffff");
  define("SANCTION_POSITIVE",   "0xffff0000");
  define("SANCTION_NEGATIVE",   "0x0000ffff");
  define("SANCTION_USER",       "0x00010000");

?>