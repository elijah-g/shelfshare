<?php
// Initialise variables and set to empty values
$fname = $lname = $gendera = $addressa = $statea = $pcode = $schoola = $namea = $imagea = $error = "";
$firstnameError = $lastnameError = $stateError = $pcodeError = $schoolError = "";

if (isset($_POST['submit']))
	{
		// GET DATA FROM FORM
		$firstname = htmlspecialchars($_POST['firstname']);
		$lastname = htmlspecialchars($_POST['lastname']);
		$email = htmlspecialchars($_POST['email']);
		if(isset($_POST['gender'])){$gender = htmlspecialchars($_POST['gender']);}
		$address = htmlspecialchars($_POST['address']);
		$state = htmlspecialchars($_POST['state']);
		$postcode = htmlspecialchars($_POST['postcode']);
		$school = htmlspecialchars($_POST['school']);
		$name = htmlspecialchars($_POST['name']);
		$image = htmlspecialchars($_POST['image']['tmp_name']);
		
		// FIRST NAME VALIDATION
		if (empty($_POST['firstname']))
		{
			$firstnameError = "First name is required";
		}
		
		else if (!preg_match("/^[a-zA-Z]+$/", $firstname))
		{
			$firstnameError = "Alphabetical characters only";
		}
		
		else 
		{
			$fname = $firstname;
		}
		
		// LAST NAME VALIDATION
		if (empty($_POST['lastname']))
		{
			$lastnameError = "Last name is required";
		}
		
		else if (!preg_match("/^[a-zA-Z]+$/", $lastname))
		{
			$lastnameError = "Alphabetical characters only";
		}
		
		else 
		{
			$lname = $lastname;
		}
		
		// GENDER VALIDATION
		$gendera = $gender;
		
		// ADDRESS VALIDATION
		$addressa = $address;
		
		// STATE VALIDATION
		$statea = $state;
		
		// POSTCODE VALIDATION
		if (empty($_POST['postcode']))
		{
			$pcodeError = "Postcode must be selected";
		}
		
		else if (!preg_match("/^\d{4}$/", $postcode))
		{
			$pcodeError = "Please enter a valid 4-digit postcode";
		}
		
		else
		{
			$pcode = $postcode;
		}
		
		// SCHOOL VALIDATION
		if (empty($_POST['school']))
		{
			$schoolError = "School must be selected";
		}
		
		else
		{
			$school = $school;
		}
		
		// IMAGE NAME VALIDATION
		$namea = $name;
		
		// IMAGE ENCODING
		$image = addslashes($_FILES['image']['tmp_name']);
		$name = addslashes($_FILES['image']['name']);
		$image = file_get_contents($image);
		$image = base64_encode($image);
		$imagea = $image;
		
		
		// If there are responses in certain fields and no errors
		if ($firstname && $lastname && $state && $postcode && $school && $firstnameError == "" && $lastnameError == "" && $stateError == "" && $pcodeError == "" && $schoolError == "")
		{
			// Update database with user details
	        $sqlquery = "UPDATE Accounts SET Gender = '$gender', Location = '$address', State = '$state', PCode = '$postcode', School = '$school', Name = '$name', Image = '$image' WHERE Email = '$email'";
	        
	        // Run query to update database
	        $result = mysqli_query($db,$sqlquery);
	        
	        // Inform user that the account details were saved
	        if ($result) 
	        {
				echo "<script>alert('All changes to your account with Shelfshare have been saved!');</script>";
	        }
	        else 
	        {
	        	echo "<script>alert('Changes to your account were unable to be saved. Please check for errors and try again.');</script>";
	        }
		}
	}
	
	// If the Delete Account button has been selected
	if (isset($_POST['delete']))
	{
		// GET DATA FROM FORM
		$email = htmlspecialchars($_POST['email']);
		
		// Delete account from database
		$sqlquery = "DELETE FROM Accounts WHERE Email = '$email'";
		    
        // Run query to update database
	    $result = mysqli_query($db,$sqlquery);
	    
	    // Inform user that the account has been deleted
	    if ($result) 
	    {
			echo "<script>alert('Your account has been deleted with Shelfshare!');</script>";
	    }
	    else 
	    {
	     	echo "<script>alert('Your account was unable to be deleted. Please check for errors and try again.');</script>";
	    }
	}
?>