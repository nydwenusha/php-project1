<?php

$server = 'sql7.freesqldatabase.com';
$db_user = 'sql7834792';
$db_password = 'iNsnCUYxuq';
$db_name = 'sql7834792';

$conn = mysqli_connect($server, $db_user, $db_password, $db_name);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to UTF-8
mysqli_set_charset($conn, "utf8");
?>
