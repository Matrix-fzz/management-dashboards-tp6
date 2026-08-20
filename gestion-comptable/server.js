const express = require('express');
const mysql = require('mysql2');
const cors = require('cors');
const path = require('path');

const app = express();
const port = 3000;

// Middleware
app.use(cors());
app.use(express.json());
app.use(express.static('public'));

// MySQL connection
const db = mysql.createConnection({
    host: 'localhost',
    user: 'root', // Change to your MySQL username
    password: '', // Change to your MySQL password
    database: 'accounting_db',
    multipleStatements: true // Allow multiple statements
});

// Connect to MySQL
db.connect((err) => {
    if (err) {
        console.error('Database connection failed: ' + err.stack);
        return;
    }
    console.log('Connected to MySQL database');
    createTables();
});

// Create tables one by one
function createTables() {
    const tables = [
        `CREATE TABLE IF NOT EXISTS agences (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(255) NOT NULL,
            ville VARCHAR(255) NOT NULL,
            directeur VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )`,
        
        `CREATE TABLE IF NOT EXISTS comptables (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(255) NOT NULL,
            age INT NOT NULL,
            telephone VARCHAR(20) NOT NULL,
            agence_id INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (agence_id) REFERENCES agences(id) ON DELETE SET NULL
        )`,
        
        `CREATE TABLE IF NOT EXISTS exploitations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(255) NOT NULL,
            commune VARCHAR(255) NOT NULL,
            sau INT NOT NULL COMMENT 'Surface Agricole Utile en hectares',
            comptable_id INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (comptable_id) REFERENCES comptables(id) ON DELETE SET NULL
        )`,
        
        `CREATE TABLE IF NOT EXISTS dossiers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(255) NOT NULL,
            date_creation DATE NOT NULL,
            comptable_id INT,
            agence_id INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (comptable_id) REFERENCES comptables(id) ON DELETE SET NULL,
            FOREIGN KEY (agence_id) REFERENCES agences(id) ON DELETE SET NULL
        )`
    ];

    let currentTable = 0;

    function createNextTable() {
        if (currentTable >= tables.length) {
            console.log('All tables created successfully');
            insertSampleData();
            return;
        }

        db.query(tables[currentTable], (err) => {
            if (err) {
                console.error(`Error creating table ${currentTable + 1}:`, err);
            } else {
                console.log(`Table ${currentTable + 1} created successfully`);
                currentTable++;
                createNextTable();
            }
        });
    }

    createNextTable();
}

function insertSampleData() {
    // Check if data already exists
    db.query('SELECT COUNT(*) as count FROM agences', (err, results) => {
        if (err) {
            console.error('Error checking existing data:', err);
            return;
        }
        
        if (results[0].count === 0) {
            console.log('Inserting sample data...');
            
            // Insert sample data in sequence to maintain foreign key relationships
            const insertAgences = `INSERT INTO agences (nom, ville, directeur) VALUES
                ('Agence Paris', 'Paris', 'Pierre Martin'),
                ('Agence Lyon', 'Lyon', 'Sophie Bernard'),
                ('Agence Marseille', 'Marseille', 'Jean Dupont')`;
            
            db.query(insertAgences, (err) => {
                if (err) {
                    console.error('Error inserting agences:', err);
                    return;
                }
                console.log('Agences inserted');
                
                const insertComptables = `INSERT INTO comptables (nom, age, telephone, agence_id) VALUES
                    ('Alice Durand', 32, '01 23 45 67 89', 1),
                    ('Bruno Lefebvre', 45, '02 34 56 78 90', 1),
                    ('Céline Moreau', 28, '03 45 67 89 01', 2),
                    ('David Petit', 39, '04 56 78 90 12', 3)`;
                
                db.query(insertComptables, (err) => {
                    if (err) {
                        console.error('Error inserting comptables:', err);
                        return;
                    }
                    console.log('Comptables inserted');
                    
                    const insertExploitations = `INSERT INTO exploitations (nom, commune, sau, comptable_id) VALUES
                        ('Ferme du Soleil', 'Chartres', 120, 1),
                        ('Domaine des Vignes', 'Reims', 85, 1),
                        ('Élevage Bovin Central', 'Clermont-Ferrand', 200, 2),
                        ('Culture Céréalière Sud', 'Toulouse', 350, 3),
                        ('Verger Provençal', 'Avignon', 65, 4)`;
                    
                    db.query(insertExploitations, (err) => {
                        if (err) {
                            console.error('Error inserting exploitations:', err);
                            return;
                        }
                        console.log('Exploitations inserted');
                        
                        const insertDossiers = `INSERT INTO dossiers (nom, date_creation, comptable_id, agence_id) VALUES
                            ('Dossier Fiscal 2023', '2023-01-15', 1, 1),
                            ('Comptes Annuels 2023', '2023-03-22', 2, 1),
                            ('Déclarations Sociales', '2023-05-10', 3, 2),
                            ('Bilan Patrimonial', '2023-07-18', 4, 3)`;
                        
                        db.query(insertDossiers, (err) => {
                            if (err) {
                                console.error('Error inserting dossiers:', err);
                                return;
                            }
                            console.log('Dossiers inserted');
                            console.log('Sample data inserted successfully');
                        });
                    });
                });
            });
        } else {
            console.log('Data already exists, skipping sample data insertion');
        }
    });
}

