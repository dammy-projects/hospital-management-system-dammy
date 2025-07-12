class MedicineManager {
    constructor() {
        this.currentPage = 1;
        this.totalPages = 1;
        this.limit = 6;
        this.isEditing = false;
        this.currentMedicineId = null;
        this.dosageForms = [];
        
        this.init();
    }
    
    init() {
        this.loadDosageForms();
        this.loadMedicines();
        this.bindEvents();
    }
    
    bindEvents() {
        // Modal events
        document.getElementById('addMedicineBtn').addEventListener('click', () => this.openModal());
        document.getElementById('closeModal').addEventListener('click', () => this.closeModal());
        document.getElementById('cancelModal').addEventListener('click', () => this.closeModal());
        document.getElementById('saveMedicine').addEventListener('click', () => this.saveMedicine());
        
        // Delete modal events
        document.getElementById('closeDeleteModal').addEventListener('click', () => this.closeDeleteModal());
        document.getElementById('cancelDelete').addEventListener('click', () => this.closeDeleteModal());
        document.getElementById('confirmDelete').addEventListener('click', () => this.deleteMedicine());
        
        // Search and filter events
        document.getElementById('searchInput').addEventListener('input', this.debounce(() => this.searchMedicines(), 300));
        document.getElementById('dosageFormFilter').addEventListener('change', () => this.searchMedicines());
        document.getElementById('clearFilters').addEventListener('click', () => this.clearFilters());
        
        // Pagination events
        document.getElementById('prevPage').addEventListener('click', () => this.goToPage(this.currentPage - 1));
        document.getElementById('nextPage').addEventListener('click', () => this.goToPage(this.currentPage + 1));
        
        // Close modals when clicking background
        document.getElementById('medicineModal').querySelector('.modal-background').addEventListener('click', () => this.closeModal());
        document.getElementById('deleteModal').querySelector('.modal-background').addEventListener('click', () => this.closeDeleteModal());
        
        // Notification close
        document.getElementById('closeNotification').addEventListener('click', () => this.hideNotification());
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
    
    async loadDosageForms() {
        try {
            const response = await fetch('controllers/MedicineController.php?action=dosage-forms');
            const result = await response.json();
            
            if (result.success) {
                this.dosageForms = result.data;
                this.populateDosageFormFilter();
            }
        } catch (error) {
            console.error('Error loading dosage forms:', error);
        }
    }
    
    populateDosageFormFilter() {
        const filterSelect = document.getElementById('dosageFormFilter');
        
        // Clear existing options except the first one
        filterSelect.innerHTML = '<option value="">All Dosage Forms</option>';
        
        this.dosageForms.forEach(form => {
            const option = document.createElement('option');
            option.value = form.dosage_form;
            option.textContent = form.dosage_form;
            filterSelect.appendChild(option);
        });
    }
    
    async loadMedicines() {
        this.showLoading();
        
        try {
            const params = new URLSearchParams({
                action: 'list',
                page: this.currentPage,
                limit: this.limit,
                search: document.getElementById('searchInput').value,
                dosage_form_filter: document.getElementById('dosageFormFilter').value
            });
            
            const response = await fetch(`controllers/MedicineController.php?${params}`);
            const result = await response.json();
            
            if (result.success) {
                this.displayMedicines(result.data);
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
            console.error('Error loading medicines:', error);
            this.hideLoading();
            this.showNotification('Error loading medicines: ' + error.message, 'danger');
        }
    }
    
    displayMedicines(medicines) {
        const grid = document.getElementById('medicinesGrid');
        grid.innerHTML = '';
        
        medicines.forEach(medicine => {
            const medicineCard = this.createMedicineCard(medicine);
            grid.appendChild(medicineCard);
        });
    }
    
    createMedicineCard(medicine) {
        const column = document.createElement('div');
        column.className = 'column is-4';
        
        const dosageFormBadgeClass = this.getDosageFormBadgeClass(medicine.dosage_form);
        
        column.innerHTML = `
            <div class="card">
                <div class="card-content">
                    <div class="content">
                        <h6 class="title is-6">${this.escapeHtml(medicine.medicine_name)}</h6>
                        <p class="subtitle is-7">
                            <span class="tag ${dosageFormBadgeClass} dosage-form-badge">
                                ${this.escapeHtml(medicine.dosage_form || 'N/A')}
                            </span>
                        </p>
                        <p class="strength-display">
                            <strong>Strength:</strong> ${this.escapeHtml(medicine.strength || 'N/A')}
                        </p>
                    </div>
                </div>
                <footer class="card-footer">
                    <a class="card-footer-item has-text-info" onclick="medicineManager.editMedicine(${medicine.medicine_id})">
                        <span class="icon is-small">
                            <i class="fas fa-edit"></i>
                        </span>
                        <span>Edit</span>
                    </a>
                    <a class="card-footer-item has-text-danger" onclick="medicineManager.confirmDelete(${medicine.medicine_id}, '${this.escapeHtml(medicine.medicine_name)}')">
                        <span class="icon is-small">
                            <i class="fas fa-trash"></i>
                        </span>
                        <span>Delete</span>
                    </a>
                </footer>
            </div>
        `;
        
        return column;
    }
    
    getDosageFormBadgeClass(dosageForm) {
        const formClassMap = {
            'Tablet': 'is-primary',
            'Capsule': 'is-info',
            'Injection': 'is-danger',
            'Syrup': 'is-warning',
            'Inhaler': 'is-success',
            'Cream': 'is-light',
            'Ointment': 'is-light',
            'Drops': 'is-link',
            'Powder': 'is-dark',
            'Suspension': 'is-warning'
        };
        
        return formClassMap[dosageForm] || 'is-light';
    }
    
    updatePagination(pagination) {
        this.currentPage = pagination.current_page;
        this.totalPages = pagination.total_pages;
        
        const paginationElement = document.getElementById('pagination');
        const prevButton = document.getElementById('prevPage');
        const nextButton = document.getElementById('nextPage');
        const paginationList = document.getElementById('paginationList');
        
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
        
        const maxVisiblePages = 5;
        let startPage = Math.max(1, this.currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(this.totalPages, startPage + maxVisiblePages - 1);
        
        if (endPage - startPage + 1 < maxVisiblePages) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }
        
        for (let i = startPage; i <= endPage; i++) {
            const li = document.createElement('li');
            const a = document.createElement('a');
            a.className = 'pagination-link';
            a.textContent = i;
            
            if (i === this.currentPage) {
                a.classList.add('is-current');
            }
            
            a.addEventListener('click', () => this.goToPage(i));
            li.appendChild(a);
            paginationList.appendChild(li);
        }
    }
    
    goToPage(page) {
        if (page >= 1 && page <= this.totalPages && page !== this.currentPage) {
            this.currentPage = page;
            this.loadMedicines();
        }
    }
    
    searchMedicines() {
        this.currentPage = 1;
        this.loadMedicines();
    }
    
    clearFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('dosageFormFilter').value = '';
        this.searchMedicines();
    }
    
    openModal(medicine = null) {
        const modal = document.getElementById('medicineModal');
        const modalTitle = document.getElementById('modalTitle');
        const form = document.getElementById('medicineForm');
        
        form.reset();
        
        if (medicine) {
            this.isEditing = true;
            this.currentMedicineId = medicine.medicine_id;
            modalTitle.textContent = 'Edit Medicine';
            
            document.getElementById('medicineId').value = medicine.medicine_id;
            document.getElementById('medicineName').value = medicine.medicine_name;
            document.getElementById('dosageForm').value = medicine.dosage_form;
            document.getElementById('strength').value = medicine.strength;
        } else {
            this.isEditing = false;
            this.currentMedicineId = null;
            modalTitle.textContent = 'Add New Medicine';
        }
        
        modal.classList.add('is-active');
    }
    
    closeModal() {
        const modal = document.getElementById('medicineModal');
        modal.classList.remove('is-active');
        this.isEditing = false;
        this.currentMedicineId = null;
    }
    
    async saveMedicine() {
        const form = document.getElementById('medicineForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        const medicineData = {
            medicine_name: document.getElementById('medicineName').value,
            dosage_form: document.getElementById('dosageForm').value,
            strength: document.getElementById('strength').value
        };
        
        try {
            let url = 'controllers/MedicineController.php?action=';
            let method = 'POST';
            
            if (this.isEditing) {
                url += `update&id=${this.currentMedicineId}`;
                method = 'PUT';
                medicineData.medicine_id = this.currentMedicineId;
            } else {
                url += 'create';
            }
            
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(medicineData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.closeModal();
                this.loadMedicines();
                this.loadDosageForms(); // Refresh dosage forms in case a new one was added
                this.showNotification(result.message, 'success');
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error saving medicine:', error);
            this.showNotification('Error saving medicine: ' + error.message, 'danger');
        }
    }
    
    async editMedicine(medicineId) {
        try {
            const response = await fetch(`controllers/MedicineController.php?action=get&id=${medicineId}`);
            const result = await response.json();
            
            if (result.success) {
                this.openModal(result.data);
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error loading medicine:', error);
            this.showNotification('Error loading medicine: ' + error.message, 'danger');
        }
    }
    
    confirmDelete(medicineId, medicineName) {
        this.currentMedicineId = medicineId;
        document.getElementById('deleteMedicineName').textContent = medicineName;
        document.getElementById('deleteModal').classList.add('is-active');
    }
    
    closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('is-active');
        this.currentMedicineId = null;
    }
    
    async deleteMedicine() {
        if (!this.currentMedicineId) return;
        
        try {
            const response = await fetch(`controllers/MedicineController.php?action=delete&id=${this.currentMedicineId}`, {
                method: 'DELETE'
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.closeDeleteModal();
                this.loadMedicines();
                this.loadDosageForms(); // Refresh dosage forms
                this.showNotification(result.message, 'success');
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error deleting medicine:', error);
            this.showNotification('Error deleting medicine: ' + error.message, 'danger');
        }
    }
    
    showLoading() {
        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('medicinesContainer').style.opacity = '0.5';
    }
    
    hideLoading() {
        document.getElementById('loadingSpinner').style.display = 'none';
        document.getElementById('medicinesContainer').style.opacity = '1';
    }
    
    showEmptyState() {
        document.getElementById('emptyState').style.display = 'block';
        document.getElementById('pagination').style.display = 'none';
    }
    
    hideEmptyState() {
        document.getElementById('emptyState').style.display = 'none';
    }
    
    showNotification(message, type = 'info') {
        const notification = document.getElementById('notification');
        const messageElement = document.getElementById('notificationMessage');
        
        // Remove existing type classes
        notification.classList.remove('is-success', 'is-warning', 'is-danger', 'is-info');
        
        // Add new type class
        notification.classList.add(`is-${type}`);
        
        messageElement.textContent = message;
        notification.style.display = 'block';
        
        // Auto hide after 5 seconds
        setTimeout(() => {
            this.hideNotification();
        }, 5000);
    }
    
    hideNotification() {
        document.getElementById('notification').style.display = 'none';
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

// Initialize the medicine manager when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.medicineManager = new MedicineManager();
}); 