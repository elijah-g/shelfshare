<?php
    include 'Connect.php';
    $pageTitle = 'Notifications';
    include 'Header.php';
  
// Set this test to the number 1 - 3 to activate each of the 3 tests on this page.
$testmode = 0;
// 1 tests redirecting unlogged in users to the log in page upon arrival. This test logs you out.
// 2 tests displaying a dummy notification from the database editing. Notification ID 1 holds the dummy data.
// 3 tests retrieving the ID and displaying the name of senders associated with a message notification.
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
?>
    <!-- NOTIFICATIONS PAGE INFORMATION -->
   	<h1 class="page-header">
   		
   	    <?php $user = $_SESSION['current_user'];
   	    $userID = $_SESSION['userID'];
    
        // prepare the query for checking user details //
        $query = "SELECT * FROM Accounts WHERE Email = '$user'";
                    
        // run the query to get user details //
        $result = mysqli_query($db,$query);
            
		// get row FirstName to display username
		while($row = mysqli_fetch_array($result)):;?>
		    <?php echo $row['FirstName'];?>'s Notifications
		<?php endwhile;?>
			
	</h1>
		
	<?php
    // prepare the query for getting notifications //
    $sqlquery = "SELECT * FROM Notify WHERE Recipient = '$userID' ORDER BY Status, Publish_Date DESC";
    
    // unit test 2, to see if the database returns the dummy data for display. Dummy data is ID 1 in Notify
    if ($testmode == 2 or $testmode == 3) {
        $sqlquery = "SELECT * FROM Notify WHERE ID = 1";
    }
		// run the query to get notifications //
		$result1 = mysqli_query($db,$sqlquery);
	   
	    // display resuts of query for listed book details
        while($row1 = mysqli_fetch_array($result1)):;
            $bookID = $row1['BookID'];
    
         if ($testmode == 3) { 
            $row1['Notify_Type'] = 0;
        } 
        
                // if welcome message, display:
                 if ($row1['Notify_Type'] == 2) {?>
                    <a href="AccountSettings.php">
                    <!-- add class with read messages for grey color -->
                    <div class="content-box <?php if ($row1['Status'] != 0){echo "notify-read";}?>">
        	             <img src="imgs/bell2-icon.png" class="notify-bell2" alt="Message ">
        	            <div class='notify-date'> <?php echo date('d-M-Y', strtotime($row1['Publish_Date']));?> </div>
                        <p> <?php echo $row1['Notify_Msg'] ?> </p>
                    </div>
                </a>
                <?php 
                // if unread, change welcome notif status to read
                $updatequery = "UPDATE Notify SET Status = 1 WHERE Recipient = $userID AND Notify_Type = 2";
                $updateresult = mysqli_query($db,$updatequery);
                 }
        
        
                // if match notification, display:
                if ($row1['Notify_Type'] == 1) { ?>
                    <a href="BookMatch.php?message=<?php echo $row1['ID'];?>&book=<?php echo $row1['BookID'];?>">
                    <!-- add class with read messages for grey color -->
                    <div class="content-box <?php if ($row1['Status'] != 0){echo "notify-read";}?>">
        	             <img src="imgs/bell2-icon.png" class="notify-bell2" alt="Message ">
        	            <div class='notify-date'> <?php echo date('d-M-Y', strtotime($row1['Publish_Date']));?> </div>
                        <p <?php if ($row1['Status'] == 0){echo "style='color:red'";}?>> Notification: Book Match Found!! </p>
                    </div>
                </a>
                <?php 
                    // detector for unit test 2                
            if ($testmode == 2) {
                if ($row1['Recipient'] != '1' or $row1['Sender'] != '1' or $row1['BookID']!= '1' or $row1['Notify_Msg'] != '1' or $row1['Status'] != '1') {
                    echo "test 2 failed, database lacks test data or code not behaving as expected.";
                }
            } 
                }
                
                // if message notification, display 
                if ($row1['Notify_Type'] == 0) { ?>
                <!-- Link to entire message -->
                <a href="Message.php?to=<?php echo $row1['BookID'];?>&from=<?php echo $row1['CorrespondingBookID'];?>">
                    <div class="content-box <?php if ($row1['Status'] != 0){echo "notify-read";}?>" >
                        <img src="imgs/mail-icon.png" class="notify-envelope" alt="Message ">
                        <?php 
                            // Get name of query sender using their ID
        	                $SenderID = $row1['Sender'];
            
                            // Create query for getting sender details
                            $query = "SELECT * FROM Accounts WHERE ID = $SenderID";
                            
                            // Run query to get Sender details
                            $result = mysqli_query($db,$query);
                            
                            // Get query results
                            $row = mysqli_fetch_array($result);
                            
                            ?>
        	            
        	            <div class='notify-date'> <?php echo date('d-M-Y', strtotime($row1['Publish_Date']));?> </div>
                        <p> Message from:  <?php echo $row['FirstName']; ?> </p>
                        <p> Book Title: <?php 
                            $query2 = "SELECT * FROM Books WHERE BookID = $bookID";
                
                            // Run query to check if book exists
                            $result2 = mysqli_query($db,$query2);
                
                            // Get query results with mysqli_fetch_assoc
                            $row2 = mysqli_fetch_assoc($result2);
                            
                            $Title = $row2['Title'];
                            
                            echo $Title;?>
                        </p>
                    </div>
                </a>
        <?php }
        endwhile;
        
        
        
        // detector for unit test 3
        if ($testmode == 3) {
            if ($row['FirstName'] != 'ShelfShare') {
               echo "test 3 failed, the ID of the associated sender was not in the database or code not behaving as expected."; 
             }
        }
        ?>
		    

<?php include 'Footer.php';?>