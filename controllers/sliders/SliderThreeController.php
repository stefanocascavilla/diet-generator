<?php
require '../../handlers/SessionHandler.php';
const SESSION_ITEM_KEY = 'slider_three';

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

$dog_user_think = (isset($_POST['dog_user_think'])) ? $_POST['dog_user_think'] : $_GET['dog_user_think'];

if ($dog_user_think) {
    $session_handler->add_session_item(
        SESSION_ITEM_KEY,
        $dog_user_think
    );
    
    if (isset($_GET['dog_id'])) {
        header("Location: /slider_4.php?dog_id=" . $_GET['dog_id']);
    } else {
        header("Location: /slider_4.php");
    }
} else {
    header("Location: /slider_3.php?error=true");
}

?>