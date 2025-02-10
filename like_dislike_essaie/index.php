<?php
//index.php
include('database_connection.php');
if (!isset($_SESSION["user_id"])) {
    header("location:login.php");
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>PHP Like System with Notification using Ajax Jquery</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="jquery-3.7.1.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body>
    <br />
    <div class="container">
        <h2 align="center">PHP Like System with Notification using Ajax Jquery</h2>
        <br />
        <div align="right">
            <a href="logout.php">Logout</a>
        </div>
        <br />
        <nav class="navbar navbar-inverse">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="#">Webslesson - <?php echo $_SESSION['user_name']; ?></a>
                </div>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="label label-pill label-danger count"></span> Notification</a>
                        <ul class="dropdown-menu"></ul>
                    </li>
                </ul>
            </div>
        </nav>
        <br />
        <br />
        <form method="post" id="form_wall">
            <textarea name="content" id="content" class="form-control" placeholder="Share any thing what's in your mind"></textarea>
            <br />
            <div align="right">
                <input type="submit" name="submit" id="submit" class="btn btn-primary btn-sm" value="Post" />
            </div>
        </form>

        <br />
        <br />


        <br />
        <br />
        <h4>Latest Post</h4>
        <br />
        <div id="website_stuff"></div>
    </div>
</body>

</html>

<script>
    $(document).ready(function() {

        load_stuff();

        function load_stuff() {
            $.ajax({
                url: "load_stuff.php",
                method: "POST",
                success: function(data) {
                    $('#website_stuff').html(data);
                }
            })
        }
        $('#form_wall').on('submit', function(event) {
            event.preventDefault();
            if ($.trim($('#content').val()).length == 0) {
                alert("Please Write Something");
                return false;
            } else {
                var form_data = $(this).serialize();
                $.ajax({
                    url: "insert.php",
                    method: "POST",
                    data: form_data,
                    success: function(data) {
                        if (data == 'done') {
                            $('#form_wall')[0].reset();
                            load_stuff();
                        }
                    }
                })
            }
        });

        $(document).on('click', '.like_button', function() {
            var content_id = $(this).data('content_id');
            $(this).attr('disabled', 'disabled');
            $.ajax({
                url: "like.php",
                method: "POST",
                data: {
                    content_id: content_id
                },
                success: function(data) {
                    if (data == 'done') {
                        load_stuff();
                    }
                }
            })
        });

        load_unseen_notification();

        function load_unseen_notification(view = '') {
            $.ajax({
                url: "load_notification.php",
                method: "POST",
                data: {
                    view: view
                },
                dataType: "json",
                success: function(data) {
                    $('.dropdown-menu').html(data.notification);
                    if (data.unseen_notification > 0) {
                        $('.count').html(data.unseen_notification);
                    }
                }
            })
        }
        $(document).on('click', '.dropdown-toggle', function() {
            $('.count').html('');
            load_unseen_notification('yes');
        });
    });
</script>