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
function invalidatedatacache($name,$withpath=true){
	global $datacache;
	if (getsetting("usedatacache",0)){
		if ($withpath) $fullname = makecachetempname($name);
			else $fullname=$name;
		if (file_exists($fullname)) unlink($fullname);
		if (!$withpath) unset($datacache[$name]);
	}
}


//Invalidates *all* caches, which contain $name at the beginning of their filename.
function massinvalidate($name="") {
	if (getsetting("usedatacache",0)){
		$name = DATACACHE_FILENAME_PREFIX.$name;
		global $datacachefilepath;
		if ($datacachefilepath=="")
			$datacachefilepath = getsetting("datacachepath","/tmp");
		$dir = dir($datacachefilepath);
		while(false !== ($file = $dir->read())) {
			if (strpos($file, $name) !== false) {
				invalidatedatacache($datacachefilepath."/".$file,false);
			}
		}
		$dir->close();
	}
}


function makecachetempname($name){
	//one place to sanitize names for data caches.
	global $datacache, $datacachefilepath,$checkedforolddatacaches;
	if ($datacachefilepath=="")
		$datacachefilepath = getsetting("datacachepath","/tmp");
	//let's make sure that someone can't trick us in to
	$name = rawurlencode($name);
	$name = str_replace("_","-",$name); //sanity measure
	$name = DATACACHE_FILENAME_PREFIX.preg_replace("'[^A-Za-z0-9.-]'","",$name);
	$fullname = $datacachefilepath."/".$name;
	//clean out double slashes (this also blocks file wrappers woot)
	$fullname = preg_replace("'//'","/",$fullname);
	$fullname = preg_replace("'\\\\'","\\",$fullname);


	if ($checkedforolddatacaches==false){
		$checkedforolddatacaches=true;
		// we want this to be 1 in 100 chance per page hit, not per data
		// cache call.
		// Once a hundred page hits, we want to clean out old caches.

		/* Add on from Nightborn
			I recommend running a cronjob. If you cache directory contains 20k files... and you have 6k users... that 100th hit is about ... 3 times A SECOND(!) ... 
			Cleaning up every hour is fine. 
			cronjob should execute a line like:
				for i in /MYTEMPDIR/*;do rm $i -f;done

		*/

//		if (mt_rand(1,100)<2){
//			$handle = opendir($datacachefilepath);
//			while (($file = readdir($handle)) !== false) {
//				if (substr($file,0,strlen(DATACACHE_FILENAME_PREFIX)) ==
//						DATACACHE_FILENAME_PREFIX){
//					$fn = $datacachefilepath."/".$file;
//					$fn = preg_replace("'//'","/",$fn);
//					$fn = preg_replace("'\\\\'","\\",$fn);
//					if (is_file($fn) &&
//							filemtime($fn) < strtotime("-24 hours")){
//						unlink($fn);
//					}else{
//					}
//				}
//			}
//		}
	}
	return $fullname;
}

?>
