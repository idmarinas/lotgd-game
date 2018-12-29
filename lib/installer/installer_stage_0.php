<?php

output('`@`c`bWelcome to Legend of the Green Dragon´b´c`0');
output('`2This is the installer script for Legend of the Green Dragon, by Eric Stevens & JT Traub.`n');
output('`nIn order to install and use Legend of the Green Dragon (LoGD), you must agree to the license under which it is deployed.`n');
output('`n`&This game is a small project into which we have invested a tremendous amount of personal effort, and we provide this to you absolutely free of charge.`2');
output("Please understand that if you modify our copyright, or otherwise violate the license, you are not only breaking international copyright law (which includes penalties which are defined in whichever country you live), but you're also defeating the spirit of open source, and ruining any good faith which we have demonstrated by providing our blood, sweat, and tears to you free of charge.  You should also know that by breaking the license even one time, it is within our rights to require you to permanently cease running LoGD forever.`n");
output('`nPlease note that in order to use the installer, you must have cookies enabled in your browser.`n');

$needsauthentication = false;
if (DB_CHOSEN)
{
    $sql = 'SELECT count(*) AS c FROM accounts WHERE superuser & '.SU_MEGAUSER;
    $result = DB::query($sql);
    $row = DB::fetch_assoc($result);

    if (0 == $row['c'])
    {
        $needsauthentication = false;
    }

    if (httppost('username') > '')
    {
        $password = stripslashes((string) httppost('password'));
        $sql = 'SELECT * FROM '.DB::prefix('accounts')." WHERE login='".httppost('username')."' AND password='".md5(md5($password))."' AND superuser & ".SU_MEGAUSER;
        $result = DB::query($sql);

        if ($result->count() > 0)
        {
            $row = DB::fetch_assoc($result);
            // Okay, we have a username with megauser, now we need to do
            // some hackery with the password.
            $needsauthentication = true;
            $p1 = md5($password);
            $p2 = md5($p1);

            if ('-1' == getsetting('installer_version', '-1'))
            {
                // Okay, they are upgrading from 0.9.7  they will have
                // either a non-encrypted password, or an encrypted singly
                // password.
                if (32 == strlen($row['password']) &&
                $row['password'] == $p1)
                {
                    $needsauthentication = false;
                }
                elseif ($row['password'] == $password)
                {
                    $needsauthentication = false;
                }
            }
            elseif ($row['password'] == $p2)
            {
                $needsauthentication = false;
            }

            if (false === $needsauthentication)
            {
                redirect('installer.php?stage=1');
            }
            output('`$That username / password was not found, or is not an account with sufficient privileges to perform the upgrade.`n');
        }
        else
        {
            $needsauthentication = true;
            output('`$That username / password was not found, or is not an account with sufficient privileges to perform the upgrade.`n');
        }

        unset($password);
    }
    else
    {
        $sql = 'SELECT count(*) AS c FROM '.DB::prefix('accounts').' WHERE superuser & '.SU_MEGAUSER;
        $result = DB::query($sql);
        $row = DB::fetch_assoc($result);

        $needsauthentication = false;
        if ($row['c'] > 0)
        {
            $needsauthentication = true;
        }
    }
}
//if a user with appropriate privs is already logged in, let's let them past.
if ($session['user']['superuser'] & SU_MEGAUSER)
{
    $needsauthentication = false;
}

if ($needsauthentication)
{
    $session['installer']['stagecompleted'] = -1;
    output('`n`%In order to upgrade this LoGD installation, you will need to provide the username and password of a superuser account with the MEGAUSER privilege`0`n`n');
    rawoutput("<form action='installer.php?stage=0' method='POST' class='ui form'>");
    rawoutput('<div class="inline field"><label>');
    output('`^Username: `0');
    rawoutput("</label><input name='username'></div>");
    rawoutput('<div class="inline field"><label>');
    output('`^Password: `0');
    rawoutput("</label><input type='password' name='password'></div>");
    $submit = translate_inline('Submit');
    rawoutput("<div class='field'><input type='submit' value='$submit' class='ui button'></div>");
    rawoutput('</form>');
}
else
{
    output('`nPlease continue on to the next page, "License Agreement."');
}
