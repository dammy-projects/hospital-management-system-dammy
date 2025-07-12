<?php
require_once 'includes/header.php';
?>

<style>
/* Compact styles for billing cards */
.billing-grid .column {
    margin-bottom: 1rem;
    padding: 0.5rem;
}

.billing-grid .card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border-radius: 6px;
    height: 100%;
    display: flex;
    flex-direction: column;
    max-height: 280px; /* Limit card height */
}

.billing-grid .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.card-footer-item {
    padding: 0.4rem 0.2rem;
    cursor: pointer;
    transition: background-color 0.2s ease;
    font-size: 0.7rem;
    text-align: center;
    border-right: 1px solid #dbdbdb;
    flex: 1;
    min-width: 0;
}

.card-footer-item:last-child {
    border-right: none;
}

.card-footer-item:hover {
    background-color: #f5f5f5;
}

.card-footer-item .icon {
    margin-right: 0.25rem !important;
}

.card-footer-item span:not(.icon) {
    font-size: 0.65rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Text overflow handling */
.card .title,
.card .subtitle {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.card .title.is-6 {
    font-size: 1rem; /* Smaller title */
    margin-bottom: 0.25rem !important;
}

.card .subtitle.is-7 {
    font-size: 0.8rem; /* Smaller subtitle */
    margin-top: 0 !important;
    margin-bottom: 0.5rem !important;
}

/* Status badge styling */
.status-badge {
    font-size: 0.65rem;
    padding: 0.15rem 0.4rem;
    border-radius: 8px;
    font-weight: 600;
    text-transform: uppercase;
}

/* Amount display */
.amount-display {
    color: #2563eb;
    font-weight: 600;
    font-size: 1.1rem;
}

/* Insurance status styling */
.insurance-status {
    font-size: 0.65rem;
    padding: 0.1rem 0.3rem;
    border-radius: 6px;
    font-weight: 500;
    text-transform: capitalize;
}

/* Compact card content */
.card-content {
    flex-grow: 1;
    padding: 0.75rem; /* Reduced padding */
}

.card-content .content {
    font-size: 0.85rem; /* Smaller text overall */
}

/* Mobile responsiveness for smaller cards */
@media (max-width: 768px) {
    .billing-grid .column {
        flex: 0 0 100%;
        max-width: 100%;
    }
}

@media (min-width: 769px) and (max-width: 1023px) {
    .billing-grid .column {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

@media (min-width: 1024px) and (max-width: 1407px) {
    .billing-grid .column {
        flex: 0 0 33.333%;
        max-width: 33.333%;
    }
}

@media (min-width: 1408px) {
    .billing-grid .column {
        flex: 0 0 25%;
        max-width: 25%;
    }
}

/* Large modal for billing form */
.modal-card {
    max-width: 700px;
    width: 90vw;
}

/* Peso symbol styling in input field */
.icon.is-small.is-left {
    font-weight: bold;
    font-size: 14px;
    color: #363636;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Print styles for billing receipt */
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
    
    .print-bill-info {
        margin-bottom: 12px;
        border: 1px solid #000;
        padding: 8px;
    }
    
    .print-bill-info h3 {
        margin: 0 0 6px 0 !important;
        font-size: 12px;
        border-bottom: 1px solid #ccc;
        padding-bottom: 3px;
    }
    
    .print-bill-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        font-size: 10px;
        margin-bottom: 6px;
    }
    
    .print-amount {
        margin-bottom: 12px;
        font-size: 14px;
        font-weight: bold;
        text-align: center;
        background-color: #f0f0f0;
        padding: 8px;
        border: 2px solid #000;
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
    
    @page {
        margin: 0.5in;
        size: letter;
    }
}
</style>

<div class="page-transition mt-4">
    <!-- Page Header -->
    <div class="columns is-vcentered mb-4">
        <div class="column">
            <h1 class="title is-3">
                <span class="icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </span>
                Billing Management
            </h1>
            <p class="subtitle">Manage patient billing and insurance claims</p>
        </div>
        <div class="column is-narrow">
            <button class="button is-primary" id="addBillingBtn">
                <span class="icon">
                    <i class="fas fa-plus"></i>
                </span>
                <span>New Bill</span>
            </button>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card mb-4">
        <div class="card-content">
            <div class="columns">
                <div class="column is-3">
                    <div class="field">
                        <label class="label">Search Bills</label>
                        <div class="control has-icons-left">
                            <input class="input" type="text" id="searchInput" placeholder="Search by patient, amount..." />
                            <span class="icon is-small is-left">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="field">
                        <label class="label">Payment Status</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select id="paymentStatusFilter">
                                    <option value="">All Payment Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="paid">Paid</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="field">
                        <label class="label">Insurance Status</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select id="insuranceStatusFilter">
                                    <option value="">All Insurance Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="field">
                        <label class="label">&nbsp;</label>
                        <div class="control">
                            <button class="button is-info is-fullwidth" id="clearFilters">
                                <span class="icon">
                                    <i class="fas fa-redo"></i>
                                </span>
                                <span>Reset Filters</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Billing Grid -->
    <div id="billingContainer">
        <div class="columns is-multiline billing-grid" id="billingGrid">
            <!-- Bills will be loaded here -->
        </div>
    </div>

    <!-- Loading Spinner -->
    <div class="has-text-centered mt-6 mb-6" id="loadingSpinner" style="display: none;">
        <span class="icon is-large">
            <i class="fas fa-spinner fa-pulse fa-2x"></i>
        </span>
        <p class="mt-2">Loading bills...</p>
    </div>

    <!-- Empty State -->
    <div class="has-text-centered mt-6 mb-6" id="emptyState" style="display: none;">
        <span class="icon is-large has-text-grey-light">
            <i class="fas fa-file-invoice-dollar fa-3x"></i>
        </span>
        <p class="title is-5 has-text-grey mt-3">No bills found</p>
        <p class="subtitle is-6 has-text-grey">Try adjusting your search criteria or create a new bill</p>
    </div>

    <!-- Pagination -->
    <nav class="pagination is-centered mt-5" role="navigation" aria-label="pagination" id="pagination" style="display: none;">
        <a class="pagination-previous" id="prevPage">Previous</a>
        <a class="pagination-next" id="nextPage">Next page</a>
        <ul class="pagination-list" id="paginationList">
            <!-- Pagination buttons will be generated here -->
        </ul>
    </nav>
</div>

<!-- Add/Edit Billing Modal -->
<div class="modal" id="billingModal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title" id="modalTitle">New Bill</p>
            <button class="delete" aria-label="close" id="closeModal"></button>
        </header>
        <section class="modal-card-body">
            <form id="billingForm">
                <input type="hidden" id="billingId" />
                
                <!-- Billing Details -->
                <div class="columns">
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Patient *</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select id="patientId" required>
                                        <option value="">Select Patient</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Appointment</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select id="appointmentId">
                                        <option value="">Select Appointment (Optional)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="columns">
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Amount *</label>
                            <div class="control has-icons-left">
                                <input class="input" type="number" step="0.01" min="0" id="amount" required placeholder="0.00" />
                                <span class="icon is-small is-left">
                                    ₱
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Billing Date *</label>
                            <div class="control">
                                <input class="input" type="datetime-local" id="billingDate" required />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="columns">
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Payment Status *</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select id="paymentStatus" required>
                                        <option value="pending">Pending</option>
                                        <option value="paid">Paid</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Insurance Claim Status *</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select id="insuranceClaimStatus" required>
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-success" id="saveBilling">Save Bill</button>
            <button class="button" id="cancelModal">Cancel</button>
        </footer>
    </div>
</div>

<!-- View Billing Modal -->
<div class="modal" id="viewModal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">Bill Details</p>
            <button class="delete" aria-label="close" id="closeViewModal"></button>
        </header>
        <section class="modal-card-body">
            <div id="billingDetails">
                <!-- Bill details will be loaded here -->
            </div>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-success" id="printFromViewBtn">
                <span class="icon">
                    <i class="fas fa-print"></i>
                </span>
                <span>Print Receipt</span>
            </button>
            <button class="button" id="closeViewModalBtn">Close</button>
        </footer>
    </div>
</div>

<!-- Hidden Print Area -->
<div id="billingPrintArea" style="display: none;">
    <!-- Print content will be generated here -->
</div>

<!-- Delete Confirmation Modal -->
<div class="modal" id="deleteModal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">Confirm Delete</p>
            <button class="delete" aria-label="close" id="closeDeleteModal"></button>
        </header>
        <section class="modal-card-body">
            <div class="content">
                <p>Are you sure you want to delete this bill?</p>
                <p><strong id="deleteBillingInfo"></strong></p>
                <p class="has-text-danger">This action cannot be undone.</p>
            </div>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-danger" id="confirmDelete">Delete Bill</button>
            <button class="button" id="cancelDelete">Cancel</button>
        </footer>
    </div>
</div>

<!-- JavaScript -->
<script src="js/billing.js"></script>

    </div> <!-- End of container from header.php -->
</body>
</html> 