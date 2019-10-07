<?php
session_start();
$pageTitle = 'Create Account';
include_once "Connect.php";
// log users out
if (isset($_SESSION['current_user'])) {
    unset($_SESSION['current_user']);
    unset($_SESSION['userID']);
}
include("Header.php");

// Define variables and initialize with empty values
$useremail = $userpassword = $confirmpassword = $error = "";
$hashedpassword = $sqlquery = $result = "";
$firstname = $lastname = $pcode = "";

// React only to POSTed form data
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
  // put the form data in variables, removing database-dangerous characters and trimming // 
  $useremail = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['Email'])));  
  $userpassword = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['Password'])));
  $confirmpassword = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['ConfirmPassword'])));
  $firstname = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['FirstName'])));  
  $lastname = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['LastName'])));  
  $pcode = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['PCode'])));
  
  if ((preg_match('/[^0-9]/', $pcode)) or (strlen($pcode) != 4)) {
    $error = "You must enter a valid postcode.";        
    }
  else {
    if (empty($firstname) or empty($lastname)) {
      $error = "You must enter a first and a last name.";
    }
    else {      
      // check if any dangerous characters were used. Preg_match here allows a-9 . , ! ? //
      if ($useremail != $_POST['Email'] or $userpassword != $_POST['Password'] or $confirmpassword !=  $_POST['ConfirmPassword']  or $firstname != $_POST['FirstName'] or $lastname != $_POST['LastName'] or $pcode != $_POST['PCode'] 
        or preg_match('/[^a-zA-Z0-9\.\,\?\!]/', $userpassword) or preg_match('/[^a-zA-Z0-9\.\,\?\!]/', $confirmpassword) or preg_match('/[^a-zA-Z\.\,]/', $firstname) or preg_match('/[^a-zA-Z\.\,]/', $lastname)) { 
        $error = "You have entered illegal characters. Please try again.";
      }
      else {
        // check that the email entered is in an email format //
        if (preg_match('/^\w+([\.-]?\w{1})*(@{1}\w+){1}([\.]{1}\w*){1}([\.]{1}\w+)*$/', $useremail) == 0) {
          $error = "Please enter a valid email address.";
        }
        else {
          // check if the email entered already exists in the database //
          // prepare the query for checking if the email exists //
          $sqlquery = "SELECT COUNT(Email) as total FROM Accounts WHERE Email = '$useremail'";
            
          // run the query to check if the email exists //
          $result = mysqli_query($db,$sqlquery);
            
          // get the results of the query //
          $rows = mysqli_fetch_assoc($result);

          if ($rows[total] != 0) {
            $error = "An account already uses that email address.";    
            }
          else {
            // check the password is at least the minimum length //
            if (strlen($userpassword) < 6) {
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
                          
                // Generate unique random code for email verification code
                $verification = substr(md5(mt_rand()),0,25);
                          
                // Create the account in the database //
                // prepare a query to insert the account //
                $sqlquery = "INSERT INTO Accounts (Email, FirstName, LastName, Password, PCode, VCode) VALUES ('$useremail', '$firstname', '$lastname', '$hashedpassword', '$pcode', '$verification')";
                         
                // Run the query to create the account in the database //
                $result = mysqli_query($db,$sqlquery);
                
                // Get the user's newly created ID
                $idquery = "SELECT ID FROM Accounts WHERE Email = '$useremail'";
                // Run the query to get the ID
                $idresult = mysqli_query($db,$idquery);          
                // get the results of the query //
                $idrow = mysqli_fetch_assoc($idresult);
                $userID = $idrow['ID'];
                                
                // write the welcome message to send people.
                $welcome = "Welcome to Shelfshare! Please update your details on the Profile Settings page to get the best results from Shelfshare services.";
                
                // Send the user's account a notification to welcome them and encourage them to update their user details.
                $welcomequery = "INSERT INTO Notify (Recipient, Sender, Notify_Type, BookID, Notify_Msg, Status) VALUES ('$userID', 1, 2, 1, '$welcome', 0)";
                // Run the query to send the notification
                $welcomeresult = mysqli_query($db,$welcomequery);                             
                // Send email after signup with verification code
                $verificationLink = $hostAddress  . "/Activate.php?code=" . $verification;
                                
                // Generate email
                require 'vendor/autoload.php';
                $sendgrid_apikey = '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';
                $email = new \SendGrid\Mail\Mail();
                $email->setSubject("Shelf Share Email Verification");
                $email->addTo($useremail, $firstname . $lastname);
                $email->setFrom("test@example.com", "Shelf Share");
                $email->setGlobalSubject("Shelf Share Email Verification");
                $email->addContent(
                    "text/plain",
                    "REGISTRATION CONFIRMATION. Thank you for registering your details. Please click the verify email link below to verify your subscription to Shelf Share. Once your email has been verified, you can then Login with your email and password." . $verificationLink
                );
                $email->addContent(
                    "text/html",
                    "<b>REGISTRATION CONFIRMATION</b><br /><br /><br />Thank you for registering your details. Please click the verify email link below to verify your subscription to Shelf Share.<br /><br />' . '<a href=' . $verificationLink . '>VERIFY EMAIL</a><br /><br />Once your email has been verified, you can then Login with your email and password.<br /><br />Kind Regards,<br />Shelf Share"
                );
                $email->setTemplateId("d-8ee4a020b8c746769a94565b056c1470");
                $email->addDynamicTemplateData("verificationLink", $verificationLink);
                  
                $sendgrid = new \SendGrid($sendgrid_apikey);
                
                $response = $sendgrid->send($email);
                
                // Redirect user to login page
                header('Refresh:3; url=Login.php');
                            
                // Tell user an email has been sent with a verification link
                echo "<script>alert('An email has been sent to you with a verification link to activate your account with Shelfshare');</script>";
                        
                }
              }   
            }
          }
        }
      }       
    }
  }
