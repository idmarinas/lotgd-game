<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\ExpressionLanguage;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class BattleProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions()
    {
        return [
            new ExpressionFunction(
                'get_module_pref',
                fn($name, $module, $user = null) => sprintf('get_module_pref(%s, %s, %s)', $name, $module, $user),
                function (array $variables, $name, $module, $user = null)
                {
                    $user = $user ?: ($variables['character']['acctid'] ?? null);

                    return get_module_pref($name, $module, $user);
                }
            ),
            new ExpressionFunction(
                'character_attr',
                fn($attr) => sprintf('$character[%s]', $attr),
                fn(array $variables, $attr) => $variables['character'][$attr] ?? null
            ),
        ];
    }
}
