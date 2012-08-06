<?php
  /****************************************************************************
  steam_attributes - sTeam V1.4.php - all types of attributes (deprecated since sTeam V1.5)
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

  define("OBJ_OWNER",           101);
  define("OBJ_NAME",            102);
  define("OBJ_DESC",            104);
  define("OBJ_ICON",            105);
  define("OBJ_KEYWORDS",        111);
  define("OBJ_COMMAND_MAP",     112); // objects that can be executed with this
  define("OBJ_POSITION_X",      113);
  define("OBJ_POSITION_Y",      114);
  define("OBJ_POSITION_Z",      115);
  define("OBJ_LAST_CHANGED",    116);
  define("OBJ_CREATION_TIME",   119);

  define("DOC_TYPE",            207);
  define("DOC_MIME_TYPE",       208);
  define("DOC_USER_MODIFIED",   213);
  define("DOC_LAST_MODIFIED",   214);
  define("DOC_LAST_ACCESSED",   215);
  define("DOC_EXTERN_URL",      216);
  define("DOC_TIMES_READ",      217);
  define("DOC_IMAGE_ROTATION",  218);
  define("DOC_IMAGE_THUMBNAIL", 219);
  define("DOC_IMAGE_SIZEX",     220);
  define("DOC_IMAGE_SIZEY",     221);

  define("CONT_SIZE_X",         300);
  define("CONT_SIZE_Y",         301);
  define("CONT_SIZE_Z",         302);

  define("EXIT_TO",             401);

  define("GROUP_MEMBERSHIP_REQS", 500);
  define("GROUP_EXITS",           501);

  define("USER_ADRESS",         611);
  define("USER_FULLNAME",       612);
  define("USER_MAILBOX",        613);
  define("USER_WORKROOM",       614);
  define("USER_LAST_LOGIN",     615);
  define("USER_EMAIL",          616);
  define("USER_UMASK",          617);
  define("USER_MODE",           618);
  define("USER_MODE_MSG",       619);
  define("USER_LOGOUT_PLACE",   620);
  define("USER_TRASHBIN",       621);
  define("USER_BOOKMARKROOM",   622);

  define("DRAWING_TYPE",        700);
  define("DRAWING_WIDTH",       701);
  define("DRAWING_HEIGHT",      702);
  define("DRAWING_COLOR",       703);
  define("DRAWING_THICKNESS",   704);
  define("DRAWING_FILLED",      705);

  define("GROUP_WORKROOM",      800);

  define("LINK_TARGET",         900);


  define("CONTROL_ATTR_USER",   1);
  define("CONTROL_ATTR_CLIENT", 2);
  define("CONTROL_ATTR_SERVER", 3);

  define("DRAWING_LINE",        1);
  define("DRAWING_RECTANGLE",   2);
  define("DRAWING_TRIANGLE",    3);
  define("DRAWING_POLYGON",     4);
  define("DRAWING_CONNECTOR",   5);
  define("DRAWING_CIRCLE",      6);
  define("DRAWING_TEXT",        7);

  define("REGISTERED_TYPE",     0);
  define("REGISTERED_DESC",     1);
  define("REGISTERED_EVENT_READ",  2);
  define("REGISTERED_EVENT_WRITE", 3);
  define("REGISTERED_ACQUIRE",  4);
  define("REGISTERED_CONTROL",  5);
  define("REGISTERED_DEFAULT",  6);

  define("REG_ACQ_ENVIRONMENT", 1);
  define("CLASS_ANY",           0); // for packages and registering attributes

  //login feature
  define("CLIENT_STATUS_CONNECTED", 1);
?>