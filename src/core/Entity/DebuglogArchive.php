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
 * DebuglogArchive.
 *
 * @ORM\Table(name="debuglog_archive",
 *     indexes={
 *         @ORM\Index(name="date", columns={"date"}),
 *         @ORM\Index(name="target", columns={"target"}),
 *         @ORM\Index(name="field", columns={"actor", "field"})
 *     }
 * )
 * @ORM\Entity
 */
class DebuglogArchive
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false, options={"default": "0000-00-00 00:00:00"})
     */
    private $date = '0000-00-00 00:00:00';

    /**
     * @var int
     *
     * @ORM\Column(name="actor", type="integer", nullable=true, options={"unsigned": true})
     */
    private $actor;

    /**
     * @var int
     *
     * @ORM\Column(name="target", type="integer", nullable=true, options={"unsigned": true})
     */
    private $target;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", length=65535, nullable=false)
     */
    private $message;

    /**
     * @var string
     *
     * @ORM\Column(name="field", type="string", length=20, nullable=false)
     */
    private $field;

    /**
     * @var float
     *
     * @ORM\Column(name="value", type="float", precision=9, scale=2, nullable=false, options={"default": "0.00"})
     */
    private $value = '0.00';

    /**
     * Set the value of Id.
     *
     * @param int $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of Id.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set the value of Date.
     *
     * @return self
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get the value of Date.
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * Set the value of Actor.
     *
     * @param int $actor
     *
     * @return self
     */
    public function setActor($actor)
    {
        $this->actor = $actor;

        return $this;
    }

    /**
     * Get the value of Actor.
     */
    public function getActor(): int
    {
        return $this->actor;
    }

    /**
     * Set the value of Target.
     *
     * @param int $target
     *
     * @return self
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get the value of Target.
     */
    public function getTarget(): int
    {
        return $this->target;
    }

    /**
     * Set the value of Message.
     *
     * @param string $message
     *
     * @return self
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get the value of Message.
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Set the value of Field.
     *
     * @param string $field
     *
     * @return self
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Get the value of Field.
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * Set the value of Value.
     *
     * @param float $value
     *
     * @return self
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get the value of Value.
     */
    public function getValue(): float
    {
        return $this->value;
    }
}
