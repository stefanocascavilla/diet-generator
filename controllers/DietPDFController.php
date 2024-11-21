<?php
require '../handlers/SessionHandler.php';
require '../handlers/DatabaseHandler.php';
require '../handlers/JsonHandler.php';
require '../handlers/MessagesHandler.php';

if (!$_SERVER['REQUEST_METHOD'] === 'GET') {
    header("Location: /");
    exit();
}
if (!isset($_GET['dog_id'])) {
    header("Location: /");
    exit();
}

$session_handler = new SessionHandlerClass();
$is_login_session_set = $session_handler->check_existing_logged_session();
if ($is_login_session_set) {
    $user = unserialize($session_handler->get_session_item(SessionHandlerClass::LOGIN_SESSION_NAME));
} else {
    header("Location: /login.php");
    exit();
}

$dog_id = $_GET['dog_id'];

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
$db_secrets = get_db_secrets('../private/db_secrets.json');
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

// Formulas
$qta_carne_1 = ($dog_tab_result['daily_kcal'] / 360) * 100;
$tuberi_1 = $qta_carne_1 / 2;
$verdure_1 = ($qta_carne_1 + $tuberi_1) / 10;
$basmati_2 = $tuberi_1 / 4;
$qta_carne_bianca_3 = ($dog_tab_result['daily_kcal'] / 200) * 100;
$olio_lino = 0.5 * $dog_tab_result['weight_kg'];
if ($dog_tab_result['status'] == 'cucciolo') {
    $calcio = 0.5 * $dog_tab_result['weight_kg'];
} else {
    $calcio = 0.1 * $dog_tab_result['weight_kg'];
}

require '../lib/fpdf/fpdf.php';
ob_start();

$pdf = new FPDF();
$pdf->AddPage();
//TItle
$pdf->SetFont('Arial','B', 16);
$pdf->Cell(45, 0, '', 0, 0, '');
$pdf->Image('../assets/img/login-logo.png');
$pdf->Cell(0, 0, '', 0, 1, '');
$pdf->Cell(80, 10, 'Dieta di ' . $dog_tab_result['dog_name'], 0, 0, '');
$pdf->Cell(100, 6, '', 0, 1, '');
$pdf->Cell(100, 6, '', 0, 1, '');


$pdf->SetFont('Arial','B', 13);
$pdf->Cell(60, 6, 'Nome Famiglia:', 0, 0, '');
$pdf->Cell(60, 6, 'Cognome Famiglia:', 0, 0, '');
$pdf->Cell(60, 6, 'Nome Cane:', 0, 1, '');

$pdf->SetFont('Arial','', 13);
$pdf->Cell(60, 6, $dog_tab_result['name_owner'], 0, 0, '');
$pdf->Cell(60, 6, $dog_tab_result['surname_owner'], 0, 0, '');
$pdf->Cell(60, 6, $dog_tab_result['dog_name'], 0, 0, '');
$pdf->Cell(100, 6, '', 0, 1, '');
$pdf->Cell(100, 6, '', 0, 1, '');

$pdf->SetFont('Arial','B', 13);
$pdf->Cell(60, 6, 'Razza:', 0, 0, '');
$pdf->Cell(60, 6, 'Peso:', 0, 0, '');
$pdf->Cell(60, 6, 'Eta:', 0, 1, '');

$pdf->SetFont('Arial','', 13);
$pdf->Cell(60, 6, $dog_tab_result['dog_race'], 0, 0, '');
$pdf->Cell(60, 6, $dog_tab_result['weight_kg'] . ' Kg', 0, 0, '');
$pdf->Cell(60, 6, $dog_tab_result['age'] . ' anni', 0, 0, '');
$pdf->Cell(100, 6, '', 0, 1, '');
$pdf->Cell(100, 6, '', 0, 1, '');
$pdf->Cell(100, 6, '', 0, 1, '');


$pdf->SetFont('Arial','B', 13);
$pdf->Cell(100, 6, 'Fabbisogno Giornaliero in Kcal:', 0, 1, '');
$pdf->SetFont('Arial','', 13);
$pdf->Cell(100, 6, number_format($dog_tab_result['daily_kcal'], 0) . ' Kcal', 0, 1, '');
$pdf->Cell(100, 6, '', 0, 1, '');

$pdf->SetFont('Arial','B', 13);
$pdf->Cell(40, 6, 'Dieta Uno', 0, 0, '');
$pdf->Cell(47, 6, 'Dieta Due', 0, 0, '');
$pdf->Cell(47, 6, 'Dieta Tre', 0, 0, '');
$pdf->Cell(47, 6, 'Dieta Quattro', 0, 1, '');

