<?php
/**
 * Library (supporting) functions for page output
 *		addnews ready
 *		translator ready
 *		mail ready.
 *
 * @author core_module
 * @author rewritten + adapted by IDMarinas
 */
global $html, $statbuff;

$nopopups   = [];
$runheaders = [];
$html       = ['content' => ''];

/**
 * Resets the character stats array.
 *
 * @deprecated 6.2.0 deleted in version 7.0.0. Use "LotgdKernel::get("Lotgd\Core\Character\Stats")->wipeStats()" instead.
 */
function wipe_charstats()
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 6.2.0; and delete in 7.0.0 version. Use "LotgdKernel::get("Lotgd\Core\Character\Stats")->wipeStats()" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get(Lotgd\Core\Character\Stats::class)->wipeStats();
}

/**
 * Add a attribute and/or value to the character stats display.
 *
 * @param string $label The label to use
 * @param string $value (optional) value to display
 *
 * @deprecated 6.2.0 deleted in version 7.0.0. Use "LotgdKernel::get("Lotgd\Core\Character\Stats")->addcharstat($label, $value)" instead.
 */
function addcharstat($label, $value = null)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 6.2.0; and delete in 7.0.0 version. Use "LotgdKernel::get("Lotgd\Core\Character\Stats")->addcharstat($label, $value)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get(Lotgd\Core\Character\Stats::class)->addcharstat($label, $value);
}

/**
 * Returns the character stat related to the category ($cat) and the label.
 *
 * @param string $cat   The relavent category for the stat
 * @param string $label The label of the character stat
 *
 * @return string The value associated with the stat
 *
 * @deprecated 6.2.0 deleted in version 7.0.0. Use "LotgdKernel::get("Lotgd\Core\Character\Stats")->getcharstat($cat, $label)" instead.
 */
function getcharstat($cat, $label)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 6.2.0; and delete in 7.0.0 version. Use "LotgdKernel::get("Lotgd\Core\Character\Stats")->getcharstat($cat, $label)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get(Lotgd\Core\Character\Stats::class)->getcharstat($cat, $label);
}

/**
 * Sets a value to the passed category & label for character stats.
 *
 * @param string $cat   The category for the char stat
 * @param string $label The label associated with the value
 * @param mixed  $val   The value of the attribute
 *
 * @deprecated 6.2.0 deleted in version 7.0.0. Use "LotgdKernel::get("Lotgd\Core\Character\Stats")->setcharstat($cat, $label, $val)" instead.
 */
function setcharstat($cat, $label, $val)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 6.2.0; and delete in 7.0.0 version. Use "LotgdKernel::get("Lotgd\Core\Character\Stats")->setcharstat($cat, $label, $val)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get(Lotgd\Core\Character\Stats::class)->setcharstat($cat, $label, $val);
}

/**
 * Is alias of getcharstat.
 *
 * @param string $section The character stat section
 * @param string $title   The stat display label
 *
 * @return string The value associated with the stat
 *
 * @deprecated 6.2.0 deleted in version 7.0.0. Use "LotgdKernel::get("Lotgd\Core\Character\Stats")->getcharstat($cat, $label)" instead.
 */
function getcharstat_value($section, $title)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 6.2.0; and delete in 7.0.0 version. Use "LotgdKernel::get("Lotgd\Core\Character\Stats")->getcharstat($cat, $label)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return getcharstat($section, $title);
}

$statbuff = '';
/**
 * Returns output formatted character stats.
 *
 * @param array $buffs
 *
 * @return string
 *
 * @deprecated 6.2.0 deleted in version 7.0.0. Use "LotgdKernel::get("Lotgd\Core\Service\PageParts")->getCharStats($buffs)" instead.
 */
function getcharstats($buffs)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 6.2.0; and delete in 7.0.0 version. Use "LotgdKernel::get("Lotgd\Core\Service\PageParts")->getCharStats($buffs)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get(Lotgd\Core\Service\PageParts::class)->getCharStats($buffs);
}

/**
 * Returns the current character stats or (if the character isn't logged in) the currently online players
 * Hooks provided:
 *		charstats.
 *
 * @param bool $return
 *
 * @return array The current stats for this character or the list of online players
 *
 * @deprecated 6.2.0 deleted in version 7.0.0. Use "LotgdKernel::get("Lotgd\Core\Service\PageParts")->charStats($return)" instead.
 */
function charstats($return = true)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 6.2.0; and delete in 7.0.0 version. Use "LotgdKernel::get("Lotgd\Core\Service\PageParts")->charStats($return)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get(Lotgd\Core\Service\PageParts::class)->charStats($return);
}
