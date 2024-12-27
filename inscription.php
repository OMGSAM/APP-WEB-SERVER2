<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Bienfaisant</title>
</head>
<body>
    <h1>Inscription - Bienfaisant</h1>
    <?php
    // Initialiser les erreurs
    $errors = [];
    $success = "";

    // Traitement du formulaire
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Récupération des données du formulaire
        $nomBien = htmlspecialchars($_POST["nomBien"]);
        $prenomBien = htmlspecialchars($_POST["prenomBien"]);
        $emailBien = htmlspecialchars($_POST["emailBien"]);
        $passBien = $_POST["passBien"];
        $confirmPass = $_POST["confirmPass"];

        // Validation des champs
        if (empty($nomBien) || empty($prenomBien) || empty($emailBien) || empty($passBien) || empty($confirmPass)) {
            $errors[] = "Tous les champs sont obligatoires.";
        } elseif (!filter_var($emailBien, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'adresse e-mail n'est pas valide.";
        } elseif ($passBien !== $confirmPass) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }

        // Enregistrement dans la base de données si aucune erreur
        if (empty($errors)) {
            // Connexion à la base de données
            $conn = new mysqli("localhost", "root", "", "FondationCo");

            // Vérifier la connexion
            if ($conn->connect_error) {
                die("Erreur de connexion : " . $conn->connect_error);
            }

            // Hachage du mot de passe
            $hashedPass = password_hash($passBien, PASSWORD_DEFAULT);

            // Insertion des données
            $stmt = $conn->prepare("INSERT INTO Bienfaisant (nomBien, prenomBien, emailBien, passBien) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nomBien, $prenomBien, $emailBien, $hashedPass);

            if ($stmt->execute()) {
                $success = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
            } else {
                $errors[] = "Une erreur est survenue lors de l'inscription.";
            }

            // Fermeture de la connexion
            $stmt->close();
            $conn->close();
        }
    }
    ?>

    <!-- Affichage des messages d'erreur -->
    <?php if (!empty($errors)): ?>
        <ul style="color: red;">
            <?php foreach ($errors as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <!-- Message de succès -->
    <?php if ($success): ?>
        <p style="color: green;"><?= $success ?></p>
    <?php endif; ?>

    <!-- Formulaire d'inscription -->
    <form method="POST">
        <label for="nomBien">Nom :</label>
        <input type="text" name="nomBien" id="nomBien" required><br><br>

        <label for="prenomBien">Prénom :</label>
        <input type="text" name="prenomBien" id="prenomBien" required><br><br>

        <label for="emailBien">Adresse e-mail :</label>
        <input type="email" name="emailBien" id="emailBien" required><br><br>

        <label for="passBien">Mot de passe :</label>
        <input type="password" name="passBien" id="passBien" required><br><br>

        <label for="confirmPass">Confirmer le mot de passe :</label>
        <input type="password" name="confirmPass" id="confirmPass" required><br><br>

        <button type="submit">S'inscrire</button>
    </form>
</body>
</html>
