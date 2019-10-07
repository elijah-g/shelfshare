<?php
session_start();
include_once "Connect.php";
$pageTitle = 'Book Match';

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

// Define variables and initialize with empty values
$ISBN = $title = $authors = $bookcondition = $image = $error = "";
$year = $ownerID = "";
$bookID = $displayquery = $displayrow = "";
$category = "";

// Get message id
$messageID = trim(mysqli_real_escape_string($db,htmlspecialchars($_GET['message'])));

// Update message status
$statusquery = "UPDATE Notify SET Status = 1 WHERE ID = $messageID";

// Run the query to update message status to read
$statusresult = mysqli_query($db,$statusquery);

include("Header.php");

// Get book code
$bookID = trim(mysqli_real_escape_string($db, $_GET['book']));  
    
// test 2, loading in known dummy data for display 
if ($testmode == 2) {
    $bookID = 1;
}    
    
//Get the corresponding book id
$query = "SELECT CorrespondingBookID FROM Notify WHERE ID = $messageID";
$result = mysqli_query($db,$query);
$displayrow = mysqli_fetch_assoc($result);
$other_book_id = $displayrow["CorrespondingBookID"];    
    
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
    $bookstate = $displayrow['BookState'];
    $image = $displayrow['Image'];
    $ownerID = $displayrow['OwnerID'];
    
    // Check if record exists
    $sqlquery1 = "SELECT * FROM Accounts WHERE ID = '$ownerID'";
        
    // Run query to check if book exists
    $displayresult1 = mysqli_query($db,$sqlquery1);
        
    // Get query results with mysqli_fetch_assoc
    $displayrow1 = mysqli_fetch_assoc($displayresult1);
    
    if ($displayresult1)  {
        // Display fields for selected book
        $location = $displayrow1['Location'];
        $state = $displayrow1['State'];
        $pcode = $displayrow1['PCode'];
    }
    
    // detector for test 2, checks that dummy data was found and loaded properly.
    if ($testmode == 2) {
        if ($ISBN != '1' or $title != '1' or $authors != '1' or $year != '2000' or $category != 'Architecture' or $bookcondition != '1') {
            echo "test 2 failed, code not behaving as expected, or dummy data not in database.";
        }
    }
}
?>

    <!-- MATCHED BOOK INFORMATION -->
	<h1 class="page-header">
		Matched Book
	</h1>
    
        <!-- Insert back button to return to Requested Book Matches -->
        <input type="button" class="btn btn-primary" value="Back to Matches" onClick="history.go(-1);">
            <!-- Show available book details -->
            <div class="content-box login">
        	    <form action="MessageSend.php" method="post" enctype="multipart/form-data">
        	    <input type="hidden" id="BookID" name="BookID" value="<?php echo $bookID; ?>">
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
                </div>
                <div class="form-group <?php echo (!empty($error)) ? 'has-error' : ''; ?>">
                    <label>Book Condition</label>
                    <input type="text" name="BookCondition" class="form-control" value="<?php echo $bookcondition; ?>" readonly>
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
        	    
        	    <!-- Insert Google Maps showing where the book is located -->
        	    <label>Book Location</label>
        	    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
                    <style>
                        #gmap {
                            height: 30em;
                            width: 100%;
                        }
                    </style>
                <?php  
                    // function to geocode address details
                    function getGeocodeData($address) { 
                        $address = urlencode($address);     
                        $googleMapUrl = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=AIzaSyBcg9FQmYczStSJNJVxu-XwxS7UfSzB5vc";
                        $geocodeResponseData = file_get_contents($googleMapUrl);
                        $responseData = json_decode($geocodeResponseData, true);
                        if($responseData['status']=='OK') {
                            $latitude = isset($responseData['results'][0]['geometry']['location']['lat']) ? $responseData['results'][0]['geometry']['location']['lat'] : "";
                            $longitude = isset($responseData['results'][0]['geometry']['location']['lng']) ? $responseData['results'][0]['geometry']['location']['lng'] : "";
                            $formattedAddress = isset($responseData['results'][0]['formatted_address']) ? $responseData['results'][0]['formatted_address'] : "";         
                            if($latitude && $longitude && $formattedAddress) {         
                                $geocodeData = array();                         
                                array_push(
                                    $geocodeData, 
                                    $latitude, 
                                    $longitude, 
                                    $formattedAddress
                                );             
                                return $geocodeData;             
                            } else {
                                return false;
                            }         
                        } else {
                            echo "ERROR: {$responseData['status']}";
                            return false;
                        }
                    }
                    
                    $searchaddress = "$state" . " " . "$pcode" . " " . "Australia";
                    
                	// get geocode address details
                    $geocodeData = getGeocodeData($searchaddress); 
                    if($geocodeData) {         
                    	$latitude = $geocodeData[0];
                    	$longitude = $geocodeData[1];
                    	$address = $geocodeData[2];                     
                    ?> 
                    <div id="gmap">Loading map.</div>
                    <script type="text/javascript" src="https://maps.google.com/maps/api/js?key=AIzaSyBcg9FQmYczStSJNJVxu-XwxS7UfSzB5vc"></script>   
                    <script type="text/javascript">
                    	function init_map() {
                    		var options = {
                    			zoom: 14,
                    			center: new google.maps.LatLng(<?php echo $latitude; ?>, <?php echo $longitude; ?>),
                    			mapTypeId: google.maps.MapTypeId.ROADMAP
                    		};
                    		map = new google.maps.Map($("#gmap")[0], options);
                    		marker = new google.maps.Marker({
                    			map: map,
                    			position: new google.maps.LatLng(<?php echo $latitude; ?>, <?php echo $longitude; ?>)
                    		});
                    	}
                    	google.maps.event.addDomListener(window, 'load', init_map);
                    </script> 
                    <?php 
                    } else {
                    	echo "Incorrect details to show map!";
                    }
                
                ?>
                
                <!-- Insert details of the match (what was matched ???) -->
                	    
        	    </form> 
        	    <!-- Provide button to send book owner a message -->
                <button onclick="window.location.href = 'Message.php?to=<?php echo $bookID?>&from=<?php echo $other_book_id?>';" id="message-send" name="message" class="btn btn-primary" type="submit" value="Send Message">Send Message</button>
                
	        </div>
	        
	        
<?php include("Footer.php"); ?>