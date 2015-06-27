<?php

function align($val,$user=FALSE){
	// global $session;
	// if ($user === FALSE) $user = $session['user']['acctid'];
	// No need for this line, as if 4th argument of increment_module_pref is false, it calls $session['user']['acctid'];
	
	$val = (int)$val;
	$hook = modulehook('align-val-adjust', array('modifier'=>1));
	if (!isset($hook['modifier']))
		// FAIL SAFE
		$modifier = 1;
	else
		$modifier = $hook['modifier'];
	$newval = round($val * $modifier,0);
	increment_module_pref('alignment',$newval,'alignment',$user);
}

function get_align($user=false){
	// global $session;
	// if ($user === FALSE) $user = $session['user']['acctid'];
	// No need for this line, as if 4th argument of get_module_pref is false, it calls $session['user']['acctid'];
		
	$val = get_module_pref('alignment','alignment',$user);
	return $val;
}

function set_align($val,$user=false){
	// global $session;
	// if ($user === FALSE) $user = $session['user']['acctid'];
	// No need for this line, as if 4th argument of set_module_pref is false, it calls $session['user']['acctid'];
	
	set_module_pref('alignment',$val,'alignment',$user);
}

?>