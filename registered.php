<?php
session_start();

if (!isset($_SESSION['login_id'], $_SESSION['username'])) {
    header("Location: login.php");
}


include('views/registered.php');