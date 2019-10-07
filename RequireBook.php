<?php
session_start();
include_once "Connect.php";
$pageTitle = 'Find';

// Temporarily set this $testmode to the number 1 - 3 to activate each of the 3 tests on this page.
$testmode = 0;
// 1 tests redirecting unlogged-in users to the login page upon arrival. This test logs you out.
// 2 tests displaying dummy data from the database for editing. BookID 1 holds the dummy data.
// 3 tests the regex filter for the year. 
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
// detector for test 1 restults
if ($testmode == 1) {
    echo "test 1 failed: You were not properly redirected.";
}

include("Header.php");

// Define variables and initialize with empty values
$ISBN = $title = $authors = $bookcondition = $image = $error = "";
$year = $avaiable = $ownerID = "";
$bookID = $displayquery = $displayrow = "";
$category = "";

// test 2, to see if the following variables get displayed. Dummy data is 1, 1, 1, 1, 2000, 1, 1, 1 0, Architecture.
if ($testmode == 2) {
    $_SESSION['edit_book'] = 1;
}
// Check if the edit or create button was pressed
if ($_SESSION['edit_book'])  {
    // query to fill the variables with old book data for displaying
    // build the query to get info of book to be edited
    $bookID = $_SESSION['edit_book'];
    $displayquery = "SELECT * FROM Books WHERE BookID = $bookID";
    // run the query
    $displayresult = mysqli_query($db,$displayquery);
    // get query results with mysqli_fetch_assoc
    $displayrow = mysqli_fetch_assoc($displayresult);
        
    // take results from that and put into variables for displaying
    $ISBN = $displayrow['ISBN'];
    $title = $displayrow['Title'];
    $authors = $displayrow['Authors'];
    $year = $displayrow['Year'];
    $category = $displayrow['Category'];
    $bookcondition = $displayrow['BookCondition'];
    $image = $displayrow['Image'];
    
// detector for test 2 results
if ($testmode == 2) {
    unset($_SESSION['edit_book']);
    if ($ISBN != '1' or $title != '1' or $authors != '1' or $year != '2000' or $category != 'Architecture' or $bookcondition != '1') {
        echo "test 2 failed, the code is not working or the dummy data is no longer in the database.";
    }
    } 
}
if ($testmode == 3) {
    $_SERVER["REQUEST_METHOD"] = "POST";
}

// React only to POSTed form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // sanitize entries to make safe for database.
    $ISBN = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['ISBN'])));
    $title = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['Title'])));
    $authors = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['Authors'])));
    $year = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['Year'])));
    $category = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['Category'])));
    $bookcondition = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['BookCondition'])));
    $name = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['ImageName'])));
    $image = trim(mysqli_real_escape_string($db,htmlspecialchars($_POST['Image'])));
	
	if(!empty($_FILES['image']['tmp_name'])) {
		// IMAGE ENCODING
		$image = addslashes($_FILES['image']['tmp_name']);
		$name = addslashes($_FILES['image']['name']);
		$image = file_get_contents($image);
		$image = base64_encode($image);
	}
	
    // test 3, see if the year format validator fails to identify bad formats. Click Submit to run test.
    if ($testmode == 3) {
        $year = 'a';
    }
    // check if the year field was empty to allow it to skip past the filter
    // Check if non-numeric characters were put in the Year field
    if ((!empty($year) and  preg_match('/[^0-9]/', $year)) or (!empty($year) and  (strlen($year) != 4))) {
        $error = "You cannot enter an invalid year.";
}   
    else {
        // detector for test 3 results
        if ($testmode == 3) {
        echo "test 3 fail: The regex failed to identify a bad year input.";
    }
        // Check if any dangerous characters were used and alert user
        if ($ISBN != $_POST['ISBN'] or $title != $_POST['Title'] or $authors != $_POST['Authors'] or $year != $_POST['Year'] or $bookcondition != $_POST['BookCondition']) {
            if (empty($error)) {
                $error = "You have used illegal characters. Please try again.";
            }
        }    
        else {
            if ($_SESSION['edit_book']) {
                // Build the UPDATE query for editing existing books 
                $query = "UPDATE Books SET ISBN = '$ISBN', Title = '$title', Authors = '$authors', Year = '$year', BookCondition = '$bookcondition', Category = '$category', ImageName = '$name', Image = '$image' WHERE BookID = '$bookID'";
            }
            else {
                // grab the listing creator's ID from the global for the foreign key 
                $ownerID = $_SESSION['userID'];
                // Build the INSERT INTO query for creating books.
                $query = "INSERT INTO Books (ISBN, Title, Authors, Year, BookCondition, OwnerID, BookState, Category, ImageName, Image) VALUES ('$ISBN', '$title', '$authors', '$year', '$bookcondition', '$ownerID', '1', '$category', '$name', '$image')";
                
            }

                // check that the title and author fields are not empty
                if (empty($title) or empty($authors)) {
                    $error = "You must include the book's title and authors.";
                }
                else {
                    // Run the query to update or create the database record
                    echo $query;
                    $result = mysqli_query($db,$query);
                    
                    //Get the id of the book just inserted
                    if(!$_SESSION['edit_book']){
                        $query = "SELECT LAST_INSERT_ID();";
                        $result = mysqli_query($db,$query);
                        $displayrow = mysqli_fetch_assoc($result);
                        $bookID = $displayrow['LAST_INSERT_ID()'];
                    }
                    
                    
                    // Remove the edit global so users can create records after editing one
                    if ($_SESSION['edit_book']) {
                        unset($_SESSION['edit_book']);
                    }
                    
                    // If the query to update the database was successful, redirect 
                    if ($result) {
                        $redirect = "Location: MatchResults.php?BookID={$bookID}";
                        header($redirect);
                        $error = "Your book was created, but your redirect failed.";
                    }

                    if (empty($error)) {
                        $error = "Failed to update database. Please try again.";
                    }
                
            }
        }
    }
}  
 
 
 
