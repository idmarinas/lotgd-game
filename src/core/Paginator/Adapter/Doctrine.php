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

namespace Lotgd\Core\Paginator\Adapter;

use Doctrine\ORM\QueryBuilder;
use Zend\Paginator\Adapter\AdapterInterface;

class Doctrine implements AdapterInterface
{
    /**
     * Doctrine instance of QueryBuilder.
     *
     * @var QueryBuilder
     */
    protected $doctrine;

    /**
     * Total item count.
     *
     * @var int
     */
    protected $rowCount;

    public function __construct(QueryBuilder $query)
    {
        $this->doctrine = $query;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $qb = clone $this->doctrine;

        return $qb
            ->setFirstResult($offset)
            ->setMaxResults($itemCountPerPage)
            ->getQuery()
            ->getArrayResult()
        ;
    }

    /**
     * Returns the total number of rows in the result set.
     *
     * @return int
     */
    public function count(): int
    {
        if (null !== $this->rowCount)
        {
            return $this->rowCount;
        }

        $this->rowCount = $this->selectCount();

        return $this->rowCount;
    }

    /**
     * Count total rows.
     *
     * @return int
     */
    private function selectCount(): int
    {
        $qb = clone $this->doctrine;

        return $qb->select('COUNT(1) AS C')
            ->setFirstResult(null)
            ->setMaxResults(null)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
