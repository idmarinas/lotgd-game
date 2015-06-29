<?php
$itemid = httpget("id");
$sql = "SELECT name FROM " . db_prefix("dwitems") . " WHERE itemid='$itemid'";
$result = db_query($sql);
$row = db_fetch_assoc($result);
output("`@Maeher`Q nodds and asks `#\"`3To which of your dwellings do you want your new %s to be delivered?`#\"`n`n", $row['name']);
$sql = "SELECT name, dwid FROM " . db_prefix("dwellings") . " WHERE ownerid=" . $session['user']['acctid']. " AND type!='dwinns'";
$result = db_query($sql);
if(db_num_rows($result)<=0){
	output("`QWaiting for an answer that doesn't come `@Maeher`Q begins to chuckle.`n");
	output("`#\"`3So... you don't have a dwelling?`#\"`Q he grins. `#\"`I would suggest you should come back when you've got one. I'll be more than happy to take your money then.`#\"");
}elseif(db_num_rows($result)==1){
	$row = db_fetch_assoc($result);
	$dwid = $row['dwid'];
	$name = $row['name'];
	if($name=="")
		$name = translate_inline("Unnamed");
	$tconfirm = translate_inline("Confirm");
	output("`QBefore your answer passes through your lips he continues `#\"`3Oh, I see you only got one.`#\"`n`n");
	output("`#\"`3So, we will deliver it to your dwelling `5%s`3, right?`#\"",$name);
	rawoutput("<form action='runmodule.php?module=dwitems&op=buy2' method='POST'>");
	rawoutput("<input type='hidden' name='id' value='$itemid'>");
	rawoutput("<input type='hidden' name='dwid' value='$dwid'>");
	rawoutput("<input type='submit' class='button' value='$tconfirm'></form>");
	addnav("","runmodule.php?module=dwitems&op=buy2");
}else{
	rawoutput("<form action='runmodule.php?module=dwitems&op=buy2' method='POST'>");
	output("`QSend it to ");
	rawoutput("<select name='dwid' class='input'>");
	$number=db_num_rows($result);
	for ($i=0;$i<$number;$i++){
		$row = db_fetch_assoc($result);
		if($row['name']=="")
			$name = translate_inline("Unnamed");
		else{
			require_once("lib/sanitize.php");
			$name = full_sanitize($row['name']);
		}
		rawoutput("<option value='".$row['dwid']."'>".$name."</option>");
	}
	$tsubmit = translate_inline("Submit");
	rawoutput("</select>");
	rawoutput("<input type='hidden' name='id' value='$itemid'>");
	rawoutput("<input type='submit' class='button' value='$tsubmit'></form>",true);
	addnav("","runmodule.php?module=dwitems&op=buy2");	
}
addnav("Back to the store","runmodule.php?module=dwitems&op=shop");
addnav("Return to the city","village.php");
?>