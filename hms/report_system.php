<?php
require_once 'includes/header.php';
?>

<style>
/* Report-specific styling - copied from patient reports */
.report-container {
    margin: 2rem 0;
}

.report-tabs {
    margin-bottom: 2rem;
}

.report-tabs .tabs {
    border-bottom: 2px solid #e8e8e8;
}

.report-tabs .tabs ul {
    border-bottom: none;
}

.report-tabs .tabs li a {
    border: none;
    border-bottom: 3px solid transparent;
    padding: 1rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.report-tabs .tabs li.is-active a {
    border-bottom-color: #3273dc;
    color: #3273dc;
    background-color: #f8f9fa;
}

.report-tabs .tabs li a:hover {
    border-bottom-color: #3273dc;
    color: #3273dc;
}

.chart-box {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border-left: 4px solid #3273dc;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #3273dc;
    display: block;
}

.stat-label {
    color: #666;
    font-size: 0.9rem;
    margin-top: 0.5rem;
}

.export-buttons {
    margin-bottom: 1rem;
}

.filter-section {
    background: white;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Data table styling */
.data-table {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.data-table table {
    width: 100%;
    margin-bottom: 0;
}

.data-table .table thead th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    color: #495057;
}

.data-table .table tbody tr:hover {
    background-color: #f8f9fa;
}

/* Chart containers */
.chart-container {
    position: relative;
    height: 400px;
    margin: 1rem 0;
}

/* Loading spinner */
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}

.tab-content {
    display: none;
}
.tab-content.is-active {
    display: block;
}

.search-controls {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.pagination-controls {
    background: #ffffff;
    border-radius: 8px;
    padding: 1rem;
    border: 1px solid #e1e5e9;
}

.action-btn {
    margin-right: 0.25rem;
}

.action-btn:last-child {
    margin-right: 0;
}

/* Modal enhancements */
.modal {
    z-index: 1000;
}

.modal-card {
    max-width: 90vw;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-card-body {
    max-height: 70vh;
    overflow-y: auto;
}

.user-info-card {
    border: 1px solid #e1e5e9;
    border-radius: 8px;
}

.user-info-card .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.user-info-card .card-header-title {
    color: white;
}

@media (max-width: 768px) {
    .modal-card {
        max-width: 95vw;
    }
    
    .modal-card-body {
        padding: 1rem;
    }
    
    .columns {
        display: block;
    }
    
    .column {
        margin-bottom: 1rem;
    }
}

/* Print Styles */
@media print {
    body * {
        visibility: hidden;
    }
    
    #systemPrintArea,
    #systemPrintArea * {
        visibility: visible;
    }
    
    #systemPrintArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100% !important;
        padding: 15px;
        font-family: Arial, sans-serif;
        display: block !important;
        visibility: visible !important;
        font-size: 11px;
        line-height: 1.3;
    }
    
    .print-header {
        text-align: center;
        border-bottom: 2px solid #000;
        padding-bottom: 8px;
        margin-bottom: 12px;
    }
    
    .print-title {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 3px;
    }
    
    .print-subtitle {
        font-size: 12px;
        color: #666;
        margin: 0;
    }
    
    .print-info {
        margin-bottom: 12px;
        border: 1px solid #000;
        padding: 8px;
    }
    
    .print-info h3 {
        margin: 0 0 6px 0 !important;
        font-size: 12px;
        border-bottom: 1px solid #ccc;
        padding-bottom: 3px;
    }
    
    .print-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        font-size: 10px;
        margin-bottom: 6px;
    }
    
    .print-users {
        margin-bottom: 12px;
    }
    
    .print-users h3 {
        margin: 0 0 8px 0 !important;
        font-size: 12px;
        background-color: #f0f0f0;
        padding: 4px 8px;
        border: 1px solid #000;
    }
    
    .users-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #000;
        font-size: 9px;
    }
    
    .users-table th,
    .users-table td {
        border: 1px solid #ccc;
        padding: 3px 4px;
        text-align: left;
        vertical-align: top;
    }
    
    .users-table th {
        background-color: #f5f5f5;
        font-weight: bold;
        font-size: 9px;
    }
    
    .user-name {
        font-weight: bold;
        font-size: 10px;
    }
    
    .print-footer {
        margin-top: 15px;
        border-top: 1px solid #000;
        padding-top: 6px;
        text-align: center;
        font-size: 8px;
        line-height: 1.2;
    }
    
    .print-footer div {
        margin-bottom: 2px;
    }
    
    /* Ensure everything fits on one page */
    @page {
        margin: 0.5in;
        size: letter;
    }
    
    /* Hide elements that shouldn't be printed */
    .navbar, .tabs, .search-controls, .pagination-controls, 
    .button, .modal, .loading-overlay {
        display: none !important;
    }
}
</style>