$pdf->SetFont('Arial','', 13);
$pdf->Cell(40, 6, 'Carne: ' . number_format($qta_carne_1, 1) . ' g', 0, 0, '');
$pdf->Cell(47, 6, 'Riso Basmati: ' . number_format($basmati_2, 1) . ' g', 0, 0, '');
$pdf->Cell(47, 6, 'Carne Bianca: ' . number_format($qta_carne_bianca_3, 1) . ' g', 0, 0, '');
$pdf->Cell(47, 6, 'Carne Bianca/Pesce: ' . number_format($qta_carne_bianca_3, 1) . ' g', 0, 1, '');

$pdf->Cell(40, 6, 'Tuberi: ' . number_format($tuberi_1, 1) . ' g', 0, 0, '');
$pdf->Cell(47, 6, 'Carne: ' . number_format($qta_carne_1, 1) . ' g', 0, 0, '');
$pdf->Cell(47, 6, 'Tuberi: ' . number_format($tuberi_1, 1) . ' g', 0, 0, '');
$pdf->Cell(47, 6, 'Tuberi: ' . number_format($tuberi_1, 1) . ' g', 0, 1, '');

$pdf->Cell(40, 6, 'Verdure: ' . number_format($verdure_1, 1) . ' g', 0, 0, '');
$pdf->Cell(47, 6, 'Verdure: ' . number_format($verdure_1, 1) . ' g', 0, 0, '');
$pdf->Cell(47, 6, 'Verdure: ' . number_format($verdure_1, 1) . ' g', 0, 0, '');
$pdf->Cell(47, 6, 'Verdure: ' . number_format($verdure_1, 1) . ' g', 0, 1, '');

$pdf->Cell(100, 6, '', 0, 1, '');
$pdf->Cell(100, 6, '', 0, 1, '');


$pdf->SetFont('Arial','B', 13);
$pdf->Cell(100, 6, 'Fabbisogno Giornaliero di Omega-3:', 0, 0, '');
$pdf->Cell(40, 6, 'Calcio:', 0, 0, '');
$pdf->Cell(40, 6, '', 0, 1, '');

$pdf->SetFont('Arial','', 13);
$pdf->Cell(100, 6, $dog_tab_result['omega_3'] . ' g', 0, 0, '');
$pdf->Cell(40, 6, number_format($calcio, 1) . ' g', 0, 0, '');
$pdf->Cell(40, 6, '', 0, 1, '');

$pdf->Cell(100, 6, '', 0, 1, '');
$pdf->Cell(100, 6, '', 0, 1, '');

$pdf->Cell(100, 6, iconv('UTF-8', 'windows-1252', "(è consigliabile utilizzare gli integratori di Omega 3 in perle da mezzo grammo o un grammo)"), 0, 1, '');
$pdf->Cell(100, 6, iconv('UTF-8', 'windows-1252', "(è consigliabile utilizzare il calcio sotto forma di guscio d'uovo in polvere acquistabile su Amazon"), 0, 1, '');
$pdf->Cell(100, 6, "(un cucchiaino raso e compreso tra i 6 e 8 grammi)", 0, 1, '');

// Note
$pdf->AddPage();
//TItle
$pdf->SetFont('Arial','B', 16);
$pdf->Cell(45, 0, '', 0, 0, '');
$pdf->Image('../assets/img/login-logo.png');
$pdf->Cell(100, 6, '', 0, 1, '');
$pdf->Cell(100, 6, '', 0, 1, '');

$pdf->SetFont('Arial','B', 13);
$pdf->Cell(100, 6, 'Olio di Lino', 0, 1, '');
$pdf->SetFont('Arial','', 13);
$pdf->MultiCell(0, 6, utf8_decode('Un cucchiaio da tavola e circa 16 grammi.'), 0, 1, '');
$pdf->Cell(100, 6, '', 0, 1, '');
$pdf->Cell(100, 6, '', 0, 1, '');

$pdf->SetFont('Arial','B', 13);
$pdf->Cell(100, 6, 'Verdure', 0, 1, '');
$pdf->SetFont('Arial','', 13);
$pdf->MultiCell(0, 6, utf8_decode('è possibile usare un mix di sedano, carota e mela frullati (il mix di verdure è necessario per fornire le fibre solubili che nutrono la flora intestinale buona, sarebbe meglio somministrare a crudo)'), 0, 1, '');
$pdf->Cell(100, 6, '', 0, 1, '');
$pdf->Cell(100, 6, '', 0, 1, '');

$pdf->SetFont('Arial','B', 13);
$pdf->Cell(100, 6, 'Tuberi', 0, 1, '');
$pdf->SetFont('Arial','', 13);
$pdf->MultiCell(0, 6, utf8_decode('è consigliabile utilizzare patata americana, patata a pasta rossa, patata a pasta gialla, zucca (i tuberi vanno pesati a crudo e poi cotti come per alimentazione umana)'), 0, 1, '');
$pdf->Cell(100, 6, '', 0, 1, '');
$pdf->Cell(100, 6, '', 0, 1, '');

