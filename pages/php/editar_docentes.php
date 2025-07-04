<?php
// Bufferizamos la salida y desactivamos errores para devolver solo JSON
ob_start();
ini_set('display_errors', 0);
error_reporting(0);

require '../php/conecta.php';
header('Content-Type: application/json');
$conexion = conecta();
if (!$conexion) {
    http_response_code(500);
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Error de conexión.']);
    exit;
}

// 1) GET: devolver datos de un docente (incluye creado_en y actualizado_en)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conexion->prepare("SELECT
        docente_id,
        nombre,
        apellido,
        telefono,
        correo,
        activo,
        puesto,
        genero,
        fecha_nacimiento,
        salario,
        direccion,
        foto_url,
        creado_en,
        actualizado_en
    FROM docentes
    WHERE docente_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        ob_end_clean();
        echo json_encode($row);
    } else {
        http_response_code(404);
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Docente no encontrado.']);
    }
    $stmt->close();
    exit;
}

// 2) POST: procesar formulario multipart/form-data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger campos
    $docente_id           = $_POST['id']               ?? null;
    $nombre               = $_POST['nombre']           ?? '';
    $apellido             = $_POST['apellido']         ?? '';
    $telefono             = $_POST['telefono']         ?? '';
    $correo               = $_POST['correo']           ?? '';
    $activo               = $_POST['activo']           ?? 1;
    $puesto               = $_POST['puesto']           ?? '';
    $genero               = $_POST['genero']           ?? '';
    $fecha_nacimiento     = $_POST['fecha_nacimiento'] ?? null;
    $salario              = $_POST['salario']          ?? '';
    $direccion            = $_POST['direccion']        ?? '';
    $foto_url             = $_POST['foto_url']         ?? '';
    $nueva_contraseña     = $_POST['nueva_contraseña']     ?? '';
    $confirmar_contraseña = $_POST['confirmar_contraseña'] ?? '';

    // Validación básica
    if (!$docente_id) {
        http_response_code(400);
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'ID no proporcionado.']);
        exit;
    }

    // 2.1) Procesar posible subida de archivo
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $tmp  = $_FILES['foto']['tmp_name'];
        $name = basename($_FILES['foto']['name']);
        // Subir DOS niveles arriba a Modular/assets/img/docentes
        $dest = __DIR__ . '/../../assets/img/docentes/' . $name;
        if (move_uploaded_file($tmp, $dest)) {
            // Actualizamos la URL para la BD
            $foto_url = 'assets/img/docentes/' . $name;
        }
    }

    // Actualizar contraseña si se solicitó
    if ($nueva_contraseña !== '' || $confirmar_contraseña !== '') {
        if ($nueva_contraseña !== $confirmar_contraseña) {
            http_response_code(400);
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden.']);
            exit;
        }
        // Obtener usuario_id
        $q = $conexion->prepare('SELECT usuario_id FROM docentes WHERE docente_id = ?');
        $q->bind_param('i', $docente_id);
        $q->execute();
        $q->bind_result($usuario_id);
        $q->fetch();
        $q->close();
        if ($usuario_id) {
            $hash = password_hash($nueva_contraseña, PASSWORD_BCRYPT);
            $up = $conexion->prepare('UPDATE usuarios SET contraseña = ? WHERE usuario_id = ?');
            $up->bind_param('si', $hash, $usuario_id);
            $up->execute();
            $up->close();
        }
    }

    // Normalizar fecha nula
    if ($fecha_nacimiento === '0000-00-00') {
        $fecha_nacimiento = null;
    }

    // Update de la tabla docentes
    $sql = "UPDATE docentes SET
                nombre           = ?,
                apellido         = ?,
                telefono         = ?,
                correo           = ?,
                activo           = ?,
                puesto           = ?,
                genero           = ?,
                fecha_nacimiento = ?,
                salario          = ?,
                direccion        = ?,
                foto_url         = ?,
                actualizado_en   = NOW()
            WHERE docente_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param(
        'ssssissssssi',
        $nombre,
        $apellido,
        $telefono,
        $correo,
        $activo,
        $puesto,
        $genero,
        $fecha_nacimiento,
        $salario,
        $direccion,
        $foto_url,
        $docente_id
    );

    if ($stmt->execute()) {
        ob_end_clean();
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Error al actualizar el docente.']);
    }
    $stmt->close();
    exit;
}

// Métodos no permitidos
http_response_code(405);
ob_end_clean();
echo json_encode(['success' => false, 'message' => 'Método no permitido.']);