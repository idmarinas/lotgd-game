<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Fixed;

use Laminas\Paginator\Paginator;
use Lotgd\Core\Navigation\Navigation as CoreNavigation;

class Navigation
{
    /**
     * Instance of Navigation.
     *
     * @var Lotgd\Core\Navigation\Navigation
     */
    protected static $instance;

    /**
     * Add support for magic static method calls.
     *
     * @param mixed $method
     * @param array $arguments
     *
     * @return mixed the returned value from the resolved method
     */
    public static function __callStatic($method, $arguments)
    {
        if (\method_exists(self::$instance, $method))
        {
            return self::$instance->{$method}(...$arguments);
        }

        $methods = \implode(', ', \get_class_methods(self::$instance));

        throw new \BadMethodCallException("Undefined method '{$method}'. The method name must be one of '{$methods}'");
    }

    /**
     * Set instance of Navigation.
     */
    public static function instance(CoreNavigation $instance)
    {
        self::$instance = $instance;
    }

    /**
     * Navigation menu used with Paginator.
     *
     * @param Laminas\Paginator\Paginator $paginator
     * @param bool|null                   $forcePages Force to show pages if only have 1 page
     */
    public static function pagination(Paginator $paginator, string $url, $forcePages = null)
    {
        $paginator = $paginator->getPages('all');

        if (1 >= $paginator->pageCount && ! $forcePages)
        {
            return;
        }

        $union = false === \strpos($url, '?') ? '?' : '&';
        self::$instance->addHeader('common.pagination.title');

        foreach ($paginator->pagesInRange as $page)
        {
            $minItem = (($page - 1) * $paginator->itemCountPerPage) + 1;
            $maxItem = \min($paginator->itemCountPerPage * $page, $paginator->totalItemCount);

            $text = ($page != $paginator->current ? 'common.pagination.page' : 'common.pagination.current');
            self::$instance->addNav($text, "{$url}{$union}page={$page}", [
                'params' => [
                    'page'  => $page,
                    'item'  => $minItem,
                    'total' => $maxItem,
                ],
            ]);
        }
    }
}

\class_alias('Lotgd\Core\Fixed\Navigation', 'LotgdNavigation', false);
