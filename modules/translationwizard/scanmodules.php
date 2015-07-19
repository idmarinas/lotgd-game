<?php
switch (httpget('error')) {
	case 1:
		output("`b`%Insert successful!`b`0");
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
		output("`b`%Insert unsuccessful!`b`0");
		output_notl("`n`n");			
		break;		
	case 7:
		output("`bPlease enter something below!`b");
		output_notl("`n`n");			
		break;					
}

$trans = httppost("nametext");
if (is_array($trans))  //setting for any intexts you might receive
	{
	$nametext = $trans;
	}else
	{
	if ($trans) $nametext = array($trans);
	else $nametext = array();
	}	
$trans = httppost("translatedtid");	
if (is_array($trans))  //setting for any intexts you might receive
	{
	$translatedtid = $trans;
	}else
	{
	if ($trans) $translatedtid = array($trans);
	else $translatedtid = array();
	}	
switch($mode)
{
case "insert":
	$transintext = httppost("inserttext");
	if (!httppost('insertandeditchecked')) {
		require_once("./modules/translationwizard/scanmodules_func.php");
		wizard_insertfile($transintext,$languageschema,true);
		redirect('runmodule.php?module=translationwizard&op=scanmodules&error=1'); //back to the roots, no error but success
	} else { //if edit button was pushed
		rawoutput("<form action='runmodule.php?module=translationwizard&op=scanmodules&mode=saveedited' method='post'>");
		addnav("", "runmodule.php?module=translationwizard&op=scanmodules&mode=saveedited");
		//rawoutput("<input type='submit' class='button' value='". translate_inline("Show") ."'>"); //no longer necessary
		require("./modules/translationwizard/editchecked.php"); //if you want to edit some translations at a time
	}
	break;

case "saveedited":
	$redirectonline=0;
	require("./modules/translationwizard/multichecked.php");//if you want to copy the checked translations with intext and the entered outtext, this commences the copy process
	output("Job done");
	break;
	
case "scan":
require_once("./modules/translationwizard/scanvalidfiles.php");
require_once("./modules/translationwizard/scanmodules_func.php");
if (!httpget('how')=='multi') {
	rawoutput("<form action='runmodule.php?module=translationwizard&op=scanmodules&mode=scan' name='listenauswahl' method='post'>");
	addnav("", "runmodule.php?module=translationwizard&op=scanmodules&mode=scan");
	$one=wizard_showvalidfiles();
	output("`nChoose alternate scheme (valid for main file+libs, i.e. 'module-translationwizard'):`n");
	rawoutput("<input id='input' name='alternate' width=55>");
	rawoutput("</form>");
	$lookfor=httppost('lookfor');
	$alternate=httppost('alternate');
	if ($alternate) $ausgabe=wizard_scanfile($lookfor,false,$alternate);
		else  $ausgabe=wizard_scanfile($lookfor,false);
	
} else {
	$lookfor=httppost('lookfor');
	$alternate=httppost('alternate');
	if ($alternate=='' || !$alternate) {
		$posi=strrpos($lookfor,"/");
		$name=substr($lookfor,$posi+1,strlen($lookfor)-$posi-5);
		if (!$posi) $name=substr($lookfor,0,strrpos($lookfor,"."));
		if (strstr($lookfor,"modules")) $name="module-".$name;	
		$alternate=$name; //secure, if no standard given, take the name of the main file
	};
	//$transintext = lib files etc
	$ausgabe=wizard_scanfile($lookfor,false,$alternate); //main file
	//now merge the libs with the alternate scheme to the main
	while (list($key,$val)=each($transintext)) {
		$ausgabe=array_merge($ausgabe,wizard_scanfile($val,false,$alternate));
	}
}
rawoutput("<form action='runmodule.php?module=translationwizard&op=scanmodules&mode=insert' name='editfeld' method='post' >");
addnav("", "runmodule.php?module=translationwizard&op=scanmodules&mode=insert");
output("%s rows have been found.",sizeof($ausgabe));
output_notl("`n`n");
rawoutput("<table border='0' cellpadding='2' cellspacing='0'>");
rawoutput("<tr class='trhead'><td></td><td>". translate_inline("Namespace") ."</td><td>". translate_inline("Intext")."</td><td>".translate_inline("Translated")."</td></tr>");	
//prepare sql
$sql="SELECT tid,outtext FROM ".db_prefix("translations")." WHERE ";
$wasthereanuntranslated=0;
//end
while (list($key,$row)=each($ausgabe))
{
		$result=db_query($sql."intext='".addslashes($row['text'])."' AND language='".$languageschema."' AND uri='".$row['schema']."';");
		if (db_num_rows($result)==0) {
			$alreadytranslated=translate_inline("No");
			$trans=0;
			$outtext='';
			$tid=0;
			$wasthereanuntranslated=1;
		}elseif (db_num_rows($result)==1) {
			$alreadytranslated=translate_inline("Yes");
			$rownow=db_fetch_assoc($result);
			$tid=$rownow['tid'];
			$outtext=rawurlencode($rownow['outtext']);
			$trans=1;
		} else {
			$alreadytranslated=translate_inline("`\$Multiple found! Clean your stuff up!`0");
			$rownow=db_fetch_assoc($result);
			$tid=$rownow['tid'];
			$outtext=rawurlencode($rownow['outtext']);
			$trans=1;
		}
		$row=array_merge($row,array("outtext"=>$outtext,"tid"=>$tid));
		rawoutput("<input type='hidden' name='translated[]' value='$trans' >");
		rawoutput("<tr class='".($key%2?"trlight":"trdark")."'><td>");
		rawoutput("<input type='checkbox' name='inserttext[]' value='".rawurlencode(serialize($row))."' >");
		rawoutput("</td><td>");
		rawoutput($row['schema']);
		rawoutput("</td><td>");
		rawoutput(htmlentities(stripslashes($row['text']),ENT_COMPAT,$coding));
		rawoutput("</td><td>");
		output_notl($alreadytranslated);
		rawoutput("</td></tr>");
		}
		rawoutput("</table>");
	//some check/uncheck all
	$all=translate_inline("Check all");
	$none=translate_inline("Uncheck all");
	$alltranslated=translate_inline("Check all Untranslated");
	$nonetranslated=translate_inline("Uncheck all Untranslated");	
	rawoutput("<script type='text/javascript' language='JavaScript'>
				<!-- Begin
				var checkflag = 'false';
				var checknoflag = 'false';
				cb = document.forms['editfeld'].elements['inserttext[]'];
				
				function check() {
					if (checkflag == 'false') {
						for (i = 0; i < cb.length; i++) {
						cb[i].checked=true;}
						checkflag = 'true';
						return ' $none '; }
					else {
						for (i = 0; i < cb.length; i++) {
						cb[i].checked=false; }
						checkflag = 'false';
						return ' $all '; }
				}

				cba = document.forms['editfeld'].elements['translated[]'];				
				function checkonlyno() {
					if (checknoflag == 'false') {
						for (i = 0; i < cb.length; i++) {
						if (cba[i].value==0) {cb[i].checked=true;}
						}
						checknoflag = 'true';
						return ' $nonetranslated '; }
					else {
						for (i = 0; i < cb.length; i++) {
						if (cba[i].value==0) {cb[i].checked=false;}
						}
						checknoflag = 'false';
						return ' $alltranslated '; }
				}				
					//  End -->
				</script>");
	//end		
	if (sizeof($ausgabe)>1) rawoutput("<input type='button' onClick='this.value=check()' name='allcheck' value='". $all ."' class='button'>");
	if ($wasthereanuntranslated) rawoutput("<input type='button' onClick='this.value=checkonlyno()' name='transcheck' value='". $alltranslated ."' class='button'>");	
	output_notl("`n");
	if (sizeof($ausgabe)>0) rawoutput("<input type='submit' name='replacechecked' value='". translate_inline("Insert Selected into your untranslated table") ."' class='button'>");
	if (sizeof($ausgabe)>0) rawoutput("<input type='submit' name='insertandeditchecked' value='". translate_inline("Translate Checked") ."' class='button'>");
	rawoutput("</form>");	
	break;

default:

output_notl("`b`c-------------`$".translate_inline("Warning! Expert Users Only")."`0-------------`b`c");
output_notl("`n`n");
output("This function scans a module for any addnews, translate_inline, output, page_header, etc who are translatable.");
output_notl("`n`n");
output("It is *very* hard to get `ball`b translatable parts, I think this function by `2Edorian`0 fetches most of them.");
output_notl("`n");
output("Yet... the wizard uses the namespace of the file... it resets according to tlschemas in the file, but if there is none, and you scan a file that's called somewhere... well... not good-> useless.");
output(" You will have also a column that shows you if the sentence is already *in* the translations table or not.");
output(" You may then decide to edit and insert them.");
output_notl("`n");
output("Do `bnot`b scan any library files modules are including. This will result in double and useless entries!");
output_notl("`n`n");

require_once("./modules/translationwizard/scanvalidfiles.php");
rawoutput("<form action='runmodule.php?module=translationwizard&op=scanmodules&mode=scan' name='listenauswahl' method='post'>");
addnav("", "runmodule.php?module=translationwizard&op=scanmodules&mode=scan");
$one=wizard_showvalidfiles();
output("`nChoose alternate scheme (valid for main file+libs, i.e. 'module-translationwizard'):`n");
rawoutput("<input id='input' name='alternate' width=55>");
rawoutput("</form>");

output_notl("`n`n");
output("The function below does a bit more.");
output(" You can select one main file in the pulldown and add files (i.e. libs) you want to translate.");
output(" Then the wizard scans these file(s), you may even enter an individual scheme.");
output_notl("`n`n");
output("`^Note: if you select a main file where a folder exists with the same name, the wizard `bautomatically selects all files`b therein.`0");
output_notl("`n`n");

rawoutput("<form action='runmodule.php?module=translationwizard&op=scanmodules&mode=scan&how=multi' name='secondscan' method='post'>");
addnav("", "runmodule.php?module=translationwizard&op=scanmodules&mode=scan&how=multi");
$two=wizard_showvalidfiles(false,1,true,true);
output("`nChoose alternate scheme (valid for main file+libs, i.e. 'module-translationwizard'):`n");
rawoutput("<input id='input' name='alternate' width=55>");
output_notl("`n`n");
rawoutput("<table border='0' cellpadding='2' cellspacing='0'>");
rawoutput("<tr class='trhead'><td></td><td>".translate_inline("File")."</td></tr>");	
$two=wizard_showvalidfiles(false,2,false);
while(list($key,$val)=each($two)) {
	rawoutput("<tr class='".($key%2?"trlight":"trdark")."'><td>");
	rawoutput("<input type='checkbox' name='transtext[]' value='$val' >");
	rawoutput("</td><td>");
	output_notl($val);
	rawoutput("</td></tr>");
} /*
				var selectedentry = document.forms['secondscan'].elements['lookfor'].value;
				selectedentry=selectedentry.substr(0,selectedentry.length-4);
if (cbb[i].search(shortentry)!=-1) {cbb[i].checked=true;}
	*/ 
	rawoutput("<script type='text/javascript' language='JavaScript'>
				<!-- Begin
				cbb = document.forms['secondscan'].elements['transtext[]'];
				var selectedentry = document.forms['secondscan'].elements['lookfor'];
				var shortentry;
				var searchentry=/(.+)[/](.+)/;
				var found;
				function modulecheck() {
					shortentry = selectedentry.value.substr(0,selectedentry.value.length-4);
					searchentry.exec(shortentry);
					found=RegExp.$2;
					for (i = 0; i < cbb.length; i++) {
					if (cbb[i].value.search(found)!=-1) {cbb[i].checked=true;}
						else {cbb[i].checked=false;}
					}

				}
					//  End -->
				</script>");
	//end	
rawoutput("</table></form>");
/*rawoutput("<form action='runmodule.php?module=translationwizard&op=scanmodules&mode=scan' method='post'>");
addnav("", "runmodule.php?module=translationwizard&op=scanmodules&mode=scan");
rawoutput("<input id='input' name='lookfor' width=55>");
rawoutput("<input type='submit' name='select' value='". translate_inline("Search")."' class='button'>");
rawoutput("</form>");*/


}

?>