<?php

  /****************************************************************************
  mimetype_map.php - mapping for filename tails and mimetypes
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

  Modifications by hase, 18.05.2005:
  Added several MIME types
  ****************************************************************************/

  $mimetype_map = array(
    ".3dmf" => "x-world/x-3dmf",                    //QuickDraw3D scene data (Apple)
    ".abs" => "audio/x-mpeg",                       //MPEG audio
    ".ai" => "application/postcript",               //PostScript
    ".aif" => "audio/x-aiff",                       //Macintosh audio format (AIpple)
    ".aifc" => "audio/x-aiff",                      //Macintosh audio format (AIpple)
    ".aiff" => "audio/x-aiff",                      //Macintosh audio format (AIpple)
    ".ano" => "application/x-annotator",            //Iris Annotator data
    ".asn" => "application/astound",                //Astound Web Player multimedia data (GoldDisk)
    ".asp" => "application/x-asap",                 //ASAP WordPower (Software Publishing Corp.)
    ".au" => "audio/basic",                         //"basic"audio - 8-bit u-law PCM
    ".avi" => "video/x-msvideo",                    //Microsoft video
    ".axs" => "application/x-olescript",            //OLE script e.g. Visual Basic (Ncompass)
    ".bcpio" => "application/x-bcpio",              //Old CPIO format
    ".bin" => "application/octet-stream",           //Binary, UUencoded
    ".bmp" => "image/x-ms-bmp",                     //Microsoft Windows bitmap
    ".c" => "text/plain",                           //Plain text: documents; program listings
    ".cal" => "image/x-cals",                       //CALS Type 1 or 2
    ".cc" => "text/plain",                          //Plain text: documents; program listings
    ".ccv" => "application/ccv",                    //Carbon Copy - remote control/access (Microcom)
    ".cdr" => "application/x-coreldraw",            //CorelDraw
    ".cgm" => "image/cgm",                          //Computer Graphics Metafile
    ".cmx" => "image/x-cmx",                        //CMX vector image (Corel)
    ".cpio" => "application/x-cpio",                //POSIX CPIO format
    ".cpp" => "text/plain",                         //Plain text: documents; program listings
    ".csh" => "application/x-csh",                  //UNIX c-shell program
    ".css" => "text/css",                           //Cascading Stylesheets
    ".dir" => "application/x-dirview",              //Directory Viewer
    ".doc" => "application/msword",                 //Windows Word Documents
    ".dvi" => "application/x-dvi",                  //TeX dvi format
    ".dwf" => "drawing/x-dwf",                      //Autocad WHIP vector drawings
    ".emm" => "application/mjet-mm",                //Mindmanager Mindjet
    ".eps" => "application/postcript",              //PostScript
    ".es" => "audio/echospeech",                    //compressed speech (Echo Speech Corp.)
    ".evy" => "application/envoy",                  //Envoy Document
    ".exe" => "application/octet-stream",           //PC executable
    ".dxf" => "image/x-dxf",                        //AutoCad DXF file (SoftSource)
    ".dwg" => "image/x-dwg",                        //AutoCad Drawing (SoftSource)
    ".dsf" => "image/x-mgx-dsf",                    //QuickSilver active image (Micrografx)
    ".faxmgr" => "application/x-faxmanager",        //Fax manager file
    ".faxmgrjob" => "application/x-faxmanager-job", //Fax job data file
    ".fif" => "application/fractals",               //Fractal Image Format
    ".fm" => "application/x-framemaker",            //FrameMaker Documents (Frame)
    ".frame" => "application/x-framemaker",         //FrameMaker Documents (Frame)
    ".frm" => "application/x-framemaker",           //FrameMaker Documents (Frame)
    ".g3f" => "image/g3fax",                        //Group III Fax (RFC 1494)
    ".gif" => "image/gif",                          //Comupserver GIF
    ".gtar" => "application/x-gtar",                //Gnu tar format
    ".h" => "text/plain",                           //Plain text: documents; program listings
    ".hdf" => "application/hdf",                    //NCSA HDF data format
    ".htm" => "text/html",                          //HTML text data (RFC 1866)
    ".html" => "text/html",                         //HTML text data (RFC 1866)
    ".hqx" => "application/mac-binhex40",           //Macintosh Binhexed archive
    ".icnbk" => "application/x-iconbook",           //IconBook data
    ".ief" => "image/ief",                          //Image Exchange Format (RFC 1314)
    ".igs" => "application/iges",                   //IGES models -- CAD/CAM (CGM) data
    ".ins" => "application/x-insight",              //Insight Manual pages
    ".insight" => "application/x-insight",          //Insight Manual pages
    ".inst" => "application/x-install",             //Installable software in 'inst' format
    ".iv" => "graphics/x-inventor",                 //Open Inventor 3-D scenes
    ".jpg" => "image/jpg",                          //JPEG
    ".jpg" => "image/jpeg",                         //JPEG
    ".jpe" => "image/jpeg",                         //JPEG
    ".js" => "text/javascript",                     //Javascript program
    ".kml" => "application/vnd.google-earth.kml+xml",  //Google KML file
    ".kmz" => "application/vnd.google-earth.kmz",   //Google KMZ file
    ".latex" => "application/x-latex",              //LaTeX document
    ".lic" => "application/x-enterlicense",         //Software License
    ".lcc" => "application/fastman",                //RapidTransit compressed audio (Fast Man)
    ".ls" => "text/javascript",                     //Javascript program
    ".ma" => "application/mathematica",             //Mathematica notebook
    ".mail" => "application/x-mailfolder",          //Mail folder
    ".man" => "application/x-troff-man",            //Troff document with MAN macros
    ".mbd" => "application/mbedlet",                //mBED multimedia data (mBED)
    ".me" => "application/x-troff-me",              //Troff document with ME macros
    ".mif" => "application/x-mif",                  //Maker Interchange Format (FrameMaker)
    ".mil" => "image/x-cals",                       //CALS Type 1 or 2
    ".mmid" => "x-music/x-midi",                    //MIDI music data
    ".mmp" => "application/mjet-mm",                //Mindmanager Mindjet
    ".mocha" => "text/javascript",                  //Javascript program
    ".mov" => "video/quicktime",                    //Macintosh Quicktime
    ".movie" => "video/x-sgi-video",                //SGI Movie format
    ".mp2a" => "audio/x-mpeg-2",                    //MPEG-2 audio
    ".mp2v" => "video/mpeg-2",                      //MPEG-2 video
    ".mp3" => "audio/mpeg",                         //MP3 audio
    ".mp4" => "video/mpeg",                         //MPEG-4 video
    ".mpa" => "video/mpeg",                         //MPEG audio
    ".mpa2" => "audio/x-mpeg-2",                    //MPEG-2 audio
    ".mpe" => "video/mpeg",                         //MPEG video
    ".mpeg" => "video/mpeg",                        //MPEG video
    ".mpega" => "audio/x-mpeg",                     //MPEG audio
    ".mpg" => "video/mpeg",                         //MPEG video
    ".mpv2" => "video/mpeg",                        //MPEG-2 video
    ".ms" => "application/x-troff-ms",              //Troff document with MS macros
    ".msh" => "x-model/x-mesh",                     //Computational meshes for numerical simulations
    ".oda" => "application/oda",                    //Office Document Architecture
    ".ods" => "application/x-oleobject",            //OLE Object (Microsoft/NCompass)
    ".opp" => "x-form/x-openspace",                 //OpenScape OLE/OCX objects (Business@Web)
    ".p3d" => "application/x-p3d",                  //Play3D 3d scene data (Play3D)
    ".pbm" => "image/x-portable-bitmap",            //PBM (UNIX PPM package)
    ".pcb" => "image/x-photo-cd",                   //Kodak Photo-CD
    ".pcn" => "application/x-pcn",                  //Pointcast news data (Pointcast)
    ".pdf" => "application/pdf",                    //Adobe Acrobat PDF
    ".pgm" => "image/x-portable-graymap",           //PGM (UNIX PPM package)
    ".pict" => "image/x-pict",                      //Macintosh PICT format
    ".pl" => "application/x-perl",                  //Perl program
    ".png" => "image/png",                          //Portable Network Graphics
    ".pnm" => "image/x-portable-anymap",            //PNM (UNIX PPM package)
    ".pp" => "application/x-ppages",                //?
    ".ppages" => "application/x-ppages",            //?
    ".ppm" => "image/x-portable-pixmap",            //PPM (UNIX PPM package)
    ".ppt" => "application/ms-powerpoint",          //PowerPoint (Microsoft)
    ".pps" => "application/ms-powerpoint",          //PowerPoint (Microsoft)
    ".ppz" => "application/ms-powerpoint",          //PowerPoint presentation (Microsoft)
    ".ps" => "application/postcript",               //PostScript
    ".qt" => "video/quicktime",                     //Macintosh Quicktime
    ".ra" => "application/x-pn-audioman",           //Realaudio (Progressive Networks)
    ".rad" => "application/x-rad-powermedia",       //PowerMedia multimedia (RadMedia)
    ".ram" => "application/x-pn-audioman",          //Realaudio (Progressive Networks)
    ".ras" => "image/x-cmu-raster",                 //CMU raster
    ".rgb" => "image/rgb",                          //RGB
    ".roff" => "application/x-troff",               //Troff document
    ".rtf" => "application/rtf",                    //Microsoft Rich Text Format
    ".sc" => "application/x-showcase",              //Showcase Presentations
    ".sea" => "application/x-stuffit",              //Macintosh Stuffit Archive
    ".sgi-lpr" => "application/x-sgi-lpr",          //Data for printer (via lpr)
    ".sh" => "application/x-sh",                    //UNIX bourne shell program
    ".shar" => "application/x-shar",                //UNIX sh shell archive
    ".sho" => "application/x-showcase",             //Showcase Presentations
    ".show" => "application/x-showcase",            //Showcase Presentations
    ".showcase" => "application/x-showcase",        //Showcase Presentations
    ".sit" => "application/x-stuffit",              //Macintosh Stuffit Archive
    ".skp" => "application/x-koan",                 //Koan music data (SSeyo)
    ".slides" => "application/x-showcase",          //Showcase Presentations
    ".snd" => "audio/basic",                        //"basic"audio - 8-bit u-law PCM
    ".spl" => "application/futuresplash",           //FutureSplash vector animation (FutureWave)
    ".src" => "application/x-wais-source",          //WAIS "sources"
    ".swf" => "application/x-shockwave-flash",      //Shockwave Flash
    ".svd" => "application/vnd.svd",                //SourceView document (Dataware Electronics)
    ".svf" => "vector/x-svf",                       //Simple Vector Format (SoftSource)
    ".svg" => "image/svg+xml",                      //Scalable Vector Graphics
    ".svr" => "x-world/x-svr",                      //Viscape Interactive 3d world data (Superscape)
    ".sxc" => "application/vnd.sun.xml.calc",       //Staroffice XML Calc
    ".sxd" => "application/vnd.sun.xml.draw",       //Staroffice XML Calc
    ".sxi" => "application/vnd.sun.xml.impress",    //Staroffice XML Impress
    ".sxw" => "application/vnd.sun.xml.writer",     //Staroffice XML Writer
    ".t" => "application/x-troff",                  //Troff document
    ".talk" => "text/x-speech",                     //Speech synthesis data (MVP Solutions)
    ".tar" => "application/x-tar",                  //4.3BSD tar format
    ".tardist" => "application/x-tardist",          //Software in 'tardist' format
    ".tcl" => "application/x-tcl",                  //Tcl (Tool Control Language) program
    ".tex" => "application/x-tex",                  //Tex/LaTeX document
    ".texi" => "application/x-texinfo",             //GNU TexInfo document
    ".texinfo" => "application/x-texinfo",          //GNU TexInfo document
    ".tif" => "image/tiff",                         //TIFF
    ".tiff" => "image/tiff",                        //TIFF
    ".tr" => "application/x-troff",                 //Troff document
    ".txt" => "text/plain",                         //Plain text: documents; program listings
    ".ustar" => "application/x-ustar",              //POSIX tar format
    ".uu" => "application/octet-stream",            //Binary, UUencoded
    ".v5d" => "application/vis5d",                  //Vis5D 5-dimensional data
    ".vdo" => "video/vdo",                          //VDOlive streaming video (VDOnet)
    ".vi" => "application/x-robolab",               //Robolab files (Lego)
    ".viv" => "video/vivo",                         //Vivo streaming video (Vivo software)
    ".vsd" => "application/vnd.visio",              //Microsoft Visio
    ".vts" => "application/formulaone",             //Spreadsheets (Visual Components)
    ".vox" => "audio/voxware",                      //Toolvox speech audio (Voxware)
    ".vrml" => "x-world/x-vrml",                    //VRML data file
    ".vrw" => "x-world/x-vream",                    //WIRL - VRML data (VREAM)
    ".wav" => "audio/x-wav",                        //Microsoft audio
    ".wb" => "application/x-inpview",               //?
    ".wba" => "application/x-webbasic",             //Visual Basic objects (Amara)
    ".wfx" => "x-script/x-wfxclient",               //client-server objects (Wayfarer Communications)
    ".wi" => "image/wavelet",                       //Wavelet-compressed (Summus)
    ".wkz" => "application/x-wingz",                //WingZ spreadsheet
    ".wmv" => "video/x-ms-wmv",                     //Microsoft Windows Media Video
    ".wrl" => "x-world/x-vrml",                     //VRML data file
    ".wsrc" => "application/x-wais-source",         //WAIS "sources"
    ".wvr" => "x-world/wvr",                        //WebActive 3d data (Plastic Thought)
    ".xbm" => "image/x-xbitmap",                    //X-Windows bitmap (b/w)
    ".xls" => "application/ms-excel",               //Microsoft Excel
    ".xlt" => "application/ms-excel",               //Microsoft Excel (Mustervorlage)
    ".xml" => "text/xml",                           //XML text data
    ".xpm" => "image/x-xpixmap",                    //X-Windows pixelmap (8-bit color)
    ".xwd" => "image/x-xwindowdump",                //X Windowdump format
    ".zip" => "application/zip",                    //DOS/PC - Pkzipped archive
    ".ztardist" => "application/ztardist"           //Software in compressed 'tardist' format
  );
?>