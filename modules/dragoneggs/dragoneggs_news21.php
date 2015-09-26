<?php
function dragoneggs_news21(){
	global $session;
	page_header("LoGD News");
	output("`n`c`b`!Daily News`b`c`2");
	output_notl("`c`2-=-`@=-=`2-=-`@=-=`2-=-`@=-=`2-=-`0`c");
	addnews("`2A new shop has opened up in town called `&`i%s's `&Gem Exchange`2. Take a look and see what it's all about!",$session['user']['name']);
	output("You decide to prove to the reporter that there is a serious problem in the Kingdom by giving him a `&Dragon Egg Point`2.");
	increment_module_pref("dragoneggs",-1,"dragoneggpoints");
	output("`n`nHe takes a look at it and snaps his fingers. `6'Oh my! Yes, there seems to be a storm brewing.  I'm glad you showed this to me.  I'll open a shop in town right away and I'm going to name it after you!'`7 he exclaims.");
	set_module_setting("left",get_module_setting("open"));
	set_module_setting("found",$session['user']['name']);
	addnav("Return to the Daily News","news.php");
	villagenav();
}
?>