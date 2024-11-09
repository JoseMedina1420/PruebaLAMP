<?php
include 'conexion.php';
// Parámetros de la página y el límite
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 12;
$offset = ($page - 1) * $limit;

// Consulta para obtener los artículos de ambas tablas con paginación
$sql = "
    SELECT * FROM (
        SELECT id, nombre, descripcion, talla, precio, stock, imagen FROM playera_infantil
        UNION
        SELECT id, nombre, descripcion, talla, precio, stock, imagen FROM playera_adulto
    ) AS combined
    LIMIT $limit OFFSET $offset
";
$result = $conn->query($sql);

// Consulta para obtener el total de artículos
$totalSql = "
    SELECT COUNT(*) as total FROM (
        SELECT id FROM playera_infantil
        UNION ALL
        SELECT id FROM playera_adulto
    ) AS combined
";
$totalResult = $conn->query($totalSql);
$totalRow = $totalResult->fetch_assoc();
$totalArticulos = $totalRow['total'];

// Arreglo para almacenar los artículos
$articulos = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Convertir el BLOB de la imagen a Base64
        $row['imagen'] = 'data:image/jpeg;base64,' . base64_encode($row['imagen']);
        $articulos[] = $row;
    }
}

// Respuesta en formato JSON
echo json_encode([
    "articulos" => $articulos,
    "totalArticulos" => $totalArticulos
]);
$conn->close();
?>
