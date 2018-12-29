<?php

require_once 'lib/installer/installer_functions.php';

rawoutput("<form id='install-3' action='installer.php?stage=4' method='POST'>");
output('`@`c`bDatabase Connection Information´b´c`2');
output('In order to run Legend of the Green Dragon, your server must have access to a MySQL database.');
output("If you are not sure if you meet this need, talk to server's Internet Service Provider (ISP), and make sure they offer MySQL databases.");
output("If you are running on your own machine or a server under your control, you can download and install MySQL from <a href='http://www.mysql.com/' target='_blank' rel='noopener noreferrer'>the MySQL website</a> for free.`n", true);

if (file_exists(\Lotgd\Core\Application::FILE_DB_CONNECT))
{
    output('There appears to already be a database setup file (%s) in your site, you can proceed to the next step.', \Lotgd\Core\Application::FILE_DB_CONNECT);
}
else
{
    output('`nIt looks like this is a new install of Legend of the Green Dragon.');
    output('First, thanks for installing LoGD!');
    output("In order to connect to the database server, I'll need the following information.");
    output("`iIf you are unsure of the answer to any of these questions, please check with your server's ISP, or read the documentation on MySQL´i`n");

    rawoutput('<div class="ui form"><div class="inline field"><label>');
    output('`n`@What is the driver of your DataBase?`0`n');
    rawoutput('</label><select class="ui dropdown" name="DB_DRIVER"><option value="Mysqli">Mysqli</option><option value="Pdo_Mysql" selected>Pdo_Mysql</option></select>');
    tip('Select de driver to connect to your Database. Default is PDO Mysql.');
    rawoutput('</div>');

    rawoutput('<div class="inline field"><label>');
    output('`n`@What is the address of your database server?`0`n');
    rawoutput("</label><input type='text' name='DB_HOST' value=\"".htmlentities($session['dbinfo']['DB_HOST'], ENT_COMPAT, getsetting('charset', 'utf-8')).'">');
    tip("If you are running LoGD from the same server as your database, use 'localhost' here.  Otherwise, you will have to find out what the address is of your database server.  Your server's ISP might be able to provide this information.");
    rawoutput('</div>');

    rawoutput('<div class="inline field"><label>');
    output('`n`@What is the username you use to connect to the database server?`0`n');
    rawoutput("</label><input type='text' name='DB_USER' value=\"".htmlentities($session['dbinfo']['DB_USER'], ENT_COMPAT, getsetting('charset', 'utf-8')).'">');
    tip("This username does not have to be the same one you use to connect to the database server for administrative reasons.  However, in order to use this installer, and to install some of the modules, the account you provide here must have the ability to create, modify, and drop tables.  If you want the installer to create a new database for LoGD, the account will also have to have the ability to create databases.  Finally, to run the game, this account must at a minimum be able to select, insert, update, and delete records, and be able to lock tables.  If you're uncertain, grant the account the following privileges: SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, and ALTER.");
    rawoutput('</div>');

    rawoutput('<div class="inline field"><label>');
    output('`n`@What is the password for this username?`0`n');
    rawoutput("</label><input type='password' name='DB_PASS' value=\"".htmlentities($session['dbinfo']['DB_PASS'], ENT_COMPAT, getsetting('charset', 'utf-8')).'">');
    tip('The password is necessary here in order for the game to successfully connect to the database server.  This information is not shared with anyone, it is simply used to configure the game.');
    rawoutput('</div>');

    rawoutput('<div class="inline field"><label>');
    output('`n`@What is the name of the database you wish to install LoGD in?`0`n');
    rawoutput("</label><input type='text' name='DB_NAME' value=\"".htmlentities($session['dbinfo']['DB_NAME'], ENT_COMPAT, getsetting('charset', 'utf-8')).'">');
    tip('Database servers such as MySQL can control many different databases.  This is very useful if you have many different programs each needing their own database.  Each database has a unique name.  Provide the name you wish to use for LoGD in this field.');
    rawoutput('</div>');

    rawoutput('<div class="inline field"><label>');
    output('`n`@Do you want to use datacaching (high load optimization)?`0`n');
    rawoutput('</label>');
    rawoutput('<div class="ui toggle checkbox"><input type="hidden" name="DB_USEDATACACHE" value="0"><input type="checkbox" value="1" name="DB_USEDATACACHE" '.($session['dbinfo']['DB_USEDATACACHE'] ? 'selected=\"selected\"' : '').'></div>');
    tip('Do you want to use a datacache for the sql queries? Many internal queries produce the same results and can be cached. This feature is *highly* recommended to use as the MySQL server is usually high frequented. When using in an environment where Safe Mode is enabled; this needs to be a path that has the same UID as the web server runs.');
    rawoutput('</div>');

    rawoutput('<div class="inline field"><label>');
    output('`n`@If yes, what is the path to the datacache directory?`0`n');
    rawoutput("</label><input type='text' name='DB_DATACACHEPATH' value=\"".htmlentities($session['dbinfo']['DB_DATACACHEPATH'], ENT_COMPAT, getsetting('charset', 'utf-8')).'">');
    tip('If you have chosen to use the datacache function, you have to enter a path here to where temporary files may be stored. Verify that you have the proper permission (777) set to this folder, else you will have lots of errors. Do NOT end with a slash / ... just enter the dir');
    rawoutput('</div>');

    /*
        $yes = translate_inline("Yes");
        $no = translate_inline("No");
        output("`nShould I attempt to create this database if it does not exist?`n");
        rawoutput("<select name='DB_CREATE'><option value='1'>$yes</option><option value='0'>$no</option></select>");
        tip("If this database doesn't exist, I'll try to create it for you if you like.");
    */
    $submit = 'Test this connection information.';
    $confirmSumit = 'Check all data before';
    rawoutput('<p></p><a onclick="document.getElementById(\'install-3\').submit(); return false;" class="ui animated primary button"><div class="visible content">'.$submit.'</div><div class="hidden content">'.$confirmSumit.'</div></a>');

    rawoutput('</div>');
}
rawoutput('</form>');
