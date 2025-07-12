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

.stat-card.scheduled {
    border-left-color: #209cee;
}

.stat-card.completed {
    border-left-color: #23d160;
}

.stat-card.cancelled {
    border-left-color: #ff3860;
}

.stat-card.today {
    border-left-color: #ffdd57;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #3273dc;
    display: block;
}

.stat-number.scheduled {
    color: #209cee;
}

.stat-number.completed {
    color: #23d160;
}

.stat-number.cancelled {
    color: #ff3860;
}

.stat-number.today {
    color: #ffdd57;
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

/* Status badges */
.status-badge.scheduled {
    background-color: #209cee;
    color: white;
}

.status-badge.completed {
    background-color: #23d160;
    color: white;
}

.status-badge.cancelled {
    background-color: #ff3860;
    color: white;
}

.appointment-card {
    border: 1px solid #e1e5e9;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.appointment-card .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.appointment-card .card-header-title {
    color: white;
}

@media (max-width: 768px) {
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
    
    #appointmentPrintArea,
    #appointmentPrintArea * {
        visibility: visible;
    }
    
    #appointmentPrintArea {
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
    
    .appointments-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #000;
        font-size: 9px;
        margin-bottom: 12px;
    }
    
    .appointments-table th,
    .appointments-table td {
        border: 1px solid #ccc;
        padding: 3px 4px;
        text-align: left;
        vertical-align: top;
    }
    
    .appointments-table th {
        background-color: #f5f5f5;
        font-weight: bold;
        font-size: 9px;
    }
    
    .appointments-table td {
        font-size: 8px;
        line-height: 1.2;
    }
    
    .appointments-table .date-cell {
        white-space: nowrap;
        min-width: 70px;
    }
    
    .appointments-table .time-cell {
        white-space: nowrap;
        min-width: 50px;
    }
    
    .appointments-table .status-cell {
        text-align: center;
        font-weight: bold;
    }
    
    .appointments-table .purpose-cell {
        max-width: 120px;
        word-wrap: break-word;
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
                    <li class="is-active"><a href="#"><span class="icon"><i class="fas fa-calendar-alt"></i></span><span>Appointment Reports</span></a></li>
                </ul>
            </nav>
            <h1 class="title is-3">
                <span class="icon">
                    <i class="fas fa-calendar-alt"></i>
                </span>
                Appointment Reports & Analytics
            </h1>
            <p class="subtitle">Comprehensive appointment data analysis and scheduling insights</p>
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
                        <input class="input" type="text" id="searchInput" placeholder="Search appointments..." />
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
                                <option value="scheduled">Scheduled</option>
                                <option value="completed">Completed</option>
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
    <div class="stats-grid" id="appointmentStats">
        <!-- Stats will be loaded here -->
    </div>

    <!-- Report Tabs -->
    <div class="report-tabs">
        <div class="tabs is-boxed">
            <ul>
                <li class="is-active" data-tab="status-analytics">
                    <a>
                        <span class="icon"><i class="fas fa-chart-pie"></i></span>
                        <span>Status Analytics</span>
                    </a>
                </li>
                <li data-tab="daily-schedule">
                    <a>
                        <span class="icon"><i class="fas fa-calendar-day"></i></span>
                        <span>Daily Schedule</span>
                    </a>
                </li>
                <li data-tab="doctor-performance">
                    <a>
                        <span class="icon"><i class="fas fa-user-md"></i></span>
                        <span>Doctor Performance</span>
                    </a>
                </li>
                <li data-tab="appointment-list">
                    <a>
                        <span class="icon"><i class="fas fa-list"></i></span>
                        <span>Appointment List</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab Content -->
    <div id="tabContent">
        <!-- Status Analytics Tab -->
        <div id="status-analytics-tab" class="tab-content is-active">
            <div class="columns">
                <div class="column is-6">
                    <div class="chart-box">
                        <h4 class="title is-5 mb-4">Appointment Status Distribution</h4>
                        <div class="chart-container">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="column is-6">
                    <div class="chart-box">
                        <h4 class="title is-5 mb-4">Monthly Appointment Trends</h4>
                        <div class="chart-container">
                            <canvas id="trendsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Schedule Tab -->
        <div id="daily-schedule-tab" class="tab-content">
            <div class="chart-box">
                <div class="columns is-vcentered mb-4">
                    <div class="column">
                        <h4 class="title is-5">Daily Schedule View</h4>
                    </div>
                    <div class="column is-narrow">
                        <div class="field">
                            <div class="control">
                                <input class="input" type="date" id="scheduleDate" />
                            </div>
                        </div>
                    </div>
                </div>
                <div id="dailyScheduleContent">
                    <!-- Daily schedule will be loaded here -->
                </div>
            </div>
        </div>

        <!-- Doctor Performance Tab -->
        <div id="doctor-performance-tab" class="tab-content">
            <div class="chart-box">
                <div class="columns is-vcentered mb-4">
                    <div class="column">
                        <h4 class="title is-5">Doctor Appointment Statistics</h4>
                    </div>
                    <div class="column is-narrow">
                        <div class="field has-addons">
                            <div class="control">
                                <input class="input" type="text" id="doctorSearchInput" placeholder="Search doctors..." />
                            </div>
                            <div class="control">
                                <button class="button is-primary" id="searchDoctors">
                                    <span class="icon"><i class="fas fa-search"></i></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="doctorChart"></canvas>
                </div>
            </div>
            <div class="data-table mt-4">
                <div class="columns is-vcentered mb-3">
                    <div class="column">
                        <h5 class="title is-6">Doctor Performance Details</h5>
                    </div>
                    <div class="column is-narrow">
                        <div class="field">
                            <div class="control">
                                <div class="select">
                                    <select id="doctorRecordsPerPage">
                                        <option value="5" selected>5 per page</option>
                                        <option value="10">10 per page</option>
                                        <option value="20">20 per page</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table is-fullwidth is-striped">
                    <thead>
                        <tr>
                            <th>Doctor</th>
                            <th>Specialty</th>
                            <th>Total Appointments</th>
                            <th>Completed</th>
                            <th>Scheduled</th>
                            <th>Cancelled</th>
                            <th>Completion Rate</th>
                        </tr>
                    </thead>
                    <tbody id="doctorPerformanceTable">
                        <!-- Doctor performance data will be loaded here -->
                    </tbody>
                </table>
                
                <!-- Doctor Performance Pagination -->
                <div class="pagination-controls mt-4" id="doctorPaginationContainer" style="display: none;">
                    <nav class="pagination is-centered" role="navigation" aria-label="pagination">
                        <a class="pagination-previous" id="doctorPrevPage">Previous</a>
                        <a class="pagination-next" id="doctorNextPage">Next page</a>
                        <ul class="pagination-list" id="doctorPaginationList">
                            <!-- Pagination items will be generated here -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Appointment List Tab -->
        <div id="appointment-list-tab" class="tab-content">
            <div class="chart-box">
                <div class="columns is-vcentered mb-4">
                    <div class="column">
                        <h4 class="title is-5">All Appointments</h4>
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
                    <table class="table is-fullwidth is-striped">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Purpose</th>
                                <th>Status</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody id="appointmentListTable">
                            <!-- Appointment list will be loaded here -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination-controls mt-4" id="paginationContainer" style="display: none;">
                    <nav class="pagination is-centered" role="navigation" aria-label="pagination">
                        <a class="pagination-previous" id="prevPage">Previous</a>
                        <a class="pagination-next" id="nextPage">Next page</a>
                        <ul class="pagination-list" id="paginationList">
                            <!-- Pagination items will be generated here -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay" style="display: none;">
        <div class="has-text-centered">
            <div class="loading-spinner"></div>
            <p class="mt-2">Loading report data...</p>
        </div>
    </div>

    <!-- Print Area (Hidden) -->
    <div id="appointmentPrintArea" style="display: none;">
        <!-- Print content will be generated here -->
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Reports JavaScript -->
<script>
class AppointmentReports {
    constructor() {
        this.currentTab = 'status-analytics';
        this.currentPage = 1;
        this.itemsPerPage = 20;
        this.doctorCurrentPage = 1;
        this.doctorItemsPerPage = 5;
        this.charts = {};
        this.reportData = {};
        
        this.initializeEventListeners();
        this.loadReportData();
        this.setDefaultDates();
    }

    initializeEventListeners() {
        // Tab switching
        document.querySelectorAll('.tabs li').forEach(tab => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();
                this.switchTab(tab.dataset.tab);
            });
        });

        // Filter controls
        document.getElementById('applyFilters').addEventListener('click', () => {
            this.loadReportData();
        });

        document.getElementById('searchInput').addEventListener('input', () => {
            clearTimeout(this.searchTimer);
            this.searchTimer = setTimeout(() => {
                if (this.currentTab === 'appointment-list') {
                    this.currentPage = 1;
                    this.loadAppointmentList();
                }
            }, 500);
        });

        document.getElementById('statusFilter').addEventListener('change', () => {
            this.loadReportData();
            if (this.currentTab === 'appointment-list') {
                this.currentPage = 1;
                this.loadAppointmentList();
            }
        });

        document.getElementById('recordsPerPage').addEventListener('change', () => {
            this.itemsPerPage = parseInt(document.getElementById('recordsPerPage').value);
            this.currentPage = 1;
            this.loadAppointmentList();
        });

        document.getElementById('scheduleDate').addEventListener('change', () => {
            this.loadDailySchedule();
        });

        // Doctor search functionality
        document.getElementById('doctorSearchInput').addEventListener('input', () => {
            clearTimeout(this.doctorSearchTimer);
            this.doctorSearchTimer = setTimeout(() => {
                this.doctorCurrentPage = 1;
                this.loadDoctorPerformance();
            }, 500);
        });

        document.getElementById('searchDoctors').addEventListener('click', () => {
            this.doctorCurrentPage = 1;
            this.loadDoctorPerformance();
        });

        document.getElementById('doctorRecordsPerPage').addEventListener('change', () => {
            this.doctorItemsPerPage = parseInt(document.getElementById('doctorRecordsPerPage').value);
            this.doctorCurrentPage = 1;
            this.loadDoctorPerformance();
        });

        // Export and print
        document.getElementById('exportExcel').addEventListener('click', () => {
            this.exportToExcel();
        });

        document.getElementById('printReport').addEventListener('click', () => {
            this.printReport();
        });

        // Pagination for appointments
        document.getElementById('prevPage').addEventListener('click', () => {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.loadAppointmentList();
            }
        });

        document.getElementById('nextPage').addEventListener('click', () => {
            this.currentPage++;
            this.loadAppointmentList();
        });

        // Pagination for doctor performance
        document.getElementById('doctorPrevPage').addEventListener('click', () => {
            if (this.doctorCurrentPage > 1) {
                this.doctorCurrentPage--;
                this.loadDoctorPerformance();
            }
        });

        document.getElementById('doctorNextPage').addEventListener('click', () => {
            this.doctorCurrentPage++;
            this.loadDoctorPerformance();
        });
    }

    setDefaultDates() {
        const today = new Date();
        const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
        
        document.getElementById('fromDate').value = firstDayOfMonth.toISOString().split('T')[0];
        document.getElementById('toDate').value = today.toISOString().split('T')[0];
        document.getElementById('scheduleDate').value = today.toISOString().split('T')[0];
    }

    switchTab(tabName) {
        // Update active tab
        document.querySelectorAll('.tabs li').forEach(tab => {
            tab.classList.remove('is-active');
        });
        document.querySelector(`[data-tab="${tabName}"]`).classList.add('is-active');

        // Show active content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('is-active');
        });
        document.getElementById(`${tabName}-tab`).classList.add('is-active');

        this.currentTab = tabName;

        // Load tab-specific data
        switch (tabName) {
            case 'status-analytics':
                this.loadStatusAnalytics();
                break;
            case 'daily-schedule':
                this.loadDailySchedule();
                break;
            case 'doctor-performance':
                this.loadDoctorPerformance();
                break;
            case 'appointment-list':
                this.loadAppointmentList();
                break;
        }
    }

    async loadReportData() {
        this.showLoading();
        
        try {
            const fromDate = document.getElementById('fromDate').value;
            const toDate = document.getElementById('toDate').value;
            const status = document.getElementById('statusFilter').value;
            
            const params = new URLSearchParams({
                action: 'appointment_reports',
                from_date: fromDate,
                to_date: toDate,
                status: status
            });

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.reportData = data.data;
                this.renderStatistics();
                this.loadTabContent();
            } else {
                this.showError('Failed to load report data: ' + data.message);
            }
        } catch (error) {
            console.error('Error loading report data:', error);
            this.showError('Error loading report data. Please try again.');
        } finally {
            this.hideLoading();
        }
    }

    renderStatistics() {
        const stats = this.reportData.statistics;
        const statsContainer = document.getElementById('appointmentStats');
        
        statsContainer.innerHTML = `
            <div class="stat-card">
                <span class="stat-number">${stats.total || 0}</span>
                <div class="stat-label">Total Appointments</div>
            </div>
            <div class="stat-card scheduled">
                <span class="stat-number scheduled">${stats.scheduled || 0}</span>
                <div class="stat-label">Scheduled</div>
            </div>
            <div class="stat-card completed">
                <span class="stat-number completed">${stats.completed || 0}</span>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-card cancelled">
                <span class="stat-number cancelled">${stats.cancelled || 0}</span>
                <div class="stat-label">Cancelled</div>
            </div>
            <div class="stat-card today">
                <span class="stat-number today">${stats.today || 0}</span>
                <div class="stat-label">Today's Appointments</div>
            </div>
        `;
    }

    loadTabContent() {
        switch (this.currentTab) {
            case 'status-analytics':
                this.loadStatusAnalytics();
                break;
            case 'daily-schedule':
                this.loadDailySchedule();
                break;
            case 'doctor-performance':
                this.loadDoctorPerformance();
                break;
            case 'appointment-list':
                this.loadAppointmentList();
                break;
        }
    }

    loadStatusAnalytics() {
        this.renderStatusChart();
        this.renderTrendsChart();
    }

    renderStatusChart() {
        const ctx = document.getElementById('statusChart').getContext('2d');
        const stats = this.reportData.statistics;
        
        if (this.charts.statusChart) {
            this.charts.statusChart.destroy();
        }
        
        this.charts.statusChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Scheduled', 'Completed', 'Cancelled'],
                datasets: [{
                    data: [stats.scheduled || 0, stats.completed || 0, stats.cancelled || 0],
                    backgroundColor: ['#209cee', '#23d160', '#ff3860'],
                    borderWidth: 2,
                    borderColor: '#fff'
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

    renderTrendsChart() {
        const ctx = document.getElementById('trendsChart').getContext('2d');
        const trends = this.reportData.trends || [];
        
        if (this.charts.trendsChart) {
            this.charts.trendsChart.destroy();
        }
        
        const labels = trends.map(item => item.month);
        const data = trends.map(item => item.total);
        
        this.charts.trendsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Appointments',
                    data: data,
                    borderColor: '#3273dc',
                    backgroundColor: 'rgba(50, 115, 220, 0.1)',
                    borderWidth: 2,
                    fill: true
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

    async loadDailySchedule() {
        const date = document.getElementById('scheduleDate').value;
        
        try {
            const params = new URLSearchParams({
                action: 'daily_schedule',
                date: date
            });

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderDailySchedule(data.data);
            } else {
                this.showError('Failed to load daily schedule: ' + data.message);
            }
        } catch (error) {
            console.error('Error loading daily schedule:', error);
            this.showError('Error loading daily schedule.');
        }
    }

    renderDailySchedule(appointments) {
        const container = document.getElementById('dailyScheduleContent');
        
        if (appointments.length === 0) {
            container.innerHTML = `
                <div class="has-text-centered py-6">
                    <span class="icon is-large has-text-grey-light">
                        <i class="fas fa-calendar-times fa-2x"></i>
                    </span>
                    <p class="title is-6 has-text-grey">No appointments scheduled for this date</p>
                </div>
            `;
            return;
        }

        let html = '<div class="columns is-multiline">';
        
        appointments.forEach(appointment => {
            const statusClass = appointment.status;
            const time = new Date(appointment.appointment_date).toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit'
            });
            
            html += `
                <div class="column is-6">
                    <div class="appointment-card">
                        <div class="card-header">
                            <p class="card-header-title">
                                <span class="icon"><i class="fas fa-clock"></i></span>
                                ${time} - ${appointment.patient_name}
                            </p>
                            <span class="tag ${statusClass} status-badge">${appointment.status}</span>
                        </div>
                        <div class="card-content">
                            <div class="content">
                                <p><strong>Doctor:</strong> ${appointment.doctor_name}</p>
                                <p><strong>Specialty:</strong> ${appointment.specialty}</p>
                                <p><strong>Purpose:</strong> ${appointment.purpose}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        container.innerHTML = html;
    }

    async loadDoctorPerformance() {
        try {
            const search = document.getElementById('doctorSearchInput')?.value || '';
            const fromDate = document.getElementById('fromDate').value;
            const toDate = document.getElementById('toDate').value;
            
            const params = new URLSearchParams({
                action: 'doctor_performance',
                page: this.doctorCurrentPage,
                limit: this.doctorItemsPerPage,
                search: search,
                from_date: fromDate,
                to_date: toDate
            });

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderDoctorChart(data.data.doctors);
                this.renderDoctorTable(data.data.doctors);
                this.renderDoctorPagination(data.data.pagination);
            } else {
                // Fallback to the original data structure if new endpoint doesn't exist
                const doctors = this.reportData.doctor_performance || [];
                this.renderDoctorChart(doctors);
                this.renderDoctorTable(doctors);
            }
        } catch (error) {
            console.error('Error loading doctor performance:', error);
            // Fallback to the original method
            const doctors = this.reportData.doctor_performance || [];
            this.renderDoctorChart(doctors);
            this.renderDoctorTable(doctors);
        }
    }

    renderDoctorChart(doctors) {
        const ctx = document.getElementById('doctorChart').getContext('2d');
        
        if (this.charts.doctorChart) {
            this.charts.doctorChart.destroy();
        }
        
        const labels = doctors.slice(0, 10).map(doctor => doctor.doctor_name); // Show top 10 in chart
        const data = doctors.slice(0, 10).map(doctor => doctor.total_appointments);
        
        this.charts.doctorChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Appointments',
                    data: data,
                    backgroundColor: '#3273dc',
                    borderColor: '#2366d1',
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

    renderDoctorTable(doctors) {
        const tbody = document.getElementById('doctorPerformanceTable');
        
        if (doctors.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="has-text-centered py-4">
                        <span class="icon is-large has-text-grey-light">
                            <i class="fas fa-user-md fa-2x"></i>
                        </span>
                        <p class="title is-6 has-text-grey">No doctors found</p>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = doctors.map(doctor => {
            const completionRate = doctor.total_appointments > 0 
                ? ((doctor.completed / doctor.total_appointments) * 100).toFixed(1)
                : '0.0';
                
            return `
                <tr>
                    <td>${doctor.doctor_name}</td>
                    <td>${doctor.specialty}</td>
                    <td><strong>${doctor.total_appointments}</strong></td>
                    <td><span class="tag is-success">${doctor.completed}</span></td>
                    <td><span class="tag is-info">${doctor.scheduled}</span></td>
                    <td><span class="tag is-danger">${doctor.cancelled}</span></td>
                    <td><strong>${completionRate}%</strong></td>
                </tr>
            `;
        }).join('');
    }

    renderDoctorPagination(pagination) {
        const container = document.getElementById('doctorPaginationContainer');
        const list = document.getElementById('doctorPaginationList');
        
        if (!pagination || pagination.total_pages <= 1) {
            container.style.display = 'none';
            return;
        }

        container.style.display = 'block';
        
        // Update Previous/Next buttons
        document.getElementById('doctorPrevPage').classList.toggle('is-disabled', this.doctorCurrentPage === 1);
        document.getElementById('doctorNextPage').classList.toggle('is-disabled', this.doctorCurrentPage === pagination.total_pages);

        // Generate page numbers
        list.innerHTML = '';
        const startPage = Math.max(1, this.doctorCurrentPage - 2);
        const endPage = Math.min(pagination.total_pages, this.doctorCurrentPage + 2);

        for (let i = startPage; i <= endPage; i++) {
            const li = document.createElement('li');
            li.innerHTML = `
                <a class="pagination-link ${i === this.doctorCurrentPage ? 'is-current' : ''}" 
                   onclick="appointmentReports.goToDoctorPage(${i})">${i}</a>
            `;
            list.appendChild(li);
        }
    }

    goToDoctorPage(page) {
        this.doctorCurrentPage = page;
        this.loadDoctorPerformance();
    }

    async loadAppointmentList() {
        try {
            this.showLoading();
            
            const search = document.getElementById('searchInput').value;
            const status = document.getElementById('statusFilter').value;
            const fromDate = document.getElementById('fromDate').value;
            const toDate = document.getElementById('toDate').value;
            
            const params = new URLSearchParams({
                action: 'appointment_list',
                page: this.currentPage,
                limit: this.itemsPerPage,
                search: search,
                status: status,
                from_date: fromDate,
                to_date: toDate
            });

            console.log('Loading appointment list with params:', params.toString());

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Appointment list response:', data);

            if (data.success) {
                this.renderAppointmentList(data.data.appointments);
                this.renderPagination(data.data.pagination);
            } else {
                console.error('API Error:', data.message);
                this.showError('Failed to load appointment list: ' + data.message);
            }
        } catch (error) {
            console.error('Error loading appointment list:', error);
            this.showError('Error loading appointment list: ' + error.message);
        } finally {
            this.hideLoading();
        }
    }

    renderAppointmentList(appointments) {
        const tbody = document.getElementById('appointmentListTable');
        
        if (appointments.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="has-text-centered py-4">
                        <span class="icon is-large has-text-grey-light">
                            <i class="fas fa-calendar-times fa-2x"></i>
                        </span>
                        <p class="title is-6 has-text-grey">No appointments found</p>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = appointments.map(appointment => {
            const dateTime = new Date(appointment.appointment_date);
            const formattedDate = dateTime.toLocaleDateString();
            const formattedTime = dateTime.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit'
            });
            const createdDate = new Date(appointment.created_at).toLocaleDateString();
            
            return `
                <tr>
                    <td>
                        <strong>${formattedDate}</strong><br>
                        <small class="has-text-grey">${formattedTime}</small>
                    </td>
                    <td>${appointment.patient_name}</td>
                    <td>
                        ${appointment.doctor_name}<br>
                        <small class="has-text-grey">${appointment.specialty}</small>
                    </td>
                    <td>${appointment.purpose}</td>
                    <td><span class="tag status-badge ${appointment.status}">${appointment.status}</span></td>
                    <td>${createdDate}</td>
                </tr>
            `;
        }).join('');
    }

    renderPagination(pagination) {
        const container = document.getElementById('paginationContainer');
        const list = document.getElementById('paginationList');
        
        if (!pagination || pagination.total_pages <= 1) {
            container.style.display = 'none';
            return;
        }

        container.style.display = 'block';
        
        // Update Previous/Next buttons
        document.getElementById('prevPage').classList.toggle('is-disabled', this.currentPage === 1);
        document.getElementById('nextPage').classList.toggle('is-disabled', this.currentPage === pagination.total_pages);

        // Generate page numbers
        list.innerHTML = '';
        const startPage = Math.max(1, this.currentPage - 2);
        const endPage = Math.min(pagination.total_pages, this.currentPage + 2);

        for (let i = startPage; i <= endPage; i++) {
            const li = document.createElement('li');
            li.innerHTML = `
                <a class="pagination-link ${i === this.currentPage ? 'is-current' : ''}" 
                   onclick="appointmentReports.goToPage(${i})">${i}</a>
            `;
            list.appendChild(li);
        }
    }

    goToPage(page) {
        if (page !== this.currentPage) {
            this.currentPage = page;
            this.loadAppointmentList();
        }
    }

    exportToExcel() {
        // Implementation for Excel export
        const fromDate = document.getElementById('fromDate').value;
        const toDate = document.getElementById('toDate').value;
        const status = document.getElementById('statusFilter').value;
        
        const params = new URLSearchParams({
            action: 'export_appointments',
            format: 'excel',
            from_date: fromDate,
            to_date: toDate,
            status: status
        });

        window.open(`controllers/ReportsController.php?${params}`, '_blank');
    }

    async printReport() {
        const printArea = document.getElementById('appointmentPrintArea');
        const fromDate = document.getElementById('fromDate').value;
        const toDate = document.getElementById('toDate').value;
        const stats = this.reportData.statistics;
        
        // Get all appointments for printing (not just current page)
        try {
            const search = document.getElementById('searchInput').value;
            const status = document.getElementById('statusFilter').value;
            
            const params = new URLSearchParams({
                action: 'appointment_list',
                page: 1,
                limit: 1000, // Get all appointments for printing
                search: search,
                status: status,
                from_date: fromDate,
                to_date: toDate
            });

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            const data = await response.json();
            
            let appointmentsTableHTML = '';
            
            if (data.success && data.data.appointments.length > 0) {
                appointmentsTableHTML = `
                    <div class="print-appointments">
                        <h3>Appointment Details</h3>
                        <table class="appointments-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Specialty</th>
                                    <th>Purpose</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>`;
                
                data.data.appointments.forEach(appointment => {
                    const dateTime = new Date(appointment.appointment_date);
                    const formattedDate = dateTime.toLocaleDateString();
                    const formattedTime = dateTime.toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    
                    appointmentsTableHTML += `
                        <tr>
                            <td class="date-cell">${formattedDate}</td>
                            <td class="time-cell">${formattedTime}</td>
                            <td>${appointment.patient_name}</td>
                            <td>${appointment.doctor_name}</td>
                            <td>${appointment.specialty}</td>
                            <td class="purpose-cell">${appointment.purpose}</td>
                            <td class="status-cell">${appointment.status.charAt(0).toUpperCase() + appointment.status.slice(1)}</td>
                        </tr>`;
                });
                
                appointmentsTableHTML += `
                            </tbody>
                        </table>
                    </div>`;
            } else {
                appointmentsTableHTML = `
                    <div class="print-appointments">
                        <h3>Appointment Details</h3>
                        <p>No appointments found for the selected criteria.</p>
                    </div>`;
            }
            
            const filterInfo = [];
            if (search) filterInfo.push(`Search: "${search}"`);
            if (status) filterInfo.push(`Status: ${status.charAt(0).toUpperCase() + status.slice(1)}`);
            const filterText = filterInfo.length > 0 ? filterInfo.join(', ') : 'All appointments';
            
            const printContent = `
                <div class="print-header">
                    <div class="print-title">Hospital Management System</div>
                    <div class="print-subtitle">Appointment Reports</div>
                    <div class="print-subtitle">Generated on: ${new Date().toLocaleDateString()}</div>
                </div>
                
                <div class="print-info">
                    <h3>Report Information</h3>
                    <div class="print-info-grid">
                        <div><strong>Date Range:</strong> ${fromDate} to ${toDate}</div>
                        <div><strong>Generated By:</strong> System Administrator</div>
                        <div><strong>Filters Applied:</strong> ${filterText}</div>
                        <div><strong>Report Date:</strong> ${new Date().toLocaleDateString()}</div>
                        <div><strong>Total Appointments:</strong> ${stats.total || 0}</div>
                        <div><strong>Records Shown:</strong> ${data.success ? data.data.appointments.length : 0}</div>
                    </div>
                </div>
                
                <div class="print-appointments">
                    <h3>Appointment Statistics Summary</h3>
                    <table class="appointments-table">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Count</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Scheduled</td>
                                <td>${stats.scheduled || 0}</td>
                                <td>${stats.total > 0 ? ((stats.scheduled / stats.total) * 100).toFixed(1) : 0}%</td>
                            </tr>
                            <tr>
                                <td>Completed</td>
                                <td>${stats.completed || 0}</td>
                                <td>${stats.total > 0 ? ((stats.completed / stats.total) * 100).toFixed(1) : 0}%</td>
                            </tr>
                            <tr>
                                <td>Cancelled</td>
                                <td>${stats.cancelled || 0}</td>
                                <td>${stats.total > 0 ? ((stats.cancelled / stats.total) * 100).toFixed(1) : 0}%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                ${appointmentsTableHTML}
                
                <div class="print-footer">
                    <div>© ${new Date().getFullYear()} Hospital Management System</div>
                    <div>This is a computer-generated report</div>
                    <div>Printed on: ${new Date().toLocaleString()}</div>
                </div>
            `;
            
            printArea.innerHTML = printContent;
            printArea.style.display = 'block';
            window.print();
            printArea.style.display = 'none';
            
        } catch (error) {
            console.error('Error loading appointments for print:', error);
            this.showError('Error loading appointment data for printing: ' + error.message);
        }
    }

    showLoading() {
        document.getElementById('loadingOverlay').style.display = 'flex';
    }

    hideLoading() {
        document.getElementById('loadingOverlay').style.display = 'none';
    }

    showError(message) {
        // Create a better notification instead of alert
        const notification = document.createElement('div');
        notification.className = 'notification is-danger';
        notification.innerHTML = `
            <button class="delete" onclick="this.parentElement.remove()"></button>
            <strong>Error:</strong> ${message}
        `;
        
        // Insert at the top of the page
        const container = document.querySelector('.page-transition');
        container.insertBefore(notification, container.firstChild);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
        
        // Also log to console for debugging
        console.error('Error:', message);
    }

    showSuccess(message) {
        const notification = document.createElement('div');
        notification.className = 'notification is-success';
        notification.innerHTML = `
            <button class="delete" onclick="this.parentElement.remove()"></button>
            ${message}
        `;
        
        const container = document.querySelector('.page-transition');
        container.insertBefore(notification, container.firstChild);
        
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 3000);
    }
}

// Initialize the reports system
document.addEventListener('DOMContentLoaded', function() {
    window.appointmentReports = new AppointmentReports();
});
</script>

</div> <!-- End of container from header.php -->
</body>
</html> 