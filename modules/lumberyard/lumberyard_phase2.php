<?php
function lumberyard_phase2(){
	global $session;
	output("`n`c`b`QT`qhe `QL`qumber `QY`qard `QP`qhase `Q2`0`c`b`n");
	$allprefs=unserialize(get_module_pref('allprefs'));
	$allprefs['phase']=3;
	$allprefs['usedlts']=$allprefs['usedlts']+1;
	set_module_pref('allprefs',serialize($allprefs));
	$session['user']['turns']--;
	
	$loc = city_id();
	$allprefs=unserialize(get_module_pref('allprefs'));
	output("`^You grab the tree that you chopped down and start to drag it to the mill.`n`n");
	switch(e_rand(1,20)){
		case 1: case 2: case 3: case 4: case 5: case 6: case 7: case 8: case 9:
			output("`^Taking the tree to the mill turns out to be quite a workout. But that's what you came here for.`n`n");
			output("You've completed `QPhase 2`^ of work in the lumber yard. It only took you `@one turn`^.");
			debuglog("completed Phase 2 in the Lumberyard.");
		break;
		case 10:
			output("`^You drag the tree back to the mill and get there and smile. You are so cool! You look at your hands and see that you have huge blisters from dragging the log.");
			output("You can't go fight monsters with such nasty blisters. You sit down on the log for a couple turns as your hands heal. `n`nYou `@lose 2 turns`^ but complete `QPhase 2`^.");
			$session['user']['turns']--;
			debuglog("completed Phase 2 in the Lumberyard but had to spend an extra turn healing.");
			
			//-- Datos para los hook pierde un turno extra por las ampollas
			return array(
				'turns' => -1
			);
		break;
		case 11:
			output("`^You know how frustrating it is when you are dragging a huge log and it gets stuck? You do now!");
			output("`n`nYou `@lose an extra turn`^ getting the tree to the mill, but you've completed `QPhase 2`^ of work.");
			$session['user']['turns']--;
			debuglog("completed Phase 2 in the Lumberyard but had to spend an extra turn with a log.");
			
			//-- Datos para los hook pierde un turno extra porque el tronco era muy grande
			return array(
				'turns' => -1
			);
		break;
		case 12:
			output("`^Dragging logs comes naturally to you. You perform the task in record speed, feeling like you have enough energy to `@fight more in the forest`^ in addition to completing `QPhase 2`^ of work!");
			$session['user']['turns']+=2;
			debuglog("completed Phase 2 in the Lumberyard and gained 2 turns for being so efficient.");
			
			//-- Datos para los hook gana dos turnos extra
			return array(
				'turns' => 1
			);
		break;
		case 13:
			output("`^The log gets lodged under your foot and you are trapped. Not only is this horribly embarrassing, but	it is very costly of your time.");
			$turns = 1;
			if ($session['user']['turns']==1) {
				output("`n`nYou `@lose 2 forest turns");
				$session['user']['turns']--;
				debuglog("completed Phase 2 in the Lumberyard and lost a turn.");
			}elseif ($session['user']['turns']>1) {
				output("`n`nYou `@lose 3 forest turns");
				$session['user']['turns']-=2;
				$turns = 2;
				debuglog("completed Phase 2 in the Lumberyard and lost 2 turns");
			}else{
				output("`n`nYou waste your time");
				debuglog("completed Phase 2 in the Lumberyard.");
				$turns = 0;
			}
			output("`^getting your foot out and you aren't able to complete this phase of work.");
			$allprefs['phase']=2;
			set_module_pref('allprefs',serialize($allprefs));
			
			//-- Datos para los hook pierde entre 1 y 2 turnos
			return array(
				'turns' => -$turns
			);
		break;
		case 14:
			output("Quickly you drag the log and zip through the mill and cut your wood down into 2 squares.");
			output("`n`nThe foreman	comes over and smiles at your handiwork and gives you a voucher for one square of wood.");
			output("`n`nYou completed `QTwo Phases`^ in the time that it usually takes to complete one!`n`n");
			// increment_module_setting("remainsize",-1);
			increment_module_objpref("city", $loc, "remainsize", -1, "lumberyard");
			$remainsize=get_module_objpref("city",$loc,"remainsize","lumberyard");
			$allprefs['phase']=1;
			$allprefs['squares']=$allprefs['squares']+1;
			$allprefs['squareshof']=$allprefs['squareshof']+1;
			set_module_pref('allprefs',serialize($allprefs));
			$squares=$allprefs['squares'];
			output("`^You now have`& %s %s of Wood`^.`n`n`^There are now`6 %s trees `^left in the forest.`n`n",$squares,translate_inline($squares>1?"Squares":"Square"),$remainsize);
			debuglog("completed Phase 2 and 3 in the Lumberyard all at once gaining a square of wood.");
			
			//-- Datos para los hook completa la fase 2 y 3 consiguiendo la madera
			return array(
				'event' => 'complete-phases-2-3',
				'wood' => 1
			);
		break;
		case 15:
			$crushgold=get_module_setting("crushgold");
			$crushgem=get_module_setting("crushgem");
			output("You drag the tree a short distance and notice something shiny. It looks like the spot where an old	adventure got crushed by a tree. You gather");
			if ($crushgold>0) output("`b%s gold`b",$crushgold);
			if ($crushgold>0 && $crushgem>0) output("and");
			if ($crushgem==1) output("`%one gem`^");
			elseif ($crushgem>1) output("`%%s gems`^",$crushgem);
			output("from around the tree. Although you forget to finish this phase for the day, your jingling pockets make you feel better.");
			$allprefs['phase']=2;
			set_module_pref('allprefs',serialize($allprefs));
			$session['user']['gold']+=$crushgold;
			$session['user']['gems']+=$crushgem;
			debuglog("didn't finish Phase 2 in the Lumberyard but gained about $crushgold gold and $crushgem gems.");
			
			//-- Datos para los hook encuentra oro y/o gemas al arrastrar el tronco
			return array(
				'event' => 'find-a-crushpresent',
				'gold' => $crushgold,
				'gems' => $crushgem
			);
		break;
		case 16:
			$expbonus=$session['user']['dragonkills']*3;
			$expgain =($session['user']['level']*17+$expbonus);
			$session['user']['experience']+=$expgain;
			output("As you drag the tree along, you find a patch of flowers and stop to smell them. Although you aren't able to finish dragging the tree to the mill, you gain experience because there's nothing more important in life than stopping to smell the`$ roses`^.`n`n");
			output("You have gained`# %s experience`^.",$expgain);
			$allprefs['phase']=2;
			set_module_pref('allprefs',serialize($allprefs));
			debuglog("didn't finish Phase 2 in the Lumberyard but gained $expgain experience.");
			
			//-- Datos para los hook se para para oler las flores
			return array(
				'event' => 'smell-flowers',
				'experience' => $expgain,
			);
		break;
		case 17;
			$expbonus=$session['user']['dragonkills']*4;
			$expgain =($session['user']['level']*22+$expbonus);
			$session['user']['experience']+=$expgain;
			$gnomegold=get_module_setting("gnomegold");
			if ($gnomegold<0) $gnomegold=0;
			$session['user']['gold']+=$gnomegold;   
			output("You find several small creatures wandering through the forest. It turns out these are the very rare garden gnomes!!");
			output("They distract you from your task and take you to their hidden cave. They teach you the secrets of finances and how to become more efficient in everything you do.");
			output("You're on `QPhase 2`^... And then `QPhase 3`^ means Profit!! `n`nIn addition, one decides to follow you around so you can take pictures of him with you on your adventures.");
			output("(He won't add much to your strength, but you'll be able to send great pictures to friends with him in the foreground of every shot!)");
			if ($gnomegold>0) output("`n`nYou gain`b %s gold`b!`n",$gnomegold);
			output("You gain`% %s experience`^.`n`n",$expgain);
			output("Did you notice your new buff?`n");
			apply_buff('gardengnome',array(
				"name"=>"`$ Garden`!Gnome",
				"rounds"=>25,
				"wearoff"=>"`^The`$ Garden `!Gnome`^ returns to his home",
				"atkmod"=>1.06,
				"roundmsg"=>"`#The`$ Garden`! Gnome`# strikes a pose and you quickly take his picture",
			));
			debuglog("completed Phase 2 in the Lumberyard, gained a Garden Gnome buff, and gained $expgain experience.");
			
			//-- Datos para los hook se para para oler las flores
			return array(
				'event' => 'garde-gnomes',
				'experience' => $expgain,
				'gold' => $gnomegold
			);
		break;
		case 18; case 19;
			addnav("Bear`$ Fight","runmodule.php?module=lumberyard&op=attack");
			require_once("modules/lumberyard/lumberyard_blocknavs.php");
			lumberyard_blocknavs();
			blocknav("forest.php");
			output("`^As you use all your might to drag the log to the saw mill, a `b`qG`^reat `qB`^ig `qB`^ear`b surprises you! This looks bad for you!`n`n You're going to have to fight a bear!`n`n");
		break;
		case 20;
			output("You drag the tree right through a patch of `@poison ivy`^. `#(Remember!! Leaves of three, let them be!");
			output("Poison ivy is three shiny leaves on a vine or shrub.)`^`n`nWell, this`@ itch`^ is really going to bother you for a while.");
			output("Sorry!`n`nYou finish `QPhase 2`^ in `@one turn`^.");
			apply_buff('poisonivy',array(
				"name"=>"`@Poison Ivy",
				"rounds"=>30,
				"wearoff"=>"The anti-itch cream finally kicks in",
				"atkmod"=>.98,
				"defmod"=>.98,
				"roundmsg"=>"`@You stop to scratch an itch",
				"survivenewday"=>1,
				"newdaymessage"=>"`n`@The poison ivy itch didn't clear up yet.`n",
			));
			debuglog("completed Phase 2 in the Lumberyard but got the Poison Ivy Buff.");
			
			//-- Datos para los hook se encuentra un hiedra venenosa
			return array(
				'event' => 'poisonivy',
			);
		break;
	}
}
?>