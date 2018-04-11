<?php

class ServerFunctions
{
	static public function isTheServerFull()
	{
        if (abs(getsetting('OnlineCountLast', 0) - strtotime('now')) > 60)
        {
            $select = DB::select('accounts');
            $select->columns(['counter' => DB::expression('COUNT(1)')])
                ->where->equalTo('locked', 0)
                    ->equalTo('loggedin', 1)
                    ->greaterThan('laston', date('Y-m-d H:i:s', strtotime('-' . getsetting('LOGINTIMEOUT', 900) . ' seconds')));
            $result = DB::execute($select)->current();

			savesetting('OnlineCount', $result['counter']);
			savesetting('OnlineCountLast', strtotime('now'));
        }
        else
        {
			$onlinecount = getsetting('OnlineCount', 0);
        }

        if ($onlinecount >= getsetting('maxonline', 0) && getsetting('maxonline', 0) != 0) return true;

		return false;
	}

	static public function resetAllDragonkillPoints($acctid = false)
	{
        $select = DB::select('accounts');
        $select->columns(['acctid', 'dragonpoints'])
            ->where->notEqualTo('dragonpoints', '');//-- Not is necesary all accounts

        if (is_numeric($acctid)) { $select->where->equalTo('acctid', $acctid); }
        else if (is_array($acctid)) { $select->where->in('acctid', $acctid); }

		$result = DB::execute($select);
		//this is ugly, but fortunately only needed out of the ordinary
        while($row = DB::fetch_assoc($result))
        {
            $dkpoints = $row['dragonpoints'];

            $dkpoints = unserialize(stripslashes($dkpoints));

            if (empty($dkpoints)) { continue; } //-- Not do nothing if is an empty array

			$distribution = array_count_values($dkpoints);
			$sets = [];
            foreach ($distribution as $key => $val)
            {
                switch ($key)
                {
					case 'str':
						$recalc = (int )$val;
						$sets['strength'] = DB::expression("strength-$recalc");
						break;
					case 'con':
						$recalc = (int) $val;
						$sets['constitution'] = DB::expression("constitution-$recalc");
						break;
					case 'int':
						$recalc = (int) $val;
						$sets['intelligence'] = DB::expression("intelligence-$recalc");
						break;
					case 'wis':
						$recalc = (int) $val;
						$sets['wisdom'] = DB::expression("wisdom-$recalc");
						break;
					case 'dex':
						$recalc = (int) $val;
						$sets['dexterity'] = DB::expression("dexterity-$recalc");
						break;
					case 'hp':
						$recalc = (int) ($val * 5);
						$sets['maxhitpoints'] = DB::expression("maxhitpoints-$recalc");
						$sets['hitpoints'] = DB::expression("hitpoints-$recalc");
						break;
					case 'at':
						$recalc = (int) $val;
						$sets['attack'] = DB::expression("attack-$recalc");
						break;
					case 'de':
						$recalc = (int) $val;
						$sets['defense'] = DB::expression("defense-$recalc");
						break;
				}
			}

            $sets['dragonpoints'] = '';
            $update = DB::update('accounts');
            $update->set($sets)
                ->where->equalTo('acctid', $row['acctid']);

            DB::execute($update);

			//adding a hook, nasty, but you don't call this too often
			modulehook('dragonpointreset', [$row]);
		}
	}
}
