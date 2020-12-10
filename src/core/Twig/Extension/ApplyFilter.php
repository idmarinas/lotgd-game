<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.8.0
 */

namespace Lotgd\Core\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Lotgd\Core\Template\Theme as Environment;

class ApplyFilter extends AbstractExtension
{
    public function getName()
    {
        return 'apply_filter';
    }

    public function getFilters()
    {
        return [
            new TwigFilter('apply_filter', [$this, 'applyFilter'], [
                'needs_environment' => true,
                'needs_context'     => true,
            ]),
        ];
    }

    /**
     * Create a template to render a filters.
     */
    public function applyFilter(Environment $env, $context = [], $value, $filters)
    {
        $name = 'apply_filter_'.\md5($filters);

        $template = \sprintf('{{ %s|%s }}', $name, $filters);
        $template = $env->loadTemplate($template);

        $context[$name] = $value;

        return $template->render($context);
    }
}
