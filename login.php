<?php
require 'config.php';

// Démarrage de la session
session_start();

// Vérification si l'utilisateur est déjà connecté, s'il l'est, le rediriger vers la page d'accueil
if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Initialisation des variables
$username = $password = '';
$error = '';

// Traitement du formulaire de connexion lors de la soumission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Requête pour vérifier les informations de connexion dans la base de données
    $sql = "SELECT * FROM Utilisateur WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    // Vérification du mot de passe si l'utilisateur existe
    if ($user && password_verify($password, $user['password'])) {
        // Informations de connexion correctes, démarrer la session et rediriger vers la page d'accueil
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_role'] = $user['role'];
        header('Location: index.php');
        exit;
    } else {
        // Identifiants invalides, affichage d'un message d'erreur
        $error = "Nom d'utilisateur ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Connexion</h2>
        <?php if (!empty($error)): ?>
        <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="" method="post">
            <div class="form-group">
                <label for="username">Nom d'utilisateur:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>
</html>
