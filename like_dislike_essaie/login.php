<?php
//login.php

include("database_connection.php");

$message = '';

if (isset($_POST["login"])) {
    if (empty($_POST["user_email"]) || empty($_POST["user_password"])) {
        $message = '<label>Both Fields are required</label>';
    } else {

        $_SESSION['user_id'] = 1;
        $_SESSION['user_name'] = 'John Smith';
        header("location:index.php");


        //         $query = "
        //   SELECT * FROM user_details 
        //   WHERE user_email = :user_email
        //   ";
        //         $statement = $connect->prepare($query);
        //         $statement->execute(
        //             array(
        //                 'user_email' => $_POST["user_email"]
        //             )
        //         );
        //         $count = $statement->rowCount();
        //         if ($count > 0) {
        //             $result = $statement->fetchAll();
        //             foreach ($result as $row) {
                        // if (password_verify($_POST["user_password"], $row["user_password"])) {
        //                     $_SESSION['user_id'] = $row['user_id'];
        //                     $_SESSION['user_name'] = $row['user_name'];
        //                     header("location:index.php");
                        // } else {
                        //     $message = '<label>Wrong Password</label>';
                        // }
        //             }
        //         } else {
        //             $message = '<label>Wrong Email Address</labe>';
        //         }
    }
}



?>

<!DOCTYPE html>
<html>

<head>
    <title>PHP Like System with Notification using Ajax Jquery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- <script src="jquery-3.7.1.min.js"></script> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script> -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body>
    <br />
    <div class="container">
        <h2 align="center">PHP Like System with Notification using Ajax Jquery</h2>
        <br />
        <div class="panel panel-default">
            <div class="panel-heading">Login</div>
            <div class="panel-body">
                <form method="post">
                    <span class="text-danger"><?php echo $message; ?></span>
                    <div class="form-group">
                        <label>User Email</label>
                        <input type="text" name="user_email" id="user_email" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="user_password" id="user_password" class="form-control" />
                    </div>
                    <div class="form-group">
                        <input type="submit" name="login" id="login" class="btn btn-info" value="Login" />
                    </div>
                </form>

            </div>
        </div>
        <br />
        <p><strong>Password</strong> - password</p>
    </div>
</body>

</html>