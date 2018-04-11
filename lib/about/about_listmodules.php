<?php
/**
 * Page displaying active modules
 *
 * This page is part of the about system
 * and displays the name, version, author
 * and download location of all the active
 * modules on the server. Modules are sorted
 * by category, and are displayed in a table.
 *
 * @copyright Copyright © 2002-2005, Eric Stevens & JT Traub, © 2006-2009, Dragonprime Development Team
 * @version Lotgd 1.1.2 DragonPrime Edition
 * @package Core
 * @subpackage Library
 * @license http://creativecommons.org/licenses/by-nc-sa/2.0/legalcode
 */
addnav('About LoGD');
addnav('About LoGD', 'about.php');
addnav('Game Setup Info', 'about.php?op=setup');
addnav('License Info', 'about.php?op=license');

$select = DB::select('modules');
$select->order('category, formalname')
    ->where->equalTo('active', 1);
$result = DB::execute($select);

rawoutput($lotgd_tpl->renderThemeTemplate('pages/about/listmodules.twig', ['result' => $result]));
