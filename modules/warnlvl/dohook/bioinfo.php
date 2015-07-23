<?php
	if( $session['user']['superuser'] & SU_GIVES_YOM_WARNING )
	{
		addnav('Warnings');
		addnav('Warn Player','runmodule.php?module=warnlvl&op=warnplayer&id=' . $id . '&ret=' . urlencode($_SERVER['REQUEST_URI']));
	}

	if( get_module_setting('show') == 1 || $session['user']['superuser'] & SU_GIVES_YOM_WARNING )
	{
		$change = FALSE;
		$count = 0;
		$total = 0;
		$list2 = '';
		$allprefs = get_module_pref('allprefs','warnlvl',$id);
		if( !empty($allprefs) ) 
		{
			$allprefs = unserialize($allprefs);
			$count = ( isset($allprefs['reason']) ) ? count($allprefs['reason']) : 0;
			$total = ( isset($allprefs['warnings']) ) ? $allprefs['warnings'] : 0;

			$reasons = explode("\r\n",get_module_setting('reasons','warnlvl'));
			$reasons['999'] = translate_inline('Unknown');
			$keep_days = get_module_setting('days','warnlvl');
			$seconds = 60 * 60 * 24 * $keep_days;
			$list = array();
			for( $i=0; $i<$count; $i++ )
			{
				if( $keep_days == 0 || ($allprefs['date'][$i] + $seconds) > time() )
				{
					$list[$reasons[$allprefs['reason'][$i]]]++;
				}
				else
				{
					unset($allprefs['reason'][$i]);
					unset($allprefs['comments'][$i]);
					unset($allprefs['subber_id'][$i]);
					unset($allprefs['date'][$i]);
					$count--;
					$change = TRUE;
				}
			}

			if( !empty($change) )
			{
				set_module_pref('allprefs',serialize($allprefs),'warnlvl',$id);
			}

			if( !empty($list) )
			{
				foreach( $list as $key => $value )
				{
					if( $value > 1 )
					{
						$list2 .= '`$' . $value . ' x ' . $key;
					}
					else
					{
						$list2 .= '`$' . $key;
					}
					$list2 .= '`@, ';
				}
				$list2 = rtrim($list2, ', ') . '.';
			}
		}

		if( $count > 0 && $total > 0 )
		{
			output("`^Warnings: `@%s `@currently has `$%s %s`@. %s in total.`n", $args['name'], $count, translate_inline($count==1?'warning':'warnings'), $total);
		}
		elseif( $count == 0 && $total > 0 )
		{
			output("`^Warnings: `@%s `@has no current warnings, but has had %s in the past.`n", $args['name'], $total);
		}
		else
		{
			output("`^Warnings: `@%s `@has had no warnings at all.`n", $args['name']);
		}

		if( $count == 1 )
		{
			output('This warning was for the following reason: %s`n`n', $list2);
		}
		elseif( $count > 1 )
		{
			output('These warnings were for the following reasons: %s`n`n', $list2);
		}
	}
?>