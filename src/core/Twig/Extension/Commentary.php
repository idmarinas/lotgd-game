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
use Lotgd\Core\Pattern\Container;
use Lotgd\Core\ServiceManager;
use Lotgd\Core\Translator\Translator;
use Lotgd\Core\Twig\NodeVisitor\{
    CommentaryDefaultAddCommentNodeVisitor,
    CommentaryDefaultDomainStatusNodeVisitor,
    CommentaryDefaultLimitNodeVisitor,
    CommentaryDefaultPaginationNodeVisitor,
    CommentaryDefaultPaginationUrlNodeVisitor,
    CommentaryNodeVisitor
};
use Lotgd\Core\Twig\TokenParser\{
    CommentaryDefaultAddCommentTokenParser,
    CommentaryDefaultDomainStatusTokenParser,
    CommentaryDefaultLimitTokenParser,
    CommentaryDefaultPaginationTokenParser,
    CommentaryDefaultPaginationUrlTokenParser
};
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Commentary extends AbstractExtension
{
    use Container;
    use Pattern\Commentary;

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
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager)
    {
        $this->setContainer($serviceManager);
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('commentary_block', [$this, 'commentaryBlock']),
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
            new CommentaryDefaultPaginationUrlNodeVisitor()
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
     *
     * @return CommentaryCore
     */
    public function getCommentary(): CommentaryCore
    {
        if (! $this->commentary instanceof CommentaryCore)
        {
            $this->commentary = $this->getContainer(CommentaryCore::class);
        }

        return $this->commentary;
    }

    /**
     * Get Translator instance.
     *
     * @return Translator
     */
    public function getTranslator(): Translator
    {
        if (! $this->translator instanceof Translator)
        {
            $this->translator = $this->getContainer(Translator::class);
        }

        return $this->translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'commentary';
    }
}
