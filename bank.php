<?php
// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/systemmail.php';
require_once 'lib/sanitize.php';
require_once 'lib/http.php';
require_once 'lib/villagenav.php';

tlschema('bank');

checkday();

$op = httpget('op');
$amount = abs((int) httppost('amount'));
$amt = $amount;
$amount = $amount ?: abs($session['user']['goldinbank']);
$to = (string) httppost('to');
$lefttoborrow = $amount;
$maxborrow = $session['user']['level'] * getsetting('borrowperlevel', 20);
$maxout = $session['user']['level'] * getsetting('maxtransferout', 25);

$bankername = getsetting('bankername','`@Elessa`0');

$basetext = [
    'title' => 'Ye Olde Bank',

    'enter' => [
        'desc' => [
            '`6As you approach the pair of impressive carved rock crystal doors, they part to allow you entrance into the bank.',
            'You find yourself standing in a room of exquisitely vaulted ceilings of carved stone.',
            'Light filters through tall windows in shafts of soft radiance.',
            'About you, clerks are bustling back and forth.',
            'The sounds of gold being counted can be heard, though the treasure is nowhere to be seen.`n`n',
            'You walk up to a counter of jet black marble.`n`n',
            ['%s`6, a petite woman in an immaculately tailored business dress, greets you from behind reading spectacles with polished silver frames.`n`n', $bankername],
            '`6"`5Greetings, my good lady, `6"you greet her,"`5 Might I inquire as to my balance this fine day?`6"`n`n',
            ["%s`6 blinks for a moment and then smiles, \"`@Hmm, `&%s`@, let's see.....`6\" she mutters as she scans down a page in her ledger.", $bankername, $session['user']['name']]
        ],

        'goldinbank' => [['`6"`@Aah, yes, here we are. You have `^%s gold`@ in our prestigious bank. Is there anything else I can do for you?`6"', $lotgdFormat->numeral($session['user']['goldinbank'])]],
        'debtinbank' => [['`6"`@Aah, yes, here we are. You have a `&debt`@ of `^%s gold`@ in our prestigious bank. Is there anything else I can do for you?`6"', $lotgdFormat->numeral(abs($session['user']['goldinbank']))]],
    ],

    'withdraw' => [
        'desc' => [
            'balance' => [['%s`6 scans through her ledger, "`@You have a balance of `^%s`@ gold in the bank.`6"`n', $bankername, $lotgdFormat->numeral(abs($session['user']['goldinbank']))]],
            'debt' => [['%s`6 scans through her ledger, "`@You have a `$debt`@ of `^%s`@ gold in the bank.`6"`n', $bankername, $lotgdFormat->numeral(abs($session['user']['goldinbank']))]],
        ],
        'question' => ['`6"`@How much would you like to withdraw `&%s`@?`6"`n`n', $session['user']['name']],
        'help' => '`n`iEnter 0 or nothing to withdraw it all`i',
        'button' => 'Withdraw',
        'finish' => [
            'error' => [
                '`$ERROR: Not enough gold in the bank to withdraw.`^`n`n',
                ['`6Having been informed that you have `^%s`6 gold in your account, you declare that you would like to withdraw all `^%s`6 of it.`n`n', $lotgdFormat->numeral($session['user']['goldinbank'], $lotgdFormat->numeral($amount))],
                ['%s`6 looks at you for a few moments without blinking, then advises you to take basic arithmetic.  You realize your folly and think you should try again.', $bankername]
            ],
            'success' => [['%s`6 records your withdrawal of `^%s `6gold in her ledger. "`@Thank you, `&%s`@.  You now have a balance of `^%s`@ gold in the bank and `^%s`@ gold in hand.`6"', $bankername, $lotgdFormat->numeral($amount), $session['user']['name'], $lotgdFormat->numeral(abs($session['user']['goldinbank'])), $lotgdFormat->numeral($session['user']['gold'])]],

            'borrow' => [
                'error' => [['`6Considering the `^%s`6 gold in your account, you ask to borrow `^%s`6. %s`6 peers through her ledger, runs a few calculations and then informs you that, at your level, you may only borrow up to a total of `^%s`6 gold.', $lotgdFormat->numeral($session['user']['goldinbank']), $lotgdFormat->numeral($lefttoborrow-$session['user']['goldinbank']), $bankername, $lotgdFormat->numeral($maxborrow)]],
                'success' => [['`6You withdraw your remaining `^%s`6 gold.', $lotgdFormat->numeral($session['user']['goldinbank'])]],
                'can' => [
                    'desc' => ['%s`6 records your withdrawal of `^%s `6gold in her ledger. "`@Thank you, `&%s`@. You now have a debt of `$%s`@ gold to the bank and `^%s`@ gold in hand.`6"', $bankername, $lotgdFormat->numeral($amount), $session['user']['name'], $lotgdFormat->numeral(abs($session['user']['goldinbank']), $lotgdFormat->numeral($session['user']['gold']))],
                    'didwithdraw' => ['`6Additionally, you borrow `^%s`6 gold.', $lotgdFormat->numeral($lefttoborrow)],
                    'nodidwithdraw' => ['`6You borrow `^%s`6 gold.', $lotgdFormat->numeral($lefttoborrow)],
                ],
                'cant' => [
                    'desc' => ['%s`6 looks up your account and informs you that you may only borrow up to `^%s`6 gold.', $bankername, $lotgdFormat->numeral($maxborrow)],
                    'didwithdraw' => ['`6Additionally, you ask to borrow `^%s`6 gold.', $lotgdFormat->numeral($lefttoborrow)],
                    'nodidwithdraw' => ['`6You ask to borrow `^%s`6 gold.', $lotgdFormat->numeral($lefttoborrow)],
                ]
            ]
        ]
    ],

    'borrow' => [
        'desc' => [
            'balance' => [['%s`6 scans through her ledger, "`@You have a balance of `^%s`@ gold in the bank.`6"`n', $bankername, $lotgdFormat->numeral(abs($session['user']['goldinbank']))]],
            'debt' => [['%s`6 scans through her ledger, "`@You have a `$debt`@ of `^%s`@ gold to the bank.`6"`n', $bankername, $lotgdFormat->numeral(abs($session['user']['goldinbank']))]],
        ],
        'question' => ['`6"`@How much would you like to borrow `&%s`@? At your level, you may borrow up to a total of `^%s`@ from the bank.`6"`n`n', $session['user']['name'], $lotgdFormat->numeral($maxborrow)],
        'help' => '`n(Money will be withdrawn until you have none left, the remainder will be borrowed)',
        'button' => 'Borrow',
    ],

    'deposit' => [
        'desc' => [
            'balance' => [['%s`6 says, "`@You have a balance of `^%s`@ gold in the bank.`6"`n', $bankername, $lotgdFormat->numeral(abs($session['user']['goldinbank']))]],
            'debt' => [['%s`6 says, "`@You have a `$debt`@ of `^%s`@ gold to the bank.`6"`n', $bankername, $lotgdFormat->numeral(abs($session['user']['goldinbank']))]],
            'hand' => [['`6Searching through all your pockets and pouches, you calculate that you currently have `^%s`6 gold on hand.`n`n', $lotgdFormat->numeral($session['user']['gold'])]]
        ],
        'question' => [
            'dep' => '`^Deposit how much?`0',
            'pay' => '`^Pay off how much?`0'
        ],
        'help' => '`n`iEnter 0 or nothing to deposit it all`i',
        'button' => 'Deposit',
        'finish' => [
            'notenough' => [['`$ERROR: Not enough gold in hand to deposit.`n`n`^You plunk your `&%s`^ gold on the counter and declare that you would like to deposit all `&%s`^ gold of it.`n`n%s`6 stares blandly at you for a few seconds until you become self conscious and recount your money, realizing your mistake.', $lotgdFormat->numeral($session['user']['gold']), $lotgdFormat->numeral($amount), $bankername]],
            'debt' => [['%s`6 records your deposit of `^%s `6gold in her ledger. "`@Thank you, `&%s`@.  You now have a debt of `$%s`@ gold to the bank and `^%s`@ gold in hand.`6"', $bankername, $lotgdFormat->numeral($amount), $session['user']['name'], $lotgdFormat->numeral(abs($session['user']['goldinbank'])),$lotgdFormat->numeral($session['user']['gold'])]],
            'balance' => [['%s`6 records your deposit of `^%s `6gold in her ledger. "`@Thank you, `&%s`@.  You now have a balance of `^%s`@ gold in the bank and `^%s`@ gold in hand.`6"', $bankername, $lotgdFormat->numeral($amount), $session['user']['name'], $lotgdFormat->numeral(abs($session['user']['goldinbank'])),$lotgdFormat->numeral($session['user']['gold'])]],
        ]
    ],

    'transfer' => [
        'subtitle' => '`6`bTransfer Money`b:`n',
        'desc' => [
            ["%s`6 tells you, \"`@Just so that you are fully aware of our policies, you may only transfer `^%s`@ gold per the recipient's level.", $bankername, getsetting('transferperlevel', 25)],
            ['Similarly, you may transfer no more than `^%s`@ gold total during the day.`6"`n', $maxout]
        ],
        'transferedtoday' => [['`6She scans her ledgers briefly, "`@For your knowledge, you have already transferred `^%s`@ gold today.`6"`n', $session['user']['amountouttoday']]],
        'debt' => [['%s`6 tells you that she refuses to transfer money for someone who is in debt.', $bankername]],
        'amount' => 'Transfer how much: ',
        'to' => 'To: ',
        'help' => '(partial names are ok, you will be asked to confirm the transaction before it occurs).`n',
        'button' => 'Preview Transfer',

        'confirm' => [
            'subtitle'=> '`6`bConfirm Transfer`b:`n',
            'noresults' => [["%s`6 blinks at you from behind her spectacles, \"`@I'm sorry, but I can find no one matching that name who does business with our bank!  Please try again.`6\"", $bankername]],
            'oneresult' => '`6Transfer `^%s`6 to `&%s`6.',
            'tomuchresults' => [['%s`6 looks at you disdainfully and coldly, but politely, suggests you try narrowing down the field of who you want to send money to just a little bit!`n`n', $bankername]],
            'button' => 'Complete Transfer',
        ],

        'finish' => [
            'subtitle' => '`6`bTransfer Completion`b`n',
            'error' => [['%s`6 stands up to her full, but still diminutive height and glares at you, "`@How can you transfer `^%s`@ gold when you only possess `^%s`@?`6"', $bankername, $lotgdFormat->numeral($amt), $lotgdFormat->numeral($session['user']['gold']+$session['user']['goldinbank'])]],
            'notfound' => [["%s`6 looks up from her ledger with a bit of surprise on her face, \"`@I'm terribly sorry, but I seem to have run into an accounting error, would you please try telling me what you wish to transfer again?`6\"", $bankername]],
            'sameacct' => [['%s`6 glares at you, her eyes flashing dangerously, "`@You may not transfer money to yourself!  That makes no sense!`6"', $bankername]],
            'maxout' => [["%s`6 shakes her head, \"`@I'm sorry, but I cannot complete that transfer; you are not allowed to transfer more than `^%s`@ gold total per day.`6\"", $bankername, $lotgdFormat->numeral($maxout)]],
            'maxtfer' => "%s`6 shakes her head, \"`@I'm sorry, but I cannot complete that transfer; `&%s`@ may only receive up to `^%s`@ gold per day.`6\"",
            'tomanytfer' => "%s`6 shakes her head, \"`@I'm sorry, but I cannot complete that transfer; `&%s`@ has received too many transfers today, you will have to wait until tomorrow.`6\"",
            'level' => [["%s`6 shakes her head, \"`@I'm sorry, but I cannot complete that transfer; you might want to send a worthwhile transfer, at least as much as your level.`6\"", $bankername]]
        ]
    ],
];

