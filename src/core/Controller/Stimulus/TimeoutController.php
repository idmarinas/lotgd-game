<?php

/**
 * This file is part of "LoTGD Core Package".
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 7.0.0
 */

namespace Lotgd\Core\Controller\Stimulus;

use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Controller\LotgdControllerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class TimeoutController extends AbstractController implements LotgdControllerInterface
{
    const TRANSLATION_DOMAIN = 'app_default';

    private $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function index(): Response
    {
        global $session;

        //-- Do nothing if there is no active session
        if ( ! ($session['user']['loggedin'] ?? false))
        {
            return new Response();
        }

        $timeout = $session['user']['laston']->getTimestamp() - \strtotime(\date('Y-m-d H:i:s', \strtotime('-'.$this->settings->getSetting('LOGINTIMEOUT', 900).' seconds')));

        if ($timeout >= 120)
        {
            return new Response();
        }

        return $this->render('components/timeout_notification.html.twig', [
            'translation_domain' => self::TRANSLATION_DOMAIN,
            'timeout' => $timeout
        ]);
    }

    public function allowAnonymous(): bool
    {
        return false;
    }

    public function overrideForcedNav(): bool
    {
        return true;
    }
}
