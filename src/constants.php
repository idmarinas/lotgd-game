<?php

// addnews ready
// translator ready
// mail ready

//Superuser constants
\defined('SU_MEGAUSER')             || \define('SU_MEGAUSER', 1);
\defined('SU_EDIT_MOUNTS')          || \define('SU_EDIT_MOUNTS', 2);
\defined('SU_EDIT_CREATURES')       || \define('SU_EDIT_CREATURES', 4);
\defined('SU_EDIT_PETITIONS')       || \define('SU_EDIT_PETITIONS', 8);
\defined('SU_EDIT_COMMENTS')        || \define('SU_EDIT_COMMENTS', 16);
\defined('SU_EDIT_DONATIONS')       || \define('SU_EDIT_DONATIONS', 32);
\defined('SU_EDIT_USERS')           || \define('SU_EDIT_USERS', 64);
\defined('SU_EDIT_CONFIG')          || \define('SU_EDIT_CONFIG', 128);
\defined('SU_INFINITE_DAYS')        || \define('SU_INFINITE_DAYS', 256);
\defined('SU_EDIT_EQUIPMENT')       || \define('SU_EDIT_EQUIPMENT', 512);
\defined('SU_EDIT_PAYLOG')          || \define('SU_EDIT_PAYLOG', 1024);
\defined('SU_DEVELOPER')            || \define('SU_DEVELOPER', 2048);
\defined('SU_POST_MOTD')            || \define('SU_POST_MOTD', 4096);
\defined('SU_DEBUG_OUTPUT')         || \define('SU_DEBUG_OUTPUT', 8192);
\defined('SU_MODERATE_CLANS')       || \define('SU_MODERATE_CLANS', 16384);
\defined('SU_EDIT_RIDDLES')         || \define('SU_EDIT_RIDDLES', 32768);
\defined('SU_MANAGE_MODULES')       || \define('SU_MANAGE_MODULES', 65536);
\defined('SU_AUDIT_MODERATION')     || \define('SU_AUDIT_MODERATION', 131072);
\defined('SU_IS_TRANSLATOR')        || \define('SU_IS_TRANSLATOR', 262144);
\defined('SU_RAW_SQL')              || \define('SU_RAW_SQL', 524288);
\defined('SU_VIEW_SOURCE')          || \define('SU_VIEW_SOURCE', 1_048_576);
\defined('SU_NEVER_EXPIRE')         || \define('SU_NEVER_EXPIRE', 2_097_152);
\defined('SU_EDIT_ITEMS')           || \define('SU_EDIT_ITEMS', 4_194_304);
\defined('SU_GIVE_GROTTO')          || \define('SU_GIVE_GROTTO', 8_388_608);
\defined('SU_OVERRIDE_YOM_WARNING') || \define('SU_OVERRIDE_YOM_WARNING', 16_777_216);
\defined('SU_SHOW_PHPNOTICE')       || \define('SU_SHOW_PHPNOTICE', 33_554_432);
\defined('SU_IS_GAMEMASTER')        || \define('SU_IS_GAMEMASTER', 67_108_864);
\defined('SU_IS_BANMASTER')         || \define('SU_IS_BANMASTER', 134_217_728);

\defined('SU_ANYONE_CAN_SET')        || \define('SU_ANYONE_CAN_SET', SU_DEBUG_OUTPUT | SU_INFINITE_DAYS | SU_OVERRIDE_YOM_WARNING | SU_SHOW_PHPNOTICE);
\defined('SU_DOESNT_GIVE_GROTTO')    || \define('SU_DOESNT_GIVE_GROTTO', SU_DEBUG_OUTPUT | SU_INFINITE_DAYS | SU_VIEW_SOURCE | SU_NEVER_EXPIRE);
\defined('SU_HIDE_FROM_LEADERBOARD') || \define('SU_HIDE_FROM_LEADERBOARD', SU_MEGAUSER | SU_EDIT_DONATIONS | SU_EDIT_USERS | SU_EDIT_CONFIG | SU_INFINITE_DAYS | SU_DEVELOPER | SU_RAW_SQL);
\defined('NO_ACCOUNT_EXPIRATION')    || \define('NO_ACCOUNT_EXPIRATION', SU_HIDE_FROM_LEADERBOARD | SU_NEVER_EXPIRE);
//likely privs which indicate a visible admin.
\defined('SU_GIVES_YOM_WARNING') || \define('SU_GIVES_YOM_WARNING', SU_EDIT_COMMENTS | SU_EDIT_USERS | SU_EDIT_CONFIG | SU_POST_MOTD);
\defined('SU_EDIT_BANS')         || \define('SU_EDIT_BANS', SU_MEGAUSER | SU_IS_BANMASTER);

//Clan constants
//Changed for v1.1.0 Dragonprime Edition to extend clan possibilities
\defined('CLAN_APPLICANT')      || \define('CLAN_APPLICANT', 0);
\defined('CLAN_MEMBER')         || \define('CLAN_MEMBER', 10);
\defined('CLAN_OFFICER')        || \define('CLAN_OFFICER', 20);
\defined('CLAN_ADMINISTRATIVE') || \define('CLAN_ADMINISTRATIVE', 25);
\defined('CLAN_LEADER')         || \define('CLAN_LEADER', 30);
\defined('CLAN_FOUNDER')        || \define('CLAN_FOUNDER', 31);

//Location Constants
\defined('LOCATION_FIELDS') || \define('LOCATION_FIELDS', 'Degolburg');
\defined('LOCATION_INN')    || \define('LOCATION_INN', "The Boar's Head Inn");

//Gender Constants
\defined('SEX_MALE')   || \define('SEX_MALE', 0);
\defined('SEX_FEMALE') || \define('SEX_FEMALE', 1);

//Miscellaneous
\defined('INT_MAX') || \define('INT_MAX', 4_294_967_295);

\defined('RACE_UNKNOWN') || \define('RACE_UNKNOWN', 'app_unknown');

//Character Deletion Types
\defined('CHAR_DELETE_AUTO')   || \define('CHAR_DELETE_AUTO', 1);
\defined('CHAR_DELETE_MANUAL') || \define('CHAR_DELETE_MANUAL', 2);
//reserved for the future -- I don't have any plans this way currently, but it seemed appropriate to have it here.
\defined('CHAR_DELETE_PERMADEATH') || \define('CHAR_DELETE_PERMADEATH', 3);
\defined('CHAR_DELETE_SUICIDE')    || \define('CHAR_DELETE_SUICIDE', 4);

// Constants used in lib/modules - for providing more information about the
// status of the module
\defined('MODULE_NO_INFO')          || \define('MODULE_NO_INFO', 0);
\defined('MODULE_INSTALLED')        || \define('MODULE_INSTALLED', 1);
\defined('MODULE_VERSION_OK')       || \define('MODULE_VERSION_OK', 2);
\defined('MODULE_NOT_INSTALLED')    || \define('MODULE_NOT_INSTALLED', 4);
\defined('MODULE_FILE_NOT_PRESENT') || \define('MODULE_FILE_NOT_PRESENT', 8);
\defined('MODULE_VERSION_TOO_LOW')  || \define('MODULE_VERSION_TOO_LOW', 16);
\defined('MODULE_ACTIVE')           || \define('MODULE_ACTIVE', 32);
\defined('MODULE_INJECTED')         || \define('MODULE_INJECTED', 64);
