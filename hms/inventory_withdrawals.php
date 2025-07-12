<?php
require_once 'includes/header.php';
?>

<style>
/* Compact styles for withdrawal cards */
.withdrawals-grid .column {
    margin-bottom: 1rem;
    padding: 0.5rem;
}

.withdrawals-grid .card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border-radius: 6px;
    height: 100%;
    display: flex;
    flex-direction: column;
    max-height: 280px; /* Limit card height */
}

.withdrawals-grid .card:hover {
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

/* Item count display */
.item-count {
    color: #666;
    font-weight: 500;
    font-size: 0.8rem;
}

/* Withdrawal items styling */
.withdrawal-items {
    max-height: 150px;
    overflow-y: auto;
    margin-top: 0.5rem;
}

.withdrawal-item {
    padding: 0.25rem 0;
    border-bottom: 1px solid #f0f0f0;
    font-size: 0.8rem;
}

.withdrawal-item:last-child {
    border-bottom: none;
}

/* Dynamic item form styling */
.item-entry {
    border: 1px solid #dbdbdb;
    border-radius: 6px;
    padding: 1rem;
    margin-bottom: 1rem;
    background-color: #fafafa;
}

.item-entry-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.remove-item-btn {
    background: none;
    border: none;
    color: #ff3860;
    cursor: pointer;
    font-size: 1.2rem;
    padding: 0.25rem;
}

.remove-item-btn:hover {
    background-color: rgba(255, 56, 96, 0.1);
    border-radius: 50%;
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
    .withdrawals-grid .column {
        flex: 0 0 100%;
        max-width: 100%;
    }
}

@media (min-width: 769px) and (max-width: 1023px) {
    .withdrawals-grid .column {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

@media (min-width: 1024px) and (max-width: 1407px) {
    .withdrawals-grid .column {
        flex: 0 0 33.333%;
        max-width: 33.333%;
    }
}

@media (min-width: 1408px) {
    .withdrawals-grid .column {
        flex: 0 0 25%;
        max-width: 25%;
    }
}

/* Large modal for withdrawal form */
.modal-card {
    max-width: 900px;
    width: 90vw;
}

/* Print styles for withdrawal receipt */
@media print {
    body * {
        visibility: hidden;
    }
    
    #withdrawalPrintArea,
    #withdrawalPrintArea * {
        visibility: visible;
    }
    
    #withdrawalPrintArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100% !important;
        padding: 15px;
        font-family: Arial, sans-serif;
        display: block !important; /* Override inline display: none */
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
    
    .print-withdrawal-info {
        margin-bottom: 12px;
        border: 1px solid #000;
        padding: 8px;
    }
    
    .print-withdrawal-info h3 {
        margin: 0 0 6px 0 !important;
        font-size: 12px;
        border-bottom: 1px solid #ccc;
        padding-bottom: 3px;
    }
    
    .print-withdrawal-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        font-size: 10px;
        margin-bottom: 6px;
    }
    
    .print-items {
        margin-bottom: 12px;
    }
    
    .print-items h3 {
        margin: 0 0 8px 0 !important;
        font-size: 12px;
        background-color: #f0f0f0;
        padding: 4px 8px;
        border: 1px solid #000;
    }
    
    .items-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #000;
        font-size: 9px;
    }
    
    .items-table th,
    .items-table td {
        border: 1px solid #ccc;
        padding: 3px 4px;
        text-align: left;
        vertical-align: top;
    }
    
    .items-table th {
        background-color: #f5f5f5;
        font-weight: bold;
        font-size: 9px;
    }
    
    .item-name {
        font-weight: bold;
        font-size: 10px;
    }
    
    .print-notes {
        margin-bottom: 12px;
        font-size: 10px;
        background-color: #f9f9f9;
        padding: 6px;
        border: 1px solid #ddd;
    }
    
    .print-signature {
        margin-top: 15px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    
    .signature-box {
        text-align: center;
        font-size: 9px;
    }
    
    .signature-line {
        border-bottom: 1px solid #000;
        height: 25px;
        margin-bottom: 3px;
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
}
</style>

<div class="page-transition mt-4">
    <!-- Page Header -->
    <div class="columns is-vcentered mb-4">
        <div class="column">
            <h1 class="title is-3">
                <span class="icon">
                    <i class="fas fa-box-open"></i>
                </span>
                Inventory Withdrawals
            </h1>
            <p class="subtitle">Manage inventory withdrawals and stock distribution</p>
        </div>
        <div class="column is-narrow">
            <button class="button is-primary" id="addWithdrawalBtn">
                <span class="icon">
                    <i class="fas fa-plus"></i>
                </span>
                <span>New Withdrawal</span>
            </button>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card mb-4">
        <div class="card-content">
            <div class="columns">
                <div class="column is-3">
                    <div class="field">
                        <label class="label">Search Withdrawals</label>
                        <div class="control has-icons-left">
                            <input class="input" type="text" id="searchInput" placeholder="Search by notes, user..." />
                            <span class="icon is-small is-left">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="field">
                        <label class="label">Filter by Status</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select id="statusFilter">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="field">
                        <label class="label">Filter by User</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select id="userFilter">
                                    <option value="">All Users</option>
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

    <!-- Withdrawals Grid -->
    <div id="withdrawalsContainer">
        <div class="columns is-multiline withdrawals-grid" id="withdrawalsGrid">
            <!-- Withdrawals will be loaded here -->
        </div>
    </div>

    <!-- Loading Spinner -->
    <div class="has-text-centered mt-6 mb-6" id="loadingSpinner" style="display: none;">
        <span class="icon is-large">
            <i class="fas fa-spinner fa-pulse fa-2x"></i>
        </span>
        <p class="mt-2">Loading withdrawals...</p>
    </div>

    <!-- Empty State -->
    <div class="has-text-centered mt-6 mb-6" id="emptyState" style="display: none;">
        <span class="icon is-large has-text-grey-light">
            <i class="fas fa-box-open fa-3x"></i>
        </span>
        <p class="title is-5 has-text-grey mt-3">No withdrawals found</p>
        <p class="subtitle is-6 has-text-grey">Try adjusting your search criteria or create a new withdrawal</p>
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

<!-- Add/Edit Withdrawal Modal -->
<div class="modal" id="withdrawalModal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title" id="modalTitle">New Withdrawal</p>
            <button class="delete" aria-label="close" id="closeModal"></button>
        </header>
        <section class="modal-card-body">
            <form id="withdrawalForm">
                <input type="hidden" id="withdrawalId" />
                
                <!-- Withdrawal Details -->
                <div class="columns">
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Withdrawal Date *</label>
                            <div class="control">
                                <input class="input" type="datetime-local" id="withdrawalDate" required />
                            </div>
                        </div>
                    </div>
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Status *</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select id="status" required>
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="completed" selected>Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Notes</label>
                    <div class="control">
                        <textarea class="textarea" id="notes" placeholder="Enter withdrawal notes or reason..." rows="3"></textarea>
                    </div>
                </div>

                <!-- Items Section -->
                <div class="field">
                    <label class="label">Withdrawal Items *</label>
                    <div class="control">
                        <button type="button" class="button is-info is-small" id="addItemBtn">
                            <span class="icon">
                                <i class="fas fa-plus"></i>
                            </span>
                            <span>Add Item</span>
                        </button>
                    </div>
                </div>

                <div id="itemsContainer">
                    <!-- Dynamic withdrawal items will be added here -->
                </div>
            </form>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-success" id="saveWithdrawal">Save Withdrawal</button>
            <button class="button" id="cancelModal">Cancel</button>
        </footer>
    </div>
</div>

<!-- View Withdrawal Modal -->
<div class="modal" id="viewModal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">Withdrawal Details</p>
            <button class="delete" aria-label="close" id="closeViewModal"></button>
        </header>
        <section class="modal-card-body">
            <div id="withdrawalDetails">
                <!-- Withdrawal details will be loaded here -->
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
                <p>Are you sure you want to delete this withdrawal?</p>
                <p><strong id="deleteWithdrawalInfo"></strong></p>
                <p class="has-text-danger">This action cannot be undone and may affect inventory records.</p>
            </div>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-danger" id="confirmDelete">Delete Withdrawal</button>
            <button class="button" id="cancelDelete">Cancel</button>
        </footer>
    </div>
</div>

<!-- Hidden Print Area -->
<div id="withdrawalPrintArea" style="display: none;">
    <!-- Print content will be generated here -->
</div>

<!-- JavaScript -->
<script src="js/inventory_withdrawals.js"></script>

    </div> <!-- End of container from header.php -->
</body>
</html> 