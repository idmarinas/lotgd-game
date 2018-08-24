<?php

// Initially written as a module by Chris Vorndran.
// Moved into core by JT Traub

require_once 'common.php';

check_su_access(SU_EDIT_CREATURES);

tlschema('masters');

$op = httpget('op');
$id = (int) httpget('id');
$act = httpget('act');

page_header('Masters Editor');
require_once 'lib/superusernav.php';
superusernav();

if ('del' == $op)
{
    $sql = 'DELETE FROM '.DB::prefix('masters')." WHERE creatureid=$id";
    DB::query($sql);
    output('`^Master deleted.`0');
    $op = '';
    httpset('op', '');
}
elseif ('save' == $op)
{
    $name = addslashes(httppost('name'));
    $weapon = addslashes(httppost('weapon'));
    $win = addslashes(httppost('win'));
    $lose = addslashes(httppost('lose'));
    $lev = (int) httppost('level');

    if (0 != $id)
    {
        $sql = 'UPDATE '.DB::prefix('masters')." SET creaturelevel=$lev, creaturename='$name', creatureweapon='$weapon',  creaturewin='$win', creaturelose='$lose' WHERE creatureid=$id";
    }
    else
    {
        $atk = $lev * 2;
        $def = $lev * 2;
        $hp = $lev * 11;

        if (11 == $hp)
        {
            $hp++;
        }
        $sql = 'INSERT INTO '.DB::prefix('masters')." (creatureid,creaturelevel,creaturename,creatureweapon,creaturewin,creaturelose,creaturehealth,creatureattack,creaturedefense) VALUES ($id,$lev,'$name', '$weapon', '$win', '$lose', '$hp', '$atk', '$def')";
    }
    DB::query($sql);

    if (0 == $id)
    {
        output('`^Master %s`^ added.', stripslashes($name));
    }
    else
    {
        output('`^Master %s`^ updated.', stripslashes($name));
    }
    $op = '';
    httpset('op', '');
}
elseif ('edit' == $op)
{
    addnav('Functions');
    addnav('Return to Masters Editor', 'masters.php');
    $sql = 'SELECT * FROM '.DB::prefix('masters')." WHERE creatureid=$id";
    $res = DB::query($sql);

    if (0 == DB::num_rows($res))
    {
        $row = [
            'creaturelevel' => 1,
            'creaturename' => '',
            'creatureweapon' => '',
            'creaturewin' => '',
            'creaturelose' => ''
        ];
    }
    else
    {
        $row = DB::fetch_assoc($res);
    }
    addnav('', "masters.php?op=save&id=$id");
    rawoutput("<form action='masters.php?op=save&id=$id' method='POST'>");
    output("`^Master's level:`n");
    rawoutput("<select name='level'>");
    $maxlevel = getsetting('maxlevel');

    for ($i = 0; $i < $maxlevel; $i++)
    {
        $selected = ($i == $row['creaturelevel'] ? ' selected' : '');
        rawoutput("<option$selected>$i</option>");
    }
    rawoutput('</select>');
    output_notl('`n');
    output("`^Master's name:`n");
    rawoutput("<input id='input' name='name' value='".htmlentities($row['creaturename'], ENT_COMPAT, getsetting('charset', 'UTF-8'))."'>");
    output_notl('`n');
    output("`^Master's weapon:`n");
    rawoutput("<input id='input' name='weapon' value='".htmlentities($row['creatureweapon'], ENT_COMPAT, getsetting('charset', 'UTF-8'))."'>");
    output_notl('`n');
    output("`^Master's speech when player wins:`n");
    rawoutput("<textarea name='lose' rows='5' cols='30' class='input'>".htmlentities($row['creaturelose'], ENT_COMPAT, getsetting('charset', 'UTF-8')).'</textarea>');
    output_notl('`n');
    output("`^Master's speech when player loses:`n");
    rawoutput("<textarea name='win' rows='5' cols='30' class='input'>".htmlentities($row['creaturewin'], ENT_COMPAT, getsetting('charset', 'UTF-8')).'</textarea>');
    output_notl('`n');
    $submit = translate_inline('Submit');
    rawoutput("<input type='submit' class='button' value='$submit'>");
    rawoutput('</form>');
    output_notl('`n`n');
    output('`#The following codes are supported in both the win and lose speeches (case matters):`n');
    output('The following codes are supported (case matters):`n');
    output("{goodguyname}	= The player's name (also can be specified as {goodguy}`n");
    output("{weaponname}	= The player's weapon (also can be specified as {weapon}`n");
    output("{armorname}	= The player's armor (also can be specified as {armor}`n");
    output('{himher}	= Subjective pronoun for the player (him her)`n');
    output('{hisher}	= Possessive pronoun for the player (his her)`n');
    output('{heshe}		= Objective pronoun for the player (he she)`n');
    output("{badguyname}	= The monster's name (also can be specified as {badguy}`n");
    output("{badguyweapon}	= The monster's weapon (also can be specified as {creatureweapon}`n");
}

if ('' == $op)
{
    addnav('Functions');
    addnav('Refresh list', 'masters.php');
    addnav('Add master', 'masters.php?op=edit&id=0');
    $sql = 'SELECT * FROM '.DB::prefix('masters').' ORDER BY creaturelevel';
    $res = DB::query($sql);
    $count = DB::num_rows($res);
    $ops = translate_inline('Ops');
    $edit = translate_inline('edit');
    $del = translate_inline('del');
    $delconfirm = translate_inline('Are you sure you wish to delete this master.');
    $name = translate_inline('Name');
    $level = translate_inline('Level');
    $lose = translate_inline('Lose to Master');
    $win = translate_inline('Win against Master');
    $weapon = translate_inline('Weapon');
    rawoutput("<table class='ui very compact striped selectable table'>");
    rawoutput("<thead><tr><th>$ops</th><th>$level</th><th>$name</th><th>$weapon</th><th>$win</th><th>$lose</tr></thead>");

    while ($row = DB::fetch_assoc($res))
    {
        $id = $row['creatureid'];
        rawoutput("<tr><td class='collapsing'>");
        rawoutput("[ <a href='masters.php?op=edit&id=$id'>");
        output_notl($edit);
        rawoutput("</a> | <a href='masters.php?op=del&id=$id' onClick='return confirm(\"$delconfirm\");'>");
        output_notl($del);
        rawoutput('] </a>');
        addnav('', "masters.php?op=edit&id=$id");
        addnav('', "masters.php?op=del&id=$id");
        rawoutput('</td><td>');
        output_notl('`%%s`0', $row['creaturelevel']);
        rawoutput('</td><td>');
        output_notl('`#%s`0', stripslashes($row['creaturename']));
        rawoutput('</td><td>');
        output_notl('`!%s`0', stripslashes($row['creatureweapon']));
        rawoutput('</td><td>');
        output_notl('`&%s`0', stripslashes($row['creaturelose']));
        rawoutput('</td><td>');
        output_notl('`^%s`0', stripslashes($row['creaturewin']));
        rawoutput('</td></tr>');
    }
    rawoutput('</table>');
    output('`n`#You can change the names, weapons and messages of all of the Training Masters.');
    output('`n`3You can add masters up to the maximum level where the dragon appears in the forest and which can be set in your game settings -> game setup. You cannot assign higher masters, but if you choose not to make one master for each level, the earlier master will appear again to the player to test him.`n');
    output('`#  It is suggested, that you do not toy around with this, unless you know what you are doing.`0`n');
}
page_footer();