// API Routes

// Get all entities
app.get('/api/agences', (req, res) => {
    db.query('SELECT * FROM agences', (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else {
            res.json(results);
        }
    });
});

app.get('/api/comptables', (req, res) => {
    const query = `
        SELECT c.*, a.nom as agence_nom 
        FROM comptables c 
        LEFT JOIN agences a ON c.agence_id = a.id
    `;
    db.query(query, (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else {
            res.json(results);
        }
    });
});

app.get('/api/exploitations', (req, res) => {
    const query = `
        SELECT e.*, c.nom as comptable_nom 
        FROM exploitations e 
        LEFT JOIN comptables c ON e.comptable_id = c.id
    `;
    db.query(query, (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else {
            res.json(results);
        }
    });
});

app.get('/api/dossiers', (req, res) => {
    const query = `
        SELECT d.*, c.nom as comptable_nom, a.nom as agence_nom 
        FROM dossiers d 
        LEFT JOIN comptables c ON d.comptable_id = c.id 
        LEFT JOIN agences a ON d.agence_id = a.id
    `;
    db.query(query, (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else {
            res.json(results);
        }
    });
});

// Get single entity
app.get('/api/agences/:id', (req, res) => {
    db.query('SELECT * FROM agences WHERE id = ?', [req.params.id], (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else {
            res.json(results[0]);
        }
    });
});

app.get('/api/comptables/:id', (req, res) => {
    const query = `
        SELECT c.*, a.nom as agence_nom 
        FROM comptables c 
        LEFT JOIN agences a ON c.agence_id = a.id 
        WHERE c.id = ?
    `;
    db.query(query, [req.params.id], (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else {
            res.json(results[0]);
        }
    });
});

app.get('/api/exploitations/:id', (req, res) => {
    const query = `
        SELECT e.*, c.nom as comptable_nom 
        FROM exploitations e 
        LEFT JOIN comptables c ON e.comptable_id = c.id 
        WHERE e.id = ?
    `;
    db.query(query, [req.params.id], (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else {
            res.json(results[0]);
        }
    });
});

app.get('/api/dossiers/:id', (req, res) => {
    const query = `
        SELECT d.*, c.nom as comptable_nom, a.nom as agence_nom 
        FROM dossiers d 
        LEFT JOIN comptables c ON d.comptable_id = c.id 
        LEFT JOIN agences a ON d.agence_id = a.id 
        WHERE d.id = ?
    `;
    db.query(query, [req.params.id], (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else {
            res.json(results[0]);
        }
    });
});

// Create entities
app.post('/api/agences', (req, res) => {
    const { nom, ville, directeur } = req.body;
    db.query('INSERT INTO agences (nom, ville, directeur) VALUES (?, ?, ?)', 
        [nom, ville, directeur], (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else {
            res.json({ id: results.insertId, ...req.body });
        }
    });
});

app.post('/api/comptables', (req, res) => {
    const { nom, age, telephone, agence_id } = req.body;
    db.query('INSERT INTO comptables (nom, age, telephone, agence_id) VALUES (?, ?, ?, ?)', 
        [nom, age, telephone, agence_id], (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else {
            res.json({ id: results.insertId, ...req.body });
        }
    });
});

