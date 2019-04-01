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

use Twig\Environment;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\FilterExpression;
use Twig\Node\Node;
use Twig\NodeVisitor\AbstractNodeVisitor;

/**
 * TranslateNodeVisitor extracts translation messages.
 */
class TranslatorNodeVisitor extends AbstractNodeVisitor
{
    const UNDEFINED_DOMAIN = '_undefined';

    private $enabled = false;
    private $messages = [];

    public function enable()
    {
        $this->enabled = true;
        $this->messages = [];
    }

    public function disable()
    {
        $this->enabled = false;
        $this->messages = [];
    }

    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    protected function doEnterNode(Node $node, Environment $env)
    {
        if (! $this->enabled)
        {
            return $node;
        }

        if ($node instanceof FilterExpression && $node->getNode('node') instanceof ConstantExpression && 't' === $node->getNode('filter')->getAttribute('value'))
        {
            // extract constant nodes with a trans filter
            $this->messages[] = [
                $node->getNode('node')->getAttribute('value'),
                $this->getReadDomainFromArguments($node->getNode('arguments'), 1),
            ];
        }
        elseif ($node instanceof Node)
        {
            // extract trans nodes
            $this->messages[] = [
                $node->getNode('body')->getAttribute('data'),
                $node->hasNode('domain') ? $this->getReadDomainFromNode($node->getNode('domain')) : null,
            ];
        }

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    protected function doLeaveNode(Node $node, Environment $env)
    {
        return $node;
    }

    private function getReadDomainFromArguments(Node $arguments, int $index): ?string
    {
        if ($arguments->hasNode('domain'))
        {
            $argument = $arguments->getNode('domain');
        }
        elseif ($arguments->hasNode($index))
        {
            $argument = $arguments->getNode($index);
        }
        else
        {
            return null;
        }

        return $this->getReadDomainFromNode($argument);
    }

    private function getReadDomainFromNode(Node $node): ?string
    {
        if ($node instanceof ConstantExpression)
        {
            return $node->getAttribute('value');
        }

        return self::UNDEFINED_DOMAIN;
    }
}
