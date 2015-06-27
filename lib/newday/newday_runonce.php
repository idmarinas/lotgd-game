<?php
//newday runonce
	//Let's do a new day operation that will only fire off for
	//one user on the whole server.
	//run the hook.
	modulehook('newday-runonce',array());

	//Do some high-load-cleanup

	//Moved from lib/datacache.php
	if (getsetting('usedatacache',0)){
		$handle = opendir($datacachefilepath);
		while (($file = readdir($handle)) !== false) {
			if (substr($file,0,strlen(DATACACHE_FILENAME_PREFIX)) ==
					DATACACHE_FILENAME_PREFIX){
				$fn = $datacachefilepath.'/'.$file;
				$fn = preg_replace("'//'","/",$fn);
				$fn = preg_replace("'\\\\'","\\",$fn);
				if (is_file($fn) &&
						filemtime($fn) < strtotime('-24 hours')){
				unlink($fn);
			}else{
			}
			}
		}
	}

	//only if not done by cron
	if (!getsetting('newdaycron',0)) {

		require_once("lib/newday/dbcleanup.php");
		require("lib/newday/commentcleanup.php");
		require("lib/newday/charcleanup.php");


	}

?>
