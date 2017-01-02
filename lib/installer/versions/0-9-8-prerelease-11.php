<?php

$accountsTable = DB::prefix('accounts');

return [
	"UPDATE $accountsTable SET specialty='' WHERE specialty='0'",
];