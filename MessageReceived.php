<?php
session_start();
include_once "Connect.php";
$pageTitle = 'Message Received';

// redirect users that aren't logged in to login.
if (!isset($_SESSION['userID'])) {
    header("Location: Login.php");
}

// Define variables and initialize with empty values
$messageID = $senderID = $sender = $date = $title = $messageID = $message = $bookID = "";
$statusquery = $statusresult = $notifyquery = $notifyresult = $notifyrow = "";
$accountquery = $accountresult = $accountrow = $bookquery = $bookresult = $bookrow = "";

// Get message id
$messageID = trim(mysqli_real_escape_string($db,$_GET['message']));

// Update message status
$statusquery = "UPDATE Notify SET Status = 1 WHERE ID = $messageID";

// Run the query to update message status to read
$statusresult = mysqli_query($db,$statusquery);

// Display header with updated notification count
include("Header.php");
    
// Check if record exists
$notifyquery = "SELECT * FROM Notify WHERE ID = '$messageID'";
    
// Run query to check if message exists
$notifyresult = mysqli_query($db,$notifyquery);
    
// Get message query results
$notifyrow = mysqli_fetch_assoc($notifyresult);

// If message exists
if ($notifyresult) {
    
    // Display fields for selected message
    $senderID = $notifyrow['Sender'];
    $date = $notifyrow['Publish_Date'];
    $bookID = $notifyrow['BookID'];
    $message = $notifyrow['Notify_Msg'];
        
    // Check if sender account exists
    $accountquery = "SELECT * FROM Accounts WHERE ID = $senderID";
        
    // Run query to get sender account details
    $accountresult = mysqli_query($db,$accountquery);
        
    // Get sender account query results
    $accountrow = mysqli_fetch_assoc($accountresult);
    
    // If sender account exists
    if ($accountresult) {
        // Display fields for sender
        $sender = $accountrow['FirstName'] . " " . $accountrow['LastName'];
    }
}

// Build query to get book details
$bookquery = "SELECT * FROM Books WHERE BookID = $bookID";
                
// Run query to check if book exists
$bookresult = mysqli_query($db,$bookquery);
    
// Get book query results
$bookrow = mysqli_fetch_assoc($bookresult);

// Display field for title of book 
$title = $bookrow['Title'];

?>

    <!-- MESSAGE RECEIVED HTML -->
	<h1 class="page-header">
	    <!-- Page Title -->
		Message Received
	</h1>

	<div class="content-box login">

        <!-- Send form details to message reply page -->
        <form action="MessageReply.php" method="post" enctype="multipart/form-data">
            <input type="hidden" id="SenderID" name="SenderID" value="<?php echo $senderID; ?>">
            <div class="form-group">
                <label for="Sender">Sender:</label>
                <input type="text" class="form-control" id="Sender" name="Sender" value="<?php echo $sender; ?>" readonly>
            </div>
             <div class="form-group">
                <label for="Date">Date</label>
                <input type="text" name="Date" class="form-control" value="<?php echo $date; ?>" readonly>
            </div>
            <input type="hidden" id="BookID" name="BookID" value="<?php echo $bookID; ?>">
            <div class="form-group">
                <label for="Title">Book Title:</label>
                <input type="text" class="form-control" id="Title" name="Title" value="<?php echo $title; ?>" readonly>
            </div>
            <div class="form-group">
                <label for="Message">Message</label>
                <textarea name="Message" class="form-control" cols="50" rows="5" readonly><?php echo $message; ?></textarea>
            </div>
	    
        <!-- Submit form button -->
        <button type="submit" value="Reply" class="btn btn-primary">Reply</button>

        </form> 
	</div>

<?php include("Footer.php"); ?>