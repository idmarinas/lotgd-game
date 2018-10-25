<?php
// translator ready
// addnews ready
// mail ready

$settings = LotgdLocator::get(Lotgd\Core\Lib\Settings::class);

function savesetting(string $settingname, $value)
{
	global $settings;

	return $settings->saveSetting($settingname, $value);
}

function clearsettings()
{
	global $settings;

	return $settings->clearSettings();
}

function getsetting(string $settingname, $default = false)
{
	global $settings;

	return $settings->getSetting($settingname, $default);
}


function getAllSettings()
{
    global $settings;

	return $settings->getAllSettings();
}
