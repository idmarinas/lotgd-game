<?php

$injected_modules = [1 => [], 0 => []];

function injectmodule($modulename, $force = false)
{
    global $mostrecentmodule, $injected_modules;

    //try to circumvent the array_key_exists() problem we've been having.
    $force = $force ? 1 : 0;

    //early escape if we already called injectmodule this hit with the
    //same args.
    if (isset($injected_modules[$force][$modulename]))
    {
        $mostrecentmodule = $modulename;

        return $injected_modules[$force][$modulename];
    }

    $modulename = \LotgdSanitize::moduleNameSanitize($modulename);
    $modulefilename = "modules/{$modulename}.php";

    if (file_exists($modulefilename))
    {
        $repository = \Doctrine::getRepository('LotgdCore:Modules');
        $row = $repository->find($modulename);

        if (! $force)
        {
            //our chance to abort if this module isn't currently installed
            //or doesn't meet the prerequisites.
            if (! $row)
            {
                \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('flash.message.module.uninstalled', [ 'module' => $modulename ], 'app-default'));

                $injected_modules[$force][$modulename] = false;

                return false;
            }

            if (! $row->getActive())
            {
                \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('flash.message.module.unactive', [ 'module' => $modulename ], 'app-default'));

                $injected_modules[$force][$modulename] = false;

                return false;
            }
        }

        require_once $modulefilename;

        $mostrecentmodule = $modulename;
        $info = '';

        if (! $force)
        {
            //avoid calling the function if we're forcing the module
            $fname = $modulename.'_getmoduleinfo';
            $info = $fname();

            $info['requires'] = $info['requires'] ?? [];
            $info['download'] = $info['download'] ?? '';
            $info['description'] = $info['description'] ?? '';

            if (! is_array($info['requires']))
            {
                $info['requires'] = [];
            }

            if (! module_check_requirements($info['requires']))
            {
                $injected_modules[$force][$modulename] = false;
                \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('flash.message.module.requisites', [ 'module' => $modulename ], 'app-default'));

                return false;
            }
        }

        //check to see if the module needs to be upgraded.
        if ($row)
        {
            $filemoddate = new \DateTime(date('Y-m-d H:i:s', filemtime($modulefilename)));

            if ($row->getFilemoddate() != $filemoddate || '' == $row->getInfokeys() || '|' != $row->getInfokeys()[0] || '' == $row->getVersion())
            {
                //the file mod time is still different from that
                //recorded in the database, time to update the database
                //and upgrade the module.
                debug("The module $modulename was found to have updated, upgrading the module now.");

                if (! is_array($info))
                {
                    //we might have gotten this info above, if not,
                    //we need it now.
                    $fname = "{$modulename}_getmoduleinfo";
                    $info = $fname();

                    $info['download'] = $info['download'] ?? '';
                    $info['version'] = $info['version'] ?? '0.0';
                    $info['description'] = $info['description'] ?? '';
                }

                $row = $repository->hydrateEntity($info, $row);
                $row->setFilemoddate($filemoddate);
                $row->setModuleauthor($info['author']);
                $row->setFormalname($info['name']);

                \Doctrine::persist($row);
                \Doctrine::flush();

                // Remove any old hooks (install will reset them)
                module_wipehooks($modulename);
                $fname = "{$modulename}_install";

                if (false === $fname())
                {
                    return false;
                }
                invalidatedatacache("injections-inject-$modulename");
            }
        }
        $injected_modules[$force][$modulename] = true;

        return true;
    }

    \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('flash.message.module.unfound', [ 'module' => $modulename ], 'app-default'));
    $injected_modules[$force][$modulename] = false;

    return false;
}
