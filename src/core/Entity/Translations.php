<?php

namespace Lotgd\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Translations.
 *
 * @ORM\Table(name="translations",
 *     indexes={
 *         @ORM\Index(name="language", columns={"language", "uri"}),
 *         @ORM\Index(name="uri", columns={"uri"})
 *     }
 * )
 * @ORM\Entity
 */
class Translations
{
    /**
     * @var int
     *
     * @ORM\Column(name="tid", type="integer", nullable=false, options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $tid;

    /**
     * @var string
     *
     * @ORM\Column(name="language", type="string", length=10, nullable=false)
     */
    private $language;

    /**
     * @var string
     *
     * @ORM\Column(name="uri", type="string", length=255, nullable=false)
     */
    private $uri;

    /**
     * @var string
     *
     * @ORM\Column(name="intext", type="blob", length=65535, nullable=false)
     */
    private $intext;

    /**
     * @var string
     *
     * @ORM\Column(name="outtext", type="blob", length=65535, nullable=false)
     */
    private $outtext;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=50, nullable=false)
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="version", type="string", length=50, nullable=false)
     */
    private $version;

    /**
     * Set the value of Tid.
     *
     * @param int tid
     *
     * @return self
     */
    public function setTid($tid)
    {
        $this->tid = $tid;

        return $this;
    }

    /**
     * Get the value of Tid.
     *
     * @return int
     */
    public function getTid(): int
    {
        return $this->tid;
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
     * Set the value of Uri.
     *
     * @param string uri
     *
     * @return self
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Get the value of Uri.
     *
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

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
     * Set the value of Outtext.
     *
     * @param string outtext
     *
     * @return self
     */
    public function setOuttext($outtext)
    {
        $this->outtext = $outtext;

        return $this;
    }

    /**
     * Get the value of Outtext.
     *
     * @return string
     */
    public function getOuttext(): string
    {
        return $this->outtext;
    }

    /**
     * Set the value of Author.
     *
     * @param string author
     *
     * @return self
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get the value of Author.
     *
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * Set the value of Version.
     *
     * @param string version
     *
     * @return self
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get the value of Version.
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }
}
