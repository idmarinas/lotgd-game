<?php
function metalmine_work(){
	global $session;
	$op2 = httpget('op2');
	$op3 = httpget('op3');
	$mineturnset=get_module_setting("mineturnset");
	$allprefs=unserialize(get_module_pref('allprefs'));
	addnav("Metal Mine");
	output("`n`c`b`&Metal `)Mine`c`b`n`0");
	$metal=$allprefs['metal'];
	$canary=$allprefs['canary'];
	$marray=translate_inline(array("","`)Iron Ore`0","`QCopper`0","`&Mithril`0"));
	if (get_module_setting("down")==1){
		output("You escape from the mine during a huge cave-in.  You'll need to head to the mine entrance to see if you can help.");
		addnav("Continue","runmodule.php?module=metalmine&op=enter");
	}else{
		if ($op2=="arrive"){
			if ($allprefs['canary']!="") output("You place %s`0 next to your work area.",$allprefs['canary']);
			else output("You realize that you don't have a canary with you.  Oh well!");
		}
		$pickaxe=$allprefs['pickaxe'];
		if ($pickaxe==0){
			output("You don't have a pickaxe, so you have to scrounge around on the floor trying to gather together some of the precious metals.");
			$chance=e_rand(1,6);
			if ($chance==1){
				$collect=round(100/$mineturnset);
				output("You are able to gather `^%s grams`0 of %s.  You feel so proud.`n`n",$collect,$marray[$metal]);
				$allprefs['metal'.$metal]=$allprefs['metal'.$metal]+$collect;
				$allprefs['metalhof']=$allprefs['metalhof']+$collect;
			}elseif ($chance<5){
				output("You can't find any %s to gather.  That's sad.`n`n",$marray[$metal]);
			}else{
				$collect=round(200/$mineturnset);
				output("You are able to gather `^%s grams`0 of %s.  What a great use of your time.  Think about all those suckers who bought a pickaxe!`n`n",$collect,$marray[$metal]);
				$allprefs['metal'.$metal]=$allprefs['metal'.$metal]+$collect;
				$allprefs['metalhof']=$allprefs['metalhof']+$collect;
			}
			$allprefs['usedmts']=$allprefs['usedmts']+1;
			set_module_pref('allprefs',serialize($allprefs));
			$usedmts=$allprefs['usedmts'];
			$mineturns=$mineturnset-$usedmts;
			if ($mineturns>0) output("You have `^%s Mine %s`0 left.",$mineturns,translate_inline($mineturns>1?"Turns":"Turn"));
			else output("You've used up all your Mine Turns for the day. It's probably time for you to head out.");
			if ($usedmts<$mineturnset) addnav("Try again","runmodule.php?module=metalmine&op=work");
			addnav("Leave Mine","runmodule.php?module=metalmine&op=leavemine");
		}else{
			switch(e_rand(1,100)+($pickaxe*3)){
			//switch(101){
				//only Pickaxe 1
				//1: Broken Pickaxe
				case 4:
					output("You swing wildly at the wall and find a poor vein of %s.",$marray[$metal]);
					output("`n`nUnfortunately, your pickaxe gets jammed in the wall! You try to pry it out...`n`n");
					$chance=e_rand(1,2);
					if ($chance==1) output("and luckily you're able to pry it out without damaging it.");
					else{
						output("Sadly, you break your pickaxe.");
						$allprefs['pickaxe']=0;
					}
					$allprefs['oil']+=1;
					$grams=round((e_rand(250,500))/$mineturnset);
					output("`n`nYou gather `^%s grams`0 of %s.",$grams,$marray[$metal]);
					$allprefs['metal'.$metal]=$allprefs['metal'.$metal]+$grams;
					$allprefs['metalhof']=$allprefs['metalhof']+$grams;
					$allprefs['usedmts']=$allprefs['usedmts']+1;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//2: Find a new pickaxe
				case 5:
					$chance=e_rand(1,10);
					output("You get ready to work on the mine when suddenly you notice something shiny on the ground.");
					output("You go to look at what it is and discover that it's");
					if ($chance==1){
						output("a very");
						$allprefs['pickaxe']=3;
					}else{
						output("an");
						$allprefs['pickaxe']=2;
					}
					$allprefs['oil']+=1;
					output("expensive pickaxe!`n`nSince this one is much nicer than your current one, you decide to 'upgrade'.");
					$allprefs['usedmts']=$allprefs['usedmts']+1;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//3: Missed swing
				case 6:
					output("You swing with your pickaxe, but the handle slips out of your hands.`n`n");
					output("That was a waste of a `^Mine Turn`0.");
					$allprefs['usedmts']=$allprefs['usedmts']+1;
					$allprefs['oil']+=1;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//Pickaxe 1 and 2
				//4: Broken Pickaxe
				case 7:
					output("You swing wildly at the wall and find a vein of %s.",$marray[$metal]);
					output("`n`nUnfortunately, your pickaxe gets jammed in the wall! You try to pry it out...`n`n");
					$chance=e_rand(1,1+$pickaxe);
					if ($chance==1){
						output("Sadly, you break your pickaxe.");
						$allprefs['pickaxe']=0;
					}else output("and luckily you're able to pry it out without damaging it.");
					$grams=round((e_rand(450,600))/$mineturnset);
					if ($grams<2) $grams=2;
					output("`n`nYou gather `^%s grams`0 of %s.",$grams,$marray[$metal]);
					$allprefs['metal'.$metal]=$allprefs['metal'.$metal]+$grams;
					$allprefs['metalhof']=$allprefs['metalhof']+$grams;
					$allprefs['usedmts']=$allprefs['usedmts']+1;
					$allprefs['oil']+=1;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//5: Poor Vein of Metal
				case 8:
					output("You swing at the wall and find an extremely poor vein of %s.",$marray[$metal]);
					$grams=round((e_rand(20,60))/$mineturnset);
					if ($grams<2) $grams=2;
					output("`n`nYou gather `^%s grams`0 of %s.",$grams,$marray[$metal]);
					$allprefs['metal'.$metal]=$allprefs['metal'.$metal]+$grams;
					$allprefs['metalhof']=$allprefs['metalhof']+$grams;
					$allprefs['usedmts']=$allprefs['usedmts']+1;
					$allprefs['oil']+=1;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//6: Sandstone
				case 9:
					output("You hit a patch of sandstone.  It's easy going.  You don't find anything of value, but you don't lose a `^Mine Turn`0.");
					$allprefs['oil']+=1;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//All Pickaxes here
				//7: Lumberjack Fight
				case 10:
					output("You get ready with a back-swing and prepare to hit the wall as hard as you can. Unfortunately, you hit a miner walking behind you!");
					output("`#`n`n'I don't take kindly to getting hit with an axe!'`0 he yells.");
					if (is_module_active("lumberyard")) {
						output("`n`nYou recognize him... He's the `@B`4u`@r`4l`@y `4L`@u`4m`@b`4e`@r`4j`@a`4c`@k`0 from the Lumberyard!");
						output("It seems like he decided to become a Miner. How nice, he had a career change.");
					}
					output("`n`nBefore you can get a chance to apologize, you're surrounded by miners chanting `@'Fight! Fight! Fight!'`0");
					addnav("Fight the Miner","runmodule.php?module=metalmine&op=miner");
					blocknav("runmodule.php?module=metalmine&op=leavemine");
					blocknav("runmodule.php?module=metalmine&op=travel");
					blocknav("runmodule.php?module=metalmine&op=work");
					$allprefs['usedmts']=$allprefs['usedmts']+1;
					$allprefs['oil']+=5;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//8: One gram of everything
				case 11:
					output("You swing as hard as you can and all the miners around you come to stare at you.  They didn't know you had such power!");
					output("`n`nYou go to gather your amazing bounty and find something very odd...");
					output("`n`nAll you've done is discovered one gram of each type of metal!!");
					output("`n`nYou collect `)One Gram of Iron Ore`0, `QOne Gram of Copper`0, and `&One Gram of Mithril`0.");
					output("`n`nAll the miners laugh at your discovery.  Ha Ha!");
					$allprefs['metal1']=$allprefs['metal1']+1;
					$allprefs['metal2']=$allprefs['metal2']+1;
					$allprefs['metal3']=$allprefs['metal3']+1;
					$allprefs['metalhof']=$allprefs['metalhof']+3;
					$allprefs['usedmts']=$allprefs['usedmts']+1;
					$allprefs['oil']+=1;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//9: Broken Pickaxe Event
				case 12:
					output("You swing wildly at the wall and find a poor vein of %s.",$marray[$metal]);
					output("`n`nUnfortunately, your pickaxe gets jammed in the wall! You try to pry it out...`n`n");
					$chance=e_rand(1,1+$pickaxe);
					if ($chance==1){
						output("Sadly, you break your pickaxe.");
						$allprefs['pickaxe']=0;
					}else output("and luckily you're able to pry it out without damaging it.");
					$grams=round((e_rand(250,500))/$mineturnset);
					if ($grams<2) $grams=2;
					output("`n`nYou gather `^%s grams`0 of %s.",$grams,$marray[$metal]);
					$allprefs['metal'.$metal]=$allprefs['metal'.$metal]+$grams;
					$allprefs['metalhof']=$allprefs['metalhof']+$grams;
					$allprefs['usedmts']=$allprefs['usedmts']+1;
					$allprefs['oil']+=1;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//10: Mummy Chamber
				case 13:
					if($allprefs['found']==0){
						output("You strike at the wall of the mine and suddenly it collapses!! You don't find any metal, but");
						output("you look around to see a passage to a secret chamber.`n`nWill you enter it?");
						addnav("To the Chamber","runmodule.php?module=metalmine&op=chamber");
					}else output("You hit a patch of sandstone.  It's easy going.  You don't find anything of value, but you don't lose a `^Mine Turn`0.");
					$allprefs['oil']+=10;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//11: Collapsing Wall Chamber
				case 14:
					if($allprefs['found']==0){
						output("You strike at the wall of the mine and suddenly it collapses!! You don't find any metal, but");
						output("you look around to see a passage to a secret chamber.`n`nWill you enter it?");
						addnav("To the Chamber","runmodule.php?module=metalmine&op=tunnel");
					}else output("You hit a patch of sandstone.  It's easy going.  You don't find anything of value, but you don't lose a `^Mine Turn`0.");
					$allprefs['oil']+=10;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//12: Magic Black Ball Chamber
				case 15:
					if($allprefs['found']==0){
						output("You strike at the wall of the mine and suddenly it collapses!! You don't find any metal, but");
						output("you look around to see a passage to a secret chamber.`n`nWill you enter it?");
						addnav("To the Chamber","runmodule.php?module=metalmine&op=chamber2");
					}else output("You hit a patch of sandstone.  It's easy going.  You don't find anything of value, but you don't lose a `^Mine Turn`0.");
					$allprefs['oil']+=10;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//13: Ignore Gold
				case 16:
					output("You take a swing at the wall and hit some `^yellow`0 substance.  You think about what you're trying to find...");
					output("`n`n`QCopper`0... `)Iron Ore`0... `&Mithril`0... It's none of those.");
					output("`n`nYou move on without thinking back.");
					output("Sometime later, you realize that that was probably `^GOLD`0!!! If only you had stopped to look at it a little more closely.");
					$allprefs['usedmts']=$allprefs['usedmts']+1;
					$allprefs['oil']+=1;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//14: Find Gold
				case 17;
					output("You take a swing at the wall and hit some `^yellow`0 substance.  You think about what you're trying to find...");
					output("`n`n`QCopper`0... `)Iron Ore`0... `&Mithril`0... It's none of those.");
					output("`n`nWAIT! That's `^Gold`0!!!");
					$gold=e_rand(100,200);
					output("`n`nYou gather up `^%s gold`0.",$gold);
					$session['user']['gold']+=$gold;
					$allprefs['usedmts']=$allprefs['usedmts']+1;
					$allprefs['oil']+=1;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//15: Sandstone
				case 18; case 19; case 20;
					output("You hit a patch of sandstone.  It's easy going.  You don't find anything of value, but you don't lose a `^Mine Turn`0.");
				break;
				//16: Basalt
				case 21: case 22: case 23:
					output("You look for a good spot to mine and hit the wall with a solid swing.  Unfortunately, you've hit a section of basalt and mining is slow.");
					output("You don't find any metal and you spend a turn hitting the wall fruitlessly.");
					$allprefs['usedmts']=$allprefs['usedmts']+1;
					$allprefs['oil']+=1;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//17: Canary Event
				case 24: case 25: case 26: case 27: case 28: case 29: case 30:
					$rand=e_rand(1,10);
					if ($rand<10){
						output("You swing at the wall and find a vein of %s.",$marray[$metal]);
						$grams=round((e_rand(1000,2000))/$mineturnset);
						output("`n`nYou gather `^%s grams`0 of %s.",$grams,$marray[$metal]);
						$allprefs['metal'.$metal]=$allprefs['metal'.$metal]+$grams;
						$allprefs['metalhof']=$allprefs['metalhof']+$grams;
						if ($rand<4){
							if ($canary!=""){
								output("`n`nAfter you gather the metal, you look over at `^%s`0 and feel a sense of panic. Your canary has stopped singing!",$canary);
								output("`n`nYou run over and realize that it's just sleeping... no problem!");
							}else{
								output("You inhale something that makes you start coughing.");
								if ($session['user']['hitpoints']>10){
									output("You lose `\$10 hitpoints`0.");
									$session['user']['hitpoints']-=10;
								}elseif ($session['user']['hitpoints']>1){
									output("You lose `\$all your hitpoints except 1`0.");
									$session['user']['hitpoints']=1;
								}else{
									output("Fortunately, you recover after a couple of minutes. You spend `^one mine turn`0 coughing.");
								}
							}
							$allprefs['oil']+=2;
							$allprefs['usedmts']=$allprefs['usedmts']+1;
						}elseif($rand<8){
							$allprefs['metal'.$metal]=$allprefs['metal'.$metal]-$grams;
							$allprefs['metalhof']=$allprefs['metalhof']-$grams;
							if ($canary!=""){
								output("`n`nAfter you gather the metal, you look over at `^%s`0 and feel a sense of panic. Your canary has stopped singing!",$canary);
								output("`n`nYou drop all the metal that you mined and try to resucitate your little friend.");
								output("`n`nLuckily, after a couple of bird-sized coughs, your canary recovers.");
							}else{
								output("You inhale something that makes you start coughing. You're unable to stop coughing and drop all the %s that you just mined.",$marray[$metal]);
								if ($session['user']['hitpoints']>25){
									output("You lose `\$25 hitpoints`0.");
									$session['user']['hitpoints']-=25;
								}elseif ($session['user']['hitpoints']>1){
									output("You lose `\$all your hitpoints except 1`0.");
									$session['user']['hitpoints']=1;
								}else{
									output("Fortunately, you recover after a couple of minutes. You spend `^one mine turn`0 coughing.");
								}
							}
							$allprefs['oil']+=3;
							$allprefs['usedmts']=$allprefs['usedmts']+1;
						}else{
							$allprefs['metal'.$metal]=$allprefs['metal'.$metal]-$grams;
							$allprefs['metalhof']=$allprefs['metalhof']-$grams;
							$usedmts=$allprefs['usedmts'];
							if ($canary!=""){
								output("`n`nAfter you gather the metal, you look over at `^%s`0 and feel a sense of panic.  Your canary has stopped singing!",$canary);
								output("`n`nYou drop all of the metal that you mined and try to resucitate your little friend.");
								if ($usedmts<$mineturnset) output("You spend the rest of your `^Mine Turns`0 helping your bird recover.");
								output("`n`nLuckily, after a couple of bird-sized coughs, your canary recovers.");
							}else{
								output("You inhale something that makes you start coughing. You're unable to stop coughing and drop all the %s that you just mined.",$marray[$metal]);
								if ($session['user']['hitpoints']>1){
									output("You lose `\$all your hitpoints except 1`0.`n`n");
									$session['user']['hitpoints']=1;
								}
								if ($usedmts<$mineturnset) output("You spend the rest of your `^mine turns`0 coughing.");
							}
							$allprefs['oil']+=10;
							$allprefs['usedmts']=$mineturnset;
						}
					}else{
						if ($canary!=""){
							output("You start to cough and choke, thinking that you swallowed something wrong.  However, when you look over, you notice that `^%s`0 is dead!",$canary);
							output("You realize that there must be a pocket of toxic gas.  You rush to the mine surface.");
							output("`n`nYou're done working in the mine today, but you're lucky to have survived at all. You `\$lose all your hitpoints except 1`0.");
							$session['user']['hitpoints']=1;
							$allprefs['canary']="";
							$allprefs['oil']+=4;
						}else{
							output("You inhale a pocket of very toxic gas. As you die, you suddenly wish you had brought a canary with you to warn you.");
							output("`n`nYou lose `^all your gold`0 and `#10 percent`0 of your experience.");
							$session['user']['hitpoints']=0;
							$session['user']['alive']=false;
							$session['user']['gold']=0;
							$session['user']['experience']*=.9;
							$allprefs['oil']+=25;
							addnav("The Shades","shades.php");
							addnews("%s`0 died inhaling toxic fumes in the Mine.",$session['user']['name']);
							blocknav("runmodule.php?module=metalmine&op=leavemine");
						}
						blocknav("runmodule.php?module=metalmine&op=travel");
						blocknav("runmodule.php?module=metalmine&op=work");
						$allprefs['usedmts']=$mineturnset;
					}
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//18: Standard Vein
				case 31: case 32: case 33: case 34: case 35: case 36: case 37: case 38: case 39: case 40:
				case 41: case 42: case 43: case 44: case 45: case 46: case 47: case 48: case 49: case 50:
				case 51: case 52: case 53: case 54: case 55: case 56: case 57: case 58: case 59: case 60:
					output("You swing at the wall and find a standard vein of %s.",$marray[$metal]);
					$grams=round((e_rand(1250,2500))/$mineturnset);
					output("`n`nYou gather `^%s grams`0 of %s.",$grams,$marray[$metal]);
					$allprefs['metal'.$metal]=$allprefs['metal'.$metal]+$grams;
					$allprefs['metalhof']=$allprefs['metalhof']+$grams;
					$allprefs['usedmts']=$allprefs['usedmts']+1;
					$allprefs['oil']+=1;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//19: Bear Attack
				case 61:
					output("You break through a wall and find that you're in a cave!");
					output("`n`nYou decide to take a look around and accidentally step on a bear!!");
					if (is_module_active("lumberyard") || is_module_active("quarry")){
						output("`n`nYou suddenly recognize this bear from the");
						if (is_module_active("lumberyard")) output("lumberyard");
						else output("quarry");
						output("and you're not looking forward to this fight...");
					}
					output("`n`nThe bear doesn't take too kindly to being stepped on.");
					addnav("Bear Fight","runmodule.php?module=metalmine&op=bear");
					blocknav("runmodule.php?module=metalmine&op=leavemine");
					blocknav("runmodule.php?module=metalmine&op=work");
					blocknav("runmodule.php?module=metalmine&op=travel");
					$allprefs['usedmts']=$allprefs['usedmts']+1;
					$allprefs['oil']+=5;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//20: Lumberyard: Free Wood
				case 62:
					if (is_module_active("lumberyard")){
						output("You can't quite explain it, but you find a `&square of wood`0 that someone had processed at the lumberyard.");
						output("You spend a `^mine turn`0 collecting the lumber.");
						$allprefsl=unserialize(get_module_pref('allprefs','lumberyard'));
						$allprefsl['squares']=$allprefsl['squares']+1;
						$allprefsl['squareshof']=$allprefsl['squareshof']+1;
						set_module_pref('allprefs',serialize($allprefsl),'lumberyard');
					}else{
						output("You look for a good spot to mine and hit the wall with a solid swing.  Unfortunately, you've hit a section of basalt and mining is slow.");
						output("You don't find any metal and you spend a turn hitting the wall fruitlessly.");
					}
					$allprefs['oil']+=3;
					$allprefs['usedmts']=$allprefs['usedmts']+1;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//21: Quarry: Free Stone
				case 63:
					if (is_module_active("quarry")){
						output("You can't quite explain it, but you find a `&block of stone`0 that someone had processed at the quarry.");
						output("You spend a `^mine turn`0 collecting the stone.");
						$allprefsq=unserialize(get_module_pref('allprefs','quarry'));
						$allprefsq['blocks']=$allprefsq['blocks']+1;
						$allprefsq['blockshof']=$allprefsq['blockshof']+1;
						set_module_pref('allprefs',serialize($allprefsq),'quarry');
					}else{
						output("You look for a good spot to mine and hit the wall with a solid swing.  Unfortunately, you've hit a section of basalt and mining is slow.");
						output("You don't find any metal and you spend a turn hitting the wall fruitlessly.");
					}
					$allprefs['oil']+=3;
					$allprefs['usedmts']=$allprefs['usedmts']+1;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//22: Different Metal
				case 64: case 65: case 66:
					$metal++;
					if ($metal==4) $metal=1;
					$grams=round((e_rand(1250,2500))/$mineturnset);
					output("You strike the wall with your pickaxe and find some metal... but it's not what you expected!");
					output("You've hit a pocket of %s`0.",$marray[$metal]);
					output("`n`nYou gather `^%s grams`0 of %s.",$grams,$marray[$metal]);
					$allprefs['metal'.$metal]=$allprefs['metal'.$metal]+$grams;
					$allprefs['metalhof']=$allprefs['metalhof']+$grams;
					$allprefs['usedmts']=$allprefs['usedmts']+1;
					$allprefs['oil']+=1;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//23: Helmet Damage
				case 67:
					output("You swing at the wall and find a standard vein of %s.",$marray[$metal]);
					$grams=round((e_rand(1250,2500))/$mineturnset);
					if ($allprefs['helmet']>0){
						output("`n`nAs you gather the `^%s grams`0 of %s, a rock hits your helmet. Your helmet is downgraded.",$grams,$marray[$metal]);
						$allprefs['helmet']=$allprefs['helmet']-1;
					}else{
						output("`n`nAs you gather the `^%s grams`0 of %s, you see a shiny new helmet.",$grams,$marray[$metal]);
						output("In fact, it looks like a top quality helmet! You feel a great relief that you don't have to go without a helmet anymore.");
						$allprefs['helmet']=3;
					}
					if ($allprefs['helmet']==0){
						output("Unfortunately, this makes traveling around in the mine a bit more difficult since your helmet is now nothing but a piece of junk.");
					}
					$allprefs['oil']+=5;
					$allprefs['metal'.$metal]=$allprefs['metal'.$metal]+$grams;
					$allprefs['metalhof']=$allprefs['metalhof']+$grams;
					$allprefs['usedmts']=$allprefs['usedmts']+1;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//24: Find a Gem, Lose a Gem
				case 68:
					output("You swing at the wall and find a standard vein of %s.",$marray[$metal]);
					$grams=round((e_rand(1250,2500))/$mineturnset);
					output("`n`nAs you're gathering up the `^%s grams`0 of %s, you notice something shiny.`n`n",$grams,$marray[$metal]);
					$chance=e_rand(1,6);
					if ($chance==1){
						output("You gleefully pull up a nice looking `5gem`0 and cradle it close.  You are so distracted, that you fail to notice two very critical things:");
						output("`n1.  The `5gem`0 is really a worthless rock.`n2.");
						if ($session['user']['gems']>0){
							output("One of your real `%gems`0 falls out of your gem bag and is lost forever.");
							$session['user']['gems']--;
						}else output("You don't have any real gems to compare this one to anyway.");
					}elseif ($chance==2) output("You think you thought you saw a `%gem`0, but it turns out it was just a rock.");
					else{
						output("You look a little more closely and realize it's a `%gem`0!");
						$session['user']['gems']++;
					}
					$allprefs['oil']+=1;
					$allprefs['metal'.$metal]=$allprefs['metal'.$metal]+$grams;
					$allprefs['metalhof']=$allprefs['metalhof']+$grams;
					$allprefs['usedmts']=$allprefs['usedmts']+1;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//25: Mine Collapse Event
				case 69:
					output("You strike the wall as hard as you can. You hear a rumbling sound above you and start to question the integrity of the walls here.");
					increment_module_setting("accident",1);
					if (get_module_setting("accident")>=get_module_setting("collapse")){
						output("`n`nThe rumbling gets louder...");
						output("`n`nAnd louder...");
						output("`n`nYou look around and see miners fleeing the mine.");
						output("You decide that this may be a good idea and start running for the exit.");
						if ($allprefs['canary']!="") {
							output("You get a couple steps out and suddenly remember `^%s`0.  Will you run back to save your little friend?",$canary);
							addnav("Run Out of the Mine","runmodule.php?module=metalmine&op=emergencyleave&op2=leavecanary");
							addnav("Go Rescue Your Canary","runmodule.php?module=metalmine&op=savecanary");
						}else addnav("Continue","runmodule.php?module=metalmine&op=emergencyleave");
						$allprefs['usedmts']=$mineturnset;
						blocknav("runmodule.php?module=metalmine&op=leavemine");
						blocknav("runmodule.php?module=metalmine&op=travel");
					}else{
						output("Not phased, you finish your work and find a standard vein of %s.",$marray[$metal]);
						$grams=round((e_rand(1250,2500))/$mineturnset);
						output("`n`nYou gather `^%s grams`0 of %s.",$grams,$marray[$metal]);
						$allprefs['metal'.$metal]=$allprefs['metal'.$metal]+$grams;
						$allprefs['metalhof']=$allprefs['metalhof']+$grams;
						$allprefs['usedmts']=$allprefs['usedmts']+1;
					}
					$allprefs['oil']+=20;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//26: Fight another player
				case 70:
					$id=$session['user']['acctid'];
					$sql = "SELECT acctid,name,weapon,armor,gold FROM ".db_prefix("accounts")." WHERE acctid<>'$id' ORDER BY rand(".e_rand().") LIMIT 1";
					$result = db_query($sql);
					$row = db_fetch_assoc($result);
					$name = $row['name'];
					$id = $row['acctid'];
					$armor = $row['armor'];
					$weapon = $row['weapon'];
					$gold = $row['gold'];
					output("You are walking to a nice place to mine for some %s when you're hit by a pickaxe!`n`nLuckily, it was only a slight nik and only costs you",$marray[$metal]);
					if ($session['user']['hitpoints']>1){
						output("`\$one hitpoint`0.");
						$session['user']['hitpoints']--;
					}else{
						output("a scare that makes you lose `\$one charm`0.");
						$session['user']['charm']--;
					}
					output("`n`nYou turn around and notice that it's `^%s`0 carrying a `&%s`0 and wearing `&%s`0.`n`n",$name,$weapon,$armor);
					if (is_module_active("alignment")){
						if (get_module_pref("alignment","alignment")<get_module_setting("evilalign","alignment")){
							output("Being `\$Evil`0, you decide that you're not going to put up with this, no matter how tough someone looks.");
							addnav("Pick a Fight","runmodule.php?module=metalmine&op=player&op2=$id&op3=evil");
						}elseif(get_module_pref("alignment","alignment")>get_module_setting("goodalign","alignment")){
							output("Being `@Good`0, you decide that you're going to turn the other cheek and walk away.");
							addnav("Walk Away","runmodule.php?module=metalmine&op=walkaway&op2=$id&op3=good");
						}else{
							output("Being `^Neutral`0, you take a look at `^%s`0 and decide whether you think you could win a fight and gain some `^gold`0.",$name);
							if ($gold>250){
								addnav("Pick a Fight","runmodule.php?module=metalmine&op=player&op2=$id&op3=neutral");
								output("You notice a hint of wealth and think that this may be worth your effort.");
							}else{
								output("You notice that your opponent is pretty poverty stricken.  It's probably best if you just walk away.");
								addnav("Walk Away","runmodule.php?module=metalmine&op=walkaway&op2=$id&op3=neutral");
							}
						}
					}else{
						output("You'll have to decide what you want to do...");
						output("Do you pick a fight or walk away?");
						addnav("Pick a Fight","runmodule.php?module=metalmine&op=player&op2=$id");
						addnav("Walk Away","runmodule.php?module=metalmine&op=walkaway&op2=$id");
					}
					$allprefs['oil']+=5;
					blocknav("runmodule.php?module=metalmine&op=leavemine");
					blocknav("runmodule.php?module=metalmine&op=work");
					blocknav("runmodule.php?module=metalmine&op=travel");
					$allprefs['usedmts']=$allprefs['usedmts']+1;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//27: Hit a spring
				case 71:
					output("You swing at the wall and suddenly hear a splashing sound...");
					output("`n`nYou take a closer look and suddenly see that the section of the mine your're in is flooding!");
					output("`n`nYou've hit a `#Natural Spring`0!!`n`n");
					if ($allprefs['metal'.$metal]>1000){
						output("You have to head for the exit with extra speed so you drop `^150 grams`0 of %s to help lighten your load.`n`n",$marray[$metal]);
						$allprefs['metal'.$metal]=$allprefs['metal'.$metal]-150;
						$allprefs['metalhof']=$allprefs['metalhof']-150;
					}
					$allprefs['oil']+=5;
					output("You find that you've got to travel to a different part of the mine or leave.");
					$allprefs['usedmts']=$allprefs['usedmts']+1;
					set_module_pref('allprefs',serialize($allprefs));
					if ($allprefs['usedmts']>=$mineturnset && get_module_setting("limitloc")<=1) addnav("Travel To a Different Area","runmodule.php?module=metalmine&op=travel");
					blocknav("runmodule.php?module=metalmine&op=work");
				break;
				//28: Elevator shaft
				case 72:
					if ($allprefs['toothy']==5){
						output("Once again, you feel the spirit of `qToothy`0 take over.  It's time to look for his pickaxe!");
						output("`n`nYou find a path to an unexplored area of the mine and notice something strange at the end of a tunnel. It looks like an elevator shaft!");
					}else output("You find a new area of the mine that doesn't look like anyone has recently visited.  Then, at the end of the tunnel you're in, you notice something strange.");
					addnav("Contine","runmodule.php?module=metalmine&op=strange");
					blocknav("runmodule.php?module=metalmine&op=leavemine");
					blocknav("runmodule.php?module=metalmine&op=work");
					blocknav("runmodule.php?module=metalmine&op=travel");
					$allprefs['usedmts']=$allprefs['usedmts']+1;
					$allprefs['oil']+=10;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//29: The Quest for Toothy McPicker
				case 73:
					if ($allprefs['toothy']==0){
						$allprefs['toothy']=$allprefs['toothy']+1;
						output("You decide to chat with some of the other miners about the history of the mine...");
						output("`n`n`\$Old Gummy`0 tells a story of the old days. `4'Back in the old days, this mine was a lot trickier to navigate.");
						output("Many miners got lost.  None of us forget the greatest miner of them all though.  His name was `qToothy McPicker`4.  He was an amazing miner, I tell you!'");
						output("`n`n`0The miners all take there hats off and give a moment of silence.");
						output("`n`n`4'`qToothy McPicker`4 could collect `^5,000 miligrams`4 of `Qcopper`4 with one swing of his pickaxe.  In fact, he could probably have cleared out this mine if it wasn't for the fact that he disappeared.");
						output("One day he went out to the mine and never came back.  We searched for months for him but we couldn't find him. We still search for him, hoping to find him alive. You know the owner of the `0General Store`4, he is the biggest admirer of `qToothy McPicker`4.");
						output("If you ever find a relic from him, I think Grober would be willing to trade for something of value with you.'");
						output("`n`n`0You take all this information and store it in the back of your mind.  Maybe one day you can find `qToothy McPicker's Pickaxe`0.");
					}elseif ($allprefs['toothy']==1){
						$allprefs['toothy']=$allprefs['toothy']+1;
						$allprefs['oil']+=1;
						output("You venture deep into the mine looking for a good place to dig.  Before long, you feel like you may be lost.");
						output("You look down and see a piece of parchment on the ground.`n`n");
						rawoutput("<br><center><table><tr><td align=center><img src=modules/metalmine/metalmineimg/map.gif></td></tr></table></center><br>"); 
						output("Clearly this has something to do with what happened to `qToothy McPicker`0! You feel the energy of an adventure surge through you! You should go visit Grober in his General Store.");
					}elseif ($allprefs['toothy']==3){
						$allprefs['toothy']=$allprefs['toothy']+1;
						$allprefs['oil']+=5;
						output("Feeling a little `qToothy Inspiration`0 you wander far and deep into the mine, following the map that you found earlier.");
						output("`n`nSoon, you're getting so far into the mine that you're afraid you're going to get lost!");
						output("`n`nJust when you feel like you should turn back, you see someone slumped against the wall ahead of you.");
						output("`n`nYou've found `qToothy McPicker`0!!!!");
						addnav("Continue","runmodule.php?module=metalmine&op=toothybes");
						blocknav("runmodule.php?module=metalmine&op=leavemine");
						blocknav("runmodule.php?module=metalmine&op=work");
						blocknav("runmodule.php?module=metalmine&op=travel");
					}elseif ($allprefs['toothy']==7){
						output("You chat some more with the other miners.  You tell them your story of `qToothy McPicker's Ghost`0, the tooth, and the pickaxe.  Everyone listens intently.");
						output("You finish your story and they all shake your hand. Suddenly, one of the other miners runs up to the group with groundbreaking news!");
						output("`n`n`@'Somebody's robbed Grober's General Store! The thief was described as 'skinny, smelly, and dead-looking'.  The only thing they took was some items from Grober's shrine to `qToothy`@.'");
						output("`n`n`0You recognize that description as that of `qThe Ghost of Toothy`0! It seems like your adventures with `qToothy`0 are going to begin again!");
						$allprefs['toothy']=0;
					}else{
						$allprefs['oil']+=1;
						output("You look for a good spot to mine and hit the wall with a solid swing.  Unfortunately, you've hit a section of basalt and mining is slow.");
						output("You don't find any metal and you spend a turn hitting the wall fruitlessly.");
					}
					$allprefs['usedmts']=$allprefs['usedmts']+1;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//30: Mime collapse
				case 74:
					output("You swing at the wall and find a standard vein of %s.",$marray[$metal]);
					$grams=round((e_rand(1250,2500))/$mineturnset);
					output("`n`nYou gather `^%s grams`0 of %s.",$grams,$marray[$metal]);
					output("`n`nYou suddenly notice about 10 people walking towards you.  You get a little nervous because they're all dressed in black.  You take a closer look and notice that they're all wearing white gloves and their faces are painted white.");
					output("`n`nYou take a defensive posture and the group of people stop.  They suddenly seem to be trapped in a huge invisible box!");
					output("There hands are waving all over the place and they can't get out!");
					output("`n`nThen, one of them loses his balance and falls, causing all the others to fall on top of him.");
					output("`n`nIt's a Mime Collapse!!!!");
					output("`n`nFor some reason, you really don't care about them. They weren't even very good mimes.");
					$allprefs['metal'.$metal]=$allprefs['metal'.$metal]+$grams;
					$allprefs['metalhof']=$allprefs['metalhof']+$grams;
					$allprefs['usedmts']=$allprefs['usedmts']+1;
					$allprefs['oil']+=1;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//31: Foot Pain
				case 75:
					output("You take a swing with your pickaxe and make a nice hit.");
					output("You find a standard vein of %s.",$marray[$metal]);
					$grams=round((e_rand(1250,2500))/$mineturnset);
					output("`n`nYou gather `^%s grams`0 of %s.",$grams,$marray[$metal]);
					output("As you're gathering the metal, you drop the axe and it hits your foot. It causes a cut on your foot and you feel woozy.");
					if ($session['user']['hitpoints']>1){
						output("You lose `\$one hitpoint`0 and need to sit down for a second.");
						$session['user']['hitpoints']--;
					}else{
						output("You get an ugly scar on your feet.  Nobody likes ugly scarred feet. You lose `\$one charm`0.");
						$session['user']['charm']--;
					}
					$chance=e_rand(1,100);
					if ($chance==100) output("`n`n`n`nEaster Egg from Module Author `^Dave`0: Okay, just so you know, something like this really happened to me once when I was camping.");
					$allprefs['oil']+=1;
					$allprefs['metal'.$metal]=$allprefs['metal'.$metal]+$grams;
					$allprefs['metalhof']=$allprefs['metalhof']+$grams;
					$allprefs['usedmts']=$allprefs['usedmts']+1;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//32: Relatively Poor Vein
				case 76: case 77: case 78: case 79: case 80: case 81: case 82: case 83: case 84:
					output("You swing at the wall and find a relatively poor vein of %s.",$marray[$metal]);
					$grams=round((e_rand(500,1000))/$mineturnset);
					output("`n`nYou gather `^%s grams`0 of %s.",$grams,$marray[$metal]);
					$allprefs['metal'.$metal]=$allprefs['metal'.$metal]+$grams;
					$allprefs['metalhof']=$allprefs['metalhof']+$grams;
					$allprefs['usedmts']=$allprefs['usedmts']+1;
					$allprefs['oil']+=1;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//33: Poor Vein
				case 85: case 86: case 87: case 88: case 89: case 90: case 91: case 92: case 93:
					output("You swing at the wall and find a poor vein of %s.",$marray[$metal]);
					$grams=round((e_rand(250,500))/$mineturnset);
					output("`n`nYou gather `^%s grams`0 of %s.",$grams,$marray[$metal]);
					$allprefs['metal'.$metal]=$allprefs['metal'.$metal]+$grams;
					$allprefs['metalhof']=$allprefs['metalhof']+$grams;
					$allprefs['usedmts']=$allprefs['usedmts']+1;
					$allprefs['oil']+=1;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//34: Excellent Vein
				case 94: case 95: case 96: case 97:
					output("You swing at the wall and find an excellent vein of %s.",$marray[$metal]);
					$grams=round((e_rand(3000,5000))/$mineturnset);
					output("`n`nYou gather `^%s grams`0 of %s.",$grams,$marray[$metal]);
					$allprefs['metal'.$metal]=$allprefs['metal'.$metal]+$grams;
					$allprefs['metalhof']=$allprefs['metalhof']+$grams;
					$allprefs['usedmts']=$allprefs['usedmts']+1;
					$allprefs['oil']+=1;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//35: bats
				case 98:
					output("You swing at the wall and it collapses.  There's a crawl-space that you just opened.");
					output("`n`nWould you like to check it out?");
					addnav("Enter the Crawl Space","runmodule.php?module=metalmine&op=crawl");
					$allprefs['usedmts']=$allprefs['usedmts']+1;
					$allprefs['oil']+=5;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//36: wishing well
				case 99:
					output("You come upon a strange shaft in the ceiling.  You try to look up and notice that it leads to the surface quite a ways up.");
					output("You look around at your feet and see `^gold coins`0... there's probably at least a hundred of them.");
					output("`n`nIt's the bottom of a wishing well, and these are the wishes of other people.");
					output("Then again, maybe it's just a bunch of gold there for your taking.  I guess it depends on what kind of person you are.");
					output("`n`nWhat kind are you?");
					addnav("Take the Gold","runmodule.php?module=metalmine&op=welltake");
					addnav("Just Leave","runmodule.php?module=metalmine&op=wellleave");
					addnav("Leave some Gold","runmodule.php?module=metalmine&op=wellgive");
					blocknav("runmodule.php?module=metalmine&op=leavemine");
					blocknav("runmodule.php?module=metalmine&op=work");
					blocknav("runmodule.php?module=metalmine&op=travel");
					$allprefs['oil']+=1;
					$allprefs['usedmts']=$allprefs['usedmts']+1;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//37: cave collapse based on Race
				case 100:
					//From Goldmine from the core
					$vals = modulehook("raceminedeath");
					$chance=e_rand(1,5);
					if ($vals['racesave']) {
						if ($vals['schema']) tlschema($vals['schema']);
						$racemsg = translate_inline($vals['racesave']);
						if ($vals['schema']) tlschema();
					}
					if (e_rand(1, 100) < $vals['chance']) $dead = 1;
					output("You suddenly feel the walls closing in around you. The mine is collapsing!");
					if ($dead==1 && $chance==1){
						$exploss = round($session['user']['experience']*.05);
						output("You try to escape but you're not able to.");
						output("`n`nAfter a lot of crawling and scratching, you finally get out and you're barely alive.  In fact, you're `\$Mostly Dead`0.");
						output("`n`nYou lose all your remaining `^mine turns`0, half of your `@forest turns left`0, all your `^gold`0, all your `\$hitpoints except one`0, and `#%s experience points`0.",$exploss);
						output("Consider yourself lucky to be alive.");
						$session['user']['experience']-=$exploss;
						$session['user']['hitpoints']=1;
						$session['user']['gold']=0;
						$session['user']['turns']*=.5;
					}else{
						output_notl($racemsg);
						output("`nThat was too close for comfort though.  You decide that you're done working in the mine for the day.");
					}
					//End Goldmine code from core
					$allprefs['oil']+=1;
					$allprefs['usedmts']=$mineturnset;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//38: Oil
				case 101:
					$allprefs['oil']+=100;
					if ($allprefs['oil']>=1000){
						output("Your headlight flickers and fades...");
						output("You are out of oil.  You can finish your turns here, but if you try to travel anywhere you'll just stumble around.");
					}else{
						output("You're busy working when suddenly you take your helmet off to wipe your brow. Suddenly you realize you've spilled some of the oil!");
						output("`n`nBad things happen if your helmet doesn't have any oil.");
						output(" You should make you sure you don't need a refill next time you're at Grober's General Store.");
					}
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//39: Something Happens
				case 102:
					if ($allprefs['something']<5) output("Something happens, but it hasn't happened enough for you to figure out what it was.");
					$allprefs['something']=$allprefs['something']+1;
					if ($allprefs['something']>=6){
						output("You remember that 'something happening' all the time and you can't figure out what it is? Well, something happens again... but this time you figure out what it is!`n`n");
						switch(e_rand(1,4)){
							case 1:
								output("You've grown stronger!");
								if (get_module_setting("permhps")==1){
									$session['user']['maxhitpoints']++;
									output("You `@gain 1 permanent hitpoint`0.");
								}else{
									if ($session['user']['hitpoints']<$session['user']['maxhitpoints']){
										output("You're healed back to normal!");
										$session['user']['hitpoints']=$session['user']['maxhitpoints'];
									}else{
										output("You gain `@10`0 hitpoints!");
										$session['user']['hitpoints']+=10;
									}
								}
							break;
							case 2:
								output("You've grown uglier! Lose 1 Charm.");
								$session['user']['charm']--;
							break;
							case 3:
								output("You've gotten better looking! Gain 1 Charm.");
								$session['user']['charm']++;
							break;
							case 4:
								output("It was nothing.");
							break;
						}
						$allprefs['something']=0;
					}
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//40: Lily Quest
				case 103:
					$lily=$allprefs['lily'];
					if ($lily>0 && $allprefs['return']==0){
						$items=translate_inline(array("","`4Rose Quartz Heart`0","`^Citrine Moonstone`0","`QAmber Star Gem`0","`@Four Leaf Clover`0","`!Blue Diamond`0","`5Violet's Horseshoe`0"));
						if ($lily<4|| $lily==5) output("You find a %s!  It's very beautiful.",$items[$lily]);
						if ($lily==4) output("You find a `@Four Leaf Clover`0!  That's a lucky little trick!");
						if ($lily==6) output("You're ready to strike the wall when you notice a horseshoe on the ground.  What would a horse be doing here? You pick it up and read that it has the word `5Violet`0 engraved on it.  Suddenly, you remember that this is `5Violet's Horseshoe`0!");
						output("`n`nYou realize that you've find one of `&Lily's Items`0! All you have to do is stop by at her office for a reward.");
						$allprefs['usedmts']=$allprefs['usedmts']+1;
						$allprefs['return']=$allprefs['return']+1;
						set_module_pref('allprefs',serialize($allprefs));
					}else output("You hit a patch of sandstone.  It's easy going.  You don't find anything of value, but you don't lose a `^Mine Turn`0.");
					$allprefs['oil']+=1;
				break;
				//Only pickaxe 2 and 3 from this point
				//41: New Pickaxe
				case 104:
					if ($pickaxe==2){
						output("You get ready to work on the mine when suddenly you notice something shiny on the ground.");
						output("You go to look at what it is and discover that it's a very expensive pickaxe!");
						output("`n`nSince this one is much nicer than your current one, you decide to 'upgrade'.");
						$allprefs['pickaxe']=3;
					}else{
						output("You swing at the wall and find a nice vein of %s.",$marray[$metal]);
						$grams=round((e_rand(2000,3000))/$mineturnset);
						output("`n`nYou gather `^%s grams`0 of %s.",$grams,$marray[$metal]);
						$allprefs['metal'.$metal]=$allprefs['metal'.$metal]+$grams;
						$allprefs['metalhof']=$allprefs['metalhof']+$grams;
					}
					$allprefs['usedmts']=$allprefs['usedmts']+1;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//42: Lucky Duck
				case 105:
					output("You discover a fossil of a duck.");
					output("Nothing's luckier than a duck fossil!");
					apply_buff('metalmine',array(
						"name"=>"`^Lucky Duck",
						"rounds"=>3,
						"atkmod"=>1.04,
						"roundmsg"=>"`^It's all about Duck Luck.",
					));
				break;
				//43: Nothing Happens
				case 106:
					output("Nothing happens.  Nope.  Nothing at all.");
				break;
				//Only pickaxe 3 from this point
				//44: Sandstone
				case 107:
					output("You hit a patch of sandstone.  It's easy going.  You don't find anything of value, but you don't lose a `^Mine Turn`0.");
					$allprefs['oil']+=1;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//45: Charm based event
				case 108:
					output("You are about to be crushed by a cave-in.  You call out for help!`n`n");
					if ($session['user']['charm']>20){
						output("Because you're so attractive with a high charm, everyone runs to save you!");
						output("You feel elated by all the attention.");
						apply_buff('metalmine',array(
							"name"=>"`&So Charming",
							"rounds"=>3,
							"atkmod"=>1.04,
							"roundmsg"=>"`&Your charming personality woos the monster",
						));
					}elseif ($session['user']['charm']>5){
						output("Because you're kind of attractive, one or two lonely old miners come to save you.");
						if ($session['user']['hitpoints']>25){
							output("Their delay in helping you causes you to get hit by a rock. You lose `\$25 hitpoints`0.");
							$session['user']['hitpoints']-=25;
						}elseif ($session['user']['hitpoints']>1){
							output("Their delay in helping you causes you to get hit by a rock. You lose `\$all your hitpoints except 1`0.");
							$session['user']['hitpoints']=1;
						}else{
							output("Fortunately, you run away quickly as the dust settles around you.");
							$usedmts=$allprefs['usedmts'];
							if ($usedmts<$mineturnset){
								$allprefs['usedmts']=$allprefs['usedmts']+1;
								output("You spend `^one mine turn`0 coughing.");
							}
						}
					}else{
						output("Because you're charm is so low, nobody comes to save you and you must struggle out on your own.");
						if ($session['user']['hitpoints']>50){
							output("You to get hit by a rock. You lose `\$50 hitpoints`0.");
							$session['user']['hitpoints']-=50;
						}elseif ($session['user']['hitpoints']>1){
							output("You to get hit by a rock. You lose `\$all your hitpoints except 1`0.");
							$session['user']['hitpoints']=1;
						}else{
							output("Fortunately, you run away quickly as the dust settles around you.");
							$usedmts=$allprefs['usedmts'];
							if ($usedmts<$mineturnset){
								$allprefs['usedmts']=$allprefs['usedmts']+1;
								output("You spend `^one mine turn`0 coughing.");
							}
						}
					}
					$allprefs['oil']+=5;
					set_module_pref('allprefs',serialize($allprefs));
				break;
				//46: Motherload
				case 109:
					output("You swing at the wall... MOTHERLOAD!!!!!!!!!!!!");
					$grams=round((e_rand(10000,20000))/$mineturnset);
					output("`n`nYou gather `^%s grams`0 of %s.",$grams,$marray[$metal]);
					$allprefs['metal'.$metal]=$allprefs['metal'.$metal]+$grams;
					$allprefs['metalhof']=$allprefs['metalhof']+$grams;
					$allprefs['usedmts']=$allprefs['usedmts']+1;
					$allprefs['oil']+=1;
					set_module_pref('allprefs',serialize($allprefs));
				break;
			}
			$allprefs=unserialize(get_module_pref('allprefs'));
			$usedmts=$allprefs['usedmts'];
			$mineturns=$mineturnset-$usedmts;
			if ($mineturns>0) output("`n`nYou have `^%s Mine %s`0 left.",$mineturns,translate_inline($mineturns>1?"Turns":"Turn"));
			elseif ($session['user']['hitpoints']>0) output("`n`nYou've used up all your `^Mine Turns`0 for the day. It's probably time for you to head out.");
			if ($usedmts<$mineturnset){
				addnav("Work The Mine More","runmodule.php?module=metalmine&op=work");
				if (get_module_setting("limitloc")<=1) addnav("Travel To a Different Area","runmodule.php?module=metalmine&op=travel");
			}
			addnav("Leave Mine","runmodule.php?module=metalmine&op=leavemine");
		}
	}
}
?>