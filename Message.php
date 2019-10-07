<?php
include_once "Connect.php";

//Process post data which means a message has been sent
if (isset($_POST["to"])){
    $to_id = $_POST["to"];
    $from_id = $_POST["from"];
    $message_text = mysqli_real_escape_string($db,htmlspecialchars($_POST["message"]));
    $to_user = $_POST["to_user"];
    $from_user = $_POST["from_user"];
    
   
    //Add the sent message to the database
    $query = "INSERT INTO Notify (Recipient, Sender, Notify_Type, BookID, CorrespondingBookID, Notify_Msg, Publish_Date, Status) VALUES ($to_user, $from_user, 0, $to_id, $from_id, '$message_text', now(), 0);";
    echo $query;
    mysqli_query($db,$query);
    
    
    //Redirect/Reload the page
    header("Location: Message.php?to=$from_id&from=$to_id");
}


//Get the book ids of both books.
$current_book_id = $_GET["to"];
$other_book_id = $_GET["from"];

// Update message status
$statusquery = "UPDATE Notify SET Status = 1 WHERE BookID = $current_book_id AND CorrespondingBookID = $other_book_id";


// Run the query to update message status to read
$statusresult = mysqli_query($db,$statusquery);


// $current_book_id = 247;
// $other_book_id = 235;


//Get owner details of the books.
//First we'll get current owner ID and the title while we're there
$query = "SELECT OwnerID, Title, ISBN, CounterpartID FROM Books WHERE BookID = {$current_book_id}";
$result = mysqli_query($db,$query);
$displayrow = mysqli_fetch_assoc($result);
$current_owner_id = $displayrow['OwnerID'];
$title = $displayrow["Title"];
$current_isbn = $displayrow["ISBN"];
$current_counterpart = $displayrow["CounterpartID"];


//Then other owner ID
$query = "SELECT OwnerID, ISBN, CounterpartID, Rating FROM Books WHERE BookID = {$other_book_id}";
$result = mysqli_query($db,$query);
$displayrow = mysqli_fetch_assoc($result);
$other_owner_id = $displayrow['OwnerID'];
$other_isbn = $displayrow["ISBN"];
$other_counterpart = $displayrow["CounterpartID"];
$other_rating = $displayrow["Rating"];

//Now get both owners names
//First the current
$query = "SELECT FirstName FROM Accounts WHERE ID = {$current_owner_id}";
$result = mysqli_query($db,$query);
$displayrow = mysqli_fetch_assoc($result);
$current_owner_name = $displayrow['FirstName'];

//Then the other
$query = "SELECT FirstName FROM Accounts WHERE ID = {$other_owner_id}";
$result = mysqli_query($db,$query);
$displayrow = mysqli_fetch_assoc($result);
$other_owner_name = $displayrow['FirstName'];


//Check that the current user id matches that stored in session. 
//If they don't then redirect them to the profile page.
//  if($_SESSION["userID"] != $current_owner_id){
//      header("Location: Profile.php");
//  }

//Check to make sure they're an approved match before we proceed. to do this we'll quickly compare their ISBNs
if ($current_isbn != $other_isbn){
    header("Location: Profile.php");
}


//Lets get the party started now we have the info and have checked it's them
$pageTitle = "{$other_owner_name} - Message";
include 'Header.php';
?>
<!--A little script which changes a few key css properties for this page-->
<script type="text/javascript" >
    $("html").css("height", "100%");
    $("body").css("height", "100%");
    $(".container-fluid").css("height", "77%");

</script>

<script type="text/javascript">
    function confirm(current, other, other_cp){
        //Use ajax to update DB
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
            }
        };
        xmlhttp.open("GET", "MessageUtil.php?current=" + current + "&other=" + other, true);
        xmlhttp.send();
        
        //Then check if the other user has confirmed and display appropriate message
        if (other_cp == 0){
            //Hide the current notify and show new one.
            
            $(".notify-div").html("<span><p>Waiting on the other user to confirm...</p></span>")
        }
        else{
            $(".notify-div").html('<span><p>Please leave a rating for user based on this transaction </p><i id="star-1" onclick="rating(1,' + other + ')" class="fas fa-star"></i><i id="star-2" onclick="rating(2,'+ other +')"  class="fas fa-star"></i><i id="star-3" onclick="rating(3,'+ other +')"  class="fas fa-star"></i><i id="star-4" onclick="rating(4,'+ other +')"  class="fas fa-star"></i><i id="star-5" onclick="rating(5,'+ other +')"  class="fas fa-star"></i></span>')
        }
        
        
        
    }
    
    function rating(number, current){
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                //Do something with the returned if we want.
            }
        };
        xmlhttp.open("GET", "MessageUtil.php?book=" + current + "&number=" + number, true);
        xmlhttp.send();
        $(".fa-star").css("color", "black");
        for (i = 1; i<= number; i++){
            
            $("#star-" + i).css("color","orange");
            
        }
        
    }
