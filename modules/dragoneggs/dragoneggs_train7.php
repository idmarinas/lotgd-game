<?php
function dragoneggs_train7(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Bluspring's Warrior Training");
	output("`c`b`7Bluspring's Warrior Training`b`c");
	if ($op2==1){
		output("You see a gem in the tablet and hope to grab it. You start to pronounce some of the words on the tablet in order to distract the others:`n`n");
		$chance=e_rand(1,9);
		$level=$session['user']['level'];
		if (($level>10 && $chance<=4) || ($level<=10 && $chance<=3)){
			output("`i`3'Krow Dlouhs Leps Siht!!'`i`7 you proclaim.");
			output("`n`nThe spell works!! Everyone looks away and you `%grab the gem`7!");
			$session['user']['gems']++;
			debuglog("gained a gem by researching at the Bluspring's Warrior Training.");
		}else{
			output("`i`3'Epon Tub Krow Dlouhs Leps Siht!!'`i`7 you proclaim.");
			output("`n`nWhoops! That wasn't right. Everyone stares at you! You're `iCursed`i!!");
			if ($session['bufflist']['blesscurse']['atkmod']==0.8) {
				$session['bufflist']['blesscurse']['rounds'] += 5;
				debuglog("increased their curse by 5 rounds by researching at the Bluspring's Warrior Training.");
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
				debuglog("received a curse by researching at the Bluspring's Warrior Training.");
			}
		}
	}else{
		output("Having more pressing things to do, you excuse yourself.");
	}
	addnav("Continue at the Bluspring's Warrior Training","train.php");
	villagenav();
}
?>