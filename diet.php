<!DOCTYPE html>
<html lang="it">

<?php
    require './handlers/SessionHandler.php';
    require './handlers/DatabaseHandler.php';
    require './handlers/JsonHandler.php';
    require './models/User.php';
    require './handlers/MessagesHandler.php';

    $session_handler = new SessionHandlerClass();
    $is_login_session_set = $session_handler->check_existing_logged_session();
    if ($is_login_session_set) {
        $user = unserialize($session_handler->get_session_item(SessionHandlerClass::LOGIN_SESSION_NAME));
    } else {
        header("Location: /login.php");
        exit();
    }

    if (isset($_GET['dog_id'])) {
        $dog_id = $_GET['dog_id'];
    } else {
        header("Location: /");
        exit();
    }

    const DOG_TAB_QUERY = "
      SELECT
        dog.*,
        dog_tab.daily_kcal,
        dog_tab.omega_3,

        sicks.sicks
      FROM diet_generator_dog as dog
        INNER JOIN diet_generator_dog_tab as dog_tab
        ON dog.id = dog_tab.dog_id
        INNER JOIN diet_generator_sicks as sicks
        ON dog.id = sicks.dog_id
      WHERE dog.id = ?
    ";
    $db_secrets = get_db_secrets('./private/db_secrets.json');
    $db = new DatabaseHandlerClass(
      $db_secrets->host,
      $db_secrets->username,
      $db_secrets->password,
      $db_secrets->database
    );
    $dog_tab_result = $db->execute_query(
        DOG_TAB_QUERY,
        array(
            $dog_id => "s"
        )
    )->fetch_assoc();
    $db->close_connection();

    if (is_null($dog_tab_result['sicks'])) {
      $sicks_result = [];
    } else {
      $sicks_result = explode(',', $dog_tab_result['sicks']);
    }

    $sicks_result = array_diff($sicks_result, ["Nessuna Patologia Conosciuta"]);
