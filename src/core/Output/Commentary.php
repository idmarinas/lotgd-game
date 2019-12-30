<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Output;

use Lotgd\Core\Entity as LotgdEntity;
use Lotgd\Core\EntityRepository\CommentaryRepository;
use Lotgd\Core\Pattern;
use Zend\Filter;

class Commentary
{
    use Pattern\LotgdCore;

    /**
     * Repository of commentary.
     *
     * @var CommentaryRepository
     */
    protected $repository;

    /**
     * Get comments in the section.
     *
     * @param string $section
     * @param int    $page
     * @param int    $limit
     *
     * @return Paginator
     */
    public function getComments(string $section, int $page = 1, int $limit = 25)
    {
        return $this->getList($section, $page, $limit);
    }

    /**
     * Get comments for moderate.
     *
     * @return Paginator
     */
    public function getCommentsModerate()
    {
        return $this->getList(null, 1, 100);
    }

    /**
     * Moderate comments, Hide/Unhide.
     *
     * @param array|null $post
     *
     * @return bool
     */
    public function moderateComments(?array $post): bool
    {
        if (! $post)
        {
            return false;
        }

        return $this->getRepository()->moderateComments($post);
    }

    /**
     * Process save comment.
     *
     * @param array $post
     *
     * @return bool
     */
    public function saveComment(array $post): bool
    {
        global $session;

        //-- Clean comment
        $post['comment'] = $this->cleanComment($post['comment']);
        $post['commentRaw'] = $post['comment']; //-- Only filter for save safe in DB

        //-- Check if have comment and them process commands in comment
        if (! $post['comment'] || ! $this->processCommands($post))
        {
            return false;
        }

        //-- Add data of clan
        if ($session['user']['clanid'] && $session['user']['clanrank'])
        {
            $clanInfo = datacache("commentary-claninfo-{$session['user']['clanid']}", 86400, true);

            if (! is_array($clanInfo) || empty($clanInfo))
            {
                $clanRep = $this->doctrine->getRepository(\Lotgd\Core\Entity\Clans::class);
                $clanInfo = $clanRep->findOneBy(['clanid' => $session['user']['clanid']]);
                $clanInfo = $clanRep->extractEntity($clanInfo);

                $clanInfo['clanrank'] = $session['user']['clanrank'];

                updatedatacache("commentary-claninfo-{$session['user']['clanid']}", $clanInfo, true);
            }

            $post['clanId'] = $session['user']['clanid'];
            $post['clanRank'] = $session['user']['clanrank'];
            $post['clanName'] = $clanInfo['clanname'];
            $post['clanNameShort'] = $clanInfo['clanshort'];
        }

        $post['author'] = $session['user']['acctid'];
        $post['authorName'] = $session['user']['name'];

        //-- Apply profanity filter
        $censor = $this->getCensor();
        $post['comment'] = $censor->filter($post['comment']);
        $post['commentOri'] = $censor->getOrigString();
        $post['commentMatch'] = $censor->getMatchWords();

        $args = modulehook('postcomment', ['data' => $post]);

        //-- A module tells us to ignore this comment, so we will
        if ($args['ignore'] ?? false)
        {
            return false;
        }

        $session['user']['recentcomments'] = new \DateTime('now');

        return $this->injectComment($args['data']);
    }

    /**
     * Set instance of Doctrine.
     *
     * @return Lotgd\Core\Entity\Commentary
     */
    public function getRepository()
    {
        if (! $this->repository instanceof CommentaryRepository)
        {
            $this->repository = $this->getDoctrineRepository(LotgdEntity\Commentary::class);
        }

        return $this->repository;
    }

