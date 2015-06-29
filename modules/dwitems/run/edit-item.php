<?php
$id = httpget("id");
$save = httpget("save");
if(httpget("save") == "1"){
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
		$sql = "UPDATE " .db_prefix("dwitems") . " SET itemid='$id', name='$name', type='$type', amount='$amount', chance='$chance', goldcost='$goldcost', gemcost='$gemcost', mindk='$mindk' , newdaytext='$newdaytext', dwellingtext='$dwellingtext', dwellingtextplural='$dwellingtextplural' WHERE itemid=$id";
		db_query($sql);
		output("Your item was succesfully saved to the database.`n`n`n");
	}else
		output("Your item could not be saved, one of your inputs was flawed. Please try again.`n`n`n");
}else{
	$sql = "SELECT * FROM " . db_prefix("dwitems") . " WHERE itemid=$id";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$name = $row['name'];
	$type = $row['type'];
	$amount = $row['amount'];
	$chance = $row['chance'];
	$mindk = $row['mindk'];
	$goldcost = $row['goldcost'];
	$gemcost = $row['gemcost'];
	$newdaytext = $row['newdaytext'];
	$dwellingtext = $row['dwellingtext'];
	$dwellingtextplural = $row['dwellingtextplural'];


	$tname = translate_inline("Name (50 characters max)");
	$ttype = translate_inline("Type");
	$tchance = translate_inline("Reciprocal of the possibility that one item has an effect on newday (1 in x chance).");
	$tamount = translate_inline("Amount of X added at newday (Can be negative. Leave empty for script items)");
	$tnewdaytext = translate_inline("Text shown on newday if item has an effect (200 characters max) (For script items: Leave empty if a script shall be executed at newday)");
	$tdwellingtext = translate_inline("Text shown inside dwelling (200 characters max) (For script items: Leave empty if a script shall be executed on entrance)");
	$tgoldcost = translate_inline("Gold to pay for this item");
	$tgemcost = translate_inline("Gems to pay for this item");
	$tmindk = translate_inline("Minimum dragonkills required for purchasing this item");
	$tdwellingtextplural = translate_inline("Text shown inside dwelling if more than one item of this type is there. Use \%s for the number of items. (200 characters max)");
	$tsave = translate_inline("Save");

	rawoutput("<form action='runmodule.php?module=dwitems&op=edit-item&id=$id&save=1' method='POST'>");
	rawoutput("<br>$tname<br>");
	rawoutput("<input id='input' name='name' width='2' maxlength='50' value='$name'>");
	rawoutput("<br>$ttype<br>");
	rawoutput("<select name='type' class='input'>");
	if($type=="0") rawoutput("<option value='0' selected>Charm</option>");
	else rawoutput("<option value='0'>Charm</option>");
	if($type=="1") rawoutput("<option value='1' selected>Gold</option>");
	else rawoutput("<option value='1'>Gold</option>");
	if($type=="2") rawoutput("<option value='2' selected>Gem</option>");
	else rawoutput("<option value='2'>Gem</option>");
	if($type=="3") rawoutput("<option value='3' selected>HP</option>");
	else rawoutput("<option value='3'>HP</option>");
	if($type=="4") rawoutput("<option value='4' selected>MaxHP</option>");
	else rawoutput("<option value='4'>MaxHP</option>");
	if($type=="5") rawoutput("<option value='5' selected>Favor</option>");
	else rawoutput("<option value='5'>Favor</option>");
	if($type=="6") rawoutput("<option value='6' selected>Turn</option>");
	else rawoutput("<option value='6'>Turn</option>");
	if($type=="7") rawoutput("<option value='7' selected>Script</option>");
	else rawoutput("<option value='7'>Script</option>");
	rawoutput("</select>");
	rawoutput("<br>$tamount<br>");
	rawoutput("<input id='input' name='amount' width='2' maxlength='5' value='$amount'>");
	rawoutput("<br>$tchance<br>");
	rawoutput("<input id='input' name='chance' width='2' maxlength='5' value='$chance'>");
	rawoutput("<br>$tgoldcost<br>");
	rawoutput("<input id='input' name='goldcost' width='2' maxlength='5' value='$goldcost'>");
	rawoutput("<br>$tgemcost<br>");
	rawoutput("<input id='input' name='gemcost' width='2' maxlength='5' value='$gemcost'>");
	rawoutput("<br>$tmindk<br>");
	rawoutput("<input id='input' name='mindk' width='2' maxlength='5'> value='$mindk'>");
	rawoutput("<br>$tnewdaytext<br>");
	rawoutput("<textarea name='newdaytext' class='input' cols='50' rows='4'>$newdaytext</textarea>");
	rawoutput("<br>$tdwellingtext<br>");
	rawoutput("<textarea name='dwellingtext' class='input' cols='50' rows='4'>$dwellingtext</textarea>");
	rawoutput("<br>$tdwellingtextplural<br>");
	rawoutput("<textarea name='dwellingtextplural' class='input' cols='50' rows='4'>$dwellingtextplural</textarea>");
	rawoutput("<br><input type='submit' class='button' value='$tsave'>");
	rawoutput("</form>");
	
	addnav("","runmodule.php?module=dwitems&op=edit-item&id=$id&save=1");
}
addnav("Back to the Editor", "runmodule.php?module=dwitems&op=editor");
?>