?>
		<h1 class="page-header">
			Find a book
		</h1>

  <div id="results">
 

    <div class="input-group mb-3">
      <input type="text" id="searchbar" class="form-control" placeholder="Find a book...">
      <div class="input-group-append">
        <button class="btn btn-outline-secondary" id="enter" type="button">Search</button>
      </div>
    </div>


    </div>

    <script>
jQuery.fn.center = function () {
    this.css("position","relative");
    this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 4) + 
                                                $(window).scrollTop()) + "px");
    return this;
}


//$("#results").center()
$(".page-header").css("text-align","center")
$(".page-header").css("padding-left","0px")


//Stimulate button click on enter press (Adapted from w3 schools)
// Get the input field
var input = document.getElementById("searchbar");

// Execute a function when the user releases a key on the keyboard
input.addEventListener("keyup", function(event) {
  // Number 13 is the "Enter" key on the keyboard
  if (event.keyCode === 13) {
    // Cancel the default action, if needed
    event.preventDefault();
    // Trigger the button element with a click
    document.getElementById("enter").click();
  }
});


var query = ''
var URL = ''
var bookIndex = 0;
document.getElementById('enter').onclick = function(){
    //Move the search bar back to the top.
    $("#results").css("position","relative")
   
    $("#results").animate({top: "50px", width: "100%"},1000)
    //get the info for the query out of the search bar and turn it
    //into the URL to feed to the AJAX call
    query = document.getElementById('searchbar').value
    document.getElementById('searchbar').value = ''
    clearPrevious();
    loadbooks();
    // Add a button to the bottom to load more books when clicked
    var moreButton = document.createElement('button')
    moreButton.className = "btn btn-primary show-more";
    moreButton.setAttribute("onclick","loadbooks()");
    moreButton.innerHTML = "Show More";
    $(".container-fluid").append(moreButton);
} 

