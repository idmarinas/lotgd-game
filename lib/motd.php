<?php
// addnews ready
// translator ready
// mail ready

function motd_admin($id, $poll=false) {
	global $session;
	if ($session['user']['superuser'] & SU_POST_MOTD) {
		$ed = translate_inline("Edit");
		$del = translate_inline("Del");
		$confirm = translate_inline("Are you sure you want to delete this item?");
		output_notl("[ ");
		if (!$poll) {
			rawoutput("<a href='motd.php?op=add".($poll?"poll":"")."&id=$id'>$ed</a> | ");
		}
		rawoutput("<a href='motd.php?op=del&id=$id' onClick=\"return confirm('$confirm');\">$del</a> ]");
	}
}

function motditem($subject,$body,$author,$date,$id){
	if ($date)
		rawoutput("<a name='motd".date("YmdHis",strtotime($date))."'>");
	output_notl("`b`^%s`0`b", $subject);
	if ($id > "") {
		motd_admin($id);
	}
	if ($date || $author) output_notl("`n");
	if ($author > "") {
		output_notl("`3%s`0", $author);
	}
	if ($date>"")
		output_notl("`0 &#150; `#%s`0", $date, true);
	if ($date || $author) output_notl("`n");

	output_notl("`2%s`0", nltoappon($body), true);
	if ($date) rawoutput("</a>");
	rawoutput("<hr>");
}

function pollitem($id,$subject,$body,$author,$date,$showpoll=true){
	global $session;
	$sql = "SELECT count(resultid) AS c, MAX(choice) AS choice FROM " . DB::prefix("pollresults") . " WHERE motditem='$id' AND account='{$session['user']['acctid']}'";
	$result = DB::query($sql);
	$row = DB::fetch_assoc($result);
	$choice = $row['choice'];
	$body = unserialize($body);

	$poll = translate_inline("Poll:");
	if ($session['user']['loggedin'] && $showpoll) {
		rawoutput("<form action='motd.php?op=vote' method='POST'>");
		rawoutput("<input type='hidden' name='motditem' value='$id'>",true);
	}
	output_notl("`b`&%s `^%s`0`b", $poll, $subject);
	if ($showpoll) motd_admin($id, true);
	output_notl("`n`3%s`0 &#150; `#%s`0`n", $author, $date, true);
	output_notl("`2%s`0`n", stripslashes($body['body']),true); // allow HTML
	$sql = "SELECT count(resultid) AS c, choice FROM " . DB::prefix("pollresults") . " WHERE motditem='$id' GROUP BY choice ORDER BY choice";
	$result = DB::query_cached($sql,"poll-$id");
	$choices=array();
	$totalanswers=0;
	$maxitem = 0;
	while ($row = DB::fetch_assoc($result)) {
		$choices[$row['choice']]=$row['c'];
		$totalanswers+=$row['c'];
		if ($row['c']>$maxitem) $maxitem = $row['c'];
	}
	foreach ($body['opt'] as $key=>$val) {
		if (trim($val)!=""){
			if ($totalanswers<=0) $totalanswers=1;
			$percent = 0;
			if(isset($choices[$key])) {
				$percent = round($choices[$key] / $totalanswers * 100,1);
			}
			if ($session['user']['loggedin'] && $showpoll) {
				rawoutput("<input type='radio' name='choice' value='$key'".($choice==$key?" checked":"").">");
			}
			output_notl("%s (%s - %s%%)`n", stripslashes($val),
					(isset($choices[$key])?(int)$choices[$key]:0), $percent);
			if ($maxitem==0 || !isset($choices[$key])){
				$width=1;
			} else {
				$width = round(($choices[$key]/$maxitem) * 400,0);
			}
			$width = max($width,1);
			rawoutput("<img src='images/rule.gif' width='$width' height='2' alt='$percent'><br>");
		}
	}
	if ($session['user']['loggedin'] && $showpoll) {
		$vote = translate_inline("Vote");
		rawoutput("<input type='submit' class='button' value='$vote'></form>");
	}
	rawoutput("<hr>",true);
}

