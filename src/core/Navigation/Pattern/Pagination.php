<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.3.0
 */

namespace Lotgd\Core\Navigation\Pattern;

use Laminas\Paginator\Paginator;

trait Pagination
{
    /**
     * Navigation menu used with Paginator.
     *
     * @param bool|null                   $forcePages Force to show pages if only have 1 page
     */
    public function pagination(Paginator $paginator, string $url, $forcePages = null)
    {
        $paginator = $paginator->getPages('all');

        if (1 >= $paginator->pageCount && ! $forcePages)
        {
            return;
        }

        $union = false === \strpos($url, '?') ? '?' : '&';
        $this->addHeader('common.pagination.title');

        foreach ($paginator->pagesInRange as $page)
        {
            $minItem = (($page - 1) * $paginator->itemCountPerPage) + 1;
            $maxItem = \min($paginator->itemCountPerPage * $page, $paginator->totalItemCount);

            $text = ($page != $paginator->current ? 'common.pagination.page' : 'common.pagination.current');
            $this->addNav($text, "{$url}{$union}page={$page}", [
                'params' => [
                    'page'  => $page,
                    'item'  => $minItem,
                    'total' => $maxItem,
                ],
            ]);
        }
    }
}
