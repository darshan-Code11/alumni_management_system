<?php


define('DB_HOST', '127.0.0.1'); 
define('DB_USER', 'root'); 
define('DB_PASS', ''); 
define('DB_NAME', 'alumni_db'); 
define('DB_PORT', 3307);

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
