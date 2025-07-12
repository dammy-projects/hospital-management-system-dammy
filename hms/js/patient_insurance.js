class PatientInsuranceManager {
    constructor() {
        this.currentPage = 1;
        this.totalPages = 1;
        this.limit = 6;
        this.isEditing = false;
        this.currentInsuranceId = null;
        this.patients = [];
        this.providers = [];
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.loadInsurances();
        this.loadPatients();
        this.loadProviders();
        this.loadProvidersForFilter();
    }
    
    bindEvents() {
        // Modal events
        document.getElementById('addInsuranceBtn').addEventListener('click', () => this.openModal());
        document.getElementById('closeModal').addEventListener('click', () => this.closeModal());
        document.getElementById('cancelModal').addEventListener('click', () => this.closeModal());
        document.getElementById('saveInsurance').addEventListener('click', () => this.saveInsurance());
        
        // View modal events
        document.getElementById('closeViewModal').addEventListener('click', () => this.closeViewModal());
        document.getElementById('cancelView').addEventListener('click', () => this.closeViewModal());
        
        // Delete modal events
        document.getElementById('closeDeleteModal').addEventListener('click', () => this.closeDeleteModal());
        document.getElementById('cancelDelete').addEventListener('click', () => this.closeDeleteModal());
        document.getElementById('confirmDelete').addEventListener('click', () => this.deleteInsurance());
        
        // Search and filter events
        document.getElementById('searchInput').addEventListener('input', this.debounce(() => this.searchInsurances(), 300));
        document.getElementById('providerFilter').addEventListener('change', () => this.searchInsurances());
        document.getElementById('statusFilter').addEventListener('change', () => this.searchInsurances());
        document.getElementById('clearFilters').addEventListener('click', () => this.clearFilters());
        
        // Pagination events
        document.getElementById('prevPage').addEventListener('click', () => this.goToPage(this.currentPage - 1));
        document.getElementById('nextPage').addEventListener('click', () => this.goToPage(this.currentPage + 1));
        
        // Close modals when clicking background
        document.getElementById('insuranceModal').querySelector('.modal-background').addEventListener('click', () => this.closeModal());
        document.getElementById('viewModal').querySelector('.modal-background').addEventListener('click', () => this.closeViewModal());
        document.getElementById('deleteModal').querySelector('.modal-background').addEventListener('click', () => this.closeDeleteModal());
        
        // Form submit prevention
        document.getElementById('insuranceForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveInsurance();
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
    
    async loadInsurances() {
        this.showLoading();
        
        try {
            const params = new URLSearchParams({
                action: 'list',
                page: this.currentPage,
                limit: this.limit,
                search: document.getElementById('searchInput').value,
                provider_filter: document.getElementById('providerFilter').value,
                status_filter: document.getElementById('statusFilter').value
            });
            
            const response = await fetch(`controllers/PatientInsuranceController.php?${params}`);
            const result = await response.json();
            
            if (result.success) {
                this.displayInsurances(result.data);
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
            console.error('Error loading insurance records:', error);
            this.hideLoading();
            this.showNotification('Error loading insurance records: ' + error.message, 'danger');
        }
    }
    
    displayInsurances(insurances) {
        const grid = document.getElementById('insuranceGrid');
        grid.innerHTML = '';
        
        insurances.forEach(insurance => {
            const card = this.createInsuranceCard(insurance);
            grid.appendChild(card);
        });
    }
    
    createInsuranceCard(insurance) {
        const card = document.createElement('div');
        card.className = 'column is-4';
        
        const statusColor = {
            'active': 'success',
            'inactive': 'warning'
        }[insurance.status] || 'info';
        
        card.innerHTML = `
            <div class="card" style="display: flex; flex-direction: column; height: 220px;">
                <div class="card-content" style="padding: 0.75rem; flex: 1; display: flex; flex-direction: column;">
                    <div class="has-text-centered mb-2">
                        <div class="mb-1">
                            <span class="icon is-small has-text-primary">
                                <i class="fas fa-shield-alt fa-lg"></i>
                            </span>
                        </div>
                        <h6 class="title is-6 mb-1" style="line-height: 1.1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${this.escapeHtml(insurance.patient_name || 'N/A')}">${this.escapeHtml(insurance.patient_name || 'N/A')}</h6>
                        <p class="subtitle is-7 has-text-grey mb-2" style="line-height: 1; margin-top: 0.1rem;">${this.escapeHtml(insurance.provider_name || 'N/A')}</p>
                    </div>
                    
                    <div class="content" style="font-size: 0.75rem; flex: 1;">
                        <div class="field mb-2">
                            <span class="tag is-${statusColor} is-light is-small">${insurance.status}</span>
                        </div>
                        
                        <p class="mb-1 is-size-7" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${this.escapeHtml(insurance.insurance_number)}"><strong>Policy #:</strong> ${this.escapeHtml(insurance.insurance_number)}</p>
                        
                        ${insurance.patient_phone ? `<p class="mb-1 is-size-7" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${this.escapeHtml(insurance.patient_phone)}"><strong>Phone:</strong> ${this.escapeHtml(insurance.patient_phone)}</p>` : ''}
                    </div>
                </div>
                
                <footer class="card-footer" style="border-top: 1px solid #dbdbdb; margin-top: auto;">
                    <a class="card-footer-item has-text-primary is-size-7" onclick="patientInsuranceManager.viewInsurance(${insurance.patient_insurance_id})" style="padding: 0.4rem;">
                        <span class="icon is-small">
                            <i class="fas fa-eye"></i>
                        </span>
                        <span>View</span>
                    </a>
                    <a class="card-footer-item has-text-info is-size-7" onclick="patientInsuranceManager.editInsurance(${insurance.patient_insurance_id})" style="padding: 0.4rem;">
                        <span class="icon is-small">
                            <i class="fas fa-edit"></i>
                        </span>
                        <span>Edit</span>
                    </a>
                    <a class="card-footer-item has-text-danger is-size-7" onclick="patientInsuranceManager.confirmDeleteInsurance(${insurance.patient_insurance_id}, '${this.escapeHtml(insurance.patient_name)} - ${this.escapeHtml(insurance.provider_name)}')" style="padding: 0.4rem;">
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
            this.loadInsurances();
        }
    }
    
    openModal(insurance = null) {
        this.isEditing = !!insurance;
        this.currentInsuranceId = insurance ? insurance.patient_insurance_id : null;
        
        const modal = document.getElementById('insuranceModal');
        const title = document.getElementById('modalTitle');
        
        title.textContent = this.isEditing ? 'Edit Insurance Record' : 'Add New Insurance Record';
        
        if (insurance) {
            document.getElementById('insuranceId').value = insurance.patient_insurance_id;
            document.getElementById('patientId').value = insurance.patient_id || '';
            document.getElementById('insuranceProviderId').value = insurance.insurance_provider_id || '';
            document.getElementById('insuranceNumber').value = insurance.insurance_number || '';
            document.getElementById('status').value = insurance.status || '';
        } else {
            document.getElementById('insuranceForm').reset();
            document.getElementById('insuranceId').value = '';
        }
        
        modal.classList.add('is-active');
    }
    
    closeModal() {
        document.getElementById('insuranceModal').classList.remove('is-active');
        document.getElementById('insuranceForm').reset();
        this.isEditing = false;
        this.currentInsuranceId = null;
    }
    
    async saveInsurance() {
        const form = document.getElementById('insuranceForm');
        const formData = new FormData(form);
        
        // Validate required fields
        const patientId = document.getElementById('patientId').value;
        const insuranceProviderId = document.getElementById('insuranceProviderId').value;
        const insuranceNumber = document.getElementById('insuranceNumber').value;
        const status = document.getElementById('status').value;
        
        if (!patientId || !insuranceProviderId || !insuranceNumber || !status) {
            this.showNotification('Please fill in all required fields', 'warning');
            return;
        }
        
        const data = {
            patient_id: patientId,
            insurance_provider_id: insuranceProviderId,
            insurance_number: insuranceNumber,
            status: status
        };
        
        if (this.isEditing) {
            data.patient_insurance_id = this.currentInsuranceId;
        }
        
        try {
            const url = this.isEditing 
                ? 'controllers/PatientInsuranceController.php?action=update'
                : 'controllers/PatientInsuranceController.php?action=create';
            
            const method = this.isEditing ? 'PUT' : 'POST';
            
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showNotification(result.message, 'success');
                this.closeModal();
                this.loadInsurances();
            } else {
                this.showNotification(result.message, 'danger');
            }
        } catch (error) {
            console.error('Error saving insurance record:', error);
            this.showNotification('Error saving insurance record: ' + error.message, 'danger');
        }
    }
    
    async editInsurance(id) {
        try {
            const response = await fetch(`controllers/PatientInsuranceController.php?action=get&id=${id}`);
            const result = await response.json();
            
            if (result.success) {
                this.openModal(result.data);
            } else {
                this.showNotification(result.message, 'danger');
            }
        } catch (error) {
            console.error('Error loading insurance record:', error);
            this.showNotification('Error loading insurance record: ' + error.message, 'danger');
        }
    }
    
    confirmDeleteInsurance(id, name) {
        this.currentInsuranceId = id;
        document.getElementById('deleteInsuranceName').textContent = name;
        document.getElementById('deleteModal').classList.add('is-active');
    }
    
    closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('is-active');
        this.currentInsuranceId = null;
    }
    
    async deleteInsurance() {
        try {
            const response = await fetch('controllers/PatientInsuranceController.php?action=delete', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: this.currentInsuranceId })
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showNotification(result.message, 'success');
                this.closeDeleteModal();
                this.loadInsurances();
            } else {
                this.showNotification(result.message, 'danger');
            }
        } catch (error) {
            console.error('Error deleting insurance record:', error);
            this.showNotification('Error deleting insurance record: ' + error.message, 'danger');
        }
    }
    
    async viewInsurance(id) {
        try {
            const response = await fetch(`controllers/PatientInsuranceController.php?action=get&id=${id}`);
            const result = await response.json();
            
            if (result.success) {
                this.populateViewModal(result.data);
                this.currentInsuranceId = id;
                document.getElementById('viewModal').classList.add('is-active');
            } else {
                this.showNotification(result.message, 'danger');
            }
        } catch (error) {
            console.error('Error loading insurance record:', error);
            this.showNotification('Error loading insurance record: ' + error.message, 'danger');
        }
    }
    
    populateViewModal(insurance) {
        // Basic information
        document.getElementById('viewPatientName').value = insurance.patient_name || 'N/A';
        document.getElementById('viewProviderName').value = insurance.provider_name || 'N/A';
        document.getElementById('viewInsuranceNumber').value = insurance.insurance_number || 'N/A';
        document.getElementById('viewPatientPhone').value = insurance.patient_phone || 'N/A';
        
        // Status with color
        const statusElement = document.getElementById('viewStatus');
        statusElement.textContent = insurance.status || 'N/A';
        statusElement.className = 'tag is-medium';
        
        const statusColor = {
            'active': 'is-success',
            'inactive': 'is-warning'
        }[insurance.status] || 'is-info';
        
        statusElement.classList.add(statusColor);
        
        // Provider details (if available)
        document.getElementById('viewProviderContact').value = insurance.provider_contact || 'N/A';
        document.getElementById('viewProviderAddress').value = insurance.provider_address || 'N/A';
    }
    
    closeViewModal() {
        document.getElementById('viewModal').classList.remove('is-active');
        this.currentInsuranceId = null;
    }
    
    searchInsurances() {
        this.currentPage = 1;
        this.loadInsurances();
    }
    
    clearFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('providerFilter').value = '';
        document.getElementById('statusFilter').value = '';
        this.searchInsurances();
    }
    
    async loadPatients() {
        try {
            const response = await fetch('controllers/PatientInsuranceController.php?action=patients');
            const result = await response.json();
            
            if (result.success) {
                this.patients = result.data;
                this.populatePatientDropdown();
            }
        } catch (error) {
            console.error('Error loading patients:', error);
        }
    }
    
    populatePatientDropdown() {
        const select = document.getElementById('patientId');
        select.innerHTML = '<option value="">Select Patient</option>';
        
        this.patients.forEach(patient => {
            const option = document.createElement('option');
            option.value = patient.patient_id;
            option.textContent = patient.patient_name;
            select.appendChild(option);
        });
    }
    
    async loadProviders() {
        try {
            const response = await fetch('controllers/PatientInsuranceController.php?action=providers');
            const result = await response.json();
            
            if (result.success) {
                this.providers = result.data;
                this.populateProviderDropdown();
            }
        } catch (error) {
            console.error('Error loading providers:', error);
        }
    }
    
    populateProviderDropdown() {
        const select = document.getElementById('insuranceProviderId');
        select.innerHTML = '<option value="">Select Provider</option>';
        
        this.providers.forEach(provider => {
            const option = document.createElement('option');
            option.value = provider.insurance_provider_id;
            option.textContent = provider.provider_name;
            select.appendChild(option);
        });
    }
    
    async loadProvidersForFilter() {
        try {
            const response = await fetch('controllers/PatientInsuranceController.php?action=providers');
            const result = await response.json();
            
            if (result.success) {
                this.populateProviderFilterDropdown(result.data);
            }
        } catch (error) {
            console.error('Error loading providers for filter:', error);
        }
    }
    
    populateProviderFilterDropdown(providers) {
        const select = document.getElementById('providerFilter');
        select.innerHTML = '<option value="">All Providers</option>';
        
        providers.forEach(provider => {
            const option = document.createElement('option');
            option.value = provider.insurance_provider_id;
            option.textContent = provider.provider_name;
            select.appendChild(option);
        });
    }
    
    showLoading() {
        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('insuranceContainer').style.display = 'none';
    }
    
    hideLoading() {
        document.getElementById('loadingSpinner').style.display = 'none';
        document.getElementById('insuranceContainer').style.display = 'block';
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
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.zIndex = '9999';
        notification.style.maxWidth = '400px';
        
        notification.innerHTML = `
            <button class="delete"></button>
            ${message}
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 5000);
        
        // Remove on click
        notification.querySelector('.delete').addEventListener('click', () => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        });
    }
    
    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
}

// Initialize the manager when the page loads
document.addEventListener('DOMContentLoaded', function() {
    window.patientInsuranceManager = new PatientInsuranceManager();
}); 