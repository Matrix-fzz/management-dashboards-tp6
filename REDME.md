# Get dashboard statistics
SELECT 
    (SELECT COUNT(*) FROM dossiers) as total_dossiers,
    (SELECT COUNT(*) FROM comptables) as total_comptables,
    (SELECT COUNT(*) FROM exploitations) as total_exploitations,
    (SELECT COUNT(*) FROM agences) as total_agences;

-- Get dossiers with comptable and agence information
SELECT 
    d.id,
    d.nom as dossier_nom,
    d.date_creation,
    c.nom as comptable_nom,
    a.nom as agence_nom
FROM dossiers d
LEFT JOIN comptables c ON d.comptable_id = c.id
LEFT JOIN agences a ON d.agence_id = a.id
ORDER BY d.date_creation DESC;

-- Get comptables with their agence and exploitation count
SELECT 
    c.id,
    c.nom as comptable_nom,
    c.age,
    c.telephone,
    a.nom as agence_nom,
    COUNT(e.id) as nombre_exploitations
FROM comptables c
LEFT JOIN agences a ON c.agence_id = a.id
LEFT JOIN exploitations e ON c.id = e.comptable_id
GROUP BY c.id, c.nom, c.age, c.telephone, a.nom;

-- Get exploitations with comptable and agence information
SELECT 
    e.id,
    e.nom as exploitation_nom,
    e.commune,
    e.sau,
    c.nom as comptable_nom,
    a.nom as agence_nom
FROM exploitations e
LEFT JOIN comptables c ON e.comptable_id = c.id
LEFT JOIN agences a ON c.agence_id = a.id;

-- Get agences with comptable count
SELECT 
    a.id,
    a.nom as agence_nom,
    a.ville,
    a.directeur,
    COUNT(c.id) as nombre_comptables
FROM agences a
LEFT JOIN comptables c ON a.id = c.agence_id
GROUP BY a.id, a.nom, a.ville, a.directeur;

# Views for Common Queries
-- Create view for dossier details
CREATE VIEW view_dossiers_details AS
SELECT 
    d.id,
    d.nom as dossier_nom,
    d.date_creation,
    c.id as comptable_id,
    c.nom as comptable_nom,
    c.telephone as comptable_telephone,
    a.id as agence_id,
    a.nom as agence_nom,
    a.ville as agence_ville
FROM dossiers d
LEFT JOIN comptables c ON d.comptable_id = c.id
LEFT JOIN agences a ON d.agence_id = a.id;

-- Create view for comptable details
CREATE VIEW view_comptables_details AS
SELECT 
    c.id,
    c.nom as comptable_nom,
    c.age,
    c.telephone,
    a.id as agence_id,
    a.nom as agence_nom,
    a.ville as agence_ville,
    a.directeur as agence_directeur,
    COUNT(e.id) as nombre_exploitations
FROM comptables c
LEFT JOIN agences a ON c.agence_id = a.id
LEFT JOIN exploitations e ON c.id = e.comptable_id
GROUP BY c.id, c.nom, c.age, c.telephone, a.id, a.nom, a.ville, a.directeur; 

