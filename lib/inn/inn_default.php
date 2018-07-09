<?php

if ('' == $com && ! $comment && 'fleedragon' != $op)
{
    if (0 != module_events('inn', getsetting('innchance', 0)))
    {
        if (checknavs())
        {
            page_footer();
        }
        else
        {
            $skipinndesc = true;
            $session['user']['specialinc'] = '';
            $session['user']['specialmisc'] = '';
            $op = '';
            httpset('op', '');
        }
    }
}

addnav('Things to do');
$args = modulehook('blockcommentarea', ['section' => 'inn']);

if (! isset($args['block']) || 'yes' != $args['block'])
{
    addnav('Converse with patrons', 'inn.php?op=converse');
}
addnav(['B?Talk to %s`0 the Barkeep', $barkeep], 'inn.php?op=bartender');

addnav('Other');
addnav('Get a room (log out)', 'inn.php?op=room');

if (! $skipinndesc)
{
    $twig = [
        'op' => $op,
        'partner' => $partner,
        'barkeep' => $barkeep
    ];

    if ('fleedragon' == $op)
    {
        if ($session['user']['charm'] > 0)
        {
            $session['user']['charm']--;
        }
    }

    $chats = [
        translate_inline('dragons'),
        getsetting('bard', '`^Seth`0'),
        getsetting('barmaid', '`%Violet`0'),
        '`#MightyE`0',
        translate_inline('fine drinks'),
        $partner,
    ];
    $chats = modulehook('innchatter', $chats);
    $talk = $chats[array_rand($chats, 1)];

    $twig['talk'] = $talk;

    rawoutput($lotgd_tpl->renderThemeTemplate('pages/inn.twig', $twig));
    modulehook('inn-desc', []);
}

modulehook('inn', []);
module_display_events('inn', 'inn.php');
