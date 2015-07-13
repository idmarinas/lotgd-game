<?php

/**
 * 1.2.0
 * Add -> Currency simbol personalizate
 * Add -> Position of simbol (left of right of amount
 */
function funddrive_getmoduleinfo(){
	$info = array(
		"name"=>"Fund Drive Indicator",
		"version"=>"1.2.0",
		"author"=>"Eric Stevens and Iván D.M",
		"category"=>"Administrative",
		"download"=>"core_module",
		"settings"=>array(
			"indicatorText"=>"Indicator Text|Fund drive:",
			"baseamount"=>"Base amount (positive for donations not registered with the site / negative for expenses),int|0",
			"goalamount"=>"Goal amount of profit,int|5000",
			"simbol" => "Simbol of currency,var|$",
			"simbolPosition" => "Currency before amount?,bool|1",
			"targetmonth"=>"Month which we're watching,enum,,Always the current month,01,January,02,February,03,March,04,April,05,May,06,June,07,July,08,August,09,September,10,October,11,November,12,December|",
			"usebar"=>"Graph display:,enum,0,None,1,Bar,2,Graphic|1",
			"usetext"=>"Should we display the text as well?,bool|1",
			"showdollars"=>"Display dollar amounts in the text?,bool|1",
			"deductfees"=>"Should the paypal fees be deducted from the amount?,bool|0",
		),
	);
	return $info;
}

function funddrive_install(){
	module_addhook("everyfooter");
	module_addhook("donation");
	module_addhook("funddrive_getpercent");
	return true;
}

function funddrive_uninstall(){
	return true;
}

function funddrive_dohook($hookname,$args){
	if ($hookname=="donation"){
		invalidatedatacache("mod_funddrive_totals");
	}elseif ($hookname=="funddrive_getpercent"){
		$prog = funddrive_getpercent();
		$args['percent'] = $prog['percent'];
	}elseif ($hookname=="everyfooter"){
		if (!array_key_exists('paypal', $args) || !is_array($args['paypal'])){
			$args['paypal'] = array();
		}
		$prog = funddrive_getpercent();

		$out = "{$prog['percent']}%";
		$goal = $prog['goal'];
		$pct = $prog['percent'];
		$current = $prog['current'];
		
		$text = "";
		if (get_module_setting("usetext")) {
			$simbol = get_module_setting("simbol");
			if (get_module_setting("simbolPosition"))
			{
				$currencyText = "($simbol$current/$simbol$goal)";
			}
			else 
			{
				$currencyText = "($current$simbol/$goal$simbol)";
			}
			
			
			$text = "".str_replace(' ','&nbsp;',get_module_setting("indicatorText"))."&nbsp;$out".(get_module_setting("showdollars")?" ". $currencyText:"");
		}
		switch(get_module_setting("usebar")) {
		case 1:
			$color = ($pct < 100 ? "progress-funddrivebar-color" : "progress-funddrivebar-full");
			$res = "<div class='funddrivebar'>
					 	<div class='progress-funddrivebar $color' style='width: $pct%;'>&nbsp;$text</div>
					 </div>";
			break;
		case 2:
			$nonpct = 100-$pct;
			$imgwidth = 140;
			$imgheight = 140;
			$topheight = round($imgheight * $nonpct / 100);
			$bottomheight = $imgheight - $topheight;
			if ($pct < 100){
				$res = "<table border='0' cellpadding='0' cellspacing='0' width='$imgwidth' height='$imgheight'>"
					."<tr>"
					."<td class='padding0' style=\"background-image: url(images/Medallion-Red.gif); background-position: top left; background-repeat: no-repeat;\" height='$topheight'><img src='images/trans.gif' width='$imgwidth' height='$topheight' alt=''></td>"
					."</tr><tr>"
					."<td class='padding0' style=\"background-image: url(images/Medallion-Yellow.gif); background-position: bottom left; background-repeat: no-repeat;\" height='$bottomheight'><img src='images/trans.gif' width='$imgwidth' height='$bottomheight' alt=''></td>"
					."</tr>"
					."</table><br><div align='center'>$res</div>";
			}else{
				$res = "<table border='0' cellpadding='0' cellspacing='0' width='$imgwidth' height='$imgheight'>"
					."<tr>"
					."<td class='padding0' style=\"background-image: url(images/Medallion-Green.gif); background-position: top left; background-repeat: no-repeat;\" height='$topheight'><img src='images/trans.gif' width='$imgwidth' height='$imgheight' alt=''></td>"
					."</tr>"
					."</table><br><div align='center'>$res</div>";
			}
		}
		if ($res) array_push($args['paypal'],$res);
	}
	return $args;
}
function funddrive_getpercent(){
	$targetmonth = get_module_setting("targetmonth");
	if ($targetmonth==""){
		$targetmonth=date("m");
	}
	$start = date("Y")."-".$targetmonth."-01";
	$end = date("Y-m-d",strtotime("+1 month",strtotime($start)));
	$result = db_query_cached("SELECT sum(amount) AS gross, sum(txfee) AS fees FROM ".db_prefix("paylog")." WHERE processdate >= '$start' AND processdate < '$end'","mod_funddrive_totals",10);
	$goal = get_module_setting("goalamount");
	$base = get_module_setting("baseamount");
	$row = db_fetch_assoc($result);
	$current = $row['gross'] + $base;
	if (get_module_setting("deductfees")) {
		$current -= $row['fees'];
	}
	$pct = round($current / $goal * 100,0);
	$ret = array(
		'percent'=>$pct,
		'goal'=>$goal,
		'current'=>$current
	);
	return $ret;
}
?>
