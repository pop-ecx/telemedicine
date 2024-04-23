<?php

define('SAFETY_CONSTANT', true);

// Include the database initialization file
require_once('db/initialize.php');

// Establish a new database connection
function getConnection() {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Check connection
    if ($mysqli->connect_error) {
        die('Connect Error (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
    }

    return $mysqli;
}
?>
