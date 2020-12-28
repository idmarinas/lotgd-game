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

use Lotgd\Core\Pattern as PatternCore;
use Twig\TwigFunction;

class Donation extends AbstractExtension
{
    use Pattern\AttributesString;
    use Pattern\Navigation;
    use PatternCore\Http;
    use PatternCore\Template;
    use PatternCore\Translator;

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('donation_buttons', [$this, 'display']),
        ];
    }

    public function display()
    {
        global $session;

        $request = $this->getHttpRequest();

        $params = [
            'item_number' => \htmlentities($session['user']['login'], ENT_COMPAT, getsetting('charset', 'UTF-8')).':'.$request->getServer('HTTP_HOST').'/'.$request->getServer('REQUEST_URI'),
            'notify_url'  => '//'.$request->getServer('HTTP_HOST').\dirname($request->getServer('REQUEST_URI')).'/payment.php',
        ];

        return $this->getTemplate()->render('@core/paypal.html.twig', $params);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'paypal';
    }
}
