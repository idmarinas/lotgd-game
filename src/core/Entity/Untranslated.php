<?php

namespace Lotgd\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Untranslated.
 *
 * @ORM\Table(name="untranslated",
 *      indexes={
 *          @ORM\Index(name="language", columns={"language"})
 *      }
 * )
 * @ORM\Entity
 */
class Untranslated
{
    /**
     * @var string
     *
     * @ORM\Column(name="intext", type="string", length=255, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $intext;

    /**
     * @var string
     *
     * @ORM\Column(name="language", type="string", length=5, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $language;

    /**
     * @var string
     *
     * @ORM\Column(name="namespace", type="string", length=255, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $namespace;

    /**
     * Set the value of Intext.
     *
     * @param string intext
     *
     * @return self
     */
    public function setIntext($intext)
    {
        $this->intext = $intext;

        return $this;
    }

    /**
     * Get the value of Intext.
     *
     * @return string
     */
    public function getIntext(): string
    {
        return $this->intext;
    }

    /**
     * Set the value of Language.
     *
     * @param string language
     *
     * @return self
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get the value of Language.
     *
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * Set the value of Namespace.
     *
     * @param string namespace
     *
     * @return self
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * Get the value of Namespace.
     *
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }
}
