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

namespace Lotgd\Core\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Lotgd\Core\Tool\Format;
use Twig\TwigFilter;

class FormatExtension extends AbstractExtension
{
    public function __construct(Format $format)
    {
        $this->format = $format;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('colorize', [$this->format, 'colorize'], ['is_safe' => ['html']]),
            new TwigFilter('uncolorize', [$this->format, 'uncolorize']),
            new TwigFilter('message_formatter', [$this->format, 'messageFormatter']),
            new TwigFilter('numeral', [$this->format, 'numeral']),
            new TwigFilter('relative_date', [$this->format, 'relativedate']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'lotgd-core-format';
    }
}
