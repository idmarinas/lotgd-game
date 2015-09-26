<?php
function dragoneggs_bank27(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Ye Olde Bank");
	output("`c`b`^Ye Olde Bank`b`c`6");
	if ($op2>1){
		if (e_rand(1,2)==1){
			if ($op2==50) $charm=1;
			elseif ($op2==100) $charm=2;
			elseif ($op2==200) $charm=3;
			else $charm=4;
			output("He takes your gold and happily accepts your gift.");
			output("You gain `&%s charm`6.",$charm);
			debuglog("received $charm charm points for giving $op2 gold by researching at the Bank.");
			$session['user']['gold']-=$op2;
			$session['user']['charm']+=$charm;
		}else{
			output("He accepts your gift and smiles, opening his jacket to reveal a very expensive suit hidden underneath.`n`n");
			output("`@'I like to test the generosity of the people sometimes.  Your reward is your gift back right back to you as an equal gift.'");
			$session['user']['gold']+=$op2;
			debuglog("received $op2 gold by researching at the Bank.");
		}
	}else{
		if (e_rand(1,3)==1 && $session['user']['gems']>0){
			output("`@'That's okay,'`6 he says, `@'You're wasting your time.  There's no way to prevent the Green Dragon from coming.'");
			output("`n`n`6His prophecy makes you pause... he snags a gem! You `%lose a gem`6.");
			$session['user']['gems']--;
			debuglog("lost a gem researching dragon eggs at the Bank.");
		}else{
			output("You keep walking past as if you didn't hear anything.");
		}
	}
	addnav("Continue Banking","bank.php");
	villagenav();
}
?>