<?php
    $db_host = getenv('DB_HOST') ?: 'localhost';
    $db_user = getenv('DB_USER') ?: '';
    $db_pass = getenv('DB_PASS') ?: '';
    $db_name = getenv('DB_NAME') ?: '';
    $db_prefix = getenv('DB_PREFIX') ?: 'pp_';
    $mode = 'live';
    $password_reset = 'off';
?>
