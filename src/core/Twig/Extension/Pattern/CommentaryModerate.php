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

namespace Lotgd\Core\Twig\Extension\Pattern;

use Twig\Environment;

trait CommentaryModerate
{
    /**
     * Display the commentary moderation block.
     *
     * @param bool $showPagination
     * @param bool $canAddComment
     * @param int  $limit
     */
    public function commentaryModerateBlock(Environment $env, array $commentary, string $textDomain): string
    {
        $paginator = $this->commentary->getCommentsModerate();

        $comments = [];

        foreach ($paginator as $comment)
        {
            $comments[$comment['section']][] = $comment;
        }

        $params = [
            'commentary' => $commentary,
            'textDomain' => $textDomain,
            'comments'   => $comments,
            'sections'   => $this->commentary->commentaryLocs(),
            'formUrl'    => $this->commentaryFormUrl(),
        ];

        return $env->load('_blocks/_commentary.html.twig')->renderBlock('commentary_moderate', $params);
    }
}
