<?php
declare(strict_types=1);
session_start();
require 'conecta.php';
header('Content-Type: application/json');

// Desactivar warnings visibles para evitar interferencias con JSON
ini_set('display_errors', '0');
error_reporting(E_ALL);

$con = conecta();

// Recibir datos JSON
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Verificar datos
if (
  !isset($data['asistencias']) || !is_array($data['asistencias']) ||
  !isset($data['materia_id'])
) {
  echo json_encode(["success" => false, "error" => "Datos inválidos"]);
  exit;
}

$materia_id = (int)$data['materia_id'];
$fecha = date("Y-m-d");

// Verificar sesión
$usuario_id = $_SESSION['usuario_id'] ?? null;
if (!$usuario_id) {
  echo json_encode(["success" => false, "error" => "No autenticado"]);
  exit;
}

// Obtener el docente_id correspondiente al usuario_id
$docenteQuery = $con->prepare("SELECT docente_id FROM docentes WHERE usuario_id = ?");
$docenteQuery->bind_param("i", $usuario_id);
$docenteQuery->execute();
$result = $docenteQuery->get_result();

if ($result->num_rows === 0) {
  echo json_encode(["success" => false, "error" => "Docente no encontrado"]);
  exit;
}

$docente_id = (int)$result->fetch_assoc()['docente_id'];
$docenteQuery->close();

// Preparar inserción
$query = "INSERT INTO asistencias (estudiante_id, estado, fecha, docente_id, materia_id) VALUES (?, ?, ?, ?, ?)";
$stmt = $con->prepare($query);

if (!$stmt) {
  echo json_encode(["success" => false, "error" => "Error en la preparación"]);
  exit;
}

// Insertar asistencias
foreach ($data['asistencias'] as $registro) {
  $estudiante_id = (int)$registro['estudiante_id'];
  $estado = $registro['estado'];

  $stmt->bind_param("sssii", $estudiante_id, $estado, $fecha, $docente_id, $materia_id);
  if (!$stmt->execute()) {
    echo json_encode(["success" => false, "error" => "Error al insertar asistencia"]);
    exit;
  }
}

$stmt->close();
$con->close();

echo json_encode(["success" => true]);
