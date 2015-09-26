<?php
function dragoneggs_bank5(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Ye Olde Bank");
	output("`c`b`^Ye Olde Bank`b`c`6");
	if ($op2==1){
		output("You succeed at helping in the con.  The gang members slip you a cut.");
		$bank=$session['user']['level']*e_rand(5,24);
		output("You slip out of the bank `^%s gold`6 richer!",$bank);
		$session['user']['gold']+=$bank;
		debuglog("gained $bank gold by conning at the Bank.");
	}else{
		output("You refuse to participate.");
		$chance=e_rand(1,5);
		if ($chance<4){
			$bank=$session['user']['level']*e_rand(10,20);
			$session['user']['gold']+=$bank;
			output("The gang members don't notice that you're not participating and actually slip you `^%s gold`6 as your part of the cut!");
		}elseif($chance==4){
			output("The gang members don't notice you.  They continue with the con unhindered.");
		}else{
			output("The gang members are mad that you didn't help. They revoke your membership in the gang.");
			set_module_pref("member","sheldon",0);
		}
		addnav("Continue Banking","bank.php");
	}
	villagenav();
}
?>