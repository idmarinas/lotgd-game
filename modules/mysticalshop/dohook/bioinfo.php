<?php
$acctid = $args['acctid'];
$name = $args['name'];

if( get_module_pref( 'helm', 'mysticalshop', $acctid ) )
	output( '`^%s`2 is wearing `3%s`2.`0`n', $name,
			get_module_pref( 'helmname', 'mysticalshop', $acctid ) );
if( get_module_pref( 'amulet', 'mysticalshop', $acctid ) )
	output( '`^%s`2 is wearing `3%s`2.`0`n', $name,
			get_module_pref( 'amuletname', 'mysticalshop', $acctid ) );
if( get_module_pref( 'cloak', 'mysticalshop', $acctid ) )
	output( '`^%s`2 is wearing `3%s`2.`0`n', $name,
			get_module_pref( 'cloakname', 'mysticalshop', $acctid ) );
if( get_module_pref( 'ring', 'mysticalshop', $acctid ) )
	output( '`^%s`2 is wearing `3%s`2.`0`n', $name,
			get_module_pref( 'ringname', 'mysticalshop', $acctid ) );
if( get_module_pref( 'glove', 'mysticalshop', $acctid ) )
	output( '`^%s`2 is wearing `3%s`2.`0`n', $name,
			get_module_pref( 'glovename', 'mysticalshop', $acctid ) );
if( get_module_pref( 'boots', 'mysticalshop', $acctid ) )
	output( '`^%s`2 is wearing `3%s`2.`0`n', $name,
			get_module_pref( 'bootname', 'mysticalshop', $acctid ) );
if( get_module_pref( 'weapon', 'mysticalshop', $acctid ) )
	output( '`^%s`2 is wielding `3%s`2.`0`n', $name,
			get_module_pref( 'weaponname', 'mysticalshop', $acctid ) );
if( get_module_pref( 'armor', 'mysticalshop', $acctid ) )
	output( '`^%s`2 is equipped with `3%s`2.`0`n', $name,
			get_module_pref( 'armorname', 'mysticalshop', $acctid ) );
?>