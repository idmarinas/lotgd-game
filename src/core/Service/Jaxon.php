<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.10.0
 */

namespace Lotgd\Core\Service;

use Jaxon\App\Ajax;
use Jaxon\Jaxon as JaxonCore;
use function Jaxon\jaxon;

class Jaxon extends Ajax
{
    public function __construct(array $config)
    {
        $this->config()->setOptions($config);

        //-- Register all class of Lotgd in dir "src/ajax/core"
        $this->register(JaxonCore::CALLABLE_DIR, './src/ajax/core', ['namespace' => 'Lotgd\\Ajax\\Core\\']);

        //-- Register all custom class (Available globally) in dir "src/ajax/local"
        $this->register(JaxonCore::CALLABLE_DIR, './src/ajax/local', ['namespace' => 'Lotgd\\Ajax\\Local\\']);
    }
}
