<?php
// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/http.php';
require_once 'lib/superusernav.php';

tlschema('rawsql');

check_su_access(SU_RAW_SQL);

page_header('Raw SQL/PHP execution');
superusernav();
addnav('Execution');
addnav('SQL', 'rawsql.php');
addnav('PHP', 'rawsql.php?op=php');

$op = httpget('op');
if ($op == '' || $op == 'sql')
{
	$sql = httppost('sql');
    if ($sql != '')
    {
		$sql = stripslashes($sql);
		modulehook("rawsql-execsql",array("sql"=>$sql));
		debuglog('Ran Raw SQL: ' . $sql);
		$r = DB::query($sql, false);
		if (!$r) { output("`\$SQL Error:`& %s`0`n`n", DB::error($r)); }
        else
        {
			if (DB::affected_rows() > 0) { output('`&%s rows affected.`n`n', DB::affected_rows()); }
			rawoutput("<table class='ui very compact striped table'>");
			$number = DB::num_rows($r);
			for ($i = 0; $i < $number; $i++) {
				$row = DB::fetch_assoc($r);
				if ($i == 0) {
					rawoutput("<thead><tr>");
					$keys = array_keys($row);
					foreach ($keys as $value) {
						rawoutput("<th>$value</th>");
					}
					rawoutput('</tr></thead>');
				}
				rawoutput('<tr>');
                foreach ($keys as $value)
                {
					rawoutput("<td valign='top'>{$row[$value]}</td>");
				}
				rawoutput('</tr>');
			}
			rawoutput('</table>');
		}
	}

	output('Type your query');
	$execute = translate_inline('Execute');
	$ret = modulehook('rawsql-modsql', ['sql' => $sql]);
	$sql = $ret['sql'];
	rawoutput("<form action='rawsql.php' method='post' class='ui form'>");
	rawoutput("<div class='field'><textarea name='sql' class='input' cols='60' rows='10'>".htmlentities($sql, ENT_COMPAT, getsetting("charset", "UTF-8"))."</textarea></div>");
	rawoutput("<div class='field'><input type='submit' class='ui button' value='$execute'></div>");
	rawoutput("</form>");
	addnav("", "rawsql.php");
}
else
{
	$php = stripslashes(httppost("php"));
	$source = translate_inline("Source:");
	$execute = translate_inline("Execute");
    if ($php > '')
    {
		rawoutput("<div style='background-color: #FFFFFF; color: #000000; width: 100%'><b>$source</b><br>");
		rawoutput(highlight_string("<?php\n$php\n?>", true));
		rawoutput('</div>');
		output('`bResults:`b`n');
		modulehook('rawsql-execphp', ['php' => $php]);
		ob_start();
		eval($php);
		output_notl(ob_get_contents(), true);
		ob_end_clean();
		debuglog('Ran Raw PHP: ' . $php);
	}
	output('`n`nType your code:');
	$ret = modulehook('rawsql-modphp', ['php' => $php]);
	$php = $ret['php'];
	rawoutput("<form action='rawsql.php?op=php' method='post' class='ui form'>");
	rawoutput("<div class='field'>&lt;?php<textarea name='php'>".htmlentities($php, ENT_COMPAT, getsetting("charset", "UTF-8"))."</textarea>?&gt;</div>");
	rawoutput("<div class='field'><input type='submit' class='ui button' value='$execute'></div>");
	rawoutput('</form>');
	addnav('', 'rawsql.php?op=php');
}
page_footer();
