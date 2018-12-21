<?php

namespace Lotgd\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Nastywords.
 *
 * @ORM\Table(name="nastywords")
 * @ORM\Entity
 */
class Nastywords
{
    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=10, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="words", type="text", length=65535, nullable=true)
     */
    private $words;

    /**
     * Set the value of Type.
     *
     * @param string type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the value of Type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set the value of Words.
     *
     * @param string words
     *
     * @return self
     */
    public function setWords($words)
    {
        $this->words = $words;

        return $this;
    }

    /**
     * Get the value of Words.
     *
     * @return string
     */
    public function getWords(): string
    {
        return $this->words;
    }
}
