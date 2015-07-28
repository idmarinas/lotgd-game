<?php
/*
v 1.0 only stand-guard supply
*/

function dwellings_antiplunder_getmoduleinfo(){
	$info = array(
		"name"=>"Dwellings Antiplunder",
		"version"=>"1.0",
		"author"=>"`2Oliver Brendel",
		"category"=>"Dwellings",
		"download"=>"http://dragonprime.net/dls/dwellings_antiplunder.zip",
		"settings"=>array(
			"Antiplunder - Preferences, title",
			"turns"=>"What percentage (result rounded afterwards) of the newday turns does a guard lose?, floatrange,1,100,1|40",
			"maxguards"=>"What is the maximum amount of fights the player guard can handle?, floatrange,1,20,1|5",
			"This should also prevent the random creation & insertion of farmies to make the coffers not accessible,note",
			"Also, dragonkills<1 can't guard,note",
			),
		"prefs"=>array(
			"Dwellings Antiplunder Preferences,title",
			"standsinwhatdwelling"=>"guards what dwid,int",
			"guarded"=>"how often happened an incident?,int",
			),
		"requires"=>array(
			"dwellings_plunder"=>"1.02|`2Oliver Brendel",
			"dwellings"=>"20060212|<a href='http://www.joshuadhall.com' target=_new>Sixf00t4</a>, Christian Rutsch, Chris Vorndran, `4Talisman`0, Oliver Brendel",
		), 			
	);
	return $info;
}

function dwellings_antiplunder_install(){
	module_addhook_priority("dwellings_plunder_traps", 50);
	module_addhook_priority("newday", 5);
	module_addhook_priority("dwellings-inside", 5);
	module_addhook_priority("dwellings-addsleepers",5);
	module_addhook("player-login");
	module_addhook("dwellings-management");
	return true;
}

function dwellings_antiplunder_uninstall(){
	return true;
}

function dwellings_antiplunder_dohook($hookname,$args){
	global $session;
	switch ($hookname) {
		case "dwellings-management";
			$dwid=$args['dwid'];
			$antiplunder=translate_inline("Anti-Plundering measures");
			rawoutput("<tr height=30px class='trlight'><td>$antiplunder</td><td>");
			$guard=antiplunder_dwidguard($dwid);
			if ($guard!=false) {
				$sql="SELECT name,weapon FROM ".db_prefix("accounts")." WHERE acctid=$guard";
				$row=db_fetch_assoc(db_query($sql));			
				output("`\$Guard: `5%s`5 (%s`5)",$row['name'],$row['weapon']);
			}
			rawoutput("</td></tr>");
			break;	
		case "player-login":
			set_module_pref('standsinwhatdwelling',0,'dwellings_antiplunder',$session['user']['acctid']);
			break;
		case "dwellings-addsleepers":
			if (antiplunder_dwidguard($args['dwid'])!=false) $args['allowed']++; //the guard can always sleep here
			break;
		case "dwellings-inside":
			$guard=antiplunder_dwidguard($args['dwid']);
			if ($guard) {
				$sql="SELECT name,weapon FROM ".db_prefix("accounts")." WHERE acctid=$guard";
				$row=db_fetch_assoc(db_query($sql));			
				output("`5%s`5 stands with prepared %s`5 guard at the coffers.`n",$row['name'],$row['weapon']);
			}
			addnav("Coffers Safety","runmodule.php?module=dwellings_antiplunder&dwid={$args['dwid']}");
			break;
		case "newday":
			if (get_module_pref('guarded')) { //stood guard last gameday
				$lostturns=round($session['user']['turns']*get_module_setting('turns')/100,0);
				output("`2Due to the fact you only slept half of the night yesterday because of your guard, you `\$lose %s turns`2!`n`n",$lostturns);
				$session['user']['turns']-=$lostturns;
				set_module_pref('standsinwhatdwelling',0);
				set_module_pref('guarded',0); //to make sure nobody creates a level 1 farmboy just to stand guard (even if he is defeated the coffers won't be accessed)
			}
			break;
		case "dwellings_plunder_traps":
			$guard=antiplunder_dwidguard($args['id']);

			if ($guard!=false) {
				$howoften=get_module_pref('guarded','dwellings_antiplunder',$guard);
				if ($howoften>=get_module_setting('maxguards')) break;
				//dwelling IS guarded and the guard is ready
				$args['chance']="fight";
				$args['fight']=array("acctid"=>$guard);
				set_module_pref('guarded',$howoften+1,'dwellings_antiplunder',$guard);
			}
			break;
	}
	return $args;
}

