<?php

namespace Lotgd\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Titles.
 *
 * @ORM\Table(name="titles",
 *      indexes={
 *          @ORM\Index(name="dk", columns={"dk"})
 *      }
 * )
 * @ORM\Entity
 */
class Titles
{
    /**
     * @var int
     *
     * @ORM\Column(name="titleid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $titleid;

    /**
     * @var int
     *
     * @ORM\Column(name="dk", type="integer", nullable=false)
     */
    private $dk = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="ref", type="string", length=100, nullable=false)
     */
    private $ref;

    /**
     * @var string
     *
     * @ORM\Column(name="male", type="string", length=25, nullable=false)
     */
    private $male;

    /**
     * @var string
     *
     * @ORM\Column(name="female", type="string", length=25, nullable=false)
     */
    private $female;

    /**
     * Set the value of Titleid.
     *
     * @param int titleid
     *
     * @return self
     */
    public function setTitleid($titleid)
    {
        $this->titleid = $titleid;

        return $this;
    }

    /**
     * Get the value of Titleid.
     *
     * @return int
     */
    public function getTitleid(): int
    {
        return $this->titleid;
    }

    /**
     * Set the value of Dk.
     *
     * @param int dk
     *
     * @return self
     */
    public function setDk($dk)
    {
        $this->dk = $dk;

        return $this;
    }

    /**
     * Get the value of Dk.
     *
     * @return int
     */
    public function getDk(): int
    {
        return $this->dk;
    }

    /**
     * Set the value of Ref.
     *
     * @param string ref
     *
     * @return self
     */
    public function setRef($ref)
    {
        $this->ref = $ref;

        return $this;
    }

    /**
     * Get the value of Ref.
     *
     * @return string
     */
    public function getRef(): string
    {
        return $this->ref;
    }

    /**
     * Set the value of Male.
     *
     * @param string male
     *
     * @return self
     */
    public function setMale($male)
    {
        $this->male = $male;

        return $this;
    }

    /**
     * Get the value of Male.
     *
     * @return string
     */
    public function getMale(): string
    {
        return $this->male;
    }

    /**
     * Set the value of Female.
     *
     * @param string female
     *
     * @return self
     */
    public function setFemale($female)
    {
        $this->female = $female;

        return $this;
    }

    /**
     * Get the value of Female.
     *
     * @return string
     */
    public function getFemale(): string
    {
        return $this->female;
    }
}
