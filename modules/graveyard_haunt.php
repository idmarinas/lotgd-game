<?php

function graveyard_haunt_getmoduleinfo(){
	$info = array(
		"name"=>"Graveyard Haunting",
		"version"=>"1.0",
		"author"=>"Core extraction by `2Oliver Brendel",
		"category"=>"Graveyard",
		"download"=>"core",
		"settings"=>array(
			"hauntcost"=>"Cost to haunt,int|25",
			"turnloss"=>"How many turns loses a successfully haunted user?,int|1"
			),
		"prefs"=>array(
			"hauntedby"=>"Acctid of the haunter,viewonly",
			)
	);
	return $info;
}

function graveyard_haunt_install(){
	module_addhook("deathoverlord_actions");
	module_addhook("newday");
	return true;
}

function graveyard_haunt_uninstall(){
	return true;
}

function graveyard_haunt_dohook($hookname,$args){
	global $session;
	if ($session['user']['acctid']!=7) return $args;
	switch ($hookname) {
	case "newday":
		$by=(int)get_module_pref('hauntedby');
		if ($by!=0){
			$sql="SELECT name from ".db_prefix('accounts')." WHERE acctid=".$by;
			$result=db_query($sql);
			$row=db_fetch_assoc($result);
			if (db_num_rows($result)==0) {
				$haunter=translate_inline("The Evil Reaper");
			} else $haunter=$row['name'];
			output("`n`n`)You have been haunted by %s`); as a result, you lose a forest fight!",$haunter);
			$session['user']['turns']-=get_module_setting('turnloss');
			set_module_pref('hauntedby',0);
		}
		
		break;

	case "deathoverlord_actions":
		$args[]=array(
			"favor"=>get_module_setting('hauntcost'),
			"link"=>"graveyard_haunt&op=haunt",
			"linktext"=>translate_inline("Haunt a foe"),
			"text"=>"",
			"titletext"=>translate_inline("`\${deathoverlord}`) speaks, \"`7I am moderately impressed with your efforts.  A minor favor I now grant to you, but continue my work, and I may yet have more power to bestow.`)\"")
			);
		break;
	}
	return $args;
}

