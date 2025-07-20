<?php
require '../php/conecta.php';

session_start(); // 🔸 Necesario para acceder a $_SESSION

$volverA = (isset($_SESSION['rol']) && $_SESSION['rol'] == 1)
  ? '../students-doc/allcoursesDoc.php'
  : 'allcourses.html';



$db = conecta();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$query = "SELECT * FROM materias WHERE materia_id = $id";
$res   = mysqli_query($db, $query);
if (!$res || mysqli_num_rows($res) === 0) {
  header('Location: allcourses.html');
  exit;
}
$m = mysqli_fetch_assoc($res);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= htmlspecialchars($m['nombre']) ?> — Weeducate</title>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="../../assets/css/style.css" rel="stylesheet">
  <style>
    :root {
      --primary:        #ff8c00;
      --secondary:      #ffd180;
      --accent-light:   #fff3e0;
      --accent-lighter: #fff8e1;
    }

    /* GRADIENTE LATERAL + PATRÓN SUAVE */
    body {
      position: relative;
      background:
        linear-gradient(
          90deg,
          var(--accent-lighter) 0%,
          #ffffff               20%,
          #ffffff               80%,
          var(--accent-lighter) 100%
        ),
        url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"><circle cx="20" cy="20" r="1" fill="%23ccc" fill-opacity="0.1"/></svg>')
        repeat;
      font-family: 'Segoe UI', sans-serif;
    }
    body::before,
    body::after {
      content: "";
      position: absolute;
      width: 300px; height: 300px;
      background: radial-gradient(circle, rgba(255,255,255,0.7), transparent 70%);
      pointer-events: none;
      z-index: -1;
    }
    body::before { top: 0; left: 0; }
    body::after  { bottom: 0; right: 0; }

    /* CENTRAR MAIN */
    main.container {
      max-width: 1024px;      /* ancho máximo */
      margin: 2rem auto;      /* centrado horizontal */
      background: #fff;
      border-radius: 1rem;
      box-shadow: 0 4px 20px rgba(0,0,0,0.05);
      padding: 2rem;
    }

    /* BOTÓN VOLVER */
    #back-btn {
      position: fixed;
      top: 1rem; left: calc(50% - 512px + 1rem);
      /* si cambias max-width, ajusta este cálculo */
      z-index: 1000;
      background: #fff;
      border: none;
      border-radius: 50px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      padding: .5rem 1rem;
      display: flex; align-items: center;
      transition: transform .15s, box-shadow .15s;
    }
    #back-btn i,
    #back-btn span {
      color: var(--primary);
      transition: color .15s;
    }
    #back-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 14px rgba(0,0,0,0.15);
    }
    #back-btn:hover i,
    #back-btn:hover span {
      color: var(--secondary);
    }

    /* CABECERA DETALLE */
    .detail-header {
      position: relative;
      background: radial-gradient(circle at top left,
        var(--primary)   0%,
        var(--secondary) 60%,
        #ffa726         100%
      );
      color: #fff;
      border-radius: 1.5rem 0.5rem 1.5rem 0.5rem;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      padding: 2.5rem;
      margin-bottom: 3rem;
      clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);
      overflow: visible;
    }
    .detail-header::before {
      content: "";
      position: absolute;
      top: -40px; left: -40px;
      width: 80px; height: 80px;
      background: rgba(255,255,255,0.15);
      border-radius: 50%;
    }
    .detail-header::after {
      content: "";
      position: absolute;
      bottom: -50px; right: -50px;
      width: 100px; height: 100px;
      background: rgba(0,0,0,0.05);
      border-radius: 50%;
    }
    .detail-header img {
      width: 100%;
      border-radius: .75rem;
      object-fit: cover;
      max-height: 220px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    .detail-header h1 {
      font-size: 2rem;
      margin-bottom: .5rem;
    }
    .detail-header p {
      color: rgba(255,255,255,0.85);
      margin-bottom: .25rem;
    }

    /* BOTONES */
    .btn-orange {
      background: var(--primary) !important;
      border-color: var(--primary) !important;
      color: #fff !important;
    }
    .btn-orange:hover {
      background: var(--secondary) !important;
      border-color: var(--secondary) !important;
    }
    .btn-outline-primary {
      background: transparent;
      border: 1px solid var(--primary);
      color: var(--primary);
    }
    .btn-outline-primary:hover {
      background: var(--primary);
      color: #fff;
    }

    /* NAVEGACIÓN PESTAÑAS */
    .nav-vertical .nav-link {
      color: #495057;
      padding: .75rem 1rem;
      border-radius: .5rem;
      margin-bottom: .5rem;
      transition: background .2s, color .2s;
    }
    .nav-vertical .nav-link:hover {
      background: var(--accent-light);
      color: var(--primary);
    }
    .nav-vertical .nav-link.active {
      background: var(--primary);
      color: #fff;
      font-weight: 500;
    }

    /* CONTENIDO PESTAÑAS */
    .tab-content {
      margin-top: -2rem;
    }
    .tab-content .card {
      border: none;
      border-radius: 1rem 2rem 1rem 2rem;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      margin-bottom: 1.5rem;
    }
    .tab-content h5 {
      font-size: 1.25rem;
      margin-bottom: 1rem;
    }
  </style>
</head>
<body>

  <!-- Botón volver -->
<a href="<?= $volverA ?>" id="back-btn">
  <i class="material-icons">arrow_back</i>
  <span>Todas las materias</span>
</a>



  <main class="container py-5">

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb bg-transparent px-0">
    <li class="breadcrumb-item">
      <a href="<?= $volverA ?>">Todas las materias</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
      <?= htmlspecialchars($m['nombre']) ?>
    </li>
  </ol>
</nav>


    <!-- Cabecera -->
    <div class="detail-header row align-items-center">
      <div class="col-md-4 mb-3 mb-md-0">
        <img src="../../<?= htmlspecialchars($m['foto_url'] ?: 'assets/img/default.jpg') ?>"
             alt="<?= htmlspecialchars($m['nombre']) ?>">
      </div>
      <div class="col-md-8">
        <h1><?= htmlspecialchars($m['nombre']) ?></h1>
        <p><strong>Nivel:</strong> <?= htmlspecialchars($m['nivel_grado']) ?></p>
        <p><strong>ID:</strong> <?= $m['materia_id'] ?></p>
      </div>
    </div>

    <div class="row">
      <!-- Pestañas -->
      <nav class="col-md-3 mb-4">
        <div class="nav flex-column nav-pills nav-vertical" id="v-pills-tab" role="tablist">
          <a class="nav-link active" id="v-pills-resumen-tab" data-toggle="pill"
             href="#v-pills-resumen" role="tab">Resumen</a>
          <a class="nav-link" id="v-pills-temario-tab" data-toggle="pill"
             href="#v-pills-temario" role="tab">Temario</a>
          <a class="nav-link" id="v-pills-recursos-tab" data-toggle="pill"
             href="#v-pills-recursos" role="tab">Recursos</a>
          <a class="nav-link" id="v-pills-docente-tab" data-toggle="pill"
             href="#v-pills-docente" role="tab">Docente</a>
          <a class="nav-link" id="v-pills-opiniones-tab" data-toggle="pill"
             href="#v-pills-opiniones" role="tab">Opiniones</a>
        </div>
      </nav>

      <!-- Contenido -->
      <div class="col-md-9">
        <div class="tab-content" id="v-pills-tabContent">
          <!-- Resumen -->
          <div class="tab-pane fade show active" id="v-pills-resumen" role="tabpanel">
            <div class="card">
              <div class="card-body">
                <h5>Descripción</h5>
                <p><?= nl2br(htmlspecialchars($m['descripcion'])) ?></p>
              </div>
            </div>
          </div>
          <!-- Temario -->
          <div class="tab-pane fade" id="v-pills-temario" role="tabpanel">
            <div class="card">
              <div class="card-body">
                <h5>Temario</h5>
                <ul class="list-unstyled mb-0">
                  <li><strong>Unidad 1:</strong> Introducción
                    <ul><li>Subtema A</li><li>Subtema B</li></ul>
                  </li>
                  <li><strong>Unidad 2:</strong> Desarrollo
                    <ul><li>Subtema C</li><li>Subtema D</li></ul>
                  </li>
                  <li><strong>Unidad 3:</strong> Avanzado
                    <ul><li>Subtema E</li><li>Subtema F</li></ul>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <!-- Recursos -->
          <div class="tab-pane fade" id="v-pills-recursos" role="tabpanel">
            <div class="card">
              <div class="card-body">
                <h5>Recursos</h5>
                <ul>
                  <li><a href="#">Guía PDF descargable</a></li>
                  <li><a href="#">Vídeos explicativos</a></li>
                </ul>
              </div>
            </div>
          </div>
          <!-- Docente -->
          <div class="tab-pane fade" id="v-pills-docente" role="tabpanel">
            <div class="card d-flex flex-row align-items-center p-3">
              <img src="../../assets/img/uploads/profesor-default.png"
                   alt="Docente" class="rounded-circle me-3" width="80">
              <div>
                <h5 class="mb-1">Nombre del Docente</h5>
                <p class="mb-0"><i class="material-icons align-middle">email</i>
                  docente@weeducate.mx
                </p>
              </div>
            </div>
          </div>
          <!-- Opiniones -->
          <div class="tab-pane fade" id="v-pills-opiniones" role="tabpanel">
            <div class="card">
              <div class="card-body">
                <h5>Opiniones</h5>
                <p><strong>4.7 ★★★★★</strong> (30 reseñas)</p>
                <div class="mb-3">
                  <strong>Ana G.</strong> ★★★★★<br>
                  “Explica muy bien los conceptos, ¡recomendado!”
                </div>
                <div>
                  <strong>Carlos R.</strong> ★★★★☆<br>
                  “Muy útil, faltaron más ejercicios prácticos.”
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </main>

  <script src="../../assets/js/jquery-3.3.1.min.js"></script>
  <script src="../../assets/js/popper.min.js"></script>
  <script src="../../assets/js/bootstrap.min.js"></script>
</body>
</html>
