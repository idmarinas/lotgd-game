<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Twig\Extension;

use Lotgd\Core\Twig\Extension\Pattern\CommentaryModerate;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Output\Color;
use Lotgd\Core\Output\Commentary as CommentaryCore;
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
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Commentary extends AbstractExtension
{
    use Pattern\Commentary;
    use CommentaryModerate;

    protected $commentary;
    protected $translator;
    protected $request;
    protected $color;
    protected $commentaryNodeVisitor;

    /**
     * Text for status of online player.
     *
     * @var array
     */
    protected $onlineStatus;
    private $settings;

    public function __construct(
        CommentaryCore $commentary,
        TranslatorInterface $translator,
        Request $request,
        Color $color,
        Settings $settings
    ) {
        $this->commentary = $commentary;
        $this->translator = $translator;
        $this->request    = $request;
        $this->color      = $color;
        $this->settings   = $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('commentary_block', [$this, 'commentaryBlock'], ['needs_environment' => true]),
            new TwigFunction('commentary_moderate_block', [$this, 'commentaryModerateBlock'], ['needs_environment' => true]),
            new TwigFunction('display_one_comment', [$this, 'displayOneComment'], ['needs_environment' => true]),
            new TwigFunction('display_status_online_player', [$this, 'displayStatusOnlinePlayer']),
            new TwigFunction('add_comment', [$this, 'addComment'], ['needs_environment' => true]),
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
            /*
             * @param int
             * {% commentary_limit_comments 35 %}
             */
            new CommentaryDefaultLimitTokenParser(),
            /*
             * @param bool
             * {% commentary_show_pagination true %}
             */
            new CommentaryDefaultPaginationTokenParser(),
            /*
             * @param string
             * {% commentary_pagination_link_url 'foobar.php' %}
             */
            new CommentaryDefaultPaginationUrlTokenParser(),
            /*
             * @param bool
             * {% commentary_can_add_comments true %}
             */
            new CommentaryDefaultAddCommentTokenParser(),
            /*
             * @param string
             * {% commentary_domain_status 'foobar' %}
             */
            new CommentaryDefaultDomainStatusTokenParser(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'commentary';
    }
}
