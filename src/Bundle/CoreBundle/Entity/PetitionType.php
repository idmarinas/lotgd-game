<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Lotgd\Bundle\CoreBundle\Repository\PetitionTypeRepository;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Sonata\TranslationBundle\Traits\Gedmo\PersonalTranslatableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PetitionTypeRepository::class)
 * @Gedmo\TranslationEntity(class="Lotgd\Bundle\CoreBundle\Entity\PetitionTypeTranslation")
 */
class PetitionType implements TranslatableInterface
{
    use Common\IdTrait;
    use PersonalTranslatableTrait;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=150)
     *
     * @Assert\Length(
     *     min=3,
     *     max=150
     * )
     */
    private $name;

    /**
     * @Gedmo\Slug(fields={"name"}, updatable=false)
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="PetitionTypeTranslation", mappedBy="object", cascade={"all"})
     *
     * @var \Lotgd\Bundle\CoreBundle\Entity\PetitionTypeTranslation[]|\Doctrine\Common\Collections\Collection<int, \Lotgd\Bundle\CoreBundle\Entity\PetitionTypeTranslation>
     */
    private $translations;

    public function __toString(): string
    {
        return (string) ($this->getName() ?: $this->getSlug());
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
