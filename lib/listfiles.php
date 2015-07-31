<?php
function list_files($ruta){
	global $sort;
	//abrir un directorio y listarlo recursivo 
 	if (is_dir($ruta)) { 
		if ($dh = opendir($ruta)) { 
			while (($file = readdir($dh)) !== false) { 
				if (is_dir($ruta ."/". $file) && $file!="." && $file!=".."){
			    	list_files($ruta ."/". $file);
			    }
				else
				{
					$names=explode(".",$file);
					if (isset($names[1]) && $names[1]=="php") {
						//sorting
						$sort[]=",".$ruta."/".$names[0].",".$ruta."/".$names[0];
					}
				}
			} 
			closedir($dh); 
		} 
	}
}