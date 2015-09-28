<?php
global $session;
$op=httpget('op');
switch($type){
	case 'cellar':
		output("`%You spot a seed in the darkness, picking it up you realize it's the apple seed you've been looking for!");
		require_once("modules/orchard/orchard_func.php");
		orchard_findseed();
	break;
	case 'forest':
		$session['user']['specialinc'] = "module:orchard";
		$allprefs=unserialize(get_module_pref('allprefs'));
		$seed= $allprefs['seed'];
		if ($seed==2){
			output("`n`2As you are walking through the forest, you come across a large orange grove, you pick an orange and eat it.`n`n");
			output("Just as you are about to throw the skin and seeds away, you remember the orchard and pocket one of the seeds.");
			require_once("modules/orchard/orchard_func.php");
			$session['user']['specialinc']="";
			orchard_findseed();
		}elseif ($seed==9){
			output("`n`2Strolling through the forest in search of something to kill, you stumble upon a mango tree.  You think perhaps it's a little odd for a mange tree to be growing in the middle of the forest, but this is only a game after all.`n");
			output("You decide it would be nice to eat a mango, and then you could keep one of the mango seeds and take it to the orchard.  So thats what you do.");
			require_once("modules/orchard/orchard_func.php");
			$session['user']['specialinc']="";
			orchard_findseed();
		}elseif ($seed==15 && ((is_module_active("quarry") && get_module_setting("alloworchard","quarry")==0)||is_module_active("quarry")==0)){
			output("`n`2As you wander through the forest you notice that life is starting to get you down.");
			output("`n`nYou come upon a lime tree and decide that the best thing you can do is make lime-ade.");
			output("However, you realize you have no sugar.  Instead, you open up one of the limes and keep the seed.");
			require_once("modules/orchard/orchard_func.php");
			$session['user']['specialinc']="";
			orchard_findseed();
		}elseif ($seed==16){
			output("`n`2Running through the forest you slip on a piece of fruit.  You look around and notice you're standing in the middle of a pomegranate grove.");
			output("`n`nExcellent!");
			$session['user']['specialinc']="";
			require_once("modules/orchard/orchard_func.php");
			orchard_findseed();
		}elseif ($seed==17){
			if ($op==""){
				output("`n`2A strange bird lands on your shoulder and starts singing beautifully.`n`nCould this be the kiwi bird you've been looking for???");
				addnav("Rub its belly","forest.php?op=orubbelly");
				addnav("Leave","forest.php?op=orchardleave");
			}
			if ($op=="orubbelly"){
				$allprefs['bellyrub']=$allprefs['bellyrub']+1;
				$rub=$allprefs['bellyrub'];
				output("`n`2You reach up and start to rub the belly of the bird.`n`n");
				switch ($rub){
					case 1:
						output("Suddenly, the bird pecks you in the hand!");
						output("`6'Hey!!'`2 says the bird,`6 'What kind of pervert are you???'`n`n");
						output("`2Clearly this is not the bird you were looking for.");
					break;
					case 2:
						output("The bird smiles and gives you a pouch with a note inside.");
						output("`n`n`6Thanks for the belly rub. Here's your tip!");
						output("`n`n`2You find a `%gem`2 inside.  Perhaps there's a future in bird massage for you!");
						output("Unfortunately, this isn't the kiwi bird you were looking for.");
						$session['user']['gems']++;
					break;
					case 3:
						output("`6'That feels SPECTACULAR!!!! Thank you so much!'");
						output("`n`n`2The talking bird opens a small pouch and drops a seed in your hand.");
						require_once("modules/orchard/orchard_func.php");
						orchard_findseed();
						$allprefs=unserialize(get_module_pref('allprefs'));
						$allprefs['bellyrub']=0;
					break;
				}
				set_module_pref('allprefs',serialize($allprefs));
				addnav("Return to the Forest", "forest.php?php");
				$session['user']['specialinc']="";
			}
			if ($op=="orchardleave"){
				output("`2`nYou head back into the forest.");
				addnav("Return to the Forest", "forest.php?php");
				$session['user']['specialinc']="";
			}
		}elseif (($seed==4||$seed==6||$seed==7||$seed==10||$seed==11||$seed==14||$seed==18||$seed==19||$seed==20) && $allprefs['dietreehit']==0 && $allprefs['dieingtree']==0){
			$treecare=get_module_setting("treecare");
			$tree=$allprefs['tree'];
			$names=translate_inline(array("","Apple","Orange","Pear","Apricot","Banana","Peach","Plum","Fig","Mango","Cherry","Tangerine","Grapefruit","Lemon","Avocado","Lime","Pomegranate","Kiwi","Cranberry","Star Fruit","Dragonfruit"));
			output("`n`2You are wandering through the forest when you encounter `!Elendir`2 of the Fruit Orchard.`n`n");
			output("\"`#There you are! I've been looking for you everywhere.  I have some bad news to report to you. Your `^%s`# tree is very sick.`2\"",$names[$tree]);
			output("`n`nYou get a horrible feeling in your stomach and a sense of panic. You wait for `!Elendir`2 to tell you more.");
			output("`n`n\"`#In order to save your tree you'll have to meet me back at the orchard.  I can try to help you but it will take `@%s turn%s`# to try to heal it and even then there's a chance it won't get better.`2\"",$treecare,translate_inline($treecare>1?"s":""));
			output("`n`n\"`#Please hurry.  If you aren't able to help heal the tree today, it will die for sure by tomorrow.`2\"");
			output("`n`nPerhaps you should go take care of your poor little tree!");
			$allprefs['dietreehit']=1;
			$allprefs['dieingtree']=get_module_setting("treecare");
			set_module_pref('allprefs',serialize($allprefs));
			$session['user']['specialinc']="";
		}
	break;
	case 'darkalley':
		$session['user']['specialinc'] = "module:orchard";
		if($session['user']['gold']<1000){
			output("`n`7A shady figure approaches out of the darkness of the alley.`n");
			output("`7Quickly looking you over he moves on in another direction. ");
			output("Disappearing back into the darkness. You shake your head and wonder what that was all about.`n");
			$session['user']['specialinc']="";
			break;
		}
		if ($op=="pass"){
			$session['user']['specialinc'] = "";
			output("`n`7The salesman seems just too shady for you, and you pass on his offer.`n`n");
			output("`7He slinks away, muttering, \"`7You don't know what you're missing, pal.`7\"`n`n");
			output("`7You shake your head, and turn around to head back to the alley.");
		}elseif ($op=="accept"){
			$session['user']['specialinc'] = "";
			output("`n`7Shady though he is, you decide to take up the salesman on his offer.`n`n");
			$session['user']['gold']-=1000;
			debuglog("spent 1000 on the shady salesman.");
			output("`7The salesman reaches into one of his deep pockets and pulls out a mango seed.");
			output("The salesman explains to you that this seed is very rare.");
			output("He then slinks off into the shadows as you examine the seed, ");
			output("which you thankfully discover is actually a mango seed like he said, just what you needed for the orchard.");
			require_once("modules/orchard/orchard_func.php");
			orchard_findseed();
		}else{
			output("`n`7While passing through the alley, a shady figure in a black trenchcoat and fedora catches your eye and motions you over into his dark corner.");
			output("You cautiously apporach the man, wondering what he could want with you.");
			output("Once you are close by he finally speaks, \"`7Greetings, adventurer.");
			output("You look like you could use a break, and I am just the person to give you such a break.");
			output("For the pittance of only `^1000`7 gold, I can offer you something that will be a great asset to you on your travels.");
			output("Bear in mind, I don't offer my services to just anyone, so you're very lucky.");
			output("Although, you will have to decide quickly, as I am quite busy.`7\"");
			output("He stands there with his hands in his deep pockets, awaiting your decision.`n`n");
			output("You ponder the shady salesman's pitch carefully, wondering how useful this mystery item might actually be, somewhat doubting that the salesman was fully honest.");
			addnav("Accept the Salesman's Offer (`^1000`0 gold)", "runmodule.php?module=darkalley&op=accept");
			addnav("Pass on the Salesman's Offer","runmodule.php?module=darkalley&op=pass");
		}
	break;
}
?>