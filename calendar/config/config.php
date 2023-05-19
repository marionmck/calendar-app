<?php

/**
* Function to overide exception handling
*
* @param $e exception to be handled
*
* @author Mario Nemecek
*/

// Function to get connection to the database
function getConnection() {
    try {
        $connection = new PDO("mysql:host=localhost; dbname=unn_w17015200","unn_w17015200", "NUNBMDY1");
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $connection;
    }
    catch (Exception $e) { // if connection fails
        throw new Exception("Connection error ".$e->getMessage(), 0, $e);
        log_error($e);
    }
}

/* SESSION FUNCTIONS */
function setSession($key, $value) {
    $_SESSION[$key] = $value;
    return true;
}

function getSession($key) {
    if(isset($_SESSION[$key])) {
        return $_SESSION[$key];
    }
}

function checkLogin() {
    if(getSession('logged-in') == true){
        return true;
    }
    else {
        return false;
    }
}

// free to use code from https://stackoverflow.com/questions/520237/how-do-i-expire-a-php-session-after-30-minutes
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    // last request was more than 30 minutes ago
    session_unset();     // unset $_SESSION variable for the run-time
    session_destroy();   // destroy session data in storage
}
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

function exceptionHandler($e) {
    $system_msg = array("status" => 500, "message" => $e->getMessage(),
                        "file" => $e->getFile(), "line" => $e->getLine());

    $user_msg = array("status" => 500, "message" => "Sorry! Internal server error.");

    header("Access-Control-Allow-Origin *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Acess-Control-Allow-Methods: GET, POST");

    logError("Message: " . $system_msg['message'] . " File:" . $system_msg['file']
             . " Line:" . $system_msg['line']);
    echo json_encode($user_msg);
}

set_exception_handler('exceptionHandler');

/**
* Function to overide error handling
*/
function errorHandler($errno, $errstr, $errfile, $errline) {
    if ($errno != 2 && $errno != 8) {
        throw new Exception("Fatal Error Detected: [$errno] $errstr line: $errline", 1);
    } else {
        logError("Fatal Error Detected: [$errno] $errstr File: $errfile Line: $errline", 1);
    }
}

set_error_handler('errorHandler');


/**
* Function to store exception and errors in log file (error_log_file.log)
*
* @param $e exception or error to be logged
*/
function logError($e) {
    $fileHandle = fopen("error_log_file.log", "ab");
    $errorDate = date('D M j G:i:s T Y');
    fwrite($fileHandle, "$errorDate | $e".PHP_EOL);
    fclose($fileHandle);
}

/**
* Function to automatically load classes
*
* @param string $className the name of the class to be loaded
*/
function autoloadClasses($className) {
    $filename = "router\\" . strtolower($className) . ".class.php";
    $filename = str_replace("\\", DIRECTORY_SEPARATOR, $filename);
    if(is_readable($filename)) {
        include_once $filename;
    }
    else {
        throw new exception("File not found: " . $className . " (" . $filename . ")");
    }
}
spl_autoload_register("autoloadClasses");

// Global Variables
$ini['main'] = parse_ini_file("config.ini", true);
define('BASEPATH', $ini['main']['paths']['basepath']);
define('CSSPATH', $ini['main']['paths']['css']);
define('JSPATH', $ini['main']['paths']['js']);

?>
