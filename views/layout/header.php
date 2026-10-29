<?php
/**
 * Layout: cabecera comun a todas las pantallas.
 * Recibe $accion desde public/index.php solo para marcar el link activo.
 */
$links = [
    'crear'   => 'Crear pedido',
    'listar'  => 'Listar pedidos',
    'reporte' => 'Reporte',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratorio · Pedidos</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header class="topbar">
    <span class="marca">🧪 Laboratorio</span>
    <nav>
        <?php foreach ($links as $ruta => $texto): ?>
            <a href="?accion=<?= $ruta ?>"
               class="<?= $accion === $ruta ? 'activo' : '' ?>"><?= $texto ?></a>
        <?php endforeach; ?>
    </nav>
</header>
<main class="contenido">
