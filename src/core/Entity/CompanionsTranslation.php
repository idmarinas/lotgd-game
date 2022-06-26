<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.2.0
 */

namespace Lotgd\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation;

/**
 * Companions translations.
 *
 * @ORM\Table(
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="lookup_unique_idx", columns={"locale", "object_id", "field"})
 *     }
 * )
 * @ORM\Entity
 */
class CompanionsTranslation extends AbstractPersonalTranslation
{
    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected ?int $id = null;

    /**
     * @var \Lotgd\Core\Entity\Companions|null
     *
     * @ORM\ManyToOne(targetEntity="Companions", inversedBy="translations", cascade={"all"})
     * @ORM\JoinColumn(name="object_id", referencedColumnName="companionid", onDelete="CASCADE")
     */
    protected ?Companions $object = null;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true,  options={"collation": "utf8mb4_unicode_ci"})
     */
    protected ?string $content = null;

    /**
     * Convenient constructor.
     *
     * @param string $locale
     * @param string $field
     * @param string $value
     */
    public function __construct($locale, $field, $value)
    {
        $this->setLocale($locale);
        $this->setField($field);
        $this->setContent($value);
    }

    public function __toString()
    {
        return $this->getContent();
    }
}
