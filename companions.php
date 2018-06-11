<?php

// addnews ready
// mail ready
// translator ready

// hilarious copy of mounts.php
require_once 'common.php';
require_once 'lib/http.php';
require_once 'lib/showform.php';
require_once 'lib/superusernav.php';

check_su_access(SU_EDIT_MOUNTS);

tlschema('companions');

page_header('Companion Editor');

superusernav();

addnav('Companion Editor');
addnav('Add a companion', 'companions.php?op=add');

$op = httpget('op');
$id = httpget('id');

if ('deactivate' == $op)
{
    $update = DB::update('companions');
    $update->set(['companionactive' => 0])
        ->where->equalTo('companionid', $id)
    ;
    DB::execute($select);

    $op = '';
    httpset('op', '');
    invalidatedatacache("companionsdata-$id");
}
elseif ('activate' == $op)
{
    $update = DB::update('companions');
    $update->set(['companionactive' => 1])
        ->where->equalTo('companionid', $id)
    ;
    DB::execute($select);

    $op = '';
    httpset('op', '');
    invalidatedatacache("companiondata-$id");
}
elseif ('del' == $op)
{
    //drop the companion.
    $delete = DB::delete('companions');
    $delete->where->equalTo('companionid', $id);
    DB::execute($delete);

    module_delete_objprefs('companions', $id);
    $op = '';
    httpset('op', '');
    invalidatedatacache("companiondata-$id");
}
elseif ('take' == $op)
{
    $sql = 'SELECT * FROM '.DB::prefix('companions')." WHERE companionid='$id'";
    $result = DB::query($sql);
    $select = DB::select('companions');
    $select->where->equalTo('companionid', $id);
    $result = DB::execute($select);

    if ($row = $result->current())
    {
        require_once 'lib/buffs.php';

        $row['attack'] = $row['attack'] + $row['attackperlevel'] * $session['user']['level'];
        $row['defense'] = $row['defense'] + $row['defenseperlevel'] * $session['user']['level'];
        $row['maxhitpoints'] = $row['maxhitpoints'] + $row['maxhitpointsperlevel'] * $session['user']['level'];
        $row['hitpoints'] = $row['maxhitpoints'];
        $row = modulehook('alter-companion', $row);
        $row['abilities'] = @unserialize($row['abilities']);

        if (apply_companion($row['name'], $row))
        {
            output('`$Successfully taken `^%s`$ as companion.', $row['name']);
        }
        else
        {
            output('`$Companion not taken due to global limit.`0');
        }
    }
    $op = '';
    httpset('op', '');
}
elseif ('save' == $op)
{
    $subop = httpget('subop');

    if ('' == $subop)
    {
        $companion = httppost('companion');

        if ($companion)
        {

            $companion['abilities'] = serialize($companion['abilities']);

            if ($id > '')
            {
                $update = DB::update('companions');
                $update->set($companion)
                    ->where->equalTo('companionid', $id);

                DB::execute($update);
            }
            else
            {
                $insert = DB::insert('companions');
                $insert->values($companion);
                DB::execute($insert);
            }

            invalidatedatacache("companiondata-$id");

            if (DB::affected_rows())
            {
                output('`^Companion saved!`0`n`n');
            }
            else
            {
                // if (strlen($sql) > 400) $sql = substr($sql,0,200)." ... ".substr($sql,strlen($sql)-200);
                output('`^Companion `$not`^ saved: `$');
                rawoutput(DB::sqlString());
                output_notl('`0`n`n');
            }
        }
    }
    elseif ('module' == $subop)
    {
        // Save modules settings
        $module = httpget('module');
        $post = httpallpost();
        reset($post);

        foreach ($post as $key => $val)
        {
            set_module_objpref('companions', $id, $key, $val, $module);
        }
        output('`^Saved!`0`n');
    }

    if ($id)
    {
        $op = 'edit';
    }
    else
    {
        $op = '';
    }

    httpset('op', $op);
}

if ('' == $op)
{
    $select = DB::select('companions');
    $select->order('category, name');
    $result = DB::execute($select);

    $twig = [
        'companions' => $result
    ];

    rawoutput($lotgdTpl->renderThemeTemplate('pages/companions/default.twig', $twig));
}
elseif ('add' == $op)
{
    output('Add a companion:`n');
    addnav('Companion Editor Home', 'companions.php');
    companionform([]);
}
elseif ('edit' == $op)
{
    addnav('Companion Editor Home', 'companions.php');
    $sql = 'SELECT * FROM '.DB::prefix('companions')." WHERE companionid='$id'";
    $result = DB::query_cached($sql, "companiondata-$id", 3600);

    if (DB::num_rows($result) <= 0)
    {
        output('`iThis companion was not found.`i');
    }
    else
    {
        addnav('Companion properties', "companions.php?op=edit&id=$id");
        module_editor_navs('prefs-companions', "companions.php?op=edit&subop=module&id=$id&module=");
        $subop = httpget('subop');

        if ('module' == $subop)
        {
            $module = httpget('module');
            rawoutput("<form action='companions.php?op=save&subop=module&id=$id&module=$module' method='POST'>");
            module_objpref_edit('companions', $module, $id);
            rawoutput('</form>');
            addnav('', "companions.php?op=save&subop=module&id=$id&module=$module");
        }
        else
        {
            output('Companion Editor:`n');
            $row = DB::fetch_assoc($result);
            $row['abilities'] = @unserialize($row['abilities']);
            companionform($row);
        }
    }
}

