<?php

// addnews ready
// mail ready
// translator ready
require_once 'common.php';
require_once 'lib/http.php';
require_once 'lib/superusernav.php';

check_su_access(SU_EDIT_CREATURES);

tlschema('deathmessage');

page_header('Deathmessage Editor');
superusernav();
$op = httpget('op');
$deathmessageid = httpget('deathmessageid');

switch ($op)
{
    case 'edit':
        addnav('Deathmessages');
        addnav('Return to the Deathmessage editor', 'deathmessages.php');

        if ('' != $deathmessageid)
        {
            require_once 'lib/substitute.php';

            $select = DB::select('deathmessages');
            $select->where->equalTo('deathmessageid', $deathmessageid);
            $row = DB::execute($select)->current();

            $badguy = ['creaturename' => '`2The Nasty Rabbit', 'creatureweapon' => 'Rabbit Ears'];
            $deathmessage = substitute_array($row['deathmessage'], ['{where}'], ['in the fields']);
            $deathmessage = call_user_func_array('sprintf_translate', $deathmessage);

            output('Preview: %s`0`n`n', $deathmessage);
        }
        else
        {
            $row = ['deathmessageid' => 0, 'deathmessage' => '', 'forest' => 0, 'graveyard' => 0, 'taunt' => 0];
        }

        $twig = [
            'content' => $row,
            'deathmessageid' => $deathmessageid
        ];

        rawoutput($lotgd_tpl->renderThemeTemplate('pages/deathmessages/edit.twig', $twig));

        break;
    case 'del':
        $sql = 'DELETE FROM '.DB::prefix('deathmessages')." WHERE deathmessageid=\"$deathmessageid\"";
        DB::query($sql);
        $op = '';
        httpset('op', '');
        break;
    case 'save':
        $deathmessage = httppost('deathmessage');
        $forest = (int) httppost('forest');
        $graveyard = (int) httppost('graveyard');
        $taunt = (int) httppost('taunt');

        if ('' != $deathmessageid)
        {
            $sql = 'UPDATE '.DB::prefix('deathmessages')." SET deathmessage=\"$deathmessage\",taunt=$taunt,forest=$forest,graveyard=$graveyard,editor=\"".addslashes($session['user']['login'])."\" WHERE deathmessageid=\"$deathmessageid\"";
        }
        else
        {
            $sql = 'INSERT INTO '.DB::prefix('deathmessages')." (deathmessage,taunt,forest,graveyard,editor) VALUES (\"$deathmessage\",$taunt,$forest,$graveyard,\"".addslashes($session['user']['login']).'")';
        }
        DB::query($sql);
        $op = '';
        httpset('op', '');
        break;
}

if ('' == $op)
{
    $select = DB::select('deathmessages');
    $result = DB::execute($select);

    addnav('Deathmessages');
    addnav('Add a new deathmessage', 'deathmessages.php?op=edit');

    $twig = [
        'deathmessages' => $result,
    ];

    rawoutput($lotgd_tpl->renderThemeTemplate('pages/deathmessages.twig', $twig));
}

page_footer();
