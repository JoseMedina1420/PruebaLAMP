<?php
// Incluir la conexión a la base de datos
include 'conexion.php';

// Obtener el número de página actual y el número de productos por página
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 12; // Productos por página
$offset = ($page - 1) * $limit; // Calcular el offset

// Consulta para obtener productos con paginación
$sql = "SELECT id, nombre, descripcion, talla, precio, stock, imagen FROM playera_infantil LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

$productos = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Convertir el BLOB en una cadena Base64
        $row['imagen'] = base64_encode($row['imagen']);
        $productos[] = $row;
    }
}

// Obtener el número total de productos
$totalProductosResult = $conn->query("SELECT COUNT(*) as total FROM playera_infantil");
$totalProductos = $totalProductosResult->fetch_assoc()['total'];

// Calcular el número total de páginas
$totalPages = ceil($totalProductos / $limit);

// Enviar productos y total de páginas como respuesta
$response = array(
    'productos' => $productos,
    'totalPages' => $totalPages
);

header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
?>