<?php

// translator ready
// addnews ready
// mail ready

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.settings')->saveSetting($settingname, $value)" instead. Removed in future version. */
function savesetting(string $settingname, $value)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.settings")->saveSetting($settingname, $value);" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.settings')->saveSetting($settingname, $value);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.settings')->clearSettings()" instead. Removed in future version. */
function clearsettings()
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.settings")->clearSettings();" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.settings')->clearSettings();
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.settings')->getSetting($settingname, $default)" instead. Removed in future version. */
function getsetting(string $settingname, $default = false)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.settings")->getSetting($settingname, $default);" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.settings')->getSetting($settingname, $default);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.settings')->getAllSettings()" instead. Removed in future version. */
function getAllSettings()
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.settings")->getAllSettings();" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.settings')->getAllSettings();
}
