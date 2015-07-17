<?php

function staminafood_getmoduleinfo(){
	$info=array(
		"name"=>"Food for the Stamina System",
		"version"=>"20090127",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"Stamina",
		"download"=>"",
		"prefs"=>array(
			"nutrition"=>"Player's current Nutrition value,int|0",
			"fullness"=>"Player's current Fullness value,int|0",
			"fat"=>"Player's current Fat value,int|0",
		)
	);
	return $info;
}

function staminafood_install(){
	module_addhook("village");
	module_addhook("stamina-newday-intercept");
	module_addhook("stamina-newday");
	module_addhook("dragonkill");
	return true;
}

function staminafood_uninstall(){
	return true;
}

function staminafood_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "village":
			tlschema($args['schemas']['marketnav']);
			addnav($args['marketnav']);
			tlschema();
			switch($session['user']['location']){
				case "NewHome":
					addnav("Joe's Diner","runmodule.php?module=staminafood&op=start&location=nh");
					break;
				case "Kittania":
					addnav("Cool Springs Cafe","runmodule.php?module=staminafood&op=start&location=ki");
					break;
				case "New Pittsburgh":
					addnav("BRAAAAAINS","runmodule.php?module=staminafood&op=start&location=np");
					break;
				case "Squat Hole":
					addnav("Kebabs 'n' Shite","runmodule.php?module=staminafood&op=start&location=sq");
					break;
				case "Pleasantville":
					addnav("Mutated Munchies","runmodule.php?module=staminafood&op=start&location=pl");
					break;
			}
			break;
		case "stamina-newday-intercept":
			if ($session['user']['age']>1){
				//Remove the value of used Stamina from the player's Nutrition and Fat counters
				//Jokers do not have to eat, can do so if they want, but get no long-term benefit from doing so
				//Robots cannot eat, but don't suffer any effects from not doing so
				if ($session['user']['race']!="Robot" && $session['user']['race']!="Joker"){
					debug("Nutrition:");
					debug(get_module_pref("nutrition"));
					debug("Fat:");
					debug(get_module_pref("fat"));
					debug("Fullness:");
					debug(get_module_pref("fullness"));
					require_once("modules/staminasystem/lib/lib.php");
					$pctused = round(100 - get_stamina(3));
					if ($pctused>0){
						$newnu = get_module_pref("nutrition")-$pctused;
						$newfa = get_module_pref("fat")-$pctused;
						set_module_pref("nutrition",$newnu);
						set_module_pref("fat",$newfa);
					}
					if (get_module_pref("fat") < -200){
						set_module_pref("fat",-200);
					}
					if (get_module_pref("nutrition") < -200){
						set_module_pref("nutrition",-200);
					}
					if (get_module_pref("fat") > 200){
						set_module_pref("fat", 200);
					}
					if (get_module_pref("nutrition") > 200){
						set_module_pref("nutrition",200);
					}
				}
				increment_module_pref("fullness", -100);
				if (get_module_pref("fullness") < -100){
					set_module_pref("fullness", -100);
				}
			}
			break;
		case "stamina-newday":
			if ($session['user']['race']!="Robot" && $session['user']['race']!="Gobot" && $session['user']['race']!="Foebot" && $session['user']['race']!="Joker" && $session['user']['race']!="Stranger"){
				require_once("modules/staminasystem/lib/lib.php");
				//Output messages pertaining to the user's weight and fitness, apply buffs
				output("`nYou take a few moments to take stock of how you're looking and feeling.`n");
				$fat = get_module_pref("fat");
				$nut = get_module_pref("nutrition");
				debug ($nut);
				debug ($fat);
				if ($nut < 0 && $nut >= -20){
					output("`0You are feeling `4kinda weak and malnourished`0.  You lose some Stamina.`n");
					removestamina(25000);
				}
				if ($nut < -20 && $nut >= -50){
					output("`0You are feeling `4more than a little weak and malnourished`0.  You lose some Stamina.`n");
					removestamina(50000);
				}
				if ($nut < -50 && $nut >= -100){
					output("`0You are feeling `4very weak and ridiculously hungry`0.  You lose some Stamina.`n");
					removestamina(100000);
				}
				if ($nut < -100){
					output("`0You are `4slowly dying of malnutrition`0.  You lose some Stamina.`n");
					removestamina(200000);
				}
				if ($nut >= 0 && $nut<50){
					output("`0You are feeling quite healthy!`n");
				}
				if ($nut>=50 && $nut<100){
					output("`0You are feeling `2well-nourished and content`0!  You gain some Stamina!`n");
					addstamina(50000);
				}
				if ($nut>=100){
					output("`0You are feeling `2strong and energetic`0!  You gain some Stamina!`n");
					addstamina(100000);
				}
				if ($fat<0){
					output("`0You are looking `2trim and slender`0!  You gain some Stamina!`n");
					addstamina(50000);
				}
				if ($fat>=0 && $fat<20){
					output("`0You are looking pleasantly well-fed!`n");
				}
				if ($fat>=20 && $fat<50){
					output("`0You are looking `4a little bit round!`0  You lose a little Stamina.`n");
					removestamina(25000);
				}
				if ($fat>=50 && $fat<100){
					output("`0You are looking `4pretty chunky!`0  You lose some Stamina.`n");
					removestamina(50000);
				}
				if ($fat>=100){
					output("`0You are looking... well, let's not mince words.  `4You're fat.  VERY fat.  You look like you just ate a schoolbus full of well-fed orphans`0.  You lose some Stamina.`n");
					removestamina(100000);
				}
			}
			break;
		case "dragonkill":
			set_module_pref("nutrition",0);
			set_module_pref("fat",0);
			set_module_pref("fullness",0);
			break;
	}
	return $args;
}

