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

    if (empty($_GET)){
        header('Location: index.php');
        exit;
    }

    echo '<a href="index.php">❮❮ VOLVER</a>';

    include 'connection.php';

    $filter = '';

    if (isset($_GET['game'])) {
        echo '<h1>Información sobre el juego</h1>';
        $filter = 'g.game_name = :game';
        $game = $_GET['game'];
    
    } else if (isset($_GET['year'])) {
        echo '<h1>Juegos lanzados el año ' . $_GET['year'] . ' </h1>';
        $filter = 'gp.release_year = :year';
        $year = $_GET['year'];
    
    } else if (isset($_GET['platform'])) {
        echo '<h1>Juegos para ' . $_GET['platform'] . ' </h1>';
        $filter = 'p.platform_name = :platform';
        $platform = $_GET['platform'];
    
    } else if (isset($_GET['genre'])) {
        echo '<h1>Juegos de ' . $_GET['genre'] . ' </h1>';
        $filter = 'ge.genre_name = :genre';
        $genre = $_GET['genre'];
    
    } else if (isset($_GET['publisher'])) {
        echo '<h1>Juegos editados por ' . $_GET['publisher'] . ' </h1>';
        $filter = 'pub.publisher_name = :publisher';
        $publisher = $_GET['publisher'];
    }

    if ($filter !== '') {
        $consulta = 
            'SELECT 
                g.id AS IdJuego,
                g.game_name AS NombreJuego,
                gp.release_year AS AñoLanzamiento,
                p.platform_name AS Plataforma,
                p.platform_description AS Descripcion,
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
                publisher pub ON gpu.publisher_id = pub.id
            WHERE ' . $filter;
    
        $query = $connection->prepare($consulta);  //preparamos la consulta

        if (isset($game)) {  //preparamos el parámetro que corresponda
            $query->bindParam(':game', $game);
        } elseif (isset($year)) {
            $query->bindParam(':year', $year);
        } elseif (isset($platform)) {
            $query->bindParam(':platform', $platform); // Hay un fallo en la descripción de PC: Se usa << en la descripción, lo que destroza el html. He tenido que usar htmlspecialchars
        } elseif (isset($genre)) {
            $query->bindParam(':genre', $genre);
        } elseif (isset($publisher)) {
            $query->bindParam(':publisher', $publisher);
        }
    
        $query->execute(); // ejecutamos la consulta
        
    
    } else {
        echo "No se encontró ningún filtro válido.";
    }

    $juegos = $query->fetchAll();  // Trabajamos con fetchAll para hacer las mínimas consultas al servidor

    if (isset($platform)) {
        echo "<div class='platform'>";
        echo "<h1>". $juegos[0]['Plataforma'] . "</h1>";
        echo htmlspecialchars($juegos[0]['Descripcion']);
        echo "</div>";
    }
    echo "<div class='container'>";
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
    echo "</div>";

    unset ($connection);
    unset ($juegos);

    ?>
</body>
</html>