<div class="page-transition mt-4">
    <!-- Page Header -->
    <div class="columns is-vcentered mb-4">
        <div class="column">
            <nav class="breadcrumb" aria-label="breadcrumbs">
                <ul>
                    <li><a href="reports.php"><span class="icon"><i class="fas fa-chart-bar"></i></span><span>Reports</span></a></li>
                    <li class="is-active"><a href="#"><span class="icon"><i class="fas fa-cogs"></i></span><span>System Reports</span></a></li>
                </ul>
            </nav>
            <h1 class="title is-3">
                <span class="icon">
                    <i class="fas fa-cogs"></i>
                </span>
                System Reports & Analytics
            </h1>
            <p class="subtitle">Comprehensive system administration and usage analytics</p>
        </div>
        <div class="column is-narrow">
            <div class="buttons export-buttons">
                <button class="button is-info" id="exportExcel">
                    <span class="icon"><i class="fas fa-file-excel"></i></span>
                    <span>Export Excel</span>
                </button>
                <button class="button is-link" id="printReport">
                    <span class="icon"><i class="fas fa-print"></i></span>
                    <span>Print</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="filter-section">
        <div class="columns is-vcentered">
            <div class="column">
                <h4 class="title is-6 mb-2">
                    <span class="icon"><i class="fas fa-filter"></i></span>
                    Report Filters
                </h4>
            </div>
            <div class="column is-2">
                <div class="field">
                    <label class="label">Search</label>
                    <div class="control">
                        <input class="input" type="text" id="searchInput" placeholder="Search users..." />
                    </div>
                </div>
            </div>
            <div class="column is-2">
                <div class="field">
                    <label class="label">From Date</label>
                    <div class="control">
                        <input class="input" type="date" id="fromDate" />
                    </div>
                </div>
            </div>
            <div class="column is-2">
                <div class="field">
                    <label class="label">To Date</label>
                    <div class="control">
                        <input class="input" type="date" id="toDate" />
                    </div>
                </div>
            </div>
            <div class="column is-2">
                <div class="field">
                    <label class="label">Role</label>
                    <div class="control">
                        <div class="select is-fullwidth">
                            <select id="roleFilter">
                                <option value="">All Roles</option>
                                <option value="admin">Admin</option>
                                <option value="doctor">Doctor</option>
                                <option value="staff">Staff</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="column is-2">
                <div class="field">
                    <label class="label">&nbsp;</label>
                    <div class="control">
                        <button class="button is-primary is-fullwidth" id="applyFilters">
                            <span class="icon"><i class="fas fa-search"></i></span>
                            <span>Apply</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Overview -->
    <div class="stats-grid" id="systemStats">
        <!-- Stats will be loaded here -->
    </div>

    <!-- Report Tabs -->
    <div class="report-tabs">
        <div class="tabs is-boxed">
            <ul>
                <li class="is-active" data-tab="users">
                    <a>
                        <span class="icon"><i class="fas fa-users"></i></span>
                        <span>Users</span>
                    </a>
                </li>
                <li data-tab="activity">
                    <a>
                        <span class="icon"><i class="fas fa-chart-line"></i></span>
                        <span>Activity</span>
                    </a>
                </li>
                <li data-tab="logs">
                    <a>
                        <span class="icon"><i class="fas fa-list-alt"></i></span>
                        <span>System Logs</span>
                    </a>
                </li>
                <li data-tab="performance">
                    <a>
                        <span class="icon"><i class="fas fa-tachometer-alt"></i></span>
                        <span>Performance</span>
                    </a>
                </li>
                <li data-tab="security">
                    <a>
                        <span class="icon"><i class="fas fa-shield-alt"></i></span>
                        <span>Security</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab Content -->
    <div id="tabContent">
        <!-- Users Tab -->
        <div id="users-tab" class="tab-content is-active">
            <div class="chart-box">
                <div class="columns is-vcentered mb-4">
                    <div class="column">
                        <h4 class="title is-5">System Users</h4>
                    </div>
                    <div class="column is-narrow">
                        <div class="field has-addons">
                            <div class="control">
                                <div class="select">
                                    <select id="recordsPerPage">
                                        <option value="10">10 per page</option>
                                        <option value="20" selected>20 per page</option>
                                        <option value="50">50 per page</option>
                                        <option value="100">100 per page</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="data-table">
                    <table class="table is-fullwidth is-striped is-hoverable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Activity Level</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <!-- Data will be loaded here -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination Controls -->
                <div class="columns is-vcentered mt-4">
                    <div class="column">
                        <p class="help" id="paginationInfo">
                            Showing 0 of 0 users
                        </p>
                    </div>
                    <div class="column is-narrow">
                        <nav class="pagination is-small" role="navigation" aria-label="pagination">
                            <button class="pagination-previous" id="prevPage" disabled>Previous</button>
                            <button class="pagination-next" id="nextPage" disabled>Next</button>
                            <ul class="pagination-list" id="paginationList">
                                <!-- Pagination numbers will be inserted here -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Tab -->
        <div id="activity-tab" class="tab-content" style="display: none;">
            <div class="chart-box">
                <h4 class="title is-5 mb-4">User Activity Over Time</h4>
                <div class="chart-container">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Logs Tab -->
        <div id="logs-tab" class="tab-content" style="display: none;">
            <div class="chart-box">
                <div class="columns is-vcentered mb-4">
                    <div class="column">
                        <h4 class="title is-5">System Logs</h4>
                    </div>
                    <div class="column is-narrow">
                        <div class="field has-addons">
                            <div class="control">
                                <div class="select">
                                    <select id="logsPerPage">
                                        <option value="10">10 per page</option>
                                        <option value="20" selected>20 per page</option>
                                        <option value="50">50 per page</option>
                                        <option value="100">100 per page</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="data-table">
                    <table class="table is-fullwidth is-striped is-hoverable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Timestamp</th>
                                <th>Type</th>
                                <th>Action</th>
                                <th>User</th>
                                <th>IP Address</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody id="logsTableBody">
                            <!-- Logs will be loaded here -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination Controls for Logs -->
                <div class="columns is-vcentered mt-4">
                    <div class="column">
                        <p class="help" id="logsPaginationInfo">
                            Showing 0 of 0 logs
                        </p>
                    </div>
                    <div class="column is-narrow">
                        <nav class="pagination is-small" role="navigation" aria-label="pagination">
                            <button class="pagination-previous" id="logsPrevPage" disabled>Previous</button>
                            <button class="pagination-next" id="logsNextPage" disabled>Next</button>
                            <ul class="pagination-list" id="logsPaginationList">
                                <!-- Pagination numbers will be inserted here -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Tab -->
        <div id="performance-tab" class="tab-content" style="display: none;">
            <div class="columns">
                <div class="column">
                    <div class="chart-box">
                        <h4 class="title is-5 mb-4">System Performance</h4>
                        <div class="chart-container">
                            <canvas id="performanceChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="column">
                    <div class="chart-box">
                        <h4 class="title is-5 mb-4">Resource Usage</h4>
                        <div class="chart-container">
                            <canvas id="resourceChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Tab -->
        <div id="security-tab" class="tab-content" style="display: none;">
            <div class="columns">
                <div class="column">
                    <div class="chart-box">
                        <h4 class="title is-5 mb-4">Security Metrics</h4>
                        <div class="content">
                            <div id="securityStats">
                                <!-- Security stats will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column">
                    <div class="chart-box">
                        <h4 class="title is-5 mb-4">Security Events</h4>
                        <div class="data-table">
                            <table class="table is-fullwidth is-striped is-hoverable">
                                <thead>
                                    <tr>
                                        <th>Timestamp</th>
                                        <th>Type</th>
                                        <th>User</th>
                                        <th>Severity</th>
                                    </tr>
                                </thead>
                                <tbody id="securityEventsBody">
                                    <!-- Security events will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- User Details Modal -->
