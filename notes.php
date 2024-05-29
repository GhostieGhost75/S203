<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

$username = $_SESSION['username'];

// Connexion à la base de données
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "s203";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $note = $_POST['note'];
    // Insérer la note
    $sql = "INSERT INTO notes (username, notecontent) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $note);
    $stmt->execute();
    $stmt->close();
}

// Récupérer les notes de l'utilisateur
$sql = "SELECT notecontent FROM notes WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username); // Utilisation de "s" pour string
$stmt->execute();
$result = $stmt->get_result();
$notes = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vos notes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container2">
        <h2>Bonjour, <?php echo htmlspecialchars($_SESSION['username']); ?>. Voici vos notes:</h2>

        <ul>
            <?php foreach ($notes as $note): ?>
                <li><?php echo htmlspecialchars($note['notecontent']); ?></li>
            <?php endforeach; ?>
        </ul>

        <h3>Ajouter une note</h3>
        <form action="notes.php" method="post">
            <textarea name="note" required></textarea>
            <br>
            <button type="submit">Ajouter</button>
        </form>

        <form action="logout.php" method="post" class="logout">
            <button type="submit">Se déconnecter</button>
        </form>
    </div>
</body>
</html>
