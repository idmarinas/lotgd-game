<?php
	page_header("Dwellings Registry");
	$sortby = httpget('sortby');
	$ref = httpget('ref');
	$page = httpget('page');
 	$order = httpget('order');
	$showonly = httpget('showonly');
	$dw=db_prefix('dwellings');
	$ac=db_prefix('accounts');
	$pnum=0;
	$limit = "";
	if($order=="") $order="desc";
	if($sortby=="") $sortby="dwid";
	
	if($ref == "hof"){
		page_header("Dwellings Registry");
	    addnav("Navigation");
	    addnav("Return to HoF","hof.php");
	}else{
	    addnav("Navigation");
	    addnav("Back to the Hamlet","runmodule.php?module=dwellings");
	}
	$dwellsperpage = get_module_setting("listnum");
	$onlyshow = "";
	if($showonly!="") $onlyshow = " WHERE type='".$showonly."'";
	if($ref == "hamlet"){
		if($showonly!=""){
			$onlyshow=$onlyshow." and $dw.location='".$session['user']['location']."'";
		}else{	
			$onlyshow=" WHERE $dw.location='".$session['user']['location']."'";
		}
	}
	$sql = "SELECT count(dwid) AS c FROM " . db_prefix("dwellings") . "$onlyshow";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$totaldwellings = $row['c'];
	$pageoffset = (int)$page;
	if ($pageoffset>0) $pageoffset--;
	$pageoffset*=$dwellsperpage;
	$limit=" LIMIT $pageoffset,$dwellsperpage ";
	//}
	addnav("Pages");
	for ($i = 0; $i < $totaldwellings; $i += $dwellsperpage){
		$pnum = $i/$dwellsperpage+1;
		if ($page == $pnum) {
			addnav(array(" ?`b`#Page %s`0 (%s-%s)`b", $pnum, $i+1, min($i+$dwellsperpage,$totaldwellings)), "runmodule.php?module=dwellings&op=list&ref=$ref&sortby=$sortby&showonly=$showonly&order=$order&page=$pnum");
		} else {
			addnav(array(" ?Page %s (%s-%s)", $pnum, $i+1, min($i+$dwellsperpage,$totaldwellings)), "runmodule.php?module=dwellings&op=list&ref=$ref&sortby=$sortby&showonly=$showonly&order=$order&page=$pnum");
		}
	}
	modulehook("dwellings-list-type",array("ref"=>$ref,"order"=>$order,"showonly"=>$showonly,"sortby"=>$sortby));
	$sql = "SELECT $dw.*,$ac.name AS ownername FROM $dw LEFT JOIN $ac ON $dw.ownerid=$ac.acctid $onlyshow ORDER BY $sortby $order $limit";
	$result = db_query($sql);
	$name = translate_inline("Name");
	$owner = translate_inline("Owner");
	$type = translate_inline("Type");		
	$desc = translate_inline("Description");
	$status = translate_inline("Status");
	$loc = translate_inline("Location");
	$interact = translate_inline("Interact");
	$imgdesc="<img src=modules/dwellings/images/desc.gif>";
	$imgasc="<img src=modules/dwellings/images/asc.gif>";		
	rawoutput("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>");
	rawoutput("<tr class='trhead'>");
	rawoutput("<td><a href='runmodule.php?module=dwellings&op=list&ref=$ref&sortby=name&showonly=$showonly&order=desc&page=$pnum'>$imgdesc</a>");
	rawoutput("<a href='runmodule.php?module=dwellings&op=list&ref=$ref&sortby=name&showonly=$showonly&order=asc&page=$pnum'>$imgasc</a>");
	rawoutput("$name</td>");
	addnav("","runmodule.php?module=dwellings&op=list&ref=$ref&sortby=name&showonly=$showonly&order=desc&page=$pnum");
	addnav("","runmodule.php?module=dwellings&op=list&ref=$ref&sortby=name&showonly=$showonly&order=asc&page=$pnum");

	rawoutput("<td><a href='runmodule.php?module=dwellings&op=list&ref=$ref&sortby=ownerid&showonly=$showonly&order=desc&page=$pnum'>$imgdesc</a>");
	rawoutput("<a href='runmodule.php?module=dwellings&op=list&ref=$ref&sortby=ownerid&showonly=$showonly&order=asc&page=$pnum'>$imgasc</a>");
	rawoutput("$owner</td>");
	addnav("","runmodule.php?module=dwellings&op=list&ref=$ref&sortby=ownerid&showonly=$showonly&order=desc&page=$pnum");
	addnav("","runmodule.php?module=dwellings&op=list&ref=$ref&sortby=ownerid&showonly=$showonly&order=asc&page=$pnum");

	rawoutput("<td>$desc</td>");
 	if($showonly == ""){	   
		rawoutput("<td><a href='runmodule.php?module=dwellings&op=list&ref=$ref&sortby=type&showonly=$showonly&order=desc&page=$pnum'>$imgdesc</a>");
		rawoutput("<a href='runmodule.php?module=dwellings&op=list&ref=$ref&sortby=type&showonly=$showonly&order=asc&page=$pnum'>$imgasc</a>");
		rawoutput("$type</td>");
		addnav("","runmodule.php?module=dwellings&op=list&ref=$ref&sortby=type&showonly=$showonly&order=desc&page=$pnum");
		addnav("","runmodule.php?module=dwellings&op=list&ref=$ref&sortby=type&showonly=$showonly&order=asc&page=$pnum");
	}else{
		addnav("Show Only Types");
		addnav("Show All","runmodule.php?module=dwellings&op=list&ref=$ref&sortby=status&showonly=&order=asc&page=$pnum");
	}
	if($ref != "hamlet"){
		rawoutput("<td><a href='runmodule.php?module=dwellings&op=list&ref=$ref&sortby=location&showonly=$showonly&order=desc&page=$pnum'>$imgdesc</a>");
		rawoutput("<a href='runmodule.php?module=dwellings&op=list&ref=$ref&sortby=location&showonly=$showonly&order=asc&page=$pnum'>$imgasc</a>");
		rawoutput("$loc</td>");
		addnav("","runmodule.php?module=dwellings&op=list&ref=$ref&sortby=location&showonly=$showonly&order=desc&page=$pnum");
		addnav("","runmodule.php?module=dwellings&op=list&ref=$ref&sortby=location&showonly=$showonly&order=asc&page=$pnum");
	}
	rawoutput("<td><a href='runmodule.php?module=dwellings&op=list&ref=$ref&sortby=status&showonly=$showonly&order=desc&page=$pnum'>$imgdesc</a>");
	rawoutput("<a href='runmodule.php?module=dwellings&op=list&ref=$ref&sortby=status&showonly=$showonly&order=asc&page=$pnum'>$imgasc</a>");
	rawoutput("$status</td>");
	addnav("","runmodule.php?module=dwellings&op=list&ref=$ref&sortby=status&showonly=$showonly&order=desc&page=$pnum");
	addnav("","runmodule.php?module=dwellings&op=list&ref=$ref&sortby=status&showonly=$showonly&order=asc&page=$pnum");

	if($ref!="hof"){
		rawoutput("<td>$interact</td>");
	}
	rawoutput("</tr>");
