<?php
// translator ready
// addnews ready
// mail ready
//This is a data caching library intended to lighten the load on lotgd.net
//use of this library is not recommended for most installations as it raises
//the issue of some race conditions which are mitigated on high volume
//sites but which could cause odd behavior on low volume sites, with out
//offering much if any advantage.

//basically the idea behind this library is to provide a non-blocking
//storage mechanism for non-critical data.

/* Add on from Nightborn 

* use of this is very well recommended as it cuts down database load to a minimum at the expense of doing more PHP file checking



*/

$datacache = array();
$datacachefilepath = "";
$checkedforolddatacaches = false;
define("DATACACHE_FILENAME_PREFIX","datacache_");

function datacache($name,$duration=60){
	global $datacache;
	if (getsetting("usedatacache",0)){
		if (isset($datacache[$name])){
			// we've already loaded this data cache this page hit and we
			// can simply return it.
			return $datacache[$name];
		}else{
			//we haven't loaded this data cache this page hit.
			$fullname = makecachetempname($name);
			if (file_exists($fullname) &&
					@filemtime($fullname) > strtotime("-$duration seconds")){
				//the cache file *does* exist, and is not overly old.
				//funny, without the @ in the filemtime it HAPPENED that the file_exists returned TRUE but the file already was deleted ;) great whoe to those milliseconds 
				$fullfile = @file_get_contents($fullname);
				if ($fullfile > ""){
					$datacache[$name] = @unserialize($fullfile);
					return $datacache[$name];
				}else{
					return false;
				}
			}
		}
	}
	// The field didn't exist, or it was too old.
	return false;
}

//do NOT send simply a false value in to array or it will bork datacache in to
//thinking that no data is cached or we are outside of the cache period.
function updatedatacache($name,$data){
	global $datacache;
	if (getsetting("usedatacache",0)){
		$fullname = makecachetempname($name);
		$datacache[$name] = $data; //serialize($array);
		$fp = fopen($fullname,"w");
		if ($fp){
			if (!fwrite($fp,serialize($data))){
			}else{
			}
			fclose($fp);
		}else{
		}
		return true;
	}
	//debug($datacache);
	return false;
}

//we want to be able to invalidate data caches when we know we've done
//something which would change the data.
function invalidatedatacache($name,$full=false){
	global $datacache;
	if (getsetting("usedatacache",0)){
		if(!$full) $fullname = makecachetempname($name);
		else $fullname = $name;
		if (file_exists($fullname)){
			// debug("Unlinking file ".$fullname);
			@unlink($fullname);
		} else {
			// debug("Cannot unlink file ".$fullname);
		}
		unset($datacache[$name]);
	}
}


//Invalidates *all* caches, which contain $name at the beginning of their filename.
function massinvalidate($name,$dir=false) {
	if (getsetting("usedatacache",0)){
		//$name = DATACACHE_FILENAME_PREFIX.$name;
		global $datacachefilepath;
		if ($datacachefilepath=="") $datacachefilepath = getsetting("datacachepath","/tmp");
		if ($dir){
			$datacachefilepath.="/".$dir;
		}
		$cachepath = dir($datacachefilepath);
		// debug("Trying to invalidate ".$name);
		while(false !== ($file = $cachepath->read())) {
			if (strpos($file, $name) !== false) {
				invalidatedatacache($cachepath->path."/".$file,true);
				// debug("Invalidated ".$file);
			}
		}
		$cachepath->close();
	}
}


function makecachetempname($name){
	//one place to sanitize names for data caches.
	global $datacache, $datacachefilepath,$checkedforolddatacaches;
	if ($datacachefilepath=="") $datacachefilepath = getsetting("datacachepath","/tmp");
	$path = pathinfo($name);
	if (!file_exists($datacachefilepath."/".$path['dirname'])){
		@mkdir($datacachefilepath."/".$path['dirname'],0777,1);
	}
	$fullname = $datacachefilepath."/".$name;
	$fullname = preg_replace("'//'","/",$fullname);
	$fullname = preg_replace("'\\\\'","\\",$fullname);
	if ($checkedforolddatacaches==false){
		$checkedforolddatacaches=true;
	}
	// echo($fullname);
	return $fullname;
}


function empty_datacache(){
	global $datacache, $datacachefilepath;
	recursive_remove_directory($datacachefilepath,true);
	unset($datacache);
}

//ganked shamelessly from lixlpixel.org, called during maintenance to empty the datacache
function recursive_remove_directory($directory, $empty=FALSE){
	// if the path has a slash at the end we remove it here
	if(substr($directory,-1) == '/')
	{
		$directory = substr($directory,0,-1);
	}

	// if the path is not valid or is not a directory ...
	if(!file_exists($directory) || !is_dir($directory))
	{
		// ... we return false and exit the function
		return FALSE;

	// ... if the path is not readable
	}elseif(!is_readable($directory))
	{
		// ... we return false and exit the function
		return FALSE;

	// ... else if the path is readable
	}else{

		// we open the directory
		$handle = opendir($directory);

		// and scan through the items inside
		while (FALSE !== ($item = readdir($handle)))
		{
			// if the filepointer is not the current directory
			// or the parent directory
			if($item != '.' && $item != '..')
			{
				// we build the new path to delete
				$path = $directory.'/'.$item;

				// if the new path is a directory
				if(is_dir($path)) 
				{
					// we call this function with the new path
					recursive_remove_directory($path);

				// if the new path is a file
				}else{
					// we remove the file
					unlink($path);
				}
			}
		}
		// close the directory
		closedir($handle);

		// if the option to empty is not set to true
		if($empty == FALSE)
		@rmdir($directory);
		return TRUE;
	}
}

?>
