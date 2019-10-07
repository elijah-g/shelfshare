Run "mysql-ctl cli" in bash to use SQL there directly. 
Then "USE SHELFSHARE;" to select the database. 
Then you can "SELECT * FROM Accounts;" to see the current account table.
You can also "SELECT * FROM Books;" to see the current book listing table.

 Run, preview

List of globals:
$_SESSION['userID']  (holds the online user's ID, updated by login)
$_SESSION['current_user']  (holds the user's email address)
$_SESSION['edit_book']   (flags if editing or creating a book listing, holds bookID)

BookState:
0 = available
1 = required
2 = unlisted / deleted
