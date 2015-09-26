<?php
function dragoneggs_bank7(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Ye Olde Bank");
	output("`c`b`^Ye Olde Bank`b`c`6");
	if ($op2<5){
		$chance=e_rand(1,3);
		output("He flips the Fortune Teller and then suddenly stops.");
		if ($chance<3){
			output("`#'You're `iBlessed`i!'`6 he says.");
			if ($session['bufflist']['blesscurse']['atkmod']==1.2) {
				$session['bufflist']['blesscurse']['rounds'] += 5;
				debuglog("increased their blessing by 5 rounds by researching at the Bank.");
			}else{
				apply_buff('blesscurse',
					array("name"=>translate_inline("Blessed"),
						"survivenewday"=>1,
						"rounds"=>15,
						"wearoff"=>translate_inline("The burst of energy passes."),
						"atkmod"=>1.2,
						"defmod"=>1.1,
						"roundmsg"=>translate_inline("Energy flows through you!"),
					)
				);
				debuglog("received a blessing by researching at the Bank.");
			}
		}else{
			if ($session['bufflist']['blesscurse']['atkmod']==0.8) {
				$session['bufflist']['blesscurse']['rounds'] += 5;
				debuglog("increased their curse by 5 rounds by researching at the Bank.");
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
				debuglog("received a curse by researching at the Bank.");
			}
		}
	}else{
		output("You refuse to participate and he shrugs.");
	}
	addnav("Continue Banking","bank.php");
	villagenav();
}
?>