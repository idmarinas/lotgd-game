<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 *
 * @since 5.3.0
 */

namespace Lotgd\Core\Tool;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Laminas\Hydrator\ClassMethodsHydrator;
use Lotgd\Core\Combat\Buffer;
use Lotgd\Core\Entity\AccountsEverypage;
use Lotgd\Core\Entity\AccountsOutput;
use Lotgd\Core\Entity\News;
use Lotgd\Core\Event\Other;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Http\Response;
use Lotgd\Core\Lib\Settings as LibSettings;
use Lotgd\Core\Tool\Tool\DeathMessage;
use Lotgd\Core\Tool\Tool\Name;
use Lotgd\Core\Tool\Tool\Substitute;
use Lotgd\Core\Tool\Tool\Taunt;
use Lotgd\Core\Tool\Tool\Title;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Tool
{
    use DeathMessage;
    use Name;
    use Substitute;
    use Taunt;
    use Title;

    private $dispatcher;
    private $doctrine;
    private $settings;
    private $response;
    private $cache;
    private $request;
    private $translator;
    private $buffer;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        EntityManagerInterface $doctrine,
        LibSettings $settings,
        Request $request,
        TranslatorInterface $translator,
        Buffer $buffer,
        CacheInterface $cacheApp
    ) {
        $this->dispatcher = $dispatcher;
        $this->doctrine   = $doctrine;
        $this->settings   = $settings;
        $this->cache      = $cacheApp;
        $this->request    = $request;
        $this->translator = $translator;
        $this->buffer     = $buffer;
    }

    /**
     * Adds a news item for the current user.
     */
    public function addNews(string $text, array $params = [], string $textDomain = 'partial_news', bool $hideFromBio = false): void
    {
        global $session;

        $user = $session['user']['acctid'] ?? 0;
        $user = $hideFromBio ? 0 : $user;

        $entity = (new News())
            ->setDate(new DateTime('now'))
            ->setText($text)
            ->setArguments($params)
            ->setTextDomain($textDomain)
            ->setAccountId($user)
        ;

        $this->doctrine->persist($entity);
        $this->doctrine->flush();
    }

    /**
     * Returns the experience needed to advance to the next level.
     *
     * @param int $curlevel the current level of the player
     * @param int $curdk    the current number of dragonkills
     *
     * @return int the amount of experience needed to advance to the next level
     */
    public function expForNextLevel(int $curlevel, int $curdk): int
    {
        // the exp is first 3 times the starting one, then later goes down to <25% from the previous one. It is harder to obtain enough exp though.
        $expstring = $this->settings->getSetting('exp-array', '100,400,1002,1912,3140,4707,6641,8985,11795,15143,19121,23840,29437,36071,43930');
        $maxlevel  = (int) $this->settings->getSetting('maxlevel', 15);
        $cacheKey  = 'exp-for-next-level-array-'.md5($expstring)."-lvl-{$maxlevel}-dk-{$curdk}";

        // error!
        if ('' == $expstring)
        {
            $this->response->pageDebug('Setting "exp-array" is empty. Configure this setting. Return 0 as exp need for next level.');

            return 0;
        }

        // fetch all for that DK if already calculated!
        $exparray = $this->cache->get($cacheKey, function () use ($expstring, $curdk, $maxlevel)
        {
            $exparray = explode(',', $expstring);
            $count    = \count($exparray);

            foreach ($exparray as $key => $val)
            {
                $exparray[$key] = (int) round($val + ($curdk / 4) * ($key + 1) * 100, 0);
            }

            // -- Always +1 level max too avoid error of cant get exp need for next level if player are in máx level
            ++$maxlevel;
            // fill it up, we have too few entries to have a valid exp array
            if ($maxlevel > $count)
            {
                for ($i = $count; $i < $maxlevel; ++$i)
                {
                    $exparray[$i] = (int) round($exparray[$i - 1] * 1.2);
                }
            }

            return $exparray;
        });

        // -- Avoid level less than 0 and more than max lvl
        $curlevel = min(max($curlevel - 1, 0), $maxlevel);

        // -- If not find level invalidate cache and redo it
        if ( ! isset($exparray[$curlevel]))
        {
            $this->cache->delete($cacheKey);

            return $this->expForNextLevel($curlevel, $curdk);
        }

        return $exparray[$curlevel];
    }

    /**
     * Check if user have a ban.
     */
    public function checkBan(?string $login = null): void
    {
        global $session;

        if ($session['banoverride'] ?? false)
        {
            return;
        }
        elseif ( ! $login)
        {
            $ip = $this->request->getServer('REMOTE_ADDR');
            $id = $this->request->getCookie('lgi', '');
        }
        else
        {
            $repository = $this->doctrine->getRepository('LotgdCore:User');
            /** @var \Lotgd\Core\Entity\User $result */
            $result = $repository->findOneBy(['login' => $login]);

            if ($result && ($result->getBanoverride() || ($result->getSuperuser() & ~SU_DOESNT_GIVE_GROTTO)))
            {
                $session['banoverride'] = true;

                return;
            }

            $ip = $result->getLastip();
            $id = $result->getUniqueid();
        }

        /** @var \Lotgd\Core\Repository\BansRepository $repository */
        $repository = $this->doctrine->getRepository('LotgdCore:Bans');
        $repository->removeExpireBans();

        $query  = $repository->createQueryBuilder('u');
        $result = $query->where("((substring(:ip ,1 , length(u.ipfilter)) = u.ipfilter AND u.ipfilter != '') OR (u.uniqueid = :id AND u.uniqueid != '')) AND (u.banexpire = '0000-00-00' OR u.banexpire >= :date)")

            ->setParameter('ip', $ip)
            ->setParameter('id', $id)
            ->setParameter('date', new DateTime('now'))

            ->getQuery()
            ->getResult()
        ;

        if ( ! empty($result))
        {
            $session = [];
            $session['message'] .= $this->translator->trans('checkban.ban', [], 'page_bans');

            foreach ($result as $row)
            {
                $session['message'] .= $row->getBanreason().'`n';

                $message = $this->translator->trans('checkban.expire.time', ['date' => $row->getBanexpire()], 'page_bans');

                if (new DateTime('0000-00-00') == $row->getBanexpire() || new DateTime('0000-00-00 00:00:00') == $row->getBanexpire())
                {
                    $message = $this->translator->trans('checkban.expire.permanent', [], 'page_bans');
                }

                $session['message'] .= $message;

                $row->setLasthit(new DateTime('now'));
                $this->doctrine->persist($row);

                $session['message'] .= '`n';
                $session['message'] .= $this->translator->trans('checkban.by', ['by' => $row['banner']], 'page_bans');
            }

            $this->doctrine->flush();

            $session['message'] .= $this->translator->trans('checkban.note', [], 'page_bans');
            header('Location: index.php');

            exit();
        }
    }

    /**
     * Get info of mount by ID.
     *
     * @param int $horse
     */
    public function getMount($horse = 0): ?array
    {
        $horse = (int) $horse;

        if (0 === $horse)
        {
            return null;
        }

        /** @var \Lotgd\Core\Repository\MountsRepository $repository */
        $repository = $this->doctrine->getRepository('LotgdCore:Mounts');

        return $repository->extractEntity($repository->find($horse));
    }

    /**
     * Get name of partner.
     */
    public function getPartner(bool $player = false): string
    {
        global $session;

        if ( ! isset($session['user']['prefs']['sexuality']) || empty($session['user']['prefs']['sexuality']))
        {
            $session['user']['prefs']['sexuality'] = ! $session['user']['sex'];
        }

        $partnerFemale = $this->settings->getSetting('barmaid', '`%Violet`0');
        $partnerMale   = $this->settings->getSetting('bard', '`^Seth`0');

        if ( ! $player || ($player && INT_MAX == $session['user']['marriedto']))
        {
            return SEX_MALE == $session['user']['prefs']['sexuality'] ? $partnerMale : $partnerFemale;
        }

        /** @var \Lotgd\Core\Repository\UserRepository $repository */
        $repository = $this->doctrine->getRepository('LotgdCore:User');
        $name       = $repository->getCharacterNameFromAcctId($session['user']['marriedto']);

        if ('' !== $name && '0' !== $name)
        {
            return $name;
        }

        $session['user']['marriedto'] = 0;

        return SEX_MALE == $session['user']['prefs']['sexuality'] ? $partnerMale : $partnerFemale;
    }

    public function holidayize(string $text, string $type = 'unknown'): string
    {
        global $session;

        $session['user']['prefs']['ihavenocheer'] ??= 0;

        if ($session['user']['prefs']['ihavenocheer'])
        {
            return $text;
        }

        $args = new Other(['text' => $text, 'type' => $type]);
        $this->dispatcher->dispatch($args, Other::SPECIAL_HOLIDAY);
        $args = modulehook('holiday', $args->getData());

        return $args['text'];
    }

    /**
     * Save user data and character.
     */
    public function saveUser(bool $updateLastOn = true, bool $regenSession = false): void
    {
        global $session, $companions;

        // -- It's defined as not save user, Not are a user logged in or not are defined id of account
        if (\defined('NO_SAVE_USER') || ! ($session['loggedin'] ?? false) || ! ($session['user']['acctid'] ?? false))
        {
            return;
        }

        // Any time we go to save a user, make SURE that any tempstat changes
        // are undone.
        $this->buffer->restoreBuffFields();

        $session['user']['bufflist'] = $session['bufflist'];

        if ($updateLastOn)
        {
            $session['user']['laston'] = new DateTime('now');
        }

        if (isset($companions) && \is_array($companions))
        {
            $session['user']['companions'] = $companions;
        }

        $hydrator = new ClassMethodsHydrator();

        $accountRep = $this->doctrine->getRepository('LotgdCore:User');
        $pageRep    = $this->doctrine->getRepository('LotgdCore:AccountsEverypage');

        $everypage = $hydrator->hydrate($session['user'], $pageRep->find((int) $session['user']['acctid']) ?: new AccountsEverypage());
        $account   = $hydrator->hydrate($session['user'], $accountRep->find((int) $session['user']['acctid']));
        $character = $hydrator->hydrate($session['user'], $account->getAvatar());

        $account->setAvatar($character);
        $character->setAcct($account);

        $this->doctrine->persist($account);
        $this->doctrine->persist($everypage);

        if ($session['output'] ?? false)
        {
            $outputRep  = $this->doctrine->getRepository('LotgdCore:AccountsOutput');
            $acctOutput = $outputRep->find((int) $session['user']['acctid']) ?: new AccountsOutput();

            $acctOutput->setAcctid($session['user']['acctid'])
                ->setOutput(gzcompress($session['output'], 1))
            ;

            $this->doctrine->persist($acctOutput);
        }

        $this->doctrine->flush(); // Persist objects

        if ($regenSession)
        {
            $session['user'] = [
                'acctid'   => $session['user']['acctid'],
                'loggedin' => $session['user']['loggedin'],
            ];
        }
    }


    /** @required */
    public function setResponse(Response $response): self
    {
        $this->response = $response;

        return $this;
    }
}
