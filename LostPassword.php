<?php
include("Connect.php");
$pageTitle = 'Lost Password';
include("Header.php");
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
      
    // create empty variables first
    $useremail = $userpassword = $loginattempts = $storedpassword = "";
    $error = $result = "";
      
    // These escapes filter out NUL (ASCII 0), \n, \r, \, ', " for safety
    // The $_POST argument here grabs the user's entry in the fields
    $useremail = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['Email'])));
    if(isset($_POST['Resend'])){$resend = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['Resend'])));}
    
    // check that the email entered is in an email format //
    if (preg_match('/^\w+([\.-]?\w{1})*(@{1}\w+){1}([\.]{1}\w*){1}([\.]{1}\w+)*$/', $useremail) == 0) {
        $error = "Please enter a valid email address.";
    }
    
    // set up a query for grabbing the entered email
    $sqlquery = "SELECT ID, Email, Password, Verified, VCode FROM Accounts WHERE Email = '$useremail'";
      
    // run the query and grab the useremail
    $result = mysqli_query($db,$sqlquery);
    
    // print error if no such account exists
    if (mysqli_num_rows($result) === 0) {
        $error = "No such account exists. Please create an account.";
    }
 
    // these split the query results into variables //
    $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
    $useremail = $row['Email'];
    $verified = $row['Verified'];
    $storedpassword = $row['Password'];
    $verification = $row['VCode'];
    
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
        $resetPasswordLink = $hostAddress  . "/ResetPassword.php?Email=" . $useremail;
        
        // Generate email
        require 'vendor/autoload.php';
        $sendgrid_apikey = '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';
        $email = new \SendGrid\Mail\Mail();
        $email->setSubject("Shelf Share Reset Password");
        $email->addTo($useremail, $firstname . $lastname);
        $email->setFrom("test@example.com", "Shelf Share");
        $email->setGlobalSubject("Shelf Share Reset Password");
        $email->addContent(
            "text/plain",
            "RESET PASSWORD. You have requested to reset your password. Please click the reset password link below to change your password with Shelfshare. Once your password has been reset, you can then Login with your email and new password.<br /><br />Kind Regards,<br />Shelfshare" . $resetPasswordLink
        );
        $email->addContent(
            "text/html",
            "<b>RESET PASSWORD</b><br /><br /><br />You have requested to reset your password. Please click the reset password link below to reset your password with Shelfshare.<br /><br />' . '<a href=' . $resetPasswordLink . '>RESET PASSWORD</a><br /><br />Once your password has been reset, you can then Login with your email and new password.<br /><br />Kind Regards,<br />Shelfshare"
        );
        $email->setTemplateId("d-c111f91be8d54a23bbc760c767a2e218");
        $email->addDynamicTemplateData("resetPasswordLink", $resetPasswordLink);
                
        $sendgrid = new \SendGrid($sendgrid_apikey);
                
        $response = $sendgrid->send($email);
        
        // Redirect user to login page
        header('Refresh:3; url=Login.php');
        
        // Tell user an email has been sent with a verification link
        echo "<script>alert('An email has been sent to you with a reset password link to change your password with Shelfshare');</script>";
    }
}
?>

    <!-- FORGOT PASSWORD INFORMATION -->
		<h1 class="page-header">
			Forgot Password
		</h1>

		<div class="content-box login">
            <p class='error'><?php echo $error;?></p>
            <form action="<?php echo($_SERVER['PHP_SELF'])?>" method="post">
                <div class="form-group">
                    <label for="email">Email address:</label>
                    <input type="email" class="form-control" id="email" name="Email" value="<?php echo $useremail;?>">
                </div>
                <div class="form-group">
                    <input type="checkbox" id="Resend" name="Resend">
                    <label for="Resend">Resend email notification link</label>
                </div>
                <button type="submit" value=" Submit " class="btn btn-primary">Submit</button>
            </form> 
		</div>
		
<?php include("Footer.php");?>