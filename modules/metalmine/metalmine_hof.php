<?php
function metalmine_hof(){
	global $session;
	$op = httpget('op');
	page_header("Hall of Fame");
	$perpage = get_module_setting("perpage");
	if ($perpage==0) $perpage=40;
	$subop = httpget('subop');
	if ($subop=="") $subop=1;
	$min = (($subop-1)*$perpage);
	$max = $perpage*$subop;
	//This unserializes the pref to count the number of players with blocks so we can set up pages, thanks to Danbi for helping with it
	if (get_module_setting("nosuper")==1){
		//Thanks to Laroux for this		
		$superusermask = SU_HIDE_FROM_LEADERBOARD;
		$standardwhere = "(locked=0 AND (superuser & $superusermask) = 0)";
		$sql = "SELECT acctid,name FROM ".db_prefix("accounts")." WHERE $standardwhere";
	}else $sql = "SELECT acctid,name FROM ".db_prefix("accounts")."";
	$res = db_query($sql);
	$number=0;
	$new_array = array();
	for ($i=0;$i<db_num_rows($res);$i++){
		$row = db_fetch_assoc($res);
		$array=unserialize(get_module_pref('allprefs','metalmine',$row['acctid']));
		if ($array[$op]>0){
			$number=$number+1;
			$new_array[$row['acctid']] = $array[$op];
		}
	}
	$totalpages=ceil($number/$perpage);
	addnav("Pages");
	if ($totalpages>1){
		for($i = 0; $i < $totalpages; $i++) {
			$j=$i+1;
			$minpage = (($j-1)*$perpage)+1;
			$maxpage = $perpage*$j;
			if ($maxpage>$number) $maxpage=$number;
			if ($maxpage==$minpage) addnav(array("Page %s (%s)", $j, $minpage), "runmodule.php?module=metalmine&op=$op&subop=$j");
			else addnav(array("Page %s (%s-%s)", $j, $minpage, $maxpage), "runmodule.php?module=metalmine&op=$op&subop=$j");
		}
	}
	$rank = translate_inline("Rank");
	$name = translate_inline("Name");
	if ($op=="metalhof"){
		$birdname = translate_inline("Canary Name");
		$metalhof = translate_inline("Total Grams Mined");
		$none = translate_inline("Nothing Mined");
		output("`b`c`@Greatest`$ Miners `@in the Land`c`b`n");
	}else{
		$metalhof = translate_inline("Rescue Attempts");
		$none = translate_inline("No Rescue Attempts");
		output("`b`c`@Greatest`$ Miner Rescuers `@in the Land`c`b`n");
	}
	rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
	if ($op=="metalhof") rawoutput("<tr class='trhead'><td>$rank</td><td>$name</td><td>$metalhof</td><td>$birdname</td></tr>");
	else rawoutput("<tr class='trhead'><td>$rank</td><td>$name</td><td>$metalhof</td></tr>");
	$n=0;
	if ($number==0) output_notl("<tr class='trlight'><td colspan='4' align='center'>`&$none`0</td></tr>",true);
	else{
		//Thanks to Sichae for the next lines
		arsort($new_array);
		foreach($new_array AS $acctid => $value){
			$n=$n+1;
			if ($n>$min && $n<=$max){
				if ($acctid==$session['user']['acctid']) rawoutput("<tr class='trhilight'><td>");
				else rawoutput("<tr class='".($n%2?"trdark":"trlight")."'><td>");
				$sql = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid=".$acctid;
				$result = db_query($sql);
				$row = db_fetch_assoc($result);
				$name=$row['name'];
				$allprefshof=unserialize(get_module_pref('allprefs','metalmine',$acctid));
				$bird=$allprefshof['canary'];
				if ($bird=="") $bird=translate_inline("None");
				output_notl("`&%s",$n);
				rawoutput("</td><td>");
				output_notl("`&%s",$name);
				rawoutput("</td><td><center>");
				output_notl("`@%s",$value);
				if ($op=="metalhof"){
					rawoutput("</center></td><td><center>");
					output_notl("`^%s",$bird);
				}
				rawoutput("</center></td></tr>");
			}
		}
	}
	rawoutput("</table>");
	addnav("Other");
	addnav("Back to HoF","hof.php");
	villagenav();
}
?>