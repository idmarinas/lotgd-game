<?php

	tlschema("faq");
	popup_header("Multi-Village Questions");
	$c = translate_inline("Return to Contents");
	rawoutput("<a href='petition.php?op=faq'>$c</a><hr>");
	output("`n`n`c`bQuestions about the multiple village system`b`c`n");
	output("`^1. Why, oh why did you activate such a (choose one [wondrous, horrible]) feature?`n");
	output("`@For kicks, of course. We like to mess with your head.`n");
	output("But seriously, have you looked at the user list?  On lotgd.net, we've got over 6,000 people cramming themselves into the Village Square and trying to get their voices heard! Too much! Too much!`n");
	output("In the interests of sanity, we've made more chat boards. And in the interests of game continuity, we've put them into separate villages with many cool new features.`n`n");
	output("If you are a smaller server, this might not be right for you, but we think it works okay there too.`n`n");
	output("`^2. How do I go to other villages?`n");
	output("`@Walk, skate, take the bus...`n");
	output("Or press the Travel link (in the City Gates or Village Gates category) in the navigation bar.`n`n");
	output("`^3. How does travelling work?`n");
	output("`@Pretty well, actually. Thanks for asking.`n");
	output("You get some number of  free travels per day (%s on this server) in which you can travel to any other village you want.", get_module_setting("allowance"));
	output("Also, it is possible for the admin to give additional free travels with some mounts.");
	output("After that, you use up one forest fight per travel.");
	output("After that...well, we hope you like where you end up.");
	output("Since all major economic transactions come through %s (the capital of the region), the roads to and from there have been fortified to protect against monsters from wandering onto them.", getsetting("villagename", LOCATION_FIELDS));
	output("That was a while back though, and the precautions are no longer perfect.`n");
	output("Travel between the other villages have no such precautions.`n");
	output("In either case, you might want to heal yourself before travelling.");
	output("You have been warned.`n`n");
	output("`^4. Where's (the Inn, the forest, my training master, etc.)?`n");
	output("`@Look around. Do you see it? No? Then it's not here.`n");
	output("The problem's usually:`n");
	output("a) It's actually there, you just missed it the first time around.`n");
	output("b) It's in another village, try travelling.`n");
	output("c) It's not on this server, check out the LoGD Net link on the login page.`n");
	output("d) Are you sure you didn't just see that feature in a dream?`n`n");
	output("`^5. I've used up my free travels and forest fights. How do I travel now?`n");
	output("`@We hope you like where you've ended up, because you're stuck there until the next new day.`n`n");
	output("`^6. Can I pay for more travels?`n");
	output("`@No, but you can just plain pay us.");
	if (file_exists("lodge.php")) {
		output("Check out the Hunter's Lodge.");
	} else {
		output("Speak to an admin about donating money.");
	}
	output("Actually, we are considering it.`n");
	if (is_module_active("newbieisland")) {
		$newbieisland=get_module_setting("villagename", "newbieisland");
		if($session['user']['location'] == $newbieisland){
			$newbieisland = translate_inline($newbieisland);
			output("`^7. I'm on %s.", $newbieisland);
			output("Why can't I see the Travel link or any of the other stuff this section talks about?`n");
			output("`@You need at least 5 levels in search, or a Ring of Finding +2 to see any of this stuff.`n");
			output("If you haven't figured it out by now, this second answer is always the real one.");
			output("You'll only be able to see Travel once you leave %s.", $newbieisland);
			output("Feel free to skip the rest of this section and come back to it later.`n`n");
		}
	}
	rawoutput("<hr><a href='petition.php?op=faq'>$c</a>");
	popup_footer();

?>