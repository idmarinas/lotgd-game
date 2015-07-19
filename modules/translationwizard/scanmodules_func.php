<?php
function wizard_scanfile($filepath,$debug=false,$standard_tlschema=false) {
// made by the incomparable genius Edorian, master of the realms and destroyer of souls
	if(!is_file($filepath))
	{
		die("Fatal Error ! Could not find File.");
	}
	$str = join("",file($filepath));
	$file_len=strlen($str);
	// Handle 'standard_tlschema' Para
	if($standard_tlschema === false)
	{	
		//get some standards if nothing is put into
		$posi=strrpos($filepath,"/");
		$name=substr($filepath,$posi+1,strlen($filepath)-$posi-5);
		if (!$posi) $name=substr($filepath,0,strrpos($filepath,"."));
		if (strstr($filepath,"modules")) $name="module-".$name;
		debug("Used a parsed tlschema:".$name);
		//$standard_tlschema = "standard_tlschema";
		$standard_tlschema=$name;
	}
	//$_GET['debug'] = 1;
	$start = explode(" ",microtime());

	// Initalise Flags
	$escape_flag       = false;
	$escape_justset    = false;
	$open_bracket_flag = false;
	$pretext_flag      = false;
	$intext_flag       = false;
	$translate_inline_inline_select_flag = false;
	$single_quote_flag = false;
	
	// Initalise Strings
	$current_translate   = "";
	$current_tlschema    = $standard_tlschema;
	$unreadable_tlschema = "unreadable_tlschema";
	$current_outputtype  = "";
	$temp_tlschema = "";

	// Initalise Arrays
	$tlschema_stack = array();
	$return = array();
	
	// Linecount
	$line = 1;

	// Initalise Stringlens used for checking ::: This also is the function list the Parser handles
	$output_len            = strlen("output");
	$output_notl_len       = strlen("output_notl");
	$translate_inline_len  = strlen("translate_inline");
	$addnav_len            = strlen("addnav");
	$tlschema_len          = strlen("tlschema");
	$addnews_len           = strlen("addnews");
	$page_header_len       = strlen("page_header");
	$sprintf_translate_len = strlen("sprintf_translate");
	
	// Initalise Stringlens used for skipping
	$output_skip            = $output_len - 1;
	$output_notl_skip       = $output_notl_len - 1;
	$translate_inline_skip  = $translate_inline_len - 1;
	$addnav_skip            = $addnav_len - 1;
	$tlschema_skip          = $tlschema_len - 1;
	$addnews_skip           = $addnews_len - 1;
	$page_header_skip       = $page_header_len - 1;
	$sprintf_translate_skip = $sprintf_translate_len - 1;
	
	
	// Start parse
	for( $i = 0; $i < $file_len; $i++ )
	{
		if($str[$i] == "\n")
		{
			$line++;
		}
		// echo "<br>$i Char is : {$str[$i]}";
		if($intext_flag == false)
		{
			wizard_skipcommentary($str,$i,$line);
			if($pretext_flag == false)
			{
				if(substr($str,$i,$output_len) == "output")
				{
					// Skip if 'output_notl'
					if(substr($str,$i,$output_notl_len) != "output_notl")
					{
						// Skip if 'rawoutput'
						if(substr($str,$i-3,$output_len+3) != "rawoutput")
						{
							if ($debug)
							{
								debug("<br>Line $line: 'output' found");
							}
							$current_outputtype = "output";
							$pretext_flag = true;
						}
						// Skip that Chars anyway. "Pointer" is at 'o' so its just 'uptput' in both cases
						$i += $output_skip;
					}
					else
					{
						$i += $output_notl_skip;
					}
				}
				if(substr($str,$i,$translate_inline_len) == "translate_inline")
				{
					if($debug)
					{
						debug("<br>Line $line: 'translate_inline' found");
					}
					$current_outputtype = "translate_inline";
					$pretext_flag = true;
					$i += $translate_inline_skip;
				}
				if(substr($str,$i,$output_len) == "addnav")
				{
					if($debug)
					{
						debug("<br>Line $line: 'addnav' found");
					}
					$current_outputtype = "addnav";
					$pretext_flag = true;
					$i += $addnav_skip;
				}
				if(substr($str,$i,$tlschema_len) == "tlschema")
				{
					if($debug)
					{
						debug("<br>Line $line: 'tlschema' found");
					}
					$current_outputtype = "tlschema";
					$pretext_flag = true;
					$i += $tlschema_skip;
				}
				if(substr($str,$i,$addnews_len) == "addnews")
				{
					if($debug)
					{
						debug("<br>Line $line: 'addnews' found");
					}
					$current_outputtype = "addnews";
					$pretext_flag = true;
					$i += $output_skip;
				}
				if(substr($str,$i,$page_header_len) == "page_header")
				{
					if($debug)
					{
						debug("<br>Line $line: 'page_header' found");
					}
					$current_outputtype = "page_header";
					$pretext_flag = true;
					$i += $page_header_skip;
				}
				if(substr($str,$i,$sprintf_translate_len) == "sprintf_translate")
				{
					if ($debug)
					{
						debug("<br>Line $line: 'sprintf_translate' found");
					}
					$current_outputtype = "sprintf_translate";
					$pretext_flag = true;
					$i += $sprintf_translate_skip;
				}
			}
			else // $pretext flag == true
			{
				if($str[$i] == "(") //)  
				{
					$open_bracket_flag = true;
				}
				elseif( $str[$i] == '"' || $str[$i] == '\'' )
				{
					if($open_bracket_flag == true)
					{
						if ($debug)
						{
							debug("<br>Line $line: Reading string from '$current_outputtype' started");
						}
						if( $str[$i] == '\'' )
							$single_quote_flag = true;
						else
							$single_quote_flag = false;
						$intext_flag = true;
						$pretext_flag = false;
						$open_bracket_flag = false;
					}
					else
					{
						if($current_outputtype == "sprintf_translate")
						{
							if ($debug)
							{
								debug("<br>Line $line: No '(' found before ' \" '. Assuming an 'call_user_func_array' call. Skipping till next';'");
							}
							while ($str[$i] != ";")
							{
								$i++;
								wizard_skipcommentary($str,$i,$line);
							}
							$intext_flag = false;
							$pretext_flag = false;
							$open_bracket_flag = false;
						}
						else
						{
							if ($debug)
							{
								debug("<br><b>Line $line: <big>Important:</big></b> No '(' before ' \" ' was found in a '$current_outputtype'. Parse Error ? (Very confusing) Skipping till next';'");
							}
							$intext_flag = false;
							$pretext_flag = false;
							$open_bracket_flag = false;
						}
					}
				}
				else if($str[$i] == "\$")
				{
					if($current_outputtype == "translate_inline")
					{
						if ($debug)
						{
							debug("<br>Line $line:  Assuming an translate_inline(\$var?\"First\":\"Second\");");
						}
						$translate_inline_inline_select_flag = true;
					}
					else if($current_outputtype == "tlschema")
					{
						if ($debug)
						{
							debug("<br>Line $line: Unreadable tlschema. Pushing '$unreadable_tlschema' on tl_stack. ");
						}
						$current_tlschema = $unreadable_tlschema;
						array_push($tlschema_stack,$unreadable_tlschema);
						$pretext_flag = false;
						$open_bracket_flag = false;
					}
					else
					{
						if ($debug)
						{
							debug("<br><b>Line $line: <big>Important:</big></b> Found an '\$' in a '$current_outputtype'. Not translation ready ! Skipping till next';'");
						}
						while ($str[$i] != ";")
						{
							$i++;
							wizard_skipcommentary($str,$i,$line);
						}
						$pretext_flag = false;
						$open_bracket_flag = false;
					}
				}
				else if($str[$i] == ";")
				{
					if($current_outputtype == "tlschema")
					{
						// Throw away Current schema
						array_pop($tlschema_stack);
						// Get last one
						$temp_tlschema = array_pop($tlschema_stack);
						if($temp_tlschema != false)
						{
							$current_tlschema = $temp_tlschema;
							if ($debug)
							{
								debug("<br>Line $line: tlschema set back. Pulled from tl_stack : '$temp_tlschema' . ");
							}
							// Put current back
							array_push($tlschema_stack,$current_tlschema);
						}
						else
						{
							$current_tlschema = $standard_tlschema;
							if ($debug)
							{
								debug("<br>Line $line: tlschema set back. tl_stack is empty setting du standart : '$standard_tlschema' . ");
							}
						}
					}
					else if($current_outputtype == "translate_inline")
					{
						if($translate_inline_inline_select_flag == true)
						{
							$translate_inline_inline_select_flag = false;
							$assume = "\$array";
						}
						else
						{
							$assume = "array(\"...\",\"...\")";
						}
						if ($debug)
						{
							debug("<br>Line $line: Reached ';' Now assuming an translate_inline($assume). Can't be handled. Skipping");
						}
					}
					else if($current_outputtype == "page_header")
					{
						if ($debug)
						{
							debug("<br>Line $line: Empty page_header. No Problem. Skipping");
						}
					}
					else
					{
						if ($debug)
						{
							debug("<br>Line $line: Unexpected ';' Skipping current Scan.");
						}
					}
					$pretext_flag = false;
					$open_bracket_flag = false;
				}
			}
		}
		else // $intext flag == true 
		{
			if ( ( $str[$i] == '"' && !$single_quote_flag || $str[$i] == '\'' && $single_quote_flag ) && $escape_flag == false )
			{
				if($current_translate == false)
				{
					if($current_outputtype == "addnav")
					{
						if ($debug)
						{
							debug("<br>Line $line:  Empty Addnav. Assuming it works together with an HTML 'form' Tag");
						}
					}
					else if($current_outputtype == "page_header")
					{
						if ($debug)
						{
							debug("<br>Line $line:  Empty Addnav. No Problem. Skipping");
						}
					}
					else
					{
						if ($debug)
						{
							debug("<br><b>Line $line: Empty '$current_outputtype'. Makes no sense... parser is very sad ... :(</b>");
						}
					}
				}
				else if($current_outputtype == "tlschema")
				{
					if ($debug) debug("<br>Line $line: tlschema changed to '$current_translate' and pushed onto tl_stack");
					$current_tlschema = $current_translate;
					array_push($tlschema_stack,$current_tlschema);
				}
				else if(already_in_array($return,$current_translate,$current_tlschema) == false )
				{
					if ($current_outputtype == "addnews" && strstr($current_translate,"%s")) $current_translate=str_replace("`%","`%%",$current_translate);
					$return[] = array("text" => $current_translate, "schema" => $current_tlschema);
				}
				else
				{
					if ($debug)
					{
						debug("<br><b>Line $line: Found : $current_translate</b> --- tlschema: $current_tlschema");
					}
				}

				$intext_flag = false;
				$current_translate = "";

				if($translate_inline_inline_select_flag == true)
				{
					if ($debug)
					{
						debug("<br>Line $line: 2nd translate inline initalised");
					}
					$translate_inline_inline_select_flag = false;
					$pretext_flag = true;
					$open_bracket_flag = true;
				}
			}
			else if($str[$i] == "\\")
			{
				$escape_flag = true;
				$escape_justset = true;
				
			}
			else if($str[$i] == "\$" && $escape_flag == false)
			{
				if ($debug)
				{
					debug("<br><b>Line $line: <big>Important:</big></b> Found an '\$' in an opend '$current_outputtype'. Not translation ready ! Skipping till next';'");
				}
				while ($str[$i] != ";")
				{
					$i++;
					wizard_skipcommentary($str,$i,$line);
				}
				$intext_flag = false;
			}
			else
			{
				$current_translate .= $str[$i];
			}
		}
		
		if($escape_flag == true)
		{
			if($escape_justset == true)
			{
				$escape_justset = false;
			}
			else
			{
				$escape_flag = false;
			}
		}
		// echo "<br>$i Char is : {$str[$i]}";
	}
	$end = explode(" ",microtime());
	$used_micro = $end[0] - $start[0];
	$used_sec = $end[1] - $start[1] + $used_micro;
	debug("Time needed : $used_sec & Lines done: $line");
	//debug($return);
	return $return; //returns array (intext,tlschema)


}

