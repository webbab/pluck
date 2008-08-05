<?php
/* 
 * This file is part of pluck, the easy content management system
 * Copyright (c) somp (www.somp.nl)
 * http://www.pluck-cms.org
 * Pluck is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 
 * See docs/COPYING for the complete license.
*/

//Make sure the file isn't accessed directly
if((!ereg("index.php", $_SERVER['SCRIPT_FILENAME'])) && (!ereg("admin.php", $_SERVER['SCRIPT_FILENAME'])) && (!ereg("install.php", $_SERVER['SCRIPT_FILENAME'])) && (!ereg("login.php", $_SERVER['SCRIPT_FILENAME']))){
    //Give out an "access denied" error
    echo "access denied";
    //Block all other code
    exit();
}

//Function: check if module is compatible
//------------
function module_is_compatible($module) {
	//Include pluck version
	include("data/inc/pluck_info.php");
	//Include module information
	if (file_exists("data/modules/$module/module_info.php")) {
		include("data/modules/$module/module_info.php");
	}

	if (isset($module_compatibility)) {
		if (preg_match("/,/", $module_compatibility)) {
			$version_compat = explode(",",$module_compatibility);
		}
		else {
			$version_compat[0] = $module_compatibility;
		}
		//Now check if we have an incompatible version
		foreach ($version_compat as $number => $version) {
			if ($version == $pluck_version) {
				$compatible = "yes";
			}
		}
	}

	if ($compatible == "yes") {
		return TRUE;
	}
	else {
		return FALSE;
	}
	unset($compatible);
}

//Function: recursively delete an entire directory
//------------
function recursive_remove_directory($directory, $empty=FALSE)	{
	if(substr($directory,-1) == '/') {
		$directory = substr($directory,0,-1);
	}
	if(!file_exists($directory) || !is_dir($directory)) {
		return FALSE;
	}
	elseif(is_readable($directory)) {
		$handle = opendir($directory);

		while (FALSE !== ($item = readdir($handle))) {
			if($item != '.' && $item != '..') {
				$path = $directory.'/'.$item;
            if(is_dir($path)) {
					recursive_remove_directory($path);
				}
				else {
					unlink($path);
				}
			}
		}
		closedir($handle);
		if($empty == FALSE) {
			if(!rmdir($directory)) {
				return FALSE;
			}
		}
	}
	return TRUE;
}

//Function: get site title
//---------------------------------
function get_sitetitle() {
	include("data/settings/options.php");
	return $sitetitle;
}

?>