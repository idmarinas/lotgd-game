<?
	$dwid = httpget("dwid");
	page_header("Customizing your ale");
	
	$sql = "SELECT alerounds, aleattack, aledefense FROM " . db_prefix("dwinns") . " WHERE dwid='$dwid'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	
	$alerounds = $row['alerounds'];
	$aleattack = $row['aleattack'];
	$aledefense = $row['aledefense'];
	if($alerounds=="")
		$alerounds=0;
	if($aleattack=="")
		$aleattack=0;
	if($aledefense=="")
		$aledefense=0;
	
	addnav(array("Back to the %s",get_module_setting("dwname","dwinns")),"runmodule.php?module=dwellings&op=enter&dwid=$dwid");
	addnav("","runmodule.php?module=dwinns&op=confirm-alechange&dwid=$dwid");
	
	output("`2A little more hops, a different brand of yeast and of course the kind of stuff you always add to the ale but never tell about change not only flavor, but also the properties of the stuff you try to sell to unsuspecting customers. ");
	output("`2Here you can tweak your ale to make it special and stand out from all the others. Hopefully no one will be able to taste the acid ... oops, almost gave a trade secret away.`n`n");
	output("`2Each attribute of your ale can have from 0 to %s points.`n",get_module_setting("maxalepointseach"));
	output("`2You should have at least one point in each attribute. Setting 0 rounds will create a buff with only 1 round duration, while 0 on defense or attack will have a negative effect on the player.`n");
	output("`2You can distribute %s points in total.`n`n",get_module_setting("maxalepoints"));
	
	$irounds = translate_inline("Rounds:");
	$iattack = translate_inline("Attack:");
	$idefense = translate_inline("Defense:");
	$isubmit = translate_inline("Submit");
	
	rawoutput("<form action='runmodule.php?module=dwinns&op=confirm-alechange&dwid=$dwid' method='POST'>");
	rawoutput("<br>$irounds<br>");
	rawoutput("<input id='input' name='rounds' width='2' maxlength='2' value='$alerounds'>");
	rawoutput("<br>$iattack<br>");
	rawoutput("<input id='input' name='attack' width='2' maxlength='2' value='$aleattack'>");
	rawoutput("<br>$idefense<br>");
	rawoutput("<input id='input' name='defense' width='2' maxlength='2' value='$aledefense'>");
	rawoutput("<input type='submit' class='button' value='$isubmit'>");
	rawoutput("</form>");
?>