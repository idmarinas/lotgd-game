<?php
function quarry_completeblock(){
	$allprefs=unserialize(get_module_pref('allprefs'));
	$allprefs['blocks']=$allprefs['blocks']+1;
	$allprefs['blockshof']=$allprefs['blockshof']+1;
	set_module_pref('allprefs',serialize($allprefs));
	if (is_module_active('lostruins') && get_module_setting("usequarry")==0){
		increment_module_setting("blocksleft",-1);
		if (get_module_setting("blocksleft")<10) output("`@T`3he `@Q`3uarry`% is looking low on stone and may have to be shut down soon.`n`n");
	}
}
function quarry_quarrynavs(){
	addnav("V?(V) Return to Village","village.php");
	$allprefs=unserialize(get_module_pref('allprefs'));
	if ($allprefs['usedqts']<get_module_setting("quarryturns")) addnav("Work the Quarry","runmodule.php?module=quarry&op=work");
	addnav("Office","runmodule.php?module=quarry&op=office");
}
function quarry_blocknavs(){
	blocknav("village.php");
	blocknav("runmodule.php?module=quarry&op=office");
	blocknav("runmodule.php?module=quarry&op=work");
} 
function quarry_giantkill(){
	global $session;
	increment_module_setting("giantleft",-1);
	if (get_module_setting("giantleft")<=0) {
		set_module_setting("underatk",0);
		debuglog("killed the last giant in the quarry.");
		addnews("%s killed the last giant in the quarry! It's safe to return to work there.",$session['user']['name']);
	}else{
		debuglog("killed a giant in the quarry.");
	}	
	$allprefs=unserialize(get_module_pref('allprefs'));
	$allprefs['gianthof']++;
	set_module_pref('allprefs',serialize($allprefs));
	addnav("V?(V) Return to Village","village.php");
}
function quarry_dead(){
	global $session;
	output("`b`%You may try again tomorrow.`b`n`n");
	addnav("Daily news","news.php");
	$session['user']['experience']-=$exploss;
	$session['user']['alive'] = false;
	$session['user']['hitpoints'] = 0;
	$session['user']['gold']=0;
	$allprefs=unserialize(get_module_pref('allprefs'));
	if ($allprefs['insured']==1){
		$inspaygold=get_module_setting("inspaygold");
		$inspaygems=get_module_setting("inspaygems");
		output("It's times like these that you're happy that you bought your `)D`\$eath `)I`\$nsurance`%.`n`n You will have`^ %s gold`% and`b an additional %s gems `bon your next day.",$inspaygold,$inspaygems);
		$session['user']['gold']=$inspaygold;
		$session['user']['gems']+=$inspaygems;
		$allprefs['insured']=0;
		set_module_pref('allprefs',serialize($allprefs));
	}
}
function quarry_giantdead(){
	global $session;
	output("`b`\$You may try again tomorrow.`b`n`n");
	addnav("Daily news","news.php");
	$session['user']['alive'] = false;
	$session['user']['hitpoints'] = 0;
	$session['user']['gold']=0;
}
?>