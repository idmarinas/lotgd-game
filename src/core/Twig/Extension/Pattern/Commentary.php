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

namespace Lotgd\Core\Twig\Extension\Pattern;

use DateTime;
use DateInterval;
use Twig\Environment;

trait Commentary
{
    protected $onlineStatus;
    protected $templateCommentaryBlock;

    /**
     * Default text domain for translator.
     *
     * @var string
     */
    protected $textDomain;

    /**
     * Display de commentary block.
     */
    public function commentaryBlock(
        Environment $env,
        array $commentary,
        string $textDomain,
        ?string $paginationLinkUrl = null,
        bool $showPagination = true,
        bool $canAddComment = true,
        int $limit = 25
    ): string {
        global $session;

        $this->textDomain           = $textDomain; //-- Default text domain for commentary block
        $session['user']['chatloc'] = $commentary['section'];

        $page = (int) $this->request->getQuery('commentPage', 1);

        $comments = $this->commentary->getComments($commentary['section'], $page, $limit);

        $params = [
            'commentary'        => $commentary,
            'textDomain'        => $textDomain,
            'comments'          => $comments,
            'showPagination'    => $showPagination,
            'canAddComment'     => $canAddComment,
            'paginationLinkUrl' => $paginationLinkUrl ?? $this->request->getServer('REQUEST_URI'),
            'formUrl'           => $this->commentaryFormUrl(),
            'SU_EDIT_COMMENTS'  => $session['user']['superuser'] & SU_EDIT_COMMENTS,
        ];

        return $env->load('_blocks/_commentary.html.twig')->renderBlock('commentary_block', $params);
    }

    /**
     * Display a comment.
     *
     * @param string $textDomain
     */
    public function displayOneComment(Environment $env, array $comment, $textDomain, array $commentary): string
    {
        global $session;

        $params = [
            'comment'                 => $comment,
            'textDomain'              => $textDomain,
            'commentary'              => $commentary,
            'defaultTextDomainStatus' => $commentary['textDomainStatus'] ?? null,
            'returnLink'              => $this->request->getServer('REQUEST_URI'),
            'SU_EDIT_COMMENTS'        => $session['user']['superuser'] & SU_EDIT_COMMENTS,
        ];

        return $env->load('_blocks/_commentary.html.twig')->renderBlock('commentary_comment', $params);
    }

    /**
     * Show if status of player (Online, Offline, Nearby).
     *
     * @param string $textDomain
     */
    public function displayStatusOnlinePlayer(array $comment, ?string $textDomain = null): string
    {
        global $session;

        $textDomain = ($textDomain ?? $this->textDomain) ?: $this->textDomain;

        $logout  = (int) $this->settings->getSetting('LOGINTIMEOUT', 900);
        $offline = new DateTime('now');
        $offline->sub(new DateInterval("PT{$logout}S"));

        $status = $this->getStatusMessages($textDomain);

        //-- Add basic status icons for online/offline/nearby/afk/dnd
        $icon = [ //-- Online in other chat
            // 'icon' => 'images/icons/onlinestatus/online.png',
            'outIcon'    => 'text-green-300 far fa-circle pulse transition looping',
            'insideIcon' => 'fas fa-circle text-green-800',
            'label'      => $status['online'],
        ];

        $singleIcon = $this->statusCommandSingleIcon($comment, $status);

        if ($singleIcon)
        {
            return $singleIcon;
        }

        if ('AFK' == \strtoupper($comment['chatloc']))
        {
            $icon = [
                // 'icon' => 'images/icons/onlinestatus/afk.png',
                'outIcon'    => 'far fa-circle',
                'insideIcon' => 'text-grey-500 fas fa-circle',
                'label'      => $status['afk'],
            ];
        }
        elseif ('DNI' == \strtoupper($comment['chatloc']))
        {
            $icon = [
                'icon'       => 'far fa-circle',
                'insideIcon' => 'text-blue-500 fas fa-circle',
                'label'      => $status['dni'],
            ];
        }
        elseif ($comment['laston'] < $offline || ! $comment['loggedin'])
        {
            $icon = [
                // 'icon' => 'images/icons/onlinestatus/offline.png',
                'outIcon'    => 'far fa-circle',
                'insideIcon' => 'text-red-500 fas fa-circle',
                'label'      => $status['offline'],
            ];
        }
        elseif ($comment['chatloc'] == $session['user']['chatloc'])
        { //-- Online and in same chat
            $icon = [
                // 'icon' => 'images/icons/onlinestatus/nearby.png',
                'outIcon'    => 'text-green-300 far fa-circle tada transition looping',
                'insideIcon' => 'text-orange-500 fas fa-circle',
                'label'      => $status['nearby'],
            ];
        }

        return \sprintf(
            '<span data-tooltip="%3$s"><span class="fa-stack" aria-label="%3$s" style="font-size: 0.5em;">
                <i class="%1$s fa-stack-2x" aria-hidden="true"></i>
                <i class="%2$s fa-stack-1x" aria-hidden="true"></i>
            </span></span>',
            $icon['outIcon'],
            $icon['insideIcon'],
            $icon['label']
        );
    }

