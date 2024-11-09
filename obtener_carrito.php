<?php
include 'conexion.php';

$sql = "SELECT nombre, descripcion, precio, cantidad FROM carrito";
$result = $conn->query($sql);

$productos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $productos[] = $row;
    }
}

echo json_encode($productos);

$conn->close();
?>
