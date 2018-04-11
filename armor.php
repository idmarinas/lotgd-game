<?php
// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/http.php';
require_once 'lib/villagenav.php';

tlschema('armor');

checkday();
$tradeinvalue = round(($session['user']['armorvalue']*.75), 0);
$basetext = [
    'title' => 'Pegasus Armor',

	'desc' => [
		"`5The fair and beautiful `#Pegasus`5 greets you with a warm smile as you stroll over to her brightly colored gypsy wagon, which is placed, not out of coincidence, right next to `!MightyE`5's weapon shop.",
		'Her outfit is as brightly colored and outrageous as her wagon, and it is almost (but not quite) enough to make you look away from her huge gray eyes and flashes of skin between her not-quite-sufficient gypsy clothes.`n`n',
    ],

	'tradein' => [
		'`5You look over the various pieces of apparel, and wonder if `#Pegasus`5 would be so good as to try some of them on for you, when you realize that she is busy staring dreamily at `!MightyE`5 through the window of his shop as he, bare-chested, demonstrates the use of one of his fine wares to a customer.',
		["Noticing for a moment that you are browsing her wares, she glances at your `&%s`5 and says that she'll give you `^%s`5 for them.`0`n`n", $session['user']['armor'], $tradeinvalue],
    ],

	'nosucharmor' => "`#Pegasus`5 looks at you, confused for a second, then realizes that you've apparently taken one too many bonks on the head, and nods and smiles.",

    'tryagain' => 'Try again?',

    'notenoughgold'	=> "`5Waiting until `#Pegasus`5 looks away, you reach carefully for the `%%%s`5, which you silently remove from the stack of clothes on which it sits. Secure in your theft, you begin to turn around only to realize that your turning action is hindered by a fist closed tightly around your throat.  Glancing down, you trace the fist to the arm on which it is attached, which in turn is attached to a very muscular `!MightyE`5. You try to explain what happened here, but your throat doesn't seem to be able to open up to let your voice through, let alone essential oxygen.`n`nAs darkness creeps in on the edge of your vision, you glance pleadingly, but futilely at `%Pegasus`5 who is staring dreamily at `!MightyE`5, her hands clutched next to her face, which is painted with a large admiring smile.`n`n`n`nYou wake up some time later, having been tossed unconscious into the street.",

    'payarmor' => "`#Pegasus`5 takes your gold, and much to your surprise she also takes your `%%%s`5 and promptly puts a price on it, setting it neatly on another stack of clothes.`n`nIn return, she hands you a beautiful  new `%%%s`5.`n`nYou begin to protest, \"`@Won't I look silly wearing nothing but my `&%s`@?`5\" you ask. You ponder it a moment, and then realize that everyone else in the town is doing the same thing. \"`@Oh well, when in Rome...`5\"",
];

$schemas = [
	'title' => 'armor',
	'desc' => 'armor',
	'tradein' => 'armor',
	'nosucharmor' => 'armor',
	'tryagain' => 'armor',
	'notenoughgold' => 'armor',
	'payarmor' => 'armor',
];

$basetext['schemas'] = $schemas;

// This hook is specifically to allow modules that do other villages can have a custom armor to create ambience.
$texts = modulehook('armortext', $basetext);
$schemas = $texts['schemas'];
unset($texts['schemas']);

tlschema($schemas['title']);
page_header($texts['title']);
tlschema();

$op = httpget('op');

$data = [
    'texts' => $texts,
    'schemas' => $schemas
];

if ($op == '')
{
    $select = DB::select('armor');
    $select->order('value ASC')
        ->where->equalTo('level', DB::expression("(SELECT MAX(`level`) FROM `armor` WHERE `level` <= '{$session['user']['dragonkills']}')"));
	$data['armors'] = DB::execute($select);

    //-- Render template
    rawoutput($lotgd_tpl->renderThemeTemplate('pages/armor.twig', $data));

	villagenav();
}
elseif ($op == 'buy')
{
    $id = httpget('id');

    $select = DB::select('armor');
    $select->limit(1)
        ->where->equalTo('armorid', $id);
    $result = DB::execute($select);

    $data['armor'] = false;
    if ($result->count())
    {
        $armor = $result->current();
        $data['armor'] = modulehook('modify-armor', $armor);

        if ($data['armor']['value'] <= ($session['user']['gold'] + $tradeinvalue))
        {
            $data['purchased'] = true;
            $data['armor']['old'] = $session['user']['armor'];

            debuglog("spent " . ($data['armor']['value']-$tradeinvalue) . " gold on the " . $data['armor']['armorname'] . " armor");

			$session['user']['gold'] -= $data['armor']['value'];
			$session['user']['armor'] = $data['armor']['armorname'];
			$session['user']['gold'] += $tradeinvalue;
			$session['user']['defense'] -= $session['user']['armordef'];
			$session['user']['armordef'] = $data['armor']['defense'];
			$session['user']['defense'] += $session['user']['armordef'];
			$session['user']['armorvalue'] = $data['armor']['value'];
        }
        else
        {
            $data['purchased'] = false;
        }
    }
    else
    {
		tlschema($schemas['tryagain']);
		addnav($texts['tryagain'], 'armor.php');
		tlschema();
    }

    villagenav();

    //-- Render template
    rawoutput($lotgd_tpl->renderThemeTemplate('pages/armor/buy.twig', $data));
}

page_footer();
