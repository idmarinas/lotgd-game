<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.7.0
 */

namespace Lotgd\Ajax\Core;

use Jaxon\Response\Response;
use Lotgd\Core\AjaxAbstract;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Tracy\Debugger;

/**
 * Actions for Cache.
 */
class Cache extends AjaxAbstract
{
    public const TEXT_DOMAIN = 'jaxon-cache';

    public function optimize($class): Response
    {
        return $this->processAction($class, 'optimize');
    }

    public function clearexpire($class): Response
    {
        return $this->processAction($class, 'clearExpired');
    }

    public function clearall($class): Response
    {
        return $this->processAction($class, 'flush');
    }

    public function clearbyprefix($class, $prefix): Response
    {
        return $this->processAction($class, 'clearbyprefix', $prefix);
    }

    protected function processAction($class, $action, $prefix = ''): Response
    {
        $response = new Response();

        if ('twigtemplates' == $class)
        {
            $finder = new Finder();
            $fs     = new Filesystem();
            $finder->in('storage/cache/template/')->ignoreDotFiles(true);

            try
            {
                $fs->remove($finder);
                $response->dialog->success(\LotgdTranslator::t('success', ['name' => 'Twig System'], self::TEXT_DOMAIN));
            }
            catch (\Throwable $th)
            {
                Debugger::log($th);

                $response->dialog->error(\LotgdTranslator::t('fail', ['name' => 'Twig System'], self::TEXT_DOMAIN));
            }

            return $response;
        }

        if (\LotgdLocator::has($class))
        {
            $cache = \LotgdLocator::get($class);

            try
            {
                $cache->{$action}($prefix);
                $response->dialog->success(\LotgdTranslator::t('success', ['name' => $class], self::TEXT_DOMAIN));
            }
            catch (\Throwable $th)
            {
                Debugger::log($th);
                $response->dialog->error(\LotgdTranslator::t('fail', ['name' => $class], self::TEXT_DOMAIN));
            }
        }
        else
        {
            $response->dialog->error(\LotgdTranslator::t('not.found.factory', ['name' => $class], self::TEXT_DOMAIN));
        }

        return $response;
    }
}
