<?php
function dragoneggs_bath3(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("The Outhouse");
	output("`n`c`b`2The Outhouse`b`c`2`n");
	if ($op2==""){
		output("`&'For the low low price of only `%3 gems`& I can give you a potion that will make you stronger. You know you want it!'`2 says the stranger.");
		output("`n`nHmm... sounds almost too good to be true.  Will you take a chance?");
		addnav("Give 3 gems","runmodule.php?module=dragoneggs&op=bath3&op2=continue");
	}else{
		if ($session['user']['gems']<3){
			output("Not having `%3 gems`2 you decide to take a chance and pass off some bogus pieces of glass.`n`n");
			$chance=e_rand(1,9);
			$level=$session['user']['level'];
			if (($level>10 && $chance<=3) || ($level<=10 && $chance<=2)){
				output("`&'It's a deal!'`2 he says without evaluating the 'gems' very carefully.");
				output("`n`nHe hands a you a potion and you drink it down.  You `&gain 1 attack`2!");
				$session['user']['attack']++;
				debuglog("gained 1 attack while researching dragon eggs at the Outhouse.");
			}else{
				output("`&'Hey! Are you trying to swindle me? Nobody gets away with that!' `2 he says with an angry shout.`n`n");
				if ($session['user']['turns']>2){
					output("He starts to chase you and tries to hit you.  You spend `@3 turns`2 trying to evade him.");
					$session['user']['turns']-=3;
					debuglog("lost 3 turns while researching dragon eggs at the Outhouse.");
				}elseif ($session['user']['gold']>=1000){
					output("He grabs your wallet and steals `^1000 gold`2 and runs away.");
					$session['user']['gold']-=1000;
					debuglog("lost 1000 gold while researching dragon eggs at the Outhouse.");
				}else{
					output("He gives you the `\$evil eye`2! It's a hex that won't go away just with a new day.");
					if (isset($session['bufflist']['ievileye'])) {
						$session['bufflist']['ievileye']['rounds'] += 10;
						debuglog("increased the ievileye buff by 10 rounds while researching dragon eggs at the Outhouse.");
					}else{
						apply_buff('ievileye',
							array("name"=>translate_inline("Evil Eye"),
								"rounds"=>50,
								"wearoff"=>translate_inline("The hex fades"),
								"defmod"=>0.9,
								"survivenewday"=>1,
								"roundmsg"=>translate_inline("The weight of the Evil Eye weighs you down!"),
							)
						);
						debuglog("received the ievileye buff while researching dragon eggs at the Outhouse.");
					}
				}
			}
		}else{
			output("You hand over the `%3 gems`2 and he hands you a gooey green vial.");
			output("`n`nRealizing that you've already gone this far, you dring it down.");
			$chance=e_rand(1,9);
			$level=$session['user']['level'];
			if (($level>10 && $chance<=8) || ($level<=10 && $chance<=7)){
				output("Ahhh... that hits the spot.  You `&gain 1 attack`2.");
				$session['user']['gems']-=3;
				$session['user']['attack']++;
				debuglog("gained 1 attack for 3 gems while researching dragon eggs at the Outhouse.");
			}else{
				output("Hey! this is just maple syrup!");
				output("`n`nBefore you have a chance to react, he runs off.  Luckily, he drops `%2 gems`2 in his hasty retreat so you only end up losing 1.");
				$session['user']['gems']--;
				debuglog("lost 1 gem while researching dragon eggs at the Outhouse.");
			}
		}
	}
	addnav("Return to the Outhouse","runmodule.php?module=outhouse");
	addnav("Return to the Forest","forest.php");
}
?>