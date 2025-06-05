<?php
require_once '../includes/session_start.php';
require_once '../includes/toast.php';
require_once '../private/config/db_connection.php';

// Buscar esportes e localizações para a navbar
$connection = open_connection();

$sports_query = "SELECT id, name FROM sport ORDER BY name";
$sports_result = mysqli_query($connection, $sports_query);

$locations_query = "SELECT id, name FROM location ORDER BY name";
$locations_result = mysqli_query($connection, $locations_query);

close_connection($connection);
?>

<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php include '../includes/bootstrap_styles.php' ?>
  <link rel="stylesheet" href="../assets/css/bootstrap_custom.css">
  <title>Tourman - Dashboard</title>
</head>

<body class="bg-light">

  <?php render_toast(); ?>
  
  <nav class="navbar navbar-dark bg-primary fixed-top">
    <div class="container-fluid">
      <a class="navbar-brand d-flex align-items-center" href="#">
        <i class="bi bi-globe2 fs-4"></i>
        <span class="ms-2 align-middle fw-bold">Tourman</span>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Offcanvas -->
      <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
        <div class="offcanvas-header bg-primary text-white">
          <h5 class="offcanvas-title" id="offcanvasNavbarLabel">
            <i class="bi bi-globe2"></i> Menu
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body">
          <div class="mb-4">
            <h6 class="fw-bold text-primary mb-3">
              <i class="bi bi-trophy"></i> Por Esporte
            </h6>
            <ul class="list-unstyled ms-3">
              <?php while ($sport = mysqli_fetch_assoc($sports_result)): ?>
                <li class="mb-2">
                  <a href="matches.php?filter=sport&id=<?= $sport['id'] ?>" class="text-decoration-none text-dark">
                    <i class="bi bi-circle-fill text-primary me-2" style="font-size: 8px;"></i>
                    <?= htmlspecialchars($sport['name']) ?>
                  </a>
                </li>
              <?php endwhile; ?>
            </ul>
          </div>

          <div class="mb-4">
            <h6 class="fw-bold text-success mb-3">
              <i class="bi bi-geo-alt"></i> Por Localização
            </h6>
            <ul class="list-unstyled ms-3">
              <?php while ($location = mysqli_fetch_assoc($locations_result)): ?>
                <li class="mb-2">
                  <a href="matches.php?filter=location&id=<?= $location['id'] ?>" class="text-decoration-none text-dark">
                    <i class="bi bi-circle-fill text-success me-2" style="font-size: 8px;"></i>
                    <?= htmlspecialchars($location['name']) ?>
                  </a>
                </li>
              <?php endwhile; ?>
            </ul>
          </div>

          <hr>

          <div class="mt-auto">
            <a href="../admin/login.php" class="btn btn-outline-primary w-100">
              <i class="bi bi-person-circle"></i> Área Administrativa
            </a>
          </div>
        </div>
      </div>
    </div>
  </nav>


  <?php require_once '../includes/bootstrap_script.php' ?>
  <script src="../assets/js/toast.js"></script>
</body>

</html>