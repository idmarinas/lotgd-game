<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Output;

use Doctrine\ORM\EntityManagerInterface;
use Laminas\Filter;
use Lotgd\Core\Entity as LotgdEntity;
use Lotgd\Core\Entity\Commentary as EntityCommentary;
use Lotgd\Core\EntityRepository\CommentaryRepository;
use Lotgd\Core\Event\Commentary as EventCommentary;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Commentary
{
    public const TEXT_DOMAIN = 'app_commentary';

    /**
     * Repository of commentary.
     *
     * @var CommentaryRepository
     */
    protected $repository;
    protected $translator;
    protected $cache;
    protected $censor;
    protected $hook;
    protected $flashBag;
    protected $doctrine;
    protected $normalizer;

    public function __construct(
        TranslatorInterface $translator,
        CacheInterface $appCache,
        Censor $censor,
        EventDispatcherInterface $hook,
        FlashBagInterface $flashBag,
        EntityManagerInterface $doctrine,
        DenormalizerInterface $normalizer
    ) {
        $this->translator = $translator;
        $this->cache      = $appCache;
        $this->censor     = $censor;
        $this->hook       = $hook;
        $this->flashBag   = $flashBag;
        $this->doctrine   = $doctrine;
        $this->normalizer = $normalizer;
    }

    /**
     * Get comments in the section.
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
     */
    public function moderateComments(?array $post): bool
    {
        if ( ! $post)
        {
            return false;
        }

        return $this->getRepository()->moderateComments($post);
    }

    /**
     * Process save comment.
     */
    public function saveComment(array $post): bool
    {
        global $session;

        //-- Clean comment
        $post['comment']    = $this->cleanComment($post['comment']);
        $post['commentRaw'] = $post['comment']; //-- Only filter for save safe in DB

        //-- Check if have comment and them process commands in comment
        if ( ! $post['comment'] || ! $this->processCommands($post))
        {
            return false;
        }

        //-- Add data of clan
        if ($session['user']['clanid'] && $session['user']['clanrank'])
        {
            $clanInfo = $this->cache->get("commentary-claninfo-{$session['user']['clanid']}", function (ItemInterface $item) use ($session)
            {
                $item->expiresAt(new \DateTime('tomorrow'));

                /** @var Entity\Core\EntityRepository\ClanRepository */
                $clanRep = $this->doctrine->getRepository(\Lotgd\Core\Entity\Clans::class);
                $clanInfo = $clanRep->findOneBy(['clanid' => $session['user']['clanid']]);
                $clanInfo = $clanRep->extractEntity($clanInfo);

                $clanInfo['clanrank'] = $session['user']['clanrank'];

                return $clanInfo;
            });

            $post['clanId']        = $session['user']['clanid'];
            $post['clanRank']      = $session['user']['clanrank'];
            $post['clanName']      = $clanInfo['clanname'];
            $post['clanNameShort'] = $clanInfo['clanshort'];
        }

        $post['author']     = $session['user']['acctid'];
        $post['authorName'] = $session['user']['name'];

        //-- Apply profanity filter
        $post['comment']      = $this->censor->filter($post['comment']);
        $post['commentOri']   = $this->censor->getOrigString();
        $post['commentMatch'] = $this->censor->getMatchWords();

        $args = new EventCommentary(['data' => $post]);
        $this->hook->dispatch($args, EventCommentary::COMMENT_POST);
        $args = modulehook('postcomment', $args->getData());

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
        if ( ! $this->repository instanceof CommentaryRepository)
        {
            $this->repository = $this->doctrine->getRepository(LotgdEntity\Commentary::class);
        }

        return $this->repository;
    }

    /**
     * Process commands for comentary.
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

        $command = \strtoupper($data['comment']);

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
        $args = new EventCommentary(['command' => $command, 'section' => $data['section'], 'data' => &$data]);
        $this->hook->dispatch($args, EventCommentary::COMMANDS);
        $returnedHook = modulehook('commentary-command', $args->getData());

        $processed = true;

        if (isset($returnedHook['skipCommand']) && ! $returnedHook['skipCommand'])
        {
            //if for some reason you're going to involve a command that can be a mix of upper and lower case, set $args['skipCommand'] and $args['ignore'] to true and handle it in postcomment instead.
            if (isset($returnedHook['processed']) && ! $returnedHook['processed'])
            {
                $this->flashBag->add('info', $this->translator->trans('command.unrecognized', [], self::TEXT_DOMAIN));
            }

            $processed = false;
        }

        return $processed;
    }

    /**
     * Process special commands that save to data base.
     */
    public function processSpecialCommands(array &$data): bool
    {
        if ('/me' == \substr($data['comment'], 0, 3))
        {
            $data['comment'] = \trim(\substr($data['comment'], 3));
            $data['command'] = 'me';
        }
        elseif ('::' == \substr($data['comment'], 0, 1))
        {
            $data['comment'] = \trim(\substr($data['comment'], 2));
            $data['command'] = 'me';
        }
        elseif (':' == \substr($data['comment'], 0, 1))
        {
            $data['comment'] = \trim(\substr($data['comment'], 1));
            $data['command'] = 'me';
        }

        //-- If process special commands return
        return (bool) ($data['command'] ?? false);
    }

    /**
     * Clean comment for safety insert in DB.
     */
    public function cleanComment(?string $comment): string
    {
        if ( ! $comment)
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
        if (\substr_count($comment, '`i') != \substr_count($comment, '´i'))
        {
            $filterChain->attach(new Filter\PregReplace(['pattern' => ['/`i/', '/´i/'], 'replacement' => '']));
        }

        //-- Only accept correct format, all bold open tag need close tag.
        //-- Other wise, bold format is removed.
        if (\substr_count($comment, '`b') != \substr_count($comment, '´b'))
        {
            $filterChain->attach(new Filter\PregReplace(['pattern' => ['/`b/', '/´b/'], 'replacement' => '']));
        }

        //-- Only accept correct format, all center open tag need close tag.
        //-- Other wise, center format is removed.
        if (\substr_count($comment, '`c') != \substr_count($comment, '´c'))
        {
            $filterChain->attach(new Filter\PregReplace(['pattern' => ['/`c/', '/´c/'], 'replacement' => '']));
        }

        $comment = $filterChain->filter($comment);

        //-- Process comment
        $args = new EventCommentary(['comment' => $comment]);
        $this->hook->dispatch($args, EventCommentary::COMMENT);
        $comment = modulehook('commentary-comment', $args->getData());

        return $comment['comment'];
    }

    /**
     * All comentary sections.
     */
    public function commentaryLocs(): array
    {
        return $this->cache->get('commentary-comsecs', function (ItemInterface $item)
        {
            $item->expiresAt(new \DateTime('tomorrow'));

            $comsecs = [];
            $comsecs['village'] = $this->translator->trans('section.village', ['village' => getsetting('villagename', LOCATION_FIELDS)], self::TEXT_DOMAIN);
            $comsecs['superuser'] = $this->translator->trans('section.superuser', [], self::TEXT_DOMAIN);
            $comsecs['shade'] = $this->translator->trans('section.shade', [], self::TEXT_DOMAIN);
            $comsecs['grassyfield'] = $this->translator->trans('section.grassyfield', [], self::TEXT_DOMAIN);
            $comsecs['inn'] = getsetting('innname', LOCATION_INN);
            $comsecs['motd'] = $this->translator->trans('section.motd', [], self::TEXT_DOMAIN);
            $comsecs['veterans'] = $this->translator->trans('section.veterans', [], self::TEXT_DOMAIN);
            $comsecs['hunterlodge'] = $this->translator->trans('section.hunterlodge', [], self::TEXT_DOMAIN);
            $comsecs['gardens'] = $this->translator->trans('section.gardens', [], self::TEXT_DOMAIN);
            $comsecs['waiting'] = $this->translator->trans('section.waiting', [], self::TEXT_DOMAIN);
            $comsecs['beta'] = $this->translator->trans('section.beta', [], self::TEXT_DOMAIN);

            $comsecs = new EventCommentary($comsecs);
            // All of the ones after this will be translated in the modules.
            $this->hook->dispatch($comsecs, EventCommentary::MODERATE_SECTIONS);

            return modulehook('moderate-comment-sections', $comsecs);
        });
    }

    /**
     * Get list of comments.
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
     */
    protected function injectComment(array $data): bool
    {
        $commentary = $this->normalizer->denormalize($data, EntityCommentary::class);

        return $this->getRepository()->saveComment($commentary);
    }
}
