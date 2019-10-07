<?php
session_start();
include_once "Connect.php";
$pageTitle = 'Edit';

echo print_r($_POST);
echo print_r($_FILES);
    // React only to POSTed form data
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // sanitize entries to make safe for database.
        $bookcondition = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['BookCondition'])));
        $bookID = $_POST["BookID"];
        
        if(!empty($_FILES['Image']['tmp_name']) && file_exists($_FILES['Image']['tmp_name'])) {
            // IMAGE ENCODING
    	    $image = addslashes($_FILES['Image']['tmp_name']);
    	    $name = addslashes($_FILES['Image']['name']);
    	    $image = file_get_contents($image);
    	    $image = base64_encode($image);
        }
        
        
        
        //If no image is set leave it as is.
        if (empty($image) && $bookcondition != '') {
    		// Build the UPDATE query for editing existing books 
                $query = "UPDATE Books SET  BookCondition = '$bookcondition' WHERE BookID = '$bookID'";

    	}
    	//If no book condition set we'll just update the image
    	elseif ($bookcondition == "" && !empty($image)){
    	    $query = "UPDATE Books SET  ImageName = '$name', Image = '$image' WHERE BookID = '$bookID'";
    	}
    	
    	//If we get this far everything needs updating.
        else{
                // Build the UPDATE query for editing existing books 
                $query = "UPDATE Books SET  BookCondition = '$bookcondition', ImageName = '$name', Image = '$image' WHERE BookID = '$bookID'";
        }     
        
        //Check atleast one thing was specified to be changed.
        if ($image != '' || $bookcondition != ''){
            
                // Run the query to update or create the database record
                    echo $query;
                    $result = mysqli_query($db,$query);
                     
                    
            
        }
               }

 
?>

