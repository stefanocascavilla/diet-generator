<?php

class SessionHandlerClass {
    public const LOGIN_SESSION_NAME = 'logged_user_gen_dieta';

    function __construct () {
        session_start();
    }

    public function destroy_session () {
        session_unset();
        session_destroy();
    }

    public function check_existing_logged_session () {
        return isset($_SESSION[self::LOGIN_SESSION_NAME]);
    }

    public function add_session_item (string $key, $value) {
        $_SESSION[$key] = $value;
    }

    public function get_session_item (string $key) {
        return $_SESSION[$key];
    }

    public function delete_session_item (string $key) {
        unset($_SESSION[$key]);
    }
}

?>