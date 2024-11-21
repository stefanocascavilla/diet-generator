<?php
require '../../handlers/SessionHandler.php';
const SESSION_ITEM_KEY = 'slider_two';

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

$is_body_measures = $_POST['height_cm'];

if (isset($is_body_measures)) {
    $height_cm = $_POST['height_cm'];
    $circ_chest_cm = $_POST['circ_chest_cm'];
    $circ_abs_cm = $_POST['circ_abs_cm'];
    
    if (
        isset($height_cm) &&
        isset($circ_chest_cm) &&
        isset($circ_abs_cm)
    ) {
        $tmp_formula_dog_body = floatval(number_format(((1 - ($height_cm / $circ_abs_cm)) * 100), 2));
        if ($tmp_formula_dog_body < 0) {
            $dog_body_form = 'sottopeso';
        } else if ($tmp_formula_dog_body == 0) {
            $dog_body_form = 'peso_forma';
        } else {
            $dog_body_form = 'sovrappeso';
        }

        $error_measures = false;
        if (!$session_handler->get_session_item('slider_one')['dog_race'] != 'Bassotto') {
            $x = floatval($circ_abs_cm * 1.2);
            $x_15 = (15 * $x) / 100;

            if ($circ_chest_cm < ($x - $x_15) || $circ_chest_cm > ($x + $x_15)) {
                $error_measures = true;
            }
        }

        $session_handler->add_session_item(
            SESSION_ITEM_KEY,
            array(
                'height_cm' => $height_cm,
                'circ_chest_cm' => $circ_chest_cm,
                'circ_abs_cm' => $circ_abs_cm
            )
        );
        $session_handler->add_session_item(
            'dog_user_think',
            $dog_body_form
        );
        
        if (isset($_GET['dog_id'])) {
            header("Location: /slider_3.php?dog_id=" . $_GET['dog_id'] . "&error_measures=" . $error_measures);
        } else {
            header("Location: /slider_3.php?error_measures=" . $error_measures);
        }
    } else {
        header("Location: /slider_2.php?error=true");
    }
} else {
    $session_handler->add_session_item(
        SESSION_ITEM_KEY,
        array(
            'height_cm' => null,
            'circ_chest_cm' => null,
            'circ_abs_cm' => null
        )
    );
    $session_handler->add_session_item(
        'dog_user_think',
        null
    );

    if (isset($_GET['dog_id'])) {
        header("Location: /slider_3.php?dog_id=" . $_GET['dog_id']);
    } else {
        header("Location: /slider_3.php");
    }
}

?>