function motd_form($id) {
	global $session;
	$subject = httppost('subject');
	$body = httppost('body');
	$preview = httppost('preview');
	if ($subject=="" || $body=="" || $preview>""){
		$edit = translate_inline("Edit a MoTD");
		$add = translate_inline("Add a MoTD");
		$ret = translate_inline("Return");

		$row = array(
			"motditem"=>0,
			"motdauthorname"=>"",
			"motdtitle"=>"",
			"motdbody"=>"",
		);
		if ($id>""){
			$sql = "SELECT " . DB::prefix("motd") . ".*,name AS motdauthorname FROM " . DB::prefix("motd") . " LEFT JOIN " . DB::prefix("accounts") . " ON " . DB::prefix("accounts") . ".acctid = " . DB::prefix("motd") . ".motdauthor WHERE motditem='$id'";
			$result = DB::query($sql);
			if (DB::num_rows($result)>0){
				$row = DB::fetch_assoc($result);
				$msg = $edit;
			}else{
				$msg = $add;
			}
		}else{
			$msg = $add;
		}
		output_notl("`b%s`b", $msg);
		rawoutput("[ <a href='motd.php'>$ret</a> ]<br>");

		rawoutput("<form action='motd.php?op=add&id={$row['motditem']}' method='POST'>");
		addnav("","motd.php?op=add&id={$row['motditem']}");
		if ($row['motdauthorname']>"")
			output("Originally by `@%s`0 on %s`n", $row['motdauthorname'],
					$row['motddate']);
		if ($subject>"") $row['motdtitle'] = stripslashes($subject);
		if ($body>"") $row['motdbody'] = stripslashes($body);
		if ($preview>""){
			if (httppost('changeauthor') || $row['motdauthorname']=="")
				$row['motdauthorname']=$session['user']['name'];
			if (httppost('changedate') || $row['motddate']=="")
				$row['motddate']=date("Y-m-d H:i:s");
			motditem($row['motdtitle'], $row['motdbody'],
					$row['motdauthorname'],$row['motddate'], "");
		}
		output("Subject: ");
		rawoutput("<input type='text' size='50' name='subject' value=\"".HTMLEntities(stripslashes($row['motdtitle']), ENT_COMPAT, getsetting("charset", "UTF-8"))."\"><br/>");
		output("Body:`n");
		rawoutput("<textarea align='right' class='input' name='body' cols='37' rows='5'>".HTMLEntities(stripslashes($row['motdbody']), ENT_COMPAT, getsetting("charset", "UTF-8"))."</textarea><br/>");
		if ($row['motditem']>0){
			output("Options:`n");
			rawoutput("<input type='checkbox' value='1' name='changeauthor'".(httppost('changeauthor')?" checked":"").">");
			output("Change Author`n");
			rawoutput("<input type='checkbox' value='1' name='changedate'".(httppost('changedate')?" checked":"").">");
			output("Change Date (force popup again)`n");
		}
		$prev = translate_inline("Preview");
		$sub = translate_inline("Submit");
		rawoutput("<input type='submit' class='button' name='preview' value='$prev'> <input type='submit' class='button' value='$sub'></form>");
	}else{
		if ($id>""){
			$sql = " SET motdtitle='$subject', motdbody='$body'";
			if (httppost('changeauthor'))
				$sql.=", motdauthor={$session['user']['acctid']}";
			if (httppost('changedate'))
				$sql.=", motddate='".date("Y-m-d H:i:s")."'";
			$sql = "UPDATE " . DB::prefix("motd") . $sql . " WHERE motditem='$id'";
			DB::query($sql);
			invalidatedatacache("motd");
			invalidatedatacache("lastmotd");
			invalidatedatacache("motddate");
		}
		if ($id=="" || DB::affected_rows()==0){
			if ($id>""){
				$sql = "SELECT * FROM " . DB::prefix("motd") . " WHERE motditem='$id'";
				$result = DB::query($sql);
				if (DB::num_rows($result)>0) $doinsert = false;
				else $doinsert=true;
			}else{
				$doinsert=true;
			}
			if ($doinsert){
				$sql = "INSERT INTO " . DB::prefix("motd") . " (motdtitle,motdbody,motddate,motdauthor) VALUES (\"$subject\",\"$body\",'".date("Y-m-d H:i:s")."','{$session['user']['acctid']}')";
				DB::query($sql);
				invalidatedatacache("motd");
				invalidatedatacache("lastmotd");
				invalidatedatacache("motddate");
			}
		}
		header("Location: motd.php");
		exit();
	}
}

function motd_poll_form() {
	global $session;
	$subject = httppost('subject');
	$body = httppost('body');
	if ($subject=="" || $body==""){
		output("`\$NOTE:`^ Polls cannot be edited after they are begun in order to ensure fairness and accuracy of results.`0`n`n");
		rawoutput("<form action='motd.php?op=addpoll' method='POST'>");
		addnav("","motd.php?op=add");
		output("Subject: ");
		rawoutput("<input type='text' size='50' name='subject' value=\"".HTMLEntities(stripslashes($subject), ENT_COMPAT, getsetting("charset", "UTF-8"))."\"><br/>");
		output("Body:`n");
		rawoutput("<textarea class='input' name='body' cols='37' rows='5'>".HTMLEntities(stripslashes($body), ENT_COMPAT, getsetting("charset", "UTF-8"))."</textarea><br/>");
		$option = translate_inline("Option");
		output("Choices:`n");
		$pollitem = "$option <input name='opt[]'><br/>";
		rawoutput($pollitem);
		rawoutput($pollitem);
		rawoutput($pollitem);
		rawoutput($pollitem);
		rawoutput($pollitem);
		rawoutput("<div id='hidepolls'>");
		rawoutput("</div>");
		rawoutput("<script language='JavaScript'>document.getElementById('hidepolls').innerHTML = '';</script>",true);
		$addi = translate_inline("Add Poll Item");
		$add = translate_inline("Add");
		rawoutput("<a href=\"#\" onClick=\"javascript:document.getElementById('hidepolls').innerHTML += '".addslashes($pollitem)."'; return false;\">$addi</a><br>");
		rawoutput("<input type='submit' class='button' value='$add'></form>");
	}else{
		$opt = httppost("opt");
		$body = array("body"=>$body,"opt"=>$opt);
		$sql = "INSERT INTO " . DB::prefix("motd") . " (motdtitle,motdbody,motddate,motdtype,motdauthor) VALUES (\"$subject\",\"".addslashes(serialize($body))."\",'".date("Y-m-d H:i:s")."',1,'{$session['user']['acctid']}')";
		DB::query($sql);
		invalidatedatacache("motd");
		invalidatedatacache("lastmotd");
		invalidatedatacache("motddate");
		header("Location: motd.php");
		exit();
	}
}

function motd_del($id) {
	$sql = "DELETE FROM " . DB::prefix("motd") . " WHERE motditem=\"$id\"";
	DB::query($sql);
	invalidatedatacache("motd");
	invalidatedatacache("lastmotd");
	invalidatedatacache("motddate");
	header("Location: motd.php");
	exit();
}

?>
