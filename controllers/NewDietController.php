<?php
require '../handlers/DatabaseHandler.php';
require '../handlers/SessionHandler.php';
require '../handlers/JsonHandler.php';
require '../helpers/uuid_generator.php';
require '../models/User.php';

const INSERT_DOG_INFO = "INSERT INTO diet_generator_dog(id, name_owner, surname_owner, dog_name, dog_race, weight_kg, age, status, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
const UPDATE_DOG_INFO = "UPDATE diet_generator_dog SET name_owner = ?, surname_owner = ?, dog_name = ?, dog_race = ?, weight_kg = ?, age = ?, status = ?, num_changes = num_changes + 1 WHERE id = ?";

const INSERT_DOG_BODY_INFO = "INSERT INTO diet_generator_dog_body(height_cm, circ_chest_cm, circ_abs_cm, dog_user_think, dog_id) VALUES (?, ?, ?, ?, ?)";
const UPDATE_DOG_BODY_INFO = "UPDATE diet_generator_dog_body SET height_cm = ?, circ_chest_cm = ?, circ_abs_cm = ?, dog_user_think = ? WHERE dog_id = ?";

const INSERT_DOG_SICKS_INFO = "INSERT INTO diet_generator_sicks(sicks, dog_id) VALUES (?, ?)";
const UPDATE_DOG_SICKS_INFO = "UPDATE diet_generator_sicks SET sicks = ? WHERE dog_id = ?";

const INSERT_DOG_TAB_INFO = "INSERT INTO diet_generator_dog_tab(daily_kcal, omega_3, dog_id) VALUES (?, ?, ?)";
const UPDATE_DOG_TAB_INFO = "UPDATE diet_generator_dog_tab SET daily_kcal = ?, omega_3 = ? WHERE dog_id = ?";

$session_handler = new SessionHandlerClass();
$is_login_session_set = $session_handler->check_existing_logged_session();
if ($is_login_session_set) {
    $user = unserialize($session_handler->get_session_item(SessionHandlerClass::LOGIN_SESSION_NAME));
} else {
    header("Location: /login.php");
    exit();
}

if (!$_SERVER['REQUEST_METHOD'] === 'GET') {
    header("Location: /");
    exit();
}

$dog_info = $session_handler->get_session_item('slider_one');
$dog_body_info = $session_handler->get_session_item('slider_two');
$dog_user_think_info = $session_handler->get_session_item('slider_three');
$dog_sick_info = $session_handler->get_session_item('slider_four');

if (isset($_GET['dog_id'])) {
    $dog_id = $_GET['dog_id'];
} else {
    $dog_id = guidv4();   
}

$db_secrets = get_db_secrets('../private/db_secrets.json');
$db = new DatabaseHandlerClass(
    $db_secrets->host,
    $db_secrets->username,
    $db_secrets->password,
    $db_secrets->database
);

if (isset($_GET['dog_id'])) {
    $db->update_dog_info(
        UPDATE_DOG_INFO,
        $dog_id,
        $dog_info
    );
} else {
    $db->insert_dog_info(
        INSERT_DOG_INFO,
        $dog_id,
        $user->get_id(),
        $dog_info
    );
}

$db->insert_update_dog_body_info(
    isset($_GET['dog_id']) ? UPDATE_DOG_BODY_INFO : INSERT_DOG_BODY_INFO,
    $dog_id,
    $dog_body_info,
    $dog_user_think_info
);
$db->insert_update_dog_sick_info(
    isset($_GET['dog_id']) ? UPDATE_DOG_SICKS_INFO : INSERT_DOG_SICKS_INFO,
    $dog_id,
    $dog_sick_info
);

if ($dog_info['status'] == 'cucciolo') {
    $daily_kcal = (pow($dog_info['weight_kg'], 0.75)) * 150;
} else {
    $daily_kcal = (pow($dog_info['weight_kg'], 0.67)) * 150;
}

$omega_3 = $dog_info['weight_kg'] / 4;
$db->insert_update_dog_tab_info(
    isset($_GET['dog_id']) ? UPDATE_DOG_TAB_INFO : INSERT_DOG_TAB_INFO,
    $dog_id,
    array(
        'daily_kcal' => $daily_kcal,
        'omega_3' => $omega_3
    )
);

$db->close_connection();
$session_handler->delete_session_item('slider_one');
$session_handler->delete_session_item('slider_two');
$session_handler->delete_session_item('slider_three');
$session_handler->delete_session_item('slider_four');
$session_handler->delete_session_item('dog_user_think');

header("Location: /spinner.php?dog_id=" . $dog_id);

?>