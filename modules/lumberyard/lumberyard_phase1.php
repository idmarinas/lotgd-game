<?php
function lumberyard_phase1(){
	global $session;
	output("`n`c`b`QT`qhe `QL`qumber `QY`qard `QP`qhase `Q1`0`c`b`n");
	$allprefs=unserialize(get_module_pref('allprefs'));
	$allprefs['phase']=2;
	$allprefs['usedlts']=$allprefs['usedlts']+1;
	set_module_pref('allprefs',serialize($allprefs));
	$session['user']['turns']--;
	//count how many orchard trees are available to be cut down
	if (is_module_active("orchard")){
		$sql = "SELECT acctid FROM ".db_prefix("accounts")."";
		$res = db_query($sql);
		$n=0;
		for ($i=0;$i<db_num_rows($res);$i++){
			$row = db_fetch_assoc($res);
			$allprefsorchard=unserialize(get_module_pref('allprefs','orchard',$row['acctid']));
			if ($allprefsorchard['tree']>1) $n=$n+1;
		}
	}
	$allprefs=unserialize(get_module_pref('allprefs'));
	if ($n<=10) $chopchance=0;
	elseif($n>10 && $n<=25) $chopchance=1;
	elseif($n>25 && $n<=100) $chopchance=2;
	elseif($n>100) $chopchance=3;
	output("`^You grab your axe to attack a tree with all your strength.`n`n");
	switch(e_rand(1,20)){
	//switch (18){
		case 1: case 2: case 3: case 4:
			if (is_module_active("orchard")){
				$allprefsorch=unserialize(get_module_pref('allprefs','orchard'));
				if ($allprefsorch['seed']==13 && get_module_setting("alloworchard")==1) redirect("runmodule.php?module=orchard&op=lumberyard");
			}
		case 5: case 6: case 7: case 8: case 9:
			output("`^You've got this down. With amazing efficiency you take down the tree and trim the branches. This was a very successful session.`n`n");
			output("You've completed `QPhase 1`^ of work in the lumber yard. It only took you `@one turn`^.`n`n");
			debuglog("completed phase 1 of the lumberyard.");
		break;
		case 10:
			$gems=get_module_setting("beargem");
			output("`^You find bear droppings. You try to avoid the droppings, but you suddenly notice that there's something shiny in it. `n`n You dig through the droppings and find `%%s %s `^and what appears to be a ring. You can't quite	figure out why a bear would eat a ring.",$gems,translate_inline($gems>1?"gems":"gem"));
			output("I mean, that's something that an adventurer would wear on his finger... `n`nOooohhhhhh!");
			output("You decide to be a little more careful in the lumber yard. `n`nYou are able to melt the ring down for gold. `n`nYou finish bringing the log to the mill and get `^`b100 ");
			if ($gems<=0) output("gold`b.");
			elseif ($gems>0) output("gold`b, and `%%s %s`^.",$gems,translate_inline($gems>1?"gems":"gem"));
			output("`n`n You `@Lose 2 turns`^; one for digging through bear droppings and one to complete `QPhase 1`^.`n`n You also `&lose 1 charm`^.");
			$session['user']['gold']+=100;
			$session['user']['gems']+=$gems;
			$session['user']['charm']--;
			debuglog("completed phase 1 of the lumberyard and gained 100 gold, $gems gems, and lost 1 turn.");
		break;
		case 11:
			output("`^As the tree falls, you realize you have no clue what you are doing and it falls right at you! You nimbly jump out of the way and smile at your good fortune.");
			output("`n`nYou take a swing to cut of the branches and you accidentally hit your leg!! Although you are able to save your leg by some very clever first aid, it causes unbelievable `4pain`^.");
			output("`n`nYou won't be able to finish this `QPhase`^ of work right now and you've been severely injured!`n`n");
			output("You `@lose one turn`^ and you barely survive with`$ 1 hitpoint`^!");
			$session['user']['hitpoints']=1;
			$allprefs['phase']=1;
			set_module_pref('allprefs',serialize($allprefs));
			debuglog("almost got crushed by a tree in the lumberyard and was left with 1 hitpoint.");
		break;
		case 12:
			output("`^Your efficiency is inspiring. You are able to cut the tree, trim it down, and transport it back to the mill. `n`nYou accomplish `QTwo Phases`^ in `@one turn`^!");
			$allprefs['phase']=3;
			set_module_pref('allprefs',serialize($allprefs));
			debuglog("completed phase 1 and 2 of the lumberyard all in one turn.");
		break;
		case 13:
			output("`^You chop the tree down and trim it just as planned. The work takes `@one turn`^ to complete.`n`nHowever, when you finish, you look around and see `%a gem`^ in the dirt.");
			$session['user']['gems']++;
			debuglog("completed phase 1 of the lumberyard and gained a gem.");
		break;
		case 14:
			$gold=$session['user']['gold'];
			if ($gold==0){
				output("`^You've got this down.");
				output("With amazing efficiency you take down the tree and trim the branches.");
				output("This was a very successful session.");
				debuglog("completed phase 1 of the lumberyard.");
			}else{
				output("`^You finish chopping the tree and trimming it.");
				output("`n`nHowever, your absent minded work causes you to lose");
				if ($gold<100){
					output("your money bag. You look around but can't find any of your `bgold`b!");
					$session['user']['gold']=0;
					debuglog("completed phase 1 of the lumberyard but lost about 100 gold.");
				}else{
					output("lose some of your gold. You find you've `blost 100 gold`b!");
					$session['user']['gold']-=100;
					debuglog("completed phase 1 of the lumberyard and lost 100 gold.");
				}
			}
			output("`n`nYou've completed `QPhase 1`^ of work in the lumber yard. It only took you `@one turn`^.`n`n");
		break;
		case 15:
			switch(e_rand(1,5)){
				case 1: case 2: case 3:
					output("`^You watch the tree start to fall and`b it's about to hit you! `b`n`nYou make a desperate jump and just barely get out of the way.");
					output("`n`nAfter brushing yourself off and counting all of your fingers, you take a deep breath and think about how fortune has smiled upon you.`n`n");
					output("You've completed `QPhase 1`^ of work in the lumber yard. It only took you `@one turn`^.`n`n");
					debuglog("completed phase 1 of the lumberyard.");
				break;
				case 4: case 5:
					$lumberturns=get_module_setting("lumberturns");
					$allprefs['phase']=1;
					$allprefs['usedlts']=$lumberturns;
					set_module_pref('allprefs',serialize($allprefs));
					$exploss = round($session['user']['experience']*.05);
					output("`^Well, this is just really bad luck. One of the trees falls on you. You almost die...`n`n");
					output("`^`bHalf your gold`b has been trapped under a tree!`n`n`b`^ You lose `#%s experience`^.`n`n",$exploss);
					output("`b`^ You are done working in the `QL`qumber `QY`qard`^ for today.`n`c`n`@You are `\$MOSTLY dead`@... Which means you're still really alive.`b`c");
					addnews("%s `@was found `\$MOSTLY dead `@under a tree in the `QL`qumber `QY`qard`@.  What a tree hugger!",$session['user']['name']);
					require_once("modules/lumberyard/lumberyard_blocknavs.php");
					lumberyard_blocknavs();
					$session['user']['experience']-=$exploss;
					$session['user']['hitpoints']=1;
					$session['user']['gold']*=.5;
					debuglog("was almost crushed by a tree, lost $exploss, 1/2 gold, and left with 1 hitpoint.");
				break;
			}
		break;
		case 16:
			$name=$session['user']['name'];
			output("You follow some beautiful shrubbery. Next to it is another shrubbery, only slightly higher so you get the two-level effect with a little path running down the middle.`n`n");
			output("This frightens you for some reason.  You decide to turn back, leaving the forest.`n`nOut of nowhere, a band of minstrels start following you.`n`n");
			output("`%Brave Sir %s `%ran away,`nBravely ran away, away.`n",$name);
			output("When danger reared its ugly head,`n %s `%bravely turned tail and fled.`n",$name);
			output("Yes, brave Sir %s `%turned about`nAnd gallantly, chickened out.`n Bravely taking to feet,`n",$name);
			output("%s `%beat a very brave retreat,`nBravest of the brave, Sir %s`%.`n`n",$name,$name);
			output("`^I think those minstrels will be following you around for a while, and you fail to complete any work in the lumber yard.`n`n");
			$allprefs['phase']=1;
			set_module_pref('allprefs',serialize($allprefs));
			apply_buff('robinminstrels',array(
				"name"=>"`5(Not so)`% Helpful Minstrels",
				"rounds"=>10,
				"wearoff"=>"`%Suspicous that the minstrels were mocking you, you send them away.",
				"atkmod"=>.95,
				"roundmsg"=>"`5The Minstrels sing`%' When danger reared its ugly head, you bravely turned your tail and fled!'",
			));
			debuglog("has some minstrels following him around from the lumberyard.");
		break;
		case 17:
			output("`^As you approach a tree, you are suddenly surrounded!`n`nYou fall to the ground paralyzed as a group of towering knights start shouting at you in a menacing tone!`n`n");
			output("`\$'NI! NI! NI!'`n`n`^However, you recover soon enough to realize that in actuality, they are more annoying than dangerous.");
			output("`n`nFor some odd reason, you pick up a `3herring`^ and chop down one of the trees in the forest.`n`nYou harness the power of `\$'NI' `^to attack other creatures!`n`n");
			output("You've completed `QPhase 1`^ of work in the lumber yard. It only took you `@one turn`^.`n`n");
			apply_buff('ni',array(
				"name"=>"`$`b NI! NI! NI!`b",
				"rounds"=>30,
				"wearoff"=>"`^You are No Longer a Knight that says`$ 'Ni'`^.  You now say`$ 'Ecky- ecky- ecky- ecky- pikang- zoop- boing- goodem- zoo- owli- zhiv'`^.",
				"atkmod"=>1.3,
				"roundmsg"=>"`$ NI! NI! NI! NI!",
			));
			debuglog("completed phase 1 of the lumberyard and gained the NI! NI! NI! buff.");
		break;
		case 18:
			//is the orchard active, are you allowed to chop down trees, and are there trees higher than apple available to be chopped down?
			if (is_module_active("orchard") && get_module_setting("chopop")==1 && $n>0) {
				//randomly choose a number out of the number of people that have above apple available to chop
				$t=0;
				$choptry=e_rand(1,$n);
				$sql = "SELECT acctid,name FROM ".db_prefix("accounts")."";
				$res = db_query($sql);
				for ($i=0;$i<db_num_rows($res);$i++){
					$row = db_fetch_assoc($res);
					$allprefstree=unserialize(get_module_pref('allprefs','orchard',$row['acctid']));
					if ($allprefstree['tree']>1){
						$t=$t+1;
						if ($t==$choptry){
							$id=$row['acctid'];
							$name=$row['name'];
						}
					}
				}
				$subj = array("Orchard Notice from `!Elendir");
				$body = array("`^Dear %s`^,`n`nWe regret to inform you that %s`^ was attempting to chop down one of your trees in the orchard. `n`n Luckily, `!Elendir `^stopped this injustice from happening.  The culprit has been fined `b200 Gold`b and this money has been put in your bank account for emotional compensation.`n`n  Best Wishes, `n`n `!Elendir",$name,$session['user']['name']);
				require_once("lib/systemmail.php");
				systemmail($id,$subj,$body);
				addnews("%s`^ was caught about to chop down one of %s`^'s trees in the orchard, but was stopped by `!Elendir",$session['user']['name'],$name);
				$sql = "UPDATE ". db_prefix("accounts") . " SET goldinbank=goldinbank+200 WHERE acctid='$id'";
				db_query($sql);
				output("`^You run into the forest and prepare to chop down a tree. As you raise your axe to swing, `!Elendir`^ runs up to you and warns you that you are about to chop down %s`^'s fruit tree!",$name);
				output("`n`nIn order to teach you to be more careful, `!Elendir`^ takes");
				if ($session['user']['gold']<200){
					output("all your gold");
					$session['user']['gold']=0;
				}else{
					output("200 gold from you");
					$session['user']['gold']-=200;
				}
				output("and gives it to %s`^.",$name);
				output("`n`n`^ You wonder off to cut down a NON-fruit tree. You `@lose one turn`^ finishing `QPhase 1`^.");
			}else{
				output("`^You've got this down. With amazing efficiency you take down the tree and trim the branches down. This was a very successful session.`n`n");
				output("You've completed `QPhase 1`^ of work in the lumber yard. It only took you `@one turn`^.`n`n");
			}
		break;
		case 19:
			if ($chopchance==0) $random=0;
			elseif ($chopchance==1) $random=15;
			elseif ($chopchance==2) $random=7;
			else $random=2;
			if ($random>0){
				$t=0;
				//randomly choose player from total number of trees available
				$choptry=e_rand(1,$n);
				$sql = "SELECT acctid,name FROM ".db_prefix("accounts")."";
				$res = db_query($sql);
				for ($i=0;$i<db_num_rows($res);$i++){
					$row = db_fetch_assoc($res);
					$allprefstree=unserialize(get_module_pref('allprefs','orchard',$row['acctid']));
					if ($allprefstree['tree']>1){
						$t=$t+1;
						if ($t==$choptry){
							$id=$row['acctid'];
							$name=$row['name'];
						}
					}
				}
				if (is_module_active("treeguard")){
					$allprefsguard=unserialize(get_module_pref('allprefs','treeguard',$id));
					if ($allprefsguard['ftime']>0) $random=$random*2;
					if ($allprefsguard['tguard']>0) $random=$random*5;
				}
			}
			$choprand=(e_rand(0,$random));
			if (is_module_active("orchard") && get_module_setting("chopop")==1 && $choprand==1) {
				$allprefs=unserialize(get_module_pref('allprefs'));
				$allprefs['fruitname']=$name;
				$allprefs['fruitid']=$id;
				set_module_pref('allprefs',serialize($allprefs));
				output("`^You come across %s`^'s fruit tree in the orchard. You look around for a moment and realize nobody is looking. You are suddenly faced with a dilemna.",$name);
				output("`n`n`\$To cut or not to cut!`^`n`nWhat would you like to do?");
				addnav("Chop the Fruit Tree","runmodule.php?module=lumberyard&op=chopfruit");
				addnav("Leave the Fruit Tree Alone","runmodule.php?module=lumberyard&op=savefruit");
				blocknav("forest.php");
				require_once("modules/lumberyard/lumberyard_blocknavs.php");
				lumberyard_blocknavs();
			}else{
				output("`^You've got this down. With amazing efficiency you take down the tree and trim the branches down. This was a very successful session.`n`n");
				output("You've completed `QPhase 1`^ of work in the lumber yard. It only took you `@one turn`^.`n`n");
			}
		break;
		case 20:
			if ($chopchance==0) $random=0;
			elseif ($chopchance==1) $random=20;
			elseif ($chopchance==2) $random=12;
			else $random=3;
			if ($random>0){
				$t=0;
				$choptry=e_rand(1,$n);
				$sql = "SELECT acctid,name FROM ".db_prefix("accounts")."";
				$res = db_query($sql);
				for ($i=0;$i<db_num_rows($res);$i++){
					$row = db_fetch_assoc($res);
					$allprefstree=unserialize(get_module_pref('allprefs','orchard',$row['acctid']));
					if ($allprefstree['tree']>1){
						$t=$t+1;
						if ($t==$choptry){
							$id=$row['acctid'];
							$name=$row['name'];
						}
					}
				}
				if (is_module_active("treeguard")){
					$allprefsguard=unserialize(get_module_pref('allprefs','treeguard',$id));
					if ($allprefsguard['ftime']>0) $random=$random*2;
					if ($allprefsguard['tguard']>0) $random=$random*5;
				}
			}
			$choprand=(e_rand(0,$random));
			if (is_module_active("orchard") && get_module_setting("chopop")==1 && $choprand==1) {
				$allprefsorchchop=unserialize(get_module_pref('allprefs','orchard',$id));
				$allprefsorchchop['bankkey']=0;
				$allprefsorchchop['mespiel']=0;
				$allprefsorchchop['menumb']=0;
				$allprefsorchchop['caspiel']=0;
				$allprefsorchchop['canumb']=0;
				$allprefsorchchop['bellyrub']=0;
				$allprefsorchchop['pegplay']=0;
				$allprefsorchchop['dragonseedage']=0;
				$allprefsorchchop['monsterid']="";
				$allprefsorchchop['monsterlevel']="";
				$allprefsorchchop['monstername']="";
				$allprefsorchchop['dietreehit']=0;
				$allprefsorchchop['dieingtree']=0;
				if ($allprefsorchchop['seed']>0) $allprefsorchchop['seed']=$allprefsorchchop['seed']-1;
				if ($allprefsorchchop['found']>0) $allprefsorchchop['found']=$allprefsorchchop['found']-1;
				if ($allprefsorchchop['tree']>0) $allprefsorchchop['tree']=$allprefsorchchop['tree']-1;
				set_module_pref('allprefs',serialize($allprefsorchchop),'orchard',$id);
				$subj = array("Orchard Notice from `!Elendir");
				$body = array("`^Dear %s`^,`n`nWe regret to inform you that %s`^ accidentally chopped down one of your trees in the orchard.`n`n You'll have to find a new seed to plant a new tree.`n`n  Best Wishes, `n`n `!Elendir",$name,$session['user']['name']);
				require_once("lib/systemmail.php");
				systemmail($id,$subj,$body);
				output("`^You run into the forest and vigorously chop down a tree.");
				output("You finish chopping down the tree and trimming it.");
				output("`n`n`\$You suddenly realize that this wasn't a lumber tree... this was `^%s`^'s `\$tree from the orchard!!",$name);
				output("`n`n`^ Oh no!");
				output("`!Elendir`^ sees you do it and sends a message to %s `^and puts a notice in the news... but the adrenaline rush gives you `@2 extra forest fights`^ and you finish `QPhase 1`^!!",$name);
				addnews("%s`^ accidentally chopped down one of %s`^'s trees in the orchard",$session['user']['name'],$name);
				$session['user']['turns']+=3;
				debuglog("accidentally chopped down one of $name's trees in the orchard.");
			}else{
				output("`^You've got this down. With amazing efficiency you take down the tree and trim the branches down. This was a very successful session.`n`n");
				output("You've completed `QPhase 1`^ of work in the lumber yard. It only took you `@one turn`^.`n`n");
				debuglog("completed phase 1 of the lumberyard.");
			}
		break;
	}
}
?>