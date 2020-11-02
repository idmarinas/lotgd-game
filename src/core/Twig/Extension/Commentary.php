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

namespace Lotgd\Core\Twig\Extension;

use Lotgd\Core\Output\Commentary as CommentaryCore;
use Lotgd\Core\Pattern as PatternCore;
use Lotgd\Core\Twig\NodeVisitor\CommentaryDefaultAddCommentNodeVisitor;
use Lotgd\Core\Twig\NodeVisitor\CommentaryDefaultDomainStatusNodeVisitor;
use Lotgd\Core\Twig\NodeVisitor\CommentaryDefaultLimitNodeVisitor;
use Lotgd\Core\Twig\NodeVisitor\CommentaryDefaultPaginationNodeVisitor;
use Lotgd\Core\Twig\NodeVisitor\CommentaryDefaultPaginationUrlNodeVisitor;
use Lotgd\Core\Twig\NodeVisitor\CommentaryNodeVisitor;
use Lotgd\Core\Twig\TokenParser\CommentaryDefaultAddCommentTokenParser;
use Lotgd\Core\Twig\TokenParser\CommentaryDefaultDomainStatusTokenParser;
use Lotgd\Core\Twig\TokenParser\CommentaryDefaultLimitTokenParser;
use Lotgd\Core\Twig\TokenParser\CommentaryDefaultPaginationTokenParser;
use Lotgd\Core\Twig\TokenParser\CommentaryDefaultPaginationUrlTokenParser;
use Twig\TwigFunction;

class Commentary extends AbstractExtension
{
    use PatternCore\Container;
    use PatternCore\Translator;
    use PatternCore\Template;
    use Pattern\Commentary;
    use Pattern\CommentaryModerate;

    protected $commentary;
    protected $translator;
    protected $commentaryNodeVisitor;

    /**
     * Text for status of online player.
     *
     * @var array
     */
    protected $onlineStatus;

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('commentary_block', [$this, 'commentaryBlock']),
            new TwigFunction('commentary_moderate_block', [$this, 'commentaryModerateBlock']),
            new TwigFunction('display_one_comment', [$this, 'displayOneComment']),
            new TwigFunction('display_status_online_player', [$this, 'displayStatusOnlinePlayer']),
            new TwigFunction('add_comment', [$this, 'addComment']),
            new TwigFunction('save_comment', [$this, 'saveComment']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeVisitors()
    {
        return [
            $this->getCommentaryNodeVisitor(),
            new CommentaryDefaultAddCommentNodeVisitor(),
            new CommentaryDefaultDomainStatusNodeVisitor(),
            new CommentaryDefaultLimitNodeVisitor(),
            new CommentaryDefaultPaginationNodeVisitor(),
            new CommentaryDefaultPaginationUrlNodeVisitor(),
        ];
    }

    public function getCommentaryNodeVisitor()
    {
        return $this->commentaryNodeVisitor ?: $this->commentaryNodeVisitor = new CommentaryNodeVisitor();
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return [
            /**
             * @param int
             * {% commentary_limit_comments 35 %}
             */
            new CommentaryDefaultLimitTokenParser(),
            /**
             * @param bool
             * {% commentary_show_pagination true %}
             */
            new CommentaryDefaultPaginationTokenParser(),
            /**
             * @param string
             * {% commentary_pagination_link_url 'foobar.php' %}
             */
            new CommentaryDefaultPaginationUrlTokenParser(),
            /**
             * @param bool
             * {% commentary_can_add_comments true %}
             */
            new CommentaryDefaultAddCommentTokenParser(),
            /**
             * @param string
             * {% commentary_domain_status 'foobar' %}
             */
            new CommentaryDefaultDomainStatusTokenParser(),
        ];
    }

    /**
     * Get the Commentary instance.
     */
    public function getCommentary(): CommentaryCore
    {
        if ( ! $this->commentary instanceof CommentaryCore)
        {
            $this->commentary = $this->getContainer(CommentaryCore::class);
        }

        return $this->commentary;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'commentary';
    }

    /**
     * Template block for commentary.
     * Only load one time.
     */
    protected function getTemplateBlock()
    {
        if ( ! $this->templateCommentaryBlock)
        {
            $this->templateCommentaryBlock = $this->getTemplate()->load('{theme}/_blocks/_commentary.html.twig');
        }

        return $this->templateCommentaryBlock;
    }
}