<div class="modal" id="userModal">
    <div class="modal-background" onclick="closeUserModal()"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">
                <span class="icon">
                    <i class="fas fa-user"></i>
                </span>
                User Details
            </p>
            <button class="delete" aria-label="close" onclick="closeUserModal()"></button>
        </header>
        <section class="modal-card-body">
            <div id="userModalContent">
                <!-- User details will be loaded here -->
                <div class="has-text-centered">
                    <span class="icon is-large">
                        <i class="fas fa-spinner fa-pulse fa-2x"></i>
                    </span>
                    <p class="mt-2">Loading user details...</p>
                </div>
            </div>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-light" onclick="closeUserModal()">Close</button>
        </footer>
    </div>
</div>

<!-- Loading Spinner -->
<div class="loading-overlay" id="loadingSpinner" style="display: none;">
    <span class="icon is-large">
        <i class="fas fa-spinner fa-pulse fa-2x"></i>
    </span>
</div>

<!-- Print Area (Hidden) -->
<div id="systemPrintArea" style="display: none;">
    <!-- Print content will be generated here -->
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- JavaScript -->
<script>
class SystemReportsManager {
    constructor() {
        this.currentTab = 'users';
        this.charts = {};
        this.dateRange = {
            from: null,
            to: null
        };
        this.filters = {
            role: '',
            search: ''
        };
        this.usersPage = 1;
        this.usersLimit = 20;
        this.totalPages = 1;
        this.totalRecords = 0;
        this.searchTimeout = null;
        
        // Logs pagination state
        this.logsPage = 1;
        this.logsLimit = 20;
        this.logsTotalPages = 1;
        this.logsTotalRecords = 0;
        
        this.init();
    }

    init() {
        this.setDefaultDates();
        this.bindEvents();
        this.loadSystemStats();
        this.loadTabContent('users');
    }

