<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>VideoJuegosBD</title>
</head>
<body>
    <?php

    echo '<a href="index.php">❮❮ VOLVER</a>';
    
    include 'connection.php';

    if (isset($_GET['gameid']) && is_numeric($_GET['gameid'])) {

        try {
            $connection->beginTransaction();  // se hace una transacción por si algo falla, revertirlo

            // BORRADO INVERSO:    JUEGO -> PUBLISHER -> PLATFORM -> REGION SALES

            // borramos de region sales
            $consulta = 'DELETE region_sales FROM region_sales
                        JOIN game_platform ON region_sales.game_platform_id = game_platform.id
                        JOIN game_publisher ON game_platform.game_publisher_id = game_publisher.id
                        WHERE game_publisher.game_id = :gameid';
            $query = $connection->prepare($consulta);
            $query->execute([':gameid' => $_GET['gameid']]);

            // borramos de plataforma
            $consulta = 'DELETE game_platform FROM game_platform
                         JOIN game_publisher ON game_platform.game_publisher_id = game_publisher.id
                         WHERE game_publisher.game_id = :gameid';
            $query = $connection->prepare($consulta);
            $query->execute([':gameid' => $_GET['gameid']]);

            // borramos de editor
            $consulta = 'DELETE FROM game_publisher WHERE game_id = :gameid';
            $query = $connection->prepare($consulta);
            $query->execute([':gameid' => $_GET['gameid']]);

            // borramos el juego
            $consulta = 'DELETE FROM game WHERE id = :gameid';
            $query = $connection->prepare($consulta);
            $query->execute([':gameid' => $_GET['gameid']]);

            $connection->commit(); // Se hace la transacción

            echo "<h1 class='game'>Juego con ID " . $_GET['gameid'] . " eliminado correctamente.</h1>";

            // PARA VOLVER A LA PÁGINA ANTERIOR. QUEDA BIEN, PERO PARECE QUE NO HA CAMBIADO DE PAGINA.
            // PREFIERO QUE SE VEA EL MENSAJE DE ÉXITO (REFERER MANDA A LA PAG DE DONDE VIENES)

            // if (isset($_SERVER['HTTP_REFERER'])) {
            //     header('Location: ' . $_SERVER['HTTP_REFERER']);
            // }

        } catch (PDOException $e) {
            $connection->rollBack(); // Si falla, dale marcha atrás chica disco 🕺
            echo "<h1 class='error'>Error al eliminar el juego: " . $e->getMessage() . "</h1>";
        }
    } else {
        echo "<h1 class='error'>No se especificó un ID de juego válido para eliminar.</h1>";
    }


    ?>
</body>
</html>
