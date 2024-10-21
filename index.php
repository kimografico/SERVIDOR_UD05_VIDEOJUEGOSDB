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
    include 'connection.php';

    $consulta = 
        'SELECT 
            g.id AS IdJuego,
            g.game_name AS NombreJuego,
            gp.release_year AS AñoLanzamiento,
            p.platform_name AS Plataforma,
            ge.genre_name AS Género,
            pub.publisher_name AS Editor
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
            publisher pub ON gpu.publisher_id = pub.id;';

    $juegos = $connection->query($consulta)->fetchAll();  // Trabajamos con fetchAll para hacer las mínimas consultas al servidor
    // print_r($juegos);

    echo '<h1 class="titulo">🎮 VideoJuegosDB 👾</h1>';
    foreach ($juegos as $juego){ 
        echo "<div class='game'>";
        echo "<h1><a href='filter.php?game=". $juego['NombreJuego'] . "'>" . $juego['NombreJuego'] . "</a></h1>";
        echo "Año de lanzamiento: <a href='filter.php?year=". $juego['AñoLanzamiento'] . "'>" . $juego['AñoLanzamiento'] . "</a> | ";
        echo "Plataforma: <a href='filter.php?platform=" . $juego['Plataforma'] . "'>" . $juego['Plataforma'] . "</a> | ";
        echo "Género: <a href='filter.php?genre=" . $juego['Género'] . "'>" . $juego['Género'] . "</a> | ";
        echo "Editor: <a href='filter.php?publisher=" . $juego['Editor'] . "'>" . $juego['Editor'] . "</a>";
        echo "<a class='delete' href='delete.php?gameid=". $juego['IdJuego'] . "'>✖</a>";
        echo "</div>";
    }

    unset ($connection);
    unset ($juegos);

    ?>
</body>
</html>