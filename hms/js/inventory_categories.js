class InventoryCategoryManager {
    constructor() {
        this.currentPage = 1;
        this.totalPages = 1;
        this.limit = 6;
        this.isEditing = false;
        this.currentCategoryId = null;
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.loadCategories();
    }
    
    bindEvents() {
        // Modal events
        document.getElementById('addCategoryBtn').addEventListener('click', () => this.openModal());
        document.getElementById('closeModal').addEventListener('click', () => this.closeModal());
        document.getElementById('cancelModal').addEventListener('click', () => this.closeModal());
        document.getElementById('saveCategory').addEventListener('click', () => this.saveCategory());
        
        // Delete modal events
        document.getElementById('closeDeleteModal').addEventListener('click', () => this.closeDeleteModal());
        document.getElementById('cancelDelete').addEventListener('click', () => this.closeDeleteModal());
        document.getElementById('confirmDelete').addEventListener('click', () => this.deleteCategory());
        
        // Search and filter events
        document.getElementById('searchInput').addEventListener('input', this.debounce(() => this.searchCategories(), 300));
        document.getElementById('sortFilter').addEventListener('change', () => this.searchCategories());
        document.getElementById('clearFilters').addEventListener('click', () => this.clearFilters());
        
        // Pagination events
        document.getElementById('prevPage').addEventListener('click', () => this.goToPage(this.currentPage - 1));
        document.getElementById('nextPage').addEventListener('click', () => this.goToPage(this.currentPage + 1));
        
        // Close modals when clicking background
        document.getElementById('categoryModal').querySelector('.modal-background').addEventListener('click', () => this.closeModal());
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
        this.showLoading();
        
        try {
            const params = new URLSearchParams({
                action: 'list',
                page: this.currentPage,
                limit: this.limit,
                search: document.getElementById('searchInput').value,
                sort: document.getElementById('sortFilter').value
            });
            
            const response = await fetch(`controllers/InventoryCategoryController.php?${params}`);
            const result = await response.json();
            
            if (result.success) {
                this.displayCategories(result.data);
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
            console.error('Error loading categories:', error);
            this.hideLoading();
            this.showNotification('Error loading categories: ' + error.message, 'danger');
        }
    }
    
    displayCategories(categories) {
        const container = document.getElementById('categoriesGrid');
        container.innerHTML = '';
        
        categories.forEach(category => {
            const categoryCard = this.createCategoryCard(category);
            container.appendChild(categoryCard);
        });
    }
    
    createCategoryCard(category) {
        const card = document.createElement('div');
        card.className = 'column is-4';
        
        card.innerHTML = `
            <div class="card" style="height: 200px;">
                <div class="card-content" style="padding: 1rem;">
                    <div class="has-text-centered mb-3">
                        <div class="mb-2">
                            <span class="icon is-medium has-text-primary">
                                <i class="fas fa-tag fa-2x"></i>
                            </span>
                        </div>
                        <h6 class="title is-6 mb-1" style="line-height: 1.1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${this.escapeHtml(category.category_name)}">${this.escapeHtml(category.category_name)}</h6>
                        <p class="subtitle is-7 has-text-grey mb-2" style="line-height: 1; margin-top: 0.25rem;">ID: ${category.category_id}</p>
                    </div>
                    
                    <div class="content" style="font-size: 0.8rem;">
                        <div class="field mb-2 has-text-centered">
                            <span class="tag is-info is-light is-small">Category</span>
                        </div>
                        
                        <div class="has-text-centered">
                            <p class="is-size-7 has-text-grey">
                                <strong>Category ID:</strong> ${category.category_id}
                            </p>
                        </div>
                    </div>
                </div>
                
                <footer class="card-footer" style="border-top: 1px solid #dbdbdb;">
                    <a class="card-footer-item has-text-info is-size-7" onclick="categoryManager.editCategory(${category.category_id})" style="padding: 0.5rem;">
                        <span class="icon is-small">
                            <i class="fas fa-edit"></i>
                        </span>
                        <span>Edit</span>
                    </a>
                    <a class="card-footer-item has-text-danger is-size-7" onclick="categoryManager.confirmDeleteCategory(${category.category_id}, '${this.escapeHtml(category.category_name)}')" style="padding: 0.5rem;">
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
            this.loadCategories();
        }
    }
    
    searchCategories() {
        this.currentPage = 1;
        this.loadCategories();
    }
    
    clearFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('sortFilter').value = 'category_name';
        this.searchCategories();
    }
    
    openModal(category = null) {
        this.isEditing = !!category;
        this.currentCategoryId = category ? category.category_id : null;
        
        const modal = document.getElementById('categoryModal');
        const title = document.getElementById('modalTitle');
        
        title.textContent = this.isEditing ? 'Edit Category' : 'Add New Category';
        
        if (category) {
            document.getElementById('categoryId').value = category.category_id;
            document.getElementById('categoryName').value = category.category_name || '';
        } else {
            document.getElementById('categoryForm').reset();
            document.getElementById('categoryId').value = '';
        }
        
        modal.classList.add('is-active');
        // Focus on the category name input
        setTimeout(() => {
            document.getElementById('categoryName').focus();
        }, 100);
    }
    
    closeModal() {
        document.getElementById('categoryModal').classList.remove('is-active');
        document.getElementById('categoryForm').reset();
    }
    
    async editCategory(categoryId) {
        try {
            const response = await fetch(`controllers/InventoryCategoryController.php?action=get&id=${categoryId}`);
            const result = await response.json();
            
            if (result.success) {
                this.openModal(result.data);
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error loading category:', error);
            this.showNotification('Error loading category: ' + error.message, 'danger');
        }
    }
    
    async saveCategory() {
        const form = document.getElementById('categoryForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        const categoryData = {
            category_name: document.getElementById('categoryName').value.trim()
        };
        
        // Additional validation
        if (categoryData.category_name.length < 2) {
            this.showNotification('Category name must be at least 2 characters long', 'warning');
            return;
        }
        
        if (categoryData.category_name.length > 100) {
            this.showNotification('Category name must not exceed 100 characters', 'warning');
            return;
        }
        
        try {
            let url = 'controllers/InventoryCategoryController.php?action=';
            let method = 'POST';
            
            if (this.isEditing) {
                url += `update&id=${this.currentCategoryId}`;
                method = 'PUT';
            } else {
                url += 'create';
            }
            
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(categoryData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.closeModal();
                this.loadCategories();
                this.showNotification(result.message, 'success');
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error saving category:', error);
            this.showNotification('Error saving category: ' + error.message, 'danger');
        }
    }
    
    confirmDeleteCategory(categoryId, categoryName) {
        this.currentCategoryId = categoryId;
        document.getElementById('deleteCategoryName').textContent = categoryName;
        document.getElementById('deleteModal').classList.add('is-active');
    }
    
    closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('is-active');
        this.currentCategoryId = null;
    }
    
    async deleteCategory() {
        if (!this.currentCategoryId) return;
        
        try {
            const response = await fetch(`controllers/InventoryCategoryController.php?action=delete&id=${this.currentCategoryId}`, {
                method: 'DELETE'
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.closeDeleteModal();
                this.loadCategories();
                this.showNotification(result.message, 'success');
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error deleting category:', error);
            this.showNotification('Error deleting category: ' + error.message, 'danger');
        }
    }
    
    showLoading() {
        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('categoriesContainer').style.display = 'none';
        document.getElementById('emptyState').style.display = 'none';
    }
    
    hideLoading() {
        document.getElementById('loadingSpinner').style.display = 'none';
        document.getElementById('categoriesContainer').style.display = 'block';
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

// Initialize the category manager when the page loads
let categoryManager;
document.addEventListener('DOMContentLoaded', () => {
    categoryManager = new InventoryCategoryManager();
}); 