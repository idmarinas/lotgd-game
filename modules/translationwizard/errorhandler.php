<?php
switch (httpget('error'))
	{		
	case 1:
		output("`bPlease enter something below!`b");
		output_notl("`n`n");
		break;		
	case 2:
		output("`bNo row has been found! Select anew!`b");
		output_notl("`n`n");			
		break;
	case 3:
		output("`bUnknown error, please report this!`b");
		output_notl("`n`n");			
		break;
	case 4:
		output("`b`\$Save unsuccessful!`b`0");
		output_notl("`n`n");			
		break;		
	case 5:
		output("`b`\$Save successful!`b`0");
		output_notl("`n`n");
		break;
	case 6:
		output("`b`%Delete unsuccessful!`b`0");
		output_notl("`n`n");			
		break;		
	case 7:
		output("`b`%Delete successful!`b`0");
		output_notl("`n`n");
		break;
	case 8:
		output("`b`%Replace unsuccessful!`b`0");
		output_notl("`n`n");			
		break;		
	case 9:
		output("`b`%Replace successful!`b`0");
		output_notl("`n`n");
		break;			
	case 10:
		output("`b`%MySQL error!`b`0");
		output_notl("`n`n");
		break;
		
	}

?>