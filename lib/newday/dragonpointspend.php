<?php
if ($dkills-$dp > 1) {
	page_header("Dragon Points");
	output("`@You earn one dragon point each time you slay the dragon.");
	output("Advancements made by spending dragon points are permanent!");
	output("`n`nYou have `^%s`@ unspent dragon points.", $dkills-$dp);
	output("How do you wish to spend them?`n`n");
	output("Be sure that your allocations add up to your total unspent dragon points.");
	$text = "<script type='text/javascript' language='Javascript'>
	<!--
	function pointsLeft() {
			var form = document.getElementById(\"dkForm\");
	";
	foreach($labels as $type=>$label) {
		$head=explode(",",$label);
		if (count($head)>1) continue; //got a headline here
		if (isset($canbuy[$type]) && $canbuy[$type]) {
			$text .= "var $type = parseInt(form.$type.value);
			";
		}
	}
	foreach($labels as $type=>$label) {
		$head=explode(",",$label);
		if (count($head)>1) continue; //got a headline here
		if (isset($canbuy[$type]) && $canbuy[$type]) {
			$text .= "if (isNaN($type)) $type = 0;
			";
		}
	}
	$text .= "var val = $dkills - $dp ";
	foreach($labels as $type=>$label) {
		$head=explode(",",$label);
		if (count($head)>1) continue; //got a headline here
		if (isset($canbuy[$type]) && $canbuy[$type]) {
			$text .= "- $type";
		}
	}
	$text .= ";
			var absval = Math.abs(val);
			var points = 'points';
			if (absval == 1) points = 'point';
				if (val >= 0)
				document.getElementById(\"amtLeft\").innerHTML = \"<span class='colLtWhite'>You have </span><span class='colLtYellow'>\"+absval+\"</span><span class='colLtWhite'> \"+points+\" left to spend.</span><br />\";
			else
				document.getElementById(\"amtLeft\").innerHTML = \"<span class='colLtWhite'>You have spent </span><span class='colLtRed'>\"+absval+\"</span><span class='colLtWhite'> \"+points+\" too many!</span><br />\";
		}
	// -->
	</script>\n";
	rawoutput($text);
	addnav("Reset", "newday.php?pdk=0$resline");
		$link = appendcount("newday.php?pdk=1$resline");
		rawoutput("<form id='dkForm' action='$link' method='POST'>");
	addnav("",$link);
	rawoutput("<br><table cellpadding='0' cellspacing='0' border='0' width='200'>");
	foreach($labels as $type=>$label) {
		$head=explode(",",$label);//debug($label);
		if (count($head)>1) {
			rawoutput("<tr><td colspan='2' nowrap>");
			output("`b`4%s`0`b`n",translate_inline($head[0])); //got a headline here
			rawoutput("</td></tr>");
			continue;
		}
		if (isset($canbuy[$type]) && $canbuy[$type]) {
			rawoutput("<tr><td nowrap>");
			output($label);
			output_notl(":");
			rawoutput("</td><td>");
			rawoutput("<input id='$type' name='$type' size='4' maxlength='4' value='{$pdks[$type]}' onKeyUp='pointsLeft();' onBlur='pointsLeft();' onFocus='pointsLeft();'>");
			rawoutput("</td></tr>");
		}
	}
	rawoutput("<tr><td colspan='2'>&nbsp;");
	rawoutput("</td></tr><tr><td colspan='2' align='center'>");
	$click = translate_inline("Spend");
	rawoutput("<input id='dksub' type='submit' class='button' value='$click'>");
	rawoutput("</td></tr><tr><td colspan='2'>&nbsp;");
	rawoutput("</td></tr><tr><td colspan='2' align='center'>");
	rawoutput("<div id='amtLeft'></div>");
	rawoutput("</td></tr>");
	rawoutput("</table>");
	rawoutput("</form>");
	$count = 0;
	foreach($labels as $type=>$label) {
		$head=explode(",",$label);
		if (count($head)>1) continue; //got a headline here
		if ($count > 0) break;
		if (isset($canbuy[$type]) && $canbuy[$type]) {
			rawoutput("<script language='JavaScript'>document.getElementById('$type').focus();</script>");
			$count++;
		}
	}
}else{
	page_header("Dragon Points");
	$dist = array();
	foreach ($labels as $type=>$label) {
		$head=explode(",",$label);
		if (count($head)>1) {
			addnav($head[0]); //got a headline here
			continue;
		}
		$dist[$type] = 0;  // Initialize the distribution
		if (isset($canbuy[$type]) && $canbuy[$type]) {
			addnav($label, "newday.php?dk=$type$resline");
		}
	}
		output("`@You have `&1`@ unspent dragon point.");
	output("How do you wish to spend it?`n`n");
	output("You earn one dragon point each time you slay the dragon.");
	output("Advancements made by spending dragon points are permanent!");
	$player_dkpoints=count($session['user']['dragonpoints']);
	for ($i=0; $i<$player_dkpoints; $i++) {
		if (isset($dist[$session['user']['dragonpoints'][$i]])) {
			$dist[$session['user']['dragonpoints'][$i]]++;
		} else {
			$dist['unknown']++;
		}
	}
		output("`n`nCurrently, the dragon points you have already spent are distributed in the following manner.");
	rawoutput("<blockquote>");
	rawoutput("<table>");

	foreach ($labels as $type=>$label) {
		$head=explode(",",$label);
		if ($dist[$type]==0) continue;
		if (count($head)>1) {
			rawoutput("<tr><td colspan='2' nowrap>");
			output("`b`4%s`0`b`n",translate_inline($head[0])); //got a headline here
			rawoutput("</td></tr>");
			continue;
		}
		if ($type == 'unknown' && $dist[$type] == 0) continue;
		rawoutput("<tr><td nowrap>");
		output($label);
		output_notl(":");
		rawoutput("</td><td>&nbsp;&nbsp;</td><td>");
		output_notl("`@%s", $dist[$type]);
		rawoutput("</td></tr>");
	}
	rawoutput("</table>");
	rawoutput("</blockquote>");
}
?>
