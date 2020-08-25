<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.3.0
 */

namespace Lotgd\Ajax\Core;

use Jaxon\Request\Traits\Factory as Paginate;
use Jaxon\Response\Response;
use Lotgd\Core\AjaxAbstract;
use Lotgd\Core\EntityRepository\MotdRepository;
use Tracy\Debugger;
use Lotgd\Ajax\Pattern\Core as PatternCore;

/**
 * Dialog for MOTD.
 */
class Motd extends AjaxAbstract
{
    use Paginate;
    use PatternCore\Motd\Item;
    use PatternCore\Motd\Poll;

    const TEXT_DOMAIN = 'page-motd';
    protected $repositoryMotd;

    /**
     * List all MOTD.
     */
    public function list(?int $page = 1, ?int $motdPerPage = 5, ?string $month = null): Response
    {
        global $session;

        $response    = new Response();
        $repository  = $this->getRepository();
        $params      = $this->getParams();
        $qb          = $repository->createQueryBuilder('u');
        $page        = $page ?: 1;
        $motdPerPage = $motdPerPage ?: 5;

        // Dialog title
        $title = \LotgdTranslator::t('title', [], self::TEXT_DOMAIN);

        // The dialog buttons
        $buttons = [
            [
                'title' => \LotgdTranslator::t('modal.buttons.cancel', [], 'app-default'),
                'class' => 'ui red deny button',
            ],
        ];

        // Dialog content
        try
        {
            $qb->select('u', 'c.name as motdauthorname')
                ->leftJoin(\Lotgd\Core\Entity\Accounts::class, 'a', 'with', $qb->expr()->eq('a.acctid', 'u.motdauthor'))
                ->leftJoin(\Lotgd\Core\Entity\Characters::class, 'c', 'with', $qb->expr()->eq('c.id', 'a.character'))
                ->orderBy('u.motddate', 'DESC')
            ;

            if ($month)
            {
                $params['monthSelected'] = $month;
                $month                   = \explode('-', $month);
                $qb->where('MONTH(u.motddate) = :month AND YEAR(u.motddate) = :year')
                    ->setParameters([
                        'month' => $month[1],
                        'year'  => $month[0],
                    ])
                ;
            }

            $params['paginator']            = $repository->getPaginator($qb, $page, $motdPerPage);
            $params['motdMothCountPerYear'] = $repository->getMonthCountPerYear();
            $params['motdPerPage']          = $motdPerPage;
            $session['needtoviewmotd']      = false;

            $lastMotd = $repository->getLastMotdDate();

            if ($lastMotd)
            {
                $session['user']['lastmotd'] = $lastMotd;
            }

            $content = \LotgdTheme::renderThemeTemplate('page/motd.twig', $params);
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            $response->jQuery('#motd-button')->removeClass('loading disabled');
            $response->dialog->error(\LotgdTranslator::t('list.fail', [], self::TEXT_DOMAIN));

            return $response;
        }

        //-- Options
        $options = [
            'autofocus' => false,
        ];

        // Show the dialog
        $response->dialog->show($title, ['content' => $content, 'isScrollable' => true], $buttons, $options);
        $response->jQuery('#motd-button')->removeClass('loading disabled');
        $response->jQuery('.ui.lotgd.dropdown')->dropdown();

        return $response;
    }

    /**
     * Delete a item MOTD.
     */
    public function delete(int $id): Response
    {
        $response   = new Response();
        $check      = $this->checkLoggedIn();
        $permission = checkSuPermission(SU_POST_MOTD);

        if (true !== $check || ! $permission)
        {
            //-- Cant edit if not logged in and have SU_POST_MOTD
            return $response;
        }

        try
        {
            $entity = $this->getRepository()->find($id);

            $message = \LotgdTranslator::t('item.del.not.found', [], self::TEXT_DOMAIN);
            $type    = 'warning';

            if ($entity)
            {
                \Doctrine::remove($entity);
                \Doctrine::flush();

                $message = \LotgdTranslator::t('item.del.deleted', ['id' => $id], self::TEXT_DOMAIN);
                $type    = 'success';
            }

            $response->dialog->{$type}($message);
        }
        catch (\Throwable $th)
        {
            $response->dialog->error($id);
        }

        $response->remove("motd-list-item-{$id}");

        return $response;
    }

    /**
     * Get text domain
     */
    public function getTextDomain(): string
    {
        return self::TEXT_DOMAIN;
    }

    /**
     * Get repository of Motd entity.
     */
    private function getRepository(): MotdRepository
    {
        if ( ! $this->repositoryMotd instanceof MotdRepository)
        {
            $this->repositoryMotd = \Doctrine::getRepository('LotgdCore:Motd');
        }

        return $this->repositoryMotd;
    }

    /**
     * Get default params.
     */
    private function getParams(): array
    {
        global $session;

        return [
            'textDomain'   => self::TEXT_DOMAIN,
            'SU_POST_MOTD' => ($session['user']['superuser'] & SU_POST_MOTD),
        ];
    }
}
