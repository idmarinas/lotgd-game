<?php

/**
 * Delete an account and create a backup.
 *
 * In order to create a backup and delete the data, the EntityRepository of each table needs to have the following two methods:
 *  - public function backupDeleteDataFromAccount(int $accountId): array {}
 *  - public function backupGetDataFromAccount(int $accountId): int {}
 *
 * @param int $accountId
 * @param int $type
 */
function char_cleanup($accountId, $type): bool
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.3.0; and delete in future version. Use "LotgdKernel::get(\Lotgd\Core\Tool\Backup::class)->characterCleanUp($accountId, $type);" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd.core.backup')->characterCleanUp($accountId, $type);
}
