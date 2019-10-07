<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>ShelfShare | <?php echo $pageTitle?> </title>
	 <!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

	<!-- jQuery library -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

  <!--jQuery Form plugin-->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.min.js" integrity="sha384-FzT3vTVGXqf7wRfy8k4BiyzvbNfeYjK+frTVqZeNDFl8woCbF0CYG6g2fMEFFo/i" crossorigin="anonymous"></script>

	<!-- Popper JS -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>

	<!-- Latest compiled JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script> 


	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
	
	<link rel="stylesheet" href="style.css">
	<link rel="stylesheet" href="style2.css">

	<link href="https://fonts.googleapis.com/css?family=Raleway:black,semi-bold|Roboto:light" rel="stylesheet">  

  <link rel="shortcut icon" href="imgs/logo.png">

</head>
<body>
	<header>
	
	
<?php
$hostAddress = "https://shelfshare-happyluke.c9users.io";
if (isset($_SESSION['userID'])){
  
  $userID = $_SESSION['userID'];
  
  // Check unread notifications for user
  $notifyquery = "SELECT * FROM Notify WHERE Recipient = $userID AND Status = 0";
      
  // Run query for unread messages
  $notifyresult = mysqli_query($db,$notifyquery);
      
  // count number of rows of unread notifications for user
  $notifications = mysqli_num_rows($notifyresult);

function getRating($userID, $db) {
	// query for averaging where not null
	$query = "SELECT ROUND(AVG(Rating)) FROM Books WHERE Rating IS NOT NULL AND OwnerID = $userID";
	// run query to average ratings
	$result = mysqli_query($db,$query);
	// get the average from the returned query 
	$resultrow = mysqli_fetch_assoc($result);
	$avg = $resultrow['ROUND(AVG(Rating))'];
return $avg;
}

// unit test for rating fetching function 
function getRatingTest($db) {
	$userID = 1;
	$expected = getRating($userID, $db);
	if ($expected != 1) {
		echo('The getRating function did not behave as expected, or the dummy data was not found in the database.');
	}
}
 

echo <<<OUTPUT
		<nav class="navbar navbar-expand-lg navbar-dark bg-ss">
  <a class="navbar-brand" href="Profile.php">
  	<img src="imgs/logo.png" alt="logo" style="width:40px;">
  	ShelfShare
  </a> 
  <a class="navbar-bell" href="Notifications.php">
  	<img src="imgs/bell-icon.png" class="bell-icon notification" alt="Notifications">
  	<span class="notify-num">
OUTPUT;
  	if ($notifications > 0){
  	  echo $notifications;
  	}
echo <<<OUTPUT
  	</span>
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <a class="nav-link" href="RequireBook.php">Find a Book</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="BookDetails.php">Lend a Book</a>
      </li>
		
    </ul>
    <ul class="nav navbar-nav navbar-right">
    	    	  <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#"> <i  class="fas fa-user-alt nav-user"></i></a>
    <div id="nav-dropdown" class="dropdown-menu bg-ss navbar-dark">
      <a class="dropdown-item" href="Profile.php">Profile</a>
      <a class="dropdown-item" href="AccountSettings.php">Settings</a>
      
    </div>
  </li>
    	<li><a href="Logout.php" class="nav-link">Logout</a></li>

    </ul>
  </div>
</nav>

OUTPUT;
}



else{
echo <<<OUTPUT
    <nav class="navbar navbar-expand-lg navbar-dark bg-ss">
  <a class="logo-center navbar-brand" href="#">
  	<img src="imgs/logo.png" alt="logo" style="width:40px;">
  	ShelfShare
  </a>


</nav>
    
OUTPUT;
}

echo <<<OUTPUT
	</header>
	<div class="container-fluid">
OUTPUT;
?>