<?php 

function get_creature_stats($dk = 0)
{
    global $session;
    
    if (0 == $dk) $dk = $session['user']['dragonkills'];

    //-- Estan colocados por orden de importancia
    $con = e_rand($dk/6,$dk/2);
    $dk -= $con;
    $str = e_rand(0,$dk);
    $dk -= $str;
    $dex = e_rand(0,$dk);
    $dk -= $dex;
    $int = e_rand(0,$dk);
    $wis = ($dk - $int);
    
    return ['str' => $str, 'dex' => $dex, 'con' => $con, 'int' => $int, 'wis' => $wis ];
}

function get_creature_hitpoints($attrs)
{   
    $conbonus = $attrs['con'] * .5;
	$wisbonus = $attrs['wis'] * .2;
	$strbonus = $attrs['str'] * .3;
        
    $hitpoints = round($conbonus + $wisbonus + $strbonus, 0);
    
    return max($hitpoints, 0);
}

function get_creature_attack($attrs) 
{
	$strbonus = (1/3) * $attrs['str'];
	$speedbonus = (1/3) * get_creature_speed($attrs);
	$wisdombonus = (1/6) * $attrs['wis'];
	$intbonus = (1/6) * $attrs['int'];
	
	$attack = $strbonus + $speedbonus + $wisdombonus + $intbonus;
    
	return max($attack,0);
}

function get_creature_defense($attrs) 
{
	$wisdombonus = (1/4) * $attrs['wis'];
	$constbonus = (3/8) * $attrs['con'];
	$speedbonus = (3/8) * get_player_speed($attrs);
    
	$defense = $wisdombonus + $speedbonus + $constbonus;
    
	return max($defense,0);
}

function get_creature_speed($attrs) 
{
	$speed = (1/2) * $attrs['dex'] + (1/4) * $attrs['int'] + (5/2);
    
	return max($speed,0);
}