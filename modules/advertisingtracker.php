<?php
function advertisingtracker_getmoduleinfo(){
	$info = array(
		"name"=>"Advertising Tracker (inGame)",
		"version"=>"1.0",
		"author"=>"`2Oliver Brendel",
		"category"=>"Administrative",
		"download"=>"",
		"settings"=>array(
					"Ad Tracker - Preferences, title",
					"who"=>"Enter here seperated by comma the acctid of the admins who should be excluded (secure mail), text|1,7",
					),
	);
	return $info;
}

function advertisingtracker_install(){
	//table setup for 1.1.1 & 1.1.0, if you use a lower version, you will encounter errors -> remove some fields
	module_addhook("superuser");
	$abusemail=array(
		'messageid'=>array('name'=>'messageid','type'=>'int(11) unsigned','extra'=>'auto_increment'),
		'msgfrom'=>array('name'=>'msgfrom', 'type'=>'int(11) unsigned', 'default'=>'0'),
		'msgto'=>array('name'=>'msgto', 'type'=>'int(11) unsigned', 'default'=>'0'	),
		'subject'=>array('name'=>'subject', 'type'=>'varchar(255)'),
		'body'=>array('name'=>'body', 'type'=>'text'),
		'sent'=>array('name'=>'sent', 'type'=>'datetime', 'default'=>'0000-00-00 00:00:00'),
		'seen'=>array('name'=>'seen', 'type'=>'tinyint(1)', 'default'=>'0'),
		'key-PRIMARY'=>array('name'=>'PRIMARY','type'=>'primary key','unique'=>'1','columns'=>'messageid'),
		'key-msgto'=>array('name'=>'msgto', 'type'=>'key', 'columns'=>'msgto'),
		'key-seen'=>array('name'=>'seen', 'type'=>'key', 'columns'=>'seen')
		);
	$abuseuser=array(
		'acctid'=>array('name'=>'acctid', 'type'=>'int(11) unsigned', 'extra'=>'auto_increment'),
		'name'=>array('name'=>'name', 'type'=>'varchar(60)'),
		'sex'=>array('name'=>'sex', 'type'=>'tinyint(4) unsigned', 'default'=>'0'),
		'level'=>array('name'=>'level', 'type'=>'int(11) unsigned', 'default'=>'1'),
		'laston'=>array('name'=>'laston', 'type'=>'datetime', 'default'=>'0000-00-00 00:00:00'),
		'password'=>array('name'=>'password', 'type'=>'varchar(32)'),
		'superuser'=>array('name'=>'superuser', 'type'=>'int(11) unsigned', 'default'=>'1'),
		'login'=>array('name'=>'login', 'type'=>'varchar(50)'),
		'lasthit'=>array('name'=>'lasthit', 'type'=>'datetime', 'default'=>'0000-00-00 00:00:00'),
		'dragonkills'=>array('name'=>'dragonkills', 'type'=>'int(11) unsigned', 'default'=>'0'),
		'locked'=>array('name'=>'locked', 'type'=>'tinyint(4) unsigned', 'default'=>'0'),
				'lastip'=>array('name'=>'lastip', 'type'=>'varchar(40)'),
		'uniqueid'=>array('name'=>'uniqueid', 'type'=>'varchar(32)', 'null'=>'1'),
		'emailaddress'=>array('name'=>'emailaddress', 'type'=>'varchar(128)'),
		'emailvalidation'=>array('name'=>'emailvalidation', 'type'=>'varchar(32)'),
		'donation'=>array('name'=>'donation', 'type'=>'int(11) unsigned', 'default'=>'0'),
		'donationspent'=>array('name'=>'donationspent', 'type'=>'int(11) unsigned', 'default'=>'0'),
		'ctitle'=>array('name'=>'ctitle', 'type'=>'varchar(25)'),
		'regdate'=>array('name'=>'regdate','type'=>'datetime','default'=>'0000-00-00 00:00:00'),
		'clanjoindate'=>array('name'=>'clanjoindate','type'=>'datetime','default'=>'0000-00-00 00:00:00'),
		'key-PRIMARY'=>array('name'=>'PRIMARY','type'=>'primary key','unique'=>'1','columns'=>'acctid'),
		);
	require_once("lib/tabledescriptor.php");
	synctable(db_prefix("abusemail"), $abusemail, true);
	synctable(db_prefix("abuseuser"), $abuseuser, true);
	return true;
}

