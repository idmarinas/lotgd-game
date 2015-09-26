<?php
function dragoneggs_news(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("LoGD News");
	output("`n`c`b`!Daily News`b`c`2");
	output_notl("`c`2-=-`@=-=`2-=-`@=-=`2-=-`@=-=`2-=-`0`c");
	$open=get_module_setting("newsopen");
	if ($open==0 && $session['user']['dragonkills']<get_module_setting("newsmin") && get_module_setting("newslodge")>0 && get_module_pref("newsaccess")==0){
		output("You don't have enough `@Green Dragon Kills`2 to research here.  Once you do, you will need to purchase access through the lodge. Please check back frequently to see if you qualify. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && $session['user']['dragonkills']<get_module_setting("newsmin")+get_module_setting("mindk")){
		output("You don't have enough `@Green Dragon Kills`2 to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && get_module_setting("newslodge")>0 && get_module_pref("newsaccess")==0){
		output("You will need to purchase access through the Lodge to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif (get_module_pref("researches")>=get_module_setting("research")){
		output("`2You're out of research turns for today.");
	}else{
		output("`2You decide to look for Dragon Eggs at the Daily News.`n`n");
		$rumor36=0;
		if (is_module_active("rumors")){
			if($session['user']['dragonkills']>=get_module_setting("mindk","rumors")+get_module_setting("mindk")) $rumor36=1; 
		}
		if($rumor36==1) $case=e_rand(1,36);
		else $case=e_rand(1,35);
		increment_module_pref("researches",1);
		//switch($case){
		switch(27){
			case 1: case 2:
				output("While walking through the halls of the Daily News, you get a sense of awe at the wonders that the free press can accomplish.");
				output("`n`nYou stare at the plaques on the walls demonstrating the honors that the paper has achieved.");
				output("`\$'HEY! Watch where you're going,'`2 says one of the reporters as you bump into him.");
				output("`n`nHe drops the box of quills he was carrying and it lands square on your foot.`n`n");
				if ($session['user']['hitpoints']>1){
					output("You shriek in pain and `\$lose half of your hitpoints`2.");
					$session['user']['hitpoints']=round($session['user']['hitpoints']*.5);
					debuglog("lost 1/2 hitpoints while researching dragon eggs at the Daily News.");
				}else{
					output("Luckily your shoes provide sufficient protection to prevent any serious injury.");
				}
			break;
			case 3: case 4:
				output("Maybe you can find something useful by reading old newspapers.`n`n");
				$level=$session['user']['level'];
				$chance=e_rand(1,5);
				if (($level>8 && $chance<=3) || ($level<=8 && $chance<=2)){
					output("Excellent! Here's a story that sheds some light on some of the strange events happening in the kingdom.");
					output("`n`nIt's a 'gem' of a story! You `%gain a gem`2!");
					$session['user']['gems']++;
					debuglog("gained a gem while researching dragon eggs at the Daily News.");
				}else{
					output("Nope.  You find yourself reading the comics.  That Marmaduke is hilarious!");
					output("`n`nYour jovial nature makes you more attractive though. You `&gain 1 charm`2!");
					$session['user']['charm']++;
					debuglog("gained a charm while researching dragon eggs at the Daily News.");
				}
			break;
			case 5: case 6:
				output("`#'Watch out.  I think there's something wrong with that printing press,' `2you tell a worker swiping it down with a large rag.");
				output("`n`nHe disregards your advice and his arm gets caught! Eww!");
				output("`n`nYou decide to leave.");
				blocknav("news.php");
			break;
			case 7: case 8:
				if ($session['user']['level']>5){
					$egold=0;
					$egem=0;
					output("You stop by at the editor's office and ask if he has anything to help you destroy Dragon Eggs.");
					output("`4'Well kid, I tell you what.  I can sell you some `%gems`4 or I can buy them from you.  Your choice,'`2 he offers.");
					if ($session['user']['gold']>=500){
						$egold=1;
						addnav("Give Gold for Gems");
						addnav("500 gold for 1 gem","runmodule.php?module=dragoneggs&op=news7&op2=1");
						if ($session['user']['gold']>=900) addnav("900 gold for 2 gems","runmodule.php?module=dragoneggs&op=news7&op2=2");
						if ($session['user']['gold']>=1200) addnav("1200 gold for 3 gems","runmodule.php?module=dragoneggs&op=news7&op2=3");
					}
					if ($session['user']['gems']>0){
						$egem=1;
						addnav("Give Gems for Gold");
						addnav("1 gem for 300 gold","runmodule.php?module=dragoneggs&op=news7&op2=4");
						if ($session['user']['gems']>=2) addnav("2 gems for 700 gold","runmodule.php?module=dragoneggs&op=news7&op2=5");
						if ($session['user']['gems']>=3) addnav("3 gems for 1200 gold","runmodule.php?module=dragoneggs&op=news7&op2=6");
					}
					if ($egold==0 && $egem==0) output("`n`n`4'Of course, if you don't have any gems or gold, I can't make an exchange.  Come back when you've got something I need,'`2 the Editor says.");
					else addnav("Leave");
				}else{
					output("You do some amazing research on forest creatures.");
					$gain=e_rand(15,30)*$session['user']['level'];
					$session['user']['experience']+=$gain;
					output("`n`nYou gain `^%s`# experience`2.",$gain);
					debuglog("gained $gain experience while researching dragon eggs at the Daily News.");
				}
			break;
			case 9: case 10:
				output("Wandering from reporter to reporter, you look for anything interesting.");
				output("`n`nOne of the reporters has an ink blot on the wall.  You take a look at it and see...");
				output("`n`nA terrible vision of the future with death, destruction, sadness, despair... it's just dreadful!");
				if ($session['user']['turns']>2){
					if (isset($session['bufflist']['inkblot'])) {
						$session['bufflist']['inkblot']['rounds'] += 10;
					}else{
						apply_buff('inkblot',array(
							"name"=>translate_inline("Ink Blot Horror"),
							"rounds"=>20,
							"wearoff"=>translate_inline("`2The vision clears from your mind."),
							"defmod"=>.9,
						));
					}
					debuglog("got the Ink Blot Buff while researching dragon eggs at the Daily News.");
				}else{
					output("You won't get that vision out of your mind eyes before the next day comes...");
					if (isset($session['bufflist']['blurredvision'])) {
						$session['bufflist']['blurredvision']['rounds'] += 13;
					}else{
						apply_buff('blurredvision',array(
							"name"=>translate_inline("Blurred Vision"),
							"rounds"=>25,
							"wearoff"=>translate_inline("`2Your eyes clear and your focus returns."),
							"defmod"=>.9,
							"survivenewday"=>1,
						));
					}
					debuglog("got the Ink Blot Buff with survivenewday while researching dragon eggs at the Daily News.");
				}
			break;
			case 11: case 12:
				output("It's the early edition... the VERY early edition!");
				output("You grab it an run out of the newspaper and get a chance to peruse the news for tomorrow.`n`n");
				$level=$session['user']['level'];
				$chance=e_rand(1,5);
				if (($level>8 && $chance<=2) || ($level<=8 && $chance<=1)){
					output("You read of stolen gems turning up in a dumpster.  You go dive into the dumpster and find `%a gem`2! Look! It even made the news!");
					$session['user']['gems']++;
					output("`n`nBy the way, you smell.  You `&lose 1 charm`2.");
					$session['user']['charm']--;
					debuglog("gained a gem and lost 1 charm while researching dragon eggs at the Daily News.");
					addnews("%s `^found `%a gem`^ while dumpster diving.  <Sniff Sniff> someone smells!",$session['user']['name']);
				}else{
					output("Nope.  Nothing worthwhile in it.");
				}
				blocknav("news.php");
			break;
			case 13: case 14: case 15: case 16:
				output("You give the lead reporter a juicy scoop. He hands you `^150 gold`2!");
				$session['user']['gold']+=150;
				debuglog("gained 150 gold for a great story while researching dragon eggs at the Daily News.");
			break;
			case 17: case 18: case 19: case 20:
				$travel=1;
				if (e_rand(1,2)==1){
					$level=$session['user']['level'];
					$chance=e_rand(1,5);
					if (($level>8 && $chance<=3) || ($level<=8 && $chance<=2)){
						output("You have a quick conversation with one of the paper delivery men and he offers to take you anywhere you'd like to research that's available today.");
						output("`n`nBecause of the simple nature of the conversation, you don't even use up a research turn.");
					}else{
						$travel=0;
						output("You get into an argument with one of the delivery men. `4'I was going to take you anywhere you wanted to go,'`2 he says in a huff before turning and leaving.");
						output("`n`nYou feel like you've missed an opportunity here.");
					}
				}else{
					output("You talk to the Editor about where you'd like to go to research further.");
					output("You offer more information about some good leads and he's quite impressed.`n`n");
					output("`4'I'll take you there if you'd like,'`2 he offers.  In addition, he gives you `^200 gold`2 for the information.");
					$session['user']['gold']+=200;
					debuglog("gained 200 gold while researching dragon eggs at the Daily News.");
				}
				if ($travel==1){
					increment_module_pref("researches",1);
					blocknav("runmodule.php?module=dragoneggs&op=news&op3=nav");
					addnav("Travel");
					dragoneggs_navs();
					addnav("Leave");
					debuglog("received a free research to research somewhere else while researching dragon eggs at the Daily News.");
				}
			break;
			case 21: case 22:
				if (get_module_setting("open")>0){
					if (get_module_setting("left")<=0){
						if (e_rand(1,3)==1){
							output("You end up talking with one of the newspaper reporters.`n`n");
							output("`6'I was thinking of opening a shop to purchase `%gems`6 in exchange for money.  I can use the gems to start my own paper. The only problem is that I'm not sure there's really a market for another paper right now,'`2 he says.");
							output("`n`nYou could probably convince him that there's enough news out there by telling him about the Dragon Egg Problem. He'll need some proof though... You'll have to give him a `&Dragon Egg Point`2.`n`n");
							if (get_module_pref("dragoneggs","dragoneggpoints")>0) addnav("Give a Dragon Egg Point","runmodule.php?module=dragoneggs&op=news21");
							else output("Unfortunately, you don't have any dragon egg points.  I guess it's a dream that will go unfulfilled for now.");
						}else{
							output("You don't find anything of value.");
						}
					}else{
						if (e_rand(1,5)==1){
							increment_module_setting("left",-1);
							debuglog("caused the Exchange Store days open to decrease by one day while researching dragon eggs at the Daily News.");
						}
						output("You don't find anything of value.");
					}
				}else{
					output("You talk to one of the reporters and he gives you a useless rock. Ha! It turns out it's actually a gem. You `%gain a gem`2!");
					$session['user']['gems']++;
					debuglog("gained a gem while researching dragon eggs at the Daily News.");
				}
			break;
			case 23: case 24: case 25: case 26:
				output("You have a meeting with the Editor-in-chief.`n`n`4");
				if (get_module_pref("retainer")==0 && e_rand(1,3)==1){
					output("'I tell you what.  I'll put you on retainer.  You find a good story and let me know about it. You don't have to do anything else,'`2 he explains.");
					output("`n`nThere's no downside to a deal like that so you accept.`n`n");
					set_module_pref("retainer",2);
					rawoutput("<small><small>");
					output("`c`^Notes on Retainers`c");
					output("Retainers are a nice cushion for those lucky enough to obtain one.`n`n");
					output("If you get one, then once a system day you may receive a small stipend. If you have a lucky day, it will be more than the standard amount. On an unlucky day, you won't get anything.  If it's a REALLY bad day, you'll lose the retainer.");
					rawoutput("<big><big>");
				}elseif (get_module_pref("retainer")==0){
					output("'If you want to work at the paper, you got to prove you know your stuff. Come back later'`2 he says.");
				}else{
					output("'You've already got a retainer, kid.  Now go get some good stories!'`2 he says as he ushers you out the door.");
				}
			break;
			case 27: case 28:
				output("One of the Newspaper Boys approaches you.");
				output("`n`n`@'I want my `^2 gold pieces`@!!'`2 he yells.`n`n");
				if ($session['user']['gold']>1){
					output("You tell him that you don't want to give him any of your gold.");
				}else{
					output("You explain that you don't have `^2 gold pieces`2.");
				}
				output("`n`nHe doesn't like your answer. It's fighting time!");
				set_module_pref("monster",20);
				addnav("Fight the Newsboy","runmodule.php?module=dragoneggs&op=attack");
				blocknav("village.php");
				blocknav("news.php");
			break;
			case 29: case 30: case 31: case 32: case 33: case 34: case 35:
				output("You don't find anything of value.");
			break;
			case 36:
				dragoneggs_case36();
			break;
		}
	}
	addnav("Return to the Daily News","news.php");
	villagenav();
}
?>