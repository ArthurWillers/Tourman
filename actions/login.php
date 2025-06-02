<?php
require_once '../includes/session_start.php';
require_once '../private/config/db_connection.php';
require_once '../includes/toast.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_login'])) {
  $user_login = trim($_POST['user_login']);
  $password_login = trim($_POST['password_login']);

  if (empty($user_login) || empty($password_login)) {
    redirect_with_toast('../admin/login.php', 'Todos os campos são obrigatórios', 'danger');
  }

  try {
    $conn = open_connection();

    $result = mysqli_execute_query($conn, "SELECT * FROM admin WHERE username = ?", [$user_login]);

    if (!$result) {
      redirect_with_toast('../admin/login.php', 'Erro ao consultar o banco de dados', 'danger');
    }

    if (mysqli_num_rows($result) === 0) {
      redirect_with_toast('../admin/login.php', 'Usuário ou senha invalidos', 'danger');
    }

    $user = mysqli_fetch_assoc($result);
    if (!password_verify($password_login, $user['password'])) {
      redirect_with_toast('../admin/login.php', 'Usuário ou senha invalidos', 'danger');
    }

    session_regenerate_id(true);
    $_SESSION['logged_in'] = true;

    close_connection($conn);
    redirect_with_toast('../admin/dashboard.php', 'Login realizado com sucesso', 'success');
  } catch (Exception $e) {
    redirect_with_toast('../admin/login.php', 'Erro ao processar o login', 'danger');
  }
} else {
  redirect_with_toast('../index.php', 'Método de requisição inválido', 'danger');
}