?>

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Generazione dieta cane</title>
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
                if (isset($user)) { echo $user->get_name() . ' ' . $user->get_surname(); }
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
      <h1>Generazione dieta cane</h1>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">

        <div class="col-xl-6">

          <div class="card">
            <div class="card-body">
              <div class="tab-content pt-1">

              <?php
                if (count($sicks_result) != 0) {
              ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <i class="bi bi-exclamation-octagon me-1"></i>
                  Hai segnalato che il tuo cane soffre delle seguenti patologie: <b><?php echo join(', ', $sicks_result); ?></b>. <br>
                </div>
              <?php
                }
              ?>

                <div class="tab-pane fade show active profile-overview" id="profile-overview">

                  <h5 class="card-title">Dettagli Dieta</h5>

                  <?php
                    if (!isset($_GET['viewer'])) {
                  ?>
                    <a href="controllers/DietPDFController.php?dog_id=<?php echo $dog_id ?>" class="logo d-flex align-items-center">
                      <button type="button" class="btn btn-primary"><i class="bi bi-clipboard-plus me-1"></i> Scarica PDF</button>
                    </a>
                    <br>
                  <?php
                    }
                  ?>

                  <div class="row">
                    <div class="col-lg-6 col-md-6 label ">Fabbisogno Giornaliero in Kcal</div>
                    <div class="col-lg-6 col-md-6">
                      <?php
                          echo number_format($dog_tab_result['daily_kcal'], 0);
                      ?> Kcal
                    </div>
                  </div>

                </div>

              </div><!-- End Bordered Tabs -->

            </div>
          </div>
        </div>

        <div class="col-xl-6">
          <div class="card">
            <div class="card-body">
              <div class="tab-content pt-1">

                <div class="tab-pane fade show active profile-overview" id="profile-overview">

                  <h5 class="card-title">Dettagli Integratori</h5>

                  <div class="row">
                    <div class="col-lg-8 col-md-8 label">Fabbisogno Giornaliero di Omega-3</div>
                    <div class="col-lg-4 col-md-4">
                      <?php
                          echo number_format($dog_tab_result['omega_3'], 2);
                      ?> g
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-lg-12 col-md-12 label">
                      (è consigliabile utilizzare gli integratori di Omega 3 in perle da mezzo grammo o un grammo;
                      oppure tramite olio di lino <?php
                          $olio_lino = 0.5 * $dog_tab_result['weight_kg'];
                          echo number_format($olio_lino, 2);
                      ?> g)
                    </div>
                  </div>

                  <br>

                  <div class="row">
                    <div class="col-lg-8 col-md-8 label">Calcio</div>
                    <div class="col-lg-4 col-md-4">
                      <?php
                          if ($dog_tab_result['status'] == 'cucciolo') {
                            $calcio = 0.5 * $dog_tab_result['weight_kg'];
                          } else {
                            $calcio = 0.1 * $dog_tab_result['weight_kg'];
                          }
                          echo number_format($calcio, 2);
                      ?> g
                    </div>
                  </div>

                  <br/>

                  <div class="row">
                    <div class="col-lg-12 col-md-12 label">
                      (è consigliabile utilizzare il calcio sotto forma di guscio d'uovo in polvere acquistabile su amazon (un cucchiaino raso e compreso tra i 6 e 8 grammi)
                    </div>
                  </div>

                </div>

              </div><!-- End Bordered Tabs -->

            </div>
          </div>
        </div>

        <div class="col-xl-6">

          <div class="card">
            <div class="card-body">
              <div class="tab-content pt-1">

                <div class="tab-pane fade show active profile-overview" id="profile-overview">

                  <h5 class="card-title">Dieta Uno</h5>

                  <?php
                    $qta_carne_1 = ($dog_tab_result['daily_kcal'] / 360) * 100;
                    $tuberi_1 = $qta_carne_1 / 2;
                    $verdure_1 = ($qta_carne_1 + $tuberi_1) / 10;
                  ?>

                  <table class="table">
                    <thead>
                      <tr>
                        <th scope="col"></th>
                        <th scope="col">Carboidrati</th>
                        <th scope="col">Proteine</th>
                        <th scope="col">Grassi</th>
                        <th scope="col">Totale</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Grammi</td>
                        <td>Test</td>
                        <td>Test</td>
                        <td>Test</td>
                        <td>Test</td>
                      </tr>
                      <tr>
                        <td>Calorie</td>
                        <td>Test</td>
                        <td>Test</td>
                        <td>Test</td>
                        <td>Test</td>
                      </tr>
                      <tr>
                        <td>%</td>
                        <td>Test</td>
                        <td>Test</td>
                        <td>Test</td>
                        <td>Test</td>
                      </tr>
                    </tbody>
                  </table>

                  <table class="table table-borderless">
                    <thead>
                      <tr>
                        <th scope="col">Alimento</th>
                        <th scope="col">Quantit&agrave;</th>
                        <th scope="col">Carboidrati</th>
                        <th scope="col">Proteine</th>
                        <th scope="col">Grassi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Carne (gr)</td>
                        <td class="fw-bold">
                          <?php echo intval($qta_carne_1); ?>
                        </td>
                        <td>0%</td>
                        <td>16%</td>
                        <td>20%</td>
                      </tr>
                      <tr>
                        <td>Tuberi (gr)</td>
                        <td class="fw-bold">
                          <?php echo intval($tuberi_1); ?>
                        </td>
                        <td>17%</td>
                        <td>2%</td>
                        <td>0,1%</td>
                      </tr>
                      <tr>
                        <td>Verdure (gr)</td>
                        <td class="fw-bold">
                          <?php echo intval($verdure_1); ?>
                        </td>
                        <td>2,4%</td>
                        <td>2,3%</td>
                        <td>0,2%</td>
                      </tr>
                    </tbody>
                  </table>

                  <br/>

                  <nav>
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item">Leggi sotto per gli alimenti completi</li>
                    </ol>
                  </nav>

                </div>

              </div><!-- End Bordered Tabs -->

            </div>
          </div>

        </div>

        <div class="col-xl-6">

          <div class="card">
            <div class="card-body">
              <div class="tab-content pt-1">

                <div class="tab-pane fade show active profile-overview" id="profile-overview">

                  <h5 class="card-title">Dieta Due</h5>

                  <?php
                    $basmati_2 = $tuberi_1 / 4;
                  ?>

                  <table class="table">
                    <thead>
                      <tr>
                        <th scope="col"></th>
                        <th scope="col">Carboidrati</th>
                        <th scope="col">Proteine</th>
                        <th scope="col">Grassi</th>
                        <th scope="col">Totale</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Grammi</td>
                        <td>Test</td>
                        <td>Test</td>
                        <td>Test</td>
                        <td>Test</td>
                      </tr>
                      <tr>
                        <td>Calorie</td>
                        <td>Test</td>
                        <td>Test</td>
                        <td>Test</td>
                        <td>Test</td>
                      </tr>
                      <tr>
                        <td>%</td>
                        <td>Test</td>
                        <td>Test</td>
                        <td>Test</td>
                        <td>Test</td>
                      </tr>
                    </tbody>
                  </table>

                  <table class="table table-borderless">
                    <thead>
                      <tr>
                        <th scope="col">Alimento</th>
                        <th scope="col">Quantit&agrave;</th>
                        <th scope="col">Carboidrati</th>
                        <th scope="col">Proteine</th>
                        <th scope="col">Grassi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Riso Basmati (gr)</td>
                        <td class="fw-bold">
                          <?php echo intval($basmati_2); ?>
                        </td>
                        <td>28%</td>
                        <td>3,5%</td>
                        <td>0,4%</td>
                      </tr>
                      <tr>
                        <td>Carne (gr)</td>
                        <td class="fw-bold">
                          <?php echo intval($qta_carne_1); ?>
                        </td>
                        <td>0%</td>
                        <td>16%</td>
                        <td>20%</td>
                      </tr>
                      <tr>
                        <td>Verdure (gr)</td>
                        <td class="fw-bold">
                          <?php echo intval($verdure_1); ?>
                        </td>
                        <td>2,4%</td>
                        <td>2,3%</td>
                        <td>0,2%</td>
                      </tr>
                    </tbody>
                  </table>

                  <br/>

                  <nav>
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item">Leggi sotto per gli alimenti completi</li>
                    </ol>
                  </nav>

                </div>

              </div><!-- End Bordered Tabs -->

            </div>
          </div>

        </div>

        <div class="col-xl-6">

          <div class="card">
            <div class="card-body">
              <div class="tab-content pt-1">

                <div class="tab-pane fade show active profile-overview" id="profile-overview">

                  <h5 class="card-title">Dieta Tre</h5>

                  <?php
                    $qta_carne_bianca_3 = ($dog_tab_result['daily_kcal'] / 200) * 100;
                  ?>

                  <table class="table">
                    <thead>
                      <tr>
                        <th scope="col"></th>
                        <th scope="col">Carboidrati</th>
                        <th scope="col">Proteine</th>
                        <th scope="col">Grassi</th>
                        <th scope="col">Totale</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Grammi</td>
                        <td>Test</td>
                        <td>Test</td>
                        <td>Test</td>
                        <td>Test</td>
                      </tr>
                      <tr>
                        <td>Calorie</td>
                        <td>Test</td>
                        <td>Test</td>
                        <td>Test</td>
                        <td>Test</td>
                      </tr>
                      <tr>
                        <td>%</td>
                        <td>Test</td>
                        <td>Test</td>
                        <td>Test</td>
                        <td>Test</td>
                      </tr>
                    </tbody>
                  </table>

                  <table class="table table-borderless">
                    <thead>
                      <tr>
                        <th scope="col">Alimento</th>
                        <th scope="col">Quantit&agrave;</th>
                        <th scope="col">Carboidrati</th>
                        <th scope="col">Proteine</th>
                        <th scope="col">Grassi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Carne Bianca (gr)</td>
                        <td class="fw-bold">
                          <?php echo intval($qta_carne_bianca_3); ?>
                        </td>
                        <td>0%</td>
                        <td>22%</td>
                        <td>2,5%</td>
                      </tr>
                      <tr>
                        <td>Tuberi (gr)</td>
                        <td class="fw-bold">
                          <?php echo intval($tuberi_1); ?>
                        </td>
                        <td>17%</td>
                        <td>2%</td>
                        <td>0,1%</td>
                      </tr>
                      <tr>
                        <td>Verdure (gr)</td>
                        <td class="fw-bold">
                          <?php echo intval($verdure_1); ?>
                        </td>
                        <td>2,4%</td>
                        <td>2,3%</td>
                        <td>0,2%</td>
                      </tr>
                    </tbody>
                  </table>

                  <br/>

                  <nav>
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item">Leggi sotto per gli alimenti completi</li>
                    </ol>
                  </nav>

                </div>

              </div><!-- End Bordered Tabs -->

            </div>
          </div>

        </div>

        <div class="col-xl-6">

          <div class="card">
            <div class="card-body">
              <div class="tab-content pt-1">

                <div class="tab-pane fade show active profile-overview" id="profile-overview">

                  <h5 class="card-title">Dieta Quattro</h5>

                  <table class="table">
                    <thead>
                      <tr>
                        <th scope="col"></th>
                        <th scope="col">Carboidrati</th>
                        <th scope="col">Proteine</th>
                        <th scope="col">Grassi</th>
                        <th scope="col">Totale</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Grammi</td>
                        <td>Test</td>
                        <td>Test</td>
                        <td>Test</td>
                        <td>Test</td>
                      </tr>
                      <tr>
                        <td>Calorie</td>
                        <td>Test</td>
                        <td>Test</td>
                        <td>Test</td>
                        <td>Test</td>
                      </tr>
                      <tr>
                        <td>%</td>
                        <td>Test</td>
                        <td>Test</td>
                        <td>Test</td>
                        <td>Test</td>
                      </tr>
                    </tbody>
                  </table>

                  <table class="table table-borderless">
                    <thead>
                      <tr>
                        <th scope="col">Alimento</th>
                        <th scope="col">Quantit&agrave;</th>
                        <th scope="col">Carboidrati</th>
                        <th scope="col">Proteine</th>
                        <th scope="col">Grassi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Carne Bianca / Pesce (gr)</td>
                        <td class="fw-bold">
                          <?php echo intval($qta_carne_bianca_3); ?>
                        </td>
                        <td>0%</td>
                        <td>22%</td>
                        <td>2,5%</td>
                      </tr>
                      <tr>
                        <td>Tuberi (gr)</td>
                        <td class="fw-bold">
                          <?php echo intval($tuberi_1); ?>
                        </td>
                        <td>17%</td>
                        <td>2%</td>
                        <td>0,1%</td>
                      </tr>
                      <tr>
                        <td>Verdure (gr)</td>
                        <td class="fw-bold">
                          <?php echo intval($verdure_1); ?>
                        </td>
                        <td>2,4%</td>
                        <td>2,3%</td>
                        <td>0,2%</td>
                      </tr>
                    </tbody>
                  </table>

                  <br/>

                  <nav>
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item">Leggi sotto per gli alimenti completi</li>
                    </ol>
                  </nav>

                </div>

              </div><!-- End Bordered Tabs -->

            </div>
          </div>

        </div>

        <div class="col-xl-12">

          <div class="card">
            <div class="card-body">
              <div class="tab-content pt-1">

                <div class="tab-pane fade show active profile-overview" id="profile-overview">

                  <h5 class="card-title">Note Alimentazione</h5>

                  <div class="row">
                    <nav>
                      <ol class="breadcrumb">
                        <li class="breadcrumb-item">Olio di Lino</li>
                      </ol>
                    </nav>
                    <div class="col-lg-12 col-md-12 label">
                      Un cucchiaio da tavola &egrave; circa 16 grammi.
                    </div>
                  </div>

                  <br/>

                  <div class="row">
                    <nav>
                      <ol class="breadcrumb">
                        <li class="breadcrumb-item">Verdure</li>
                      </ol>
                    </nav>
                    <div class="col-lg-12 col-md-12 label">
                      &Egrave; possibile usare un mix frullato di sedano, carota e mela (il mix di verdure è necessario per fornire le fibre solubili che nutrono la flora intestinale buona, sarebbe meglio somministrare a crudo)
                    </div>
                  </div>

                  <br/>

                  <div class="row">
                    <nav>
                      <ol class="breadcrumb">
                        <li class="breadcrumb-item">Tuberi</li>
                      </ol>
                    </nav>
                    <div class="col-lg-12 col-md-12 label">
                      &Egrave; consigliabile utilizzare patata americana, patata a pasta rossa, patata a pasta gialla, zucca (i tuberi vanno pesati a crudo e poi cotti come per alimentazione umana)
                    </div>
                  </div>

                  <br/>

                  <div class="row">
                    <nav>
                      <ol class="breadcrumb">
                        <li class="breadcrumb-item">Carne Rossa</li>
                      </ol>
                    </nav>
                    <div class="col-lg-12 col-md-12 label">
                      &Egrave; consigliabile utilizzare carne di manzo, vitello, cavallo, maiale, asino, agnello, capretto e pecora. Scegliere i tagli mediamente grassi con una percentuale di grasso che va dal 20% al 30% sulla massa totale (macinato per ragù). Le carni adatte all'alimentazione umana, possono essere somministrate crude o leggermente sbollentate, conservando l'acqua di cottura. Carni con questa percentuale di grassi non devono essere sottoposte a cotture ostinate in quanto i grassi possono saponificare e diventare indigeribili.
                    </div>
                  </div>

                  <br/>

                  <div class="row">
                    <nav>
                      <ol class="breadcrumb">
                        <li class="breadcrumb-item">Sul Riso e la Cottura</li>
                      </ol>
                    </nav>
                    <div class="col-lg-12 col-md-12 label">
                      &Egrave; consigliabile scegliere il riso basmati in quanto risulta essere più digeribile. Il riso andrebbe sciacquato prima della cottura per eliminare i residui della lavorazione, andrebbe cotto per i tempi corretti secondo l'alimentazione umana e andrebbe sciacquato dopo la cottura per liberarlo dalle mucillagini che possono indurre la diarrea nel cane. Stracuocere il riso, può liberare le mucillagini e causare diarrea.
                    </div>
                  </div>

                  <br/>

                  <div class="row">
                    <nav>
                      <ol class="breadcrumb">
                        <li class="breadcrumb-item">Carni Bianche</li>
                      </ol>
                    </nav>
                    <div class="col-lg-12 col-md-12 label">
                      &Egrave; consigliabile scegliere pollo e tacchino. Non scegliere parti contenente ossa o cartilagini che possano essere pericolose per ingestione. Preferire il petto di pollo o le cosce disossate. Le carni bianche devono essere somministrate previa cottura per il rischio di salmonella.
                    </div>
                  </div>

                  <br/>

                  <div class="row">
                    <nav>
                      <ol class="breadcrumb">
                        <li class="breadcrumb-item">Carne di Maiale</li>
                      </ol>
                    </nav>
                    <div class="col-lg-12 col-md-12 label">
                      La carne di maiale è considerata pericolosa per il cane per la possibilità di trasmissione del morbo di Aujeszky, una malattia virale trasmissibile dal maiale al cane. Per le carni di maiali provenienti dall'Italia questo rischio non è presente in quanto la malattia è eradicata da tempo. Controllare sempre la provenienza della carne di maiale.
                    </div>
                  </div>

                  <br/>

                  <div class="row">
                    <nav>
                      <ol class="breadcrumb">
                        <li class="breadcrumb-item">Pesce</li>
                      </ol>
                    </nav>
                    <div class="col-lg-12 col-md-12 label">
                      Preferire pesci mediamente grassi come pesce azzurro e salmone per favorire l'apporto di omega3. Preferire sempre il pesce pescato al pesce d'allevamento, limitare i pesci in scatola come tonno, sgombro, salmone per il rischio di intossicazione da metalli pesanti.
                    </div>
                  </div>

                  <br/>

                  <div class="row">
                    <nav>
                      <ol class="breadcrumb">
                        <li class="breadcrumb-item">Latticini</li>
                      </ol>
                    </nav>
                    <div class="col-lg-12 col-md-12 label">
                      Molti latticini come la ricotta, il latte e il formaggio sono in grado di apportare importanti nutrienti ma per l'alimentazione naturale del cane vanno evitati in quanto possono favorire l'infiammazione generalizzata e l'acidosi metabolica oltre a non apportare tutti gli amminoacidi essenziali.
                    </div>
                  </div>

                </div>

              </div><!-- End Bordered Tabs -->

            </div>
          </div>

        </div>

      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>Alimentazione Naturale del Cane</span></strong>. All Rights Reserved
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