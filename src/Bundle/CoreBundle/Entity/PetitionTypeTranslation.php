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
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;

/**
 * Petition types translations.
 *
 * @ORM\Table(
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="lookup_unique_idx", columns={"locale", "object_id", "field"})
 *     }
 * )
 * @ORM\Entity
 */
class PetitionTypeTranslation extends AbstractPersonalTranslation
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="PetitionType", inversedBy="translations", cascade={"all"})
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true,  options={"collation": "utf8mb4_unicode_ci"})
     */
    protected $content;

    public function __toString()
    {
        return (string) $this->getContent();
    }
}
