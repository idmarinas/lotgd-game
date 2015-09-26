<?php
function dragoneggpoints_getmoduleinfo(){
	$info = array(
		"name"=>"Dragon Egg Points",
		"version"=>"1.0",
		"author"=>"DaveS",
		"category"=>"Dragon Expansion",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1362",
		"settings"=>array(
			"Dragon Egg Points,title",
			"pp"=>"Listings per page in HoF?,int|40",
			"church"=>"How many Green Dragon Kills above Base before getting blessing for dragon egg point?,int|5",
		),
		"prefs"=>array(
			"Dragon Egg Points,title",
			"dragoneggs"=>"Number of Dragon Eggs:,int|0",
			"dragoneggshof"=>"Number of Dragon Eggs HoF:,int|0",
		),
	);
	return $info;
}
function dragoneggpoints_install(){
	module_addhook("footer-hof");
	module_addhook("footer-healer");
	module_addhook("dragonkilltext");
	module_addhook("charstats");
	module_addhook("footer-bank");
	if (is_module_active("oldchurch")) module_addhook("footer-oldchurch");
	module_addhook("footer-train");
	return true;
}
function dragoneggpoints_uninstall(){
	return true;
}
function dragoneggpoints_dohook($hookname,$args){
	global $session,$SCRIPT_NAME;
	$dragoneggs=get_module_pref("dragoneggs");
	$op = httpget("op");
	switch($hookname){
		case "footer-hof":
			addnav("Warrior Rankings");
			addnav("Dragon Egg Destroyers","runmodule.php?module=dragoneggpoints&op=hof");	
		break;
		case "dragonkilltext":
			if ($session['user']['dragonkills']>=get_module_setting("mindk","dragoneggs")){
				increment_module_pref("dragoneggs",1);
				increment_module_pref("dragoneggshof",1);
				output("`n`n`&Dragon Eggs`^! You remember that with your last breath you were able to destroy one of those `&Dragon Eggs`^.  You receive a `&Dragon Egg Point`^.");
			}
		break;
		case "charstats":
			if ($session['user']['dragonkills']>=get_module_setting("mindk","dragoneggs")){
				//modified slightly from backpack module by Webpixie
				$vc=translate_inline(" `^- Info");
				if (!strstr($SCRIPT_NAME, "village")){
					$info=$dragoneggs;
				}else{
					$vc=translate_inline(" `&[`^ Info `&]");
					//$info="<a href='runmodule.php?module=dragoneggpoints&op=explain' onClick=\"".popup("runmodule.php?module=dragoneggpoints&op=explain").";return false;\" target='_blank' align='center' class=\"charinfo\" style=\"font-size:12px\">"."`^".get_module_pref("gates").$vc."</a>";
					$info="<a href='runmodule.php?module=dragoneggpoints&op=explain'\">`^".$dragoneggs.$vc."</a>";
					addnav("","runmodule.php?module=dragoneggpoints&op=explain");
				}
				addcharstat("Personal Info");
				addcharstat("Dragon Egg Points",$info);
			}
		break;
		case "footer-healer":
			if ($dragoneggs>0 && $session['user']['hitpoints']<$session['user']['maxhitpoints']){
				addnav("Dragon Egg Point Healing");
				addnav("Use Dragon Egg Point to Super Heal","runmodule.php?module=dragoneggpoints&op=heal");
			}
		break;
		case "footer-bank":
			if ($dragoneggs>0){
				addnav("Dragon Egg Point Gold");
				addnav("Sell a Dragon Egg Point for `^250 Gold","runmodule.php?module=dragoneggpoints&op=bank");
			}
		break;
		case "footer-oldchurch":
			if ($dragoneggs>0 && $op==""){
				addnav("Dragon Egg Point Blessing");
				addnav("Sell a Dragon Egg Point for a Blessing","runmodule.php?module=dragoneggpoints&op=church");
			}
		break;
		case "footer-train":
			if ($dragoneggs>0 && $op==""){
				$pointsavailable =$session['user']['donation']-$session['user']['donationspent'];
				output("`n`n`7In addition, the master offers you the option of getting some gems.");
				output("`&'I can give you a `%gem`& for a dragon egg point.  Even better, I can give you `%2 gems`& in exchange for a dragon egg point and `55 lodge points`&.");
				addnav("Exchange Dragon Egg Point");
				addnav("Dragon Egg Point for a Gem","runmodule.php?module=dragoneggpoints&op=train&op2=1");
				if ($pointsavailable<5){
					output("Unfortunately, you don't have any lodge points to use, so you'll only be able to get one gem.'");
				}else{
					output("Luckily, you have enough lodge points to make the exchange.'");
					addnav("Dragon Egg Point & Lodge Points for 2 Gems","runmodule.php?module=dragoneggpoints&op=train&op2=2");
				}
			}
		break;
	}
	return $args;
}

