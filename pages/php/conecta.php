<?php
define("HOST", 'localhost');  // Servidor de base de datos
define("BD", 'schoolcare');  // Nombre de la base de datos
define("USER_BD", 'root');  // Usuario predeterminado de MySQL en XAMPP
define("PASS_BD", '');  // En XAMPP, la contraseña de root es vacía

function conecta() {
    $con = new mysqli(HOST, USER_BD, PASS_BD, BD);
    if ($con->connect_error) {
        die("Conexión fallida: " . $con->connect_error);
    }
    return $con;
}
?>
