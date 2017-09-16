<?php
// translator ready
// addnews ready
// mail ready
define("OVERRIDE_FORCED_NAV",true);
require_once 'common.php';

tlschema("mail");
$args=modulehook('header-mail',[]);

$superusermessage = getsetting("superuseryommessage","Asking an admin for gems, gold, weapons, armor, or anything else which you have not earned will not be honored.  If you are experiencing problems with the game, please use the 'Petition for Help' link instead of contacting an admin directly.");

$op = httpget('op');
$id = (int)httpget('id');
if($op == 'del')
{
	$sql = "DELETE FROM " . DB::prefix("mail") . " WHERE msgto='".$session['user']['acctid']."' AND messageid='$id'";
	DB::query($sql);
	invalidatedatacache("mail-{$session['user']['acctid']}");
	header("Location: mail.php");
	exit();
}
elseif($op == 'process')
{
	$msg = httppost('msg');
	if (!is_array($msg) || count($msg)<1){
		$session['message'] = "`n`n`\$`bYou cannot delete zero messages!  What does this mean?  You pressed \"Delete Checked\" but there are no messages checked!  What sort of world is this that people press buttons that have no meaning?!?`b`0";
		header("Location: mail.php");
		exit();
	}else{
		$sql = "DELETE FROM " . DB::prefix("mail") . " WHERE msgto='".$session['user']['acctid']."' AND messageid IN ('".join("','",$msg)."')";
		DB::query($sql);
		invalidatedatacache("mail-{$session['user']['acctid']}");
		header("Location: mail.php");
		exit();
	}
}
elseif ($op == 'unread')
{
	$sql = "UPDATE " . DB::prefix("mail") . " SET seen=0 WHERE msgto='".$session['user']['acctid']."' AND messageid='$id'";
	DB::query($sql);
	invalidatedatacache("mail-{$session['user']['acctid']}");
	header("Location: mail.php");
	exit();
}

popup_header("Ye Olde Poste Office");
$inbox = translate_inline("Inbox");
$write = translate_inline("Write");

// Build the initial args array
$args = [];
array_push($args, ["mail.php", $inbox]);
array_push($args, ["mail.php?op=address",$write]);
// to use this hook,
// just call array_push($args, array("pagename", "functionname"));,
// where "pagename" is the name of the page to forward the user to,
// and "functionname" is the name of the mail function to add
$mailfunctions = modulehook("mailfunctions", $args);

rawoutput('<div class="ui buttons">');
$count_mailfunctions = count($mailfunctions);
for($i = 0; $i < $count_mailfunctions; ++$i)
{
	if (is_array($mailfunctions[$i]))
    {
		if (count($mailfunctions[$i]) == 2)
        {
			$page = $mailfunctions[$i][0];
			$name = $mailfunctions[$i][1]; // already translated
			rawoutput("<a href='$page' class='ui button'>$name</a>");
			// No need for addnav since mail function pages are (or should be) outside the page nav system.
		}
	}
}
rawoutput('</div>');
output_notl('`n`n');

if($op == 'send') require_once 'lib/mail/case_send.php';

switch ($op)
{
    case 'read':
        require_once 'lib/mail/case_read.php';
    break;
    case 'address':
        require_once 'lib/mail/case_address.php';
    break;
    case 'write':
        require_once 'lib/mail/case_write.php';
    break;
    default:
        require_once 'lib/mail/case_default.php';
	break;
}
popup_footer();
?>
