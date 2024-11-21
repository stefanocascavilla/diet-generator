<?php

function get_db_secrets (string $secrets_json_path) {
    $secrets_json = file_get_contents($secrets_json_path);
    return json_decode($secrets_json, false);
}

?>