function staminafood_run(){
	global $session;
	$pmeat1 = has_item_quantity("meat_low");
	$pmeat2 = has_item_quantity("meat_medium");
	$pmeat3 = has_item_quantity("meat_high");
	addnav("Eat");
	switch (httpget("op")){
		case "sellmeat":
			switch ($session['user']['location']){
				case "NewHome":
					page_header("Joe's Diner");
					if (httpget('q')==1){
						delete_item(has_item("meat_medium"));
						$session['user']['gold']+=5;
						output("With a surly grunt, Joe grabs your meat and slaps down five Requisition tokens.`n`n");
					} else {
						for ($i=1;$i<=$pmeat2;$i++){
							delete_item(has_item("meat_medium"));
							$session['user']['gold']+=5;
						}
						output("With a surly grunt, Joe grabs your meat and slaps down %s Requisition tokens.`n`n",$pmeat2*5);
					}
				break;
				case "New Pittsburgh":
					page_header("BRAAAAAINS");
					if (httpget('q')==1){
						delete_item(has_item("meat_medium"));
						$session['user']['gold']+=6;
						output("With a nod, the waiter takes your meat and hands back six Requisition tokens.`n`n");
					} else {
						for ($i=1;$i<=$pmeat2;$i++){
							delete_item(has_item("meat_medium"));
							$session['user']['gold']+=6;
						}
						output("With a nod, the waiter takes your meat and hands back %s Requisition tokens.`n`n",$pmeat2*6);
					}
				break;
				case "Kittania":
					page_header("Cool Springs Cafe");
					if (httpget('q')==1){
						delete_item(has_item("meat_high"));
						$session['user']['gold']+=12;
						output("With a warm smile, the waitress takes your meat and hands back twelve Requisition tokens.`n`n");
					} else {
						for ($i=1;$i<=$pmeat3;$i++){
							delete_item(has_item("meat_high"));
							$session['user']['gold']+=12;
						}
						output("With a warm smile, the waitress takes your meat and hands back %s Requisition tokens.`n`n",$pmeat3*12);
					}
				break;
				case "Squat Hole":
					page_header("Kebabs 'N' Shite");
					if (httpget('q')==1){
						delete_item(has_item("meat_low"));
						$session['user']['gold']+=2;
						output("With a squeaky \"Ta mate,\" the Midget behind the counter relieves you of the stinking yellow meat and hands back two Requisition tokens.`n`n");
					} else {
						for ($i=1;$i<=$pmeat1;$i++){
							delete_item(has_item("meat_low"));
							$session['user']['gold']+=2;
						}
						output("With a squeaky \"Ta mate,\" the Midget behind the counter relieves you of the stinking yellow meat and hands back %s Requisition tokens.`n`n",$pmeat1*2);
					}
				break;
			}
		break;
		case "start":
			switch (httpget("location")){
				case "nh":
					page_header("Joe's Diner");
					output("`0You head into what presents itself as a 1950's-style diner.  Plastic red and white gingham patterns cover every available surface.  Tomato-shaped ketchup bottles are dotted on tables here and there, dried gunge crusting their nozzles.  Behind the bar can be seen Joe, the owner, who is keeping himself busy wiping down the counter tops with a rag, redistributing the half-inch-thick layer of grease into a more uniform level.  A sign above the counter reads \"`2WE BUY MEAT.  WE PAY 5 REQ PER 120 GRAM'S.`0\"`n`n");
					if ($session['user']['race']!="Robot"){
						output("The smell of fried onions does its wicked work, and you glance up at the menu.`n`n");
						if (get_module_pref("fullness")<=100){
							if ($session['user']['gold']>=10){
								addnav("Crisps (10 Req)","runmodule.php?module=staminafood&op=buy&bought=1");
							} else {
								output("After a careful read of the menu, you realise that you can't afford a single thing on it.  Bah.");
							}
							if ($session['user']['gold']>=40){
								addnav("Garden Salad (40 Req)","runmodule.php?module=staminafood&op=buy&bought=2");
							}
							if ($session['user']['gold']>=50){
								addnav("Plate of Chips (50 Req)","runmodule.php?module=staminafood&op=buy&bought=3");
								addnav("Coffee (50 Req)","runmodule.php?module=staminafood&op=buy&bought=4");
							}
							if ($session['user']['gold']>=150){
								addnav("Bangers & Mash (150 Req)","runmodule.php?module=staminafood&op=buy&bought=5");
							}
							if ($session['user']['gold']>=300){
								addnav("Cheeseburger (300 Req)","runmodule.php?module=staminafood&op=buy&bought=6");
							}
						} else {
							output("You are far too full to eat any more today.`n`n");
						}
					}
					if ($pmeat2){
						output("You remember that Maiko told you that the NewHome diner will only buy middling-quality meat.  You have %s pieces of average-quality meat to sell.  All of them, quite conveniently - perhaps a little TOO conveniently - weigh exactly a hundred and twenty grams each.`n`n",$pmeat2);
						addnav("Sell Meat");
						addnav("Sell one piece","runmodule.php?module=staminafood&op=sellmeat&q=1");
						if ($pmeat2 > 1){
							addnav("Sell all Meat","runmodule.php?module=staminafood&op=sellmeat&q=all");
						}
					}
					break;
				case "ki":
					page_header("Cool Springs Cafe");
					output("You head into what at first appears to be a little hut.  As you work down the stairs into the rock underneath Kittania, you realise that this place is so much more.`n`nStrings of fairy lights illuminate the cavern, and soft trickling sounds can be heard against the laughter and conversation of KittyMorphs around you.`n`nYou take a seat and a white-furred KittyMorph approaches, a menu in her hand.  \"Welcome to the Cool Springs Cafe,\" she says with a smile.  \"We try to tread on Mother Earth as lightly as we can, in here; all of our produce is locally-grown, you'll find a wonderful selection of vegetarian and vegan meals, and the various waters come from the three springs that run through this very cavern.  Now, what can I get you?\"`n`nYou peruse the menu, your eyes lingering on the last entry, detailing a rare, dripping, bloody steak.  The KittyMorph follows your gaze, and laughs sheepishly.  \"Like I said, we `itry`i,\" she giggles, elongated canines peeking out.  \"We `iare`i carnivores, you know.  Oh, on that note, I should mention that we also buy meat, if you've got any of the good stuff to sell.  We pay twelve Requisition per slice.\"`n`n");
					if ($session['user']['race']!="Robot"){
						if (get_module_pref("fullness")<=100){
							if ($session['user']['gold']>=50){
								addnav("Hot Chocolate (50 Req)","runmodule.php?module=staminafood&op=buy&bought=7");
								addnav("White Spring Water (50 Req)","runmodule.php?module=staminafood&op=buy&bought=8");
							} else {
								output("After a careful read of the menu, you realise that you can't afford a single thing on it.  Bah.");
							}
							if ($session['user']['gold']>=100){
								addnav("Nut and Berry Salad (100 Req)","runmodule.php?module=staminafood&op=buy&bought=9");
							}
							if ($session['user']['gold']>=175){
								addnav("Turquoise Spring Water (175 Req)","runmodule.php?module=staminafood&op=buy&bought=10");
							}
							if ($session['user']['gold']>=250){
								addnav("Red Spring Water (250 Req)","runmodule.php?module=staminafood&op=buy&bought=11");
							}
							if ($session['user']['gold']>=500){
								addnav("Still-Twitching Steak (500 Req)","runmodule.php?module=staminafood&op=buy&bought=12");
							}
						} else {
							output("You are far too full to eat any more today.`n`n");
						}
					}
					if ($pmeat3){
						output("You remember that Maiko told you that the Kittania cafe will only buy the best-quality meat.  You have %s pieces of high-quality meat to sell.`n`n",$pmeat3);
						addnav("Sell Meat");
						addnav("Sell one piece","runmodule.php?module=staminafood&op=sellmeat&q=1");
						if ($pmeat3 > 1){
							addnav("Sell all Meat","runmodule.php?module=staminafood&op=sellmeat&q=all");
						}
					}
					break;
				case "np":
					page_header("BRAAAAAINS");
					output("You head into the local cafe, imaginatively titled \"BRAAAAAINS\".  A waiter comes shuffling over to you, green skin peeling from his face.  \"BRAAAAAINS?\" he asks, holding out a menu.`n`nA sign above the counter says \"We are only too happy to buy your surplus meat.  We pay 6 Requisition tokens per 120 grams.\"`n`n");
					if ($session['user']['race']!="Robot"){
						if (get_module_pref("fullness")<=100){
							if ($session['user']['gold']>=50){
								addnav("Egg and Brains (50 Req)","runmodule.php?module=staminafood&op=buy&bought=13");
							} else {
								output("After a careful read of the menu, you realise that you can't afford a single thing on it.  Bah.");
							}
							if ($session['user']['gold']>=100){
								addnav("Sausage and Brains (100 Req)","runmodule.php?module=staminafood&op=buy&bought=14");
							}
							if ($session['user']['gold']>=150){
								addnav("Spam and Brains (150 Req)","runmodule.php?module=staminafood&op=buy&bought=15");
							}
							if ($session['user']['gold']>=200){
								addnav("Egg, Brains, Sausage and Brains (200 Req)","runmodule.php?module=staminafood&op=buy&bought=16");
							}
							if ($session['user']['gold']>=250){
								addnav("Brains, Spam, Brains, Sausage and Brains (250 Req)","runmodule.php?module=staminafood&op=buy&bought=17");
							}
							if ($session['user']['gold']>=300){
								addnav("Brains, Brains, Brains, Brains and Spam (300 Req)","runmodule.php?module=staminafood&op=buy&bought=18");
							}
						} else {
							output("You are far too full to eat any more today.`n`n");
						}
					}
					if ($pmeat2){
						output("You remember that Maiko told you that the New Pittsburgh diner will only buy middling-quality meat.  You have %s pieces of average-quality meat to sell.  All of them, quite conveniently - perhaps a little TOO conveniently - weigh a hundred and twenty grams each.`n`n",$pmeat2);
						addnav("Sell Meat");
						addnav("Sell one piece","runmodule.php?module=staminafood&op=sellmeat&q=1");
						if ($pmeat2 > 1){
							addnav("Sell all Meat","runmodule.php?module=staminafood&op=sellmeat&q=all");
						}
					}
					break;
				case "sq":
					page_header("Kebabs 'N' Shite");
					output("You head into the local kebab house.  As you're studying the menu, a Midget brushes past you, dragging a six-foot blue plastic bag with the words \"INCINERATE ONLY\" stencilled on the side.  He stops, glares up at you, and mutters \"Yer din't see `inuffink.`i\"  Then he resumes his journey, dragging the bag into the back of the shop.  You're sure you saw a bit of steering wheel poking out.`n`nA sign above the counter reads \"WE BY MEET 2 REK PER BIT\"`n`n");
					if ($session['user']['race']!="Robot"){
						if (get_module_pref("fullness")<=100){
							if ($session['user']['gold']>=20){
								addnav("Crisps (20 Req)","runmodule.php?module=staminafood&op=buy&bought=25");
							} else {
								output("After a careful read of the menu, you realise that you can't afford a single thing on it.  Bah.");
							}
							if ($session['user']['gold']>=50){
								addnav("Skinheads on a Raft (50 Req)","runmodule.php?module=staminafood&op=buy&bought=26");
							}
							if ($session['user']['gold']>=75){
								addnav("Doner Kebab (75 Req)","runmodule.php?module=staminafood&op=buy&bought=27");
								addnav("Crimson Pitbull (75 Req)","runmodule.php?module=staminafood&op=buy&bought=28");
							}
							if ($session['user']['gold']>=150){
								addnav("Cock Nuggets (150 Req)","runmodule.php?module=staminafood&op=buy&bought=29");
							}
							if ($session['user']['gold']>=200){
								addnav("Sausage Feast Pizza (200 Req)","runmodule.php?module=staminafood&op=buy&bought=30");
							}
						} else {
							output("You are far too full to eat any more today.`n`n");
						}
					}
					if ($pmeat1){
						output("You remember that Maiko told you that the Squat Hole kebab shop only buys the sort of meat that the dog food factory would throw away.  You have %s wobbling chunks of Crap Meat to sell.`n`n",$pmeat1);
						addnav("Sell Meat");
						addnav("Sell one piece","runmodule.php?module=staminafood&op=sellmeat&q=1");
						if ($pmeat1 > 1){
							addnav("Sell all Meat","runmodule.php?module=staminafood&op=sellmeat&q=all");
						}
					}
					break;
				case "pl":
					page_header("Mutated Munchies");
					if ($session['user']['race']!="Robot"){
						output("You head into the local cafe.  The stench of vomit and disinfectant hangs in the air.  Bravely, you sit down at a table and peruse the menu.`n`n");
						if (get_module_pref("fullness")<=100){
							if ($session['user']['gold']>=100){
								addnav("Wriggly Biscuits (100 Req)","runmodule.php?module=staminafood&op=buy&bought=19");
							} else {
								output("After a careful read of the menu, you realise that you can't afford a single thing on it.  Bah.");
							}
							if ($session['user']['gold']>=200){
								addnav("Phallic Nuts (200 Req)","runmodule.php?module=staminafood&op=buy&bought=20");
							}
							if ($session['user']['gold']>=300){
								addnav("Noodly Noodles (300 Req)","runmodule.php?module=staminafood&op=buy&bought=21");
							}
							if ($session['user']['gold']>=400){
								addnav("Three-Eyed Fish (400 Req)","runmodule.php?module=staminafood&op=buy&bought=22");
							}
							if ($session['user']['gold']>=500){
								addnav("Magical Mystery Meatloaf (500 Req)","runmodule.php?module=staminafood&op=buy&bought=23");
							}
							if ($session['user']['gold']>=750){
								addnav("Mutant Steak (750 Req)","runmodule.php?module=staminafood&op=buy&bought=24");
							}
						} else {
							output("You are far too full to eat any more today.`n`n");
						}
					} else {
						output("You are a robot, and this place doesn't buy meat.  You have no business here.  Out with you!`n`n");
					}
					break;
			}
			break;
		case "buy":
			page_header("Om nom nom");
			require_once("modules/staminasystem/lib/lib.php");
			switch (httpget("bought")){
				case 1:
					output("You munch happily on your crisps, reflecting that they're probably not too good for you - but hell, at least they're cheap.`n`nYou gain some Stamina!");
					$st = 5000;
					$nu = 5;
					$fa = 10;
					$fu = 5;
					$co = 10;
					break;
				case 2:
					output("You pick the snails out of your Garden Salad, and tuck in.`n`nYou gain some Stamina!");
					$st = 18000;
					$nu = 15;
					$fa = 0;
					$fu = 10;
					$co = 40;
					break;
				case 3:
					output("You wolf down your plate of chips.  They're like little brown paper bags filled with pus, but damn it, you paid good money for these and you're going to eat them come Hell or high water.`n`nYou gain some Stamina!");
					$st = 25000;
					$nu = 20;
					$fa = 30;
					$fu = 25;
					$co = 50;
					break;
				case 4:
					output("The coffee swirls grittily down your throat.  You feel like you have more energy!");
					apply_stamina_buff('newhomedinercoffee', array(
						"name"=>"Caffeine Rush",
						"action"=>"Global",
						"costmod"=>0.8,
						"expmod"=>1,
						"rounds"=>20,
						"roundmsg"=>"Your Caffeine Rush makes everything a little bit easier!",
						"wearoffmsg"=>"The effects of the caffeine seem to have worn off.",
					));
					$co = 50;
					break;
				case 5:
					output("It's not so much Bangers and Mash as Mingers and Mush, but you get stuck in anyway.`n`nYou gain some Stamina!");
					$st = 105000;
					$nu = 40;
					$fa = 30;
					$fu = 40;
					$co = 150;
					break;
				case 6:
					output("You can has cheeseburger!  Well, it's not so much \"cheese\" as \"unidentifiable bright orange goo\", but I'm sure you'll live.`n`nYou gain some Stamina!");
					$st = 225000;
					$nu = 60;
					$fa = 40;
					$fu = 25;
					$co = 300;
					break;
				case 7:
					output("You sip your hot chocolate, listening to the babbling brooks.  Life ain't so bad.`n`nYou gain some Stamina!");
					$st = 25000;
					$nu = 10;
					$fa = 10;
					$fu = 15;
					$co = 50;
					break;
				case 8:
					output("There's something very odd about the water from the White Spring.  You feel light on your feet.  It seems to make everything easier, somehow.");
					apply_stamina_buff('whitespringwater', array(
						"name"=>"White Spring Lightness",
						"action"=>"Global",
						"costmod"=>0.8,
						"expmod"=>1,
						"rounds"=>20,
						"roundmsg"=>"The waters of the White Spring seem to be making everything a little easier.",
						"wearoffmsg"=>"The White Spring effects seem to have worn off.",
					));
					$co = 50;
					break;
				case 9:
					output("You scarf down your salad of nuts and berries, secretly wishing for a nice juicy steak.`n`nYou gain some Stamina!");
					$st = 65000;
					$nu = 25;
					$fa = 5;
					$fu = 10;
					$co = 100;
					break;
				case 10:
					output("The water from the Turquoise Spring is served at room temperature, but somehow tastes very cold.  After a few moments, your eyesight improves; you can make out individual facets of a crystal buried in the far wall of the cavern.  This should make it a bit easier to hunt down monsters!");
					apply_stamina_buff('turquoisespringwater', array(
						"name"=>"Turquoise Sight",
						"class"=>"Hunting",
						"costmod"=>0.5,
						"expmod"=>1,
						"rounds"=>3,
						"roundmsg"=>"Thanks to your heightened senses granted by the waters of the Turquoise Spring, hunting for monsters seems a lot easier now.",
						"wearoffmsg"=>"The effects of the Turquoise Spring water have worn off, and your senses return to their usual state.",
					));
					$co = 175;
					break;
				case 11:
					output("You down the water from the Red Spring.  There's a distinct taste of iron in there.  After a few moments, you become anxious - surely there must be something around here that you can engage in combat...");
					apply_stamina_buff('redspringwater', array(
						"name"=>"Red Haze",
						"class"=>"Combat",
						"costmod"=>0.5,
						"expmod"=>1,
						"rounds"=>20,
						"roundmsg"=>"The waters of the Red Spring make fighting seem more natural and fluid.  You're not expending nearly as much Stamina as usual.",
						"wearoffmsg"=>"The effects of the Red Spring water have worn off, and your senses return to their natural state.",
					));
					$co = 250;
					break;
				case 12:
					output("The menu described a still-twitching steak, and boy, it delivered.  Steam rises from the plate of raw muscle in front of you.  The meat jerks and spasms as it reacts to the sudden flood of oxygen, slapping gently against the plate.  Your mouth waters, and you can see some nearby KittyMorphs glancing jealously in your direction.  You wolf it down before they get any ideas.`n`nYou gain some Stamina!");
					$st = 385000;
					$nu = 100;
					$fa = 10;
					$fu = 80;
					$co = 500;
					break;
				case 13:
					output("You scarf down your braaaaainy meal, hoping that there will be no complications from the frankly ridiculous amounts of braaaaains that you're eating.`n`nYou gain some Stamina!");
					$st = 25000;
					$nu = 10;
					$fa = 10;
					$fu = 10;
					$co = 50;
					break;
				case 14:
					output("You scarf down your braaaaainy meal, hoping that there will be no complications from the frankly ridiculous amounts of braaaaains that you're eating.`n`nYou gain some Stamina!");
					$st = 65000;
					$nu = 20;
					$fa = 20;
					$fu = 20;
					$co = 100;
					break;
				case 15:
					output("You scarf down your braaaaainy meal, hoping that there will be no complications from the frankly ridiculous amounts of braaaaains that you're eating.`n`nYou gain some Stamina!");
					$st = 105000;
					$nu = 30;
					$fa = 30;
					$fu = 30;
					$co = 150;
					break;
				case 16:
					output("You scarf down your braaaaainy meal, hoping that there will be no complications from the frankly ridiculous amounts of braaaaains that you're eating.`n`nYou gain some Stamina!");
					$st = 145000;
					$nu = 40;
					$fa = 40;
					$fu = 40;
					$co = 200;
					break;
				case 17:
					output("You scarf down your braaaaainy meal, hoping that there will be no complications from the frankly ridiculous amounts of braaaaains that you're eating.`n`nYou gain some Stamina!");
					$st = 185000;
					$nu = 50;
					$fa = 50;
					$fu = 50;
					$co = 250;
					break;
				case 18:
					output("You scarf down your braaaaainy meal, hoping that there will be no complications from the frankly ridiculous amounts of braaaaains that you're eating.`n`nYou gain some Stamina!");
					$st = 225000;
					$nu = 60;
					$fa = 60;
					$fu = 60;
					$co = 300;
					break;
				case 19:
					output("You chew thoughtfully on your Wriggly Biscuits.  They live up to their name quite adequately.`n`nYou gain some Stamina!");
					$st = 65000;
					$nu = 20;
					$fa = 10;
					$fu = 10;
					$co = 100;
					break;
				case 20:
					output("You run your tongue lovingly up, down and around the extremely phallic nuts, savouring their delightfully salty flavour.`n`nYou gain some Stamina!");
					$st = 145000;
					$nu = 40;
					$fa = 10;
					$fu = 20;
					$co = 200;
					break;
				case 21:
					output("The Noodly Noodles jerk and spasm, each bite releasing a sticky yellow goo.  Tasty!`n`nYou gain some Stamina!");
					$st = 225000;
					$nu = 40;
					$fa = 10;
					$fu = 20;
					$co = 300;
					break;
				case 22:
					output("You stare down at your three-eyed fish.  It stares back up at you, in ways that you can only imagine.  After a few moments of depressed contemplation, you tuck in - and hey, it's actually not that bad.`n`nYou gain some Stamina!");
					$st = 305000;
					$nu = 50;
					$fa = 25;
					$fu = 40;
					$co = 400;
					break;
				case 23:
					output("As your fork sinks into your Magical Mystery Meatloaf, a hundred pairs of eyes open on its crispy skin.  They close again almost instantly, and you try your hardest to persuade yourself that you were hallucinating from the disinfectant fumes - it beats reality, that's for damn sure.`n`nYou gain some Stamina!");
					$st = 385000;
					$nu = 50;
					$fa = 25;
					$fu = 50;
					$co = 500;
					break;
				case 24:
					output("The Mutant Steak is lean, delicious, nutritious and as big as your head!  What a pleasant surprise!`n`nYou gain some Stamina!");
					$st = 585000;
					$nu = 150;
					$fa = 10;
					$fu = 80;
					$co = 750;
					break;
				case 25:
					output("You grimace, and call over to the midget behind the counter.  \"These taste like someone tried to make bacon flavour crisps and failed, badly.\"`n`n\"Read the fookin' packet, dick'ead!\"`n`nYou do as he says.  Ah.  Tumour flavour.  Nice.`n`nYou gain some Stamina!");
					$st = 10000;
					$nu = 5;
					$fa = 10;
					$fu = 5;
					$co = 20;
					break;
				case 26:
					output("Skinheads on a Raft turned out to be beans on toast.  You're not sure whether to be relieved.`n`nYou gain some Stamina!");
					$st = 25000;
					$nu = 20;
					$fa = 20;
					$fu = 15;
					$co = 50;
					break;
				case 27:
					//Potential for expansion: have the kebab taste better if the player's drunk, have the player find a wedding ring that he can sell for Req, etc.
					output("You scarf down your doner kebab, stopping occasionally to pick out the occasional toenail or piece of car dashboard.  It's pretty fatty, and would taste a lot better if you were drunk, but you make do.`n`nYou gain some Stamina!");
					$st = 65000;
					$nu = 20;
					$fa = 40;
					$fu = 20;
					$co = 75;
					break;
				case 28:
					output("You knock back your Crimson Pitbull, which turned out to be some sort of energy drink.`n`nYou feel aggressive!");
					apply_stamina_buff('crimsonpitbull', array(
						"name"=>"Bark of the Crimson Pitbull",
						"class"=>"Combat",
						"costmod"=>0.5,
						"expmod"=>1,
						"rounds"=>20,
						"roundmsg"=>"The harsh, chemically-sweet taste of the Crimson Pitbull lurks in the back of your throat.  You're not expending nearly as much Stamina as usual.",
						"wearoffmsg"=>"The effects of the Crimson Pitbull have worn off, and your senses return to their natural state.",
					));
					$co = 75;
					break;
				case 29:
					output("Cock Nuggets turned out to be chicken nuggets, only with a little more fowl language.`n`n...`n`n...sorry.`n`nYou gain some Stamina!");
					$st = 145000;
					$nu = 30;
					$fa = 60;
					$fu = 25;
					$co = 150;
					break;
				case 30:
					output("The Sausage Feast turned out to be less sausage, and more fat, but hey, it's cheap and it tastes halfway decent.`n`nYou gain some Stamina!");
					$st = 185000;
					$nu = 40;
					$fa = 80;
					$fu = 50;
					$co = 200;
					break;
			}
			addstamina($st);
			increment_module_pref("nutrition", $nu);
			increment_module_pref("fat", $fa);
			increment_module_pref("fullness", $fu);
			$full = get_module_pref("fullness");
			if ($full < 0){
				output("`n`nYou still feel as though you haven't eaten in days.");
			}
			if ($full >= 0 && $full < 50){
				output("`n`nYou feel a little less hungry.");
			}
			if ($full >= 50 && $full < 100){
				output("`n`nYou still feel as though you've got room for more!");
			}
			if ($full >= 100){
				output("`n`nYou're stuffed!  You feel as though you can't possibly eat anything more today.");
			}
			$session['user']['gold'] -= $co;
			break;
	}
	debug(get_module_pref("fat"));
	debug(get_module_pref("nutrition"));
	addnav("Exit");
	addnav("Return to the Outpost","village.php");
	page_footer();
}
?>
