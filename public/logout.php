<?php
session_start();
require '../vendor/autoload.php';
use App\Includes\Auth;

Auth::logoutUser();
header('Location: login.php');
exit;