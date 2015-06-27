<?php
$alrighty=true;
while (list($key,$trans)=each($transintext)) {
	if ($transouttext[$key]<>"")
		{
		$intext = $trans; //this comes in from the textarea and mustn't be decoded
		$outtext = $transouttext[$key];
		if ($nametext[$key]) $namespace=$nametext[$key];
		$login = $session['user']['login'];
		if ($translatedtid[$key]) {
		$sql = "UPDATE " . db_prefix("translations") . " SET outtext='$outtext',author='$login',version='$logd_version' WHERE tid={$translatedtid[$key]};";
		} else {
		$sql = "INSERT INTO " . db_prefix("translations") . " (language,uri,intext,outtext,author,version) VALUES" . " ('$languageschema','$namespace','$intext','$outtext','$login','$logd_version')";
		}
		$result1=db_query($sql);
		invalidatedatacache("translations-".$namespace."-".$languageschema);
		//debug($sql);
		$sql = "DELETE FROM " . db_prefix("untranslated") . " WHERE intext = '$intext' AND language = '$languageschema' AND namespace = '$namespace'";
		//debug($sql);
		$result2=db_query($sql);
		if (!$result1 || !$result2) $alrighty=false;
		}
}
if (!$alrighty) $error=4;
	else $error=5;
if ($redirectonline) redirect("runmodule.php?module=translationwizard&op=list&ns=".$namespace."&error=".$error); //just redirecting 
?>