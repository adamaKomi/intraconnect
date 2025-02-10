<?php

session_start();

if(!isset($_SESSION['auth']))
{
    $_SESSION['from'] = $_SERVER['REQUEST_URI'];
    header("Location:pages-login.php");
}