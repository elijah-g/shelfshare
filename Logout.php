<?php
session_start();
include("Connect.php");

    // Remove globals
    unset($_SESSION['current_user']);
    unset($_SESSION['userID']);
    unset($_SESSION['edit_book']);
 
    // close the database 
    mysqli_close($db);

    // send them back to the login page 
    header("location: Login.php");


?>


