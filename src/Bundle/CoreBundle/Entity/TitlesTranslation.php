<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.2.0
 */

namespace Lotgd\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation;

/**
 * Titles translations.
 *
 * @ORM\Table(
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="lookup_unique_idx", columns={"locale", "object_id", "field"})
 *     }
 * )
 * @ORM\Entity
 */
class TitlesTranslation extends AbstractPersonalTranslation
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
     * @ORM\ManyToOne(targetEntity="Titles", inversedBy="translations", cascade={"all"})
     * @ORM\JoinColumn(name="object_id", referencedColumnName="titleid", onDelete="CASCADE")
     */
    protected $object;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true,  options={"collation": "utf8mb4_unicode_ci"})
     */
    protected $content;

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
        return (string) $this->getContent();
    }
}
