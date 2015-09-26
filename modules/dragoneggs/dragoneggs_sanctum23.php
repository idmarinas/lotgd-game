<?php
function dragoneggs_sanctum23(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Order of the Inner Sanctum");
	output("`n`c`b`&Order of the Inner Sanctum`b`c`7");
	if ($op2==1){
		output("You read the paragraph on banishing the `qWerebeast`7. The moon goes from full to new in seconds and a mystic energy issues forth.");
		output("`n`nYou have successfully cast the spell to banish the `qWerebeast`7. All that is left is a silver gem. You gain a `%gem`7.");
		$session['user']['gems']++;
		debuglog("gained a gem by banishing a Werebeast by researching at the Inner Sanctum.");	
	}elseif ($op2==2){
		output("You feel the air grow very dry; such that the `5Tsoitthian's`7 moist skin becomes dessicated.");
		if(e_rand(1,3)>1 || $session['user']['gems']<=0){
			output("`n`nYou have successfully cast the spell to banish the `5Tsoitthian`7. Each eye is a gem! You gain `%2 gems`7.");
			$session['user']['gems']+=2;
			debuglog("gained 2 gems by banishing a Tsoitthian by researching at the Inner Sanctum.");
		}else{
			output("`n`nYou fail to cast the spell correctly... The `5Tsoitthian`7 continues to haunt the kingdom. You lose a `%gem`7.");
			$session['user']['gems']--;
			debuglog("lost a gem by failing to banish a Tsoitthian by researching at the Inner Sanctum.");
		}
	}elseif ($op2==3){
		output("You finish reading the banishment spell for the `)Crysthose`7 and hear a loud `&<boom>`7 echo through the kingdom.");
		if ($session['user']['gold']>250){
			$exp=$session['user']['level']*15;
			$session['user']['experience']+=$exp;
			output("`n`nYou destroy the `)Crysthose`7 but have to pay `^250 gold`7 to repair the windows at the `&Order`7.");
			output("However, you also gain `#%s experience`7 for banishing it.",$exp);
			debuglog("gained $exp experience and paid 250 gold to banish a Crysthose by researching at the Inner Sanctum.");
		}else{
			output("`n`nAlthough you hope it works, there doesn't seem to be any evidence that it did.");
		}
	}elseif ($op2==4){
		output("You complete the spell and a light flashes out the window. The `1Mytrico`7 has been eliminated instantly.");
		output("`n`nYou turn the page to find a `^100 gold`7!");
		$session['user']['gold']+=100;
		debuglog("gained 100 gold by banishing a Mytrico by researching at the Inner Sanctum.");
	}elseif ($op2==5){
		output("You read the spell and hear the beating of wings.  Instead of banishing the `QYthilian`7, you've summoned it!");
		addnav("Fight the `QYthilian","runmodule.php?module=dragoneggs&op=sanctum235");
		blocknav("runmodule.php?module=sanctum");
		blocknav("village.php");
	}elseif ($op2==6){
		output("A picture of a `#Pricole`7 catches your eye but then you notice a huge Dog also in the book. You cast the spell to summon the faithful dog `qRex`7 to kill the `#Pricole`7.");
		output("`n`n`qRex`7 joins you to fight by your side!");
		if (isset($session['bufflist']['ally'])) {
			if ($session['bufflist']['ally']['type']=="rexdog"){
				$ally=1;
			}else{
				output("`n`nRealizing that you've found help from someone new, %s`7 decides to leave.",$session['bufflist']['ally']['name']);
				$ally=0;
			}
		}else $ally=0;
		if ($ally==0){
			apply_buff('ally',array(
				"name"=>translate_inline("`qRex the Dog"),
				"rounds"=>40,
				"wearoff"=>translate_inline("`qRex runs off at the sound of a car driving by."),
				"defmod"=>1.1,
				"survivenewday"=>1,
				"type"=>"rexdog",
			));
			output("`n`nYou gain the help of `qRex the Dog`7!");
			debuglog("gained the help of ally Rex the Dog by banishing a Pricole by researching at the Inner Sanctum.");
		}else{
			$session['bufflist']['ally']['rounds'] += 14;
			output("`n`n`qRex the Dog`7 decides to help you out for another `^14 rounds`7!");
			debuglog("gained the help of ally Rex the Dog for another 14 rounds by banishing a Pricole by researching at the Inner Sanctum.");
		}
		if (is_module_active("dlibrary")){
			if (get_module_setting("ally2","dlibrary")==0){
				set_module_setting("ally2",1,"dlibrary");
				addnews("%s`^ was the first person to meet `qRex the Dog`^ at the Inner Sanctum.",$session['user']['name']);
			}
		}
	}elseif ($op2==7){
		output("When you try to banish the `%Aatrithic`7, you hear one of the other members of the `&Order`7 walk in and try to stop you.");
		output("`n`n`&'No! If you banish the `%Aatrithic`& we will never be able destroy the dragon egg it will conjure,'`7 she tells you.");
		output("`n`nYou stop reading the spell and she thanks you. You `@gain 2 turns`7 by not spending a lot of time banishing the `%Aatrithic`7.");
		$session['user']['turns']+=2;
		debuglog("gained 2 turns when trying to banish an Aatrithic by researching at the Inner Sanctum.");
	}else{
		output("Fearing the `@Flaayer`7 due to its powers over memory, you banish it.");
		if(e_rand(1,3)>1){
			output("`n`nYou have successfully cast the spell to banish the `@Flaayer`7. You gain `@3 turns`7.");
			$session['user']['turns']+=3;
			debuglog("gained 3 turns by banishing a Flaayer by researching at the Inner Sanctum.");
		}else{
			output("`n`nPerhaps you didn't cast it correctly... Perhaps the spell affected your memory.  You `@spend a turn`7 pondering this before moving on.");
			$session['user']['turns']--;
			debuglog("lost a turn trying to banish a Flaayer by researching at the Inner Sanctum.");
		}
	}
	addnav("Return to the Order","runmodule.php?module=sanctum");
	villagenav();
}
?>