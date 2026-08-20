<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type_logement = $_POST['type_logement'];
    $charges_forfaitaires = $_POST['charges_forfaitaires'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO TYPE_DE_LOGEMENT (TYPE_LOGEMENT, CHARGES_FORFAITAIRES) VALUES (?, ?)");
        $stmt->execute([$type_logement, $charges_forfaitaires]);
        
        header("Location: index.php?page=types-logement&success=1");
        exit();
    } catch(PDOException $e) {
        header("Location: index.php?page=types-logement&error=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter Type de Logement</title>
    <style>
        /* Same styles as index.php */
        :root {
            --primary: #3498db;
            --secondary: #2c3e50;
            --success: #2ecc71;
            --light: #ecf0f1;
            --dark: #34495e;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            padding: 20px;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            color: var(--secondary);
            margin-bottom: 20px;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: var(--secondary);
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            margin-right: 10px;
        }
        
        .btn-success {
            background-color: var(--success);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Ajouter un Type de Logement</h1>
        <form method="POST">
            <div class="form-group">
                <label for="type_logement">Type de Logement</label>
                <input type="text" id="type_logement" name="type_logement" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="charges_forfaitaires">Charges Forfaitaires (€)</label>
                <input type="number" id="charges_forfaitaires" name="charges_forfaitaires" class="form-control" step="0.01" required>
            </div>
            
            <button type="submit" class="btn btn-success">Enregistrer</button>
            <a href="index.php?page=types-logement" class="btn">Annuler</a>
        </form>
    </div>
</body>
</html>