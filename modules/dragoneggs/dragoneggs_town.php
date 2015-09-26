<?php
function dragoneggs_town(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	$vname = getsetting("villagename", LOCATION_FIELDS);
	$header = color_sanitize($vname);
	page_header("%s Square",$header);
	output("`c`b`@%s Square`@`b`c",$vname);
	$session['user']['location']=$vname;
	if (get_module_pref("researches")>=get_module_setting("research")){
		output("`@You're out of research turns for today.");
	}else{
		if ($op2=="case27") $case=27;
		else{
			output("`@You decide to look for Dragon Eggs at %s Square`@.`n`n",$vname);
			$rumor36=0;
			if (is_module_active("rumors")){
				if($session['user']['dragonkills']>=get_module_setting("mindk","rumors")+get_module_setting("mindk")) $rumor36=1; 
			}
			if($rumor36==1) $case=e_rand(1,36);
			else $case=e_rand(1,35);
		}
		increment_module_pref("researches",1);
		debuglog("used a research turn in the Capital Town Square.");
		//switch($case){
		switch(5){
			case 1: case 2: case 3: case 4:
				output("You stop by and notice a small pinpoint of light that's eminating from the middle of the %s Square`@.",$vname);
				output("Upon closer research, you realize it's a monster teleporting here! Suddenly a huge insect-like creature appears carrying a dragon egg!");
				output("`n`nYou must defeat the `\$Rhthithc`@!");
				set_module_pref("monster",5);
				addnav("Fight the `\$Rhthithc","runmodule.php?module=dragoneggs&op=attack");
				blocknav("village.php");
			break;
			case 5: case 6: case 7: case 8:
				output("As you're looking around the square, you notice a make-shift table with a gypsy sitting at it.");
				output("`n`n`2'Tell your fortune, only `^50 gold`2,'`@ she says.");
				output("`n`nConfused by her presence, you mention that her price seems a little more reasonable than at the `%'Gypsy Seer's Tent`@.");
				output("`n`n`2'Yes, that's true, but it's probably because I have lower overhead,'`@ she explains as she shows off her rickety table.`n`n");
				$hps=$session['user']['hitpoints'];
				$level=$session['user']['level'];
				$chance=e_rand(1,7);
				if (($level>6 && $chance<=3) || ($level<=6 && $chance<=2)){
					output("`#'I don't really need my fortune read, but I have something important I'm doing,'`@, you tell her.  You explain your concerns about the rising of the `bGreen Dragons`b and `bZithria the Gypsy`b agrees to accompany you instead of reading your future.");
					if (isset($session['bufflist']['ally'])) {
						if ($session['bufflist']['ally']['type']=="zithria"){
							$ally=1;
						}else{
							output("`n`nRealizing that you've found help from someone new, %s`@ decides to leave.",$session['bufflist']['ally']['name']);
							$ally=0;
						}
					}else $ally=0;
					if ($ally==0){
						apply_buff('ally',array(
							"name"=>translate_inline("`@Zithria the Gypsy"),
							"rounds"=>60,
							"survivenewday"=>1,
							"wearoff"=>translate_inline(array("Zithria leaves to go back to work in  %s Square.",$vname)),
							"regen"=>ceil($session['user']['level']/3),
							"effectmsg"=>translate_inline("Zithria casts a healing spell on you for {damage} hitpoints."),
							"effectnodmgmsg"=>translate_inline("You have no wounds to heal."),
							"type"=>"zithria",
						));
						output("`n`nYou gain the help of Zithria the Gypsy!");
						debuglog("gained the help of ally Zithria the Gypsy by researching at the Capital Town Square.");
					}else{
						$session['bufflist']['ally']['rounds'] += 20;
						output("`n`n`@Zithria the Gypsy decides to help you out for another `^20 rounds`@!");
						debuglog("gained the help of ally Zithria the Gypsy for an additional 20 rounds by researching at the Capital Town Square.");
					}
					if (is_module_active("dlibrary")){
						if (get_module_setting("ally4","dlibrary")==0){
							set_module_setting("ally4",1,"dlibrary");
							addnews("%s`^ was the first person to meet `@Zithria the Gypsy`^ at %s Square`^.",$session['user']['name'],$vname);
						}
					}
				}else{
					output("You give her a polite 'nod and smile' and get on your way.  You don't want your fortune read by a second-rate hack!");
				}	
			break;
			case 9: case 10:
				output("You sit down by the fountain and hear a band strike up a tune.");
				output("`n`nYou find it quite soothing. You `\$gain 25 hitpoints`@.");
				$session['user']['hitpoints']+=25;
				debuglog("gained 25 hitpoints by researching at the Capital Town Square.");
			break;
			case 11: case 12:
				output("You notice a gold piece stuck in a crack.`n`n");
				if ($session['user']['turns']>0){
					$session['user']['turns']--;
					$session['user']['gold']++;
					output("Deciding that you are going to get that gold piece you `bspend a turn`b digging it out.");
					output("`n`nYou gain `^1 gold`@ but lose a turn.");
					debuglog("gained a gold but spent a turn by researching at the Capital Town Square.");
				}else{
					output("You're not going to waste your time on a gold piece so you keep looking.");
				}
			break;
			case 13: case 14:
				output("You see a group of Gypsies gathering in the Square.");
				output("`n`nWould you like to talk to them?");
				addnav("Chat with the Gypsies","runmodule.php?module=dragoneggs&op=town13");
			break;
			case 15: case 16:
				output("Not finding much of interest, you sit back on the fountain and have a short rest.");
				$hps=$session['user']['hitpoints'];
				$level=$session['user']['level'];
				$chance=e_rand(1,11);
				if (($level>6 && $chance<=3) || ($level<=6 && $chance<=2)){
					if ($session['user']['turns']>0){
						output("Suddenly, you gain an insight into all the problems that are facing the kingdom.");
						output("You sit pensive for a few moments and a flood of answers hits you.");
						output("`n`nYou `blose one turn`b and `\$all your hitpoints except one`@ but you `%gain 2 gems`@ and `&increment in your specialty`@!`n");
						require_once("lib/increment_specialty.php");
						increment_specialty("`@");
						$session['user']['turn']--;
						$session['user']['hitpoints']=1;
						$session['user']['gems']+=2;
						debuglog("lost a turn and all hitpoints except 1 to increment specialty and gain 2 gems.");
					}else{
						output("If only you had a turn to spend thinking, you'd probably gain something amazing like an insight giving you 2 gems or something like that.");
					}
				}else{
					output("You suddenly notice a dragon egg!");
					output("You can probably destroy it if you use `%5 gems`@ to cast a spell!");
					if ($session['user']['gems']>=5) addnav("Destroy the Dragon Egg","runmodule.php?module=dragoneggs&op=town15");
					else{
						output("`n`nUnfortunately, you just don't have enough gems to destroy it so you quietly shuffle away whistling inconspicuously.`n`n");
						if (get_module_setting("townegg")==0){
							output("You end up leaving the egg in %s Square`@ and hope someone else will be able to destroy it.",$vname);
							set_module_setting("townegg",1);
						}else output("Oh well. You leave the egg for someone else to deal with.");
						set_module_setting("deserter",$session['user']['name']);
						addnews("`@A dragon egg was found in %s Square`@ and %s`@ was seen walking away from it.  How peculiar, wouldn't you say?",$vname,$session['user']['name']);
					}
				}
			break;
			case 17: case 18:
				output("You see several people enjoying a nice tasty beverage.  You stop to enquire what it is and they invite you to have a drink yourself.");
				output("`n`nAre you feeling adventurous?");
				addnav("Drink the Strange Brew","runmodule.php?module=dragoneggs&op=town17");
			break;
			case 19: case 20:
				output("A group of gypsy children swarm around you.`n`n");
				$chance=e_rand(1,9);
				$level=$session['user']['level'];
				if (($level>10 && $chance<=4) || ($level<=10 && $chance<=3)){
					output("Suspicious of pickpockets, you get away from them as quickly as possible. You do a quick survey and nothing's missing.");
				}else{
					$gold=$session['user']['gold'];
					if ($gold>0){
						output("You smile at the attention, laugh with the kids, and they depart.`n`n");
						output("You notice you're missing your gold pouch!");
						if ($gold>500){
							output("`n`nLuckily, they only stole `^500 gold`@ from you.");
							$session['user']['gold']-=500;
							debuglog("lost 500 gold while researching in the Capital Town Square."); 
						}else{
							output("`n`nThey got all your gold, those little urchins!");
							$session['user']['gold']=0;
							debuglog("lost $gold gold while researching in the Capital Town Square."); 
						}
					}elseif ($session['user']['gems']>0){
						output("You smile at the attention, laugh with the kids, and they depart.`n`n");
						output("You notice you're missing one of your gems!");
						$session['user']['gems']--;
						debuglog("lost a gem while researching in the Capital Town Square.");
					}elseif ($session['user']['turns']>0){
						output("Worried that they're trying to steal your hat you spend a turn evading them.");
						$session['user']['turns']--;
						debuglog("lost a turn while researching in the Capital Town Square.");
					}else{
						output("Suspicious of pickpockets, you get away from them as quickly as possible. You do a quick survey and nothing's missing.");
					}
				}
			break;
			case 21: case 22:
				output("You see the sheriff in the square and he starts to approach you.`n`n");
				$level=$session['user']['level'];
				$chance=e_rand(1,5);
				if (($level>6 && $chance<=3) || ($level<=6 && $chance<=2)){
					output("You inform him that you're in the middle of some very serious research and he leaves you alone.");
				}else{
					if (is_module_active("djail")||is_module_active("jail")){
						$deputy=0;
						if (is_module_active("jail")) $jail="jail";
						else $jail="djail";
						if (is_module_active("djail")){
							if (get_module_pref("deputy","djail")==1) $deputy=1;
						}
						if ($deputy==1) output("You show him your deputy badge and he apologizes for bothering you.");
						else{
							output("`#'You're wanted for questioning,'`@ he explains.  You're escorted to the Jail where you're questioned and released right away.");
							blocknav("village.php");
							if (get_module_setting("oneloc",$jail)==1){
									$session['user']['location']=get_module_setting("jailloc",$jail);
							}
							addnav("The Jail","runmodule.php?module=$jail");
						}
					}else output("You inform him that you're in the middle of some very serious research and he leaves you alone.");
				}
			break;
			case 23: case 24:
				output("You feel as if a a shadow is passing over you.  You look up but the sky is empty.`n`n");
				$hps=$session['user']['hitpoints'];
				$level=$session['user']['level'];
				$chance=e_rand(1,5);
				if (($level>6 && $chance<=3) || ($level<=6 && $chance<=2)){
					output("You shrug it off and leave.");
				}else{
					output("You shiver with fear and trip over your feet `\$losing a hitpoint`@.");
					$session['user']['hitpoints']--;
					if ($session['user']['hitpoints']<=0){
						debuglog("died by tripping while researching the Capital Town Square.");
						blocknav("village.php");
						$session['user']['hitpoints']=0;
						$session['user']['alive']=false;
						//addnav("Continue","shades.php");
					}else{
						debuglog("lost a hitpoint while researching the Town Square.");
					}
				}
			break;
			case 25: case 26:
				output("You start searching a small squirrel to see if it's covering up a `&Dragon Egg`@. Your research is fruitless.");
			break;
			case 27: case 28:
				if ($op2!="case27") {
					output("You see `%Zithria the Gypsy`@ and ask her to tell you your fortune.");
					output("She looks up at you and takes out a magical black ball.  You anticipate an amazing fortune, and she starts to shake the black ball.  You look closely and see a number '8' on the side of it.`n`n");
					if (is_module_active("metalmine")){
						output("Suddenly, you become very suspicious that you may have seen something like that on a pedastal in the Metal Mine. Hmmmmm.`n`n");
						if (e_rand(1,100)==1) output("Yup...this looks like almost the exact same thing.`n`n");
					}
				}else output("`%Zithria`@ decides to ask again.`n`n");
				$op3=e_rand(1,5);
				$question=translate_inline(array("","Rich","Pretty","Strong","Fast","Smart"));
				output("She asks the `)Magic Black Ball`@ a question:");
				output("`n`n`%'Will %s`% be %s?'`@`n`nShe flips over the ball and looks at you...",$session['user']['name'],$question[$op3]);
				$ball=translate_inline(array("","Outlook Good","You May Rely On It","Most Likely","Yes","Yes Definitely","It Is Certain","It Is Decidedly So","Signs Point To Yes","Without A Doubt","As I See It, Yes","Outlook Not So Good","My Reply Is No","Don't Count On It","Very Doubtful","My Sources Say No","Ask Again Later","Concentrate and Ask Again","Reply Hazy,  Try Again","Better Not Tell You Now"));
				$rand=e_rand(1,20);
				output("`n`n`c`#%s`@`c`n",$ball[$rand]);
				if ($rand<=10){
					require_once("lib/names.php");
					$joke=e_rand(1,5);
					if ($op3=="1"){
						if ($joke==1){
							$newtitle = translate_inline("Rich");
							$newname = change_player_title($newtitle);
							$session['user']['title'] = $newtitle;
							$session['user']['name'] = $newname;
							output("`%'Yes! Now you're Rich!'");
							debuglog("changed title to Rich by talking to a Gypsy while researching the Capital Town Square.");
						}else{
							$gold=e_rand(250,500);
							output("`%'Yes! Now you're Rich!'`@ You look down and see `^%s gold`@ at your feet.",$gold);
							$session['user']['gold']+=$gold;
							debuglog("gained $gold gold by talking to a Gypsy while researching the Capital Town Square.");
							
						}
					}elseif ($op3=="2"){
						if ($joke==1){
							$newtitle = translate_inline("Pretty");
							$newname = change_player_title($newtitle);
							$session['user']['title'] = $newtitle;
							$session['user']['name'] = $newname;
							output("`%'Yes! Now you're Pretty!'");
							debuglog("changed title to Pretty by talking to a Gypsy while researching the Capital Town Square.");
						}else{
							output("`%'Yes! Now you're Pretty!'`@ You suddenly feel more pretty!  You gain `%One Charm`@!");
							$session['user']['charm']++;
							debuglog("gained a charm by talking to a Gypsy while researching the Capital Town Square.");
						}
					}elseif ($op3=="3"){
						if ($joke<=3){
							$newtitle = translate_inline("Strong");
							$newname = change_player_title($newtitle);
							$session['user']['title'] = $newtitle;
							$session['user']['name'] = $newname;
							output("`%'Yes! Now you're Strong!'");
							debuglog("changed title to Strong by talking to a Gypsy while researching the Capital Town Square.");
						}else{
							output("`%'Yes! Now you're Strong!'`@ You suddenly feel stronger!  You gain `&One Attack`@!");
							$session['user']['attack']++;
							debuglog("gained an attack by talking to a Gypsy while researching the Capital Town Square.");
						}			
					}elseif ($op3=="4"){
						if ($joke<=3){
							$newtitle = translate_inline("Fast");
							$newname = change_player_title($newtitle);
							$session['user']['title'] = $newtitle;
							$session['user']['name'] = $newname;
							output("`%'Yes! Now you're Fast!'");
							debuglog("changed title to Fast by talking to a Gypsy while researching the Capital Town Square.");
						}else{
							output("`%'Yes! Now you're Fast!'`@ You suddenly feel faster!  You gain `&One Defense`@!");
							$session['user']['defense']++;
							debuglog("gained a defense by talking to a Gypsy while researching the Capital Town Square.");
						}
					}else{
						if ($joke<=3){
							$newtitle = translate_inline("Smart");
							$newname = change_player_title($newtitle);
							$session['user']['title'] = $newtitle;
							$session['user']['name'] = $newname;
							output("`%'Yes! Now you're Smart!'");
							debuglog("changed title to Smart by talking to a Gypsy while researching the Capital Town Square.");
						}else{
							$expmultiply = e_rand(5,8);
							$expbonus=$session['user']['dragonkills'];
							$expgain =($session['user']['level']*$expmultiply+$expbonus);
							$session['user']['experience']+=$expgain;
							output("`%'Yes! Now you're Smart!'`@ You suddenly feel smarter!  You gain `#%s experience`@!",$expgain);
							debuglog("gained $expgain experience by talking to a Gypsy while researching the Capital Town Square.");
						}
					}
				}elseif ($rand<=15){
					if ($op3=="1"){
						$gold=$session['user']['gold'];
						if ($gold>100){
							output("`%'No, you will not be Rich.'@");
							if ($gold<300){
								$session['user']['gold']=0;
								output("You find all your `^money`@ has disappeared.");
								debuglog("lost all their money by talking to a Gypsy while researching the Capital Town Square.");
							}else{
								$session['user']['gold']-=250;
								output("You find that you've lost `^250 gold`@.");
								debuglog("lost 250 gold by talking to a Gypsy while researching the Capital Town Square.");
							}
						}elseif ($session['user']['gems']>0){
							$session['user']['gems']--;
							output("`%'No, you will not be rich, you'll be gemless!'");
							output("`@You find that you've lost a `%gem`@.");
							debuglog("lost a gem by talking to a Gypsy while researching the Capital Town Square.");
						}else{
							output("`%'No, you're poor now and you'll stay poor,'`@ she tells you.");
						}
					}elseif ($op3=="2"){
						output("`%'No, you will not be Pretty.'@");
						output("She hits you with a stick with the word `&'Ugly'`@ written on it.");
						output("`n`nYou've been hit with the `&Ugly Stick`@! You `\$lose one charm`@.");
						$session['user']['charm']--;
						debuglog("lost a charm by talking to a Gypsy while researching the Capital Town Square.");
					}elseif ($op3==3 || $op3==4){
						if ($op3==3) output("`%'No, you will not be Strong.'`@");
						else output("`%'No, you will not be Fast.'`@");
						output("You feel sick to your stomach.  You feel weaker!");
						apply_buff('blackball',array(
							"name"=>translate_inline("`)Black Ball Weakness"),
							"rounds"=>10,
							"wearoff"=>translate_inline("Outlook Good for you to start feeling better!"),
							"atkmod"=>.75,
							"roundmsg"=>translate_inline("Outlook Not So Good for you to feel better yet."),
						));
						debuglog("gained a black ball weakness buff by talking to a Gypsy while researching the Capital Town Square.");
					}else{
						output("`%'No, you will not be the most smarter.'@");
						output("You feel so stupid! It's like you've forgotten things you've learned in the past.");
						if ($session['user']['experience']>100){
							output("You `\$lose 100 experience`@ points.");
							$session['user']['experience']-=100;
							debuglog("lost 100 exp by talking to a Gypsy while researching the Capital Town Square.");
						}else{
							output("You `\$lose all your experience`@.");
							$session['user']['experience']=0;
							debuglog("lost all experience by talking to a Gypsy while researching the Capital Town Square.");
						}
					}
				}elseif ($rand<=19){
					output("Okay, go ahead and ask again.");
					addnav("Magic Black Ball");
					addnav("Ask Again","runmodule.php?module=dragoneggs&op=town&op2=case27");
					increment_module_pref("researches",-1);
					blocknav("village.php");
				}else{
					output("You can't believe that you didn't get an answer.  You tell her to ask again, but she says that the `)Magic Black Ball`@ is ignoring you.");		
				}
			break;
			case 29: case 30: case 31: case 32: case 33: case 34: case 35:
				output("You don't find anything of value.");
			break;
			case 36:
				dragoneggs_case36();
			break;
		}
	}
	villagenav();
}
?>