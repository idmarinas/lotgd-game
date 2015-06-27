<?php
// addnews ready
// translator ready
// mail ready
function arraytourl($array){
	//takes an array and encodes it in key=val&key=val form.
	reset($array);
	$url="";
	$i=0;
	foreach ($array as $key=>$val) {
		if ($i>0) $url.="&";
		$i++;
		$url.=rawurlencode($key)."=".rawurlencode($val);
	}
	return $url;
}
function urltoarray($url){
	//takes a URL and returns its arguments in array form.
	if (strpos($url,"?")!==false){
		$url = substr($array,strpos($url,"?")+1);
	}
	$a = explode("&",$url);
	$array = array();
	foreach ($a as $key=>$val) {
		$b = explode("=",$val);
		$array[urldecode($b[0])] = urldecode($b[1]);
	}
	return $array;
}

?>
