<?php
require_once 'includes/header.php';
?>

<style>
/* Report-specific styling - Copied from patient reports */
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

.insurance-info-card {
    border: 1px solid #e1e5e9;
    border-radius: 8px;
}

.insurance-info-card .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.insurance-info-card .card-header-title {
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
    
    #insurancePrintArea,
    #insurancePrintArea * {
        visibility: visible;
    }
    
    #insurancePrintArea {
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
    
    .navbar, .tabs, .search-controls, .pagination-controls, 
    .button, .modal, .loading-overlay {
        display: none !important;
    }
}
</style>

<section class="section page-transition">
    <!-- Page Header -->
    <div class="columns is-vcentered mb-4">
        <div class="column">
            <nav class="breadcrumb" aria-label="breadcrumbs">
                <ul>
                    <li><a href="reports.php"><span class="icon"><i class="fas fa-chart-bar"></i></span><span>Reports</span></a></li>
                    <li class="is-active"><a href="#"><span class="icon"><i class="fas fa-shield-alt"></i></span><span>Insurance Reports</span></a></li>
                </ul>
            </nav>
            <h1 class="title is-3">
                <span class="icon">
                    <i class="fas fa-shield-alt"></i>
                </span>
                Insurance Reports & Analytics
            </h1>
            <p class="subtitle">Comprehensive insurance coverage analysis and reporting</p>
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
                        <input class="input" type="text" id="searchInput" placeholder="Search insurance..." />
                    </div>
                </div>
            </div>
            <div class="column is-2">
                <div class="field">
                    <label class="label">Provider</label>
                    <div class="control">
                        <div class="select is-fullwidth">
                            <select id="providerFilter">
                                <option value="">All Providers</option>
                            </select>
                        </div>
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
                                <option value="inactive">Inactive</option>
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
    <div class="stats-grid" id="insuranceStats">
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
                <li data-tab="providers">
                    <a>
                        <span class="icon"><i class="fas fa-building"></i></span>
                        <span>Providers</span>
                    </a>
                </li>
                <li data-tab="coverage">
                    <a>
                        <span class="icon"><i class="fas fa-chart-pie"></i></span>
                        <span>Coverage</span>
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
                        <h4 class="title is-5">Insurance Records</h4>
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
                                <th>Patient Name</th>
                                <th>Provider</th>
                                <th>Policy Number</th>
                                <th>Status</th>
                                <th>Contact</th>
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
                            Showing 0 of 0 insurance records
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

        <!-- Providers Tab -->
        <div id="providers-tab" class="tab-content" style="display: none;">
            <div class="chart-box">
                <h4 class="title is-5 mb-4">Insurance Providers Distribution</h4>
                <div class="chart-container">
                    <canvas id="providersChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Coverage Tab -->
        <div id="coverage-tab" class="tab-content" style="display: none;">
            <div class="columns">
                <div class="column">
                    <div class="chart-box">
                        <h4 class="title is-5 mb-4">Coverage Status</h4>
                        <div class="chart-container">
                            <canvas id="coverageChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="column">
                    <div class="chart-box">
                        <h4 class="title is-5 mb-4">Provider Market Share</h4>
                        <div class="chart-container">
                            <canvas id="marketChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Tab -->
        <div id="summary-tab" class="tab-content" style="display: none;">
            <div class="chart-box">
                <h4 class="title is-5 mb-4">Insurance Summary Statistics</h4>
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
</section>

