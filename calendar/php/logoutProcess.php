<?php

/*
 * @author Mario Nemecek
*/

ini_set("session.save_path", "../sessionData/");
ini_set("session.gc_maxlifetime", "1800");
session_start();

include "../config/config.php";

if(isset($_SESSION['logged-in'])) {
    $_SESSION=array();
    session_regenerate_id();
}

//send user back to the log-in page
header('Location: '.BASEPATH);
die();

?>
