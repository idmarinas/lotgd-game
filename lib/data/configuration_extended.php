<?php

$setup_extended = [
	"Mail Texts,title",
    'sendhtmlmail' => 'Send mail in HTML format?,bool',
    'You can find the template in "themes/jade/templates/mail.twig",note',
    "No HTML possible! No colors possible!<br> Restrictions apply as then we'd need proper header formatting and you'll most likely end up in the spam folders,note",
	"verificationmailsubject"=>"Subject to send for verification,text",
	"verificationmailtext"=>"Text to send for verification,textarea",

	"Replacements:<br>
    {login}=login name<br>
    {acctid}=account id<br>
    {emailaddress}=Emailaddress of the char<br>
    {gameurl}=URL extracted from the site<br>
    {validationid}=ID generated (!!! important !!!)<br>
    ,note",

	"forgottenpasswordmailsubject"=>"Subject to send for a forgottenpassword,text",
	"forgottenpasswordmailtext"=>"Text to send for a forgottenpassword,textarea",
    "Replacements:<br>
    {login}=login name<br>
    {acctid}=account id<br>
    {gameurl}=URL extracted from the site<br>
    {requester_IP}=IP of the requester<br>
    {forgottenid}=ID generated (!!! important !!!)<br>
    ,note",

	"expirationnoticesubject"=>"Subject to send to an email when a char is about to expire,text",
	"expirationnoticetext"=>"Text to send to an email when a char is about to expire,textarea",
    "Replacements:<br>
    {charactername}=login name<br>
    {server}=Your servers URL<br>
    ,note",

	"notificationmailsubject"=>"Subject to send to notify the user of a received YOM,text",
	"notificationmailtext"=>"Text to send to notify the user of a received YOM,textarea",
    "Replacements:<br>
    {gameurl}=Your servers URL<br>
    {subject}=Subject in the YOM<br>
    {body}=Body of the YOM<br>
    {sendername}=Name of the Sender<br>
    {receivername}=Name of the Receiver<br>
    ,note"
];