    private function statusCommandSingleIcon(array $comment, array $status): ?string
    {
        global $session;

        $icon = null;

        //-- Is a message of the game
        if ('GAME' == \strtoupper($comment['command']))
        {
            $icon = \sprintf(
                '<span data-tooltip="%1$s" class="fa-stack" style="font-size: 0.5em;"><i class="fas fa-gamepad fa-stack-2x" aria-label="%1$s"></i></span>',
                $status['game']
            );
        }
        //-- Is a deleted message by the author
        elseif ('GREM' == \strtoupper($comment['command']))
        {
            $icon = \sprintf(
                '<span data-tooltip="%1$s" class="fa-stack" style="font-size: 0.5em;"><i class="fas fa-eraser fa-stack-2x" aria-label="%1$s"></i></span>',
                $status['grem']
            );
        }
        elseif ($session['user']['acctid'] == $comment['author'])
        {
            $icon = \sprintf(
                '<span data-tooltip="%1$s" class="fa-stack" style="font-size: 0.5em;"><i class="fas fa-user fa-stack-2x" aria-label="%1$s"></i></span>',
                $status['you']
            );
        }

        return $icon;
    }

    /**
     * Add form for add comment.
     *
     * @param array  $commentary Array with commentary data
     * @param string $textDomain Text domain for translator
     */
    public function addComment(Environment $env, array $commentary, string $textDomain): string
    {
        $params = [
            'formUrl'    => $this->commentaryFormUrl(),
            'textDomain' => $textDomain,
            'commentary' => $commentary,
            'colors'     => $this->color->getColors(),
            'maxChars'   => $this->settings->getSetting('maxchars', 200) + 100,
        ];

        return $env->load('_blocks/_commentary.html.twig')->renderBlock('commentary_add', $params);
    }

    /**
     * Save comment to data base.
     */
    public function saveComment(): void
    {
        $moderate = $this->request->getPost('hideComment');

        if ($moderate)
        {
            $this->commentary->moderateComments($moderate);

            return;
        }

        $data = $this->request->getPostAll();

        if ( ! $data || empty($data))
        {
            return;
        }

        $this->commentary->saveComment($data);
    }

    /**
     * Get status messages of player.
     *
     * @param string $textDomain
     */
    protected function getStatusMessages($textDomain): array
    {
        if ( ! $this->onlineStatus)
        {
            $this->onlineStatus = [
                'game'    => $this->translator->trans('commentary.status.game', [], $textDomain),
                'grem'    => $this->translator->trans('commentary.status.grem', [], $textDomain),
                'you'     => $this->translator->trans('commentary.status.you', [], $textDomain),
                'afk'     => $this->translator->trans('commentary.status.afk', [], $textDomain),
                'nearby'  => $this->translator->trans('commentary.status.nearby', [], $textDomain),
                'offline' => $this->translator->trans('commentary.status.offline', [], $textDomain),
                'dni'     => $this->translator->trans('commentary.status.dni', [], $textDomain),
                'online'  => $this->translator->trans('commentary.status.online', [], $textDomain),
            ];
        }

        return $this->onlineStatus;
    }

    /**
     * Get a valid url to use in commentary form.
     */
    protected function commentaryFormUrl(): string
    {
        $url = $this->request->getServer('REQUEST_URI');

        //-- Sanitize link: Delete previous queries of: "page", "c", "commentPage" and "frombio"
        $url = \preg_replace('/(?:[?&]c=[[:digit:]]+)|(?:[?&]page=[[:digit:]]+)|(?:[?&]commentPage=[[:digit:]]+)|(?:[?&]frombio=[[:alnum:]])/i', '', $url);
        $url = \preg_replace('/op=fight/i', 'op=continue', $url);

        if (false === \strpos($url, '?') && false !== \strpos($url, '&'))
        {
            $url = \preg_replace('/[&]/', '?', $url, 1);
        }

        //-- Check if have a ?
        if (false === \strpos($url, '?'))
        {
            $url = "{$url}?";
        }
        elseif (false !== \strpos($url, '?'))
        {
            $url = "{$url}&";
        }

        return $url;
    }
}
