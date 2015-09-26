<?php
function docks_fishmisc($op2){
	global $session;
	$op = httpget('op');
	$op3 = httpget('op3');
	$temp=get_module_pref("pqtemp");
	page_header("Fishing Expedition");
	if ($op2=='gauge'){
		if ($op=="fishingexpedition"){
			addnav("Continue","runmodule.php?module=docks&op=fishingexpedition&loc=".$temp);
			if (get_module_pref("fishbook")==1) addnav("Read Your Fishing Book","runmodule.php?module=docks&op=docks&op2=readfishbook&op3=expedition&op4=$op3");
		}else{
			output("`nThe readings at location `^%s`7 are:`n",$op3);
			if (get_module_pref("fishbook")==1){
				addnav("Research");
				addnav("Read Your Fishing Book","runmodule.php?module=docks&op=docks&op2=readfishbook&op3=expeditiona&op4=$op3");
			}
		}
		output("`n`#Temperature: `7%s Degrees`n",get_module_pref("temp".$op3));
		output("`%Depth: `7%s Feet`n",get_module_pref("depth".$op3));
		output("`@Wind: `7%s Knots`n",get_module_pref("wind".$op3));
	}
	if ($op2=="gold"){
		if ($op3=="1"){
			output("`nYou pocket the `^220 gold`7 and think you're so slick.");
			output("One of the crew notices your theft and you 'accidentally' get hit in the head with a club.");
			output("`n`nYou gain `^200 gold`7 but you lose all your hitpoints except one.");
			$session['user']['gold']+=200;
			$session['user']['hitpoints']=1;
			if (is_module_active("alignment")){
				output("`n`nYou are a bit more `\$evil`7 because of this!");
				$change=-2;
			}
		}elseif ($op3=="2"){
			output("`nYou have better things to do than steal gold from the crew.  It's not your concern.");
			$chance=e_rand(1,2);
			if ($chance==1){
				output("You look down and notice a `%gem`7 lodged in a plank.  You pry it out and pocket it instead of worrying about the sack of gold.`n`n");
				$session['user']['gems']++;
			}
			if (is_module_active("alignment")){
				$alignment=get_module_pref("alignment","alignment");
				$evil=get_module_setting("minimum","alignment");
				$good=get_module_setting("maximum","alignment");
				$neutral=($good-$evil/2) + $evil;
				if ($alignment<$neutral) $change=1;
				else $change=-1;
				output("Your actions make you more `^neutral`7.");
			}
		}else{
			output("`nYou mention the bag of gold to the captain and he smiles at your honesty.");
			output("`n`n`&'I put it there to test you, and you've passed the test,'`7 he says.");
			output("He hands you `^100 gold`7 as a reward for your honesty.");
			$session['user']['gold']+=100;
			if (is_module_active("alignment")){
				$change=2;
				output("Your actions make you a `@better`7 person.");
			}
		}
		if (is_module_active("alignment")) increment_module_pref("alignment",$change,"alignment");
		docks_expeditionnav();
	}
	if ($op2=="walkaway"){
		output("`nYou decide to walk away, despite the jeers, laughter, mockery, name calling, and teasing.");
		output("`n`nMy oh my you are really a whimp.  You lose `&5 charm`7 due to whimpery.");
		$session['user']['charm']-=5;
		docks_expeditionnav();
	}
	if ($op2=="damagepay"){
		$item=translate_inline(array("","`&3 squares of wood","a `%gem","`^300 gold","`@2 turns"));
		if ($op3=="1"){
			$allprefsl=unserialize(get_module_pref('allprefs','lumberyard'));
			$allprefsl['squares']-=3;
			set_module_pref('allprefs',serialize($allprefsl),'lumberyard');
		}elseif ($op3=="2") $session['user']['gems']--;
		elseif ($op3=="3") $session['user']['gold']-=300;
		else $session['user']['turns']-=2;
		output("`nYou spend %s`7 to settle your business with the captain and get back to fishing.",$item[$op3]);
		docks_expeditionnav();
	}
	if ($op2=="bodysearch"){
		if ($op3==2){
			if (is_module_active("alignment")){
				output("Searching a body is an `\$evil`7 act and your alignment suffers because of this.");
				increment_module_pref("alignment",-1,"alignment");
			}
			output("You search the body and find nothing of value except a small pouch of gold.  You count out `^180 gold pieces`7 and slip them into your pocket before calling over the captain.");
			output("`n`nThe captain looks over the dead body and orders his crew to take the body to the hold so it can be dropped off at the next port.");
			$session['user']['gold']+=180;
		}else{
			$gemrand=e_rand(1,4);
			output("You call the captain over to help decide what to do.");
			if ($gemrand==1){
				output("He looks over the body and states how he must have been a sailor that got washed overboard in a storm.");
				output("He tells you of the rules of the sea and that the rescuer of the body of a sailor who dies at sea may keep the earring of the deceased to help guaranty that their body will be returned for burial.");
				output("`n`nThe crew gather around and hold a short ceremony for the deceased.  The ceremony culminates with the captain removing the gem from the body and handing it to you.");
				output("`n`nYou take the `%gem`7 and wish the departed a safe journey to the afterlife.");
				$session['user']['gems']++;
			}else{
				output("He tells you that it looks like it's the body of one of the slave traders.  He probably died in the storm.");
				output("`n`nNot being a fan of slave trading, the captain has the crew store the body in the hold until he can dispose of it at the next port.");
			}
		}
		docks_expeditionnav();
	}
	if ($op2=="fish"){
		$tem=get_module_pref("temp".$op3);
		$dep=get_module_pref("depth".$op3);
		$win=get_module_pref("wind".$op3);
		if (($tem<70 && $dep<75) || ($tem>=70 && $tem<80 && $dep>=100) || ($tem>=80 && $dep>=75 && $dep<100)) $table2=1; //A
		elseif (($tem>=70 && $tem<80 && $dep<75) || ($tem<70 && $dep>=75 && $dep<100) || ($tem>=80 && $dep>=100)) $table2=2; //B
		else $table2=3; //C
		if (($table2==1 && $win<=10) || ($table2==2 && $win>=11 && $win<21) || ($table2==3 && $win>=21)) $fishing=3; //Excellent
		elseif (($table2==3 && $win<=10) || ($table2==1 && $win>=11 && $win<21) || ($table2==2 && $win>=21)) $fishing=2; //Fair
		else $fishing=1; //Poor
		if ($fishing==1){
			$low=1;
			$high=30;
		}elseif ($fishing==2){
			$low=6;
			$high=30;
		}else{
			$low=6;
			$high=35;
		}
		set_module_pref("quality",$op3);
		increment_module_pref("fishingtoday",1);
		$weight=0;
		output("`nYou decide to cast your line in this location.");
		switch(e_rand($low,$high)){
		//switch(30){
			case 1:
				output("You reel in a very nice and shiny boot. Except it's a boot.  And it's water logged.");
			break;
			case 2:
				output("You catch a really nice piece of kelp.  Yumm.  Kelp.  Useless kelp.");
			break;
			case 3: case 4:
				output("You reel it in and find you've caught a fish! It's the smallest fish you've ever seen! Isn't that special??");
				$weight=e_rand(2,6);			
			break;
			case 5:
				output("You feel a tug and realize you've got a huge fish on the line!`n`n");
				$losebait=e_rand(1,2);
				if ($losebait==2){
					output("You tug and tug and suddenly trip over your bait.  You watch helplessly as it rolls into the sea. Looks like you're done fishing for today.");
					set_module_pref("bait",0);
					set_module_pref("fishingtoday",5);
				}else{
					output("You're about to reel it in when the hook falls out.  No catch.");
				}
			break;
			case 6: case 7:
				output("You reel it in and pull in quite a very dinky fish, barely worth your bait.");
				$weight=e_rand(10,32);
			break;
			case 8: case 9:
				output("You reel it in and pull in quite a dinky fish, barely worth your bait.");
				$weight=e_rand(33,60);
			break;
			case 10: case 11:
				output("You reel it in and pull in quite a fish; not a bad size if you may say so yourself.");
				$weight=e_rand(61,100);
			break;
			case 12: 
				output("You reel it in and pull in a decent fish; nicely done! This is bigger than anything that can be caught at the dock, that's for sure! You gain a turn from your adrenaline!");
				$weight=e_rand(101,200);
				$session['user']['turns']++;
			break;
			case 14:
				output("You reel it in and pull in a very decent fish; good job!");
				$weight=e_rand(201,300);
			break;
			case 16:
				$breakrod=e_rand(1,3);
				output("You strain against a HUGE fish! Everyone on the ship comes to watch you.");
				if ($breakrod==1){
					output("Suddenly your fishing pole snaps! Oh no! Well, you're done fishing for today, that's for sure.");
					set_module_pref("pole",0);
					set_module_pref("fishingtoday",5);
				}else{
					output("Suddenly, your line breaks and the fish gets away.");
				}
			break;
			case 17:
				output("You start hauling in your catch and it's huge... in fact, it's bigger than any fish you've ever seen.");
				output("`n`nYou call over the crew to help you bring it in when the captain runs over and cuts your line.");
				output("`n`nIn anger, you face the captain ready to fight. `#'What were you thinking????'`7 you yell.");
				output("`n`nThrough the laughter of the crew you finally hear the captain explain that you weren't hauling in a fish.");
				output("`n`n`&'That, my young angler, wasn't a fish.  That was a WHALE!!!'");
			break;
			case 18: case 15: case 13:
				output("You reel it in and pull in a very decent fish; good job!");
				$weight=e_rand(201,300);
			break;
			case 19:
				output("You reel in your line and notice your nightcrawler fell off. Somehow, you accidentally get the hook caught in your hand!");
				if ($session['user']['hitpoints']>10){
					output("You lose `\$10`7 hitpoints.");
					$session['user']['hitpoints']-=10;
				}else{
					output("You lose all your hitpoints except one.");
					$session['user']['hitpoints']=1;
				}
			break;
			case 20:
				output("You reel in your line and find a genie bottle attached to it!");
				output("You rub the bottle and out pops a genie!!");
				output("`n`n`%'Hey, thanks for letting me out.  Now, I know you're probably expecting a wish or something, but to be honest with you I really don't have that much power.");
				output("At best, I can offer you three bags of gold.  Each bag has `^100 gold`%.  Enjoy!'");
				output("`n`n`7The genie tosses you 3 bags of gold and floats away.");
				$session['user']['gold']+=300;
			break;
			case 21:
				output("You have a huge fish on the line! You pull and struggle with it");
				$charmrand=e_rand(1,5);
				if ($charmrand<3){
					output("but you get pulled into the water and the crew has to rescue you.");
					output("`n`nYou lose `&3 charm`7 due to embarrassment.");
					$session['user']['charm']-=3;
				}else{
					output("but it gets away.  Several of the crew saw how big the fish was though and tells you how they admire your technique.");
					output("`n`nYou gain `&3 charm`7.");
					$session['user']['charm']+=3;
				}
			break;
			case 22:
				output("Suddenly you notice that one of the crew members dropped their gold.  You count 220 pieces.");
				output("`n`nWill you keep it?");
				addnav("`\$Keep Gold","runmodule.php?module=docks&op=$op&op2=gold&op3=1");
				addnav("`^Ignore Gold","runmodule.php?module=docks&op=$op&op2=gold&op3=2");
				addnav("`@Return Gold","runmodule.php?module=docks&op=$op&op2=gold&op3=3");
				blocknav("runmodule.php?module=docks&op=$op&op2=fish&op3=$op3");
				blocknav("runmodule.php?module=docks&op=$op&op2=gauge&op3=$op3");
				blocknav("runmodule.php?module=docks&op=$op&loc=".$temp);
			break;
			case 23:
				$fight=e_rand(1,3);
				if ($fight==1 && $session['user']['turns']>0){
					output("Before you can reel in your catch, you're approached by one of the crew.  He looks like one of the biggest men you've ever seen in your life.");
					output("`n`n`2'I challenge you to a fight.  If you win, I'll give you `^200 gold`2.  If I win, you have to swob the deck. Are you interested?'");
					output("`n`n`7Several of the crew surround the two of you to form a fighting ring.  Looks like the pressure's on!");
					addnav("Fight Crewman","runmodule.php?module=docks&op=fishcrew");
					addnav("Walk Away","runmodule.php?module=docks&op=$op&op2=walkaway&op3=$op3");
					blocknav("runmodule.php?module=docks&op=$op&op2=fish&op3=$op3");
					blocknav("runmodule.php?module=docks&op=$op&op2=gauge&op3=$op3");
					blocknav("runmodule.php?module=docks&op=$op&loc=".$temp);
				}else{
					 if($session['user']['turns']==0){
						output("You notice one of the crew look you over but then he keeps walking.  If you had some extra turns, he probably would have challenged you to a duel. Oh well, you missed a chance for a fun experience!");
					 }else{
						output("One of the crewmen brushes by you briskly.  Before you have a chance to react he disappears.  You have a feeling that he was trying to size you up.");
					 }
				}
			break;
			case 24:
				output("You find that a shark has taken your bait. In order to reel this in you'll have to 'fight' the shark! Otherwise, you can just abandon the effort and cast another line.");
				addnav("Shark Fishing");
				addnav("Reel in the Shark","runmodule.php?module=docks&op=fishshark");				
			break;
			case 25:
				output("You reel in a duck! Somehow you caught a duck. Nobody can explain it. You unhook the duck and go back to fishing for fish.");
			break;
			case 26:
				$rand=e_rand(1,2);
				output("You reel it in and pull in a very decent fish; good job! You gain a turn from excitement!");
				if ($rand==1){
					output("`n`nAs you gather the fish though, it kicks your pocket and you lose your fishing book!!");
					set_module_pref("fishbook",0);
				}
				$weight=e_rand(201,300);
				$session['user']['turns']++;
			break;
			case 27:
				$number=0;
				$type=0;
				output("You take a step back to pull in your catch and your foot goes through a plank on the deck.  The captain comes over to examine the damage.");
				output("`n`n`&'Well, that's not going to be cheap to fix.");
				if (is_module_active("lumberyard")){
					$allprefsl=unserialize(get_module_pref('allprefs','lumberyard'));
					if ($allprefsl['squares']>=3){
						$type=1;
						output("You can give me `^3`& of your squares of wood from the lumberyard and I can have some of my crew fix it.");
						$number++;
						addnav("Pay Wood","runmodule.php?module=docks&op=$op&op2=damagepay&op3=1");
					}
				}
				if ($session['user']['gems']>=1){
					$type=2;
					output("You can give me a `%gem`& and that should square us.");
					$number++;
					addnav("Pay a Gem","runmodule.php?module=docks&op=$op&op2=damagepay&op3=2");
				}
				if ($session['user']['gold']>=300){
					$type=3;
					output("You can give me `^300 gold`& to help pay for repair costs.");
					$number++;
					addnav("Pay Gold","runmodule.php?module=docks&op=$op&op2=damagepay&op3=3");
				}
				if ($session['user']['turns']>=2){
					$type=4;
					output("I think you could spend `@2 turns`& fixing it and that would be helpful.");
					$number++;
					addnav("Spend Turns","runmodule.php?module=docks&op=$op&op2=damagepay&op3=4");
				}
				output("So what do you want to do?'`7`n`n");
				if ($number==1){
					$item=translate_inline(array("","`&3 squares of wood","a `%gem","`^300 gold","`@2 turns"));
					if ($type==1){
						$allprefsl['squares']-=3;
						set_module_pref('allprefs',serialize($allprefsl),'lumberyard');
					}elseif ($type==2) $session['user']['gems']--;
					elseif($type==3) $session['user']['gold']-=300;
					else $session['user']['turns']-=2;
					blocknav("runmodule.php?module=docks&op=$op&op2=damagepay&op3=$type");
					output("You spend %s`7 to settle your business with the captain and get back to fishing.",$item[$type]);
				}elseif ($number>1){
					blocknav("runmodule.php?module=docks&op=$op&op2=fish&op3=$op3");
					blocknav("runmodule.php?module=docks&op=$op&op2=gauge&op3=$op3");
					blocknav("runmodule.php?module=docks&op=$op&loc=".$temp);
				}else{
					output("Not having any gold, gems, time, or anything else of value, you offer your fishing pole.");
					output("The captain accepts it as payment.  It looks like you're done fishing for the day.");
					set_module_pref("pole",0);
					set_module_pref("fishingtoday",5);
				}
			break;
			case 28:
				blocknav("runmodule.php?module=docks&op=$op&op2=fish&op3=$op3");
				blocknav("runmodule.php?module=docks&op=$op&op2=gauge&op3=$op3");
				blocknav("runmodule.php?module=docks&op=$op&loc=".$temp);
				$pass=e_rand(1,5);
				output("You reel in a huge catch... about 160 pounds of... DEAD BODY!!!");
				output("You pull the body on the ship.  Will you search it?");
				addnav("Search the Body","runmodule.php?module=docks&op=$op&op2=bodysearch&op3=2");
				addnav("Leave the Body","runmodule.php?module=docks&op=$op&op2=bodysearch&op3=3");
			break;
			case 29:
				output("You're about to reel in a dinky fish when you hear a loud commotion from the other side of the boat.");
				output("One of the anglers has an amazing fish on the line! You see him struggle and pull and fight against it.");
				output("He's getting it closer to the boat when you catch a glimpse of the biggest fish you've ever seen!`n`n");
				if (get_module_setting("captaincrouton")==""){
					output("It's `QCaptain Crouton`7!!!");
				}else{
					output("It's `QThe Son of Captain Crouton`7!! (`qCaptain Crouton already having been caught by %s`7.)",get_module_setting("captaincrouton"));
				}
				output("`n`nJust as it looks like he's going to haul him in, the line breaks.");
				output("Looks like he got away... that means you still have a chance to catch him.");
			break;
			case 30:
				output("You reel in your line and it's empty.`n`nHaving kind of a slow day, you go to chat with some of the crew. They're sitting around telling fishing jokes.");
				output("You sit down and listen to one.`2`n`n");
				switch(e_rand(1,10)){
					case 1:
						output("What do you call a fish with no eyes?`n`@Fsh!");
					break;
					case 2:
						output("What lives in the ocean, is grouchy, and hates neighbors?`n`@A hermit crab!");
					break;
					case 3:
						output("Why did the whale cross the road?`n`@To get to the other tide!");
					break;
					case 4:
						output("What do you call a big fish who makes an offer you can't refuse?`n`@The Codfather!");
					break;
					case 5:
						output("What did the boy octopus say to the girl octopus?`n`@I wanna hold your hand, hand, hand, hand, hand, hand, hand, hand!");
					break;
					case 6:
						output("Why do ducks have webbed feet?`n`@To stamp out forest fires!");
					break;
					case 7:
						output("What's the saddest creature in the sea?`n`@The Blue Whale!");
					break;
					case 8:
						output("What's the difference between a fish and a piano?`n`@You can't tuna fish!");
					break;
					case 9:
						output("Where do fish go to borrow money?`n`@The Loan Shark!");
					break;
					case 10:
						output("What game do fish like playing the most?`n`@Name that tuna!");
					break;
				}
			break;
			case 31: case 32:
				output("You reel in your line and it's empty.");
			break;
			case 33:
				output("You reel it in and pull in a big fish; great job!");
				if ($session['user']['turns']>0){
					output("You lose a turn from exhaustion, but boy was it worth it!");
					$session['user']['turns']--;
				}
				$weight=e_rand(301,400);
			break;
			case 34:
				output("You reel it in and pull in a huge fish; excellent job! You gain 2 turns from excitement!");
				$weight=e_rand(401,500);
				$session['user']['turns']+=2;
			break;
			case 35:
				$rand=e_rand(1,10);
				if ($rand<10){
					output("You reel it in and pull in quite a nice fish! You proudly display your fish to everyone.  Nice Catch! You gain 2 turns from your adrenaline.");
					$session['user']['turns']+=2;
					$weight=e_rand(500,600);
				}else{
					$rand2=e_rand(1,10);
					if ($rand2<10){
						output("You reel in a great fish from the dock! You feel the envious eyes of the other anglers. You gain 3 turns from your adrenaline!");
						$session['user']['turns']+=3;
						$weight=e_rand(601,700);
					}else{
						$rand3=e_rand(1,15);
						if ($rand3<15){
							output("You reel in one of the biggest fish ever caught on the dock! It's quite a catch! You gain 4 turns from your adrenaline rush!");
							$session['user']['turns']+=4;
							$weight=e_rand(701,800);
						}else{
							output("You catch a fish so big you're almost pulled into the sea! It's amazing! One in a million! The other anglers on the dock come over and admire your fish. You gain 5 turns form your adrenaline rush!");
							$session['user']['turns']+=5;
							$weight=e_rand(801,820);
							if ($weight==820){
								$rand4=e_rand(1,20);
								if ($rand4<5) $weight=e_rand(1000,1100);
								elseif ($rand4<7) $weight=e_rand(1101,1200);
								elseif ($rand4<9) $weight=e_rand(1201,1300);
								elseif ($rand4<11) $weight=e_rand(1301,1400);
								elseif ($rand4<13) $weight=e_rand(1401,1600);
								elseif ($rand4<15) $weight=e_rand(1601,1800);
								elseif ($rand4<17) $weight=e_rand(1801,2000);
								elseif ($rand4<19) $weight=e_rand(2001,2500);
								elseif ($rand4<20) $weight=e_rand(2501,3000);
								elseif (get_module_setting("captaincrouton")==""){
									$weight=4000;
									$gold=get_module_setting("croutongold");
									$gems=get_module_setting("croutongems");
									$session['user']['gold']+=$gold;
									$session['user']['gems']+=$gems;
									output("`n`nYou've caught `qCaptain Crouton`7.  It is the most amazing catch ever. Congratulations!!!");
									output("`n`nYou receive a grand award of `^%s gold`7 and `%%s gems.",$gold,$gems);
									addnews("`^ caught `qCaptain Crouton`^, the 250 pound Super Fish of the sea!!!",$session['user']['name']);
									set_module_setting("captaincrouton",$session['user']['name']);
									debuglog("caught Captain Crouton to win $gold gold and $gem gems.");
								}else{
									$weight=3999;
									output("`n`nYou've caught one of `qCaptain Crouton's`7 offspring... `qBaby Crouton`7! He weighs only 1 ounce less than `qCaptain Crouton`7.");
									$gold=round(get_module_setting("croutongold")/10);
									$gems=round(get_module_setting("croutongems")/5);
									$session['user']['gold']+=$gold;
									$session['user']['gems']+=$gems;
									output("`n`nYou receive a super award of `^%s gold`7 and `%%s gems.",$gold,$gems);
									addnews("`^ caught `qSon of Captain Crouton`^, the 249 pound and 15 ounce Super Fish of the sea!!!",$session['user']['name']);
									debuglog("caught Son of Captain Crouton to win $gold gold and $gem gems.");
								}
							}
						}
					}
				}
			break;
			
		}
		if ($weight>0){
			$pounds=floor($weight/16);
			$ounces=$weight-($pounds*16);
			output("`n`nYou check the weight:`n`n`&");
			if ($pounds>0) output("%s %s%s`7",$pounds,translate_inline($pounds>1?"Pounds":"Pound"),translate_inline($ounces>0?",":""));
			if ($ounces>0) output("`&%s %s`7",$ounces,translate_inline($ounces>1?"Ounces":"Ounce"));
			increment_module_pref("numberfish",1);
			increment_module_pref("fishweight",$weight);
			if ($weight>get_module_pref("bigfish")){
				output("`n`nThis is the biggest fish you've ever caught!");
				set_module_pref("bigfish",$weight);
			}
		}
		docks_expeditionnav();
	}
}
?>