<?php

require_once "modules/mountaccessories/lib.php";

function mountaccessories_getmoduleinfo(){
	$info=array(
		"name"=>"Mount Accessories - Core",
		"version"=>"0.1 2009-01-13",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"Mount Accessories",
		"download"=>"",
		"settings"=>array(
			"accessories"=>"Array of Mount accessories,viewonly",
		),
		"prefs"=>array(
			"accessories"=>"Player's current Mount accessories,textarea|array()",
		)
	);
	return $info;
}

function mountaccessories_install(){
	module_addhook("newday");
	module_addhook("stables-nav");
	module_addhook("boughtmount");
	module_addhook("soldmount");
	module_addhook("superuser");
	module_addhook("charstats");
	return true;
}

function mountaccessories_uninstall(){
	return true;
}

function mountaccessories_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "boughtmount":
		case "soldmount":
			$accs = get_player_acc_list();
			$hadaccessories=count($accs);
			if ($hadaccessories!=0){
				output("Merick merrily strips off the accessories adorning your previous mount, and tosses them into a pile in the corner.  \"Ye'll nae be needin' these any more, I dare wager!\"`n`nYou contemplate asking him for the accessories back, or perhaps some money for them - but the slightly manic look in Merick's eye, and the sharpened pitchfork slung across his back, give you second thoughts.`n");
			};
			strip_accessories();
			addnav("Browse Mount Accessories","runmodule.php?module=mountaccessories&op=browse");
			if ($session['user']['hashorse']==0){
				blocknav("runmodule.php?module=mountaccessories&op=browse");
			}
			break;
		case "newday":
			apply_all_accessories();
			break;
		case "superuser":
			addnav("Edit Mount Accessories","runmodule.php?module=mountaccessories&op=editdefaultaccessories");
			break;
		case "stables-nav":
			if ($session['user']['hashorse']>0){
				addnav("Browse Mount Accessories","runmodule.php?module=mountaccessories&op=browse");
			}
			break;
		case "charstats":
			$rawaccs = get_player_acc_list();
			$display = "";
			$number = 0;
			foreach($rawaccs as $acc => $details){
				$number++;
				$display .= $details['displayname'];
				$display .= "<br>";
			}
			$stat = "Mount Accessories";
			if ($number==0){
				$display = "None";
			}
			setcharstat("Equipment Info", $stat, $display);
			break;
	}
	return $args;
}

