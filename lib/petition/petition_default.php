<?php

tlschema('petition');
popup_header('Petition for Help');
$post = httpallpost();

if (count($post) > 0)
{
    $ip = explode('.', $_SERVER['REMOTE_ADDR']);
    array_pop($ip);
    $ip = join($ip, '.').'.';
    $sql = 'SELECT count(petitionid) AS c FROM '.DB::prefix('petitions')." WHERE (ip LIKE '$ip%' OR id = '".addslashes($_COOKIE['lgi'])."') AND date > '".date('Y-m-d H:i:s', strtotime('-1 day'))."'";
    $result = DB::query($sql);
    $row = DB::fetch_assoc($result);

    if ($row['c'] < 5 || (isset($session['user']['superuser']) && $session['user']['superuser'] & ~SU_DOESNT_GIVE_GROTTO))
    {
        if (! isset($session['user']['acctid']))
        {
            $session['user']['acctid'] = 0;
        }

        if (! isset($session['user']['password']))
        {
            $session['user']['password'] = '';
        }
        $p = $session['user']['password'];
        unset($session['user']['password']);
        $date = date('Y-m-d H:i:s');

        if (! isset($post['cancelpetition']))
        {
            $post['cancelpetition'] = false;
        }

        if (! isset($post['cancelreason']))
        {
            $post['cancelreason'] = 'The admins here decided they didn\'t like something about how you submitted your petition.  They were also too lazy to give a real reason.';
        }
        $post = modulehook('addpetition', $post);

        if (! $post['cancelpetition'])
        {
            $sql = 'INSERT INTO '.DB::prefix('petitions').' (author,date,body,pageinfo,ip,id) VALUES ('.(int) $session['user']['acctid'].",'$date',\"".addslashes(output_array($post)).'","'.addslashes(output_array($session, 'Session:'))."\",'{$_SERVER['REMOTE_ADDR']}','".addslashes($_COOKIE['lgi'])."')";
            DB::query($sql);
            // Fix the counter
            invalidatedatacache('petitioncounts');
            // If the admin wants it, email the petitions to them.
            if (getsetting('emailpetitions', 0))
            {
                // Yeah, the format of this is ugly.
                require_once 'lib/sanitize.php';
                $name = color_sanitize($session['user']['name']);
                $url = getsetting('serverurl',
                    'http://'.$_SERVER['SERVER_NAME'].
                    (80 == $_SERVER['SERVER_PORT'] ? '' : ':'.$_SERVER['SERVER_PORT']).
                    dirname($_SERVER['REQUEST_URI']));

                if (! preg_match('/\\/$/', $url))
                {
                    $url = $url.'/';
                    savesetting('serverurl', $url);
                }
                $tl_server = translate_inline('Server');
                $tl_author = translate_inline('Author');
                $tl_date = translate_inline('Date');
                $tl_body = translate_inline('Body');
                $tl_subject = sprintf_translate('New LoGD Petition at %s', $url);

                $msg = "$tl_server: $url\n";
                $msg .= "$tl_author: $name\n";
                $msg .= "$tl_date : $date\n";
                $msg .= "$tl_body :\n".output_array($post)."\n";
                lotgd_mail(getsetting('gameadminemail', 'postmaster@localhost.com'), $tl_subject, $msg);
            }
            $session['user']['password'] = $p;
            output('Your petition has been sent to the server admin.');
            output('Please be patient, most server admins have jobs and obligations beyond their game, so sometimes responses will take a while to be received.');
        }
        else
        {
            output('`$There was a problem with your petition!`n');
            output("`@Please read the information below carefully; there was a problem with your petition, and it was not submitted.\n");
            rawoutput('<blockquote>');
            output($post['cancelreason']);
            rawoutput('</blockquote>');
        }
    }
    else
    {
        output('`$`bError:`b There have already been %s petitions filed from your network in the last day; to prevent abuse of the petition system, you must wait until there have been 5 or fewer within the last 24 hours.', $row['c']);
        output('If you have multiple issues to bring up with the staff of this server, you might think about consolidating those issues to reduce the overall number of petitions you file.');
    }
}
else
{
    output('`c`b`$Before sending a petition, please make sure you have read the motd.`n');
    output('Petitions about problems we already know about just take up time we could be using to fix those problems.`b´c`n');
    rawoutput("<form action='petition.php?op=submit' method='POST' class='ui form'>");

    if ($session['user']['loggedin'])
    {
        rawoutput('<div class="inline field"><label>');
        output("Your Character's Name:");
        output_notl('%s', $session['user']['name']);
        rawoutput("</label><input type='hidden' name='charname' value=\"".htmlentities($session['user']['name'], ENT_COMPAT, getsetting('charset', 'UTF-8')).'">');
        rawoutput('</div><div class="inline field"><label>');
        output('`nYour email address: ');
        output_notl('%s', htmlentities($session['user']['emailaddress']));
        rawoutput("</label><input type='hidden' name='email' value=\"".htmlentities($session['user']['emailaddress'], ENT_COMPAT, getsetting('charset', 'UTF-8')).'">');
        rawoutput('</div>');
    }
    else
    {
        rawoutput('<div class="inline field"><label>');
        output("Your Character's Name: ");
        rawoutput("</label><input name='charname' value=''>");
        rawoutput('</div><div class="inline field"><label>');
        output('`nYour email address: ');
        rawoutput("</label><input name='email' value=''>");
        $nolog = translate_inline('Character is not logged in!!');
        rawoutput("<input name='unverified' type='hidden' value='$nolog'>");
        rawoutput('</div>');
    }
    rawoutput('<div class="inline field"><label>');
    output('`nType of your Problem / Enquiry: ');
    rawoutput("</label><select class='ui dropdown' name='problem_type'>");
    $types = getsetting('petition_types', 'General');
    $types = explode(',', $types);

    foreach ($types as $type)
    {
        $type = htmlentities($type, ENT_COMPAT, getsetting('charset', 'UTF-8'));
        rawoutput("<option value='".$type."'>$type</option>");
    }
    rawoutput("</select></div><div class='inline field'><label>");
    output('Description of the problem:');
    rawoutput('</label>');
    $abuse = httpget('abuse');

    if ('yes' == $abuse)
    {
        rawoutput("<textarea name='description'></textarea>");
        rawoutput("<input type='hidden' name='abuse' value=\"".stripslashes_deep(htmlentities(httpget('problem'), ENT_COMPAT, getsetting('charset', 'UTF-8'))).'"><br><hr><pre>'.stripslashes(htmlentities(httpget('problem'))).'</pre><hr><br>');
        rawoutput("<input type='hidden' name='abuseplayer' value=\"".httpget('abuseplayer').'">');
    }
    else
    {
        rawoutput("<textarea name='description'>".stripslashes_deep(htmlentities(httpget('problem'), ENT_COMPAT, getsetting('charset', 'UTF-8'))).'</textarea>');
    }
    rawoutput('</div>');
    modulehook('petitionform', []);
    $submit = translate_inline('Submit');
    rawoutput("<p><input type='submit' class='ui button' value='$submit'></p>");
    output('Please be as descriptive as possible in your petition.');
    output("If you have questions about how the game works, please check out the <a href='petition.php?op=faq'>FAQ</a>.", true);
    output('Petitions about game mechanics will more than likely not be answered unless they have something to do with a bug.');
    output('Remember, if you are not signed in, and do not provide an email address, we have no way to contact you.');
    rawoutput('</form>');
}
