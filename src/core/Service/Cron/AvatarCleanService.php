<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.2.0
 */

namespace Lotgd\Core\Service\Cron;

use Doctrine\ORM\EntityManagerInterface;
use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Log;
use Lotgd\Core\Tool\Backup;
use Symfony\Contracts\Translation\TranslatorInterface;

class AvatarCleanService
{
    public const TRANSLATION_DOMAIN = 'console_command';

    protected static $defaultName = 'lotgd:cron:content:clean';

    private $settings;
    private $log;
    private $translator;
    private $doctrine;
    private $backup;

    public function __construct(
        Settings $settings,
        Log $log,
        TranslatorInterface $translator,
        EntityManagerInterface $doctrine,
        Backup $backup
    ) {
        $this->settings   = $settings;
        $this->log        = $log;
        $this->translator = $translator;
        $this->doctrine   = $doctrine;
        $this->backup     = $backup;
    }

    public function execute(): void
    {
        $lastexpire   = strtotime($this->settings->getSetting('last_char_expire', '0000-00-00 00:00:00'));
        $needtoexpire = strtotime('-23 hours');

        if ($lastexpire >= $needtoexpire)
        {
            return;
        }

        $this->settings->saveSetting('last_char_expire', date('Y-m-d H:i:s'));

        $old   = (int) $this->settings->getSetting('expireoldacct', 45);
        $new   = (int) $this->settings->getSetting('expirenewacct', 10);
        $trash = (int) $this->settings->getSetting('expiretrashacct', 1);

        /** @var \Lotgd\Core\Repository\AvatarRepository $repository */
        $repository = $this->doctrine->getRepository('LotgdCore:Avatar');
        $query      = $repository->createQueryBuilder('u');
        $expr       = $query->expr();

        $dateOld = new \DateTime('now');
        $dateOld->sub(new \DateInterval("P{$old}D"));

        $dateNew = new \DateTime('now');
        $dateNew->sub(new \DateInterval("P{$new}D"));

        $dateTrash = new \DateTime('now');
        $dateTrash->sub(new \DateInterval("P{$trash}D"));

        $query
            ->where('BIT_AND(a.superuser, :permit) = 0')
            ->andWhere($expr->orX(
                '1 = 0',
                $old !== 0 ? $expr->lt('a.laston', ':dateOld') : null,
                $new !== 0 ? $expr->andX($expr->lt('a.laston', ':dateNew'), $expr->eq('u.level', 1), $expr->eq('u.dragonkills', 0)) : null,
                $trash !== 0 ? $expr->andX($expr->lt('a.regdate', ':dateTrash'), $expr->eq('a.laston', 'a.regdate')) : null
            ))
            ->leftJoin('LotgdCore:User', 'a', 'with', $expr->eq('a.acctid', 'u.acct'))

            ->setParameter('permit', NO_ACCOUNT_EXPIRATION)
        ;

        ($old !== 0) ? $query->setParameter('dateOld', $dateOld) : null;
        ($new !== 0) ? $query->setParameter('dateNew', $dateNew) : null;
        ($trash !== 0) ? $query->setParameter('dateTrash', $dateTrash) : null;

        $result = $query->getQuery()->getResult();

        //-- Delete users and backups data
        $this->deleteAndBackup($result);

        //adjust for notification - don't notify total newbie chars
        $old = max(1, $old - (int) $this->settings->getSetting('notifydaysbeforedeletion', 5)); //a minimum of 1 day is necessary

        /** @var \Lotgd\Core\Repository\UserRepository $repository */
        $repository = $this->doctrine->getRepository('LotgdCore:User');
        $query      = $repository->createQueryBuilder('a');

        $query
            ->where('BIT_AND(a.superuser, :permit) = 0')
            ->andWhere(
                $expr->orX(
                    '1 = 0',
                    $old !== 0 ? $expr->lt('a.laston', ':dateOld') : null
                ),
                $expr->andX(
                    $expr->neq('a.emailaddress', ':empty'),
                    $expr->eq('a.sentnotice', 0)
                )
            )
            ->leftJoin('LotgdCore:Avatar', 'u', 'with', $expr->eq('u.id', 'a.avatar'))

            ->setParameter('permit', NO_ACCOUNT_EXPIRATION)
            ->setParameter('empty', '')
        ;

        ($old !== 0) ? $query->setParameter('dateOld', $dateOld) : null;

        $result = $query->getQuery()->getResult();

        $server = $this->settings->getSetting('serverurl', 'https://nodomain.notd');

        foreach ($result as $entity)
        {
            $prefs = $entity->getPrefs();

            $subject = $this->translator->trans('expirationnotice.subject', [], 'app_mail', $prefs['language'] ?? null);
            $message = $this->translator->trans('expirationnotice.body', [
                'charname' => $entity->getLogin(),
                'server'   => $server,
            ], 'app_mail', $prefs['language'] ?? null);

            lotgd_mail($entity->getEmailaddress(), $subject, $message);

            $entity->setSentnotice(true);

            $this->doctrine->persist($entity);
        }

        $this->doctrine->flush();
    }

    private function deleteAndBackup($result)
    {
        $pinfo  = [];
        $dk0lvl = 0;
        $dk0ct  = 0;
        $dk1lvl = 0;
        $dk1ct  = 0;
        $dks    = 0;

        foreach ($result as $entity)
        {
            //-- Delete account and data related
            if ( ! $this->backup->characterCleanUp($entity->getAcct()->getAcctid(), CHAR_DELETE_AUTO))
            {
                continue;
            }

            $pinfo[] = "{$entity->getAcct()->getLogin()}:dk{$entity->getDragonkills()}-lv{$entity->getLevel()}";

            if (0 == $entity->getDragonkills())
            {
                $dk0lvl += $entity->getLevel();
                ++$dk0ct;
            }
            elseif (1 == $entity->getDragonkills())
            {
                $dk1lvl += $entity->getLevel();
                ++$dk1ct;
            }
            $dks += $entity->getDragonkills();
        }

        //Log which accounts were deleted.
        $msg = "[{$dk0ct}] with 0 dk avg lvl [".round($dk0lvl / max(1, $dk0ct), 2)."]\n";
        $msg .= "[{$dk1ct}] with 1 dk avg lvl [".round($dk1lvl / max(1, $dk1ct), 2)."]\n";
        $msg .= 'Avg DK: ['.round($dks / max(1, \count($result)), 2)."]\n";
        $msg .= 'Accounts: '.implode(', ', $pinfo);

        $this->log->game('Deleted '.\count($result)." accounts:\n{$msg}", 'char expiration');
    }
}
