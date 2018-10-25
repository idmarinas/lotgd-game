<?php

require_once 'lib/installer/installer_functions.php';

if (httppostisset('DB_PREFIX') > '')
{
    $session['dbinfo']['DB_PREFIX'] = httppost('DB_PREFIX');
}

if ($session['dbinfo']['DB_PREFIX'] > '' && '_' != substr($session['dbinfo']['DB_PREFIX'], -1))
{
    $session['dbinfo']['DB_PREFIX'] .= '_';
}

$descriptors = descriptors($session['dbinfo']['DB_PREFIX']);
$unique = 0;
$game = 0;
$missing = 0;
$conflict = [];

//Note: this is mysql only, we should maybe rewrite that part. :/
//Or we could save ourselves the dbtype stuff

//-- Settings for Database Adapter
$adapter = new Lotgd\Core\Lib\Dbwrapper([
    'driver' => $session['dbinfo']['DB_DRIVER'],
    'hostname' => $session['dbinfo']['DB_HOST'],
    'database' => $session['dbinfo']['DB_NAME'],
    'charset' => 'utf8',
    'username' => $session['dbinfo']['DB_USER'],
    'password' => $session['dbinfo']['DB_PASS']
]);
$adapter->setPrefix($session['dbinfo']['DB_PREFIX']);
//-- Configure DB
DB::wrapper($adapter);

$link = DB::connect();

$metadata = new Zend\Db\Metadata\Metadata($adapter->getAdapter());
$tableNames = $metadata->getTableNames();
//the conflicts seems not to work - we should check this.
foreach ($tableNames as $key => $val)
{
    if (isset($descriptors[$val]))
    {
        $game++;
        array_push($conflict, $val);
    }
    else
    {
        $unique++;
    }
}

$missing = count($descriptors) - $game;

if ($missing * 10 < $game)
{
    $upgrade = true;
} //looks like an upgrade
else
{
    $upgrade = false;
}

if ('install' == httpget('type'))
{
    $upgrade = false;
}

if ('upgrade' == httpget('type'))
{
    $upgrade = true;
}

$session['dbinfo']['upgrade'] = $upgrade;

if ($upgrade)
{
    output('`@This looks like a game upgrade.');
    output("`^If this is not an upgrade from a previous version of LoGD, <a href='installer.php?stage=5&type=install'>click here</a>.", true);
    output('`2Otherwise, continue on to the next step.');
}
else
{
    //looks like a clean install
    $upgrade = false;
    output('`@This looks like a fresh install.');
    output("`2If this is not a fresh install, but rather an upgrade from a previous version of LoGD, chances are that you installed LoGD with a table prefix.  If that's the case, enter the prefix below.  If you are still getting this message, it's possible that I'm just spooked by how few tables are common to the current version, and in which case, I can try an upgrade if you <a href='installer.php?stage=5&type=upgrade'>click here</a>.`n", true);

    if (count($conflict) > 0)
    {
        output('`n`n`$There are table conflicts.`2');
        output("If you continue with an install, the following tables will be overwritten with the game's tables.  If the listed tables belong to LoGD, they will be upgraded, otherwise all existing data in those tables will be destroyed.  Once this is done, this cannot be undone unless you have a backup!`n");
        output('`nThese tables conflict: `^'.join(', ', $conflict).'`2`n');

        if ('confirm_overwrite' == httpget('op'))
        {
            $session['sure i want to overwrite the tables'] = true;
        }

        if (! isset($session['sure i want to overwrite the tables']) || ! $session['sure i want to overwrite the tables'])
        {
            $session['stagecompleted'] = 4;
            output("`nIf you are sure that you wish to overwrite these tables, <a href='installer.php?stage=5&op=confirm_overwrite'>click here</a>.`n", true);
        }
    }
    output('`nYou can avoid table conflicts with other applications in the same database by providing a table name prefix.');
    output('This prefix will get put on the name of every table in the database.');
}

//Display rights - I won't parse them, sue me for laziness, and this should work nicely to explain any errors
$result = DB::query('SHOW GRANTS FOR CURRENT_USER()');
output("`2These are the rights for your mysql user, `\$make sure you have the 'LOCK TABLES' privileges OR a \"GRANT ALL PRIVLEGES\" on the tables.`2`n`n");
output('If you do not know what this means, ask your hosting provider that supplied you with the database credentials.`n`n');
rawoutput("<table class='ui very compact striped selectable table'>");
$i = 0;

foreach ($result as $row)
{
    if (0 == $i)
    {
        rawoutput("<tr class='trhead'>");
        $keys = array_keys($row);

        foreach ($keys as $value)
        {
            rawoutput("<td>$value</td>");
        }
        rawoutput('</tr>');
    }
    rawoutput("<tr class='".(0 == $i % 2 ? 'trlight' : 'trdark')."'>");

    foreach ($keys as $value)
    {
        rawoutput("<td valign='top'>{$row[$value]}</td>");
    }

    rawoutput('</tr>');
    $i++;
}
rawoutput('</table>');
//done

output('`nTo provide a table prefix, enter it here.');
output("If you don't know what this means, you should either leave it blank, or enter an intuitive value such as \"logd\".`n");
rawoutput("<form action='installer.php?stage=5' method='POST' class='ui form'><div class='ui action input'>");
rawoutput("<input name='DB_PREFIX' value=\"".htmlentities($session['dbinfo']['DB_PREFIX'], ENT_COMPAT, getsetting("charset", "UTF-8"))."\"><br>");
$submit = translate_inline("Submit your prefix.");
rawoutput("<button type='submit' class='ui button'>$submit</button>");
rawoutput("</div></form>");
if (0 == count($conflict))
{
    output("`^It looks like you can probably safely skip this step if you don't know what it means.");
}
output('`n`n`@Once you have submitted your prefix, you will be returned to this page to select the next step.');
output("If you don't need a prefix, just select the next step now.");
