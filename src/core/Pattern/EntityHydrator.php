<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Pattern;

use Laminas\Hydrator\ClassMethodsHydrator;
use Lotgd\Core\Entity\EntityInterface;

@trigger_error(EntityHydrator::class . ' is deprecated, if possible use Dependency Injection.', E_USER_DEPRECATED);

/**
 * @deprecated 5.0.0 use Dependency Injection when you can, and LotgdKernel::get(ServiceName) when not can use Dependency Injection.
 */
trait EntityHydrator
{
    protected $hydrator;
    protected $entity;

    /**
     * Hydrate an object by populating getter/setter methods.
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
     */
    public function getHydrator(): ClassMethodsHydrator
    {
        if ( ! $this->hydrator)
        {
            $this->hydrator = new ClassMethodsHydrator();
        }

        return $this->hydrator;
    }
}
