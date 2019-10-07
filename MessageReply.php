<?php
session_start();
include_once "Connect.php";
$pageTitle = 'Message Sent';

// redirect users that aren't logged in to login.
if (!isset($_SESSION['userID'])){
    header("Location: Login.php");
}

// Define variables and initialize with empty values

$statusquery = $statusresult = $sqlquery = $displayresult = $displayrow = $query = $result = $row = "";

include("Header.php");

$userID = $_SESSION['userID'];
    
// React only to POSTed form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // sanitize entries to make safe for database.
    $recipientID = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['SenderID'])));
    $recipient = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['Sender'])));
    $bookID = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['BookID'])));
    $title = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['Title'])));
    $pmessage = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['Message'])));

    // Get date and time
    $publish_date = date("Y-m-d H:i:s");
}
?>

    <!-- MESSAGE INFORMATION -->
	<h1 class="page-header">
		Message Reply
	</h1>
	<div class="content-box login">

        <form action="MessageSent.php" method="post" enctype="multipart/form-data">
            <input type="hidden" id="RecipientID" name="RecipientID" value="<?php echo $recipientID; ?>">
        <div class="form-group">
            <label for="Recipient">Recipient:</label>
            <input type="text" class="form-control" id="Recipient" name="Recipient" value="<?php echo $recipient; ?>" readonly>
        </div>
         <div class="form-group">
            <label for="Date">Date</label>
            <input type="text" name="Publish_Date" class="form-control" value="<?php echo $publish_date; ?>" readonly>
        </div>
        <input type="hidden" id="BookID" name="BookID" value="<?php echo $bookID; ?>">
        <div class="form-group">
            <label for="Title">Book Title:</label>
            <input type="text" class="form-control" id="Title" name="Title" value="<?php echo $title; ?>" readonly>
        </div>
        <div class="form-group">
            <label for="Message">Message</label>
            <textarea name="Message" class="form-control" cols="50" rows="5"></textarea>
        </div>
        <div class="form-group">
            <label for="PMessage">Previous Message</label>
            <textarea name="PMessage" class="form-control" cols="50" rows="5"><?php echo $pmessage; ?></textarea>
        </div>
        
        <button type="submit" value="Send" class="btn btn-primary">Send</button>
        
        </form> 
	</div>
	
<?php
include("Footer.php");
?>