app.post('/api/exploitations', (req, res) => {
    const { nom, commune, sau, comptable_id } = req.body;
    db.query('INSERT INTO exploitations (nom, commune, sau, comptable_id) VALUES (?, ?, ?, ?)', 
        [nom, commune, sau, comptable_id], (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else {
            res.json({ id: results.insertId, ...req.body });
        }
    });
});

app.post('/api/dossiers', (req, res) => {
    const { nom, date_creation, comptable_id, agence_id } = req.body;
    db.query('INSERT INTO dossiers (nom, date_creation, comptable_id, agence_id) VALUES (?, ?, ?, ?)', 
        [nom, date_creation, comptable_id, agence_id], (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else {
            res.json({ id: results.insertId, ...req.body });
        }
    });
});

// Update entities
app.put('/api/agences/:id', (req, res) => {
    const { nom, ville, directeur } = req.body;
    db.query('UPDATE agences SET nom = ?, ville = ?, directeur = ? WHERE id = ?', 
        [nom, ville, directeur, req.params.id], (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else {
            res.json({ id: req.params.id, ...req.body });
        }
    });
});

app.put('/api/comptables/:id', (req, res) => {
    const { nom, age, telephone, agence_id } = req.body;
    db.query('UPDATE comptables SET nom = ?, age = ?, telephone = ?, agence_id = ? WHERE id = ?', 
        [nom, age, telephone, agence_id, req.params.id], (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else {
            res.json({ id: req.params.id, ...req.body });
        }
    });
});

app.put('/api/exploitations/:id', (req, res) => {
    const { nom, commune, sau, comptable_id } = req.body;
    db.query('UPDATE exploitations SET nom = ?, commune = ?, sau = ?, comptable_id = ? WHERE id = ?', 
        [nom, commune, sau, comptable_id, req.params.id], (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else {
            res.json({ id: req.params.id, ...req.body });
        }
    });
});

app.put('/api/dossiers/:id', (req, res) => {
    const { nom, date_creation, comptable_id, agence_id } = req.body;
    db.query('UPDATE dossiers SET nom = ?, date_creation = ?, comptable_id = ?, agence_id = ? WHERE id = ?', 
        [nom, date_creation, comptable_id, agence_id, req.params.id], (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else {
            res.json({ id: req.params.id, ...req.body });
        }
    });
});

// Delete entities
app.delete('/api/agences/:id', (req, res) => {
    db.query('DELETE FROM agences WHERE id = ?', [req.params.id], (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else {
            res.json({ message: 'Agence deleted successfully' });
        }
    });
});

app.delete('/api/comptables/:id', (req, res) => {
    db.query('DELETE FROM comptables WHERE id = ?', [req.params.id], (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else {
            res.json({ message: 'Comptable deleted successfully' });
        }
    });
});

app.delete('/api/exploitations/:id', (req, res) => {
    db.query('DELETE FROM exploitations WHERE id = ?', [req.params.id], (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else {
            res.json({ message: 'Exploitation deleted successfully' });
        }
    });
});

app.delete('/api/dossiers/:id', (req, res) => {
    db.query('DELETE FROM dossiers WHERE id = ?', [req.params.id], (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else {
            res.json({ message: 'Dossier deleted successfully' });
        }
    });
});

// Dashboard stats
app.get('/api/dashboard/stats', (req, res) => {
    const queries = {
        dossiers: 'SELECT COUNT(*) as count FROM dossiers',
        comptables: 'SELECT COUNT(*) as count FROM comptables',
        exploitations: 'SELECT COUNT(*) as count FROM exploitations'
    };

    const results = {};
    let completed = 0;

    Object.keys(queries).forEach(key => {
        db.query(queries[key], (err, result) => {
            if (err) {
                console.error(`Error fetching ${key}:`, err);
                results[key] = 0;
            } else {
                results[key] = result[0].count;
            }
            completed++;
            
            if (completed === Object.keys(queries).length) {
                res.json(results);
            }
        });
    });
});

// Serve the main page
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

app.listen(port, () => {
    console.log(`Server running at http://localhost:${port}`);
});

// Get agences with comptable count
app.get('/api/agences-with-stats', (req, res) => {
    const query = `
        SELECT 
            a.*, 
            COUNT(c.id) as nombre_comptables
        FROM agences a 
        LEFT JOIN comptables c ON a.id = c.agence_id 
        GROUP BY a.id
    `;
    db.query(query, (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else {
            res.json(results);
        }
    });
});