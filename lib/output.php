<?php

//-- INIT Output Collector
$output = LotgdLocator::get(Lotgd\Core\Output\Collector::class);

/*function support without the object call */

/**
 * Block any output statements temporarily.
 *
 * @param $block should output be blocked
 *
 * @deprecated
 */
function set_block_new_output($block)
{
    global $output;
    $output->set_block_new_output($block);
}

/**
 * Raw output (unprocessed) appended to the output buffer.
 *
 * @param $indata
 *
 * @deprecated
 */
function rawoutput($indata)
{
    global $output;

    $output->rawoutput($indata);
}

/**

/**
 * Generates the appropriate output based on the LOGD coding system (ie: `b: Bold, `i: Italic).
 *
 * @param string $data The string to be output
 * @param bool   $priv Indicates if the passed string ($data) contains HTML
 *
 * @return string An output (HTML) formatted string
 */
function debug($text, $force = false)
{
    global $output;
    $output->debug($text, $force);
}

/**
 * Generates the appropriate output based on the LOGD coding system (ie: `b: Bold, `i: Italic).
 *
 * @param string $data The string to be output
 * @param bool   $priv Indicates if the passed string ($data) contains HTML
 *
 * @return string An output (HTML) formatted string
 */
function appoencode(string $data, $priv = false)
{
    global $output;

    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 4.7.0; and delete in future version, use new "LotgdFormat::colorize($string)"',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdFormat::colorize($data);
}
