<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'conexion.php';

header('Content-Type: application/json');

// Obtener los datos enviados en formato JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$id_producto = $data['id'] ?? null;
$descripcion = $data['descripcion'] ?? null;
$nombre = $data['nombre'] ?? null;
$precio = $data['precio'] ?? null;
$cantidad = $data['cantidad'] ?? null;

// Verificación de datos
if (empty($id_producto)) {
    echo json_encode(['success' => false, 'error' => 'ID de producto no proporcionado']);
    exit;
}

// Consulta para verificar si el producto existe
$sql = "SELECT * FROM playera_infantil WHERE id = ? UNION SELECT * FROM playera_adulto WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_producto, $id_producto);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Si el producto existe, se añade al carrito
    $insert_sql = "INSERT INTO carrito (producto_id, nombre, descripcion, precio, cantidad) VALUES (?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("issdi", $id_producto, $nombre, $descripcion, $precio, $cantidad);

    if ($insert_stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al agregar al carrito']);
    }
    $insert_stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Producto no encontrado']);
}

$stmt->close();
$conn->close();
