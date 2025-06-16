<?php
require_once '../includes/toast.php';
require_once '../private/config/db_connection.php';

$conn = open_connection();

$sports_query = "SELECT id, name FROM sport ORDER BY name";
$sports_result = mysqli_query($conn, $sports_query);

$locations_query = "SELECT id, name FROM location ORDER BY name";
$locations_result = mysqli_query($conn, $locations_query);

close_connection($conn);
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

  <!-- Conteúdo Principal -->
  <div class="container pt-5 mt-5">
    <div class="row">
      <div class="col-12">
        <h2 class="text-center mb-4">
          <i class="bi bi-trophy"></i> Painel de Partidas
        </h2>
        
        <!-- Botão para carregar partidas e contador -->
        <div class="text-center mb-4">
          <button id="loadMatches" class="btn btn-primary">
            <i class="bi bi-refresh"></i> Carregar Partidas
          </button>
          <div class="mt-2">
            <small class="text-muted">
              Próxima atualização em: <span id="countdown">10</span>s
            </small>
          </div>
        </div>

        <!-- Loading spinner -->
        <div id="loading" class="text-center d-none">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Carregando...</span>
          </div>
        </div>

        <!-- Container das partidas -->
        <div id="matchesContainer" class="row">
          <!-- As partidas serão carregadas aqui via AJAX -->
        </div>

        <!-- Mensagem quando não há partidas -->
        <div id="noMatches" class="alert alert-info text-center d-none">
          <i class="bi bi-info-circle"></i> Nenhuma partida em andamento no momento.
        </div>
      </div>
    </div>
  </div>

  <?php require_once '../includes/bootstrap_script.php' ?>
  <script src="../assets/js/toast.js"></script>
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const loadMatchesBtn = document.getElementById('loadMatches');
      const loading = document.getElementById('loading');
      const matchesContainer = document.getElementById('matchesContainer');
      const noMatches = document.getElementById('noMatches');
      const countdown = document.getElementById('countdown');
      
      let autoUpdateInterval;
      let countdownInterval;
      let countdownTime = 10;

      loadMatchesBtn.addEventListener('click', function() {
        loadMatches();
        resetAutoUpdate();
      });

      function loadMatches() {
        // Mostrar loading
        loading.classList.remove('d-none');
        matchesContainer.innerHTML = '';
        noMatches.classList.add('d-none');
        loadMatchesBtn.disabled = true;

        // Fazer requisição AJAX
        fetch('../api/get_matches.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          }
        })
        .then(response => response.json())
        .then(data => {
          loading.classList.add('d-none');
          loadMatchesBtn.disabled = false;

          if (data.error) {
            console.error('Erro API:', data.error);
            noMatches.classList.remove('d-none');
            return;
          }

          if (data.length === 0) {
            noMatches.classList.remove('d-none');
            return;
          }

          // Renderizar partidas
          renderMatches(data);
        })
        .catch(error => {
          console.error('Erro:', error);
          loading.classList.add('d-none');
          loadMatchesBtn.disabled = false;
          noMatches.classList.remove('d-none');
        });
      }

      function renderMatches(matches) {
        matchesContainer.innerHTML = '';

        matches.forEach(match => {
          const statusClass = getStatusClass(match.status);
          const statusText = getStatusText(match.status);

          const matchCard = `
            <div class="col-12 mb-4">
              <div class="card position-relative shadow-sm">
                <span class="badge ${statusClass} position-absolute top-0 end-0 m-2">${statusText}</span>
                
                <div class="card-header bg-light text-center fw-bold">
                  ${match.name || 'Partida ' + match.id}
                </div>
                
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="text-center" style="width: 40%">
                      <div class="fw-bold fs-5">${match.team1_name}</div>
                    </div>
                    
                    <div class="text-center">
                      <span class="fs-4">${match.team1_points || 0}</span>
                      <span class="fs-4 fw-bold text-secondary mx-2">X</span>
                      <span class="fs-4">${match.team2_points || 0}</span>
                    </div>
                    
                    <div class="text-center" style="width: 40%">
                      <div class="fw-bold fs-5">${match.team2_name}</div>
                    </div>
                  </div>
                </div>
                
                <div class="card-footer bg-light text-center">
                  <span class="text-muted">
                    <i class="bi bi-geo-alt-fill"></i> ${match.location_name}
                  </span>
                  <span class="text-muted ms-3">
                    <i class="bi bi-trophy-fill"></i> ${match.sport_name}
                  </span>
                </div>
              </div>
            </div>
          `;

          matchesContainer.innerHTML += matchCard;
        });
      }

      function getStatusClass(status) {
        switch(status) {
          case 'pending': return 'bg-secondary';
          case 'in_progress': return 'bg-warning';
          case 'finished': return 'bg-success';
          default: return 'bg-secondary';
        }
      }

      function getStatusText(status) {
        switch(status) {
          case 'pending': return 'Pendente';
          case 'in_progress': return 'Em Andamento';
          case 'finished': return 'Finalizado';
          default: return 'Desconhecido';
        }
      }

      function startAutoUpdate() {
        autoUpdateInterval = setInterval(() => {
          loadMatches();
          resetCountdown();
        }, 10000); // 10 segundos
      }

      function startCountdown() {
        countdownInterval = setInterval(() => {
          countdownTime--;
          countdown.textContent = countdownTime;
          
          if (countdownTime <= 0) {
            resetCountdown();
          }
        }, 1000);
      }

      function resetCountdown() {
        countdownTime = 10;
        countdown.textContent = countdownTime;
        clearInterval(countdownInterval);
        startCountdown();
      }

      function resetAutoUpdate() {
        clearInterval(autoUpdateInterval);
        clearInterval(countdownInterval);
        startAutoUpdate();
        resetCountdown();
      }

      // Inicializar
      loadMatches();
      startAutoUpdate();
      startCountdown();
    });
  </script>
</body>

</html>