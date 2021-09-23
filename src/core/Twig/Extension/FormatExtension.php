<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.2.0
 */

namespace Lotgd\Core\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Lotgd\Core\Output\Format;
use Twig\TwigFilter;

class FormatExtension extends AbstractExtension
{
    private $format;

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