function dragoneggpoints_run(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
if ($op=="explain"){
	$level=get_module_setting("level");
	page_header("Dragon Egg Points");
	output("`c`b`&Dragon Egg Points`b`c`n`^");
	output("The ongoing domination of the `@Green Dragon`^ continues because of the existence of `&Dragon Eggs`^. Only by destroying all the eggs can you save the kingdom!");
	output("`n`nDestroying an egg is accomplished through several means. The most direct route is by defeating a `@Green Dragon`^ in combat. Doing so allows you destroy one of the eggs being guarded.");
	output("Other ways of finding and destroying eggs may be less difficult but will take a bit of research and luck to accomplish.  It's up for you to discover those eggs in order to destroy them.");
	output("`n`nDestroying an egg will not only gain you the prestige and awe of your fellow warriors, but it will also give you a powerful bonus in the form of a `&Dragon Egg Point`^.");
	output("`n`nA `&Dragon Egg Point`^ can be a wonderful resource in dire situations.  Some locations will automatically have options for spending them. Other locations may not appear until your need appears.  Some locations may only allow you to use your `&Dragon Egg Point`^ after attaining a certain number of `@Green Dragon`^ kills.");
	output("Finally, some locations may only allow you to spend `&Egg Points`^ after contributing to the site.  (See the Hunter's Lodge for further details on how to contribute to the site.)");
	output("`n`nMay you succeed in destroying all the `&Dragon Eggs`^ thereby saving us all!");
}
if ($op=="train"){
	page_header("Bluspring's Warrior Training");
	output("`n`c`b`7Bluspring's Warrior Training`b`c");
	if ($op2="2"){
		output("`7You decide it is worth making the exchange for `%2 gems`7 and feel it was a good deal.");
		$session['user']['gems']+=2;
		$session['user']['donationspent']+=5;
		debuglog("used 1 dragon egg point and 5 lodge points to get 2 gems.");
	}else{
		output("`7You make the exchange and get a `%gem`7.");
		$session['user']['gems']++;
		debuglog("used 1 dragon egg point to get 1 gem.");
	}
	increment_module_pref("dragoneggs",-1);
}
if ($op=="church"){
	page_header("Old Church");
	if ($session['user']['dragonkills']<get_module_setting("church")+get_module_setting("mindk","dragoneggs")){
		output("`3You ask `5Capelthwaite`3 to bless you but he says that you're not worthy of his blessing yet.");
		output("`n`n`^'You need to have killed at least `&%s `@Green Dragons`^ before you are worthy to exchange a dragon egg point for a blessing.'",get_module_setting("church")+get_module_setting("mindk","dragoneggs"));
	}else{
		output("`3You use the back door to exchange a dragon egg point for a quick blessing. `5Father Michael`3 performs a quick blessing on you.");
		increment_module_pref("dragoneggs",-1);
		apply_buff('blesscurse',
			array("name"=>translate_inline("Blessed"),
				"rounds"=>15,
				"wearoff"=>translate_inline("The burst of energy passes."),
				"atkmod"=>1.2,
				"defmod"=>1.1,
				"roundmsg"=>translate_inline("Energy flows through you!"),
			)
		);
		debuglog("used 1 dragon egg point to get blessed.");
	}
}
if ($op=="bank"){
	page_header("Ye Olde Bank");
	output("`n`c`b`^Ye Olde Bank`b`c`6");
	output("`6You sell a dragon egg point for some quick money. `@Elessa`6 gives you `^250 gold`6.");
	$session['user']['gold']+=250;
	increment_module_pref("dragoneggs",-1);
	debuglog("used 1 dragon point to get 250 gold.");
	addnav("Continue Banking","bank.php");
}
if ($op=="heal"){
	page_header("Healer's Hut");
	output("`c`b`#Healer's Hut`b`c`n");
	output("`2You decide to heal using a `&Dragon Egg Point`2.  You feel a huge surge of power.  In fact, you're better than healed. You gain a `\$permanent hitpoint`2 and `\$extra temporary hitpoints`2.");
	$session['user']['maxhitpoints']++;
	$session['user']['hitpoints']=round($session['user']['maxhitpoints']*1.1);
	increment_module_pref("dragoneggs",-1);
	debuglog("used 1 dragon point to get 1 max hitpoint and maxhitpoints*1.1.");
	require_once("lib/forest.php");
	forest(true);
}
if ($op == "hof") {
	page_header("Hall of Fame");
	$page = httpget('page');
	$pp = get_module_setting("pp");
	$pageoffset = (int)$page;
	if ($pageoffset > 0) $pageoffset--;
	$pageoffset *= $pp;
	$limit = "LIMIT $pageoffset,$pp";
	$sql = "SELECT COUNT(*) AS c FROM " . db_prefix("module_userprefs") . " WHERE modulename = 'dragoneggpoints' AND setting = 'dragoneggshof' AND value > 0";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$total = $row['c'];
	$count = db_num_rows($result);
	if (($pageoffset + $pp) < $total){
		$cond = $pageoffset + $pp;
	}else{
		$cond = $total;
	}
	$sql = "SELECT ".db_prefix("module_userprefs").".value, ".db_prefix("accounts").".name FROM " . db_prefix("module_userprefs") . "," . db_prefix("accounts") . " WHERE acctid = userid AND modulename = 'dragoneggpoints' AND setting = 'dragoneggshof' AND value > 0 ORDER BY (value+0) DESC $limit";
	$result = db_query($sql);
	$rank = translate_inline("Rank");
	$name = translate_inline("Name");
	$hofdesc = translate_inline("Dragon Egg Points");
	$none = translate_inline("No Dragon Eggs Destroyed");
	output("`n`b`c`^Most Eggs Destroyed`c`n`b");
	rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
	rawoutput("<tr class='trhead'><td>$rank</td><td>$name</td><td>$hofdesc</td></tr>");
	if (db_num_rows($result)<=0) output_notl("<tr class='trlight'><td colspan='3' align='center'>`&$none`^</td></tr>",true);
	else{
		for($i = $pageoffset; $i < $cond && $count; $i++) {
			$row = db_fetch_assoc($result);
			if ($row['name']==$session['user']['name']){
				rawoutput("<tr class='trhilight'><td>");
			}else{
				rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td>");
			}
			$j=$i+1;
			output_notl("$j.");
			rawoutput("</td><td>");
			output_notl("`&%s`^",$row['name']);
			rawoutput("</td><td>");
			output_notl("`c`b`Q%s`c`b`^",$row['value']);
			rawoutput("</td></tr>");
        }
	}
	rawoutput("</table>");
	if ($total>$pp){
		addnav("Pages");
		for ($p=0;$p<$total;$p+=$pp){
			addnav(array("Page %s (%s-%s)", ($p/$pp+1), ($p+1), min($p+$pp,$total)), "runmodule.php?module=dragoneggpoints&op=hof&page=".($p/$pp+1));
		}
	}
	addnav("Other");
	addnav("Back to Hall of Fame", "hof.php");
}
villagenav();
page_footer();
}
?>