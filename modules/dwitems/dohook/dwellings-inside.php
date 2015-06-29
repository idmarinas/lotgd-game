<?php
$dwid = httpget("dwid");
$dwitems = db_prefix("dwitems");
$dwellingsitems = db_prefix("dwellingsitems");
$sql = "SELECT $dwitems.name AS name, $dwitems.type AS type, $dwellingsitems.quantity AS quantity, $dwitems.dwellingtext AS dwellingtext, $dwitems.dwellingtextplural AS dwellingtextplural FROM $dwitems INNER JOIN $dwellingsitems ON $dwitems.itemID = $dwellingsitems.itemID WHERE $dwellingsitems.dwid=$dwid";
$result = db_query($sql);
while($row = db_fetch_assoc($result)){
	if($row['type']==7 && $row['dwellingtext']==""){
		require_once("modules/dwitems/items/" . preg_replace("/\s/", "_", $row['name']) . "_dwelling.php");
	}
	elseif($row['quantity']>1)
		output($row['dwellingtextplural'],$row['quantity']);
	else
		output($row['dwellingtext']);
}
if(db_num_rows($result)>=1) output("`n`n");
?>