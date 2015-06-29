<?php
$id = httpget("id");
if($id!=""){
	if($id=="all"){
		$sql = "DELETE FROM " . db_prefix("dwitems");
	}else{
		$sql = "DELETE FROM " . db_prefix("dwitems") . " WHERE itemid=$id";
	}
	db_query($sql);
}

$sql = "SELECT itemid, name, type FROM " . db_prefix("dwitems");
$result = db_query($sql);
if(db_num_rows($result) > 0){
	$titemid = translate_inline("itemID");
	$tname = translate_inline("Name");
	$ttype = translate_inline("Type");
	$tedit = translate_inline("Edit");
	$tdelete = translate_inline("Delete");
	$tdeleteall = translate_inline("Delete all items");
	
	rawoutput("<a href='runmodule.php?module=dwitems&op=delete-items&id=all'>$tdeleteall</a><br><br>");	
	rawoutput("<table border='0' cellpadding='3' cellspacing='0' align='center'>");
	rawoutput("<tr class='trhead'><td style='width:40px' align=center><b>$titemid</b></td><td align=center><b>$tname</b></td><td align=center><b>$ttype</b></td><td style='width:75px' align=center><b>&nbsp;</b></td><td style='width:75px' align=center><b>&nbsp;</b></td></tr>");
	for($i = 0; $i < db_num_rows($result); $i++){
		$row = db_fetch_assoc($result);
		$itemid = $row['itemid'];
		$name = $row['name'];
		$type = $row['type'];
		switch($type){
			case 0:
				$type="`5Charm";
				break;
			case 1:
				$type="`^Gold";
				break;
			case 2:
				$type="`%Gem";
				break;
			case 3:
				$type="`4HP";
				break;
			case 4:
				$type="`\$maxHP";
				break;
			case 5:
				$type="`qFavor";
				break;
			case 6:
				$type="`@Turns";
				break;
			case 7:
				$type="`TScript";
			}
		$type = appoencode(translate_inline($type));
		$edit = "<a href='runmodule.php?module=dwitems&op=edit-item&id=$itemid'>$tedit</a>";
		$delete = "<a href='runmodule.php?module=dwitems&op=delete-items&id=$itemid'>$tdelete</a>";
		rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td align=center>$itemid</td><td>$name</td><td>$type</td><td align=center>[ $edit ]</td><td align=center>[ $delete ]</td></tr>");
		addnav("","runmodule.php?module=dwitems&op=delete-items&id=$itemid");
		addnav("","runmodule.php?module=dwitems&op=edit-item&id=$itemid");
	}
	rawoutput("</table>");
}else{
	output("No Items were found in your database.");
}
addnav("Return to the Grotto", "superuser.php");
addnav("Back to the Editor", "runmodule.php?module=dwitems&op=editor");
?>