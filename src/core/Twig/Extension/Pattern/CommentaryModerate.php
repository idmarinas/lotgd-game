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

trait CommentaryModerate
{
    /**
     * Display the commentary moderation block.
     *
     * @param array  $commentary
     * @param string $textDomain
     * @param bool   $showPagination
     * @param bool   $canAddComment
     * @param int    $limit
     *
     * @return string
     */
    public function commentaryModerateBlock(array $commentary, string $textDomain): string
    {
        $paginator = $this->getCommentary()->getCommentsModerate();

        $comments = [];

        foreach ($paginator as $comment)
        {
            $comments[$comment['section']][] = $comment;
        }

        $params = [
            'commentary' => $commentary,
            'textDomain' => $textDomain,
            'comments' => $comments,
            'sections' => $this->getCommentary()->commentaryLocs(),
            'formUrl' => $this->commentaryFormUrl()
        ];

        return \LotgdTheme::renderThemeTemplate('parts/moderate/commentary.twig', $params);
    }
}