/**
 * Create a companion form
 *
 * @param array $companion
 *
 * @return string
 */
function companionform($companion)
{
    global $lotgdTpl ;

    // Let's sanitize the data
    $companion['companionactive'] = isset($companion['companionactive']) ? $companion['companionactive'] : '';
    $companion['name'] = isset($companion['name']) ? htmlentities(stripcslashes($companion['name']), ENT_COMPAT, getsetting('charset', 'UTF-8')) : '';
    $companion['companionid'] = isset($companion['companionid']) ? $companion['companionid'] : '';
    $companion['description'] = isset($companion['description']) ? htmlentities(stripcslashes($companion['description']), ENT_COMPAT, getsetting('charset', 'UTF-8')) : '';
    $companion['dyingtext'] = isset($companion['dyingtext']) ? htmlentities(stripcslashes($companion['dyingtext']), ENT_COMPAT, getsetting('charset', 'UTF-8')) : '';
    $companion['jointext'] = isset($companion['jointext']) ? htmlentities(stripcslashes($companion['jointext']), ENT_COMPAT, getsetting('charset', 'UTF-8')) : '';
    $companion['category'] = isset($companion['category']) ? htmlentities(stripcslashes($companion['category']), ENT_COMPAT, getsetting('charset', 'UTF-8')) : '';
    $companion['companionlocation'] = isset($companion['companionlocation']) ? $companion['companionlocation'] : 'all';
    $companion['companioncostdks'] = isset($companion['companioncostdks']) ? ($companion['companioncostdks']) : 0;
    $companion['companioncostgems'] = isset($companion['companioncostgems']) ? $companion['companioncostgems'] : 0;
    $companion['companioncostgold'] = isset($companion['companioncostgold']) ? $companion['companioncostgold'] : 0;
    $companion['attack'] = isset($companion['attack']) ? $companion['attack'] : '';
    $companion['attackperlevel'] = isset($companion['attackperlevel']) ? $companion['attackperlevel'] : '';
    $companion['defense'] = isset($companion['defense']) ? $companion['defense'] : '';
    $companion['defenseperlevel'] = isset($companion['defenseperlevel']) ? $companion['defenseperlevel'] : '';
    $companion['hitpoints'] = isset($companion['hitpoints']) ? $companion['hitpoints'] : '';
    $companion['maxhitpoints'] = isset($companion['maxhitpoints']) ? $companion['maxhitpoints'] : '';
    $companion['maxhitpointsperlevel'] = isset($companion['maxhitpointsperlevel']) ? $companion['maxhitpointsperlevel'] : '';
    $companion['abilities']['fight'] = isset($companion['abilities']['fight']) ? $companion['abilities']['fight'] : 0;
    $companion['abilities']['defend'] = isset($companion['abilities']['defend']) ? $companion['abilities']['defend'] : 0;
    $companion['abilities']['heal'] = isset($companion['abilities']['heal']) ? $companion['abilities']['heal'] : 0;
    $companion['abilities']['magic'] = isset($companion['abilities']['magic']) ? $companion['abilities']['magic'] : 0;
    $companion['cannotdie'] = isset($companion['cannotdie']) ? $companion['cannotdie'] : 0;
    $companion['cannotbehealed'] = isset($companion['cannotbehealed']) ? $companion['cannotbehealed'] : 1;
    $companion['allowinshades'] = isset($companion['allowinshades']) ? $companion['allowinshades'] : 0;
    $companion['allowinpvp'] = isset($companion['allowinpvp']) ? $companion['allowinpvp'] : 0;
    $companion['allowintrain'] = isset($companion['allowintrain']) ? $companion['allowintrain'] : 0;

    // Run a modulehook to find out where camps are located.  By default
    // they are located in 'Degolburg' (ie, getgamesetting('villagename'));
    // Some later module can remove them however.
    $vname = getsetting('villagename', LOCATION_FIELDS);
    $locs = [$vname => sprintf_translate('The Village of %s', $vname)];
    $locs = modulehook('camplocs', $locs);
    $locs['all'] = translate_inline('Everywhere');
    ksort($locs);
    reset($locs);

    $twig = [
        'companion' => $companion,
        'locs' => $locs
    ];

    rawoutput($lotgdTpl->renderThemeTemplate('pages/companions/form.twig', $twig));
}

page_footer();
