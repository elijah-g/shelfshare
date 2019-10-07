<?php
include_once "Connect.php";

if (isset($_REQUEST["current"])){
    $current = $_REQUEST["current"];
    $other = $_REQUEST["other"];
    
    $query = "UPDATE Books SET CounterpartID = $other WHERE BookID = $current";
    mysqli_query($db,$query);
    echo $query;
    
    //Check if both books have counter parts then set Status to 3
    $query = "SELECT CounterpartID FROM Books WHERE BookID = $current OR $other";
    $result = mysqli_query($db,$query);
    $both = true;
    while($row = $result->fetch_assoc()) {
        if ($row["CounterpartID"] === null){
            $both = false;
        }
    }
    if ($both == true){
        $query = "UPDATE Books SET BookState = 3 WHERE BookID = $current OR $other";
        mysqli_query($db, $query);
    }
}

if(isset($_REQUEST["book"])){
    $book = $_REQUEST["book"];
    $number = $_REQUEST["number"];
    
    $query = "UPDATE Books SET Rating = $number WHERE BookID = $book";
    mysqli_query($db,$query);
    echo $query;
    
    
}