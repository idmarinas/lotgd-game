<?php

$params = [
    'showForm' => true,
    'DB_HOST' => $session['installer']['dbinfo']['DB_HOST'] ?? '',
    'DB_USER' => $session['installer']['dbinfo']['DB_USER'] ?? '',
    'DB_PASS' => $session['installer']['dbinfo']['DB_PASS'] ?? '',
    'DB_NAME' => $session['installer']['dbinfo']['DB_NAME'] ?? '',
];

if (file_exists(\Lotgd\Core\Application::FILE_DB_CONNECT))
{
    $params['showForm'] = false;
}

rawoutput(LotgdTheme::renderLotgdTemplate('core/page/installer/stage-3.twig', $params));
