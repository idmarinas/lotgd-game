<?php
function metalmine_office(){
	$allprefs=unserialize(get_module_pref('allprefs'));
	$metal1=$allprefs['metal1'];
	$metal2=$allprefs['metal2'];
	$metal3=$allprefs['metal3'];
	output("`n`c`b`&Lily's `)Office`0`c`b`n");
	output("`&Lily`0 greets you and welcomes you to her office.");
	output("`n`nYou take a look around and notice a picture of a duck on the wall.");
	addnav("Lily's Office");
	if ($allprefs['lilyoffice']==0){
		output("You ask Lily about the duck and she looks at you as if you just had your head chopped off.");
		output("`n`n`&'You mean you don't like ducks?  Sheesh.  You're weird.'`0`n`n");
		output("You decide not to ask about ducks ever again.");
		$allprefs['lilyoffice']=1;
	}else output("You decide not to ask about the duck.");
	$total=$allprefs['metal1']+$allprefs['metal2']+$allprefs['metal3'];
	if ($metal1>=1000 || $metal2>=1000 || $metal3>=1000){
		output("`n`n`&'I see you've done well in the mine.  If you're interested in selling some of your metal, I'd be willing to purchase it from you.");
		output("I purchase in bulk quantities of `^1000 grams`& at a time.'");
		addnav("Sell Metal","runmodule.php?module=metalmine&op=priceguide");
	}else{
		output("`n`n`&'I would be willing to purchase any metal that you mine.  However, I only purchase in bulk quantities.");
		output("After you've mined at least `^1000 grams`& of any metal I can consider purchasing it from you.'");
		addnav("Review Prices","runmodule.php?module=metalmine&op=priceguide");
	}
	if ($metal1>=200 || $metal2>=200 || $metal3>=200){
		output("`n`n'In addition, I would be willing to trade metals with you.  The best I can do is a 2 to 1 trade.  For every `^200 grams `& of a metal that you give me, I can give you `^100 grams`& of the metal of your choice.'");
	}else output("`n`n'You don't have enough of any particular metal for trading at this time.  Once you get over `^200 grams`& of a metal we can discuss making a trade.'");
	addnav("Discuss Trading Metal","runmodule.php?module=metalmine&op=trade");
	addnav("Lily's Quests","runmodule.php?module=metalmine&op=lilyquest");
	addnav("Leave Lily's Office","runmodule.php?module=metalmine&op=enter");
	set_module_pref('allprefs',serialize($allprefs));
}
?>