<?php
session_start();

// Vérifier que le client est connecté
if (empty($_SESSION['client_id'])) {
    header("Location: client_login.php");
    exit;
}

// Connexion BDD
require 'config.php';

$message = "";

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $password = $_POST['password'];

    // Mise à jour avec ou sans changement de mot de passe
    if (!empty($password)) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE clients SET nom=:nom, email=:email, telephone=:telephone, password_hash=:hash WHERE id=:id");
        $stmt->execute([
            'nom' => $nom,
            'email' => $email,
            'telephone' => $telephone,
            'hash' => $hash,
            'id' => $_SESSION['client_id']
        ]);
    } else {
        $stmt = $pdo->prepare("UPDATE clients SET nom=:nom, email=:email, telephone=:telephone WHERE id=:id");
        $stmt->execute([
            'nom' => $nom,
            'email' => $email,
            'telephone' => $telephone,
            'id' => $_SESSION['client_id']
        ]);
    }

    $message = "✅ Informations mises à jour avec succès.";
    // Rafraîchir les infos du client
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['client_id']]);
    $client = $stmt->fetch();
    $_SESSION['client_nom'] = $client['nom']; // mise à jour du nom affiché dans l'icône
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Modifier mon profil</title>
    <link rel="stylesheet" href="style.css"/>
</head>
<body>
  <header>
    <h2>AutoConcession — Modifier mon profil</h2>
  </header>

  <div class="wrap">
    <h2>Éditer mes informations</h2>

    <?php if ($message): ?>
      <p class="message"><?= $message ?></p>
    <?php endif; ?>

    <form method="post">
      <label>Nom :</label>
      <input type="text" name="nom" value="<?= htmlspecialchars($client['nom']) ?>" required>

      <label>Email :</label>
      <input type="email" name="email" value="<?= htmlspecialchars($client['email']) ?>" required>

      <label>Téléphone :</label>
      <input type="text" name="telephone" value="<?= htmlspecialchars($client['telephone']) ?>">

      <label>Nouveau mot de passe (laisser vide si inchangé) :</label>
      <input type="password" name="password">

      <button class="submit" type="submit">Mettre à jour</button>
    </form>

    <a href="profil.php" class="back">⬅ Retour au profil</a>
  </div>
</body>
</html>
