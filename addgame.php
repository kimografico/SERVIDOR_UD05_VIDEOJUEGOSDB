<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>VideoJuegosBD</title>
</head>
<body>

<h1>Agregar Videojuego</h1>
<form class="form" action="#" method="POST">
    <div>
        <label for="game_name">Nombre:</label>
        <input type="text" id="game_name" name="game_name" required>
    </div>

    <div>
        <label for="release_year">Año:</label>
        <input type="number" id="release_year" name="release_year" min="1972" max="2050" value="2000" required>
    </div>

    <div>
        <label for="platform">Plataforma:</label>
        <select id="platform" name="platform" required>
            <option value="1">Wii</option>
            <option value="2">NES</option>
            <option value="3">GameBoy</option>
            <option value="4">Nintendo DS</option>
            <option value="5">Xbox 360</option>
            <option value="6">Play Station 3</option>
            <option value="7">Play Station 2</option>
            <option value="8">Super Nintendo</option>
            <option value="9">Game Boy Advance</option>
            <option value="10">Nintendo 3DS</option>
            <option value="11">Play Station 4</option>
            <option value="12">Nintendo 64</option>
            <option value="13">Play Station</option>
            <option value="14">Xbox</option>
            <option value="15">PC</option>
            <option value="16">Atari 2600</option>
            <option value="17">PSP</option>
            <option value="18">Xbox One</option>
            <option value="19">Game Cube</option>
            <option value="20">Wii U</option>
            <option value="21">GENÉRICO</option>
            <option value="22">Dreamcast</option>
            <option value="23">Play Station Vita</option>
            <option value="24">Sega Saturn</option>
            <option value="25">Sega CD</option>
        </select>
    </div>

    <div>
        <label for="genre">Género:</label>
        <select id="genre" name="genre" required>
            <option value="1">Action</option>
            <option value="2">Adventure</option>
            <option value="3">Fighting</option>
            <option value="4">Misc</option>
            <option value="5">Platform</option>
            <option value="6">Puzzle</option>
            <option value="7">Racing</option>
            <option value="8">Role-Playing</option>
            <option value="9">Shooter</option>
            <option value="10">Simulation</option>
            <option value="11">Sports</option>
            <option value="12">Strategy</option>
        </select>
    </div>

    <div>
        <label for="publisher">Editor:</label>
        <input type="text" id="publisher" name="publisher" required>
    </div>

    <div>
        <button type="submit">Añadir Juego</button>
    </div>
</form>

<?php
if (!empty($_POST)) {
    include 'connection.php';
    $connection->beginTransaction();
    try {
        // Comprobamos si el publisher ya existe y si no, lo creamos.
        $consulta = 'SELECT id FROM publisher WHERE publisher_name = :publisher_name';
        $query = $connection->prepare($consulta);
        $query->execute([':publisher_name' => $_POST['publisher']]);
        $publisher = $query->fetch();

        if (!$publisher) {
            $consulta = 'INSERT INTO publisher (publisher_name) VALUES (:publisher_name)';
            $query = $connection->prepare($consulta);
            $query->execute([':publisher_name' => $_POST['publisher']]);
            $publisherId = $connection->lastInsertId();  // ID del editor recién creado
            echo "Publisher ID Insertado: " . $publisherId . "<br>";
        } else {
            $publisherId = $publisher['id'];  // ID del editor si ya existe
            echo "Publisher ID Ya existe: " . $publisherId . "<br>";
        }

        // Insertamos el juego
        $consulta = 'INSERT INTO game (game_name, genre_id) VALUES (:game_name, :genre_id)';
        $query = $connection->prepare($consulta);
        $query->execute([
            ':game_name' => $_POST['game_name'],
            ':genre_id' => $_POST['genre']
        ]);

        $gameId = $connection->lastInsertId();  // ID del juego recién insertado

        $consulta = 'INSERT INTO game_publisher (game_id, publisher_id) VALUES (:game_id, :publisher_id)';
        $query = $connection->prepare($consulta);
        $query->execute([
            ':game_id' => $gameId,            // ID del juego
            ':publisher_id' => $publisherId    // ID del editor
        ]);

        $gamePublisherId = $connection->lastInsertId();  // ID del juego recién insertado



        // Insertamos la plataforma
        $consulta = 'INSERT INTO game_platform (game_publisher_id, platform_id, release_year) VALUES (:game_publisher_id, :platform_id, :release_year)';
        $query = $connection->prepare($consulta);
        $query->execute([
            ':game_publisher_id' => $gamePublisherId,  // ID del editor que ya hemos guardado
            ':platform_id' => $_POST['platform'], 
            ':release_year' => $_POST['release_year'] // Año de lanzamiento
        ]);

        $connection->commit();
        
        // SI TODO OK, REDIRIGIMOS AL JUEGO
        header('Location: http://localhost/UD05/filter.php?game=' . $_POST['game_name']);

    } catch (PDOException $e) {
        // Rollback en caso de error
        $connection->rollBack();
        echo 'Error al añadir el juego: ' . $e->getMessage();
    }
}
?>
</body>
</html>
