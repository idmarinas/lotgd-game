<?php
function orchard_treelist(){
	global $session;
	
	$names=translate_inline(array("","`\$Apple","`QOrange","`6Pear","`QApricot","`^Banana","`QPeach","`5Plum","`qFig","`^Mango","`\$Cherry","`QTangerine","`^Grapefruit","`^Lemon","`@Avocado","`2Lime","`\$Pomegranate","`qKiwi","`4Cranberry","`^Star Fruit","`@Dragon`\$fruit"));
	$perpage = 15;
	$subop = httpget('subop');
	if ($subop=="") $subop=1;
	$first = ($subop-1)*$perpage;
	//This unserializes the pref to count the number of players with trees so we can set up pages, thanks to Danbi for helping with it
	$sql = "SELECT acctid,name FROM ".db_prefix("accounts")."";
	$res = db_query($sql);
	$total=0;
	$new_array = array();
	for ($i=0;$i<db_num_rows($res);$i++){
		$row = db_fetch_assoc($res);
		$allprefs=unserialize(get_module_pref('allprefs','orchard',$row['acctid']));
		if ($allprefs['tree']>0) {
			$total=$total+1;
			$new_array[$row['name']] = $allprefs['tree'];
		}
	}
	addnav("Pages");
	$totalpages=ceil($total/$perpage);
	if ($totalpages>1){
		for($i = 0; $i < $totalpages; $i++) {
			$j=$i+1;
			$minpage = (($j-1)*$perpage)+1;
			$maxpage = $perpage*$j;
			if ($maxpage>$total) $maxpage=$total;
			if ($maxpage==$minpage) addnav(array("Page %s (%s)", $j, $minpage), "runmodule.php?module=orchard&op=explore&subop=$j");
			else addnav(array("Page %s (%s-%s)", $j, $minpage, $maxpage), "runmodule.php?module=orchard&op=explore&subop=$j");
		}
	}
	$name = translate_inline("Name");
	$tree = translate_inline("Best Tree");
	$none = translate_inline("No Trees Found");
	
	rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
	rawoutput("<tr class='trhead'><td>$name</td><td>$tree</td></tr>");
	//This gathers the names from the unserialized list
	$n=0;
	if ($total==0) output_notl("<tr class='trlight'><td colspan='2' align='center'>`&$none`0</td></tr>",true);
	else{
		//Thanks to Sichae for the next code
		arsort($new_array);
		foreach($new_array AS $name => $value){
			$n=$n+1;
			if ($n>$first && $n<=($subop*$perpage)){
				if ($name==$session['user']['name']) rawoutput("<tr class='trhilight'><td>");
				else rawoutput("<tr class='".($n%2?"trdark":"trlight")."'><td>");
				output_notl("`&%s`0",$name);
				rawoutput("</td><td><center>");
				output_notl("`@%s`0",$names[$value]);
				rawoutput("</center></td></tr>");
			}
		}
	}
	rawoutput("</table>");
}
?>