//		for ($i = 0; $i < db_num_rows($result); $i++){ 
//			$row = db_fetch_assoc($result);
// Better to have it here, so we can translate everything at once.
	$status1 = translate_inline("`#Occupied");
	$status2 = translate_inline("`@Financing");
	$status3 = translate_inline("`QIn Construction");
	$status4 = translate_inline("`!Abandoned");
	$status5 = translate_inline("`%For Sale");
	while ($row = db_fetch_assoc($result)) {
		rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td>");
		$ctype = translate_inline(ucwords(get_module_setting("dwname",$row['type'])));
// That's much better :) (No, it isn't but it looks like you're a really cool coder...)
		$name = "status".$row['status'];
		$status = $$name;
		$stat=modulehook("dwellings-status",array("rowstatus"=>$row['status'],"dwid"=>$row['dwid'],"type"=>$row['type'],"status"=>$status));
		$status=$stat['status'];
		$name = $row['name'];
		if($name == ""){ 
			$name = translate_inline("Unnamed");
		}
		output_notl($name);
		rawoutput("</td><td>");
		output_notl($row['ownername']);
		rawoutput("</td><td>");
		$windowpeer = $row['windowpeer'];
		if($windowpeer == ""){
				$windowpeer = translate_inline("This dwelling has no public description yet.");
			if ($row['status']==2){
				$windowpeer = translate_inline("This dwelling is still being built.");
			}
		}
		output_notl("%s", $windowpeer);
		rawoutput("</td><td>");
		if($showonly == ""){
			output_notl($ctype);
			rawoutput("</td><td>");
		}
		if($ref != "hamlet"){
			output_notl($row['location']);
			rawoutput("</td><td>");
		}
		output_notl("%s",$status);
		if($ref!="hof"){
			rawoutput("</td><td>");
			modulehook("dwellings-list-interact",array("type"=>$row['type'],"dwid"=>$row['dwid'],"owner"=>$row['ownerid'],"status"=>$row['status'],"location"=>$row['location']));
		}
		rawoutput("</td></tr>");			 
	}
	rawoutput("</table>");
?>
