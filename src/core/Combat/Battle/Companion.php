<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\Combat\Battle;

trait Companion
{
    /**
     * Enables suspended companions.
     *
     * @param string $susp  The type of suspension
     * @param string $nomsg The message to be displayed upon unsuspending. If false, no message will be displayed.
     */
    public function unSuspendCompanions($susp, $nomsg = null)
    {
        $notify = false;

        foreach ($this->companions as &$companion)
        {
            if (isset($companion['suspended']) && $companion['suspended'])
            {
                $notify                 = true;
                $companion['suspended'] = false;
            }
        }

        if ($notify && false !== $nomsg)
        {
            $nomsg = $nomsg ?: 'skill.companion.restored';

            if ($nomsg)
            {
                $this->addContextToRoundAlly($nomsg);
            }
        }
    }
}
