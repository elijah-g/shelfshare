<?php
    include 'Connect.php';
    $pageTitle = 'Profile';
    include 'Header.php';
    if (!isset($_SESSION['userID'])){
        header("Location: Login.php");
    }
?>
	<!-- PROFILE PAGE INFORMATION -->
   	<h1 class="page-header">
   		
   	    <?php $user = $_SESSION['current_user'];
    
        // prepare the query for checking user details //
        $sqlquery = "SELECT * FROM Accounts WHERE Email = '$user'";
                    
        // run the query to get user details //
        $result = mysqli_query($db,$sqlquery);
            
		// get row FirstName
		while($row = mysqli_fetch_array($result)):;?>
		    <?php echo $row['FirstName'];?>'s Profile
		<?php endwhile;?>
			
	</h1>
		
	<?php
		
	    $userID = $_SESSION['userID'];
		
        // prepare the query for checking book details //
        $sqlquery1 = "SELECT * FROM Books WHERE OwnerID = '$userID' AND BookState = 0";
                    
        // run the query to get book details //
        $result1 = mysqli_query($db,$sqlquery1);
            
        // count number or rows from books result
        $rows1 = mysqli_num_rows($result1);
            
        // prepare the query for checking requested book details
        $sqlquery2 = "SELECT * FROM Books WHERE OwnerID = '$userID' AND BookState = 1";
            
        // run the query to get the requested book details
        $result2 = mysqli_query($db,$sqlquery2);
            
        // count number of rows from requested book details
        $rows2 = mysqli_num_rows($result2);
            
    ?>
		
	<div class="content-box profile">
		<!-- Show the user number of listed and requested books -->
		<h3>You have <?php echo $rows1;?> books listed and...</h3>
		<h3>You are looking for <?php echo $rows2;?> books</h3>
	</div>
		
	<?php
		
		//Counter for which number book we're at
		$count = 0;
		
	    // display resuts of query for listed book details
        while($row1= mysqli_fetch_array($result1)):;?>
            
            
        <div class="content-box profile">
	        <div class="row">
		        <?php
		        // Display book image from database or use default book image
		        if (empty($row1['Image'])) {?>
		            <div class='profile-text'><img class='settings-image book-image' id="image-<?php echo $count;?>" src='imgs/book-icon.jpg' alt='Book Image' ><p> <b>Listed Book:</b> <?php echo $row1['Title'];?></p></div>
		        	<?php
		        }
	            else {?>
		            <div class='profile-text'><img class='settings-image book-image' id="image-<?php echo $count;?>" alt='Book Image' src='data:image;base64, <?php echo $row1['Image'];?>' ><p> <b>Listed Book:</b> <?php echo $row1['Title'];?></p></div>
		            <?php 
		        };?>
		        

		        <div class="edit-div">
		            <!-- Display edit image to edit book -->
		            <button data-toggle="collapse" data-target="#book<?php echo $count;?>" aria-expanded="true" class="edit-button"><i class="fas fa-edit"></i></button>
	            </div>
	            
	        </div>
	        <div class="row collapse-form collapse" id="book<?php echo $count;?>" style="">
	        	<form id="form-book<?php echo $count;?>" method="post" enctype="multipart/form-data" action="EditBook.php" class="form-inline">

            <label class="mr-sm-2" for="image">Book Image: </label>
            
            <input class="mb-2 mr-sm-2" type="file" name="Image" id="image in-<?php echo $count;?>">

            <input type="hidden" value="<?php echo $row1['BookID'];?>" name="BookID"> 
            
            <label class="mr-sm-2">Book Condition: </label>
            <input class="form-control mb-2 mr-sm-2" type="text" name="BookCondition">
            
            <input class="btn btn-primary" type="submit" style="margin:0px 0px 7px 7px;" value="Confirm">
            </form>
            		        <div class="delete-div">
		            <!-- Display bin image to delete book -->
		            <a href="DeleteBook.php?book=<?php echo $row1['BookID'];?>"><i class="fas fa-trash"></i></a>
		        </div>
            </div>
        </div>
	    <?php 
	    $count++;
	    endwhile;?>
		    
	<?php
		    
	    // display resuts of query for requested book details
        while($row2= mysqli_fetch_array($result2)):;?>
        
        <div class="content-box profile">
	        <div class="row">
		        <?php
		        // Display book image from database or use default book image
		        if (empty($row2['Image'])){ ?>
		            <div class='profile-text'><img class='settings-image book-image' src='imgs/book-icon.jpg' alt='Book Image' ><p><b>Requested Book:</b> <?php echo $row2['Title'];?></p></div>
		            <?php
		        }
		        else {?>
		            <div class='profile-text'><img class='settings-image book-image' alt='Book Image' src='data:image;base64, <?php echo $row2['Image'];?>' ><p><b>Requested Book:</b> <?php echo $row2['Title'];?></p></div>
		            <?php 
		        };?>
		        <div class="delete-div">
		            <!-- Display bin image to delete book -->
		            <a href="DeleteBook.php?book=<?php echo $row2['BookID'];?>"><i class="fas fa-trash"></i></a>
		        </div>
            </div>
	    </div>
	    <?php endwhile;?>

<?php include 'Footer.php';?>


<script>
// wait for the DOM to be loaded 
        $(document).ready(function() { 
            // bind 'myForm' and provide a simple callback function 
            $('form').ajaxForm({
            	beforeSubmit: showRequest,
            	success: displayResult,
            	resetForm: true
            }); 
        }); 
      
 function displayResult(data) {
 	$(".collapse").removeClass("show");
 }    
  
  
 //Function which reads the image input and displays it to current image.  
 function readURL(input, formNum) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function(e) {
      $('#image-' + formNum).attr('src', e.target.result);
    }

    reader.readAsDataURL(input.files[0]);
  }
}


function showRequest(formData, jqForm, options) {
    var queryString = $.param(formData);
    console.log('About to submit: \n' + queryString + '\n');
	var formId = jqForm[0].id;
	//Get the from number from the form id
	var formNum = formId.replace( /^\D+/g, '');
	
	//Get the element for image input
	var input = document.getElementById("image in-" + formNum);

	//Call the function to display the image from the above input	
	readURL(input, formNum);
	
    return true;
    
}
	
</script>

</body>

</html>