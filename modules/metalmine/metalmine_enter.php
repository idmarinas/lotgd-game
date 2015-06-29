<?php
function metalmine_enter(){
	global $session;
	$op2 = httpget('op2');
	$op3 = httpget('op3');
	$allprefs=unserialize(get_module_pref('allprefs'));
	addnav("Metal Mine");
	output("`n`c`b`&Metal `)Mine`0`c`b`n");
	$turns=get_module_setting("ffs");
	$allprefs['inmine']=1;
	if ($op3>"") $allprefs['metal']=$op3;
	if ($op2=="turn"){
		output("You spend `@%s %s`0 traveling.`n`n",$turns,translate_inline($turns>1?"turns":"turn"));
		$session['user']['turns']-=$turns;
	}
	output("You are at the mine and look around.");
	if ($allprefs['firstm']==0){
		output("`n`nA young lady comes up and greets you.");
		output("`&'Hello.  My name is Lily.  I am the owner of the `&Metal `)Mine`&. I'd like to welcome you.'");
		output("`0You shake her hand and reflect that she seems much more powerful than you would expect from someone so young.");
		if ($turns>0) output("`n`n`&'You've journeyed far to get here, so I want to make sure all your questions are answered.  Please");
		else output("`n`n`&'Please");
		output("come to my office.'");
		output("`n`n`0You follow her down a short path to her office.");
		$allprefs['firstm']=1;
		addnav("To the Office","runmodule.php?module=metalmine&op=firstoffice");
	}else{
		addnav("Locations");
		addnav("General Store","runmodule.php?module=metalmine&op=store");
		addnav("Rules","runmodule.php?module=metalmine&op=rules");
		addnav("Lily's Office","runmodule.php?module=metalmine&op=office");
		output("`n`nThere's a small building next to the mine with the label 'General Store'.  There's a board next  to the mine entrance that says 'Rules'.");
		output("`&Lily's`0 office is down a short path, and of course, there's the entrance to the mine itself.");
		if (get_module_setting("down")==1){
			output("`n`nYou see a large crowd of miners gathered around the mine entrance.  There's been an accident and miners are trapped.");
			output("Your help is needed to rescue them.");
			addnav("Rescue","runmodule.php?module=metalmine&op=rescue");
		}else addnav("The Mine","runmodule.php?module=metalmine&op=mine");
		addnav("Return to the Forest","runmodule.php?module=metalmine&op=leave");
	}
	set_module_pref('allprefs',serialize($allprefs));
}
?>