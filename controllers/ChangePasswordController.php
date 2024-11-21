<?php
require '../handlers/DatabaseHandler.php';
require '../handlers/SessionHandler.php';
require '../handlers/JsonHandler.php';
require '../models/User.php';
const USER_QUERY = "SELECT password FROM diet_generator_user WHERE email = ?";
const UPDATE_PASSWORD_QUERY = "UPDATE diet_generator_user SET password = ? WHERE email = ?";

$session_handler = new SessionHandlerClass();
$is_login_session_set = $session_handler->check_existing_logged_session();
if ($is_login_session_set) {
    $user = unserialize($session_handler->get_session_item(SessionHandlerClass::LOGIN_SESSION_NAME));
} else {
    header("Location: /login.php");
    exit();
}

if (!$_SERVER['REQUEST_METHOD'] === 'POST') {
    header("Location: /profilo.php");
    exit();
}

$old_password = $_POST['old_password'];
$new_password = $_POST['new_password'];

if (!isset($old_password) || !isset($new_password)) {
    header("Location: /profilo.php");
    exit();
}

$db_secrets = get_db_secrets('../private/db_secrets.json');
$db = new DatabaseHandlerClass(
    $db_secrets->host,
    $db_secrets->username,
    $db_secrets->password,
    $db_secrets->database
);
$user_result = $db->execute_query(
    USER_QUERY,
    array(
        $user->get_email() => "s"
    )
)->fetch_assoc();

if (!isset($user_result)) {
    $db->close_connection();
    header("Location: /profilo.php?change_error=true");
    exit();
}

// Check for old password
$current_password = $user_result['password'];
if ($current_password == 'admin') {
    $is_correct_password = $old_password == $current_password;
} else {
    $is_correct_password = password_verify($old_password, $current_password);
}

if ($is_correct_password) {
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $db->update_user_password(UPDATE_PASSWORD_QUERY, $hashed_password, $user->get_email());
    $db->close_connection();

    header("Location: /profilo.php?change_ok=true");
} else {
    $db->close_connection();
    
    header("Location: /profilo.php?change_error=true");
}

?>