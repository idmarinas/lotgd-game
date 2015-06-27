<?php
output("`b`iMail Box`i`b");
if (isset($session['message'])) {
	output($session['message']);
}
$session['message']="";
$mail = db_prefix("mail");
$accounts = db_prefix("accounts");
$sortorder=httpget('sortorder');
if ($sortorder=='') $sortorder='date';
switch ($sortorder) {
	case "subject":
		$order="subject";
		break;
	case "name":
		$order=$accounts.".name";
		break;
	default: //date
		$order="sent";
}
$sorting_direction=(int)httpget('direction');
if ($sorting_direction==0) $direction="DESC";
	else $direction="ASC";
$newdirection=(int)!$sorting_direction;

$sql = "SELECT subject,messageid,".$accounts.".name,".$accounts.".acctid,msgfrom,seen,sent FROM ".$mail." LEFT JOIN ".$accounts." ON ".$accounts.".acctid=".$mail.".msgfrom WHERE msgto=\"".$session['user']['acctid']."\" ORDER BY $order $direction";
$result = db_query($sql);
$db_num_rows = db_num_rows($result);
if ($db_num_rows>0){
	$no_subject = translate_inline("`i(No Subject)`i");
	$subject = translate_inline("Subject");
	$from = translate_inline("Sender");
	$date = translate_inline("SendDate");
	$arrow = ($sorting_direction?"arrow_down.png":"arrow_up.png");

	rawoutput("<form action='mail.php?op=process' method='post'><table>");
	rawoutput("<tr class='trhead'><td></td>");
	rawoutput("<td>".($sortorder=='subject'?"<img src='images/shapes/$arrow' alt='$arrow'":"")."<a href='mail.php?sortorder=subject&direction=".($sortorder=='subject'?$newdirection:$sorting_direction)."'>$subject</a></td>");
	rawoutput("<td>".($sortorder=='name'?"<img src='images/shapes/$arrow' alt='$arrow'":"")."<a href='mail.php?sortorder=name&direction=".($sortorder=='name'?$newdirection:$sorting_direction)."'>$from</a></td>");
	rawoutput("<td>".($sortorder=='date'?"<img src='images/shapes/$arrow' alt='$arrow'":"")."<a href='mail.php?sortorder=date&direction=".($sortorder=='date'?$newdirection:$sorting_direction)."'>$date</a></td>");
	rawoutput("</tr>");
	$from_list=array();
	$rows=array();
	$userlist=array();

	while($row = db_fetch_assoc($result)){
		$rows[]=$row;
		if ($row['acctid']) $userlist[]=$row['acctid'];
	}

	$user_statuslist=mass_is_player_online($userlist);

	foreach ($rows as $row) {

		rawoutput("<tr>");
		rawoutput("<td nowrap><input type='checkbox' id='".$row['messageid']."' name='msg[]' value='{$row['messageid']}'>");
		rawoutput("<img src='images/".($row['seen']?"old":"new")."scroll.GIF' width='16px' height='16px' alt='".($row['seen']?"Old":"New")."'></td>");
		rawoutput("<td>");
		$status_image="";
		if ((int)$row['msgfrom']==0){
			$row['name']=translate_inline("`i`^System`0`i");
			// Only translate the subject if it's an array, ie, it came from the game.
			$row_subject = @unserialize($row['subject']);
			if ($row_subject !== false) {
				$row['subject'] = call_user_func_array("sprintf_translate", $row_subject);
			}
		} elseif ($row['name']=='') {
			$row['name']=translate_inline("`i`^Deleted User`0`i");
		} else {
			//get status
			$online=$user_statuslist[$row['acctid']];
			$status=($online?"online":"offline");
			$status_image="<img src='images/$status.gif' alt='$status'>";
		}
		//collect sanitized names plus message IDs for later use
		$sname=sanitize($row['name']);
		if (!isset($from_list[$sname])) {
			$from_list[$sname]="'".$row['messageid']."'";
		} else {
			$from_list[$sname].=", '".$row['messageid']."'";
		}
		// In one line so the Translator doesn't screw the Html up
		rawoutput("<a href='mail.php?op=read&id={$row['messageid']}'>");
		output_notl(((trim($row['subject']))?$row['subject']:$no_subject));
		rawoutput("</a>");
		rawoutput("</td><td><a href='mail.php?op=read&id={$row['messageid']}'>");
		output_notl($row['name']);
		rawoutput("</a>$status_image</td><td><a href='mail.php?op=read&id={$row['messageid']}'>".date("M d, h:i a",strtotime($row['sent']))."</a></td>");
		rawoutput("</tr>");
	}
	rawoutput("</table>");
	$script="<script language='Javascript'>
					function check_all() {
						var elements = document.getElementsByName(\"msg[]\");
						var max = elements.length;
						var Zaehler=0;
						var checktext='".translate_inline("Check all")."';
						var unchecktext='".translate_inline("Uncheck all")."';
						var check = false;
						for (Zaehler=0;Zaehler<max;Zaehler++) {
							if (elements[Zaehler].checked==true) {
								check=true;
								break;
							}
						}
						if (check==false) {
							for (Zaehler=0;Zaehler<max;Zaehler++) {
								elements[Zaehler].checked=true;
								document.getElementById('button_check').value=unchecktext;
							}
						} else {
							for (Zaehler=0;Zaehler<max;Zaehler++) {
								elements[Zaehler].checked=false;
								document.getElementById('button_check').value=checktext;
							}
						}
					}
					function check_name(who) {
						if (who=='') return;
					";
	$add='';
	$i=0;
	$option="<option value=''>---</option>
		";
	foreach ($from_list as $key=>$ids) {
		if ($add=='') {
			$add="new Array(".$ids.")";
		} else $add.=",new Array(".$ids.")";
		$option.="<option value='$i'>".$key."</option>
			";
		$i++;
	}		
	$script.="var container = new Array($add);
			var who = document.getElementById('check_name_select').value;
			var unchecktext='".translate_inline("Uncheck all")."';
			for (var i=0;i<container[who].length;i++) {
				document.getElementById(container[who][i]).checked=true;
			}
			document.getElementById('button_check').value=unchecktext;
		}
					</script>";
	rawoutput($script);
	$checkall = htmlentities(translate_inline("Check All"), ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
	$delchecked = htmlentities(translate_inline("Delete Checked"), ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
	$checknames = htmlentities(translate_inline("`vCheck by Name"), ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
	output_notl($checknames." <select onchange='check_name()' id='check_name_select'>".$option."</select><br>",true);
	rawoutput("<input type='button' id='button_check' value=\"$checkall\" class='button' onClick='check_all()'>");
	rawoutput("<input type='submit' class='button' value=\"$delchecked\">");
	//enter here more input buttons as you like, you can then evaluate them via the mailfunctions hook
	modulehook("mailform",array());
	//end of hooking
	rawoutput("</form>");
}else{
	output("`i`4Aww, you have no mail, how sad.`i");
}
output("`n`n`i`lYou currently have %s messages in your inbox.`nYou will no longer be able to receive messages from players if you have more than %s unread messages in your inbox.  `nMessages are automatically deleted (read or unread) after %s days.",db_num_rows($result),getsetting('inboxlimit',50),getsetting("oldmail",14));
?>
