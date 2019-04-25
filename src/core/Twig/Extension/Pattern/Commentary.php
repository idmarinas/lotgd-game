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

trait Commentary
{
    protected $onlineStatus;

    /**
     * Default text domain for translator.
     *
     * @var string
     */
    protected $textDomain;

    /**
     * Display de commentary block.
     *
     * @param array  $commentary
     * @param string $textDomain
     * @param bool   $showPagination
     * @param bool   $canAddComment
     * @param int    $limit
     *
     * @return string
     */
    public function commentaryBlock(
        array $commentary,
        string $textDomain,
        ?string $paginationLinkUrl = null,
        bool $showPagination = true,
        bool $canAddComment = true,
        int $limit = 25
    ): string {
        global $session;

        $this->textDomain = $textDomain; //-- Default text domain for commentary block
        $session['user']['chatloc'] = $commentary['section'];

        $page = (int) \LotgdHttp::getQuery('commentPage', 1);

        $comments = $this->getCommentary()->getComments($commentary['section'], $page, $limit);

        $params = [
            'commentary' => $commentary,
            'textDomain' => $textDomain,
            'comments' => $comments,
            'showPagination' => $showPagination,
            'canAddComment' => $canAddComment,
            'paginationLinkUrl' => $paginationLinkUrl ?? \LotgdHttp::getServer('REQUEST_URI'),
            'formUrl' => $this->commentaryFormUrl(),
            'SU_EDIT_COMMENTS' => $session['user']['superuser'] & SU_EDIT_COMMENTS
        ];

        return \LotgdTheme::renderThemeTemplate('parts/commentary.twig', $params);
    }

    /**
     * Display a comment.
     *
     * @param array  $comment
     * @param string $textDomain
     * @param array  $commentary
     *
     * @return string
     */
    public function displayOneComment(array $comment, $textDomain, array $commentary): string
    {
        global $session;

        $params = [
            'comment' => $comment,
            'textDomain' => $textDomain,
            'commentary' => $commentary,
            'defaultTextDomainStatus' => $commentary['textDomainStatus'] ?? null,
            'returnLink' => \LotgdHttp::getServer('REQUEST_URI'),
            'SU_EDIT_COMMENTS' => $session['user']['superuser'] & SU_EDIT_COMMENTS
        ];

        return \LotgdTheme::renderThemeTemplate('parts/commentary/comment.twig', $params);
    }

    /**
     * Show if status of player (Online, Offline, Nearby).
     *
     * @param array  $comment
     * @param string $textDomain
     *
     * @return string
     */
    public function displayStatusOnlinePlayer(array $comment, ?string $textDomain = null): string
    {
        global $session;

        $textDomain = ($textDomain ?? $this->textDomain) ?: $this->textDomain;

        $logout = (int) getsetting('LOGINTIMEOUT', 900);
        $offline = new \DateTime('now');
        $offline->sub(new \DateInterval("PT{$logout}S"));

        $status = $this->getStatusMessages($textDomain);

        //-- Add basic status icons for online/offline/nearby/afk/dnd
        $icon = [ //-- Online in other chat
            // 'icon' => 'images/icons/onlinestatus/online.png',
            'outIcon' => ' olive circle outline pulse transition looping',
            'insideIcon' => 'green small circle',
            'label' => translate_inline('Online'),
        ];

        //-- Is a message of the game
        if ('GAME' == strtoupper($comment['command']))
        {
            return \sprintf('<span class="ui tooltip" data-content="%1$s"><i class="gamepad icon" aria-label="%1$s"></i></span>',
                $status['game']
            );
        }
        elseif ($session['user']['acctid'] == $comment['author'])
        {
            return \sprintf('<span class="ui tooltip" data-content="%1$s"><i class="user icon" aria-label="%1$s"></i></span>',
                $status['you']
            );
        }
        elseif ('AFK' == strtoupper($comment['chatloc']))
        {
            $icon = [
                // 'icon' => 'images/icons/onlinestatus/afk.png',
                'outIcon' => 'circle outline',
                'insideIcon' => 'grey small circle',
                'label' => $status['afk'],
            ];
        }
        elseif ('DNI' == strtoupper($comment['chatloc']))
        {
            $icon = [
                'icon' => 'images/icons/onlinestatus/dni.png',
                'icon' => 'circle outline',
                'insideIcon' => 'blue small circle',
                'label' => $status['dni'],
            ];
        }
        elseif ($comment['laston'] < $offline || ! $comment['loggedin'])
        {
            $icon = [
                // 'icon' => 'images/icons/onlinestatus/offline.png',
                'outIcon' => 'circle outline',
                'insideIcon' => 'red small circle',
                'label' => $status['offline'],
            ];
        }
        elseif ($comment['chatloc'] == $session['user']['chatloc'])
        { //-- Online and in same chat
            $icon = [
                // 'icon' => 'images/icons/onlinestatus/nearby.png',
                'outIcon' => ' olive circle outline tada transition looping',
                'insideIcon' => 'orange small circle',
                'label' => $status['nearby'],
            ];
        }

        return \sprintf('<span class="ui tooltip" data-content="%3$s"><i class="icons" aria-label="%3$s">
                <i class="%1$s icon" aria-hidden="true"></i>
                <i class="%2$s icon" aria-hidden="true"></i>
            </i></span>',
            $icon['outIcon'],
            $icon['insideIcon'],
            $icon['label']
        );
    }

