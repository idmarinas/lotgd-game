<?php

$module    = (string) \LotgdHttp::getQuery('module');
$save      = (string) \LotgdHttp::getQuery('save');
$isLaminas = (string) \LotgdHttp::getQuery('laminas');

$flashMessages = \LotgdTranslator::t('flash.message.module.fail.inject', [], $textDomain);

if (injectmodule($module, true))
{
    $flashMessages = '';

    if ('' != $save && ! $isLaminas)
    {
        $old  = load_module_settings($module);
        $post = \LotgdHttp::getPostAll();

        process_post_save_data($post, $old, $flashMessages, $module, $textDomain);

        $flashMessages .= \LotgdTranslator::t('flash.message.module.save.saved', [], $textDomain);
    }

    $params['moduleName']     = $module;
    $params['moduleInfo']     = get_module_info($module);
    $params['moduleSettings'] = false;

    if (isset($params['moduleInfo']['settings']) && ! empty($params['moduleInfo']['settings']))
    {
        $params['moduleSettings'] = true;
        $params['isLaminas']      = false;
        $params['isModuleActive'] = is_module_active($module);
        $moduleSettings           = load_module_settings($module);

        //-- Check if is a Laminas Form setting
        if (\is_string($params['moduleInfo']['settings']))
        {
            $params['isLaminas'] = true;

            $params['form'] = \LotgdLocator::get($params['moduleInfo']['settings']);
            $params['form']->setAttribute('action', "configuration.php?setting=module&module={$params['moduleName']}&save=save&laminas=true");
            $params['form']->setAttribute('method', 'POST');
            $params['form']->setAttribute('autocomplete', 'off');
            $params['form']->setAttribute('class', 'ui form');

            $params['formTypeTab'] = $params['form']->getOption('form_type_tab');

            if (\LotgdHttp::isPost())
            {
                $old = load_module_settings($module);
                $params['form']->setData(\LotgdHttp::getPostAll());

                if ($params['form']->isValid())
                {
                    $data = $params['form']->getData();

                    process_post_save_data($data, $old, $flashMessages, $module, $textDomain);

                    $flashMessages .= \LotgdTranslator::t('flash.message.module.save.saved', [], $textDomain);
                }
            }
            else
            {
                $params['form']->setData($moduleSettings);
            }
        }
        // Is old form (array)
        elseif (\is_array($params['moduleInfo']['settings']) && \count($params['moduleInfo']['settings']))
        {
            $processSettings = [];

            foreach ($params['moduleInfo']['settings'] as $key => $val)
            {
                if (\is_array($val))
                {
                    $v      = $val[0];
                    $x      = \explode('|', $v);
                    $val[0] = $x[0];
                    $x[0]   = $val;
                }
                else
                {
                    $x = \explode('|', $val);
                }
                $processSettings[$key] = $x[0];

                if ( ! isset($moduleSettings[$key]) && isset($x[1]))
                {
                    $moduleSettings[$key] = $x[1];
                }
            }

            $params['form'] = lotgd_showform($processSettings, $moduleSettings, false, false, false);
        }
    }
}

if ($flashMessages)
{
    \LotgdFlashMessages::addInfoMessage($flashMessages);
}

function process_post_save_data($post, $old, &$flashMessages, $module, $textDomain)
{
    \reset($post);

    foreach ($post as $key => $val)
    {
        //-- Compatibility with Laminas Form and fieldsets
        if (\is_array($val))
        {
            process_post_save_data($val, $old, $flashMessages, $module, $textDomain);

            continue;
        }

        $key = \stripslashes($key);
        $val = \stripslashes($val);
        set_module_setting($key, $val, $module);

        if ( ! isset($old[$key]) || $old[$key] != $val)
        {
            $flashMessages .= \LotgdTranslator::t('flash.message.module.save.change', ['key' => $key, 'newValue' => $val, 'oldValue' => $old[$key]], $textDomain);
            // Notify modules
            $oldval = '';

            if (isset($old[$key]))
            {
                $oldval = $old[$key];
            }
            gamelog("`@Changed module(`5{$module}`0) setting `^{$key}`0 from `#{$oldval}`0 to `&{$val}`0`0", 'settings');

            $args = ['module' => $module, 'setting' => $key, 'old' => $oldval, 'new' => $val];
            \LotgdHook::trigger(\Lotgd\Core\Hook::HOOK_CORE_SETTING_CHANGE, null, $args);
            modulehook('changesetting', $args, true);
        }
    }
}
