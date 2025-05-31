<?php
session_start();
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'conecta.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "⚠️ Por favor, complete todos los campos."]);
        exit;
    }

    $con = conecta();

    $stmt = $con->prepare("SELECT usuario_id, correo, contraseña, rol FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($db_usuario_id, $db_email, $db_password, $db_rol);
        $stmt->fetch();

        if (password_verify($password, $db_password)) {
            $_SESSION['correo'] = $db_email;
            $_SESSION['rol'] = $db_rol;
            $_SESSION['usuario_id'] = $db_usuario_id;
            $_SESSION['logueado'] = true;

            switch ($db_rol) {
                case 0:
                    $redirect = "../../index.php";
                    break;
                case 1:
                    $redirect = "../../PruebaDoc.php";
                    break;
                case 2:
                    $redirect = "../../pruebaTuto.php";
                    break;
                default:
                    echo json_encode(["status" => "error", "message" => "❌ Rol no válido."]);
                    exit;
            }

            echo json_encode(["status" => "success", "redirect" => $redirect]);
        } else {
            echo json_encode(["status" => "error", "message" => "❌ Contraseña incorrecta."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "❌ Usuario no encontrado."]);
    }

    $stmt->close();
    $con->close();
} else {
    echo json_encode(["status" => "error", "message" => "⛔ Acceso no permitido."]);
}