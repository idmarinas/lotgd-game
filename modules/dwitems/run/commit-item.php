<?php
$name = httppost("name");
$type = httppost("type");
$amount = httppost("amount");
$chance = httppost("chance");
$goldcost = httppost("goldcost");
$gemcost = httppost("gemcost");
$mindk = httppost("mindk");
$newdaytext = httppost("newdaytext");
$dwellingtext = httppost("dwellingtext");
$dwellingtextplural = httppost("dwellingtextplural");

if($chance > 0 && $type >=0 && $type <=7){
	if($goldcost == "")
		$goldcost = 0;
	if($gemcost == "")
		$gemcost = 0;
	if($amount == "")
		$amount == 1;
	if($mindk == "")
		$mindk = 0;
	$sql = "SELECT itemid FROM " . db_prefix("dwitems") . " ORDER BY itemid";
	$result = db_query($sql);
	$id=0;
	while($row = db_fetch_assoc($result)){
		if($row['itemid'] > $id)
			break;
		$id++;
	}
	$sql = "INSERT INTO " .db_prefix("dwitems") . " VALUES ('$id', '$name', '$type', '$amount', '$chance', '$goldcost', '$gemcost', '$mindk', '$newdaytext', '$dwellingtext', '$dwellingtextplural')";
	db_query($sql);
	output("Your item was succesfully saved to the database.`n`n`n");
}else{
	output("Your item could not be saved, one of your inputs was flawed. Please try again.`n`n`n");
}
require("modules/dwitems/run/editor.php");
?>