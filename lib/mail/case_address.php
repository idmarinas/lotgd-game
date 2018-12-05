<?php

$id = (int) httpget('id');
$forwardlink = '';

$to = translate_inline('To: ');
$forwardto = translate_inline('Forward To: ');
$search = htmlentities(translate_inline('Search'), ENT_COMPAT, getsetting('charset', 'UTF-8'));

if ($id > 0)
{
    $to = $forwardto;
    $forwardlink = "<input type='hidden' name='forwardto' value='$id'>";
}

rawoutput("<form action='mail.php?op=write' method='post' class='ui form'>");
output('`b`2Address:´b`n');
rawoutput(sprintf("<div class='inline field'><label>%s</label> <div class='ui action input'>", appoencode("`2$to`0")));
output_notl("<input autofocus name='to' id='to' value=\"".htmlentities(stripslashes(httpget('prepop')), ENT_COMPAT, getsetting('charset', 'UTF-8')).'">', true);
output_notl("<button type='submit' class='ui button'>$search</button>", true);
rawoutput($forwardlink.'</div>');
rawoutput('</form>');
