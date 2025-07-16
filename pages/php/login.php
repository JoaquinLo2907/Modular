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

            // Si es profesor
            if ($db_rol == 1) {
                $stmt_doc = $con->prepare("SELECT docente_id FROM docentes WHERE usuario_id = ?");
                $stmt_doc->bind_param("i", $db_usuario_id);
                $stmt_doc->execute();
                $stmt_doc->bind_result($docente_id);
                if ($stmt_doc->fetch()) {
                    $_SESSION['docente_id'] = $docente_id;
                }
                $stmt_doc->close();
            }

            if ($db_rol == 2) {
                $stmt_tutor = $con->prepare("SELECT tutor_id, nombre, apellido, correo FROM tutores WHERE usuario_id = ?");
                $stmt_tutor->bind_param("i", $db_usuario_id);
                $stmt_tutor->execute();
                $stmt_tutor->store_result();
                if ($stmt_tutor->num_rows > 0) {
                    $stmt_tutor->bind_result($tutor_id, $nombre, $apellido, $correo);
                    $stmt_tutor->fetch();

                    $_SESSION['tutor_id'] = $tutor_id;
                    $_SESSION['nombre'] = $nombre;
                    $_SESSION['apellido'] = $apellido;
                    $_SESSION['correo'] = $correo;

                    error_log("✅ Tutor cargado: $nombre $apellido");
                } else {
                    error_log("❌ No se encontró tutor con usuario_id: $db_usuario_id");
                }
                $stmt_tutor->close();
            }

            // Redirigir al dashboard correspondiente según el rol
            $redirect = '';
            switch ($db_rol) {
                case 0:
                    $redirect = '../../index.php';      // Admin
                    break;
                case 1:
                    $redirect = '../../Docentes.php';   // Docente
                    break;
                case 2:
                    $redirect = '../../Tutor.php';      // Tutor
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