$schemas = [
    //-- Title for page
    'title' => 'bank',

    //-- Text for when you enter the bank
    'enter' => [
        'desc' => 'bank',
        'goldinbank' => 'bank',
        'debtinbank' => 'bank',
    ],

    //-- Text for when you withdraw money
    'withdraw' => [
        'desc' => [
            'balance' => 'bank',
            'debt' => 'bank',
        ],
        'question' => 'bank',
        'help' => 'bank',
        'button' => 'bank',
        //-- Checkout transaction complete
        'finish' => [
            'error' => 'bank',
            'success' => 'bank',
            'borrow' => [
                'error' => 'bank',
                'success' => 'bank',
                'can' => [
                    'desc' => 'bank',
                    'didwithdraw' => 'bank',
                    'nodidwithdraw' => 'bank',
                ],
                'cant' => [
                    'desc' => 'bank',
                    'didwithdraw' => 'bank',
                    'nodidwithdraw' => 'bank',
                ]
            ]
        ]
    ],

    //-- Text for when take out a Loan
    'borrow' => [
        'desc' => [
            'balance' => 'bank',
            'debt' => 'bank',
        ],
        'question' => 'bank',
        'help' => 'bank',
        'button' => 'bank',
    ],

    //-- Text for when deposit gold
    'deposit' => [
        'desc' => [
            'balance' => 'bank',
            'debt' => 'bank',
            'hand' => 'bank',
        ],
        'question' => [
            'dep' => 'bank',
            'pay' => 'bank'
        ],
        'help' => 'bank',
        'button' => 'bank',
        'finish' => [
            'notenough' => 'bank',
            'debt' => 'bank',
            'balance' => 'bank',
        ]
    ],

    //-- Text for transfer gold
    'transfer' => [
        'subtitle' => 'bank',
        'desc' => 'bank',
        'debt' => 'bank',
        'amount' => 'bank',
        'to' => 'bank',
        'help' => 'bank',
        'button' => 'bank',

        'confirm' => [
            'subtitle' => 'bank',
            'noresults' => 'bank',
            'oneresult' => 'bank',
            'tomuchresults' => 'bank',
            'button' => 'bank',
        ],

        'finish' => [
            'subtitle' => 'bank',
            'error' => 'bank',
            'notfound' => 'bank',
            'sameacct' => 'bank',
            'maxout' => 'bank',
            'maxtfer' => 'bank',
            'tomanytfer' => 'bank',
            'level' => 'bank',
            '' => 'bank',
        ]
    ]
];

