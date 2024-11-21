<!DOCTYPE html>
<html lang="it">

<?php
    require './handlers/SessionHandler.php';
    require './handlers/DatabaseHandler.php';
    require './handlers/JsonHandler.php';
    require './models/User.php';

    $session_handler = new SessionHandlerClass();
    $is_login_session_set = $session_handler->check_existing_logged_session();
    if ($is_login_session_set) {
        $user = unserialize($session_handler->get_session_item(SessionHandlerClass::LOGIN_SESSION_NAME));
    } else {
        header("Location: /login.php");
        exit();
    }

    if (!$session_handler->get_session_item('slider_two')) {
        header("Location: /slider_2.php");
        exit();
    }

    const USER_DOGS = "SELECT * FROM diet_generator_dog WHERE user_id = ?";
    const CHANGE_DOG = "SELECT * FROM diet_generator_dog_body WHERE dog_id = ?";
    $db_secrets = get_db_secrets('./private/db_secrets.json');
    $db = new DatabaseHandlerClass(
      $db_secrets->host,
      $db_secrets->username,
      $db_secrets->password,
      $db_secrets->database
    );

    if (isset($_GET['dog_id'])) {
      $dog_result = $db->execute_query(
        CHANGE_DOG,
        array(
            $_GET['dog_id'] => "s"
        )
      );
      $dog_result = $dog_result->fetch_assoc();

      $db->close_connection();
    } else {
      $user_dogs_result = $db->execute_query(
        USER_DOGS,
        array(
            $user->get_id() => "i"
        )
      );

      $db->close_connection();
    }

    $dog_user_think = $session_handler->get_session_item('dog_user_think');
?>

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Nuova Dieta</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

  <div class="d-flex align-items-center justify-content-between">
      <a href="index.php" class="logo d-flex align-items-center">
        <span class="d-none d-lg-block">Generazione dieta cane</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item dropdown pe-3">
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <span class="d-none d-md-block ps-2">
            <?php
                echo $user->get_name() . ' ' . $user->get_surname();
            ?>
            </span>
          </a><!-- End Profile Iamge Icon -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link collapsed" href="index.php">
          <i class="bi bi-grid"></i>
          <span>Diete</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="profilo.php">
          <i class="bi bi-person"></i>
          <span>Profilo</span>
        </a>
      </li><!-- End Profile Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="controllers/LogoutController.php">
          <i class="bi bi-door-open"></i>
          <span>Logout</span>
        </a>
      </li><!-- End Signout Page Nav -->

    </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Nuova Dieta</h1>
    </div><!-- End Page Title -->

    <?php
        if (isset($_GET['error']) && $_GET['error']) {
    ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-octagon me-1"></i>
            Valorizza tutti i campi!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php
        } else if (isset($_GET['error_measures']) && $_GET['error_measures']) {
    ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-octagon me-1"></i>
              Le dimensioni del tuo cane non sono proporzionate. Potrai continuare a formulare la dieta, ma ti consigliamo di prendere nuovamente le misure.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php
        }
    ?>

    <section class="section profile">
      <div class="row">

        <div class="col-xl-12">

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Peso Forma Cane</h5>

                    <?php
                      if (isset($dog_user_think) && $dog_user_think) {
                    ?>
                      <!-- Vertical Form -->
                      <form class="row g-3" method="post" action="./controllers/sliders/SliderThreeController.php<?php echo '?dog_user_think=' . $dog_user_think . '&'; ?><?php if (isset($_GET['dog_id'])) { echo 'dog_id=' . $dog_result['dog_id']; } ?>">
                          <div class="col-md-4">
                              <label class="form-label">Il tuo cane è risultato:</label>
                              <div class="col-sm-10">
                                  <input type="text" name="dog_user_think" class="form-control" value="<?php if ($dog_user_think == 'peso_forma') { echo 'Peso Forma'; } else { echo $dog_user_think; } ; ?>" required disabled>
                              </div>
                          </div>
                          <div class="text-center">
                              <button type="button" class="btn btn-primary" onclick="history.back()">Indietro</button>
                              <button type="submit" class="btn btn-primary">Prosegui</button>
                          </div>
                      </form><!-- Vertical Form -->
                    <?php
                      } else {
                    ?>
                      <!-- Vertical Form -->
                      <form class="row g-3" method="post" action="./controllers/sliders/SliderThreeController.php<?php if (isset($_GET['dog_id'])) { echo '?dog_id=' . $dog_result['dog_id']; } ?>">
                          <div class="col-md-4">
                              <label class="form-label">Credi che il tuo cane sia:</label>
                              <div class="col-sm-10">
                                  <div class="form-check">
                                      <input class="form-check-input" type="radio" name="dog_user_think" value="normopeso"
                                        <?php if (isset($_GET['dog_id']) && $dog_result['dog_user_think'] == 'normopeso') { echo "checked"; } ?>
                                      >
                                      <label class="form-check-label">
                                          Sottopeso
                                      </label>
                                  </div>
                              </div>
                              <div class="col-sm-10">
                                  <div class="form-check">
                                      <input class="form-check-input" type="radio" name="dog_user_think" value="peso_forma"
                                        <?php if (isset($_GET['dog_id']) && $dog_result['dog_user_think'] == 'peso_forma') { echo "checked"; } ?>
                                      >
                                      <label class="form-check-label">
                                          Peso Forma
                                      </label>
                                  </div>
                              </div>
                              <div class="col-sm-10">
                                  <div class="form-check">
                                      <input class="form-check-input" type="radio" name="dog_user_think" value="sovrappeso"
                                        <?php if (isset($_GET['dog_id']) && $dog_result['dog_user_think'] == 'sovrappeso') { echo "checked"; } ?>
                                      >
                                      <label class="form-check-label">
                                          Sovrappeso
                                      </label>
                                  </div>
                              </div>
                              <div class="col-sm-10">
                                  <div class="form-check">
                                      <input class="form-check-input" type="radio" name="dog_user_think" value="non_lo_so"
                                        <?php if (isset($_GET['dog_id']) && $dog_result['dog_user_think'] == 'non_lo_so') { echo "checked"; } ?>
                                      >
                                      <label class="form-check-label">
                                          Non lo so
                                      </label>
                                  </div>
                              </div>
                          </div>
                          <div class="text-center">
                              <button type="button" class="btn btn-primary" onclick="history.back()">Indietro</button>
                              <button type="submit" class="btn btn-primary">Prosegui</button>
                          </div>
                      </form><!-- Vertical Form -->
                  <?php
                    }
                  ?>

                </div>
            </div>

        </div>

      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>Generazione dieta cane</span></strong>. All Rights Reserved
    </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.min.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.min.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>