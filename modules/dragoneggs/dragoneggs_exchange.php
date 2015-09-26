<?php
function dragoneggs_exchange(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	$name=color_sanitize(get_module_setting("found"));
	page_header("The %s Gem Exchange",$name);
	output("`c`b`&The %s`& Gem Exchange`b`c`n`2",get_module_setting("found"));
	if ($op2==""){
		output("You enter a pleasant little shop.  Sitting at a desk is a young gemologist.");
		output("`n`n`6'Hello! I'm gathering gems for some jewelry I want to make.  If you have some gems, I'd be willing to pay top gold for them,'`2 he says.");
		output("You ask for what he's paying and he points to the bulletin board.`n`n");
		rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
		rawoutput("<tr class='trhead'><td>");
		output("`c`b`@Gem Exchange`c`n`b");
		rawoutput("</td></tr>");
		rawoutput("<tr class='trhilight'><td>");
		output("`@1. Only gems from experienced players are accepted. In other words, if you're not at least `^level %s`@, don't bother asking to exchange your gems.  There has been fraudulent activity reported from inexperienced warriors. My apologies for this policy.`n`n",get_module_setting("minlevel"));
		output("2. Prices may vary between `^%s gold`@ and `^%s gold`@ based on my current demand.`n`n",get_module_setting("minvalue"),get_module_setting("maxvalue"));
		output("3. I usually only need `%%s gems`@ from each warrior every day.`n`n",get_module_setting("maxperday"));
		output("4. I will probably only be open for another `^%s days`@.`n`n",get_module_setting("left"));
		output("5. If you are level 15, you should be out hunting down the Green Dragon not exchanging gems. No sales if you are level 15!");
		rawoutput("</td></tr>");
		rawoutput("</table>");
		addnav("Exchange Gems","runmodule.php?module=dragoneggs&op=exchange&op2=cont1");
		villagenav();
	}elseif ($op2=="cont1"){
		if ($session['user']['level']<get_module_setting("minlevel")){
			output("`6'Nope.  I'm not taking gems from someone with so little experience.  Come back when you've gained some levels,'`2 says the jeweler.");
		}elseif ($session['user']['level']>=15){
			output("`6'Hey! I hear a `@Green Dragon`6 calling your name.  Go save the Kingdom!'`2 the jeweler says.");
		}elseif ($session['user']['gems']<=0){
			output("The jeweler gives you a strange look. `6'Well, you don't have any gems. I don't think you can help me,'`2 he says.");
		}elseif (get_module_pref("sold")>=get_module_setting("maxperday")){
			output("The jeweler reminds you that you've already exchanged your maximum number of gems today. `6'Come back tomorrow!'`2 he says.");
		}else{
			$left=get_module_setting("maxperday")-get_module_pref("sold");
			$level=$session['user']['level']-get_module_setting("minlevel");
			$value=min(get_module_setting("minvalue")+50*$level,get_module_setting("maxvalue"));
			output("`6'How many gems would you like to give me?'`2 he asks.");
			output("`n`nHe's paying `^%s gold`2 for each gem. He'll buy up to `%%s %s`2 from you today.",$value,$left,translate_inline($left>1?"gems":"gem"));
			output("<form action='runmodule.php?module=dragoneggs&op=exchange&op2=cont2&op3=$value' method='POST'><input name='sell' id='sell'><input type='submit' class='button' value='Exchange'></form>",true);
			addnav("","runmodule.php?module=dragoneggs&op=exchange&op2=cont2&op3=$value");
		}
		villagenav();
	}elseif ($op2=="cont2"){
		$sell = httppost('sell');
		if ($sell<=0) $sell=0;
		if ($sell>get_module_setting("maxperday")-get_module_pref("sold")) $sell=get_module_setting("maxperday")-get_module_pref("sold");
		if ($sell>$session['user']['gems']) $sell=$session['user']['gems'];
		if ($sell==0){
			output("`6'Yes, I can use no gems.  In return, I give you no gold.  That was a great exchange!'`2 he says.  You sense a little sarcasm.");
		}else{
			increment_module_pref("sold",$sell);
			$pay=$op3*$sell;
			$session['user']['gems']-=$sell;
			$session['user']['gold']+=$pay;
			output("You give `%%s %s`2 and receive `^%s gold`2.  You feel like it was a good exchange.",$sell,translate_inline($sell>1?"gems":"gem"),$pay);
			debuglog("gained $pay gold in exchange for $sell gems from the Exchange Store.");
		}
		addnav("Return to the Entrance","runmodule.php?module=dragoneggs&op=exchange");
		villagenav();
	}
}
?>