<?php
require_once '../includes/session_start.php';
require_once '../includes/toast.php';
?>

<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php include '../includes/bootstrap_styles.php' ?>
  <link rel="stylesheet" href="../assets/css/bootstrap_custom.css">
  <title>Login - Tourman</title>
</head>

<body class="bg-light">

  <?php render_toast(); ?>

  <nav class="navbar navbar-dark bg-primary fixed-top">
    <div class="container-fluid">
      <a class="navbar-brand d-flex align-items-center" href="#">
        <i class="bi bi-globe2 fs-4"></i>
        <span class="ms-2 align-middle fw-bold">Tourman</span>
      </a>
      <a class="btn btn-outline-light" href="../index.php">Voltar</a>
    </div>
  </nav>

  <div class="container min-vh-100 d-flex align-items-center justify-content-center">
    <div class="row w-100">
      <div class="col-md-8 col-lg-6 col-xl-4 mx-auto">
        <div class="card shadow-lg p-4">
          <div class="text-center mb-3">
            <h3>Área Administrativa</h3>
            <p class="text-muted">Faça login para acessar o painel</p>
          </div>

          <form method="POST" action="../actions/login.php">
            <div class="form-floating mb-3">
              <input type="text" name="user_login" class="form-control" id="floating_user" placeholder="nome@exemplo.com" required>
              <label for="floating_user">Usuário</label>
            </div>

            <div class="form-floating mb-3 position-relative">
              <input id="password_login" type="password" name="password_login" class="form-control" placeholder="Senha" required>
              <label for="password_login">Senha</label>
              <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y" id="togglePassword" style="z-index: 5;">
                <i class="bi bi-eye text-dark fs-4" aria-hidden="true"></i>
              </button>
            </div>

            <button type="submit" name="submit_login" class="btn btn-primary w-100 mb-3">Entrar</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <?php require_once '../includes/bootstrap_script.php' ?>
  <script src="../assets/js/toast.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const togglePassword = document.querySelector('#togglePassword');
      const password = document.querySelector('#password_login');

      togglePassword.addEventListener('click', function() {

        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);

        this.querySelector('i').classList.toggle('bi-eye');
        this.querySelector('i').classList.toggle('bi-eye-slash');
      });
    });
  </script>
</body>

</html>