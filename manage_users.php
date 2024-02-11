<?php
require 'config.php'; // accès à la base de données

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $role = $_POST['role'];
    $password = $_POST['password']; // Le mot de passe fourni par l'utilisateur
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hashage du mot de passe

    if ($_POST['form_action'] == 'add') {
        // Ajout d'un nouvel utilisateur
        $sql = "INSERT INTO Utilisateur (username, password, role) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, $hashedPassword, $role]);
    } elseif ($_POST['form_action'] == 'edit') {
        // Mise à jour d'un utilisateur existant
        $userId = $_POST['user_id']; // Assurez-vous que cet ID est bien passé via le formulaire
        if (!empty($password)) {
            // Si un nouveau mot de passe est fourni, le mettre à jour
            $sql = "UPDATE Utilisateur SET username = ?, password = ?, role = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$username, $hashedPassword, $role, $userId]);
        } else {
            // Si aucun mot de passe n'est fourni, ne pas mettre à jour le mot de passe
            $sql = "UPDATE Utilisateur SET username = ?, role = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$username, $role, $userId]);
        }
    }
    
    // Redirection pour éviter la soumission multiple du formulaire
    header('Location: manage_users.php');
    exit;
}

// Formulaire HTML (simplifié pour l'exemple)
?>
<!DOCTYPE html>
<html>
<head>
    <title>Gestion des Utilisateurs</title>
</head>
<body>
    <form method="post" action="">
        <input type="hidden" name="form_action" value="add" /> <!-- Changez la valeur en "edit" pour la mise à jour -->
        <input type="hidden" name="user_id" value="" /> <!-- L'ID de l'utilisateur pour l'édition -->
        <label for="username">Nom d'utilisateur:</label>
        <input type="text" id="username" name="username" required /><br />
        <label for="password">Mot de passe:</label>
        <input type="password" id="password" name="password" /><br />
        <label for="role">Rôle:</label>
        <select id="role" name="role">
            <option value="admin">Admin</option>
            <option value="user">Utilisateur</option>
        </select><br />
        <button type="submit">Soumettre</button>
    </form>
</body>
</html>
