<?php
require '../../handlers/SessionHandler.php';
const SESSION_ITEM_KEY = 'slider_four';

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

$dog_sicks = null;

if (isset($_POST['dog_sicks'])) {
    $dog_sicks = join(',', $_POST['dog_sicks']);
}

$session_handler->add_session_item(
    SESSION_ITEM_KEY,
    array(
        'dog_sicks' => $dog_sicks
    )
);

if (isset($_GET['dog_id'])) {
    header("Location: /controllers/NewDietController.php?dog_id=" . $_GET['dog_id']);
} else {
    header("Location: /controllers/NewDietController.php");
}

?>