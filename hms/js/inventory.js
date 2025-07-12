// Inventory Management JavaScript
class InventoryManager {
    constructor() {
        this.currentPage = 1;
        this.totalPages = 1;
        this.limit = 6;
        this.isEditing = false;
        this.currentItemId = null;
        this.categories = [];
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.loadCategories();
        this.loadItems();
    }
    
    bindEvents() {
        // Modal events
        document.getElementById('addItemBtn').addEventListener('click', () => this.openModal());
        document.getElementById('closeModal').addEventListener('click', () => this.closeModal());
        document.getElementById('cancelModal').addEventListener('click', () => this.closeModal());
        document.getElementById('saveItem').addEventListener('click', () => this.saveItem());
        
        // Delete modal events
        document.getElementById('closeDeleteModal').addEventListener('click', () => this.closeDeleteModal());
        document.getElementById('cancelDelete').addEventListener('click', () => this.closeDeleteModal());
        document.getElementById('confirmDelete').addEventListener('click', () => this.deleteItem());
        
        // Search and filter events
        document.getElementById('searchInput').addEventListener('input', this.debounce(() => this.searchItems(), 300));
        document.getElementById('categoryFilter').addEventListener('change', () => this.searchItems());
        document.getElementById('statusFilter').addEventListener('change', () => this.searchItems());
        document.getElementById('stockFilter').addEventListener('change', () => this.searchItems());
        document.getElementById('clearFilters').addEventListener('click', () => this.clearFilters());
        
        // Pagination events
        document.getElementById('prevPage').addEventListener('click', () => this.goToPage(this.currentPage - 1));
        document.getElementById('nextPage').addEventListener('click', () => this.goToPage(this.currentPage + 1));
        
        // Close modals when clicking background
        document.getElementById('itemModal').querySelector('.modal-background').addEventListener('click', () => this.closeModal());
        document.getElementById('deleteModal').querySelector('.modal-background').addEventListener('click', () => this.closeDeleteModal());
        
        // Close modals on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeModal();
                this.closeDeleteModal();
            }
        });
    }
    
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    async loadCategories() {
        try {
            const response = await fetch('controllers/InventoryItemController.php?action=categories');
            const result = await response.json();
            
            if (result.success) {
                this.categories = result.data;
                this.populateCategoryDropdowns();
            }
        } catch (error) {
            console.error('Error loading categories:', error);
        }
    }
    
    populateCategoryDropdowns() {
        const categorySelect = document.getElementById('categoryId');
        const categoryFilter = document.getElementById('categoryFilter');
        
        // Clear existing options (except the first one)
        categorySelect.innerHTML = '<option value="">Select Category</option>';
        categoryFilter.innerHTML = '<option value="">All Categories</option>';
        
        this.categories.forEach(category => {
            categorySelect.innerHTML += `<option value="${category.category_id}">${category.category_name}</option>`;
            categoryFilter.innerHTML += `<option value="${category.category_id}">${category.category_name}</option>`;
        });
    }
    
    async loadItems() {
        this.showLoading();
        
        try {
            const params = new URLSearchParams({
                action: 'list',
                page: this.currentPage,
                limit: this.limit,
                search: document.getElementById('searchInput').value,
                category_filter: document.getElementById('categoryFilter').value,
                status_filter: document.getElementById('statusFilter').value,
                stock_filter: document.getElementById('stockFilter').value
            });
            
            const response = await fetch(`controllers/InventoryItemController.php?${params}`);
            const result = await response.json();
            
            if (result.success) {
                this.displayItems(result.data);
                this.updatePagination(result.pagination);
                this.hideLoading();
                
                if (result.data.length === 0) {
                    this.showEmptyState();
                } else {
                    this.hideEmptyState();
                }
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error loading items:', error);
            this.hideLoading();
            this.showNotification('Error loading items: ' + error.message, 'danger');
        }
    }
    
    displayItems(items) {
        const container = document.getElementById('inventoryGrid');
        container.innerHTML = '';
        
        items.forEach(item => {
            const itemCard = this.createItemCard(item);
            container.appendChild(itemCard);
        });
    }
    
    createItemCard(item) {
        const card = document.createElement('div');
        card.className = 'column is-4';
        
        const statusColor = {
            'active': 'success',
            'inactive': 'warning', 
            'discontinued': 'danger'
        }[item.status] || 'info';
        
        // Determine stock level
        let stockClass = 'stock-good';
        let stockText = 'Good Stock';
        if (item.quantity_in_stock === 0) {
            stockClass = 'stock-out';
            stockText = 'Out of Stock';
        } else if (item.quantity_in_stock <= item.reorder_level) {
            stockClass = 'stock-low';
            stockText = 'Low Stock';
        }
        
        card.innerHTML = `
            <div class="card">
                <div class="card-content" style="padding: 1rem; height: 270px; display: flex; flex-direction: column;">
                    <div class="has-text-centered mb-3">
                        <div class="mb-2">
                            <span class="icon is-medium has-text-primary">
                                <i class="fas fa-box fa-2x"></i>
                            </span>
                        </div>
                        <h6 class="title is-6 mb-1" style="line-height: 1.1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${this.escapeHtml(item.item_name)}">${this.escapeHtml(item.item_name)}</h6>
                        <p class="subtitle is-7 has-text-grey mb-2" style="line-height: 1; margin-top: 0.25rem;">${this.escapeHtml(item.category_name || 'No Category')}</p>
                    </div>
                    
                    <div class="content" style="font-size: 0.8rem; flex-grow: 1;">
                        <div class="field mb-2">
                            <span class="tag is-${statusColor} is-light is-small">${item.status}</span>
                            <span class="stock-indicator ${stockClass}"></span>
                            <span class="is-size-7">${stockText}</span>
                        </div>
                        
                        <div style="margin-bottom: 0.5rem;">
                            <p class="mb-1 is-size-7"><strong>Stock:</strong> ${item.quantity_in_stock} ${item.unit || 'units'}</p>
                            ${item.reorder_level > 0 ? `<p class="mb-1 is-size-7"><strong>Reorder:</strong> ${item.reorder_level}</p>` : ''}
                            ${item.serial_number ? `<p class="mb-1 is-size-7" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${this.escapeHtml(item.serial_number)}"><strong>Serial:</strong> ${this.escapeHtml(item.serial_number)}</p>` : ''}
                        </div>
                        
                        ${item.item_description ? `<p class="is-size-7 has-text-grey" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${this.escapeHtml(item.item_description)}">${this.escapeHtml(item.item_description.length > 40 ? item.item_description.substring(0, 40) + '...' : item.item_description)}</p>` : ''}
                    </div>
                </div>
                
                <footer class="card-footer" style="border-top: 1px solid #dbdbdb;">
                    <a class="card-footer-item has-text-info is-size-7" onclick="inventoryManager.editItem(${item.item_id})" style="padding: 0.5rem;">
                        <span class="icon is-small">
                            <i class="fas fa-edit"></i>
                        </span>
                        <span>Edit</span>
                    </a>
                    <a class="card-footer-item has-text-danger is-size-7" onclick="inventoryManager.confirmDeleteItem(${item.item_id}, '${this.escapeHtml(item.item_name)}')" style="padding: 0.5rem;">
                        <span class="icon is-small">
                            <i class="fas fa-trash"></i>
                        </span>
                        <span>Delete</span>
                    </a>
                </footer>
            </div>
        `;
        
        return card;
    }
    
    updatePagination(pagination) {
        this.currentPage = pagination.current_page;
        this.totalPages = pagination.total_pages;
        
        const paginationElement = document.getElementById('pagination');
        const paginationList = document.getElementById('paginationList');
        const prevButton = document.getElementById('prevPage');
        const nextButton = document.getElementById('nextPage');
        
        if (this.totalPages <= 1) {
            paginationElement.style.display = 'none';
            return;
        }
        
        paginationElement.style.display = 'flex';
        
        // Update previous/next buttons
        prevButton.disabled = this.currentPage === 1;
        nextButton.disabled = this.currentPage === this.totalPages;
        
        if (this.currentPage === 1) {
            prevButton.classList.add('is-disabled');
        } else {
            prevButton.classList.remove('is-disabled');
        }
        
        if (this.currentPage === this.totalPages) {
            nextButton.classList.add('is-disabled');
        } else {
            nextButton.classList.remove('is-disabled');
        }
        
        // Generate page numbers
        paginationList.innerHTML = '';
        
        const startPage = Math.max(1, this.currentPage - 2);
        const endPage = Math.min(this.totalPages, this.currentPage + 2);
        
        for (let i = startPage; i <= endPage; i++) {
            const li = document.createElement('li');
            const a = document.createElement('a');
            a.className = `pagination-link ${i === this.currentPage ? 'is-current' : ''}`;
            a.textContent = i;
            a.addEventListener('click', () => this.goToPage(i));
            li.appendChild(a);
            paginationList.appendChild(li);
        }
    }
    
    goToPage(page) {
        if (page >= 1 && page <= this.totalPages && page !== this.currentPage) {
            this.currentPage = page;
            this.loadItems();
        }
    }
    
    searchItems() {
        this.currentPage = 1;
        this.loadItems();
    }
    
    clearFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('categoryFilter').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('stockFilter').value = '';
        this.searchItems();
    }
    
    openModal(item = null) {
        this.isEditing = !!item;
        this.currentItemId = item ? item.item_id : null;
        
        const modal = document.getElementById('itemModal');
        const title = document.getElementById('modalTitle');
        
        title.textContent = this.isEditing ? 'Edit Item' : 'Add New Item';
        
        if (item) {
            document.getElementById('itemId').value = item.item_id;
            document.getElementById('itemName').value = item.item_name || '';
            document.getElementById('itemDescription').value = item.item_description || '';
            document.getElementById('serialNumber').value = item.serial_number || '';
            document.getElementById('productNumber').value = item.product_number || '';
            document.getElementById('categoryId').value = item.category_id || '';
            document.getElementById('quantityInStock').value = item.quantity_in_stock || 0;
            document.getElementById('unit').value = item.unit || '';
            document.getElementById('reorderLevel').value = item.reorder_level || 0;
            document.getElementById('status').value = item.status || 'active';
        } else {
            document.getElementById('itemForm').reset();
            document.getElementById('itemId').value = '';
            document.getElementById('status').value = 'active';
        }
        
        modal.classList.add('is-active');
        // Focus on the item name input
        setTimeout(() => {
            document.getElementById('itemName').focus();
        }, 100);
    }
    
    closeModal() {
        document.getElementById('itemModal').classList.remove('is-active');
        document.getElementById('itemForm').reset();
    }
    
    async editItem(itemId) {
        try {
            const response = await fetch(`controllers/InventoryItemController.php?action=get&id=${itemId}`);
            const result = await response.json();
            
            if (result.success) {
                this.openModal(result.data);
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error loading item:', error);
            this.showNotification('Error loading item: ' + error.message, 'danger');
        }
    }
    
    async saveItem() {
        const form = document.getElementById('itemForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        const itemData = {
            item_name: document.getElementById('itemName').value.trim(),
            item_description: document.getElementById('itemDescription').value.trim(),
            serial_number: document.getElementById('serialNumber').value.trim(),
            product_number: document.getElementById('productNumber').value.trim(),
            category_id: document.getElementById('categoryId').value,
            quantity_in_stock: parseInt(document.getElementById('quantityInStock').value) || 0,
            unit: document.getElementById('unit').value.trim(),
            reorder_level: parseInt(document.getElementById('reorderLevel').value) || 0,
            status: document.getElementById('status').value
        };
        
        // Additional validation
        if (itemData.item_name.length < 2) {
            this.showNotification('Item name must be at least 2 characters long', 'warning');
            return;
        }
        
        if (!itemData.category_id) {
            this.showNotification('Please select a category', 'warning');
            return;
        }
        
        if (itemData.quantity_in_stock < 0) {
            this.showNotification('Quantity in stock cannot be negative', 'warning');
            return;
        }
        
        if (itemData.reorder_level < 0) {
            this.showNotification('Reorder level cannot be negative', 'warning');
            return;
        }
        
        try {
            let url = 'controllers/InventoryItemController.php?action=';
            let method = 'POST';
            
            if (this.isEditing) {
                url += `update&id=${this.currentItemId}`;
                method = 'PUT';
            } else {
                url += 'create';
            }
            
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(itemData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.closeModal();
                this.loadItems();
                this.showNotification(result.message, 'success');
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error saving item:', error);
            this.showNotification('Error saving item: ' + error.message, 'danger');
        }
    }
    
    confirmDeleteItem(itemId, itemName) {
        this.currentItemId = itemId;
        document.getElementById('deleteItemName').textContent = itemName;
        document.getElementById('deleteModal').classList.add('is-active');
    }
    
    closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('is-active');
        this.currentItemId = null;
    }
    
    async deleteItem() {
        if (!this.currentItemId) return;
        
        try {
            const response = await fetch(`controllers/InventoryItemController.php?action=delete&id=${this.currentItemId}`, {
                method: 'DELETE'
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.closeDeleteModal();
                this.loadItems();
                this.showNotification(result.message, 'success');
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error deleting item:', error);
            this.showNotification('Error deleting item: ' + error.message, 'danger');
        }
    }
    
    showLoading() {
        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('inventoryContainer').style.display = 'none';
        document.getElementById('emptyState').style.display = 'none';
    }
    
    hideLoading() {
        document.getElementById('loadingSpinner').style.display = 'none';
        document.getElementById('inventoryContainer').style.display = 'block';
    }
    
    showEmptyState() {
        document.getElementById('emptyState').style.display = 'block';
        document.getElementById('pagination').style.display = 'none';
    }
    
    hideEmptyState() {
        document.getElementById('emptyState').style.display = 'none';
    }
    
    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification is-${type} is-light`;
        notification.innerHTML = `
            <button class="delete"></button>
            ${this.escapeHtml(message)}
        `;
        
        // Add to page
        const container = document.querySelector('.container');
        container.insertBefore(notification, container.firstChild);
        
        // Add close functionality
        notification.querySelector('.delete').addEventListener('click', () => {
            notification.remove();
        });
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
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
        return text.replace(/[&<>"']/g, (m) => map[m]);
    }
}

// Initialize when page loads
let inventoryManager;
document.addEventListener('DOMContentLoaded', () => {
    inventoryManager = new InventoryManager();
}); 