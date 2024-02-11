<?php
require 'config.php'; // Connexion à la base de données

session_start(); // démarrer la session

if (!isset($_SESSION['user_role'])) {
    header('Location: login.php'); // Rediriger vers la page de connexion si non connecté
    exit;
}
$userRole = $_SESSION['user_role']; // Rôle de l'utilisateur connecté

// Message pour les feedbacks d'actions
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $description = $_POST['description'] ?? ''; // La description peut être facultative
    $prixVente = $_POST['prixVente'];

    if ($_POST['form_action'] == 'add' && in_array($userRole, ['admin', 'ajout_article'])) {
        // Ajout d'un nouvel article
        $sql = "INSERT INTO Article (nom, description, prixVente) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$nom, $description, $prixVente])) {
            $message = "Article ajouté avec succès.";
        }
    } elseif ($_POST['form_action'] == 'edit' && in_array($userRole, ['admin'])) {
        // Mise à jour d'un article existant
        $articleId = $_POST['article_id']; // Assurez-vous de transmettre l'ID de l'article dans le formulaire
        $sql = "UPDATE Article SET nom = ?, description = ?, prixVente = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$nom, $description, $prixVente, $articleId])) {
            $message = "Article mis à jour avec succès.";
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id']) && $userRole == 'admin') {
    // Suppression d'un article
    $articleId = $_GET['id'];
    $sql = "DELETE FROM Article WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$articleId])) {
        $message = "Article supprimé avec succès.";
    }
}

// Récupérer tous les articles pour l'affichage
$sql = "SELECT * FROM Article";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$articles = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html>
<head>
    <title>...:: Gestion des Articles ::...</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php if (!empty($message)): ?>
    <p><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <input type="hidden" name="form_action" value="add" />
        <input type="hidden" name="article_id" value="" /> <!-- Pour l'édition -->
        <label for="nom">Nom de l'article:</label>
        <input type="text" id="nom" name="nom" required /><br />
        <label for="description">Description:</label>
        <textarea id="description" name="description"></textarea><br />
        <label for="prixVente">Prix de Vente:</label>
        <input type="number" id="prixVente" name="prixVente" step="0.01" required /><br />
        <button type="submit">Soumettre</button>
    </form>

    <h2>Liste des Articles</h2>
    <?php foreach ($articles as $article): ?>
    <div>
        <p><?php echo htmlspecialchars($article['nom']); ?></p>
        <p><?php echo htmlspecialchars($article['description']); ?></p>
        <p><?php echo htmlspecialchars($article['prixVente']); ?> €</p>
        <?php if ($userRole == 'admin'): ?>
        <a href="?action=edit&id=<?php echo $article['id']; ?>">Modifier</a>
        <a href="?action=delete&id=<?php echo $article['id']; ?>" onclick="return confirm('Confirmez-vous la suppression ?');">Supprimer</a>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</body>
</html>
