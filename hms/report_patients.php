<?php
require_once 'includes/header.php';
?>

<style>
/* Report-specific styling */
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

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1001;
}

.patient-info-card {
    border: 1px solid #e1e5e9;
    border-radius: 8px;
}

.patient-info-card .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.patient-info-card .card-header-title {
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
    
    #patientPrintArea,
    #patientPrintArea * {
        visibility: visible;
    }
    
    #patientPrintArea {
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
    
    .print-patients {
        margin-bottom: 12px;
    }
    
    .print-patients h3 {
        margin: 0 0 8px 0 !important;
        font-size: 12px;
        background-color: #f0f0f0;
        padding: 4px 8px;
        border: 1px solid #000;
    }
    
    .patients-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #000;
        font-size: 9px;
    }
    
    .patients-table th,
    .patients-table td {
        border: 1px solid #ccc;
        padding: 3px 4px;
        text-align: left;
        vertical-align: top;
    }
    
    .patients-table th {
        background-color: #f5f5f5;
        font-weight: bold;
        font-size: 9px;
    }
    
    .patient-name {
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
                    <li class="is-active"><a href="#"><span class="icon"><i class="fas fa-users"></i></span><span>Patient Reports</span></a></li>
                </ul>
            </nav>
            <h1 class="title is-3">
                <span class="icon">
                    <i class="fas fa-users"></i>
                </span>
                Patient Reports & Analytics
            </h1>
            <p class="subtitle">Comprehensive patient data analysis and reporting</p>
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
                        <input class="input" type="text" id="searchInput" placeholder="Search patients..." />
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
                    <label class="label">Gender</label>
                    <div class="control">
                        <div class="select is-fullwidth">
                            <select id="genderFilter">
                                <option value="">All Genders</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
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
    <div class="stats-grid" id="patientStats">
        <!-- Stats will be loaded here -->
    </div>

    <!-- Report Tabs -->
    <div class="report-tabs">
        <div class="tabs is-boxed">
            <ul>
                <li class="is-active" data-tab="demographics">
                    <a>
                        <span class="icon"><i class="fas fa-user-friends"></i></span>
                        <span>Demographics</span>
                    </a>
                </li>
                <li data-tab="registrations">
                    <a>
                        <span class="icon"><i class="fas fa-user-plus"></i></span>
                        <span>Registrations</span>
                    </a>
                </li>
                <li data-tab="age-gender">
                    <a>
                        <span class="icon"><i class="fas fa-chart-pie"></i></span>
                        <span>Age & Gender</span>
                    </a>
                </li>
                <li data-tab="summary">
                    <a>
                        <span class="icon"><i class="fas fa-chart-bar"></i></span>
                        <span>Summary</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab Content -->
    <div id="tabContent">
        <!-- Demographics Tab -->
        <div id="demographics-tab" class="tab-content is-active">
            <div class="chart-box">
                <div class="columns is-vcentered mb-4">
                    <div class="column">
                        <h4 class="title is-5">Patient Demographics</h4>
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
                                <th>Name</th>
                                <th>Age</th>
                                <th>Gender</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Registration Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="demographicsTableBody">
                            <!-- Data will be loaded here -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination Controls -->
                <div class="columns is-vcentered mt-4">
                    <div class="column">
                        <p class="help" id="paginationInfo">
                            Showing 0 of 0 patients
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

        <!-- Registrations Tab -->
        <div id="registrations-tab" class="tab-content" style="display: none;">
            <div class="chart-box">
                <h4 class="title is-5 mb-4">Patient Registrations Over Time</h4>
                <div class="chart-container">
                    <canvas id="registrationsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Age & Gender Tab -->
        <div id="age-gender-tab" class="tab-content" style="display: none;">
            <div class="columns">
                <div class="column">
                    <div class="chart-box">
                        <h4 class="title is-5 mb-4">Age Distribution</h4>
                        <div class="chart-container">
                            <canvas id="ageChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="column">
                    <div class="chart-box">
                        <h4 class="title is-5 mb-4">Gender Distribution</h4>
                        <div class="chart-container">
                            <canvas id="genderChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Tab -->
        <div id="summary-tab" class="tab-content" style="display: none;">
            <div class="chart-box">
                <h4 class="title is-5 mb-4">Patient Summary Statistics</h4>
                <div class="columns">
                    <div class="column">
                        <div class="content">
                            <h5 class="subtitle is-6">Key Metrics</h5>
                            <div id="summaryStats">
                                <!-- Summary data will be loaded here -->
                            </div>
                        </div>
                    </div>
                    <div class="column">
                        <div class="chart-container">
                            <canvas id="summaryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Patient Details Modal -->
<div class="modal" id="patientModal">
    <div class="modal-background" onclick="closePatientModal()"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">
                <span class="icon">
                    <i class="fas fa-user"></i>
                </span>
                Patient Details
            </p>
            <button class="delete" aria-label="close" onclick="closePatientModal()"></button>
        </header>
        <section class="modal-card-body">
            <div id="patientModalContent">
                <!-- Patient details will be loaded here -->
                <div class="has-text-centered">
                    <span class="icon is-large">
                        <i class="fas fa-spinner fa-pulse fa-2x"></i>
                    </span>
                    <p class="mt-2">Loading patient details...</p>
                </div>
            </div>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-light" onclick="closePatientModal()">Close</button>
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
<div id="patientPrintArea" style="display: none;">
    <!-- Print content will be generated here -->
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- JavaScript -->
<script>
class PatientReportsManager {
    constructor() {
        this.currentTab = 'demographics';
        this.charts = {};
        this.dateRange = {
            from: null,
            to: null
        };
        this.filters = {
            gender: '',
            search: ''
        };
        this.demographicsPage = 1;
        this.demographicsLimit = 20;
        this.totalPages = 1;
        this.totalRecords = 0;
        this.searchTimeout = null;
        
        this.init();
    }

    init() {
        this.setDefaultDates();
        this.bindEvents();
        this.loadPatientStats();
        this.loadTabContent('demographics');
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

        document.getElementById('genderFilter').addEventListener('change', (e) => {
            this.filters.gender = e.target.value;
        });

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', (e) => {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.filters.search = e.target.value;
                this.demographicsPage = 1; // Reset to first page
                if (this.currentTab === 'demographics') {
                    this.loadDemographics();
                }
            }, 500); // Debounce search
        });

        // Records per page
        document.getElementById('recordsPerPage').addEventListener('change', (e) => {
            this.demographicsLimit = parseInt(e.target.value);
            this.demographicsPage = 1; // Reset to first page
            if (this.currentTab === 'demographics') {
                this.loadDemographics();
            }
        });

        // Pagination events
        document.getElementById('prevPage').addEventListener('click', () => {
            if (this.demographicsPage > 1) {
                this.demographicsPage--;
                this.loadDemographics();
            }
        });

        document.getElementById('nextPage').addEventListener('click', () => {
            if (this.demographicsPage < this.totalPages) {
                this.demographicsPage++;
                this.loadDemographics();
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
        const oneYearAgo = new Date(today.getTime() - (365 * 24 * 60 * 60 * 1000));
        
        const todayStr = today.toISOString().split('T')[0];
        const oneYearAgoStr = oneYearAgo.toISOString().split('T')[0];
        
        document.getElementById('fromDate').value = oneYearAgoStr;
        document.getElementById('toDate').value = todayStr;
        
        this.dateRange.from = oneYearAgoStr;
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

    async loadPatientStats() {
        try {
            const params = new URLSearchParams({
                action: 'patients',
                report_type: 'status',
                from_date: this.dateRange.from,
                to_date: this.dateRange.to
            });

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderPatientStats(data.data);
            }
        } catch (error) {
            console.error('Error loading patient stats:', error);
        }
    }

    renderPatientStats(stats) {
        const container = document.getElementById('patientStats');
        
        container.innerHTML = `
            <div class="stat-card">
                <span class="stat-number">${stats.total_patients || 0}</span>
                <div class="stat-label">Total Patients</div>
            </div>
            <div class="stat-card">
                <span class="stat-number">${stats.male_patients || 0}</span>
                <div class="stat-label">Male Patients</div>
            </div>
            <div class="stat-card">
                <span class="stat-number">${stats.female_patients || 0}</span>
                <div class="stat-label">Female Patients</div>
            </div>
            <div class="stat-card">
                <span class="stat-number">${stats.minors || 0}</span>
                <div class="stat-label">Minors (< 18)</div>
            </div>
            <div class="stat-card">
                <span class="stat-number">${stats.adults || 0}</span>
                <div class="stat-label">Adults (18-65)</div>
            </div>
            <div class="stat-card">
                <span class="stat-number">${stats.seniors || 0}</span>
                <div class="stat-label">Seniors (65+)</div>
            </div>
        `;
    }

    async loadTabContent(tabName) {
        this.showLoading();
        
        try {
            switch (tabName) {
                case 'demographics':
                    await this.loadDemographics();
                    break;
                case 'registrations':
                    await this.loadRegistrations();
                    break;
                case 'age-gender':
                    await this.loadAgeGenderCharts();
                    break;
                case 'summary':
                    await this.loadSummary();
                    break;
            }
        } catch (error) {
            console.error(`Error loading ${tabName} content:`, error);
        } finally {
            this.hideLoading();
        }
    }

    async loadDemographics() {
        try {
            const params = new URLSearchParams({
                action: 'patients',
                report_type: 'demographics',
                from_date: this.dateRange.from,
                to_date: this.dateRange.to,
                page: this.demographicsPage,
                limit: this.demographicsLimit
            });

            if (this.filters.gender) {
                params.append('gender', this.filters.gender);
            }

            if (this.filters.search) {
                params.append('search', this.filters.search);
            }

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            const data = await response.json();

            if (data.success) {
                if (data.data.patients) {
                    // New pagination format
                    this.renderDemographicsTable(data.data.patients, data.data.page, data.data.total_pages, data.data.total);
                    this.totalPages = data.data.total_pages;
                    this.totalRecords = data.data.total;
                    this.updatePaginationControls();
                } else {
                    // Fallback for old format
                    this.renderDemographicsTable(data.data, 1, 1, data.data.length);
                    this.totalPages = 1;
                    this.totalRecords = data.data.length;
                }
            } else {
                this.showNotification('Error loading demographics: ' + data.message, 'is-danger');
            }
        } catch (error) {
            console.error('Error loading demographics:', error);
            this.showNotification('Failed to load patient demographics', 'is-danger');
        }
    }

    renderDemographicsTable(patients, currentPage, totalPages, totalCount) {
        // Store current patients for printing
        this.currentPatients = patients;
        
        const tbody = document.getElementById('demographicsTableBody');
        tbody.innerHTML = '';
        
        if (patients.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="has-text-centered">
                        <div class="content">
                            <p><strong>No patients found</strong></p>
                            <p class="help">Try adjusting your filters or search terms.</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }
        
        patients.forEach(patient => {
            const row = `
                <tr>
                    <td>${patient.id}</td>
                    <td><strong>${patient.first_name} ${patient.last_name}</strong></td>
                    <td>${patient.age || 'N/A'}</td>
                    <td><span class="tag ${patient.gender === 'male' ? 'is-info' : 'is-danger'}">${patient.gender || 'N/A'}</span></td>
                    <td>${patient.phone || 'N/A'}</td>
                    <td>${patient.email || 'N/A'}</td>
                    <td>${new Date(patient.created_at).toLocaleDateString()}</td>
                    <td>
                        <div class="buttons are-small">
                            <button class="button is-link is-small" onclick="viewPatient(${patient.id})">
                                <span class="icon"><i class="fas fa-eye"></i></span>
                                <span>View</span>
                            </button>
                        </div>
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
        const start = ((this.demographicsPage - 1) * this.demographicsLimit) + 1;
        const end = Math.min(this.demographicsPage * this.demographicsLimit, this.totalRecords);
        paginationInfo.textContent = `Showing ${start}-${end} of ${this.totalRecords} patients`;

        // Update previous/next buttons
        prevBtn.disabled = this.demographicsPage <= 1;
        nextBtn.disabled = this.demographicsPage >= this.totalPages;

        // Update pagination numbers
        paginationList.innerHTML = '';
        
        const maxPages = 5;
        let startPage = Math.max(1, this.demographicsPage - Math.floor(maxPages / 2));
        let endPage = Math.min(this.totalPages, startPage + maxPages - 1);
        
        if (endPage - startPage + 1 < maxPages) {
            startPage = Math.max(1, endPage - maxPages + 1);
        }

        for (let i = startPage; i <= endPage; i++) {
            const pageItem = document.createElement('li');
            const pageLink = document.createElement('a');
            pageLink.className = `pagination-link ${i === this.demographicsPage ? 'is-current' : ''}`;
            pageLink.textContent = i;
            pageLink.addEventListener('click', (e) => {
                e.preventDefault();
                this.demographicsPage = i;
                this.loadDemographics();
            });
            pageItem.appendChild(pageLink);
            paginationList.appendChild(pageItem);
        }
    }

    async loadRegistrations() {
        try {
            const params = new URLSearchParams({
                action: 'patients',
                report_type: 'registrations',
                from_date: this.dateRange.from,
                to_date: this.dateRange.to
            });

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderRegistrationsChart(data.data);
            }
        } catch (error) {
            console.error('Error loading registrations:', error);
        }
    }

    renderRegistrationsChart(registrations) {
        const ctx = document.getElementById('registrationsChart').getContext('2d');
        
        if (this.charts.registrations) {
            this.charts.registrations.destroy();
        }

        const labels = registrations.map(r => new Date(r.date).toLocaleDateString());
        const values = registrations.map(r => r.registrations);

        this.charts.registrations = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Patient Registrations',
                    data: values,
                    borderColor: '#3273dc',
                    backgroundColor: 'rgba(50, 115, 220, 0.1)',
                    fill: true,
                    tension: 0.4
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
    }

    async loadAgeGenderCharts() {
        try {
            const params = new URLSearchParams({
                action: 'patients',
                report_type: 'age_gender'
            });

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderAgeGenderCharts(data.data);
            }
        } catch (error) {
            console.error('Error loading age/gender data:', error);
        }
    }

    renderAgeGenderCharts(distribution) {
        // Age Chart
        const ageCtx = document.getElementById('ageChart').getContext('2d');
        
        if (this.charts.age) {
            this.charts.age.destroy();
        }

        const ageGroups = [...new Set(distribution.map(d => d.age_group))];
        const ageData = ageGroups.map(group => {
            return distribution.filter(d => d.age_group === group)
                            .reduce((sum, d) => sum + parseInt(d.count), 0);
        });

        this.charts.age = new Chart(ageCtx, {
            type: 'doughnut',
            data: {
                labels: ageGroups,
                datasets: [{
                    data: ageData,
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Gender Chart
        const genderCtx = document.getElementById('genderChart').getContext('2d');
        
        if (this.charts.gender) {
            this.charts.gender.destroy();
        }

        const genders = [...new Set(distribution.map(d => d.gender))];
        const genderData = genders.map(gender => {
            return distribution.filter(d => d.gender === gender)
                            .reduce((sum, d) => sum + parseInt(d.count), 0);
        });

        this.charts.gender = new Chart(genderCtx, {
            type: 'pie',
            data: {
                labels: genders.map(g => g.charAt(0).toUpperCase() + g.slice(1)),
                datasets: [{
                    data: genderData,
                    backgroundColor: ['#36A2EB', '#FF6384']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    async loadSummary() {
        // Summary data is the same as patient stats, just display differently
        const params = new URLSearchParams({
            action: 'patients',
            report_type: 'status'
        });

        const response = await fetch(`controllers/ReportsController.php?${params}`);
        const data = await response.json();

        if (data.success) {
            this.renderSummary(data.data);
        }
    }

    renderSummary(stats) {
        const container = document.getElementById('summaryStats');
        
        container.innerHTML = `
            <div class="content">
                <p><strong>Total Patients:</strong> ${stats.total_patients}</p>
                <p><strong>Male Patients:</strong> ${stats.male_patients} (${((stats.male_patients/stats.total_patients)*100).toFixed(1)}%)</p>
                <p><strong>Female Patients:</strong> ${stats.female_patients} (${((stats.female_patients/stats.total_patients)*100).toFixed(1)}%)</p>
                <p><strong>Minors (< 18):</strong> ${stats.minors}</p>
                <p><strong>Adults (18-65):</strong> ${stats.adults}</p>
                <p><strong>Seniors (65+):</strong> ${stats.seniors}</p>
            </div>
        `;

        // Summary chart
        const ctx = document.getElementById('summaryChart').getContext('2d');
        
        if (this.charts.summary) {
            this.charts.summary.destroy();
        }

        this.charts.summary = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Male', 'Female', 'Minors', 'Adults', 'Seniors'],
                datasets: [{
                    label: 'Count',
                    data: [stats.male_patients, stats.female_patients, stats.minors, stats.adults, stats.seniors],
                    backgroundColor: [
                        '#36A2EB',
                        '#FF6384',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    applyFilters() {
        this.dateRange.from = document.getElementById('fromDate').value;
        this.dateRange.to = document.getElementById('toDate').value;
        this.filters.gender = document.getElementById('genderFilter').value;
        this.filters.search = document.getElementById('searchInput').value;
        
        this.demographicsPage = 1; // Reset pagination
        this.loadPatientStats();
        this.loadTabContent(this.currentTab);
        
        this.showNotification('Filters applied successfully', 'is-success');
    }

    exportReport(format) {
        const params = new URLSearchParams({
            action: 'export_patients',
            format: format,
            report_type: this.currentTab,
            from_date: this.dateRange.from,
            to_date: this.dateRange.to
        });

        if (this.filters.gender) {
            params.append('gender', this.filters.gender);
        }

        const link = document.createElement('a');
        link.href = `controllers/ReportsController.php?${params}`;
        link.download = `patient_report_${this.currentTab}_${new Date().toISOString().split('T')[0]}.${format}`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        this.showNotification(`${format.toUpperCase()} export initiated`, 'is-success');
    }

    printReport() {
        // Get current patient data from the table
        const currentData = this.currentPatients || [];
        
        if (currentData.length === 0) {
            alert('No patient data to print. Please load data first.');
            return;
        }
        
        // Generate print content
        this.generatePrintContent(currentData);
        
        // Trigger print
        window.print();
    }
    
    generatePrintContent(patients) {
        const printArea = document.getElementById('patientPrintArea');
        const currentDate = new Date().toLocaleDateString();
        const currentDateTime = new Date().toLocaleString();
        
        let patientsHtml = '';
        if (patients && patients.length > 0) {
            patientsHtml = `
                <table class="patients-table">
                    <thead>
                        <tr>
                            <th style="width: 10%;">Patient ID</th>
                            <th style="width: 25%;">Name</th>
                            <th style="width: 8%;">Gender</th>
                            <th style="width: 8%;">Age</th>
                            <th style="width: 15%;">Phone</th>
                            <th style="width: 20%;">Email</th>
                            <th style="width: 14%;">Registration</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            patients.forEach((patient, index) => {
                const registrationDate = patient.created_at ? new Date(patient.created_at).toLocaleDateString() : 'N/A';
                patientsHtml += `
                    <tr>
                        <td>#${patient.id}</td>
                        <td class="patient-name">${this.escapeHtml(patient.first_name)} ${this.escapeHtml(patient.last_name)}</td>
                        <td>${patient.gender ? patient.gender.charAt(0).toUpperCase() + patient.gender.slice(1) : 'N/A'}</td>
                        <td>${patient.age || 'N/A'}</td>
                        <td>${this.escapeHtml(patient.phone || 'N/A')}</td>
                        <td>${this.escapeHtml(patient.email || 'N/A')}</td>
                        <td>${registrationDate}</td>
                    </tr>
                `;
            });
            
            patientsHtml += `
                    </tbody>
                </table>
            `;
        } else {
            patientsHtml = '<table class="patients-table"><tr><td colspan="7" style="text-align: center; font-style: italic;">No patient data available.</td></tr></table>';
        }
        
        printArea.innerHTML = `
            <div class="print-header">
                <div class="print-title">Hospital Management System</div>
                <div class="print-subtitle">Patient Demographics Report</div>
            </div>
            
            <div class="print-info">
                <h3>Report Information</h3>
                <div class="print-info-grid">
                    <div><strong>Report Date:</strong> ${currentDate}</div>
                    <div><strong>Total Patients:</strong> ${patients.length}</div>
                    <div><strong>Date Range:</strong> ${this.dateRange.from || 'All'} to ${this.dateRange.to || 'All'}</div>
                    <div><strong>Gender Filter:</strong> ${this.filters.gender || 'All Genders'}</div>
                </div>
            </div>
            
            <div class="print-patients">
                <h3>Patient Demographics (${patients.length} records)</h3>
                ${patientsHtml}
            </div>
            
            <div class="print-footer">
                <div>Report ID: RPT-${Date.now()}</div>
                <div>Generated on: ${currentDateTime} | Hospital Management System</div>
                <div style="margin-top: 4px; font-weight: bold;">📊 Patient Demographics Report - Confidential Medical Data</div>
            </div>
        `;
    }
    
    escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
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
function viewPatient(patientId) {
    openPatientModal(patientId);
}

async function openPatientModal(patientId) {
    const modal = document.getElementById('patientModal');
    const modalContent = document.getElementById('patientModalContent');
    
    // Show modal
    modal.classList.add('is-active');
    
    // Show loading state
    modalContent.innerHTML = `
        <div class="has-text-centered">
            <span class="icon is-large">
                <i class="fas fa-spinner fa-pulse fa-2x"></i>
            </span>
            <p class="mt-2">Loading patient details...</p>
        </div>
    `;
    
    try {
        // Load patient details
        const response = await fetch(`controllers/ReportsController.php?action=patient_details&patient_id=${patientId}`);
        const data = await response.json();
        
        if (data.success && data.data) {
            renderPatientDetails(data.data);
        } else {
            showPatientError(data.message || 'Failed to load patient details.');
        }
    } catch (error) {
        console.error('Error loading patient details:', error);
        showPatientError('An error occurred while loading patient details.');
    }
}

function renderPatientDetails(patient) {
    const modalContent = document.getElementById('patientModalContent');
    
    const age = patient.age || 'N/A';
    const formattedDate = patient.date_of_birth ? new Date(patient.date_of_birth).toLocaleDateString() : 'N/A';
    const registrationDate = patient.created_at ? new Date(patient.created_at).toLocaleDateString() : 'N/A';
    
    modalContent.innerHTML = `
        <div class="columns">
            <div class="column">
                <div class="card patient-info-card">
                    <div class="card-header">
                        <p class="card-header-title">
                            <span class="icon">
                                <i class="fas fa-user-circle"></i>
                            </span>
                            Personal Information
                        </p>
                    </div>
                    <div class="card-content">
                        <div class="content">
                            <div class="field">
                                <label class="label">Full Name</label>
                                <p class="is-size-5"><strong>${patient.first_name} ${patient.last_name}</strong></p>
                            </div>
                            <div class="columns">
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Gender</label>
                                        <p>
                                            <span class="tag ${patient.gender === 'male' ? 'is-info' : 'is-danger'}">
                                                <span class="icon">
                                                    <i class="fas fa-${patient.gender === 'male' ? 'mars' : 'venus'}"></i>
                                                </span>
                                                <span>${patient.gender ? patient.gender.charAt(0).toUpperCase() + patient.gender.slice(1) : 'N/A'}</span>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Age</label>
                                        <p><strong>${age} years old</strong></p>
                                    </div>
                                </div>
                            </div>
                            <div class="field">
                                <label class="label">Date of Birth</label>
                                <p>${formattedDate}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="column">
                <div class="card patient-info-card">
                    <div class="card-header">
                        <p class="card-header-title">
                            <span class="icon">
                                <i class="fas fa-address-book"></i>
                            </span>
                            Contact Information
                        </p>
                    </div>
                    <div class="card-content">
                        <div class="content">
                            <div class="field">
                                <label class="label">Phone Number</label>
                                <p>
                                    <span class="icon">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                    ${patient.phone || 'N/A'}
                                </p>
                            </div>
                            <div class="field">
                                <label class="label">Email Address</label>
                                <p>
                                    <span class="icon">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    ${patient.email || 'N/A'}
                                </p>
                            </div>
                            <div class="field">
                                <label class="label">Address</label>
                                <p>
                                    <span class="icon">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </span>
                                    ${patient.address || 'N/A'}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card patient-info-card mt-4">
            <div class="card-header">
                <p class="card-header-title">
                    <span class="icon">
                        <i class="fas fa-info-circle"></i>
                    </span>
                    System Information
                </p>
            </div>
            <div class="card-content">
                <div class="content">
                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label">Patient ID</label>
                                <p><code>#${patient.id}</code></p>
                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                <label class="label">Registration Date</label>
                                <p>${registrationDate}</p>
                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                <label class="label">Status</label>
                                <p><span class="tag is-success">Active</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function showPatientError(message) {
    const modalContent = document.getElementById('patientModalContent');
    modalContent.innerHTML = `
        <div class="notification is-danger">
            <span class="icon">
                <i class="fas fa-exclamation-triangle"></i>
            </span>
            <span>${message}</span>
        </div>
    `;
}

function closePatientModal() {
    const modal = document.getElementById('patientModal');
    modal.classList.remove('is-active');
}

// Close modal when pressing Escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closePatientModal();
    }
});

// Initialize the reports manager when the page loads
document.addEventListener('DOMContentLoaded', () => {
    window.patientReportsManager = new PatientReportsManager();
});
</script>

    </div> <!-- End of container from header.php -->
</body>
</html> 