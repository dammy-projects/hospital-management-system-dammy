<?php
require_once 'includes/header.php';
?>

<style>
/* Compact styles for smaller inventory cards */
.inventory-grid .column {
    margin-bottom: 1rem;
    padding: 0.5rem;
}

.inventory-grid .card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border-radius: 6px;
    height: 320px;
}

.inventory-grid .card:hover {
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

/* Ensure proper spacing */
.card .title.is-6 {
    margin-bottom: 0.25rem !important;
}

.card .subtitle.is-7 {
    margin-top: 0 !important;
    margin-bottom: 0.5rem !important;
}

/* Stock level indicators */
.stock-indicator {
    border-radius: 50%;
    width: 10px;
    height: 10px;
    display: inline-block;
    margin-right: 5px;
}

.stock-good { background-color: #48c774; }
.stock-low { background-color: #ffdd57; }
.stock-out { background-color: #f14668; }

/* Mobile responsiveness */
@media (max-width: 768px) {
    .inventory-grid .column {
        flex: 0 0 100%;
        max-width: 100%;
    }
}

@media (min-width: 769px) and (max-width: 1023px) {
    .inventory-grid .column {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

@media (min-width: 1024px) and (max-width: 1215px) {
    .inventory-grid .column {
        flex: 0 0 33.333%;
        max-width: 33.333%;
    }
}

@media (min-width: 1216px) {
    .inventory-grid .column {
        flex: 0 0 25%;
        max-width: 25%;
    }
}
</style>

<div class="page-transition mt-4">
    <!-- Page Header -->
    <div class="columns is-vcentered mb-4">
        <div class="column">
            <h1 class="title is-3">
                <span class="icon">
                    <i class="fas fa-boxes"></i>
                </span>
                Inventory Management
            </h1>
            <p class="subtitle">Manage hospital inventory items and stock levels</p>
        </div>
        <div class="column is-narrow">
            <button class="button is-primary" id="addItemBtn">
                <span class="icon">
                    <i class="fas fa-plus"></i>
                </span>
                <span>Add New Item</span>
            </button>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card mb-4">
        <div class="card-content">
            <div class="columns">
                <div class="column is-4">
                    <div class="field">
                        <label class="label">Search Items</label>
                        <div class="control has-icons-left">
                            <input class="input" type="text" id="searchInput" placeholder="Search by name, description, serial..." />
                            <span class="icon is-small is-left">
                                <i class="fas fa-search"></i>
                            </span>
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
                                    <option value="">All Items</option>
                                    <option value="low">Low Stock</option>
                                    <option value="out">Out of Stock</option>
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

    <!-- Inventory Grid -->
    <div id="inventoryContainer">
        <div class="columns is-multiline inventory-grid" id="inventoryGrid">
            <!-- Items will be loaded here -->
        </div>
    </div>

    <!-- Loading Spinner -->
    <div class="has-text-centered mt-6 mb-6" id="loadingSpinner" style="display: none;">
        <span class="icon is-large">
            <i class="fas fa-spinner fa-pulse fa-2x"></i>
        </span>
        <p class="mt-2">Loading inventory...</p>
    </div>

    <!-- Empty State -->
    <div class="has-text-centered mt-6 mb-6" id="emptyState" style="display: none;">
        <span class="icon is-large has-text-grey-light">
            <i class="fas fa-boxes fa-3x"></i>
        </span>
        <p class="title is-5 has-text-grey mt-3">No items found</p>
        <p class="subtitle is-6 has-text-grey">Try adjusting your search criteria or add a new item</p>
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

<!-- Add/Edit Item Modal -->
<div class="modal" id="itemModal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title" id="modalTitle">Add New Item</p>
            <button class="delete" aria-label="close" id="closeModal"></button>
        </header>
        <section class="modal-card-body">
            <form id="itemForm">
                <input type="hidden" id="itemId" />
                
                <div class="columns">
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Item Name *</label>
                            <div class="control">
                                <input class="input" type="text" id="itemName" placeholder="Enter item name" required />
                            </div>
                        </div>
                    </div>
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Category *</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select id="categoryId" required>
                                        <option value="">Select Category</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Description</label>
                    <div class="control">
                        <textarea class="textarea" id="itemDescription" placeholder="Enter item description" rows="3"></textarea>
                    </div>
                </div>

                <div class="columns">
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Serial Number</label>
                            <div class="control">
                                <input class="input" type="text" id="serialNumber" placeholder="Enter serial number" />
                            </div>
                        </div>
                    </div>
                    <div class="column is-6">
                        <div class="field">
                            <label class="label">Product Number</label>
                            <div class="control">
                                <input class="input" type="text" id="productNumber" placeholder="Enter product number" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="columns">
                    <div class="column is-4">
                        <div class="field">
                            <label class="label">Quantity in Stock *</label>
                            <div class="control">
                                <input class="input" type="number" id="quantityInStock" placeholder="0" min="0" required />
                            </div>
                        </div>
                    </div>
                    <div class="column is-4">
                        <div class="field">
                            <label class="label">Unit</label>
                            <div class="control">
                                <input class="input" type="text" id="unit" placeholder="e.g., pieces, bottles, boxes" />
                            </div>
                        </div>
                    </div>
                    <div class="column is-4">
                        <div class="field">
                            <label class="label">Reorder Level</label>
                            <div class="control">
                                <input class="input" type="number" id="reorderLevel" placeholder="0" min="0" />
                            </div>
                            <p class="help">Alert when stock falls below this level</p>
                        </div>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Status *</label>
                    <div class="control">
                        <div class="select is-fullwidth">
                            <select id="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="discontinued">Discontinued</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-success" id="saveItem">Save Item</button>
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
                <p>Are you sure you want to delete this inventory item?</p>
                <p><strong id="deleteItemName"></strong></p>
                <p class="has-text-danger">This action cannot be undone and may affect related records.</p>
            </div>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-danger" id="confirmDelete">Delete Item</button>
            <button class="button" id="cancelDelete">Cancel</button>
        </footer>
    </div>
</div>

<!-- JavaScript -->
<script src="js/inventory.js"></script>

    </div> <!-- End of container from header.php -->
</body>
</html> 