$pdf->SetFont('Arial','B', 13);
$pdf->Cell(100, 6, 'Carne Rossa', 0, 1, '');
$pdf->SetFont('Arial','', 13);
$pdf->MultiCell(0, 6, utf8_decode('è consigliabile utilizzare carne di manzo, vitello, cavallo, maiale, asino, agnello, capretto e pecora. Scegliere i tagli mediamente grassi con una % di grasso che va dal 20 al 30% sulla massa totale (macinato per ragù). Le carni adatte alla alimentazione umana, possono essere somministrate crude o leggermente sbollentate, conservando acqua di cottura. Carni con questa % di grassi non devono essere sottoposte a cotture ostinate in quanto i grassi possono saponificare e diventare indigeribili.'), 0, 1, '');
$pdf->Cell(100, 6, '', 0, 1, '');
$pdf->Cell(100, 6, '', 0, 1, '');

$pdf->SetFont('Arial','B', 13);
$pdf->Cell(100, 6, 'Sul Riso e la Cottura', 0, 1, '');
$pdf->SetFont('Arial','', 13);
$pdf->MultiCell(0, 6, utf8_decode('è consigliabile scegliere il riso basmati in quanto risulta essere più digeribile. Il riso andrebbe sciacquato prima della cottura per eliminare i residui della lavorazione, andrebbe cotto per i tempi corretti secondo alimentazione umana e andrebbe sciacquato dopo la cottura per liberarlo dalle mucillagini che possono indurre la diarrea nel cane. Stracuocere il riso, può liberare le mucillagini e causare diarrea.'), 0, 1, '');
$pdf->Cell(100, 6, '', 0, 1, '');
$pdf->Cell(100, 6, '', 0, 1, '');

$pdf->SetFont('Arial','B', 13);
$pdf->Cell(100, 6, 'Carni Bianche', 0, 1, '');
$pdf->SetFont('Arial','', 13);
$pdf->MultiCell(0, 6, utf8_decode('è consigliabile scegliere pollo e tacchino. Non scegliere parti contenente ossa o cartilagini che possano essere pericolose per ingestione. Preferire il petto di pollo o le cosce disossate. Le carni bianche devono essere somministrate previa cottura per il rischio di salmonella.'), 0, 1, '');
$pdf->Cell(100, 6, '', 0, 1, '');
$pdf->Cell(100, 6, '', 0, 1, '');

$pdf->SetFont('Arial','B', 13);
$pdf->Cell(100, 6, 'Carne di Maiale', 0, 1, '');
$pdf->SetFont('Arial','', 13);
$pdf->MultiCell(0, 6, utf8_decode('La carne di maiale è considerata pericolosa per il cane per la possibilità di trasmissione del morbo di Aujeszky, una malattia virale trasmissibile dal maiale al cane. Per le carni di maiali provenienti da Italia questo rischio non è presente in quanto la malattia è eradicata da tempo. Controllare sempre la provenienza della carne del maiale.'), 0, 1, '');
$pdf->Cell(100, 6, '', 0, 1, '');
$pdf->Cell(100, 6, '', 0, 1, '');

$pdf->SetFont('Arial','B', 13);
$pdf->Cell(100, 6, 'Pesce', 0, 1, '');
$pdf->SetFont('Arial','', 13);
$pdf->MultiCell(0, 6, utf8_decode('Preferire pesci mediamente grassi come pesce azzurro e salmone per favorire apporto di omega3. Preferire sempre il pesce pescato al pesce di allevamento, limitare i pesci in scatola come tonno, sgombro, salmone per il rischio di intossicazione da metalli pesanti.'), 0, 1, '');
$pdf->Cell(100, 6, '', 0, 1, '');
$pdf->Cell(100, 6, '', 0, 1, '');

$pdf->SetFont('Arial','B', 13);
$pdf->Cell(100, 6, 'Latticini', 0, 1, '');
$pdf->SetFont('Arial','', 13);
$pdf->MultiCell(0, 6, utf8_decode('Molti latticini come la ricotta, il latte e il formaggio sono in grado di apportare importanti nutrienti ma per alimentazione naturale del cane vanno evitati in quanto possono favorire infiammazione generalizzata e acidosi metabolica.'), 0, 1, '');
$pdf->Cell(100, 6, '', 0, 1, '');
$pdf->Cell(100, 6, '', 0, 1, '');

$pdf->Output('D', $dog_tab_result['dog_name'] . '.pdf');

ob_end_flush();

?>