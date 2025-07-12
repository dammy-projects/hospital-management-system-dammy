// Insurance Providers Management JavaScript
class InsuranceProviderManager {
    constructor() {
        this.currentPage = 1;
        this.itemsPerPage = 6;
        this.totalPages = 1;
        this.currentProviderId = null;
        this.searchTimer = null;
        
        this.initializeEventListeners();
        this.loadProviders();
    }

    initializeEventListeners() {
        // Add Provider Button
        document.getElementById('addProviderBtn').addEventListener('click', () => {
            this.openModal();
        });

        // Modal Close Buttons
        document.getElementById('closeModal').addEventListener('click', () => {
            this.closeModal();
        });

        document.getElementById('cancelModal').addEventListener('click', () => {
            this.closeModal();
        });

        // Save Provider Button
        document.getElementById('saveProvider').addEventListener('click', () => {
            this.saveProvider();
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
            this.deleteProvider();
        });

        // Search Input with Debounce
        document.getElementById('searchInput').addEventListener('input', (e) => {
            clearTimeout(this.searchTimer);
            this.searchTimer = setTimeout(() => {
                this.currentPage = 1;
                this.loadProviders();
            }, 500);
        });

        // Filter Dropdowns
        document.getElementById('statusFilter').addEventListener('change', () => {
            this.currentPage = 1;
            this.loadProviders();
        });

        document.getElementById('sortBy').addEventListener('change', () => {
            this.currentPage = 1;
            this.loadProviders();
        });

        // Clear Filters Button
        document.getElementById('clearFilters').addEventListener('click', () => {
            this.clearFilters();
        });

        // Pagination
        document.getElementById('prevPage').addEventListener('click', () => {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.loadProviders();
            }
        });

