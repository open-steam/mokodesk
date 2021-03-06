<?php
  /****************************************************************************
  derive_logoname.php - function to derive the filename of the server logo from a path name
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

  Author: Harald Selke
  EMail: hase@upb.de

  ****************************************************************************/

  function derive_logoname($path)
  {
    if (strpos($path, "schulen/gt") == 1 || strpos($path, "dialog/gt") == 1)
      return "logo_schulen-gt.gif";
    else
      return "bid_Logo_neu.gif";
  }

?>