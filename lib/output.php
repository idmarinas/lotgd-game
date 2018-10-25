<?php

//-- INIT Output Collector
$output = LotgdLocator::get(Lotgd\Core\Output\Collector::class);

/*function support without the object call */

/**
 * Block any output statements temporarily.
 *
 * @param $block should output be blocked
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
 */
function rawoutput($indata)
{
    global $output;

    $output->rawoutput($indata);
}

/**
 * Handles color and style encoding, and appends to the output buffer ($output).
 *
 * @param $indata If an array is passed then the format for sprintf is assumed otherwise a simple string is assumed
 *
 * @see sprintf, apponencode
 */
function output_notl($indata)
{
    global $output;
    $args = func_get_args();
    call_user_func_array([$output, 'output_notl'], $args);
}

/**
 * Outputs a translated, color/style encoded string to the browser.
 *
 * @param What to output. If an array is passed then the format used by sprintf is assumed
 *
 * @see output_notl
 */
function output()
{
    global $output;

    $args = func_get_args();
    call_user_func_array([$output, 'output'], $args);
}

/**
 * Generate debug output for players who have the SU_DEBUG_OUTPUT flag set in the superuser mask.
 *
 * @param $text The string to output
 * @param  $force If true, force debug output even for non SU/non flagged
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
function appoencode($data, $priv = false)
{
    global $output;

    return $output->appoencode($data, $priv);
}
