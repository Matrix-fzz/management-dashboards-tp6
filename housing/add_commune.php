<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_commune = $_POST['id_commune'];
    $nom_commune = $_POST['nom_commune'];
    $distance_agence = $_POST['distance_agence'];
    $nombre_habitants = $_POST['nombre_habitants'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO COMMUNE (ID_COMMUNE, NOM_COMMUNE, DISTANCE_AGENCE, NOMBRE_D_HABITANTS) VALUES (?, ?, ?, ?)");
        $stmt->execute([$id_commune, $nom_commune, $distance_agence, $nombre_habitants]);
        
        header("Location: index.php?page=communes&success=1");
        exit();
    } catch(PDOException $e) {
        header("Location: index.php?page=add-commune&error=1");
        exit();
    }
}
?>