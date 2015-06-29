<?php
$user = httpget('user');
$login = httpget('login');
$id = (int)httpget('id');
remove_item($id, 1, $user);
redirect("bio.php?char=".$login);
?>