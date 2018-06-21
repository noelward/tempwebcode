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
    <title>Process Input</title>
</head>

<body>

<?php

//Set some application variables for the photo uploads
require_once('appvars.php');

//Receive info and store in variables
$reviewer_name = filter_input(INPUT_POST, 'reviewer_name');
$reviewer_email = filter_input(INPUT_POST, 'reviewer_email', FILTER_VALIDATE_EMAIL);
$book_name = filter_input(INPUT_POST, 'book_name');
$book_genre = filter_input(INPUT_POST, 'book_genre');
$book_link = filter_input(INPUT_POST, 'book_link');
$book_review = filter_input(INPUT_POST, 'book_review');

//Initialize some variables that we will call later depending on the form state (Update or New)
$book_image = null;
$book_image_type = null;
$book_image_size = null;
$book_id = NULL;
$book_id = filter_input(INPUT_POST, 'book_id');

//Assign the variables if a new record was received
if (!empty($_FILES['book_image']['name']) && !empty($_FILES['book_image']['type']) && !empty($_FILES['book_image']['size'])){
    //use $_FILES to grab the image info
    $book_image = $_FILES['book_image']['name'];
    $book_image_type = $_FILES['book_image']['type'];
    $book_image_size = $_FILES['book_image']['size'];
}

//set up a flag variable
$ok = true;
$error_message = '';
    
    //Validate the input received
if(empty($reviewer_name)){
    $error_message .= "Your name is required. ";
} if (empty($reviewer_email)){
    $error_message .= "Your email is required. ";
} if (empty($book_name)){
    $error_message .= "Book name is required. ";
} if(empty($book_genre)){
    $error_message .= "Book genre is required. ";
} if (empty($book_link)) {
    $error_message .= "The book link is required. ";
} if (empty($book_review) ) {
    $error_message .= "A review of the book is required. ";
} if (strlen($reviewer_name) >40) {
    $error_message .= "Your name must be 40 characters or less. ";
} if (strlen($book_name) >60) {
    $error_message .= "Book name must be 60 characters or less. ";
} 
    
if(empty($book_id)){
    
    if((($book_image_type == 'image/gif') || ($book_image_type == 'image/jpg') ||
($book_image_type == 'image/jpeg') || ($book_image_type == 'image/png')) &&
($book_image_size > 0) && ($book_image_size <= MAXFILESIZE)) {
} else {
        $error_message .= 'Your image must be either PNG, JPG, JPEG, or GIF and must be less than 32 KB. ';
    }
    
    if($_FILES['book_image']['error'] == 0){
    } else {
        $error_message .= 'Your photo was not uploaded. Please try again';
    }
}

//If an error message exists, go to the index page
if ($error_message !=''){
    $ok = false;
    include('index.php');
    exit();
}
    
    if($ok == true){
        
    //Connect to the db
    require('db.php');
        
    if(!empty($book_image)){
        $target = UPLOADPATH . $book_image;
        if(move_uploaded_file($_FILES['book_image']['tmp_name'], $target));
    }

    //set up sql query to UPDATE if $book_id exists or add a new record if it doesn't
    if(!empty($book_id)){
        
        $sql = "UPDATE project1 SET reviewer_name = :rname, reviewer_email = :remail, book_name = :bname, book_genre = :bgenre, book_link = :blink, book_review = :breview WHERE book_id = :book_id";
        
    } else {
        
        $sql = "INSERT INTO project1 (reviewer_name, reviewer_email, book_name, book_genre, book_link, book_review, book_image) VALUES (:rname, :remail, :bname, :bgenre, :blink, :breview, :bimage)";
        
    }

        //prepare query
        $cmd = $conn->prepare($sql);

        //bind parameters
        $cmd->bindParam(':rname', $reviewer_name, PDO::PARAM_STR, 40);
        $cmd->bindParam(':remail', $reviewer_email, PDO::PARAM_STR, 60);
        $cmd->bindParam(':bname', $book_name, PDO::PARAM_STR, 60);
        $cmd->bindParam(':bgenre', $book_genre, PDO::PARAM_STR, 30);
        $cmd->bindParam(':blink', $book_link, PDO::PARAM_STR, 100);
        $cmd->bindParam(':breview', $book_review, PDO::PARAM_STR, 300);
        
        if(!empty($book_image)){
            $cmd->bindParam(':bimage', $book_image, PDO::PARAM_STR, 100);
        }
        
    if(!empty($book_id)){
        $cmd->bindParam(':book_id', $book_id);
    }

        //execute query

        $cmd->execute();

        //close the db
        $cmd->closeCursor();
        
    }
    
    ?>

        <div class="container">
            <main>
                <h1>Thanks for submitting your favourite book!</h1>

                <label>Your Name: </label>
                <span class="result"><?php echo $reviewer_name; ?></span>
                <br>

                <label>Your Email: </label>
                <span class="result"><?php echo $reviewer_email; ?></span>
                <br>

                <label>Your Favourite Book: </label>
                <span class="result"><?php echo $book_name; ?></span>
                <br>

                <label>Book Genre: </label>
                <span class="result"><?php echo $book_genre; ?></span>
                <br>

                <label>Book Link: </label>
                <span class="result"><?php echo $book_link; ?></span>
                <br>

                <label>Your Review: </label>
                <span class="result"><?php echo $book_review; ?></span>
                <br>

                <?php
                if(!empty($book_image)){
                    echo '<label>Book Image (resized): </label><br>
                <span class="result"><img src="' . UPLOADPATH . $book_image . '" alt="Book image" class="med-img" /></span>
                <br>';
                }

                
                ?>
                    <div id="links">
                        <a href="index.php">Add a New Book</a>
                        <?php
                    
                    if (!empty($book_id)){
                        
                        echo '<a href="admin.php">Return to Administration</a>';
                            
                    } else {
                        
                        echo '<a href="books.php">View All Books</a>';
                            
                    }
                    
                    ?>

                    </div>
            </main>
        </div>


</body>

</html>