<!-- Insurance Details Modal -->
<div class="modal" id="insuranceModal">
    <div class="modal-background" onclick="closeInsuranceModal()"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">
                <span class="icon">
                    <i class="fas fa-shield-alt"></i>
                </span>
                Insurance Details
            </p>
            <button class="delete" aria-label="close" onclick="closeInsuranceModal()"></button>
        </header>
        <section class="modal-card-body">
            <div id="insuranceModalContent">
                <!-- Insurance details will be loaded here -->
                <div class="has-text-centered">
                    <span class="icon is-large">
                        <i class="fas fa-spinner fa-pulse fa-2x"></i>
                    </span>
                    <p class="mt-2">Loading insurance details...</p>
                </div>
            </div>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-light" onclick="closeInsuranceModal()">Close</button>
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
<div id="insurancePrintArea" style="display: none;">
    <!-- Print content will be generated here -->
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- JavaScript -->
<script>
class InsuranceReportsManager {
    constructor() {
        this.currentTab = 'overview';
        this.charts = {};
        this.filters = {
            provider: '',
            search: '',
            status: ''
        };
        this.overviewPage = 1;
        this.overviewLimit = 20;
        this.totalPages = 1;
        this.totalRecords = 0;
        this.searchTimeout = null;
        this.currentInsuranceRecords = [];
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadInsuranceStats();
        this.loadTabContent('overview');
        this.loadProviders();
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

        document.getElementById('providerFilter').addEventListener('change', (e) => {
            this.filters.provider = e.target.value;
        });

        document.getElementById('statusFilter').addEventListener('change', (e) => {
            this.filters.status = e.target.value;
        });

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', (e) => {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.filters.search = e.target.value;
                this.overviewPage = 1;
                if (this.currentTab === 'overview') {
                    this.loadOverview();
                }
            }, 500);
        });

        // Records per page
        document.getElementById('recordsPerPage').addEventListener('change', (e) => {
            this.overviewLimit = parseInt(e.target.value);
            this.overviewPage = 1;
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

    async loadInsuranceStats() {
        try {
            const params = new URLSearchParams({
                action: 'insurance',
                report_type: 'stats'
            });

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderStats(data.data);
            }
        } catch (error) {
            console.error('Error loading insurance stats:', error);
        }
    }

    renderStats(stats) {
        const container = document.getElementById('insuranceStats');
        
        container.innerHTML = `
            <div class="stat-card">
                <span class="stat-number">${stats.total_records || 0}</span>
                <div class="stat-label">Total Insurance Records</div>
            </div>
            <div class="stat-card">
                <span class="stat-number">${stats.active_records || 0}</span>
                <div class="stat-label">Active Policies</div>
            </div>
            <div class="stat-card">
                <span class="stat-number">${stats.total_providers || 0}</span>
                <div class="stat-label">Insurance Providers</div>
            </div>
            <div class="stat-card">
                <span class="stat-number">${stats.coverage_percentage || 0}%</span>
                <div class="stat-label">Patient Coverage Rate</div>
            </div>
            <div class="stat-card">
                <span class="stat-number">${stats.inactive_records || 0}</span>
                <div class="stat-label">Inactive Policies</div>
            </div>
        `;
    }

    async loadProviders() {
        try {
            const response = await fetch('controllers/ReportsController.php?action=insurance_providers');
            const data = await response.json();

            if (data.success) {
                this.populateProviderFilter(data.data);
            }
        } catch (error) {
            console.error('Error loading providers:', error);
        }
    }

    populateProviderFilter(providers) {
        const select = document.getElementById('providerFilter');
        providers.forEach(provider => {
            const option = document.createElement('option');
            option.value = provider.insurance_provider_id;
            option.textContent = provider.provider_name;
            select.appendChild(option);
        });
    }

    switchTab(tabName) {
        // Update active tab
        document.querySelectorAll('[data-tab]').forEach(tab => {
            tab.classList.remove('is-active');
        });
        document.querySelector(`[data-tab="${tabName}"]`).classList.add('is-active');

        // Show active content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.style.display = 'none';
            content.classList.remove('is-active');
        });
        document.getElementById(`${tabName}-tab`).style.display = 'block';
        document.getElementById(`${tabName}-tab`).classList.add('is-active');

        this.currentTab = tabName;
        this.loadTabContent(tabName);
    }

    async loadTabContent(tabName) {
        switch (tabName) {
            case 'overview':
                await this.loadOverview();
                break;
            case 'providers':
                await this.loadProviders();
                break;
            case 'coverage':
                await this.loadCoverage();
                break;
            case 'summary':
                await this.loadSummary();
                break;
        }
    }

    async loadOverview() {
        this.showLoading();

        try {
            const params = new URLSearchParams({
                action: 'insurance',
                report_type: 'overview',
                page: this.overviewPage,
                limit: this.overviewLimit,
                ...this.filters
            });

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.currentInsuranceRecords = data.data.records;
                this.renderOverviewTable(data.data.records);
                this.updatePagination(data.data);
            }
        } catch (error) {
            console.error('Error loading overview:', error);
        } finally {
            this.hideLoading();
        }
    }

    renderOverviewTable(records) {
        const tbody = document.getElementById('overviewTableBody');
        
        if (records.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="has-text-centered">
                        <span class="icon"><i class="fas fa-exclamation-circle"></i></span>
                        No insurance records found
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = records.map(record => `
            <tr>
                <td>#${record.patient_insurance_id}</td>
                <td><strong>${this.escapeHtml(record.patient_name || 'N/A')}</strong></td>
                <td>${this.escapeHtml(record.provider_name || 'N/A')}</td>
                <td><code>${this.escapeHtml(record.insurance_number || 'N/A')}</code></td>
                <td>
                    <span class="tag ${record.status === 'active' ? 'is-success' : 'is-danger'}">
                        ${record.status}
                    </span>
                </td>
                <td>${this.escapeHtml(record.provider_contact || 'N/A')}</td>
                <td>
                    <button class="button is-small is-info action-btn" onclick="insuranceReports.viewInsurance(${record.patient_insurance_id})">
                        <span class="icon"><i class="fas fa-eye"></i></span>
                    </button>
                </td>
            </tr>
        `).join('');
    }

    updatePagination(data) {
        this.totalPages = data.total_pages;
        this.totalRecords = data.total_records;
        
        // Update pagination info
        document.getElementById('paginationInfo').textContent = 
            `Showing ${data.records.length} of ${data.total_records} insurance records`;
        
        // Update pagination buttons
        document.getElementById('prevPage').disabled = data.current_page <= 1;
        document.getElementById('nextPage').disabled = data.current_page >= data.total_pages;
        
        // Update pagination list
        const paginationList = document.getElementById('paginationList');
        paginationList.innerHTML = '';
        
        for (let i = Math.max(1, data.current_page - 2); i <= Math.min(data.total_pages, data.current_page + 2); i++) {
            const li = document.createElement('li');
            li.innerHTML = `
                <button class="pagination-link ${i === data.current_page ? 'is-current' : ''}" 
                        onclick="insuranceReports.goToPage(${i})">
                    ${i}
                </button>
            `;
            paginationList.appendChild(li);
        }
    }

    async loadProvidersChart() {
        try {
            const response = await fetch('controllers/ReportsController.php?action=insurance&report_type=providers');
            const data = await response.json();

            if (data.success) {
                this.renderProvidersChart(data.data);
            }
        } catch (error) {
            console.error('Error loading providers chart:', error);
        }
    }

    renderProvidersChart(data) {
        const ctx = document.getElementById('providersChart').getContext('2d');
        
        if (this.charts.providers) {
            this.charts.providers.destroy();
        }

        this.charts.providers = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.provider_stats.map(p => p.provider_name),
                datasets: [{
                    label: 'Number of Policies',
                    data: data.provider_stats.map(p => p.policy_count),
                    backgroundColor: '#3273dc',
                    borderColor: '#2563eb',
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

    async loadCoverage() {
        try {
            const response = await fetch('controllers/ReportsController.php?action=insurance&report_type=coverage');
            const data = await response.json();

            if (data.success) {
                this.renderCoverageCharts(data.data);
            }
        } catch (error) {
            console.error('Error loading coverage:', error);
        }
    }

    renderCoverageCharts(data) {
        // Coverage Chart
        const coverageCtx = document.getElementById('coverageChart').getContext('2d');
        
        if (this.charts.coverage) {
            this.charts.coverage.destroy();
        }

        this.charts.coverage = new Chart(coverageCtx, {
            type: 'doughnut',
            data: {
                labels: ['Active', 'Inactive'],
                datasets: [{
                    data: [data.coverage_stats.active, data.coverage_stats.inactive],
                    backgroundColor: ['#3273dc', '#ff3860']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Market Share Chart
        const marketCtx = document.getElementById('marketChart').getContext('2d');
        
        if (this.charts.market) {
            this.charts.market.destroy();
        }

        this.charts.market = new Chart(marketCtx, {
            type: 'pie',
            data: {
                labels: ['Insured Patients', 'Uninsured Patients'],
                datasets: [{
                    data: [data.patient_coverage.insured, data.patient_coverage.uninsured],
                    backgroundColor: ['#23d160', '#ff3860']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    async loadSummary() {
        try {
            const response = await fetch('controllers/ReportsController.php?action=insurance&report_type=summary');
            const data = await response.json();

            if (data.success) {
                this.renderSummary(data.data);
            }
        } catch (error) {
            console.error('Error loading summary:', error);
        }
    }

    renderSummary(stats) {
        const container = document.getElementById('summaryStats');
        
        container.innerHTML = `
            <div class="content">
                <p><strong>Total Records:</strong> ${stats.total_records}</p>
                <p><strong>Active Policies:</strong> ${stats.active_records} (${((stats.active_records/stats.total_records)*100).toFixed(1)}%)</p>
                <p><strong>Inactive Policies:</strong> ${stats.inactive_records} (${((stats.inactive_records/stats.total_records)*100).toFixed(1)}%)</p>
                <p><strong>Total Providers:</strong> ${stats.total_providers}</p>
                <p><strong>Coverage Rate:</strong> ${stats.coverage_percentage}%</p>
                <p><strong>Top Provider:</strong> ${stats.top_provider}</p>
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
                labels: ['Active', 'Inactive', 'Providers', 'Coverage %'],
                datasets: [{
                    label: 'Count',
                    data: [stats.active_records, stats.inactive_records, stats.total_providers, stats.coverage_percentage],
                    backgroundColor: [
                        '#3273dc',
                        '#ff3860',
                        '#ffdd57',
                        '#23d160'
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

    async viewInsurance(insuranceId) {
        try {
            const response = await fetch(`controllers/ReportsController.php?action=insurance_details&insurance_id=${insuranceId}`);
            const data = await response.json();

            if (data.success) {
                this.showInsuranceModal(data.data);
            } else {
                alert('Error loading insurance details: ' + data.message);
            }
        } catch (error) {
            console.error('Error loading insurance details:', error);
            alert('Error loading insurance details');
        }
    }

    showInsuranceModal(insurance) {
        const modalContent = document.getElementById('insuranceModalContent');
        
        modalContent.innerHTML = `
            <div class="columns">
                <div class="column">
                    <div class="card insurance-info-card">
                        <div class="card-header">
                            <p class="card-header-title">
                                <span class="icon">
                                    <i class="fas fa-user-circle"></i>
                                </span>
                                Patient Information
                            </p>
                        </div>
                        <div class="card-content">
                            <div class="content">
                                <div class="field">
                                    <label class="label">Patient Name</label>
                                    <p class="is-size-5"><strong>${insurance.patient_name || 'N/A'}</strong></p>
                                </div>
                                <div class="field">
                                    <label class="label">Phone Number</label>
                                    <p>
                                        <span class="icon">
                                            <i class="fas fa-phone"></i>
                                        </span>
                                        ${insurance.patient_phone || 'N/A'}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column">
                    <div class="card insurance-info-card">
                        <div class="card-header">
                            <p class="card-header-title">
                                <span class="icon">
                                    <i class="fas fa-shield-alt"></i>
                                </span>
                                Insurance Information
                            </p>
                        </div>
                        <div class="card-content">
                            <div class="content">
                                <div class="field">
                                    <label class="label">Provider</label>
                                    <p><strong>${insurance.provider_name || 'N/A'}</strong></p>
                                </div>
                                <div class="field">
                                    <label class="label">Policy Number</label>
                                    <p><code>${insurance.insurance_number || 'N/A'}</code></p>
                                </div>
                                <div class="field">
                                    <label class="label">Status</label>
                                    <p><span class="tag ${insurance.status === 'active' ? 'is-success' : 'is-danger'}">${insurance.status}</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card insurance-info-card mt-4">
                <div class="card-header">
                    <p class="card-header-title">
                        <span class="icon">
                            <i class="fas fa-building"></i>
                        </span>
                        Provider Details
                    </p>
                </div>
                <div class="card-content">
                    <div class="content">
                        <div class="columns">
                            <div class="column">
                                <div class="field">
                                    <label class="label">Contact Number</label>
                                    <p>${insurance.provider_contact || 'N/A'}</p>
                                </div>
                            </div>
                            <div class="column">
                                <div class="field">
                                    <label class="label">Address</label>
                                    <p>${insurance.provider_address || 'N/A'}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.getElementById('insuranceModal').classList.add('is-active');
    }

    goToPage(page) {
        this.overviewPage = page;
        this.loadOverview();
    }

    applyFilters() {
        this.filters = {
            search: document.getElementById('searchInput').value,
            provider: document.getElementById('providerFilter').value,
            status: document.getElementById('statusFilter').value
        };
        
        this.overviewPage = 1;
        this.loadTabContent(this.currentTab);
        
        this.showNotification('Filters applied successfully', 'is-success');
    }

    exportReport(format) {
        const params = new URLSearchParams({
            action: 'export_insurance',
            format: format,
            ...this.filters
        });

        const link = document.createElement('a');
        link.href = `controllers/ReportsController.php?${params}`;
        link.download = `insurance_report_${new Date().toISOString().split('T')[0]}.${format}`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        this.showNotification(`${format.toUpperCase()} export initiated`, 'is-success');
    }

    printReport() {
        const currentData = this.currentInsuranceRecords || [];
        
        if (currentData.length === 0) {
            alert('No insurance data to print. Please load data first.');
            return;
        }
        
        this.generatePrintContent(currentData);
        window.print();
    }
    
    generatePrintContent(records) {
        const printArea = document.getElementById('insurancePrintArea');
        const currentDate = new Date().toLocaleDateString();
        const currentDateTime = new Date().toLocaleString();
        
        let recordsHtml = '';
        if (records && records.length > 0) {
            recordsHtml = `
                <table style="width: 100%; border-collapse: collapse; border: 1px solid #000; font-size: 9px;">
                    <thead>
                        <tr>
                            <th style="border: 1px solid #ccc; padding: 3px 4px; background-color: #f5f5f5; font-weight: bold;">ID</th>
                            <th style="border: 1px solid #ccc; padding: 3px 4px; background-color: #f5f5f5; font-weight: bold;">Patient</th>
                            <th style="border: 1px solid #ccc; padding: 3px 4px; background-color: #f5f5f5; font-weight: bold;">Provider</th>
                            <th style="border: 1px solid #ccc; padding: 3px 4px; background-color: #f5f5f5; font-weight: bold;">Policy Number</th>
                            <th style="border: 1px solid #ccc; padding: 3px 4px; background-color: #f5f5f5; font-weight: bold;">Status</th>
                            <th style="border: 1px solid #ccc; padding: 3px 4px; background-color: #f5f5f5; font-weight: bold;">Contact</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            records.forEach((record) => {
                recordsHtml += `
                    <tr>
                        <td style="border: 1px solid #ccc; padding: 3px 4px;">#${record.patient_insurance_id}</td>
                        <td style="border: 1px solid #ccc; padding: 3px 4px; font-weight: bold;">${this.escapeHtml(record.patient_name || 'N/A')}</td>
                        <td style="border: 1px solid #ccc; padding: 3px 4px;">${this.escapeHtml(record.provider_name || 'N/A')}</td>
                        <td style="border: 1px solid #ccc; padding: 3px 4px;">${this.escapeHtml(record.insurance_number || 'N/A')}</td>
                        <td style="border: 1px solid #ccc; padding: 3px 4px;">${record.status}</td>
                        <td style="border: 1px solid #ccc; padding: 3px 4px;">${this.escapeHtml(record.provider_contact || 'N/A')}</td>
                    </tr>
                `;
            });
            
            recordsHtml += `
                    </tbody>
                </table>
            `;
        }
        
        printArea.innerHTML = `
            <div class="print-header">
                <div class="print-title">Hospital Management System</div>
                <div class="print-subtitle">Insurance Reports</div>
            </div>
            
            <div style="margin-bottom: 12px; border: 1px solid #000; padding: 8px;">
                <h3 style="margin: 0 0 6px 0; font-size: 12px; border-bottom: 1px solid #ccc; padding-bottom: 3px;">Report Information</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 10px;">
                    <div><strong>Report Date:</strong> ${currentDate}</div>
                    <div><strong>Total Records:</strong> ${records.length}</div>
                </div>
            </div>
            
            <div style="margin-bottom: 12px;">
                <h3 style="margin: 0 0 8px 0; font-size: 12px; background-color: #f0f0f0; padding: 4px 8px; border: 1px solid #000;">Insurance Records (${records.length} records)</h3>
                ${recordsHtml}
            </div>
            
            <div style="margin-top: 15px; border-top: 1px solid #000; padding-top: 6px; text-align: center; font-size: 8px;">
                <div>Report ID: INS-${Date.now()}</div>
                <div>Generated on: ${currentDateTime} | Hospital Management System</div>
                <div style="margin-top: 4px; font-weight: bold;">📊 Insurance Reports - Confidential Medical Data</div>
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
}

// Global functions
function viewInsurance(insuranceId) {
    insuranceReports.viewInsurance(insuranceId);
}

function closeInsuranceModal() {
    document.getElementById('insuranceModal').classList.remove('is-active');
}

// Close modal when pressing Escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeInsuranceModal();
    }
});

// Initialize the reports manager when the page loads
document.addEventListener('DOMContentLoaded', () => {
    window.insuranceReports = new InsuranceReportsManager();
});
</script>

    </div> <!-- End of container from header.php -->
</body>
</html> 