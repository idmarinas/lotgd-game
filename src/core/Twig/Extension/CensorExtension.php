<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.4.0
 */

namespace Lotgd\Core\Twig\Extension;

use Lotgd\Core\Output\Censor;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CensorExtension extends AbstractExtension
{
    protected $censor;

    public function __construct(Censor $censor)
    {
        $this->censor = $censor;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('censor', [$this->censor, 'filter']),
        ];
    }
}
