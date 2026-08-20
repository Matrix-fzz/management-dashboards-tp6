<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_quartier = $_POST['id_quartier'];
    $libelle_quartier = $_POST['libelle_quartier'];
    $id_commune = $_POST['id_commune'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO QUARTIER (ID_QUARTIER, LIBELLE_QUARTIER, ID_COMMUNE) VALUES (?, ?, ?)");
        $stmt->execute([$id_quartier, $libelle_quartier, $id_commune]);
        
        header("Location: index.php?page=quartiers&success=1");
        exit();
    } catch(PDOException $e) {
        header("Location: index.php?page=add-quartier&error=1");
        exit();
    }
}
?>