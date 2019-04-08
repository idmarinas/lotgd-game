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

use Lotgd\Core\Twig\Node\CommentaryDefaultDomainStatusNode;
use Twig\Environment;
use Twig\Node\BlockNode;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\FunctionExpression;
use Twig\Node\Expression\NameExpression;
use Twig\Node\ModuleNode;
use Twig\Node\Node;

class CommentaryDefaultDomainStatusNodeVisitor extends NodeVisitorAbstract
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

        if ($node instanceof CommentaryDefaultDomainStatusNode
            && ($node->getNode('expr') instanceof ConstantExpression || $node->getNode('expr') instanceof NameExpression)
        ) {
            $this->scope->set('textDomain', $node->getNode('expr'));

            return $node;
        }

        if (! $this->scope->has('textDomain'))
        {
            return $node;
        }

        if ($node instanceof FunctionExpression && \in_array($node->getAttribute('name'), ['display_status_online_player']))
        {
            $arguments = $node->getNode('arguments');

            if (! $arguments->hasNode('textDomain'))
            {
                $arguments->setNode('textDomain', $this->scope->get('textDomain'));
            }
        }

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    protected function doLeaveNode(Node $node, Environment $env)
    {
        if ($node instanceof CommentaryDefaultDomainStatusNode)
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
