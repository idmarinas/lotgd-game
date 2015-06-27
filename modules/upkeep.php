<?php
function upkeep_getmoduleinfo(){
	$info = array(
		"name"=>"Dwelling Upkeep",
		"version"=>"20061009",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=162",
		"vertxtloc"=>"http://dragonprime.net/users/sixf00t4/",
		"author"=>"<a href='http://www.joshuadhall.com' targer=_new>Sixf00t4</a>",
		"category"=>"Dwellings",
		"description"=>"Forces dwelling owners to maintain their dwellings",
		"requires"=>array(
	       "dwellings"=>"20060105|By Sixf00t4, available on DragonPrime",
	),
		"settings"=>array(
			"repo"=>"What should be done when the owner of a dwelling fails to keep up?, enum,
				4,Abandon it,
				5,Put it up for sale|5",
	),
		"prefs-dwellingtypes"=>array(
			"Upkeep Type Settings,title",
			"useupkeep" => "Require users to keep up this dwelling type?, bool|1",
			"upkeepdays" => "every __ game days is an upkeep cycle., int|12",
			"upkeepturns" => "require how many turns to spend per upkeep cycle?, int|5",
			"upkeepgold" => "require how many gold to spend per upkeep cycle?, int|150",
			"upkeepgems" => "require how many gems to spend per upkeep cycle?, int|2",
			"upkeepgoldloss" => "How much does the gold value go down if they fail the cycle?, int|350",
			"upkeepgemsloss" => "How much does the gems value go down if they fail the cycle?, int|5",   
			"upkeepgoldgain" => "How much does the gold value increase if they do the cycle?, int|25",
			"upkeepgemsgain" => "How much does the gems value increase if they do the cycle?, int|1",
	),
	"prefs-dwellings"=>array(
		"upkeepturns"=>"How many turns have they spent on upkeeping this dwelling?,int|0",
		"upkeepgold"=>"How much have they spent on upkeeping this dwelling this cycle?,int|0",
		"upkeepgems"=>"How many gems have they spent on upkeeping this dwelling this cycle?,int|0",
		"upkeepdays"=>"How many days have gone by this cycle?,int|0",
		"exempt"=>"Is this dwelling exempt from up keep?,bool|0",
	),
	);
	return $info;
}

function upkeep_install(){
	module_addhook("newday");
	module_addhook("dwellings-management");
	module_addhook("dwellings-sold");
	return true;
}

function upkeep_uninstall() {
	return true;
}

