<?php
    include 'Connect.php';
    $pageTitle = 'Settings';
    include 'Header.php';
    if (!isset($_SESSION['userID'])){
    header("Location: Login.php");
    }
    
    // Get current user
    $user = $_SESSION['current_user'];
    
    // Database query to get Account details for the user
    $query = "SELECT * FROM Accounts WHERE Email = '$user'";
    $result = mysqli_query($db,$query);
    $row = mysqli_fetch_assoc($result);
	$rating = getRating($row['ID'],$db);
    $firstname = $row["FirstName"];
    $lastname = $row["LastName"];
    $image = $row['Image'];
    $name = $row['ImageName'];
    $email = $row['Email'];
    $gender = $row['Gender'];
    $address = $row['Location'];
    $school = $row['School'];
    $state = $row['State'];
    $postcode = $row['PCode'];
    
    if (isset($_POST['submit'])) {
		// GET DATA FROM FORM
		$email = htmlspecialchars($_POST['email']);
		if(isset($_POST['gender'])){$gender = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['gender'])));}
		$address = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['address'])));
		$state = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['state'])));
		$school = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['school'])));
		$name = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['name'])));
		$image = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['image']['tmp_name'])));
		$postcode = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['postcode'])));
		
		if(!empty($_FILES['image']['tmp_name']) && file_exists($_FILES['image']['tmp_name'])) {
		// IMAGE ENCODING
		$image = addslashes($_FILES['image']['tmp_name']);
		$name = addslashes($_FILES['image']['name']);
		$image = file_get_contents($image);
		$image = base64_encode($image);
		}
		
		if(empty($_FILES['image']['tmp_name'])) {
			$image = $row['Image'];
			$name = $row['ImageName'];
		}
		
		// Update database with user details
	    $sqlquery = "UPDATE Accounts SET Gender = '$gender', Location = '$address', State = '$state', PCode = '$postcode', School = '$school', ImageName = '$name', Image = '$image' WHERE Email = '$user'";
	        
	    // Run query to update database
	    $result = mysqli_query($db,$sqlquery);
	        
	    // Inform user that the account details were saved
	    if ($result) {
			echo "<script>alert('All changes to your account with Shelfshare have been saved!');</script>";
	    }
	    else {
	       	echo "<script>alert('Changes to your account were unable to be saved. Please check for errors and try again.');</script>";
	    }
	}
	
	// If the Delete Account button has been selected
	if (isset($_POST['delete'])) {
		$userID = $_SESSION['userID'];
		
		// query to delete notifications 
		$sqlquery = "DELETE FROM Notify WHERE Recipient = '$userID' or  Sender =  '$userID'";
		
		// Run the query to delete notifications
		$result = mysqli_query($db,$sqlquery);
			
		// Delete books from database
		$sqlquery = "DELETE FROM Books WHERE OwnerID = '$userID'";
			    
	    // Run query to update database
		$result = mysqli_query($db,$sqlquery);
		    
		// Delete account from database
		$sqlquery1 = "DELETE FROM Accounts WHERE ID = '$userID'";
			    
	    // Run query to update database
		$result1 = mysqli_query($db,$sqlquery1);
	    
		// Redirect user and inform the user that the account has been deleted
		if ($result1) {
			header('Refresh:3; url=Logout.php');
			echo "<script>alert('Your account has been deleted with Shelfshare!');</script>";
		}
		else {
		   	echo "<script>alert('Your account was unable to be deleted. Please try again.');</script>";
		}
	}
?>
	
   		<h1 class="page-header">
		<!--	Name  -->
		</h1>

		<div class="content-box settings">
		<div class="row">

			<div class="col-sm-6">
			
            <h3 class="name_head">FirstName: <?php echo $firstname;?></h3>
			<h3 class="name_head">LastName: <?php echo $lastname;?></h3>
			
			<!--- display user rating stars --->
			<div id="star-div">
				<i  <?php if ($rating >= 1){echo "style='color:orange'";} else {echo "style='color:black'";}?> class="fas fa-star"></i>
				<i  <?php if ($rating >= 2){echo "style='color:orange'";} else {echo "style='color:black'";}?> class="fas fa-star"></i>
				<i  <?php if ($rating >= 3){echo "style='color:orange'";} else {echo "style='color:black'";}?> class="fas fa-star"></i>
				<i  <?php if ($rating >= 4){echo "style='color:orange'";} else {echo "style='color:black'";}?> class="fas fa-star"></i>
				<i  <?php if ($rating == 5){echo "style='color:orange'";} else {echo "style='color:black'";}?> class="fas fa-star"></i>
			</div>
		</div> 

		<div class="col-sm-6 profile-img">
			
			<?php
			if (empty($name)) { 
				echo "<img id='profile-image' class='settings-image' src='imgs/profile-holder.jpg' alt='Profile Image'>";
			}
			else {?>
				<img id='profile-image' class='settings-image' alt='Profile Image' src='data:image;base64, <?php echo $image;?>' height="250" width="200">
			<?php }?>
