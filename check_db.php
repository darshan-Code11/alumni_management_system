<?php
require_once 'config/db.php';

$res = mysqli_query($conn, "SHOW COLUMNS FROM users");
while($row = mysqli_fetch_assoc($res)) { echo $row['Field'] . " "; }
echo "\n<br>";
$res = mysqli_query($conn, "SHOW COLUMNS FROM events");
while($row = mysqli_fetch_assoc($res)) { echo $row['Field'] . " "; }
echo "\n<br>";
$res = mysqli_query($conn, "SHOW COLUMNS FROM jobs");
while($row = mysqli_fetch_assoc($res)) { echo $row['Field'] . " "; }
echo "\n<br>";
$res = mysqli_query($conn, "SELECT COUNT(*), college_name FROM users GROUP BY college_name");
var_dump(mysqli_fetch_all($res));
?>
