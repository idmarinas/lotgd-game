<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.5.0
 */

namespace Lotgd\Core\Twig\Extension;

use Lotgd\Core\Http\Request;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Donation extends AbstractExtension
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('donation_buttons', [$this, 'display'], ['needs_environment' => true]),
        ];
    }

    public function display(Environment $env)
    {
        global $session;

        $params = [
            'item_number' => \htmlentities($session['user']['login'], ENT_COMPAT, getsetting('charset', 'UTF-8')).':'.$this->request->getServer('HTTP_HOST').'/'.$this->request->getServer('REQUEST_URI'),
            'notify_url'  => '//'.$this->request->getServer('HTTP_HOST').\dirname($this->request->getServer('REQUEST_URI')).'/payment.php',
        ];

        return $env->render('@core/paypal.html.twig', $params);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'paypal';
    }
}
