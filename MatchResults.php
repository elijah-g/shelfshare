<?php
session_start();
include_once "Connect.php";
$pageTitle = 'Matches';
if (!isset($_SESSION['userID'])){
    header("Location: Login.php");
}


if (isset($_GET["BookID"])){
    
    include("Header.php");
    
    
    //Get the book id of the book just listed or requested
    $bookID = $_GET["BookID"];
    
    
    //Run the python script which contains the matching algorithm
    $command = escapeshellcmd('python3 algorithm.py ' . $bookID);
    $json_output = shell_exec($command);
    
    //Decode the matches from json
    $matches = json_decode($json_output, true);
    
    
    //Query db to get book details
    $query = "SELECT * FROM Books WHERE BookID = {$bookID}";
    $result = mysqli_query($db,$query);
    $displayrow = mysqli_fetch_assoc($result);
            
    $bookstate = $displayrow['BookState'];
    
    //Display page header
    echo '<h1 class="page-header">Matches</h1>';
    
    
    //Check if any matches were found
    if ((!empty($matches)) && ($bookstate == 0)){
        
        //Loop through matches
        foreach ((array)$matches as $match){
            //Assign variables from matches
            $m_bookID = $match["BookID"];
            $m_school = $match["SameSchool"];
            $m_distance = $match["Distance"];

            
            //Calculate distance bracket.
            $bracket = "";
            //List of brackets
            $distance_brackets = [10,20,30,40,50,60,70,80,100,150,200,300,500];
            //Loops through brackets to see which bracket we're in
            foreach((array)$distance_brackets as $d_bracket){
                if ($m_distance < $d_bracket){
                    $bracket = "Less than {$d_bracket}km";
                    //Break the loops once we have a match.
                    break;
                    
                }
                elseif($m_distance >= 500){
                    $bracket = "More than 500km";
                }
            }
            
            if ($m_school == 1){
                $m_school_flag = "Same School";
            }
            else{
                $m_school_flag = "";
            }
           
            
            //Query db to get remaining details
            $query = "SELECT * FROM Books WHERE BookID = {$m_bookID}";
            $result = mysqli_query($db,$query);
            $displayrow = mysqli_fetch_assoc($result);
            
            $m_title = $displayrow["Title"];
            $m_authors = $displayrow["Authors"];
            $m_condition = $displayrow["BookCondition"];
            $m_ownerID = $displayrow['OwnerID'];
            $m_bookstate = $displayrow['BookState'];
            
            // Book match details
            $bookmatch = "A listed book has been matched with your request";
            
            // Sql query to insert message for matched books
            $query1 = "INSERT INTO Notify (Recipient, Sender, Notify_Type, BookID, CorrespondingBookID, Notify_Msg, Publish_Date, Status) VALUES ('$m_ownerID', 0, 1, '$m_bookID', '$bookID', '$bookmatch', now(), 0)";
            
            $rating = getRating($m_ownerID, $db);
            
            // Run the query to send message record
            $result1 = mysqli_query($db,$query1);
            
            if ($result1) {
                
                // Get ID of message from database
                $query2 = "SELECT * FROM Notify WHERE Recipient = '$m_ownerID' AND BookID = '$m_bookID'";
                    
                // Run query to get message ID
                $result2 = mysqli_query($db,$query2);
                    
                // Display message ID number
                $displayrow2 = mysqli_fetch_assoc($result2);
                $m_messageID = $displayrow2['ID'];
            
                    //Output a div with the resulting match with message ID
                    echo <<<OUTPUT
                        <a href="Message.php?from=$m_bookID&to=$bookID">
                            <div class="content-box login match">
                                <h3>{$m_title}</h3>
                                <h5>Authors: {$m_authors}</h5>
                                <div class="row">
                                    <div class="col-sm-4"><p>{$m_school_flag}</p></div>
                                    <div class="col-sm-4"><p>Condition: {$m_condition}</p></div>
                                    <div class="col-sm-4"><p>Distance: {$bracket}</p></div>
                                </div>
OUTPUT;
?>
			<!--- display user rating stars --->
			<div id="m-star-div">
				<i  <?php if ($rating >= 1){echo "style='color:orange'";} else {echo "style='color:black'";}?> class="fas fa-star"></i>
				<i  <?php if ($rating >= 2){echo "style='color:orange'";} else {echo "style='color:black'";}?> class="fas fa-star"></i>
				<i  <?php if ($rating >= 3){echo "style='color:orange'";} else {echo "style='color:black'";}?> class="fas fa-star"></i>
				<i  <?php if ($rating >= 4){echo "style='color:orange'";} else {echo "style='color:black'";}?> class="fas fa-star"></i>
				<i  <?php if ($rating == 5){echo "style='color:orange'";} else {echo "style='color:black'";}?> class="fas fa-star"></i>
			</div>

    </div>
 </a>
<?php
            }
        }
    }

    else if ((empty($matches)) && ($bookstate == 0)) {   
        echo <<<OUTPUT
            <div class="content-box login match">
                <h3>No Matches</h3>
                <p>Thank you for listing your book with Shelfshare. There is currently no requests for this book. We will notify you when one is found.</p>
        </div>
OUTPUT;
    }
    else if ((empty($matches)) && ($bookstate == 1)) {
        echo <<<OUTPUT
            <div class="content-box login match">
                <h3>No Matches</h3>
                <p>Unfortunately we were unable to find any matches, we will notify you when one is found.</p>
                <a href="Profile.php"> My profile </a>
        </div>
OUTPUT;
    }
    
}


else {
    header("Location: Profile.php");
}
?>


<?php include("Footer.php");?>
