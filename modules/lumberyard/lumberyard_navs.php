<?php
function lumberyard_navs(){
	addnav("`@To the Forest","forest.php");
	addnav("Cut Trees","runmodule.php?module=lumberyard&op=work");
	addnav("Plant Trees","runmodule.php?module=lumberyard&op=planttree");
	addnav("Remind Me of the Rules","runmodule.php?module=lumberyard&op=rules");
	addnav("`@T`7he `@F`7oreman's `@O`7ffice","runmodule.php?module=lumberyard&op=office");
}
?>