</div>


		</div>
		<form action="" method="post" enctype="multipart/form-data">
	  <div class="form-group">
	    <label for="email">Email:</label>
	    <h6 class="form-control" id="email" name="email"><?php echo $user;?></h6>
	  
	  </div>
		<div class="form-group">
	    <label for="image">Image: </label>
	    <br/>
		<input type="file" name="image" id="image">
		</div>
		<div class="form-group">
			<label for="gender">Gender: </label>
			<br />
			<input id="gender" class="radio" name="gender" type="radio" value="Male" <?php if ($gender=="Male") echo "checked";?> />Male<br />
           	<input class="radio" name="gender" type="radio" value="Female" <?php if ($gender=="Female") echo "checked";?> />Female<br />
           	<input class="radio" name="gender" type="radio" value="Other" <?php if ($gender=="Other") echo "checked";?> />Other<br />
		</div>
		<div class="form-group">
			<label>Address: </label>
			<br/>
			<input type="text" id="address" name="address" cols="20" rows="5" value="<?php echo $address;?>">
		</div>

<?php
$statetester = 0;
$selection = '';
// this section displays the dropdown for the states in the database.
$states = '';
$statequery = "(SELECT DISTINCT State FROM Schools)";
$stateresult = mysqli_query($db,$statequery); 
// fill rows of state dropdown
while($staterow = mysqli_fetch_assoc($stateresult)) {
	$statetester++;
	// if saved state data exists, use it. 
	if (isset($state) && $state == $staterow['State']) {
    	$states .="<option selected>" . $staterow['State'] . "</option>";
	}
	else {
		$states .="<option>" . $staterow['State'] . "</option>";
	}
}

// write state dropdown html
$statedropdown = "<div class='form-group'>
	<label>State and school <span class='error'></span>: </label><br/>
	<select name='state' id='states' class='form-control' type='select'>
    <option value=''>  </option>
    " . $states . "
    </select>";

echo $statedropdown;
// automated unit test for state dropdown
if ($statetester < 1) {
	echo "<script>alert('Unit Test error: the states could not be found in the database, or the code did not behave as expected.');</script>";	
}

// this section displays the dropdown for schools in the database
$schooltester = 0;
$schools = '';
$schoolquery="(SELECT * FROM Schools)";
$schoolresult = mysqli_query($db,$schoolquery); 
// fill rows of the school dropdown
while($schoolrow = mysqli_fetch_assoc($schoolresult)) {
	$schooltester++;
	// take spaces out of states to use as class names
	$stateclass = preg_replace('/\s+/', '', $schoolrow['State']);
	if (isset($school) && $school == $schoolrow['School']) {
    $schools .="<option selected class='allstates  . $stateclass . '>" . $schoolrow['School'] . "</option>";
	}
	else {
	$schools .="<option class='allstates  . $stateclass . '>" . $schoolrow['School'] . "</option>";
	}
}

// write school dropdown html if school is already selected in their profile
if (isset($school) && $school != ''){
$schooldropdown ="<select name='school' id='schools' class='form-control' type='select'>
    <option value=''>  </option>
    " . $schools . "
    </select>
    </div>";
}
else {
// write school dropdown html if no school is selected in their profile    
$schooldropdown ="<select name='school' id='schools' class='form-control' style='display: none;' type='select'>
    <option value=''>  </option>
    " . $schools . "
    </select>
    </div>";
}
echo $schooldropdown;
// automated unit test for the school dropdown
if ($schooltester < 1) {
	echo "<script>alert('Unit Test error: the schools could not be found in the database, or the code did not behave as expected.');</script>";	
}

?>

		<div class="form-group">
			<label>Postcode <span class="error"></span>: </label>
			<br/>
			<input type="number" name="postcode" value="<?php echo $postcode;?>" />
		</div>
		
		<div class="settings-buttons">
			<input id="delete" name="delete" class="btn btn-danger" type="submit" value="Delete Account" onclick="return confirm('Are you sure you want to delete your account? This cannot be undone.');"/>
	    	<input id="submit" name="submit" class="btn btn-primary" type="submit" value="Save Changes" />
    	</div>
	</form>
</div>

<?php include 'Footer.php';?>

<script>
// This jquery removes spaces for using states as class, shows the chosen state school list, and hides previously chosen ones.
$("#states").change ( function () {
    var chosenstate  = $(this).val ();
    chosenstateID = chosenstate.replace(/\s/g,''); 
    // hide and show the school dropdown
    $('#' + 'schools').show ();
    // Hide and show schools depending on if they're in the chosen state
    $(".allstates").hide ();
    $('.' + chosenstateID).show ();
    // deselect the chosen school when changing state
    $(".allstates").prop ("selected", false);
})


</script>