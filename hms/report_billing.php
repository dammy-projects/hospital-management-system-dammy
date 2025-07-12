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

.stat-card.revenue {
    border-left-color: #23d160;
}

.stat-card.pending {
    border-left-color: #ffdd57;
}

.stat-card.paid {
    border-left-color: #23d160;
}

.stat-card.outstanding {
    border-left-color: #ff3860;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #3273dc;
    display: block;
}

.stat-number.revenue {
    color: #23d160;
}

.stat-number.pending {
    color: #ffdd57;
}

.stat-number.paid {
    color: #23d160;
}

.stat-number.outstanding {
    color: #ff3860;
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

/* Payment status badges */
.status-badge.paid {
    background-color: #23d160;
    color: white;
}

.status-badge.pending {
    background-color: #ffdd57;
    color: #333;
}

.status-badge.cancelled {
    background-color: #ff3860;
    color: white;
}

.billing-card {
    border: 1px solid #e1e5e9;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.billing-card .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.billing-card .card-header-title {
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
    
    #billingPrintArea,
    #billingPrintArea * {
        visibility: visible;
    }
    
    #billingPrintArea {
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
    
    .billing-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #000;
        font-size: 9px;
        margin-bottom: 12px;
    }
    
    .billing-table th,
    .billing-table td {
        border: 1px solid #ccc;
        padding: 3px 4px;
        text-align: left;
        vertical-align: top;
    }
    
    .billing-table th {
        background-color: #f5f5f5;
        font-weight: bold;
        font-size: 9px;
    }
    
    .billing-table td {
        font-size: 8px;
        line-height: 1.2;
    }
    
    .billing-table .amount-cell {
        text-align: right;
        font-weight: bold;
    }
    
    .billing-table .status-cell {
        text-align: center;
        font-weight: bold;
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
                    <li class="is-active"><a href="#"><span class="icon"><i class="fas fa-file-invoice-dollar"></i></span><span>Billing Reports</span></a></li>
                </ul>
            </nav>
            <h1 class="title is-3">
                <span class="icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </span>
                Billing Reports & Analytics
            </h1>
            <p class="subtitle">Comprehensive financial data analysis and revenue tracking</p>
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
                        <input class="input" type="text" id="searchInput" placeholder="Search billing..." />
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
                    <label class="label">Payment Status</label>
                    <div class="control">
                        <div class="select is-fullwidth">
                            <select id="paymentStatusFilter">
                                <option value="">All Status</option>
                                <option value="paid">Paid</option>
                                <option value="pending">Pending</option>
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
    <div class="stats-grid" id="billingStats">
        <!-- Stats will be loaded here -->
    </div>

    <!-- Report Tabs -->
    <div class="report-tabs">
        <div class="tabs is-boxed">
            <ul>
                <li class="is-active" data-tab="revenue-analytics">
                    <a>
                        <span class="icon"><i class="fas fa-chart-line"></i></span>
                        <span>Revenue Analytics</span>
                    </a>
                </li>
                <li data-tab="payment-status">
                    <a>
                        <span class="icon"><i class="fas fa-credit-card"></i></span>
                        <span>Payment Status</span>
                    </a>
                </li>
                <li data-tab="billing-history">
                    <a>
                        <span class="icon"><i class="fas fa-history"></i></span>
                        <span>Billing History</span>
                    </a>
                </li>
                <li data-tab="financial-summary">
                    <a>
                        <span class="icon"><i class="fas fa-calculator"></i></span>
                        <span>Financial Summary</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab Content -->
    <div id="tabContent">
        <!-- Revenue Analytics Tab -->
        <div id="revenue-analytics-tab" class="tab-content is-active">
            <div class="columns">
                <div class="column is-6">
                    <div class="chart-box">
                        <h4 class="title is-5 mb-4">Revenue Trends</h4>
                        <div class="chart-container">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="column is-6">
                    <div class="chart-box">
                        <h4 class="title is-5 mb-4">Payment Method Distribution</h4>
                        <div class="chart-container">
                            <canvas id="paymentMethodChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Status Tab -->
        <div id="payment-status-tab" class="tab-content">
            <div class="columns">
                <div class="column is-6">
                    <div class="chart-box">
                        <h4 class="title is-5 mb-4">Payment Status Distribution</h4>
                        <div class="chart-container">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="column is-6">
                    <div class="chart-box">
                        <h4 class="title is-5 mb-4">Insurance Claims Status</h4>
                        <div class="chart-container">
                            <canvas id="insuranceChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Billing History Tab -->
        <div id="billing-history-tab" class="tab-content">
            <div class="chart-box">
                <div class="columns is-vcentered mb-4">
                    <div class="column">
                        <h4 class="title is-5">All Billing Records</h4>
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
                                <th>Billing ID</th>
                                <th>Patient</th>
                                <th>Amount</th>
                                <th>Payment Status</th>
                                <th>Insurance Status</th>
                                <th>Billing Date</th>
                            </tr>
                        </thead>
                        <tbody id="billingHistoryTable">
                            <!-- Billing history will be loaded here -->
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

        <!-- Financial Summary Tab -->
        <div id="financial-summary-tab" class="tab-content">
            <div class="chart-box">
                <h4 class="title is-5 mb-4">Financial Performance Overview</h4>
                <div id="financialSummaryContent">
                    <!-- Financial summary will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay" style="display: none;">
        <div class="has-text-centered">
            <div class="loading-spinner"></div>
            <p class="mt-2">Loading billing data...</p>
        </div>
    </div>

    <!-- Print Area (Hidden) -->
    <div id="billingPrintArea" style="display: none;">
        <!-- Print content will be generated here -->
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Billing Reports JavaScript -->
<script>
class BillingReports {
    constructor() {
        this.currentTab = 'revenue-analytics';
        this.currentPage = 1;
        this.itemsPerPage = 20;
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
                if (this.currentTab === 'billing-history') {
                    this.currentPage = 1;
                    this.loadBillingHistory();
                }
            }, 500);
        });

        document.getElementById('paymentStatusFilter').addEventListener('change', () => {
            this.loadReportData();
            if (this.currentTab === 'billing-history') {
                this.currentPage = 1;
                this.loadBillingHistory();
            }
        });

        document.getElementById('recordsPerPage').addEventListener('change', () => {
            this.itemsPerPage = parseInt(document.getElementById('recordsPerPage').value);
            this.currentPage = 1;
            this.loadBillingHistory();
        });

        // Export and print
        document.getElementById('exportExcel').addEventListener('click', () => {
            this.exportToExcel();
        });

        document.getElementById('printReport').addEventListener('click', () => {
            this.printReport();
        });

        // Pagination
        document.getElementById('prevPage').addEventListener('click', () => {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.loadBillingHistory();
            }
        });

        document.getElementById('nextPage').addEventListener('click', () => {
            this.currentPage++;
            this.loadBillingHistory();
        });
    }

    setDefaultDates() {
        const today = new Date();
        const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
        
        document.getElementById('fromDate').value = firstDayOfMonth.toISOString().split('T')[0];
        document.getElementById('toDate').value = today.toISOString().split('T')[0];
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
            case 'revenue-analytics':
                this.loadRevenueAnalytics();
                break;
            case 'payment-status':
                this.loadPaymentStatus();
                break;
            case 'billing-history':
                this.loadBillingHistory();
                break;
            case 'financial-summary':
                this.loadFinancialSummary();
                break;
        }
    }

    async loadReportData() {
        this.showLoading();
        
        try {
            const fromDate = document.getElementById('fromDate').value;
            const toDate = document.getElementById('toDate').value;
            const paymentStatus = document.getElementById('paymentStatusFilter').value;
            
            const params = new URLSearchParams({
                action: 'billing_reports',
                from_date: fromDate,
                to_date: toDate,
                payment_status: paymentStatus
            });

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.reportData = data.data;
                this.renderStatistics();
                this.loadTabContent();
            } else {
                this.showError('Failed to load billing data: ' + data.message);
            }
        } catch (error) {
            console.error('Error loading billing data:', error);
            this.showError('Error loading billing data. Please try again.');
        } finally {
            this.hideLoading();
        }
    }

    renderStatistics() {
        const stats = this.reportData.statistics;
        const statsContainer = document.getElementById('billingStats');
        
        statsContainer.innerHTML = `
            <div class="stat-card revenue">
                <span class="stat-number revenue">PHP ${this.formatCurrency(stats.total_revenue || 0)}</span>
                <div class="stat-label">Total Revenue</div>
            </div>
            <div class="stat-card paid">
                <span class="stat-number paid">PHP ${this.formatCurrency(stats.paid_amount || 0)}</span>
                <div class="stat-label">Paid Amount</div>
            </div>
            <div class="stat-card pending">
                <span class="stat-number pending">PHP ${this.formatCurrency(stats.pending_amount || 0)}</span>
                <div class="stat-label">Pending Amount</div>
            </div>
            <div class="stat-card outstanding">
                <span class="stat-number outstanding">${stats.outstanding_bills || 0}</span>
                <div class="stat-label">Outstanding Bills</div>
            </div>
            <div class="stat-card">
                <span class="stat-number">${stats.total_bills || 0}</span>
                <div class="stat-label">Total Bills</div>
            </div>
        `;
    }

    loadTabContent() {
        switch (this.currentTab) {
            case 'revenue-analytics':
                this.loadRevenueAnalytics();
                break;
            case 'payment-status':
                this.loadPaymentStatus();
                break;
            case 'billing-history':
                this.loadBillingHistory();
                break;
            case 'financial-summary':
                this.loadFinancialSummary();
                break;
        }
    }

    loadRevenueAnalytics() {
        this.renderRevenueChart();
        this.renderPaymentMethodChart();
    }

    renderRevenueChart() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const trends = this.reportData.revenue_trends || [];
        
        if (this.charts.revenueChart) {
            this.charts.revenueChart.destroy();
        }
        
        const labels = trends.map(item => item.month);
        const data = trends.map(item => parseFloat(item.total_revenue));
        
        this.charts.revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenue (PHP)',
                    data: data,
                    borderColor: '#23d160',
                    backgroundColor: 'rgba(35, 209, 96, 0.1)',
                    borderWidth: 3,
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
                            callback: function(value) {
                                return 'PHP ' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: PHP ' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    renderPaymentMethodChart() {
        const ctx = document.getElementById('paymentMethodChart').getContext('2d');
        const stats = this.reportData.statistics;
        
        if (this.charts.paymentMethodChart) {
            this.charts.paymentMethodChart.destroy();
        }
        
        // Mock data for payment methods (you can enhance this with real data)
        const paymentMethods = [
            { method: 'Insurance', amount: stats.paid_amount * 0.6 },
            { method: 'Cash', amount: stats.paid_amount * 0.25 },
            { method: 'Credit Card', amount: stats.paid_amount * 0.15 }
        ];
        
        this.charts.paymentMethodChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: paymentMethods.map(item => item.method),
                datasets: [{
                    data: paymentMethods.map(item => item.amount),
                    backgroundColor: ['#3273dc', '#23d160', '#ffdd57'],
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
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': PHP ' + context.parsed.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    loadPaymentStatus() {
        this.renderStatusChart();
        this.renderInsuranceChart();
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
                labels: ['Paid', 'Pending', 'Cancelled'],
                datasets: [{
                    data: [stats.paid_count || 0, stats.pending_count || 0, stats.cancelled_count || 0],
                    backgroundColor: ['#23d160', '#ffdd57', '#ff3860'],
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

    renderInsuranceChart() {
        const ctx = document.getElementById('insuranceChart').getContext('2d');
        const stats = this.reportData.statistics;
        
        if (this.charts.insuranceChart) {
            this.charts.insuranceChart.destroy();
        }
        
        this.charts.insuranceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Approved', 'Pending', 'Rejected'],
                datasets: [{
                    label: 'Insurance Claims',
                    data: [stats.insurance_approved || 0, stats.insurance_pending || 0, stats.insurance_rejected || 0],
                    backgroundColor: ['#23d160', '#ffdd57', '#ff3860'],
                    borderColor: ['#1fc653', '#f5c842', '#f14668'],
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

    async loadBillingHistory() {
        try {
            this.showLoading();
            
            const search = document.getElementById('searchInput').value;
            const paymentStatus = document.getElementById('paymentStatusFilter').value;
            const fromDate = document.getElementById('fromDate').value;
            const toDate = document.getElementById('toDate').value;
            
            const params = new URLSearchParams({
                action: 'billing_history',
                page: this.currentPage,
                limit: this.itemsPerPage,
                search: search,
                payment_status: paymentStatus,
                from_date: fromDate,
                to_date: toDate
            });

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderBillingHistory(data.data.billing);
                this.renderPagination(data.data.pagination);
            } else {
                this.showError('Failed to load billing history: ' + data.message);
            }
        } catch (error) {
            console.error('Error loading billing history:', error);
            this.showError('Error loading billing history: ' + error.message);
        } finally {
            this.hideLoading();
        }
    }

    renderBillingHistory(billingRecords) {
        const tbody = document.getElementById('billingHistoryTable');
        
        if (billingRecords.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="has-text-centered py-4">
                        <span class="icon is-large has-text-grey-light">
                            <i class="fas fa-file-invoice-dollar fa-2x"></i>
                        </span>
                        <p class="title is-6 has-text-grey">No billing records found</p>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = billingRecords.map(bill => {
            const billingDate = new Date(bill.billing_date).toLocaleDateString();
            
            return `
                <tr>
                    <td><strong>#${bill.billing_id}</strong></td>
                    <td>${bill.patient_name}</td>
                    <td class="has-text-weight-bold">PHP ${this.formatCurrency(bill.amount)}</td>
                    <td><span class="tag status-badge ${bill.payment_status}">${bill.payment_status.charAt(0).toUpperCase() + bill.payment_status.slice(1)}</span></td>
                    <td><span class="tag ${this.getInsuranceStatusClass(bill.insurance_claim_status)}">${bill.insurance_claim_status.charAt(0).toUpperCase() + bill.insurance_claim_status.slice(1)}</span></td>
                    <td>${billingDate}</td>
                </tr>
            `;
        }).join('');
    }

    loadFinancialSummary() {
        const container = document.getElementById('financialSummaryContent');
        const stats = this.reportData.statistics;
        
        const collectionRate = stats.total_revenue > 0 ? ((stats.paid_amount / stats.total_revenue) * 100).toFixed(1) : '0.0';
        const averageBillAmount = stats.total_bills > 0 ? (stats.total_revenue / stats.total_bills).toFixed(2) : '0.00';
        
        container.innerHTML = `
            <div class="columns">
                <div class="column is-6">
                    <div class="billing-card">
                        <div class="card-header">
                            <p class="card-header-title">
                                <span class="icon"><i class="fas fa-chart-line"></i></span>
                                Revenue Metrics
                            </p>
                        </div>
                        <div class="card-content">
                            <div class="content">
                                <div class="columns is-mobile">
                                    <div class="column">
                                        <p class="heading">Total Revenue</p>
                                        <p class="title is-4 has-text-success">PHP ${this.formatCurrency(stats.total_revenue || 0)}</p>
                                    </div>
                                    <div class="column">
                                        <p class="heading">Collection Rate</p>
                                        <p class="title is-4 has-text-info">${collectionRate}%</p>
                                    </div>
                                </div>
                                <div class="columns is-mobile">
                                    <div class="column">
                                        <p class="heading">Average Bill</p>
                                        <p class="title is-4">PHP ${averageBillAmount}</p>
                                    </div>
                                    <div class="column">
                                        <p class="heading">Outstanding</p>
                                        <p class="title is-4 has-text-danger">${stats.outstanding_bills || 0}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column is-6">
                    <div class="billing-card">
                        <div class="card-header">
                            <p class="card-header-title">
                                <span class="icon"><i class="fas fa-credit-card"></i></span>
                                Payment Analysis
                            </p>
                        </div>
                        <div class="card-content">
                            <div class="content">
                                <div class="columns is-mobile">
                                    <div class="column">
                                        <p class="heading">Paid Bills</p>
                                        <p class="title is-4 has-text-success">${stats.paid_count || 0}</p>
                                    </div>
                                    <div class="column">
                                        <p class="heading">Pending Bills</p>
                                        <p class="title is-4 has-text-warning">${stats.pending_count || 0}</p>
                                    </div>
                                </div>
                                <div class="columns is-mobile">
                                    <div class="column">
                                        <p class="heading">Insurance Approved</p>
                                        <p class="title is-4 has-text-info">${stats.insurance_approved || 0}</p>
                                    </div>
                                    <div class="column">
                                        <p class="heading">Insurance Pending</p>
                                        <p class="title is-4 has-text-warning">${stats.insurance_pending || 0}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
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
                   onclick="billingReports.goToPage(${i})">${i}</a>
            `;
            list.appendChild(li);
        }
    }

    goToPage(page) {
        if (page !== this.currentPage) {
            this.currentPage = page;
            this.loadBillingHistory();
        }
    }

    exportToExcel() {
        const fromDate = document.getElementById('fromDate').value;
        const toDate = document.getElementById('toDate').value;
        const paymentStatus = document.getElementById('paymentStatusFilter').value;
        
        const params = new URLSearchParams({
            action: 'export_billing',
            format: 'excel',
            from_date: fromDate,
            to_date: toDate,
            payment_status: paymentStatus
        });

        window.open(`controllers/ReportsController.php?${params}`, '_blank');
    }

    async printReport() {
        const printArea = document.getElementById('billingPrintArea');
        const fromDate = document.getElementById('fromDate').value;
        const toDate = document.getElementById('toDate').value;
        const stats = this.reportData.statistics;
        
        // Get all billing records for printing
        try {
            const search = document.getElementById('searchInput').value;
            const paymentStatus = document.getElementById('paymentStatusFilter').value;
            
            const params = new URLSearchParams({
                action: 'billing_history',
                page: 1,
                limit: 1000, // Get all records for printing
                search: search,
                payment_status: paymentStatus,
                from_date: fromDate,
                to_date: toDate
            });

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            const data = await response.json();
            
            let billingTableHTML = '';
            
            if (data.success && data.data.billing.length > 0) {
                billingTableHTML = `
                    <div class="print-billing">
                        <h3>Billing Details</h3>
                        <table class="billing-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Patient</th>
                                    <th>Amount</th>
                                    <th>Payment Status</th>
                                    <th>Insurance Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>`;
                
                data.data.billing.forEach(bill => {
                    const billingDate = new Date(bill.billing_date).toLocaleDateString();
                    
                    billingTableHTML += `
                        <tr>
                            <td>#${bill.billing_id}</td>
                            <td>${bill.patient_name}</td>
                            <td class="amount-cell">PHP ${this.formatCurrency(bill.amount)}</td>
                            <td class="status-cell">${bill.payment_status.charAt(0).toUpperCase() + bill.payment_status.slice(1)}</td>
                            <td class="status-cell">${bill.insurance_claim_status.charAt(0).toUpperCase() + bill.insurance_claim_status.slice(1)}</td>
                            <td>${billingDate}</td>
                        </tr>`;
                });
                
                billingTableHTML += `
                            </tbody>
                        </table>
                    </div>`;
            } else {
                billingTableHTML = `
                    <div class="print-billing">
                        <h3>Billing Details</h3>
                        <p>No billing records found for the selected criteria.</p>
                    </div>`;
            }
            
            const filterInfo = [];
            if (search) filterInfo.push(`Search: "${search}"`);
            if (paymentStatus) filterInfo.push(`Status: ${paymentStatus.charAt(0).toUpperCase() + paymentStatus.slice(1)}`);
            const filterText = filterInfo.length > 0 ? filterInfo.join(', ') : 'All billing records';
            
            const printContent = `
                <div class="print-header">
                    <div class="print-title">Hospital Management System</div>
                    <div class="print-subtitle">Billing Reports</div>
                    <div class="print-subtitle">Generated on: ${new Date().toLocaleDateString()}</div>
                </div>
                
                <div class="print-info">
                    <h3>Report Information</h3>
                    <div class="print-info-grid">
                        <div><strong>Date Range:</strong> ${fromDate} to ${toDate}</div>
                        <div><strong>Generated By:</strong> System Administrator</div>
                        <div><strong>Filters Applied:</strong> ${filterText}</div>
                        <div><strong>Report Date:</strong> ${new Date().toLocaleDateString()}</div>
                        <div><strong>Total Revenue:</strong> PHP ${this.formatCurrency(stats.total_revenue || 0)}</div>
                        <div><strong>Records Shown:</strong> ${data.success ? data.data.billing.length : 0}</div>
                    </div>
                </div>
                
                <div class="print-billing">
                    <h3>Financial Summary</h3>
                    <table class="billing-table">
                        <thead>
                            <tr>
                                <th>Metric</th>
                                <th>Amount</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Total Revenue</td>
                                <td class="amount-cell">PHP ${this.formatCurrency(stats.total_revenue || 0)}</td>
                                <td>100.0%</td>
                            </tr>
                            <tr>
                                <td>Paid Amount</td>
                                <td class="amount-cell">PHP ${this.formatCurrency(stats.paid_amount || 0)}</td>
                                <td>${stats.total_revenue > 0 ? ((stats.paid_amount / stats.total_revenue) * 100).toFixed(1) : 0}%</td>
                            </tr>
                            <tr>
                                <td>Pending Amount</td>
                                <td class="amount-cell">PHP ${this.formatCurrency(stats.pending_amount || 0)}</td>
                                <td>${stats.total_revenue > 0 ? ((stats.pending_amount / stats.total_revenue) * 100).toFixed(1) : 0}%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                ${billingTableHTML}
                
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
            console.error('Error loading billing data for print:', error);
            this.showError('Error loading billing data for printing: ' + error.message);
        }
    }

    // Helper methods
    formatCurrency(amount) {
        return parseFloat(amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    getInsuranceStatusClass(status) {
        switch(status) {
            case 'approved': return 'is-success';
            case 'pending': return 'is-warning';
            case 'rejected': return 'is-danger';
            default: return 'is-light';
        }
    }

    showLoading() {
        document.getElementById('loadingOverlay').style.display = 'flex';
    }

    hideLoading() {
        document.getElementById('loadingOverlay').style.display = 'none';
    }

    showError(message) {
        const notification = document.createElement('div');
        notification.className = 'notification is-danger';
        notification.innerHTML = `
            <button class="delete" onclick="this.parentElement.remove()"></button>
            <strong>Error:</strong> ${message}
        `;
        
        const container = document.querySelector('.page-transition');
        container.insertBefore(notification, container.firstChild);
        
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
        
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

// Initialize the billing reports system
document.addEventListener('DOMContentLoaded', function() {
    window.billingReports = new BillingReports();
});
</script>

</div> <!-- End of container from header.php -->
</body>
</html> 