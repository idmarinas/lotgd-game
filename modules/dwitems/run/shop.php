<?php
output("`@Maeher`Q sits behind the enormous counter of his store. He looks up and smiles shortly upon your entry.`n");
output("`QYou look around and see an amazing smorgasbord of strange items of which's purpose you do not have the slightest idea.`n");
output("`QA strange looking `ithing`i attracts your attention. But as you touch it, it falls off it's shelf.`n`n");
output("`QBefore it reaches the floor `@Maeher`Q jumps and saves the item worth `%20.000 gems`Q from it's certain destruction.`n");
output("`QHe gives you a lenient smile. `#\"`3So, what can I do for you?`#\"`0`n`n`n");
$sql = "SELECT itemid, name, goldcost, gemcost FROM " . db_prefix("dwitems") . " WHERE mindk <= " . $session['user']['dragonkills'];
$result = db_query($sql);
$tname=translate_inline("`bName`b");
$tcost=translate_inline("`bCost`b");
rawoutput("<div align='center'><table border='0' cellpadding='0' width='400'>");
rawoutput("<tr class='trhead'><td align='center'>");
output_notl($tname);
rawoutput("</td><td align='center'>");
output_notl($tcost);
rawoutput("</td></tr>");
$number=db_num_rows($result);
for ($i=0;$i<$number;$i++){
	$row = db_fetch_assoc($result);
	rawoutput("<tr class='".($i%2==1?"trlight":"trdark")."'><td align='center'>");
	$color = "`)";
	if ($session['user']['gold']>=$row['goldcost'] && $session['user']['gems']>=$row['gemcost']){
		rawoutput("<a href='runmodule.php?module=dwitems&op=buy&id={$row['itemid']}'>");
		output_notl("`&{$row['name']}`0");
		rawoutput("</a>");
		addnav("","runmodule.php?module=dwitems&op=buy&id={$row['itemid']}");
	}else
		output_notl("`){$row['name']}`0");
	rawoutput("</td><td align='center'>");
	output("`^%s Gold `%%s Gems`0",$row['goldcost'],$row['gemcost']);
	rawoutput("</td></tr>");
}
rawoutput("</table></div>");
addnav("Return to the city","village.php");
?>