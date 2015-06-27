<?php
$what=httpget('deletemode');
switch ($what) {

	case "delete":
		$modules=unserialize(stripslashes(rawurldecode(httpget('delmodule'))));
		while (list($key,$modulename)=each($modules)){
			$sql="DELETE FROM ".db_prefix('translations')." WHERE uri='module-".$modulename."';";
			$result=db_query($sql); //debug($sql);
			output("Translations for module `^%s`0 deleted from db`n",$modulename);
		}		
		break;
		
	default:
		$post=httppost('module');
		if ($post=="") $post=httpget('module');
		if (!is_array($post)) $post=array($post);
		output("You just deinstalled:");
		output_notl("`n`n");
		while (list($key,$modulename)=each($post)){
			output("Module: `^%s`0`n",$modulename);
		}
		output_notl("`n`n");
		output("Delete translations from table translations too?");
		output_notl("`n");
		$t=translate_inline("Yes, delete them");
		$post=rawurlencode(serialize($post));
		rawoutput("<a href='runmodule.php?module=translationwizard&op=deleteuninstalled&deletemode=delete&delmodule=$post'><big>$t</big></a>");
		addnav('','runmodule.php?module=translationwizard&op=deleteuninstalled&deletemode=delete&delmodule='.$post);
		output("`\$`iAttention, no further confirmation`i");
		output_notl("`n`n");
		//$get and $post already defined from the call //not working
		/*$skript="<script type='text/javascript' language='JavaScript'>
					<!-- Begin
					function openwindow() {
					Fensteroptionen = 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0';
					Breite=300;
					Hoehe=500;
					Arbeitsfenster = window.open('', '', Fensteroptionen + ',width=' + Breite + ',height=' +  Hoehe);
					Arbeitsfenster.focus();
					Arbeitsfenster.document.open();
					document.write('<html><head>');
					document.write('<title>".translate_inline("Translation Wizard")."</title>');
					document.write('</head>');
					document.write('<body leftmargin=\"0\" marginheight=\"0\" marginwidth=\"0\" topmargin=\"0\">');				
					document.write('".translate_inline("Do you want to proceed and delete translations for this/these modules you selected?")."');
					document.write('<a href=runmodule.php?module=translationwizard&op=delete_popup&mode=continue&get=$get&post=$post>".translate_inline("Continue")."</a>');
					document.write('</body></html>');
					}
					//  End -->
					</script>
					";
		 $args = array("script"=>$skript);
		*/
	break;
}



?>