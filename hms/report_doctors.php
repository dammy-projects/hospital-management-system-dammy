<?php
require_once 'includes/header.php';
?>

<style>
.page-transition {
    opacity: 0;
    transform: translateY(20px);
    animation: fadeInUp 0.6s ease forwards;
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.stat-card .stat-number {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.stat-card .stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

.filter-section {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 2rem;
    border: 1px solid #e9ecef;
}

.chart-box {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin-bottom: 1rem;
}

.report-tabs .tabs {
    margin-bottom: 0;
}

.report-tabs .tabs ul {
    border-bottom: 2px solid #e9ecef;
}

.report-tabs .tabs li.is-active a {
    border-bottom: 3px solid #667eea;
    color: #667eea;
}

.export-buttons .button {
    margin-right: 0.5rem;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    justify-content: center;
    align-items: center;
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

.doctor-info-card {
    border: 1px solid #e1e5e9;
    border-radius: 8px;
}

.doctor-info-card .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.doctor-info-card .card-header-title {
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
    
    #doctorPrintArea,
    #doctorPrintArea * {
        visibility: visible;
    }
    
    #doctorPrintArea {
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
    
    .print-doctors {
        margin-bottom: 12px;
    }
    
    .print-doctors h3 {
        margin: 0 0 8px 0 !important;
        font-size: 12px;
        background-color: #f0f0f0;
        padding: 4px 8px;
        border: 1px solid #000;
    }
    
    .doctors-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #000;
        font-size: 9px;
    }
    
    .doctors-table th,
    .doctors-table td {
        border: 1px solid #ccc;
        padding: 3px 4px;
        text-align: left;
        vertical-align: top;
    }
    
    .doctors-table th {
        background-color: #f5f5f5;
        font-weight: bold;
        font-size: 9px;
    }
    
    .doctor-name {
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
                    <li class="is-active"><a href="#"><span class="icon"><i class="fas fa-user-md"></i></span><span>Doctor Reports</span></a></li>
                </ul>
            </nav>
            <h1 class="title is-3">
                <span class="icon">
                    <i class="fas fa-user-md"></i>
                </span>
                Doctor Reports & Analytics
            </h1>
            <p class="subtitle">Comprehensive doctor data analysis and reporting</p>
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
                        <input class="input" type="text" id="searchInput" placeholder="Search doctors..." />
                    </div>
                </div>
            </div>
            <div class="column is-2">
                <div class="field">
                    <label class="label">Specialty</label>
                    <div class="control">
                        <div class="select is-fullwidth">
                            <select id="specialtyFilter">
                                <option value="">All Specialties</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="column is-2">
                <div class="field">
                    <label class="label">Department</label>
                    <div class="control">
                        <div class="select is-fullwidth">
                            <select id="departmentFilter">
                                <option value="">All Departments</option>
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
    <div class="stats-grid" id="doctorStats">
        <!-- Stats will be loaded here -->
    </div>

    <!-- Report Tabs -->
    <div class="report-tabs">
        <div class="tabs is-boxed">
            <ul>
                <li class="is-active" data-tab="directory">
                    <a>
                        <span class="icon"><i class="fas fa-address-book"></i></span>
                        <span>Directory</span>
                    </a>
                </li>
                <li data-tab="specialties">
                    <a>
                        <span class="icon"><i class="fas fa-stethoscope"></i></span>
                        <span>Specialties</span>
                    </a>
                </li>
                <li data-tab="departments">
                    <a>
                        <span class="icon"><i class="fas fa-building"></i></span>
                        <span>Departments</span>
                    </a>
                </li>
                <li data-tab="workload">
                    <a>
                        <span class="icon"><i class="fas fa-chart-line"></i></span>
                        <span>Workload</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab Content -->
    <div id="tabContent">
        <!-- Directory Tab -->
        <div id="directory-tab" class="tab-content is-active">
            <div class="chart-box">
                <div class="columns is-vcentered mb-4">
                    <div class="column">
                        <h4 class="title is-5">Doctor Directory</h4>
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
                                <th>Specialty</th>
                                <th>Department</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="directoryTableBody">
                            <!-- Data will be loaded here -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination Controls -->
                <div class="columns is-vcentered mt-4">
                    <div class="column">
                        <p class="help" id="paginationInfo">
                            Showing 0 of 0 doctors
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

        <!-- Specialties Tab -->
        <div id="specialties-tab" class="tab-content" style="display: none;">
            <div class="chart-box">
                <h4 class="title is-5 mb-4">Doctor Specialties Distribution</h4>
                <div class="chart-container">
                    <canvas id="specialtiesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Departments Tab -->
        <div id="departments-tab" class="tab-content" style="display: none;">
            <div class="columns">
                <div class="column">
                    <div class="chart-box">
                        <h4 class="title is-5 mb-4">Department Assignment</h4>
                        <div class="chart-container">
                            <canvas id="departmentChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Workload Tab -->
        <div id="workload-tab" class="tab-content" style="display: none;">
            <div class="chart-box">
                <h4 class="title is-5 mb-4">Doctor Workload Analysis</h4>
                <div class="columns">
                    <div class="column">
                        <div class="content">
                            <h5 class="subtitle is-6">Top Performing Doctors</h5>
                            <div id="workloadStats">
                                <!-- Workload data will be loaded here -->
                            </div>
                        </div>
                    </div>
                    <div class="column">
                        <div class="chart-container">
                            <canvas id="workloadChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Doctor Details Modal -->
<div class="modal" id="doctorModal">
    <div class="modal-background" onclick="closeDoctorModal()"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">
                <span class="icon">
                    <i class="fas fa-user-md"></i>
                </span>
                Doctor Details
            </p>
            <button class="delete" aria-label="close" onclick="closeDoctorModal()"></button>
        </header>
        <section class="modal-card-body">
            <div id="doctorModalContent">
                <!-- Doctor details will be loaded here -->
                <div class="has-text-centered">
                    <span class="icon is-large">
                        <i class="fas fa-spinner fa-pulse fa-2x"></i>
                    </span>
                    <p class="mt-2">Loading doctor details...</p>
                </div>
            </div>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-light" onclick="closeDoctorModal()">Close</button>
        </footer>
    </div>
</div>

<!-- Loading Spinner -->
<div class="loading-overlay" id="loadingSpinner" style="display: none;">
    <span class="icon is-large">
        <i class="fas fa-spinner fa-pulse fa-2x"></i>
    </span>
</div>

<!-- Hidden Print Area for Doctor Info -->
<div id="doctorPrintArea" style="display: none;">
    <!-- Doctor print content will be generated here -->
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- JavaScript -->
<script>
class DoctorReportsManager {
    constructor() {
        this.currentTab = 'directory';
        this.charts = {};
        this.filters = {
            specialty: '',
            department: '',
            search: ''
        };
        this.directoryPage = 1;
        this.directoryLimit = 20;
        this.totalPages = 1;
        this.totalRecords = 0;
        this.searchTimeout = null;
        this.currentDoctors = [];
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadDoctorStats();
        this.loadTabContent('directory');
        this.loadFilterOptions();
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

        document.getElementById('specialtyFilter').addEventListener('change', (e) => {
            this.filters.specialty = e.target.value;
        });

        document.getElementById('departmentFilter').addEventListener('change', (e) => {
            this.filters.department = e.target.value;
        });

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', (e) => {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.filters.search = e.target.value;
                this.directoryPage = 1; // Reset to first page
                if (this.currentTab === 'directory') {
                    this.loadDirectory();
                }
            }, 500); // Debounce search
        });

        // Records per page
        document.getElementById('recordsPerPage').addEventListener('change', (e) => {
            this.directoryLimit = parseInt(e.target.value);
            this.directoryPage = 1; // Reset to first page
            if (this.currentTab === 'directory') {
                this.loadDirectory();
            }
        });

        // Pagination events
        document.getElementById('prevPage').addEventListener('click', () => {
            if (this.directoryPage > 1) {
                this.directoryPage--;
                this.loadDirectory();
            }
        });

        document.getElementById('nextPage').addEventListener('click', () => {
            if (this.directoryPage < this.totalPages) {
                this.directoryPage++;
                this.loadDirectory();
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

    async loadFilterOptions() {
        try {
            // Load specialties for filter
            const specialtyResponse = await fetch('controllers/ReportsController.php?action=doctors&report_type=specialty');
            const specialtyData = await specialtyResponse.json();
            
            if (specialtyData.success) {
                const specialtySelect = document.getElementById('specialtyFilter');
                specialtyData.data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.specialization;
                    option.textContent = item.specialization;
                    specialtySelect.appendChild(option);
                });
            }

            // Load departments for filter
            const deptResponse = await fetch('controllers/ReportsController.php?action=doctors&report_type=department');
            const deptData = await deptResponse.json();
            
            if (deptData.success) {
                const deptSelect = document.getElementById('departmentFilter');
                deptData.data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.department_name;
                    option.textContent = item.department_name;
                    deptSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading filter options:', error);
        }
    }

    async loadDoctorStats() {
        try {
            const response = await fetch('controllers/ReportsController.php?action=doctors&report_type=directory');
            const data = await response.json();

            if (data.success) {
                this.renderDoctorStats(data.data);
            }
        } catch (error) {
            console.error('Error loading doctor stats:', error);
        }
    }

    renderDoctorStats(doctors) {
        const statsContainer = document.getElementById('doctorStats');
        
        const totalDoctors = doctors.length;
        const activeDoctors = doctors.filter(d => d.status === 'active').length;
        const specialties = [...new Set(doctors.map(d => d.specialization))].length;
        const departments = [...new Set(doctors.map(d => d.department_name))].filter(d => d).length;

        statsContainer.innerHTML = `
            <div class="stat-card">
                <div class="stat-number">${totalDoctors}</div>
                <div class="stat-label">Total Doctors</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">${activeDoctors}</div>
                <div class="stat-label">Active Doctors</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">${specialties}</div>
                <div class="stat-label">Specialties</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">${departments}</div>
                <div class="stat-label">Departments</div>
            </div>
        `;
    }

    switchTab(tabName) {
        // Update active tab
        document.querySelectorAll('[data-tab]').forEach(tab => {
            tab.classList.remove('is-active');
        });
        document.querySelector(`[data-tab="${tabName}"]`).classList.add('is-active');

        // Show/hide tab content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('is-active');
            content.style.display = 'none';
        });
        
        const activeTab = document.getElementById(`${tabName}-tab`);
        activeTab.classList.add('is-active');
        activeTab.style.display = 'block';

        this.currentTab = tabName;
        this.loadTabContent(tabName);
    }

    async loadTabContent(tabName) {
        this.showLoading();

        try {
            switch (tabName) {
                case 'directory':
                    await this.loadDirectory();
                    break;
                case 'specialties':
                    await this.loadSpecialties();
                    break;
                case 'departments':
                    await this.loadDepartments();
                    break;
                case 'workload':
                    await this.loadWorkload();
                    break;
            }
        } catch (error) {
            console.error(`Error loading ${tabName}:`, error);
            this.showNotification(`Error loading ${tabName} data`, 'is-danger');
        } finally {
            this.hideLoading();
        }
    }

    async loadDirectory() {
        const params = new URLSearchParams({
            action: 'doctors',
            report_type: 'directory',
            page: this.directoryPage,
            limit: this.directoryLimit,
            search: this.filters.search,
            specialty: this.filters.specialty,
            department: this.filters.department
        });

        const response = await fetch(`controllers/ReportsController.php?${params}`);
        const data = await response.json();

        if (data.success) {
            this.totalPages = data.data.total_pages;
            this.totalRecords = data.data.total;
            this.renderDirectoryTable(data.data.data, this.directoryPage, this.totalPages, this.totalRecords);
        }
    }

    renderDirectoryTable(doctors, currentPage, totalPages, totalCount) {
        // Store current doctors for printing
        this.currentDoctors = doctors;
        
        const tbody = document.getElementById('directoryTableBody');
        tbody.innerHTML = '';

        if (doctors.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="has-text-centered has-text-grey">
                        <span class="icon"><i class="fas fa-info-circle"></i></span>
                        No doctors found matching your criteria
                    </td>
                </tr>
            `;
            return;
        }

        doctors.forEach(doctor => {
            const row = document.createElement('tr');
            
            const statusClass = doctor.status === 'active' ? 'is-success' : 'is-warning';
            const fullName = `${doctor.first_name} ${doctor.last_name}`;

            row.innerHTML = `
                <td>#${doctor.id}</td>
                <td class="doctor-name">${this.escapeHtml(fullName)}</td>
                <td>${this.escapeHtml(doctor.specialization || 'N/A')}</td>
                <td>${this.escapeHtml(doctor.department_name || 'N/A')}</td>
                <td>${this.escapeHtml(doctor.phone || 'N/A')}</td>
                <td>${this.escapeHtml(doctor.email || 'N/A')}</td>
                <td><span class="tag ${statusClass}">${doctor.status}</span></td>
                <td>
                    <div class="buttons are-small">
                        <button class="button is-info is-small action-btn" onclick="viewDoctor(${doctor.id})">
                            <span class="icon"><i class="fas fa-eye"></i></span>
                            <span>View</span>
                        </button>
                    </div>
                </td>
            `;

            tbody.appendChild(row);
        });

        // Update pagination info
        const start = (currentPage - 1) * this.directoryLimit + 1;
        const end = Math.min(currentPage * this.directoryLimit, totalCount);
        document.getElementById('paginationInfo').textContent = `Showing ${start} to ${end} of ${totalCount} doctors`;
        
        // Update pagination controls
        this.updatePaginationControls();
    }
    
    updatePaginationControls() {
        const prevBtn = document.getElementById('prevPage');
        const nextBtn = document.getElementById('nextPage');
        const paginationList = document.getElementById('paginationList');
        
        // Update previous/next buttons
        prevBtn.disabled = this.directoryPage <= 1;
        nextBtn.disabled = this.directoryPage >= this.totalPages;
        
        // Clear existing pagination numbers
        paginationList.innerHTML = '';
        
        // Calculate page range to show
        const maxPagesToShow = 5;
        let startPage = Math.max(1, this.directoryPage - Math.floor(maxPagesToShow / 2));
        let endPage = Math.min(this.totalPages, startPage + maxPagesToShow - 1);
        
        // Adjust startPage if we're near the end
        if (endPage - startPage + 1 < maxPagesToShow) {
            startPage = Math.max(1, endPage - maxPagesToShow + 1);
        }
        
        // Add page numbers
        for (let i = startPage; i <= endPage; i++) {
            const li = document.createElement('li');
            const a = document.createElement('a');
            a.className = `pagination-link ${i === this.directoryPage ? 'is-current' : ''}`;
            a.textContent = i;
            a.href = '#';
            a.addEventListener('click', (e) => {
                e.preventDefault();
                if (i !== this.directoryPage) {
                    this.directoryPage = i;
                    this.loadDirectory();
                }
            });
            li.appendChild(a);
            paginationList.appendChild(li);
        }
    }

    async loadSpecialties() {
        const response = await fetch('controllers/ReportsController.php?action=doctors&report_type=specialty');
        const data = await response.json();

        if (data.success) {
            this.renderSpecialtiesChart(data.data);
        }
    }

    renderSpecialtiesChart(distribution) {
        const ctx = document.getElementById('specialtiesChart').getContext('2d');
        
        if (this.charts.specialties) {
            this.charts.specialties.destroy();
        }

        const labels = distribution.map(d => d.specialization);
        const data = distribution.map(d => parseInt(d.count));

        this.charts.specialties = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                        '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    }

    async loadDepartments() {
        const response = await fetch('controllers/ReportsController.php?action=doctors&report_type=department');
        const data = await response.json();

        if (data.success) {
            this.renderDepartmentChart(data.data);
        }
    }

    renderDepartmentChart(distribution) {
        const ctx = document.getElementById('departmentChart').getContext('2d');
        
        if (this.charts.department) {
            this.charts.department.destroy();
        }

        const labels = distribution.map(d => d.department_name);
        const data = distribution.map(d => parseInt(d.doctor_count));

        this.charts.department = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Number of Doctors',
                    data: data,
                    backgroundColor: '#667eea'
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

    async loadWorkload() {
        const response = await fetch('controllers/ReportsController.php?action=doctors&report_type=workload');
        const data = await response.json();

        if (data.success) {
            this.renderWorkload(data.data);
        }
    }

    renderWorkload(workload) {
        const container = document.getElementById('workloadStats');
        
        const topDoctors = workload.slice(0, 10); // Top 10 doctors
        
        let html = '<div class="table-container"><table class="table is-fullwidth is-striped">';
        html += '<thead><tr><th>Doctor</th><th>Specialty</th><th>Total Appointments</th><th>Last 30 Days</th><th>Completed</th></tr></thead><tbody>';
        
        topDoctors.forEach(doctor => {
            html += `
                <tr>
                    <td>${this.escapeHtml(doctor.first_name)} ${this.escapeHtml(doctor.last_name)}</td>
                    <td>${this.escapeHtml(doctor.specialization || 'N/A')}</td>
                    <td><span class="tag is-info">${doctor.total_appointments}</span></td>
                    <td><span class="tag is-success">${doctor.appointments_last_30_days}</span></td>
                    <td><span class="tag is-primary">${doctor.completed_appointments}</span></td>
                </tr>
            `;
        });
        
        html += '</tbody></table></div>';
        container.innerHTML = html;

        // Workload chart
        const ctx = document.getElementById('workloadChart').getContext('2d');
        
        if (this.charts.workload) {
            this.charts.workload.destroy();
        }

        const top5 = workload.slice(0, 5);
        const labels = top5.map(d => `${d.first_name} ${d.last_name}`);
        const appointmentData = top5.map(d => parseInt(d.total_appointments));

        this.charts.workload = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Appointments',
                    data: appointmentData,
                    backgroundColor: '#36A2EB'
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
        this.filters.specialty = document.getElementById('specialtyFilter').value;
        this.filters.department = document.getElementById('departmentFilter').value;
        this.filters.search = document.getElementById('searchInput').value;
        
        this.directoryPage = 1; // Reset pagination
        this.loadDoctorStats();
        this.loadTabContent(this.currentTab);
        
        this.showNotification('Filters applied successfully', 'is-success');
    }

    exportReport(format) {
        const params = new URLSearchParams({
            action: 'export_doctors',
            format: format,
            report_type: this.currentTab,
            specialty: this.filters.specialty,
            department: this.filters.department
        });

        const link = document.createElement('a');
        link.href = `controllers/ReportsController.php?${params}`;
        link.download = `doctor_report_${this.currentTab}_${new Date().toISOString().split('T')[0]}.${format}`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        this.showNotification(`${format.toUpperCase()} export initiated`, 'is-success');
    }

    printReport() {
        // Get current doctor data from the table
        const currentData = this.currentDoctors || [];
        
        if (currentData.length === 0) {
            alert('No doctor data to print. Please load data first.');
            return;
        }
        
        // Generate print content
        this.generatePrintContent(currentData);
        
        // Trigger print
        window.print();
    }
    
    generatePrintContent(doctors) {
        const printArea = document.getElementById('doctorPrintArea');
        const currentDate = new Date().toLocaleDateString();
        const currentDateTime = new Date().toLocaleString();
        
        let doctorsHtml = '';
        if (doctors && doctors.length > 0) {
            doctorsHtml = `
                <table class="doctors-table">
                    <thead>
                        <tr>
                            <th style="width: 10%;">Doctor ID</th>
                            <th style="width: 25%;">Name</th>
                            <th style="width: 20%;">Specialty</th>
                            <th style="width: 15%;">Department</th>
                            <th style="width: 15%;">Phone</th>
                            <th style="width: 15%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            doctors.forEach((doctor, index) => {
                const fullName = `${doctor.first_name} ${doctor.last_name}`;
                doctorsHtml += `
                    <tr>
                        <td>#${doctor.id}</td>
                        <td class="doctor-name">${this.escapeHtml(fullName)}</td>
                        <td>${this.escapeHtml(doctor.specialization || 'N/A')}</td>
                        <td>${this.escapeHtml(doctor.department_name || 'N/A')}</td>
                        <td>${this.escapeHtml(doctor.phone || 'N/A')}</td>
                        <td>${doctor.status ? doctor.status.charAt(0).toUpperCase() + doctor.status.slice(1) : 'N/A'}</td>
                    </tr>
                `;
            });
            
            doctorsHtml += `
                    </tbody>
                </table>
            `;
        } else {
            doctorsHtml = '<table class="doctors-table"><tr><td colspan="6" style="text-align: center; font-style: italic;">No doctor data available.</td></tr></table>';
        }
        
        printArea.innerHTML = `
            <div class="print-header">
                <div class="print-title">Hospital Management System</div>
                <div class="print-subtitle">Doctor Directory Report</div>
            </div>
            
            <div class="print-info">
                <h3>Report Information</h3>
                <div class="print-info-grid">
                    <div><strong>Report Date:</strong> ${currentDate}</div>
                    <div><strong>Total Doctors:</strong> ${doctors.length}</div>
                    <div><strong>Specialty Filter:</strong> ${this.filters.specialty || 'All Specialties'}</div>
                    <div><strong>Department Filter:</strong> ${this.filters.department || 'All Departments'}</div>
                </div>
            </div>
            
            <div class="print-doctors">
                <h3>Doctor Directory (${doctors.length} records)</h3>
                ${doctorsHtml}
            </div>
            
            <div class="print-footer">
                <div>Report ID: DOC-${Date.now()}</div>
                <div>Generated on: ${currentDateTime} | Hospital Management System</div>
                <div style="margin-top: 4px; font-weight: bold;">👨‍⚕️ Doctor Directory Report - Internal Use Only</div>
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
function viewDoctor(doctorId) {
    openDoctorModal(doctorId);
}

async function openDoctorModal(doctorId) {
    const modal = document.getElementById('doctorModal');
    const modalContent = document.getElementById('doctorModalContent');
    
    // Show modal
    modal.classList.add('is-active');
    
    // Show loading state
    modalContent.innerHTML = `
        <div class="has-text-centered">
            <span class="icon is-large">
                <i class="fas fa-spinner fa-pulse fa-2x"></i>
            </span>
            <p class="mt-2">Loading doctor details...</p>
        </div>
    `;
    
    try {
        // Fetch doctor details from API
        const response = await fetch(`controllers/ReportsController.php?action=doctor_details&doctor_id=${doctorId}`);
        const data = await response.json();
        
        if (data.success) {
            const doctor = data.data;
            modalContent.innerHTML = `
                <div class="columns">
                    <div class="column">
                        <div class="doctor-info-card card">
                            <div class="card-header">
                                <p class="card-header-title">
                                    <span class="icon">
                                        <i class="fas fa-user-md"></i>
                                    </span>
                                    Personal Information
                                </p>
                            </div>
                            <div class="card-content">
                                <div class="content">
                                    <div class="field">
                                        <label class="label">Doctor ID</label>
                                        <p class="control">
                                            <span class="tag is-primary">#${doctor.id}</span>
                                        </p>
                                    </div>
                                    <div class="columns">
                                        <div class="column">
                                            <div class="field">
                                                <label class="label">First Name</label>
                                                <p>${doctor.first_name || 'N/A'}</p>
                                            </div>
                                        </div>
                                        <div class="column">
                                            <div class="field">
                                                <label class="label">Last Name</label>
                                                <p>${doctor.last_name || 'N/A'}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <label class="label">Specialty</label>
                                        <p>${doctor.specialization || 'N/A'}</p>
                                    </div>
                                    <div class="field">
                                        <label class="label">Department</label>
                                        <p>${doctor.department_name || 'N/A'}</p>
                                    </div>
                                    <div class="field">
                                        <label class="label">Status</label>
                                        <p><span class="tag ${doctor.status === 'active' ? 'is-success' : 'is-warning'}">${doctor.status}</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="column">
                        <div class="doctor-info-card card">
                            <div class="card-header">
                                <p class="card-header-title">
                                    <span class="icon">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                    Contact Information
                                </p>
                            </div>
                            <div class="card-content">
                                <div class="content">
                                    <div class="field">
                                        <label class="label">Phone Number</label>
                                        <p>${doctor.phone || 'N/A'}</p>
                                    </div>
                                    <div class="field">
                                        <label class="label">Email Address</label>
                                        <p>${doctor.email || 'N/A'}</p>
                                    </div>
                                    <div class="field">
                                        <label class="label">Registration Date</label>
                                        <p>${doctor.created_at ? new Date(doctor.created_at).toLocaleDateString() : 'N/A'}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else {
            modalContent.innerHTML = `
                <div class="notification is-danger">
                    <span class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </span>
                    <span>${data.message || 'Failed to load doctor details.'}</span>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading doctor details:', error);
        modalContent.innerHTML = `
            <div class="notification is-danger">
                <span class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </span>
                <span>Failed to load doctor details. Please try again.</span>
            </div>
        `;
    }
}

function closeDoctorModal() {
    const modal = document.getElementById('doctorModal');
    modal.classList.remove('is-active');
}

// Close modal when pressing Escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeDoctorModal();
    }
});

// Initialize the reports manager when the page loads
document.addEventListener('DOMContentLoaded', () => {
    window.doctorReportsManager = new DoctorReportsManager();
});
</script>

    </div> <!-- End of container from header.php -->
</body>
</html> 