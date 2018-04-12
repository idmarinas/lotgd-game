<?php
// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/showform.php';
require_once 'lib/http.php';
require_once 'lib/superusernav.php';

check_su_access(SU_EDIT_EQUIPMENT);

tlschema('armor');

page_header('Armor Editor');

$armorlevel = (int) httpget('level');
$op = httpget('op');
$id = (int) httpget('id');

//-- Data for template
$twig = [
    'armorlevel' => $armorlevel,
    'id' => $id,
    'op' => $op
];

//-- Prices for each armor level
$values = [1 => 48, 225 ,585, 990, 1575, 2250, 2790, 3420, 4230, 5040, 5850, 6840, 8010, 9000, 10350];

$armorarray=  [
	'Armor,title',
	'armorid' => 'Armor ID,hidden',
	'armorname' => 'Armor Name',
    'defense' => 'Defense,range,1,15,1'
];

superusernav();
addnav('Armor Editor');
addnav('Armor Editor Home', "armoreditor.php?level=$armorlevel");
addnav('Add armor', "armoreditor.php?op=add&level=$armorlevel");

if($op == 'edit' || $op == 'add')
{
    $result = ['defense' => 1];
    if ($id)
    {
        $select = DB::select('armor');
        $select->where->equalTo('armorid', $id);
        $result = DB::execute($select)->current();
    }

    $twig['form'] = lotgd_showform($armorarray, $result, true, false, false);
}
else if($op == 'del')
{
	$sql = "DELETE FROM " . DB::prefix("armor") . " WHERE armorid='$id'";
	DB::query($sql);
	//output($sql);
	$op = '';
    httpset('op', $op);
    $twig['op'] = $op;
}
else if($op == 'save')
{
	$armorid = httppost('armorid');
	$armorname = httppost('armorname');
    $defense = httppost('defense');

    if ($armorid)
    {
		$sql = "UPDATE " . DB::prefix("armor") . " SET armorname=\"$armorname\",defense=\"$defense\",value=".$values[$defense]." WHERE armorid='$armorid'";
    }
    else
    {
		$sql = "INSERT INTO " . DB::prefix("armor") . " (level,defense,armorname,value) VALUES ($armorlevel,\"$defense\",\"$armorname\",".$values[$defense].")";
    }

    DB::query($sql);

	$op = '';
	httpset('op', $op);
    $twig['op'] = $op;
}

if ($op == '')
{
    $select = DB::select('armor');
    $select->columns(['level' => DB::expression('MAX(`level`+1)')]);
    $result = DB::execute($select);
    $max = $result->current()['level'];

    for ($i = 0; $i <= $max; $i++) { addnav(['Armor for %s DK', $i], "armoreditor.php?level=$i"); }

    $select = DB::select('armor');
    $select->order('defense')
        ->where->equalTo('level', $armorlevel);

    $result = DB::execute($select);

    $twig['armors'] = $result;
}

rawoutput($lotgd_tpl->renderThemeTemplate('pages/armoreditor.twig', $twig));

page_footer();
