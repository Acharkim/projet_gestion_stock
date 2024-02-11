<?php
$host = 'localhost';
$db   = 'gestion_stock';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass, $options);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db`");
    $pdo->exec("use `$db`");

    // Création de la table Utilisateur
    $sqlUtilisateur = "CREATE TABLE IF NOT EXISTS Utilisateur (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'ajout_article', 'sortie_stock') NOT NULL
    )";
    $pdo->exec($sqlUtilisateur);

    // Création de la table Article
    $sqlArticle = "CREATE TABLE IF NOT EXISTS Article (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(255) NOT NULL,
        description TEXT,
        prixVente DECIMAL(10, 2) NOT NULL
    )";
    $pdo->exec($sqlArticle);

    // Création de la table BonDeStock
    $sqlBonDeStock = "CREATE TABLE IF NOT EXISTS BonDeStock (
        id INT AUTO_INCREMENT PRIMARY KEY,
        date DATE NOT NULL,
        utilisateurId INT,
        FOREIGN KEY (utilisateurId) REFERENCES Utilisateur(id) ON DELETE CASCADE
    )";
    $pdo->exec($sqlBonDeStock);

    // Création de la table Detail_BonDeStock
    $sqlDetail_BonDeStock = "CREATE TABLE IF NOT EXISTS Detail_BonDeStock (
        id INT AUTO_INCREMENT PRIMARY KEY,
        bonDeStockId INT,
        articleId INT,
        quantite INT NOT NULL,
        sens INT NOT NULL CHECK (sens IN (-1, 1)),
        FOREIGN KEY (bonDeStockId) REFERENCES BonDeStock(id) ON DELETE CASCADE,
        FOREIGN KEY (articleId) REFERENCES Article(id) ON DELETE CASCADE
    )";
    $pdo->exec($sqlDetail_BonDeStock);

    echo "Toutes les tables ont été créées avec succès.";
} catch (PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
