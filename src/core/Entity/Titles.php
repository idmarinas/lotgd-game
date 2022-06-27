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

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Titles.
 *
 * @ORM\Table(name="titles",
 *     indexes={
 *         @ORM\Index(name="dk", columns={"dk"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="Lotgd\Core\Repository\TitlesRepository")
 * @Gedmo\TranslationEntity(class="Lotgd\Core\Entity\TitlesTranslation")
 */
class Titles implements Translatable
{
    /**
     *
     * @ORM\Column(name="titleid", type="integer", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $titleid = null;

    /**
     *
     * @ORM\Column(name="dk", type="integer", options={"unsigned"=true})
     */
    private ?int $dk = 0;

    /**
     *
     * @ORM\Column(name="ref", type="string", length=100)
     */
    private ?string $ref = null;

    /**
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="male", type="string", length=25)
     *
     * @Assert\Length(
     *     min=1,
     *     max=25,
     *     allowEmptyString=false
     * )
     */
    private ?string $male = null;

    /**
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="female", type="string", length=25)
     *
     * @Assert\Length(
     *     min=1,
     *     max=25,
     *     allowEmptyString=false
     * )
     */
    private ?string $female = null;

    /**
     * @var \Doctrine\Common\Collections\Collection<\Lotgd\Core\Entity\TitlesTranslation>
     *
     * @ORM\OneToMany(targetEntity="TitlesTranslation", mappedBy="object", cascade={"all"})
     */
    private Collection $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function __toString()
    {
        return (string) $this->getTitleid();
    }

    public function getTranslations()
    {
        return $this->translations;
    }

    public function addTranslation(TitlesTranslation $t): void
    {
        if ( ! $this->translations->contains($t))
        {
            $t->setObject($this);
            $this->translations->add($t);
        }
    }

    /**
     * Set the value of Titleid.
     *
     * @param int $titleid
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
     */
    public function getTitleid(): int
    {
        return $this->titleid;
    }

    /**
     * Set the value of Dk.
     *
     * @param int $dk
     *
     * @return self
     */
    public function setDk($dk)
    {
        $this->dk = (int) $dk;

        return $this;
    }

    /**
     * Get the value of Dk.
     */
    public function getDk(): int
    {
        return $this->dk;
    }

    /**
     * Set the value of Ref.
     *
     * @param string $ref
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
     */
    public function getRef(): string
    {
        return $this->ref;
    }

    /**
     * Set the value of Male.
     *
     * @param string $male
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
     */
    public function getMale(): string
    {
        return $this->male;
    }

    /**
     * Set the value of Female.
     *
     * @param string $female
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
     */
    public function getFemale(): string
    {
        return $this->female;
    }
}
