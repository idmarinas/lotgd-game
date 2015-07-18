<?php
	$skills = db_prefix("skills");
	$skillsbuffs = db_prefix("skillsbuffs");

	// SQLs for creation of skills-table

	$skills_table = array(
		'skillid'=>array( 'name'=>'skillid', 'type'=>'int(10) unsigned', 'null'=>'1', 'extra'=>'auto_increment' ),
		'name'=>array( 'name'=>'name', 'type'=>'varchar(50)', 'null'=>'1' ),
		'ccode'=>array( 'name'=>'ccode', 'type'=>'varchar(5)', 'null'=>'1' ),
		'globals'=>array( 'name'=>'globals', 'type'=>'text', 'null'=>'1' ),
		'requirement'=>array( 'name'=>'requirement', 'type'=>'text', 'null'=>'1' ),
		'cooldown'=>array( 'name'=>'cooldown', 'type'=>'int(10) unsigned', 'null'=>'1' ),
		'execvalue'=>array( 'name'=>'execvalue', 'type'=>'text', 'null'=>'1' ),
		'buffids'=>array( 'name'=>'buffids', 'type'=>'varchar(50)', 'null'=>'1' ),
		'key-PRIMARY'=>array( 'name'=>'PRIMARY', 'type'=>'primary key', 'unique'=>'1', 'columns'=>'skillid' ) );

	$skillsbuffs_table = array(
		'buffid'=> array('name'=>'buffid', 'type'=>'int(10) unsigned', 'null'=>'1', 'extra'=>'auto_increment'),
		'buffname'=> array('name'=>'buffname', 'type'=>'varchar(255)', 'null'=>'1'),
		'buffshortname'=> array('name'=>'buffshortname', 'type'=>'varchar(50)', 'null'=>'1'),
		'rounds'=> array('name'=>'rounds', 'type'=>'varchar(255)', 'null'=>'1'),
		'invulnerable'=> array('name'=>'invulnerable', 'type'=>'varchar(255)', 'null'=>'1'),
		'dmgmod'=> array('name'=>'dmgmod', 'type'=>'varchar(255)', 'null'=>'1'),
		'badguydmgmod'=> array('name'=>'badguydmgmod', 'type'=>'varchar(255)', 'null'=>'1'),
		'atkmod'=> array('name'=>'atkmod', 'type'=>'varchar(255)', 'null'=>'1'),
		'badguyatkmod'=> array('name'=>'badguyatkmod', 'type'=>'varchar(255)', 'null'=>'1'),
		'defmod'=> array('name'=>'defmod', 'type'=>'varchar(255)', 'null'=>'1'),
		'badguydefmod'=> array('name'=>'badguydefmod', 'type'=>'varchar(255)', 'null'=>'1'),
		'lifetap'=> array('name'=>'lifetap', 'type'=>'varchar(255)', 'null'=>'1'),
		'damageshield'=> array('name'=>'damageshield', 'type'=>'varchar(255)', 'null'=>'1'),
		'regen'=> array('name'=>'regen', 'type'=>'varchar(255)', 'null'=>'1'),
		'minioncount'=> array('name'=>'minioncount', 'type'=>'varchar(255)', 'null'=>'1'),
		'maxbadguydamage'=> array('name'=>'maxbadguydamage', 'type'=>'varchar(255)', 'null'=>'1'),
		'minbadguydamage'=> array('name'=>'minbadguydamage', 'type'=>'varchar(255)', 'null'=>'1'),
		'maxgoodguydamage'=> array('name'=>'maxgoodguydamage', 'type'=>'varchar(255)', 'null'=>'1'),
		'mingoodguydamage'=> array('name'=>'mingoodguydamage', 'type'=>'varchar(255)', 'null'=>'1'),
		'startmsg'=> array('name'=>'startmsg', 'type'=>'varchar(255)', 'null'=>'1'),
		'roundmsg'=> array('name'=>'roundmsg', 'type'=>'varchar(255)', 'null'=>'1'),
		'wearoff'=> array('name'=>'wearoff', 'type'=>'varchar(255)', 'null'=>'1'),
		'effectfailmsg'=> array('name'=>'effectfailmsg', 'type'=>'varchar(255)', 'null'=>'1'),
		'effectnodmgmsg'=> array('name'=>'effectnodmgmsg', 'type'=>'varchar(255)', 'null'=>'1'),
		'effectmsg'=> array('name'=>'effectmsg', 'type'=>'varchar(255)', 'null'=>'1'),
		'allowinpvp'=> array('name'=>'allowinpvp', 'type'=>'varchar(255)', 'null'=>'1'),
		'allowintrain'=> array('name'=>'allowintrain', 'type'=>'varchar(255)', 'null'=>'1'),
		'survivenewday'=> array('name'=>'survivenewday', 'type'=>'varchar(255)', 'null'=>'1'),
		'expireafterfight'=> array('name'=>'expireafterfight', 'type'=>'varchar(255)', 'null'=>'1'),
		'key-PRIMARY' => array('name'=>'PRIMARY', 'type'=>'primary key', 'unique'=>'1', 'columns'=>'buffid'));


	require_once("lib/tabledescriptor.php");
	if (!db_table_exists($skills)) {
		synctable($skills, $skills_table, true);
		synctable($skillsbuffs, $skillsbuffs_table, true);
		$sql = "INSERT INTO ".$skills." (`skillid`, `name`, `ccode`, `requirement`, `cooldown`, `execvalue`, `buffids`) VALUES (2, 'Gil Toss', '`^', '\$session[''user''][''gold'']>100', 5, '\$session[''user''][''gold'']-=100;', 'a:1:{i:1;i:1;}'), (3, 'Torment', '`7', '\$session[''user''][''gravefights'']>1', 5, '\$session[''user''][''gravefights'']--;', 'a:1:{i:2;i:1;}');";
		db_query($sql);
		$sql = "INSERT INTO ".$skillsbuffs." (`buffid`, `buffname`, `buffshortname`, `rounds`, `invulnerable`, `dmgmod`, `badguydmgmod`, `atkmod`, `badguyatkmod`, `defmod`, `badguydefmod`, `lifetap`, `damageshield`, `regen`, `minioncount`, `maxbadguydamage`, `minbadguydamage`, `maxgoodguydamage`, `mingoodguydamage`, `startmsg`, `roundmsg`, `wearoff`, `effectfailmsg`, `effectnodmgmsg`, `effectmsg`, `allowinpvp`, `allowintrain`, `survivenewday`, `expireafterfight`) VALUES (1, 'Gil Toss', 'Gil Toss', '5', '0', '', '', '', '', '', '', '', '', '', '5', '5', '3', '', '', '', '%cYou throw money at {badguy}%c!`0', '%cYou decide to keep the rest of your money.`0', '', '', '%c{badguy}%c takes `\${damage}%c damage from your thrown coinage!`0', '0', '0', '0', '1'), (2, 'Torment', 'Torment', '10', '0', '', '.75', '', '.75', '', '.75', '', '', '', '', '', '', '', '', '%cYou make a dark deal with Ramius and trade `$1%c shades torment for the day in exchange for tormenting your enemy!`0', '%cYou torment {badguy}%c until he cries!`0', '%c{badguy}%c starts crying; You feel bad for being so mean to it. You big meanie >:(`0', '', '', '', '0', '0', '0', '1');";
		db_query($sql);
	}
	module_addhook("superuser");
	module_addhook("newday");	
	module_addhook("fightnav-specialties");
	module_addhook("apply-specialties");
?>