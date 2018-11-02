<?php

/**
 * Blocks a module from being able to hook for the rest of the page hit.
 * Please note, any hooks already executed by the blocked module will
 * not be undone, so this function is pretty flaky all around.
 *
 * The only way to use this safely would be to block/unblock modules from
 * the everyhit hook and make sure to shortcircuit for any page other than
 * the one you care about.
 *
 * @param mixed $modulename the name of the module you wish to block or true if you want to block all modules
 */
$block_all_modules = false;
$blocked_modules = [];
function blockmodule($modulename)
{
    global $blocked_modules, $block_all_modules, $currenthook;

    if (true === $modulename)
    {
        $block_all_modules = true;

        return;
    }
    $blocked_modules[$modulename] = 1;
}

/**
 * Unblocks a module from being able to hook for the rest of the page hit.
 * Please note, any hooks already blocked for the module being unblocked
 * have been lost, so this function is pretty flaky all around.
 *
 * The only way to use this safely would be to block/unblock modules from
 * the everyhit hook and make sure to shortcircuit for any page other than
 * the one you care about.
 *
 * @param mixed $modulename the name of the module you wish to unblock or true if you want to unblock all modules
 */
$unblocked_modules = [];
function unblockmodule($modulename)
{
    global $unblocked_modules, $block_all_modules;

    if (true === $modulename)
    {
        $block_all_modules = false;

        return;
    }
    $unblocked_modules[$modulename] = 1;
}