    bindEvents() {
        // Tab switching
        document.querySelectorAll('[data-tab]').forEach(tab => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();
                const tabName = tab.getAttribute('data-tab');
                this.switchTab(tabName);
            });
        });

        // Filter events
        document.getElementById('applyFilters').addEventListener('click', () => {
            this.applyFilters();
        });

        document.getElementById('fromDate').addEventListener('change', (e) => {
            this.dateRange.from = e.target.value;
        });

        document.getElementById('toDate').addEventListener('change', (e) => {
            this.dateRange.to = e.target.value;
        });

        document.getElementById('roleFilter').addEventListener('change', (e) => {
            this.filters.role = e.target.value;
        });

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', (e) => {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.filters.search = e.target.value;
                this.usersPage = 1; // Reset to first page
                if (this.currentTab === 'users') {
                    this.loadUsers();
                }
            }, 500); // Debounce search
        });

        // Records per page
        document.getElementById('recordsPerPage').addEventListener('change', (e) => {
            this.usersLimit = parseInt(e.target.value);
            this.usersPage = 1; // Reset to first page
            if (this.currentTab === 'users') {
                this.loadUsers();
            }
        });

        // Logs per page
        document.getElementById('logsPerPage').addEventListener('change', (e) => {
            this.logsLimit = parseInt(e.target.value);
            this.logsPage = 1; // Reset to first page
            if (this.currentTab === 'logs') {
                this.loadLogs();
            }
        });

        // Pagination events
        document.getElementById('prevPage').addEventListener('click', () => {
            if (this.usersPage > 1) {
                this.usersPage--;
                this.loadUsers();
            }
        });

        document.getElementById('nextPage').addEventListener('click', () => {
            if (this.usersPage < this.totalPages) {
                this.usersPage++;
                this.loadUsers();
            }
        });

        // Logs pagination events
        document.getElementById('logsPrevPage').addEventListener('click', () => {
            if (this.logsPage > 1) {
                this.logsPage--;
                this.loadLogs();
            }
        });

        document.getElementById('logsNextPage').addEventListener('click', () => {
            if (this.logsPage < this.logsTotalPages) {
                this.logsPage++;
                this.loadLogs();
            }
        });

        // Export events
        document.getElementById('exportExcel').addEventListener('click', () => {
            this.exportReport('excel');
        });

        document.getElementById('printReport').addEventListener('click', () => {
            this.printReport();
        });
    }

    setDefaultDates() {
        const today = new Date();
        const oneMonthAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
        
        const todayStr = today.toISOString().split('T')[0];
        const oneMonthAgoStr = oneMonthAgo.toISOString().split('T')[0];
        
        document.getElementById('fromDate').value = oneMonthAgoStr;
        document.getElementById('toDate').value = todayStr;
        
        this.dateRange.from = oneMonthAgoStr;
        this.dateRange.to = todayStr;
    }

    switchTab(tabName) {
        // Update tab navigation
        document.querySelectorAll('[data-tab]').forEach(tab => {
            tab.classList.remove('is-active');
        });
        document.querySelector(`[data-tab="${tabName}"]`).classList.add('is-active');

        // Show/hide tab content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.style.display = 'none';
            content.classList.remove('is-active');
        });
        
        const targetContent = document.getElementById(`${tabName}-tab`);
        targetContent.style.display = 'block';
        targetContent.classList.add('is-active');

        this.currentTab = tabName;
        this.loadTabContent(tabName);
    }

    async loadSystemStats() {
        try {
            const params = new URLSearchParams({
                action: 'system',
                report_type: 'status',
                from_date: this.dateRange.from,
                to_date: this.dateRange.to
            });

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderSystemStats(data.data);
            }
        } catch (error) {
            console.error('Error loading system stats:', error);
        }
    }

    async loadUsers() {
        try {
            const params = new URLSearchParams({
                action: 'system',
                report_type: 'users',
                from_date: this.dateRange.from,
                to_date: this.dateRange.to,
                role: this.filters.role,
                search: this.filters.search,
                page: this.usersPage,
                limit: this.usersLimit
            });

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderUsers(data.data);
            }
        } catch (error) {
            console.error('Error loading users:', error);
        }
    }

    renderSystemStats(stats) {
        const container = document.getElementById('systemStats');
        
        container.innerHTML = `
            <div class="stat-card">
                <span class="stat-number">${stats.total_users || 0}</span>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <span class="stat-number">${stats.active_users || 0}</span>
                <div class="stat-label">Active Users</div>
            </div>
            <div class="stat-card">
                <span class="stat-number">${stats.admin_users || 0}</span>
                <div class="stat-label">Admin Users</div>
            </div>
            <div class="stat-card">
                <span class="stat-number">${stats.daily_active || 0}</span>
                <div class="stat-label">Daily Active</div>
            </div>
            <div class="stat-card">
                <span class="stat-number">${stats.weekly_active || 0}</span>
                <div class="stat-label">Weekly Active</div>
            </div>
            <div class="stat-card">
                <span class="stat-number">${stats.monthly_active || 0}</span>
                <div class="stat-label">Monthly Active</div>
            </div>
        `;
    }

    async loadTabContent(tabName) {
        this.showLoading();
        
        try {
            switch (tabName) {
                case 'users':
                    await this.loadUsers();
                    break;
                case 'activity':
                    await this.loadActivity();
                    break;
                case 'logs':
                    await this.loadLogs();
                    break;
                case 'performance':
                    await this.loadPerformance();
                    break;
                case 'security':
                    await this.loadSecurity();
                    break;
            }
        } catch (error) {
            console.error(`Error loading ${tabName} content:`, error);
        } finally {
            this.hideLoading();
        }
    }

    async loadUsers() {
        try {
            const params = new URLSearchParams({
                action: 'system',
                report_type: 'overview',
                page: this.usersPage,
                limit: this.usersLimit,
                search: this.filters.search,
                role: this.filters.role
            });

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderUsersTable(data.data.users, data.data.page, data.data.total_pages, data.data.total);
                this.totalPages = data.data.total_pages;
                this.totalRecords = data.data.total;
                this.updatePaginationControls();
            }
        } catch (error) {
            console.error('Error loading users:', error);
        }
    }

    renderUsersTable(users, currentPage, totalPages, totalCount) {
        this.currentUsers = users;
        
        const tbody = document.getElementById('usersTableBody');
        tbody.innerHTML = '';
        
        if (users.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="has-text-centered">
                        <div class="content">
                            <p><strong>No users found</strong></p>
                            <p class="help">Try adjusting your filters or search terms.</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }
        
        users.forEach(user => {
            const row = `
                <tr>
                    <td>${user.id}</td>
                    <td><strong>${user.username}</strong></td>
                    <td>${user.email || 'N/A'}</td>
                    <td><span class="tag is-info">${user.role}</span></td>
                    <td><span class="tag ${user.status === 'active' ? 'is-success' : 'is-danger'}">${user.status}</span></td>
                    <td><span class="tag ${user.activity_level === 'Recent' ? 'is-success' : user.activity_level === 'Moderate' ? 'is-warning' : 'is-light'}">${user.activity_level}</span></td>
                    <td>${new Date(user.created_at).toLocaleDateString()}</td>
                    <td>
                        <button class="button is-link is-small" onclick="viewUser(${user.id})">
                            <span class="icon"><i class="fas fa-eye"></i></span>
                            <span>View</span>
                        </button>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    }

    updatePaginationControls() {
        const prevBtn = document.getElementById('prevPage');
        const nextBtn = document.getElementById('nextPage');
        const paginationInfo = document.getElementById('paginationInfo');
        const paginationList = document.getElementById('paginationList');

        // Update pagination info
        const start = ((this.usersPage - 1) * this.usersLimit) + 1;
        const end = Math.min(this.usersPage * this.usersLimit, this.totalRecords);
        paginationInfo.textContent = `Showing ${start}-${end} of ${this.totalRecords} users`;

        // Update previous/next buttons
        prevBtn.disabled = this.usersPage <= 1;
        nextBtn.disabled = this.usersPage >= this.totalPages;

        // Update pagination numbers
        paginationList.innerHTML = '';
        
        const maxPages = 5;
        let startPage = Math.max(1, this.usersPage - Math.floor(maxPages / 2));
        let endPage = Math.min(this.totalPages, startPage + maxPages - 1);
        
        if (endPage - startPage + 1 < maxPages) {
            startPage = Math.max(1, endPage - maxPages + 1);
        }

        for (let i = startPage; i <= endPage; i++) {
            const pageItem = document.createElement('li');
            const pageLink = document.createElement('a');
            pageLink.className = `pagination-link ${i === this.usersPage ? 'is-current' : ''}`;
            pageLink.textContent = i;
            pageLink.addEventListener('click', (e) => {
                e.preventDefault();
                this.usersPage = i;
                this.loadUsers();
            });
            pageItem.appendChild(pageLink);
            paginationList.appendChild(pageItem);
        }
    }

    async loadActivity() {
        // Load activity chart data
        try {
            const params = new URLSearchParams({
                action: 'system',
                report_type: 'activity'
            });

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderActivityChart(data.data);
            }
        } catch (error) {
            console.error('Error loading activity:', error);
        }
    }

    renderActivityChart(activity) {
        const ctx = document.getElementById('activityChart').getContext('2d');
        
        if (this.charts.activity) {
            this.charts.activity.destroy();
        }

        const labels = activity.map(a => a.date);
        const values = activity.map(a => a.active_users);

        this.charts.activity = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Active Users',
                    data: values,
                    borderColor: '#3273dc',
                    backgroundColor: 'rgba(50, 115, 220, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    async loadLogs() {
        try {
            const params = new URLSearchParams({
                action: 'system',
                report_type: 'logs',
                page: this.logsPage,
                limit: this.logsLimit
            });

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderLogsTable(data.data.logs, data.data.page, data.data.total_pages, data.data.total);
                this.logsTotalPages = data.data.total_pages;
                this.logsTotalRecords = data.data.total;
                this.updateLogsPaginationControls();
            }
        } catch (error) {
            console.error('Error loading logs:', error);
        }
    }

    renderLogsTable(logs, currentPage, totalPages, totalCount) {
        const tbody = document.getElementById('logsTableBody');
        tbody.innerHTML = '';
        
        if (logs.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="has-text-centered">
                        <div class="content">
                            <p><strong>No logs found</strong></p>
                            <p class="help">No system logs available at this time.</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }
        
        logs.forEach(log => {
            const row = `
                <tr>
                    <td>${log.id}</td>
                    <td>${log.timestamp}</td>
                    <td><span class="tag ${log.type === 'ERROR' ? 'is-danger' : log.type === 'WARNING' ? 'is-warning' : 'is-info'}">${log.type}</span></td>
                    <td>${log.action}</td>
                    <td>${log.user}</td>
                    <td>${log.ip_address}</td>
                    <td>${log.details}</td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    }

    updateLogsPaginationControls() {
        const prevBtn = document.getElementById('logsPrevPage');
        const nextBtn = document.getElementById('logsNextPage');
        const paginationInfo = document.getElementById('logsPaginationInfo');
        const paginationList = document.getElementById('logsPaginationList');

        // Update pagination info
        const start = ((this.logsPage - 1) * this.logsLimit) + 1;
        const end = Math.min(this.logsPage * this.logsLimit, this.logsTotalRecords);
        paginationInfo.textContent = `Showing ${start}-${end} of ${this.logsTotalRecords} logs`;

        // Update previous/next buttons
        prevBtn.disabled = this.logsPage <= 1;
        nextBtn.disabled = this.logsPage >= this.logsTotalPages;

        // Update pagination numbers
        paginationList.innerHTML = '';
        
        const maxPages = 5;
        let startPage = Math.max(1, this.logsPage - Math.floor(maxPages / 2));
        let endPage = Math.min(this.logsTotalPages, startPage + maxPages - 1);
        
        if (endPage - startPage + 1 < maxPages) {
            startPage = Math.max(1, endPage - maxPages + 1);
        }

        for (let i = startPage; i <= endPage; i++) {
            const pageItem = document.createElement('li');
            const pageLink = document.createElement('a');
            pageLink.className = `pagination-link ${i === this.logsPage ? 'is-current' : ''}`;
            pageLink.textContent = i;
            pageLink.addEventListener('click', (e) => {
                e.preventDefault();
                this.logsPage = i;
                this.loadLogs();
            });
            pageItem.appendChild(pageLink);
            paginationList.appendChild(pageItem);
        }
    }

    async loadPerformance() {
        try {
            const params = new URLSearchParams({
                action: 'system',
                report_type: 'performance'
            });

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderPerformanceCharts(data.data);
            }
        } catch (error) {
            console.error('Error loading performance:', error);
        }
    }

    renderPerformanceCharts(performance) {
        // Performance chart
        const perfCtx = document.getElementById('performanceChart').getContext('2d');
        
        if (this.charts.performance) {
            this.charts.performance.destroy();
        }

        const labels = performance.map(p => p.time);
        const responseTime = performance.map(p => p.response_time);

        this.charts.performance = new Chart(perfCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Response Time (s)',
                    data: responseTime,
                    borderColor: '#ff6b6b',
                    backgroundColor: 'rgba(255, 107, 107, 0.1)',
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Resource usage chart
        const resourceCtx = document.getElementById('resourceChart').getContext('2d');
        
        if (this.charts.resource) {
            this.charts.resource.destroy();
        }

        const memoryUsage = performance.map(p => p.memory_usage);
        const cpuUsage = performance.map(p => p.cpu_usage);

        this.charts.resource = new Chart(resourceCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Memory Usage (%)',
                    data: memoryUsage,
                    borderColor: '#4ecdc4',
                    backgroundColor: 'rgba(78, 205, 196, 0.1)',
                    fill: false
                }, {
                    label: 'CPU Usage (%)',
                    data: cpuUsage,
                    borderColor: '#ffe66d',
                    backgroundColor: 'rgba(255, 230, 109, 0.1)',
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    async loadSecurity() {
        try {
            const params = new URLSearchParams({
                action: 'system',
                report_type: 'security'
            });

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderSecurityData(data.data);
            }
        } catch (error) {
            console.error('Error loading security data:', error);
        }
    }

    renderSecurityData(security) {
        const container = document.getElementById('securityStats');
        
        container.innerHTML = `
            <div class="content">
                <h6 class="subtitle is-6">Failed Login Attempts</h6>
                <p><strong>Last 24h:</strong> ${security.failed_logins.last_24h}</p>
                <p><strong>Last Week:</strong> ${security.failed_logins.last_week}</p>
                <p><strong>Last Month:</strong> ${security.failed_logins.last_month}</p>
                
                <h6 class="subtitle is-6 mt-4">Password Changes</h6>
                <p><strong>Last 24h:</strong> ${security.password_changes.last_24h}</p>
                <p><strong>Last Week:</strong> ${security.password_changes.last_week}</p>
                <p><strong>Last Month:</strong> ${security.password_changes.last_month}</p>
                
                <h6 class="subtitle is-6 mt-4">Active Sessions</h6>
                <p><strong>Current:</strong> ${security.active_sessions}</p>
            </div>
        `;

        // Render security events table
        const tbody = document.getElementById('securityEventsBody');
        tbody.innerHTML = '';
        
        security.security_events.slice(0, 10).forEach(event => {
            const row = `
                <tr>
                    <td>${event.timestamp}</td>
                    <td><span class="tag is-info">${event.type}</span></td>
                    <td>${event.user}</td>
                    <td><span class="tag ${event.severity === 'High' ? 'is-danger' : event.severity === 'Medium' ? 'is-warning' : 'is-light'}">${event.severity}</span></td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    }

    applyFilters() {
        this.dateRange.from = document.getElementById('fromDate').value;
        this.dateRange.to = document.getElementById('toDate').value;
        this.filters.role = document.getElementById('roleFilter').value;
        this.filters.search = document.getElementById('searchInput').value;
        
        this.usersPage = 1; // Reset pagination
        this.loadSystemStats();
        this.loadTabContent(this.currentTab);
        
        this.showNotification('Filters applied successfully', 'is-success');
    }

    exportReport(format) {
        const params = new URLSearchParams({
            action: 'export_system',
            format: format,
            report_type: this.currentTab,
            from_date: this.dateRange.from,
            to_date: this.dateRange.to,
            role: this.filters.role
        });

        const link = document.createElement('a');
        link.href = `controllers/ReportsController.php?${params}`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        this.showNotification(`${format.toUpperCase()} export initiated`, 'is-success');
    }

    printReport() {
        const currentData = this.currentUsers || [];
        
        if (currentData.length === 0) {
            alert('No data to print. Please load data first.');
            return;
        }
        
        this.generatePrintContent(currentData);
        window.print();
    }
    
    generatePrintContent(users) {
        const printArea = document.getElementById('systemPrintArea');
        const currentDate = new Date().toLocaleDateString();
        const currentDateTime = new Date().toLocaleString();
        
        let usersHtml = '';
        if (users && users.length > 0) {
            usersHtml = `
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Activity Level</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            users.forEach((user) => {
                const createdDate = user.created_at ? new Date(user.created_at).toLocaleDateString() : 'N/A';
                usersHtml += `
                    <tr>
                        <td>#${user.id}</td>
                        <td class="user-name">${user.username}</td>
                        <td>${user.email || 'N/A'}</td>
                        <td>${user.role}</td>
                        <td>${user.status}</td>
                        <td>${user.activity_level}</td>
                        <td>${createdDate}</td>
                    </tr>
                `;
            });
            
            usersHtml += `</tbody></table>`;
        } else {
            usersHtml = '<p>No user data available.</p>';
        }
        
        printArea.innerHTML = `
            <div class="print-header">
                <div class="print-title">Hospital Management System</div>
                <div class="print-subtitle">System Reports - ${this.currentTab.charAt(0).toUpperCase() + this.currentTab.slice(1)}</div>
            </div>
            
            <div class="print-info">
                <h3>Report Information</h3>
                <div class="print-info-grid">
                    <div><strong>Report Date:</strong> ${currentDate}</div>
                    <div><strong>Total Records:</strong> ${users.length}</div>
                    <div><strong>Date Range:</strong> ${this.dateRange.from || 'All'} to ${this.dateRange.to || 'All'}</div>
                    <div><strong>Role Filter:</strong> ${this.filters.role || 'All Roles'}</div>
                </div>
            </div>
            
            <div class="print-users">
                <h3>System ${this.currentTab.charAt(0).toUpperCase() + this.currentTab.slice(1)} (${users.length} records)</h3>
                ${usersHtml}
            </div>
            
            <div class="print-footer">
                <div>Report ID: SYS-${Date.now()}</div>
                <div>Generated on: ${currentDateTime} | Hospital Management System</div>
                <div style="margin-top: 4px; font-weight: bold;">🔧 System Administration Report - Confidential</div>
            </div>
        `;
    }

    showLoading() {
        document.getElementById('loadingSpinner').style.display = 'flex';
    }

    hideLoading() {
        document.getElementById('loadingSpinner').style.display = 'none';
    }

    showNotification(message, type = 'is-info') {
        const notification = document.createElement('div');
        notification.className = `notification ${type} is-light`;
        notification.innerHTML = `
            <button class="delete"></button>
            ${message}
        `;

        document.body.appendChild(notification);

        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.zIndex = '9999';
        notification.style.maxWidth = '400px';

        notification.querySelector('.delete').addEventListener('click', () => {
            notification.remove();
        });

        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
}

// Global functions
function viewUser(userId) {
    openUserModal(userId);
}

async function openUserModal(userId) {
    const modal = document.getElementById('userModal');
    const modalContent = document.getElementById('userModalContent');
    
    // Show modal
    modal.classList.add('is-active');
    
    // Show loading state
    modalContent.innerHTML = `
        <div class="has-text-centered">
            <span class="icon is-large">
                <i class="fas fa-spinner fa-pulse fa-2x"></i>
            </span>
            <p class="mt-2">Loading user details...</p>
        </div>
    `;
    
    try {
        // Load user details
        const response = await fetch(`controllers/ReportsController.php?action=user_details&user_id=${userId}`);
        const data = await response.json();
        
        if (data.success && data.data) {
            renderUserDetails(data.data);
        } else {
            showUserError(data.message || 'Failed to load user details.');
        }
    } catch (error) {
        console.error('Error loading user details:', error);
        showUserError('An error occurred while loading user details.');
    }
}

function renderUserDetails(user) {
    const modalContent = document.getElementById('userModalContent');
    
    const createdDate = user.created_at ? new Date(user.created_at).toLocaleDateString() : 'N/A';
    const lastLogin = user.last_login ? new Date(user.last_login).toLocaleDateString() : 'Never';
    
    modalContent.innerHTML = `
        <div class="columns">
            <div class="column">
                <div class="card user-info-card">
                    <div class="card-header">
                        <p class="card-header-title">
                            <span class="icon">
                                <i class="fas fa-user-circle"></i>
                            </span>
                            User Information
                        </p>
                    </div>
                    <div class="card-content">
                        <div class="content">
                            <div class="field">
                                <label class="label">Username</label>
                                <p class="is-size-5"><strong>${user.username}</strong></p>
                            </div>
                            <div class="columns">
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Role</label>
                                        <p>
                                            <span class="tag is-info">
                                                <span class="icon">
                                                    <i class="fas fa-user-tag"></i>
                                                </span>
                                                <span>${user.role}</span>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Status</label>
                                        <p>
                                            <span class="tag ${user.status === 'active' ? 'is-success' : 'is-danger'}">
                                                <span class="icon">
                                                    <i class="fas fa-${user.status === 'active' ? 'check-circle' : 'times-circle'}"></i>
                                                </span>
                                                <span>${user.status}</span>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="field">
                                <label class="label">Email</label>
                                <p>
                                    <span class="icon">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    ${user.email || 'N/A'}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="column">
                <div class="card user-info-card">
                    <div class="card-header">
                        <p class="card-header-title">
                            <span class="icon">
                                <i class="fas fa-info-circle"></i>
                            </span>
                            Activity Information
                        </p>
                    </div>
                    <div class="card-content">
                        <div class="content">
                            <div class="field">
                                <label class="label">Activity Level</label>
                                <p>
                                    <span class="tag ${user.activity_level === 'Recent' ? 'is-success' : user.activity_level === 'Moderate' ? 'is-warning' : 'is-light'}">
                                        ${user.activity_level}
                                    </span>
                                </p>
                            </div>
                            <div class="field">
                                <label class="label">Created Date</label>
                                <p>
                                    <span class="icon">
                                        <i class="fas fa-calendar-plus"></i>
                                    </span>
                                    ${createdDate}
                                </p>
                            </div>
                            <div class="field">
                                <label class="label">Last Login</label>
                                <p>
                                    <span class="icon">
                                        <i class="fas fa-sign-in-alt"></i>
                                    </span>
                                    ${lastLogin}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card user-info-card mt-4">
            <div class="card-header">
                <p class="card-header-title">
                    <span class="icon">
                        <i class="fas fa-cogs"></i>
                    </span>
                    System Information
                </p>
            </div>
            <div class="card-content">
                <div class="content">
                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label">User ID</label>
                                <p><code>#${user.user_id}</code></p>
                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                <label class="label">Account Status</label>
                                <p><span class="tag ${user.status === 'active' ? 'is-success' : 'is-danger'}">${user.status === 'active' ? 'Active' : 'Inactive'}</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function showUserError(message) {
    const modalContent = document.getElementById('userModalContent');
    modalContent.innerHTML = `
        <div class="notification is-danger">
            <span class="icon">
                <i class="fas fa-exclamation-triangle"></i>
            </span>
            <span>${message}</span>
        </div>
    `;
}

function closeUserModal() {
    const modal = document.getElementById('userModal');
    modal.classList.remove('is-active');
}

// Close modal when pressing Escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeUserModal();
    }
});

// Initialize the reports manager when the page loads
document.addEventListener('DOMContentLoaded', () => {
    window.systemReportsManager = new SystemReportsManager();
});
</script>

    </div> <!-- End of container from header.php -->
</body>
</html> 