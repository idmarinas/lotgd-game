<?php
$intext = httppost('intext');
$outtext = httppost('outtext');
if ($outtext <> "") {
	$login = $session['user']['login'];
	$sql = "INSERT INTO " . db_prefix("translations") . " (language,uri,intext,outtext,author,version) VALUES" . " ('$languageschema','$namespace','$intext','$outtext','$login','$logd_version')";
	$result1=db_query($sql);
	$sql = "DELETE FROM " . db_prefix("untranslated") . " WHERE intext = '$intext' AND language = '$languageschema' AND namespace = '$namespace'";
	$result2=db_query($sql);
	if (!$result1 || !$result2) $error=4;
		else $error=5;
	invalidatedatacache("translations-".$namespace."-".$languageschema);
}
redirect("runmodule.php?".$from."&error=".$error); //just redirecting so you go back to the previous page after the save	
?>