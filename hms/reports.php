<?php
require_once 'includes/header.php';
?>

<style>
/* Report card styling */
.report-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border-radius: 8px;
    height: 100%;
    cursor: pointer;
}

.report-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.report-card .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.report-card.patients .card-header {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.report-card.doctors .card-header {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.report-card.appointments .card-header {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}

.report-card.billing .card-header {
    background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
}

.report-card.inventory .card-header {
    background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
}

.report-card.prescriptions .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stat-box {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
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

.chart-container {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
}

/* Date range picker styling */
.date-range-container {
    background: white;
    border-radius: 8px;
    padding: 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
}
</style>

<div class="page-transition mt-4">
    <!-- Page Header -->
    <div class="columns is-vcentered mb-4">
        <div class="column">
            <h1 class="title is-3">
                <span class="icon">
                    <i class="fas fa-chart-bar"></i>
                </span>
                Reports & Analytics
            </h1>
            <p class="subtitle">Comprehensive reporting for all hospital modules</p>
        </div>
        <div class="column is-narrow">
            <div class="buttons">
                <button class="button is-info" id="refreshDashboard">
                    <span class="icon">
                        <i class="fas fa-sync-alt"></i>
                    </span>
                    <span>Refresh</span>
                </button>
                <button class="button is-success" id="exportSummary">
                    <span class="icon">
                        <i class="fas fa-download"></i>
                    </span>
                    <span>Export Summary</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="date-range-container">
        <div class="columns is-vcentered">
            <div class="column">
                <h4 class="title is-6 mb-2">
                    <span class="icon">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                    Date Range Filter
                </h4>
            </div>
            <div class="column is-3">
                <div class="field">
                    <label class="label">From Date</label>
                    <div class="control">
                        <input class="input" type="date" id="fromDate" />
                    </div>
                </div>
            </div>
            <div class="column is-3">
                <div class="field">
                    <label class="label">To Date</label>
                    <div class="control">
                        <input class="input" type="date" id="toDate" />
                    </div>
                </div>
            </div>
            <div class="column is-2">
                <div class="field">
                    <label class="label">&nbsp;</label>
                    <div class="control">
                        <button class="button is-primary is-fullwidth" id="applyDateFilter">
                            <span class="icon">
                                <i class="fas fa-filter"></i>
                            </span>
                            <span>Apply</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overview Statistics -->
    <div class="chart-container">
        <h4 class="title is-5 mb-4">
            <span class="icon">
                <i class="fas fa-tachometer-alt"></i>
            </span>
            Quick Overview
        </h4>
        <div class="columns" id="overviewStats">
            <!-- Statistics will be loaded here -->
        </div>
    </div>

    <!-- Module Reports Grid -->
    <div class="chart-container">
        <h4 class="title is-5 mb-4">
            <span class="icon">
                <i class="fas fa-file-alt"></i>
            </span>
            Module Reports
        </h4>
        <div class="columns is-multiline">
            <!-- Patient Reports -->
            <div class="column is-4">
                <div class="card report-card patients" onclick="openModuleReport('patients')">
                    <header class="card-header">
                        <p class="card-header-title">
                            <span class="icon">
                                <i class="fas fa-users"></i>
                            </span>
                            <span>Patient Reports</span>
                        </p>
                    </header>
                    <div class="card-content">
                        <div class="content">
                            <p><strong>Available Reports:</strong></p>
                            <ul>
                                <li>Patient Demographics</li>
                                <li>Registration Statistics</li>
                                <li>Patient Status Summary</li>
                                <li>Age & Gender Distribution</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Doctor Reports -->
            <div class="column is-4">
                <div class="card report-card doctors" onclick="openModuleReport('doctors')">
                    <header class="card-header">
                        <p class="card-header-title">
                            <span class="icon">
                                <i class="fas fa-user-md"></i>
                            </span>
                            <span>Doctor Reports</span>
                        </p>
                    </header>
                    <div class="card-content">
                        <div class="content">
                            <p><strong>Available Reports:</strong></p>
                            <ul>
                                <li>Doctor Directory</li>
                                <li>Specialty Distribution</li>
                                <li>Department Assignment</li>
                                <li>Doctor Workload</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Appointment Reports -->
            <div class="column is-4">
                <div class="card report-card appointments" onclick="openModuleReport('appointments')">
                    <header class="card-header">
                        <p class="card-header-title">
                            <span class="icon">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <span>Appointment Reports</span>
                        </p>
                    </header>
                    <div class="card-content">
                        <div class="content">
                            <p><strong>Available Reports:</strong></p>
                            <ul>
                                <li>Daily Appointments</li>
                                <li>Status Summary</li>
                                <li>Doctor Schedule Analysis</li>
                                <li>Appointment Trends</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Billing Reports -->
            <div class="column is-4">
                <div class="card report-card billing" onclick="openModuleReport('billing')">
                    <header class="card-header">
                        <p class="card-header-title">
                            <span class="icon">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </span>
                            <span>Billing Reports</span>
                        </p>
                    </header>
                    <div class="card-content">
                        <div class="content">
                            <p><strong>Available Reports:</strong></p>
                            <ul>
                                <li>Revenue Summary</li>
                                <li>Payment Status</li>
                                <li>Insurance Claims</li>
                                <li>Outstanding Bills</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory Reports -->
            <div class="column is-4">
                <div class="card report-card inventory" onclick="openModuleReport('inventory')">
                    <header class="card-header">
                        <p class="card-header-title">
                            <span class="icon">
                                <i class="fas fa-boxes"></i>
                            </span>
                            <span>Inventory Reports</span>
                        </p>
                    </header>
                    <div class="card-content">
                        <div class="content">
                            <p><strong>Available Reports:</strong></p>
                            <ul>
                                <li>Stock Levels</li>
                                <li>Low Stock Alert</li>
                                <li>Withdrawal History</li>
                                <li>Category Analysis</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prescription Reports -->
            <div class="column is-4">
                <div class="card report-card prescriptions" onclick="openModuleReport('prescriptions')">
                    <header class="card-header">
                        <p class="card-header-title">
                            <span class="icon">
                                <i class="fas fa-prescription-bottle-alt"></i>
                            </span>
                            <span>Prescription Reports</span>
                        </p>
                    </header>
                    <div class="card-content">
                        <div class="content">
                            <p><strong>Available Reports:</strong></p>
                            <ul>
                                <li>Prescription Summary</li>
                                <li>Medicine Usage</li>
                                <li>Doctor Prescriptions</li>
                                <li>Patient Medications</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Insurance Reports -->
            <div class="column is-4">
                <div class="card report-card" onclick="openModuleReport('insurance')">
                    <header class="card-header">
                        <p class="card-header-title">
                            <span class="icon">
                                <i class="fas fa-shield-alt"></i>
                            </span>
                            <span>Insurance Reports</span>
                        </p>
                    </header>
                    <div class="card-content">
                        <div class="content">
                            <p><strong>Available Reports:</strong></p>
                            <ul>
                                <li>Coverage Analysis</li>
                                <li>Provider Statistics</li>
                                <li>Claim Status</li>
                                <li>Patient Coverage</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Reports -->
            <div class="column is-4">
                <div class="card report-card" onclick="openModuleReport('system')">
                    <header class="card-header">
                        <p class="card-header-title">
                            <span class="icon">
                                <i class="fas fa-cogs"></i>
                            </span>
                            <span>System Reports</span>
                        </p>
                    </header>
                    <div class="card-content">
                        <div class="content">
                            <p><strong>Available Reports:</strong></p>
                            <ul>
                                <li>User Activity</li>
                                <li>System Logs</li>
                                <li>Department Reports</li>
                                <li>Usage Statistics</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Spinner -->
<div class="has-text-centered mt-6 mb-6" id="loadingSpinner" style="display: none;">
    <span class="icon is-large">
        <i class="fas fa-spinner fa-pulse fa-2x"></i>
    </span>
    <p class="mt-2">Loading reports...</p>
</div>

<!-- JavaScript -->
<script src="js/reports.js"></script>

    </div> <!-- End of container from header.php -->
</body>
</html> 