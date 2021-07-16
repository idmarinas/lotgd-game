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

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage as ExpressionLanguageBase;

class Battle extends ExpressionLanguageBase
{
    public function __construct(CacheItemPoolInterface $cache = null, array $providers = [])
    {
        array_unshift($providers, new BattleProvider());

        parent::__construct($cache, $providers);
    }
}
