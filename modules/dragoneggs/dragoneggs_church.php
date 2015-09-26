<?php
function dragoneggs_church(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Old Church");
	//If the player isn't in the right city, move them to it.
	if ($session['user']['location']!= get_module_setting("oldchurchplace","oldchurch")){
		$session['user']['location'] = get_module_setting("oldchurchplace","oldchurch");
	}
	$open=get_module_setting("churchopen");
	if ($open==0 && $session['user']['dragonkills']<get_module_setting("churchmin") && get_module_setting("churchlodge")>0 && get_module_pref("churchaccess")==0){
		output("You don't have enough `@Green Dragon Kills`3 to research here.  Once you do, you will need to purchase access through the lodge. Please check back frequently to see if you qualify. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && $session['user']['dragonkills']<get_module_setting("churchmin")+get_module_setting("mindk")){
		output("You don't have enough `@Green Dragon Kills`3 to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && get_module_setting("churchlodge")>0 && get_module_pref("churchaccess")==0){
		output("You will need to purchase access through the Lodge to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif (get_module_pref("researches")>=get_module_setting("research")){
		output("`3You're out of research turns for today.");
	}else{
		output("`3You decide to look for Dragon Eggs at the Church.`n`n");
		$rumor36=0;
		if (is_module_active("rumors")){
			if($session['user']['dragonkills']>=get_module_setting("mindk","rumors")+get_module_setting("mindk")) $rumor36=1; 
		}
		if($rumor36==1) $case=e_rand(1,36);
		else $case=e_rand(1,35);
		increment_module_pref("researches",1);
		switch($case){
		//switch(1){
			case 1: case 2:
				output("`^'You should consider sharing your wealth with the poor,'`3 says `5Capelthwaite`3.`n`n");
				if (($session['user']['gold']+$session['user']['goldinbank']>10000) && e_rand(1,2)==1){
					output("Realizing that you've been very fortunate to be so wealthy, you decide to give generously to the church.`n`n");
					if ($session['user']['gold']>=1000){
						output("You hand over `^1,000 gold`3 to help the poor.");
						$session['user']['gold']-=1000;
						debuglog("gave 1000 gold for a super blessing while researching dragon eggs at Church.");
					}else{
						output("You hand over all the money you have in addition to money from your bank account to donate a total of `^1000 gold`3 to the church.");
						$gold=$session['user']['gold'];
						$session['user']['gold']=0;
						$session['user']['goldinbank']=$session['user']['goldinbank']-1000+$gold;
						debuglog("gave $gold gold from on hand and the rest from their bank account to donate a total of 1000 gold for a super blessing  while researching dragon eggs at Church.");
					}
					output("`5Capelthwaite`3 gives you his blessing.");
					output("You are `iVERY Blessed`i!");
					if (isset($session['bufflist']['blesscurse'])) strip_buff('blesscurse');
					if (isset($session['bufflist']['veryblessed'])) {
						$session['bufflist']['veryblessed']['rounds'] += 8;
					}else{
						apply_buff('veryblessed',
							array("name"=>translate_inline("Very Blessed"),
								"rounds"=>15,
								"wearoff"=>translate_inline("The burst of energy passes."),
								"atkmod"=>1.5,
								"defmod"=>1.5,
								"roundmsg"=>translate_inline("Blessed Energy flows through you!"),
							)
						);
					}
				}elseif (($session['user']['gold']+$session['user']['goldinbank']>2000) && e_rand(1,2)==1){
					output("Realizing how lucky you've been, you decide to share some of your wealth with the church.");
					if ($session['user']['gold']>=300){
						output("You hand over `^300 gold`3 to help the poor.");
						$session['user']['gold']-=300;
						debuglog("gave 1000 gold for a super blessing while researching dragon eggs at Church.");
					}else{
						output("You hand over all the money you have in addition to money from your bank account to donate a total of `^300 gold`3 to the church.");
						$gold=$session['user']['gold'];
						$session['user']['gold']=0;
						$session['user']['goldinbank']=$session['user']['goldinbank']-300+$gold;
						debuglog("gave $gold gold from on hand and the rest from their bank account to donate a total of 300 gold for a blessing  while researching dragon eggs at Church.");
					}
					output("`5Capelthwaite`3 gives you his blessing.");
					if ($session['bufflist']['blesscurse']['atkmod']==1.2) {
						$session['bufflist']['blesscurse']['rounds'] += 5;
						output("You are `iMore Blessed`i!");
					}else{
						apply_buff('blesscurse',
							array("name"=>translate_inline("Blessed"),
								"survivenewday"=>1,
								"rounds"=>15,
								"wearoff"=>translate_inline("The burst of energy passes."),
								"atkmod"=>1.2,
								"defmod"=>1.1,
								"roundmsg"=>translate_inline("Energy flows through you!"),
							)
						);
						output("You are `iBlessed`i!");
					}
				}elseif(($session['user']['gold']+$session['user']['goldinbank']>200) && e_rand(1,2)==1){
					output("Realizing that there are people in much worse shape than you're in, you decide to share some of your wealth with the church.");
					if ($session['user']['gold']>=50){
						output("You hand over `^50 gold`3 to help the poor.");
						$session['user']['gold']-=50;
						debuglog("gave 50 gold while researching dragon eggs at Church.");
					}else{
						output("You hand over all the money you have in addition to money from your bank account to donate a total of `^50 gold`3 to the church.");
						$gold=$session['user']['gold'];
						$session['user']['gold']=0;
						$session['user']['goldinbank']=$session['user']['goldinbank']-50+$gold;
						debuglog("gave $gold gold from on hand and the rest from their bank account to donate a total of 50 gold while researching dragon eggs at Church.");
					}
					output("`5Capelthwaite`3 thanks you for your gift.");
				}elseif($session['user']['gold']+$session['user']['goldinbank']>10){
					output("Realizing that there are people in much worse shape than you're in, you decide to share some of your wealth with the church.");
					if ($session['user']['gold']>=10){
						output("You hand over `^10 gold`3 to help the poor.");
						$session['user']['gold']-=10;
						debuglog("gave 10 gold while researching dragon eggs at Church.");
					}else{
						output("You hand over all the money you have in addition to money from your bank account to donate a total of `^10 gold`3 to the church.");
						$gold=$session['user']['gold'];
						$session['user']['gold']=0;
						$session['user']['goldinbank']=$session['user']['goldinbank']-10+$gold;
						debuglog("gave $gold gold from on hand and the rest from their bank account to donate a total of 10 gold while researching dragon eggs at Church.");
					}
					output("`5Capelthwaite`3 says `^'Thank you. Every little bit helps.'");
				}else{
					output("`5Capelthwaite`3 looks at you and says `^'You are the poor that we want to help.  You may have some food and some alms.'`3");
					output("`n`nHe hands you a delicious soup and gives you `^7 gold`3.");
					if (isset($session['bufflist']['churchsoup'])) {
						$session['bufflist']['churchsoup']['rounds'] += 3;
					}else{
						apply_buff('churchsoup',
							array("name"=>translate_inline("Soup"),
								"rounds"=>5,
								"defmod"=>1.03,
								"roundmsg"=>translate_inline("The soup warms your soul."),
							)
						);
					}
					$session['user']['gold']+=7;
					debuglog("gained a soup buff and 7 gold while researching dragon eggs at the Church.");
				}
			break;
			case 3: case 4:
				output("`^'THE DEVIL!!! DEMON BEGONE!!!!'`3 you hear `5Capelthwaite`3 yelling as he charges at you with a giant cross.");
				addnav("Fight Capelthwaite","runmodule.php?module=dragoneggs&op=church3");
				blocknav("runmodule.php?module=oldchurch");
				blocknav("village.php");
			break;
			case 5: case 6:
				output("It's time for you to confess your sins. You enter the confessional.  What are you going to tell `5Capelthwaite`3?");
				addnav("Sins");
				addnav("Took Candy From a Baby","runmodule.php?module=dragoneggs&op=church5&op2=1");
				addnav("Ran with Scissors","runmodule.php?module=dragoneggs&op=church5&op2=2");
				addnav("Left Toilet Seat Up","runmodule.php?module=dragoneggs&op=church5&op2=3");
				addnav("Ate Food on Floor AFTER 5 Seconds","runmodule.php?module=dragoneggs&op=church5&op2=4");
				blocknav("runmodule.php?module=oldchurch");
				blocknav("village.php");
			break;
			case 7: case 8:
				output("Exhausted from all your adventures you decide to sit down and enjoy the heavenly choir music.");
				output("`n`nIt brings you peace and you feel one with the world.  Your mission is clarified! You are ready to face the world!");
				output("`n`nYou `@gain 2 extra turns`3 and `%1 gem`3!");
				$session['user']['turns']+=2;
				$session['user']['gems']++;
				debuglog("gained 2 turns and 1 gem while researching dragon eggs at the Church.");
			break;
			case 9: case 10:
				output("While looking around the church you find yourself sitting in one of the pews.  Next thing you notice is that you're looking at the prayer books and hymnals.");
				output("`n`nOne particular book seems a bit more interesting.  Will you take a closer look?");
				addnav("Examine the Book","runmodule.php?module=dragoneggs&op=church9");
			break;
			case 11: case 12:
				if ($session['user']['gems']>=2){
					output("`^'I can help you,' `5Capelthwaite`3 explains. `^'What you lack is experience.  Share some of your gems with me and I'll share some knowledge with you.'");
					output("`n`n`3You have the chance to spend `%2 gems`3 for some experience.  Will you exchange with `5Capelthwaite`3?");
					addnav("Exchange 2 gems","runmodule.php?module=dragoneggs&op=church11");
				}else{
					output("`^'I will help you if you had something I need,'`5 Capelthwaite`3 explains.  You ask what he's looking for and he explains that he wants 2 gems.");
					output("`n`nUnfortunately, you can't help him.");
				}
			break;
			case 13: case 14:
				output("You look through the large rose window and you are bathed in light. A sudden calm spreads across you.");
				output("`n`nA wave of bravery sweeps across you and the people around you notice that you carry yourself with a little more pride.");
				output("`n`nYou `&gain 1 charm`3.");
				$session['user']['charm']++;
				debuglog("gained a charm while researching dragon eggs at the Church.");
			break;
			case 15: case 16:
				output("As you look around you see the holy water and ask `5Capelthwaite`3 if you can bring some with you for protection.");
				output("`n`n`^'Take what you need.  May it protect you from the dark places,'`3 he says.");
				if ($session['user']['level']>8) $damage=8;
				else $damage=max($session['user']['level'],3);
				if (isset($session['bufflist']['holywater'])) {
					output("`n`nYou take enough Holy Water to last for 10 rounds.  That's as much as you can carry.");
				}
				apply_buff('holywater',array(
					"name"=>translate_inline("Holy Water"),
					"rounds"=>10,
					"survivenewday"=>1,
					"startmsg"=>translate_inline("`5You sprinkle some holy water on your {weapon}`5."),
					"wearoff"=>translate_inline("You run out of holy water."),
					"minioncount"=>1,
					"effectmsg"=>translate_inline("`5The holy water causes an additional `^{damage}`5 damage."),
					"effectnodmgmsg"=>translate_inline("The holy water has no effect."),
					"effectfailmsg"=>translate_inline("The holy water has no effect."),
					"minbadguydamage"=>0,
					"maxbadguydamage"=>$damage,
				));
				debuglog("gained holy water buff that survives the newday while researching dragon eggs at the Church.");
			break;
			case 17: case 18:
				output("You have a rare opportunity to commune with the most devout of monks.`n`n");
				if ($session['user']['gems']>0){
					output("However, their assistance is not without cost.  Will you give them a `%gem`3 to learn from them?");
					addnav("Give 1 gem","runmodule.php?module=dragoneggs&op=church17");
				}else{
					output("`@'Give us a gem and we will help you,'`3 they explain.");
					output("`n`n`#'But I don't have a gem,'`3 you say.");
					output("`n`n`@'Oh.  Too bad for you then,'`3 the monks say as they leave.");
					output("`n`nTalk about missed opportunities!!");
				}
			break;
			case 19: case 20:
				output("You storm into a room yelling `#'SHOW ME THE `%GEMS`#!!!'`3");
				output("`n`nThen you realize you just burst into a funeral. Ooops!`n`n");
				if ($session['user']['charm']>1){
					output("Flushed and embarrassed, you `&lose 2 charm points`3.`n`n");
					$session['user']['charm']-=2;
					debuglog("lost 2 charm while researching dragon eggs at the Church.");
				}
				output("You beat a hasty retreat back to the village.");
				blocknav("runmodule.php?module=oldchurch");
			break;
			case 21: case 22:
				output("You sit down and have a long chat with `5Capelthwaite`3.  You explain your fears, your worries, you dread.");
				output("`n`n`^'There is a reason for everything, my child,'`3 he says. He talks to you for a little while longer and you feel the darkness lift.");
				output("`n`nYou find yourself with the energy to explore `@2 more times`3.");
				$session['user']['turns']+=2;
				debuglog("gained 2 turns while researching dragon eggs at the Church.");
			break;
			case 23: case 24:
				output("You look around and one of the stone gargoyles grabs for you...");
				output("`n`nOr maybe it didn't.  You go back and take a look again.`n`n");
				if ($session['user']['turns']>0){
					output("You `@spend a turn`3 looking at the gargoyle but nothing happens.");
					$session['user']['turns']--;
					debuglog("spent a turn looking at a gargoyle while researching dragon eggs at the Church.");
				}else{
					output("You're totally creeped out! It's a feeling that's not going to go away.");
					if (isset($session['bufflist']['gargoylecreep'])) {
						$session['bufflist']['gargoylecreep']['rounds'] += 5;
						debuglog("increased the gargoylecreep buff by 5 rounds while researching dragon eggs at the Church.");
					}else{
						apply_buff('gargoylecreep',array(
							"name"=>translate_inline("Creeped Out"),
							"rounds"=>20,
							"wearoff"=>translate_inline("`#You're no longer creeped out."),
							"atkmod"=>.9,
							"survivenewday"=>1,
						));
						debuglog("received the gargoylecreep buff while researching dragon eggs at the Church.");
					}
				}
			break;
			case 25: case 26: case 27: case 28:
				output("`^'I give you my blessing,'`3 says `5Capelthwaite`3.");
				output("You are `iBlessed`i!");
				if ($session['bufflist']['blesscurse']['atkmod']==1.2) {
					$session['bufflist']['blesscurse']['rounds'] += 5;
					debuglog("increased their blessing by 5 rounds while researching dragon eggs at the Church.");
				}else{
					apply_buff('blesscurse',
						array("name"=>translate_inline("Blessed"),
							"survivenewday"=>1,
							"rounds"=>15,
							"wearoff"=>translate_inline("The burst of energy passes."),
							"atkmod"=>1.2,
							"defmod"=>1.1,
							"roundmsg"=>translate_inline("Energy flows through you!"),
						)
					);
					debuglog("was blessed while researching dragon eggs at the Church.");
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
	addnav("Return to the Church","runmodule.php?module=oldchurch");
	villagenav();
}
?>