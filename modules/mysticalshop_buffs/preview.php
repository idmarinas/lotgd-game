<?php
		$id = httpget('id');
		$sql = "SELECT * FROM " . db_prefix("magicitems") . " WHERE id='$id'";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		if (get_module_setting("togglebuffview") && $row['buffid']>0) {
			$buffid = $row['buffid'];
			$sql = "SELECT * FROM " . db_prefix("magicitembuffs") . " WHERE buffid='$buffid'";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			$buff = unserialize($row['itembuff']);
	
			if (get_module_setting("toggleatkmod") && $buff['atkmod'] != "") {
				$var = mysticalshop_buffs_calc($buff['atkmod']);
				$atkmod = round($var*100-100,2);
				if ($atkmod != 0) {
					if ($atkmod > 0) {
						$sign1 = '+';
					}
					$atk = sprintf_translate(array('`n%s%s% attack',$sign1, $atkmod));
				}
			}
			if (get_module_setting("toggledefmod") && $buff['defmod'] != "") {
				$var0 = mysticalshop_buffs_calc($buff['defmod']);
				$defmod = round($var0*100-100,2);
				if ($defmod != 0) {
					if ($defmod > 0) {
						$sign2 = '+';
					}
					$def = sprintf_translate(array('`n%s%s% defense',$sign2, $defmod));
				}
			}
			if (get_module_setting("toggledmgmod") && $buff['dmgmod'] != "") {
				$var0 = mysticalshop_buffs_calc($buff['dmgmod']);
				$dmgmod = round($var0*100-100,2);
				if ($dmgmod != 0) {
					if ($dmgmod > 0) {
						$sign2 = '+';
					}
					$dmg = sprintf_translate(array('`n%s%s% damage',$sign2, $dmgmod));
				}
			}
			if (get_module_setting("toggleregen") && $buff['regen'] != "") {
				$var1 = round(mysticalshop_buffs_calc($buff['regen']),0);
				if ($var1 != 0) {
					if ($var1 > 0) {
						$sign3 = '+';
					}
				$regen = sprintf_translate(array('`n%s%s HP regeneration per round',$sign3, $var1));
				}
			}
			if (get_module_setting("toggledmgshld") && $buff['damageshield'] != "") {
				$var2 = mysticalshop_buffs_calc($buff['damageshield']);
				$damageshieldmod = round($var2*100,2);
				if ($damageshieldmod != 0) {
					if ($damageshieldmod >= 0) {
						$sign4 = '+';
					}
					$damageshield = sprintf_translate(array('`n%s%s% damage shield', $sign4, $damageshieldmod ));
				}
			}
			if (get_module_setting("togglelifetap") && $buff['lifetap'] != "") {
				$var3 = mysticalshop_buffs_calc($buff['lifetap']);
				$lifetapmod = round($var3*100,2);
				if ($lifetapmod != 0) {
					if ($lifetapmod >= 0) {
						$sign5 = '+';
					}
					$lifetap = sprintf_translate(array('`n`n%s%s% HP leech',$sign5, $lifetapmod));
				}
			}
			if (get_module_setting("toggleminioncount") && $buff['minioncount'] != "") {
				$var4 = round(mysticalshop_buffs_calc($buff['minioncount']),0);
				if ($var4 != 0) {
					$minioncount = sprintf_translate(array('`n%s attack(s) maximum per round', $var4));
				}
			}
			  if (get_module_setting("togglemaxbadguydamage") && $buff['maxbadguydamage'] != "") {
				$var5 = round(mysticalshop_buffs_calc($buff['maxbadguydamage']),0);
				if ($var5 != 0) {
					$maxbadguydamage = sprintf_translate(array('`n%s maximum damage per attack', $var5));
				}
			}	 
			  if (get_module_setting("togglebadguyatkmod") && $buff['badguyatkmod'] != "") {
				$var6 = mysticalshop_buffs_calc($buff['badguyatkmod']);
				$badguyatk = round($var6*100-100,2);
				if ($badguyatk != 0) {
					if ($badguyatk >= 0) {
						$sign6 = '+';
					}
					$badguyatkmod = sprintf_translate(array('`n%s%s% enemy attack',$sign6, $badguyatk));
				}
			}	 
			  if (get_module_setting("togglebadguydefmod") && $buff['badguydefmod'] != "") {
				$var7 = mysticalshop_buffs_calc($buff['badguydefmod']);
				$badguydef = round($var7*100-100,2);
				if ($badguydef != 0) {
					if ($badguydef >= 0) {
						$sign7 = '+';
					}
					$badguydefmod = sprintf_translate(array('`n%s%s% enemy defense', $sign7, $badguydef));
				}
			}	 
			  if (get_module_setting("togglebadguydmgmod") && $buff['badguydmgmod'] != "") {
				$var8 = mysticalshop_buffs_calc($buff['badguydmgmod']);
				$badguydmg = round($var8*100-100,2);
				if ($badguydmg != 0) {
					if ($badguydmg >= 0) {
						$sign8 = '+';
					}
					$badguydmgmod = sprintf_translate(array('`n%s%s% enemy damage modifier', $sign8, $badguydmg));
				}
			}	 
			  if (get_module_setting("toggleinv") && $buff['invulnerable'] != "") {
				$var9 = round(mysticalshop_buffs_calc($buff['invulnerable']),0);
				if ($var9 == 1) {
					$inv = '`n'. translate_inline("INVULNERABLE!!");
				}
			}	 
			if (get_module_setting("toggleround") && $buff['rounds'] != "" && $buff['rounds'] != 0) {
				$round = $buff['rounds'];
				if ($round < 0) $round = translate_inline('Permanent');
				$rounds = '`n'.$round.' '.translate_inline('Rounds');
			}
			if ($buff['name'] != "") {
				$name = $buff['name'];
			}
			output("`n`b`c`@Special Abilities`7`b`c");
			output_notl("`c`7$name`7 $rounds $atk $def $dmg $regen $damageshield $lifetap $minioncount $maxbadguydamage $badguyatkmod $badguydefmod $badguydmgmod`c`n");
		}
?>