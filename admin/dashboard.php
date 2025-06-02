<?php 
require_once '../includes/session_start.php';
require_once '../private/config/db_connection.php';
require_once '../includes/toast.php';

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    redirect_with_toast('../index.php', 'Você precisa estar logado para acessar o painel', 'danger');
}

?>