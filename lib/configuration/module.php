<?php

$module = (string) \LotgdHttp::getQuery('module');
$save = (string) \LotgdHttp::getQuery('save');

$flashMessages = \LotgdTranslator::t('flash.message.module.fail.inject', [], $textDomain);

if (injectmodule($module, true))
{
    $flashMessages = '';

    if ('' != $save)
    {
        $moduleSettings = load_module_settings($module);
        $old = $moduleSettings;
        $post = \LotgdHttp::getPostAll();

        reset($post);

        foreach ($post as $key => $val)
        {
            $key = stripslashes($key);
            $val = stripslashes($val);
            set_module_setting($key, $val);

            if (! isset($old[$key]) || $old[$key] != $val)
            {
                $flashMessages .= \LotgdTranslator::t('flash.message.module.save.change', ['key' => $key, 'newValue' => $val, 'oldValue' => $old[$key]], $textDomain);
                // Notify modules
                $oldval = '';

                if (isset($old[$key]))
                {
                    $oldval = $old[$key];
                }
                gamelog("`@Changed module(`5{$module}`0) setting `^{$key}`0 from `#{$oldval}`0 to `&{$val}`0`0", 'settings');

                modulehook('changesetting', ['module' => $module, 'setting' => $key, 'old' => $oldval, 'new' => $val], true);
            }
        }

        $flashMessages .= \LotgdTranslator::t('flash.message.module.save.saved', [], $textDomain);
    }

    $params['moduleName'] = $module;
    $params['moduleInfo'] = get_module_info($module);
    $params['moduleSettings'] = false;

    if (count($params['moduleInfo']['settings']))
    {
        $params['moduleSettings'] = true;
        $moduleSettings = load_module_settings($module);
        $processSettings = [];

        foreach ($params['moduleInfo']['settings'] as $key => $val)
        {
            if (is_array($val))
            {
                $v = $val[0];
                $x = explode('|', $v);
                $val[0] = $x[0];
                $x[0] = $val;
            }
            else
            {
                $x = explode('|', $val);
            }
            $processSettings[$key] = $x[0];

            if (! isset($moduleSettings[$key]) && isset($x[1]))
            {
                $moduleSettings[$key] = $x[1];
            }
        }

        $params['isModuleActive'] = is_module_active($module);

        $params['form'] = lotgd_showform($processSettings, $moduleSettings, false, false, false);
    }
}

if ($flashMessages)
{
    \LotgdFlashMessages::addInfoMessage($flashMessages);
}
