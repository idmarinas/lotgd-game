<?php

function timeandweather_getmoduleinfo(){
	$info = array(
		"name"=>"Time and Weather",
		"version"=>"2010-10-27",
		"author"=>"Dan Hall",
		"category"=>"Time and Weather",
		"download"=>"",
		"settings"=>array(
			"currentweather"=>"The current weather code,int|4",
			"lastweather"=>"The previous weather code,int|4",
			"lastupdate"=>"Timestamp of last weather change,int|0",
			"changeevery"=>"Change the weather once per this number of real seconds,int|1200",
		),
	);
	return $info;
}

function timeandweather_install(){
	module_addhook("charstats");
	module_addhook("prerender");
	module_addhook("worldnav");
	return true;
}

function timeandweather_uninstall(){
	return true;
}

function timeandweather_dohook($hookname,$args){
	global $session,$outdoors,$shady,$rainy,$brightness;
	switch($hookname){
		case "prerender":
			//debug($outdoors);
			// $brightness = "darker";
			// $brightness = "darkest";
			// $brightness = "lighter";
			// $rainy = 2;
			// $outdoors = true;
			// $shady = true;
			if (!$session['noweathergraphics'] && (strpos($session['templatename'],"Default")===0 || strpos($session['templatename'],"Dragon - Verde")===0)){
				global $output;
				timeandweather_calculate_graphics();
				if ($outdoors){
					$weatherstart = "";
					$weatherend = "";
					for ($i=0; $i<$rainy; $i++){
						$weatherstart .= "<div style='background:url(images/weather/rain1.png); background-position:".e_rand(0,100)."% ".e_rand(0,100)."%; background-repeat:no-repeat;'>";
						$weatherend .= "</div>";
						$weatherstart .= "<div style='background:url(images/weather/rain2.png); background-position:".e_rand(0,100)."% ".e_rand(0,100)."%; background-repeat:no-repeat;'>";
						$weatherend .= "</div>";
						$weatherstart .= "<div style='background:url(images/weather/rain3.png); background-position:".e_rand(0,100)."% ".e_rand(0,100)."%; background-repeat:no-repeat;'>";
						$weatherend .= "</div>";
						//debug("rain");
					}
					if ($shady && $brightness!="darker" && $brightness!="darkest" && !$rainy){
						$weatherstart .= "<div style='background:url(images/weather/tree-1.gif); background-repeat:no-repeat;'>";
						$weatherend .= "</div>";
						$weatherstart .= "<div style='background:url(images/weather/tree-2.gif); background-position:top right; background-repeat:no-repeat;'>";
						$weatherend .= "</div>";
					}
					$output = str_replace("<!--weatherstart-->",$weatherstart,$output);
					$output = str_replace("<!--weatherend-->",$weatherend,$output);
					if ($brightness){
						$output = str_replace("sitecenter","sitecenter-".$brightness,$output);
					}
				}
			}
		break;
		case "charstats":
			//debug($session);
			$info = timeandweather_getcurrent();
			$definitiontext = array(
				1 => array(
					1 => "Cold and Frosty",
					2 => "Cold and Misty",
					3 => "Cool and Dewy",
					4 => "Warm and Clear",
					5 => "Cool and Drizzly",
					6 => "Dark and Rainy",
					7 => "Dark and Stormy",
				),
				2 => array(
					1 => "Cool and Misty",
					2 => "Mild and Dewy",
					3 => "Mild and Clear",
					4 => "Warm and Clear",
					5 => "Cool and Drizzly",
					6 => "Dark and Rainy",
					7 => "Dark and Stormy",
				),
				3 => array(
					1 => "Hot and Humid",
					2 => "Hot and Sunny",
					3 => "Warm and Sunny",
					4 => "Clear and Sunny",
					5 => "Light Showers",
					6 => "Heavy Rain",
					7 => "Thunderstorms",
				),
				4 => array(
					1 => "Hot and Humid",
					2 => "Hot and Sunny",
					3 => "Warm and Sunny",
					4 => "Clear and Sunny",
					5 => "Light Showers",
					6 => "Heavy Rain",
					7 => "Thunderstorms",
				),
				5 => array(
					1 => "Hot and Humid",
					2 => "Warm and Bright",
					3 => "Clear and Bright",
					4 => "Cool and Bright",
					5 => "Cloudy Skies",
					6 => "Darkening Rain",
					7 => "Dark and Stormy",
				),
				6 => array(
					1 => "Warm and Damp",
					2 => "Mild and Damp",
					3 => "Mild and Clear",
					4 => "Cool and Clear",
					5 => "Dark and Humid",
					6 => "Dark and Rainy",
					7 => "Dark and Stormy",
				),
				7 => array(
					1 => "Cold and Bright",
					2 => "Chilly and Light",
					3 => "Clear and Still",
					4 => "Warm and Humid",
					5 => "Dark and Humid",
					6 => "Pitch Black Rain",
					7 => "Black Storm",
				),
			);
			addcharstat("Game State");
			$stat = $definitiontext[$info['timezone']][$info['weather']];
			addcharstat("Current Weather:",$stat);
			break;
		}
	return $args;
}

function timeandweather_run(){
	return true;
}