$basetext['schemas'] = $schemas;

// This hook is specifically to allow modules that do other villages can have a custom bank to create ambience.
$texts = modulehook('banktext', $basetext);
$schemas = $texts['schemas'];
unset($texts['schemas']);

tlschema($schemas['title']);
page_header($texts['title']);
tlschema();

$twig = [
    'op' => $op,
    'texts' => $texts,
    'schemas' => $schemas
];

if($op == 'transfer')
{
    if ($session['user']['goldinbank'] >= 0) { $twig['cantransfer'] = true; }
    else { $twig['cantransfer'] = false; }
}
elseif($op == 'transfer2')
{
	$string = '%';
    for ($x=0; $x < strlen($to); $x++)
    {
		$string .= substr($to,$x,1).'%';
    }

    $select = DB::select('accounts');
    $select->columns(['name', 'login'])
        ->order('login DESC, name DESC, login')
        ->where->like('name', $string)
            ->equalTo('locked', 0);
    $result = DB::execute($select);

    $twig['amt'] = $amt;

    if ($result->count() == 0) { $twig['found'] = false; }
    elseif ($result->count() == 1)
    {
        $twig['found'] = true;
        $row = $result->current();
        $twig['row']['login'] = HTMLEntities($row['login'], ENT_COMPAT, getsetting('charset', 'UTF-8'));
        $twig['row']['name'] = $row['name'];
    }
    //-- Too much results
    elseif ($result->count() > 100) {  $twig['found'] = $result->count(); }
    //-- Format array for create a select options with 2 to 100 results
    else
    {
        $twig['found'] = $result->count();
        $twig['select'] = [];

        while($row = DB::fetch_assoc($result))
        {
            $twig['select'][HTMLEntities($row['login'], ENT_COMPAT, getsetting('charset', 'UTF-8'))] = full_sanitize($row['name']);
        }
    }
}
elseif($op == 'transfer3')
{
    if (($session['user']['gold'] + $session['user']['goldinbank']) >= $amt)
    {
        $select = DB::select('accounts');
        $select->columns(['name', 'acctid', 'level', 'transferredtoday'])
            ->where->equalTo('login', $to);
        $result = DB::execute($select);

        if ($result->count() == 1)
        {
            $row = $result->current();
            $twig['transfered'] = 0;

            $maxtfer = $row['level'] * getsetting('transferperlevel', 25);

            if (($session['user']['amountouttoday'] + $amt) > $maxout) { $twig['transfered'] = 'maxout'; }
            else if ($maxtfer < $amt)
            {
                $twig['bankername'] = $bankername;
                $twig['name'] = $row['name'];
                $twig['maxtfer'] = $maxtfer;
                $twig['transfered'] = 'maxtfer';

                if (is_string($basetext['transfer']['finish']['maxtfer']))
                {
                    $basetext['transfer']['finish']['maxtfer'] = [[$basetext['transfer']['finish']['maxtfer'], $bankername, $row['name'], $maxtfer]];
                }
            }
            else if($row['transferredtoday'] >= getsetting('transferreceive', 3))
            {
                $twig['bankername'] = $bankername;
                $twig['name'] = $row['name'];
                $twig['transfered'] = 'tomanytfer';

                if (is_string($basetext['transfer']['finish']['tomanytfer']))
                {
                    $basetext['transfer']['finish']['tomanytfer'] = [[$basetext['transfer']['finish']['tomanytfer'], $bankername, $row['name']]];
                }
            }
            else if($amt < (int) $session['user']['level']) { $twig['transfered'] = 'level'; }
            else if($row['acctid'] == $session['user']['acctid']) { $twig['transfered'] = 'sameacct'; }
            else
            {
                $twig['transfered'] = true;
				debuglog("transferred $amt gold to", $row['acctid']);
				$session['user']['gold']-=$amt;
                if ($session['user']['gold']<0)
                {
					//withdraw in case they don't have enough on hand.
					$session['user']['goldinbank'] += $session['user']['gold'];
					$session['user']['gold'] = 0;
				}
				$session['user']['amountouttoday']+= $amt;
				$sql = "UPDATE ". DB::prefix("accounts") . " SET goldinbank=goldinbank+$amt,transferredtoday=transferredtoday+1 WHERE acctid='{$row['acctid']}'";
				DB::query($sql);
				output("`@Elessa`6 smiles, \"`@The transfer has been completed!`6\"");
				$subj = array("`^You have received a money transfer!`0");
				$body = array("`&%s`6 has transferred `^%s`6 gold to your bank account!",$session['user']['name'],$amt);
				systemmail($row['acctid'],$subj,$body);
			}
        }
        else
        {
            $twig['transfered'] = 0;
		}
    }
    else
    {
        $twig['transfered'] = false;
	}
}
elseif($op == 'depositfinish')
{
    if ($amount > $session['user']['gold'])
    {
        $twig['deposited'] = false;
    }
    else
    {
        $twig['deposited'] = true;
		$session['user']['goldinbank'] += $amount;
        $session['user']['gold'] -= $amount;
		debuglog("deposited " . $amount . " gold in the bank");
	}
}
elseif($op == 'withdrawfinish')
{
    if ($amount > $session['user']['goldinbank'] && httppost('borrow') == '')
    {
        $twig['withdrawal'] = false;
    }
    else if($amount > $session['user']['goldinbank'])
    {
        $didwithdraw = 0;
        $twig['didwithdraw'] = $didwithdraw;

        if ($lefttoborrow <= ($session['user']['goldinbank'] + $maxborrow))
        {
            if ($session['user']['goldinbank'] > 0)
            {
				$lefttoborrow -= $session['user']['goldinbank'];
				$session['user']['gold'] += $session['user']['goldinbank'];
				$session['user']['goldinbank'] = 0;
                $didwithdraw = 1;
                $twig['didwithdraw'] = $didwithdraw;

				debuglog("withdrew $amount gold from the bank");
            }

            if (($lefttoborrow - $session['user']['goldinbank']) > $maxborrow)
            {
                $twig['borrow'] = false;
            }
            else
            {
                $twig['borrow'] = true;
				$session['user']['goldinbank'] -= $lefttoborrow;
                $session['user']['gold'] += $lefttoborrow;

				debuglog("borrows $lefttoborrow gold from the bank");
			}
        }
        else
        {
            $twig['cantborrow'] = false;
		}
    }
    else
    {
        $twig['withdrawal'] = true;
		$session['user']['goldinbank'] -= $amount;
        $session['user']['gold'] += $amount;

		debuglog("withdrew $amount gold from the bank");
	}
}

rawoutput($lotgd_tpl->renderThemeTemplate('pages/bank.twig', $twig));

addnav('Navigation');
villagenav();

addnav('Money');
addnav('Money balance', 'bank.php');
if ($session['user']['goldinbank'] >= 0)
{
	addnav('W?Withdraw', 'bank.php?op=withdraw');
	addnav('D?Deposit', 'bank.php?op=deposit');
	if (getsetting('borrowperlevel', 20)) { addnav('L?Take out a Loan', 'bank.php?op=borrow'); }
}
else
{
	addnav('D?Pay off Debt', 'bank.php?op=deposit');
	if (getsetting('borrowperlevel', 20)) { addnav('L?Borrow More', 'bank.php?op=borrow'); }
}
if (getsetting('allowgoldtransfer', 1))
{
    if ($session['user']['level'] >= getsetting('mintransferlev', 3) || $session['user']['dragonkills'] > 0)
    {
		addnav('M?Transfer Money', 'bank.php?op=transfer');
	}
}

page_footer();
