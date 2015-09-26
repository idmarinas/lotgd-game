<?php
function dragoneggs_town13(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	$vname = getsetting("villagename", LOCATION_FIELDS);
	$header = color_sanitize($vname);
	page_header("%s Square",$header);
	output("`c`b`@%s Square`@`b`c",$vname);
	if ($op2==""){
		$hps=$session['user']['hitpoints'];
		$level=$session['user']['level'];
		$chance=e_rand(1,5);
		if (($level>6 && $chance<=3) || ($level<=6 && $chance<=2)){
			output("`%'Perhaps you'd be interested in some of our wares?'`@ pipes in a beefy looking gypsy.`n`n");
			if (($session['user']['gold']+$session['user']['goldinbank'])>=10000 && $session['user']['weapon']!="Power Wand"){
				output("He pulls out a very precious Power Wand.");
				output("`n`n`%'This is a `iPower Wand`i,'`@ he explains, `%'Only `^10,000 gold`% and it's one of the greatest wands you've ever seen.'`@");
				output("`n`nYou examine it and realize it's amazing and after doing some mental calculation you realize it's probably at least an attack of `&20`@.");
				output("`n`nThe gypsy notices your interest. `%'We can do a direct withdrawal from your bank account if you don't have enough cash on hand,'`@ he offers.");
				output("`n`nAre you interested?");
				addnav("Purchase the Power Wand","runmodule.php?module=dragoneggs&op=town13&op2=buy");
			}else{
				output("`%'Well, I have this writing Quill if you're interested.  Only `^10,000 gold`%.  After all, the pen is mightier than the sword.'");
				output("`n`n`@Not having much trust for the gypsy, you leave to continue your research.");
			}
		}else{
			output("`$'Would you like to talk to MEEEeeee!'`@ yells an old gypsy lady.");
			output("`n`nShe gives you an evil eye and you're `iCursed`i.");
			if ($session['bufflist']['blesscurse']['atkmod']==0.8) {
				$session['bufflist']['blesscurse']['rounds'] += 5;
				debuglog("increased their curse by 5 rounds by researching at the Town Square.");
			}else{
				apply_buff('blesscurse',
					array("name"=>translate_inline("Cursed"),
						"rounds"=>15,
						"survivenewday"=>1,
						"wearoff"=>translate_inline("The burst of energy passes."),
						"atkmod"=>0.8,
						"defmod"=>0.9,
						"roundmsg"=>translate_inline("Dark Energy flows through you!"),
					)
				);
				debuglog("received a curse by researching at the Town Square.");
			}
		}
	}elseif ($op2=="buy"){
		output("Knowing a bargain when you see one, you decide to buy the wand.`n`n");
		if ($session['user']['gold']>=10000){
			output("You hand over the `^10,000 gold`@ from the money you have on hand.");
			$session['user']['gold']-=10000;
			debuglog("bought a Power Wand (Attack 20) for 10000 gold from a gypsy by researching at the Capital Town Square.");
		}else{
			$gold=$session['user']['gold'];
			$pay=10000-$session['user']['gold'];
			output("You hand over `^%s gold`@ from your bag and sign a withdrawal slip for `^%s gold`@ from your bank account.",$gold,$pay);
			$session['user']['gold']-=$gold;
			$session['user']['goldinbank']-=$pay;
			debuglog("bought a Power Wand (Attack 20) for $gold gold and $pay goldinbank from a gypsy by researching at the Capital Town Square.");
		}
		output("`n`nYou proudly display your `^Power Wand`@ and return to %s Square`@.",$vname);
		$session['user']['attack']-=$session['user']['weapondmg'];
		$session['user']['weapon']="Power Wand";
		$session['user']['weaponvalue']=10000;
		$session['user']['weapondmg'] = 20;
		$session['user']['attack']+=20;
	}
	villagenav();
}
?>