<?php

$subject = httppost('subject');
$body = '';
$row = '';
$replyto = (int) httpget('replyto');
$forwardto = (int) httppost('forwardto');

if ($session['user']['superuser'] & SU_IS_GAMEMASTER)
{
    $from = httppost('from');
}

if ($replyto > 0)
{
    $msgid = $replyto;
}
else
{
    $msgid = $forwardto;
}

if ($msgid > 0)
{
    $mail = DB::prefix('mail');
    $accounts = DB::prefix('accounts');
    $sql = 'SELECT '.$mail.'.sent,'.$mail.'.body,'.$mail.'.msgfrom, '.$mail.'.subject,'.$accounts.'.login, '.$accounts.'.superuser, '.$accounts.'.name FROM '.$mail.' LEFT JOIN '.$accounts.' ON '.$accounts.'.acctid='.$mail.'.msgfrom WHERE msgto="'.$session['user']['acctid'].'" AND messageid="'.$replyto.'"';
    $result = DB::query($sql);

    if ($row = DB::fetch_assoc($result))
    {
        if ('' == $row['login'] && 0 == $forwardto)
        {
            output('You cannot reply to a system message.`n');
            $row = [];
            popup_footer();
        }

        if ($forwardto > 0)
        {
            $row['login'] = 0;
        }
    }
    else
    {
        output('Eek, no such message was found!`n');
    }
}
$to = httpget('to');

if ($to)
{
    $sql = 'SELECT login,name, superuser FROM '.DB::prefix('accounts')." WHERE login=\"$to\"";
    $result = DB::query($sql);

    if (! ($row = DB::fetch_assoc($result)))
    {
        output('Could not find that person.`n');
    }
}

if (is_array($row))
{
    if (isset($row['subject']) && $row['subject'])
    {
        $row['subject'] = stripslashes($row['subject']);
        $row['body'] = stripslashes($row['body']);

        if (0 == (int) $row['msgfrom'])
        {
            $row['name'] = translate_inline('`i`^System`0´i');
            // No translation for subject if it's not an array
            $row_subject = @unserialize($row['subject']);

            if (false !== $row_subject)
            {
                $row['subject'] = call_user_func_array('sprintf_translate', $row_subject);
            }
            // No translation for body if it's not an array
            $row_body = @unserialize($row['body']);

            if (false !== $row_body)
            {
                $row['body'] = call_user_func_array('sprintf_translate', $row_body);
            }
        }
        $subject = $row['subject'];

        if (0 !== strncmp($subject, 'RE: ', 4))
        {
            $subject = "RE: $subject";
        }
    }

    if (isset($row['body']) && $row['body'])
    {
        $body = "\n\n---".sprintf_translate(['Original Message from %s (%s)', sanitize($row['name']), date('Y-m-d H:i:s', strtotime($row['sent']))])."---\n".$row['body'];
    }
}
rawoutput("<form action='mail.php?op=send' method='post' class='ui form'>");
rawoutput("<input type='hidden' name='returnto' value=\"".htmlentities(stripslashes($msgid), ENT_COMPAT, getsetting('charset', 'UTF-8')).'">');
$superusers = [];

if (isset($row['login']) && '' != $row['login'])
{
    output_notl("<input type='hidden' name='to' id='to' value=\"".htmlentities($row['login'], ENT_COMPAT, getsetting('charset', 'UTF-8')).'">', true);
    output('`2To: `^%s`n', $row['name']);

    if (($row['superuser'] & SU_GIVES_YOM_WARNING) && ! ($row['superuser'] & SU_OVERRIDE_YOM_WARNING))
    {
        array_push($superusers, $row['login']);
    }
}
else
{
    rawoutput('<div class="inline field"></label>');
    output('`2To: ');
    rawoutput('</label>');
    $to = httppost('to');
    $sql = "SELECT login,name,superuser FROM accounts WHERE login = '".addslashes($to)."' AND locked = 0";
    $result = DB::query($sql);
    $count = DB::num_rows($result);

    if (1 != $count)
    {
        $string = '%';
        $to_len = strlen($to);

        for ($x = 0; $x < $to_len; $x++)
        {
            $string .= $to[$x].'%';
        }
        $sql = 'SELECT login,name,superuser FROM '.DB::prefix('accounts')." WHERE name LIKE '".addslashes($string)."' AND locked=0 ORDER by login='$to' DESC, name='$to' DESC, login";
        $result = DB::query($sql);
        $count = DB::num_rows($result);
    }

    if (1 == $count)
    {
        $row = DB::fetch_assoc($result);
        output_notl("<input type='hidden' id='to' name='to' value=\"".htmlentities($row['login'], ENT_COMPAT, getsetting('charset', 'ISO-8859-1')).'">', true);
        output_notl("`^{$row['name']}`n");

        if (($row['superuser'] & SU_GIVES_YOM_WARNING) && ! ($row['superuser'] & SU_OVERRIDE_YOM_WARNING))
        {
            array_push($superusers, $row['login']);
        }
    }
    elseif (0 == $count)
    {
        output('`$No one was found who matches "%s".`n', stripslashes($to));
        output('`@Please try again.`n');
        httpset('prepop', $to, true);
        rawoutput('</form>');
        require 'lib/mail/case_address.php';
        popup_footer();
    }
    else
    {
        output_notl("<select class='ui dropdown' name='to' id='to' onchange='check_su_warning();'>", true);
        $superusers = [];

        while ($row = DB::fetch_assoc($result))
        {
            output_notl('<option value="'.htmlentities($row['login'], ENT_COMPAT, getsetting('charset', 'UTF-8')).'">', true);
            require_once 'lib/sanitize.php';
            output_notl('%s', full_sanitize($row['name']));

            if (($row['superuser'] & SU_GIVES_YOM_WARNING) && ! ($row['superuser'] & SU_OVERRIDE_YOM_WARNING))
            {
                array_push($superusers, $row['login']);
            }
        }
        output_notl('</select>`n', true);
    }
}
rawoutput("<script type='text/javascript'>var superusers = new Array();");

