<?php
	require_once("lib/tabledescriptor.php");
	if (!is_module_active('dwellings')){
		if ($session['user']['superuser']&~SU_DOESNT_GIVE_GROTTO) output_notl("`4Installing dwellings Module.`n");
	}else{
		if ($session['user']['superuser']&~SU_DOESNT_GIVE_GROTTO) output_notl("`4Updating dwellings Module.`n");
	}
	$dwellings = array(
		'dwid'=>array('name'=>'dwid', 'type'=>'int unsigned',	'extra'=>'not null auto_increment'),
		'ownerid'=>array('name'=>'ownerid', 'type'=>'int unsigned',	'default'=>'0', 'extra'=>'not null'),
		'name'=>array('name'=>'name', 'type'=>'varchar(255)', 'extra'=>'not null'),
		'description'=>array('name'=>'description', 'type'=>'text'),
		'location'=>array('name'=>'location', 'type'=>'varchar(255)', 'extra'=>'not null'),
		'windowpeer'=>array('name'=>'windowpeer', 'type'=>'text'),
		'gold'=>array('name'=>'gold', 'type'=>'int unsigned'),
		'gems'=>array('name'=>'gems', 'type'=>'int unsigned'),
		'status'=>array('name'=>'status', 'type'=>'tinyint', 'extra'=>'not null'),
		'type'=>array('name'=>'type', 'type'=>'varchar(50)', 'extra'=>'not null'),
		'goldvalue'=>array('name'=>'goldvalue', 'type'=>'int unsigned'),
		'gemvalue'=>array('name'=>'gemvalue', 'type'=>'int unsigned'),
		'storedinfo'=>array('name'=>'storedinfo', 'type'=>'mediumtext', 'default'=>''),
		'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'dwid'),
		'index-dwid'=>array('name'=>'dwid', 'type'=>'index', 'columns'=>'dwid'),
		'index-ownerid'=>array('name'=>'ownerid', 'type'=>'index', 'columns'=>'ownerid'),
		'index-type'=>array('name'=>'type', 'type'=>'index', 'columns'=>'type'));
	$dwellingtypes = array(
		'typeid'=>array('name'=>'typeid', 'type'=>'int unsigned',	'extra'=>'not null auto_increment'),
		'module'=>array('name'=>'module', 'type'=>'varchar(255)', 'extra'=>'not null'),
		'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'typeid'),
		'index-typeid'=>array('name'=>'typeid', 'type'=>'index', 'columns'=>'typeid'),
		'index-module'=>array('name'=>'module', 'type'=>'index', 'columns'=>'module'));
	$dwellingkeys = array(
		'keyid'=>array('name'=>'keyid', 'type'=>'int unsigned',	'extra'=>'not null auto_increment'),
		'keyowner'=>array('name'=>'keyowner', 'type'=>'int unsigned',	'default'=>'0', 'extra'=>'not null'),
		'dwid'=>array('name'=>'dwid', 'type'=>'int unsigned',	'extra'=>'not null'),
		'dwidowner'=>array('name'=>'dwidowner', 'type'=>'int unsigned',	'default'=>'0', 'extra'=>'not null'),
		'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'keyid'),
		'index-keyowner'=>array('name'=>'keyowner', 'type'=>'index', 'columns'=>'keyowner'),
		'index-dwid'=>array('name'=>'dwid', 'type'=>'index', 'columns'=>'dwid'),
		'index-dwidowner'=>array('name'=>'dwidowner', 'type'=>'index', 'columns'=>'dwidowner'));
	synctable(db_prefix('dwellings'), $dwellings, true);
	synctable(db_prefix('dwellingkeys'), $dwellingkeys, true);
	synctable(db_prefix('dwellingtypes'), $dwellingtypes, true);
	module_addhook("village");
	module_addhook("newday");
	module_addhook("footer-hof");
	module_addhook("delete_character");
	module_addhook("changesetting");
	module_addhook("player-login");
?>