function mountaccessories_run(){
	global $session;
	$op = httpget('op');
	switch($op){
		case "editdefaultaccessories":
			page_header("Editing Mount Accessories");
			$accs = get_default_accs_list();
			output("Here we have the editing screen.  Follow the format you see already, seperating parameters from their values with '=>' and using ';;' to denote a new line.  Leave the last line without the ';;' and everything should be fine.`n`n");
			output("Here are the ID's of all your Mounts, which will come in handy:`n");
			$sql = "SELECT mountid, mountname FROM " . db_prefix("mounts");
			$result = db_query($sql);
			for ($i=0;$i<db_num_rows($result);$i++){
				$row = db_fetch_assoc($result);
				output("%s: %s`n",$row['mountid'],$row['mountname']);
			}
			output("`n`nDefault stuff which affects Mount Accessories in core:`n`ndisplayname - shown in stables`ngoldcost`ngemcost`nhidefromstables`nnewdaymsg`nturns`nbuffname`nbuffrounds`nbuffwearoffmsg`nbuffeffectmsg`nbuffnodmgmsg`nbuffeffectfailmsg`nbuffatkmod`nbuffdefmod`nbuffinvulnerable`nbuffregen`nbuffminioncount`nbuffminbadguydamage`nbuffmaxbadguydamage`nbuffmingoodguydamage`nbuffmaxgoodguydamage`nbufflifetap`nbuffdamageshield`nbuffbadguydmgmod`nbuffbadguyatkmod`nbuffbadguydefmod`n`nFormatting:`ndisplayname=>Sexy Accessory;;`ngoldcost=>500;;`nturns=>10`nNote the lack of ;; on the last line.`nHave fun!`n`n");
			rawoutput("<form action='runmodule.php?module=mountaccessories&op=savedefaultaccessories' method='POST'>");
			foreach($accs AS $acc => $details){
				rawoutput($acc);
				rawoutput("<a href=\"runmodule.php?module=mountaccessories&op=deleteaccessory&acc=$acc\">Delete</a>");
				addnav("","runmodule.php?module=mountaccessories&op=deleteaccessory&acc=".$acc);
				rawoutput("<br /><textarea name='$acc' cols='40' rows='12'>");
				$size=count($details);
				$i = 0;
				foreach($details AS $detail => $value){
					$textboxoutput = $detail."=>".$value;
					$i++;
					if ($i != $size){
						$textboxoutput .=";;";
					}
					rawoutput($textboxoutput);
				}
				rawoutput("</textarea><br /><br />");
			}
			rawoutput("<input type='submit' class='button' value='".translate_inline("Save")."'");
			rawoutput("</form>");
			addnav("","runmodule.php?module=mountaccessories&op=savedefaultaccessories");
			addnav("Create a new Mount Accessory","runmodule.php?module=mountaccessories&op=newaccessory");
			addnav("Back to the Grotto","superuser.php");
		break;
		case "savedefaultaccessories":
			page_header("Editing Mount Accessories");
			$postedaccs = httpallpost();
			function trim_value(&$value) {
				$value = trim($value);
			}
			foreach($postedaccs AS $acc => $details){
				$acc = str_replace("_"," ",$acc);
				$acc = trim($acc);
				$detailsexploded = explode(";;",$details);
				foreach($detailsexploded as $detail){
					$detailandvalue = explode("=>",$detail);
					$size=count($detailandvalue);
					for ($i=0; $i < $size/2; $i++){
						$detailandvalue[$i] = trim($detailandvalue[$i]);
						$accsarray[$acc][$detailandvalue[$i]]=$detailandvalue[$i+($size/2)];
					}
				}
			}
			debug("Is this a full, reconstructed Accessories array?");
			debug($accsarray);
			set_module_setting("accessories",serialize($accsarray),"mountaccessories");
			output("Accessories have been saved.");
			addnav("Back to the Grotto","superuser.php");
		break;
		case "newaccessory":
			page_header("Add a new Mount Accessory");
			output("Now we're going to add a new Mount Accessory.  This has to be parsed so that it can be turned back into an array, so follow the format you'll see below, seperating parameters from their values with '=>' and using ';;' to denote a new line.  Insert an internal name or ID for the mount accessory in the first input box - probably better to keep this value URL-friendly, IE lower-case and no spaces.  Leave the last line without the ';;' and everything should be fine.`n`n");
			output("Here are the ID's of all your Mounts, which will come in handy:`n");
			$sql = "SELECT mountid, mountname FROM " . db_prefix("mounts");
			$result = db_query($sql);
			for ($i=0;$i<db_num_rows($result);$i++){
				$row = db_fetch_assoc($result);
				output("%s: %s`n",$row['mountid'],$row['mountname']);
			}
			output("`n`nDefault stuff which affects Mount Accessories in core:`n`ndisplayname - shown in stables`ngoldcost`ngemcost`nhidefromstables`nnewdaymsg`nturns`nbuffname`nbuffrounds`nbuffwearoffmsg`nbuffeffectmsg`nbuffnodmgmsg`nbuffeffectfailmsg`nbuffatkmod`nbuffdefmod`nbuffinvulnerable`nbuffregen`nbuffminioncount`nbuffminbadguydamage`nbuffmaxbadguydamage`nbuffmingoodguydamage`nbuffmaxgoodguydamage`nbufflifetap`nbuffdamageshield`nbuffbadguydmgmod`nbuffbadguyatkmod`nbuffbadguydefmod`n`nFormatting:`ndisplayname=>Sexy Accessory;;`ngoldcost=>500;;`nturns=>10`nNote the lack of ;; on the last line.`nHave fun!`n`n");
			rawoutput("<form action='runmodule.php?module=mountaccessories&op=savenewaccessory' method='POST'>");
			rawoutput("Internal name or ID:<input name='newaccname'>");
			rawoutput("<br /><textarea name='newacc' cols='40' rows='12'>mountid=>0;;</textarea><br /><br />");
			rawoutput("<input type='submit' class='button' value='".translate_inline("Save")."'");
			rawoutput("</form>");
			addnav("","runmodule.php?module=mountaccessories&op=savenewaccessory");
			addnav("Back to the Grotto","superuser.php");
		break;
		case "savenewaccessory":
			page_header("Editing Mount Accessories");
			$acc = httppost("newacc");
			$accname = httppost("newaccname");
			debug($acc);
			$acc = str_replace("_"," ",$acc);
			$acc = trim($acc);
			$detailsexploded = explode(";;",$acc);
			debug($detailsexploded);
			foreach($detailsexploded as $detail){
				$detailandvalue = explode("=>",$detail);
				$size=count($detailandvalue);
				for ($i=0; $i < $size/2; $i++){
					$detailandvalue[$i] = trim($detailandvalue[$i]);
					$newacc[$detailandvalue[$i]]=$detailandvalue[$i+($size/2)];
				}
			}
			debug("Is this a full, reconstructed Accessory?");
			debug($accname);
			
			$allaccs = get_default_accs_list();
			$allaccs[$accname] = $newacc;
			
			debug("Is this the proper Accessories array?");
			debug($allaccs);
			
			set_module_setting("accessories",serialize($allaccs),"mountaccessories");
			output("The new accessory has been saved.");
			addnav("Back to the Grotto","superuser.php");
			addnav("Create a new Mount Accessory","runmodule.php?module=mountaccessories&op=newaccessory");
			addnav("Edit Mount Accessories","runmodule.php?module=mountaccessories&op=editdefaultaccessories");
		break;
		case "deleteaccessory":
			page_header("Deleting a Mount Accessory");
			$acc = httpget("acc");
			$accsarray = get_default_accs_list();
			unset ($accsarray[$acc]);
			set_module_setting("accessories",serialize($accsarray),"mountaccessories");
			output("Accessory Deleted.");
			addnav("Back to the Grotto","superuser.php");
			addnav("Create a new Mount Accessory","runmodule.php?module=mountaccessories&op=newaccessory");
			addnav("Edit Mount Accessories","runmodule.php?module=mountaccessories&op=editdefaultaccessories");
		break;
		case "browse":
			page_header("Mount Accessories");
			output("Merick directs you to a rack of accessories for your current Mount.`n`n\"Now, I have to be warnin' yer,\" says Merick, \"I'm quite happy to give yer a trade-in on any beasties ye might buy from me, but I don't be takin' no returns on the accessories.  There's nae market in pre-owned add-ons, y'see.\"`n`nYou peruse the accessories on display.");
			output("Here are the accessories available for your Mount:`n`n");
			$stuffavailable = 0;
			$accs = get_mount_accs_list($session['user']['hashorse']);
			debug($accs);
			foreach($accs as $acc => $details){
				if (!get_player_acc($acc) && !$details['hidefromstables']){
					$stuffavailable++;
					output("Accessory name: %s`nCost: %s Requisition, %s Cigarettes`n%s`n`n",$details['displayname'],$details['goldcost'],$details['gemcost'],$details['description']);
					addnav(array("Buy %s",$details['displayname']),"runmodule.php?module=mountaccessories&op=buy&acc=".$acc);
				}
			}
			if ($stuffavailable == 0){
				output("There are no upgrades available for your Mount.  Feh.");
			}
			addnav("Return to the Stables","stables.php");
		break;
		case "buy":
			page_header("Mount Accessories");
			$acc=httpget("acc");
			if (!mountaccessories_takemoney($acc)){
				output("Merick shakes his head.  \"I dunno, you lot with yer bloody lack of a basic grasp on financial transactions...\"`n`nYou discover with a cringe of embarrassment that you don't actually have enough money to buy that accessory.");
			} else {
				give_accessory($acc);
				apply_accessory($acc);
				output("Merick takes your money, shows you a broad grin, and begins the process of equipping the accessory to your Mount.  Within a few minutes, his work is done, and your Mount is upgraded.");
			}
			addnav("Return to the Stables","stables.php");
		break;
	}
	page_footer();
	return true;
}
?>
