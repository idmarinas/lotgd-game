<?php

$name = httpget('name');
$sql = 'SELECT name,level,hauntedby,acctid FROM '.DB::prefix('accounts')." WHERE login='$name'";
$result = DB::query($sql);

$select = DB::select('accounts');
$select->columns(['name', 'level', 'hauntedby', 'acctid'])
    ->where->equalTo('login', $login)
;
$result = DB::execute($select);

$twig = [
    'deathoverlord' => $deathoverlord,
    'content' => $result->current()
];

if ($result->count() > 0)
{
    $twig['found'] = true;

    if ('' == $row['hauntedby'])
    {
        $session['user']['deathpower'] -= 25;
        $roll1 = e_rand(0, $row['level']);
        $roll2 = e_rand(0, $session['user']['level']);

        if ($roll2 > $roll1)
        {
            require 'lib/systemmail.php';

            $twig['success'] = true;

            $update = DB::update('accounts');
            $update->set(['hauntedby' => $session['user']['name']])
                ->where->equalTo('login', $name)
            ;
            DB::execute($update);

            addnews('`7%s`) haunted `7%s`)!', $session['user']['name'], $row['name']);

            $subj = ['`)You have been haunted'];
            $body = ['`)You have been haunted by `&%s`).', $session['user']['name']];

            systemmail($row['acctid'], $subj, $body);
        }
        else
        {
            $twig['success'] = false;
            addnews('`7%s`) unsuccessfully haunted `7%s`)!', $session['user']['name'], $row['name']);

            switch (e_rand(0, 5))
            {
                case 0:
                    $twig['msg'] = 'Just as you were about to haunt `7%s`) good, they sneezed, and missed it completely.';
                    break;
                case 1:
                    $twig['msg'] = "You haunt `7%s`) real good like, but unfortunately they're sleeping and are completely unaware of your presence.";
                    break;
                case 2:
                    $twig['msg'] = "You're about to haunt `7%s`), but trip over your ghostly tail and land flat on your, um... face.";
                    break;
                case 3:
                    $twig['msg'] = 'You go to haunt `7%s`) in their sleep, but they look up at you, and roll over mumbling something about eating sausage just before going to bed.';
                    break;
                case 4:
                    $twig['msg'] = 'You wake `7%s`) up, who looks at you for a moment before declaring, "Neat!" and trying to catch you.';
                    break;
                case 5:
                    $twig['msg'] = 'You go to scare `7%s`), but catch a glimpse of yourself in the mirror and panic at the sight of a ghost!';
                    break;
            }
        }
    }
}
else
{
    $twig['found'] = false;
}

rawoutput($lotgd_tpl->renderThemeTemplate('pages/graveyard/haunt3.twig', $twig));

addnav(['Question `$%s`0 about the worth of your soul', $deathoverlord], 'graveyard.php?op=question');
$max = $session['user']['level'] * 5 + 50;
$favortoheal = round(10 * ($max - $session['user']['soulpoints']) / $max);
addnav(['Restore Your Soul (%s favor)', $favortoheal], 'graveyard.php?op=restore');
addnav('Places');
addnav('S?Land of the Shades', 'shades.php');
addnav('G?The Graveyard', 'graveyard.php');
addnav('M?Return to the Mausoleum', 'graveyard.php?op=enter');
