<?php

/**
* @author Mario Nemecek
*/

ini_set("session.save_path", "sessionData/");
ini_set("session.gc_maxlifetime", "1800");
session_start();

include "config/config.php";

$page = new Router();
echo $page->get_page();

?>
