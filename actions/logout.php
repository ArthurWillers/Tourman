<?php 
require_once '../includes/session_start.php';
session_destroy();
header("Location: ../index.php");
exit();