<?php
require_once 'config/db.php';

$queries = [
    "ALTER TABLE users ADD COLUMN college_name VARCHAR(255) DEFAULT NULL",
    "ALTER TABLE events ADD COLUMN college_name VARCHAR(255) DEFAULT NULL",
    "ALTER TABLE jobs ADD COLUMN college_name VARCHAR(255) DEFAULT NULL",
    "UPDATE users SET college_name = 'Global University' WHERE college_name IS NULL",
    "UPDATE events SET college_name = 'Global University' WHERE college_name IS NULL",
    "UPDATE jobs SET college_name = 'Global University' WHERE college_name IS NULL"
];

foreach ($queries as $q) {
    try {
        if (mysqli_query($conn, $q)) {
            echo "Success: $q\n<br>";
        } else {
            echo "Error: ".mysqli_error($conn)." on $q\n<br>";
        }
    } catch (Exception $e) {
        echo "Exception: ".$e->getMessage()." on $q\n<br>";
    }
}
echo "Migration complete\n<br>";
?>
