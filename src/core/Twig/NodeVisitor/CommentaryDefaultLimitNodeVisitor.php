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

use Lotgd\Core\Twig\Node\CommentaryDefaultLimitNode;
use Twig\Environment;
use Twig\Node\BlockNode;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\FunctionExpression;
use Twig\Node\Expression\NameExpression;
use Twig\Node\ModuleNode;
use Twig\Node\Node;

class CommentaryDefaultLimitNodeVisitor extends NodeVisitorAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function doEnterNode(Node $node, Environment $env)
    {
        if ($node instanceof BlockNode || $node instanceof ModuleNode)
        {
            $this->scope = $this->scope->enter();
        }

        if ($node instanceof CommentaryDefaultLimitNode
            && ($node->getNode('expr') instanceof ConstantExpression || $node->getNode('expr') instanceof NameExpression)
        ) {
            $this->scope->set('limit', $node->getNode('expr'));

            return $node;
        }

        if (! $this->scope->has('limit'))
        {
            return $node;
        }

        if ($node instanceof FunctionExpression && \in_array($node->getAttribute('name'), ['commentary_block']))
        {
            $arguments = $node->getNode('arguments');

            if (! $arguments->hasNode('limit'))
            {
                $arguments->setNode('limit', $this->scope->get('limit'));
            }
        }

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    protected function doLeaveNode(Node $node, Environment $env)
    {
        if ($node instanceof CommentaryDefaultLimitNode)
        {
            return false;
        }

        if ($node instanceof BlockNode || $node instanceof ModuleNode)
        {
            $this->scope = $this->scope->leave();
        }

        return $node;
    }
}
