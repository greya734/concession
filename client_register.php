<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=concession;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $tel = trim($_POST['telephone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO clients (nom,email,telephone,password_hash) VALUES (:n,:e,:t,:p)");
        $stmt->execute(['n'=>$nom,'e'=>$email,'t'=>$tel,'p'=>$password]);
        $msg = "✅ Compte créé avec succès. Vous pouvez vous connecter.";
    } catch (PDOException $e) {
        $msg = "❌ Erreur : " . $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8"><title>Inscription client</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="card login">
    <h2>Créer un compte client</h2>
    <p style="color:green"><?= htmlspecialchars($msg) ?></p>
    <form method="post">
        <input type="text" name="nom" placeholder="Nom complet" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="text" name="telephone" placeholder="Téléphone"><br>
        <input type="password" name="password" placeholder="Mot de passe" required><br>
        <button  class="submit" type="submit">S'inscrire</button>
    </form>
    <p>Déjà inscrit ? <a href="client_login.php">Se connecter</a></p>
</div>
</body>
</html>
