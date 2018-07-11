<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/http.php';
require_once 'lib/listfiles.php';
require_once 'lib/creaturefunctions.php';
require_once 'lib/superusernav.php';

check_su_access(SU_EDIT_CREATURES);

tlschema('creatures');

//this is a setup where all the creatures are generated.
$creaturestats = lotgd_generate_creature_levels();

page_header('Creature Editor');

superusernav();

$op = httpget('op');
$subop = httpget('subop');

$refresh = 0;
if (httppost('refresh'))
{
    httpset('op', 'add');
    $op = 'add';
    $subop = '';
    $refresh = 1; //let them know this is a refresh
    //had to do this as there is no onchange in a form...
}

if ('save' == $op)
{
    $forest = (int) (httppost('forest'));
    $grave = (int) (httppost('graveyard'));
    $id = httppost('creatureid');

    if (! $id)
    {
        $id = httpget('creatureid');
    }

    if ('' == $subop)
    {
        $post = httpallpost();
        $lev = (int) httppost('creaturelevel');

        if ($id)
        {
            $sql = '';

            foreach ($post as $key => $val)
            {
                if ('creature' == substr($key, 0, 8))
                {
                    $sql .= "$key = '$val', ";
                }
            }

            foreach ($creaturestats[$lev] as $key => $val)
            {
                if ('' != $post[$key])
                {
                    continue;
                }

                if ('creaturelevel' != $key && 'creature' == substr($key, 0, 8))
                {
                    $sql .= "$key = \"".addslashes($val).'", ';
                }
            }
            $sql .= " forest='$forest', ";
            $sql .= " graveyard='$grave', ";
            $sql .= " createdby='".$session['user']['login']."' ";
            $sql = 'UPDATE '.DB::prefix('creatures').' SET '.$sql." WHERE creatureid='$id'";
            $result = DB::query($sql) or output('`$'.DB::error()."`0`n`#$sql`0`n");
        }
        else
        {
            $cols = [];
            $vals = [];

            foreach ($post as $key => $val)
            {
                if ('creature' == substr($key, 0, 8))
                {
                    array_push($cols, $key);
                    array_push($vals, $val);
                }
            }
            array_push($cols, 'forest');
            array_push($vals, $forest);
            array_push($cols, 'graveyard');
            array_push($vals, $grave);
            reset($creaturestats[$lev]);

            foreach ($creaturestats[$lev] as $key => $val)
            {
                if (isset($post[$key]) && '' != $post[$key])
                {
                    continue;
                }

                if ('creaturelevel' != $key && 'creature' == substr($key, 0, 8))
                {
                    array_push($cols, $key);
                    array_push($vals, $val);
                }
            }
            $sql = 'INSERT INTO '.DB::prefix('creatures').' ('.join(',', $cols).',createdby) VALUES ("'.join('","', $vals).'","'.addslashes($session['user']['login']).'")';
            $result = DB::query($sql);
            $id = DB::insert_id();
        }

        if (DB::affected_rows())
        {
            output('`^Creature saved!`0`n');
        }
        else
        {
            output('`^Creature `$not`^ saved!`0`n');
        }
    }
    elseif ('module' == $subop)
    {
        // Save module settings
        $module = httpget('module');
        $post = httpallpost();
        reset($post);

        while (list($key, $val) = each($post))
        {
            set_module_objpref('creatures', $id, $key, $val, $module);
        }
        output('`^Saved!`0`n');
    }
    // Set the httpget id so that we can do the editor once we save
    httpset('creatureid', $id, true);
    // Set the httpget op so we drop back into the editor
    httpset('op', 'edit');
}

$op = httpget('op');
$id = httpget('creatureid');

if ('del' == $op)
{
    $sql = 'DELETE FROM '.DB::prefix('creatures')." WHERE creatureid = '$id'";
    DB::query($sql);

    if (DB::affected_rows() > 0)
    {
        output('Creature deleted`n`n');
        module_delete_objprefs('creatures', $id);
    }
    else
    {
        output('Creature not deleted: %s', DB::error());
    }
    $op = '';
    httpset('op', '');
}