foreach ($superusers as $val)
{
    rawoutput("	superusers['".addslashes($val)."'] = true;");
}
rawoutput("</script><div class='inline field'><label>");
output('`2Subject:');
rawoutput("</label><input name='subject' value=\"".htmlentities($subject, ENT_COMPAT, getsetting('charset', 'UTF-8')).htmlentities(stripslashes(httpget('subject')), ENT_COMPAT, getsetting('charset', 'UTF-8')).'"></div>');
rawoutput("<div id='warning' style='visibility: hidden; display: none;' class='ui warning message'>");
output('`bNotice:´b %s', $superusermessage);
rawoutput("</div><div class='inline field'><label>");
output('`2Body:');
rawoutput('</label><span id="sizemsg"></span>');

$key = 1;
$keyout = 'body';
$prefs = &$session['user']['prefs'];

//substr is necessary if you have chars that take up more than 1 byte. That breaks the entire HTMLentities up and it returns nothing
rawoutput("<textarea id='textarea$key' class='input' onKeyUp='sizeCount(this);' name='$keyout'>".htmlentities(str_replace('`n', "\n", mb_substr($body, 0, getsetting('mailsizelimit', 1024, getsetting('charset', 'UTF-8')))), ENT_COMPAT, getsetting('charset', 'UTF-8')).htmlentities(sanitize_mb(stripslashes(httpget('body'))), ENT_COMPAT, getsetting('charset', 'UTF-8')).'</textarea>');
$send = translate_inline('Send');
$sendclose = translate_inline('Send and Close');
$sendback = translate_inline('Send and back to main Mailbox');

rawoutput('</div><div class="ui buttons">');
rawoutput("<button type='submit' class='ui button'>$send</button>");
rawoutput("<button type='submit' class='ui orange button'>$sendback</button>");
rawoutput("<button type='submit' class='ui red button'>$sendclose</button>");
rawoutput('</div>');

rawoutput('</form>');
$sizemsg = '`#Max message size is `@%s`#, you have `^XX`# characters left.';
$sizemsg = translate_inline($sizemsg);
$sizemsg = sprintf($sizemsg, getsetting('mailsizelimit', 1024));
$sizemsgover = '`$Max message size is `@%s`$, you are over by `^XX`$ characters!';
$sizemsgover = translate_inline($sizemsgover);
$sizemsgover = sprintf($sizemsgover, getsetting('mailsizelimit', 1024));
$sizemsg = explode('XX', $sizemsg);
$sizemsgover = explode('XX', $sizemsgover);
$usize1 = addslashes('<span>'.appoencode($sizemsg[0]).'</span>');
$usize2 = addslashes('<span>'.appoencode($sizemsg[1]).'</span>');
$osize1 = addslashes('<span>'.appoencode($sizemsgover[0]).'</span>');
$osize2 = addslashes('<span>'.appoencode($sizemsgover[1]).'</span>');
rawoutput("
<script type='text/javascript'>
	var maxlen = ".getsetting('mailsizelimit', 1024).";
	function sizeCount(box){
		if (box==null) return;
		var len = box.value.length;
		var msg = '';
		if (len <= maxlen){
			msg = '$usize1'+(maxlen-len)+'$usize2';
		}else{
			msg = '$osize1'+(len-maxlen)+'$osize2';
		}
		document.getElementById('sizemsg').innerHTML = msg;
	}
	sizeCount(document.getElementById('textarea'));
	function check_su_warning(){
		var to = document.getElementById('to');
		var warning = document.getElementById('warning');
		if (superusers[to.value]){
			warning.style.visibility = 'visible';
			warning.style.display = 'block';
		}else{
			warning.style.visibility = 'hidden';
			warning.style.display = 'none';
		}
	}
	check_su_warning();
</script>");
