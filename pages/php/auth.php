<?php
session_start();

// Verifica si el usuario está logueado
if (!isset($_SESSION['correo'])) {
    header("Location: pages/prebuilt-pages/default-login.html");
    exit();
}

// 🔁 Logout universal
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: /dashboard/Modular/pages/prebuilt-pages/default-login.html');
    exit;
}





?>