<?php
function metalmine_eexplore(){
	global $session;
	$allprefs=unserialize(get_module_pref('allprefs'));
	output("`n`c`b`&Metal `)Mine`c`b`n`0");
	$allprefs['eleave']=1;
	set_module_pref('allprefs',serialize($allprefs));
	switch(e_rand(1,4)){
		case 1:
			output("You turn on your helmet light and look around.  It's so odd... it looks like it was designed by aliens!");
			output("`n`nYou keep exploring until you discover a strange looking room.");
			output("You see a gnome standing in the corner. He points to the table and whispers in a creepy voice `#'Staaaarrrrttt the reaaactorrrrr.'`0");
			output("`n`nWhat do you do?");
			addnav("Start the Reactor","runmodule.php?module=metalmine&op=ereactor");
			addnav("Leave","runmodule.php?module=metalmine&op=contgd2");
		break;
		case 2:
			output("You find yourself in a Dwarf tomb.  A search reveals a book that reads the following:`n");
			output("`c`5The end comes... drums, drums in the deep... they are coming...`c`0`n");
			output("Suddenly, you hear a deep drumming sound.");
			output("You turn to escape but find yourself face to face with a `7Cave Troll`0!");
			addnav("Cave Troll Fight","runmodule.php?module=metalmine&op=cavetroll");
		break;
		case 3:
			output("You don't find anything of value except a huge dust bunny.  The bunny gives you a gold piece.");
			$session['user']['gold']++;
			addnav("Leave","runmodule.php?module=metalmine&op=contgd2");
		break;
		case 4:
			output("You find yourself standing in front of a huge lake. In the distance you see a figure on an island.");
			output("`n`nYou decide to leave and you notice something on the ground.  It looks like it's a ring.");
			output("`n`nThe creature in the background cries out `#'My preccioussssss!!!'`0");
			output("`n`nYou slip on the ring and disappear!");
			apply_buff('metalmine',array(
				"name"=>"`&Invisibility",
				"rounds"=>1,
				"defmod"=>1.5,
				"roundmsg"=>"Your ring of invisibility makes you a difficult target for your opponent.",
				"wearoff"=>"`&The ring decides you are not worthy and falls off your finger.",
			));
			addnav("Leave","runmodule.php?module=metalmine&op=contgd2");
		break;
	}
}
?>