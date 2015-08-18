<?php
/*
Module Name:  Alignment Events
Category:  Forest Specials
Author:  CortalUX, modified by DaveS and Selenity Hyperion
*/

function alignmentevents_getmoduleinfo(){
	$info = array(
		"name"=>"Alignment Events",
		"version"=>"3.12",
		"author"=>"`@CortalUX`7, modified by DaveS and `b`&Se`)le`4ni`4ty `&Hy`)pe`4ri`4on`b",
		"category"=>"Forest Specials",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=334",
		"settings"=>array(
			"alignbad"=>"Alignment points lost being evil, int|1",
			"aligngood"=>"Alignment points gained for being good, int|1",
		),
		"prefs"=>array(
			"Alignment Events - Preferences,title",
			"aligntried"=>"Had a chance to help someone this newday?,bool|0",
		),
		"requires"=>array(
			"alignment" => "1.8|Chris Vorndran, WebPixie",
		),
	);
	return $info;
}

function alignmentevents_chance() {
	global $session;
	if (get_module_pref('aligntried','alignmentevents',$session['user']['acctid'])==1) return 0;
	return 100;
}

function alignmentevents_install(){
	module_addeventhook("forest","require_once(\"modules/alignmentevents.php\"); 
	return alignmentevents_chance();");
	module_addhook("newday");
	return true;
}

function alignmentevents_uninstall(){
	return true;
}

function alignmentevents_dohook($hookname,$args){
	global $session;
	switch ($hookname) {
		case "newday":
			set_module_pref("aligntried",0);
		break;
	}
	return $args;
}
function alignmentevents_runevent($type) {
	global $session;
	$op = httpget('op');
	$op2 = httpget('op2');
	if ($op==""){
		$session['user']['specialinc']="module:alignmentevents";
		set_module_pref("aligntried",1);
		$rand=e_rand(1,12);
		addnav("Actions");
		addnav("`@Help","forest.php?op=help&op2=$rand");
		addnav("`4Hinder","forest.php?op=hinder&op2=$rand");
		addnav("`6Ignore","forest.php?op=ignore&op2=$rand");
		switch($rand){
			case 1:
				output("`n`@Walking around in the forest you rest in a convenient tree... waking up a bit later, you notice a clingy substance clinging to your face... feeling it, you realize it to be spider silk.`n`n ");
				output("An enchanted spider approaches you, and explains that it didn't mean to scare you, but it and it's friends are trying to learn to spin thread, and it is `%not`@ going well.`n");
				output("What are you going to do?");
			break;
			case 2:
				output("`n`^You stumble upon a frothing, raging, running river, and see a stranded fishing boat sinking slowly,");
				output("yet held up slightly by a pile of rocks!`n`nThe boat is full of a worried looking crew, and they seem to notice you!`n`n");
				output("`3 \"`&Help! Help!`3\"`^ they yell.`n");
				output("What are you going to do?");
			break;
			case 3:
				output("`n`#You wander across a group of children playing in a tree.  They cry out in fear as a branch begins to break.");
				output("It looks like one of them will fall!`n");
				output("What are you going to do?");
			break;
			case 4:
				output("`n`QA huge lion comes crashing through the forest. You notice he's limping! Since you've never seen a limping lion before,");
				output("you find this quite amazing.  He comes to within a couple of feet and raises his front foot and you see a large thorn sticking out of it.`n");
				output("What are you going to do?");
			break;
			case 5:
				output("`n`%You come up to an open patch in the forest and see a young explorer yelling for help.");
				output("He's caught in quicksand and he's going down fast.`n");
				output("What are you going to do?");
			break;
			case 6:
				output("`n`!You are wandering through the forest when you happen upon a group of young children.");
				output("They are sitting huddled up against a tree, their clothes ragged and torn. They look as if they haven't eaten in days.`n");
				output("What are you going todo?");
			break;
			case 7:
				output("`&While you are wandering through the forest, you stumble upon an enormous herd of elephants!`n");
				output("You notice that they are `%pink `&with `5purple `&dots all over them and are only the size of a small dog!!`n");
				output("Are you going to help them by painting them the color of normal elephants?`n");
				output("Or are you going to ignore them and be on your way?`n");
				output("Or perhaps you will market their weirdness and make a profit?!`n");
				output("What are you going to do?");
			break;
			case 8:
				output("`&As you are wandering through the forest, you trip and fall flat on your face!`n");
				output("Grumbling, you pick yourself up, only to find yourself looking right into the eyes of a `4Red Fox`&!");
				output("Your heart begins to beat faster as the fox sniffs your face, teeth bared.");
				output("Suddenly, the fox whimpers, and you see that it has been caught in a trap!");
				output("`#What are you going to do?");
				output("`&You could `@help `&the fox and get it out of the trap, or you could simply `6ignore `&it and be on your way.");
				output("Then again, you could be `4absolutely evil `&and pick on the fox and tease it.");
			break;
			case 9:
				output("`VThe forest seems especially quite today, you realize. You have been wandering through the woods for a good while,");
				output("searching for creatures to release from this realm of existence. Suddenly, you hear the snap of a twig. Whirling around,");
				output("you draw your weapon, eyes searching the foliage for the source of the intrusion upon your journey.`n`n");
				output("Your fingers tighten around the weapon as a creature emerges from the shadows of the forest.`n`n`n");
				output("It is a wounded deer! She has an arrow sticking out of her side and she is bleeding profusely.");
				output("Her eyes plead with you, yet at the same time are full of fear, frightened at the weapon in your hand, but too injured to run.");
				output("What are you going to do?`n`n`n");
				output("You could `2help`V the poor creature and heal its wounds.");
				output("`n`nOR you could `6ignore`V it and go on your way.");
				output("`n`nOR, being the vile, cruel person you are, you could hurt the deer even more by torturing it.");
			break;
			case 10:
				output("`jIt has been a long, difficult day, and you are walking around in the forest, wondering if you should kill any more of these creatures.");
				output("You know that it'll be....wait...what's that up ahead?");
				output("`n`nBlocking the path is a wagon, and something is wrong about it...");
				output("`n`nYou notice that the wagon is uneven and the occupants are standing on the side of the road.");
				output("Thinking you might benefit from this somehow, you go to find out what is going on.");
				output("When you arrive, the people look at you, distress apparent in their eyes.");
				output("They beg with you to help them with their wagon, which has broken off a wheel on its way to the market.");
				output("Will you help them?");
			break;
			case 11:
				output("`\$As you are trudging wearily through the forest, you come across a little girl in a red cape.");
				output("She is holding a basket and you can smell the food coming out of it, delictable, delicious, intoxicating...`n`n");
				output("The little red caped girl is singing about going to her Granma's house.");
				output("It clicks in your mind that there are several evil creatures in the forest...");
				output("This is a chance to help out a small, weak being.`n`n");
				output("What are you going to do?");
			break;
			case 12:
				output("`QYou are wandering through the forest, enjoying the scenery, searching for the `@Green Dragon`Q when you look down and notice a trail of `tcrumbs.");
				output("`QThinking it might lead you to a bakery, you bend down and taste the `tcrumbs `Qbefore grinning happily.");
				output("You continue to follow the trail, gobbling down the `tcrumbs`Q. After a little while, the crumbs stop, and frowning, look up.");
				output("The sight before you is astounding! It's a house made entirely out of candy!`n");
				output("Drooling, you rush up to the house, reaching out to take a piece of the window...`n");
				output("When suddenly you hear a scream! You peek inside the window to see two local village children locked in a cage!`n");
				output("A woman stands over them, grinning hideously. The children are in trouble! What are you going to do?!");
				output("Are you going to `2help`Q the children or `\$hinder`Q them and help the witch?!");
			break;
		}
	}elseif ($op=="help"){
		output("`n`c`^You feel `@`bGOOD`b`^!`c`n");
		increment_module_pref("alignment",get_module_setting("aligngood"),"alignment");
		switch($op2){
			case 1: 
				output("`@You walk around a bit more, seeking help for your spidery chums.`nEventually, you walk to the village, and buy them a knitting magazine!`n");
				addnews("`^%s`@ has bought a knitting book for a group of spiders in the forest today! How kind!!!",$session['user']['name']);
			break;
			case 2:
				output("`^You paddle across the raging river, with a rope- one end tied around you, the other upon the shore. When you land on the boat, ");
				output("you tie your rope to it, and help the crew pull the boat to shore, by using their weight to stay in the boat, and pull the boat over.`n");
				addnews("`^%s`@ has helped a boat full of people in the forest today! How kind!!!",$session['user']['name']);
			break;
			case 3:
				output("`#With amazing agility, you run to capture the child as he falls from the tree and lands in your arms.  He runs off without saying ");
				output("anything, but you know you saved him from a nasty injury!`n");
				addnews("`^%s`@ has helped a child in the forest today! How kind!!!",$session['user']['name']);
			break;
			case 4:
				output("`QFor some reason you feel a little mousy when you do this, but you pull out the thorn without any problems!  The lion smiles and walks away.`n");
				addnews("`^%s`@ has helped an injured lion in the forest today! How kind!!!",$session['user']['name']);
			break;
			case 5:
				output("`%Using a long branch you safely pull the explorer to shore.  He shakes your hand gratefully and walks off into the forest.`n");
				addnews("`^%s`@ has helped an endangered explorer in the forest today! How kind!!!",$session['user']['name']);
			break;
			case 6:
				output("`!You show the children how to hunt for food and use the skins and some leaves to make clothes. They cheer your name as you leave.`n");
				addnews("`^%s`@ has helped a group of starving children in the forest today! How kind!!!",$session['user']['name']);
			break;
			case 7:
				output("`&Being the goody-two-shoes that you are, you grab a bucket of paint and turn them all grey so they seem like normal elephants...a bit more so, anyways.`n");
				addnews("`^%s`@ has helped a group of `inormal`i elephants in the forest today! How kind!!!",$session['user']['name']);
			break;
			case 8:
				output("`@Since you are a lover of animals, you help the fox out of the trap and nurse it quickly back to health!`n");
				addnews("`^%s `@has helped a `4Fox `&out of a trap in the forest! How kind!!!",$session['user']['name']);			
			break;
			case 9:
				output("`tBeing the kind person you are, you help the deer and stay with it until its wounds are completely healed.`n");
				addnews("`^%s `@has helped an injured `tdeer `&in the forest! How kind!!!",$session['user']['name']);
			break;
			case 10:
				output("`jYou are so kind that you decide to help the people out and go chop down a tree, sharpen it with your weapon`j, and create a new wheel for them!");
				addnews("`^%s `jhas helped a broken down wagon `jin the forest! How kind!!!",$session['user']['name']);
			break;
			case 11:
				output("`\$Smiling to yourself, you wait for her to see you. When she does, she smile and asks for you to join her for lunch at her granma's house!");
				addnews("`^%s `\$has been invited to lunch with a Red Caped Girl and her Granma in the forest today! Yummy!!",$session['user']['name']);
			break;
			case 12:
				output("`QYou run to help the children escape from the horrid witch! You manage to get rid of her and enjoy the tasty house!");
				addnews("`^%s `Qhas helped 2 children escape from an evil witch in the forest today!",$session['user']['name']);		
			break;
		}
		$session['user']['specialinc']="";
	}elseif ($op=="hinder") {
		output("`n`c`^You feel more `4`bEVIL`b`^!`c`n");
		increment_module_pref("alignment",-get_module_setting("alignbad"),"alignment");
		switch($op2){
			case 1: 
				output("`@You `%`ihate`i`@ spiders, and squash them!`n");
				addnews("`^%s`4 has squashes a group of spiders in the forest today! How mean!!!",$session['user']['name']);
			break;
			case 2:
				output("`^You find it funny, and decide to sink the boat yourself!");
				output("Finding a convenient rock, you lob it over the river, hitting one of the rocks beneath the boat, lowering it imperceptibly.");
				addnews("`^%s`4 has sunk a boat full of people in the forest today! How mean!!!",$session['user']['name']);
			break;
			case 3:
				output("`#You start throwing rocks at the child.  You laugh as he falls and think about how sticks AND stones can break bones!`n");
				addnews("`^%s`4 has thrown a rock and knocked a child out of a tree in the forest today! How mean!!!",$session['user']['name']);
			break;
			case 4:
				output("`QYou look at the poor lion and get an evil glint in your eyes.  You quickly jam the thorn deeper into his paw and run off ");
				output("to the forest looking for even MORE fun!`n");
				addnews("`^%s`4 has hurt a lion in the forest today! How mean!!!",$session['user']['name']);
			break;
			case 5:
				output("`%You tell him he can survive if he tries to swim really fast to you.  He sinks because of your bad advice and you smirk and wander off.`n");
				addnews("`^%s`4 has tricked a young explorer in a patch of quicksand in the forest today! How mean!!!",$session['user']['name']);
			break;
			case 6:
				output("`!You smirk and begin to make fun of the children, sending your pet to attack the children and take anything of value they might have, which is nothing. `n");
				addnews("`^%s`4 has made fun of a group of starving children in the forest today!! How mean!!!",$session['user']['name']);
			break;
			case 7:
				output("`4Being the evil person you are, you decide to market the freaks and become famous!`n");
				addnews("`^%s`4 has tried to market a group of pink and purple dotted elephants from the forest today! How mean!!!",$session['user']['name']);
			break;
			case 8:
				output("`4Since you are so evil, you poke and prod the poor creature with a stick!`n");
				addnews("`^%s `4has been cruel to a poor `4Fox `4 in the forest today!! How mean!!",$session['user']['name']);
			break;
			case 9:
				output("`tYour evil nature causes you to be cruel to the poor creature! How could you be so mean?!?!`n");
				addnews("`^%s `4has tortured a poor injured `tdeer`4 in the forest today!! How mean!!",$session['user']['name']);
			break;
			case 10:
				output("`jGrinning evilly, you pull out your weapon and take all of their valuables!`n");
				addnews("`^%s `@has robbed a broken down wagon `jin the forest! How kind!!!",$session['user']['name']);
			break;
			case 11:
				output("`\$You grin deviously and run to where you know the old lady's house is. Donning a wolf suit,"); 
				output("you eat the granma and, when the little girl gets to the house, you eat all the food and the little girl, too!`n");
				addnews("`^%s `\$ has eaten a granma and a little Red Caped Girl in the forest today! How freaky!!",$session['user']['name']);
			break;
			case 12:
				output("`QYou decide to be evil so you run in and help the witch make little children stew!");
				addnews("`^%s `Qhas made soup from 2 little children in the forest! How creepy!!!",$session['user']['name']);
			break;
		}
		$session['user']['specialinc']="";
	}elseif ($op=="ignore"){
		output("`n`c`^You feel kinda `&`bNeutral`b`^ about the world.`n`n`c");
		switch($op2){
			case 1: 
				output("`@You ignore these freaky creatures, and walk off");
				addnews("`^%s`6 has ignored a spider in the forest today!",$session['user']['name']);
			break;
			case 2:
				output("`^You ignore the yelling and walk off.");
				addnews("`^%s`6 has ignored a boat of people in danger in the forest today!",$session['user']['name']);
			break;
			case 3:
				output("`#Thinking that children have to learn from their mistakes, you walk off.");
				addnews("`^%s`6 has ignored a tree full of children in the forest today!",$session['user']['name']);
			break;
			case 4:
				output("`QYou decide you don't even want to get involved in this fable and walk off.");
				addnews("`^%s`6 has ignored an injured lion in the forest today!",$session['user']['name']);
			break;
			case 5:
				output("`%Figuring that at least you know how to identify quicksand now, you walk off.");
				addnews("`^%s`6 has ignored a young explorer in danger in the forest today!",$session['user']['name']);
			break;
			case 6:
				output("`!You blink and throw some crusty bread at them, grinning inside at how stupid children are.");
				addnews("`^%s`6 has ignored a group of starving children in the forest today!",$session['user']['name']);
			break;
			case 7:
				output("`&You ignore these strange things and walk off.");
				addnews("`^%s`6 has ignored a herd of pink and purple dotted elephants in the forest today!",$session['user']['name']);
			break;
			case 8:
				output("`&You decided to ignore the poor creature caught in the trap and go on your way.`n");
				addnews("`^%s `6has ignored a poor `4Fox`6 caught in a trap in the forest today!",$session['user']['name']);
			break;
			case 9:
				output("`tQuirking an eyebrow, you continue on your way, ignoring the critter.`n");
				addnews("`^%s `6has ignored a poor injured `tdeer`6 in the forest today!",$session['user']['name']);
			break;
			case 10:
				output("`jIgnoring the people, you go on your way.`n");
				addnews("`^%s `jhas ignored a broken down wagon `jin the forest.",$session['user']['name']);
			break;
			case 11:
				output("`\$Ignoring the little red speck, you continue on your way.`n");
				addnews("`^%s`\$ has ignored a little Red Caped Girl in the forest today.",$session['user']['name']);
			break;
			case 12:
				output("`QIgnoring the weird scene, you eat away at the yummy house!");
				addnews("`^%s `Qhas ignored 2 children caught by an evil yummy-house weilding witch in the forest today.",$session['user']['name']);
			break;
		}
		$session['user']['specialinc']="";
	}
}
function alignmentevents_run(){
}
?>