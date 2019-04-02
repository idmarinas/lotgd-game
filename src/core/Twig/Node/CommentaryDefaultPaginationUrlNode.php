<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Twig\Node;

use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Node;

class CommentaryDefaultPaginationUrlNode extends Node
{
    public function __construct(AbstractExpression $expr, int $lineno = 0, string $tag = null)
    {
        parent::__construct(['expr' => $expr], [], $lineno, $tag);
    }

    public function compile(Compiler $compiler)
    {
    }
}
