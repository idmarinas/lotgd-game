<?php
// addnews ready
// translator ready
// mail ready

function motd_admin($id, $poll = false)
{
	global $session;

	$item = '';
	if ($session['user']['superuser'] & SU_POST_MOTD)
	{
		$ed = translate_inline("Edit");
		$del = translate_inline("Del");
		$confirm = translate_inline("Are you sure you want to delete this item?");

		if (!$poll)
		{
			$item .= "<a href='motd.php?op=add".($poll?"poll":"")."&id=$id'>$ed</a> | ";
		}
		$item .= "<a href='motd.php?op=del&id=$id' onClick=\"return confirm('$confirm');\">$del</a>";

		$item = sprintf('[%s]', $item);
	}

	return $item;
}

function motditem($subject, $body, $author, $date, $id)
{
	$showpolltext = ($id > '' ? motd_admin($id) : '');

	$item = '<div class="item"><div class="content">';
	$item .= sprintf('<div class="header">%s</div> %s', $subject, $showpolltext);
	$item .= '<div class="meta"><span class="author">'.$author.'</span> <small class="date">`#'.$date.'`0</small></div>';
	$item .= '';
	$item .= '<div class="description"><p>`2'.nltoappon($body).'`0</p></div>';
	$item .= '</div></div>';

	output_notl($item, true);
}

