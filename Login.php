<?php
include("Connect.php");
$pageTitle = 'Login';
include("Header.php");
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
      
    // create empty variables first
    $useremail = $userpassword = $loginattempts = $storedpassword = "";
    $error = $result = "";
      
    // These escapes filter out NUL (ASCII 0), \n, \r, \, ', " for safety
    // The $_POST argument here grabs the user's entry in the fields
    $useremail = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['Email'])));
    $userpassword = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['EnteredPassword'])));
    if(isset($_POST['Resend'])){$resend = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['Resend'])));}
      
    // set up a query for grabbing the hashed password for entered email
    $sqlquery = "SELECT * FROM Accounts WHERE Email = '$useremail'";
      
    // run the query and grab the hashed password
    $result = mysqli_query($db,$sqlquery);
    
    // print error if no such account exists
    if (mysqli_num_rows($result) === 0) {
        $error = "Account does not exist or password is incorrect.";
    }
 
    // these split the query results into variables //
    $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
    $verified = $row['Verified'];
    $storedpassword = $row['Password'];
    $verification = $row['VCode'];
    $firstname = $row['FirstName'];
    $lastname = $row['LastName'];
    $school = $row['School'];
    $location = $row['Location'];
    $state = $row['State'];
    $pcode = $row['PCode'];
    $gender = $row['Gender'];
    $imagename = $row['ImageName']; 
    $image = $row['Image'];
    
    // check the password entered was correct //
    if (password_verify($userpassword,$storedpassword)){
    if (($verified != TRUE) && (!isset($resend))) {
        $error = "That account has not yet been verified. Please check emails for link to verify this account or check box below and submit to resend email verification link.";
    }
        
    else if (($verified != TRUE) && (isset($resend))) {
        // Resend email with verification code
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
                        
        // Tell user an email has been sent with a verification link
        echo "<script>alert('An email has been resent to you with a verification link to activate your account with Shelfshare');</script>";
        }
        else {
            // this $_SESSION makes the user's email and ID globals. 
            $_SESSION['current_user'] = $useremail;
            $_SESSION['userID'] = $row['ID'];

            // successful login redirects to account settings if incompletely filled out        
            if ($firstname != '' AND $lastname != '' AND $school != '' AND $location != '' AND $state != '' AND $pcode != '' AND $pcode != '0' AND $gender != '' AND $image != '') {
            header("Location: Profile.php");
            }
            else {
                header("Location: AccountSettings.php");
            }
         }
      }
         else if (empty($error)) {
        $error = "Account does not exist or password is incorrect."; 
    }  
   }
?>

        <!-- LOGIN INFORMATION -->
		<h1 class="page-header">
			Login
		</h1>

		<div class="content-box login">
            <p class='error'><?php echo $error;?></p>
            <form action="<?php echo($_SERVER['PHP_SELF'])?>" method="post">
                <div class="form-group">
                    <label for="email">Email address:</label>
                    <input type="email" class="form-control" id="email" name="Email" value="<?php echo @htmlspecialchars($useremail);?>">
                </div>
                <div class="form-group">
                    <label for="pwd">Password:</label>
                    <input type="password" class="form-control" id="pwd" name="EnteredPassword" value="<?php echo @htmlspecialchars($userpassword);?>">
                </div>
                <div class="form-group">
                    <input type="checkbox" id="Resend" name="Resend">
                    <label for="Resend">Resend email notification link</label>
                </div>
                <div class="form-group">    
                <button type="submit" value=" Login " class="btn btn-primary">Submit</button>
                </div>
                <div>
                    <p class="create-p">Forgot password? <a href="LostPassword.php">Click here</a></p>
                    <p class="create-p">Dont have an account? <a href="CreateAccount.php">Create an Account</a></p>
                </div>
            </form> 
		</div>
		
<?php
include("Footer.php");
?>