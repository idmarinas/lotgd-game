<?php

//Author: Lonny Luberts - 3/18/2005
//Heavily modified by JT Traub
require_once 'common.php';

check_su_access(SU_EDIT_USERS);

tlschema('retitle');

page_header('Title Editor');
$op = httpget('op');
$id = httpget('id');
$editarray = [
    'Titles,title',
    //"titleid"=>"Title Id,hidden",
    'dk' => 'Dragon Kills,int|0',
    // "ref"=>"Arbitrary Tag,int",
    'male' => 'Male Title,text|',
    'female' => 'Female Title,text|',
];
addnav('Other');
require_once 'lib/superusernav.php';
superusernav();
addnav('Functions');

switch ($op)
{
    case 'save':
        $male = httppost('male');
        $female = httppost('female');
        $dk = httppost('dk');
        // Ref is currently unused
        // $ref = httppost('ref');
        $ref = '';

        if (0 == (int) $id)
        {
            $sql = 'INSERT INTO '.DB::prefix('titles')." (titleid,dk,ref,male,female) VALUES ($id,$dk,'$ref','$male','$female')";
            $note = '`^New title added.`0';
            $errnote = '`$Unable to add title.`0';
        }
        else
        {
            $sql = 'UPDATE '.DB::prefix('titles')." SET dk=$dk,ref='$ref',male='$male',female='$female' WHERE titleid=$id";
            $note = '`^Title modified.`0';
            $errnote = '`$Unable to modify title.`0';
        }
        DB::query($sql);

        if (0 == DB::affected_rows())
        {
            output($errnote);
            rawoutput(DB::error());
        }
        else
        {
            output($note);
        }
        $op = '';
        break;
    case 'delete':
        $sql = 'DELETE FROM '.DB::prefix('titles')." WHERE titleid='$id'";
        DB::query($sql);
        output('`^Title deleted.`0');
        $op = '';
        break;
}