function advertisingtracker_uninstall(){
	return true;
}

function advertisingtracker_dohook($hookname,$args){
	global $session;
	switch ($hookname) {
	case "superuser":
		if (($session['user']['superuser'] & SU_EDIT_COMMENTS)== SU_EDIT_COMMENTS) {
			addnav("Mechanics");
			addnav("Advertising Tracker","runmodule.php?module=advertisingtracker&op=commentary");
		}

	break;
	}
	return $args;
}

function advertisingtracker_run(){
	global $session;
	$op=httpget('op');
	require_once("lib/superusernav.php");
	superusernav();
	page_header("Advertising Tracker");
	if (($session['user']['superuser'] & SU_EDIT_USERS)== SU_EDIT_USERS) {
		addnav("Mechanics");
		addnav("Commentary Tracker","runmodule.php?module=advertisingtracker&op=commentary");
		addnav("Mail Tracker","runmodule.php?module=advertisingtracker&op=mail");
		addnav("Mail Tracker (<50 chars)","runmodule.php?module=advertisingtracker&op=mail&length=50");
		addnav("Mail Tracker (<100 chars)","runmodule.php?module=advertisingtracker&op=mail&length=100");
		addnav("Mail Tracker (<150 chars)","runmodule.php?module=advertisingtracker&op=mail&length=150");
		addnav("Mail Tracker (<200 chars)","runmodule.php?module=advertisingtracker&op=mail&length=200");
		addnav("Mail Tracker (<250 chars)","runmodule.php?module=advertisingtracker&op=mail&length=250");
		addnav("Mail Tracker (Lotgd)","runmodule.php?module=advertisingtracker&op=mail&track=lotgd");
		addnav("Mail Tracker (Logd)","runmodule.php?module=advertisingtracker&op=mail&track=logd");
		addnav("Mail Tracker (freehostia)","runmodule.php?module=advertisingtracker&op=mail&track=freehostia");
		addnav("Mail Tracker (serverheaven.net)","runmodule.php?module=advertisingtracker&op=mail&track=serverheaven.net");
		if (($session['user']['superuser'] & SU_MEGAUSER)== SU_MEGAUSER) {
			addnav("User Actions");
			addnav("Wipe One Off","runmodule.php?module=advertisingtracker&op=wipe");
		}
		addnav("Commentary Searches");
		addnav("Search Comments by User","runmodule.php?module=advertisingtracker&op=presearch&track=user");
		addnav("Search Comments by String","runmodule.php?module=advertisingtracker&op=presearch&track=string");
		if (($session['user']['superuser'] & SU_MEGAUSER)== SU_MEGAUSER) addnav("Search Mail by User"
,"runmodule.php?module=advertisingtracker&op=presearchmail&track=user");

		//content search not made deliberately
	}
	switch ($op) {
		case "wipe":
			$action=httpget('action');
			output("`4You are now going to:`n`c`\$-> wipe a user from the game`n->insert mails to&from him from the mail into the abusemail table`nlog his account info into the abuseuser table`n`c`n");
			output("`4Think carefully before you do this! The char gets deleted, so the char restorer (if installed) will work. Though, it's a bad thing. ONLY do this if you have an absolute jerk on your server you want to get off immediately and still have his actions logged.`n`n");
			switch ($action) {
				case "reallywipe":
					$target=httpget('target');
					$sql="SELECT * FROM ".db_prefix('accounts')." WHERE acctid=$target LIMIT 1";
					$result=db_query($sql);
					$account=db_fetch_assoc($result);
					$sql="INSERT INTO ".db_prefix('abusemail')." SELECT * FROM ".db_prefix('mail')." WHERE msgto=$target OR msgfrom=$target;";
					db_query($sql);
					$sql="DELETE FROM ".db_prefix('mail')." WHERE msgto=$target OR msgfrom=$target;";
					$result=db_query($sql);
					$number=db_affected_rows($result);
					$sql="DESCRIBE ".db_prefix('abuseuser').";";
					$result=db_query($sql);
					$newsql="INSERT INTO ".db_prefix('abuseuser')." (";
					$array=array();
					while ($row=db_fetch_assoc($result)) {
						$newsql.=$row['Field'].",";
						$array[]=$row['Field'];
					}
					debug($newsql);
					$newsql=substr($newsql,0,strlen($newsql)-1).") VALUES (";
					foreach ($array as $field) {
						$newsql.="'".$account[$field]."',";
					}
					$newsql=substr($newsql,0,strlen($newsql)-1)."); ";
					debug($newsql);
					db_query($newsql); //insert infos
					require_once("lib/charcleanup.php");
					char_cleanup($target,"CHAR_DELETE_MANUAL"); //finish prefs
					$sql="DELETE FROM ".db_prefix('accounts')." WHERE acctid=$target LIMIT 1;";
					db_query($sql); //gone
					output("`4User %s`4 has been deleted, %s mails have been moved to the abusemail table.",$row['name'],$number);
					break;
					
				case "confirm":
					$target=httpget('target');
					$sql="SELECT * FROM ".db_prefix('accounts')." WHERE acctid=$target LIMIT 1";
					$result=db_query($sql);
					$row=db_fetch_assoc($result);
					output("`\$Do you really want to delete the user %s`\$, record his mails and so on?`n(Note: Mails displayed below to/from him)`%`n`n",$row['name']);
					addnav("User Wipe!");
					addnav(array("`\$Wipe user %s",$row['name']),"runmodule.php?module=advertisingtracker&op=wipe&action=reallywipe&target=$target");
					
					$ac=db_prefix("accounts");
					$mail=db_prefix("mail");
					$sql="SELECT $ac.name AS name,$mail.body AS body,$mail.sent AS date FROM $mail INNER JOIN $ac ON $mail.msgfrom=$ac.acctid WHERE msgto=$target OR msgfrom=$target ORDER BY $mail.messageid DESC LIMIT 200"; //acctid 7 = neji
					$result = db_query ($sql);
					$date=translate_inline("Postdate");
					$author=translate_inline("Author");
					$body=translate_inline("Message");
					rawoutput("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999' align=center>");
					rawoutput("<tr class='trhead' height=30px><td><b>$date</b></td><td><b>$author</b></td><td><b>$body</b></td></tr>");
					$class="trlight";
					while ($row=db_fetch_assoc($result)) {
						$class=($class=='trlight'?'trdark':'trlight');
						rawoutput("<tr height=30px class='$class'>");
						rawoutput("<td>");
						output_notl($row['date']);
						rawoutput("</td><td>");
						output_notl($row['name']);
						rawoutput("</td><td>");
						output_notl($row['body']);
						rawoutput("</td></tr>");

					}
					rawoutput("</table>");
					break;
				case "search":
					output("`4Pick the user -click the name- (still confirmation to wipe him after the click on the following page):`n`n");
					$class="trlight";
					$target=httppost('target');
					$sql="SELECT name, login, acctid FROM ".db_prefix('accounts')." WHERE (name LIKE '$target' OR login like '$target')";
					$result=db_query($sql);
					$acctid=translate_inline("Acctid");
					$name=translate_inline("Name");
					$login=translate_inline("Login");
					rawoutput("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999' align=center>");
					rawoutput("<tr class='trhead' height=30px><td><b>$acctid</b></td><td><b>$name</b></td><td><b>$login</b></td></tr>");
					while ($row=db_fetch_assoc($result)) {
						$class=($class=='trlight'?'trdark':'trlight');
						rawoutput("<tr height=30px class='$class'>");
						rawoutput("<td>");
						output_notl($row['acctid']);
						rawoutput("</td><td>");
						rawoutput("<a href='runmodule.php?module=advertisingtracker&op=wipe&action=confirm&target={$row['acctid']}'>".appoencode($row['name'])."</a>");
						addnav("","runmodule.php?module=advertisingtracker&op=wipe&action=confirm&target={$row['acctid']}");
						rawoutput("</td><td>");
						output_notl($row['login']);
						rawoutput("</td></tr>");

					}
					rawoutput("</table>");
				break;
				default:
					$submit=translate_inline("Submit");
					output("`2Remember to use % as placeholders for any amount of symbols! Else it gets exact matches!`n`n");
					output("`2Please enter what you look for:`n`n");
					rawoutput("<form action='runmodule.php?module=advertisingtracker&op=wipe&action=search' method='post'>");
					addnav("","runmodule.php?module=advertisingtracker&op=wipe&action=search");
					switch($track) {
						case "user":
							output("Loginname:`n");
							break;
						case "string":
							output("Commentcontent:`n");
							break;
					}
					rawoutput("<input type='input' name='target'>");
					rawoutput("<br><br><input type='submit' value='$submit'>");
					rawoutput("</form>");
				break;
			}
			break;
		case "mail":
			$track=httpget('track');
			$length=httpget('length');
			addnav("Actions");
			addnav("Refresh","runmodule.php?module=advertisingtracker&op=mail&track=$track");
			$ac=db_prefix("accounts");
			$mail=db_prefix("mail");
			if (get_module_setting('who')!='') {
				$cond=" AND $mail.msgfrom NOT IN (".get_module_setting('who').") ";
				$cond.=" AND $mail.msgto NOT IN (".get_module_setting('who').") ";
			} else $cond='';
			if ($track!='') $cond.=" AND body LIKE '%$track%'";
			if ($length!='') $cond.= " AND LENGTH(body)<=$length";
			$sql="SELECT $ac.name AS name,$mail.body AS body,$mail.sent AS date FROM $mail INNER JOIN $ac ON $mail.msgfrom=$ac.acctid WHERE (body like '%http://%' OR body like '%www.%' OR body like '%.com%') $cond ORDER BY $mail.messageid DESC LIMIT 200"; //acctid 7 = neji
			$result = db_query ($sql);
			$date=translate_inline("Postdate");
			$author=translate_inline("Author");
			$body=translate_inline("Message");
			rawoutput("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999' align=center>");
			rawoutput("<tr class='trhead' height=30px><td><b>$date</b></td><td><b>$author</b></td><td><b>$body</b></td></tr>");
			$class="trlight";
			$col="`&"; //not used
			output("Since bans here should be done either permanently or people normally only warned, no ban options are offered here`n`n");
			while ($row=db_fetch_assoc($result)) {
				$class=($class=='trlight'?'trdark':'trlight');
				rawoutput("<tr height=30px class='$class'>");
				rawoutput("<td>");
				output_notl($row['date']);
				rawoutput("</td><td>");
				output_notl($row['name']);
				rawoutput("</td><td>");
				output_notl($row['body']);
				rawoutput("</td></tr>");

			}
			rawoutput("</table>");
			break;
		case "commentary":
			addnav("Actions");
			addnav("Refresh","runmodule.php?module=advertisingtracker&op=commentary");
			$ac=db_prefix("accounts");
			$cm=db_prefix("commentary");
			$sql="SELECT $ac.name AS name,$cm.section AS section, $cm.postdate AS postdate, $cm.comment AS comment FROM $cm INNER JOIN $ac ON $cm.author=$ac.acctid WHERE comment like '%http://%' OR comment like '%www.%'  OR comment like '%.com%' ORDER BY $cm.commentid DESC LIMIT 50";
			$result = db_query ($sql);
			$date=translate_inline("Postdate");
			$author=translate_inline("Author");
			$section=translate_inline("Section");
			$comment=translate_inline("Comment");
			rawoutput("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999' align=center>");
			rawoutput("<tr class='trhead' height=30px><td><b>$date</b></td><td><b>$author</b></td><td><b>$section</b></td><td><b>$comment</b></td></tr>");
			$class="trlight";
			$col="`&";
			output("Since bans here should be done either permanently or people normally only warned, no ban options are offered here`n`n");
			while ($row=db_fetch_assoc($result)) {
				$class=($class=='trlight'?'trdark':'trlight');
				rawoutput("<tr height=30px class='$class'>");
				rawoutput("<td>");
				output_notl($row['postdate']);
				rawoutput("</td><td>");
				output_notl($row['name']);
				rawoutput("</td><td>");
				output_notl($row['section']);
				rawoutput("</td><td>");
				output_notl($row['comment']);
				rawoutput("</td></tr>");

			}
			rawoutput("</table>");
			break;
		case "presearch":
			$track=httpget('track');
			$submit=translate_inline("Submit");
			output("`2Remember to use % as placeholders for any amount of symbols! Else it gets exact matches!`n`n");
			output("`2Please enter what you look for:`n`n");
			rawoutput("<form action='runmodule.php?module=advertisingtracker&op=search&track=$track' method='post'>");
			addnav("","runmodule.php?module=advertisingtracker&op=search&track=$track");
			switch($track) {
				case "user":
					output("Loginname:`n");
					break;
				case "string":
					output("Commentcontent:`n");
					break;
			}
			rawoutput("<input type='input' name='target'>");
			output("`n`nHow many results (limit clause):`n");
			rawoutput("<input type='input' name='limit' value='50'>");
			output("`n`nIn what commentary section (if left empty, all will be displayed):`n");
			rawoutput("<input type='input' name='section'>");
			rawoutput("<br><br><input type='submit' value='$submit'>");
			rawoutput("</form>");
			break;
		case "search":
			$track=httpget('track');
			$target=httppost('target');
			$limit=httppost('limit');
			$section=httppost('section');
			$ac=db_prefix("accounts");
			$cm=db_prefix("commentary");
			if ($section=='') $searchsection='';
				else $searchsection="AND $cm.section LIKE '$section'";
			if ($limit=='') $limit=50;
			addnav("Actions");
			addnav("Clear Mask","runmodule.php?module=advertisingtracker&op=search&track=$track&target=$target");
			switch ($track) {
				case "user":
					$sql="SELECT $ac.name AS name,$cm.section AS section, $cm.postdate AS postdate, $cm.comment AS comment FROM $cm INNER JOIN $ac ON $cm.author=$ac.acctid WHERE $ac.login like '$target' $searchsection ORDER BY $cm.commentid DESC LIMIT $limit";
					break;
				case "string":
					$sql="SELECT $ac.name AS name,$cm.section AS section, $cm.postdate AS postdate, $cm.comment AS comment FROM $cm INNER JOIN $ac ON $cm.author=$ac.acctid WHERE $cm.comment like '$search' $searchsection ORDER BY $cm.commentid DESC LIMIT $limit";
					break;
			}

			$result = db_query ($sql);
			$date=translate_inline("Postdate");
			$author=translate_inline("Author");
			$section=translate_inline("Section");
			$comment=translate_inline("Comment");
			rawoutput("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999' align=center>");
			rawoutput("<tr class='trhead' height=30px><td><b>$date</b></td><td><b>$author</b></td><td><b>$section</b></td><td><b>$comment</b></td></tr>");
			$class="trlight";
			$col="`&";
			output("Since bans here should be done either permanently or people normally only warned, no ban options are offered here`n`n");
			while ($row=db_fetch_assoc($result)) {
				$class=($class=='trlight'?'trdark':'trlight');
				rawoutput("<tr height=30px class='$class'>");
				rawoutput("<td>");
				output_notl($row['postdate']);
				rawoutput("</td><td>");
				output_notl($row['name']);
				rawoutput("</td><td>");
				output_notl($row['section']);
				rawoutput("</td><td>");
				output_notl($row['comment']);
				rawoutput("</td></tr>");

			}
			rawoutput("</table>");
		case "presearchmail":
			$track=httpget('track');
			$submit=translate_inline("Submit");
			output("`2Remember to use % as placeholders for any amount of symbols! Else it gets exact matches!`n`n");
			output("`2Please enter what you look for:`n`n");
			rawoutput("<form action='runmodule.php?module=advertisingtracker&op=searchmail&track=$track' method='post'>");
			addnav("","runmodule.php?module=advertisingtracker&op=searchmail&track=$track");
			switch($track) {
				case "user":
					output("Loginname:`n");
					break;
				case "string":
					output("Commentcontent:`n");
					break;
			}
			rawoutput("<input type='input' name='target'>");
			output("`nMailpartner (leave empty if not wanted:`n");
			rawoutput("<input type='input' name='target2'>");
			output("`n`nHow many results (limit clause):`n");
			rawoutput("<input type='input' name='limit' value='50'>");
			if (is_module_active('outbox')) {
				output("`n`nSearch the outbox instead of the mailbox?:`n");
				rawoutput("<input type='checkbox' name='boxcheck'>");
			}
			rawoutput("<br><br><input type='submit' value='$submit'>");
			rawoutput("</form>");
			break;
		case "searchmail":
			$track=httpget('track');
			$target=httppost('target');
			$target2=httppost('target2');
			$boxcheck=httppost('boxcheck');
			$limit=httppost('limit');
			$ac=db_prefix("accounts");
			if ($boxcheck) {
				$mail=db_prefix("mailoutbox");
			} else {
				$mail=db_prefix("mail");
			}
			if ($section=='') $searchsection='';
				else $searchsection="AND $mail.section LIKE '$section'";
			if ($limit=='') $limit=50;
			addnav("Actions");
			addnav("Clear Mask","runmodule.php?module=advertisingtracker&op=search&track=$track&target=$target");
			$sql="SELECT acctid,name FROM ".db_prefix('accounts')." WHERE login LIKE '%$target%'";
			$result=db_query($sql);
			$row=db_fetch_assoc($result);
			if (((int)$row['acctid']) == 0) {
				output("`2`cNo users found!`c");
				break;
			} else {
				$acctid=(int) $row['acctid'];
				output("`2You are now viewing the outgoing mails of %s`2:`n`n",$row['name']);
			}
			
			switch ($track) {
				case "user":
					if ($target2) {
						$sql="SELECT acctid FROM ".db_prefix('accounts')." WHERE login LIKE '$target2';";
						$row=db_fetch_assoc(db_query($sql));
						$targetadd=" AND $mail.msgto='".$row['acctid']."' ";
					} else $targetadd='';
					$sql="SELECT $ac.name AS name, $mail.* FROM $mail INNER JOIN $ac ON $mail.msgto=$ac.acctid WHERE $mail.msgfrom=$acctid $searchsection $targetadd ORDER BY $mail.messageid DESC LIMIT $limit";
					break;
				case "string":
					$sql="SELECT $ac.name AS name, $mail.* FROM $mail INNER JOIN $ac ON $mail.msgto=$ac.acctid WHERE $mail.body like '$search' $searchsection ORDER BY $mail.messageid DESC LIMIT $limit";
					break;
			}
debug($sql);
			$result = db_query ($sql);
			$date=translate_inline("Postdate");
			$author=translate_inline("Recipient");
			$subject=translate_inline("Subject");
			$body=translate_inline("Body");
			rawoutput("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999' align=center width='100%'>");
			rawoutput("<tr class='trhead' height=30px><td><b>$date</b></td><td><b>$author</b></td><td><b>$subject</b></td><td><b>$body</b></td></tr>");
			$class="trlight";
			$col="`&";
			output("Since bans here should be done either permanently or people normally only warned, no ban options are offered here`n`n");
			while ($row=db_fetch_assoc($result)) {
				$class=($class=='trlight'?'trdark':'trlight');
				rawoutput("<tr height=30px class='$class'>");
				rawoutput("<td>");
				output_notl($row['sent']);
				rawoutput("</td><td>");
				output_notl($row['name']);
				rawoutput("</td><td>");
				output_notl($row['subject']);
				rawoutput("</td><td>");
				$out=unserialize($row['body']);
				if (is_array($out)) 
					output_notl(sprintf($out));
					else
					output_notl($row['body']);
				rawoutput("</td></tr>");

			}
			rawoutput("</table>");
		default:
	}
	page_footer();
}


?>