    /**
     * Add form for add comment.
     *
     * @param array  $commentary Array with commentary data
     * @param string $textDomain Text domain for translator
     *
     * @return string
     */
    public function addComment(array $commentary, string $textDomain): string
    {
        global $output;

        $params = [
            'formUrl' => $this->commentaryFormUrl(),
            'textDomain' => $textDomain,
            'commentary' => $commentary,
            'colors' => $output->getColors(),
            'maxChars' => getsetting('maxchars', 200) + 100
        ];

        return \LotgdTheme::renderThemeTemplate('parts/commentary/add.twig', $params);
    }

    /**
     * Save comment to data base.
     */
    public function saveComment(): void
    {
        $moderate = \LotgdHttp::getPost('hideComment');

        if ($moderate)
        {
            $this->getCommentary()->moderateComments($moderate);

            return;
        }

        $data = \LotgdHttp::getPostAll();

        if (! $data || empty($data))
        {
            return;
        }

        $this->getCommentary()->saveComment($data);
    }

    /**
     * Get status messages of player.
     *
     * @param string $textDomain
     *
     * @return array
     */
    protected function getStatusMessages($textDomain): array
    {
        if (! $this->onlineStatus)
        {
            $this->onlineStatus = [
                'game' => $this->getTranslator()->trans('commentary.status.game', [], $textDomain),
                'you' => $this->getTranslator()->trans('commentary.status.you', [], $textDomain),
                'afk' => $this->getTranslator()->trans('commentary.status.afk', [], $textDomain),
                'nearby' => $this->getTranslator()->trans('commentary.status.nearby', [], $textDomain),
                'offline' => $this->getTranslator()->trans('commentary.status.offline', [], $textDomain),
                'dni' => $this->getTranslator()->trans('commentary.status.dni', [], $textDomain),
                'online' => $this->getTranslator()->trans('commentary.status.online', [], $textDomain),
            ];
        }

        return $this->onlineStatus;
    }

    /**
     * Get a valid url to use in commentary form.
     *
     * @return string
     */
    protected function commentaryFormUrl(): string
    {
        $url = \LotgdHttp::getServer('REQUEST_URI');

        //-- Sanitize link: Delete previous queries of: "page", "c", "commentPage" and "frombio"
        $url = preg_replace('/(?:[?&]c=[[:digit:]]+)|(?:[?&]page=[[:digit:]]+)|(?:[?&]commentPage=[[:digit:]]+)|(?:[?&]frombio=[[:alnum:]])/i', '', $url);
        $url = preg_replace('/op=fight/i', 'op=continue', $url);

        if (false === \strpos($url, '?') && false !== \strpos($url, '&'))
        {
            $url = \preg_replace('/[&]/', '?', 1);
        }

        return $url;
    }
}
