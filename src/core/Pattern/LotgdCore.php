<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Pattern;

trait LotgdCore
{
    use Censor;
    use Container;
    use EntityHydrator;
    use Output;
    use Repository;
    use Sanitize;
    use Translator;
}
