<?php

if ('save' == $save || '' != $save)
{
    $charRepository = \Doctrine::getRepository(\Lotgd\Core\Entity\Characters::class);

    $postSettings = \LotgdHttp::getPostAll();

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
    if ($postSettings['villagename'] && $postSettings['villagename'] != getsetting('villagename', LOCATION_FIELDS))
    {
        debug('Updating village name -- moving players');

        //-- Moving from, to
        $charRepository->movingPlayersToLocation(getsetting('villagename', LOCATION_FIELDS), $postSettings['villagename']);

        if ($session['user']['location'] == getsetting('villagename', LOCATION_FIELDS))
        {
            $session['user']['location'] = $postSettings['villagename'];
        }
    }

    //-- Moving players if change name of Inn
    if ($postSettings['innname'] && $postSettings['innname'] != getsetting('innname', LOCATION_INN))
    {
        //-- Moving from, to
        $charRepository->movingPlayersToLocation(getsetting('innname', LOCATION_INN), $postSettings['innname']);

        if ($session['user']['location'] == getsetting('innname', LOCATION_INN))
        {
            $session['user']['location'] = $postSettings['innname'];
        }
    }

    $old = $settings->getArray();
    $current = $settings->getArray();

    foreach ($postSettings as $key => $val)
    {
        $val = stripslashes($val);

        if (! isset($current[$key]) || ($val != $current[$key]))
        {
            if (! isset($old[$key]))
            {
                $old[$key] = '';
            }

            savesetting($key, $val);

            $flashMessages .= \LotgdTranslator::t('flash.message.default.save.change.setting', ['key' => $key, 'oldValue' => $old[$key], 'newValue' => $val], $textDomain);

            gamelog("`@Changed core setting `^$key`@ from `#{$old[$key]}`@ to `&$val`0", 'settings');

            // Notify every module
            modulehook('changesetting', ['module' => 'core', 'setting' => $key, 'old' => $old[$key], 'new' => $val], true);
        }
    }

    $flashMessages .= \LotgdTranslator::t('flash.message.default.save.saved', [], $textDomain);

    $op = '';
    \LotgdHttp::setQuery($op, '');

    \LotgdFlashMessages::addInfoMessage($flashMessages);
}
