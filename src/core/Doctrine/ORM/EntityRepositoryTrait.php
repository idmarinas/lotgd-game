<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\Doctrine\ORM;

use Doctrine\ORM\Query;
use Locale;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Translatable\TranslatableListener;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Paginator\Paginator;
use Lotgd\Core\Paginator\Adapter\Doctrine as DoctrineAdapter;

trait EntityRepositoryTrait
{
    protected $repositoryHydrator;

    /**
     * Get a pagination for a result.
     */
    public function getPaginator(QueryBuilder $query, int $page = 1, int $perPage = 25, int $resultType = DoctrineAdapter::RESULT_ARRAY): Paginator
    {
        $page    = \max(1, $page);
        $perPage = \max(1, $perPage); //-- Min items per page is 1

        $paginator = new Paginator(new DoctrineAdapter($query, $resultType));
        //- Set current page
        $paginator->setCurrentPageNumber($page);
        //-- Max number of results per page
        $paginator->setItemCountPerPage($perPage);

        return $paginator;
    }

    /**
     * Hydrate an object by populating getter/setter methods.
     *
     * @param object|null $entity
     *
     * @return object
     */
    public function hydrateEntity(array $data, $entity = null)
    {
        if ('object' != \gettype($entity))
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
     */
    public function extractEntity($object): array
    {
        if (\is_array($object))
        {
            $set = [];

            foreach ($object as $key => $value)
            {
                $set[$key] = $this->extractEntity($value);
            }

            return $set;
        }
        elseif ( ! \is_object($object))
        {
            return (array) $object;
        }

        return $this->getHydrator()->extract($object);
    }

    /**
     * Get a instance of query builder.
     */
    public function getQueryBuilder(): QueryBuilder
    {
        return $this->_em->createQueryBuilder();
    }

    /**
     * Create query for translate entity.
     *
     * @param string $dql Note: If pass a "Doctrine\ORM\QueryBuilder" auto-get a DQL string
     * @return \Doctrine\ORM\Query
     */
    public function createTranslatebleQuery(string $dql)
    {
        $query = $this->_em->createQuery($dql);

        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker');
        // take locale from session or request etc.
        $query->setHint(TranslatableListener::HINT_TRANSLATABLE_LOCALE, Locale::getDefault());
        // fallback to default values in case if record is not translated
        $query->setHint(TranslatableListener::HINT_FALLBACK, 1);

        return $query;
    }

    /**
     * Get Hydrator instance.
     */
    protected function getHydrator(): ClassMethodsHydrator
    {
        if ( ! $this->repositoryHydrator)
        {
            $this->repositoryHydrator = new ClassMethodsHydrator();
            //-- With this keyValue is keyValue. Otherwise it would be key_value
            $this->repositoryHydrator->removeNamingStrategy();
        }

        return $this->repositoryHydrator;
    }
}
