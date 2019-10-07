<?php
session_start();
include_once "Connect.php";
$pageTitle = 'Delete';

// Set this variable to the number 1 or 2 to activate each of the 2 tests on this page.
$testmode = 0;
// 1 tests redirecting unlogged in users to the log in page upon arrival. This test logs you out.
// 2 tests displaying dummy data from the database. Book ID 1 holds the dummy data.
// These tests will display warnings if the code fails to behave as intended.

// test 1, to see if the following isset() sends unlogged-in users to login.php 
if ($testmode == 1) { 
    unset($_SESSION['current_user']);
    unset($_SESSION['userID']);
}
// redirect users that aren't logged in to login.
if (!isset($_SESSION['userID'])){
    header("Location: Login.php");
}
// detector for test 1 results
if ($testmode == 1) {
    echo "test 1 failed: You were not properly redirected.";
}
include("Header.php");

// Define variables and initialize with empty values
$ISBN = $title = $authors = $bookcondition = $image = $error = "";
$year = $ownerID = "";
$bookID = $displayquery = $displayrow = "";
$category = "";

// Get book code
$bookID = trim(mysqli_real_escape_string($db, $_GET['book']));  
    
// test 2, loading in known dummy data for display 
if ($testmode == 2) {
    $bookID = 1;
}    
    
// Check if record exists
$sqlquery = "SELECT * FROM Books WHERE BookID = '$bookID'";
    
// Run query to check if book exists
$displayresult = mysqli_query($db,$sqlquery);
    
// Get query results with mysqli_fetch_assoc
$displayrow = mysqli_fetch_assoc($displayresult);

if ($displayresult)  {
    // Display fields for selected book
    $ISBN = $displayrow['ISBN'];
    $title = $displayrow['Title'];
    $authors = $displayrow['Authors'];
    $year = $displayrow['Year'];
    $category = $displayrow['Category'];
    $bookcondition = $displayrow['BookCondition'];

// detector for test 2, checks that dummy data was found and loaded properly.
if ($testmode == 2) {
    if ($ISBN != '1' or $title != '1' or $authors != '1' or $year != '2000' or $category != 'Architecture' or $bookcondition != '1') {
        echo "test 2 failed, code not behaving as expected, or dummy data not in database.";
    }
    } 
    
    // If the Delete Book button has been selected
	if (isset($_POST['delete'])) {
		$userID = $_SESSION['userID'];
			
		// Delete book from database
		$sqlquery = "UPDATE Books SET BookState = 2 WHERE BookID = '$bookID' AND OwnerID = '$userID'";
			    
	    // Run query to update database
		$result = mysqli_query($db,$sqlquery);
		    
		// Inform user that the account has been deleted
		if ($result) {
			echo "<script>alert('Your book has been deleted with Shelfshare!');</script>";
				
			// Redirect user to create account page
            header("Location: Profile.php");
		}
		else {
		   	echo "<script>alert('Your book was unable to be deleted. Please try again.');</script>";
		}
	}
}
?>

    <!-- DELETE BOOK INFORMATION -->
	<h1 class="page-header">
		Delete a book
	</h1>

	<div class="content-box login">

        <form action="" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="ISBN">ISBN:</label>
            <input type="text" class="form-control" id="ISBN" name="ISBN" value="<?php echo $ISBN; ?>" readonly>
        </div>
        <div class="form-group">
            <label for="pwd">Title:</label>
            <input type="text" class="form-control" id="Title" name="Title" value="<?php echo $title; ?>" readonly>
        </div>
        <div class="form-group">
            <label>Authors</label>
            <input type="text" name="Authors" class="form-control" value="<?php echo $authors; ?>" readonly>
            <span class="help-block"></span>
        </div>
	    <div class="form-group">
            <label>Year</label>
            <input type="text" name="Year" class="form-control" value="<?php echo $year; ?>"readonly>
            <span class="help-block"></span>
        </div>
        <div class="form-group">
            <label>Category</label>
            <select type="select" name="Category" class="form-control" >
                <option value = "<?php echo $category; ?>" readonly><?php echo $category; ?></option>
                <option value = "Architecture">Architecture</option>
                <option value = "Art">Art</option>
                <option value = "Biology">Biology</option>
                <option value = "Computer Science">Computer Science</option>
                <option value = "Education">Education</option>           
                <option value = "Mathematics">Mathematics</option>
                <option value = "Natural Science">Natural Science</option>
                <option value = "Social Science">Social Science</option>
                <option value = "Philosophy">Philosophy</option>
                <option value = "">leave empty</option>
            </select>    
            <span class="help-block"></span>
        </div>
        <div class="form-group <?php echo (!empty($error)) ? 'has-error' : ''; ?>">
            <label>Book Condition</label>
            <input type="text" name="BookCondition" class="form-control" value="<?php echo $bookcondition; ?>" readonly>
            <span class="help-block"><?php echo $error; ?></span>
        </div>   
        <div class="form-group">
	        <label for="image">Book Image: </label>
	        <br/>
		    <input type="file" name="image" id="image">
		    <?php 
		    // Display book image from database or use default book image
	        if (empty($displayrow['Image'])) { 
	            echo "<img id='book-image' class='settings-image' src='imgs/book-icon.jpg' alt='Book Image' height='100' width='100'>";
	        }
	        else {?>
	            <img id='book-image' class='settings-image' alt='Book Image' height='100' width='100' src='data:image;base64, <?php echo $displayrow['Image'];?>' ><?php 
            }?>
	    </div>

        <input id="delete" name="delete" class="btn btn-danger" type="submit" value="Delete Book" />

        </form> 
	</div>
	
<?php
include("Footer.php");
?>