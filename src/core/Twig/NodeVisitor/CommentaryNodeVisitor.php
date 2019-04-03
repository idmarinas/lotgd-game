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
use Twig\Node\Expression\FunctionExpression;
use Twig\Node\Node;
use Twig\NodeVisitor\AbstractNodeVisitor;

class CommentaryNodeVisitor extends AbstractNodeVisitor
{
    const UNDEFINED = '_undefined';

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

        if ($node instanceof FunctionExpression && $node->getNode('node') instanceof ConstantExpression && 'commentary_block' === $node->getAttribute('name'))
        {
            $this->messages[] = [
                $node->getNode('node')->getAttribute('value'),
                $this->getReadFromArguments('limit', $node->getNode('arguments'), 1),
                $this->getReadFromArguments('showPagination', $node->getNode('arguments'), 1),
                $this->getReadFromArguments('paginationLinkUrl', $node->getNode('arguments'), 1),
                $this->getReadFromArguments('canAddComment', $node->getNode('arguments'), 1),
            ];
        }
        elseif ($node instanceof FunctionExpression && $node->getNode('node') instanceof ConstantExpression && 'display_status_online_player' === $node->getAttribute('name'))
        {
            $this->messages[] = [
                $node->getNode('node')->getAttribute('value'),
                $this->getReadFromArguments('textDomain', $node->getNode('arguments'), 1),
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

    private function getReadFromArguments(string $name, Node $arguments, int $index): ?string
    {
        if ($arguments->hasNode($name))
        {
            $argument = $arguments->getNode($name);
        }
        elseif ($arguments->hasNode($index))
        {
            $argument = $arguments->getNode($index);
        }
        else
        {
            return null;
        }

        return $this->getReadFromNode($argument);
    }

    private function getReadFromNode(Node $node): ?string
    {
        if ($node instanceof ConstantExpression)
        {
            return $node->getAttribute('value');
        }

        return self::UNDEFINED;
    }
}
