<?php
session_start();
include_once "Connect.php";
$pageTitle = 'Message Send';

// redirect users that aren't logged in to login.
if (!isset($_SESSION['userID'])) {
    header("Location: Login.php");
}

// Define variables and initialize with empty values
$bookID = $ISBN = $title = $authors = $year = $category = $bookcondition = $name = $image = "";
$recipientID = $recipient = $date = $title = $message = "";
$bookquery = $bookresult = $bookrow = $accountquery = $accountresult = $accountrow = "";

include("Header.php");
    
// React only to POSTed form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get book details
    $bookID = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['BookID'])));
    $ISBN = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['ISBN'])));
    $title = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['Title'])));
    $authors = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['Authors'])));
    $year = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['Year'])));
    $category = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['Category'])));
    $bookcondition = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['BookCondition'])));
    $name = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['ImageName'])));
    $image = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['Image'])));
    
    // Build query to check book records
    $bookquery = "SELECT * FROM Books WHERE BookID = $bookID";
    
    // Run query to check if book exists
    $bookresult = mysqli_query($db,$bookquery);
    
    // Get book query results
    $bookrow = mysqli_fetch_assoc($bookresult);
    
    // If book exists
    if ($bookresult)  {
        
        // Get recipient ID from book details
        $recipientID = $bookrow['OwnerID'];
        
        // Check for recipient account details
        $accountquery = "SELECT * FROM Accounts WHERE ID = $recipientID";
            
        // Run query to get recipient details
        $accountresult = mysqli_query($db,$accountquery);
            
        // Get recipient query results
        $accountrow = mysqli_fetch_assoc($accountresult);
        
        // If recipient account exists
        if ($accountresult) {
            
            // Display recipient details
            $recipient = $accountrow['FirstName'] . " " . $accountrow['LastName'];
        }
    }
    
    // Get date and time
    $date = date("Y-m-d H:i:s");           
}
?>

    <!-- SEND MESSAGE HTML -->
	<h1 class="page-header">
	    <!-- Page Title -->
		Send Message
	</h1>

	<div class="content-box login">

        <!-- Send form details to message sent page -->
        <form action="MessageSent.php" method="post" enctype="multipart/form-data">
            <input type="hidden" id="RecipientID" name="RecipientID" value="<?php echo $recipientID; ?>">
            <div class="form-group">
                <label for="Sender">Recipient:</label>
                <input type="text" class="form-control" id="Recipient" name="Recipient" value="<?php echo $recipient; ?>" readonly>
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
                <textarea name="Message" class="form-control" cols="50" rows="5"><?php echo $message; ?></textarea>
            </div>
        
        <!-- Submit form button -->
        <button type="submit" value="Send" class="btn btn-primary">Send</button>

        </form> 
	</div>
	
<?php include("Footer.php"); ?>