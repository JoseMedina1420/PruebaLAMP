<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include 'conexion.php';

try {
    // Iniciar una transacción
    $conn->begin_transaction();

    // Obtener los datos del carrito
    $sql = "SELECT nombre, descripcion, precio, cantidad FROM carrito";
    $result = $conn->query($sql);

    $total = 0;
    $productosVendidos = [];

    while ($row = $result->fetch_assoc()) {
        // Guardar los productos vendidos para el ticket
        $productosVendidos[] = $row;

        // Calcular el total de la compra
        $total += $row['precio'] * $row['cantidad'];

        // Insertar en la tabla 'nota'
        $insertNota = $conn->prepare("INSERT INTO nota (nombre, descripcion, cantidad, precio) VALUES (?, ?, ?, ?)");
        $insertNota->bind_param("ssii", $row['nombre'], $row['descripcion'], $row['cantidad'], $row['precio']);
        $insertNota->execute();
        $insertNota->close();

        // Reducir el stock del producto en 'playera_adulto' o 'playera_infantil'
        $updateStockAdulto = $conn->prepare("UPDATE playera_adulto SET stock = stock - ? WHERE nombre = ?");
        $updateStockAdulto->bind_param("is", $row['cantidad'], $row['nombre']);
        $updateStockAdulto->execute();
        $updateStockAdulto->close();

        $updateStockInfantil = $conn->prepare("UPDATE playera_infantil SET stock = stock - ? WHERE nombre = ?");
        $updateStockInfantil->bind_param("is", $row['cantidad'], $row['nombre']);
        $updateStockInfantil->execute();
        $updateStockInfantil->close();
    }

    // Vaciar la tabla del carrito
    $conn->query("DELETE FROM carrito");

    // Confirmar la transacción
    $conn->commit();

    // Enviar respuesta de éxito
    echo json_encode([
        'success' => true,
        'total' => $total,
        'productos' => $productosVendidos
    ]);
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conn->rollback();

    // Enviar respuesta de error
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
