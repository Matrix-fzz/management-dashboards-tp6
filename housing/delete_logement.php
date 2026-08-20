<?php
require_once 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        // First delete related telephones
        $stmt = $pdo->prepare("DELETE FROM TELEPHONE WHERE N_LOGEMENT = ?");
        $stmt->execute([$id]);
        
        // Then delete the logement
        $stmt = $pdo->prepare("DELETE FROM LOGEMENT WHERE N_LOGEMENT = ?");
        $stmt->execute([$id]);
        
        header("Location: index.php?page=logements&success=1");
        exit();
    } catch(PDOException $e) {
        header("Location: index.php?page=logements&error=1");
        exit();
    }
}
?>