function upkeep_dohook($hookname,$args) {
	global $session;
	switch ($hookname) {
		case "dwellings-sold":
			$dwid=$args['dwid'];
			set_module_objpref("dwellings",$dwid,"upkeepdays",0);
			set_module_objpref("dwellings",$dwid,"upkeepturns",0);
			set_module_objpref("dwellings",$dwid,"upkeepgold",0);
			set_module_objpref("dwellings",$dwid,"upkeepgems",0);
			set_module_objpref("dwellings",$dwid,"exempt",0);
			break;
		case "newday":
			$sql = "SELECT * FROM ".db_prefix("dwellings")." WHERE ownerid=".$session['user']['acctid']." and status=1";
			$result = db_query($sql);
			while($row = db_fetch_assoc($result)){
				$dwid = $row['dwid'];
				if(get_module_objpref("dwellings",$dwid,"exempt")) return $args;
				$type = $row['type'];
				$typeid=get_module_setting("typeid",$type);
				if(get_module_objpref("dwellingtypes",$typeid,"useupkeep")){
					
					$days = get_module_objpref("dwellings",$dwid,"upkeepdays");
					$tdays=get_module_objpref("dwellingtypes",$typeid,"upkeepdays");
					
					if($days >= $tdays){  //time is up for the up keep cycle
						set_module_objpref("dwellings",$dwid,"upkeepdays",0);
						$days = 0;
						$value=0;
						
						$turns = get_module_objpref("dwellings",$dwid,"upkeepturns");
						$gold = get_module_objpref("dwellings",$dwid,"upkeepgold");
						$gems = get_module_objpref("dwellings",$dwid,"upkeepgems");
						if($turns < get_module_objpref("dwellingtypes",$typeid,"upkeepturns")
						|| $gems < get_module_objpref("dwellingtypes",$typeid,"upkeepgems")
						|| $gold < get_module_objpref("dwellingtypes",$typeid,"upkeepgold")) $value++;

						$upargs=modulehook("upkeep-newday",array("type"=>$type,"dwid"=>$dwid,"value"=>$value));
						$value=$upargs['value'];

						set_module_objpref("dwellings",$dwid,"upkeepturns",0);
						set_module_objpref("dwellings",$dwid,"upkeepgems",0);
						set_module_objpref("dwellings",$dwid,"upkeepgold",0);

						if($value > 0){ //they didn't keep up
							$dwname=get_module_setting("dwname",$type);
							output("`n`n`0Since you failed to keep up with the maintenance of your %s`0 in %s, it has decreased in value.",$dwname,$row['location']);
							$goldloss = get_module_objpref("dwellingtypes",$typeid,"upkeepgoldloss");
							$gemsloss = get_module_objpref("dwellingtypes",$typeid,"upkeepgemsloss");
							$loss = "";
							if(($row['goldvalue'] - $goldloss)>0){
								$loss="goldvalue=goldvalue-$goldloss";
							}
							if(($row['gemvalue'] - $gemsloss) > 0){
								if ($loss != "") $loss=$loss.",";
								$loss = $loss."gemvalue=gemvalue-$gemsloss";
							}
							if($loss == ""){//repo it!
								output("`n`n`0Your %s`0 in %s, has been repossessed!  What a slum!",$dwname,$row['location']);
								$sql = "UPDATE ".db_prefix("dwellings")." SET ownerid=0, status=".get_module_setting("repo","upkeep")." WHERE dwid=$dwid";
								db_query($sql);
								$sql = "UPDATE ".db_prefix("dwellingkeys")." SET keyowner=0 WHERE dwid=$dwid";
								db_query($sql);
							}else{//lose value
								$sql = "UPDATE ".db_prefix("dwellings")." SET $loss WHERE dwid=$dwid";
								db_query($sql);
							}
						}else{//they kept up
							$gain = "";
							$maxgold = get_module_setting("goldcost",$type);
							$maxgems = get_module_setting("gemcost",$type);
							$dwname = get_module_setting("dwname",$type);
							output("`n`n`0The value of your %s`0 in %s, has increased in value due to you keeping up with the maintenance.",$dwname,$row['location']);
							$goldgain = get_module_objpref("dwellingtypes",$typeid,"upkeepgoldgain");
							$gemsgain = get_module_objpref("dwellingtypes",$typeid,"upkeepgemsgain");
							if(($row['goldvalue'] + $goldgain) <= $maxgold){
								//prevent profit just from cleaning your dwelling
								$gain .= "goldvalue=goldvalue+$goldgain";
							}
							if(($row['gemvalue'] + $gemsgain) <= $maxgems){
								//prevent profit just from cleaning your dwelling
								if ($gain != "") $gain = $gain.",";
								$gain = $gain."gemvalue=gemvalue+$gemsgain";
							}
							if($gain != ""){//increase value
								$sql = "UPDATE ".db_prefix("dwellings")." SET $gain WHERE dwid=$dwid";
								db_query($sql);
							}
						}
					}
					set_module_objpref("dwellings",$dwid,"upkeepdays",$days+1);
				}
			}
			break;
		case "dwellings-management":
			$dwid = $args['dwid'];
			$typeid = get_module_setting("typeid",$args['type']);
			$turns = get_module_objpref("dwellings",$dwid,"upkeepturns","upkeep");			$maxturns = get_module_objpref("dwellingtypes",$typeid,"upkeepturns","upkeep");			$turnsleft = $maxturns-$turns;			$gems = get_module_objpref("dwellings",$dwid,"upkeepgems","upkeep");			$maxgems = get_module_objpref("dwellingtypes",$typeid,"upkeepgems","upkeep");			$gemsleft = $maxgems - $gems;			$gold = get_module_objpref("dwellings",$dwid,"upkeepgold","upkeep");			$maxgold = get_module_objpref("dwellingtypes",$typeid,"upkeepgold","upkeep");			$goldleft = $maxgold - $gold;			$invest = translate_inline("Invest");			$inupkeep = translate_inline("in upkeep");			$dwturns = translate_inline("turns");
			$dwgems = translate_inline("Gems");
			$dwgold = translate_inline("Gold");			rawoutput("<tr><td colspan=2 class='trhead' style='text-align:center;'>Dwellings Upkeep</td></tr>");			rawoutput("<tr height=30px class='trlight'><td colspan=2>");			output("This upkeep cycle will end in %s days out of %s days.",get_module_objpref("dwellingtypes",$typeid,"upkeepdays","upkeep")-get_module_objpref("dwellings",$dwid,"upkeepdays","upkeep"),get_module_objpref("dwellingtypes",$typeid,"upkeepdays","upkeep"));			if($goldleft==0 && $gemsleft==0 && $turnsleft ==0)			output(" There's nothing to do, so just sit back and relax.");			rawoutput("</td></tr>");			$duringcycle = translate_inline("During this upkeep cycle you have invested");			rawoutput("<tr height=30px class='trdark'><td rowspan=3>$duringcycle</td><td>");			output("%s out of %s turns. ",$turns,$maxturns);			if($session['user']['turns']>=$turnsleft && $turnsleft>0){				rawoutput("<a href='runmodule.php?module=upkeep&op=manage&invest=turns&amount=$turnsleft&dwid=$dwid&type=$typeid'>$invest $turnsleft $dwturns $inupkeep</a></td></tr>");				addnav("","runmodule.php?module=upkeep&op=manage&invest=turns&amount=$turnsleft&dwid=$dwid&type=$typeid");			}else{				rawoutput("</td></tr>");			}			rawoutput("<tr height=30px class='trlight'><td>");			output("%s out of %s gems. ",$gems,$maxgems);			if($session['user']['gems']>=$gemsleft && $gemsleft>0){				rawoutput("<a href='runmodule.php?module=upkeep&op=manage&invest=gems&amount=$gemsleft&dwid=$dwid&type=$typeid'>$invest $gemsleft $dwgems $inupkeep</a></td></tr>");				addnav("","runmodule.php?module=upkeep&op=manage&invest=gems&amount=$gemsleft&dwid=$dwid&type=$typeid");			}else{				rawoutput("</td></tr>");			}			rawoutput("<tr height=30px class='trdark'><td>");			output("%s out of %s gold. ",$gold,$maxgold);			if($session['user']['gold']>=$goldleft && $goldleft>0){				rawoutput("<a href='runmodule.php?module=upkeep&op=manage&invest=gold&amount=$goldleft&dwid=$dwid&type=$typeid'>$invest $goldleft $dwgold $inupkeep</a></td></tr>");				addnav("","runmodule.php?module=upkeep&op=manage&invest=gold&amount=$goldleft&dwid=$dwid&type=$typeid");			}else{				rawoutput("</td></tr>");			}
			break;
	}
	return $args;
}

