<?php
function quarry_work(){
	global $session;
	require_once("modules/quarry/quarry_func.php");
	$ruler=get_module_setting("ruler");
	$allprefs=unserialize(get_module_pref('allprefs'));
	quarry_quarrynavs();
	if (is_module_active('lostruins') && get_module_setting("usequarry")==0) output("`n`c`b`@T`3he %s `@Q`3uarry`c`b`n",get_module_setting("quarryfinder"));
	else output("`n`c`b`@T`3he `@Q`3uarry`c`b`n");
	if (get_module_setting("blocksleft")<=0) {
		if (get_module_setting("newsclosed")==0){
			addnews("`n`@T`3he %s`& `@Q`3uarry `@o`3f `@G`3reat `@S`3tone `@in the village of %s `@has run out of stone and has been `\$closed`@.`n",get_module_setting("quarryfinder"),get_module_setting("quarryloc"));
			set_module_setting("newsclosed",1);
			$sql = "SELECT acctid FROM ".db_prefix("accounts")."";
			$res = db_query($sql);
			for ($i=0;$i<db_num_rows($res);$i++){
				$row = db_fetch_assoc($res);
				$allprefsclosed=unserialize(get_module_pref('allprefs','quarry',$row['acctid']));
				$allprefsclosed['firstq']=0;
				set_module_pref('allprefs',serialize($allprefsclosed),'quarry',$row['acctid']);
			}
			set_module_setting("quarryfound","","lostruins");
		}
		debuglog("discovered that the quarry was going to be closed.");
		output("`^'I'm so sorry, but this `@Q`3uarry`^ is empty.  It's going to be closed down.'`n`n'Until a new one opens up, you won't be able to sell any of your `)Blocks of Stone`^ once you leave, so please consider selling them now if you're interested.'`n");
		blocknav("runmodule.php?module=quarry&op=work");
	}elseif ($session['user']['turns']<1){
		output("`%Whoa there.  You're too exhausted to work the quarry.  Why don't you try again when you've got the strength for some heavy labor?");
		blocknav("runmodule.php?module=quarry&op=work");
	}elseif ($allprefs['usedqts']>=get_module_setting("quarryturns")){
		output("`%'You've spent enough time working `@T`3he `@Q`3uarry`%.  Try back tomorrow.'");
		blocknav("runmodule.php?module=quarry&op=work");
	}else{
		output("`%You grab your hardhat and get to work.`n`n");
		$allprefs['usedqts']=$allprefs['usedqts']+1;
		set_module_pref('allprefs',serialize($allprefs));
		$allprefs=unserialize(get_module_pref('allprefs'));
		$session['user']['turns']--;
		switch(e_rand(1,30)){
		//switch(20){
			case 1: case 2: case 3: 
				$allprefso=unserialize(get_module_pref('allprefs','orchard'));
				if ($allprefso['seed']==15 && get_module_setting("alloworchard")==1){
					debuglog("went to collect a lime seed from the quarry.");
					redirect("runmodule.php?module=orchard&op=quarry");
					break;
				}
			case 4: case 5: case 6: case 7:	case 8:
				output("Despite your best efforts to get a decent `)block of stone`%, you`b`\$ Fail`b`%.`n`n`@T`3he `@Q`3uarry`% is a lot harder to work than you thought.");
				debuglog("tried to collect a block of stone in the quarry but failed.");
				if (is_module_active('lostruins') && get_module_setting("usequarry")==0 && get_module_setting("blocksleft")<10) output("`n`n`@T`3he `@Q`3uarry`% is looking low on stone and may have to be shut down soon.`n`n");
			break;
			case 9: case 10: case 11: case 12:
				output("Not bad.  Not bad at all. You finish `)One Block of Stone`% in `@one turn`%!`n`n");
				debuglog("spent a turn collecting a block of stone in the quarry.");
				quarry_completeblock();	
			break;
			case 13:
				$gemenum=get_module_setting("case13ge");
				if ($gemenum==1) $gemfind=(e_rand(1,2));
				elseif ($gemenum==2) $gemfind=(e_rand(2,3));
				elseif ($gemenum==3) $gemfind=(e_rand(3,5));
				elseif ($gemenum==4) $gemfind=(e_rand(5,10));
				output("You carve out `)One Block of Stone`% in `@one turn`%.`n`nWhen you finish, you notice that the stone that you carved it out of has a natural `\$p`^o`@c`#k`!e`%t `\$o`^f `@g`#e`!m`%s`\$!`%`n`n");
				output("You collect `b%s gem%s`b!!`n`n",$gemfind,translate_inline($gemfind>1?"s":""));
				$session['user']['gems']+=$gemfind;
				debuglog("spent a turn collecting a block of stone in the quarry and found $gemfind gems.");
				quarry_completeblock();
			break;
			case 14:
				output("You finish the work and get a credit for `)One Block of Stone`%.`n`n  But the work was invigorating and you get to spend an extra turn in `@T`3he `@Q`3uarry`% if you would like to, in addition to `@2 extra forest fights`%!`n`n");
				$allprefs['usedqts']=$allprefs['usedqts']-1;
				set_module_pref('allprefs',serialize($allprefs));
				$session['user']['turns']+=3;
				debuglog("spent a turn collecting a block of stone in the quarry and gained 3 turns and an extra quarry turn.");
				quarry_completeblock();	
			break;
			case 15:
				output("Confused and disoriented by the heat, you end up sitting down and grabbing a `#bottle of water`% to drink.`n`n You notice that there's something funny about the taste.`#`n`n");
				switch(e_rand(1,3)){
					case 1:
						output("Tasty!  And gives you a nice little hitpoint boost!");
						$session['user']['hitpoints']+=20;
						debuglog("spent a turn in the quarry and gained 20 hitpoints");
					break;
					case 2:
						if ($session['user']['hitpoints']==1) {
							output("Nope... it's just water.  Ha! you thought something interesting was going to happen!");
							debuglog("spent a turn in the quarry.");
						}else{
							output("Eww! There's a worm on the bottom of the bottle! That just turns your stomach, and causes you to
								`\$lose some hitpoints`#!");
							if ($session['user']['hitpoints']<=20) {
								$session['user']['hitpoints']=1;
								debuglog("spent a turn in the quarry and lost all hitpoints but one.");
							}else{
								$session['user']['hitpoints']-=20;
								debuglog("spent a turn in the quarry and lost 20 hitpoints.");
							}
						}
					break;
					case 3:
						output("Nope... it's just water.  Ha! you thought something interesting was going to happen!");
						debuglog("spent a turn in the quarry.");
					break;
				}
				output("`n`n`%Unfortunately, you aren't able to complete `)Block of Stone`% this turn.`n`n");
				if (is_module_active('lostruins') && get_module_setting("usequarry")==0 && get_module_setting("blocksleft")<10) output("`n`n`@T`3he `@Q`3uarry`% is looking low on stone and may have to be shut down soon.`n`n");
			break;
			case 16:
				output("You stand before your finished `)Block of Stone`% and give it a little tap proving how impressive you are. The `)stone `%falls on top of you.`n`n  You`$ lose all hitpoints`% except `\$one`%, but you were able to finish the `)stone!");
				$session['user']['hitpoints']=1;
				debuglog("spent a turn collecting a block of stone in the quarry and lost all hitpoints but one.");
				quarry_completeblock();	
			break;
			case 17:
				output("You wander around aimlessly pretending you are a teamster.  Since you aren't, you don't get any work done and don't get paid, either.`n`nSo surly!!");
				debuglog("spent a turnin the quarry being surly.");
				if (is_module_active('lostruins') && get_module_setting("usequarry")==0 && get_module_setting("blocksleft")<10) output("`n`n`@T`3he `@Q`3uarry`% is looking low on stone and may have to be shut down soon.`n`n");
			break;
			case 18:
				output("With some pretty impressive pick-axe work, you cleave the stone like butter.  In fact, you are able to cut `)Two Blocks of Stone`% in `@one turn`%!  Very nice work!");
				$allprefs['blocks']=$allprefs['blocks']+2;
				$allprefs['blockshof']=$allprefs['blockshof']+2;
				set_module_pref('allprefs',serialize($allprefs));
				debuglog("gained 2 blocks of stone in the quarry.");
				if (is_module_active('lostruins') && get_module_setting("usequarry")==0) {
					//Intentionally only subtract 1 block even though player gets credit for 2.  This is for the Giant Siege check.  Trust me here.
					increment_module_setting("blocksleft",-1);
					if (get_module_setting("blocksleft")<10) output("`n`n`@T`3he `@Q`3uarry`% is looking low on stone and may have to be shut down soon.`n`n");
				}
			break;
			case 19:
				output("`%Thinking that you know everything there is to know, you start hacking away at a wonderful `)Block of Stone`%.`n`n You stand up full of pride to show your work when `!Engineer `\$Uraal`% taps you on the shoulder.");
				output("`n`n`@'I'm so sorry, but that was a `)Block of Stone`@ that was already completed.  We have to charge you for that mistake.'`%`n`n");
				if ($session['user']['gold']<750){
					output("You hand over `^all your money");
					$session['user']['gold']=0;
					debuglog("lost all gold accidentally taking someone's block of stone in the quarry.");
				}else{
					output("You hand over `^750 gold");
					$session['user']['gold']-=750;
					debuglog("lost 750 gold accidentally taking someone's block of stone in the quarry.");
				}
				output("`%with a sheepish look. `n`n You `@lose a turn`%.");
				if (is_module_active('lostruins') && get_module_setting("usequarry")==0) {
					increment_module_setting("blocksleft",-1);
					if (get_module_setting("blocksleft")<10) output("`n`n`@T`3he `@Q`3uarry`% is looking low on stone and may have to be shut down soon.`n`n");
				}
			break;
			case 20:
				$allprefs['blocks']=$allprefs['blocks']+1;
				$allprefs['blockshof']=$allprefs['blockshof']+1;
				set_module_pref('allprefs',serialize($allprefs));
				output("`%Your pick-axe flies and you set free a perfect `)Block of Stone`%. `n`nYou complete your work on `)One Block of Stone`% in `@one turn`%. You puff your chest with pride and show the stone off to all the people around you.`n`nYou think one shady looking character is particularly interested in your work.`n`n");
				if (is_module_active('masons')){
					if (get_module_setting("recruiting","masons")==1) {
						$sql = "SELECT COUNT(*) AS c FROM " . db_prefix("module_userprefs") . " WHERE modulename = 'masons' AND setting = 'masonnumber' AND value > 0";
						$result = db_query($sql);
						$row = db_fetch_assoc($result);
						$masonnum = $row['c'];
						if ($masonnum<=get_module_setting("autozero","masons")) $random=15;
						elseif ($masonnum<=get_module_setting("autoone","masons")) $random=2;
						elseif ($masonnum<=get_module_setting("autotwo","masons")) $random=4;
						else $random=6;
					}elseif (get_module_setting("recruiting","masons")==2){
						if ($allprefs['blockshof']>=get_module_setting("autotwo","masons")) $random=15;
						elseif ($allprefs['blockshof']>=get_module_setting("autoone","masons")) $random=2;
						elseif ($allprefs['blockshof']>=get_module_setting("autozero","masons")) $random=4;
						else $random=6;
					}else $random=2;
				}else $random=2;
				switch(e_rand(1,$random)){
					case 1: case 3: case 4: case 5: case 6:
						output("However, he turns away.`n`n");
						debuglog("spent a turn collecting a block of stone in the quarry and was almost approached by the masons.");
					break;
					case 2: case 7: case 8: case 9: case 10: case 11: case 12: case 13: case 14: case 15:
						output("`4So you take a couple of steps closer to him...`n`n");
						switch(e_rand(1,$random)){
							case 1: case 3: case 4: case 5: case 6:
								output("`7And he quickly retreats.  It must have been your imagination.`n`n");
								debuglog("spent a turn collecting a block of stone in the quarry and was almost recruited into the masons.");
							break;
							case 2: case 7: case 8: case 9: case 10: case 11: case 12: case 13: case 14: case 15:
								if (is_module_active('masons')) {
									$allprefsm=unserialize(get_module_pref('allprefs','masons'));
									if ($allprefsm['masonmember']==1){
										output("`%He smiles and notices your tattoo. `7`n`n'This is a gift from `&T`)he `&S`)ecret `&O`)rder `&o`)f `&M`)asons`7.  May you experience excellence in everything you do.`n`n");
										output("`%You open a small bag and find it is filled with `bgems`b and `^gold`%.`n`n");
										$gemenum=get_module_setting("case20ge");
										if ($gemenum==1) $gemfind=(e_rand(1,2));
										elseif ($gemenum==2) $gemfind=(e_rand(2,3));
										elseif ($gemenum==3) $gemfind=(e_rand(3,5));
										elseif ($gemenum==4) $gemfind=(e_rand(5,10));
										output("You collect `b%s gem%s`b and `^250 gold`%.",$gemfind,translate_inline($gemfind>1?"s":""));
										$session['user']['gems']+=$gemfind;
										$session['user']['gold']+=250;
										debuglog("spent a turn collecting a block of stone in the quarry and got 250 gold and $gemfind gems because a mason rewarded them.");
									}else{
										output("`%He approaches you and stares at you.  Then he extends his hand and asks if he could have a 'private' word with you.");
										addnav("Private Chat","runmodule.php?module=quarry&op=private");
									}
								}else{
									output("`%The man smiles and hands you a `bbag full of gems`b!!`n`n `7'I am one of `&%s's Envoys`7 and I am giving you this token of appreciation from `&%s`7 from your excellent work.  Have a great day!'`n`n",$ruler,$ruler);
									$gemenum=get_module_setting("case20ge");
									if ($gemenum==1) $gemfind=(e_rand(1,2));
									elseif ($gemenum==2) $gemfind=(e_rand(2,3));
									elseif ($gemenum==3) $gemfind=(e_rand(3,5));
									elseif ($gemenum==4) $gemfind=(e_rand(5,10));
									elseif ($gemenum==5) $gemfind=(e_rand(8,15));
									output("`%You count out and find that you've received `b%s gem%s`b!!!",$gemfind,translate_inline($gemfind>1?"s":""));
									$session['user']['gems']+=$gemfind;
									debuglog("spent a turn collecting a block of stone in the quarry and received $gemfind gems as a bonus.");
								}
							break;
						}
					break;
				}
				if (is_module_active('lostruins') && get_module_setting("usequarry")==0) {
					increment_module_setting("blocksleft",-1);
					if (get_module_setting("blocksleft")<10) output("`n`n`@T`3he `@Q`3uarry`% is looking low on stone and may have to be shut down soon.`n`n");
				}
			break;
			case 21:
				output("`n`c`^Somebody went under a dock`nAnd there they saw a rock`nIt wasn't a rock`nIt was a `)Rock `4Lobster`^!!`c");
				addnav("`)Rock `4Lobster `\$Attack","runmodule.php?module=quarry&op=lobster");
				quarry_blocknavs();
			break;
			case 22:
				output("`%You split one of the giant stones and discover a strange fossil inside.  You blow on it and try to clean it off...`n`n  Your breath causes the `^F`Qossil `^D`Qinosaur`% to come to life!!");
				addnav("`^F`Qossil `^D`Qinosaur `\$Attack","runmodule.php?module=quarry&op=fossil");
				quarry_blocknavs();
			break;
			case 23:
				output("`%Soon enough, you finish your work.  `!Engineer `\$Uraal`% hands you a voucher for `)One Block of Stone`%.  As soon as you take the voucher though, you hear panicked cries of workers all around you. `n`n It seems like your old nemesis, `qG`^reat `qB`^ig `qB`^ear`%, is back to cause more trouble!`n`n");
				$allprefs['blocks']=$allprefs['blocks']+1;
				$allprefs['blockshof']=$allprefs['blockshof']+1;
				set_module_pref('allprefs',serialize($allprefs));
				addnav("Bear`$ Fight","runmodule.php?module=quarry&op=bear");
				quarry_blocknavs();
			break;
			case 24:
				output("`%Your pick-axe work is quite admirable.  However, before you get more than a couple of swings in, the quarry workers start to shout and panic.  On of the large boulders right above you is coming lose!`n`n  `@`b'Look out below!!'`b`%`n`n  You will have to depend on your razor sharp reflexes to save you!");
				output("You will be able to do this by `\$'fighting'`% your way to safety!");
				addnav("`\$Stone Collapse!","runmodule.php?module=quarry&op=stonec");
				quarry_blocknavs();
			break;
			case 25:
				output("`%You start to work the quarry and here a voice from above shout `@`b'Falling Rock!'`b`%`n`n  You will have to depend on your razor sharp reflexes to dodge the falling rocks!  You will be able to do this by `\$'fighting'`% your way to safety!");
				addnav("`\$Avalanche!","runmodule.php?module=quarry&op=avalanche");
				quarry_blocknavs();
			break;
			case 26:
				$gold=get_module_setting("case26g");
				$randgold=round(e_rand($gold,$gold/2));
				output("You carve out `)One Block of Stone`% in `@one turn`%.`n`nWhen you finish, you notice that the stone that you carved it out of has a natural `^pocket of gold`%!`n`n You collect `^%s Gold`%!!`n`n",$randgold);
				$session['user']['gold']+=$randgold;
				debuglog("spent a turn collecting a block of stone in the quarry and found $randgold gold.");
				quarry_completeblock();
			break;
			case 27:
				switch(e_rand(1,4)){
					case 1: case 2: case 3:
						output("Despite your best efforts to get a decent `)block of stone`%, you fail.`n`n`@T`3he `@Q`3uarry`% is a lot harder to work than you thought.");
						debuglog("spent a turn collecting a block of stone in the quarry but failed to collect it.");
					break;
					case 4:
						switch(e_rand(1,4)){
							case 1: case 2: case 3:
								output("You stand before your finished `)Block of Stone`% and give it a little tap proving how impressive you are.`n`nThe `)stone `%falls on top of you.`n`n  You`$ lose all hitpoints`% except `\$one`%, but you were able to finish the `)stone`%!");
								$session['user']['hitpoints']=1;
								debuglog("spent a turn collecting a block of stone in the quarry but lost all hitpoints except one.");
								quarry_completeblock();
							break;
							case 4:
								$exploss = round($session['user']['experience']*.05);
								output("Everything seems to be going so well, when suddenly a huge boulder falls off of the cliff above you.`n`nBefore you get a chance to escape, you are crushed under it's weight.`n`nYou are `\$MOSTLY dead`%.`n`n");
								output("`b`4You lose `#%s experience`4.`b`n`n",$exploss);
								output("`b`%You lose `^All Your Gold`%.`n`n");
								output("`b`%You are done visiting `@T`3he `@Q`3uarry`% for today.`n`n");
								addnews("%s`% was found `\$MOSTLY Dead`% but still alive at `@T`3he `@Q`3uarry`%.",$session['user']['name']);
								$session['user']['experience']-=$exploss;
								$session['user']['hitpoints'] = 1;
								$session['user']['gold']=0;
								$allprefs['usedqts']=get_module_setting("quarryturns");
								debuglog("almost died and lost $exploss experience, all gold, all hitpoints but one in the quarry.");
								set_module_pref('allprefs',serialize($allprefs));
								blocknav("runmodule.php?module=quarry&op=office");
								blocknav("runmodule.php?module=quarry&op=work");
							break;
						}
					break;
				}
			break;
			case 28:
			case 29:
				output("You finish up with your work and collect your voucher for `)One Block of Stone`%.  Since you did it in record time, you find yourself getting a little bored.");
				output("You wander around and see a group of kids playing around an`q ant hill`% with a magnifying glass.`n`n  What do you do?`n`n`\$1.  Help Burn the Ants, showing better and more efficient methods.`n`^2.Wander off.");
				output("It's not your problem.`n`@3. Intervene to stop the little hooligans!`n`n");
				addnav("`\$Burn Ants","runmodule.php?module=quarry&op=burn");
				addnav("`^Wander Off","runmodule.php?module=quarry&op=wander");
				addnav("`@Stop them","runmodule.php?module=quarry&op=stopthem");
				debuglog("spent a turn collecting a block of stone in the quarry then made a decision about saving some ants.");
				quarry_completeblock();
				quarry_blocknavs();
			break;
			case 30:
				output("As you finish your work, you give one big swing to the ground and suddenly hear a loud gushing noise.`n`n  You look down to see that you've hit a `#natural spring`%!!`n`n");
				output("Well, you don't get to collect your `)stone`% because it's now about 10 feet underwater.`n`n");
				if ($session['user']['hitpoints']==7) output("You swim away to safety.`n`n");
				elseif ($session['user']['hitpoints']<=7) output("You swim away and the water is invigorating.  You suddenly find that you have `@7 hitpoints`%!`n`n");
				else output("You almost drown and `\$lose all your hitpoints except seven`%!`n`n(I figured you're probably getting sick of only having 1 hitpoint after all these things happen to you.)`n`n");
				$session['user']['hitpoints']=7;
				debuglog("lost all hitpoints but seven in the quarry.");
				if (is_module_active('lostruins') && get_module_setting("usequarry")==0) {
					$beforeblock=get_module_setting("blocksleft");
					if ($beforeblock>10) increment_module_setting("blocksleft",-3);
					debuglog("Caused 3 blocks to be covered underwater.");
					//double check to see if this avoided the giant siege
					$afterblock=get_module_setting("blocksleft");
					$hblockmin=get_module_setting("blockmin")/2;
					if ($beforeblock>$hblockmin && $afterblock<$hblockmin) {
						set_module_setting("underatk",1);
						set_module_setting("giantleft",get_module_setting("numbgiant"));
					}	
					output("This section of `@T`3he `@Q`3uarry`% will no longer be useful.`n`n");
					if (get_module_setting("blocksleft")<10) output("`@T`3he `@Q`3uarry`% is looking low on stone and may have to be shut down soon.`n`n");
				}
			break;
		}	
	}
}
?>