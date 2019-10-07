<?php
    include 'Connect.php';
    $pageTitle = 'Reset Password';
    include 'Header.php';
    
    // Define variables and initialise with empty values
    $sqlquery = $result = $rows = "";
    
    // Get verification code from email link
    $email = trim(mysqli_real_escape_string($db, $_GET['Email'])); 
    
    // React only to POSTed form data
    if($_SERVER["REQUEST_METHOD"] == "POST"){
 
        // put the form data in variables, removing database-dangerous characters and trimming // 
        $userpassword = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['Password'])));
        $confirmpassword = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['ConfirmPassword'])));
  
    
        // check the password is at least the minimum length //
        if (strlen($userpassword) < 8) {
            $error = "Your password is too short.";
        }
        else {
            // check the password matches the confirm password //
            if ($userpassword != $confirmpassword) {
                $error = "Your password fields did not match.";
            }
            else {
                // hash the password //
                $hashedpassword = password_hash($userpassword,PASSWORD_DEFAULT);
    
                // Update the password
                $sqlquery = "UPDATE Accounts SET password = '$hashedpassword' WHERE Email = '$email'";
    
                // Run query to update the password
                $result = mysqli_query($db,$sqlquery);
    
                // Inform user if password was changed and then redirect to Login page
                if ($result) {
                    header('Refresh:3; url=Login.php');
                    echo "<script>alert('Your password has been changed.');</script>";
                }
                else {
                    echo "<script>alert('Your password could not be changed. Please try again');</script>";
                }
            }
        }
    }
    
?>

    <!-- RESET PASSWORD PAGE HTML -->
	<h1 class="page-header">
		Reset Password
	</h1>

	<div class="content-box login">
        <p class='error'><?php echo $error;?></p>
        <form action="" method="post">
          <div class="form-group">
            <label for="email">Email address:</label>
            <input type="email" class="form-control" id="email" name="Email" value="<?php echo $email;?>" readonly>
          </div>
          <div class="form-group">
            <label for="pwd">Password:</label>
            <input type="password" class="form-control" id="pwd" name="Password">
          </div>
          <div class="form-group">
            <label for="pwd">Confirm Password:</label>
            <input type="password" class="form-control" id="pwd" name="ConfirmPassword">
          </div>
  
          <button type="submit" value=" Submit " class="btn btn-primary">Submit</button>
          
        </form> 
	</div>
	
<?php include 'Footer.php';?>