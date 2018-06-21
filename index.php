<!DOCTYPE html>
<html lang="en">

<head>
   
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

    <meta charset="UTF-8">
    <title>Book Lovers Society</title>
    
</head>

<body>
   
    <?php
    
    //Initializing variables
    $book_id = null;
    $reviewer_name = null;
    $reviewer_email = null;
    $book_name = null;
    $book_genre = null;
    $book_link = null;
    $book_review = null;
    $book_image = null;
    $edit_mode = false;
    
    //Set some application variables for the photo uploads
    require_once('appvars.php');
    
    if(!empty($_GET['book_id']) && is_numeric($_GET['book_id'])) {
        
        //Set a flag variable
        $edit_mode = true;
        
        //Grab the movie_id from the URL
        $book_id = $_GET['book_id'];
        
        //Connect to the db
        require('db.php');
        
        //The query that we would like to run
        $sql = "SELECT * FROM project1 WHERE book_id = :book_id";
        
        //Prepare the query to be run
        $cmd = $conn->prepare($sql);
        
        //Bind the parameters
        $cmd->bindParam(':book_id', $book_id);
        
        //Execute the query
        $cmd->execute();
        
        //Store info into an array
        $books = $cmd->fetchAll();
        
        //Loop through array using foreach and assign data to the previously set variables
        foreach ($books as $book) {
            
            $reviewer_name = $book['reviewer_name'];
            $reviewer_email = $book['reviewer_email'];
            $book_name = $book['book_name'];
            $book_genre = $book['book_genre'];
            $book_link = $book['book_link'];
            $book_review = $book['book_review'];
            $book_image = $book['book_image'];
        }
        
        //close the db connection
        $cmd->closeCursor();
        
    }
    
    ?>
   
    <div class="container">
        <h1>Submit Your Favourite Books</h1>
        <a href="books.php">View All Books</a>
        <a href="admin.php">Administration</a>
        <form enctype="multipart/form-data" method="post" action="process.php">
        <?php if(isset($error_message)) {echo '<p class="error">You have encountered the following errors:</p><p>' . $error_message . '</p>';} ?>
            <div class="form-group">
                <label for="reviewer_name">Your Name:</label>
                <input type="text" name="reviewer_name" class="form-control" value="<?php echo htmlspecialchars($reviewer_name); ?>">
            </div>
            
            <div class="form-group">
                <label for="reviewer_email">Your Email: </label>
                <input type="text" name="reviewer_email" class="form-control" value="<?php echo htmlspecialchars($reviewer_email); ?>">
            </div>

            <div class="form-group">
                <label for="book_name">Book Name:</label>
                <input type="text" name="book_name" class="form-control" value="<?php echo htmlspecialchars($book_name); ?>">
            </div>

            <div class="form-group">
                <label for="book_genre">Genre:</label>
                <input type="text" name="book_genre" class="form-control" value="<?php echo htmlspecialchars($book_genre); ?>">
            </div>
            
            <div class="form-group">
                <label for="book_link">Link to Purchase Book:</label>
                <input type="text" name="book_link" class="form-control" value="<?php echo htmlspecialchars($book_link); ?>">
            </div>
            
            <div class="form-group">
                <label for="book_review">Your Review:</label>
                <textarea name="book_review" class="form-control" rows="5"><?php echo htmlspecialchars($book_review); ?></textarea>
            </div>
            
            <div class="form-group">
                <?php if($edit_mode) {
                    echo '<label>Photo of Book:</label><br>';
                    echo '<img src="' . UPLOADPATH . $book_image . '" alt="Book image" class="med-img" />'; 
                } else {
                    echo '<label for="book_image">Photo of Book:</label><br>';
                    echo '<input type="file" name="book_image">';                    
                } ?>
            </div>
            
            <input type="hidden" name="book_id" value="<?php echo $book_id ?>" />

            <input type="submit" value="Submit">

        </form>
    </div>
</body>

</html>
