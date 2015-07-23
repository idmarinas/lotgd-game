<?php
$magic_items = array(
	'id'=>array('name'=>'id', 'type'=>'int(11) unsigned',	'extra'=>'auto_increment'),
	'category'=>array('name'=>'category', 'type'=>'int(10) unsigned', 'default'=>'0'),
	'name'=>array('name'=>'name', 'type'=>'varchar(50)','default'=>'None'),
	'description'=>array('name'=>'description', 'type'=>'text'),
	'gold'=>array('name'=>'gold', 'default'=>'0', 'type'=>'int(11) unsigned'),
	'gems'=>array('name'=>'gems', 'default'=>'0', 'type'=>'int(11) unsigned'),
	'dk'=>array('name'=>'dk', 'type'=>'int(11)', 'default'=>'0'),
	'attack'=>array('name'=>'attack', 'type'=>'varchar(11)', 'default'=>'0'),
	'defense'=>array('name'=>'defense', 'type'=>'varchar(11)', 'default'=>'0'),
	'charm'=>array('name'=>'charm', 'type'=>'varchar(11)', 'default'=>'0'),
	'hitpoints'=>array('name'=>'hitpoints', 'type'=>'varchar(11)', 'default'=>'0'),
	'turns'=>array('name'=>'turns', 'type'=>'varchar(11)', 'default'=>'0'),
	'favor'=>array('name'=>'favor', 'type'=>'varchar(11)', 'default'=>'0'),
	'bigdesc'=>array('name'=>'bigdesc', 'type'=>'text'),
	'align'=>array('name'=>'align', 'type'=>'varchar(11)', 'default'=>'0'),
	'odor'=>array('name'=>'odor', 'type'=>'varchar(11)', 'default'=>'0'),
	'hunger'=>array('name'=>'hunger', 'type'=>'varchar(11)', 'default'=>'0'),
	'rare'=>array('name'=>'rare', 'type'=>'tinyint(3)', 'default'=>'0'),
	'rarenum'=>array('name'=>'rarenum', 'type'=>'int(11)', 'default'=>'0'),
	'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'id'),
	'index-category'=>array('name'=>'category', 'type'=>'index', 'columns'=>'category'),
);
require_once("lib/tabledescriptor.php");
synctable(db_prefix('magicitems'), $magic_items, true);

if( getsetting( 'usedatacache', false ) )
{
	invalidatedatacache( 'modules-mysticalshop-enter' );
	invalidatedatacache( 'modules-mysticalshop-viewgoods' );
	invalidatedatacache( 'module-mysticalshop-selectall-ordercat' );
	require_once( './modules/mysticalshop/libcoredup.php' );
	mysticalshop_massinvalidate( 'modules-mysticalshop-' );
}

module_addhook("superuser");
module_addhook("village");
module_addhook("dragonkill");
module_addhook("newday");
module_addhook("lodge");
module_addhook("pointsdesc");
module_addhook("charstats");
module_addhook("changesetting");
module_addhook("training-victory");
module_addhook("bioinfo");
module_addhook("validateprefs");
?>