function wizard_insertfile($delrows,$languageschema,$serialized=false) {
	if (is_array($delrows))  //setting for any intexts you might receive
		{
		$insertrows = $delrows;
		}else
		{
		if ($delrows) $insertrows  = array($insertrows);
		else 
			{
			$insertrows = array();
			}
		}
	while (list($key,$val) = each ($insertrows))
		{
		if ($serialized) {
			$val=unserialize(rawurldecode($val));
		}	//else $val = split("[||||]", $val);
		$sql="Insert IGNORE INTO ".db_prefix("untranslated")." Values ('".addslashes($val['text'])."','$languageschema','".addslashes($val['schema'])."');";
		db_query($sql);
		}
}

function wizard_skipcommentary($str,&$i,&$line)
{
	while($str[$i] == "/" && $str[$i] != "")
	{
		if($str[($i+1)] == "/")
		{
			while($str[$i] != "\n")
			{
				$i++;
			}
			$line++;
		}
		else if($str[($i+1)] == "*")
		{
			while( ($str[$i] != "*" || $str[($i+1)] != "/") && $str[$i] != "") 
			{
				if($str[$i] == "\n")
				{
					$line++;
				}
				$i++;
			}
		}
		else
		{
			return;
		}
	}
}

function already_in_array($array,$text,$schema)
{
	foreach ($array as $entry) 
	{
		if($entry['text'] == $text && $entry['schema'] == $schema)
		{
			return true;
		}
	}
	return false;
}
?>