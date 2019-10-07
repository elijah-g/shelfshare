<?php
session_start();
    // this code adapted from https://community.c9.io/t/setting-up-mysql/ALTER 

    $servername = getenv('IP');
    $username = getenv('C9_USER');
    $password = "";
    $database = "SHELFSHARE";
    $dbport = 3306;

    // Create connection
    $db = new mysqli($servername, $username, $password, $database, $dbport);

    // Check connection
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    } 
?>