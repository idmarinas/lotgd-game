<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Pattern;

use Lotgd\Core\Entity\EntityInterface;
use Zend\Hydrator\ClassMethods;

trait EntityHydrator
{
    protected $hydrator;
    protected $entity;

    /**
     * Hydrate an object by populating getter/setter methods.
     *
     * @param array $data
     *
     * @return object
     */
    public function hydrateEntity(array $data)
    {
        return $this->getHydrator()->hydrate($data, $this->entity);
    }

    /**
     * Extract values from an object with class methods.
     *
     * @param object $object
     *
     * @return array
     */
    public function extractEntity($object): array
    {
        return $this->getHydrator()->extract($object);
    }

    /**
     * Set Entity instance.
     *
     * @param EntityInterface $hydrator
     *
     * @return $this
     */
    public function setEntity(EntityInterface $entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get Hydrator instance.
     *
     * @return ClassMethods
     */
    public function getHydrator(): ClassMethods
    {
        if (! $this->hydrator)
        {
            $this->hydrator = new ClassMethods();
        }

        return $this->hydrator;
    }
}
