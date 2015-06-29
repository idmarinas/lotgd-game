<?php
	global $session;
	$op = httpget('op');
	$page = httpget('page');
	page_header("Lumber Yard");
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
	$allprefse=unserialize(get_module_pref('allprefs',"lumberyard",$id));
	if ($allprefse['usedlts']=="") $allprefse['usedlts']=0;
	if ($allprefse['squares']=="") $allprefse['squares']=0;
	if ($allprefse['squareshof']=="") $allprefse['squareshof']=0;
	if ($allprefse['planthof']=="") $allprefse['planthof']=0;
	if ($allprefse['fruitname']=="") $allprefse['fruitname']="";
	if ($allprefse['fruitid']=="") $allprefse['fruitid']=0;
	set_module_pref('allprefs',serialize($allprefse),'lumberyard',$id);
	if ($subop!='edit'){
		$allprefse=unserialize(get_module_pref('allprefs',"lumberyard",$id));
		$allprefse['firstl']= httppost('firstl');
		$allprefse['usedlts']= httppost('usedlts');
		$allprefse['phase']= httppost('phase');
		$allprefse['squares']= httppost('squares');
		$allprefse['squareshof']= httppost('squareshof');
		$allprefse['planthof']= httppost('planthof');
		$allprefse['ccspiel']= httppost('ccspiel');
		$allprefse['fruitname']= httppost('fruitname');
		$allprefse['fruitid']= httppost('fruitid');
		set_module_pref('allprefs',serialize($allprefse),'lumberyard',$id);
		output("Allprefs Updated`n");
		$subop="edit";
	}
	if ($subop=="edit"){
		require_once("lib/showform.php");
		$form = array(
			"Lumber Yard,title",
			"firstl"=>"Has player ever been to the lumber yard?,bool",
			"usedlts"=>"How many turns did they use in the yard today?,int",
			"phase"=>"Phase of wood building (of 3) that player is on,range,1,3,1",
			"squares"=>"Number of squares player has,int",
			"squareshof"=>"Total number of squares ever cut,int",
			"planthof"=>"Total number of trees planted,int",
			"ccspiel"=>"Have they been informed of the current clearcut?,bool",
			"fruitname"=>"Name of the last person whose tree they could cut down,text",
			"fruitid"=>"ID of the last person whose tree they could cut down,text",
		);
		$allprefse=unserialize(get_module_pref('allprefs',"lumberyard",$id));
		rawoutput("<form action='runmodule.php?module=lumberyard&op=superuser&userid=$id' method='POST'>");
		showform($form,$allprefse,true);
		$click = translate_inline("Save");
		rawoutput("<input id='bsave' type='submit' class='button' value='$click'>");
		rawoutput("</form>");
		addnav("","runmodule.php?module=lumberyard&op=superuser&userid=$id");
	}
}
	//the settings