function dwellings_antiplunder_run(){
	global $session;
	$dwid=httpget('dwid');
	$op=httpget('op');
	page_header("Coffers Safety");
	output("`b`i`5`cCoffers `2Safety`c`i`b`n`n");
	addnav("Return to the dwelling","runmodule.php?module=dwellings&op=enter&dwid=$dwid");
	addnav("Actions");
	switch($op) {
		case "guardlogout":
			set_module_pref('standsinwhatdwelling',$dwid);
			set_module_pref('guarded',0);
			redirect("runmodule.php?module=dwellings&op=logout&dwid=$dwid");
			break;
		case "guard":
			output("`5So you want to guard the dwelling? Excellent.`n`n");
			output("Being a guard you don't take up space and don't need a bed.`n");
			output("Yet as you know `@it consumes %s %% of your next days turns`5 as you have to stay awake during the night and just keep standing with coffee and need some rest during the morning.",get_module_setting("turns"));
			output(" Though, if nothing happens you won't lose anything.");
			output("`nYou will probably be able to fend off `\$%s`5 attacks on the coffers before you collapse and sleep.",get_module_setting('maxguards'));
			output(" `^If you login again and there has been no new day, the dwellings will be unguarded for that time until you come here again and guard again!`5.");
			output("`n`nDo you want to stand guard and logout at the coffers?");
			addnav("Stand guard & Logout","runmodule.php?module=dwellings_antiplunder&dwid=$dwid&op=guardlogout");
			addnav("Back to Coffers Safety","runmodule.php?module=dwellings_antiplunder&dwid=$dwid");
			break;
		default:
			output("`5You access the local security options and can now decide *how* you want to secure the coffers and the dwelling you stand in. (you don't need to be the owner to setup safety measures or stand guard)`n`n");
			output("Currently you can still:`n");
			$option=0;
			$guard=antiplunder_dwidguard($dwid);
			$guardyou=antiplunder_isguard($session['user']['acctid']);
			if ($guard==false && $guardyou==false) {
				if ($session['user']['dragonkills']<1) {
					output("Sorry, you are too young to guard coffers. Please go and get some experience (you need 1 Dragonkill at least).`n");
				} else {
					output("`@-Stand Guard `5(consumes %s %% of your next days turns)`n",get_module_setting("turns"));
					addnav("Stand guard","runmodule.php?module=dwellings_antiplunder&op=guard&dwid=$dwid");
					$option=1;
				}
			} elseif ($guard!=false) {
				$sql="SELECT name FROM ".db_prefix("accounts")." WHERE acctid=$guard";
				$row=db_fetch_assoc(db_query($sql));
				output("`@-Guard %s`5 is already here and greets you`n",$row['name']);
			}			
				
			if ($option==0) output("`nSorry, currently you can't do anything");
	}
	page_footer();
}

function antiplunder_dwidguard($dwid) {
	//determines  if a guard is there or not and returns the acctid or FALSE
	$sql="SELECT userid FROM ".db_prefix("module_userprefs")." WHERE modulename='dwellings_antiplunder' AND setting='standsinwhatdwelling' AND value='$dwid';";
	$result=db_query($sql);
	if (db_num_rows($result)>0) {
		$row=db_fetch_assoc($result);
		return $row['userid'];
	}
	return false;
}

function antiplunder_isguard($acctid) {
	//determines if this user is a guard or not and returns the dwid
	$sql="SELECT value FROM ".db_prefix("module_userprefs")." WHERE modulename='dwellings_antiplunder' AND setting='standsinwhatdwelling' AND userid='$acctid';";
	$result=db_query($sql);
	if (db_num_rows($result)>0) {
		$row=db_fetch_assoc($result);
		return $row['value'];
	}
	return false;
}

?>