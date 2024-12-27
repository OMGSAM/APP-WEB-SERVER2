<?php
// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "FondationCo");

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Récupérer les familles
$result = $conn->query("SELECT * FROM Famille");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Opérations en cours</title>
</head>
<body>
    <form method="POST">
        <label for="famille">Sélectionnez une famille :</label>
        <select name="famille" id="famille">
            <?php while ($row = $result->fetch_assoc()): ?>
                <option value="<?= $row['idFamille'] ?>"><?= $row['nomFamille'] ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit">Afficher</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $familleId = $_POST['famille'];
        $query = $conn->prepare("SELECT idOp, nomOp, cumulMontant, nomBeneficiare FROM Operation WHERE idFamille = ? AND dateFin > CURDATE()");
        $query->bind_param("i", $familleId);
        $query->execute();
        $result = $query->get_result();

        echo "<table border='1'>
                <tr>
                    <th>ID Opération</th>
                    <th>Nom Opération</th>
                    <th>Montant Total Obtenu</th>
                    <th>Nom Bénéficiaire</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['idOp']}</td>
                    <td>{$row['nomOp']}</td>
                    <td>{$row['cumulMontant']}</td>
                    <td>{$row['nomBeneficiare']}</td>
                  </tr>";
        }
        echo "</table>";
    }
    ?>
</body>
</html>
