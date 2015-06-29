<?php
	global $session,$badguy;
	$op = httpget('op');
	page_header("The Quarry");
	$ruler=get_module_setting("ruler");
	$blockpay = get_module_setting("blockpay");
	if (get_module_setting("leveladj")==1) $blockpay=round($blockpay*$session['user']['level']/15);
	require_once("modules/quarry/quarry_func.php");
if ($op=="superuser"){
	require_once("modules/allprefseditor.php");
	allprefseditor_search();
	page_header("Allprefs Editor");
	$subop=httpget('subop');
	$id=httpget('userid');
	addnav("Navigation");
	addnav("Return to the Grotto","superuser.php");
	villagenav();
	addnav("Edit user","user.php?op=edit&userid=$id");
	modulehook('allprefnavs');
	$allprefse=unserialize(get_module_pref('allprefs',"quarry",$id));
	if ($allprefse['usedqts']=="") $allprefse['usedqts']=0;
	if ($allprefse['blocks']=="") $allprefse['blocks']=0;
	if ($allprefse['blockshof']=="") $allprefse['blockshof']=0;
	if ($allprefse['gianthof']=="") $allprefse['gianthof']=0;
	set_module_pref('allprefs',serialize($allprefse),'quarry',$id);
	if ($subop!='edit'){
		$allprefse=unserialize(get_module_pref('allprefs',"quarry",$id));
		$allprefse['firstq']= httppost('firstq');
		$allprefse['usedqts']= httppost('usedqts');
		$allprefse['blocks']= httppost('blocks');
		$allprefse['blockshof']= httppost('blockshof');
		$allprefse['gianthof']= httppost('gianthof');
		$allprefse['insured']= httppost('insured');
		$allprefse['sgfought']= httppost('sgfought');
		set_module_pref('allprefs',serialize($allprefse),'quarry',$id);
		output("Allprefs Updated`n");
		$subop="edit";
	}
	if ($subop=="edit"){
		require_once("lib/showform.php");
		$form = array(
			"Quarry,title",
			"firstq"=>"Has player ever been to the quarry?,bool",
			"usedqts"=>"How many times did they quarry today?,int",
			"blocks"=>"Number of blocks player has,int",
			"blockshof"=>"Total number of blocks ever cut,int",
			"gianthof"=>"Total number of giants ever killed,int",
			"insured"=>"Does player have Death Insurance for today?, bool",
			"sgfought"=>"Has the player fought a stone giant during the siege today?,bool",
		);
		$allprefse=unserialize(get_module_pref('allprefs',"quarry",$id));
		rawoutput("<form action='runmodule.php?module=quarry&op=superuser&userid=$id' method='POST'>");
		showform($form,$allprefse,true);
		$click = translate_inline("Save");
		rawoutput("<input id='bsave' type='submit' class='button' value='$click'>");
		rawoutput("</form>");
		addnav("","runmodule.php?module=quarry&op=superuser&userid=$id");
	}
}
//If the quarry is tied to the lost ruins:
if ($op!="superuser" && $op!="blockshof" && $op!="gianthof" && $op!="work"){
	if (is_module_active('lostruins') && get_module_setting("usequarry")==0) {
		if (get_module_setting("blocksleft")==round((get_module_setting("blockmin"))/2)) {
			addnews("%s`% discovered that `3T`@he `3Q`@uarry`% is under siege by `)S`&tone `)G`&iants`%!",$session['user']['name']);
			debuglog ("was the first to notice the quarry was under siege.");
			set_module_setting("underatk",1);
			increment_module_setting("blocksleft",-3);
			set_module_setting("giantleft",get_module_setting("numbgiant"));
		}
		if (get_module_setting("blocksleft")<=0) {
			if (get_module_setting("newsclosed")==0){
				addnews("`n`@T`3he %s`& `@Q`3uarry `@o`3f `@G`3reat `@S`3tone `@in the village of %s `@has run out of stone and has been `\$closed`@.`n",get_module_setting("quarryfinder"),get_module_setting("quarryloc"));
				set_module_setting("newsclosed",1);
				debuglog("discovered that the quarry was going to be closed.");
				$sql = "SELECT acctid FROM ".db_prefix("accounts")."";
				$res = db_query($sql);
				for ($i=0;$i<db_num_rows($res);$i++){
					$row = db_fetch_assoc($res);
					$allprefs=unserialize(get_module_pref('allprefs','quarry',$row['acctid']));
					$allprefs['firstq']=0;
					set_module_pref('allprefs',serialize($allprefs),'quarry',$row['acctid']);
				}
				set_module_setting("quarryfound",0,"lostruins");
			}
			output("`n`c`b`@T`3he %s `@Q`3uarry`c`b`n",get_module_setting("quarryfinder"));
			output("`^'I'm so sorry, but this `@Q`3uarry`^ is empty.  It's going to be closed down.'`n`n'Until a new one opens up, you won't be able to sell any of your `)Blocks of Stone`^ once you leave, so please consider selling them now if you're interested.'`n");
			blocknav("runmodule.php?module=quarry&op=work");
		}
	}
}
if ($op=="enter") {
	require_once("modules/quarry/quarry_enter.php");
	quarry_enter();
}
elseif($op=="rules"){
	require_once("modules/quarry/quarry_rules.php");
	quarry_rules();
}
elseif($op=="work"){
	require_once("modules/quarry/quarry_work.php");
	quarry_work();
}
elseif ($op=="burn") {
	if (is_module_active('alignment')) increment_module_pref("alignment",-3,"alignment");
	output("`n`c`b`\$Burning Ants`c`b`n");
	output("`n`%You nudge the children and grab the magnifying glass. `n`nYou feel a little more `\$evil`%.`n`n Before you know it, you've got 5 ants on fire all at once! `n`nThe 'Ooohs' and 'Ahhhs' of the children make you feel `&more charming`%!!");
	$session['user']['charm']+=1;
	require_once("modules/quarry/quarry_func.php");
	quarry_quarrynavs();
}
elseif ($op=="wander") {
	if (is_module_active('alignment')){
		if (get_module_pref("alignment","alignment")>=get_module_setting("goodalign","alignment")) increment_module_pref("alignment",-3,"alignment");
		elseif (get_module_pref("alignment","alignment")<=get_module_setting("evilalign","alignment")) increment_module_pref("alignment",+3,"alignment");
	}
	output("`n`c`b`^Wander Off`c`b`n");
	output("`%This really isn't your problem.  You feel a little more `^neutral`% about the world.`n`n  However, as wander off, you look down to spot a `bgem`b!! How cool!`n`n");
	$session['user']['gems']+=1;
	debuglog("found a gem after wandering away from a problem in the quarry.");
	require_once("modules/quarry/quarry_func.php");
	quarry_quarrynavs();
}
elseif ($op=="stopthem") {
	if (is_module_active('alignment')) set_module_pref("alignment",get_module_pref("alignment","alignment")+3,"alignment");
	output("`n`c`b`@Save the Ants`c`b`n");
	output("`n`%You rescue those poor little ants! You push the dumb punky kids out of the way and save the colony!`n`n The ants notice how you've saved them and bring up a bottle of wonderful elixir. `n`n ");
	if ($session['user']['hitpoints']<$session['user']['maxhitpoints']){
		$session['user']['hitpoints']=$session['user']['maxhitpoints'];
		output("You drink it down and your `\$hitpoints are restored to full`%!!");
		debuglog("saved some ants from punky kids in the quarry and restored hitpoints to full.");
	}else{
		$session['user']['maxhitpoints']+=1;
		output("You drink it down and `\$gain one permanent hitpoint`%!!");
		debuglog("saved some ants from punky kids in the quarry and gained a permanent hitpoint.");
	}
	require_once("modules/quarry/quarry_func.php");
	quarry_quarrynavs();
}
elseif ($op=="private"){
	output("`n`c`b`&T`)he `&S`)ecret `&O`)rder `&o`)f `&M`)asons`c`b`n");
	output("`7'We are very honored to offer you membership into our `&S`)ecret `&O`)rder`7.  If you would like to join, you will find our hidden location revealed to you in %s.  Go there and present this `&I`)nvitation `&S`)croll`7.  You will receive further instructions when you get there.'`n`n",get_module_setting("masonsloc","masons"));
	output("`%You happily accept the scroll.`n`n`7'Just one warning.  Our offer is for today only.  If you don't show up, you may not get another chance.'`7");
	$allprefsm=unserialize(get_module_pref('allprefs','masons'));
	$allprefsm['offermember']=1;
	set_module_pref('allprefs',serialize($allprefsm),'masons');
	debuglog("was given an invitation to join the Masons Order.");
	require_once("modules/quarry/quarry_func.php");
	quarry_quarrynavs();
	if (is_module_active('lostruins') && get_module_setting("usequarry")==0) {
		increment_module_setting("blocksleft",-1);
		if (get_module_setting("blocksleft")<10) output("`n`n`@T`3he `@Q`3uarry`% is looking low on stone and may have to be shut down soon.`n`n");
	}
}
elseif($op=="office"){
	$allprefs=unserialize(get_module_pref('allprefs'));
	if ($allprefs['insured']==0) addnav("Purchase `)D`\$eath `)I`\$nsurance`^","runmodule.php?module=quarry&op=insurance");
	if ($allprefs['usedqts']<get_module_setting("quarryturns")) addnav("Work the Quarry","runmodule.php?module=quarry&op=work");
	addnav("Review the Rules","runmodule.php?module=quarry&op=rules");
	addnav("V?(V) Return to Village","village.php");
	output("`n`b`c`@Q`3uarry `@O`3ffice`b`c`n");
	output("`&Slatemaker `\$S`7h`&y`\$l`7l`&e`% takes off her hat and sits down behind the desk.`^`n`n");
	if (get_module_setting("blocksleft")>0) {
		if ($allprefs['insured']==0) output("'We are currently offering `)D`\$eath `)I`\$nsurance`^ in case you die in `@Q`3uarry`^ for the low low price of`b %s gold`b.'",get_module_setting("insurecost"));
		output ("'Insurance will pay out `b%s gold`b and `%%s gems`^ if you are killed in the quarry.  The insurance runs out tomorrow.'`n`n",
		get_module_setting("inspaygold"),get_module_setting("inspaygems"));
		output("`\$You  ");
		if ($allprefs['insured']==0)  output("`^ do not ");
		output("`\$have a valid `)D`\$eath `)I`\$nsurance `)P`\$olicy.`n`n");
	}
	if (get_module_setting("blocksleft")<=0) {
		blocknav("runmodule.php?module=quarry&op=rules");
		blocknav("runmodule.php?module=quarry&op=insurance");
	}
	if (get_module_setting("blocksleft")<=0)output("`^'There are `b`)No More Blocks of Stone`b`^ left to quarry.  `@T`3he `@Q`3uarry`^ is `\$Closed`^.'`n`n");
	output("`%She reviews your ledger:`n`n");
	output("`^'Let's see, under`0 %s`^... I have you listed for the following:`n`n",$session['user']['name']);
	if (get_module_setting("blocksleft")>0){
		output("`^You've spent`@ %s out of %s turns`^ working in the `@Q`3uarry`^.`n`n",$allprefs['usedqts'],get_module_setting("quarryturns"));
		if (is_module_active('lostruins') && get_module_setting("usequarry")==0) output("`^There are about `b`)%s`b Blocks of Stone`^ left to quarry.`n`n",get_module_setting("blocksleft"));
	}
	$blocks=$allprefs['blocks'];
	if  ($blocks==0) $blocks=translate_inline("Zero");
	output("`^You now have`b`) %s `b%s of Stone`^.`n`n", $blocks,translate_inline($blocks<>1?"Blocks":"Block"));

	if ($blocks>=1){
		$levelreq=get_module_setting("levelreq");
		if (($levelreq>1 && $session['user']['level']>=$levelreq) || $levelreq==1){
			$maximumsell=get_module_setting("maximumsell");
			if (($maximumsell>0 && $allprefs['stonesold']<$maximumsell) || $maximumsell==0){
				output("`^Currently, the best I can offer you for a `)Block of Stone`^ is`b %s gold`b.",$blockpay);
				if ($maximumsell>0){
					$left=$maximumsell-$allprefs['stonesold'];
					output("Remember, you can sell up to `&%s `)%s`^ per day, and",$maximumsell,translate_inline($maximumsell>1?"blocks":"block"));
					if ($allprefs['stonesold']==0) output("you haven't sold any today yet.");
					else output("you've already sold `&%s `)%s`^ today; meaning you can only sell `&%s`^ more today.",$allprefs['stonesold'],translate_inline($allprefs['stonesold']>1?"blocks":"block"),$left);
					if ($left>$blocks) $left=$blocks;
					addnav(array("Sell %s %s Stone",translate_inline($left>1?"All":""), $left),"runmodule.php?module=quarry&op=blocksell&op2=$left");
				}else addnav(array("Sell %s %s Stone",translate_inline($blocks>1?"All":""),$blocks),"runmodule.php?module=quarry&op=blocksell&op2=$blocks");
				output("`n`n'How many would you like to sell?'");
				output("<form action='runmodule.php?module=quarry&op=blocksell' method='POST'><input name='sell' id='sell'><input type='submit' class='button'value='sell'></form>",true);
				addnav("","runmodule.php?module=quarry&op=blocksell");
			}else{
				output("`^Unfortunately, you've sold your maximum `&%s`^ `)Blocks of Stone`^ today already.  Please come back tomorrow.'",$maximumsell);
			}
		}else{
			output("`^Unfortunately, you need to be at least level `&%s`^ to sell any `)stone`^.  Feel free to come back when you've advanced.'",$levelreq);
		}
	}else{
		output("`^'Since you don't have any blocks to sell, you'll probably want to get to work in the quarry.'`n`n`&Slatemaker `\$S`7h`&y`\$l`7l`&e `%closes the ledger and leads you to the door.`n`n");
	}
}
elseif ($op=="insurance"){
	$allprefs=unserialize(get_module_pref('allprefs'));
	addnav("V?(V) Return to Village","village.php");
	if ($allprefs['usedqts']<get_module_setting("quarryturns")) addnav("Work the Quarry","runmodule.php?module=quarry&op=work");
	addnav("Review the Rules","runmodule.php?module=quarry&op=rules");
	addnav("Back to the Office","runmodule.php?module=quarry&op=office");
	output("`n`b`c`)D`\$eath `)I`\$nsurance`b`c`n");
	if ($session['user']['gold']<get_module_setting("insurecost")){
		output("`^'Although I appreciate your interest in our `)D`\$eath `)I`\$nsurance`^, I really think you should at least have the money to purchase a policy before thinking we will cover you.  Stop back when you've got at least`b %s gold`b.'",get_module_setting("insurecost"));
	}else{
		output("`^'Well, I think this was a really good idea.  Not that I think you'll die.  Err... I'm sure you'll be perfectly fine.`n`nJust remember, the insurance only works if you die in the `@Q`3uarry`^.'");
		$allprefs['insured']=1;
		set_module_pref('allprefs',serialize($allprefs));
		$session['user']['gold']-=get_module_setting("insurecost");
		debuglog("purchased insurance from the quarry.");
	}
}
elseif ($op=="blocksell"){
	$op2 = httpget('op2');
	$allprefs=unserialize(get_module_pref('allprefs'));
	if ($allprefs['usedqts']<get_module_setting("quarryturns")) addnav("Work the Quarry","runmodule.php?module=quarry&op=work");
	addnav("Review the Rules","runmodule.php?module=quarry&op=rules");
	addnav("Back to the Office","runmodule.php?module=quarry&op=office");
	output("`n`b`c`@Q`3uarry `@O`3ffice`b`c`n");
	$sell = httppost('sell');
	if ($op2>0) $sell=$op2;
	if (get_module_setting("maximumsell")>0){
		$max=get_module_setting("maximumsell")-$allprefs['stonesold'];
		if ($max>$allprefs['blocks']) $max=$allprefs['blocks'];
	}else $max = $allprefs['blocks'];
	if ($sell < 0) $sell = 0;
	if ($sell >= $max) $sell = $max;
	if ($max < $sell) {
		output("`&Slatemaker `\$S`7h`&y`\$l`7l`&e`% looks at you bewildered.  `^'You know you don't have that many blocks!'`n`n");
	}else{
		$cost=($sell * $blockpay);
		$session['user']['gold']+=$cost;
		$allprefs['blocks']=$allprefs['blocks']-$sell;
		$allprefs['stonesold']=$allprefs['stonesold']+$sell;
		set_module_pref('allprefs',serialize($allprefs));
		increment_module_setting("stonesold",$sell);
		output("`&Slatemaker `\$S`7h`&y`\$l`7l`&e`% gives you `^`b%s gold`b `%in return for`) %s %s`%.",$cost,$sell,translate_inline($sell>1?"blocks":"block"));
		debuglog("sold $sell blocks of stone for $blockpay each to collect $cost gold.");
	}
}
elseif ($op == "blockshof" || $op == "gianthof") {
	require_once("modules/quarry/quarry_hof.php");
	quarry_hof();
}
if ($op=="stonec"){
	$dkb = round($session['user']['dragonkills']*.05);
	$name=translate_inline("`)Falling `&rocks");
	$weapon=translate_inline("`0terribly sharp `&edges");
	$badguy = array(
		"creaturename"=>$name,
		"creaturelevel"=>$session['user']['level'],
		"creatureweapon"=>$weapon,
		"creatureattack"=>$session['user']['attack'],
		"creaturedefense"=>$session['user']['defense'],
		"creaturehealth"=>round($session['user']['maxhitpoints']*1.1),
		"diddamage"=>0,
		"type"=>"debri");
	$session['user']['badguy']=createstring($badguy);
	$op="fight";
	$start=translate_inline("`n`^A small avalanche of rocks falls onto you!`n");
	$bname=translate_inline("`)A`&valanche!");
	$woff=translate_inline("`n`&The larger rocks keep falling.");
	$nodmg=translate_inline("`&You nimbly dodge one of the rocks.");
	apply_buff('littlerocks', array(
		"startmsg"=>$start,
		"name"=>$bname,
		"rounds"=>1,
		"wearoff"=>$woff,
		"minioncount"=>$session['user']['level'],
		"mingoodguydamage"=>0,
		"maxgoodguydamage"=>1+$dkb,
		"effectmsg"=>"`)You are hit by a rock for `\${damage}`) damage.",
		"effectnodmgmsg"=>$nodmg,
		"effectfailmsg"=>$nodmg,
	));
	$session['user']['badguy']=createstring($badguy);
	$op="fight";
}
if ($op=="avalanche"){
	$dkb = round($session['user']['dragonkills']*.07);
	$name=translate_inline("a `)Falling `&Boulder");
	$weapon=translate_inline(" `)crushing weight");
	$badguy = array(
		"creaturename"=>$name,
		"creaturelevel"=>$session['user']['level']+1,
		"creatureweapon"=>$weapon,
		"creatureattack"=>round($session['user']['attack']*1.1),
		"creaturedefense"=>round($session['user']['defense']*1.1),
		"creaturehealth"=>round($session['user']['maxhitpoints']*.5),
		"diddamage"=>0,
		"type"=>"stonefall");
	$smsg=translate_inline("`n`^A small avalanche of rocks falls onto you!`n");
	$bname=translate_inline("`)A`&valanche!");
	$woff=translate_inline("`n`&The larger rocks keep falling.");
	$nodmg=translate_inline("`&You nimbly dodge one of the rocks.");
	apply_buff('littlerocks', array(
		"startmsg"=>$smsg,
		"name"=>$bname,
		"rounds"=>1,
		"wearoff"=>$woff,
		"minioncount"=>$session['user']['level']+5,
		"mingoodguydamage"=>0,
		"maxgoodguydamage"=>1+$dkb,
		"effectmsg"=>"`)You are hit by a rock for `\${damage}`) damage.",
		"effectnodmgmsg"=>$nodmg,
		"effectfailmsg"=>$nodmg,
	));
	$session['user']['badguy']=createstring($badguy);
	$op="fight";
}
if ($op=="bear"){
	$name=translate_inline("`qG`^reat `qB`^ig `qB`^ear");
	$weapon=translate_inline("`@its`% Claws");
	$badguy = array(
		"creaturename"=>$name,
		"creaturelevel"=>$session['user']['level']+1,
		"creatureweapon"=>$weapon,
		"creatureattack"=>round($session['user']['attack']*.85),
		"creaturedefense"=>round($session['user']['defense']*1.3),
		"creaturehealth"=>round($session['user']['maxhitpoints']),
		"diddamage"=>0,
		"type"=>"bear");
	$session['user']['badguy']=createstring($badguy);
	$op="fight";
}
if ($op=="fossil"){
	$name=translate_inline("`^T`Qhe `^F`Qossil `^D`Qinosaur");
	$weapon=translate_inline("`^B`Qrittle `^C`Qlaws");
	$badguy = array(
		"creaturename"=>$name,
		"creaturelevel"=>$session['user']['level']+1,
		"creatureweapon"=>$weapon,
		"creatureattack"=>round($session['user']['attack']*1.1),
		"creaturedefense"=>round($session['user']['defense']*.7),
		"creaturehealth"=>round($session['user']['maxhitpoints']),
		"diddamage"=>0,
		"type"=>"fosdino");
	$session['user']['badguy']=createstring($badguy);
	$op="fight";
}
if ($op=="lobster"){
	$name=translate_inline("`4The `)Rock `4Lobster");
	$weapon=translate_inline("`)Rockin `4and `)Rollin `4Claws");
	$badguy = array(
		"creaturename"=>$name,
		"creaturelevel"=>$session['user']['level']+1,
		"creatureweapon"=>$weapon,
		"creatureattack"=>round($session['user']['attack']-2),
		"creaturedefense"=>round($session['user']['defense']*.95),
		"creaturehealth"=>round($session['user']['maxhitpoints']*.93),
		"diddamage"=>0,
		"type"=>"rocklobster");
	$session['user']['badguy']=createstring($badguy);
	$op="fight";
}
if ($op=="smallgiant"){
	$dkb = round($session['user']['dragonkills']*.07);
	$name=translate_inline("`)S`&mall `)S`&tone `)G`&iant");
	$weapon=translate_inline("`0a pretty good sized boulder");
	$badguy = array(
		"creaturename"=>$name,
		"creaturelevel"=>$session['user']['level']+1,
		"creatureweapon"=>$weapon,
		"creatureattack"=>round($session['user']['attack']*.9),
		"creaturedefense"=>round($session['user']['defense']*.9),
		"creaturehealth"=>round($session['user']['maxhitpoints']*.9+2),
		"diddamage"=>0,
		"type"=>"smallstonegiant");
	$smsg=translate_inline("`n`^A small avalanche of rocks falls onto you!`n`n");
	$bname=translate_inline("`)A`&valanche!");
	$woff=translate_inline("The barrage of small rocks ends.");
	$ndmg==translate_inline("`&You nimbly dodge one of the rocks.");
	apply_buff('littlerocks', array(
		"startmsg"=>$smsg,
		"name"=>$bname,
		"rounds"=>1,
		"wearoff"=>$woff,
		"minioncount"=>$session['user']['level'],
		"mingoodguydamage"=>0,
		"maxgoodguydamage"=>1+$dkb,
		"effectmsg"=>"`)You are hit by a rock for `\${damage}`) damage.",
		"effectnodmgmsg"=>$ndmg,
		"effectfailmsg"=>$ndmg,
	));
	$session['user']['badguy']=createstring($badguy);
	$op="fight";
}
if ($op=="medgiant"){
	$dkb = round($session['user']['dragonkills']*.08);
	$name=translate_inline("`)M`&edium `)S`&tone `)G`&iant");
	$weapon=translate_inline("`0a pretty good sized boulder");
	$badguy = array(
		"creaturename"=>$name,
		"creaturelevel"=>$session['user']['level'],
		"creatureweapon"=>$weapon,
		"creatureattack"=>round($session['user']['attack']),
		"creaturedefense"=>round($session['user']['defense']),
		"creaturehealth"=>round($session['user']['maxhitpoints']+4),
		"diddamage"=>0,
		"type"=>"mediumstonegiant");
	$smsg=translate_inline("`n`^A small avalanche of rocks falls onto you!`n`n");
	$bname=translate_inline("`)A`&valanche!");
	$woff=translate_inline("The barrage of small rocks ends.");
	$ndmg==translate_inline("`&You nimbly dodge one of the rocks.");
	apply_buff('littlerocks', array(
		"startmsg"=>$smsg,
		"name"=>$bname,
		"rounds"=>1,
		"wearoff"=>$woff,
		"minioncount"=>$session['user']['level']+1,
		"mingoodguydamage"=>0,
		"maxgoodguydamage"=>1+$dkb,
		"effectmsg"=>"`)You are hit by a rock for `\${damage}`) damage.",
		"effectnodmgmsg"=>$ndmg,
            "effectfailmsg"=>$ndmg,
	));
	$session['user']['badguy']=createstring($badguy);
	$op="fight";
}
if ($op=="largegiant"){
	$dkb = round($session['user']['dragonkills']*.09);
	$name=translate_inline("`)L`&arge `)S`&tone `)G`&iant");
	$weapon=translate_inline("`0a large boulder");
	$badguy = array(
		"creaturename"=>$name,
		"creaturelevel"=>$session['user']['level']+1,
		"creatureweapon"=>$weapon,
		"creatureattack"=>round($session['user']['attack']*1.1),
		"creaturedefense"=>round($session['user']['defense']*1.1),
		"creaturehealth"=>round($session['user']['maxhitpoints']*1.1+5,0),
		"diddamage"=>0,
		"type"=>"largestonegiant");
	$smsg=translate_inline("`n`^A small avalanche of rocks falls onto you!`n`n");
	$bname=translate_inline("`)A`&valanche!");
	$woff=translate_inline("The barrage of small rocks ends.");
	$ndmg==translate_inline("`&You nimbly dodge one of the rocks.");
	apply_buff('littlerocks', array(
		"startmsg"=>$smsg,
		"name"=>$bname,
		"rounds"=>1,
		"wearoff"=>$woff,
		"minioncount"=>$session['user']['level'],
		"mingoodguydamage"=>0,
		"maxgoodguydamage"=>2+$dkb,
		"effectmsg"=>"`)You are hit by a rock for `\${damage}`) damage.",
		"effectnodmgmsg"=>$ndmg,
		"effectfailmsg"=>$ndmg,
	));
	$session['user']['badguy']=createstring($badguy);
	$op="fight";
}
if ($op=="hugegiant"){
	$dkb = round($session['user']['dragonkills']*.1);
	$name=translate_inline("`)H`&uge `)S`&tone `)G`&iant");
	$weapon=translate_inline("`0an unbelievably huge boulder");
	$badguy = array(
		"creaturename"=>$name,
		"creaturelevel"=>$session['user']['level'],
		"creatureweapon"=>$weapon,
		"creatureattack"=>round($session['user']['attack']*1.15),
		"creaturedefense"=>round($session['user']['defense']*1.15),
		"creaturehealth"=>round($session['user']['maxhitpoints']*1.15+10,0),
		"diddamage"=>0,
		"type"=>"hugestonegiant");
	$smsg=translate_inline("`n`^A small avalanche of rocks falls onto you!`n`n");
	$bname=translate_inline("`)A`&valanche!");
	$woff=translate_inline("The barrage of small rocks ends.");
	$ndmg==translate_inline("`&You nimbly dodge one of the rocks.");
	apply_buff('littlerocks', array(
		"startmsg"=>$smsg,
		"name"=>$bname,
		"rounds"=>1,
		"wearoff"=>$woff,
		"minioncount"=>$session['user']['level']+1,
		"mingoodguydamage"=>0,
		"maxgoodguydamage"=>2+$dkb,
		"effectmsg"=>"`)You are hit by a rock for `\${damage}`) damage.",
		"effectnodmgmsg"=>$ndmg,
		"effectfailmsg"=>$ndmg,
	));
	$session['user']['badguy']=createstring($badguy);
	$op="fight";
}

