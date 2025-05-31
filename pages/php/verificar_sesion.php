<?php
session_start();
if (!isset($_SESSION['logueado']) || $_SESSION['logueado'] !== true) {
    header("Location: ../prebuilt-pages/default-login.html"); // Redirige al login
    exit();
}
?>
