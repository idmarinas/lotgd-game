<?php
	$edit = translate_inline("Edit");
	$del = translate_inline("Delete");
	$delconfirm = translate_inline("Are you sure you wish to delete this item?");
	$sql = "SELECT * FROM " . db_prefix("skills") . " WHERE id>=0 ORDER BY type,levelreq,manacost";
	$result = db_query($sql);
	$count = db_num_rows($result);
	if ($count == 0){
		output("`6No Skills in database yet.");
	}else{
		$ops = translate_inline("Ops");
		$skillid = translate_inline("Skill ID");
		$name = translate_inline("Name");
		$lvlreq = translate_inline("Level Requirement");
		$mana = translate_inline("Mana Cost");
		$cate = translate_inline("Specialty Type");
		rawoutput("<table cellspacing=0 cellpadding=2 width='100%' align='center'>");
		rawoutput("<tr><td>$ops</td><td>$skillid</td><td>$name</td><td>$lvlreq</td><td>$mana</td><td>$cate</td></tr>");
		$i = 0;
		while($row = db_fetch_assoc($result)){
			rawoutput("<tr class='".($i%2?"trlight":"trdark")."'>"); 
			rawoutput("<td>[<a href='runmodule.php?module=skilleditor&op=editor&what=edit&id={$row['id']}'>$edit</a>|<a href='runmodule.php?module=skilleditor&op=editor&what=delete&id={$row['id']}' onClick='return confirm(\"$delconfirm\");'>$del</a>]</td>");   
			addnav("","runmodule.php?module=skilleditor&op=editor&what=edit&id={$row['id']}");
			addnav("","runmodule.php?module=skilleditor&op=editor&what=delete&id={$row['id']}");
			rawoutput("<td>");
			output_notl($row['id']);
			rawoutput("</td><td>");
			output_notl($row['name']);
			rawoutput("</td><td>");
			output_notl($row['levelreq']);
			rawoutput("</td><td>");
			output_notl($row['manacost']);
			rawoutput("</td><td>");
			output_notl($row['type']);
			rawoutput("</td></tr>");
			$i++;
		}
		rawoutput("</table>");
	}
?>
