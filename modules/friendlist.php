<?php
//version info is in readme.txt
require_once("lib/http.php");

function friendlist_getmoduleinfo(){
	$info = array(
		"name"=>"Friend List",
		"version"=>"2.3",
		"author"=>"`@CortalUX `^modified by `2Oliver Brendel",
		"override_forced_nav"=>true,
		"category"=>"Mail",
		"download"=>"http://dragonprime.net",
		"settings"=>array(
			"Friend List - Settings,title",
			"allowStat"=>"Allow users to see the amount of online friends they have?,bool|1",
			"showType"=>"Allow users to see the names of their online friends in their charstats?,bool|1",
			"allowType"=>"Can users see the names of users- or just their login?,enum,0,Just their Login,1,Names as well|1",
			"linkType"=>"What should names in the friend list do when you click them?,enum,0,Nothing,1,Send Mails,2,Link to Bio (on some pages)|1",
		),
		"prefs"=>array(
			"Friend List,title",
			"friends"=>"`^Friends?,text|",
			"ignored"=>"`^Ignored this user?,text|",
			"iveignored"=>"`^This user's ignored?,text|",
			"request"=>"`^Requests?,text|",
			"note"=>"`@(pipe seperated for each user id),viewonly",
			"note2"=>"`\$`bItems below are for the user to edit and make no sense if you're in the admin area:`b,viewonly",
			"check_show"=>"`@Do you want to see anything in your character stats?,bool|1",
			"check_login"=>"`@Do you want to see your friends names under your character stats?,bool|0",
			"check_names"=>"`@Do you want to see your friends names under your character stats?,enum,0,No Thanks,1,Just their short name,2,Their full names|1",
			"check_head"=>"`^Which stat heading will this information come under?,enum,Vital Info,Vital Info,Character Info,Character Info,Personal Info,Personal Info,Extra Info,Extra Info,Friends,Friends|Friends",
		),
	);
	return $info;
}

function rexplode($array) {
	if ($array=='') return array();
		else
		return explode("|",$array);
}
function rimplode($array) {
	if ($array==array()) return "";
		else {
			$array=array_unique($array);
			$array=array_diff($array,array(""));
			return implode("|",$array);
		}
}


function friendlist_install(){
	if (!is_module_active('friendlist')){
		output("`n`c`b`QFriend List Module - Installed`0`b`c");
	}else{
		output("`n`c`b`QFriend List Module - Updated`0`b`c");
	}
	module_addhook("checkuserpref");
	module_addhook("faq-toc");
	module_addhook("mailfunctions");
	module_addhook("charstats");
	return true;
}

function friendlist_uninstall(){
	output("`n`c`b`QFriend List Module - Uninstalled`0`b`c");
	return true;
}

