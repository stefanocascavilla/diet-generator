<?php
require '../handlers/DatabaseHandler.php';
require '../handlers/JsonHandler.php';
const NEW_USER_QUERY = "INSERT INTO vet_client_user(name, surname, email, phone) VALUES (?, ?, ?, ?)";

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    return;
}

$name = $_GET['name'];
$surname = $_GET['surname'];
$email = $_GET['email'];
$phone = $_GET['phone'];

if (
    !isset($name) ||
    !isset($surname) ||
    !isset($email) ||
    !isset($phone)
) {
    return;
}

$db_secrets = get_db_secrets('../private/db_secrets.json');
$db = new DatabaseHandlerClass(
    $db_secrets->host,
    $db_secrets->username,
    $db_secrets->password,
    $db_secrets->database
);
$db->insert_new_user(
    NEW_USER_QUERY,
    array(
        'name' => $name,
        'surname' => $surname,
        'email' => $email,
        'phone' => $phone
    )
);
$db->close_connection();

header("Location: https://gianlucabarbatoveterinario.it/grazie-per-dieta/");

?>