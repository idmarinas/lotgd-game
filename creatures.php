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

page_header('Creature Editor');

superusernav();

$op = httpget('op');
$subop = httpget('subop');
$page = (int) (httpget('page') ?: 1);
$creaturesperpage = 25;

if ('save' == $op)
{
    $id = (int) (httppost('creatureid') ?: httpget('creatureid'));

    if ('' == $subop)
    {
        $post = httpallpost();

        if ($id)
        {
            $post['createdby'] = $session['user']['login'];
            $update = DB::update('creatures');
            $update->set($post)
                ->where->equalTo('creatureid', $id)
            ;
            $result = DB::execute($update) or output('`$'. DB::error()."`0`n");
        }
        else
        {
            $post['createdby'] = $session['user']['login'];
            $insert = DB::insert('creatures');
            $insert->values($post);

            DB::execute($insert);

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
    $q = (string) httppost('q');

    $select = DB::select('creatures');
    $select->order('creaturename');

    if ($q)
    {
        $select->where->like('creaturename', "%$q%")
            ->or->like('creaturecategory', "%$q%")
            ->or->like('creatureweapon', "%$q%")
            ->or->like('creaturelose', "%$q%")
            ->or->like('createdby', "%$q%")
        ;
    }

    $paginator = DB::paginator($select, $page, $creaturesperpage);

    DB::pagination($paginator, 'creatures.php');

    // Search form
    $search = translate_inline('Search');
    rawoutput("<form action='creatures.php?op=search' method='POST'>");
    output('Search by field: ');
    rawoutput("<div class='ui action input'><input name='q' id='q'>");
    rawoutput("<button type='submit' class='ui button'>$search</button>");
    rawoutput('</div></form>');
    rawoutput("<script language='JavaScript'>document.getElementById('q').focus();</script>", true);
    addnav('', 'creatures.php?op=search');

    addnav('Edit');
    addnav('Add a creature', 'creatures.php?op=add');
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

    rawoutput("<table class='ui very compact striped selectable table'><thead>");
    rawoutput("<tr><th>$opshead</th><th>$name</th><th>$cat</th><th>$weapon</th><th>$script</th><th>$winmsg</th><th>$diemsg</th><th>$author</th></tr></thead>");
    addnav('', 'creatures.php');

    if ($paginator->getCurrentItemCount())
    {
        foreach ($paginator as $key => $row)
        {
            rawoutput('<tr>');
            rawoutput("<td class='collapsing'>[ <a data-tooltip='$edit' href='creatures.php?op=edit&creatureid={$row['creatureid']}'><i class='write icon'></i></a>");
            rawoutput(" | <a data-tooltip='$del' href='creatures.php?op=del&creatureid={$row['creatureid']}' onClick='return confirm(\"$confirm\");'>");
            rawoutput("<i class='trash icon'></i></a> ]</td><td>");
            addnav('', "creatures.php?op=edit&creatureid={$row['creatureid']}");
            addnav('', "creatures.php?op=del&creatureid={$row['creatureid']}");
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
    }
    else
    {
        rawoutput('<tr><td class="center aligned" colspan="8">');
        output('No creatures found');
        rawoutput('</td><Ttr>');
    }
    rawoutput('</table>');
}
elseif ('edit' == $op || 'add' == $op)
{
    require_once 'lib/showform.php';

    addnav('Edit');
    addnav('Creature properties', "creatures.php?op=edit&creatureid=$id");
    addnav('Add');
    addnav('Add Another Creature', "creatures.php?op=add");
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
        }
        else
        {
            $posted = ['category', 'weapon', 'name', 'win', 'lose', 'aiscript', 'id'];

            foreach ($posted as $field)
            {
                $row['creature'.$field] = stripslashes(httppost('creature'.$field));
            }

            if (! $row['creatureid'])
            {
                $row['creatureid'] = 0;
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
            'creaturename' => 'Creature Name',
            'creaturecategory' => 'Creature Category',
            'creatureimage' => 'Creature image',
            'creaturedescription' => 'Creature description,textarea',
            'creatureweapon' => 'Weapon',
            'creaturewin' => 'Win Message',
            'creaturelose' => 'Death Message',
            'forest' => 'Creature is in forest?,bool',
            'graveyard' => 'Creature is in graveyard?,bool',
            'creatureaiscript' => "Creature's A.I.,enum".$scriptenum,
        ];
        rawoutput("<form action='creatures.php?op=save' method='POST'>");
        lotgd_showform($form, $row);
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
    addnav('Navigation');
    addnav('Return to the creature editor', "creatures.php");
}
else
{
    $module = httpget('module');
    rawoutput("<form action='mounts.php?op=save&subop=module&creatureid=$id&module=$module' method='POST'>");
    module_objpref_edit('creatures', $module, $id);
    rawoutput('</form>');
    addnav('', "creatures.php?op=save&subop=module&creatureid=$id&module=$module");
    addnav('Navigation');
    addnav('Return to the creature editor', "creatures.php");
}
page_footer();
