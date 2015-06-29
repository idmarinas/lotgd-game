<?php
function metalmine_buycanary(){
	global $session;
	$op2 = httpget('op2');
	$op3 = httpget('op3');
	$allprefs=unserialize(get_module_pref('allprefs'));
	addnav("General Store");
	output("`n`c`b`&General `)Store`0`c`b`n");
	$subop = httpget('subop');
	$submit1= httppost('submit1');
	$canaryset=get_module_setting("canary");
	if ($op2=="buy"){
		if ($session['user']['gold']<$canaryset) redirect("runmodule.php?module=metalmine&op=canaryshort");
		else{
			output("You pay Grober `^%s gold`0 for the canary and he looks at you happily.",$canaryset);
			output("`Q'What would you like to name the little crooner?'");
			$session['user']['gold']-=$canaryset;
		}
	}elseif ($op2=="change") output("`Q'Go ahead and pick a new name for your canary.'"); 
	if ($op3==""){
		if ($subop!="submit1"){
			if ($op2=="") output("`Q'Go ahead and pick a name for your canary.'");
			$submit1 = translate_inline("Submit");
			rawoutput("<form action='runmodule.php?module=metalmine&op=buycanary&subop=submit1' method='POST'><input name='submit1' id='submit1'><input type='submit' class='button' value='$submit1'></form>");
			addnav("","runmodule.php?module=metalmine&op=buycanary&subop=submit1");	
			addnav("Submit","runmodule.php?module=metalmine&op=buycanary");
			addnav("Use Default 'Bird'","runmodule.php?module=metalmine&op=buycanary&op3=Bird");
		}else{
			if ($submit1==""){
				output("`Q'Since you did not submit a name I will offer you a chance to try again.  Otherwise, you may just go with the default name of `^'Bird'`Q.'");
				addnav("Try Again","runmodule.php?module=metalmine&op=buycanary");
				addnav("Use Default 'Bird'","runmodule.php?module=metalmine&op=buycanary&op3=Bird");
			}elseif ($submit1=="None" || $submit1=="none" || $submit1=="NONE"){
				output("`Q'Haha. Yeah, that's hilarious.  Call the bird the same thing as what you see in your inventory; then you never know if you really have a bird or not.'");
				output("`n`n'I'm even funnier.  I'm going to name your bird `^'Haha'`Q.'");
				addnav("Continue","runmodule.php?module=metalmine&op=buycanary&op3=Haha");
			}elseif ($submit1!="default"){
				output("`Q'Is this the name you wish to use?'");
				output("`n`n`c`^'%s`^'`c",$submit1);
				addnav("Yes","runmodule.php?module=metalmine&op=buycanary&op3=".rawurlencode($submit1));
				addnav("No","runmodule.php?module=metalmine&op=buycanary");
			}else{
				output("`Q'Since you're not sure what to name the canary, we'll just call it `^'Bird'`Q.'");
				addnav("Yes","runmodule.php?module=metalmine&op=buycanary&op3=Bird");
			}
		}
	}else{
		output("`0You look at `^%s`0 and it gives you a pleasant little chirp.  You smile and thank Grober.",$op3);
		$allprefs['canary']=$op3;
		set_module_pref('allprefs',serialize($allprefs));
		blocknav("runmodule.php?module=metalmine&op=canary");
		metalmine_storenavs();
	}
}
?>