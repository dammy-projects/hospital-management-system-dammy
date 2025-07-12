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
    border-left: 4px solid #9b59b6;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #9b59b6;
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

.prescription-status {
    font-weight: bold;
}

.status-active {
    color: #00b894;
}

.status-fulfilled {
    color: #0984e3;
}

.status-cancelled {
    color: #d63031;
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

.prescription-info-card {
    border: 1px solid #e1e5e9;
    border-radius: 8px;
}

.prescription-info-card .card-header {
    background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);
    color: white;
}

.prescription-info-card .card-header-title {
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
    
    #prescriptionPrintArea,
    #prescriptionPrintArea * {
        visibility: visible;
    }
    
    #prescriptionPrintArea {
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
    
    .print-prescriptions {
        margin-bottom: 12px;
    }
    
    .print-prescriptions h3 {
        margin: 0 0 8px 0 !important;
        font-size: 12px;
        background-color: #f0f0f0;
        padding: 4px 8px;
        border: 1px solid #000;
    }
    
    .prescriptions-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #000;
        font-size: 9px;
    }
    
    .prescriptions-table th,
    .prescriptions-table td {
        border: 1px solid #ccc;
        padding: 3px 4px;
        text-align: left;
        vertical-align: top;
    }
    
    .prescriptions-table th {
        background-color: #f5f5f5;
        font-weight: bold;
        font-size: 9px;
    }
    
    .prescription-patient {
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
                    <li class="is-active"><a href="#"><span class="icon"><i class="fas fa-prescription-bottle-alt"></i></span><span>Prescription Reports</span></a></li>
                </ul>
            </nav>
            <h1 class="title is-3">
                <span class="icon">
                    <i class="fas fa-prescription-bottle-alt"></i>
                </span>
                Prescription Reports & Analytics
            </h1>
            <p class="subtitle">Comprehensive prescription data analysis and reporting</p>
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
                        <input class="input" type="text" id="searchInput" placeholder="Search prescriptions..." />
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
                    <label class="label">Status</label>
                    <div class="control">
                        <div class="select is-fullwidth">
                            <select id="statusFilter">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="fulfilled">Fulfilled</option>
                                <option value="cancelled">Cancelled</option>
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
    <div class="stats-grid" id="prescriptionStats">
        <!-- Stats will be loaded here -->
    </div>

    <!-- Report Tabs -->
    <div class="report-tabs">
        <div class="tabs is-boxed">
            <ul>
                <li class="is-active" data-tab="overview">
                    <a>
                        <span class="icon"><i class="fas fa-list"></i></span>
                        <span>Overview</span>
                    </a>
                </li>
                <li data-tab="medicines">
                    <a>
                        <span class="icon"><i class="fas fa-pills"></i></span>
                        <span>Medicine Usage</span>
                    </a>
                </li>
                <li data-tab="doctors">
                    <a>
                        <span class="icon"><i class="fas fa-user-md"></i></span>
                        <span>Doctor Patterns</span>
                    </a>
                </li>
                <li data-tab="trends">
                    <a>
                        <span class="icon"><i class="fas fa-chart-line"></i></span>
                        <span>Trends</span>
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
        <!-- Overview Tab -->
        <div id="overview-tab" class="tab-content is-active">
            <div class="chart-box">
                <div class="columns is-vcentered mb-4">
                    <div class="column">
                        <h4 class="title is-5">Prescription Overview</h4>
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
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Medicines</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="overviewTableBody">
                            <!-- Data will be loaded here -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination Controls -->
                <div class="columns is-vcentered mt-4">
                    <div class="column">
                        <p class="help" id="paginationInfo">
                            Showing 0 of 0 prescriptions
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

        <!-- Medicine Usage Tab -->
        <div id="medicines-tab" class="tab-content" style="display: none;">
            <div class="chart-box">
                <h4 class="title is-5 mb-4">Medicine Usage Analysis</h4>
                <div class="chart-container">
                    <canvas id="medicineUsageChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Doctor Patterns Tab -->
        <div id="doctors-tab" class="tab-content" style="display: none;">
            <div class="columns">
                <div class="column">
                    <div class="chart-box">
                        <h4 class="title is-5 mb-4">Prescriptions by Doctor</h4>
                        <div class="chart-container">
                            <canvas id="doctorPrescriptionsChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="column">
                    <div class="chart-box">
                        <h4 class="title is-5 mb-4">Specialty Distribution</h4>
                        <div class="chart-container">
                            <canvas id="specialtyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trends Tab -->
        <div id="trends-tab" class="tab-content" style="display: none;">
            <div class="chart-box">
                <h4 class="title is-5 mb-4">Prescription Trends Over Time</h4>
                <div class="chart-container">
                    <canvas id="trendsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Summary Tab -->
        <div id="summary-tab" class="tab-content" style="display: none;">
            <div class="chart-box">
                <h4 class="title is-5 mb-4">Prescription Summary Statistics</h4>
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

<!-- Prescription Details Modal -->
<div class="modal" id="prescriptionModal">
    <div class="modal-background" onclick="closePrescriptionModal()"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">
                <span class="icon">
                    <i class="fas fa-prescription-bottle-alt"></i>
                </span>
                Prescription Details
            </p>
            <button class="delete" aria-label="close" onclick="closePrescriptionModal()"></button>
        </header>
        <section class="modal-card-body">
            <div id="prescriptionModalContent">
                <!-- Prescription details will be loaded here -->
                <div class="has-text-centered">
                    <span class="icon is-large">
                        <i class="fas fa-spinner fa-pulse fa-2x"></i>
                    </span>
                    <p class="mt-2">Loading prescription details...</p>
                </div>
            </div>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-light" onclick="closePrescriptionModal()">Close</button>
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
<div id="prescriptionPrintArea" style="display: none;">
    <!-- Print content will be generated here -->
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- JavaScript -->
<script>
class PrescriptionReportsManager {
    constructor() {
        this.currentTab = 'overview';
        this.charts = {};
        this.dateRange = {
            from: null,
            to: null
        };
        this.filters = {
            status: '',
            search: ''
        };
        this.overviewPage = 1;
        this.overviewLimit = 20;
        this.totalPages = 1;
        this.totalRecords = 0;
        this.searchTimeout = null;
        
        this.init();
    }

    init() {
        this.setDefaultDates();
        this.bindEvents();
        this.loadPrescriptionStats();
        this.loadTabContent('overview');
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

        document.getElementById('statusFilter').addEventListener('change', (e) => {
            this.filters.status = e.target.value;
        });

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', (e) => {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.filters.search = e.target.value;
                this.overviewPage = 1; // Reset to first page
                if (this.currentTab === 'overview') {
                    this.loadOverview();
                }
            }, 500); // Debounce search
        });

        // Records per page
        document.getElementById('recordsPerPage').addEventListener('change', (e) => {
            this.overviewLimit = parseInt(e.target.value);
            this.overviewPage = 1; // Reset to first page
            if (this.currentTab === 'overview') {
                this.loadOverview();
            }
        });

        // Pagination events
        document.getElementById('prevPage').addEventListener('click', () => {
            if (this.overviewPage > 1) {
                this.overviewPage--;
                this.loadOverview();
            }
        });

        document.getElementById('nextPage').addEventListener('click', () => {
            if (this.overviewPage < this.totalPages) {
                this.overviewPage++;
                this.loadOverview();
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

    async loadPrescriptionStats() {
        try {
            const params = new URLSearchParams({
                action: 'prescriptions',
                report_type: 'stats',
                from_date: this.dateRange.from,
                to_date: this.dateRange.to
            });

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderPrescriptionStats(data.data);
            }
        } catch (error) {
            console.error('Error loading prescription stats:', error);
        }
    }

    renderPrescriptionStats(stats) {
        const container = document.getElementById('prescriptionStats');
        
        container.innerHTML = `
            <div class="stat-card">
                <span class="stat-number">${stats.total_prescriptions || 0}</span>
                <div class="stat-label">Total Prescriptions</div>
            </div>
            <div class="stat-card">
                <span class="stat-number">${stats.active_prescriptions || 0}</span>
                <div class="stat-label">Active Prescriptions</div>
            </div>
            <div class="stat-card">
                <span class="stat-number">${stats.fulfilled_prescriptions || 0}</span>
                <div class="stat-label">Fulfilled Prescriptions</div>
            </div>
            <div class="stat-card">
                <span class="stat-number">${stats.cancelled_prescriptions || 0}</span>
                <div class="stat-label">Cancelled Prescriptions</div>
            </div>
            <div class="stat-card">
                <span class="stat-number">${stats.total_patients_with_prescriptions || 0}</span>
                <div class="stat-label">Patients with Prescriptions</div>
            </div>
            <div class="stat-card">
                <span class="stat-number">${stats.total_medicines_prescribed || 0}</span>
                <div class="stat-label">Total Medicines Prescribed</div>
            </div>
        `;
    }

    async loadTabContent(tabName) {
        this.showLoading();
        
        try {
            switch (tabName) {
                case 'overview':
                    await this.loadOverview();
                    break;
                case 'medicines':
                    await this.loadMedicineUsage();
                    break;
                case 'doctors':
                    await this.loadDoctorPatterns();
                    break;
                case 'trends':
                    await this.loadTrends();
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

    async loadOverview() {
        try {
            const params = new URLSearchParams({
                action: 'prescriptions',
                report_type: 'overview',
                from_date: this.dateRange.from,
                to_date: this.dateRange.to,
                page: this.overviewPage,
                limit: this.overviewLimit
            });

            if (this.filters.status) {
                params.append('status', this.filters.status);
            }

            if (this.filters.search) {
                params.append('search', this.filters.search);
            }

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderOverviewTable(data.data.prescriptions, data.data.page, data.data.total_pages, data.data.total);
                this.totalPages = data.data.total_pages;
                this.totalRecords = data.data.total;
                this.updatePaginationControls();
            } else {
                this.showNotification('Error loading overview: ' + data.message, 'is-danger');
            }
        } catch (error) {
            console.error('Error loading overview:', error);
            this.showNotification('Failed to load prescription overview', 'is-danger');
        }
    }

    renderOverviewTable(prescriptions, currentPage, totalPages, totalCount) {
        // Store current prescriptions for printing
        this.currentPrescriptions = prescriptions;
        
        const tbody = document.getElementById('overviewTableBody');
        tbody.innerHTML = '';
        
        if (prescriptions.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="has-text-centered">
                        <div class="content">
                            <p><strong>No prescriptions found</strong></p>
                            <p class="help">Try adjusting your filters or search terms.</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }
        
        prescriptions.forEach(prescription => {
            const statusClass = prescription.status === 'active' ? 'is-success' : 
                              prescription.status === 'fulfilled' ? 'is-info' : 'is-danger';
            
            const row = `
                <tr>
                    <td>${prescription.prescription_id}</td>
                    <td><strong>${prescription.patient_name}</strong></td>
                    <td>${prescription.doctor_name}<br><small class="has-text-grey">${prescription.doctor_specialty || ''}</small></td>
                    <td>${new Date(prescription.prescription_date).toLocaleDateString()}</td>
                    <td><span class="tag is-light">${prescription.total_medicines || 0} medicines</span></td>
                    <td><span class="tag ${statusClass}">${prescription.status}</span></td>
                    <td>
                        <div class="buttons are-small">
                            <button class="button is-link is-small" onclick="viewPrescription(${prescription.prescription_id})">
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
        const start = ((this.overviewPage - 1) * this.overviewLimit) + 1;
        const end = Math.min(this.overviewPage * this.overviewLimit, this.totalRecords);
        paginationInfo.textContent = `Showing ${start}-${end} of ${this.totalRecords} prescriptions`;

        // Update previous/next buttons
        prevBtn.disabled = this.overviewPage <= 1;
        nextBtn.disabled = this.overviewPage >= this.totalPages;

        // Update pagination numbers
        paginationList.innerHTML = '';
        
        const maxPages = 5;
        let startPage = Math.max(1, this.overviewPage - Math.floor(maxPages / 2));
        let endPage = Math.min(this.totalPages, startPage + maxPages - 1);
        
        if (endPage - startPage + 1 < maxPages) {
            startPage = Math.max(1, endPage - maxPages + 1);
        }

        for (let i = startPage; i <= endPage; i++) {
            const pageItem = document.createElement('li');
            const pageLink = document.createElement('a');
            pageLink.className = `pagination-link ${i === this.overviewPage ? 'is-current' : ''}`;
            pageLink.textContent = i;
            pageLink.addEventListener('click', (e) => {
                e.preventDefault();
                this.overviewPage = i;
                this.loadOverview();
            });
            pageItem.appendChild(pageLink);
            paginationList.appendChild(pageItem);
        }
    }

    async loadMedicineUsage() {
        try {
            const params = new URLSearchParams({
                action: 'prescriptions',
                report_type: 'medicine_usage',
                from_date: this.dateRange.from,
                to_date: this.dateRange.to
            });

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderMedicineUsageChart(data.data);
            }
        } catch (error) {
            console.error('Error loading medicine usage:', error);
        }
    }

    renderMedicineUsageChart(medicineData) {
        const ctx = document.getElementById('medicineUsageChart').getContext('2d');
        
        if (this.charts.medicineUsage) {
            this.charts.medicineUsage.destroy();
        }

        const labels = medicineData.map(m => m.medicine_name);
        const values = medicineData.map(m => m.prescription_count);

        this.charts.medicineUsage = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Times Prescribed',
                    data: values,
                    backgroundColor: '#9b59b6',
                    borderColor: '#8e44ad',
                    borderWidth: 1
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

    async loadDoctorPatterns() {
        try {
            const params = new URLSearchParams({
                action: 'prescriptions',
                report_type: 'doctor_patterns',
                from_date: this.dateRange.from,
                to_date: this.dateRange.to
            });

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderDoctorPatternsCharts(data.data);
            }
        } catch (error) {
            console.error('Error loading doctor patterns:', error);
        }
    }

    renderDoctorPatternsCharts(doctorData) {
        // Doctor Prescriptions Chart
        const doctorCtx = document.getElementById('doctorPrescriptionsChart').getContext('2d');
        
        if (this.charts.doctorPrescriptions) {
            this.charts.doctorPrescriptions.destroy();
        }

        const doctorLabels = doctorData.doctors.map(d => d.doctor_name);
        const doctorValues = doctorData.doctors.map(d => d.prescription_count);

        this.charts.doctorPrescriptions = new Chart(doctorCtx, {
            type: 'bar',
            data: {
                labels: doctorLabels,
                datasets: [{
                    label: 'Prescriptions',
                    data: doctorValues,
                    backgroundColor: '#3498db',
                    borderColor: '#2980b9',
                    borderWidth: 1
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

        // Specialty Chart
        const specialtyCtx = document.getElementById('specialtyChart').getContext('2d');
        
        if (this.charts.specialty) {
            this.charts.specialty.destroy();
        }

        const specialtyLabels = doctorData.specialties.map(s => s.specialty);
        const specialtyValues = doctorData.specialties.map(s => s.prescription_count);

        this.charts.specialty = new Chart(specialtyCtx, {
            type: 'doughnut',
            data: {
                labels: specialtyLabels,
                datasets: [{
                    data: specialtyValues,
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    async loadTrends() {
        try {
            const params = new URLSearchParams({
                action: 'prescriptions',
                report_type: 'trends',
                from_date: this.dateRange.from,
                to_date: this.dateRange.to
            });

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderTrendsChart(data.data);
            }
        } catch (error) {
            console.error('Error loading trends:', error);
        }
    }

    renderTrendsChart(trendsData) {
        const ctx = document.getElementById('trendsChart').getContext('2d');
        
        if (this.charts.trends) {
            this.charts.trends.destroy();
        }

        const labels = trendsData.map(t => new Date(t.date).toLocaleDateString());
        const values = trendsData.map(t => t.prescription_count);

        this.charts.trends = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Prescriptions',
                    data: values,
                    borderColor: '#9b59b6',
                    backgroundColor: 'rgba(155, 89, 182, 0.1)',
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

    async loadSummary() {
        // Summary data is the same as prescription stats, just display differently
        const params = new URLSearchParams({
            action: 'prescriptions',
            report_type: 'summary',
            from_date: this.dateRange.from,
            to_date: this.dateRange.to
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
                <p><strong>Total Prescriptions:</strong> ${stats.total_prescriptions}</p>
                <p><strong>Active Prescriptions:</strong> ${stats.active_prescriptions} (${((stats.active_prescriptions/stats.total_prescriptions)*100).toFixed(1)}%)</p>
                <p><strong>Fulfilled Prescriptions:</strong> ${stats.fulfilled_prescriptions} (${((stats.fulfilled_prescriptions/stats.total_prescriptions)*100).toFixed(1)}%)</p>
                <p><strong>Cancelled Prescriptions:</strong> ${stats.cancelled_prescriptions} (${((stats.cancelled_prescriptions/stats.total_prescriptions)*100).toFixed(1)}%)</p>
                <p><strong>Patients with Prescriptions:</strong> ${stats.total_patients_with_prescriptions}</p>
                <p><strong>Average Medicines per Prescription:</strong> ${(stats.total_medicines_prescribed / stats.total_prescriptions).toFixed(1)}</p>
            </div>
        `;

        // Summary chart
        const ctx = document.getElementById('summaryChart').getContext('2d');
        
        if (this.charts.summary) {
            this.charts.summary.destroy();
        }

        this.charts.summary = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Active', 'Fulfilled', 'Cancelled'],
                datasets: [{
                    data: [stats.active_prescriptions, stats.fulfilled_prescriptions, stats.cancelled_prescriptions],
                    backgroundColor: [
                        '#00b894',
                        '#0984e3',
                        '#d63031'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    applyFilters() {
        this.dateRange.from = document.getElementById('fromDate').value;
        this.dateRange.to = document.getElementById('toDate').value;
        this.filters.status = document.getElementById('statusFilter').value;
        this.filters.search = document.getElementById('searchInput').value;
        
        this.overviewPage = 1; // Reset pagination
        this.loadPrescriptionStats();
        this.loadTabContent(this.currentTab);
        
        this.showNotification('Filters applied successfully', 'is-success');
    }

    exportReport(format) {
        const params = new URLSearchParams({
            action: 'export_prescriptions',
            format: format,
            report_type: this.currentTab,
            from_date: this.dateRange.from,
            to_date: this.dateRange.to
        });

        if (this.filters.status) {
            params.append('status', this.filters.status);
        }

        if (this.filters.search) {
            params.append('search', this.filters.search);
        }

        const link = document.createElement('a');
        link.href = `controllers/ReportsController.php?${params}`;
        link.download = `prescription_report_${this.currentTab}_${new Date().toISOString().split('T')[0]}.${format}`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        this.showNotification(`${format.toUpperCase()} export initiated`, 'is-success');
    }

    printReport() {
        // Get current prescription data from the table
        const currentData = this.currentPrescriptions || [];
        
        if (currentData.length === 0) {
            alert('No prescription data to print. Please load data first.');
            return;
        }
        
        // Generate print content
        this.generatePrintContent(currentData);
        
        // Trigger print
        window.print();
    }

    generatePrintContent(prescriptions) {
        const printArea = document.getElementById('prescriptionPrintArea');
        const currentDate = new Date().toLocaleDateString();
        const currentDateTime = new Date().toLocaleString();
        
        let prescriptionsHtml = '';
        if (prescriptions && prescriptions.length > 0) {
            prescriptionsHtml = `
                <table class="prescriptions-table">
                    <thead>
                        <tr>
                            <th style="width: 8%;">Rx ID</th>
                            <th style="width: 25%;">Patient Name</th>
                            <th style="width: 25%;">Doctor</th>
                            <th style="width: 12%;">Date</th>
                            <th style="width: 10%;">Medicines</th>
                            <th style="width: 10%;">Status</th>
                            <th style="width: 10%;">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            prescriptions.forEach((prescription, index) => {
                const prescriptionDate = prescription.prescription_date ? new Date(prescription.prescription_date).toLocaleDateString() : 'N/A';
                prescriptionsHtml += `
                    <tr>
                        <td>#${prescription.prescription_id}</td>
                        <td class="prescription-patient">${this.escapeHtml(prescription.patient_name)}</td>
                        <td>${this.escapeHtml(prescription.doctor_name)}<br><small>${this.escapeHtml(prescription.doctor_specialty || '')}</small></td>
                        <td>${prescriptionDate}</td>
                        <td>${prescription.total_medicines || 0}</td>
                        <td>${prescription.status}</td>
                        <td>${this.escapeHtml(prescription.notes || 'N/A')}</td>
                    </tr>
                `;
            });
            
            prescriptionsHtml += `
                    </tbody>
                </table>
            `;
        } else {
            prescriptionsHtml = '<table class="prescriptions-table"><tr><td colspan="7" style="text-align: center; font-style: italic;">No prescription data available.</td></tr></table>';
        }
        
        printArea.innerHTML = `
            <div class="print-header">
                <div class="print-title">Hospital Management System</div>
                <div class="print-subtitle">Prescription Report</div>
            </div>
            
            <div class="print-info">
                <h3>Report Information</h3>
                <div class="print-info-grid">
                    <div><strong>Report Date:</strong> ${currentDate}</div>
                    <div><strong>Total Prescriptions:</strong> ${prescriptions.length}</div>
                    <div><strong>Date Range:</strong> ${this.dateRange.from || 'All'} to ${this.dateRange.to || 'All'}</div>
                    <div><strong>Status Filter:</strong> ${this.filters.status || 'All Status'}</div>
                </div>
            </div>
            
            <div class="print-prescriptions">
                <h3>Prescriptions (${prescriptions.length} records)</h3>
                ${prescriptionsHtml}
            </div>
            
            <div class="print-footer">
                <div>Report ID: RPT-RX-${Date.now()}</div>
                <div>Generated on: ${currentDateTime} | Hospital Management System</div>
                <div style="margin-top: 4px; font-weight: bold;">💊 Prescription Report - Confidential Medical Data</div>
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
function viewPrescription(prescriptionId) {
    openPrescriptionModal(prescriptionId);
}

async function openPrescriptionModal(prescriptionId) {
    const modal = document.getElementById('prescriptionModal');
    const modalContent = document.getElementById('prescriptionModalContent');
    
    // Show modal
    modal.classList.add('is-active');
    
    // Show loading state
    modalContent.innerHTML = `
        <div class="has-text-centered">
            <span class="icon is-large">
                <i class="fas fa-spinner fa-pulse fa-2x"></i>
            </span>
            <p class="mt-2">Loading prescription details...</p>
        </div>
    `;
    
    try {
        // Load prescription details
        const response = await fetch(`controllers/ReportsController.php?action=prescription_details&prescription_id=${prescriptionId}`);
        const data = await response.json();
        
        if (data.success && data.data) {
            renderPrescriptionDetails(data.data);
        } else {
            showPrescriptionError(data.message || 'Failed to load prescription details.');
        }
    } catch (error) {
        console.error('Error loading prescription details:', error);
        showPrescriptionError('An error occurred while loading prescription details.');
    }
}

function renderPrescriptionDetails(prescription) {
    const modalContent = document.getElementById('prescriptionModalContent');
    
    const prescriptionDate = prescription.prescription_date ? new Date(prescription.prescription_date).toLocaleDateString() : 'N/A';
    const statusColor = prescription.status === 'active' ? 'is-success' : 
                       prescription.status === 'fulfilled' ? 'is-info' : 'is-danger';
    
    let medicinesHtml = '';
    if (prescription.items && prescription.items.length > 0) {
        medicinesHtml = prescription.items.map(item => `
            <div class="box">
                <h6 class="title is-6">${item.medicine_name}</h6>
                <div class="columns">
                    <div class="column">
                        <p><strong>Dosage:</strong> ${item.dosage}</p>
                        <p><strong>Frequency:</strong> ${item.frequency}</p>
                    </div>
                    <div class="column">
                        <p><strong>Duration:</strong> ${item.duration_days} days</p>
                        <p><strong>Quantity:</strong> ${item.quantity}</p>
                    </div>
                    <div class="column">
                        <p><strong>Instructions:</strong> ${item.instructions || 'N/A'}</p>
                    </div>
                </div>
            </div>
        `).join('');
    } else {
        medicinesHtml = '<p class="has-text-grey">No medicines prescribed</p>';
    }
    
    modalContent.innerHTML = `
        <div class="columns">
            <div class="column">
                <div class="card prescription-info-card">
                    <div class="card-header">
                        <p class="card-header-title">
                            <span class="icon">
                                <i class="fas fa-user"></i>
                            </span>
                            Patient Information
                        </p>
                    </div>
                    <div class="card-content">
                        <div class="content">
                            <div class="field">
                                <label class="label">Patient Name</label>
                                <p class="is-size-5"><strong>${prescription.patient_name}</strong></p>
                            </div>
                            <div class="field">
                                <label class="label">Doctor</label>
                                <p><strong>${prescription.doctor_name}</strong><br>
                                <small class="has-text-grey">${prescription.doctor_specialty || ''}</small></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="column">
                <div class="card prescription-info-card">
                    <div class="card-header">
                        <p class="card-header-title">
                            <span class="icon">
                                <i class="fas fa-prescription-bottle-alt"></i>
                            </span>
                            Prescription Information
                        </p>
                    </div>
                    <div class="card-content">
                        <div class="content">
                            <div class="columns">
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Prescription ID</label>
                                        <p><code>#${prescription.prescription_id}</code></p>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Date</label>
                                        <p>${prescriptionDate}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="field">
                                <label class="label">Status</label>
                                <p><span class="tag ${statusColor}">${prescription.status}</span></p>
                            </div>
                            <div class="field">
                                <label class="label">Notes</label>
                                <p>${prescription.notes || 'No notes provided'}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card prescription-info-card mt-4">
            <div class="card-header">
                <p class="card-header-title">
                    <span class="icon">
                        <i class="fas fa-pills"></i>
                    </span>
                    Prescribed Medicines
                </p>
            </div>
            <div class="card-content">
                <div class="content">
                    ${medicinesHtml}
                </div>
            </div>
        </div>
    `;
}

function showPrescriptionError(message) {
    const modalContent = document.getElementById('prescriptionModalContent');
    modalContent.innerHTML = `
        <div class="notification is-danger">
            <span class="icon">
                <i class="fas fa-exclamation-triangle"></i>
            </span>
            <span>${message}</span>
        </div>
    `;
}

function closePrescriptionModal() {
    const modal = document.getElementById('prescriptionModal');
    modal.classList.remove('is-active');
}

// Close modal when pressing Escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closePrescriptionModal();
    }
});

// Initialize the reports manager when the page loads
document.addEventListener('DOMContentLoaded', () => {
    window.prescriptionReportsManager = new PrescriptionReportsManager();
});
</script>

    </div> <!-- End of container from header.php -->
</body>
</html> 