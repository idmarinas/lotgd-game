<?php
$itemid = httppost("id");
$dwid = httppost("dwid");
$sql = "SELECT itemid FROM " . db_prefix('dwellingsitems') . " WHERE itemid='$itemid' AND dwid='$dwid'";
$result = db_query($sql);
if(db_num_rows($result) == 0)
	$sql = "INSERT INTO " . db_prefix('dwellingsitems') . " VALUES ($itemid, $dwid, 1)";
else
	$sql = "UPDATE " . db_prefix('dwellingsitems') . " SET quantity=quantity+1 WHERE itemid='$itemid' AND dwid='$dwid'";
db_query($sql);
$sql = "SELECT name, gemcost, goldcost FROM " . db_prefix("dwitems") . " WHERE itemid='$itemid'";
$result = db_query($sql);
$row = db_fetch_assoc($result);
$session['user']['gold']-=$row['goldcost'];
$session['user']['gems']-=$row['gemcost'];
output("`@Maeher`Q greedily takes your money and smiles. `#\"`3Your new %s will be promptly delivered to your dwelling. Enjoy it!`#\"", $row['name']);
addnav("Back to the store","runmodule.php?module=dwitems&op=shop");
addnav("Return to the city","village.php");
?>