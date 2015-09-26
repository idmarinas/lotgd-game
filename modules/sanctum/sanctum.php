<?php
	global $session;
	$op2 = httpget('op2');
	$op = httpget('op');
	page_header("Order of the Inner Sanctum");
	output("`n`c`b`&Order of the Inner Sanctum`c`b`n");
if ($op=="") {
	if (get_module_pref("member")==-1){
		if (getsetting("installer_version","-1")=="1.1.1 Dragonprime Edition"){
			$innname=getsetting("innname", LOCATION_INN);
		}else{
			$innname=translate_inline("The Boar's Head Inn");
		}
		output("`&After a somewhat prolonged search you find a nondescript building tucked away in a back alley. You look up to notice that this is a huge mansion, but the subtle architecture makes it seem less imposing.");
		output("You step back and the thought crosses your mind that it may even be larger than the palace itself. `n`n With a small tremor, you pull out your `&I`)nvitation `&S`)croll`& and approach the door.`n`n");
		output("`&Before you even get a chance to knock, you are greeted at the door by a beautiful woman wearing a flowing black robe.");
		output("She guides you to a little tiny side room and has you sit down on a large cushioned chair. You take a moment to look around at her stark office.");
		output("You notice a subtle tattoo on her wrist with beautiful calligraphy. After staring at it for several seconds, you decipher that it says:`n`n");
		output("`c`b`&O`)I`&S`b`c`n Before you get a chance to ask any questions, she catches your gaze.`n`n`7'Welcome,`^ %s`7.",$session['user']['name']);
		output("I see you've noticed my tattoo. I'll explain that to you in good time. Perhaps introductions are in order though.");
		output("My name is `&N`)armyan`7. I am the `&H`)ead `&M`)istress `7of `&The Order of the Inner Sanctum`7.'");
		output("`n`n `&You reach to show her the `&I`)nvitation `&S`)croll`& you received in `5%s`& but she shakes her head softly.",$innname);
		output("`7'You don't need that. I know who you are. You are here because you have received that invitation to join our `&O`)rder`7 by your show of intelligence.'`n`n");
		output("`7'I would like to tell you more, but due to the nature of our organization, all I can do at this point is offer you membership.");
		output("There are no fees or dues for membership. But to be honest, there are other costs.");
		output("You will learn about those in good time.'`n`n'This offer will not come often. It's time for you to decide. Would you like to join?'");
		addnav("Yes, I will `&J`)oin","runmodule.php?module=sanctum&op=accept");
		addnav("No, I will `&N`)ot `&J`)oin","runmodule.php?module=sanctum&op=decline");
	}elseif (get_module_pref("member")>0){
		output("`&You enter into the L`)ounge`& area and have a sip of wine with other members.`n`n");
		output("`7`c'Please welcome the newest member to our `&O`)rder`7, %s.'`n`n`c",get_module_setting("newestmember"));
		require_once("lib/commentary.php");
		addcommentary();
		viewcommentary("sanctum","`&S`)peak `&F`)reely`&, you are amongst friends.",20,"says");
		addnav("The Order");
		addnav("`&Membership List","runmodule.php?module=sanctum&op=memberlist");
		modulehook ("sanctum-enter"); 
		addnav("Return");
		villagenav();
	}else{
		output("`@You have somehow stumbled by a strange building and you knock on the door.");
		output("Nobody answers, so you decide to leave.");
		villagenav();
	}
}
if ($op=="accept") {
	output("`&You barely hesitate before you stand up proudly to accept the offer.`n`nA slow smile comes across `&N`)armyan`&'s face and she takes your hand and walks you down a series of long corridors.");
	output("Soon you are lost in the maze of hallways. `&N`)armyan`& opens a door and before you know what's happening, you find yourself sitting on a chair with your legs and hands bound.");
	output("`n`n`7'Unfortunately, one of the most painful costs of membership is the tattoo you noticed on my hand earlier. Unlike other tattoos, the ink we use is a little, how can I explain this?");
	output("Well, it's a little more `4painful`7 than usual.'`n`n`&A strange man with a VERY mean looking needle starts to jab deep into your hand. The pain is unbearable, and before you can scream, you lose consciousness.`n`n");
	increment_module_setting("sanctumnum",1);
	set_module_setting("newestmember",$session['user']['name']);
	set_module_pref("member",get_module_setting("sanctumnum"));
	set_module_pref("tatpain",get_module_setting("healnumber"));
	$session['user']['hitpoints']=1;
	addnav("`&T`)he `&T`)attoo","runmodule.php?module=sanctum&op=tattoo");
}
if ($op=="tattoo") {
	//adopted from Shannon Brown's Petra's Tattoo parlor
	output("`&After what may have been minutes or hours, you wake to find yourself lying on a luxurious bed. Disoriented and alarmed, you sit up and notice that `&N`)armyan `&is close by.");
	output("`n`n`7'The tattoo will take a little while to heal. And for the next %s days when you wake, it will remind you of the price our `&O`)rder`7 requires for membership.",get_module_pref("tatpain"));
	output("I offer no apologies for this. I suffered through the same, as did all other members. You will wear this mark with pride as it will remind you that you are one of the elite. If you ever lose membership then your tattoo will magically fade.'`n`n");
	output("`&You look down at your hand and notice the same tattoo on your hand that you saw on `&N`)armyan`&.`n`n`c`b`&O`)I`&S`b`c`n");
	output("`&She pauses and presses her hand firmly against your tattoo, sending a wave of`\$ unbearable pain`& through you. Before you lose consciousness once again, you hear `&N`)armyan`& whisper into your ear...");
	output("`n`n`7'Welcome to `&The Order of the Inner Sanctum`7.'`n`n`&The `\$pain`& reduces your `\$hitpoints to one`&.");
	output("`n`nWill this be worth it?");
	addnav("`&T`)he `&P`)arlor","runmodule.php?module=sanctum");
	debuglog("received a tattoo to the sanctum and became a member of the order.");
}
if ($op=="memberlist") {
	global $session;
	$pp = 40;
	$page = httpget('page');
	$pageoffset = (int)$page;
	if ($pageoffset > 0) $pageoffset--;
	$pageoffset *= $pp;
	$limit = "LIMIT $pageoffset,$pp";
	$sql = "SELECT COUNT(*) AS c FROM " . db_prefix("module_userprefs") . " WHERE modulename = 'sanctum' AND setting = 'member' AND value > 0";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$total = $row['c'];
	$count = db_num_rows($result);
	if (($pageoffset + $pp) < $total){
		$cond = $pageoffset + $pp;
	}else{
		$cond = $total;
	}
	$sql = "SELECT ".db_prefix("module_userprefs").".value, ".db_prefix("accounts").".name FROM " . db_prefix("module_userprefs") . "," . db_prefix("accounts") . " WHERE acctid = userid AND modulename = 'sanctum' AND setting = 'member' AND value > 0 ORDER BY (0-value) DESC $limit";
	$result = db_query($sql);
	$rank = translate_inline("Number");
	$name = translate_inline("Name");
	$none = translate_inline("No Members");
	rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
	rawoutput("<tr class='trhead'><td>$rank</td><td>$name</td></tr>");
	if (db_num_rows($result)==0) output_notl("<tr class='trlight'><td colspan='3' align='center'>`&$none`0</td></tr>",true);
	else{
		for($i = $pageoffset; $i < $cond && $count; $i++) {
			$row = db_fetch_assoc($result);
			if ($row['name']==$session['user']['name']){
			rawoutput("<tr class='trhilight'><td>");
		}else{
			rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td>");
		}
		$j=$i+1;
		output_notl("$j.");
		rawoutput("</td><td>");
		output_notl("`&%s`0",$row['name']);
		rawoutput("</td></tr>");
		}
	}
	rawoutput("</table>");
	if ($total>$pp){
		addnav("Pages");
		for ($p=0;$p<$total;$p+=$pp){
			addnav(array("Page %s (%s-%s)", ($p/$pp+1), ($p+1), min($p+$pp,$total)), "runmodule.php?module=sanctum&op=memberlist&page=".($p/$pp+1));
		}
	}
	addnav("The Order");
	addnav("`&The Lounge","runmodule.php?module=sanctum");
	addnav("Leave");
	villagenav();
}
if ($op=="decline") {
	output("`&You explain that you're really not into this kinda thing.");
	output("You're sorry, you've made a mistake.`n`n");
	output("`&N`)armyan`& doesn't seem too disappointed.");
	output("`n`n`7'Well, some people aren't really worthy to be here anyways.");
	output("You just go on your merry way.");
	output("Perhaps if you ever get another chance, you'll reconsider.'");
	addnav("Leave");
	villagenav();
	set_module_pref("member",0);
	debuglog("declined membership into the Order of the Inner Sanctum.");
}
page_footer();
?>