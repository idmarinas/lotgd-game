<?php
function dragoneggs_news7(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("LoGD News");
	output("`n`c`b`!Daily News`b`c`2");
	output_notl("`c`2-=-`@=-=`2-=-`@=-=`2-=-`@=-=`2-=-`0`c");
	if ($op2==1){
		output("`4'`^Five hundred gold`4 for `%a gem`4.  Here ya go,'`2 he says as he gives you a nice shiny gem.");
		$gem=1;
		$cash=-500;
	}elseif ($op2==2){
		output("`4'`^Nine hundred gold`4 for `%2 gems`4.  Here ya go,'`2 he says as he gives you 2 very pretty gems.");
		$gem=2;
		$cash=-900;
	}elseif ($op2==3){
		output("`4'`^Twelve hundred gold`4 for `%3 gems`4.  Here ya go,'`2 he says as he gives you 3 perfect gems.");
		$gem=3;
		$cash=-1200;
	}elseif ($op2==4){
		output("`4'`%One gem`4 for `^300 gold`4.  Here ya go,'`2 he says as he gives you a handful of gold.");
		$gem=-1;
		$cash=300;
	}elseif ($op2==5){
		output("`4'`%Two gems`4 for `^700 gold`4.  Here ya go,'`2 he says as he gives you a handful of gold.");
		$gem=-2;
		$cash=700;
	}elseif ($op2==6){
		output("`4'`%Three gems`4 for `^1200 gold`4.  Here ya go,'`2 he says as he gives you a handful of gold.");
		$gem=-3;
		$cash=1200;
	}
	$session['user']['gems']+=$gem;
	$session['user']['gold']+=$cash;
	if ($gem<=0) debuglog("Made an exchange, giving $gem gems for $cash gold while researching dragon eggs at the Daily News.");
	else debuglog("Made an exchange, giving $cash gold for $gem gems while researching dragon eggs at the Daily News.");
	addnav("Return to the Daily News","news.php");
	villagenav();
}
?>