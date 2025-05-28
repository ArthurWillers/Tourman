<?php
require_once './includes/session_start.php';

if (isset($_SESSION) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: ./admin/dashboard.php');
    exit;
} else {
    header('Location: ./pages/dashboard.php');
    exit;
}
