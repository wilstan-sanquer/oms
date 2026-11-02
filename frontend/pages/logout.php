<?php
session_start();
unset($_SESSION['user_id'], $_SESSION['username']);
$_SESSION['flash'] = 'Vous êtes bien déconnecté.';
header('Location: ../index.php');
exit();