if ($op!="superuser"){
	$fullsize=get_module_setting("fullsize");
	$remainsize=get_module_setting("remainsize");
	$plantneed=get_module_setting("plantneed");
	$daygrowth=get_module_setting("daygrowth");
	$clearcutter=get_module_setting("clearcutter");
	$clearcut=get_module_setting("clearcut");
	$cccount=get_module_setting("cccount");
	$cutpercent=get_module_setting("cutpercent");
	$cutdown=get_module_setting("cutdown");
	$lumberturns=get_module_setting("lumberturns");
	$squarepay=get_module_setting("squarepay");
	
	$allprefs=unserialize(get_module_pref('allprefs'));
	$usedlts=$allprefs['usedlts'];
	$phase=$allprefs['phase'];
	$squares=$allprefs['squares'];
	$squarshof=$allprefs['squareshof'];
	
	$remaining=$plantneed-$remainsize;
	//this marks the forest as cutdown if people ever cut down all the trees
	if($remainsize<=0){
		set_module_setting("remainsize",0);
		set_module_setting("cutdown",1);
		set_module_setting("cccount",0);
	}
	//check to see if player has used more turns than allowed
	if($usedlts>=$lumberturns){
		$allprefs['usedlts']=get_module_setting("lumberturns");
		set_module_pref('allprefs',serialize($allprefs));
		$allprefs=unserialize(get_module_pref('allprefs'));
	}
	//check to see if the forest has more trees than allowed
	if($remainsize>$fullsize) set_module_setting("remainsize",get_module_setting("fullsize"));
	
	//check to see if the squarepay needs to be adjusted for levels
	if (get_module_setting("leveladj")==1) $squarepay=round($squarepay*$session['user']['level'] / 15);
}
if ($op=="enter") {
	$cutdown=get_module_setting("cutdown");
	$ccspiel=$allprefs['ccspiel'];
	if ($cutdown==1 && $ccspiel==0) {
		$allprefs['ccspiel']=1;
		debuglog("entered the lumberyard and heard the opening spiel.");
		set_module_pref('allprefs',serialize($allprefs));
		output("`n`$ `c`b Clear Cut Alert`b`n`n`c");
		output("`^Before you get a chance to get to work in `b`QT`qhe `QL`qumber `QY`qard`b`^, `@F`7oreman `@H`7anson`^ sadly asks you to come to his office.");
		output("`n`n  You walk up to the second floor to the office.`n`n`#'Remember how I warned you that sometimes that crazy man`b`$ %s `b`#comes and clearcuts the forest?",$clearcutter);
		output("Well, unfortunately it has happened. There are no trees left. You'll have to wait for the new ones to grow in.");
		output("However, you can help speed the process along by assisting with the planting.'`n`n'At this time, the yard should be ready once we've got `6 %s more %s`# planted.'`n`n",$remaining,translate_inline($remaining>1?"trees":"tree"));
		output("'You can reduce that number by one if you agree to help plant for 2 turns.'`n`n'Would you be able to give some of your time to plant more trees?'");
		if ($session['user']['turns']>1) addnav("Yes, I'll help","runmodule.php?module=lumberyard&op=clearyes");
		addnav("No, but show me my Ledger","runmodule.php?module=lumberyard&op=clearno");
		addnav("`@No, Back to the Forest","forest.php");
	}elseif ($cutdown==1 && $ccspiel==1) {
		if (get_module_setting("remainsize")>=get_module_setting("plantneed")) {
			set_module_setting("cutdown",0);
			set_module_setting("cccount",0);
			output("`c`b`QT`qhe `QL`qumber `QY`qard `@Open!`b`c`n`n");
			output("A smiling foreman comes to greet you.");
			output("'`#Well, `b`QT`qhe `QL`qumber `QY`qard`b `#is back in business!'`n`n");
			output("'Are you ready to go back to work the yard?'");
			addnav("Cut More Trees","runmodule.php?module=lumberyard&op=work");
			addnav("Plant Trees","runmodule.php?module=lumberyard&op=clearyes");
			addnav("No, but show me my Ledger","runmodule.php?module=lumberyard&op=office");
			addnav("`@No, Back to the Forest","forest.php");
		}elseif ($usedlts < $lumberturns) {
			output("`n`$ `c`b Clear Cut Alert`b`n`n`c");
			output("`^The foreman approaches you.");
			output("`n`n`# 'I'm sorry, but the lumber yard is not ready to be worked yet because of being clear cut.`n`n");
			output("At this time, the yard should be ready once we've got `6 %s more trees`# planted.'`n`n",$remaining);
			output("Would you be able to give 2 turns to plant more trees?'");
			if ($session['user']['turns']>1) addnav("Yes, I'll help","runmodule.php?module=lumberyard&op=clearyes");
			addnav("No, but show me my Ledger","runmodule.php?module=lumberyard&op=clearno");
			addnav("`@No, Back to the Forest","forest.php");
		}else{
			output("`c`n`b`$ Tree Shortage Alert`b`c`n`n");
			output("`#Thank you for your enthusiasm, but I think you've spent enough time in the `b`QT`qhe `QL`qumber `QY`qard`b`#.");
			output("`n`nPlease stop by to help tomorrow.`n`n");
			addnav("`@Back to the Forest","forest.php");
			if ($remainsize<$plantneed)	output("I think the yard should be ready once we've got `6 %s more trees`# planted.`n`n",$remaining);
		}
	}elseif ($plantneed>$remainsize){
		output("`n`$ `c`bLumber Yard Shortage Warning`b`n`n`c");
		output("`^Before you get a chance to get to work in `b`QT`qhe `QL`qumber `QY`qard`b`^, `@F`7oreman `@H`7anson`^ sadly asks you to come to his office.");
		output("`n`nYou walk up to the second floor to the office.`n`n`#'Remember how I warned you that if the yard isn't replenished with new trees often enough, it will be too detrimental to the forest to continue to cut them down?");
		output("Well, that's happened.'`n`n'There are only`6 %s %s `# remaining in the forest for cutting right now.'`n`n",$remainsize,translate_inline($remainsize>1?"trees":"tree"));
		output("'The yard should be ready once we've got `6 %s more %s`# planted.'`n`n",$remaining,translate_inline($remaining>1?"trees":"tree"));
		output("'You can reduce that number by one if you agree to help plant for 2 turns.'`n`n'Would you be able to give some of your time to plant more trees?'`n`n");
		output("'I'm not going to stop you from chopping trees though, but I have to warn you that it's not really very nice to chop trees down if there's a shortage.'`n`n");
		if ($session['user']['turns']>1) {
			addnav("Yes, I'll help","runmodule.php?module=lumberyard&op=planttree");
			addnav("I want to Chop","runmodule.php?module=lumberyard&op=evilchop");
		}
		addnav("No, but show me my Ledger","runmodule.php?module=lumberyard&op=shortoffice");
		addnav("`@No, Back to the Forest","forest.php");
	}else{
		output("`n`c`b`QT`qhe `QL`qumber `QY`qard`0`c`b`n");
		if ($allprefs['firstl']==1){
			output("`@F`7oreman `@N`7. `@H`7anson `^gives you a firm handshake.`n`n`#'Welcome back! Are you ready to get back to work?'`n`n");
			output("`^There are currently`6 %s `^trees available in the forest.`n`n",$remainsize);
			output("`^You now have`b`& %s `b%s of Wood`^.`n`n",$squares,translate_inline($squares>1?"Squares":"Square"));
			output("`^You are on `Q`bPhase %s`b`^.`n`n",$phase);
		}else{
			$allprefs['firstl']=1;
			debuglog("entered the Lumberyard Office and heard the rules.");
			set_module_pref('allprefs',serialize($allprefs));
			output("`^The foreman walks up to you and grabs your hand, giving you a firm handshake.");
			output("`n`n`#'Good day. I am `@F`7oreman `@N`7. `@H`7anson`#. Let me give you a tour of the facilities.'`n`n `^Curious, you follow along for the tour.`n`n");
			output("You are lead out to a section of the forest where all the trees are planted in beautiful rows.");
			output("`@F`7oreman `@H`7anson `^turns to you and shows you where the axes are stored. `#'That's the tour.'`n`n");
			output("`^You look at an axe and ask what this is about.`n`nThe foreman looks at you and shakes his head.");
			output("`#'Man, you really ARE new to this. Okay, here's how things work around here:'`n`n");
			require_once("modules/lumberyard/lumberyard_rules.php");
			lumberyard_rules();
		}
		require_once("modules/lumberyard/lumberyard_navs.php");
		lumberyard_navs();
	}
}
if ($op=="clearyes" || $op=="planttree") {
	require_once("modules/lumberyard/lumberyard_planttree.php");
	lumberyard_planttree();
}
if ($op=="shortoffice" || $op=="clearno") {
	require_once("modules/lumberyard/lumberyard_office.php");
	lumberyard_office();
}
if ($op=="clearcutsell" || $op=="shortsell"){
	addnav("`@To the forest","forest.php");
	addnav("Review Your Ledger","runmodule.php?module=lumberyard&op=clearno");
	require_once("modules/lumberyard/lumberyard_func.php");
	lumberyard_sellsquare();
}
if($op=="evilchop"){
	output("`c`n`b`$ Forest Tree Level Low`b`c`n");
	if ($session['user']['turns']<2){
		output("`#'You look too tired to plant trees.");
		output("Why don't you come back when you're feeling a little more energetic?'`n`n");
		addnav("`@Back to the Forest","forest.php");
	}elseif ($usedlts>=$lumberturns){
		output("`#'You've spent enough time in `b`QT`qhe `QL`qumber `QY`qard`b`#.");
		output("Try back tomorrow.'");
		addnav("`@To the Forest","forest.php");
	}elseif($remainsize<=0){
		output("`#'Well, there are no trees to chop down.");
		output("You can plant some though, or you can go back to what's left of the forest to destroy something else.'");
		addnav("`@To the Forest","forest.php");
		addnav("Plant Trees","runmodule.php?module=lumberyard&op=planttree");
	}else{
		output("'Well, it is against my advice for you to cut trees when the forest is running low, but hey, it's your choice.`n`n");
		debuglog("cut down a tree despite a tree shortage in the lumberyard.");
		addnav("Cut Trees","runmodule.php?module=lumberyard&op=work");
		switch(e_rand(1,7)){
			case 1: case 2: case 3:
				output("`^You walk past the foreman carrying your axe.");
				output("It looks like nobody else noticed.`n`n");
			break;
			case 4: case 5:
				output("`^This is really a bad idea.");
				output("An evil idea.");
				output("In fact, you feel a little`$ evil`^ for going forward with it.");
				addnews("%s `$ decided that cutting down trees is a right, even when the forest is low on trees.",$session['user']['name']);
				if (is_module_active('alignment')) increment_module_pref("alignment",-get_module_setting("alignevil"),"alignment");
				debuglog("cut down a tree despite a tree shortage in the lumberyard.");
			break;
			case 6: case 7:
				$gold=$session['user']['gold'];
				$name=$session['user']['name'];
				output("`^You boldly walk past the townsfolk that are helping to plant the trees and go to chop some down.`n`n");
				if ($gold==0){
					addnews("%s `$ decided that cutting down trees is a right, even when the forest is low on trees.",$name);
				}else{
					output("`^Before you get very far, the taxman taps you on the shoulder.`n`n");
					output("`#'You realize that this forest is currently regulated because of the declining numbers of the native insect;");
					if ($gold<150){
						output("the spike-headed katydid. We currently have a tax to help restore their habitat, and I think I'll just take all your money and call us even.'`n`n");
						addnews("%s `$ decided that cutting down trees is a right and endangered the rare spike-headed katydid. There was no consideration for the fact that the forest is low on trees.",$name);
						$session['user']['gold']=0;
					}else{
						output("baby red-blue eagles. We are currently have a tax to help restore their habitat, and today it's going to cost you `^150 gold `#to take down that tree.'`n`n");
						addnews("%s `$ decided that cutting down trees is a right and probably killed some baby eagles.  There was no consideration for the fact that the forest is low on trees.",$name);
						$session['user']['gold']-=150;	
					}
				}
			break;
		}
	}
}
if($op=="rules"){
	output("`n`c`b`QT`qhe `QL`qumber `QY`qard `QR`qules`0`c`b`n");
	output("`@F`7oreman `@H`7anson `^walks you through the `QT`qhe `QL`qumber `QY`qard `QR`qules`^:`n`n");
	require_once("modules/lumberyard/lumberyard_rules.php");
	lumberyard_rules();
	require_once("modules/lumberyard/lumberyard_navs.php");
	lumberyard_navs();
	blocknav("runmodule.php?module=lumberyard&op=rules");
}
if($op=="chopfruit"){
	output("`n`c`b`QT`qhe `QL`qumber `QY`qard`0`c`b`n");
	$name=$allprefs['fruitname'];
	$id=$allprefs['fruitid'];
	output("`^With an`$ evil`^ glint in your eye, you chop down `@%s`^'s tree.",$name);
	output("Ah the sweet smell of fruit tree!`n`n You feel more`$ evil`^, but in a self satisifying good way.`n`n`^You `@lose one turn`^ finishing `QPhase 1`^.",$name);
	if (is_module_active('alignment')) increment_module_pref("alignment",-get_module_setting("fruitalign"),"alignment");
	$allprefso=unserialize(get_module_pref('allprefs','orchard',$id));
	$allprefso['bankkey']=0;
	$allprefso['mespiel']=0;
	$allprefso['menumb']=0;
	$allprefso['caspiel']=0;
	$allprefso['canumb']=0;
	$allprefso['bellyrub']=0;
	$allprefso['pegplay']=0;
	$allprefso['dragonseedage']=0;
	$allprefso['monsterid']="";
	$allprefso['monsterlevel']="";
	$allprefso['monstername']="";
	$allprefso['dietreehit']=0;
	$allprefso['dieingtree']=0;
	if ($allprefso['seed']>0) $allprefso['seed']=$allprefso['seed']-1;
	if ($allprefso['found']>0) $allprefso['found']=$allprefso['found']-1;
	if ($allprefso['tree']>0) $allprefso['tree']=$allprefso['tree']-1;
	debuglog("cut down $name's tree in the lumberyard.");
	set_module_pref('allprefs',serialize($allprefso),'orchard',$id);
	$subj = array("Orchard Notice from `!Elendir");
	$body = array("`^Dear %s`^,`n`nWe regret to inform you that someone chopped down one of your trees in your orchard. We don't know who it was.  `n`nYou'll have to find a new seed to plant a new tree.`n`n  Best Wishes, `n`n `!Elendir",$name);
	require_once("lib/systemmail.php");
	systemmail($id,$subj,$body);
	addnews("`^Somebody intentionally chopped down one of %s`^'s trees in the orchard",$name);
	require_once("modules/lumberyard/lumberyard_navs.php");
	lumberyard_navs();
}
if($op=="savefruit"){
	output("`n`c`b`QT`qhe `QL`qumber `QY`qard`0`c`b`n");
	$name=$allprefs['fruitname'];
	$id=$allprefs['fruitid'];
	$allprefs['phase']=1;
	debuglog("was going to cut down $name's tree but decided not to.");
	set_module_pref('allprefs',serialize($allprefs));
	output("`^After carefully studying %s`^'s tree, you decide that there's no way you can chop it down.`n`n",$name);
	output("You feel like a `@better person`^, and your day is happier. `!Elendir`^ notices your good deed and decides to tell everyone about what a good person you are.`n`n");
	output("`^Unfortunately, you `@lose one turn`^ and you aren't able to finish this phase.`n`n");
	if (is_module_active('alignment')) increment_module_pref("alignment",get_module_setting("fruitalign"),"alignment");
	$subj = array("Orchard Notice from `!Elendir");
	$body = array("`^Dear %s`^,`n`nWe are very happy to inform you that %s `^ spared your fruit tree from being chopped down.  Maybe you should send %s`^ some`4 BBQ potato chips`^ or something for being so nice.`n`n  Best Wishes, `n`n `!Elendir",$name,$session['user']['name'],$session['user']['name']);
	require_once("lib/systemmail.php");
	systemmail($id,$subj,$body);
	addnews("%s`^ spared one of %s`^'s trees in the orchard! Hurray for good people!",$session['user']['name'],$name);
	require_once("modules/lumberyard/lumberyard_navs.php");
	lumberyard_navs();
}
if($op=="work"){
	if ($phase>=4 || $phase<=0) {
		$allprefs['phase']=1;
		set_module_pref('allprefs',serialize($allprefs));
	}
	$allprefs=unserialize(get_module_pref('allprefs'));
	$usedlts=$allprefs['usedlts'];
	$phase=$allprefs['phase'];
	require_once("modules/lumberyard/lumberyard_navs.php");
	lumberyard_navs();
	if ($session['user']['turns']<1){
		output("`n`c`b`QT`qhe `QL`qumber `QY`qard`0`c`b`n");
		output("`#Whoa there.");
		output("You're too exhausted to work the `b`QT`qhe `QL`qumber `QY`qard`b`#.");
		output("Why don't you try again when you've got the strength to do some heavy labor.");
	}elseif ($usedlts>=$lumberturns){
		output("`n`c`b`QT`qhe `QL`qumber `QY`qard`0`c`b`n");
		output("`#'You've spent enough time in `b`QT`qhe `QL`qumber `QY`qard`b`#.");
		output("Try back tomorrow.'");
	}elseif ($phase==1){
		require_once("modules/lumberyard/lumberyard_phase1.php");
		lumberyard_phase1();
	}elseif ($phase==2){
		require_once("modules/lumberyard/lumberyard_phase2.php");
		lumberyard_phase2();
	}elseif ($phase==3){
		require_once("modules/lumberyard/lumberyard_phase3.php");
		lumberyard_phase3();
	}
}
if ($op=="attack") {
	$allprefs=unserialize(get_module_pref('allprefs'));
	$phase=$allprefs['phase'];
	if ($phase==3){
		//bear
		$level = $session['user']['level']-1;
		if ($level<=0) $level=1;
		$name=translate_inline("`qG`^reat `qB`^ig `qB`^ear");
		$weapon=translate_inline("`@its`% Claws");
		$badguy = array(
			"creaturename"=>$name,
			"creaturelevel"=>$level,
			"creatureweapon"=>$weapon,
			"creatureattack"=>$session['user']['attack'],
			"creaturedefense"=>$session['user']['defense'],
			"creaturehealth"=>$session['user']['maxhitpoints'],0,
			"diddamage"=>0,
			"type"=>"bear");
		$session['user']['badguy']=createstring($badguy);
		$op="fight";
	}elseif ($phase==1){
		//lumberjack
		$level = $session['user']['level'];
		$name=translate_inline("a `@B`4u`@r`4l`@y `4L`@u`4m`@b`4e`@r`4j`@a`4c`@k");
		$weapon=translate_inline("`@a `%Razor Sharp Axe");
		$badguy = array(
			"creaturename"=>$name,
			"creaturelevel"=>$level,
			"creatureweapon"=>$weapon,
			"creatureattack"=>round($session['user']['attack']*1.2),
			"creaturedefense"=>round($session['user']['defense']*1.2),
			"creaturehealth"=>round($session['user']['maxhitpoints']*1.2,0),
			"diddamage"=>0,
			"type"=>"lumberjack");
		$session['user']['badguy']=createstring($badguy);
		$op="fight";
	}
}
//start here
if ($op=="fight"){ $battle=true; }
if ($battle){
	include("battle.php");
	if ($victory){
		$allprefs=unserialize(get_module_pref('allprefs'));
		$phase=$allprefs['phase'];
		if ($phase==3){
			//bear
			$expbonus=$session['user']['dragonkills']*4;
			$expgain =($session['user']['level']*34+$expbonus);
			output("`n`@`bYou've gained `#%s experience`@.`b`n`n",$expgain);
			output("`bThe adrenaline rush allows you to finish`Q Phase 2`@ and you `2don't lose a turn`@!`b`n`n");
			if(is_module_active("bearhof")) increment_module_pref("bearkills",1,"bearhof");
		}elseif ($phase==1){
			//lumberjack
			$expbonus=$session['user']['dragonkills']*6;
			$expgain =($session['user']['level']*45+$expbonus);
			output("`n`b`@You've gained `#%s experience`@.`b`b`n`n",$expgain);
			output("`b`@The adrenaline rush allows you to finish `QPhase 3`@ and you `2don't lose a turn`@!`n`n");
			output("`^You now have`& %s Squares of Wood`^.`n`nThere are now`6 %s trees `^left in the forest.`n`n",$squares,$remainsize);
		}
		$session['user']['turns']++;
		$session['user']['experience']+=$expgain;
		require_once("modules/lumberyard/lumberyard_navs.php");
		lumberyard_navs();
    }elseif($defeat){
		require_once("lib/taunt.php");
		$exploss = round($session['user']['experience']*.1);
		$session['user']['experience']-=$exploss;
		$session['user']['gold']=0;
		$allprefs=unserialize(get_module_pref('allprefs'));
		$phase=$allprefs['phase'];
		if ($phase==3){
			//bear
			$taunt = select_taunt_array();
			output("`n`n`b`4You can't `Q'bear'`4 to think how you got killed`n`4You `Q'lumbered' `4right into that beast`n`n");
			output("`4All `^gold on hand has been lost!`n`4You lose `#%s experience`4.`n`n",$exploss);
			output("`@Obviously you can't finish dragging the tree to the mill. You may try again tomorrow.`b`n");
			addnews("%s `@was hugged to death by a `Q`bbear`b`@!`n%s",$session['user']['name'],$taunt);
			addnav("Daily news","news.php");
			$allprefs['phase']=2;
			$session['user']['alive'] = false;
			$session['user']['hitpoints'] = 0;
			debuglog("was defeated by a bear in the lumberyard.");
		}elseif ($phase==1){
			//lumberjack
			output("`n`n`b`$ The Lumber Jack steals your `^gold `4and leaves you for dead, but you fool him by not dying!`b`n`n");
			output("`@`bYou figure you've had enough of the Lumber Yard for the day.`b");
			addnews("%s `@was pwned by a lumberjack!",$session['user']['name']);
			addnav("`@To the forest","forest.php");
			$allprefs['phase']=3;
			$allprefs['squares']=$allprefs['squares']-1;
			$allprefs['squareshof']=$allprefs['squareshof']-1;
			$allprefs['usedlts']=get_module_setting("lumberturns");
			increment_module_setting("remainsize",1);
			$session['user']['hitpoints'] = 1;
			debuglog("was defeated by a lumberjack in the lumberyard.");
		}
		set_module_pref('allprefs',serialize($allprefs));
	}else{
		require_once("lib/fightnav.php");
		fightnav(true,false,"runmodule.php?module=lumberyard");
	}
}
if($op=="office"){
	require_once("modules/lumberyard/lumberyard_basicoffice.php");
	lumberyard_basicoffice();
}
if ($op=="squaresell"){
	require_once("modules/lumberyard/lumberyard_navs.php");
	lumberyard_navs();
	require_once("modules/lumberyard/lumberyard_func.php");
	lumberyard_sellsquare();
}
if ($op == "squareshof" || $op=="planthof") {
	require_once("modules/lumberyard/lumberyard_hof.php");
	lumberyard_hof();
}
page_footer();
?>