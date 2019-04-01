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

use Lotgd\Core\Twig\Node\DefaultDomainNode as TranslatorDefaultDomainNode;
use Twig\Environment;
use Twig\Node\BlockNode;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\FilterExpression;
use Twig\Node\Expression\NameExpression;
use Twig\Node\ModuleNode;
use Twig\Node\Node;
use Twig\NodeVisitor\AbstractNodeVisitor;

class TranslatorDefaultDomainNodeVisitor extends AbstractNodeVisitor
{
    private $domain;

    public function __construct()
    {
        $this->domain = new Domain();
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return -10;
    }

    /**
     * {@inheritdoc}
     */
    protected function doEnterNode(Node $node, Environment $env)
    {
        if ($node instanceof BlockNode || $node instanceof ModuleNode)
        {
            $this->domain = $this->domain->enter();
        }

        if ($node instanceof TranslatorDefaultDomainNode
            && ($node->getNode('expr') instanceof ConstantExpression || $node->getNode('expr') instanceof NameExpression)
        ) {
            $this->domain->set('domain', $node->getNode('expr'));

            return $node;
        }

        if (! $this->domain->has('domain'))
        {
            return $node;
        }

        if ($node instanceof FilterExpression && \in_array($node->getNode('filter')->getAttribute('value'), ['t']))
        {
            $arguments = $node->getNode('arguments');

            if (! $arguments->hasNode('domain'))
            {
                $arguments->setNode('domain', $this->domain->get('domain'));
            }
        }
        elseif ($node instanceof TranslatorNode)
        {
            if (! $node->hasNode('domain'))
            {
                $node->setNode('domain', $this->domain->get('domain'));
            }
        }

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    protected function doLeaveNode(Node $node, Environment $env)
    {
        if ($node instanceof TranslatorDefaultDomainNode)
        {
            return false;
        }

        if ($node instanceof BlockNode || $node instanceof ModuleNode)
        {
            $this->domain = $this->domain->leave();
        }

        return $node;
    }
}
