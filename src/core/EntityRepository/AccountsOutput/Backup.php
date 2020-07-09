<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\EntityRepository\AccountsOutput;

/**
 * Functions for backup data.
 */
trait Backup
{
    /**
     * Delete output of account.
     *
     * @return int
     */
    public function backupDeleteDataFromAccount(int $accountId): int
    {
        return $this->deleteOutputOfAccount($accountId);
    }
}
