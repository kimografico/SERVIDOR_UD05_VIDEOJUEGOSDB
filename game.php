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

        if (empty($_GET)) {
            header('Location: index.php');
            exit;
        }

        echo '<a href="index.php">❮❮ VOLVER</a>';

        include 'connection.php';

        $filter = '';

        if (isset($_GET['game'])) {
            echo '<h1>Información sobre el juego</h1>';

            $game = $_GET['game'];

            $consulta =
                'SELECT 
                g.id AS IdJuego,
                g.game_name AS NombreJuego,
                gp.release_year AS AñoLanzamiento,
                p.platform_name AS Plataforma,
                p.platform_description AS Descripcion,
                ge.genre_name AS Género,
                pub.publisher_name AS Editor,
                rs.num_sales AS Ventas,
                r.region_name AS Region
            FROM 
                game g
            JOIN 
                game_publisher gpu ON g.id = gpu.game_id
            JOIN 
                game_platform gp ON gpu.id = gp.game_publisher_id
            JOIN 
                platform p ON gp.platform_id = p.id
            JOIN 
                genre ge ON g.genre_id = ge.id
            JOIN 
                publisher pub ON gpu.publisher_id = pub.id
            JOIN 
                region_sales rs ON rs.game_platform_id = gp.id
            JOIN 
                region r ON rs.region_id = r.id
            WHERE g.game_name = :game';

            $query = $connection->prepare($consulta);  // preparamos la consulta
            $query->bindParam(':game', $game);
            $query->execute();  // ejecutamos la consulta

            $juego = $query->fetchAll();  // Trabajamos con fetchAll para hacer las mínimas consultas al servidor

            // echo '<pre>'; echo print_r($juego); echo '</pre>';

            echo "<div class='game'>";
            echo "<h1><a href='filter.php?game=" . $juego[0]['NombreJuego'] . "'>" . $juego[0]['NombreJuego'] . '</a></h1>';
            echo "<hr>";
            echo "Año de lanzamiento: <a href='filter.php?year=" . $juego[0]['AñoLanzamiento'] . "'>" . $juego[0]['AñoLanzamiento'] . '</a> <br><br> ';
            echo "Plataforma: <a href='filter.php?platform=" . $juego[0]['Plataforma'] . "'>" . $juego[0]['Plataforma'] . '</a> <br><br> ';
            echo "Género: <a href='filter.php?genre=" . $juego[0]['Género'] . "'>" . $juego[0]['Género'] . '</a> <br><br> ';
            echo "Editor: <a href='filter.php?publisher=" . $juego[0]['Editor'] . "'>" . $juego[0]['Editor'] . '</a> <br><br>';
            echo "Región: <b>" . $juego[0]['Region'] . '</b> <br><br>';
            echo "Ventas: <b>" . $juego[0]['Ventas'] . ' millones de juegos vendidos a nivel mundial</b> <br><br>';
            echo "<a class='delete' href='delete.php?gameid=" . $juego[0]['IdJuego'] . "'>✖</a>";
            echo '</hr>';

        } else {
            header('Location: index.php');
        }

        unset($connection);
        unset($juegos);

    ?>
</body>
</html>