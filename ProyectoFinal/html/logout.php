<?php
require_once 'includes/functions.php';
start_session_safe();
session_destroy();
redirect('login.php');
?>
