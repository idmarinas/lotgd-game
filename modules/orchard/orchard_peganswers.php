<?php
function orchard_peganswers(){
	switch(e_rand(1,3)){
		case 1:
			output("`5\"`@What was the name of the province that my people came from?`5\"");
			addnav("Parantray","runmodule.php?module=orchard&op=1");
			addnav("Ofrenhaven","runmodule.php?module=orchard&op=2");
			addnav("Gravenstaff","runmodule.php?module=orchard&op=3");
			addnav("Ordenmarn","runmodule.php?module=orchard&op=4");
			addnav("Priselton","runmodule.php?module=orchard&op=5");
			addnav("Havermark","runmodule.php?module=orchard&op=6");
			addnav("Traktrin","runmodule.php?module=orchard&op=7");
			addnav("Garensow","runmodule.php?module=orchard&op=8");
			addnav("Rikenham","runmodule.php?module=orchard&op=9");
			addnav("Schantiln","runmodule.php?module=orchard&op=10");
		break;
		case 2:
			output("`5\"`@What was the first scultpure I made out of glass?`5\"");
			addnav("Duck","runmodule.php?module=orchard&op=1");
			addnav("Butterfly","runmodule.php?module=orchard&op=2");
			addnav("Wagon","runmodule.php?module=orchard&op=3");
			addnav("Helmet","runmodule.php?module=orchard&op=4");
			addnav("Unicorn","runmodule.php?module=orchard&op=5");
			addnav("Tree","runmodule.php?module=orchard&op=6");
			addnav("Flower","runmodule.php?module=orchard&op=7");
			addnav("Penguin","runmodule.php?module=orchard&op=8");
			addnav("Frog","runmodule.php?module=orchard&op=9");
			addnav("Turtle","runmodule.php?module=orchard&op=10");
		break;
		case 3:
			output("`5\"`@How many months did it take me to master my first suit of armor?`5\"");
			addnav("36","runmodule.php?module=orchard&op=1");
			addnav("37","runmodule.php?module=orchard&op=2");
			addnav("38","runmodule.php?module=orchard&op=3");
			addnav("39","runmodule.php?module=orchard&op=4");
			addnav("40","runmodule.php?module=orchard&op=5");
			addnav("41","runmodule.php?module=orchard&op=6");
			addnav("42","runmodule.php?module=orchard&op=7");
			addnav("43","runmodule.php?module=orchard&op=8");
			addnav("44","runmodule.php?module=orchard&op=9");
			addnav("45","runmodule.php?module=orchard&op=10");
		break;
	}
}
?>