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
use Zend\Hydrator\ClassMethods;
use Zend\Paginator\Paginator;

class EntityRepository extends DoctrineEntityRepository
{
    protected $repositoryHydrator;

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

    /**
     * Hydrate an object by populating getter/setter methods.
     *
     * @param array       $data
     * @param object|null $entity
     *
     * @return object
     */
    public function hydrateEntity(array $data, $entity = null)
    {
        if ('object' != gettype($entity))
        {
            $entity = $this->_entityName;
            $entity = new $entity();
        }

        return $this->getHydrator()->hydrate($data, $entity);
    }

    /**
     * Extract values from an object with class methods.
     *
     * @param object|array $object
     *
     * @return array
     */
    public function extractEntity($object): array
    {
        if (is_array($object))
        {
            $set = [];

            foreach ($object as $key => $value)
            {
                $set[$key] = $this->extractEntity($value);
            }

            return $set;
        }
        elseif ('object' != gettype($object))
        {
            return (array) $object;
        }

        return $this->getHydrator()->extract($object);
    }

    /**
     * Get Hydrator instance.
     *
     * @return ClassMethods
     */
    protected function getHydrator(): ClassMethods
    {
        if (! $this->repositoryHydrator)
        {
            $this->repositoryHydrator = new ClassMethods();
        }

        return $this->repositoryHydrator;
    }
}
