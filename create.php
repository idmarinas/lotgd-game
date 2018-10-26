<?php

// translator ready
// addnews ready
// mail ready
define('ALLOW_ANONYMOUS', true);

require_once 'common.php';
require_once 'lib/is_email.php';
require_once 'lib/checkban.php';
require_once 'lib/sanitize.php';
require_once 'lib/serverfunctions.class.php';

//-- Settings extended
$settings_extended = LotgdLocator::get(Lotgd\Core\Lib\SettingsExtended::class);

checkban();

tlschema('create');

$trash = getsetting('expiretrashacct', 1);
$new = getsetting('expirenewacct', 10);
$old = getsetting('expireoldacct', 45);

$op = httpget('op');

if ('val' == $op || 'forgotval' == $op)
{
    if (true == ServerFunctions::isTheServerFull())
    {
        //server is full, your "cheat" does not work here buddy ;) you can't bypass this!
        page_header('Account Validation');
        output('Sorry, there are too many people online. Click at the link you used to get here later on. Thank you.');
        addnav('Login', 'index.php');

        page_footer();
    }
}

if ('forgotval' == $op)
{
    $id = httpget('id');
    $select = DB::select('accounts');
    $select->columns(['acctid', 'login', 'superuser', 'password', 'name', 'replaceemail', 'emailaddress', 'emailvalidation'])
        ->where->equalTo('forgottenpassword', $id)
            ->notEqualTo('forgottenpassword', '')
    ;

    $result = DB::execute($select);

    if (DB::num_rows($result) > 0)
    {
        $row = $result->current();

        $sql = 'UPDATE '.DB::prefix('accounts')." SET forgottenpassword='' WHERE forgottenpassword='$id';";
        DB::query($sql);
        output('`#`cYour login request has been validated.  You may now log in.`c`0');
        rawoutput("<form action='login.php' method='POST'>");
        rawoutput("<input name='name' value=\"{$row['login']}\" type='hidden'>");
        rawoutput("<input name='password' value=\"!md52!{$row['password']}\" type='hidden'>");
        rawoutput("<input name='force' value='1' type='hidden'>");
        $click = translate_inline('Click here to log in');
        rawoutput("<input type='submit' class='ui button' value='$click'></form>");
        output_notl('`n');

        if ($trash > 0)
        {
            output('`^Characters that have never been logged into will be deleted after %s day(s) of no activity.`n`0', $trash);
        }

        if ($new > 0)
        {
            output('`^Characters that have never reached level 2 will be deleted after %s days of no activity.`n`0', $new);
        }

        if ($old > 0)
        {
            output('`^Characters that have reached level 2 at least once will be deleted after %s days of no activity.`n`0', $old);
        }
        //rare case: we have somebody who deleted his first validation email and then requests a forgotten PW...
        if ('' != $row['emailvalidation'] && 'x' != substr($row['emailvalidation'], 0, 1))
        {
            $sql = 'UPDATE '.DB::prefix('accounts')." SET emailvalidation='' WHERE acctid=".$row['acctid'];
            DB::query($sql);
        }
    }
    else
    {
        output('`#Your request could not be verified.`n`n');
        output('This may be because the link you used is invalid.');
        output("Try to log in, and if that doesn't help, use the 'Forgotten Password' option to retrieve a new mail.`n`nIn case of all hope lost, use the petition link at the bottom of the page and provide ALL details with what you did and what info you got.`n`n");
    }
}
elseif ('val' == $op)
{
    $id = httpget('id');
    $select = DB::select('accounts');
    $select->columns(['acctid', 'login', 'superuser', 'password', 'name', 'replaceemail', 'emailaddress'])
        ->where->equalTo('emailvalidation', $id)
            ->notEqualTo('emailvalidation', '')
    ;

    $result = DB::execute($select);

    if (DB::num_rows($result) > 0)
    {
        $row = $result->current();

        if ('' != $row['replaceemail'])
        {
            $replace_array = explode('|', $row['replaceemail']);
            $replaceemail = $replace_array[0]; //1==date
            //note: remove any forgotten password request!
            $sql = 'UPDATE '.DB::prefix('accounts')." SET emailaddress='".$replaceemail."', replaceemail='',forgottenpassword='' WHERE emailvalidation='$id';";
            DB::query($sql);
            output('`#`c Email changed successfully!`c`0`n');
            require_once 'lib/debuglog.php';
            debuglog('Email change request validated by link from '.$row['emailaddress'].' to '.$replaceemail, $row['acctid'], $row['acctid'], 'Email');
            //If a superuser changes email, we want to know about it... at least those who can ee it anyway, the user editors...
            if ($row['superuser'] > 0)
            {
                // 5 failed attempts for superuser, 10 for regular user
                // send a system message to admin
                require_once 'lib/systemmail.php';
                $sql = 'SELECT acctid FROM '.DB::prefix('accounts').' WHERE (superuser&'.SU_EDIT_USERS.')';
                $result2 = DB::query($sql);
                $subj = translate_mail(['`#%s`j has changed the email address', $row['name']], 0);
                $alert = translate_mail(["Email change request validated by link to %s from %s originally for login '%s'.", $replaceemail, $row['emailaddress'], $row['login']], 0);

                while ($row2 = DB::fetch_assoc($result2))
                {
                    $msg = translate_mail(['This message is generated as a result of an email change to a superuser account.  Log Follows:`n`n%s', $alert], 0);
                    systemmail($row2['acctid'], $subj, $msg, 0, $noemail);
                }
            }
        }
        $sql = 'UPDATE '.DB::prefix('accounts')." SET emailvalidation='' WHERE emailvalidation='$id';";
        DB::query($sql);
        output('`#`cYour email has been validated.  You may now log in.`c`0');
        output('Your email has been validated, your login name is `^%s`0.`n`n',
                $row['login']);

        if ('' == $row['replaceemail'])
        {
            //no auto-login for email changers
            rawoutput("<form action='login.php' method='POST'>");
            rawoutput("<input name='name' value=\"{$row['login']}\" type='hidden'>");
            rawoutput("<input name='password' value=\"!md52!{$row['password']}\" type='hidden'>");
            rawoutput("<input name='force' value='1' type='hidden'>");
            $click = translate_inline('Click here to log in');
            rawoutput("<input type='submit' class='ui button' value='$click'></form>");
        }
        output_notl('`n');

        if ($trash > 0)
        {
            output('`^Characters that have never been logged into will be deleted after %s day(s) of no activity.`n`0', $trash);
        }

        if ($new > 0)
        {
            output('`^Characters that have never reached level 2 will be deleted after %s days of no activity.`n`0', $new);
        }

        if ($old > 0)
        {
            output('`^Characters that have reached level 2 at least once will be deleted after %s days of no activity.`n`0', $old);
        }
        savesetting('newestplayer', $row['acctid']);
    }
    else
    {
        output('`#Your email could not be verified.`n`n');
        output('This may be because you already validated your email.');
        output("Try to log in, and if that doesn't help, use the 'Forgotten Password' option to retrieve a new mail.`n`nIn case of all hope lost, use the petition link at the bottom of the page and provide ALL details with what you did and what info you got.`n`n");
    }
}

