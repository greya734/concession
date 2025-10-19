<?php
session_start();

// Vérifier que le client est connecté
if (empty($_SESSION['client_id'])) {
    header("Location: client_login.php");
    exit;
}

$client_id = $_SESSION['client_id'];

// Connexion à la base
require 'config.php';
// Récupération des voitures dans le panier du client
$sql = "
    SELECT v.id, v.marque, v.modele, v.prix, v.annee, v.image_url, p.id AS panier_id
    FROM panier p
    JOIN voitures v ON p.id_voiture = v.id
    WHERE p.id_client = :client_id
";
$stmt = $pdo->prepare($sql);
$stmt->execute(['client_id' => $client_id]);
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #fafafa; }
        h1 { color: #333; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #f0f0f0; }
        .price { color: #2a7; font-weight: bold; }
        .btn { padding: 6px 12px; background: #c33; color: white; border-radius: 4px; text-decoration: none; }
        .btn:hover { background: #a11; }
        .empty { color: #777; font-style: italic; margin-top: 20px; }
        .thumb { width: 100px; }
        img { width: 100%; border-radius: 6px; }
    </style>
</head>
<body>

<h1>🛒 Mon panier</h1>

<?php if (empty($articles)): ?>
    <p class="empty">Votre panier est vide pour le moment.</p>
<?php else: ?>
    <table>
        <tr>
            <th>Photo</th>
            <th>Véhicule</th>
            <th>Année</th>
            <th>Prix (€)</th>
            <th>Action</th>
        </tr>
        <?php foreach ($articles as $v): ?>
        <tr>
            <td class="thumb">
                <?php if (!empty($v['image_url'])): ?>
                    <img src="<?= htmlspecialchars($v['image_url']) ?>" alt="<?= htmlspecialchars($v['marque'] . ' ' . $v['modele']) ?>">
                <?php else: ?>
                    🚗
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($v['marque'] . ' ' . $v['modele']) ?></td>
            <td><?= htmlspecialchars($v['annee']) ?></td>
            <td class="price"><?= number_format((float)$v['prix'], 0, ',', ' ') ?> €</td>
            <td>
                <a class="btn" href="supprimer_panier.php?id=<?= $v['panier_id'] ?>">Supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
<a href="index.php">Retour à l'accueuil</a>
</body>
</html>
