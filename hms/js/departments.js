// Department Management JavaScript
class DepartmentManager {
    constructor() {
        this.currentPage = 1;
        this.itemsPerPage = 6;
        this.totalPages = 1;
        this.currentDepartmentId = null;
        this.searchTimer = null;
        
        this.initializeEventListeners();
        this.loadDepartments();
    }

    initializeEventListeners() {
        // Add Department Button
        document.getElementById('addDepartmentBtn').addEventListener('click', () => {
            this.openModal();
        });

        // Modal Close Buttons
        document.getElementById('closeModal').addEventListener('click', () => {
            this.closeModal();
        });

        document.getElementById('cancelModal').addEventListener('click', () => {
            this.closeModal();
        });

        // Save Department Button
        document.getElementById('saveDepartment').addEventListener('click', () => {
            this.saveDepartment();
        });

        // Delete Modal Close Buttons
        document.getElementById('closeDeleteModal').addEventListener('click', () => {
            this.closeDeleteModal();
        });

        document.getElementById('cancelDelete').addEventListener('click', () => {
            this.closeDeleteModal();
        });

        // Confirm Delete Button
        document.getElementById('confirmDelete').addEventListener('click', () => {
            this.deleteDepartment();
        });

        // Search Input with Debounce
        document.getElementById('searchInput').addEventListener('input', (e) => {
            clearTimeout(this.searchTimer);
            this.searchTimer = setTimeout(() => {
                this.currentPage = 1;
                this.loadDepartments();
            }, 500);
        });

        // Filter Dropdowns
        document.getElementById('statusFilter').addEventListener('change', () => {
            this.currentPage = 1;
            this.loadDepartments();
        });

        document.getElementById('sortBy').addEventListener('change', () => {
            this.currentPage = 1;
            this.loadDepartments();
        });

        // Clear Filters Button
        document.getElementById('clearFilters').addEventListener('click', () => {
            this.clearFilters();
        });

        // Pagination
        document.getElementById('prevPage').addEventListener('click', () => {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.loadDepartments();
            }
        });