    /**
     * Process commands for comentary.
     *
     * @param array $data
     *
     * @return array
     */
    public function processCommands(array &$data): bool
    {
        global $session;

        //-- Special command for users
        if ($this->processSpecialCommands($data))
        {
            return true;
        }
        //-- /game will have a specific function for the system

        $command = strtoupper($data['comment']);

        //-- Deletes the user's last written comment, only if no more than 24 hours have passed.
        // if ('GREM' == $command || '::GREM' == $command || '/GREM' == $command)
        // {
        //     $last = $this->getRepository()->createQueryBuilder('u');

        //     $date = new \DateTime('now');
        //     $date->sub(new \DateInterval("P1D"));

        //     $last->where('u.author = :id AND u.postdate >= :date')
        //     ->setParameters([
        //             'id' => $session['user']['acctid'],
        //             'date' => $date
        //         ])
        //         ->setMaxResults(1)
        //         ->getQuery()
        //         ->getResult()
        //     ;

        //     $last = $this->getRepository()->findBy([
        //         'author' => $session['user']['acctid'],
        //         'postdate' => '',
        //     ], ['postdate' => 'DESC'], 1);

        //     return false;
        // }

        //-- Process additional commands
        $returnedHook = modulehook('commentary-command', ['command' => $command, 'section' => $data['section'], 'data' => &$data]);

        $processed = true;

        if (isset($returnedHook['skipCommand']) && ! $returnedHook['skipCommand'])
        {
            //if for some reason you're going to involve a command that can be a mix of upper and lower case, set $args['skipCommand'] and $args['ignore'] to true and handle it in postcomment instead.
            if (isset($returnedHook['processed']) && ! $returnedHook['processed'])
            {
                \LotgdFlashMessages::addInfoMessage(appoencode("`c`b`JCommand Not Recognized´b`0`nWhen you type in ALL CAPS, the game doesn't think you're talking to other players; it thinks you're trying to perform an action within the game.  For example, typing `#GREM`0 will remove the last comment you posted, as long as you posted it less than two minutes ago.  Typing `#AFK`0 or `#BRB`0 will turn your online status bar grey, so that people know you're `#A`0way `#F`0rom the `#K`0eyboard (or, if you prefer, that you'll `#B`0e `#R`0ight `#B`0ack).  Typing `#DNI`0 will let other players know that you're busy talking to one particular player - maybe somewhere off-camera - and that you don't want to be interrupted right now.`nSome areas have special hidden commands or other easter eggs that you can hunt for. This time around, you didn't trigger anything special.´c`0`n"));
            }

            $processed = false;
        }

        return $processed;
    }

    /**
     * Process special commands that save to data base.
     *
     * @param array $data
     *
     * @return bool
     */
    public function processSpecialCommands(array &$data): bool
    {
        if ('/me' == substr($data['comment'], 0, 3))
        {
            $data['comment'] = trim(substr($data['comment'], 3));
            $data['command'] = 'me';
        }
        elseif ('::' == substr($data['comment'], 0, 1))
        {
            $data['comment'] = trim(substr($data['comment'], 2));
            $data['command'] = 'me';
        }
        elseif (':' == substr($data['comment'], 0, 1))
        {
            $data['comment'] = trim(substr($data['comment'], 1));
            $data['command'] = 'me';
        }

        //-- If process special commands return
        return (bool) ($data['command'] ?? false);
    }

    /**
     * Clean comment for safety insert in DB.
     *
     * @param string|null $comment
     *
     * @return string
     */
    public function cleanComment(?string $comment): string
    {
        if (! $comment)
        {
            return '';
        }

        $filterChain = new Filter\FilterChain();
        $filterChain
            ->attach(new Filter\StringTrim())
            ->attach(new Filter\StripTags())
            ->attach(new Filter\StripNewlines())
            ->attach(new Filter\PregReplace(['pattern' => '/`n/', 'replacement' => '']))
            ->attach(new Filter\PregReplace(['pattern' => '/([^[:space:]]{45,45})([^[:space:]])/', 'replacement' => '\\1 \\2']))
            ->attach(new Filter\Callback([new \HTMLPurifier(), 'purify']), -1) //-- Executed last in query
        ;

        //-- Only accept correct format, all italic open tag need close tag.
        //-- Other wise, italic format is removed.
        if (substr_count($comment, '`i') != substr_count($comment, '´i'))
        {
            $filterChain->attach(new Filter\PregReplace(['pattern' => ['/`i/', '/´i/'], 'replacement' => '']));
        }

        //-- Only accept correct format, all bold open tag need close tag.
        //-- Other wise, bold format is removed.
        if (substr_count($comment, '`b') != substr_count($comment, '´b'))
        {
            $filterChain->attach(new Filter\PregReplace(['pattern' => ['/`b/', '/´b/'], 'replacement' => '']));
        }

        //-- Only accept correct format, all center open tag need close tag.
        //-- Other wise, center format is removed.
        if (substr_count($comment, '`c') != substr_count($comment, '´c'))
        {
            $filterChain->attach(new Filter\PregReplace(['pattern' => ['/`c/', '/´c/'], 'replacement' => '']));
        }

        $comment = $filterChain->filter($comment);

        //-- Process comment
        $comment = modulehook('commentary-comment', ['comment' => $comment]);

        return $comment['comment'];
    }

