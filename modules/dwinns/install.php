<?
	require_once("lib/tabledescriptor.php");
	if (!is_module_active('dwinns')){
		if ($session['user']['superuser']&~SU_DOESNT_GIVE_GROTTO)
			output_notl("`4Installing dwellings Module: dwinns.`n");
	}else{
		if ($session['user']['superuser']&~SU_DOESNT_GIVE_GROTTO)
			output_notl("`4Updating dwellings Module: dwinns.`n");
	}
	
	$dwinns = array(
		'dwid'=>array('name'=>'dwid', 'type'=>'int unsigned'),
		'rooms'=>array('name'=>'rooms', 'type'=>'int unsigned', 'default'=>'2'),
		'guests'=>array('name'=>'guests', 'type'=>'int unsigned', 'default'=>'0'),
		'stars'=>array('name'=>'stars', 'type'=>'int unsigned', 'default'=>'0'),
		'price'=>array('name'=>'price', 'type'=>'int unsigned'),
		'brewname'=>array('name'=>'brewname', 'type'=>'varchar(50)','null'=>'1'),
		'alerounds'=>array('name'=>'alerounds', 'type'=>'int unsigned', 'default'=>'0'),
		'aleattack'=>array('name'=>'aleattack', 'type'=>'int unsigned', 'default'=>'0'),
		'aledefense'=>array('name'=>'aledefense', 'type'=>'int unsigned', 'default'=>'0'),
		'drinkqual'=>array('name'=>'drinkqual', 'type'=>'int unsigned', 'default'=>'0'),
		'brewexp'=>array('name'=>'brewexp', 'type'=>'int unsigned', 'default'=>'0'),
		'meals'=>array('name'=>'meals', 'type'=>'int unsigned', 'default'=>'0'),
		'drinks'=>array('name'=>'drinks', 'type'=>'int unsigned', 'default'=>'0'),
		'villageadd'=>array('name'=>'villageadd', 'type'=>'int unsigned', 'default'=>'0'),
		'brewdays'=>array('name'=>'brewdays', 'type'=>'int unsigned', 'default'=>'0'),
		'closed'=>array('name'=>'closed', 'type'=>'int unsigned', 'default'=>'0'),
		'logrooms'=>array('name'=>'logrooms', 'type'=>'int unsigned', 'default'=>'0'),
		'logmeals'=>array('name'=>'logmeals', 'type'=>'int unsigned', 'default'=>'0'),
		'logdrinks'=>array('name'=>'logdrinks', 'type'=>'int unsigned', 'default'=>'0'),
		'statroomsprofit'=>array('name'=>'statroomsprofit', 'type'=>'int unsigned', 'default'=>'0'),
		'statmealsprofit'=>array('name'=>'statmealsprofit', 'type'=>'int unsigned', 'default'=>'0'),
		'statdrinksprofit'=>array('name'=>'statdrinksprofit', 'type'=>'int unsigned', 'default'=>'0'),
		'statrooms'=>array('name'=>'statrooms', 'type'=>'int unsigned', 'default'=>'0'),
		'statmeals'=>array('name'=>'statmeals', 'type'=>'int unsigned', 'default'=>'0'),
		'statdrinks'=>array('name'=>'statdrinks', 'type'=>'int unsigned', 'default'=>'0'),
		'statmealsbought'=>array('name'=>'statmealsbought', 'type'=>'int unsigned', 'default'=>'0'),
		'statticks'=>array('name'=>'statticks', 'type'=>'int unsigned', 'default'=>'0'),
		'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'dwid'),
		'index-dwid'=>array('name'=>'dwid', 'type'=>'index', 'columns'=>'dwid')
	);
	
	synctable(db_prefix('dwinns'), $dwinns, true);
	
	module_addhook("village");
	module_addhook("newday");
	module_addhook("newday-runonce");
	module_addhook_priority("dwellings",100);
	module_addhook("dwellings-inside");
	module_addhook("dwellings-manage");
	module_addhook("dwellings-list-type");
	module_addhook("dwellings-list-interact");
	module_addhook("dragonkill");
	module_addhook("dwellings-addsleepers");
	
	if (!is_module_active('dwinns')){
		$sql="SELECT module FROM ".db_prefix("dwellingtypes")." WHERE module='dwinns'";
		$res=db_query($sql);
		if(db_num_rows($res)==0){
			$sql = "INSERT INTO ".db_prefix("dwellingtypes")." (module) VALUES ('dwinns')";
			db_query($sql);
		}
	}
	$sql = "SELECT typeid FROM ".db_prefix("dwellingtypes")." WHERE module='dwinns'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	set_module_setting("typeid",$row['typeid'],"dwinns");
?>
