<?php
function dragoneggs_animal25(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	if ($op2==500){
		if (getsetting("installer_version","-1")=="1.1.1 Dragonprime Edition"){
			$innname=getsetting("innname", LOCATION_INN);
		}else{
			$innname=translate_inline("The Boar's Head Inn");
		}
		page_header("%s",$innname);
		rawoutput("<span style='color: #9900FF'>");
		output("`c`b%s`b`c`0",$innname);
		output("You put down `^500 gold`0 to buy a round for everyone and they all raise their glasses to toast you.");
		output("`n`nOne particularily large gentleman comes over and taps you on the shoulder.`n`n");
		$color="`0";
		addnav(array("Return to %s",$innname),"inn.php");
	}else{
		page_header("Merick's Stables");
		output("`c`b`^Merick's Stables`b`c`7`n");
		addnav("Return to Merick's Stables","stables.php");
		$color="`7";
	}
	villagenav();
	output("`3'My name is Boulder Billings and I'd like to help you,'%s explains the large man.",$color);
	if (isset($session['bufflist']['ally'])) {
		if ($session['bufflist']['ally']['type']=="boulder"){
			$ally=1;
		}else{
			output("`n`nRealizing that you've found help from someone new, %s%s decides to leave.",$session['bufflist']['ally']['name'],$color);
			$ally=0;
		}
	}else $ally=0;
	if ($ally==0){
		apply_buff('ally',array(
			"name"=>translate_inline("`3Boulder Billings"),
			"rounds"=>50,
			"wearoff"=>translate_inline("`3Boulder Billings leaves in search of greater adventures."),
			"atkmod"=>1.1,
			"survivenewday"=>1,
			"type"=>"boulder",
		));
		debuglog("gained the help of ally Boulder Billings by researching at Merick's Stables.");
		output("`n`nYou gladly accept the help of your new ally, `3Boulder Billings%s.",$color);
	}else{
		$session['bufflist']['ally']['rounds'] += 16;
		debuglog("gained the help of ally Boulder Billings for another 16 rounds by researching at Merick's Stables.");
		output("`n`n`3Boulder Billings%s decides to stay with you another `^16 rounds%s.",$color,$color);
	}
	if (is_module_active("dlibrary")){
		if (get_module_setting("ally9","dlibrary")==0){
			set_module_setting("ally9",1,"dlibrary");
			addnews("%s`^ was the first person to meet `3Boulder Billings`^ at Merick's Stables.",$session['user']['name']);
		}
	}
}
?>