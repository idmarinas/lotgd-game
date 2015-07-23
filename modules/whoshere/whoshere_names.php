<?php
	// No matter what village they enter the grotto/clanhalls/gardens from, show their names.
	$where = ( $SCRIPT_NAME == 'superuser.php' || $SCRIPT_NAME == 'clan.php' || $SCRIPT_NAME == 'gardens.php' ) ? '' : "a.location = '" . addslashes($session['user']['location']) . "' AND";
	$where .= ( $SCRIPT_NAME == 'clan.php' ) ? "a.clanid = '" . $session['user']['clanid'] . "' AND" : '';

	$sql = "SELECT a.acctid, a.name, a.login
			FROM " . db_prefix('accounts') . " a, " . db_prefix('module_userprefs') . " b
			WHERE $where a.loggedin = 1
				AND b.modulename = 'whoshere'
				AND b.setting = 'playerloc'
				AND b.value = '" . $SCRIPT_NAME . "'
				AND a.acctid = b.userid
				AND a.acctid <> '" . $session['user']['acctid'] . "'
				AND a.laston > '" . date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT",300) . " seconds"))."'";
	$result = db_query($sql);

	output("`n`@Who Else is here:`n");

	if( $count = db_num_rows($result) )
	{
		$pre = '';
		$and = translate_inline('and');
		$i = 1;
		while( $row = db_fetch_assoc($result) )
		{
			if( is_module_active('hiddenplayers') && $SCRIPT_NAME != 'superuser.php' )
			{
				if( $session['user']['superuser'] > 0 )
				{
					if( get_module_pref('hidden','hiddenplayers',$row['acctid']) == TRUE )
					{
						$pre .= '<a href="bio.php?char=' . rawurlencode($row['login']) . '&ret=' . URLEncode($_SERVER['REQUEST_URI']) . '"><span>' . $row['name'] . '<i>-hidden</i></span></a>';
					}
					else
					{
						$pre .= '<a href="bio.php?char=' . rawurlencode($row['login']) . '&ret=' . URLEncode($_SERVER['REQUEST_URI']) . '"><span>' . $row['name'] . '</span></a>';
					}
					addnav('',"bio.php?char=".rawurlencode($row['login'])."&ret=".URLEncode($_SERVER['REQUEST_URI']));
				}
				else
				{
					if( get_module_pref('hidden','hiddenplayers', $row['acctid']) == FALSE )
					{
						$pre .= '<a href="bio.php?char=' . rawurlencode($row['login']) . '&ret=' . URLEncode($_SERVER['REQUEST_URI']) . '"><span>' . $row['name'] . '</span></a>';
						addnav('',"bio.php?char=".rawurlencode($row['login'])."&ret=".URLEncode($_SERVER['REQUEST_URI']));
					}
				}
			}
			else
			{
				$pre .= '<a href="bio.php?char=' . rawurlencode($row['login']) . '&ret=' . URLEncode($_SERVER['REQUEST_URI']) . '"><span>' . $row['name'] . '</span></a>';
				addnav('',"bio.php?char=".rawurlencode($row['login'])."&ret=".URLEncode($_SERVER['REQUEST_URI']));
			}

			if( $count >= 3 && $i != ($count-1) && $i != $count )
			{
				$pre .= '`0, ';
			}
			if( $i == ($count-1) )
			{
				$pre .= ' `0' . $and . ' ';
			}
			$i++;
		}
		output('`7%s`7.',$pre,TRUE);
	}
	else
	{
		output("`2No one`7.`6");
	}

	output("`n`2-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-`n");
?>