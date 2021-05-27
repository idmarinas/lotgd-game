


<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.3.0
 */

namespace Lotgd\Core;

/**
 * Events available in LoTGD Core. See also events in 'src/core/Event/'.
 *
 * This events recibe a instance of Symfony\Component\EventDispatcher\GenericEvent.
 */
class Events
{
    /**
     * Payment events
     */
    public const PAYMENT_DONATION_SUCCESS = 'lotgd.payment.donation.success';
    public const PAYMENT_DONATION_ERROR = 'lotgd.payment.donation.error';
    public const PAYMENT_DONATION_ADJUSTMENT = 'lotgd.payment.donation.adjustment';
}
