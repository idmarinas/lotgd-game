<?php
	global $session;
	require_once("modules/metalmine/metalmine_func.php");
	$op = httpget('op');
	$op2 = httpget('op2');
	$op3 = httpget('op3');
	$op4 = httpget('op4');
	$page = httpget('page');
	$level=$session['user']['level'];
	$turns=get_module_setting("ffs");
	$minename=get_module_setting("minename");
	$allprefs=unserialize(get_module_pref('allprefs'));
	$metal1=$allprefs['metal1'];
	$metal2=$allprefs['metal2'];
	$metal3=$allprefs['metal3'];
	$minename=get_module_setting("minename");
	$canary=$allprefs['canary'];
	$canaryset=get_module_setting("canary");
	$mineturnset=get_module_setting("mineturnset");
	$usedmts=$allprefs['usedmts'];
	$mineturns=$mineturnset-$usedmts;
	$header=color_sanitize($minename);
	page_header("%s",$header);
	$dayssince=get_module_setting("dayssince");
	$marray=translate_inline(array("","`)Iron Ore`0","`QCopper`0","`&Mithril`0"));
if ($op=="superuser"){
	require_once("modules/allprefseditor.php");
	allprefseditor_search();
	page_header("Allprefs Editor");
	$subop=httpget('subop');
	$id=httpget('userid');
	addnav("Navigation");
	addnav("Return to the Grotto","superuser.php");
	villagenav();
	addnav("Edit user","user.php?op=edit&userid=$id");
	modulehook('allprefnavs');
	$allprefse=unserialize(get_module_pref('allprefs',"metalmine",$id));
	if ($allprefse['usedmts']=="") $allprefse['usedmts']= 0;
	if ($allprefse['since']=="") $allprefse['since']= 0;
	if ($allprefse['id']=="") $allprefse['id']= 0;
	if ($allprefse['metal1']=="") $allprefse['metal1']= 0;
	if ($allprefse['metal2']=="") $allprefse['metal2']= 0;
	if ($allprefse['metal3']=="") $allprefse['metal3']= 0;
	if ($allprefse['metalhof']=="") $allprefse['metalhof']= 0;
	if ($allprefse['rescuehof']=="") $allprefse['rescuehof']= 0;
	if ($allprefse['oil']=="") $allprefse['oil']= 0;
	if ($allprefse['canary']=="") $allprefse['canary']= "";
	if ($allprefse['drinkstoday']=="") $allprefse['drinkstoday']= 0;
	if ($allprefse['mazeturn']=="") $allprefse['mazeturn']= 0;
	set_module_pref('allprefs',serialize($allprefse),'metalmine',$id);
	if ($subop!='edit'){
		$allprefse=unserialize(get_module_pref('allprefs',"metalmine",$id));
		$allprefse['inmine']= httppost('inmine');
		$allprefse['firstm']= httppost('firstm');
		$allprefse['usedmts']= httppost('usedmts');
		$allprefse['since']= httppost('since');
		$allprefse['help']= httppost('help');
		$allprefse['id']= httppost('id');
		$allprefse['metal']= httppost('metal');
		$allprefse['metal1']= httppost('metal1');
		$allprefse['metal2']= httppost('metal2');
		$allprefse['metal3']= httppost('metal3');
		$allprefse['metalhof']= httppost('metalhof');
		$allprefse['rescuehof']= httppost('rescuehof');
		$allprefse['firststore']= httppost('firststore');
		$allprefse['pickaxe']= httppost('pickaxe');
		$allprefse['helmet']= httppost('helmet');
		$allprefse['oil']= httppost('oil');
		$allprefse['canary']= httppost('canary');
		$allprefse['drinkstoday']= httppost('drinkstoday');
		$allprefse['lilyoffice']= httppost('lilyoffice');
		$allprefse['return']= httppost('return');
		$allprefse['lily']= httppost('lily');
		$allprefse['something']= httppost('something');
		$allprefse['eleave']= httppost('eleave');
		$allprefse['toothy']= httppost('toothy');
		$allprefse['loc1']= httppost('loc1');
		$allprefse['loc2']= httppost('loc2');
		$allprefse['loc9']= httppost('loc9');
		$allprefse['loc11']= httppost('loc11');
		$allprefse['loc17']= httppost('loc17');
		$allprefse['loc24']= httppost('loc24');
		$allprefse['loc25t']= httppost('loc25t');
		$allprefse['loc25a']= httppost('loc25a');
		$allprefse['loc18a']= httppost('loc18a');
		$allprefse['found']= httppost('found');
		$allprefse['mazeturn']= httppost('mazeturn');
		set_module_pref('allprefs',serialize($allprefse),'metalmine',$id);
		output("Allprefs Updated`n");
		$subop="edit";
	}
	if ($subop=="edit"){
		require_once("lib/showform.php");
		$form = array(
			"Metal Mine,title",
			"inmine"=>"Is the player in the Mine?,bool",
			"firstm"=>"Has the player ever been to the mine?,bool",
			"usedmts"=>"How many times did they mine today?,int",
			"since"=>"How many days have passed that the player has logged on since the accident was announced?,int",
			"help"=>"Did the player help at least once with the current mine disaster?,bool",
			"rescuehof"=>"How many times has player helped rescue miners,int",
			"id"=>"What is the id of the last player they fought?,int",
			"metal"=>"What is the current metal they are mining?,enum,0,None,1,Ore,2,Copper,3,Mithril",
			"metal1"=>"Grams of Ore player has:,int",
			"metal2"=>"Grams of Copper player has:,int",
			"metal3"=>"Grams of Mithril player has:,int",
			"metalhof"=>"Total number of grams of all metals ever mined:, int",
			"Store,title",
			"firststore"=>"Has the player ever been to the store?,bool", 
			"pickaxe"=>"What pickaxe did they buy?,enum,0,None,1,Basic,2,Standard,3,Quality",
			"helmet"=>"What helmet did they buy?,enum,0,None,1,Basic,2,Standard,3,Quality",
			"oil"=>"How much oil has been used?,int",
			"canary"=>"What is the name of the canary?,text",
			"drinkstoday"=>"How many drinks of XXX has the player had today?,int",
			"Lily's Office,title",
			"lilyoffice"=>"Has the player ever been to Lily's Office?,bool",
			"return"=>"Has the player found Lily's quest item?,bool",
			"lily"=>"What quest are they on for Lily?,enum,0,None,1,Heart,2,Moon,3,Star,4,Clover,5,Diamond,6,Horseshoe",
			"Miscellaneous Events,title",
			"something"=>"How many times has the player encountered the 'something happens' event?,range,0,6,1",
			"eleave"=>"Is the player in the Elevator?,bool",
			"toothy"=>"How far are they in the Toothy McPicker Story?,enum,0,None,1,The Map,2,Chat with Gruber,3,Fight Bones,4,Returned Bones,5,Elevator,6,Returned Pickaxe,7,Epilogue",
			"Secret Chamber 1,title",
			"loc1"=>"Passed Location 1?,enum,0,No,1,Yes,2,In Process",
			"loc2"=>"Passed location 2?,bool",
			"loc9"=>"Passed location 9?,bool",
			"loc11"=>"Passed location 11?,bool",
			"loc17"=>"Passed location 17?,bool",
			"loc24"=>"Passed location 24?,bool",
			"Secret Chamber 2,title",
			"loc25t"=>"Passed location 25t?,bool",
			"Secret Chamber 3,title",
			"loc25a"=>"Passed location 25a?,bool",
			"loc18a"=>"Passed location 18a?,bool",
			"Map Notes,title",
			"found"=>"Has the player found a Secret Chamber today?,bool",
			"mazeturn"=>"Secret Chamber Turn,int",
		);
		$allprefse=unserialize(get_module_pref('allprefs',"metalmine",$id));
		rawoutput("<form action='runmodule.php?module=metalmine&op=superuser&userid=$id' method='POST'>");
		showform($form,$allprefse,true);
		$click = translate_inline("Save");
		rawoutput("<input id='bsave' type='submit' class='button' value='$click'>");
		rawoutput("</form>");
		addnav("","runmodule.php?module=metalmine&op=superuser&userid=$id");
	}
}
if ($op==""){
	output("`n`c`b`&Metal `)Mine`c`b`n`0");
	$allprefs['inmine']=1;
	set_module_pref('allprefs',serialize($allprefs));
	output("You look down the long road to the Metal Mine. It will take you `@%s %s`0 to get to the mine.",$turns,translate_inline($turns>1?"turns":"turn"));
	if ($session['user']['turns']>=$turns){
		addnav("Travel to the Mine","runmodule.php?module=metalmine&op=enter&op2=turn&op3=$op3");
		output("`n`nAre you ready to go?");
	}else output("`n`nUnfortunately, you don't have the energy to make the trip.");
	addnav("Return to the Forest","runmodule.php?module=metalmine&op=leave");
}
if ($op=="enter"){
	require_once("modules/metalmine/metalmine_enter.php");
	metalmine_enter();
}
if ($op=="firstoffice"){
	require_once("modules/metalmine/metalmine_firstoffice.php");
	metalmine_firstoffice();
}
if ($op=="office"){
	require_once("modules/metalmine/metalmine_office.php");
	metalmine_office();
}
if ($op=="lilyquest"){
	require_once("modules/metalmine/metalmine_lilyquest.php");
	metalmine_lilyquest();
}
if ($op=="priceguide"){
	require_once("modules/metalmine/metalmine_priceguide.php");
	metalmine_priceguide();
}
if ($op=="sellmetal"){
	output("`n`c`b`&Lily's `)Office`0`c`b`n");
	$bottom=floor($allprefs['metal'.$op2]/1000);
	$maximumsell=get_module_setting("maximumsell");
	if ($maximumsell>0){
		$left=$maximumsell-$allprefs['metalsold'];
		if ($bottom>$left) $bottom=$left;
	}
	output("You can sell `^%s kilograms`0 of %s to`& Lily`0 at a price of `^%s gold`0 per kilogram.",$bottom,$marray[$op2],$op3);
	output("How much would you like to sell?");
	output("<form action='runmodule.php?module=metalmine&op=metalsell&op2=$op2&op3=$op3' method='POST'><input name='sell' id='sell'><input type='submit' class='button' value='Sell'></form>",true);
	addnav("","runmodule.php?module=metalmine&op=metalsell&op2=$op2&op3=$op3");
	addnav("Sell Metal");
	if ($metal1>=1000 && $op2<>1) addnav("Sell `)Iron Ore","runmodule.php?module=metalmine&op=sellmetal&op2=1&op3=$op3");
	if ($metal2>=1000 && $op2<>2) addnav("Sell `QCopper","runmodule.php?module=metalmine&op=sellmetal&op2=2&op3=$op3");
	if ($metal3>=1000 && $op2<>3) addnav("Sell `&Mithril","runmodule.php?module=metalmine&op=sellmetal&op2=3&op3=$op3");
	addnav("Other");
	addnav("Discuss Trading Metal","runmodule.php?module=metalmine&op=trade");
	addnav("Lily's Quests","runmodule.php?module=metalmine&op=lilyquest");
	addnav("Leave Lily's Office","runmodule.php?module=metalmine&op=enter");
}
if ($op=="metalsell"){
	require_once("modules/metalmine/metalmine_metalsell.php");
	metalmine_metalsell();
}
if ($op=="trade"){
	require_once("modules/metalmine/metalmine_trade.php");
	metalmine_trade();
}
if ($op=="trademetal"){
	require_once("modules/metalmine/metalmine_trademetal.php");
	metalmine_trademetal();
}
if ($op=="metaltrade"){
	require_once("modules/metalmine/metalmine_metaltrade.php");
	metalmine_metaltrade();
}
if ($op=="store"){
	output("`n`c`b`&General `)Store`0`c`b`n");
	addnav("General Store");
	output("Those funny little bells that people attach to the door ring lightly as you walk in.");
	if (is_module_active("musicshop")){
		$allprefsms=unserialize(get_module_pref('allprefs','musicshop'));
		if ($allprefsms['nummastered']>0) output("You quickly identify the notes as C and G, and give a silent thanks to `%Amleine`0 for your musical training.");
	}
	if ($allprefs['firststore']==0){
		output("`n`nAn old sheep dog looks up at you with disinterest, then goes back to sleep.");
		output("`n`n`Q'Don't mind ol' Zeke.  He won't bite,' says the grizzled old man behind the shop counter.");
		output("He smells of earth and oil with a touch of mint.  It's a strange combination and you take an instant liking to the shopkeeper.");
		output("`Q'My name is Grober.  I used to work that mine over there.  Now I just help others achieve their dreams while I sell the supplies.'");
		output("`n`n`0You look around and see all different types of pickaxes, helmets, and other mining equipment. There's even a barrel labeled `^'Helmet Oil - 5 Gold per Refill'`0. You also notice an area crowded with little bird cages.`n`n");
		output("Then, Grober looks at you slyly and whispers to you `Q'If you're ever feeling under the weather, I can give you a little 'pick-me up' to help.' `0He pulls out a bottle marked `^XXX`0 and waves it at you.`n`n");
		$allprefs['firststore']=1;
		set_module_pref('allprefs',serialize($allprefs));
	}else{
		output("`n`nYou pet ol' Zeke on the head and wave at Grober.  He looks  up at you and smiles. `Q'What can I do for you?'");
	}
	metalmine_storenavs();
}
if ($op=="helmetoil"){
	addnav("General Store");
	output("`n`c`b`&General `)Store`0`c`b`n");
	if ($allprefs['oil']>0){
		output("`Q'Now let's see. You're helmet oil lasts for about `^1000 swings`Q of the 'ole pick-axe, so to speak; doing more intensive exploring wastes more oil.");
		if ($allprefs['oil']>=1000){
			output("I see you're in need of a refill for your helmet.  No problem, that will be `^5 gold`Q.'");
		}else{
			$left=1000-$allprefs['oil'];
			output("You have about `^%s`Q %s left. Would you like to top off your oil? It will still cost you `^5 gold`Q though; this ain't a discount store.'",$left,translate_inline($left>1?"swings":"swing"));
		}
		if ($session['user']['gold']<5) output("`n`nUnfortunately, you don't have enough gold right now.  You'll have to come back later.");
		else{
			addnav("Purchase Oil","runmodule.php?module=metalmine&op=helmetoilbuy");
		}
	}else output("`Q'I see you don't need any oil for your helmet.'");
	metalmine_storenavs();
	blocknav("runmodule.php?module=metalmine&op=helmetoil");
}
if ($op=="helmetoilbuy"){
	addnav("General Store");
	$session['user']['gold']-=5;
	output("`n`c`b`&General `)Store`0`c`b`n");
	output("You fill the little lamp on your helmet with oil and pay the `^5 gold`0.");
	output("`n`n`Q'Is there anything else I can get you?'`0 asks Grober.");
	$allprefs['oil']=0;
	set_module_pref('allprefs',serialize($allprefs));
	metalmine_storenavs();
	blocknav("runmodule.php?module=metalmine&op=helmetoil");
}
if ($op=="storemcp"){
	require_once("modules/metalmine/metalmine_storemcp.php");
	metalmine_storemcp();
}
if ($op=="pickaxe"){
	require_once("modules/metalmine/metalmine_pickaxe.php");
	metalmine_pickaxe();
}
if ($op=="helmets"){
	require_once("modules/metalmine/metalmine_helmets.php");
	metalmine_helmets();
}
if ($op=="purchase"){
	addnav("General Store");
	output("`n`c`b`&General `)Store`0`c`b`n");
	if ($op3==1) $type="general";
	if ($op3==2) $type="standard";
	if ($op3==3) $type="quality";
	$cost=get_module_setting($op2.$op3);
	if ($session['user']['gold']<$cost) output("Grober looks at you as you find you're a little short on gold. `Q'Maybe something a little cheaper?'`0 he asks.");
	else{
		$allprefs[$op2]=$op3;
		set_module_pref('allprefs',serialize($allprefs));
		$session['user']['gold']-=$cost;
		output("You take the %s %s from Grober and pay him `^%s gold`0.  He nods happily at you and asks if there's anything else you'd like.",$type,$op2,$cost);
	}
	if ($op2=="pickaxe") blocknav("runmodule.php?module=metalmine&op=pickaxe");
	if ($op2=="helmet") blocknav("runmodule.php?module=metalmine&op=helmets");
	metalmine_storenavs();
}
if ($op=="canary"){
	addnav("General Store");
	output("`n`c`b`&General `)Store`0`c`b`n");
	if ($allprefs['canary']==""){
		output("`Q'Very few will go into the mine without a trusted canary.  Your purchase includes the bird, cage, and food for the little warbler.");
		output("I can sell you a canary for `^%s gold`Q. I tell you what, you can even name it. Would you like one?'",$canaryset);
		addnav("Purchase a Canary","runmodule.php?module=metalmine&op=buycanary&op2=buy");
	}else{
		output("`Q'I see you have a canary named `^%s`Q. You can give it a different name if you want to though.'",$allprefs['canary']);
		addnav("Change Name","runmodule.php?module=metalmine&op=buycanary&op2=change");
	}
	blocknav("runmodule.php?module=metalmine&op=canary");
	metalmine_storenavs();
}
if ($op=="canaryshort"){
	addnav("General Store");
	output("`n`c`b`&General `)Store`0`c`b`n");
	output("You dig through your pockets for a while pretending that you have enough gold to buy the canary, but eventually you have to admit that you're a little short on funds.");
	output("`n`nGrober looks sympathetic and asks if perhaps you'd like something else.");
	blocknav("runmodule.php?module=metalmine&op=canary");
	metalmine_storenavs();
}
if ($op=="buycanary"){
	require_once("modules/metalmine/metalmine_buycanary.php");
	metalmine_buycanary();
}
if ($op=="bottle"){
	addnav("General Store");
	$hps=$session['user']['hitpoints'];
	$max=$session['user']['maxhitpoints'];
	output("`n`c`b`&General `)Store`0`c`b`n");
	output("You slyly look over at the bottles labelled `&XXX`0.  You give a nod to Grober and point to the bottles.`n`n");
	if (is_module_active("drinks")) $drunk = get_module_pref("drunkeness","drinks");
	if ($drunk>75|| $hps>$max/2 || $allprefs['drinkstoday']>2) {
		if ($drunk>75) output("`Q'I think you've been tippin' the whisky a bit too much already.  I can't give you anything in your current state.'");
		elseif ($allprefs['drinkstoday']>2) output("`Q'I think you've been tippin' the whisky a bit too much already.  I can't give you anything more today.'");
		elseif ($hps>$max/2) output("`Q'I could give you a drink, but it won't do you much good.  There's a time and a place for such a helpful elixir;  and now isn't the time.'");
		metalmine_storenavs();
		blocknav("runmodule.php?module=metalmine&op=bottle");
	}else{
		output("Grober looks you over. `Q'You look like you could use a pick-me-up. Have one on me.'");
		output("`n`n`0He slides you a shot glass and takes the bottle down. With a caustic 'popping' noise, the cork comes off.  You watch as he pours a couple of teaspoons into the glass.");
		output("`n`nAs you grab for it, he gives you a cautionary look.  `Q'You should let it stop steaming, first.'`0 It's not until he mentions it that you suddenly notice a drop of it had landed on the counter.");
		output("Slowly, the steam rises around the drop and corrodes the wood of the counter away.");
		output("`n`nAfter about 10 minutes, the steam subsides and Grober smiles at you. `Q'Bottoms Up!'");
		addnav("Bottoms Up!","runmodule.php?module=metalmine&op=drink");
		addnav("Decline","runmodule.php?module=metalmine&op=store");
	}
}
if ($op=="drink"){
	require_once("modules/metalmine/metalmine_drink.php");
	metalmine_drink();
}
if ($op=="rules"){
	output("`n`c`b`&Metal `)Mine`0`c`b`n");
	output("You wander over to the rules board and take a look.`n`n");
	rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
	rawoutput("<tr class='trhead'><td>");
	output("`c`bWelcome to `&Lily's Metal Mine`c`b");
	rawoutput("</td></tr>");
	rawoutput("<tr class='trhilight'><td>");
	output("`c`n`0It has been `%%s `0%s without an accident`c",$dayssince,translate_inline($dayssince==1?"Day":"Days"));
	output("`n`cPlease obey the following rules for your safety:`c");
	output("`n`&1. Your time in the mine is limited to `^%s`& Mine %s per day.",$mineturnset,translate_inline($mineturnset>1?"Turns":"Turn"));
	output("`n2. It is strongly recommended (although not required) that you bring a canary with you.");
	output("`n3. A quality helmet can guide your way more quickly.");
	output("`n4. A nice pickaxe will make your work more efficient.");
	output("`n5. Raw materials harvested may be sold at Lily's office or you may keep them.");
	rawoutput("</td></tr>");
	rawoutput("</table>");
	addnav("Return","runmodule.php?module=metalmine&op=enter");
}
if ($op=="rescue"){
	output("`n`c`b`&Metal `)Mine`0`c`b`n");
	if (get_module_setting("down")==0){
		output("Luckily, the miners have been rescued!!! The mine is open again.");
		addnav("Continue","runmodule.php?module=metalmine&op=enter");
	}else{
		if ($usedmts<=0){
			output("You decide to help rescue the trapped miners.");
			if (is_module_active("alignment")){
				output("Your `@good deed`0 improves your alignment.");
				increment_module_pref("alignment",2,"alignment");
			}
			output("`n`nYou travel down to the cave-in area and start working with your fellow miners clearing debris.");
			addnav("Continue","runmodule.php?module=metalmine&op=rescue2");
		}else{
			output("You try to go back to help but you're just too tired.  You don't have the `^mining turns`0 needed to help today.");
			addnav("Leave","runmodule.php?module=metalmine&op=enter");
		}
	}
}
if ($op=="rescue2"){
	output("`n`c`b`&Metal `)Mine`0`c`b`n");
	output("Using all your strength, you remove some boulders.");
	output("`n`nAfter a little while, all the other workers stop and everyone listens intently.");
	output("`n`nYou hear tapping!!! That means there are survivors!");
	output("`n`nYou feel invigorated and work even harder.");
	addnav("Continue","runmodule.php?module=metalmine&op=rescue3");
}
if ($op=="rescue3"){
	require_once("modules/metalmine/metalmine_rescue3.php");
	metalmine_rescue3();
}
if ($op=="rescue4"){
	$sql = "SELECT acctid FROM ".db_prefix("accounts")."";
	$res = db_query($sql);
	for ($i=0;$i<db_num_rows($res);$i++){
		$row = db_fetch_assoc($res);
		$allprefsc=unserialize(get_module_pref('allprefs','metalmine',$row['acctid']));
		$allprefsc['help']=0;
		$allprefsc['since']=0;
		set_module_pref('allprefs',serialize($allprefsc),'metalmine',$row['acctid']);
	}
	redirect("runmodule.php?module=metalmine&op=enter");
}
if ($op=="mine" || $op=="travel"){
	require_once("modules/metalmine/metalmine_mineortravel.php");
	metalmine_mineortravel();
}
if ($op=="mining"){
	require_once("modules/metalmine/metalmine_mining.php");
	metalmine_mining();
}
if ($op=="work"){
	require_once("modules/metalmine/metalmine_work.php");
	metalmine_work();
}
if ($op=="crawl"){
	output("`n`c`b`&Metal `)Mine`c`b`n`0");
	output("You start crawling through the tunnel and find a place where you can stand up.");
	output("The ground is kind of smelly and it's raining.");
	output("`n`nYou look down and notice that it's bat guano.  You suddenly have a horrible fear of looking up.");
	addnav("Look Up","runmodule.php?module=metalmine&op=batup");
	addnav("Don't Look Up","runmodule.php?module=metalmine&op=batdown");
}
if ($op=="batup" || $op=="batdown"){
	require_once("modules/metalmine/metalmine_batup.php");
	metalmine_batup();
}
if ($op=="welltake"){
	require_once("modules/metalmine/metalmine_welltake.php");
	metalmine_welltake();
}
if ($op=="wellleave"){
	output("`n`c`b`&Metal `)Mine`c`b`n`0");
	addnav("Metal Mine");
	if (is_module_active('alignment')){
		$evil=get_module_setting("evilalign","alignment");
		$good=get_module_setting("goodalign","alignment");
		$neutral=($evil+$good)/2;
		if (get_module_pref("alignment","alignment")>=$neutral) increment_module_pref("alignment",-1,"alignment");
		if (get_module_pref("alignment","alignment")<=$neutral) increment_module_pref("alignment",+1,"alignment");
	}
	$usedmts=$allprefs['usedmts'];
	$mineturns=$mineturnset-$usedmts;
	if ($mineturns>0) output("You have `^%s Mine %s`0 left.",$mineturns,translate_inline($mineturns>1?"Turns":"Turn"));
	elseif ($session['user']['hitpoints']>0) output("You've used up all your `^Mine Turns`0 for the day. It's probably time for you to head out.");
	if ($usedmts<$mineturnset){
		addnav("Work The Mine More","runmodule.php?module=metalmine&op=work");
		if (get_module_setting("limitloc")<=1) addnav("Travel To a Different Area","runmodule.php?module=metalmine&op=travel");
	}
	addnav("Leave Mine","runmodule.php?module=metalmine&op=leavemine");
}
if ($op=="wellgive"){
	require_once("modules/metalmine/metalmine_wellgive.php");
	metalmine_wellgive();
}
if ($op=="toothybes"){
	output("`n`c`b`qThe Search For Toothy McPicker`c`b`n`0You run up to `qToothy`0 and offer him some water.");
	output("However, he doesn't really seem interested in drinking.  In fact, upon further inspection, you realize that `qToothy`0 isn't looking so good.");
	output("`n`n`qToothy's`0 DEAD!!`n`nNot that this bothers you too much, but it does make you wonder what you should do.");
	output("Your choice is made for you as the bones of `qToothy McPicker`0 rise before you. It seems like the `qGhost of Toothy McPicker`0 is alive and well!`n`n`Q'You're after my `^GOLD`Q!'`0`n`nUh oh.");
	addnav("Fight Toothy's Ghost","runmodule.php?module=metalmine&op=toothy");
}
if ($op=="strange"){
	output("`n`c`b`&Metal `)Mine`c`b`n`0");
	output("You realize it's an elevator shaft!");
	output("`n`nThe elevator car is waiting for you... Will you get in?");
	addnav("Enter the Elevator","runmodule.php?module=metalmine&op=goingdown");
	if ($usedmts<$mineturnset) addnav("Go Back to Working the Mine","runmodule.php?module=metalmine&op=work");
	addnav("Leave Mine","runmodule.php?module=metalmine&op=leavemine");
}
if ($op=="goingdown"){
	output("`n`c`b`&Metal `)Mine`c`b`n`0");
	output("You jump in the elevator and hit the `#'Down'`0 button. At this point, I would just like to pause for a second and reflect on what you've just done.");
	output("`n`n1.  You have wandered into a mine which is KNOWN to have accidents on occasion");
	output("`n2. You just jumped into an elevator leading who-knows-where without checking it over to see if it's safe");
	output("`n3. You just hit the down button.  What were you thinking????");
	output("`n`nWell, it's not my problem.  Off you go!");
	addnav("Continue","runmodule.php?module=metalmine&op=goingdown2");
}
if ($op=="walkaway"){
	require_once("modules/metalmine/metalmine_walkaway.php");
	metalmine_walkaway();
}

