<?php
// translator ready
// addnews ready
// mail ready
function httpget($var){
	global $HTTP_GET_VARS;

	$res = isset($_GET[$var]) ? $_GET[$var] : false;
	if ($res === false || $res == '') {
		$res = isset($HTTP_GET_VARS[$var]) ? $HTTP_GET_VARS[$var] : false;
	}
	return $res;
}

function httpallget() {
	return $_GET;
}

function httpset($var, $val,$force=false){
	global $HTTP_GET_VARS;
	if (isset($_GET[$var]) || $force) $_GET[$var] = $val;
	if (isset($HTTP_GET_VARS[$var])) $HTTP_GET_VARS[$var] = $val;
}

function httppost($var){
	global $HTTP_POST_VARS;

	$res = isset($_POST[$var]) ? $_POST[$var] : false;
	if ($res === false || $res == '') {
		$res = isset($HTTP_POST_VARS[$var]) ?
			$HTTP_POST_VARS[$var] : false;
	}
	return $res;
}

function httppostisset($var) {
	global $HTTP_POST_VARS;

	$res = isset($_POST[$var]) ? 1 : 0;
	if ($res === 0) {
		$res = isset($HTTP_POST_VARS[$var]) ? 1 : 0;
	}
	return $res;
}

function httppostset($var, $val, $sub=false){
	global $HTTP_POST_VARS;
	if ($sub === false) {
		if (isset($_POST[$var])) $_POST[$var] = $val;
		if (isset($HTTP_POST_VARS[$var])) $HTTP_POST_VARS[$var] = $val;
	} else {
		if (isset($_POST[$var]) && isset($_POST[$var][$sub]))
			$_POST[$var][$sub]=$val;
		if (isset($HTTP_POST_VARS[$var]) && isset($HTTP_POST_VARS[$var][$sub]))
			$HTTP_POST_VARS[$var][$sub]=$val;
	}
}

function httpallpost(){
	return $_POST;
}

function postparse($verify=false, $subval=false){
	if ($subval) $var = $_POST[$subval];
	else $var = $_POST;

	reset($var);
	$sql = "";
	$keys = "";
	$vals = "";
	$i = 0;
	foreach ($var as $key=>$val) {
		if ($verify === false || isset($verify[$key])) {
			if (is_array($val)) $val = addslashes(serialize($val));
			$sql .= (($i > 0) ? "," : "") . "$key='$val'";
			$keys .= (($i > 0) ? "," : "") . "$key";
			$vals .= (($i > 0) ? "," : "") . "'$val'";
			$i++;
		}
	}
	return array($sql, $keys, $vals);
}

/**
 * Return base url of game
 *
 * @param false|string $file
 *
 * @return string
 */
function lotgd_base_url($file = false)
{
	$basename = (!$file ? basename($_SERVER['SCRIPT_NAME']) : $file);
	if ($basename)
	{
		$path = ($_SERVER['PHP_SELF'] ? trim($_SERVER['PHP_SELF'], '/') : '');
		$basePos = strpos($path, $basename) ?: 0;
		$baseUrl = substr($path, 0, $basePos);
	}

	return  $baseUrl;
}

/**
 * Deprecated
 */
function baseUrl($file = false)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 2.1.0; and delete in version 3.0.0 please use "%s" instead',
        __METHOD__,
		'lotgd_base_url'
    ), E_USER_DEPRECATED);

    return lotgd_base_url($file);
}
