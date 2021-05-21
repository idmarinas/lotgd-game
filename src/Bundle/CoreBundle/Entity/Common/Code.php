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

namespace Lotgd\Bundle\CoreBundle\Entity\Common;

use Bukashk0zzz\FilterBundle\Annotation\FilterAnnotation as Filter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait Code
{
    /**
     * Unique code (similar to slug, but code is manual created).
     *
     * @ORM\Column(type="string", length=255, unique=true)
     *
     * @Filter(filter="Lotgd\Bundle\CoreBundle\Filter\Slugify")
     *
     * @Assert\Length(
     *     min=1,
     *     max=255
     * )
     */
    protected $code = '';

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }
}
