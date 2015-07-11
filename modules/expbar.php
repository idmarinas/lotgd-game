<?php
// addnews ready
// mail ready
// translator ready

function expbar_getmoduleinfo(){
	$info = array(
		"name"=>"Experience Bar",
		"version"=>"1.1.0",
		"author"=>"JT Traub and Iván D.M<br>based on idea by Dan Van Dyke",
		"category"=>"Stat Display",
		"download"=>"core_module",
		"settings"=>array(
			"Experience Bar Module Settings,title",
			"showexpnumber"=>"Show current experience number,bool|1",
			"shownextgoal"=>"Show the exp needed for next level (only if current exp is shown),bool|0",
			"showbar"=>"Show the experience toward next level as a bar,bool|1",
		)
	);
	return $info;
}

function expbar_install(){
	module_addhook("charstats");
	return true;
}

function expbar_uninstall(){
	return true;
}

function expbar_dohook($hookname,$args){
	global $session;
	switch($hookname){
	case "charstats":
		require_once("lib/experience.php");
		$level = $session['user']['level'];
		$dks = $session['user']['dragonkills'];
		$min = exp_for_next_level($level-1, $dks);
		$req = exp_for_next_level($level, $dks);
		$exp = round($session['user']['experience'], 0) . check_temp_stat("experience",1);

		// If the user has dropped below the previous level, make that the
		// min and they need 100%  They will continue to need 100% until
		// they reach 'min' again.
		if ($exp < $min) $min = $exp;
		if ($req-$min > 0) $nonpct = floor(($req-$exp)/($req-$min) * 100);
		else $nonpct = 0;
		$pct = 100-$nonpct;
		if ($pct > 100) {
			$pct = 100;
			$nonpct = 0;
		}
		if ($pct < 0) {
			$pct = 0;
			$nonpct = 100;
		}
		if ($exp >= $req) {
			$animated = "animated flash";
			$color = "progress-bar-ready";
			$text = "<i class='fa fa-arrow-up'></i> " . translate_inline("Ready");
			$script = "<script>
						var animatedName = 'animated flash';
						$('.progress.expbar').on('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
								var self = this;
								$(self).removeClass(animatedName);
								setTimeout(function(){
									$(self).addClass(animatedName);
								},5000);
						});
					</script>";
		} else {
			$animated = "active";
			$color = "progress-bar-in-progress progress-bar-striped";
			$texr = "";
			$script = "";
		}
		$old = getcharstat("Character Info", "Experience");
		$new = "";
		$shownum = get_module_setting("showexpnumber");
		$shownext = get_module_setting("shownextgoal");
		$showbar = get_module_setting("showbar");
		if (!$shownum && !$showbar) $new="`b`\$hidden`b";
		if ($shownum) $new .= $old;
		if ($shownum && $shownext) $new .= "`0/`@$req`0";
		if ($showbar) {
			if ($shownum) $new .= "<br />";			
			$new .= "<div class='progress expbar $animated'>
					  <div class='progress-bar $color' style='width: $pct%;'>$text</div>
					</div>
					$script
					";
		}
		setcharstat("Character Info", "Experience", $new);
		break;
	}
	return $args;
}

function expbar_run(){

}
?>
