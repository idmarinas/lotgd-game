<?php

function getip() {
	if (isset($_SERVER)){
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$realip = $_SERVER['HTTP_CLIENT_IP'];
		} else {
			$realip = $_SERVER['REMOTE_ADDR'];
		}
	} else {
		if ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) {
			$realip = getenv( 'HTTP_X_FORWARDED_FOR' );
		} elseif ( getenv( 'HTTP_CLIENT_IP' ) ) {
			$realip = getenv( 'HTTP_CLIENT_IP' );
		} else {
			$realip = getenv( 'REMOTE_ADDR' );
		}
	}
	return $realip;
}

function index_getmoduleinfo(){
	$info = array(
		"name"=>"Mod Hack",
		"author"=>"Chris Vorndran<br>`6Idea: `2Robert`0",
		"category"=>"Administrative",
		"version"=>"1.0",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=55",
		"vertxtloc"=>"http://dragonprime.net/users/Sichae/",
		"description"=>"Place in /modules/ folder to know who is attempting to view the contents.",
	);
	return $info;
}
function index_install(){
	$modhack = array(
		'hackid'=>array('name'=>'hackid', 'type'=>'int unsigned', 'extra'=>'not null auto_increment'),
		'ipadd'=>array('name'=>'ipadd', 'type'=>'varchar(255)', 'extra'=>'not null'),
		'dateof'=>array('name'=>'dateof', 'type'=>'datetime', 'extra'=>'not null'),
		'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key', 'unique'=>'1', 'columns'=>'hackid'),
		'index-hackid'=>array('name'=>'hackid', 'type'=>'index', 'columns'=>'hackid'));
	require_once("lib/tabledescriptor.php");
    synctable(db_prefix('modhack'), $modhack, true);
	module_addhook("superuser");
	return true;
}
function index_uninstall(){
	db_query("DROP TABLE IF EXISTS `".db_prefix("modhack")."`");
	return true;
}
function index_dohook($hookname,$args){
	global $session;
	switch ($hookname){
		case "superuser":
			if ($session['user']['superuser'] & SU_MEGAUSER){
				addnav("Actions");
				addnav("/modules/ Trespass","runmodule.php?module=index");
			}
			break;
		}
	return $args;
}
function index_run(){
	global $session;
	page_header("/modules/ Trespass");
	output("`#Here are all of the IPs of those that have ventured into the /modules/ folder, in order to poke around at what you have.`n`n");
	$sql = "SELECT * FROM ".db_prefix("modhack")." ORDER BY dateof DESC";
	$res = db_query($sql);
	$date = translate_inline("Date of Intrusion");
	$ip = translate_inline("IP Address");
	rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
	rawoutput("<tr class='trhead'><td>$date</td><td>$ip</tr>");
	if (db_num_rows($res)>0){
		for($i = 0; $i < db_num_rows($res); $i++) {
			$row = db_fetch_assoc($res);
			rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td>");
			output_notl("%s",$row['dateof']);
			rawoutput("</td><td>");
			output_notl("`&%s`0",$row['ipadd']);
			rawoutput("</td></tr>");
		}
	}
	rawoutput("</table>");
	require_once("lib/superusernav.php");
	superusernav();
	page_footer();
}
if ($_SERVER['REQUEST_URI'] == "/modules/"){
	require_once("../dbconnect.php");
	$link = mysql_connect($DB_HOST,$DB_USER,$DB_PASS);
	mysql_select_db($DB_NAME,$link);
	$grab = getip();
	$sql = "INSERT INTO ".$DB_PREFIX."modhack 
		(dateof,ipadd) 
		VALUES ('".date("Y-m-d H:i:s",strtotime("now"))."','".$grab."')";
		mysql_query($sql,$link);
	echo("<big>What do you think you are doing in here?</big>");
}
?>