<?php
// addnews ready
// mail ready
// translator ready

function healthbar_getmoduleinfo(){
	$info = array(
		"name"=>"Health Bar",
		"version"=>"2.1.0",
		"author"=>"Iván D.M based on idea JT Traub",
		"category"=>"Stat Display",
		"download"=>"",
		"prefs"=>array(
			"Barra de salud,title",
			"user_showcurrent"=>"Mostrar la salud actual en número,bool|0",
			"user_showmax"=>"Mostrar la salud máxima (sólo si actual),bool|0"
		)
	);
	return $info;
}

function healthbar_install(){
	module_addhook("charstats");
	return true;
}

function healthbar_uninstall(){
	return true;
}

function healthbar_dohook($hookname,$args){
	global $session;
	switch($hookname){
	case "charstats":
		if ($session['user']['alive']) {
			$cur = $session['user']['hitpoints'];
			$realmax = $session['user']['maxhitpoints'];
			$stat = "Hitpoints";
			$cur_adjustment = check_temp_stat("hitpoints",1);
			$max_adjustment = check_temp_stat("maxhitpoints",1);
		} else {
			$cur = $session['user']['soulpoints'];
			$realmax = $session['user']['level'] * 10 + 50 + $session['user']['dragonkills']*2;
			$stat = "Soulpoints";
			$cur_adjustment = check_temp_stat("soulpoints",1);
			$max_adjustment = "";
		}
		if ($cur > $realmax) $max = $cur;
		else $max = $realmax;

		$pct = round($cur / $max * 100, 0);
		if ($pct > 100) {
			$pct = 100;
		}
		if ($pct < 0) {
			$pct = 0;
		}
		$text = $script = "";
		if ($pct > 60) {
			if ($session['user']['alive']) $color = "progress-healthbar-alive-60 ";
			else $color = "progress-healthbar-dead-60";
		} elseif ($pct > 25) {
			if ($session['user']['alive']) $color = "progress-healthbar-alive-25 animated pulse";
			else $color = "progress-healthbar-dead-25";
			$text = '<i class="fa fa-exclamation fa-fw"></i>';
			
		} else {
			if ($session['user']['alive']) $color = "progress-healthbar-alive-0 animated flash";
			else $color = "progress-healthbar-dead-0";
			$text = '<i class="fa fa-exclamation-triangle fa-fw"></i>';
			$script = '<script>
				$(".healthbar").on("webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend", function(){
					var self = this;
					var animatedName = "animated flash";
					$(self).removeClass(animatedName);
					setTimeout(function(){
						$(self).addClass(animatedName);
					},5000);
				});
			</script>';
		}
			
		$showcur = get_module_pref("user_showcurrent");
		$showmax = get_module_pref("user_showmax");
		$showbar = get_module_pref("user_showbar");
		
		if (!$showcur && !$showbar) $text.="";
		if ($showcur) $text .= $cur . $cur_adjustment;
		if ($showcur && $showmax) $text .= "`0/`&$realmax`0" . $max_adjustment;
		
		$new = "<div class='healthbar $animated'>
				 	<div class='progress-healthbar $color' style='width: $pct%;'>$text</div>
				</div>
				$script
		";
			
		setcharstat("Character Info", $stat, $new);
		break;
	}
	return $args;
}

function healthbar_run(){

}
?>
