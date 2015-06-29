<?php
function lumberyard_rules(){
	$fullsize=get_module_setting("fullsize");
	$remainsize=get_module_setting("remainsize");
	$lumberturns=get_module_setting("lumberturns");
	$plantneed=get_module_setting("plantneed");
	$clearcutter=get_module_setting("clearcutter");

	output("`@1. Everyday you may spend`b %s turns`b in the lumber yard.`n`n",$lumberturns);
	output("`22. Your job here is to cut trees down and make`& squares of wood`2. A square is equal to 100 square feet of wood. I supply everything but the labor; and you get to keep 50% of the squares you cut!`n`n");
	output("`@3.  There are `Q3 phases `@to completing a square of wood. `Q`nPhase 1:`@ Chop a tree down and trim it. `n`QPhase 2:`@ Transport the tree back to the mill. `Q`nPhase 3:`@ Cut the tree on the mill saw into 10 foot long boards that are a foot wide.`nThis will make 2 squares of wood; one for you and one for me.`n`n");
	output("`24. With your squares of wood, you will be able to do some wonderful things around the village. But	that's for you to figure out on your own. I'm not going to tell you how to run your life.`n`n");
	output("`@5. If you ever want to go over your account or discuss selling your wood to me, just meet me in my office.`n`n");
	output("`26.  The forest is not endless. In fact, there are a limited number of trees available to be harvested. When the yard is full, it will have a total of`6 %s trees`2 that can be cut down. Currently, there are`6 %s trees`2.",$fullsize,$remainsize);
	output("The yard is dangerously low when there are less than`6 %s trees `2in the forest.`n`n",$plantneed);
	output("`@7.  Instead of chopping down trees, you may instead decide to plant trees.  It takes 2 turns to plant a tree.`n`n");
	output("`^The foreman looks around nervously then leans in to whisper in your ear... `n`n`#'But there is something you need to know. There's a madman that comes around... There's nothing I can do to stop him. He `b`$ HATES `b`#trees.");
	output("Every once in a while you'll arrive to the lumber yard and find that it was clear cut. That means `b`$ %s `b`#has struck.",$clearcutter);
	output("When that happens, I'll need you to help plant new trees before you'll be able to cut more wood.'`n`n'Ready to work?'");

	if (get_module_setting("leveladj")==1 || get_module_setting("levelreq")>1 || get_module_setting("maximumsell")>0) output("`&`n`n`b`cCalculation of Wood Reimbursement:`c`b");
	if (get_module_setting("leveladj")==1) output("`nPay for stone is based on your current level.  The higher your level, the higher price you'll be able to negotiate for your wood.`n");
	if (get_module_setting("levelreq")>1) output("`nYou will need to be at least `^level %s`& to sell your wood.`n",get_module_setting("levelreq"));
	if (get_module_setting("maximumsell")>0) Output("`nYou may sell up to `^%s`& %s of wood per day.`n",get_module_setting("maximumsell"),translate_inline(get_module_setting("maximumsell")>1?"squares":"square"));
}
?>