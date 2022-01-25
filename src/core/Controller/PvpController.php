<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\Controller;

use DateTime;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Navigation\Navigation;
use Lotgd\Core\Pvp\Listing;
use Lotgd\Core\Pvp\Warning;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class PvpController extends AbstractController
{
    private $listing;
    private $settings;
    private $navigation;
    private $pvpWarning;

    public function __construct(Listing $listing, Settings $settings, Navigation $navigation, Warning $warning)
    {
        $this->listing    = $listing;
        $this->settings   = $settings;
        $this->navigation = $navigation;
        $this->pvpWarning = $warning;
    }

    public function index(array $params, Request $request): Response
    {
        global $session;

        $this->pvpWarning->warning();

        $pvptime = $this->settings->getSetting('pvptimeout', 600);

        $params['tpl']        = 'list';
        $params['paginator']  = $this->listing->getPvpList($session['user']['location']);
        $params['sleepers']   = $this->listing->getLocationSleepersCount($session['user']['location']);
        $params['returnLink'] = $request->getServer('REQUEST_URI');
        $params['pvpTimeOut'] = new DateTime(date('Y-m-d H:i:s', strtotime("-{$pvptime} seconds")));

        $this->navigation->addNav('common.nav.warriors', 'pvp.php');
        $this->navigation->villageNav();

        return $this->renderPvp($params);
    }

    private function renderPvp(array $params): Response
    {
        return $this->render('page/pvp.html.twig', $params);
    }
}
