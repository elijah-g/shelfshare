<?php
session_start();
include_once "Connect.php";
$pageTitle = 'Message Sent';

// redirect users that aren't logged in to login.
if (!isset($_SESSION['userID'])){
    header("Location: Login.php");
}

// Define variables and initialize with empty values
$recipientID = $recipient = $date = $bookID = $title = $message = "";
$statusquery = $statusresult = $sqlquery = $displayresult = $displayrow = $query = $result = $row = "";

include("Header.php");

$userID = $_SESSION['userID'];
    
// React only to POSTed form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // sanitize entries to make safe for database.
    $recipientID = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['RecipientID'])));
    $recipient = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['Recipient'])));
    $publish_date = date("Y-m-d H:i:s");  
    $bookID = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['BookID'])));
    $title = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['Title'])));
    $message = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['Message'])));

    // Sql query to insert message for matched books
    $query = "INSERT INTO Notify (Recipient, Sender, Notify_Type, BookID, CorrespondingBookID, Notify_Msg, Publish_Date, Status) VALUES ('$recipientID', '$userID', 0, '$bookID', 0, '$message', '$publish_date', 0)";
            
    // Run the query to send message record
    $result = mysqli_query($db,$query);
}
?>

    <!-- MESSAGE INFORMATION -->
	<h1 class="page-header">
		Message Sent
	</h1>
	<p style="font:bold; color:blue;">The following message has been sent. The recipient will reply at their earliest convenience.</p>

	<div class="content-box login">

        <form action="" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="Sender">Recipient:</label>
            <input type="text" class="form-control" id="Recipient" name="Recipient" value="<?php echo $recipient; ?>" readonly>
        </div>
         <div class="form-group">
            <label for="Date">Date</label>
            <input type="text" name="Date" class="form-control" value="<?php echo $publish_date; ?>" readonly>
        </div>
        <div class="form-group">
            <label for="Title">Book Title:</label>
            <input type="text" class="form-control" id="Title" name="Title" value="<?php echo $title; ?>" readonly>
        </div>
        <div class="form-group">
            <label for="Message">Message</label>
            <textarea name="Message" class="form-control" cols="50" rows="5" readonly><?php echo $message; ?></textarea>
        </div>
        </form> 
	</div>
	
<?php include("Footer.php"); ?>