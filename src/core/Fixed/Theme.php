<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 3.0.0
 */

namespace Lotgd\Core\Fixed;

/**
 * @method static string render($name, array $context = [])
 * @method static void display($name, array $context = [])
 * @method static \Twig\TemplateWrapper load($name)
 */
class Theme
{
    use StaticTrait;
}

\class_alias('Lotgd\Core\Fixed\Theme', 'LotgdTheme', false);
