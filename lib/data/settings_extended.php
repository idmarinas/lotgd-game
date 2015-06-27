<?php

$defaults = array(
	//Mail Texts
	"verificationmailsubject"=>"LoGD Account Verification",
	"verificationmailtext"=>"Login name: {login} `n`nIn order to verify your account, you will need to click on the link below.`n`n  {gameurl}?op=val&id={validationid}`n`nThanks for playing!",
	"forgottenpasswordmailsubject"=>"Forgotten Password requested",
	"forgottenpasswordmailtext"=>"Login: {login}`n`nSomeone from {requester_ip} requested a forgotten password link for your account.  If this was you, then here is your link, you may click it to log into your account and change your password from your preferences page in the village square.`n`nIf you didn't request this email, then don't sweat it, you're the one who is receiving this email, not them.`n`n  {gameurl}?op=forgotval&id={forgottenid} `n`n Thanks for playing!",
	"expirationnoticesubject"=>"LoGD Character Expiration",
	"expirationnoticetext"=>"One or more ({charname}) of your characters in Legend of the Green Dragon at {server} is about to expire.`n`nIf you wish to keep this character, you should log on to him or her soon!",
	"notificationmailsubject"=>"New LoGD Mail ({subject})",
	"notificationmailtext"=>"You have received new mail on LoGD at {gameurl}`n`n-=-=-=-=-=-=-=-=-=-=-=-=-=-`nFrom: {sendername}`nTo: {receivername}`nSubject: {subject}`nBody: `n{body}`n-=-=-=-=-=-=-=-=-=-=-=-=-=-`n`nDo not respond directly to this email, it was sent from the game email address, and not the email address of the person who sent you the message.  If you wish to respond, log into Legend of the Green Dragon at {gameurl} .`n`nYou may turn off these alerts in your preferences page, available from the village square.",
	
);
