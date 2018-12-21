<?php

namespace Lotgd\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ModuleEventHooks.
 *
 * @ORM\Table(name="module_event_hooks",
 *      indexes={
 *          @ORM\Index(name="modulename", columns={"modulename"}),
 *          @ORM\Index(name="event_type", columns={"event_type"})
 *      }
 * )
 * @ORM\Entity
 */
class ModuleEventHooks
{
    /**
     * @var string
     *
     * @ORM\Column(name="event_type", type="string", length=20, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $eventType;

    /**
     * @var string
     *
     * @ORM\Column(name="modulename", type="string", length=50, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $modulename;

    /**
     * @var string
     *
     * @ORM\Column(name="event_chance", type="text", length=65535, nullable=false)
     */
    private $eventChance;

    /**
     * Set the value of Event Type.
     *
     * @param string eventType
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
     *
     * @return string
     */
    public function getEventType(): string
    {
        return $this->eventType;
    }

    /**
     * Set the value of Modulename.
     *
     * @param string modulename
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
     *
     * @return string
     */
    public function getModulename(): string
    {
        return $this->modulename;
    }

    /**
     * Set the value of Event Chance.
     *
     * @param string eventChance
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
     *
     * @return string
     */
    public function getEventChance(): string
    {
        return $this->eventChance;
    }
}
