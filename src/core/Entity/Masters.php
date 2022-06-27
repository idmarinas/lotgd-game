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
 * Masters.
 *
 * @ORM\Table(name="masters")
 * @ORM\Entity(repositoryClass="Lotgd\Core\Repository\MastersRepository")
 * @Gedmo\TranslationEntity(class="Lotgd\Core\Entity\MastersTranslation")
 */
class Masters implements Translatable
{
    /**
     *
     * @ORM\Column(name="creatureid", type="integer", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $creatureid = null;

    /**
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="creaturename", type="string", length=50, nullable=true)
     *
     * @Assert\Length(
     *     min=1,
     *     max=50
     * )
     */
    private ?string $creaturename = null;

    /**
     *
     * @ORM\Column(name="creaturelevel", type="integer", nullable=true, options={"unsigned": true})
     *
     * @Assert\DivisibleBy(1)
     */
    private ?int $creaturelevel = 1;

    /**
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="creatureweapon", type="string", length=50, nullable=true)
     */
    private ?string $creatureweapon = null;

    /**
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="creaturelose", type="string", length=120, nullable=true)
     */
    private ?string $creaturelose = null;

    /**
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="creaturewin", type="string", length=120, nullable=true)
     */
    private ?string $creaturewin = null;

    /**
     * @var \Doctrine\Common\Collections\Collection<\Lotgd\Core\Entity\MastersTranslation>
     *
     * @ORM\OneToMany(targetEntity="MastersTranslation", mappedBy="object", cascade={"all"})
     */
    private Collection $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function getTranslations()
    {
        return $this->translations;
    }

    public function addTranslation(MastersTranslation $t): void
    {
        if ( ! $this->translations->contains($t))
        {
            $t->setObject($this);
            $this->translations->add($t);
        }
    }

    /**
     * Set the value of Creatureid.
     *
     * @param int $creatureid
     *
     * @return self
     */
    public function setCreatureid($creatureid)
    {
        $this->creatureid = $creatureid;

        return $this;
    }

    /**
     * Get the value of Creatureid.
     */
    public function getCreatureid(): ?int
    {
        return $this->creatureid;
    }

    /**
     * Set the value of Creaturename.
     *
     * @param string $creaturename
     *
     * @return self
     */
    public function setCreaturename($creaturename)
    {
        $this->creaturename = $creaturename;

        return $this;
    }

    /**
     * Get the value of Creaturename.
     */
    public function getCreaturename(): string
    {
        return $this->creaturename;
    }

    /**
     * Set the value of Creaturelevel.
     *
     * @param int $creaturelevel
     *
     * @return self
     */
    public function setCreaturelevel($creaturelevel)
    {
        $this->creaturelevel = (int) $creaturelevel;

        return $this;
    }

    /**
     * Get the value of Creaturelevel.
     */
    public function getCreaturelevel(): int
    {
        return $this->creaturelevel;
    }

    /**
     * Set the value of Creatureweapon.
     *
     * @param string $creatureweapon
     *
     * @return self
     */
    public function setCreatureweapon($creatureweapon)
    {
        $this->creatureweapon = $creatureweapon;

        return $this;
    }

    /**
     * Get the value of Creatureweapon.
     */
    public function getCreatureweapon(): string
    {
        return $this->creatureweapon;
    }

    /**
     * Set the value of Creaturelose.
     *
     * @param string $creaturelose
     *
     * @return self
     */
    public function setCreaturelose($creaturelose)
    {
        $this->creaturelose = $creaturelose;

        return $this;
    }

    /**
     * Get the value of Creaturelose.
     */
    public function getCreaturelose(): string
    {
        return $this->creaturelose;
    }

    /**
     * Set the value of Creaturewin.
     *
     * @param string $creaturewin
     *
     * @return self
     */
    public function setCreaturewin($creaturewin)
    {
        $this->creaturewin = $creaturewin;

        return $this;
    }

    /**
     * Get the value of Creaturewin.
     */
    public function getCreaturewin(): string
    {
        return $this->creaturewin;
    }
}
