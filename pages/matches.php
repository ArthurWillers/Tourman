<?php
require_once '../includes/toast.php';
require_once '../private/config/db_connection.php';

$filter = $_GET['filter'] ?? '';
$id = $_GET['id'] ?? '';

if (empty($id)) {
    redirect_with_toast('dashboard.php', 'ID inválido.', 'danger');
}
if ($filter !== 'sport' && $filter !== 'location') {
    redirect_with_toast('dashboard.php', 'Filtro inválido.', 'danger');
}

if ($filter === 'sport') {
    $query = "SELECT m.*, t1.name as team1_name, t2.name as team2_name, s.name as sport_name, l.name as location_name 
              FROM `match` m 
              JOIN team t1 ON m.team1_id = t1.id 
              JOIN team t2 ON m.team2_id = t2.id 
              JOIN sport s ON m.sport_id = s.id 
              JOIN location l ON m.location_id = l.id 
              WHERE m.sport_id = ? 
              ORDER BY m.sport_order";
    $title = "Partidas por Esporte";
} else {
    $query = "SELECT m.*, t1.name as team1_name, t2.name as team2_name, s.name as sport_name, l.name as location_name 
              FROM `match` m 
              JOIN team t1 ON m.team1_id = t1.id 
              JOIN team t2 ON m.team2_id = t2.id 
              JOIN sport s ON m.sport_id = s.id 
              JOIN location l ON m.location_id = l.id 
              WHERE m.location_id = ? 
              ORDER BY m.location_order";
    $title = "Partidas por Local";
}
$conn = open_connection();
$result = mysqli_execute_query($conn, $query, [$id]);

// Buscar a lista de esportes e locais para o menu
$sports_query = "SELECT id, name FROM sport ORDER BY name";
$sports_result = mysqli_query($conn, $sports_query);

$locations_query = "SELECT id, name FROM location ORDER BY name";
$locations_result = mysqli_query($conn, $locations_query);
?>

<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php include '../includes/bootstrap_styles.php' ?>
  <link rel="stylesheet" href="../assets/css/bootstrap_custom.css">
  <title>Tourman - Matches</title>
</head>

<body class="bg-light">
  
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
            <a href="../index.php" class="btn btn-outline-primary w-100">
              <i class="bi bi-house-door"></i> Voltar para a página inicial
            </a>
          </div>
        </div>
      </div>
    </div>
  </nav>

  <div class="container pt-5 mt-5">
    <h2 class="mb-4"><?= $title ?></h2>
    
    <?php if (mysqli_num_rows($result) > 0): ?>
      <div class="row">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <div class="col-12 mb-4">
            <div class="card position-relative shadow-sm">
              <?php 
              $statusClass = '';
              $statusText = '';
              switch($row['status']) {
                case 'pending':
                  $statusClass = 'bg-secondary';
                  $statusText = 'Pendente';
                  break;
                case 'in_progress':
                  $statusClass = 'bg-warning';
                  $statusText = 'Em Andamento';
                  break;
                case 'finished':
                  $statusClass = 'bg-success';
                  $statusText = 'Finalizado';
                  break;
              }
              ?>
              <span class="badge <?= $statusClass ?> position-absolute top-0 end-0 m-2"><?= $statusText ?></span>
              
              <div class="card-header bg-light text-center fw-bold">
                <?= htmlspecialchars($row['name']) ?>
              </div>
              
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div class="text-center w-40">
                    <div class="fw-bold fs-5"><?= htmlspecialchars($row['team1_name']) ?></div>
                  </div>
                  
                  <div class="text-center">
                    <span class="fs-4"><?= htmlspecialchars($row['team1_points']) ?></span>
                    <span class="fs-4 fw-bold text-secondary mx-2">X</span>
                    <span class="fs-4"><?= htmlspecialchars($row['team2_points']) ?></span>
                  </div>
                  
                  <div class="text-center w-40">
                    <div class="fw-bold fs-5"><?= htmlspecialchars($row['team2_name']) ?></div>
                  </div>
                </div>
              </div>
              
              <div class="card-footer bg-light text-center">
                <span class="text-muted">
                  <i class="bi bi-geo-alt-fill"></i> <?= htmlspecialchars($row['location_name']) ?>
                </span>
                <span class="text-muted ms-3">
                  <i class="bi bi-trophy-fill"></i> <?= htmlspecialchars($row['sport_name']) ?>
                </span>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <div class="alert alert-info">
        Nenhuma partida encontrada.
      </div>
    <?php endif; ?>
  </div>

  <?php require_once '../includes/bootstrap_script.php' ?>
  <script src="../assets/js/toast.js"></script>
</body>

</html>