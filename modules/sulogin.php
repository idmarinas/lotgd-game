<?php
/* Superuser Login by Catscradler
   Superusers can sign in even when the server is full.
   Anyone may navigate to the page /index.php?op=backdoor
   Only superusers with commentary or user editor privileges (includes MEGAUSERS) 
   will be able to successfully sign in on this page.
   Everyone else will be directed back to the regular login page with an
   error message.
   Does not override the serversuspend module.
*/

function sulogin_getmoduleinfo(){
	$info = array(
		"name"=>"Superuser Login",
		"version"=>"1.0",
		"author"=>"Catscradler",
		"category"=>"Administrative",
		"download"=>"http://dragonprime.net",
		"allowanonymous"=>true,
	);
	return $info;
}

function sulogin_install(){
	module_addhook("header-home");
	module_addhook("check-login");
	global $session;
	if ($session['user']['superuser'] & SU_MANAGE_MODULES)
		output("Superusers can now log in at any time using the page /index.php?op=backdoor");
	return true;
}

function sulogin_uninstall(){
	return true;
}

function sulogin_dohook($hookname, $args){
	switch($hookname){

		case "header-home":
			if (httpget("op")=="backdoor"){
				redirect("runmodule.php?module=sulogin");
			}
			break;

		case "check-login":
			if (httppostisset("sulogin")){
				global $session;
				if (!($session['user']['superuser'] & SU_EDIT_COMMENTS) && !($session['user']['superuser'] & SU_EDIT_USERS)){  //check for proper permissions
					$session['message'].=translate_inline("`4You are not a superuser. You must use this page to sign in.`0`n"); //send naughty regular users back to their login page
					require_once("lib/redirect.php");
					redirect("index.php");
				}
			}
			break;
	}
	return $args;
}

function sulogin_run(){
	page_header("Superuser Login");

	output("`c`b`\$Superuser Login`b`n");
	//This is just a partial copy of login.php with an extra element.
	rawoutput("<script language='JavaScript' src='lib/md5.js'></script>");
	rawoutput("<script language='JavaScript'>
	<!--
	function md5pass(){
		//encode passwords before submission to protect them even from network sniffing attacks.
		var passbox = document.getElementById('password');
		if (passbox.value.substring(0, 5) != '!md5!') {
			passbox.value = '!md5!' + hex_md5(passbox.value);
		}
	}
	//-->
	</script>");
	$uname = translate_inline("<u>U</u>sername");
	$pass = translate_inline("<u>P</u>assword");
	$butt = translate_inline("Log in");
	rawoutput("<form action='login.php' method='POST' onSubmit=\"md5pass();\">".templatereplace("login",array("username"=>$uname,"password"=>$pass,"button"=>$butt))."<input type=\"hidden\" name=\"sulogin\" value=\"sulogin\"/> </form>");
	output_notl("`c");
	page_footer();
}

?>
