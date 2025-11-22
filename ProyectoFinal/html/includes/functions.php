<?php
function start_session_safe() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

function is_logged_in() {
    start_session_safe();
    return isset($_SESSION['user_id']);
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function get_user_name() {
    start_session_safe();
    return isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Invitado';
}
?>
