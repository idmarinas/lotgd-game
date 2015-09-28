<?php
	module_addhook("village");
	module_addhook("newday");
	module_addhook("footer-hof");   
	module_addhook("village-desc");
	module_addhook("changesetting");
	module_addeventhook("forest", "return 100;");

//RPGee.com - added in newday-runonce for additional grotto settings
	module_addhook("newday-runonce");
//END RPGee.com
?>