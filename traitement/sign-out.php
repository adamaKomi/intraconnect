<?php

session_start();

unset($_SESSION['auth']);
unset($_SESSION['admin']);
session_destroy();
header('location:../pages-login.php');