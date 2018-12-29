<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core;

class Application
{
    /**
     * Version of game in public display format.
     *
     * @var string
     */
    const VERSION = '3.1.0 IDMarinas Edition';

    /**
     * Identify version of game in numeric format.
     *
     * @var int
     */
    const VERSION_NUMBER = 30100;

    /**
     * File where the connection data to the DB is stored.
     *
     * @var string
     */
    const FILE_DB_CONNECT = 'config/autoload/local/dbconnect.php';
}
