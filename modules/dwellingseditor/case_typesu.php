<?php
	page_header("Dwelling Types");
    $id=translate_inline("ID");
    $name=translate_inline("Dwelling Type");
    $edit=translate_inline("Edit");
    $sql = "SELECT * FROM ".db_prefix("dwellingtypes");
    $result = db_query($sql);
    rawoutput("<table border='0' cellpadding='3' cellspacing='0' align='center'><tr class='trhead'><td style=\"width:50px\">$id</td><td style='width:150px' align=center>$name</td><td align=center>$edit</td></tr>"); 
	$i = 0;
    while($row = db_fetch_assoc($result)){
		$i++;
	    rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td align=center>".$row['typeid']."</td><td align=center>");
	    output_notl("%s",$row['module']);
	    rawoutput("</td><td align=center>");
	    rawoutput("<a href='runmodule.php?module=dwellingseditor&op=typeeditmodule&typeid=".$row['typeid']."'>$edit</a></td></tr>");
	    addnav("","runmodule.php?module=dwellingseditor&op=typeeditmodule&typeid=".$row['typeid']."");  
	}
    rawoutput("</table>");
?>