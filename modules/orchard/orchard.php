<?php
global $session;
$op = httpget('op');
$allprefs=unserialize(get_module_pref('allprefs'));
$seed=$allprefs['seed'];
$found=$allprefs['found'];
$tree=$allprefs['tree'];
$treegrowth=$allprefs['treegrowth'];
$dietreehit=$allprefs['dietreehit'];
$dieingtree=$allprefs['dieingtree'];
$bankkey=$allprefs['bankkey'];

if ($op=="superuser"){
	require_once("modules/allprefseditor.php");
	allprefseditor_search();
	page_header("Allprefs Editor");
	$subop=httpget('subop');
	$id=httpget('userid');
	addnav("Navigation");
	addnav("Return to the Grotto","superuser.php");
	villagenav();
	addnav("Edit user","user.php?op=edit&userid=$id");
	modulehook('allprefnavs');
	$allprefse=unserialize(get_module_pref('allprefs',"orchard",$id));
	if ($allprefse['treegrowth']=="") $allprefse['treegrowth']=0;
	if ($allprefse['dieingtree']=="")$allprefse['dieingtree']=0;
	if ($allprefse['monsterid']=="") $allprefse['monsterid']=0;
	if ($allprefse['monstername']=="") $allprefse['monstername']="";
	set_module_pref('allprefs',serialize($allprefse),'orchard',$id);
	if ($subop!='edit'){
		$allprefse=unserialize(get_module_pref('allprefs',"orchard",$id));
		$allprefse['seed']= httppost('seed');
		$allprefse['found']= httppost('found');
		$allprefse['tree']= httppost('tree');
		$allprefse['treegrowth']= httppost('treegrowth');
		$allprefse['dietreehit']= httppost('dietreehit');
		$allprefse['dieingtree']= httppost('dieingtree');
		$allprefse['monsterid']= httppost('monsterid');
		$allprefse['monsterlevel']= httppost('monsterlevel');
		$allprefse['monstername']= httppost('monstername');
		$allprefse['bankkey']= httppost('bankkey');
		$allprefse['mespiel']= httppost('mespiel');
		$allprefse['meplay']= httppost('meplay');
		$allprefse['menumb']= httppost('menumb');
		$allprefse['caspiel']= httppost('caspiel');
		$allprefse['caplay']= httppost('caplay');
		$allprefse['canumb']= httppost('canumb');
		$allprefse['bellyrub']= httppost('bellyrub');
		$allprefse['pegplay']= httppost('pegplay');
		$allprefse['dragonseedage']= httppost('dragonseedage');
		$allprefse['hadfruittoday']= httppost('hadfruittoday');
		set_module_pref('allprefs',serialize($allprefse),'orchard',$id);
		output("Allprefs Updated`n");
		$subop="edit";
	}
	if ($subop=="edit"){
		require_once("lib/showform.php");
		$form = array(
			"Fruit Orchard XL,title",
			"Be careful when editing these.  If you don't fully understand what they each do you could easily break the module for the user you're editing.,note",
			"seed"=>"The level of the seed the user is currently searching for,range,0,20,1",
			"found"=>"If the user has found their current seed this is it's level,range,0,20,1",
			"tree"=>"The level of the users best tree,range,0,20,1",
			"treegrowth"=>"The number of days left for the users current tree to finish growing:,int",
			"dietreehit"=>"Has player encountered the Fruit Tree Disease (FTD) event?,bool",
			"dieingtree"=>"If player has encountered FTD they need to spend this number of turns fixing their tree still:,int",
			"monsterid"=>"If the user is looking for a seed found on a dead monster this is that monster's ID:,int",
			"monsterlevel"=>"If the user is looking for a seed found on a dead monster this is that monster's level:,range,0,18,1",
			"monstername"=>"If the user is looking for a seed found on a dead monster this is that monster's name:,text",
			"bankkey"=>"Has player had an offer to chat about the bank key for the 11th seed?,enum,0,No,1,Yes,2,Purchased",
			"mespiel"=>"Has the player heard  MightyE's spiel for the 14th seed?,bool",
			"meplay"=>"Has the player played MightyE's game today?,bool",
			"menumb"=>"Number of times player played MightyE's game:,range,0,5,1",
			"caspiel"=>"Has the player heard  Crazy Audrey's spiel for the 16th seed?,bool",
			"caplay"=>"Has the player played Crazy Audrey's game today?,bool",
			"canumb"=>"Number of times player played Crazy Audrey's game:,range,0,11,1",
			"bellyrub"=>"Number of bird's bellies that player has rubbed looking for the 17th seed:,range,0,3,1",
			"pegplay"=>"Has the player listened to Pegasus tell her story today for the 18th seed?,bool",
			"dragonseedage"=>"How old is the Dragon Seed?,enum,0,Not Available,1,1st Generation,2,2nd Generation,3,Ready to Collect",
			"hadfruittoday"=>"Has the user eaten fruit from their trees today?,bool",
		);
		$allprefse=unserialize(get_module_pref('allprefs',"orchard",$id));
		rawoutput("<form action='runmodule.php?module=orchard&op=superuser&userid=$id' method='POST'>");
		showform($form,$allprefse,true);
		$click = translate_inline("Save");
		rawoutput("<input id='bsave' type='submit' class='button' value='$click'>");
		rawoutput("</form>");
		addnav("","runmodule.php?module=orchard&op=superuser&userid=$id");
	}
}
if ($op==""){
	page_header("The Fruit Orchard");
	addnav("Enter the Hollow Tree","runmodule.php?module=orchard&op=hollowtree");
	addnav("Explore the orchard","runmodule.php?module=orchard&op=explore");
	if ($tree>0 || $treegrowth>0) addnav("Visit your trees","runmodule.php?module=orchard&op=trees");
	villagenav();
	output("`7You stroll into the orchard, it's a picturesque area filled with a large variety of different fruit trees, many of the fruits look ripe and ready to be picked.");
	output("You could easily spend hours just relaxing in here.`n`n");
	output("To the right of the entrance is a small path leading up to the hollow tree, the house of `!Elendir`7, keeper of the orchard.`n`n");
	output("It looks as though he might be home.");
	modulehook ("orchard-entrance");   
}
if ($op=="explore"){
	page_header("The Fruit Orchard");
	addnav("Return to the entrance","runmodule.php?module=orchard");
	output("`7Wandering through the orchard between the many trees planted by different villagers, occasionally you meet someone else and wave.");
	output("It's a beautiful place to spend an afternoon.`n`n");
	output("`c`b`^The best trees in the orchard:`b`c`n");
	require_once("modules/orchard/orchard_treelist.php");
	orchard_treelist();
}
if ($op=="hollowtree"){
	page_header("The Hollow Tree");
	addnav("Return to the entrance","runmodule.php?module=orchard");
	if ($dietreehit==1 && $dieingtree>0){
		output("\"`#I'm so glad you were able to make it here so quickly.`7\"");
		if ($session['user']['turns']>=$dieingtree){
			output("`n`n\"`#We'll need to spend `@%s turn%s`# to even have a chance to save your tree. Are you ready?`7\"",$dieingtree,translate_inline($dieingtree>1?"s":""));
			addnav("Try to Save Your Tree","runmodule.php?module=orchard&op=savetree");
		}else{
			output("`n`n\"`#Unfortunately, you don't have enough time to help take care of your tree.  If you can't find the time before the new day, your tree is going to die.");
			output("You need `@%s turn%s`# to save your tree.`7\"",$dieingtree,translate_inline($dieingtree>1?"s":""));
		}
	}else{
		if ($bankkey==1) addnav("Ask about the Bank Key","runmodule.php?module=orchard&op=bankkey");
		if ($found>0) addnav("Show Elendir your seed","runmodule.php?module=orchard&op=giveseed");
		elseif ($tree<get_module_setting("treesinorchard")) addnav("Ask about tree seeds","runmodule.php?module=orchard&op=askseeds");
		if ($tree>0 || $treegrowth>0) addnav("Ask about your trees","runmodule.php?module=orchard&op=asktrees");
		output("`!Elendir`7 spots you as you are walking down the path and comes out to greet you.");
		if ($tree==0 && $seed==0){
			output("`n`n\"`#Hello there, you're a new face, have you looked around the orchard yet?  It's truly wonderful is it not.`7\" he says merrily.`n");
			output("\"`#Please, do come in and stay a while, I can tell you all about the orchard if you like.`7\"`n`n");
			output("Of course, you were heading to his house anyway and who can refuse an invitation from an elf, so you follow him inside.`n`n");
			output("\"`#OK, I'm sure you'll be wanting your own little spot and I believe theres still space for more trees.");
			output("Unfortunately it isn't quite that simple, I cannot help you plant trees unless you bring me seeds to plant them with.  And then of course they take time to grow, but I think you'll agree that it's worth the wait.`7\"`n");
			output("`!Elendir`7 smiles and waves his arms vaguely towards the window, where there is a superb view of the orchard.");
		}elseif ($tree==0){
			output("`n`n\"`#Hello there %s`#!  You are looking very well today, I wonder, have you found any seeds for me yet?`7\"",$session['user']['name']);
			output("He says, greeting you with a firm handshake.`n`n");
			output("\"`#Please, come inside and sit a while.`7\" `!Elendir`7 gestures you inside and you follow politely.`n`n");
			output("\"`#So, would you like me to tell you about the orchard?  Perhaps you think I know about some seeds you may be able to find?`7\"");
			output("He smiles at you knowingly and sits down opposite you.");
		}else{
			output("\"`#Hello there %s`#!  How are you today, you're trees seem to be doing very well for themselves, as do everyone elses.  What is it you would like today?`7\" he asks jovially, gesturing you inside.`n`n",$session['user']['name']);
			output("You follow him in and take a seat.`n`n");
			if ($tree<get_module_setting("treesinorchard")){
				output("\"`#I suppose you're interested in how your trees are doing?  and of course there are always more seeds to be found.`7\"");
			}else{
				output("\"`#My friend, you know as much about the trees in this orchard as I do, I'm not sure I can be of any more service to you.  Of course you are welcome to stay and relax here as long as you want.`7\"");
			}
		}
	modulehook ("orchard-hollowtree"); 
	}
}
if ($op=="savetree"){
	page_header("The Orchard");
	$session['user']['turns']-=$dieingtree;
	output("`7You work dilligently with `!Elendir`7 to save your tree.");
	output("`n`nTime slows to a standstill as you work.");
	if (e_rand(1,100)<=get_module_setting("treechance")){
		output("`n`nUnfortunately, You're unable to save the tree.  You sadly look down and `!Elendir`7 calls out the time of death.");
		$allprefs['bankkey']=0;
		$allprefs['mespiel']=0;
		$allprefs['menumb']=0;
		$allprefs['caspiel']=0;
		$allprefs['canumb']=0;
		$allprefs['bellyrub']=0;
		$allprefs['pegplay']=0;
		$allprefs['dragonseedage']=0;
		$allprefs['dietreehit']=0;
		$allprefs['monsterid']="";
		$allprefs['monsterlevel']="";
		$allprefs['monstername']="";
		if ($allprefs['seed']>0) $allprefs['seed']=$allprefs['seed']-1;
		if ($allprefs['found']>0) $allprefs['found']=$allprefs['found']-1;
		if ($allprefs['tree']>0) $allprefs['tree']=$allprefs['tree']-1;
	}else{
		if (get_module_setting("dryenable")==0) $allprefs['dietreehit']=0;
		output("`n`nHappily, you're able to save your tree!!! You thank `!Elendir`7 for all his help.");
	}
	$allprefs['dieingtree']=0;
	set_module_pref('allprefs',serialize($allprefs));
	addnav("Return to the entrance","runmodule.php?module=orchard");
	villagenav();
}
if ($op=="giveseed"){
	page_header("The Hollow Tree");
	addnav("Return to the entrance","runmodule.php?module=orchard");
	$names=translate_inline(array("","`\$Apple","`QOrange","`6Pear","`QApricot","`^Banana","`QPeach","`5Plum","`qFig","`^Mango","`\$Cherry","`QTangerine","`^Grapefruit","`^Lemon","`@Avocado","`2Lime","`\$Pomegranate","`qKiwi","`4Cranberry","`^Star Fruit","`@Dragon`\$fruit"));
	if ($allprefs['treegrowth']>0){
			output("`7\"`#If you recall, you still have a `@%s`# tree growing in the orchard, you need to wait for it to finish growing before you can plant another.`7\"",$names[($tree+1)]);
			if ($tree>0) addnav("Ask about your trees","runmodule.php?module=orchard&op=asktrees");
	}else{
		if ($tree>0) addnav("Pick some fruit","runmodule.php?module=orchard&op=pick");
		output("`7\"`#Ah, wonderful, the `@%s`# seed.  Come, lets head out into the orchard and get this planted shall we.`7\"`n`n",$names[$found]);
		output("You follow `!Elendir`7 out into the orchard to a small clearing and he asks for the `@%s`7 seed.",$names[$found]);
		output("`!Elendir`7 begins to dig a small hole to plant the seed in and starts chatting about the orchard.");
		output("You sit and listen whilst he plants the tree, keeping an eye on what he's doing.");
		output("After a short while the tree is planted and `!Elendir`7 stands up and smiles at you.`n`n");
		output("\"`#Thats going to be a fine tree when it's fully grown, trust me.  Just make sure you look after it.`7\"`n`n");
		output("\"`#Remember never to pick too much fruit from it, otherwise it will become unhealthy and possibly even die.`7\"`n`n");
		output("\"`#Oh, and it will take `^%s`# day%s for your `@%s tree`# to grow to full size and bear fruit for you to eat.  You must be patient.`7\"`n`n",get_module_setting("growth"),translate_inline(get_module_setting("growth")>1?"s":""),$names[$found]);
		output("With a final wave `!Elendir`7 heads back through the orchard, leaving you with the newly planted tree.");
		$allprefs['found']=0;
		$allprefs['treegrowth']=get_module_setting("growth");
		if ($tree>0){
			for($i=1;$i<=$tree;$i++){
				if ($trees == null) $trees = $names[$i];
				else $trees = $trees."`7,`^ ".$names[$i];
			}
		output("Looking around, you see that you have the following trees: `^%s`7.",$trees);
		}
		set_module_pref('allprefs',serialize($allprefs));
	}
}
if ($op=="askseeds"){
	require_once("modules/orchard/orchard_askseeds.php");
	orchard_askseeds();
}
if ($op=="asktrees"){
	page_header("The Hollow Tree");
	addnav("Return to the entrance","runmodule.php?module=orchard");
	$names=translate_inline(array("","`\$Apple","`QOrange","`6Pear","`QApricot","`^Banana","`QPeach","`5Plum","`qFig","`^Mango","`\$Cherry","`QTangerine","`^Grapefruit","`^Lemon","`@Avocado","`2Lime","`\$Pomegranate","`qKiwi","`4Cranberry","`^Star Fruit","`@Dragon`\$fruit"));
	if ($allprefs['found']>0) addnav("Show Elendir your seed","runmodule.php?module=orchard&op=giveseed");
	elseif ($tree<get_module_setting("treesinorchard")) addnav("Ask about tree seeds","runmodule.php?module=orchard&op=askseeds");
	output("`7\"`#Of course you can always go and look at your trees yourself, you're free to roam the orchard as you please.");
	output("But if you want my opinion on your trees then it is yours.`7\"`n`n");
	switch($tree){
		case 0:
			output("\"`#Sadly you have no fully grown trees in the orchard, but time will change that as I'm sure you know.`)\"");
		break;
		case 1:case 2:case 3:case 4: case 5: case 6: case 7:
			output("\"`#Well, I'm very pleased that you've taken an interest in the orchard, but you still have a lot to learn from me about trees.");
			output("I hope you maintain your interest, and your little section of my orchard grows.`7\"");
		break;
		case 8: case 9: case 10: case 11: case 12: case 13:
			output("\"`#I have to say you're doing very well, there are still a few things I have to show you though, but I'm sure you'll pick them up just fine.`7\"");
		break;
		case 14: case 15: case 16: case 17: case 18: case 19:
			output("\"`#Well, your skills in the orchard almost match my own.  You should be very proud of what you have achieved here.`7\"");
		break;
		case 20:
			output("\"`#We are like brothers, our knowledge of the orchard is equal and unrivalled.`7\"");
		break;
	}
	if ($treegrowth>0) output("`n`n\"`#No doubt you're also wondering about the `@%s`# tree that we planted not long ago, well it still has `^%s`# more day%s before it will be fully grown.`7\"",$names[$tree+1],$treegrowth,translate_inline($treegrowth>1?"s":""));
}
if ($op=="trees"){
	page_header("The Fruit Orchard");
	$names=translate_inline(array("","`\$Apple","`QOrange","`6Pear","`QApricot","`^Banana","`QPeach","`5Plum","`qFig","`^Mango","`\$Cherry","`QTangerine","`^Grapefruit","`^Lemon","`@Avocado","`2Lime","`\$Pomegranate","`qKiwi","`4Cranberry","`^Star Fruit","`@Dragon`\$fruit"));
	addnav("Return to the entrance","runmodule.php?module=orchard");
	if ($tree>0) addnav("Pick some fruit","runmodule.php?module=orchard&op=pick");
	output("`7You saunter through the orchard to a familiar area, you think back to the proud moment when `!Elendir`7 helped you to plant your very own trees in this spot.");
	output("Some of the fruit on the more mature trees looks wonderfully ripe, you could so easily reach out and pick some and eat it right now.`n`n");
	if ($tree>0){
		for($i=1;$i<=$allprefs['tree'];$i++){
			if (!isset($trees)) $trees = $names[$i];
			else $trees = $trees."`7,`^ ".$names[$i];
		}
		output("Looking around, you see that you have the following trees: `^%s`7.`n`n",$trees);
	}
	if ($treegrowth>0)output("You note that the tree you and `!Elendir`7 planted is still not fully grown, perhaps `!Elendir`7 could give you an idea of how much longer it will take.");
}
if ($op=="pick"){
	page_header("The Fruit Orchard");
	addnav("Go back to your trees","runmodule.php?module=orchard&op=trees");
	addnav("Return to the entrance","runmodule.php?module=orchard");
	if ($allprefs['dietreehit']==1 && $allprefs['dieingtree']>0){
		output("As you grab the fruit, you realize there's something wrong with it.");
		output("Perhaps you better talk to `!Elendir`7 before you eat that piece of fruit!");
	}elseif ($allprefs['hadfruittoday']==1) output("`7You reach out to pick some fruit, but remember what `!Elendir`7 told you, only one piece of fruit per day or the trees may grow unhealthy.");
	else{
		$names=translate_inline(array("","`\$Apple","`QOrange","`6Pear","`QApricot","`^Banana","`QPeach","`5Plum","`qFig","`^Mango","`\$Cherry","`QTangerine","`^Grapefruit","`^Lemon","`@Avocado","`2Lime","`\$Pomegranate","`qKiwi","`4Cranberry","`^Star Fruit","`@Dragon`\$fruit"));
		$level=$allprefs['tree'];
		$tree=$names[$level];
		output("`7You reach out and pick a ripe `@%s`7 from a nearby tree and munch on it happily.`n`n",$tree);
		output("You feel much healthier for it, and gain `^%s`7 health!",$level*get_module_setting("healfruit"));
		$session['user']['hitpoints']+=$level*get_module_setting("healfruit");
		$allprefs['hadfruittoday']=1;
		set_module_pref('allprefs',serialize($allprefs));
	}
}
// everything after here is interaction with other modules
if ($op=="starseedbuy"){
	page_header("Hunter's Lodge");
	addnav("L?Return to the Lodge","lodge.php");
	$seedcost=get_module_setting("lodgeseed");
	$pointsavailable = $session['user']['donation'] - $session['user']['donationspent'];
	output("`7J. C. Petersen smiles at you, \"`&So, you're interested in purchasing a Star Fruit Seed.`7\"`n");
	if ($pointsavailable < $seedcost){
		output("`nHe consults his book silently for a moment and then turns to you. \"`&I'm terribly sorry, but you only have %s points available.`7\"`n", $pointsavailable);
	}else{
		output("`n\"`&This very rare seed will cost %s points.`7\"`n`n",$seedcost);
		addnav("Buy Star Fruit Seed","runmodule.php?module=orchard&op=buylseed");
	}
}
if ($op=="buylseed"){
	page_header("Hunter's Lodge");
	$seedcost=get_module_setting("lodgeseed");
	$session['user']['donationspent'] += $seedcost;
	debuglog ("spent $seedcost lodge points buying the Star Fruit Seed.");
	output("J. C. Petersen nods and hopes you enjoy your Star Fruit Seed.");
	require_once("modules/orchard/orchard_func.php");
	orchard_findseed();
	addnav("L?Return to the Lodge","lodge.php");
}
if ($op=="pegcran"){
	page_header("Pegasus");
	output("`c`b`%Pegasus Armor`0`c`b");
	if ($allprefs['pegplay']==0){
		output("`5After admiring the amazing armor, you look up at `#Pegasus`5 and ask if she would be willing to give you a cranberry seed.");
		output("`n`n`5\"`@I'm glad you asked about my cranberry tree.  If you have a moment, I'd like to share a story with you.`5\"");
		addnav("Listen to her story","runmodule.php?module=orchard&op=pegstory");
	}else{
		output("`#Pegasus`5 ignores you. Perhaps she'll chat some more with you tomorrow.");
	}
	addnav("Look at Armor","armor.php");
}
if ($op=="pegstory"){
	page_header("Pegasus");
	output("`c`b`%Pegasus Armor`0`c`b");
	if ($session['user']['turns']<4){
		output("`5\"`@Oh, I'm so sorry.  It will probably take about 4 turns to hear the whole story. Stop back when you have more time.");
		villagenav();
	}else{
		$allprefs['pegplay']=1;
		$story=e_rand(1,10);
		$session['user']['turns']-=4;
		$allprefs['pegstory']=$story;
		$homechoice=translate_inline(array("","Parantray","Ofrenhaven","Gravenstaff","Ordenmarn","Priselton","Havermark","Traktrin","Garensow","Rikenham","Schantiln"));
		$artpiece=translate_inline(array("","duck","butterfly","wagon","helmet","unicorn","tree","flower","penguin","frog","turtle"));
		$months=translate_inline(array(0,36,37,38,39,40,41,42,43,44,45));
		output("`5\"`@I’m sure you’ve noticed that I have my shop set up inside of my wagon.");
		output("It’s true, I am one of the Gypsies of the %s province.",$homechoice[$story]);
		output("I remember growing up with my best friend `3Maritrina`@.  She was an amazing singer and we would often stay up late to sing harmonies with the world around us.");
		output("It was a wonderful way to grow up and I have such fond memories of my childhood.`5\"");
		output("`n`n`5\"`@So how did a young gypsy child learn to make the armor you see before you?");
		output("That's a story of turnabout, I guess.  I was a gypsy who grew tired of the nomadic ways of our people. I needed stability and I settled down and looked for something that I could do for a living.");
		output("I found that I loved creating pieces of art with my hands.  I started by working with glass.");
		output("The first sculpture I ever made was of a %s.  I admit it wasn't the most beautiful sculpture ever seen, but it was enough to spark a passion that has never left me.",$artpiece[$story]);
		output("Soon enough I turned my skills to molding and shaping metal.  It took me over %s months to make a suit of armor that was tournament worthy, but when I finished the suit the knight that wore it won the competition!`5\"",$months[$story]);
		output("`n`n`5\"`@My armor reflects my appreciation for nature.  And that's how my story comes around full circle to the Cranberry Tree. I have a feeling you can see why I do not trust my cranberry seeds to just anyone.`5\"");
		output("`n`nBy the time `#Pegasus`5 finishes her story, you realize that it's taken `@4 turns`5 to hear.");
		addnav("Continue","runmodule.php?module=orchard&op=pegcontinue");
		set_module_pref('allprefs',serialize($allprefs));
	}
}
if ($op=="pegcontinue"){
	page_header("Pegasus");
	output("`c`b`%Pegasus Armor`0`c`b");
	output("`5\"`@Before I give you the Cranberry Seed, you have to answer one little question for me about my story.`5\"`n`n");
	addnav("Answers");
	require_once("modules/orchard/orchard_peganswers.php");
	orchard_peganswers();
}
if (($op=="1"||$op=="2"||$op=="3"||$op=="4"||$op=="5"||$op=="6"||$op=="7"||$op=="8"||$op=="9"||$op=="10") && $op==$allprefs['pegstory']){
	page_header("Pegasus");
	output("`c`b`%Pegasus Armor`0`c`b");
	output("`5\"`@You are indeed worthy of one of my Cranberry Seeds.  Please take good care of it.`5\"");
	$allprefs['pegplay']=0;
	$allprefs['pegstory']=0;
	set_module_pref('allprefs',serialize($allprefs));
	require_once("modules/orchard/orchard_func.php");
	orchard_findseed();
	villagenav();
}elseif ($op=="1"||$op=="2"||$op=="3"||$op=="4"||$op=="5"||$op=="6"||$op=="7"||$op=="8"||$op=="9"||$op=="10"){
	page_header("Pegasus");
	output("`c`b`%Pegasus Armor`0`c`b");
	output("`5\"`%You weren't paying attention were you? I think you need to leave now.`5\"");
	output("`n`nShe gives you a glare and turns her attention to watching the swordsmith in the store next to her wagon. Maybe you can talk to her again tomorrow.");
	villagenav();
}
if ($op=="caseed"){
	page_header("Crazy Audrey");
	if ($allprefs['caspiel']==0){
		output("`5\"`%You're after my precious seed, aren't you?`5\"");
		output("`n`nTrying to compose yourself, you explain that you'd be willing to compensate her fairly for her pomegranate seed.");
		output("`n`n\"`%You think you can just take my precious seed, don't you?`5\"");
		output("`n`nOnce again you try to explain that money isn't a problem... you want to be fair.");
		output("`n`n\"`%NO NO  no no nononononono... Money isn't a problem.  But if you want my precious seed, you have to beat me.`5\"");
		output("`n`nAt this point, you have no qualms with beating her, but you have a feeling that's not what this is about.");
		output("`n`n\"`%It will be a staring contest.  And it will cost you `^1000 gold `%each time you play me.`5\"");
		output("`n`n`5Ready to stare down Crazy Audrey?");
		addnav("Stare at Crazy Audrey","runmodule.php?module=orchard&op=stareca");
		addnav("Run away from Crazy Audrey","runmodule.php?module=orchard&op=runca");
		$allprefs['caspiel']=1;
		set_module_pref('allprefs',serialize($allprefs));
	}elseif ($allprefs['caplay']==0){
		output("`5\"`%You think you can stare me down for my precious seed today?  If you've got `^1000`% gold, then bring it on!`5\"");
		output("`n`n`5Ready to stare down Crazy Audrey?");
		addnav("Stare at Crazy Audrey","runmodule.php?module=orchard&op=stareca");
		addnav("Run away from Crazy Audrey","runmodule.php?module=orchard&op=runca");
	}else{
		output("`5\"`%Come back to stare at my beautiful eyes already, have you? Well, I don't think you can handle so much beauty in one day. Come back tomorrow.`5\"");
		villagenav();
	}
}
if ($op=="runca"){
	page_header("Crazy Audrey");
	output("`5You run, very quickly, away from this mad woman.");
	villagenav();
}
if ($op=="stareca"){
	page_header("Crazy Audrey");
	if ($session['user']['gold']<1000) output("`5Not having `^1000`5 gold you wander sadly away.");
	elseif ($allprefs['canumb']<=1){
		$allprefs['caplay']=1;
		$allprefs['canumb']=$allprefs['canumb']+1;
		output("`5You hand over `^1000`5 gold and settle down for a staring contest with Crazy Audrey.");
		output("`n`nYou're doing quite well, when suddenly she throws a %s at you.",get_module_setting("animal","crazyaudrey"));
		output("\"`%HA HA ha ha hahahahaha!!! You lost! Come back tomorrow and maybe you can beat me then.`5\"");
		output("`n`nSomewhat dejected and defeated, you leave.  Oh, she'll learn her lesson tomorrow!");
		$session['user']['gold']-=1000;
		set_module_pref('allprefs',serialize($allprefs));
	}else{
		require_once("modules/orchard/orchard_audresults.php");
		orchard_audresults();
	}
	villagenav();
}
if ($op=="quarry"){
	page_header("The Quarry");
	if (is_module_active('lostruins') && get_module_setting("usequarry","quarry")==0) {
		output("`n`c`b`@T`3he %s `@Q`3uarry`c`b`n",get_module_setting("quarryfinder","quarry"));
	}else{
		output("`n`c`b`@T`3he `@Q`3uarry`c`b`n");
	}
	output("`0You look down and notice some limestone.");
	addnav("Investigate","runmodule.php?module=orchard&op=limestone");
}
if ($op=="limestone"){
	page_header("The Quarry");
	if (is_module_active('lostruins') && get_module_setting("usequarry","quarry")==0) {
		output("`n`c`b`@T`3he %s `@Q`3uarry`c`b`n",get_module_setting("quarryfinder","quarry"));
	}else{
		output("`n`c`b`@T`3he `@Q`3uarry`c`b`n");
	}
	addnav("V?(V) Return to Village","village.php");
	$allprefsquarry=unserialize(get_module_pref('allprefs','quarry'));
	if ($allprefsquarry["usedqts"]<get_module_setting("quarryturns","quarry")) addnav("Work the Quarry","runmodule.php?module=quarry&op=work");
	addnav("Office","runmodule.php?module=quarry&op=office");
	output("`0There's more here then just limestone.  It seems like....");
	output("`n`n`n`nWait for it...");
	output("`n`n`nIt's the Lime Seed!!!");
	require_once("modules/orchard/orchard_func.php");
	orchard_findseed();
}
if ($op=="megame"){
	page_header("MightyE's Weapons");
	output("`c`b`&MightyE's Weapons`0`c`b");
	if ($allprefs['mespiel']==0){
		output("A dagger whizzes by and just touches misses cutting your ear.");
		output("`n`n\"`#You want to know about avocados?  That `!Elendir`# has been shooting his mouth off again, hasn't he? Fair enough.`0\"");
		output("`n`n\"`#Okay. Here's the deal. I'll give you one chance to grab the avocado seed from my hand.`0\" He shows you the very seed you're looking for.");
		output("`\"`#If you succeed, you can have my avocado seed. If you fail, you'll get a nice little scar and a painful reminder that sometimes you have to pay a price to play.`0\"");
		output("`n`n`!MightyE`0 twirls a dagger in one hand and teases you with the avocado seed in the other.");
		$allprefs['mespiel']=1;
		set_module_pref('allprefs',serialize($allprefs));
		addnav("Play the Game","runmodule.php?module=orchard&op=playmegame");
	}elseif ($allprefs['meplay']==0){
		output("\"`#So, are you ready to play my game again? Remember, you try to grab the avocado seed from my hand. If you succeed, you can keep it.  If you fail, you get a little reminder to work harder.`0\"");
		addnav("Play the Game","runmodule.php?module=orchard&op=playmegame");
	}else{
		output("\"`#Don't be a fool. Come back tomorrow`0\"");
	}
	addnav("Slowly Back Away","weapons.php");
}
if ($op=="playmegame"){
	require_once("modules/orchard/orchard_megame.php");
	orchard_megame();
}
if ($op=="lumberyard"){
	page_header("Lumber Yard");
	output("`n`c`b`QT`qhe `QL`qumber `QY`qard `QP`qhase `Q1`0`c`b`n");
	output("`^You're about to take a swing at the tree when you suddenly stop.  What is that little white rock by your feet?");
	output("`n`nYou take a closer look and notice something... It's not a rock! That's a lemon seed!");
	require_once("modules/orchard/orchard_func.php");
	orchard_findseed();
	output("`^You decide to get back to work chopping that tree down!`n`n");
	output("You've completed `QPhase 1`^ of work in the lumber yard.");
	output("It only took you `@one turn`^.`n`n");
	addnav("`@To the forest","forest.php");
	addnav("Cut More Trees","runmodule.php?module=lumberyard&op=work");
	addnav("Plant Trees","runmodule.php?module=lumberyard&op=planttree");
	addnav("`@T`7he `@F`7oreman's `@O`7ffice","runmodule.php?module=lumberyard&op=office");
}
if ($op=="lastmeal"){
	$sheriff = get_module_setting("sheriffname","jail");
	page_header(array("The jail of %s", $session['user']['location']));
	output("You tell the sheriff to come over to your cell and order a `#'Last Meal'`0.`n`n");
	output("%s`0 gladly comes back in a little while carrying a beautiful grapefruit.",$sheriff);
	output("`n`nAfter gobbling down the grapefruit, you grab one of the seeds and slip it in your pocket.");
	output("Now all you have to do is wait for the next day and you'll be free!");
	require_once("modules/orchard/orchard_func.php");
	orchard_findseed();
	addnav("Continue","runmodule.php?module=orchard&op=returnjail");
}
if ($op=="returnjail"){
	$sheriff = get_module_setting("sheriffname","jail");
	page_header(array("The Jail of %s", $session['user']['location']));
	output("%s comes back and takes your plate away and leads you out of the jail cell.",$sheriff);
	output("You are a bit confused by this.`n`n`#'What is going on?'`0 you enquire.");
	output("`n`n`@'We're going to a hangin'!'`0 replies the Sheriff.");
	output("`n`n`0This seems like a pleasant diversion, so you enquire who is being executed.");
	output("`n`nWith a sly grin, %s`0 responds `@'Why, after such a pleasant last meal, it's clearly you!'",$sheriff);
	addnav("Get Executed","runmodule.php?module=orchard&op=execution");
}
if ($op=="execution"){
	page_header("Hanging Gallows");
	output("Soon enough, there's a rope around your neck and a crowd gathering to watch.");
	output("`n`nYou should be getting nervous at this point.");
	output("`n`nThe trap door is released and you close your eyes.");
	addnav("Shades","runmodule.php?module=orchard&op=jshades");
}
if ($op=="jshades"){
	page_header("Hanging Gallows");
	output("You fall to the ground... it seems that someone was using some cheap rope and it broke.");
	output("`n`nWell, that's a relief.  You're pardoned and sent on your merry way.  You suddenly remember something in your pocket.");
	addnews("%s `@escaped from a hanging. That's luck for you.",$session['user']['name']);
	set_module_pref("injail",0,"jail");
	villagenav();
}
if ($op=="bankkey"){
	page_header("The Fruit Orchard");
	$subop = httpget('subop');
	if ($subop==""){
		output("`!Elendir`7 walks you back to his office and starts to search through some drawers.");
		output("\"`#Ah, yes, here it is.  Well, I'm not making any guarantees about this, but I'd be willing to sell this to you for a price.\"");
		output("`n`n`7You pause, a little concerned about how high the price may be.`n`n");
		output("\"`#Only `^12,000 gold`# and `%12 gems`#. Isn't that a great price?\"");
		output("`n`n`7He gives you a knowing grin, sensing that you may actually be interested.");
		addnav("Buy Bank Key","runmodule.php?module=orchard&op=bankkey&subop=buy");
	}elseif ($subop="buy"){
		if ($session['user']['gold']>=12000 && $session['user']['gems']>=12){
			output("`!Elendir`7 takes your `^gold`7 and `%gems`7 and hands you a small key in exchange.");
			$session['user']['gold']-=12000;
			$session['user']['gems']-=12;
			$allprefs['bankkey']=2;
			set_module_pref('allprefs',serialize($allprefs));
		}else{
			if ($session['user']['gold']>=12000)output("`!Elendir`7 stares at you blankly.  \"`#You don't have that many gems, `bgo get some more gems!`b`7\" he says.");
			elseif ($session['user']['gems']>=12) output("`!Elendir `7stares at you blankly.  \"`#You don't have enough gold, `bgo get some more gold!`b`7\" he says.");
			else output("`!Elendir `7stares at you blankly.  \"`#You don't have enough gold or gems, `bgo get some more!`b`7\" he says.");
		}
	}
	addnav("Return to the entrance","runmodule.php?module=orchard");
}
if ($op=="bank"){
	page_header("Ye Olde Bank");
	output("`6You show `@Elessa`6 your bank key and she leads you to the back room where the locked boxes are kept.");
	output("`n`nYou match up your key with the correct box and open it.");
	output("`n`nThere's nothing there! Curse that `!Elendir`6!! You shake the box and then a tiny little seed falls out.");
	output("`n`nExcellent!`n");
	require_once("modules/orchard/orchard_func.php");
	orchard_findseed();
	addnav("Return to the Front of the Bank","bank.php");
	$allprefs=unserialize(get_module_pref('allprefs'));
	$allprefs['bankkey']=0;
	set_module_pref('allprefs',serialize($allprefs));
	villagenav();
}
if ($op=="cedrick"){
	$subop = httpget('subop');
	$iname = getsetting("innname", LOCATION_INN);
	page_header($iname);
	rawoutput("<span style='color: #9900FF'>",true);
	output_notl("`c`b$iname`b`c");
	if ($subop==""){
		output("\"`%You're looking for seeds, are ya?`0\" Cedrik asks.  \"`%Well I guess I could part with my prized Apricot seed, but it'll cost ya. `^10 gems`% and `^10,000 gold`%!`0\"`n`n");
		output("Will you buy the seed from Cedrick?");
		addnav("Buy Apricot seed","runmodule.php?module=orchard&op=cedrick&subop=buy");
	}elseif ($subop="buy"){
		if ($session['user']['gold']>=10000 && $session['user']['gems']>=10){
			output("Cedrick takes your gold and gems and hands you a small seed in exchange.");
			$session['user']['gold']-=10000;
			$session['user']['gems']-=10;
			require_once("modules/orchard/orchard_func.php");
			orchard_findseed();
		}else{
			if ($session['user']['gold']>=10000)output("Cedrik stares at you blankly.  \"`%You don't have that many gems, `bgo get some more gems!`b`0\" he says.");
			elseif ($session['user']['gems']>=10) output("Cedrik stares at you blankly.  \"`%You don't have enough gold, `bgo get some more gold!`b`0\" he says.");
			else output("Cedrik stares at you blankly.  \"`%You don't have enough gold or gems, `bgo get some more!`b`0\" he says.");
		}
	}
	addnav("Other");
	addnav("Return to the Inn","inn.php");
	villagenav();
}
if ($op=="stables"){
	page_header("Merick's Stables");
	output("`7You go searching for the Plum seed in the stables, Merick gives you an odd sort of glance but lets you be.");
	output("Unfortunately you can't seem to find it, just as you're about to give up you spot a large heap of manure in the corner.");
	output("You're not entirely sure you want to go looking in there, but what the heck it's worth it right?`n`n");
	output("You delve into the manure heap up to your waist and push your arms into the pile searching around.");
	output("By some miracle you manage to find a Plum seed in the stinking mess and raise it above you head shouting \"`5Woohoo!`7\"");
	output("`7Seeing what you're doing, Merick comes storming over screaming \"`&Ach, thats disgusting! git outta me stables!`7\" and chases you away.`n`n");
	output("`7Fortunately you managed to keep hold of the Plum seed and race away from the stables triumphantly.");
	require_once("modules/orchard/orchard_func.php");
	orchard_findseed();
	villagenav();
}
if ($op=="abh"){
	if ($allprefs['seed']==5){
		page_header("Abandoned House");
		output("`7`c`bAbandoned House`b`c`n");
		output("You enter the derelict building to discover... nothing.");
		output("Oddly enough the place is empty having been abandoned some time ago by it's previous owner.`n`n");
		output("You explore a little, and notice a small seed covered in dust, you aren't sure, but it looks like a banana seed.");
		addnav("Return to the Alley","runmodule.php?module=darkalley");
		require_once("modules/orchard/orchard_func.php");
		orchard_findseed();
	}else{
		page_header("Abandoned House");
		output("`7`c`bAbandoned House`b`c`n");
		output("You enter the derelict building to discover... nothing.");
		output("Oddly enough the place is empty having been abandoned some time ago by it's previous owner.`n`n");
		output("You explore a little, but discover nothing of interest, the previous owner must have taken everything with them when they left.");
		addnav("Return to the Alley","runmodule.php?module=darkalley");
	}
}
if ($op=="ramius"){
	page_header("The Graveyard");
	output("`7`b`cThe Mausoleum`c`b");
	$subop = httpget('subop');
	if ($allprefs['seed']==6){
		if ($subop=="restore"){
			$session['user']['deathpower']-=100;
			output("`\$Ramius`7 is impressed with your actions, and agrees to restore life to your peach seed.`n`n");
			require_once("modules/orchard/orchard_func.php");
			orchard_findseed();
		}else{
			if ($session['user']['deathpower']>=100){
				output("`\$Ramius`7 speaks, \"`7You ask a favour of me?  but of course, as you are such a faithful servant to me I would be happy to grant this request.`7\"");
				addnav("Restore the Seed (100 Favors)","runmodule.php?module=orchard&op=ramius&subop=restore");
			}else{
				output("`\$Ramius`7 speaks, \"`7No, I refuse to grant your request.  If you want something from me, you have to earn it.  Continue my work and we may speak further.`7\"");
			}
		}
	}
	output("`n`nYou have `6%s`7 favor with `\$Ramius`7.", $session['user']['deathpower']);
	addnav("Question `\$Ramius`0 about the worth of your soul","graveyard.php?op=question");
	$max = $session['user']['level'] * 5 + 50;
	$favortoheal = round(10 * ($max-$session['user']['soulpoints'])/$max);
	addnav(array("Restore Your Soul (%s favor)",$favortoheal),"graveyard.php?op=restore");
	addnav("Places");
	addnav("S?Land of the Shades","shades.php");
	addnav("G?Return to the Graveyard","graveyard.php");
	modulehook("ramiusfavors");
}
if ($op=="odin"){
	page_header("The Ancient Hall");
	$subop = httpget('subop');
	output("`7`b`cThe Ancient Halls`c`b");
	if ($allprefs['seed']==6){
		if ($subop=="restore"){
			$session['user']['deathpower']-=100;
			output("`&Odin`7 is impressed with your actions, and agrees to restore life to your peach seed.`n`n");
			require_once("modules/orchard/orchard_func.php");
			orchard_findseed();
		}else{
			if ($session['user']['deathpower']>=100){
				output("`&Odin`7 speaks, \"`7You ask a favour of me?  but of course, as you are such a faithful servant to me I would be happy to grant this request.`7\"");
				addnav("Restore the Seed (100 Favors)","runmodule.php?module=orchard&op=odin&subop=restore");
			}else{
				output("`&Odin`7 speaks, \"`7No, I refuse to grant your request.  If you want something from me, you have to earn it.  Continue my work and we may speak further.`7\"");
			}
		}
	}
	output("`n`nYou have `6%s`7 favor with `&Odin`7.", $session['user']['deathpower']);
	addnav("Question `\$Odin`0 about the worth of your soul","runmodule.php?module=valhalla&location=ancienthall&op=question");
	$max = $session['user']['level'] * 5 + 50;
	$favortoheal = round(10 * ($max-$session['user']['soulpoints'])/$max);
	addnav(array("Restore Your Soul (%s favor)",$favortoheal),"runmodule.php?module=valhalla&location=ancienthall&op=restore");
	addnav("Places");
	addnav("Return to Valhalla","runmodule.php?module=valhalla&location=valhalla");
	addnav("A?Return to the Ancient Halls","runmodule.php?module=valhalla&location=ancienthall");
}

if ($op=="hof"){
	require_once("modules/orchard/orchard_hof.php");
	orchard_hof();
}
page_footer();
?>