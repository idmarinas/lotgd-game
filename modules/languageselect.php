<?php
/**
 *	10/10/10 - v1.0.0
 */
function languageselect_getmoduleinfo()
{
	$info = array(
		"name"=>"Language Selector",
		"description"=>"Creates a dropdown menu of installed languages on the homepage.",
		"version"=>"1.0.0",
		"author"=>"`@MarcTheSlayer",
		"category"=>"General",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1443",
		"settings"=>array(
			"Language Setting,title",
			"hide"=>"Hide homepage language list:,bool|0",
		)
	);
	return $info;
}

function languageselect_install()
{
	output("`c`b`Q%s 'languageselect' Module.`b`n`c", translate_inline(is_module_active('languageselect')?'Updating':'Installing'));
	module_addhook('changesetting');
	module_addhook('create-form');
	module_addhook('process-create');
	if( get_module_setting('hide') == 0 )
	{
		module_addhook_priority('header-home',1);
		module_addhook_priority('footer-home',1);
	}
	return TRUE;
}

function languageselect_uninstall()
{
	output("`n`c`b`Q'languageselect' Module Uninstalled`0`b`c");
	return TRUE;
}

function languageselect_dohook($hookname,$args)
{
	switch( $hookname )
	{
		case 'changesetting':
		if( $args['module'] == 'languageselect' && $args['setting'] == 'hide' )
		{
			if( $args['new'] == 1 )
			{
				module_drophook('header-home');
				module_drophook('footer-home');
			}
			elseif( $args['new'] == 0 )
			{
				module_addhook_priority('header-home',1);
				module_addhook_priority('footer-home',1);
			}
		}
		break;

		case 'header-home':
			if( isset($_POST['language']) )
			{
				setcookie('language',$_POST['language'],strtotime("+45 days"));
				redirect('home.php'); // Have to do this because the translate_setup() function gets called before any hooks. 
			}
		break;

		case 'footer-home':
			$languages = getsetting('serverlanguages','en,English,fr,Fran�ais,dk,Danish,de,Deutsch,es,Espa�ol,it,Italian');
			if( !empty($languages) )
			{
				require_once('lib/showform.php');
				rawoutput('<form action="home.php" method="POST"><table style="width:auto; margin:auto;"><tr><td>');
				$form = array('language'=>'Choose a different language:,enum,'.$languages);
				$prefs['language'] = ( isset($_POST['language']) ) ? $_POST['language'] : (( isset($_COOKIE['language']) ) ? $_COOKIE['language'] : getsetting('defaultlanguage','en'));
				showform($form, $prefs, TRUE);
				$submit = translate_inline('Choose');
				rawoutput('</td><td><br><p><input type="submit" class="button" value="'.$submit.'"></p></td></tr></table></form>');
			}
		break;

		case 'create-form':
			$serverlanguages = getsetting('serverlanguages','en,English,fr,Fran�ais,dk,Danish,de,Deutsch,es,Espa�ol,it,Italian');
			if( !empty($serverlanguages) )
			{
				$languages = explode(',', $serverlanguages);
				$language = ( isset($_COOKIE['language']) ) ? $_COOKIE['language'] : getsetting('defaultlanguage','en');
				output('Choose a different language:', TRUE);
				rawoutput('<select name="language">');
				for( $i=0; $i<count($languages); $i++ )
				{
					$key = $languages[$i];
					$i++;
					$value = $languages[$i];
					$selected = ( $key == $language ) ? ' selected="selected"' : '';
					rawoutput('<option value="'.$key.'"'.$selected.'>'.$value.'</option>');
				}
				rawoutput('</select><br><br>');
			}
		break;

		case 'process-create':
			$prefs = array('language'=>$args['language']); // Session prefs don't get created until the first newday which comes after this hook.
			db_query("UPDATE " . db_prefix('accounts') . " SET prefs = '".addslashes(serialize($prefs))."' WHERE acctid = '{$args['acctid']}'");
		break;
	}

	return $args;
}
?>