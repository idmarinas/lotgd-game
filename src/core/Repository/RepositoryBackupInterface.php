<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.md
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\Repository;

interface RepositoryBackupInterface
{
    /**
     * Get all rows of table to backup.
     */
    public function backupGetDataFromAccount(int $accountId): array;

    /**
     * Delete all rows of table when backup is completed.
     *
     * @return int Number of rows deleted
     */
    public function backupDeleteDataFromAccount(int $accountId): int;
}