        document.getElementById('nextPage').addEventListener('click', () => {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
                this.loadDepartments();
            }
        });

        // Modal Background Click
        document.querySelector('#departmentModal .modal-background').addEventListener('click', () => {
            this.closeModal();
        });

        document.querySelector('#deleteModal .modal-background').addEventListener('click', () => {
            this.closeDeleteModal();
        });

        // Form Submit Prevention
        document.getElementById('departmentForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveDepartment();
        });
    }

    async loadDepartments() {
        this.showLoading();
        
        try {
            const searchTerm = document.getElementById('searchInput').value;
            const statusFilter = document.getElementById('statusFilter').value;
            const sortBy = document.getElementById('sortBy').value;
            
            const params = new URLSearchParams({
                page: this.currentPage,
                limit: this.itemsPerPage,
                search: searchTerm,
                status: statusFilter,
                sort: sortBy
            });

            const response = await fetch(`controllers/DepartmentController.php?action=list&${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderDepartments(data.departments);
                this.renderPagination(data.pagination);
                this.hideLoading();
                
                if (data.departments.length === 0) {
                    this.showEmptyState();
                } else {
                    this.hideEmptyState();
                }
            } else {
                this.showError('Failed to load departments: ' + data.message);
            }
        } catch (error) {
            console.error('Error loading departments:', error);
            this.showError('Error loading departments. Please try again.');
        }
    }

    renderDepartments(departments) {
        const grid = document.getElementById('departmentsGrid');
        grid.innerHTML = '';

        departments.forEach(department => {
            const departmentCard = this.createDepartmentCard(department);
            grid.appendChild(departmentCard);
        });
    }

    createDepartmentCard(department) {
        const column = document.createElement('div');
        column.className = 'column is-4';

        const statusClass = department.status === 'active' ? 'is-success' : 'is-warning';
        const statusIcon = department.status === 'active' ? 'fa-check-circle' : 'fa-pause-circle';

        column.innerHTML = `
            <div class="card">
                <div class="card-content">
                    <div class="media">
                        <div class="media-left">
                            <span class="icon is-large has-text-primary">
                                <i class="fas fa-building fa-2x"></i>
                            </span>
                        </div>
                        <div class="media-content">
                            <p class="title is-6">${this.escapeHtml(department.department_name)}</p>
                            <p class="subtitle is-7">
                                <span class="icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </span>
                                ${this.escapeHtml(department.location)}
                            </p>
                            <div class="tags">
                                <span class="tag ${statusClass}">
                                    <span class="icon is-small">
                                        <i class="fas ${statusIcon}"></i>
                                    </span>
                                    <span>${department.status.charAt(0).toUpperCase() + department.status.slice(1)}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <footer class="card-footer">
                    <a class="card-footer-item has-text-info" onclick="departmentManager.editDepartment(${department.department_id})">
                        <span class="icon">
                            <i class="fas fa-edit"></i>
                        </span>
                        <span>Edit</span>
                    </a>
                    <a class="card-footer-item has-text-danger" onclick="departmentManager.confirmDelete(${department.department_id}, '${this.escapeHtml(department.department_name)}')">
                        <span class="icon">
                            <i class="fas fa-trash"></i>
                        </span>
                        <span>Delete</span>
                    </a>
                </footer>
            </div>
        `;

        return column;
    }

    renderPagination(pagination) {
        this.totalPages = pagination.total_pages;
        const paginationContainer = document.getElementById('pagination');
        const paginationList = document.getElementById('paginationList');
        
        if (this.totalPages <= 1) {
            paginationContainer.style.display = 'none';
            return;
        }

        paginationContainer.style.display = 'flex';
        paginationList.innerHTML = '';

        // Update Previous/Next buttons
        document.getElementById('prevPage').classList.toggle('is-disabled', this.currentPage === 1);
        document.getElementById('nextPage').classList.toggle('is-disabled', this.currentPage === this.totalPages);

        // Generate page numbers
        const startPage = Math.max(1, this.currentPage - 2);
        const endPage = Math.min(this.totalPages, this.currentPage + 2);

        for (let i = startPage; i <= endPage; i++) {
            const li = document.createElement('li');
            li.innerHTML = `
                <a class="pagination-link ${i === this.currentPage ? 'is-current' : ''}" 
                   onclick="departmentManager.goToPage(${i})">${i}</a>
            `;
            paginationList.appendChild(li);
        }
    }

    goToPage(page) {
        this.currentPage = page;
        this.loadDepartments();
    }

    openModal(department = null) {
        const modal = document.getElementById('departmentModal');
        const modalTitle = document.getElementById('modalTitle');
        const form = document.getElementById('departmentForm');
        
        // Reset form
        form.reset();
        
        if (department) {
            // Edit mode
            modalTitle.textContent = 'Edit Department';
            document.getElementById('departmentId').value = department.department_id;
            document.getElementById('departmentName').value = department.department_name;
            document.getElementById('location').value = department.location;
            document.getElementById('status').value = department.status;
            this.currentDepartmentId = department.department_id;
        } else {
            // Add mode
            modalTitle.textContent = 'Add New Department';
            document.getElementById('departmentId').value = '';
            this.currentDepartmentId = null;
        }
        
        modal.classList.add('is-active');
    }

    closeModal() {
        document.getElementById('departmentModal').classList.remove('is-active');
        this.currentDepartmentId = null;
    }

    async editDepartment(departmentId) {
        try {
            const response = await fetch(`controllers/DepartmentController.php?action=get&id=${departmentId}`);
            const data = await response.json();
            
            if (data.success) {
                this.openModal(data.department);
            } else {
                this.showError('Failed to load department data: ' + data.message);
            }
        } catch (error) {
            console.error('Error loading department:', error);
            this.showError('Error loading department data.');
        }
    }

    async saveDepartment() {
        const form = document.getElementById('departmentForm');
        const saveButton = document.getElementById('saveDepartment');
        
        // Validate form
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Disable button during save
        saveButton.classList.add('is-loading');
        saveButton.disabled = true;

        try {
            const formData = new FormData();
            formData.append('action', this.currentDepartmentId ? 'update' : 'create');
            
            if (this.currentDepartmentId) {
                formData.append('department_id', this.currentDepartmentId);
            }
            
            formData.append('department_name', document.getElementById('departmentName').value.trim());
            formData.append('location', document.getElementById('location').value.trim());
            formData.append('status', document.getElementById('status').value);

            const response = await fetch('controllers/DepartmentController.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.closeModal();
                this.loadDepartments();
                this.showSuccess(this.currentDepartmentId ? 'Department updated successfully!' : 'Department created successfully!');
            } else {
                this.showError('Failed to save department: ' + data.message);
            }
        } catch (error) {
            console.error('Error saving department:', error);
            this.showError('Error saving department. Please try again.');
        } finally {
            saveButton.classList.remove('is-loading');
            saveButton.disabled = false;
        }
    }

    confirmDelete(departmentId, departmentName) {
        this.currentDepartmentId = departmentId;
        document.getElementById('deleteDepartmentName').textContent = departmentName;
        document.getElementById('deleteModal').classList.add('is-active');
    }

    closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('is-active');
        this.currentDepartmentId = null;
    }

    async deleteDepartment() {
        const deleteButton = document.getElementById('confirmDelete');
        
        deleteButton.classList.add('is-loading');
        deleteButton.disabled = true;

        try {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('department_id', this.currentDepartmentId);

            const response = await fetch('controllers/DepartmentController.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.closeDeleteModal();
                this.loadDepartments();
                this.showSuccess('Department deleted successfully!');
            } else {
                this.showError('Failed to delete department: ' + data.message);
            }
        } catch (error) {
            console.error('Error deleting department:', error);
            this.showError('Error deleting department. Please try again.');
        } finally {
            deleteButton.classList.remove('is-loading');
            deleteButton.disabled = false;
        }
    }

    clearFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('sortBy').value = 'department_name';
        this.currentPage = 1;
        this.loadDepartments();
    }

    showLoading() {
        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('departmentsContainer').style.display = 'none';
        document.getElementById('pagination').style.display = 'none';
    }

    hideLoading() {
        document.getElementById('loadingSpinner').style.display = 'none';
        document.getElementById('departmentsContainer').style.display = 'block';
    }

    showEmptyState() {
        document.getElementById('emptyState').style.display = 'block';
        document.getElementById('departmentsContainer').style.display = 'none';
    }

    hideEmptyState() {
        document.getElementById('emptyState').style.display = 'none';
        document.getElementById('departmentsContainer').style.display = 'block';
    }

    showSuccess(message) {
        this.showNotification(message, 'is-success');
    }

    showError(message) {
        this.showNotification(message, 'is-danger');
    }

    showNotification(message, type) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification ${type} is-light`;
        notification.innerHTML = `
            <button class="delete"></button>
            ${message}
        `;

        // Add to page
        const container = document.querySelector('.container');
        container.insertBefore(notification, container.firstChild);

        // Add delete functionality
        notification.querySelector('.delete').addEventListener('click', () => {
            notification.remove();
        });

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize the department manager when the page loads
let departmentManager;
document.addEventListener('DOMContentLoaded', () => {
    departmentManager = new DepartmentManager();
}); 