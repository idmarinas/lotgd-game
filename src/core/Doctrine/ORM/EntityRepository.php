<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Doctrine\ORM;

use Doctrine\ORM\{
    EntityRepository as DoctrineEntityRepository,
    QueryBuilder
};
use Lotgd\Core\Paginator\Adapter\Doctrine as DoctrineAdapter;
use Zend\Paginator\Paginator;

class EntityRepository extends DoctrineEntityRepository
{
    /**
     * Get a pagination for a result.
     *
     * @param QueryBuilder $query
     * @param int          $page
     * @param int          $perPage
     *
     * @return Paginator
     */
    public function getPaginator(QueryBuilder $query, int $page = 1, int $perPage = 25): Paginator
    {
        $page = max(1, $page);

        $paginator = new Paginator(new DoctrineAdapter($query));
        //- Set current page
        $paginator->setCurrentPageNumber($page);
        //-- Max number of results per page
        $paginator->setItemCountPerPage($perPage);

        return $paginator;
    }
}
