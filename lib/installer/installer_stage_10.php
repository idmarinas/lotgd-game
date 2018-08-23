<?php

output('`@`c`bSuperuser Accounts`b`c');
$sql = 'SELECT login, password FROM '.DB::prefix('accounts').' WHERE superuser & '.SU_MEGAUSER;
$result = DB::query($sql);

if (0 == DB::num_rows($result))
{
    if (httppost('name') > '')
    {
        $showform = false;

        if (httppost('pass1') != httppost('pass2'))
        {
            output("`\$Oops, your passwords don't match.`2`n");
            $showform = true;
        }
        elseif (strlen(httppost('pass1')) < 6)
        {
            output("`\$Whoa, that's a short password, you really should make it longer.`2`n");
            $showform = true;
        }
        else
        {
            // Give the superuser a decent set of privs so they can
            // do everything needed without having to first go into
            // the user editor and give themselves privs.
            $su = SU_MEGAUSER | SU_EDIT_MOUNTS | SU_EDIT_CREATURES |
            SU_EDIT_PETITIONS | SU_EDIT_COMMENTS | SU_EDIT_DONATIONS |
            SU_EDIT_USERS | SU_EDIT_CONFIG | SU_INFINITE_DAYS |
            SU_EDIT_EQUIPMENT | SU_EDIT_PAYLOG | SU_DEVELOPER |
            SU_POST_MOTD | SU_MODERATE_CLANS | SU_EDIT_RIDDLES |
            SU_MANAGE_MODULES | SU_AUDIT_MODERATION | SU_RAW_SQL |
            SU_VIEW_SOURCE | SU_NEVER_EXPIRE;
            $name = httppost('name');
            $pass = md5(md5(stripslashes(httppost('pass1'))));
            $sql = 'DELETE FROM '.DB::prefix('accounts')." WHERE login='$name'";
            DB::query($sql);
            $sql = 'INSERT INTO '.DB::prefix('accounts')." (login,password,superuser,name,ctitle,regdate) VALUES('$name','$pass',$su,'`%Admin `&$name`0','`%Admin', NOW())";
            $result = DB::query($sql);

            if (0 == DB::affected_rows($result))
            {
                print_r($sql);

                die('Failed to create Admin account. Your first check should be to make sure that MYSQL (if that is your type) is not in strict mode.');
            }
            output("`^Your superuser account has been created as `%Admin `&$name`^!");
            savesetting('installer_version', $logd_version);
        }
    }
    else
    {
        $showform = true;
        savesetting('installer_version', $logd_version);
    }

    if ($showform)
    {
        rawoutput("<br><div class='ui form'><form action='installer.php?stage=$stage' method='POST'><div class='inline field'><label>");
        output('Enter a name for your superuser account:');
        rawoutput("</label><input name='name' value=\"".htmlentities(httppost('name'), ENT_COMPAT, getsetting('charset', 'UTF-8'))."\"></div><div class='inline field'><label>");
        output('`nEnter a password: ');
        rawoutput("</label><input name='pass1' type='password'></div><div class='inline field'><label>");
        output('`nConfirm your password: ');
        rawoutput("</label><input name='pass2' type='password'></div>");
        $submit = translate_inline('Create');
        rawoutput("<div class='inline field'><input type='submit' value='$submit' class='ui button'></div>");
        rawoutput('</form>');
    }
}
else
{
    output('`#You already have a superuser account set up on this server.');
    savesetting('installer_version', $logd_version);
}
