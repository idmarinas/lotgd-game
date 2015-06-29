<?php
    $dwid = httpget('dwid');
    page_header("Edit dwellings");
    output("Dwelling Editor:`n");
	require_once("modules/dwellingseditor/lib.php");
    dwellingform();
?>