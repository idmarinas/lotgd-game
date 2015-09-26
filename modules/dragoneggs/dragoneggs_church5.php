<?php
function dragoneggs_church5(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Old Church");
	$rand=0;
	if (e_rand(1,2)==1) $rand++;
	$level=$session['user']['level'];
	$chance=e_rand(1,5);
	if (($level>8 && $chance<=3) || ($level<=8 && $chance<=2)) $rand++;
	if ($op2==1){
		if ($rand==0) output("'DEMON! From a child? Oh how could you have!'");
		elseif ($rand==1) output("'Hmmm.  I have heard of worse sins.  But to take from the innocent is pretty bad,'");
		else output("'Ah, I know how you feel.  When they have good candy, it's so tempting.  It's really not that bad,'");
	}elseif ($op2==2){
		if ($rand==0) output("'IT WAS YOU?? Oh, lord above, there is nothing worse.  You could have poked your eye out!,");
		elseif ($rand==1) output("'Running with scissors, eh? I would have been very mad if you had fallen and stabbed someone,'");
		else output("'Eh, that's not so bad.  I've known to run with scissors once in a while in my day.  Don't feel too guilty about that one,'");
	}elseif ($op2==3){
		if ($session['user']['sex']==1){
			output("'EWWWW!!! You're a WOMAN! What are you doing with the toilet seat up?'");
			$rand=0;
		}else{
			if ($rand==0) output("'There is a special place in Hell just for you.  I am going to struggle with forgiving you for this. Have you no respect for women??'");
			elseif ($rand==1)output("'If that's the worst thing you're doing then it's not that bad.  Just try to put the darn seat down for heaven's sake,'");
			else output("'Bah! I don't even know if that's a sin.  I mean, maybe women need to realize that the natural position for the toilet seat is UP!'");
		}
	}else{
		if ($rand==0) output("'You are disgusting. How do you live with yourself? Do you even know what bacteria are?'");
		elseif ($rand==1) output("'Well, I personally think that you're going to die for that, but what do I know about staying healthy?'");
		else output("'Five seconds? I think it should be a 10 second rule.  After all, God made dirt, so dirt don't hurt!'");
	}
	output("`3 says `5Capelthwaite`3.");
	if ($rand==0){
		output("`^'Please leave the church.  Your soul is too unclean to be in such a holy place,'`3 proclaims `5Capelthwaite`3. He hits you on the head with a cross causing you to `\$lose all your hitpoints except one`3.`n`n `^'Oh, and say a couple 'Hail Mary's and you'll be absolved of your sins,'`3 he adds as he ushers you out.");
		blocknav("runmodule.php?module=dragoneggs&op=oldchurch");
		$session['user']['hitpoints']=1;
		debuglog("Lost all hitpoints except 1 during confessional while researching dragon eggs at the Church.");
	}elseif ($rand==1){
		output("`^'Well, say a couple of 'Our Fathers' and you will be returned into God's grace,' `3 he adds.");
		output("`n`nHe ushers you out of the church.");
	}else{
		output("`^'I'm impressed if that's the only thing you have done wrong.  I have something that may help you,'`3 he says as he hands you a `%gem`3.");
		$session['user']['gems']++;
		debuglog("gained a gem while researching dragon eggs at the Church.");
		addnav("Return to the Church","runmodule.php?module=oldchurch");
	}
	villagenav();
}
?>