<?php

// addnews ready
// mail ready
// translator ready
require_once 'common.php';
require_once 'lib/http.php';

tlschema('taunt');

check_su_access(SU_EDIT_CREATURES);

page_header('Taunt Editor');
require_once 'lib/superusernav.php';
superusernav();
$op = httpget('op');
$tauntid = httpget('tauntid');

if ('edit' == $op)
{
    addnav('Taunts');
    addnav('Return to the taunt editor', 'taunt.php');
    rawoutput("<form action='taunt.php?op=save&tauntid=$tauntid' method='POST' class='ui form'>", true);
    addnav('', "taunt.php?op=save&tauntid=$tauntid");

    if ('' != $tauntid)
    {
        $sql = 'SELECT * FROM '.DB::prefix('taunts')." WHERE tauntid=\"$tauntid\"";
        $result = DB::query($sql);
        $row = DB::fetch_assoc($result);
        require_once 'lib/substitute.php';
        $badguy = ['creaturename' => 'Baron Munchausen', 'creatureweapon' => 'Bad Puns'];
        $taunt = substitute_array($row['taunt']);
        $taunt = call_user_func_array('sprintf_translate', $taunt);
        output('Preview: %s`0`n`n', $taunt);
    }
    else
    {
        $row = ['tauntid' => 0, 'taunt' => ''];
    }
    output('Taunt: ');
    rawoutput("<input name='taunt' value=\"".htmlentities($row['taunt'], ENT_COMPAT, getsetting('charset', 'UTF-8'))."\" size='70'><br>");
    output('The following codes are supported (case matters):`n');
    output("{goodguyname}	= The player's name (also can be specified as {goodguy}`n");
    output("{goodguyweapon}	= The player's weapon (also can be specified as {weapon}`n");
    output("{armorname}	= The player's armor (also can be specified as {armor}`n");
    output('{himher}	= Subjective pronoun for the player (him her)`n');
    output('{hisher}	= Possessive pronoun for the player (his her)`n');
    output('{heshe}		= Objective pronoun for the player (he she)`n');
    output("{badguyname}	= The monster's name (also can be specified as {badguy}`n");
    output("{badguyweapon}	= The monster's weapon (also can be specified as {creatureweapon}`n");
    $save = translate_inline('Save');
    rawoutput("<input type='submit' class='ui button' value='$save'>");
    rawoutput('</form>');
}
elseif ('del' == $op)
{
    $sql = 'DELETE FROM '.DB::prefix('taunts')." WHERE tauntid=\"$tauntid\"";
    DB::query($sql);
    $op = '';
    httpset('op', '');
}
elseif ('save' == $op)
{
    $taunt = httppost('taunt');

    if ('' != $tauntid)
    {
        $sql = 'UPDATE '.DB::prefix('taunts')." SET taunt=\"$taunt\",editor=\"".addslashes($session['user']['login'])."\" WHERE tauntid=\"$tauntid\"";
    }
    else
    {
        $sql = 'INSERT INTO '.DB::prefix('taunts')." (taunt,editor) VALUES (\"$taunt\",\"".addslashes($session['user']['login']).'")';
    }
    DB::query($sql);
    $op = '';
    httpset('op', '');
}

if ('' == $op)
{
    $sql = 'SELECT * FROM '.DB::prefix('taunts');
    $result = DB::query($sql);
    rawoutput("<table class='ui very compact striped selectable table'>");
    $op = translate_inline('Ops');
    $t = translate_inline('Taunt String');
    $auth = translate_inline('Author');
    rawoutput("<thead><tr><th>$op</th><th>$t</th><th>$auth</th></tr></thead>");
    $number = DB::num_rows($result);

    for ($i = 0; $i < $number; $i++)
    {
        $row = DB::fetch_assoc($result);
        rawoutput('<tr>');
        rawoutput("<td class='collapsing'>");
        $edit = translate_inline('Edit');
        $del = translate_inline('Del');
        $conf = translate_inline('Are you sure you wish to delete this taunt?');
        $id = $row['tauntid'];
        rawoutput("[ <a href='taunt.php?op=edit&tauntid=$id'>$edit</a> | <a href='taunt.php?op=del&tauntid=$id' onClick='return confirm(\"$conf\");'>$del</a> ]");
        addnav('', "taunt.php?op=edit&tauntid=$id");
        addnav('', "taunt.php?op=del&tauntid=$id");
        rawoutput('</td><td>');
        output_notl('%s', $row['taunt']);
        rawoutput('</td><td>');
        output_notl('%s', $row['editor']);
        rawoutput('</td></tr>');
    }
    addnav('', 'taunt.php?c='.httpget('c'));
    rawoutput('</table>');
    addnav('Taunts');
    addnav('Add a new taunt', 'taunt.php?op=edit');
}
page_footer();