if ('' == $op || 'search' == $op)
{
    $level = (int) httpget('level');

    if (! $level)
    {
        $level = 1;
    }
    $q = httppost('q');

    if ($q)
    {
        $where = "creaturename LIKE '%$q%' OR creaturecategory LIKE '%$q%' OR creatureweapon LIKE '%$q%' OR creaturelose LIKE '%$q%' OR createdby LIKE '%$q%'";
    }
    else
    {
        $where = "creaturelevel='$level'";
    }
    $sql = 'SELECT * FROM '.DB::prefix('creatures')." WHERE $where ORDER BY creaturelevel,creaturename";
    $result = DB::query($sql);
    // Search form
    $search = translate_inline('Search');
    rawoutput("<form action='creatures.php?op=search' method='POST'>");
    output('Search by field: ');
    rawoutput("<div class='ui action input'><input name='q' id='q'>");
    rawoutput("<button type='submit' class='ui button'>$search</button>");
    rawoutput('</div></form>');
    rawoutput("<script language='JavaScript'>document.getElementById('q').focus();</script>", true);
    addnav('', 'creatures.php?op=search');

    addnav('Levels');
    $sql1 = 'SELECT count(creatureid) AS n,creaturelevel FROM '.DB::prefix('creatures').' group by creaturelevel order by creaturelevel';
    $result1 = DB::query($sql1);

    while ($row = DB::fetch_assoc($result1))
    {
        addnav(['Level %s: (%s creatures)', $row['creaturelevel'], $row['n']],
                "creatures.php?level={$row['creaturelevel']}");
    }
    addnav('Edit');
    addnav('Add a creature', "creatures.php?op=add&level=$level");
    $opshead = translate_inline('Ops');
    $idhead = translate_inline('ID');
    $name = translate_inline('Name');
    $lev = translate_inline('Level');
    $weapon = translate_inline('Weapon');
    $winmsg = translate_inline('Win');
    $diemsg = translate_inline('Die');
    $cat = translate_inline('Category');
    $script = translate_inline('Script?');
    $author = translate_inline('Author');
    $edit = translate_inline('Edit');
    $yes = translate_inline('Yes');
    $no = translate_inline('No');
    $confirm = translate_inline('Are you sure you wish to delete this creature?');
    $del = translate_inline('Del');

    rawoutput("<table class='ui very compact striped selectable table'>");
    rawoutput('<thead><tr><th colspan="8" class="center aligned">Creatures level "'.$level.'"</th></tr>');
    rawoutput("<tr><th>$opshead</th><th>$name</th><th>$cat</th><th>$weapon</th><th>$script</th><th>$winmsg</th><th>$diemsg</th><th>$author</th></tr></thead>");
    addnav('', 'creatures.php');

    while ($row = DB::fetch_assoc($result))
    {
        rawoutput('<tr>');
        rawoutput("<td class='collapsing'>[ <a data-tooltip='$edit' href='creatures.php?op=edit&creatureid={$row['creatureid']}'><i class='write icon'></i></a>");
        rawoutput(" | <a data-tooltip='$del' href='creatures.php?op=del&creatureid={$row['creatureid']}&level={$row['creaturelevel']}' onClick='return confirm(\"$confirm\");'>");
        rawoutput("<i class='trash icon'></i></a> ]</td><td>");
        addnav('', "creatures.php?op=edit&creatureid={$row['creatureid']}");
        addnav('', "creatures.php?op=del&creatureid={$row['creatureid']}&level={$row['creaturelevel']}");
        output_notl('(%s) %s', $row['creatureid'], $row['creaturename']);
        rawoutput('</td><td>');
        output_notl('%s', $row['creaturecategory']);
        rawoutput('</td><td>');
        output_notl('%s', $row['creatureweapon']);
        rawoutput('</td><td>');

        if ('' != $row['creatureaiscript'])
        {
            output_notl($yes);
        }
        else
        {
            output_notl($no);
        }
        rawoutput('</td><td>');
        output_notl('%s', $row['creaturewin']);
        rawoutput('</td><td>');
        output_notl('%s', $row['creaturelose']);
        rawoutput('</td><td>');
        output_notl('%s', $row['createdby']);
        rawoutput('</td></tr>');
    }
    rawoutput('</table>');
}
else
{
    $level = (int) httpget('level');

    if (! $level)
    {
        $level = 1;
    }

    if ('edit' == $op || 'add' == $op)
    {
        require_once 'lib/showform.php';
        addnav('Edit');
        addnav('Creature properties', "creatures.php?op=edit&creatureid=$id");
        addnav('Add');
        addnav('Add Another Creature', "creatures.php?op=add&level=$level");
        module_editor_navs('prefs-creatures', "creatures.php?op=edit&subop=module&creatureid=$id&module=");

        if ('module' == $subop)
        {
            $module = httpget('module');
            rawoutput("<form action='creatures.php?op=save&subop=module&creatureid=$id&module=$module' method='POST'>");
            module_objpref_edit('creatures', $module, $id);
            rawoutput('</form>');
            addnav('', "creatures.php?op=save&subop=module&creatureid=$id&module=$module");
        }
        else
        {
            if ('edit' == $op && '' != $id)
            {
                $sql = 'SELECT * FROM '.DB::prefix('creatures')." WHERE creatureid=$id";
                $result = DB::query($sql);

                if (1 != DB::num_rows($result))
                {
                    output('`4Error`0, that creature was not found!');
                }
                else
                {
                    $row = DB::fetch_assoc($result);
                }
                $level = $row['creaturelevel'];
            }
            else
            {
                //check what was posted if this is a refresh, always fill in the base values
                if ($refresh)
                {
                    $level = (int) httppost('creaturelevel');
                }
                $row = $creaturestats[$level];
                $posted = ['level', 'category', 'weapon', 'name', 'win', 'lose', 'aiscript', 'id'];

                foreach ($posted as $field)
                {
                    $row['creature'.$field] = stripslashes(httppost('creature'.$field));
                }

                if (! $row['creatureid'])
                {
                    $row['creatureid'] = 0;
                }

                if ('' == $row['creaturelevel'])
                {
                    $row['creaturelevel'] = $level;
                }
                $row['forest'] = (int) httppost('forest');
                $row['graveyard'] = (int) httppost('graveyard');
            }
            //get available scripts
            //(uncached, won't hit there very often
            $sort = list_files('creatureai', []);
            sort($sort);
            $scriptenum = implode('', $sort);
            $scriptenum = ',,none'.$scriptenum;
            $form = [
                'Creature Properties,title',
                'creatureid' => 'Creature id,hidden',
                'creaturelevel' => 'Level,range,1,'.(getsetting('maxlevel', 15) + 4).',1',
                'Note: After changing the level causes please refresh the form to put the new preset stats for that level in,note',
                'creaturecategory' => 'Creature Category',
                'creaturename' => 'Creature Name',
                'creatureimage' => 'Creature image',
                'creaturedescription' => 'Creature description,textarea',
                'creaturehealth' => 'Creature Health',
                'creatureweapon' => 'Weapon',
                'creatureexp' => 'Creature Experience',
                'Note: Health and Experience of the creature are base values and get modified according to the hook buffbadguy,note',
                'creatureattack' => 'Creature Attack',
                'creaturedefense' => 'Creature Defense',
                'Note: Both are base values and will be buffed up. Try to make the creature beatable for a 0 DK person too,note',
                'creaturegold' => 'Creature Gold carried',
                'Note: Gold will be more or less when fighting suicidally or slumbering,note',
                'creaturewin' => 'Win Message',
                'creaturelose' => 'Death Message',
                'forest' => 'Creature is in forest?,bool',
                'graveyard' => 'Creature is in graveyard?,bool',
                'creatureaiscript' => "Creature's A.I.,enum".$scriptenum,
            ];
            rawoutput("<form action='creatures.php?op=save' method='POST'>");
            lotgd_showform($form, $row);
            $refresh = translate_inline('Refresh');
            rawoutput("<input type='submit' class='ui button' name='refresh' value='$refresh'>");
            rawoutput('</form>');
            addnav('', 'creatures.php?op=save');

            if ('' != $row['creatureaiscript'])
            {
                $scriptfile = 'scripts/'.$row['creatureaiscript'].'.php';

                if (file_exists($scriptfile))
                {
                    output('Current Script File Content:`n`n');
                    output_notl(implode('`n', str_replace(['`n'], ['``n'], color_sanitize(file($scriptfile)))));
                }
            }
        }
    }
    else
    {
        $module = httpget('module');
        rawoutput("<form action='mounts.php?op=save&subop=module&creatureid=$id&module=$module' method='POST'>");
        module_objpref_edit('creatures', $module, $id);
        rawoutput('</form>');
        addnav('', "creatures.php?op=save&subop=module&creatureid=$id&module=$module");
    }
    addnav('Navigation');
    addnav('Return to the creature editor', "creatures.php?level=$level");
}
page_footer();
