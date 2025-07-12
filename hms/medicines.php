<?php
require_once 'includes/header.php';
?>

<style>
/* Compact styles for smaller medicine cards */
.medicines-grid .column {
    margin-bottom: 1rem;
    padding: 0.5rem;
}

.medicines-grid .card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border-radius: 6px;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.medicines-grid .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.card-footer-item {
    padding: 0.5rem;
    cursor: pointer;
    transition: background-color 0.2s ease;
    font-size: 0.8rem;
}

.card-footer-item:hover {
    background-color: #f5f5f5;
}

/* Text overflow handling */
.card .title,
.card .subtitle {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Ensure proper spacing between medicine name and details */
.card .title.is-6 {
    margin-bottom: 0.25rem !important;
}

.card .subtitle.is-7 {
    margin-top: 0 !important;
    margin-bottom: 0.5rem !important;
}

/* Dosage form badge styling */
.dosage-form-badge {
    font-size: 0.7rem;
    padding: 0.2rem 0.5rem;
    border-radius: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

/* Strength display */
.strength-display {
    color: #666;
    font-weight: 500;
    font-size: 0.9rem;
}

/* Mobile responsiveness for smaller cards */
@media (max-width: 768px) {
    .medicines-grid .column {
        flex: 0 0 100%;
        max-width: 100%;
    }
}

@media (min-width: 769px) and (max-width: 1023px) {
    .medicines-grid .column {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

@media (min-width: 1024px) and (max-width: 1215px) {
    .medicines-grid .column {
        flex: 0 0 33.333%;
        max-width: 33.333%;
    }
}

@media (min-width: 1216px) {
    .medicines-grid .column {
        flex: 0 0 33.333%;
        max-width: 33.333%;
    }
}

/* Card content area should grow */
.card-content {
    flex-grow: 1;
}
</style>

<div class="page-transition mt-4">
    <!-- Page Header -->
    <div class="columns is-vcentered mb-4">
        <div class="column">
            <h1 class="title is-3">
                <span class="icon">
                    <i class="fas fa-pills"></i>
                </span>
                Medicine Management
            </h1>
            <p class="subtitle">Manage hospital medicines and their dosage information</p>
        </div>
        <div class="column is-narrow">
            <button class="button is-primary" id="addMedicineBtn">
                <span class="icon">
                    <i class="fas fa-plus"></i>
                </span>
                <span>Add New Medicine</span>
            </button>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card mb-4">
        <div class="card-content">
            <div class="columns">
                <div class="column is-4">
                    <div class="field">
                        <label class="label">Search Medicines</label>
                        <div class="control has-icons-left">
                            <input class="input" type="text" id="searchInput" placeholder="Search by name, dosage form, strength..." />
                            <span class="icon is-small is-left">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="field">
                        <label class="label">Filter by Dosage Form</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select id="dosageFormFilter">
                                    <option value="">All Dosage Forms</option>
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

    <!-- Medicines Grid -->
    <div id="medicinesContainer">
        <div class="columns is-multiline medicines-grid" id="medicinesGrid">
            <!-- Medicines will be loaded here -->
        </div>
    </div>

    <!-- Loading Spinner -->
    <div class="has-text-centered mt-6 mb-6" id="loadingSpinner" style="display: none;">
        <span class="icon is-large">
            <i class="fas fa-spinner fa-pulse fa-2x"></i>
        </span>
        <p class="mt-2">Loading medicines...</p>
    </div>

    <!-- Empty State -->
    <div class="has-text-centered mt-6 mb-6" id="emptyState" style="display: none;">
        <span class="icon is-large has-text-grey-light">
            <i class="fas fa-pills fa-3x"></i>
        </span>
        <p class="title is-5 has-text-grey mt-3">No medicines found</p>
        <p class="subtitle is-6 has-text-grey">Try adjusting your search criteria or add a new medicine</p>
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

<!-- Add/Edit Medicine Modal -->
<div class="modal" id="medicineModal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title" id="modalTitle">Add New Medicine</p>
            <button class="delete" aria-label="close" id="closeModal"></button>
        </header>
        <section class="modal-card-body">
            <form id="medicineForm">
                <input type="hidden" id="medicineId" />
                
                <div class="field">
                    <label class="label">Medicine Name *</label>
                    <div class="control">
                        <input class="input" type="text" id="medicineName" placeholder="Enter medicine name" required />
                    </div>
                    <p class="help">Enter the generic or brand name of the medicine</p>
                </div>

                <div class="columns">
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Dosage Form *</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select id="dosageForm" required>
                                        <option value="">Select Dosage Form</option>
                                        <option value="Tablet">Tablet</option>
                                        <option value="Capsule">Capsule</option>
                                        <option value="Injection">Injection</option>
                                        <option value="Syrup">Syrup</option>
                                        <option value="Inhaler">Inhaler</option>
                                        <option value="Cream">Cream</option>
                                        <option value="Ointment">Ointment</option>
                                        <option value="Drops">Drops</option>
                                        <option value="Powder">Powder</option>
                                        <option value="Suspension">Suspension</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Strength *</label>
                            <div class="control">
                                <input class="input" type="text" id="strength" placeholder="e.g., 500mg, 10mg/mL" required />
                            </div>
                            <p class="help">Specify the concentration or strength</p>
                        </div>
                    </div>
                </div>
            </form>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-success" id="saveMedicine">Save Medicine</button>
            <button class="button" id="cancelModal">Cancel</button>
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
                <p>Are you sure you want to delete this medicine?</p>
                <p><strong id="deleteMedicineName"></strong></p>
                <p class="has-text-danger">This action cannot be undone.</p>
            </div>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-danger" id="confirmDelete">Delete Medicine</button>
            <button class="button" id="cancelDelete">Cancel</button>
        </footer>
    </div>
</div>

<!-- Notification -->
<div class="notification is-fixed" id="notification" style="position: fixed; top: 20px; right: 20px; z-index: 999; display: none;">
    <button class="delete" id="closeNotification"></button>
    <span id="notificationMessage"></span>
</div>

<!-- JavaScript -->
<script src="js/medicines.js"></script>

    </div> <!-- End of container from header.php -->
</body>
</html> 