function timeandweather_calculate_graphics(){
	global $session,$outdoors,$shady,$rainy,$brightness,$override_weather;
	if (!$override_weather){
		$info = timeandweather_getcurrent();
		switch ($info['timezone']){
			case 1:
				switch($info['weather']){
					case 1:
						$brightness = "darker";
					break;
					case 2:
						$brightness = "darker";
					break;
					case 3:
						$brightness = "darker";
					break;
					case 4:
						$brightness = "darker";
					break;
					case 5:
						$rainy = 1;
						$brightness = "darker";
					break;
					case 6:
						$rainy = 2;
						$brightness = "darkest";
					break;
					case 7:
						$rainy = 3;
						$brightness = "darkest";
					break;
				}
			break;
			case 2:
				switch($info['weather']){
					case 5:
						$rainy = 1;
						$brightness = "darker";
					break;
					case 6:
						$rainy = 2;
						$brightness = "darkest";
					break;
					case 7:
						$rainy = 3;
						$brightness = "darkest";
					break;
				}
			break;
			case 3:
				switch($info['weather']){
					case 1:
						$brightness = "lighter";
					break;
					case 2:
						$brightness = "lighter";
					break;
					case 5:
						$rainy = 1;
					break;
					case 6:
						$rainy = 2;
						$brightness = "darker";
					break;
					case 7:
						$rainy = 3;
						$brightness = "darkest";
					break;
				}
			break;
			case 4:
				switch($info['weather']){
					case 1:
						$brightness = "lighter";
					break;
					case 2:
						$brightness = "lighter";
					break;
					case 5:
						$rainy = 1;
					break;
					case 6:
						$rainy = 2;
						$brightness = "darker";
					break;
					case 7:
						$rainy = 3;
						$brightness = "darkest";
					break;
				}
			break;
			case 5:
				switch($info['weather']){
					case 5:
						$rainy = 1;
						$brightness = "darker";
					break;
					case 6:
						$rainy = 2;
						$brightness = "darkest";
					break;
					case 7:
						$rainy = 3;
						$brightness = "darkest";
					break;
				}
			break;
			case 6:
				switch($info['weather']){
					case 1:
						$brightness = "darker";
					break;
					case 2:
						$brightness = "darker";
					break;
					case 3:
						$brightness = "darker";
					break;
					case 4:
						$brightness = "darker";
					break;
					case 5:
						$rainy = 1;
						$brightness = "darker";
					break;
					case 6:
						$rainy = 2;
						$brightness = "darkest";
					break;
					case 7:
						$rainy = 3;
						$brightness = "darkest";
					break;
				}
			break;
			case 7:
				switch($info['weather']){
					case 1:
						$brightness = "darkest";
					break;
					case 2:
						$brightness = "darkest";
					break;
					case 3:
						$brightness = "darkest";
					break;
					case 4:
						$brightness = "darkest";
					break;
					case 5:
						$rainy = 1;
						$brightness = "darkest";
					break;
					case 6:
						$rainy = 2;
						$brightness = "darkest";
					break;
					case 7:
						$rainy = 3;
						$brightness = "darkest";
					break;
				}
			break;
		}
	}
}

function timeandweather_getcurrent(){
	global $session,$outdoors,$shady,$rainy,$brightness;
	require_once "lib/datetime.php";
	$tdet = gametimedetails();
	$now = $tdet['secssofartoday'];
	//debug ($now);
	timeandweather_update();
	$ret = array();
	$ret['time'] = $now;
	//get coarse timezone
	switch ($now){
		case $now > 79200:
		case $now < 14400:
			//night
			$zone = 7;
		break;
		case $now > 77400:
			//dusk
			$zone = 6;
		break;
		case $now > 75600:
			//sunset
			$zone = 5;
		break;
		case $now > 43200:
			//afternoon
			$zone = 4;
		break;
		case $now > 18000:
			//morning
			$zone = 3;
		break;
		case $now > 16200:
			//sunrise
			$zone = 2;
		break;
		case $now > 14400:
			//dawn
			$zone = 1;
		break;
	}
	$ret['timezone'] = $zone;
	$ret['weather'] = get_module_setting("currentweather","timeandweather");
	$change = get_module_setting("lastweather","timeandweather") - get_module_setting("currentweather","timeandweather");
	$ret['change'] = $change;
	//debug($ret);
	return $ret;
}

function timeandweather_update(){
	$now = time();
	$last = get_module_setting("lastupdate","timeandweather");
	$change = get_module_setting("changeevery","timeandweather");
	$changeat = $last + $change;
	//debug($changeat);
	$changein = $changeat - $now;
	//debug($changein);
	//$changeat = 0;
	if ($now > $changeat){
		set_module_setting("lastupdate",$now,"timeandweather");
		//time to change the weather
		$old = get_module_setting("currentweather","timeandweather");
		set_module_setting("lastweather",$old,"timeandweather");
		if ($old==1){
			$new = e_rand(0,2);
		} else if ($old==2){
			$new = e_rand(-1,2);
		} else if ($old==6){
			$new = e_rand(-2,1);
		}
		 else if ($old==7){
			$new = e_rand(-2,0);
		} else {
			//trend towards the sun
			//$new = e_rand(-2,2);
			$new = e_rand(-2,1);
		}
		increment_module_setting("currentweather",$new,"timeandweather");
		if (get_module_setting("currentweather","timeandweather") > 7){
			set_module_setting("currentweather",7,"timeandweather");
		} else if (get_module_setting("currentweather","timeandweather") < 1){
			set_module_setting("currentweather",1,"timeandweather");
		}
	}
}

?>