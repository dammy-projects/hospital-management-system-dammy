<?php
require_once 'includes/header.php';
?>

<style>
/* Compact styles for smaller insurance provider cards */
.providers-grid .column {
    margin-bottom: 1rem;
    padding: 0.5rem;
}

.providers-grid .card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border-radius: 6px;
}

.providers-grid .card:hover {
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

/* Ensure proper spacing between name and contact */
.card .title.is-6 {
    margin-bottom: 0.25rem !important;
}

.card .subtitle.is-7 {
    margin-top: 0 !important;
    margin-bottom: 0.5rem !important;
}

/* Address text handling */
.provider-address {
    font-size: 0.75rem;
    color: #6c757d;
    max-height: 2.4em;
    overflow: hidden;
    line-height: 1.2em;
}

/* Mobile responsiveness for smaller cards */
@media (max-width: 768px) {
    .providers-grid .column {
        flex: 0 0 100%;
        max-width: 100%;
    }
}

@media (min-width: 769px) and (max-width: 1023px) {
    .providers-grid .column {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

@media (min-width: 1024px) and (max-width: 1215px) {
    .providers-grid .column {
        flex: 0 0 33.333%;
        max-width: 33.333%;
    }
}

@media (min-width: 1216px) {
    .providers-grid .column {
        flex: 0 0 33.333%;
        max-width: 33.333%;
    }
}
</style>

<div class="page-transition mt-4">
    <!-- Page Header -->
    <div class="columns is-vcentered mb-4">
        <div class="column">
            <h1 class="title is-3">
                <span class="icon">
                    <i class="fas fa-shield-alt"></i>
                </span>
                Insurance Providers
            </h1>
            <p class="subtitle">Manage insurance providers and their information</p>
        </div>
        <div class="column is-narrow">
            <button class="button is-primary" id="addProviderBtn">
                <span class="icon">
                    <i class="fas fa-plus"></i>
                </span>
                <span>Add New Provider</span>
            </button>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card mb-4">
        <div class="card-content">
            <div class="columns">
                <div class="column is-4">
                    <div class="field">
                        <label class="label">Search Providers</label>
                        <div class="control has-icons-left">
                            <input class="input" type="text" id="searchInput" placeholder="Search by name, contact, address..." />
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
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="field">
                        <label class="label">Sort By</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select id="sortBy">
                                    <option value="provider_name">Provider Name</option>
                                    <option value="contact_number">Contact Number</option>
                                    <option value="status">Status</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column is-2">
                    <div class="field">
                        <label class="label">&nbsp;</label>
                        <div class="control">
                            <button class="button is-info is-fullwidth" id="clearFilters">
                                <span class="icon">
                                    <i class="fas fa-redo"></i>
                                </span>
                                <span>Reset</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Providers Grid -->
    <div id="providersContainer">
        <div class="columns is-multiline providers-grid" id="providersGrid">
            <!-- Providers will be loaded here -->
        </div>
    </div>

    <!-- Loading Spinner -->
    <div class="has-text-centered mt-6 mb-6" id="loadingSpinner" style="display: none;">
        <span class="icon is-large">
            <i class="fas fa-spinner fa-pulse fa-2x"></i>
        </span>
        <p class="mt-2">Loading insurance providers...</p>
    </div>

    <!-- Empty State -->
    <div class="has-text-centered mt-6 mb-6" id="emptyState" style="display: none;">
        <span class="icon is-large has-text-grey-light">
            <i class="fas fa-shield-alt fa-3x"></i>
        </span>
        <p class="title is-5 has-text-grey mt-3">No insurance providers found</p>
        <p class="subtitle is-6 has-text-grey">Try adjusting your search criteria or add a new provider</p>
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

<!-- Add/Edit Provider Modal -->
<div class="modal" id="providerModal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title" id="modalTitle">Add New Insurance Provider</p>
            <button class="delete" aria-label="close" id="closeModal"></button>
        </header>
        <section class="modal-card-body">
            <form id="providerForm">
                <input type="hidden" id="providerId" />
                
                <div class="field">
                    <label class="label">Provider Name *</label>
                    <div class="control">
                        <input class="input" type="text" id="providerName" placeholder="Enter insurance provider name" required />
                    </div>
                    <p class="help">Enter the full name of the insurance provider</p>
                </div>

                <div class="field">
                    <label class="label">Contact Number *</label>
                    <div class="control">
                        <input class="input" type="tel" id="contactNumber" placeholder="Enter contact number" required />
                    </div>
                    <p class="help">Enter the main contact number for the provider</p>
                </div>

                <div class="field">
                    <label class="label">Address *</label>
                    <div class="control">
                        <textarea class="textarea" id="address" placeholder="Enter complete address" rows="3" required></textarea>
                    </div>
                    <p class="help">Enter the complete address of the provider</p>
                </div>

                <div class="field">
                    <label class="label">Status *</label>
                    <div class="control">
                        <div class="select is-fullwidth">
                            <select id="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <p class="help">Set the operational status of the provider</p>
                </div>
            </form>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-success" id="saveProvider">Save Provider</button>
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
                <p>Are you sure you want to delete this insurance provider?</p>
                <p><strong id="deleteProviderName"></strong></p>
                <p class="has-text-danger">This action cannot be undone and may affect patients with insurance from this provider.</p>
            </div>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-danger" id="confirmDelete">Delete Provider</button>
            <button class="button" id="cancelDelete">Cancel</button>
        </footer>
    </div>
</div>

<!-- JavaScript -->
<script src="js/insurance_providers.js"></script>

    </div> <!-- End of container from header.php -->
</body>
</html> 