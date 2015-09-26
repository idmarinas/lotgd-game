<?php
function docks_docks(){
	global $session;
	$op = httpget('op');
	$op2 = httpget('op2');
	$op3 = httpget('op3');
	$op4 = httpget('op4');
	page_header("The Docks");
	if ($op2=="enter"){
		output("`b`c`^The Docks`b`c`n`7");
		output("You head down to the docks to see what adventures await you there.");
		output("Taking a look around, you notice several locations of interest including one of the fishing vessels coming and going. Where would you like to go from here?");
		addnav("The Docks");
		addnav("`QBait and Tackle Shack","runmodule.php?module=docks&op=docks&op2=baittackle");
		addnav("`qGo Fishing on the Dock","runmodule.php?module=docks&op=docks&op2=fishing");
		addnav("Fishing Boat");
		addnav("`&`iCorinth`i","runmodule.php?module=docks&op=docks&op2=corinth");//Fishing Vessel
		addnav("Return");
		addnav("Back to the Forest","forest.php");
	}
	//Bait and Tackle Shop
	if ($op2=="baittackle"){
		output("`b`c`^Bait and Tackle Shop`b`c`n`7");
		output("You take a look around the old bait shop to see what you might purchase.");
		output("You notice the owner sitting on a stool behind the counter.  His skin is sun and salt worn but his eyes remain sharp. He looks at you with a smile.");
		output("`2`n`n'My name is Hoglin. Welcome to my little shop.  What can I interest you in today?'");
		output("`n`n`7You decide to take a look around.`n`n");
		if (get_module_pref("pole")==1 || get_module_pref("bait")==1 || get_module_pref("fishbook")==1){
			output("`n`c`@Fishing Inventory:`c`1");
			if (get_module_pref("pole")==1) output("`cFishing Pole`c");
			if (get_module_pref("bait")==1) output("`cBait`c");
			if (get_module_pref("fishbook")==1) output("`cBook on Fishing`c");
			if (get_module_pref("pole")==1 && get_module_pref("bait")==1) output("`n`c`!Looks like you're ready to go fishing!`c");
		}
		docks_baitnav();
	}
	if ($op2=="fishchat"){
		output("`b`c`^Bait and Tackle Shop`b`c`n`7");
		output("You ask Hoglin how the fish are biting.");
		if (get_module_pref("fishweight")<100){
			output("`n`n`2'Ah, well, you can catch a nice bounty off the dock, that's for sure.");
			if (get_module_pref("pole")==1 || get_module_pref("bait")==1){
				output("I notice you're getting ready to go fishing yourself there. You've got your");
				if (get_module_pref("pole")==1) output("fishing pole");
				if (get_module_pref("pole")==1 && get_module_pref("bait")==1) output("and");
				if (get_module_pref("bait")==1) output("bait");
				output("so I bet you're ready to get out there.");
			}else output ("You'll need a good fishing pole and some bait before you can go fishing.");
			if (get_module_pref("fishbook")==1) output("Your book probably won't be any good until you get onto a fishing boat.");
			else output("I have some fishing books for sale, but they mainly deal with open sea fishing; they're not too useful for fishing off the end of the dock.");
			output("Now, of course, the best place to catch fish is from the side of a ship.  However, I doubt you'll be let onto a fishing vessel until you've proven you know your way around a fishing pole; so for now your best bet is to head to the docks.'");
		}else{
			output("Hoglin notices that you've been doing your fair share of fishing and you've got your name up on the list showing your abilities.");
			output("`n`n`2'You know, the best fish are caught in the deep deep sea.  I recommend you try to charter an expedition on the `&`iCorinth`i`2 for some real fishing!'");
		}
		if (get_module_pref("pole")==1 || get_module_pref("bait")==1 || get_module_pref("fishbook")==1){
			output("`n`n`c`@Fishing Inventory:`c`1");
			if (get_module_pref("pole")==1) output("`cFishing Pole`c");
			if (get_module_pref("bait")==1) output("`cBait`c");
			if (get_module_pref("fishbook")==1) output("`cBook on Fishing`c");
			if (get_module_pref("pole")==1 && get_module_pref("bait")==1) output("`n`c`!Looks like you're ready to go fishing!`c");
		}
		docks_baitnav();
		blocknav("runmodule.php?module=docks&op=docks&op2=fishchat");
	}
	if ($op2=="fishbooks"){
		output("`b`c`^Fishing Books`b`c`n`7");
		output("You decide to take a look at the collection of fishing books for sale.  There doesn't seem to be much of a selection. After reading through a couple of titles you realize there's probably only one worth");
		addnav("Fishing Books");
		if (get_module_pref("fishbook")==0 && get_module_pref("readbook")==1){
			output("purchasing.  However, if you hadn't lost your old one, you wouldn't be looking at this one now, would you?");
			output("`n`n`2'You know that one costs `^125 gold`2. Are you interested?'");
			addnav("Purchase Book","runmodule.php?module=docks&op=docks&op2=fishbookbuy");
		}elseif (get_module_pref("fishbook")==1){
			output("purchasing and it looks like you've already purchased it.");
			addnav("Read Fishing Book","runmodule.php?module=docks&op=docks&op2=readfishbook&op3=store");
		}else{
			output("purchasing.`n`nYou ask Hoglin how much the book titled `HFishing the Open Seas`H`7 costs.  He comes over and takes a look at the book, looks you over, and then quotes you a price.");
			output("`n`n`2'Well, this one here's going to run you `^125 gold`2. Are you interested?'");
			addnav("Purchase Book","runmodule.php?module=docks&op=docks&op2=fishbookbuy");
		}
		docks_baitnav();
		blocknav("runmodule.php?module=docks&op=docks&op2=fishbooks");
	}
	if ($op2=="fishbookbuy"){
		output("`b`c`^Fishing Books`b`c`n`7");
		if ($session['user']['gold']>=125){
			addnav("Fishing Books");
			addnav("Read Fishing Book","runmodule.php?module=docks&op=docks&op2=readfishbook&op3=store");
			set_module_pref("fishbook",1);
			$session['user']['gold']-=125;
			output("You hand over the `^125 gold`7 and take a read through the book.");
		}else{
			output("Hoglin repeats the price of the book to you and you realize you don't have enough gold to purchase it.");
			output("`n`nHe puts it back on the shelf and tells you not to worry, it will still be here when you get enough gold.");
			docks_baitnav();
			blocknav("runmodule.php?module=docks&op=docks&op2=fishbooks");
		}
	}
	if ($op2=="readfishbook"){
		output("`b`c`^Fishing the Open Seas`c`b`n`7");
		if (get_module_pref("readbook")==0){
			output("`6`i\"The winds are critical to the assessment of the location of fish schools. A complicated algorithm has been devised based from the works and studies of Francisco Gronadgolan starting after the Yilithian Wars.");
			output("The foundation of wind analysis includes a correlation with the depth of the water and the surrounding undercurrent temperature with an inversion of the product used to calculate the likelihood ratio for...`i\"");
			output("`n`n`7BLAH BLAH BLAH!  This is the most boring information you've ever seen in your life.");
			output("You page through the book and finally find a chart that could possibly be useful.  You tear out this chart and keep it for future reference and you'll find it available when you need it:`n`n");
			set_module_pref("readbook",1);
		}
		output("`c`b`%How to find Excellent Fishing locations using Temperature, Depth, and Wind Speed`b`c`n");
		output("`c`b`#Temperature To Depth Reference`b");
		rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
		rawoutput("<tr class='trhilight'><td><center>");
		rawoutput("</center></td><td class='trhead'><center>");
		output("50+ Feet");
		rawoutput("</center></td><td class='trhead'><center>");
		output("75+ Feet");
		rawoutput("</center></td><td class='trhead'><center>");
		output("100+ Feet");
		rawoutput("<center/></td></tr>");
		rawoutput("<tr class='trhead'><td><center>");
		output("60+ Degrees");
		rawoutput("</center></td><td class='trlight'><center>");
		output("`\$A");
		rawoutput("</center></td><td class='trlight'><center>");
		output("`^B");
		rawoutput("</center></td><td class='trlight'><center>");
		output("`@C");
		rawoutput("<center/></td></tr>");
		rawoutput("<tr class='trhead'><td><center>");
		output("70+ Degrees");
		rawoutput("</center></td><td class='trdark'><center>");
		output("`^B");
		rawoutput("</center></td><td class='trdark'><center>");
		output("`@C");
		rawoutput("</center></td><td class='trdark'><center>");
		output("`\$A");
		rawoutput("<center/></td></tr>");
		rawoutput("<tr class='trhead'><td><center>");
		output("80+ Degrees");
		rawoutput("</center></td><td class='trlight'><center>");
		output("`@C");
		rawoutput("</center></td><td class='trlight'><center>");
		output("`\$A");
		rawoutput("</center></td><td class='trlight'><center>");
		output("`^B");
		rawoutput("<center/></td></tr>");
		rawoutput("</table>");
		output("`n`b`@Wind Cross Reference to Temperature/Depth Chart`b");
		rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
		rawoutput("<tr class='trhead'><td class='trhilight'><center>");
		rawoutput("</center></td><td><center>");
		output("`\$A");
		rawoutput("</center></td><td><center>");
		output("`^B");
		rawoutput("</center></td><td><center>");
		output("`@C");
		rawoutput("<center/></td></tr>");
		rawoutput("<tr class='trhead'><td><center>");
		output("0-10 Knots");
		rawoutput("</center></td><td class='trlight'><center>");
		output("Excellent");
		rawoutput("</center></td><td class='trlight'><center>");
		output("Poor");
		rawoutput("</center></td><td class='trlight'><center>");
		output("Fair");
		rawoutput("<center/></td></tr>");
		rawoutput("<tr class='trhead'><td><center>");
		output("11-20 Knots");
		rawoutput("</center></td><td class='trdark'><center>");
		output("Fair");
		rawoutput("</center></td><td class='trdark'><center>");
		output("Excellent");
		rawoutput("</center></td><td class='trdark'><center>");
		output("Poor");
		rawoutput("<center/></td></tr>");
		rawoutput("<tr class='trhead'><td><center>");
		output("21+ Knots");
		rawoutput("</center></td><td class='trlight'><center>");
		output("Poor");
		rawoutput("</center></td><td class='trlight'><center>");
		output("Fair");
		rawoutput("</center></td><td class='trlight'><center>");
		output("Excellent");
		rawoutput("<center/></td></tr>");
		rawoutput("</table>");
		output_notl("`c");
		if ($op3=="store"){
			docks_baitnav();
			blocknav("runmodule.php?module=docks&op=docks&op2=fishbooks");
			blocknav("runmodule.php?module=docks&op=docks&op2=readfishbook&op3=store");
		}elseif ($op3=="expedition"){
			page_header("Fishing Expedition");
			addnav("Return to Gauges","runmodule.php?module=docks&op=fishingexpedition&op2=gauge&op3=$op4");
			addnav("Return to Expedition","runmodule.php?module=docks&op=fishingexpedition&loc=".get_module_pref("pqtemp"));
		}elseif ($op3=="expeditiona"){
			page_header("Fishing Expedition");
			addnav("Return to Expedition","runmodule.php?module=docks&op=fishingexpeditiona");
		}
	}
	if ($op2=="fishpoles"){
		output("`b`c`^Fishing Pole`b`c`n`7");
		output("Hoglin shows off the fishing poles.`2");
		addnav("Fishing Poles");
		if (get_module_pref("pole")==1){
			output("`7Suddenly, Hoglin realizes you've already got a mighty fine fishpole strapped to your back. `3'Hey! You don't need another fishing pole!'");
		}elseif (get_module_pref("stickstring")==0 && $op3==""){
			output("'Well, I can give you a stick with a string or I can sell you a quality fishing pole.  What would you prefer?'");
			addnav("Stick with a String","runmodule.php?module=docks&op=docks&op2=fishstick");
			addnav("Quality Fishing Poles","runmodule.php?module=docks&op=docks&op2=fishpoles&op3=quality");
		}else{
			output("`7He shows you a very sturdy fishing pole.  You examine it closely, pretending that you know what you're looking at.");
			output("You studder out `#'How much?'`7 and Hoglin knows he's got a bite for one of his wares.`n`n`2");
			if (get_module_setting("fishingpole")>0) output("'Such a nice pole can't be purchased for less than `^%s gold`2. Are you interested?'",get_module_setting("fishingpole"));
			else output("'Luckily, I received a donation recently to sponsor new fishermen. I can give this to you for free.'");
			if ($session['user']['gold']<get_module_setting("fishingpole")){
				output("`7`n`nExcited at the prospect, you get ready to hand over the gold but suddenly realize you're a little short. You'll have to come back with you've got some more money.");
			}else{
				addnav("Purchase Fishing Pole","runmodule.php?module=docks&op=docks&op2=fishpolebuy");
			}
		}
		if (get_module_pref("pole")==1 || get_module_pref("bait")==1 || get_module_pref("fishbook")==1){
			output("`n`n`c`@Fishing Inventory:`c`1");
			if (get_module_pref("pole")==1) output("`cFishing Pole`c");
			if (get_module_pref("bait")==1) output("`cBait`c");
			if (get_module_pref("fishbook")==1) output("`cBook on Fishing`c");
			if (get_module_pref("pole")==1 && get_module_pref("bait")==1) output("`n`c`!Looks like you're ready to go fishing!`c");
		}
		blocknav("runmodule.php?module=docks&op=docks&op2=fishpoles");
		docks_baitnav();
	}
	if ($op2=="fishstick"){
		output("`b`c`^Fishing Pole`b`c`n`7");
		output("`3'I take it you've never heard of sarcasm. You can't catch fish with just a stick and a string; at least not anything worth mentioning. Let's talk about some real fishing equipment.'");
		set_module_pref("stickstring",1);
		docks_baitnav();
	}
	if ($op2=="fishpolebuy"){
		output("`b`c`^Fishing Pole`b`c`n`7");
		output("You hand over your `^%s gold`7 and Hoglin gives you a brand new fishing pole.  How nice!",get_module_setting("fishingpole"));
		$session['user']['gold']-=get_module_setting("fishingpole");
		set_module_pref("pole",1);
		docks_baitnav();
		blocknav("runmodule.php?module=docks&op=docks&op2=fishpoles");
		if (get_module_pref("pole")==1 || get_module_pref("bait")==1 || get_module_pref("fishbook")==1){
			output("`n`n`c`@Fishing Inventory:`c`1");
			if (get_module_pref("pole")==1) output("`cFishing Pole`c");
			if (get_module_pref("bait")==1) output("`cBait`c");
			if (get_module_pref("fishbook")==1) output("`cBook on Fishing`c");
			if (get_module_pref("pole")==1 && get_module_pref("bait")==1) output("`n`c`!Looks like you're ready to go fishing!`c");
		}
	}
	if ($op2=="fishbait"){
		output("`b`c`^Bait`b`c`n`7");
		output("`2'Now, quality worms are essential to catching anything worth keeping.");
		if (get_module_pref("bait")>0){
			$fishingbait=get_module_setting("fishingbait");
			$sellprice=floor(.75*$fishingbait*((5-get_module_pref("fishingtoday"))/5));
			if ($sellprice==0) $sellprice=floor($fishingbait*.75);
			output("`2I notice you've got some nice Nightcrawlers there.  Would you be interested in selling some of them? I'd be willing to buy your Nightcrawlers for `^%s gold`2. Let me know if you're interested.'",$sellprice);
			addnav("Bait Sell");
			addnav("Sell Nightcrawlers","runmodule.php?module=docks&op=docks&op2=fishbaitsell&op3=$sellprice");
		}elseif (get_module_pref("fishingtoday")=="" || get_module_pref("fishingtoday")==0){
			if (is_module_active("trading")) output("I can tell you've seen those run-of-the-mill worms from the trading posts.  Those won't do.");
			output("You need Nightcrawlers to play with the big fish.  A box of nightcrawlers will last for a day of fishing.  Of course, these little guys don't last too long so you'll need to get some more everyday.");
			output("They only cost `^%s gold`2.  Are you interested?'",get_module_setting("fishingbait"));
			addnav("Bait Purchase");
			addnav("Box of Nightcrawlers","runmodule.php?module=docks&op=docks&op2=fishbaitbuy");
		}else{
			output("But there's a problem.'`n`n`7Hoglin tells you that he's sorry but he doesn't have enough Nightcrawlers to sell; he has to save them for the `&`iCorinth`i`7 because she's setting sail soon. `2'Stop by tomorrow.  I have a kid out there trying to find some for me.'");
		}
		docks_baitnav();
		blocknav("runmodule.php?module=docks&op=docks&op2=fishbait");
	}
	if ($op2=="fishbaitsell"){
		output("`b`c`^Bait`b`c`n`7");
		output("You hand over your Nightcrawlers and collect `^%s gold`7.",$op3);
		$session['user']['gold']+=$op3;
		set_module_pref("bait",0);
		set_module_pref("fishingtoday",5);
		docks_baitnav();
	}
	if ($op2=="fishbaitbuy"){
		output("`b`c`^Bait`b`c`n`7");
		$fishingbait=get_module_setting("fishingbait");
		if (get_module_pref("bait")==1) output("You show your box of Nightcrawlers to Hoglin and he looks them over.`n`n`2'These still look good.  You don't need to buy anymore.'");
		elseif ($session['user']['gold']<$fishingbait) output("You pretend to hand Hoglin `^%s gold`7 and he winks and pretends to hand you a box of Nightcrawlers.  To get REAL Nightcrawlers you'll need to get some REAL gold.",$fishingbait);
		else{
			output("You hand Hoglin `^%s gold`7 and he hands you a box of Nightcrawlers.  You look at the wiggly suckers and blanch a little.  Do fish really think these are tasty?? Oh well, if that's what Hoglin says they like, that's what you're going to use.",$fishingbait);
			$session['user']['gold']-=$fishingbait;
			set_module_pref("bait",1);
		}
		if (get_module_pref("pole")==1 || get_module_pref("bait")==1 || get_module_pref("fishbook")==1){
			output("`n`n`c`@Fishing Inventory:`c`1");
			if (get_module_pref("pole")==1) output("`cFishing Pole`c");
			if (get_module_pref("bait")==1) output("`cBait`c");
			if (get_module_pref("fishbook")==1) output("`cBook on Fishing`c");
			if (get_module_pref("pole")==1 && get_module_pref("bait")==1) output("`n`c`!Looks like you're ready to go fishing!`c");
		}
		docks_baitnav();
		blocknav("runmodule.php?module=docks&op=docks&op2=fishbait");
	}
	if ($op2=="fishnotices"){
		output("`b`c`^Notice Board`b`c`n`7");
		output("You decide to go look over the Notice Board.");
		docks_noticenav();
		docks_baitnav();
		blocknav("runmodule.php?module=docks&op=docks&op2=fishnotices");
	}
	if ($op2=="forsale"){
		output("`b`c`^For Sale`b`c`n`7");
		output("You decide to peruse the 'For Sale' Notices.`n`n");
		output("`5`cFor Sale");
		if ($op3=="usedfish"){
			output("Used fish for sale.  Slight odor.  Please contact Klodnio if interested.`c");
			output("`7`n`nHmm... Now who on earth would want used fish?");
		}elseif ($op3=="newfish"){
			output("New fish for sale.  Never used.  Only 5 weeks old, slight odor.  Please contact Klodnio if interested.`c");
			output("`7`n`nSeems like this Klodnio character has a new marketing ploy for old dead fish.");
		}elseif ($op3=="stickstring"){
			output("Stick with string.  Not useful for fishing, so who are you kidding?`c");
			output("`7`n`nThere's no contact number.  This must be one of Hoglin's jokes.");
		}
		docks_noticenav();
		docks_baitnav();
		blocknav("runmodule.php?module=docks&op=docks&op2=fishnotices");
	}
	if ($op2=="wanted"){
		output("`b`c`^Wanted`b`c`n`7");
		output("You decide to peruse the 'Wanted' Notices.`n`n`5`c");
		output("Wanted:  Used fish.  Age unimportant.  Must have eyes intact.  Please contact Witch Brunda.`c");
		output("`7`n`nMaybe Brunda should contact that Klodnio guy.");
		docks_noticenav();
		docks_baitnav();
		blocknav("runmodule.php?module=docks&op=docks&op2=fishnotices");
	}
	if ($op2=="jobavailable"){
		output("`b`c`^Jobs`b`c`n`7");
		output("You decide to peruse the 'Job' Notices.`n`n`5");
		if (is_module_active("jobs") && $op3=="1"){
			output("Job Opening at the Farm:  Please visit our Farm to apply.  We are looking for hard workers who will help supply the village with food.");
		}elseif ($op3=="1"){
			output("Job Opening at the Fish and Tackle Shop:  We are looking for people that will buy items from our store.  Please see Hoglin.");
			output("`n`n`7You glance over at Hoglin and smirk.  This seems like a pretty self-serving 'Job'!");
		}elseif ($op3=="2"){
			output("Job Opening at the Flat Earth Society:  We are looking for experienced sailors to help sail to edge of Earth.  Not responsible if we fall off.");
			output("`n`n`7You decide that that is NOT the kind of job you're looking for.");
		}elseif($op3=="3"){
			output("Job Description:  Wanted: Person with small fingers to pull cotton out of small bottles.");
			output("`n`n`7Does the adventure end? How boring is that? Could there be anything more boring???");
		}elseif ($op3=="4"){
			output("Job Description:  Wanted: Person with small fingers to put cotton into small bottles.");
			output("`n`n`7Suddenly, you feel like this may be a contender for the most boring job ever.");
		}elseif ($op3=="5"){
			output("Job Description:  Wanted: Nightcrawlers for bait shop.  If you find some in the forest, please see Hoglin.");
			output("`n`n`7You'll be sure to look for nightcrawlers in the forest.");
		}
		docks_noticenav();
		docks_baitnav();
		blocknav("runmodule.php?module=docks&op=docks&op2=jobsavailable&op3=$op3");
		blocknav("runmodule.php?module=docks&op=docks&op2=fishnotices");
	}
	if ($op2=="bigfish" || $op2=="fishweight" || $op2=="numberfish"){
		if ($op2=="bigfish") $title=translate_inline("Largest Fish Ever Caught");
		elseif ($op2=="fishweight") $title=translate_inline("Most Fish Caught by Weight");
		elseif ($op2=="numberfish") $title=translate_inline("Most Fish Caught by Number");
		output("`b`c`^%s`b`c`n`7",$title);
		$page = httpget('page');
		$pp = 40;
		$pageoffset = (int)$page;
		if ($pageoffset > 0) $pageoffset--;
		$pageoffset *= $pp;
		$limit = "LIMIT $pageoffset,$pp";
		if ($op2=="bigfish") $sql = "SELECT COUNT(*) AS c FROM " . db_prefix("module_userprefs") . " WHERE modulename = 'docks' AND setting = 'bigfish' AND value > 0";
		elseif ($op2=="fishweight") $sql = "SELECT COUNT(*) AS c FROM " . db_prefix("module_userprefs") . " WHERE modulename = 'docks' AND setting = 'fishweight' AND value > 0";
		elseif ($op2=="numberfish") $sql = "SELECT COUNT(*) AS c FROM " . db_prefix("module_userprefs") . " WHERE modulename = 'docks' AND setting = 'numberfish' AND value > 0";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$total = $row['c'];
		$count = db_num_rows($result);
		if (($pageoffset + $pp) < $total){
			$cond = $pageoffset + $pp;
		}else{
			$cond = $total;
		}
		if ($op2=="bigfish") $sql = "SELECT ".db_prefix("module_userprefs").".value, ".db_prefix("accounts").".name, ".db_prefix("accounts").".acctid FROM " . db_prefix("module_userprefs") . "," . db_prefix("accounts") . " WHERE acctid = userid AND modulename = 'docks' AND setting = 'bigfish' AND value > 0 ORDER BY (value+0) DESC $limit";
		elseif ($op2=="fishweight") $sql = "SELECT ".db_prefix("module_userprefs").".value, ".db_prefix("accounts").".name, ".db_prefix("accounts").".acctid FROM " . db_prefix("module_userprefs") . "," . db_prefix("accounts") . " WHERE acctid = userid AND modulename = 'docks' AND setting = 'fishweight' AND value > 0 ORDER BY (value+0) DESC $limit";
		elseif ($op2=="numberfish") $sql = "SELECT ".db_prefix("module_userprefs").".value, ".db_prefix("accounts").".name, ".db_prefix("accounts").".acctid FROM " . db_prefix("module_userprefs") . "," . db_prefix("accounts") . " WHERE acctid = userid AND modulename = 'docks' AND setting = 'numberfish' AND value > 0 ORDER BY (value+0) DESC $limit";
		$result = db_query($sql);
		$rank = translate_inline("Rank");
		$name = translate_inline("Name");
		$none = translate_inline("No Fish Caught");
		if ($op2=="bigfish") $weight= translate_inline("Weight");
		else $weight= translate_inline("Total Weight");
		$numberfish = translate_inline("Number of Fish");
		rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
		if ($op2=="bigfish"){
			rawoutput("<tr class='trhead'><td>$rank</td><td>$name</td><td>$weight</td></tr>");
		}elseif ($op2=="fishweight" || $op2=="numberfish"){
			rawoutput("<tr class='trhead'><td>$rank</td><td>$name</td><td>$numberfish</td><td>$weight</td></tr>");
		}
		if (db_num_rows($result)==0) output_notl("<tr class='trlight'><td colspan='4' align='center'>`&$none`0</td></tr>",true);
		else{
			for($i = $pageoffset; $i < $cond && $count; $i++) {
				$row = db_fetch_assoc($result);
				$name=$row['name'];
				$value=$row['value'];
				$id=$row['acctid'];
				if ($name==$session['user']['name']){
					rawoutput("<tr class='trhilight'><td>");
				}else{
					rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td>");
				}
				$j=$i+1;
				output_notl("$j.");
				rawoutput("</td><td>");
				output_notl("`^%s`0",$name);
				if ($op2=="fishweight" || $op2=="numberfish"){
					rawoutput("</center></td><td><center>");
					if ($op2=="fishweight"){
						$numberfish=get_module_pref('numberfish','docks',$id);
						output_notl("`^%s",$numberfish);
					}else output_notl("`^%s",$value);
				}
				rawoutput("</td><td><align=right>");
				if ($op2=="fishweight" || $op2=="bigfish"){
					$pounds=floor($value/16);
					$ounces=$value-($pounds*16);
				}else{
					$fishweight=get_module_pref('fishweight','docks',$id);
					$pounds=floor($fishweight/16);
					$ounces=$fishweight-($pounds*16);
				}
				output("%s %s, %s %s",$pounds,translate_inline($pounds<>1?"pounds":"pound"),$ounces,translate_inline($ounces<>1?"ounces":"ounce"));
				rawoutput("</td></tr>");
			}
		}
		rawoutput("</table>");
		if ($total>$pp){
			addnav("Pages");
			for ($p=0;$p<$total;$p+=$pp){
				addnav(array("Page %s (%s-%s)", ($p/$pp+1), ($p+1), min($p+$pp,$total)), "runmodule.php?module=docks&op=docks&op2=$op2&op3=$op3&page=".($p/$pp+1));
			}
		}
		if ($op3=="hof"){
			page_header("Hall of Fame");
			addnav("Fishing");
			addnav("Biggest Fish Caught","runmodule.php?module=docks&op=docks&op2=bigfish&op3=hof");
			addnav("Most Fish by Weight","runmodule.php?module=docks&op=docks&op2=fishweight&op3=hof");
			addnav("Most Fish by Number","runmodule.php?module=docks&op=docks&op2=numberfish&op3=hof");
			blocknav("runmodule.php?module=docks&op=docks&op2=$op2&op3=hof");
			addnav("Return");
			addnav("Hall of Fame","hof.php");
		}else{
			docks_baitnav();
			blocknav("runmodule.php?module=docks&op=docks&op2=$op2");
		}
	}
	//Fishing
	if ($op2=="fishing"){
		output("`b`c`^Fishing Dock`b`c`7`n");
		output("You arrive at the end of the dock and notice several people fishing here. There's a big sign outlining the rules of fishing off the dock.`n`n");
		addnav("Dock Fishing");
		$fishingtoday=get_module_pref("fishingtoday");
		if ((get_module_pref("pole")==0 || get_module_pref("bait")==0) && $fishingtoday<5){
			if (get_module_pref("pole")=="") output("You pull out a piece of string and tie it to stick");
			else output("You pull out your fishing pole and get ready to cast your empty hook");
			output("and the rest of the fishermen on the dock start laughing and pointing at you. One of the oldest anglers of the bunch wanders over to you and puts his arm around your shoulder.");
			output("`n`n`3'%s, I've been fishing these docks for longer than you've been walking this kingdom.  There have been many truths and many lies I've heard.",translate_inline($session['user']['sex']?"Missy":"Sonny"));
			output("I've seen a man catch an octopus as large as a boat.  I swear this on my last worm.  I've heard a man claim to have caught the greatest fish in the seas- `qCaptain Crouton`3. That's a big fish tale, I promise you. However, there's one thing I've never seen. I've never seen anyone catch a fish");
			if (get_module_pref("pole")=="" || get_module_pref("pole")==0) output("with a stick and a string.");
			else output("without any bait.");
			output("You'll need to head on over to the 'ole bait shop.'");			
		}elseif ($fishingtoday<5){
			$fishingleft=5-$fishingtoday;
			output("Ready to go fishing? You have enough bait for `^%s `7more %s.",$fishingleft,translate_inline($fishingleft>1?"casts":"cast"));
			addnav("Go Fishing","runmodule.php?module=docks&op=docks&op2=godockfishing");
		}else{
			output("You've done enough fishing for today.");
		}
		addnav("Read Rules","runmodule.php?module=docks&op=docks&op2=fishingrules");
		addnav("Chat with Fishermen","runmodule.php?module=docks&op=docks&op2=fishingchat");
		addnav("Docks");
		addnav("Return to the Docks","runmodule.php?module=docks&op=docks&op2=enter");
	}
	if ($op2=="fishingchat"){
		$person=e_rand(0,5);
		$fishername=translate_inline(array("`%Wendall`7","`\$Earl`7","`QFisherman Pete`7","`^Ole Opie`7","`@Gordon`7","`!Admiral Adam`7"));
		$color=array("`%","`\$","`Q","`^","`@","`!");
		output("`b`c`^Fishing Dock`b`c`7`n");
		output("You wander over to one of the fishermen on the dock and try to strike up a casual conversation.");
		output("You introduce yourself and find yourself talking to %s.",$fishername[$person]);
		output("The best you can come up with for conversation is `#'How are the fish biting today?'%s`n`n",$color[$person]);
		switch(e_rand(1,10)){
			case 1:
				output("'I haven't caught a fish from this dock in 3 years.  Anything else you want to know??'");
				output("`n`n`7Hmm.  Maybe he's not the best person to talk to today.");
			break;
			case 2:
				output("'Have you caught `q'Captain Crouton'%s, the biggest fish in the sea??'",$color[$person]);
				output("`n`n`7You dream whistfully of catching the monster fish. `#'Err, no, I haven't caught him yet,'`7 you respond.");
			break;
			case 3:
				output("'Yer not going to get any experience fishing by yabbering all day.'");
				output("`n`n`7You look at the decrepit form of the 'ole salt and think that maybe dedicating your life to fishing isn't all it's cracked up to be.");
			break;
			case 4:
				output("'I once caught a fish bigger than this dock once, but it got away.'");
				output("`n`n`7All the other fishermen at the dock laugh at %s's boast.  You nod and smile politely.",$fishername[$person]);
			break;
			case 5:
				output("'Why are you fishing off the dock? You know, the real fish are caught on the open sea.'");
				output("`n`n`7Ah, the open sea.  That's where you want to fish!");
			break;
			case 6:
				output("'Be careful how you cast your line.  Some of the fishermen on this dock can get pretty mean if you screw around.'");
				output("`n`n`7You make a mental note: Don't screw around too much!");
			break;
			case 7:
				output("'I don't read much, but I suppose if I did I wouldn't have time for all this fishing I need to do.'");
				output("`n`n`7Sometimes old fishermen just don't make sense.");
			break;
			case 8:
				output("'The funniest thing I ever saw on this dock was when a young angler about your size got pulled into the sea by a fish. HA!'");
				output("`n`n`7For some reason you don't think that sounds very funny.");
			break;
			case 9:
				output("'GO AWAY! I'm busy fishing!'");
				output("`n`n`7Cranky ole coot!");
			break;
			case 10:
				output("'Don't forget to weigh your fish. It's about honor!'");
				output("`n`n`7You imagine all the high honor you can get for catching a fish. Wow!");
			break;
		}
		$fishingtoday=get_module_pref("fishingtoday");
		addnav("Fishing Dock");
		addnav("Chat Some More","runmodule.php?module=docks&op=docks&op2=fishingchat");
		if ($fishingtoday<5 && get_module_pref("pole")>0 && get_module_pref("bait")>0) addnav("Go Fishing","runmodule.php?module=docks&op=docks&op2=godockfishing");
		addnav("Read the Rules","runmodule.php?module=docks&op=docks&op2=fishingrules");
		addnav("Docks");
		addnav("Return to the Docks","runmodule.php?module=docks&op=docks&op2=enter");
	}
	if ($op2=="fishingrules"){
		$dockfish=get_module_setting("dockfish");
		$dockfisher=get_module_setting("dockfishangler");
		$pounds=floor($dockfish/16);
		$ounces=$dockfish-($pounds*16);
		rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
		rawoutput("<tr class='trhead'><td>");
		output("`c`bWelcome to the `&Fishing Dock`c`b");
		rawoutput("</td></tr>");
		rawoutput("<tr class='trhilight'><td>");
		if ($dockfish>0){
			output("`c`n`^Largest Fish Ever Caught of the Docks:`n");
			if ($pounds>0) output("`&%s %s%s",$pounds,translate_inline($pounds>1?"Pounds":"Pound"),translate_inline($ounces>0?",":""));
			if ($ounces>0) output("`&%s %s",$ounces,translate_inline($ounces>1?"Ounces":"Ounce"));
			output("`n`^Angler who Caught the Fish:`n");
			output("`&%s`c",$dockfisher);
		}else output("`c`^No Fish caught at Dock Yet`c");
		output("`n`n`&`cPlease obey the following rules for fishing off the dock:`c");
		output("`n`&1. This is CATCH AND RELEASE ONLY! You may weigh your fish after catching it for documentation purposes but you must release the fish after weighing it.");
		output("`n2. No swimming off the dock");
		output("`n3. Please use proper equipment when fishing");
		output("`n4. Do not monopolize the dock.  Fishing is usually limited to no more than `^5 casts`& per day.`n`n");
		rawoutput("</td></tr>");
		rawoutput("</table>");
		$fishingtoday=get_module_pref("fishingtoday");
		addnav("Fishing Dock");
		if ($fishingtoday<5 && get_module_pref("pole")>0 && get_module_pref("bait")>0) addnav("Go Fishing","runmodule.php?module=docks&op=docks&op2=godockfishing");
		addnav("Chat with Fishermen","runmodule.php?module=docks&op=docks&op2=fishingchat");
		addnav("Docks");
		addnav("Return to the Docks","runmodule.php?module=docks&op=docks&op2=enter");
	}
	if ($op2=="godockfishing"){
		output("`b`c`^Fishing Dock`b`c`7`n");
		output("You cast your line.");
		increment_module_pref("fishingtoday",1);
		$weight=0;
		switch(e_rand(1,50)){
		//switch(45){
			case 1: case 2: case 3: case 4: case 5: case 6: case 7: case 8: case 9: case 10:
				output("You reel it in and find nothing attached. Looks like you've wasted a nightcrawler.");
			break;
			case 11: case 12: case 13: case 14: case 15: case 16: case 17: case 18: case 19: case 20:
				//average 8
				output("You reel it in and find you've caught a fish! You show your catch to the other anglers on the dock and they laugh, calling your catch a 'cute little guppy'.");
				$weight=e_rand(4,12);
			break;
			case 21: case 22: case 23: case 24: case 25: case 26: case 27: case 28:
				//average 15
				output("You reel it in and find you've caught a fish! You show your catch to the other anglers on the dock and they smile politely at your minnow.");
				$weight=e_rand(10,20);
			break;
			case 27: case 28: case 29: case 30: case 31: case 32:
				//average 24
				output("You reel it in and find you've caught a nice fish! You show your catch to the other anglers on the dock and they give you a thumbs up!");
				$weight=e_rand(16,32);
			break;
			case 33: case 34: case 35: case 36:
				//average 32
				output("You reel it in and find you've caught a decent fish! You show your catch to the other anglers on the dock and they give you a nod of congratulations.");
				$weight=e_rand(25,40);
			break;
			case 37: case 38:
				//average 41
				output("You reel it in and find you've caught an acceptable fish! You show your catch to the other anglers on the dock and they smile at the catch, maybe a little envious!");
				$weight=e_rand(32,50);
			break;
			case 39:
				//average 50 or Bigger!
				$rand=e_rand(1,7);
				if ($rand<7){
					output("You reel it in and pull in quite a nice fish! You proudly display your fish to everyone.  Nice Catch! You gain a turn from your adrenaline.");
					$session['user']['turns']++;
					$weight=e_rand(40,60);
				}else{
					$rand2=e_rand(1,10);
					if ($rand2<10){
						output("You reel in a great fish from the dock! You feel the envious eyes of the other anglers. You gain 2 turns from your adrenaline!");
						$session['user']['turns']+=2;
						$weight=e_rand(61,70);
					}else{
						$rand3=e_rand(1,12);
						if ($rand3<12){
							output("You reel in one of the biggest fish ever caught on the dock! It's quite a catch! You gain 3 turns from your adrenaline rush!");
							$session['user']['turns']+=3;
							$weight=e_rand(71,80);
						}else{
							output("You catch a fish so big you're almost pulled into the sea! It's amazing! One in a million! The other anglers on the dock come over and admire your fish. You gain 4 turns form your adrenaline rush!");
							$session['user']['turns']+=4;
							$weight=e_rand(81,90);
							if ($weight==90){
								$rand4=e_rand(1,50);
								if ($rand4<10) $weight=91;
								elseif ($rand4<19) $weight=92;
								elseif ($rand4<27) $weight=93;
								elseif ($rand4<34) $weight=94;
								elseif ($rand4<39) $weight=95;
								elseif ($rand4<43) $weight=96;
								elseif ($rand4<46) $weight=97;
								elseif ($rand4<48) $weight=98;
								elseif ($rand4<50) $weight=99;
								else $weight=100;
							}
						}
					}
				}
			break;
			case 40:
				$rand=e_rand(1,3);
				output("It hooks on one of the other fishermen!");
				if ($rand<3){
					output("You go over to him and yank it out and he smiles thankfully. It looks like you lost your worm!");
				}else{
					output("He comes up to you and you notice that he isn't smiling. I don't think you're getting your worm back.");
					if (is_module_active("lumberyard") || is_module_active("quarry") || is_module_active("metalmine")){
						output("You suddenly recognize him from");
						if (is_module_active("lumberyard")) output("`@the lumberyard...");
						if (is_module_active("quarry")) output("`%the quarry...");
						if (is_module_active("metalmine")) output("`)the metal mine...");
						output("`7Oh my! Why do you keep picking on this guy??");
					}
					output("`n`nLooks like you're going to have to fight him.");
					addnav("Fisherman Fight!","runmodule.php?module=docks&op=fishermanfight");
					blocknav("runmodule.php?module=docks&op=docks&op2=godockfishing");
					blocknav("runmodule.php?module=docks&op=docks&op2=fishingrules");
					blocknav("runmodule.php?module=docks&op=docks&op2=fishingchat");
					blocknav("runmodule.php?module=docks&op=docks&op2=enter");
				}
			break;
			case 41:
				output("You reel back in an old boot.  Garbage!");
			break;
			case 42:
				output("You reel back in a toilet seat.  Garbage!");
			break;
			case 43:
				output("You reel back in a dead rat.  Ewwww!");
			break;
			case 44:
				output("You reel back in a dead fish.  How did that got onto your hook? You pull off the fish and find it's a magic fish!");
				output("You start talking to it and realize it may be a magic fish, but it's still dead.  All the other fishermen look at you and snicker.");
				output("`n`nYou lose a `&charm`7.");
				$session['user']['charm']--;
			break;
			case 45:
				output("You reel back a magic fish! Yay!");
				output("The fish looks at you with a twinkle... `4'If you grant me my freedom, I will give you one wish.'");
				addnav("Ask for Wish","runmodule.php?module=docks&op=docks&op2=wishfish");
				addnav("Weigh and Gut the Fish","runmodule.php?module=docks&op=docks&op2=weighgut");
				blocknav("runmodule.php?module=docks&op=docks&op2=godockfishing");
				blocknav("runmodule.php?module=docks&op=docks&op2=fishingrules");
				blocknav("runmodule.php?module=docks&op=docks&op2=fishingchat");
				blocknav("runmodule.php?module=docks&op=docks&op2=enter");
			break;
			case 46:
				output("You reel back in a gold piece!");
				$session['user']['gold']++;
			break;
			case 47:
				output("You feel a huge fish on your line! You fight to reel it in but you're pulled into the water...");
				output("`n`nBy the time you dry off and the rest of the fishermen stop laughing, you realize you've lost 2 charm.");
				$session['user']['charm']++;
			break;
			case 48:
				output("You feel a hook of another fishermen catch on your hand. `\$Ouch!!!`7");
				if ($session['user']['hitpoints']>5){
					$session['user']['hitpoints']-=4;
					output("You lose 4 hitpoints!");
				}else output("Luckily you remove the hook without any injury.");
			break;
				output("You notice your worm falling off your hook.  Oh well.");
			break;
			case 49:
				$rand=e_rand(1,3);
				if ($rand==1){
					output("Your pole slips from your and and floats away.  Looks like you'll need to buy a new fishing pole.");
					set_module_pref("pole",0);
					blocknav("runmodule.php?module=docks&op=docks&op2=godockfishing");
				}else{
					output("Your pole slips from your hands but you grasp it desperately.  Phew! You almost lost your pole!");
				}
			break;
			case 50:
				output("You hook a seagull! Eww!");
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
			if ($weight>get_module_setting("dockfish")){
				output("`n`nYou've caught the biggest fish on the dock! Your name will be immortalized on the dock...");
				if ($weight<100) output("at least until someone else catches a bigger one.");
				else{
					output("You have caught the biggest fish that can be caught at the dock; a one in 2 million chance to happen!");
					output("You gain 10 extra turns for such an amazing accomplishment!");
					$session['user']['turns']+=10;
				}
				set_module_setting("dockfish",$weight);
				set_module_setting("dockfishangler",$session['user']['name']);
			}
		}
		addnav("Dock Fishing");
		$fishingtoday=get_module_pref("fishingtoday");
		if ($fishingtoday<5){
			$fishingleft=5-$fishingtoday;
			addnav("More Fishing","runmodule.php?module=docks&op=docks&op2=godockfishing");
			output("`n`n`c`@Fishing Turns Left: `^%s`c",$fishingleft);
		}else{
			output("`n`n`c`\$No Fishing Turns Left`7`c");
			set_module_pref("bait",0);
		}
		addnav("Read Rules","runmodule.php?module=docks&op=docks&op2=fishingrules");
		addnav("Chat with Fishermen","runmodule.php?module=docks&op=docks&op2=fishingchat");
		addnav("Docks");
		addnav("Return to the Docks","runmodule.php?module=docks&op=docks&op2=enter");
	}
	if ($op2=="wishfish"){
		output("`b`c`^Wishing Fish`b`c`7`n");
		output("You start to name your wish when suddenly the fish stops you.  `4'No no no.  I'm sorry, I forgot to specify.  I can grant any wish you want as long as you wish for a `%gem`.'`7");
		output("`n`nYou shrug and take the `%gem`7.");
		$session['user']['gems']++;
		if (get_module_pref("fishingtoday")<5) addnav("More Fishing","runmodule.php?module=docks&op=docks&op2=godockfishing");
		addnav("Return to the Docks","runmodule.php?module=docks&op=docks&op2=enter");
	}
	if ($op2=="weighgut"){
		output("`b`c`^Wishing Fish`b`c`7`n");
		output("Figuring that the talking fish is probably a liar, you bring him over to the scale.");
		$weight=e_rand(32,50);
		$pounds=floor($weight/16);
		$ounces=$weight-($pounds*16);
		output("You check the weight:`n`n`&");
		output("%s %s%s",$pounds,translate_inline($pounds>1?"Pounds":"Pound"),translate_inline($ounces>0?",":""));
		if ($ounces>0) output("%s %s",$ounces,translate_inline($ounces>1?"Ounces":"Ounce"));
		increment_module_pref("numberfish",1);
		increment_module_pref("fishweight",$weight);
		if ($weight>get_module_pref("bigfish")){
			output("`n`nThis is the biggest fish you've ever caught!");
			set_module_pref("bigfish",$weight);
		}
		if ($weight>get_module_setting("dockfish")){
			output("`n`nYou've caught the biggest fish on the dock! Your name will be immortalized on the dock... at least until someone else catches a bigger one.");
			set_module_setting("dockfish",$weight);
			set_module_setting("dockfishangler",$session['user']['name']);
		}
		if (get_module_pref("fishingtoday")<5) addnav("More Fishing","runmodule.php?module=docks&op=docks&op2=godockfishing");
		addnav("Return to the Docks","runmodule.php?module=docks&op=docks&op2=enter");
	}
	//Getting onto the Corinth (Fishing Vessel)
	if ($op2=="corinth"){
		output("`b`c`^`iThe Corinth`i`b`c`n`7");
		output("You approach a sailor to ask about passage but realize that this isn't for the open sea but rather a fishing vessel. You ask about going on a fishing expedition and one of the sailors comes over to size you up.`n`n");
		if (get_module_pref("fishweight")>=get_module_setting("fishmin")){
			if (get_module_pref("fishingtoday")<=1){
				output("`3'Ah, we've heard of your fishing prowess.  Indeed, you're welcome to come with us on an expedition.'");
				if (get_module_pref("bait")==1 && get_module_pref("pole")==1){
					output("`n`n`7It will cost a `@forest turn`7 to go on an expedition though!");
					if ($session['user']['turns']>0){
						output("You'll be charged the turn as soon as you step on board.");
						output("`n`nThe captain mentions that there aren't many rules on the ship except regarding your catch.");
						output("`&'I get your fish.  You get anything else you catch. Sorry, that's the rules.'");
						output("`n`n`7Not really having much use for fish, you figure this will still be worth your time.");
						if (get_module_setting("interface")==0 && get_module_pref("user_interface")==0) addnav("Fishing Expedition","runmodule.php?module=docks&op=fishingexpedition&op3=payturn");
						else addnav("Fishing Expedition","runmodule.php?module=docks&op=fishingexpeditiona&op3=payturn");
					}else{
						output("It looks like you don't have the energy to go on an expedition right now.");
					}
				}else{
					output("`n`n'Unfortunately, we don't provide equipment.  Go stop at the Bait Shop and we can set sail.'");
				}
			}else{
				output("`3'Well, you'd be welcome to come fishing with us, but you can only go fishing once a day and can tell by your smell that you've already cast your line today.'");
			}
		}else{
			output("`3'Fishing on this vessel isn't for amateurs.  You have to know your way around a fishing pole before you can sail with us. Maybe you should go practice a little at the end of the dock first.'");
		}
		addnav("Return to the Docks","runmodule.php?module=docks&op=docks&op2=enter");
	}
}
?>