function upkeep_run(){
	global $session;
	page_header("Dwelling Maintenance");

	$invest = httpget('invest');
	$dwid = httpget('dwid');
	$typeid = httpget('type');
	$op = httpget('op');

	addnav("Back to Management","runmodule.php?module=dwellings&op=manage&dwid=$dwid");
	switch ($op){
		case "manage":
			if (httpget('amount')){
				if(get_module_objpref("dwellingtypes",$typeid,"upkeepgold","upkeep")-get_module_objpref("dwellings",$dwid,"upkeepgold","upkeep")==0 &&				get_module_objpref("dwellingtypes",$typeid,"upkeepturns","upkeep")-get_module_objpref("dwellings",$dwid,"upkeepturns","upkeep")==0 &&				get_module_objpref("dwellingtypes",$typeid,"upkeepgems","upkeep")-get_module_objpref("dwellings",$dwid,"upkeepgems","upkeep")==0){					output("PHEW!  You take a look around and see how much cleaner and orderly everything seems!  You're now set for the remaining %s upkeep days.",get_module_objpref("dwellingtypes",$typeid,"upkeepdays","upkeep")-get_module_objpref("dwellings",$dwid,"upkeepdays","upkeep"));				}else{
					$amount = httpget('amount');					output("`nYou dwelling shows the fruits of your labor.");
					set_module_objpref("dwellings",
					$dwid,
										"upkeep".$invest,
					$amount+get_module_objpref("dwellings",$dwid,"upkeep".$invest,
										"upkeep"),
										"upkeep");					$session['user'][$invest]-=$amount;					debuglog("invested $amount of $invest in the upkeep of his dwelling number $dwid");				}
			}
			break;
	}
	page_footer();
}
?>