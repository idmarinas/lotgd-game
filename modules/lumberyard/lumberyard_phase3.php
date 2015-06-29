<?php
function lumberyard_phase3(){
	global $session;
	output("`n`c`b`QT`qhe `QL`qumber `QY`qard `QP`qhase `Q3`0`c`b`n");
	$allprefs=unserialize(get_module_pref('allprefs'));
	$allprefs['phase']=1;
	$allprefs['usedlts']=$allprefs['usedlts']+1;
	$allprefs['squareshof']=$allprefs['squareshof']+1;
	$allprefs['squares']=$allprefs['squares']+1;
	set_module_pref('allprefs',serialize($allprefs));
	increment_module_setting("remainsize",-1);
	$session['user']['turns']--;
	$remainsize=get_module_setting("remainsize");
	$allprefs=unserialize(get_module_pref('allprefs'));
	$squares=$allprefs['squares'];
	output("`^With the log ready to be cut, you enter the mill for some saw work.`n`n");
	switch(e_rand(1,20)){
		case 1: case 2: case 3: case 4: case 5: case 6: case 7: case 8: case 9: case 10:
			output("You get a good rhythm going and get that log cut into perfect piles of 2 squares.");
			output("The foreman comes over and smiles at your handiwork, giving you a voucher for`& One Square of Wood`^.");
			output("`n`nYou've completed `QPhase 3`^ in `@one turn`^.`n`n");
			output("`^You now have`& %s %s of Wood`^.`n`n`^There are now`6 %s trees `^left in the forest.`n`n",$squares,translate_inline($squares>1?"Squares":"Square"),$remainsize);
			debuglog("completed Phase 3 in the Lumberyard and gained a square of wood.");
		break;
		case 11:
			output("`@You're a lumber jack and you're okay!`nYou work all night and you sleep all day!`nYou cut down trees. You eat your lunch.`n");
			output("You go to the lavatory.`nOn Wednesdays you go shoppin'`nAnd have buttered scones for tea.`n`n");
			output("`^The foreman comes over and smiles at your handiwork, giving you a voucher for`& One Square of Wood`^.");
			output("You've completed `QPhase 3 `^in `@one turn`^.`n`n");
			output("`^You now have`& %s %s of Wood`^.`n`n`^There are now`6 %s trees `^left in the forest.`n`n",$squares,translate_inline($squares>1?"Squares":"Square"),$remainsize);
			apply_buff('lumberjack',array(
				"name"=>"Lumberjack Strength",
				"rounds"=>7,
				"wearoff"=>"You stop cutting down trees. You stop skipping and jumping",
				"atkmod"=>1.5,
				"roundmsg"=>"You cut down trees. You skip and jump.",
			));
			debuglog("completed Phase 3 in the Lumberyard and gained a square of wood and the Lumberjack Strength Buff.");
		break;
		case 12:
			output("`^You work well with the saw, but a board kicks back and gives you a nasty little cut on your cheek.");
			output("However, it just makes you look really cool.`n`n You`& gain a charm point`^!!`n`n In addition, you finish cutting the wood and complete `QPhase 3`^ in `@one turn`^.`n`n The foreman gives you a voucher for`& One Square of Wood`^.`n`n");
			output("`^You now have`& %s %s of Wood`^.`n`n`^There are now`6 %s trees `^left in the forest.`n`n",$squares,translate_inline($squares>1?"Squares":"Square"),$remainsize);
			$session['user']['charm']++;
			debuglog("completed Phase 3 in the Lumberyard and gained a square of wood and gained a charm.");
		break;
		case 13:
			output("`^You steal someone's lunch and sell it back to them. Wow, that's kinda funny.`n`n You `bgain 50 gold`b.");
			output("`n`nIn addition, you finish cutting the wood and complete `QPhase 3`^ in `@one turn`^. The foreman gives you a voucher for `&One Square of Wood`^.`n`n");
			output("`^You now have`& %s %s of Wood`^.`n`n`^There are now`6 %s trees `^left in the forest.`n`n",$squares,translate_inline($squares>1?"Squares":"Square"),$remainsize);
			$session['user']['gold']+=50;
			debuglog("completed Phase 3 in the Lumberyard and gained a square of wood and gained 50 gold.");
		break;
		case 14:
			$fingergem=get_module_setting("fingergem");
			if ($fingergem<0) $fingergem=0;
			output("`^You finish cutting the log into boards and clean up. Nobody has ever cleaned up before.`n`n You find `43 fingers`^");
			if ($fingergem==1) output(",`% one gem`^,");
			if ($fingergem>1) output(", `%%s gems`^,",$fingergem);
			output("and `b10 gold`b. You decide not to keep the fingers.`n`n But you are able to finish cutting the wood and complete `QPhase 3`^ in `@one turn`^.");
			output("The foreman  gives you a voucher for`& One Square of Wood`^.`n`n");
			$session['user']['gold']+=10;
			$session['user']['gems']+=$fingergem;
			output("`^You now have`& %s %s of Wood`^.`n`n`^There are now`6 %s trees `^left in the forest.`n`n",$squares,translate_inline($squares>1?"Squares":"Square"),$remainsize);
			debuglog("completed Phase 3 in the Lumberyard and gained a square of wood and found 10 gold and $fingergem gems.");
		break;
		case 15:
			output("`^You notice a second log just outside of the building that some careless adventurer has left at the doorstep. You figure that it's better not to waste the log.");
			output("`n`n You spend`@ 2 turns`^ cutting all the wood and smile innocently as the foreman gives you a voucher for`& Two Squares of Wood`^.`n`n");
			$session['user']['turns']--;
			$allprefs=unserialize(get_module_pref('allprefs'));
			$allprefs['squareshof']=$allprefs['squareshof']+1;
			$allprefs['squares']=$allprefs['squares']+1;
			$squares=$allprefs['squares'];
			set_module_pref('allprefs',serialize($allprefs));
			increment_module_setting("remainsize",-1);
			if (get_module_setting("remainsize")<0) {
				set_module_setting("remainsize",0);
			}
			$remainsize=get_module_setting("remainsize");
			output("`^You now have`& %s %s of Wood`^.`n`n`^There are now`6 %s trees `^left in the forest.`n`n",$squares,translate_inline($squares>1?"Squares":"Square"),$remainsize);
			debuglog("completed Phase 3 in the Lumberyard and gained two squares of wood.");
		break;
		case 16:
			addnav("Lumberjack`$ Fight","runmodule.php?module=lumberyard&op=attack");
			require_once("modules/lumberyard/lumberyard_blocknavs.php");
			lumberyard_blocknavs();
			blocknav("forest.php");
			output("You mind your own business and cut your log. However, a`$ Burly Lumberjack`^ doesn't like the look of you and picks a fight with you.");
		break;
		case 17:
			output("`^You get in a rhythm. You cut like a pro! All the other workers stop to watch you.");
			output("`n`nYou are	popular! You feel energy!`n`nYou`& gain 2 charm points`^ and `@gain 2 forest fights`^!");
			output("`n`nBut wait! There's more The foreman shakes your hand and gives you a voucher for`& One Completed Square`^!");
			output("`^You now have`& %s %s of Wood`^.`n`n`^There are now`6 %s trees `^left in the forest.`n`n",$squares,translate_inline($squares>1?"Squares":"Square"),$remainsize);
			$session['user']['turns']+=3;
			$session['user']['charm']+=2;
			debuglog("completed Phase 3 in the Lumberyard and gained a square of wood and gained 3 turns and 2 charm.");
		break;
		case 18:
			output("You finish your work and the foreman hands you a voucher for`& One Completed Square`^.");
			output("It took you`@ One Turn`^.`n`n`^You now have`& %s Squares of Wood`^.`n`n",$squares);
			output("`^You head out to the forest and sit down by a bird. The bird sings a wonderful song about worms eating goats.");
			output("Then you see a frog. It stands up and walks away. A beautiful polar bear comes over and sits by you.");
			output("You play Rummy with the polar bear for hours.`n`nThat is the last time you eat strange mushrooms.");
			output("The question is, what is the consequence of your action? Well, hallucinating isn't good.");
			output("So you sometimes get confused while you're fighting.`n`n");
			apply_buff('hallucinations',array(
				"name"=>"Hallucination",
				"rounds"=>10,
				"wearoff"=>"You realize there's only one monster",
				"badguyatkmod"=>1.2,
				"roundmsg"=>"You think there's two of them attacking you, and they hit you twice as hard as usual",
			));
			debuglog("completed Phase 3 in the Lumberyard and gained a square of wood and gained the hallucination buff.");
		break;
		case 19:
			if (is_module_active("bakery")) {
				output("`^With amazing gusto you get to work. However, halfway through you get a terrible stomach ache. `n`nYou can't finish your work and will have to work in the saw mill again later.");
				output("However, you suddenly realize that the best cure for a stomach ache is homemade pastries. You find yourself wandering over to	`b`@Hara's `^Bakery`b in a confused haze.");
				//set pastrytoday at hara's to zero
				$allprefsb=unserialize(get_module_pref('allprefs','bakery'));
				$allprefsb['pastrytoday']=0;
				set_module_pref('allprefs',serialize($allprefsb),"bakery");
				//give back the squares and phase
				increment_module_setting("remainsize",1);
				$allprefs=unserialize(get_module_pref('allprefs'));
				$allprefs['phase']=3;
				$allprefs['squareshof']=$allprefs['squareshof']-1;
				$allprefs['squares']=$allprefs['squares']-1;
				set_module_pref('allprefs',serialize($allprefs));
				//change location to the same place as where the bakery is
				$session['user']['location']=get_module_setting("bakeryloc","bakery");
				addnav("B?Hara's Bakery","runmodule.php?module=bakery&op=food");
				require_once("modules/lumberyard/lumberyard_blocknavs.php");
				lumberyard_blocknavs();
				blocknav("forest.php");
				debuglog("couldn't complete Phase 3 in the Lumberyard and got sent to Hara's Bakery.");
			}else{
				output("`^You get a good rhythm going and get that log cut into perfect boards of 2 squares.`n`n");
				output("The foreman comes over and smiles at your handiwork, giving you a voucher for`& One Square of Wood`^.");
				output("You've completed `QPhase 3`^ in `@one turn`^.`n`n");
				output("`^You now have`& %s %s of Wood`^.`n`n`^There are now`6 %s trees `^left in the forest.`n`n",$squares,translate_inline($squares>1?"Squares":"Square"),$remainsize);
				debuglog("completed Phase 3 in the Lumberyard and gained a square of wood.");
			}
		break;
		case 20:
			if ($session['user']['gold']<200){
				output("`^You were going to pay someone off to do the work but you don't have enough gold on you.");
				output("You`@ waste a turn `^looking for someone to do the work and you `@lose a turn`^ getting the job done.`n`n");
				$session['user']['turns']--;
				output("`^You finish cutting the wood and complete `QPhase 3`^.");
				output("The foreman gives you a voucher for`& One Square of Wood`^.");
				debuglog("completed Phase 3 in the Lumberyard and gained a square of wood and spend an extra turn there.");
			}else{
				output("`^You decide to pay off someone to do the work.");
				output("So instead you get to run off to the forest to use your turn more wisely!");
				output("`n`n After paying out `b200 gold`b, you go to the foreman to get your voucher for`& One Square of Wood`^.");
				$session['user']['gold']-=200;
				$session['user']['turns']++;
				debuglog("completed Phase 3 in the Lumberyard and gained a square of wood, spent 200 gold, and didn't lose a turn.");
			}
			output("`^You now have`& %s %s of Wood`^.`n`n`^There are now`6 %s trees `^left in the forest.`n`n",$squares,translate_inline($squares>1?"Squares":"Square"),$remainsize);
		break;
	}
}
?>