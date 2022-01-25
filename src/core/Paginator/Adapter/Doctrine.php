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

namespace Lotgd\Core\Paginator\Adapter;

use Throwable;
use Doctrine\ORM\QueryBuilder;
use Laminas\Paginator\Adapter\AdapterInterface;
use Tracy\Debugger;

class Doctrine implements AdapterInterface
{
    /**
     * Use getScalarResult to get results.
     */
    public const RESULT_SCALAR = 1;

    /**
     * Use getArrayResult to get results.
     */
    public const RESULT_ARRAY = 2;

    /**
     * Hydrates an object graph. This is the default behavior.
     */
    public const HYDRATE_OBJECT = 3;

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

    /**
     * Result type to use.
     *
     * @var int
     */
    protected $resultType;

    public function __construct(QueryBuilder $query, int $resultType = self::RESULT_ARRAY)
    {
        $this->doctrine   = $query;
        $this->resultType = $resultType;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $qb = clone $this->doctrine;

        $qb
            ->setFirstResult($offset)
            ->setMaxResults($itemCountPerPage)
        ;

        if (self::RESULT_SCALAR == $this->resultType)
        {
            return $qb->getQuery()->getScalarResult();
        }
        elseif (self::HYDRATE_OBJECT == $this->resultType)
        {
            return $qb->getQuery()->getResult();
        }

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * Returns the total number of rows in the result set.
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
     */
    private function selectCount(): int
    {
        $qb = clone $this->doctrine;

        try
        {
            return $qb->select('COUNT(1) AS doctrinePaginatorCount')
                ->orderBy('doctrinePaginatorCount')
                ->setFirstResult(null)
                ->setMaxResults(null)
                ->getQuery()
                ->getSingleScalarResult()
            ;
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return 0;
        }
    }
}
