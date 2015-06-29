<?php
function metalmine_firstoffice(){
	output("`n`c`b`&Lily's `)Office`0`c`b`n");
	output("You settle down into a sparse but comfortable office and wait for `&Lily`0 to explain more.");
	output("`n`n`&'My mine is very peculiar in that it contains three very different types of metal.'");
	output("`n`n`0Lily takes out three unprocessed pieces of metal and lays them on the desk before you; a `)black`0 piece, an `Qorange-`@green`Q piece`0, and a `&white`0 piece.  She picks up the `)black`0 piece.");
	output("`n`n`&'This is `)Iron Ore`&.  It is a wonderful metal that we create most of our weapons out of.  It is called the 'Blacksmith's Blessing'.   Without `)Iron Ore`&, our civilization would be set back hundreds of years.'`0  She puts down the `)iron ore`0 and picks up the `Qorange-`@green`0 piece.");
	output("`n`n`&'This is `QCopper`&.  Copper is combined with other metals to make strong alloys such as bronze and brass.  It also has an inherent trade value.'`0 Lastly, she picks up the `&white`0 piece.");
	output("`n`n`&'Our most rare and precious metal is `bMithril`b.  This is a type of silver that makes weapons stronger and more deadly.'");
	output("`n`n'You can mine any of these metals here.");
	if (is_module_active("alignment")){
		$align=get_module_pref("alignment","alignment");
		output("Finding the metal that you want may not be as easy as you'd think.  There seems to be some magic in the mine. There's a tendency for `\$Evil`& people to be more successful at finding `)Iron ore`&. `^Neutral`& people excel at finding `QCopper`&, and of course `@Good`& people find more `bMithril`b.'");
		output("`0She looks you over and says");
		if ($align<get_module_setting("evilalign","alignment")) output("`&'I have a feeling you'll be pretty good at finding `)Iron Ore`&.'");
		elseif ($align>get_module_setting("goodalign","alignment")) output("`&'Clearly you'll excel at finding `bMithril`b.'");
		else output("`&'You're going to find a lot of `QCopper`&.'");
	}
	output("`n`n`&'Unfortunately, you can't work the mine without the right equipment.  You will notice a small general store just at the mine entrance.");
	output("You will need to purchase the following items for proper mining:");
	output("`n`n`cHelmet with Mining Light`nPickaxe`nA Canary`c");
	output("`nIn order to mine, you will have to get to a location that looks favorable.  That's what the light is for.  The helmet is to protect you if there's a cave-in.  Don't worry though, accidents are very rare.");
	output("The Pick-Axe; well if you don't know what that's for then you're probably at the wrong place.  The canary isn't required but it can save your life and I strongly recommend you bring one with you.");
	output("The rest of the rules are posted by the mine for you to review on your own.'");
	if (get_module_setting("down")==1) output("`n`nUnfortunately, there has recently been an accident at the mine.  Your help is needed to rescue some of the miners trapped there.  Please help.'");
	output("`n`n`&Lily`0 wishes you good luck and sends you to the mine entrance.");
	addnav("To the Mine","runmodule.php?module=metalmine&op=enter");
}
?>