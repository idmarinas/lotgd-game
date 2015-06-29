<?php
		$sortby = httpget('sortby');
		$ref = httpget('ref');
        $page = httpget('page');
 		$order = httpget('order');
        $showonly = httpget('showonly');
        $ownerid = httpget('ownerid');
		if($op!="new")	addnav("Add a New Dwelling","runmodule.php?module=dwellingseditor&op=new");
        
        $dwellsperpage = get_module_setting("listnum","dwellings");
        
        $search = "";
        $limit = "";
        if($order == "") $order="desc";
        if($sortby == "") $sortby="dwid";
        
        $onlyshow = "";
        if($showonly!="") $onlyshow = " WHERE type='".$showonly."'";
        if($showonly == "location") $onlyshow=" WHERE location='".$session['user']['location']."'";
        if($ownerid!="" && $showonly!=""){
            $onlyshow = $onlyshow." and ownerid=".$ownerid."";
        }elseif($ownerid!=""){
            $onlyshow = $onlyshow." where ownerid=".$ownerid."";
        }
        $sql = "SELECT count(dwid) AS c FROM " . db_prefix("dwellings") . "$onlyshow";
        $result = db_query($sql);
        $row = db_fetch_assoc($result);
        $totaldwellings = $row['c'];
        $pageoffset = (int)$page;
        if ($pageoffset>0) $pageoffset--;
        $pageoffset*=$dwellsperpage;
        $from = $pageoffset+1;
        $to = min($pageoffset+$dwellsperpage,$totaldwellings);
        $limit=" LIMIT $pageoffset,$dwellsperpage ";
        addnav("Pages");
        for ($i = 0; $i < $totaldwellings; $i += $dwellsperpage){
            $pnum = $i/$dwellsperpage+1;
            if ($page == $pnum) {
                addnav(array(" ?`b`#Page %s`0 (%s-%s)`b", $pnum, $i+1, min($i+$dwellsperpage,$totaldwellings)), "runmodule.php?module=dwellingseditor&&ref=$ref&sortby=$sortby&showonly=$showonly&ownerid=$ownerid&order=$order&page=$pnum");
            } else {
                addnav(array(" ?Page %s (%s-%s)", $pnum, $i+1, min($i+$dwellsperpage,$totaldwellings)), "runmodule.php?module=dwellingseditor&&ref=$ref&sortby=$sortby&showonly=$showonly&ownerid=$ownerid&order=$order&page=$pnum");
            }
        }
		$sql = "SELECT * FROM ".db_prefix("dwellings")." $onlyshow ORDER BY $sortby $order $limit";
        $result = db_query($sql);
        $id = translate_inline("ID");
        $name = translate_inline("Name");
        $owner = translate_inline("Owner");
        $type = translate_inline("Type");        
        $edit = translate_inline("Edit");
        $status = translate_inline("Status");
        $loc = translate_inline("Location");
        $imgdesc="<img src=modules/dwellings/images/desc.gif>";
        $imgasc="<img src=modules/dwellings/images/asc.gif>";        
        rawoutput("<table style='width:100%;' border=0 cellpadding=1 cellspacing=1 bgcolor='#999999'>");
        rawoutput("<tr class='trhead'>");
        rawoutput("<td align=center cellpadding=0 nowrap><a href='runmodule.php?module=dwellingseditor&&ref=$ref&sortby=dwid&showonly=$showonly&ownerid=$ownerid&order=desc&page=$pnum'>$imgdesc</a>");
        rawoutput("<a href='runmodule.php?module=dwellingseditor&&ref=$ref&sortby=dwid&showonly=$showonly&ownerid=$ownerid&order=asc&page=$pnum'>$imgasc</a>");
        rawoutput("$id</td>");
        addnav("","runmodule.php?module=dwellingseditor&&ref=$ref&sortby=dwid&showonly=$showonly&ownerid=$ownerid&order=desc&page=$pnum");
        addnav("","runmodule.php?module=dwellingseditor&&ref=$ref&sortby=dwid&showonly=$showonly&ownerid=$ownerid&order=asc&page=$pnum");

        rawoutput("<td align=center style=\"width:150px\"><a href='runmodule.php?module=dwellingseditor&&ref=$ref&sortby=name&showonly=$showonly&ownerid=$ownerid&order=desc&page=$pnum'>$imgdesc</a>");
        rawoutput("<a href='runmodule.php?module=dwellingseditor&&ref=$ref&sortby=name&showonly=$showonly&ownerid=$ownerid&order=asc&page=$pnum'>$imgasc</a>");
        rawoutput("$name</td>");
        addnav("","runmodule.php?module=dwellingseditor&&ref=$ref&sortby=name&showonly=$showonly&ownerid=$ownerid&order=desc&page=$pnum");
        addnav("","runmodule.php?module=dwellingseditor&&ref=$ref&sortby=name&showonly=$showonly&ownerid=$ownerid&order=asc&page=$pnum");

        rawoutput("<td align=center style=\"width:75px\"><a href='runmodule.php?module=dwellingseditor&&ref=$ref&sortby=ownerid&showonly=$showonly&ownerid=$ownerid&order=desc&page=$pnum'>$imgdesc</a>");
        rawoutput("<a href='runmodule.php?module=dwellingseditor&&ref=$ref&sortby=ownerid&showonly=$showonly&ownerid=$ownerid&order=asc&page=$pnum'>$imgasc</a>");
        rawoutput("$owner</td>");
        addnav("","runmodule.php?module=dwellingseditor&&ref=$ref&sortby=ownerid&showonly=$showonly&ownerid=$ownerid&order=desc&page=$pnum");
        addnav("","runmodule.php?module=dwellingseditor&&ref=$ref&sortby=ownerid&showonly=$showonly&ownerid=$ownerid&order=asc&page=$pnum");

        rawoutput("<td align=center><a href='runmodule.php?module=dwellingseditor&&ref=$ref&sortby=type&showonly=$showonly&ownerid=$ownerid&order=desc&page=$pnum'>$imgdesc</a>");
        rawoutput("<a href='runmodule.php?module=dwellingseditor&&ref=$ref&sortby=type&showonly=$showonly&ownerid=$ownerid&order=asc&page=$pnum'>$imgasc</a>");
        rawoutput("$type</td>");
        addnav("","runmodule.php?module=dwellingseditor&&ref=$ref&sortby=type&showonly=$showonly&ownerid=$ownerid&order=desc&page=$pnum");
        addnav("","runmodule.php?module=dwellingseditor&&ref=$ref&sortby=type&showonly=$showonly&ownerid=$ownerid&order=asc&page=$pnum");
		if (is_module_active("cities")){
	        rawoutput("<td align=center style=\"width:75px\"><a href='runmodule.php?module=dwellingseditor&&ref=$ref&sortby=location&showonly=$showonly&ownerid=$ownerid&order=desc&page=$pnum'>$imgdesc</a>");
	        rawoutput("<a href='runmodule.php?module=dwellingseditor&&ref=$ref&sortby=location&showonly=$showonly&ownerid=$ownerid&order=asc&page=$pnum'>$imgasc</a>");
	        rawoutput("$loc</td>");
	        addnav("","runmodule.php?module=dwellingseditor&&ref=$ref&sortby=location&showonly=$showonly&ownerid=$ownerid&order=desc&page=$pnum");
	        addnav("","runmodule.php?module=dwellingseditor&&ref=$ref&sortby=location&showonly=$showonly&ownerid=$ownerid&order=asc&page=$pnum");
		}
        rawoutput("<td align=center style=\"width:50px\"><a href='runmodule.php?module=dwellingseditor&&ref=$ref&sortby=status&showonly=$showonly&ownerid=$ownerid&order=desc&page=$pnum'>$imgdesc</a>");
        rawoutput("<a href='runmodule.php?module=dwellingseditor&&ref=$ref&sortby=status&showonly=$showonly&ownerid=$ownerid&order=asc&page=$pnum'>$imgasc</a>");
        rawoutput("$status</td>");
        addnav("","runmodule.php?module=dwellingseditor&&ref=$ref&sortby=status&showonly=$showonly&ownerid=$ownerid&order=desc&page=$pnum");
        addnav("","runmodule.php?module=dwellingseditor&&ref=$ref&sortby=status&showonly=$showonly&ownerid=$ownerid&order=asc&page=$pnum");

		rawoutput("<td align=center style=\"width:50px\">$edit</td></tr>");
		$i = 0;
		while($row = db_fetch_assoc($result)){
			$i++;
			$sql2 = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid='{$row['ownerid']}'";
            $result2 = db_query($sql2);
            $row2 = db_fetch_assoc($result2);            
            rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td>".$row['dwid']."</td><td>");
            $ctype = translate_inline(get_module_setting("dwname",$row['type']));
			$status1 = translate_inline("`#Occupied");
			$status2 = translate_inline("`@Financing");
			$status3 = translate_inline("`QIn Construction");
			$status4 = translate_inline("`!Abandoned");
			$status5 = translate_inline("`%For Sale");
			$name = "status".$row['status'];
			$status = $$name;
			$stat = modulehook("dwellings-status",array("rowstatus"=>$row['status'],"dwid"=>$row['dwid'],"type"=>$row['type'],"status"=>$status));
			$status = $stat['status'];
			$name = $row['name'];
			if($name == ""){ 
				$name = translate_inline("Unnamed");
			}
			output_notl($name);
			rawoutput("</td><td>");
			output_notl($row2['name']);
			rawoutput("</td><td>");
			output_notl($ctype);
			rawoutput("</td>");
			if (is_module_active("cities")){
				rawoutput("<td>");
				output_notl($row['location']);
				rawoutput("</td>");
			}
			rawoutput("<td>");
			output_notl("%s",$status);
			rawoutput("</td><td style='text-align:center;'>");
			output_notl("<a href='runmodule.php?module=dwellingseditor&op=dwsu&dwid=".$row['dwid']."'>`![`@$edit`!]`0</a>",true);
			addnav("","runmodule.php?module=dwellingseditor&op=dwsu&dwid=".$row['dwid']."");
			rawoutput("</td></tr>");             
		}
        rawoutput("</table>");
?>