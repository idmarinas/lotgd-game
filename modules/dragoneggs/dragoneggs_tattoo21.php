<?php
function dragoneggs_tattoo21(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Tattoo Parlor");
	output("`c`b`&Petra, the Ink Artist`b`c`7");
	$session['user']['gems']-=3;
	output("You spend more time talking to `QRyan Dean`7 and give `%3 gems`7 to him. He agrees to fight by your side.");
	if (isset($session['bufflist']['ally'])) {
		if ($session['bufflist']['ally']['type']=="ryandean"){
			$ally=1;
		}else{
			output("`n`nRealizing that you've found help from someone new, %s`7 decides to leave.",$session['bufflist']['ally']['name']);
			$ally=0;
		}
	}else $ally=0;
	if ($ally==0){
		apply_buff('ally',array(
			"name"=>translate_inline("`QRyan Dean"),
			"rounds"=>30,
			"wearoff"=>translate_inline("`QRyan decides he's going to get another tattoo and leaves."),
			"atkmod"=>1.3,
			"survivenewday"=>1,
			"type"=>"ryandean",
		));
		output("`n`nYou gain the help of `QRyan Dean`7!");
		debuglog("gained the help of Ryan Dean while researching dragon eggs at the Tattoo Parlor.");
	}else{
		$session['bufflist']['ally']['rounds'] += 10;
		output("`n`n`QRyan Dean`7 decides to help you out for another `^10 rounds`7!");
		debuglog("gained the help of Ryan Dean for an additional 10 rounds while researching dragon eggs at the Tattoo Parlor.");
	}
	if (is_module_active("dlibrary")){
		if (get_module_setting("ally7","dlibrary")==0){
			set_module_setting("ally7",1,"dlibrary");
			addnews("%s`^ was the first person to meet `QRyan Dean`^ at the Tattoo Parlor.",$session['user']['name']);
		}
	}
	addnav("Return to the Tattoo Parlor","runmodule.php?module=petra");
	villagenav();
}
?>