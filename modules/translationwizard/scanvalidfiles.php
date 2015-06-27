<?php
/* This is mostly a copy of the source.php*/

function wizard_showvalidfiles($dosubmit=true,$onlymodules=0,$showselectbox=true,$mainmodulecheck=false) {
	global $coding;
	require_once("lib/errorhandling.php");
	$url="";
	$outputfiles=array();
	$dir = str_replace("\\","/",dirname($url)."/");
	$subdir = str_replace("\\","/",dirname($_SERVER['SCRIPT_NAME'])."/");
	if ($subdir == "//") $subdir = "/";
	switch ($onlymodules) {
		case 2: 
			$legal_start_dirs = array(
				$subdir."modules/*" => 1,
			);
			break;
		
		case 1:
			$legal_start_dirs = array(
				$subdir."modules/" => 1,
			);
			break;
		default:
		$legal_start_dirs = array(
			$subdir."" => 1,
			$subdir."lib/*" => 1,
			$subdir."modules/*" => 1,
		);
	}
	$illegal_files = array(
			($subdir=="//"?"/":$subdir)."dbconnect.php"=>"Contains sensitive information specific to this installation.",
	);
	$legal_files=array();

	// Gather all the legal dirs
	$legal_dirs = array();
	foreach ($legal_start_dirs as $dir=>$value) {
		// If this is a dir to exclude, skip it
		if (!$value) continue;
		
		$sdir = substr($dir,strlen($subdir));
		if ($sdir == dirname($_SERVER['SCRIPT_NAME'])) $sdir ="";
		$base = "./$sdir";
		// If this is not a 'recursive' dir, add it and continue
		if (!strstr($base, "/*")) {
			array_push($legal_dirs, $base);
			continue;
		}
		// Strip of the /*
		$base = substr($base, 0, -2);
		array_push($legal_dirs, $base . "/");
		$legal_dirs=array_merge($legal_dirs,wizard_tree($base));
	} //debug($legal_dirs);return;
	sort($legal_dirs);
	if ($dosubmit) $sub="onChange='this.form.submit()'";
	if ($mainmodulecheck) $sub="onChange='modulecheck()'";//modulecheck()'";
	if ($showselectbox) rawoutput("<select name='lookfor' $sub >");
	foreach ($legal_dirs as $key) {
		//$skey = substr($key,strlen($subdir));
		//if ($key==dirname($_SERVER['SCRIPT_NAME'])) $skey="";
		//$d = dir("./$skey");
		//if (substr($key,0,2)=="//") $key = substr($key,1);
		//if ($key=="//") $key="/";
		// Gaurentee a sort order on source files - Hidehisa Yasuda
		$key1 = substr($key, 2);
		$key2 = "/" . $key1;
		//$skey = "//" . $key1;
		$d = dir("$key");
		$files = array();
		if ($onlymodules==2) {
				$check=$subdir.str_replace(array("./"),array(""),$key)."*";
				//debug($check);debug($legal_start_dirs);
			if (array_key_exists($check,$legal_start_dirs)) continue; //go on if you only want subfolders of modules
		}
		while ($entry = $d->read()) {
			if (substr($entry,strrpos($entry,"."))==".php"){
				array_push($files, "$entry");
			}
		}
		$d->close();
		asort($files);
		foreach($files as $entry) {
			if (isset($illegal_files["$key2$entry"]) &&
					$illegal_files["$key2$entry"]!=""){
				if ($illegal_files["$key2$entry"]=="X"){
					//we're hiding the file completely.
				}else{
					if ($showselectbox) rawoutput("<li>$key1$entry");
					$reason = translate_inline($illegal_files[$key2 . $entry]);
					if ($showselectbox) output("&#151; This file cannot be viewed: %s", $reason, true);
					if ($showselectbox)rawoutput("</li>\n");
				}
			}else{
				$namesp=$key1.$entry;
				array_push($outputfiles,$namesp);
				if ($showselectbox) rawoutput("<option value='".htmlentities($namesp,ENT_COMPAT,$coding)."' $selected>".htmlentities($namesp,ENT_COMPAT,$coding)."</option>");
				$legal_files["$key2$entry"]=true;
			}
		}
	}
	if ($showselectbox) {
		rawoutput("</select>");
		rawoutput("<input type='submit' class='button' name='dummy' value='". translate_inline("Start Scan") ."'>");
	}
	return $outputfiles;
}

function wizard_tree($base) {
	$d = dir("$base");
	$back=array();
	while($entry = $d->read()) {
		// Skip any . files
		if ($entry[0] == '.') continue;
		// skip any php files
		if (substr($entry,strrpos($entry, '.')) == ".php") continue;
		$ndir = $base . "/" . $entry;
		// Okay, check if it's a directory
		$test = preg_replace("!^\./!", "//", $ndir);
		if (is_dir($ndir)) {
			$back=array_merge($back,wizard_tree($ndir));
			array_push($back, $ndir . "/");
		}
	}
	return $back;
}
?>
