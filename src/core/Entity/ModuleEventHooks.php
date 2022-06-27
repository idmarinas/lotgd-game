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

namespace Lotgd\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ModuleEventHooks.
 *
 * @ORM\Table(name="module_event_hooks",
 *     indexes={
 *         @ORM\Index(name="modulename", columns={"modulename"}),
 *         @ORM\Index(name="event_type", columns={"event_type"})
 *     }
 * )
 * @ORM\Entity
 */
class ModuleEventHooks
{
    /**
     *
     * @ORM\Column(name="event_type", type="string", length=20)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private ?string $eventType = null;

    /**
     *
     * @ORM\Column(name="modulename", type="string", length=50)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private ?string $modulename = null;

    /**
     *
     * @ORM\Column(name="event_chance", type="text", length=65535)
     */
    private ?string $eventChance = null;

    /**
     * Set the value of Event Type.
     *
     * @param string $eventType
     *
     * @return self
     */
    public function setEventType($eventType)
    {
        $this->eventType = $eventType;

        return $this;
    }

    /**
     * Get the value of Event Type.
     */
    public function getEventType(): string
    {
        return $this->eventType;
    }

    /**
     * Set the value of Modulename.
     *
     * @param string $modulename
     *
     * @return self
     */
    public function setModulename($modulename)
    {
        $this->modulename = $modulename;

        return $this;
    }

    /**
     * Get the value of Modulename.
     */
    public function getModulename(): string
    {
        return $this->modulename;
    }

    /**
     * Set the value of Event Chance.
     *
     * @param string $eventChance
     *
     * @return self
     */
    public function setEventChance($eventChance)
    {
        $this->eventChance = $eventChance;

        return $this;
    }

    /**
     * Get the value of Event Chance.
     */
    public function getEventChance(): string
    {
        return $this->eventChance;
    }
}
