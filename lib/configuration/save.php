<?php

use Lotgd\Core\Event\Core;

$charRepository = \Doctrine::getRepository('LotgdCore:Avatar');

$flashMessages = '';

if (1 == (int) $postSettings['blockdupeemail'] && 1 != (int) $postSettings['requirevalidemail'])
{
    $postSettings['requirevalidemail'] = 1;

    $flashMessages .= \LotgdTranslator::t('flash.message.default.save.requirevalidemail', [], $textDomain);
}

if (1 == (int) $postSettings['requirevalidemail'] && 1 != (int) $postSettings['requireemail'])
{
    $postSettings['requireemail'] = 1;

    $flashMessages .= \LotgdTranslator::t('flash.message.default.save.requireemail', [], $textDomain);
}

if ('' != $postSettings['defaultsuperuser'])
{
    $value = 0;

    foreach ($postSettings['defaultsuperuser'] as $k => $v)
    {
        if ($v)
        {
            $value += (int) $k;
        }
    }
    $postSettings['defaultsuperuser'] = $value;
}

//-- Moving players if change name of village
if ($postSettings['villagename'] && $postSettings['villagename'] != LotgdSetting::getSetting('villagename', LOCATION_FIELDS))
{
    \LotgdResponse::pageDebug('Updating village name -- moving players');

    //-- Moving from, to
    $charRepository->movingPlayersToLocation(LotgdSetting::getSetting('villagename', LOCATION_FIELDS), $postSettings['villagename']);

    if ($session['user']['location'] == LotgdSetting::getSetting('villagename', LOCATION_FIELDS))
    {
        $session['user']['location'] = $postSettings['villagename'];
    }
}

//-- Moving players if change name of Inn
if ($postSettings['innname'] && $postSettings['innname'] != LotgdSetting::getSetting('innname', LOCATION_INN))
{
    //-- Moving from, to
    $charRepository->movingPlayersToLocation(LotgdSetting::getSetting('innname', LOCATION_INN), $postSettings['innname']);

    if ($session['user']['location'] == LotgdSetting::getSetting('innname', LOCATION_INN))
    {
        $session['user']['location'] = $postSettings['innname'];
    }
}

$old     = $settings->getArray();
$current = $settings->getArray();

foreach ($postSettings as $key => $val)
{
    $val = \stripslashes($val);

    if ( ! isset($current[$key]) || ($val != $current[$key]))
    {
        if ( ! isset($old[$key]))
        {
            $old[$key] = '';
        }

        LotgdSetting::saveSetting($key, $val);

        $flashMessages .= \LotgdTranslator::t('flash.message.default.save.change.setting', ['key' => $key, 'oldValue' => $old[$key], 'newValue' => $val], $textDomain);

        \LotgdLog::game("`@Changed core setting `^{$key}`@ from `#{$old[$key]}`@ to `&{$val}`0", 'settings');

        // Notify every module
        $args = new Core(['module' => 'core', 'setting' => $key, 'old' => $old[$key], 'new' => $val]);
        \LotgdEventDispatcher::dispatch($args, Core::SETTING_CHANGE);
    }
}

$flashMessages .= \LotgdTranslator::t('flash.message.default.save.saved', [], $textDomain);

$op = '';
\LotgdRequest::setQuery($op, '');

\LotgdFlashMessages::addInfoMessage($flashMessages);
