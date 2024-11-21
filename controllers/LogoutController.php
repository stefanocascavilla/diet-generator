<?php

require '../handlers/SessionHandler.php';

$session_handler = new SessionHandlerClass();
$session_handler->destroy_session();

sleep(1);
header("Location: /login.php");

?>