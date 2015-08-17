<?php
define("OVERRIDE_FORCED_NAV",true);

function mailoptions_getmoduleinfo(){
	$info = array(
		"name" => "Mail Options",
		"version" => "1.0",
		"author" => "Christian Rutsch",
		"category" => "Mail",
		"download" => "http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1306",
		"override_forced_nav"=>true,
	);
	return $info;
}

function mailoptions_install(){
	module_addhook_priority("mailfunctions", INT_MAX);
	return true;
}

function mailoptions_uninstall(){
	return true;
}

function mailoptions_dohook($hookname, $args){
	global $session;
	$mail = db_prefix('mail');
	$acctid = $session['user']['acctid'];
	$sql = false;
	switch($hookname) {
		case "mailfunctions":
			$options = translate_inline("Options");
			array_push($args, array("mail.php?opmailops=mailoptions", $options));
			switch(httpget('opmailops')) {
				case 'mailoptions':
					output("This is the mail options page.`n`n");
					$return = translate_inline("Return to mailbox");
					$deleteall = translate_inline("Delete all messages");
					$deleteallsys = translate_inline("Delete all system messages");
					$deleteallread = translate_inline("Delete all read messages");
					$markallread = translate_inline("Mark all messages as read");
					$markallsysread = translate_inline("Mark all system messages as read");
					$markallplayerread = translate_inline("Mark all other messages as read");
					rawoutput("<table width='100%' border='0' cellpadding='2' cellspacing='2'>");
					rawoutput("<tr>");
					rawoutput("<td><a href='mail.php' class='motd'>$return</a><br/>&nbsp;</td>");
					rawoutput("<td>&nbsp;</td>");
					rawoutput("<td>&nbsp;</td>");
					rawoutput("</tr>");
					rawoutput("<tr>");
					rawoutput("<td><a href='mail.php?opmailops=deleteall' class='motd'>$deleteall</a></td>");
					rawoutput("<td><a href='mail.php?opmailops=deleteallsys' class='motd'>$deleteallsys</a></td>");
					rawoutput("<td><a href='mail.php?opmailops=deleteallread' class='motd'>$deleteallread</a></td>");
					rawoutput("</tr>");
					rawoutput("<tr>");
					rawoutput("<td><a href='mail.php?opmailops=markallread' class='motd'>$markallread</a></td>");
					rawoutput("<td><a href='mail.php?opmailops=markallsysread' class='motd'>$markallsysread</a></td>");
					rawoutput("<td><a href='mail.php?opmailops=markallplayerread' class='motd'>$markallplayerread</a></td>");
					rawoutput("</tr>");
					rawoutput("</table>");
					popup_footer();
					break;
				case 'deleteall':
					$sql = "DELETE FROM $mail WHERE msgto = $acctid";
					break;
				case 'deleteallsys':
					$sql = "DELETE FROM $mail WHERE msgto = $acctid AND msgfrom = 0";
					break;
				case 'deleteallread':
					$sql = "DELETE FROM $mail WHERE msgto = $acctid AND seen = 1";
					break;
				case 'markallread';
					$sql = "UPDATE $mail SET seen=1 WHERE msgto = $acctid";
					break;
				case 'markallsysread';
					$sql = "UPDATE $mail SET seen=1 WHERE msgto = $acctid AND msgfrom = 0";
					break;
				case 'markallplayerread';
					$sql = "UPDATE $mail SET seen=1 WHERE msgto = $acctid AND msgfrom <> 0";
					break;
			}
			break;
	}
	if ($sql !== false) {
		$result = db_query($sql);
	}
	return $args;
}

function mailoptions_run() {
}