if ($op=="fight" or $op=="run"){
	global $badguy;
	$battle=true;
	$fight=true;
	if ($battle){
		require_once("battle.php");
		if ($victory){
			if ($badguy['type']=="debri"){
				$expbonus=$session['user']['dragonkills']*2;
				$expgain =($session['user']['level']*17+$expbonus);
				$session['user']['experience']+=$expgain;
				output("`n`%You made it! You were able to fight through the avalanche to safety!`n`n");
				output("`@`bYou've gained `#%s experience`@.`b`n`n",$expgain);
				output("`%The adrenaline rush allows you to finish your work in the quarry`@ without losing a turn`%!`n`n");
				output("You complete your work on `)One Block of Stone`%.`n`n ");
				debuglog("fought through an avalanche to complete a block of stone in the quarry.");
				$session['user']['turns']++;
				quarry_completeblock();
				quarry_quarrynavs();
			}elseif ($badguy['type']=="stonefall"){
				$expbonus=$session['user']['dragonkills']*3;
				$expgain =($session['user']['level']*19+$expbonus);
				$session['user']['experience']+=$expgain;
				$gemenum=get_module_setting("caseb");
				if ($gemenum==1) $gemfind=(e_rand(1,2));
				if ($gemenum==2) $gemfind=(e_rand(2,3));
				if ($gemenum==3) $gemfind=(e_rand(3,5));
				if ($gemenum==4) $gemfind=(e_rand(5,10));
				output("`n`%You made it! Your fancy pick-axe work makes light of the huge boulder!`n`n");
				output("`@`bYou've gained `#%s experience`@.`b`n`n",$expgain);
				output("`%The adrenaline rush allows you to finish your work in the quarry`@ without losing a turn`%!`n`n");
				output("You complete your work on `)One Block of Stone`%.`n`n You also find `b%s gem%s`b in the debris.`n`n",$gemfind,translate_inline($gemfind>1?"s":""));
				$session['user']['turns']++;
				$session['user']['gems']+=$gemfind;
				debuglog("destroyed a boulder a boulder to finish a block of stone, gain $expgain experience, gain a turn, and gain $gemfind gems.");
				quarry_completeblock();
				quarry_quarrynavs();
			}elseif ($badguy['type']=="bear"){
				$expbonus=$session['user']['dragonkills']*4;
				$expgain =($session['user']['level']*39+$expbonus);
				$session['user']['experience']+=$expgain;
				output("`n`%Silly `qB`^ear`%! When will you ever learn?  `n`nYou `@don't lose a turn `%for working in the `@Q`3uarry`%.`n");
				output("`n`@`bYou've gained `#%s experience`@.`b`n`n",$expgain);
				$session['user']['turns']++;
				debuglog("defeated a bear in the quarry and gained $expgain experience.");
				if(is_module_active("bearhof")) increment_module_pref("bearkills",1,"bearhof");
				quarry_completeblock();
				quarry_quarrynavs();
			}elseif ($badguy['type']=="fosdino"){
				$expbonus=$session['user']['dragonkills']*5;
				$expgain =($session['user']['level']*27+$expbonus);
				$session['user']['experience']+=$expgain;
				$gemenum=get_module_setting("casef");
				if ($gemenum==1) $gemfind=(e_rand(1,2));
				if ($gemenum==2) $gemfind=(e_rand(2,3));
				if ($gemenum==3) $gemfind=(e_rand(3,5));
				if ($gemenum==4) $gemfind=(e_rand(5,10));
				output("`n`%The `^F`Qossil `^D`Qinosaur`% crumbles before your might! You complete work on the `)Block`% and suddenly notice `b%s gem%s`b that constituted the heart of the monster!`n",$gemfind,translate_inline($gemfind>1?"s":""));
				output("`n`@`bYou've gained `#%s experience`@.`b`n`n",$expgain);
				debuglog("defeated a Fossil Dinosaur to gain $expgain experience and $gemfind gems.");
				$session['user']['gems']+=$gemfind;
				quarry_completeblock();
				quarry_quarrynavs();
			}elseif ($badguy['type']=="rocklobster"){
				$expbonus=$session['user']['dragonkills']*9;
				$expgain =($session['user']['level']*20+$expbonus);
				$session['user']['experience']+=$expgain;
				output("`n`%The `)Rock `4Lobster`% ends his dance! You complete work on `)One Block`%.`n");
				output("`n`@`bYou've gained `#%s experience`@.`b`n`n",$expgain);
				debuglog("defeated a Rock Lobster and gained $expgain experience.");
				quarry_completeblock();
				quarry_quarrynavs();
			}elseif ($badguy['type']=="smallstonegiant"){
				$expbonus=$session['user']['dragonkills']*7;
				$expgain =($session['user']['level']*35+$expbonus);
				$session['user']['experience']+=$expgain;
				output("`n`%You defeat the `)S`&mall `)S`&tone `)G`&iant`% to help save `@T`2he `@Q`3uarry`% and gain `@an extra turn`%.`n");
				output("`n`@`bYou've gained `#%s experience`@.`b`n`n",$expgain);
				debuglog("defeated a small giant in the quarry to gain $expgain exp and a turn.");
				$session['user']['turns']++;
				quarry_giantkill();
			}elseif ($badguy['type']=="mediumstonegiant"){
				$expbonus=$session['user']['dragonkills']*9;
				$expgain =($session['user']['level']*37+$expbonus);
				$session['user']['experience']+=$expgain;
				output("`n`%You defeat the `)M`&edium `)S`&tone `)G`&iant`% to help save `@T`2he `@Q`3uarry`% and gain `@two extra turns`%.`n");
				output("`n`@`bYou've gained `#%s experience`@.`b`n`n",$expgain);
				debuglog("defeated a medium giant in the quarry to gain $expgain exp and two turns.");
				$session['user']['turns']+=2;
				quarry_giantkill();
			}elseif ($badguy['type']=="largestonegiant"){
				$expbonus=$session['user']['dragonkills']*11;
				$expgain =($session['user']['level']*42+$expbonus);
				$session['user']['experience']+=$expgain;
				output("`n`%You defeat the `)L`&arge `)S`&tone `)G`&iant`% to help save `@T`2he `@Q`3uarry`% and gain `@three extra turns`%.`n");
				output("`n`@`bYou've gained `#%s experience`@.`b`n`n",$expgain);
				debuglog("defeated a large giant in the quarry to gain $expgain exp and three turns.");
				$session['user']['turns']+=3;
				quarry_giantkill();
			}elseif ($badguy['type']=="hugestonegiant"){
				$expbonus=$session['user']['dragonkills']*13;
				$expgain =($session['user']['level']*45+$expbonus);
				$session['user']['experience']+=$expgain;
				output("`n`%With a huge triumphant cry, you defeat the `)H`&uge `)S`&tone `)G`&iant`% to help save `@T`2he `@Q`3uarry`% and gain `@four extra turns`%.`n");
				output("`n`%Other quarry workers sing your praises and report your deed to the kingdom.`n");
				addnews("%s`% defeated one of the `)H`&uge `)S`&tone `)G`&iants `% ransacking `@T`3he `@Q`3uarry`%!!",$session['user']['name']);
				output("`n`@`bYou've gained `#%s experience`@.`b`n`n",$expgain);
				debuglog("defeated a huge giant in the quarry to gain $expgain exp and four turns.");
				$session['user']['turns']+=4;
				quarry_giantkill();
			}
		}elseif ($defeat){
			require_once("lib/taunt.php");
			$taunt = select_taunt_array();
			$allprefs=unserialize(get_module_pref('allprefs'));
			if ($badguy['type']=="debri"){
				$exploss = round($session['user']['experience']*.1);
				$session['user']['experience']-=$exploss;
				output("`n`%You become buried under the avalanche of pointy rocks.`n");
				output("`b`^All gold on hand has been lost!`b`n");
				output("`b`%You lose `#%s experience`4.`b`n`n",$exploss);
				if ($allprefs['insured']==1) addnews("%s `%was crushed by falling rocks in `@T`3he `@Q`3uarry`%.  Luckily, %s`% had purchased `)D`\$eath `)I`\$nsurance`% and didn't lose everything!",$session['user']['name'],translate_inline($session['user']['sex']?"she":"he"));
				else addnews("%s `%was crushed by falling rocks in `@T`3he `@Q`3uarry`%.",$session['user']['name']);
				debuglog("was killed by an avalanche in the quarry, losing $exploss experience.");
				quarry_dead();
			}elseif ($badguy['type']=="stonefall"){
				$exploss = round($session['user']['experience']*.08);
				$session['user']['experience']-=$exploss;
				output("`n`%You become buried under the huge boulder.`n");
				output("`b`^All gold on hand has been lost!`b`n");
				output("`b`%You lose `#%s experience`4.`b`n`n",$exploss);
				if ($allprefs['insured']==1) addnews("%s `%was crushed by a huge boulder in `@T`3he `@Q`3uarry`%.  Luckily, %s`% had purchased `)D`\$eath `)I`\$nsurance`% and didn't lose everything!",$session['user']['name'],translate_inline($session['user']['sex']?"she":"he"));
				else addnews("%s `%was crushed by a huge boulder in `@T`3he `@Q`3uarry`%.",$session['user']['name']);
				debuglog("was killed by a huge boulder in the quarry, losing $exploss experience.");
				quarry_dead();
			}elseif ($badguy['type']=="bear"){
				$exploss = round($session['user']['experience']*.1);
				$session['user']['experience']-=$exploss;
				output("`n`n`b`%You can't `Q'bear'`% to think how you got killed...`b`n");
				output("`b`%He really `)'rocked'`% your world!`b`n");
				output("`b`^All gold on hand has been lost!`b`n");
				output("`b`%You lose `#%s experience`4.`b`n`n",$exploss);
				if ($allprefs['insured']==1) addnews("%s `%received a `Qbear hug`% in `@T`3he `@Q`3uarry`%... to death!  Luckily, %s`% had purchased `)D`\$eath `)I`\$nsurance`% and didn't lose everything!",$session['user']['name'],translate_inline($session['user']['sex']?"she":"he"));
				else addnews("%s `%received a `Qbear hug`% in `@T`3he `@Q`3uarry`%... to death!",$session['user']['name']);
				quarry_dead();
			}elseif ($badguy['type']=="fosdino"){
				$exploss = round($session['user']['experience']*.07);
				$session['user']['experience']-=$exploss;
				output("`n`n`b`%The `^F`Qossil `^D`Qinosaur`% sends you to the bone yard...`b`n");
				output("`b`%Now you've become the fossil.  Isn't it Ironic?`b`n");
				output("`b`^All gold on hand has been lost!`b`n");
				output("`b`%You lose `#%s experience`4.`b`n`n",$exploss);
				if ($allprefs['insured']==1) addnews("%s `%was fossilized by a `^F`Qossil `^D`Qinosaur`% in `@T`3he `@Q`3uarry`%...  Luckily, %s`% had purchased `)D`\$eath `)I`\$nsurance`% and didn't lose everything!",$session['user']['name'],translate_inline($session['user']['sex']?"she":"he"));
				else addnews("%s `%was fossilized by a `^F`Qossil `^D`Qinosaur`% in `@T`3he `@Q`3uarry`%.",$session['user']['name']);
				debuglog("was killed by a Fossil Dinosaur in the quarry, losing $exploss experience.");
				quarry_dead();
			}elseif ($badguy['type']=="rocklobster"){
				$exploss = round($session['user']['experience']*.07);
				$session['user']['experience']-=$exploss;
				output("`n`n`b`%The `)Rock `4Lobster`% dances all over your grave.`b`n");
				output("`b`^All gold on hand has been lost!`b`n");
				output("`b`%You lose `#%s experience`4.`b`n`n",$exploss);
				debuglog("was killed by a Rock Lobster in the quarry, losing $exploss experience.");
				if ($allprefs['insured']==1) addnews("%s `%was stepped on by a `)Rock `4Lobster`% in `@T`3he `@Q`3uarry`%...  Luckily, %s`% had purchased `)D`\$eath `)I`\$nsurance`% and didn't lose everything!",$session['user']['name'],translate_inline($session['user']['sex']?"she":"he"));
				else addnews("%s `%was stepped on by a `)Rock `4Lobster`% in `@T`3he `@Q`3uarry`%.",$session['user']['name']);
				quarry_dead();
			}elseif ($badguy['type']=="smallstonegiant"){
				$exploss = round($session['user']['experience']*.085);
				$session['user']['experience']-=$exploss;
				output("`n`n`b`%The `)S`&mall `)S`&tone `)G`&iant`% pulverizes you to death.`b`n");
				output("`b`^All gold on hand has been lost!`b`n");
				output("`b`%You lose `#%s experience`4.`b`n`n",$exploss);
				debuglog("was killed by a Small Stone Giant in the quarry, losing $exploss experience.");
				quarry_giantdead();
				addnews("%s `%was killed by the `)S`&mall `)S`&tone `)G`&iant`% terrorizing `@T`3he `@Q`3uarry`%.`n%s",$session['user']['name'],$taunt);
			}elseif ($badguy['type']=="mediumstonegiant"){
				$exploss = round($session['user']['experience']*.085);
				$session['user']['experience']-=$exploss;
				output("`n`n`b`%The `)M`&edium `)S`&tone `)G`&iant`% pulverizes you to death.`b`n");
				output("`b`^All gold on hand has been lost!`b`n");
				output("`b`%You lose `#%s experience`4.`b`n`n",$exploss);
				debuglog("was killed by a Medium Stone Giant in the quarry, losing $exploss experience.");
				quarry_giantdead();
				addnews("%s `%was killed by the `)M`&edium `)S`&tone `)G`&iant`% terrorizing `@T`3he `@Q`3uarry`%.`n%s",$session['user']['name'],$taunt);
			}elseif ($badguy['type']=="largestonegiant"){
				$exploss = round($session['user']['experience']*.085);
				$session['user']['experience']-=$exploss;
				output("`n`n`b`%The `)L`&arge `)S`&tone `)G`&iant`% pulverizes you to death.`b`n");
				output("`b`^All gold on hand has been lost!`b`n");
				output("`b`%You lose `#%s experience`4.`b`n`n",$exploss);
				debuglog("was killed by a Large Stone Giant in the quarry, losing $exploss experience.");
				quarry_giantdead();
				addnews("%s `%was killed by the `)L`&arge `)S`&tone `)G`&iant`% terrorizing `@T`3he `@Q`3uarry`%.`n%s",$session['user']['name'],$taunt);
			}elseif ($badguy['type']=="hugestonegiant"){
				$exploss = round($session['user']['experience']*.085);
				$session['user']['experience']-=$exploss;
				output("`n`n`b`%The `)L`&arge `)S`&tone `)G`&iant`% pulverizes you to death.`b`n");
				output("`b`^All gold on hand has been lost!`b`n");
				output("`b`%You lose `#%s experience`4.`b`n`n",$exploss);
				debuglog("was killed by a Huge Stone Giant in the quarry, losing $exploss experience.");
				quarry_giantdead();
				addnews("%s `%was killed by the `)H`&uge `)S`&tone `)G`&iant`% terrorizing `@T`3he `@Q`3uarry`%.`n%s",$session['user']['name'],$taunt);
			}
		}else{
			require_once("lib/fightnav.php");
			fightnav(true,false,"runmodule.php?module=quarry");
		}
	}
}
page_footer();
?>