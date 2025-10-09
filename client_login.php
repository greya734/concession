<?php
require 'config.php';
session_start();


$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM clients WHERE email = :e");
    $stmt->execute(['e'=>$email]);
    $client = $stmt->fetch();

    if ($client && password_verify($password, $client['password_hash'])) {
        $_SESSION['client_id'] = $client['id'];
        $_SESSION['client_nom'] = $client['nom'];
        $_SESSION['client_email'] = $client['email'];
        $_SESSION['client_tel'] = $client['telephone'];
        header("Location: index.php");
        exit;
    } else {
        $error = "❌ Email ou mot de passe incorrect";
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8"><title>Connexion client</title>
    <link rel="stylesheet" href="style.css"/>
</head>
<body>
        <div class="card login">
            <h2>Connexion client</h2>
            <p style="color:red"><?= htmlspecialchars($error) ?></p>
        
            <form method="post">
                <input type="email" name="email" placeholder="Email" required><br>
                <input type="password" name="password" placeholder="Mot de passe" required><br>
                <button  class="submit" type="submit">Se connecter</button>
            </form>
            <a href="client_register.php">Créer un compte</a>
        </div>
</body>
</html>
