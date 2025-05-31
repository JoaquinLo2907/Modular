<?php
session_start();

// Verifica si el usuario está logueado
if (!isset($_SESSION['correo'])) {
    header("Location: pages/prebuilt-pages/default-login.html");
    exit();
}

// Si el usuario hace clic en "logout", destruye la sesión
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: pages/prebuilt-pages/default-login.html");
    exit();
}
?>
