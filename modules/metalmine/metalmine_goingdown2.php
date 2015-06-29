<?php
function metalmine_goingdown2(){
	global $session;
	$op2 = httpget('op2');
	$allprefs=unserialize(get_module_pref('allprefs'));
	$locale = httpget('loc');
	page_header("The Elevator");
	if ($session['user']['hitpoints'] <= 0) redirect("shades.php");
	$umaze = get_module_pref('maze');
	$umazeturn = $allprefs['mazeturn'];
	$upqtemp = get_module_pref('pqtemp');
	if ($op2 == "" && $locale == "") {
		$locale=20;
		$umazeturn = 0;
		$allprefs=unserialize(get_module_pref('allprefs'));
		$allprefs['mazeturn']=0;
		set_module_pref('allprefs',serialize($allprefs));
		$allprefs=unserialize(get_module_pref('allprefs'));
		if (!isset($maze)){
			$maze = array(16,37,16,16,37,16,16,37,16,16,37,16,16,37,16,16,37,16,16,37,16);
			$umaze = implode($maze,",");
			set_module_pref("maze", $umaze);
		}
	}
	if ($op2 <> ""){
		if ($op2 == "n") {
			$locale+=3;
			redirect("runmodule.php?module=metalmine&op=goingdown2&loc=$locale");
		}
		if ($op2 == "s"){
			$locale-=3;
			redirect("runmodule.php?module=metalmine&op=goingdown2&loc=$locale");
		}
	}else{
		if ($locale <> ""){
			$maze=explode(",", $umaze);
			if ($locale=="") $locale = $upqtemp;
			$upqtemp = $locale;
			set_module_pref("pqtemp", $upqtemp);
			for ($i=0;$i<$locale-1;$i++){
			}
			$navigate=ltrim($maze[$i]);
			if ($session['user']['hitpoints'] > 0){
				output("`c`b`^The Elevator`0`b`c");
				if ($locale=="20"){
					addnav("Exit the Elevator","runmodule.php?module=metalmine&op=leavegoingdown2");
					blocknav("runmodule.php?module=metalmine&op=goingdown2&op2=n&loc=$locale");
				}
				addnav("Up","runmodule.php?module=metalmine&op=goingdown2&op2=n&loc=$locale");
				addnav("Down","runmodule.php?module=metalmine&op=goingdown2&op2=s&loc=$locale");
				if ($locale=="2"){
					$allprefs=unserialize(get_module_pref('allprefs'));
					if ($allprefs['toothy']==5) addnav("Exit the Elevator","runmodule.php?module=metalmine&op=eltoothy");
					else addnav("Exit the Elevator","runmodule.php?module=metalmine&op=contgd2");
					blocknav("runmodule.php?module=metalmine&op=goingdown2&op2=s&loc=$locale");
				}
				$allprefs['mazeturn']=$allprefs['mazeturn']+1;
				set_module_pref('allprefs',serialize($allprefs));
				$allprefs=unserialize(get_module_pref('allprefs'));
			}else{
				addnav("Continue","shades.php");
			}
			$mazemap=$navigate;
			$mazemap.="maze.gif";
			output_notl("`n`c");
			rawoutput("<small>");
			$mapkey2="<table style=\"height: 230px; width: 210px; text-align: left;\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tbody><tr><td style=\"vertical-align: top;\">";
			$mapkey="";
			for ($i=0;$i<21;$i++){
				$keymap=ltrim($maze[$i]);
				$mazemap=$keymap;
				$mazemap.="maze.gif";
				if ($i==$locale-1){
					$mapkey.="<img src=\"./modules/metalmine/metalmineimg/mcyan2.gif\" title=\"\" alt=\"\" style=\"width: 50px; height: 50px;\">";
				}else{
					if (($i==19 && get_module_pref("pqtemp")<18)||(($i==19||$i==16) && get_module_pref("pqtemp")<15)||(($i==19||$i==16||$i==13) && get_module_pref("pqtemp")<12)||(($i==19||$i==16||$i==13||$i==10) && get_module_pref("pqtemp")<9)||(($i==19||$i==16||$i==13||$i==10||$i==7) && get_module_pref("pqtemp")<6)||(($i==19||$i==16||$i==13||$i==10||$i==7||$i==4) && get_module_pref("pqtemp")<3)){
						$mapkey.="<img src=\"./modules/metalmine/metalmineimg/38maze.gif\" title=\"\" alt=\"\" style=\"width: 50px; height: 50px;\">";					
					}else{
						$mapkey.="<img src=\"./modules/metalmine/metalmineimg/$mazemap\" title=\"\" alt=\"\" style=\"width: 50px; height: 50px;\">";
					}
				}
				if ($i==2 or $i==5 or $i==8 or $i==11 or $i==14 or $i==17 or $i==20 or $i==23){
					$mapkey="`n".$mapkey;
					$mapkey2=$mapkey.$mapkey2;
					$mapkey="";
				}
			}
			$mapkey2.="</td></tr></tbody></table>";
			output_notl($mapkey2,true);
			output_notl("`c");
		}
	}
}
?>