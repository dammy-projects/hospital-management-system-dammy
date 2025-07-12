<?php
require_once 'includes/header.php';
?>

<style>
/* Compact styles for prescription cards */
.prescriptions-grid .column {
    margin-bottom: 1rem;
    padding: 0.5rem;
}

.prescriptions-grid .card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border-radius: 6px;
    height: 100%;
    display: flex;
    flex-direction: column;
    max-height: 280px; /* Limit card height */
}

.prescriptions-grid .card:hover {
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

/* Medicine count display */
.medicine-count {
    color: #666;
    font-weight: 500;
    font-size: 0.8rem;
}

/* Prescription items styling */
.prescription-items {
    max-height: 150px;
    overflow-y: auto;
    margin-top: 0.5rem;
}

.prescription-item {
    padding: 0.25rem 0;
    border-bottom: 1px solid #f0f0f0;
    font-size: 0.8rem;
}

.prescription-item:last-child {
    border-bottom: none;
}

/* Dynamic medicine form styling */
.medicine-item {
    border: 1px solid #dbdbdb;
    border-radius: 6px;
    padding: 1rem;
    margin-bottom: 1rem;
    background-color: #fafafa;
}

.medicine-item-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.remove-medicine-btn {
    background: none;
    border: none;
    color: #ff3860;
    cursor: pointer;
    font-size: 1.2rem;
    padding: 0.25rem;
}

.remove-medicine-btn:hover {
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
    .prescriptions-grid .column {
        flex: 0 0 100%;
        max-width: 100%;
    }
}

@media (min-width: 769px) and (max-width: 1023px) {
    .prescriptions-grid .column {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

@media (min-width: 1024px) and (max-width: 1407px) {
    .prescriptions-grid .column {
        flex: 0 0 33.333%;
        max-width: 33.333%;
    }
}

@media (min-width: 1408px) {
    .prescriptions-grid .column {
        flex: 0 0 25%;
        max-width: 25%;
    }
}

/* Large modal for prescription form */
.modal-card {
    max-width: 900px;
    width: 90vw;
}

/* Print styles for prescription receipt */
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
    
    .print-patient-info {
        margin-bottom: 12px;
        border: 1px solid #000;
        padding: 8px;
    }
    
    .print-patient-info h3 {
        margin: 0 0 6px 0 !important;
        font-size: 12px;
        border-bottom: 1px solid #ccc;
        padding-bottom: 3px;
    }
    
    .print-patient-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        font-size: 10px;
        margin-bottom: 6px;
    }
    
    .print-medicines {
        margin-bottom: 12px;
    }
    
    .print-medicines h3 {
        margin: 0 0 8px 0 !important;
        font-size: 12px;
        background-color: #f0f0f0;
        padding: 4px 8px;
        border: 1px solid #000;
    }
    
    .medicines-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #000;
        font-size: 9px;
    }
    
    .medicines-table th,
    .medicines-table td {
        border: 1px solid #ccc;
        padding: 3px 4px;
        text-align: left;
        vertical-align: top;
    }
    
    .medicines-table th {
        background-color: #f5f5f5;
        font-weight: bold;
        font-size: 9px;
    }
    
    .medicine-name {
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
                    <i class="fas fa-prescription-bottle-alt"></i>
                </span>
                Prescription Management
            </h1>
            <p class="subtitle">Manage patient prescriptions and medications</p>
        </div>
        <div class="column is-narrow">
            <button class="button is-primary" id="addPrescriptionBtn">
                <span class="icon">
                    <i class="fas fa-plus"></i>
                </span>
                <span>New Prescription</span>
            </button>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card mb-4">
        <div class="card-content">
            <div class="columns">
                <div class="column is-3">
                    <div class="field">
                        <label class="label">Search Prescriptions</label>
                        <div class="control has-icons-left">
                            <input class="input" type="text" id="searchInput" placeholder="Search by patient, doctor..." />
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
                                    <option value="active">Active</option>
                                    <option value="fulfilled">Fulfilled</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="field">
                        <label class="label">Filter by Patient</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select id="patientFilter">
                                    <option value="">All Patients</option>
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

    <!-- Prescriptions Grid -->
    <div id="prescriptionsContainer">
        <div class="columns is-multiline prescriptions-grid" id="prescriptionsGrid">
            <!-- Prescriptions will be loaded here -->
        </div>
    </div>

    <!-- Loading Spinner -->
    <div class="has-text-centered mt-6 mb-6" id="loadingSpinner" style="display: none;">
        <span class="icon is-large">
            <i class="fas fa-spinner fa-pulse fa-2x"></i>
        </span>
        <p class="mt-2">Loading prescriptions...</p>
    </div>

    <!-- Empty State -->
    <div class="has-text-centered mt-6 mb-6" id="emptyState" style="display: none;">
        <span class="icon is-large has-text-grey-light">
            <i class="fas fa-prescription-bottle-alt fa-3x"></i>
        </span>
        <p class="title is-5 has-text-grey mt-3">No prescriptions found</p>
        <p class="subtitle is-6 has-text-grey">Try adjusting your search criteria or create a new prescription</p>
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

<!-- Add/Edit Prescription Modal -->
<div class="modal" id="prescriptionModal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title" id="modalTitle">New Prescription</p>
            <button class="delete" aria-label="close" id="closeModal"></button>
        </header>
        <section class="modal-card-body">
            <form id="prescriptionForm">
                <input type="hidden" id="prescriptionId" />
                
                <!-- Prescription Details -->
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
                            <label class="label">Doctor *</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select id="doctorId" required>
                                        <option value="">Select Doctor</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="columns">
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Prescription Date *</label>
                            <div class="control">
                                <input class="input" type="date" id="prescriptionDate" required />
                            </div>
                        </div>
                    </div>
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Status *</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select id="status" required>
                                        <option value="active">Active</option>
                                        <option value="fulfilled">Fulfilled</option>
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
                        <textarea class="textarea" id="notes" placeholder="Additional notes or instructions" rows="3"></textarea>
                    </div>
                </div>

                <hr>

                <!-- Medicines Section -->
                <div class="field">
                    <div class="level">
                        <div class="level-left">
                            <div class="level-item">
                                <h4 class="title is-5">Medicines</h4>
                            </div>
                        </div>
                        <div class="level-right">
                            <div class="level-item">
                                <button type="button" class="button is-success is-small" id="addMedicineBtn">
                                    <span class="icon">
                                        <i class="fas fa-plus"></i>
                                    </span>
                                    <span>Add Medicine</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="medicinesContainer">
                    <!-- Medicine items will be added here dynamically -->
                </div>
            </form>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-success" id="savePrescription">Save Prescription</button>
            <button class="button" id="cancelModal">Cancel</button>
        </footer>
    </div>
</div>

<!-- View Prescription Modal -->
<div class="modal" id="viewModal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">Prescription Details</p>
            <button class="delete" aria-label="close" id="closeViewModal"></button>
        </header>
        <section class="modal-card-body" id="prescriptionDetails">
            <!-- Prescription details will be loaded here -->
        </section>
        <footer class="modal-card-foot">
            <button class="button" id="closeViewModalBtn">Close</button>
        </footer>
    </div>
</div>

<!-- Hidden Print Area -->
<div id="prescriptionPrintArea" style="display: none;">
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
                <p>Are you sure you want to delete this prescription?</p>
                <p><strong id="deletePrescriptionInfo"></strong></p>
                <p class="has-text-danger">This action cannot be undone.</p>
            </div>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-danger" id="confirmDelete">Delete Prescription</button>
            <button class="button" id="cancelDelete">Cancel</button>
        </footer>
    </div>
</div>

<!-- Medicine Item Template -->
<template id="medicineItemTemplate">
    <div class="medicine-item">
        <div class="medicine-item-header">
            <h6 class="subtitle is-6">Medicine <span class="medicine-number"></span></h6>
            <button type="button" class="remove-medicine-btn" title="Remove Medicine">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="columns">
            <div class="column is-6">
                <div class="field">
                    <label class="label">Medicine *</label>
                    <div class="control">
                        <div class="select is-fullwidth">
                            <select class="medicine-select" required>
                                <option value="">Select Medicine</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="column is-6">
                <div class="field">
                    <label class="label">Dosage *</label>
                    <div class="control">
                        <input class="input dosage-input" type="text" placeholder="e.g., 500mg, 1 tablet" required />
                    </div>
                </div>
            </div>
        </div>
        <div class="columns">
            <div class="column is-4">
                <div class="field">
                    <label class="label">Frequency *</label>
                    <div class="control">
                        <div class="select is-fullwidth">
                            <select class="frequency-select" required>
                                <option value="">Select Frequency</option>
                                <option value="Once daily">Once daily</option>
                                <option value="Twice daily">Twice daily</option>
                                <option value="Three times daily">Three times daily</option>
                                <option value="Four times daily">Four times daily</option>
                                <option value="As needed">As needed</option>
                                <option value="Before meals">Before meals</option>
                                <option value="After meals">After meals</option>
                                <option value="At bedtime">At bedtime</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="column is-4">
                <div class="field">
                    <label class="label">Duration (days) *</label>
                    <div class="control">
                        <input class="input duration-input" type="number" min="1" placeholder="7" required />
                    </div>
                </div>
            </div>
            <div class="column is-4">
                <div class="field">
                    <label class="label">Quantity *</label>
                    <div class="control">
                        <input class="input quantity-input" type="number" min="1" placeholder="30" required />
                    </div>
                </div>
            </div>
        </div>
        <div class="field">
            <label class="label">Instructions</label>
            <div class="control">
                <textarea class="textarea instructions-input" placeholder="Special instructions for this medicine" rows="2"></textarea>
            </div>
        </div>
    </div>
</template>

<!-- Notification -->
<div class="notification is-fixed" id="notification" style="position: fixed; top: 20px; right: 20px; z-index: 999; display: none;">
    <button class="delete" id="closeNotification"></button>
    <span id="notificationMessage"></span>
</div>

<!-- JavaScript -->
<script src="js/prescriptions.js"></script>

    </div> <!-- End of container from header.php -->
</body>
</html> 