?>
	  <!-- CREATE ACCOUNNT PAGE HTML -->
		<h1 class="page-header">
			Create an Account
		</h1>

		<div class="content-box login">
      <p class='error'><?php echo $error;?></p>
        <form action="" method="post">
          <div class="form-group">
            <label for="email">Email address:</label>
            <input type="email" class="form-control" id="email" name="Email" value="<?php echo $useremail;?>">
          </div>
          <div class="form-group">
            <label for="fname">First name:</label>
            <input type="text" class="form-control" id="fname" name="FirstName" value="<?php echo $firstname;?>">
          </div>
          <div class="form-group">
            <label for="lname">Last name:</label>
            <input type="text" class="form-control" id="lname" name="LastName" value="<?php echo $lastname;?>">
          </div>
          <div class="form-group">
            <label for="pcode">Post code:</label>
            <input type="text" class="form-control" id="pcode" name="PCode" value="<?php echo $pcode;?>">
          </div>
          <div class="form-group">
            <label for="pwd">Password:</label>
            <input type="password" class="form-control" id="pwd" name="Password">
          </div>
          <div class="form-group">
            <label for="pwd">Confirm Password:</label>
            <input type="password" class="form-control" id="pwd" name="ConfirmPassword">
          </div>
          <div class=form-group>
              <input type="checkbox" required name="terms">
              <label for="terms"> I accept the
              <span onclick="showterms()"> <a href="#"> Terms and Conditions</a></a></span>
            </label>
          </div>
          <button type="submit" value=" Submit " class="btn btn-primary">Submit</button>
          <p class="create-p">Already have an account? <a href="Login.php">Login here</a>.</p>
        </form> 
		</div>
		
		<!-- Terms and Conditions HTML -->
		  <div id="termswindow">
		  <?php include_once "TermsAndConditions.php"; ?>  </div>

<?php
include("Footer.php");
?>

<script>

function showterms() {
  var terms = document.getElementById("termswindow");
    terms.style.display = "block";
}

function hideterms() {
    var terms = document.getElementById("termswindow");
    terms.style.display = "none";
}

</script>
