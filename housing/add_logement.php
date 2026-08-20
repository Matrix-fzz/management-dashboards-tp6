<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $n_logement = $_POST['n_logement'];
    $type_logement = $_POST['type_logement'];
    $id_quartier = $_POST['id_quartier'];
    $no_rue = $_POST['no_rue'];
    $rue = $_POST['rue'];
    $superficie = $_POST['superficie'];
    $loyer = $_POST['loyer'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO LOGEMENT (N_LOGEMENT, TYPE_LOGEMENT, ID_QUARTIER, NO_RUE, RUE, SUPERFICIE, LOYER) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$n_logement, $type_logement, $id_quartier, $no_rue, $rue, $superficie, $loyer]);
        
        header("Location: index.php?page=logements&success=1");
        exit();
    } catch(PDOException $e) {
        header("Location: index.php?page=add-logement&error=1");
        exit();
    }
}
?>