if ('forgot' == $op)
{
    $charname = httppost('charname');

    if ('' != $charname)
    {
        $sql = 'SELECT acctid,login,emailaddress,forgottenpassword,password FROM '.DB::prefix('accounts')." WHERE login='$charname'";
        $result = DB::query($sql);

        if (0 < DB::num_rows($result))
        {
            $row = DB::fetch_assoc($result);

            if ('' != trim($row['emailaddress']))
            {
                if ('' == $row['forgottenpassword'])
                {
                    $row['forgottenpassword'] = substr('x'.md5(date('Y-m-d H:i:s').$row['password']), 0, 32);
                    $sql = 'UPDATE '.DB::prefix('accounts')." SET forgottenpassword='{$row['forgottenpassword']}' where login='{$row['login']}'";
                    DB::query($sql);
                }

                $subj = translate_mail($settings_extended->getSetting('forgottenpasswordmailsubject'), $row['acctid']);
                $msg = translate_mail($settings_extended->getSetting('forgottenpasswordmailtext'), $row['acctid']);
                $replace = [
                    '{login}' => $row['login'],
                    '{acctid}' => $row['acctid'],
                    '{emailaddress}' => $row['emailaddress'],
                    '{requester_ip}' => $_SERVER['REMOTE_ADDR'],
                    '{gameurl}' => (443 == $_SERVER['SERVER_PORT'] ? 'https' : 'http').'://'.($_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']),
                    '{forgottenid}' => $row['forgottenpassword'],
                ];

                $keys = array_keys($replace);
                $values = array_values($replace);
                $msg = str_replace($keys, $values, $msg);

                lotgd_mail($row['emailaddress'], $subj, str_replace('`n', "\n", $msg));
                output('`#Sent a new validation email to the address on file for that account.');
                output('You may use the validation email to log in and change your password.');
            }
            else
            {
                output("`#We're sorry, but that account does not have an email address associated with it, and so we cannot help you with your forgotten password.");
                output('Use the Petition for Help link at the bottom of the page to request help with resolving your problem.');
            }
        }
        else
        {
            output('`#Could not locate a character with that name.');
            output("Look at the List Warriors page off the login page to make sure that the character hasn't expired and been deleted.");
        }
    }
    else
    {
        rawoutput("<form action='create.php?op=forgot' method='POST'>");

        output_notl('`n');

        $data = [
            'requireemail' => getsetting('requireemail', 0),
            'sendbutton' => translate_inline('Email me my password')
        ];
        output_notl(LotgdTheme::renderThemeTemplate('content/forgot.twig', $data), true);
        unset($data);
        rawoutput('</form>');
    }
}
page_header('Create A Character');

if (0 == getsetting('allowcreation', 1))
{
    output('`$Creation of new accounts is disabled on this server.');
    output('You may try it again another day or contact an administrator.');
}
else
{
    if ('create' == $op)
    {
        $emailverification = '';
        $shortname = sanitize_name(getsetting('spaceinname', 0), httppost('name'));

        if (soap($shortname) != $shortname)
        {
            output('`$Error`^: Bad language was found in your name, please consider revising it.`n');
            $op = '';
        }
        else
        {
            $blockaccount = false;
            $msg = '';
            $email = httppost('email');
            $pass1 = httppost('pass1');
            $pass2 = httppost('pass2');

            if (1 == getsetting('blockdupeemail', 0) && 1 == getsetting('requireemail', 0))
            {
                $sql = 'SELECT login FROM '.DB::prefix('accounts')." WHERE emailaddress='".DB::quoteValue($email)."'";
                $result = DB::query($sql);

                if (DB::num_rows($result) > 0)
                {
                    $blockaccount = true;
                    $msg .= translate_inline('You may have only one account.`n');
                }
            }

            $passlen = (int) httppost('passlen');

            if ('!md5!' != substr($pass1, 0, 5) && '!md52!' != substr($pass1, 0, 6))
            {
                $passlen = strlen($pass1);
            }

            if ($passlen <= 3)
            {
                $msg .= translate_inline('Your password must be at least 4 characters long.`n');
                $blockaccount = true;
            }

            if ($pass1 != $pass2)
            {
                $msg .= translate_inline('Your passwords do not match.`n');
                $blockaccount = true;
            }

            if (strlen($shortname) < 3)
            {
                $msg .= translate_inline('Your name must be at least 3 characters long.`n');
                $blockaccount = true;
            }

            if (strlen($shortname) > 25)
            {
                $msg .= translate_inline("Your character's name cannot exceed 25 characters.`n");
                $blockaccount = true;
            }

            if (1 == getsetting('requireemail', 0) && is_email($email) || 0 == getsetting('requireemail', 0))
            {
            }
            else
            {
                $msg .= translate_inline('You must enter a valid email address.`n');
                $blockaccount = true;
            }
            $args = modulehook('check-create', httpallpost());

            if (isset($args['blockaccount']) && $args['blockaccount'])
            {
                $msg .= $args['msg'];
                $blockaccount = true;
            }

            if (! $blockaccount)
            {
                $shortname = preg_replace("/\s+/", ' ', $shortname);
                $sql = 'SELECT name FROM '.DB::prefix('accounts')." WHERE login='$shortname'";
                $result = DB::query($sql);

                if (DB::num_rows($result) > 0)
                {
                    output('`$Error`^: Someone is already known by that name in this realm, please try again.');
                    $op = '';
                }
                else
                {
                    $sex = (int) httppost('sex');
                    // Inserted the following line to prevent hacking
                    // Reported by Eliwood
                    if (SEX_MALE != $sex)
                    {
                        $sex = SEX_FEMALE;
                    }

                    require_once 'lib/titles.php';

                    $title = get_dk_title(0, $sex);

                    if (getsetting('requirevalidemail', 0))
                    {
                        $emailverification = md5(date('Y-m-d H:i:s').$email);
                    }
                    $refer = httpget('r');

                    if ($refer > '')
                    {
                        $sql = 'SELECT acctid FROM '.DB::prefix('accounts')." WHERE login='".DB::quoteValue($refer)."'";
                        $result = DB::query($sql);
                        $ref = DB::fetch_assoc($result);
                        $referer = $ref['acctid'];
                    }
                    else
                    {
                        $referer = 0;
                    }
                    $dbpass = '';

                    if ('!md5!' == substr($pass1, 0, 5))
                    {
                        $dbpass = md5(substr($pass1, 5));
                    }
                    else
                    {
                        $dbpass = md5(md5($pass1));
                    }
                    $sql = 'INSERT INTO '.DB::prefix('accounts')."
						(playername,name, superuser, title, password, sex, login, laston, uniqueid, lastip, gold, location, emailaddress, emailvalidation, referer, regdate)
						VALUES
						('$shortname','$title $shortname', '".getsetting('defaultsuperuser', 0)."', '$title', '$dbpass', '$sex', '$shortname', '".date('Y-m-d H:i:s', strtotime('-1 day'))."', '".$_COOKIE['lgi']."', '".$_SERVER['REMOTE_ADDR']."', ".getsetting('newplayerstartgold', 50).", '".addslashes(getsetting('villagename', LOCATION_FIELDS))."', '$email', '$emailverification', '$referer', NOW())";
                    DB::query($sql);

                    if (DB::affected_rows() <= 0)
                    {
                        output('`$Error`^: Your account was not created for an unknown reason, please try again. ');
                    }
                    else
                    {
                        $sql = 'SELECT acctid, emailaddress  FROM '.DB::prefix('accounts')." WHERE login='$shortname'";
                        $result = DB::query($sql);
                        $row = DB::fetch_assoc($result);
                        $args = httpallpost();
                        $args['acctid'] = $row['acctid'];
                        //insert output
                        $sql_output = 'INSERT INTO '.DB::prefix('accounts_output')." VALUES ({$row['acctid']},'');";
                        DB::query($sql_output);
                        //end
                        modulehook('process-create', $args);

                        if ('' != $emailverification)
                        {
                            $subj = translate_mail($settings_extended->getSetting('verificationmailsubject'), 0);
                            $msg = translate_mail($settings_extended->getSetting('verificationmailtext'), 0);
                            $replace = [
                                '{login}' => $shortname,
                                '{acctid}' => $row['acctid'],
                                '{emailaddress}' => $row['emailaddress'],
                                '{gameurl}' => (443 == $_SERVER['SERVER_PORT'] ? 'https' : 'http').'://'.($_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']),
                                '{validationid}' => $emailverification,
                            ];

                            $keys = array_keys($replace);
                            $values = array_values($replace);
                            $msg = str_replace($keys, $values, $msg);
                            lotgd_mail($email, $subj, str_replace('`n', "\n", $msg));
                            output('`4An email was sent to `$%s`4 to validate your address.  Click the link in the email to activate your account.`0`n`n', $email);
                        }
                        else
                        {
                            rawoutput("<form action='login.php' method='POST'>");
                            rawoutput("<input name='name' value=\"$shortname\" type='hidden'>");
                            rawoutput("<input name='password' value=\"$pass1\" type='hidden'>");
                            output('Your account was created, your login name is `^%s`0.`n`n', $shortname);
                            $click = translate_inline('Click here to log in');
                            rawoutput("<input type='submit' class='ui button' value='$click'>");
                            rawoutput('</form>');
                            output_notl('`n');
                            savesetting('newestplayer', $row['acctid']);
                        }

                        if ($trash > 0)
                        {
                            output('`^Characters that have never been logged into will be deleted after %s day(s) of no activity.`n`0', $trash);
                        }

                        if ($new > 0)
                        {
                            output('`^Characters that have never reached level 2 will be deleted after %s days of no activity.`n`0', $new);
                        }

                        if ($old > 0)
                        {
                            output('`^Characters that have reached level 2 at least once will be deleted after %s days of no activity.`n`0', $old);
                        }
                    }
                }
            }
            else
            {
                output('`$Error`^:`n%s', $msg);
                $op = '';
            }
        }
    }

    if ('' == $op)
    {
        output('`&`c`bCreate a Character`b`c`0');
        $refer = httpget('r');

        if ($refer)
        {
            $refer = '&r='.htmlentities($refer, ENT_COMPAT, getsetting('charset', 'UTF-8'));
        }

        rawoutput("<script language='JavaScript' src='resources/md5.js'></script>");
        rawoutput("<script language='JavaScript'>
		<!--
		function md5pass(){
			// encode passwords
			var plen = document.getElementById('passlen');
			var pass1 = document.getElementById('pass1');
			plen.value = pass1.value.length;

			if(pass1.value.substring(0, 5) != '!md5!') {
				pass1.value = '!md5!'+hex_md5(pass1.value);
			}
			var pass2 = document.getElementById('pass2');
			if(pass2.value.substring(0, 5) != '!md5!') {
				pass2.value = '!md5!'+hex_md5(pass2.value);
			}
		}
		//-->
		</script>");
        rawoutput("<form action=\"create.php?op=create$refer\" method='POST' onSubmit=\"md5pass();\">");
        // this is the first thing a new player will se, so let's make it look
        // better
        rawoutput("<input type='hidden' name='passlen' id='passlen' value='0'>");
        $r1 = translate_inline('`^(optional -- however, if you choose not to enter one, there will be no way that you can reset your password if you forget it!)`0');
        $r2 = translate_inline('`$(required)`0');
        $r3 = translate_inline('`$(required, an email will be sent to this address to verify it before you can log in)`0');

        if (0 == getsetting('requireemail', 0))
        {
            $req = $r1;
            $reqbool = false;
        }
        elseif (0 == getsetting('requirevalidemail', 0))
        {
            $req = $r2;
            $reqbool = true;
        }
        else
        {
            $req = $r3;
            $reqbool = true;
        }
        $data = [
            'createbutton' => translate_inline('Create your character'),
            'reqemailtext' => $req,
            'reqemail' => $reqbool
        ];
        $data = modulehook('create-form', $data);

        output_notl(LotgdTheme::renderThemeTemplate('content/register.twig', $data), true);
        unset($data);
        output_notl('`n`n');

        if ($trash > 0)
        {
            output('`^Characters that have never been logged into will be deleted after %s day(s) of no activity.`n`0', $trash);
        }

        if ($new > 0)
        {
            output('`^Characters that have never reached level 2 will be deleted after %s days of no activity.`n`0', $new);
        }

        if ($old > 0)
        {
            output('`^Characters that have reached level 2 at least once will be deleted after %s days of no activity.`n`0', $old);
        }
        rawoutput('</form>');
    }
}
addnav('Login page');
addnav('Login', 'index.php');

page_footer();
