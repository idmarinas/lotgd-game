<?php

/**
 * This trait contain all function to manage prefix of tables.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Lib\Pattern;

trait Prefix
{
    protected $prefix = '';

    /**
     * Prefix for tables.
     *
     * @param string|array $tablename Name of table
     * @param false|string $force     If you want to force a prefix
     *
     * @return string|array
     */
    public function prefix($tablename, $force = false)
    {
        $prefixNew = $force;

        if (false === $force)
        {
            // The following file should be used to override or modify the
            // specialPrefixes array to be correct for your site.  Do NOT
            // do this unles you know EXACTLY what this means to you, your
            // game, your county, your state, your nation, your planet and
            // your universe!
            // Example: you change name of a table
            if (file_exists('prefixes.php'))
            {
                $specialPrefixes = include_once 'prefixes.php';
            }

            $prefixNew = $this->getPrefix();

            if (isset($specialPrefixes[$tablename]))
            {
                $prefixNew = $specialPrefixes[$tablename];
            }
        }

        if (is_array($tablename))
        {
            list($key, $value) = each($tablename);

            return [$key => $prefixNew.$value];
        }

        return $prefixNew.$tablename;
    }

    /**
     * Get the value of prefix.
     *
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * Set the value of prefix.
     *
     * @param string $prefix
     *
     * @return self
     */
    public function setPrefix(string $prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }
}
