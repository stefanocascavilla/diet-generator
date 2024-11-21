<?php
require '../../handlers/SessionHandler.php';
const SESSION_ITEM_KEY = 'slider_one';

$session_handler = new SessionHandlerClass();
$is_login_session_set = $session_handler->check_existing_logged_session();
if ($is_login_session_set) {
    $user = unserialize($session_handler->get_session_item(SessionHandlerClass::LOGIN_SESSION_NAME));
} else {
    header("Location: /login.php");
    exit();
}

if (!$_SERVER['REQUEST_METHOD'] === 'POST') {
    header("Location: /");
    exit();
}

$name_owner = $_POST['name_owner'];
$surname_owner = $_POST['surname_owner'];
$dog_name = $_POST['dog_name'];
$dog_race = $_POST['dog_race'];
$weight_kg = $_POST['weight_kg'];
$age = $_POST['age'];
$status = $_POST['dog_status'];

if (
    isset($name_owner) &&
    isset($surname_owner) &&
    isset($dog_name) &&
    isset($dog_race) &&
    isset($weight_kg) &&
    isset($age) &&
    isset($status)
) {
    $session_handler->add_session_item(
        SESSION_ITEM_KEY,
        array(
            'name_owner' => $name_owner,
            'surname_owner' => $surname_owner,
            'dog_name' => $dog_name,
            'dog_race' => $dog_race,
            'weight_kg' => $weight_kg,
            'age' => $age,
            'status' => $status
        )
    );
    
    if (isset($_GET['dog_id'])) {
        header("Location: /slider_2.php?dog_id=" . $_GET['dog_id']);
    } else {
        header("Location: /slider_2.php");
    }
} else {
    header("Location: /slider_1.php?error=true");
}

?>