switch ($op)
{
    case 'reset':

        require_once 'lib/titles.php';
        require_once 'lib/names.php';

        output('`^Rebuilding all titles for all players.`0`n`n');
        $sql = 'SELECT name,title,dragonkills,acctid,sex,ctitle,playername FROM '.DB::prefix('accounts');
        $result = DB::query($sql);
        $number = DB::num_rows($result);

        for ($i = 0; $i < $number; $i++)
        {
            $row = DB::fetch_assoc($result);
            $oname = $row['name'];
            $dk = $row['dragonkills'];
            $otitle = $row['title'];
            $dk = (int) ($row['dragonkills']);

            if (! valid_dk_title($otitle, $dk, $row['sex']))
            {
                $sex = translate_inline($row['sex'] ? 'female' : 'male');
                $newtitle = get_dk_title($dk, (int) $row['sex']);
                $newname = change_player_title($newtitle, $row);
                $id = $row['acctid'];

                if ($oname != $newname)
                {
                    output('`@Changing `^%s`@ to `^%s `@(%s`@ [%s,%s])`n',
                            $oname, $newname, $newtitle, $dk, $sex);

                    if ($session['user']['acctid'] == $row['acctid'])
                    {
                        $session['user']['title'] = $newtitle;
                        $session['user']['name'] = $newname;
                    }
                    else
                    {
                        $sql = 'UPDATE '.DB::prefix('accounts')." SET name='".
                            addslashes($newname)."', title='".
                            addslashes($newtitle)."' WHERE acctid='$id'";
                        DB::query($sql);
                    }
                }
                elseif ($otitle != $newtitle)
                {
                    output('`@Changing only the title (not the name) of `^%s`@ `@(%s`@ [%s,%s])`n',
                            $oname, $newtitle, $dk, $sex);

                    if ($session['user']['acctid'] == $row['acctid'])
                    {
                        $session['user']['title'] = $newtitle;
                    }
                    else
                    {
                        $sql = 'UPDATE '.DB::prefix('accounts').
                            " SET title='".addslashes($newtitle).
                            "' WHERE acctid='$id'";
                        DB::query($sql);
                    }
                }
            }
        }
        output('`n`n`^Done.`0');
        addnav('Main Title Editor', 'titleedit.php');
        break;

    case 'edit': case 'add':
        require_once 'lib/showform.php';

        if ('edit' == $op)
        {
            output('`$Editing an existing title`n`n');
            $sql = 'SELECT * FROM '.DB::prefix('titles')." WHERE titleid='$id'";
            $result = DB::query($sql);
            $row = DB::fetch_assoc($result);
        }
        elseif ('add' == $op)
        {
            output('`$Adding a new title`n`n');
            $row = ['titleid' => 0, 'male' => '', 'female' => '', 'dk' => 0];
            $id = 0;
        }
        rawoutput("<form action='titleedit.php?op=save&id=$id' method='POST' class='ui form'>");
        addnav('', "titleedit.php?op=save&id=$id");
        lotgd_showform($editarray, $row);
        rawoutput('</form>');
        addnav('Functions');
        addnav('Main Title Editor', 'titleedit.php');
        title_help();
        //fallthrough

        // no break
        default:
            $sql = 'SELECT * FROM '.DB::prefix('titles').' ORDER BY dk, titleid';
            $result = DB::query($sql);

            if (DB::num_rows($result) < 1)
            {
                output('');
            }
            else
            {
                $row = DB::fetch_assoc($result);
            }
            output('`@`c`b-=Title Editor=-´b´c');
            $ops = translate_inline('Ops');
            $dks = translate_inline('Dragon Kills');
            // $ref is currently unused
            // $reftag = translate_inline("Reference Tag");
            $mtit = translate_inline('Male Title');
            $ftit = translate_inline('Female Title');
            $edit = translate_inline('Edit');
            $del = translate_inline('Delete');
            $delconfirm = translate_inline('Are you sure you wish to delete this title?');
            rawoutput("<table class='ui very compact striped selectable table'>");
            // reference tag is currently unused
            // rawoutput("<tr class='trhead'><td>$ops</td><td>$dks</td><td>$reftag</td><td>$mtit</td><td>$ftit</td></tr>");
            rawoutput("<thead><tr><th>$ops</th><th>$dks</th><th>$mtit</th><th>$ftit</th></tr></thead>");
            $result = DB::query($sql);
            $i = 0;

            while ($row = DB::fetch_assoc($result))
            {
                $id = $row['titleid'];
                rawoutput('<tr>');
                rawoutput("<td class='collapsing'>[<a data-tooltip='$edit' href='titleedit.php?op=edit&id=$id'><i class='write icon'></i></a>|<a href='titleedit.php?op=delete&id=$id' onClick='return confirm(\"$delconfirm\");' data-tooltip='$del'><i class='trash icon'></i></a>]</td>");
                addnav('', "titleedit.php?op=edit&id=$id");
                addnav('', "titleedit.php?op=delete&id=$id");
                rawoutput('<td>');
                output_notl('`&%s`0', $row['dk']);
                rawoutput('</td><td>');
                // reftag is currently unused
                // output("`^%s`0", $row['ref']);
                // output("</td><td>");
                output_notl('`2%s`0', $row['male']);
                rawoutput('</td><td>');
                output_notl('`6%s`0', $row['female']);
                rawoutput('</td></tr>');
                $i++;
            }
            rawoutput('</table>');
            //modulehook("titleedit", array());
            addnav('Functions');
            addnav('Add a Title', 'titleedit.php?op=add');
            addnav('Refresh List', 'titleedit.php');
            addnav('Reset Users Titles', 'titleedit.php?op=reset');
            title_help();
        break;
}

function title_help()
{
    output('`#You can have multiple titles for a given dragon kill rank.');
    output('If you do, one of those titles will be chosen at random to give to the player when a title is assigned.`n`n');
    output('You can have gaps in the title order.');
    output('If you have a gap, the title given will be for the DK rank less than or equal to the players current number of DKs.`n');
}

page_footer();
