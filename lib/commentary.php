<?php

// translator ready
// addnews ready
// mail ready
require_once 'lib/datetime.php';
require_once 'lib/sanitize.php';

/**
 * All comentary sections.
 *
 * @return array
 */
function commentarylocs()
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0 and not do nothing; and delete in version 4.1.0, use new commentary system.',
        __METHOD__
    ), E_USER_DEPRECATED);
}

/**
 * Hidde a commentary.
 *
 * @param int    $cid
 * @param string $reason
 * @param int    $mod
 */
function removecommentary()
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0 and not do nothing; and delete in version 4.1.0, use new commentary system.',
        __METHOD__
    ), E_USER_DEPRECATED);
}

/**
 * Restore a hidden commentary.
 *
 * @param [type] $cid
 * @param [type] $reason
 * @param [type] $mod
 */
function restorecommentary()
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0 and not do nothing; and delete in version 4.1.0, use new commentary system.',
        __METHOD__
    ), E_USER_DEPRECATED);
}

/**
 * Clean commentary for insert in DB.
 *
 * @param string $comment
 *
 * @return string
 */
function commentcleanup()
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0 and not do nothing; and delete in version 4.1.0, use new commentary system.',
        __METHOD__
    ), E_USER_DEPRECATED);
}

/**
 * Add a comment.
 *
 * @return void|false
 */
function addcommentary()
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0 and not do nothing; and delete in version 4.1.0, use new commentary system.',
        __METHOD__
    ), E_USER_DEPRECATED);
}

/**
 * Add a new comment.
 *
 * @param string $section
 * @param string $talkline
 * @param string $comment
 */
function injectcommentary()
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0 and not do nothing; and delete in version 4.1.0, use new commentary system.',
        __METHOD__
    ), E_USER_DEPRECATED);
}

/**
 * Lets system put comments without a user association...be careful, it is not trackable who posted it.
 *
 * @param string $section
 * @param string $comment
 *
 * @return injectrawcomment
 */
function injectsystemcomment()
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0 and not do nothing; and delete in version 4.1.0, use new commentary system.',
        __METHOD__
    ), E_USER_DEPRECATED);
}

/**
 * Lets gamemasters put raw comments.
 *
 * @param string $section
 * @param int    $author
 * @param string $comment
 * @param string $name
 * @param array  $info
 */
function injectrawcomment()
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0 and not do nothing; and delete in version 4.1.0, use new commentary system.',
        __METHOD__
    ), E_USER_DEPRECATED);
}

/**
 * Display comentary block.
 *
 * @param string $intro
 * @param string $section
 * @param string $message
 * @param int    $limit
 * @param string $talkline
 * @param bool   $schema
 */
function commentdisplay()
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0 and not do nothing; and delete in version 4.1.0, use new commentary system.',
        __METHOD__
    ), E_USER_DEPRECATED);
}

/**
 * View all comments.
 *
 * @param string       $section
 * @param string       $message
 * @param int          $limit
 * @param string       $talkline
 * @param bool         $schema
 * @param bool         $skipfooter
 * @param false|string $customsql
 * @param bool         $skiprecentupdate
 * @param bool         $overridemod
 */
function viewcommentary()
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0 and not do nothing; and delete in version 4.1.0, use new commentary system.',
        __METHOD__
    ), E_USER_DEPRECATED);
}

/**
 * Prepare block of commentaries.
 *
 * @param [type] $section
 * @param string $message
 * @param int    $limit
 * @param string $talkline
 * @param bool   $schema
 * @param bool   $skipfooter
 * @param bool   $customsql
 * @param bool   $skiprecentupdate
 * @param bool   $overridemod
 * @param bool   $returnlink
 */
function preparecommentaryblock()
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0 and not do nothing; and delete in version 4.1.0, use new commentary system.',
        __METHOD__
    ), E_USER_DEPRECATED);
}

/**
 * Get comments.
 *
 * @param string       $section
 * @param int          $limit
 * @param string       $talkline
 * @param false|string $customsql
 * @param bool         $showmodlink
 * @param bool         $returnlink
 */
function getcommentary()
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0 and not do nothing; and delete in version 4.1.0, use new commentary system.',
        __METHOD__
    ), E_USER_DEPRECATED);
}

/**
 * Create a line of comment.
 *
 * @param array $line
 */
function preparecommentaryline()
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0 and not do nothing; and delete in version 4.1.0, use new commentary system.',
        __METHOD__
    ), E_USER_DEPRECATED);
}

/**
 * Footer of list of pages of comments.
 *
 * @param string $section
 * @param string $message
 * @param int    $limit
 * @param string $talkline
 * @param bool   $schema
 */
function commentaryfooter()
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0 and not do nothing; and delete in version 4.1.0, use new commentary system.',
        __METHOD__
    ), E_USER_DEPRECATED);
}

/**
 * Create a link for comment.
 *
 * @param string $append
 * @param bool   $returnlink
 *
 * @return string
 */
function buildcommentarylink()
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0 and not do nothing; and delete in version 4.1.0, use new commentary system.',
        __METHOD__
    ), E_USER_DEPRECATED);
}

/**
 * Form for talk in section.
 *
 * @param string $section
 * @param string $talkline
 * @param int    $limit
 * @param bool   $schema
 */
function talkform()
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0 and not do nothing; and delete in version 4.1.0, use new commentary system.',
        __METHOD__
    ), E_USER_DEPRECATED);
}