function pollitem($id, $subject, $body, $author, $date, $showpoll = true)
{
	global $session;
	$sql = "SELECT count(resultid) AS c, MAX(choice) AS choice FROM " . DB::prefix("pollresults") . " WHERE motditem='$id' AND account='{$session['user']['acctid']}'";
	$result = DB::query($sql);
	$row = DB::fetch_assoc($result);
	$choice = $row['choice'];
	$body = unserialize($body);
	$poll = translate_inline("Poll:");
	$showpolltext = ($showpoll ? motd_admin($id, true) : '');

	$item = '<div class="item"><div class="content">';
	$item .= sprintf('<div class="header">%s %s</div> %s',$poll, $subject, $showpolltext);
	$item .= '<div class="meta"><span class="author">'.$author.'</span> <small class="date">`#'.$date.'`0</small></div>';
	$item .= '<div class="description"><p>`2'.stripslashes($body['body']).'`0</p></div>';

	if ($session['user']['loggedin'] && $showpoll)
	{
		$item .= "<form action='motd.php?op=vote' method='POST'>";
		$item .= "<input type='hidden' name='motditem' value='$id'>";
	}

	$sql = "SELECT count(resultid) AS c, choice FROM " . DB::prefix("pollresults") . " WHERE motditem='$id' GROUP BY choice ORDER BY choice";
	$result = DB::query_cached($sql,"poll-$id");
	$choices = [];
	$totalanswers = 0;
	$maxitem = 0;
	while ($row = DB::fetch_assoc($result))
	{
		$choices[$row['choice']]=$row['c'];
		$totalanswers+=$row['c'];
		if ($row['c']>$maxitem) $maxitem = $row['c'];
	}

	$item .= '<div class="ui list">';
	foreach ($body['opt'] as $key=>$val)
	{
		if (trim($val)!="")
		{
			$item .= '<div class="item">';
			if ($totalanswers<=0) $totalanswers = 1;
			$percent = 0;
			if(isset($choices[$key]))
			{
				$percent = round($choices[$key] / $totalanswers * 100, 1);
			}

			if ($session['user']['loggedin'] && $showpoll)
			{
				$item .= '<div class="ui slider checkbox">';
				$item .= "<input type='radio' name='choice' value='$key'".($choice==$key?" checked":"").">";
				$item .= sprintf('<label>%s</label>', stripslashes($val));
				$item .= '</div>';
			}
			else
			{
				$item .= stripslashes($val);
			}

			$item .= sprintf(" (%s - %s%%)`n", (isset($choices[$key])?(int)$choices[$key]:0), $percent);
			$item .= '<div class="ui tiny progress" data-percent="'.$percent.'"><div class="bar"></div></div>';
			$item .= "</div>";
		}
	}
	$item .= '</div>';
	if ($session['user']['loggedin'] && $showpoll)
	{
		$vote = translate_inline("Vote");
		$item .= "<input type='submit' class='ui button' value='$vote'></form>";
	}
	$item .= '</div></div>';

	output_notl($item, true);
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

		rawoutput("<form action='motd.php?op=add&id={$row['motditem']}' method='POST' class='ui form'>");
		addnav("","motd.php?op=add&id={$row['motditem']}");
		if ($row['motdauthorname']>"")
			output("Originally by `@%s`0 on %s`n", $row['motdauthorname'],
					$row['motddate']);
		if ($subject>"") $row['motdtitle'] = stripslashes($subject);
		if ($body>"") $row['motdbody'] = stripslashes($body);
		if ($preview>""){
			if (httppost('changeauthor') || $row['motdauthorname']=="")
				$row['motdauthorname']=$session['user']['name'];
			if (httppost('changedate') || !isset($row['motddate']) || $row['motddate']=='')
				$row['motddate'] = date("Y-m-d H:i:s");
			motditem($row['motdtitle'], $row['motdbody'], $row['motdauthorname'],$row['motddate'], "");
		}
		rawoutput('<div class="field"><label>');
		output("Subject: ");
		rawoutput("</label><input type='text' size='50' name='subject' value=\"".HTMLEntities(stripslashes($row['motdtitle']), ENT_COMPAT, getsetting("charset", "UTF-8"))."\"><br/>");
		rawoutput('</div><div class="field"><label>');
		output("Body:`n");
		rawoutput("</label><textarea align='right' class='input' name='body' cols='37' rows='5'>".HTMLEntities(stripslashes($row['motdbody']), ENT_COMPAT, getsetting("charset", "UTF-8"))."</textarea></div><br/>");
		if ($row['motditem']>0){
			rawoutput('<div class="field">');
			output("Options:`n");
			rawoutput('</div><div class="field"><div class="ui toggle checkbox"><label>');
			output("Change Author`n");
			rawoutput("</label><input type='checkbox' value='1' name='changeauthor'".(httppost('changeauthor')?" checked":"").">");
			rawoutput('</div></div><div class="field"><div class="ui toggle checkbox"><label>');
			output("Change Date (force popup again)`n");
			rawoutput("</label><input type='checkbox' value='1' name='changedate'".(httppost('changedate')?" checked":"").">");
			rawoutput('</div></div>');
		}
		$prev = translate_inline("Preview");
		$sub = translate_inline("Submit");
		rawoutput("<input type='submit' class='ui button' name='preview' value='$prev'> <input type='submit' class='ui button' value='$sub'></form>");
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
		rawoutput("<form action='motd.php?op=addpoll' method='POST' class='ui form'>");
		addnav("","motd.php?op=add");
		rawoutput('<div class="field"><label>');
		output("Subject: ");
		rawoutput("</label><input type='text' name='subject' value=\"".HTMLEntities(stripslashes($subject), ENT_COMPAT, getsetting("charset", "UTF-8"))."\">");
		rawoutput('</div><div class="field"><label>');
		output("Body:`n");
		rawoutput("</label><textarea name='body' cols='37' rows='5'>".HTMLEntities(stripslashes($body), ENT_COMPAT, getsetting("charset", "UTF-8"))."</textarea>");
		$option = translate_inline("Option");
		rawoutput('</div><div class="field">');
		output("Choices:`n");
		rawoutput('</div>');
		$pollitem = "<div class='inline field'><label>$option</label> <input name='opt[]'></div>";
		rawoutput($pollitem);
		rawoutput($pollitem);
		rawoutput($pollitem);
		rawoutput("<div id='hidepolls'>");
		rawoutput("</div>");
		rawoutput("<script language='JavaScript'>document.getElementById('hidepolls').innerHTML = '';</script>",true);
		$addi = translate_inline("Add Poll Item");
		$add = translate_inline("Add");
		rawoutput("<a href=\"#\" onClick=\"javascript:document.getElementById('hidepolls').innerHTML += '".addslashes($pollitem)."'; return false;\">$addi</a><br><br>");
		rawoutput("<input type='submit' class='ui button' value='$add'></form>");
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
