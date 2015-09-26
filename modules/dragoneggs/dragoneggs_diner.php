<?php
function dragoneggs_diner(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Hara's Bakery");
	output("`n`c`b`@Hara's `^Bakery`b`c`#`n");
	if ($session['user']['location']!=get_module_setting("bakeryloc","bakery")){
		$session['user']['location']=get_module_setting("bakeryloc","bakery");
	}
	$open=get_module_setting("dineropen");
	if ($open==0 && $session['user']['dragonkills']<get_module_setting("dinermin") && get_module_setting("dinerlodge")>0 && get_module_pref("dineraccess")==0){
		output("You don't have enough `@Green Dragon Kills`# to research here.  Once you do, you will need to purchase access through the lodge. Please check back frequently to see if you qualify. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && $session['user']['dragonkills']<get_module_setting("dinermin")+get_module_setting("mindk")){
		output("You don't have enough `@Green Dragon Kills`# to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && get_module_setting("dinerlodge")>0 && get_module_pref("dineraccess")==0){
		output("You will need to purchase access through the Lodge to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif (get_module_pref("researches")>=get_module_setting("research")){
		output("You're out of research turns for today.");
	}else{
		output("You decide to look for Dragon Eggs at Hara's Bakery.`n`n");
		increment_module_pref("researches",1);
		$rumor36=0;
		if (is_module_active("rumors")){
			if($session['user']['dragonkills']>=get_module_setting("mindk","rumors")+get_module_setting("mindk")) $rumor36=1; 
		}
		if($rumor36==1) $case=e_rand(1,36);
		else $case=e_rand(1,35);
		debuglog("used a research turn in Hara's Bakery.");
		switch($case){
		//switch(27){
			case 1: case 2: case 3: case 4:
				output("Not finding anything useful, you decide it might be worth having some of that nice `@Apple Pie`# that Hara's got in the display case.");
				output("`n`n`%'Only `^50 gold`% a slice,'`# she tells you.`n`n");
				$max=floor($session['user']['gold']/50);
				if ($max>0){
					if ($max<6){
						output("You count your money and realize you can afford to buy `@%s pieces of pie`#.",$max);
					}else{
						$max=6;
						output("You figure you can probably down about `@6 pieces of pie`# if you want to spend that much money.");
					}
					addnav("Purchase Pie");
					addnav("Purchase 1 Piece","runmodule.php?module=dragoneggs&op=diner1&op2=1");
					if ($max>1) addnav("Purchase 2 Pieces","runmodule.php?module=dragoneggs&op=diner1&op2=2");
					if ($max>2) addnav("Purchase 3 Pieces","runmodule.php?module=dragoneggs&op=diner1&op2=3");
					if ($max>3) addnav("Purchase 4 Pieces","runmodule.php?module=dragoneggs&op=diner1&op2=4");
					if ($max>4) addnav("Purchase 5 Pieces","runmodule.php?module=dragoneggs&op=diner1&op2=5");
					if ($max>5) addnav("Purchase 6 Pieces","runmodule.php?module=dragoneggs&op=diner1&op2=6");
					addnav("Leave");
				}else{
					output("You realize you don't have enough cash on hand to buy any pie, so `%Hara`# gives you the scrapings from the last pie.");
					output("`n`nHaving paid nothing for it, you get no real joy from the piece of pie.");
				}
			break;
			case 5: case 6:
				output("You see `^100 gold`# on the floor.  Someone must have dropped it! Would you like to grab it?");
				addnav("Grab the Gold","runmodule.php?module=dragoneggs&op=diner5");
			break;
			case 7: case 8:
				$chance=e_rand(1,9);
				$level=$session['user']['level'];
				output("You see a rat running into the kitchen.");
				if (($level>10 && $chance<=4) || ($level<=10 && $chance<=3)){
					output("'Hey `%Hara`#,' you call out, 'Looks like you got yourself a Rat Problem! I bet everyone will find this interesting.'");
					output("`n`nShe eyes you up, trying to see if you're bluffing and finally decides you're not. `%'Okay, here's `^250 gold`%... don't tell anyone.'");
					output("`n`n`#You pocket the gold and decide to keep your mouth shut.");
					$session['user']['gold']+=250;
					debuglog("gained 250 gold by researching at Hara's Bakery.");
				}else{
					output("`%Hara`# tells everyone that there's a `^50 gold`# reward if you can catch the rat.");
					if (e_rand(1,3)==1){
						output("You show your rat-catching prowess by grabbing the rat in one quick swift motion.");
						output("`n`n`%Hara`# gives you `^50 gold`# for your effort.");
						$session['user']['gold']+=50;
						debuglog("gained 50 gold by researching at Hara's Bakery.");
					}else{
						output("You decide it's not worth your effort.");
					}
				}
			break;
			case 9: case 10:
				output("You mention that you think one of the forks is dirty to `%Hara`#.  She takes it and looks at it, agreeing with you.");
				output("She storms into the kitchen and you hear a heated discussion, terminating with a young man leaving the kitchen in an angry huff.");
				output("`n`nBefore he leaves, he turns to tell `%Hara `2'Good Luck finding a better Dishwasher!'`n`n");
				$chance=e_rand(1,5);
				$level=$session['user']['level'];
				if ((($level>5 && $chance<=2) || ($level<=5 && $chance<=1)) && get_module_pref("retainer")==0){
					output("`#Realizing that you can step up to the plate, you tell `%Hara`# that you'll take the job. She looks you up and down and throws you a towel. `%'The job's yours.  Don't screw it up!'");
					output("`n`n`#You gain a retainer!");
					debuglog("gained a retainer by researching at Hara's Bakery.");
					set_module_pref("retainer",2);
					rawoutput("<small><small>");
					output("`c`^Notes on Retainers`c");
					output("Retainers are a nice cushion for those lucky enough to obtain one.`n`n");
					output("If you get one, then once a system day you may receive a small stipend. If you have a lucky day, it will be more than the standard amount. On an unlucky day, you won't get anything.  If it's a REALLY bad day, you'll lose the retainer.");
					rawoutput("<big><big>");
				}else{
					output("`#You feel a little sheepish for getting the kid fired, but decide not to get involved.");
				}
			break;
			case 11: case 12:
				output("`%Hara`# decides to show off her abilities to read tea leaves on you.`n`n`%");
				$chance=e_rand(1,5);
				$level=$session['user']['level'];
				if (($level>5 && $chance<=3) || ($level<=5 && $chance<=2)){
					output("'Things are looking up! You're `iBlessed`i!'`#reports `%Hara`#.");
					if ($session['bufflist']['blesscurse']['atkmod']==1.2) {
						$session['bufflist']['blesscurse']['rounds'] += 5;
						debuglog("increased their blessing by 5 rounds by researching at Hara's Bakery.");
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
						debuglog("received a blessing by researching at Hara's Bakery.");
					}
				}else{
					output("'Oooh... this is bad.  I'm glad I'm not you.  You're `iCursed`i!'`#reports `%Hara`#.");
					if ($session['bufflist']['blesscurse']['atkmod']==0.8) {
						$session['bufflist']['blesscurse']['rounds'] += 5;
						debuglog("increased their curse by 5 rounds by researching at Hara's Bakery.");
					}else{
						apply_buff('blesscurse',
							array("name"=>translate_inline("Cursed"),
								"rounds"=>15,
								"survivenewday"=>1,
								"wearoff"=>translate_inline("The burst of energy passes."),
								"atkmod"=>0.8,
								"defmod"=>0.9,
								"roundmsg"=>translate_inline("Dark Energy flows through you!"),
							)
						);
						debuglog("received a curse by researching at Hara's Bakery.");
					}
				}
			break;
			case 13: case 14:
				output("You're taking a bite out of an apple and notice a worm!");
				$chance=e_rand(1,5);
				$level=$session['user']['level'];
				if (($level>5 && $chance<=3) || ($level<=5 && $chance<=2)){
					output("Luckily you throw it away before you eat any of it. Disgusting!");
				}else{
					output("Ewww! It's only 1/2 a worm!");
					output("You lose `\$1/2 your hitpoints`#.  Good thing you didn't eat the whole thing!");
					$session['user']['hitpoints']*=.5;
					debuglog("lost 1/2 hitpoints by researching at Hara's Bakery.");
				}
			break;
			case 15: case 16:
				output("A shady looking character approaches you and pulls out a pitch black `1Obsidian Sword`#.  You realize that this is really a top-quality weapon.");
				if ($session['user']['weapondmg']<18 && $session['user']['gold']+$session['user']['goldinbank']>=12000 && $session['user']['weapon']!="`1Obsidian Sword`0"){
					output("You ask him to give you the details on this canon and he explains to you that it can be yours for only `^12,000 gold`# and it has an attack of `&18`#.");
					output("`n`nAre you interested?");
					addnav("Purchase the Sword","runmodule.php?module=dragoneggs&op=diner15");
				}else{
					output("`n`nHowever, there's something not right about this guy and you decide you don't want to get involved with him.");
				}
			break;
			case 17: case 18: case 19: case 20:
				output("`%Hara`# tells you that she's trying to get involved in selling big deserts.  She offers you the following feast:`&`c`n");
				if ($session['user']['gold']>=500){
					output("Four tier Vanilla and Strawberry cake with Butter Cream Frosting.");
					$cost=500;
				}elseif ($session['user']['gold']>=100){
					output("10 Cupcakes stacked on top of each other, an amazing display of balance!");
					$cost=100;
				}elseif ($session['user']['gold']>0){
					output("The biggest cookie you have ever seen");
					$cost=1;
				}else{
					output("A super huge goop of one week old frosting.");
					output("`c`n`#For some reason, that just doesn't sound appealing.  You decline.  You think that if only you had some money, perhaps she would have offered something else.");
					$cost=0;
				}
				if ($cost>0){
					output("`c`n`#All this can be yours for the low low price of only `^%s gold`#.",$cost);
					addnav("Purchase the Meal","runmodule.php?module=dragoneggs&op=diner17&op2=$cost");
				}
			break;
			case 21: case 22:
				output("You comment to `%Hara`# that the place is looking a bit dirty.  She looks at you and says `%'Well, why don't you do something about it?'`#");
				if ($session['user']['turns']>0){
					$pay=$session['user']['turns']*50;
					if ($pay>400) $pay=400;
					output("`n`nYou talk a little more with her and she tells you she's serious.  She'll give you `^%s gold`# if you spend a turn cleaning the place up.",$pay);
					addnav("Help Clean","runmodule.php?module=dragoneggs&op=diner21&op2=$pay");
				}else{
					output("You laugh and tell her you'd help out but you just don't have the time.");
				}
			break;
			case 23: case 24: case 25: case 26:
				output("`%'Isn't it your birthday?'`# Hara asks. Before you have a chance to respond, she brings out a cake.");
				output("`%'I hope you have a great day!'`# she says with a smile.");
				if (isset($session['bufflist']['cake'])) {
					$session['bufflist']['cake']['rounds'] += 5;
				}else{
					apply_buff('cake',array(
						"name"=>translate_inline("Birthday Cake"),
						"rounds"=>10,
						"wearoff"=>translate_inline("`4The cake power fades!"),
						"atkmod"=>1.2,
						"defmod"=>1.35,
					));
				}
				debuglog("got a birthday cake while researching dragon eggs at Hara's Bakery.");
			break;
			case 27: case 28:
				if ($session['user']['level']>4 && $session['user']['maxhitpoints']>$session['user']['level']*11){
					output("A fight breaks out in the diner and you realize that you can end it.  However, you see the flash of a knife and realize that you're going to lose a `\$permanent hitpoint`# if you get involved.");
					output("`n`nWill you help out?");
					addnav("Break up the Fight","runmodule.php?module=dragoneggs&op=diner27");
				}else{
					output("A fight breaks out in the diner and you decide to get out while you can.  As you're leaving, a chair hits you and knocks you unconscious.");
					output("`n`nYou `\$lose all hitpoints except 1`#.");
					$session['user']['hitpoints']=1;
					debuglog("lost all hitpoints except 1 while researching dragon eggs at Hara's Bakery.");
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
	addnav("Return to Hara's Bakery","runmodule.php?module=bakery&op=food");
	villagenav();
}
?>