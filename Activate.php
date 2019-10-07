<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

    <!-- WEB PAGE TITLE -->
    <title>Account Activation</title>

    <!-- META DETAILS -->
    <meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
    <meta http-equiv="content-language" content="en-GB" />
    <meta http-equiv="content-style-type" content="text/css" />
    <meta name="title" lang="en" content="Registration" />
    <meta name="documenttitle" lang="en" content="Registration" />
    <meta name="keywords" content="Selfshare, registration" />
    <meta name="description" content="Selfshare" />
    <meta name="robot" content="index" />

    <!-- HTML/PHP, CSS, AND JAVASCRIPT LINKS -->
    <link rel="home" type="text/html/php" href="index.php" title="Home" />
    <link rel="stylesheet" type="text/css" href="shelfshare.css" title="Stylesheet" />
    <link rel="sign_up" type="text/html/php" href="createAccount.php" title="Sign Up" />
    <link rel="activate" type="text/html/php" href="activate.php" title="Account Activation" />
</head>

<body>
 
<!-- LOGO AND BANNER -->
<div id="header"> 
<?php include 'Header.php';?>
</div>

<!-- MAIN CONTENT -->
<div id="content">
  	<div>
      	<!-- ACCOUNT ACTIVATION INFORMATION --> 
    	<h1>Shelfshare Account Activation</h1>
  	</div>
  	<div>
  	    <p>Welcome to Shelfshare!</p>
  	</div>
  	
  	<?php
    include 'Connect.php';
    
    // Define variables and initialise with empty values
    $sqlquery = $result = $rows = "";
    
    // Get verification code from email link
    $code = trim(mysqli_real_escape_string($db, $_GET['code']));  
    
    // First check if record exists
    $sqlquery = "SELECT ID FROM Accounts WHERE VCode = '$code' and Verified = '0'";
    
    // Run query to check if verification code exists
    $result = mysqli_query($db,$sqlquery);
    
    if ($result) {
        // Update the verified field from 0 to 1
        $sqlquery = "UPDATE Accounts SET verified = '1' WHERE VCode = '$code'";
    
        // Run query to update verified field
        $result = mysqli_query($db,$sqlquery);
    
        // Inform user if account was verified
        if ($result) {
            // Redirect user to login page
            header('Refresh:5; url=Login.php');
            
            echo "<script>alert('Welcome to Shelfshare! Your email has been verified');</script>";
        }
        else {
            echo "<script>alert('Your account was unable to be verified. Please request new verification code by completing your login details on the Login page and selecting the resend email notification checkbox.');</script>";
        }
    }
    else {
        // Inform user to re-register an account
        echo "<script>alert('The verification code is incorrect. Please re-register.');</script>";
    }
    
    
    ?>
  	
</div>

<!-- COPYRIGHT YEAR, AUTHOR, SITEMAP, AND PRIVACY POLICY -->
<div id="footer">
<?php include 'Footer.php';?>
</div>

</body>
</html>