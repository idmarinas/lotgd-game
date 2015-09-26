<?php
function dragoneggs_library(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	if (is_module_active("library")) $library="library";
	else{
		$library="dlibrary";
		output("`c`b`^Library`b`c`2`n");
	}
	page_header(array("%s Public Library",get_module_setting("libraryloc",$library)));
	$open=get_module_setting("libraryopen");
	if ($open==0 && $session['user']['dragonkills']<get_module_setting("librarymin") && get_module_setting("librarylodge")>0 && get_module_pref("libraryaccess")==0){
		output("`2You don't have enough `@Green Dragon Kills`2 to research here.  Once you do, you will need to purchase access through the lodge. Please check back frequently to see if you qualify. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && $session['user']['dragonkills']<get_module_setting("librarymin")+get_module_setting("mindk")){
		output("`2You don't have enough `@Green Dragon Kills`2 to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && get_module_setting("librarylodge")>0 && get_module_pref("libraryaccess")==0){
		output("`2You will need to purchase access through the Lodge to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif (get_module_pref("researches")>=get_module_setting("research")){
		output("`2You're out of research turns for today.");
	}else{
		output("`2You decide to look for Dragon Eggs at the Library.`n`n");
		$rumor36=0;
		if (is_module_active("rumors")){
			if($session['user']['dragonkills']>=get_module_setting("mindk","rumors")+get_module_setting("mindk")) $rumor36=1; 
		}
		if($rumor36==1) $case=e_rand(1,36);
		else $case=e_rand(1,35);
		increment_module_pref("researches",1);
		switch($case){
		//switch(9){
			case 1: case 2:
				output("One of the books that you look through has a bookmark still stuck in it.`n`n");
				$chance=e_rand(1,7);
				if (($session['user']['level']>7 && $chance<4) || ($session['user']['level']<=7 && $chance<3) && $session['user']['turns']>0){
					$gold=e_rand(200,500);
					output("There's an ancient parchment in it worth `^%s gold`2! You're able to sell it for the gold.",$gold);
					$session['user']['gold']+=$gold;
					debuglog("found $gold gold while researching dragon eggs at the Library.");
				}else{
					output("It's a `iHello Kitty`i bookmark.  Congratulations. You found a piece of garbage.");
				}
			break;
			case 3: case 4:
				output("One of the books in the corner starts to whisper to you.");
				output("`n`n`6'Shhhhhhhhhhhhhh!'`2 says the book.");
				if ($session['user']['turns']>0){
					output("You spend a turn looking for the annoying book but can't find it.");
					$session['user']['turns']--;
					debuglog("lost a turn while researching dragon eggs at the Library.");
				}else{
					output("You slowly back away from the shelves and go back to the village.");
					blocknav("runmodule.php?module=$library&op=enter");
				}
			break;
			case 5: case 6:
				$chance=e_rand(1,9);
				$level=$session['user']['level'];
				if (($level>10 && $chance<=4) || ($level<=10 && $chance<=3)){
					output("Between the shelves you see a strange glowing statue that catches your attention.");
					output("You pick it up and feel a strange sensation run through your body.");
					output("`n`nYou `@gain 3 turns`2 and `&2 charm`2!");
					$session['user']['turns']+=3;
					$session['user']['charm']+=2;
					debuglog("gained 3 turns and 2 charm while researching dragon eggs at the Library.");
				}else{
					output("You knock over a stack of books and they tumble to the ground in a large crash.");
					output("`n`nThe librarian comes over and starts to yell at you. Instead of putting up with the abuse, you head out of the library.");
					blocknav("runmodule.php?module=$library&op=enter");
				}
			break;
			case 7: case 8:
				output("You fall asleep reading `iMy Pet Goat`i.  Suddenly, it all becomes clear in your head.  There's a conspiracy going on!");
				output("`n`n`%Gain 1 gem`2.");
				$session['user']['gems']++;
				debuglog("gained a gem while researching dragon eggs at the Library.");
			break;
			case 9: case 10:
				$numb=e_rand(1,8);
				$array=translate_inline(array("","Necronomicon","Out of the Aeons","The Shadow Out of Time","Through the Dragon Eggs of the Silver Key","At the Mountains of Madness","The Dunwich Horror","The Whisperer in Darkness","The Haunter of the Dark"));
				output("As you look around in the stacks, the librarian notices you.  `3'You owe the library `^600 gold`3 in overdue book fines.  We have you listed as having checked out `i%s`i 10 years ago and never returning it.  Pay up!'`2 she says.`n`n",$array[$numb]);
				if ($session['user']['gold']+$session['user']['goldinbank']<600){
					if (get_module_pref("retainer")>0){
						output("You don't have enough money so the librarian takes your retainer.");
						set_module_pref("retainer",0);
						debuglog("lost a retainer in order to pay an overdue book fine while researching dragon eggs at the Library.");
					}elseif ($session['user']['gems']>1){
						output("You don't have enough money so you give the librarian `%2 gems`2 to cover your debt.");
						$session['user']['gems']-=2;
						debuglog("lost 2 gems in order to pay an overdue book fine while researching dragon eggs at the Library.");
					}elseif ($session['user']['turns']>2){
						output("You don't have enough money so you spend `@3 turns`2 cleaning up the library to cover your debt.");
						$session['user']['turns']-=3;
						debuglog("lost 3 turns in order to pay an overdue book fine while researching dragon eggs at the Library.");
					}elseif ($session['user']['experience']>500){
						$exp=max(200,round($session['user']['experience']*.08));
						output("You don't have enough money so you use some of your experience to improve the library.");
						output("You `#lose `^%s`# experience`2.",$exp);
						$session['user']['experience']-=$exp;
						debuglog("lost $exp experience in order to pay an overdue book fine while researching dragon eggs at the Library.");
					}else{
						output("Deciding that you don't want to pay the fine you leave the library.");
						blocknav("runmodule.php?module=$library&op=enter");
					}
				}else{
					if ($session['user']['gold']<600){
						output("Not having enough money on hand, the librarian prepares a bank withdrawal slip from the front desk.");
						if ($session['user']['gold']<1){
							output("`n`nYou fill in `^600 gold`2 on the slip and hand it to the librarian.");
							$session['user']['goldinbank']-=600;
							debuglog("paid 600 gold from the bank to pay for overdue book fees at the Library.");
						}else{
							$inbank=600-$session['user']['gold'];
							output("`n`nYou fill in `^%s gold`2 on the slip and hand over the rest of the money from your gold on hand to the librarian.",$inbank);
							$session['user']['goldinbank']-=$inbank;
							$session['user']['gold']=0;
							debuglog("paid 600 gold from the bank and money on hand to pay for overdue book fees at the Library.");
						}
					}else{
						output("You hand over the `^600 gold`2 and the librarian gives you a receipt. `3'Thank you,'`2 she says with a smile.");
						$session['user']['gold']-=600;
						debuglog("paid 600 gold to pay for overdue book fees at the Library.");
					}
				}
			break;
			case 11: case 12:
				$chance=0;
				if (e_rand(1,3)==1) $chance++;
				$rand=e_rand(1,9);
				$level=$session['user']['level'];
				if (($level>10 && $rand<=2) || ($level<=10 && $rand<=1)) $chance++;
				if ($chance==0){
					output("You accidentally spill a drink on one of the books you're reading.");
					output("Rather than risk getting in trouble you decide to leave the library.");
					blocknav("runmodule.php?module=$library&op=enter");
				}elseif ($chance==1){
					output("You are escorted to the private collection of the library and find a spell book.");
					output("`n`nBy reading it, you improve in your specialty!`n");
					require_once("lib/increment_specialty.php");
					increment_specialty("`Q");
					debuglog("improved specialty while researching dragon eggs at the Library.");
				}else{
					output("You find an amazing Self-Help book. You read it cover to cover and find your life greatly improved!");
					output("`n`nYou `&gain 1 charm`2, `&gain 1 attack`2,`& gain 1 defense`2, and `%gain 1 gem`2!");
					$session['user']['charm']++;
					$session['user']['attack']++;
					$session['user']['defense']++;
					$session['user']['gems']++;
					debuglog("gained 1 charm, 1 attack, 1 defense, and 1 gem while researching dragon eggs at the Library.");
				}
			break;
			case 13: case 14:
				output("There are strange symbols written in the margin of one of the books!");
				$chance=e_rand(1,7);
				if (($session['user']['level']>7 && $chance<3) || ($session['user']['level']<=7 && $chance<2)){
					output("It's a spell! You read it out loud and suddenly find yourself able to focus more.`n");
					require_once("lib/increment_specialty.php");
					increment_specialty("`1");
					debuglog("incremented specialty while researching dragon eggs at the Library.");
				}else{
					output("You don't know how to read strange symbols.  Oh well!");
				}
			break;
			case 15: case 16:
				$previous= strpos($session['user']['armor'],"Cool Jacket")!==false ? 1 : 0;
				if ($previous==0){//Never had this
					output("`@'Check out this Cool Jacket,'`2 says the janitor. `@'It can be yours for only `^800 gold`@.'");
					output("`n`n`2He lets you examine it and you realize it's probably at least 2 points better than your %s`2.",$session['user']['armor']);
					if ($session['user']['gold']>=800) addnav("Buy the Cool Jacket","runmodule.php?module=dragoneggs&op=library15");
					else{
						if ($session['user']['gold']==0) $gold="this piece of lint";
						else $gold=$session['user']['gold']." gold";
						output("`n`n`#'Well, I don't have that much.  Would you take `^%s`# for it?'`2 you offer.",$gold);
						output("`n`n`@'Hey, this ain't no charity auction. Buzz off,'`2 he replies.");
					}
				}else{
					output("You find a `%gem`2 in one of the books.");
					$session['user']['gems']++;
					debuglog("gained a gem while researching dragon eggs at the Library.");
				}
			break;
			case 17: case 18:
				output("`#'I can't find anything useful,'`2 you complain to the librarian.`n`n");
				output("`5'What kind of book are you looking for?'`2 she asks.");
				addnav("Books");
				addnav("Become More Charming","runmodule.php?module=dragoneggs&op=library17&op2=3");
				addnav("Money Making Schemes","runmodule.php?module=dragoneggs&op=library17&op2=4");
				addnav("Gemology","runmodule.php?module=dragoneggs&op=library17&op2=5");
				addnav("Destroying Dragon Eggs","runmodule.php?module=dragoneggs&op=library17&op2=9");
				addnav("Leave");
			break;
			case 19: case 20:
				output("You search through the stacks trying to find a book on `@Green Dragons`2.");
				output("A Dragon Sympathist sees you doing your research and picks a fight with you.");
				blocknav("runmodule.php?module=$library&op=enter");
				blocknav("village.php");
				addnav("Fight the Dragon Sympathist","runmodule.php?module=dragoneggs&op=attack");
				set_module_pref("monster",16);
			break;
			case 21: case 22:
				output("You find a book that radiates `\$Evil`2.");
				output("`n`nNow, since you're an adventurer at heart, of course you're going to read it!");
				output("`n`nYou settle down and dive deep into the book.`n`n");
				$chance=e_rand(1,20);
				$level=$session['user']['level'];
				if (($level>10 && $chance<=3) || ($level<=10 && $chance<=2)){
					output("This is GREAT stuff!! It's a spell to create gems!  You cast it and...`n`n");
					$gems=e_rand(1,6);
					if ($gems>3 && e_rand(1,3)<3) $gems=e_rand(1,2);
					output("You `%create %s %s`2.",$gems,translate_inline($gems>1?"gems":"gem"));
					debuglog("gained $gems gems while researching dragon eggs at the library.");
				}else{
					output("Oh, maybe this wasn't the best idea. The `\$evil corrupts you`2! You feel weak.");
					output("`n`nYou lose `\$all hitpoints except 1`2 and you're `iCursed`i.");
					$session['user']['hitpoints']=1;
					if ($session['bufflist']['blesscurse']['atkmod']==0.8) {
						$session['bufflist']['blesscurse']['rounds'] += 5;
						debuglog("increased their curse by 5 rounds and lost all hitpoints except 1 while researching dragon eggs at the library.");
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
						debuglog("was cursed and lost all hitpoints except 1 while researching dragon eggs at the library.");
					}
				}
			break;
			case 23: case 24:
				if (get_module_pref("book")==0){
					$chance=e_rand(1,9);
					$level=$session['user']['level'];
					if (($level>10 && $chance<=4) || ($level<=10 && $chance<=3)){
						output("You find a really nice book without any markings on it indicating that it's a library book.");
						output("`n`nYou go to the librarian and ask her about the book and she dismisses you.");
						output("`n`n`5'All our books are marked properly.  It's not one of ours. You can keep it,'`2 she says.");
						output("`n`nCool! You get to keep the book! Maybe you can sell it to a different librarian some day.");
						set_module_pref("book",1);
						debuglog("found a book while researching dragon eggs at the library.");
					}else{
						output("You look around for a book that might be fun to read.");
						output("`n`nNope.  Nothing worth reading here.");
					}
				}else{
					output("You see that the library is collecting books today.");
					output("`n`n`3'Do you have any books you'd like to sell to the library?'`2 asks the librarian.");
					output("`n`n`#'Why, yes I do!'`2 you say.");
					output("`n`nYou negotiate a reasonable price for the book that you found in the library that one time.");
					$gold=e_rand(500,1000);
					output("`n`n`3'It's agreed.  We'll take the book and pay you `^%s gold`3.  Thank you!' `2she says.",$gold);
					$session['user']['gold']+=$gold;
					debuglog("gained $gold gold for selling a book while researching dragon eggs at the library.");
					set_module_pref("book",0);
				}
			break;
			case 25: case 26:
				output("You start snooping around and look over the shoulders of people reading books.");
				output("`n`nSuddenly, you see a very strange man reading a peculiar looking book. This looks good!");
				output("`n`nYou start to read over his shoulder");
				$chance=e_rand(1,9);
				$level=$session['user']['level'];
				if (($level>10 && $chance<=3) || ($level<=10 && $chance<=2)){
					output("and you're able to read the spell before he catches you.");
					output("`n`nYou improve your specialty!`n");
					require_once("lib/increment_specialty.php");
					increment_specialty("`#");
					debuglog("improved specialty while researching dragon eggs at the library.");
				}else{
					output("but he notices you and closes the book before you can figure out what he was reading.");
					output("`n`nA bit embarrassed, you leave the library.");
					blocknav("runmodule.php?module=$library&op=enter");
				}
			break;
			case 27: case 28:
				output("You find a book that has some great information about `@Green Dragons`2.`n`n");
				if ($session['user']['turns']>0){
					output("You `@spend a turn`2 reading the book.  The eye of the dragon is actually a gem... so you pry it out! You `%gain a gem`2.");
					$session['user']['gems']++;
					$session['user']['turns']--;
					debuglog("gained a gem but lost a turn while researching dragon eggs at the library.");
				}else{
					output("Too bad you don't have any turns to read it.");
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
	addnav("Return to the Library","runmodule.php?module=$library&op=enter");
	villagenav();
}
?>