</script>


<h3><?php echo $title?> - <?php echo $other_owner_name?></h3>
<!--Div that is the height of the page minus header and footer-->
<div class="full-height">

    
    <!--scrollable div to display messages in-->
    <div class="scrollable" id="scrollable">

<?php
    if ($other_counterpart === null){
        $other_counterpart_value = 0;
    }
    
    // <!--Display this if the trade is not yet confirmed by this user.-->
    if ($current_counterpart === null){
        echo <<<OUTPUT
    <div class="notify-div">
           <span><p>Is this trade going ahead?</p><button class="btn btn-success" onclick="confirm({$current_book_id}, $other_book_id, $other_counterpart_value )">Confirm</button></span>
    </div>
OUTPUT;
}
    elseif($other_counterpart === null){
        echo <<<OUTPUT
    <!--If the trade is confirmed by this user but no the other user display this-->
    <div class'="notify-div">
           <span><p>Waiting on the other user to confirm...</p></span>
    </div>
OUTPUT;
}
    else{
        echo <<<OUTPUT
    <!--If both books are confirmed display this-->
    <div class="notify-div">
           <span><p>Please leave a rating for user based on this transaction </p><i id="star-1" onclick="rating(1,$other_book_id)" class="fas fa-star"></i><i id="star-2" onclick="rating(2,$other_book_id)"  class="fas fa-star"></i><i id="star-3" onclick="rating(3,$other_book_id)"  class="fas fa-star"></i><i id="star-4" onclick="rating(4,$other_book_id)"  class="fas fa-star"></i><i id="star-5" onclick="rating(5,$other_book_id)"  class="fas fa-star"></i></span>
    </div>
OUTPUT;
}
//Get from notify where Bookid = sendid or receiveid and SendId = sendid or received id and order by datetime
$query = "SELECT * FROM Notify WHERE (BookID = {$current_book_id} OR BookID = {$other_book_id}) AND (Recipient = {$current_owner_id} OR Recipient = {$other_owner_id}) AND Notify_Type = 0 ORDER BY Publish_Date ASC";
$result = mysqli_query($db,$query);


//If there are no results display a message and skip next part
if ($result->num_rows < 1){
    echo "<p id='start_convo'>Start a conversation</p>";
}

else{
//Loop through and display on oposite sides depending on user.
while($row = $result->fetch_assoc()) {
    $message = $row["Notify_Msg"];   
    $sender = $row["Sender"];
    $time = $row["Publish_Date"];
    
    //Check who the sender is and get their name
    if ($sender == $current_owner_id){
        $sender_name = $current_owner_name;
        echo "<div class='message-r'>
            <p><span class='message-name'>{$sender_name}</span> {$time}</p>
            <p>{$message}</p>
            </div>";
    }
    else{
        $sender_name = $other_owner_name;
        echo "<div class='message-s'>
            <p><span class='message-name'>{$sender_name}</span> {$time}</p>
            <p>{$message}</p>
            </div>";
    }
    
    
    //
    
    
}
}
?>
        </div>
            <div class="message-input">
                <form action="Message.php" class="form-inline" method="post">
                    <input type="hidden" id="to" name="to" value="<?php echo $other_book_id;?>">
                    <input type="hidden" id="from" name="from" value="<?php echo $current_book_id;?>">
                    <input type="hidden" id="to_user" name="to_user" value="<?php echo $other_owner_id;?>">
                    <input type="hidden" id="from_user" name="from_user" value="<?php echo $current_owner_id;?>">
                    <div class="message-div">
                     <textarea placeholder="Send a message..." type="textarea" name="message" class="form-control mb-2 mr-sm-2" id="message"></textarea>
                     <button type="submit" class="btn btn-primary mb-2">Send</button>
                     </div>
                </form>
            </div>
        

        
    </div>


<!--//Use AJAX to submit a new message and add it to the bottom of the list.-->


<?php
include("Footer.php");

//Set the stars the current rating given.
if ($other_rating !== null){
echo <<<OUTPUT
    <script>
        for (i = 1; i<= $other_rating; i++){
            $("#star-" + i).css("color","orange");
        }
    </script>
OUTPUT;
}

?>

<script type="text/javascript">
    function updateScroll(){
        var element = document.getElementById('scrollable');
        element.scrollTop = element.scrollHeight;
    }
    updateScroll();
    
</script>
