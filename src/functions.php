<?php

require_once __DIR__.'/functions_old.php';

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.10.0
 */
if ( ! \function_exists('e_rand'))
{
    /**
     * Alias of mt_rand with some improvements.
     *
     * @param int|float $min
     * @param int|float $max
     */
    function e_rand($min = null, $max = null): int
    {
        if ( ! \is_numeric($min))
        {
            return \mt_rand();
        }
        $min = \round($min);

        if ( ! \is_numeric($max))
        {
            return $min;
        }
        $max = \round($max);

        return \mt_rand(\min($min, $max), \max($min, $max));
    }
}

if ( ! \function_exists('r_rand'))
{
    /**
     * Same as e_rand but $min and $max are multiplied by 1000.
     *
     * @param int|float $min
     * @param int|float $max
     *
     * @return int|float
     */
    function r_rand($min = null, $max = null)
    {
        if ( ! \is_numeric($min))
        {
            return \mt_rand();
        }
        $min *= 1000;

        if ( ! \is_numeric($max))
        {
            return \mt_rand($min) / 1000;
        }
        $max *= 1000;

        return \mt_rand(\min($min, $max), \max($min, $max)) / 1000;
    }
}

if ( ! \function_exists('is_email'))
{
    /**
     * Check if given email is valid.
     *
     * @param string $email
     *
     * @deprecated 6.1.0 delete in version 7.0.0. Use symfony validator service
     *
     * @return bool
     */
    function is_email($email)
    {
        \trigger_error(\sprintf(
            'Usage of %s is obsolete since 6.1.0; and delete in 7.0.0 version. Use service LotgdKernel::get("lotgd_core.tool.validator")->isMail(string) instead.',
            __METHOD__
        ), E_USER_DEPRECATED);

        $validator = new Laminas\Validator\EmailAddress();

        return $validator->isValid($email);
    }
}

if ( ! \function_exists('arraytourl'))
{
    /**
     * Turns an array into an URL argument string.
     *
     * Takes an array and encodes it in key=val&key=val form.
     * Does not add starting ?
     *
     * @param array $array The array to turn into an URL
     *
     * @deprecated 6.1.0 deleted in version 7.0.0. Use http_build_query
     *
     * @return string The URL
     */
    function arraytourl($array)
    {
        \trigger_error(\sprintf(
            'Usage of %s is obsolete since 6.1.0; and delete in 7.0.0 version. Use php function "http_build_query" instead.',
            __METHOD__
        ), E_USER_DEPRECATED);

        //takes an array and encodes it in key=val&key=val form.
        $url = '';
        $i   = 0;

        foreach ($array as $key => $val)
        {
            if ($i > 0)
            {
                $url .= '&';
            }
            ++$i;
            $url .= \rawurlencode($key).'='.\rawurlencode($val);
        }

        return $url;
    }
}

if ( ! \function_exists('urltoarray'))
{
    /**
     * Takes an array and returns its arguments in an array.
     *
     * @param string $url The URL
     *
     * @deprecated 6.1.0 deleted in version 7.0.0. Use "parse_str"
     *
     * @return array The arguments from the URL
     */
    function urltoarray($url)
    {
        \trigger_error(\sprintf(
            'Usage of %s is obsolete since 6.1.0; and delete in 7.0.0 version. Use php function "parse_str" instead.',
            __METHOD__
        ), E_USER_DEPRECATED);

        //takes a URL and returns its arguments in array form.
        if (false !== \strpos($url, '?'))
        {
            $url = \substr($url, \strpos($url, '?') + 1);
        }
        $a     = \explode('&', $url);
        $array = [];

        foreach ($a as $val)
        {
            $b                        = \explode('=', $val);
            $array[\urldecode($b[0])] = \urldecode($b[1]);
        }

        return $array;
    }
}

if ( ! \function_exists('createstring'))
{
    /**
     * Turns the given parameter into a string.
     *
     * If the given parameter is an array or object,
     * it is serialized, and the serialized string is
     * return.
     *
     * Otherwise, the parameter is cast as a string
     * and returned.
     *
     * @param mixed $array
     *
     * @deprecated 6.1.0 deleted in version 7.0.0. Use "serialize".
     *
     * @return string The parameter converted to a string
     */
    function createstring($array)
    {
        \trigger_error(\sprintf(
            'Usage of %s is obsolete since 6.1.0; and delete in 7.0.0 version. Use php function "serialize" instead.',
            __METHOD__
        ), E_USER_DEPRECATED);

        return \is_array($array) || \is_object($array) ? \serialize($array) : (string) $array;
    }
}

if ( ! \function_exists('myDefine'))
{
    function myDefine($name, $value)
    {
        //-- No try to define a defined constant
        if ( ! \defined($name))
        {
            \define($name, $value);
        }
    }
}

