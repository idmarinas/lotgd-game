<?php

$setup_extended = array(
	"Mail Texts,title",
	"No HTML possible! No colors possible!`n Restrictions apply as then we'd need proper header formatting and you'll most likely end up in the spam folders,note",
	
	"Replacements:`n
{login}=login name`n
{acctid}=account id`n
{emailaddress}=Emailaddress of the char`n
{gameurl}=URL extracted from the site`n
{validationid}=ID generated (!!! important !!!)`n
,note",
	"verificationmailsubject"=>"Subject to send for verification,text",
	"verificationmailtext"=>"Text to send for verification,textarea",
	
"Replacements:`n
{login}=login name`n
{acctid}=account id`n
{gameurl}=URL extracted from the site`n
{requester_IP}=IP of the requester`n
{forgottenid}=ID generated (!!! important !!!)`n
,note",
	"forgottenpasswordmailsubject"=>"Subject to send for a forgottenpassword,text",
	"forgottenpasswordmailtext"=>"Text to send for a forgottenpassword,textarea",
	
"Replacements:`n
{charactername}=login name`n
{server}=Your servers URL`n
,note
",
	"expirationnoticesubject"=>"Subject to send to an email when a char is about to expire,text",
	"expirationnoticetext"=>"Text to send to an email when a char is about to expire,textarea",

"Replacements:`n
{gameurl}=Your servers URL`n
{subject}=Subject in the YOM`n
{body}=Body of the YOM`n
{sendername}=Name of the Sender`n
{receivername}=Name of the Receiver`n
,note
",
	"notificationmailsubject"=>"Subject to send to notify the user of a received YOM,text",
	"notificationmailtext"=>"Text to send to notify the user of a received YOM,textarea",

);
