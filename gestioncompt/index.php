<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounting File Management Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --danger: #f72585;
            --warning: #f8961e;
            --info: #4895ef;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --sidebar-width: 250px;
            --header-height: 70px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fb;
            color: var(--dark);
            line-height: 1.6;
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary), var(--secondary));
            color: white;
            height: 100vh;
            position: fixed;
            overflow-y: auto;
            transition: all 0.3s;
            z-index: 100;
        }

        .sidebar-header {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-header i {
            font-size: 1.8rem;
        }

        .sidebar-header h2 {
            font-size: 1.3rem;
            font-weight: 600;
        }

        .sidebar-menu {
            padding: 1rem 0;
        }

        .sidebar-menu ul {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 5px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left-color: white;
        }

        .sidebar-menu i {
            width: 20px;
            text-align: center;
        }

        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
        }

        .header {
            height: var(--header-height);
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 99;
        }

        .header-left h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            padding: 10px 15px 10px 40px;
            border: 1px solid var(--light-gray);
            border-radius: 6px;
            width: 250px;
            transition: all 0.3s;
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .dashboard-content {
            padding: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 15px;
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
        }

        .stat-icon.primary {
            background-color: var(--primary);
        }

        .stat-icon.success {
            background-color: var(--success);
        }

        .stat-icon.warning {
            background-color: var(--warning);
        }

        .stat-icon.info {
            background-color: var(--info);
        }

        .stat-info h3 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-info p {
            color: var(--gray);
            font-size: 0.9rem;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 2rem;
        }

        .chart-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .chart-header h3 {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .chart-container {
            height: 300px;
            position: relative;
        }

        .tables-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 2rem;
        }

        .table-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .card-header {
            background-color: var(--light);
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid var(--light-gray);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h2 {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--dark);
        }

        .card-body {
            padding: 1.5rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-success {
            background-color: var(--success);
            color: white;
        }

        .btn-success:hover {
            background-color: #3aa8d0;
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background-color: #e11570;
        }

        .btn-warning {
            background-color: var(--warning);
            color: white;
        }

        .btn-warning:hover {
            background-color: #e0861b;
        }

        .btn-light {
            background-color: var(--light);
            color: var(--dark);
        }

        .btn-light:hover {
            background-color: #e2e6ea;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--light-gray);
        }

        th {
            background-color: var(--light);
            font-weight: 600;
            color: var(--dark);
        }

        tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: modalFade 0.3s;
        }

        @keyframes modalFade {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-header {
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid var(--light-gray);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            font-size: 1.3rem;
            font-weight: 600;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--gray);
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--light-gray);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--light-gray);
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }

        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }

        .form-col {
            flex: 1;
            padding: 0 10px;
            min-width: 250px;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            display: none;
        }

        .alert-success {
            background-color: rgba(76, 201, 240, 0.2);
            border: 1px solid var(--success);
            color: #0c5460;
        }

        .alert-danger {
            background-color: rgba(247, 37, 133, 0.2);
            border: 1px solid var(--danger);
            color: #721c24;
        }

        @media (max-width: 992px) {
            .charts-grid, .tables-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            
            .sidebar-header h2, .sidebar-menu span {
                display: none;
            }
            
            .sidebar-header {
                justify-content: center;
            }
            
            .main-content {
                margin-left: 70px;
                width: calc(100% - 70px);
            }
            
            .search-box input {
                width: 150px;
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .header {
                padding: 0 1rem;
            }
            
            .dashboard-content {
                padding: 1rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-file-invoice-dollar"></i>
            <h2>Accounting Files</h2>
        </div>
        <div class="sidebar-menu">
            <ul>
                <li><a href="#" class="active"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                <li><a href="#" onclick="showSection('comptables')"><i class="fas fa-users"></i> <span>Comptables</span></a></li>
                <li><a href="#" onclick="showSection('exploitations')"><i class="fas fa-building"></i> <span>Exploitations</span></a></li>
                <li><a href="#"><i class="fas fa-map-marker-alt"></i> <span>Communes</span></a></li>
                <li><a href="#"><i class="fas fa-landmark"></i> <span>Agences</span></a></li>
                <li><a href="#"><i class="fas fa-chart-bar"></i> <span>Reports</span></a></li>
                <li><a href="#"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <h1>Dashboard</h1>
            </div>
            <div class="header-right">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search..." id="searchInput">
                </div>
                <div class="user-profile">
                    <div class="user-avatar">JD</div>
                    <div class="user-info">
                        <div class="user-name">Jean Dupont</div>
                        <div class="user-role">Administrator</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <!-- Alert Messages -->
            <div class="alert alert-success" id="successAlert"></div>
            <div class="alert alert-danger" id="errorAlert"></div>

            <!-- Stats Grid -->
            <div class="stats-grid" id="statsGrid">
                <!-- Stats will be loaded dynamically -->
            </div>

            <!-- Charts Grid -->
            <div class="charts-grid">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Comptables by Commune</h3>
                    </div>
                    <div class="chart-container">
                        <canvas id="comptablesChart"></canvas>
                    </div>
                </div>
                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Exploitations Distribution</h3>
                    </div>
                    <div class="chart-container">
                        <canvas id="exploitationsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tables Grid -->
            <div class="tables-grid">
                <div class="table-card">
                    <div class="card-header">
                        <h2>Recent Comptables</h2>
                        <button class="btn btn-primary" onclick="openComptableModal()">
                            <i class="fas fa-plus"></i> Add New
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-container">
                            <table id="comptablesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Commune</th>
                                        <th>Agence</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="comptablesTableBody">
                                    <!-- Comptables will be loaded dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="table-card">
                    <div class="card-header">
                        <h2>Recent Exploitations</h2>
                        <button class="btn btn-primary" onclick="openExploitationModal()">
                            <i class="fas fa-plus"></i> Add New
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-container">
                            <table id="exploitationsTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Commune</th>
                                        <th>SAU</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="exploitationsTableBody">
                                    <!-- Exploitations will be loaded dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Comptable Modal -->
    <div class="modal" id="comptableModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="comptableModalTitle">Add Comptable</h3>
                <button class="modal-close" onclick="closeComptableModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="comptableForm">
                    <input type="hidden" id="comptableId">
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="comptableIdInput">Comptable ID</label>
                                <input type="text" id="comptableIdInput" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="comptableName">Name</label>
                                <input type="text" id="comptableName" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="birthDate">Birth Date</label>
                                <input type="date" id="birthDate" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="tel" id="phone" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="commune">Commune</label>
                                <select id="commune" class="form-control" required>
                                    <option value="">Select Commune</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="agence">Agence</label>
                                <select id="agence" class="form-control" required>
                                    <option value="">Select Agence</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" onclick="closeComptableModal()">Cancel</button>
                <button class="btn btn-primary" id="saveComptableBtn" onclick="saveComptable()">Save Comptable</button>
            </div>
        </div>
    </div>

    <!-- Exploitation Modal -->
    <div class="modal" id="exploitationModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="exploitationModalTitle">Add Exploitation</h3>
                <button class="modal-close" onclick="closeExploitationModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="exploitationForm">
                    <input type="hidden" id="exploitationId">
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="exploitationIdInput">Exploitation ID</label>
                                <input type="text" id="exploitationIdInput" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="exploitationName">Name</label>
                                <input type="text" id="exploitationName" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="sau">SAU</label>
                                <input type="number" id="sau" class="form-control" step="0.01" required>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="exploitationCommune">Commune</label>
                                <select id="exploitationCommune" class="form-control" required>
                                    <option value="">Select Commune</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" onclick="closeExploitationModal()">Cancel</button>
                <button class="btn btn-primary" id="saveExploitationBtn" onclick="saveExploitation()">Save Exploitation</button>
            </div>
        </div>
    </div>

    <script>
        // API Base URL
        const API_BASE = 'api.php';

        // Global variables
        let comptablesChart, exploitationsChart;
        let currentComptableId = null;
        let currentExploitationId = null;

        // Initialize the dashboard
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboard();
            loadCommunes();
            loadAgences();
        });

        // Load dashboard data
        async function loadDashboard() {
            try {
                // Load stats
                const statsResponse = await fetch(`${API_BASE}/stats`);
                const stats = await statsResponse.json();
                updateStats(stats);

                // Load comptables
                const comptablesResponse = await fetch(`${API_BASE}/comptables`);
                const comptables = await comptablesResponse.json();
                updateComptablesTable(comptables);

                // Load exploitations
                const exploitationsResponse = await fetch(`${API_BASE}/exploitations`);
                const exploitations = await exploitationsResponse.json();
                updateExploitationsTable(exploitations);

                // Initialize charts
                initCharts(stats);
            } catch (error) {
                showError('Failed to load dashboard data: ' + error.message);
            }
        }

        // Update stats cards
        function updateStats(stats) {
            const statsGrid = document.getElementById('statsGrid');
            statsGrid.innerHTML = `
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>${stats.comptables}</h3>
                        <p>Comptables</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-info">
                        <h3>${stats.exploitations}</h3>
                        <p>Exploitations</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon warning">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3>${stats.communes}</h3>
                        <p>Communes</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon info">
                        <i class="fas fa-landmark"></i>
                    </div>
                    <div class="stat-info">
                        <h3>${stats.agences}</h3>
                        <p>Agences</p>
                    </div>
                </div>
            `;
        }

        // Update comptables table
        function updateComptablesTable(comptables) {
            const tbody = document.getElementById('comptablesTableBody');
            tbody.innerHTML = comptables.map(comptable => `
                <tr>
                    <td>${comptable.id_comptable}</td>
                    <td>${comptable.nom_comptable}</td>
                    <td>${comptable.nom_commune || 'N/A'}</td>
                    <td>${comptable.nom_agence || 'N/A'}</td>
                    <td class="actions">
                        <button class="btn btn-light btn-sm" onclick="viewComptable('${comptable.id_comptable}')">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="editComptable('${comptable.id_comptable}')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="deleteComptable('${comptable.id_comptable}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Update exploitations table
        function updateExploitationsTable(exploitations) {
            const tbody = document.getElementById('exploitationsTableBody');
            tbody.innerHTML = exploitations.map(exploitation => `
                <tr>
                    <td>${exploitation.id_exploitation}</td>
                    <td>${exploitation.nom_exploitation}</td>
                    <td>${exploitation.nom_commune || 'N/A'}</td>
                    <td>${exploitation.sau} ha</td>
                    <td class="actions">
                        <button class="btn btn-light btn-sm" onclick="viewExploitation('${exploitation.id_exploitation}')">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="editExploitation('${exploitation.id_exploitation}')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="deleteExploitation('${exploitation.id_exploitation}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Load communes for dropdown
        async function loadCommunes() {
            try {
                const response = await fetch(`${API_BASE}/communes`);
                const communes = await response.json();
                
                const communeSelect = document.getElementById('commune');
                const exploitationCommuneSelect = document.getElementById('exploitationCommune');
                
                communeSelect.innerHTML = '<option value="">Select Commune</option>';
                exploitationCommuneSelect.innerHTML = '<option value="">Select Commune</option>';
                
                communes.forEach(commune => {
                    communeSelect.innerHTML += `<option value="${commune.id_commune}">${commune.nom_commune}</option>`;
                    exploitationCommuneSelect.innerHTML += `<option value="${commune.id_commune}">${commune.nom_commune}</option>`;
                });
            } catch (error) {
                showError('Failed to load communes: ' + error.message);
            }
        }

        // Load agences for dropdown
        async function loadAgences() {
            try {
                const response = await fetch(`${API_BASE}/agences`);
                const agences = await response.json();
                
                const agenceSelect = document.getElementById('agence');
                agenceSelect.innerHTML = '<option value="">Select Agence</option>';
                
                agences.forEach(agence => {
                    agenceSelect.innerHTML += `<option value="${agence.id_agence}">${agence.nom_agence}</option>`;
                });
            } catch (error) {
                showError('Failed to load agences: ' + error.message);
            }
        }

        // Initialize charts
        function initCharts(stats) {
            // Comptables by Commune Chart
            const comptablesCtx = document.getElementById('comptablesChart').getContext('2d');
            comptablesChart = new Chart(comptablesCtx, {
                type: 'bar',
                data: {
                    labels: stats.comptables_by_commune.map(item => item.nom_commune),
                    datasets: [{
                        label: 'Number of Comptables',
                        data: stats.comptables_by_commune.map(item => item.count),
                        backgroundColor: 'rgba(67, 97, 238, 0.7)',
                        borderColor: 'rgba(67, 97, 238, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });

            // Exploitations Distribution Chart (dummy data for now)
            const exploitationsCtx = document.getElementById('exploitationsChart').getContext('2d');
            exploitationsChart = new Chart(exploitationsCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Bordeaux', 'Pessac', 'Talence', 'Mérignac', 'Others'],
                    datasets: [{
                        data: [5, 3, 2, 2, 3],
                        backgroundColor: [
                            'rgba(67, 97, 238, 0.7)',
                            'rgba(76, 201, 240, 0.7)',
                            'rgba(248, 150, 30, 0.7)',
                            'rgba(247, 37, 133, 0.7)',
                            'rgba(72, 149, 239, 0.7)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Comptable CRUD operations
        function openComptableModal() {
            currentComptableId = null;
            document.getElementById('comptableModalTitle').textContent = 'Add Comptable';
            document.getElementById('comptableForm').reset();
            document.getElementById('comptableModal').style.display = 'flex';
        }

        function closeComptableModal() {
            document.getElementById('comptableModal').style.display = 'none';
        }

        async function saveComptable() {
            const formData = {
                id_comptable: document.getElementById('comptableIdInput').value,
                nom_comptable: document.getElementById('comptableName').value,
                date_naissance: document.getElementById('birthDate').value,
                no_tel: document.getElementById('phone').value,
                id_commune: document.getElementById('commune').value,
                id_agence: document.getElementById('agence').value
            };

            try {
                const url = currentComptableId ? 
                    `${API_BASE}/comptables/${currentComptableId}` : 
                    `${API_BASE}/comptables`;
                
                const method = currentComptableId ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();
                
                if (result.error) {
                    showError(result.error);
                } else {
                    showSuccess(currentComptableId ? 'Comptable updated successfully' : 'Comptable created successfully');
                    closeComptableModal();
                    loadDashboard();
                }
            } catch (error) {
                showError('Failed to save comptable: ' + error.message);
            }
        }

        async function editComptable(id) {
            try {
                const response = await fetch(`${API_BASE}/comptables/${id}`);
                const comptable = await response.json();
                
                currentComptableId = id;
                document.getElementById('comptableModalTitle').textContent = 'Edit Comptable';
                document.getElementById('comptableIdInput').value = comptable.id_comptable;
                document.getElementById('comptableName').value = comptable.nom_comptable;
                document.getElementById('birthDate').value = comptable.date_naissance;
                document.getElementById('phone').value = comptable.no_tel;
                document.getElementById('commune').value = comptable.id_commune;
                document.getElementById('agence').value = comptable.id_agence;
                
                document.getElementById('comptableModal').style.display = 'flex';
            } catch (error) {
                showError('Failed to load comptable: ' + error.message);
            }
        }

        async function deleteComptable(id) {
            if (confirm('Are you sure you want to delete this comptable?')) {
                try {
                    const response = await fetch(`${API_BASE}/comptables/${id}`, {
                        method: 'DELETE'
                    });
                    
                    const result = await response.json();
                    
                    if (result.error) {
                        showError(result.error);
                    } else {
                        showSuccess('Comptable deleted successfully');
                        loadDashboard();
                    }
                } catch (error) {
                    showError('Failed to delete comptable: ' + error.message);
                }
            }
        }

        function viewComptable(id) {
            alert('View comptable details for ID: ' + id);
            // In a real application, you would show a detailed view
        }

        // Exploitation CRUD operations (similar pattern)
        function openExploitationModal() {
            currentExploitationId = null;
            document.getElementById('exploitationModalTitle').textContent = 'Add Exploitation';
            document.getElementById('exploitationForm').reset();
            document.getElementById('exploitationModal').style.display = 'flex';
        }

        function closeExploitationModal() {
            document.getElementById('exploitationModal').style.display = 'none';
        }

        async function saveExploitation() {
            const formData = {
                id_exploitation: document.getElementById('exploitationIdInput').value,
                nom_exploitation: document.getElementById('exploitationName').value,
                sau: document.getElementById('sau').value,
                id_commune: document.getElementById('exploitationCommune').value
            };

            try {
                const url = currentExploitationId ? 
                    `${API_BASE}/exploitations/${currentExploitationId}` : 
                    `${API_BASE}/exploitations`;
                
                const method = currentExploitationId ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();
                
                if (result.error) {
                    showError(result.error);
                } else {
                    showSuccess(currentExploitationId ? 'Exploitation updated successfully' : 'Exploitation created successfully');
                    closeExploitationModal();
                    loadDashboard();
                }
            } catch (error) {
                showError('Failed to save exploitation: ' + error.message);
            }
        }

        async function editExploitation(id) {
            try {
                const response = await fetch(`${API_BASE}/exploitations/${id}`);
                const exploitation = await response.json();
                
                currentExploitationId = id;
                document.getElementById('exploitationModalTitle').textContent = 'Edit Exploitation';
                document.getElementById('exploitationIdInput').value = exploitation.id_exploitation;
                document.getElementById('exploitationName').value = exploitation.nom_exploitation;
                document.getElementById('sau').value = exploitation.sau;
                document.getElementById('exploitationCommune').value = exploitation.id_commune;
                
                document.getElementById('exploitationModal').style.display = 'flex';
            } catch (error) {
                showError('Failed to load exploitation: ' + error.message);
            }
        }

        async function deleteExploitation(id) {
            if (confirm('Are you sure you want to delete this exploitation?')) {
                try {
                    const response = await fetch(`${API_BASE}/exploitations/${id}`, {
                        method: 'DELETE'
                    });
                    
                    const result = await response.json();
                    
                    if (result.error) {
                        showError(result.error);
                    } else {
                        showSuccess('Exploitation deleted successfully');
                        loadDashboard();
                    }
                } catch (error) {
                    showError('Failed to delete exploitation: ' + error.message);
                }
            }
        }

        function viewExploitation(id) {
            alert('View exploitation details for ID: ' + id);
            // In a real application, you would show a detailed view
        }

        // Utility functions
        function showSuccess(message) {
            const alert = document.getElementById('successAlert');
            alert.textContent = message;
            alert.style.display = 'block';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 5000);
        }

        function showError(message) {
            const alert = document.getElementById('errorAlert');
            alert.textContent = message;
            alert.style.display = 'block';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 5000);
        }

        function showSection(section) {
            // Implementation for showing different sections
            alert('Showing section: ' + section);
        }
    </script>
</body>
</html>