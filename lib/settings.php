<?php
// translator ready
// addnews ready
// mail ready

require_once 'lib/settings.class.php';

function savesetting($settingname,$value)
{
	global $settings;

	if (is_a($settings,"settings")) $settings->saveSetting($settingname,$value);
}

function loadsettings()
{
	global $settings;
	// as this seems to be a common complaint, examine the execution path
	// of this function, it will only load the settings once per page hit,
	// in subsequent calls to this function, $settings will be an array,
	// thus this function will do nothing.
	// slight change in 1.1.1 ... let's store a serialized array instead of a cached query
	// we need it too often and the for/while construct necessary is just too much for it.
	// if (!is_array($settings)){
	// 	$settings=datacache("game-settings");
	// 	if (!is_array($settings)){
	// 		$settings=array();
	// 		$sql = "SELECT * FROM " . db_prefix("settings");
	// 		$result = db_query($sql);//db_query_cached($sql,"game-settings");
	// 		while ($row = db_fetch_assoc($result)) {
	// 			$settings[$row['setting']] = $row['value'];
	// 		}
	// 		db_free_result($result);
	// 		updatedatacache("game-settings",$settings);
	// 	}
	// }
}

function clearsettings()
{
	//scraps the loadsettings() data to force it to reload.
	global $settings;

	if (is_a($settings,"settings")) $settings->clearSettings();
	unset($settings);
	$settings = new settings("settings");
}

function getsetting($settingname, $default = false)
{
	global $settings;

	if (!is_a($settings,"settings")) return $default;

	return $settings->getSetting($settingname,$default);
}
?>
