<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Database configuration
$host = "localhost";
$dbname = "accounting_management";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(["error" => "Database connection failed: " . $e->getMessage()]);
    exit;
}

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Get endpoint from URL
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$table = preg_replace('/[^a-z0-9_]+/i', '', array_shift($request));
$key = array_shift($request);

switch ($method) {
    case 'GET':
        if ($table == 'comptables') {
            getComptables($pdo, $key);
        } elseif ($table == 'exploitations') {
            getExploitations($pdo, $key);
        } elseif ($table == 'communes') {
            getCommunes($pdo);
        } elseif ($table == 'agences') {
            getAgences($pdo);
        } elseif ($table == 'stats') {
            getStats($pdo);
        }
        break;
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        if ($table == 'comptables') {
            createComptable($pdo, $input);
        } elseif ($table == 'exploitations') {
            createExploitation($pdo, $input);
        }
        break;
    case 'PUT':
        $input = json_decode(file_get_contents('php://input'), true);
        if ($table == 'comptables' && $key) {
            updateComptable($pdo, $key, $input);
        } elseif ($table == 'exploitations' && $key) {
            updateExploitation($pdo, $key, $input);
        }
        break;
    case 'DELETE':
        if ($table == 'comptables' && $key) {
            deleteComptable($pdo, $key);
        } elseif ($table == 'exploitations' && $key) {
            deleteExploitation($pdo, $key);
        }
        break;
}

// Functions for Comptables
function getComptables($pdo, $id = null) {
    if ($id) {
        $stmt = $pdo->prepare("
            SELECT c.*, co.nom_commune, a.nom_agence 
            FROM comptables c 
            LEFT JOIN communes co ON c.id_commune = co.id_commune 
            LEFT JOIN agences a ON c.id_agence = a.id_agence 
            WHERE c.id_comptable = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $stmt = $pdo->query("
            SELECT c.*, co.nom_commune, a.nom_agence 
            FROM comptables c 
            LEFT JOIN communes co ON c.id_commune = co.id_commune 
            LEFT JOIN agences a ON c.id_agence = a.id_agence 
            ORDER BY c.id_comptable
        ");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    echo json_encode($result);
}

function createComptable($pdo, $data) {
    $stmt = $pdo->prepare("
        INSERT INTO comptables (id_comptable, nom_comptable, date_naissance, no_tel, id_commune, id_agence) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    try {
        $stmt->execute([
            $data['id_comptable'],
            $data['nom_comptable'],
            $data['date_naissance'],
            $data['no_tel'],
            $data['id_commune'],
            $data['id_agence']
        ]);
        echo json_encode(["success" => "Comptable created successfully"]);
    } catch(PDOException $e) {
        echo json_encode(["error" => "Failed to create comptable: " . $e->getMessage()]);
    }
}

function updateComptable($pdo, $id, $data) {
    $stmt = $pdo->prepare("
        UPDATE comptables 
        SET nom_comptable = ?, date_naissance = ?, no_tel = ?, id_commune = ?, id_agence = ? 
        WHERE id_comptable = ?
    ");
    try {
        $stmt->execute([
            $data['nom_comptable'],
            $data['date_naissance'],
            $data['no_tel'],
            $data['id_commune'],
            $data['id_agence'],
            $id
        ]);
        echo json_encode(["success" => "Comptable updated successfully"]);
    } catch(PDOException $e) {
        echo json_encode(["error" => "Failed to update comptable: " . $e->getMessage()]);
    }
}

function deleteComptable($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM comptables WHERE id_comptable = ?");
    try {
        $stmt->execute([$id]);
        echo json_encode(["success" => "Comptable deleted successfully"]);
    } catch(PDOException $e) {
        echo json_encode(["error" => "Failed to delete comptable: " . $e->getMessage()]);
    }
}

// Functions for Exploitations
function getExploitations($pdo, $id = null) {
    if ($id) {
        $stmt = $pdo->prepare("
            SELECT e.*, c.nom_commune 
            FROM exploitations e 
            LEFT JOIN communes c ON e.id_commune = c.id_commune 
            WHERE e.id_exploitation = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $stmt = $pdo->query("
            SELECT e.*, c.nom_commune 
            FROM exploitations e 
            LEFT JOIN communes c ON e.id_commune = c.id_commune 
            ORDER BY e.id_exploitation
        ");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    echo json_encode($result);
}

function createExploitation($pdo, $data) {
    $stmt = $pdo->prepare("
        INSERT INTO exploitations (id_exploitation, nom_exploitation, sau, id_commune) 
        VALUES (?, ?, ?, ?)
    ");
    try {
        $stmt->execute([
            $data['id_exploitation'],
            $data['nom_exploitation'],
            $data['sau'],
            $data['id_commune']
        ]);
        echo json_encode(["success" => "Exploitation created successfully"]);
    } catch(PDOException $e) {
        echo json_encode(["error" => "Failed to create exploitation: " . $e->getMessage()]);
    }
}

function updateExploitation($pdo, $id, $data) {
    $stmt = $pdo->prepare("
        UPDATE exploitations 
        SET nom_exploitation = ?, sau = ?, id_commune = ? 
        WHERE id_exploitation = ?
    ");
    try {
        $stmt->execute([
            $data['nom_exploitation'],
            $data['sau'],
            $data['id_commune'],
            $id
        ]);
        echo json_encode(["success" => "Exploitation updated successfully"]);
    } catch(PDOException $e) {
        echo json_encode(["error" => "Failed to update exploitation: " . $e->getMessage()]);
    }
}

function deleteExploitation($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM exploitations WHERE id_exploitation = ?");
    try {
        $stmt->execute([$id]);
        echo json_encode(["success" => "Exploitation deleted successfully"]);
    } catch(PDOException $e) {
        echo json_encode(["error" => "Failed to delete exploitation: " . $e->getMessage()]);
    }
}

// Functions for Communes and Agences
function getCommunes($pdo) {
    $stmt = $pdo->query("SELECT * FROM communes ORDER BY nom_commune");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
}

function getAgences($pdo) {
    $stmt = $pdo->query("SELECT * FROM agences ORDER BY id_agence");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
}

// Function for Statistics
function getStats($pdo) {
    $stats = [];
    
    // Count comptables
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM comptables");
    $stats['comptables'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Count exploitations
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM exploitations");
    $stats['exploitations'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Count communes
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM communes");
    $stats['communes'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Count agences
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM agences");
    $stats['agences'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Comptables by commune
    $stmt = $pdo->query("
        SELECT c.nom_commune, COUNT(co.id_comptable) as count 
        FROM communes c 
        LEFT JOIN comptables co ON c.id_commune = co.id_commune 
        GROUP BY c.id_commune 
        ORDER BY count DESC
    ");
    $stats['comptables_by_commune'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($stats);
}
?>