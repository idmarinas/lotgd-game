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

namespace Lotgd\Core\Twig\NodeVisitor;

use Twig\NodeVisitor\AbstractNodeVisitor;

abstract class NodeVisitorAbstract extends AbstractNodeVisitor
{
    private $scope;

    public function __construct()
    {
        $this->scope = new Scope();
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return -10;
    }
}