        document.getElementById('nextPage').addEventListener('click', () => {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
                this.loadProviders();
            }
        });

        // Modal Background Click
        document.querySelector('#providerModal .modal-background').addEventListener('click', () => {
            this.closeModal();
        });

        document.querySelector('#deleteModal .modal-background').addEventListener('click', () => {
            this.closeDeleteModal();
        });

        // Form Submit Prevention
        document.getElementById('providerForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveProvider();
        });
    }

    async loadProviders() {
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

            const response = await fetch(`controllers/InsuranceProviderController.php?action=list&${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderProviders(data.providers);
                this.renderPagination(data.pagination);
                this.hideLoading();
                
                if (data.providers.length === 0) {
                    this.showEmptyState();
                } else {
                    this.hideEmptyState();
                }
            } else {
                this.showError('Failed to load insurance providers: ' + data.message);
            }
        } catch (error) {
            console.error('Error loading providers:', error);
            this.showError('Error loading insurance providers. Please try again.');
        }
    }

    renderProviders(providers) {
        const grid = document.getElementById('providersGrid');
        grid.innerHTML = '';

        providers.forEach(provider => {
            const providerCard = this.createProviderCard(provider);
            grid.appendChild(providerCard);
        });
    }

    createProviderCard(provider) {
        const column = document.createElement('div');
        column.className = 'column is-4';

        const statusClass = provider.status === 'active' ? 'is-success' : 'is-warning';
        const statusIcon = provider.status === 'active' ? 'fa-check-circle' : 'fa-pause-circle';

        // Truncate address for display
        const addressDisplay = provider.address ? 
            (provider.address.length > 60 ? provider.address.substring(0, 60) + '...' : provider.address) : 
            'No address provided';

        column.innerHTML = `
            <div class="card">
                <div class="card-content">
                    <div class="media">
                        <div class="media-left">
                            <span class="icon is-large has-text-primary">
                                <i class="fas fa-shield-alt fa-2x"></i>
                            </span>
                        </div>
                        <div class="media-content">
                            <p class="title is-6">${this.escapeHtml(provider.provider_name)}</p>
                            <p class="subtitle is-7">
                                <span class="icon">
                                    <i class="fas fa-phone"></i>
                                </span>
                                ${this.escapeHtml(provider.contact_number || 'No contact')}
                            </p>
                            <div class="provider-address mb-2">
                                <span class="icon is-small">
                                    <i class="fas fa-map-marker-alt"></i>
                                </span>
                                ${this.escapeHtml(addressDisplay)}
                            </div>
                            <div class="tags">
                                <span class="tag ${statusClass}">
                                    <span class="icon is-small">
                                        <i class="fas ${statusIcon}"></i>
                                    </span>
                                    <span>${provider.status.charAt(0).toUpperCase() + provider.status.slice(1)}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <footer class="card-footer">
                    <a class="card-footer-item has-text-info" onclick="providerManager.editProvider(${provider.insurance_provider_id})">
                        <span class="icon">
                            <i class="fas fa-edit"></i>
                        </span>
                        <span>Edit</span>
                    </a>
                    <a class="card-footer-item has-text-danger" onclick="providerManager.confirmDelete(${provider.insurance_provider_id}, '${this.escapeHtml(provider.provider_name)}')">
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
                   onclick="providerManager.goToPage(${i})">${i}</a>
            `;
            paginationList.appendChild(li);
        }
    }

    goToPage(page) {
        this.currentPage = page;
        this.loadProviders();
    }

    openModal(provider = null) {
        const modal = document.getElementById('providerModal');
        const modalTitle = document.getElementById('modalTitle');
        const form = document.getElementById('providerForm');
        
        // Reset form
        form.reset();
        
        if (provider) {
            // Edit mode
            modalTitle.textContent = 'Edit Insurance Provider';
            document.getElementById('providerId').value = provider.insurance_provider_id;
            document.getElementById('providerName').value = provider.provider_name;
            document.getElementById('contactNumber').value = provider.contact_number || '';
            document.getElementById('address').value = provider.address || '';
            document.getElementById('status').value = provider.status;
            this.currentProviderId = provider.insurance_provider_id;
        } else {
            // Add mode
            modalTitle.textContent = 'Add New Insurance Provider';
            document.getElementById('providerId').value = '';
            this.currentProviderId = null;
        }
        
        modal.classList.add('is-active');
    }

    closeModal() {
        document.getElementById('providerModal').classList.remove('is-active');
        this.currentProviderId = null;
    }

    async editProvider(providerId) {
        try {
            const response = await fetch(`controllers/InsuranceProviderController.php?action=get&id=${providerId}`);
            const data = await response.json();
            
            if (data.success) {
                this.openModal(data.provider);
            } else {
                this.showError('Failed to load provider data: ' + data.message);
            }
        } catch (error) {
            console.error('Error loading provider:', error);
            this.showError('Error loading provider data.');
        }
    }

    async saveProvider() {
        const form = document.getElementById('providerForm');
        const saveButton = document.getElementById('saveProvider');
        
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
            formData.append('action', this.currentProviderId ? 'update' : 'create');
            
            if (this.currentProviderId) {
                formData.append('insurance_provider_id', this.currentProviderId);
            }
            
            formData.append('provider_name', document.getElementById('providerName').value.trim());
            formData.append('contact_number', document.getElementById('contactNumber').value.trim());
            formData.append('address', document.getElementById('address').value.trim());
            formData.append('status', document.getElementById('status').value);

            const response = await fetch('controllers/InsuranceProviderController.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.closeModal();
                this.loadProviders();
                this.showSuccess(this.currentProviderId ? 'Provider updated successfully!' : 'Provider created successfully!');
            } else {
                this.showError('Failed to save provider: ' + data.message);
            }
        } catch (error) {
            console.error('Error saving provider:', error);
            this.showError('Error saving provider. Please try again.');
        } finally {
            saveButton.classList.remove('is-loading');
            saveButton.disabled = false;
        }
    }

    confirmDelete(providerId, providerName) {
        this.currentProviderId = providerId;
        document.getElementById('deleteProviderName').textContent = providerName;
        document.getElementById('deleteModal').classList.add('is-active');
    }

    closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('is-active');
        this.currentProviderId = null;
    }

    async deleteProvider() {
        const deleteButton = document.getElementById('confirmDelete');
        
        deleteButton.classList.add('is-loading');
        deleteButton.disabled = true;

        try {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('insurance_provider_id', this.currentProviderId);

            const response = await fetch('controllers/InsuranceProviderController.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.closeDeleteModal();
                this.loadProviders();
                this.showSuccess('Provider deleted successfully!');
            } else {
                this.showError('Failed to delete provider: ' + data.message);
            }
        } catch (error) {
            console.error('Error deleting provider:', error);
            this.showError('Error deleting provider. Please try again.');
        } finally {
            deleteButton.classList.remove('is-loading');
            deleteButton.disabled = false;
        }
    }

    clearFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('sortBy').value = 'provider_name';
        this.currentPage = 1;
        this.loadProviders();
    }

    showLoading() {
        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('providersContainer').style.display = 'none';
        document.getElementById('pagination').style.display = 'none';
    }

    hideLoading() {
        document.getElementById('loadingSpinner').style.display = 'none';
        document.getElementById('providersContainer').style.display = 'block';
    }

    showEmptyState() {
        document.getElementById('emptyState').style.display = 'block';
        document.getElementById('providersContainer').style.display = 'none';
    }

    hideEmptyState() {
        document.getElementById('emptyState').style.display = 'none';
        document.getElementById('providersContainer').style.display = 'block';
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

// Initialize the provider manager when the page loads
let providerManager;
document.addEventListener('DOMContentLoaded', () => {
    providerManager = new InsuranceProviderManager();
}); 