function loadbooks(){
    URL = 'https://www.googleapis.com/books/v1/volumes?q='+query +'&startIndex=' + bookIndex;   
    
    $.ajax({
      url: URL.toString(),
      dataType: 'json',
      success: function(data){
      console.log(data);

        for(i=0; i<10; i++){

            var booki = 'book'+(bookIndex + 1);
            
            
            //Create the div for the book
            var bookDiv = document.createElement('div')
            bookDiv.className = "content-box"
            bookDiv.setAttribute("id", booki)
            var form = document.createElement('form')
            form.setAttribute('method',"post")
            form.setAttribute('action',"")
            var title = data.items[i].volumeInfo.title
            if ("authors" in data.items[i].volumeInfo){
            var author = data.items[i].volumeInfo.authors[0]
            }
            var isbn = data.items[i].volumeInfo.industryIdentifiers[0].identifier
            var pub_date = data.items[i].volumeInfo.publishedDate
            pub_date = parseInt(pub_date)
            var image = data.items[i].volumeInfo.imageLinks.smallThumbnail
            if ("categories" in data.items[i].volumeInfo){
                var category = data.items[i].volumeInfo.categories[0]
            }
            var description = data.items[i].volumeInfo.description

            var title_hid = document.createElement("input")
            title_hid.setAttribute("type", "hidden")
            title_hid.setAttribute("value", title)
            title_hid.setAttribute("name", "Title")
            
            var author_hid = document.createElement("input")
            author_hid.setAttribute("type", "hidden")
            author_hid.setAttribute("value", author)
            author_hid.setAttribute("name", "Authors")
            
            var isbn_hid = document.createElement("input")
            isbn_hid.setAttribute("type", "hidden")
            isbn_hid.setAttribute("value", isbn)
            isbn_hid.setAttribute("name", "ISBN")
            
            var pub_date_hid = document.createElement("input")
            pub_date_hid.setAttribute("type", "hidden")
            pub_date_hid.setAttribute("value", pub_date)
            pub_date_hid.setAttribute("name", "Year")
            
            var category_hid = document.createElement("input")
            category_hid.setAttribute("type", "hidden")
            category_hid.setAttribute("value", category)
            category_hid.setAttribute("name", "Category")
            
            form.appendChild(title_hid)
            form.appendChild(author_hid)
            form.appendChild(isbn_hid)
            form.appendChild(pub_date_hid)
            
            var submit = document.createElement("input")
            submit.setAttribute("type", 'submit')
            submit.setAttribute("value", "Select")
            submit.className = "btn btn-success"
            form.appendChild(submit)
            
            
            
            var row1 = document.createElement("div")
            row1.className = "row"
            
            var img_div = document.createElement("div")
            img_div.className = "col-sm-3"
            var img_tag = document.createElement("img")
            img_tag.setAttribute("src", image)
            img_tag.setAttribute("class", "book_img")
            img_div.appendChild(img_tag)
            
            var title_div = document.createElement("div")
            title_div.className = "col-sm-9"
            var title_tag = document.createElement("h3")
            title_tag.innerHTML = title
            title_div.appendChild(title_tag)
            var description_tag = document.createElement("p")
            description_tag.innerHTML = description
            title_div.appendChild(description_tag)
            
    
            row1.appendChild(img_div)
            row1.appendChild(title_div)
            
            
            
            var row2 = document.createElement("div")
            row2.className = "row"
            
            var isbn_div = document.createElement("div")
            isbn_div.className = "col"
            var isbn_tag = document.createElement("p")
            isbn_tag.innerHTML = "ISBN: " + isbn
            isbn_div.appendChild(isbn_tag)
            
            var author_div = document.createElement("div")
            author_div.className = "col"
            var author_tag = document.createElement("p")
            author_tag.innerHTML = author
            author_div.appendChild(author_tag)
            
            var year_div = document.createElement("div")
            year_div.className = "col"
            var year_tag = document.createElement("p")
            year_tag.innerHTML = pub_date
            year_div.appendChild(year_tag)
            
            
            var submit_div = document.createElement("div")
            submit_div.className = "col"
            submit_div.appendChild(form)
            
            row2.appendChild(isbn_div)
            row2.appendChild(author_div)
            row2.appendChild(year_div)
            row2.appendChild(submit_div)
            
            bookDiv.appendChild(row1)
            bookDiv.appendChild(row2)
            
            $("#results").append(bookDiv)
            
            bookIndex++;

        }//end for loop
      }//end ajax success function
    })//end ajax call
}//end click function

function clearPrevious(){
        bookIndex = 0;
        $(".show-more").remove();
        $(".content-box").remove();

}//end clearPrevious function

function removeElementsByClass(className){
    var elements = document.getElementsByClassName(className);
    for(j=0; j<elements.length; j++){
        elements[0].parentNode.removeChild(elements[0]);
    }
}
    </script>
    
<?php
include("Footer.php")
?>