if ($op=="savecanary"){
	require_once("modules/metalmine/metalmine_savecanary.php");
	metalmine_savecanary();
}
if ($op=="emergencyleave"){
	require_once("modules/metalmine/metalmine_emergencyleave.php");
	metalmine_emergencyleave();
}
if ($op=="emergencyleave2"){
	output("`n`c`b`&Metal `)Mine`c`b`n`0");
	if (get_module_setting("massyom")) output("A letter is sent to all the people in the kingdom informing them");
	else output("A notice is placed in the news informing all the people in the kingdom");
	output("that miners have been trapped and requesting help to save them.");
	output("`n`nYou feel an adrenaline surge and all your mining turns are available again to help the effort.");
	output("`n`nWill you help?");
	$sql = "SELECT acctid FROM ".db_prefix("accounts")."";
	$res = db_query($sql);
	for ($i=0;$i<db_num_rows($res);$i++){
		$row = db_fetch_assoc($res);
		$allprefs=unserialize(get_module_pref('allprefs','metalmine',$row['acctid']));
		$allprefs['usedmts']=0;
		set_module_pref('allprefs',serialize($allprefs),'metalmine',$row['acctid']);
	}
	addnav("Continue","runmodule.php?module=metalmine&op=enter");
}
if ($op=="contgd2"){
	output("`n`c`b`&Metal `)Mine`c`b`n`0");
	if ($allprefs['eleave']==0) output("You reach the bottom of the shaft and stare around. You can leave the elevator and explore or you can go back.");
	else output("There's not much more to explore so it's time to head back up.");
	addnav("Back to the Elevator","runmodule.php?module=metalmine&op=goingdown2&loc=2");
	if ($allprefs['toothy']==6) output("For some reason you get a very very bad feeling about being here right now.  You decide it'd be better for you to leave right away.");
	elseif ($allprefs['eleave']==0) addnav("Explore","runmodule.php?module=metalmine&op=eexplore");
}
if ($op=="eexplore"){
	require_once("modules/metalmine/metalmine_eexplore.php");
	metalmine_eexplore();
}
if ($op=="ereactor"){
	output("`n`c`b`&Metal `)Mine`c`b`n`0");
	output("You place your hand on the table and wait for the reactor to start.");
	switch(e_rand(1,2)){
		case 1:
			output("`n`nThe gnome looks at you like you've been tipping the mead too much.");
			output("'`#I said `3'Start the Tractor'`#.  What the heck are you doing sitting there with your hand on the table?'`0");
			output("`n`nThe gnome puts a little toy tractor down on the table and pushes a button.  Through an amazing feat of mechanical genius, the toy tractor rolls foward pulling a cart full of tiny mechanical cows.");
			output("`n`nYou enjoy the little show immensely!");
			apply_buff('metalmine',array(
				"name"=>"`&Tractor High",
				"rounds"=>3,
				"atkmod"=>1.05,
				"roundmsg"=>"You fondly remember the little train.",
			));
		break;
		case 2:
			output("Suddenly, the ceiling starts to collapse!");
			output("`n`nYou look at the gnome but he just shrugs and slides out a back door.");
			output("As you're leaving, a rock hits you and cuts your cheek. The scar is cool and you `&Gain 1 Charm`0.");
			$session['user']['charm']++;
		break;
	}
	addnav("Leave","runmodule.php?module=metalmine&op=contgd2");
}
if ($op=="eltoothy"){
	output("`n`c`b`&Metal `)Mine`c`b`n`0");
	output("You step off the elevator and look around.  There's not much here... it seems like `qToothy`0 was just getting started on excavating here.");
	output("`n`nYou search for as long as you can and start to get frustrated.  It's time to head back.");
	output("As you walk back to the elevator, you notice something that catches your eye.");
	output("`n`nYou've found `qToothy McPicker's Pickaxe`0!!");
	$allprefs['toothy']=$allprefs['toothy']+1;
	set_module_pref('allprefs',serialize($allprefs));
	output("`n`nTime to head back to the main part of the mine.");
	addnav("Leave","runmodule.php?module=metalmine&op=contgd2");
}
if ($op=="leavemine"){
	output("`n`c`b`&Metal `)Mine`c`b`n`0");
	output("Luckily, getting out of the mine is pretty easy.  Soon enough you find yourself back at the mine shaft entrance.");
	addnav("Continue","runmodule.php?module=metalmine&op=enter");
}
if ($op=="leavechamber"){
	output("`n`c`b`&Metal `)Mine`c`b`n`0");
	output("You decide to leave the chamber and head back to the mine. As soon as you do, there's a cave-in! The chamber is buried!!`n`n");
	if ($mineturns>0) output("You have `^%s Mine %s`0 left.",$mineturns,translate_inline($mineturns>1?"Turns":"Turn"));
	else output("You've used up all your Mine Turns for the day. It's probably time for you to head out.");
	if ($usedmts<$mineturnset) addnav("Mine for More Metal","runmodule.php?module=metalmine&op=work");
	addnav("Leave Mine","runmodule.php?module=metalmine&op=leavemine");
	$allprefs=unserialize(get_module_pref('allprefs'));
	$allprefs['loc1']=0;
	$allprefs['loc2']=0;
	$allprefs['loc9']=0;
	$allprefs['loc11']=0;
	$allprefs['loc17']=0;
	$allprefs['loc24']=0;
	set_module_pref('allprefs',serialize($allprefs));
	metalmine_clearmap();
}
if ($op=="leavechamber2"){
	output("`n`c`b`&Metal `)Mine`c`b`n`0");
	output("You decide to leave the chamber and head back to the mine. As soon as you do, there's a cave-in! The chamber is buried!!`n`n");
	if ($mineturns>0) output("You have `^%s Mine %s`0 left.",$mineturns,translate_inline($mineturns>1?"Turns":"Turn"));
	else output("You've used up all your Mine Turns for the day. It's probably time for you to head out.");
	if ($op2=="top" && $usedmts<$mineturnset) addnav("Mine for More Metal","runmodule.php?module=metalmine&op=work");
	if ($op2=="bottom" && $usedmts<$mineturnset) addnav("Mine for More Metal","runmodule.php?module=metalmine&op=mining");
	addnav("Leave Mine","runmodule.php?module=metalmine&op=leavemine");
	$allprefs=unserialize(get_module_pref('allprefs'));
	$allprefs['loc25a']=0;
	$allprefs['loc18a']=0;
	set_module_pref('allprefs',serialize($allprefs));
	metalmine_clearmap();
}
if ($op=="leavetunnel"){
	output("`n`c`b`&Metal `)Mine`c`b`n`0");
	if ($op2=="start") output("You decide to leave the chamber and head back to the mine. As soon as you do, there's a cave-in! The chamber is buried!!`n`n");
	if ($op2=="end"){
		output("You dive for the exit and escape the chamber just in time! You turn to see it collapse... that was too close for comfort.");
		$allprefs['loc24t']=0;
		$expmultiply = e_rand(8,15);
		$expbonus=$session['user']['dragonkills'];
		$expgain =($session['user']['level']*$expmultiply+$expbonus);
		$session['user']['experience']+=$expgain;
		output("`n`n`#You gain `^%s `#experience!`0`n`n",$expgain);
	}
	$allprefs['loc25t']=0;
	set_module_pref('allprefs',serialize($allprefs));
	metalmine_clearmap();
	if ($mineturns>0) output("You have `^%s Mine %s`0 left.",$mineturns,translate_inline($mineturns>1?"Turns":"Turn"));
	else output("You've used up all your Mine Turns for the day. It's probably time for you to head out.");
	if ($usedmts<$mineturnset) addnav("Mine for More Metal","runmodule.php?module=metalmine&op=work");
	addnav("Leave Mine","runmodule.php?module=metalmine&op=leavemine");
}
if ($op=="leavegoingdown2"){
	output("`n`c`b`&Metal `)Mine`c`b`n`0");
	output("You decide to leave the elevator and head back to the mine. As soon as you do, the elevator cable breaks! You hear a huge crashing noise as it falls to its destruction!!`n`n");
	$allprefs['eleave']=0;
	metalmine_clearmap();
	if ($mineturns>0) output("You have `^%s Mine %s`0 left.",$mineturns,translate_inline($mineturns>1?"Turns":"Turn"));
	else output("You've used up all your Mine Turns for the day. It's probably time for you to head out.");
	if ($usedmts<$mineturnset) addnav("Mine for More Metal","runmodule.php?module=metalmine&op=work");
	addnav("Leave Mine","runmodule.php?module=metalmine&op=leavemine");
}

if ($op=="leave"){
	$allprefs['inmine']=0;
	set_module_pref('allprefs',serialize($allprefs));
	redirect("forest.php");
}
if ($op == "metalhof" || $op=="rescuehof") {
	require_once("modules/metalmine/metalmine_hof.php");
	metalmine_hof();
}
$knownmonsters = array('trap','mummy','miner','bear','player','cavetroll','toothy','welltroll');
if (in_array($op, $knownmonsters) || $op == "fight") {
	metalmine_fight($op);
	die;
}
if ($op=="goingdown2"){
	require_once("modules/metalmine/metalmine_goingdown2.php");
	metalmine_goingdown2();
}
if ($op=="chamber"){
	require_once("modules/metalmine/metalmine_chamber.php");
	metalmine_chamber();
}
if ($op=="chamber2"){
	require_once("modules/metalmine/metalmine_chamber2.php");
	metalmine_chamber2();
}
if ($op=="tunnel"){
	require_once("modules/metalmine/metalmine_tunnel.php");
	metalmine_tunnel();
}
function metalmine_runevent($type){
	global $session;
}
page_footer();
?>