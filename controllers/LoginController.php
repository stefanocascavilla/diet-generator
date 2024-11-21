<?php
require '../handlers/DatabaseHandler.php';
require '../handlers/SessionHandler.php';
require '../handlers/JsonHandler.php';
require '../models/User.php';
const USER_QUERY = "SELECT id, name, surname, email, phone, password FROM diet_generator_user WHERE email = ?";

if (!$_SERVER['REQUEST_METHOD'] === 'POST') {
    header("Location: /login.php");
    exit();
}

$email = $_POST['username'];
$password = $_POST['password'];

if (!isset($email) || !isset($password)) {
    header("Location: /login.php");
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
        $email => "s"
    )
)->fetch_assoc();

if (!isset($user_result)) {
    $db->close_connection();
    header("Location: /login.php?login_error=true");
    exit();
}

$user_password = $user_result['password'];
if ($user_password == 'admin') {
    $is_correct_password = $password == $user_password;
} else {
    $is_correct_password = password_verify($password, $user_password);
}

if ($is_correct_password) {
    $user = new UserModel($user_result['id'], $user_result['name'], $user_result['surname'], $user_result['email'], $user_result['phone']);
    $session_handler = new SessionHandlerClass();
    $session_handler->add_session_item(SessionHandlerClass::LOGIN_SESSION_NAME, serialize($user));
    $db->close_connection();

    header("Location: /");
} else {
    $db->close_connection();
    
    header("Location: /login.php?login_error=true");
}

?>