function graveyard_haunt_run(){
	global $session;
	$deathoverlord=getsetting('deathoverlord','`$Ramius');
	page_header("%s's little haunting",sanitize($deathoverlord));
	addnav("M?Return to the Mausoleum","graveyard.php?op=enter");
	addnav("Places");
	addnav("S?Land of the Shades","shades.php");
	addnav("G?The Graveyard","graveyard.php");
	$op=httpget('op');
	switch ($op) {
case "stage2":
$string="%";
$name = httppost('name');
for ($x=0;$x<strlen($name);$x++){
	$string .= substr($name,$x,1)."%";
}
$sql = "SELECT login,name,level FROM " . db_prefix("accounts") . " WHERE name LIKE '".addslashes($string)."' AND locked=0 ORDER BY level,login";
$result = db_query($sql);
if (db_num_rows($result)<=0){
	output("`\$%s`) could find no one who matched the name you gave him.",$deathoverlord);
}elseif(db_num_rows($result)>100){
	output("`\$%s`) thinks you should narrow down the number of people you wish to haunt.",$deathoverlord);
	$search = translate_inline("Search");
	rawoutput("<form action='runmodule.php?module=graveyard_haunt&op=stage2' method='POST'>");
	addnav("","runmodule.php?module=graveyard_haunt&op=stage2");
	output("Who would you like to haunt? ");
	rawoutput("<input name='name' id='name'>");
	rawoutput("<input type='submit' class='button' value='$search'>");
	rawoutput("</form>");
	rawoutput("<script language='JavaScript'>document.getElementById('name').focus()</script>",true);
}else{
	output("`\$%s`) will allow you to try to haunt these people:`n",$deathoverlord);
	$name = translate_inline("Name");
	$lev = translate_inline("Level");
	rawoutput("<table cellpadding='3' cellspacing='0' border='0'>");
	rawoutput("<tr class='trhead'><td>$name</td><td>$lev</td></tr>");
	for ($i=0;$i<db_num_rows($result);$i++){
		$row = db_fetch_assoc($result);
		rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td><a href='runmodule.php?module=graveyard_haunt&op=stage3&name=".HTMLEntities($row['login'], ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."'>");
		output_notl("%s", $row['name']);
		rawoutput("</a></td><td>");
		output_notl("%s", $row['level']);
		rawoutput("</td></tr>",true);
		addnav("","runmodule.php?module=graveyard_haunt&op=stage3&name=".HTMLEntities($row['login'], ENT_COMPAT, getsetting("charset", "ISO-8859-1")));
	}
	rawoutput("</table>",true);
}
break;
case "stage3":
output("`)`c`bThe Mausoleum`b`c");
$name = httpget('name');
$sql = "SELECT name,level,acctid FROM " . db_prefix("accounts") . " WHERE login='$name'";
$result = db_query($sql);
if (db_num_rows($result)>0){
	$row = db_fetch_assoc($result);
	$already_haunted=(int)get_module_pref('hauntedby',$row['acctid']);
	if ($row['hauntedby']!=0){
		output("That person has already been haunted, please select another target");
	}else{
		$session['user']['deathpower']-=get_module_pref('hauntcost');
		$roll1 = e_rand(0,$row['level']);
		$roll2 = e_rand(0,$session['user']['level']);
		if ($roll2>$roll1){
			output("You have successfully haunted `7%s`)!", $row['name']);
			set_module_pref('hauntedby',$session['user']['acctid'],'graveyard_haunt');
			addnews("`7%s`) haunted `7%s`)!",$session['user']['name'],$row['name']);
			$subj = array("`)You have been haunted");
			$body = array("`)You have been haunted by `&%s`).",$session['user']['name']);
			require("lib/systemmail.php");
			systemmail($row['acctid'], $subj, $body);
		}else{
			addnews("`7%s`) unsuccessfully haunted `7%s`)!",$session['user']['name'],$row['name']);
			switch (e_rand(0,5)){
			case 0:
				$msg = "Just as you were about to haunt `7%s`) good, they sneezed, and missed it completely.";
				break;
			case 1:
				$msg = "You haunt `7%s`) real good like, but unfortunately they're sleeping and are completely unaware of your presence.";
				break;
			case 2:
				$msg = "You're about to haunt `7%s`), but trip over your ghostly tail and land flat on your, um... face.";
				break;
			case 3:
				$msg = "You go to haunt `7%s`) in their sleep, but they look up at you, and roll over mumbling something about eating sausage just before going to bed.";
				break;
			case 4:
				$msg = "You wake `7%s`) up, who looks at you for a moment before declaring, \"Neat!\" and trying to catch you.";
				break;
			case 5:
				$msg = "You go to scare `7%s`), but catch a glimpse of yourself in the mirror and panic at the sight of a ghost!";
				break;
			}
			output($msg, $row['name']);
		}
	}
}else{
	output("`\$%s`) has lost their concentration on this person, you cannot haunt them now.",$deathoverlord);
}
break;
default:
output("`\$%s`) is impressed with your actions, and grants you the power to haunt a foe.`n`n",$deathoverlord);
$search = translate_inline("Search");
rawoutput("<form action='runmodule.php?module=graveyard_haunt&op=stage2' method='POST'>");
addnav("","runmodule.php?module=graveyard_haunt&op=stage2");
output("Who would you like to haunt? ");
rawoutput("<input name='name' id='name'>");
rawoutput("<input type='submit' class='button' value='$search'>");
rawoutput("</form>");
rawoutput("<script language='JavaScript'>document.getElementById('name').focus()</script>");
break;
}
page_footer();
}

?>
