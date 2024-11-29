<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see     https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author  IDMarinas
 *
 * @since   4.10.0
 */

namespace Lotgd\Core\Service;

use Jaxon\Jaxon as JaxonCore;
use function Jaxon\jaxon;

class Jaxon
{
	public function __construct (array $config)
	{
		jaxon()->config()->setOptions($config);

		//-- Register all class of Lotgd in dir "src/ajax/core"
		jaxon()->register(JaxonCore::CALLABLE_DIR, './src/ajax/core', ['namespace' => 'Lotgd\\Ajax\\Core\\']);

		//-- Register all custom class (Available globally) in dir "src/ajax/local"
		jaxon()->register(JaxonCore::CALLABLE_DIR, './src/ajax/local', ['namespace' => 'Lotgd\\Ajax\\Local\\']);
	}

	public function __invoke ()
	{
		return jaxon();
	}
}