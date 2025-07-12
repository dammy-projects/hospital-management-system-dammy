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
    border-left: 4px solid #ff6b35;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #ff6b35;
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

.stock-status {
    font-weight: bold;
}

.stock-low {
    color: #ff6b35;
}

.stock-out {
    color: #d63031;
}

.stock-normal {
    color: #00b894;
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

.inventory-info-card {
    border: 1px solid #e1e5e9;
    border-radius: 8px;
}

.inventory-info-card .card-header {
    background: linear-gradient(135deg, #ff6b35 0%, #f39c12 100%);
    color: white;
}

.inventory-info-card .card-header-title {
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
    
    #inventoryPrintArea,
    #inventoryPrintArea * {
        visibility: visible;
    }
    
    #inventoryPrintArea {
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
    
    .print-inventory {
        margin-bottom: 12px;
    }
    
    .print-inventory h3 {
        margin: 0 0 8px 0 !important;
        font-size: 12px;
        background-color: #f0f0f0;
        padding: 4px 8px;
        border: 1px solid #000;
    }
    
    .inventory-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #000;
        font-size: 9px;
    }
    
    .inventory-table th,
    .inventory-table td {
        border: 1px solid #ccc;
        padding: 3px 4px;
        text-align: left;
        vertical-align: top;
    }
    
    .inventory-table th {
        background-color: #f5f5f5;
        font-weight: bold;
        font-size: 9px;
    }
    
    .item-name {
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
                    <li class="is-active"><a href="#"><span class="icon"><i class="fas fa-boxes"></i></span><span>Inventory Reports</span></a></li>
                </ul>
            </nav>
            <h1 class="title is-3">
                <span class="icon">
                    <i class="fas fa-boxes"></i>
                </span>
                Inventory Reports & Analytics
            </h1>
            <p class="subtitle">Comprehensive inventory data analysis and reporting</p>
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
                        <input class="input" type="text" id="searchInput" placeholder="Search items..." />
                    </div>
                </div>
            </div>
            <div class="column is-2">
                <div class="field">
                    <label class="label">Category</label>
                    <div class="control">
                        <div class="select is-fullwidth">
                            <select id="categoryFilter">
                                <option value="">All Categories</option>
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
                                <option value="discontinued">Discontinued</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="column is-2">
                <div class="field">
                    <label class="label">Stock Level</label>
                    <div class="control">
                        <div class="select is-fullwidth">
                            <select id="stockFilter">
                                <option value="">All Levels</option>
                                <option value="low">Low Stock</option>
                                <option value="out">Out of Stock</option>
                                <option value="normal">Normal Stock</option>
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
    <div class="stats-grid" id="inventoryStats">
        <!-- Stats will be loaded here -->
    </div>

    <!-- Report Tabs -->
    <div class="report-tabs">
        <div class="tabs is-boxed">
            <ul>
                <li class="is-active" data-tab="overview">
                    <a>
                        <span class="icon"><i class="fas fa-chart-pie"></i></span>
                        <span>Overview</span>
                    </a>
                </li>
                <li data-tab="stock-levels">
                    <a>
                        <span class="icon"><i class="fas fa-layer-group"></i></span>
                        <span>Stock Levels</span>
                    </a>
                </li>
                <li data-tab="categories">
                    <a>
                        <span class="icon"><i class="fas fa-tags"></i></span>
                        <span>Categories</span>
                    </a>
                </li>
                <li data-tab="movements">
                    <a>
                        <span class="icon"><i class="fas fa-exchange-alt"></i></span>
                        <span>Movements</span>
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
                        <h4 class="title is-5">Inventory Overview</h4>
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
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>Stock</th>
                                <th>Unit</th>
                                <th>Reorder Level</th>
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
                            Showing 0 of 0 items
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

        <!-- Stock Levels Tab -->
        <div id="stock-levels-tab" class="tab-content" style="display: none;">
            <div class="chart-box">
                <h4 class="title is-5 mb-4">Stock Level Analysis</h4>
                <div class="chart-container">
                    <canvas id="stockLevelsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Categories Tab -->
        <div id="categories-tab" class="tab-content" style="display: none;">
            <div class="columns">
                <div class="column">
                    <div class="chart-box">
                        <h4 class="title is-5 mb-4">Items by Category</h4>
                        <div class="chart-container">
                            <canvas id="categoriesChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="column">
                    <div class="chart-box">
                        <h4 class="title is-5 mb-4">Stock Value by Category</h4>
                        <div class="chart-container">
                            <canvas id="categoryValueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Movements Tab -->
        <div id="movements-tab" class="tab-content" style="display: none;">
            <div class="chart-box">
                <h4 class="title is-5 mb-4">Inventory Movements</h4>
                <div class="chart-container">
                    <canvas id="movementsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Summary Tab -->
        <div id="summary-tab" class="tab-content" style="display: none;">
            <div class="chart-box">
                <h4 class="title is-5 mb-4">Inventory Summary Statistics</h4>
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

<!-- Inventory Details Modal -->
<div class="modal" id="inventoryModal">
    <div class="modal-background" onclick="closeInventoryModal()"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">
                <span class="icon">
                    <i class="fas fa-box"></i>
                </span>
                Inventory Item Details
            </p>
            <button class="delete" aria-label="close" onclick="closeInventoryModal()"></button>
        </header>
        <section class="modal-card-body">
            <div id="inventoryModalContent">
                <!-- Inventory details will be loaded here -->
                <div class="has-text-centered">
                    <span class="icon is-large">
                        <i class="fas fa-spinner fa-pulse fa-2x"></i>
                    </span>
                    <p class="mt-2">Loading inventory details...</p>
                </div>
            </div>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-light" onclick="closeInventoryModal()">Close</button>
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
<div id="inventoryPrintArea" style="display: none;">
    <!-- Print content will be generated here -->
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- JavaScript -->
<script>
class InventoryReportsManager {
    constructor() {
        this.currentTab = 'overview';
        this.charts = {};
        this.filters = {
            category: '',
            status: '',
            stock: '',
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
        this.bindEvents();
        this.loadCategories();
        this.loadInventoryStats();
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

        document.getElementById('categoryFilter').addEventListener('change', (e) => {
            this.filters.category = e.target.value;
        });

        document.getElementById('statusFilter').addEventListener('change', (e) => {
            this.filters.status = e.target.value;
        });

        document.getElementById('stockFilter').addEventListener('change', (e) => {
            this.filters.stock = e.target.value;
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

    async loadCategories() {
        try {
            const response = await fetch('controllers/ReportsController.php?action=inventory_categories');
            const data = await response.json();

            if (data.success) {
                const select = document.getElementById('categoryFilter');
                select.innerHTML = '<option value="">All Categories</option>';
                
                data.data.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.category_id;
                    option.textContent = category.category_name;
                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading categories:', error);
        }
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

    async loadInventoryStats() {
        try {
            const params = new URLSearchParams({
                action: 'inventory',
                report_type: 'stats'
            });

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderInventoryStats(data.data);
            }
        } catch (error) {
            console.error('Error loading inventory stats:', error);
        }
    }

    renderInventoryStats(stats) {
        const container = document.getElementById('inventoryStats');
        
        container.innerHTML = `
            <div class="stat-card">
                <span class="stat-number">${stats.total_items || 0}</span>
                <div class="stat-label">Total Items</div>
            </div>
            <div class="stat-card">
                <span class="stat-number">${stats.active_items || 0}</span>
                <div class="stat-label">Active Items</div>
            </div>
            <div class="stat-card">
                <span class="stat-number">${stats.low_stock || 0}</span>
                <div class="stat-label">Low Stock Items</div>
            </div>
            <div class="stat-card">
                <span class="stat-number">${stats.out_of_stock || 0}</span>
                <div class="stat-label">Out of Stock</div>
            </div>
            <div class="stat-card">
                <span class="stat-number">${stats.inactive_items || 0}</span>
                <div class="stat-label">Inactive Items</div>
            </div>
            <div class="stat-card">
                <span class="stat-number">${stats.discontinued_items || 0}</span>
                <div class="stat-label">Discontinued</div>
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
                case 'stock-levels':
                    await this.loadStockLevels();
                    break;
                case 'categories':
                    await this.loadCategoriesCharts();
                    break;
                case 'movements':
                    await this.loadMovements();
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
                action: 'inventory',
                report_type: 'overview',
                page: this.overviewPage,
                limit: this.overviewLimit
            });

            if (this.filters.category) {
                params.append('category', this.filters.category);
            }

            if (this.filters.status) {
                params.append('status', this.filters.status);
            }

            if (this.filters.stock) {
                params.append('stock', this.filters.stock);
            }

            if (this.filters.search) {
                params.append('search', this.filters.search);
            }

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderOverviewTable(data.data.items, data.data.page, data.data.total_pages, data.data.total);
                this.totalPages = data.data.total_pages;
                this.totalRecords = data.data.total;
                this.updatePaginationControls();
            } else {
                this.showNotification('Error loading overview: ' + data.message, 'is-danger');
            }
        } catch (error) {
            console.error('Error loading overview:', error);
            this.showNotification('Failed to load inventory overview', 'is-danger');
        }
    }

    renderOverviewTable(items, currentPage, totalPages, totalCount) {
        // Store current items for printing
        this.currentItems = items;
        
        const tbody = document.getElementById('overviewTableBody');
        tbody.innerHTML = '';
        
        if (items.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="has-text-centered">
                        <div class="content">
                            <p><strong>No inventory items found</strong></p>
                            <p class="help">Try adjusting your filters or search terms.</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }
        
        items.forEach(item => {
            const stockStatus = this.getStockStatus(item.quantity_in_stock, item.reorder_level);
            const statusColor = item.status === 'active' ? 'is-success' : 
                              item.status === 'inactive' ? 'is-warning' : 'is-danger';
            
            const row = `
                <tr>
                    <td>${item.item_id}</td>
                    <td><strong>${item.item_name}</strong></td>
                    <td>${item.category_name || 'N/A'}</td>
                    <td><span class="${stockStatus.class}">${item.quantity_in_stock}</span></td>
                    <td>${item.unit || 'N/A'}</td>
                    <td>${item.reorder_level || 'N/A'}</td>
                    <td><span class="tag ${statusColor}">${item.status}</span></td>
                    <td>
                        <div class="buttons are-small">
                            <button class="button is-link is-small" onclick="viewInventoryItem(${item.item_id})">
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

    getStockStatus(currentStock, reorderLevel) {
        if (currentStock === 0) {
            return { class: 'stock-status stock-out', text: 'Out of Stock' };
        } else if (currentStock <= reorderLevel) {
            return { class: 'stock-status stock-low', text: 'Low Stock' };
        } else {
            return { class: 'stock-status stock-normal', text: 'Normal' };
        }
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

    updatePaginationControls() {
        const prevBtn = document.getElementById('prevPage');
        const nextBtn = document.getElementById('nextPage');
        const paginationInfo = document.getElementById('paginationInfo');
        const paginationList = document.getElementById('paginationList');

        // Update pagination info
        const start = ((this.overviewPage - 1) * this.overviewLimit) + 1;
        const end = Math.min(this.overviewPage * this.overviewLimit, this.totalRecords);
        paginationInfo.textContent = `Showing ${start}-${end} of ${this.totalRecords} items`;

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

    exportReport(format) {
        const params = new URLSearchParams({
            action: 'export_inventory',
            format: format,
            report_type: this.currentTab
        });

        if (this.filters.category) {
            params.append('category', this.filters.category);
        }

        if (this.filters.status) {
            params.append('status', this.filters.status);
        }

        if (this.filters.stock) {
            params.append('stock', this.filters.stock);
        }

        if (this.filters.search) {
            params.append('search', this.filters.search);
        }

        const link = document.createElement('a');
        link.href = `controllers/ReportsController.php?${params}`;
        link.download = `inventory_report_${this.currentTab}_${new Date().toISOString().split('T')[0]}.${format}`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        this.showNotification(`${format.toUpperCase()} export initiated`, 'is-success');
    }

    printReport() {
        // Get current inventory data from the table
        const currentData = this.currentItems || [];
        
        if (currentData.length === 0) {
            alert('No inventory data to print. Please load data first.');
            return;
        }
        
        // Generate print content
        this.generatePrintContent(currentData);
        
        // Trigger print
        window.print();
    }
    
    generatePrintContent(items) {
        const printArea = document.getElementById('inventoryPrintArea');
        const currentDate = new Date().toLocaleDateString();
        const currentDateTime = new Date().toLocaleString();
        
        let itemsHtml = '';
        if (items && items.length > 0) {
            itemsHtml = `
                <table class="inventory-table">
                    <thead>
                        <tr>
                            <th style="width: 8%;">Item ID</th>
                            <th style="width: 25%;">Item Name</th>
                            <th style="width: 15%;">Category</th>
                            <th style="width: 10%;">Stock</th>
                            <th style="width: 8%;">Unit</th>
                            <th style="width: 10%;">Reorder</th>
                            <th style="width: 10%;">Status</th>
                            <th style="width: 14%;">Last Updated</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            items.forEach((item, index) => {
                const lastUpdated = item.last_updated ? new Date(item.last_updated).toLocaleDateString() : 'N/A';
                itemsHtml += `
                    <tr>
                        <td>#${item.item_id}</td>
                        <td class="item-name">${this.escapeHtml(item.item_name)}</td>
                        <td>${this.escapeHtml(item.category_name || 'N/A')}</td>
                        <td>${item.quantity_in_stock}</td>
                        <td>${this.escapeHtml(item.unit || 'N/A')}</td>
                        <td>${item.reorder_level || 'N/A'}</td>
                        <td>${item.status}</td>
                        <td>${lastUpdated}</td>
                    </tr>
                `;
            });
            
            itemsHtml += `
                    </tbody>
                </table>
            `;
        } else {
            itemsHtml = '<table class="inventory-table"><tr><td colspan="8" style="text-align: center; font-style: italic;">No inventory data available.</td></tr></table>';
        }
        
        printArea.innerHTML = `
            <div class="print-header">
                <div class="print-title">Hospital Management System</div>
                <div class="print-subtitle">Inventory Report</div>
            </div>
            
            <div class="print-info">
                <h3>Report Information</h3>
                <div class="print-info-grid">
                    <div><strong>Report Date:</strong> ${currentDate}</div>
                    <div><strong>Total Items:</strong> ${items.length}</div>
                    <div><strong>Category Filter:</strong> ${this.filters.category || 'All Categories'}</div>
                    <div><strong>Status Filter:</strong> ${this.filters.status || 'All Status'}</div>
                </div>
            </div>
            
            <div class="print-inventory">
                <h3>Inventory Items (${items.length} records)</h3>
                ${itemsHtml}
            </div>
            
            <div class="print-footer">
                <div>Report ID: RPT-INV-${Date.now()}</div>
                <div>Generated on: ${currentDateTime} | Hospital Management System</div>
                <div style="margin-top: 4px; font-weight: bold;">📦 Inventory Report - Confidential Data</div>
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

    async loadStockLevels() {
        try {
            const params = new URLSearchParams({
                action: 'inventory',
                report_type: 'stock_levels'
            });

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderStockLevelsChart(data.data);
            }
        } catch (error) {
            console.error('Error loading stock levels:', error);
        }
    }

    renderStockLevelsChart(stockData) {
        const ctx = document.getElementById('stockLevelsChart').getContext('2d');
        
        if (this.charts.stockLevels) {
            this.charts.stockLevels.destroy();
        }

        this.charts.stockLevels = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Normal Stock', 'Low Stock', 'Out of Stock'],
                datasets: [{
                    label: 'Item Count',
                    data: [
                        stockData.normal_stock || 0,
                        stockData.low_stock || 0,
                        stockData.out_of_stock || 0
                    ],
                    backgroundColor: [
                        '#00b894',
                        '#ff6b35',
                        '#d63031'
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

    async loadCategoriesCharts() {
        try {
            const params = new URLSearchParams({
                action: 'inventory',
                report_type: 'categories'
            });

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderCategoriesCharts(data.data);
            }
        } catch (error) {
            console.error('Error loading categories data:', error);
        }
    }

    renderCategoriesCharts(categoryData) {
        // Categories Chart
        const categoriesCtx = document.getElementById('categoriesChart').getContext('2d');
        
        if (this.charts.categories) {
            this.charts.categories.destroy();
        }

        const categoryLabels = categoryData.map(c => c.category_name);
        const categoryValues = categoryData.map(c => c.item_count);

        this.charts.categories = new Chart(categoriesCtx, {
            type: 'doughnut',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categoryValues,
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

        // Category Value Chart (placeholder - would need pricing data)
        const categoryValueCtx = document.getElementById('categoryValueChart').getContext('2d');
        
        if (this.charts.categoryValue) {
            this.charts.categoryValue.destroy();
        }

        this.charts.categoryValue = new Chart(categoryValueCtx, {
            type: 'pie',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categoryValues, // This would be value data in a real implementation
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

    async loadMovements() {
        try {
            const params = new URLSearchParams({
                action: 'inventory',
                report_type: 'movements'
            });

            const response = await fetch(`controllers/ReportsController.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderMovementsChart(data.data);
            }
        } catch (error) {
            console.error('Error loading movements:', error);
        }
    }

    renderMovementsChart(movementsData) {
        const ctx = document.getElementById('movementsChart').getContext('2d');
        
        if (this.charts.movements) {
            this.charts.movements.destroy();
        }

        const labels = movementsData.map(m => new Date(m.date).toLocaleDateString());
        const inData = movementsData.map(m => m.in_movements || 0);
        const outData = movementsData.map(m => m.out_movements || 0);

        this.charts.movements = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Stock In',
                    data: inData,
                    borderColor: '#00b894',
                    backgroundColor: 'rgba(0, 184, 148, 0.1)',
                    fill: false,
                    tension: 0.4
                }, {
                    label: 'Stock Out',
                    data: outData,
                    borderColor: '#d63031',
                    backgroundColor: 'rgba(214, 48, 49, 0.1)',
                    fill: false,
                    tension: 0.4
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

    async loadSummary() {
        try {
            const params = new URLSearchParams({
                action: 'inventory',
                report_type: 'summary'
            });

            const response = await fetch(`controllers/ReportsController.php?${params}`);
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
                <p><strong>Total Items:</strong> ${stats.total_items}</p>
                <p><strong>Active Items:</strong> ${stats.active_items} (${((stats.active_items/stats.total_items)*100).toFixed(1)}%)</p>
                <p><strong>Low Stock Items:</strong> ${stats.low_stock}</p>
                <p><strong>Out of Stock Items:</strong> ${stats.out_of_stock}</p>
                <p><strong>Inactive Items:</strong> ${stats.inactive_items}</p>
                <p><strong>Discontinued Items:</strong> ${stats.discontinued_items}</p>
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
                labels: ['Active', 'Inactive', 'Discontinued', 'Low Stock', 'Out of Stock'],
                datasets: [{
                    label: 'Count',
                    data: [stats.active_items, stats.inactive_items, stats.discontinued_items, stats.low_stock, stats.out_of_stock],
                    backgroundColor: [
                        '#00b894',
                        '#fdcb6e',
                        '#e17055',
                        '#ff6b35',
                        '#d63031'
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
        this.overviewPage = 1; // Reset pagination
        this.loadInventoryStats();
        this.loadTabContent(this.currentTab);
        
        this.showNotification('Filters applied successfully', 'is-success');
    }
}

// Global functions
function viewInventoryItem(itemId) {
    openInventoryModal(itemId);
}

async function openInventoryModal(itemId) {
    const modal = document.getElementById('inventoryModal');
    const modalContent = document.getElementById('inventoryModalContent');
    
    // Show modal
    modal.classList.add('is-active');
    
    // Show loading state
    modalContent.innerHTML = `
        <div class="has-text-centered">
            <span class="icon is-large">
                <i class="fas fa-spinner fa-pulse fa-2x"></i>
            </span>
            <p class="mt-2">Loading inventory item details...</p>
        </div>
    `;
    
    try {
        // Load inventory item details
        const response = await fetch(`controllers/ReportsController.php?action=inventory_details&item_id=${itemId}`);
        const data = await response.json();
        
        if (data.success && data.data) {
            renderInventoryDetails(data.data);
        } else {
            showInventoryError(data.message || 'Failed to load inventory item details.');
        }
    } catch (error) {
        console.error('Error loading inventory item details:', error);
        showInventoryError('An error occurred while loading inventory item details.');
    }
}

function renderInventoryDetails(item) {
    const modalContent = document.getElementById('inventoryModalContent');
    
    const lastUpdated = item.last_updated ? new Date(item.last_updated).toLocaleDateString() : 'N/A';
    const statusColor = item.status === 'active' ? 'is-success' : 
                       item.status === 'inactive' ? 'is-warning' : 'is-danger';
    
    modalContent.innerHTML = `
        <div class="columns">
            <div class="column">
                <div class="card inventory-info-card">
                    <div class="card-header">
                        <p class="card-header-title">
                            <span class="icon">
                                <i class="fas fa-box"></i>
                            </span>
                            Item Information
                        </p>
                    </div>
                    <div class="card-content">
                        <div class="content">
                            <div class="field">
                                <label class="label">Item Name</label>
                                <p class="is-size-5"><strong>${item.item_name}</strong></p>
                            </div>
                            <div class="field">
                                <label class="label">Description</label>
                                <p>${item.item_description || 'N/A'}</p>
                            </div>
                            <div class="columns">
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Category</label>
                                        <p><strong>${item.category_name || 'N/A'}</strong></p>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Status</label>
                                        <p><span class="tag ${statusColor}">${item.status}</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="column">
                <div class="card inventory-info-card">
                    <div class="card-header">
                        <p class="card-header-title">
                            <span class="icon">
                                <i class="fas fa-layer-group"></i>
                            </span>
                            Stock Information
                        </p>
                    </div>
                    <div class="card-content">
                        <div class="content">
                            <div class="field">
                                <label class="label">Current Stock</label>
                                <p class="is-size-4">
                                    <strong class="${item.quantity_in_stock === 0 ? 'has-text-danger' : item.quantity_in_stock <= item.reorder_level ? 'has-text-warning' : 'has-text-success'}">
                                        ${item.quantity_in_stock} ${item.unit || ''}
                                    </strong>
                                </p>
                            </div>
                            <div class="columns">
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Unit</label>
                                        <p>${item.unit || 'N/A'}</p>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Reorder Level</label>
                                        <p><strong>${item.reorder_level || 'N/A'}</strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card inventory-info-card mt-4">
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
                                <label class="label">Item ID</label>
                                <p><code>#${item.item_id}</code></p>
                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                <label class="label">Serial Number</label>
                                <p>${item.serial_number || 'N/A'}</p>
                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                <label class="label">Product Number</label>
                                <p>${item.product_number || 'N/A'}</p>
                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                <label class="label">Last Updated</label>
                                <p>${lastUpdated}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function showInventoryError(message) {
    const modalContent = document.getElementById('inventoryModalContent');
    modalContent.innerHTML = `
        <div class="notification is-danger">
            <span class="icon">
                <i class="fas fa-exclamation-triangle"></i>
            </span>
            <span>${message}</span>
        </div>
    `;
}

function closeInventoryModal() {
    const modal = document.getElementById('inventoryModal');
    modal.classList.remove('is-active');
}

// Close modal when pressing Escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeInventoryModal();
    }
});

// Initialize the reports manager when the page loads
document.addEventListener('DOMContentLoaded', () => {
    window.inventoryReportsManager = new InventoryReportsManager();
});
</script>

    </div> <!-- End of container from header.php -->
</body>
</html> 