if ( ! \function_exists('list_files'))
{
    /** @deprecated 6.1.0 deleted in version 7.0.0. Use Symfony Component Finder */
    function list_files($ruta, $sort)
    {
        \trigger_error(\sprintf(
            'Usage of %s is obsolete since 6.1.0; and delete in 7.0.0 version. Use php "Symfony Component Finder" instead.',
            __METHOD__
        ), E_USER_DEPRECATED);

        //abrir un directorio y listarlo recursivo
        if (\is_dir($ruta) && $dh = \opendir($ruta))
        {
            while (false !== ($file = \readdir($dh)))
            {
                if (\is_dir($ruta.'/'.$file) && '.' != $file && '..' != $file)
                {
                    $sort = list_files($ruta.'/'.$file, $sort);
                }
                else
                {
                    $names = \explode('.', $file);

                    if (isset($names[1]) && 'php' == $names[1])
                    {
                        //sorting
                        $sort[] = ','.$ruta.'/'.$names[0].','.$ruta.'/'.$names[0];
                    }
                }
            }
            \closedir($dh);
        }

        return $sort;
    }
}

if ( ! \function_exists('_curl'))
{
    /** @deprecated 6.1.0 deleted in version 7.0.0. Use service LotgdKernel::get('http_client') instead */
    function _curl($url)
    {
        \trigger_error(\sprintf(
            'Usage of %s is obsolete since 6.1.0; and delete in 7.0.0 version. Use service LotgdKernel::get("http_client") instead.',
            __METHOD__
        ), E_USER_DEPRECATED);

        $ch = \curl_init();

        if ( ! $ch)
        {
            return false;
        }

        // set URL and other appropriate options
        \curl_setopt($ch, CURLOPT_URL, $url);
        \curl_setopt($ch, CURLOPT_HEADER, 0);
        \curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        \curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        \curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        // grab URL and pass it to the browser
        $ret = \curl_exec($ch);

        // close curl resource, and free up system resources
        \curl_close($ch);

        $val   = \explode("\n", $ret);
        $total = \count($val);
        $cur   = 0;

        foreach ($val as $a)
        {
            ++$cur;
            $done[] = $a.($cur != $total ? "\n" : '');
        }

        return $done;
    }
}

if ( ! \function_exists('_sock'))
{
    /** @deprecated 6.1.0 deleted in version 7.0.0. Use service LotgdKernel::get('http_client') instead */
    function _sock($url)
    {
        \trigger_error(\sprintf(
            'Usage of %s is obsolete since 6.1.0; and delete in 7.0.0 version. Use service LotgdKernel::get("http_client") instead.',
            __METHOD__
        ), E_USER_DEPRECATED);

        $a = \preg_match('!http://([^/:]+)(:\d+)?(/.*)!', $url, $matches);

        if ( ! $a)
        {
            return false;
        }

        $host = $matches[1];
        $port = (int) $matches[2];

        if (0 == $port)
        {
            $port = 80;
        }
        $path = $matches[3];

        $f = @\fsockopen($host, $port, $errno, $errstr, 1);

        if ( ! $f)
        {
            return false;
        }

        if (\function_exists('stream_set_timeout'))
        {
            \stream_set_timeout($f, 1);
        }

        $out = "GET {$path} HTTP/1.1\r\n";
        $out .= "Host: {$host}\r\n";
        $out .= "Connection: Close\r\n\r\n";

        \fwrite($f, $out);
        $skip = 1;
        $done = [];

        while ( ! \feof($f))
        {
            $buf = \fgets($f, 8192);

            if ("\r\n" == $buf && $skip)
            {
                $skip = 0;

                continue;
            }

            if ( ! $skip)
            {
                $done[] = $buf;
            }
        }
        $info = \stream_get_meta_data($f);
        \fclose($f);

        if ($info['timed_out'])
        {
            \LotgdResponse::pageDebug("Call to {$url} timed out!");
            $done = false;
        }

        return $done;
    }
}

if ( ! \function_exists('pullurl'))
{
    /** @deprecated 6.1.0 deleted in version 7.0.0. Use service LotgdKernel::get('http_client') instead */
    function pullurl($url)
    {
        \trigger_error(\sprintf(
            'Usage of %s is obsolete since 6.1.0; and delete in 7.0.0 version. Use service LotgdKernel::get("http_client") instead.',
            __METHOD__
        ), E_USER_DEPRECATED);

        //if (function_exists("curl_init")) return _curl($url);
        // For some reason the socket code isn't working
        //if (function_exists("fsockopen")) return _sock($url);
        return @\file($url);
    }
}

if ( ! \function_exists('safeescape'))
{
    function safeescape($input)
    {
        $prevchar = '';
        $out      = '';

        for ($x = 0; $x < \strlen($input); ++$x)
        {
            $char = \substr($input, $x, 1);

            if (("'" == $char || '"' == $char) && '\\' != $prevchar)
            {
                $char = "\\{$char}";
            }
            $out .= $char;
            $prevchar = $char;
        }

        return $out;
    }
}

if ( ! \function_exists('nltoappon'))
{
    function nltoappon($in)
    {
        $out = \str_replace("\r\n", "\n", $in);
        $out = \str_replace("\r", "\n", $out);

        return \str_replace("\n", '`n', $out);
    }
}
