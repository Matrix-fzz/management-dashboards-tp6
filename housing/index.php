<?php
// index.php
require_once 'config.php';
session_start();

// Get dashboard statistics
$stats = [];
try {
    // Total logements
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM LOGEMENT");
    $stats['total_logements'] = $stmt->fetch()['total'];
    
    // Total quartiers
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM QUARTIER");
    $stats['total_quartiers'] = $stmt->fetch()['total'];
    
    // Total communes
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM COMMUNE");
    $stats['total_communes'] = $stmt->fetch()['total'];
    
    // Average loyer
    $stmt = $pdo->query("SELECT AVG(LOYER) as avg FROM LOGEMENT");
    $stats['avg_loyer'] = number_format($stmt->fetch()['avg'], 2);
    
    // Recent logements
    $stmt = $pdo->query("
        SELECT l.*, t.TYPE_LOGEMENT, q.LIBELLE_QUARTIER 
        FROM LOGEMENT l 
        LEFT JOIN TYPE_DE_LOGEMENT t ON l.TYPE_LOGEMENT = t.ID_TYPE 
        LEFT JOIN QUARTIER q ON l.ID_QUARTIER = q.ID_QUARTIER 
        ORDER BY l.N_LOGEMENT DESC LIMIT 5
    ");
    $recent_logements = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système de Gestion de Logements</title>
    <style>
        :root {
            --primary: #3498db;
            --secondary: #2c3e50;
            --success: #2ecc71;
            --danger: #e74c3c;
            --warning: #f39c12;
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
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            color: white;
            padding: 20px 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.8rem;
            font-weight: bold;
        }
        
        nav ul {
            display: flex;
            list-style: none;
        }
        
        nav ul li {
            margin-left: 20px;
        }
        
        nav ul li a {
            color: white;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        nav ul li a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        .main-content {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 20px;
            margin-top: 20px;
        }
        
        .sidebar {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        
        .sidebar h3 {
            margin-bottom: 15px;
            color: var(--secondary);
            border-bottom: 2px solid var(--light);
            padding-bottom: 10px;
        }
        
        .sidebar ul {
            list-style: none;
        }
        
        .sidebar ul li {
            margin-bottom: 10px;
        }
        
        .sidebar ul li a {
            color: var(--dark);
            text-decoration: none;
            display: block;
            padding: 8px 12px;
            border-radius: 4px;
            transition: all 0.3s;
        }
        
        .sidebar ul li a:hover, .sidebar ul li a.active {
            background-color: var(--primary);
            color: white;
        }
        
        .content {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        
        .page-title {
            margin-bottom: 20px;
            color: var(--secondary);
            border-bottom: 2px solid var(--light);
            padding-bottom: 10px;
        }
        
        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .card-title {
            color: var(--secondary);
            font-size: 1.2rem;
        }
        
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #2980b9;
        }
        
        .btn-success {
            background-color: var(--success);
        }
        
        .btn-success:hover {
            background-color: #27ae60;
        }
        
        .btn-danger {
            background-color: var(--danger);
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        table th, table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        table th {
            background-color: var(--light);
            color: var(--secondary);
        }
        
        table tr:hover {
            background-color: rgba(52, 152, 219, 0.1);
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
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
        
        .form-control:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary);
            margin: 10px 0;
        }
        
        .stat-label {
            color: var(--dark);
            font-size: 0.9rem;
        }
        
        footer {
            background-color: var(--secondary);
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: 40px;
        }
        
        .hidden {
            display: none;
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        
        .alert-success {
            background-color: rgba(46, 204, 113, 0.2);
            color: #27ae60;
            border: 1px solid #2ecc71;
        }
        
        .alert-danger {
            background-color: rgba(231, 76, 60, 0.2);
            color: #c0392b;
            border: 1px solid #e74c3c;
        }
        
        @media (max-width: 768px) {
            .main-content {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                display: none;
            }
            
            .header-content {
                flex-direction: column;
                text-align: center;
            }
            
            nav ul {
                margin-top: 15px;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">Gestion de Logements</div>
                <nav>
                    <ul>
                        <li><a href="#" class="nav-link" data-page="dashboard">Tableau de Bord</a></li>
                        <li><a href="#" class="nav-link" data-page="logements">Logements</a></li>
                        <li><a href="#" class="nav-link" data-page="quartiers">Quartiers</a></li>
                        <li><a href="#" class="nav-link" data-page="communes">Communes</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
    
    <div class="container">
        <div class="main-content">
            <aside class="sidebar">
                <h3>Navigation</h3>
                <ul>
                    <li><a href="#" class="nav-link active" data-page="dashboard">Tableau de Bord</a></li>
                    <li><a href="#" class="nav-link" data-page="logements">Logements</a></li>
                    <li><a href="#" class="nav-link" data-page="types-logement">Types de Logement</a></li>
                    <li><a href="#" class="nav-link" data-page="quartiers">Quartiers</a></li>
                    <li><a href="#" class="nav-link" data-page="communes">Communes</a></li>
                    <li><a href="#" class="nav-link" data-page="telephones">Téléphones</a></li>
                </ul>
                
                <h3>Actions Rapides</h3>
                <ul>
                    <li><a href="#" class="nav-link" data-page="add-logement">Ajouter Logement</a></li>
                    <li><a href="#" class="nav-link" data-page="add-quartier">Ajouter Quartier</a></li>
                    <li><a href="#" class="nav-link" data-page="add-commune">Ajouter Commune</a></li>
                </ul>
            </aside>
            
            <main class="content">
                <!-- Dashboard Page -->
                <div id="dashboard" class="page">
                    <h2 class="page-title">Tableau de Bord</h2>
                    
                    <div class="stats">
                        <div class="stat-card">
                            <div class="stat-label">Total Logements</div>
                            <div class="stat-value"><?php echo $stats['total_logements']; ?></div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Quartiers</div>
                            <div class="stat-value"><?php echo $stats['total_quartiers']; ?></div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Communes</div>
                            <div class="stat-value"><?php echo $stats['total_communes']; ?></div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Loyer Moyen</div>
                            <div class="stat-value"><?php echo $stats['avg_loyer']; ?> €</div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Logements Récemment Ajoutés</h3>
                            <a href="#" class="btn nav-link" data-page="logements">Voir Tout</a>
                        </div>
                        <div id="recent-logements">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Adresse</th>
                                        <th>Type</th>
                                        <th>Loyer</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($recent_logements as $logement): ?>
                                    <tr>
                                        <td><?php echo $logement['N_LOGEMENT']; ?></td>
                                        <td><?php echo $logement['NO_RUE'] . ' ' . $logement['RUE']; ?></td>
                                        <td><?php echo $logement['TYPE_LOGEMENT']; ?></td>
                                        <td><?php echo $logement['LOYER']; ?> €</td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Logements Page -->
                <div id="logements" class="page hidden">
                    <h2 class="page-title">Gestion des Logements</h2>
                    
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Liste des Logements</h3>
                            <a href="#" class="btn nav-link" data-page="add-logement">Ajouter Logement</a>
                        </div>
                        <div id="logements-list">
                            <?php
                            try {
                                $stmt = $pdo->query("
                                    SELECT l.*, t.TYPE_LOGEMENT, q.LIBELLE_QUARTIER, c.NOM_COMMUNE 
                                    FROM LOGEMENT l 
                                    LEFT JOIN TYPE_DE_LOGEMENT t ON l.TYPE_LOGEMENT = t.ID_TYPE 
                                    LEFT JOIN QUARTIER q ON l.ID_QUARTIER = q.ID_QUARTIER 
                                    LEFT JOIN COMMUNE c ON q.ID_COMMUNE = c.ID_COMMUNE
                                    ORDER BY l.N_LOGEMENT
                                ");
                                $logements = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            } catch(PDOException $e) {
                                die("Error fetching logements: " . $e->getMessage());
                            }
                            ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Adresse</th>
                                        <th>Type</th>
                                        <th>Quartier</th>
                                        <th>Commune</th>
                                        <th>Superficie</th>
                                        <th>Loyer</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($logements as $logement): ?>
                                    <tr>
                                        <td><?php echo $logement['N_LOGEMENT']; ?></td>
                                        <td><?php echo $logement['NO_RUE'] . ' ' . $logement['RUE']; ?></td>
                                        <td><?php echo $logement['TYPE_LOGEMENT']; ?></td>
                                        <td><?php echo $logement['LIBELLE_QUARTIER']; ?></td>
                                        <td><?php echo $logement['NOM_COMMUNE']; ?></td>
                                        <td><?php echo $logement['SUPERFICIE']; ?> m²</td>
                                        <td><?php echo $logement['LOYER']; ?> €</td>
                                        <td>
                                            <a href="edit_logement.php?id=<?php echo $logement['N_LOGEMENT']; ?>" class="btn">Modifier</a>
                                            <a href="delete_logement.php?id=<?php echo $logement['N_LOGEMENT']; ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr?')">Supprimer</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Add Logement Page -->
                <div id="add-logement" class="page hidden">
                    <h2 class="page-title">Ajouter un Logement</h2>
                    
                    <div class="card">
                        <form action="add_logement.php" method="POST">
                            <div class="form-group">
                                <label for="logement-numero">Numéro de Logement</label>
                                <input type="number" id="logement-numero" name="n_logement" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="logement-type">Type de Logement</label>
                                <select id="logement-type" name="type_logement" class="form-control" required>
                                    <option value="">Sélectionner un type</option>
                                    <?php
                                    $stmt = $pdo->query("SELECT * FROM TYPE_DE_LOGEMENT");
                                    $types = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    foreach($types as $type): ?>
                                    <option value="<?php echo $type['ID_TYPE']; ?>"><?php echo $type['TYPE_LOGEMENT']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="logement-quartier">Quartier</label>
                                <select id="logement-quartier" name="id_quartier" class="form-control" required>
                                    <option value="">Sélectionner un quartier</option>
                                    <?php
                                    $stmt = $pdo->query("SELECT * FROM QUARTIER");
                                    $quartiers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    foreach($quartiers as $quartier): ?>
                                    <option value="<?php echo $quartier['ID_QUARTIER']; ?>"><?php echo $quartier['LIBELLE_QUARTIER']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="logement-rue">Rue</label>
                                <input type="text" id="logement-rue" name="rue" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="logement-no-rue">Numéro de Rue</label>
                                <input type="text" id="logement-no-rue" name="no_rue" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="logement-superficie">Superficie (m²)</label>
                                <input type="number" id="logement-superficie" name="superficie" class="form-control" step="0.01" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="logement-loyer">Loyer (€)</label>
                                <input type="number" id="logement-loyer" name="loyer" class="form-control" step="0.01" required>
                            </div>
                            
                            <button type="submit" class="btn btn-success">Enregistrer</button>
                            <a href="#" class="btn nav-link" data-page="logements">Annuler</a>
                        </form>
                    </div>
                </div>
                
                <!-- Quartiers Page -->
                <div id="quartiers" class="page hidden">
                    <h2 class="page-title">Gestion des Quartiers</h2>
                    
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Liste des Quartiers</h3>
                            <a href="#" class="btn nav-link" data-page="add-quartier">Ajouter Quartier</a>
                        </div>
                        <div id="quartiers-list">
                            <?php
                            try {
                                $stmt = $pdo->query("
                                    SELECT q.*, c.NOM_COMMUNE 
                                    FROM QUARTIER q 
                                    LEFT JOIN COMMUNE c ON q.ID_COMMUNE = c.ID_COMMUNE
                                    ORDER BY q.ID_QUARTIER
                                ");
                                $quartiers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            } catch(PDOException $e) {
                                die("Error fetching quartiers: " . $e->getMessage());
                            }
                            ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Libellé</th>
                                        <th>Commune</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($quartiers as $quartier): ?>
                                    <tr>
                                        <td><?php echo $quartier['ID_QUARTIER']; ?></td>
                                        <td><?php echo $quartier['LIBELLE_QUARTIER']; ?></td>
                                        <td><?php echo $quartier['NOM_COMMUNE']; ?></td>
                                        <td>
                                            <a href="edit_quartier.php?id=<?php echo $quartier['ID_QUARTIER']; ?>" class="btn">Modifier</a>
                                            <a href="delete_quartier.php?id=<?php echo $quartier['ID_QUARTIER']; ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr?')">Supprimer</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Add Quartier Page -->
                <div id="add-quartier" class="page hidden">
                    <h2 class="page-title">Ajouter un Quartier</h2>
                    
                    <div class="card">
                        <form action="add_quartier.php" method="POST">
                            <div class="form-group">
                                <label for="quartier-id">ID Quartier</label>
                                <input type="number" id="quartier-id" name="id_quartier" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="quartier-libelle">Libellé Quartier</label>
                                <input type="text" id="quartier-libelle" name="libelle_quartier" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="quartier-commune">Commune</label>
                                <select id="quartier-commune" name="id_commune" class="form-control" required>
                                    <option value="">Sélectionner une commune</option>
                                    <?php
                                    $stmt = $pdo->query("SELECT * FROM COMMUNE");
                                    $communes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    foreach($communes as $commune): ?>
                                    <option value="<?php echo $commune['ID_COMMUNE']; ?>"><?php echo $commune['NOM_COMMUNE']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-success">Enregistrer</button>
                            <a href="#" class="btn nav-link" data-page="quartiers">Annuler</a>
                        </form>
                    </div>
                </div>
                
                <!-- Communes Page -->
                <div id="communes" class="page hidden">
                    <h2 class="page-title">Gestion des Communes</h2>
                    
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Liste des Communes</h3>
                            <a href="#" class="btn nav-link" data-page="add-commune">Ajouter Commune</a>
                        </div>
                        <div id="communes-list">
                            <?php
                            try {
                                $stmt = $pdo->query("SELECT * FROM COMMUNE ORDER BY ID_COMMUNE");
                                $communes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            } catch(PDOException $e) {
                                die("Error fetching communes: " . $e->getMessage());
                            }
                            ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nom</th>
                                        <th>Distance Agence</th>
                                        <th>Habitants</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($communes as $commune): ?>
                                    <tr>
                                        <td><?php echo $commune['ID_COMMUNE']; ?></td>
                                        <td><?php echo $commune['NOM_COMMUNE']; ?></td>
                                        <td><?php echo $commune['DISTANCE_AGENCE']; ?> km</td>
                                        <td><?php echo $commune['NOMBRE_D_HABITANTS']; ?></td>
                                        <td>
                                            <a href="edit_commune.php?id=<?php echo $commune['ID_COMMUNE']; ?>" class="btn">Modifier</a>
                                            <a href="delete_commune.php?id=<?php echo $commune['ID_COMMUNE']; ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr?')">Supprimer</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Add Commune Page -->
                <div id="add-commune" class="page hidden">
                    <h2 class="page-title">Ajouter une Commune</h2>
                    
                    <div class="card">
                        <form action="add_commune.php" method="POST">
                            <div class="form-group">
                                <label for="commune-id">ID Commune</label>
                                <input type="number" id="commune-id" name="id_commune" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="commune-nom">Nom Commune</label>
                                <input type="text" id="commune-nom" name="nom_commune" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="commune-distance">Distance Agence (km)</label>
                                <input type="number" id="commune-distance" name="distance_agence" class="form-control" step="0.01" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="commune-habitants">Nombre d'Habitants</label>
                                <input type="number" id="commune-habitants" name="nombre_habitants" class="form-control" required>
                            </div>
                            
                            <button type="submit" class="btn btn-success">Enregistrer</button>
                            <a href="#" class="btn nav-link" data-page="communes">Annuler</a>
                        </form>
                    </div>
                </div>
                
                <!-- Types de Logement Page -->
                <div id="types-logement" class="page hidden">
                    <h2 class="page-title">Types de Logement</h2>
                    
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Liste des Types de Logement</h3>
                            <a href="add_type.php" class="btn">Ajouter Type</a>
                        </div>
                        <div id="types-list">
                            <?php
                            try {
                                $stmt = $pdo->query("SELECT * FROM TYPE_DE_LOGEMENT ORDER BY ID_TYPE");
                                $types = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            } catch(PDOException $e) {
                                die("Error fetching types: " . $e->getMessage());
                            }
                            ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Type</th>
                                        <th>Charges Forfaitaires</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($types as $type): ?>
                                    <tr>
                                        <td><?php echo $type['ID_TYPE']; ?></td>
                                        <td><?php echo $type['TYPE_LOGEMENT']; ?></td>
                                        <td><?php echo $type['CHARGES_FORFAITAIRES']; ?> €</td>
                                        <td>
                                            <a href="edit_type.php?id=<?php echo $type['ID_TYPE']; ?>" class="btn">Modifier</a>
                                            <a href="delete_type.php?id=<?php echo $type['ID_TYPE']; ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr?')">Supprimer</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Téléphones Page -->
                <div id="telephones" class="page hidden">
                    <h2 class="page-title">Gestion des Téléphones</h2>
                    
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Liste des Téléphones</h3>
                            <a href="add_telephone.php" class="btn">Ajouter Téléphone</a>
                        </div>
                        <div id="telephones-list">
                            <?php
                            try {
                                $stmt = $pdo->query("
                                    SELECT t.*, l.NO_RUE, l.RUE 
                                    FROM TELEPHONE t 
                                    LEFT JOIN LOGEMENT l ON t.N_LOGEMENT = l.N_LOGEMENT
                                    ORDER BY t.ID_TELEPHONE
                                ");
                                $telephones = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            } catch(PDOException $e) {
                                die("Error fetching telephones: " . $e->getMessage());
                            }
                            ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Numéro</th>
                                        <th>Logement</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($telephones as $telephone): ?>
                                    <tr>
                                        <td><?php echo $telephone['ID_TELEPHONE']; ?></td>
                                        <td><?php echo $telephone['NUMERO_TELEPHONE']; ?></td>
                                        <td><?php echo $telephone['NO_RUE'] . ' ' . $telephone['RUE']; ?></td>
                                        <td>
                                            <a href="edit_telephone.php?id=<?php echo $telephone['ID_TELEPHONE']; ?>" class="btn">Modifier</a>
                                            <a href="delete_telephone.php?id=<?php echo $telephone['ID_TELEPHONE']; ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr?')">Supprimer</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <footer>
        <div class="container">
            <p>&copy; 2023 Système de Gestion de Logements. Tous droits réservés.</p>
        </div>
    </footer>

    <script>
        // Navigation functionality
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const pageId = this.getAttribute('data-page');
                showPage(pageId);
                
                // Update active state in sidebar
                document.querySelectorAll('.sidebar a').forEach(a => {
                    a.classList.remove('active');
                });
                this.classList.add('active');
            });
        });
        
        function showPage(pageId) {
            // Hide all pages
            document.querySelectorAll('.page').forEach(page => {
                page.classList.add('hidden');
            });
            
            // Show selected page
            document.getElementById(pageId).classList.remove('hidden');
        }
        
        // Initialize the dashboard
        document.addEventListener('DOMContentLoaded', function() {
            showPage('dashboard');
        });
    </script>
</body>
</html>