    /**
     * All comentary sections.
     *
     * @return array
     */
    public function commentaryLocs(): array
    {
        $comsecs = datacache('commentary-comsecs', 1800, true);

        if (is_array($comsecs) && count($comsecs))
        {
            return $comsecs;
        }

        $domain = 'app-commentary';

        $comsecs = [];
        $comsecs['village'] = \LotgdTranslator::t('section.village', ['village' => getsetting('villagename', LOCATION_FIELDS)], $domain);
        $comsecs['superuser'] = \LotgdTranslator::t('section.superuser', [], $domain);
        $comsecs['shade'] = \LotgdTranslator::t('section.shade', [], $domain);
        $comsecs['grassyfield'] = \LotgdTranslator::t('section.grassyfield', [], $domain);
        $comsecs['inn'] = getsetting('innname', LOCATION_INN);
        $comsecs['motd'] = \LotgdTranslator::t('section.motd', [], $domain);
        $comsecs['veterans'] = \LotgdTranslator::t('section.veterans', [], $domain);
        $comsecs['hunterlodge'] = \LotgdTranslator::t('section.hunterlodge', [], $domain);
        $comsecs['gardens'] = \LotgdTranslator::t('section.gardens', [], $domain);
        $comsecs['waiting'] = \LotgdTranslator::t('section.waiting', [], $domain);
        $comsecs['beta'] = \LotgdTranslator::t('section.beta', [], $domain);

        // All of the ones after this will be translated in the modules.
        $comsecs = modulehook('moderate-comment-sections', $comsecs);

        updatedatacache('commentary-comsecs', $comsecs, true);

        return $comsecs;
    }

    /**
     * Get list of comments.
     *
     * @param string|null $section
     * @param int         $page
     * @param int         $limit
     *
     * @return Paginator
     */
    protected function getList(?string $section, int $page = 1, int $limit = 25)
    {
        $query = $this->getRepository()->createQueryBuilder('u');

        $query->select('u.id', 'u.section', 'u.command', 'u.comment', 'u.postdate', 'u.extra', 'u.author', 'u.authorName', 'u.clanId', 'u.clanRank', 'u.clanName', 'u.clanNameShort', 'u.hidden', 'u.hiddenComment', 'u.hiddenBy', 'u.hiddenByName')
            ->addSelect('a.loggedin', 'a.laston')
            ->addSelect('c.chatloc')
            ->leftJoin('LotgdCore:Accounts', 'a', 'WITH', $query->expr()->eq('a.acctid', 'u.author'))
            ->leftJoin('LotgdCore:Characters', 'c', 'WITH', $query->expr()->eq('c.id', 'a.character'))
            ->orderBy('u.postdate', 'DESC')
            ->addOrderBy('u.section', 'ASC')
        ;

        if ($section)
        {
            $query->where('u.section = :section')
                ->setParameter('section', $section)
            ;
        }

        return $this->getRepository()->getPaginator($query, $page, $limit);
    }

    /**
     * Save data in data base.
     *
     * @param array $data
     *
     * @return bool
     */
    protected function injectComment(array $data): bool
    {
        $commentary = $this->hydrateEntity($data);

        return $this->getRepository()->saveComment($commentary);
    }
}
