<?php
require 'config.php'; // Connexion à la base de données via PDO

$action = $_GET['action'] ?? 'list'; // Action : ajout, édition, suppression, liste
$userId = $_GET['id'] ?? 0; // ID utilisateur pour éditer ou supprimer

// Traitement des actions (add, edit, delete)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password']; // Pensez à hasher le mot de passe
    $role = $_POST['role'];

    if ($_POST['action'] == 'add') {
        // Insertion
        $sql = "INSERT INTO Utilisateur (username, password, role) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT), $role]);
    } elseif ($_POST['action'] == 'edit' && $userId > 0) {
        // Mise à jour
        $sql = "UPDATE Utilisateur SET username = ?, role = ?".(!empty($password) ? ", password = '".password_hash($password, PASSWORD_DEFAULT)."'" : "")." WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, $role, $userId]);
    }
    // Redirection pour éviter les soumissions de formulaire en double
    header('Location: manage_users.php');
    exit;
}

if ($action == 'delete' && $userId > 0) {
    // Suppression
    $sql = "DELETE FROM Utilisateur WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    header('Location: manage_users.php');
    exit;
}

// Préparation du formulaire pour 'edit'
$userToEdit = null;
if ($action == 'edit' && $userId > 0) {
    $sql = "SELECT * FROM Utilisateur WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    $userToEdit = $stmt->fetch();
}

// Affichage du formulaire (ajout et édition)
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Utilisateurs</title>
    <!-- Styles CSS -->
</head>
<body>
<h2><?php echo $action == 'edit' ? 'Modifier' : 'Ajouter'; ?> un utilisateur</h2>
<form method="post" action="manage_users.php">
    <input type="hidden" name="action" value="<?php echo $action == 'edit' ? 'edit' : 'add'; ?>">
    <?php if ($action == 'edit'): ?>
        <input type="hidden" name="id" value="<?php echo $userId; ?>">
    <?php endif; ?>
    <label>Nom d'utilisateur:</label>
    <input type="text" name="username" value="<?php echo $userToEdit['username'] ?? ''; ?>" required><br>
    <label>Mot de passe:</label>
    <input type="password" name="password"><br>
    <label>Rôle:</label>
    <select name="role">
        <option value="admin" <?php if (isset($userToEdit) && $userToEdit['role'] == 'admin') echo 'selected'; ?>>Admin</option>
        <option value="ajout_article" <?php if (isset($userToEdit) && $userToEdit['role'] == 'ajout_article') echo 'selected'; ?>>Ajout d'articles</option>
        <option value="sortie_stock" <?php if (isset($userToEdit) && $userToEdit['role'] == 'sortie_stock') echo 'selected'; ?>>Sortie de stock</option>
    </select><br>
    <button type="submit"><?php echo $action == 'edit' ? 'Modifier' : 'Ajouter'; ?></button>
</form>

<h2>Liste des utilisateurs</h2>
<table>
    <tr>
        <th>Nom d'utilisateur</th>
        <th>Rôle</th>
        <th>Actions</th>
    </tr>
    <?php
    $users = $pdo->query("SELECT * FROM Utilisateur")->fetchAll();
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($user['username']) . "</td>";
        echo "<td>" . htmlspecialchars($user['role']) . "</td>";
        echo "<td>
                <a href='?action=edit&id=" . $user['id'] . "'>Modifier</a>
                <a href='?action=delete&id=" . $user['id'] . "' onclick='return confirm(\"Confirmer la suppression?\");'>Supprimer</a>
              </td>";
        echo "</tr>";
    }
    ?>
</table>
</body>
</html>
