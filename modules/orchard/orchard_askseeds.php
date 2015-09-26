<?php
function orchard_askseeds(){
	global $session;
	page_header("The Hollow Tree");
	addnav("Return to the entrance","runmodule.php?module=orchard");
	$allprefs=unserialize(get_module_pref('allprefs'));
	$seed= $allprefs['seed'];
	$tree= $allprefs['tree'];
	if ($allprefs['treegrowth']>0) $tree++;
	if ($allprefs['found']>0) addnav("Show Elendir your seed","runmodule.php?module=orchard&op=giveseed");
	if ($tree>0)addnav("Ask about your trees","runmodule.php?module=orchard&op=asktrees");
	output("`7\"`#So you want to find a seed to make your own contribution to the orchard, thats wonderful!`7\"`n`n");
	require_once("modules/orchard/orchard_func.php");
	switch ($tree){
		case 0:
			if (is_module_active("cellar")) output("\"`#I know that Cedrick used to have a few `\$apple`# seeds in his posession, but he probably lost them in the cellar somewhere knowing him.`7\"");
			else orchard_monster(1);
			$allprefs=unserialize(get_module_pref('allprefs'));
		break;
		case 1:
			output("\"`#I heard tell of an `Qorange`# grove hidden deep in the forest, perhaps you will find it in your travels.`7\"");
		break;
		case 2:
			orchard_monster(3);
			$allprefs=unserialize(get_module_pref('allprefs'));
		break;
		case 3:
			output("\"`#Cedrick owns a small `Qapricot`# tree, it's a very important posession to him so it will probably cost you dearly to get a seed from it, go and talk to him.`7\"");
		break;
		case 4:
			if (is_module_active("darkalley")) output("\"`#An old man used to live in the Dark Alley, he owned a `^banana`# tree, perhaps he left some seeds in his house when he left.`7\"");
			else orchard_monster(5);
			$allprefs=unserialize(get_module_pref('allprefs'));
		break;
		case 5:
			output("\"`#I have here a `4peach`# seed, but unfortunately it has been dead for some time.  I will help you plant it if you can convince Raimus to breathe life back into the seed.`7\"");
		break;
		case 6:
			output("\"`#I believe Merick feeds the horses at his stables `5plums`# sometimes.`7\"");
		break;
		case 7:
			orchard_monster(8);
			$allprefs=unserialize(get_module_pref('allprefs'));
		break;
		case 8:
			if (is_module_active("darkalley")) output("\"`#The last I heard, some unsavoury character who is often seen in the Dark Alley had some `6mango`# seeds, who knows where he is now though.`7\"");
			else output("\"`#I heard tell of a `6mango`# grove hidden deep in the forest, perhaps you will find it in your travels.`7\"");
		break;
		case 9:
			output("\"`#Rumor has it that the Green Dragon is fond of `\$cherries`#.  There is only one way to find out for sure though.`7\"");
		break;
		case 10:
			output("\"`#I'm not sure if this is true or not, but there may be a `Qtangerine`# seed in the bank.");
			if ($allprefs['bankkey']==1) addnav("Ask about the Bank Key","runmodule.php?module=orchard&op=bankkey");
			if ($allprefs['bankkey']==0){
				output("I think I have a key around here somewhere that might help you.");
				$allprefs['bankkey']=1;
				addnav("Talk Some More","runmodule.php?module=orchard&op=bankkey");
			}
		break;
		case 11:
			if (is_module_active("jail")) output("\"`#I hear they serve really good `^grapefruit`# for your last meal in jail.`7\"");
			else orchard_monster(12);
			$allprefs=unserialize(get_module_pref('allprefs'));
		break;
		case 12:
			if (is_module_active("lumberyard")){
				if (get_module_setting("alloworchard","lumberyard")==1) output("\"`#There's a sneaky foreman that runs the lumberyard.  Perhaps you can find a `^lemon`# seed while you swing the ole axe for a little bit.`7\"");
				else orchard_monster(13);
			}else orchard_monster(13);
			$allprefs=unserialize(get_module_pref('allprefs'));
		break;
		case 13:
			output("\"`#Well, I promised him I wouldn't mention it to anyone, but I think you might find an `2avocado`# seed over at `!MightyE's`# Weaponry.`0\"");
		break;
		case 14:
			if (is_module_active("quarry")){
				if (get_module_setting("alloworchard","quarry")==1) output("\"`#You're going to need to find a `@lime`# seed next.  Perhaps there's one in the quarry.`7\"");
				else output("\"`#I heard tell of a `@lime`# grove hidden deep in the forest, perhaps you will find it in your travels.`7\"");
			}else output("\"`#I heard tell of a `@lime`# grove hidden deep in the forest, perhaps you will find it in your travels.`7\"");
		break;
		case 15:
			if (is_module_active("crazyaudrey")) output("\"`#There's a strange woman that I've seen in the village and in the forest.  She may have a `\$pomegranate`# seed, but I'm not sure if she's mentally stable.  Try to find `%Crazy Audrey`# as she may have a seed for you.`7\"");
			else output("\"`#I heard tell of a `\$pomegranate`# grove hidden deep in the forest, perhaps you will find it in your travels.`7\"");
		break;
		case 16:
			output("\"`#You won't believe this, and I'm sure it won't make much sense to you, but here's the deal:\"`n`n\"There's a bird in the forest.  If you rub it's tummy you can get a seed from it.");
			output("The bird is a kiwi bird and it loves to have its belly rubbed. For some reason it has a collection of `qkiwi`# seeds and will offer one to you if you do this. I know, it doesn't make much sense, but I promise you there's magic in that forest!`7\"");
		break;
		case 17:
			output("\"`#The Gypsy named Pegasus is known for creating some of the most amazing armor in the kingdom. She also has the finest `4Cranberry`# tree around.  Perhaps she will let you have a seed.`7\"");
		break;
		case 18:
			output("\"`#The `^Star Fruit`# Seed is only available for those who are of a generous nature. You can visit the Lodge for your next seed if you're the generous type.`7\"");
		break;
		case 19:
			$dsage=$allprefs['dragonseedage'];
			if ($dsage==0){
				output("`!Elendir`7 hands you a seed.  It's warm to the touch.  You smile thinking how this seed has turned out to be so easy to get.  You are about to ask him to help you plant it when he stops you.");
				output("`n`n\"`#Here is the `@Dragon `\$Fruit`# Seed.  Unfortunately, it will not grow unless it is nurtured by a dragon.  You will need to plant this seed in the cave of the `@Dragon`# the next time you face her.");
				output("It will need to be in a dragon lair for 3 generations of dragons before it will be ready to be planted.  Good luck!`7\"");
			}elseif ($dsage>0){
				output("\"`#Your `@Dragon `\$Fruit`# Seed is germinating in the Dragon's Cave. Just");
				if ($dsage==1) output("3 more dragon kills");
				elseif ($dsage==2) output("2 more dragon kills");
				elseif ($dsage==3) output("one more dragon kill");
				output("and it will be ready for planting.`7\"");
			}
		break;
		case 20:
			output("\"`#Well, to be honest with you, there are no other trees that can be planted in the orchard.`7\"");
		break;
	}
	$allprefs['seed']=$tree+1;
	set_module_pref('allprefs',serialize($allprefs));
}
?>