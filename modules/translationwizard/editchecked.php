<?php

rawoutput("<br>");
output("`n`bBeware of the autologoff-timeout!`b`n`n");
while (list($key,$trans)=each($transintext))
	{
		output("Text:");
		output_notl("`n");
		$trans=rawurldecode($trans);
		if (unserialize($trans)) $trans=unserialize($trans);
		if  (is_array($trans)) {
			$currentnamespace=$trans['schema'];
			if (!$currentnamespace) $currentnamespace=$namespace;
			$intext=$trans['text'];
			if (array_key_exists("outtext",$trans)) $outtext=$trans['outtext'];
				else
				$outtext='';
		} else {
			$intext=$trans;
			$currentnamespace=$namespace;
			$outtext='';
		}
		rawoutput("<textarea name='transtext[]' class='input' cols='60' rows='5' readonly >".htmlentities($intext,ENT_COMPAT,$coding)."</textarea><br>");
		rawoutput("<input type='hidden' name='nametext[]' value='$currentnamespace'>");
		rawoutput("<input type='hidden' name='translatedtid[]' value='{$trans['tid']}'>");
		output("Translation:");
		output_notl("`n");
		rawoutput("<textarea name='transtextout[]' class='input' cols='60' rows='5'>".rawurldecode($outtext)."</textarea><br>");
		output_notl("`n`n");
		output_notl("`b`c-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-`c`b");
	}
output_notl("`n`n");
rawoutput("<input type='submit' name='multichecked' value='". translate_inline("Save") ."' class='button'>");
rawoutput("</form>");

?>