<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Valider les données
    if (empty($username) || empty($password)) {
        die("Veuillez remplir tous les champs.");
    }

    // Hacher le mot de passe
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

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

    // Vérifier si le nom d'utilisateur existe déjà
    $sql = "SELECT username FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        die("Le nom d'utilisateur est déjà pris.");
    }

    // Insérer les données dans la base de données
    $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Inscription réussie. Vous pouvez maintenant vous connecter.";
	header("Location: index.html");
	exit();
    } else {
        echo "Erreur: " . $stmt->error;
    }
    header("Location: index.html");
    // Fermer les connexions
    $stmt->close();
    $conn->close();
}
?>
