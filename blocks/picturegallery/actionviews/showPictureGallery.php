<?php
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT  - DO NOT REMOVE THIS NOTICE                      //
//                                                                       //
// OPENTADKA FRAMEWORK											         //
//          http://www.opentadka.org                                     //
//                                                                       //
// Copyright (C) 2010 onwards  Manu Sharma  http://www.opentadka.org     //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
//////////////////////////////////////////////////////////////////////////
?>

<div id="gallery">
					<?php
					// Define PlaceHolders
					$filename_placeholder = '';
					$filedescription_placeholder = '';
					// Get Image Slider File Data
					$columns = array('id','filename','filedescription');
					$conditions = array();
					$conditions['=']['isactive'] = 1;
					$conditions['AND =']['filelocationid'] = 2;

					$sqlObj = new MainSQL();
					$sqlfilecontents = $sqlObj->SQLCreator('S', 'fileupload', $columns, $conditions, '', '', '');
					if($resultfilecontents = $sqlObj->FireSQL($sqlfilecontents)){
					if($sqlObj->getNumRows($resultfilecontents) !=0){ // If file Exists
					while($resultsetfilecontents = $sqlObj->FetchResult($resultfilecontents)){
					$filename_placeholder = $sqlObj->getCleanData($resultsetfilecontents->filename);
					$filedescription_placeholder = $sqlObj->getCleanData($resultsetfilecontents->filedescription);
					?>
                    <a rel="gallery" href="<?php echo PROJ_DATA_WWW_DIR._WS.'pictures'._WS.$filename_placeholder; ?>" title="<?php echo $filedescription_placeholder; ?>"><img width="100px" height="100px" src="<?php echo PROJ_DATA_WWW_DIR._WS.'pictures'._WS.$filename_placeholder; ?>" alt="<?php echo $filedescription_placeholder; ?>" /></a>
					<?php
					}
					}
					}
					?>
</div>