function friendlist_dohook($hookname,$args){
	global $session,$SCRIPT_NAME,$battle;
	switch($hookname){
		case "checkuserpref":
			$args['allow']=false;
			if (get_module_setting('allowStat')&&$args['name']=="check_show") {
				$args['allow']=true;
			} elseif (get_module_Setting('showType')&&get_module_pref('check_show')) {
				if ($args['name']=="check_login"&&get_module_setting('allowType')==0||$args['name']=="check_names"&&get_module_setting('allowType')==1||$args['name']=="check_head") {
					$args['allow']=true;
				}
			}
		break;
		case "faq-toc":
			$t = translate_inline("`@Frequently Asked Questions on Friend Lists`0");
			output_notl("&#149;<a href='runmodule.php?module=friendlist&op=faq'>$t</a><br/>", true);
			addnav("","runmodule.php?module=friendlist&op=faq");
		break;
		case "mailfunctions":
				output_notl("`c`^[`@");
				$t = translate_inline("Friend List");
				rawoutput("<a href='runmodule.php?module=friendlist&op=list'>$t</a>");
				addnav('','runmodule.php?module=friendlist&op=list');
				output_notl("`^]`c`n");
				if (httpget('op')=='send'&&!($session['user']['superuser']&SU_GIVES_YOM_WARNING)) {
					$sql = "SELECT acctid,name FROM ".db_prefix("accounts")." WHERE login='".httppost('to')."'";
					$result = db_query($sql);
					if (db_num_rows($result)>0) {
						$row = db_fetch_assoc($result);
						if (in_array($row['acctid'],explode('|',get_module_pref('ignored')))) {
							popup_header("Ye Olde Poste Office");
							output_notl("`c`^[`%");
							$t = translate_inline("Back to your Mail");
							rawoutput("<a href='mail.php'>$t</a>");
							output_notl("`^]`c`Q`n");
							$info = translate_inline("%s`Q has ignored you, so you cannot send %s`Q Ye Olde Mail.");
							$info = str_replace('%s',$row['name'],$info);
							output_notl($info);
							popup_footer();
							die();
						}
					}
				}
		break;
		case "charstats":
			if (get_module_setting('allowStat')&&get_module_pref('check_show')) { // I could so other 'if' checks here, but if admin have it turned off, it'd increase load, when it isn't needed anyway
				$friends = rexplode(get_module_pref('friends'));
				$x=0;
				$last = date("Y-m-d H:i:s", strtotime("-".getsetting("LOGINTIMEOUT", 900)." sec"));
				$addon="";
				if (get_module_setting("allowType")==0&&get_module_pref("check_login")==1||get_module_setting("allowType")==1&&get_module_pref("check_names")==1) {
					$addon=",login";
				} elseif (get_module_setting("allowType")==1&&get_module_pref("check_names")==2) {
					$addon=",name";
				}
				$onlinelist="";
				$bl=false;
				if ($battle===false||!isset($battle)||empty($battle)) {
					if (httpget('module')==''&&$session['user']['specialinc']==''&&$session['user']['specialmisc']=='') {
						$bl=true;
					}
				}
				if (implode(",",$friends)!='') {
					$sql = "SELECT loggedin,laston$addon FROM ".db_prefix("accounts")." WHERE acctid IN (".implode(",",$friends).") AND locked=0";
					$result = db_query_cached($sql,"friendliststat-".$session['user']['acctid'],60);
					while ($row=db_fetch_assoc($result)) {
						$loggedin=$row['loggedin'];
						if ($row['laston']<$last) {
							$loggedin=false;
						}
						if ($loggedin) {
							$x++;
							if ($addon!="") {
								if ($onlinelist!="") $onlinelist.=", ";
								if (get_module_setting('linkType')==1) {
									$onlinelist.="<a href='mail.php?op=write&to={$row['login']}' class='colLtGreen' target='_blank' onClick=\"".popup("mail.php?op=write&to={$row['login']}").";return false;\">";
								} elseif (get_module_setting('linkType')==2&&$bl) {
									$link="bio.php?char=".rawurlencode($row['login'])."&ret=".urlencode($_SERVER['REQUEST_URI']);
									$onlinelist.="<a href='$link' class='colLtGreen'>";
									addnav($link,"");
								}
								if ($addon==",login") {
									$onlinelist.=sanitize($row['login']);
								} else {
									$onlinelist.=sanitize($row['name']);
								}
								if (get_module_setting('linkType')==1||get_module_setting('linkType')==2&&$bl) $onlinelist.="</a>";
							}
						}
					}
				}
				$onlinelist.=".";
				if ($x>0) {
					$words =sprintf_translate("`^You have `%%s`^ logged-in friends.",$x);
				} else {
					$words = translate_inline("`@None of your friends are logged-in.");
				}
				setcharstat(translate_inline(get_module_pref('check_head')),translate_inline("Friend Count"),$words);
				if ($onlinelist!=".") setcharstat(translate_inline(get_module_pref('check_head')),translate_inline("Friend List"),$onlinelist);
			}
		break;
	}
	return $args;
}

function friendlist_run(){
	global $session;
	$op = httpget('op');
	if ($op=='faq') {
		popup_header("Frequently Asked Questions on Friend Lists");
	} else {
		popup_header("Ye Olde Poste Office");
		output_notl("`c`^[`@");
		$t = translate_inline("Friend List");
		rawoutput("<a href='runmodule.php?module=friendlist&op=list'>$t</a>");
		addnav('','runmodule.php?module=friendlist&op=list');
		output_notl("`^] - [`@");
		$t = translate_inline("Friend Search");
		rawoutput("<a href='runmodule.php?module=friendlist&op=search'>$t</a>");
		addnav('','runmodule.php?module=friendlist&op=search');
		output_notl("`^] - [`%");
		$t = translate_inline("Back to the Ye Olde Poste Office");
		rawoutput("<a href='mail.php'>$t</a>");
		output_notl("`^]`c`Q`n");
	}
	require_once("modules/friendlist/friendlist_$op.php");
	if ($op=='deny') {
		friendlist_deny();
		$op="list";
		require_once("modules/friendlist/friendlist_list.php");
	}
	$fname="friendlist_".$op;
	$fname();
	popup_footer();
}
?>
