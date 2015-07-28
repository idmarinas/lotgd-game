<?
	$dwid = get_module_pref("sleepingindwinn");
	$dwname = get_module_setting("dwname","dwinns");
	if ($dwid!=0){
		set_module_pref("sleepingindwinn",0);
		
		$sql = "UPDATE " . db_prefix("dwinns") . " SET guests=guests-1 WHERE dwid=$dwid";
		db_query($sql);
		$sql = "SELECT stars FROM " . db_prefix("dwinns") . " WHERE dwid=$dwid";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		
		switch($row['stars']){
			case "0":
				$session['user']['hitpoints']=1;
				output("`2`nYou had rats and cockroaches crawling around and on top of you the whole time. You haven't slept a wink. You are even more tired now, than when you went to sleep.");
				break;
			case "1":
				$session['user']['hitpoints']=round($session['user']['maxhitpoints']*0.25);
				output("`2`nThe cats kept the rats away from your room, but their singing woke you up at 5 am. Next time you're throwing them a boot or two.");
				break;
			case "2":
				$session['user']['hitpoints']=round($session['user']['maxhitpoints']*0.5);
				output("`2`nThe cook was really not that bad *burp*, but you ate too much and even after a good night sleep *burp* you feel full. You wonder where *burp* you'll be able to find an alka-selzer *burp*.");
				break;
			case "3":
				$session['user']['hitpoints']=round($session['user']['maxhitpoints']*0.75);
				output("`2`nThe bedding of the %s`2 you slept was really confy. The next room neighbours must have felt the same way too, they kept screaming 'Oh yeah! until way over midnight.",$dwname);
				break;
			case "4":
				$session['user']['hitpoints']=round($session['user']['maxhitpoints']*0.9);
				output("`2`nThe soothing music from the jukebox on the floor below you let you sleep like a baby.");
				break;
			case "5":
				output("`2`nYou wake up to the smell of bacon and eggs beeing made from the room service. Ooh, they even have freshly pressed orange juice!");
				break;
			case "6":
				$session['user']['hitpoints']=round($session['user']['maxhitpoints']*1.1);
				output("`2`nThe tight clothes of the staff gave you some really nice dreams! You wake up with energy to spare!");
				break;
			case "7":
				$session['user']['charm']+=2;
				output("`2`nYou slept in an %s`2 with marble floors. Whoever thought you didn't have class, now stands corrected. You win two charm.",$dwname);
				break;
			case "8":
				$session['user']['gold']+=e_rand(1*$session['user']['level'],20*$session['user']['level']);
				output("`2`nAs you pass by the water fountain you notice, that the staff hasn't pick up the coins from yesterday yet. You pocket the wet gold coins quickly.");
				break;
			case "9":
				$session['user']['hitpoints']=round($session['user']['maxhitpoints']*1.25);
				$session['user']['turns']++;
				output("`2`nYou had a nice long swim before going to bed last night. You feel motivated and well rested. You can fight for one more round.");
				break;
			case "10":
				$session['user']['hitpoints']=round($session['user']['maxhitpoints']*1.5);
				$session['user']['turns']++;
				$session['user']['charm']++;
				output("`2`nAahh, a life restoring sauna. This is the real life, this is how the very rich live like, in pure blissful decadence. You wake up well rested, motivated enough for an extra fight and with a charming smile on your face.");
				break;
		}
		debuglog("woke up in an inn with " . $row['stars'] . " stars");
	}
	$userid = $session['user']['acctid'];
	$sql = "SELECT dwid, name FROM " . db_prefix("dwellings") . " WHERE ownerid='$userid' AND type='dwinns' AND status='1'";
	$result = db_query($sql);
	if (db_num_rows($result)>0){
		$profit = 0;
		while ($row = db_fetch_assoc($result)){
			$dwid = $row['dwid'];
			$name = $row['name'];
			
			$sql2 = "SELECT logrooms,logmeals,logdrinks,villageadd,meals,drinks,stars,drinkqual,brewdays,closed FROM " . db_prefix("dwinns") . " WHERE dwid=$dwid";
			$result2 = db_query($sql2);
			$row2 = db_fetch_assoc($result2);
			
			$logrooms = $row2['logrooms'];
			$logmeals = $row2['logmeals'];
			$logdrinks = $row2['logdrinks'];
			$villageadd = $row2['villageadd'];
			$meals = $row2['meals'];
			$drinkqual = $row2['drinkqual'];
			$drinks = $row2['drinks'];
			$brewdays = $row2['brewdays'];
			$closed = $row2['closed'];
			
			if($closed>0) output("`$ `nYour %s`$ will be closed for repairs/renovation for `@%s`$ more days.`n",$dwname,$closed);
			
			if($name=="")
				$name="unnamed";
			$profit+=$logrooms+$logmeals+$logdrinks;
			
			output("`2`nYour %s`2 %s`2 sold meals for %s gold, sold drinks for %s gold and rented rooms for %s gold.",$dwname,$name,$logmeals,$logdrinks,$logrooms);

			if($villageadd>0) 
				$villageadd--;
			
			if($meals>0){
				$maxmealrand = (get_module_setting("maxmealrand","dwinns")*$row2['stars'])+1;
				$random = e_rand(0,$maxmealrand);
				if($random==0){
					$maxrottenmeals = (get_module_setting("maxrottenmeals","dwinns")/100)*$meals;
					$rotten = e_rand(1,$maxrottenmeals);
					$meals-=$rotten;
					output("`2`nDuring the night `5%s meals`2 go rotten.",$rotten);
					debuglog("$rotten meals go rotten in dwelling $dwid");
				}
			}
			if($drinkqual > 0){
				if($brewdays == 0){
					$drinkqual = 0;
				}
				else{
					$brewdays--;
				}
			}
			if($drinks>0){
				$random = e_rand(0,$drinkqual*10);
				if($random==0){
					$maxstaleales = (get_module_setting("maxstaleales","dwinns")/100)*$drinks;
					$stale = e_rand(1,$maxstaleales);
					$drinks-=$stale;
					output("`2`nDuring the night `5%s liters of ale`2 go stale.",$stale);
					debuglog("$stale liters of ale go stale in dwelling $dwid");
				}
			}
			$sql = "UPDATE " . db_prefix("dwinns") . " SET villageadd='$villageadd', logrooms='0', logmeals='0', logdrinks='0', drinks='$drinks', meals='$meals', drinkqual='$drinkqual', brewdays='$brewdays', statticks=statticks+1 WHERE dwid='$dwid'";
			db_query($sql);
		}
		$taxes = round($profit*0.1);
		output("`2`nYour overall profit was of %s gold, but %s went for taxes, which has been deposited in your bank account.",$profit,$taxes);
		$session['user']['goldinbank']+=($profit-$taxes);
		debuglog("recieved $profit gold from profit from his/her inns");	
	}
	
	set_module_pref("dwinnsmeals",0,"dwinns");
?>
