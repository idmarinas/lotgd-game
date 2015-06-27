<?php
// translator ready
// addnews ready
// mail ready
/**
* \file index.php
* This is just a redirection site to the home.php
* @see home.php
*/
header("Location: home.php?".$_SERVER['QUERY_STRING']);
?>
